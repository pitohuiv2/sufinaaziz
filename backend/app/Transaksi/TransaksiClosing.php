<?php

namespace App\Transaksi;

class TransaksiClosing extends Transaksi
{
    protected $table ="transaksiclosingtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
