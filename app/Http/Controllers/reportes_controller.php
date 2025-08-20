<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\reportes_model;

class reportes_controller extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    function index(){
        $clases     = reportes_model::claseTerapeutica();
        $articulos  = reportes_model::articulos();
        $clientes   = reportes_model::clientes();
        $rutas      = reportes_model::rutas();
        $Labs       = reportes_model::Laboratorio();
        $data = [
            'name' =>  'GUMA@NET',
            'page' => 'Ventas'
        ];
        return view('pages.reportes', compact('data', 'clases', 'articulos', 'clientes','rutas',"Labs"));
    }

    public function detalleVentas(Request $request) {
        if($request->isMethod('post')) {
            $obj = reportes_model::returndetalleVentas(
                $request->input('clase'),
                $request->input('cliente'),
                $request->input('Labs'),
                $request->input('articulo'),
                $request->input('mes'),
                $request->input('anio'),
                $request->input('ruta'));
            return response()->json($obj);
        }
    }

    public function getDetFactVenta(Request $request){

        if($request->isMethod('post')) {

            $Factura = $request->input('factura');
            $obj = reportes_model::returnDetFactVenta($Factura);

            return response()->json($obj);
        }
    }
}