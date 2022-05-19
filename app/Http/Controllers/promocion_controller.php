<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\promocion_model;

class promocion_controller extends Controller {
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

        return view('pages.promocion', $data);
    }

    public function getPromocion(Request $request) {

        $f1 = $request->input('f1');
        $f2 = $request->input('f2');

        

        if (!$f1) {
            return response()->json(false);
        }

        $obj = promocion_model::getPromocion($f1, $f2);
        return response()->json($obj);

    }
    public function getHistorialFactura(Request $request){
        if($request->isMethod('post')) {
            $obj = promocion_model::HistorialFactura($request->input('factura'));
            return response()->json($obj);
        }
    }
    public function getResumen(Request $request) {

        $f1 = $request->input('f1');
        $f2 = $request->input('f2');

        

        if (!$f1) {
            return response()->json(false);
        }

        $obj = promocion_model::getResumen($f1, $f2);
        return response()->json($obj);

    }


}
