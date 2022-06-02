<?php

namespace App\Transaksi;

class Sal extends Transaksi
{
    protected $table = 'sal_t';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}


