@extends('layouts.main')


@section('title' , $data['name'])
@section('name_user' , 'Administrador')
@section('metodosjs')
  @include('jsViews.js_InventarioTransito');
@endsection
@section('content')
<div class="container-fluid">
  <div class="row mb-5">
    <div class="col-md-10">
      <h4 class="h4">Inventario {{ ($data['ID'] == 0)? 'Transito Sin Codigo' : 'Transito Con Codigo' }} </h4>
    </div>
  </div>
  <span id="id_frm_show" style="display:none">{{ $data['ID'] }}</span>
  <div class="row mt-3">
    <div class="col-sm-11">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
        </div>
        <input type="text" id="InputDtShowSearchFilterArt" class="form-control" aria-describedby="basic-addon1" placeholder ="Buscar en Inventario">
        <div class="input-group-prepend">
          <span class="input-group-text" id="btn_add_item"><i data-feather="plus"></i></span>
        </div>
        <div class="input-group-text bg-transparent" id="btn_upload">
          <span class="fas fa-upload fs--1 text-success" ></span>
        </div>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="input-group mb-3">
        <select class="custom-select" id="InputDtShowColumnsArtic" name="InputDtShowColumnsArtic">
          <option value="5" selected>5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="100">100</option>
          <option value="-1">Todo</option>
        </select>
      </div>
    </div>
    <div class="col-sm-2 p-0 m-0" style="display:none">
      <a id="exp-to-excel" href="#!" onclick="descargarArchivo('inventario')" class="btn btn-light btn-block text-success float-right"><i class="fas fa-file-excel"></i> Exportar</a>
    </div>      
  </div>
  <div class="row">
      <div class="col-12">
        <div class="table-responsive mt-3 mb-2">
              <table class="table table-bordered" width="100%" id="dtInvCompleto"></table>
          </div>
      </div>
  </div>
  <!--MODAL: DETALLE DE ARTICULO-->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="mdDetalleArt" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" id="id_form_save">
      <div class="modal-header d-block">
        
        <span id="txtNumRow" style="display:none">0000</span>   
        <h5 class="modal-title text-center" id="tArticulo"></h5>
      </div>
      <div class="modal-body">
        <div class="row" >   

          <div class="col-sm-12">
            <div class="form-group">
                <label for="txtDescripcion">DESCRIPCION:</label>
                <input type="text" class="form-control" id="txtDescripcion">
                <small id="alert_Descripcion" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>
              
          <div class="col-sm-3">
            <div class="form-group">
                <label for="txtArticulo">ARTICULO:</label>
                <input type="text" class="form-control" id="txtArticulo" >
                <small id="alert_Articulo" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
                <label for="date_estimada">FECHA ESTIMADA DE ARRIBO:</label>
                <input type="text" class="input-fecha" id="date_estimada" >
                <small id="alert_fecha_estimada" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
                <label for="date_pedido">FECHA CREACION PEDIDO:</label>
                <input type="text" class="input-fecha" id="date_pedido" >
                <small id="alert_fecha_pedido" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
                <label for="select_estado">ESTADO:</label>
                <select class="form-control" id="select_estado">
                    <option value="N/D">N/D</option>
                    <option value="PEDIDO">PEDIDO</option>
                    <option value="TRANSITO">TRANSITO</option>
                    <option value="ON-HAND">ON-HAND</option>
                </select>
                <small id="alert_Estado" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

        
          <div class="col-sm-3">
            <div class="form-group">
                <label for="exampleInputEmail1">DOC. (FACT. , BL/AWB ):</label>
                <input type="text" class="form-control" id="txtDocuments" >
                <small id="alert_documento" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
                <label for="txtCantidad">CANTIDAD PEDIDO:</label>
                <input type="text" class="form-control" id="txtCantidad" oninput="validateInput(this)">
                <small id="alert_cantidad" class="form-text text-danger">0.00</small>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
                <label for="txtCantidad">CANTIDAD TRANSITO:</label>
                <input type="text" class="form-control" id="txtCantidadTransito" oninput="validateInput(this)">
                <small id="alert_cantidad" class="form-text text-danger">0.00</small>
            </div>
          </div>
        
          <div class="col-sm-3">
            <div class="form-group">
                <label for="slcMercado">MERCADO:</label>
                <select class="form-control" id="slcMercado">
                    <option value="N/D">N/D</option>
                    <option value="PRIVADO">PRIVADO</option>
                    <option value="MINSA">MINSA</option>
                </select>
                <small id="alert_mercado" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>


          <div class="col-sm-3">
            <div class="form-group">
                <label for="exampleFormControlSelect1">MIFIC:</label>
                <select class="form-control" id="slcMIFIC">
                    <option value="N/D">N/D</option>
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
                <small id="alert_mific" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="form-group">
                <label for="txtPrecioMific">PREC. MIFIC FARMACIA:</label>
                <input type="text" class="form-control" id="txtPrecioMific" oninput="validateInput(this)">
                <small id="alert_precio_mific" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
                <label for="txtPrecioMificPublic">PREC. MIFIC PUBLIC:</label>
                <input type="text" class="form-control" id="txtPrecioMificPublic" oninput="validateInput(this)">
                <small id="alert_precio_mific" class="form-text text-danger">Lorem ipsum dolor sit amet, consectetuer.</small>
            </div>
          </div>
          
          <div class="col-sm-12 mb-3">
              <label for="validationTextarea">OBSERVACIONES: </label>
              <textarea class="form-control" id="txtObservacion" placeholder="Comentarios maximo de 255 caracteres" required></textarea>
              <small id="alert_observaciones" class="form-text text-danger"></small>
          </div>
      </div>      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" id="btnDeleteTransito" @click="DeleteInformacion">Borrar</button>
        <button type="button" class="btn btn-success btn-sm" id="btnSaveTransito" @click="SaveInformacion">Guardar</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_upload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl mt-6" role="document">
      <div class="modal-content">
        <div class="modal-header d-block">
            <h4 class="modal-title text-center" id="id_titulo_modal"> Actualizar Informacion Transito.</h4>
        </div>
        <div class="modal-body py-4 px-5 ">
          <div class="row">
            <div class="col-md-12">  
                <div class="row">
                <div class="col-md-3">
                    <div class="input-group" > 
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="search"><i data-feather="search"></i></span>
                      </div>
                      <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="id_txt_excel" />
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="input-group">
                      <div class="custom-file" id="contInputExlFileTransito">
                        <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" value="upload" class="custom-file-input" name="addExlFileTransito" id="frm-upload"/>
                        <label class="custom-file-label" id="fileLabelTransito" for="addExlFileTransito" data-label="Buscar">Seleccione un archivo Excel
                        </label>
                      </div>
                    </div>
                  </div>
                 
                </div>
            </div>
             
            <div class="col-md-12 mt-3">
              <div class="table-responsive" >                        
                  <table class="table table-hover table-striped overflow-hidden" id="tbl_excel" >
                    <thead>
                        <tr>
                            <th>ARTICULO</th>
                            <th>DESCRIPCION</th>
                            <th>DOCUMENTO</th>
                            <th>CANTIDAD</th>                          
                            <th>FECHA PEDIDO</th>
                            <th>FECHA ESTIMADA</th>
                            <th>MERCADO</th>
                            <th>MIFIC</th>
                            <th>PRECIO MIFIC</th>
                            <th>COMENTARIO</th>
                            <th></th>
                        </tr>
                    </thead>
                  <tbody>
                  <tr colspan="11">
                      <td class="text-center" colspan="11">-</td>
                  </tr>
                  </tbody>
                  </table>  
              </div>
            </div>
            <button class="btn btn-bg-inn btn-primary d-block w-100 mt-3" id="id_send_data_excel" type="submit" name="submit">Procesar</button>
          </div>                                 
            
        </div>
      </div>
    </div>
  </div>


<div class="modal fade bd-example-modal-xl" data-backdrop="static" data-keyboard="false" id="id_dml_add_articulo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" id="xxxxxx">
      <div class="modal-header d-block">
        <h5 class="modal-title text-center">AGREGAR UN ARTICULO NUEVO</h5>
      </div>
      <div class="modal-body">
        <div class="row" >
          
        
          <div class="col-sm-12">
            <div class="form-group">
                <select class="selectpicker form-control form-control-sm" id="frm_select_articulo" data-show-subtext="true" data-live-search="true">                    
                    @foreach($Articulos as $art)
                    <option value="{{strtoupper($art->ARTICULO)}}">{{strtoupper($art->DESCRIPCION )}} - [{{strtoupper($art->ARTICULO)}} ]</option>
                    @endforeach

                </select>
            </div>
          </div>

      </div>      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-sm" id="btn_add_con_codigo" >Guardar</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

</div>
@endsection