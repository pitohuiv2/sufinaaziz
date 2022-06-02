<?php


namespace App\Transaksi;

class BPJSKlaimTxt extends Transaksi
{
    protected $table ="bpjsklaimtxt_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}