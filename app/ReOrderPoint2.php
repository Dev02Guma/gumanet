<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReOrderPoint2 extends Model
{
    // Canales que forman la venta privada y la pública (claves de MES_CANAL / ANT_CANAL y prefijos de campos de canales)
    private static $canalesPrivados = [
        'FARMACIAS' => 'FARMACIA', 'CADENAS' => 'CADENA_FARMACIA', 'MAYORISTAS' => 'MAYORISTA',
        'INSTITUCIONES_PRIVADAS' => 'INSTITUCION_PRIVADA', 'CRUZ_AZUL' => 'CRUZ_AZUL',
    ];
    private static $canalesPublicos = ['INSTITUCION_PUBLICA', 'LICITACION'];

    private static $nombreMes = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    // Promedio depurado: con pocos meses de venta (venta intermitente) se usa el total de 12 meses / 12
    const MAX_MESES_INTERMITENTE = 5;
    const DIAS_MES               = 30.44;

    // ---- Parámetros de la política v2.1 (hoja RESUMEN de Contribucion_Reorden_2026-10-26.xlsx) ----
    // Clasificación sobre la contribución privada
    const CORTE_A           = 0.80;     // A: acumulan el 80% de la contribución privada
    const CORTE_B           = 0.90;     // B: capa 80-90% ...
    const MIN_CONTRIB_B     = 360000;   // ... o contribución privada > C$360,000
    const L_MIN_PUBLICO     = 0.50;     // L: > 50% de unidades públicas ...
    const L_MAX_CONTRIB     = 100000;   // ... y contribución privada < C$100,000
    // Reorden: meses de cobertura a los que se rellena y disparadores
    const RELLENO_A         = 18;
    const RELLENO_B         = 12;
    const MARGEN_CONSOLIDAR = 2;        // CONSOLIDAR: grupo a <= 2 meses de su disparador
    const SOBRE_STOCK       = 36;       // meses de cobertura consolidada
    const CAIDA_DEFENDER    = -0.20;    // A/B con venta privada cayendo > 20%
    const SUBIR_CRECIMIENTO = 0.20;     // C con repunte privado > 20% ...
    const SUBIR_CONTRIB     = 170000;   // ... y contribución privada > C$170,000 (inferido del análisis)
    const ORDEN_GRANDE_LOC  = 1000000;  // órdenes > C$1M o > 8 meses de demanda se dividen en 2-4 despachos
    const ORDEN_GRANDE_MES  = 8;
    // Notas / banderas
    const NOTA_MAX_CLIENTES = 5;
    const NOTA_CRUZ_AZUL    = 0.50;
    const NOTA_CAIDA_MARGEN = -0.08;
    const ORIGEN_DEFAULT    = 'Otro/verificar';

    private static $disparadorA = ['China' => 6, 'India' => 7, 'Otro/verificar' => 7];
    private static $disparadorB = 4;
    // URGENTE: cobertura consolidada por debajo del lead time = disparador - 1 mes (inferido del análisis)
    const HOLGURA_LEAD_TIME = 1;

    /**
     * Decisión por producto (política de reorden v2.1), alimentada con la Contribución por canales (/canales).
     * Se consume ContribucionPorCanales::getData() tal cual (sin modificar canales); sobre esos datos se
     * calculan las columnas del análisis y se aplica la política con los maestros de PRODUCCION
     * (database/reorder_point2_sqlserver.sql): grupos de hermanas, origen por fabricante e historial de categorías.
     */
    public static function getData()
    {
        $periodo = DB::connection('sqlsrv')->select("SELECT MIN(fecha) AS INI, MAX(fecha) AS FIN FROM PRODUCCION.dbo.tbl_contribucion_canales");
        if (empty($periodo) || !$periodo[0]->FIN) {
            return ['META' => null, 'HEADERS' => [], 'DATA' => []];
        }

        $corte  = substr($periodo[0]->FIN, 0, 10);
        $ini    = substr($periodo[0]->INI, 0, 10);
        $nMeses = (int) date('n', strtotime($corte));
        $diaCorte = (int) date('j', strtotime($corte));
        $diasMes  = (int) date('t', strtotime($corte));
        // El mes del corte es parcial: en promedio y momentum se lleva a mes completo
        $factor   = $diaCorte < $diasMes ? $diasMes / $diaCorte : 1;

        $meses = array_slice(self::$nombreMes, 0, $nMeses);
        if ($factor != 1) {
            $meses[$nMeses - 1] .= ' (' . $diaCorte . ' d.)';
        }

        $tc = DB::connection('sqlsrv')->select("SELECT TOP 1 MONTO FROM Softland.umk.TIPO_CAMBIO_HIST
            WHERE TIPO_CAMBIO = 'OFIC' AND FECHA <= ? ORDER BY FECHA DESC", [$corte . ' 23:59:59']);

        // La vista de canales repite algunos artículos con una fila "CAMBIO DE COSTO DE xxx-M" (ajuste de costo
        // sin unidades): se usa la fila del artículo y el ajuste solo cuando es la única que hay, como el Excel original
        $filas = [];
        foreach (ContribucionPorCanales::getData() as $row) {
            $art    = (string) $row['ARTICULODESC'];
            $ajuste = strpos($row['DESCRIPCION'], 'CAMBIO DE COSTO DE ') === 0;
            if (!isset($filas[$art]) || !$ajuste) {
                $filas[$art] = $row;
            }
        }

        $data = [];
        foreach ($filas as $row) {
            $data[] = self::fila($row, $corte, $nMeses, $factor);
        }

        $tipoCambio = $tc ? (float) $tc[0]->MONTO : null;
        $maestros   = self::maestros($corte);
        $data       = self::aplicarPolitica($data, $maestros, $tipoCambio);

        if ($maestros['OK']) {
            self::guardarCategorias($corte, $data);
        }

        $meta = self::meta($ini, $corte, $meses, $factor, $tipoCambio, $maestros['FECHA_ANTERIOR']);
        if (!$maestros['OK']) {
            $meta['AVISO'] = 'No se encontraron las tablas maestras del reorden en PRODUCCION (database/reorder_point2_sqlserver.sql): sin hermanas, origen ni historial de categorías.';
        }

        return [
            'META'    => $meta,
            'HEADERS' => self::headers($meses, $maestros['FECHA_ANTERIOR']),
            'DATA'    => $data,
        ];
    }

    /**
     * Maestros del reorden en PRODUCCION: grupos de hermanas, origen por fabricante y la foto de categorías
     * del análisis anterior (la última con fecha de corte menor a la actual).
     * Si las tablas aún no existen el reporte sigue funcionando sin ellos.
     */
    private static function maestros($corte)
    {
        $vacio = ['OK' => false, 'GRUPOS' => [], 'ORIGEN' => [], 'CAT_ANTERIOR' => [], 'FECHA_ANTERIOR' => null];
        try {
            $db = DB::connection('sqlsrv');

            $grupos = [];
            foreach ($db->select("SELECT ARTICULO, GRUPO FROM PRODUCCION.dbo.tbl_reorder_grupo_articulo WHERE ACTIVO = 1") as $g) {
                $grupos[trim($g->ARTICULO)] = trim($g->GRUPO);
            }

            $origen = [];
            foreach ($db->select("SELECT FABRICANTE, ORIGEN FROM PRODUCCION.dbo.tbl_reorder_origen_fabricante") as $o) {
                $origen[strtoupper(trim($o->FABRICANTE))] = trim($o->ORIGEN);
            }

            $anterior = $db->select("SELECT MAX(FECHA_CORTE) AS FECHA FROM PRODUCCION.dbo.tbl_reorder_categoria_hist WHERE FECHA_CORTE < ?", [$corte]);
            $fecha    = $anterior && $anterior[0]->FECHA ? substr($anterior[0]->FECHA, 0, 10) : null;
            $catAnt   = [];
            if ($fecha) {
                foreach ($db->select("SELECT ARTICULO, CATEGORIA FROM PRODUCCION.dbo.tbl_reorder_categoria_hist WHERE FECHA_CORTE = ?", [$fecha]) as $c) {
                    $catAnt[trim($c->ARTICULO)] = trim($c->CATEGORIA);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return $vacio;
        }

        return ['OK' => true, 'GRUPOS' => $grupos, 'ORIGEN' => $origen, 'CAT_ANTERIOR' => $catAnt, 'FECHA_ANTERIOR' => $fecha];
    }

    // Foto de las categorías del corte actual (una sola vez por fecha de corte) para comparar en el siguiente análisis
    private static function guardarCategorias($corte, array $data)
    {
        $db = DB::connection('sqlsrv');
        $existe = $db->select("SELECT COUNT(*) AS N FROM PRODUCCION.dbo.tbl_reorder_categoria_hist WHERE FECHA_CORTE = ?", [$corte]);
        if ($existe[0]->N > 0) {
            return;
        }
        $filas = array_values(array_filter($data, function ($r) { return $r['CAT_ACTUAL'] !== null; }));
        // SQL Server admite hasta 2100 parámetros por consulta: 3 por fila
        foreach (array_chunk($filas, 500) as $bloque) {
            $db->insert("INSERT INTO PRODUCCION.dbo.tbl_reorder_categoria_hist (FECHA_CORTE, ARTICULO, CATEGORIA) VALUES "
                . implode(', ', array_fill(0, count($bloque), '(?, ?, ?)')),
                array_merge(...array_map(function ($r) use ($corte) { return [$corte, $r['ARTICULO'], $r['CAT_ACTUAL']]; }, $bloque)));
        }
    }

    /**
     * Política de reorden v2.1 sobre las filas base (las reglas reproducen el análisis Contribucion_Reorden_2026-10-26):
     * presentación y empaque, categoría A/B/C/L (+CA), ranking privado, grupos de hermanas, disparador, reorden,
     * despachos, consolidación, veredicto y notas. $maestros: GRUPOS [articulo => grupo], ORIGEN [fabricante => origen],
     * CAT_ANTERIOR [articulo => categoría del análisis anterior] y FECHA_ANTERIOR.
     */
    public static function aplicarPolitica(array $data, array $maestros, $tipoCambio)
    {
        $grupos = $maestros['GRUPOS'] ?? [];
        $origen = $maestros['ORIGEN'] ?? [];

        // Presentación, empaque y origen
        foreach ($data as &$r) {
            // Unidades mínimas por empaque: "100/CAJA" -> 100, "10/FRASCO 1/CAJA" -> 10; ampollas, viales, "100ML/FRASCO" -> 1
            $r['UNDS_EMPAQUE']  = preg_match('/(\d[\d,]*)\s*\/\s*(CAJA|FRASCO|BLISTER)/u', $r['DESCRIPCION'], $m) ? (float) str_replace(',', '', $m[1]) : 1;
            $r['PRESENTACION']  = preg_match('/\b1,?000\s*\//u', $r['DESCRIPCION']) ? 'LICITACIÓN' : 'PRIVADO';
            $r['ORIGEN']        = $origen[strtoupper(trim($r['FABRICANTE']))] ?? self::ORIGEN_DEFAULT;
            $r['_GRUPO']        = $grupos[$r['ARTICULO']] ?? $r['ARTICULO'];
            $r['CAT_ACTUAL']    = ($r['PORC_PUBLICO'] > self::L_MIN_PUBLICO && $r['CONTRIB_PRIV'] < self::L_MAX_CONTRIB) ? 'L' : null;
        }
        unset($r);

        // Ranking privado (sin L) por contribución privada y clasificación por contribución acumulada;
        // el bloque L va al final ordenado por contribución pública
        usort($data, function ($a, $b) {
            $la = $a['CAT_ACTUAL'] === 'L'; $lb = $b['CAT_ACTUAL'] === 'L';
            if ($la !== $lb) return $la ? 1 : -1;
            return $la ? $b['CONTRIB_PUBLICA'] <=> $a['CONTRIB_PUBLICA'] : $b['CONTRIB_PRIV'] <=> $a['CONTRIB_PRIV'];
        });
        // ROUNDUP de Excel: redondea antes el ruido de punto flotante (2.000000002 -> 2)
        $redondeoArriba = function ($x) { return (int) ceil(round($x, 6)); };
        $total = array_sum(array_map(function ($r) { return $r['CAT_ACTUAL'] === 'L' ? 0 : $r['CONTRIB_PRIV']; }, $data));
        $acum  = 0; $rank = 0;
        $letra = [];
        foreach ($data as $i => $r) {
            if ($r['CAT_ACTUAL'] === 'L') {
                continue;
            }
            $previo = $total != 0 ? $acum / $total : 1;
            $acum  += $r['CONTRIB_PRIV'];
            $letra[$i] = $previo < self::CORTE_A ? 'A'
                : (($previo < self::CORTE_B || $r['CONTRIB_PRIV'] > self::MIN_CONTRIB_B) ? 'B' : 'C');
            $data[$i]['RANK'] = ++$rank;
        }

        // Las hermanas toman la mejor letra del grupo; el sufijo CA es propio (vende a Cruz Azul)
        $miembros = [];
        foreach ($data as $i => $r) {
            $miembros[$r['_GRUPO']][] = $i;
        }
        foreach ($letra as $i => $l) {
            foreach ($miembros[$data[$i]['_GRUPO']] as $j) {
                if (isset($letra[$j]) && $letra[$j] < $l) {
                    $l = $letra[$j];
                }
            }
            $data[$i]['CAT_ACTUAL'] = $l . ($data[$i]['PORC_CRUZ_AZUL'] > 0 ? 'CA' : '');
        }

        // Cobertura consolidada del grupo en unidades mínimas
        foreach ($miembros as $idx) {
            $tab = 0; $dem = 0;
            foreach ($idx as $i) {
                $tab += $data[$i]['TOTAL_DISP'] * $data[$i]['UNDS_EMPAQUE'];
                $dem += $data[$i]['PROM_DEPURADO'] * $data[$i]['UNDS_EMPAQUE'];
            }
            foreach ($idx as $i) {
                $otros = array_filter($idx, function ($j) use ($i) { return $j !== $i; });
                $data[$i]['PAR_HERMANA']      = $otros ? implode(', ', array_map(function ($j) use ($data) {
                    return 'Rk ' . ($data[$j]['RANK'] ?? '-');
                }, $otros)) : null;
                $data[$i]['TAB_DISP_GRUPO']   = $tab;
                $data[$i]['DEMANDA_GRUPO']    = $dem;
                $data[$i]['MESES_DISP_GRUPO'] = $dem != 0 ? $tab / $dem : null;
            }
        }

        // Disparador y reorden por presentación (la compra del grupo se reparte según la demanda de cada una)
        $proveedorOrdena = [];
        foreach ($data as &$r) {
            $l = $r['CAT_ACTUAL'] === 'L' ? 'L' : substr($r['CAT_ACTUAL'], 0, 1);
            $r['DISPARADOR'] = $l === 'A' ? (self::$disparadorA[$r['ORIGEN']] ?? 7) : ($l === 'B' ? self::$disparadorB : null);

            $relleno = $l === 'A' ? self::RELLENO_A : self::RELLENO_B;
            $falta   = ($r['DEMANDA_GRUPO'] > 0)
                ? $redondeoArriba(max(0, $relleno * $r['DEMANDA_GRUPO'] - $r['TAB_DISP_GRUPO']) * $r['PROM_DEPURADO'] / $r['DEMANDA_GRUPO'])
                : 0;
            $r['_FALTA'] = $falta;

            $r['REORDEN_UNDS'] = ($r['MESES_DISP_GRUPO'] === null || $r['DISPARADOR'] === null || $r['PROM_DEPURADO'] == 0
                || $r['MESES_DISP_GRUPO'] >= $r['DISPARADOR']) ? 0 : $falta;

            $reo   = $r['REORDEN_UNDS'];
            $costo = (float) $r['ULT_COSTO_LOC'];
            if ($reo > 0) {
                $grande = $reo * $costo > self::ORDEN_GRANDE_LOC || $reo / $r['PROM_DEPURADO'] > self::ORDEN_GRANDE_MES;
                $r['N_DESPACHOS']     = $grande ? min(4, max(2, $redondeoArriba($reo / $r['PROM_DEPURADO'] / 6))) : 1;
                $r['UNDS_X_DESPACHO'] = $redondeoArriba($reo / $r['N_DESPACHOS']);
                $r['REORDEN_LOC']     = $costo != 0 ? $reo * $costo : null;
                $r['LOC_X_DESPACHO']  = $r['REORDEN_LOC'] !== null ? $r['REORDEN_LOC'] / $r['N_DESPACHOS'] : null;
                $r['REORDEN_USD']     = ($r['ULT_COSTO_USD'] !== null && $r['ULT_COSTO_USD'] > 0) ? $reo * $r['ULT_COSTO_USD']
                    : (($r['REORDEN_LOC'] !== null && $tipoCambio) ? $r['REORDEN_LOC'] / $tipoCambio : null);
                $proveedorOrdena[$r['FABRICANTE']] = true;
            }
        }
        unset($r);

        // Veredicto (en orden de prioridad) y notas
        $catAnt   = $maestros['CAT_ANTERIOR'] ?? [];
        $fechaAnt = $maestros['FECHA_ANTERIOR'] ?? null;
        $etiqAnt  = $fechaAnt ? self::fechaCorta($fechaAnt, false) : null;
        foreach ($data as &$r) {
            $l = $r['CAT_ACTUAL'] === 'L' ? 'L' : substr($r['CAT_ACTUAL'], 0, 1);

            $r['CONSOLIDAR_UNDS'] = ($r['REORDEN_UNDS'] == 0 && $r['DISPARADOR'] !== null && $r['MESES_DISP_GRUPO'] !== null
                && $r['MESES_DISP_GRUPO'] <= $r['DISPARADOR'] + self::MARGEN_CONSOLIDAR
                && isset($proveedorOrdena[$r['FABRICANTE']]) && $r['_FALTA'] > 0) ? $r['_FALTA'] : null;

            $crec = $r['CREC_PRIV'];
            if ($l === 'L')                                                        $v = 'SOLO LICITACIÓN';
            elseif ($r['RIESGO_LOTE'] === 'SÍ')                                    $v = 'ALERTA VENCIMIENTO';
            elseif ($r['REORDEN_UNDS'] > 0)                                        $v = 'REORDENAR';
            elseif ($r['CONSOLIDAR_UNDS'] !== null)                                $v = 'CONSOLIDAR';
            elseif ($r['CONTRIB_PRIV'] < 0)                                        $v = 'REVISAR PRECIO';
            elseif ($r['MESES_DISP_GRUPO'] !== null && $r['MESES_DISP_GRUPO'] > self::SOBRE_STOCK) $v = 'SOBRE-STOCK';
            elseif (in_array($l, ['A', 'B']) && $crec !== null && $crec < self::CAIDA_DEFENDER) $v = 'DEFENDER';
            elseif ($l === 'C' && $crec !== null && $crec > self::SUBIR_CRECIMIENTO && $r['CONTRIB_PRIV'] > self::SUBIR_CONTRIB) $v = 'CANDIDATO A SUBIR';
            elseif ($l === 'C')                                                    $v = 'NO REORDENAR';
            else                                                                   $v = 'OK';
            $r['VEREDICTO'] = $v;

            $r['CAT_CAMBIO'] = ($r['CAT_ANTERIOR'] !== null && $r['CAT_ANTERIOR'] !== $r['CAT_ACTUAL']) ? '≠' : null;
            $r['CAT_12OCT']  = $catAnt[$r['ARTICULO']] ?? null;

            $n = [];
            if ($l === 'L') {
                $n[] = 'nicho público/licitación';
            } elseif ($r['PORC_PUBLICO'] > self::L_MIN_PUBLICO) {
                $n[] = round($r['PORC_PUBLICO'] * 100) . '% unds públicas: ranking y reorden usan SOLO lo privado';
            }
            if ($r['PAR_HERMANA']) {
                $n[] = 'presentación hermana (' . $r['PAR_HERMANA'] . '): cobertura y reorden CONSOLIDADOS en tabletas';
            }
            if ($r['DISPARADOR'] !== null && $r['MESES_DISP_GRUPO'] !== null
                && $r['MESES_DISP_GRUPO'] < $r['DISPARADOR'] - self::HOLGURA_LEAD_TIME) {
                $n[] = '⚠ URGENTE: cobertura consolidada por debajo del lead time — evaluar embarque parcial aéreo o proveedor más rápido';
            }
            if ($r['CONSOLIDAR_UNDS'] !== null) {
                $n[] = 'a ≤' . self::MARGEN_CONSOLIDAR . ' meses de su disparador consolidado: sumar ' . number_format($r['CONSOLIDAR_UNDS'])
                    . ' cajas a la próxima orden de ' . trim($r['FABRICANTE']);
            }
            if ($crec !== null && $crec < self::CAIDA_DEFENDER) {
                $n[] = 'caída privada >' . abs(self::CAIDA_DEFENDER * 100) . '% vs año ant.';
            }
            if ($r['N_CLIENTES'] <= self::NOTA_MAX_CLIENTES) {
                $n[] = 'solo ' . $r['N_CLIENTES'] . ' clientes';
            }
            if ($r['PORC_CRUZ_AZUL'] > self::NOTA_CRUZ_AZUL) {
                $n[] = 'Cruz Azul >' . (self::NOTA_CRUZ_AZUL * 100) . '% del SKU';
            }
            // Margen de reposición y venta intermitente solo aplican al ranking privado (no al bloque L)
            if ($l !== 'L' && $r['DELTA_MARGEN'] !== null && $r['DELTA_MARGEN'] < self::NOTA_CAIDA_MARGEN) {
                $n[] = 'margen de reposición cae >' . abs(self::NOTA_CAIDA_MARGEN * 100) . 'pp';
            }
            if ($l !== 'L' && $r['MESES_ACTIVOS'] <= self::MAX_MESES_INTERMITENTE) {
                $n[] = 'venta privada intermitente (≤' . self::MAX_MESES_INTERMITENTE . ' meses activos)';
            }
            if ($r['PORC_BONIF'] > 0) {
                $n[] = 'bonificación ' . round($r['PORC_BONIF'] * 100) . '%';
            }
            if ($r['REORDEN_UNDS'] > 0 && !$r['ULT_COSTO_LOC']) {
                $n[] = '⚠ sin último costo registrado — valorar la orden con costo del proveedor';
            }
            if ($r['CAT_12OCT'] !== null && $r['CAT_12OCT'] !== $r['CAT_ACTUAL']) {
                $n[] = 'cambió de categoría vs análisis ' . $etiqAnt . ' (' . $r['CAT_12OCT'] . ' → ' . $r['CAT_ACTUAL'] . ')';
            }
            $r['NOTAS'] = $n ? implode('; ', $n) : null;

            unset($r['_GRUPO'], $r['_FALTA']);
        }
        unset($r);

        return $data;
    }

    // 12-oct / 12-oct-2026
    private static function fechaCorta($f, $conAnio = true)
    {
        $t = strtotime($f);
        return date('j', $t) . '-' . strtolower(self::$nombreMes[date('n', $t) - 1]) . ($conAnio ? '-' . date('Y', $t) : '');
    }

    /**
     * Una fila del análisis a partir de un registro de ContribucionPorCanales::getData().
     * Ojo: canales devuelve las cantidades por canal ya formateadas ("1,234") y ARTICULO como enlace HTML.
     */
    private static function fila(array $row, $corte, $nMeses, $factor)
    {
        $num = function ($v) { return (float) str_replace(',', '', (string) $v); };

        // Venta, costo y unidades de 12 meses: privado y público
        $undsPriv = 0; $ventaPriv = 0; $costoPriv = 0;
        foreach (self::$canalesPrivados as $campo) {
            $undsPriv  += $num($row[$campo . '_CANTIDAD']);
            $ventaPriv += (float) $row[$campo . '_VENTA'];
            $costoPriv += (float) $row[$campo . '_COSTO'];
        }
        $undsPub = 0; $ventaPub = 0; $costoPub = 0;
        foreach (self::$canalesPublicos as $campo) {
            $undsPub  += $num($row[$campo . '_CANTIDAD']);
            $ventaPub += (float) $row[$campo . '_VENTA'];
            $costoPub += (float) $row[$campo . '_COSTO'];
        }
        $contribPriv = $ventaPriv - $costoPriv;

        // Unidades privadas por mes, de enero al mes del corte
        $mesCanal = (array) $row['MES_CANAL'];
        $mensual  = array_fill(0, $nMeses, 0);
        foreach (array_keys(self::$canalesPrivados) as $canal) {
            for ($m = 0; $m < $nMeses; $m++) {
                $mensual[$m] += (float) ($mesCanal[$canal]['U'][$m] ?? 0);
            }
        }
        $ajustado = $mensual;
        $ajustado[$nMeses - 1] *= $factor;
        $activos  = count(array_filter($mensual, function ($u) { return $u > 0; }));

        $promDep = $activos <= self::MAX_MESES_INTERMITENTE
            ? $undsPriv / 12
            : array_sum($ajustado) / $activos;
        $ultimos  = array_slice($ajustado, -3);
        $momentum = $promDep > 0 ? (array_sum($ultimos) / count($ultimos)) / $promDep : null;

        // Año anterior (Ene-Dic) por canal
        $antCanal = (array) $row['ANT_CANAL'];
        $ventaAnt = 0;
        foreach (array_keys(self::$canalesPrivados) as $canal) {
            $ventaAnt += (float) ($antCanal[$canal]['V'] ?? 0);
        }
        $ventaCA    = (float) $row['CRUZ_AZUL_VENTA'];
        $ventaAntCA = (float) ($antCanal['CRUZ_AZUL']['V'] ?? 0);

        // Stock
        $totalDisp = (float) $row['INVENTARIO'] + (float) $row['PEDIDO'] + (float) $row['TRANSITO'];
        $mesesDisp = $promDep > 0 ? $totalDisp / $promDep : null;

        // Lote (canales ya lo trae calculado: el de vencimiento más lejano) y última compra, de d/m/Y a Y-m-d
        $fecha = function ($v) {
            $f = $v ? \DateTime::createFromFormat('d/m/Y', $v) : false;
            return $f ? $f->format('Y-m-d') : null;
        };
        $loteVence = $fecha($row['Lote_Mas_a_Vencer_PRIVADO_6_MESES']);
        $cantLote  = (float) $row['Existencia_En_Lote_proximo_Vencer_6_MESES'];
        $mesesVcto = $loteVence ? (strtotime($loteVence) - strtotime($corte)) / 86400 / self::DIAS_MES : null;
        // El lote se consume con la demanda privada depurada más la pública mensual
        $consumo   = $promDep + $undsPub / 12;
        $mesesCons = ($loteVence && $cantLote > 0 && $consumo > 0) ? $cantLote / $consumo : null;

        // Margen a costo de reposición: precio promedio privado contra el último costo de compra
        $costoLoc   = $row['ULTIMO_COSTO_LOC'];
        $precioPriv = $undsPriv > 0 ? $ventaPriv / $undsPriv : 0;
        $ctbRepo    = ($costoLoc !== null && $precioPriv > 0) ? 1 - $costoLoc / $precioPriv : null;
        $ctbPriv    = $ventaPriv != 0 ? $contribPriv / $ventaPriv : 0;

        $r = [
            'RANK'                => null,
            'ARTICULO'            => (string) $row['ARTICULODESC'],
            'DESCRIPCION'         => $row['DESCRIPCION'],
            'FABRICANTE'          => trim($row['FABRICANTE']),
            'PRESENTACION'        => null,
            // La categoría de /canales es la del "reporte anterior"; la actual la calcula aplicarPolitica()
            'CAT_ANTERIOR'        => trim((string) $row['CATEGORIA']) ?: null,
            'CAT_ACTUAL'          => null,
            'CAT_CAMBIO'          => null,
            'CAT_12OCT'           => null,
            'PAR_HERMANA'         => null,
            'INVENTARIO'          => (float) $row['INVENTARIO'],
            'PEDIDO'              => (float) $row['PEDIDO'],
            'TRANSITO'            => (float) $row['TRANSITO'],
            'TOTAL_DISP'          => $totalDisp,
        ];
        foreach ($mensual as $m => $u) {
            $r[sprintf('M%02d', $m + 1)] = $u;
        }

        return $r + [
            'MESES_ACTIVOS'       => $activos,
            'PROM_SISTEMA'        => $row['PROM_NORMAL'] !== null ? (float) $row['PROM_NORMAL'] : null,
            'UNDS_12M_PRIVADO'    => $undsPriv,
            'UNDS_12M_PUBLICO'    => $undsPub,
            'PORC_PUBLICO'        => ($undsPriv + $undsPub) > 0 ? $undsPub / ($undsPriv + $undsPub) : 0,
            'PROM_DEPURADO'       => $promDep,
            'MESES_DISP_PROPIO'   => $mesesDisp,
            'UNDS_EMPAQUE'        => null,
            'TAB_DISP_GRUPO'      => null,
            'DEMANDA_GRUPO'       => null,
            'MESES_DISP_GRUPO'    => null,
            'VENTA_12M_PRIV'      => $ventaPriv,
            'CONTRIB_PRIV'        => $contribPriv,
            'PORC_CTB_PRIV'       => $ctbPriv,
            'CONTRIB_PUBLICA'     => $ventaPub - $costoPub,
            'VENTA_PRIV_ANT'      => $ventaAnt,
            'CREC_PRIV'           => $ventaAnt != 0 ? $ventaPriv / $ventaAnt - 1 : null,
            'CREC_CRUZ_AZUL'      => $ventaAntCA != 0 ? $ventaCA / $ventaAntCA - 1 : null,
            'MOMENTUM'            => $momentum,
            'LOTE_VENCE'          => $loteVence,
            'MESES_A_VCTO'        => $mesesVcto,
            'CANT_LOTE'           => $cantLote,
            'MESES_P_CONSUMIR'    => $mesesCons,
            'RIESGO_LOTE'         => ($mesesCons !== null && $mesesCons > $mesesVcto) ? 'SÍ' : null,
            'N_CLIENTES'          => (int) $row['CLIENTES'],
            'PORC_CRUZ_AZUL'      => $ventaPriv != 0 ? $ventaCA / $ventaPriv : 0,
            'ULT_COMPRA'          => $fecha($row['ULTIMA_COMPRA']),
            'ULT_COSTO_LOC'       => $costoLoc,
            'ULT_COSTO_USD'       => $row['ULTIMO_COSTO_DOL'],
            'PORC_CTB_REPOSICION' => $ctbRepo,
            'DELTA_MARGEN'        => $ctbRepo !== null ? $ctbRepo - $ctbPriv : null,
            'PORC_BONIF'          => $row['PORC_BONIFICADO'] !== null ? $row['PORC_BONIFICADO'] / 100 : 0,
            'ORIGEN'              => null,
            'DISPARADOR'          => null,
            'REORDEN_UNDS'        => null,
            'N_DESPACHOS'         => null,
            'UNDS_X_DESPACHO'     => null,
            'REORDEN_LOC'         => null,
            'LOC_X_DESPACHO'      => null,
            'REORDEN_USD'         => null,
            'CONSOLIDAR_UNDS'     => null,
            'VEREDICTO'           => null,
            'NOTAS'               => null,
        ];
    }

    private static function meta($ini, $corte, array $meses, $factor, $tipoCambio, $fechaAnterior)
    {
        $corta = function ($f) { return self::fechaCorta($f); };
        $parcial = $factor != 1
            ? ' ' . self::$nombreMes[count($meses) - 1] . ' es parcial (' . date('j', strtotime($corte)) . ' días): cuenta como mes activo y en el promedio depurado y el momentum se lleva a mes completo (×' . date('t', strtotime($corte)) . '/' . date('j', strtotime($corte)) . ').'
            : '';

        return [
            'FECHA_CORTE' => $corte,
            'PERIODO_INI' => $ini,
            'PERIODO_FIN' => $corte,
            'TIPO_CAMBIO' => $tipoCambio,
            'POLITICA'    => 'v2.1',
            'MESES'       => $meses,
            'FUENTE'      => 'GUMA@NET · Contribución por canales',
            'TITULO'      => 'REORDER POINT — UNIMARK · 12 meses (' . $corta($ini) . ' a ' . $corta($corte) . ') · POLÍTICA DE REORDEN v2.1: cobertura CONSOLIDADA por producto'
                . ($fechaAnterior ? ' · actualización del análisis del ' . $corta($fechaAnterior) : ''),
            'NOTA'        => 'POLÍTICA v2.1 — mín/máx sobre DEMANDA PRIVADA depurada, evaluada por PRODUCTO CONSOLIDADO: las presentaciones hermanas (misma molécula/concentración, presentación privada) suman inventario+pedido+tránsito y demanda EN UNIDADES MÍNIMAS (tabletas/ampollas); el disparador se evalúa contra los MESES DISP. GRUPO y la compra se reparte entre presentaciones según su demanda. A dispara <6 m (India/por verificar: 7) rellena a 18 · B dispara <4 rellena a 12 · C/L compra cero. CONSOLIDAR = grupo a ≤2 meses del disparador con proveedor ya ordenando. Órdenes >C$1M o >8 meses → 2–4 despachos.' . $parcial . ' AMARILLO = A/ACA · VERDE = B/BCA · AZUL = L.',
            'SEPARADOR_L' => '▼ BLOQUE NICHO LICITACIÓN (CATEGORÍA L) — sin ranking privado · comprar SOLO contra adjudicación · no entra en reorden',
        ];
    }

    // Encabezados completos de la hoja Decision_x_Producto (los meses dependen del corte)
    private static function headers(array $meses, $fechaAnterior)
    {
        $h = [
            'RANK' => 'Rk#', 'ARTICULO' => 'ARTÍCULO', 'DESCRIPCION' => 'DESCRIPCIÓN', 'FABRICANTE' => 'FABRICANTE',
            'PRESENTACION' => 'PRESENT.', 'CAT_ANTERIOR' => 'CAT. REPORTE ANT.', 'CAT_ACTUAL' => 'CAT. ACTUAL', 'CAT_CAMBIO' => 'Δ',
            // CAT_12OCT conserva el nombre del campo del Excel original; es la categoría del análisis anterior
            'CAT_12OCT' => 'CAT. ' . ($fechaAnterior ? self::fechaCorta($fechaAnterior, false) : 'ANÁLISIS ANT.'), 'PAR_HERMANA' => 'PAR (hermana)', 'INVENTARIO' => 'INVENTARIO', 'PEDIDO' => 'PEDIDO',
            'TRANSITO' => 'TRÁNSITO', 'TOTAL_DISP' => 'TOTAL DISP.',
        ];
        foreach ($meses as $i => $m) {
            $h[sprintf('M%02d', $i + 1)] = $m;
        }
        $ultimo = self::$nombreMes[count($meses) - 1];

        return $h + [
            'MESES_ACTIVOS' => 'MESES ACTIVOS (de ' . count($meses) . ')', 'PROM_SISTEMA' => 'PROM. SISTEMA',
            'UNDS_12M_PRIVADO' => 'UNDS 12M PRIVADO', 'UNDS_12M_PUBLICO' => 'UNDS 12M PÚB.+LICIT', 'PORC_PUBLICO' => '% PÚBLICO',
            'PROM_DEPURADO' => 'PROM. DEPURADO (privado; ' . $ultimo . ' a mes completo)', 'MESES_DISP_PROPIO' => 'MESES DISP. PROPIO',
            'UNDS_EMPAQUE' => 'UNDS/EMPAQUE', 'TAB_DISP_GRUPO' => 'TABLETAS DISP. GRUPO', 'DEMANDA_GRUPO' => 'DEMANDA GRUPO (tab/mes)',
            'MESES_DISP_GRUPO' => 'MESES DISP. GRUPO', 'VENTA_12M_PRIV' => 'VENTA 12M PRIVADA C$', 'CONTRIB_PRIV' => 'CONTRIB. PRIVADA C$',
            'PORC_CTB_PRIV' => '% CTB PRIV.', 'CONTRIB_PUBLICA' => 'CONTRIB. PÚBLICA C$', 'VENTA_PRIV_ANT' => 'VENTA PRIV. AÑO ANT. C$',
            'CREC_PRIV' => 'CRECIMIENTO PRIV. %', 'CREC_CRUZ_AZUL' => 'CREC. CRUZ AZUL %', 'MOMENTUM' => 'MOMENTUM (últimos 3 meses)',
            'LOTE_VENCE' => 'LOTE VENCE', 'MESES_A_VCTO' => 'MESES A VCTO.', 'CANT_LOTE' => 'CANT. LOTE', 'MESES_P_CONSUMIR' => 'MESES P/CONSUMIR',
            'RIESGO_LOTE' => 'RIESGO LOTE', 'N_CLIENTES' => 'N° CLIENTES', 'PORC_CRUZ_AZUL' => '% CRUZ AZUL (venta)',
            'ULT_COMPRA' => 'ÚLT. COMPRA', 'ULT_COSTO_LOC' => 'ÚLT. COSTO C$', 'ULT_COSTO_USD' => 'ÚLT. COSTO US$',
            'PORC_CTB_REPOSICION' => '% CTB A REPOSICIÓN', 'DELTA_MARGEN' => 'Δ MARGEN (pp)', 'PORC_BONIF' => '% BONIF.',
            'ORIGEN' => 'ORIGEN PROVEEDOR', 'DISPARADOR' => 'DISPARADOR (meses)', 'REORDEN_UNDS' => 'REORDEN (unds)',
            'N_DESPACHOS' => 'N° DESPACHOS', 'UNDS_X_DESPACHO' => 'UNDS POR DESPACHO', 'REORDEN_LOC' => 'REORDEN C$ TOTAL',
            'LOC_X_DESPACHO' => 'C$ POR DESPACHO (1er pago)', 'REORDEN_USD' => 'REORDEN US$ TOTAL',
            'CONSOLIDAR_UNDS' => 'CONSOLIDAR CON ORDEN (unds sugeridas)', 'VEREDICTO' => 'VEREDICTO', 'NOTAS' => 'NOTAS / BANDERAS',
        ];
    }

    /**
     * Columnas de la hoja Decision_x_Producto en el mismo orden del Excel original:
     * [campo, ancho, formato, centrado]. Los meses van de enero al mes del corte.
     */
    private static function columnasExcel($nMeses)
    {
        $n  = '#,##0';
        $p  = '0.0%';
        $c0 = '"C$ "#,##0';
        $d  = 'dd/mm/yyyy';

        $meses = [];
        for ($m = 1; $m <= $nMeses; $m++) {
            $meses[] = [sprintf('M%02d', $m), 7, $n, false];
        }

        return array_merge([
            ['RANK', 5, null, true],            ['ARTICULO', 10, null, false],      ['DESCRIPCION', 54, null, false],
            ['FABRICANTE', 15, null, false],    ['PRESENTACION', 11, null, false],  ['CAT_ANTERIOR', 9, null, false],
            ['CAT_ACTUAL', 9, null, true],      ['CAT_CAMBIO', 4, null, true],      ['CAT_12OCT', 8, null, true],
            ['PAR_HERMANA', 12, null, false],   ['INVENTARIO', 10, $n, false],      ['PEDIDO', 8, $n, false],
            ['TRANSITO', 9, $n, false],         ['TOTAL_DISP', 10, $n, false],
        ], $meses, [
            ['MESES_ACTIVOS', 8, '0', false],   ['PROM_SISTEMA', 9, $n, false],     ['UNDS_12M_PRIVADO', 10, $n, false],
            ['UNDS_12M_PUBLICO', 9, $n, false], ['PORC_PUBLICO', 8, $p, false],     ['PROM_DEPURADO', 10, '#,##0.0', false],
            ['MESES_DISP_PROPIO', 8, '0.0', false], ['UNDS_EMPAQUE', 9, $n, false], ['TAB_DISP_GRUPO', 12, $n, false],
            ['DEMANDA_GRUPO', 11, $n, false],   ['MESES_DISP_GRUPO', 8, '0.0', false], ['VENTA_12M_PRIV', 13, $c0, false],
            ['CONTRIB_PRIV', 12, $c0, false],   ['PORC_CTB_PRIV', 8, $p, false],    ['CONTRIB_PUBLICA', 12, $c0, false],
            ['VENTA_PRIV_ANT', 13, $c0, false], ['CREC_PRIV', 9, $p, false],        ['CREC_CRUZ_AZUL', 9, $p, false],
            ['MOMENTUM', 8, '0.00', false],     ['LOTE_VENCE', 11, $d, false],      ['MESES_A_VCTO', 8, '0.0', false],
            ['CANT_LOTE', 9, $n, false],        ['MESES_P_CONSUMIR', 8, '0.0', false], ['RIESGO_LOTE', 7, null, true],
            ['N_CLIENTES', 8, null, false],     ['PORC_CRUZ_AZUL', 9, $p, false],   ['ULT_COMPRA', 11, $d, false],
            ['ULT_COSTO_LOC', 10, '"C$ "#,##0.00', false], ['ULT_COSTO_USD', 9, '\$#,##0.00', false],
            ['PORC_CTB_REPOSICION', 10, $p, false], ['DELTA_MARGEN', 9, '\+0.0%;\-0.0%', false], ['PORC_BONIF', 8, $p, false],
            ['ORIGEN', 13, null, false],        ['DISPARADOR', 9, '0', true],       ['REORDEN_UNDS', 10, $n, false],
            ['N_DESPACHOS', 9, '0', true],      ['UNDS_X_DESPACHO', 11, $n, false], ['REORDEN_LOC', 13, $c0, false],
            ['LOC_X_DESPACHO', 12, $c0, false], ['REORDEN_USD', 11, '\$#,##0', false], ['CONSOLIDAR_UNDS', 13, $n, false],
            ['VEREDICTO', 17, null, false],     ['NOTAS', 62, null, false],
        ]);
    }

    // Colores del Excel original: AMARILLO = A/ACA · VERDE = B/BCA · AZUL = L
    private static $colorCategoria = ['A' => 'FFFF00', 'ACA' => 'FFFF00', 'B' => 'C6E0B4', 'BCA' => 'C6E0B4', 'L' => 'BDD7EE'];

    private static $colorVeredicto = [
        'REORDENAR'          => 'DDEBF7',
        'CONSOLIDAR'         => 'D6E4F0',
        'DEFENDER'           => 'FFF2CC',
        'ALERTA VENCIMIENTO' => 'FFC7CE',
        'SOBRE-STOCK'        => 'FFD9B3',
        'CANDIDATO A SUBIR'  => 'C6EFCE',
        'NO REORDENAR'       => 'EDEDED',
        'REVISAR PRECIO'     => 'FF9999',
        'SOLO LICITACIÓN'    => 'BDD7EE',
    ];

    /**
     * Excel con la misma estructura y estilo de la hoja Decision_x_Producto del análisis original:
     * título (fila 1), nota de política (fila 2), totales (fila 3), encabezados (fila 4), datos desde la fila 5,
     * bloque L (licitación) al final con su fila separadora, paneles inmovilizados en D5 y autofiltro.
     * Las alertas que en el original son formato condicional se pintan directo sobre la celda.
     */
    public static function Excel(array $meta, array $headers, array $data)
    {
        $columnas = self::columnasExcel(count($meta['MESES'] ?? []) ?: 12);
        $letra    = function ($i) { return \PHPExcel_Cell::stringFromColumnIndex($i); };
        $ultCol   = $letra(count($columnas) - 1);
        $idx      = [];
        foreach ($columnas as $i => $c) {
            $idx[$c[0]] = $i;
        }
        $col = function ($campo) use ($letra, $idx) { return $letra($idx[$campo]); };

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator('GUMA@NET')->setTitle('Reorder_Point');
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Reorder_Point');

        $borde = ['borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
        $relleno = function ($rango, $rgb) use ($sheet) {
            $sheet->getStyle($rango)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($rgb);
        };

        // Filas 1-2: título y nota de la política
        $sheet->setCellValue('A1', $meta['TITULO'] ?? 'REORDER POINT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('1F4E79');
        $sheet->setCellValue('A2', $meta['NOTA'] ?? '');
        $sheet->getStyle('A2')->getFont()->setSize(8)->getColor()->setRGB('595959');
        $sheet->getRowDimension(2)->setRowHeight(48);

        // Fila 4: encabezados
        $filaHead = 4;
        foreach ($columnas as $i => $c) {
            $sheet->setCellValueByColumnAndRow($i, $filaHead, $headers[$c[0]] ?? $c[0]);
            $sheet->getColumnDimension($letra($i))->setWidth($c[1]);
        }
        $rangoHead = 'A' . $filaHead . ':' . $ultCol . $filaHead;
        $sheet->getStyle($rangoHead)->applyFromArray($borde + [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true],
        ]);
        $relleno($rangoHead, '1F4E79');
        $sheet->getRowDimension($filaHead)->setRowHeight(48);

        // Ranking privado primero y el bloque L al final, como en el original
        $ranking = array_values(array_filter($data, function ($r) { return ($r['CAT_ACTUAL'] ?? '') !== 'L'; }));
        $bloqueL = array_values(array_filter($data, function ($r) { return ($r['CAT_ACTUAL'] ?? '') === 'L'; }));

        $fila = $filaHead + 1;
        $primeraFila = $fila;
        foreach ([$ranking, $bloqueL] as $b => $bloque) {
            if ($b === 1 && count($bloqueL)) {
                // Fila separadora del bloque L
                $sheet->setCellValue('C' . $fila, $meta['SEPARADOR_L'] ?? '▼ BLOQUE NICHO LICITACIÓN (CATEGORÍA L)');
                $relleno('A' . $fila . ':' . $ultCol . $fila, '2E5B8A');
                $sheet->getStyle('A' . $fila . ':' . $ultCol . $fila)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $fila++;
            }

            foreach ($bloque as $r) {
                foreach ($columnas as $i => $c) {
                    $v = $r[$c[0]] ?? null;
                    if ($c[0] === 'RANK' && $v === null) {
                        $v = $r['CAT_ACTUAL'] ?? null;
                    }
                    if ($v === null || $v === '') {
                        continue;
                    }
                    if ($c[2] === 'dd/mm/yyyy') {
                        $v = \PHPExcel_Shared_Date::PHPToExcel(strtotime($v));
                    }
                    if (is_numeric($v) && !in_array($c[0], ['ARTICULO'])) {
                        $sheet->setCellValueByColumnAndRow($i, $fila, $v);
                    } else {
                        $sheet->setCellValueExplicitByColumnAndRow($i, $fila, (string) $v, \PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                }

                // Categoría: A..J con el color de la categoría
                if (isset(self::$colorCategoria[$r['CAT_ACTUAL'] ?? ''])) {
                    $relleno('A' . $fila . ':J' . $fila, self::$colorCategoria[$r['CAT_ACTUAL']]);
                }
                // Presentaciones hermanas: columnas consolidadas del grupo en rosado
                if (!empty($r['PAR_HERMANA'])) {
                    $relleno($col('TAB_DISP_GRUPO') . $fila . ':' . $col('MESES_DISP_GRUPO') . $fila, 'FDE9D9');
                }
                // Veredicto en negrita con su color
                if (isset(self::$colorVeredicto[$r['VEREDICTO'] ?? ''])) {
                    $relleno($col('VEREDICTO') . $fila, self::$colorVeredicto[$r['VEREDICTO']]);
                }

                // Alertas (formato condicional del original)
                $alerta = function ($campo, $cumple, $rgb) use ($col, $fila, $relleno) {
                    if ($cumple) {
                        $relleno($col($campo) . $fila, $rgb);
                    }
                };
                $num = function ($campo) use ($r) { return isset($r[$campo]) && is_numeric($r[$campo]) ? (float) $r[$campo] : null; };
                $alerta('CAT_12OCT', !empty($r['CAT_12OCT']) && $r['CAT_12OCT'] !== $r['CAT_ACTUAL'], 'FCE4D6');
                $alerta('PORC_PUBLICO', $num('PORC_PUBLICO') !== null && $num('PORC_PUBLICO') > 0.5, 'FFD9B3');
                $alerta('MESES_DISP_GRUPO', $num('MESES_DISP_GRUPO') !== null && $num('DISPARADOR') !== null
                    && $num('MESES_DISP_GRUPO') < $num('DISPARADOR'), 'FFC7CE');
                $alerta('MESES_DISP_GRUPO', ($r['CAT_ACTUAL'] ?? '') !== 'L' && $num('MESES_DISP_GRUPO') !== null
                    && $num('MESES_DISP_GRUPO') > 36, 'FFD9B3');
                $alerta('CREC_PRIV', $num('CREC_PRIV') !== null && $num('CREC_PRIV') < -0.2, 'FFC7CE');
                $alerta('RIESGO_LOTE', ($r['RIESGO_LOTE'] ?? '') === 'SÍ', 'FFC7CE');

                $fila++;
            }
        }
        $ultFila = $fila - 1;

        // Formatos, bordes y alineación del cuerpo
        if ($ultFila >= $primeraFila) {
            $sheet->getStyle('A' . $primeraFila . ':' . $ultCol . $ultFila)->applyFromArray($borde);
            foreach ($columnas as $i => $c) {
                $rango = $letra($i) . $primeraFila . ':' . $letra($i) . $ultFila;
                if ($c[2]) {
                    $sheet->getStyle($rango)->getNumberFormat()->setFormatCode($c[2]);
                }
                if ($c[3]) {
                    $sheet->getStyle($rango)->getAlignment()
                        ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);
                }
            }
            $sheet->getStyle($col('VEREDICTO') . $primeraFila . ':' . $col('VEREDICTO') . $ultFila)->getFont()->setBold(true);
        }

        // Fila 3: totales del reorden (sobre las filas exportadas)
        $suma = function ($campo, $soloPositivos) use ($data) {
            return array_reduce($data, function ($a, $r) use ($campo, $soloPositivos) {
                $v = isset($r[$campo]) && is_numeric($r[$campo]) ? (float) $r[$campo] : 0;
                return $a + (($soloPositivos && $v <= 0) ? 0 : $v);
            }, 0);
        };
        $sheet->setCellValue('C3', 'TOTALES');
        $sheet->getStyle('C3')->getFont()->setBold(true);
        $totales = [
            'REORDEN_UNDS'    => [$suma('REORDEN_UNDS', false), '#,##0'],
            'REORDEN_LOC'     => [$suma('REORDEN_LOC', true), '"C$ "#,##0'],
            'LOC_X_DESPACHO'  => [$suma('LOC_X_DESPACHO', true), '"C$ "#,##0'],
            'REORDEN_USD'     => [$suma('REORDEN_USD', true), '\$#,##0'],
            'CONSOLIDAR_UNDS' => [$suma('CONSOLIDAR_UNDS', false), '#,##0'],
        ];
        foreach ($totales as $campo => $t) {
            $celda = $col($campo) . '3';
            $sheet->setCellValue($celda, $t[0]);
            $sheet->getStyle($celda)->applyFromArray($borde + ['font' => ['bold' => true]]);
            $sheet->getStyle($celda)->getNumberFormat()->setFormatCode($t[1]);
            $relleno($celda, 'D9D9D9');
        }

        $sheet->freezePane('D5');
        $sheet->setAutoFilter('A' . $filaHead . ':' . $ultCol . max($ultFila, $filaHead));
        $sheet->setSelectedCell('A1');

        return $objPHPExcel;
    }

    // PRUEBA: destinatarios fijos (correos de prueba, no los reales)
    private static $destinatariosPrueba = [
        'TO' => ['analista.guma@gmail.com'],
        'CC' => ['endscom@gmail.com', 'marangelox@gmail.com'],
    ];

    // Destinatarios del envío por correo: ['TO' => [...], 'CC' => [...]]
    public static function destinatarios()
    {
        return self::$destinatariosPrueba;
    }

    /**
     * Envía por correo el Excel Decision_x_Producto (misma lógica de envío que PromocionesController@SendAcciones).
     * $params['articulos']: artículos que quedaron en pantalla con los filtros; sin lista se envía todo.
     */
    public static function SendReOrderPoint(array $params = [])
    {
        $lista = self::destinatarios();
        if (count($lista['TO']) === 0) {
            return ['status' => false, 'message' => 'No hay destinatarios para el envío', 'destinatarios' => $lista];
        }

        set_time_limit(0);

        $res  = self::getData();
        $data = $res['DATA'] ?? [];
        $articulos = array_map('strval', (array) ($params['articulos'] ?? []));
        if (count($articulos)) {
            $data = array_values(array_filter($data, function ($r) use ($articulos) {
                return in_array((string) $r['ARTICULO'], $articulos, true);
            }));
        }
        if (count($data) === 0) {
            return ['status' => false, 'message' => 'No hay productos para enviar', 'destinatarios' => $lista];
        }

        $meta    = $res['META'];
        $fecha   = $meta['FECHA_CORTE'];
        $archivo = 'Reorder_Point_' . $fecha . '.xlsx';
        $ruta    = tempnam(sys_get_temp_dir(), 'rop2');

        try {
            // PHPExcel (abandonado) lanza warnings al escribir el autofiltro en PHP 7.2+: se silencian solo al escribir
            $nivel = error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);
            try {
                \PHPExcel_IOFactory::createWriter(self::Excel($meta, $res['HEADERS'], $data), 'Excel2007')->save($ruta);
            } finally {
                error_reporting($nivel);
            }

            // El adjunto va en memoria: el archivo temporal se borra al terminar
            $excel = file_get_contents($ruta);
            $vista = ['fechaCorte' => date('d/m/Y', strtotime($fecha))];

            Mail::send('pages.ReOrderPoint2.emails.reorder_point', $vista, function ($message) use ($lista, $fecha, $excel, $archivo) {
                $message->from(config('mail.from.address'), 'GUMA@NET - Reorder Point')
                        ->to($lista['TO'])
                        ->cc($lista['CC'])
                        ->subject('Reorder Point — corte ' . date('d/m/Y', strtotime($fecha)))
                        ->attachData($excel, $archivo, [
                            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]);
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'No se pudo enviar el correo: ' . $e->getMessage(), 'destinatarios' => $lista];
        } finally {
            @unlink($ruta);
        }

        return [
            'status'        => true,
            'enviado'       => true,
            'message'       => 'Correo enviado a ' . implode(', ', $lista['TO']) . ($lista['CC'] ? ' con copia a ' . implode(', ', $lista['CC']) : ''),
            'destinatarios' => $lista,
            'productos'     => count($data),
        ];
    }
}
