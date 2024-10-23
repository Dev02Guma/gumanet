@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_inventario');
@endsection
@section('content')
<div class="container-fluid">
  <div class="row mb-5">
    <div class="col-md-6">
      <h4 class="h4">Inventario</h4>
    </div>
    @if( Auth::User()->email=='asaenz@unimarksa.com' || Auth::User()->email=='admin@gmail.com' || Auth::User()->email=='asisp@unimarksa.com' )    
      <div class="col-md-2">
        <a id="" href="{{url('/invCompleto')}}" class="btn btn-primary btn-block">Inventario Completo</a>
      </div>
      
      <div class="col-md-2">
        <a id="" href="{{url('/Inventario/Transito/1')}}" class="btn btn-primary btn-block">Transito Con Codigo</a>
      </div>
      <div class="col-md-2">
        <a id="" href="{{url('/Inventario/Transito/0')}}" class="btn btn-primary btn-block">Transito Sin Codigo</a>
      </div>
    @endif
  </div>
  <div class="row mt-3">
    <div class="col-sm-9">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
        </div>
        <input type="text" id="InputDtShowSearchFilterArt" class="form-control" placeholder="Buscar en Inventario" aria-label="Username" aria-describedby="basic-addon1">
      </div>
    </div>
    <div class="col-sm-1">
      <div class="input-group mb-3">
        <select class="custom-select" id="InputDtShowColumnsArtic" name="InputDtShowColumnsArtic">
          <option value="10" selected>10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="-1">Todo</option>
        </select>
      </div>
    </div>
    <div class="col-sm-2 p-0 m-0">
      <a id="exp-to-excel" href="#!" onclick="descargarArchivo('inventario')" class="btn btn-light btn-block text-success float-right"><i class="fas fa-file-excel"></i> Exportar</a>
    </div>      
  </div>
  <div class="row">
      <div class="col-12">
          <div class="table-responsive mt-3 mb-2">
              <table class="table table-bordered table-sm" width="100%" id="dtInventarioArticulos"></table>
          </div>
      </div>
  </div><hr>
  <div class="row mt-4" id="modulo-inventario">
    <div class="col-sm-12">
      <h1 class="h4 text-info mb-4">Articulos próximos a vencer</h1>
      <div class="row">
        <div class="col-md-7">
          <div class="form-group">
            <label for="InputDtShowSearchFilterArtVenc" class="text-muted">Realizar busqueda por Articulo</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
              </div>
              <input type="text" id="InputDtShowSearchFilterArtVenc" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="orderByDate" class="text-muted">Filtrar por</label>
            <select class="form-control" id="orderByDate">
              <option value="6">Vencimiento a 6 meses</option>
              <option value="12">Vencimiento a 12 meses</option>
            </select>
          </div>
        </div>
        <div class="col-sm-1">
          <div class="form-group">
            <label for="InputDtShowColumnsArtic2" class="text-muted">Ver por</label>
            <select class="custom-select" id="InputDtShowColumnsArtic2" name="InputDtShowColumnsArtic2">
              <option value="10" selected>10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Todo</option>
            </select>
          </div>
        </div>
        <div class="col-sm-1">
          <div class="form-group">
            <label for="exp-to-excel" class="text-muted">Exportar a</label>
            <a id="exp-to-excel" href="#!" onclick="descargarArchivo('vencimiento')" class="btn btn-light btn-block text-success float-right"><i class="fas fa-file-excel"></i></a>
          </div>
        </div>
      </div>
      <div class="table-responsive mb-5">
        <table class="table table-bordered table-sm" width="100%" id="tblArticulosVencimiento"></table>
      </div>
    </div>
  </div>




  <div class="row mt-4" id="modulo-inventario-vencido">
    <div class="col-sm-12">
      <h1 class="h4 text-info mb-4">Articulos de Bodega 004 - Dañados y Vencidos</h1>
      <div class="row">
        <div class="col-md-11">
          <div class="form-group">
            <label for="InputDtShowSearchFilterArtVenc" class="text-muted">Realizar busqueda por Articulo</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" id=""><i data-feather="search"></i></span>
              </div>
              <input type="text" id="id_search_tble_inventario_vencido" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
            </div>
          </div>
        </div>        
        <div class="col-sm-1">
          <div class="form-group">
            <label for="InputDtShowColumnsArtic2" class="text-muted">Ver por</label>
            <select class="custom-select" id="id_select_inventario_vencido" name="InputDtShowColumnsArtic2">
              <option value="10" selected>10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Todo</option>
            </select>
          </div>
        </div>
      </div>
      <div class="table-responsive mb-5">
        <table class="table table-bordered table-sm" width="100%" id="id_tbl_inventario_b004"></table>
      </div>
    </div>
  </div>


