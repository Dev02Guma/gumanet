<script>
$(document).ready(function() {
    fullScreen();
    $('[data-toggle="tooltip"]').tooltip()
    var date    = new Date();
    var anio    = parseInt(date.getFullYear())
    var mes     = parseInt(date.getMonth()+1);
    var list_chk = {
                    'container-vm'      : 'Ventas del mes',
                    'container-rm'      : 'Recuperacion del mes',
                    'container-vb'      : 'Valorización de Bodegas',
                    'container-cv'      : 'Reporte YTD Montos C$',
                    'container-cc'      : 'Reporte YTD (Total de Items)',
                    'container-tc'      : 'Top 10 de Clientes',
                    'container-tp'      : 'Top 10 de Productos',
                    'container-vms'     : 'Comportamiento de ventas',
                    'container-cat'     : 'Ventas por categorias',
                    'container-rvts'    : 'Metas Ventas Reales' };

    var list_dash = '';

    //GUARDO VARIABLES EN COOKIES
    $(".content-graf .graf div").each(function() {
        name_class = $(this).attr('class');
        ( $.cookie( name_class )=='not_visible' || name_class=='container-vb' )?($('div.'+name_class).parent().hide()):($('div.'+name_class).parent().show());

        visibility = ( $.cookie( name_class )=='not_visible' )?'':'checked';

        if (name_class!='container-vb') {
            list_dash +=
            `<li class="">
                <div class="form-check">
                    <input class="dash-opc form-check-input" type="checkbox" `+visibility+` value="`+name_class+`" id="`+name_class+`">
                    <label class="form-check-label" for="`+name_class+`">
                        `+ ( list_chk[name_class] ) +`
                    </label>
                </div>
            </li>`
        }
    });

    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Home</li>`);

    $("#content-dash").append(`
        <p class="font-weight-bold ml-2">Ver en Dashboard</p>
        <ul class="list-group list-group-flush mt-3">`+list_dash+`</ul>`);

    var tipo = 1;
    if (typeof $.cookie('xbolsones') === 'undefined') {
        $("#customSwitch1").attr('checked', true);
        $.cookie( 'xbolsones' , 'yes_bolsones');
        tipo = 1;
    } else {
        if ( $.cookie( 'xbolsones' )=='yes_bolsones' ) {
            $("#customSwitch1").attr('checked', true);
            tipo = 1;
        } else if ( $.cookie( 'xbolsones' )=='not_bolsones' ) {
            $("#customSwitch1").attr('checked', false);
            tipo = 0;
        }
    }


    graf_Comportamiento_clientes_anual();

    graf_Comportamiento_sku_anual();
    graf_Ticket_promedio();

    grafVentasMensuales(tipo);
    grafRealVentasMensuales(tipo,0);
    fn_grafica_ventas_exportacion(tipo,0);
    reordenandoPantalla();
    actualizandoGraficasDashboard(mes, anio, tipo);

    
    Highcharts.setOptions({
        lang: {
            numericSymbols: [ 'k' , 'M' , 'B' , 'T' , 'P' , 'E'],
            decimalPoint: '.',
            thousandsSep: ','
        },
        colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
    function getAvg(dta) {
        const total = dta.reduce((acc, c) => acc + c, 0);        
        return total / dta.length;
    }

    function format_number(Numero,Formato){
        return numeral(Numero).format(Formato);
    }


    function promedio_comportamiento(Grafica,Categoria) {

        var Titulo = ""
        if (Grafica=="Clientes") {
            var Titulo = "Comportamiento de Cliente Anual"
            
            $("#id_row_cliente").show()
            $("#id_row_ticket").hide()
            $("#id_row_sku").hide()

            $("#id_tbl_clientes_no_facturados").show()
            

            var mes = ClientesAnuales.xAxis.categories.indexOf(Categoria.name) + 1;
            var anio = $('#opcAnio option:selected').val();

            //Promedio Comportamiento Anual de Clientes
            var avg_anterior_cliente_prom     = getAvg(ClientesAnuales.series[0].data);
            var avg_anterior_cliente_nombre   = ClientesAnuales.series[0].name

            var avg_actual_cliente_prom       = getAvg(ClientesAnuales.series[1].data);
            var avg_actual_cliente_nombre     = ClientesAnuales.series[1].name

            var dif_cliente = 0;

            $('#id_avg_anterior_cliente_prom').text(format_number(avg_anterior_cliente_prom,'0,0.00'));
            $('#id_avg_anterior_cliente_nombre').text(avg_anterior_cliente_nombre);
            $('#id_avg_actual_cliente_prom').text(format_number(avg_actual_cliente_prom,'0,0.00'));
            $('#id_avg_actual_cliente_nombre').text(avg_actual_cliente_nombre);

            dif_cliente  = (( avg_actual_cliente_prom / avg_anterior_cliente_prom ) - 1 ) * 100;
            cls_1 = (dif_cliente <0 )? 'text-danger font-weight-bolder':'text-success font-weight-bolder';
            dif_cliente_html = '<p class="font-weight-bolder '+cls_1+'">'+format_number(dif_cliente,'0,0.00')+'</p>';
            $('#id_dif_cliente').html(dif_cliente_html);  

            

            $('#tblClientes').DataTable({
                    "ajax":{
                        "url": "ClientesNoFacturados/"+mes+"/"+anio,
                        'dataSrc': '',
                    },
                    "destroy": true,
                    "info": true,
                    "lengthMenu": [[10,-1], [10,"Todo"]],
                    "language": {
                        "zeroRecords": "-",
                        "paginate": {
                            "first": "Primera",
                            "last": "Última ",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                        "info":       "Clientes que no han comprado del master",
                        "infoEmpty":  "",
                        "infoPostFix":    "",
                        "infoFiltered":   "",
                        "lengthMenu": "MOSTRAR _MENU_",
                        "emptyTable": "REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS DE FECHA",
                        "search": "BUSCAR"
                    },
                'columns': [
                    {"title": "CLIENTE",                "data": "CLIENTE"},
                    {"title": "NOMBRE",                 "data": "NOMBRE_CLIENTE"},
                    {"title": "FECHA ULTIMA COMPRA",    "data": "ULTIMA_COMPRA"},
                    {"title": "TIEMPO SIN COMPRAR",     "data": "Diferencia"},
                ],
                "columnDefs": [
                    {"className": "dt-center","targets": [0,2,3]},
                    {"className": "dt-right","targets": []},
                    {"className": "dt-left","targets": [1]},
                    {"visible": false,"searchable": false,"targets": []},
                    {"width": "5%","targets": [0,2,3]},
                    {"width": "10%","targets": [1]},
                ],
            });

            $("#tblClientes_length").hide();
            $("#tblClientes_filter").hide();

        } else if(Grafica=="SKUs") {
            var Titulo = "Comportamiento de SKU Anual"

            $("#id_row_cliente").hide()
            $("#id_row_ticket").hide()
            $("#id_row_sku").show()
            $("#id_tbl_clientes_no_facturados").hide()

            //Promedio Comportamiento de SKU Anuales
            const avg_anterior_sku_prom         = getAvg(SkusAnual.series[0].data);
            const avg_anterior_sku_nombre       = SkusAnual.series[0].name

            const avg_actual_sku_prom           = getAvg(SkusAnual.series[1].data);
            const avg_actual_sku_nombre         = SkusAnual.series[1].name    

            var dif_skus = 0;

            $('#id_avg_anterior_sku_prom').text(format_number(avg_anterior_sku_prom,'0,0.00'));
            $('#id_avg_anterior_sku_nombre').text(avg_anterior_sku_nombre);
            $('#id_avg_actual_sku_prom').text(format_number(avg_actual_sku_prom,'0,0.00'));
            $('#id_avg_actual_sku_nombre').text(avg_actual_sku_nombre);

            dif_skus  = (( avg_actual_sku_prom / avg_anterior_sku_prom ) - 1 ) * 100;
            cls_1 = (dif_skus <0 )? 'text-danger font-weight-bolder':'text-success font-weight-bolder';
            dif_sku_html = '<p class="font-weight-bolder '+cls_1+'">'+format_number(dif_skus,'0,0.00')+'</p>';
            $('#id_difs_skus').html(dif_sku_html);

        }else{
            var Titulo = "Comportamiento de Ticket Promedio Anual "
            $("#id_row_cliente").hide()
            $("#id_row_ticket").show()
            $("#id_row_sku").hide()
            $("#id_tbl_clientes_no_facturados").hide()

            //Promedio Comportamiento de Ticket Promedio Anual
            const avg_anterior_ticket_prom       = getAvg(TicketProm.series[0].data);
            const avg_anterior_ticket_nombre     = TicketProm.series[0].name

            const avg_actual_ticket_prom         = getAvg(TicketProm.series[1].data);
            const avg_actual_ticket_nombre       = TicketProm.series[1].name
            
            var dif_ticket = 0;
            
            $('#id_avg_anterior_ticket_prom').text("C$ " + format_number(avg_anterior_ticket_prom,'0,0.00'));
            $('#id_avg_anterior_ticket_nombre').text(avg_anterior_ticket_nombre);
            $('#id_avg_actual_ticket_prom').text("C$ " + format_number(avg_actual_ticket_prom,'0,0.00'));
            $('#id_avg_actual_ticket_nombre').text(avg_actual_ticket_nombre);

            dif_ticket = (( avg_actual_ticket_prom / avg_anterior_ticket_prom ) - 1 ) * 100;
            cls_1 = (dif_ticket <0 )? 'text-danger font-weight-bolder':'text-success font-weight-bolder';
            dif_ticket_html = '<p class="font-weight-bolder '+cls_1+'">'+format_number(dif_ticket,'0,0.00')+'</p>';
            $('#id_dif_ticket').html(dif_ticket_html);    
        }

        $('#titleModal-comportamiento').text(Titulo);

        $('#mdl_Promedios_Comportamiento').modal('show')
        

    }

    //GRAFICA VENTAS MENSUALES
    ventasMensuales = {
        chart: {
            type: 'spline',
            renderTo: 'grafVtsMes'
        },
        title: {
            text: `<p class="font-weight-bolder">Comportamiento de Venta Anual</p>`
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            title: {
                text: ''
            }                
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                },
                events: {
                    legendItemClick: function() {
                        return false;
                    }
                }
            },
        },
        tooltip: {},
        legend: {
            align: 'center',
            verticalAlign: 'top',
            borderWidth: 0
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                maxWidth: 500
                },
                chartOptions: {
                    legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

    ClientesAnuales = {
        chart: {
            type: 'spline',
            renderTo: 'grafClienteAnual'
        },
        title: {
            text: `<p class="font-weight-bolder">Comportamiento de Clientes Anual</p>`
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            title: {
                text: ''
            }                
        },
        
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                },
                events: {
                    legendItemClick: function() {
                        return false;
                    }
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                            promedio_comportamiento("Clientes",event.point.category)
                            
                        }
                    }
                }
            },
        },
        tooltip: {},
        legend: {
            align: 'center',
            verticalAlign: 'top',
            borderWidth: 0
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                maxWidth: 500
                },
                chartOptions: {
                    legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

    SkusAnual = {
        chart: {
            type: 'spline',
            renderTo: 'grafSkuAnual'
        },
        title: {
            text: `<p class="font-weight-bolder">Comportamiento de SKU Anual </p>`
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            title: {
                text: ''
            }                
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                },
                events: {
                    legendItemClick: function() {
                        return false;
                    }
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                            promedio_comportamiento("SKUs","")
                        }
                    }
                }
            },
        },
        tooltip: {},
        legend: {
            align: 'center',
            verticalAlign: 'top',
            borderWidth: 0
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                maxWidth: 500
                },
                chartOptions: {
                    legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

    TicketProm = {
        chart: {
            type: 'spline',
            renderTo: 'grafTicketProm'
        },
        title: {
            text: `<p class="font-weight-bolder">Comportamiento de Ticket Promedio Anual </p>`
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            title: {
                text: ''
            }                
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                },
                events: {
                    legendItemClick: function() {
                        return false;
                    }
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                            promedio_comportamiento("TicketProm","")
                        }
                    }
                }
            },
        },
        tooltip: {},
        legend: {
            align: 'center',
            verticalAlign: 'top',
            borderWidth: 0
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                maxWidth: 500
                },
                chartOptions: {
                    legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

    //GRAFICA METAS-REAL MENSUALES
    grafica_ventas_exportacion = {
        chart: {
            type: 'spline',
            renderTo: 'id_grafica_venta_exportacion'
        },
        title: {
            text: `<p class="font-weight-bolder">Ventas de Exportación</p>`
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            title: {
                text: ''
            }                
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                },
                events: {
                    legendItemClick: function() {
                        return false;
                    }
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                            window.location = "exportacion";
                            
                        }
                    }
                }
            },
        },
        tooltip: {},
        legend: {
            align: 'center',
            verticalAlign: 'top',
            borderWidth: 0
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                maxWidth: 500
                },
                chartOptions: {
                    legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

    //GRAFICA METAS-REAL MENSUALES
    ventasRealMensuales = {
        chart: {
            type: 'spline',
            renderTo: 'grafRealVentas'
        },
        title: {
            text: `<p class="font-weight-bolder">Venta Real Vs Meta</p>`
        },
        xAxis: {
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        yAxis: {
            title: {
                text: ''
            }                
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                },
                events: {
                    legendItemClick: function() {
                        return false;
                    }
                }
            },
        },
        tooltip: {},
        legend: {
            align: 'center',
            verticalAlign: 'top',
            borderWidth: 0
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                maxWidth: 500
                },
                chartOptions: {
                    legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

    //GRAFICA VENTAS POR CATEGORIAS
    ventasXCateg = {
        chart: {
            type: 'pie',
            renderTo: 'grafVtsXCateg',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Ventas por categorias'
        },
        subtitle: {},
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        tooltip: {
            pointFormat: "<p class='font-weight-bold' style='font-size:14px'>C${point.y:,.2f}<br><span class='font-weight-bold' style='color:green; font-size:14px'>({point.porc}%)</span></p>"
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 45,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            },
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function( ) {
                        return this.point.name+' '+ FormatPretty(this.y);
                    }
                }
            },
        },
        series: [{
            data: [],
        }],
    };

    // GRAFICA POR VENTAS DIARIAS DE RUTAS
    ventas_por_rutas = {
        chart: {
            type: 'pie',
            renderTo: 'id_grafica_pie_ventas_ruta',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: ''
        },
        subtitle: {},
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        tooltip: {
            pointFormat: "<p class='font-weight-bold' style='font-size:14px'>C${point.y:,.2f}<br><span class='font-weight-bold' style='color:green; font-size:14px'>({point.porc}%)</span></p>"
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 45,
                dataLabels: {
                    enabled: true,
                    format:  "<span class='font-weight-bold' style='color:black; font-size:10px'>{point.name}</span> "+"<span class='font-weight-bold' style='color:green; font-size:10px'> ({point.porc}%)</span>"
                }
            },
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function( ) {
                        return this.point.name+' '+ FormatPretty(this.y);
                    }
                }
            },
        },
        series: [{
            data: [],
        }],
    };

    //GRAFICA VENTAS
    ventas = {
        chart: {
            type: 'column',
            renderTo: 'grafVentas'
        },
        title: {
            text: 'Ventas del mes'
        },
        subtitle: {},
        xAxis: {
            type: 'category',
            visible: false
        },
        yAxis: {
            title: {
                text: ''
            },
            stackLabels: {
            enabled: true,
            formatter: function() {
                return FormatPretty(this.total);                
                }
            }
        },
        tooltip: {
            formatter: function() {
                return this.series.tooltipOptions.customTooltipPerSeries.call(this);
            }
        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                }
            },
        },
        series: [],
    };

    //GRAFICA RECUPERACION DEL MES
    recuperacionMes = {
        chart: {
            type: 'column',
            renderTo: 'grafRecupera'
        },
        title: {
            text: 'Recuperación del mes'
        },
        xAxis: {
            type: 'category',
            visible: false
        },
        yAxis: {
            title: {
                text: ''
            }

        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                }
            },
        },
        tooltip: {
        formatter: function() {
            return this.series.tooltipOptions.customTooltipPerSeries.call(this);
            }
        },
        series:[{
            colorByPoint: true,
            data: [],
            showInLegend: false
        }]  
    };

    //GRAFICA COMPARACION VENTAS
    comparacionMesesVentas = {
        chart: {
            type: 'column',
            renderTo: 'grafCompMontos'
        },
        title: {
            text: 'Reporte YTD Montos C$'
        },
        xAxis: {
            type: 'category',
            visible: false
        },
        yAxis: {
            title: {
                text: ''
            }

        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                }
            },
        },
        tooltip: {
        formatter: function() {
            return this.series.tooltipOptions.customTooltipPerSeries.call(this);
            }
        },
        series:[{
            colorByPoint: true,
            data: [],
            showInLegend: false
        }]  
    };

    //GRAFICA COMPARACION ITEMS
    comparacionMesesItems = {
        chart: {
            type: 'column',
            renderTo: 'grafCompCantid'
        },
        title: {
            text: 'Reporte YTD (Total de Items)'
        },
        xAxis: {
            type: 'category',
            visible: false
        },
        yAxis: {
            title: {
                text: ''
            }

        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return FormatPretty(this.y);
                    }
                }
            },
        },
        tooltip: {
        formatter: function() {
            return this.series.tooltipOptions.customTooltipPerSeries.call(this);
            }
        },
        series:[{
            colorByPoint: true,
            data: [],
            showInLegend: false
        }]  
    };

    //GRAFICA: VALORIZACION DE INVENTARIO
    val_bodega = {
        chart: {
            type: 'column',
            renderTo: 'grafBodega'
        },
        title: {
            text: 'Valorización de Bodegas'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                allowPointSelect: false,
                
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        if (this.y > 1000) {
                        return Highcharts.numberFormat(this.y / 1000, 1) + "K";
                        } else {
                        return this.y
                        }
                    }
                }
            }
        },
        tooltip: {
            pointFormat: '<span style="color:black"><b>C$ {point.y}</b></span>'
        },
        series:[{
            colorByPoint: true,
            data: [],
            showInLegend: false
        }]    
    };

    //GRAFICA: TOP 10 CLIENTES
    clientes = {
        chart: {
            type: 'column',
            renderTo: 'grafClientes'
        },
        title: {
            text: 'Top 10 Clientes'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                allowPointSelect: false,                
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        if (this.y > 1000) {
                        return Highcharts.numberFormat(this.y / 1000, 1) + "K";
                        } else {
                        return this.y
                        }
                    }
                }
            }
        },
        tooltip: {
            pointFormat: '<span style="color:{point.color}"><b>C${point.y:,.2f}</b>',
        },
        series:[{
            colorByPoint: true,
            data: [],
            showInLegend: false,
            cursor: 'pointer',
            point: {
                events: {
                    click: function(e) {
                        detalleVentasMes('clien', `[`+this.category+`] - `+this.name, this.category, 'ND');
                    }
                }
            }
        }]        
    }

    //GRAFICA: TOP 10 PRODUCTOS

    productos = {
        chart: {
            type: 'column',
            renderTo: 'grafProductos'
        },
        xAxis: {
            type: 'category',
            categories : []
        },
        title: {
            text: 'Top 10 Productos mas vendidos'
        },       
        legend: {
            enabled: false
        },
        yAxis: {
            title: {
                text: ''
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: ( // theme
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        plotOptions: {
            column: {
            stacking: 'normal',
            dataLabels: {
                enabled: false
            },
            
        },
        },
        plotOptions: {
        column: {
            stacking: 'normal',
                dataLabels: {
                    enabled: false
                },
                point: {
                events: {
                    click: function(e) {

                        
                        const _this = this.series.chart.options.series[0].data[this.x];    


                        var dta_send = [];

                        dta_send.push({
                            total_fact      : _this.Total,
                            unit_Fact       : _this.und,
                            unit_bonif      : _this.undBo,
                            prec_prom       : _this.dtavg,
                            cost_unit       : _this.dtcpm,
                            marg_contrib    : _this.dtmco,
                            porc_contrib    : _this.dtpco,
                            dttie           : _this.dttie,
                            dttub           : _this.dttub,
                            dttb2           : _this.dttb2,
                            dtpro           : _this.dtpro
                        })


                        detalleVentasMes('artic', `[`+_this.Descripcion+`] - `+_this.Articulo, dta_send, _this.Articulo);
                    
                    }
                }
            },
            
            }
        },
        tooltip: {
            
            formatter: function() {
                
                Info = this.series.chart.series[0].points[this.point.index];

                
                
                temporal =  Info.Descripcion + ' <br/>';
                temporal += '<span style="color:black">\u25CF</span> TOT. FACT. :<b>C$  ' + numeral(Info.Total).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> UNIT. FACT.: <b>  ' + numeral(Info.und).format('0,0.00')  + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> UNIT. BONIF: <b>  ' + numeral(Info.undBo).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> PREC. PROM. : <b>C$ ' + numeral(Info.dtavg).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> COST. PROM. UNIT. :<b>C$ ' + numeral(Info.dtcpm).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> CONTRIBUCION.  : <b>C$ ' +  numeral(Info.dtmco).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> % MARGEN BRUTO: <b>% ' + numeral(Info.dtpco).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> CANT. DISP. B002: <b> ' + numeral(Info.dttb2).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> CANT. DISP. UNDS. B002: <b> ' + numeral(Info.dttub).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> PROM. UNDS MES 2022: <b> ' + numeral(Info.dtpro).format('0,0.00') + ' </b><br/>';
                temporal += '<span style="color:black">\u25CF</span> CANT. DISP. MES: <b> ' + numeral(Info.dttie).format('0,0.00') + ' </b><br/>';


                

                return temporal;
            }
        }
    }



    grafiacas_productos_Diarios = {
        chart: {
            type: 'column',
            renderTo: 'grafVtsDiario',
        },      

        title: {
            text: 'Comportamiento Diario'
        },
        subtitle: {
            text: 'C$ 0.00',
            align: 'right',
            x: -10
        },
        xAxis: [{
            type: 'category'   
        }],
        legend: {
            enabled: false
        },
        yAxis:{
            title: {
                text: ''
            },
            plotLines: [{
                value: 0,
                color: 'red',
                dashStyle: 'shortdot',
                width: 3,
                zIndex: 10,
                label: {
                    text: 'Prom. Diario C$ ',
                    x: 0
                }
            },
            {
                value: 0,
                color: 'green',
                dashStyle: 'shortdot',
                width: 3,
                zIndex: 9,
                label: {
                    text: 'Prom. Diario C$ ',
                    x: 0
                }
            }]
            
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
            point: {
                events: {
                    click: function(e) {
                        detalles_ventas_diarias(this.name,this.mAVG);
                    }
                }
            },
        }]
    };   

});

var colors = ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];

$("#filterM_A").click( function(e) {
    var mes = $('#opcMes option:selected').val();
    var anio = $('#opcAnio option:selected').val();

    if ($('#customSwitch1').is(':checked')) {
        actualizandoGraficasDashboard(mes, anio, 1);
    }
    else {
        actualizandoGraficasDashboard(mes, anio, 0);
    }

});


$("#customSwitch1").change( function() {
    var mes = $('#opcMes option:selected').val();
    var anio = $('#opcAnio option:selected').val();
    
    graf_Comportamiento_clientes_anual();
    graf_Comportamiento_sku_anual();
    graf_Ticket_promedio();

    if ($(this).is(':checked')) {
        switchStatus = $(this).is(':checked');
        $.cookie( 'xbolsones' , 'yes_bolsones');
        grafVentasMensuales(1);
        grafRealVentasMensuales(1,0);
        fn_grafica_ventas_exportacion(1,0);
        actualizandoGraficasDashboard(mes, anio, 1);
    }
    else {
        switchStatus = $(this).is(':checked');
        $.cookie( 'xbolsones' , 'not_bolsones');
        grafVentasMensuales(0);
        grafRealVentasMensuales(0,0);
        fn_grafica_ventas_exportacion(0,0);
        actualizandoGraficasDashboard(mes, anio, 0);
    }
});

var val_bodega                  = {};
var clientes                    = {};
var ventas                      = {};
var recuperacionMes             = {};
var comparacionMesesVentas      = {};
var comparacionMesesItems       = {};
var ventasXCateg                = {};
var montoMetaVenta              = 0;
var montoMetaRecup              = 0;
function actualizandoGraficasDashboard(mes, anio, xbolsones) {
    $("#grafClientes, #grafProductos, #grafVentas, #grafBodega, #grafRecupera, #grafCompMontos, #grafCompCantid, #grafVtsXCateg, #grafVtsDiario")
    .empty()
    .append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);

    $('#tbody01')
    .empty()
    .append(`<tr><td colspan="2" class="text-primary">Cargando...</td></tr>`);

    $('#tbody02')
    .empty()
    .append(`<tr><td colspan="4" class="text-primary">Cargando...</td></tr>`);

    $.getJSON("dataGraf/"+mes+"/"+anio+"/"+xbolsones, function(json) {
        var dta = [];
        var title = [];
        var dta_avr = [];

        $.each(json, function (i, item) {

            etiqueta = (xbolsones)?'':'C$ ';

            switch (item['tipo']) {
                case 'dtaBodega':
                    dta = [];
                    title = [];
                    $.each(item['data'], function(i, x) {
                        dta.push({
                            name  : x['bodega'],
                            y     : x['data']
                        })

                        title.push(x['name'])
                    });                    
                    val_bodega.xAxis.categories = title;
                    val_bodega.series[0].data = dta;
                    val_bodega.subtitle = 'Datos hasta la fecha';
                    chart = new Highcharts.Chart(val_bodega);
                break;

                case 'dtaCliente':
                    dta = [];
                    title = [];

                    $('#btnclick').attr('onclick', 'detailAllClients('+xbolsones+')');

                    $.each(item['data'], function(i, x) {
                        dta.push({
                            name  : x['cliente'],
                            y     : x['data']
                        })

                        title.push(x['name'])
                    });

                    temporal = (xbolsones)?'<span style="color:{point.color}"><b>{point.y:,.2f}</b>':'<span style="color:{point.color}"><b>C${point.y:,.2f}</b>';
                    clientes.tooltip = {
                        pointFormat : temporal
                    }
                    clientes.xAxis.categories = title;
                    clientes.series[0].data = dta;
                    chart = new Highcharts.Chart(clientes);
                break;

                case 'dtaProductos':
                    dta                 =   [];
                    title               =   [];
                    Segmento            =   '';
                    SegFarmacia         =   []; 
                    SegMayoristas       =   [];
                    SegInstituciones    =   [];
                    Segmentos           =   [];
                    InfoSegmento        =   [];
                    isOne = 0;
                    
                            
                    $.each(item['data'], function(i, x) {
                        
                        InfoSegmento.push({
                            Articulo  : x['name'],
                            Descripcion : x['articulo'], 
                            Total     : x['data'], 
                            und   : (x['dtUnd'] > 0 ) ?  x['dtUnd'] : '  ',
                            undBo : (x['dtUndBo'] > 0 ) ?  x['dtUndBo'] : '  ',
                            dtavg :  x['dtAVG'],
                            dtcpm :  x['dtCPM'],
                            dtmco :  x['dtMCO'],
                            dtpco :  x['dtPCO'],

                            dttie :  x['dtTIE'],
                            dttb2 :  x['dtTB2'],
                            dttub :  x['dtTUB'],
                            dtpro :  x['dtPRO'],

                        })

                        title.push(x['name'])
                        SegFarmacia.push(parseFloat(x['M1']))
                        SegMayoristas.push(parseFloat(x['M2']))
                        SegInstituciones.push(parseFloat(x['M3']))
                    });  


                    // VALIDA EXISTENCIA DENTRO DEL ARREGLOS, Y DETERMINA SI ES 1 O MAS SEGMENTOS                    
                    $.each(SegInstituciones,function(){isOne+=parseFloat(this) || 0; });

                    if (isOne > 0) {
                

                    Segmento += '<option value="0">Todos</option>'+
                            '<option value="1">Farmacias</option>'+
                            '<option value="2">Mayoristas</option>'+
                            '<option value="3">Instituciones</option>';

                    Segmentos.push({
                            name :"InfoExtra",
                            data: InfoSegmento,
                            showInLegend: false
                            
                        },{
                            name :"Farmacia",
                            data: SegFarmacia
                        },{
                            name :"Mayoristas",
                            data: SegMayoristas
                        },{
                            name :"Instituciones",
                            data: SegInstituciones
                        }
                    );
                    } else {
                        
                        Segmento += '<option value="0">Todos</option>';

                        Segmentos.push({
                            name :"InfoExtra",
                            data: InfoSegmento,
                            showInLegend: false                            
                        },{
                            name :"Todos",
                            data: SegFarmacia
                        }
                    );
                    }


                    $("#opcSegmentos,#OpcSegmClt,#opc_seg_graf01,#opc_seg_graf02").empty().append(Segmento).selectpicker('refresh');


                    productos.xAxis.categories = title;
                    productos.series = Segmentos;
                    chart = new Highcharts.Chart(productos);
                    
                    

                break;

                case 'dtaVentasDiarias':

                    dta = [];
                    dta_avr = [];
                    title = [];
                    tmp_total = 0;
                    Tendencia = 1;
                    Day_Max = [];

                    var vVtsDiarias;

                    $.each(item['data'], function(i, x) {

                        tmp_total = tmp_total + parseFloat(x['data']);

                        dta.push({
                            name  :'Dia ' + x['articulo'],
                            mAVG  : x['dtAVG'],
                            dtavg : x['dtavg_'],
                            y     : x['data'], 
                            und   : (x['dtUnd'] > 0 ) ?  x['dtUnd']  : '  '
                        });

                        goal = x['dtAVG']
                        title.push(x['name']); 
                        Day_Max.push(x['data']); 
                    }); 

                    //temporal = (xbolsones)?'<span style="color:black"><b>{point.y}</b></span>' : '<span style="color:black"><b> C$ {point.y} {point.und}</b></span>';
                    moneda = (xbolsones)? "" :"C$ "
                    temporal = '<span style="color:black">\u25CF</span> VALOR :<b>C$  {point.y} </b><br/>';
                    temporal += '<span style="color:black">\u25CF</span> UNITS.: <b>  {point.und} </b><br/>';                   
                    grafiacas_productos_Diarios.tooltip = {
                        pointFormat : temporal
                    }
                    vVtsDiarias = numeral(tmp_total).format('0,0.00');
                    grafiacas_productos_Diarios.xAxis.categories = title;
                    grafiacas_productos_Diarios.subtitle.text = moneda + vVtsDiarias + " Total";
                    grafiacas_productos_Diarios.series[0].data = dta;


                    $("#id_ventas_diarias").html(moneda + vVtsDiarias)

                    Lblmoneda = (xbolsones)? "Bolsones Venta Local" :"Venta Local "
                    $("#id_lbl_ventas_diarias").html(Lblmoneda)

                    



                    
                    Tendencia = (tmp_total / dta.length ) 

                    var var_Day_Max = Math.max.apply(Math, Day_Max);

                    var_Day_Max = var_Day_Max + (var_Day_Max * 0.12);
                    
                    
                    chart = new Highcharts.Chart(grafiacas_productos_Diarios);

                    chart.yAxis[0].options.plotLines[0].value = goal;
                    chart.yAxis[0].options.plotLines[0].label.text = "P. D. M. C$ " + numeral(goal).format('0,0.00');

                    chart.yAxis[0].options.plotLines[1].value = Tendencia
                    chart.yAxis[0].options.plotLines[1].label.text = "P. D. T. C$ " + numeral(Tendencia ).format('0,0.00');
                    
                    chart.yAxis[0].update();
    
                break;

                case 'dtaVentasXCateg':
                    dta = [];
                    cate = '<option>TODAS LAS CATEGORIAS</option>';
                    
                    objVenta = item['data'].map(function (obj) {return obj.data;});
                    mTotal = objVenta.reduce(function (m, n) {return m + n;}, 0);

                    $.each(item['data'], function(i, x) {
                        dta.push({
                            name  : x['name'],
                            y     : x['data'],
                            porc  : numeral((parseFloat(x['data'])/parseFloat(mTotal))*100).format('0.00')
                        })
                        cate += `<option>`+x['name']+`</option>` 
                    });

                    $("#select-cate")
                    .empty()
                    .append(cate)
                    .selectpicker('refresh');

                    temporal = (xbolsones)?"<p class='font-weight-bold' style='font-size:14px'>{point.y:,.2f}<br><span class='font-weight-bold' style='color:green; font-size:14px'>({point.porc}%)</span></p>":"<p class='font-weight-bold' style='font-size:14px'>C${point.y:,.2f}<br><span class='font-weight-bold' style='color:green; font-size:14px'>({point.porc}%)</span></p>";

                    ventasXCateg.tooltip = {
                        pointFormat : temporal
                    }

                    ventasXCateg.subtitle = {text: 'Todas las  categorias'};
                    ventasXCateg.series[0].data = dta;
                    chart = new Highcharts.Chart(ventasXCateg);
                    $('.highcharts-title').append('<p>Vamos venga</p>')
                break;

                case 'dtaVentasMes':
                    dta = [];
                    title = [];
                    items = 0;
                    ventas.series = [];

                    if (item['data'].length>0) {
                        $.each(item['data'], function(i, x) {
                            if (x['name']=='items') {
                                items = parseFloat(x['data']);
                            } else {
                                dta.push({
                                    name  : x['name'],
                                    y     : x['data'],
                                })
                                title.push(x['name']);
                            }
                        });

                        var real_ = dta[0]['y'];
                        var meta_ = json[3].data[1].data;
                        var remanente = 0;

                        var porcentaje = (meta_!=0)? (real_/meta_) * 100 :0;

                        var dta_vst = [];


                        dta_vst.push({
                            dt_vst_real      : real_,
                            dt_vst_meta      : meta_,
                            dt_vst_porc      : porcentaje
                        })


                        if ( real_>meta_ && meta_>0 ) {
                            remanente = real_- meta_
                        } else {
                            remanente = 0;
                        }
                        porcentaje = `<p class="font-weight-bolder" style="font-size:14px">`+numeral(porcentaje).format('0,0.00')+`% de 100%</p>`;


                        
                        ventas.series[0]= {
                            name: 'Real',
                            type: 'column',
                            data: [real_],
                            tooltip: {
                                customTooltipPerSeries: function() {
                                    return '<b>'+etiqueta+numeral(real_).format('0,0.00')+'<br>N° de items '+items+'</b>';
                                }
                            },
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function(e) {
                                        detalleVentasMes('vent', 'Ventas del Mes', 'data',dta_vst);
                                    }
                                }
                            },
                            color: colors[0]
                        }

                        ventas.series[1]= {
                            name: 'Meta',
                            type: 'column',
                            data: [meta_],
                            tooltip: {
                                customTooltipPerSeries: function() {
                                    return '<b>C$ '+numeral(meta_).format('0,0.00')+'</b>';
                                }
                            },
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function(e) {
                                        detalleVentasMes('vent', 'Ventas del Mes', 'data',dta_vst);
                                    }
                                }
                            },
                            color: colors[1],
                            allowPointSelect: false,                
                            borderWidth: 0,
                        }
                        
                        ventas.subtitle = {text: porcentaje};
                        montoMetaVenta = numeral(json[3].data[1].data).format('0,0.00');
                    }

                    chart = new Highcharts.Chart(ventas);
                break;

                case 'dtaCompMesesVentas':
                    dtaVentasMes = item['data'];
                    title = [];
                    var lbl_dif         = 0;
                    var lbl_porcen01    = 0;
                    var lbl_porcen02    = 0;
                    lbl_toolTips = "";
                    $.each(item['data'], function(i, x) {
                        comparacionMesesVentas.series[i]= {
                            name: x['name'],
                            type: 'column',
                            data: [x['data']],
                            tooltip: {
                                customTooltipPerSeries: function() {


                                    mes_actual      = dtaVentasMes[0]['name'];
                                    anio_pasado     = dtaVentasMes[1]['name'];
                                    mes_pasado      = dtaVentasMes[2]['name'];

                                    m_actual        = parseFloat(dtaVentasMes[0]['data']);
                                    m_anio_pasado   = parseFloat(dtaVentasMes[1]['data']);
                                    m_mes_pasado    = parseFloat(dtaVentasMes[2]['data']); 

                                    if (m_anio_pasado>0) {
                                        lbl_dif = (m_actual-m_anio_pasado);
                                        lbl_porcen01 = (lbl_dif/m_anio_pasado)*100;
                                    }

                                    if (m_mes_pasado>0) {
                                        lbl_dif = (m_actual-m_mes_pasado);
                                        lbl_porcen02 = (lbl_dif/m_mes_pasado)*100;
                                    }

                                    text_monto_actual       = etiqueta+numeral(dtaVentasMes[0]['data']).format('0,0.00');            
                                    text_monto_anio_pasado  = etiqueta+numeral(dtaVentasMes[1]['data']).format('0,0.00');
                                    text_monto_mes_pasado   = etiqueta+numeral(dtaVentasMes[2]['data']).format('0,0.00');


                                                                        
                                    lbl_toolTips = '<span style="color:black">\u25CF</span> '+mes_actual + ' : <b>' + text_monto_actual + '</b><br>';
                                    lbl_toolTips += '<span style="color:black">\u25CF</span> '+anio_pasado + ' : <b>' + text_monto_anio_pasado + '</b><br>';
                                    lbl_toolTips += '<span style="color:black">\u25CF</span> DIF % : '+ '<b>' + numeral(lbl_porcen01).format('0.0') + '</b><br>';
                                    lbl_toolTips += '<span style="color:black">\u25CF</span> '+mes_pasado + ' : <b>' + text_monto_mes_pasado + '</b><br>';
                                    lbl_toolTips += '<span style="color:black">\u25CF</span> DIF % : <b>'+ numeral(lbl_porcen02).format('0.0') + '</b>';
                                    
                                    return lbl_toolTips;
                                }
                            },
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function(e) {
                                        detalleComparacionVentas(dtaVentasMes, 'vts')
                                    }
                                }
                            },
                            color: colors[i],
                            allowPointSelect: false,                
                            borderWidth: 0,
                        }
                        title.push(x['name'])
                    });
                    chart = new Highcharts.Chart(comparacionMesesVentas);
                break;

                case 'dtaCompMesesItems':
                    dtaItems = item['data'];
                    title = [];

                    var lbl_dif         = 0;
                    var lbl_porcen01    = 0;
                    var lbl_porcen02    = 0;
                    lbl_toolTips02 = "";
                    $.each(item['data'], function(i, x) {
                        comparacionMesesItems.series[i]= {
                            name: x['name'],
                            type: 'column',
                            data: [x['data']],
                            tooltip: {
                                customTooltipPerSeries: function() {

                                    mes_actual      = dtaItems[0]['name'];
                                    anio_pasado     = dtaItems[1]['name'];
                                    mes_pasado      = dtaItems[2]['name'];

                                    m_actual        = parseFloat(dtaItems[0]['data']);
                                    m_anio_pasado   = parseFloat(dtaItems[1]['data']);
                                    m_mes_pasado    = parseFloat(dtaItems[2]['data']); 

                                    if (m_anio_pasado>0) {
                                        lbl_dif = (m_actual-m_anio_pasado);
                                        lbl_porcen01 = (lbl_dif/m_anio_pasado)*100;
                                    }

                                    if (m_mes_pasado>0) {
                                        lbl_dif = (m_actual-m_mes_pasado);
                                        lbl_porcen02 = (lbl_dif/m_mes_pasado)*100;
                                    }

                                    text_monto_actual       = etiqueta+numeral(dtaItems[0]['data']).format('0,0');            
                                    text_monto_anio_pasado  = etiqueta+numeral(dtaItems[1]['data']).format('0,0');
                                    text_monto_mes_pasado   = etiqueta+numeral(dtaItems[2]['data']).format('0,0');


                                    lbl_toolTips02 = '<span style="color:black">\u25CF</span> '+mes_actual + ' : <b>' + text_monto_actual + '</b><br>';
                                    lbl_toolTips02 += '<span style="color:black">\u25CF</span> '+anio_pasado + ' : <b>' + text_monto_anio_pasado + '</b><br>';
                                    lbl_toolTips02 += '<span style="color:black">\u25CF</span> DIF % : <b>'+ numeral(lbl_porcen01).format('0.0') + '</b><br>';
                                    lbl_toolTips02 += '<span style="color:black">\u25CF</span> '+mes_pasado+ ' : <b>' + text_monto_mes_pasado + '</b><br>';
                                    lbl_toolTips02 += '<span style="color:black">\u25CF</span> DIF % : <b>'+ numeral(lbl_porcen02).format('0.0') + '</b>';
                                    
                                    return lbl_toolTips02;
                                }
                            },
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function(e) {
                                        detalleComparacionVentas(dtaItems, 'its')
                                    }
                                }
                            },
                            color: colors[i],
                            allowPointSelect: false,                
                            borderWidth: 0,
                        }
                        title.push(x['name'])
                    });
                    chart = new Highcharts.Chart(comparacionMesesItems);                    
                break;

                case 'dtaRecupera':
                    dta = item['data'];
                    title = [];
                    recuperacionMes.series = [];

                    if (item['data'].length>0) {
                        $.each(item['data'], function(i, x) {
                            recuperacionMes.series[i]= {
                                name: x['name'],
                                type: 'column',
                                data: [x['data']],
                                tooltip: {
                                    customTooltipPerSeries: function() {
                                        return x['name']+'<br><b>C$ '+numeral(x['data']).format('0,0.00')+'</b>';
                                    }
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function(e) {
                                            detalleVentasMes('recu', 'Recuperacion del Mes', 'ND', 'ND');
                                        }
                                    }
                                },
                                color: colors[i],
                                allowPointSelect: false,                
                                borderWidth: 0,
                            }

                            title.push(x['name'])
                        });

                        var real_ = dta[0]['data'];
                        var meta_ = dta[1]['data'];
                        var remanente = 0;

                        var porcentaje = (meta_!=0)? (real_/meta_) * 100 :0;
                        remanente = (real_>meta_)? real_- meta_ :0;
                        porcentaje = `<p class="font-weight-bolder" style="font-size:13px">`+numeral(porcentaje).format('0,0.00')+`% de 100%</p>`;
                        
                        recuperacionMes.subtitle = {text: porcentaje};
                        montoMetaRecup = numeral(meta_).format('0,0.00');
                    }

                    chart = new Highcharts.Chart(recuperacionMes);                    
                break;

                case 'dtaClientes':
                    var tbody = '';
                    var meta__ = real__ = clientesReal__ = clientesMeta__ = cumpl = 0;

                    if (item['data'].length>0) {
                        $.each(item['data'], function(i, x) {
                            temp = x['title'];

                            if (temp=='real') {
                                real__ = x['data'];
                            }else if (temp=='meta') {
                                meta__ = x['data'];
                            }else if (temp=='clientesMeta') {
                                clientesMeta__=x['data'];
                            }else if (temp=='clientesReal') {
                                clientesReal__=x['data'];
                            }

                            cumpl = (parseFloat(real__)/parseFloat(meta__))*100;
                            cumplC = (parseFloat(clientesReal__)/parseFloat(clientesMeta__))*100;

                        });

                        tbody = `<tr>
                                <th scope="row" style="font-size: 1rem!important">Meta</th>
                                <td class="text-right">
                                    <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(meta__).format('0,0.00') +`</p>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row" style="font-size: 1rem!important">Real</th>
                                <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(real__).format('0,0.00') +`</p>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row" style="font-size: 1rem!important">% Cumplimiento</th>
                                <td class="text-right">
                                    <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(cumpl).format('0.0') +` %</p>
                                    </td>
                                </tr>
                                <tr>
                                <th scope="row" style="font-size: 1rem!important">Clientes Meta</th>
                                <td class="text-right">
                                    <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(clientesMeta__).format('0.0') +`</p>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row" style="font-size: 1rem!important">Real Cliente</th>
                                <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(clientesReal__).format('0,0') +`</p>
                                </td>
                                </tr>
                                <tr>
                                <th scope="row" style="font-size: 1rem!important">% Cobertura</th>
                                <td class="text-right">
                                    <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(cumplC).format('0.0') + ` %</p>
                                </td>
                                </tr>`;

                        $('#tbody01').empty().append(tbody);
                    }else {
                        $("#crecimientoxruta").css('display', 'none');
                    }
                break;

                case 'dtaProyectos':





                    var tbody = '';
                    var metaGRP1__ = metaGRP2__ = metaGRP3__ = 0;
                    var realGRP1__ = realGRP2__ = realGRP3__ = 0;
                    var totalMETA__ = totalREAL__ = totalALC__ = 0;
                    

                    if (item['data'].length>0) {
                        $.each(item['data'], function(i, x) {
                            temp = x['proyecto'];

                            /*if ( temp.indexOf('1')!== -1 ) {
                                console.log()
                                metaGRP1__ = x['meta'];
                                realGRP1__ = x['real'];
                            }else if ( temp.indexOf('2')!== -1 ) {
                                metaGRP2__ = x['meta'];
                                realGRP2__ = x['real'];
                            }else if ( temp.indexOf('3') !== -1 ) {
                                metaGRP3__= x['meta'];
                                realGRP3__ = x['real'];
                            }*/

                            if ( temp=='Instituciones' ) {
                                metaGRP1__ = x['meta'];
                                realGRP1__ = x['real'];
                            }else if ( temp=='Mayoristas' ) {
                                metaGRP2__ = x['meta'];
                                realGRP2__ = x['real'];
                            }else if ( temp=='Farmacias' ) {
                                metaGRP3__= x['meta'];
                                realGRP3__ = x['real'];
                            }

                            totalMETA__ = totalMETA__ + parseFloat(x['meta']);
                            totalREAL__ = totalREAL__ + parseFloat(x['real']);


                            cumplGRP1 = (parseFloat(realGRP1__)/parseFloat(metaGRP1__))*100;
                            cumplGRP2 = (parseFloat(realGRP2__)/parseFloat(metaGRP2__))*100;
                            cumplGRP3 = (parseFloat(realGRP3__)/parseFloat(metaGRP3__))*100;
                            cumplTOTAL = (parseFloat(totalREAL__)/parseFloat(totalMETA__))*100;
                        });

                        tbody = `<tr>
                            <th scope="row" style="font-size: 1rem!important">Instituciones</th>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(metaGRP1__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(realGRP1__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(cumplGRP1).format('0.0') +` %</p>
                            </td>
                            </tr>
                            <tr>
                            <th scope="row" style="font-size: 1rem!important">Mayoristas</th>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(metaGRP2__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(realGRP2__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(cumplGRP2).format('0.0') +` %</p>
                            </td>
                            </tr>
                            <tr>
                            <th scope="row" style="font-size: 1rem!important">Farmacia</th>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(metaGRP3__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(realGRP3__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(cumplGRP3).format('0.0') +` %</p>
                            </td>
                            </tr>
                            <tr>
                            <th scope="row" style="font-size: 1rem!important">Total</th>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(totalMETA__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">C$ `+ numeral(totalREAL__).format('0,0') +`</p>
                            </td>
                            <td class="text-right">
                                <p class="font-weight-bolder" style="font-size: 1rem!important">`+ numeral(cumplTOTAL).format('0.0') +` %</p>
                            </td>
                            </tr>`;
                        $('#tbody02').empty().append(tbody);
                    }               

                break;

                case 'vtsDolares':
                    var val_vts_month = $("#id_ventas_diarias").text().replace(/[\ U,C$]/g, '')  
                    val_vts_month = parseFloat(val_vts_month) + parseFloat(item['data']['Local']);
                    
                    var inCordobas = "C$. " +numeral(item['data']['Local']).format('0,0.00')
                    
                    $('.has_standard_tooltip').attr('data-toggle', 'tooltip');
	                $('.vts-month-dolar').attr('title', inCordobas);  
                    $('[data-toggle="tooltip"]').tooltip();

                    if (xbolsones==1) {
                        $("#id_ventas_totales").html("Total Venta C$. 0.00")
                        $("#id_ventas_dolares").html("Venta Exportación $ 0.00")
                    } else {
                        $("#id_ventas_dolares").html("<a href='exportacion'> Venta Exportación $ " + numeral(item['data']['Dolar']).format('0,0.00')+"</a>")
                        $("#id_ventas_totales").html("Total Venta C$ " + numeral(val_vts_month).format('0,0.00'))
                    }

                break;

                default:
                alert('Ups... parece que ocurrio un error :(');
            }
        });
    });
}

