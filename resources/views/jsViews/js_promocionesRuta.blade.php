<script type="text/javascript">
    var dta_table_header = [];
    // INICIALIZA Y ASIGNA LA FECHA EN EL DATEPICKER
    const RangerStartOfMonth  = moment().subtract(1,'days').format('YYYY-MM-DD');
    const RangerEndOfMonth    = moment().subtract(0, "days").format("YYYY-MM-DD");
    var lblRange              = RangerStartOfMonth + " to " + RangerEndOfMonth;      
    $('#id_range_select').val(lblRange);

    var Selectors = {        
        ADD_ITEM_RUTA: '#modl_view_detalles_ruta',
        ADD_PROMOCION: '#modl_add_promocion',
    };

    // INICIALIZA LA DATATABLE CON LOS VALORES POR DEFECTO 
    $("#table_promociones").DataTable({
        "destroy": true,
        "info": false,
        "bPaginate": true,
        "lengthMenu": [
            [10,20, -1],
            [10,20, "Todo"]
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

    //OCULTA DE LA PANTALLA EL FILTRO DE PAGINADO Y FORM DE BUSQUEDA
    $("#table_promociones_length").hide();
    $("#table_promociones_filter").hide();

    //NUMERO DE REGISTROS MOSTRADOS EN PANTALLA
    $( "#frm_lab_row").change(function() {
        var table = $('#table_promociones').DataTable();
        table.page.len(this.value).draw();
    });

    //HABILITA LA BUSQUEDA DENTRO DE LA TABLA
    $('#id_txt_buscar').on('keyup', function() {        
        var vTablePedido = $('#table_promociones').DataTable();
        vTablePedido.search(this.value).draw();
    });
    
    function OpenModal(Promo){
        var addMultiRow = document.querySelector(Selectors.ADD_ITEM_RUTA);
        var modal = new window.bootstrap.Modal(addMultiRow);
        modal.show();

        
        $('#id_num_prom').html(Promo.id);
        $('#id_lbl_nombre').html(Promo.Titulo);
        
        $('#nombre_ruta_modal').html(Promo.vendor.NOMBRE);        
        $('#nombre_ruta_zona_modal').html(Promo.vendor.VENDEDOR + " | " + Promo.zona.Zona);
        $('#id_lbl_fechas').html("Valido desde " + Promo.fecha_ini + " al " + Promo.fecha_end);
        
        //BluidTable(Detalles)
        getDetalles(Promo.id)
    }
    function getDetalles(IdPromo) {
        $.ajax({
                url: "getDetalles",
                type: 'GET',
                data: {
                    IdPromo         : IdPromo
                },
                async: true,
                success: function(response) {
                    BluidTable(response)
                }
            })
    }
    
    function BluidTable(Obj) {
        data_array = Obj
        dta_table_header = [
            {"title": "Index","data": "id"},
            {"data": "Articulo",
                "render": function(data, type, row, meta) {
                return `<div class="d-flex align-items-center position-relative">                                        
                            <div class="flex-1 ">
                                <h6 class="mb-0 fw-semi-bold"><div class="stretched-link text-primary">`+ row.Descripcion +`</div></h6>
                                <p class="text-500 fs--2 mb-0">`+ row.Articulo +`  </p>
                            </div>
                        </div>`
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">                                    
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+ numeral(row.Precio).format('0,0,00.00')  +` </h5>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">                                    
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+ row.NuevaBonificacion +` </h5>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+  numeral(row.ValorVinneta).format('0,0,00.00')  +` </h5>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+ numeral(row.Promedio_VAL).format('0,0,00.00') +`</h5>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+  numeral(row.ValMeta).format('0,0,00.00') +` </h5>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">
                                    
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+  numeral(row.Venta).format('0,0,00.00') +` </h5>
                                        <span class="badge rounded-pill bg-primary text-light">`+  numeral(row.PromVenta).format('0,0,00.00') +`%</span>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+ row.Promedio_UND +`</h5>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2">`+ row.MetaUnd +` </h5>
                                    </div>
                                </div> `
            }},
            {"data": "",
                "render": function(data, type, row, meta) {
                return `<div class="pe-4 border-sm-end border-200">
                                    <div class="d-flex align-items-center">
                                        <h5 class="fs-0 text-900 mb-0 me-2"> `+ row.VentaUND +` </h5>
                                        <span class="badge rounded-pill bg-primary text-light">`+ numeral(row.PromVentaUND).format(0,0.00) +` %</span>
                                    </div>
                                </div> `
            }},                    
        ]
        table_render('#tbl_excel',data_array,dta_table_header,false)
    }

    function table_render(Table,datos,Header,Filter){
        
        var txt_ttMetaValor = 0 ;
        var txt_ttVenta     = 0 ;
        var txt_ttMetaUND   = 0 ;
        var txt_ttVentaUND  = 0 ;

        $(Table).DataTable({
            "data": datos,
            "destroy": true,
            "info": false,
            "bPaginate": true,
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [5,10, -1],
                [5,10, "Todo"]
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
                { "width":"150%", "targets": [ 1 ] }
            ],
            "createdRow": function( row, data, dataIndex ) {    
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                    i.replace(/[^0-9.]/g, '')*1 :
                    typeof i === 'number' ?
                    i : 0;
                };
                txt_ttMetaValor += intVal(data.ValMeta)
                txt_ttVenta += intVal(data.Venta)
                txt_ttMetaUND += intVal(data.MetaUnd)
                txt_ttVentaUND += intVal(data.VentaUND)
                

              
            },
            rowCallback: function( row, data, index ) {
                if ( data.Index < 0 ) {
                    $(row).addClass('table-danger');
                } 
            }
        });
        $('#id_ttMetaValor').text("C$ " + numeral(txt_ttMetaValor).format('0,0.00'));
        $('#id_ttVenta').text("C$ " + numeral(txt_ttVenta).format('0,0.00'));
        $('#id_ttMetaUND').text(numeral(txt_ttMetaUND).format('0,0'));
        $('#id_ttVentaUND').text(numeral(txt_ttVentaUND).format('0,0'));
        if(!Filter){
            $(Table+"_length").hide();
            $(Table+"_filter").hide();
        }

    }

    if ( $("#id_spinner_load").hasClass('visible') ) {
            $("#id_spinner_load").removeClass('visible');
            $("#id_spinner_load").addClass('invisible');
        }
    
</script>
