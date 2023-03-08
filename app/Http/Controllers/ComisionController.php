<?php

namespace App\Http\Controllers;

use App\Comision;
use Illuminate\Http\Request;

class ComisionController extends Controller
{
    public function index()
    {  
        return view('pages.Comiciones');
    }

    public function getDataComiciones(Request $request)
    {  
        $Mes   = $request->input('mes');
        $Anno   = $request->input('anno');

        $Comision = Comision::getData($Mes,$Anno);
        return response()->json($Comision);  
    }

    public function getHistoryItem(Request $request){
        $mes = $request->input('mes');
        $anno = $request->input('anno');
        $ruta = $request->input('ruta');

        $result = Comision::getHistoryItem($mes, $anno, $ruta);
        return response()->json($result);
    }
}
