<?php

namespace App\Transaksi;

class StrukVerifikasi extends Transaksi
{
    protected $table ="transaksiverifikasitr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
