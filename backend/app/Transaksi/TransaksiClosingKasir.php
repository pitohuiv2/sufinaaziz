<?php

namespace App\Transaksi;

class TransaksiClosingKasir extends Transaksi
{
    protected $table ="transaksiclosingkasirtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
