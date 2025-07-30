<?php

namespace App;

use App\user;
use Illuminate\Database\Eloquent\Model;


class Lote extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.iweb_lotes";
    protected $fillable = [
        'LOTE','FECHA_VENCIMIENTO'
    ];

}