var ventasRealMensuales = {};
function grafRealVentasMensuales(xbolsones,segmentos) {
    var temporal = "";
    $("#grafRealVentas")
    .empty()
    .append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);

    ventasRealMensuales.series = [];
    $.getJSON("dataRealVtsMensuales/"+xbolsones+"/"+segmentos, function(json) {
        var newseries;
        
        $.each(json, function (i, item) {
            temporal = (xbolsones)?'<span style="color:black"><b>{point.y:,.2f}</b></span>':'<span style="color:black"><b>C$ {point.y:,.2f}</b></span>';

            newseries = {};
            newseries.data = item['data'];
            newseries.name = item['title'];
            newseries.color = colors_[i];
            ventasRealMensuales.series.push(newseries);
            ventasRealMensuales.tooltip = {
                pointFormat : temporal
            }
            var chart = new Highcharts.Chart(ventasRealMensuales);
        })
    })
}


var grafica_ventas_exportacion = {};
function fn_grafica_ventas_exportacion(xbolsones,segmentos) {
    var temporal = "";
    $("#id_grafica_venta_exportacion")
    .empty()
    .append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);

            grafica_ventas_exportacion.series = [];
    $.getJSON("dtaVentaExportacion/"+xbolsones+"/"+segmentos, function(json) {
        var newseries;
        
        $.each(json, function (i, item) {
            temporal = (xbolsones)?'<span style="color:black"><b>{point.y:,.2f}</b></span>':'<span style="color:black"><b>TON {point.y:,.2f}</b></span>';

            newseries = {};
            newseries.data = item['data'];
            newseries.name = item['title'];
            newseries.color = colors_[i];
            grafica_ventas_exportacion.series.push(newseries);
            grafica_ventas_exportacion.tooltip = {
                pointFormat : temporal
            }
            var chart = new Highcharts.Chart(grafica_ventas_exportacion);
        })
    })
}

