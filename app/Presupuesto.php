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
        
        $ventas = Presupuesto::selectRaw('CLASE_PRODUCTO, CANAL2, SUM(PRECIO_TOTAL) as total_precio, SUM(CONTRIBUCION) as contribucion' )
            ->where('MES','!=',0)
            ->groupBy('CLASE_PRODUCTO', 'CANAL2')
            ->get();
        
        $nombreMes = Presupuesto::NameMonth();

        foreach($ventas as $v){
            $total += $v->total_precio;
            $json[$v->CLASE_PRODUCTO]['VENTA'] = floatval($v->total_precio); 
            $json[$v->CLASE_PRODUCTO]['CONTRIBUCION'] = floatval($v->contribucion); 
        }        
        $json['PRIMARIOS UMK']['PRESUPUESTO'] = 12887968; 
        $json['SECUNDARIOS']['PRESUPUESTO'] = 2785449; 
        $json['NUEVOS']['PRESUPUESTO'] = 0;
        $json['ONCO']['PRESUPUESTO'] = 101900; 
        $json['GPHARMA']['PRESUPUESTO'] = 1229265;
        $json['CRUZ AZUL']['PRESUPUESTO'] = 3020491; 
        $json['LICITACIONES']['PRESUPUESTO'] = 9333333;

        $json['PRIMARIOS UMK']['PRESUPUESTO_CONTRIB'] = 6351032; 
        $json['SECUNDARIOS']['PRESUPUESTO_CONTRIB'] = 1290123; 
        $json['NUEVOS']['PRESUPUESTO_CONTRIB'] = 0;
        $json['ONCO']['PRESUPUESTO_CONTRIB'] = 562021; 
        $json['GPHARMA']['PRESUPUESTO_CONTRIB'] = 245772;
        $json['CRUZ AZUL']['PRESUPUESTO_CONTRIB'] = 1469233; 
        $json['LICITACIONES']['PRESUPUESTO_CONTRIB'] = 1680000;

        $json['VENTAS_BRUTAS']['EJECUTADO'] = $total;
        $json['VENTAS_BRUTAS']['PRESUPUESTO'] = $json['PRIMARIOS UMK']['PRESUPUESTO'] + $json['SECUNDARIOS']['PRESUPUESTO'] + $json['NUEVOS']['PRESUPUESTO'] + $json['ONCO']['PRESUPUESTO'] + $json['GPHARMA']['PRESUPUESTO'] + $json['CRUZ AZUL']['PRESUPUESTO'] + $json['LICITACIONES']['PRESUPUESTO'];

        // CONSOLIDADOS VENTAS
        $json['VENTAS_PRIVADO']['EJECUTADO']                = $json['PRIMARIOS UMK']['VENTA'] + $json['SECUNDARIOS']['VENTA'] + $json['NUEVOS']['VENTA'];
        $json['VENTAS_PRIVADO']['EJECUTADO_PORCIENTO']      = ($json['VENTAS_PRIVADO']['EJECUTADO']/$json['VENTAS_BRUTAS']['EJECUTADO'])*100;
        $json['VENTAS_PRIVADO']['PRESUPUESTO']              = $json['PRIMARIOS UMK']['PRESUPUESTO'] + $json['SECUNDARIOS']['PRESUPUESTO'] + $json['NUEVOS']['PRESUPUESTO'];
        $json['VENTAS_PRIVADO']['PRESUPUESTO_PORCIENTO']    = ($json['VENTAS_PRIVADO']['PRESUPUESTO'] / $json['VENTAS_BRUTAS']['PRESUPUESTO']) * 100;
        $json['VENTAS_PRIVADO']['DIF_ABSOLUTA']             = $json['VENTAS_PRIVADO']['EJECUTADO'] - $json['VENTAS_PRIVADO']['PRESUPUESTO'];
        $json['VENTAS_PRIVADO']['DIF_RELATIVA']             = ($json['VENTAS_PRIVADO']['DIF_ABSOLUTA'] / $json['VENTAS_PRIVADO']['PRESUPUESTO']) * 100;

        $json['VENTAS_PROYECTOS']['EJECUTADO']              = $json['ONCO']['VENTA'] + $json['GPHARMA']['VENTA'];
        $json['VENTAS_PROYECTOS']['EJECUTADO_PORCIENTO']    = ($json['VENTAS_PROYECTOS']['EJECUTADO']/$json['VENTAS_BRUTAS']['EJECUTADO'])*100;
        $json['VENTAS_PROYECTOS']['PRESUPUESTO']            = $json['ONCO']['PRESUPUESTO'] + $json['GPHARMA']['PRESUPUESTO'];
        $json['VENTAS_PROYECTOS']['PRESUPUESTO_PORCIENTO']  = ($json['VENTAS_PROYECTOS']['PRESUPUESTO'] / $json['VENTAS_BRUTAS']['PRESUPUESTO']) * 100;
        $json['VENTAS_PROYECTOS']['DIF_ABSOLUTA']           = $json['VENTAS_PROYECTOS']['EJECUTADO'] - $json['VENTAS_PROYECTOS']['PRESUPUESTO'];
        $json['VENTAS_PROYECTOS']['DIF_RELATIVA']           = ($json['VENTAS_PROYECTOS']['DIF_ABSOLUTA'] / $json['VENTAS_PROYECTOS']['PRESUPUESTO']) * 100;
        
        $json['VENTAS_INSTITUCIONES']['EJECUTADO']              = $json['CRUZ AZUL']['VENTA'] + $json['LICITACIONES']['VENTA'];
        $json['VENTAS_INSTITUCIONES']['EJECUTADO_PORCIENTO']    = ($json['VENTAS_INSTITUCIONES']['EJECUTADO']/$json['VENTAS_BRUTAS']['EJECUTADO'])*100;
        $json['VENTAS_INSTITUCIONES']['PRESUPUESTO']            = $json['CRUZ AZUL']['PRESUPUESTO'] + $json['LICITACIONES']['PRESUPUESTO'];
        $json['VENTAS_INSTITUCIONES']['PRESUPUESTO_PORCIENTO']  = ($json['VENTAS_INSTITUCIONES']['PRESUPUESTO'] / $json['VENTAS_BRUTAS']['PRESUPUESTO']) * 100;
        $json['VENTAS_INSTITUCIONES']['DIF_ABSOLUTA']           = $json['VENTAS_INSTITUCIONES']['EJECUTADO'] - $json['VENTAS_INSTITUCIONES']['PRESUPUESTO'];
        $json['VENTAS_INSTITUCIONES']['DIF_RELATIVA']           = ($json['VENTAS_INSTITUCIONES']['DIF_ABSOLUTA'] / $json['VENTAS_INSTITUCIONES']['PRESUPUESTO']) * 100;

        // CONSOLIDADO CONTRIBUCIONES
        $json['VENTAS_PRIVADO']['EJECUTADO_CONTRIB']                = $json['PRIMARIOS UMK']['CONTRIBUCION'] + $json['SECUNDARIOS']['CONTRIBUCION'] + $json['NUEVOS']['CONTRIBUCION'];
        $json['VENTAS_PRIVADO']['EJECUTADO_PORCIENTO_CONTRIB']      = ($json['VENTAS_PRIVADO']['EJECUTADO_CONTRIB']/$json['VENTAS_BRUTAS']['EJECUTADO'])*100;
        $json['VENTAS_PRIVADO']['PRESUPUESTO_CONTRIB']              = $json['PRIMARIOS UMK']['PRESUPUESTO_CONTRIB'] + $json['SECUNDARIOS']['PRESUPUESTO_CONTRIB'] + $json['NUEVOS']['PRESUPUESTO_CONTRIB'];
        $json['VENTAS_PRIVADO']['PRESUPUESTO_PORCIENTO_CONTRIB']    = ($json['VENTAS_PRIVADO']['PRESUPUESTO_CONTRIB'] / $json['VENTAS_BRUTAS']['PRESUPUESTO']) * 100;
        $json['VENTAS_PRIVADO']['DIF_ABSOLUTA_CONTRIB']             = $json['VENTAS_PRIVADO']['EJECUTADO_CONTRIB'] - $json['VENTAS_PRIVADO']['PRESUPUESTO_CONTRIB'];
        $json['VENTAS_PRIVADO']['DIF_RELATIVA_CONTRIB']             = ($json['VENTAS_PRIVADO']['DIF_ABSOLUTA_CONTRIB'] / $json['VENTAS_PRIVADO']['PRESUPUESTO_CONTRIB']) * 100;

        $json['VENTAS_PROYECTOS']['EJECUTADO_CONTRIB']              = $json['ONCO']['CONTRIBUCION'] + $json['GPHARMA']['CONTRIBUCION'];
        $json['VENTAS_PROYECTOS']['EJECUTADO_PORCIENTO_CONTRIB']    = ($json['VENTAS_PROYECTOS']['EJECUTADO_CONTRIB']/$json['VENTAS_BRUTAS']['EJECUTADO'])*100;
        $json['VENTAS_PROYECTOS']['PRESUPUESTO_CONTRIB']            = $json['ONCO']['PRESUPUESTO_CONTRIB'] + $json['GPHARMA']['PRESUPUESTO_CONTRIB'];
        $json['VENTAS_PROYECTOS']['PRESUPUESTO_PORCIENTO_CONTRIB']  = ($json['VENTAS_PROYECTOS']['PRESUPUESTO_CONTRIB'] / $json['VENTAS_BRUTAS']['PRESUPUESTO']) * 100;
        $json['VENTAS_PROYECTOS']['DIF_ABSOLUTA_CONTRIB']           = $json['VENTAS_PROYECTOS']['EJECUTADO_CONTRIB'] - $json['VENTAS_PROYECTOS']['PRESUPUESTO_CONTRIB'];
        $json['VENTAS_PROYECTOS']['DIF_RELATIVA_CONTRIB']           = ($json['VENTAS_PROYECTOS']['DIF_ABSOLUTA_CONTRIB'] / $json['VENTAS_PROYECTOS']['PRESUPUESTO_CONTRIB']) * 100;
        
        $json['VENTAS_INSTITUCIONES']['EJECUTADO_CONTRIB']              = $json['CRUZ AZUL']['CONTRIBUCION'] + $json['LICITACIONES']['CONTRIBUCION'];
        $json['VENTAS_INSTITUCIONES']['EJECUTADO_PORCIENTO_CONTRIB']    = ($json['VENTAS_INSTITUCIONES']['EJECUTADO_CONTRIB']/$json['VENTAS_BRUTAS']['EJECUTADO'])*100;
        $json['VENTAS_INSTITUCIONES']['PRESUPUESTO_CONTRIB']            = $json['CRUZ AZUL']['PRESUPUESTO_CONTRIB'] + $json['LICITACIONES']['PRESUPUESTO_CONTRIB'];
        $json['VENTAS_INSTITUCIONES']['PRESUPUESTO_PORCIENTO_CONTRIB']  = ($json['VENTAS_INSTITUCIONES']['PRESUPUESTO_CONTRIB'] / $json['VENTAS_BRUTAS']['PRESUPUESTO']) * 100;
        $json['VENTAS_INSTITUCIONES']['DIF_ABSOLUTA_CONTRIB']           = $json['VENTAS_INSTITUCIONES']['EJECUTADO_CONTRIB'] - $json['VENTAS_INSTITUCIONES']['PRESUPUESTO_CONTRIB'];
        $json['VENTAS_INSTITUCIONES']['DIF_RELATIVA_CONTRIB']           = ($json['VENTAS_INSTITUCIONES']['DIF_ABSOLUTA_CONTRIB'] / $json['VENTAS_INSTITUCIONES']['PRESUPUESTO_CONTRIB']) * 100;

        $json['ANIO'] = Presupuesto::selectRaw('YEAR(FECHA_FACTURA) as anio')->distinct()->pluck('anio')->first();
        $json['MES'] = $nombreMes;
        
        //dd($nombreMes);
        return $json;

    }

    public static function getPresupuestoAnual(){
        $ventas = "";
        $totalVenta = $totalContribucion = 0;
        $json = array();
        
        $ventas = Presupuesto::selectRaw('CLASE_PRODUCTO, CANAL2, SUM(PRECIO_TOTAL) as total_precio, SUM(CONTRIBUCION) as contribucion' )
            ->groupBy('CLASE_PRODUCTO', 'CANAL2')
            ->get();
        
        foreach($ventas as $v){
            $totalVenta += $v->total_precio;
            $totalContribucion += $v->contribucion;
            $json[$v->CLASE_PRODUCTO]['VENTA'] = floatval($v->total_precio); 
            $json[$v->CLASE_PRODUCTO]['CONTRIBUCION'] = floatval($v->contribucion); 
        }        
        $json['PRIMARIOS UMK']['PRESUPUESTO'] = 154655620; 
        $json['SECUNDARIOS']['PRESUPUESTO'] = 33425391; 
        $json['NUEVOS']['PRESUPUESTO'] = 0;
        $json['ONCO']['PRESUPUESTO'] = 1222795; 
        $json['GPHARMA']['PRESUPUESTO'] = 14751185;
        $json['CRUZ AZUL']['PRESUPUESTO'] = 36245895; 
        $json['LICITACIONES']['PRESUPUESTO'] = 112000000;

        $json['VENTAS_PRIVADO']['EJECUTADO'] = $json['PRIMARIOS UMK']['VENTA'] + $json['SECUNDARIOS']['VENTA'] + $json['NUEVOS']['VENTA'];
        $json['VENTAS_PRIVADO']['PRESUPUESTO'] = $json['PRIMARIOS UMK']['PRESUPUESTO'] + $json['SECUNDARIOS']['PRESUPUESTO'] + $json['NUEVOS']['PRESUPUESTO'];
        
        $json['VENTAS_PROYECTOS']['EJECUTADO'] = $json['ONCO']['VENTA'] + $json['GPHARMA']['VENTA'];
        $json['VENTAS_PROYECTOS']['PRESUPUESTO'] = $json['ONCO']['PRESUPUESTO'] + $json['GPHARMA']['PRESUPUESTO'];
        
        $json['VENTAS_INSTITUCIONES']['EJECUTADO'] = $json['CRUZ AZUL']['VENTA'] + $json['LICITACIONES']['VENTA'];
        $json['VENTAS_INSTITUCIONES']['PRESUPUESTO'] = $json['CRUZ AZUL']['PRESUPUESTO'] + $json['LICITACIONES']['PRESUPUESTO'];

        $json['VENTAS_BRUTAS']['EJECUTADO'] = $totalVenta;
        
        //dd($json);
        return $json;

    }

    public static function actualizarEjecucionPresupuesto($mes, $ano){
        DB::connection('sqlsrv')->statement('EXEC PRODUCCION.dbo.pr_calcular_presupuesto_umk '.$mes.', '.$ano);        
    }

    public static function NameMonth()
    {
        $date = new \DateTime(Presupuesto::distinct()->where('MES','!=',0)->pluck('FECHA_FACTURA')->first());

        $month = $date->format('M');
        
        $month = str_replace(
            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ['ENERO', 'FEBRERO', 'MAYO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'],
            $month
        );

        return $month;
    }

}