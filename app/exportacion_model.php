<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class exportacion_model extends Model
{
    public static function getVentasExportacion($f1, $f2) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $i=0;
        $data = array();

        $f1 = $f1." 00 : 00 : 00 : 000";
        $f2 = $f2." 23 : 59 : 59 : 998";

        $sql_exec = "EXEC gnet_ventas_exportacion '".$f1."','".$f2."'";

       


        
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);
        
        foreach ($query as $key) {

            $Factura_Detalles = exportacion_model::HistorialFactura($key['FACTURA']);
            
            $Cantidad = array_sum(array_column($Factura_Detalles['objDt'],'CANTIDAD')) / 1000;

            $data[$i]["DETALLE"]             = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';       
            $data[$i]['FACTURA']             = $key['FACTURA'];
            $data[$i]['CLIENTE']             = $key['CLIENTE'];
            $data[$i]['NOMBRE_CLIENTE']      = $key['NOMBRE_CLIENTE'];
            $data[$i]['FECHA']               = $key['Dia']->format('d/m/Y');
            $data[$i]['CANTIDAD']            = $Cantidad;
            $data[$i]['VENDEDOR']            = $key['RUTA'];                
            $data[$i]['TOTAL_FACTURA']       = $key['Total'];
            $data[$i]['TIPO_CAMBIO']         = $key['TIPO_CAMBIO'];
            $data[$i]['TOTAL_MONEDA_LOCAL']  = $key['Total'] * $key['TIPO_CAMBIO'];
            $i++;
        }
        $sql_server->close();        

        return $data;
    }

    public static function HistorialFactura($nFactura){
        $sql_server = new \sql_server();
        $Dta = array();
        $sql_exec = '';
        $request = Request();
        $sql_exec = 'SELECT FACTURA, ARTICULO, DESCRIPCION, CANTIDAD, PRECIO_UNITARIO, PRECIO_TOTAL FROM INN_DETALLES_FACTURAS WHERE FACTURA = '."'".$nFactura."'";                
        $query = $sql_server->fetchArray($sql_exec, SQLSRV_FETCH_ASSOC);

        if( count($query)>0 ){
            return $Dta = array('objDt' => $query);
        }

        $sql_server->close();
        return false;
    }
        
}

