<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ReOrderPointByArticulo extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_gnet_reorder_sale12month_articulo";
}