var ventasMensuales     = {};
var ClientesAnuales     = {};
var SkusAnual           = {};
var TicketProm          = {};

var colors_ = ['#407EC9', '#D19000', '#00A376', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];
function graf_Comportamiento_clientes_anual() {
    var temporal = "";
    $("#grafRealCliente").empty().append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando comportamiento SKU Anual...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);

    $("#anioAcumulado").empty();
    $("#porcentaje").empty();
    
    ClientesAnuales.series = [];
    elementCount = "[Cod. Cliente]";
    $.getJSON("dtaComportamientoAnuales/"+elementCount, function(json) {
    var newseries;
        var sumTotales = [];
        var temp = 0;
        var anio = 0;
        var date  = new Date();
        var anio_ = parseInt(date.getFullYear());
        var mes_ = parseInt(date.getMonth()+1);
        temporal = '<span style="color:black"><b>{point.y:,.0f}</b></span>';
        $.each(json, function (i, item) {
            
            if (anio != item['name']) {

                $.each(item['venta'], function(i_, item_) {
                    temp = temp + parseFloat(item_)
                })

                sumTotales.push({ 'anio':item['name'], 'suma':temp });
                
                anio = item['name'];
                temp = 0;
            }


            newseries = {};
            newseries.data = item['venta'];
            newseries.name = item['name'];
            newseries.color = colors_[i];
            ClientesAnuales.series.push(newseries);
            ClientesAnuales.tooltip = {
                pointFormat : temporal
            };
            
            var chart = new Highcharts.Chart(ClientesAnuales);
            
        }) 

        
    })    
        
    
}

