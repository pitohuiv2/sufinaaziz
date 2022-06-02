<?php

namespace App\Master;

// use Illuminate\Database\Eloquent\Model;

class SatuanStandar extends MasterModel
{
    protected $table ="satuanstandarmt";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";

    public function produk(){
        return $this->hasMany('App\Master\Produk', 'objectsatuanstandarfk');
    }
}
