<?php

namespace App\Http\Controllers;

use App\InnovaKardex;
use App\InnovaModel;
use App\InnovaEstadisticas;
use Illuminate\Http\Request;

class InnovaController extends Controller
{
    public function inventarioInnova(){
        $inventario = InnovaModel::getAll();
        return view('pages.inventarioINN', compact('inventario'));
    }

    public function getKerdex(Request $request){
        $ini = $request->ini;
        $end = $request->end;
       
        $kardex = InnovaKardex::getReporteKardex($ini, $end);
        return response()->json($kardex);
    }

    public function getResumenKardex(){
        $resumen = InnovaKardex::getResumenKardex();
        return response()->json($resumen);
    }
    public function getMateriaPrima(){
        $resumen = InnovaKardex::getMateriaPrima();
        return response()->json($resumen);
    }

    public function getStatsInn(Request $request)
    {

        $dta[] = array(
            'dtVenta' => InnovaEstadisticas::getInnStatSale($request),
            'dtaRuta' => InnovaEstadisticas::getInnStatRuta($request)
        );
        return $dta;
    }

    public function saveInnStat()
    {
        InnovaEstadisticas::saveInnStat(7, 2023);
    }
}
