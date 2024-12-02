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
        return view('pages.Presupuesto.Table',compact('presupuesto'));
    }
}