<?php


namespace App\Transaksi;

class AntrianApotik extends Transaksi
{
    protected $table ="antrianapotiktr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
