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
    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

  .tbl_rows_recibo_color{
      background-color: #F7CFCF !important;
  }
  .tbl_rows_recibo_ingress{
      background-color: #c8f5bf !important;
  }

  .tbl_rows_done{
      color: #c7c7c7
  }

  

</style>
<link rel="stylesheet" href="{{ url('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ url('css/fuente.css') }}">
<link rel="stylesheet" href="{{ url('css/Tooltip.css') }}">
<!--Import Google Icon Font-->
<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css" rel="stylesheet"/>
  <link href="https://cdn.datatables.net/fixedcolumns/3.2.2/css/fixedColumns.dataTables.min.css" rel="stylesheet"/>
  
</head>
<body>
@include('layouts.menu')
<div class="page-content" id="content">
  <nav class="navbar navbar-expand-lg  " style="background-color: #e9ecef; height: 50px;">
      <ol class="breadcrumb" style=" margin-top: 16px !important;">
        <li>
          <a href="#!" id="sidebarCollapse">
            <i class="material-icons"  class="material-icons" >menu</i>
          </a>
        </li>
        @if(Auth::User()->activeRole()!=3 && Auth::User()->activeRole()!=4 && Auth::User()->activeRole()!=5)
          <li class="breadcrumb-item" id="item-nav-01"><a href="{{url('/Dashboard')}}">Dashboard</a></li>
        @endif              
      </ol>
      <div class="collapse navbar-collapse " >
        <ul class="navbar-nav ml-auto nav-flex-icons ">
            <li class="nav-item">
              <a href="#!" class="nav-link">
                <span class="badge badge-danger " id="id-count-im">0</span>
                <i class="material-icons text-info"  style="font-size: 20px">notifications</i>
              </a>
            </li>
            <li class="ml-auto mt-2"><a href="#!"><i class="active-menu material-icons text-info" style="font-size: 20px">settings</i></a></li>
          </ul>
      </div>
    </nav>
    <br>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">        
        @yield('content')
        <div id="sidebar" class="border-left shadow-sm p-3">
            <p class="font-weight-bold ml-2">Configuración<button type="button" class="active-menu close" aria-label="Close"><span aria-hidden="true">&times;</span></button></p>
            <ul class="list-group list-group-flush">
              <li><a href="{{ route('formReset') }}"><i class="align-middle material-icons">https</i> Cambiar contraseña</a></li>
              <li><a href="{{ route('logout') }}"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="align-middle material-icons" >exit_to_app</i> Cerrar sesion</a></li>
            </ul><hr>
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

<script src="{{ asset('js/jquery-2.1.1.min.js') }}"></script>    
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>  
<script src="https://cdn.datatables.net/fixedcolumns/3.2.2/js/dataTables.fixedColumns.min.js"></script>
<script src="{{ url('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="{{ url('js/ext/moment.js') }}"></script>
<script src="{{ url('js/Numeral.js') }}"></script>
<script src="{{ url('js/all.min.js') }}"></script>
<script src="{{ url('js/ext/feather.min.js') }}"></script>
<script src="{{ url('js/js_general.js') }}"></script>
<script src="{{ url('js/jquery.cookie.js') }}"></script>





@yield('metodosjs')
</body>
</html>
