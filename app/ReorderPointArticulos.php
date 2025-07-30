<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ReorderPointArticulos extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_categoria_articulo_canales";
}