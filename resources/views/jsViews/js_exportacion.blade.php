<script>
$(document).ready(function() {
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Ventas de Exportacion</li>`);
    $('[data-toggle="tooltip"]').tooltip()
    dataVentaExportacion(0,0);
    inicializaControlFecha();

});


$("#BuscarPromocion").click( function() {

    f1 = $("#f1").val();
    f2 = $("#f2").val();

    dataVentaExportacion(f1, f2);
});

$('#txtSearch').on( 'keyup', function () {
    var table = $('#dtVentaExportacion').DataTable();
    table.search(this.value).draw();
});
$( "#dtLength").change(function() {
    var table = $('#dtVentaExportacion').DataTable();
    table.page.len(this.value).draw();
});

function dataVentaExportacion(f1, f2) {

    $('#dtVentaExportacion').DataTable({
        'ajax':{
            'url':'getVentasExportacion',
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
            { "title": "TONELADA FACTURADA",  "data": "CANTIDAD",render: $.fn.dataTable.render.number( ',', '.', 2  , ' ' ) },         
            { "title": "TOTAL FACT.", "data": "TOTAL_FACTURA" ,render: $.fn.dataTable.render.number( ',', '.', 2  , '$ ' )},
            { "title": "TIPO DE CAMBIO.", "data": "TIPO_CAMBIO" ,render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' )},
            { "title": "TOTAL FACT.", "data": "TOTAL_MONEDA_LOCAL" ,render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' )},
            
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [0,1,2,4,6 ]},
            {"className": "dt-right", "targets": [ 5,6,7,8 ]},
            {"className": "dt-left", "targets": [ 3 ]},
            { "width": "6%", "targets": [0,1,2,4,5,6,7] },
            { "width": "10%", "targets": [8] },
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

            total_ton = api.column( 5 ).data().reduce( function (a, b){
                return intVal(a) + intVal(b);
            }, 0 );
        
            total_facturado = api.column( 6 ).data().reduce( function (a, b){
                return intVal(a) + intVal(b);
            }, 0 );

            total_moneda_local = api.column( 8 ).data().reduce( function (a, b){
                return intVal(a) + intVal(b);
            }, 0 );
            
            $('#id_total_Facturado').text('$ ' + numeral(total_facturado).format('0,0.00'));
            $('#id_total_moneda_local').text('C$ ' + numeral(total_moneda_local).format('0,0.00'));
            $('#id_total_ton').text(numeral(total_ton).format('0,0.00'));
           
        },
    });

    $("#dtVentaExportacion_length").hide();
    $("#dtVentaExportacion_filter").hide();
}



$(document).on('click', '#exp_more', function(ef) {
    var table = $('#dtVentaExportacion').DataTable();
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



function format ( callback, ordCompra_ ,Cliente) {
    role = $("#id_form_role").html();


    var thead = tbody = '';            
    thead =`<table class="table table-striped table-bordered table-sm">
                <thead class="text-center bg-secondary text-light">
                    <tr>
                        <th class="center">ARTICULO</th>
                        <th class="center">DESCRIP.</th>
                        <th class="center">CANTIDAD</th>
                        <th class="center">PRECIO UNITARIO</th>
                        <th class="center">PRECIO TOTAL</th>
                        
                    </tr>
                </thead>
                <tbody>`;
                
    $.ajax({
        type: "POST",
        url: "getHistorialFactura",
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

                tbody +='<tr>'+
                            '<td class="text-center">' + item['ARTICULO'] + '</td>'+
                            '<td class="text-left">' + item['DESCRIPCION'] + '</td>'+
                            '<td class="text-center">' + numeral(item['CANTIDAD']).format('0,0.00') + '</td>'+                            
                            '<td class="text-right">' + numeral(item['PRECIO_UNITARIO']).format('0,0.00')  + '</td>'+
                            '<td class="text-right">' + numeral(item['PRECIO_TOTAL']).format('0,0.00')  + '</td>'+
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