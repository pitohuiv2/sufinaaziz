<?php

namespace App\Master;
 

class ProdusenProduk extends MasterModel
{
    protected $table ="produsenproduk_m";
    protected $fillable = [];

    public function produk(){
        return $this->hasMany('App\Master\Produk', 'objectprodusenprodukfk');
    }
}
