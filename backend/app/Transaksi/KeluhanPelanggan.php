<?php
namespace App\Transaksi;

class KeluhanPelanggan extends Transaksi
{
    protected $table ="keluhanpelanggantr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
    protected $depositId= null;



}
