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
        subtitle: {
            text: 'C$ 0.00',
            align: 'right',
            x: -10
        },
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
    
    $('#id_txt_buscar').on('keyup', function() {   
        var vTable = $('#table_contribucion').DataTable();     
        vTable.search(this.value).draw();        
    });

    $( "#InputCanales").change(function() {
        var table = $('#table_contribucion').DataTable();
        table.page.len(this.value).draw();
    });

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

                                    dt.draw(false);
                                
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
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        dt.draw(false);
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
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        dt.draw(false);
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
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        dt.draw(false);
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
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        dt.draw(false);
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
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        dt.draw(false);
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
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        dt.draw(false);
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
                                    } else {
                                        $(node).css({
                                            'color': '#000000',
                                            'background-color': 'transparent'
                                        });
                                        dt.draw(false);
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
                    }
                }]
            }
        },    
        stateSave: true,
        fixedColumns: {
            start: 4
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

                if (ctable.column(4).visible()) {
                    cantidad += parseFloat(row.FARMACIA_CANTIDAD.replace(/,/g, ''));
                }else {viArray.push('FARMACIAS');}
                
                if (ctable.column(10).visible()) {
                    cantidad += parseFloat(row.CADENA_FARMACIA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('CADENAS');}

                if (ctable.column(16).visible()) {
                    cantidad += parseFloat(row.MAYORISTA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('MAYORISTAS');}

                if (ctable.column(22).visible()) {
                    cantidad += parseFloat(row.INSTITUCION_PRIVADA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('INSTITUCIONES_PRIVADAS');}

                if (ctable.column(28).visible()) {
                cantidad += parseFloat(row.CRUZ_AZUL_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('CRUZ_AZUL');}

                if (ctable.column(34).visible()) {
                    cantidad += parseFloat(row.INSTITUCION_PUBLICA_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('INSTITUCIONES_PUBLICAS');}

                if (ctable.column(40).visible()) {
                    cantidad += parseFloat(row.LICITACION_CANTIDAD.replace(/,/g, ''));
                }else{viArray.push('LICITACIONES');}

                if(viArray == ''){
                    viArray.push('Todos');
                }                

                return '<a href="#" onclick=\'getDetalleCanal("' + row.ARTICULODESC + '", ' + JSON.stringify(viArray) + ', "' + row.DESCRIPCION + '", "' + 1 + '")\'>' + numeral(cantidad).format('0,0.00') + '</a>';
            }},
            {"data": "TOTAL_PRECIO_PROM",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "TOTAL_VENTAS_C$",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "TOTAL_COSTOS_C$",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "TOTAL_CONTRIBUCION_C$",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "TOTAL_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},         
        ],
        "columnDefs": [        
            {"className": "dt-center", "targets":[ 3 ]},               
            {"className": "dt-right", "targets": [ 4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45 ]},
            { "width": "200px", "targets": [ 1 ] },
            { "width": "10px", "targets": [ 4, 5, 6, 7, 9, 10, 11, 12, 13, 16, 18, 19, 22, 24, 25, 28, 30, 31, 36, 37, 40, 41, 42, 43 ] },
            { "width": "10px", "targets": [ 8, 14, 20, 26, 32, 38, 44 ] },
            { "width": "10px", "targets": [ 35 ] },
            { "width": "10px", "targets": [ 17, 23, 29, 34 ] },
            { "width": "10px", "targets": [ 9, 15, 21, 27, 33, 45 ] },
            { "width": "50px", "targets": [ 39 ] }
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
                    return Swal.showValidationMessage(`${JSON.stringify(await response.json())}`);
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
                    title: "Calculos completados",
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
        calcularTotales();        
    });

    
    
});

function calcularTotales() {    
    console.log('hoy')
    var table = $('#table_contribucion').DataTable();
    var Farmacia_Cantidad = Farmacia_Costo = Farmacia_Venta = Farmacia_Contribucion = 0;
    var Cadena_Farmacia_Cantidad = Cadena_Farmacia_Costo = Cadena_Farmacia_Venta = Cadena_Farmacia_Contribucion = 0;
    var Mayorista_Cantidad = Mayorista_Costo = Mayorista_Venta = Mayorista_Contribucion = 0;
    var Cruz_Azul_Cantidad = Cruz_Azul_Costo = Cruz_Azul_Venta = Cruz_Azul_Contribucion = 0;
    var Institucion_Privada_Cantidad = Institucion_Privada_Costo = Institucion_Privada_Venta = Institucion_Privada_Contribucion = 0;
    var Institucion_Publica_Cantidad = Institucion_Publica_Costo = Institucion_Publica_Venta = Institucion_Publica_Contribucion = 0;
    var Licitacion_Cantidad = Licitacion_Costo = Licitacion_Venta = Licitacion_Contribucion = 0;
    var Total_Cantidad = Total_Costo = Total_Venta = Total_Contribucion = 0;

    table.rows({ search: 'applied' }).every(function() {
        var data = this.data();
        
        // TOTAL DE FARMACIAS
        Farmacia_Cantidad       += parseFloat(data.FARMACIA_CANTIDAD) || 0;
        Farmacia_Venta          += parseFloat(data.FARMACIA_VENTA) || 0;
        Farmacia_Costo          += parseFloat(data.FARMACIA_COSTO) || 0;
        Farmacia_Contribucion   += parseFloat(data.FARMACIA_CONTRIBUCION) || 0;

        // TOTAL DE CADENA DE FARMACIAS
        Cadena_Farmacia_Cantidad    += parseFloat(data.CADENA_FARMACIA_CANTIDAD) || 0;
        Cadena_Farmacia_Venta       += parseFloat(data.CADENA_FARMACIA_VENTA) || 0;
        Cadena_Farmacia_Costo       += parseFloat(data.CADENA_FARMACIA_COSTO) || 0;
        Cadena_Farmacia_Contribucion+= parseFloat(data.CADENA_FARMACIA_CONTRIBUCION) || 0;

        // TOTAL DE MAYORISTAS
        Mayorista_Cantidad      += parseFloat(data.MAYORISTA_CANTIDAD) || 0;
        Mayorista_Venta         += parseFloat(data.MAYORISTA_VENTA) || 0;
        Mayorista_Costo         += parseFloat(data.MAYORISTA_COSTO) || 0;
        Mayorista_Contribucion  += parseFloat(data.MAYORISTA_CONTRIBUCION) || 0;

        // TOTAL DE CRUZ AZUL
        Cruz_Azul_Cantidad      += parseFloat(data.CRUZ_AZUL_CANTIDAD) || 0;
        Cruz_Azul_Venta         += parseFloat(data.CRUZ_AZUL_VENTA) || 0;
        Cruz_Azul_Costo         += parseFloat(data.CRUZ_AZUL_COSTO) || 0;
        Cruz_Azul_Contribucion  += parseFloat(data.CRUZ_AZUL_CONTRIBUCION) || 0;

        // TOTAL INTITUCION PRIVADA
        Institucion_Privada_Cantidad    += parseFloat(data.INSTITUCION_PRIVADA_CANTIDAD) || 0;
        Institucion_Privada_Venta       += parseFloat(data.INSTITUCION_PRIVADA_VENTA) || 0;
        Institucion_Privada_Costo       += parseFloat(data.INSTITUCION_PRIVADA_COSTO) || 0;
        Institucion_Privada_Contribucion+= parseFloat(data.INSTITUCION_PRIVADA_CONTRIBUCION) || 0;

        // TOTAL INTITUCION PUBLICA
        Institucion_Publica_Cantidad    += parseFloat(data.INSTITUCION_PUBLICA_CANTIDAD) || 0;
        Institucion_Publica_Venta       += parseFloat(data.INSTITUCION_PUBLICA_VENTA) || 0;
        Institucion_Publica_Costo       += parseFloat(data.INSTITUCION_PUBLICA_COSTO) || 0;
        Institucion_Publica_Contribucion+= parseFloat(data.INSTITUCION_PUBLICA_CONTRIBUCION) || 0;

        // TOTAL DE LICITACIONES
        Licitacion_Cantidad    += parseFloat(data.LICITACION_CANTIDAD) || 0;
        Licitacion_Venta       += parseFloat(data.LICITACION_VENTA) || 0;
        Licitacion_Costo       += parseFloat(data.LICITACION_COSTO) || 0;
        Licitacion_Contribucion+= parseFloat(data.LICITACION_CONTRIBUCION) || 0;

        // TOTAL DE TOTALES
        if (table.column(4).visible()) {
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
        }
        if (table.column(10).visible()) {
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
        }
        if (table.column(16).visible()) {
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
        }
        if (table.column(22).visible()) {
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
        }
        if (table.column(28).visible()) {
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
        }
        if (table.column(34).visible()) {
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
        }
        if (table.column(40).visible()) {
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
        }
        if (table.column(6).visible()) {
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
        }
        if (table.column(12).visible()) {
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
        }
        if (table.column(18).visible()) {
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
        }
        if (table.column(24).visible()) {
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
        }
        if (table.column(30).visible()) {
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
        }
        if (table.column(36).visible()) {
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
        }
        if (table.column(42).visible()) {
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
        }
        if (table.column(7).visible()) {
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
        }
        if (table.column(13).visible()) {
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
        }
        if (table.column(19).visible()) {
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
        }
        if (table.column(25).visible()) {
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
        }
        if (table.column(31).visible()) {
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
        }
        if (table.column(37).visible()) {
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
        }
        if (table.column(43).visible()) {
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
        }
        
    });
    
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
       
            var vVtsDiarias;

            $.each(json[0]['CANTIDAD_MES'], function(i, x) {
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

            vVtsDiarias = numeral(tmp_total).format('0,0.00');
            
            grafica_articulos.xAxis.categories = title;
            grafica_articulos.subtitle.text = vVtsDiarias + " UNITS";
            grafica_articulos.series[0].data = dta;

            chart = new Highcharts.Chart(grafica_articulos);
            
            chart.yAxis[0].update();

    })
}

function grafMensual(Articulo){
    $.each(JsonCanal, function (i, item) {
        dta = [];
        title = [];
        tmp_total = 0;
        Day_Max = [];
        if(item.ARTICULODESC === Articulo){
            $("#idCostoPriv").html(numeral(item.COSTO_PROM_PRIV_PACK).format('0,0.00'));
            $("#idCostoMinsa").html(numeral(item.COSTO_PROM_MINSA_PACK).format('0,0.00'));
            $("#idValorInventario").html(numeral(item.Valor_USD_Inventario_ONHAND_PRIVADO).format('0,0.00'));
            $("#idValorDisponible").html(numeral(item.Valor_USD_Total_OnHand_Tránsito_PRIVADO).format('0,0.00'));
            $('#idCantDisponible').html(numeral(item.Disponibilidad_Packs_PRIVADO_6_MESES).format('0,0'));
            $('#idLoteVencer').html(item.Lote_Mas_a_Vencer_PRIVADO_6_MESES);
            $('#idCantProxima').html(numeral(item.Existencia_En_Lote_proximo_Vencer_6_MESES).format('0,0'));
            $.each(item.CANTIDAD_MES, function(i, x) {
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

            vVtsDiarias = numeral(tmp_total).format('0,0.00');

            grafica_articulos.xAxis.categories = title;
            grafica_articulos.subtitle.text = vVtsDiarias + " UNITS";
            grafica_articulos.series[0].data = dta;

            chart = new Highcharts.Chart(grafica_articulos);
            
            chart.yAxis[0].update();
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