@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
@include('jsViews.js_vinneta');
@endsection
@section('content')  
<div class="container-fluid">	
		<div class="card border-0 shadow-sm mt-3 ">
			<div class="col-sm-12">
				<div class="card-body">					
					<div class="row ">
						<div class="col-sm-6 mt-4 ">
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
									<a href="#!" class="btn btn-primary float-left" id="BuscarVinneta">Filtrar</a>
								</div>
							</div>
						</div>  
					</div>
				</div>
			</div>
		</div>
		<div class="card border-0 shadow-sm mt-3">	
			<div class="card-body col-sm-12">
			<h5 class="card-title">Por Facturas.</h5>					
				<div class="row mt-3">

					<div class="col-sm-2">						
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="numero_factura">0.00</h3>
								<p class="card-text" id="">Facturas con Viñetas.</p>
							</div>
						</div>
					</div>

					<div class="col-sm-2">						
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="id_total_Facturado">C$ 0.00</h3>
								<p class="card-text" id="">Monto Total Facturado.</p>
							</div>
						</div>
					</div>

					<div class="col-sm-2">
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="MontoVinneta">C$ 0.00</h3>
								<p class="card-text">Total en Viñetas.</p>
							</div>
						</div>
					</div>	
					
					<div class="col-sm-2">
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="id_roi"> 0.00</h3>
								<p class="card-text">ROI</p>
							</div>
						</div>
					</div>	

					<div class="col-sm-4">
						<div class="card text-center">
							<div class="card-body">
								<h3 class="card-title" id="MontoPagado">C$ 0.00</h3>
								<p class="card-text">Total Pagado en Viñetas</p>
							</div>
						</div>
					</div>

				</div>
				<table class="table table-striped table-bordered table-sm post_back mt-3" width="100%" id="dtVinneta">
					<thead class="bg-blue text-light"></thead>
				</table>
			</div>
		</div>

		<div class="card border-0 shadow-sm mt-3">
			<div class="col-sm-12">				
				<div class="card-body">
					<h5 class="card-title">Por rutas.</h5>
					<table class="table table-striped table-bordered table-sm post_back" width="100%" id="dtResumenVinneta" >
					<thead class="bg-blue text-light"></thead>
					<tfoot>
						<tr>
							<th colspan="11" style="text-align:right">Total:</th>
						</tr>
					</tfoot>
					</table>
				</div>
			</div>
		</div>		
	</div>
</div>
@endsection

