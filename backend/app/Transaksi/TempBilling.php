<?php

namespace App\Transaksi;

class TempBilling extends Transaksi
{
    protected $table ="temp_billingtr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;


}
