<script>
$(document).ready(function() {
    fullScreen();
    //AGREGO LA RUTA AL NAVEGADOR
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Inventario</li>`);
    var infoTable = '';
    emp = $("#companny_id").text();

    switch(emp) {
        case '1':        
        columns = [
            { title: "ARTICULO",                data: "ARTICULO" },
            { title: "DESCRIPCION",             data: "DESCRIPCION" },
            { title: "UNIDAD",                  data: "UNIDAD_ALMACEN" },
            { title: "CANT. DISP. B002",        data: "total" },
            { title: "TOTAL UNITS. DISP. B002",           data: "und" },
            { title: "TOTAL UNITS/ MES",        data: "VST_MES_ACTUAL" }, //VALIDADO ES CORRECTO
            { title: "PROM UNITS/ MES {{date('Y')}}",    data: "PROM_VST_ANUAL" },//VALIDADO ES CORRECTO
            { title: "TOTAL UNITS {{date('Y')}}",        data: "VST_ANNO_ACTUAL" },//VALIDADO ES CORRECTO
            { title: "PROM. UNITS/ MES {{date('Y',strtotime('-1 year'))}}",   data: "PROMEDIO_VENTA" },
            { title: "TOTAL UNITS {{date('Y',strtotime('-1 year'))}}",        data: "CANT_ANIO_PAS" },
            { title: "MESES INVENTARIO",        data: "MESES_INVENTARIO" },
            { title: "Nº MESES CON VTA",        data: "COUNT_MONTH" },
            { title: "TOTAL VTA ANUAL",         data: "SUM_ANUAL" },
            { title: "PROM. VTA ANUAL",         data: "AVG_ANUAL" },
            { title: "PROM. 3M. MAS ALTO",      data: "AVG_3M" }
            
        ];
        columnDefs=[
            {"className":"dt-right", "targets": [ 3, 4, 5, 6, 7, 8, 9, 10,12,13,14 ]},
            {"className":"dt-center", "targets": [ 0, 2,11 ]},
            { "width":"50%", "targets": [ 1 ] }
        ]
        infoTable = `Mostrando Articulos solo de Bodega 002`;
        break;
    case '2':
        columns = [
            { title: "ARTICULO",                data: "ARTICULO" },
            { title: "DESCRIPCION",             data: "DESCRIPCION" },
            { title: "UNIDAD",                  data: "UNIDAD_ALMACEN" },
            { title: "CANT. DISP.",             data: "total" },
            { title: "TOTAL UNITS. ",           data: "und" },
            { title: "TOTAL UNITS/ MES",        data: "VST_MES_ACTUAL" },
            { title: "PROM UNITS/ MES {{date('Y')}}",    data: "PROM_VST_ANUAL" },
            { title: "TOTAL UNITS {{date('Y',strtotime('-1 year'))}}",        data: "VST_ANNO_ACTUAL" },
            { title: "PROM. UNITS/ MES {{date('Y',strtotime('-1 year'))}}",   data: "PROMEDIO_VENTA" },
            { title: "TOTAL UNITS 2021",        data: "CANT_ANIO_PAS" },
            { title: "MESES INVENTARIO",        data: "MESES_INVENTARIO" },
            { title: "Nº MESES CON VTA",        data: "COUNT_MONTH" },
            { title: "TOTAL VTA ANUAL",         data: "SUM_ANUAL" },
            { title: "PROM. VTA ANUAL",         data: "AVG_ANUAL" },
            { title: "PROM. 3M. MAS ALTO",      data: "AVG_3M" }
            
        ];
        columnDefs=[
            {"className":"dt-right", "targets": [ 3, 4, 5, 6, 7, 8, 9, 10,11,12,13 ]},
            {"className":"dt-center", "targets": [ 0, 2 ]},
            { "width":"50%", "targets": [ 1 ] }
        ]
    break;
    case '4':
        columns = [
            { title: "ARTICULO",                data: "ARTICULO" },
            { title: "DESCRIPCION",             data: "DESCRIPCION" },
            { title: "UNIDAD",                  data: "UNIDAD_ALMACEN" },
            { title: "CANT. DISP.",             data: "total" },
            { title: "TOTAL UNITS. ",           data: "und" },
            { title: "TOTAL UNITS/ MES",        data: "VST_MES_ACTUAL" },
            { title: "PROM UNITS/ MES {{date('Y')}}",    data: "PROM_VST_ANUAL" },
            { title: "TOTAL UNITS {{date('Y',strtotime('-1 year'))}}",        data: "VST_ANNO_ACTUAL" },
            { title: "PROM. UNITS/ MES {{date('Y',strtotime('-1 year'))}}",   data: "PROMEDIO_VENTA" },
            { title: "TOTAL UNITS 2021",        data: "CANT_ANIO_PAS" },
            { title: "MESES INVENTARIO",        data: "MESES_INVENTARIO" },
            { title: "Nº MESES CON VTA",        data: "COUNT_MONTH" },
            { title: "TOTAL VTA ANUAL",         data: "SUM_ANUAL" },
            { title: "PROM. VTA ANUAL",         data: "AVG_ANUAL" },
            { title: "PROM. 3M. MAS ALTO",      data: "AVG_3M" }
            
        ];
        columnDefs=[
            {"className":"dt-right", "targets": [ 3, 4, 5, 6, 7, 8, 9, 10,11,12,13 ]},
            {"className":"dt-center", "targets": [ 0, 2 ]},
            { "width":"50%", "targets": [ 1 ] }
        ]
        $("#modulo-inventario").empty();
        $("#modulo-inventario-vencido").empty();
        
    break
    default:
        alert('Ups... ocurrio un problema')
    }

    $('#dtInventarioArticulos').DataTable({
        "ajax":{
            "url": "articulos",
            'dataSrc': '',
        },
        "info":    true,
        "lengthMenu": [[10,30,50,100,-1], [20,30,50,100,"Todo"]],
        "language": {
            "info": infoTable,
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
        'columns': columns,
        "columnDefs": columnDefs,
        rowCallback: function (row, data) {
            /*
            if (numeral(data.MESES_INVENTARIO).format('0.00') <= 3) {                
                $("td:eq(10)", row).addClass("alert alert-danger");
            }else if(numeral(data.MESES_INVENTARIO).format('0.00') > 3 &&  data.MESES_INVENTARIO <= 12){
                $("td:eq(10)", row).addClass("alert alert-warning");
            }*/

        }
    });

    liquidacionPorMeses(6)
    inicializaControlFecha();
    InventarioB004(12)
});

function InventarioB004() {
    $('#id_tbl_inventario_b004').DataTable({
        "destroy": true,
        "ajax":{
            "url": "invenVencidos",
            'dataSrc': '',
        },
        "info":    false,
        "lengthMenu": [[5,10,50,-1], [5,10,100,"Todo"]],
        "language": {
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
            { "title": "ARTICULO",              "data": "ARTICULO" },
            { "title": "DESCRIPCION",           "data": "DESCRIPCION" },
            { "title": "LOTE",                  "data": "LOTE" },            
            { "title": "CANTIDAD",              "data": "CANT_DISPONIBLE" },
            { "title": "COSTO PROM. LOC.",      "data": "COSTO_PROM_LOC",render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' ) },
            { "title": "COSTO ULT. LOC.",       "data": "COSTO_ULT_LOC",render: $.fn.dataTable.render.number( ',', '.', 2  , 'C$ ' ) },
            { "title": "FECHA DE VENCIMIENTO",  "data": "FECHA_VENCIMIENTO" },
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [ 0, 2 ,6 ]},
            {"className": "dt-right", "targets": [ 3,4,5 ]},
            { "width": "50%", "targets": [ 1 ] }
        ],
    });

    $("#id_tbl_inventario_b004_length ").hide();
    $("#id_tbl_inventario_b004_filter").hide();
}

function liquidacionPorMeses(valor) {
    $('#tblArticulosVencimiento').DataTable({
        "destroy": true,
        "ajax":{
            "url": "liqMeses/"+valor,
            'dataSrc': '',
        },
        "info":    false,
        "lengthMenu": [[5,10,50,-1], [5,10,100,"Todo"]],
        "language": {
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
            { "title": "ARTICULO",              "data": "ARTICULO" },
            { "title": "DESCRIPCION",           "data": "DESCRIPCION" },
            { "title": "DÍAS PARA VENCERSE",    "data": "DIAS_VENCIMIENTO" },
            { "title": "CANTIDAD DISPONIBLE",   "data": "CANT_DISPONIBLE" },
            { "title": "FECHA VENCE",           "data": "F_VENCIMIENTO" },
            { "title": "LOTE",                  "data": "LOTE" },
            { "title": "BODEGA",                "data": "BODEGA" },
            { "title": "TOTAL UNITS {{date('Y')}}",      "data": "VTS_ANIO_ANT" },
            { "title": "PROM. UNITS/MES {{date('Y')}}",  "data":  "PROMEDIO_VENTA" },
            { "title": "MESES DE INVENTARIO",   "data": "TEMPO_ESTI_VENT" },
            { "title": "COSTO PROM.",           "data": "COSTO_PROM_LOC" },
            { "title": "COSTO ULT.",            "data": "COSTO_ULT_LOC" },
            { "title": "COSTO TOTAL",           "data": "COSTO_TOTAL" },
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": [ 0, 2, 4, 5 ]},
            {"className": "dt-right", "targets": [ 3, 6, 7, 8, 9, 10, 11, 12]},
            { "width": "50%", "targets": [ 1 ] }
        ],
    });

    $("#dtInventarioArticulos_length, #tblArticulosVencimiento_length").hide();
    $("#dtInventarioArticulos_filter, #tblArticulosVencimiento_filter").hide();
}

function descargarArchivo( tipo ) {
    if (tipo=='vencimiento') {        
        valor = $( "#orderByDate" ).val()
        location.href = "desInventario/"+tipo+"/"+valor;
    }else {
        location.href = "desInventario/"+tipo+"/ND";
    }    
}

$('#InputDtShowSearchFilterArt').on( 'keyup', function () {
    var table = $('#dtInventarioArticulos').DataTable();
    table.search(this.value).draw();
});

$('#InputDtShowSearchFilterArtVenc').on( 'keyup', function () {
    var table = $('#tblArticulosVencimiento').DataTable();
    table.search(this.value).draw();
});

$('#InputDtShowSearchFilterArt12M').on( 'keyup', function () {
    var table = $('#dtLiq12Meses').DataTable();
    table.search(this.value).draw();
});

$( "#orderByDate").change(function() {
    valor = $( this ).val()  
    
    liquidacionPorMeses(valor)
});

$( "#InputDtShowColumnsArtic").change(function() {
    var table = $('#dtInventarioArticulos').DataTable();
    table.page.len(this.value).draw();
});

$( "#InputDtShowColumnsArtic2").change(function() {
    var table = $('#tblArticulosVencimiento').DataTable();
    table.page.len(this.value).draw();
});

$( "#id_select_inventario_vencido").change(function() {
    var table = $('#id_tbl_inventario_b004').DataTable();
    table.page.len(this.value).draw();
});

$('#id_search_tble_inventario_vencido').on( 'keyup', function () {
    var table = $('#id_tbl_inventario_b004').DataTable();
    table.search(this.value).draw();
});


$('nav .nav.nav-tabs a').click(function(){
    var idNav = $(this).attr('id');
    switch(idNav) {
        case 'navBodega':
            getDataBodega(articulo_g);
        break;
        case 'navPrecios':
            getPrecios(articulo_g);
            break;
        case 'navBonificados':
            getBonificados(articulo_g);
        break;
        case 'navTransaccion':        
        break;
        case 'navCostos':        
            getCostos(articulo_g)
        break;
        case 'navMargen':        
            getMargen(articulo_g)
        break;
        case 'navOtros':        
            getOtros(articulo_g)
        break;
        case 'navIndicadores':        
            getIndicadores(articulo_g)
        break;
        case 'navVinneta':  
            getVineta(articulo_g)
        break;
        default:
            alert('Al parecer alguio salio mal :(')
    }    
})

var articulo_g = 0;
function getDetalleArticulo(articulo, descripcion) {
    articulo_g = articulo;
    $("#tArticulo").html(descripcion+`<p class="text-muted">`+articulo+`</p>`);
    getDataBodega(articulo);

    var target = '#nav-bod';
    $('a[data-toggle=tab][href=' + target + ']').tab('show');

    $("#tbody1").empty()
    .append(`<tr><td colspan='5'><center>Aún no ha realizado ninguna busqueda</center></td></tr>`);

    $("#mdDetalleArt").modal('show');

}

function getDataBodega(articulo) {
    $("#tblBodega").dataTable({
        responsive: true,
        "autoWidth":false,
        "ajax":{
            "url": "objBodega/"+articulo,
            'dataSrc': '',
        },
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
function getMargen(articulo) {
    $("#tblMargen").dataTable({
        responsive: true,
        "autoWidth":false,
        "ajax":{
            "url": "objMargen/"+articulo,
            'dataSrc': '',
        },
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
function getPrecios(articulo) {
    $("#tblPrecios").dataTable({
        responsive: true,
        "autoWidth":false,
        "ajax":{
            "url": "objPrecios/"+articulo,
            'dataSrc': '',
        },
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
function getCostos(articulo) {   
    $.ajax({
        url: "objCostos/"+articulo,
        type: 'get',
        data: {},
        async: true,
        success: function(data) {
            $("#id_prec_prom").text(data[0]['COSTO_PROM_LOC']);
            $("#id_ult_prec").text(data[0]['COSTO_ULT_LOC'])
        }
    })
}

function getOtros(articulo) {   
    $.ajax({
        url: "objOtros/"+articulo,
        type: 'get',
        data: {},
        async: true,
        success: function(data) {
            $("#id_clase_abc").text(data[0]['CLASE']);
            $("#id_existencia_minima").text(data[0]['MINIMO'])
            $("#id_punto_de_reoden").text(data[0]['REORDEN']);
            $("#id_plazo_rebast").text(data[0]['REABASTECIMIENTO'])
        }
    })
}

function getVineta(articulo) {   
    $.ajax({
        url: "objVineta/"+articulo,
        type: 'get',
        data: {},
        async: true,
        success: function(data) {
            $("#id_vineta_valor").text("C$ " + numeral(data[0]['VINNETA']).format("0,00.00"))
        }
    })
}

function getIndicadores(articulo) {   
    $.ajax({
        url: "objIndicadores/"+articulo,
        type: 'get',
        data: {},
        async: true,
        success: function(data) {
            

            $("#id_total_fact").text("C$ " + numeral(data['ANUAL'][0]['data']).format("0,00.00"));
            $("#id_unit_fact").text(numeral(data['ANUAL'][0]['dtUnd']).format("0,00.00"));
            $("#id_unit_bonif").text(numeral(data['ANUAL'][0]['dtUndBo']).format("0,00.00"));
            $("#id_prom_prec").text("C$ " + data['ANUAL'][0]['dtAVG']);
            $("#id_prom_cost_unit").text("C$ " +numeral(data['ANUAL'][0]['dtCPM']).format("0,00.00"));
            $("#id_contribucion").text("C$ " + data['ANUAL'][0]['dtMCO']);
            $("#id_margen_bruto").text(numeral(data['ANUAL'][0]['dtPCO']).format("0,00.00") + " %");

            $("#id_disp_bodega").text(numeral(data['ANUAL'][0]['dtTB2']).format("0,00.00") );
            $("#id_disp_bodega_unds").text(numeral(data['ANUAL'][0]['dtTUB']).format("0,00.00"));

            $("#id_prom_unds_mes").text(numeral(data['ANUAL'][0]['dtPRO']).format("0,00.00") );
            $("#id_cant_disp_mes").text(numeral(data['ANUAL'][0]['dtTIE']).format("0,00.00"));

            $("#id_total_fact_month").text("C$ " + numeral(data['MENSUAL'][0]['data']).format("0,00.00"));
            $("#id_unit_fact_month").text(numeral(data['MENSUAL'][0]['dtUnd']).format("0,00.00"));
            $("#id_unit_bonif_month").text(numeral(data['MENSUAL'][0]['dtUndBo']).format("0,00.00"));
            $("#id_prom_prec_month").text("C$ " + data['MENSUAL'][0]['dtAVG']);
            $("#id_prom_cost_unit_month").text("C$ " +numeral(data['ANUAL'][0]['dtCPM']).format("0,00.00"));
            $("#id_contribucion_month").text("C$ " + data['MENSUAL'][0]['dtMCO']);
            $("#id_margen_bruto_month").text(numeral(data['MENSUAL'][0]['dtPCO']).format("0,00.00") + " %");

            $("#id_disp_bodega_month").text(numeral(data['MENSUAL'][0]['dtTB2']).format("0,00.00") );
            $("#id_disp_bodega_unds_month").text(numeral(data['MENSUAL'][0]['dtTUB']).format("0,00.00"));

            $("#id_prom_unds_mes_month").text(numeral(data['MENSUAL'][0]['dtPRO']).format("0,00.00") );
            $("#id_cant_disp_mes_month").text(numeral(data['MENSUAL'][0]['dtTIE']).format("0,00.00"));
        }
    })
}

function getBonificados(articulo) {
    $("#tblBonificados").dataTable({
        responsive: true,
        "autoWidth":false,
        "ajax":{
            "url": "objBonificado/"+articulo,
            'dataSrc': '',
            async: false
        },
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

$("#btnSearch").click(function() {    
    var tbody = '';
    var Total = 0 ;
    $.ajax({
        type: "POST",
        url: "transacciones",
        data:{
            f1: $("#f1").val(),
            f2: $("#f2").val(),
            art: articulo_g,
            tp: $( "#catArt option:selected" ).val()            
        },
        success: function (data) {
            if (data.length==0) {
                $("#tbody1").empty();
                tbody +=`<tr>
                            <td colspan='5'><center>No hay datos que mostrar</center></td>
                        </tr>`;
                mensaje('No se encontraron registros con los datos proporcionados', 'error');
            }else {                
                $("#tbody1").empty();
                $.each(data, function(i, item) {
                    tbody +=`<tr>
                                <td>`+item['FECHA']+`</td>
                                <td>`+item['LOTE']+`</td>
                                <td>`+item['APLICACION']+`</td>
                                <td>`+item['DESCRTIPO']+`</td>
                                <td class="text-right">`+item['CANT']+`</td>
                                <td>`+item['REFERENCIA']+`</td>
                                <td>`+item['CODIGO_CLIENTE']+`</td>
                                <td>`+item['NOMBRE']+`</td>
                            </tr>`;

                            Total += numeral(item['CANTIDAD']).value(); 
                });
                Total = numeral(Total).format('0,00');
                tbody +=`<tr class="bg-blue text-light">
                            <td class="text-light" colspan='4'> TOTAL UNIDADES DESPLAZADAS</td>
                            
                            <td class="text-light text-right" >`+Total+`</td>
                            <td class="text-right" colspan='3'></td>
                        </tr>`;
            }
            $("#tbody1").append(tbody);
        }
    });
});
    
$(document).on('click', '#exp_more', function(ef) {
    var table = $('#tblBodega').DataTable();
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

        format(row.child,data.BODEGA,articulo_g,data.UNIDAD);
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
</script>