<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Articulo_vinneta_modal extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.gnet_articulos_vinneta";

}
