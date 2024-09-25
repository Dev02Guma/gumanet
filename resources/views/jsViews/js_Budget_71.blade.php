<script type="text/javascript">
    $(document).ready(function() {
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Presupuesto</li>`);
    inicializaControlFecha();    
    DrawTable71();

});
    $('#txt_Search71').on( 'keyup', function () {
        var table = $('#dtProyect71').DataTable();
        table.search(this.value).draw();
    });

    $(document).on('click', '#exp_more_71', function(ef) {
        var table = $('#dtProyect71').DataTable();
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

            Draw_Table_71(row.child,data);
            tr.addClass('shown');
            
            ef.target.innerHTML = "expand_less";
            ef.target.style.background = '#ff5252';
            ef.target.style.color = '#e2e2e2';
        }
    });

    function Draw_Table_71 ( callback, dta ) {    
        var table = thead = tBody  = '';
        $.each(dta.FECHA, function (i, item) {
            
            var month_UND = item.mes + '_UND'
            var month_UND_B = item.mes + '_UND_B'
            var month_VAL = item.mes + '_VAL'

            var TotalUnits = isValue(dta[month_UND],0,true)  + isValue(dta[month_UND_B],0,true) ;

            thead += `<th class="center">`+item.mes+`</th>`;

            tBody +=  `<td><table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th class="bg-blue text-light">UNITS FACT.</th>   
                                    <th class="bg-blue text-light">UNITS BONIF.</th>      
                                    <th class="bg-blue text-light">UNITS TOTAL.</th>                                   
                                    <th class="bg-blue text-light">MONTO FACT. C$</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> <p class="text-right">`+numeral(dta[month_UND]).format('0,0')+`</p></td>
                                    <td> <p class="text-right">`+numeral(dta[month_UND_B]).format('0,0')+`</p></td>
                                    <td> <p class="text-right">`+numeral(TotalUnits).format('0,0')+`</p></td>
                                    <td> <p class="text-right">`+numeral(dta[month_VAL]).format('0,0')+`</p></td>
                                </tr>
                            </tbody>
                        </table></td>`;



        });

        table = `<table class="table table-striped table-bordered table-sm">
                        <thead class="text-center bg-secondary text-black">
                            <tr>`+thead+`</tr>
                        </thead>
                        <tbody>
                            <tr>`+tBody+`</tr>
                        </tbody>
                        </table>`;



        

    callback(table).show();
        
    }

    $("#btnTable71").click( function() {
        TblInit([], [{ "title": "DETALLE"},   
                { "title": "ARTICULO"},
                {"title": "DESCRIPCION"},
                { "title": "TOTAL INVEN."},
                { "title": "UNITS. FACT."},
                { "title": "MONTO FACT. C$"},
                
                {"title": "PREC. PROM"},
                {"title": "CONTRIBUCION"},
                {"title": "% MARGEN BRUTO"}],'#dtProyect71',[3,4,5,6,7,8]);
        DrawTable71();
    
    });

    function DrawTable71(){



        f1 = $("#f1_p71").val();
        f2 = $("#f2_p71").val();

        $("#spn_dtIni_71").html(moment(f1).format('DD/MMM/YY'))
        $("#spn_dtEnd_71").html(moment(f2).format('DD/MMM/YY'))

        $("#Id_Progress_Bar_71").empty().append(`<div>
                        <div class="d-flex align-items-center">
                            <strong class="text-info">Calculando...</strong>
                            <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                        </div>
                    </div>`);



        $.getJSON("dtProyect?f1="+f1+"&f2="+f2+"&pr=2", function(dataset) {

            var c = 2 ; 
            var Header_Align = [];
            var tbl_header = [];
            tbl_header = [
                { "title": "DETALLE",      "data": "DETALLE" },   
                { "title": "ARTICULO",      "data": "ARTICULO" },
                {"title": "DESCRIPCION",    "data": "DESCRIPCION", "render": function(data, type, row, meta) { 
                    return`<a href="#!" onclick="OpenModal_Pro71(`+ "'" +row.ARTICULO + "'" +` )" >`+ row.DESCRIPCION +`</a>`
                }},
                { "title": "TOTAL INVEN.",  "data": "TOTAL_INVENTARIO", render: $.fn.dataTable.render.number(',', '.', 0, '') },
                { "title": "UNITS. FACT.",   "data": "CANTI_FACT_MES", render: $.fn.dataTable.render.number(',', '.', 0, '') },
                { "title": "MONTO FACT. C$",  "data": "VALOR_FACT_MES", render: $.fn.dataTable.render.number(',', '.', 0, '') },
                
                {"title": "PREC. PROM",    "data": "CANTI_FACT_MES", "render": function(data, type, row, meta) { 
                    var prec_prom = row.VALOR_FACT_MES / row.CANTI_FACT_MES
                    return numeral(prec_prom).format('0,0');
                }},
                {"title": "CONTRIBUCION",    "data": "VALOR_FACT_MES", "render": function(data, type, row, meta) { 

                    var monto_contribucion = row.VALOR_FACT_MES -  ( row.CANTI_FACT_MES * row.COSTO_PROM)

                    monto_contribucion = (monto_contribucion < 0) ? 0.00 : monto_contribucion;

                    return numeral(monto_contribucion).format('0,0');
                }},
                {"title": "% MARGEN BRUTO",    "data": "VALOR_FACT_MES", "render": function(data, type, row, meta) { 

                    var monto_contribucion      = row.VALOR_FACT_MES -  ( row.CANTI_FACT_MES * row.COSTO_PROM)
                    var porcent_contribucion    = (row.VALOR_FACT_MES / monto_contribucion ) *  100

                    
                    //var porcent_contribucion = (( prec_prom - row.COSTO_PROM ) / prec_prom) * 100;

                    porcent_contribucion = (porcent_contribucion < 0) ? 0 : porcent_contribucion;


                   // var porcent_contribucion = row.VALOR_FACT_MES / row.CANTI_FACT_MES
                    return numeral(porcent_contribucion).format('0,0');
                }},
                
            ];

            TblInit(dataset, tbl_header,'#dtProyect71',[3,4,5,6,7,8]);


        })
    }
    function OpenModal_Pro71(ARTICULO) {
        $("#idArti").val(ARTICULO);
        $("#mdl_char_product").modal();
        bluid_char(ARTICULO,2,1);
    }

    $( "#orderComportamiento89").change(function() {
        valor = $( this ).val()  
        var articulo = $("#idArti").val();
        
        bluid_char(articulo, 2, valor);
    });

    function TblInit(datos, Header,Table,Align) {


        if ( $.fn.DataTable.isDataTable(Table) ) {

            var dataTable = $(Table).DataTable();

            dataTable.clear().destroy();

            $(Table).empty();
            

            
        }
        var ObjTable = $(Table).DataTable({
            "data": datos,
            "destroy" : true,
            "info":    true,
            "order": [[2, 'asc']],
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
                "emptyTable": " Calculando . . . ",
                "search":     "BUSCAR"
            },
            'columns': Header,
            fixedColumns: {
                left: 6,
                right: 0
            },
            paging: false,
            scrollCollapse: true,
            scrollX: true,
            scrollY: 500,
            "columnDefs": [
                {"className": "dt-center","targets": [0]},
                {"className": "dt-right","targets": Align},
                
            ],
            
            "footerCallback": function ( row, data, start, end, display ) {
                //$("#IdCardTitle").text("Proyecto 89") 
                $("#Id_Progress_Bar_71").empty();
            
            },
        });

            
        ObjTable.columns().header().each(function (columnHeader) {
            $(columnHeader).addClass('bg-blue text-light');
        });

        $(Table + "_length").hide();
        $(Table + "_filter").hide();


}


</script>