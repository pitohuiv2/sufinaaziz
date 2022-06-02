<?php

namespace App\Transaksi;

class StrukBuktiPenerimaan extends Transaksi
{
    protected $table ="strukbuktipenerimaantr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
