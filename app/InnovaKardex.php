<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InnovaKardex extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_inventario_innova_kardex";

    

    public static function getKardex(Request $request)
    {
        $id = $request->input('ArticuloID');
        $d1 = $request->input('DateStart');
        $d2 = $request->input('DateEnd');
        return InnovaKardex::where('ID_ART ',$id)
                ->orderBy('ID', 'DESC')
                ->whereBetween('FECHA', [$d1, $d2])
                ->get();
    }

    public static function getReporteKardex($ini, $end)
    {
        $d1 = $ini;
        $d2 = $end;

        $json_arrays = array();
        $i = 0 ;

        try{
     
            $dtFechas = InnovaKardex::select('FECHA')
                    ->whereBetween('FECHA', [$d1, $d2])
                    ->groupBy('FECHA')
                    ->get();

            $json_arrays['header_date_count'] = count($dtFechas) ;

            foreach($dtFechas as $f){
                $json_arrays['header_date'][$i] = $f->FECHA;
                $i++;
            }
            $Rows = DB::connection('sqlsrv')->select('SET NOCOUNT ON ;EXEC PRODUCCION.dbo.gnet_calc_kardex '."'".$d1."'".','."'".$d2."'".",''" );
            foreach($Rows as $r){

                $RoleUsr = KardexUsuario::find($r->USUARIO);
            
                $json_arrays['header_date_rows'][$i]['ARTICULO'] = $r->ARTICULO;
                $json_arrays['header_date_rows'][$i]['DESCRIPCION'] = $r->DESCRIPCION;
                $json_arrays['header_date_rows'][$i]['UND'] = $r->UND;
                $json_arrays['header_date_rows'][$i]['USUARIO'] = $RoleUsr->rol->descripcion;

                
                foreach($json_arrays['header_date'] as $dtFecha => $valor){                    

                    $rows_in    = 'IN01_'.date('Ymd',strtotime($valor));
                    $rows_out   = 'OUT02_'.date('Ymd',strtotime($valor));
                    $rows_stock = 'STOCK03_'.date('Ymd',strtotime($valor));

                    $json_arrays['header_date_rows'][$i][$rows_in]      = number_format($r->$rows_in,2)  ;
                    $json_arrays['header_date_rows'][$i][$rows_out]     = number_format($r->$rows_out,2);
                    $json_arrays['header_date_rows'][$i][$rows_stock]   =  number_format($r->$rows_stock,2);

                }
                $i++;
            }
            return $json_arrays;
        }catch(Exception $e){
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }

    }

    public static function InitKardex(Request $request){
        try {
            $datos_a_insertar = array();    
            $Articulos = ArticuloInnova::getArticulos();
            InnovaKardex::where('USUARIO', Auth::id())->delete();
            foreach ($Articulos as $key => $val) {
                $datos_a_insertar[$key]['ID_ART']           = $val->ID;
                $datos_a_insertar[$key]['ARTICULO']         = $val->ARTICULO;
                $datos_a_insertar[$key]['DESCRIPCION']      = $val->DESCRIPCION;
                $datos_a_insertar[$key]['ENTRADA']          = 0;
                $datos_a_insertar[$key]['SALIDA']           = 0;
                $datos_a_insertar[$key]['STOCK']            = $val->CANTIDAD;
                $datos_a_insertar[$key]['TIPO_MOVIMIENTO']  = 'In';
                $datos_a_insertar[$key]['FECHA']            = date('Y-m-d');
                $datos_a_insertar[$key]['USUARIO']          = Auth::id();
                $datos_a_insertar[$key]['created_at']       = date('Y-m-d H:i:s');
                
            }
            InnovaKardex::insert($datos_a_insertar); 
            
        } catch (Exception $e) {
            $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
            return response()->json($mensaje);
        }
    }

    public static function getResumenKardex(){
        $json = array();

        $result = DB::connection('sqlsrv')->select('SELECT * FROM PRODUCCION.dbo.view_stats_inventario_innova');
    
        foreach($result as $row => $r){

            $Product = $r->Product;

            $articulos = ArticuloInnova::select('ARTICULO', 'DESCRIPCION')
            ->whereHas('clasificacion1', function ($query) use ($Product) {
                $query->where('DESCRIPCION', $Product);
            })
            ->where('Clasificacion_1', '>', 1)
            ->get()
            ->toArray();

            //5 ES EL PESO DEL BOLSON
            $JR_KG          = ($r->JR) / 5;

            //SE LE RESTA EL 8% DE MERMA UNA VES QUE PASA A PROCESO SECO
            $JR_KG_MERMA    = $JR_KG * 0.92;

            //SUMATORIA DE TOTAL ESTIMADO
            $TT_ESTIMADO    = $r->PT + $JR_KG_MERMA + $r->MP;
            

            $json[$row]['Product'] = $Product;
            $json[$row]['PT'] = $r->PT;
            $json[$row]['JR'] = $JR_KG_MERMA;
            $json[$row]['JR_KG'] = $r->JR;
            $json[$row]['MP'] = $r->MP;
            $json[$row]['TE'] = $TT_ESTIMADO;
            $json[$row]['AT'] = $articulos;
        }

        return $json;
    }
    public static function getMateriaPrima(){
        $json = array();

        $result = DB::connection('sqlsrv')->select('SELECT * FROM PRODUCCION.dbo.view_stats_materia_prima');
    
        foreach ($result as $key => $val) {
            $json[$key]['UND'] = 'KG';
            $json[$key]['BLANCO_IMPRESO']   = $val->BLANCO_IMPRESO;
            $json[$key]['BLANCO_MEZCLADO']  = $val->BLANCO_MEZCLADO;
            $json[$key]['TETRA_PACK']       = $val->TETRA_PACK;
            $json[$key]['TERMOMECANICO']    = $val->TERMOMECANICO;
            $json[$key]['PRENSA']           = $val->PRENSA;
            $json[$key]['CARTON']           = $val->CARTON;
            $json[$key]['FOLDER']           = $val->FOLDER;
            $json[$key]['COLOR']            = $val->COLOR;
        }

        return $json;
    }
}
