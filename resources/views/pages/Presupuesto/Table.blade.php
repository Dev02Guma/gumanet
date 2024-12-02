@extends('layouts.main')
@section('content')
<div class="container-fluid">	
    <div class="row justify-content-end">
        <div class="col-sm-2">
            <div class="input-group">
                <select class="custom-select" id="InputMeses" name="InputMeses">
                <option value="0" selected>TODOS</option>
                <option value="1">ENERO</option>
                <option value="2">FEBRERO</option>
                <option value="3">MARZO</option>
                <option value="4">ABRIL</option>
                <option value="5">MAYO</option>
                <option value="6">JUNIO</option>
                <option value="7">JULIO</option>
                <option value="8">AGOSTO</option>
                <option value="9">SEPTIEMBRE</option>
                <option value="10">OCTUBRE</option>
                <option value="11">NOVIEMBRE</option>
                <option value="12">DICIEMBRE</option>
                </select>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group">
                <select class="custom-select" id="InputMeses" name="InputMeses">
                <option value="2023" selected>2023</option>                
                </select>
            </div>
        </div>
        <!--<div class="col-sm-1" >
        <a id="exp-to-excel-canales" href="#!" class="btn btn-light btn-block text-success"><i class="fas fa-file-excel"></i> Exportar</a>
        </div>-->   
  </div>

  <div class="card border-0 shadow-sm mt-5">
      <div class="card-body col-sm-12 p-0 mb-2">	
        <div class="p-0 px-car">
          <div class="table-responsive flex-between-center scrollbar border border-1 border-300 rounded-2">
          
            <table id="table_presupuesto" class="table table-bordered table-sm" width="100%">
              <thead>
                <tr class="bg-blue text-light">
                  <th ></th>
                  <th >EJECUTADO</th>
                  <th >%</th>
                  <th >PRESUPUESTO</th>
                  <th >%</th>
                  <th >DIFERENCIA ABSOLUTA</th>
                  <th >DIFERENCIA RELATIVA</th>
                </tr>               
              </thead>
              <tbody>
                <tr>
                    <td class="bg-blue text-light">VENTAS BRUTAS</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_BRUTAS']['EJECUTADO'])}}</td>
                    <td style="text-align:right">100.00%</td>
                    <td style="text-align:right">29,358,407</td>
                    <td style="text-align:right">100.00%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_BRUTAS']['EJECUTADO'] - 29358407)}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['VENTAS_BRUTAS']['EJECUTADO'] - 29358407)/29358407)*100,2)}}%)</td>
                </tr>
                <tr class="text-light" style="background-color: dodgerblue;">
                    <td>VENTAS PRIVADO</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_PRIVADO']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['VENTAS_PRIVADO']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_PRIVADO']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['VENTAS_PRIVADO']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_PRIVADO']['EJECUTADO'] - $presupuesto['VENTAS_PRIVADO']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['VENTAS_PRIVADO']['EJECUTADO'] - $presupuesto['VENTAS_PRIVADO']['PRESUPUESTO'])/$presupuesto['VENTAS_PRIVADO']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS PRIMARIOS UMK</td>
                    <td style="text-align:right">{{@number_format($presupuesto['PRIMARIOS UMK']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['PRIMARIOS UMK']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['PRIMARIOS UMK']['PRESUPUESTO']),2)}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['PRIMARIOS UMK']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['PRIMARIOS UMK']['EJECUTADO'] - $presupuesto['PRIMARIOS UMK']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['PRIMARIOS UMK']['EJECUTADO'] - $presupuesto['PRIMARIOS UMK']['PRESUPUESTO'])/$presupuesto['PRIMARIOS UMK']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS SECUNDARIOS UMK</td>
                    <td style="text-align:right">{{@number_format($presupuesto['SECUNDARIOS']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['SECUNDARIOS']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['SECUNDARIOS']['PRESUPUESTO']),2)}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['SECUNDARIOS']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['SECUNDARIOS']['EJECUTADO'] - $presupuesto['SECUNDARIOS']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['SECUNDARIOS']['EJECUTADO'] - $presupuesto['SECUNDARIOS']['PRESUPUESTO'])/$presupuesto['SECUNDARIOS']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS NUEVOS</td>
                    <td style="text-align:right">{{@number_format($presupuesto['NUEVOS']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['NUEVOS']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['NUEVOS']['PRESUPUESTO']),2)}}</td>
                    <td style="text-align:right">0%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['NUEVOS']['EJECUTADO'] - $presupuesto['NUEVOS']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">(0%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS LIQUIDACION</td>
                    <td style="text-align:right">0</td>
                    <td style="text-align:right">0%</td>
                    <td style="text-align:right">0</td>
                    <td style="text-align:right">0%</td>
                    <td style="text-align:right">0</td>
                    <td style="text-align:right">(0%)</td>
                </tr>
                <tr class="text-light" style="background-color: dodgerblue;">
                    <td>VENTAS PROYECTOS ESPECIALES</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_PROYECTOS']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['VENTAS_PROYECTOS']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_PROYECTOS']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['VENTAS_PROYECTOS']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_PROYECTOS']['EJECUTADO'] - $presupuesto['VENTAS_PROYECTOS']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['VENTAS_PROYECTOS']['EJECUTADO'] - $presupuesto['VENTAS_PROYECTOS']['PRESUPUESTO'])/$presupuesto['VENTAS_PROYECTOS']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS ONCO</td>
                    <td style="text-align:right">{{@number_format($presupuesto['ONCO']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['ONCO']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['ONCO']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['ONCO']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['ONCO']['EJECUTADO'] - $presupuesto['ONCO']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['ONCO']['EJECUTADO'] - $presupuesto['ONCO']['PRESUPUESTO'])/$presupuesto['ONCO']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS GUMAPHARMA</td>
                    <td style="text-align:right">{{@number_format($presupuesto['GPHARMA']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['GPHARMA']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['GPHARMA']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['GPHARMA']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['GPHARMA']['EJECUTADO'] - $presupuesto['GPHARMA']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['GPHARMA']['EJECUTADO'] - $presupuesto['GPHARMA']['PRESUPUESTO'])/$presupuesto['GPHARMA']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr class="text-light" style="background-color: dodgerblue;">
                    <td>VENTAS INSTITUCIONALES</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_INSTITUCIONES']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['VENTAS_INSTITUCIONES']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_INSTITUCIONES']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['VENTAS_INSTITUCIONES']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['VENTAS_INSTITUCIONES']['EJECUTADO'] - $presupuesto['VENTAS_INSTITUCIONES']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['VENTAS_INSTITUCIONES']['EJECUTADO'] - $presupuesto['VENTAS_INSTITUCIONES']['PRESUPUESTO'])/$presupuesto['VENTAS_INSTITUCIONES']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS CRUZ AZUL</td>
                    <td style="text-align:right">{{@number_format($presupuesto['CRUZ AZUL']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['CRUZ AZUL']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['CRUZ AZUL']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['CRUZ AZUL']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['CRUZ AZUL']['EJECUTADO'] - $presupuesto['CRUZ AZUL']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['CRUZ AZUL']['EJECUTADO'] - $presupuesto['CRUZ AZUL']['PRESUPUESTO'])/$presupuesto['CRUZ AZUL']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                <tr>
                    <td class="bg-blue text-light">VENTAS LICITACIÓN</td>
                    <td style="text-align:right">{{@number_format($presupuesto['LICITACIONES']['EJECUTADO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['LICITACIONES']['EJECUTADO']/$presupuesto['VENTAS_BRUTAS']['EJECUTADO'])*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['LICITACIONES']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">{{@number_format(($presupuesto['LICITACIONES']['PRESUPUESTO']/29358407)*100,2)}}%</td>
                    <td style="text-align:right">{{@number_format($presupuesto['LICITACIONES']['EJECUTADO'] - $presupuesto['LICITACIONES']['PRESUPUESTO'])}}</td>
                    <td style="text-align:right">({{@number_format((($presupuesto['LICITACIONES']['EJECUTADO'] - $presupuesto['LICITACIONES']['PRESUPUESTO'])/$presupuesto['LICITACIONES']['PRESUPUESTO'])*100,2)}}%)</td>
                </tr>
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection('content')