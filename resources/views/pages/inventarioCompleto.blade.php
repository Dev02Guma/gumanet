@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_inventarioCompleto');
@endsection
@section('content')
<div class="container-fluid">
  <div class="row mb-5">
    <div class="col-md-10">
      <h4 class="h4">Inventario Completo</h4>
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
</div>
@endsection