<?php
namespace App\Transaksi;

class TransaksiPasien extends Transaksi
{
    protected $table ="transaksipasientr";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";


    public function daftar_pasien_ruangan(){
        return $this->belongsTo('App\Transaksi\DaftarPasienRuangan', 'daftarpasienruangantrfk','norec');
    }
    public  function produk(){
        return $this->belongsTo('App\Master\Produk', 'produkidfk');
    }


 

}
