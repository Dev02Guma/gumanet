<?php

namespace App\Http\Controllers;

use App\DetalleOrden_model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models;
use App\Company;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;



class DetalleOrdenController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        setlocale(LC_ALL, 'es_ES');
    }

    function index()
    {
        return view('pages.detalleOrden');
    }

    public function getMateriaPrima($numOrden)
    {
        $data = array();
        $i = 0;
        //  $query2 = DB::table('producciontest.orden_produccion')->where('numOrden', $key->numOrden)->get();

        $mp_directa =  DB::table('producciontest.mp_directa')->select('producciontest.mp_directa.*', 'producciontest.fibras.descripcion', 'producciontest.maquinas.nombre')
            ->join('producciontest.fibras', 'producciontest.mp_directa.idFibra', '=', 'producciontest.fibras.idFibra')
            ->join('producciontest.maquinas', 'producciontest.mp_directa.idMaquina', '=', 'producciontest.maquinas.idMaquina')
            ->where('producciontest.mp_directa.numOrden', $numOrden)
            ->get();

        foreach ($mp_directa as $key => $mp) {
            $data[$i]['fibra'] = $mp->descripcion;
            $data[$i]['maquina'] = $mp->nombre;
            $data[$i]['cantidad'] = number_format($mp->cantidad, 2) . " kg";
            $i++;
        }

        return response()->json($data);
    }

    public function getMOD($numOrden)
    {
        $data = array();
        $i = 0;
        $t_pulpeo = DB::table('producciontest.tiempo_pulpeo')->select(DB::raw('COALESCE(SUM(cant_dia),0) as cantDia, COALESCE(SUM(cant_noche),0) as cantNoche,  COALESCE(tiempoPulpeo,0) as tiempoPulpeo'))
            ->where('numOrden', $numOrden)
            ->groupBy('tiempoPulpeo')
            ->get()->first();

        $t_lavado = DB::table('producciontest.tiempo_lavado')->select(DB::raw('COALESCE(SUM(cant_dia),0)as cantDia, COALESCE(SUM(cant_noche),0) as cantNoche, COALESCE(tiempoLavado,0)'))
            ->where('numOrden', $numOrden)
            ->groupBy('tiempoLavado')
            ->get()->first();

        $t_muertos =  DB::table('producciontest.tiempos_muertos')->select(DB::raw('COALESCE(SUM(y1_dia),0) as cantDiaY1, COALESCE(SUM(y2_dia),0) as cantDiaY2, COALESCE(SUM(y1_noche),0) as cantNocheY1, COALESCE(SUM(y2_noche),0)as cantNocheY2'))
            ->where('numOrden', $numOrden)
            ->get()->first();

        $hrsTrabajadas = DB::table('producciontest.orden_produccion')->select('hrsTrabajadas')->where('numOrden', $numOrden)->get()->first();
        $hrsTrabajadas = $hrsTrabajadas->hrsTrabajadas / 2;

        if (!empty($t_pulpeo)) {
            $t_pulpeo_dia = ($t_pulpeo->cantDia * $t_pulpeo->tiempoPulpeo) / 60;
            $t_pulpeo_noche = ($t_pulpeo->cantNoche * $t_pulpeo->tiempoPulpeo) / 60;
        } else {
            $t_pulpeo_dia = 0;
            $t_pulpeo_noche = 0;
        }
        if (!empty($t_lavado)) {
            if (!empty($t_pulpeo)) {
                $t_lavado_dia = ($t_lavado->cantDia * $t_pulpeo->tiempoPulpeo) / 60;
                $t_lavado_noche = ($t_lavado->cantNoche * $t_pulpeo->tiempoPulpeo) / 60;
            } else {
                $t_lavado_dia = 0;
                $t_lavado_noche = 0;
            }
        } else {
            $t_lavado_dia = 0;
            $t_lavado_noche = 0;
        }
        if ($t_muertos) {
            $y1_jumboroll_dia = $hrsTrabajadas - ($t_muertos->cantDiaY1 / 60);
            $y1_jumboroll_noche = $hrsTrabajadas - ($t_muertos->cantNocheY1 / 60);
            $y1_jumboroll_total = $y1_jumboroll_dia + $y1_jumboroll_noche;
            $y2_jumboroll_dia = $hrsTrabajadas - ($t_muertos->cantDiaY2 / 60);
            $y2_jumboroll_noche = $hrsTrabajadas - ($t_muertos->cantNocheY2 / 60);
            $y2_jumboroll_total = $y2_jumboroll_dia + $y2_jumboroll_noche;
        } else {
            $y1_jumboroll_dia = 0;
            $y1_jumboroll_noche = 0;
            $y1_jumboroll_total = 0;
            $y2_jumboroll_dia = 0;
            $y2_jumboroll_noche = 0;
            $y2_jumboroll_total = 0;
        }
        $data[0]['actividad'] = 'Pulper 1 - Pasta Reciclada';
        $data[0]['dia']       = number_format($t_pulpeo_dia, 2) . " hrs";
        $data[0]['noche']     = number_format($t_pulpeo_noche, 2)  . " hrs";
        $data[0]['total']     = number_format(($t_pulpeo_dia + $t_pulpeo_noche), 2)  . " hrs";

        $data[1]['actividad'] = 'Lavadora de Tetrapack';
        $data[1]['dia'] = number_format($t_lavado_dia, 2)  . " hrs";
        $data[1]['noche'] = number_format($t_lavado_noche, 2)  . " hrs";
        $data[1]['total'] = number_format(($t_lavado_dia + $t_lavado_noche), 2)  . " hrs";

        $data[2]['actividad'] = 'Pulper 2 - Pasta Virgen';
        $data[2]['dia'] = number_format(0, 2)  . " hrs";
        $data[2]['noche'] = number_format(0, 2)  . " hrs";
        $data[2]['total'] = number_format(0, 2) . " hrs";

        $data[3]['actividad'] = 'Pulper 2 - Pasta Virgen';
        $data[3]['dia'] = number_format(0, 2)  . " hrs";
        $data[3]['noche'] = number_format(0, 2)  . " hrs";
        $data[3]['total'] = number_format(0, 2)  . " hrs";

        $data[4]['actividad'] = 'Yankee 1 - Jumbo Roll';
        $data[4]['dia'] = number_format($y1_jumboroll_dia, 2)  . " hrs";
        $data[4]['noche'] = number_format($y1_jumboroll_noche, 2)  . " hrs";
        $data[4]['total'] = number_format($y1_jumboroll_total, 2)  . " hrs";

        $data[5]['actividad'] = 'Yankee 2 - Jumbo Roll';
        $data[5]['dia'] = number_format($y2_jumboroll_dia, 2)  . " hrs";
        $data[5]['noche'] = number_format($y2_jumboroll_noche, 2)  . " hrs";
        $data[5]['total'] = number_format($y2_jumboroll_total, 2)  . " hrs";

        $data[6]['actividad'] = 'Caldera';
        $data[6]['dia'] = number_format($hrsTrabajadas, 2)  . " hrs";
        $data[6]['noche'] = number_format($hrsTrabajadas, 2)  . " hrs";
        $data[6]['total'] = number_format($hrsTrabajadas * 2, 2)  . " hrs";

        $data[7]['actividad'] = 'Planta de Tratamiento';
        $data[7]['dia'] = number_format($hrsTrabajadas, 2)  . " hrs";
        $data[7]['noche'] = number_format($hrsTrabajadas, 2)  . " hrs";
        $data[7]['total'] = number_format($hrsTrabajadas * 2, 2)  . " hrs";

        // return $array;
        return response()->json($data);
    }
    public function getSubCostos($numOrden)
    {
        $data = array();
        $i = 0;
        //$query2 = DB::table('producciontest.orden_produccion')->where('numOrden', $key->numOrden)->get();
        $costos_por_orden = DB::table('producciontest.inn_costo_orden_subtotal')->where('numOrden', $numOrden)->get();

        foreach ($costos_por_orden as $key => $costo) {
            $data[$i]['codigo'] = $costo->codigo;
            $data[$i]['descripcion'] = $costo->descripcion;
            $data[$i]['unidad_Medida'] = $costo->unidad_medida;
            $data[$i]['cantidad'] = number_format($costo->cantidad, 2) . " kg";
            $data[$i]['costo_Unitario'] =  number_format($costo->costo_unitario, 2);
            $data[$i]['costo_Total'] =  number_format($costo->subtotal, 2);
            $i++;
        }

        return response()->json($data);
    }

    public function getQuimicos($numOrden)
    {
        $data = array();
        $i = 0;

        $quimico_maquina =   DB::table('producciontest.quimico_maquina')->select('producciontest.quimico_maquina.*', 'producciontest.quimicos.descripcion', 'producciontest.maquinas.nombre')
            ->join('producciontest.quimicos', 'producciontest.quimico_maquina.idQuimico', '=', 'producciontest.quimicos.idQuimico')
            ->join('producciontest.maquinas', 'producciontest.quimico_maquina.idMaquina', '=', 'producciontest.maquinas.idMaquina')
            ->where('producciontest.quimico_maquina.numOrden', $numOrden)
            ->get();

        foreach ($quimico_maquina as $key => $qm) {
            $data[$i]['quimico'] = $qm->descripcion;
            $data[$i]['maquina'] = $qm->nombre;
            $data[$i]['cantidad'] = number_format($qm->cantidad, 2)  . " kg";
            $i++;
        }

        return response()->json($data);
    }

    public function getOtrosConsumos($numOrden)
    {
        $data = array();
        $electricidad = DB::table('producciontest.electricidad')->select('inicial', 'final')
            ->where('numOrden', $numOrden)
            ->get()->first();

        $consumo_agua =  DB::table('producciontest.consumo_agua')->select('inicial', 'final')
            ->where('numOrden', $numOrden)
            ->get()->first();

        $consumo_gas = DB::table('producciontest.gas')->select('inicial', 'final')
            ->where('numOrden', $numOrden)
            ->get()->first();

        $prodRealTon = DB::table('producciontest.inn_produccion_total')
            ->where('numOrden', $numOrden)
            ->get()->first();



        //Electricidad
        if ($electricidad) {
            $inicialE = ($electricidad->inicial == '') ? 0 : $electricidad->inicial;
            $finalE = ($electricidad->final == '') ? 0 : $electricidad->final;
        } else {
            $inicialE = 0;
            $finalE = 0;
        }
        //Consumo de agua
        if ($consumo_agua) {
            $inicialA = ($consumo_agua->inicial == '') ? 0 : $consumo_agua->inicial;
            $finalA = ($consumo_agua->final == '') ? 0 : $consumo_agua->final;
        } else {
            $inicialA = 0;
            $finalA = 0;
        }
        //Consume de gas
        if ($consumo_gas) {
            $inicialG = ($consumo_gas->inicial == '') ? 0 : $consumo_gas->inicial;
            $finalG = ($consumo_gas->final == '') ? 0 : $consumo_gas->final;
        } else {
            $inicialG = 0;
            $finalG = 0;
        }
        //Electricidad
        if ($finalE > 0) {
            $data[0]['Einicial']          = number_format($inicialE, 2);
            $data[0]['Efinal']            = number_format($finalE, 2);
            $data[0]['EtotalConsumo']     =  number_format(($finalE - $inicialE), 2);
            $data[0]['E_ConsumoSTD']      = number_format(((((($finalE - $inicialE) * 560) * 0.8) / ($prodRealTon->prod_real + $prodRealTon->merma_total)) * 1000), 2);
            $data[0]['E_ConsumoPH']       = number_format((($finalE - $inicialE) * 560) * 0.8, 2); //Consumo 80% PH
            $data[0]['E_ConsumoTTestimado'] = number_format((($finalE - $inicialE) * 560), 2); //Consumo 80% PH
        } else {
            $data[0]['Einicial']          = number_format(0, 2);
            $data[0]['Efinal']            = number_format(0, 2);
            $data[0]['EtotalConsumo']     = number_format(0, 2);
            $data[0]['E_ConsumoSTD']      = number_format(0, 2);
            $data[0]['E_ConsumoPH']      = number_format(0, 2);
            $data[0]['E_ConsumoTTestimado'] = number_format((($finalE - $inicialE) * 560), 2); //Consumo 80% PH
        }
        //Consumo de Agua
        if ($finalA > 0) {
            $data[0]['Ainicial']         =  number_format($inicialA, 2);
            $data[0]['Afinal']           =  number_format($finalA, 2);
            $data[0]['AtotalConsumo']    =  number_format(($finalA - $inicialA), 2);
        } else {
            $data[0]['Ainicial']          = number_format(0, 2);
            $data[0]['Afinal']           = number_format(0, 2);
            $data[0]['AtotalConsumo']    = number_format(0, 2);
        }
        //Consumo de Gas
        if ($finalG > 0) {
            $data[0]['Ginicial']          = number_format($inicialG, 2);
            $data[0]['Gfinal']            = number_format($finalG, 2);
            $data[0]['GtotalConsumo']     =  number_format(($finalG - $inicialG), 2);
            $data[0]['G_totalConsumoTon'] = number_format(((($finalG - $inicialG) / ($prodRealTon->prod_real + $prodRealTon->merma_total)) * 1000), 2);
        } else {
            $data[0]['Ginicial']          = number_format(0, 2);
            $data[0]['Gfinal']           = number_format(0, 2);
            $data[0]['GtotalConsumo']    = number_format(0, 2);
            $data[0]['G_totalConsumoTon']   = number_format(0, 2);
        }

        return response()->json($data);
    }
    public function getDetailSumary($numOrden)
    {
        $data = array();
        $i = 0;
        $j = 0;
        $produccion_total = DB::table('producciontest.inn_produccion_total')
            ->select('inn_produccion_total.*', 'producciontest.orden_produccion.*')
            ->join('producciontest.orden_produccion', 'producciontest.inn_produccion_total.numOrden', '=', 'producciontest.orden_produccion.numOrden')
            ->where('producciontest.inn_produccion_total.numOrden', $numOrden);
        $pt = $produccion_total->get();
        $idTetra = DB::table('producciontest.fibras')->select('idFibra')->where('descripcion', 'like', '%Tetrapack%')->get()->first();
        foreach ($pt as $Orden => $key) {
            $data[$i]['numOrden'] = $key->numOrden;
            $data[$i]['merma_total'] = number_format($key->merma_total, 2);
            $data[$i]['residuo_total'] = number_format($key->residuo_total, 2);
            $data[$i]['lavadora_total'] = number_format($key->lavadora_total, 2);
            $data[$i]['prod_real'] = number_format($key->prod_real, 2);
            $data[$i]['prod_total'] = number_format($key->merma_total +  $key->prod_real, 2);
            $data[$i]['fechaInicio'] = date('d/m/Y', strtotime($key->fechaInicio));
            $data[$i]['fechaFinal'] = date('d/m/Y', strtotime($key->fechaFinal));
            $data[$i]['horaInicio'] =  date('g:i a', strtotime($key->horaInicio));
            $data[$i]['horaFinal'] = date('g:i a', strtotime($key->horaFinal));
            $data[$i]['hrsTrabajadas'] = number_format($key->hrsTrabajadas, 2);
            $productos = DB::table('producciontest.productos')->where('idProducto',  $key->producto)->get()->first();;
            $data[$i]['producto'] = $productos->nombre;
            $mp_directa_exist  = $this->getMateriaPrima($numOrden);
            $mp_total =  DB::table('producciontest.mp_directa')->select(DB::raw('SUM(cantidad) as mp_directa'))
                ->where('numOrden', $numOrden)
                ->get()->first();
                
            $totalMPTPACK = DB::table('producciontest.mp_directa')->select(DB::raw('SUM(cantidad) as total'))
                ->where('idFibra', $idTetra->idFibra)
                ->where('numOrden', $numOrden)
                ->get()->first();
            //Calculo de los porcentajes
            $porcentMermaYankeeDry = ($key->merma_total > 0  && $key->prod_real > 0) ?  (($key->merma_total / ($key->prod_real + $key->merma_total)) * 100) :   0;
            $porcentLavadoraTetrapack = ($key->lavadora_total > 0 && $totalMPTPACK->total > 0) ? (($key->lavadora_total / $totalMPTPACK->total) * 100) : 0;
            $porcentResiduosPulper = ($key->residuo_total > 0 && $mp_total->mp_directa > 0) ?  (($key->residuo_total / $mp_total->mp_directa) * 100) : 0;
            $factorFibral =  (!is_null($mp_directa_exist) > 0 && $key->lavadora_total != '') ? (($mp_total->mp_directa - $key->lavadora_total) / ($key->prod_real + $key->merma_total)) : 0;
            $data[$i]['costoBolson'] =  number_format(((($key->merma_total +  $key->prod_real)) / 4.5), 2);
            $data[$i]['bolsones'] =   number_format(40000, 2);

            $data[$i]['factorFibral'] = number_format($factorFibral, 2);
            $data[$i]['porcentMermaYankeeDry'] = number_format($porcentMermaYankeeDry, 2);
            $data[$i]['porcentLavadoraTetrapack'] = number_format($porcentLavadoraTetrapack, 2);
            $data[$i]['porcentResiduosPulper'] = number_format($porcentResiduosPulper, 2);

            //Calculo de la tonelada por dia
            $Tonelada_dia =  (($key->prod_real) > 0 && ($key->hrsTrabajadas > 0)) ?  number_format(($key->prod_real / ($key->hrsTrabajadas / 24)) / 1000, 2) : 0;
            $data[$i]['Tonelada_dia'] = $Tonelada_dia;

            $i++;
        }
        return response()->json($data);
    }

    public function getHrasProducidas($numOrden)
    {

        $horas_efectivas = DB::table('producciontest.horas_efectivas')->select(DB::raw('SUM(TIME_TO_SEC(y1_dia)) as total_y1_Dia,
        SUM(TIME_TO_SEC(y1_noche)) as total_y1_Noche, 
        SUM(TIME_TO_SEC(y2_dia)) as total_y2_Dia,
        SUM(TIME_TO_SEC(y2_noche)) as total_y2_Noche'))
            ->where('numOrden', $numOrden)->where('estado', 1)->groupBy('numOrden')
            ->get()->first();

        if (!is_null($horas_efectivas)) {
            $total_y1_Dia   = $horas_efectivas->total_y1_Dia / 3600;
            $total_y1_Noche = $horas_efectivas->total_y1_Noche / 3600;
            $total_y2_Dia   = $horas_efectivas->total_y2_Dia / 3600;
            $total_y2_Noche = $horas_efectivas->total_y2_Noche / 3600;
            $total          = $total_y1_Dia +  $total_y1_Noche  + $total_y1_Dia +  $total_y1_Noche +  $total_y2_Dia + $total_y2_Noche;
            $totak_yk       = number_format($total / 3, 2);
        } else {
            $total_y1_Dia   = 0;
            $total_y1_Noche = 0;
            $total_y2_Dia   = 0;
            $total_y2_Noche = 0;
            $total          = 0;
            $totak_yk       = 0;
        }


        // YANKEE 1
        $data[0]['nombre'] = 'Yankee  Dryer 1 '; //$horas_efectivas->;
        $data[0]['dia'] =  number_format($total_y1_Dia, 2); //$horas_efectivas->;
        $data[0]['noche'] = number_format($total_y1_Noche, 2);
        $data[0]['total'] =  number_format($total_y1_Dia + $total_y1_Noche, 2);

        //YANKEE 2
        $data[1]['nombre'] = 'Yankee  Dryer 2';
        $data[1]['dia'] = number_format($total_y2_Dia, 2);
        $data[1]['noche'] = number_format($total_y2_Noche, 2);
        $data[1]['total'] = number_format($total_y2_Dia + $total_y2_Noche, 2);
        //Total de hiras efectivas
        $data[1]['totalYk'] = number_format($totak_yk, 2);

        //return $data;
        return response()->json($data);
    }

    public function getMes($date)
    {
        $meses = [
            "January" => "Enero",
            "February" => "Febrero",
            "March" => "Marzo",
            "April" => "Abril",
            "May" => "Mayo",
            "June" => "Junio",
            "July" => "Julio",
            "August" => "Agosto",
            "September" => "Septiembre",
            "October" => "Octubre",
            "November" => "Noviembre",
            "December" => "Diciembre"
        ];

        $clave = array_search($date, $meses);
        $result = $meses[$date];

        return $result;
    }

    public function getDataFormat()
    {
        $data = array();
        $i = 0;

        $produccion_total = DB::table('producciontest.inn_produccion_total')->select('inn_produccion_total.*', 'producciontest.orden_produccion.*')
            ->join('producciontest.orden_produccion', 'producciontest.inn_produccion_total.numOrden', '=', 'producciontest.orden_produccion.numOrden')
            ->where('producciontest.orden_produccion.estado', 1);

        $obj = $produccion_total->get();
        foreach ($obj as $Orden => $key) {
            $data[$i]['numOrden'] = $key->numOrden;
            $co_subTT = DB::table('producciontest.inn_costo_orden_subtotal')->select(DB::raw('SUM(subtotal) as total'))->where('numOrden',  $key->numOrden)->get();

            if (is_null($co_subTT)) {
                $data[$i]['costo_total'] = 0;
            } else {
                foreach ($co_subTT as $costo_subTotal => $cst) {
                    $data[$i]['costo_total'] = $cst->total;
                }
            }

            $data[$i]['fechaInicio'] =  date('d/m/Y', strtotime($key->fechaInicio)) . ' ' .  date('g:i a', strtotime($key->horaInicio));
            $data[$i]['fechaFinal'] = date('d/m/Y', strtotime($key->fechaFinal)) . ' ' . date('g:i a', strtotime($key->horaFinal));
            $data[$i]['anio'] =  date('Y', strtotime($key->fechaInicio));
            $nameMonth = date('F', strtotime($key->fechaInicio));
            $mes = $this->getMes($nameMonth);
            $data[$i]['mes'] = $mes;


            (is_null($key->tipo_cambio)) ? $data[$i]['tipo_cambio'] = 0 :  $data[$i]['tipo_cambio'] = $key->tipo_cambio;
            if (($key->tipo_cambio == 0) || (is_null($key->tipo_cambio))) {
                $data[$i]['prod_real_ton'] =  0;
                $data[$i]['costo_real_ton'] = 0;
                $data[$i]['ct_dolar'] =  0;
            } else {
                $data[$i]['ct_dolar'] =  $cst->total / $key->tipo_cambio;
                $data[$i]['prod_real_ton'] =  $cst->total / $key->tipo_cambio;
                $data[$i]['costo_real_ton'] = ($cst->total / $key->tipo_cambio) / ($key->prod_real / 1000);
            }

            $productos = DB::table('producciontest.productos')->where('idProducto',  $key->producto)->get();
            foreach ($productos as $producto => $p) {
                $data[$i]['producto'] = $p->nombre;
                //$data[$i]['descripcion'] = $p->descripcion;
                $data[$i]['descripcion'] =  $p->descripcion == null || $p->descripcion == '' ? '' : $p->descripcion;
                $data[$i]['ver'] = '<a href="#!"  class="btn "  onclick="getMoreDetail(' . "'" . $key->numOrden . "'" . ', ' . "'" . $p->nombre . "'" .
                    ',' . "'" . $data[$i]['fechaInicio'] . "'" . ', ' . "'" .  $data[$i]['fechaFinal'] . "'" . ')"><i class="fas fa-eye fa-2x text-primary"></i></a>';
            }

            $data[$i]['prod_real'] = $key->prod_real;
            $data[$i]['prod_total'] = $key->merma_total +  $key->prod_real;
            $data[$i]['prod_real_ton'] = ($key->prod_real / 1000);
            $i++;
        }

        $j = 0;
        $k = 0;

        $dataP = array();
        $dataDate = array();
        $arrayValue =  array_unique(array_column($data, 'mes')); // mes
        $arrayValueAnio =  array_unique(array_column($data, 'anio'));  // años
        /*foreach($arrayValueAnio as $anios){
            $dataDate[]
        } */
        $arrayValueMesAnio =  array_merge($arrayValue,  $arrayValueAnio);
        //los meses que estan en la orden No repetidos.
        foreach ($arrayValue as $dataMonth) {

            $prod_real_total        = 0;
            $prod_total_total       = 0;
            $prod_real_ton_total    = 0;
            $costo_total_total      = 0;
            $costo_real_ton_total   = 0;
            $ct_dolar_total         = 0;
            $contador               = 0;
            $mes                    = '';
            $anio                    = '';
            $subData = array();

            foreach ($data as $dataOrden  => $key) {
                if ($key['mes'] == $dataMonth) { //Si tienen el mismo mes
                    $subData[$k]['numOrden'] =        $key['numOrden'];
                    $subData[$k]['producto'] =        $key['producto'];
                    $subData[$k]['descripcion'] =     $key['descripcion'] == null || $key['descripcion'] == '' ? '' : $key['descripcion'];
                    $subData[$k]['anio'] =            $key['anio'];
                    $subData[$k]['mes'] =             $key['mes'];
                    $subData[$k]['fechaInicio'] =     $key['fechaInicio'];
                    $subData[$k]['fechaFinal'] =      $key['fechaFinal'];
                    $subData[$k]['prod_real'] =       $key['prod_real'];
                    $subData[$k]['prod_total'] =      $key['prod_total'];
                    $subData[$k]['prod_real_ton'] =   $key['prod_real_ton'];
                    $subData[$k]['costo_total'] =     $key['costo_total'];
                    $subData[$k]['tipo_cambio'] =     $key['tipo_cambio'];
                    $subData[$k]['ct_dolar'] =        $key['ct_dolar'];
                    $subData[$k]['costo_real_ton'] =  $key['costo_real_ton'];
                    $subData[$k]['ver'] =             $key['ver'];
                    $k++;
                    $prod_real_total +=  $key['prod_real'];
                    $prod_total_total += $key['prod_total'];
                    $prod_real_ton_total +=  $key['prod_real_ton'];
                    $costo_total_total +=  $key['costo_total'];
                    $costo_real_ton_total +=  $key['costo_real_ton'];
                    $ct_dolar_total +=  $key['ct_dolar'];
                    $mes  = $dataMonth;
                    $anio  = $key['anio'];
                    ++$contador;
                }
            }
            $dataP[$j]['ordenes']              = '(' . $contador . ')';
            $dataP[$j]['all_detalles']         = $subData;
            $dataP[$j]['mes_']                 = $mes;
            $dataP[$j]['anio_']                = $anio;
            $dataP[$j]['prod_real_total']      = number_format($prod_real_total, 2);
            $dataP[$j]['prod_total_total']     = number_format($prod_total_total, 2);
            $dataP[$j]['prod_real_ton_total']  = number_format($prod_real_ton_total, 2);
            $dataP[$j]['costo_total_total']    = number_format($costo_total_total, 2);
            $dataP[$j]['costo_real_ton_total'] = number_format($costo_real_ton_total, 2);
            $dataP[$j]['ct_dolar_total']       = number_format($ct_dolar_total, 2);
            $dataP[$j]['detalle_general']      = '<a id="exp_more_" class="exp_more_" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
            $j++;
        }
        return response()->json($dataP);
    }


    public function getData()
    {
        $detalle_orden = DB::table('producciontest.inn_detalles_gumanet')->get();
        $Data = array();
        $k = 0;
        $arrDetalles = array();
        foreach ($detalle_orden as $detalle => $key) {
            $Data[$k]['anio']    = $key->year_;
            $mes =  DateTime::createFromFormat('!m', $key->mes_);
            $Data[$k]['mes']     =   $this->getMes($mes->format('F'));
            $Data[$k]['contOrder']        = $key->contOrder;
            $Data[$k]['prod_real_total']       = number_format($key->prod_real_mensual,2);
            $Data[$k]['prod_total_total']     = number_format($key->prod_total_mensual, 2);
            $Data[$k]['prod_real_ton_total']  = number_format($key->prod_real_tonelada_mensual, 2);
            $Data[$k]['costo_total_total']    = number_format($key->costo_total_mensual, 4);
            $Data[$k]['costo_real_ton_total'] = number_format($key->costo_real_tonelada_mensual, 4);
            $Data[$k]['ct_dolar_total']       = number_format($key->costo_total_dolar_mensual, 4);
            $Data[$k]['detalle_general']       = '<a id="exp_more" class="exp_more" href="#!"><i class="material-icons expan_more">expand_more</i></a>';
            $lista_ordenes  =  $key->Detalles;
            $size     = explode(";", $lista_ordenes);
            $cLineas    = count($size);
            for ($l = 0; $l < $cLineas; $l++) {

                $_detalles     = explode(",", $size[$l]);
                $arrDetalles[$l]['numOrden']        = '<a href="#!"  class=""  onclick="getMoreDetail(' . "'" . $_detalles[0] . "'" . ', ' . "'" . $_detalles[1] . "'" .
                    ',' . "'" . $_detalles[3] . "'" . ', ' . "'" .  $_detalles[4] . "'" . ')"> ' . $_detalles[0] . ' </i></a>';
                $arrDetalles[$l]['producto']        = $_detalles[1];
                $arrDetalles[$l]['descripcion']     = $_detalles[2];
                $arrDetalles[$l]['fechaInicio']     = $_detalles[3];
                $arrDetalles[$l]['fechaFinal']      = $_detalles[4];
                $arrDetalles[$l]['prod_real']       = $_detalles[5];
                $arrDetalles[$l]['prod_total']      = $_detalles[6];
                $arrDetalles[$l]['prod_real_ton']   = $_detalles[7];
                $arrDetalles[$l]['costo_total']     = $_detalles[8];
                $arrDetalles[$l]['tipo_cambio']     = $_detalles[9];
                $arrDetalles[$l]['ct_dolar']        = $_detalles[10];
                $arrDetalles[$l]['costo_real_ton']  = $_detalles[11];
            }

            $Data[$k]['Detalles']       = $arrDetalles;
            $k++;
        }
        return response()->json($Data);
    }
}
