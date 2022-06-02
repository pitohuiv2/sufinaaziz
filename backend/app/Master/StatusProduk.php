<?php

namespace App\Master;
 

class StatusProduk extends MasterModel
{
    protected $table ="statusproduk_m";
    protected $fillable = [];

    public function produk(){
        return $this->hasMany('App\Master\Produk', 'objectstatusprodukfk');
    }
}
