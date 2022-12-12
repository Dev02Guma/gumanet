<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticuloVinneta extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.gnet_articulos_vinneta";

    public static function getArticulos()
    {

        return ArticuloVinneta::all();
    }
}
