@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_reorder_point');
@endsection
@section('content')
<div class="container-fluid">
  <div class="row mb-5">
    <div class="col-md-10">
      <h4 class="h4">Reorder Point</h4>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-sm-11">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
        </div>
        <input type="text" id="InputDtShowSearchFilterArt" class="form-control" aria-describedby="basic-addon1">
      </div>
    </div>
    <div class="col-sm-1">
      <div class="input-group mb-3">
        <select class="custom-select" id="InputDtShowColumnsArtic" name="InputDtShowColumnsArtic">
          <option value="5" selected>5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="100">100</option>
          <option value="-1">Todo</option>
        </select>
      </div>
    </div>
    <div class="col-sm-2 p-0 m-0" style="display:none">
      <a id="exp-to-excel" href="#!" onclick="descargarArchivo('inventario')" class="btn btn-light btn-block text-success float-right"><i class="fas fa-file-excel"></i> Exportar</a>
    </div>      
  </div>
  <div class="row">
      <div class="col-12">
        <div class="table-responsive mt-3 mb-2">
              <table class="table table-bordered" width="100%" id="dtInvCompleto"></table>
          </div>
      </div>
  </div>
  <!--MODAL: DETALLE DE ARTICULO-->
  <div class="modal fade " data-backdrop="static" data-keyboard="false" id="mdDetalleArt" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        
        
        <div class="card">  
          <div class="card-header bg-primary text-white">  
              <h4 class="card-title text-uppercase" id="id_titulo_modal_all_items" >Employee Form</h4>  
          </div>
          <div class="card-body">  
          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <a class="nav-item nav-link active" id="navBodega" data-toggle="tab" href="#nav-bod" role="tab" aria-controls="nav-bod" aria-selected="true">TAB 01</a>
              <a class="nav-item nav-link" id="navPrecios" data-toggle="tab" href="#nav-prec" role="tab" aria-controls="nav-prec" aria-selected="false">TAB02</a>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-bod" role="tabpanel" aria-labelledby="navBodega">
              <div class="row">
                  <div class="col-sm-12 mt-3">
                    
                    <div class="col-12">
                      <div class="row align-items-center g-4">
                        <div class="col-12 col-md-auto">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">100,000</h4>
                              <p class="text-800 fs--1 mb-0">REORDER</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-auto">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">100,000</h4>
                              <p class="text-800 fs--1 mb-0">CANTIDAD A ORDENAR</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-auto">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">$ 0.00</h4>
                              <p class="text-800 fs--1 mb-0">COSTO PROMEDIO USD</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-auto">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">$ 0.00 </h4>
                              <p class="text-800 fs--1 mb-0">ULTIMO COSTO USD</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-auto">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">$ 0.00</h4>
                              <p class="text-800 fs--1 mb-0">LEADTIME</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-auto">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">$ 0.00 </h4>
                              <p class="text-800 fs--1 mb-0">FACTOR STOCK SEGURIDAD</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr class="bg-200 mb-6 mt-4">
                    </div>

                    <form id="needs-validation" novalidate>  
                      
                      <div class="row">  
                          <div class="col-sm-2 col-md-2 col-xs-12">  
                              <label for="firstName">Pedido</label>  
                              <input type="text" class="form-control" id="XXXX" placeholder="0.00" required>  
                          </div>  
                          <div class="col-sm-2 col-md-2 col-xs-12">  
                              <label for="lastName">Fecha Pedido</label>  
                              <input type="text" class="form-control" id="XXXX" placeholder="0.00" required>  
                              
                          </div>
                          <div class="col-sm-2 col-md-2 col-xs-12">  
                              <label for="firstName">Meses Transcurridos</label>  
                              <input type="text" class="form-control" id="XXXX" placeholder="0.00" required>  
                          </div>  
                          <div class="col-sm-2 col-md-2 col-xs-12">  
                              <label for="lastName">Procedido</label>  
                              <input type="text" class="form-control" id="XXXX" placeholder="0.00" required>  
                          </div>  
                          <div class="col-sm-2 col-md-2 col-xs-12">  
                              <label for="lastName">Transito</label>  
                              <input type="text" class="form-control" id="XXXX" placeholder="0.00" required>  
                          </div>  
                          <div class="col-sm-2 col-md-2 col-xs-12">  
                              <label for="lastName">MOQ</label>  
                              <input type="text" class="form-control" id="XXXX" placeholder="0.00" required>  
                          </div>                                
                      </div>
                      <div class="row mt-3">  
                          <div class="col-sm-12 col-md-12 col-xs-12">
                              <div class="container-vms" id="grafVtsDiario" style="width: 100%; margin: 0 auto"></div>
                          </div>  
                      </div>  
                      
                    </form> 
                    <hr class="bg-200 mb-6 mt-4">

                    <p class="text-800 fs--1 mb-0">Rotacion</p>
                    <div class="col-12">
                      <div class="row align-items-center g-4">
                        <div class="col-sm-4 col-md-4 col-xs-12">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">1,000,000</h4>
                              <p class="text-800 fs--1 mb-0">CORTA (MESES ON HAND) Límite 6.5</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-xs-12">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">1,000,000</h4>
                              <p class="text-800 fs--1 mb-0">MEDIA (ON HAND+TRANSITO) Límite 10.5</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-xs-12">
                          <div class="d-flex align-items-center">
                            <div class="ms-3">
                              <h4 class="mb-0">1,000,000</h4>
                              <p class="text-800 fs--1 mb-0">LARGA (ON HAND+TRANSITO+PEDIDOS) Límite 12</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                  </div>
              </div>
            </div>
            <div class="tab-pane fade" id="nav-prec" role="tabpanel" aria-labelledby="navPrecios">
              <div class="row">
                <div class="col-sm-12">
                  <div class="col-12">
                    <div class="row align-items-center g-4 mt-3">
                      <div class="col-sm-6 col-md-6 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">100,000</h4>
                            <p class="text-800 fs--1 mb-0">PRESUPUESTOUNIDADES 2022 - MENSUAL</p>
                          </div>
                        </div>
                      </div>
                      <div class="ccol-sm-6 col-md-6 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">100,000</h4>
                            <p class="text-800 fs--1 mb-0">EJECUTADOUNIDADES YTD (Promedio mensual a la fecha)</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <hr class="bg-200 mb-6 mt-4">
                  </div>
                  <form id="needs-validation" novalidate>  
                      <div class="row mt-3">  
                          <div class="col-sm-6 col-md-6 col-xs-12">  
                              <div class="form-group">  
                                  <label for="firstname">DemandaAnual CRUZ AZUL (NETA)</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                              </div>  
                          </div>  
                          <div class="col-sm-6 col-md-6 col-xs-12">  
                              <div class="form-group">  
                                  <label for="lastname">DemandaAnual CRUZ AZUL (AJUSTADA)</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                                  
                              </div>  
                          </div>  
                      </div>  
                      <div class="row mt-3">  
                          <div class="col-sm-4 col-md-4 col-xs-12">  
                              <div class="form-group">  
                                  <label for="firstname">PORCENTAJE FARMACIAS % (Volumen)</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                              </div>  
                          </div>  
                          <div class="col-sm-4 col-md-4 col-xs-12">  
                              <div class="form-group">  
                                  <label for="lastname">PORCENTAJE MAYORISTAS % (Volumen)</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                                  
                              </div>  
                          </div>
                          <div class="col-sm-4 col-md-4 col-xs-12">  
                              <div class="form-group">  
                                  <label for="lastname">PORCENTAJE INSTITUCIONES PRIVADAS  % (Volumen)</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                                  
                              </div>  
                          </div>  
                        </div>  
                        <div class="row mt-3">  
                          <div class="col-sm-4 col-md-4 col-xs-12">  
                              <div class="form-group">  
                                  <label for="firstname">PRECIO FARMACIAS C$</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                              </div>  
                          </div>  
                          <div class="col-sm-4 col-md-4 col-xs-12">  
                              <div class="form-group">  
                                  <label for="lastname">PRECIO MAYORISTAS C$</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                                  
                              </div>  
                          </div>
                          <div class="col-sm-4 col-md-4 col-xs-12">  
                              <div class="form-group">  
                                  <label for="lastname">PRECIO INSTITUCIONES PRIVADAS C$</label>  
                                  <input type="text" id="XXXXXX" placeholder="0.00" class="form-control" aria-describedby="inputGroupPrepend" required />  
                                  
                              </div>  
                          </div>  
                        </div>
                      
                  </form>  
                  
                  <div class="col-12">
                    <div class="row align-items-center g-4 mt-3">
                      <div class="col-sm-4 col-md-4 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">0.00 % </h4>
                            <p class="text-800 fs--1 mb-0">TENDENCIA CANTIDAD VENDIDA</p>
                          </div>
                        </div>
                      </div>
                      <div class="ccol-sm-4 col-md-4 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">0.00 %</h4>
                            <p class="text-800 fs--1 mb-0">TENDENCIA PRECIO DE VENTA C$</p>
                          </div>
                        </div>
                      </div>
                      <div class="ccol-sm-4 col-md-4 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">0.00 %</h4>
                            <p class="text-800 fs--1 mb-0">TENDENCIA COSTO DE VENTA C$</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <hr class="bg-200 mb-6 mt-4">
                  </div>
                  <div class="col-sm-12 table-responsive" >                    
                    <table id="dtInfo" class="display nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>VALUACION</th>
                                <th>MARGEN EJECUTADO</th>
                                <th>RATING EN EJECUCION</th>
                                <th>% ACUMULADOCONTRIBUCION YTD</th>
                                <th>CONTRIBUCION INDIVIDUAL % YTD</th>
                                <th>CONTRIBUCION EJECUTADA YTD C$</th>
                                <th>LIMITE LOGISTICO MEDIO</th>
                                
                              
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                              <td>VALUACION</td>
                              <td>MARGEN EJECUTADO</td>
                              <td>RATING EN EJECUCION</td>
                              <td>% ACUMULADOCONTRIBUCION YTD</td>
                              <td>CONTRIBUCION INDIVIDUAL % YTD</td>
                              <td>CONTRIBUCION EJECUTADA YTD C$</td>
                              <td>LIMITE LOGISTICO MEDIO</td>
                              
                              
                            </tr>
                            
                        </tbody>
                    </table>
                                      
                    <table id="dtEstimacion" class="display nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>ESTIMACION</th>
                                <th>ESTIMACION SOBRANTES (Unidades)</th>
                                <th>ESTIMACION FALTANTES (Unidades)</th>
                                <th>VALUACION SOBRANTES (USD)</th>
                                <th>VALUACION FALTANTES (USD)</th>
                                <th>RAZON REORDER/MOQ</th>
                              
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                              <td>ESTIMACION</td>
                              <td>ESTIMACION SOBRANTES (Unidades)</td>
                              <td>ESTIMACION FALTANTES (Unidades)</td>
                              <td>VALUACION SOBRANTES (USD)</td>
                              <td>VALUACION FALTANTES (USD)</td>
                              <td>RAZON REORDER/MOQ</td>
                              
                            </tr>
                            
                        </tbody>
                    </table>
                  </div>
                  <div class="col-12">
                    <hr class="bg-200 mb-6 mt-4">

                    <div class="row align-items-center g-4">
                      <div class="col-sm-2 col-md-2 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">A</h4>
                            <p class="text-800 fs--1 mb-0">CLASE ORIGINAL</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2 col-md-2 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">12</h4>
                            <p class="text-800 fs--1 mb-0">NUEVACLASE</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2 col-md-2 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">NO / SI</h4>
                            <p class="text-800 fs--1 mb-0">CAMBIO</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2 col-md-2 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">A</h4>
                            <p class="text-800 fs--1 mb-0">CLASE</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2 col-md-2 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">0.00</h4>
                            <p class="text-800 fs--1 mb-0">FACTOR</p>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2 col-md-2 col-xs-12">
                        <div class="d-flex align-items-center">
                          <div class="ms-3">
                            <h4 class="mb-0">0.00 </h4>
                            <p class="text-800 fs--1 mb-0">REORDER1</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
            


          </div> 
          <div class="modal-footer">		
            <button class="btn btn-danger rounded-0" data-dismiss="modal" type="submit">Cerrar</button>  
            <button class="btn btn-primary rounded-0" type="submit">Guardar</button>  	
		      </div> 
        </div>  
      </div>
    </div>
  </div>
</div>
@endsection