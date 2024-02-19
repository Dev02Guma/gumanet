<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReOrderPoint extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_gnet_reorder_master";

    public static function getArticulo() 
    {
        $array = [];
        $Articulos = ReOrderPoint::all();
        foreach ($Articulos as $key => $a) {
            $array[$key] = [
                "ARTICULO"                  => $a->ARTICULO,
                "DESCRIPCION"               => strtoupper($a->DESCRIPCION),
                "VENCE_MENOS_IGUAL_12"      => number_format($a->VENCE_MENOS_IGUAL_12,2),
                "VENCE_MAS_IGUAL_7"         => number_format($a->VENCE_MAS_IGUAL_7,2),
                "LOTE_MAS_PROX_VENCER"      => $a->LOTE_MAS_PROX_VENCER,
                "EXIT_LOTE_PROX_VENCER"     => number_format($a->EXIT_LOTE_PROX_VENCER,2),
                "ROTACION_PREVISTA"         => "N/D",
            ];
        }

        return $array;
    }
    public static function getDataGrafica($Articulos) {

        $array = array();

        $Sales = ReOrderSales::WHERE('ARTICULO',$Articulos)->first();
        for ($i=1; $i <= 12; $i++) { 
            $array[$i] = [
                "Mes"                 => "Mes".$i,
                "data" => (float) number_format(floatval($Sales->$i),2,".",""),
            ];
        }

        return $array;
    }
}
