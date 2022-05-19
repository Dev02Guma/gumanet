@extends('layouts.main')
@section('metodosjs')
@include('jsViews.js_detalleOrdenes')
@endsection
@section('content')
<div class="container-fluid">
    <!-- [ Main Content ] start -->
    <div class="row my-3">
        <div class="col-sm-12 mt-4">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i data-feather="search"></i></span>
                </div>
                <input type="text" id="InputDtShowSearchFilterArt" class="form-control" placeholder="Buscar en ordenes de produccion" aria-label="Username" aria-describedby="basic-addon1">
                <input type="text" id="InputDt_PC" class="form-control" placeholder="Buscar en ordenes de produccion" aria-label="orden" aria-describedby="basic-addon1" style="display: none;">

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
                                            <p class="text-muted m-0">Merma Yankee Dry (kg):</p>
                                            <div class="input-group">
                                                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="merma-yankee-dry">C$ 0.00</p>
                                                <p class="font-weight-bolder ml-2" style="font-size: 1.3rem!important" id="porcentaje_merma"> </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="" class="text-muted m-0">Tonelada al dia /
                                                <small for="" class="text-muted m-0 p-0 ">STD : 10 </small>
                                            </p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="ton_dia"> </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="residuos-pulper" class="text-muted m-0">Residuos del Pulper (kg):
                                            </p>
                                            <div class="input-group">
                                                <p class="font-weight-bolder" id="residuos-pulper" style="font-size: 1.3rem!important"> </p>
                                                <p class="font-weight-bolder ml-2" style="font-size: 1.3rem!important" id="porcentaje_rp"> </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="lav-tetrapack" class="text-muted m-0">Lavadora de Tetrapack (kg):
                                            </p>
                                            <div class="input-group">
                                                <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="lav-tetrapack"> </p>
                                                <p class="font-weight-bolder ml-2" style="font-size: 1.3rem!important" id="porcentaje_tpack"> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p for="" class="text-muted m-0">Horas Trabajadas:</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="hrsTrabajadas"> </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="factorFibral" class="text-muted m-0">Factor fibral /
                                                <small for="factorFibral" class="text-muted m-0">STD = 1.3 %</small>
                                            </p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="factor-fibral"> </p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="produccionNeta" class="text-muted m-0">PROD.REAL (kg):</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="produccionNeta"></p>
                                        </div>
                                        <div class="col-sm-3">
                                            <p for="produccionReal" class="text-muted m-0">PROD.TOTAL (kg) - (Real + Merma)</p>
                                            <p class="font-weight-bolder" style="font-size: 1.3rem!important" id="produccionReal"></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- [ Header orden produccion ] end -->
                    </div>
                    <div class="row mt-3">
                        <!-- Gastos de consumo-->

                        <!-- Consumo de agua -->
                        <div class="col-3">
                            <div class="card card-social" style="height: 170px">
                                <div class="card-header text-center bg-blue" style="height: 40px;">
                                    <h6 class="text-white m-0">Agua</h6>
                                </div>
                                <div class="card-block  border-bottom">
                                    <div class="row  mx-2 my-1  align-items-center justify-content-center">
                                        <div class="col-6 m-0 p-0">
                                            <p class="mb-0">Consumo: </p>
                                        </div>
                                        <div class="col-6 m-0 p-0">
                                            <h6 class="text-left  font-weight-bold" id="AtotalConsumo"></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center justify-content-center  mx-2 my-1  border-bottom">
                                    <div class="col-8 m-0 p-0">
                                        <p class="text-left m-0 p-0">--</p>
                                    </div>
                                    <div class="col-4 m-0 p-0">
                                        <p class="text-lef m-0 p-0">--</p>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-6 m-0 p-0">
                                            <p class="text-left m-0 p-0"><span class="text-muted m-r-5">Inicial:</span>
                                            </p>
                                        </div>
                                        <div class="col-6 m-0 p-0">
                                            <p class="text-left m-0 p-0"><span class="text-muted m-r-5">Final:</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-6 m-0 p-0">
                                            <h6 class="text-muted text-left m-0 p-0"><span class="text-muted m-r-5" id="Ainicial"></span></h6>
                                        </div>

                                        <div class="col-6 m-0 p-0">
                                            <h6 class="text-muted text-left  m-0 p-0"><span class="text-muted m-r-5" id="Afinal"></span> m<sup>3</sup></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consumo de electricidad -->
                        <div class="col-3">
                            <div class="card card-social" style="height: 250px">
                                <div class="card-header text-center p-2 bg-blue">
                                    <h6 class="m-0 text-white">Electricidad</h6>
                                </div>
                                <div class="row align-items-center justify-content-center mx-2 my-1 ">
                                    <div class="col-8 m-0 p-0">
                                        <p class="m-0 p-0">Consumo: </p>
                                    </div>
                                    <div class="col-4 m-0 p-0">
                                        <h6 class=" font-weight-bold text-left" id="EtotalConsumo"></h6>
                                    </div>
                                </div>
                                <div class="row align-items-center justify-content-center  mx-2 my-1  border-bottom">
                                    <div class="col-8 m-0 p-0">
                                        <p class="text-left m-0 p-0">Factor de conversión:</p>
                                    </div>
                                    <div class="col-4 m-0 p-0">
                                        <h6 class="text-lef">560</h6>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">
                                        <div class="col-8 m-0 p-0">
                                            <p class="text-left m-0 p-0"><span class="text-muted m-r-5">Inicial:</span>
                                            </p>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <p class="text-left m-0 p-0"><span class="text-muted m-r-5">Final:</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mx-2 my-1 align-items-center justify-content-center card-active">

                                        <div class="col-8 m-0 p-0">
                                            <h6 class="text-muted text-left m-0 p-0"><span id="Einicial" class="text-muted m-r-5"></span>
                                            </h6>
                                        </div>
                                        <div class="col-4 m-0 p-0">
                                            <h6 class="text-muted text-left m-0 p-0"><span id="Efinal" class="text-muted m-r-5"></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block border-top ">
                                    <div class="row align-items-center mt-1 mx-2 border-bottom">
                                        <div class="col-7 m-0 p-0 ">
                                            <p class="m-0 p-0 text-muted  font-weight-bold">Consumo total estimado: </p>
                                        </div>
                                        <div class="col-5 m-0 p-0 ">
                                            <p class="m-0 p-0 font-weight-bold text-center " id="E_ConsumoTTestimado"></p>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mx-2">
                                        <div class="col-7 m-0 p-0">
                                            <p class="m-0 p-0 text-muted  font-weight-bold">Consumo total estimado PH: </p>
                                        </div>
                                        <div class="col-5 m-0 p-0">
                                            <p class="m-0 p-0 font-weight-bold text-center" id="E_ConsumoPH"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consumo de electricidad Ton/KW-->
                        <div class="col-3">
                            <div class="card card-social" style="height: 170px">
                                <div class="card-header text-center p-2 bg-blue">
                                    <h6 class="m-0 text-white">Electricidad Kw/Ton</h6>
                                </div>
                                <div class="row mx-2 my-1 align-items-center justify-content-center">
                                    <div class="col-6 m-0 p-0">
                                        <h6 class="">Consumo: </h6>
                                    </div>
                                    <div class="col-6  m-0 p-0 text-right">
                                        <h6 class="font-weight-bold" id="E_ConsumoSTD" style="color: rgb(255, 0, 0);"></h6>
                                    </div>
                                </div>

                                <!--<div class="r                       ow align-items-center justify-content-center px-1">
                                    <div class="col-auto">
                                        <h6 class="mb-0">Consumo Real (80%) </h6>
                                    </div>
                                    <div class="col text-right">
                                        <h6 id="consumo_ps"></h6>
                                    </div>
                                </div>-->
                                <div class="row mx-2 my-1  align-items-center justify-content-center">
                                    <div class="col-6  m-0 p-0 ">
                                        <h6>STD kw/Ton: </h6>
                                    </div>
                                    <div class="col-6   m-0 p-0 text-right">
                                        <h6 id="E_STD">740 kw/ton</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consumo de Gas butano -->
                        <div class="col-3">
                            <div class="card card-social" style="height: 170px">
                                <div class="card-header text-center p-2 bg-blue">
                                    <h6 class="m-0 text-white">Gas Butano</h6>
                                </div>
                                <div class="card-block">
                                    <div class="row  mx-2 my-1 align-items-center justify-content-center">
                                        <div class="col-auto m-0 p-0">
                                            <h6 class="mb-0">Consumo Ton: </h6>
                                        </div>
                                        <div class="col text-right m-0 p-0">
                                            <h6 class="font-weight-bold" id="G_totalConsumoTon" style="color: rgb(255, 0, 0);"></h6>
                                        </div>
                                    </div>
                                    <div class="row  mx-2 my-1 align-items-center justify-content-center">
                                        <div class="col-auto m-0 p-0">
                                            <h6 class="">Consumo: </h6>
                                        </div>
                                        <div class="col text-right m-0 p-0">
                                            <h6 class="font-weight-bold" id="GtotalConsumo"></h6>
                                        </div>
                                    </div>
                                    <div class="row  mx-2 my-1 align-items-center justify-content-center">
                                        <div class="col-auto m-0 p-0">
                                            <h6 class="mb-0">STD: </h6>
                                        </div>
                                        <div class="col text-right m-0 p-0">
                                            <h6 id="G_STD"> 145 gln/ton</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Horas efectivas -->
                    </div>



                </div>
                <div class=" modal-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="navMP" data-toggle="tab" href="#nav-mp" role="tab" aria-controls="nav-mp" aria-selected="true">Materia Prima</a>
                            <a class="nav-item nav-link" id="navMOD" data-toggle="tab" href="#nav-mod" role="tab" aria-controls="nav-mod" aria-selected="false">Mano de obra directa</a>
                            <a class="nav-item nav-link" id="navQuimicos" data-toggle="tab" href="#nav-quimicos" role="tab" aria-controls="nav-quimicos" aria-selected="false">Quimicos</a>
                            <a class="nav-item nav-link" id="navCIF" data-toggle="tab" href="#nav-cif" role="tab" aria-controls="nav-cif" aria-selected="false">Costos indirectos de Fabricación</a>
                            <a class="nav-item nav-link" id="navCostos" data-toggle="tab" href="#nav-costos" role="tab" aria-controls="nav-costos" aria-selected="false">Costos Por OP</a>
                            <a class="nav-item nav-link" id="navHrsEfect" data-toggle="tab" href="#nav-HrsEfect" role="tab" aria-controls="nav-HrsEfect" aria-selected="false">Horas producidas</a>
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
                        <div class="tab-pane fade" id="nav-HrsEfect" role="tabpanel" aria-labelledby="navHrsEfect">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="tblHrsEfect" class="table table-bordered mt-3">
                                        <thead class="bg-blue text-light">
                                            <tr>
                                                <th>MAQUINA</th>
                                                <th>Día</th>
                                                <th>Noche</th>
                                                <th>Total/Horas</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td id="" class="font-weight-bold">TOTAL</td>
                                                <td id="hrasTotales" class="font-weight-bold"> </td>
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

    <div class="row mt-5">
        <div class="col-xl-12">
            <div class="card">
                <div class="container-fluid form-row m-0 p-0 pr-4">
                    <div class="col-md-10 col-6">
                        <p class="text-left mt-3 ml-4 pb-2 font-weight-bolder " style="font-size: 1.3rem!important">Ordenes de producción</p>
                    </div>
                    <div class="col-md-2 col-6 pr-0">
                        <select class="custom-select  mt-3 pb-2 " id="tipo_procceso">
                            <option value="1">Proceso Humedo</option>
                            <option value="2">Proceso de Conversión</option>

                        </select>
                    </div>
                </div>
                <div class="card-block table-border-style mx-4 mt-2">
                    <div class="table-responsive">
                        <div class="table-responsive mb-2 ">
                            <table class="table table-bordered table-hover" width="99.9%" id="dtDetalles">

                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-block table-border-style mx-4">
                    <div class="table-responsive">
                        <div class="table-responsive mb-2 ">
                            <table class="table table-bordered table-hover" width="99.9%" id="dtOrdenes_pc">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-xl" data-backdrop="static" data-keyboard="false" id="mdDetalleOrd_pc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header d-block">
                    <div class="row">
                        <!-- [ Header orden produccion ] start -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-center align-items-center pt-1 pb-0 bg-blue">
                                    <h5 class="modal-title text-center align-self-center p-1 bg-blue text-white" id="title_detail_pc"></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mt-3">
                                        <div class="col-sm-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h3 class="card-title" id="peso_pc"></h3>
                                                    <p class="card-text" id="">PESO % </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h3 class="card-title" id="jr_total_pc"></h3>
                                                    <p class="card-text" id="">JR TOTAL (KG) </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h3 class="card-title" id="hrs_trabajadas_pc"></h3>
                                                    <p class="card-text" id="">HORAS TRABAJADAS</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h3 class="card-title" id="total_bultos_pc"> </h3>
                                                    <p class="card-text" id="">TOTAL DE BULTOS (UNDS)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Header orden produccion ] end -->
                        </div>
                    </div>

                    <div class=" modal-body">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab-pc" role="tablist">
                                <a class="nav-item nav-link active" id="navProd-pc" data-toggle="tab" href="#nav-prod-pc" role="tab" aria-controls="nav-prod-pc" aria-selected="false">Productos</a>
                                <a class="nav-item nav-link " id="navMP_pc" data-toggle="tab" href="#nav-mp-pc" role="tab" aria-controls="nav-mp-pc" aria-selected="true">Materia Prima</a>
                                <a class="nav-item nav-link" id="navTiemposParos" data-toggle="tab" href="#nav-tiempos-paros" role="tab" aria-controls="nav-tiempos-paros" aria-selected="false">Tiempos Paros</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-prod-pc" role="tabpanel" aria-labelledby="navProd-pc">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="tblProductos_pc" class="table table-hover border-bottom-0">
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-mp-pc" role="tabpanel" aria-labelledby="navMP_pc">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="tblMateriaPrima_pc" class="table table-hover border-bottom-0">
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="nav-tiempos-paros" role="tabpanel" aria-labelledby="navTiemposParos">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="tblTiemposParos_pc" class="table table-hover border-bottom-0">
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