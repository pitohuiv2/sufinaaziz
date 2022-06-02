<?php


namespace App\Transaksi;

class SeqNumber extends Transaksi
{
    protected $table ="seqnumbertr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;



}
