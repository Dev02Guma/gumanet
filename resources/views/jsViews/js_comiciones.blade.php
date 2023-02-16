<script type="text/javascript">
$(document).ready(function() {
    const fecha = new Date();
    $('#id_txt_History2').hide();

    dataComisiones(fecha.getMonth()+1, fecha.getFullYear());
});
    // INICIALIZA Y ASIGNA LA FECHA EN EL DATEPICKER
    const startOfMonth  = moment().subtract(1,'days').format('YYYY-MM-DD');
    const endOfMonth    = moment().subtract(0, "days").format("YYYY-MM-DD");
    var labelRange      = startOfMonth + " to " + endOfMonth;      
    $('#id_range_select').val(labelRange);

    $("#id_btn_new").click( function() {

        var mes = $('#id_select_mes').val();
        var anno = $('#id_select_year').val();

        dataComisiones(mes, anno);
    });

    $('#id_txt_buscar').on( 'keyup', function () {
        var table = $('#table_comisiones').DataTable();
        table.search(this.value).draw();
    });

    $( "#frm_lab_row").change(function() {
        var table = $('#table_comisiones').DataTable();
        table.page.len(this.value).draw();
    });
       
    $('#id_txt_History').on( 'keyup', function () {
        var table = $('#tb_history80').DataTable();
        
        $("#tb_history80_length").hide();
        $("#tb_history80_filter").hide();
        table.search(this.value).draw();
    });

    $('#id_txt_History2').on( 'keyup', function () {
        var table = $('#tb_history20').DataTable();
        
        $("#tb_history20_length").hide();
        $("#tb_history20_filter").hide();
        table.search(this.value).draw();
    });

    function dataComisiones(mes, anno){
        //var mes = $('#id_select_mes').val();
        //var anno = $('#id_select_year').val();

        $.ajax({
                url: "getDataComiciones",
                type: 'get',
                data: {
                    mes      : mes,
                    anno     : anno
                },
                async: false,
                success: function(response) {
                    $('#table_comisiones').DataTable({
                        "data":response,
                        "destroy" : true,
                        "info":    false,
                        "lengthMenu": [[5,10,-1], [5,10,"Todo"]],
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
                            {    "data": "NOMBRE", "render": function(data, type, row, meta) {

                                return  `   <div class="d-flex align-items-center position-relative mt-2">
                                                <div class="flex-1 ms-3" style="text-align: left;">
                                                    <h7 class="mb-0 fw-semi-bold"><a class="stretched-link text-900 fw-semi-bold" href="#!" id="itemHistory" idRuta="`+row.VENDEDOR+`"  data-toggle="modal" data-target="#modalHistoryItem"><div class="stretched-link text-dark"><b>`+row.NOMBRE+`</b></div></a></h7>
                                                    <p class="text-secondary fs--2 mb-0">`+row.VENDEDOR+` | `+row.ZONA+` </p>
                                                </div>
                                            </div>
                                        `

                            }},        
                            {   "data": "VENDEDOR", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Basico</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-1">C$ `+row.BASICO+` </h6>
                                            </div>
                                        </div>`

                            } },    
                            {   "data": "BASICO", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Ventas Val.</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">C$ `+numeral(row.DATARESULT.Comision_de_venta.Total[1]).format('0,0.00')+` </h6>
                                            </div>
                                        </div>`

                            } },
                            {    "data": "VENDEDOR", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Comición</b></h7>
                                            <div class="dropdown font-sans-serif btn-reveal-trigger">
                                            <button class="btn btn-link btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="dropdown-total-sales" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                                                <div class="d-flex align-items-center">
                                                <h6 class="fs-0 text-dark mb-0 me-2">C$ `+numeral(row.DATARESULT.Comision_de_venta.Total[3]).format('0,0.00')+`</h6>
                                                <span class="badge rounded-pill badge-primary">
                                                `+row.DATARESULT.Comision_de_venta.Total[2]+`%
                                                    </span>
                                                </div>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-total-sales">
                                                
                                            <table class="table" style="border: 2px solid black;">                                  
                                                <thead class="bg-200 text-900">
                                                <tr class="bg-primary text-light">
                                                    <th class="">CLASIF</th>
                                                    <th class="">SKU</th>
                                                    <th class="">Val. C$.</th>
                                                    <th class="">Fct.%</th>
                                                    <th class="">Comision</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">80% </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Lista80[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista80[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+row.DATARESULT.Comision_de_venta.Lista80[2]+` %</td>                                          
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista80[3]).format('0,0.00')+` </td>
                                                </tr>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">20% </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Lista20[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+row.DATARESULT.Comision_de_venta.Lista20[2]+` %</td>                                          
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20[3]).format('0,0.00')+` </td>
                                                </tr>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">Total </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Total[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Total[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+row.DATARESULT.Comision_de_venta.Total[2]+` %</td>                                          
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Total[3]).format('0,0.00')+` </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table class="table" style="border: 2px solid black;"> 
                                                <thead class="bg-200 text-900">
                                                <tr class="bg-primary text-light">
                                                    <th colspan="2">ANULACIÓN NOTA DE CRÉDITOS</th>
                                                    
                                                </tr>
                                                <tr>                       
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                </thead>     
                                                <tbody>
                                                    <tr class="border-200">
                                                        <td class="align-middle">
                                                        <h6 class="mb-0 text-nowrap">80% </h6>
                                                        </td>
                                                        <td class="align-middle text-right ">C$ `+numeral(row.DATARESULT.NotaCredito_val80).format('0,0.00')+` </td>
                                                    </tr>
                                                    <tr class="border-200">
                                                        <td class="align-middle">
                                                        <h6 class="mb-0 text-nowrap">20% </h6>
                                                        </td>
                                                        <td class="align-middle text-right">C$ `+numeral(row.DATARESULT.NotaCredito_val20).format('0,0.00')+` </td>
                                                    </tr>
                                                    <tr class="border-200">
                                                        <td class="align-middle">
                                                        <h6 class="mb-0 text-nowrap">Total </h6>
                                                        </td>
                                                        <td class="align-middle text-right">C$ `+numeral(row.DATARESULT.NotaCredito_total).format('0,0.00')+` </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            </div>
                                            </div>
                                            
                                        </div>`

                            }},        
                            {   "data": "VENDEDOR", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Prom.</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">`+row.DATARESULT.Totales_finales[4]+`</h6>
                                            </div>
                                        </div>`

                            } },    
                            {  "data": "BASICO", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Meta.</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">`+row.DATARESULT.Totales_finales[5]+`</h6>
                                            </div>
                                        </div>`

                            } },
                            {    "data": "NOMBRE", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Fact.</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">`+row.DATARESULT.Totales_finales[6]+`</h6>
                                            </div>
                                        </div>`

                            }},        
                            {   "data": "VENDEDOR", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Bono.Cobertura</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-dark mb-0 me-2">C$ `+numeral(row.DATARESULT.Totales_finales[0]).format('0,0.00')+`</h6>
                                            <span class="badge rounded-pill badge-primary">`+row.DATARESULT.Totales_finales[3]+`%</span>
                                            </div>
                                        </div> `

                            } },    
                            {   "data": "BASICO", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200" >
                                            <h7 class="fs--2 text-secondary mb-1"><b>Comición + Bono</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">C$ `+numeral(row.DATARESULT.Totales_finales[1]).format('0,0.00')+`</h6>
                                            </div>
                                        </div>`

                            } },
                            {   "data": "BASICO", "render": function(data, type, row, meta) {

                                return  `<div class="">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Total Comp.</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">C$ `+numeral(row.DATARESULT.Total_Compensacion).format('0,0.00')+`</h6>                                  
                                            </div>
                                        </div>`

                            } },
                        ],
                        "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api();
                        var Total       = 0;

                        var intVal = function ( i ) {
                                return typeof i === 'string' ?
                                i.replace(/[^0-9.]/g, '')*1 :
                                typeof i === 'number' ?
                                i : 0;
                            };
                            
                        total = api.column( 4 ).data().reduce( function (a, b){
                            return intVal(a) + intVal(b);
                        }, 0 );

                        for (var i = 0; i < data.length; i++) {
 
                            Total += intVal(data[i].DATARESULT.Total_Compensacion);
                        }
                        //Total = Pendiete + Ingresado + Verificado;
                            
                        $(api.column(8).footer()).html('<h6 class="fs-0 text-900 mb-0 me-2">TOTAL PAGADO EN COMICIONES: </h6>');
                        $(api.column(9).footer()).html('<div class="d-flex align-items-center"><h6 class="fs-0 text-900 mb-0 me-2">C$ '+numeral(Total).format('0,0.00')+'</h6></div>');
                    },
                    })
                    //OCULTA DE LA PANTALLA EL FILTRO DE PAGINADO Y FORM DE BUSQUEDA
                    $("#table_comisiones_length").hide();
                    $("#table_comisiones_filter").hide();
                }
        });
    }

    

    $(document).on('click', '#itemHistory', function(ef) {
        const fecha = new Date();
        var i = j = venta = meta = valor = k = l = 0;
        $('#id_txt_History2').val("");

        $('#id_txt_History').val("");
        var mes = fecha.getMonth()+1;
        var anno = fecha.getFullYear();
        var ruta = $(this).attr('idRuta');
        var thead = tbody = thead2 = tbody2 ='';

            $.ajax({
            url: "getHistoryItem",
            type: 'GET',
            data:{
                mes :   mes,
                anno:   anno,
                ruta:   ruta
            },
            async: true,
            success: function(response) {
                 thead =`<table class="table table-striped table-bordered table-sm" id="tb_history80" width="100%">
                        <thead c class="bg-blue text-light">
                            <tr>
                                <th class="center" width="10%">ARTICULO</th>
                                <th class="center" width="60%">DESCRIPCION</th>
                                <th class="center" width="10%">P.UNIT</th>
                                <th class="center" width="10%">META UND</th>
                                <th class="center" width="10%">VENTA UND</th>
                                <th class="center" width="10%">VENTA VAL.</th>
                                <th class="center" width="10%">CUM.%</th>
                                <th class="center" width="10%">CUM. META</th>
                                
                            </tr>
                        </thead>
                        </tbody>`;
                    thead2 =`<table class="table table-striped table-bordered table-sm" id="tb_history20" width="100%">
                        <thead c class="bg-blue text-light">
                            <tr>
                                <th class="center" width="10%">ARTICULO</th>
                                <th class="center" width="60%">DESCRIPCION</th>
                                <th class="center" width="10%">P.UNIT</th>
                                <th class="center" width="10%">META UND</th>
                                <th class="center" width="10%">VENTA UND</th>
                                <th class="center" width="10%">VENTA VAL.</th>
                                <th class="center" width="10%">CUM.%</th>
                                <th class="center" width="10%">CUM. META</th>
                                
                            </tr>
                        </thead>
                        </tbody>`;
                $.each( response, function( key, item ) {
                    if(item['Lista'] === '80'){
                        i += 1;
                    }else{
                        j +=1;
                    }
                    valor += Number(item['VentaVAL']);
                    meta += Number(item['MetaUND']);
                    venta += Number(item['VentaUND']);
                    if(item['Lista'] == 80){
                        if(item['VentaUND'] > 0){
                            tbody += '<tr style="background-color:rgb(134 239 172);">';
                            k += 1;
                        }else{
                            tbody += '<tr>';
                        }
                        tbody +='<td class="text-center" >' + item['ARTICULO'] + '</td>'+
                                '<td width="60%">' + item['DESCRIPCION'] + '</td>'+
                                '<td class="text-center text-right">C$ ' + numeral(item['Venta']).format('0,0.00') + '</td>'+
                                '<td class="text-center text-right">' + numeral(item['MetaUND']).format('0,0') + '</td>'+
                                '<td class="text-right">' + numeral(item['VentaUND']).format('0,0') + '</td>'+
                                '<td class="text-right">C$ ' + numeral(item['VentaVAL']).format('0,0.00') + '</td>'+
                                '<td class="text-right">' + numeral(item['Cumple']).format('0,0.00') + '</td>'+
                                '<td class="text-center text-right">' + item['isCumpl'] + '</td>'+
                            '</tr>';
                    }
                    if(item['Lista'] == 20){
                        if(numeral(item['VentaUND']).format('0,0') > 0){
                            tbody2 += '<tr  style="background-color:rgb(134 239 172);">';
                            l += 1;
                        }else{
                            tbody2 += '<tr>';
                        }
                        tbody2 +='<td class="text-center" >' + item['ARTICULO'] + '</td>'+
                                '<td width="60%">' + item['DESCRIPCION'] + '</td>'+
                                '<td class="text-center text-right">C$ ' + numeral(item['Venta']).format('0,0.00') + '</td>'+
                                '<td class="text-center text-right">' + numeral(item['MetaUND']).format('0,0') + '</td>'+
                                '<td class="text-right">' + numeral(item['VentaUND']).format('0,0') + '</td>'+
                                '<td class="text-right">C$ ' + numeral(item['VentaVAL']).format('0,0.00') + '</td>'+
                                '<td class="text-right">' + numeral(item['Cumple']).format('0,0.00') + '</td>'+
                                '<td class="text-center text-right">' + item['isCumpl'] + '</td>'+
                            '</tr>';
                    }
                })
                
                $('#lbl_80').html(k+' / '+i+' <span class="badge rounded-pill badge-light text-primary"><span class="fas fa-caret-up text-primary"></span> '+ numeral((k/i)*100).format('0,0.00')+' %</span>');
                $('#lbl_20').html(l+' / '+j+' <span class="badge rounded-pill badge-light text-primary"><span class="fas fa-caret-up text-primary"></span> '+ numeral((l/j)*100).format('0,0.00')+' %</span>');
                $('#lbl_venta').html(numeral(venta).format('0,0'));
                $('#lbl_meta').html(numeral(meta).format('0,0'));
                $('#lbl_val').html(numeral(valor).format('0,0.00'));
                tbody += `</tbody> </table>`;
                tbody2 += `</tbody> </table>`;
                temp = thead + tbody;
                $("#sku-80").empty().append(temp);
                temp2 = thead2 + tbody2;
                $("#sku-20").empty().append(temp2);
                $('#tb_history80').DataTable({
                    "destroy": true,
                    "info": false,
                    "lengthMenu": [
                        [10, -1],
                        [10, "Todo"]
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
                });
                $('#tb_history20').DataTable({
                    "destroy": true,
                    "info": false,
                    "lengthMenu": [
                        [10, -1],
                        [10, "Todo"]
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
                });
                $("#tb_history80_length").hide();
                $("#tb_history80_filter").hide();
                $("#tb_history20_length").hide();
                $("#tb_history20_filter").hide();
                
            },
            
        });

        
        
    });
    
    $("#sku-20-tab").click( function() {
        $('#id_txt_History').hide();
        $('#id_txt_History2').show();
        $('#sku-80-tab').removeClass('bg-blue');
        $('#sku-80-tab').removeClass('text-light');
        $('#sku-80-tab').addClass('text-dark');

        $(this).removeClass('text-dark');
        $(this).addClass('bg-blue');
        $(this).addClass('text-light');
    });

    $("#sku-80-tab").click( function() {
        $('#id_txt_History2').hide();
        $('#id_txt_History').show();
        $('#sku-20-tab').removeClass('bg-blue');
        $('#sku-20-tab').removeClass('text-light');
        $('#sku-20-tab').addClass('text-dark');

        $(this).removeClass('text-dark');
        $(this).addClass('bg-blue');
        $(this).addClass('text-light');
    });

</script>
