<?php

namespace App\Transaksi;

use App\Master\SettingDataFixed;
class DiagnosaTindakanPasien extends Transaksi
{
    protected $table ="diagnosapasienicdixtr";
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}
