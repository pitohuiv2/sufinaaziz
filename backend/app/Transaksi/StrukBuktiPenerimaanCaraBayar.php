<?php

namespace App\Transaksi;

class StrukBuktiPenerimaanCaraBayar extends Transaksi
{
    protected $table ="strukbuktipenerimaancarabayartr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
