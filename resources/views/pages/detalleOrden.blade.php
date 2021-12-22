@extends('layouts.main')
@section('metodosjs')
@include('jsViews.js_detalleOrdenes')
@endsection
@section('content')
<div class="container-fluid">
    <!-- [ Main Content ] start -->
    <div class="row my-3">
        <div class="col-sm-12">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                </div>
                <input type="text" id="InputDtShowSearchFilterArt" class="form-control" placeholder="Buscar en ordenes de produccion" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
    </div>
    <!-- [ Header detalle ordenes ] start -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header mt-3">
                    <h5 class="text-secondary">ORDEN DE PRODUCCIÓN</h5>
                </div>
                <div class="card-block table-border-style">
                    <div class="table-responsive">
                        <div class="table-responsive mt-3 mb-2">
                            <table class="table table-bordered table-sm table-hover" width="99.9%" id="dtDetalleOrdenes"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- modal detalle Orden -->
    <div class="modal fade bd-example-modal-xl" data-backdrop="static" data-keyboard="false" id="mdDetalleOrd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-block">
                    <div class="row">
                        <!-- [ Header orden produccion ] start -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-center align-items-center pt-1 pb-0 bg-blue">
                                    <h5 class="modal-title text-center align-self-center p-1 bg-blue text-white" id="tDetalleOrdenes"></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="text-muted m-0" >Merma Yankee Dry (kg):</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="merma-yankee-dry">C$ 0.00</p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="merma-yankee-dry" class="text-muted m-0">Merma Yankee Dry (%):</p>
                                            <p class="font-weight-bolder"  style="font-size: 1.3rem!important" id="porcentaje_merma"> </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="residuos-pulper" class="text-muted m-0">Residuos del Pulper (kg):</p>
                                            <div class="input-group">
                                                <p class="font-weight-bolder" id="residuos-pulper" style="font-size: 1.3rem!important"> </p>
                                                <p class="font-weight-bolder ml-2"  style="font-size: 1.3rem!important" id="porcentaje_rp"> </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="lav-tetrapack" class="text-muted m-0">Lavadora de Tetrapack (kg):</p>
                                            <div class="input-group">
                                                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="lav-tetrapack"> </p>
                                                <p class="font-weight-bolder ml-2" style="font-size: 1.3rem!important" id="porcentaje_tpack"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p for=""  class="text-muted m-0">Horas Trabajadas:</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="hrsTrabajadas"> </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="factorFibral"  class="text-muted m-0">Factor fibral:</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="factor-fibral"> </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="produccionNeta"  class="text-muted m-0">PROD.REAL (kg):</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="produccionNeta"></p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="produccionReal"  class="text-muted m-0">PROD.TOTAL (kg):</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="produccionReal"></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- [ Header orden produccion ] end -->
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="card card-social">
                                <div class="card-header text-center p-2 bg-blue">
                                    <h5 class="text-white m-0">Agua</h5>
                                </div>
                                <div class="card-block  mx-2  my-1 border-bottom">
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-auto">
                                            <h6 class="mb-0">Consumo: </h6>
                                        </div>
                                        <div class="col text-right">
                                            <h6 id="AtotalConsumo"> </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-2 m-0 p-0">
                                            <h6 class="text-left m-b-10"><span class="text-muted m-r-5">Inicial:</span></h6>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="text-muted text-right m-b-10"><span class="text-muted m-r-5" id="Ainicial"></span> m<sup>3</sup></h6>
                                        </div>
                                        <div class="col-2 m-0 p-0">
                                            <h6 class="text-right m-b-10"><span class="text-muted m-r-5">Final:</span></h6>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="text-muted text-right  m-b-10"><span class="text-muted m-r-5" id="Afinal"></span> m<sup>3</sup></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-social">
                                <div class="card-header text-center p-2 bg-blue">
                                    <h5 class="m-0 text-white">Electricidad</h5>
                                </div>
                                <div class="card-block mx-2  my-1 border-bottom">
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-auto">
                                            <h6 class="mb-0">Consumo: </h6>
                                        </div>
                                        <div class="col text-right">
                                            <h6 id="EtotalConsumo"></h6>
                                        </div>
                                    </div>
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-auto">
                                            <h6 class="mb-0">Factor de conversión: </h6>
                                        </div>
                                        <div class="col text-right">
                                            <h6>560</h6>
                                        </div>
                                    </div>
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-auto">
                                            <h6 class="mb-0">Consumo en C$: </h6>
                                        </div>
                                        <div class="col text-right">
                                            <h6 id="EtotalCordobas"></h6>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-block">
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-2 m-0 p-0">
                                            <h6 class="text-left m-b-10"><span class="text-muted m-r-5">Inicial:</span>
                                            </h6>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="text-muted text-right m-b-10"><span id="Einicial" class="text-muted m-r-5"></span> Kwh
                                            </h6>
                                        </div>
                                        <div class="col-2 m-0 p-0">
                                            <h6 class="text-right m-b-10"><span class="text-muted m-r-5">Final:</span>
                                            </h6>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="text-muted text-right m-b-10"><span id="Efinal" class="text-muted m-r-5"></span> Kwh
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-social">
                                <div class="card-header text-center p-2 bg-blue">
                                    <h5 class="m-0 text-white">Gas Butano</h5>
                                </div>
                                <div class="card-block mx-2 my-1">
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-auto">
                                            <h6 class="mb-0">Consumo: </h6>
                                        </div>
                                        <div class="col text-right">
                                            <h6 id="GtotalConsumo"> Glns</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="navMP" data-toggle="tab" href="#nav-mp" role="tab" aria-controls="nav-mp" aria-selected="true">Materia Prima</a>
                            <a class="nav-item nav-link" id="navMOD" data-toggle="tab" href="#nav-mod" role="tab" aria-controls="nav-mod" aria-selected="false">Mano de obra directa</a>
                            <a class="nav-item nav-link" id="navQuimicos" data-toggle="tab" href="#nav-quimicos" role="tab" aria-controls="nav-quimicos" aria-selected="false">Quimicos</a>
                            <a class="nav-item nav-link" id="navCIF" data-toggle="tab" href="#nav-cif" role="tab" aria-controls="nav-cif" aria-selected="false">Costos indirectos de Fabricación</a>
                            <a class="nav-item nav-link" id="navCostos" data-toggle="tab" href="#nav-costos" role="tab" aria-controls="nav-costos" aria-selected="false">Costos Por OP</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-mp" role="tabpanel" aria-labelledby="navMP">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tblMP" class="table table-bordered mt-3">
                                        <thead class="bg-blue text-light">
                                            <tr>
                                                <th>Maquina</th>
                                                <th>Fibra</th>
                                                <th>cantidad</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-mod" role="tabpanel" aria-labelledby="navMOD">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tblMOD" class="table table-bordered mt-3">
                                        <thead class="bg-blue text-light">
                                            <tr>
                                                <th>Descripción de la actividad</th>
                                                <th>Dia</th>
                                                <th>Noche</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-quimicos" role="tabpanel" aria-labelledby="navQuimicos">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tblQuimicos" class="table table-bordered mt-3">
                                        <thead class="bg-blue text-light">
                                            <tr>
                                                <th>Maquina</th>
                                                <th>Quimico</th>
                                                <th>cantidad</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-cif" role="tabpanel" aria-labelledby="navCIF">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tblCIF" class="table table-bordered mt-3">
                                        <thead class="bg-blue text-light">
                                            <tr>
                                                <th>Descripción de la actividad</th>
                                                <th>Horas</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-costos" role="tabpanel" aria-labelledby="navCostos">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tblCostos" class="table table-bordered mt-3">
                                        <thead class="bg-blue text-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Descripción</th>
                                                <th>Unidad de Medida</th>
                                                <th>Cantidad</th>
                                                <th>Costo Unitario (C$)</th>
                                                <th>Costo Total (C$)</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="font-weight-bold">TOTAL</td>
                                                <td id="CT_Unitario" class="font-weight-bold"></td>
                                                <td id="costoTotal" class="font-weight-bold"> </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endsection