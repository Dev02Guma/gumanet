<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\DashboardInnova; 

class DashboardInnovaController extends Controller
{
    public function getDataInnova(Request $request)
    {
        $Metricas_Actuales = DashboardInnova::getActuales($request);
        $Metricas_Comparativas = DashboardInnova::getComparativas($request);
        $Metricas_YTD = DashboardInnova::getComparativasYTD($request);


        $Metricas = [
            'ACTUAL'        => $Metricas_Actuales,
            'COMPARATIVA'   => $Metricas_Comparativas,
            'COMPARATIVAYTD'=> $Metricas_YTD
        ];
        return response()->json($Metricas);
    }
    public function getDetallesSKUCliente(Request $request)
    {
        $data = DashboardInnova::getDetallesSKUCliente($request);
        return response()->json($data);
    }

    public function ExportToExcel(Request $request) {
        $obj = DashboardInnova::ExportToExcel($request);
        return $obj;
    }
    
}