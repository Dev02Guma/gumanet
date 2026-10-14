<?php

namespace App;

use App\user;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use PhpParser\Node\Stmt\Foreach_;

use App\Logs_calcs;

class ContribucionPorCanales extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_contribucion_canales";
    //protected $table = 'PRODUCCION.dbo.tbl_gnet_ContribucionCanales';

    public function getClasificacion()
    {
        return $this->hasOne(ReorderPointArticulos::class, 'ARTICULO', 'ARTICULO');
    }

    public function getTransito()
    {
        return $this->hasMany(EstadoCompras::class, 'ARTICULO', 'ARTICULO');
    }

    public function getInventario()
    {
        return $this->hasMany(IwebBodegas::class, 'ARTICULO', 'ARTICULO');
    }

    public function getPromedio()
    {
        return $this->hasOne(PromedioCanales::class, 'ARTICULO', 'ARTICULO');
    }

    public static function getData(){
        $json = array(); $i = 0;
        $sql        = ContribucionPorCanales::with(['getTransito','getInventario','getPromedio'])->get();
        $Meses      = DB::connection('sqlsrv')->select('EXEC PRODUCCION.dbo.sp_calc_12_month_canales_articulo_dev ?, ?', ['Todos',0]);
        $fecha      = DB::connection('sqlsrv')->select("SELECT MIN(fecha) AS primera_fecha, MAX(fecha) AS ultima_fecha FROM PRODUCCION.dbo.tbl_contribucion_canales");
        $NameMonths = ContribucionPorCanales::NameMonth($fecha[0]->ultima_fecha);
        $categoria  = DB::connection('sqlsrv')->select("SELECT * FROM PRODUCCION.dbo.tbl_categoria_articulo_canales");
        $lote       = DB::connection('sqlsrv')->select("SELECT ARTICULO, FECHA_VENCIMIENTO AS VENCE, SUM(CANT_DISPONIBLE) AS DISPONIBLE
            FROM PRODUCCION.dbo.iweb_lotes
            WHERE BODEGA IN ('002', '005', '006')
            GROUP BY ARTICULO, FECHA_VENCIMIENTO");

        $lotesPorArticulo = [];
        foreach ($lote as $item) {
            $lotesPorArticulo[$item->ARTICULO][] = $item;
        }

        $clientes = DB::connection('sqlsrv')->select("SELECT c.ARTICULO, COUNT(DISTINCT f.CLIENTE) AS CLIENTES
            FROM PRODUCCION.dbo.tbl_contribucion_canales c
            INNER JOIN Softland.umk.FACTURA f ON f.FACTURA = c.FACTURA AND f.TIPO_DOCUMENTO = 'F'
            GROUP BY c.ARTICULO");
        $clientesPorArticulo = array_column(array_map(function($x) { return (array) $x; }, $clientes), 'CLIENTES', 'ARTICULO');

        $compras = DB::connection('sqlsrv')->select("WITH compras AS (
                SELECT t.ARTICULO, t.AUDIT_TRANS_INV, MAX(t.FECHA) AS FECHA, SUM(t.CANTIDAD) AS CANTIDAD,
                       SUM(t.COSTO_TOT_FISC_LOC) AS COSTO_LOC, SUM(t.COSTO_TOT_FISC_DOL) AS COSTO_DOL
                FROM Softland.umk.TRANSACCION_INV t
                WHERE t.AJUSTE_CONFIG = '~OO~' AND t.NATURALEZA = 'E'
                  AND t.ARTICULO IN (SELECT DISTINCT ARTICULO FROM PRODUCCION.dbo.tbl_contribucion_canales)
                GROUP BY t.ARTICULO, t.AUDIT_TRANS_INV
            ), ultima AS (
                SELECT *, ROW_NUMBER() OVER (PARTITION BY ARTICULO ORDER BY FECHA DESC, AUDIT_TRANS_INV DESC) AS RN FROM compras
            )
            SELECT u.ARTICULO, u.FECHA, h.APLICACION, u.CANTIDAD,
                   u.COSTO_LOC / NULLIF(u.CANTIDAD, 0) AS COSTO_UNIT_LOC,
                   u.COSTO_DOL / NULLIF(u.CANTIDAD, 0) AS COSTO_UNIT_DOL
            FROM ultima u
            INNER JOIN Softland.umk.AUDIT_TRANS_INV h ON h.AUDIT_TRANS_INV = u.AUDIT_TRANS_INV
            WHERE u.RN = 1");
        $compraPorArticulo = [];
        foreach ($compras as $item) {
            $compraPorArticulo[$item->ARTICULO] = $item;
        }

        $bonificadas = DB::connection('sqlsrv')->select("SELECT ARTICULO,
                SUM(CASE WHEN VENTA = 0 THEN CANTIDAD ELSE 0 END) AS BONIFICADAS,
                SUM(CANTIDAD) AS UNIDADES
            FROM PRODUCCION.dbo.tbl_contribucion_canales
            WHERE CANAL IN ('FARMACIAS', 'CADENAS', 'MAYORISTAS', 'INSTITUCIONES_PRIVADAS', 'CRUZ_AZUL')
            GROUP BY ARTICULO");
        $bonifPorArticulo = [];
        foreach ($bonificadas as $item) {
            $bonifPorArticulo[$item->ARTICULO] = $item;
        }

        $anterior = ContribucionPorCanales::periodoAnterior($fecha[0]->ultima_fecha);
        $meses    = ContribucionPorCanales::mesesAnioActual($fecha[0]->ultima_fecha);
        $mesIni   = $meses['ini'];
        $mesFin   = $meses['fin'];
        $mesesYM  = $meses['ym'];   // indice 0 = enero

        $ventasCanal = DB::connection('sqlsrv')->select("SELECT ARTICULO, CANAL, YEAR(FECHA) * 100 + MONTH(FECHA) AS YM,
                SUM(CASE WHEN FECHA BETWEEN ? AND ? THEN CANTIDAD ELSE 0 END) AS ANT_U,
                SUM(CASE WHEN FECHA BETWEEN ? AND ? THEN VENTA    ELSE 0 END) AS ANT_V,
                SUM(CASE WHEN FECHA BETWEEN ? AND ? THEN CANTIDAD ELSE 0 END) AS MES_U,
                SUM(CASE WHEN FECHA BETWEEN ? AND ? THEN VENTA    ELSE 0 END) AS MES_V
            FROM (" . ContribucionPorCanales::sqlVentasConCanal() . ") x
            GROUP BY ARTICULO, CANAL, YEAR(FECHA) * 100 + MONTH(FECHA)", [
                $anterior['ini'], $anterior['fin'], $anterior['ini'], $anterior['fin'],
                $mesIni, $mesFin, $mesIni, $mesFin,
                min($anterior['ini'], $mesIni), $mesFin,
            ]);

        $antPorArticulo   = [];
        $antPorCanal      = [];
        $mesesPorCanal    = [];
        foreach ($ventasCanal as $item) {
            $art = $item->ARTICULO; $canal = $item->CANAL;

            if ($item->ANT_U != 0 || $item->ANT_V != 0) {
                $antPorArticulo[$art]['U'] = ($antPorArticulo[$art]['U'] ?? 0) + $item->ANT_U;
                $antPorArticulo[$art]['V'] = ($antPorArticulo[$art]['V'] ?? 0) + $item->ANT_V;
                $antPorCanal[$art][$canal]['U'] = ($antPorCanal[$art][$canal]['U'] ?? 0) + $item->ANT_U;
                $antPorCanal[$art][$canal]['V'] = ($antPorCanal[$art][$canal]['V'] ?? 0) + $item->ANT_V;
            }

            $idx = array_search((int) $item->YM, $mesesYM, true);
            if ($idx !== false && ($item->MES_U != 0 || $item->MES_V != 0)) {
                if (!isset($mesesPorCanal[$art][$canal])) {
                    $mesesPorCanal[$art][$canal] = ['U' => array_fill(0, 12, 0), 'V' => array_fill(0, 12, 0)];
                }
                $mesesPorCanal[$art][$canal]['U'][$idx] += (float) $item->MES_U;
                $mesesPorCanal[$art][$canal]['V'][$idx] += (float) $item->MES_V;
            }
        }

        $fields = [
            "FARMACIA_CANTIDAD",
            "CADENA_FARMACIA_CANTIDAD",
            "MAYORISTA_CANTIDAD",
            "INSTITUCION_PRIVADA_CANTIDAD",
            "CRUZ_AZUL_CANTIDAD",
            "INSTITUCION_PUBLICA_CANTIDAD",
            "LICITACION_CANTIDAD"
        ];
        $fieldsVenta = [
            "FARMACIA_VENTA",
            "CADENA_FARMACIA_VENTA",
            "MAYORISTA_VENTA",
            "INSTITUCION_PRIVADA_VENTA",
            "CRUZ_AZUL_VENTA",
            "INSTITUCION_PUBLICA_VENTA",
            "LICITACION_VENTA"
        ];
        $fieldsCosto = [
            "FARMACIA_COSTO",
            "CADENA_FARMACIA_COSTO",
            "MAYORISTA_COSTO",
            "INSTITUCION_PRIVADA_COSTO",
            "CRUZ_AZUL_COSTO",
            "INSTITUCION_PUBLICA_COSTO",
            "LICITACION_COSTO"
        ];

        foreach($sql as $row){
            $TotalCantidad = 0; $TotalCosto = 0; $TotalVenta = 0; $countGlobal = 0;
            foreach ($fields as $field) {
                if (isset($row->$field)) {
                    $valor = (float)$row->$field;

                    $TotalCantidad += $valor;
                    $countGlobal++;
                }
            }
            foreach ($fieldsVenta as $field) {
                if (isset($row->$field)) {
                    $valor = (float)$row->$field;
                
                    $TotalVenta += $valor;
                }
            }

            foreach ($fieldsCosto as $field) {
                if (isset($row->$field)) {
                    $valor = (float)$row->$field;
                
                    $TotalCosto += $valor;
                }
            }
            $fechaActual = date('Y-m-d');
            $fechaVencimiento = null;
            $articulo = $row['ARTICULO'];
            $cantDisponible = 0;
            $cantProxima = 0;
            $mess12 = null;
            $categ = "";
            $pedido = 0;
            $transito = 0;
            $inventario = 0;

            foreach($lotesPorArticulo[$articulo] ?? [] as $item) {
                $vence = substr($item->VENCE, 0, 10);
                if($vence >= $fechaActual) {
                    $cantDisponible += $item->DISPONIBLE;

                    if (is_null($fechaVencimiento) || $vence > $fechaVencimiento) {
                        $fechaVencimiento = $vence;
                        $cantProxima = $item->DISPONIBLE;
                    }
                }
            }

            foreach($Meses as $item){
                if($item->ARTICULO == $articulo){
                    $mess12 = array_map(function($month, $value) use ($item) { 
                                return [
                                        "Mes"   => $month,
                                        "data"  => (float) number_format($item->$value,2,".","")
                                        ];
                                }, $NameMonths, range(1, 12));
                }
            }

            foreach($categoria as $item){
                if($item->ARTICULO == $articulo){
                    $categ = $item->CATEGORIA;
                    break;
                }
            }

            foreach ($row->getTransito as $transitos) {
                if ($transitos->estado_compra == 'TRANSITO') {
                    $transito += $transitos->Cantidad;
                }

                if ($transitos->estado_compra == 'PEDIDO') {
                    $pedido += $transitos->Cantidad;
                }
            }
            foreach ($row->getInventario as $disponible) {
                if ($disponible->BODEGA == '002' || $disponible->BODEGA == '005' || $disponible->BODEGA == '006' ) {
                    $inventario += $disponible->CANT_DISPONIBLE;
                }
            }

            

            $json[$i]['COSTO_PROM_PRIV_PACK']                       = (($TotalCantidad-$row['INSTITUCION_PUBLICA_CANTIDAD']) > 0) ? ($TotalCosto-$row['INSTITUCION_PUBLICA_COSTO'])/($TotalCantidad-$row['INSTITUCION_PUBLICA_CANTIDAD']):0;
            $json[$i]['COSTO_PROM_MINSA_PACK']                      = ($row['INSTITUCION_PUBLICA_CANTIDAD'] > 0) ? $row['INSTITUCION_PUBLICA_COSTO']/$row['INSTITUCION_PUBLICA_CANTIDAD']:0;
            $json[$i]['Disponibilidad_Packs_PRIVADO_6_MESES']       = $cantDisponible;
            $json[$i]['Lote_Mas_a_Vencer_PRIVADO_6_MESES']          = is_null($fechaVencimiento) ? null : date('d/m/Y',strtotime($fechaVencimiento));
            $json[$i]['Existencia_En_Lote_proximo_Vencer_6_MESES']  = $cantProxima;
            $compra                                                 = $compraPorArticulo[$articulo] ?? null;
            $json[$i]['CLIENTES']                                   = (int) ($clientesPorArticulo[$articulo] ?? 0);
            $json[$i]['ULTIMA_COMPRA']                              = $compra ? date('d/m/Y', strtotime($compra->FECHA)) : null;
            $json[$i]['ULTIMA_COMPRA_DOC']                          = $compra ? trim($compra->APLICACION) : null;
            $json[$i]['ULTIMA_COMPRA_CANT']                         = $compra ? (float) $compra->CANTIDAD : null;
            $json[$i]['ULTIMO_COSTO_LOC']                           = $compra ? (float) $compra->COSTO_UNIT_LOC : null;
            $json[$i]['ULTIMO_COSTO_DOL']                           = $compra ? (float) $compra->COSTO_UNIT_DOL : null;
            $bonif                                                  = $bonifPorArticulo[$articulo] ?? null;
            $json[$i]['UNDS_BONIFICADAS']                           = $bonif ? (float) $bonif->BONIFICADAS : 0;
            $json[$i]['UNDS_PRIVADAS']                              = $bonif ? (float) $bonif->UNIDADES : 0;
            $json[$i]['PORC_BONIFICADO']                            = ($bonif && $bonif->UNIDADES > 0) ? ($bonif->BONIFICADAS / $bonif->UNIDADES) * 100 : null;
            $json[$i]['ANT_UNIDADES']                               = (float) ($antPorArticulo[$articulo]['U'] ?? 0);
            $json[$i]['ANT_VENTA']                                  = (float) ($antPorArticulo[$articulo]['V'] ?? 0);
            $json[$i]['ANT_CANAL']                                  = (object) ($antPorCanal[$articulo] ?? []);
            $json[$i]['MES_CANAL']                                  = (object) ($mesesPorCanal[$articulo] ?? []);
            $json[$i]['ARTICULO']                                   = '<a href="#!" onclick="getDetalleArticulo('."'".$row['ARTICULO']."'".', '."'".strtoupper($row['DESCRIPCION'])."'".')" >'.$row['ARTICULO'].'</a>';
            $json[$i]['ARTICULODESC']                               = $row['ARTICULO'];
            $json[$i]['DESCRIPCION']                                = strtoupper($row['DESCRIPCION']);
            $json[$i]['FABRICANTE']                                 = strtoupper($row['FABRICANTE']);
            $json[$i]['CATEGORIA']                                  = $categ;
            $json[$i]['FARMACIA_CANTIDAD']                          = number_format($row['FARMACIA_CANTIDAD'],0,'.',',');            
            $json[$i]['FARMACIA_PROMEDIO']                          = $row['FARMACIA_PROMEDIO'];
            $json[$i]['FARMACIA_VENTA']                             = $row['FARMACIA_VENTA'];
            $json[$i]['FARMACIA_COSTO']                             = $row['FARMACIA_COSTO'];
            $json[$i]['FARMACIA_CONTRIBUCION']                      = $row['FARMACIA_CONTRIBUCION'];
            $json[$i]['FARMACIA_MARGEN']                            = $row['FARMACIA_MARGEN'];
            $json[$i]['CADENA_FARMACIA_CANTIDAD']                   = number_format($row['CADENA_FARMACIA_CANTIDAD'],0,'.',',');
            $json[$i]['CADENA_FARMACIA_PROMEDIO']                   = $row['CADENA_FARMACIA_PROMEDIO'];
            $json[$i]['CADENA_FARMACIA_VENTA']                      = $row['CADENA_FARMACIA_VENTA'];
            $json[$i]['CADENA_FARMACIA_COSTO']                      = $row['CADENA_FARMACIA_COSTO'];
            $json[$i]['CADENA_FARMACIA_CONTRIBUCION']               = $row['CADENA_FARMACIA_CONTRIBUCION'];
            $json[$i]['CADENA_FARMACIA_MARGEN']                     = $row['CADENA_FARMACIA_MARGEN'];
            $json[$i]['MAYORISTA_CANTIDAD']                         = number_format($row['MAYORISTA_CANTIDAD'],0,'.',',');
            $json[$i]['MAYORISTA_PROMEDIO']                         = $row['MAYORISTA_PROMEDIO'];
            $json[$i]['MAYORISTA_VENTA']                            = $row['MAYORISTA_VENTA'];
            $json[$i]['MAYORISTA_COSTO']                            = $row['MAYORISTA_COSTO'];
            $json[$i]['MAYORISTA_CONTRIBUCION']                     = $row['MAYORISTA_CONTRIBUCION'];
            $json[$i]['MAYORISTA_MARGEN']                           = $row['MAYORISTA_MARGEN'];
            $json[$i]['INSTITUCION_PRIVADA_CANTIDAD']               = number_format($row['INSTITUCION_PRIVADA_CANTIDAD'],0,'.',',');
            $json[$i]['INSTITUCION_PRIVADA_PROMEDIO']               = $row['INSTITUCION_PRIVADA_PROMEDIO'];
            $json[$i]['INSTITUCION_PRIVADA_VENTA']                  = $row['INSTITUCION_PRIVADA_VENTA'];
            $json[$i]['INSTITUCION_PRIVADA_COSTO']                  = $row['INSTITUCION_PRIVADA_COSTO'];
            $json[$i]['INSTITUCION_PRIVADA_CONTRIBUCION']           = $row['INSTITUCION_PRIVADA_CONTRIBUCION'];
            $json[$i]['INSTITUCION_PRIVADA_MARGEN']                 = $row['INSTITUCION_PRIVADA_MARGEN'];
            $json[$i]['CRUZ_AZUL_CANTIDAD']                         = number_format($row['CRUZ_AZUL_CANTIDAD'],0,'.',',');
            $json[$i]['CRUZ_AZUL_PROMEDIO']                         = $row['CRUZ_AZUL_PROMEDIO'];
            $json[$i]['CRUZ_AZUL_VENTA']                            = $row['CRUZ_AZUL_VENTA'];
            $json[$i]['CRUZ_AZUL_COSTO']                            = $row['CRUZ_AZUL_COSTO'];
            $json[$i]['CRUZ_AZUL_CONTRIBUCION']                     = $row['CRUZ_AZUL_CONTRIBUCION'];
            $json[$i]['CRUZ_AZUL_MARGEN']                           = $row['CRUZ_AZUL_MARGEN'];
            $json[$i]['INSTITUCION_PUBLICA_CANTIDAD']               = number_format($row['INSTITUCION_PUBLICA_CANTIDAD'],0,'.',',');
            $json[$i]['INSTITUCION_PUBLICA_PROMEDIO']               = $row['INSTITUCION_PUBLICA_PROMEDIO'];
            $json[$i]['INSTITUCION_PUBLICA_VENTA']                  = $row['INSTITUCION_PUBLICA_VENTA'];
            $json[$i]['INSTITUCION_PUBLICA_COSTO']                  = $row['INSTITUCION_PUBLICA_COSTO'];
            $json[$i]['INSTITUCION_PUBLICA_CONTRIBUCION']           = $row['INSTITUCION_PUBLICA_CONTRIBUCION'];
            $json[$i]['INSTITUCION_PUBLICA_MARGEN']                 = $row['INSTITUCION_PUBLICA_MARGEN'];
            $json[$i]['LICITACION_CANTIDAD']                        = number_format($row['LICITACION_CANTIDAD'],0,'.',',');
            $json[$i]['LICITACION_PROMEDIO']                        = $row['LICITACION_PROMEDIO'];
            $json[$i]['LICITACION_VENTA']                           = $row['LICITACION_VENTA'];
            $json[$i]['LICITACION_COSTO']                           = $row['LICITACION_COSTO'];
            $json[$i]['LICITACION_CONTRIBUCION']                    = $row['LICITACION_CONTRIBUCION'];
            $json[$i]['LICITACION_MARGEN']                          = $row['LICITACION_MARGEN'];
            $json[$i]['TOTAL_VENTAS_PACK']                          = number_format($TotalCantidad,0,'.',',');
            $json[$i]['TOTAL_PRECIO_PROM']                          = ($TotalCantidad > 0) ? $TotalVenta/$TotalCantidad:0;
            $json[$i]['TOTAL_VENTAS_C$']                            = $TotalVenta;
            $json[$i]['TOTAL_COSTOS_C$']                            = $TotalCosto;
            $json[$i]['TOTAL_CONTRIBUCION_C$']                      = $TotalVenta-$TotalCosto;
            $json[$i]['TOTAL_MARGEN']                               = (($TotalVenta > 0) ? ($TotalVenta-$TotalCosto)/$TotalVenta:0)*100;
            $json[$i]['INVENTARIO']                                 = $inventario;
            $json[$i]['PEDIDO']                                     = $pedido;
            $json[$i]['TRANSITO']                                   = $transito;
            $json[$i]['PROM_NORMAL']                                = optional($row->getPromedio)->PROM_MONTH;
            $json[$i]['CANTIDAD_MES']                               = $mess12;
            $i++;
        }
        return $json;
    }

    public static function getDataCanal($articulo, $canal, $opcion){
        $Meses = DB::connection('sqlsrv')->select('EXEC PRODUCCION.dbo.sp_calc_12_month_canales_articulo_dev ?, ?', [$canal,$opcion]);
        $result = DB::connection('sqlsrv')->select("SELECT MIN(fecha) AS primera_fecha, MAX(fecha) AS ultima_fecha FROM PRODUCCION.dbo.tbl_contribucion_canales");
        $NameMonths = ContribucionPorCanales::NameMonth($result[0]->ultima_fecha);
        $json = array(); $mess12 = 0;

        foreach($Meses as $item){
            if($item->ARTICULO == $articulo){
                $mess12 = array_map(function($month, $value) use ($item) { 
                            return [
                                    "Mes"   => $month,
                                    "data"  => (float) number_format($item->$value,2,".","")
                                    
                                    ];
                            }, $NameMonths, range(1, 12));
                $json[0]["EJECUTADO_UND_YTD"] = $item->EJECUTADO_UND_YTD;
            }
            
        }
        $json[0]['CANTIDAD_MES'] = $mess12;
        return $json;
    }

    public static function NameMonth($currentDate) {

        $startOfMonth = date('Y-m-01', strtotime($currentDate));

        $FechaIni = date('Y-m-d', strtotime('-11 months', strtotime($startOfMonth)));
        $FechaEnd = date('Y-m-d', strtotime($currentDate));

        // Array para almacenar los nombres de los meses
        $months = [];

        $start = new \DateTime($FechaIni);
        $end = new \DateTime($FechaEnd);

        // Iterar desde la fecha inicial hasta la fecha final
        while ($start <= $end) {
            $months[] = $start->format('My'); // 'M' para el nombre abreviado del mes, 'y' para el año en dos dígitos
            $start->modify('+1 month');
        }

        // Convertir el formato "M y" a "Ene23", "Feb23", etc.
        $months = array_map(function($month) {
            return str_replace(
                ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 
                ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
                $month
            );
        }, $months);

        return $months;
        
    }

    public static function calcularCanales($fechaIni, $fechaEnd)
    {

        DB::connection('sqlsrv')->statement("SET NOCOUNT ON ; EXEC PRODUCCION.dbo.pr_calcular_canal_contribucion"); 
        
        
        ContribucionPorCanalesTable::where(function ($query) use ($fechaIni, $fechaEnd) {
            $query->where('FECHA', '<', $fechaIni)
                ->orWhere('FECHA', '>', $fechaEnd);
        })->delete();

        
        DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.sp_categoria_articulo_canales");

        // Insertar en el modelo Logs_calcs
        Logs_calcs::create([
            'Modulo'        => 'AportePorCanales',
            'ini'           => $fechaIni,
            'end'           => $fechaEnd,
            'Observacion'   => 'Calculo de Aporte por canales con fechas: ' . date('Y-m-d H:i')
        ]);
        
    }

    public static function periodoFechas()
    {
        $currentDate = date('Y-m-d');
        $startOfMonth = date('Y-m-01', strtotime($currentDate));

        $FechaIni   = date('Y-m-d 00:00:00.000', strtotime('-11 months', strtotime($startOfMonth)));
        $FechaEnd   = date('Y-m-d 00:00:00.000', strtotime($currentDate . ' -1 days'));

        $result = DB::connection('sqlsrv')->select("SELECT MIN(fecha) AS primera_fecha, MAX(fecha) AS ultima_fecha FROM PRODUCCION.dbo.tbl_contribucion_canales");
        $anterior = ContribucionPorCanales::periodoAnterior($result[0]->ultima_fecha);
        return [
            'primera_fecha' => date('Y-m-01', strtotime($result[0]->primera_fecha)),
            'ultima_fecha' => $result[0]->ultima_fecha,
            'fechaIni' => $FechaIni,
            'fechaEnd' => $FechaEnd,
            'anterior_ini' => $anterior['ini'],
            'anterior_fin' => $anterior['fin'],
            'meses'        => ContribucionPorCanales::mesesAnioActual($result[0]->ultima_fecha)['nombres'],
        ];
    }

    // Lineas de venta con su canal, entre dos fechas (los dos ultimos ? del query que lo use).
    // Replica la fuente, filtros y clasificacion de canal de PRODUCCION.dbo.pr_calcular_canal_contribucion:
    // si cambian alla, hay que cambiarlos aqui tambien.
    private static function sqlVentasConCanal(){
        return "SELECT ARTICULO, Fecha_de_factura AS FECHA, CANTIDAD_TOTAL AS CANTIDAD, VentaNetaLocal AS VENTA,
                CASE
                    WHEN VEND_ACTUAL = 'F12' THEN
                        CASE
                            WHEN CLIENTE_CODIGO IN ('03165', '612-00-00') AND ARTICULO <> 'RF000001' THEN 'LICITACIONES'
                            WHEN CLIENTE_CODIGO = '03164' THEN 'CRUZ_AZUL' ELSE 'INSTITUCIONES_PUBLICAS'
                        END
                    WHEN VEND_ACTUAL = 'F02' THEN 'INSTITUCIONES_PRIVADAS'
                    WHEN VEND_ACTUAL IN ('F04', 'F13') THEN 'MAYORISTAS'
                    WHEN VEND_ACTUAL = 'F01' THEN 'OTRAS'
                    WHEN EXISTS (SELECT 1 FROM PRODUCCION.dbo.view_cadena_de_farmacia T1 WHERE T1.CLIENTE = CLIENTE_CODIGO) THEN 'CADENAS'
                    ELSE 'FARMACIAS'
                END AS CANAL
            FROM Softland.dbo.ANA_VentasTotales_MOD_Contabilidad_UMK
            WHERE ANULADA = 'N'
            AND ARTICULO NOT LIKE 'VU%'
            AND ARTICULO NOT LIKE '%-M'
            AND ARTICULO NOT IN ('11720011', '30042041')
            AND ARTICULO IN (SELECT DISTINCT ARTICULO FROM PRODUCCION.dbo.tbl_contribucion_canales)
            AND Fecha_de_factura BETWEEN ? AND ?";
    }

    // Meses de la venta mensual por canal: enero a diciembre del año del cierre del reporte.
    // Se corta en la fecha de cierre, asi que los meses posteriores quedan en 0 hasta recalcular.
    public static function mesesAnioActual($ultimaFecha){
        $anio   = date('Y', strtotime($ultimaFecha));
        $nombre = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $ym = []; $nombres = [];
        for ($m = 1; $m <= 12; $m++) {
            $ym[]      = (int) ($anio * 100 + $m);
            $nombres[] = $nombre[$m - 1] . substr($anio, 2);
        }
        return [
            'ini'     => $anio . '-01-01 00:00:00',
            'fin'     => date('Y-m-d 23:59:59', strtotime($ultimaFecha)),
            'ym'      => $ym,
            'nombres' => $nombres,
        ];
    }

    // Año anterior completo (enero a diciembre) al año del cierre del reporte, para "F. UNDS 25" / "F. VTA 25"
    public static function periodoAnterior($ultimaFecha){
        $anioAnt = date('Y', strtotime($ultimaFecha)) - 1;
        return [
            'ini' => $anioAnt . '-01-01 00:00:00',
            'fin' => $anioAnt . '-12-31 23:59:59',
        ];
    }
    public static function ExportToExcel() {
        $objPHPExcel = new PHPExcel();
        $tituloReporte = "";
        $titulosColumnas = array();
        $columnIndex = 0;
        $rowIndex = 1;

        $estiloTituloColumnas = array(
            'font' => array(
                        'name'  => 'Arial',
                        'bold'  => true
            ),
            'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'wrap'          => TRUE
                            ),
            'borders' => array(
                            'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                            )
            )
        );
                
        $estiloInformacion = new PHPExcel_Style();
        $estiloInformacion->applyFromArray(
            array(
                'borders' => array(
                'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ),
                'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                ),
                )
            )
        );    

        $RowReOrderPoint = ContribucionPorCanales::all();
        $titulosColumnas = array_keys(ContribucionPorCanales::first()->toArray());

    
        //TODO: AGREGAR CORRECTAMENTE DESDE EL CAMPO DE RANGO QUE SE ESTA EVALUANDO
        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', "Actualizado al : ". $RowReOrderPoint[0][ 'FechaFinal']);       

       
        
        foreach ($titulosColumnas as $titulo) {
            //VALIDACION SI EL CAMPO ES ENTERO Y INCREMENTA A 1 PARA EL NUMERO DE MESES
            $titulo = (!is_string($titulo)) ? strval( $titulo + 1) : $titulo ;
            $CantidadFarmacia = 0; $VentasFarmacia = 0; $CostoFarmacia = 0;
            $CantidadCadenaFarmacia = 0; $VentasCadenaFarmacia = 0; $CostoCadenaFarmacia = 0;
            $CantidadMayorista = 0; $VentasMayorista = 0; $CostoMayorista = 0;
            $CantidadIntitucionPrivida = 0; $VentasIntitucionPrivida = 0; $CostoIntitucionPrivida = 0;
            $CantidadCruzAzul = 0; $VentasCruzAzul = 0; $CostoCruzAzul = 0;
            $CantidaIntitucionPublica = 0; $VentasIntitucionPublica = 0; $CostoIntitucionPublica = 0;
            $CantidadTotal = 0; $VentasTotal = 0; $CostoTotal = 0;
            //TODO: AQUI EXCLUIR LAS COLUMNAS QUE NO QUIERES QUE SE MUESTRE EN EL REPORTE
            if (!in_array($titulo, array('FechaFinal', 'IS_CA','CALC_AVG'))) {
                $i = 3;

                $NameColumna = (strlen($titulo) <= 2) ? "Mes".$titulo : $titulo ;

                //ASIGNA LA LETRA A LA COLUMNA
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($columnIndex);
               
                $objPHPExcel->setActiveSheetIndex()->setCellValue($columnLetter . $rowIndex, $NameColumna);
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(15);
                
                //ASIGNA LOS VALORES A CADA UNA DE LAS CELDAS
                foreach ($RowReOrderPoint as $key) {
                    $objPHPExcel->setActiveSheetIndex()->setCellValue($columnLetter.$i, $key[$titulo]);
                    $CantidadFarmacia += $key['FARMACIA_CANTIDAD'];
                    $VentasFarmacia += $key['FARMACIA_VENTA'];
                    $CostoFarmacia += $key['FARMACIA_COSTO'];
                    $CantidadCadenaFarmacia += $key['CADENA_FARMACIA_CANTIDAD'];
                    $VentasCadenaFarmacia += $key['CADENA_FARMACIA_VENTA'];
                    $CostoCadenaFarmacia += $key['CADENA_FARMACIA_COSTO'];
                    $CantidadMayorista += $key['MAYORISTA_CANTIDAD'];
                    $VentasMayorista += $key['MAYORISTA_VENTA'];
                    $CostoMayorista += $key['MAYORISTA_COSTO'];
                    $CantidadIntitucionPrivida += $key['INSTITUCION_PRIVADA_CANTIDAD'];
                    $VentasIntitucionPrivida += $key['INSTITUCION_PRIVADA_VENTA'];
                    $CostoIntitucionPrivida += $key['INSTITUCION_PRIVADA_COSTO'];
                    $CantidadCruzAzul += $key['CRUZ_AZUL_CANTIDAD'];
                    $VentasCruzAzul += $key['CRUZ_AZUL_VENTA'];
                    $CostoCruzAzul += $key['CRUZ_AZUL_COSTO'];
                    $CantidaIntitucionPublica += $key['INSTITUCION_PUBLICA_CANTIDAD'];
                    $VentasIntitucionPublica += $key['INSTITUCION_PUBLICA_VENTA'];
                    $CostoIntitucionPublica += $key['INSTITUCION_PUBLICA_COSTO'];
                    $CantidadTotal += $key['TOTAL_VENTAS_PACK'];
                    $VentasTotal += $key['TOTAL_VENTAS_C$'];
                    $CostoTotal += $key['TOTAL_COSTOS_C$'];
                    $i++;
                }
                // TOTAL FARMACIA
                $objPHPExcel->setActiveSheetIndex()->setCellValue('D2', ''.$CantidadFarmacia);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('E2', ''.($VentasFarmacia/$CantidadFarmacia));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('F2', ''.$VentasFarmacia);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('G2', ''.$CostoFarmacia);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('H2', ''.($VentasFarmacia-$CostoFarmacia));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('I2', ''.(($VentasFarmacia-$CostoFarmacia)/$VentasFarmacia)*100);

                //TOTAL CADENA DE FARMACIA
                $objPHPExcel->setActiveSheetIndex()->setCellValue('J2', ''.$CantidadCadenaFarmacia);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('K2', ''.($VentasCadenaFarmacia/$CantidadCadenaFarmacia));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('L2', ''.$VentasCadenaFarmacia);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('M2', ''.$CostoCadenaFarmacia);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('N2', ''.($VentasCadenaFarmacia-$CostoCadenaFarmacia));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('O2', ''.(($VentasCadenaFarmacia-$CostoCadenaFarmacia)/$VentasCadenaFarmacia)*100);
                // TOTAL MAYORISTA
                $objPHPExcel->setActiveSheetIndex()->setCellValue('P2', ''.$CantidadMayorista);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('Q2', ''.($VentasMayorista/$CantidadMayorista));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('R2', ''.$VentasMayorista);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('S2', ''.$CostoMayorista);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('T2', ''.($VentasMayorista-$CostoMayorista));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('U2', ''.(($VentasMayorista-$CostoMayorista)/$VentasMayorista)*100);
                
                // TOTAL INSTITUCION PRIVADA
                $objPHPExcel->setActiveSheetIndex()->setCellValue('V2', ''.$CantidadIntitucionPrivida);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('W2', ''.($VentasIntitucionPrivida/$CantidadIntitucionPrivida));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('X2', ''.$VentasIntitucionPrivida);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('Y2', ''.$CostoIntitucionPrivida);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('Z2', ''.($VentasIntitucionPrivida-$CostoIntitucionPrivida));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AA2', ''.(($VentasIntitucionPrivida-$CostoIntitucionPrivida)/$VentasIntitucionPrivida)*100);
                
                // TOTAL CRUZ AZUL
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AB2', ''.$CantidadCruzAzul);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AC2', ''.($VentasCruzAzul/$CantidadCruzAzul));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AD2', ''.$VentasCruzAzul);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AE2', ''.$CostoCruzAzul);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AF2', ''.($VentasCruzAzul-$CostoCruzAzul));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AG2', ''.(($VentasCruzAzul-$CostoCruzAzul)/$VentasCruzAzul)*100);
        
                // TOTAL INSTITUCION PUBLICA
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AH2', ''.$CantidaIntitucionPublica);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AI2', ''.($VentasIntitucionPublica/$CantidaIntitucionPublica));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AJ2', ''.$VentasIntitucionPublica);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AK2', ''.$CostoIntitucionPublica);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AL2', ''.($VentasIntitucionPublica-$CostoIntitucionPublica));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AM2', ''.(($VentasIntitucionPublica-$CostoIntitucionPublica)/$VentasIntitucionPublica)*100);
                
                // TOTALES
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AN2', ''.$CantidadTotal);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AO2', ''.($VentasTotal/$CantidadTotal));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AP2', ''.$VentasTotal);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AQ2', ''.$CostoTotal);
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AR2', ''.($VentasTotal-$CostoTotal));
                $objPHPExcel->setActiveSheetIndex()->setCellValue('AS2', ''.(($VentasTotal-$CostoTotal)/$VentasTotal)*100);
                $columnIndex++;
            }
            
        }
        $ultimaColumnaLetra = PHPExcel_Cell::stringFromColumnIndex($columnIndex - 1);
        $i++;   
        
        //ANCHO DE CADA COLUMNAS
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(110);
        $startColumn = 'D';
        $endColumn = 'AS';
        
        for ($column = $startColumn; $column !== $endColumn; $column++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(30);
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension($endColumn)->setWidth(30);

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $ultimaColumnaLetra . '1')->applyFromArray($estiloTituloColumnas);

        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2:". $ultimaColumnaLetra .($i-1));

        //FORMATOS NUMERICOS
        $formatCode = '_-" "* #,##0.00_-;_-" "* #,##0.00_-;_-" "* "-"??_-;_-@_-';
        $objPHPExcel->getActiveSheet()->getStyle("D2:". $ultimaColumnaLetra .($i-1))->getNumberFormat()->setFormatCode($formatCode);

        /*$objPHPExcel->getActiveSheet()->getStyle("A1:C1")->applyFromArray([
            'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => '0000FF' 
            ]
        ]);*/

        $objPHPExcel->getActiveSheet()->getStyle("A1:C1")->getFill('')
                ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '1d5dec')
        ));

        $objPHPExcel->getActiveSheet()->getStyle("D1:I1")->getFill('')
                ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'FFFF00')
        ));

        $objPHPExcel->getActiveSheet()->getStyle("J1:O1")->getFill('')
                ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'FF6F1D')
        ));
        
        $objPHPExcel->getActiveSheet()->getStyle("P1:U1")->getFill('')
                ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'EE9C4')
        ));

        $objPHPExcel->getActiveSheet()->getStyle("V1:AA1")->getFill('')
                ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '1dec43')
        ));

        $objPHPExcel->getActiveSheet()->getStyle("AB1:AG1")->getFill('')
                ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '1d91ec')
        ));

        $objPHPExcel->getActiveSheet()->getStyle("AH1:AM1")->getFill('')
                ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '1dec43')
        ));

        $objPHPExcel->getActiveSheet()->getStyle("AN1:AS1")->getFill('')
            ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'FFFF00')
        ));

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte_por_canales.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }
}