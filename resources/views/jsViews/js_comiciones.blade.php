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
        var table = $('#tb_history2023').DataTable();

        $("#tb_history2023_length").hide();
        $("#tb_history2023_filter").hide();
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
                                                    <h7 class="mb-0 fw-semi-bold"><a class="stretched-link text-900 fw-semi-bold" href="#!" id="itemHistory" idRuta="`+row.VENDEDOR+`" iZona="`+row.ZONA+`"  data-toggle="modal" data-target="#modalHistoryItem"><div class="stretched-link text-dark"><b>`+row.NOMBRE+`</b></div></a></h7>
                                                    <p class="text-secondary fs--2 mb-0">`+row.VENDEDOR+` | `+row.ZONA+` </p>
                                                </div>
                                            </div>
                                        `

                            }},
                        {   "data": "VENDEDOR", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Salario Garantizado</b></h7>
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
                                            <h7 class="fs--2 text-secondary mb-1"><b>Comisión</b></h7>
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
                                                    <th class="">N. Crédito</th>
                                                    <th class="">Comision</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">SKU_80 </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Lista80[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista80[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+row.DATARESULT.Comision_de_venta.Lista80[2]+` %</td>
                                                    <td class="align-middle text-right ">C$ `+numeral(row.DATARESULT.NotaCredito_val80).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista80[3]).format('0,0.00')+` </td>
                                                </tr>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">SKU_20_A </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Lista20A[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20A[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+row.DATARESULT.Comision_de_venta.Lista20A[2]+` %</td>
                                                    <td class="align-middle text-right">C$ `+numeral(row.DATARESULT.NotaCredito_val20).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20A[3]).format('0,0.00')+` </td>
                                                </tr>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">SKU_20_B </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Lista20B[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20B[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+row.DATARESULT.Comision_de_venta.Lista20B[2]+` %</td>
                                                    <td class="align-middle text-end">C$  - </td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20B[3]).format('0,0.00')+` </td>
                                                </tr>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">SKU_20_C </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Lista20C[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20C[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+row.DATARESULT.Comision_de_venta.Lista20C[2]+` %</td>
                                                    <td class="align-middle text-end">C$  - </td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Lista20C[3]).format('0,0.00')+` </td>
                                                </tr>
                                                <tr class="border-200">
                                                    <td class="align-middle">
                                                    <h6 class="mb-0 text-nowrap">Total </h6>
                                                    </td>
                                                    <td class="align-middle text-center">`+row.DATARESULT.Comision_de_venta.Total[0]+`</td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Total[1]).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end">  - </td>
                                                    <td class="align-middle text-right">C$ `+numeral(row.DATARESULT.NotaCredito_total).format('0,0.00')+` </td>
                                                    <td class="align-middle text-end ">`+numeral(row.DATARESULT.Comision_de_venta.Total[3]).format('0,0.00')+` </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            
                                            </div>
                                            </div>

                                        </div>`

                            }},
                        {   "data": "VENDEDOR", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Prom. CLTE.</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">`+row.DATARESULT.Totales_finales[4]+`</h6>
                                            </div>
                                        </div>`

                            } },
                        {  "data": "BASICO", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Meta. CLTE.</b></h7>
                                            <div class="d-flex align-items-center">
                                            <h6 class="fs-0 text-900 mb-0 me-2">`+row.DATARESULT.Totales_finales[5]+`</h6>
                                            </div>
                                        </div>`

                            } },
                        {    "data": "NOMBRE", "render": function(data, type, row, meta) {

                                return  `<div class="pe-4 border-sm-end border-200">
                                            <h7 class="fs--2 text-secondary mb-1"><b>Fact. CLTE.</b></h7>
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
                                            <h7 class="fs--2 text-secondary mb-1"><b>Comisión</b></h7>
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

                        $(api.column(8).footer()).html('<h6 class="fs-0 text-900 mb-0 me-2">TOTAL PAGADO EN COMISIONES: </h6>');
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
        $('#id_txt_History2').val("");

        $('#id_txt_History').val("");
        var mes = $('#id_select_mes').val();
        var anno = $('#id_select_year').val();
        var ruta = $(this).attr('idRuta');
        var Zona = $(this).attr('iZona');
        var nombre = $(this).text();

        var ventaValor  = 0;
        var VentaUND    = 0;
        var MetaUND     = 0;
        var Item80      = 0;
        var Item20      = 0;
        var ItemC80     = 0;
        var ItemC20     = 0;

        $.ajax({
            url: "getHistoryItem",
            type: 'GET',
            data:{
                mes :   mes,
                anno:   anno,
                ruta:   ruta
            },
            async: true,
            dataType: "json",
            success: function(data) { 

                Item80      = data['LISTA_80']
                Item20      =  data['LISTA_20']

                ItemC80     = data['LISTA_80C_FACT']
                ItemC20     = data['LISTA_20_FACT']

                if (data['dt'].length > 0) {
                    dta_table_80 = []; 
                    dta_table_20 = [];
                    dta_table_header = [
                        {"data": "ROW_ID"},
                        {"data": "ARTICULO",
                            "render": function(data, type, row, meta) {
                               
                                return `<div class="d-flex align-items-center position-relative ">
                                    <div class="flex-1">
                                        <h6 class="mb-0 fw-semi-bold">
                                            <div class="stretched-link text-dark">`+ row.DESCRIPCION +`</div>
                                        </h6>
                                        <p class="text-secondary fs--2 mb-0">`+ row.ARTICULO +` </p>
                                    </div>
                                </div>`
                            }},
                        {"data": "MetaUND","render": function(data, type, row, meta) {return data + ' UND'}},
                        {"data": "VentaUND","render": function(data, type, row, meta) {return data + ' UND'}},
                        {"data": "VentaVAL","render": function(data, type, row, meta) {
                                return `<div class="pe-4">
                                <div class="d-flex align-items-center">
                                  <h6 class="fs-0 text-dark mb-0 me-2">C$ `+ row.VentaVAL +`</h6>
                                  <span class="badge rounded-pill badge-primary">`+ row.Cumple +` %</span>
                                </div>
                              </div>`
                            }},
                        {"data": "isCumpl","render": function(data, type, row, meta) {
                                var lbl = '';
                                if ( row.isCumpl == 'SI' ) {
                                    lbl = '<span class="badge badge rounded-pill d-block p-2 badge-primary">Cumplio<span class="ms-1 fas fa-dollar-sign" data-fa-transform="shrink-2"></span></span>'
                                }
                                return lbl
                            }},
                    ]

                    $.each(data['dt'],function(key, registro) {

                        ventaValor  += parseFloat(numeral(registro.VentaVAL).format('00.00'));
                        VentaUND    += parseFloat(registro.VentaUND.replace(/,/g, ''), 10);
                        MetaUND     += parseFloat(registro.MetaUND.replace(/,/g, ''), 10);
                       
                        if(registro.Lista == 'SKU_80'){
                            dta_table_80.push({
                                ROW_ID: registro.ROW_ID,
                                ARTICULO: registro.ARTICULO,
                                DESCRIPCION: registro.DESCRIPCION,
                                Venta: numeral(registro.Venta).format('0,0,00.00'),
                                Aporte: numeral(registro.Aporte).format('0,0,00.00'),
                                Lista: registro.Lista,
                                MetaUND: registro.MetaUND,
                                VentaUND: registro.VentaUND,
                                VentaVAL: numeral(registro.VentaVAL).format('0,0,00.00'),
                                Cumple: numeral(registro.Cumple).format('0,0,00.00') ,
                                isCumpl: registro.isCumpl
                            })
                        }else if(registro.Lista == 'SKU_20_A' || registro.Lista == 'SKU_20_B' || registro.Lista == 'SKU_20_C'){
                            dta_table_20.push({
                                ROW_ID: registro.ROW_ID,
                                ARTICULO: registro.ARTICULO,
                                DESCRIPCION: registro.DESCRIPCION,
                                Venta: numeral(registro.Venta).format('0,0,00.00'),
                                Aporte: numeral(registro.Aporte).format('0,0,00.00'),
                                Lista: registro.Lista,
                                MetaUND: registro.MetaUND,
                                VentaUND: registro.VentaUND,
                                VentaVAL: numeral(registro.VentaVAL).format('0,0,00.00'),
                                Cumple: numeral(registro.Cumple).format('0,0,00.00') ,
                                isCumpl: registro.isCumpl
                            })
                        }
                    });

                    table_render('#tb_history80',dta_table_80,dta_table_header,false)
                    table_render('#tb_history2023',dta_table_20,dta_table_header,false)

                    ventaValor = "C$ " +numeral(ventaValor).format('0,0,00.00')
                    $("#lbl_val").text(ventaValor)

                    VentaUND = numeral(VentaUND).format('0,0,00')
                    $("#lbl_venta").text(VentaUND)

                    MetaUND = numeral(MetaUND).format('0,0,00')
                    $("#lbl_meta").text(MetaUND)

                    $("#lbl_80").text(ItemC80 + " / " + Item80 )
                    $("#lbl_20").text(ItemC20 + " / " + Item20)

                    var v80 = (((ItemC80 / Item80 ) * 100) )
                    var v20 = (((ItemC20 / Item20 ) * 100) )

                    v80 = numeral(v80).format('0,0,00.00')
                    v20 = numeral(v20).format('0,0,00.00')

                    $("#id_prom_ls80").text(v80+" %")
                    $("#id_prom_ls20").text(v20+" %")

                    $("#nombre_ruta_modal").text(nombre)
                    $("#nombre_ruta_zona_modal").text(ruta + " | " + Zona)


                }

            },

        });

    });

    function table_render(Table,datos,Header,Filter){
        $(Table).DataTable({
            "data": datos,
            "destroy": true,
            "info": false,
            "bPaginate": true,
            "order": [
                [0, "asc"]
            ],
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
            'columns': Header,
            "columnDefs": [
                {
                    "visible": false,
                    "searchable": false,
                    "targets": [0]
                },
                { "width": "60%", "targets": [1] },
                { "width": "10%", "targets": [2,3,4,5] },

            ],
            "createdRow": function( row, data, dataIndex ) {
                if ( data.VentaUND > '0.00') {
                    $(row).addClass('table-success');
                }else{
                    $(row).addClass('table-white');
                }
            },
            rowCallback: function( row, data, index ) {
                if ( data.Index < 0 ) {
                    $(row).addClass('table-danger');
                }
            }
        });
        if(!Filter){
            $(Table+"_length").hide();
            $(Table+"_filter").hide();
        }
    }

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
