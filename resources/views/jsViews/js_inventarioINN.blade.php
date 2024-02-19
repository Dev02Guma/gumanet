<script type="text/javascript">
$(document).ready(function() {
    var date = new Date();

    var inicio = new Date(date.getFullYear(), date.getMonth(), 1);
    var final = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    var primerDia = inicio.getFullYear()+'-'+(inicio.getMonth()+1)+'-'+inicio.getDate();
    var ultimoDia = final.getFullYear()+'-'+(final.getMonth()+1)+'-'+final.getDate();

    var dia = date.getDate() - 14;
    

    if(dia > 0){
        primerDia = final.getFullYear()+'-'+(final.getMonth()+1)+'-'+dia;
    }else{
        var final = new Date(date.getFullYear(),date.getMonth(), 0);
        primerDia = final.getFullYear()+'-'+(final.getMonth()+1)+'-'+(final.getDate()+dia);
    }


    tblKardex(primerDia, ultimoDia);
});

$("#id_btn_new").click( function() {
    var date = new Date();

    var mes = $('#id_select_mes').val();

    

    var invM = (date.getMonth() + 1) - mes;
    
    var inicio = new Date(date.getFullYear(), invM, 1);
    var final = new Date(date.getFullYear(),date.getMonth() + 1, 0);

    var primerDia = inicio.getFullYear()+'-'+(inicio.getMonth()+1)+'-'+inicio.getDate();
    var ultimoDia = final.getFullYear()+'-'+(final.getMonth()+1)+'-'+final.getDate();
    
    if(mes == 7 || mes == 15){
        var dia = (date.getDate()+1) - mes;
        if(dia > 0){
            primerDia = final.getFullYear()+'-'+(final.getMonth()+1)+'-'+dia;
        }else{
            var final = new Date(date.getFullYear(),date.getMonth(), 0);
            primerDia = final.getFullYear()+'-'+(final.getMonth()+1)+'-'+(final.getDate()+dia);
        }
    }

    tblKardex(primerDia, ultimoDia);
});


