<?php

namespace App\Transaksi;

class EmrFoto extends Transaksi
{
    protected $table = "emrfoto_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}