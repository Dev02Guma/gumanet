<script type="text/javascript">
    
    var colors_ = ['#407EC9', '#D19000', '#00A376', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];
    grafiacas_productos_Diarios = {
        chart: {
            type: 'spline',
            renderTo: 'grafVtsDiario',
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
    $('[data-toggle="tooltip"]').tooltip();
    //fullScreen();
    function isValue(value, def, is_return) {
        if ( $.type(value) == 'null'
            || $.type(value) == 'undefined'
            || $.trim(value) == '(en blanco)'
            || $.trim(value) == ''
            || ($.type(value) == 'number' && !$.isNumeric(value))
            || ($.type(value) == 'array' && value.length == 0)
            || ($.type(value) == 'object' && $.isEmptyObject(value)) ) {
            return ($.type(def) != 'undefined') ? def : false;
        } else {
            return ($.type(is_return) == 'boolean' && is_return === true ? value : true);
        }
    }
$(document).ready(function() {
    Loading();
    //fullScreen();
    let Table = new DataTable('#dt_articulos',{
		"ajax":{
			"url": "getData",
			'dataSrc': '',
		},
	
		"lengthMenu": [[5,30,50,100,-1], [5,30,50,100,"Todo"]],
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
                    exportOptions: {
                        columns: ':visible'
                    }
                }]
            }
        },
        stateSave: true,
        fixedColumns: {
            start: 3
        },
        paging: true,
        scrollCollapse: true,
        scrollY: '1200px',
        scrollX: true,
		'columns': [	
			{"data": "ARTICULO"},
			{"data": "DESCRIPCION"},
            {"data": "FABRICANTE"},
			{"data": "LEADTIME"},
			{"data": "FACTOR_STOCK_SEGURIDAD"},
			{"data": "ROTACION_PREVISTA_EXISTENCIAS_VENCER"},
			{"data": "TOTAL_UMK"},
			{"data": "TOTAL_GP"},
			{"data": "TOTAL_DISP"},
			{"data": "VENCE_MENOS_IGUAL_12"},
			{"data": "VENCE_MAS_IGUAL_7"},
			{"data": "LOTE_MAS_PROX_VENCER"},
			{"data": "EXIT_LOTE_PROX_VENCER"},
			{"data": "FECHA_ENTRADA_LOTE"},
			{"data": "CANTIDAD_INGRESADA"},
			{"data": "EJECUTADO_UND_YTD"},
			{"data": "PEDIDO"},
			{"data": "TRANSITO"},
			{"data": "VENTAS_YTD"},
			{"data": "CONTRIBUCION_YTD"},
			{"data": "ROTACION_CORTA"},
			{"data": "ROTACION_MEDIA"},
			{"data": "ROTACION_LARGA"},
			{"data": "MOQ"},
			{"data": "REORDER"},
			{"data": "CANTIDAD_ORDENAR"},
			{"data": "CANTIDAD_ORDENAR", "render": function(data, type, row, meta) {

				var _ReOrder = numeral(row.REORDER).format('00.00');
				var _MOQ     = numeral(row.MOQ).format('00.00')
				
				let color_cant_order = _ReOrder / _MOQ;

				color_cant_order = isValue(color_cant_order,0,true);

				return numeral(color_cant_order).format('0.00');


			}},
			{"data": "COSTO_PROMEDIO_LOC"},
			{"data": "COSTO_PROMEDIO_USD"},
			{"data": "ULTIMO_COSTO_USD"},
			{"data": "DEMANDA_ANUAL_CA_NETA"},
			{"data": "DEMANDA_ANUAL_CA_AJUSTADA"},
			{"data": "FACTOR"},
			{"data": "LIMITE_LOGISTICO_MEDIO"},
			{"data": "CLASE"},
			{"data": "VALUACION"},
			{"data": "REORDER1"},
			{"data": "ESTIMACION_SOBRANTES_UND"},
			
		],
        "columnDefs": [
            {"className": "dt-center", "targets": []},
            {"className": "dt-right", "targets": [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,20,21,22,23,24,25,26,27,28,29,30]},
            {"className": "dt-right-color", "targets": [24]},
            { "width": "300", "targets": [ 1 ] },
        ],
       
        "createdRow": function( row, data, dataIndex){

            $("#id_UpdateAt").html(data.UPDATED_AT);

            var _ReOrder = numeral(data.REORDER).format('00.00');
            var _MOQ     = numeral(data.MOQ).format('00.00')
            
            let color_cant_order = _ReOrder / _MOQ;

            color_cant_order = isValue(color_cant_order,0,true);

            $(row).find('td:eq(24)').addClass( (color_cant_order <= 0.5 ) ? 'dt-cant-ordenar-red' : 'dt-cant-ordenar-green');
            
        
            if( data["IS_CA"] ==  `S`){
                $(row).addClass('dt-is-ca-background');
            } 

        },
        "initComplete": function(settings, json) {
            $("#LoadingID").empty();
        }

        
    });

    //$(".dt-layout-start, .dt-layout-end").hide();



	$('#txt_search').on( 'keyup', function () {
        var table = $('#dt_articulos').DataTable();
        table.search(this.value).draw();
	});

	
    $( "#select_rows").change(function() {
        var table = $('#dt_articulos').DataTable();
        table.page.len(this.value).draw();
    });



});

