<?php

namespace App\Transaksi;

class TransaksiReturDetail extends Transaksi
{
    protected $table ="transaksireturdetailtr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;


}