function graf_Comportamiento_sku_anual() {
    var temporal = "";
    $("#grafSkuAnual").empty().append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);


    $("#anioAcumulado").empty();
    $("#porcentaje").empty();
    
    SkusAnual.series = [];
    elementCount = "ARTICULO";
    $.getJSON("dtaComportamientoAnuales/"+elementCount, function(json) {
        var newseries;
        var sumTotales = [];
        var temp = 0;
        var anio = 0;
        var date  = new Date();
        var anio_ = parseInt(date.getFullYear());
        var mes_ = parseInt(date.getMonth()+1);
        
        $.each(json, function (i, item) {
            temporal = '<span style="color:black"><b>{point.y:,.0f} Items </b></span>';
            if (anio != item['name']) {

                $.each(item['venta'], function(i_, item_) {
                    temp = temp + parseFloat(item_)
                })

                sumTotales.push({ 'anio':item['name'], 'suma':temp });
                
                anio = item['name'];
                temp = 0;
            }

            newseries = {};
            newseries.data = item['venta'];
            newseries.name = item['name'];
            newseries.color = colors_[i];
            SkusAnual.series.push(newseries);
            SkusAnual.tooltip = {
                pointFormat : temporal
            };
            var chart = new Highcharts.Chart(SkusAnual);
            
        })    
    })    
        
    
}
function graf_Ticket_promedio() {
    var temporal = "";
    $("#grafTicketProm").empty().append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);


    $("#anioAcumulado").empty();
    $("#porcentaje").empty();
    
    TicketProm.series = [];
    elementCount = "TICKETPROM";
    $.getJSON("dtaComportamientoAnuales/"+elementCount, function(json) {
        var newseries;
        var sumTotales = [];
        var temp = 0;
        var anio = 0;
        var date  = new Date();
        var anio_ = parseInt(date.getFullYear());
        var mes_ = parseInt(date.getMonth()+1);
        
        $.each(json, function (i, item) {
            temporal = 'C$ <span style="color:black"><b>{point.y:,.2f}  </b></span>';
            if (anio != item['name']) {

                $.each(item['venta'], function(i_, item_) {
                    temp = temp + parseFloat(item_)
                })

                sumTotales.push({ 'anio':item['name'], 'suma':temp });
                
                anio = item['name'];
                temp = 0;
            }

            newseries = {};
            newseries.data = item['venta'];
            newseries.name = item['name'];
            newseries.color = colors_[i];
            TicketProm.series.push(newseries);
            TicketProm.tooltip = {
                pointFormat : temporal
            };
            var chart = new Highcharts.Chart(TicketProm);
            
        })    
    })    
        
    
}
function grafVentasMensuales(xbolsones) {

    var temporal = "";
    $("#grafVtsMes")
    .empty()
    .append(`<div style="height:400px; background:#ffff; padding:20px">
                <div class="d-flex align-items-center">
                    <strong class="text-info">Cargando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);

    $(".divSpinner")
    .before(`<div class="spinner-border text-white float-right spinner-acum spinner-border-sm" role="status"></div>`);

    $("#anioAcumulado").empty();
    $("#porcentaje").empty();

    ventasMensuales.series = [];

    $.getJSON("dataVentasMens/"+xbolsones, function(json) {
        var newseries;
        var sumTotales = [];
        var temp = 0;
        var anio = 0;
        var porcentaje01 = 0;
        var porcentaje02 = 0;
        var porcentajeTo = 0;
        var anioAcumulado = porcentaje = acumuladoAnioAnte = porcentajeAnioAnte = '';
        var etiqueta = (xbolsones)?"":"C$ ";
        var date  = new Date();
        var anio_ = parseInt(date.getFullYear());
        var mes_ = parseInt(date.getMonth()+1);
        
        $.each(json, function (i, item) {
            temporal = (xbolsones)?'<span style="color:black"><b>{point.y:,.2f}</b></span>':'<span style="color:black"><b>C$ {point.y:,.2f}</b></span>';
            if (anio != item['name']) {

                $.each(item['venta'], function(i_, item_) {
                    temp = temp + parseFloat(item_)
                })

                sumTotales.push({ 'anio':item['name'], 'suma':temp });
                
                anio = item['name'];
                temp = 0;
            }

            newseries = {};
            newseries.data = item['venta'];
            newseries.name = item['name'];
            newseries.color = colors_[i];
            ventasMensuales.series.push(newseries);
            ventasMensuales.tooltip = {
                pointFormat : temporal
            };
            var chart = new Highcharts.Chart(ventasMensuales);
            
        })

        if (sumTotales.length > 0) {
            
            sumTotales.sort(function (a, b) { return b.anio - a.anio; });            
            
            anioActual = sumTotales[0].anio;
            montoAnioActual = parseFloat(sumTotales[0].suma);
            porcentaActual = ( montoAnioActual /  mes_  );

            anioPasado = sumTotales[1].anio;
            montoAnioPasado = parseFloat(sumTotales[1].suma);
            porcentaPasado = ( montoAnioPasado /  12  );

            lengthArray = sumTotales.length;
            if (lengthArray>2) {
                anioAntePasado = sumTotales[2].anio;
                montoAntePasado = parseFloat(sumTotales[2].suma);
                porcentaAntePasado = ( montoAntePasado /  12  );

                crecimiento3 = (( montoAnioPasado / montoAntePasado ) - 1 ) * 100;
                crecimiento4 = (( porcentaPasado / porcentaAntePasado ) - 1 ) * 100;

                st_3 = (crecimiento3<0)?` <i class="material-icons text-danger font-weight-bold" style="font-size:15px">arrow_downward</i>`:` <i class="material-icons text-success font-weight-bold" style="font-size:15px">arrow_upward</i>`;
                cls_3 = (crecimiento3<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;
                
                st_4 = (crecimiento4<0)?` <i class="material-icons text-danger font-weight-bold" style="font-size:15px">arrow_downward</i>`:` <i class="material-icons text-success font-weight-bold" style="font-size:15px">arrow_upward</i>`;
                cls_4 = (crecimiento4<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;

                acumuladoAnioAnte = `
                <div class="col-sm-5 text-center">                    
                    <p class="text-muted m-0">`+anioPasado+`</p>
                    <p class="font-weight-bolder" style="font-size: 1.2rem!important">C$`+numeral(montoAnioPasado).format('0,0.00')+`</p>
                </div>
                <div class="col-sm-5 text-center">
                    <p class="text-muted m-0 clsAnioAntePas">`+anioAntePasado+`</p>
                    <p class="font-weight-bolder" style="font-size: 1.2rem!important">C$`+numeral(montoAntePasado).format('0,0.00')+`</p>
                </div>
                <div class="col-sm-2 text-center">
                    <p class="text-muted m-0">% CREC.</p>
                    <p class="font-weight-bolder `+cls_3+`" style="font-size: 1.2rem!important">`+numeral(crecimiento3).format('0,0.00')+` `+st_3+`</p>
                </div>`;

                porcentajeAnioAnte = `
                <div class="col-sm-5 text-center">                    
                    <p class="text-muted m-0">`+anioPasado+`</p>
                    <p class="font-weight-bolder" style="font-size: 1.2rem!important">`+numeral(porcentaPasado).format('0,0.00')+`</p>
                </div>
                <div class="col-sm-5 text-center">
                    <p class="text-muted m-0">`+anioAntePasado+`</p>
                    <p class="font-weight-bolder" id="lblporcenanio3" style="font-size: 1.2rem!important">`+numeral(porcentaAntePasado).format('0,0.00')+`</p>
                </div>
                <div class="col-sm-2 text-center">
                    <p class="text-muted m-0">% CREC.</p>
                    <p class="font-weight-bolder `+cls_4+`" style="font-size: 1.2rem!important">`+numeral(crecimiento4).format('0,0.00')+` `+st_4+`</p>
                </div>`;
            }

            crecimiento1 = (( montoAnioActual / montoAnioPasado ) - 1 ) * 100;
            crecimiento2 = (( porcentaActual / porcentaPasado ) - 1 ) * 100;


            st_1 = (crecimiento1<0)?` <i class="material-icons text-danger font-weight-bold" style="font-size:15px">arrow_downward</i>`:` <i class="material-icons text-success font-weight-bold" style="font-size:15px">arrow_upward</i>`;
            cls_1 = (crecimiento1<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;

            st_2 = (crecimiento2<0)?` <i class="material-icons text-danger font-weight-bold" style="font-size:15px">arrow_downward</i>`:` <i class="material-icons text-success font-weight-bold" style="font-size:15px">arrow_upward</i>`;
            cls_2 = (crecimiento2<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;

            anioAcumulado = `
            <div class="col-sm-5 text-center">                    
                <p class="text-muted m-0">`+anioActual+`</p>
                <p class="font-weight-bolder" style="font-size: 1.2rem!important">C$`+numeral(montoAnioActual).format('0,0.00')+`</p>
            </div>
            <div class="col-sm-5 text-center">
                <p class="text-muted m-0">`+anioPasado+`</p>
                <p class="font-weight-bolder" style="font-size: 1.2rem!important">C$`+numeral(montoAnioPasado).format('0,0.00')+`</p>
            </div>
            <div class="col-sm-2 text-center">
                <p class="text-muted m-0">% CREC.</p>
                <p class="font-weight-bolder `+cls_1+`" style="font-size: 1.2rem!important">`+numeral(crecimiento1).format('0,0.00')+` `+st_1+`</p>
            </div>`;

            porcentaje = `
            <div class="col-sm-5 text-center">                    
                <p class="text-muted m-0">`+anioActual+`</p>
                <p class="font-weight-bolder" style="font-size: 1.2rem!important">`+numeral(porcentaActual).format('0,0.00')+`</p>
            </div>
            <div class="col-sm-5 text-center">
                <p class="text-muted m-0 lblanio1">`+anioPasado+`</p>
                <p class="font-weight-bolder" id="lblporcenanio1" style="font-size: 1.2rem!important">`+numeral(porcentaPasado).format('0,0.00')+`</p>
            </div>
            <div class="col-sm-2 text-center">
                <p class="text-muted m-0">% CREC.</p>
                <p class="font-weight-bolder `+cls_2+`" style="font-size: 1.2rem!important">`+numeral(crecimiento2).format('0,0.00')+` `+st_2+`</p>
            </div>`;

            $(".spinner-acum").remove()
            $("#anioAcumulado").append(anioAcumulado);
            $("#porcentaje").append(porcentaje);

            if (lengthArray>2) {
                $("#acumuladoAnioAnte").append(acumuladoAnioAnte);
                $("#porcentajeAnioAnte").append(porcentajeAnioAnte);
            }else {
                $("#cardAnioAntePasa").remove()
            }
        }
        
    })
}


$("#select-cate").change(function() {
    
    $.ajax({
        url: 'dataCate',
        type: 'post',
        data: {
            mes : $('#opcMes option:selected').val(),
            anio: $('#opcAnio option:selected').val(),
            cate: this.value
        },
        async: true,
        success: function(response) {
            dta = [];
            
            objVenta = response.map(function (obj) {return obj.data;});
            mTotal = objVenta.reduce(function (m, n) {return m + n;}, 0);
            
            $.each(response, function(i, x) {
                dta.push({
                    name  : x['name'],
                    y     : x['data'],
                    porc  : numeral((parseFloat(x['data'])/parseFloat(mTotal))*100).format('0.00')
                })
            });            
            ventasXCateg.subtitle = {text: cate};
            ventasXCateg.series[0].data = dta;
            chart = new Highcharts.Chart(ventasXCateg);
        }
    })
})


$("#opc_seg_graf01,#opc_seg_graf02").change( function() {
    mes         = $("#opcMes option:selected").val();         
    anio        = $("#opcAnio option:selected").val();  
    Segmento    = this.value;
    xbolsones   = 1;
    var id = $(this).attr('id');


    if (id=='opc_seg_graf01') {
        $("#grafVtsDiario")
        .empty()
        .append('<div style="height:400px; background:#ffff; padding:20px">'+
                    '<div class="d-flex align-items-center">'+
                        '<strong class="text-info">Cargando...</strong>'+
                        '<div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>'+
                    '</div>'+
                '</div>');

        $.getJSON("Grafselect/"+mes+"/"+anio+"/"+xbolsones+"/"+Segmento, function(json) { 

            dta = [];
            dta_avr = [];
            title = [];
            tmp_total = 0;

            Tendencia = 1;
            Day_Max = [];

            $.each(json, function(i, x) {

                tmp_total = tmp_total + parseFloat(x['data']);

                dta.push({
                    name  :'Dia ' + x['articulo'],
                    mAVG  : x['dtAVG'],
                    dtavg : x['dtavg_'],
                    y     : x['data'], 
                    und   : (x['dtUnd'] > 0 ) ?  x['dtUnd']  : '  '
                });
                
                goal = x['dtAVG']
                title.push(x['name']); 
                Day_Max.push(x['data']); 
            }); 

            //temporal = (xbolsones)?'<span style="color:black"><b>{point.y}</b></span>' : '<span style="color:black"><b> C$ {point.y} {point.und}</b></span>';
            temporal = '<span style="color:black">\u25CF</span> VALOR :<b>C$  {point.y} </b><br/>';
            temporal += '<span style="color:black">\u25CF</span> UNITS.: <b>  {point.und} </b><br/>';                   
            grafiacas_productos_Diarios.tooltip = {
                pointFormat : temporal
            }

            grafiacas_productos_Diarios.xAxis.categories = title;
            grafiacas_productos_Diarios.subtitle.text = "C$ " + numeral(tmp_total).format('0,0.00') + " Total";
            grafiacas_productos_Diarios.series[0].data = dta;

            Tendencia = (tmp_total / dta.length ) 

            var var_Day_Max = Math.max.apply(Math, Day_Max);

            var_Day_Max = var_Day_Max + (var_Day_Max * 0.05);


            chart = new Highcharts.Chart(grafiacas_productos_Diarios);

            chart.yAxis[0].options.plotLines[0].value = goal;
            chart.yAxis[0].options.plotLines[0].label.text = "P. D. M. C$ " + numeral(goal).format('0,0.00');

            chart.yAxis[0].options.plotLines[1].value = Tendencia
            chart.yAxis[0].options.plotLines[1].label.text = "P. D. T. C$ " + numeral(Tendencia ).format('0,0.00');

            chart.yAxis[0].update();

        });
        
    } else {
        grafRealVentasMensuales(xbolsones,Segmento)
        fn_grafica_ventas_exportacion(xbolsones,Segmento);
    }

    


})
$("#OpcSegmClt").change( function() { 
    mes         = $("#opcMes option:selected").val();         
    anio        = $("#opcAnio option:selected").val();  
    Segmento    = this.value;
    xbolsones   = 1;


    _dta = [];
    _title = [];


    chart = new Highcharts.Chart(clientes); 
    var seriesLength = chart.series.length;
    for(var i = seriesLength - 1; i > -1; i--) {
        chart.series[i].remove();
    }

    
    $.getJSON("graficaSegmentoCL/"+mes+"/"+anio+"/"+xbolsones+"/"+Segmento, function(json) {
        $.each(json, function (i, x) {            
            _dta.push({
                name  : x['cliente'],
                y     : x['data']
            })

            _title.push(x['name'])  
        })
        _temporal = (xbolsones)?'<span style="color:{point.color}"><b>{point.y:,.2f}</b>':'<span style="color:{point.color}"><b>C${point.y:,.2f}</b>';
        clientes.tooltip = {
            pointFormat : _temporal
        }
        clientes.xAxis.categories = _title;
        clientes.series[0].data = _dta;
        chart = new Highcharts.Chart(clientes);
    });
   

});
$("#opcSegmentos").change( function() {   
    mes         = $("#opcMes option:selected").val();         
    anio        = $("#opcAnio option:selected").val();  
    Segmento    = this.value;
    xbolsones   = 1;

    title           =   [];                    
    SegFarmacia     =   []; 
    SegMayoristas   =   [];
    SegInstituciones =   [];            
    Segmentos       =   [];
    InfoSegmento       =   [];

    
    chart = new Highcharts.Chart(productos); 
    var seriesLength = chart.series.length;
    for(var i = seriesLength - 1; i > -1; i--) {
        chart.series[i].remove();
    }



    $.getJSON("graficaSegmento/"+mes+"/"+anio+"/"+xbolsones+"/"+Segmento, function(json) {
        
        $.each(json, function (i, x) {
            InfoSegmento.push({
                Articulo  : x['name'],
                Descripcion : x['articulo'], 
                Total     : x['data'], 
                und   : (x['dtUnd'] > 0 ) ?  x['dtUnd'] : '  ',
                undBo : (x['dtUndBo'] > 0 ) ?  x['dtUndBo'] : '  ',
                dtavg :  x['dtAVG'],
                dtcpm :  x['dtCPM'],
                dtmco :  x['dtMCO'],
                dtpco :  x['dtPCO'],

                dttie :  x['dtTIE'],
                dttb2 :  x['dtTB2'],
                dttub :  x['dtTUB'],
            })

            title.push(x['name'])
            SegFarmacia.push(parseFloat(x['M1']))
            SegMayoristas.push(parseFloat(x['M2']))
            SegInstituciones.push(parseFloat(x['M3']))

        });
        if (Segmento==0) {
            Segmentos.push({
                            name :"InfoExtra",
                            data: InfoSegmento,
                            showInLegend: false
                            
                        },{
                            name :"Farmacia",
                            data: SegFarmacia
                        },{
                            name :"Mayoristas",
                            data: SegMayoristas
                        },{
                            name :"Instituciones",
                            data: SegInstituciones
                        }
                    );
        } else {
            if (Segmento==1) {
                Segmentos.push({
                            name :"InfoExtra",
                            data: InfoSegmento,
                            showInLegend: false
                            
                        },{
                            name :"Farmacia",
                            data: SegFarmacia
                        }
                    );OpcSegmClt
            } else {
                if (Segmento==2) {
                    Segmentos.push({
                            name :"InfoExtra",
                            data: InfoSegmento,
                            showInLegend: false
                            
                        },{
                            name :"Mayoristas",
                            data: SegMayoristas
                        }
                    );
                } else {
                    if (Segmento==3) {
                        Segmentos.push({
                            name :"InfoExtra",
                            data: InfoSegmento,
                            showInLegend: false
                            
                        },{
                            name :"Instituciones",
                            data: SegInstituciones
                        }
                    ); 
                    }
                    
                }
                
            }
            
        }
        


        productos.xAxis.categories = title;
        productos.series = Segmentos;
        chart = new Highcharts.Chart(productos); 

    });
    
});



var tableActive='';
function detalles_ventas_diarias($dia,$mAVG)
{
    $('#title-page-tem').addClass('text-uppercase').text("Detalle de venta del dia ");
    $("#page-details").toggleClass('active');  

    $( "#id_div_titulo_Ventas_Rutas").removeClass( "table-responsive" ).addClass( "table" );

    mes         = $("#opcMes option:selected").val();    
    mes_name    = $("#opcMes option:selected").text();    
    anio        = $("#opcAnio option:selected").val();    
    pageName    = 'Dashboard';

    ElSegmento  = $("#opc_seg_graf01 option:selected").val();    


    FechaFiltrada = `Mostrando registros del `+$dia+` de `+mes_name + ' ' + anio;    
    $dia = $dia.replace(/[^\d.-]/g, '');
    $("#fechaFiltrada").text(FechaFiltrada);
    $('#filterDtTemp').val('');

    $("#id_detalles_articulos").hide();

    $("#cjVentasFacturas").show();
    $("#cjVentas").hide();


    
    $("#cjRutVentasRutas").show();
    $("#id_grafica_pie_ventas_ruta").show();
    $("#cjRutVentas").hide();



    $('#MontoReal').text('Cargando...');
    $('#cumplMeta').text('Cargando...');
    $('#cumplMetaContent').show();
    $("#montoMetaContent").show();
    $("#txtMontoReal").show();
    $("#MontoReal").show();
    
    $("#cjRecuperacion").hide();
    $("#cjCliente").hide();
    $("#cjArticulo").hide();
    
    $("#MontoMeta").text('C$ '+ numeral($mAVG).format('0,0.00') );
    $("#cantRowsDtTemp selected").val("50");
    

            $("#dtVentaRuta").dataTable({
                "scrollX": false,
                "ordering": false,
                "ajax":{
                    "url": "detallesdia/"+$dia+"/"+mes+"/"+anio+"/"+ElSegmento,
                    'dataSrc': '',
                },
                
                "destroy" : true,
                "info":    false,
                "lengthMenu": [[20,-1], [20,"Todo"]],
                "language": {
                    "zeroRecords": "Cargando...",
                    "paginate": {
                        "first":      "Primera",
                        "last":       "Última ",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "lengthMenu": "MOSTRAR _MENU_",
                    "emptyTable": "NO HAY DATOS DISPONIBLES",
                    "search":     "BUSCAR"
                },
                'columns': [
                    { "title": "Ruta",              "data": "RUTA" },
                    { "title": "Nombre",            "data": "VENDE" },
                    { "title": "Real Vtas.",        "data": "REALE" },
                ],
                "columnDefs": [
                    {"className": "dt-back-unit", "targets": [ 2 ]},
                    {"className": "dt-left", "targets": [ 1 ]},
                    {"className": "dt-center", "targets": [ 0 ]}
                    
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                        dta_pie_rutas = [];
                        var api = this.api(), data;
                        
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                            i.replace(/[^0-9.]/g, '')*1 :
                            typeof i === 'number' ?
                            i : 0;
                        };

                        total = api.column( 2 ).data().reduce( function (a, b) 
                        {
                            return intVal(a) + intVal(b);
                        }, 0 );

                        tmp = parseFloat($('#MontoMeta').text().replace(/[\ U,C$]/g, ''));
                        cump = (tmp>0)?(( parseFloat(total) / tmp ) * 100):0;     

                        api.rows().data().each(function (value) {
                            varMonto = value.REALE.replace(/[\ U,C$]/g, '');
                            dta_pie_rutas.push({
                                name  : value.VENDE,
                                y     : parseFloat(varMonto),
                                porc  : numeral((parseFloat(varMonto)/parseFloat(total))*100).format('0.00')
                            })
                        })

                        

                        $('#cumplMeta').text(numeral(cump).format('0.00')+'%');
                        $('#MontoReal').text('C$ '+ numeral(total).format('0,0.00'));
                        
                        
                        ventasXCateg.tooltip = {
                            pointFormat : ''
                        }
                        ventasXCateg.subtitle = {text: 'Todas las  categorias'};
                        ventas_por_rutas.series[0].data = dta_pie_rutas ;
                        chart = new Highcharts.Chart(ventas_por_rutas);
                        Todos_Los_Items_Diario($dia,mes,anio,ElSegmento);
                    },
                
            });
            
            $('#txtMontoReal').text('Total real ventas');
            $('#txtMontoMeta').text('Total Venta Diario');

            $("#dtVentaRuta_length").hide();
            $("#dtVentaRuta_filter,#dtVentaRuta_paginate").hide();
            
            $("#id_div_detalles_vendedores").hide();
            




    var st = $('#sidebar-menu-left').hasClass('active');

    if (st) {        
        $('#page-details').css('width','100%')
    }


}

var tableActive='';
function detalleVentasMes(tipo, title, cliente, articulo) {

    $("#cjRutVentasRutas").hide();
    $("#id_grafica_pie_ventas_ruta").hide();
    $("#cjRutVentas").show();

    $("#id_detalles_articulos").hide();

    $('#title-page-tem')
    .addClass('text-uppercase')
    .text(title);
    $("#page-details").toggleClass('active');
    mes         = $("#opcMes option:selected").val();
    mesNombre   = $("#opcMes option:selected").text();
    anio        = $("#opcAnio option:selected").val();    
    pageName    = 'Dashboard';

    FechaFiltrada = `Mostrando registros de `+mesNombre+` de `+anio;
    $("#fechaFiltrada").text(FechaFiltrada);
    $('#filterDtTemp').val('');


    switch(tipo) {
        case 'vent':
            $('#MontoReal').text('Cargando...');
            $('#cumplMeta').text('Cargando...');
            $('#cumplMetaContent').show();
            $("#montoMetaContent").show();
            $("#cjVentas").show();
            $("#cjRecuperacion").hide();
            $("#cjCliente").hide();
            $("#cjArticulo").hide();
            $("#cjRutVentas").show();
            tableActive = `#dtTotalXRutaVent`;
            $("#MontoMeta").text('C$ '+montoMetaVenta);
            $("#cantRowsDtTemp selected").val("5");
            
           /* $.ajax({// calcula el total real neto
                    url: "detalles/"+tipo+"/"+mes+"/"+anio+"/ND/ND/ND",
                    type: "GET",
                    async: true,
                    success: function(res) {
                        tmp = parseFloat($('#MontoMeta').text().replace(/[\ U,C$]/g, ''))
                        $('#MontoReal').empty().text('C$ '+ numeral(res[0]['MONTO']).format('0,0.00'));

                        cump = (tmp>0)?(( parseFloat(res[0]['MONTO']) / tmp ) * 100):0;
                        $('#cumplMeta').text(numeral(cump).format('0.00')+'%');

                    }
                });*/

                $('#MontoReal').empty().text('C$ '+ numeral(articulo[0].dt_vst_real).format('0,0.00'));
                $('#cumplMeta').text(numeral(articulo[0].dt_vst_porc).format('0.00')+'%');
            //Tabla Ventas del mes dashboard
            $(tableActive).dataTable({
                responsive: true,
                "autoWidth":false,
                "ajax":{
                    "url": "unidadxProd/"+mes+"/"+anio,
                    'dataSrc': '',
                },
                "destroy" : true,
                "info":    false,
                "lengthMenu": [[30,50,-1], [30,100,"Todo"]],
                "language": {
                    "zeroRecords": "Cargando...",
                    "paginate": {
                        "first":      "Primera",
                        "last":       "Última ",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "lengthMenu": "MOSTRAR _MENU_",
                    "emptyTable": "NO HAY DATOS DISPONIBLES",
                    "search":     "BUSCAR"
                },
                'columns': [
                    { "title": "Ruta",              "data": "RUTA" },
                    { "title": "Nombre",            "data": "VENDE" },
                    { "title": "Meta Units.",       "data": "METAU" },
                    { "title": "Real Units.",       "data": "REALU" },
                    { "title": "% Cumpl. Units.",   "data": "DIFU" },
                    { "title": "Meta Vtas.",        "data": "METAE" },
                    { "title": "Real Vtas.",        "data": "REALE" },
                    { "title": "% Cumpl. Vtas.",    "data": "DIFE" },
                ],
                "columnDefs": [
                    {"className": "dt-left", "targets": [ 1 ]},
                    //{"className": "dt-right", "targets": [ 2, 3, 4, 5, 6, 7 ]},
                    {"className": "dt-back-unit", "targets": [ 2, 3, 4 ]},
                    {"className": "dt-back-vtas", "targets": [ 5, 6, 7 ]},
                    {"className": "dt-center", "targets": [ 0 ]}
                ],
                
            });
            $('#txtMontoReal').text('Total real ventas');
            $('#txtMontoMeta').text('Total meta venta');
            $("#id_div_detalles_vendedores").show();
            break;
        case 'recu':
            
            $('#cumplMetaContent').show();
            $("#cjVentas").hide();
            $("#cjRutVentas").hide();
            $("#cjCliente").hide();
            $("#cjArticulo").hide();
            $("#montoMetaContent").show();
            $("#MontoMeta").text('C$ '+montoMetaRecup);
            $("#cantRowsDtTemp selected").val("5");
            


            companny_id = $("#companny_id").text();

            if (companny_id == '1' || companny_id == '4') {
                $("#cjRecuperacion").show();
                $("#cjRecu_GumaPharma").hide();

                tableActive = `#dtRecuperacion`;

                var route="getRecuRowsByRoutes/"+mes+"/"+anio+"/"+pageName;
                var metodo = 'GET';
                $(tableActive).dataTable({
                    responsive: true,
                    "autoWidth":false,
                    'ajax':{
                        'url':route,
                        'method':metodo,
                        'async' : false,
                        'dataSrc': '',
                    },        
                    "destroy" : true,
                    "info":    false,
                    "lengthMenu": [[5,10,15,-1], [5,10,15,"Todo"]],
                    "language": {
                        "zeroRecords": "Cargando...",
                        "paginate": {
                            "first":      "Primera",
                            "last":       "Última ",
                            "next":       "Siguiente",
                            "previous":   "Anterior"
                        },
                        "lengthMenu": "MOSTRAR _MENU_",
                        "emptyTable": "NO HAY DATOS DISPONIBLES",
                        "search":     "BUSCAR"
                    },
                    'columns': [
                        { "title": "Ruta",      "data": "RECU_RUTA" },
                        { "title": "Vendedor", "data": "RECU_VENDE" },
                        { "title": "Meta",      "data": "RECU_META" },
                        { "title": "Recup. Crédito",      "data": "RECU_CREDITO" },
                        { "title": "Recup. Contado","data": "RECU_CONTADO" },
                        { "title": "Recup. Total",      "data": "RECU_TOTAL" },
                        { "title": "% Cump. Crédito",      "data": "RECU_CUMPLIMIENTO" },
                        //{ "title": 'Opciones',"data": "RECU_OPCIONES" },
                    ],
                    "columnDefs": [
                        {"width":"20%","targets":[1]},
                        {"width":"15%","targets":[2, 3, 4, 5, 6]},
                        {"className": "dt-center", "targets":[0, 1, 2, 3, 4, 5, 6]}
                    ],
                    "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                            i.replace(/[^0-9.]/g, '')*1 :
                            typeof i === 'number' ?
                            i : 0;
                        };
                        total = api
                        .column( 3 )
                        .data()
                        .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                        }, 0 );
                        tmp = parseFloat($('#MontoMeta').text().replace(/[\ U,C$]/g, ''));
                        cump = (tmp>0)?(( parseFloat(total) / tmp ) * 100):0;
                        $('#cumplMeta').text(numeral(cump).format('0.00')+'%');
                        $('#MontoReal').text('C$'+ numeral(total).format('0,0.00'));
                    },
                    "fnInitComplete": function () {
                        
                    }
                    
                });

                $('#dtIntroRecup_length').hide();//Ocultar select que muestra cantidad de registros por pagina
                $('#dtIntroRecup_filter').hide();//Esconde input de filtro de tabla por texto escrito

                $('#txtMontoReal').text('Total real recup. crédito');
                $('#txtMontoMeta').text('Total meta recuperacion');

            } else if (companny_id == '2') {


                $("#cjRecuperacion").hide();
                $("#cjRecu_GumaPharma").show();

                tableActive = `#dtRecu_GumaPharma`;

                var route="getRecuRowsByRoutes/"+mes+"/"+anio+"/"+pageName;
                var metodo = 'GET';
                $(tableActive).dataTable({
                    responsive: true,
                    "autoWidth":false,
                    'ajax':{
                        'url':route,
                        'method':metodo,
                        'async' : false,
                        'dataSrc': '',
                    },        
                    "destroy" : true,
                    "info":    false,
                    "lengthMenu": [[5,10,15,-1], [5,10,15,"Todo"]],
                    "language": {
                        "zeroRecords": "Cargando...",
                        "paginate": {
                            "first":      "Primera",
                            "last":       "Última ",
                            "next":       "Siguiente",
                            "previous":   "Anterior"
                        },
                        "lengthMenu": "MOSTRAR _MENU_",
                        "emptyTable": "NO HAY DATOS DISPONIBLES",
                        "search":     "BUSCAR"
                    },
                    'columns': [
                        { "title": "Ruta",      "data": "RECU_RUTA" },
                        { "title": "Vendedor", "data": "RECU_VENDE" },
                        { "title": "Meta",      "data": "RECU_META" },
                        { "title": "Recuperación",      "data": "RECU_TOTAL" },
                        { "title": "% Cumplimiento",      "data": "RECU_CUMPLIMIENTO" }
                        //{ "title": 'Opciones',"data": "RECU_OPCIONES" },
                    ],
                    "columnDefs": [
                        {"width":"20%","targets":[0,1]},
                        {"width":"15%","targets":[2, 3, 4]},
                        {"className": "dt-center", "targets":[0, 1, 2, 4]},
                        {"className": "dt-right", "targets":[3]}
                    ],
                    "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                            i.replace(/[ \C$,]/g, '')*1 :
                            typeof i === 'number' ?
                            i : 0;

                        };
                        total =api
                        .column( 3 )
                        .data()
                        .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                        }, 0 );
                
                        tmp = parseFloat($('#MontoMeta').text().replace(/[\ U,C$]/g, ''));
                        cump = (tmp>0)?(( parseFloat(total) / tmp ) * 100):0;
                        $('#cumplMeta').text(numeral(cump).format('0.00')+'%');
                        $('#MontoReal').text('C$'+ numeral(total).format('0,0.00'));
                    },
                    "fnInitComplete": function () {
                        
                    }
                    
                });

                $('#dtRecu_GumaPharma_length').hide();//Ocultar select que muestra cantidad de registros por pagina
                $('#dtRecu_GumaPharma_filter').hide();//Esconde input de filtro de tabla por texto escrito

                $('#txtMontoReal').text('Total real recuperación');
                $('#txtMontoMeta').text('Total meta recuperacion');
            }



        break;
        case 'clien':
            $("#cjRecuperacion").hide();
            $("#cjVentas").hide();
            $('#cumplMetaContent').hide();
            $("#cjRutVentas").hide();
            $("#cjArticulo").hide();
            $("#montoMetaContent").hide()
            $("#cjCliente").show();
            tableActive = `#dtCliente`;
            
            $(tableActive).dataTable({
                responsive: true,
                "autoWidth":false,
                "ajax":{
                    "url": "detalles/"+tipo+"/"+mes+"/"+anio+"/"+cliente+"/ND/ND",
                    'dataSrc': '',
                },
                "destroy" : true,
                "info":    false,
                "lengthMenu": [[5,10,20,50,-1], [20,30,50,100,"Todo"]],
                "language": {
                    "zeroRecords": "Cargando...",
                    "paginate": {
                        "first":      "Primera",
                        "last":       "Última ",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "lengthMenu": "MOSTRAR _MENU_",
                    "emptyTable": "NO HAY DATOS DISPONIBLES",
                    "search":     "BUSCAR"
                },
                'columns': [
                    { "title": "Articulo",      "data": "ARTICULO" },
                    { "title": "Descripcion",   "data": "DESCRIPCION" },
                    { "title": "Cantidad",      "data": "CANTIDAD" },
                    { "title": "Total",         "data": "TOTAL" }
                ],
                "columnDefs": [
                    {"className": "dt-right", "targets": [ 2, 3 ]},
                    {"className": "dt-center", "targets": [ 0 ]}
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    total = api
                        .column( 3 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    $('#MontoReal').text('C$'+ numeral(total).format('0,0.00'));
                    $('#txtMontoReal').text('Total facturado');


                    $('#MontoMeta').text('');
                    $('#txtMontoMeta').text('');
                }
            });
        break;
        case 'artic':

            $('#MontoMeta').text('Cargando...');
            $('#txtMontoMeta').text('Cargando...');

            
            
            $("#id_detalles_articulos").show();

            $("#cjRecuperacion").hide();
            $('#cumplMetaContent').show();
            $("#cjVentas").hide();
            $("#cjRutVentas").hide();
            $("#cjCliente").hide();
            $("#montoMetaContent").show()
            $("#cjArticulo").show();
            tableActive = `#dtArticulo`;
            
            $(tableActive).dataTable({
                responsive: true,
                "autoWidth":false,
                "ajax":{
                    "url": "detalles/"+tipo+"/"+mes+"/"+anio+"/ND/"+articulo+"/ND",
                    'dataSrc': '',
                },
                "destroy" : true,
                "info":    false,
                "lengthMenu": [[20,50,-1], [20,30,50,100,"Todo"]],
                "language": {
                    "zeroRecords": "Cargando...",
                    "paginate": {
                        "first":      "Primera",
                        "last":       "Última ",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "lengthMenu": "MOSTRAR _MENU_",
                    "emptyTable": "NO HAY DATOS DISPONIBLES",
                    "search":     "BUSCAR"
                },
                'columns': [
                    { "title": "Cliente",               "data": "CLIENTE" },
                    { "title": "Nombre",                "data": "NOMBRE" },
                    { "title": "Cantidad Facturada",    "data": "CANTIDAD" },
                    { "title": "Cantidad Bonificada",   "data": "CANTIDAD_BONI" },
                    { "title": "Total",                 "data": "TOTAL" }
                ],
                "columnDefs": [
                    {"className": "dt-right", "targets": [ 2, 3,4 ]},
                    {"className": "dt-center", "targets": [ 0 ]},
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    total = api
                        .column( 3 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    total_und = api
                        .column( 2 )
                        .data()
                        .reduce( function (a, b) {                            
                            return intVal(a) + intVal(b);
                        }, 0 );



                    $('#id_detall_prec_prom').text('C$ '+ numeral(cliente[0].prec_prom).format('0,0.00'));  
                    $('#id_detall_cost_unit').text('C$ '+ numeral(cliente[0].cost_unit).format('0,0.00'));  
                    $('#id_detall_marg_contrib').text('C$ '+ numeral(cliente[0].marg_contrib).format('0,0.00'));  
                    $('#id_detall_porc_contrib').text(numeral(cliente[0].porc_contrib).format('0,0.00'));  


                    $('#id_disp_cant').empty().text(numeral(cliente[0].dttb2).format('0,0.00'));
                    $('#id_disp_unds').empty().text(numeral(cliente[0].dttub).format('0,0.00'));
                    $('#id_disp_meses').empty().text(numeral(cliente[0].dttie).format('0,0.00'));
                    $('#id_prom_mes_actual').empty().text(numeral(cliente[0].dtpro).format('0,0.00'));

                    $('#txtMontoMeta').text('TOT. FACT. :');
                    $('#MontoMeta').text(numeral('C$ '+ cliente[0].total_fact).format('0,0.00'));
                    
                    $('#MontoReal').text(numeral(cliente[0].unit_Fact).format('0,0.00'));
                    $('#txtMontoReal').text('UNIT. FACT. :');

                    $('#cumplMeta').text(numeral(cliente[0].unit_bonif).format('0,0.00'));
                    $('#id_detall_unit_bonif').text('UNIT. BONIF:');

                    
                }
            });
        break;
        default:
        mensaje("Ups... algo ha salido mal")
    }
    $("#dtVentas_length, #dtRecuperacion_length, #dtCliente_length, #dtTotalXRutaVent_length, #dtArticulo_length").hide();
    $("#dtVentas_filter, #dtRecuperacion_filter, #dtCliente_filter, #dtTotalXRutaVent_filter, #dtArticulo_filter").hide();

    var st = $('#sidebar-menu-left').hasClass('active');

    if (st) {        
        $('#page-details').css('width','100%')
    }
}

function getDetalleVenta(mes, anio, metau, realu, metae, reale, ruta, nombre) {
    $('#dtVentas').dataTable({
        responsive: true,
        "autoWidth":false,
        "ajax":{
            "url": "detallesVentasRuta/"+mes+"/"+anio+"/"+ruta,
            'dataSrc': '',
        },
        "destroy" : true,
        "info":    false,
        "lengthMenu": [[5,10,20,50,-1], [20,30,50,100,"Todo"]],
        "language": {
            "zeroRecords": "Cargando...",
            "paginate": {
                "first":      "Primera",
                "last":       "Última ",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "lengthMenu": "MOSTRAR _MENU_",
            "emptyTable": "NO HAY DATOS DISPONIBLES",
            "search":     "BUSCAR"
        },
        'columns': [
            { "title"   : "Articulo",       "data"  : "ARTICULO" },
            { "title"   : "Descripción",    "data"  : "DESCRIPCION" },
            { "title"   : "Meta Units.",     "data"  : "METAU" },
            { "title"   : "Real Units.",     "data"  : "REALU" },
            { "title"   : "% Cumpl. Units.",  "data"  : "DIFU" },
            { "title"   : "Meta Vtas.",      "data"  : "METAE" },
            { "title"   : "Real Vtas.",      "data"  : "REALE" },
            { "title"   : "% Cumpl. Vtas.",   "data"  : "DIFE" }
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [ 0 ]},
            {"className": "dt-back-unit", "targets": [ 2, 3, 4 ]},
            {"className": "dt-back-vtas", "targets": [ 5, 6, 7 ]},
            {"width": "20%", "targets": [ 1]},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\ U,C$]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            totalRealU = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            totalMetaU = api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
            }, 0 );
            totalDifU = (totalMetaU==0) ? "0.00%" : ((parseFloat(realu.replace(/[\ U,C$]/g, ''))/parseFloat(metau.replace(/[\ U,C$]/g, '')))*100);


            totalRealE = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
            }, 0 );
            totalMetaE = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
            }, 0 );
            totalDifE = (totalMetaE==0) ? "0.00%" : ((parseFloat(reale.replace(/[\ U,C$]/g, ''))/parseFloat(metae.replace(/[\ U,C$]/g, '')))*100);

            $('#vendedorNombre').text(nombre);
            $('#total_Real_Unidad').text(numeral(realu.replace(/[\ U,C$]/g, '')).format('0,0.00'));
            $('#total_Meta_Unidad').text(numeral(metau.replace(/[\ U,C$]/g, '')).format('0,0.00'));
            $('#total_Dif_Unidad')
            .attr('class','font-weight-bolder text-info')
            .text(numeral(totalDifU).format('0,0.00')+'%');

            $('#total_Real_Efectivo').text('C$'+numeral(reale.replace(/[\ U,C$]/g, '')).format('0,0.00'));
            $('#total_Meta_Efectivo').text('C$'+numeral(metae.replace(/[\ U,C$]/g, '')).format('0,0.00'));
            
            $('#total_Dif_Efectivo')
            .attr('class','font-weight-bolder text-info')
            .text(numeral(totalDifE).format('0,0.00')+'%');
        }
    });
    $("#dtVentas_length").hide();
    $("#dtVentas_filter").hide();
    $('#id_detalles_ventas').show();
    $('#dtVentasFacturas').hide();
    $('#id_div_Detalles_venta').show();
    $('#mdDetailsVentas').modal('show');
}

