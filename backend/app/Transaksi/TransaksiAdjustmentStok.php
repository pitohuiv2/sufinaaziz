<?php


namespace App\Transaksi;

class TransaksiAdjustmentStok extends Transaksi
{
    protected $table ="transaksiadjusmentstoktr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;

}
