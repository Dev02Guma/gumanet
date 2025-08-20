<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticuloMOQ extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_gnet_reorder_moq";
    protected $primaryKey = 'ARTICULO';

    protected $fillable = ['MOQ_REVISADO'];
}
