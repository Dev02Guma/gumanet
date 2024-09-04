@extends('layouts.ly_reorder')
@section('metodosjs')
@include('jsViews.js_contribuciones')
@endsection
@section('content')
<style>
  span.btn-change-color {
    background-color: #28a745;
  }
</style>
<div class="container-fluid"> 
  <p class="font-italic text-muted pt-0 mt-0">Actualizado del <span id="tl_periodo"> - </span></p>	
  <div class="row">
    <div class="col">		      
      <div class="input-group"> 
        <input type="text" id="id_txt_buscar" class="form-control" aria-describedby="basic-addon1" placeholder="Buscar...">
          <div class="input-group-prepend">
            <span class="btn-change-color text-white input-group-text" id="BtnClick"><i data-feather="refresh-cw"></i></span>
          </div>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="input-group">
        <select class="custom-select" id="InputCanales" name="InputCanales">
          <option value="5" selected>5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="100">100</option>
          <option value="-1">Todo</option>
        </select>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="row ">
        <div class="col mt-1">
          <div class="form-group">  
            <input type="text" class="input-fecha" id="f1">
          </div>
        </div>
        <div class="col mt-1 ">
          <div class="form-group">  
            <input type="text" class="input-fecha" id="f2">
          </div>
        </div>
        
      </div>
    </div>
    <!--<div class="col-sm-1" >
      <a id="exp-to-excel-canales" href="#!" class="btn btn-light btn-block text-success"><i class="fas fa-file-excel"></i> Exportar</a>
    </div>-->   
      
  </div>

    <div class="card border-0 shadow-sm ">
      <div class="card-body col-sm-12 p-0 mb-2">	
        <div class="p-0 px-car">
          <div class="table-responsive flex-between-center scrollbar border border-1 border-300 rounded-2">
          
            <table id="table_contribucion" class="table nowrap table-bordered" width="100%">
              <thead>
                <tr class="bg-blue text-light">
                  <th class="bg-blue text-light" colspan="3">SKU</th>
                  <th colspan="3">INFO</th>
                  <th colspan="6">FARMACIA</th>
                  <th colspan="6">CADENA FARMACIA</th>
                  <th colspan="6">MAYORISTA</th>
                  <th colspan="6">INSTITUCION PRIVADA</th>
                  <th colspan="6">CRUZ AZUL</th>
                  <th colspan="6">INSTITUCION PUBLICA</th>
                  <th colspan="6">TOTAL</th>
                </tr>
                <tr>
                  <th class="bg-blue" colspan="3"></th>
                  <th id="Costo_Prom_Priv"></th>
                  <th id="Costo_Prom_Minsa"></th>
                  <th id="Valor_USD_Inventario_ONHAND_PRIVADO"></th>
                  <th id="Farmacia_Cantidad"></th>
                  <th id="Farmacia_Promedio"></th>
                  <th id="Farmacia_Venta"></th>
                  <th id="Farmacia_Costo"></th>
                  <th id="Farmacia_Contribucion"></th>
                  <th id="Farmacia_Margen"></th>
                  <th id="Cadena_Farmacia_Cantidad"></th>
                  <th id="Cadena_Farmacia_Promedio"></th>
                  <th id="Cadena_Farmacia_Venta"></th>
                  <th id="Cadena_Farmacia_Costo"></th>
                  <th id="Cadena_Farmacia_Contribucion"></th>
                  <th id="Cadena_Farmacia_Margen"></th>
                  <th id="Mayorista_Cantidad"></th>
                  <th id="Mayorista_Promedio"></th>
                  <th id="Mayorista_Venta"></th>
                  <th id="Mayorista_Costo"></th>
                  <th id="Mayorista_Contribucion"></th>
                  <th id="Mayorista_Margen"></th>
                  <th id="Institucion_Privada_Cantidad"></th>
                  <th id="Institucion_Privada_Promedio"></th>
                  <th id="Institucion_Privada_Venta"></th>
                  <th id="Institucion_Privada_Costo"></th>
                  <th id="Institucion_Privada_Contribucion"></th>
                  <th id="Institucion_Privada_Margen"></th>
                  <th id="Cruz_Azul_Cantidad"></th>
                  <th id="Cruz_Azul_Promedio"></th>
                  <th id="Cruz_Azul_Venta"></th>
                  <th id="Cruz_Azul_Costo"></th>
                  <th id="Cruz_Azul_Contribucion"></th>
                  <th id="Cruz_Azul_Margen"></th>
                  <th id="Institucion_Publica_Cantidad"></th>
                  <th id="Institucion_Publica_Promedio"></th>
                  <th id="Institucion_Publica_Venta"></th>
                  <th id="Institucion_Publica_Costo"></th>
                  <th id="Institucion_Publica_Contribucion"></th>
                  <th id="Institucion_Publica_Margen"></th>
                  <th id="Total_Cantidad"></th>
                  <th id="Total_Promedio"></th>
                  <th id="Total_Venta"></th>
                  <th id="Total_Costo"></th>
                  <th id="Total_Contribucion"></th>
                  <th id="Total_Margen"></th>
                </tr>
                <tr>
                    <th class="bg-blue text-light">ARTICULO</th>
                    <th class="bg-blue text-light">DESCRIPCION</th>
                    <th class="bg-blue text-light">FABRICANTE</th>
                    <th style="background-color:lightsalmon">COSTO PROM. PRIV. (PACK)</th>
                    <th style="background-color:lightsalmon">COSTO PROM. MINSA (PACK)</th>
                    <th style="background-color:bisque">Valor (USD) Inventario ONHAND-PRIVADO</th>
                    <th class="bg-warning text-black">CANTIDAD</th>
                    <th class="bg-warning text-black">PROMEDIO C$</th>
                    <th class="bg-warning text-black">VENTA C$</th>
                    <th class="bg-warning text-black">COSTO C$</th>
                    <th class="bg-warning text-black">CONTRIBUCION C$</th>
                    <th class="bg-warning text-black">MARGEN %</th>
                    <th style="background-color:peru">CANTIDAD</th>
                    <th style="background-color:peru">PROMEDIO C$</th>
                    <th style="background-color:peru">VENTA C$</th>
                    <th style="background-color:peru">COSTO C$</th>
                    <th style="background-color:peru">CONTRIBUCION C$</th>
                    <th style="background-color:peru">MARGEN %</th>
                    <th style="background-color:burlywood">CANTIDAD</th>
                    <th style="background-color:burlywood">PROMEDIO C$</th>
                    <th style="background-color:burlywood">VENTA C$</th>
                    <th style="background-color:burlywood">COSTO C$</th>
                    <th style="background-color:burlywood">CONTRIBUCION C$</th>
                    <th style="background-color:burlywood">MARGEN %</th>
                    <th style="background-color:limegreen">CANTIDAD</th>
                    <th style="background-color:limegreen">PROMEDIO C$</th>
                    <th style="background-color:limegreen">VENTA C$</th>
                    <th style="background-color:limegreen">COSTO C$</th>
                    <th style="background-color:limegreen">CONTRIBUCION C$</th>
                    <th style="background-color:limegreen">MARGEN %</th>
                    <th style="background-color:cornflowerblue">CANTIDAD</th>
                    <th style="background-color:cornflowerblue">PROMEDIO C$</th>
                    <th style="background-color:cornflowerblue">VENTA C$</th>
                    <th style="background-color:cornflowerblue">COSTO C$</th>
                    <th style="background-color:cornflowerblue">CONTRIBUCION C$</th>
                    <th style="background-color:cornflowerblue">MARGEN %</th>
                    <th style="background-color:limegreen">CANTIDAD</th>
                    <th style="background-color:limegreen">PROMEDIO C$</th>
                    <th style="background-color:limegreen">VENTA C$</th>
                    <th style="background-color:limegreen">COSTO C$</th>
                    <th style="background-color:limegreen">CONTRIBUCION C$</th>
                    <th style="background-color:limegreen">MARGEN %</th>
                    <th style="background-color:gold">CANTIDAD</th>
                    <th style="background-color:gold">PROMEDIO C$</th>
                    <th style="background-color:gold">VENTA C$</th>
                    <th style="background-color:gold">COSTO C$</th>
                    <th style="background-color:gold">CONTRIBUCION C$</th>
                    <th style="background-color:gold">MARGEN %</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

  





    
              

</div>


@endsection('content')