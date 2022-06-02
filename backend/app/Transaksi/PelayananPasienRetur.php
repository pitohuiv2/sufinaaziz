<?php


namespace App\Transaksi;

class PelayananPasienRetur extends Transaksi
{
    protected $table ="pelayananpasienretur_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}