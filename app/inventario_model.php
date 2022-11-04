<?php
namespace App;
use App;
use App\User;
use App\Company;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style;
use PHPExcel_Style_Border;
use Illuminate\Database\Eloquent\Model;
use App\metas_model;
use App\Models;
use App\tbl_temporal;
use DataTables;
use DB;
use Illuminate\Http\Request;
class inventario_model extends Model {
    
    public static function getArticulos() {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        $anio = date('Y');
        $getMonth  = date('n');
        //$anio = intval($anio) - 1;


        switch ($company_user) {
            case '1':
                $sql_exec = "SELECT T0.*,T1.SUM_ANUAL,T1.AVG_ANUAL,T1.AVG_3M,T1.COUNT_MONTH FROM iweb_articulos T0 LEFT JOIN gnet_inventario_promedios_anuales_umk T1 ON T0.ARTICULO = T1.ARTICULO where T0.ARTICULO NOT LIKE 'VU%'";

                $qSKU = "SELECT
                                    T1.ARTICULO,
                                    T1.DESCRIPCION,
                                    T0.CANT_DISPONIBLE,
                                    T1.UNIDAD_ALMACEN
                            FROM
                                iweb_bodegas T0
                                INNER JOIN iweb_articulos T1 ON T0.ARTICULO = T1.ARTICULO
                            WHERE
                                T0.ARTICULO LIKE 'VU%'";
                $qResult = $sql_server->fetchArray( $qSKU , SQLSRV_FETCH_ASSOC);

                $sql_vent_art = "SELECT
                                ARTICULO,
                                DESCRIPCION,
                                SUM(Cantidad) AS CANTIDAD,
                                SUM([P. Unitario] * Cantidad) AS VENTA,
                                (SELECT ISNULL(SUM ( T1.Cantidad ), 0) AS CANTIDAD FROM Softland.dbo.VtasTotal_UMK T1 ( nolock ) WHERE T1.[Año] = YEAR(GETDATE()) AND T1.nMes= MONTH(GETDATE()) AND T1.ARTICULO=T0.ARTICULO and T1.Ruta NOT IN('F01', 'F12') AND T1.[P. Unitario] > 0  ) AS VstMesActual,
	                            (SELECT ISNULL(SUM ( T1.Cantidad ), 0) AS CANTIDAD FROM Softland.dbo.VtasTotal_UMK T1 ( nolock ) WHERE T1.[Año] = YEAR(GETDATE()) AND T1.ARTICULO=T0.ARTICULO and T1.Ruta NOT IN('F01', 'F12') AND T1.[P. Unitario] > 0 )  AS VstAnnoActual 
                                FROM
                                Softland.dbo.VtasTotal_UMK T0 (nolock)
                                WHERE [Año] = ".$anio." 
                                AND [P. Unitario] > 0   AND
                                Ruta NOT IN('F01', 'F12')
                                GROUP BY ARTICULO, DESCRIPCION";
                break;
            case '2':                
                $sql_exec = "SELECT T0.*,T1.SUM_ANUAL,T1.AVG_ANUAL,T1.AVG_3M,T1.COUNT_MONTH FROM gp_iweb_articulos T0 LEFT JOIN gnet_inventario_promedios_anuales_gup T1 ON T0.ARTICULO = T1.ARTICULO ";
                $sql_vent_art = "SELECT
                                ARTICULO,
                                DESCRIPCION,
                                CAST (SUM(Cantidad) AS FLOAT) AS CANTIDAD,
                                CAST (
                                    SUM ([P. Unitario] * Cantidad) AS FLOAT
                                ) AS VENTA,
                                (SELECT ISNULL(CAST ( SUM ( T1.Cantidad ) AS FLOAT ), 0) AS CANTIDAD FROM Softland.dbo.GP_VtasTotal_UMK T1 ( nolock ) WHERE T1.[Año] = YEAR(GETDATE()) AND T1.nMes= MONTH(GETDATE()) AND T1.ARTICULO=T0.ARTICULO AND T1.[P. Unitario] > 0  ) AS VstMesActual,
	                            (SELECT ISNULL(CAST ( SUM ( T1.Cantidad ) AS FLOAT ), 0) AS CANTIDAD FROM Softland.dbo.GP_VtasTotal_UMK T1 ( nolock ) WHERE T1.[Año] = YEAR(GETDATE()) AND T1.ARTICULO=T0.ARTICULO AND T1.[P. Unitario] > 0 )  AS VstAnnoActual 
                            FROM
                                Softland.dbo.GP_VtasTotal_UMK T0 (nolock)
                            WHERE
                                [Año] = ".$anio."
                            AND [P. Unitario] > 0
                            GROUP BY
                                ARTICULO,
                                DESCRIPCION";
                                $qResult =[];

                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = "SELECT T0.*,T1.SUM_ANUAL,T1.AVG_ANUAL,T1.AVG_3M,T1.COUNT_MONTH FROM inn_iweb_articulos T0 LEFT JOIN gnet_inventario_promedios_anuales_inn T1 ON T0.ARTICULO = T1.ARTICULO ";
                $sql_vent_art = "SELECT
                                ARTICULO,
                                DESCRIPCION,
                                CAST (SUM(Cantidad) AS FLOAT) AS CANTIDAD,
                                CAST (
                                    SUM ([P. Unitario] * Cantidad) AS FLOAT
                                ) AS VENTA,
                                (SELECT ISNULL(CAST ( SUM ( T1.Cantidad ) AS FLOAT ), 0) AS CANTIDAD FROM Softland.dbo.INN_VtasTotal_UMK T1 ( nolock ) WHERE T1.[Año] = YEAR(GETDATE()) AND T1.nMes= MONTH(GETDATE()) AND T1.ARTICULO=T0.ARTICULO AND T1.[P. Unitario] > 0  ) AS VstMesActual,
	                            (SELECT ISNULL(CAST ( SUM ( T1.Cantidad ) AS FLOAT ), 0) AS CANTIDAD FROM Softland.dbo.INN_VtasTotal_UMK T1 ( nolock ) WHERE T1.[Año] = YEAR(GETDATE()) AND T1.ARTICULO=T0.ARTICULO AND T1.[P. Unitario] > 0 )  AS VstAnnoActual 
                            FROM
                                Softland.dbo.INN_VtasTotal_UMK T0 (nolock)
                            WHERE
                                [Año] = ".$anio."
                            AND [P. Unitario] > 0
                            GROUP BY
                                ARTICULO,
                                DESCRIPCION";
                                $qResult =[];
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = array();
        $i=0;
        

        $query1 = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);
        $query_vent_art = $sql_server->fetchArray($sql_vent_art, SQLSRV_FETCH_ASSOC);
        
        tbl_temporal::truncate()->insert($query_vent_art);
        
        foreach ($query1 as $key) {

            $desc_art = inventario_model::clean($key['DESCRIPCION']);

            $Stat_Articulos = tbl_temporal::where('articulo', $key['ARTICULO'])->get()->first();

            if ($Stat_Articulos) {
                $cantidad = $Stat_Articulos->cantidad;
                $vst_mes_Actual = $Stat_Articulos->VstMesActual;
                $vst_anno_Actual = $Stat_Articulos->VstAnnoActual;
            } else {
                $cantidad = 0;
                $vst_mes_Actual = 0;
                $vst_anno_Actual = 0;
            }

            
            $promedio =   ( $cantidad>0 )?( $cantidad / 12 ):0;


            //$vst_mes_Actual = ( $vst_mes_Actual['VstMesActual']=='' )?0:$vst_mes_Actual['VstMesActual'];
            //$vst_anno_Actual = ( $vst_anno_Actual['VstAnnoActual']=='' )?0:$vst_anno_Actual['VstAnnoActual'];
            
            $MesInventario = '0.00';

            $PromedioActual = number_format(($vst_anno_Actual / $getMonth), 2,".","");
            $Existencia =  number_format($key['total'], 2,".","");

            $MesInventario = ($key['total'] > 0.10 && $PromedioActual > 0.10) ? $Existencia   / $PromedioActual : "0.00" ;

            $query[$i]['ARTICULO']          = '<a href="#!" onclick="getDetalleArticulo('."'".$key['ARTICULO']."'".', '."'".$desc_art."'".')" >'.$key['ARTICULO'].'</a>';
            $query[$i]['ARTICULO_']         = $key['ARTICULO'];
            $query[$i]['CLASE_TERAPEUTICA'] = $key['CLASE_TERAPEUTICA'];
            $query[$i]['DESCRIPCION']       = $key['DESCRIPCION'];
            $query[$i]['total']             = number_format($key['total'], 2);
            $query[$i]['und']               = number_format($key['UNIDADES'], 2);
            $query[$i]['LABORATORIO']       = $key['LABORATORIO'];
            $query[$i]['UNIDAD_ALMACEN']    = $key['UNIDAD_ALMACEN'];
            $query[$i]['006']               = $key['006'];
            $query[$i]['005']               = $key['005'];
            $query[$i]['PUNTOS']            = $key['PUNTOS'];
            $query[$i]['PRECIO_FARMACIA']   = $key['PRECIO_FARMACIA'];
            $query[$i]['EMP']               = $company_user;
            $query[$i]['PROMEDIO_VENTA']    = number_format($promedio, 2);
            $query[$i]['CANT_ANIO_PAS']     = number_format($cantidad, 2);
            $query[$i]['VST_MES_ACTUAL']    = number_format($vst_mes_Actual, 2);
            $query[$i]['PROM_VST_ANUAL']    = number_format(($vst_anno_Actual / $getMonth), 2);
            $query[$i]['VST_ANNO_ACTUAL']   = number_format($vst_anno_Actual, 2);
            $query[$i]['MESES_INVENTARIO']  = number_format($MesInventario,2);
            $query[$i]['SUM_ANUAL']         = number_format($key['SUM_ANUAL'],2);
            $query[$i]['AVG_ANUAL']         = number_format($key['AVG_ANUAL'],2);
            $query[$i]['AVG_3M']            = number_format($key['AVG_3M'],2);
            $query[$i]['COUNT_MONTH']       = number_format($key['COUNT_MONTH'],0);

            $i++;
        }

        foreach ($qResult as $key) {

            $query[$i]['ARTICULO']          = '<a href="#!" onclick="getDetalleArticulo('."'".$key['ARTICULO']."'".', '."'".$key['DESCRIPCION']."'".')" >'.$key['ARTICULO'].'</a>';
            $query[$i]['ARTICULO_']         = $key['ARTICULO'];
            $query[$i]['CLASE_TERAPEUTICA'] = "-";
            $query[$i]['DESCRIPCION']       = $key['DESCRIPCION'];
            $query[$i]['total']             = number_format($key['CANT_DISPONIBLE'], 2);
            $query[$i]['und']               = number_format(0, 2);
            $query[$i]['LABORATORIO']       = "-";
            $query[$i]['UNIDAD_ALMACEN']    = $key['UNIDAD_ALMACEN'];
            $query[$i]['006']               = "-";
            $query[$i]['005']               = "-";
            $query[$i]['PUNTOS']            = "";
            $query[$i]['PRECIO_FARMACIA']   = "";
            $query[$i]['EMP']               = "";
            $query[$i]['PROMEDIO_VENTA']    = number_format(0, 2);
            $query[$i]['CANT_ANIO_PAS']     = number_format(0, 2);

            $query[$i]['VST_MES_ACTUAL']    = number_format(0, 2);
            $query[$i]['PROM_VST_ANUAL']    = number_format(0, 2);
            $query[$i]['VST_ANNO_ACTUAL']   = number_format(0, 2);
            $query[$i]['MESES_INVENTARIO']  = number_format(0, 2);
            $query[$i]['SUM_ANUAL']         = "";
            $query[$i]['AVG_ANUAL']         = "";
            $query[$i]['AVG_3M']            = "";
            $query[$i]['COUNT_MONTH']       = "";

            $i++;
        }



        $sql_server->close();        

        return $query;
    }

