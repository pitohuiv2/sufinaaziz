<?php

namespace App\Transaksi;

class StrukVerifikasiAnggaran extends Transaksi
{
    protected $table ="strukverifikasianggaran_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}
