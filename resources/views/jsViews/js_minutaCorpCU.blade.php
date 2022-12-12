<script>
$(document).ready(function() {
	$( '#content_max' ).ckeditor();
	CKEDITOR.config.height = 500;
	$("#item-nav-01").after(`<li class="breadcrumb-item active">Nueva Minuta Corporativa</li>`);
})

$("#guardarMinuta").click( function() {
	var data = CKEDITOR.instances.content_max.getData();
	var content_max2 = CKEDITOR.instances.content_max.document.getBody().getText();
	var titulo = $("#tituloMinuta").val();

	if (data=='') {
		mensaje('No ha escrito ningun contenido aun', 'error');
		return false;
	}

	if (titulo=='') {
		mensaje('Necesita agregar un titulo', 'error');
		return false;
	}

  Swal.fire({
      title: 'Guardar Minuta',
      text: "¿Desea guardar esta minuta?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, guardar',
      cancelButtonText: 'Seguir editando',
    }).then((result) => {
      if (result.value) {        
		$('<input />')
		.attr('type', 'hidden')
		.attr('name', "content_max2")
		.attr('value', content_max2)
		.appendTo('#fmrMinuta');

		$('#fmrMinuta').submit();
      }
    })
})

$("#cancelMinuta").click( function() {
  var base_url = window.location.origin + '/' + window.location.pathname.split ('/') [1] + '/';
  
  Swal.fire({
    title: 'Cancelar redaccion',
    text: "¿Desea cancelar la edicion?",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, cancelar',
    cancelButtonText: 'Seguir editando',
  }).then((result) => {
    if (result.value) {
    	location.href = base_url+"public/MinutasCorporativas";
    }
  })
})
</script>