    public static function invenVencidos() {
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        $Unidad ='';
        $jsonResulto = array();
        
        switch ($company_user) {
            case '1':
                $Unidad ='umk';
                break;
            case '2':
                $Unidad ='guma';
                break;
            case '3':
                return false;
                break;
            case '4':
                return false;
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $sql_exec="SELECT
        T0.ARTICULO,
        T0.DESCRIPCION,
        T1.LOTE,
        T1.CANT_DISPONIBLE,
        (SELECT T2.COSTO_PROM_LOC FROM Softland.".$Unidad.".ARTICULO T2 WHERE T2.ARTICULO= T0.ARTICULO) AS COSTO_PROM_LOC,
        (SELECT T3.COSTO_ULT_LOC FROM Softland.".$Unidad.".ARTICULO T3 WHERE T3.ARTICULO= T0.ARTICULO ) AS COSTO_ULT_LOC,
        (SELECT T4.FECHA_VENCIMIENTO FROM Softland.".$Unidad.".LOTE T4 WHERE T4.ARTICULO = T0.ARTICULO AND T4.LOTE = T1.LOTE ) AS FECHA_VENCIMIENTO
    FROM
        Softland.umk.ARTICULO T0  
        INNER JOIN Softland.".$Unidad.".EXISTENCIA_LOTE T1 ON T0.ARTICULO = T1.ARTICULO
        
    WHERE
        (LEN(T0.ARTICULO) <= 8) AND (T0.ACTIVO = 'S') AND (LEN(T0.ARTICULO) > 7) and T0.ARTICULO LIKE '1%' AND T1.CANT_DISPONIBLE > 0 AND T1.BODEGA = '004' 
        GROUP BY 
        T0.ARTICULO,
        T0.DESCRIPCION,
        T1.LOTE,
        T1.CANT_DISPONIBLE";

        //dd($sql_exec);
        $i=0;

        $qInvetario = $sql_server->fetchArray( $sql_exec ,SQLSRV_FETCH_ASSOC);
        foreach ($qInvetario as $key) {
            $jsonResulto[$i]['ARTICULO']                  = $key['ARTICULO'];
            $jsonResulto[$i]['DESCRIPCION']               = $key['DESCRIPCION'];
            $jsonResulto[$i]['LOTE']                      = $key['LOTE'];
            $jsonResulto[$i]['CANT_DISPONIBLE']           = number_format($key['CANT_DISPONIBLE'], 2);
            $jsonResulto[$i]['FECHA_VENCIMIENTO']         = $key['FECHA_VENCIMIENTO']->format('d/m/Y');;
            $jsonResulto[$i]['COSTO_PROM_LOC']            = number_format($key['COSTO_PROM_LOC'], 2);
            $jsonResulto[$i]['COSTO_ULT_LOC']             = number_format($key['COSTO_ULT_LOC'], 2);
            $i++;
        }
        $sql_server->close();

        return $jsonResulto;
    }

    public static function getInventarioCompleto() {
        $sql_server = new \sql_server();        
       
        $sql_exec = "SELECT T0.ARTICULO, T0.DESCRIPCION,T0.UNIDAD, SUM(T0.TOTAL) TOTAL FROM inventario_totalizado T0 WHERE T0.TOTAL > 0 GROUP BY T0.ARTICULO, T0.DESCRIPCION,T0.UNIDAD";
        $query = array();
        $i=0;

        $query1 = $sql_server->fetchArray( $sql_exec ,SQLSRV_FETCH_ASSOC);
        foreach ($query1 as $key) {
            $query[$i]["DETALLE"]            = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
            $query[$i]['ARTICULO']                  = $key['ARTICULO'];
            $query[$i]['DESCRIPCION']               = $key['DESCRIPCION'];
            $query[$i]['UNIDAD']               = $key['UNIDAD'];
            $query[$i]['CANT_DISPONIBLE']           = number_format($key['TOTAL'], 2);
            $i++;
        }
        $sql_server->close();

        return $query;
    }

    public static function getInventarioTotalizado() {
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        
        switch ($company_user) {
            case '1':
                $sql_exec = "SELECT * FROM TOTAL_INVENTARIO";
                break;
            case '2':
                return false;
                break;
            case '3':
                return false;
                break;
            case '4':
                return false;
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = array();
        $i=0;

        $query1 = $sql_server->fetchArray( $sql_exec ,SQLSRV_FETCH_ASSOC);
        foreach ($query1 as $key) {
            $query[$i]['ARTICULO']        = $key['ARTICULO'];            
            $query[$i]['DESCRIPCION']     = $key['DESCRIPCION'];
            $query[$i]['LABORATORIO']     = $key['LABORATORIO'];
            $query[$i]['UNIDAD_MEDIDA']   = $key['UNIDAD_MEDIDA'];
            $query[$i]['B_UMK']           = number_format($key['Bodega_Unimark'], 2);
            $query[$i]['B_INV']           = number_format($key['Bodega_Innova'], 2);
            $i++;
        }
        $sql_server->close();        

        return $query;
    }

    public static function descargarInventario($tipo, $valor) {
        $objPHPExcel = new PHPExcel();
        $tituloReporte = "";
        $titulosColumnas = array();

        $getMonth  = date('n');

        

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

        $right = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

        $left = array(
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

        switch ($tipo) {
            case 'inventario':
                $temp = inventario_model::getArticulos();
                
                $tituloReporte = "INVENTARIO DE ARTICULOS ACTUALIZADOS HASTA ".date('d/m/Y');
                $titulosColumnas = array(
                    'ARTICULO',
                    'DESCRIPCION',
                    'UNIDAD',
                    'CANTI. DISP B002 ',
                    'TOTAL UNITS/ MES',
                    'TOTAL UNITS/ 2022',
                    'PROM. UNITS/MES 2021',
                    'TOTAL UNITS. 2021',
                    'MESES INVENTARIOS',
                    'Nº MESES',
                    'TOTAL VST. ANUAL',
                    'PROM. VST. ANUAL',
                    'PROM. 3M. MAS ALTO'
                );

                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:L1');

                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1',$tituloReporte)
                ->setCellValue('A3',  $titulosColumnas[0])
                ->setCellValue('B3',  $titulosColumnas[1])
                ->setCellValue('C3',  $titulosColumnas[2])
                ->setCellValue('D3',  $titulosColumnas[3])
                ->setCellValue('E3',  $titulosColumnas[4])
                ->setCellValue('F3',  $titulosColumnas[5])
                ->setCellValue('G3',  $titulosColumnas[6])
                ->setCellValue('H3',  $titulosColumnas[7])
                ->setCellValue('I3',  $titulosColumnas[8])
                ->setCellValue('J3',  $titulosColumnas[9])
                ->setCellValue('K3',  $titulosColumnas[10])
                ->setCellValue('L3',  $titulosColumnas[11])
                ->setCellValue('M3',  $titulosColumnas[12]);
                
                $i=4;

                foreach ($temp as $key) {
                    $MesInventario = 0;         

                    //SE OPTIMIZO A HACER UNA SOLA PETICION, YA QUE SE REALIZABAN MULTIPLES PETICIONES Y NO RETORNABA INFORMACION
                    $Stat_Articulos = tbl_temporal::where('articulo', $key['ARTICULO_'])->get()->first();

                    if ($Stat_Articulos) {
                        $cantidad = $Stat_Articulos->cantidad;
                        $vst_mes_Actual = $Stat_Articulos->VstMesActual;
                        $vst_anno_Actual = $Stat_Articulos->VstAnnoActual;
                    } else {
                        $cantidad = 0;
                        $vst_mes_Actual = 0;
                        $vst_anno_Actual = 0;
                    }
        
                    $promedio =   ( $cantidad > 0 ) ? ( $cantidad / 12 ) : 0;


                    //SE USA SOLO PARA LIMPIAR DE COMAS LOS CARACTERES QUE ESTAN DENTRO DE EL VALOR TOTAL
                    $Existencia = str_replace(",","",$key['total']);
                    $Existencia = floatval($Existencia);
                    
                    $PromedioActual = number_format(($vst_anno_Actual / $getMonth), 2,".","");


                    //CALCULO DE MESES RESTANTE QUE QUEDAN DE INVENTARIO, EN BASE AL PROMEDIO DE VENTA DE AÑO ACTUAL
                    $MesInventario = ($key['total'] > 0.10 && $PromedioActual > 0.10) ? $Existencia  / $PromedioActual : "0.00" ;

                    $MesInventario = number_format($MesInventario , 2,".","");
                    
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,  $key['ARTICULO_'])
                    ->setCellValue('B'.$i,  $key['DESCRIPCION'])
                    ->setCellValue('C'.$i,  $key['UNIDAD_ALMACEN'])
                    ->setCellValue('D'.$i,  $key['total'])
                    ->setCellValue('E'.$i,  number_format($vst_mes_Actual))
                    ->setCellValue('F'.$i,  number_format($vst_anno_Actual))
                    ->setCellValue('G'.$i,  number_format($promedio))
                    ->setCellValue('H'.$i,  number_format($cantidad))
                    ->setCellValue('I'.$i,  $MesInventario)
                    ->setCellValue('J'.$i,  $key['COUNT_MONTH'])
                    ->setCellValue('L'.$i,  $key['SUM_ANUAL'])
                    ->setCellValue('L'.$i,  $key['AVG_ANUAL'])
                    ->setCellValue('M'.$i,  $key['AVG_3M']);
                    $i++;
                    
                }

                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
                
                $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($estiloTituloColumnas);      
                $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:M".($i-1));
                $objPHPExcel->getActiveSheet()->getStyle("C4:M".($i-1))->applyFromArray($right);

                break;
            case 'vencimiento':
                $temp = inventario_model::dataLiquidacionMeses($valor);

                $tituloReporte = ($valor==6)?("VENCIMIENTO A 6 MESES HASTA ".date('d/m/Y')):("VENCIMIENTO A 12 MESES HASTA ".date('d/m/Y'));

                $titulosColumnas = array('ARTICULO', 'DESCRIPCION', 'DIAS PARA VENCERSE', 'CANTIDAD DISPONIBLE', 'FECHA VENCE', 'LOTE', 'BODEGA', 'TOTAL UNITS 2020', 'PROM. UNITS/MES 2020', 'MESES DE INVENTARIO','COSTO PROM.','COSTO ULT.','COSTO TOTAL');

                $objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells('A1:J1');

                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1',$tituloReporte)
                ->setCellValue('A3',  $titulosColumnas[0])
                ->setCellValue('B3',  $titulosColumnas[1])
                ->setCellValue('C3',  $titulosColumnas[2])
                ->setCellValue('D3',  $titulosColumnas[3])
                ->setCellValue('E3',  $titulosColumnas[4])
                ->setCellValue('F3',  $titulosColumnas[5])
                ->setCellValue('G3',  $titulosColumnas[6])
                ->setCellValue('H3',  $titulosColumnas[7])
                ->setCellValue('I3',  $titulosColumnas[8])
                ->setCellValue('J3',  $titulosColumnas[9])
                ->setCellValue('K3',  $titulosColumnas[10])
                ->setCellValue('L3',  $titulosColumnas[11])
                ->setCellValue('M3',  $titulosColumnas[12]);


                
                
                $i=4;

                foreach ($temp as $key) {
                    
                    $cantidad = tbl_temporal::where('articulo', $key['ARTICULO'])->select('cantidad')->first();
                    $cantidad = ( $cantidad['cantidad']=='' )?0:$cantidad['cantidad'];

                    $totalExistencia = $key['CANT_DISPONIBLE2'];
                    $promedio =   ( $cantidad>0 )?( $cantidad / 12 ):0;
                    $tempoEstimado = ($promedio>0)?($totalExistencia / $promedio):0;


                
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,  $key['ARTICULO'])
                    ->setCellValue('B'.$i,  $key['DESCRIPCION'])
                    ->setCellValue('C'.$i,  $key['DIAS_VENCIMIENTO'])
                    ->setCellValue('D'.$i,  $key['CANT_DISPONIBLE2'])
                    ->setCellValue('E'.$i,  $key['F_VENCIMIENTO'])
                    ->setCellValue('F'.$i,  $key['LOTE'])
                    ->setCellValue('G'.$i,  $key['BODEGA'])
                    ->setCellValue('H'.$i,  number_format($cantidad, 2))
                    ->setCellValue('I'.$i,  number_format($promedio, 2))
                    ->setCellValue('J'.$i,  number_format($tempoEstimado, 2))
                    ->setCellValue('L'.$i,  number_format(floatval(str_replace(",","",$key['COSTO_PROM_LOC'])), 2))
                    ->setCellValue('K'.$i,  number_format(floatval(str_replace(",","",$key['COSTO_ULT_LOC'])), 2))
                    ->setCellValue('m'.$i,  number_format((floatval(str_replace(",","",$key['CANT_DISPONIBLE']))  * floatval(str_replace(",","",$key['COSTO_PROM_LOC'])))),2);
                    
                    $i++;
                }

                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);

                $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:M".($i-1));
                $objPHPExcel->getActiveSheet()->getStyle("C4:M".($i-1))->applyFromArray($right);

            break;
            default:
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
            break;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Inventario actualizado hasta '.date('d/m/Y').'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public static function dataLiquidacionMeses($valor) {
        $sql_server = new \sql_server();
        
        $request = Request();
        $sql_exec = '';
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        $anio = date('Y');
        $getMonth  = date('n');
        $anio = intval($anio) - 1;
        
        switch ($company_user) {
            case '1':
                $sql_exec = "EXECUTE FCHA_VENCIMIENTO_LOTE ".$valor;
                break;
            case '2':
                $sql_exec = "EXECUTE FCHA_VENCIMIENTO_LOTE_GUMA ".$valor;                
                break;
            case '3':
                return false;
                break;
            case '4':
                return false;
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = array();
        $i=0;

        $query1 = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
        /*$query_vent_art = $sql_server->fetchArray($sql_vent_art, SQLSRV_FETCH_ASSOC);*/
        /*tbl_temporal::truncate()->insert($query_vent_art);*/

        foreach ($query1 as $key) {

            $oItem = tbl_temporal::where('articulo', $key['ARTICULO'])->get()->first();
            if ($oItem) {
                $cantidad = $oItem->cantidad;
                $vst_mes_Actual = $oItem->VstMesActual;
                $vst_anno_Actual = $oItem->VstAnnoActual;
            } else {
                $cantidad = 0;
                $vst_mes_Actual = 0;
                $vst_anno_Actual = 0;
            }

            /*$cantidad = tbl_temporal::where('articulo', $key['ARTICULO'])->select('cantidad')->first();
            $cantidad = ( $cantidad['cantidad']=='' )?0:$cantidad['cantidad'];*/

            

            $totalExistencia = $key['CANT_DISPONIBLE'];
            $promedio =   ( $cantidad>0 )?( $cantidad / 12 ):0;
            //$tempoEstimado = ($promedio>0)?($totalExistencia / $PromedioActual):0;

            $PromedioActual = number_format(($vst_anno_Actual / $getMonth), 2,".","");
            $tempoEstimado = ($key['CANT_DISPONIBLE'] > 0.10 && $PromedioActual > 0.10) ? $totalExistencia  / $PromedioActual : "0.00" ;

            $query[$i]['ARTICULO']          = $key['ARTICULO'];
            $query[$i]['DESCRIPCION']       = $key['DESCRIPCION'];
            $query[$i]['DIAS_VENCIMIENTO']  = $key['DIAS_VENCIMIENTO'];
            $query[$i]['CANT_DISPONIBLE2']  = $key['CANT_DISPONIBLE'];
            $query[$i]['CANT_DISPONIBLE']   = number_format($key['CANT_DISPONIBLE'],2).' - [ '.$key['UNIDAD_VENTA'].' ]';
            $query[$i]['F_VENCIMIENTO']     = date('d/m/Y', strtotime($key['FECHA_VENCIMIENTO']) );
            $query[$i]['LOTE']              = $key['LOTE'];
            $query[$i]['VTS_ANIO_ANT']      = number_format($vst_anno_Actual, 2);
            $query[$i]['BODEGA']            = $key['BODEGA'];
            $query[$i]['PROMEDIO_VENTA']    = number_format($PromedioActual, 2);
            $query[$i]['TEMPO_ESTI_VENT']   = number_format($tempoEstimado, 2);
            $query[$i]['COSTO_PROM_LOC']    = number_format($key['COSTO_PROM_LOC'], 2);
            $query[$i]['COSTO_ULT_LOC']     = number_format($key['COSTO_ULT_LOC'], 2);
            $query[$i]['COSTO_TOTAL']       = number_format(($key['CANT_DISPONIBLE'] * $key['COSTO_PROM_LOC']), 2);
            $i++;
        }
        $sql_server->close();

        return $query;
    }

    public static function getBodegaInventario($articulo) {
        
        $sql_server     = new \sql_server();
        $i = 0;
        $json = array();
        $sql_exec       = '';
        $request        = Request();
        $company_user   = Company::where('id',$request->session()->get('company_id'))->first()->id;
        $lbl = '';

        switch ($company_user) {
            case '1':
                $sql_exec = "SELECT * FROM gnet_master_bodegas WHERE ARTICULO = '".$articulo."' AND  BODEGA not in ('004')";
                $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
                foreach ($query as $fila) {
                    $json[$i]["id"]                 = $i;
                    $json[$i]["DETALLE"]            = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
                    $json[$i]["BODEGA"]             = $fila["BODEGA"];
                    $json[$i]["UNIDAD"]             =  $fila["UNIDAD"];
                    $json[$i]["NOMBRE"]             = $fila["NOMBRE"];
                    $json[$i]["CANT_DISPONIBLE"]    = number_format($fila["CANT_DISPONIBLE"],2);
                    $i++;
                }

              
                break;
            case '2':
                $sql_exec = 'SELECT * FROM gp_iweb_bodegas WHERE ARTICULO = '."'".$articulo."'".'';
                $lbl='GUMAPHARMA';
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = 'SELECT * FROM INN_iweb_bodegas WHERE ARTICULO = '."'".$articulo."'".'';
                $lbl = 'INNOVA';
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }
        if ($company_user != 1) {
            $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
            foreach ($query as $fila) {
                $json[$i]["id"]                 = $i;
                $json[$i]["DETALLE"]            = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
                $json[$i]["BODEGA"]             = $fila["BODEGA"];
                $json[$i]["UNIDAD"]             = $lbl;
                $json[$i]["NOMBRE"]             = $fila["NOMBRE"];
                $json[$i]["CANT_DISPONIBLE"]    = number_format($fila["CANT_DISPONIBLE"],2);
                $i++;
            }
        }

        $sql_server->close();
        return $json;
    }

    public static function getAllBodegas(Request $request) {
        
        $sql_server     = new \sql_server();
        $i = 0;
        $json = array(); 
        
        $Articulo   = $request->input('articulo');
        $Unidad     = $request->input('UNIDAD');

        $sql_exec = "SELECT * FROM gnet_master_bodegas WHERE ARTICULO = '".$Articulo."' AND UNIDAD = '".$Unidad."' AND BODEGA not in ('004')";
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
        foreach ($query as $fila) {
            $json[$i]["id"]                 = $i;
            $json[$i]["BODEGA"]             = $fila["BODEGA"];
            $json[$i]["UNIDAD"]             = $fila["UNIDAD"];
            $json[$i]["NOMBRE"]             = $fila["NOMBRE"];
            $json[$i]["CANT_DISPONIBLE"]    = number_format($fila["CANT_DISPONIBLE"],2);
            $i++;
        }
        $sql_server->close();
        return $json;
    }

    public static function getPreciosArticulos($articulo) {
        
        $sql_server     = new \sql_server();
        $sql_exec       = '';
        $request        = Request();
        $company_user   = Company::where('id',$request->session()->get('company_id'))->first()->id;
        switch ($company_user) {
            case '1':
                $sql_exec = 'EXEC sp_iweb_precios '."'".$articulo."'".' ';
                break;
            case '2':
                $sql_exec = 'EXEC sp_gp_iweb_precios '."'".$articulo."'".' ';
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = 'EXEC sp_inn_iweb_precios '."'".$articulo."'".' ';
                break;   
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }        

        $i = 0;
        $json = array();
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        foreach ($query as $fila) {
            $json[$i]["NIVEL_PRECIO"] = $fila["NIVEL_PRECIO"];
            $json[$i]["PRECIO"] = ($fila["PRECIO"]=="") ? "N/D" : number_format($fila["PRECIO"],2);
            $i++;
        }

        $sql_server->close();
        return $json;
    }
    public static function getMargenArticulos($articulo) {
        
        $sql_server     = new \sql_server();
        $sql_exec       = '';
        $request        = Request();
        $company_user   = Company::where('id',$request->session()->get('company_id'))->first()->id;
        switch ($company_user) {
            case '1':
                $sql_exec = 'EXEC sp_iweb_margen '."'".$articulo."'".' ';
                break;
            case '2':
                $sql_exec = 'EXEC sp_gp_iweb_precios '."'".$articulo."'".' ';
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = 'EXEC sp_inn_iweb_precios '."'".$articulo."'".' ';
                break;   
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }        

        $i = 0;
        $json = array();
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        foreach ($query as $fila) {
            $json[$i]["NIVEL_PRECIO"] = $fila["NIVEL_PRECIO"];
            $json[$i]["PRECIO"] = ($fila["PRECIO"]=="") ? "N/D" : number_format($fila["PRECIO"],2);
            $i++;
        }

        $sql_server->close();
        return $json;
    }
    public static function getCostosArticulos($articulo) {
        
        $sql_server     = new \sql_server();
        $sql_exec       = '';
        $request        = Request();
        $company_user   = Company::where('id',$request->session()->get('company_id'))->first()->id;
        switch ($company_user) {
            case '1':
                $sql_exec = "EXEC gnet_articulo_costos N'".$articulo."',umk";
                break;
            case '2':
                $sql_exec = "EXEC gnet_articulo_costos N'".$articulo."',guma";
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = "EXEC gnet_articulo_costos N'".$articulo."',innova";
                break;   
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }        

        $i = 0;
        $json = array();
        
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        foreach ($query as $fila) {
            $json[$i]["COSTO_PROM_LOC"] = "C$ " .number_format($fila["COSTO_PROM_LOC"],4);
            $json[$i]["COSTO_ULT_LOC"]  = ($fila["COSTO_ULT_LOC"]=="") ? "N/D" : "C$ " .number_format($fila["COSTO_ULT_LOC"],4);
            $i++;
        }

        $sql_server->close();
        return $json;
    }

    public static function getOtrosArticulos($articulo) {
        
        $sql_server     = new \sql_server();
        $sql_exec       = '';
        $request        = Request();
        $company_user   = Company::where('id',$request->session()->get('company_id'))->first()->id;
        switch ($company_user) {
            case '1':
                $sql_exec = "EXEC gnet_articulo_otros N'".$articulo."',umk";
                break;
            case '2':
                $sql_exec = "EXEC gnet_articulo_otros N'".$articulo."',guma";
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = "EXEC gnet_articulo_otros N'".$articulo."',innova";
                break;   
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }        

        $i = 0;
        $json = array();
        
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        foreach ($query as $fila) {
            $json[$i]["CLASE"]              = ($fila["CLASE_ABC"]=="") ? "N/D" : $fila["CLASE_ABC"];
            $json[$i]["MINIMO"]             = ($fila["EXISTENCIA_MINIMA"]=="") ? "N/D" : number_format($fila["EXISTENCIA_MINIMA"],4);
            $json[$i]["REORDEN"]            = ($fila["PUNTO_DE_REORDEN"]=="") ? "N/D" : number_format($fila["PUNTO_DE_REORDEN"],4);
            $json[$i]["REABASTECIMIENTO"]   = ($fila["PLAZO_REABAST"]=="") ? "N/D" : $fila["PLAZO_REABAST"]." Dias";
            $i++;
        }

        $sql_server->close();
        return $json;
    }

    public static function objIndicadores($articulo) {
        
        $sql_server = new \sql_server();
        $sql_exec_anual = '';
        $sql_exec_Vueno= '';
        $tem_=0;
        $RutaSegmento = "";
        $request        = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;;
        $Segmento = 0;

        $mes = intval(date('n'));
        $anio = intval(date('Y'));;

        switch ($company_user) {
            case '1':
                

                if ($Segmento==0) {
                    //TODAS LOS SEGMENTOS
                    $qSegmento =" Ruta NOT IN ('F01','F12') ";

                } else {
                    if ($Segmento==1) {
                        //TODAS LAS RUTAS DEL SEGMENTO FARMACIA
                        $qSegmento =" Ruta NOT IN ('F04','F02','F01','F12') ";
                    } else {
                        if ($Segmento==2) {
                           //TODAS LAS RUTAS DEL SEGMENTO MAYORISTA
                            $qSegmento =" Ruta IN ('F04') ";
                        } else {
                            if ($Segmento==3) {
                               //TODAS LAS RUTAS DEL SEGMENTO INSTITUCION
                                $qSegmento =" Ruta IN ('F02') ";
                            }
                            
                        }
                        
                    }
                }

            $sql_exec_anual = "SELECT 
                        T1.Articulo,T1.Descripcion,T1.Clasificacion6,
                        count(T1.articulo) As NºVentaMes,
                        isnull(sum(T1.cantidad),0) Cantidad,
                        isnull(sum(T1.venta),0) MontoVenta,
                        AVG (T1.[P. Unitario]) as AVG_,         
                        T1.[Costo Unitario] AS COSTO_PROM,
                        isnull((SELECT TOP 1 SUM(T2.cantidad) AS Cantidad FROM Softland.dbo.VtasTotal_UMK T2  WHERE ".$anio." = T2.[Año] AND T2.[P. Unitario] <= 0 AND T2.Articulo = T1.Articulo and ".$qSegmento." GROUP BY  T2.Articulo),0) AS Cantida_boni,
                        
                        T3.total,
                        T3.UNIDADES
            
                        FROM Softland.dbo.VtasTotal_UMK T1 
                        INNER JOIN iweb_articulos T3 ON T1.ARTICULO = T3.ARTICULO 
                        Where ".$anio." = T1.[Año] and T1.[P. Unitario] > 0 AND T1.Articulo = '".$articulo."'
                        AND  Ruta NOT IN('F01', 'F12') AND  ".$qSegmento." 
                        group by T1.Articulo,T1.Descripcion,T1.Clasificacion6,T1.año,T1.[Costo Unitario],T3.total,T3.UNIDADES
                        order by MontoVenta desc";

                        

            $sql_exec_mensual = "SELECT                         
                        T1.Articulo,T1.Descripcion,T1.Clasificacion6,
                        count(T1.articulo) As NºVentaMes,
                        isnull(sum(T1.cantidad),0) Cantidad,
                        isnull(sum(T1.venta),0) MontoVenta,
                        AVG (T1.[P. Unitario]) as AVG_,         
                        T1.[Costo Unitario] AS COSTO_PROM,
                        isnull((SELECT TOP 1 SUM(T2.cantidad) AS Cantidad FROM Softland.dbo.VtasTotal_UMK T2  WHERE ".$mes." = T2.nMes AND ".$anio." = T2.[Año] AND T2.[P. Unitario] <= 0 AND T2.Articulo = T1.Articulo and ".$qSegmento." GROUP BY  T2.Articulo),0) AS Cantida_boni,
                        T3.total,
                        T3.UNIDADES

                        FROM Softland.dbo.VtasTotal_UMK T1 
                        INNER JOIN iweb_articulos T3 ON T1.ARTICULO = T3.ARTICULO 
                        Where ".$mes." = T1.nMes and ".$anio." = T1.[Año] and T1.[P. Unitario] > 0 AND T1.Articulo = '".$articulo."'
                        AND  Ruta NOT IN('F01', 'F12') AND  ".$qSegmento." 
                        group by T1.Articulo,T1.Descripcion,T1.Clasificacion6,T1.mes,T1.año,T1.[Costo Unitario],T3.total,T3.UNIDADES
                        order by MontoVenta desc";

                break;
            case '2':


            $sql_exec_anual = "SELECT 
                T1.Articulo,T1.Descripcion,T1.Clasificacion6,
                count(T1.articulo) As NºVentaMes,
                isnull(sum(T1.cantidad),0) Cantidad,
                isnull(sum(T1.venta),0) MontoVenta,
                AVG (T1.[P. Unitario]) as AVG_,         
                T1.[Costo Unitario] AS COSTO_PROM,
                isnull((SELECT TOP 1 SUM(T2.cantidad) AS Cantidad FROM Softland.dbo.GP_VtasTotal_UMK T2  WHERE ".$anio." = T2.[Año] AND T2.[P. Unitario] <= 0 AND T2.Articulo = T1.Articulo  GROUP BY  T2.Articulo),0) AS Cantida_boni,
                
                T3.total,
                T3.UNIDADES
    
                FROM Softland.dbo.GP_VtasTotal_UMK T1 
                INNER JOIN GP_iweb_articulos T3 ON T1.ARTICULO = T3.ARTICULO 
                Where ".$anio." = T1.[Año] and T1.[P. Unitario] > 0 AND T1.Articulo = '".$articulo."'
                
                group by T1.Articulo,T1.Descripcion,T1.Clasificacion6,T1.año,T1.[Costo Unitario],T3.total,T3.UNIDADES
                order by MontoVenta desc";

                
    $sql_exec_mensual = "SELECT                         
                T1.Articulo,T1.Descripcion,T1.Clasificacion6,
                count(T1.articulo) As NºVentaMes,
                isnull(sum(T1.cantidad),0) Cantidad,
                isnull(sum(T1.venta),0) MontoVenta,
                AVG (T1.[P. Unitario]) as AVG_,         
                T1.[Costo Unitario] AS COSTO_PROM,
                isnull((SELECT TOP 1 SUM(T2.cantidad) AS Cantidad FROM Softland.dbo.GP_VtasTotal_UMK T2  WHERE ".$mes." = T2.nMes AND ".$anio." = T2.[Año] AND T2.[P. Unitario] <= 0 AND T2.Articulo = T1.Articulo  GROUP BY  T2.Articulo),0) AS Cantida_boni,
                T3.total,
                T3.UNIDADES

                FROM Softland.dbo.GP_VtasTotal_UMK T1 
                INNER JOIN GP_iweb_articulos T3 ON T1.ARTICULO = T3.ARTICULO 
                Where ".$mes." = T1.nMes and ".$anio." = T1.[Año] and T1.[P. Unitario] > 0 AND T1.Articulo = '".$articulo."'
                group by T1.Articulo,T1.Descripcion,T1.Clasificacion6,T1.mes,T1.año,T1.[Costo Unitario],T3.total,T3.UNIDADES
                order by MontoVenta desc";
                break;

                

            case '3':
                //$sql_exec_anual = "";
                break;   
            case '4':
                $sql_exec_anual = "SELECT 
                T1.Articulo,T1.Descripcion,
                count(T1.articulo) As NºVentaMes,
                isnull(sum(T1.cantidad),0) Cantidad,
                isnull(sum(T1.venta),0) MontoVenta,
                AVG (T1.[P. Unitario]) as AVG_,         
                T1.[Costo Unitario] AS COSTO_PROM,
                isnull((SELECT TOP 1 SUM(T2.cantidad) AS Cantidad FROM Softland.dbo.INV_VtasTotal_UMK_Temporal T2  WHERE ".$anio." = T2.[Año] AND T2.[P. Unitario] <= 0 AND T2.Articulo = T1.Articulo  GROUP BY  T2.Articulo),0) AS Cantida_boni,
                
                T3.total,
                T3.UNIDADES
    
                FROM Softland.dbo.INV_VtasTotal_UMK_Temporal T1 
                INNER JOIN inn_iweb_articulos T3 ON T1.ARTICULO = T3.ARTICULO 
                Where ".$anio." = T1.[Año] and T1.[P. Unitario] > 0 AND T1.Articulo = '".$articulo."'
                
                group by T1.Articulo,T1.Descripcion,T1.año,T1.[Costo Unitario],T3.total,T3.UNIDADES
                order by MontoVenta desc";

                
    $sql_exec_mensual = "SELECT                         
                T1.Articulo,T1.Descripcion,
                count(T1.articulo) As NºVentaMes,
                isnull(sum(T1.cantidad),0) Cantidad,
                isnull(sum(T1.venta),0) MontoVenta,
                AVG (T1.[P. Unitario]) as AVG_,         
                T1.[Costo Unitario] AS COSTO_PROM,
                isnull((SELECT TOP 1 SUM(T2.cantidad) AS Cantidad FROM Softland.dbo.INV_VtasTotal_UMK_Temporal T2  WHERE ".$mes." = T2.nMes AND ".$anio." = T2.[Año] AND T2.[P. Unitario] <= 0 AND T2.Articulo = T1.Articulo  GROUP BY  T2.Articulo),0) AS Cantida_boni,
                T3.total,
                T3.UNIDADES

                FROM Softland.dbo.INV_VtasTotal_UMK_Temporal T1 
                INNER JOIN inn_iweb_articulos T3 ON T1.ARTICULO = T3.ARTICULO 
                Where ".$mes." = T1.nMes and ".$anio." = T1.[Año] and T1.[P. Unitario] > 0 AND T1.Articulo = '".$articulo."'
                group by T1.Articulo,T1.Descripcion,T1.mes,T1.año,T1.[Costo Unitario],T3.total,T3.UNIDADES
                order by MontoVenta desc";

                break;        
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query_anual = $sql_server->fetchArray($sql_exec_anual,SQLSRV_FETCH_ASSOC);
        $query_mensual = $sql_server->fetchArray($sql_exec_mensual,SQLSRV_FETCH_ASSOC);
        

        $json = array();
        
        $i = 0;
        
        $getMonth  = date('n');

        if( count($query_anual)>0 ) {
            foreach ($query_anual as $key) {

                $oItem = tbl_temporal::where('articulo', $key['Articulo'])->get()->first();
                if ($oItem) {
                    $cantidad = $oItem->cantidad;
                    $vst_mes_Actual = $oItem->VstMesActual;
                    $vst_anno_Actual = $oItem->VstAnnoActual;
                } else {
                    $cantidad = 0;
                    $vst_mes_Actual = 0;
                    $vst_anno_Actual = 0;
                }

                $totalExistencia = $key['total'];

                $PromedioActual = number_format(($vst_anno_Actual / $getMonth), 2,".","");
                $tempoEstimado = ($key['total'] > 0.10 && $PromedioActual > 0.10) ? $totalExistencia  / $PromedioActual : "0.00" ;

                $Total_Facturado        = $key['MontoVenta'];
                $Cantidad               = $key['Cantidad'];
                $Cantidad_bonificada    = $key['Cantida_boni'];                
                $COSTO_PROM             = $key['COSTO_PROM'];
                $TOTAL_B002             = $key['total'];
                $TOTAL_UND_B002         = $key['UNIDADES'];
                $json['ANUAL'][$i]['name']       = $key['Articulo'];
                $json['ANUAL'][$i]['articulo']   = $key['Descripcion'];


                $AVG = floatval($Total_Facturado)  / (  floatval($Cantidad) + floatval($Cantidad_bonificada) );

                $Costo_total_Promedio = (floatval($Cantidad) + floatval($Cantidad_bonificada)) * floatval($COSTO_PROM);
                
                $Monto_Contribucion = floatval($Total_Facturado)  - floatval($Costo_total_Promedio);

               //$prom_contribucion = ($Monto_Contribucion / $Costo_total_Promedio) * 100;          

               $prom_contribucion = (( $AVG - floatval($COSTO_PROM) ) / $AVG) * 100;


                $tem_ = floatval($Total_Facturado);
                $UND_ = $Cantidad;
                $UND_BO = floatval($Cantidad_bonificada);
                $AVG_ = number_format(floatval($AVG),2);
                $COSTO_PROM_ = number_format(floatval($COSTO_PROM),2);
                $MARG_CONTRI = number_format(floatval($Monto_Contribucion),2);
                $PORC_CONTRI = number_format(floatval($prom_contribucion),2);
                $TIEMPO_ESTIMADO = number_format(floatval($tempoEstimado),2);



                $json['ANUAL'][$i]['data']       = $tem_;
                $json['ANUAL'][$i]['dtUnd']      = $UND_;
                $json['ANUAL'][$i]['dtUndBo']    = $UND_BO;
                $json['ANUAL'][$i]['dtAVG']      = $AVG_;
                $json['ANUAL'][$i]['dtCPM']      = $COSTO_PROM_;
                $json['ANUAL'][$i]['dtMCO']      = $MARG_CONTRI;
                $json['ANUAL'][$i]['dtPCO']      = $PORC_CONTRI; 
                
                $json['ANUAL'][$i]['dtTIE']      = $TIEMPO_ESTIMADO;   
                $json['ANUAL'][$i]['dtTB2']      = $TOTAL_B002;   
                $json['ANUAL'][$i]['dtTUB']      = $TOTAL_UND_B002; 
                $json['ANUAL'][$i]['dtPRO']      = $PromedioActual;
             
                
                $i++;
            }
        }
        $i = 0;
        if( count($query_mensual)>0 ) {
            foreach ($query_mensual as $row) {

                $oItem = tbl_temporal::where('articulo', $key['Articulo'])->get()->first();
                if ($oItem) {
                    $cantidad = $oItem->cantidad;
                    $vst_mes_Actual = $oItem->VstMesActual;
                    $vst_anno_Actual = $oItem->VstAnnoActual;
                } else {
                    $cantidad = 0;
                    $vst_mes_Actual = 0;
                    $vst_anno_Actual = 0;
                }

                $totalExistencia = $key['total'];

                $PromedioActual = number_format(($vst_anno_Actual / $getMonth), 2,".","");
                $tempoEstimado = ($row['total'] > 0.10 && $PromedioActual > 0.10) ? $totalExistencia  / $PromedioActual : "0.00" ;

                $Total_Facturado        = $row['MontoVenta'];
                $Cantidad               = $row['Cantidad'];
                $Cantidad_bonificada    = $row['Cantida_boni'];                
                $COSTO_PROM             = $row['COSTO_PROM'];
                $TOTAL_B002             = $row['total'];
                $TOTAL_UND_B002         = $row['UNIDADES'];
                $json['MENSUAL'][$i]['name']       = $row['Articulo'];
                $json['MENSUAL'][$i]['articulo']   = $row['Descripcion'];


                $AVG = floatval($Total_Facturado)  / (  floatval($Cantidad) + floatval($Cantidad_bonificada) );

                $Costo_total_Promedio = (floatval($Cantidad) + floatval($Cantidad_bonificada)) * floatval($COSTO_PROM);
                
                $Monto_Contribucion = floatval($Total_Facturado)  - floatval($Costo_total_Promedio);

               //$prom_contribucion = ($Monto_Contribucion / $Costo_total_Promedio) * 100;          

               $prom_contribucion = (( $AVG - floatval($COSTO_PROM) ) / $AVG) * 100;


                $tem_ = floatval($Total_Facturado);
                $UND_ = floatval($Cantidad);
                $UND_BO = floatval($Cantidad_bonificada);
                $AVG_ = number_format(floatval($AVG),2);
                $COSTO_PROM_ = number_format(floatval($COSTO_PROM),2);
                $MARG_CONTRI = number_format(floatval($Monto_Contribucion),2);
                $PORC_CONTRI = number_format(floatval($prom_contribucion),2);
                $TIEMPO_ESTIMADO = number_format(floatval($tempoEstimado),2);



                $json['MENSUAL'][$i]['data']       = $tem_;
                $json['MENSUAL'][$i]['dtUnd']      = $UND_;
                $json['MENSUAL'][$i]['dtUndBo']    = $UND_BO;
                $json['MENSUAL'][$i]['dtAVG']      = $AVG_;
                $json['MENSUAL'][$i]['dtCPM']      = $COSTO_PROM_;
                $json['MENSUAL'][$i]['dtMCO']      = $MARG_CONTRI;
                $json['MENSUAL'][$i]['dtPCO']      = $PORC_CONTRI; 
                
                $json['MENSUAL'][$i]['dtTIE']      = $TIEMPO_ESTIMADO;   
                $json['MENSUAL'][$i]['dtTB2']      = $TOTAL_B002;   
                $json['MENSUAL'][$i]['dtTUB']      = $TOTAL_UND_B002; 
                $json['MENSUAL'][$i]['dtPRO']      = $PromedioActual;
                
                
                $i++;
            }
        }

        return $json;
        $sql_server->close();
    }

    public static function getArtBonificados($articulo) {
        
        $sql_server = new \sql_server();
        
        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        
        switch ($company_user) {
            case '1':
                $sql_exec = 'SELECT REGLAS FROM GMV_mstr_articulos WHERE ARTICULO = '."'".$articulo."'".' ';
                break;
            case '2':
                $sql_exec = 'SELECT REGLAS FROM GP_GMV_mstr_articulos WHERE ARTICULO = '."'".$articulo."'".' ';
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = 'SELECT REGLAS FROM INN_GMV_mstr_articulos WHERE ARTICULO = '."'".$articulo."'".' ';
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
        $i = 0;
        $json = array();       
        foreach ($query as $fila) {
            $porciones = explode(",", $fila["REGLAS"]);
            for($n=0;$n<count($porciones);$n++){
                $Position_elementos = substr($porciones[$n], 0, strpos ($porciones[$n] , "+" ));
                $json[$i]["ORDEN"] = $Position_elementos;
                $json[$i]["REGLAS"] = $porciones[$n];
                $i++;
            }           
        }
        $sql_server->close();
        return $json;
    }

    public static function transaccionesDetalle($f1, $f2, $art, $tp) {
        $sql_server = new \sql_server();
        
        $f1_ = date('Y-m-d', strtotime($f1));
        $f2_ = date('Y-m-d', strtotime($f2));

        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) {
            case '1':
                $sql_exec = 'SELECT * FROM iweb_transacciones WHERE ARTICULO = '."'".$art."'".' AND DESCRTIPO = '."'".$tp."'".' AND FECHA  BETWEEN '."'".$f1."'".' AND '."'".$f2."'".'  ORDER BY ARTICULO ASC';
                break;
            case '2':
                $sql_exec = 'SELECT * FROM GP_iweb_transacciones WHERE ARTICULO = '."'".$art."'".' AND DESCRTIPO = '."'".$tp."'".' AND FECHA  BETWEEN '."'".$f1."'".' AND '."'".$f2."'".'  ORDER BY ARTICULO ASC';
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = 'SELECT * FROM inn_iweb_transacciones WHERE ARTICULO = '."'".$art."'".' AND DESCRTIPO = '."'".$tp."'".' AND FECHA  BETWEEN '."'".$f1."'".' AND '."'".$f2."'".'  ORDER BY ARTICULO ASC';
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
        $i=0;
        $json = array();
        foreach($query as $fila){
            $json[$i]["FECHA"] = date_format($fila["FECHA"],"d/m/Y");
            $json[$i]["LOTE"] = $fila["LOTE"];
            $json[$i]["DESCRTIPO"] = $fila["DESCRTIPO"];
            $json[$i]["CANTIDAD"] = number_format($fila["CANTIDAD"],2);
            $json[$i]["REFERENCIA"] = $fila["REFERENCIA"];
            $i++;
        }

        $sql_server->close();
        return $json;
    }

    public static function getLotes($articulo) {
        
        $sql_server = new \sql_server();
        
        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) {
            case '1':
                $sql_exec = 'SELECT * FROM iweb_lotes WHERE ARTICULO = '."'".$articulo."'".' ORDER BY BODEGA ';
                break;
            case '2':
                $sql_exec = 'SELECT * FROM gp_iweb_lotes WHERE ARTICULO = '."'".$articulo."'".' ';
                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = 'SELECT * FROM inn_iweb_lotes WHERE ARTICULO = '."'".$articulo."'".' ';
                break;
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);
        $i = 0;
        $json = array();
        foreach ($query as $fila) {
            $json[$i]["ARTICULO"] = $fila["ARTICULO"];
            $json[$i]["BODEGA"] = $fila["BODEGA"];
            $json[$i]["CANT_DISPONIBLE"] = number_format($fila["CANT_DISPONIBLE"], 2);
            $json[$i]["LOTE"] = $fila["LOTE"];
            $json[$i]["FECHA_INGRESO"] = date('d/m/Y',strtotime($fila["FECHA_ENTR"]));
            $json[$i]["CANTIDAD_INGRESADA"] = number_format($fila["CANTIDAD_INGRESADA"], 2);
            $json[$i]["FECHA_ENTRADA"] = date('d/m/Y',strtotime($fila["FECHA_ENTRADA"]));
            $json[$i]["FECHA_VENCIMIENTO"] = date('d/m/Y',strtotime($fila["FECHA_VENCIMIENTO"]));
            $i++;
        }
        $sql_server->close();
        return $json;
    }

    public static function getLotesArticulo($bodega, $articulo,$Unidad) {
        
        $sql_server = new \sql_server();        
        $sql_exec = '';
        $i = 0;
        $json = array();
        $request = Request();
        
        $array_unidades = array(
            "iweb_lotes"=>"UMK" , 
            "gp_iweb_lotes" => "GUMAPHARMA",
            "inn_iweb_lotes" => "INN",
            "gp_iweb_lotes" => "GP",
        );

        $view = array_search($Unidad, $array_unidades);

        $sql_exec = "SELECT * FROM ".$view." WHERE BODEGA = '".$bodega."' AND  ARTICULO = '".$articulo."' ";
        
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        foreach ($query as $fila) {
            $json[$i]["ARTICULO"] = $fila["ARTICULO"];
            $json[$i]["BODEGA"] = $fila["BODEGA"];
            $json[$i]["CANT_DISPONIBLE"] = number_format($fila["CANT_DISPONIBLE"], 2);
            $json[$i]["LOTE"] = $fila["LOTE"];
            $json[$i]["FECHA_INGRESO"] = date('d/m/Y',strtotime($fila["FECHA_ENTR"]));
            $json[$i]["CANTIDAD_INGRESADA"] = number_format($fila["CANTIDAD_INGRESADA"], 2);
            $json[$i]["FECHA_ENTRADA"] = date('d/m/Y',strtotime($fila["FECHA_ENTRADA"]));
            $json[$i]["FECHA_VENCIMIENTO"] = date('d/m/Y',strtotime($fila["FECHA_VENCIMIENTO"]));
            $i++;
        }
        $sql_server->close();
        return $json;
    }

    public static function clean($string) {
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);
    }
}