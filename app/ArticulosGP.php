<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticulosGP extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.GP_iweb_articulos";
    protected $primaryKey = 'ARTICULO';
}
