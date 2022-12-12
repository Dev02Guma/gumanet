
@extends('layouts.main')
@section('title' , $data['name'])
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_infraestructura');
@endsection
@section('content')  
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h4 class="h4 mb-4">Proyectos por Unidades de Negocio.</h4>
		</div>
	</div>
	<div class="row">
		<div class="col-md-11">
			<div class="input-group mt-3">
				<div class="input-group-prepend">
					<span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
				</div>
				<input type="text" id="txtBuscarProyecto" class="form-control" placeholder="Buscar..." aria-label="Username" aria-describedby="basic-addon1">
			</div>
		</div>
		
		<div class="col-md-1">
			<div class="form-group">
				<label for="orderByDate" class="text-muted m-0">UNIDAD</label>
				<select class="form-control form-control-sm" id="txtSelectCliente" name="InputDtShowColumnsInvTotal">
					<option value="" selected="selected">Todos</option>					
					@foreach ($Companies as $c)
					<option value="{{$c->name}}">{{$c->name}} </option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
	<div class="row">
	    <div class="col-12 mb-4">
	        <div class="table-responsive mt-3">
	            <table class="table table-bordered table-sm" width="100%" id="TblProjects"></table>
	        </div>
	    </div>
	</div>
</div>
@endsection