@extends('layouts.kardex')
@section('metodosjs')
@include('jsViews.js_inventarioINN')
@endsection
@section('content')
<div class="container-fluid"> 
  <div style="padding:20px">
      <div class="d-flex align-items-center">
      <h4 class="h4">Inventario Innova</h4>
        <div id="id_Status" class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
      </div>
  </div>
  

    <div class="card border-0 shadow-sm ">
      <div class="card-body col-sm-12 p-0 mb-2">	
        <div class="p-0 px-car">
          <div class="flex-between-center scrollbar border border-1 border-300 rounded-2">
          
            <table id="table_resumen" class="table table-striped table-bordered table-sm fs--1" width="100%">
              <thead>
                <tr class="bg-blue text-light">
                  <th style="width: 200px;" rowspan="1">PRODUCTO</th>
                  <th >PT</th>
                  <th style="width: 200px;">JR_SKU</th>
                  <th>JR_TOTAL KG</th>
                  <th>JR_ESTIM. BULTO</th>
                  <th>TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
              <tfoot>
                <tr class="bg-blue text-light">
                    <th colspan="5" style="text-align:right"></th>
                    <th></th>
                </tr>
            </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm ">
      <div class="card-body col-sm-12 p-0">	
        <div class="p-0 px-car">
          <div class="">
          
            <table id="table_materia_prima" class="table table-striped table-bordered table-sm fs--1" width="100%">
              <thead>
                <tr class="bg-blue text-light">
                  <th></th>
                  <th style="width: 200px;" rowspan="1">BLANCO IMPRESO</th>
                  <th >BLANCO MEZCLADO</th>
                  <th style="width: 200px;">TETRA PACK</th>
                  <th>TERMOMECÁNICO</th>
                  <th>PRENSA</th>                  
                  <th>CARTON</th>
                  <th>FOLDER</th>
                  <th>COLOR</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td> </td>
                </tr>
              </tbody>
              </tbody>
              <tfoot>
                <tr class="bg-blue text-light">
                    <th colspan="8" style="text-align:right"></th>
                    <th></th>
                </tr>
            </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>






    <div class="card border-0 shadow-sm mt-3 ">
        <div class="card-body col-sm-12 p-0 mb-2">
            <div class="row col-md-12 mb-3 mt-3" >
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
                            
                                <option value="15" >2 SEMANAS</option>
                                <option value="1" >1 MES</option>
                                <option value="3" >3 MESES</option>
                                <option value="6" >6 MESES</option>
                                <option value="12" >12 MESES</option>
                            
                        </select>
                        <div class="btn input-group-text bg-transparent" id="id_btn_new">
                            <span class="fas fa-history fs--1 text-600"></span>
                        </div>
                    </div>
                </div>
            </div>	
            <div class="p-0 px-car">
            <div class="flex-between-center responsive mb-3" id="kardex">
                    
                
            </div>
            </div>
        </div>
    </div>
              

</div>


@endsection('content')