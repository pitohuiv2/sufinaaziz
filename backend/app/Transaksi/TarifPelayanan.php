<?php


namespace App\Transaksi;

class TarifPelayanan extends Transaksi
{
    protected $table ="tarifpelayananmt";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";


}
