<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotaCreditos extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb4_facturas_por_rutas";
}
