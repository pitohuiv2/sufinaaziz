<?php


namespace App\Transaksi;

class EMRPasien extends Transaksi
{
    protected $table ="emrpasientr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;


}
