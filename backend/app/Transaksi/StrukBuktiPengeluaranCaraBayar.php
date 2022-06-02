<?php

namespace App\Transaksi;

class StrukBuktiPengeluaranCaraBayar extends Transaksi
{
    protected $table ="strukbuktipengeluarancarabayar_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
