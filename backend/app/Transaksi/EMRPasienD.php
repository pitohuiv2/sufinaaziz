<?php


namespace App\Transaksi;

class EMRPasienD extends Transaksi
{
    protected $table ="emrpasiendtr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;


}
