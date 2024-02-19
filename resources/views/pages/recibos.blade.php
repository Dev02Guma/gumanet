@extends('layouts.main')
@section('title' , $data['name'])
@section('name_user' , 'Administrador')
@section('metodosjs')
@include('jsViews.js_recibos');
@endsection
@section('content')  
<div class="container-fluid">	
		<div class="card border-0 shadow-sm mt-3 ">
			<div class="col-sm-12">
				<div class="card-body">					
					<div class="row ">
						<div class="col-sm-2 mt-4 ">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
								</div>
								<input type="text" id="txtSearch" class="form-control" placeholder="Buscar...">
							</div>
						</div>

					<div class="col-sm-4 mt-4 ">
						<div class="form-group">
							<select class="selectpicker form-control" id="dtCliente" data-show-subtext="true" data-live-search="true">					
								<option selected value="">TODOS</option>
								@foreach($clientes as $key)										
									<option value="{{$key['CLIENTE']}}">{{ $key['NOMBRE'] }}</option>
								@endforeach
							</select>
						</div>
					</div>

						<div class="col-sm-1 mt-4 ">
							<div class="input-group">
								<select class="custom-select" id="dtRutas" name="dtRutas">
								<option selected value="">Todos</option>
									@foreach($rutas as $key)
										<option value="{{ $key['VENDEDOR'] }}"> {{ $key['VENDEDOR'] }} - {{ $key['NOMBRE'] }}</option>
									@endforeach
								</select>
							</div>
						</div>

						

						<div class="col-sm-1 mt-4 ">
							<div class="input-group">
								<select class="custom-select" id="dtStatus" name="dtLength">
								<option value="" selected>Todos</option>
								<option value="0">Pendiente</option>
								<option value="1">Ingresado</option>
								<option value="2">Verificado</option>								
								</select>
							</div>
						</div>
						
						<div class="col-sm-4 border-left">
							<div class="row ">
								<div class="col-sm-4 ">
									<div class="form-group">                
										<label for="f1">Desde:</label>
										<input type="text" class="input-fecha" id="f1">
									</div>
								</div>
								<div class="col-sm-4 ">
									<div class="form-group">                
										<label for="f2">Hasta:</label>
										<input type="text" class="input-fecha" id="f2">
									</div>
								</div>
								<div class="col-sm-2 mt-4 ">
									<div class="form-group"> 
										<a href="#!" class="btn btn-primary float-left" id="BuscarVinneta">
											<i class="material-icons text-white mt-1"  style="font-size: 20px">filter_list_alt</i>
										</a>
									</div>
								</div>	
								<div class="col-sm-2 mt-4">
									<div class="form-group"> 
										<a href="#!" class="btn btn-primary float-left" id="resument">
											<i class="material-icons text-white mt-1"  style="font-size: 20px">local_printshop</i>
										</a>
									</div>
								</div>							
							</div>
						</div>  
					</div>
				</div>
			</div>
		</div>
		<div class="card border-0 shadow-sm mt-3">	
			<div class="card-body col-sm-12">
				<div class="row">
					<div class="col-sm-3">
						<p class="text-muted m-0" >Ingresado por Vendedor.  <i class="material-icons text-secondary font-weight-bold" style="font-size:15px">done</i></p>
						<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_valor_pendiente">C$ 0.00</p>
					</div>
					<div class="col-sm-5">
						<p class="text-muted m-0" id="text-anio-pasado">Validado por Cartera. <i class="material-icons text-success font-weight-bold" style="font-size:15px">done_all</i></p>
						<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_valor_ingresado">C$ 0.00</p>
					</div>
					<div class="col-sm-2 border-right" style="display:none">
						<p class="text-muted m-0">Validado por Tesoreria. <i class="material-icons text-success font-weight-bold" style="font-size:15px">done_all</i></p>
						<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_valor_verificado">C$ 0.00</p>
					</div>
					<div class="col-sm-2">
						<p class="text-muted m-0" id="text-mes-pasado">Total</p>
						<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_valor_Total" >C$ 0.00</p>
					</div>
					<div class="col-sm-2">
						<p class="text-muted m-0"></p>
						<p class="font-weight-bolder" style="font-size: 1.3rem!important" ></p>
					</div>
				</div>												
				<table class="table table-striped table-bordered table-sm post_back mt-3" width="100%" id="dtVinneta">
					<thead class="bg-blue text-light"></thead>
				</table>
			</div>
		</div>				
	</div>
</div>

<!-- Modal -->
<div class="modal fade " id="mdlAttachFile" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document" >
		<div class="modal-content">
		<div class="modal-header">			
			<h5 class="modal-title" id="exampleModalLongTitle">Documentos Adjuntos Recibo Nº : <span id="id_Factura_history">0</span></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body" id="id_contenido_history" style="background-color: #f1f5f8;">
		</div>
		
		</div>
	</div>
</div>

<div class="modal fade" id="mdlAnulacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle">Anulación de recibo</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<span id="id_request">0</span>
			<div class="form-group">
				<label for="message-text" class="col-form-label">Comentario:</label>
				<textarea class="form-control" id="message-text"></textarea>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="id_frm_save_anulacion">Guardar</button>
		</div>
		</div>
	</div>
</div>

<div class="modal fade" id="mdlResumen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Resumen de recibos. </h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">		
			<div class="row">           
				<div class="col-sm-2 ">
					<p class="text-muted m-0">RUTA</p>
					<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id-form-ruta"></p>
				</div>
				<div class="col-sm-5 ">
					<p class="text-muted m-0">EJECUTIVO</p>
					<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id-form-ruta-name"></p>
				</div>
				<div class="col-sm-2 border-right">
					<p class="text-muted m-0">FECHA</p>
					<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id-form-time">0.00</p>
				</div>

				<div class="col-sm-3 border-right">
					<p class="text-muted m-0">TOTAL</p>
					<p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id-form-Total">0.00</p>
				</div>
				
			</div>

			<div class="mt-3" id="dtViewLiquidacion"></div>

			<div class="form-group">
				<label for="message-text" class="col-form-label" id="id-nota">Nota:</label>
				<textarea class="form-control" id="id-coment" placeholder="Escriba una nota."></textarea>
			</div>


		</div>
		<div class="modal-footer">			
			<button type="button" class="btn btn-primary" id="id-print-pdf">Imprimir</button>
		</div>
		</div>
	</div>
</div>
@endsection

