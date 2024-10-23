<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;

class ReOrderPoint extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_gnet_reorder_lvl3";

    /**
     * Executes three stored procedures to calculate reorder points for articles.
     *
     * @return void
     */
    public static function CalcReorder()
    {
        $currentDate = date('Y-m-d');
        $startOfMonth = date('Y-m-01', strtotime($currentDate));

        $FechaIni   = date('Y-m-d 00:00:00.000', strtotime('-11 months', strtotime($startOfMonth)));
        $FechaEnd   = date('Y-m-d 00:00:00.000', strtotime($currentDate . ' -1 days'));
        $DiaActual  = (int) date('d', strtotime($FechaEnd)); 
        
        // Ejecutar el tercer procedimiento almacenado
        DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.sp_Calc_12_month_reorder_point ?, ?, ?", [$FechaIni, $FechaEnd, $DiaActual]);

        // Ejecutar el primer procedimiento almacenado
        DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.pr_calc_reorder_factura_linea ?, ?", [$FechaIni, $FechaEnd]);

        // Ejecutar el segundo procedimiento almacenado
        DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.pr_calc_reorder_factura_linea_ca ?", [$FechaEnd]);

        // Ejecutar el cuarto procedimiento almacenado        
        //DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.sp_categoria_articulo_canales");
        

    }

    public static function getArticulo() 
    {
        $array = [];
        $Articulos = ReOrderPoint::all();
        $NameMonths = ReOrderPoint::NameMonth($Articulos[0]->FechaFinal)  ;
        foreach ($Articulos as $key => $a) {
            $array[$key] = [
                "ARTICULO"                  => '<a href="#!" onclick="getDetalleArticulo('."'".$a->ARTICULO."'".', '."'".strtoupper($a->DESCRIPCION)."'".')" >'.$a->ARTICULO.'</a>',
                "DESCRIPCION"               => strtoupper($a->DESCRIPCION),
                "FABRICANTE"               => strtoupper($a->LABORATORIO),
                "VENCE_MENOS_IGUAL_12"      => number_format($a->VENCE_MENOS_IGUAL_12,2),
                "VENCE_MAS_IGUAL_7"         => number_format($a->VENCE_MAS_IGUAL_7,2),
                "LOTE_MAS_PROX_VENCER"      => date("d-m-Y", strtotime($a->LOTE_MAS_PROX_VENCER)),
                "EXIT_LOTE_PROX_VENCER"     => number_format($a->EXIT_LOTE_PROX_VENCER,2),
                "FECHA_ENTRADA_LOTE"        => date("d-m-Y", strtotime($a->FECHA_ENTRADA_LOTE)),
                "CANTIDAD_INGRESADA"        => number_format($a->CANTIDAD_INGRESADA,2),
                "LEADTIME"                  => $a->LEADTIME,
                "EJECUTADO_UND_YTD"         => number_format($a->EJECUTADO_UND_YTD,2),
                "VENTAS_YTD"                => number_format($a->VENTAS_YTD,2),
                "CONTRIBUCION_YTD"          => number_format($a->CONTRIBUCION,2),
                "DEMANDA_ANUAL_CA_NETA"     => number_format($a->DEMANDA_ANUAL_CA_NETA,2),
                "DEMANDA_ANUAL_CA_AJUSTADA" => number_format($a->DEMANDA_ANUAL_CA_AJUSTADA,2),
                "FACTOR"                    => number_format($a->FACTOR,2),
                "LIMITE_LOGISTICO_MEDIO"    => number_format($a->LIMITE_LOGISTICO_MEDIO,2),
                "CLASE"                     => $a->CLASE,
                "VALUACION"                 => $a->VALUACION,
                "CONTRIBUCION"              => number_format($a->CONTRIBUCION,2),
                "PEDIDO"                    => number_format($a->PEDIDO,2),
                "TRANSITO"                  => number_format($a->TRANSITO,2),
                "MOQ"                       => number_format($a->MOQ,0),
                "ESTIMACION_SOBRANTES_UND"  => number_format($a->ESTIMACION_SOBRANTES_UND,2),
                "REORDER1"                  => number_format($a->REORDER1,0),
                "REORDER"                   => number_format($a->REORDER,2),
                "CANTIDAD_ORDENAR"          => number_format($a->CANTIDAD_ORDENAR,0),
                "IS_CA"                     => $a->IS_CA,
                "ROTACION_CORTA"            => number_format($a->ROTACION_CORTA, 2),
                "ROTACION_MEDIA"            => number_format($a->ROTACION_MEDIA, 2),
                "ROTACION_LARGA"            => number_format($a->ROTACION_LARGA, 2),
                "ULTIMO_COSTO_USD"          => number_format($a->ULTIMO_COSTO_USD, 2),
                "COSTO_PROMEDIO_USD"        => number_format($a->COSTO_PROMEDIO_USD, 2),
                "COSTO_PROMEDIO_LOC"        => number_format($a->COSTO_PROMEDIO_LOC, 2),
                "UPDATED_AT"                => substr($a->FechaFinal, 0, 10),
                "FACTOR_STOCK_SEGURIDAD"    => number_format($a->FACTOR_STOCK_SEGURIDAD, 2),                
                "ROTACION_PREVISTA_EXISTENCIAS_VENCER" => number_format($a->ROTACION_PREVISTA_EXISTENCIAS_VENCER, 2),
                "TOTAL_UMK"                 => number_format($a->TOTAL_UMK, 2),
                "TOTAL_GP"                  => number_format($a->TOTAL_GP, 2),
                "TOTAL_DISP"                => number_format($a->TOTAL_DISP, 2),
                "VENTAS"                    => array_map(function($month, $value) use ($a) { 
                                            return [
                                                    "Mes"   => $month,
                                                    "Valor"  => isset($a->$value) && !empty($a->$value) ? (float) number_format($a->$value,2,".",""): 0
                                                    ];
                                            }, $NameMonths, range(1, 12)),
                "PROM_MESES_TOP"            => number_format($a->PROM_MESES_TOP, 0),

                'CANTIDAD_V2'               => isset($a->CANTIDAD_ORDENAR_AJUSTADA) ? number_format($a->CANTIDAD_ORDENAR_AJUSTADA,0) : '',
                'CLASE_V2'                  => isset($a->CLASE_AJUSTADAS) ? $a->CLASE_AJUSTADAS : '',

                'ALTURA'                    => isset($a->ALTURA) ? $a->ALTURA : '', 
                'LARGO'                     => isset($a->LARGO) ? $a->LARGO : '',  
                'ANCHO'                     => isset($a->ANCHO) ? $a->ANCHO : '', 
            ];
        }

        return $array;
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
    public static function getDataGrafica($Articulos,$Canal) {

        $array = array();

        $currentDate = date('Y-m-d');
        $startOfMonth = date('Y-m-01', strtotime($currentDate));

        $FechaEnd   = date('Y-m-d 00:00:00.000', strtotime($currentDate . ' -1 days'));
        $DiaActual  = (int) date('d', strtotime($FechaEnd)); 

        if ($Canal === 'Todos') {
            $Sales = ReOrderPoint::WHERE('ARTICULO',$Articulos)->first();
        } else {            
            DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.sp_calc_12_month_reorder_articulo ?, ?, ?", [$Canal, $Articulos, $DiaActual]);
            $Sales = ReOrderPointByArticulo::WHERE('ARTICULO',$Articulos)->first();
        }

        
        $NameMonths = ($Canal === 'TODOS') ? ReOrderPoint::NameMonth($Sales->FechaFinal) : ReOrderPoint::NameMonth($FechaEnd) ;


        $array = [
            'LEADTIME'                      => isset($Sales->LEADTIME) ? number_format($Sales->LEADTIME, 0, '.', '') : 0,
            'DEMANDA_ANUAL_CA_NETA'         => isset($Sales->DEMANDA_ANUAL_CA_NETA) ? number_format($Sales->DEMANDA_ANUAL_CA_NETA, 0, '.', '') : 0,
            'DEMANDA_ANUAL_CA_AJUSTADA'     => isset($Sales->DEMANDA_ANUAL_CA_AJUSTADA) ? number_format($Sales->DEMANDA_ANUAL_CA_AJUSTADA, 0, '.', '') : 0,
            'LIMITE_LOGISTICO_MEDIO'        => isset($Sales->LIMITE_LOGISTICO_MEDIO) ? number_format($Sales->LIMITE_LOGISTICO_MEDIO, 0, '.', '') : 0,
            'CONTRIBUCION'                  => isset($Sales->CONTRIBUCION) ? number_format($Sales->CONTRIBUCION, 0, '.', '') : 0,

            'REORDER1'                      => isset($Sales->REORDER1) ? number_format($Sales->REORDER1, 0, '.', '') : 0,
            'REORDER'                       => isset($Sales->REORDER) ? number_format($Sales->REORDER, 0, '.', '') : 0,
            'CANTIDAD_ORDENAR'              => isset($Sales->CANTIDAD_ORDENAR) ? number_format($Sales->CANTIDAD_ORDENAR, 0, '.', '') : 0,
            'MOQ'                           => isset($Sales->MOQ) ? number_format($Sales->MOQ, 0, '.', '') : 0,
            'PEDIDO'                        => isset($Sales->PEDIDO) ? number_format($Sales->PEDIDO, 0, '.', '') : 0,
            'TRANSITO'                      => isset($Sales->TRANSITO) ? number_format($Sales->TRANSITO, 0, '.', '') : 0,
            'CLASE'                         => isset($Sales->CLASE) ? $Sales->CLASE : '',

            'ROTACION_CORTA'                => isset($Sales->ROTACION_CORTA) ? bcadd(number_format($Sales->ROTACION_CORTA, 0), 5, 0) : 0,
            'ROTACION_MEDIA'                => isset($Sales->ROTACION_MEDIA) ? bcadd(number_format($Sales->ROTACION_MEDIA, 0), 5, 0) : 0, 
            'ROTACION_LARGA'                => isset($Sales->ROTACION_LARGA) ? bcadd(number_format($Sales->ROTACION_LARGA, 0), 5, 0) : 0,

            'COSTO_PROMEDIO_USD'            => isset($Sales->COSTO_PROMEDIO_USD) ? number_format($Sales->COSTO_PROMEDIO_USD, 0, '.', '') : 0,
            'ULTIMO_COSTO_USD'              => isset($Sales->ULTIMO_COSTO_USD) ? number_format($Sales->ULTIMO_COSTO_USD, 0, '.', '') : 0,
            "COSTO_PROMEDIO_LOC"            => isset($Sales->COSTO_PROMEDIO_LOC) ? number_format($Sales->COSTO_PROMEDIO_LOC, 2) : 0,
            'VENTAS_YTD'                    => isset($Sales->VENTAS_YTD) ? number_format($Sales->VENTAS_YTD, 0, '.', '') : 0,
            'CONTRIBUCION_YTD'              => isset($Sales->CONTRIBUCION_YTD) ? number_format($Sales->CONTRIBUCION_YTD, 0, '.', '') : 0,
            'EJECUTADO_UND_YTD'             => isset($Sales->EJECUTADO_UND_YTD) ? number_format($Sales->EJECUTADO_UND_YTD, 0, '.', '') : 0,
            "VENTAS"                        => array_map(function($month, $value) use ($Sales) { 
                                                return [
                                                        "Mes"   => $month,
                                                        "data"  => isset($Sales->$value) && !empty($Sales->$value) ? (float) number_format($Sales->$value,2,".",""): 0
                                                        ];
                                                }, $NameMonths, range(1, 12)),
            'CANTIDAD_V2'                   => isset($Sales->CANTIDAD_ORDENAR_AJUSTADA) ? $Sales->CANTIDAD_ORDENAR_AJUSTADA : '',
            'CLASE_V2'                      => isset($Sales->CLASE) ? $Sales->CLASE : '',                                    
        ];
        


        return $array;
    }

    public static function ExportToExcel() {
        $objPHPExcel = new PHPExcel();
        $tituloReporte = "";
        $titulosColumnas = array();
        $columnIndex = 0;
        $rowIndex = 1;

        $estiloTituloReporte = array(
            'font' => array(
            'name'      => 'Tahoma',
            'bold'      => true,
            'italic'    => false,
            'strike'    => false,
            'size'      => 14,
            'color'     => array(
                            'rgb' => '212121')
            ),
            'alignment' =>  array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation'   => 0,
                            'wrap'       => TRUE,
                            )
        );

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

        $RowReOrderPoint = ReOrderPoint::all();
        $titulosColumnas = array_keys(ReOrderPoint::first()->toArray());

    
        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', "Actualizado al : ". $RowReOrderPoint[0][ 'FechaFinal']);
        

        
        foreach ($titulosColumnas as $titulo) {
            //VALIDACION SI EL CAMPO ES ENTERO Y INCREMENTA A 1 PARA EL NUMERO DE MESES
            $titulo = (!is_string($titulo)) ? strval( $titulo + 1) : $titulo ;

            if (!in_array($titulo, array('FechaFinal', 'IS_CA','CALC_AVG','CONTRIBUCION'))) {
                $i = 2;

                $NameColumna = (strlen($titulo) <= 2) ? "Mes".$titulo : $titulo ;

                //ASIGNA LA LETRA A LA COLUMNA
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($columnIndex);
                
                $objPHPExcel->setActiveSheetIndex()->setCellValue($columnLetter . $rowIndex, $NameColumna);
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(15);

                //ASIGNA LOS VALORES A CADA UNA DE LAS CELDAS
                foreach ($RowReOrderPoint as $key) {
                    $objPHPExcel->setActiveSheetIndex()->setCellValue($columnLetter.$i,  $key[$titulo]);
                    $i++;
                }
                
                $columnIndex++;
            }
            
        }
        $ultimaColumnaLetra = PHPExcel_Cell::stringFromColumnIndex($columnIndex - 1);
    
        $i++;   

        //ANCHO DE CADA COLUMNAS
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(110);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $ultimaColumnaLetra . '1')->applyFromArray($estiloTituloColumnas);

        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2:". $ultimaColumnaLetra .($i-1));

        //FORMATOS NUMERICOS
        $formatCode = '_-" "* #,##0.00_-;_-" "* #,##0.00_-;_-" "* "-"??_-;_-@_-';
        $objPHPExcel->getActiveSheet()->getStyle("D2:". $ultimaColumnaLetra .($i-1))->getNumberFormat()->setFormatCode($formatCode);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReOrderPoint.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');


    }
}
