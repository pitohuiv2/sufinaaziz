<?php

namespace App\Http\Controllers\KiosK;

use App\Http\Controllers\ApiController;
// use App\Master\Alamat;
// use App\Master\Pasien;
// use App\Master\Ruangan;
// use App\Master\SlottingKiosk;
// use App\Master\SlottingOnline;
use Carbon\Carbon;
//use Faker\Provider\DateTime;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Traits\PelayananPasienTrait;
use App\Traits\Valet;
use App\Transaksi\DaftarPasienRuangan;
use DB;
// use App\Transaksi\AntrianPasienRegistrasi;
use App\Transaksi\DaftarRuanganPasien;
use App\Transaksi\KeluhanPelanggan;
use App\Transaksi\PasienPerjanjian;
// use App\Transaksi\SurveyKepuasanPelanggan;
use Webpatser\Uuid\Uuid;

class KiosKC extends ApiController
{
    use Valet, PelayananPasienTrait;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }
    public function saveAntrianTouchscreen(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        $noRec = '';
        try {
            $tglAyeuna = date('Y-m-d H:i:s');
            $tglAwal = date('Y-m-d 00:00:00');
            $tglAkhir = date('Y-m-d 23:59:59');
            $kdRuanganTPP = $this->settingDataFixed('idRuanganTPP1', $kdProfile);

            $newptp = new PasienPerjanjian();
            $norec = $newptp->generateNewId();
            $nontrian = PasienPerjanjian::where('jenis', $request['jenis'])
                ->whereBetween('tanggalreservasi', [$tglAwal, $tglAkhir])
                ->max('noantrian') + 1;
            $newptp->norec = $norec;
            $newptp->koders = $kdProfile;
            $newptp->aktif = true;
            $newptp->ruanganidfk = isset($request['ruanganfk']) ? $request['ruanganfk'] : $kdRuanganTPP;
            $newptp->jeniskelaminidfk = 0;
            $newptp->noantrian = $nontrian;
            $newptp->noreservasi = "-";
            $newptp->pendidikanidfk = 0;
            $newptp->tanggalreservasi = $tglAyeuna;
            $newptp->tipepasien = "BARU";
            $newptp->type = "BARU";
            $newptp->jenis = $request['jenis'];
            $newptp->statuspanggil = 0;
            $newptp->iskiosk = true;
            $newptp->save();
            $noRec = $newptp->norec;


            $ReportTrans = "Selesai";
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
        }

        if ($transStatus != 'false') {

            DB::commit();
            $result = array(
                "noRec" => $noRec,
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            DB::rollBack();
            $result = array(
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "status" => 400,
                "message" => $ReportTrans,
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getComboSettingKios(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $kodePPKRujukan = $this->settingDataFixed('kodePPKRujukan', $kdProfile);
        $isTemporaryBrigding = $this->settingDataFixed('isTemporaryBrigding', $kdProfile);
        $isAdminOtomatisKiosk = $this->settingDataFixed('isAdminOtomatisKiosk', $kdProfile);
        $data['ppkpelayanan'] = $kodePPKRujukan;
        $data['isTemporaryBrigding'] = $isTemporaryBrigding;
        $data['isAdminOtomatisKiosk'] = $isAdminOtomatisKiosk;
        return $this->respond($data);
    }
    public function getComboKios2(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenRajal',   $kdProfile));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item) {
            $kdDepartemenRawatJalan[] =  (int)$item;
        }

        $dataRuanganJalan = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk as objectdepartemenfk')
            ->where('ru.aktif', true)
            ->where('ru.koders', $kdProfile)
            ->wherein('ru.instalasiidfk', $kdDepartemenRawatJalan);
        if (isset($request['eksek']) && $request['eksek'] != '' && $request['eksek'] != 'undefined') {
            if ($request['eksek'] == 'false') {
                $dataRuanganJalan = $dataRuanganJalan->whereRaw("(ru.iseksekutif is null or ru.iseksekutif =false)");
            } else if ($request['eksek'] == 'true') {
                $dataRuanganJalan = $dataRuanganJalan->whereRaw(" ru.iseksekutif =true");
            }
        }

        $dataRuanganJalan = $dataRuanganJalan->orderBy('ru.namaruangan');
        $dataRuanganJalan = $dataRuanganJalan->get();

        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter',   $kdProfile);

        $dkoter = \DB::table('pegawaimt')
            ->select('*')
            ->where('aktif', true)
            ->where('koders', $kdProfile)
            ->where('objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->orderBy('namalengkap')
            ->get();


        $result = array(
            'ruanganrajal' => $dataRuanganJalan,
            'dokter' => $dkoter,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }
    public function getComboDokterKios(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter', $kdProfile);
        $req = $request->all();
        $data = \DB::table('pegawaimt')
            ->select('id as kode', 'namalengkap as nama')
            ->where('aktif', true)
            ->where('objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->where('koders', (int)$kdProfile)
            ->orderBy('namalengkap');

        if (
            isset($req['namalengkap']) &&
            $req['namalengkap'] != "" &&
            $req['namalengkap'] != "undefined"
        ) {
            $data = $data->where('namalengkap', 'ilike', '%' . $req['namalengkap'] . '%');
        }


        $data = $data->take(50);
        $data = $data->get();

        return $this->respond($data);
    }
    public function getRuanganByKodeInternal($kode, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('ruanganmt')
            ->select('*','instalasiidfk as objectdepartemenfk')
            ->where('aktif', true)
            ->where('kdinternal', '=', $kode)
            ->where('koders', '=', $kdProfile)
            ->whereRaw("(iseksekutif is null or iseksekutif =false)")
            ->orderBy('namaruangan')
            ->first();

        $result = array(
            'data' => $data,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDiagnosaByKode($kode, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        $data = \DB::table('icdxmt')
            ->where('aktif', true)
            ->where('kddiagnosa', '=', $kode)
            ->where('koders', '=', $kdProfile)
            ->first();

        $result = array(
            'data' => $data,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDokterInternal(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dat = DB::table('pegawaimt')
            ->select('id', 'namalengkap')
            ->where('aktif', true)
            ->where('koders', $kdProfile)
            ->where('kodeexternal', $request['kode'])
            ->first();
        if (!empty($dat)) {
            return $this->respond($dat);
        } else {
            return $this->respond(false);
        }
    }
    public function getDataRuangan(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        if ($request['jenis'] == 'rj') {
            $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenRawatJalanFix', $kdProfile));
            $arr = [];
            foreach ($deptJalan as $item) {
                $arr[] =  (int)$item;
            }
        } else {
            $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile));
            $arr = [];
            foreach ($deptRanap as $itemRanap) {
                $arr[] =  (int)$itemRanap;
            }
        }

        $dataRuangan = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->whereIn('ru.instalasiidfk', $arr)
            ->where('ru.aktif', true)
            ->where('ru.koders', $kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        $result = array(
            'ruangan' => $dataRuangan,
        );

        return $this->respond($result);
    }

    public function SaveKeluhanPelanggan(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        DB::beginTransaction();
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->select('lu.objectpegawaifk')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', $idProfile)
            ->first();
        try {
            if ($request['data']['id'] == '') {
                $dataKeluhan = new KeluhanPelanggan();
           
                $norecDK = $dataKeluhan->generateNewId();
                $dataKeluhan->koders = $idProfile;
                $dataKeluhan->aktif = true;
       
                $dataKeluhan->norec = $norecDK;
            } else {
                $dataKeluhan =  KeluhanPelanggan::where('norec', $request['data']['id'])->where('kdprofile', $idProfile)->first();
            }
            $dataKeluhan->alamat =  $request['data']['alamat'];
            $dataKeluhan->email =  $request['data']['email'];
            $dataKeluhan->keluhan = $request['data']['keluhan'];
            $dataKeluhan->namapasien = $request['data']['namapasien'];
            $dataKeluhan->norm =  $request['data']['norm'];
            $dataKeluhan->notlp =  $request['data']['notlp'];
            $dataKeluhan->ruanganidfk = $request['data']['objectruanganfk'];
            $dataKeluhan->saran = $request['data']['saran'];
            $dataKeluhan->pekerjaanidfk = $request['data']['objectpekerjaanfk'];
            $dataKeluhan->umur = $request['data']['umur'];
            $dataKeluhan->tglkeluhan = $request['data']['tglkeluhan'];
            $dataKeluhan->pegawaiidfk = (int)$dataPegawai->objectpegawaifk; 
            $dataKeluhan->notlpkntr = $request['data']['notlpkntr'];
            if (isset($request['data']['namapengisi'])) {
                $dataKeluhan->namapengisi = $request['data']['namapengisi'];
            }

            $dataKeluhan->save();
            $norecKeluhan = $dataKeluhan->norec;


            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Simpan Gagal";
        }

        if ($transStatus == 'true') {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "norec" => $norecKeluhan,
                "as" => 'Xoxo',
            );
        } else {
            $transMessage = "Simpan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
}
