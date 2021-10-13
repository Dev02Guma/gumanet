@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_inventario');
@endsection
@section('content')
<div class="container-fluid">
  <div class="row mb-5">
    <div class="col-md-10">
      <h4 class="h4">Inventario</h4>
    </div>
    <div class="col-md-2">
      @if( Auth::User()->email=='asaenz@unimarksa.com' || Auth::User()->email=='admin@gmail.com' )
        <a id="" href="{{url('/invCompleto')}}" class="btn btn-primary btn-block">Inventario Completo</a>
      @endif
    </div>
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
            <a class="nav-item nav-link" id="navCostos" data-toggle="tab" href="#nav-costos" role="tab" aria-controls="nav-trans" aria-selected="false">Costos</a>
            <a class="nav-item nav-link" id="navTransaccion" data-toggle="tab" href="#nav-trans" role="tab" aria-controls="nav-trans" aria-selected="false">Transacciones</a>
            
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
                  </div>
                </div>
                <table id="tblTrans" class="table table-bordered mt-2">
                    <thead class="bg-blue text-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Lote</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Referencia</th>
                    </tr>
                    </thead>
                    <tbody id="tbody1">
                      <tr>
                        <td colspan="5"><center>No hay datos que mostrar</center></td>
                      </tr>
                    </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="nav-costos" role="tabpanel" aria-labelledby="navCostos">
            <div class="row">
              <div class="col-sm-12">                
                <table id="tblCostos" class="table table-bordered mt-3">
                  <tbody id="tbody1">
                      <tr>
                        <td class="bg-blue text-light"><b>Precio Promedio.</b></td>
                        <td id="id_prec_prom" class ="dt-right">0</td>
                      </tr>
                      <tr >
                        <td class="bg-blue text-light"><b>Ultimo Costo.</b></td>
                        <td id="id_ult_prec" class="dt-right">0</td>
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