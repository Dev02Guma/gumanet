<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendedorInnova extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "Softland.innova.VENDEDOR";

}
