@extends('layouts.main')
@section('title' , $name)
    @section('name_user' , 'Administrador')
@section('metodosjs')
    @include('pages.Dashboard.js_dashboard_innova')
    @include('pages.Dashboard.js_chart_SKU')  
    @include('pages.Dashboard.js_chart_cliente_bolson')   
    @include('pages.Dashboard.js_chart_YTD')   
    @include('pages.Dashboard.css_dasboard')
@endsection

@section('content')

    <!-- Header -->
    <div class="row border">
      <div class="col-md-7">            
        <h4 class="h4 text-innova"> INNOVA INDUSTRIAS S.A. </h4>
        <p class="text-muted mb-4">Reportes de ventas de productos, tomando en cuenta el periodo de <span id="tl_periodo"></span>.</p>
      </div>
      <div class="col-md-2 ">
        <div class="form-group">                
          <label for="f1">Desde:</label>
          <input type="text" class="input-fecha" id="desdeInnova">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">                
          <label for="f2">Hasta:</label>
          <input type="text" class="input-fecha" id="hastaInnova">
        </div>
      </div>
      <div class="col-md-1 mt-4">
        <div class="btn-group w-100">               
          <button type="button" class="btn btn-primary-umk btn-block float-right" id="filtrarFechas">Filtrar </button>		
        </div>      
      </div>
      <!-- <div class="col-md-1 mt-4">
        <div class="btn-group w-100">               
          <button type="button" class="btn btn-success btn-block float-right" id="export_excel">Exportar </button>		
        </div>      
      </div> -->
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-4">
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">
            
            <div class="summary-value">
              <div class="d-flex justify-content-between align-items-center">
                <span id="bultos_facturacion"> 0.00 </span>
                <span>
                  <i class="fas fa-comment-dollar"></i>
                </span>
              </div>
            </div>
            <div class="summary-title" style="color: #890fa1">Valor Actual</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">
            
            <div class="summary-value">
              <div class="d-flex justify-content-between align-items-center">
                <span id="bultos_valor">0.00</span>
                <span>
                  <i class="fas fa-boxes"></i>
                </span>
              </div>
            </div>
            <div class="summary-title" style="color: #890fa1">Volumen Actual</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">            
            <div class="summary-value">
              <div class="d-flex justify-content-between align-items-center">
                <span id="bultos_anterior">0.00</span>
                <span>
                  <i class="fa fa-exclamation-circle"></i>
                </span>
              </div>
            </div>
            <div class="summary-title" style="color: #890fa1">Bultos 2024</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">            
            <div class="summary-value"> 
              <div class="d-flex justify-content-between align-items-center">
                <span id="bultos_actual">0.00</span>
                <span>
                  <i class="fa fa-exclamation-circle"></i>
                </span>
              </div>
            </div>
            <div class="summary-title" style="color: #890fa1">Bultos 2025</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tablas -->
    <div class="row g-4 mb-4">      
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-inn-card text-white">            
            <div class="d-flex justify-content-between">
              <h6 class="mb-0 text-bold">CLIENTES FACTURADOS AL: <b><span id="fechaClienteFact" >0000/00/00</span></b> </h6>
              <a href="#!" class="text-white mb-0" onClick="OnWay();" >Detalles <i class="fas fa-arrow-alt-circle-right"></i></a>
            </div>
          </div>
          <div class="card-body">
            <table id="clientesTable" class="display" style="width:100%"></table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-inn-card text-white">            
            <div class="d-flex justify-content-between">
              <h6 class="mb-0">VENTAS POR VENDEDOR: <b><span id="fechaVentaVendedor" >0000/00/00</span></b></h6>
              <a href="#!" class="text-white mb-0" onClick="OnWay();" >Detalles <i class="fas fa-arrow-alt-circle-right"></i></a>
            </div>
          </div>
          <div class="card-body">
            <table id="vendedoresTable" class="display" style="width:100%"></table>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-innova text-white">
            <h6 class="mb-0">GRAFICO DE VENTAS</h6>
          </div>
          <div class="card-body">
            <figure class="highcharts-figure">
                <div id="container"></div>   
            </figure>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-innova text-white">
            <h6 class="mb-0">GRAFICO DE CLIENTES</h6>
          </div>
          <div class="card-body">
            <div id="chart_cliente_bolson"></div>
          </div>
        </div>
      </div>    
    </div>

    <!-- Tablas -->
    <div class="row g-4 mb-4">      
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-innova text-white">            
            <div class="d-flex justify-content-between">
              <h6 class="mb-0">TOP SKUs VENTAS NETAS</h6>
              <a href="#!" class="text-white mb-0" onClick="OnWay();" >Detalles <i class="fas fa-arrow-alt-circle-right"></i></a>
            </div>
            <p class="text-white mb-0" id="fechaSKU">00/00/0000 al  00/00/0000</p>
          </div>
          <div class="card-body">
            <table id="tbl_top_sku" class="display" style="width:100%">
              <tfoot>
                <tr>
                  <th colspan="3" >
                    <div class="row">
                      <div class="col-md-6">
                        <span class="item-left">Total:</span>
                      </div>
                      <div class="col-md-6 text-right">  
                        <span id="total_sku_valor">C$. 0.00</span><br>
                        <span id="total_sku_bultos">0.00 Bls</span>
                      </div>
                    </div>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-innova text-white">            
            <div class="d-flex justify-content-between">
              <h6 class="mb-0">TOP CLIENTES VENTAS NETAS</h6>
              <a href="#!" class="text-white mb-0" onClick="OnWay();" >Detalles <i class="fas fa-arrow-alt-circle-right"></i></a>
            </div>
            <p class="text-white mb-0" id="fechaVentaNeta">00/00/0000 al  00/00/0000</p>
          </div>
          <div class="card-body">
            <table id="tbl_top_clientes" class="display" style="width:100%">
              <tfoot>
                <tr>
                  <th colspan="2">
                    <div class="row">
                      <div class="col-md-6">
                        <span class="item-left">Total:</span>
                      </div>                    
                      <div class="col-md-6 text-right" >  
                        <span id="total_Cliente_valor">C$. 0.00</span><br>
                        <span id="total_Cliente_bultos">0.00 Bls</span>
                      </div>
                    </div>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-4">
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">
            
            <div class="summary-title">
              <div class="d-flex justify-content-between align-items-center border-bottom">
                <span>VENTA NETA</span>
                <span id="anioAnterior">0</span>
              </div>
            </div>
            <div class="summary-value" style="color: #890fa1"><span id="ytd_anterior">0.00</span></div>
          </div>
        </div>
      </div>
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">
            
            <div class="summary-title">
              <div class="d-flex justify-content-between align-items-center border-bottom">
                <span>VENTA NETA</span>
                <span id="anioActual">0</span>
              </div>
            </div>
            <div class="summary-value" style="color: #890fa1"><span id="ytd_actual">0.00</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">            
            <div class="summary-title">
              <div class="d-flex justify-content-between align-items-center border-bottom">
                <span>CRECIMIENTO</span>
                <span>%</span>
              </div>
            </div>
            <div class="summary-value" style="color: #890fa1"><span id="ytd_crecimiento">0.00</span></div>
          </div>
        </div>
      </div>
      <div class="col-md-3 border-end">
        <div class="card summary-card">
          <div class="card-body">            
            <div class="summary-title"> 
              <div class="d-flex justify-content-between align-items-center border-bottom">
                <span id="filtro">FILTRADO POR:</span>
                <span></span>
              </div>
            </div>
            <div class="summary-value" style="color: #890fa1">
              <select class="custom-select" id="tipoDato" onchange="actualizarGraficoYTD()">
                <option value="valor">VALOR</option>
                <option value="bulto">BULTOS</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header bg-innova text-white">
            <h6 class="mb-0">GRAFICO YTD VENTAS</h6>
          </div>
          <div class="card-body">
            <div id="chart_ytd"></div>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="mdl-topsku" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">                    
                    <h4 class="modal-title text-umk" id="id-name-articulo">  </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-sm-11">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"> <i class="fas fa-search"></i> </span>
                                </div>
                                <input type="text" id="id_search_importaciones" class="form-control" placeholder="Buscar..." aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        
                        <div class="col-sm-1">
                            <a id="exp-to-excel" href="#!" class="btn btn-success btn-block text-light float-right button_export_excel"><i class="fas fa-file-excel"></i> </a>
                        </div>      
                    </div>

                    <div class="table-responsive">
                        <table id="tbl_topsku_clientes" class="table table-striped " width="100%"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
