@extends('layouts.main')
@section('metodosjs')
@include('jsViews.js_notasCreditos')
@endsection
@section('content')

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <div class="container-fluid" data-layout="container">        
        <div class="content">            

           
            

            <div class="col-12">
              
              <div class="card h-100">
                
                <div class="card-header ">
                  <div class="row flex-between-center">
                    <div class="col-4 col-sm-auto d-flex align-items-center">
                      <form class="row align-items-center g-3">
                        <div class="col-auto"><h6 class="text-700 mb-0"> </h6></div>
                        <div class="col-md-auto position-relative" style="display:none">
                          <span class="fas fa-calendar-alt text-primary position-absolute translate-middle-y ms-2 mt-3"> </span>
                          <input id="id_range_select" class="form-control form-control-sm datetimepicker ps-4" type="text" data-options='{"mode":"range","dateFormat":"Y-m-d","disableMobile":true}'/>
                        </div>
                        <div class="col-md-12">
                          
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                            </div>								
                            <input type="text" id="id_credito_buscar" class="form-control" placeholder="Buscar...">
                          </div>
                        </div>
                      </form>                            
                    </div>
                    <div class="col-8 col-sm-auto text-end ">
                      <div class="row g-3 needs-validation" >
                      
                        <div class="col-md-6 border-left">
                          <select class="custom-select"  id="id_credito_vendedor">
                            
                          @foreach ($Vendedores as $vendedor)
                                <option value="{{$vendedor['VENDEDOR']}}">{{$vendedor['VENDEDOR']}} | {{strtoupper($vendedor['NOMBRE'])}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-3 border-left">
                          <select class="custom-select"  id="id_credito_mes">
                            
                          @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>{{ Carbon\Carbon::createFromFormat('m', $i)->format('F') }}</option>
                          @endfor
                          </select>
                        </div>
                        <div class="col-md-3 border-left">
                          <div class="input-group" >
                            <select class="custom-select"  id="id_credito_year">
                                @foreach (range(date('Y'),date('Y')-1) as $year)
                                  <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach  
                            </select>
                            <div class="btn input-group-text bg-transparent" onClick="getFacturasRuta()" id="id_btn_new">
                                  <span class="fas fa-history fs--1 text-600"></span>
                              </div>
                          </div>
                        </div> 
                       
 
                         
                      </div>
                    </div>
                  </div>
                </div> 
               
                
              
                
              </div>
            </div>

            <div class="row mt-3">
                <div class="col-xxl-6 col-lg-6">
                    <div class="card overflow-hidden">
                        <div class="card-header d-flex flex-between-center bg-blue text-light py-2">
                            <h6 class="mb-0">Facturas</h6>
                            
                        </div>
                        <div class="card-body py-0">
                            <div class="table-responsive scrollbar">
                                <table class="table table-borderedmb-0 fs--1" id="tbl_facturas">
                                  <tbody>
                                  <td><b>REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS</b></td>
                                  </tbody>
                                
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2">
                            <div class="row flex-between-center">
                                <div class="col-auto">
                                
                                </div>
                                <div class="col-auto"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-xxl-6 col-lg-6">
                    <div class="card overflow-hidden">
                        <div class="card-header ">
                            <div class="row flex-between-center g-0">
                                <div class="col-auto ">
                                <h6 class="mb-0">Notas de Credito</h6>
                                </div>
                                <div class="col-auto d-flex">
                                <div class="form-check mb-0 d-flex">
                                    <input class="form-check-input form-check-input-primary" id="ecommerceLastMonth" type="checkbox" checked="checked" />
                                    <label class="form-check-label ps-2 fs--2 text-600 mb-0" for="ecommerceLastMonth">80 %<span class="text-dark d-none d-md-inline">: C$ 32,502.00</span></label>
                                </div>
                                <div class="form-check mb-0 d-flex ps-0 ps-md-3">
                                    <input class="form-check-input ms-2 form-check-input-warning opacity-75" id="ecommercePrevYear" type="checkbox" checked="checked" />
                                    <label class="form-check-label ps-2 fs--2 text-600 mb-0" for="ecommercePrevYear">20 % <span class="text-dark d-none d-md-inline">: C$ 46,018.00</span></label>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-0">
                            <div class="table-responsive scrollbar">
                                <table class="table table-dashboard mb-0 fs--1" id="tbl_creditos">
                                  <tbody>
                                    <td><b>REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS</b></td>
                                  </tbody>
                                <!--<php for ($i=0; $i < 10; $i++) { ?>
                                <tr>
                                    <td class="align-middle ps-0 text-nowrap">
                                    <div class="d-flex position-relative align-items-center"><img class="d-flex align-self-center me-2" src="../assets/img/logos/atlassian.png" alt="" width="30" />
                                        <div class="flex-1"><a class="stretched-link" href="#!">
                                            <h6 class="mb-0">FCT. 00000 </h6>
                                        </a>
                                        <p class="mb-0">0000 - NOMBRE DEL CLIENTE</p>
                                        </div>
                                    </div>
                                    </td>
                                    <td class="align-middle px-4" style="width:1%;"><span class="badge fs--1 w-100 badge-soft-success">N/C: 0000 </span></td>
                                    <td class="align-middle px-4 text-end text-nowrap" style="width:1%;">
                                    <h6 class="mb-0">C$ 290.00 NIO </h6>
                                    <p class="fs--2 mb-0">15 May, 2020</p>
                                    </td>
                                    <td class="align-middle ps-4 pe-1" style="width: 130px; min-width: 130px;">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-window-close text-primary"></span></div>
                                    </td>
                                </tr>
                                <php } ?>-->
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2">
                            <div class="row flex-between-center">
                                <div class="col-auto">
                               
                                </div>
                                <div class="col-auto"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

           

            
          </div>

          <div class="modal fade" id="modl_view_detalles_ruta" tabindex="-1" role="dialog" aria-labelledby="authentication-modal-label" aria-hidden="true">
          <div class="modal-dialog modal-xl mt-6" role="document">
            <div class="modal-content border-0">
              <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                <div class="position-relative z-index-1 light">
                  <p class="fs--1 mb-0 text-white">Listado de Articulos que Confirman el 80 / 20 de la ruta</p>
                </div>
                <button class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body py-4 px-5 ">
                <div class="row">

                  <div class="row flex-between-center">
                    <div class="col-auto">
                      <h6 class="mb-2">Resumen</h6>
                    </div>
                    <div class="col-auto mt-2">
                      <div class="row g-sm-4">
                      <div class="col-12 col-sm-auto">
                          <div class="mb-3 pe-4 border-sm-end border-200">
                            <h6 class="fs--2 text-600 mb-1">SKUS</h6>
                            <div class="d-flex align-items-center">
                              <h5 class="fs-0 text-900 mb-0 me-2" id="id_list_80">0</h5><span class="badge rounded-pill badge-soft-primary"><span class="fas fa-caret-up"></span> 80 %</span>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-auto">
                          <div class="mb-3 pe-4 border-sm-end border-200">
                            <h6 class="fs--2 text-600 mb-1">SKUS</h6>
                            <div class="d-flex align-items-center">
                              <h5 class="fs-0 text-900 mb-0 me-2" id="id_list_20">0</h5><span class="badge rounded-pill badge-soft-primary"><span class="fas fa-caret-up"></span> 20 %</span>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-auto">
                          <div class="mb-3 pe-4 border-sm-end border-200">
                            <h6 class="fs--2 text-600 mb-1">META UND</h6>
                            <div class="d-flex align-items-center">
                              <h5 class="fs-0 text-900 mb-0 me-2" id="id_Meta_UND">0</h5>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-auto">
                          <div class="mb-3 pe-4 border-sm-end border-200">
                            <h6 class="fs--2 text-600 mb-1">VENTA UND</h6>
                            <div class="d-flex align-items-center">
                              <h5 class="fs-0 text-900 mb-0 me-2" id="id_Venta_UND">0</h5>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-sm-auto">
                          <div class="mb-3 pe-0">
                            <h6 class="fs--2 text-600 mb-1">VENTA VALOR</h6>
                            <div class="d-flex align-items-center">
                              <h5 class="fs-0 text-900 mb-0 me-2" id="ttVenta_Val">C$256,489</h5>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-12 ">
                      <div class="input-group">
                        <input class="form-control  shadow-none search" type="search"  id="id_searh_table_Excel" placeholder="Ingrese informacion a buscar." aria-label="search" />
                        <div class="input-group-text bg-transparent">
                            <span class="fa fa-search fs--1 text-600"></span>
                        </div>                          
                      </div>
                    </div>
                  </div>

                  <div class="mb-3 mt-3">
                    <table class="table table-hover table-striped overflow-hidden" id="tbl_excel" style="width:100%" ></table> 
                  </div>
                  
              </div>
            </div>
          </div>
        </div>
      
    </div>
</main>


@endsection('content')