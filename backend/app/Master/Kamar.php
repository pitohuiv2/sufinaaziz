<?php

namespace App\Master;


class Kamar extends MasterModel
{
    protected $table = 'kamarmt';
    protected $fillable = [];
    public $timestamps = false;

    protected $primaryKey = "id";

}