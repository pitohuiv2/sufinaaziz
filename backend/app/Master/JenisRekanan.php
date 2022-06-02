<?php

namespace App\Master;

// use Illuminate\Database\Eloquent\Model;

class JenisRekanan extends MasterModel
{
    protected $table ="jenisrekananmt";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";
}
