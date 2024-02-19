<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Budget extends Model
{
    public static function dtProyect($request) {        
        
        $startDate  = $request->input('f1');
        $endDate    = $request->input('f2');

      
      
        // ACTUALIZA PRIMERO LOS CAMPOS DE PRECIO PROMEDIO Y MARGEN DE CONTRIBUCION TOMANDO EN CUENTA EL RANGO SOLICITADO
        DB::connection("sqlsrv")->select("EXEC PRODUCCION.dbo.gnet_presupuesto_update '$startDate', '$endDate'");

        //CONTRUYE LA METRICA DE NUMERO DE MES A
        $resultados = DB::connection("sqlsrv")->select('EXEC PRODUCCION.dbo.gnet_presupuesto_calc ?, ?', [$startDate, $endDate]);

        

        // Formatear los resultados según lo esperado por DataTables
        $datosFormateados = [];

        foreach ($resultados as $resultado) {
            $fila = [
                'ARTICULO' => $resultado->ARTICULO,
                'DESCRIPCION' => $resultado->DESCRIPCION,
                'PRESUPUESTO' => $resultado->UND_ANUAL,
                'CS_VALOR' => $resultado->VAL_ANUAL,
                'FECHA' => [], 
                'PREC_PROM' => $resultado->PREC_PROM,
                'CONTRIBUCION' => $resultado->CONTRIBUCION,
                'UND_MES' => $resultado->UND_MES,
                'VAL_MES' => $resultado->VAL_MES
            ];

    
        $columnas_agregadas = [];
        foreach ($resultado as $columna => $valor) {
            if ($columna !== 'ARTICULO' && $columna !== 'DESCRIPCION' && $columna !== 'PRESUPUESTO' && $columna !== 'CS_VALOR' && $columna !== 'TOTAL' && $columna !== 'UND_ANUAL'  && $columna !== 'VAL_ANUAL'  && $columna !== 'UND_MES' && $columna !== 'VAL_MES' && $columna !== 'PREC_PROM' && $columna !== 'CONTRIBUCION' ) {
                
                $fila[$columna] = $valor;
                $nombre_columna = strstr($columna, '_', true);


                if (!in_array($nombre_columna, $columnas_agregadas)) {
                    // Agregar el nombre de la columna al array de columnas agregadas
                    $columnas_agregadas[] = $nombre_columna;
        
        
                    // Si necesitas agregar el nombre de la columna a un subarray 'FECHA'
                    $fila['FECHA'][] = [
                        'mes' => $nombre_columna,
                    ];
                }

                
              
            }
        }
        

        $datosFormateados[] = $fila;
    }
        return $datosFormateados;
    }

    public static function dtArticulo($request) {        
        
        $startDate  = $request->input('f1');
        $endDate    = $request->input('f2');
        $ARTICULO   = $request->input('ARTICULO');

        $resultados = DB::connection("sqlsrv")->select('EXEC PRODUCCION.dbo.gnet_presupuesto_articulos ?, ?, ?', [$startDate, $endDate,$ARTICULO]);

        foreach ($resultados as $resultado) {
            $fila = [
                'ARTICULO' => $resultado->ARTICULO,
                'FECHA' => [], 
                'UND_MES' => $resultado->UND_MES,
            ];

    
        $columnas_agregadas = [];
        foreach ($resultado as $columna => $valor) {
            if ($columna !== 'ARTICULO' && $columna !== 'UND_MES' ) {
                
                $fila[$columna] = $valor;
                $fila['FECHA'][] = [
                    'mes' => $columna,
                ];
              
            }
        }
        

        $datosFormateados[] = $fila;
    }
        return $datosFormateados;
    }



    
}
