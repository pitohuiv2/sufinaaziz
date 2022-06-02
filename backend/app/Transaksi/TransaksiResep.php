<?php


namespace App\Transaksi;

class TransaksiResep extends Transaksi
{
    protected $table ="transaksireseptr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
