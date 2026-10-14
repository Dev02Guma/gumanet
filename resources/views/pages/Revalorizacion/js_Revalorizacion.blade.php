<script>
// Último filtro calculado: el Excel se genera con este mismo filtro
var _filtro = null;

$(document).ready(function() {
    fullScreen();

    // Fecha de corte: un solo día, por defecto hoy, sin fechas futuras
    $('#dt_fecha_corte').daterangepicker({
        singleDatePicker: true,
        autoApply: true,
        maxDate: moment(),
        startDate: moment(),
        opens: 'left',
        locale: {
            format: "D MMM. YYYY",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            weekLabel: "S",
            daysOfWeek: ["Dom.", "Lun.", "Mar.", "Mie.", "Jue.", "Vie.", "Sab."],
            monthNames: [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ],
            firstDay: 0
        }
    });

    GetBodegas();

    $('#btn_filtrar').on('click', function() {
        var bodega  = $('#sel_bodegas').val();
        var bodegas = bodega ? [bodega] : [];
        var fecha   = $('#dt_fecha_corte').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var agrupar = $('#sel_agrupar').val();

        if (bodegas.length === 0) {
            Swal.fire('Bodega', 'Seleccione una bodega.', 'warning');
            return;
        }

        CallFilter(bodegas, fecha, agrupar);
    });

    $('#txt_busqueda_revalorizacion').on('keyup', function() {
        if ($('#tbl_revalorizacion').hasClass('dataTable')) {
            $('#tbl_revalorizacion').DataTable().search(this.value).draw();
        }
    });

    $('#btn_exportar').on('click', function() {
        if (!_filtro) return;
        ExportarExcel(_filtro);
    });
});

    // Descarga el Excel con fetch para mantener el loading de SweetAlert2 hasta que el archivo esté listo
    async function ExportarExcel(filtro) {
        Swal.fire({
            title: 'Generando Excel...',
            html: 'Calculando la revalorización al corte, por favor espere.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function() { Swal.showLoading(); }
        });

        try {
            const url = 'ExportRevalorizacion?fecha=' + encodeURIComponent(filtro.fecha) +
                        '&bodegas=' + encodeURIComponent(filtro.bodegas.join(',')) +
                        '&agrupar=' + encodeURIComponent(filtro.agrupar);
            const response = await fetch(url, { credentials: 'same-origin' });

            const tipo = response.headers.get('Content-Type') || '';
            if (!response.ok || tipo.indexOf('spreadsheetml') === -1) {
                throw new Error('HTTP ' + response.status);
            }

            const blob = await response.blob();
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'Revalorizacion_Inventario_' + (filtro.agrupar === 'LOTE' ? 'Lotes_' : 'Articulos_') + filtro.fecha + '.xlsx';
            document.body.appendChild(link);
            link.click();
            link.remove();
            setTimeout(function() { URL.revokeObjectURL(link.href); }, 1000);

            Swal.fire({ icon: 'success', title: 'Excel generado', timer: 1800, showConfirmButton: false });

        } catch (error) {
            console.error('Error al exportar:', error);
            Swal.fire('Error', 'No se pudo generar el Excel. Intente nuevamente.', 'error');
        }
    }

    async function GetBodegas() {
        try {
            const response = await fetch('getBodegasRevalorizacion');
            const result = await response.json();

            var opciones = result.BODEGAS.map(function(b) {
                return `<option value="${b.BODEGA}" data-subtext="${b.BODEGA}">${b.NOMBRE}</option>`;
            }).join('');

            $('#sel_bodegas').html(opciones);
            // Por ahora se trabaja primero con la bodega 004
            if ($('#sel_bodegas option[value="004"]').length) {
                $('#sel_bodegas').val('004');
            }
            $('#sel_bodegas').selectpicker('refresh');

        } catch (error) {
            console.error('Error al obtener las bodegas:', error);
            Swal.fire('Error', 'No se pudieron cargar las bodegas.', 'error');
        }
    }

    function CallFilter(bodegas, fecha, agrupar) {
        $('#tl_fecha_corte').html(`<b>${moment(fecha).format('D MMM. YYYY')}</b>`);

        var nombres = $('#sel_bodegas option:selected').map(function() { return $(this).text(); }).get();
        $('#tl_bodegas').html(nombres.join(', ') + ' &middot; por ' + (agrupar === 'LOTE' ? 'lote' : 'artículo'));

        GetData(bodegas, fecha, agrupar);
    }

    function eneableButton(EnableButton, textButton = '<i class="fas fa-filter"></i> Filtrar') {
        $('#btn_filtrar').prop('disabled', EnableButton);
        $('#btn_filtrar').html('<i class="fas fa-spinner fa-spin" style="display:' + (EnableButton ? 'inline-block' : 'none') + '"></i> ' + textButton);
    }

    async function GetData(bodegas, fecha, agrupar) {
        try {
            eneableButton(true, 'Calc...');
            $('#btn_exportar, #txt_busqueda_revalorizacion').prop('disabled', true);
            _filtro = null;

            const response = await fetch('getRevalorizacion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    bodegas: bodegas,
                    fecha: fecha,
                    agrupar: agrupar
                })
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const result = await response.json();

            TablaRevalorizacion('#tbl_revalorizacion', result.REVALORIZACION, agrupar);

            _filtro = { bodegas: bodegas, fecha: fecha, agrupar: agrupar };
            $('#btn_exportar').prop('disabled', result.REVALORIZACION.length === 0);
            $('#txt_busqueda_revalorizacion').prop('disabled', false);

        } catch (error) {
            console.error('Error al obtener los datos:', error);
            Swal.fire('Error', 'No se pudo calcular la revalorización.', 'error');
        } finally {
            eneableButton(false, '<i class="fas fa-filter"></i> Filtrar');
        }
    }

    function TablaRevalorizacion(selector, data, agrupar) {
        $('#revalorizacion-vacio').hide();
        $('#revalorizacion-tabla').show();

        var porLote = agrupar === 'LOTE';

        // Las columnas cambian entre Lote y Lotes: destruir y quitar el thead generado antes de reconstruir
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().clear().destroy();
            $(selector + ' thead').remove();
        }

        var numero = function(formato) {
            return function(d) { return numeral(d).format(formato); };
        };

        $(selector).DataTable({
            data: data,
            destroy: true,
            paging: true,
            pageLength: 25,
            info: true,
            searching: true,
            ordering: true,
            scrollX: true,
            order: [[0, 'asc'], [2, 'asc']],
            columns: [
                { title: 'ARTÍCULO',       data: 'ARTICULO',         className: 'text-left fw-bold' },
                { title: 'DESCRIPCIÓN',    data: 'DESCRIPCION',      className: 'text-left' },
                porLote
                    ? { title: 'LOTE',  data: 'LOTE',  className: 'text-center' }
                    : { title: 'LOTES', data: 'LOTES', className: 'text-center', render: numero('0,0') },
                { title: 'CANTIDAD',       data: 'CANTIDAD',         className: 'text-right', render: numero('0,0.00') },
                { title: 'UNIDAD',         data: 'UNIDAD_ALMACEN',   className: 'text-center' },
                { title: 'COSTO UNIT.',    data: 'COSTO_UNITARIO',   className: 'text-right', render: numero('0,0.0000') },
                { title: 'COSTO TOTAL',    data: 'COSTO_TOTAL',      className: 'text-right', render: numero('0,0.00') },
                { title: 'COMPRA TOTAL',   data: 'COMPRA_TOTAL',     className: 'text-right', render: numero('0,0.00') },
                { title: 'NUM. CRM',       data: 'NUM_CRM',          className: 'text-center' },
                { title: 'ORDEN COMPRA',   data: 'ORDEN_COMPRA',     className: 'text-center' },
                { title: 'PROVEEDOR',      data: 'PROVEEDOR',        className: 'text-center' },
                { title: 'NOMBRE PROVEEDOR', data: 'NOMBRE_PROVEEDOR', className: 'text-left' },
            ],
            // Totales de lo que queda visible con la búsqueda
            footerCallback: function() {
                var api = this.api();
                var suma = function(col) {
                    return api.column(col, { search: 'applied' }).data().reduce(function(a, b) { return a + (parseFloat(b) || 0); }, 0);
                };
                $(api.column(6).footer()).html(numeral(suma(6)).format('0,0.00'));
                $(api.column(7).footer()).html(numeral(suma(7)).format('0,0.00'));
            },
            language: {
                emptyTable: 'No hay inventario para las bodegas y fecha de corte seleccionadas',
                zeroRecords: 'No se encontraron artículos',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ ' + (porLote ? 'lotes' : 'artículos'),
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_)',
                paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' }
            }
        });

        $(selector + '_length').hide();
        $(selector + '_filter').hide();
    }
</script>
