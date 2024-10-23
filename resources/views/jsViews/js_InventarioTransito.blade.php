<script>
	var TableExcel;
	dta_table_excel = [];
	var isError = false
$(document).ready(function() {
    fullScreen();
    inicializaControlFecha();
    $("#item-nav-01").after(`<li class="breadcrumb-item active"><a href="{{url('../Inventario')}}">Inventario</a></li><li class="breadcrumb-item active">Inventario completo</li>`);

	InitTable();
	

	$('#InputDtShowSearchFilterArt').on( 'keyup', function () {
        var table = $('#dtInvCompleto').DataTable();
        table.search(this.value).draw();
	});

	$('#id_txt_excel').on('keyup', function() {    
		if(isValue(TableExcel,0,true)){
			TableExcel.search(this.value).draw();
		}
	});

	$( "#InputDtShowColumnsArtic").change(function() {
        var table = $('#dtInvCompleto').DataTable();
        table.page.len(this.value).draw();
	});
	$("#btn_add_con_codigo").click(function(){

		var Articulo   	  = $("#frm_select_articulo option:selected").val();  
		try {					
			$.ajax({
				url: "../../SaveTransitoConCodigo",
				data: {
					Articulo  : Articulo,
					_token  : "{{ csrf_token() }}" 
				},
				type: 'post',
				async: true,
				success: function(response) {

					location.reload();
					
				},
				error: function(response) {
					Swal.fire("Oops", "No se ha podido guardar!", "error");
				}
			}).done(function(data) {
			});

		} catch (error) {
			Swal.showValidationMessage(`Request failed: ${error}`);
		}

	})

	$("#btn_add_item").click(function(){

		var id = $("#id_frm_show").text();		

		if (id == 1) {
			$("#id_dml_add_articulo").modal('show');
		} else {
			Swal.fire({
			input: "textarea",
			inputLabel: "Nuevo Articulo",
			inputPlaceholder: "Nombre del Artiulo nuevo...",
			inputAttributes: {
				"aria-label": "Type your message here"
			},
			showCancelButton: true,
			confirmButtonText: "Guardar",
			showLoaderOnConfirm: true,
			preConfirm: async (Transito) => {
				try {					
					$.ajax({
						url: "../../SaveTransitoNew",
						data: {
							Articulo  : Transito,
							_token  : "{{ csrf_token() }}" 
						},
						type: 'post',
						async: true,
						success: function(response) {
							
						},
						error: function(response) {
							Swal.fire("Oops", "No se ha podido guardar!", "error");
						}
					}).done(function(data) {
					});
        
				} catch (error) {
					Swal.showValidationMessage(`Request failed: ${error}`);
				}
			},
			allowOutsideClick: () => !Swal.isLoading()
			}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: "Articulo Fue Agregado"
				});
				InitTable()
			}
		});
		}

		
	})


});
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
function InitTable(){
	$(".text-danger").hide();

	var id = $("#id_frm_show").text();


	$('#dtInvCompleto').DataTable({
		"ajax":{
			"url": "../../getTransito/" + id,
			'dataSrc': '',
		},
		'destroy' : true,
		'info': false,
		"lengthMenu": [[10,20,30,40,-1], [10,20,30,40,"Todo"]],
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
		
				return`<a href="#!" id="idArticulo" onclick="getDetalleArticulo(`+ "'" +row.ARTICULO + "'" +` , ` + "'" +row.DESCRIPCION + "'" +`,`+ "'" +row.ID + "'" +`)" >`+ row.ARTICULO +`</a>`
				
			}},
			{"title": "DESCRIPCIÓN", 		"data": "DESCRIPCION"},
            {"title": "FECHA ESTIMADA", "data": "FECHA_ESTIMADA" },
            {"title": "FECHA PEDIDO", "data": "FECHA_PEDIDO" },
			{"title": "MERCADO", "data": "MERCADO" },
            {"title": "PEDIDO", "data": "PEDIDO" },
			{"title": "TRANSITO", "data": "TRANSITO" },
		],
		"columnDefs": [
			{"className": "dt-center", "targets": [0, 2,3,4 ]},
			{"className": "dt-right", "targets": [5,6]},
			{"className": "dt-left", "targets": [1]},
			{"width":"20%","targets":[]},
			{"width":"5%","targets":[]}
		],
    });
    $("#dtInvCompleto_length").hide();
    $("#dtInvCompleto_filter").hide();

	
}
function getDetalleArticulo(Articulo,Descripcion,ID) 
{

	$("#txtNumRow").html(ID)
	$("#txtArticulo").val(Articulo)
	$("#txtDescripcion").val(Descripcion)

	$("#date_estimada").val("")
	$("#date_pedido").val("")
	$("#txtDocuments").val("")
	$("#txtCantidad").val("")
	$("#txtCantidadTransito").val("")
	$("#slcMercado").val('N/D').change();
    $("#slcMIFIC").val('N/D').change();
	$("#txtObservacion").val("")
	$("#txtPrecioMific").val("")
	

	try {					
		$.ajax({
			url: "../../getInfoArticulo",
			data: {
				ID_ROW 	: ID,
				_token  : "{{ csrf_token() }}" 
			},
			type: 'post',
			async: true,
			success: function(object) {
				
				var a = object.data[0]
				a = isValue(a,0,true)
				if (a !=0 ) {

					var FechaPedido = moment(a.fecha_pedido, 'YYYY-MM-DD');
					var FechaEstimada = moment(a.fecha_estimada, 'YYYY-MM-DD');					

					$("#date_estimada").val(FechaEstimada.format('YYYY-MM-DD'))
					$("#date_pedido").val(FechaPedido.format('YYYY-MM-DD'))
					$("#txtDocuments").val(a.documento)
					$("#txtCantidad").val(a.cantidad_pedido)
					$("#txtCantidadTransito").val(a.cantidad_transito)
					$("#slcMercado").val(a.mercado).change();
					$("#slcMIFIC").val(a.mific).change();
					$("#select_estado").val(a.estado_compra).change();					
					$("#txtObservacion").val(a.observaciones)
					$("#txtPrecioMific").val(numeral(a.Precio_mific_farmacia).format('0,0.0000'))
					$("#txtPrecioMificPublic").val(numeral(a.Precio_mific_public).format('0,0.0000'))
					
					
				}
			},
			error: function(response) {
				Swal.fire("Oops", "No se ha podido guardar!", "error");
			}
		}).done(function(data) {
		});

	} catch (error) {
		Swal.showValidationMessage(`Request failed: ${error}`);
	}

	var target = '#nav-bod';
	$('a[data-toggle=tab][href=' + target + ']').tab('show');
	$("#mdDetalleArt").modal('show');
	$(".text-danger").hide();
}