function GetTop10Items(){

    var dia             = 0;
    var mes             = $('#opcMes option:selected').val();
    var anio            = $('#opcAnio option:selected').val();
    var segmento        = $('#opcSegmentos option:selected').val();

    location.href = "excelAllTop10/" + dia + "/" + mes + "/" + anio + "/" + segmento;
}

function Todos_Los_Items(){

    tableActive = '';
    tableActive = '#tblAllItems';

    var mes             = $('#opcMes option:selected').val();
    var anio            = $('#opcAnio option:selected').val();
    mes_name            = $("#opcMes option:selected").text();   

    var segmento        = $('#opcSegmentos option:selected').val();
    var SegmentoName    = $("#opcSegmentos option:selected").text();  
    var dia = 0; 

   

    if (segmento == 0) {
        varTitulo       = 'Mostrando registros del mes de ' + mes_name + ' ' + anio ;        
        varSubTitulo    = 'De todos los segmentos.' ;        
    } else {
        varTitulo = 'Mostrando registros del mes de ' + mes_name + ' ' + anio 
        varSubTitulo    = ' Del segmento ' + SegmentoName;      
    }
    
    $("#id_titulo_modal_all_items").text(varTitulo);
    $("#id_sub_titulo_modal_all_items").text(varSubTitulo);
    



    $(tableActive).DataTable({
        "destroy" : true,
        "info":    false,
        "scrollX": false,
        "order": [[ 8, "desc" ]],
        "lengthMenu": [[-1], ["Todo"]],
        "ajax":{
            "url": "detallesTodosItems/" + dia + "/" + mes + "/" + anio + "/" + segmento,
            'dataSrc': '',
        },
        "language": {
            "zeroRecords": "Cargando...",
            "paginate": {
                "first":      "Primera",
                "last":       "Última ",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "lengthMenu": "MOSTRAR _MENU_",
            "emptyTable": "NO HAY DATOS DISPONIBLES",
            "search":     "BUSCAR"
        },
        
        'columns': [
            { "title"   : "ARTICULO",           "data"  : "Articulo" },
            { "title"   : "DESCRIPCION",        "data"  : "Descripcion" },
            { "title"   : "CANT. DISP",         "data"  : "Existencia" },
            { "title"   : "TOT. FACT",          "data"  : "TotalFacturado" },
            { "title"   : "UNIT. FACT.",        "data"  : "UndFacturado" },
            { "title"   : "UNIT. BONIF.",       "data"  : "UndBoni" },
            { "title"   : "PREC. PROM.",        "data"  : "PrecProm" },
            { "title"   : "COST. PROM. UNIT.",  "data"  : "CostProm" },
            { "title"   : "CONTRIBUCION",       "data"  : "Contribu" },
            { "title"   : "% MARGEN BRUTO",     "data"  : "MargenBruto" }
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [0]},
            {"className": "dt-right", "targets": [ 2,3,4,5,6,7,8,9]},            
            {"width": "20%", "targets": [ 1]},
            
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            var intVal = function ( i ) {
                    return typeof i === 'string' ?
                    i.replace(/[^0-9.]/g, '')*1 :
                    typeof i === 'number' ?
                    i : 0;
                };
                total = api.column( 2 ).data().reduce( function (a, b) 
                    {
                        return intVal(a) + intVal(b);
                    }, 0 );
                $('#id_total_segmento').text('Total C$ '+ numeral(total).format('0,0'));
        },
    });
    $("#mdDetailsAllItems").modal();
    $(tableActive + "_length").hide();
    $(tableActive + "_filter").hide();
}
function Todos_Los_Items_Diario(dia,mes,anio,segmento){

tableActive = '';
tableActive = '#tblAllItemsDiario';

$(tableActive).DataTable({
    "destroy" : true,
    "info":    false,
    "scrollX": false,
    "order": [[ 8, "desc" ]],
    "lengthMenu": [[10,-1], [10,"Todo"]],
    "ajax":{
        "url": "detallesTodosItems/" + dia + "/" + mes + "/" + anio + "/" + segmento,
        'dataSrc': '',
    },
    "language": {
        "zeroRecords": "Cargando...",
        "paginate": {
            "first":      "Primera",
            "last":       "Última ",
            "next":       "Siguiente",
            "previous":   "Anterior"
        },
        "lengthMenu": "MOSTRAR _MENU_",
        "emptyTable": "NO HAY DATOS DISPONIBLES",
        "search":     "BUSCAR"
    },
    
    'columns': [
        { "title"   : "ARTICULO",           "data"  : "Articulo" },
        { "title"   : "DESCRIPCION",        "data"  : "Descripcion" },
        { "title"   : "CANT. DISP",         "data"  : "Existencia" },
        { "title"   : "TOT. FACT",          "data"  : "TotalFacturado" },
        { "title"   : "UNIT. FACT.",        "data"  : "UndFacturado" },
        { "title"   : "UNIT. BONIF.",       "data"  : "UndBoni" },
        { "title"   : "PREC. PROM.",        "data"  : "PrecProm" },
        { "title"   : "COST. PROM. UNIT.",  "data"  : "CostProm" },
        { "title"   : "CONTRIBUCION",       "data"  : "Contribu" },
        { "title"   : "% MARGEN BRUTO",     "data"  : "MargenBruto" }
    ],
    "columnDefs": [
        {"className": "dt-center", "targets": [0]},
        {"className": "dt-right", "targets": [ 2,3,4,5,6,7,8,9]},            
        {"width": "20%", "targets": [ 1]},
        
    ],
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;
        var intVal = function ( i ) {
                return typeof i === 'string' ?
                i.replace(/[^0-9.]/g, '')*1 :
                typeof i === 'number' ?
                i : 0;
            };
            total = api.column( 2 ).data().reduce( function (a, b) 
                {
                    return intVal(a) + intVal(b);
                }, 0 );
            $('#id_total_segmento').text('Total C$ '+ numeral(total).format('0,0'));
    },
});
$(tableActive + "_length").hide();
$(tableActive + "_filter").hide();
}



