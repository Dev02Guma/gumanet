<script>
$(document).ready(function() {
    fullScreen();
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active"><a href="{{url('/Inventario')}}">Inventario</a></li><li class="breadcrumb-item active">Inventario completo</li>`);

    $('#dtInvCompleto').DataTable({
		"ajax":{
			"url": "invTotalizadoDT",
			'dataSrc': '',
		},
		'info': false,
		"lengthMenu": [[100,200,300,400,-1], [100,200,300,400,"Todo"]],
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

				var obj = {
				a: "hello"
				};

				return`<a href="#!" onclick="getDetalleArticulo(`+ "'" +row.ARTICULO + "'" +` , ` + "'" +row.DESCRIPCION + "'" +` ,`+ "'" +row.UNIDAD + "'" +`)" >`+ row.ARTICULO +`</a>`

			}},
			{"title": "DESCRIPCIÓN", 		"data": "DESCRIPCION", "render": function(data, type, row, meta) { 
                var lblVinneta =''
                if(row.VINNETA != ''){
                    var lblVinneta = `<span class="badge badge-pill badge-success"> `+ row.VINNETA +`</span>`
                }

				return`<div class="row justify-content-between">
                                <div class="col">
                                  <div class="d-flex">
                                    <div class="avatar avatar-2xl status-online">
                                      <img class="rounded-circle" src="{{ asset('images/item.png') }}" alt="" />
                                    </div>
                                    <div class="flex-1 align-self-center ms-2">
                                        <h6 class="mb-1 fs-1 fw-semi-bold">`+ row.DESCRIPCION +`</h6>
                                        <p class="mb-0 fs--1">
                                            <span class="badge badge-pill badge-primary"><span class="fas fa-check"></span> `+ row.UNIDAD +`</span>
                                            `+lblVinneta+`
                                            
                                            
                                        </p>
                                    </div>
                                  </div>
                                </div>
                              </div>`

			}},
			{"title": "CANT.DISPONIBLE", "data": "CANT_DISPONIBLE" },
		],
		"columnDefs": [
			{"className": "dt-center", "targets": [0, 1 ]},
			{"className": "dt-right", "targets": [2]},
			{"width":"20%","targets":[]},
			{"width":"5%","targets":[]}
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

	$("#exp-to-excel").click( function() {
	    location.href = "desInvTotal2";
	})
   
    $(document).on('click', '#exp_more', function(ef) {
		var table = $('#tblBodega').DataTable();
		var tr = $(this).closest('tr');
		var row = table.row(tr);
		var data = table.row($(this).parents('td')).data();
        dtARTICULO = $("#id_cod_articulo").text()


		if (row.child.isShown()) {
			row.child.hide();
			tr.removeClass('shown');
			ef.target.innerHTML = "expand_more";
			ef.target.style.background = '#e2e2e2';
			ef.target.style.color = '#007bff';
		} else {
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

			//format(row.child,data.UNIDAD,data.ARTICULO);
            format(row.child,data.BODEGA,dtARTICULO,data.UNIDAD);

			tr.addClass('shown');
			
			ef.target.innerHTML = "expand_less";
			ef.target.style.background = '#ff5252';
			ef.target.style.color = '#e2e2e2';
		}
	});

	
	
	function format ( callback, bodega_, articulo_, Unidad_ ) {
        var thead = tbody = '';            
        thead =`<table class="" width='100%'>
                    <tr>
                        <th class="center">LOTE</th>
                        <th class="center">CANT. DISPONIBLE</th>
                        <th class="center">CANT. INGRESADA POR COMPRA</th>
                        <th class="center">FECHA ULTM. INGRESO COMPRA</th>
                        <th class="center">FECHA DE CREACION</th>
                        <th class="center">FECHA VENCIMIENTO</th>
                    </tr>
                <tbody>`;
        $.ajax({
            type: "POST",
            url: "lotes",
            data:{
                bodega: bodega_,
                articulo: articulo_,
                Unidad: Unidad_,        
            },        
        success: function ( data ) {
            if (data.length==0) {
                tbody +=`<tr>
                            <td colspan='6'><center>Bodega sin existencia</center></td>
                        </tr>`;
                callback(thead + tbody).show();
            }
            $.each(data, function (i, item) {
                tbody +=`<tr class="center">
                            <td>` + item['LOTE'] + `</td>
                            <td>` + item['CANT_DISPONIBLE'] + `</td>
                            <td>` + item['CANTIDAD_INGRESADA'] + `</td>
                            <td>` + item['FECHA_INGRESO'] + `</td>
                            <td>` + item['FECHA_ENTRADA'] + `</td>
                            <td>` + item['FECHA_VENCIMIENTO'] + `</td>
                        </tr>`;
            });
            tbody += `</tbody></table>`;
            callback(thead + tbody).show();
            }
        });
    }


});
function getDetalleArticulo(Articulos,Descripcion,Undiad) {

	$("#tArticulo").html(Descripcion+`<p class="text-muted" id="id_cod_articulo">`+Articulos+`</p>`);
	
	var target = '#nav-bod';
    $('a[data-toggle=tab][href=' + target + ']').tab('show');

    //$("#tbody1").empty().append(`<tr><td colspan='5'><center>Aún no ha realizado ninguna busqueda</center></td></tr>`);
	$("#mdDetalleArt").modal('show');


	$.ajax({
        url: "ArticuloDetalles/"+Articulos+"/"+Undiad+"",
        type: 'get',
        data: {},
        async: true,
        success: function(data) {            

            $("#id_total_fact").text("C$ " + numeral(data[0].Indicadores['ANUAL'][0]['data']).format("0,00.00"));
            $("#id_unit_fact").text(numeral(data[0].Indicadores['ANUAL'][0]['dtUnd']).format("0,00.00"));
            $("#id_unit_bonif").text(numeral(data[0].Indicadores['ANUAL'][0]['dtUndBo']).format("0,00.00"));
            $("#id_prom_prec").text("C$ " + data[0].Indicadores['ANUAL'][0]['dtAVG']);
            $("#id_prom_cost_unit").text("C$ " +numeral(data[0].Indicadores['ANUAL'][0]['dtCPM']).format("0,00.00"));
            $("#id_contribucion").text("C$ " + data[0].Indicadores['ANUAL'][0]['dtMCO']);
            $("#id_margen_bruto").text(numeral(data[0].Indicadores['ANUAL'][0]['dtPCO']).format("0,00.00") + " %");

            $("#id_disp_bodega").text(numeral(data[0].Indicadores['ANUAL'][0]['dtTB2']).format("0,00.00") );
            $("#id_disp_bodega_unds").text(numeral(data[0].Indicadores['ANUAL'][0]['dtTUB']).format("0,00.00"));

            $("#id_prom_unds_mes").text(numeral(data[0].Indicadores['ANUAL'][0]['dtPRO']).format("0,00.00") );
            $("#id_cant_disp_mes").text(numeral(data[0].Indicadores['ANUAL'][0]['dtTIE']).format("0,00.00"));

            $("#id_total_fact_month").text("C$ " + numeral(data[0].Indicadores['MENSUAL'][0]['data']).format("0,00.00"));
            $("#id_unit_fact_month").text(numeral(data[0].Indicadores['MENSUAL'][0]['dtUnd']).format("0,00.00"));
            $("#id_unit_bonif_month").text(numeral(data[0].Indicadores['MENSUAL'][0]['dtUndBo']).format("0,00.00"));
            $("#id_prom_prec_month").text("C$ " + data[0].Indicadores['MENSUAL'][0]['dtAVG']);
            $("#id_prom_cost_unit_month").text("C$ " +numeral(data[0].Indicadores['ANUAL'][0]['dtCPM']).format("0,00.00"));
            $("#id_contribucion_month").text("C$ " + data[0].Indicadores['MENSUAL'][0]['dtMCO']);
            $("#id_margen_bruto_month").text(numeral(data[0].Indicadores['MENSUAL'][0]['dtPCO']).format("0,00.00") + " %");

            $("#id_disp_bodega_month").text(numeral(data[0].Indicadores['MENSUAL'][0]['dtTB2']).format("0,00.00") );
            $("#id_disp_bodega_unds_month").text(numeral(data[0].Indicadores['MENSUAL'][0]['dtTUB']).format("0,00.00"));

            $("#id_prom_unds_mes_month").text(numeral(data[0].Indicadores['MENSUAL'][0]['dtPRO']).format("0,00.00") );
            $("#id_cant_disp_mes_month").text(numeral(data[0].Indicadores['MENSUAL'][0]['dtTIE']).format("0,00.00"));

			$("#id_clase_abc").text(data[0].Otros[0]['CLASE']);
            $("#id_existencia_minima").text(data[0].Otros[0]['MINIMO']);
            $("#id_punto_de_reoden").text(data[0].Otros[0]['REORDEN']);
            $("#id_plazo_rebast").text(data[0].Otros[0]['REABASTECIMIENTO']);

			$("#id_prec_prom").text(data[0].Costos[0]['COSTO_PROM_LOC']);
            $("#id_ult_prec").text(data[0].Costos[0]['COSTO_ULT_LOC'])

            $("#id_vineta_valor").text("C$ " + numeral(data[0].ValorVinneta).format("0,00.00"))

			getMargen(data[0].Margen)
			getBonificados(data[0].Bonificaciones)
			getPrecios(data[0].Precios)
			getDataBodega(data[0].Bodega)
        }
    })

}
function getDataBodega(datos) {
    $("#tblBodega").dataTable({
        responsive: true,
        "autoWidth":false,
		"data": datos,
        "searching": false,
        "destroy": true,
        "paging":   false,
        "columns":[
            { "data": "DETALLE"},
            { "data": "BODEGA" },
            { "data": "UNIDAD" },
            { "data": "NOMBRE" },
            { "data": "CANT_DISPONIBLE" }
        ],
        "columnDefs": [
            { "width": "5%", "targets": [ 0, 1 ,2] },
            {"className":"dt-right", "targets": [ 4 ] },
            {"className":"dt-center", "targets": [ 1,2 ] }
        ],
        "info": false,
        "language": {            
            "zeroRecords": "No hay datos que mostrar",
            "emptyTable": "N/D",
            "loadingRecords": "Cargando...",
        }
    });
}
function getPrecios(datos) {
    $("#tblPrecios").dataTable({
        responsive: true,
        "autoWidth":false,
        "data": datos,
        "searching": false,
        "destroy": true,
        "paging":   false,
        "columns":[
            { "data": "NIVEL_PRECIO"},
            { "data": "PRECIO" }
        ],
        "info": false,
        "language": {            
            "zeroRecords": "No hay datos que mostrar",
            "emptyTable": "N/D",
            "loadingRecords": "Cargando...",
        }
    });
}
function getMargen(datos) {
    $("#tblMargen").dataTable({
        responsive: true,
        "autoWidth":false,
		"data": datos,
        "searching": false,
        "destroy": true,
        "paging":   false,
        "columns":[
            { "data": "NIVEL_PRECIO"},
            { "data": "PRECIO" }
        ],
        "info": false,
        "language": {            
            "zeroRecords": "No hay datos que mostrar",
            "emptyTable": "N/D",
            "loadingRecords": "Cargando...",
        }
    });
}
function getBonificados(datos) {
    $("#tblBonificados").dataTable({
        responsive: true,
        "autoWidth":false,
		"data": datos,
        "searching": false,
        "destroy": true,
        "paging":   false,
        "columns":[
            { "data": "REGLAS"}
        ],
        "info": false,
        "language": {            
            "zeroRecords": "No hay datos que mostrar",
            "emptyTable": "N/D",
            "loadingRecords": "Cargando...",
        }
    });
}
function ShowLotes(ID,Unidad_,articulo_){
	var bodega_ = ID.substr(1,ID.length);

	var thead = tbody = '';            
        thead =`<table class="" width='100%'>
                    <tr>
                        <th class="center">LOTE</th>
                        <th class="center">CANT. DISPONIBLE</th>
                        <th class="center">CANT. INGRESADA POR COMPRA</th>
                        <th class="center">FECHA ULTM. INGRESO COMPRA</th>
                        <th class="center">FECHA DE CREACION</th>
                        <th class="center">FECHA VENCIMIENTO</th>
                    </tr>
                <tbody>`;

	

	if ( $('#R'+bodega_).is(':visible')){
		$('#R'+ bodega_).hide()
	}else{
		$('#R'+ bodega_).show()


		var thead = tbody = '';            
        thead =`<table class="" width='100%'>
                    <tr>
                        <th class="center">LOTE</th>
                        <th class="center">CANT. DISPONIBLE</th>
                        <th class="center">CANT. INGRESADA POR COMPRA</th>
                        <th class="center">FECHA ULTM. INGRESO COMPRA</th>
                        <th class="center">FECHA DE CREACION</th>
                        <th class="center">FECHA VENCIMIENTO</th>
                    </tr>
                <tbody>`;
		$.ajax({
			type: "POST",
			url: "lotes",
			data:{
				bodega: bodega_,
				articulo: articulo_,
				Unidad: Unidad_,        
			},        
			success: function ( data ) {
				if (data.length==0) {
					tbody +=`<tr>
								<td colspan='6'><center>Bodega sin existencia</center></td>
							</tr>`;
					$('#'+ID).html(thead + tbody);
				}
				$.each(data, function (i, item) {
				tbody +=`<tr class="center">
								<td>` + item['LOTE'] + `</td>
								<td>` + item['CANT_DISPONIBLE'] + `</td>
								<td>` + item['CANTIDAD_INGRESADA'] + `</td>
								<td>` + item['FECHA_INGRESO'] + `</td>
								<td>` + item['FECHA_ENTRADA'] + `</td>
								<td>` + item['FECHA_VENCIMIENTO'] + `</td>
							</tr>`;
				});
				tbody += `</tbody></table>`;
				$("#" + ID).html(thead + tbody);
			}
		});
	}

}
</script>