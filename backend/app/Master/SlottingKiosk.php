<?php

namespace App\Master;

class SlottingKiosk extends MasterModel
{
    protected $table = "slottingkioskmt";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";
}