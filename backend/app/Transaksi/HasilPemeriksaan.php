<?php


namespace App\Transaksi;

class HasilPemeriksaan extends Transaksi
{
    protected $table ="hasilpemeriksaan_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
