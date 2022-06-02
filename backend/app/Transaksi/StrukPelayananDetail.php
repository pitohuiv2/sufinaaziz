<?php

namespace App\Transaksi;

class StrukPelayananDetail extends Transaksi
{
    protected $table ="strukpelayanandetailtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
