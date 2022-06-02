<?php


namespace App\Transaksi;

class TransaksiOpname extends Transaksi
{
    protected $table ="transaksiopnametr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;


}
