<?php
namespace App\Transaksi;

class TransaksiRealisasi extends Transaksi
{
    protected $table = "transaksirealisasitr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
