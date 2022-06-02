<?php


namespace App\Transaksi;

class KonversiSatuan extends Transaksi
{
    protected $table ="konversisatuantr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;




}
