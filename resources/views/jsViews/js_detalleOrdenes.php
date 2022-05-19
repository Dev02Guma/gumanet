<script>
	$(document).ready(function() {

		data();
		$('table > thead').addClass('bg-blue text-white');
		//$('#InputDt_PC').hide();

		$('#tipo_procceso').on('change', function() {
			var tipo = $(this).val();
			if (tipo == 1) {
				$('#dtOrdenes_pc').empty();
				$('#dtOrdenes_pc_paginate').empty();
				$('#InputDt_PC').hide();
				$('#InputDtShowSearchFilterArt').show();
				data();

			} else if (tipo == 2) {
				$('#dtDetalles').empty();
				$('#dtDetalles_paginate').empty();
				$('#InputDt_PC').show();

				$('#InputDtShowSearchFilterArt').hide();
				getOrdenesPC();
				$('#dtOrdenes_pc > thead').addClass('bg-blue text-white');

			}
		});

	});
	var numOrden_g = 0;
	var numOrden_pc = 0;

	function getMoreDetail(numOrden, descripcion, fechaInicio, fechaFin) {
		numOrden_g = numOrden;
		$("#tDetalleOrdenes").html(`<p class="text-white m-1">` + "#" + numOrden + "-" + descripcion + `</p>` + `<p class="text-white m-1">` + fechaInicio + " - " + fechaFin + `</p>`);
		getMateriaPrima(numOrden);
		getOtrosConsumos(numOrden);
		getDetailSumary(numOrden);
		var target = '#nav-mp';
		$('a[data-toggle=tab][href=' + target + ']').tab('show');

		$("#tbody1")
			.empty()
			.append(`<tr><td colspan='5'><center>Aún no ha realizado ninguna busqueda</center></td></tr>`);

		$("#mdDetalleOrd").modal('show');
	}

	$('#nav-tab a').click(function() {
		var idNav = $(this).attr('id');
		console.log(idNav);
		switch (idNav) {
			case 'navMP':
				getMateriaPrima(numOrden_g);
				break;
			case 'navMOD':
				getMOD(numOrden_g);
				break;
			case 'navQuimicos':
				getQuimicos(numOrden_g);
				break;
			case 'navCIF':
				getCIF(numOrden_g);
				break;
			case 'navCostos':
				getSubCostos(numOrden_g);
				break;
			case 'navHrsEfect':
				getHrsEfect(numOrden_g);
				break;
			default:
				alert('Al parecer alguio salio mal :(')
		}
	});

	//TABLA DE MP
	function getMateriaPrima(numOrden) {
		$("#tblMP").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getMateriaPrima/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
					"data": "maquina"
				},
				{
					"data": "fibra"
				},
				{
					"data": "cantidad"
				},
			],
			"columnDefs": [{
				"className": "dt-right",
				"targets": [2]
			}, ],
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}

		});
	}

	function getMOD(numOrden) {
		$("#tblMOD").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getMOD/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
					"data": "actividad"
				},
				{
					"data": "dia"
				},
				{
					"data": "noche"
				},
				{
					"data": "total"
				},
			],
			"columnDefs": [{
				"className": "dt-center",
				"targets": [1, 2, 3]
			}, ],
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}
		});
	}

	function getQuimicos(numOrden) {
		$("#tblQuimicos").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getQuimicos/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
					"data": "maquina"
				},
				{
					"data": "quimico"
				},
				{
					"data": "cantidad"
				},
			],
			"columnDefs": [{
					"className": "dt-center",
					"targets": [2]
				},
				{
					"width": "45%",
					"targets": [0]
				},

			],
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}
		});
	}

	function getCIF(numOrden) {
		$("#tblCIF").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getMOD/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
					"data": "actividad"
				},
				{
					"data": "total"
				},
			],
			"columnDefs": [{
					"className": "dt-center",
					"targets": [1]
				},
				{
					"width": "50%",
					"targets": [0]
				},
			],
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}
		});
	}

	function getSubCostos(numOrden) {
		$("#tblCostos").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getSubCostos/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
					"data": "codigo"
				},
				{
					"data": "descripcion"
				},
				{
					"data": "unidad_Medida"
				},
				{
					"data": "cantidad"
				},
				{
					"data": "costo_Unitario"
				},
				{
					"data": "costo_Total"
				},
			],
			"columnDefs": [{
					"className": "dt-center",
					"targets": [2]
				},
				{
					"className": "dt-right",
					"targets": [3, 4, 5]
				},
			],
			"footerCallback": function(row, data, start, end, display) {
				var api = this.api(),
					data;

				var intVal = function(i) {
					return typeof i === 'string' ?
						i.replace(/[\$,]/g, '') * 1 :
						typeof i === 'number' ?
						i : 0;
				};

				total = api
					.column(5)
					.data()
					.reduce(function(a, b) {
						return intVal(a) + intVal(b);
					}, 0);
				totalUnitario = api
					.column(4)
					.data()
					.reduce(function(a, b) {
						return intVal(a) + intVal(b);
					}, 0);

				$('#costoTotal').text('C$ ' + numeral(total).format('0,0.00'));
				$('#CT_Unitario').text('C$ ' + numeral(totalUnitario).format('0,0.00'));

			},
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}
		});
	}

	function getOtrosConsumos(numOrden) {
		texto = "3";
		suTexto = texto.sup().toString();
		$.ajax({
			type: 'GET',
			url: 'getOtrosConsumos/' + numOrden,
			dataType: "json",
			data: {},
			success: function(data) {
				count = Object.keys(data).length;
				data.forEach(element => {
					//Agua
					$('#AtotalConsumo').text(element.AtotalConsumo + " m").append(suTexto);
					$('#Ainicial').text(element.Ainicial);
					$('#Afinal').text(element.Afinal);
					//Electricidad
					if (parseFloat(element.E_ConsumoSTD) > 560.00) {
						$("#E_ConsumoSTD").css("color", "#FF0000");
					} else if (parseFloat(element.E_ConsumoSTD) <= 560 && parseFloat(element.E_ConsumoSTD) > 0) {
						$("#E_ConsumoSTD").css("color", "#02b841");
					} else if (parseFloat(element.E_ConsumoSTD) == 0) {
						$("#E_ConsumoSTD").css("color", "#000000");
					}
					$('#EtotalConsumo').text(element.EtotalConsumo + " Kwh");
					$('#Einicial').text(element.Einicial);
					$('#Efinal').text(element.Efinal);
					$('#E_ConsumoSTD').text(element.E_ConsumoSTD + " kw/ton"); // consumo standard
					$('#E_ConsumoPH').text(element.E_ConsumoPH + " kwh"); //proceso seco
					$('#E_ConsumoTTestimado').text(element.E_ConsumoTTestimado + " kwh");
					//Consumo de Gas	
					if (parseFloat(element.G_totalConsumoTon) > 145) {
						$("#G_totalConsumoTon").css("color", "#FF0000");
					} else if (parseFloat(element.G_totalConsumoTon) <= 145 && parseFloat(element.G_totalConsumoTon) > 0) {
						$("#G_totalConsumoTon").css("color", "#02b841");
					} else if (parseFloat(element.G_totalConsumoTon) == 0) {
						$("#G_totalConsumoTon").css("color", "#000000");
					}
					$('#GtotalConsumo').text(element.GtotalConsumo + " Glns");
					$('#G_totalConsumoTon').text(element.G_totalConsumoTon + " gln/ton");
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + '\r\n' +
					xhr.statusText + '\r\n' +
					xhr.responseText + '\r\n' +
					ajaxOptions);
			}
		});
	}

	function getDetailSumary(numOrden) {
		$.ajax({
			type: 'GET',
			url: 'getDetailSumary/' + numOrden,
			dataType: "json",
			data: {},
			success: function(data) {
				data.forEach(element => {
					$('#fechaInicio').text(element.fechaInicio);
					$('#fechaFinal').text(element.fechaFinal);
					$('#horaInicio').text(element.horaInicio);
					$('#horaFinal').text(element.horaFinal);
					$('#lav-tetrapack').text(element.lavadora_total + " kg");
					$('#residuos-pulper').text(element.residuo_total + " kg");
					$('#fechaFinal').text(element.fechaFinal);
					$('#merma-yankee-dry').text(element.merma_total + " kg");
					$('#hrsTrabajadas').text(element.hrsTrabajadas + " hrs");
					$('#produccionNeta').text(element.prod_real + " kg");
					$('#produccionReal').text(element.prod_total + " kg");
					// porcentajes
					if (parseFloat(element.factorFibral) > 1.3) {
						$("#factor-fibral").css("color", "#FF0000");
					} else if (parseFloat(element.factorFibral) <= 1.3 && parseFloat(element.factorFibral) > 0) {
						$("#factor-fibral").css("color", "#02b841");
					} else if (parseFloat(element.factorFibral) == 0) {
						$("#factor-fibral").css("color", "#000");
					}
					$('#factor-fibral').text(element.factorFibral + " %");
					$('#porcentaje_merma').text(element.porcentMermaYankeeDry + " %");
					$('#porcentaje_tpack').text(element.porcentLavadoraTetrapack + " %");
					$('#porcentaje_rp').text(element.porcentResiduosPulper + " %");
					//ultimos datos agregados
					$('#costoBolson').text(element.costoBolson);
					$('#bolsones').text(element.bolsones);
					$('#ton_dia').text(element.Tonelada_dia);

					if (parseFloat(element.Tonelada_dia) > 10) {
						$('#ton_dia').css("color", "#02b841");
					} else if (parseFloat(element.Tonelada_dia) <= 10 && parseFloat(element.Tonelada_dia) > 0) {
						$('#ton_dia').css("color", "#FF0000");
					} else if (parseFloat(element.Tonelada_dia) == 0) {
						$('#ton_dia').css("color", "#000000");
					}
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + '\r\n' +
					xhr.statusText + '\r\n' +
					xhr.responseText + '\r\n' +
					ajaxOptions);
			}
		});
	}

	function getHrsEfect(numOrden) {
		$("#tblHrsEfect").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getHrasProducidas/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
					"data": "nombre"
				},
				{
					"data": "dia"
				},
				{
					"data": "noche"
				},
				{
					"data": "total"
				},

			],
			"columnDefs": [{
					"className": "dt-center",
					"targets": [0]
				},
				{
					"className": "dt-right",
					"targets": [1, 2, 3]
				},
			],
			"footerCallback": function(row, data, start, end, display) {
				var api = this.api(),
					data;

				var intVal = function(i) {
					return typeof i === 'string' ?
						i.replace(/[\$,]/g, '') * 1 :
						typeof i === 'number' ?
						i : 0;
				};
				total3 = api.cells(0, 3).render('display').reduce(function(a, b) {
					return intVal(a) + intVal(b);
				}, 0);
				total = api.column(3).data().reduce(function(a, b) {
					return intVal(a) + intVal(b);
				}, 0);

				//$('#hrasTotales').text(numeral(total / 2).format('0,0.00') + ' Hras');
				$('#hrasTotales').text(numeral((total + total3) / 3).format('0,0.00') + ' Hras');
			},
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}
		});
	}

	// NEW TABLE DETALLE ORDENES

	function data() {

		$('#dtDetalles').DataTable({
			'ajax': {
				'url': 'getData',
				'dataSrc': '',
			},
			"destroy": true,
			"info": false,
			"language": {
				"zeroRecords": "NO HAY COINCIDENCIAS",
				"paginate": {
					"first": "Primera",
					"last": "Última ",
					"next": "Siguiente",
					"previous": "Anterior"
				},
				"infoFiltered": "(Filtrado de _MAX_ total entradas)",
				"loadingRecords": "Cargando datos...",
				"lengthMenu": "MOSTRAR _MENU_",
				"emptyTable": "REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS DE FECHA",
				"search": "BUSCAR"
			},
			'columns': [{
					"title": "Detalle",
					"data": "detalle_general"
				},
				{
					"title": "Año",
					"data": "anio"
				},
				{
					"title": "Mes",
					"data": "mes"
				},
				{
					"title": "Ordenes",
					"data": "contOrder"
				},
				{
					"title": "PROD.REAL KG",
					"data": "prod_real_total"
				},
				{
					"title": "PROD.TOTAL KG (Real + merma)",
					"data": "prod_total_total"
				},
				{
					"title": "PROD.REAL TON.",
					"data": "prod_real_ton_total"
				},
				{
					"title": "COSTO TOTAL C$",
					"data": "costo_total_total",
					"render": function(data, type, row) {
						return 'C$ ' + data;
					}
				},
				{
					"title": "COSTO TOTAL $",
					"data": "ct_dolar_total",
					"render": function(data, type, row) {
						return '$ ' + data;
					}
				},
				{
					"title": "COSTO TOTAL TON $",
					"data": "costo_real_ton_total",
					"render": function(data, type, row) {
						return '$ ' + data;
					}
				},
			],
			"columnDefs": [{
					"className": "dt-right",
					"targets": [5, 6, 7, 8, 9]
				},
				{
					"className": "dt-center",
					"targets": [0, 1, 2, 3, 4]
				}
			],

		});

		$("#dtDetalles_length").hide();
		$("#dtDetalles_filter").hide();
		$('#InputDtShowSearchFilterArt').on('keyup', function() {
			var table = $('#dtDetalles').DataTable();
			table.search(this.value).draw();
		});

	}
	$(document).on('click', '#exp_more', function(ef) {
		var table = $('#dtDetalles').DataTable();
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
			table.rows().eq(0).each(function(idx) {
				var row = table.row(idx);

				if (row.child.isShown()) {
					row.child.hide();
					ef.target.innerHTML = "expand_more";

					var c_1 = $(".expan_more");
					c_1.text('expand_more');
					c_1.css({
						background: '#e2e2e2',
						color: '#007bff',
					});
				}
			});

			format(row.child, data);
			tr.addClass('shown');

			ef.target.innerHTML = "expand_less";
			ef.target.style.background = '#ff5252';
			ef.target.style.color = '#e2e2e2';
		}
	});

	function format(callback, dta) {

		var thead = tbody = tNule = '';

		thead = `<table class="table table-striped table-bordered table-sm">
					<thead class="text-center text-white">
					<tr  class=" bg-secondary">
						<th class="center">N°.ORDEN</th>
						<th class="center">PRODUCTO</th>
						<th class="center">DESCRIPCION P.H EN O.P</th>
						<th class="center">FECHA INICIO </th>
						<th class="center">FECHA FINAL </th>	
						<th class="center">PROD.REAL </th>		
						<th class="center">PROD.TOTAL (REAL + MERMA) </th>		
						<th class="center">PROD.REAL TON</th>	
						<th class="center">COSTO TOTAL C$</th>	
						<th class="center">T.C</th>		
						<th class="center">COSTO TOTAL $</th>		
						<th class="center">COSTO.TON $</th>		
					</tr>
					</thead>
					<tbody>`;


		if (dta.length == 0) {
			tbody += `<tr>
						<td colspan='6'><center>Bodega sin existencia</center></td>
						</tr>`;
			callback(thead + tbody).show();
		}

		$.each(dta.Detalles, function(i, item) {
			tbody += '<tr>' +
				'<td class="text-center">' + item['numOrden'] + '</td>' +
				'<td class="text-center">' + item['producto'] + '</td>' +
				'<td class="text-center">' + item['descripcion'] + '</td>' +
				'<td class="text-center">' + item['fechaInicio'] + '</td>' +
				'<td class="text-center">' + item['fechaFinal'] + '</td>' +
				'<td class="text-right">' + item['prod_real'] + '</td>' +
				'<td class="text-right">' + item['prod_total'] + '</td>' +
				'<td class="text-right">' + numeral(item['prod_real_ton']).format('0,0.00') + '</td>' +
				'<td class="text-right"> C$ ' + numeral(item['costo_total']).format('0,0.0000') + '</td>' +
				'<td class="text-right"> C$ ' + numeral(item['tipo_cambio']).format('0,0.0000') + '</td>' +
				'<td class="text-right"> $ ' + numeral(item['ct_dolar']).format('0,0.0000') + '</td>' +
				'<td class="text-right">  $ ' + numeral(item['costo_real_ton']).format('0,0.0000') + '</td>' +
				'</tr>';
		});

		tbody += `</tbody></table>`;
		temp = `<div style="margin: 0 auto; height: auto; width:100%; overflow: auto">
					<pre dir="ltr" style="margin: 0px;padding:6px;">
						` + thead + tbody + `
					</pre>
					</div>
					`;
		callback(temp).show();

	}

	function getOrdenesPC() {

		$('#dtOrdenes_pc').DataTable({
			'ajax': {
				'url': 'getOrdenesPC',
				'dataSrc': '',
			},
			"destroy": true,
			"info": false,
			"language": {
				"zeroRecords": "NO HAY COINCIDENCIAS",
				"paginate": {
					"first": "Primera",
					"last": "Última ",
					"next": "Siguiente",
					"previous": "Anterior"
				},
				"infoFiltered": "(Filtrado de _MAX_ total entradas)",
				"loadingRecords": "Cargando datos...",
				"lengthMenu": "MOSTRAR _MENU_",
				"emptyTable": "REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS DE FECHA",
				"search": "BUSCAR"
			},
			'columns': [{
					"title": "DETALLE",
					"data": "detalle_general",
				},
				{
					"title": "AÑO",
					"data": "year_"
				},
				{
					"title": "MES",
					"data": "mes_"
				},
				{
					"title": "ORDENES",
					"data": "contOrder"
				},
				{
					"title": "TOTAL DE BULTOS",
					"data": "total_bultos"
				},
			],
			"columnDefs": [{
				"className": "dt-center",
				"targets": [0, 1, 2, 3, 4]
			}],

		});

		$("#dtOrdenes_pc_length").hide();
		$("#dtOrdenes_pc_filter").hide();
		$('#InputDt_PC').on('keyup', function() {
			var table = $('#dtOrdenes_pc').DataTable();
			table.search(this.value).draw();
		});
	}
	$(document).on('click', '#exp_more_pc', function(ef) {
		var table = $('#dtOrdenes_pc').DataTable();
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
			table.rows().eq(0).each(function(idx) {
				var row = table.row(idx);

				if (row.child.isShown()) {
					row.child.hide();
					ef.target.innerHTML = "expand_more";

					var c_1 = $(".expan_more");
					c_1.text('expand_more');
					c_1.css({
						background: '#e2e2e2',
						color: '#007bff',
					});
				}
			});

			format_pc(row.child, data);
			tr.addClass('shown');

			ef.target.innerHTML = "expand_less";
			ef.target.style.background = '#ff5252';
			ef.target.style.color = '#e2e2e2';
		}
	});

	function format_pc(callback, dta) {

		var thead = tbody = tNule = '';

		thead = `<table class="table table-striped table-bordered table-sm">
			<thead class="text-center text-white">
			<tr  class=" bg-secondary">
				<th class="center">N°.ORDEN</th>
				<th class="center">PRODUCTO</th>
				<th class="center">FECHA INICIO </th>
				<th class="center">FECHA FINAL </th>	
				<th class="center">HORAS TRABAJADAS</th>		
				<th class="center">PESO % </th>		
				<th class="center">TOTAL DE BULTOS (UNDS) </th>			
			</tr>
			</thead>
			<tbody>`;


		if (dta.length == 0) {
			tbody += `<tr>
				<td colspan='6'><center>Bodega sin existencia</center></td>
				</tr>`;
			callback(thead + tbody).show();
		}

		$.each(dta.Detalles, function(i, item) {
			tbody += '<tr>' +
				'<td class="text-center">' + item['num_orden'] + '</td>' +
				'<td class="text-center">' + item['nombre'] + '</td>' +
				'<td class="text-center">' + item['fecha_inicio'] + '</td>' +
				'<td class="text-center">' + item['fecha_final'] + '</td>' +
				'<td class="text-center">' + numeral(item['Hrs_trabajadas']).format('0,0.00') + '</td>' +
				'<td class="text-right">'  + numeral(item['PESO_PORCENT']).format('0,0.00') + '</td>' +
				'<td class="text-right">'  + numeral(item['TOTAL_BULTOS_UNDS']).format('0,0.00') + '</td>' +
				'</tr>';
		});

		tbody += `</tbody></table>`;
		temp = `<div style="margin: 0 auto; height: auto; width:100%; overflow: auto">
			<pre dir="ltr" style="margin: 0px;padding:6px;">
				` + thead + tbody + `
			</pre>
			</div>
			`;
		callback(temp).show();

	}

	function getDataGeneralPc(numOrden){
		$.ajax({
			type: 'GET',
			url: 'getDataGeneralPc/' + numOrden,
			dataType: "json",
			data: {},
			success: function(data) {
				data.forEach(element => {
					$('#total_bultos_pc').text(element.TOTAL_BULTOS);
					$('#hrs_trabajadas_pc').text(element.Hrs_trabajadas);
					$('#jr_total_pc').text(element.JR_TOTAL);
					$('#peso_pc').text(element.PESO_PORCENT);
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + '\r\n' +
					xhr.statusText + '\r\n' +
					xhr.responseText + '\r\n' +
					ajaxOptions);
			}
		});
	}
 
	function get_detail_pc(numOrden,id_producto, descripcion, fechaInicio, fechaFin) {
		getDataGeneralPc(numOrden);
		numOrden_pc = numOrden;
		//console.log(id_producto);
		$("#title_detail_pc").html(`<p class="text-white m-1">` + "#" + numOrden + "-" + descripcion + `</p>` + `<p class="text-white m-1">` + fechaInicio + " - " + fechaFin + `</p>`);
		getProductos_pc(numOrden);
		//getMateriaPrima_pc(numOrden);
		//tiempos_paros_pc(numOrden);
		var target = '#nav-prod-pc';
		$('a[data-toggle=tab][href=' + target + ']').tab('show');

		$("#tbody1")
			.empty()
			.append(`<tr><td colspan='5'><center>Aún no ha realizado ninguna busqueda</center></td></tr>`);

		$("#mdDetalleOrd_pc").modal('show');
	}


	$('#nav-tab-pc a').click(function() {
		var idNav = $(this).attr('id');
		console.log(idNav);
		switch (idNav) {
			case 'navProd-pc':
				getProductos_pc(numOrden_pc);
				break;
			case 'navMP_pc':
				getMateriaPrima_pc(numOrden_pc);
				break;
			case 'navTiemposParos':
				tiempos_paros_pc(numOrden_pc);
				break;
			default:
				alert('Al parecer alguio salio mal :(')
		}
	});

	function getProductos_pc(numOrden){
		$("#tblProductos_pc").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getProd_pc/" + numOrden	,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
			     	"title": "ID",
					"data": "ID_ARTICULO"
				},
				{
			     	"title": "ARTICULO",
					"data": "ARTICULO"
				},
				{
					"title": "DESCRIPCION",
					"data": "DESCRIPCION_CORTA"
				},{
					"title": "BULTOS",
					"data": "BULTOS"
				}
				,{
					"title": "PESO %",
					"data": "PESO_PORCENTUAL"
				}
				,{
					"title": "KG",
					"data": "KG"
				}
			],
			"columnDefs": [{
				"className": "dt-center",
				"targets": [1]
			},{
				"className": "dt-right",
				"targets": [3,4,5]
			},{
                "targets": [0],
                "className": "dt-center",
                "visible": false
            }
		 ],
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}

		});
	}

	function getMateriaPrima_pc(numOrden){

		$("#tblMateriaPrima_pc").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getMP_PC/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [{
			     	"title": "ID",
					"data": "ID_ARTICULO"
				},
				{
					"title": "ARTICULO",
					"data": "ARTICULO"
				},
				{
					"title": "DESCRIPCION",
					"data": "DESCRIPCION_CORTA"
				},
				{
					"title": "REQUISA",
					"data": "REQUISA"
				},	{
					"title": "PISO",
					"data": "PISO"
				},	{
					"title": "CONSUMO",
					"data": "CONSUMO"
				},	{
					"title": "MERMA",
					"data": "MERMA"
				},	{
					"title": "MERMA %",
					"data": "MERMA_PORCENTUAL"
				}
			],
			"columnDefs": [{
				"className": "dt-right",
				"targets": [3,4,5,6,7]
			},{
                "targets": [0],
                "className": "dt-center",
                "visible": false
            },{
                "targets": [1],
                "className": "dt-center",
            } ],
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}

		});

	}

	function tiempos_paros_pc(numOrden){
		$("#tblTiemposParos_pc").dataTable({
			responsive: true,
			"autoWidth": false,
			"ajax": {
				"url": "getTiempos_paros/" + numOrden,
				'dataSrc': '',
			},
			"searching": false,
			"destroy": true,
			"paging": false,
			"columns": [
				{
			     	"title": "ID",
					"data": "ID_ROW"
				},
				{
			     	"title": "DESCRIPCION DE LA ACTIVIDAD",
					"data": "ARTICULO"
				},
				{
					"title": "DIA",
					"data": "Dia"
				},
				{
					"title": "NOCHE",
					"data": "Noche"
				},	{
					"title": "TOTAL HRS",
					"data": "Total_Hrs"
				},	{
					"title": "No. Personas",
					"data": "num_personas"
				}
			],
			"columnDefs": [{
				"className": "dt-right",
				"targets": [2,3,4,5]
			}, {
                "targets": [0],
                "className": "dt-center",
                "visible": false
            }],
			"info": false,
			"language": {
				"zeroRecords": "No hay datos que mostrar",
				"emptyTable": "N/D",
				"loadingRecords": "Cargando...",
			}
		});
	}

</script>