function tblKardex(primerDia, ultimoDia) {
    tblResumen();
    tblMateriaPrima();
    $("#id_Status").show();
    $.ajax({
        url: `getKerdex`,
        type: 'get',
        data: { 
            ini : primerDia, 
            end : ultimoDia
        },
        async: true,
        success: function(data) {
            table =  `<table class="table table-bordered " id="tbl_kardex" style="width:100%; border-collapse: collapse;"><thead>`+
                        `<tr class="bg-blue text-light">`+
                            `<th style="width: 700px;" rowspan="2">ARTICULO</th>`;
                            $.each(data['header_date'], function (i, item) {
                                table += `<th colspan="3" style="text-align:center;" width="10px">`+  moment(item).format('DD-MMM-YYYY') +`</th>`;                                
                            })
                            
                        
                table +=`</tr><tr>`;                        
                        $.each(data['header_date'], function (i, item) {
                            table += `<th>Entrada</th>`+
                                    `<th>Salida</th>`+
                                    `<th>Saldo</th>`;
                                   
                        })
                    
                table +=`</tr></thead>`+
                    `<tbody style="scrollbar: collapse;">`;
                        $.each(data['header_date_rows'], function (i, item) {
                            table +=`<tr>`+
                                        `<td style="width: 700px; ">`+
                                            `<div class="d-flex position-relative">`+
                                                `<div class="flex-1" style="width: 400px; ">`+
                                                    `<h6 class="mb-0 fw-semi-bold">`+ item.DESCRIPCION +`</h6>`+
                                                    `<p class="text-500 fs--2 mb-0">`+ item.ARTICULO +` | `+item.UND+` | <span class="badge rounded-pill badge-primary">`+item.USUARIO  +`</span></b></p>`+
                                                `</div>`+
                                            `</div>`+
                                        `</td>`;
                                        $.each(data['header_date'], function (i, kar) {
                                            table += `<td> <p class="text-right" style="width: 60px;">`+numeral(item['IN01_'+moment(kar).format('YYYYMMDD')]).format('0,0.00')+`</p> </td>`+
                                                    `<td> <p class="text-right" style="width: 60px;">`+numeral(item['OUT02_'+moment(kar).format('YYYYMMDD')]).format('0,0.00')+`</p></td>`+
                                                    `<td> <p class="text-right" style="width: 60px;">`+numeral(item['STOCK03_'+moment(kar).format('YYYYMMDD')]).format('0,0.00')+`</p></td>`;
                                        });
                                        table += `</tr>`;
                                
                               
                    });
                
                table +=`</tbody></table>`;

   			$('#kardex')
   			.empty()
   			.append(table);
            
            $('#tbl_kardex').DataTable({
                "destroy" : true,
                "info":    false,
                "lengthMenu": [[15,10,-1], [15,10,"Todo"]],
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
                "scrollY":        "1200px",
                "scrollX":        true,
                "scrollCollapse": true,
                "fixedColumns": {
                    leftColumns: 1,
                    rightColumns: 3
                },
                createdRow: function (row, data, index) {
                    // Obtener la referencia a la tabla DataTable
                    var table = $('#tbl_kardex').DataTable();

                    // Obtener las últimas tres celdas de la fila actual
                    var lastCells = $('td', row).slice(-3);

                    // Agregar la clase CSS personalizada a esas celdas
                    lastCells.addClass('colorTable');
                    lastCells.addClass('encabezadoInv');
                    lastCells.hide();
                    

                    // Obtener las cabeceras de las últimas tres celdas de la tabla
                    var lastHeaders = $('th', table.table().header()).slice(-3);

                    // Agregar la clase CSS personalizada a esas cabeceras
                    lastHeaders.addClass('colorTable');
                    lastHeaders.hide();
                    

                    // Obtener la última cabecera de la tabla (corresponde a las tres ultimas columnas)
                    var lastHeader = $('th:last-child', '#tbl_kardex');

                    // Agregar la clase CSS personalizada a esa cabecera
                    lastHeader.addClass('colorTable');
                    lastHeader.addClass('text-dark');
                    lastHeader.hide();
                    
                }
            });
            $("#tbl_kardex_length").hide();
            $("#tbl_kardex_filter").hide();
            $("#id_Status").hide();

            $('#id_txt_buscar').on('keyup', function() {        
                var vTablePedido = $('#tbl_kardex').DataTable();
                vTablePedido.search(this.value).draw();
            });
        }
    });

    
}

function tblMateriaPrima(){
    $.ajax({
        url: `getMateriaPrima`,
        type: 'get',
        async: true,
        success: function(data) {



            $('#table_materia_prima').DataTable({
                "data":data,
                "destroy" : true,
                "info":    false,
                order: [[1, 'desc']],
                "lengthMenu": [[5,-1], [5,"Todo"]],
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
                    { "data": "UND","render": function(data, type, row, meta) {
                        
                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+row.UND+`
                                    </div>
                                </div>`
                    }  },
                    { "data": "BLANCO_IMPRESO","render": function(data, type, row, meta) {

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.BLANCO_IMPRESO).format('0,0.00')+`
                                    </div>
                                </div>`
                    } },
                    { "data": "BLANCO_MEZCLADO","render": function(data, type, row, meta) {              

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.BLANCO_MEZCLADO).format('0,0.00')+`
                                    </div>
                                </div>`
                    } },
                    { "data": "TETRA_PACK","render": function(data, type, row, meta) {              

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.TETRA_PACK).format('0,0.00')+`
                                    </div>
                                </div>`
                    } },
                    { "data": "TERMOMECANICO","render": function(data, type, row, meta) {              

                        return  `<div class="pe-4 border-sm-end border-200" >
                                <div class="text-right">
                                `+numeral(row.TERMOMECANICO).format('0,0.00')+`
                                </div>
                            </div>`

                    } },
                    { "data": "PRENSA","render": function(data, type, row, meta) {
                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.PRENSA).format('0,0.00')+`
                                    </div>
                                </div>`
                    } },
                    { "data": "CARTON","render": function(data, type, row, meta) {
                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.CARTON).format('0,0.00')+`
                                    </div>
                                </div>`
                    } },
                    { "data": "FOLDER","render": function(data, type, row, meta) {
                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.FOLDER).format('0,0.00')+`
                                    </div>
                                </div>`
                    } },
                    { "data": "COLOR","render": function(data, type, row, meta) {
                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.COLOR).format('0,0.00')+`
                                    </div>
                                </div>`
                    } },
                ],
            })
            $("#table_materia_prima_length").hide();
            $("#table_materia_prima_filter").hide();
            $("#table_materia_prima_paginate").hide();


        }
    })
}

