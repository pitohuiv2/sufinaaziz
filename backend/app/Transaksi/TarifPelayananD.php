<?php


namespace App\Transaksi;

class TarifPelayananD extends Transaksi
{
    protected $table ="tarifpelayanandmt";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";


}
