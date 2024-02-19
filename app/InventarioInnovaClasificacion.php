<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InventarioInnovaClasificacion extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = "PRODUCCION.dbo.tbl_inventario_innova_clasificacion";
    protected $primaryKey = 'ID'; // Asegúrate de que el nombre de la clave primaria sea correcto

    // Opcionalmente, puedes agregar una relación inversa si también necesitas acceder a los artículos relacionados
    public function articulos1()
    {
        return $this->hasMany(ArticuloInnova::class, 'Clasificacion_1', 'ID');
    }

    public function articulos2()
    {
        return $this->hasMany(ArticuloInnova::class, 'Clasificacion_2', 'ID');
    }
}