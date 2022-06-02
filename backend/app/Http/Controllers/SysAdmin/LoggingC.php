<?php
/**
 * Created by PhpStorm.
 * User: Xoxo
 * Date: 8/2/2019
 * Time: 2:03 PM
 */


namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\ApiController;
use App\Master\JenisPegawai;
// use App\Master\Pasien;
// use App\Master\Produk;
// use App\Transaksi\AntrianPasienDiperiksa;
use App\Transaksi\LoggingUser;
// use App\Transaksi\PasienDaftar;
// use App\Transaksi\PelayananPasien;
// use App\Transaksi\PelayananPasienDelete;
// use App\Transaksi\PelayananPasienPetugas;
// use App\Transaksi\RegistrasiPelayananPasien;
// use App\Transaksi\StrukPelayanan;
// use App\Transaksi\StrukResep;
// use App\Transaksi\TempBilling;
use Illuminate\Http\Request;
use App\Traits\PelayananPasienTrait;
use DB;
use App\Traits\Valet;
use Carbon\Carbon;

class LoggingC extends ApiController
{

    use Valet, PelayananPasienTrait;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function saveLoggingAll(Request $request)
    {
        DB::beginTransaction();
        $kdProfile = $this->getDataKdProfile($request);
        $transStatus = true;
        $dataLogin = $request->all();
        $newId = LoggingUser::max('id');
        $newId = $newId + 1;
        $logUser = new LoggingUser();
        $logUser->id = $newId;
        $logUser->norec = $logUser->generateNewId();
        $logUser->koders = $kdProfile;
        $logUser->aktif = true;
        $logUser->jenislog = $request['jenislog'];
        $logUser->noreff = $request['noreff'];
        $logUser->referensi = $request['referensi'];
        $logUser->keterangan = $request['keterangan'];
        $logUser->loginuserfk = $dataLogin['userData']['id'];
        $logUser->tanggal = $this->getDateTime()->format('Y-m-d H:i:s');
        try {
            $logUser->save();
            $res = $logUser->jenislog;
            $transMsg = "Simpan Log " . $res . " Sukses ";
        } catch (\Exception $e) {
            $transStatus = false;
            $transMsg = "Simpan Log Gagal ";
        }

        if ($transStatus == true) {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMsg,
                "as" => 'Xoxo'
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMsg
            );
        }
        return $this->respond($result);
    }
}
