<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaDetalle extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "DESARROLLO.dbo.gn_cuota_x_productos";
}
