<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vinneta_model extends Model
{
    public static function getVinnetas($f1, $f2) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        $i=0;
        $data = array();
        $f1 = $f1." 00 : 00 : 00 : 000";
        $f2 = $f2." 23 : 59 : 59 : 998";

        switch ($company_user) {
            case '1':
                $sql_exec = "EXEC gnet_vinneta '".$f1."','".$f2."'";
                

                break;
            case '2':
                $sql_exec = "";

                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = "";
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

    
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);
        
        

        foreach ($query as $key) {     
            $data[$i]["DETALLE"]        = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';       
            $data[$i]['FACTURA']        = $key['FACTURA'];
            $data[$i]['CLIENTE']        = $key['CLIENTE'];
            $data[$i]['NOMBRE_CLIENTE']        = $key['NOMBRE_CLIENTE'];
            $data[$i]['FECHA']          = $key['FECHA']->format('d/m/Y');;
            $data[$i]['VENDEDOR']       = $key['VENDEDOR'];
            $data[$i]['TOTAL']          = $key['TOTAL'];
            $data[$i]['CANT_LIQUIDADA'] = $key['CANT_LIQUIDADA'];
            $data[$i]['DISPONIBLE']     = ($key['TOTAL'] - $key['CANT_LIQUIDADA']);
            $data[$i]['TOTAL_FACTURA']  = $key['TOTAL_FACTURA'];
            $data[$i]["BOTONES"]        = '<button type="button" class="btn btn-secondary float-center"  onClick="History('."'".$key['FACTURA']."'".')" >
                                                <i class="material-icons text-white mt-1"  style="font-size: 20px">history</i>
                                            </button>';
            $i++;
        }
        $sql_server->close();        

        return $data;
    }

    public static function getHistorialFactura($Factura) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $i=0;
        $data = array();
        $sql_exec = "SELECT * FROM [DESARROLLO].[dbo].[tbl_vineta_liquidadas] WHERE [FACTURA] = '".$Factura."'";
    
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);

        foreach ($query as $key) {     
            
            $data[$i]['FACTURA']        = $key['FACTURA'];
            $data[$i]['VOUCHER']        = $key['VOUCHER'];
            $data[$i]['LINEA']        = $key['LINEA'];
            $data[$i]['CANTIDAD']        = $key['CANTIDAD'];
            $data[$i]['CLIENTE']        = $key['CLIENTE'];
            $data[$i]['RUTA']        = $key['RUTA'];
            $data[$i]['FECHA']        = $key['FECHA']->format('d-m-Y H:i:s');;
            $data[$i]['COD_RECIBO']        = $key['COD_RECIBO'];
            $data[$i]['VALOR_UND']        = $key['VALOR_UND'];
            $data[$i]['COMMENT']        = $key['COMMENT'];

            $i++;
        }
        $sql_server->close();        

        return $data;
    }
    public static function getPagadoRuta($f1, $f2) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        $i=0;
        $data = array();
        $f1 = $f1." 00 : 00 : 00";
        $f2 = $f2." 23 : 59 : 59";

        switch ($company_user) {
            case '1':                
                $sql_exec = "SELECT
                    T0.RUTA,
                    (SELECT T1.NOMBRE FROM UMK_VENDEDORES_ACTIVO T1 WHERE T1.VENDEDOR=T0.RUTA) AS NOMBRE,
                    SUM(T0.CANTIDAD * T0.VALOR_UND) AS TOTAL
                FROM 	
                    DESARROLLO.dbo.tbl_vineta_liquidadas T0
                WHERE T0.FECHA >= '".$f1."' and T0.FECHA <= '".$f2."' AND T0.RUTA NOT IN ('F00') 
                GROUP BY T0.RUTA";

                break;
            case '2':
                $sql_exec = "";

                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = "";
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

    
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);
        
        

        foreach ($query as $key) {          
            $data[$i]['RUTA']        = $key['RUTA'];
            $data[$i]['NOMBRE']      = $key['NOMBRE'];
            $data[$i]['TOTAL']       = $key['TOTAL'];
            $i++;
        }

        $sql_server->close();        

        return $data;
    }

    public static function getpagado($f1, $f2) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $i=0;
        $data = array();
        

        $sql_exec = "SELECT SUM(T0.CANTIDAD * T0.VALOR_UND) AS TOTAL FROM DESARROLLO.dbo.tbl_vineta_liquidadas T0 WHERE CAST(T0.FECHA AS date) BETWEEN '".$f1."' AND '".$f2."'";
    
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);

        
        $sql_server->close();        

        return $query[0]['TOTAL'];
    }

    public static function getVinnetasResumen($f1, $f2) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;
        $i=0;
        $data = array();
        $f1 = $f1." 00 : 00 : 00 : 000";
        $f2 = $f2." 23 : 59 : 59 : 998";

        switch ($company_user) {
            case '1':
                $sql_exec = "EXEC gnet_vinneta_resumen '".$f1."','".$f2."'";

                break;
            case '2':
                $sql_exec = "";

                break;
            case '3':
                return false;
                break;
            case '4':
                $sql_exec = "";
                break; 
            default:                
                dd("Ups... al parecer sucedio un error al tratar de encontrar articulos para esta empresa. ". $company->id);
                break;
        }

    
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);
        
        

        foreach ($query as $key) {            
            $data[$i]['VENDEDOR']   = $key['VENDEDOR'];
            $data[$i]['V_5']        = number_format($key['V_5'],0);
            $data[$i]['V_10']        = number_format($key['V_10'],0);
            $data[$i]['V_20']        = number_format($key['V_20'],0);
            $data[$i]['V_30']        = number_format($key['V_30'],0);
            $data[$i]['V_35']        = number_format($key['V_35'],0);
            $data[$i]['V_40']        = number_format($key['V_40'],0);
            $data[$i]['V_50']        = number_format($key['V_50'],0);
            $data[$i]['V_70']        = number_format($key['V_70'],0);
            $data[$i]['TOTAL']      = number_format($key['TOTAL'],0);
            $data[$i]['VALOR']      = number_format($key['VALOR'],0);
            
            $i++;
        }
        $sql_server->close();        

        return $data;
    }

    

    public static function getDetalleOrdenCompra($ordCompra){
        $sql_server = new \sql_server();
        $dta = array();
        $i=0;
        $sql_exec = '';
        $request = Request();
        $company_user = Company::where('id',$request->session()->get('company_id'))->first()->id;

        switch ($company_user) {
            case '1':
                $sql_exec = 'SELECT * FROM GNET_ORDEN_COMPRA_LINEAS WHERE ORDEN_COMPRA = '."'".$ordCompra."'";
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
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ) {
            foreach ($query as $key) {
                $dta[$i]['ARTICULO']                = $key['ARTICULO'];
                $dta[$i]['DESCRIPCION']             = $key['DESCRIPCION'];
                $dta[$i]['LABORATORIO']             = $key['LABORATORIO'];
                $dta[$i]['UNIDAD_ALMACEN']          = $key['UNIDAD_ALMACEN'];
                $dta[$i]['PROVEEDOR']               = $key['PROVEEDOR'];
                $dta[$i]['NOMBRE']                  = $key['NOMBRE'];
                $dta[$i]['CANTIDAD_ORDENADA']       = number_format($key['CANTIDAD_ORDENADA'], 2);
                $dta[$i]['CANTIDAD_RECIBIDA']       = number_format($key['CANTIDAD_RECIBIDA'], 2);
                $dta[$i]['CANT_PEDIDA']             = number_format($key['CANT_PEDIDA'], 2);
                $dta[$i]['CANT_RESTANTE']           = number_format($key['CANT_RESTANTE'], 2);
                $dta[$i]['ESTADO']                  = trim($key['ESTADO']);
                $dta[$i]['Fecha']                   = trim($key['Fecha_']);
                $dta[$i]['Dias_acumulados']         = trim($key['Dias_acumulados']);
                $dta[$i]['FECHA_COTIZACION']        = trim($key['FECHA_COTIZACION_']);
                $dta[$i]['FECHA_OFRECIDA']          = trim($key['FECHA_OFRECIDA_']);
                $dta[$i]['FECHA_EMISION']           = trim($key['FECHA_EMISION_']);
                $dta[$i]['FECHA_REQ_EMBARQUE']      = trim($key['FECHA_REQ_EMBARQUE_']);
                $dta[$i]['FECHA_REQUERIDA']         = trim($key['FECHA_REQUERIDA_']);
                $dta[$i]['Dias_acumulado_despacho'] = trim($key['Dias_acumulado_despacho']);
                $dta[$i]['REF_ORDEN_COMPRA']        = trim($key['REF_ORDEN_COMPRA']);
                $dta[$i]['REF_FACTURA']             = trim($key['REF_FACTURA']);
                $dta[$i]['TIPO_MERCADO']            = trim($key['TIPO_MERCADO']);
                $dta[$i]['LEYENDA_MINSA']           = trim($key['LEYENDA_MINSA']);
                $dta[$i]['LOTE']                    = trim($key['LOTE']);
                $i++;
            }
            return $dta;
        }

        $sql_server->close();
        return false;
    }
}

