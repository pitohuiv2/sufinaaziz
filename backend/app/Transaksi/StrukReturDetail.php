<?php


namespace App\Transaksi;

class StrukReturDetail extends Transaksi
{
    protected $table ="transaksireturdetailtr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;


}
