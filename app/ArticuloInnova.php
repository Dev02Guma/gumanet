<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ArticuloInnova extends Model
{
    protected $connection = 'sqlsrv';
    public $timestamps = false;
    protected $table = "PRODUCCION.dbo.tbl_inventario_innova";

    public function clasificacion1()
    {
        return $this->belongsTo(InventarioInnovaClasificacion::class, 'Clasificacion_1', 'ID');
    }

    public function clasificacion2()
    {
        return $this->belongsTo(InventarioInnovaClasificacion::class, 'Clasificacion_2', 'ID');
    }

  
    public function user()
    {
        return $this->belongsTo(Usuario::class, 'ID_USER');
    }

    public static function getArticulos()
    {
        if (Auth::check()){
        
            $Rol = Auth::user()->id_rol;

            if ($Rol == 4|| $Rol == 1) {
                return ArticuloInnova::get();
            } else {
                return ArticuloInnova::where('ID_USER',Auth::id())->get();
            }
            
        }
    }
}
