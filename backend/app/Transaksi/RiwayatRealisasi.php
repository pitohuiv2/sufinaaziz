<?php
namespace App\Transaksi;

class RiwayatRealisasi extends Transaksi
{
    protected $table = "transaksirealisasidetailtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}