new Vue({
	el: '#id_form_save',
	methods: {
		SaveInformacion() {
			$(".text-danger").hide();

			let formData = new FormData();

			formData.append('Articulo', document.getElementById('txtArticulo').value);
			formData.append('Descripcion', document.getElementById('txtDescripcion').value);
			formData.append('NumRow', document.getElementById('txtNumRow').innerHTML);

			formData.append('fecha_estimada', document.getElementById('date_estimada').value);
			formData.append('fecha_pedido', document.getElementById('date_pedido').value);


			formData.append('documento', document.getElementById('txtDocuments').value);
			formData.append('cantidad', document.getElementById('txtCantidad').value);
			formData.append('CantidadTransito', document.getElementById('txtCantidadTransito').value);
			formData.append('mercado', document.getElementById('slcMercado').value);
			formData.append('mific', document.getElementById('slcMIFIC').value);
			formData.append('select_estado', document.getElementById('select_estado').value);
			formData.append('precio_mific_f', document.getElementById('txtPrecioMific').value);
			formData.append('precio_mific_p', document.getElementById('txtPrecioMificPublic').value);
			formData.append('observaciones', document.getElementById('txtObservacion').value);

			axios.post('{{ route("SaveTransito") }}', formData)
				.then(response => {						
					Swal.fire({
						title: 'Correcto',
						text: response.data.message,
						icon: 'success',
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'OK'
						}).then((result) => {
							InitTable();
							// if (result.isConfirmed) {								
							// 	mensaje('Informacion Guardada', 'success');
							// }   
						})
				}).catch(e => {
					if (e.response.status === 422) {
						var eLabel = this.errors = e.response.data.errors;

						Object.entries(eLabel).forEach(([key, value]) => {
							$("#alert_" + key).show();
							$("#alert_" + key).html(value[0]);

						})


					}
					// Manejo de errores
					//alert('Error al guardar la información');
				});
		},
		DeleteInformacion(){
			let NumRow =document.getElementById('txtNumRow').innerHTML;

			Swal.fire({
				title: 'Eliminar Articulo',
				text: "El articulo sera eliminado y no se podra recuperar ¿Desea continuar?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Si!',
				target:"",
				showLoaderOnConfirm: true,
				preConfirm: async (Transito) => {
					$.ajax({
						url: "../../DeleteArticuloTransito",
						type: 'post',
						data: {
							NumRow      : NumRow
						},
						async: true,
						success: function(response) {
							if(response.original){
								Swal.fire({
								title: 'Articulo Eliminado',
								icon: 'success',
								showCancelButton: false,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#d33',
								confirmButtonText: 'OK'
								}).then((result) => {
									if (result.isConfirmed) {
										location.reload();
									}
								})
							}
						},
						error: function(response) {
						}
					}).done(function(data) {
						
					});
				},
				allowOutsideClick: () => !Swal.isLoading()
			})
		}
	}
});

