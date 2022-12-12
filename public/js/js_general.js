var pgurl = window.location.href.substr(window.location.href.lastIndexOf("/")+1).replace('#!', '', );

$("ol li a").each(function() {
    const ruta = $(this).attr("href").substr(window.location.href.lastIndexOf("/")+1);
    if( ruta == pgurl || $(this).attr("href") == '' ){
        $(this).removeClass('text-secondary').addClass("text-primary font-weight-bold");
    }
});

//METODO QUE PERMITE ENVIAR POR POST AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function mensaje(mensaje, tipo) {
    /*
    Tipos:
    success, error, warning, info, question
    +*/
    const toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    toast.fire({
      type: tipo,
      title: mensaje
    })
}

$("body").click( function(e) {
    if ( $("#sidebar").hasClass('active') || $(e.target).hasClass('active-menu') ) {
        $("#sidebar").toggleClass('active');
    }    
});


//RECUPERA LOS ENTRADAS DE IM 
$.ajax({
    url: "countim",
    type: "GET",
    async: true,
    success: function(count) {

        $('#id-count-im').empty().text(numeral(count).format('0'));

        if (count==0) {
            $('#id-count-im').hide();
        } else {
            $('#id-count-im').empty().text(numeral(count).format('0'));
        }

    }
});

// Sidebar toggle behavior
$('#sidebarCollapse').on('click', function() {
    $.removeCookie('navbar');
    if ( $("#sidebar-menu-left").hasClass('active') ) {        
        $.cookie( 'navbar' , true)
        
    }else {
        $.cookie( 'navbar' , false)
        
    }
    $('#sidebar-menu-left, #content').toggleClass('active');    

    
});

function fullScreen() {
    //SI ESTA EN UN TELEFONO
    if (($('header').width() <= 420 )) {
        $('#sidebar-menu-left, #content')
        .addClass('active')
        .removeClass('notactive');
    }

    if ( $.cookie('navbar')=='true' ) {
        $('#sidebar-menu-left, #content')
        .addClass('notactive')
        .removeClass('active');
    }else if( $.cookie('navbar')=='false'  ) {
        $('#sidebar-menu-left, #content')
        .addClass('active')
        .removeClass('notactive');
    }
}

feather.replace();

function inicializaControlFecha() {
    $('input[class="input-fecha"]').daterangepicker({
        "locale": {
            "format": "YYYY-MM-DD",
            "separator": " - ",
            "applyLabel": "Apply",
            "cancelLabel": "Cancel",
            "fromLabel": "From",
            "toLabel": "To",
            "customRangeLabel": "Custom",
            "daysOfWeek": [
                "D",
                "L",
                "M",
                "M",
                "J",
                "V",
                "S"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 0
        },
        singleDatePicker: true,
        showDropdowns: true
    });
}