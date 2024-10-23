
@extends('layouts.Budget')
@section('metodosjs')
@include('jsViews.js_Budget_char_89')
@include('jsViews.js_Budget_89')

@endsection
@section('content')
<div class="container-fluid">	
    
	<div class="card border-0 shadow-sm mt-3">		
    
	    <div class="card-body col-sm-12">
			<div class="col-sm-12">
                <div id="Id_Progress_Bar"></div>
                <h5 class="card-title pb-0 mb-0">Proyecto 89 </h5>
                <p class="font-italic text-muted pt-0 mt-0">Articulos desplazados del <span id="spn_dtIni"></span> al <span id="spn_dtEnd"></span></p>
                <div class="row ">
                    
					<div class="col-sm-9 mt-4 ">	
                        <div class="input-group">
                            <input type="text" id="txt_Search89" class="form-control" placeholder="Buscar...">
                            <div class="input-group-prepend">
                                <span class="btn-change-color text-white input-group-text" id="btnCalcular" ><i data-feather="search"></i></span>
                            </div>
                        </div>
					</div>
						
                    <div class="col-sm-3 border-left">
                        <div class="row ">
                            <div class="col-sm-6 ">
                                <div class="form-group">                
                                    <label for="f1">Desde:</label>
                                    <input type="text" class="input-fecha" id="f1">
                                </div>
                            </div>
                            <div class="col-sm-6 ">
                                <div class="form-group">                
                                    <label for="f2">Hasta:</label>
                                    <input type="text" class="input-fecha" id="f2">
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>  
				</div>
                <div class="flex-between-center responsive mb-3" >
                    
                    <table class="stripe row-border order-column" style="width:100%" id="dtProyect89">
                        <thead class="bg-blue text-light"></thead>
                    
                    </table>
                
                    </div>
             
				</div>	
			</div>
		</div>

			
	</div>
</div>

<div class="modal fade modal-fullscreen" id="mdl_char_product" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder text-info" id="id_titulo_modal_all_items" ></h5>
                
                <div class="form-group mr-3">
                    <input type="text" id="idArti" style="display: none;">
                    <label for="orderComportamiento" class="text-muted">Filtrar por</label>
                    <select class="form-control" id="orderComportamiento89">
                        <option value="1">UNIDADES</option>
                        <option value="2">MONTO FACTURADO</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-body" id="bodyModal">      
                <div class="graf col-sm-12 mt-3">
                    <div class="container-vms" id="grafSkuAnual" style="width: 100%; margin: 0 auto"></div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

