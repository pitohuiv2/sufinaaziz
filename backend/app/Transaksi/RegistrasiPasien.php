<?php
namespace App\Transaksi;

use App\Master\SettingDataFixed;
class RegistrasiPasien extends Transaksi
{
    protected $table ="registrasipasientr";
    protected $fillable = ['kelompokpasienlastidfk'];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
    protected $depositId= null;

    public function antrian_pasien_diperiksa(){
        return $this->hasMany('App\Transaksi\DaftarPasienRuangan', 'registrasipasienfk', 'norec');
    }

    public function pelayanan_pasien()
    {
        return $this->hasManyThrough('App\Transaksi\TransaksiPasien', 'App\Transaksi\DaftarPasienRuangan', 'registrasipasienfk', 'daftarpasienruanganfk', 'norec');
    }

    public function transaksi_pasien_detail()
    {
        return $this->hasManyThrough('App\Transaksi\TransaksiPasienDetail', 'App\Transaksi\DaftarPasienRuangan', 'registrasipasienfk', 'transaksipasienfk', 'norec');
    }

    public function KelompokPasien(){
        return $this->hasMany('App\Master\KelompokPasien', 'kelompokpasienlastidfk', 'id');
    }

}
