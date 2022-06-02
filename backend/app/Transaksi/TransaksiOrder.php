<?php
namespace App\Transaksi;

class TransaksiOrder extends Transaksi
{
    protected $table = "transaksiordertr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
