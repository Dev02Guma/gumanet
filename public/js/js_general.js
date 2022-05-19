var pgurl = window.location.href.substr(window.location.href.lastIndexOf("/") + 1).replace('#!', '',);

$("ol li a").each(function () {
    const ruta = $(this).attr("href").substr(window.location.href.lastIndexOf("/") + 1);
    if (ruta == pgurl || $(this).attr("href") == '') {
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
    const toast = swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    toast({
        type: tipo,
        title: mensaje
    })
}

$("body").click(function (e) {
    if ($("#sidebar").hasClass('active') || $(e.target).hasClass('active-menu')) {
        $("#sidebar").toggleClass('active');
    }
});


//RECUPERA LOS ENTRADAS DE IM 
$.ajax({
    url: "countim",
    type: "GET",
    async: true,
    success: function (count) {

        $('#id-count-im').empty().text(numeral(count).format('0'));

        if (count == 0) {
            $('#id-count-im').hide();
        } else {
            $('#id-count-im').empty().text(numeral(count).format('0'));
        }

    }
});


// Sidebar toggle behavior
$('#sidebarCollapse').on('click', function () {
    $.removeCookie('navbar');
    if ($("#sidebar-menu-left").hasClass('active')) {
        $.cookie('navbar', true)

    } else {
        $.cookie('navbar', false)

    }
    $('#sidebar-menu-left, #content').toggleClass('active');


});

function fullScreen() {
    //SI ESTA EN UN TELEFONO
    if (($('header').width() <= 420)) {
        $('#sidebar-menu-left, #content')
            .addClass('active')
            .removeClass('notactive');
    }

    if ($.cookie('navbar') == 'true') {
        $('#sidebar-menu-left, #content')
            .addClass('notactive')
            .removeClass('active');
    } else if ($.cookie('navbar') == 'false') {
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
var down = false;

exist_notify();
$('#bell').on('click', function (e) {
    //$('.toast').toast('show');

    $('#list-notify').empty();
    getCommentIM();
    getNotificacionesExport();
    if ($('#contain-notify').is(":visible")) {
        $('#contain-notify').hide();
        exist_notify();
    } else {
        $('#contain-notify').show();
        changeState();
    }
});

//Nueva notificacion

function exist_notify() {
    $.ajax({
        url: "exist_notify",
        type: "GET",
        async: true,
        success: function (response) {
            if (response > 0) {
                $('#noti_exist').addClass("circulo");
            } else {
                $('#noti_exist').removeClass("circulo");
            }
        }
    });

}
exist_registry();
function exist_registry() {
    $.ajax({
        url: "exist_registry",
        type: "GET",
        async: true,
        success: function (response) {
            console.log(response);
            if (response <= 0) {
                const scriptHTML = `<div class="overflow-auto m-0 p-0">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item ">
                                                <div class="row mx-2 justify-content-center">
                                                    <p>No hay notificaciones</p>
                                                </div>
                                            </li>
                                         </ul>
                                   </div>`
                    ;
                $("#No_exist").html(scriptHTML);
            }
        }
    });
}

//Obtener norificaciones
function getCommentIM() {
    $.ajax({
        url: "getCommentIM",
        type: "GET",
        async: true,
        dataType: "json",
        success: function (data) {
            var i = 0;
            var scriptHTML = '';
            if (Object.keys(data).length === 0) {
                console.log("No existen datos en las notificaciones de IM");
                return false;
            } else {
                data.forEach(element => {
                    if (element.Read == 0) {
                        scriptHTML += `<li class="list-group-item notification-list--unread" style="border-left: 3px solid #007bff !important ;">
                                <a href="http://localhost/gumanet-1/public/InteligenciaMercado">
                                    <div class="row">
                                          <div class="col-2 ">
                                            <img src="img/img_avatar.png" alt="" class="img-fluid" style="border-radius: 50%;">
                                          </div>
                                          <div class="col-7 m-0 p-0">
                                            <div class="body m-0 p-0">
                                              <div class="container-fluid m-0 p-0">
                                                <h6 class="text-dark">`+ element.Nombre + `</h6>
                                                <p class="text-secondary m-0 p-0">` + element.Titulo + `</p>
                                                <p class="border-left pl-2  border-primary text-dark" style="border-left: 3px solid #007bff !important ;">`+ element.Contenido + `</p>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-3 m-0 p-0">
                                            <span class="text-dark">`+ element.Fecha + `</span>
                                          </div>
                                    </div>
                                </a>
                            </li>`;
                    } else {
                        if (i <= 4) {
                            scriptHTML += `<li class="list-group-item ">
                                 <a href="http://localhost/gumanet-1/public/InteligenciaMercado">
                                     <div class="row">
                                           <div class="col-2 ">
                                             <img src="img/img_avatar.png" alt="" class="img-fluid" style="border-radius: 50%;">
                                           </div>
                                           <div class="col-7 m-0 p-0">
                                             <div class="body m-0 p-0">
                                               <div class="container-fluid m-0 p-0">
                                                 <h6 class="text-dark">`+ element.Nombre + `</h6>
                                                 <p class="text-secondary m-0 p-0">` + element.Titulo + `</p>
                                                 <p class="border-left pl-2  border-primary text-dark" style="border-left: 3px solid #007bff !important ;">`+ element.Contenido + `</p>
                                               </div>
                                             </div>
                                           </div>
                                           <div class="col-3 m-0 p-0">
                                             <span class="text-dark">`+ element.Fecha + `</span>
                                           </div>
                                     </div>
                                 </a>
                             </li>`;
                        }
                        i++;
                    }
                });
                $("#list-notify").append(scriptHTML);
            }
        }
    });
}

function getNotificacionesExport() {
    $.ajax({
        url: "Allnotificaciones",
        type: "GET",
        async: true,
        dataType: "json",
        success: function (data) {
            var i = 0;
            var scriptHTML = '';
            //Object.keys(data).length === 0? console.log('el json viene vacio') : console.log('Existen datos');
            if (Object.keys(data).length === 0) {
                console.log("No existen datos en las notificaciones de expo");
                return false;
            } else {

                data.forEach(element => {
                    if (element.leido == 0) {
                        scriptHTML += `<li class="list-group-item notification-list--unread" style="border-left: 3px solid #007bff !important ;">
                        <div class="row">
                          <div class="col-2">
                            <img src="img/img_avatar.png" alt="" class="img-fluid " style="border-radius: 50%;">
                          </div>
                          <div class="col-7 m-0 p-0">
                            <div class="body m-0 p-0">
                              <div class="container-fluid m-0 p-0">
                                <h6>`+ element.nombre + `</h6>
                                <p class="text-secondary m-0 p-0 mb-1">` + element.title + `</p>
                                <p class="border-left pl-2  border-primary" style="border-left: 3px solid #007bff !important ;">` + element.message + `</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-3 m-0 p-0">
                            <span>` + element.created_at + `</span>
                          </div>
                        </div>
                      </li>`;
                    } else {
                        if (i <= 4) {
                            scriptHTML += `<li class="list-group-item">
                            <div class="row">
                              <div class="col-2">
                                <img src="img/img_avatar.png" alt="" class="img-fluid " style="border-radius: 50%;">
                              </div>
                              <div class="col-7 m-0 p-0">
                                <div class="body m-0 p-0">
                                  <div class="container-fluid m-0 p-0">
                                    <h6>`+ element.nombre + `</h6>
                                    <p class="text-secondary m-0 p-0 mb-1">` + element.title + `</p>
                                    <p class="border-left pl-2  border-primary" style="border-left: 3px solid #007bff !important ;">` + element.message + `</p>
                                  </div>
                                </div>
                              </div>
                              <div class="col-3 m-0 p-0">
                                <span>` + element.created_at + `</span>
                              </div>
                            </div>
                          </li>`
                        }
                        i++;
                    }
                });
                $("#list-notify").append(scriptHTML);
            }
        }
    });
}
//Cambiar estado
function changeState() {
    $.ajax({
        url: "updateState",
        type: "POST",
        dataType: "json",
        data: {},
        async: true,
        success: function (response) {
            console.log(response);
            console.log('Estado cambiado');
        }
    });
}