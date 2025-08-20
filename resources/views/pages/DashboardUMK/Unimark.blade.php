@extends('layouts.main')
@section('title' , $name)
@section('name_user' , 'Administrador')
@section('metodosjs')
@include('jsViews.js_dashboard')
@endsection
@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-sm-6">
            <h4 class="h4 mt-4">Dashboard</h4>
        </div>
        
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="opcMes" class="text-muted m-0">Filtrar por mes</label>
                        <select class="form-control form-control-sm" id="opcMes">
                            <?php                        
                                $mes = date("m");
                                $meses = array('none','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');

                                for ($i= 1; $i <= 12 ; $i++) {
                                if ($i==$mes) {
                                    echo'<option selected value="'.$i.'">'.$meses[$i].'</option>';
                                }else {
                                    echo'<option value="'.$i.'">'.$meses[$i].'</option>';
                                }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="opcAnio" class="text-muted m-0">por año</label>
                        <select class="form-control form-control-sm" id="opcAnio">
                            <?php
                                $year = date("Y");
                                for ($i= 2018; $i <= $year ; $i++) {
                                    if ($i==$year) {
                                        echo'<option selected value="'.$i.'">'.$i.'</option>';
                                    }else {
                                        echo'<option value="'.$i.'">'.$i.'</option>';
                                    }
                                }
                            ?>
                        </select>  
                    </div>                
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <a href="#!" style="width: 100%" id="filterM_A" class="btn btn-primary float-right mt-3">Aplicar</a>
                    </div>
                </div>                
            </div>
        </div>
    </div>
    @if( Session::get('company_id')==4 )
        <div class="row" style="display: none;">
            <div class="col">
                <div class="custom-control custom-switch mt-2">
                    <input type="checkbox" class="custom-control-input" id="customSwitch1">
                    <label class="custom-control-label" for="customSwitch1">Bolsones</label>
                </div>
            </div>
            <div class="col col-lg-2 mt-2">
                <h5 class="h5" ><span id="id_lbl_ventas_diarias">Venta  </span> <span id="id_ventas_diarias" >0.00</span></h5>
            </div>
            <div class="col col-lg-2 mt-2">
                <div class = 'vts-month-dolar has_standard_tooltip'>
                    <h5 class="h5" id="id_ventas_dolares">Venta Local $ 0.00</h5>
                </div>
                
            </div>
            <div class="col col-lg-2 mt-2">
                
                <h5 class="h5" id="id_ventas_totales">Total Venta C$ 0.00</h5>
            </div>
        </div>
    @endif
    <div class="content-graf mb-5">

        @if( Session::get('company_id')==1 )
        <div class="row justify-content-end " >
            <div class="col-sm-2">
                    <select class="form-control form-control-sm" id="opc_seg_graf01" ></select>
            </div>              
        </div>
        @endif

                                
        @if( Session::get('company_id')==4 )
        <div class="card border-0 shadow-sm mt-3 ">
            <div class="card-body col-sm-12 p-0 mb-2">	
                <div class="p-0 px-car">
                    <div class="flex-between-center scrollbar border border-1 border-300 rounded-2">
                        <table id="table_cierreMesInn" class="table table-striped table-bordered table-sm mt-3 fs--1" width="100%" style="border-collapse: collapse;">
                            <thead>
                                <tr class="bg-blue text-light">
                            
                                    <th colspan="6">CIERRE MES DE <label id="cierreMes"></label> DEL <label id='cierreAnio'></label></th>
                                
                                </tr>
                            </thead>
                            <tbody id='mesCierre'>
                                <tr class="bg-blue text-light text-center">                       
                                    <th>EJECUTIVO</th>
                                    <th>BULTOS</th>
                                    <th>MONTO S/IVA</th>
                                    <th>MONTO C/IVA</th>
                                    <th rowspan="3">PRECIO PROMEDIO S/IVA</th>
                                    <th rowspan="3">PRECIO PROMEDIO C/IVA</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mt-3 ">
            <div class="card-body col-sm-12 p-0 mb-2">	
                <div class="p-0 px-car">
                    <div class="flex-between-center scrollbar border border-1 border-300 rounded-2">
                        <table id="table_VentaCategoria" class="table table-striped table-bordered table-sm mt-3 fs--1" width="100%" style="border-collapse: collapse;">
                            <thead>
                                <tr class="bg-blue text-light">
                            
                                    <th colspan="4">VENTA POR CATEGORIA</th>
                                    <th rowspan="2">PRECIO PROMEDIO S/IVA</th>
                                    <th rowspan="2">PRECIO PROMEDIO C/IVA</th>
                            
                                </tr>
                                <tr>
                                    <th ></th>
                                    <th >BULTOS</th>
                                    <th >MONTO S/IVA</th>
                                    <th >MONTO C/IVA</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-blue text-light" style="text-align:right">
                                    <th ></th>
                                    <th ></th>
                                    <th ></th>
                                    <th ></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row" id="ct04">
            <div class="graf col-sm-12 mt-3">
                <div class="container-vms" id="grafVtsDiario" style="width: 100%; margin: 0 auto"></div>
            </div>
        </div>

        @if( Session::get('company_id')==1 )
        <div class="row mt-3 justify-content-end" >
            <div class="col-sm-2">
                    <select class="form-control form-control-sm" id="opc_seg_graf02" ></select>
            </div>              
        </div>
        @endif
        @if( Session::get('company_id') ==4  )   
        <div class="row" id="ct04">
            <div class="graf col-sm-12 mt-3">
                <div class="container-rvts" id="id_grafica_venta_exportacion" style="width: 100%; margin: 0 auto"></div>
            </div>
        </div>
        @endif
        <div class="row" id="ct04">
            <div class="graf col-sm-12 mt-3">
                <div class="container-rvts" id="grafRealVentas" style="width: 100%; margin: 0 auto"></div>
            </div>
        </div>
        

        <div class="row mt-3 justify-content-end" >
            <div class="col-sm-2">
                    <select class="form-control form-control-sm" id="opc_seg_graf03" ></select>
            </div>              
        </div>
        <div class="row" id="ct04">
            <div class="graf col-sm-12 mt-3">
                <div class="container-vms" id="grafVtsMes" style="width: 100%; margin: 0 auto"></div>
            </div>
        </div>
        @if( Session::get('company_id') !=4  )       
        <div class="row" id="ct04">
            <div class="graf col-sm-12 mt-3">
                <div class="container-vms" id="grafClienteAnual" style="width: 100%; margin: 0 auto"></div>
            </div>
        </div>
        
        <div class="row" id="ct04">
            <div class="graf col-sm-12 mt-3">
                <div class="container-vms" id="grafTicketProm" style="width: 100%; margin: 0 auto"></div>
            </div>
        </div>

        <div class="row" id="ct04">
            <div class="graf col-sm-12 mt-3">
                <div class="container-vms" id="grafSkuAnual" style="width: 100%; margin: 0 auto"></div>
            </div>
        </div>  
        @endif
        <div class="row mt-3" id="cardAnioActual">
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white bg-secondary border-0">
                        <p class="text-white m-0 p-0 divSpinner text-center">ACUMULADO AÑO</p>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3" id="anioAcumulado"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white bg-secondary border-0">
                        <p class="text-white m-0 p-0 divSpinner text-center">PROMEDIO MENSUAL</p>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3" id="porcentaje"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3" id="cardAnioAntePasa">
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white bg-secondary border-0">
                        <p class="text-white m-0 p-0 divSpinner text-center">ACUMULADO AÑO ANTERIOR</p>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3" id="acumuladoAnioAnte"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white bg-secondary border-0">
                        <p class="text-white m-0 p-0 divSpinner text-center">PROMEDIO MENSUAL AÑO ANTERIOR</p>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3" id="porcentajeAnioAnte"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3" id="crecimientoxruta">
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white bg-secondary border-0">
                        <p class="text-white m-0 p-0 divSpinner text-center">CUMPLIMIENTO DE META</p>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered mt-3">
                          <thead>
                            <tr>
                              <th scope="col" class="text-center" style="font-size: 1rem!important">TOTAL</th>
                              <th scope="col" class="text-center" style="font-size: 1rem!important">GENERAL</th>
                            </tr>
                          </thead>
                          <tbody id="tbody01"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-white bg-secondary border-0">
                        <p class="text-white m-0 p-0 divSpinner text-center">ALCANCE DE VENTA POR SEGMENTO</p>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered mt-3">
                          <thead>
                            <tr>
                              <th scope="col" class="text-center">CANAL</th>
                              <th scope="col" class="text-center">META</th>
                              <th scope="col" class="text-center">VENTA</th>
                              <th scope="col" class="text-center">ALCANCE</th>
                            </tr>
                          </thead>
                          <tbody id="tbody02"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if( Session::get('company_id')==4 )
        <div class="row" id="ct05" style="display: none">            
            <div class="graf col-sm-12 mt-3 text-right">
                <figure class="highcharts-figure">
                <select class="selectpicker col-sm-4 form-control form-control-sm mb-2 mt-3" id="select-cate" data-show-subtext="false" data-live-search="true" ></select>
                    <div class="container-cat" id="grafVtsXCateg"></div>
                </figure>
            </div>
        </div>
        @else
        <div class="row" id="ct05">            
            <div class="graf col-sm-12 mt-3 text-right">
                <figure class="highcharts-figure">
                <select class="selectpicker col-sm-4 form-control form-control-sm mb-2 mt-3" id="select-cate" data-show-subtext="false" data-live-search="true" ></select>
                    <div class="container-cat" id="grafVtsXCateg"></div>
                </figure>
            </div>
        </div>
        @endif

        <div class="row" id="ct01">
            <div class="graf col-sm-4 mt-3"><div class="container-vm" id="grafVentas"></div></div>
            <div class="graf col-sm-4 mt-3"><div class="container-rm" id="grafRecupera"></div></div>
            <div class="graf col-sm-4 mt-3"><div class="container-vb" id="grafBodega"></div></div>
        </div>
        <div class="row" id="ct03">
            <div class="graf col-sm-6 mt-3"><div class="container-cv" id="grafCompMontos"></div></div>
            <div class="graf col-sm-6 mt-3"><div class="container-cc" id="grafCompCantid"></div></div>
        </div>
        <br>
        <div class="row" >
            <div class="col-sm-6" >
                <div class="row">    
                <div class="col-sm-2">
                        <div class="form-group">
                            <a href="#!" style="width: 100%" class="btn btn-primary float-right mt-3" onclick="detailAllClients()" type="button" id="btnclick">Mostrar</a>
                        </div>
                    </div>                
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="OpcSegmClt" class="text-muted m-0">SEGMENTO</label>
                            <select class="form-control form-control-sm" id="OpcSegmClt" ></select>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="col-sm-6" >
                <div class="row justify-content-end" >                    
                    <div class="col-sm-4 ">
                        <div class="form-group">
                            <label for="opcSegmentos" class="text-muted m-0">SEGMENTO</label>
                            <select class="form-control form-control-sm" id="opcSegmentos" ></select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <a href="#!" style="width: 100%" class="btn btn-primary float-right mt-3"onclick="Todos_Los_Items()">Mostrar</a>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
        <div class="row" id="ct02">
            <div class="graf col-sm-6 mt-3"><div class="container-tc" id="grafClientes"></div></div>
            <div class="graf col-sm-6 mt-3"><div class="container-tp" id="grafProductos"></div></div>
        </div>
    </div>
    <!-- PAGINA TEMPORAL DE DETALLES -->
    <div id="page-details" class="p-4 border-left" style="background-color: #f1f5f8">
        <div class="row">
            <div class="col-lg-12">
                <a href="#!" class="active-page-details btn btn-outline-primary btn-sm">Regresar</a>
            </div>
        </div>
        <div class="row center">
            <div class="col-sm-12">
                
                
                <div class="card mt-3 border-0 shadow-sm">                    
                  <div class="card-body">                  
                    <div class="row" id="lbl_id_articulos">
                        <div class="col-sm-4 border-right">
                            <h5 class="card-title" id="title-page-tem"></h5>
                            <p class="text-muted" id="fechaFiltrada"></p>
                        </div>
                        <div class="col-sm-8">
                            <div class="row ml-3">
                                <div class="col-sm-4" id="montoMetaContent">
                                    <p class="text-muted m-0" id="txtMontoMeta"></p>
                                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="MontoMeta"></p>
                                </div>
                                <div class="col-sm-4" id="montoRealContent">
                                    <p class="text-muted m-0" id="txtMontoReal"></p>
                                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="MontoReal"></p>
                                </div>
                                <div class="col-sm-4" id="cumplMetaContent">
                                    <p class="text-muted m-0" id="id_detall_unit_bonif">% Cumpl.</p>
                                    <p class="font-weight-bolder text-info" style="font-size: 1.3rem!important" id="cumplMeta"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    
                    <div class="row" id="id_div_detalles_vendedores">
                        <div class="col-sm-11 mt-3">
                           <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                                </div>
                                <input type="text" id="filterDtTemp" class="form-control" placeholder="Buscar">
                            </div>
                        </div>
                        <div class="col-sm-1 mt-3">
                            <div class="input-group">
                                <select class="custom-select" id="cantRowsDtTemp">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="-1">Todo</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    @if( Session::get('company_id') == 1 )
                    <div class="row" >
                        <div class="col-sm-12 mt-3">
                            <div class="table-responsive">
                                <table id="tblAllItemsDiario" class="table table-sm" width="100%" >
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    
                    <div class="row" >
                        <div class="col-sm-6 mt-2">
                            <div class="table-responsive" id="id_div_titulo_Ventas_Rutas">
                                <div id="cjRutVentasRutas">
                                    <table class="table table-bordered table-sm" width="100%" id="dtVentaRuta" >
                                        
                                    </table>
                                </div>        
                            </div>

                        </div>
                        <div class="col-sm-6 mt-2 align-self-center">
                            <div id="id_grafica_pie_ventas_ruta"></div>
                        </div>
                    
                    </div>
                </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" id="id_detalles_articulos" >
            <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                    <div class="col">
                                        <P class="text-muted m-0">PREC. PROM. :</P>
                                        <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_detall_prec_prom"></p>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                <div class="col">
                                    <p class="text-muted mb-0">COST. PROM. UNIT. :</p>
                                    <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_detall_cost_unit"></p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                <div class="col">
                                    <p class="text-muted mb-0">CONTRIBUCION:</p>
                                    <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_detall_marg_contrib"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                <div class="col">
                                    <p class="text-muted mb-0">% MARGEN BRUTO. :</p>
                                    <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_detall_porc_contrib"></p>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                                                 
                </div>
            <div>         
            <div class="row mt-3" id="id_card_info">
            <div class="col-12 col-lg-6 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                <div class="col">
                                    <p class="text-muted mb-0">CANT. DISP. B002. :</p>
                                    <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_disp_cant"></p>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <div class="col-12 col-lg-2 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                <div class="col">
                                    <p class="text-muted mb-0">CANT. DISP. UNDS. B002. :</p>
                                    <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_disp_unds"></p>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="col-12 col-lg-2 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                <div class="col">
                                    <p class="text-muted mb-0">PROM. UNDS. MES. 2022 :</p>
                                    <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_prom_mes_actual"> 0.00</p>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-2 col-xl">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center gx-0">
                                <div class="col">
                                    <p class="text-muted mb-0">CANT. DISP. MES. :</p>
                                    <p class="font-weight-bolder text-center" style="font-size: 1.3rem!important" id="id_disp_meses"></p>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        <div class="row" >

        <div class="col-sm-12 mt-2" >
                
            </div>

            <div class="col-sm-12 mt-2" >
                <div class="table-responsive">
                    <div id="cjRutVentas">
                        <table class="table table-bordered table-sm" width="100%" id="dtTotalXRutaVent" ></table>
                    </div>        
                </div>
            </div>

            
            <div class="col-sm-12 mt-2">
                <div class="table-responsive">
                    <div id="cjRecuperacion">
                        <table class="table table-bordered table-sm" width="100%" id="dtRecuperacion"></table>
                    </div>
                </div>                    
            </div>

            <div class="col-sm-12 mt-2">
                <div class="table-responsive">
                    <div id="cjRecu_GumaPharma">
                        <table class="table table-bordered table-sm" width="100%" id="dtRecu_GumaPharma"></table>
                    </div>
                </div>                    
            </div>

            <div class="col-sm-12 mt-2">
                <div class="table-responsive">
                    <div id="cjCliente">
                        <p class="font-weight-bold">ARTICULOS FACTURADOS</p>
                        <table class="table table-bordered table-sm" width="100%" id="dtCliente"></table>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mt-2">
                <div class="table-responsive">
                    <div id="cjLotes">
                        <p class="font-weight-bold">LOTES</p>
                        <table class="table table-bordered table-sm" width="100%" id="dtLOTES"></table>
                        
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mt-2">
                <div class="table-responsive">
                    <div id="cjArticulo">
                        <p class="font-weight-bold">CLIENTES</p>
                        <table class="table table-bordered table-sm" width="100%" id="dtArticulo"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal:Detalles de ventas de todos los articulos-->
<div class="modal fade modal-fullscreen" id="mdDetailsAllItems" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder text-info" id="id_titulo_modal_all_items" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bodyModal">
            <div class="row dBoder" >
                <div class="col-sm-10 mt-2">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                        </div>
                        <input type="text" id="id_txt_all_item"  class="form-control" placeholder="Buscar">
                    </div>
                </div>
                <div class="col-sm-1 mt-2">
                    <div class="input-group">
                        <select class="custom-select" id="id_select_all_items" >
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="-1">Todo</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-1 mt-2">
                    <a id="exp-to-excel-top-10-articulo" href="#!" class="btn btn-light btn-block text-success" onclick="GetTop10Items()"><i class="fas fa-file-excel"></i> Exportar</a>
                </div>
            </div>
                <div class="row">
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive">
                            <table id="tblAllItems" class="table table-bordered table-sm" width="100%" >
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal:Detalle Venta Comparación Meta, Real Cumplimineto -->
<div class="modal fade" id="mdDetailsVentas" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder text-info" id="vendedorNombre"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bodyModal">
                <div class="row" id="id_detalles_ventas">
                <div class="col-sm-2">
                    <p class="text-muted m-0">Meta Units.</p>
                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="total_Meta_Unidad"></p>
                </div>
                <div class="col-sm-2">                    
                    <p class="text-muted m-0" >Real Units.</p>
                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="total_Real_Unidad"></p>
                </div>
                <div class="col-sm-2 border-right">
                    <p class="text-muted m-0">Diferencia en %</p>
                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="total_Dif_Unidad"></p>
                </div>
                <div class="col-sm-2">
                    <p class="text-muted m-0" >Real Vtas.</p>
                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="total_Real_Efectivo"></p>
                </div>
                <div class="col-sm-2">
                    <p class="text-muted m-0">Meta Vtas.</p>
                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="total_Meta_Efectivo"></p>
                </div>
                <div class="col-sm-2">
                    <p class="text-muted m-0">Diferencia en %</p>
                    <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="total_Dif_Efectivo"></p>
                </div>
            </div>
            <hr>
            <div class="row" id="id_div_Detalles_venta">
                <div class="col-sm-11 mt-2">
                   <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                        </div>
                        <input type="text" id="filterDtDetalle" class="form-control" placeholder="Buscar">
                    </div>
                </div>
                <div class="col-sm-1 mt-2">
                     <div class="input-group">
                        <select class="custom-select" id="cantRowsDtDetalle">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="-1">Todo</option>
                        </select>
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive">
                            <div id="cjVentas">
                                <table class="table table-bordered table-sm" width="100%" id="dtVentas" ></table>
                            </div>
                            <div id="cjVentasFacturas">
                                <table class="table table-bordered table-sm" width="100%" id="dtVentasFacturas" ></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal:Detalles de ventas de todos los articulos-->
<div class="modal fade modal-fullscreen" id="mdDetailsAllItems" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" >
        <div class="modal-content">
            <div class="modal-header d-block">
                <div  class="d-flex">
                    <h5 class="modal-title font-weight-bolder text-info" id="id_titulo_modal_all_items" ></h5>
                
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-10">
                            <p class="text-muted m-0" id="id_sub_titulo_modal_all_items">00</p>
                        </div>
                        <div class="col-sm-2">
                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_total_segmento">C$ 0.00</p>
                        </div>
                    </div>
            </div>
            <div class="modal-body" id="bodyModal">            
            <div class="row" >
                <div class="col-sm-11 mt-2">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                        </div>
                        <input type="text" id="id_txt_all_item"  class="form-control" placeholder="Buscar">
                    </div>
                </div>
                <div class="col-sm-1 mt-2">
                    <div class="input-group">
                        <select class="custom-select" id="id_select_all_items" >
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="-1">Todo</option>
                        </select>
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive">
                            <table id="tblAllItems" class="table table-bordered table-sm" width="100%" >
                                
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Modal:Lista de clientes -->
<div class="modal fade modal-fullscreen" id="mdClientDetail" tabindex="-1" role="dialog"
     aria-labelledby="titleModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder text-info" id="id_titulo_modal_all_clients"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bodyModal">
                <div class="row">
                    <div class="col-sm-11 mt-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i
                                        data-feather="search"></i></span>
                            </div>
                            <input type="text" id="id_txt_all_clients" class="form-control" placeholder="Buscar">
                        </div>
                    </div>
                    <div class="col-sm-1 mt-2">
                        <div class="input-group">
                            <select class="custom-select" id="id_select_all_clients">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="-1">Todo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive">
                            <table id="tblAllClients" class="table table-bordered table-sm" width="100%">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal:Detalle -->
<div class="modal fade" id="mdl_Promedios_Comportamiento" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal-comportamiento">Comportamientos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bodyModal">        
        <div class="row" id="id_row_cliente">
            <div class="col-sm-4">
                <p class="text-muted m-0" id="id_avg_actual_cliente_nombre">text</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_avg_actual_cliente_prom">0.00</p>
            </div>            
            <div class="col-sm-4  border-right">                    
                <p class="text-muted m-0" id="id_avg_anterior_cliente_nombre">text</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_avg_anterior_cliente_prom">0.00</p>
            </div>
            
            <div class="col-sm-4">
                <p class="text-muted m-0">Diferencia en %</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_dif_cliente">0.00</p>
            </div>
        </div>
        <div class="row" id="id_row_ticket">  
            <div class="col-sm-4 ">
                <p class="text-muted m-0" id="id_avg_actual_ticket_nombre">text</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_avg_actual_ticket_prom">0.00</p>
            </div>          
            <div class="col-sm-4 border-right">                    
                <p class="text-muted m-0" id="id_avg_anterior_ticket_nombre">text</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_avg_anterior_ticket_prom">0.00</p>
            </div>
            
            <div class="col-sm-4">
                <p class="text-muted m-0">Diferencia en %</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_dif_ticket">0.00</p>
            </div>
        </div>
        <div class="row" id="id_row_sku">
            <div class="col-sm-4 ">
                <p class="text-muted m-0 " id="id_avg_actual_sku_nombre">text</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_avg_actual_sku_prom">0.00</p>
            </div>
            
            <div class="col-sm-4 border-right">                    
                <p class="text-muted m-0" id="id_avg_anterior_sku_nombre">text</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_avg_anterior_sku_prom">0.00</p>
            </div>
            
            <div class="col-sm-4">
                <p class="text-muted m-0">Diferencia en %</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_difs_skus">0.00</p>
            </div>
        </div>

        <div class="row col-sm-12" id="id_tbl_clientes_no_facturados">
            
            <div class="col-sm-12">
                <div class="input-group mt-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                    </div>
                    <input type="text" id="Search_cliente_no_facturado" class="form-control" placeholder="Buscar...">
                </div>
            </div>
            <table class="table table-striped table-bordered table-sm post_back mt-1" width="100%" id="tblClientes">
                <thead class="bg-blue text-light"></thead>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal:ClientesSinComprar -->
<div class="modal fade" id="mdl_clientes_sin_comprar" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal-comportamiento">Clientes sin Facturar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="">   
        <div class="row" id="">
            <div class="col-sm-4">
                <p class="text-muted m-0" id="id_lbl_txt_anterior">0000</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_lbl_val_anterior">0.00</p>
            </div>            
            <div class="col-sm-4  border-right">                    
                <p class="text-muted m-0" id="id_lbl_txt_actual">000</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_lbl_val_actual">0.00</p>
            </div>
            
            <div class="col-sm-4">
                <p class="text-muted m-0">Diferencia en %</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="id_lbl_val_dif">0.00</p>
            </div>
        </div>     
        <div class="row col-sm-12 " id="">            
        
            <div class="col-sm-12 ">
                <div class="input-group mt-3 mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                    </div>
                    <input type="text" id="Search_cliente_sin_facturar" class="form-control" placeholder="Buscar...">
                </div>
                <table class="table table-striped table-bordered table-sm post_back mt-1" width="100%" id="tblClientesSinComprar">
                    <thead class="bg-blue text-light"></thead>
                </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal:ArticulosNoFacturados -->
<div class="modal fade" id="mdl_articulos_no_facturados" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal-comportamiento">Articulos No Facturados</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="">        
        <div class="row col-sm-12 " id="">            
            <div class="col-sm-12 ">
                <div class="input-group mt-3 mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                    </div>
                    <input type="text" id="Search_articulo_no_facturado" class="form-control" placeholder="Buscar...">
                </div>
                <table class="table table-striped table-bordered table-sm post_back mt-1" width="100%" id="tblArticulos">
                    <thead class="bg-blue text-light"></thead>
                </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal:Detalle -->
<div class="modal fade" id="mdDetails" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModal-01"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="bodyModal">
        <div class="row">
            <div class="col-sm-3">                    
                <p class="text-muted m-0" id="text-mes-actual"></p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="val-mes-actual"></p>
            </div>
            <div class="col-sm-3">
                <p class="text-muted m-0" id="text-anio-pasado"></p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="val-anio-pasado"></p>
            </div>
            <div class="col-sm-2 border-right">
                <p class="text-muted m-0">Diferencia en %</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="dif-porcen-vts"></p>
            </div>
            <div class="col-sm-2">
                <p class="text-muted m-0" id="text-mes-pasado"></p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="val-mes-pasado"></p>
            </div>
            <div class="col-sm-2">
                <p class="text-muted m-0">Diferencia en %</p>
                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="dif-porcen-its"></p>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--=====================================
MODAL 
======================================-->

<div class="modal fade bd-example-modal-lg" id="mSegmento" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	    <div class="modal-content">
		    <div class="modal-header bg-blue text-light" >
                <div class="row col-md-12">
                    <div class="col-md-12 text-center">
                        <b>ALCANCE DE CLIENTES POR SEGMENTO</b>
                    </div>
                    
                </div>
               
		    </div>
		    <div class="modal-body">	
                <table class="table table-striped table-bordered table-sm" id="tb_segmento" width="100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">CANAL</th>
                            <th scope="col" class="text-center">META</th>
                            <th scope="col" class="text-center">CLIENTE</th>
                            <th scope="col" class="text-center">ALCANCE</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySegmento"></tbody>
                </table>  
            </div>
    
            <!---->
		    <div class="modal-footer">			
		    </div>
	    </div>
    </div>
</div>


<div class="modal fade bd-example-modal-lg" id="mCadenaFarmacia" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	    <div class="modal-content">
		    <div class="modal-header bg-blue text-light" >
                <div class="row col-md-12">
                   <div class="col-md-12 text-center" id="id_lbl_mdl_detalles"></div>
                    
                </div>
               
		    </div>
		    <div class="modal-body">	
                <table class="table table-striped table-bordered table-sm" id="tb_cadena_farmacia" width="100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col" class="text-center">CADENAS</th>
                            <th scope="col" class="text-center">VENTA EN C$</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySegmento"></tbody>
                    <tfoot>
                <tr class="bg-blue text-light">
                    <th colspan="2"  style="text-align:center"></th>
                    <th></th>
                </tr>
            </tfoot>
                </table>  
            </div>
    
            <!---->
		    <div class="modal-footer">			
		    </div>
	    </div>
    </div>
</div>

@endsection