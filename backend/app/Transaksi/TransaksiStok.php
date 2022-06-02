<?php


namespace App\Transaksi;

class TransaksiStok extends Transaksi
{
    protected $table ="transaksistoktr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;

}
