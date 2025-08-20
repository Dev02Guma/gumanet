<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticuloPotencialDiscasa extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_base_reorder_potencialCA";
    protected $primaryKey = 'ARTICULO';

	protected $fillable = ['POTENCIAL_CA'];
    
}
