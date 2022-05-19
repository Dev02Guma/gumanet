<script>
$(document).ready(function() {
    fullScreen();
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Viñetas</li>`);

    dataVinneta(0,0);
    resumenVinneta(0,0);
    inicializaControlFecha();

});

$('#InputDtShowSearchFilterInvTotal').on( 'keyup', function () {
    var table = $('#dtInventarioTotal').DataTable();
    table.search(this.value).draw();
});

$( "#InputDtShowColumnsInvTotal").change(function() {
    var table = $('#dtInventarioTotal').DataTable();
    table.page.len(this.value).draw();
});

$("#exp-to-excel").click( function() {
    location.href = "desInvTotal";
});

$("#BuscarVinneta").click( function() {

    f1 = $("#f1").val();
    f2 = $("#f2").val();

    dataVinneta(f1, f2);
    resumenVinneta(f1, f2);
});

$('#txtSearch').on( 'keyup', function () {
    var table = $('#dtVinneta').DataTable();
    table.search(this.value).draw();
});
$( "#dtLength").change(function() {
    var table = $('#dtVinneta').DataTable();
    table.page.len(this.value).draw();
});


function dataVinneta(f1, f2) {


    $.ajax({
        url: 'pagado',
        type: 'post',
        data: {
            'f1' : f1,
            'f2' : f2
        },
        async: true,
        success: function(pagado) {
            $('#MontoPagado').text('C$ ' + numeral(pagado).format('0,0.00'));
            
        }
    });

    $('#dtVinneta').DataTable({
        'ajax':{
            'url':'getVinnetas',
            'dataSrc': '',
            data: {
                'f1' : f1,
                'f2' : f2
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
            { "title": "",          "data": "DETALLE"},        
            { "title": "FACTURA",   "data": "FACTURA" },    
            { "title": "CLIENTE",   "data": "CLIENTE" },
            { "title": "NOMBRE",    "data": "NOMBRE_CLIENTE" },            
            { "title": "FECHA",     "data": "FECHA" },
            { "title": "VENDEDOR",  "data": "VENDEDOR" },
            { "title": "TOTAL",     "data": "TOTAL" ,render: $.fn.dataTable.render.number( ',', '.', 0  , 'C$ ' )},
            { "title": "CANT. LIQ.", "data": "CANT_LIQUIDADA" ,render: $.fn.dataTable.render.number( ',', '.', 0  , 'C$ ' )},
            { "title": "DISP.", "data": "DISPONIBLE" ,render: $.fn.dataTable.render.number( ',', '.', 0  , 'C$ ' )},
            { "title": "TOTAL FACT.", "data": "TOTAL_FACTURA" ,render: $.fn.dataTable.render.number( ',', '.', 0  , 'C$ ' )},
            { "title": "ACCIONES",  "data": "BOTONES" },
            
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [0,1,2,3,4,5,10 ]},
            {"className": "dt-right", "targets": [ 6,7,8,9 ]},
            { "width": "5%", "targets": [0,1,2,4,5,6,7,8,9,10 ] },
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api();
            varCount = api.rows().count()

            var intVal = function ( i ) {
                return typeof i === 'string' ?
                i.replace(/[^0-9.]/g, '')*1 :
                typeof i === 'number' ?
                i : 0;
            };
            $('#numero_factura').text(numeral(varCount).format('0,0'));

            total = api.column( 6 ).data().reduce( function (a, b){
                return intVal(a) + intVal(b);
            }, 0 );

            total_facturado = api.column( 9 ).data().reduce( function (a, b){
                return intVal(a) + intVal(b);
            }, 0 );

            var roi = (total_facturado - total)  / total;

            $('#MontoVinneta').text('C$ ' + numeral(total).format('0,0.00'));
            $('#id_total_Facturado').text('C$ ' + numeral(total_facturado).format('0,0.00'));
            $('#id_roi').text(numeral(roi).format('0,0.00'));
        },
    });

    $("#dtVinneta_length").hide();
    $("#dtVinneta_filter").hide();

    $('#dtRutas').DataTable({
        'ajax':{
            'url':'getPagadoRuta',
            'dataSrc': '',
            data: {
                'f1' : f1,
                'f2' : f2
            }
        },
        "destroy" : true,
        "info":    false,
        "lengthMenu": [[20,-1], [20,"Todo"]],
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
            { "title": "RUTA",   "data": "RUTA" },
            { "title": "NOMBRE",   "data": "NOMBRE" },
            { "title": "TOTAL.", "data": "TOTAL" ,render: $.fn.dataTable.render.number( ',', '.', 0  , 'C$ ' )},
            
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [0]},
            {"className": "dt-right", "targets": [ 2 ]},
            {"className": "dt-left", "targets": [ 1 ]},
            { "width": "10%", "targets": [0,2] },
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;                        
            var intVal = function ( i ) {
                
                return typeof i === 'string' ?
                i.replace(/[^0-9.,]/g, '')*1 :
                typeof i === 'number' ?
                i : 0;
            };

            

            total = api.column( 2 ).data().reduce( function (a, b) 
            {
               

                return intVal(a) + intVal(b);
            }, 0 );

            console.log(total   )

            $( api.column( 2 ).footer() ).html(
                    'C$ '+ numeral(total).format('0,0.00') +' TOTAL'
            );
        },
    });

    $("#dtRutas_length").hide();
    $("#dtRutas_filter").hide();
}

function resumenVinneta(f1, f2) {

$('#dtResumenVinneta').DataTable({
    'ajax':{
        'url':'getVinnetasResumen',
        'dataSrc': '',
        data: {
            'f1' : f1,
            'f2' : f2
        }
    },
    "destroy" : true,
    "info":    false,
    "scrollX": false,
    "lengthMenu": [[20,-1], [20,"Todo"]],
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
        { "title": "RUTA",       "data": "VENDEDOR"},
        { "title": "VIÑETA 5",   "data": "V_5" },
        { "title": "VIÑETA 10",  "data": "V_10" },
        { "title": "VIÑETA 20",  "data": "V_20" },
        { "title": "VIÑETA 30",  "data": "V_30" },
        { "title": "VIÑETA 35",  "data": "V_35" },
        { "title": "VIÑETA 40",  "data": "V_40" },
        { "title": "VIÑETA 50",  "data": "V_50" },
        { "title": "VIÑETA 70",  "data": "V_70" },
        { "title": "TOTAL",  "data": "TOTAL" },
        { "title": "VALOR",  "data": "VALOR" }
    ],
    "columnDefs": [
        {"className": "dt-center", "targets": [ 0]},
        {"className": "dt-right", "targets": [1,2,3,4,5,6,7,8,9,10]}
        
    ],
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;                        
        var intVal = function ( i ) {
            return typeof i === 'string' ?
            i.replace(/[^0-9.]/g, '')*1 :
            typeof i === 'number' ?
            i : 0;
        };

        total = api.column( 10 ).data().reduce( function (a, b) 
        {
            return intVal(a) + intVal(b);
        }, 0 );

        $( api.column( 4 ).footer() ).html(
                'C$ '+ numeral(total).format('0,0.00') +' TOTAL'
        );        
    },
});

$("#dtResumenVinneta_length").hide();
$("#dtResumenVinneta_filter").hide();
}

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

        format(row.child,data.FACTURA,data.CLIENTE);
        tr.addClass('shown');
        
        ef.target.innerHTML = "expand_less";
        ef.target.style.background = '#ff5252';
        ef.target.style.color = '#e2e2e2';
    }

    

});

function History(Factura){
    CardConten = '' ;
    $('#mdlHistory').modal('show');
    $('#id_Factura_history').html(Factura);
    CardConten ='';

    $.ajax({
        url: 'HistorialFactura',
        type: 'post',
        data: {
            vFactura     : Factura
        },
        async: true,
        success: function(response) {
        if (response.length==0) {
            CardConten = `<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<p class="text-center font-weight-bolder">No se encontraron registros</p>
								<center><img src="./images/icon_sinresultados.png" width="100" class="mt-4 mb-4" /></center>
							</div>
						</div>
					</div>
					</div>`;
        }		

        $.each(response, function (i, item) {

            CardConten +='<div class="card border-light mb-3 shadow-sm bg-white rounded">'+
			'<div class="card-body">'+
				'<div class="row">'+
					'<div class="col-md-10">'+
						'<h5 class="card-title font-weight-bold text-primary">'+item.VOUCHER+'</h5>'+
						'<p class="card-text">'+item.COMMENT+'</p>					'+
					'</div>'+
					'<div class="col-md-2 ">'+
					'</div>'+
				'</div>'+
			'</div>'+
			'<div class="card-footer bg-white border-0">'+
                '<div class="row">'+
                    '<div class="col-sm-3">'+               
                        '<p class="text-muted m-0" >RUTA</p>'+
                        '<p class="font-weight-bolder" style="font-size: 1.3rem!important" >'+item.RUTA+'</p>'+
                    '</div>'+
                    '<div class="col-sm-3">'+
                        '<p class="text-muted m-0">FECHA</p>'+
                        '<p class="font-weight-bolder" style="font-size: 1.3rem!important" >'+item.FECHA+'</p>'+
                    '</div>'+
                    '<div class="col-sm-2 border-right">'+
                        '<p class="text-muted m-0">Nº RRECIBO</p>'+
                        '<p class="font-weight-bolder" style="font-size: 1.3rem!important" >'+item.COD_RECIBO+'</p>'+
                    '</div>'+
                    '<div class="col-sm-2">'+
                        '<p class="text-muted m-0">CANTIDAD</p>'+
                        '<p class="font-weight-bolder" style="font-size: 1.3rem!important" >'+item.CANTIDAD+'</p>'+
                    '</div>'+
                    '<div class="col-sm-2">'+
                        '<p class="text-muted m-0">VALOR UNIDAD C$.</p>'+
                        '<p class="font-weight-bolder" style="font-size: 1.3rem!important" >'+item.VALOR_UND+'</p>'+
                    '</div>'+
                '</div>'+				
			'</div>'+
		'</div>';

        });

        $('#id_contenido_history').html(CardConten);


        }
    })
}

function AnulVineta(Factura,Voucher,Linea,Cliente,Valor_Linea,Cantidad){

    $('#message-text').val("")
    $('#mdlAnulacion').modal('show');
    $('#id_Factura').html(Factura);
    $('#id_Cantidad').val(Cantidad);
    $('#id_Vinneta').html(Voucher);
    $('#id_Linea').html(Linea);
    $('#id_Cliente').html(Cliente);
    $('#id_ValorUnd').html(Valor_Linea);
			
}


$("#id_frm_save_anulacion").click( function() {

    var vFactura    = $('#id_Factura').html();
    var vCantidad   = $('#id_Cantidad').val();
    var vVineta     = $('#id_Vinneta').html();
    var vLinea      = $('#id_Linea').html();
    var vCliente    = $('#id_Cliente').html();
    var vValorUnd   = $('#id_ValorUnd').html();
    var Comentario  = $('#message-text').val();


    $.ajax({
        url: 'Anular_Vineta',
        type: 'post',
        data: {
            Factura     : vFactura,
            Cantida     : vCantidad,
            Vineta      : vVineta,
            Linea       : vLinea,
            Cliente     : vCliente,
            ValorUnd    : vValorUnd,
            Coment      : Comentario
        },
        async: true,
        success: function(response) {
            
            f1 = $("#f1").val();
            f2 = $("#f2").val();

            dataVinneta(f1, f2);
            resumenVinneta(f1, f2);

            $('#mdlAnulacion').modal('hide')

        }
    })

});

function format ( callback, ordCompra_ ,Cliente) {
    role = $("#id_form_role").html();


    var thead = tbody = '';            
    thead =`<table class="table table-striped table-bordered table-sm">
                <thead class="text-center bg-secondary text-light">
                    <tr>
                        <th class="center">ARTICULO</th>
                        <th class="center">DESCRIP.</th>
                        <th class="center">CANTIDAD</th>
                        <th class="center">CANTIDAD LIQUIDADA</th>
                        <th class="center">PRECIO UNITARIO</th>
                        <th class="center">PRECIO TOTAL</th>
                        <th class="center">VALOR</th>
                        <th class="center">ACCION</th>
                        
                    </tr>
                </thead>
                <tbody>`;
                
    $.ajax({
        type: "POST",
        url: "getDetFactVenta",
        data:{
            factura: ordCompra_,
        },
        success: function ( data ) {
            if (data.length==0) {
                tbody +=`<tr>
                            <td colspan='6'><center>Bodega sin existencia</center></td>
                        </tr>`;
                callback(thead + tbody).show();
            }
            $.each(data['objDt'], function (i, item) {

                btnDownload ='';

                Valor_Vinneta = parseInt(item['ARTICULO'].substr(2,6))

                _Valor_Vinneta =(item['ARTICULO'].substr(0,2) == 'VU') ? 'C$ ' + numeral( item['CANTIDAD'] * Valor_Vinneta ).format('0,0.00') : " - ";

                ttTotal = parseInt(item['CANTIDAD']) - parseInt(item['CANT_LIQUIDADA']);

                if(role!=8){
                    if(item['ARTICULO'].substr(0,2) == 'VU'){
                        btnDownload =(ttTotal == 0) ? '' : '<button type="button" class="btn btn-danger float-center" onClick="AnulVineta('+"'"+item['FACTURA']+"',"+"'" + item['ARTICULO']+ "',"+ "'" + item['LINEA']+ "'," + "'"+ Cliente +"'," +"'" + Valor_Vinneta+ "'," +"'" +ttTotal +"'" +')"><i class="material-icons text-white mt-1"  style="font-size: 20px">close</i></button>'
                    }
                }
                

                tbody +='<tr>'+
                            '<td class="text-center">' + item['ARTICULO'] + '</td>'+
                            '<td class="text-left">' + item['DESCRIPCION'] + '</td>'+
                            '<td class="text-center">' + numeral(item['CANTIDAD']).format('0,0') + '</td>'+
                            '<td class="text-center">' + numeral(item['CANT_LIQUIDADA']).format('0,0') + '</td>'+
                            '<td class="text-right">' + numeral(item['PRECIO_UNITARIO']).format('0,0.00')  + '</td>'+
                            '<td class="text-right">' + numeral(item['PRECIO_TOTAL']).format('0,0.00')  + '</td>'+
                            '<td class="text-right"> ' + _Valor_Vinneta  + '</td>'+
                            '<td class="text-center"> ' + btnDownload  + '</td>'+
                        '</tr>';
            });
            tbody += `</tbody></table>`;
            
            temp = `
                <div style="
                margin: 0 auto;
                height: auto;
                width:100%;
                overflow: auto">
                <pre dir="ltr" style="margin: 0px;padding:6px;">
                    `+thead+tbody+`
                </pre>
                </div>`;

            callback(temp).show();
        }


    });
}

</script>