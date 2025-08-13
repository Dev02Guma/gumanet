<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class ImportacionView extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_gnet_ImportacionCalc";


    public static function getImportacionView(Request $request)
    {
        $COD_MOLECULA       = $request->COD_MOLECULA;
        $nYEar_actual       = $request->nyear_actual;
        $nYEar_pasado       = $request->nyear_pasado;
        $nMonth_ini         = $request->nmonth_ini;
        $nMonth_end         = $request->nmonth_end;

        $CANT_HOMOLO_GROUP  = [];
        $UMK_IMPORTACION    = [];
        $dta_return         = [];

        $TOTAL_UMK_VALOR    = 0;
        $TOTAL_UMK_CANT     = 0;

        //ESE ES EL CODIGO DE IMPORTACION DE UMK
        $ImportCode = env('UMK_IMPORT_CODE', '1234567890'); 
        $IMPORTACIONES = ImportacionView::where('ARTICULO', $COD_MOLECULA)
            ->whereIn('NYEAR', [$nYEar_pasado, $nYEar_actual])
            ->whereBetween('NMONTH', [$nMonth_ini, $nMonth_end])
            ->distinct()
            ->get()
            ->toArray();


        // Agrupar y sumar UNIDADES_HOMOLOGADAS por NYEAR
        foreach ($IMPORTACIONES as $item) {
            $ruc = $item['NRO_RUC'] ?? '';
            $year = $item['NYEAR'] ?? null;


            if (!$year) {
                continue;
            }

            if (!isset($CANT_HOMOLO_GROUP[$year])) {
                $CANT_HOMOLO_GROUP[$year] = [
                    'UNIDADES_HOMOLOGADAS' => 0,
                    'FOB_TOTAL' => 0,
                ];
            }

            if (!isset($UMK_IMPORTACION[$year])) {
                $UMK_IMPORTACION[$year] = [
                    'CANTIDAD' => 0,
                    'VALOR' => 0,
                ];
            }

            $CANT_HOMOLO_GROUP[$year]['UNIDADES_HOMOLOGADAS'] += (float)($item['UNIDADES_HOMOLOGADAS'] ?? 0);
            $CANT_HOMOLO_GROUP[$year]['FOB_TOTAL']  += (float)($item['FOB_TOTAL'] ?? 0);

            
            if (in_array($ruc, [$ImportCode])) {
                $UMK_IMPORTACION[$year]['CANTIDAD'] += (float)($item['UNIDADES_HOMOLOGADAS'] ?? 0);
                $UMK_IMPORTACION[$year]['VALOR']    += (float)($item['FOB_TOTAL'] ?? 0);
            }

        }

        $nyear_val_actual   = (!isset($CANT_HOMOLO_GROUP[$nYEar_actual])) ? 0 : $CANT_HOMOLO_GROUP[$nYEar_actual]['FOB_TOTAL'] ;
        $nyear_val_pasado   = (!isset($CANT_HOMOLO_GROUP[$nYEar_pasado])) ? 0 : $CANT_HOMOLO_GROUP[$nYEar_pasado]['FOB_TOTAL'] ;
        $nyear_val_crec     = ($nyear_val_pasado != 0) ? number_format(($nyear_val_actual - $nyear_val_pasado) / $nyear_val_pasado * 100, 2) : 0; 

        $nyear_cant_actual  = (!isset($CANT_HOMOLO_GROUP[$nYEar_actual])) ? 0 : $CANT_HOMOLO_GROUP[$nYEar_actual]['UNIDADES_HOMOLOGADAS'] ;
        $nyear_cant_pasado  = (!isset($CANT_HOMOLO_GROUP[$nYEar_pasado])) ? 0 : $CANT_HOMOLO_GROUP[$nYEar_pasado]['UNIDADES_HOMOLOGADAS'] ;
        $nyear_cant_crec    = ($nyear_cant_pasado != 0) ? number_format(($nyear_cant_actual - $nyear_cant_pasado) / $nyear_cant_pasado * 100, 2) : 0;

        $umk_val_pasado     = $UMK_IMPORTACION[$nYEar_pasado]['VALOR'] ?? 0;
        $umk_val_actual     = $UMK_IMPORTACION[$nYEar_actual]['VALOR'] ?? 0;
        $umk_val_crec       = ($umk_val_pasado != 0) ? number_format(($umk_val_actual - $umk_val_pasado) / $umk_val_pasado * 100, 2) : 0;
        $ic_crec_val        = ($umk_val_crec < 0) ? 'fa-caret-down text-danger' : 'fa-caret-up text-success';
        
        $umk_cant_pasado    = $UMK_IMPORTACION[$nYEar_pasado]['CANTIDAD'] ?? 0;
        $umk_cant_actual    = $UMK_IMPORTACION[$nYEar_actual]['CANTIDAD'] ?? 0;
        $umk_cant_crec      = ($umk_cant_pasado != 0) ? number_format(($umk_cant_actual - $umk_cant_pasado) / $umk_cant_pasado * 100, 2) : 0;
        $ic_crec_cant       = ($umk_cant_crec < 0) ? 'fa-caret-down text-danger' : 'fa-caret-up text-success';

        $Particion          = ($nyear_val_actual != 0) ?  ( $umk_val_actual / $nyear_val_actual ) * 100 : 0;
        $Particion          = number_format($Particion, 2);

        $TopCompetidores    = self::getTopCompetidores($COD_MOLECULA, $nYEar_actual, $nYEar_pasado,$nMonth_ini, $nMonth_end);
        $ttCompetidores     = count($TopCompetidores);
        $UMKPosition        = array_search($ImportCode, array_column($TopCompetidores, 'NRO_RUC')) + 1 ?? 0;




        $dta_return = [ 
            'PERIODO' => '<b>' . self::NameMonth($nMonth_ini) . '</b> a <b>' . self::NameMonth($nMonth_end) . '</b>',
            'PARTICION'   => $Particion,
            'RANKING'     => [
                'VALOR'     => $UMKPosition . ' / ' . $ttCompetidores,
            ],
            'MERCADO'   => [
                'VALOR_MERCADO' => [
                    $nYEar_pasado  => number_format($nyear_val_pasado, 2),
                    $nYEar_actual  => number_format($nyear_val_actual, 2),
                    'Crec'         => $nyear_val_crec,
                ],
                'CANT_HOMOLOGADAS' => [
                    $nYEar_pasado  => number_format($nyear_cant_pasado, 0),
                    $nYEar_actual  => number_format($nyear_cant_actual, 0),
                    'Crec'         => $nyear_cant_crec,
                ],
            ],
            'UNIMARKSA'   =>[
                'VALOR_MERCADO' => [
                    $nYEar_pasado  => number_format($umk_val_pasado, 2),
                    $nYEar_actual  => number_format($umk_val_actual, 2),
                    'Crec'         => $umk_val_crec,
                    'icon'         => $ic_crec_val,
                ],
                'CANTIDAD' => [
                    $nYEar_pasado  => number_format($umk_cant_pasado, 0),
                    $nYEar_actual  => number_format($umk_cant_actual, 0),
                    'Crec'         => $umk_cant_crec,
                    'icon'         => $ic_crec_cant,
                ],
            ],
            'COMPETIDORES'          => $TopCompetidores,
            'DATA_IMPORTACION'      => $IMPORTACIONES,
        ];

        return response()->json($dta_return);
    }

    public static function getTopCompetidores($Articulos,$nYear_actual, $nYear_pasado, $nMonth_ini, $nMonth_end){

        $Competidores = [];
        $Consulta_SQL = '
            SELECT DISTINCT
                NRO_RUC,
                NOMBRE_IMPORTADOR AS COMPETIDOR,
                NOMBRE_COMERCIAL AS MARCA,
                PAIS_ORIGEN AS ORIGEN,
                SUM(CASE WHEN NYEAR = '.$nYear_pasado.' AND NMONTH BETWEEN '.$nMonth_ini.' AND '.$nMonth_end.' THEN FOB_TOTAL ELSE 0 END) AS [PASADO_FOB],
                SUM(CASE WHEN NYEAR = '.$nYear_actual.' AND NMONTH BETWEEN '.$nMonth_ini.' AND '.$nMonth_end.' THEN FOB_TOTAL ELSE 0 END) AS [ACTUAL_FOB],
                SUM(CASE WHEN NYEAR = '.$nYear_pasado.' AND NMONTH BETWEEN '.$nMonth_ini.' AND '.$nMonth_end.' THEN UNIDADES_HOMOLOGADAS ELSE 0 END) AS [PASADO_CANT],
                SUM(CASE WHEN NYEAR = '.$nYear_actual.' AND NMONTH BETWEEN '.$nMonth_ini.' AND '.$nMonth_end.' THEN UNIDADES_HOMOLOGADAS ELSE 0 END) AS [ACTUAL_CANT]
            FROM
                PRODUCCION.dbo.view_gnet_ImportacionCalc
            WHERE
                ARTICULO = '.$Articulos.'
                AND NYEAR IN ('.$nYear_pasado.', '.$nYear_actual.')
                AND NMONTH BETWEEN '.$nMonth_ini.' AND '.$nMonth_end.'
            GROUP BY
                NRO_RUC,
                NOMBRE_IMPORTADOR,
                NOMBRE_COMERCIAL,
                PAIS_ORIGEN
            ORDER BY [ACTUAL_FOB] DESC;';


        $Top_Competidores = DB::connection('sqlsrv')->select($Consulta_SQL);

        foreach ($Top_Competidores as $item) {

            // Verificar si los valores son nulos y asignar 0 si es necesario
            $item->ACTUAL_FOB = $item->ACTUAL_FOB ?? 0;
            $item->PASADO_FOB = $item->PASADO_FOB ?? 0;
            $item->ACTUAL_CANT = $item->ACTUAL_CANT ?? 0;
            $item->PASADO_CANT = $item->PASADO_CANT ?? 0;
            

            $item->FOB_CREC = ($item->PASADO_FOB != 0) ? number_format(($item->ACTUAL_FOB - $item->PASADO_FOB) / $item->PASADO_FOB * 100, 2) . '%' : 0; // FOB_CREC
            $item->CNT_CREC = ($item->PASADO_CANT != 0) ? number_format(($item->ACTUAL_CANT - $item->PASADO_CANT) / $item->PASADO_CANT * 100, 2) . '%' : 0; // CNT_CREC


            $Competidores[] = [
                'NRO_RUC'       => $item->NRO_RUC,
                'COMPETIDOR'    => $item->COMPETIDOR,
                'MARCA'         => $item->MARCA,
                'ORIGEN'        => $item->ORIGEN,
                'DESCRIPCION'   => $item->DESCRIPCION ?? '',
                'ACTUAL_FOB'    => number_format($item->ACTUAL_FOB, 2),
                'PASADO_FOB'    => number_format($item->PASADO_FOB, 2),
                'FOB_CREC'      => $item->FOB_CREC,
                'ACTUAL_CANT'   => number_format($item->ACTUAL_CANT, 0),
                'PASADO_CANT'   => number_format($item->PASADO_CANT, 0),
                'CNT_CREC'      => $item->CNT_CREC,
                
            ];
            
        }

        return $Competidores;

    }

    public static function NameMonth($month)
    {
        
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        return $months[$month] ?? '';
    }    

    
}
