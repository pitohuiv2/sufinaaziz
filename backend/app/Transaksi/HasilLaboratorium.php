<?php


namespace App\Transaksi;

class HasilLaboratorium extends Transaksi
{
    protected $table ="hasillaboratoriumtr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


}