function detailAllClients(xbolsones) {
        
        tableActive = '';
        tableActive = '#tblAllClients';
        
        var mes = $('#opcMes option:selected').val();
        var anio = $('#opcAnio option:selected').val();
        mes_name = $("#opcMes option:selected").text();      
        var categoria =  $('#OpcSegmClt option:selected').val(); 

        

        //FechaFiltrada = 'Mostrando registros del mes de ' + mes_name + ' ' + anio;
        $(tableActive).dataTable({
            "responsive": true,
            "autoWidth": false,
            "scrollX": false,
            "order": [[ 2, "desc" ]],
            "ajax": {
                "url": "detailsAllCls/" + mes + "/" + anio + "/" + categoria + "/" + xbolsones ,
                'dataSrc': '',
            },
            "destroy": true,
            "info": false,
            //"lengthMenu": [[500, -1], [500, "Todo"]],
            "language": {
                "zeroRecords": "Cargando...",
                "paginate": {
                    "first": "Primera",
                    "last": "Última ",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "lengthMenu": "MOSTRAR _MENU_",
                "emptyTable": "NO HAY DATOS DISPONIBLES",
                "search": "BUSCAR"
            },
            'columns': [
                {"title": "Cliente", "data": "codigo"},
                {"title": "Nombre", "data": "cliente"},
                {"title": "MontoVenta", "data": "data_innova", render: $.fn.dataTable.render.number( ',', '.', 0, 'C$ ' )},
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [0, 1, 2]},
                {"width": "20%", "targets": [0,2]},
            ],
        });
        $('#mdClientDetail').modal();
        $(tableActive + "_length").hide();
        $(tableActive + "_filter").hide();
    }

 
