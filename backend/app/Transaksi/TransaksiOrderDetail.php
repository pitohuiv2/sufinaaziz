<?php
namespace App\Transaksi;

class TransaksiOrderDetail extends Transaksi
{
    protected $table = "transaksiorderdetailtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}
