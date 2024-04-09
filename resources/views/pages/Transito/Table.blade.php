@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_InventarioTransito');
@endsection
@section('content')
<div class="container-fluid">
  <div class="row mb-5">
    <div class="col-md-10">
      <h4 class="h4">Inventario Transito</h4>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-sm-11">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
        </div>
        <input type="text" id="InputDtShowSearchFilterArt" class="form-control" aria-describedby="basic-addon1">
        <div class="input-group-prepend">
          <span class="input-group-text" id="btn_add_item"><i data-feather="plus"></i></span>
        </div>
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
<div class="modal fade bd-example-modal-xl" data-backdrop="static" data-keyboard="false" id="mdDetalleArt" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" id="id_form_save">
      <div class="modal-header d-block">
        <h5 class="modal-title text-center" id="tArticulo"></h5>
      </div>
      <div class="modal-body">
        <div class="row" >
          <div class="col-sm-3">
            <div class="form-group">
                <label for="txtArticulo">ARTICULO:</label>
                <input type="text" class="form-control" id="txtArticulo" >
                <small id="alert_Articulo" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-9">
            <div class="form-group">
                <label for="txtDescripcion">DESCRIPCION:</label>
                <input type="text" class="form-control" id="txtDescripcion">
                <small id="alert_Descripcion" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
                <label for="date_estimada">FECHA ESTIMADA:</label>
                <input type="text" class="input-fecha" id="date_estimada" >
                <small id="alert_fecha_estimada" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="form-group">
                <label for="date_pedido">FECHA PEDIDO:</label>
                <input type="text" class="input-fecha" id="date_pedido" >
                <small id="alert_fecha_pedido" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>
        
          <div class="col-sm-3">
            <div class="form-group">
                <label for="exampleInputEmail1">DOCUMENTO:</label>
                <input type="text" class="form-control" id="txtDocuments" >
                <small id="alert_documento" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
                <label for="txtCantidad">CANTIDAD:</label>
                <input type="text" class="form-control" id="txtCantidad">
                <small id="alert_cantidad" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>
        
          <div class="col-sm-3">
            <div class="form-group">
                <label for="slcMercado">MERCADO:</label>
                <select class="form-control" id="slcMercado">
                    <option>N/D</option>
                    <option>PRIVADOR</option>
                    <option>INSTITUCION</option>
                </select>
                <small id="alert_mercado" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
                <label for="exampleFormControlSelect1">MIFIC:</label>
                <select class="form-control" id="slcMIFIC">
                    <option>N/D</option>
                    <option>SI</option>
                    <option>NO</option>
                </select>
                <small id="alert_mific" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-12 mb-3">
              <label for="validationTextarea">OBSERVACIONES: </label>
              <textarea class="form-control" id="txtObservacion" placeholder="Lorem ipsum dolor sit amet" required></textarea>
              <small id="alert_observaciones" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
          </div>
      </div>
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-sm" id="btnSaveTransito" @click="SaveInformacion">Guardar</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
</div>
@endsection