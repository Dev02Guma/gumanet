
@extends('layouts.Budget')
@section('metodosjs')
@include('jsViews.js_Budget')
@include('jsViews.js_Budget_char')
@endsection
@section('content')
<div class="container-fluid">	
	<div class="card border-0 shadow-sm mt-3">			
	    <div class="card-body col-sm-12">
			<div class="col-sm-12">
                <div class="row ">
					<div class="col-sm-9 mt-4 ">	
                        <h5 class="card-title" id="IdCardTitle">Proyecto 89.</h5>
					</div>
						
                    <div class="col-sm-3 border-left">
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
                                <a href="#!" class="btn btn-primary float-left" id="btnCalcular">
                                    <i class="material-icons text-white mt-1"  style="font-size: 20px">filter_list_alt</i>
                                </a>
                            </div>
                        </div>
                    </div>  
				</div>
                <div class="flex-between-center responsive mb-3" >
                    <table class="stripe row-border order-column" style="width:100%" id="dtProyect89">
                    
                    </table>
                
                    </div>
             
				</div>	
			</div>
		</div>

		<div class="card border-0 shadow-sm mt-3">
			<div class="col-sm-12">				
				<div class="card-body">					
					<div class="row ">
						<div class="col-sm-12">						
                        <div class="row ">
					<div class="col-sm-9 mt-4 ">	
                        <h5 class="card-title">Proyecto 71.</h5>
					</div>
						
                    <div class="col-sm-3 border-left">
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
                                <a href="#!" class="btn btn-primary float-left" id="BuscarVinneta">
                                    <i class="material-icons text-white mt-1"  style="font-size: 20px">filter_list_alt</i>
                                </a>
                            </div>
                        </div>
                    </div>  
				</div>

                <div class="flex-between-center responsive mb-3" >
                    <table class="stripe row-border order-column" style="width:100%" id="dtProyect71">
                    
                    </table>
                
                    </div>
             
				</div>	
						
						</div>
						
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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

