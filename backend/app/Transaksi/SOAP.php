<?php

namespace App\Transaksi;

use App\BaseModel;

class SOAP extends Transaksi
{
    protected $table = "soaptr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
}
