<script type="text/javascript">

$('#id_credito_buscar').on('keyup', function() {        
    var vTableFacturas = $('#tbl_facturas').DataTable();
    vTableFacturas.search(this.value).draw();
});




function getFacturasRuta(){
        var mes = $('#id_credito_mes').val();
        var anno = $('#id_credito_year').val();
        var ruta = $('#id_credito_vendedor').val();

        $.ajax({
                url: "getFacturas",
                type: 'get',
                data: {
                    mes      : mes,
                    anno     : anno,
                    ruta     : ruta
                },
                async: false,
                success: function(response) {
                    $('#tbl_facturas').DataTable({
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
                            "emptyTable": "REALICE UNA BUSQUEDA UTILIZANDO LOS FILTROS",
                            "search":     "BUSCAR"
                        },
                        'columns': [
                            {    "data": "VENDEDOR", "render": function(data, type, row, meta) {

                                return  `<td class="align-middle ps-0 text-nowrap">
                                            <div class="d-flex position-relative align-items-center"><img class="d-flex align-self-center me-2" src="" alt="" width="30" />
                                            <div class="flex-1"><a class="stretched-link" href="#!">
                                                <h6 class="mb-0">FCT. `+row.FACTURA+` </h6>
                                            </a>
                                            <h7 class="mb-0">`+row.CLIENTE_CODIGO+` - `+row.Nombre_Cliente+`</h7>
                                            </div>
                                        </div></td>`

                            }},        
                            {   "data": "FACTURA", "render": function(data, type, row, meta) {
                                const fecha = new Date(row.Fecha_de_factura);
                                return  `<td class="align-middle px-4 text-end text-nowrap" style="width:1%;">
                                        <h6 class="mb-0">C$`+numeral(row.Monto).format('0,0.00')+` NIO</h6>
                                        <p class="fs--2 mb-0">`+fecha.getDate()+`/`+fecha.getMonth()+1+`/`+fecha.getFullYear()+`</p>
                                    </td>`

                            } },    
                            
                        ],
                    })
                    //OCULTA DE LA PANTALLA EL FILTRO DE PAGINADO Y FORM DE BUSQUEDA
                    $("#tbl_facturas_length").hide();
                    $("#tbl_facturas_filter").hide();
                }
        });
}
</script>