<?php

namespace App\Master;

class AsalProduk extends MasterModel
{
    protected $table ="asalprodukmt";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";

}