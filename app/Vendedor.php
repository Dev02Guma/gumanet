<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.vtVS2_Vendedores";


    public static function getVendedor()
    {  
        return Vendedor::whereNotIn('VENDEDOR',['F01','F12','F02','F18',"F15",'F24',"F23","F22"])->get();
        //return Vendedor::whereIn('VENDEDOR',['F09'])->get();
    }

    public static function getVendedores()
    {
        

        return Vendedor::whereNotIn('VENDEDOR',['F01','F02','F04','F24'])->get();
        
    }
}
