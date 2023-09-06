@extends('layouts.main')
@section('metodosjs')
@include('jsViews.js_promocionesRuta')
@endsection
@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm mt-3">			
        <div class="card-body col-sm-12">
            <h5 class="card-title"></h5>
            
            <div class="card border-0 shadow-sm mt-3 ">
                <div class="card-body col-sm-12 p-0 mb-2">
                    <div class="col-md-12 mb-3" >
                        <span id="id_form_role" style="display:none">{{ Session::get('user_role') }}</span>                        
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                            </div>								
                            <input type="text" id="id_txt_buscar" class="form-control" placeholder="Buscar...">
                        </div>
                    </div>	
                    <div class="p-0 px-car">
                    <div class="table-responsive flex-between-center scrollbar border border-1 border-300 rounded-2">
                        <table id="table_promociones" class="table table-striped table-bordered table-sm mt-3 fs--1" width="100%">
                        <thead>
                            <tr class="bg-blue text-light">
                            <th style="width: 550px;">DESCRIPCIÓN</th>
                            <th style="width: 70px;">FECHA DE INICIO</th>
                            <th style="width: 80px;">FEHA DE FIN</th>
                            <th>META DE VENTA.</th>
                            <th>META DE UND.</th>
                            <th>VTA. PROM. {{date('Y') -1}}</th>
                            <th>UND PROM. {{date('Y') -1}}</th>
                            <th>VENTA ACUMULADAS</th>
                            <th>VENTA ACUMULADAS UND.</th>
                            <th>VENTA {{ @strtoupper($mesActual) }}</th>
                            <th>VENTA UND. {{ @strtoupper($mesActual) }}</th>
                            </tr>
                        </thead>
                            <tbody>
                                @foreach($Promociones as $p)
                                    <tr>
                                        <td style="width: 550px;">
                                            <div class="d-flex align-items-center position-relative mt-2">                                
                                                <div class="flex-1 ms-3">
                                                <a href="#!" id="exp_more" class="exp_more text-dark" idArt="{{ $p['Articulo'] }}" ini="{{$p['fechaIni']}}" ends="{{$p['fechaFin']}}" met="{{ $p['MetaUnd'] }}"><h6 class="mb-0 fw-semi-bold"><div class="stretched-link text-900">{{ $p['Descripcion'] }}</div></h6></a>
                                                <p class="text-500 fs--2 mb-0">{{ $p['Articulo'] }}  |  C$ {{ @number_format($p['Precio'],2) }}  |  {{ $p['NuevaBonificacion'] }}</p>
                                                <p class="text-500 fs--2 mb-0"><b>{{ $p['Promocion'] }}</b></p>
                                                </div>
                                            </div>
                                        </td>

                                        <td  style="width: 120px;">
                                            <div class="pe-4 border-sm-end border-200">
                                                <h6 class="fs--2 text-600 mb-1">{{ date('d M, Y', strtotime($p['fechaIni'])) }}</h6>                    
                                            </div> 
                                        </td>

                                        <td  style="width: 120px;">
                                            <div class="pe-4 border-sm-end border-200">
                                                <h6 class="fs--2 text-600 mb-1">{{ date('d M, Y', strtotime($p['fechaFin'])) }}</h6>                    
                                            </div> 
                                        </td>

                                        <td>
                                            <div class="pe-4 border-sm-end border-200">
                                                <h6 class="fs--2 text-600 mb-1">C$ {{ @number_format($p['ValMeta'],2) }}</h6>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="pe-4 border-sm-end border-200 text-center">
                                                <h6 class="fs--2 text-600 mb-1">{{ @number_format($p['MetaUnd'],0) }}</h6>                    
                                            </div> 
                                        </td>

                                        <td>
                                            <div class="pe-4 border-sm-end border-200 text-center">
                                                <h6 class="fs--2 text-600 mb-1"C$>C$ {{ @number_format($p['Promedio_VAL'],2) }}</h6>                  
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="pe-4 border-sm-end border-200 text-center">
                                                <h6 class="fs--2 text-600 mb-1">{{ @number_format($p['Promedio_UND'],2) }}</h6>                    
                                            </div> 
                                        </td>

                                       
                                        <td>
                                            <div class="pe-4 border-sm-end border-200">
                                                <h6 class="fs--2 text-600 mb-1">C$ {{ @number_format($p['Venta'],2) }} </h6>                    
                                            </div> 
                                        </td>
                                        
                                        
                                        <td>
                                            <div class="pe-4 border-sm-end border-200 text-center">
                                                <h6 class="fs--2 text-600 mb-1">{{ @number_format($p['VentaUND'],0) }}</h6>                    
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="pe-4 border-sm-end border-200">
                                                <h6 class="fs--2 text-600 mb-1">C${{ @number_format($p['VentaMActual'],2) }}</span></h6>                    
                                            </div> 
                                        </td>
                                        <td>
                                            <div class="pe-4 border-sm-end border-200 text-center">
                                                <h6 class="fs--2 text-600 mb-1">{{ @number_format($p['VentaUNDMActual'],0) }}</h6>                    
                                            </div> 
                                        </td>
                                    </tr>
                                @endforeach
                        </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>      

</div>
@endsection('content')