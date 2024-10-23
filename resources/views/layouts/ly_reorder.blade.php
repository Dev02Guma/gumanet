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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">

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

.btn-outline-primary 
{
  color: #007bff !important;
  background-color: transparent !important;
  background-image: none !important;
  border-radius: 35px !important;
  border: 1px solid rgba(0, 123, 255, 0.75) !important;   
}

.btn-outline-success 
{
  color: #28a745 !important;
  background-color: transparent !important;
  background-image: none !important;
  border-radius: 35px !important;
  border: 1px solid rgba(40, 167, 69, 0.75) !important;   
}

.btn-outline-secondary 
{
  color: #868e96 !important;
  background-color: transparent !important;
  background-image: none !important;
  border-radius: 35px !important;
  border: 1px solid rgba(134, 142, 150, 0.75) !important;   
}


  .dt-layout-cell.dt-layout-start {    
    width: 900px !important;
  }
  
  .table.dataTable  {
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-size: 12px;
  }

  

</style>
<!-- Custom styles for this template -->
<link rel="stylesheet" href="{{ url('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ url('css/fuente.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('css/daterangepicker.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css">

<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/5.0.1/css/fixedColumns.dataTables.css">

<!--Import Google Icon Font-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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

<script src="{{ url('js/ext/feather.min.js') }}"></script>
<script src="{{ url('js/ext/Chart.min.js') }}"></script>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>>

<script src="{{ url('js/highcharts.js') }}"></script>
<script src="{{ url('js/bootstrap.js') }}"></script>
<script src="{{ url('js/ext/moment.js') }}"></script>
<script src="{{ url('js/ext/daterangepicker.js') }}"></script>

<script src="{{ url('js/js_general.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ url('js/Numeral.js') }}"></script>

<script src="{{ url('js/jquery.cookie.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/dataTables.fixedColumns.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.1/js/fixedColumns.dataTables.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>



@yield('metodosjs')
</body>
</html>
