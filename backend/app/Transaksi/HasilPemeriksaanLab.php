<?php


namespace App\Transaksi;

class HasilPemeriksaanLab extends Transaksi
{
    protected $table ="hasillaboratoriumpatologitr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
