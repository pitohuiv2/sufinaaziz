<?php

namespace App\Master;
 

class TempatTidur extends MasterModel
{
    protected $table = 'tempattidurmt';
    protected $fillable = [];
    public $timestamps = false;
    protected $primaryKey = "id";

    public function kamar(){
        return $this->belongsTo('App\Master\Kamar', 'kamaridfk');
    }
    public function status_bed(){
        return $this->belongsTo('App\Master\StatusBed', 'statusbedidfk');
    }


}
