<?php
namespace App\Master;

class KelompokPasien extends MasterModel
{
    protected $table ="kelompokpasienmt";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";

    public function __construct(){$this->setTransformerPath('App\Transformers\Master\KelompokPasienTransformer');}

}
