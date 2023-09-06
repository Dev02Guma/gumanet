<!-- Vertical navbar -->
<div class="vertical-nav bg-white border-right" id="sidebar-menu-left">
  <div class="container-fluid">
    <div class="row mt-3">
      <div class="col-sm-12 text-center">        
        <img class="rounded mb-1" src="{{ url('images/p20.png') }}" width="85%">        
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-sm-12">
        <p class="font-weight-normal mt-0 mb-0 text-left ml-3"><strong class="text-muted">Usuario: </strong>{{ Auth::User()->email }}</p>
        <p id="companny_id" class="font-weight-normal mt-0 mb-0 text-left ml-3" hidden="true">{{ Session::get('company_id') }}</p>
        <p class="font-weight-normal text-left ml-3"><strong class="text-muted">Unidad: </strong>{{ Session::get('companyName')}}</p>
      </div>
    </div>
    <hr style="padding:0; margin:0;"></hr>
    <div class="row mt-4">
      <div class="col-sm-12">
        <ul class="nav flex-column">
       
          @foreach ($menus as $menu)
            <li class="nav-item">
              <a class="nav-link text-secondary" href="{{url($menu['url'])}}">
                <span data-feather="{{$menu['icono']}}" class="mr-3"></span>
                {{$menu['nombre']}}
              </a>
            </li>
          @endforeach
          <li class="nav-item">
          <a class="nav-link text-danger font-weight-bold" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span data-feather="log-out" class="mr-3"></span> Cerrar sesion
          </a>
          </li>
        </ul>
      </div>
    </div>
    </div>
  </div>
<div id="_version">
  @include('layouts.app_version', array( 'appVersion'=>Auth::User()->gitVersion() ))
</div>