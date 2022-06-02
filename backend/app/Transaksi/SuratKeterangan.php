<?php

namespace App\Transaksi;

use App\Master\SettingDataFixed;
class SuratKeterangan extends Transaksi
{
    protected $table ="suratketerangan_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
