<?php
namespace App\Transaksi;

class HasilRadiologiListGambar extends Transaksi
{
    protected $table = "hasilradiologilistgambar_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

}