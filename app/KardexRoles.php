<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class KardexRoles extends Model {
    protected $table = "rol";
    protected $connection = 'mysql_kardex_inn';
}