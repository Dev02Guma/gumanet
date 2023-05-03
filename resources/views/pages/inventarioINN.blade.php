@extends('layouts.kardex')
@section('metodosjs')
@include('jsViews.js_inventarioINN')
@endsection
@section('content')
<div class="container-fluid"> 
    <div class="row">
        <div class="col-md-12">
            <h4 class="h4 mb-4">Inventario Innova</h4>
        </div>
	</div>

    <div class="card border-0 shadow-sm mt-3">			
        <div class="card-body col-sm-12">
            <div class="row mt-3">
                <div class="col-sm-3">						
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title" id="numero_factura">0.00</h3>
                            <p class="card-text" id="">-----</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">						
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title" id="id_total_Facturado">0.00</h3>
                            <p class="card-text" id="">-----</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title" id="MontoVinneta">0.00</h3>
                            <p class="card-text">-----</p>
                        </div>
                    </div>
                </div>	
                
                <div class="col-sm-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title" id="id_roi">0.00</h3>
                            <p class="card-text">-----</p>
                        </div>
                    </div>
                </div>	
            </div>
            <div class="col-sm-12">						
                <table class="table table-striped table-bordered table-sm post_back mt-3" width="100%" id="dtVinneta">
                    <thead class="bg-blue text-light"></thead>
                </table>
            </div>	
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">			
        <div class="card-body col-sm-12">
            <h5 class="card-title"></h5>
            <div class="card border-0 shadow-sm mt-3 ">
                <div class="card-body col-sm-12 p-0 mb-2">
                    <div class="row col-md-12 mb-3" >
                        <span id="id_form_role" style="display:none">{{ Session::get('user_role') }}</span>                        
                        <div class="input-group col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                            </div>								
                            <input type="text" id="id_txt_buscar" class="form-control" placeholder="Buscar...">
                        </div>
                        <div class="col-md-3 border-left">
                            <div class="input-group">
                                <select class="custom-select"  id="id_select_mes">
                                   
                                        <option value="1" >1 MES</option>
                                        <option value="3" >3 MESES</option>
                                        <option value="6" >6 MESES</option>
                                   
                                </select>
                                <div class="btn input-group-text bg-transparent" id="id_btn_new">
                                    <span class="fas fa-history fs--1 text-600"></span>
                                </div>
                            </div>
                        </div>
                    </div>	
                    <div class="p-0 px-car">
                    <div class="flex-between-center responsive mb-3" id="kardex">
                        <table class="table table-bordered " id="tbl_kardex" style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <td class="bg-blue text-light text-center">
                                        ARTICULO
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <b>NO HAY REGISTROS PARA ESTE MES</b>
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>      

</div>


@endsection('content')