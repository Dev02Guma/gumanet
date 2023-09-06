<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class KardexUsuario extends Model {
    protected $table = "users";
    protected $connection = 'mysql_kardex_inn';
    public function rol()
    {
        return $this->belongsTo(KardexRoles::class, 'id_rol');
    }
}