<?php
namespace App\Transaksi;

class PetugasPelaksana extends Transaksi
{
    protected $table ="petugaspelaksanatr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


    public function pegawai(){
        return $this->belongsTo('App\Master\Pegawai', 'pegawaiidfk');
    }
}
