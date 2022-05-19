@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
@include('jsViews.js_exportacion');
@endsection
@section('content')  
<div class="container-fluid">	
		<div class="card border-0 shadow-sm mt-3 ">
			<div class="col-sm-12">
				<div class="card-body">					
					<div class="row ">
						<div class="col-sm-6 mt-4 ">
						<span id="id_form_role" style="display:none">{{ Session::get('user_role') }}</span>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
								</div>								
								<input type="text" id="txtSearch" class="form-control" placeholder="Buscar...">
							</div>
						</div>
						<div class="col-sm-1 mt-4 ">
							<div class="input-group">
								<select class="custom-select" id="dtLength" name="dtLength">
								<option value="5" selected>5</option>
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="50">50</option>
								<option value="-1">Todo</option>
								</select>
							</div>
						</div>
						<div class="col-sm-5 border-left">
							<div class="row ">
								<div class="col-sm-5 ">
									<div class="form-group">                
										<label for="f1">Desde:</label>
										<input type="text" class="input-fecha" id="f1">
									</div>
								</div>
								<div class="col-sm-5 ">
									<div class="form-group">                
										<label for="f2">Hasta:</label>
										<input type="text" class="input-fecha" id="f2">
									</div>
								</div>
								<div class="col-sm-2 mt-4 ">
									<a href="#!" class="btn btn-primary float-left" id="BuscarPromocion">
										<i class="material-icons text-white mt-1"  style="font-size: 20px">filter_list_alt</i>
									</a>
								</div>
							</div>
						</div>  
					</div>
				</div>
			</div>
		</div>
		<div class="card border-0 shadow-sm mt-3">			
			<div class="card-body col-sm-12">
				<h5 class="card-title"></h5>
				<div class="row mt-3">
					<div class="col-sm-3">						
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="numero_factura">0.00</h3>
								<p class="card-text" id="">Cantidad de Factura</p>
							</div>
						</div>
					</div>

					<div class="col-sm-3">						
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="id_total_Facturado">$ 0.00</h3>
								<p class="card-text" id="">Monto Total Facturado.</p>
							</div>
						</div>
					</div>

					<div class="col-sm-3">						
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="id_total_moneda_local">C$ 0.00</h3>
								<p class="card-text" id="">Monto Total Facturado.</p>
							</div>
						</div>
					</div>
					<div class="col-sm-3">						
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="id_total_ton"> 0.00</h3>
								<p class="card-text" id="">Total Tonelada Facturada</p>
							</div>
						</div>
					</div>

					
										
				</div>
				<div class="col-sm-12">						
					<table class="table table-striped table-bordered table-sm post_back mt-3" width="100%" id="dtVentaExportacion">
						<thead class="bg-blue text-light"></thead>
					</table>
				</div>	
			</div>
		</div>
		
		
			
	</div>
</div>
@endsection

