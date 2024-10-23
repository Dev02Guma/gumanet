@extends('layouts.ly_reorder')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_reorder_point');
@endsection
@section('content')
<link rel="stylesheet" type="text/css" href="{{ url('css/colors-reorder-point.css') }}">

<div class="container-fluid">

  <div class="row" id="ct04">
      <div class="graf col-sm-12 ">
          <div class="container-vms" id="LoadingID" style="width: 100%; margin: 0 auto"></div>
      </div>
  </div>

  

    

    
  <strong class="text-info">
    <p class="font-italic text-muted">Actualizado hasta <span id="id_UpdateAt"> - </span></p>	
  </strong>
  <div class="table-responsive">
    <table class="table table-bordered table-sm" id="dt_articulos" width="100%" >
      <thead class="bg-blue text-light">
        <tr>
          <th class="col-yellow">ARTICULO</th>
          <th class="col-blue-ca-1" >DESCRIPCIÓN</th>
          <th class="col-blue-ca-1">FABRICANTE</th>
          <th class="col-green">
            <span 
              data-toggle="tooltip"  
              data-placement="top" 
              title="Tiempo desde que sale el pedido">LEADTIME
            </span>
          </th>
          <th class="col-green">
            <span 
              data-toggle="tooltip"  
              data-placement="top" 
              title=" - ">FACTOR STOCK SEGURIDAD
            </span>
          </th>
          <th class="col-red-strong">
            <span 
              data-toggle="tooltip"  
              data-placement="top" 
              title=" - ">ROTACION PREVISTA EXISTENCIAS POR VENCER
            </span>
          </th>
          <th class="col-red-strong">
            <span 
              data-toggle="tooltip"  
              data-placement="top" 
              title="Total de Existencia en Bodega 002">TOTAL UMK
            </span>
          </th>
          <th class="col-red-light">
            <span data-toggle="tooltip"  
              data-placement="top" 
              title="Total de Existencia en Bodega 001">TOTAL GP
            </span>
          </th>
          <th class="col-red-light"><span data-toggle="tooltip"  data-placement="top" title="Disponibilidad total de Inventario para facturar">TOTAL DISPONIBLE</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title="Existencia de Articulos menor a 7 meses">EXIST. < 7 Meses</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title="Existencia Igual o mayor a 7 meses + ON-HAND">EXIST. >= 7 Meses</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title="Fecha de Lote que esta mas proximo a vencer">LOTE MAS PROX. A VENCER</span></th>
          <th class="col-blue-light"><span data-toggle="tooltip"  data-placement="top" title="Cantidad de Lote mas proximo a vencer">EXIST. EN LOTE MAS PROX. POR VENCERSE</span></th>
          <th class="col-red-light"><span data-toggle="tooltip"  data-placement="top" title="Fecha de ult. entrada de lote">ULT. FECHA ENTRADA LOTE</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title="Cantidad de Ult. entrada de lote">ULT. CANT. INGRESADA</span></th>
          <th class="col-green-strong"><span data-toggle="tooltip"  data-placement="top" title="Promedio de unidad desplazadas en 12 meses">PROM. UND. 12m</span></th>
          <th class="col-yellow"><span data-toggle="tooltip"  data-placement="top" title="Cantidad Pedida del Articulo">PEDIDO</span></th>
          <th class="col-blue-light"><span data-toggle="tooltip"  data-placement="top" title="Cantidad en estado de Transito">TRANSITO</span></th>
          <th class="col-green-strong"><span data-toggle="tooltip"  data-placement="top" title="Ventas realizadas en el periodo de 12m">VENTAS EJEC. 12m C$.</span></th>
          <th class="col-yellow-strong"><span data-toggle="tooltip"  data-placement="top" title="Contribucion Bruta aportada en 12 meses">CONTRIBUCION BRUTA. 12m C$.</span></th>
          
          <th class="col-green"> <span data-toggle="tooltip"  data-placement="top" title=" Cantidad de Lote Mayor o igual a 7 meses + ON-HAND ">ROTACION CORTA</span> </th>
          <th class="col-green"><span data-toggle="tooltip"  data-placement="top" title="Cantidad de Lote mayor o igual a 7 meses + ONHAND + TRANSITO ">ROTACION MEDIA</span></th>
          <th class="col-red-light"><span data-toggle="tooltip"  data-placement="top" title="Cantidad de Lote mayor o igual a 7 meses + ONHAND + PEDIDO + TRANSITO">ROTACION LARGA</span></th>

          <th class="col-red-light"><span data-toggle="tooltip"  data-placement="top" title="Cantidad minima solicitada en los ultimos 2 años">MOQ</span></th>
          <th class="col-red-light"><span data-toggle="tooltip"  data-placement="top" title=" - ">REORDER</span></th>
          <th class="col-red-light"><span data-toggle="tooltip"  data-placement="top" title=" - ">CANTIDAD A ORDENAR V1</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">RAZON REORDER/MOQ</span></th>
          <th class="col-blue-ca-2"><span data-toggle="tooltip"  data-placement="top" title=" - ">COST PROM. C$</span></th>
          <th class="col-green-strong"><span data-toggle="tooltip"  data-placement="top" title=" - ">COST PROM. USD</span></th>
          <th class="col-yellow-mostasa"><span data-toggle="tooltip"  data-placement="top" title=" - ">ULT. COST. USD</span></th>
          <th class="col-green-strong"><span data-toggle="tooltip"  data-placement="top" title=" - ">DEM. ANUAL CA NETA</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">DEM. ANUAL CA AJUSTADA</span></th>
          <th class="col-green-strong"><span data-toggle="tooltip"  data-placement="top" title=" - ">FACTOR</span></th>
          <th class="col-green-strong"><span data-toggle="tooltip"  data-placement="top" title=" - ">LIMITE LOGISTICO MEDIO</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">CATEGORIA</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">VALUACION</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">REORDER1</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">ESTIMACION SOBRANTES UND</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">1.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">2.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">3.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">4.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">5.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">6.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">7.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">8.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">9.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">10.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">11.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">12.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">PROM. 3 MESES MAS ALTO</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" Nuevo Calculo de Cantidad a Reordenar "> CANTIDAD A ORDENAR V2 </span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" Categorizacion de Articulos por aporte en base al 80/20"> CAT. </span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">ALTURA.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">LARGO.</span></th>
          <th class="col-blue-ca-1"><span data-toggle="tooltip"  data-placement="top" title=" - ">ANCHO.</span></th>
        </tr>
      </thead>
    </table>
  </div>
  <div class="modal fade bd-example-modal-xl" data-backdrop="static" data-keyboard="false" id="mdDetalleArt" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-block ">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-center align-items-center pt-1 pb-0 bg-blue">
                                  <div class="row col-md-8 ">
                                    <div class="d-flex align-items-center position-relative mt-0">
                                      <div class="flex-1 ">
                                        <h6 class="mb-0 fw-semi-bold">
                                          <div class="text-light text-uppercase" id="id_descripcion"></div>
                                          <span id="id_articulo" style="display: none"></span>
                                        </h6>
                                        <p class="text-white-50 fs--2 mb-0" id="nombre_ruta_zona_modal">
                                          CLASE : <span id="id_clase"> - </span> |  LEADTIME : <span id="id_leadtime"> - </span> | LIMITE LOGICO MEDIO : <span id="id_limite_logistico_medio"> - </span>
                                        </p>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-4 border-left">
                                    <div class="row ">
                                      <div class="col-sm-4 ">
                                        <div class="form-group">                
                                          <b><label for="f2" class="text-white">PEDIDO</label></b></br>
                                          <label for="f1"> </label><span class="badge rounded-pill badge-light text-primary ml-2"  id="id_pedido"> - </span>
                                        </div>
                                      </div>	
                                      <div class="col-sm-4 border-left">
                                        <div class="form-group">                
                                          <b><label for="f2" class="text-white">TRANSITO</label></b></br>
                                          <label for="f1" > </label><span class="badge rounded-pill badge-light text-primary ml-2"  id="id_transito" > - </span>
                                        </div>
                                      </div>	
                                      <div class="col-sm-4 border-left">
                                        <div class="form-group">                
                                          <b><label for="f1" class="text-white">MOQ</label></b></br>
                                          <label for="f1" id="lbl_20"> </label><span class="badge rounded-pill badge-light text-primary ml-2"  id="id_moq"  > - </span>
                                        </div>
                                      </div>
                                      
                                                  
                                    </div>
                                  </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" >

                                    <div class="col-sm-2">
                                        <div class="card card-social" style="height: 100px">
                                            <div class="card-header text-center bg-blue">
                                                <h6 class="text-white m-0">ORDENAR 1</h6>
                                            </div>
                                            <div class="card-body ">
                                              <h6 class="text-center  font-weight-bold" style="font-size: 1.3rem!important"  id="id_reorder1"> 0.00</h6>
                                              
                                            </div>
                                            
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="card card-social" style="height: 100px">
                                            <div class="card-header text-center bg-blue" style="height: 40px;">
                                                <h6 class="text-white m-0">ORDENAR</h6>
                                            </div>
                                            <div class="card-body ">
                                              <h6 class="text-center  font-weight-bold" style="font-size: 1.3rem!important"  id="id_reordenar"> 0.00</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="card card-social" style="height: 100px">
                                            <div class="card-header text-center bg-blue" style="height: 40px;">
                                                <h6 class="text-white m-0">CANTIDAD A ORDENAR</h6>
                                            </div>
                                            <div class="card-body ">
                                              <h6 class="text-center font-weight-bold" style="font-size: 1.3rem!important"  id="id_cant_ordenar">  0.00</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">

                                      <div class="card card-social" style="height: 100px; ">
                                          <div class="card-header text-center bg-blue" style="height: 40px;">
                                              <h6 class="text-white m-0">3 MESES MAS ALTO</h6>
                                          </div>
                                        
                                          <div class="card-block">
                                              <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                                  <div class="col-3 m-0 p-0">
                                                      <p class="text-left m-0 p-0"><span class="text-muted m-r-5" id="name_mes_1" >">M1</span>
                                                      </p>
                                                  </div>
                                                  <div class="col-3 m-0 p-0">
                                                      <p class="text-center m-0 p-0"><span class="text-muted m-r-5" id="name_mes_2">M2</span>
                                                      </p>
                                                  </div>
                                                  <div class="col-3 m-0 p-0">
                                                      <p class="text-right m-0 p-0"><span class="text-muted m-r-5" id="name_mes_3">M3<span>
                                                      </p>
                                                  </div>
                                                  <div class="col-3 m-0 p-0">
                                                      <p class="text-right m-0 p-0"><span class="text-muted m-r-5" id="name_mes_3">PROM.<span>
                                                      </p>
                                                  </div>
                                              </div>
                                              <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                                  <div class="col-3 m-0 p-0">
                                                      <h6 class="font-weight-bold text-left m-0 p-0" style="font-size: 1.1rem!important"><span class="m-r-5" id="valor_mes_1"  > 0.00 </span></h6>
                                                  </div>
                                                  <div class="col-3 m-0 p-0">
                                                      <h6 class="font-weight-bold text-center m-0 p-0" style="font-size: 1.1rem!important"><span class="m-r-5" id="valor_mes_2"  > 0.00 </span></h6>
                                                  </div>

                                                  <div class="col-3 m-0 p-0">
                                                      <h6 class="font-weight-bold text-right  m-0 p-0" style="font-size: 1.1rem!important"> <span class=" m-r-5" id="valor_mes_3"> 0.00 </span> </h6>
                                                  </div>
                                                  <div class="col-3 m-0 p-0">
                                                      <h6 class="font-weight-bold text-right  m-0 p-0" style="font-size: 1.1rem!important"> <span class=" m-r-5" id="valor_mes_promedio"> 0.00 </span> </h6>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                    

                                    </div>
                                        
                                       
                                        
                                    </div>
                                    
                                </div>

                            </div>
                        </div>
                        <!-- [ Header orden produccion ] end -->
                    </div>
                    <div class="row mt-3">
                      <div class="col-4">
                            <div class="card card-social" style="height: 100px">
                                <div class="card-header text-center bg-blue" style="height: 40px;">
                                    <h6 class="text-white m-0">DEMANDA CRUZ AZUL</h6>
                                </div>
                                
                                <div class="card-block">
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-6 m-0 p-0">
                                            <p class="text-left m-0 p-0"><span class="text-muted m-r-5" >DEMANDA ANUAL NETA</span>
                                            </p>
                                        </div>
                                        <div class="col-6 m-0 p-0">
                                            <p class="text-right m-0 p-0"><span class="text-muted m-r-5">DEMANDA AJUSTADA</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-6 m-0 p-0">
                                            <h6 class="font-weight-bold" style="font-size: 1.1rem!important" id="id_demanda_neta" > - </h6>
                                            
                                        </div>

                                        <div class="col-6 m-0 p-0">
                                            <h6 class="font-weight-bold text-right  m-0 p-0" style="font-size: 1.1rem!important" id="id_demanda_ajustada"  > - </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="col-4">
                            <div class="card card-social" style="height: 100px">
                                <div class="card-header text-center bg-blue" style="height: 40px;">
                                    <h6 class="text-white m-0">COSTOS</h6>
                                </div>
                               
                                <div class="card-block">
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-4 m-0 p-0">
                                            <p class="text-left m-0 p-0"><span class="text-muted m-r-5">ULTIMO USD</span>
                                            </p>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <p class="text-center m-0 p-0"><span class="text-muted m-r-5">PROM. USD</span>
                                            </p>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <p class="text-right m-0 p-0"><span class="text-muted m-r-5">LOCAL. C$</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="font-weight-bold text-left m-0 p-0" style="font-size: 1.1rem!important"><span class="m-r-5" id="id_ultimo_costo"  > - </span></h6>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="font-weight-bold text-center m-0 p-0" style="font-size: 1.1rem!important"><span class="m-r-5" id="id_costo"  > - </span></h6>
                                        </div>

                                        <div class="col-4 m-0 p-0">
                                            <h6 class="font-weight-bold text-right  m-0 p-0" style="font-size: 1.1rem!important"> <span class=" m-r-5" id="id_ultimo_loc"> - </span> </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-4">
                            <div class="card card-social" style="height: 100px">
                                <div class="card-header text-center p-2 bg-blue">
                                    <h6 class="m-0 text-white">ROTACIONES</h6>
                                </div>
                                <div class="card-block">
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-4 m-0 p-0">
                                            <p class="text-left m-0 p-0"><span class="text-muted m-r-5">CORTA</span>
                                            </p>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <p class="text-center m-0 p-0"><span class="text-muted m-r-5">MEDIA</span>
                                            </p>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <p class="text-right m-0 p-0"><span class="text-muted m-r-5">LARGA</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="font-weight-bold text-left m-0 p-0" style="font-size: 1.1rem!important"><span class="m-r-5" id="id_R_corta"> - </span></h6>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="font-weight-bold text-center m-0 p-0" style="font-size: 1.1rem!important"><span class="m-r-5" id="id_R_media"> - </span></h6>
                                        </div>

                                        <div class="col-4 m-0 p-0">
                                            <h6 class="font-weight-bold text-right  m-0 p-0" ><span class="m-r-5" id="id_R_larga"> - </span></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class=" modal-body">
                  <div class="row">
                  <div class="col-sm-4">
                        <p for="lav-tetrapack" class="text-muted m-0">PROMEDIO MENSUAL</p>
                        <div class="input-group">
                          <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_promedio_mensual">0.00</p>  
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted m-0">VENTAS EJECUTADAS 12 MESES</p>
                        <div class="input-group">
                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_ventas">C$ 0.00</p>
                        </div>
                    </div>
                                        
                    <div class="col-sm-4">
                        <p for="lav-tetrapack" class="text-muted m-0">CONTRIBUCION EJECUTADAS 12 MESES</p>
                        <div class="input-group">
                          <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_contribucion" >C$ 0.00</p>
                        </div>
                    </div>
                  </div>
                <div class="row mt-3">  
                  
                          <div class="col-sm-12 col-md-12 col-xs-12">
                              <div class="form-group">
                                <select class="custom-select" id="selectGrafVtsDiario" name="selectGrafVtsDiario">
                                  <option value="Todos">TODOS - Menos Licitaciones</option>
                                  <option value="FARMACIAS">FARMACIAS</option>
                                  <option value="CADENAS">CADENAS</option>
                                  <option value="MAYORISTAS">MAYORISTAS</option>
                                  <option value="INSTITUCIONES_PRIVADAS">INST. PRIVADAS</option>
                                  <option value="INSTITUCIONES_PUBLICAS"> INST. PUBLICAS</option>
                                  <option value="CRUZ_AZUL">CRUZ AZUL</option>
                                  
                                </select>
                              </div>
                              
                              <div class="container-vms" id="grafVtsDiario" style="width: 100%; margin: 0 auto"></div>
                          </div>  
                      </div>  
                </div>
                <div class="modal-footer">
                  
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection