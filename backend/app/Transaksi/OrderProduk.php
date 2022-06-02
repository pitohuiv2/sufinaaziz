<?php


namespace App\Transaksi;

class OrderProduk extends Transaksi
{
    protected $table = "orderproduk_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}