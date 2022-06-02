<?php


namespace App\Transaksi;
class TransaksiKirim extends Transaksi
{
    protected $table ="transaksikirimtr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;

}
