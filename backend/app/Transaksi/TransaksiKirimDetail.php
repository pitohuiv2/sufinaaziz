<?php
namespace App\Transaksi;
class TransaksiKirimDetail extends Transaksi
{
    protected $table ="transaksikirimdetailtr";
    protected $primaryKey = 'norec';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;




}