function isNumeric(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
}

function validateInput(input) {
    input.value = input.value.replace(/[^0-9,.-]+/g, '');
}

var Selectors = {
	TABLE_UPLOARD: '#modal_upload',
};

$("#btn_upload").click(function(){
	var addMultiRow = document.querySelector("#modal_upload");
	var modal = new window.bootstrap.Modal(addMultiRow);
	modal.show();
});

$('#frm-upload').on("change", function(e){ 
	handleFileSelect(e)
});

function handleFileSelect(evt) {    
	var files = evt.target.files;
	var xl2json = new ExcelToJSON();
	xl2json.parseExcel(files[0]);
}

var ExcelToJSON = function() {

	this.parseExcel = function(file) {
	var reader = new FileReader();

	reader.onload = function(e) {
		var data = e.target.result;
		var workbook = XLSX.read(data, {type: 'binary'});
		dta_table_excel = [];

		workbook.SheetNames.forEach(function(sheetName) {

			isError=false;

			var worksheet = workbook.Sheets[sheetName];
			var range = XLSX.utils.decode_range('A1:Q200');
			var rows = XLSX.utils.sheet_to_json(worksheet, {range: range});
			
		
			rows.forEach(function(row) {
				
				var rowArray = Object.entries(row).map(function(x) {return x[1]});
				var isDate = isValue(rowArray[1],'N/D',true) 

				if(isDate!='N/D' && isDate.length > 10){

						var fechaPedido 	= dtFormat(rowArray[7]);
						var fechaEstimada 	= dtFormat(rowArray[10]);

						var isOK = (rowArray.length < 17 )? 'N' : 'S';

						isError = (isOK == 'N')? true : false;

						dta_table_excel.push({
							ARTICULO	: rowArray[0] || 'N/D',
							DESCRIPC	: rowArray[1] || 'N/D',
							CANTIDAD	: numeral(rowArray[5]).format('0,0') || 'N/D',
							dtPedido	: fechaPedido || 'N/D',
							dtEstimada	: fechaEstimada || 'N/D',
							Mercado		: rowArray[13] ||'N/D',
							Mific		: rowArray[16] ||'N/D',
							Documento	: rowArray[12] ||'N/D',
							Pre_MIFIC_F	: 0,
							Pre_MIFIC_P	: 0,
							Comment		: rowArray[15] ||'N/D',
							isOK		: isOK
						})
				}
				
			})

		});

		dta_table_header = [
			{"title": "ARTICULO","data": "ARTICULO"},
			{"title": "DESCRIPCION","data": "DESCRIPC"}, 
			{"title": "CANTIDAD","data": "CANTIDAD"},     
			{"title": "DOCUMENTO","data": "Documento"},                                     
			{"title": "FECHA PEDIDO","data": "dtPedido"},
			{"title": "FECHA ESTIMADA","data": "dtEstimada"},
			{"title": "MERCADO","data": "Mercado"},
			{"title": "MIFIC","data": "Mific"},
			{"title": "PRECIO MIFIC","data": "Pre_MIFIC"},
			{"title": "COMENTARIO","data": "Comment"},
			{"title": "","data": "isOK"}
		]
		dta_columnDefs = [
			{"className": "dt-center", "targets": [0,3,4,5,6,7,8]},
			{"className": "dt-right", "targets": [2]},
			{"visible"  : false, "searchable": false,"targets": [10] }
		]
		table_render('#tbl_excel',dta_table_excel,dta_table_header,dta_columnDefs,false)
	};

	reader.onerror = function(ex) {

	};

	reader.readAsBinaryString(file);

	};
};
function dtFormat(fecha) {
    return (fecha.indexOf('N/') !== -1) ? fecha : moment(fecha, 'M/D/YY').format('YYYY-MM-DD');
}
function table_render(Table,datos,Header,columnDefs,Filter)
{

	TableExcel = $(Table).DataTable({
		"data": datos,
		"destroy": true,
		"info": false,
		"bPaginate": true,
		"order": [
			[0, "DESC"]
		],
		"lengthMenu": [
			[5, -1],
			[5, "Todo"]
		],
		"language": {
			"zeroRecords": "NO HAY COINCIDENCIAS",
			"paginate": {
				"first": "Primera",
				"last": "Última ",
				"next": "Siguiente",
				"previous": "Anterior"
			},
			"lengthMenu": "MOSTRAR _MENU_",
			"emptyTable": "-",
			"search": "BUSCAR"
		},
		'columns': Header,
		"columnDefs": columnDefs,
		rowCallback: function( row, data, index ) {
			console.log(data)
			if ( data.isOK == 'N' ) {
				$(row).addClass('table-danger');
			} 
		}
	});
	if(!Filter){
		$(Table+"_length").hide();
		$(Table+"_filter").hide();
	}

}

