<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReOrderPoint2 extends Model
{
    /**
     * Decisión por producto (política de reorden v2.1).
     * FASE UI: datos de prueba sacados del Excel Contribucion_Reorden_2026-10-26.xlsx;
     * en las siguientes fases se reemplaza por el cálculo real con la misma estructura (META, HEADERS, DATA).
     */
    public static function getData()
    {
        $archivo = storage_path('app/reorder/decision_x_producto_demo.json');

        if (!file_exists($archivo)) {
            return ['META' => null, 'HEADERS' => [], 'DATA' => []];
        }

        return json_decode(file_get_contents($archivo), true);
    }

    /**
     * Columnas de la hoja Decision_x_Producto en el mismo orden del Excel original:
     * [campo, ancho, formato, centrado]
     */
    private static function columnasExcel()
    {
        $n  = '#,##0';
        $p  = '0.0%';
        $c0 = '"C$ "#,##0';
        $d  = 'dd/mm/yyyy';

        return [
            ['RANK', 5, null, true],            ['ARTICULO', 10, null, false],      ['DESCRIPCION', 54, null, false],
            ['FABRICANTE', 15, null, false],    ['PRESENTACION', 11, null, false],  ['CAT_ANTERIOR', 9, null, false],
            ['CAT_ACTUAL', 9, null, true],      ['CAT_CAMBIO', 4, null, true],      ['CAT_12OCT', 8, null, true],
            ['PAR_HERMANA', 12, null, false],   ['INVENTARIO', 10, $n, false],      ['PEDIDO', 8, $n, false],
            ['TRANSITO', 9, $n, false],         ['TOTAL_DISP', 10, $n, false],
            ['M01', 7, $n, false], ['M02', 7, $n, false], ['M03', 7, $n, false], ['M04', 7, $n, false], ['M05', 7, $n, false],
            ['M06', 7, $n, false], ['M07', 7, $n, false], ['M08', 7, $n, false], ['M09', 7, $n, false], ['M10', 7, $n, false],
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
        ];
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
        $columnas = self::columnasExcel();
        $letra    = function ($i) { return \PHPExcel_Cell::stringFromColumnIndex($i); };
        $ultCol   = $letra(count($columnas) - 1);
        $idx      = [];
        foreach ($columnas as $i => $c) {
            $idx[$c[0]] = $i;
        }
        $col = function ($campo) use ($letra, $idx) { return $letra($idx[$campo]); };

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator('GUMA@NET')->setTitle('Decision_x_Producto');
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Decision_x_Producto');

        $borde = ['borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
        $relleno = function ($rango, $rgb) use ($sheet) {
            $sheet->getStyle($rango)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($rgb);
        };

        // Filas 1-2: título y nota de la política
        $sheet->setCellValue('A1', $meta['TITULO'] ?? 'ANÁLISIS DE DECISIÓN POR PRODUCTO');
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
}
