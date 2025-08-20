<?php
namespace App\Http\Controllers;

use App\ArticuloPotencialDiscasa;
use App\ArticuloMOQ;
use Illuminate\Http\Request;

class TransitoController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    } 
    public function saveInfoTransito(Request $request)
    {
        $ARTICULO       = $request->Articulo;
        $ARTICULO_MOQ   = $request->MOQ;
        $POTENCIAL      = $request->POTENCIAL;

        $registro = ArticuloPotencialDiscasa::updateOrCreate(
            ['ARTICULO' => $ARTICULO], 
            [
                'POTENCIAL_CA' => $POTENCIAL,
            ]
        );

        $registro = ArticuloMOQ::updateOrCreate(
            ['ARTICULO' => $ARTICULO], 
            [
                'MOQ_REVISADO' => $ARTICULO_MOQ,
            ]
        );

        return response()->json([
            'success' => true,
            'data'    => $registro,
            'message' => 'Registro guardado correctamente'
        ]);

        
        
        
    }  
}