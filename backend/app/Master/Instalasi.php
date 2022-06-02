<?php

namespace App\Master;


class Instalasi extends MasterModel
{
    protected $table = 'instalasimt';
    protected $fillable= [];
    public $timestamps = false;
 
    public $incrementing = false;
    protected $primaryKey = "id";


}
