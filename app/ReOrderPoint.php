<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

        $FechaIni   = date('Y-m-d 00:00:00.000', strtotime('-12 months', strtotime($startOfMonth)));
        $FechaEnd   = date('Y-m-d 00:00:00.000', strtotime($currentDate . ' -1 days'));
        $DiaActual  = (int) date('d', strtotime($FechaEnd));

        // Ejecutar el primer procedimiento almacenado
        DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.pr_calc_reorder_factura_linea ?, ?", [$FechaIni, $FechaEnd]);

        // Ejecutar el segundo procedimiento almacenado
        DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.pr_calc_reorder_factura_linea_ca ?", [$FechaEnd]);

        // Ejecutar el tercer procedimiento almacenado
        DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.sp_Calc_12_month_reorder_point ?, ?, ?", [$FechaIni, $FechaEnd, $DiaActual]);

    }

    public static function getArticulo() 
    {
        $array = [];
        $Articulos = ReOrderPoint::WHERE('VALUACION','!=',"0")->get();
        foreach ($Articulos as $key => $a) {
            $array[$key] = [
                "ARTICULO"                  => '<a href="#!" onclick="getDetalleArticulo('."'".$a->ARTICULO."'".', '."'".strtoupper($a->DESCRIPCION)."'".')" >'.$a->ARTICULO.'</a>',
                "DESCRIPCION"               => strtoupper($a->DESCRIPCION),
                "VENCE_MENOS_IGUAL_12"      => number_format($a->VENCE_MENOS_IGUAL_12,2),
                "VENCE_MAS_IGUAL_7"         => number_format($a->VENCE_MAS_IGUAL_7,2),
                "LOTE_MAS_PROX_VENCER"      => date("d-m-Y", strtotime($a->LOTE_MAS_PROX_VENCER)),
                "EXIT_LOTE_PROX_VENCER"     => number_format($a->EXIT_LOTE_PROX_VENCER,2),
                "LEADTIME"                  => $a->LEADTIME,
                "EJECUTADO_UND_YTD"         => number_format($a->EJECUTADO_UND_YTD,2),
                "VENTAS_YTD"                => number_format($a->VENTAS_YTD,2),
                "CONTRIBUCION_YTD"          => number_format($a->CONTRIBUCION_YTD,2),
                "DEMANDA_ANUAL_CA_NETA"     => number_format($a->DEMANDA_ANUAL_CA_NETA,2),
                "DEMANDA_ANUAL_CA_AJUSTADA" => number_format($a->DEMANDA_ANUAL_CA_AJUSTADA,2),
                "FACTOR"                    => number_format($a->FACTOR,2),
                "LIMITE_LOGISTICO_MEDIO"    => number_format($a->LIMITE_LOGISTICO_MEDIO,2),
                "CLASE"                     => $a->CLASE,
                "VALUACION"                 => $a->VALUACION,
                "CONTRIBUCION"              => number_format($a->CONTRIBUCION,2),
                "PEDIDO"                    => number_format($a->PEDIDO,2),
                "TRANSITO"                  => number_format($a->TRANSITO,2),
                "MOQ"                       => number_format($a->MOQ,2),
                "ESTIMACION_SOBRANTES_UND"  => number_format($a->ESTIMACION_SOBRANTES_UND,2),
                "REORDER1"                  => number_format($a->REORDER1,2),
                "REORDER"                   => number_format($a->REORDER,2),
                "CANTIDAD_ORDENAR"          => number_format($a->CANTIDAD_ORDENAR,2),
                "IS_CA"                     => $a->IS_CA,
                "ROTACION_CORTA"            => number_format($a->ROTACION_CORTA, 2),
                "ROTACION_MEDIA"            => number_format($a->ROTACION_MEDIA, 2),
                "ROTACION_LARGA"            => number_format($a->ROTACION_LARGA, 2),
                "ULTIMO_COSTO_USD"          => number_format($a->ULTIMO_COSTO_USD, 2),
                "COSTO_PROMEDIO_USD"        => number_format($a->COSTO_PROMEDIO_USD, 2),
                "UPDATED_AT"                => substr($a->FechaFinal, 0, 10)
            ];
        }

        return $array;
    }
    public static function getDataGrafica($Articulos) {

        $array = array();

        $Sales = ReOrderPoint::WHERE('ARTICULO',$Articulos)->first();
        
        $array["LEADTIME"] = number_format($Sales->LEADTIME,2);
        $array["DEMANDA_ANUAL_CA_NETA"] = number_format($Sales->DEMANDA_ANUAL_CA_NETA,2);
        $array["DEMANDA_ANUAL_CA_AJUSTADA"] = number_format($Sales->DEMANDA_ANUAL_CA_AJUSTADA,2);
        $array["LIMITE_LOGISTICO_MEDIO"] = number_format($Sales->LIMITE_LOGISTICO_MEDIO,2);
        $array["CONTRIBUCION"] = number_format($Sales->CONTRIBUCION,2);

        $array["REORDER1"] = number_format($Sales->REORDER1,2);
        $array["REORDER"] = number_format($Sales->REORDER,2);
        $array["CANTIDAD_ORDENAR"] = number_format($Sales->CANTIDAD_ORDENAR,2);
        $array["MOQ"] = number_format($Sales->MOQ, 2);
        $array["PEDIDO"] = number_format($Sales->PEDIDO, 2);
        $array["TRANSITO"] = number_format($Sales->TRANSITO, 2);
        $array["CLASE"] = $Sales->CLASE;

        $array["ROTACION_CORTA"] = number_format($Sales->ROTACION_CORTA, 2);
        $array["ROTACION_MEDIA"] = number_format($Sales->ROTACION_MEDIA, 2);
        $array["ROTACION_LARGA"] = number_format($Sales->ROTACION_LARGA, 2);
        
        $array["COSTO_PROMEDIO_USD"] = number_format($Sales->COSTO_PROMEDIO_USD, 2);
        $array["ULTIMO_COSTO_USD"] = number_format($Sales->ULTIMO_COSTO_USD, 2);
        $array["VENTAS_YTD"] = number_format($Sales->VENTAS_YTD, 2);
        $array["CONTRIBUCION_YTD"] = number_format($Sales->CONTRIBUCION_YTD,2);
        
        for ($i=1; $i <= 12; $i++) { 
            $array["VENTAS"][$i] = [
                "Mes"                 => "Mes".$i,
                "data" =>  (isset($Sales) && !empty($Sales->$i)) ? (float) number_format($Sales->$i,2,".","") : 0 
            ];
        }

        return $array;
    }
}
