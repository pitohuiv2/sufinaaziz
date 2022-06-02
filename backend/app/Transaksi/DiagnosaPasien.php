<?php

namespace App\Transaksi;

use App\Master\SettingDataFixed;
class DiagnosaPasien extends Transaksi
{
    protected $table ="diagnosapasienicdxtr";
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}
