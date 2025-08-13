<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportacionMuestra extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_gnet_importacion_muestra";
    protected $primaryKey = 'ARTICULO';
}
