<?php
namespace App\Transaksi;

class StrukRealisasi extends Transaksi
{
    protected $table = "transaksirealisasitr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
