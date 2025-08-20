<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AnalisisIMS extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_ims_report";

    public static function getAnalisisIMS(Request $request)
    {
        $Array_Analisis_IMS = [];

        $Rows_Analisis_IMS = AnalisisIMS::get();

        foreach ($Rows_Analisis_IMS as $key => $item) {
            $Array_Analisis_IMS[] = [
                'ARTICULO'                  => $item->ARTICULO,
                'COUNT_COMPETIDORES'        => $item->COUNT_COMPETIDORES,
                'VAL_US_2024'               => number_format($item->VAL_US_2024, 2),
                'PRECIO_PROM_IMS_2024'      => number_format($item->PRECIO_PROM_IMS_2024, 2),
                'DIF'                       => number_format($item->DIF, 2),

                'TOP1_MANU_DESC'            => $item->TOP1_MANU_DESC,
                'TOP1_PRICE'                => number_format($item->TOP1_PRICE, 2),
                'DIF_TOP1'                  => number_format($item->DIF_TOP1, 2),

                'TOP2_MANU_DESC'            => $item->TOP2_MANU_DESC,
                'TOP2_PRICE'                => number_format($item->TOP2_PRICE, 2),
                'DIF_TOP2'                  => number_format($item->DIF_TOP2, 2),

                'TOP3_MANU_DESC'            => $item->TOP3_MANU_DESC,
                'TOP3_PRICE'                => number_format($item->TOP3_PRICE, 2),
                'DIF_TOP3'                  => number_format($item->DIF_TOP3, 2),

                'PACKS_TOTAL_UMK23'         => number_format($item->PACKS_TOTAL_UMK23, 0),
                'PACKS_TOTAL_EQV_IMS_23'    => number_format($item->PACKS_TOTAL_EQV_IMS_23, 0),
                'MARKET_SHARE_UMK_23'       => number_format($item->MARKET_SHARE_UMK_23, 2),

                'PACKS_TOTAL_UMK24'         => number_format($item->PACKS_TOTAL_UMK24, 0),
                'PACKS_TOTAL_EQV_IMS_24'    => number_format($item->PACKS_TOTAL_EQV_IMS_24, 0),
                'MARKET_SHARE_UMK_24'       => number_format($item->MARKET_SHARE_UMK_24, 2),

                'CRECI_23_24'               => number_format($item->CRECI_23_24, 2),

                'PACKS_MANU1'               => $item->PACKS_MANU1,
                'TOP1_AVG_PRICE'            => number_format($item->TOP1_AVG_PRICE, 2),
                'PACKS_CANT1'               => number_format($item->PACKS_CANT1, 0),
                'PACKS_CANT_DIF1_24'        => number_format($item->PACKS_CANT_DIF1_24, 2),

                'PACKS_MANU2'               => $item->PACKS_MANU2,
                'TOP2_AVG_PRICE'            => number_format($item->TOP2_AVG_PRICE, 2),
                'PACKS_CANT2'               => number_format($item->PACKS_CANT2, 0),
                'PACKS_CANT_DIF2_24'        => number_format($item->PACKS_CANT_DIF2_24, 2),

                'PACKS_MANU3'               => $item->PACKS_MANU3,
                'TOP3_AVG_PRICE'            => number_format($item->TOP3_AVG_PRICE, 2),
                'PACKS_CANT3'               => number_format($item->PACKS_CANT3, 0),
                'PACKS_CANT_DIF3_24'        => number_format($item->PACKS_CANT_DIF3_24, 2),
            ];
        }

        return $Array_Analisis_IMS;
    }

}