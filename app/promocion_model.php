<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class promocion_model extends Model
{
    public static function getPromocion($f1, $f2) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';
        $i=0;
        $data = array();
        $f1 = $f1." 00 : 00 : 00 : 000";
        $f2 = $f2." 23 : 59 : 59 : 998";

        $sql_exec = "EXEC gnet_promocion '".$f1."','".$f2."'";
        
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);
        
        

        foreach ($query as $key) {  
            if($key['Promo'] > 0){
                $data[$i]["DETALLE"]        = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';       
                $data[$i]['FACTURA']        = $key['FACTURA'];
                $data[$i]['CLIENTE']        = $key['CLIENTE'];
                $data[$i]['NOMBRE_CLIENTE'] = $key['NOMBRE_CLIENTE'];
                $data[$i]['FECHA']          = $key['Dia']->format('d/m/Y');;
                $data[$i]['VENDEDOR']       = $key['Ruta'];
                $data[$i]['TOTAL']          = number_format($key['Promo'],2);
                $data[$i]['TOTAL_FACTURA']  = $key['Total'];
                $i++;
            }   
            
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

    public static function getResumen($f1, $f2) {        
        $sql_server = new \sql_server();        
        $request = Request();
        $sql_exec = '';        
        $i=0;
        $data = array();
        $f1 = $f1." 00 : 00 : 00 : 000";
        $f2 = $f2." 23 : 59 : 59 : 998";

        $sql_exec = "EXEC gnet_promocion_resumen '".$f1."','".$f2."'";
    
        $query = $sql_server->fetchArray( $sql_exec , SQLSRV_FETCH_ASSOC);
        
        

        foreach ($query as $key) {            
            $data[$i]['VENDEDOR']       = $key['VENDEDOR'];
            $data[$i]['NOMBRE']         = $key['NOMBRE'];
            $data[$i]['SKU1']           = number_format($key['SKU1'],0);
            $data[$i]['SKU2']           = number_format($key['SKU2'],0);
            $data[$i]['SKU3']           = number_format($key['SKU3'],0);
            $data[$i]['SKU4']           = number_format($key['SKU4'],0);
            $data[$i]['SKU5']           = number_format($key['SKU5'],0);
            $data[$i]['TOTAL']          = number_format($key['TOTAL'],0);
            $data[$i]['VALOR']          = number_format($key['VALOR'],0);
            
            $i++;
        }
        $sql_server->close();        

        return $data;
    }

        
}

