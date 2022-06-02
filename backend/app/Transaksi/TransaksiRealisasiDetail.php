<?php
namespace App\Transaksi;

class TransaksiRealisasiDetail extends Transaksi
{
    protected $table = "transaksirealisasidetailtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}
