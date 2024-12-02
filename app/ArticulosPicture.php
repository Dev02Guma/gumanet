<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticulosPicture extends Model
{
    protected $connection = 'mysql_pedido';
    public $timestamps = false;
    protected $table = "tbl_product";
    protected $primaryKey = 'product_id';
    
    public static function getPictures($Articulos) {

        $Images = ArticulosPicture::where('product_sku', $Articulos)->pluck('product_image')->toArray(); 

        $assetImg =(isset($Images[0])) ? env('ASSET_IMG', null).$Images[0] : env('ASSET_URL', null).'/public/img/placeholder.jpg' ;



        return $assetImg;
    }
}
