<script>
$(document).ready(function() {
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Promoción Vueno</li>`);
    $('[data-toggle="tooltip"]').tooltip()
    dataPromocion(0,0);
    Resumen(0,0)
    inicializaControlFecha();

});


$("#BuscarPromocion").click( function() {

    f1 = $("#f1").val();
    f2 = $("#f2").val();

    dataPromocion(f1, f2);
    Resumen(f1, f2);
});

$('#txtSearch').on( 'keyup', function () {
    var table = $('#dtPromocion').DataTable();
    table.search(this.value).draw();
});
$( "#dtLength").change(function() {
    var table = $('#dtPromocion').DataTable();
    table.page.len(this.value).draw();
});

function Resumen(f1, f2) {

$('#dtResumen').DataTable({
    'ajax':{
        'url':'getResumen',
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
        { "title": "RUTA",    "data": "VENDEDOR"},
        { "title": "NOMBRE",    "data": "NOMBRE"},
        { 
            "title": "SKU 01",  
            "data": "SKU1",
            "defaultContent" : '',
        },
        
        { "title": "SKU 02",  "data": "SKU2" },
        { "title": "SKU 03",  "data": "SKU3" },
        { "title": "SKU 04",  "data": "SKU4" },
        { "title": "SKU 05",  "data": "SKU5" },
        { "title": "VALOR",  "data": "VALOR" }
    ],
    "columnDefs": [
        {"className": "dt-center", "targets": [ 0]},
        {"className": "dt-right", "targets": [1,2,3,4,5,6,7]}
        
    ],
    "initComplete": function(settings){
            $('#dtResumen thead th').each(function () {
                var $td = $(this);
                var headerText  = $td.text(); 
                var headerTitle = $td.text(); 

                if ( headerText == "RUTA" )
                    headerTitle =  "";
                else if (headerText == "SKU 01" )
                    headerTitle = "PH VUENO 1000 HS + 04 BLISTERS ACETAPLUS ANTIGRIPAL";
                else if ( headerText == "SKU 02" )
                    headerTitle =  "PH VUENO 1000 HS + 05 SALES DE REHIDRATACION ORAL 28.1G/SABOR NARANJA";
                else if ( headerText == "SKU 03" )
                    headerTitle =  "PH VUENO 1000 HS + 1 CAJA ZINALER (CETIRIZINA)";
                else if ( headerText == "SKU 04" )
                headerTitle =  "PH VUENO 1000 HS + 05 BLISTERS IBUPROFENO 400 MG";
                else if ( headerText == "SKU 05" )
                headerTitle =  "PH VUENO 1000 HS + 1 CAJA ACIDO ACETILSALICILICO 100 MG";
                else if ( headerText == "VALOR" )
                headerTitle =  "";
                $td.attr('title', headerTitle);
            });
            $('#dtResumen thead th[title]').tooltip({
                "container": 'body'
            });          
        },

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

        $( api.column( 2 ).footer() ).html( numeral(total).format('0,0.00')); 

        total = api.column( 3 ).data().reduce( function (a, b) 
        {
            return intVal(a) + intVal(b);
        }, 0 );

        $( api.column( 3 ).footer() ).html( numeral(total).format('0,0.00')); 

        total = api.column( 4 ).data().reduce( function (a, b) 
        {
            return intVal(a) + intVal(b);
        }, 0 );

        $( api.column( 4 ).footer() ).html( numeral(total).format('0,0.00')); 

        total = api.column( 5 ).data().reduce( function (a, b) 
        {
            return intVal(a) + intVal(b);
        }, 0 );

        $( api.column( 5 ).footer() ).html( numeral(total).format('0,0.00')); 

        total = api.column( 6 ).data().reduce( function (a, b) 
        {
            return intVal(a) + intVal(b);
        }, 0 );

        $( api.column( 6 ).footer() ).html( numeral(total).format('0,0.00')); 

        


        total = api.column( 7 ).data().reduce( function (a, b) 
        {
            return intVal(a) + intVal(b);
        }, 0 );

        $( api.column( 7 ).footer() ).html(numeral(total).format('0,0.00'));        
    },
});;
$("#dtResumen_length").hide();
$("#dtResumen_filter").hide();
}
function dataPromocion(f1, f2) {

    $('#dtPromocion').DataTable({
        'ajax':{
            'url':'getPromocion',
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
            { "title": "TOTAL BOLSONES", "data": "TOTAL" ,render: $.fn.dataTable.render.number( ',', '.', 2  , ' ' )},
            { "title": "TOTAL FACT.", "data": "TOTAL_FACTURA" ,render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' )},
            
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [0,1,2,4,5 ]},
            {"className": "dt-right", "targets": [ 6,7 ]},
            {"className": "dt-left", "targets": [ 3 ]},
            { "width": "5%", "targets": [0,1,2,4,5,6, 7] },
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
            // var roi = (total_facturado - total)  / total;
            var roi =0;
            total_facturado = api.column( 7 ).data().reduce( function (a, b){
                return intVal(a) + intVal(b);
            }, 0 );

           

            $('#MontoPromocion').text(numeral(total).format('0,0.00'));
            $('#id_total_Facturado').text('C$ ' + numeral(total_facturado).format('0,0.00'));
            $('#id_roi').text(numeral(roi).format('0,0.00'));
        },
    });

    $("#dtPromocion_length").hide();
    $("#dtPromocion_filter").hide();
}



$(document).on('click', '#exp_more', function(ef) {
    var table = $('#dtPromocion').DataTable();
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
                            '<td class="text-center">' + numeral(item['CANTIDAD']).format('0,0') + '</td>'+                            
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