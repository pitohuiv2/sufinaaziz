<?php


namespace App\Transaksi;

class RisOrder extends Transaksi
{
    protected $table ="ris_order";
    protected $primaryKey = 'order_key';
    protected $fillable = [];
    public $timestamps = false;

}
