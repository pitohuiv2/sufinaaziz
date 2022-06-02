<?php



namespace App\Transaksi;

class RujukanKeluar extends Transaksi
{
    protected $table = "rujukankeluar_t";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}


