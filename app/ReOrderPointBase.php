<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



use Session;

class ReOrderPointBase extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_gnet_factura_linea";

    public static function getRecords($request) 
    {        
        $DataReturn = [];
        $DataReorderPoint = ReOrderPointBase::all();
        $Articulos = Articulos::get()->toArray();

        foreach ($DataReorderPoint as $key => $value) {

            $posicion = array_search($value->ARTICULO, array_column($Articulos, 'ARTICULO'));
            $Descripcion = $posicion !== false ? $Articulos[$posicion]['DESCRIPCION'] : 'N/D';
            $Laboratorio = $posicion !== false ? $Articulos[$posicion]['LABORATORIO'] : 'N/D';

            $DataReturn[] = [
                'ARTICULO'      => $value->ARTICULO,
                'DESCRIPCION'   => strtoupper($Descripcion),
                'LABORATORIO'   => strtoupper($Laboratorio),
                'CANTIDAD'      => $value->CANTIDAD,
                'NMONTH'        => $value->NMONTH,
                'NYEAR'         => $value->NYEAR,
                'SEGMENTO'      => $value->SEGMENTO,
            ];
        }
        return $DataReturn;
    }
}