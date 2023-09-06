<?php

namespace App\Http\Controllers;

use App\dashboard_model;
use App\Models;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;


class dashboard_controller extends Controller {
  
  public function __construct() {
    $this->middleware('auth');
  }
  public function index() {

    $this->agregarDatosASession();
      $data = [
          'name' =>  'GUMA@NET'
      ];      
      return view('pages.dashboard',$data);
  }

    public function agregarDatosASession(){
      $request = Request();
      $ApplicationVersion = new \git_version();
      $company = Company::where('id',$request->session()->get('company_id'))->first();// obtener nombre de empresa mediante el id de empresa
      $request->session()->put('ApplicationVersion', $ApplicationVersion::get());
      $request->session()->put('companyName', $company->nombre);// agregar nombre de compañia a session[], para obtenert el nombre al cargar otras pagina 
    }

    public function getTotalRutaXVentas($mes, $anio){
    $obj = dashboard_model::getTotalRutaXVentas($mes, $anio);
    return (response()->json($obj));
  }

  public function getTotalUnidadesXRutaXVentas($mes, $anio){
    $obj = dashboard_model::getTotalUnidadesXRutaXVentas($mes, $anio);
    return response()->json($obj);
  }

  public function ClientesNoFacturados($mes, $anio){
    $obj = dashboard_model::ClientesNoFacturados($mes, $anio);
    return response()->json($obj);
  }

  public function ArticuloNoFacturado($mes, $anio){
    $obj = dashboard_model::ArticuloNoFacturado($mes, $anio);
    return response()->json($obj);
  }

  public function getClientesSinComprar($mes, $anio){
    $obj = dashboard_model::Cliente_sin_comprar($mes, $anio);
    return response()->json($obj);
  }

	public function getDetalleVentas($tipo, $mes, $anio, $cliente, $articulo, $ruta) {
		$obj = dashboard_model::getDetalleVentas($tipo, $mes, $anio, $cliente, $articulo, $ruta);
		return response()->json($obj);
	}
  public function getDetalleVentasDia($dia, $mes, $anio,$segmento) 
  {
		$obj = dashboard_model::getDetalleVentasDia($dia, $mes, $anio,$segmento);
		return response()->json($obj);
	}
  public function getDetalleVentasXRuta($mes,$anio,$ruta){
    $obj = dashboard_model::getDetalleVentasXRuta($mes, $anio, $ruta);
    return response()->json($obj);
  }
  public function get_Vta_Ruta_dia($Dia,$mes,$anio,$ruta){
    $obj = dashboard_model::get_Vta_Ruta_dia($Dia,$mes, $anio, $ruta);
    return response()->json($obj);
  }
  public function get_Vta_all_items($dia,$mes,$anio,$segmento){
    $obj = dashboard_model::get_Vta_all_items($dia,$mes, $anio,$segmento);
    return response()->json($obj);
  }

  public function get_all_top($dia,$mes,$anio,$segmento){
    $obj = dashboard_model::get_all_top($dia,$mes, $anio,$segmento);
    return response()->json($obj);
  }


  public function getValBodegas() {
    $obj = dashboard_model::getValBodegas();
    return response()->json($obj);
  }

  public function getSaleCadena(Request $request) {
    if($request->isMethod('post')) {
      $obj = dashboard_model::getSaleCadena($request);
      return response()->json($obj);
    }
  }

  public function getSaleCadenaDetalle(Request $request) {
    if($request->isMethod('post')) {
      $obj = dashboard_model::getSaleCadenaDetalle($request);
      return response()->json($obj);
    }
  }

  public function ventaXCategorias(Request $request) {
    if($request->isMethod('post')) {
      $obj = dashboard_model::ventaXCategorias($request->input('mes'),$request->input('anio'),$request->input('cate'));
      return response()->json($obj);
    }
  }

  public function getDataGraficas($mes, $anio, $xbolsones) {
    $obj = dashboard_model::getDataGraficas($mes, $anio, $xbolsones);
    return response()->json($obj);
  }
  public function getDataGrafSelect($mes, $anio, $xbolsones,$Segmentos) {
    $obj = dashboard_model::get_Ventas_diarias($mes, $anio, 1 ,$xbolsones,$Segmentos);
    return response()->json($obj);
  }

  public function GetTop10Productos($mes, $anio, $xbolsones,$segmento) {
    $obj = dashboard_model::getTop10Productos($mes, $anio,1, $xbolsones,$segmento);
    return response()->json($obj);
  }

  public function GetTop10CLientes($mes, $anio, $xbolsones,$segmento) {
    $obj = dashboard_model::getTop10Clientes($mes, $anio,1, $xbolsones,$segmento);
    return response()->json($obj);
  }



  public function getComportamiento($elemento) {
    $obj = dashboard_model::getComportamiento($elemento);
    return response()->json($obj);
  }


  public function getVentasMensuales($xbolsones) {
    $obj = dashboard_model::getVentasMensuales($xbolsones);
    return response()->json($obj);
  }


  public function getRealVentasMensuales($xbolsones,$segmentos) {
    $obj = dashboard_model::getRealVentasMensuales($xbolsones,$segmentos);
    return response()->json($obj);
  }

  public function getVentasExportacion($xbolsones,$segmentos) {
    $obj = dashboard_model::getVentasExportacion($xbolsones,$segmentos);
    return response()->json($obj);
  }

  public function getRecuRowsByRoutes($mes, $anio, $pageName){

    $obj = dashboard_model::getRecuRowsByRoutes($mes, $anio, $pageName);
    return response()->json($obj);
      
  }

  /************ Add by Rodolfo **********/
  public function getAllClientsByCategory($mes, $anio, $categoria,$Bolson)
  {
      $obj = dashboard_model::getAllClientsByCategory($mes, $anio, $categoria,$Bolson);
      return response()->json($obj);
  }

}



