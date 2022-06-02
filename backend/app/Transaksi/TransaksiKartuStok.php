<?php

namespace App\Transaksi;
class TransaksiKartuStok extends Transaksi
{
    protected $table ="transaksikartustoktr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;




}
