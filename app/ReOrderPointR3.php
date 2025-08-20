<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use App\Logs_calcs;

use Session;

class ReOrderPointR3 extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_base_reorder_v3_calc";

    /**
     * Executes three stored procedures to calculate reorder points for articles.
     *
     * @return void
     */
    public static function Calcular()
    {
        $currentDate = date('Y-m-d');
        //$startOfMonth = date('Y-m-01', strtotime($currentDate));

        // $FechaIni   = date('Y-m-d 00:00:00.000', strtotime('-11 months', strtotime($startOfMonth)));
        // $FechaEnd   = date('Y-m-d 00:00:00.000', strtotime($currentDate . ' -1 days'));

        $FechaIni   = date('Y-m-d 00:00:00.000', strtotime('-12 months', strtotime($currentDate)));
        $FechaEnd   = date('Y-m-d 00:00:00.000', strtotime($currentDate ));

        // Ejecutar el primer procedimiento almacenado
        DB::connection('sqlsrv')->statement("EXEC PRODUCCION.dbo.sp_base_reorder_v3 ?, ?", [$FechaIni, $FechaEnd]);
        
        // Insertar en el modelo Logs_calcs
        Logs_calcs::create([
            'Modulo'        => 'ReOrderPoint_R3',
            'ini'           => $FechaIni,
            'end'           => $FechaEnd,
            'Observacion'   => 'Calculo de Reorder Point actualizado al: ' . date('Y-m-d' , strtotime($FechaEnd)),
        ]);
        

    }
    public static function getReorderPoint($request) 
    {        

        $Year_actual       = date('Y');
        $Year_anterior     = date('Y', strtotime('-1 year'));
        $Month_actual      = intval(date('n'));

        $DataReturn = [];
        $Columms    = [];

        $CountColums = 0;

        $DataReorderPoint = ReOrderPointR3::get();

        $Months_Privado  = DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.sp_base_months_privado ?, ?, ?", [$Year_anterior,$Month_actual,'PRIVADO']);

        $Months_Discasa  = DB::connection('sqlsrv')->select("EXEC PRODUCCION.dbo.sp_base_months_discasa ?", [$Year_actual]); 

        // Obtener los nombres de las columnas dinámicamente
        $Columns_Privado = array_keys(get_object_vars($Months_Privado[0]));
        $Columns_Discasa = array_keys(get_object_vars($Months_Discasa[0]));
        
        foreach ($DataReorderPoint as $key => $value) {

            $Position_Privado = array_search($value->ARTICULO, array_column($Months_Privado, 'ARTICULO'));
            $Position_Discasa = array_search($value->ARTICULO, array_column($Months_Discasa, 'ARTICULO'));

            // Inicializar arrays de datos extra
            $PrivadoData = [];
            $DiscasaData = [];

            // Agregar dinámicamente las columnas de Months_Privado
            foreach ($Columns_Privado as $columnName) {
                $Prefix = $columnName.'_pv';
                $PrivadoData[$Prefix] = ($Position_Privado !== false) ? $Months_Privado[$Position_Privado]->$columnName : 0 ;
                
                // Agregar al array de columnas
                $Columms[$CountColums] = $Prefix; 
                $CountColums++;
            }

            // Agregar dinámicamente las columnas de Months_Discasa
            foreach ($Columns_Discasa as $columnName) {
                $Prefix = $columnName.'_ds';
                $DiscasaData[$Prefix] =  ($Position_Discasa !== false) ?  $Months_Discasa[$Position_Discasa]->$columnName : 0 ;

                // Agregar al array de columnas
                $Columms[$CountColums] = $Prefix; 
                $CountColums++;
            }

            // Merge entre los datos fijos + privados + discasa
            $DataReturn[] = array_merge([
                'ARTICULO'                  => $value->ARTICULO,
                'DESCRIPCION'               => strtoupper($value->DESCRIPCION),
                'LABORATORIO'               => strtoupper($value->LABORATORIO),
                'LEADTIME'                  => $value->LEADTIME,
                'CATEGORIA'                 => $value->CATEGORIA,
                'PROM_NORMAL'               => $value->PROM_NORMAL,
                'PROM_3M'                   => $value->PROM_3M,
                'PROM_ANUAL'                => $value->PROM_ANUAL,
                'INVENTARIO'                => $value->INVENTARIO,                
                'LOTE'                      => $value->LOTE,
                'FECHA_VENCE_LOTE'          => date('d-m-Y', strtotime($value->FECHA_VENCE_LOTE)),
                'CANT_VENCE_LOTE'           => $value->CANT_VENCE_LOTE,
                'CANTIDAD_INGRESADA'        => $value->CANTIDAD_INGRESADA,
                'PEDIDO'                    => $value->PEDIDO,
                'TRANSITO'                  => $value->TRANSITO,
                'ONHAND'                    => $value->ONHAND,
                'PROCENT_ANUAL'             => number_format(($value->PROCENT_ANUAL * 100), 2, '.', ''),
                'NECESITDAD_COMPRA_ANUAL'   => $value->NECESITDAD_COMPRA_ANUAL,
                'FACT_CA_YEAR_ACTUAL'       => $value->FACT_CA_YEAR_ACTUAL,
                'POTENCIAL_CA'              => $value->POTENCIAL_CA,
                'PEDIDO_TOTAL'              => $value->PEDIDO_TOTAL,
                'MOQ'                       => $value->MOQ,
                'ULTM_COST_USD'             => $value->ULTM_COST_USD,
                'COSTO_PROM_DOL'            => $value->COSTO_PROM_DOL
            ], $PrivadoData, $DiscasaData); 
        }

        $MergeData = [
            'Rows' => $DataReturn,
            'Columns' => $Columms,
        ];

        return $MergeData;
    }

    
}
