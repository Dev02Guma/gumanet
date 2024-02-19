<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\ReOrderPoint;

class ReOrderPointController extends Controller
{
    public function ReOrderPoint()
    {  
        $data = array(
            'page'		=> 'Reorder Point',
            'name'		=> 'GUMA@NET',
            'hideTransaccion' => ''
        );
        return view('pages.ReOrderPoint.Home', $data);
    }

    public function getData() {
		$obj = ReOrderPoint::getArticulo();
		return response()->json($obj);
    }
    public function getDataGrafica($Articulos) {
    $obj = ReOrderPoint::getDataGrafica($Articulos);
    return response()->json($obj);
    }
}
