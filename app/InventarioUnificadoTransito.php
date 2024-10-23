<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventarioUnificadoTransito extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'PRODUCCION.dbo.GNET_INVENTARIO_UNIFICADO_TRANSITO';
    protected $primaryKey = 'ARTICULO';
    public $incrementing = false;
    public $timestamps = false;

}