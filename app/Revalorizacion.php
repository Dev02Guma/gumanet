<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Revalorizacion extends Model
{
    /**
     * Catálogo de bodegas de la empresa en sesión (Unimark 1, Innova 4).
     */
    public static function getBodegas()
    {
        $tabla = (Session::get('company_id') == 4) ? 'PRODUCCION.dbo.INN_iweb_bodegas' : 'PRODUCCION.dbo.iweb_bodegas';

        $rows = DB::connection('sqlsrv')->select("SELECT DISTINCT T0.BODEGA, T0.NOMBRE FROM " . $tabla . " T0 ORDER BY T0.BODEGA");

        $Arry = [];
        foreach ($rows as $item) {
            $Arry[] = [
                "BODEGA" => trim($item->BODEGA),
                "NOMBRE" => trim($item->NOMBRE),
            ];
        }
        return $Arry;
    }

    public static function Filtrar($request)
    {
        $bodegas = is_array($request->bodegas) ? $request->bodegas : [];
        $data    = self::Generar($bodegas, $request->fecha);

        return [
            'REVALORIZACION' => self::esPorArticulo($request->agrupar) ? self::AgruparPorArticulo($data) : $data,
        ];
    }

    /**
     * Agrupación del reporte: 'ARTICULO' (default) o 'LOTE'.
     */
    public static function esPorArticulo($agrupar)
    {
        return strtoupper((string) $agrupar) !== 'LOTE';
    }

    /**
     * Junta los lotes de cada artículo por bodega: suma cantidad, costo y compra;
     * el costo unitario es el promedio ponderado (costo total / cantidad) y
     * CRM, orden de compra y proveedor se listan sin repetir, separados por coma.
     */
    public static function AgruparPorArticulo(array $data)
    {
        $listas = ['NUM_CRM', 'ORDEN_COMPRA', 'PROVEEDOR', 'NOMBRE_PROVEEDOR'];
        $grupos = [];
        foreach ($data as $item) {
            $key = $item['BODEGA'] . '|' . $item['ARTICULO'];
            if (!isset($grupos[$key])) {
                $grupos[$key] = [
                    "ARTICULO"         => $item['ARTICULO'],
                    "DESCRIPCION"      => $item['DESCRIPCION'],
                    "BODEGA"           => $item['BODEGA'],
                    "LOTES"            => 0,
                    "CANTIDAD"         => 0,
                    "UNIDAD_ALMACEN"   => $item['UNIDAD_ALMACEN'],
                    "COSTO_UNITARIO"   => 0,
                    "COSTO_TOTAL"      => 0,
                    "COMPRA_TOTAL"     => 0,
                    "NUM_CRM"          => [],
                    "ORDEN_COMPRA"     => [],
                    "PROVEEDOR"        => [],
                    "NOMBRE_PROVEEDOR" => [],
                ];
            }
            $grupos[$key]['LOTES']++;
            $grupos[$key]['CANTIDAD']     += $item['CANTIDAD'];
            $grupos[$key]['COSTO_TOTAL']  += $item['COSTO_TOTAL'];
            $grupos[$key]['COMPRA_TOTAL'] += $item['COMPRA_TOTAL'];
            foreach ($listas as $campo) {
                if ($item[$campo] !== '' && !in_array($item[$campo], $grupos[$key][$campo], true)) {
                    $grupos[$key][$campo][] = $item[$campo];
                }
            }
        }

        $Arry = [];
        foreach ($grupos as $g) {
            $g['COSTO_UNITARIO'] = $g['CANTIDAD'] != 0 ? $g['COSTO_TOTAL'] / $g['CANTIDAD'] : 0;
            foreach ($listas as $campo) {
                // CRM y OC en orden ascendente; proveedor y nombre quedan en el mismo orden entre si
                if (in_array($campo, ['NUM_CRM', 'ORDEN_COMPRA'])) {
                    natsort($g[$campo]);
                }
                $g[$campo] = implode(', ', $g[$campo]);
            }
            $Arry[] = $g;
        }
        return $Arry;
    }

    /**
     * Columnas del Excel según la agrupación: [titulo, campo, tipo, ancho].
     * tipo: txt (texto, conserva ceros), str (texto), num (2 decimales), num4 (4 decimales), int (entero)
     */
    private static function columnasExcel($porArticulo)
    {
        return [
            ['Artículo',         'ARTICULO',         'txt',  14],
            ['Descripción',      'DESCRIPCION',      'str',  45],
            $porArticulo ? ['Lotes', 'LOTES', 'int', 8] : ['Lote', 'LOTE', 'txt', 16],
            ['Cantidad',         'CANTIDAD',         'num',  13],
            ['Unidad',           'UNIDAD_ALMACEN',   'str',  9],
            ['Costo Unitario',   'COSTO_UNITARIO',   'num4', 15],
            ['Costo Total',      'COSTO_TOTAL',      'num',  16],
            ['Compra Total',     'COMPRA_TOTAL',     'num',  16],
            ['Num. CRM',         'NUM_CRM',          'txt',  $porArticulo ? 18 : 13],
            ['Orden Compra',     'ORDEN_COMPRA',     'txt',  $porArticulo ? 24 : 15],
            ['Proveedor',        'PROVEEDOR',        'txt',  12],
            ['Nombre Proveedor', 'NOMBRE_PROVEEDOR', 'str',  40],
        ];
    }

    /**
     * 1) Calcula la valoración al corte (sp_ActualizaValoracionInventario guarda el corte por lote)
     * 2) Genera el reporte desde ValoracionLote_Corte, solo con las bodegas seleccionadas.
     */
    public static function Generar(array $bodegas, $fecha)
    {
        $bodegas = array_values(array_filter(array_map('trim', $bodegas), 'strlen'));
        if (empty($bodegas) || !self::fechaValida($fecha)) {
            return [];
        }

        $corte = $fecha . ' 23:59:59';

        DB::connection('sqlsrv')->statement("SET NOCOUNT ON; EXEC PRODUCCION.dbo.sp_ActualizaValoracionInventario @FechaCorte = ?", [$corte]);

        $in = implode(',', array_fill(0, count($bodegas), '?'));

        // La tabla guarda varios cortes: filtrar por el que se acaba de calcular
        $rows = DB::connection('sqlsrv')->select("
            SELECT
                FECHA_CORTE,
                ARTICULO,
                DESCRIPCION,
                BODEGA,
                LOTE,
                CANTIDAD,
                UNIDAD_ALMACEN,
                COSTO_UNITARIO,
                COSTO_TOTAL,
                COMPRA_TOTAL,
                NUM_CRM,
                ORDEN_COMPRA,
                PROVEEDOR,
                NOMBRE_PROVEEDOR
            FROM PRODUCCION.dbo.ValoracionLote_Corte
            WHERE FECHA_CORTE = ?
              AND BODEGA IN (" . $in . ")
            ORDER BY ARTICULO, LOTE
        ", array_merge([$corte], $bodegas));

        $Arry = [];
        foreach ($rows as $item) {
            $Arry[] = [
                "ARTICULO"         => trim($item->ARTICULO),
                "DESCRIPCION"      => trim($item->DESCRIPCION),
                "BODEGA"           => trim($item->BODEGA),
                "LOTE"             => trim($item->LOTE),
                "CANTIDAD"         => (float) $item->CANTIDAD,
                "UNIDAD_ALMACEN"   => trim($item->UNIDAD_ALMACEN),
                "COSTO_UNITARIO"   => (float) $item->COSTO_UNITARIO,
                "COSTO_TOTAL"      => (float) $item->COSTO_TOTAL,
                "COMPRA_TOTAL"     => (float) $item->COMPRA_TOTAL,
                "NUM_CRM"          => trim($item->NUM_CRM),
                "ORDEN_COMPRA"     => trim($item->ORDEN_COMPRA),
                "PROVEEDOR"        => trim($item->PROVEEDOR),
                "NOMBRE_PROVEEDOR" => trim($item->NOMBRE_PROVEEDOR),
            ];
        }
        return $Arry;
    }

    private static function fechaValida($fecha)
    {
        $d = \DateTime::createFromFormat('Y-m-d', (string) $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }

    /**
     * Libro de Excel presentable: logo, título, filtros, encabezado corporativo, totales y formato de impresión.
     */
    public static function Excel(array $data, $fecha, array $nombres, $logoPath, $porArticulo = false)
    {
        $columnas = self::columnasExcel($porArticulo);
        $letra    = function ($i) { return \PHPExcel_Cell::stringFromColumnIndex($i); };
        $ultCol   = $letra(count($columnas) - 1);
        $filaHead = 6;
        $azul     = '002D72';

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator('GUMA@NET')->setTitle('Revalorización de Inventario');
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Revalorizacion');
        $sheet->setShowGridlines(false);

        // Encabezado: logo + título + filtros
        $logo = new \PHPExcel_Worksheet_Drawing();
        $logo->setName('Unimark');
        $logo->setPath($logoPath);
        $logo->setHeight(55);
        $logo->setCoordinates('A1');
        $logo->setOffsetX(5);
        $logo->setOffsetY(5);
        $logo->setWorksheet($sheet);

        $sheet->setCellValue('C1', 'REVALORIZACIÓN DE INVENTARIO');
        $sheet->setCellValue('C2', 'Fecha de corte: ' . ($fecha ? date('d/m/Y', strtotime($fecha)) : '')
            . '   |   Agrupado por: ' . ($porArticulo ? 'Artículo' : 'Lote'));
        $sheet->setCellValue('C3', 'Bodegas: ' . implode(', ', $nombres));
        $sheet->setCellValue('C4', 'Generado: ' . date('d/m/Y h:i A'));
        foreach (['1', '2', '3', '4'] as $f) {
            $sheet->mergeCells('C' . $f . ':' . $ultCol . $f);
        }
        $sheet->getStyle('C1')->applyFromArray(['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $azul]]]);
        $sheet->getStyle('C2:C4')->applyFromArray(['font' => ['size' => 10, 'color' => ['rgb' => '555555']]]);
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Encabezado de la tabla
        foreach ($columnas as $i => $c) {
            $sheet->setCellValueByColumnAndRow($i, $filaHead, $c[0]);
        }
        $sheet->getStyle('A' . $filaHead . ':' . $ultCol . $filaHead)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => $azul]],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true,
            ],
        ]);
        $sheet->getRowDimension($filaHead)->setRowHeight(30);

        // Detalle (códigos como texto para no perder ceros a la izquierda)
        $texto = \PHPExcel_Cell_DataType::TYPE_STRING;
        $row = $filaHead + 1;
        foreach ($data as $item) {
            foreach ($columnas as $i => $c) {
                if ($c[2] === 'txt') {
                    $sheet->setCellValueExplicitByColumnAndRow($i, $row, $item[$c[1]], $texto);
                } else {
                    $sheet->setCellValueByColumnAndRow($i, $row, $item[$c[1]]);
                }
            }
            $row++;
        }
        $ultFila = max($row - 1, $filaHead + 1);

        $formatos = ['num' => '#,##0.00', 'num4' => '#,##0.0000', 'int' => '#,##0'];
        $centrar  = ['LOTE', 'LOTES', 'UNIDAD_ALMACEN'];
        $colTotal = [];
        foreach ($columnas as $i => $c) {
            $rango = $letra($i) . ($filaHead + 1) . ':' . $letra($i) . $ultFila;
            if (isset($formatos[$c[2]])) {
                $sheet->getStyle($rango)->getNumberFormat()->setFormatCode($formatos[$c[2]]);
            }
            if (in_array($c[1], $centrar)) {
                $sheet->getStyle($rango)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            if (in_array($c[1], ['COSTO_TOTAL', 'COMPRA_TOTAL'])) {
                $colTotal[$c[1]] = $letra($i);
            }
        }

        // Totales como valor: PHPExcel (abandonado) falla al escribir fórmulas en PHP 7.4+
        $filaTot = $ultFila + 1;
        $sheet->setCellValue('A' . $filaTot, 'TOTAL');
        $sheet->mergeCells('A' . $filaTot . ':' . $letra(array_search('COSTO_TOTAL', array_column($columnas, 1)) - 1) . $filaTot);
        foreach ($colTotal as $campo => $c) {
            $sheet->setCellValue($c . $filaTot, array_sum(array_column($data, $campo)));
            $sheet->getStyle($c . $filaTot)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->getStyle('A' . $filaTot . ':' . $ultCol . $filaTot)->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'E9ECEF']],
            'borders' => ['top' => ['style' => \PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => ['rgb' => $azul]]],
        ]);
        $sheet->getStyle('A' . $filaTot)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('A' . $filaHead . ':' . $ultCol . $filaTot)->applyFromArray([
            'borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => 'D0D5DD']]],
        ]);

        // Anchos, paneles fijos e impresión (sin autofiltro: PHPExcel falla al escribirlo en PHP 7.4+)
        foreach ($columnas as $i => $c) {
            $sheet->getColumnDimension($letra($i))->setWidth($c[3]);
        }
        $sheet->freezePane('A' . ($filaHead + 1));

        $sheet->getPageSetup()
            ->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setRowsToRepeatAtTopByStartAndEnd($filaHead, $filaHead);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.3)->setRight(0.3);
        $sheet->getHeaderFooter()->setOddFooter('&LRevalorización de Inventario&RPágina &P de &N');

        return $objPHPExcel;
    }
}
