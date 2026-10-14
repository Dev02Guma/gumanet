@extends('layouts.main')
@section('title' , "REVALORIZACIÓN DE INVENTARIO")
    @section('name_user' , 'Administrador')
@section('metodosjs')
    @include('pages.Revalorizacion.js_Revalorizacion')
    @include('pages.OrdenCompra.css_Ordenes')
@endsection

@section('content')

    <style>
        /* Todos los controles del filtro con el mismo alto */
        .rv-filtros .form-control,
        .rv-filtros .input-group-text,
        .rv-filtros .bootstrap-select > .dropdown-toggle,
        .rv-filtros .btn {
            height: 38px !important;
            font-size: .875rem;
        }
        .rv-filtros .input-group-text,
        .rv-filtros .btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .rv-filtros .bootstrap-select > .dropdown-toggle {
            display: flex;
            align-items: center;
            background-color: #fff;
            border: 1px solid #ced4da;
        }
    </style>

    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-lg-3 mb-2 mb-lg-0">
                    <img src="{{ asset('img/unimark.png') }}" alt="Unimark" class="mb-1" style="height: 40px;">
                    <p class="text-muted mb-0 small">Fecha de corte: <span id="tl_fecha_corte" class="fw-semibold text-umk">-</span></p>
                </div>

                <!-- Filtros y acciones -->
                <div class="col-lg-9">
                    <div class="row g-2 align-items-end rv-filtros">
                        <div class="col-md-3">
                            <label for="txt_busqueda_revalorizacion" class="small text-muted text-uppercase fw-semibold mb-1">Buscar</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" placeholder="Artículo, lote, proveedor..." id="txt_busqueda_revalorizacion" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="sel_bodegas" class="small text-muted text-uppercase fw-semibold mb-1">Bodega</label>
                            <select id="sel_bodegas" class="form-control form-control-sm selectpicker"
                                    data-live-search="true" data-width="100%" title="Seleccione una bodega"
                                    data-none-results-text="Sin resultados {0}">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sel_agrupar" class="small text-muted text-uppercase fw-semibold mb-1">Agrupar por</label>
                            <select id="sel_agrupar" class="form-control form-control-sm">
                                <option value="ARTICULO" selected>ARTICULOS</option>
                                <option value="LOTE">LOTES</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="dt_fecha_corte" class="small text-muted text-uppercase fw-semibold mb-1">Fecha de corte</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-sm input-fecha" id="dt_fecha_corte" readonly />
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="button" class="btn btn-primary-umk btn-sm flex-fill" id="btn_filtrar">
                                <i class="fas fa-filter me-1"></i>Filtrar
                            </button>
                            <button type="button" class="btn btn-success btn-sm flex-fill" id="btn_exportar" title="Exportar a Excel" disabled>
                                <i class="fas fa-file-excel me-1"></i>Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Revalorización -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-innova text-white">
            <h6 class="mb-0 fw-bold">INVENTARIO</h6>
            <small class="text-white-50" id="tl_bodegas">Seleccione la bodega y la fecha de corte</small>
        </div>
        <div class="card-body p-0">
            <div id="revalorizacion-vacio">
            <div class="d-flex flex-column align-items-center justify-content-center text-muted py-5">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width:64px; height:64px;">
                    <i class="fas fa-warehouse text-secondary" style="font-size:26px;"></i>
                </div>
                <div class="fw-semibold text-dark mb-1">Sin datos para mostrar</div>
                <small>Seleccione la bodega y la fecha de corte y presione <b>Filtrar</b>.</small>
            </div>
            </div>
            <div id="revalorizacion-tabla" style="display:none">
                <table id="tbl_revalorizacion" class="table table-sm table-hover" style="width:100%">
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <th colspan="6" class="text-end">TOTAL:</th>
                            <th class="text-end" id="total_costo">0.00</th>
                            <th class="text-end" id="total_compra">0.00</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection
