<script type="text/javascript">
    fullScreen();
    var JsonCanal = new Array();

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
                buttons: [ {
                        extend: 'colvis',
                        text: 'Columnas visibles',
                    } ]
            },
            topEnd: {
                buttons: [ {
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
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'FARMACIAS\', \'' + row.DESCRIPCION + '\')">' + data + '</a>';
            }},
            {"data": "FARMACIA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "FARMACIA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'CADENAS\', \'' + row.DESCRIPCION + '\')">' + data + '</a>';
            }},
            {"data": "CADENA_FARMACIA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CADENA_FARMACIA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'MAYORISTAS\', \'' + row.DESCRIPCION + '\')">' + data + '</a>';
            }},
            {"data": "MAYORISTA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "MAYORISTA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'INSTITUCIONES_PRIVADAS\', \'' + row.DESCRIPCION + '\')">' + data + '</a>';
            }},
            {"data": "INSTITUCION_PRIVADA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PRIVADA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'CRUZ_AZUL\', \'' + row.DESCRIPCION + '\')">' + data + '</a>';
            }},
            {"data": "CRUZ_AZUL_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "CRUZ_AZUL_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_CANTIDAD","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'INSTITUCIONES_PUBLICAS\', \'' + row.DESCRIPCION + '\')">' + data + '</a>';
            }},
            {"data": "INSTITUCION_PUBLICA_PROMEDIO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_VENTA",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_COSTO",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_CONTRIBUCION",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "INSTITUCION_PUBLICA_MARGEN",render: $.fn.dataTable.render.number( ',', '.', 2  , '' )},
            {"data": "TOTAL_VENTAS_PACK","render": function(data, type, row, meta) {                
                return '<a href="#" onclick="getDetalleCanal(\'' + row.ARTICULODESC + '\',\'Todos\', \'' + row.DESCRIPCION + '\')">' + data + '</a>';
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
            { "width": "110px", "targets": [ 4, 5, 6, 7, 9, 10, 11, 12, 13, 16, 17, 18, 19, 22, 23, 24, 25, 28, 29, 30, 31, 34, 35, 36, 37, 40, 41, 42, 43 ] },
            { "width": "130px", "targets": [ 8, 14, 20, 26, 32, 38, 44 ] },
            { "width": "50px", "targets": [ 9, 15, 21, 27, 33, 39, 45 ] }
        ],           
    });
    $("#table_contribucion_length").hide();
    $("#table_contribucion_filter").hide();

    
    /*$("#exp-to-excel-canales").click(function(){
        location.href = "ExportToExcelCanales";
    })*/

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

    function calcularTotales() {
        var table = $('#table_contribucion').DataTable();
        var Farmacia_Cantidad = Farmacia_Costo = Farmacia_Venta = Farmacia_Contribucion = 0;
        var Cadena_Farmacia_Cantidad = Cadena_Farmacia_Costo = Cadena_Farmacia_Venta = Cadena_Farmacia_Contribucion = 0;
        var Mayorista_Cantidad = Mayorista_Costo = Mayorista_Venta = Mayorista_Contribucion = 0;
        var Cruz_Azul_Cantidad = Cruz_Azul_Costo = Cruz_Azul_Venta = Cruz_Azul_Contribucion = 0;
        var Institucion_Privada_Cantidad = Institucion_Privada_Costo = Institucion_Privada_Venta = Institucion_Privada_Contribucion = 0;
        var Institucion_Publica_Cantidad = Institucion_Publica_Costo = Institucion_Publica_Venta = Institucion_Publica_Contribucion = 0;
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

            // TOTAL DE TOTALES
            Total_Cantidad      += parseFloat(data.TOTAL_VENTAS_PACK) || 0;
            Total_Venta         += parseFloat(data.TOTAL_VENTAS_C$) || 0;
            Total_Costo         += parseFloat(data.TOTAL_COSTOS_C$) || 0;
            Total_Contribucion  += parseFloat(data.TOTAL_CONTRIBUCION_C$) || 0;

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

        // TOTAL DE TOTALES
        $('#Total_Cantidad').html(numeral(Total_Cantidad).format('0,0'));
        $('#Total_Promedio').html('C$ '+numeral(Total_Venta/Total_Cantidad).format('0,0'));
        $('#Total_Venta').html('C$ '+numeral(Total_Venta).format('0,0'));
        $('#Total_Costo').html('C$ '+numeral(Total_Costo).format('0,0'));
        $('#Total_Contribucion').html('C$ '+numeral(Total_Contribucion).format('0,0'));
        $('#Total_Margen').html(numeral((Total_Contribucion/Total_Venta)*100).format('0,0.00'));
    }

    $('#table_contribucion').DataTable().on('draw', function() {
        calcularTotales();
    });
    inicializaControlFecha();

    
});

function getDetalleArticulo(Articulos, Descripcion){
    $("#id_descripcion").html(Descripcion+` | `+ Articulos);
    $("#info1").show();
    $("#info2").show();

	$("#mdDetalleArt").modal('show');
    grafMensual(Articulos);
}

function getDetalleCanal(Articulos, Canal, Descripcion){
    $("#id_descripcion").html(Descripcion+` | `+ Articulos + ` | ` + Canal);
    $("#info1").hide();
    $("#info2").hide();
    
	$("#mdDetalleArt").modal('show');
    grafCanales(Articulos, Canal);
}

function grafCanales(Articulos, Canal){
    $.getJSON("get12Canales/" + Articulos + "/" + Canal, function(json) {
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


</script>