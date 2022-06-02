<?php
namespace App\Master;

class ListNotif extends MasterModel
{
    protected $table = 'listnotif';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'norec';

}