function tblResumen(){

    var ItemJumbo = '';

    $.ajax({
        url: `getResumenKardex`,
        type: 'get',
        async: true,
        success: function(data) {


            $('#table_resumen').DataTable({
                "data":data,
                "destroy" : true,
                "info":    false,
                order: [[1, 'desc']],
                "lengthMenu": [[5,-1], [5,"Todo"]],
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
                    { "data": "Product","render": function(data, type, row, meta) {
                        
                        var tBody = '';
                        ItemJumbo = '';
                        $.each(row.AT, function (i, obj) {
                            tBody += `<tr class="border-200">
                                        <td class="align-middle text-left ">
                                        `+ obj.DESCRIPCION +` |  `+ obj.ARTICULO +`
                                        </td>
                                    </tr>`                                       
                                    
                                    ItemJumbo += (obj.DESCRIPCION.includes("JUMBO R")) ? obj.ARTICULO + ' | ': ''
                        });

                        ItemJumbo = (ItemJumbo !== "") ?  ItemJumbo.slice(0, -2) : ''

                        
                        

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="d-flex align-items-center">
                                    <div class="tooltip-container">
                                        <h6 class="fs-0 text-900 mb-0 me-2">`+row.Product+`</h6>
                                        <span class="tooltip">
                                        <table class="table">
                                                <thead class="bg-200 text-900">
                                                <tr class="bg-primary text-light">
                                                    <th colspan="2">`+row.Product+`</th>
                                                </tr>
                                               
                                                </thead>
                                                <tbody>
                                                `+ tBody +`
                                                </tbody>
                                            </table>
                                        
                                        </span>
                                    </div>
                                    </div>
                                </div>`

                    }  },
                    { "data": "PT","render": function(data, type, row, meta) {

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.PT).format('0,0.00')+`
                                    </div>
                                </div>`

                    } },
                    { "data": "JR","render": function(data, type, row, meta) {              

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+ItemJumbo+`
                                    
                                    </div>
                                </div>`

                    } },
                    { "data": "JR","render": function(data, type, row, meta) {              

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.JR_KG).format('0,0.00')+` KG
                                    </div>
                                </div>`

                        } },
                        { "data": "JR","render": function(data, type, row, meta) {              

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.JR).format('0,0.00')+` B.
                                    </div>
                                </div>`

                        } },
                  
                    { "data": "TE","render": function(data, type, row, meta) {

                        return  `<div class="pe-4 border-sm-end border-200" >
                                    <div class="text-right">
                                    `+numeral(row.TE).format('0,0.00')+`
                                    </div>
                                </div>`

                    } },
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api();
                        var Total  = 0;

                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[^0-9.]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };

                        total = api.column( 3 ).data().reduce( function (a, b){
                            return intVal(a) + intVal(b);
                        }, 0 );

                        for (var i = 0; i < data.length; i++) {
                            Total += intVal(data[i].TE);
                        }
                        
                        $(api.column(4).footer()).html('<h6 class="fs-0 text-900 mb-0 me-2">TOTAL ESTIMADO EN BOLSONES: </h6>');
                        $(api.column(5).footer()).html('<h6 class="text-right">'+numeral(Total).format('0,0.00')+'</h6>');
                    }, 
            })
            $("#table_resumen_length").hide();
            $("#table_resumen_filter").hide();
            $("#table_resumen_paginate").hide();


        }
    })
}

</script>