<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta charset="utf-8">
<link rel="shortcut icon" href="{{ url('images/gumanet-icon.png') }}" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','GUM@NET')</title>
<!-- Bootstrap core CSS -->
<link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
<!-- Mi CSS -->
<link rel="stylesheet" href="{{ url('css/style.css') }}">
<style>
  .bd-placeholder-img {
    font-size: 1.125rem;
    text-anchor: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  a {
    text-decoration: none !important;
    /*color: #000;*/
  }

  @media (min-width: 768px) {
    .bd-placeholder-img-lg {
      font-size: 3.5rem;
    }
  }

  .tbl_rows_recibo_color {
    background-color: #F7CFCF !important;
  }

  .circulo {
    height: 10px !important;
    width: 10px !important;
    background: #dc3545 !important;
    -moz-border-radius: 50px !important;
    -webkit-border-radius: 50px !important;
    border-radius: 50% !important;
  }

  .notification-list--unread {
    position: relative;
    background: #f4f8fa;
  }
</style>
<!-- Custom styles for this template -->
<link rel="stylesheet" href="{{ url('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ url('css/fuente.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('css/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap-select.css') }}">
<!--Import Google Icon Font-->
<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
  @include('layouts.menu')
  <div class="page-content" id="content">
    <nav class="navbar navbar-expand-lg  " style="background-color: #e9ecef; height: 50px;">
      <ol class="breadcrumb" style=" margin-top: 16px !important;">
        <li>
          <a href="#!" id="sidebarCollapse">
            <i class="material-icons" class="material-icons">menu</i>
          </a>
        </li>
        @if(Auth::User()->activeRole()!=3 && Auth::User()->activeRole()!=4 && Auth::User()->activeRole()!=5)
        <li class="breadcrumb-item" id="item-nav-01"><a href="{{url('/Dashboard')}}">Dashboard</a></li>
        @endif
      </ol>
      <div class="collapse navbar-collapse ">
        <ul class="navbar-nav ml-auto nav-flex-icons ">
          <li class="nav-item">
            <a href="#" class="nav-link" id="bell">
              <div class="row no-gutters d-flex text-center">
                <!--<span class="badge badge-danger " id="id-count-im">0</span> </div> -->
                <div class="col-12 d-flex justify-content-end">
                  <div id="noti_exist"></div>
                </div>
                <div class="col-12 text-center">
                  <i class="material-icons text-info" style="font-size: 20px" id="icon-bell">notifications</i>
                </div>
              </div>
            </a>
          </li>
          <li class="ml-auto mt-2"><a href="#!"><i class="active-menu material-icons text-info" style="font-size: 20px">settings</i></a></li>
        </ul>
      </div>
    </nav>

    <div class="container-fluid ml-auto " style="position: relative; z-index: 1 ; display:none;" id="contain-notify">
      <div class="card" style="position: absolute; top: 0; right: 25px; max-height: 500px; max-width:30%; min-width:30%" data-autohide="false" id="card-notify">
        <div class=" card-header">
          <div class="d-flex">
            <div class=" justify-content-start mr-auto">
              <strong>Notificaciones</strong>
            </div>
            <div class="justify-content-end ml-auto">
              <h6 class="text-secondary"></h6>
            </div>
          </div>
        </div>
        <div id="No_exist"></div>
        <div class="overflow-auto m-0 p-0">
          <ul class="list-group list-group-flush" id="list-notify">
          </ul>
        </div>
      </div>
    </div>
    <br>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          @yield('content')
          <div id="sidebar" class="border-left shadow-sm p-3">
            <p class="font-weight-bold ml-2">Configuración<button type="button" class="active-menu close" aria-label="Close"><span aria-hidden="true">&times;</span></button></p>
            <ul class="list-group list-group-flush">
              <li><a href="{{ route('formReset') }}"><i class="align-middle material-icons">https</i> Cambiar contraseña</a></li>
              <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="align-middle material-icons">exit_to_app</i> Cerrar sesion</a></li>
            </ul>
            <hr>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
            <!--OPCIONES PARA DASHBOARDS-->
            <div id="content-dash"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Cambiar contraseña</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button onclick="" type="submit" class="btn btn-primary" form="formCambiarPass">{{ __('Cambiar') }}</button>

        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('js/ext/feather.min.js') }}"></script>
  <script src="{{ url('js/ext/Chart.min.js') }}"></script>
  <script src="{{ url('js/jquery-2.1.1.min.js') }}"></script>
  <script src="{{ url('js/jquery.maskMoney.js') }}"></script>
  <script src="{{ url('js/jquery.mask.js') }}"></script>
  <script src="{{ url('js/highcharts.js') }}"></script>
  <script src="{{ url('js/highcharts-3d.js') }}"></script>
  <script src="{{ url('js/all.min.js') }}"></script>
  <script src="{{ url('js/popper.min.js') }}"></script>
  <script src="{{ url('js/bootstrap.min.js') }}"></script>
  <script src="{{ url('js/bootstrap-select.js') }}"></script>
  <script src="{{ url('js/ext/moment.js') }}"></script>
  <script src="{{ url('js/ext/daterangepicker.js') }}"></script>
  <script src="{{ url('js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ url('js/js_general.js') }}"></script>
  <script src="{{ url('js/sweetalert2.all.js') }}"></script>
  <script src="{{ url('js/jquery.cookie.js') }}"></script>
  <script src="{{ url('js/Numeral.js') }}"></script>
  <script src="{{ url('js/xlsx.full.min.js') }}"></script>
  <script src="{{ url('js/jszip.js') }}"></script>
  <script src="{{ url('js/no-data-to-display.js') }}"></script>
  <script src="{{ url('js/jquery.daterangepicker.min.js') }}"></script>
  <script src="{{ url('js/dataTables.rowGroup.min.js') }}"></script>
  <script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
  <script src="{{ url('js/ckeditor/adapters/jquery.js') }}"></script>
  <script src="{{ url('js/grouped-categories.js') }}"></script>
  @yield('metodosjs')
</body>

</html>