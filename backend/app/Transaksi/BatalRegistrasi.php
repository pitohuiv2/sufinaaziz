<?php
namespace App\Transaksi;

use App\Master\SettingDataFixed;
class BatalRegistrasi extends Transaksi
{
    protected $table ="batalregistrasitr";
    protected $fillable = ['alasanpembatalan'];
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "norec";
    protected $depositId= null;



}
