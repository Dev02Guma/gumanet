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
        

        return $Array_Analisis_IMS;
    }

}