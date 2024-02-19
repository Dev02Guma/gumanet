<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "DESARROLLO.dbo.metacuota_GumaNet";

    public function detalles()
    {
        return $this->hasMany('App\MetaDetalle', 'IdPeriodo', 'IdPeriodo');
    }
}
