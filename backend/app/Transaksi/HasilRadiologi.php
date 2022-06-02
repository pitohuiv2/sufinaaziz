<?php

namespace App\Transaksi;

class HasilRadiologi extends Transaksi
{
    protected $table = "hasilradiologitr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
