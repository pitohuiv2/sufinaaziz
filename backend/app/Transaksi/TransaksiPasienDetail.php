<?php
namespace App\Transaksi;

class TransaksiPasienDetail extends Transaksi
{
    protected $table ="transaksipasiendetailtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
