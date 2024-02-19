<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReOrderSales extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.view_gnet_reorder_sale12month";
}
