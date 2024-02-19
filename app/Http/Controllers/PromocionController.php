<?php

namespace App\Http\Controllers;

use App\Promocion;
use App\PromocionDetalle;
use App\Vendedor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    public function getPromocion()
    {  
        $totalMv = $totalVv = $totalMu = $totalVu = 0;
        $Promociones   = PromocionDetalle::getDetalles();

        $instance = Carbon::createFromFormat('Y-m-d', Date('Y-m-d'));
        setlocale(LC_TIME, NULL);
        $mesActual = $instance->formatLocalized('%B');

        foreach($Promociones as $p){
            $totalMv += $p['ValMeta'];
            $totalVv += $p['Venta'];
            $totalMu += $p['MetaUnd'];
            $totalVu += $p['VentaUND'];
        }
        
        return view('pages.promocionesRuta',compact('Promociones','totalMv', 'totalVv', 'totalMu', 'totalVu', 'mesActual'));
    }

    public function getPromoMes(Request $request){
        $articulo = $request->articulo;
        $ini = $request->ini;
        $fin = $request->ends;

        $promo = PromocionDetalle::getPromoMes($articulo, $ini, $fin);
        return response()->json($promo);
    }
    
}
