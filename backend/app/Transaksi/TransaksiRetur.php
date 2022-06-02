<?php


namespace App\Transaksi;

class TransaksiRetur extends Transaksi
{
    protected $table ="transaksireturtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
