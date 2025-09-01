@extends('layouts.main')
@section('title' , $NamePath)
@section('name_user' , 'Administrador')
@section('metodosjs')
    @include('pages.AnalisisIMS.JS_AnalisisIMS')   
    @include('pages.AnalisisIMS.CSS_AnalisisIMS')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row ">
        <div class="col-md-12">       
            <h4 class="h4 text-umk" id="" >ANALISIS IMS</h4>
        </div>
        
        <div class="col-sm-12 ">
            <div class="table-responsive">
                <table id="tbl_Ims" class="table table-striped table-bordered" width="100%">
                   
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
