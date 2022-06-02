<?php
namespace App\Transaksi;

class PemakaianAsuransi extends Transaksi
{
    protected $table ="pemakaianasuransitr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
