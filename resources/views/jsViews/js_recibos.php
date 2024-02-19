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
    Clie    = $("#dtCliente").val();
    Stat    = $("#dtStatus").val();
    Opt     = $('input[name=inlineRadioOptions]:checked', '#FrmOptns').val();

    
    dataVinneta(f1, f2,Ruta,Clie,Stat);

    
}



function dataVinneta(f1, f2,Ruta,Cliente,Stat) {

    $('#dtVinneta').DataTable({
        'ajax':{
            'url':'getRecibos',
            'dataSrc': '',
            data: {
                'f1' : f1,
                'f2' : f2,
                'RU' : Ruta,
                'CL' : Cliente,
                'St' : Stat,
            }
        },
        "destroy" : true,
        "info":    false,
        "lengthMenu": [[10,-1], [10,"Todo"]],
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
            { "title": "DETALLES",          "data": "DETALLE"},            
            { "title": "Nº RECIBO",         "data": "RECIBO" },
            { "title": "CLIENTE",           "data": "CLIENTE" },
            { "title": "NOMBRE",            "data": "NOMBRE_CLIENTE" },
            { "title": "FECHA",             "data": "FECHA" },
            { "title": "VENDEDOR",          "data": "VENDEDOR" },
            { "title": "TOTAL",             "data": "TOTAL" ,render: $.fn.dataTable.render.number( ',', '.', 0  , 'C$ ' )},
            { "title": "ACCIONES",          "data": "BOTONES"},
            { "title": "STATUS",            "data": "STATUS"},
            

        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [0,1,2,3,4,5,7 ]},
            {"className": "dt-right", "targets": [ 6 ]},
            { "width": "5%", "targets": [0,1,2,4,5,6 ] },
            { "width": "8%", "targets": [ 7 ] },
            { "visible":false, "searchable": false,"targets": [8] }
        ],
        "createdRow": function( row, data, dataIndex ) {
                if ( data.STATUS == 4) {        
                    $(row).addClass('tbl_rows_recibo_color');
                }

        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api();

            var intVal = function ( i ) {
                    return typeof i === 'string' ?
                    i.replace(/[^0-9.]/g, '')*1 :
                    typeof i === 'number' ?
                    i : 0;
                };

                var Pendiete    = 0;
                var Ingresado   = 0;
                var Verificado  = 0;
                var Total       = 0;

                total = api.column( 6 ).data().reduce( function (a, b){
                    return intVal(a) + intVal(b);
                }, 0 );

                for (var i = 0; i < data.length; i++) {
    
                    if (data[i].STATUS == "Pendiente")
                        Pendiete += intVal(data[i].TOTAL);
                    else if(data[i].STATUS == "Ingresado"){
                        Ingresado += intVal(data[i].TOTAL);
                    }else{
                        Verificado += intVal(data[i].TOTAL);
                    }
                }

                //Total = Pendiete + Ingresado + Verificado;
                Total = Pendiete + Ingresado + Verificado;

                $('#id_valor_pendiente').text("C$ " + numeral(Pendiete).format('0,0.00'));
                $('#id_valor_ingresado').text("C$ " + numeral(Ingresado).format('0,0.00'));
                $('#id_valor_verificado').text("C$ " + numeral(Verificado).format('0,0.00'));
                $('#id_valor_Total').text("C$ " + numeral(Total).format('0,0.00'));
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
    $('#mdlAttachFile').modal('show');
    $('#id_Factura_history').html(idRecibo);
    CardConten  = '';
    vBody       = '';

    $.ajax({
        url: 'getAttachFile',
        type: 'get',
        data: {
            iRecibo     : idRecibo
        },
        async: true,
        success: function(response) {
        var CardConten =  ``;

       

         $('#id_contenido_history').html("");

         $.each(response, function (i, item) {

            CardConten +=`
                <div class="mb-3 col-md-6 col-lg-3">
                  <div class="border rounded-1 h-100 d-flex flex-column justify-content-between pb-3">
                    <div class="overflow-hidden">
                      <div class="position-relative rounded-top overflow-hidden">
                      <a class="d-block" href="#!"style="cursor: pointer">
                        <img class="img-fluid rounded-top" src="`+item.IMAGEN+`" alt="" /></a>
                      </div>
                    </div>                    
                  </div>
                </div>`;

        });


         vBody =`<div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            `+CardConten+`
                        </div>
                    </div>
                </div>`;

        $('#id_contenido_history').html(vBody);


        }
    })
}
$("#resument").click( function() {

    Opt     = $('input[name=inlineRadioOptions]:checked', '#FrmOptns').val();

    $("#txt-fondo-inicial").val("")    
    $("#id-nota").val("")
    var table = $('#dtVinneta').DataTable();

    var form_data  = table.rows().data().toArray();

    console.log(form_data);
    
        
    var time = moment().format('DD/MM/YYYY');
    var data  = table.rows().data();
    Ruta      = $("#dtRutas").val();
    Ruta_name = $( "#dtRutas option:selected" ).text();

    Ruta_name = Ruta_name.substr(7, Ruta_name.length + 3)
    $('#mdlResumen').modal('show')
    

    $("#id-form-ruta").html(Ruta)
    $("#id-form-ruta-name").html(Ruta_name)
    $("#id-form-time").html(time)

    if(form_data.length > 0 ){
        let Subtotal  = 0;

        var thead = tbody = tfooter = '';
        var thead_dtl = tbody_dtl = tNule_dtl = temp_dtl = '';

        thead =`<table class="table table-striped table-bordered table-sm post_back" width="100%">
                    <thead c class="bg-blue text-light">
                        <tr>
                            <th class="center" width="10%">Fecha</th>
                            <th class="center" width="10%">No. de Recibo Colector</th>
                            <th class="center" width="10%">Codigo</th>
                            <th colspan="3" class="center" width="30%">Nombre del cliente</th>                            
                            <th class="center" width="10%">Total C$</th>
                        </tr>
                    </thead>
                    <tbody>`;

      



        $.each( form_data, function( key, item ) {

            let suma = 0

            thead_dtl =`<table class="table table-striped table-bordered table-sm">
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
            
            $.each( item['DETALLES'], function( key, item ) {
                suma++;


                var total_dtl = item['VALORFACTURA'] - item['NOTACREDITO'] - item['RETENCION'] - item['DESCUENTO'] - item['VALORRECIBIDO'];
                tbody_dtl +='<tr>'+
                    '<td class="text-center">' + item['FACTURA'] + '</td>'+
                    '<td class="text-center">C$ ' + numeral(item['VALORFACTURA']).format('0,0') + '</td>'+
                    '<td class="text-center">C$ ' + numeral(item['NOTACREDITO']).format('0,0') + '</td>'+
                    '<td class="text-center">C$ ' + numeral(item['RETENCION']).format('0,0') + '</td>'+
                    '<td class="text-right">C$ ' + numeral(item['DESCUENTO']).format('0,0.00')  + '</td>'+
                    '<td class="text-right">C$ ' + numeral(item['VALORRECIBIDO']).format('0,0.00')  + '</td>'+
                    '<td class="text-center">C$ ' + numeral(total_dtl).format('0,0.00')  + '</td>'+
                    '<td class="text-center">' +  item['TIPO'] + '</td>'+
                '</tr>';

            })
            tbody_dtl += `</tbody></table>`;

            strTotal = item['TOTAL'];

            Total = parseFloat(strTotal.replace('C$',' ').replace(',',''))
            var clssAnulado = (item['STATUS']==4) ? "tbl_rows_recibo_color" : "";

            tbody += '<tr class="'+clssAnulado+'">'+
                        '<td class="text-center" >' + item['FECHA'] + '</td>'+
                        '<td class="text-center">' + item['RECIBO'] + '</td>'+
                        '<td class="text-center">' + item['CLIENTE'] + '</td>'+
                        '<td colspan="3" class="text-left">' + item['NOMBRE_CLIENTE'] + '</td>'+        
                        '<td class="text-center">' + strTotal + '</td>'+
                    '</tr>' 
                    ;
            tbody += '<tr>'+
                        '<td colspan="7"class="text-center" > '+thead_dtl + tbody_dtl+' </td>'+                        
                    '</tr>'

            /* EN CASO QUE TENGA QUE PONER ALGUN VALIDADOR
            if (item['STATUS'] == "Ingresado"){
                Subtotal +=  Total; 
            }*/

            Subtotal +=  Total; 

            thead_dtl = '';
            tbody_dtl = '';
            
        });



        tfooter = `<tfoot>
                <tr>
                    <td colspan="6" align="right">TOTAL RECIBIDO C$</td>
                    <td align="right" id="id-mdl-subtotal">
                        ` + numeral(Subtotal).format('0,0.00') + `
                    </td>
                </tr>
            </tfoot>`

            tbody += `</tbody> `+tfooter +`</table>`;

            $('#id-form-Total').text("C$ " + numeral(Subtotal).format('0,0.00'));

        temp = thead + tbody;
        $("#dtViewLiquidacion").empty().append(temp);
    }else{
    var thead = tbody = tfooter = '';

    thead =`<table <table class="table table-striped table-bordered table-sm post_back" width="100%">
                <thead  class="bg-blue text-light">
                    <tr>
                        <th class="center" width="10%">Fecha</th>
                        <th class="center" width="10%">No. de Recibo pago</th>
                        <th class="center" width="30%">Nombre del cliente</th>
                        <th class="center" width="10%">Codigo</th>
                        <th class="center" width="10%">Concepto</th>
                        <th class="center" width="10%">Total C$</th>
                        
                    </tr>
                </thead>
                <tbody>`;
    tbody +='<tr>'+
        '<td colspan="6" class="text-center" >SIN RECIBOS</td></tr>';
    tfooter = `<tfoot>
                    <tr>
                        <td colspan="6"><br></td>
                    </tr>            
                    <tr>
                        <td colspan="5" align="right">TOTAL RECIBIDO C$</td>
                        <td align="right" id="id-mdl-subtotal">0.00</td>
                    </tr>
                </tfoot>`

                tbody += `</tbody> `+tfooter +`</table>`;


            temp = thead + tbody;
            $("#dtViewLiquidacion").empty().append(temp);


    }
});

$("#id-print-pdf").click( function() {

    f1      = $("#f1").val();
    f2      = $("#f2").val();
    Ruta    = $("#id-form-ruta").text();
    Clie    = $("#dtCliente").val();    
    Nota    = $("#id-coment").val();
    
    if (Ruta=='' ) {
        alert(" Tiene Información pendiente ")        
    } else {
        location.href = "print_resumen?f1="+f1+"&f2="+f2+"&RU="+Ruta+"&CL="+Clie+"&nota="+Nota+"&St="+Stat;
        $('#mdlResumen').modal('hide')
    }
    

});

function rePrint(Id){
    location.href = "rePrint?Id="+Id;
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
$(document).on('click', '#exp_more', function(ef) {
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

        format(row.child,data);
        tr.addClass('shown');
        
        ef.target.innerHTML = "expand_less";
        ef.target.style.background = '#ff5252';
        ef.target.style.color = '#e2e2e2';
    }
});

function format ( callback, dta ) {    

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