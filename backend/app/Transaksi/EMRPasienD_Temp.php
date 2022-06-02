<?php
namespace App\Transaksi;

class EMRPasienD_Temp extends Transaksi
{
    protected $table ="emrpasientemptr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;


}
