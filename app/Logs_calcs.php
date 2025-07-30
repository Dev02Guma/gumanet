<?php

namespace App;

use App\user;
use Illuminate\Database\Eloquent\Model;

class Logs_calcs extends Model
{
    protected $table = 'tbl_logs_calc';
    public $timestamps = false;
    protected $fillable = ['Modulo', 'ini', 'end', 'Observacion'];
    
}
