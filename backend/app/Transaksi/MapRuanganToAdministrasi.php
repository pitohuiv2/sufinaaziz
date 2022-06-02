<?php


namespace App\Transaksi;
class MapRuanganToAdministrasi extends Transaksi
{
    protected $table ="mapruangantoadministrasitr";
    protected $primaryKey = 'id';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;


}
