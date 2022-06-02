<?php

namespace App\Master;


class KomponenDarah extends MasterModel
{
    protected $table = 'komponendarahmt';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";
}