</div>

<!--MODAL: DETALLE DE ARTICULO-->
<div class="modal fade bd-example-modal-xl" data-backdrop="static" data-keyboard="false" id="mdDetalleArt" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header d-block">
        <h5 class="modal-title text-center" id="tArticulo"></h5>
      </div>
      <div class="modal-body">
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="navBodega" data-toggle="tab" href="#nav-bod" role="tab" aria-controls="nav-bod" aria-selected="true">Bodega</a>
            <a class="nav-item nav-link" id="navPrecios" data-toggle="tab" href="#nav-prec" role="tab" aria-controls="nav-prec" aria-selected="false">Precios</a>
            <a class="nav-item nav-link" id="navBonificados" data-toggle="tab" href="#nav-boni" role="tab" aria-controls="nav-boni" aria-selected="false">Bonificados</a>
            @if( Auth::User()->role == 1  || Auth::User()->role== 2 || Auth::User()->role== 6 || Auth::User()->role== 7 )
              <a class="nav-item nav-link" id="navCostos" data-toggle="tab" href="#nav-costos" role="tab" aria-controls="nav-trans" aria-selected="false">Costos</a>
              <a class="nav-item nav-link" id="navMargen" data-toggle="tab" href="#nav-margen" role="tab" aria-controls="nav-margen" aria-selected="false">Margen</a>
            @endif
            <a class="nav-item nav-link" id="navTransaccion" data-toggle="tab" href="#nav-trans" role="tab" aria-controls="nav-trans" aria-selected="false">Transacciones</a>
            <a class="nav-item nav-link" id="navOtros" data-toggle="tab" href="#nav-otros" role="tab" aria-controls="nav-otros" aria-selected="false">Otros</a>
            <a class="nav-item nav-link" id="navIndicadores" data-toggle="tab" href="#nav-Indicadores" role="tab" aria-controls="nav-Indicadores" aria-selected="false">Indicadores</a>
            <a class="nav-item nav-link" id="navVinneta" data-toggle="tab" href="#nav-Vinneta" role="tab" aria-controls="nav-Vineta" aria-selected="false">Viñeta</a>
            <a class="nav-item nav-link" id="navComportamiento" data-toggle="tab" href="#nav-Comportamiento" role="tab" aria-controls="nav-Comportamiento" aria-selected="false">Comportamiento</a>
            <a class="nav-item nav-link" id="navTransito" data-toggle="tab" href="#nav-Transito" role="tab" aria-controls="nav-Transito" aria-selected="false">Transito</a>
            <a class="nav-item nav-link" id="navMific" data-toggle="tab" href="#nav-Mific" role="tab" aria-controls="nav-Mific" aria-selected="false">Mific</a>
            
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

          <div class="tab-pane fade show active" id="nav-bod" role="tabpanel" aria-labelledby="navBodega">
            <div class="row">
                <div class="col-sm-12">
                    <table id="tblBodega" class="table table-bordered mt-3">
                        <thead class="bg-blue text-light">
                        <tr>
                            <th></th>
                            <th>Bodega</th>
                            <th>Unidad</th>
                            <th>Nombre</th>
                            <th>Cant. Disponible</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-prec" role="tabpanel" aria-labelledby="navPrecios">
            <div class="row">
              <div class="col-sm-12">
                <table id="tblPrecios" class="table table-bordered mt-3">
                  <thead class="bg-blue text-light">
                  <tr>
                      <th>Nivel de Precio</th>
                      <th>Precio</th>
                  </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-boni" role="tabpanel" aria-labelledby="navBonificados">
            <table id="tblBonificados" class="table table-bordered mt-3">
              <thead class="bg-blue text-light">
              <tr>
                  <th>Reglas</th>
              </tr>
              </thead>
            </table>
          </div>

          <div class="tab-pane fade" id="nav-trans" role="tabpanel" aria-labelledby="navTransaccion">
            <div class="row">
              <div class="col-sm-12">
             
                <div class="card" style="border-top: none">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="f1">Desde</label>
                          <input type="text" class="input-fecha" id="f1">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="f2">Hasta</label>
                          <input type="text" class="input-fecha" id="f2">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="catArt">Tipo</label>
                          <select class="custom-select custom-select-sm" id="catArt">
                            <option selected value="Físico">Físico</option>
                            <option value="Costo">Costo</option>
                            <option value="Compra">Compra</option>
                            <option value="Aprobación">Aprobación</option>
                            <option value="Traspaso">Traspaso</option>
                            <option value="Venta">Venta</option>
                            <option value="Reservación">Reservación</option>
                            <option value="Consumo">Consumo</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <a href="#!" id="btnSearch" class="btn btn-primary btn-sm mt-4">Buscar</a>
                      </div>
                    </div>
                    <div class="row" >
                      <div class="col-sm-4 text-center border-top">
                        <label for="lbl1" class="mt-1"><B>UNITS FACTURADAS</B></label></br>
                        <label for="lbl1" id="id_count_venta">0</label>
                      </div>
                      <div class="col-sm-4 text-center border-top">
                        <label for="lbl2" class="mt-1"><B>UNITS BONIFICADAS</B></label></br>
                        <label for="lbl2" id="id_count_bonif">0</label>
                      </div>
                      <div class="col-sm-4 text-center border-top">
                        <label for="lbl3" class="mt-1"><B>UNITS DESPLAZADAS</B></label></br>
                        <label for="lbl3" id="id_units_desp">0</label>
                      </div>
                    </div>
                  </div>
                </div>
                <table id="tblTrans_dev" class="table table-bordered">
                        <thead class="bg-blue text-light">
                          <tr>
                            <th></th>
                            <th>FECHA</th>
                            <th>LOTE</th>
                            <th>FACTURA</th>
                            <th>TIPO</th>
                            <th>CANTIDAD</th>
                            <th>REFERENCIA</th>
                            <th>CODIGO</th>
                            <th>CLIENTE</th>
                            <th>-</th>
                        </tr>
                        </thead>
                    </table>
                
              </div>
            </div>
          </div>
          
          <div class="tab-pane fade" id="nav-costos" role="tabpanel" aria-labelledby="navCostos">
            <div class="row">
              <div class="col-sm-12">                
                <table id="tblCostos" class="table table-bordered mt-3">
                  <tbody>
                      <tr>
                        <td class="bg-blue text-light"><b>Costo Promedio.</b></td>
                        <td id="id_prec_prom" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>Costo Ultimo.</b></td>
                        <td id="id_ult_prec" class="dt-right">0</td>
                      </tr>
                    </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-margen" role="tabpanel" aria-labelledby="navMargen">
            <div class="row">
              <div class="col-sm-12">
                
                <table id="tblMargen" class="table table-bordered mt-3">
                  <thead class="bg-blue text-light">
                  <tr>
                      <th>CANAL</th>
                      <th>MARGEN BRUTO</th>
                  </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-otros" role="tabpanel" aria-labelledby="navOtros">
            <div class="row">
              <div class="col-sm-12">                
                <table id="tblCostos" class="table table-bordered mt-3">
                  <tbody id="tbody1">
                      <tr>
                        <td class="bg-blue text-light"><b>Clase.</b></td>
                        <td id="id_clase_abc" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>Minimo.</b></td>
                        <td id="id_existencia_minima" class="dt-right">0</td>
                      </tr>
                      <tr>
                        <td class="bg-blue text-light"><b>ReOrden.</b></td>
                        <td id="id_punto_de_reoden" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>Reabastecimiento.</b></td>
                        <td id="id_plazo_rebast" class="dt-right">0</td>
                      </tr>
                    </tbody>
                </table>
              </div>
            </div>            
          </div>

          <div class="tab-pane fade" id="nav-Indicadores" role="tabpanel" aria-labelledby="navIndicadores">
            <div class="row">
              <div class="col-sm-12">                
                <table id="tblIndicadores" class="table table-bordered mt-3">
                  <tbody id="tbody1">
                      <tr>
                        <td class="bg-blue text-light dt-center"><b>Descripción</b></td>
                        <td id="" class ="dt-center">Mes Actual.</td>
                        <td id="" class ="dt-center">Acumulado.</td>
                      </tr>
                      <tr>
                        <td class="bg-blue text-light"><b>TOTAL. FACT.</b></td>
                        <td id="id_total_fact_month" class ="dt-right">0</td>
                        <td id="id_total_fact" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>UNIT. FACT.</b></td>
                        <td id="id_unit_fact_month" class="dt-right">0</td>
                        <td id="id_unit_fact" class="dt-right">0</td>
                      </tr>
                      <tr>
                        <td class="bg-blue text-light"><b>UNIT. BONIF. </b></td>
                        <td id="id_unit_bonif_month" class="dt-right">0</td>
                        <td id="id_unit_bonif" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>PREC. PROM.</b></td>
                        <td id="id_prom_prec_month" class="dt-right">0</td>
                        <td id="id_prom_prec" class="dt-right">0</td>
                      </tr>
                      <tr>
                        <td class="bg-blue text-light"><b>COST. PROM. UNIT</b></td>
                        <td id="id_prom_cost_unit_month" class="dt-right">0</td>
                        <td id="id_prom_cost_unit" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>CONTRIBUCION</b></td>
                        <td id="id_contribucion_month" class="dt-right">0</td>
                        <td id="id_contribucion" class="dt-right">0</td>
                      </tr>
                      <tr>
                        <td class="bg-blue text-light"><b>% MARGEN BRUTO </b></td>
                        <td id="id_margen_bruto_month" class="dt-right">0</td>
                        <td id="id_margen_bruto" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>CANT. DISP. B002</b></td>
                        <td id="id_disp_bodega_month" class="dt-right">0</td>
                        <td id="id_disp_bodega" class="dt-right">0</td>
                      </tr>
                      <tr>
                      <td class="bg-blue text-light"><b>CANT. DISP. UNDS. B002</b></td>
                      <td id="id_disp_bodega_unds_month" class="dt-right">0</td>
                        <td id="id_disp_bodega_unds" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>PROM. UNDS. MES 2022</b></td>
                        <td id="id_prom_unds_mes_month" class="dt-right">0</td>
                        <td id="id_prom_unds_mes" class="dt-right">0</td>
                      </tr>
                      <tr>
                        <td class="bg-blue text-light"><b>CANT. DISP. MES</b></td>
                        <td id="id_cant_disp_mes_month" class="dt-right">0</td>
                        <td id="id_cant_disp_mes" class ="dt-right">0</td>
                      </tr>                      
                    </tbody>
                </table>
              </div>
            </div>            
          </div>

          <div class="tab-pane fade" id="nav-Vinneta" role="tabpanel" aria-labelledby="navVinneta">
            <div class="row">
              <div class="col-sm-12">                
                <table id="tblVinneta" class="table table-bordered mt-3">
                  <tbody id="tbody1">
                      <tr>
                        <td class="bg-blue text-light"><b>VALOR.</b></td>
                        <td id="id_vineta_valor" class ="dt-right">0</td>
                      </tr>
                    </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-Comportamiento" role="tabpanel" aria-labelledby="navComportamiento">
            <div class="row">
              <div class="col-sm-12" >
                <div class="card" style="border-top: none">
                  <div class="card-body">
                    <div class="row">
                    
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="fci">Desde</label>
                          <input type="text" class="input-fecha" id="fci">
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="fcf">Hasta</label>
                          <input type="text" class="input-fecha" id="fcf">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="orderComportamiento" class="text-muted">Filtrar por</label>
                          <select class="form-control" id="orderComportamiento">
                            <option value="1">UNIDADES</option>
                            <option value="2">MONTO FACTURADO</option>
                          </select>
                        </div>
                        
                      </div>
                        <div class="col-sm-1">
                          <a href="#!" id="btnSearchComport" class="btn btn-primary btn-sm mt-4">Buscar</a>
                        </div>                      
                    </div>
                    
                    <div class="row" style="display:none">
                      <div class="col-sm-3 text-center border-top">
                        <label for="lbl1" class="mt-4"><B>PRECIO PROMEDIO</B></label></br>
                        <label for="lbl1" id="lbl1">0</label>
                      </div>
                      <div class="col-sm-3 text-center border-top">
                        <label for="lbl2" class="mt-4"><B>COST. UNITS. PROM.</B></label></br>
                        <label for="lbl2" id="lbl2">0</label>
                      </div>
                      <div class="col-sm-3 text-center border-top">
                        <label for="lbl3" class="mt-4"><B>CONTRIBUCION</B></label></br>
                        <label for="lbl3" id="lbl3">0</label>
                      </div>
                      <div class="col-sm-3 text-center border-top">
                        <label for="lbl4" class="mt-4"><B>% CONTRIBUCION</B></label></br>
                        <label for="lbl4" id="lbl4">0</label>
                      </div>
                    </div>
                  </div>
                </div>
                
                    <div class="graf col-sm-12 mt-3">
                    <div class="col-sm-12 text-right ">
                        <label id="lbl_promedio" class="text-muted">0.00</label>
                      </div>
                        <input type="text" id="idArti" style="display: none;">
                        <div id="comportamientoMen" style="width: 100%; margin: 0 auto;"></div>
                    </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-Transito" role="tabpanel" aria-labelledby="navTransito">
            <div class="row">
              <div class="col-sm-12 mt-3" >
                <div class="table-responsive" >                        
                  <table class="table table-hover table-striped overflow-hidden" id="tbl_transito_articulo" >
                  <thead class="bg-blue text-light">
                    <tr>
                      <th>FECHA PEDIDO</th>                        
                      <th>FECHA ESTIMADA</th>                      
                      <th>DOCUMENTO</th>
                      <th>MERCADO</th>
                      <th>MIFIC</th>
                      <th>PRECIO MIFIC</th>
                      <th>CANTIDAD</th>
                      <th>CANT PEDIDO</th>
                      <th>CANT TRANSITO</th>
                      <th>OBSERVACIONES</th>
                    </tr>
                  </thead>
                  </table>  
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-Mific" role="tabpanel" aria-labelledby="navOtros">
            <div class="row">
              <div class="col-sm-12">                
                <table id="tblCostos" class="table table-bordered mt-3">
                  <tbody id="tbody1">
                      <tr>
                        <td class="bg-blue text-light"><b>PRECIO MIFIC FARMACIA.</b></td>
                        <td id="id_precio_mific_farmacia" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>PRECIO MIFIC PUBLICO.</b></td>
                        <td id="id_precio_mific_public" class="dt-right">0</td>
                      </tr>
                      
                    </tbody>
                </table>
              </div>
            </div>            
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection