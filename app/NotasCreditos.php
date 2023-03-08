<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotasCreditos extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_notas_creditos";
}
