<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\exportacion_model;

class exportacion_controller extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function agregarDatosASession() {
        $request = Request();
        $ApplicationVersion = new \git_version();
        $company = Company::where('id',$request->session()->get('company_id'))->first();
        $request->session()->put('ApplicationVersion', $ApplicationVersion::get());
        $request->session()->put('companyName', $company->nombre);
    }

    public function index() {
        $this->agregarDatosASession();

        $data = array(
            'page'              => 'Inventario',
            'name'              => 'GUMA@NET',
            'hideTransaccion'   => ''
        );

        return view('pages.exportacion', $data);
    }

    public function getVentasExportacion(Request $request) {

        $f1 = $request->input('f1');
        $f2 = $request->input('f2');

        

        if (!$f1) {
            return response()->json(false);
        }

        $obj = exportacion_model::getVentasExportacion($f1, $f2);
        return response()->json($obj);

    }
    public function getHistorialFactura(Request $request){
        if($request->isMethod('post')) {
            $obj = exportacion_model::HistorialFactura($request->input('factura'));
            return response()->json($obj);
        }
    }    

    // GUARDA LA FACTURA ANULADA
    public function AnularFactura(Request $request)
    {
        $obj = exportacion_model::AnularFactura($request);
        return response()->json($obj);
    }

}
