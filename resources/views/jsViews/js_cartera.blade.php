<script>
$(document).ready(function() {
    fullScreen();
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Viñetas</li>`);

    dataVinneta(0,0,'','');
    inicializaControlFecha();

});
$(document).on('click', '.img-fluid', function (e) {
	url_image = $(this).attr('src');
	swal({
		showCloseButton: true,
		showConfirmButton: false,
		imageUrl: url_image,
		imageAlt: 'Custom image'
	})

	$(".swal2-popup").css('width', '50%');
})

$('#InputDtShowSearchFilterInvTotal').on( 'keyup', function () {
    var table = $('#dtInventarioTotal').DataTable();
    table.search(this.value).draw();
});

$( "#InputDtShowColumnsInvTotal").change(function() {
    var table = $('#dtInventarioTotal').DataTable();
    table.page.len(this.value).draw();
});

$( "#txt-fondo-inicial").on( 'keyup',function() {
    var subTotal = $('#id-mdl-subtotal').text();

    subTotal     = subTotal.replace(/\s/g, '').replace(',','')
    var dmlTotal = parseFloat(this.value)  - parseFloat(subTotal)

    dmlTotal = numeral(dmlTotal).format('0,0.00')
    
    $('#id-dml-disponible').html(dmlTotal)
    
});

$("#exp-to-excel").click( function() {
    location.href = "desInvTotal";
});

function Requestdata(){

    f1      = $("#f1").val();
    f2      = $("#f2").val();
    Ruta    = $("#dtRutas").val();
    Stat    = $("#dtStatus").val();
    Opt     = $('input[name=inlineRadioOptions]:checked', '#FrmOptns').val();

    
    dataVinneta(f1, f2,Ruta,Stat);
}

function dataVinneta(f1, f2,Ruta,Stat) {

$('#dtVinneta').DataTable({
    'ajax':{
        'url':'getCartera',
        'dataSrc': '',
        data: {
            'f1' : f1,
            'f2' : f2,
            'RU' : Ruta,
            'St' : Stat,
        }
    },
    "destroy" : true,
    "info":    false,
    "lengthMenu": [[30,-1], [30,"Todo"]],
    "language": {
        "zeroRecords": "NO HAY COINCIDENCIAS",
        "paginate": {
            "first":      "Primera",
            "last":       "Última ",
            "next":       "Siguiente",
            "previous":   "Anterior"
        },
        "lengthMenu": "MOSTRAR _MENU_",
        "emptyTable": "REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS DE FECHA",
        "search":     "BUSCAR"
    },
    'columns': [
        { "title": "",                      "data": "DETALLE","className":'text-center detalles-rutas-recibos'},
        { "title": "VENDEDOR",              "data": "VENDEDOR" },
        { "title": "NOMBRE",                "data": "NOMBRE" },
        { "title": "GRUPO",                "data": "GRUPO" },
        
        { "title": "CREADO POR VENDEDOR",                 "data": "SUM_INGRESS" ,render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' )},
        { "title": "INGRESADO A EXACTUS",                 "data": "SUM_PROCESS" ,render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' )},
        { "title": "TOTAL",                 "data": "MONTO" ,render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' )},

        { "title": "REC. CREADO POR VENDEDOR",       "data": "COUNT_INGRESS" ,render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
        { "title": "REC. INGRESADO A SISTEMA",       "data": "COUNT_PROCESS" ,render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
        { "title": "REC. ANULADO",       "data": "COUNT_ANULA" ,render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},
        { "title": "REC. TOTAL",    "data": "COUNT_TOTAL" ,render: $.fn.dataTable.render.number( ',', '.', 0  , '' )},

    ],
    "columnDefs": [
        {"className": "dt-center", "targets": [1,3,4,5,6,7,8,9,10 ]},
        {"className": "dt-left", "targets": [2]},
        { "width": "8%", "targets": [0,1,3,4,5] },
        { "width": "12%", "targets": [ 2 ] },
        { "visible":false, "searchable": false,"targets": [] }
    ],
    "createdRow": function( row, data, dataIndex ) {
    },
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api();
        var Total       = 0;
        var Pendiete    = 0;
        var Ingresado   = 0;
        var intVal = function ( i ) {
            return typeof i === 'string' ?
            i.replace(/[^0-9.]/g, '')*1 :
            typeof i === 'number' ?
            i : 0;
        };
       
        Pendiete = api.column( 3 ).data().reduce( function (a, b){
            return intVal(a) + intVal(b);
        }, 0 );
        Ingresado = api.column( 4 ).data().reduce( function (a, b){
            return intVal(a) + intVal(b);
        }, 0 );
        total = api.column( 5 ).data().reduce( function (a, b){
            return intVal(a) + intVal(b);
        }, 0 );

        $('#id_valor_pendiente').text("C$ " + numeral(Pendiete).format('0,0.00'));
        $('#id_valor_ingresado').text("C$ " + numeral(Ingresado).format('0,0.00'));
        $('#id_valor_Total').text("C$ " + numeral(total).format('0,0.00'));
    },
});

$("#dtVinneta_length").hide();
$("#dtVinneta_filter").hide();
}

$("#BuscarVinneta").click( function() {
    Requestdata()
});
function attach_file(idRecibo){
    CardConten = '' ;
    CardConten  = '';
    vBody       = '';

    $.ajax({
        url: 'getAttachFile',
        type: 'post',
        data: {
            iRecibo     : idRecibo
        },
        async: true,
        success: function(response) {

        if (response.length==0) {
            CardConten = `
							<div class="card-body">
								<p class="text-center font-weight-bolder">No se encontraron registros</p>
								<center><img src="./images/icon_sinresultados.png" width="100" class="mt-4 mb-4" /></center>
							</div>`;
        }		

        $.each(response, function (i, item) {

            CardConten +='<div class="col"><div class="card border-light mb-3 shadow-sm bg-white rounded">'+
			'<div class="card-body">'+
				
                    '<img src="'+item.IMAGEN+'" width="200" class="img-fluid rounded" style="cursor: pointer" />'+
                
			'</div></div></div>';

        });

        vBody ='<div class="container">'+
			'<div class="row">'+
				
                CardConten+
                
			'</div>'+
		'</div>';

        $('#id_contenido_history').html(vBody);


        }
    })
}






$('#txtSearch').on( 'keyup', function () {
    var table = $('#dtVinneta').DataTable();
    table.search(this.value).draw();
});



function Delete(Id){
    $.ajax({
        url: 'Deleteliq',
        type: 'post',
        data: {
            id : Id
        },
        async: true,
        success: function(response) {            
            Requestdata();
        }
    })
}
function Aprobado(Id){
    $.ajax({
        url: 'push_recibo',
        type: 'post',
        data: {
            id :Id
        },
        async: true,
        success: function(response) {

            Requestdata()
        }
    })
}
function Verificado(Id){
    $.ajax({
        url: 'push_verificado',
        type: 'post',
        data: {
            id :Id
        },
        async: true,
        success: function(response) {

            Requestdata()
        }
    })
}
function open_modal_anulacion(Id){
    $('#message-text').html("")

    $('#mdlAnulacion').modal('show');

    $('#id_request').html(Id);

    $('#id_request').hide();

    
}



$("#id_frm_save_anulacion").click( function() {

    var Id      = $('#id_request').html();

    var mess    = $('#message-text').val();


    $.ajax({
        url: 'cancelarliq',
        type: 'post',
        data: {
            id : Id,
            me : mess
        },
        async: true,
        success: function(response) {
            
            Requestdata()

            $('#mdlAnulacion').modal('hide')

        }
    })

});


//DETALLES DE RECIBOS
$('#dtVinneta').on('click', 'td.detalles-rutas-recibos', function (ef) {
    var table   = $('#dtVinneta').DataTable();
    var tr      = $(this).closest('tr');
    var row     = table.row(tr);
    var data    = table.row($(this).parents('tr')).data();
    var Ruta    = $("#dtRutas").val();

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

        
        console.log(Ruta + " -> "+ data.VENDEDOR )
        

        if(Ruta == data.VENDEDOR){
            format(row.child,data);            
            tr.addClass('shown');            
            ef.target.innerHTML = "expand_less";
            ef.target.style.background = '#ff5252';
            ef.target.style.color = '#e2e2e2';
        }else{
            if(Ruta == ""){
                format(row.child,data);            
                tr.addClass('shown');            
                ef.target.innerHTML = "expand_less";
                ef.target.style.background = '#ff5252';
                ef.target.style.color = '#e2e2e2';
            }
        }
    }
});
function getIndex(value,Array) {

    let countLayer = Array.length;
    for(var x = 0 ; x < countLayer ; x++){
        

        if(Array[x][1] == value){
            return Object[x];
        }

    }

    return null;
}
function DetalleRecibo(id){
    
    $('#mdlResumen').modal('show');

    attach_file(id)


    $.ajax({
        url: "getOneRecibos",
        data: {
            'id_recibo' : id
        },
        cache: false,
        type: "GET",
        success: function(dta) {

            $("#id-form-ruta").text(dta[0]['VENDEDOR'])
            $("#id-form-ruta-name").text(dta[0]['NOMBREV'])
            $("#id-form-time").text(dta[0]['FECHA'])
            $("#id-form-Total").text(dta[0]['TOTAL'])
            $("#id-coment").text(dta[0]['COMMENT'])

            

            var thead = tbody = tNule = '';

            
            $.each(dta[0]['DETALLES'], function (i, item) {

                var clssAnulado = (item['TIPO']==='ANULADO') ? "1" : "";

                
                tbody +='<tr class="'+clssAnulado+'">'+
                            '<td class="text-center  ">' + item['FACTURA'] + '</td>'+
                            '<td class="text-center">C$ ' + item['VALORFACTURA'] + '</td>'+
                            '<td class="text-center">C$ ' + item['NOTACREDITO'] + '</td>'+
                            '<td class="text-center">C$ ' + item['RETENCION'] + '</td>'+
                            '<td class="text-center">C$ ' + item['DESCUENTO'] + '</td>'+
                            '<td class="text-center">C$ ' + item['VALORRECIBIDO'] + '</td>'+
                            '<td class="text-center">' + item['TIPO'] + '</td>'+
                        '</tr>';

                });
            temp = `<table class="table table-striped table-bordered table-sm post_back mt-3 expand_more" width="100%" id="">
					<thead class="bg-blue text-light">
                        <tr>
                            <th class="center">FACTURA</th>
                            <th class="center">VALOR FACTURA</th>
                            <th class="center">VALOR N/C</th>
                            <th class="center">RETENCION</th>
                            <th class="center">DESCUENTO</th>
                            <th class="center">VALOR RECIBIDO</th> 
                            <th class="center">TIPO</th> 
                        </tr>
                    </thead>
                    <tbody>
                    ` + tbody+ `
                    </tbody>
				</table>`

                $("#dtViewLiquidacion").empty().append(temp);

        },
        error: function(xhr) {

        }
    });


}
function detalle_recibo ( callback, dta ) {    

    var thead = tbody = tNule = '';

    thead =`<table class="table table-striped table-bordered table-sm">
            <thead class="text-center bg-secondary text-light">
                <tr>
                    <th class="center">FACTURA</th>
                    <th class="center">VALOR FACTURA</th>
                    <th class="center">VALOR N/C</th>
                    <th class="center">RETENCION</th>
                    <th class="center">DESCUENTO</th>
                    <th class="center">VALOR RECIBIDO</th>
                    <th class="center">SALDO</th>
                    <th class="center">TIPO</th>
                    
                </tr>
            </thead>
            <tbody>`;
            

    if (dta.length==0) {
        tbody +=`<tr>
                    <td colspan='6'><center>Bodega sin existencia</center></td>
                </tr>`;
        callback(thead + tbody).show();
    }

    $.each(dta.DETALLES, function (i, item) {

        var total = item['VALORFACTURA'] - item['NOTACREDITO'] - item['RETENCION'] - item['DESCUENTO'] - item['VALORRECIBIDO'];


        tbody +='<tr>'+
                    '<td class="text-center">' + item['FACTURA'] + '</td>'+
                    '<td class="text-center">C$ ' + numeral(item['VALORFACTURA']).format('0,0') + '</td>'+
                    '<td class="text-center">C$ ' + numeral(item['NOTACREDITO']).format('0,0') + '</td>'+
                    '<td class="text-center">C$ ' + numeral(item['RETENCION']).format('0,0') + '</td>'+
                    '<td class="text-right">C$ ' + numeral(item['DESCUENTO']).format('0,0.00')  + '</td>'+
                    '<td class="text-right">C$ ' + numeral(item['VALORRECIBIDO']).format('0,0.00')  + '</td>'+
                    '<td class="text-right">C$ ' + numeral(total).format('0,0.00')  + '</td>'+
                    '<td class="text-center">' + item['TIPO']  + '</td>'+
                '</tr>';

    });


    tbody += `</tbody></table>`;

    if( dta.COMMENT_ANUL.length != 0 ){

        tNule = `<div class="col-sm-12 mt-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title" id="numero_factura">`+dta.COMMENT_ANUL+`</h5>								
                            </div>
                        </div>
                    </div>`
    }
    temp = `
        <div style="margin: 0 auto; height: auto; width:100%; overflow: auto">
        <pre dir="ltr" style="margin: 0px;padding:6px;">
            `+thead+tbody+`
        </pre>
        </div>
        <div class="row mt-3">
                    
                    
                    <div class="col-sm-12">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title" id="MontoVinneta">`+dta.COMMENT+`</h5>
                                <p class="card-text">EN CONCEPTO DE:</p>
                            </div>
                        </div>
                    </div>	
                    `+ tNule+`
                </div>
        `;
    callback(temp).show();
        
    }

function format ( callback, dta ) {   
    
    f1      = $("#f1").val();
    f2      = $("#f2").val();
    Stat    = $("#dtStatus").val();
    Opt     = $('input[name=inlineRadioOptions]:checked', '#FrmOptns').val();
    Ruta    = dta.VENDEDOR; 
    
    
    $.ajax({
        url: "getRecibos",
        data: {
            'f1' : f1,
            'f2' : f2,
            'RU' : Ruta,
            'CL' : '',
            'St' : Stat,
        },
        cache: false,
        type: "GET",
        success: function(dta) {
            var thead = tbody = tNule = '';

            
            $.each(dta, function (i, item) {

                var clssColorRow = '';

                if(item['STATUS'] ==4){
                    clssColorRow = "tbl_rows_recibo_color";
                }else{
                    if(item['STATUS']=="Ingresado"){
                        clssColorRow = "tbl_rows_recibo_ingress";
                    }
                }                

                console.log(clssColorRow)

                tbody +='<tr class="'+clssColorRow+'">'+
                            '<td class="text-center exp_detalle_recibo"><a onclick="DetalleRecibo('+item['ID']+')" href="#!"><i class="material-icons exp_detalle_recibo">open_in_new</i></a></td>'+
                            '<td class="text-center">' + item['RECIBO'] + '</td>'+
                            '<td class="text-center">' + item['CLIENTE'] + '</td>'+
                            '<td class="text-left">' + item['NOMBRE_CLIENTE'] + '</td>'+
                            '<td class="text-center">' + item['FECHA'] + '</td>'+
                            '<td class="text-center">' + item['TOTAL'] + '</td>'+
                            '<td class="text-center">' + item['ADBJ'] + '</td>'+
                        '</tr>';

                });
            temp = `<table class="table table-striped table-bordered table-sm post_back mt-3 expand_more" width="100%" id="id_exp_detalle_recibo">
					<thead class="bg-blue text-light">
                        <tr>
                            <th class="center" style="width: 63px;">DETALLES</th>
                            <th class="center" style="width: 70px;">Nº RECIBO</th>
                            <th class="center" style="width: 70px;">CLIENTE</th>
                            <th class="center">NOMBRE</th>
                            <th class="center"style="width: 75px;">FECHA</th>
                            <th class="center"style="width: 95px;">TOTAL</th> 
                            <th class="center"style="width: 95px;">ADJUNTO</th> 
                        </tr>
                    </thead>
                    <tbody>
                    ` + tbody+ `
                    </tbody>
				</table>`

            
        callback(temp).show()

        },
        error: function(xhr) {

        }
    });
    //$('#id_exp_detalle_recibo').DataTable();
    
}

//DETALLES DE LIQUITACION
$(document).on('click', '#exp_more_liq', function(ef) {
    var table = $('#dtVinneta').DataTable();
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

        format_liq(row.child,data);
        tr.addClass('shown');
        
        ef.target.innerHTML = "expand_less";
        ef.target.style.background = '#ff5252';
        ef.target.style.color = '#e2e2e2';
    }
});

function format_liq ( callback, dta ) {    

    var thead = tbody = tNule = '';
    
    thead =`<table class="table table-striped table-bordered table-sm">
                <thead class="text-center bg-secondary text-light">
                    <tr>
                        <th class="center">FECHA</th>
                        <th class="center">Nº de Recibo Pago</th>
                        <th class="center">Nombre Cliente</th>
                        <th class="center">Codigo</th>
                        <th class="center">Concepto</th>
                        <th class="center">Total C$ </th>
                        
                    </tr>
                </thead>
                <tbody>`;
                

    if (dta.length==0) {
        tbody +=`<tr>
                    <td colspan='6'><center>Bodega sin existencia</center></td>
                </tr>`;
        callback(thead + tbody).show();
    }
    
    $.each(dta.DETALLES, function (i, item) {
        tbody +='<tr>'+
                    '<td class="text-center">' + item['FECHA'] + '</td>'+
                    '<td class="text-center">' + item['RECIBO'] + '</td>'+
                    '<td class="text-center">' + item['CLIENTE_NAME'] + '</td>'+
                    '<td class="text-center">' + item['CLIENTE_COD'] + '</td>'+
                    '<td class="text-center">' + item['CONCEPTO'] + '</td>'+
                    '<td class="text-center">C$ ' + numeral(item['TOTAL']).format('0,0.00')  + '</td>'+
                '</tr>';
    });


    tbody += `</tbody></table>`;
    temp = `
        <div style="margin: 0 auto; height: auto; width:100%; overflow: auto">
        <pre dir="ltr" style="margin: 0px;padding:6px;">
            `+thead+tbody+`
        </pre>
        </div>
        <div class="row mt-3">
                    
					<div class="col-sm-12">						
						<div class="card text-center">
							<div class="card-body">
								<h5 class="card-title" id="numero_factura">`+dta.COMMENT+`</h5>
								<p class="card-text" id="">Nota:</p>
							</div>
						</div>
					</div>					
                    `+ tNule+`
				</div>
        `;
    callback(temp).show();
        
}

</script>