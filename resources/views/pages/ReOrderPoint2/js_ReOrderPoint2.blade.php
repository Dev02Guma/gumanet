<script>
// Respuesta de getReOrderPoint2 y filtros vigentes
var _meta    = null;
var _headers = {};
var _data    = [];
var _filtros = { texto: [], cat: [], ver: [], ori: [], reorden: false };
var _chart   = null;

var TABLA      = '#tbl_decision';
var CATEGORIAS = ['A', 'ACA', 'B', 'BCA', 'C', 'CCA', 'L'];
var VEREDICTOS = {
    'REORDENAR':          'reordenar',
    'CONSOLIDAR':         'consolidar',
    'ALERTA VENCIMIENTO': 'alerta',
    'REVISAR PRECIO':     'precio',
    'SOBRE-STOCK':        'sobrestock',
    'DEFENDER':           'defender',
    'CANDIDATO A SUBIR':  'subir',
    'OK':                 'ok',
    'NO REORDENAR':       'noreordenar',
    'SOLO LICITACIÓN':    'licitacion'
};
// Totales del reorden (fila 3 del Excel); los montos suman solo valores positivos, igual que el Excel
var TOTALES = {
    REORDEN_UNDS:    { prefijo: '',    soloPositivos: false },
    REORDEN_LOC:     { prefijo: 'C$ ', soloPositivos: true },
    LOC_X_DESPACHO:  { prefijo: 'C$ ', soloPositivos: true },
    REORDEN_USD:     { prefijo: '$',   soloPositivos: true },
    CONSOLIDAR_UNDS: { prefijo: '',    soloPositivos: false }
};
// Relleno de la celda VEREDICTO, mismos colores que $colorVeredicto del Excel (OK sin color)
var COLOR_VEREDICTO = {
    'REORDENAR':          '#DDEBF7',
    'CONSOLIDAR':         '#D6E4F0',
    'DEFENDER':           '#FFF2CC',
    'ALERTA VENCIMIENTO': '#FFC7CE',
    'SOBRE-STOCK':        '#FFD9B3',
    'CANDIDATO A SUBIR':  '#C6EFCE',
    'NO REORDENAR':       '#EDEDED',
    'REVISAR PRECIO':     '#FF9999',
    'SOLO LICITACIÓN':    '#BDD7EE'
};

// Formato por campo: lo usan la tabla y el modal de detalle
var FORMATO = {
    entero:  function(v) { return numeral(v).format('0,0'); },
    decimal: function(v) { return numeral(v).format('0,0.0'); },
    moneda:  function(v) { return numeral(v).format('0,0.00'); },
    pct:     function(v) { return numeral(v).format('0.0%'); },
    fecha:   function(v) { return moment(v).format('DD/MM/YYYY'); },
    texto:   function(v) { return Esc(v); }
};
var TIPO_CAMPO = {
    INVENTARIO: 'entero', PEDIDO: 'entero', TRANSITO: 'entero', TOTAL_DISP: 'entero',
    MESES_ACTIVOS: 'entero', PROM_SISTEMA: 'entero', PROM_DEPURADO: 'decimal',
    UNDS_12M_PRIVADO: 'entero', UNDS_12M_PUBLICO: 'entero', UNDS_EMPAQUE: 'entero',
    TAB_DISP_GRUPO: 'entero', DEMANDA_GRUPO: 'entero', CANT_LOTE: 'entero', N_CLIENTES: 'entero',
    DISPARADOR: 'entero', REORDEN_UNDS: 'entero', N_DESPACHOS: 'entero', UNDS_X_DESPACHO: 'entero',
    CONSOLIDAR_UNDS: 'entero',
    VENTA_12M_PRIV: 'entero', CONTRIB_PRIV: 'entero', CONTRIB_PUBLICA: 'entero', VENTA_PRIV_ANT: 'entero',
    REORDEN_LOC: 'entero', LOC_X_DESPACHO: 'entero', REORDEN_USD: 'entero',
    MESES_DISP_PROPIO: 'decimal', MESES_DISP_GRUPO: 'decimal', MESES_A_VCTO: 'decimal', MESES_P_CONSUMIR: 'decimal',
    ULT_COSTO_LOC: 'moneda', ULT_COSTO_USD: 'moneda',
    PORC_PUBLICO: 'pct', PORC_CTB_PRIV: 'pct', PORC_BONIF: 'pct', PORC_CRUZ_AZUL: 'pct',
    PORC_CTB_REPOSICION: 'pct', CREC_CRUZ_AZUL: 'pct',
    LOTE_VENCE: 'fecha', ULT_COMPRA: 'fecha'
};
var ESPECIAL = {
    // Razón (últimos 3 meses / promedio depurado), igual que el Excel: < 1 cae, > 1 sube
    MOMENTUM:         function(v) {
        var css = v > 1 ? 'text-success' : (v < 1 ? 'text-danger' : '');
        return `<span class="${css}">${numeral(v).format('0.00')}</span>`;
    },
    CREC_PRIV:        function(v) { return Variacion(v); },
    DELTA_MARGEN:     function(v) {
        var css = v > 0 ? 'text-success' : (v < 0 ? 'text-danger' : '');
        return `<span class="${css}">${numeral(v * 100).format('+0.0')} pp</span>`;
    },
    RIESGO_LOTE:      function(v) {
        return v === 'SÍ'
            ? '<span class="text-warning" title="El lote vence antes de consumirse"><i class="fas fa-exclamation-triangle"></i> SÍ</span>'
            : Esc(v);
    }
};

