<?php

namespace App\Master;

// use Illuminate\Database\Eloquent\Model;

class SatuanKecil extends MasterModel
{
    protected $table ="satuankecil_m";
    protected $fillable = [];

    public function produk(){
        return $this->hasMany('App\Master\Produk', 'objectsatuankecilfk');
    }
}