$('#selectGrafVtsDiario').change(function() {
    var Canal = this.value;         
    var Articulo = $("#id_articulo").text();
    
    FiltrarPorCanal(Articulo,Canal);     
});


// $("#BtnClickColumns").click(function() {
//     var table = new DataTable('#dt_articulos');
    
//     table.button('0').trigger();
//     //table.buttons('.buttons-colvis').trigger();
// })

// $("#BtnClickExport").click(function() {
//     var table = new DataTable('#dt_articulos');
//     table.buttons('.buttons-excel').trigger();
// })


$("#exp-to-excel").click(function() {    
    location.href = "ExportToExcel";
})

$("#BtnClick").click(function() {

    
    Swal.fire({
        title: "Recalcular Reorder Point",
        inputAttributes: {
            autocapitalize: "off"
        },
        showCancelButton: true,
        confirmButtonText: "Calcular",
        showLoaderOnConfirm: true,
        preConfirm: async (login) => {
            try {
            const githubUrl = `CalcReorder`;
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


function getDetalleArticulo(Articulos,Descripcion,Undiad) {

	$("#id_descripcion").html(Descripcion+` | `+ Articulos);
    $("#id_articulo").html(Articulos);


	$("#mdDetalleArt").modal('show');
    grafVentasMensuales(Articulos,'Todos');
    

}

function dataVinneta(f1, f2,Ruta,Cliente,Stat) {
    $('#dtInfo,#dtEstimacion').DataTable({
        responsive: true,
        "info":    false,
        "bPaginate": true,
        
    });
    $("#dtInfo_length,#dtEstimacion_length").hide();
    $("#dtInfo_filter,#dtEstimacion_filter").hide();
    
}

function FormatPretty(number) {
    if( isNaN( number ) || !isFinite( number ) ) {
        return 'N/A';
    }

    const scales = ['', 'K', 'M', 'B', 'T'];
    const negative = number < 0;
    let scaledNumber = Math.abs(number);
    let scale = 0;

    while(scaledNumber >= 1000 && scale < scales.length - 1) {
        scaledNumber /= 1000;
        scale++;
    }

    const maxDecimals = scaledNumber < 10 ? 1 : 0;

    return (negative ? '-' : '') + scaledNumber.toFixed(maxDecimals) + scales[scale];
}

function Loading() {
    
    $("#LoadingID").empty().append(`<div style=" padding:5px">
        <div class="d-flex align-items-center mt-1">
            <strong class="text-info">Cargando...</strong>
            <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
        </div>
    </div>`);
}
function grafVentasMensuales(Articulos, Canal) {
    $.getJSON("dtGraf/" + Articulos + "/" + Canal, function(json) {
            dta = [];
            title = [];
            tmp_total = 0;
            Day_Max = [];

            var vVtsDiarias;
            
            $("#id_leadtime").html(json['LEADTIME']);
            $("#id_demanda_neta").html(json['DEMANDA_ANUAL_CA_NETA']);
            $("#id_demanda_ajustada").html(json['DEMANDA_ANUAL_CA_AJUSTADA']);
            $("#id_limite_logistico_medio").html(json['LIMITE_LOGISTICO_MEDIO']);
        

            $("#id_reorder1").html(numeral(json['REORDER1']).format('0,0'));
            $("#id_reordenar").html(numeral(json['REORDER']).format('0,0'));
            $("#id_cant_ordenar").html(numeral(json['CANTIDAD_ORDENAR']).format('0,0'));

            $("#id_clase").html(json['CLASE']);
            $("#id_pedido_transito").html(numeral(json['PEDIDO_TRANSITO']).format('0,0'));
            $("#id_moq").html(json['MOQ']);

            $("#id_R_corta").html(json['ROTACION_CORTA']);
            $("#id_R_media").html(json['ROTACION_MEDIA']);
            $("#id_R_larga").html(json['ROTACION_LARGA']);
            

            $("#id_costo").html(numeral(json['COSTO_PROMEDIO_USD']).format('0,0.00'));
            $("#id_ultimo_costo").html(numeral(json['ULTIMO_COSTO_USD']).format('0,0.00'));

            $("#id_ultimo_loc").html(numeral(json['COSTO_PROMEDIO_LOC']).format('0,0.00'));

            $("#id_transito").html(numeral(json['TRANSITO']).format('0,0'));
            $("#id_pedido").html(numeral(json['PEDIDO']).format('0,0'));


            if (Canal !='Todos' ) {
                
            }


            $("#id_promedio_mensual").html(numeral(json['EJECUTADO_UND_YTD']).format('0,0') + " UNITS");                
            $("#id_ventas").html('C$ ' + numeral(json['VENTAS_YTD']).format('0,0'));        
            $("#id_contribucion").html('C$ ' + numeral(json['CONTRIBUCION']).format('0,0'));
            
            $.each(json['VENTAS'], function(i, x) {
                tmp_total = tmp_total + parseFloat(x['data']);
                dta.push({
                    name  : x['Mes'],
                    y     : x['data'], 
                });

                title.push(x['name']); 
                Day_Max.push(x['data']); 
            }); 



            temporal = '<span style="color:black">\u25CF</span><b>{point.y} </b> UNITS<br/>';                
            grafiacas_productos_Diarios.tooltip = {
                pointFormat : temporal
            }

            vVtsDiarias = numeral(tmp_total).format('0,0.00');
            
            grafiacas_productos_Diarios.xAxis.categories = title;
            grafiacas_productos_Diarios.subtitle.text = vVtsDiarias + " UNITS";
            grafiacas_productos_Diarios.series[0].data = dta;

            chart = new Highcharts.Chart(grafiacas_productos_Diarios);
            
            chart.yAxis[0].update();

    })
}
function FiltrarPorCanal(Articulos, Canal) {
    $.getJSON("dtGraf/" + Articulos + "/" + Canal, function(json) {
            dta = [];
            title = [];
            tmp_total = 0;
            Day_Max = [];

            var vVtsDiarias;

            $("#id_promedio_mensual").html(numeral(json['EJECUTADO_UND_YTD']).format('0,0') + " UNITS");                
            $("#id_ventas").html('C$ ' + numeral(json['VENTAS_YTD']).format('0,0'));        
            $("#id_contribucion").html('C$ ' + numeral(json['CONTRIBUCION_YTD']).format('0,0'));
            
            $.each(json['VENTAS'], function(i, x) {
                tmp_total = tmp_total + parseFloat(x['data']);
                dta.push({
                    name  : x['Mes'],                                        
                    y     : x['data'], 
                });

                title.push(x['name']); 
                Day_Max.push(x['data']); 
            }); 



            temporal = '<span style="color:black">\u25CF</span><b>{point.y} </b> UNITS<br/>';                
            grafiacas_productos_Diarios.tooltip = {
                pointFormat : temporal
            }

            vVtsDiarias = numeral(tmp_total).format('0,0.00');
            
            grafiacas_productos_Diarios.xAxis.categories = title;
            grafiacas_productos_Diarios.subtitle.text = vVtsDiarias + " UNITS";
            grafiacas_productos_Diarios.series[0].data = dta;

            chart = new Highcharts.Chart(grafiacas_productos_Diarios);
            
            chart.yAxis[0].update();

    })
}

</script>