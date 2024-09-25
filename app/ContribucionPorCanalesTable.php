<?php

namespace App;

use App\user;
use Illuminate\Database\Eloquent\Model;


class ContribucionPorCanalesTable extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_contribucion_canales";
}