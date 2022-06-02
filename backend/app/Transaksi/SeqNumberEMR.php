<?php



namespace App\Transaksi;

class SeqNumberEMR extends Transaksi
{
    protected $table ="seqnumberemr_t";
    protected $primaryKey = 'seqnumber';
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;



}
