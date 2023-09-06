<?php

namespace App;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocionDetalle extends Model
{
    protected $table = "view_resumen_promocion";  
    protected $connection = 'mysql_stat';
    public static function getDetalles()
    {  

        //----------------------------------------------
        $i      = 0;
        $json   = array();
        $anno   = Date('Y');
        $nMes   = date('n');
        $meses = array('ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC');
        $Rows       = PromocionDetalle::where('anno', $anno)->get();
        

        $Articulos_promedio_anual      = DB::connection('sqlsrv')->select('SELECT * FROM PRODUCCION.dbo.tbl_promedio_articulos');


        foreach($Rows as $r){

            $iPromo     = Promocion::where('id',$Rows[$i]->id_promocion)->get();

            $Segmentos  = $iPromo[0]->Segmentos;
            $fecha_ini  = $iPromo[0]->original['fecha_ini'];
            $fecha_end  = $iPromo[0]->original['fecha_end'];
            $Articulos  = trim($r->Articulo) ;
            
            $index_key = array_search($r->Articulo, array_column($Articulos_promedio_anual, 'ARTICULO'));
            
            $strQuery   = 'EXEC PRODUCCION.dbo.fn_promocion_item_venta "'.$fecha_ini.'","'.$fecha_end.'","'.$Articulos.'" ';            
            $query      = DB::connection('sqlsrv')->select($strQuery);
            
            $sql        = 'EXEC PRODUCCION.dbo.fn_promocion_history_item_sale "'.$anno.'","'.$Articulos.'"';            
            $resp       = DB::connection('sqlsrv')->select($sql);

            $resp = json_decode(json_encode($resp), true);

            $Venta          = 0;
            $PromVenta      = 0;
            $VentaUND       = 0;
            $PromVentaUND   = 0;
            $VentaMesA      = 0;
            $VentaUNDMesA   = 0;
            $AVG_VLR        = $Articulos_promedio_anual[$index_key]->VENTA_NETA;
            $AVG_UND        = $Articulos_promedio_anual[$index_key]->PROMEDIO_CANTIDAD_FACT;
            $j              = 1;

            if (count($query )>0) {
                foreach($query as $item){
                    $Venta              += number_format($item->VAL,2,'.','');
                    $VentaUND           += number_format($item->UND,0,'.','');
                    //$VentaMesA          = number_format($query[0]->VentaMesActual,2,'.','');
                    //$VentaUNDMesA       = number_format($query[0]->UNDMesActual,0,'.','');

                    //$AVG_VLR           = number_format($query[0]->AVG_VALOR_LAST_YEAR,2,'.','');
                    //$AVG_UND           = number_format($query[0]->AVG_UND_LAST_YEAR,0,'.','');  
                }
            }

            if (count($resp) > 0) {
                $VentaMesA      = $resp[1][$meses[$nMes - 1]];
                $VentaUNDMesA   = $resp[0][$meses[$nMes - 1]];
            } else {
                $VentaMesA      = 0;
                $VentaUNDMesA   = 0;
            }
            


            $PromVenta      = ( $Venta !=0 ) ? ( $Venta / $r->ValMeta  ) * 100 : 0;
            $PromVentaUND   = ( $VentaUND !=0 ) ? ( $VentaUND / $r->MetaUnd  ) * 100 : 0;



            $json[$i]['id_promocion']       = $r->id_promocion;
            $json[$i]['Articulo']           = $r->Articulo;
            $json[$i]['Promocion']          = $iPromo[0]->Titulo;
            $json[$i]['fechaIni']           = $fecha_ini;
            $json[$i]['fechaFin']           = $fecha_end;
            $json[$i]['Descripcion']        = $r->Descripcion;
            $json[$i]['Precio']             = $r->precio;
            $json[$i]['NuevaBonificacion']  = $r->NuevaBonificacion;
            $json[$i]['ValorVinneta']       = $r->ValorVinneta;
            $json[$i]['ValMeta']            = $r->ValMeta;
            $json[$i]['MetaUnd']            = $r->MetaUnd;
            $json[$i]['Promedio_VAL']       = $AVG_VLR;
            $json[$i]['Promedio_UND']       = $AVG_UND;
            $json[$i]['Venta']              = $Venta;
            $json[$i]['PromVenta']          = $PromVenta;
            $json[$i]['VentaMActual']       = $VentaMesA;
            $json[$i]['VentaUNDMActual']    = $VentaUNDMesA;
            $json[$i]['VentaUND']           = $VentaUND;
            $json[$i]['PromVentaUND']       = $PromVentaUND;
            $i++;

        }
        return  $json;
    }

    public static function getPromoMes($articulo, $ini, $fin){

        $json = array();
        $anno = Date('Y');

        $strQuery   = 'EXEC PRODUCCION.dbo.fn_history_item_sale_promocion "'.$ini.'", "'.$fin.'", "'.$articulo.'" ';            
        $query      = DB::connection('sqlsrv')->select($strQuery);

        if(count($query) > 0){
            $json[] = $query;
        }
                
        return $json;
    }    
}
