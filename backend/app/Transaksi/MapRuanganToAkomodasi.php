<?php


namespace App\Transaksi;
class MapRuanganToAkomodasi extends Transaksi
{
    protected $table ="mapruangantoakomodasitr";
    protected $primaryKey = 'id';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;


}