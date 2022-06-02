<?php


namespace App\Transaksi;

class PasienPerjanjian extends Transaksi
{
    protected $table = "perjanjianpasientr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
}