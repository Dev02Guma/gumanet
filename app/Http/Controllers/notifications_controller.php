<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\inteligenciaMercado_model;
use App\notifications_model;
use App\Company;
use Response;


//use Illuminate\Support\Facades\Http;


class notifications_controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getAllnotificaciones() {
		$obj = notifications_model::getAllnotificaciones();
		return response()->json($obj);
	}

    public function updateState(Request $request)
    {        
        $obj = notifications_model::updateState();
		return response()->json($obj);
    }

    public function exist_notify(Request $request)
    {
        $obj = notifications_model::exist_notify($request);
		return response()->json($obj);
    }

    public function exist_registry(Request $request)
    {
        $obj = notifications_model::exist_registry($request);
		return response()->json($obj);
    }

   
}