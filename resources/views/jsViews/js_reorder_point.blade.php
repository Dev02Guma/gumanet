<script>
    var colors_ = ['#407EC9', '#D19000', '#00A376', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];
    grafiacas_productos_Diarios = {
        chart: {
            type: 'column',
            renderTo: 'grafVtsDiario',
        },      

        title: {
            text: 'Comportamiento'
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
            point: {
                events: {
                    click: function(e) {
                        //detalles_ventas_diarias(this.name,this.mAVG);
                    }
                }
            },
        }]
    }; 
$(document).ready(function() {

    var articulo_g = 0;
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active"><a href="{{url('/Inventario')}}">Inventario</a></li><li class="breadcrumb-item active">Reorder Point</li>`);

    $('#dtInvCompleto').DataTable({
		"ajax":{
			"url": "getData",
			'dataSrc': '',
		},
		'info': false,
		"lengthMenu": [[25,200,300,400,-1], [25,200,300,400,"Todo"]],
		"language": {
			"infoFiltered": "(Filtrado de _MAX_ total entradas)",
			"zeroRecords": "No hay coincidencias",
			"loadingRecords": "Cargando datos...",
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
			{"title": "ARTICULO","data": "ARTICULO", "render": function(data, type, row, meta) { 

				return`<a href="#!" onclick="getDetalleArticulo(`+ "'" +row.ARTICULO + "'" +` , ` + "'" +row.DESCRIPCION + "'" +` ,`+ "'" +row.UNIDAD + "'" +`)" >`+ row.ARTICULO +`</a>`

			}},
            {"title": "DESCRIPCIÓN", 		"data": "DESCRIPCION"},
			{"title": "EXISTENCIAS PROX. A VENCER <=12 Meses", 		"data": "CANTIDAD"},
            {"title": "ROTACION PREVISTA EXISTENCIAS POR VENCER", 		"data": "CANTIDAD"},
            {"title": "EXISTENCIAS LOTE >=7 Meses", 		"data": "CANTIDAD"},
            {"title": "LOTE MAS PROX. A VENCER", 		"data": "CANTIDAD"},
            {"title": "EXISTENCIA EN LORE MAS PROX. POR VENCERSE", 		"data": "CANTIDAD"},
            
		],
		"columnDefs": [
			{"className": "dt-center", "targets": [0]},
			{"className": "dt-right", "targets": [2,3,4,5,6]},
			{"width":"20%","targets":[]},
			{"width":"10%","targets":[2,3,4,5,6]}
		],
    });

    $("#dtInvCompleto_length").hide();
    $("#dtInvCompleto_filter").hide();

	$('#InputDtShowSearchFilterArt').on( 'keyup', function () {
	    var table = $('#dtInvCompleto').DataTable();
	    table.search(this.value).draw();
	});

	$( "#InputDtShowColumnsArtic").change(function() {
	    var table = $('#dtInvCompleto').DataTable();
	    table.page.len(this.value).draw();
	});


});

function getDetalleArticulo(Articulos,Descripcion,Undiad) {
    articulo_g = Articulos;
	$("#tArticulo").html(Descripcion+`<p class="text-muted" id="id_cod_articulo">`+Articulos+`</p>`);
	
	var target = '#nav-bod';
    $('a[data-toggle=tab][href=' + target + ']').tab('show');

    //$("#tbody1").empty().append(`<tr><td colspan='5'><center>Aún no ha realizado ninguna busqueda</center></td></tr>`);
	$("#mdDetalleArt").modal('show');
    grafVentasMensuales()

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
function grafVentasMensuales() {

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





$.getJSON("dtGraf/10/2023", function(json) {



        dta = [];
        dta_avr = [];
        title = [];
        tmp_total = 0;
        Tendencia = 1;
        Day_Max = [];

        var vVtsDiarias;


        $.each(json, function(i, x) {

            tmp_total = tmp_total + parseFloat(x['data']);

            dta.push({
                name  :'Dia ' + x['articulo'],
                mAVG  : x['dtAVG'],
                FACT  : x['FACTURAS'],
                dtavg : x['dtavg_'],
                y     : x['data'], 
                und   : (x['dtUnd'] > 0 ) ?  x['dtUnd']  : '  '
            });

            goal = x['dtAVG']
            title.push(x['name']); 
            Day_Max.push(x['data']); 
        }); 

        moneda = "C$ "
        temporal = '<span style="color:black">\u25CF</span> VALOR :<b>C$  {point.y} </b><br/>';
        temporal += '<span style="color:black">\u25CF</span> UNITS.: <b>  {point.und} </b><br/>';  
        temporal += '<span style="color:black">\u25CF</span> CANT. FACT.: <b>  {point.FACT} </b><br/>';                   
        grafiacas_productos_Diarios.tooltip = {
            pointFormat : temporal
        }
        vVtsDiarias = numeral(tmp_total).format('0,0.00');
        grafiacas_productos_Diarios.xAxis.categories = title;
        grafiacas_productos_Diarios.subtitle.text = moneda + vVtsDiarias + " Total";
        grafiacas_productos_Diarios.series[0].data = dta;


        
        chart = new Highcharts.Chart(grafiacas_productos_Diarios);
        
        chart.yAxis[0].update();

    
})
}
</script>