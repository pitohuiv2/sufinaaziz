<?php
/**
 * Created by PhpStorm.
 * User: QWERTYUIOP
 * Date: 07/12/2018
 * Time: 11.07
 */

namespace App\Master;

class SatuanResep extends MasterModel
{
    protected $table = 'satuanresepmt';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";

}
