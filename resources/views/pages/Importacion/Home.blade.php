@extends('layouts.ly_reorder')
@section('title' , $NamePath )
@section('name_user' , 'Administrador')
@section('metodosjs')
    @include('pages.Importacion.js_importacion');
    @include('pages.Importacion.css_importacion');
@endsection
@section('content')
<div class="container-fluid">

    <div class="row" >

        <div class="col-md-12">            
            <h4 class="h4 text-umk" id="tl_titulo"> Cargando...</h4>
            <p class="text-muted mb-4">En esta sección podrás visualizar los reportes de importaciones de productos, tomando en cuenta el período de <span id="tl_periodo"></span>, así como los competidores y sus marcas.</p>
        </div>

        <div class="col-md-10" >
            <div class="form-group">
                <label for="Id_Molecula" class="col-form-label-sm text-muted mb-0">MOLECULAS</label>
                <select class="selectpicker form-control form-control-sm" id="Id_Molecula" data-show-subtext="true" data-live-search="true">                    
                    @foreach($ImportacionMuestra as $key)
                    <option value="{{ $key['ARTICULO'] }}">{{ $key['DESCRIPCION'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>	

        <div class="col-md-2">
            <div class="btn-group w-100">               
                <button type="button" class="btn btn-primary-umk btn-block float-right mt-4" id="IdFilterMolecula">Filtrar </button>		
                <button type="button" class="btn btn-success btn-block float-right mt-4" id="modal_importacion"><i class="fas fa-database"></i> </button>
            </div>
        </div>

        <div class="col-sm-7 mt-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white bg-umk border-0">
                    <div class="d-flex justify-content-between bg-umk">
                        <h6 class="font-weight-bold mb-0">COMERCIO NICARAGUA $: </h6>
                        <div class="d-flex align-items-center">
                            <span class="rounded-pill badge-ranking"><i class="fas fa-star"></i> <span id="ranking_valor">0</span> Ranking </span>
                            <span class="rounded-pill badge-participacion ml-1"><i class="fas fa-crown"></i><span id="id_participacion"> 0</span> % Participación</span>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 col-lg-3 text-center ">      
                            <p class="text-muted m-0"> -  </p>
                            <p class="font-weight-bolder text-style" style="">MERCADO</p>
                        </div>
                        <div class="col-sm-12 col-md-5 col-lg-3 text-center">                    
                            <p class="text-muted m-0" id="text-anio-pasado-valor"> -  </p>
                            <p class="font-weight-bolder text-style" id="val-anio-pasado-valor" >$ 0.00 </p>
                        </div>
                        <div class="col-sm-12 col-md-5 col-lg-4 text-center">
                            <p class="text-muted m-0" id="text-anio-actual-valor"> - </p>
                            <p class="font-weight-bolder text-style" id="val-anio-actual-valor">$ 0.00 </p>
                        </div>
                        <div class="col-sm-12 col-md-5 col-lg-2 text-center">
                            <p class="text-muted m-0">%.</p>
                            <p class="font-weight-bolder text-style" id="dif-porcen-valor" > - </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-5 col-lg-3 text-center">      
                            <p class="font-weight-bolder text-style" >UNIMARK S,A.</p>
                        </div>
                        <div class="col-sm-12 col-md-5 col-lg-3 text-center">
                            <p class="font-weight-bolder text-style" id="val-umk-anio-pasado-valor" > $ 0.00  </p>
                        </div>
                        <div class="col-sm-12 col-md-5 col-lg-4 text-center">
                            <p class="font-weight-bolder text-style" id="val-umk-anio-actual-valor"> $ 0.00 </p>
                        </div>
                        <div class="col-sm-12 col-md-5 col-lg-2 text-center">
                            <p class="font-weight-bolder text-style" id="dif-porcen-umk-valor" > - </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-5 mt-3">
            <div class="card shadow-sm">
                <div class="card-header text-white bg-umk">
                    <div class="d-flex justify-content-between ">
                        <h6 class="mb-0 font-weight-bold">COMERCIO NICARAGUA UNIDADES HOMOLOGADAS</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" >
                        <div class="col text-center">                    
                            <p class="text-muted m-0" id="text-anio-pasado-unidades"> - </p>
                            <p class="font-weight-bolder text-style" id="val-anio-pasado-unidades" > 0.00 </p>
                        </div>
                        <div class="col text-center">
                            <p class="text-muted m-0" id="text-anio-actual-unidades">-</p>
                            <p class="font-weight-bolder text-style" id="val-anio-actual-unidades" > 0.00 </p>
                        </div>
                        <div class="col text-center">
                            <p class="text-muted m-0">%.</p>
                            <p class="font-weight-bolder text-style" id="dif-porcen-unidades" > - </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-center">
                            <p class="font-weight-bolder text-style" id="val-umk-anio-pasado-unidades"> 0.00 </p>
                        </div>
                        <div class="col text-center">
                            <p class="font-weight-bolder text-style" id="val-umk-anio-actual-unidades"> 0.00</p>
                        </div>
                        <div class="col text-center">
                            <p class="font-weight-bolder text-style" id="dif-porcen-umk-unidades" > - </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
			<h4 class="h4 text-umk mt-3">TOP 5 COMPETIDORES</h4>
            <p class="text-muted">A continuación se presentan los 5 competidores que más importaciones han realizado en el año actual y el proyectado para el próximo año.</p>
		</div>

        <div class="col-sm-12">
            <table id="tbl_competidores" class="table table-striped" width="100%">
                <thead>
                    <tr class="text-center bg-umk text-white">
                        <th>COMPETIDOR</th>
                        <th>MARCA</th>
                        <th><span id="lbl_val_year_pasado"></span>.</th>
                        <th><span id="lbl_val_year_actual"></span>.</th>
                        <th>%</th>
                        <th>ORIGEN</th>
                        <th><span id="lbl_cant_year_pasado"></span>.</th>
                        <th><span id="lbl_cant_year_actual"></span>.</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody></tbody>
                </tbody>
            </table>
        </div>
		
    </div>

    <div class="modal fade" id="mdlImportacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">                    
                    <h4 class="modal-title text-umk" id="exampleModalLongTitle">Registros de importaciones </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-sm-11">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                                </div>
                                <input type="text" id="id_search_importaciones" class="form-control" placeholder="Buscar..." aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        
                        <div class="col-sm-1">
                            <a id="exp-to-excel" href="#!" class="btn btn-success btn-block text-light float-right button_export_excel"><i class="fas fa-file-excel"></i> </a>
                        </div>      
                    </div>

                    <div class="table-responsive">
                        <table id="tbl_importaciones" class="table table-striped table-bordered dt-responsive" width="100%">
                            <thead>
                                <tr class="text-center bg-umk text-white">
                                    <th>ARTICULO</th>
                                    <th>DESCRIPCION</th>
                                    <th>CANTIDAD</th>
                                    <th>FOB_TOTAL</th>
                                    <th>FOB_UNITARIO</th>
                                    <th>NOMBRE_COMERCIAL</th>
                                    <th>NOMBRE_IMPORTADOR</th>
                                    <th>NRO_RUC</th>
                                    <th>NYEAR</th>
                                    <th>PAIS_ORIGEN</th>
                                    <th>PRESENTACION</th>
                                    <th>UNIDADES</th>
                                    <th>UNIDADES_HOMOLOGADAS</th>
                                    <th>UNIDAD_MED</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> 
@endsection