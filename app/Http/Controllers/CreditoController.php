<?php

namespace App\Http\Controllers;

use App\NotasCreditos;
use App\Vendedor;
use Illuminate\Http\Request;

class CreditoController extends Controller
{
    public function index()
    {  
        $Vendedores = Vendedor::getVendedor();
        return view('pages.notaCredito', compact('Vendedores'));
    }

    public function getFacturas(Request $request){
        $mes = $request->input('mes');
        $anno = $request->input('anno');
        $ruta = $request->input('ruta');
        
        $facturas = NotasCreditos::where('nMes', $mes)->where('nYear', $anno)->where('VENDEDOR', $ruta)->get();
        return response()->json($facturas);
    }
}
