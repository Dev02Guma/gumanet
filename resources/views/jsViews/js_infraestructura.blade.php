<script>
$(document).ready(function() {


    $('#TblProjects').DataTable({
    	"ajax":{
    		"url": "getProyects",
    		'dataSrc': '',
    	},
    	"info":    false,
    	"lengthMenu": [[10,30,50,100,-1], [20,30,50,100,"Todo"]],
    	"language": {
    	    "zeroRecords": "No hay coincidencias",
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
			{ "title": "",      				"data": "DETALLE" },
    	    { "title": "ID Del Proyecto",      	"data": "reference" },
			{ "title": "NOMBRE",				"data": "name"}, 
			{ "title": "CLIENTE",   			"data": "company_id" },
    	    { "title": "FECHA LIMITE",  		"data": "end" },
    	    { "title": "AVANCE",        		"data": "progress" },
			{ "title": "TAREAS ABIERTAS",       "data": "tasks" },
    	],
        "columnDefs": [
            {"className": "dt-center", "targets": [ 0,1, 2, 3,4,5,6 ]},
            {"className": "dt-right", "targets": [ 4 ]},
            { "width": "50%", "targets": [ 2 ] }
        ],
    });

    $("#TblProjects_length").hide();
    $("#TblProjects_filter").hide();


	$('#txtBuscarProyecto').on( 'keyup', function () {
	    var table = $('#TblProjects').DataTable();
	    table.search(this.value).draw();
	});

	$( "#txtSelectCliente").change(function() {
	    var table = $('#TblProjects').DataTable();
	    table.search(this.value).draw();
	});
});

$(document).on('click', '#exp_more', function(ef) {
	var table = $('#TblProjects').DataTable();
	var tr = $(this).closest('tr');
	var row = table.row(tr);
	var data = table.row($(this).parents('td')).data();


	
	if (row.child.isShown()) {
		row.child.hide();
		tr.removeClass('shown');
		ef.target.innerHTML = "expand_more";
		ef.target.style.background = '#e2e2e2';
		ef.target.style.color = '#007bff';
	} else {
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

		//format(row.child,data.UNIDAD,data.ARTICULO);
		format(row.child,data.id);

		tr.addClass('shown');
		
		ef.target.innerHTML = "expand_less";
		ef.target.style.background = '#ff5252';
		ef.target.style.color = '#e2e2e2';
	}
});

function format ( callback, id) {
	var thead = tbody = '';            
        thead =`<table class="" width='100%'>
                    <tr>
                        <th class="center">TAREA</th>
                        <th class="center">ESTADO</th>
                        <th class="center">PRIORIDAD</th>
                        <th class="center">FECHA DE CREACION</th>
                        <th class="center">FECHA VENCIMIENTO</th>
                    </tr>
                <tbody>`;
	$.ajax({
		type: "POST",
		url: "getTasksProjects",
		data:{
			id: id      
		},        
        success: function ( data ) {

			
            if (data.length==0) {
                tbody +=`<tr>
                            <td colspan='6'><center>Bodega sin existencia</center></td>
                        </tr>`;
                callback(thead + tbody).show();
            }
            $.each(data.original, function (i, item) {

				var clssAnulado = (item['status']=='Hecho') ? "tbl_rows_done" : "";

				tbody +=`<tr class="`+clssAnulado+`">
                            <td>` + item['name'] + `</td>
                            <td>` + item['status'] + `</td>
                            <td>` + item['priority'] + `</td>
                            <td>` + item['start_date'] + `</td>
                            <td>` + item['dute_date'] + `</td>
                        </tr>`;
            });
            tbody += `</tbody></table>`;
            callback(thead + tbody).show();
            }
        });
}

</script>