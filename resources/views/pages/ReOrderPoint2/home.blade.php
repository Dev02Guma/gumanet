@extends('layouts.main')
@section('title' , "DECISIÓN POR PRODUCTO · REORDEN v2.1")
    @section('name_user' , 'Administrador')
@section('metodosjs')
    @include('pages.ReOrderPoint2.js_ReOrderPoint2')
    @include('pages.OrdenCompra.css_Ordenes')
@endsection

@section('content')

    <style>
        /* Todos los controles del filtro con el mismo alto */
        .rop2-filtros .form-control,
        .rop2-filtros .input-group-text,
        .rop2-filtros .bootstrap-select > .dropdown-toggle,
        .rop2-filtros .btn {
            height: 38px !important;
            font-size: .875rem;
        }
        .rop2-filtros .input-group-text,
        .rop2-filtros .btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .rop2-filtros .bootstrap-select > .dropdown-toggle {
            display: flex;
            align-items: center;
            padding: .375rem .75rem;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            box-shadow: none;
        }
        /* Texto del selectpicker centrado verticalmente, mismo tamaño y color que el buscador */
        .rop2-filtros .bootstrap-select > .dropdown-toggle .filter-option {
            position: static;
            display: flex;
            align-items: center;
            height: 100%;
            padding: 0;
            float: none;
            flex: 1 1 auto;
            min-width: 0;
        }
        .rop2-filtros .bootstrap-select > .dropdown-toggle .filter-option-inner,
        .rop2-filtros .bootstrap-select > .dropdown-toggle .filter-option-inner-inner {
            width: 100%;
            font-size: .875rem;
            line-height: 1.5;
            color: #495057;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .rop2-filtros .bootstrap-select > .dropdown-toggle.bs-placeholder .filter-option-inner-inner {
            color: #6c757d;
        }
        .rop2-filtros .bootstrap-select > .dropdown-toggle:after { margin-left: .5rem; }
        .rop2-filtros .form-control::placeholder { color: #6c757d; }
        .rop2-filtros .rop2-switch {
            height: 38px;
            display: flex;
            align-items: center;
            font-size: .875rem;
            white-space: nowrap;
        }
        .rop2-filtros .rop2-switch .custom-control-label { line-height: 1.5rem; }
        .rop2-filtros .rop2-acciones .btn { min-width: 105px; }
        .rop2-label {
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #6c757d;
            margin-bottom: .25rem;
        }

        /* KPIs */

        /* Cajas de resumen (veredictos / categorías) */

        /* Tabla densa */
        #rop2-tabla { overflow-x: auto; }
        #tbl_decision { font-size: .8rem; }
        #tbl_decision th {
            font-size: .72rem;
            white-space: nowrap;
            vertical-align: middle;
        }
        #tbl_decision td { white-space: nowrap; vertical-align: middle; }
        #tbl_decision tbody tr { cursor: pointer; }
        .rop2-row-urgente > td { background-color: rgba(220, 53, 69, .07) !important; }
        .rop2-row-urgente > td:first-child { box-shadow: inset 3px 0 0 #dc3545; }

        /* Grupo de columnas */
        #grp_columnas .btn { font-size: .75rem; padding: .2rem .55rem; }

        /* Badges de veredicto */
        .rop2-ver {
            display: inline-block;
            padding: .2em .55em;
            border-radius: .25rem;
            font-size: .7rem;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            color: #fff;
            background-color: #6c757d;
        }
        .rop2-ver-reordenar   { background-color: #dc3545; }
        .rop2-ver-consolidar  { background-color: #fd7e14; }
        .rop2-ver-alerta      { background-color: #ffc107; color: #343a40; }
        .rop2-ver-precio      { background-color: #6f42c1; }
        .rop2-ver-sobrestock  { background-color: #607d8b; }
        .rop2-ver-defender    { background-color: #d63384; }
        .rop2-ver-subir       { background-color: #20c997; }
        .rop2-ver-ok          { background-color: #28a745; }
        .rop2-ver-noreordenar { background-color: #adb5bd; color: #343a40; }
        .rop2-ver-licitacion  { background-color: #9ad0f5; color: #0b4f7c; }

        /* Badges de categoría */
        .rop2-cat {
            display: inline-block;
            min-width: 2.4em;
            padding: .15em .45em;
            border-radius: .25rem;
            font-size: .7rem;
            font-weight: 700;
            text-align: center;
            border: 1px solid transparent;
        }
        .rop2-cat-a { background-color: #fff3a0; color: #6b5800; border-color: #f0d94a; }
        .rop2-cat-b { background-color: #c8ecc9; color: #1e6b2a; border-color: #8fd394; }
        .rop2-cat-c { background-color: #f1f3f5; color: #495057; border-color: #dee2e6; }
        .rop2-cat-l { background-color: #cfe5ff; color: #0b4f9c; border-color: #93c3f5; }

        /* Barra de cobertura con marca del disparador */
        .rop2-cov {
            position: relative;
            width: 90px;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: visible;
        }
        .rop2-cov-fill {
            height: 100%;
            border-radius: 4px;
            background-color: #28a745;
            max-width: 100%;
        }
        .rop2-cov-trigger {
            position: absolute;
            top: -3px;
            width: 2px;
            height: 14px;
            background-color: #343a40;
        }

        /* Modal de detalle */
        #mdl_detalle .rop2-mdl-card .card-header {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: .4rem .75rem;
            background-color: #f8f9fa;
        }
        #mdl_detalle .rop2-mdl-card .card-body { font-size: .8rem; padding: .6rem .75rem; }
    </style>

    <!-- Header -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center mb-3">
                <img src="{{ asset('img/unimark.png') }}" alt="Unimark" class="mr-3" style="height: 40px;">
                <div class="flex-grow-1">
                    <h5 class="mb-0 font-weight-bold text-umk">
                        Decisión por producto · Reorden v2.1
                        <span class="badge badge-warning align-middle ml-1" id="badge_demo" style="font-size:.65rem;">Datos de prueba</span>
                    </h5>
                    <p class="text-muted mb-0 small">
                        Periodo: <span id="tl_periodo" class="font-weight-bold text-umk">-</span>
                        <span class="mx-1">·</span>
                        Fecha de corte: <span id="tl_fecha_corte" class="font-weight-bold text-umk">-</span>
                        <span class="mx-1">·</span>
                        T/C: <span id="tl_tipo_cambio" class="font-weight-bold text-umk">-</span>
                    </p>
                </div>
            </div>

            <!-- Filtros y acciones -->
            <div class="form-row align-items-end rop2-filtros">
                <div class="col-lg col-md-6 mb-2">
                    <label for="txt_buscar" class="rop2-label">Buscar</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm" placeholder="Artículo, descripción, fabricante..." id="txt_buscar" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-1 col-md-6 mb-2">
                    <label for="sel_categoria" class="rop2-label">Categoría</label>
                    <select id="sel_categoria" class="form-control form-control-sm selectpicker" multiple
                            data-width="100%" title="Todas" data-selected-text-format="count > 3">
                        <option value="A">A</option>
                        <option value="ACA">ACA</option>
                        <option value="B">B</option>
                        <option value="BCA">BCA</option>
                        <option value="C">C</option>
                        <option value="CCA">CCA</option>
                        <option value="L">L</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-2">
                    <label for="sel_veredicto" class="rop2-label">Veredicto</label>
                    <select id="sel_veredicto" class="form-control form-control-sm selectpicker" multiple
                            data-width="100%" title="Todos" data-selected-text-format="count > 1" data-actions-box="true"
                            data-select-all-text="Todos" data-deselect-all-text="Ninguno">
                        <option value="REORDENAR">REORDENAR</option>
                        <option value="CONSOLIDAR">CONSOLIDAR</option>
                        <option value="ALERTA VENCIMIENTO">ALERTA VENCIMIENTO</option>
                        <option value="REVISAR PRECIO">REVISAR PRECIO</option>
                        <option value="SOBRE-STOCK">SOBRE-STOCK</option>
                        <option value="DEFENDER">DEFENDER</option>
                        <option value="CANDIDATO A SUBIR">CANDIDATO A SUBIR</option>
                        <option value="OK">OK</option>
                        <option value="NO REORDENAR">NO REORDENAR</option>
                        <option value="SOLO LICITACIÓN">SOLO LICITACIÓN</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-2">
                    <label for="sel_origen" class="rop2-label">Origen proveedor</label>
                    <select id="sel_origen" class="form-control form-control-sm selectpicker" multiple
                            data-width="100%" title="Todos">
                        <option value="India">India</option>
                        <option value="China">China</option>
                        <option value="Otro/verificar">Otro/verificar</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-4 mb-2">
                    <label for="sel_mostrar" class="rop2-label">Mostrar</label>
                    <select id="sel_mostrar" class="form-control form-control-sm selectpicker" data-width="100%">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Todos</option>
                    </select>
                </div>
                <div class="col-lg-auto col-md-4 mb-2">
                    <div class="custom-control custom-switch rop2-switch">
                        <input type="checkbox" class="custom-control-input" id="chk_solo_reorden">
                        <label class="custom-control-label" for="chk_solo_reorden">Solo con reorden</label>
                    </div>
                </div>
                <div class="col-lg-auto col-md-8 mb-2 d-flex rop2-acciones">
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill mr-2" id="btn_limpiar">
                        <i class="fas fa-eraser mr-1"></i>Limpiar
                    </button>
                    <button type="button" class="btn btn-success btn-sm flex-fill" id="btn_exportar" title="Exportar a Excel" disabled>
                        <i class="fas fa-file-excel mr-1"></i>Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de decisión -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-innova text-white">
            <div>
                <h6 class="mb-0 font-weight-bold">DECISIÓN POR PRODUCTO</h6>
                <small class="text-white-50" id="tl_registros">-</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="rop2-vacio">
                <div class="d-flex flex-column align-items-center justify-content-center text-muted py-5">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width:64px; height:64px;">
                        <i class="fas fa-boxes text-secondary" style="font-size:26px;"></i>
                    </div>
                    <div class="font-weight-bold text-dark mb-1">Cargando datos...</div>
                </div>
            </div>
            <div id="rop2-tabla" style="display:none">
                <table id="tbl_decision" class="table table-sm table-hover" style="width:100%"></table>
            </div>
        </div>
    </div>

    <!-- Modal de detalle por producto -->
    <div class="modal fade" id="mdl_detalle" tabindex="-1" role="dialog" aria-labelledby="mdl_titulo" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title text-umk font-weight-bold" id="mdl_titulo">-</h5>
                        <small class="text-muted" id="mdl_subtitulo">-</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row">
                        <div class="col-lg-8 mb-3">
                            <div class="card border-0 shadow-sm h-100 rop2-mdl-card">
                                <div class="card-header">Demanda mensual</div>
                                <div class="card-body">
                                    <!-- Highcharts y Chart.js están cargados en layouts.main -->
                                    <div id="mdl_chart" style="height:260px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="card border-0 shadow-sm h-100 rop2-mdl-card">
                                <div class="card-header">Cobertura</div>
                                <div class="card-body" id="mdl_cobertura"></div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="card border-0 shadow-sm h-100 rop2-mdl-card">
                                <div class="card-header">Reorden</div>
                                <div class="card-body" id="mdl_reorden"></div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="card border-0 shadow-sm h-100 rop2-mdl-card">
                                <div class="card-header">Lote y vencimiento</div>
                                <div class="card-body" id="mdl_lote"></div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="card border-0 shadow-sm h-100 rop2-mdl-card">
                                <div class="card-header">Contribución y márgenes</div>
                                <div class="card-body" id="mdl_contrib"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rop2-mdl-card">
                                <div class="card-header">Notas y alertas</div>
                                <div class="card-body" id="mdl_notas"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