function get_Detalle_Venta_dia(dia,mes, anio, ruta, nombre) {

    $('#dtVentasFacturas').dataTable({
        responsive: true,
        "autoWidth":false,
        "ajax":{
            "url": "detallesVentasRutaDia/"+dia+"/"+mes+"/"+anio+"/"+ruta,
            'dataSrc': '',
        },
        "destroy" : true,
        "info":    false,
        "lengthMenu": [[500,-1], [500,"Todo"]],
        "language": {
            "zeroRecords": "Cargando...",
            "paginate": {
                "first":      "Primera",
                "last":       "Última ",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "lengthMenu": "MOSTRAR _MENU_",
            "emptyTable": "NO HAY DATOS DISPONIBLES",
            "search":     "BUSCAR"
        },
        'columns': [
            { "title"   : "",                   "data"  : "DETALLE"},
            { "title"   : "Factura",            "data"  : "Factura" },
            { "title"   : "Fecha",              "data"  : "Dia" },
            { "title"   : "Codigo Cliente",     "data"  : "CODE" },
            { "title"   : "Nombre Cliente",     "data"  : "NOMBRE" },
            { "title"   : "Total",              "data"  : "Total" }
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [ 0,1,2,3,4 ]},
            {"className": "dt-back-unit", "targets": [ 4 ]},
            {"className": "dt-back-vtas", "targets": [ 5 ]},
            {"width": "20%", "targets": [ 1]},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            $('#vendedorNombre').text(nombre);

            $('#id_detalles_ventas').hide();

            $('#id_div_Detalles_venta').hide();
            
            
            $('#dtVentasFacturas').show();
            
        }
    });
    $("#dtVentasFacturas_length").hide();
    $("#dtVentasFacturas_filter").hide();
    $('#mdDetailsVentas').modal('show');
}

