@extends('layouts.main')
@section('metodosjs')
@include('jsViews.js_promocionesRuta')
@endsection
@section('content')
<div class="container-fluid"> 
    <div class="card border-0 shadow-sm mt-3 ">
        <div class="col-sm-auto">
            <div class="card-body">					
                <div class="row">
                    <div class="col-md-5">
                        <span id="id_form_role" style="display:none">{{ Session::get('user_role') }}</span>                        
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                            </div>								
                            <input type="text" id="id_txt_buscar" class="form-control" placeholder="Buscar...">
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                                         
                    <div class="col-md-1.5 ">
                        <div class="input-group">
                            <select class="custom-select" id="frm_lab_row" name="frm_lab_row">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="-1">*</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>	       

    <div class="card border-0 shadow-sm mt-3 ">
      <div class="card-body col-sm-12 p-0 mb-2">	
        <div class="p-0 px-car">
          <div class="table-responsive flex-between-center scrollbar border border-1 border-300 rounded-2">
            <table id="table_promociones" class="table table-striped table-bordered table-sm mt-3 fs--1" width="100%">
              <thead>
                <tr class="bg-blue text-light">
                  <th>DESCRIPCIÓN</th>
                  <th>PRECIO</th>
                  <th>NUEVA BONIF.</th>
                  <th>VIÑETA</th>
                  <th>VAL. PROM.</th>
                  <th>META VAL.</th>
                  <th>VENTA</th>
                  <th>VENTA {{ Carbon\Carbon::createFromFormat('m', @date('m'))->format('F') }}</th>
                  <th>UND PROM.</th>
                  <th>META UND.</th>
                  <th>VENTA UND.</th>
                  <th>VENTA UND. {{ Carbon\Carbon::createFromFormat('m', @date('m'))->format('F') }}</th>
                </tr>
              </thead>
                <tbody>
                    @foreach($Promociones as $p)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center position-relative mt-2">                                
                                    <div class="flex-1 ms-3">
                                    <h6 class="mb-0 fw-semi-bold"><div class="stretched-link text-900">{{ $p['Descripcion'] }}</div></h6>
                                    <p class="text-500 fs--2 mb-0">{{ $p['Articulo'] }} </p>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="pe-4 border-sm-end border-200 mt-2">
                                    <h6 class="mb-0 fw-semi-bold">C${{ @number_format($p['Precio'],2) }} </h6>                                        
                                </div> 
                            </td>

                            <td>
                                <div class="pe-4 border-sm-end border-200 text-center">
                                    <h6 class="fs--2 text-600 mb-1">{{ $p['NuevaBonificacion'] }}</h6>                                
                                </div> 
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">C${{ @number_format($p['ValorVinneta'],2) }}</h6>                    
                                </div> 
                            </td>

                            <td>
                                <div class="pe-4 border-sm-end border-200 text-center">
                                    <h6 class="fs--2 text-600 mb-1"C$>0</h6>                  
                                </div> 
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">C${{ @number_format($p['ValMeta'],2) }}</h6>
                                </div>
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">C${{ @number_format($p['Venta'],2) }} <span class="badge rounded-pill badge-primary">{{ @number_format($p['PromVenta'],2) }}%</span></h6>                    
                                </div> 
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">C${{ @number_format($p['VentaMActual'],2) }}</span></h6>                    
                                </div> 
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200 text-center">
                                    <h6 class="fs--2 text-600 mb-1">0</h6>                    
                                </div> 
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200 text-center">
                                    <h6 class="fs--2 text-600 mb-1">{{ $p['MetaUnd'] }}</h6>                    
                                </div> 
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200 text-center">
                                    <h6 class="fs--2 text-600 mb-1">{{ $p['VentaUND'] }} <span class="badge rounded-pill badge-primary">{{ @number_format($p['PromVentaUND'],2) }}%</span></h6>                    
                                </div> 
                            </td>
                            <td>
                                <div class="pe-4 border-sm-end border-200 text-center">
                                    <h6 class="fs--2 text-600 mb-1">{{ $p['VentaUNDMActual'] }}</h6>                    
                                </div> 
                            </td>
                        </tr>
                    @endforeach
               </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

</div>


<!--<div class="modal fade" id="modl_view_detalles_ruta" tabindex="-1" role="dialog" aria-labelledby="authentication-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-xl mt-6" role="document">
        <div class="modal-content border-0">
            <div class="modal-header px-5 position-relative modal-shape-header bg-primary">
                <div class="position-relative z-index-1 light">
                    <div class="flex-1">
                        <h6 class="mb-0 fw-semi-bold">
                            <div class="stretched-link text-white" id='id_lbl_nombre'>NOMBRE DE LA PROMOCION </div>
                        </h6>
                        <p class="text-white fs--2 mb-0"id='id_lbl_fechas'>00/00/0000 al 00/00/0000</p>
                        <span id="id_num_prom" style="display:none"></span>
                    </div>
                </div>
            </div>

            <div class="modal-body ">
                <div class="row col-md-12 ">
                    <div class="col-md-4 col-sm-2">
                        <div class="d-flex align-items-center position-relative mt-0">
                            <div class="avatar avatar-xl ">
                                <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}"   />
                            </div>
                            <div class="flex-1 ms-3">
                                <h6 class="mb-0 fw-semi-bold"><div class="stretched-link text-primary" id='nombre_ruta_modal'>NOMBRE DE LA PROMOCION </div></h6>
                                <p class="text-500 fs--2 mb-0"id='nombre_ruta_zona_modal'>00/00/0000 al 00/00/0000</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 col-sm-2 border-left border-primary">
                        <div class="mb-3 pe-4 border-end border-200">
                            <h6 class="fs--2 text-secondary mb-1">Meta Val</h6>
                            <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2" id="id_ttMetaValor">C$ 0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 border-left border-primary">
                        <div class="mb-3 pe-4 border-end border-200">
                            <h6 class="fs--2 text-secondary mb-1">Venta</h6>
                            <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2" id="id_ttVenta">C$ 0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 border-left border-primary">
                        <div class="mb-3 pe-4 border-end border-200">
                            <h6 class="fs--2 text-secondary mb-1">META UND</h6>
                            <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2" id="id_ttMetaUND">0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 border-left border-primary">
                        <div class="mb-3 pe-0">
                            <h6 class="fs--2 text-secondary mb-1">VENTA UND</h6>
                            <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2" id="id_ttVentaUND">0.00</h5>
                            </div>
                        </div>
                    </div>
                        
                    
                    
                </div>
                <div class="mb-3 ">
                    <div class="table-responsive scrollbar">
                        <table class="table table-hover table-striped overflow-hidden mt-4" id="tbl_excel" style="width:100%" >
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th>Index</th>
                                    <th>Articulo</th>
                                    <th>Precio C$.</th>
                                    <th>Nueva Bonif.</th>
                                    <th>Viñeta C$.</th>
                                    <th>Val. Prom C$.</th>
                                    <th>Val. Meta C$.</th>
                                    <th>Venta C$.</th>
                                    <th>Und. Prom.</th>
                                    <th>Meta UND</th>
                                    <th>Venta Und.</th>
                                </tr>
                            </thead>
                        </table> 
                    </div>
                </div>
            </div>
        
        </div>
    </div>
</div>-->
@endsection('content')