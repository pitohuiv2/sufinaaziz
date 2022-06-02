<?php


namespace App\Transaksi;

class BPJSRujukan extends Transaksi
{
    protected $table ="bpjsrujukantr";
    protected $fillable = [];
    public $timestamps = false;
    protected $primaryKey = "norec";
    public $incrementing = false;

}