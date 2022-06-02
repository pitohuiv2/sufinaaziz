<?php

namespace App\Master;
 

class Ruangan extends MasterModel
{
    protected $table = 'ruanganmt';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";
 
}
