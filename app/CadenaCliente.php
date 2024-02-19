<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CadenaCliente extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_cadena_de_farmacia";
    protected $primaryKey = 'CLIENTE';

}
