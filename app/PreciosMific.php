<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreciosMific extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_precio_mific";
}
