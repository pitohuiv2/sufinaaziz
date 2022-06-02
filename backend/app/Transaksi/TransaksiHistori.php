<?php

namespace App\Transaksi;

class TransaksiHistori extends Transaksi
{
    protected $table ="transaksihistoritr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
