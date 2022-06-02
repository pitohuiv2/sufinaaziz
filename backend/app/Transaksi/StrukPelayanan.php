<?php

namespace App\Transaksi;

class StrukPelayanan extends Transaksi
{
    protected $table ="strukpelayanantr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";

    public function pasien_daftar(){
        return $this->belongsTo('App\Transaksi\RegistrasiPasien','registrasipasienfk');
    }


    public function transaksi_pasien(){
        return $this->hasMany('App\Transaksi\TransaksiPasien', 'strukfk');
    }

    public function pasien(){
        return $this->belongsTo('App\Master\Pasien','normidfk');
    }

}
