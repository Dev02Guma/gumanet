<?php

namespace App\Http\Controllers;

use App\Promocion;
use App\PromocionDetalle;
use App\Vendedor;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    public function getPromocion()
    {  
        $Mes   = date('n');
        $Anno   = date('Y');

        $Promociones   = PromocionDetalle::getDetalles();
        return view('pages.promocionesRuta',compact('Promociones'));
    }
    
}
