<script type="text/javascript">
    fullScreen();
    inicializaControlFecha();
    var JsonCanal = new Array();

    var selectedButton  = localStorage.getItem('buttonselected');
    var buttonCadena    = localStorage.getItem('buttonCadena');
    var buttonMayorista = localStorage.getItem('buttonMayorista');
    var buttonPrivada   = localStorage.getItem('buttonPrivada');
    var buttonCruzAzul  = localStorage.getItem('buttonCruzAzul');
    var buttonPublica   = localStorage.getItem('buttonPublica');
    var buttonLicitacion= localStorage.getItem('buttonLicitacion');
    
    
    var colors_ = ['#407EC9', '#D19000', '#00A376', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];
    grafica_articulos = {
        chart: {
            type: 'spline',
            renderTo: 'grafMeses',
        },      

        title: {
            text: ''
        },
        // subtitle: {
        //     text: 'C$ 0.00',
        //     align: 'right',
        //     x: -10
        // },
        exporting: {enabled: false},
        xAxis: [{type: 'category' }],
        legend: {enabled: false},
        yAxis:{
            title: {
                text: ''
            },
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        if (this.y > 1000) {
                            return Highcharts.numberFormat(this.y / 1000, 1) + " K";
                        } else {
                            return this.y
                        }
                    }
                }
            }
        }, 
        tooltip: {
            pointFormat: '<span style="color:black">0.0<b>C$ {point.y}</b></span>'
        },
        series: [{
            data: [],            
        }]
    };
