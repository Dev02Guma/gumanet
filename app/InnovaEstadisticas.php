<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InnovaEstadisticas extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_inn_historico_sale";

    public static function getInnStatSale(Request $request)
    {

        $mes    = $request->input('mes');
        $anio   = $request->input('anio');
        
        $query_stat_sale = "EXEC PRODUCCION.dbo.getInnStatSale @Mes = ?, @Anio = ?";
        $resul_stat_sale = DB::connection('sqlsrv')->select($query_stat_sale, [$mes, $anio]);
        
        $data = array(); 
        $key = 0;

        $Targets = ["VUENO", "CHOLIN 6000", "GENÉRICO", "ECO PLUS"];

        foreach ($Targets as $item) {

            $index_key = array_search($item, array_column($resul_stat_sale, 'DESCRIPCION'));  

            $Cantidad       = ($index_key !== false) ? $resul_stat_sale[$index_key]->CANTIDAD : 0 ;
            $Target         = $item;
            $Venta_SinIVA   = ($index_key !== false) ? $resul_stat_sale[$index_key]->VENTA_SIN_IVA : 0 ;
            $Venta_ConIVA   = ($index_key !== false) ? $resul_stat_sale[$index_key]->VENTA_CON_IVA : 0 ;
            $AVG_SinIVA     = ($index_key !== false) ? $resul_stat_sale[$index_key]->AVG_SIN_IVA : 0 ;
            $AVG_ConIVA     = ($index_key !== false) ? $resul_stat_sale[$index_key]->AVG_CON_IVA : 0 ;

            $data[$key]['DESCRIPCION']      = $Target;
            $data[$key]['CANTIDAD']         = number_format($Cantidad, 2,".","");
            $data[$key]['VENTA_SIN_IVA']    = number_format($Venta_SinIVA, 2,".","");
            $data[$key]['VENTA_CON_IVA']    = number_format($Venta_ConIVA, 2,".","");
            $data[$key]['AVG_SIN_IVA']      = number_format($AVG_SinIVA, 2,".","");
            $data[$key]['AVG_CON_IVA']      = number_format($AVG_ConIVA, 2,".","");
            $key++;
        }



        return $data;
    }
    public static function getInnStatRuta(Request $request)
    {

        $mes    = $request->mes;
        $anio   = $request->anio;
        $isUp   = null;

        $query_stat_ruta = "EXEC PRODUCCION.dbo.getInnStatRuta @Mes = ?, @Anio = ?";
        $resul_stat_ruta = DB::connection('sqlsrv')->select($query_stat_ruta, [$mes, $anio]);

        $resul_last_month = DB::connection('sqlsrv')->select($query_stat_ruta, [$mes -1, $anio]);

        $isUp = (count($resul_last_month) && count($resul_stat_ruta)) ? (floatval($resul_last_month[0]->AVG_SIN_IVA) > floatval($resul_stat_ruta[0]->AVG_SIN_IVA)) ? false : true : false ;
        
        $data = array(); 
        $key = 0;

        $Targets = ["V00", "V15"];

        foreach ($Targets as $item) {

            $v = VendedorInnova::WHERE('VENDEDOR',$item)->get()->toArray();
            $index_key = array_search($item, array_column($resul_stat_ruta, 'DESCRIPCION'));  
            
            $Cantidad       = ($index_key !== false) ? $resul_stat_ruta[$index_key]->CANTIDAD : 0 ;
            $Target         = $item;
            $Venta_SinIVA   = ($index_key !== false) ? $resul_stat_ruta[$index_key]->VENTA_SIN_IVA : 0 ;
            $Venta_ConIVA   = ($index_key !== false) ? $resul_stat_ruta[$index_key]->VENTA_CON_IVA : 0 ;
            $AVG_SinIVA     = ($index_key !== false) ? $resul_stat_ruta[$index_key]->AVG_SIN_IVA : 0 ;
            $AVG_ConIVA     = ($index_key !== false) ? $resul_stat_ruta[$index_key]->AVG_CON_IVA : 0 ;

            $data[$key]['DESCRIPCION']      = $v[0]['NOMBRE'];
            $data[$key]['CANTIDAD']         = number_format($Cantidad, 2,".","");
            $data[$key]['VENTA_SIN_IVA']    = number_format($Venta_SinIVA, 2,".","");
            $data[$key]['VENTA_CON_IVA']    = number_format($Venta_ConIVA, 2,".","");
            $data[$key]['AVG_SIN_IVA']      = number_format($AVG_SinIVA, 2,".","");
            $data[$key]['AVG_CON_IVA']      = number_format($AVG_ConIVA, 2,".","");
            $data[$key]['AVG_IS_UP']        = $isUp;
            
            $key++;
        }

        return $data;
    }


    public static function saveInnStat($mes, $anio)
    {
        InnovaEstadisticas::where('MES', $mes)->where('ANNIO', $anio)->delete();

        $query_stat_ruta = "EXEC PRODUCCION.dbo.getInnStatRuta @Mes = ?, @Anio = ?";
        $resul_stat_ruta = DB::connection('sqlsrv')->select($query_stat_ruta, [$mes, $anio]);
        $datos_a_insertar = array();

        foreach ($resul_stat_ruta as $key => $value) {
            $datos_a_insertar[$key]['DESCRIPCION']      = $value->DESCRIPCION;
            $datos_a_insertar[$key]['CANTIDAD']         = $value->CANTIDAD;
            $datos_a_insertar[$key]['VENTA_SIN_IVA']    = $value->VENTA_SIN_IVA;
            $datos_a_insertar[$key]['VENTA_CON_IVA']    = $value->VENTA_CON_IVA;
            $datos_a_insertar[$key]['AVG_SIN_IVA']      = $value->AVG_SIN_IVA;       
            $datos_a_insertar[$key]['AVG_CON_IVA']      = $value->AVG_CON_IVA;      
            $datos_a_insertar[$key]['MES']              = $mes;       
            $datos_a_insertar[$key]['ANNIO']            = $anio;          
        }
        $response = InnovaEstadisticas::insert($datos_a_insertar);


        $query_stat_sale = "EXEC PRODUCCION.dbo.getInnStatSale @Mes = ?, @Anio = ?";
        $resul_stat_sale = DB::connection('sqlsrv')->select($query_stat_sale, [$mes, $anio]);
        $datos_a_insertar = array(); 

        foreach ($resul_stat_sale as $key => $value) {
            $datos_a_insertar[$key]['DESCRIPCION']      = $value->DESCRIPCION;
            $datos_a_insertar[$key]['CANTIDAD']         = $value->CANTIDAD;
            $datos_a_insertar[$key]['VENTA_SIN_IVA']    = $value->VENTA_SIN_IVA;
            $datos_a_insertar[$key]['VENTA_CON_IVA']    = $value->VENTA_CON_IVA;
            $datos_a_insertar[$key]['AVG_SIN_IVA']      = $value->AVG_SIN_IVA;       
            $datos_a_insertar[$key]['AVG_CON_IVA']      = $value->AVG_CON_IVA;  
            $datos_a_insertar[$key]['MES']              = $mes;       
            $datos_a_insertar[$key]['ANNIO']            = $anio;       
        } 

        $response = InnovaEstadisticas::insert($datos_a_insertar); 

        return $response;
    }


}
