<?php

namespace App\Transaksi;

class StrukBuktiPengeluaran extends Transaksi
{
    protected $table ="strukbuktipengeluarantr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}
