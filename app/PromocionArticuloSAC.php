<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromocionArticuloSAC extends Model
{
    protected $table = "db_sac_app.tbl_promocion";
    protected $connection = 'mysql_pedido';
}