$(document).ready(function() {
    fullScreen();

    $('#btn_exportar').on('click', ExportarExcel);

    // Filtro propio de la tabla de decisión (no afecta otras DataTables de la página)
    $.fn.dataTable.ext.search.push(function(settings, searchData, index) {
        if (settings.nTable.id !== 'tbl_decision') return true;
        return CumpleFiltros(settings.aoData[index]._aData, _filtros);
    });

    var espera = null;
    $('#txt_buscar').on('keyup', function() {
        clearTimeout(espera);
        espera = setTimeout(AplicarFiltros, 200);
    });
    $('#sel_categoria, #sel_veredicto, #sel_origen, #chk_solo_reorden').on('change', AplicarFiltros);
    $('#btn_limpiar').on('click', LimpiarFiltros);

    // Filas por pagina desde el filtro (reemplaza el "Mostrar" del pie de la tabla)
    $('#sel_mostrar').on('change', function() {
        if ($.fn.DataTable.isDataTable(TABLA)) $(TABLA).DataTable().page.len(parseInt(this.value, 10)).draw();
    });

    $(TABLA).on('click', 'tbody tr', function() {
        var fila = $(TABLA).DataTable().row(this).data();
        if (fila) AbrirDetalle(fila);
    });

    $('#mdl_detalle').on('shown.bs.modal', function() {
        if (_chart) _chart.reflow();
    });

    GetData();
});

    async function GetData() {
        Swal.fire({
            title: 'Cargando...',
            html: 'Obteniendo el Reorder Point.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function() { Swal.showLoading(); }
        });

        try {
            const response = await fetch('getReOrderPoint2', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const result = await response.json();

            _meta    = result.META || {};
            _headers = result.HEADERS || {};
            _data    = (result.DATA || []).map(function(r) {
                r._buscar = Normalizar([r.ARTICULO, r.DESCRIPCION, r.FABRICANTE, r.NOTAS].join(' '));
                return r;
            });

            RenderEncabezado(_meta);
            LlenarSelects(_data);
            TablaDecision(_data);
            _filtros = LeerFiltros();
            RenderRegistros();
            $('#btn_exportar').prop('disabled', _data.length === 0);
            Swal.close();

        } catch (error) {
            console.error('Error al obtener los datos:', error);
            Swal.fire('Error', 'No se pudo cargar el Reorder Point.', 'error');
        }
    }

    function RenderEncabezado(meta) {
        $('#tl_periodo').text(Fecha(meta.PERIODO_INI) + ' – ' + Fecha(meta.PERIODO_FIN));
        $('#tl_fecha_corte').text(Fecha(meta.FECHA_CORTE));
        $('#tl_tipo_cambio').text(meta.TIPO_CAMBIO ? 'C$ ' + numeral(meta.TIPO_CAMBIO).format('0,0.0000') : '-');
        // Aviso cuando faltan las tablas maestras del reorden en PRODUCCION
        $('#badge_demo').toggle(!!meta.AVISO).text('Sin maestros').attr('title', meta.AVISO || '');
    }

    // Llena los selects sólo si la vista no trae opciones propias
    function LlenarSelects(data) {
        var opciones = function(valores) {
            return valores.map(function(v) { return `<option value="${Esc(v)}">${Esc(v)}</option>`; }).join('');
        };
        var origenes = data.map(function(r) { return r.ORIGEN; })
            .filter(function(v, i, a) { return v && a.indexOf(v) === i; })
            .sort();

        [['#sel_categoria', CATEGORIAS], ['#sel_veredicto', Object.keys(VEREDICTOS)], ['#sel_origen', origenes]]
            .forEach(function(par) {
                if ($(par[0] + ' option').length === 0) $(par[0]).html(opciones(par[1]));
                $(par[0]).selectpicker('refresh');
            });
    }

    // ---------- Filtros ----------

    function ValoresSelect(selector) {
        return [].concat($(selector).val() || []).filter(function(v) { return v !== ''; });
    }

    function LeerFiltros() {
        return {
            texto:   Normalizar($('#txt_buscar').val()).split(/\s+/).filter(Boolean),
            cat:     ValoresSelect('#sel_categoria'),
            ver:     ValoresSelect('#sel_veredicto'),
            ori:     ValoresSelect('#sel_origen'),
            reorden: $('#chk_solo_reorden').is(':checked')
        };
    }

    function CumpleFiltros(r, f) {
        for (var i = 0; i < f.texto.length; i++) {
            if (r._buscar.indexOf(f.texto[i]) === -1) return false;
        }
        if (f.cat.length && f.cat.indexOf(r.CAT_ACTUAL) === -1) return false;
        if (f.ver.length && f.ver.indexOf(r.VEREDICTO) === -1) return false;
        if (f.ori.length && f.ori.indexOf(r.ORIGEN) === -1) return false;
        if (f.reorden && !(r.REORDEN_UNDS > 0)) return false;
        return true;
    }

    function AplicarFiltros() {
        _filtros = LeerFiltros();
        if ($.fn.DataTable.isDataTable(TABLA)) $(TABLA).DataTable().draw();
        RenderRegistros();
    }

    function LimpiarFiltros() {
        $('#txt_buscar').val('');
        $('#sel_categoria, #sel_veredicto, #sel_origen').val([]).selectpicker('refresh');
        $('#chk_solo_reorden').prop('checked', false);
        AplicarFiltros();
    }

    function RenderRegistros() {
        var n = _data.filter(function(r) { return CumpleFiltros(r, _filtros); }).length;
        $('#tl_registros').text(numeral(n).format('0,0') + ' de ' + numeral(_data.length).format('0,0') + ' productos');
    }

    // Excel con la estructura de la hoja Decision_x_Producto, solo con los productos que dejan los filtros
    async function ExportarExcel() {
        var articulos = _data.filter(function(r) { return CumpleFiltros(r, _filtros); })
                             .map(function(r) { return r.ARTICULO; });
        if (articulos.length === 0) {
            Swal.fire('Excel', 'No hay productos con los filtros seleccionados.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Generando Excel...',
            html: 'Preparando el Reorder Point, por favor espere.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function() { Swal.showLoading(); }
        });

        try {
            const response = await fetch('ExportReOrderPoint2', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                // Todos los productos visibles = sin lista (el servidor exporta todo)
                body: JSON.stringify({ articulos: articulos.length === _data.length ? [] : articulos })
            });

            const tipo = response.headers.get('Content-Type') || '';
            if (!response.ok || tipo.indexOf('spreadsheetml') === -1) {
                throw new Error('HTTP ' + response.status);
            }

            const blob = await response.blob();
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'Reorder_Point_' + ((_meta && _meta.FECHA_CORTE) || moment().format('YYYY-MM-DD')) + '.xlsx';
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(link.href);
            Swal.close();

        } catch (error) {
            console.error('Error al exportar:', error);
            Swal.fire('Error', 'No se pudo generar el Excel.', 'error');
        }
    }

    // ---------- Tabla ----------

    function TablaDecision(data) {
        $('#rop2-vacio').hide();
        $('#rop2-tabla').show();

        if ($.fn.DataTable.isDataTable(TABLA)) {
            $(TABLA).DataTable().clear().destroy();
        }
        $(TABLA + ' thead').remove();

        // Mismas columnas y en el mismo orden que la hoja Reorder_Point del Excel:
        // HEADERS llega del servidor en ese orden (incluye los meses M01..Mnn del corte)
        var columnas = [
            { data: null, orderable: false, className: 'text-center', width: '20px',
              render: function() { return '<i class="fas fa-search-plus text-primary" title="Ver detalle"></i>'; } }
        ];
        var encabezado = '<th></th>';
        // Fila de totales sobre el encabezado, como la fila 3 del Excel
        var totales = '<th class="rop2-total"></th>';

        Object.keys(_headers).forEach(function(campo) {
            encabezado += `<th title="${Esc(_headers[campo])}">${Esc(_headers[campo])}</th>`;
            totales += TOTALES[campo]
                ? `<th class="rop2-total text-right" data-total="${campo}"></th>`
                : `<th class="rop2-total">${campo === 'DESCRIPCION' ? 'TOTALES' : ''}</th>`;
            columnas.push(ColumnaTabla(campo));
        });

        $(TABLA).prepend(`<thead><tr>${totales}</tr><tr>${encabezado}</tr></thead>`);

        $(TABLA).DataTable({
            data: data,
            destroy: true,
            paging: true,
            pageLength: parseInt($('#sel_mostrar').val(), 10) || 25,
            info: true,
            searching: true,
            ordering: true,
            // Sin scrollX: DataTables separaría el encabezado en otra tabla y se desalinea con los valores;
            // el scroll horizontal lo da #rop2-tabla
            autoWidth: false,
            dom: 'rt<"d-flex flex-wrap justify-content-between align-items-center mt-2"ip>',
            order: [[1, 'asc']],
            columns: columnas,
            // Los totales siguen los filtros: suman solo los productos que quedan en la tabla
            drawCallback: function() {
                var filas = this.api().rows({ search: 'applied' }).data().toArray();
                Object.keys(TOTALES).forEach(function(campo) {
                    var t = TOTALES[campo];
                    var suma = filas.reduce(function(a, r) {
                        var v = parseFloat(r[campo]) || 0;
                        return a + ((t.soloPositivos && v <= 0) ? 0 : v);
                    }, 0);
                    $(TABLA + ' thead th[data-total="' + campo + '"]').text(t.prefijo + numeral(suma).format('0,0'));
                });
            },
            createdRow: function(tr, r) {
                $(tr).css('cursor', 'pointer');
                if (/URGENTE/i.test(r.NOTAS || '')) $(tr).addClass('rop2-row-urgente');
            },
            language: {
                emptyTable: 'No hay productos para mostrar',
                zeroRecords: 'Ningún producto cumple los filtros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ productos',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_)',
                lengthMenu: 'Mostrar _MENU_',
                paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' }
            }
        });
    }

    // Definición de DataTables para un campo de HEADERS
    function ColumnaTabla(campo) {
        var truncar = { DESCRIPCION: 260, FABRICANTE: 160, PAR_HERMANA: 120, NOTAS: 320 };

        if (campo === 'RANK') {
            return { data: 'RANK', type: 'num', className: 'text-right',
                render: function(d, type) {
                    if (type === 'display') return d === null ? '-' : d;
                    return d === null ? 999999 : d;
                } };
        }
        if (campo === 'ARTICULO') return { data: 'ARTICULO', className: 'text-left font-weight-bold' };
        if (campo === 'CAT_ACTUAL') {
            return { data: 'CAT_ACTUAL', type: 'num', className: 'text-center',
                render: function(d, type, r) {
                    if (type === 'sort') return CATEGORIAS.indexOf(d);
                    return type === 'display' ? BadgeCategoria(d) + MarcaCambio(r) : d;
                } };
        }
        if (campo === 'VEREDICTO') {
            // Igual que el Excel: se pinta la celda, el texto queda en negrita sin badge
            return { data: 'VEREDICTO', className: 'text-left font-weight-bold rop2-td-veredicto',
                render: function(d, type) { return type === 'display' ? Esc(d || '-') : d; },
                createdCell: function(td, d) {
                    if (COLOR_VEREDICTO[d]) td.style.setProperty('background-color', COLOR_VEREDICTO[d], 'important');
                } };
        }
        if (truncar[campo]) {
            return { data: campo, className: 'text-left',
                render: function(d, type) {
                    return type === 'display'
                        ? `<span class="d-inline-block text-truncate align-middle" style="max-width:${truncar[campo]}px;" title="${Esc(d || '')}">${Esc(d || '')}</span>`
                        : (d || '');
                } };
        }

        // Numéricos: los de TIPO_CAMPO, los de formato especial y los meses (M01..Mnn)
        var numero = /^M\d{2}$/.test(campo)
            || (TIPO_CAMPO[campo] && TIPO_CAMPO[campo] !== 'fecha')
            || (ESPECIAL[campo] && campo !== 'RIESGO_LOTE');
        return { data: campo, type: numero ? 'num' : 'string', className: numero ? 'text-right' : 'text-center',
            render: function(d, type, r) { return type === 'display' ? Fmt(campo, d, r) : d; } };
    }

    // ---------- Detalle ----------

    function AbrirDetalle(r) {
        $('#mdl_titulo').text(r.ARTICULO + ' · ' + r.DESCRIPCION);
        $('#mdl_subtitulo').html([
            r.RANK !== null ? `<span class="badge badge-light">Rk# ${r.RANK}</span>` : '',
            BadgeCategoria(r.CAT_ACTUAL) + MarcaCambio(r),
            BadgeVeredicto(r.VEREDICTO),
            Esc(r.FABRICANTE || ''),
            Esc(r.PRESENTACION || ''),
            r.ORIGEN ? 'Origen: ' + Esc(r.ORIGEN) : '',
            r.PAR_HERMANA ? 'Hermana: ' + Esc(r.PAR_HERMANA) : ''
        ].filter(Boolean).join(' <span class="text-muted">·</span> '));

        GraficoDemanda(r);

        $('#mdl_cobertura').html(ListaCampos(r, ['INVENTARIO', 'PEDIDO', 'TRANSITO', 'TOTAL_DISP', 'MESES_DISP_PROPIO',
            'UNDS_EMPAQUE', 'TAB_DISP_GRUPO', 'DEMANDA_GRUPO', 'MESES_DISP_GRUPO', 'DISPARADOR']));
        $('#mdl_reorden').html(ListaCampos(r, ['REORDEN_UNDS', 'N_DESPACHOS', 'UNDS_X_DESPACHO', 'REORDEN_LOC',
            'LOC_X_DESPACHO', 'REORDEN_USD', 'CONSOLIDAR_UNDS']));
        $('#mdl_lote').html(ListaCampos(r, ['LOTE_VENCE', 'MESES_A_VCTO', 'CANT_LOTE', 'MESES_P_CONSUMIR', 'RIESGO_LOTE']));
        $('#mdl_contrib').html(ListaCampos(r, ['VENTA_12M_PRIV', 'CONTRIB_PRIV', 'PORC_CTB_PRIV', 'CONTRIB_PUBLICA',
            'VENTA_PRIV_ANT', 'CREC_PRIV', 'CREC_CRUZ_AZUL', 'PORC_BONIF', 'N_CLIENTES', 'PORC_CRUZ_AZUL',
            'ULT_COMPRA', 'ULT_COSTO_LOC', 'ULT_COSTO_USD', 'PORC_CTB_REPOSICION', 'DELTA_MARGEN']));

        var notas = (r.NOTAS || '').split(';').map(function(n) { return n.trim(); }).filter(Boolean);
        $('#mdl_notas').html(notas.length
            ? '<ul class="mb-0 pl-3">' + notas.map(function(n) {
                  return /URGENTE/i.test(n) ? `<li class="text-danger font-weight-bold">${Esc(n)}</li>` : `<li>${Esc(n)}</li>`;
              }).join('') + '</ul>'
            : '<span class="text-muted">Sin notas</span>');

        $('#mdl_detalle').modal('show');
    }

    function ListaCampos(r, campos) {
        var filas = campos.map(function(c) {
            return `<tr><td class="text-muted pl-0">${Esc(_headers[c] || c)}</td><td class="text-right pr-0">${Fmt(c, r[c], r)}</td></tr>`;
        }).join('');
        return `<table class="table table-sm mb-0"><tbody>${filas}</tbody></table>`;
    }

    // Unidades privadas por mes (el último mes es parcial) contra el promedio depurado
    function GraficoDemanda(r) {
        var meses = (_meta && _meta.MESES) || [];
        var ultimo = meses.length - 1;
        var valores = meses.map(function(m, i) {
            var campo = 'M' + (i + 1 < 10 ? '0' : '') + (i + 1);
            return { y: r[campo] || 0, color: i === ultimo ? 'rgba(53,114,176,.45)' : '#3572b0' };
        });

        if (_chart) { _chart.destroy(); _chart = null; }

        _chart = Highcharts.chart('mdl_chart', {
            chart: { style: { fontFamily: 'inherit' } },
            title: { text: null },
            credits: { enabled: false },
            exporting: { enabled: false },
            legend: { itemStyle: { fontWeight: 'normal' } },
            xAxis: { categories: meses },
            yAxis: { title: { text: 'Unidades' }, min: 0 },
            tooltip: {
                shared: true,
                formatter: function() {
                    return '<b>' + this.x + '</b><br>' + this.points.map(function(p) {
                        return p.series.name + ': <b>' + numeral(p.y).format('0,0') + '</b>';
                    }).join('<br>');
                }
            },
            series: [
                { type: 'column', name: 'Unds privadas', data: valores },
                { type: 'line', name: 'Prom. depurado', color: '#dc3545', dashStyle: 'ShortDash',
                  marker: { enabled: false }, data: meses.map(function() { return r.PROM_DEPURADO || 0; }) }
            ]
        });
    }

    // ---------- Helpers ----------

    function Esc(v) {
        if (v === null || v === undefined) return '';
        return String(v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function Normalizar(v) {
        return String(v || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    function Fecha(v) {
        return v ? moment(v).format('DD/MM/YYYY') : '-';
    }

    function Fmt(campo, v, r) {
        if (v === null || v === undefined || v === '') return '-';
        if (ESPECIAL[campo]) return ESPECIAL[campo](v, r || {});
        return FORMATO[TIPO_CAMPO[campo] || (/^M\d{2}$/.test(campo) ? 'entero' : 'texto')](v);
    }

    function Variacion(v) {
        var css = v > 0 ? 'text-success' : (v < 0 ? 'text-danger' : 'text-muted');
        return `<span class="${css}">${numeral(v).format('+0.0%')}</span>`;
    }

    function BadgeVeredicto(v) {
        if (!v) return '-';
        return `<span class="rop2-ver rop2-ver-${VEREDICTOS[v] || 'ok'}">${Esc(v)}</span>`;
    }

    function BadgeCategoria(c) {
        if (!c) return '-';
        var clase = c === 'L' ? 'l' : c.charAt(0).toLowerCase();
        return `<span class="rop2-cat rop2-cat-${clase}">${Esc(c)}</span>`;
    }

    // ↑/↓ cuando cambia la letra (A > B > C); ≠ para cambios a/desde L o sólo de sufijo CA
    function MarcaCambio(r) {
        if (r.CAT_CAMBIO !== '≠') return '';
        var antes = r.CAT_ANTERIOR || '', ahora = r.CAT_ACTUAL || '';
        var titulo = 'Antes: ' + Esc(antes || '-');
        var a = antes.charAt(0), b = ahora.charAt(0);
        if (a && b && a !== 'L' && b !== 'L' && a !== b) {
            return b < a
                ? `<small class="text-success ml-1" title="${titulo}">↑</small>`
                : `<small class="text-danger ml-1" title="${titulo}">↓</small>`;
        }
        return `<small class="text-muted ml-1" title="${titulo}">≠</small>`;
    }
</script>
