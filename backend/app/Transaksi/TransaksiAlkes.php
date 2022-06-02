<?php


namespace App\Transaksi;

class TransaksiAlkes extends Transaksi
{
    protected $table ="transaksialkestr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
