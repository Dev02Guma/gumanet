<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Presupuesto extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_presupuesto_umk";

    public static function getEjecucionPresupuesto(){
        $ventas = "";
        $total = 0;
        $json = array();
        
        $ventas = Presupuesto::selectRaw('CLASE_PRODUCTO, CANAL2, SUM(PRECIO_TOTAL) as total_precio')
            ->groupBy('CLASE_PRODUCTO', 'CANAL2')
            ->get();

        foreach($ventas as $v){
            $total += $v->total_precio;
            $json[$v->CLASE_PRODUCTO]['EJECUTADO'] = floatval($v->total_precio); 
        }        
        $json['PRIMARIOS UMK']['PRESUPUESTO'] = 12887968; 
        $json['SECUNDARIOS']['PRESUPUESTO'] = 2785449; 
        $json['NUEVOS']['PRESUPUESTO'] = 0;
        $json['ONCO']['PRESUPUESTO'] = 101900; 
        $json['GPHARMA']['PRESUPUESTO'] = 1229265;
        $json['CRUZ AZUL']['PRESUPUESTO'] = 3020491; 
        $json['LICITACIONES']['PRESUPUESTO'] = 9333333;

        $json['VENTAS_PRIVADO']['EJECUTADO'] = $json['PRIMARIOS UMK']['EJECUTADO'] + $json['SECUNDARIOS']['EJECUTADO'] + $json['NUEVOS']['EJECUTADO'];
        $json['VENTAS_PRIVADO']['PRESUPUESTO'] = $json['PRIMARIOS UMK']['PRESUPUESTO'] + $json['SECUNDARIOS']['PRESUPUESTO'] + $json['NUEVOS']['PRESUPUESTO'];
        
        $json['VENTAS_PROYECTOS']['EJECUTADO'] = $json['ONCO']['EJECUTADO'] + $json['GPHARMA']['EJECUTADO'];
        $json['VENTAS_PROYECTOS']['PRESUPUESTO'] = $json['ONCO']['PRESUPUESTO'] + $json['GPHARMA']['PRESUPUESTO'];
        
        $json['VENTAS_INSTITUCIONES']['EJECUTADO'] = $json['CRUZ AZUL']['EJECUTADO'] + $json['LICITACIONES']['EJECUTADO'];
        $json['VENTAS_INSTITUCIONES']['PRESUPUESTO'] = $json['CRUZ AZUL']['PRESUPUESTO'] + $json['LICITACIONES']['PRESUPUESTO'];

        $json['VENTAS_BRUTAS']['EJECUTADO'] = $total;
        
        //dd($json);
        return $json;

    }

    public static function actualizarEjecucionPresupuesto($mes, $ano){
        $update = DB::connection('sqlsrv')->select('EXEC PRODUCCION.dbo.pr_calcular_canal_contribucion ? ?', [$mes, $ano]);
        return $update;
    }
}