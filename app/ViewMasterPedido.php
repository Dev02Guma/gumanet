<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ViewMasterPedido extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "PRODUCCION.dbo.view_master_pedidos_umk_v2";
    
   
}
