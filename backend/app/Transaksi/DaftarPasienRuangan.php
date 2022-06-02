<?php
namespace App\Transaksi;

class DaftarPasienRuangan extends Transaksi
{
    protected $table ="daftarpasienruangantr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
