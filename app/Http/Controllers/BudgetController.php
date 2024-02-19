<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Budget;


class BudgetController extends Controller {
    public function __construct() {
		$this->middleware(['auth','roles']);
    }
    public function ViewBudget(){
      
      $data = array(
        'page'              => 'Inventario',
        'name'              => 'GUMA@NET',
        'hideTransaccion'   => ''
      );
      return view('pages.Budget.Home',$data);
        
    }
    public function dtProyect(Request $request) {
      $obj = Budget::dtProyect($request);
      return response()->json($obj);
    }
    public function dtArticulo(Request $request) {
      $obj = Budget::dtArticulo($request);
      return response()->json($obj);
    }
}