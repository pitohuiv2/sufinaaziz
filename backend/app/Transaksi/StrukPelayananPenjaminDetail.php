<?php


namespace App\Transaksi;

class StrukPelayananPenjaminDetail extends Transaksi
{
	protected $table = "strukpelayananpenjamindetailtr";
	protected $fillable = [];
	public $timestamps = false;
	public $incrementing = false;
	protected $primaryKey = "norec";

}
