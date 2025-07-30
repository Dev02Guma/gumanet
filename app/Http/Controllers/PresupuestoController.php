<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Presupuesto;


class PresupuestoController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    public function Presupuesto()
    {
        $presupuesto = Presupuesto::getEjecucionPresupuesto();
        $presupuestoAnual = Presupuesto::getPresupuestoAnual();
        return view('pages.Presupuesto.Table',compact('presupuesto','presupuestoAnual'));
    }

    public function calcularPresupuesto(Request $request){
        $mes = $request->InputMeses;
        $anio = $request->InputAnio;

        Presupuesto::actualizarEjecucionPresupuesto($mes, $anio);
        return redirect('Presupuesto');
    }
}