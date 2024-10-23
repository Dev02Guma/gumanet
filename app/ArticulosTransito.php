<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ArticulosTransito extends Model
{
    
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_articulos_transito";
    protected $primaryKey = 'Id_transito';
    protected $keyType    = 'string';

    protected $fillable = [
        'Articulo',
        'Descripcion',
        'fecha_estimada',
        'fecha_pedido',
        'documento',
        'cantidad',
        'mercado',
        'mific',
        'observaciones',
        'estado_compra',
        'Nuevo',
        'Precio_mific_farmacia',
        'Precio_mific_public',
        'cantidad_pedido',
        'cantidad_transito'
    ];


    public static function SaveTransitoExcel(Request $request) 
    {
        if ($request->ajax()) {
            try {
                $datos_a_insertar = array();    
                ArticulosTransito::truncate();
                foreach ($request->input('datos') as $k => $v) 
                {
                    $v['CANTIDAD'] = str_replace(',', '', $v['CANTIDAD']);
                    $Articulo = ($v['ARTICULO'] == 'N/D' || $v['ARTICULO'] == 'N/A' || is_numeric(intval($v['ARTICULO']) == false)) ? mt_rand(10000000, 99999999).'-N' : $v['ARTICULO'] ;

                    $datos_a_insertar[$k] = [
                        'Articulo'		    => $Articulo,
                        'Descripcion'		=> strtoupper($v['DESCRIPC']),
                        'cantidad'		    => number_format((float)$v['CANTIDAD'], 2,'.',''),
                        'fecha_pedido'		=> $v['dtPedido'],
                        'fecha_estimada'	=> (strpos($v['dtEstimada'], 'N/') === false) ? $v['dtEstimada'] : null ,
                        'mercado'		    => strtoupper($v['Mercado']),
                        'mific'			    => strtoupper($v['Mific']),
                        'documento'		    => $v['Documento'],
                        'observaciones'		=> $v['Comment'],
                        'Nuevo'		        => 'N',
                        'Precio_mific_farmacia'      =>$v['Pre_MIFIC_F'],
                        'Precio_mific_public'      =>$v['Pre_MIFIC_P'],
                    ];
                }
                $response = ArticulosTransito::insert($datos_a_insertar); 
                return $response;
                
            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }
    }
    
    public static function getTransitoConCodigo() 
    {

        $Array    = array();
        $result = ArticulosTransito::where('ARTICULO', 'NOT LIKE', '%-N%')->get();
        
        foreach ($result as $k => $v) {
            $Array[$k] = [
                'ID'                => $v['Id_transito'],
                'ARTICULO'          => $v['Articulo'],
                'DESCRIPCION'       => strtoupper($v['Descripcion']),
                'FECHA_ESTIMADA'    => ($v['fecha_estimada']== null) ? 'N/D' : \Date::parse($v['fecha_estimada'])->format('D, M d, Y') ,
                'FECHA_PEDIDO'      => ($v['fecha_pedido']== null) ? 'N/D' : \Date::parse($v['fecha_pedido'])->format('D, M d, Y') ,
                'PEDIDO'            => number_format($v['cantidad_pedido'], 0),
                'TRANSITO'          => number_format($v['cantidad_transito'], 0),
                'CANTIDAD'          => number_format($v['cantidad'], 0),
                'MERCADO'           => strtoupper($v['mercado']),
            ];        
        }        

        return $Array;
    }
    public static function getTransitoSinCodigo() 
    {
        $Array    = array();
        $result = ArticulosTransito::where('ARTICULO', 'LIKE', '%-N%')->get();

        foreach ($result as $k => $v) {
            $Array[] = [
                'ID'                => $v['Id_transito'],
                'ARTICULO'          => $v->Articulo,
                'DESCRIPCION'       => strtoupper($v['Descripcion']),
                'FECHA_ESTIMADA'    => ($v['fecha_estimada']== null) ? 'N/D' : \Date::parse($v['fecha_estimada'])->format('D, M d, Y') ,
                'FECHA_PEDIDO'      => ($v['fecha_pedido']== null) ? 'N/D' : \Date::parse($v['fecha_pedido'])->format('D, M d, Y') ,
                'CANTIDAD'          => number_format($v['cantidad'], 0),
                'MERCADO'           => strtoupper($v['mercado']),
            ];        
        }

        
        return $Array;
    }

    public static function DeleteArticuloTransito(Request $request)
    {
        if ($request->ajax()) {
            try {                
                $NumRow     = $request->NumRow;

                $response = ArticulosTransito::WHERE('Id_transito', $NumRow)->delete();
                return response()->json($response);

            } catch (Exception $e) {
                $mensaje =  'Excepción capturada: ' . $e->getMessage() . "\n";
                return response()->json($mensaje);
            }
        }
    }

}