$("#id_send_data_excel").click(function(){ 
	if(!isError){
		Swal.fire({
			title: '¿Estas Seguro de cargar  ?',
			text: "¡Se cargara la informacion previamente visualizada!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Si!',
			target: document.getElementById('mdlMatPrima'),
			showLoaderOnConfirm: true,
			preConfirm: () => {
				$.ajax({
					url: "SaveTransitoExcel",
					data: {
						datos   : dta_table_excel,
						_token  : "{{ csrf_token() }}" 
					},
					type: 'post',
					async: true,
					success: function(response) {
					console.log(response)
						if(response){
							Swal.fire({
								title: 'Articulos Ingresados Correctamente ' ,
								icon: 'success',
								showCancelButton: false,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#d33',
								confirmButtonText: 'OK'
								}).then((result) => {
								if (result.isConfirmed) {
									location.reload();
									}
								})
							}
						},
					error: function(response) {
						//Swal.fire("Oops", "No se ha podido guardar!", "error");
					}
					}).done(function(data) {
						//CargarDatos(nMes,annio);
					});
				},
			allowOutsideClick: () => !Swal.isLoading()
		});
	}else{
		Swal.fire({
			icon: 'error',
			title: 'Oops...',
			text: "Existen Filas con espacios Vacios ",
			
		})
	}


	
});

</script>