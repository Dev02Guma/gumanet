<script type="text/javascript">

$(document).ready(function() {
    $("#item-nav-01").after(`<li class="breadcrumb-item active">Presupuesto</li>`);
    inicializaControlFecha();    
    CalcIndicadores_89();
    DrawTable71();

});


$(document).on('click', '#exp_more', function(ef) {
    var table = $('#dtProyect89').DataTable();
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

        Draw_Table(row.child,data);
        tr.addClass('shown');
        
        ef.target.innerHTML = "expand_less";
        ef.target.style.background = '#ff5252';
        ef.target.style.color = '#e2e2e2';
    }
});


function Draw_Table ( callback, dta ) {    
    var table = thead = tBody  = '';
    $.each(dta.FECHA, function (i, item) {

        var month_UND   = item.mes + '_UND'
        var month_UND_B = item.mes + '_UND_B'
        var month_VAL   = item.mes + '_VAL'

        //var Cumpli = (( dta[month_VAL] / dta.VAL_MES ) * 100) 
        // var Cumpli = (dta[month_VAL] * 100 ) / dta.VAL_MES
        var Cumpli = (dta[month_UND] * 100 ) / dta.UND_MES
        var prec_prom = dta[month_VAL] / dta[month_UND] ; 

        //var Contribucion  =  dta[month_VAL] - dta[month_UND] * dta.COSTO_PROM
        // var prom_contribucion = (( prec_prom - dta.COSTO_PROM ) / prec_prom) * 100;
        var contribucion =  (dta.VAL_MES - ( dta[month_UND] * dta.COSTO_PROM) ); 
        var prom_contribucion = ( dta.VALOR_FACT_MES / contribucion ) *100

        var TotalUnits = isValue(dta[month_UND],0,true)  + isValue(dta[month_UND_B],0,true) ;

        thead += `<th class="center">`+item.mes+`</th>`;

        tBody +=  `<td><table class="table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="bg-blue text-light">UNITS. META</th>
                                <th class="bg-blue text-light">MONTO VALOR C$</th>
                                <th class="bg-blue text-light">UNITS FACT.</th>     
                                <th class="bg-blue text-light">UNITS BONIF.</th>   
                                <th class="bg-blue text-light">UNITS TOTAL.</th>                                 
                                <th class="bg-blue text-light">MONTO FACT. C$</th>
                                <th class="bg-blue text-light">% CUMP. UNITS</th>
                                
                        </thead>
                        <tbody>
                            <tr>
                                <td> <p class="text-right">`+numeral(dta.UND_MES).format('0,0')+`</p></td>
                                <td> <p class="text-right">`+numeral(dta.VAL_MES).format('0,0')+`</p></td>
                                <td> <p class="text-right">`+numeral(dta[month_UND]).format('0,0')+`</p></td>
                                <td> <p class="text-right">`+numeral(dta[month_UND_B]).format('0,0')+`</p></td>
                                <td> <p class="text-right">`+numeral(TotalUnits).format('0,0') +`</p></td>
                                <td> <p class="text-right">`+numeral(dta[month_VAL]).format('0,0')+`</p></td>
                                <td> <p class="text-right">`+numeral(Cumpli).format('0,0')+`</p></td>
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
$("#btnCalcular").click( function() {
    
    dataProyect([],  [
            { "title": "DETALLE"},   
            { "title": "ARTICULO"},
            {"title": "DESCRIPCION"},
            { "title": "UNITS. META"},
            { "title": "MONTO. META C$"},
            { "title": "UNITS. FACT."},
            { "title": "MONTO FACT. C$"},
        ],
        '#dtProyect89',[3,4,5,6,]);
    CalcIndicadores_89();
});

$('#txt_Search89').on( 'keyup', function () {
    var table = $('#dtProyect89').DataTable();
    table.search(this.value).draw();
});

function CalcIndicadores_89(){

    f1 = $("#f1").val();
    f2 = $("#f2").val();

    $("#spn_dtIni").html(moment(f1).format('DD/MMM/YY'))
    $("#spn_dtEnd").html(moment(f2).format('DD/MMM/YY'))
    
    $("#Id_Progress_Bar").empty().append(`<div>
                <div class="d-flex align-items-center">
                    <strong class="text-info">Calculando...</strong>
                    <div class="spinner-border ml-auto text-primary" role="status" aria-hidden="true"></div>
                </div>
            </div>`);

    


    

    $.getJSON("dtProyect?f1="+f1+"&f2="+f2+"&pr=1", function(dataset) {

        var c = 2 ; 
        var Header_Align = [];
        var tbl_header = [];


        

        tbl_header = [
            { "title": "DETALLE",      "data": "DETALLE" },   
            { "title": "ARTICULO",      "data": "ARTICULO" },
            {"title": "DESCRIPCION",    "data": "DESCRIPCION", "render": function(data, type, row, meta) { 
                return`<a href="#!" onclick="OpenModal_Pro89(`+ "'" +row.ARTICULO + "'" +` )" >`+ row.DESCRIPCION +`</a>`
            }},
            { "title": "UNITS. META",   "data": "PRESUPUESTO" , render: $.fn.dataTable.render.number(',', '.', 0, '')},
            { "title": "MONTO. META C$",     "data": "CS_VALOR", render: $.fn.dataTable.render.number(',', '.', 0, '') },
            { "title": "UNITS. FACT.",   "data": "CANTI_FACT_MES", render: $.fn.dataTable.render.number(',', '.', 0, '') },
            { "title": "MONTO FACT. C$",  "data": "VALOR_FACT_MES", render: $.fn.dataTable.render.number(',', '.', 0, '') },
        ];

        // $.each(dataset[0]['FECHA'], function(key, val) {
        //     tbl_header.push({ "title": val.mes, "data": val,"render": function(data, type, row, meta) {

        //         var month_UND = val.mes + '_UND'
        //         var month_VAL = val.mes + '_VAL'

        //         var Cumpli = (( row[month_VAL] / row.VAL_MES ) * 100) 

        //         table =  `<table>
        //                     <thead>
        //                         <tr>
        //                             <th class="bg-blue text-light">META</th>
        //                             <th class="bg-blue text-light">META VAL. C$</th>
        //                             <th class="bg-blue text-light">FACT.</th>                                    
        //                             <th class="bg-blue text-light">FACT. C$</th>
        //                             <th class="bg-blue text-light">%</th>
        //                     </thead>
        //                     <tbody>
        //                         <tr>
        //                             <td> <p class="text-right">`+numeral(row.UND_MES).format('0,0.00')+`</p></td>
        //                             <td> <p class="text-right">`+numeral(row.VAL_MES).format('0,0.00')+`</p></td>
        //                             <td> <p class="text-right">`+numeral(row[month_UND]).format('0,0.00')+`</p></td>
        //                             <td> <p class="text-right">`+numeral(row[month_VAL]).format('0,0.00')+`</p></td>
        //                             <td> <p class="text-right">`+numeral(Cumpli).format('0,0.00')+`</p></td>
        //                         </tr>
        //                     </tbody>
        //                 </table>`;
  
        //         return  table;
        //     } });
        //     Header_Align.push(c)
        //     c++;
        // });


        

        dataProyect(dataset, tbl_header,'#dtProyect89',[3,4,5,6,]);
    
    
    })
}


function OpenModal_Pro89(ARTICULO) {
    $("#idArti").val(ARTICULO);
    $("#mdl_char_product").modal();
    bluid_char(ARTICULO,1,1);
}

$( "#orderComportamiento89").change(function() {
    valor = $( this ).val()  
    var articulo = $("#idArti").val();
    
    bluid_char(articulo, 1, valor);
});


function dataProyect(datos, Header,Table,Align) {


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
            $("#Id_Progress_Bar").empty();
        
        },
    });

        
    ObjTable.columns().header().each(function (columnHeader) {
        $(columnHeader).addClass('bg-blue text-light');
    });

    $(Table + "_length").hide();
    $(Table + "_filter").hide();


}





</script>