$(document).ready(function () {
    
    // Buscar con una pausa de 300 ms: redibujar una tabla de ~250 columnas en cada tecla la hacia lenta
    var timerBuscar = null;
    $('#id_txt_buscar').on('keyup', function() {
        var texto = this.value;
        clearTimeout(timerBuscar);
        timerBuscar = setTimeout(function() {
            var vTable = $('#table_contribucion').DataTable();
            if (vTable.search() !== texto) vTable.search(texto).draw();
        }, 300);
    });

    $( "#InputCanales").change(function() {
        var table = $('#table_contribucion').DataTable();
        table.page.len(this.value).draw();
    });

    // Canales de las columnas de año anterior y mensuales (mismo orden que $canalesExtra en Home.blade.php)
    const CANALES_EXTRA = ['FARMACIAS', 'CADENAS', 'MAYORISTAS', 'INSTITUCIONES_PRIVADAS', 'CRUZ_AZUL', 'INSTITUCIONES_PUBLICAS', 'LICITACIONES'];

    let Table = new DataTable('#table_contribucion',{
        "destroy": true,
        "info": true,
        "ajax":{
            "url": "canalData",
            'dataSrc': function(json) {
                var periodo = json.Periodo;
                
                var fechaIni = moment(periodo.primera_fecha).format('DD/MM/YYYY');
                var fechaEnd = moment(periodo.ultima_fecha).format('DD/MM/YYYY');
                $('#tl_periodo').html(fechaIni + " hasta el "+ fechaEnd);
                if(periodo.primera_fecha === null){ 
                    $("#f1").val( moment(periodo.fechaIni).format('YYYY-MM-DD'));
                    $("#f2").val( moment(periodo.fechaEnd).format('YYYY-MM-DD'));
                }else{
                    $("#f1").val( moment(periodo.primera_fecha).format('YYYY-MM-DD'));
                    $("#f2").val( moment(periodo.ultima_fecha).format('YYYY-MM-DD'));
                }

                JsonCanal = json.Registros;

                // Rango del año anterior en el tooltip de ANT. UNDS / ANT. VTA
                $('.th-ant').attr('title', 'Año anterior: '
                    + moment(periodo.anterior_ini).format('DD/MM/YYYY') + ' al ' + moment(periodo.anterior_fin).format('DD/MM/YYYY'));

                // Encabezados del año anterior con el año comparado: "F. UNDS 25", "F. VTA 25", ...
                var anioAnt = moment(periodo.anterior_fin).format('YY');
                $('.th-ant').each(function() {
                    // Dentro del span de DataTables, para no perder el titulo que usa el export a Excel
                    var $titulo = $(this).find('.dt-column-title');
                    ($titulo.length ? $titulo : $(this)).text($(this).data('prefijo') + ' ' + anioAnt);
                });

                // Nombres de los meses (Ene a Dic del año actual) para los encabezados de venta mensual
                if (Array.isArray(periodo.meses)) {
                    $('.th-mes').each(function() {
                        var mes = periodo.meses[$(this).data('mes')];
                        // Escribir dentro del span de DataTables: si se reemplaza la celda completa se
                        // pierde el titulo de la columna y el export a Excel falla (header undefined)
                        var $titulo = $(this).find('.dt-column-title');
                        if (mes) ($titulo.length ? $titulo : $(this)).text(($(this).data('prefijo') || 'UNDS') + ' ' + mes);
                    });
                }

                return json.Registros;
            }
        },
        "language": {
            "zeroRecords": "No hay coincidencias",
            "loadingRecords": "Cargando datos...",
            "paginate": {
                "first": "Primera",
                "last": "Última ",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "lengthMenu": "MOSTRAR _MENU_",
            "emptyTable": "REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS DE FECHA",
            "search": "BUSCAR"
        },
        "lengthMenu": [[5,-1], [5,"Todo"]],
        layout: {
            topStart: null,
            bottom: 'paging',
            bottomStart: null,
            bottomEnd: null,            
            topStart: {
                buttons: [ 
                
                    { 
                        text: 'Columnas Visibles',
                        extend: 'collection',
                        className: 'btn-outline-success ',
                        buttons: [
                            {
                                extend: 'colvisGroup',
                                text: 'TODAS',
                                show: ':hidden',
                                action: function ( e, dt, node, config ) {

                                    dt.columns(':hidden').visible(true);

                                    const keysToRemove = [
                                        'buttonselected',
                                        'buttonCadena',
                                        'buttonMayorista',
                                        'buttonPrivada',
                                        'buttonCruzAzul',
                                        'buttonPublica',
                                        'buttonLicitacion'
                                    ];

                                    keysToRemove.forEach(key => localStorage.removeItem(key));


                                    // Recorrer y cambiar el estilo de todos los botones
                                    $('.dt-button').each(function() {
                                        $(this).css({
                                            'color': '#000000', 
                                            'background-color': 'transparent' 
                                        }).removeClass('dt-button-active');;
                                    });

                                    calcularTotales(dt);
                                
                                }
                            },
                            { 
                                
                                text: '1 : FARMACIA',
                                className: 'btn-farmacia',
                                action: function ( e, dt, node, config ) {
                                    for (let i = 4; i <= 9; i++) {
                                        dt.column(i).visible(!dt.column(i).visible());
                                    }
                                    //this.active(!this.active());                                    
                                    this.active(verificarLocalStorage('buttonselected', 'farmacia'));
                                    if (this.active()) {
                                        $(node).css({
                                            'color': '#dc3545',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    }
                                    
                                }
                            },
                            { 
                                text: '2 : CAD. FARMACIA',
                                className: 'btn-cadena',
                                action: function ( e, dt, node, config ) {
                                    for (let i = 10; i <= 15; i++) {
                                        dt.column(i).visible(!dt.column(i).visible());
                                    }
                                    //this.active(!this.active());
                                    this.active(verificarLocalStorage('buttonCadena', 'cadena'));
                                    if (this.active()) {
                                        $(node).css({
                                            'color': '#dc3545',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    }
                                    
                                }
                            },
                            { 
                                text: '3 : MAYORISTAS',
                                className: 'btn-mayorista',
                                action: function ( e, dt, node, config ) {
                                    for (let i = 16; i <= 21; i++) {
                                        dt.column(i).visible(!dt.column(i).visible());
                                    }
                                    //this.active(!this.active());
                                    this.active(verificarLocalStorage('buttonMayorista', 'mayorista'));
                                    if (this.active()) {
                                        $(node).css({
                                            'color': '#dc3545',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    }
                                    
                                }
                            },
                            { 
                                text: '4 : INSTI. PRIVADAS',
                                className: 'btn-privada',
                                action: function ( e, dt, node, config ) {
                                    for (let i = 22; i <= 27; i++) {
                                        dt.column(i).visible(!dt.column(i).visible());
                                    }
                                    //this.active(!this.active());
                                    this.active(verificarLocalStorage('buttonPrivada', 'privada'));
                                    if (this.active()) {
                                        $(node).css({
                                            'color': '#dc3545',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    }
                                    
                                }
                            },
                            { 
                                text: '5 : CRUZ AZUL',
                                className: 'btn-cruzAzul',
                                action: function ( e, dt, node, config ) {
                                    for (let i = 28; i <= 33; i++) {
                                        dt.column(i).visible(!dt.column(i).visible());
                                    }
                                    //this.active(!this.active());
                                    this.active(verificarLocalStorage('buttonCruzAzul', 'cruzAzul'));
                                    if (this.active()) {
                                        $(node).css({
                                            'color': '#dc3545',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    }
                                   
                                }
                            },
                            { 
                                text: '6 : INSTI. PUBLICAS',
                                className: 'btn-publica',
                                action: function ( e, dt, node, config ) {
                                    for (let i = 34; i <= 39; i++) {
                                        dt.column(i).visible(!dt.column(i).visible());
                                    }
                                    //this.active(!this.active());
                                    this.active(verificarLocalStorage('buttonPublica', 'publica'));
                                    if (this.active()) {
                                        $(node).css({
                                            'color': '#dc3545',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    }
                                    
                                }
                            },
                            { 
                                text: '7 : MINSA LICITACIONES',
                                className: 'btn-licitacion',
                                action: function ( e, dt, node, config ) {
                                    for (let i = 40; i <= 45; i++) {
                                        dt.column(i).visible(!dt.column(i).visible());
                                    }
                                    //this.active(!this.active());
                                    this.active(verificarLocalStorage('buttonLicitacion', 'licitacion'));
                                    if (this.active()) {
                                        $(node).css({
                                            'color': '#dc3545',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        calcularTotales(dt);
                                    }
                                    
                                }
                            },
                        ]
                    },
                
                ]
                
            },
            topEnd: {
                buttons: [ {
                    className: 'btn-outline-success ',
                    text: 'Exportar a excel',
                    extend: 'excelHtml5',
                    title:  'Contribucion por canal: ' + moment().format('YYYY-MM-DD HH:mm'),
                    exportOptions: {
                        columns: ':visible'
                    },
                    // Aviso mientras se arma el archivo. El setTimeout deja que el aviso termine de aparecer
                    // antes del trabajo pesado (congela la pagina); "done" lo llama Buttons al terminar.
                    action: function (e, dt, node, config, done) {
                        var boton = this;
                        $('#cc_exportando').addClass('activo');
                        setTimeout(function () {
                            try {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(boton, e, dt, node, config, function () {
                                    $('#cc_exportando').removeClass('activo');
                                    done();
                                });
                            } catch (error) {
                                console.error('Error al exportar:', error);
                                $('#cc_exportando').removeClass('activo');
                                done();
                            }
                        }, 250);
                    }
                }]
            }
        },    
        stateSave: true,
        fixedColumns: {
            start: 8
        },
        paging: true,
        scrollCollapse: true,
        scrollY: '1200px',
        scrollX: true,
        'columns': [
            {"data": "ARTICULO"},
            {"data": "DESCRIPCION"},
            {"data": "FABRICANTE"},
            {"data": "CATEGORIA"},
            {"data": "INVENTARIO", render: $.fn.dataTable.render.number(',', '.', 2, '')},
            {"data": "PEDIDO", render: $.fn.dataTable.render.number(',', '.', 2, '')},
            {"data": "TRANSITO", render: $.fn.dataTable.render.number(',', '.', 2, '')},
            {"data": "PROM_NORMAL", render: $.fn.dataTable.render.number(',', '.', 2, '')},
            {"data": "FARMACIA_CANTIDAD", "render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'FARMACIAS\', \'' + row.DESCRIPCION + '\', 0)">' + data + '</a>';
            }},
            {"data": "FARMACIA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'CADENAS\', \'' + row.DESCRIPCION + '\', 0)">' + data + '</a>';
            }},
            {"data": "CADENA_FARMACIA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'MAYORISTAS\', \'' + row.DESCRIPCION + '\', 0)">' + data + '</a>';
            }},
            {"data": "MAYORISTA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'INSTITUCIONES_PRIVADAS\', \'' + row.DESCRIPCION + '\', 0)">' + data + '</a>';
            }},
            {"data": "INSTITUCION_PRIVADA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'CRUZ_AZUL\', \'' + row.DESCRIPCION + '\', 0)">' + data + '</a>';
            }},
            {"data": "CRUZ_AZUL_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'INSTITUCIONES_PUBLICAS\', \'' + row.DESCRIPCION + '\', 0)">' + data + '</a>';
            }},
            {"data": "INSTITUCION_PUBLICA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "LICITACION_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'LICITACIONES\', \'' + row.DESCRIPCION + '\', 0)">' + data + '</a>';
            }},
            {"data": "LICITACION_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "LICITACION_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "LICITACION_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "LICITACION_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "LICITACION_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "TOTAL_VENTAS_PACK","render": function(data, type, row, meta) {                
                var ctable = $('#table_contribucion').DataTable();
                var cantidad = 0;
                var viArray = [];

                if (ctable.column(7).visible()) {
                    cantidad += parseFloat(row.FARMACIA_CANTIDAD.replace(/,/g, ''));
                }else {viArray.push('FARMACIAS');}
                
                if (ctable.column(13).visible()) {
                    cantidad += parseFloat(row.CADENA_FARMACIA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('CADENAS');}

                if (ctable.column(19).visible()) {
                    cantidad += parseFloat(row.MAYORISTA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('MAYORISTAS');}

                if (ctable.column(25).visible()) {
                    cantidad += parseFloat(row.INSTITUCION_PRIVADA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('INSTITUCIONES_PRIVADAS');}

                if (ctable.column(31).visible()) {
                    cantidad += parseFloat(row.CRUZ_AZUL_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('CRUZ_AZUL');}

                if (ctable.column(37).visible()) {
                    cantidad += parseFloat(row.INSTITUCION_PUBLICA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('INSTITUCIONES_PUBLICAS');}

                if (ctable.column(43).visible()) {
                    cantidad += parseFloat(row.LICITACION_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('LICITACIONES');}

                if(viArray == ''){
                    viArray.push('Todos');
                }                

                return '<a href="#" onclick=\'getDetalleCanal("' + row.ARTICULODESC + '", ' + JSON.stringify(viArray) + ', "' + row.DESCRIPCION + '", "' + 1 + '")\'>' + numeral(cantidad).format('0,0') + '</a>';
            }},
            {"data": "TOTAL_PRECIO_PROM","render": function(data, type, row, meta) {
                var ctable = $('#table_contribucion').DataTable();
                var venta = 0;
                var cantidad = 0;

                if (ctable.column(7).visible()) {
                    venta += parseFloat(row.FARMACIA_VENTA.replace(/,/g, ''));
                    cantidad += parseFloat(row.FARMACIA_CANTIDAD.replace(/,/g, ''));
                }
                
                if (ctable.column(13).visible()) {
                    venta += parseFloat(row.CADENA_FARMACIA_VENTA.replace(/,/g, ''));
                    cantidad += parseFloat(row.CADENA_FARMACIA_CANTIDAD.replace(/,/g, ''));
                }

                if (ctable.column(19).visible()) {
                    venta += parseFloat(row.MAYORISTA_VENTA.replace(/,/g, ''));
                    cantidad += parseFloat(row.MAYORISTA_CANTIDAD.replace(/,/g, ''));
                }

                if (ctable.column(25).visible()) {
                    venta += parseFloat(row.INSTITUCION_PRIVADA_VENTA.replace(/,/g, ''));
                    cantidad += parseFloat(row.INSTITUCION_PRIVADA_CANTIDAD.replace(/,/g, ''));
                }

                if (ctable.column(31).visible()) {
                    venta += parseFloat(row.CRUZ_AZUL_VENTA.replace(/,/g, ''));
                    cantidad += parseFloat(row.CRUZ_AZUL_CANTIDAD.replace(/,/g, ''));
                }

                if (ctable.column(37).visible()) {
                    venta += parseFloat(row.INSTITUCION_PUBLICA_VENTA.replace(/,/g, ''));
                    cantidad += parseFloat(row.INSTITUCION_PUBLICA_CANTIDAD.replace(/,/g, ''));
                }

                if (ctable.column(43).visible()) {
                    venta += parseFloat(row.LICITACION_VENTA.replace(/,/g, ''));
                    cantidad += parseFloat(row.LICITACION_CANTIDAD.replace(/,/g, ''));
                }

                return numeral(venta/cantidad).format('0,0.00');
             }},
            {"data": "TOTAL_VENTAS_C$", "render": function(data, type, row, meta) {
                var ctable = $('#table_contribucion').DataTable();
                var venta = 0;

                if (ctable.column(8).visible()) {
                    venta += parseFloat(row.FARMACIA_VENTA.replace(/,/g, ''));
                }
                
                if (ctable.column(14).visible()) {
                    venta += parseFloat(row.CADENA_FARMACIA_VENTA.replace(/,/g, ''));
                }

                if (ctable.column(20).visible()) {
                    venta += parseFloat(row.MAYORISTA_VENTA.replace(/,/g, ''));
                }

                if (ctable.column(26).visible()) {
                    venta += parseFloat(row.INSTITUCION_PRIVADA_VENTA.replace(/,/g, ''));
                }

                if (ctable.column(32).visible()) {
                    venta += parseFloat(row.CRUZ_AZUL_VENTA.replace(/,/g, ''));
                }

                if (ctable.column(38).visible()) {
                    venta += parseFloat(row.INSTITUCION_PUBLICA_VENTA.replace(/,/g, ''));
                }

                if (ctable.column(44).visible()) {
                    venta += parseFloat(row.LICITACION_VENTA.replace(/,/g, ''));
                }

                return numeral(venta).format('0,0.00');
             }},
            {"data": "TOTAL_COSTOS_C$","render": function(data, type, row, meta) { 
                var ctable = $('#table_contribucion').DataTable();
                var costo = 0;

                if (ctable.column(8).visible()) {
                    costo += parseFloat(row.FARMACIA_COSTO.replace(/,/g, ''));
                }
                
                if (ctable.column(14).visible()) {
                    costo += parseFloat(row.CADENA_FARMACIA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(20).visible()) {
                    costo += parseFloat(row.MAYORISTA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(26).visible()) {
                    costo += parseFloat(row.INSTITUCION_PRIVADA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(32).visible()) {
                    costo += parseFloat(row.CRUZ_AZUL_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(38).visible()) {
                    costo += parseFloat(row.INSTITUCION_PUBLICA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(42).visible()) {
                    costo += parseFloat(row.LICITACION_COSTO.replace(/,/g, ''));
                }

                return numeral(costo).format('0,0.00');
            }},
            {"data": "TOTAL_CONTRIBUCION_C$","render": function(data, type, row, meta) { 
                var ctable = $('#table_contribucion').DataTable();
                var venta = 0;
                var costo = 0;

                if (ctable.column(8).visible()) {
                    venta += parseFloat(row.FARMACIA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.FARMACIA_COSTO.replace(/,/g, ''));
                }
                
                if (ctable.column(14).visible()) {
                    venta += parseFloat(row.CADENA_FARMACIA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.CADENA_FARMACIA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(20).visible()) {
                    venta += parseFloat(row.MAYORISTA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.MAYORISTA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(26).visible()) {
                    venta += parseFloat(row.INSTITUCION_PRIVADA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.INSTITUCION_PRIVADA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(32).visible()) {
                    venta += parseFloat(row.CRUZ_AZUL_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.CRUZ_AZUL_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(38).visible()) {
                    venta += parseFloat(row.INSTITUCION_PUBLICA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.INSTITUCION_PUBLICA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(44).visible()) {
                    venta += parseFloat(row.LICITACION_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.LICITACION_COSTO.replace(/,/g, ''));
                }

                return numeral(venta-costo).format('0,0.00');
            }},
            {"data": "TOTAL_MARGEN","render": function(data, type, row, meta) {
                var ctable = $('#table_contribucion').DataTable();
                var venta = 0;
                var costo = 0;

                if (ctable.column(8).visible()) {
                    venta += parseFloat(row.FARMACIA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.FARMACIA_COSTO.replace(/,/g, ''));
                }
                
                if (ctable.column(14).visible()) {
                    venta += parseFloat(row.CADENA_FARMACIA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.CADENA_FARMACIA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(20).visible()) {
                    venta += parseFloat(row.MAYORISTA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.MAYORISTA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(26).visible()) {
                    venta += parseFloat(row.INSTITUCION_PRIVADA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.INSTITUCION_PRIVADA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(32).visible()) {
                    venta += parseFloat(row.CRUZ_AZUL_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.CRUZ_AZUL_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(38).visible()) {
                    venta += parseFloat(row.INSTITUCION_PUBLICA_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.INSTITUCION_PUBLICA_COSTO.replace(/,/g, ''));
                }

                if (ctable.column(44).visible()) {
                    venta += parseFloat(row.LICITACION_VENTA.replace(/,/g, ''));
                    costo += parseFloat(row.LICITACION_COSTO.replace(/,/g, ''));
                }

                return numeral(((venta-costo)/venta)*100).format('0,0.00');
             }},
            // COLUMNAS ADICIONALES: van al final para no mover los indices que usan los botones de
            // canal y calcularTotales. Las que aun no tienen fuente quedan como placeholder ("-").
            // Año anterior por canal (UNDS / VTA), mismo orden que los encabezados
            ...CANALES_EXTRA.flatMap(canal => [
                {"data": "ANT_CANAL." + canal + ".U", "defaultContent": 0, render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
                {"data": "ANT_CANAL." + canal + ".V", "defaultContent": 0, render: $.fn.dataTable.render.number( ',', '.', 2  , '' )}
            ]),
            {"data": "ANT_UNIDADES", render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
            {"data": "ANT_VENTA", render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            // Venta mensual por canal: 12 meses de unidades y 12 de C$ (indice 0 = enero del año actual)
            ...CANALES_EXTRA.flatMap(canal => [
                ...Array.from({ length: 12 }, (_, m) => ({"data": "MES_CANAL." + canal + ".U." + m, "defaultContent": 0, render: $.fn.dataTable.render.number( ',', '.', 0  , '' )})),
                ...Array.from({ length: 12 }, (_, m) => ({"data": "MES_CANAL." + canal + ".V." + m, "defaultContent": 0, render: $.fn.dataTable.render.number( ',', '.', 0  , '' )}))
            ]),
            {"data": "Lote_Mas_a_Vencer_PRIVADO_6_MESES", "className": "dt-center", "render": function(data, type) {
                if (!data) return type === 'display' ? '-' : '';
                // dd/mm/yyyy -> yyyymmdd para que ordene por fecha
                return (type === 'sort' || type === 'type') ? data.split('/').reverse().join('') : data;
            }},
            {"data": "Existencia_En_Lote_proximo_Vencer_6_MESES", render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
            {"data": "CLIENTES", render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
            {"data": "ULTIMA_COMPRA", "className": "dt-center", "render": function(data, type, row) {
                if (!data) return type === 'display' ? '-' : '';
                // dd/mm/yyyy -> yyyymmdd para que ordene por fecha
                if (type === 'sort' || type === 'type') return data.split('/').reverse().join('');
                // Documento de la compra (embarque) visible al pasar el mouse
                return type === 'display' && row.ULTIMA_COMPRA_DOC
                    ? $('<span>').attr('title', row.ULTIMA_COMPRA_DOC).text(data).prop('outerHTML')
                    : data;
            }},
            {"data": "ULTIMA_COMPRA_CANT", "defaultContent": "-", render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
            {"data": "ULTIMO_COSTO_LOC", "defaultContent": "-", render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "ULTIMO_COSTO_DOL", "defaultContent": "-", render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "PORC_BONIFICADO", "defaultContent": "-", "render": function(data, type, row) {
                if (data === null || data === undefined) return type === 'display' ? '-' : '';
                if (type !== 'display') return data;
                // Detalle del calculo visible al pasar el mouse
                var detalle = numeral(row.UNDS_BONIFICADAS).format('0,0') + ' bonificadas de ' + numeral(row.UNDS_PRIVADAS).format('0,0') + ' unds.';
                return $('<span>').attr('title', detalle).text(numeral(data).format('0,0.00')).prop('outerHTML');
            }},
        ],
        "columnDefs": [
            // El buscador solo mira las columnas del SKU (articulo, descripcion, fabricante, categoria).
            // En columnDefs la primera regla tiene prioridad, por eso va antes que '_all'.
            { "searchable": true,  "targets": [ 0, 1, 2, 3 ] },
            { "searchable": false, "targets": '_all' },
            {"className": "dt-center", "targets":[ 3 ]},
            { "className": "dt-right", "targets": [ 7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48 ]}, 
            { "width": "200px", "targets": [ 1 ] },
            { "width": "10px", "targets": [ 7, 8, 9, 10, 12, 13, 14, 15, 16, 19, 21, 22, 25, 27, 28, 31, 33, 34, 39, 40, 43, 44, 45, 46 ] },
            { "width": "10px", "targets": [ 11, 17, 23, 29, 35, 41, 47 ] },
            { "width": "10px", "targets": [ 38 ] },
            { "width": "10px", "targets": [ 20, 26, 32, 37 ] },
            { "width": "10px", "targets": [ 12, 18, 24, 30, 36, 48 ] },
            { "width": "50px", "targets": [ 42 ] }
        ],
        "initComplete": function(settings, json) {
            const buttons = [selectedButton, buttonCadena, buttonMayorista, buttonPrivada, buttonCruzAzul, buttonPublica, buttonLicitacion];

            buttons.forEach(button => {
                var buttonNode = this.api().button('.btn-' + button).node();
                var $button = $(buttonNode);

                if (button !== null) {
                    $button.css({ 'color': '#dc3545' }).addClass('dt-button-active');
                } else {
                    $button.css({ 'color': '#000000' }).removeClass('dt-button-active');
                }
            });
        }

    
    });

    /*if (selectedButton !== null) {
        console.log(selectedButton);
        Table.button('.btn-' + selectedButton).trigger();
    }*/
    
    Table.on('column-visibility', function(e, settings, column, state) {
        const colsHide = [4, 10, 16, 22, 28, 34, 40];
        if (colsHide.includes(column)) {
            Table.rows().invalidate().draw();
        }
    });

    $("#table_contribucion_length").hide();
    $("#table_contribucion_filter").hide();

    
    $("#exp-to-excel-canales").click(function(){
        location.href = "ExportToExcelCanales";
    })

    $("#BtnClick").click(function() {
        fechaIni = $("#f1").val();
        fechaEnd = $("#f2").val();
        
        Swal.fire({
            title: "Recalcular contribución de canales",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Calcular",
            showLoaderOnConfirm: true,
            preConfirm: async (login) => {
                try {
                    const githubUrl = `calcularCanales/`+fechaIni+`/`+fechaEnd;
                    const response = await fetch(githubUrl);
                    if (!response.ok) {
                        const respuesta = await response.json();
                        return Swal.showValidationMessage(`${respuesta.error}`);
                    }
                    
                    return response.json();
                } catch (error) {
                    
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: result.value.Titulo,
                    text: result.value.Mensaje,
                    confirmButtonText: "Ok",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        } 
                    });
            }
        });

    })

    Table.on('draw.dt', function () {
       calcularTotales(Table);
    });

    // Quitar el skeleton de carga cuando llega la respuesta de canalData (con o sin error)
    Table.on('xhr.dt', function (e, settings, json) {
        $('#cc_skeleton').fadeOut(200);
        if (!json) {
            Swal.fire('Error', 'No se pudo calcular la contribución por canal. Intente nuevamente.', 'error');
        }
    });

    
    
});


function UpdateUI(Prom, Units) {

    $("#prom_normal").text("0.00");
    $("#units_desplazadas").text("0.00");

    $("#prom_normal").text(Prom);
    $("#units_desplazadas").text(Units);
    
}
function calcularTotales(table) {  
    // Las cantidades vienen formateadas con comas desde PHP ("1,234"); parseFloat las cortaria en la coma
    const toNum = v => parseFloat(String(v).replace(/,/g, "")) || 0;
    var Farmacia_Cantidad = Farmacia_Costo = Farmacia_Venta = Farmacia_Contribucion = 0;
    var Cadena_Farmacia_Cantidad = Cadena_Farmacia_Costo = Cadena_Farmacia_Venta = Cadena_Farmacia_Contribucion = 0;
    var Mayorista_Cantidad = Mayorista_Costo = Mayorista_Venta = Mayorista_Contribucion = 0;
    var Cruz_Azul_Cantidad = Cruz_Azul_Costo = Cruz_Azul_Venta = Cruz_Azul_Contribucion = 0;
    var Institucion_Privada_Cantidad = Institucion_Privada_Costo = Institucion_Privada_Venta = Institucion_Privada_Contribucion = 0;
    var Institucion_Publica_Cantidad = Institucion_Publica_Costo = Institucion_Publica_Venta = Institucion_Publica_Contribucion = 0;
    var Licitacion_Cantidad = Licitacion_Costo = Licitacion_Venta = Licitacion_Contribucion = 0;
    var Total_Cantidad = Total_Costo = Total_Venta = Total_Contribucion = 0;
    
    const config = {
        cantidad: [
            { col: 8,  field: "FARMACIA_CANTIDAD" },
            { col: 14, field: "CADENA_FARMACIA_CANTIDAD" },
            { col: 20, field: "MAYORISTA_CANTIDAD" },
            { col: 26, field: "INSTITUCION_PRIVADA_CANTIDAD" },
            { col: 32, field: "CRUZ_AZUL_CANTIDAD" },
            { col: 38, field: "INSTITUCION_PUBLICA_CANTIDAD" },
            { col: 44, field: "LICITACION_CANTIDAD" }
        ],
        venta: [
            { col: 10,  field: "FARMACIA_VENTA" },
            { col: 16, field: "CADENA_FARMACIA_VENTA" },
            { col: 22, field: "MAYORISTA_VENTA" },
            { col: 28, field: "INSTITUCION_PRIVADA_VENTA" },
            { col: 34, field: "CRUZ_AZUL_VENTA" },
            { col: 40, field: "INSTITUCION_PUBLICA_VENTA" },
            { col: 46, field: "LICITACION_VENTA" }
        ],
        costo: [
            { col: 11, field: "FARMACIA_COSTO" },
            { col: 17, field: "CADENA_FARMACIA_COSTO" },
            { col: 23, field: "MAYORISTA_COSTO" },
            { col: 29, field: "INSTITUCION_PRIVADA_COSTO" },
            { col: 35, field: "CRUZ_AZUL_COSTO" },
            { col: 41, field: "INSTITUCION_PUBLICA_COSTO" },
            { col: 47, field: "LICITACION_COSTO" }
        ]
    };

    // Visibilidad de cada canal: se consulta una vez por calculo, no en cada fila
    Object.keys(config).forEach(tipo => config[tipo].forEach(c => { c.visible = table.column(c.col).visible(); }));

    table.rows({ search: 'applied' }).every(function() {
        var data = this.data();

        // TOTAL DE FARMACIAS
        Farmacia_Cantidad       += toNum(data.FARMACIA_CANTIDAD) || 0;
        Farmacia_Venta          += toNum(data.FARMACIA_VENTA) || 0;
        Farmacia_Costo          += toNum(data.FARMACIA_COSTO) || 0;
        Farmacia_Contribucion   += toNum(data.FARMACIA_CONTRIBUCION) || 0;

        // TOTAL DE CADENA DE FARMACIAS
        Cadena_Farmacia_Cantidad    += toNum(data.CADENA_FARMACIA_CANTIDAD) || 0;
        Cadena_Farmacia_Venta       += toNum(data.CADENA_FARMACIA_VENTA) || 0;
        Cadena_Farmacia_Costo       += toNum(data.CADENA_FARMACIA_COSTO) || 0;
        Cadena_Farmacia_Contribucion+= toNum(data.CADENA_FARMACIA_CONTRIBUCION) || 0;

        // TOTAL DE MAYORISTAS
        Mayorista_Cantidad      += toNum(data.MAYORISTA_CANTIDAD) || 0;
        Mayorista_Venta         += toNum(data.MAYORISTA_VENTA) || 0;
        Mayorista_Costo         += toNum(data.MAYORISTA_COSTO) || 0;
        Mayorista_Contribucion  += toNum(data.MAYORISTA_CONTRIBUCION) || 0;

        // TOTAL DE CRUZ AZUL
        Cruz_Azul_Cantidad      += toNum(data.CRUZ_AZUL_CANTIDAD) || 0;
        Cruz_Azul_Venta         += toNum(data.CRUZ_AZUL_VENTA) || 0;
        Cruz_Azul_Costo         += toNum(data.CRUZ_AZUL_COSTO) || 0;
        Cruz_Azul_Contribucion  += toNum(data.CRUZ_AZUL_CONTRIBUCION) || 0;

        // TOTAL INTITUCION PRIVADA
        Institucion_Privada_Cantidad    += toNum(data.INSTITUCION_PRIVADA_CANTIDAD) || 0;
        Institucion_Privada_Venta       += toNum(data.INSTITUCION_PRIVADA_VENTA) || 0;
        Institucion_Privada_Costo       += toNum(data.INSTITUCION_PRIVADA_COSTO) || 0;
        Institucion_Privada_Contribucion+= toNum(data.INSTITUCION_PRIVADA_CONTRIBUCION) || 0;

        // TOTAL INTITUCION PUBLICA
        Institucion_Publica_Cantidad    += toNum(data.INSTITUCION_PUBLICA_CANTIDAD) || 0;
        Institucion_Publica_Venta       += toNum(data.INSTITUCION_PUBLICA_VENTA) || 0;
        Institucion_Publica_Costo       += toNum(data.INSTITUCION_PUBLICA_COSTO) || 0;
        Institucion_Publica_Contribucion+= toNum(data.INSTITUCION_PUBLICA_CONTRIBUCION) || 0;

        // TOTAL DE LICITACIONES
        Licitacion_Cantidad    += toNum(data.LICITACION_CANTIDAD) || 0;
        Licitacion_Venta       += toNum(data.LICITACION_VENTA) || 0;
        Licitacion_Costo       += toNum(data.LICITACION_COSTO) || 0;
        Licitacion_Contribucion+= toNum(data.LICITACION_CONTRIBUCION) || 0;
  
        // Función para acumular según config
        function acumularTotales(tipo, total) {
            config[tipo].forEach(({ visible, field }) => {
                if (visible) {
                    total += toNum(data[field]) || 0;
                }
            });
            return total;
        }

        Total_Cantidad = acumularTotales("cantidad", Total_Cantidad);
        Total_Venta    = acumularTotales("venta", Total_Venta);
        Total_Costo    = acumularTotales("costo", Total_Costo);
        
    })
    
    
    // TOTAL DE FARMACIAS
    $('#Farmacia_Cantidad').html(numeral(Farmacia_Cantidad).format('0,0'));
    $('#Farmacia_Promedio').html('C$ '+numeral(Farmacia_Venta/Farmacia_Cantidad).format('0,0'));
    $('#Farmacia_Venta').html('C$ '+numeral(Farmacia_Venta).format('0,0'));
    $('#Farmacia_Costo').html('C$ '+numeral(Farmacia_Costo).format('0,0'));
    $('#Farmacia_Contribucion').html('C$ '+numeral(Farmacia_Contribucion).format('0,0'));
    $('#Farmacia_Margen').html(numeral((Farmacia_Contribucion/Farmacia_Venta)*100).format('0,0.00'));

    // TOTAL DE CADENA DE FARMACIAS
    $('#Cadena_Farmacia_Cantidad').html(numeral(Cadena_Farmacia_Cantidad).format('0,0'));
    $('#Cadena_Farmacia_Promedio').html('C$ '+numeral(Cadena_Farmacia_Venta/Cadena_Farmacia_Cantidad).format('0,0'));
    $('#Cadena_Farmacia_Venta').html('C$ '+numeral(Cadena_Farmacia_Venta).format('0,0'));
    $('#Cadena_Farmacia_Costo').html('C$ '+numeral(Cadena_Farmacia_Costo).format('0,0'));
    $('#Cadena_Farmacia_Contribucion').html('C$ '+numeral(Cadena_Farmacia_Contribucion).format('0,0'));
    $('#Cadena_Farmacia_Margen').html(numeral((Cadena_Farmacia_Contribucion/Cadena_Farmacia_Venta)*100).format('0,0.00'));

    // TOTAL DE MAYORISTAS
    $('#Mayorista_Cantidad').html(numeral(Mayorista_Cantidad).format('0,0'));
    $('#Mayorista_Promedio').html('C$ '+numeral(Mayorista_Venta/Mayorista_Cantidad).format('0,0'));
    $('#Mayorista_Venta').html('C$ '+numeral(Mayorista_Venta).format('0,0'));
    $('#Mayorista_Costo').html('C$ '+numeral(Mayorista_Costo).format('0,0'));
    $('#Mayorista_Contribucion').html('C$ '+numeral(Mayorista_Contribucion).format('0,0'));
    $('#Mayorista_Margen').html(numeral((Mayorista_Contribucion/Mayorista_Venta)*100).format('0,0.00'));

    // TOTAL INTITUCION PRIVADA
    $('#Institucion_Privada_Cantidad').html(numeral(Institucion_Privada_Cantidad).format('0,0'));
    $('#Institucion_Privada_Promedio').html('C$ '+numeral(Institucion_Privada_Venta/Institucion_Privada_Cantidad).format('0,0'));
    $('#Institucion_Privada_Venta').html('C$ '+numeral(Institucion_Privada_Venta).format('0,0'));
    $('#Institucion_Privada_Costo').html('C$ '+numeral(Institucion_Privada_Costo).format('0,0'));
    $('#Institucion_Privada_Contribucion').html('C$ '+numeral(Institucion_Privada_Contribucion).format('0,0'));
    $('#Institucion_Privada_Margen').html(numeral((Institucion_Privada_Contribucion/Institucion_Privada_Venta)*100).format('0,0.00'));
    
    // TOTAL DE CRUZ AZUL
    $('#Cruz_Azul_Cantidad').html(numeral(Cruz_Azul_Cantidad).format('0,0'));
    $('#Cruz_Azul_Promedio').html('C$ '+numeral(Cruz_Azul_Venta/Cruz_Azul_Cantidad).format('0,0'));
    $('#Cruz_Azul_Venta').html('C$ '+numeral(Cruz_Azul_Venta).format('0,0'));
    $('#Cruz_Azul_Costo').html('C$ '+numeral(Cruz_Azul_Costo).format('0,0'));
    $('#Cruz_Azul_Contribucion').html('C$ '+numeral(Cruz_Azul_Contribucion).format('0,0'));
    $('#Cruz_Azul_Margen').html(numeral((Cruz_Azul_Contribucion/Cruz_Azul_Venta)*100).format('0,0.00'));

    // TOTAL INTITUCION PUBLICA
    $('#Institucion_Publica_Cantidad').html(numeral(Institucion_Publica_Cantidad).format('0,0'));
    $('#Institucion_Publica_Promedio').html('C$ '+numeral(Institucion_Publica_Venta/Institucion_Publica_Cantidad).format('0,0'));
    $('#Institucion_Publica_Venta').html('C$ '+numeral(Institucion_Publica_Venta).format('0,0'));
    $('#Institucion_Publica_Costo').html('C$ '+numeral(Institucion_Publica_Costo).format('0,0'));
    $('#Institucion_Publica_Contribucion').html('C$ '+numeral(Institucion_Publica_Contribucion).format('0,0'));
    $('#Institucion_Publica_Margen').html(numeral((Institucion_Publica_Contribucion/Institucion_Publica_Venta)*100).format('0,0.00'));

    // TOTAL DE LICITACIONES
    $('#Licitacion_Cantidad').html(numeral(Licitacion_Cantidad).format('0,0'));
    $('#Licitacion_Promedio').html('C$ '+numeral(Licitacion_Venta/Licitacion_Cantidad).format('0,0'));
    $('#Licitacion_Venta').html('C$ '+numeral(Licitacion_Venta).format('0,0'));
    $('#Licitacion_Costo').html('C$ '+numeral(Licitacion_Costo).format('0,0'));
    $('#Licitacion_Contribucion').html('C$ '+numeral(Licitacion_Contribucion).format('0,0'));
    $('#Licitacion_Margen').html(numeral((Licitacion_Contribucion/Licitacion_Venta)*100).format('0,0.00'));

    // TOTAL DE TOTALES
    $('#Total_Cantidad').html(numeral(Total_Cantidad).format('0,0'));
    $('#Total_Promedio').html('C$ '+numeral(Total_Venta/Total_Cantidad).format('0,0'));
    $('#Total_Venta').html('C$ '+numeral(Total_Venta).format('0,0'));
    $('#Total_Costo').html('C$ '+numeral(Total_Costo).format('0,0'));
    $('#Total_Contribucion').html('C$ '+numeral(Total_Venta-Total_Costo).format('0,0'));
    $('#Total_Margen').html(numeral(((Total_Venta-Total_Costo)/Total_Venta)*100).format('0,0.00'));
    
}
function getDetalleArticulo(Articulos, Descripcion){
    $("#id_descripcion").html(Descripcion+` | `+ Articulos);
    $("#info1").show();
    $("#info2").show();

	$("#mdDetalleArt").modal('show');
    grafMensual(Articulos);
}

function getDetalleCanal(Articulos, Canal, Descripcion, opcion){
    $("#id_descripcion").html(Descripcion+` | `+ Articulos + ` | ` + Canal);
    $("#info1").hide();
    $("#info2").hide();
    
    
	$("#mdDetalleArt").modal('show');
    grafCanales(Articulos, Canal, opcion);
}

function grafCanales(Articulos, Canal, opcion){
    $.getJSON("get12Canales/" + Articulos + "/" + Canal + "/" + opcion, function(json) {
            dta = [];
            title = [];
            tmp_total = 0;
            Day_Max = [];
            numMonths = 0;
       
            var vVtsDiarias;

            numMonths = 0;
            $.each(json[0]['CANTIDAD_MES'], function(i, x) {

                if (parseFloat(x['data']) > 0) {
                    numMonths++;
                }
                tmp_total += parseFloat(x['data']);

                dta.push({
                    name  : x['Mes'],
                    y     : x['data'], 
                });

                title.push(x['name']); 
                Day_Max.push(x['data']);
            });

            temporal = '<span style="color:black">\u25CF</span><b>{point.y} </b> UNITS<br/>';                
            grafica_articulos.tooltip = {
                pointFormat : temporal
            }

            var PromUnits = tmp_total / numMonths

            vVtsDiarias = numeral(tmp_total).format('0,0.00');
            PromUnits = numeral(PromUnits).format('0,0.00');
            
            //grafica_articulos.xAxis.categories = title;
            //grafica_articulos.subtitle.text = vVtsDiarias + " UNITS";
            grafica_articulos.series[0].data = dta;

            chart = new Highcharts.Chart(grafica_articulos);
            
            chart.yAxis[0].update();            

            UpdateUI(PromUnits, vVtsDiarias);

    })
}

function grafMensual(Articulo){
    $.each(JsonCanal, function (i, item) {
        dta = [];
        title = [];
        tmp_total = 0;
        Day_Max = [];
        numMonths = 0;

        if(item.ARTICULODESC === Articulo){
            $("#idCostoPriv").html(numeral(item.COSTO_PROM_PRIV_PACK).format('0,0.00'));
            $("#idCostoMinsa").html(numeral(item.COSTO_PROM_MINSA_PACK).format('0,0.00'));
            $("#idValorInventario").html(numeral(item.Valor_USD_Inventario_ONHAND_PRIVADO).format('0,0.00'));
            $("#idValorDisponible").html(numeral(item.Valor_USD_Total_OnHand_Tránsito_PRIVADO).format('0,0.00'));
            $('#idCantDisponible').html(numeral(item.Disponibilidad_Packs_PRIVADO_6_MESES).format('0,0'));
            $('#idLoteVencer').html(item.Lote_Mas_a_Vencer_PRIVADO_6_MESES);
            $('#idCantProxima').html(numeral(item.Existencia_En_Lote_proximo_Vencer_6_MESES).format('0,0'));
            $.each(item.CANTIDAD_MES, function(i, x) {
                
                if (parseFloat(x['data']) > 0) {
                    numMonths++;
                }

                tmp_total = tmp_total + parseFloat(x['data']);
                dta.push({
                    name  : x['Mes'],                                        
                    y     : x['data'], 
                });

                title.push(x['name']); 
                Day_Max.push(x['data']); 
            }); 

            temporal = '<span style="color:black">\u25CF</span><b>{point.y} </b> UNITS<br/>';  
            grafica_articulos.tooltip = {
                pointFormat : temporal
            }

            var PromUnits = tmp_total / numMonths

            vVtsDiarias = numeral(tmp_total).format('0,0.00');
            PromUnits = numeral(PromUnits).format('0,0.00');

            //grafica_articulos.xAxis.categories = title;
            //grafica_articulos.subtitle.text = vVtsDiarias + " UNITS";
            grafica_articulos.series[0].data = dta;

            chart = new Highcharts.Chart(grafica_articulos);
            
            chart.yAxis[0].update();
            UpdateUI(PromUnits, vVtsDiarias);
            return false;
        }

    })

}

function verificarLocalStorage(boton, canal) {
    var currentState = localStorage.getItem(boton);
    if (currentState === canal) {
        localStorage.removeItem(boton);
        return false;
    } else {
        localStorage.setItem(boton, canal);
       return true;
    }    
}

</script>