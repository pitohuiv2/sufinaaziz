<?php
namespace App\Transaksi;

class PerjanjianPasien extends Transaksi
{
    protected $table ="perjanjianpasientr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}
