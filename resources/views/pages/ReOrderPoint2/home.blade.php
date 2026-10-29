@extends('layouts.main')
@section('title' , "REORDER POINT · REORDEN v2.1")
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
            line-height: 1.2;
            min-width: 64px;
            max-width: 130px;
            white-space: normal;
            vertical-align: bottom;
            background-color: #f4f6fa;
            border-bottom: 2px solid #c9d1e0;
        }
        /* Líneas verticales entre columnas para seguir cada valor con su encabezado */
        #tbl_decision th, #tbl_decision td { border-right: 1px solid #e3e7ef; padding-left: .5rem; padding-right: .5rem; }
        #tbl_decision td { white-space: nowrap; vertical-align: middle; }
        #tbl_decision tbody tr:nth-child(even) > td { background-color: #fafbfd; }
        #tbl_decision tbody tr:hover > td { background-color: #eef3fb; }
        #tbl_decision td.rop2-td-veredicto { color: #000; }
        /* Fila de totales sobre el encabezado */
        #tbl_decision thead th.rop2-total {
            background-color: #e2e5ea;
            border-bottom: 1px solid #c9d1e0;
            font-size: .75rem;
            font-weight: 700;
            white-space: nowrap;
            vertical-align: middle;
        }
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

        /* Modal de detalle */
        #mdl_detalle .rop2-mdl-card .card-header {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: .4rem .75rem;
            background-color: #f8f9fa;
        }
        #mdl_detalle .rop2-mdl-card .card-body { font-size: .8rem; padding: .6rem .75rem; }

        /* Ayuda */
        .rop2-ayuda { font-size: 1.5rem; line-height: 1; }
        .rop2-ayuda:hover, .rop2-ayuda:focus { opacity: .75; text-decoration: none; }
        #mdl_ayuda .modal-body { font-size: .85rem; }
        #mdl_ayuda h6 {
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
            margin: 1.25rem 0 .5rem;
            padding-bottom: .3rem;
            border-bottom: 1px solid #e3e7ef;
        }
        #mdl_ayuda h6:first-child { margin-top: 0; }
        #mdl_ayuda .table { font-size: .8rem; margin-bottom: .5rem; }
        #mdl_ayuda .table th { background-color: #f4f6fa; white-space: nowrap; }
        #mdl_ayuda ul, #mdl_ayuda ol { padding-left: 1.2rem; margin-bottom: .5rem; }
        #mdl_ayuda li { margin-bottom: .25rem; }
    </style>

    <!-- Header -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-start mb-3">
                <div class="flex-grow-1">
                    <h5 class="mb-0 font-weight-bold text-umk">
                        Reorder Point · Reorden v2.1
                        <span class="badge badge-warning align-middle ml-1" id="badge_demo" style="font-size:.65rem; display:none;">Sin maestros</span>
                    </h5>
                    <p class="text-muted mb-0 small">
                        Periodo: <span id="tl_periodo" class="font-weight-bold text-umk">-</span>
                        <span class="mx-1">·</span>
                        Fecha de corte: <span id="tl_fecha_corte" class="font-weight-bold text-umk">-</span>
                        <span class="mx-1">·</span>
                        T/C: <span id="tl_tipo_cambio" class="font-weight-bold text-umk">-</span>
                    </p>
                </div>
                <button type="button" class="btn btn-link text-umk p-0 ml-2 rop2-ayuda" data-toggle="modal" data-target="#mdl_ayuda"
                        title="¿Cómo se calcula este reporte?">
                    <i class="fas fa-question-circle"></i>
                </button>
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
                <h6 class="mb-0 font-weight-bold">REORDER POINT</h6>
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

    <!-- Modal de ayuda: de dónde salen los datos, cómo se calcula y por qué puede variar -->
    <div class="modal fade" id="mdl_ayuda" tabindex="-1" role="dialog" aria-labelledby="mdl_ayuda_titulo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-umk font-weight-bold" id="mdl_ayuda_titulo">
                        <i class="fas fa-question-circle mr-1"></i>Cómo funciona el Reorder Point
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="alert alert-warning small mb-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Este reporte depende de Contribución por canales (/canales).</strong>
                        No calcula ventas por su cuenta: toma lo último que se calculó en /canales.
                        Si /canales está desactualizado o se calculó con un período incorrecto, este reporte también lo estará.
                    </div>

                    <h6>1. De dónde sale cada dato</h6>
                    <table class="table table-sm table-bordered">
                        <thead><tr><th>Dato</th><th>Fuente</th></tr></thead>
                        <tbody>
                            <tr><td>Período y fecha de corte</td><td>Primera y última fecha de venta calculada en /canales</td></tr>
                            <tr><td>Unidades, venta y costo por canal; venta por mes; N° clientes; venta año anterior</td><td>/canales</td></tr>
                            <tr><td>Inventario, pedido, tránsito, lote que vence, última compra y último costo</td><td>/canales, consultados <strong>en vivo</strong> al abrir el reporte</td></tr>
                            <tr><td>CAT. REPORTE ANT.</td><td>Categoría que calcula /canales</td></tr>
                            <tr><td>CAT. (análisis anterior)</td><td>Foto de categorías del corte anterior guardada por este reporte</td></tr>
                            <tr><td>Presentaciones hermanas y origen del fabricante</td><td>Tablas maestras del reorden en PRODUCCION</td></tr>
                            <tr><td>Tipo de cambio</td><td>Tasa oficial de Softland a la fecha de corte</td></tr>
                        </tbody>
                    </table>
                    <p class="mb-0"><strong>Canales privados:</strong> Farmacias, Cadenas, Mayoristas, Instituciones privadas y Cruz Azul.
                        <strong>Públicos:</strong> Instituciones públicas y Licitaciones. La demanda y el reorden se calculan solo con la venta privada.</p>

                    <h6>2. Cómo recalcular /canales correctamente</h6>
                    <ul>
                        <li>El período debe ser de <strong>12 meses exactos</strong>. Con más meses se inflan unidades, contribución y crecimiento, y cambian el ranking y las categorías.</li>
                        <li>El botón <em>Calcular</em> de /canales <strong>no incluye el día de la fecha fin</strong>. Para incluir hasta el 26, la fecha fin debe ser el 27.</li>
                        <li>Mientras /canales se recalcula, este reporte puede mostrar datos incompletos. Ábrelo cuando el cálculo termine.</li>
                        <li>El envío automático del día 1 recalcula /canales con los 12 meses completos anteriores y no envía el correo si el cálculo no llegó al último día del mes anterior.</li>
                    </ul>

                    <h6>3. Por qué los resultados pueden cambiar entre una consulta y otra</h6>
                    <table class="table table-sm table-bordered">
                        <thead><tr><th>Variación</th><th>Qué pasa</th></tr></thead>
                        <tbody>
                            <tr><td>Período distinto en /canales</td><td>Cambian unidades 12M, contribución, crecimiento, ranking, categorías y veredictos.</td></tr>
                            <tr><td>Inventario en vivo</td><td>El inventario, pedido y tránsito se leen al momento: lo vendido o recibido desde el último cálculo mueve la cobertura, el reorden y el veredicto aunque /canales no cambie.</td></tr>
                            <tr><td>Día de corte incompleto</td><td>Si /canales se calcula a mitad del día, faltan las facturas que se emitan después.</td></tr>
                            <tr><td>Facturas anuladas o notas de crédito</td><td>Al recalcular desaparecen ventas que antes se contaban y el inventario regresa.</td></tr>
                            <tr><td>Mes del corte parcial</td><td>El último mes se completa con días del mes ÷ día del corte (ej. ×31/26). Un día más o menos cambia el promedio depurado y el momentum.</td></tr>
                            <tr><td>Ventas grandes de un solo día</td><td>Una sola factura grande puede mover el promedio, el momentum y el ranking de un producto.</td></tr>
                            <tr><td>Productos con contribución parecida</td><td>Diferencias pequeñas intercambian posiciones del ranking y pueden cruzar el corte del 80 % o 90 % (A/B/C).</td></tr>
                            <tr><td>Cobertura cerca de un umbral</td><td>Con pocos días de venta un producto puede cruzar el disparador o los 36 meses y cambiar de veredicto.</td></tr>
                            <tr><td>Artículos que aparecen o desaparecen</td><td>Un artículo entra al reporte solo si tuvo venta dentro del período de /canales.</td></tr>
                            <tr><td>Maestros del reorden</td><td>Agregar o quitar hermanas u orígenes cambia la cobertura consolidada y el disparador.</td></tr>
                            <tr><td>Foto de categorías</td><td>La columna CAT. compara contra el análisis anterior. Si no hay un corte anterior guardado, sale vacía.</td></tr>
                            <tr><td>Ajustes "CAMBIO DE COSTO"</td><td>/canales repite algunos artículos con una fila de ajuste de costo; el reporte la ignora.</td></tr>
                        </tbody>
                    </table>

                    <h6>4. Cómo se calcula</h6>
                    <ul>
                        <li><strong>PROM. DEPURADO:</strong> promedio mensual de venta privada. El último mes se completa a mes entero y se divide entre los meses con venta. Con 5 meses activos o menos (venta intermitente) se usa unidades 12M ÷ 12.</li>
                        <li><strong>MOMENTUM:</strong> promedio de los últimos 3 meses ÷ promedio depurado. Menor que 1 = la venta cae; mayor que 1 = sube.</li>
                        <li><strong>MESES DISP. GRUPO:</strong> (inventario + pedido + tránsito) ÷ demanda mensual, sumando las presentaciones hermanas en unidades mínimas (tabletas, ampollas).</li>
                        <li><strong>Categorías:</strong>
                            <strong>L</strong> si más del 50 % de las unidades es público y la contribución privada es menor de C$ 100,000.
                            <strong>A</strong> los que acumulan el 80 % de la contribución privada;
                            <strong>B</strong> hasta el 90 % o con contribución mayor a C$ 360,000;
                            <strong>C</strong> el resto. El sufijo <strong>CA</strong> indica venta a Cruz Azul. Las hermanas toman la mejor categoría del grupo.</li>
                        <li><strong>Disparador:</strong> A con origen China 6 meses; A India u otro origen 7 meses; B 4 meses. C y L no se reordenan.</li>
                        <li><strong>Reorden:</strong> si la cobertura del grupo baja del disparador, se compra hasta cubrir 18 meses (A) o 12 meses (B). Órdenes mayores a C$ 1 millón o a 8 meses de demanda se dividen en 2 a 4 despachos.</li>
                        <li><strong>CONSOLIDAR:</strong> el grupo está a 2 meses o menos de su disparador y el mismo fabricante ya tiene otro producto por reordenar.</li>
                    </ul>

                    <h6>5. Veredictos (en orden de prioridad)</h6>
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr><td style="background:#BDD7EE"><strong>SOLO LICITACIÓN</strong></td><td>Categoría L: comprar solo contra adjudicación.</td></tr>
                            <tr><td style="background:#FFC7CE"><strong>ALERTA VENCIMIENTO</strong></td><td>El lote vence antes de poder consumirse.</td></tr>
                            <tr><td style="background:#DDEBF7"><strong>REORDENAR</strong></td><td>La cobertura del grupo está por debajo del disparador.</td></tr>
                            <tr><td style="background:#D6E4F0"><strong>CONSOLIDAR</strong></td><td>Cerca del disparador: sumarlo a la próxima orden del fabricante.</td></tr>
                            <tr><td style="background:#FF9999"><strong>REVISAR PRECIO</strong></td><td>La contribución privada es negativa.</td></tr>
                            <tr><td style="background:#FFD9B3"><strong>SOBRE-STOCK</strong></td><td>Más de 36 meses de cobertura.</td></tr>
                            <tr><td style="background:#FFF2CC"><strong>DEFENDER</strong></td><td>A o B con venta privada cayendo más del 20 % contra el año anterior.</td></tr>
                            <tr><td style="background:#C6EFCE"><strong>CANDIDATO A SUBIR</strong></td><td>C creciendo más del 20 % con contribución mayor a C$ 170,000.</td></tr>
                            <tr><td style="background:#EDEDED"><strong>NO REORDENAR</strong></td><td>Categoría C.</td></tr>
                            <tr><td><strong>OK</strong></td><td>Ninguna de las anteriores.</td></tr>
                        </tbody>
                    </table>
                    <p class="mb-0 text-muted small">Las filas marcadas como <strong class="text-danger">URGENTE</strong> tienen una cobertura menor al disparador menos 1 mes (por debajo del tiempo de entrega).</p>

                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                </div>
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