function detalleComparacionVentas(obj, tp) {
    var dif         = 0;
    var porcen01    = 0;
    var porcen02    = 0;
    switch(tp) {
        case 'vts':
            title = `Reporte YTD Montos C$`;
            mes_actual      = obj[0]['name'];
            anio_pasado     = obj[1]['name'];
            mes_pasado      = obj[2]['name'];

            m_actual        = parseFloat(obj[0]['data']);
            m_anio_pasado   = parseFloat(obj[1]['data']);
            m_mes_pasado    = parseFloat(obj[2]['data']);            

            if (m_anio_pasado>0) {
                dif = (m_actual-m_anio_pasado);
                porcen01 = (dif/m_anio_pasado)*100;
            }

            if (m_mes_pasado>0) {
                dif = (m_actual-m_mes_pasado);
                porcen02 = (dif/m_mes_pasado)*100;
            }

            st_1 = (porcen01<0)?` <i class="material-icons text-danger font-weight-bold" style="font-size:15px">arrow_downward</i>`:` <i class="material-icons text-success font-weight-bold" style="font-size:15px">arrow_upward</i>`;

            cls_1 = (porcen01<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;
            cls_2 = (porcen02<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;

            text_monto_actual       = 'C$'+numeral(obj[0]['data']).format('0,0.00')+st_1;            
            text_monto_anio_pasado  = 'C$'+numeral(obj[1]['data']).format('0,0.00');
            text_monto_mes_pasado   = 'C$'+numeral(obj[2]['data']).format('0,0.00');
        break;
        case 'its':
            title = `Reporte YTD (Total de Items)`;
            mes_actual      = obj[0]['name'];
            anio_pasado     = obj[1]['name'];
            mes_pasado      = obj[2]['name'];


            m_actual        = parseFloat(obj[0]['data']);
            m_anio_pasado   = parseFloat(obj[1]['data']);
            m_mes_pasado    = parseFloat(obj[2]['data']);            

            if (m_anio_pasado>0) {
                dif = (m_actual-m_anio_pasado);
                porcen01 = (dif/m_anio_pasado)*100;
            }

            if (m_mes_pasado>0) {
                dif = (m_actual-m_mes_pasado);
                porcen02 = (dif/m_mes_pasado)*100;
            }

            st_1 = (porcen01<0)?` <i class="material-icons text-danger font-weight-bold" style="font-size:15px">arrow_downward</i>`:` <i class="material-icons text-success font-weight-bold" style="font-size:15px">arrow_upward</i>`;

            cls_1 = (porcen01<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;
            cls_2 = (porcen02<0)?`text-danger font-weight-bolder`:`text-success font-weight-bolder`;

            text_monto_actual       = numeral(obj[0]['data']).format('0')+st_1;            
            text_monto_anio_pasado  = numeral(obj[1]['data']).format('0');
            text_monto_mes_pasado   = numeral(obj[2]['data']).format('0');
        break;
        default:
        alert('Ups... parece que ocurrio un error :(');
    }
    $('#text-mes-actual').text(mes_actual);
    $('#val-mes-actual').html(text_monto_actual);

    $('#text-anio-pasado').text(anio_pasado);
    $('#val-anio-pasado').text(text_monto_anio_pasado);

    $('#text-mes-pasado').text(mes_pasado);
    $('#val-mes-pasado').text(text_monto_mes_pasado);

    $('#dif-porcen-vts')
    .attr('class', cls_1)
    .text(numeral(porcen01).format('0.0')+'%');
    $('#dif-porcen-its')
    .attr('class', cls_2)
    .text(numeral(porcen02).format('0.0')+'%');

    $('#titleModal-01').text(title)
    $('#mdDetails').modal('show')
}

$('#filterDtTemp').on( 'keyup', function () {
    var table = $(tableActive).DataTable();
    table.search(this.value).draw();
});

$('#Search_cliente_no_facturado').on( 'keyup', function () {
    var table = $("#tblClientes").DataTable();
    table.search(this.value).draw();
});



/******************FROM ALL ITEM TOP 10 ***********************/
$('#id_txt_all_item').on( 'keyup', function () {
    var table = $(tableActive).DataTable();
    table.search(this.value).draw();
});

$( "#id_select_all_items").change(function() {
    var table = $(tableActive).DataTable();
    table.page.len(this.value).draw();
});



$('#id_txt_all_clients').on( 'keyup', function () {
    var table = $(tableActive).DataTable();
    table.search(this.value).draw();
});

$( "#id_select_all_clients").change(function() {
    var table = $(tableActive).DataTable();
    table.page.len(this.value).draw();
});

/************************************************************/

$( "#cantRowsDtTemp").change(function() {
    var table = $(tableActive).DataTable();
    table.page.len(this.value).draw();
});
$('#filterDtDetalle').on( 'keyup', function () {
    var table = $('#dtVentas').DataTable();
    table.search(this.value).draw();
});

$( "#cantRowsDtDetalle").change(function() {
    var table = $('#dtVentas').DataTable();
    table.page.len(this.value).draw();
});

$(".active-page-details").click( function() {
    $("#cantRowsDtTemp").val("5");
    $("#page-details").toggleClass('active');    
});

/*OCULTANDO GRAFICAS DASHBOARD*/
$(document).on('change', '.dash-opc', function(e) {
    val01 = $(this).val();

    if( $(this).prop('checked') ) {
        $.cookie( $(this).val() , 'yes_visible');
        $('div.'+val01).parent().show();
        $.removeCookie($(this).val());
    }else {
        $.cookie( $(this).val() , 'not_visible');
        $('div.'+val01).parent().hide();
    }
    location.reload();
});

//MODAL PARA VER LOS DETALLES DE FACTUA DE LA GRAFICA DE DIARIO
$(document).on('click', '#exp_more', function(ef) {
    var table = $('#dtVentasFacturas').DataTable();
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var data = table.row($(this).parents('tr')).data();

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
        ef.target.innerHTML = "expand_more";
        ef.target.style.background = '#e2e2e2';
        ef.target.style.color = '#007bff';
    } else {
        //VALIDA SI EN LA TABLA HAY TABLAS SECUNDARIAS ABIERTAS
        table.rows().eq(0).each( function ( idx ) {
            var row = table.row( idx );
            if ( row.child.isShown() ) {
                row.child.hide();
                ef.target.innerHTML = "expand_more";

                var c_1 = $(".expan_more");
                c_1.text('expand_more');
                c_1.css({
                    background: '#e2e2e2',
                    color: '#007bff',
                });
            }
        } );

        format(row.child,data.Factura);
        tr.addClass('shown');
        
        ef.target.innerHTML = "expand_less";
        ef.target.style.background = '#ff5252';
        ef.target.style.color = '#e2e2e2';
    }
});


function format ( callback, Factura_ ) {
    var thead = tbody = '';            
    thead =`<table class="table table-striped table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="center">ARTICULO</th>                        
                        <th class="center">DESCRIPCION.</th>
                        <th class="center">CANTIDAD</th>
                        <th class="center">PRECIO UNITARIO</th>
                        <th class="center">TOTAL</th>
                    </tr>
                </thead>
                <tbody>`;
                $.ajax({
        type: "POST",
        url: "getDetFactVenta",
        data:{
            factura: Factura_,
        },
        success: function ( data ) {
            if (data.length==0) {
                tbody +=`<tr><td colspan='6'><center>....</center></td></tr>`;
                callback(thead + tbody).show();
            }
            
            $.each(data['objDt'], function (i, item) {
                tbody +=`<tr>
                            <td class="center">` + item['ARTICULO'] + `</td>
                            <td class="text-left">` + item['DESCRIPCION'] + `</td>
                            <td class="text-left">` +numeral(item['CANTIDAD']).format('0,0.00')  + `</td>
                            <td class="text-center">` + numeral(item['PRECIO_UNITARIO']).format('0,0.00') + `</td>
                            <td class="text-right">` + numeral(item['PRECIO_TOTAL']).format('0,0.00') + `</td>
                        </tr>`;
            });
            tbody += `</tbody></table>`;
            
            temp = thead+tbody;

            callback(temp).show();
        }


    });
}

function reordenandoPantalla() {
    var x = 0;
    $(".content-graf div.row").each(function(e) {
        var div01 = $(this).attr('id');
        $("#" + div01 + " div.graf").each(function() {
            ($(this).is(":visible"))?x++:x=x;            
        })
        cont = 12 / x;
        $( "#" + div01 + " div.graf" ).removeClass( "col-sm-*" ).addClass( "col-sm-"+cont );
        x=0;
    });
} 

function FormatPretty(number) {
    var numberString;
    var scale = '';
    if( isNaN( number ) || !isFinite( number ) ) {
        numberString = 'N/A';
    } else {
        var negative = number < 0;
        number = negative? -number : number;

        if( number < 1000 ) {
            scale = '';
        } else if( number < 1000000 ) {
            scale = 'K';
            number = number/1000;
        } else if( number < 1000000000 ) {
            scale = 'M';
            number = number/1000000;
        } else if( number < 1000000000000 ) {
            scale = 'B';
            number = number/1000000000;
        } else if( number < 1000000000000000 ) {
            scale = 'T';
            number = number/1000000000000;
        }
        var maxDecimals = 0;
        if( number < 10 && scale != '' ) {
            maxDecimals = 1;
        }
        number = negative ? -number : number;
        numberString = number.toFixed( maxDecimals );
        numberString += scale
    }
    return numberString;
}

</script>
