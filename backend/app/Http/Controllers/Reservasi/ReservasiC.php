<?php

namespace App\Http\Controllers\Reservasi;

use App\Http\Controllers\ApiController;
use App\Master\Alamat;
use App\Master\Pasien;
use App\Master\SlottingOnline;
use App\Master\SlottingLibur;
use App\Master\Ruangan;
use App\Web\Profile;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Traits\PelayananPasienTrait;
use App\Traits\Valet;
use DB;
use App\Transaksi\DaftarRuanganPasien;
use App\Transaksi\PasienPerjanjian;
use Webpatser\Uuid\Uuid;

class ReservasiC extends ApiController
{
    use Valet, PelayananPasienTrait;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }
    public function getComboReservasi(Request $request)
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
            ->wherein('ru.instalasiidfk', $kdDepartemenRawatJalan)
            ->orderBy('ru.namaruangan')
            ->get();
        $jk = \DB::table('jeniskelaminmt')
            ->select('id', 'jeniskelamin')
            ->where('aktif', true)
            ->orderBy('jeniskelamin')
            ->get();
        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter',   $kdProfile);

        $dkoter = \DB::table('pegawaimt')
            ->select('*')
            ->where('aktif', true)
            ->where('koders', $kdProfile)
            ->where('objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->orderBy('namalengkap')
            ->get();

        $kelompokPasien = \DB::table('kelompokpasienmt')
            ->select('id', 'kelompokpasien')
            ->where('koders', $kdProfile)
            ->where('aktif', true)
            ->orderBy('kelompokpasien')
            ->get();
        $result = array(
            'ruanganrajal' => $dataRuanganJalan,
            'jeniskelamin' => $jk,
            'dokter' => $dkoter,
            'kelompokpasien' => $kelompokPasien,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }
    public function getPasienByNoCmTglLahir($nocm, $tgllahir)
    {
        $data = \DB::table('pasienmt as ps')
            ->leftJOIN('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftjoin('pendidikanmt as pdd', 'ps.pendidikanidfk', '=', 'pdd.id')
            ->leftjoin('pekerjaanmt as pk', 'ps.pekerjaanidfk', '=', 'pk.id')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->select(
                'ps.norm as nocm',
                'ps.id as nocmfk',
                'ps.namapasien',
                'ps.jeniskelaminidfk as objectjeniskelaminfk',
                'jk.jeniskelamin',
                'alm.alamatlengkap',
                'pdd.pendidikan',
                'pk.pekerjaan',
                'ps.noidentitas',
                'ps.notelepon',
                'ps.tempatlahir',
                'ps.nobpjs',
                DB::raw(" to_char ( ps.tgllahir,'yyyy-MM-dd') as tgllahir")
            )
            ->where('ps.aktif', true);
        if (isset($nocm) && $nocm != "" && $nocm != "undefined" && $nocm != "null") {
            $data = $data->where('ps.norm', '=', $nocm)
                ->Orwhere('ps.noidentitas', '=', $nocm);
        }
        $data = $data->get();

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function saveReservasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $tgl = $request['tglReservasiFix'];
            $dataReservasi = \DB::table('perjanjianpasientr as apr')
                ->select('apr.norec', 'apr.tanggalreservasi')
                ->whereRaw("apr.tanggalreservasi = '$tgl'")
                ->where('apr.ruanganidfk', $request['poliKlinik']['id'])
                ->where('apr.noreservasi', '!=', '-')
                ->whereNotNull('apr.noreservasi')
                ->where('apr.aktif', true)
                ->where('apr.koders', (int) $kdProfile)
                ->get();
            if (count($dataReservasi) > 0) {
                $result = array(
                    "status" => 400,
                    "message" => 'Tidak bisa Reservasi, Coba di jadwal yang lain',
                );
                return $this->setStatusCode($result['status'])->respond($result, 'Mohon maaf dijam tersebut sudah ada yang reservasi, Coba di jadwal yang lain');
            }
            if ($request['isBaru'] == false) {
                $pasien  = Pasien::where('norm', $request['noCm'])->first();
            }
            $jenis  = 'B';
            if ($request['tipePembayaran']['id'] != 2){
                $jenis  = 'A';
            }
            $nontrian = PasienPerjanjian::where('jenis',$jenis)
            ->whereBetween('tanggalreservasi', [date('Y-m-d 00:00',strtotime($tgl)), date('Y-m-d 23:59',strtotime($tgl))])
            ->max('noantrian') + 1;

            $newptp = new PasienPerjanjian();
            // $nontrian = PasienPerjanjian::max('noantrian') + 1;
            $newptp->norec = $newptp->generateNewId();;
            $newptp->koders = (int) $kdProfile;
            $newptp->aktif = true;
            $newptp->noantrian = $nontrian;
            $newptp->ruanganidfk = $request['poliKlinik']['id'];
            $newptp->jeniskelaminidfk = $request['jenisKelamin']['id'];
            $newptp->noreservasi = substr(Uuid::generate(), 0, 7);
            $newptp->tanggalreservasi = $request['tglReservasiFix'];
            $newptp->tgllahir = $request['tglLahir'];
            $newptp->kelompokpasienidfk = $request['tipePembayaran']['id'];
            $newptp->pendidikanidfk = 0;
            $newptp->namapasien =  $request['namaPasien'];
            $newptp->noidentitas =  $request['nik'];
            $newptp->tglinput = date('Y-m-d H:i:s');
            if ($request['tipePembayaran']['id'] == 2) {
                $newptp->nobpjs = $request['noKartuPeserta'];
                $newptp->norujukan = $request['noRujukan'];
            } else {
                $newptp->noasuransilain = $request['noKartuPeserta'];
            }
            $newptp->notelepon = $request['noTelpon'];
            if (isset($request['dokter']['id'])) {
                $newptp->pegawaiidfk =  $request['dokter']['id'];
            }

            if ($request['isBaru'] == true) {
                $newptp->tipepasien = "BARU";
                $newptp->type = "BARU";
            } else {
                $newptp->tipepasien = "LAMA";
                $newptp->type = "LAMA";
            }
            if ($request['tipePembayaran']['id'] == 1){
                $newptp->jenis = "A";
            }else if ($request['tipePembayaran']['id'] == 2){
                $newptp->jenis = "B";
            }
            if (isset($pasien) && !empty($pasien)) {
                $newptp->agamaidfk = $pasien->agamaidfk;
                $alamat = Alamat::where('normidfk', $pasien->id)->first();
                if (!empty($alamat)) {
                    $newptp->alamatlengkap = $alamat->alamatlengkap;
                    $newptp->desakelurahanidfk = $alamat->desakelurahanidfk;
                    $newptp->negara = $alamat->objectnegarafk;
                }
                $newptp->golongandarahidfk =  $pasien->golongandarahidfk;
                $newptp->kebangsaan = $pasien->kebangsaanidfk;
                $newptp->namaayah = $pasien->namaayah;
                $newptp->namaibu = $pasien->namaibu;
                $newptp->namasuamiistri = $pasien->namasuamiistri;

                $newptp->noaditional = $pasien->noaditional;
                $newptp->noidentitas = $pasien->noidentitas;
                $newptp->normidfk =  $pasien->id;
                $newptp->paspor =  $pasien->paspor;
                $newptp->pekerjaanidfk =  $pasien->pekerjaanidfk;
                $newptp->pendidikanidfk = $pasien->pendidikanidfk != null ? $pasien->pendidikanidfk  : 0;
                $newptp->statusperkawinanidfk =  $pasien->statusperkawinanidfk;
                $newptp->tempatlahir = $pasien->tempatlahir;
            }
            $newptp->save();
            $newptp->namaruangan = Ruangan::where('id', $newptp->ruanganidfk)
                ->where('koders', (int) $kdProfile)
                ->first()->namaruangan;
            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }
        $ReportTrans = "Selesai";
        if ($transStatus == true) {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $newptp,
                "antrol" => $this->saveAntrolV2($newptp),
                "as" => 'Xoxo',
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getHistoryReservasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        $data = \DB::table('perjanjianpasientr as apr')
            ->leftJoin('pasienmt as pm', 'pm.id', '=', 'apr.normidfk')
            ->leftJoin('alamatmt as alm', 'alm.normidfk', '=', 'pm.id')
            ->leftJoin('jeniskelaminmt as jk', 'jk.id', '=', 'pm.jeniskelaminidfk')
            ->leftJoin('jeniskelaminmt as jks', 'jks.id', '=', 'apr.jeniskelaminidfk')
            ->leftJoin('pekerjaanmt as pk', 'pk.id', '=', 'pm.pekerjaanidfk')
            ->leftJoin('pendidikanmt as pdd', 'pdd.id', '=', 'pm.pendidikanidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'apr.ruanganidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'apr.pegawaiidfk')
            ->leftJoin('kelompokpasienmt as kps', 'kps.id', '=', 'apr.kelompokpasienidfk')
            ->select(
                'apr.norec',
                'pm.norm as nocm',
                'apr.noreservasi',
                'apr.tanggalreservasi',
                'apr.ruanganidfk as objectruanganfk',
                'apr.pegawaiidfk as objectpegawaifk',
                'ru.namaruangan',
                'apr.isconfirm',
                'pg.namalengkap as dokter',
                'pm.id as nocmfk',
                'pm.namapasien',
                'apr.namapasien',
                'alm.alamatlengkap',
                'pk.pekerjaan',
                'pm.noasuransilain',
                'pm.noidentitas',
                'apr.nobpjs',
                'pm.nohp',
                'pdd.pendidikan',
                'apr.type',
                'kps.kelompokpasien',
                'apr.kelompokpasienidfk as objectkelompokpasienfk',
                'ru.instalasiidfk as objectdepartemenfk',
                'ru.prefixnoantrian',
                'apr.norujukan',
                DB::raw('(case when pm.namapasien is null then apr.namapasien else pm.namapasien end) as namapasien,
                (case when apr.isconfirm=true then \'Confirm\' else \'Reservasi\' end) as status,case when pm.tempatlahir is null then apr.tempatlahir else pm.tempatlahir end as tempatlahir,
                case when jk.jeniskelamin is null then jks.jeniskelamin else jk.jeniskelamin end as jeniskelamin,
                case when apr.tgllahir is null then pm.tgllahir else apr.tgllahir end as tgllahir,
                case when apr.tipepasien = \'LAMA\' then pm.nohp else  apr.notelepon end as notelepon')
            )
            ->whereNull('apr.isconfirm')
            ->where('apr.noreservasi', '!=', '-')
            ->whereNotNull('apr.noreservasi')
            ->where('apr.koders',  $kdProfile)
            ->where('apr.aktif', true);


        if (isset($request['nocmNama']) && $request['nocmNama'] != "" && $request['nocmNama'] != "undefined" && $request['nocmNama'] != "null") {
            $data =
                $data->where('pm.norm', $request['nocmNama'])
                ->Orwhere('apr.namapasien', 'ilike', '%' . $request['nocmNama'] . '%');
        }
        if (isset($request['tgllahir']) && $request['tgllahir'] != "" && $request['tgllahir'] != "undefined" && $request['tgllahir'] != "null" &&  $request['tgllahir'] != 'Invaliddate') {
            $tgllahir = $request['tgllahir'];
            $data =
                $data->whereRaw("to_char( apr.tgllahir, 'dd-MM-yyyy')  ='$tgllahir' ");
        }

        if (isset($request['noReservasi']) && $request['noReservasi'] != "" && $request['noReservasi'] != "undefined" && $request['noReservasi'] != "null") {
            $data =
                $data->where('apr.noreservasi', $request['noReservasi']);
        }
        $data = $data->orderBy('apr.tanggalreservasi', 'desc');
        if (isset($request['jmlRows']) && $request['jmlRows'] != "" && $request['jmlRows'] != "undefined" && $request['jmlRows'] != "null" && $request['jmlRows'] != 0) {
            $data = $data->take($request['jmlRows']);
        }

        if (isset($request['jmlOffset']) && $request['jmlOffset'] != "" && $request['jmlOffset'] != "undefined" && $request['jmlOffset'] != "null") {
            $data = $data->offset($request['jmlOffset']);
        }
        $data = $data->get();

        $result = array(
            'data' => $data,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function deleteReservasi(Request $request)
    {
        DB::beginTransaction();
        try {
            PasienPerjanjian::where('norec', $request['norec'])->update([
                'aktif' => false,
                 'isbatal' => true,
            ]);
            $transStatus = 'true';

            $ReportTrans = "Hapus Reservasi Sukses";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Hapus Reservasi Gagal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function saveAntrianTouchscreen(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        DB::beginTransaction();
        $noRec = '';
        $tglAyeuna = date('Y-m-d H:i:s');
        $tglAwal = date('Y-m-d 00:00:00');
        $tglAkhir = date('Y-m-d 23:59:59');
        $kdRuanganTPP = $this->settingDataFixed('idRuanganTPP1',   $kdProfile);
        try {
            $newptp = new DaftarRuanganPasien();
            $norec = $newptp->generateNewId();
            $nontrian = DaftarRuanganPasien::where('jenis', $request['jenis'])
                ->whereBetween('tanggalreservasi', [$tglAwal, $tglAkhir])
                ->where('kdprofile', $kdProfile)
                ->max('noantrian') + 1;
            $newptp->norec = $norec;
            $newptp->kdprofile = $kdProfile;
            $newptp->statusenabled = true;
            $newptp->objectruanganfk = $kdRuanganTPP;
            $newptp->objectjeniskelaminfk = 0;
            $newptp->noantrian = $nontrian;
            $newptp->noreservasi = "-";
            $newptp->objectpendidikanfk = 0;
            $newptp->tanggalreservasi = $tglAyeuna;
            $newptp->tipepasien = "BARU";
            $newptp->type = "BARU";
            $newptp->jenis = $request['jenis'];
            $newptp->statuspanggil = 0;
            $newptp->save();
            $noRec = $newptp->norec;


            $ReportTrans = "Simpan Antrian";
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Simpan Antrian Gagal";
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
                "noRec" => $noRec,
                "status" => 400,
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getRuanganByKodeInternal($kode)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('ruangan_m')
            ->where('statusenabled', true)
            ->where('kdinternal', '=', $kode)
            ->where('kdprofile', '=', $kdProfile)
            ->first();

        $result = array(
            'data' => $data,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDiagnosaByKode($kode)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('diagnosa_m')
            ->where('statusenabled', true)
            ->where('kddiagnosa', '=', $kode)
            ->where('kdprofile', '=', $kdProfile)
            ->first();

        $result = array(
            'data' => $data,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getSlottingByRuangan($kode, $tgl)
    {
        $dataReservasi = \DB::table('perjanjianpasientr as apr')
            ->select('apr.norec', 'apr.tanggalreservasi')
            ->whereRaw(" format(apr.tanggalreservasi,'yyyy-MM-dd') = '$tgl'")
            ->where('apr.objectruanganfk', $kode)
            ->where('apr.noreservasi', '!=', '-')
            ->whereNotNull('apr.noreservasi')
            ->where('apr.statusenabled', true)
            ->get();

        $ruangan = \DB::table('ruangan_m as ru')
            ->join('slottingonline_m as slot', 'slot.objectruanganfk', '=', 'ru.id')
            ->select(
                'ru.id',
                'ru.namaruangan',
                'ru.objectdepartemenfk',
                'slot.jambuka',
                'slot.jamtutup',
                'slot.quota',
                DB::raw("datepart(hour,slot.jamtutup) -datepart(hour, slot.jambuka)as totaljam")
            )
            ->where('ru.statusenabled', true)
            ->where('ru.id', $kode)
            ->where('slot.statusenabled', true)
            ->first();
        $begin = new Carbon($ruangan->jambuka);
        $end = new Carbon($ruangan->jamtutup);
        $waktuPerorang = ((float)$ruangan->totaljam / (float)$ruangan->quota) * 60;
        $waktuPerorang = $waktuPerorang . ' min';
        $interval = \DateInterval::createFromDateString($waktuPerorang . ' min');
        $times = new \DatePeriod($begin, $interval, $end);
        $jamArr = [];
        foreach ($times as $time) {
            $jamArr[] = array(
                'jam' => $time->format('H:i'),
            );
        }

        $i = 0;
        $reservasi = [];
        foreach ($dataReservasi as $items) {
            $jamUse =  new Carbon($items->tanggalreservasi);
            $reservasi[] = array(
                'jamreservasi' => $jamUse->format('H:i')
            );
        }
        $slot  = array(
            'id' => $ruangan->id,
            'namaruangan' => $ruangan->namaruangan,
            'objectdepartemenfk' => $ruangan->objectdepartemenfk,
            'jambuka' => $ruangan->jambuka,
            'jamtutup' => $ruangan->jamtutup,
            'totaljam' => $ruangan->totaljam,
            'quota' => $ruangan->quota,
            'waktu' => $waktuPerorang,
            'listjam' => $jamArr
        );
        $result = array(
            'slot' => $slot,
            'reservasi' => $reservasi,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDaftarSlotting(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $ruangan = \DB::table('ruangan_m as ru')
            ->join('slottingonline_m as slot', 'slot.objectruanganfk', '=', 'ru.id')
            ->select(
                'ru.id as idruangan',
                'slot.id',
                'ru.namaruangan',
                'ru.objectdepartemenfk',
                'slot.jambuka',
                'slot.jamtutup',
                'slot.quota',
                DB::raw("extract(hour from slot.jamtutup) -extract(hour from slot.jambuka)as totaljam")
            )
            // DB::raw("datepart(hour,slot.jamtutup) -datepart(hour, slot.jambuka)as totaljam"))
            ->where('ru.statusenabled', true)
            ->where('slot.kdprofile', $kdProfile)
            ->where('slot.statusenabled', true);
        //			->where('ru.id', $kode)
        if (isset($request['namaRuangan']) && $request['namaRuangan'] != 'undefined' && $request['namaRuangan'] != '') {
            $ruangan = $ruangan->where('ru.namaruangan', 'ilike', '%' . $request['namaRuangan'] . '%');
        }
        if (isset($request['quota']) && $request['quota'] != 'undefined' && $request['quota'] != '') {
            $ruangan = $ruangan->where('slot.quota', '=', $request['quota']);
        }
        $ruangan = $ruangan->get();

        $result = array(
            'data' => $ruangan,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function saveSlotting(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['id'] == '') {
                $newptp = new SlottingOnline();
                $newptp->id = SlottingOnline::max('id') + 1;
                $newptp->statusenabled = true;
                $newptp->kdprofile = $kdProfile;
            } else {
                $newptp = SlottingOnline::where('id', $request['id'])->first();
            }

            $newptp->objectruanganfk = $request['objectruanganfk'];
            $newptp->jambuka = $request['jambuka'];
            $newptp->jamtutup =  $request['jamtutup'];
            $newptp->quota =  $request['quota'];
            $newptp->save();

            $ReportTrans = "Simpan Slotting";
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Simpan Slotting Gagal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "data" => $newptp,
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            DB::rollBack();
            $result = array(
                //				"noRec" =>$noRec,
                "status" => 400,
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function deleteSlotting(Request $request)
    {

        try {
            SlottingOnline::where('id', $request['id'])->delete();
            $ReportTrans = "Sukses ";
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Hapus slotting Gagal";
        }

        if ($transStatus != 'false') {

            DB::commit();
            $result = array(

                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            DB::rollBack();
            $result = array(

                "status" => 400,
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getSlottingByRuanganNew($kode, $tgl, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataReservasi = \DB::table('perjanjianpasientr as apr')
            ->select('apr.norec', 'apr.tanggalreservasi')
            ->whereRaw("to_char(apr.tanggalreservasi,'yyyy-MM-dd') = '$tgl'")
            ->where('apr.ruanganidfk', $kode)
            ->where('apr.noreservasi', '!=', '-')
            ->where('apr.koders', $kdProfile)
            ->whereNotNull('apr.noreservasi')
            ->where('apr.aktif', true)
            ->get();

        $ruangan = \DB::table('ruanganmt as ru')
            ->join('slottingonlinemt as slot', 'slot.ruanganidfk', '=', 'ru.id')
            ->select(
                'ru.id',
                'ru.namaruangan',
                'ru.instalasiidfk as objectdepartemenfk',
                'slot.jambuka',
                'slot.jamtutup',
                'slot.quota',
                // DB::raw("DATEDIFF(second,  	[slot].[jambuka],	[slot].[jamtutup]) / 3600.0 AS totaljam "))
                DB::raw("(EXTRACT(EPOCH FROM slot.jamtutup) - EXTRACT(EPOCH FROM slot.jambuka))/3600 as totaljam")
            )
            ->where('ru.aktif', true)
            ->where('ru.id', $kode)
            ->where('slot.koders', $kdProfile)
            ->where('slot.aktif', true)
            ->first();
        if (empty($ruangan)) {
                    $result = array(
                    'tanggal' => $tgl,
                    'slot' => NULL,
                    'reservasi' => [],
                    'as' => 'Xoxo',
                );
                    return($result);
        }
        $begin = new Carbon($ruangan->jambuka);
        $jamBuka = $begin->format('H:i');
        $end = new Carbon($ruangan->jamtutup);
        $jamTutup = $end->format('H:i');
        $quota = (float)$ruangan->quota;
        $waktuPerorang = ((float)$ruangan->totaljam / (float)$ruangan->quota) * 60;

        $i = 0;
        $reservasi = [];
        foreach ($dataReservasi as $items) {
            $jamUse =  new Carbon($items->tanggalreservasi);
            $reservasi[] = array(
                'jamreservasi' => $jamUse->format('H:i')
            );
        }

        $slot  = array(
            'id' => $ruangan->id,
            'namaruangan' => $ruangan->namaruangan,
            'objectdepartemenfk' => $ruangan->objectdepartemenfk,
            'jambuka' => $jamBuka,
            'jamtutup' => $jamTutup,
            'totaljam' => $ruangan->totaljam,
            'quota' => (float)$quota,
            'interval' => $waktuPerorang,
            //            'listjam' => $jamArr
        );
        $result = array(
            'tanggal' => $tgl,
            'slot' => $slot,
            'reservasi' => $reservasi,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDaftarSlottingAktif(Request $request)
    {
        $tglAwal = $request['tglAwal'] . ' 00:00';
        $tglAkhir = $request['tglAkhir'] . ' 23:59';
        $ruangan = \DB::table('ruangan_m as ru')
            ->join('slottingonline_m as slot', 'slot.objectruanganfk', '=', 'ru.id')
            ->select(
                'ru.id as idruangan',
                'slot.id',
                'ru.namaruangan',
                'ru.objectdepartemenfk',
                'slot.jambuka',
                'slot.jamtutup',
                'slot.quota',
                DB::raw("datepart(hour,slot.jamtutup) -datepart(hour, slot.jambuka)as totaljam
				")
            )
            ->where('ru.statusenabled', true)
            ->where('slot.statusenabled', true)
            ->get();
        $slot = [];
        if (count($ruangan) > 0) {
            foreach ($ruangan as $item) {
                $waktuPerorang = ((float)$item->totaljam / (float)$item->quota) * 60;
                $slot[] = array(
                    'id' => $item->id,
                    'idruangan' => $item->idruangan,
                    'namaruangan' => $item->namaruangan,
                    'jambuka' => $item->jambuka,
                    'jamtutup' => $item->jamtutup,
                    'quota' => (float) $item->quota,
                    'totaljam' => (float) $item->totaljam,
                    'interval' => $waktuPerorang,
                );
            }
        }

        $dataReservasi = \DB::table('perjanjianpasientr as apr')
            ->select('apr.norec', 'apr.tanggalreservasi')
            ->whereRaw("format(apr.tanggalreservasi,'yyyy-MM-dd') = '$tglAwal'")
            //			->where(" format(apr.tanggalreservasi,'yyyy-MM-dd') <= '$tglAkhir'")
            //			->where('apr.objectruanganfk', $kode)
            ->where('apr.noreservasi', '!=', '-')
            ->whereNotNull('apr.noreservasi')
            ->where('apr.statusenabled', true)
            ->get();


        $result = array(
            'slotting' => $slot,
            'reservasi' => $dataReservasi,
            'as' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getLiburSlotting(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        $data = \DB::table('slottingliburmt')
            ->select(DB::raw("to_char(tgllibur,'yyyy-MM-dd') as tgllibur,id,aktif"))
            ->where('aktif', true)
            ->where('koders', $kdProfile)
            ->orderBy('tgllibur');
        if (isset($request['tgllibur']) && $request['tgllibur'] != '') {
            $tgl = $request['tgllibur'];
            $data = $data->whereRaw("to_char(tgllibur,'yyyy-MM-dd') ='$tgl'");
        }

        $data = $data->get();

        $result = array(

            'libur' => $data,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }
    public function getRincianPelayanan($noRegister)
    {
        //        $pasienDaftar = PasienDaftar::where('noregistrasi', $noRegister)->first();
        $kdProfile = $this->getDataKdProfile($request);
        //        $pelayanan = $this->getPelayananPasienByNoRegistrasi($noRegister);
        $pelayanan = DB::select(
            DB::raw("select pd.objectruanganlastfk,pd.nostruklastfk,ps.id as psid,ps.nocm,
            ps.namapasien,pd.tglpulang,kps.kelompokpasien,kl.namakelas,
            pd.objectruanganlastfk,ru.objectdepartemenfk,
            pd.noregistrasi,pd.tglregistrasi,ru.namaruangan,
            pp.*

            from pasiendaftar_t pd
            left JOIN antrianpasiendiperiksa_t apd on apd.noregistrasifk=pd.norec
            left JOIN pelayananpasien_t pp on pp.noregistrasifk=apd.norec
            left JOIN pasien_m ps on ps.id=pd.nocmfk
            left JOIN kelas_m kl on kl.id=pd.objectkelasfk
            left JOIN kelompokpasien_m kps on kps.id=pd.objectkelompokpasienlastfk
            left JOIN ruangan_m ru on ru.id=pd.objectruanganlastfk
            where pd.noregistrasi=:noregistrasi
            and pd.kdprofile=$kdProfile
            --and pp.strukfk is null;
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );

        $pelayanantidakterklaim = DB::select(
            DB::raw("select pd.objectruanganlastfk,pd.nostruklastfk,ps.id as psid,ps.nocm,
            ps.namapasien,pd.tglpulang,kps.kelompokpasien,kl.namakelas,
            pd.objectruanganlastfk,ru.objectdepartemenfk,
            pd.noregistrasi,pp.* from pasiendaftar_t pd
            INNER JOIN antrianpasiendiperiksa_t apd on apd.noregistrasifk=pd.norec
            INNER JOIN pelayananpasientidakterklaim_t pp on pp.noregistrasifk=apd.norec
            INNER JOIN pasien_m ps on ps.id=pd.nocmfk
            INNER JOIN kelas_m kl on kl.id=pd.objectkelasfk
            INNER JOIN kelompokpasien_m kps on kps.id=pd.objectkelompokpasienlastfk
            INNER JOIN ruangan_m ru on ru.id=pd.objectruanganlastfk
            where pd.noregistrasi=:noregistrasi
               and pd.kdprofile=$kdProfile
            --and pp.strukfk is null;
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );
        //        $pelayanan=$pelayanan[0];
        //        $billing = $this->getBillingFromPelayananPasien($pelayanan);
        $totalBilling = 0;
        $totalKlaim = 0;
        $totalDeposit = 0;
        $totaltakterklaim = 0;

        foreach ($pelayanantidakterklaim as $values) {
            //            if ($values->produkfk == $this->getProdukIdDeposit()) {
            //                $totalDeposit = $totalDeposit + $values->hargajual;
            //            } else {
            $totaltakterklaim = $totaltakterklaim + (($values->hargajual - $values->hargadiscount) * $values->jumlah) + $values->jasa;
            //            }
        }

        foreach ($pelayanan as $value) {
            if ($value->produkfk == $this->getProdukIdDeposit()) {
                $totalDeposit = $totalDeposit + $value->hargajual;
            } else {
                $totalBilling = $totalBilling + (($value->hargajual - $value->hargadiscount) * $value->jumlah) + $value->jasa;
            }
        }

        //        $billing = new \stdClass();
        //        $billing->totalBilling = $totalBilling;
        //        $billing->totalKlaim= $totalKlaim;
        //        $billing->totalDeposit = $totalDeposit;

        $totalBilling = $totalBilling;
        //        $isRawatInap  = $this->isPasienRawatInap2($pelayanan);
        $pelayanan = $pelayanan[0];
        $isRawatInap = false;
        if ($pelayanan->objectruanganlastfk != null) {
            if ((int)$pelayanan->objectdepartemenfk == 16) {
                $isRawatInap = true;
            }
        }


        $dataTotaldibayar = DB::select(
            DB::raw("select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                from pasiendaftar_t as pd
                INNER JOIN antrianpasiendiperiksa_t as apd on apd.noregistrasifk=pd.norec
                INNER JOIN pelayananpasien_t as pp on pp.noregistrasifk=apd.norec
                INNER JOIN strukpelayanan_t as sp on sp.norec=pp.strukfk
                where  pd.noregistrasi=:noregistrasi and sp.nosbmlastfk is not null and pp.produkfk not in (402611)
                   and pd.kdprofile=$kdProfile;
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );
        $dibayar = 0;
        $dibayar = $dataTotaldibayar[0]->total;

        $totalDeposit = $totalDeposit;
        $totalKlaim = 0;
        $result = array(
            'pasienID' => $pelayanan->psid,
            'noCm' => $pelayanan->nocm,
            'noRegistrasi' => $pelayanan->noregistrasi,
            'namaPasien' => $pelayanan->namapasien,
            'tglPulang' => $pelayanan->tglpulang,
            'jenisPasien' => $pelayanan->kelompokpasien,
            'kelasRawat' => $pelayanan->namakelas,
            'tglRegistrasi' => $pelayanan->tglregistrasi,
            'ruangan' => $pelayanan->namaruangan,
            'noAsuransi' => '-', //ambil dari asuransi pasien -m tapi datanya blum ada brooo..
            'kelasPenjamin' => '-', //ini blum ada datanya gimana mau munculin,, gila yaa ?
            'billing' => $totalBilling,
            'penjamin' => '', //$penjamin=$this->getPenjamin($pelayanan)->namarekanan,
            'deposit' => $totalDeposit, //ngambil dari mana
            'totalKlaim' => $totalKlaim, //ngambil dari mana? dihitunga gak
            'jumlahBayar' => $dibayar, //$totalBilling - $totalDeposit - $totalKlaim, //jumlah bayar ini perlu gak
            'jumlahBayarNew' =>  $totalBilling - $totalDeposit - $totalKlaim - $totaltakterklaim, //jumlah bayar dengan tindakan yang tidak d klaim
            'jumlahPiutang' => 0, //ini ngambil dari pembayaran gak ?
            'needDokument' => true, //ini ngambil ddokument dari mana ? pake datafixed
            'dokuments' => [], // sama ini juga ngambilnya dari mana ..
            'totaltakterklaim' => $totaltakterklaim,
            'isRawatInap' => $isRawatInap,
        );
        return $this->respond($result);
    }
    public function getSetting(Request $request)
    {
        $data = \DB::table('settingdatafixedmt')
            ->select('nilaifield')
            ->where('aktif', true)
            ->where('namafield', $request['namaField'])
            ->first();

        return $this->respond($data->nilaifield);
    }
    public  function cekReservasiDipoliYangSama(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $tgl = $request['tglReservasi'];
        if ($request['tipePasien'] == 'baru') {
            $tglLahir = $request['tglLahir'];
            $dataReservasi = \DB::table('perjanjianpasientr as apr')
                ->select('apr.norec', 'apr.tanggalreservasi')
                ->whereRaw("format(apr.tanggalreservasi,'yyyy-MM-dd' )= '$tgl'")
                //                    ->where('apr.objectruanganfk', $request['ruanganId'])
                ->where('apr.noreservasi', '!=', '-')
                ->where('apr.namapasien', $request['namaPasien'])
                ->whereRaw("to_char(apr.tgllahir,'yyyy-MM-dd') = '$tglLahir'")
                ->whereNotNull('apr.noreservasi')
                ->where('apr.aktif', true)
                ->where('apr.koders', $kdProfile)
                ->where('apr.kelompokpasienidfk', 2)
                ->get();
        } else {
            $dataReservasi = \DB::table('perjanjianpasientr as apr')
                ->join('pasienmt as ps', 'ps.id', '=', 'apr.normidfk')
                ->select('apr.norec', 'apr.tanggalreservasi', 'ps.norm as nocm')
                ->whereRaw("to_char(apr.tanggalreservasi,'yyyy-MM-dd') = '$tgl'")
                //                    ->where('apr.objectruanganfk', $request['ruanganId'])
                ->where('apr.noreservasi', '!=', '-')
                ->where('ps.norm', $request['noCm'])
                ->whereNotNull('apr.noreservasi')
                ->where('apr.koders', $kdProfile)
                ->where('apr.aktif', true)
                ->where('apr.kelompokpasienidfk', 2)
                ->get();
        }

        $result = array(
            'data' =>  $dataReservasi,
            'msg' => 'Xoxo'
        );
        return $this->respond($result);
    }
    public function getTagihanEbilling($noregistasi, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $pelayanan = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('transaksipasientr as pp', 'pp.daftarpasienruanganfk', '=', 'apd.norec')
            ->leftjoin('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
            ->join('kelasmt as kl', 'kl.id', '=', 'apd.kelasidfk')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->leftjoin('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
            ->leftjoin('strukbuktipenerimaantr as sbm', 'sp.nosbmlastidfk', '=', 'sbm.norec')
            ->leftjoin('transaksireseptr as sre', 'sre.norec', '=', 'pp.strukresepidfk')
            ->select(
                'pp.norec',
                'pp.tglpelayanan',
                'pp.rke',
                'pr.id as prid',
                'pr.namaproduk',
                'pp.jumlah',
                'kl.id as klid',
                'kl.namakelas',
                'ru.id as ruid',
                'ru.namaruangan',
                'pp.produkidfk as produkfk',
                'pp.hargajual',
                'pp.hargadiscount',
                'sp.nostruk',
                'sp.tglstruk',
                'apd.norec as norec_apd',
                'sbm.nosbm',
                'sp.norec as norec_sp',
                'pp.jasa',
                'pd.normidfk as nocmfk',
                'pd.nostruklastidfk as nostruklastfk',
                'pd.noregistrasi',
                'pd.tglregistrasi',
                'pd.norec as norec_pd',
                'pd.tglpulang',
                'pd.rekananidfk as rekananid',
                'pp.jasa',
                'sp.totalharusdibayar',
                'sp.totalprekanan',
                'sp.totalbiayatambahan',
                'pp.aturanpakai',
                'pp.iscito',
                'pd.statuspasien',
                'pp.isparamedis',
                'pp.strukresepidfk as strukresepfk'
            )
            ->where('pd.koders', $kdProfile)
            ->where('pd.noregistrasi', $noregistasi);
        //          ->orderBy('pp.tglpelayanan', 'pp.rke');

        $pelayanan = $pelayanan->get();
        $details = [];
        if (count($pelayanan) > 0) {
            $details = array();
            foreach ($pelayanan as $value) {
                if ($value->prid != $this->getProdukIdDeposit()) {
                    $jasa = 0;
                    if (isset($value->jasa) && $value->jasa != "" && $value->jasa != null) {
                        $jasa = (float) $value->jasa;
                    }

                    $harga = (float)$value->hargajual;
                    $diskon = (float)$value->hargadiscount;
                    $detail = array(
                        'norec' => $value->norec,
                        'tglPelayanan' => $value->tglpelayanan,
                        'namaPelayanan' => $value->namaproduk,
                        'jumlah' => (float)$value->jumlah,
                        'kelasTindakan' => @$value->namakelas,
                        'ruanganTindakan' => @$value->namaruangan,
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'total' => (($harga - $diskon) * $value->jumlah) + $jasa,
                        'strukfk' => $value->nostruk,
                        'sbmfk' => $value->nosbm,
                        'pgid' => '',
                        'ruid' => $value->ruid,
                        'prid' => $value->prid,
                        'klid' => $value->klid,
                        'norec_apd' => $value->norec_apd,
                        'norec_pd' => $value->norec_pd,
                        'norec_sp' => $value->norec_sp,
                        'jasa' => $jasa,
                        'aturanpakai' => $value->aturanpakai,
                        'iscito' => $value->iscito,
                        'isparamedis' => $value->isparamedis,
                        'strukresepfk' => $value->strukresepfk
                    );

                    $details[] = $detail;
                }
            }
        }

        $arrHsil = array(
            'details' => $details,
            'deposit' =>  $this->getDepositPasien($noregistasi),
            'totalklaim' =>  $this->getTotalKlaim($noregistasi, $kdProfile),
            'bayar' =>  $this->getTotolBayar($noregistasi, $kdProfile),
        );
        return $this->respond($arrHsil);
    }

    public  function getTotalKlaim($noregistrasi, $kdProfile)
    {
        $pelayanan = collect(\DB::select("select sum(x.totalppenjamin) as totalklaim
         from (select spp.norec,spp.totalppenjamin
         from registrasipasientr as pd
            join daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
            join transaksipasientr as pp on pp.daftarpasienruanganfk =apd.norec
            join strukpelayanantr as sp on sp.norec= pp.strukfk
            join strukpelayananpenjamintr as spp on spp.nostrukidfk=sp.norec
            where pd.noregistrasi ='$noregistrasi'
        and spp.aktif is null
        and pd.koders=$kdProfile
        GROUP BY spp.norec,spp.totalppenjamin

        ) as x"))->first();
        if (!empty($pelayanan) && $pelayanan->totalklaim != null) {
            return (float) $pelayanan->totalklaim;
        } else {
            return 0;
        }
    }
    public function getTotolBayar($noregistrasi, $kdProfile)
    {
        $pelayanan = collect(\DB::select("select sum(x.totaldibayar) as totaldibayar
         from (select sbm.norec,sbm.totaldibayar
         from registrasipasientr as pd
        join daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
        join transaksipasientr as pp on pp.daftarpasienruanganfk =apd.norec
        join strukpelayanantr as sp on sp.norec= pp.strukfk
        join strukbuktipenerimaantr as sbm on sbm.nostrukidfk = sp.norec
        where pd.noregistrasi ='$noregistrasi'
        and sp.aktif is null
        and sbm.aktif =true
        and pd.koders=$kdProfile
        GROUP BY sbm.norec,sbm.totaldibayar

        ) as x"))->first();
        if (!empty($pelayanan) && $pelayanan->totaldibayar != null) {
            return (float) $pelayanan->totaldibayar;
        } else {
            return 0;
        }
    }
    public function getNomorRekening(Request $request)
    {
        $data = \DB::table('bankaccountmt')
            ->select('*')
            ->where('aktif', true)
            ->get();

        $result  = array(
            'data' => $data,
            'as' => 'Xoxo'
        );
        return $this->respond($result);
    }

    public function UpdateStatConfirm(Request $request)
    {
        //        $data=$request['data'];
        //        return $this->respond($data);
        try {
            //            foreach ($data as $item) {
            $dataApr = PasienPerjanjian::where('noreservasi', $request['noreservasi'])
                ->update([
                    'isconfirm' => true,
                    //                        'objectstatusbarang'=> 2
                ]);
            //            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Update Status Gagal";
        }


        if ($transStatus == 'true') {
            $ReportTrans = "Update Status Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                //                    "as" => 'cepot',
            );
        } else {
            $ReportTrans = "Update Status Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                //                    "as" => 'Cepot',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function updateNoCmInAntrianRegistrasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        try {

            $dataApr = DaftarRuanganPasien::where('norec', $request['norec'])
                ->update([
                    'normidfk' => $request['nocmfk'],
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }


        if ($transStatus == 'true') {
            $ReportTrans = "Update Reconfirm";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            $ReportTrans = "Update Reconfirm Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function  getPasienByNoRegistrasi($noregistrasi, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('pasiendaftar_t as pd')
            ->leftjoin('pasien_m as ps', 'ps.id', '=', 'pd.nocmfk')
            ->leftjoin('ruangan_m as ru', 'ru.id', '=', 'pd.objectruanganlastfk')
            ->leftjoin('kelas_m as kls', 'kls.id', '=', 'pd.objectkelasfk')
            ->leftjoin('kelompokpasien_m as kps', 'kps.id', '=', 'pd.objectkelompokpasienlastfk')
            ->leftjoin('rekanan_m as rk', 'rk.id', '=', 'pd.objectrekananfk')
            ->leftjoin('jeniskelamin_m as jk', 'jk.id', '=', 'ps.objectjeniskelaminfk')
            ->leftjoin('alamat_m as alm', 'alm.id', '=', 'pd.nocmfk')
            ->leftjoin('agama_m as agm', 'agm.id', '=', 'ps.objectagamafk')
            ->select(
                'pd.norec as norec_pd',
                'pd.noregistrasi',
                'pd.tglregistrasi',
                'ps.norm as nocm',
                'ps.namapasien',
                'ps.tgllahir',
                'ps.namakeluarga',
                'ru.namaruangan',
                'kls.namakelas',
                'kps.kelompokpasien',
                'rk.namarekanan',
                'alm.alamatlengkap',
                'jk.jeniskelamin',
                'agm.agama',
                'ps.nohp',
                'pd.statuspasien',
                'pd.tglpulang'
            )
            ->where('pd.noregistrasi', $noregistrasi)
            ->where('pd.kdprofile',   $kdProfile)
            ->first();

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDaftarRiwayatRegistrasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('pasienmt as ps')
            ->join('registrasipasientr as pd', 'pd.normidfk', '=', 'ps.id')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'pd.pegawaiidfk')
            //            ->leftJoin('batalregistrasi_t as br','br.pasiendaftarfk','=','pd.norec')
            ->select(DB::raw("pd.tglregistrasi,ps.norm as nocm,pd.noregistrasi,ps.namapasien,pd.ruanganlastidfk as  objectruanganlastfk,ru.namaruangan,
			                  pd.pegawaiidfk as  objectpegawaifk,pg.namalengkap as namadokter,pd.tglpulang,ru.instalasiidfk as objectdepartemenfk,
			                  CASE when pd.tglpulang <> pd.tglregistrasi then 1 else 0 end as statusinap,ps.tgllahir"))
            ->where('pd.aktif', true)
            ->where('pd.koders',   $kdProfile);

        //        if(isset($request['tglLahir']) && $request['tglLahir']!="" && $request['tglLahir']!="undefined"){
        //            $data = $data->where('ps.tgllahir','>=', $request['tglLahir'].' 00:00');
        //        };
        //        if(isset($request['tglLahir']) && $request['tglLahir']!="" && $request['tglLahir']!="undefined"){
        //            $data = $data->where('ps.tgllahir','<=', $request['tglLahir'].' 23:59');
        //        };
        if (isset($request['norm']) && $request['norm'] != "" && $request['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['norm'] . '%');
        };
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['namaPasien'] . '%');
        };
        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $request['noReg']);
        };
        if (isset($request['idRuangan']) && $request['idRuangan'] != "" && $request['idRuangan'] != "undefined") {
            $data = $data->where('pd.ruanganlastidfk', '=', $request['idRuangan']);
        };

        $data = $data->where('ps.aktif', true);
        $data = $data->orderBy('pd.tglregistrasi');
        $data = $data->get();
        $result = array(
            'daftar' => $data,
            'message' => 'godU',
        );
        return $this->respond($result);
    }
    public function cekPasienByNik($nik)
    {
        $data =  Pasien::where('noidentitas', $nik)
            ->where('aktif', true)->get();

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function saveLibur(Request $request)
    {

        DB::beginTransaction();
        try {
            $tgl = [];
            foreach ($request['listtanggal'] as $key => $value) {
                $tgl[] = $value['tgl'];
            }
            $del = SlottingLibur::whereIn('tgllibur', $tgl)->delete();
            foreach ($request['listtanggal'] as $key => $value) {

                $newptp = new SlottingLibur();
                $newptp->id = SlottingLibur::max('id') + 1;
                $newptp->statusenabled = true;
                $newptp->kdprofile = 11;
                $newptp->tgllibur = $value['tgl'];
                $newptp->save();
            }


            $ReportTrans = "Simpan Libur";
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Simpan Libur Gagal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "data" => $newptp,
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            DB::rollBack();
            $result = array(
                //              "noRec" =>$noRec,
                "status" => 400,
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function deleteLibur(Request $request)
    {

        DB::beginTransaction();
        try {

            $newptp = SlottingLibur::where('id', $request['id'])->update(
                ['statusenabled' => false]
            );

            $ReportTrans = "Hapus Libur";
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Hapus Libur Gagal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                // "data" =>$newptp,
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            DB::rollBack();
            $result = array(
                //              "noRec" =>$noRec,
                "status" => 400,
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getLibur(Request $request)
    {
        $data = \DB::table('slottinglibur_m as ps')
            ->select('ps.*')
            ->where('ps.statusenabled', true);

        if (isset($request['tgllibur']) && $request['tgllibur'] != "" && $request['tgllibur'] != "undefined") {
            $tgls = $request['tgllibur'];
            $data = $data->whereRaw("format(ps.tgllibur,'yyyy-MM-dd')= 'tgls'");
        };
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['namaPasien'] . '%');
        };
        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $request['noReg']);
        };
        if (isset($request['idRuangan']) && $request['idRuangan'] != "" && $request['idRuangan'] != "undefined") {
            $data = $data->where('pd.objectruanganlastfk', '=', $request['idRuangan']);
        };


        $data = $data->orderBy('ps.id');
        $data = $data->get();
        $result = array(
            'daftar' => $data,
            'message' => 'godU',
        );
        return $this->respond($result);
    }
    public function GetNoAntrianMobileJKN(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
    
    //       $request =  array(
    //           "nomorkartu" => "0000172381691",
    //           "nik" => "3372051109800010",
    //           "notelp" => "085642649135",
    //           "tanggalperiksa" => "2020-10-09",
    //           "kodepoli" => "JIW",
    //           "nomorreferensi" => "0001R0040116A000001",
    //           "jenisreferensi" => "1",
    //           "jenisrequest" => "2",
    //           "polieksekutif" => "0"
    //       );
    
        $request = $request->json()->all();
    //        print_r($request);
    //        exit();
    
        if(empty($request['nomorkartu']) ) {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Nomor Kartu BPJS tidak boleh kosong"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
    
        if(empty($request['tanggalperiksa'])) {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Tanggal Periksa tidak boleh kosong"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
    
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$request['tanggalperiksa'])) {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Format Tanggal Periksa salah"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
    
        if($request['tanggalperiksa'] >= date('Y-m-d',strtotime('+90 days',strtotime(date('Y-m-d'))))){
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Tanggal periksa maksimal 90 hari dari hari ini"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
        if($request['tanggalperiksa'] == date('Y-m-d')){
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Tanggal periksa minimal besok"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
        if($request['tanggalperiksa'] < date('Y-m-d')){
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Tanggal periksa minimal besok"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
    //        return date('w',strtotime( $request['tanggalperiksa'] ) );
        if(date('w',strtotime( $request['tanggalperiksa'] )) == 0 ){
            $result = array("response"=>null,
                "metadata"=>array("code" => "400",
                    "message" => "Tidak ada jadwal Poli di hari Minggu")
            );
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
    //        return  date('w',strtotime( $request['tanggalperiksa'] ));
    //
    //        function isWeekend($date) {
    //            $weekDay = date('w', strtotime($date));
    //            return ($weekDay == 0
    ////                || $weekDay == 6
    //            );
    //        }
        if(empty($request['nik']) ) {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "NIK tidak boleh kosong"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
        if(empty($request['kodepoli']) ) {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Kodepoli tidak boleh kosong"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }else{
    //            if($request['kodepoli'] != "JIW" ) {
    //                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Kodepoli tidak sesuai"));
    //                return $this->setStatusCode($result['metadata']['code'])->respond($result);
    //            }
        }
    
        if(empty($request['jenisreferensi']) ) {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Jenis Referensi tidak boleh kosong"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }else{
            if($request['jenisreferensi'] < "1" || $request['jenisreferensi'] > "2") {
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Jenis Referensi tidak sesuai"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
        }
    
        if(empty($request['jenisrequest']) ) {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Jenis Request tidak boleh kosong"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }else{
            if($request['jenisrequest'] < "1" || $request['jenisrequest'] > "2") {
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Jenis Request tidak sesuai"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
        }
    
        if(empty($request['polieksekutif']) && $request['polieksekutif'] != "0") {
            $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Poli Eksekutif tidak boleh kosong"));
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }else{
            if($request['polieksekutif'] < "0" || $request['polieksekutif'] > "1") {
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Poli Eksekutif tidak sesuai"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
        }
    
    
    //        return $antrian;
        $eksek = false;
        if($request['polieksekutif'] == 1){
            $eksek = true;
        }
        if($request['jenisrequest']  == '2') {//POLI
            DB::beginTransaction();
            try {
    
                $antrian = $this->GetJamKosong($request['kodepoli'], $request['tanggalperiksa'], $kdProfile,$eksek);
                $pasien = \DB::table('pasien_m')
                    ->whereRaw("nobpjs = '" . $request['nomorkartu'] . "'")
                    ->where('statusenabled', true)
                    ->where('kdprofile', $kdProfile)
                    ->first();
    
                $ruang = Ruangan::where('kdinternal', $request['kodepoli'])
                    ->where('statusenabled', true)
                    ->where('iseksekutif',$eksek)
                    ->where('kdprofile', $kdProfile)->first();
                if (empty($ruang)) {
                    $result = array("response" => null, "metadata" => array("code" => "400", "message" => "Kodepoli tidak sesuai"));
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
                if (empty($pasien)) {
    //                    return $this->respond($pasien);
                    $request['jenisrequest']  ='1';
                    DB::commit();
                    return  $this->postPendaftaranJKN($request,$kdProfile);
    //                    $pro = Profile::where('id', $kdProfile)->first();
    //                    $result = array(
    //                        "response" => null,
    //                        "metadata" => array("code" => "400", "message" => "Belum terdaftar sebagai pasien " . $pro->namaexternal)
    //                    );
    //                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
    
                $tipepembayaran = '2';
                $tgl = $request['tanggalperiksa'];
    
                $dataReservasi = \DB::table('antrianpasienregistrasi_t as apr')
                    ->select('apr.noantrian', 'apr.noreservasi', 'ru.namaexternal', 'apr.tanggalreservasi')
                    ->join('ruangan_m as ru', 'ru.id', '=', 'apr.objectruanganfk')
    //                        ->whereRaw("apr.tanggalreservasi BETWEEN '$tgl' AND '" . date('Y-m-d', strtotime('+1 day', strtotime($tgl))) . "'")
                    ->whereRaw("to_char(apr.tanggalreservasi,'yyyy-MM-dd')= '$tgl'")
                    ->where('apr.objectruanganfk', '=', $ruang->id)
                    ->where('apr.noreservasi', '!=', '-')
                    ->where('apr.noidentitas', '=', $request['nik'])
                    ->where('apr.nobpjs', '=', $request['nomorkartu'])
                    ->whereNotNull('apr.noreservasi')
                    ->where('apr.statusenabled', true)
                    ->first();
    //                return $this->respond($dataReservasi);
                if (isset($dataReservasi) && !empty($dataReservasi)) {
                    $result = array("response" => null, "metadata" => array("code" => "400", "message" => "Mohon maaf anda sudah mendaftar pada tanggal " . $tgl));
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
    
                $newptp = new AntrianPasienRegistrasi();
                $nontrian = AntrianPasienRegistrasi::max('noantrian') + 1;
                $newptp->norec = $newptp->generateNewId();;
                $newptp->kdprofile = $kdProfile;
                $newptp->statusenabled = true;
                $newptp->objectruanganfk = $ruang->id;
                $newptp->objectjeniskelaminfk = $pasien->objectjeniskelaminfk;
                $newptp->noreservasi = substr(Uuid::generate(), 0, 7);
                $newptp->tanggalreservasi = $request['tanggalperiksa'] . " " . $antrian['jamkosong'];
                $newptp->tgllahir = $pasien->tgllahir;
                $newptp->objectkelompokpasienfk = $tipepembayaran;
                $newptp->objectpendidikanfk = 0;
                $newptp->namapasien = $pasien->namapasien;
                $newptp->noidentitas = $request['nik'];
                $newptp->tglinput = date('Y-m-d H:i:s');
                $newptp->nobpjs = $request['nomorkartu'];
                $newptp->norujukan = $request['nomorreferensi'];
                $newptp->notelepon = $pasien->nohp;
                $newptp->objectpegawaifk = null;
                $newptp->tipepasien = "LAMA";
                $newptp->ismobilejkn = 1;
                $newptp->type = "LAMA";
    
                $newptp->objectagamafk = $pasien->objectagamafk;
                $alamat = Alamat::where('nocmfk', $pasien->id)->first();
                if (!empty($alamat)) {
                    $newptp->alamatlengkap = $alamat->alamatlengkap;
                    $newptp->objectdesakelurahanfk = $alamat->objectdesakelurahanfk;
                    $newptp->negara = $alamat->objectnegarafk;
                }
                $newptp->objectgolongandarahfk = $pasien->objectgolongandarahfk;
                $newptp->kebangsaan = $pasien->objectkebangsaanfk;
                $newptp->namaayah = $pasien->namaayah;
                $newptp->namaibu = $pasien->namaibu;
                $newptp->namasuamiistri = $pasien->namasuamiistri;
    
                $newptp->noaditional = $pasien->noaditional;
                $newptp->noantrian = $antrian['antrian'];
                $newptp->noidentitas = $pasien->noidentitas;
                $newptp->nocmfk = $pasien->id;
                $newptp->paspor = $pasien->paspor;
                $newptp->objectpekerjaanfk = $pasien->objectpekerjaanfk;
                $newptp->objectpendidikanfk = $pasien->objectpendidikanfk != null ? $pasien->objectpendidikanfk : 0;
                $newptp->objectstatusperkawinanfk = $pasien->objectstatusperkawinanfk;
                $newptp->tempatlahir = $pasien->tempatlahir;
    
                $newptp->save();
                $transStatus = 'true';
    
                $transMessage = "Ok";
            } catch (Exception $e) {
                $transMessage = "Gagal Reservasi";
                $transStatus = 'false';
            }
    
            if ($transStatus != 'false') {
                DB::commit();
    //                        $dataHasil = \DB::table('antrianpasienregistrasi_t as apr')
    //                            ->select('apr.noantrian', 'apr.noreservasi', 'ru.namaexternal', 'apr.tanggalreservasi')
    //                            ->join('ruangan_m as ru', 'ru.id', '=', 'apr.objectruanganfk')
    //                            ->whereRaw("apr.tanggalreservasi BETWEEN '$tgl' AND '" . date('Y-m-d', strtotime('+1 day', strtotime($tgl))) . "'")
    //                            ->where('apr.objectruanganfk', '=', $ruang->id)
    //                            ->where('apr.noreservasi', '!=', '-')
    //                            ->where('apr.noidentitas', '=', $request['nik'])
    //                            ->where('apr.nobpjs', '=', $request['nomorkartu'])
    //                            ->whereNotNull('apr.noreservasi')
    //                            ->where('apr.statusenabled', true)
    //                            ->first();
    
                $estimasidilayani = strtotime($newptp->tanggalreservasi) * 1000;
                $result = array(
                    "response" => array(
                        "nomorantrean" => $newptp->noantrian,
                        "kodebooking" => $newptp->noreservasi,
                        "jenisantrean" => '2',
                        "estimasidilayani" => $estimasidilayani,
                        "namapoli" => $ruang->namaexternal,
                        "namadokter" => '',
    
                    ),
                    "metadata" => array(
                        "code" => "200",
                        "message" => $transMessage)
                );
            }else{
                DB::rollBack();
                $result = array(
                    "response" => null,
                    "metadata" => array(
                        "code" => "200",
                        "message" => $transMessage)
                );
    
            }
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
    
        }else{
            /*
             * jenis reqeust 1 //PENDAFTARAN
             */
            return $this->postPendaftaranJKN($request,$kdProfile);
    
        }
    }
        public function  postPendaftaranJKN($request,$kdProfile){
            $eksek = false;
            if($request['polieksekutif'] == 1){
                $eksek = true;
            }
            DB::beginTransaction();
            try {
                $pasien = \DB::table('pasien_m')
                    ->whereRaw("nobpjs = '" . $request['nomorkartu'] . "'")
                    ->where('statusenabled', true)
                    ->where('kdprofile', $kdProfile)
                    ->first();
                $ruang = Ruangan::where('kdinternal', $request['kodepoli'])
                    ->where('statusenabled', true)
                    ->where('iseksekutif',$eksek)
                    ->where('kdprofile', $kdProfile)->first();
                if (empty($ruang)) {
                    $result = array("response" => null, "metadata" => array("code" => "400", "message" => "Kodepoli tidak sesuai"));
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
    
                $tgl = $request['tanggalperiksa'];
    
                $dataReservasi = \DB::table('antrianpasienregistrasi_t as apr')
                    ->select('apr.noantrian', 'apr.noreservasi', 'ru.namaexternal', 'apr.tanggalreservasi')
                    ->join('ruangan_m as ru', 'ru.id', '=', 'apr.objectruanganfk')
    //                        ->whereRaw("apr.tanggalreservasi BETWEEN '$tgl' AND '" . date('Y-m-d', strtotime('+1 day', strtotime($tgl))) . "'")
                    ->whereRaw("to_char(apr.tanggalreservasi,'yyyy-MM-dd')= '$tgl'")
                    ->where('apr.objectruanganfk', '=', $ruang->id)
                    ->where('apr.noreservasi', '=', '-')
                    ->where('apr.noidentitas', '=', $request['nik'])
                    ->where('apr.nobpjs', '=', $request['nomorkartu'])
                    ->whereNotNull('apr.noreservasi')
                    ->where('apr.statusenabled', true)
                    ->first();
    //                return $this->respond($dataReservasi);
                if (isset($dataReservasi) && !empty($dataReservasi)) {
                    $nomor = str_pad($dataReservasi->noantrian, 3, '0', STR_PAD_LEFT);
                    $result = array("response" => null, "metadata" => array("code" => "400",
                        "message" => "Mohon maaf anda sudah mendaftar pada tanggal " . $tgl ." No Antrean : B-".$nomor));
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
                $tglAyeuna = $request['tanggalperiksa'] . date(' H:i:s');
    
                $tglAwal = $request['tanggalperiksa'] .' 00:00:00';
                $tglAkhir =$request['tanggalperiksa'] .' 23:59:59';
                $kdRuanganTPP = $this->settingDataFixed('idRuanganTPP1',$kdProfile);
    
                $newptp = new AntrianPasienRegistrasi();
                $norec = $newptp->generateNewId();
                $nontrian = AntrianPasienRegistrasi::where('jenis', 'B')
                        ->whereBetween('tanggalreservasi', [$tglAwal, $tglAkhir])
                        ->max('noantrian') + 1;
                //                return $nontrian;
                $newptp->norec = $norec;
                $newptp->kdprofile = $kdProfile;
                $newptp->statusenabled = true;
                $newptp->objectruanganfk =  $ruang->id;//$kdRuanganTPP;
                $newptp->objectjeniskelaminfk = 0;
                $newptp->noantrian = $nontrian;
                $newptp->noreservasi = "-";
                $newptp->objectpendidikanfk = 0;
    
                $newptp->namapasien = !empty($pasien) ? $pasien->namapasien : null;
                $newptp->noidentitas = $request['nik'];
                $newptp->tglinput = date('Y-m-d H:i:s');
                $newptp->nobpjs = $request['nomorkartu'];
                $newptp->norujukan = $request['nomorreferensi'];
                $newptp->notelepon = !empty($pasien) ? $pasien->nohp : null;
                $newptp->nocmfk = !empty($pasien) ? $pasien->id : null;
    
                $newptp->tanggalreservasi = $tglAyeuna;
                $newptp->tipepasien = "BARU";
                $newptp->type = substr(Uuid::generate(), 0, 7);//"BARU";
                $newptp->jenis = 'B';//BPJS
                $newptp->statuspanggil = 0;
                $newptp->ismobilejkn = 1;
                $newptp->save();
    //                    $nontrian= 2;
                /*
                 * estimasi dilayani 5 menit sekali dari poli buka sesuai antrian
                 */
                $es = date('Y-m-d H:i:s',strtotime('+'. (float) 5 * $nontrian  .' minutes',strtotime($request['tanggalperiksa'].' 08:00:00')));
                //                return $estimasidilayani;
                $estimasidilayani = strtotime($es) * 1000;
                $transStatus = 'true';
                $transMessage = "Ok";
            } catch (Exception $e) {
                $transMessage = "Gagal Reservasi";
                $transStatus = 'false';
            }
            if($transStatus == 'true') {
                DB::commit();
                $nomor = str_pad($newptp->noantrian, 3, '0', STR_PAD_LEFT);
    //                    return $this->respond($nomor);
                $result = array(
                    "response" => array(
                        "nomorantrean" => 'B-'.$nomor,
                        "kodebooking" => $newptp->type,
                        "jenisantrean" => '1', //Pendafaran
                        "estimasidilayani" => $estimasidilayani,
                        "namapoli" => $ruang->namaexternal,
                        "namadokter" => '',
    
                    ),
                    "metadata" => array(
                        "code" => "200",
                        "message" => $transMessage)
                );
            }else{
                $result = array(
                    "response"=>null,
                    "metadata"=>array("code" => "400","message" => "Gagal Reservasi")
                );
            }
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
      
        public function GetJamKosong($kode,$tgl,$kdProfile,$eksek){
            $ruang = Ruangan::where('kdinternal',$kode)
                ->where('aktif',true)
                ->whereRaw(" ( iseksekutif=false or iseksekutif is null ) ")
                ->where('koders',$kdProfile)->first();
           
           if(empty($ruang)){
               $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Kodepoli tidak sesuai"));
               return $this->setStatusCode($result['metadata']['code'])->respond($result);
           }
            $dataReservasi = \DB::table('perjanjianpasientr as apr')
                ->select('apr.norec','apr.tanggalreservasi')
                ->whereRaw(" to_char(apr.tanggalreservasi,'yyyy-MM-dd') = '$tgl'")
                ->where('apr.ruanganidfk', $ruang->id)
                ->where('apr.noreservasi','!=','-')
                ->whereNotNull('apr.noreservasi')
                ->where('apr.aktif',true)
                ->where('apr.koders',$kdProfile)
                 ->whereRaw(" (apr.isbatal = false or apr.isbatal is null)")
                ->orderBy('apr.tanggalreservasi')
                ->get();
        
               
    
            $ruangan = \DB::table('ruanganmt as ru')
                ->join('slottingonlinemt as slot', 'slot.ruanganidfk', '=', 'ru.id')
                ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk', 'slot.jambuka', 'slot.jamtutup',
                    'slot.quota',
                    DB::raw("(EXTRACT(EPOCH FROM slot.jamtutup) - EXTRACT(EPOCH FROM slot.jambuka))/3600 as totaljam"))
                ->where('ru.aktif', true)
                ->where('ru.id',  $ruang->id)
                ->where('ru.koders',$kdProfile)
                ->where('slot.aktif', true)
                ->first();
    
            if(empty($ruangan)){
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Jadwal penuh"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            $begin = new Carbon($ruangan->jambuka);
            $jamBuka = $begin->format('H:i');
            $end = new Carbon($ruangan->jamtutup);
            $jamTutup = $end->format('H:i');
            $quota =(float)$ruangan->quota;
            $waktuPerorang = ((float)$ruangan->totaljam / (float)$ruangan->quota) * 60;
    
            $i =0;
            $slotterisi = 0;
            $jamakhir = '00:00';
            $reservasi = [];
            foreach ($dataReservasi as $items){
                $jamUse =  new Carbon($items->tanggalreservasi);
                $slotterisi += 1;
                $reservasi [] = array(
                    'jamreservasi' => $jamUse->format('H:i')
                );
                $jamakhir = $jamUse->format('H:i');
            }
            /*
             * old
             */
    //        $slotakhir = $quota-$slotterisi;
    //        if($slotakhir > 0){
    //            //$cenvertedTime = date('Y-m-d H:i:s',strtotime('+1 hour +30 minutes +45 seconds',strtotime($startTime)));
    //            $jamkosongpre = date('H:i',strtotime('+'.floor($waktuPerorang)." minutes",strtotime($jamakhir)));
    //            $jamkosongfix = new Carbon($jamkosongpre);
    //            $jamkosongfix = $jamkosongfix->format("H:i");
    //            return array("antrian"=>$slotterisi+1,"jamkosong"=>$jamkosongfix);
    //        }else{
    //            return array("antrian"=>0,"jamkosong"=>"00:00");
    //        }
            /*
            * end old
            */
    
    
    //        return   date('H:i',strtotime('+'.floor($waktuPerorang)." minutes",strtotime($jamTutup)));
            /*
             * slot
             */
            $intervals = [];
            $intervalsAwal  = [];
            $begin = new \DateTime($jamBuka);
            $end = new \DateTime($jamTutup);
            $interval = \DateInterval::createFromDateString(floor($waktuPerorang).' minutes');
        
            $period = new \DatePeriod($begin, $interval, $end);
            foreach ($period as $dt) {
                $intervals[] = array(
                    'jam'=>  $dt->format("H:i")
                );
                $intervalsAwal[] = array(
                    'jam'=>  $dt->format("H:i")
                );
            }

            if(count($intervals) == 0){
                return array("antrian"=> 0,"jamkosong"=>"00:00");
            }
 
            if (count($reservasi) > 0) {
                for ( $j = count($reservasi) - 1; $j >= 0; $j--) {
                    for ( $k =count($intervals)- 1; $k >= 0; $k--) {
                        if ($intervals[$k]['jam'] == $reservasi[$j]['jamreservasi']) {
    //                        this.listJam.splice([i], 1);
                            array_splice($intervals,$k,1);
                        }
                    }
                }
            }
       
            if(count($intervals) > 0){
    
                $antrian = 0;
                for ($x = 0; $x <= count($intervalsAwal); $x++) {
                    if($intervals[0]['jam']== $intervalsAwal[$x]['jam']){
                        $antrian = $x ;
                        // dd($antrian);
                        break;
                    }
                }

                return array("antrian"=> $antrian+1,"jamkosong"=>$intervals[0]['jam']);
            }else{
                return array("antrian"=> 0,"jamkosong"=>"00:00");
            }
    
        }
        public function GetRekapMobileJKN(Request $request)
        {
            $kdProfile = $this->getDataKdProfile($request);
    //        $request =  array(
    //            "tanggalperiksa" => "2020-10-09",
    //            "kodepoli" => "JIW",
    //            "polieksekutif" => "0"
    //        );
    
            $request = $request->json()->all();
    
    
            if(empty($request['tanggalperiksa']) ) {
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Tanggal Periksa tidak boleh kosong"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$request['tanggalperiksa'])) {
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Format Tanggal Periksa salah"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if($request['tanggalperiksa'] >= date('Y-m-d',strtotime('+90 days',strtotime(date('Y-m-d'))))){
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Tanggal periksa maksimal 90 hari dari hari ini"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if(empty($request['kodepoli']) ) {
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Kodepoli tidak boleh kosong"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }else{
    //            if($request['kodepoli'] != "JIW" ) {
    //                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Kodepoli tidak sesuai"));
    //                return $this->setStatusCode($result['metadata']['code'])->respond($result);
    //            }
            }
    
            if(empty($request['polieksekutif']) && $request['polieksekutif'] != "0") {
                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Poli Eksekutif tidak boleh kosong"));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }else{
                if($request['polieksekutif'] < "0" || $request['polieksekutif'] > "1") {
                    $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Poli Eksekutif tidak sesuai"));
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
            }
            $eksek = false;
            if($request['polieksekutif'] == 1){
                $eksek = true;
            }
            try {
                $ruang = Ruangan::where('kdinternal',$request['kodepoli'])
                    ->where('statusenabled',true)
                    ->where('iseksekutif', $eksek)
                    ->where('kdprofile',$kdProfile)->first();
                if(empty($ruang)){
                    $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Kodepoli tidak sesuai"));
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
    //            $ruang = Ruangan::where('kdprofile',$kdProfile)
                $tgl = $request['tanggalperiksa'];
    
                $data = \DB::table('antrianpasienregistrasi_t as apr')
                    ->leftJoin('ruangan_m as ru','ru.id','=','apr.objectruanganfk')
                    ->select('ru.namaexternal','apr.norec','apr.noreservasi')
                    ->where('apr.objectruanganfk','=',$ruang->id)
                    ->whereRaw(" to_char(apr.tanggalreservasi,'yyyy-MM-dd') = '$tgl'")
    //                ->where('apr.noreservasi','!=','-')
                    ->where('apr.ismobilejkn','=','1')
                    ->whereNotNull('apr.noreservasi')
                    ->where('apr.statusenabled',true)
                    ->where('apr.kdprofile',$kdProfile)
                    ->get();
    //            return $this->respond($data);
                if(count($data) > 0){
                    $ruId =$ruang->id;
                    $terlayani = collect(DB::select("SELECT
                            pd.norec,pd.noregistrasi,pd.statusschedule
                        FROM
                            pasiendaftar_t AS pd  
                        LEFT JOIN antrianpasienregistrasi_t AS apr ON apr.noreservasi = pd.statusschedule
                        AND apr.nocmfk = pd.nocmfk
                        where pd.kdprofile=$kdProfile
                        and pd.statusenabled=true
                        and apr.ismobilejkn=true
                        and pd.objectruanganlastfk =$ruId and to_char(pd.tglregistrasi,'yyyy-MM-dd')='$tgl' "))->count();
                    $result = array(
                        "response" =>
                            array(
                                "namapoli" => $data[0]->namaexternal,
                                "totalantrean" => count($data),
                                "jumlahterlayani" => $terlayani,
                                "lastupdate" => $milliseconds = round(microtime(true) * 1000)
                            ),
                        "metadata"=>
                            array(
                                'message' => "OK",
                                'code' => '200',
                            )
                    );
                }else{
                    $result = array(
                        "response" =>
                            null,
                        "metadata"=>
                            array(
                                'message' => "Belum ada data yang bisa ditampilkan",
                                'code' => '400',
                            )
                    );
                }
    
    
            } catch (Exception $e) {
                $result = array(
                    "response" =>
                        null,
                    "metadata"=>
                        array(
                            'message' => "Gagal menampilkan data",
                            'code' => '400',
                        )
                );
            }
            return $this->respond($result);
        }
        public function getKodeBokingOperasi(Request $request)
        {
            $kdProfile = $this->getDataKdProfile($request);
            $request = $request->json()->all();
            if (empty($request['nopeserta'])) {
                $result = array("metadata" => array("message" => "Nomor Kartu Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
        
            if (!is_numeric($request['nopeserta']) || strlen($request['nopeserta']) < 13) {
                $result = array("metadata" => array("message" => "Format Nomor Kartu Tidak Sesuai", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            $depbedah = $this->settingDataFixed('kdDepartemenOK', $kdProfile);
            try {
                $data = DB::select(DB::raw("SELECT
                            so.noorder as kodebooking,
                            so.tglpelayananawal  as tanggaloperasi,
                            pr.namaproduk as jenistindakan,
                            ru.kdinternal as kodepoli,
                                ru.namaexternal AS namapoli,
                            pas.norm as nocm,
                            pd.noregistrasi,pas.nobpjs,
                            so.statusorder,pd.kelompokpasienlastidfk as objectkelompokpasienlastfk
                            
                            FROM
                                transaksiordertr AS so
                            join transaksiorderdetailtr as op on op.transaksiorderfk=so.norec
                            join pelayananmt as pr on pr.id=op.produkidfk
                            LEFT JOIN registrasipasientr AS pd ON pd.norec = so.registrasipasienfk
                            INNER JOIN pasienmt AS pas ON pas.id = pd.normidfk
                            LEFT JOIN ruanganmt AS ru ON ru.id = so.ruanganidfk
                            LEFT JOIN ruanganmt AS ru2 ON ru2.id = so.ruangantujuanidfk
                        WHERE
                            so.koders = $kdProfile
                        --AND pas.nocm ILIKE '%11233764%'
                            and pas.nobpjs='$request[nopeserta]'
                        AND ru2.instalasiidfk = $depbedah
                        AND so.aktif = true
                        and so.statusorder is null
                        and ru.kdinternal is not null
                        and pd.kelompokpasienlastidfk=2
                        ORDER BY
                            so.tglorder desc"));
                $list = [];
                foreach ($data as $k){
                    $list [] = array(
                        'kodebooking' => $k->kodebooking,
                        'tanggaloperasi' => date('Y-m-d',strtotime($k->tanggaloperasi)),
                        'jenistindakan' => $k->jenistindakan,
                        'kodepoli' => $k->kodepoli,
                        'namapoli' => $k->namapoli,
                        'terlaksana' => 0,
                    );
                }
    
                if(count($list) > 0){
                    $result = array(
                        "response" =>
                            array(
                                "list" => $list,
                            ),
                        "metadata"=>
                            array(
                                'code' => 200,
                                'message' => "OK"
                            )
                    );
                }else{
                    $result = array(
                        "metadata"=>
                            array(
                                'code' => 201,
                                'message' => "Belum ada data yang bisa ditampilkan"
                            )
                    );
                }
    
    
            } catch (Exception $e) {
                $result = array(
                    "response" =>
                        null,
                    "metadata"=>
                        array(
                            'code' => 201,
                            'message' => "Gagal menampilkan data"
                        )
                );
            }
            return $this->respond($result);
        }
    
        public function getJadwalOperasi(Request $request)
        {
            $kdProfile = $this->getDataKdProfile($request);
            $request = $request->json()->all();
            if((!isset($request['tanggalawal']) &&  empty($request['tanggalawal']) )
                && (!isset($request['tanggalakhir']) &&  empty($request['tanggalakhir']))) {
                $result = array("metadata"=>array("message" => "Tanggal Awal dan Akhir tidak boleh kosong", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if($request['tanggalawal'] >  $request['tanggalakhir']) {
                $result = array("metadata"=>array("message" => "Tanggal Akhir Tidak Boleh Kecil dari Tanggal Awal ","code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            $depbedah = $this->settingDataFixed('kdDepartemenOK', $kdProfile);
         
            try {
                $data = DB::select(DB::raw("SELECT
                        so.noorder as kodebooking,
                        so.tglpelayananawal  as tanggaloperasi,
                        pr.namaproduk as jenistindakan,
                        ru.kdinternal as kodepoli,
                            ru.namaexternal AS namapoli,
                        pas.norm as nocm,
                        pd.noregistrasi,pas.nobpjs,
                        so.statusorder,pd.kelompokpasienlastidfk as objectkelompokpasienlastfk
                          
                        FROM
                            transaksiordertr AS so
                        join transaksiorderdetailtr as op on op.transaksiorderfk=so.norec
                        join pelayananmt as pr on pr.id=op.produkidfk
                        LEFT JOIN registrasipasientr AS pd ON pd.norec = so.registrasipasienfk
                        INNER JOIN pasienmt AS pas ON pas.id = pd.normidfk
                        LEFT JOIN ruanganmt AS ru ON ru.id = so.ruanganidfk
                        LEFT JOIN ruanganmt AS ru2 ON ru2.id = so.ruangantujuanidfk
                        
                        WHERE
                            so.koders = $kdProfile
                        --AND pas.nocm ILIKE '%11233764%'
                        AND ru2.instalasiidfk = $depbedah
                        AND so.aktif = true
                        --and so.statusorder is null
                        and ru.kdinternal is not null
                        and so.tglpelayananawal between '$request[tanggalawal] 00:00:00' and '$request[tanggalakhir] 23:59:59'
                        ORDER BY
                            so.tglorder desc"));
                $list = [];
                foreach ($data as $k){
                    $stt = $k->statusorder;
                    if( $k->statusorder == null){
                        $stt = 0;
                    }
                    //1 sudah dilaksanakan , 0 belum ,2 batal
    
                    $list [] = array(
                        'kodebooking' => $k->kodebooking,
                        'tanggaloperasi' => date('Y-m-d',strtotime($k->tanggaloperasi)),
                        'jenistindakan' => $k->jenistindakan,
                        'kodepoli' => $k->kodepoli,
                        'namapoli' => $k->namapoli,
                        'terlaksana' => $stt ,
                        'nopeserta' => $k->objectkelompokpasienlastfk != 2 ? '': $k->nobpjs,
                        'lastupdate' => round(microtime(true) * 1000)
                    );
                }
    
                if(count($list) > 0){
                    $result = array(
                        "response" =>
                            array(
                                "list" => $list,
                            ),
                        "metadata"=>
                            array(
                                'message' => "OK",
                                'code' => 200,
                            )
                    );
                }else{
                    $result = array(
                        "metadata"=>
                            array(
                                'message' => "Belum ada data yang bisa ditampilkan",
                                'code' => 201,
                            )
                    );
                }
    
    
            } catch (Exception $e) {
                $result = array(
                    "metadata"=>
                        array(
                            'message' => "Gagal menampilkan data",
                            'code' => 201,
                        
                        )
                );
            }
            return $this->respond($result);
        }
    
        public function GetStatusAntrianMobileJKN(Request $request){
            $kdProfile = $this->getDataKdProfile($request);
            $Datareq = $request->json()->all();
    
            $kodeDokter = $Datareq['kodedokter'];
            $kdInternalRuangan =  $Datareq['kodepoli'];
            $tglAwal = date('Y-m-d',strtotime($Datareq['tanggalperiksa'])) .' 00:00:00';
            $tglAkhir = date('Y-m-d',strtotime($Datareq['tanggalperiksa'])) .' 23:59:59';
    
            if($Datareq['tanggalperiksa'] != date('Y-m-d',strtotime( $Datareq['tanggalperiksa']))){
                $result = array("metadata" => array("message" => "Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd", "code" => 201));
                return $this->respond($result);
            }
    
            // if($tglAwal < date('Y-m-d') .' 00:00:00'){
            //     $result = array("metadata" => array("message" => "Tanggal Periksa Tidak Berlaku", "code" => 201));
            //     return $this->respond($result);
            // }
    
            try {
    
                $ruang = Ruangan::where('kdinternal', $kdInternalRuangan)
                        ->where('aktif', true)
                        ->where('koders', $kdProfile)->first();
    
                if (empty($ruang)) {
                    $result = array("metadata" => array("message" => "Poli Tidak Ditemukan", "code" => 201));
                    return $this->respond($result);
                }
                
                $getStatusAntrian = DB::select(DB::raw("
                    
                    SELECT  x.namapoli,x.namadokter,
                            SUM(x.belumdipanggil) - SUM(x.sudahdipanggil) AS sisaantrean,                    
                            SUM(x.totalantrean) AS totalantrean,
                            x.koutajkn AS koutajkn,
                            x.koutajkn - SUM(x.totalantrean) AS sisakoutajkn,
                            x.koutanonjkn AS koutanonjkn,
                            x.koutanonjkn - SUM(x.totalantrean) AS sisakoutanonjkn
                    FROM(SELECT apr.norec,ru.namaruangan AS namapoli,pg.namalengkap AS namadokter,
                                COUNT(apr.norec) AS totalantrean,
                                CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 0 THEN 1 ELSE 0 END AS belumdipanggil,
                                CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 1 THEN 0 ELSE 0 END AS dipanggil,
                                CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 2 THEN 0 ELSE 0 END AS sudahdipanggil,
                                sk.quota AS koutajkn,sk.quota AS koutanonjkn
                    FROM perjanjianpasientr AS apr
                    INNER JOIN slottingonlinemt AS sk ON sk.ruanganidfk = apr.ruanganidfk 
                    INNER JOIN ruanganmt AS ru ON ru.id = apr.ruanganidfk
                    LEFT JOIN pegawaimt AS pg ON pg.id = apr.pegawaiidfk
                    WHERE apr.koders = $kdProfile
                    AND apr.tanggalreservasi between '$tglAwal' and '$tglAkhir'
                    AND ru.kdinternal = '$kdInternalRuangan'            
                    AND pg.kddokterbpjs = $kodeDokter
                    and ( apr.isbatal = false or apr.isbatal is null)
                    GROUP BY apr.norec,ru.namaruangan,pg.namalengkap,apr.statuspanggil,
                             sk.quota
                    ) AS x
                    GROUP BY x.namapoli,x.namadokter,x.koutajkn,x.koutanonjkn        
                    LIMIT 1
                "));
    
                $getLisAntrean = DB::select(DB::raw("
                    select 
                    apr.jenis || '-' ||
                    CASE WHEN length(CAST(apr.noantrian AS VARCHAR)) = 1 THEN
                    '0'|| CAST(apr.noantrian AS VARCHAR) ELSE CAST(apr.noantrian AS VARCHAR) END AS noantrian 
                    from perjanjianpasientr AS apr
                    LEFT JOIN ruanganmt AS ru ON ru.id = apr.ruanganidfk
                    LEFT JOIN pegawaimt AS pg ON pg.id = apr.pegawaiidfk
                    where apr.koders = $kdProfile
                    AND apr.tanggalreservasi BETWEEN '$tglAwal' and '$tglAkhir'
                    AND ru.kdinternal = '$kdInternalRuangan' 
                    AND pg.kddokterbpjs = $kodeDokter
                    AND statuspanggil = '0'
                    and ( apr.isbatal = false or apr.isbatal is null)
                    ORDER BY apr.tanggalreservasi DESC
                    LIMIT 1
                "));
    
                $antrian = "";
                foreach ($getLisAntrean as $itt){
                    $antrian = $itt->noantrian;
                }
                $result = [];
                foreach ($getStatusAntrian AS $item){
                    $result = array(
                        "namapoli" => $item->namapoli,
                        "namadokter" => $item->namadokter,
                        "totalantrean" => $item->totalantrean,
                        "sisaantrean" => $item->sisaantrean,
                        "antreanpanggil" =>$antrian,
                        "sisakuotajkn"=> $item->sisakoutajkn,
                        "kuotajkn" => $item->koutajkn,
                        "sisakuotanonjkn" =>$item->sisakoutanonjkn,
                        "kuotanonjkn" => $item->koutanonjkn,
                        "keterangan" => ""
                    );
                }
    
                if(count($result) > 0){
                    $result = array(
                        "response" => $result,
                        "metadata"=>
                            array(
                                'message' => "Ok",
                                'code' => 200,
                            )
                    );
                }else{
                    $result = array(
                        "metadata"=>
                            array(
                                'message' => "Belum ada data yang bisa ditampilkan",
                                'code' => 201,
                            )
                    );
                }
            } catch (\Exception $e) {
                $result = array(
                    "metadata"=>
                        array(
                            'message' => "Gagal menampilkan data",
                            'code' => 201,
                            // "e" => $e->getMessage().' ' . $e->getLine()
                        )
                );
            }
            return $this->respond($result);
        }
    
        public function GetAntrean(Request $request){
            $kdProfile = $this->getDataKdProfile($request);
            $request = $request->json()->all();
    
            if (empty($request['nomorkartu'])) {
                $result = array("metadata" => array("message" => "Nomor Kartu Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if(empty($request['tanggalperiksa'])) {
                $result = array("metadata"=>array("message" => "Tanggal Periksa Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$request['tanggalperiksa'])) {
                $result = array("metadata"=>array("message" => "Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if($request['tanggalperiksa'] >= date('Y-m-d',strtotime('+90 days',strtotime(date('Y-m-d'))))){
                $result = array("metadata"=>array("message" => "Tanggal periksa maksimal 90 hari dari hari ini","code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if($request['tanggalperiksa'] == date('Y-m-d')){
                $result = array("metadata"=>array("message" => "Tanggal periksa minimal besok", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if($request['tanggalperiksa'] < date('Y-m-d')){
                $result = array("metadata" => array("message" => "Tanggal Periksa Tidak Berlaku", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if(date('w',strtotime( $request['tanggalperiksa'] )) == 0 ){
                $result = array(
                    "metadata"=>array(
                        "message" => "Tidak ada jadwal Poli di hari Minggu",
                        "code" => 201
                    )
                );
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['nik'])) {
                $result = array("metadata" => array("message" => "NIK Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (!is_numeric($request['nik']) || strlen($request['nik']) < 16) {
                $result = array("metadata" => array("message" => "Format NIK Tidak Sesuai ", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['nohp'])) {
                $result = array("metadata" => array("message" => "No hp tidak boleh kosong", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if(empty($request['kodepoli']) ) {
                $result = array("metadata"=>array("message" => "Poli Tidak Ditemukan", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }else{
    
            }
    
            if(empty($request['jeniskunjungan']) ) {
                $result = array("metadata"=>array("message" => "Jenis Kunjungan tidak boleh kosong", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }else{
    
            }
    
            if(empty($request['nomorreferensi']) ) {
                $result = array("metadata"=>array("message" => "Nomor Referensi tidak boleh kosong", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }else{
    //            if($request['nomorreferensi'] < "1" || $request['nomorreferensi'] > "2") {
    //                $result = array("response"=>null,"metadata"=>array("code" => "400","message" => "Nomor Referensi tidak sesuai"));
    //                return $this->setStatusCode($result['metadata']['code'])->respond($result);
    //            }
            }
    
            // cekJadwalHFIS
            $objetoRequest = new \Illuminate\Http\Request();
            $objetoRequest ['url']= "jadwaldokter/kodepoli/". $request['kodepoli']."/tanggal/". $request['tanggalperiksa'];
            $objetoRequest ['jenis']= "antrean";
            $objetoRequest ['method']= "GET";
            $objetoRequest ['data']=null;
      
    
            $cek = app('App\Http\Controllers\Bridging\BPJSC')->bpjsTools($objetoRequest);
      
            if(is_array($cek)){
                $code = $cek['metaData']->code;
            }else{
                $cek = json_decode($cek->content(), true);
                $code = $cek['metaData']['code'];
            }

            if($code != '200'){
                $result = array("metadata"=>array("message" => "Pendaftaran ke Poli Ini Sedang Tutup", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }else{
                $ada = false;

                if(count($cek['response']) > 0){
                    foreach($cek['response'] as $item){
                        // dd($item);
                        if($request['kodedokter'] == $item->kodedokter){
                            $ada = true;
                            break;
                        }
                    }
                }
                if($ada == false){
                    $dokter = DB::table('pegawaimt')
                    ->where('aktif', true)
                    ->where('koders', $kdProfile)
                    ->where('kddokterbpjs', $request['kodedokter'])
                    ->first();
                    $result = array("metadata"=>array("message" => "Jadwal Dokter ".(empty($dokter->namalengkap)?'':$dokter->namalengkap)." Tersebut Belum Tersedia, Silahkan Reschedule Tanggal dan Jam Praktek Lainnya", "code" => 201));
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }
            }
         
    
            $eksek = false;
    
            $jenisrequest = 0;
    //        if($request['polieksekutif'] == 1){
    //            $eksek = true;
    //        }
    //        if($request['jenisrequest']  == '2') {//POLI
            DB::beginTransaction();
                try {
                    $norm = "";
                    $namaDokter = "";
                    $idDokter = null;
                    $pasienBaru = 0;
                    $antrian = $this->GetJamKosong($request['kodepoli'], $request['tanggalperiksa'], $kdProfile,$eksek);
                    $pasien = \DB::table('pasienmt')
                        ->whereRaw("nobpjs = '" . $request['nomorkartu'] . "'")
                        ->where('aktif', true)
                        ->where('koders', $kdProfile)
                        ->first();
                    $dokter = DB::table('pegawaimt')
                              ->where('aktif', true)
                              ->where('koders', $kdProfile)
                              ->where('kddokterbpjs', $request['kodedokter'])
                              ->first();
    
                    $ruang = Ruangan::where('kdinternal', $request['kodepoli'])
                        ->where('aktif', true)
                        // ->where('iseksekutif',$eksek)
                        ->where('koders', $kdProfile)->first();
    
                    if (empty($ruang)) {
                        $result = array("metadata" => array("code" => 201, "message" => "Poli Tidak Ditemukan"));
                        return $this->setStatusCode($result['metadata']['code'])->respond($result);
                    }
                    if (empty($pasien)) {
                        $pasienBaru = 0;
                        $jenisrequest = 0;
                        $norm = $request['norm'];
                         
                       DB::rollBack();
                        $result = array(
                            "metadata" => array(
                                "code" => 202,
                                "message" => "Data pasien ini tidak ditemukan, silahkan Melakukan Registrasi Pasien Baru")
                        );
                        return $this->setStatusCode($result['metadata']['code'])->respond($result);
    
                        // return  $this->postPendaftaranJKN($request,$kdProfile);
                    }else{
                        $pasienBaru = 1;
                        $norm = $pasien->norm;
                        $jenisrequest = 1;
                    }
    
                    if (empty($dokter)){
                        DB::rollBack();
                        $transMessage = "Dokter Tidak Ditemukan";
                        $transStatus = 'false';
                    }else{
                        $namaDokter = $dokter->namalengkap;
                        $idDokter = $dokter->id;
                    }
    
                    $tipepembayaran = '2';
                    $tgl = $request['tanggalperiksa'];
    
                    $dataReservasi = \DB::table('perjanjianpasientr as apr')
                        ->select('apr.noantrian', 'apr.noreservasi', 'ru.namaexternal', 'apr.tanggalreservasi')
                        ->join('ruanganmt as ru', 'ru.id', '=', 'apr.ruanganidfk')
                        ->whereRaw("to_char(apr.tanggalreservasi,'yyyy-MM-dd')= '$tgl'")
                        ->where('apr.ruanganidfk', '=', $ruang->id)
                        ->where('apr.noreservasi', '!=', '-')
                        ->where('apr.noidentitas', '=', $request['nik'])
                        ->where('apr.nobpjs', '=', $request['nomorkartu'])
                        ->whereNotNull('apr.noreservasi')
                        ->whereRaw(" (apr.isbatal = false or apr.isbatal is null)")
                        ->where('apr.aktif', true)
                        ->first();
                    
                   
    
                    if (isset($dataReservasi) && !empty($dataReservasi)) {
                        DB::rollBack();
                        $result = array("metadata" => array("code" => 201, "message" => "Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal Yang Sama"));
                        return $this->setStatusCode($result['metadata']['code'])->respond($result);
                    }
    
                    if(!$antrian['jamkosong']){
                         DB::rollBack();
                        $result = array("metadata" => array("code" => 201, "message" => "Jadwal Penuh"));
                        return $this->setStatusCode($result['metadata']['code'])->respond($result);
                    }
                    $newptp = new PasienPerjanjian();
                    $nontrian = PasienPerjanjian::max('noantrian') + 1;
                    $newptp->norec = $newptp->generateNewId();;
                    $newptp->koders = $kdProfile;
                    $newptp->aktif = true;
                    $newptp->ruanganidfk = $ruang->id;
                    $newptp->jeniskelaminidfk = !empty($pasien) ? $pasien->jeniskelaminidfk : null;
                    $newptp->noreservasi = substr(Uuid::generate(), 0, 7);
                    $newptp->tanggalreservasi = $request['tanggalperiksa'] . " " . $antrian['jamkosong'];
                    $newptp->tgllahir = !empty($pasien) ? $pasien->tgllahir : null;
                    $newptp->kelompokpasienidfk = $tipepembayaran;
                    $newptp->pendidikanidfk = 0;
                    $newptp->namapasien = !empty($pasien) ? $pasien->namapasien : null;
                    // $newptp->noidentitas = $request['nik'];
                    $newptp->tglinput = date('Y-m-d H:i:s');
                    $newptp->nobpjs = $request['nomorkartu'];
                    if (isset($request['nomorkartu'])){
                        $newptp->jenis = "B";
                    }
                    $newptp->norujukan = $request['nomorreferensi'];
                    $newptp->notelepon = !empty($pasien) ? $pasien->nohp : null;
                    $newptp->pegawaiidfk = $idDokter;
                    $newptp->tipepasien = !empty($pasien) ? "LAMA" : "BARU";
                    $newptp->ismobilejkn = 1;
                    $newptp->asalrujukanidfk = $request['jeniskunjungan'];
                    $newptp->type = !empty($pasien) ? "LAMA" : "BARU";
    
                    $newptp->agamaidfk = !empty($pasien) ? $pasien->agamaidfk : null;
                    if(!empty($pasien)){
                        $alamat = Alamat::where('normidfk', $pasien->id)->first();
                        if (!empty($alamat)) {
                            $newptp->alamatlengkap = $alamat->alamatlengkap;
                            $newptp->desakelurahanidfk = $alamat->desakelurahanidfk;
                            $newptp->negara = $alamat->negaraidfk;
                        }
                    }
                    $newptp->golongandarahidfk = !empty($pasien) ? $pasien->golongandarahidfk : null;
                    $newptp->kebangsaan = !empty($pasien) ? $pasien->kebangsaanidfk : null;
                    $newptp->namaayah = !empty($pasien) ? $pasien->namaayah : null;
                    $newptp->namaibu = !empty($pasien) ? $pasien->namaibu : null;
                    $newptp->namasuamiistri = !empty($pasien) ? $pasien->namasuamiistri : null;
    
                    $newptp->noaditional = !empty($pasien) ? $pasien->noaditional : null;
                    $newptp->noantrian = $antrian['antrian'];
                    $newptp->noidentitas =  $request['nik'];
                    $newptp->normidfk = !empty($pasien) ? $pasien->id : null;
                    $newptp->paspor = !empty($pasien) ? $pasien->paspor : null;
                    $newptp->pekerjaanidfk = !empty($pasien) ? $pasien->pekerjaanidfk : null;
                    $newptp->pendidikanidfk = !empty($pasien) && $pasien->pendidikanidfk != null ? $pasien->pendidikanidfk :  0;
                    $newptp->statusperkawinanidfk = !empty($pasien) ? $pasien->statusperkawinanidfk : null;
                    $newptp->tempatlahir = !empty($pasien) ? $pasien->tempatlahir : null;
    
                    $newptp->save();
                    $nomorAntrian = strlen((string)$newptp->noantrian);
                    // dd($nomorAntrian);
                    if ($nomorAntrian == 1){
                        $nomorAntrian = '0'.$newptp->noantrian;
                    }else{
                        $nomorAntrian = $newptp->noantrian;
                    }
    
                    $kodeDokter = $request['kodedokter'];
                    $kdInternalRuangan = $request['kodepoli'];
                    $tglAwal = date('Y-m-d',strtotime($request['tanggalperiksa'])) .' 00:00:00';
                    $tglAkhir = date('Y-m-d',strtotime($request['tanggalperiksa'])) .' 23:59:59';

                $getLisAntrean = DB::select(DB::raw("
                    SELECT x.namapoli,x.namadokter,
                    SUM(x.belumdipanggil) - SUM(x.sudahdipanggil) AS sisaantrean,
                    SUM(x.totalantrean) AS totalantrean,
                    x.koutajkn AS koutajkn,
                    x.koutajkn - SUM(x.totalantrean) AS sisakoutajkn,
                    x.koutanonjkn AS koutanonjkn,
                    x.koutanonjkn - SUM(x.totalantrean) AS sisakoutanonjkn
                    FROM(SELECT apr.norec,ru.namaruangan AS namapoli,pg.namalengkap AS namadokter,
                    COUNT(apr.norec) AS totalantrean,
                    CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 0 THEN 1 ELSE 0 END AS belumdipanggil,
                    CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 1 THEN 0 ELSE 0 END AS dipanggil,
                    CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 2 THEN 0 ELSE 0 END AS sudahdipanggil,
                    sk.quota AS koutajkn,sk.quota AS koutanonjkn
                    FROM slottingonlinemt as sk 
                    INNER JOIN perjanjianpasientr AS apr ON sk.ruanganidfk = apr.ruanganidfk AND apr.tanggalreservasi between '$tglAwal' and '$tglAkhir'
                    and apr.koders = $kdProfile
                     and ( apr.isbatal = false or apr.isbatal is null)
                    INNER JOIN ruanganmt AS ru ON ru.id = apr.ruanganidfk
                    LEFT JOIN pegawaimt AS pg ON pg.id = apr.pegawaiidfk AND pg.kddokterbpjs = '$kodeDokter'
                    WHERE 
                     ru.kdinternal = '$kdInternalRuangan'
                    and apr.aktif=true
                    GROUP BY apr.norec,ru.namaruangan,pg.namalengkap,apr.statuspanggil,
                    sk.quota
                    ) AS x
                    GROUP BY x.namapoli,x.namadokter,x.koutajkn,x.koutanonjkn
                    LIMIT 1
                        "));
    //                dd($getLisAntrean);
    
                    $transStatus = 'true';
                    $transMessage = "Ok";
                } catch (\Exception $e) {
                    $transMessage = "Gagal Reservasi";
                    $transStatus = 'false';
                }
    
                if ($transStatus == 'true') {
                    DB::commit();
                    date_default_timezone_set("Asia/Jakarta");
                    if ($pasienBaru == 0){
                        $estimasidilayani = strtotime($newptp->tanggalreservasi) * 1000;
                        $result = array(
                            "response" => array(
                                "nomorantrean" =>  $newptp->jenis . '-' . $nomorAntrian,
                                "angkaantrean" => count($getLisAntrean) > 0 ? $getLisAntrean[0]->totalantrean :1,
                                "kodebooking" => $newptp->noreservasi,
                                // "pasienbaru" => $jenisrequest,
                                "norm" => $norm,
                                "namapoli" => $ruang->namaruangan,
                                "namadokter" => $namaDokter,
                                "estimasidilayani" => $estimasidilayani,
                                "sisakuotajkn" => $getLisAntrean[0]->sisakoutajkn,
                                "kuotajkn" => $getLisAntrean[0]->koutajkn,
                                "sisakuotanonjkn" => $getLisAntrean[0]->sisakoutanonjkn,
                                "kuotanonjkn" => $getLisAntrean[0]->koutanonjkn,
                                "keterangan" => "Peserta harap 60 menit lebih awal guna pencatatan administrasi."
                            ),
                            "metadata" => array(
                                "message" => "Pasien Baru",
                                "code" => 202
                            )
                        );
                    }else{
                        
                        $estimasidilayani = strtotime($newptp->tanggalreservasi) * 1000;
                        $result = array(
                            "response" => array(
                                "nomorantrean" =>  $newptp->jenis . '-' . $nomorAntrian,
                                "angkaantrean" => $getLisAntrean[0]->totalantrean,
                                "kodebooking" => $newptp->noreservasi,
                                // "pasienbaru" => $jenisrequest,
                                "norm" => $norm,
                                "namapoli" => $ruang->namaruangan,
                                "namadokter" => $namaDokter,
                                "estimasidilayani" => $estimasidilayani,
                                "sisakuotajkn" => $getLisAntrean[0]->sisakoutajkn,
                                "kuotajkn" => $getLisAntrean[0]->koutajkn,
                                "sisakuotanonjkn" => $getLisAntrean[0]->sisakoutanonjkn,
                                "kuotanonjkn" => $getLisAntrean[0]->koutanonjkn,
                                "keterangan" => "Peserta harap 60 menit lebih awal guna pencatatan administrasi."
                            ),
                            "metadata" => array(
                                "message" => $transMessage,
                                "code" => 200
                            )
                        );
                    }
                }else{
                    \DB::rollBack();
                    $result = array(
                        "metadata" => array(
                            "message" => $transMessage,
                            "code" => 201,
                            // "e" => $e->getMessage().' ' . $e->getLine()
                        )
                    );
    
                }
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
    
        }
    
        public function GetSisaAntrean(Request $request){
            $kdProfile = $this->getDataKdProfile($request);
            $request = $request->json()->all();
    
            if(empty($request['kodebooking']) ) {
                $result = array("metadata"=>array("message" => "Kode Booking Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            $data = DB::table('perjanjianpasientr as apr')
                    ->leftJoin('pegawaimt as pg','pg.id','=','apr.pegawaiidfk')
                    ->leftJoin('ruanganmt as ru','ru.id','=','apr.ruanganidfk')
                    ->selectRaw("
                        apr.*,pg.namalengkap,ru.namaruangan,apr.pegawaiidfk as objectpegawaifk,apr.ruanganidfk as objectruanganfk
                    ")
                    ->where('apr.koders', $kdProfile)
                    ->where('apr.aktif', true)
                    ->where('apr.noreservasi', $request['kodebooking'])
                    ->first();
            if(empty($data)){
                $result = array(
                    "metadata"=>
                        array(
                            'message' => "Antrean Tidak Ditemukan",
                            'code' => 201,
                        )
                );
                return $this->respond($result);
            }
            $kodeDokter = $data->objectpegawaifk;
            $kdInternalRuangan = $data->objectruanganfk;
            $tglAwal = date('Y-m-d',strtotime($data->tanggalreservasi)) .' 00:00:00';
            $tglAkhir = date('Y-m-d',strtotime($data->tanggalreservasi)) .' 23:59:59';
            $getStatusAntrian = DB::select(DB::raw("
                                SELECT  x.namapoli,x.namadokter,
                                        SUM(x.belumdipanggil) - SUM(x.sudahdipanggil) AS sisaantrean,                    
                                        SUM(x.totalantrean) AS totalantrean,
                                        x.koutajkn AS koutajkn,
                                        x.koutajkn - SUM(x.totalantrean) AS sisakoutajkn,
                                        x.koutanonjkn AS koutanonjkn,
                                        x.koutanonjkn - SUM(x.totalantrean) AS sisakoutanonjkn
                                FROM(SELECT apr.norec,ru.namaruangan AS namapoli,pg.namalengkap AS namadokter,
                                            COUNT(apr.norec) AS totalantrean,
                                            CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 0 THEN 1 ELSE 0 END AS belumdipanggil,
                                            CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 1 THEN 0 ELSE 0 END AS dipanggil,
                                            CASE WHEN CAST(apr.statuspanggil AS INTEGER) = 2 THEN 0 ELSE 0 END AS sudahdipanggil,
                                            sk.quota AS koutajkn,sk.quota AS koutanonjkn
                                FROM perjanjianpasientr AS apr
                                INNER JOIN slottingonlinemt AS sk ON sk.ruanganidfk = apr.ruanganidfk 
                                INNER JOIN ruanganmt AS ru ON ru.id = apr.ruanganidfk
                                LEFT JOIN pegawaimt AS pg ON pg.id = apr.pegawaiidfk
                                WHERE apr.koders = $kdProfile
                                AND apr.tanggalreservasi between '$tglAwal' and '$tglAkhir'
                                AND apr.ruanganidfk = '$kdInternalRuangan'            
                                AND apr.pegawaiidfk = $kodeDokter
                                GROUP BY apr.norec,ru.namaruangan,pg.namalengkap,apr.statuspanggil,
                                         sk.quota
                                ) AS x
                                GROUP BY x.namapoli,x.namadokter,x.koutajkn,x.koutanonjkn        
                                LIMIT 1
                            "));
    
            $getLisAntrean = DB::select(DB::raw("
                    select 
                    apr.jenis || '-' ||
                    CASE WHEN length(CAST(apr.noantrian AS VARCHAR)) = 1 THEN
                    '0'|| CAST(apr.noantrian AS VARCHAR) ELSE CAST(apr.noantrian AS VARCHAR) END AS noantrian 
                    from perjanjianpasientr AS apr
                    INNER JOIN ruanganmt AS ru ON ru.id = apr.ruanganidfk
                    LEFT JOIN pegawaimt AS pg ON pg.id = apr.pegawaiidfk
                    where apr.koders = $kdProfile 
                    AND apr.tanggalreservasi BETWEEN '$tglAwal' and '$tglAkhir'
                    AND ru.kdinternal = '$kdInternalRuangan' 
                    AND pg.kddokterbpjs = $kodeDokter
                    AND statuspanggil = '0'
                    ORDER BY apr.tanggalreservasi DESC
                    LIMIT 1
                "));
            $antrian = "";
            foreach ($getLisAntrean as $itt){
                $antrian = $itt->noantrian;
            }
            $nomorAntrian = strlen((string)$data->noantrian);
            if ($nomorAntrian == 1){
                $nomorAntrian = '0'.$data->noantrian;
            }else{
                $nomorAntrian = $data->noantrian;
            }
            $result = [];
            foreach ($getStatusAntrian AS $item){
                $estimasidilayani = 0;
                if ((int)$item->sisaantrean == 0){
                    $estimasidilayani = 1800 * 1;
                }else{
                    $estimasidilayani = 1800 * ((int)$item->sisaantrean - 1);
                }
                $result = array(
                    "nomorantrean" => $data->jenis . '-' . $nomorAntrian,
                    "namapoli" => $data->namaruangan,
                    "namadokter" => $data->namalengkap,
                    "sisaantrean" => $item->sisaantrean,
                    "antreanpanggil" =>$antrian,
                    "waktutunggu"=> $estimasidilayani,
                    "keterangan" => ""
                );
            }
            try {
                if(count($result) > 0){
                    $result = array(
                        "response" => $result,
                        "metadata"=>
                            array(
                                'message' => "Ok",
                                'code' => 200,
                            )
                    );
                }else{
                    $result = array(
                        "metadata"=>
                            array(
                                'message' => "Belum ada data yang bisa ditampilkan",
                                'code' => 201,
                            )
                    );
                }
            } catch (Exception $e) {
                $result = array(
                    "metadata"=>
                        array(
                            'message' => "Gagal menampilkan data",
                            'code' => 201,
                            // "e" => $e->getMessage().' ' . $e->getLine()
                        )
                );
            }
            return $this->respond($result);
        }
    
        public function saveBatalAntrean (Request $request)
        {
            $kdProfile = $this->getDataKdProfile($request);
            $request = $request->json()->all();
    
            if (empty($request['kodebooking'])) {
                $result = array("metadata" => array("message" => "Antrean Tidak Ditemukan", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['keterangan'])) {
                $result = array("metadata" => array("message" => "Keterangan Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            DB::beginTransaction();
            try {
    
                $data = DB::table('perjanjianpasientr')
                        ->where('koders', $kdProfile)
                        // ->where('statusenabled', true)
                        ->where('noreservasi', $request['kodebooking'])
                        ->first();
                if (empty($data)){
                    DB::rollBack();
                    $result = array(
                        "metadata" => array(
                            "message" => "Antrean Tidak Ditemukan",
                            "code" => 201
                        )
                    );
                    return $this->setStatusCode($result['metadata']['code'])->respond($result);
                }else{
                    if($data->aktif == false){
                        $result = array(
                            "metadata" => array(
                                "message" => "Antrean Tidak Ditemukan atau Sudah Dibatalkan",
                                "code" => 201
                            )
                        );
                        return $this->setStatusCode($result['metadata']['code'])->respond($result);
                    }
                    if($data->isconfirm == true){
                        DB::rollBack();
                        $result = array(
                            "metadata" => array(
                                "message" => "Pasien Sudah Dilayani, Antrean Tidak Dapat Dibatalkan",
                                "code" => 201
                            )
                        );
                        return $this->setStatusCode($result['metadata']['code'])->respond($result);
                    }
                    $data = DB::table('perjanjianpasientr')
                        ->where('koders', $kdProfile)
                        ->where('aktif', true)
                        ->where('noreservasi', $request['kodebooking'])
                        ->update([
                            'aktif' => false,
                            'isbatal' => false,
                            'keteranganbatal' => $request['keterangan'],
                        ]);
                }
    
                $transStatus = 'true';
                $transMessage = "Ok";
            } catch (Exception $e) {
                $transMessage = "Gagal Batal Antrean";
                $transStatus = 'false';
            }
    
            if ($transStatus != 'false') {
                DB::commit();
                $result = array(
                    "metadata" => array(
                        "message" => $transMessage,
                        "code" => 200)
                );
            }else{
                DB::rollBack();
                $result = array(
                    "metadata" => array(
                        "message" => $transMessage,
                        "code" => 201)
                );
    
            }
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
    
        public function saveCheckInAntrean (Request $request)
        {
            $kdProfile = $this->getDataKdProfile($request);
            $request = $request->json()->all();
            $t =  $request['waktu'];
            // $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            // $d = new \DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
            // $tglRegis = $d->format('Y-m-d H:i:s');
            $tglRegis = date('Y-m-d H:i:s', $t / 1000);
            $tglRegBulan = date('Y-m-d', $t / 1000);;
            if (empty($request['kodebooking'])) {
                $result = array("metadata" => array("message" => "Kode Booking Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['waktu'])) {
                $result = array("metadata" => array("message" => "Waktu Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            $data = DB::table('perjanjianpasientr as apr')
                ->leftJoin('pegawaimt as pg','pg.id','=','apr.pegawaiidfk')
                ->leftJoin('ruanganmt as ru','ru.id','=','apr.ruanganidfk')
                ->selectRaw("
                        apr.*,pg.namalengkap,ru.namaruangan,ru.instalasiidfk as objectdepartemenfk
                    ")
                ->where('apr.koders', $kdProfile)
                ->where('apr.aktif', true)
                ->where('apr.noreservasi', $request['kodebooking'])
                ->first();
            if(empty($data)){
                $result = array(
                    "metadata" => array(
                        "message" => 'Kode Booking ini tidak ada',
                        "code" => 201)
                );
    
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }    
            DB::beginTransaction();
            try {
                $pasiendaftar = array(
                        'tglregistrasi' => $tglRegis,
                        'tglregistrasidate' => $tglRegBulan,
                        'nocmfk' =>  $data->normidfk,
                        'objectruanganfk' =>  $data->ruanganidfk,
                        'objectdepartemenfk' =>  $data->objectdepartemenfk,
                        'objectkelasfk' =>  6,//nonkelas
                        'objectkelompokpasienlastfk' =>  $data->kelompokpasienidfk,//umum
                        'objectrekananfk' =>  null,
                        'tipelayanan' => 1,//reguler
                        'objectpegawaifk' =>  $data->pegawaiidfk,
                        'noregistrasi' =>  '',
                        'norec_pd' =>  '',
                        'israwatinap' =>  'false',
                        'statusschedule' => $data->noreservasi,
                        'objectrekananfk' => 2552,
                        'isjkn' => true
                );
                $antrianpasiendiperiksa = array(
                    'norec_apd' => '',
                    'tglregistrasi' => $tglRegis,
                    'objectruanganfk' => $data->ruanganidfk,
                    'objectkelasfk' => 6,
                    'objectpegawaifk' =>$data->pegawaiidfk,
                    'objectkamarfk' =>null,
                    'nobed' =>null,
                    'objectdepartemenfk' =>$data->objectdepartemenfk,
                    'objectasalrujukanfk' => $data->asalrujukanidfk,
                    'israwatgabung' => 0,
                    'objectkamarfk' => null,
                );
    
                if ($data->tipepasien == "BARU"){
                    return redirect()->route("pasienBaru",[
                        "jenis" => "B"
                    ]);
                }else{
                    return redirect()->route("CheckInAntrean",[
                        'pasiendaftar' => $pasiendaftar,
                        'antrianpasiendiperiksa' => $antrianpasiendiperiksa
                    ]);
                }
    
                $transStatus = 'true';
                $transMessage = "Ok";
            } catch (Exception $e) {
                $transMessage = "Gagal Check In";
                $transStatus = 'false';
            }
    
            if ($transStatus != 'false') {
                DB::commit();
                $result = array(
                    "metadata" => array(
                        "message" => $transMessage,
                        "code" => 200)
                );
            }else{
                DB::rollBack();
                $result = array(
                    "metadata" => array(
                        "message" => $transMessage,
                        "code" => 201)
                );
    
            }
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
    
        public function getPropinsiBpjs (){
            $data = $this->getIdConsumerBPJS();
            $secretKey = $this->getPasswordConsumerBPJS();
            // Computes the timestamp
            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
            // Computes the signature by hashing the salt with the secret key as the key
            $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
    
            // base64 encode…
            $encodedSignature = base64_encode($signature);
    
            $curl = curl_init();
    
            curl_setopt_array($curl, array(
                CURLOPT_PORT => $this->getPortBrigdingBPJS(),
                CURLOPT_URL=> $this->getUrlBrigdingBPJS()."referensi/propinsi",
    //            CURLOPT_URL => "https://dvlp.bpjs-kesehatan.go.id/VClaim-rest/referensi/propinsi",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json; charset=utf-8",
                    "X-cons-id: ".  (string)$data,
                    "X-signature: ". (string)$encodedSignature,
                    "X-timestamp: ". (string)$tStamp
                ),
            ));
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
    
            curl_close($curl);
    
            if ($err) {
                $result= "cURL Error #:" . $err;
            } else {
                $result = (array) json_decode($response);
            }
            $res = $result['response']->list;
            return $res;
        }
    
        public function getKabupatenBpjs ($kodePropinsi){
            $data = $this->getIdConsumerBPJS();
            $secretKey = $this->getPasswordConsumerBPJS();
            // Computes the timestamp
            date_default_timezone_set('UTC');
            $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
            // Computes the signature by hashing the salt with the secret key as the key
            $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
    
            // base64 encode…
            $encodedSignature = base64_encode($signature);
    
            $curl = curl_init();
    
            curl_setopt_array($curl, array(
                CURLOPT_PORT => $this->getPortBrigdingBPJS(),
                CURLOPT_URL=> $this->getUrlBrigdingBPJS()."referensi/kabupaten/propinsi/".$kodePropinsi,
    //            CURLOPT_URL => "https://dvlp.bpjs-kesehatan.go.id/VClaim-rest/referensi/kabupaten/propinsi/".$request['kodePropinsi'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json; charset=utf-8",
                    "X-cons-id: ".  (string)$data,
                    "X-signature: ". (string)$encodedSignature,
                    "X-timestamp: ". (string)$tStamp
                ),
            ));
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
    
            curl_close($curl);
    
            if ($err) {
                $result= "cURL Error #:" . $err;
            } else {
                $result = (array) json_decode($response);
            }
            $res=$result['response']->list;
            return $res;
        }
    
        public function savePasienBaru (Request $request)
        {
            $kdProfile = $this->getDataKdProfile($request);
            $request = $request->json()->all();
    
            if (empty($request['nomorkartu'])) {
                $result = array("metadata" => array("message" => "Nomor Kartu Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
        
            if (!is_numeric($request['nomorkartu']) || strlen($request['nomorkartu']) < 13) {
                $result = array("metadata" => array("message" => "Format Nomor Kartu Tidak Sesuai", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            
    
            if (empty($request['nik'])) {
                $result = array("metadata" => array("message" => "NIK Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (!is_numeric($request['nik']) || strlen($request['nik']) < 16) {
                $result = array("metadata" => array("message" => "Format NIK Tidak Sesuai ", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['nomorkk'])) {
                $result = array("metadata" => array("message" => "Nomor KK Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['nama'])) {
                $result = array("metadata" => array("message" => "Nama Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['jeniskelamin'])) {
                $result = array("metadata" => array("message" => "Jenis Kelamin Belum Dipilih", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['tanggallahir'])) {
                $result = array("metadata" => array("message" => "Tanggal Lahir Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if($request['tanggallahir'] != date('Y-m-d',strtotime($request['tanggallahir'])) || date('Y-m-d',strtotime($request['tanggallahir'])) > date('Y-m-d')){
                $result = array("metadata" => array("message" => "Format Tanggal Lahir tidak Sesuai", "code" => 201));
                return $this->respond($result);
            }
    
            if (empty($request['nohp'])) {
                $result = array("metadata" => array("message" => "No hp tidak boleh kosong", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
    
            if (empty($request['alamat'])) {
                $result = array("metadata" => array("message" => "Alamat Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['kodeprop'])) {
                $result = array("metadata" => array("message" => "Kode Propinsi Belum Diisi", "code" => 201));
                return 
                $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['namaprop'])) {
                $result = array("metadata" => array("message" => "Nama Propinsi Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['kodedati2'])) {
                $result = array("metadata" => array("message" => "Kode Dati 2 Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['namadati2'])) {
                $result = array("metadata" => array("message" => "Dati 2 Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['kodekec'])) {
                $result = array("metadata" => array("message" => "Kode Kecamatan Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['namakec'])) {
                $result = array("metadata" => array("message" => " Kecamatan Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['kodekel'])) {
                $result = array("metadata" => array("message" => "Kode Kelurahan Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['namakel'])) {
                $result = array("metadata" => array("message" => "Kelurahan Belum Diisi ", "code" => 201));
               
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['rt']) && $request['rt']!=0) {
                $result = array("metadata" => array("message" => "RT Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            if (empty($request['rw']) && $request['rw']!=0) {
                $result = array("metadata" => array("message" => "RW Belum Diisi", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
            $propinsi = DB::table('provinsimt')
                 ->select('id','namapropinsi')
                 ->where('kodebpjs',$request['kodeprop'])
                 ->first();
            // $jk = DB::table('jeniskelaminmt')
            //       ->where('reportdisplay', strtoupper($request['jeniskelamin']))
            //       ->select('id','jeniskelamin','reportdisplay')
            //       ->first();
            $pasien = array(
                'namaPasien'=> $request['nama'],
                'noIdentitas'=> $request['nik'],
                'namaSuamiIstri'=> 'null',
                'noAsuransiLain'=> null,
                'noBpjs' => $request['nomorkartu'],
                'noHp' => $request['nohp'],
                'tempatLahir' => null,
                'namaKeluarga' => null,
                'tglLahir' => date('Y-m-d H:i:s',strtotime($request['tanggallahir'])),
                'image' => 'null',
                'nomorkk' => $request['nomorkk'],
            );
            $cek = Pasien::where('noidentitas',$request['nik'])->where('aktif',true)->first();

            if(!empty($cek)){
                $result = array("metadata" => array("message" => "Data Peserta sudah pernah dientrikan", "code" => 201));
                return $this->setStatusCode($result['metadata']['code'])->respond($result);
            }
             
            // DB::beginTransaction();
            try {
                if (!empty($request)){
                
                    $zz ['userData'] =['namauser'=>$request['userData']['namauser'] ,'id'=>$request['userData']['id']];
                    // // $request = new Request;
                    // $request ['isbayi'] = false;
                    // $request ['istriageigd'] = false;
                    // $request ['userData'] =  $zz ['userData'];
           
                    // // $objetoRequest ['isPenunjang'] = false;
                    // $request ['idpasien'] = false;
                    // $request ['pasien'] = $pasien;
                    // $request ['agama']= ['id' => null];
                    // $request ['jenisKelamin'] = ['id'=>$request['jeniskelamin']=='L'?1:2];
                    // $request ['pekerjaan'] = ['id'=>null];
                    // $request ['pendidikan']= ['id'=>null];
                    // $request ['statusPerkawinan'] = null;
                    // $request ['suku'] = null;
                    // $request ['namaIbu'] = null;
                    // $request ['noTelepon'] = $request['nohp'];
                    // $request ['noAditional'] = null;
                    // $request ['kebangsaan'] = null;
                    // $request ['negara'] =['id'=>0];
                    // $request ['namaAyah'] = null;
                    // $request ['alamatLengkap'] = $request['alamat'];
                    // $request ['desaKelurahan'] = null;
                    // $request ['namaDesaKelurahan'] = $request['namakel'];
                    // $request ['kecamatan'] = null;
                    // $request ['namaKecamatan'] = $request['namakec'];
                    // $request ['kotaKabupaten'] = null;
                    // $request ['namaKotaKabupaten'] =  $request['namadati2'];
                    // $request ['propinsi'] = $propinsi!=null?$propinsi->id:null;
                    // $request ['namapropinsi'] =$request['namaprop'];
                    // $request ['kodePos'] = null;
                    // $request ['penanggungjawab'] = null;
                    // $request ['hubungankeluargapj'] = null;
                    // $request ['pekerjaanpenangggungjawab'] = null;
                    // $request ['ktppenanggungjawab'] = null;
                    // $request ['alamatrmh'] = null;
                    // $request ['alamatktr'] = null;
                    // $request ['teleponpenanggungjawab'] = null;
                    // $request ['bahasa'] = null;
                    // $request ['jeniskelaminpenanggungjawab'] = null;
                    // $request ['umurpenanggungjawab'] = null;
                    // $request ['dokterpengirim'] = null;
                    // $request ['alamatdokter'] = null;
                    // $request ['rtrw'] = $request['rt'] . '/' . $request['rw'];
                    // $request ['isjkn'] = true;
                    $objetoRequest = new \Illuminate\Http\Request();
                    $objetoRequest->setMethod('POST');
                    // $objetoRequest->header('Content-Type','application/json');
                    $objetoRequest->request->add([
                        'userData'=>['namauser'=>$request['userData']['namauser'] ,'id'=>$request['userData']['id']],
                        'isbayi' => false,
                        'istriageigd' => false,
                        'isPenunjang' => false,
                        'idpasien' => '',
                        'pasien' => $pasien,
                        'agama' =>['id' => null],
                        'jenisKelamin'=>['id'=> $request['jeniskelamin']=='L'?1:2],
                        'pekerjaan' => [ 'id' => null],
                        'pendidikan' =>  [ 'id' => null],
                        'statusPerkawinan' =>  [ 'id' => null],
                        'golonganDarah' => null,
                        'suku' => null,
                        'namaIbu' => null,
                        'noTelepon' => $request['nohp'],
                        'noAditional' => null,
                        'kebangsaan' => [ 'id' => null],
                        'negara' => ['id' => 0],
                        'namaAyah' => null,
                        'alamatLengkap' => $request['alamat'],
                        'desaKelurahan' =>  [ 'id' => null,'namaDesaKelurahan' =>$request['namakel']],
                        'kecamatan' =>['namaKecamatan' =>$request['namakec'], 'id' => null],
                        'kotaKabupaten' => ['namaKotaKabupaten' =>$request['namadati2'], 'id' => null],
                        'propinsi' =>['id'=> $propinsi!=null?$propinsi->id:null,'namapropinsi'=>$request['namaprop']],
                        'kodePos' => null,
                        'penanggungjawab' => null,
                        'hubungankeluargapj' => null,
                        'pekerjaanpenangggungjawab' => null,
                        'ktppenanggungjawab' => null,
                        'alamatrmh' => null,
                        'alamatktr' => null,
                        'teleponpenanggungjawab' => null,
                        'bahasa' => null,
                        'jeniskelaminpenanggungjawab' => null,
                        'umurpenanggungjawab' => null,
                        'dokterpengirim' => null,
                        'alamatdokter' => null,
                        'rtrw' => $request['rt'] . '/' . $request['rw'],
                        'isjkn' => true,
                    ]);
                                 
            
                    return app('App\Http\Controllers\Registrasi\RegistrasiC')->savePasienJKN($objetoRequest);
                    // return redirect()->route("savePasienBaruJkn",$objetoRequest);
                }
    
                $transStatus = 'true';
                $transMessage = "Ok";
            } catch (Exception $e) {
                $transMessage = "Gagal Check In";
                $transStatus = 'false';
            }
    
            if ($transStatus != 'false') {
                // DB::commit();
                $result = array(
                    "metadata" => array(
                        "message" => 'Harap datang ke adminsi untuk melengkapi data rekam medis',
                        "code" => 200)
                );
            }else{
                // DB::rollBack();
                $result = array(
                    "metadata" => array(
                        "message" => $transMessage,
                        "code" => 201)
                );
    
            }
            return $this->setStatusCode($result['metadata']['code'])->respond($result);
        }
         public function getDaftarStatusVA(Request $request){
            $kdProfile = (int)$this->getDataKdProfile($request);
            $dataLogin=$request->all();
            $data = \DB::table('virtualaccount_t as vr')
                ->leftjoin('strukbuktipenerimaan_t as sbm', 'sbm.norec', '=', 'vr.norec_sbm')
                ->join('strukpelayanan_t as sp', 'vr.norec_sp', '=', 'sp.norec')
                ->leftjoin('pasiendaftar_t as pd', 'vr.norec_pd', '=', 'pd.norec')
                ->leftjoin('ruangan_m as ru', 'ru.id', '=', 'pd.objectruanganlastfk')
                // ->leftjoin('loginuser_s as lu', 'lu.id', '=', 'vr.pegawaifk')
                ->leftjoin('pegawai_m as p', 'p.id', '=', 'vr.objectpegawaifk')
                ->leftjoin('pasien_m as ps', 'ps.id', '=', 'pd.nocmfk')
                ->select(
                       \DB::raw("vr.*,pd.noregistrasi,pd.tglregistrasi,ps.nocm,ps.namapasien,p.namalengkap as kasir,ru.namaruangan,sbm.nosbm,sp.nostruk")
                )
                ->where('vr.statusenabled',true)
                ->where('vr.kdprofile', $kdProfile)
                ->whereNotnull('vr.norec_pd');
    
            $filter = $request->all();
            if(isset($filter['dari']) && $filter['dari'] != "" && $filter['dari'] != "undefined") {
                $tgl2 = $filter['dari'] ;//. " 00:00:00";
                $data = $data->where('vr.datetime_created', '>=', $tgl2);
            }
            if(isset($filter['sampai']) && $filter['sampai'] != "" && $filter['sampai'] != "undefined") {
                $tgl = $filter['sampai'] ;//. " 23:59:59";
                $data = $data->where('vr.datetime_created', '<=', $tgl);
            }
            if(isset($filter['idPegawai']) && $filter['idPegawai'] != "" && $filter['idPegawai'] != "undefined") {
                $data = $data->where('p.id', '=', $filter['idPegawai']);
            }
            if(isset($filter['idCaraBayar']) && $filter['idCaraBayar'] != "" && $filter['idCaraBayar'] != "undefined") {
                $data = $data->where('cb.id', '=', $filter['idCaraBayar']);
            }
            if(isset($filter['idKelTransaksi']) && $filter['idKelTransaksi'] != "" && $filter['idKelTransaksi'] != "undefined") {
                $data = $data->where('kt.id', $filter['idKelTransaksi']);
            }
            if(isset($filter['ins']) && $filter['ins'] != "" && $filter['ins'] != "undefined") {
                $data = $data->where('ru.objectdepartemenfk', $filter['ins']);
            }
            if(isset($filter['nosbm']) && $filter['nosbm'] != "" && $filter['nosbm'] != "undefined") {
                $data = $data->where('sbm.nosbm','ilike','%'.$filter['nosbm'].'%');
            }
            if(isset($filter['nocm']) && $filter['nocm'] != "" && $filter['nocm'] != "undefined") {
                $data = $data->where('ps.nocm','ilike','%'.$filter['nocm'].'%');
            }
            if(isset($filter['nama']) && $filter['nama'] != "" && $filter['nama'] != "undefined") {
                $data = $data->where('ps.namapasien','ilike','%'.$filter['nama'].'%');
            }
             if(isset($filter['nostruk']) && $filter['nostruk'] != "" && $filter['nostruk'] != "undefined") {
                $data = $data->where('sp.nostruk','ilike','%'.$filter['nostruk'].'%');
            }
            if(isset($filter['status']) && $filter['status'] != "" && $filter['status'] != "undefined") {
                $data = $data->where('vr.va_status','ilike','%'.$filter['status'].'%');
            }
            if(isset($filter['statusB']) && $filter['statusB'] != "" && $filter['statusB'] != "undefined") {
                if($filter['statusB'] == 'Y'){
                     $data = $data->whereNotNull('vr.datetime_payment');
                }
                else if($filter['statusB'] == 'N'){
                     $data = $data->whereNull('vr.datetime_payment');
                }
                else if($filter['statusB'] == 'E'){
                     $data = $data->whereNull('vr.datetime_payment');
                     $tgl= date('Y-m-d H:i:s') ;
                     $data = $data->whereRaw("'$tgl' > vr.datetime_expired");
                }
            }
            if(isset($filter['email']) && $filter['email'] != "" && $filter['email'] != "undefined") {
                $data = $data->where('vr.customer_email','=',$filter['email']);
            }
          
    
            if(isset($request['KasirArr']) && $request['KasirArr']!="" && $request['KasirArr']!="undefined"){
                $arrRuang = explode(',',$request['KasirArr']) ;
                $kodeRuang = [];
                foreach ( $arrRuang as $item){
                    $kodeRuang[] = (int) $item;
                }
                $data = $data->whereIn('p.id',$kodeRuang);
            }
             $data = $data->orderBy('vr.datetime_created','desc');
            $data = $data->get();
            foreach($data as $d){
                if($d->datetime_payment !=null){
                    $d->status = 'Sudah dibayar';
                } 
                else if($d->datetime_payment ==null){
                    if((date('Y-m-d H:i:s') > date( $d->datetime_expired) ) ){
                        $d->status = 'Kadaluarsa';
                    }else{
                        $d->status = 'Belum Dibayar';
                    }
                    
                }
            }
            return $this->respond($data);
        }
        public function getSlottingByRuanganNew2(Request $r)
        {
            $kdProfile = $this->getDataKdProfile($r);
            $dataReservasi = \DB::table('antrianpasienregistrasi_t as apr')
                ->select('apr.norec','apr.tanggalreservasi')
                ->whereRaw("to_char(apr.tanggalreservasi,'yyyy-MM-dd') = '$r[tgl]'")
                ->where('apr.objectruanganfk', $r['ruanganfk'])
                ->where('apr.noreservasi','!=','-')
                ->where('apr.kdprofile',$kdProfile)
                ->whereNotNull('apr.noreservasi')
                ->where('apr.statusenabled',true)
                ->get();
    
            $ruangan = \DB::table('ruangan_m as ru')
                ->join('slottingonline_m as slot', 'slot.objectruanganfk', '=', 'ru.id')
                ->select('ru.id', 'ru.namaruangan', 'ru.objectdepartemenfk', 'slot.jambuka', 'slot.jamtutup',
                    'slot.quota',
                    // DB::raw("DATEDIFF(second,    [slot].[jambuka],   [slot].[jamtutup]) / 3600.0 AS totaljam "))
                   DB::raw("(EXTRACT(EPOCH FROM slot.jamtutup) - EXTRACT(EPOCH FROM slot.jambuka))/3600 as totaljam"))
                ->where('ru.statusenabled', true)
                ->where('ru.id', $r['ruanganfk'])
                ->where('slot.kdprofile',$kdProfile)
                ->where('slot.statusenabled', true)
                ->first();
           if (empty($ruangan)) {
                $result = array(
                    "slot" => [],
                    "message" => "Jadwal Reservasi Penuh",
                    'as' => 'er@epic',
                );
                return $result;
            }
    
            $begin = new Carbon($ruangan->jambuka);
            $jamBuka = $begin->format('H:i');
            $end = new Carbon($ruangan->jamtutup);
            $jamTutup = $end->format('H:i');
            $quota = (float)$ruangan->quota;
            $waktuPerorang = ((float)$ruangan->totaljam / (float)$ruangan->quota) * 60;
    //dd($waktuPerorang);
            $i = 0;
            $slotterisi = 0;
            $jamakhir = '00:00';
            $reservasi = [];
            foreach ($dataReservasi as $items) {
                $jamUse = new Carbon($items->tanggalreservasi);
                $slotterisi += 1;
                $reservasi [] = array(
                    'jamreservasi' => $jamUse->format('H:i')
                );
                $jamakhir = $jamUse->format('H:i');
            }
    
            $intervals = [];
            $intervalsAwal = [];
            $begin = new \DateTime($jamBuka);
            $end = new \DateTime($jamTutup);
            $interval = \DateInterval::createFromDateString(floor($waktuPerorang) . ' minutes');
            $period = new \DatePeriod($begin, $interval, $end);
            foreach ($period as $dt) {
                $intervals[] = array(
                    'jam' => $dt->format("H:i")
                );
                $intervalsAwal[] = array(
                    'jam' => $dt->format("H:i")
                );
            }
    
            if (count($intervals) == 0) {
                $result = array(
                    "slot" => null,
                    "code" => "400",
                    "message" => "Jadwal Reservasi Penuh"
                );
                return $result;
            }
            if (count($reservasi) > 0) {
                for ($j = count($reservasi) - 1; $j >= 0; $j--) {
                    for ($k = count($intervals) - 1; $k >= 0; $k--) {
                        if ($intervals[$k]['jam'] == $reservasi[$j]['jamreservasi']) {
                            array_splice($intervals, $k, 1);
                        }
                    }
                }
            }
    
            if (count($intervals) > 0) {
                $result = array(
                    "slot" => $intervals,
                    "code" => "200",
                    "message" => count($intervals) . " Slot/Jadwal Tersedia"
                );
            } else {
                $result = array(
                    "slot" => null,
                    "code" => "400",
                    "message" => "Jadwal Reservasi Penuh"
                );
            }
            return $this->respond($result);
        }
        public function saveAntrolV2( $request){
            try{
                $ruangan = DB::table('ruanganmt')->where('id',$request['ruanganidfk'])->first()->kdinternal;
                if($ruangan== null || $ruangan== ''){
                    $result = array("metadata"=>array("message" => "Kode Poli tidak valid", "code" => 201));
                    return $result;
                }
                date_default_timezone_set("Asia/Jakarta");
                $objetoRequest = new \Illuminate\Http\Request();
                $objetoRequest ['url']= "jadwaldokter/kodepoli/". $ruangan."/tanggal/". date('Y-m-d',strtotime($request->tanggalreservasi));
                $objetoRequest ['jenis']= "antrean";
                $objetoRequest ['method']= "GET";
                $objetoRequest ['data']=null;
        
                $cek = app('App\Http\Controllers\Bridging\BridgingBPJSV2Controller')->bpjsTools($objetoRequest);
          
    
                if(is_array($cek)){
                    $code = $cek['metaData']->code;
                }else{
                    $cek = json_decode($cek->content(), true);
                    $code = $cek['metaData']['code'];
                }
    
                if($code != '200'){
                    $result = array("metadata"=>array("message" => "Pendaftaran ke Poli Ini Sedang Tutup", "code" => 201));
                    return $result;
                }else{
                    $ada = false;
                    if(count($cek['response']) > 0){
                        $dokter['jadwal'] = $cek['response'][0]->jadwal;
                        $dokter['namadokter'] = $cek['response'][0]->namadokter;
                        $dokter['kodedokter'] = $cek['response'][0]->kodedokter;
                        $ada = true;
                        // foreach($cek['response'] as $item){
                        //     if($request['kodedokter'] == $item->kodedokter){
                        //         $ada = true;
                        //         break;
                        //     }
                        // }
                    }
                    if($ada == false){
                        $result = array("metadata"=>array("message" => "Jadwal Dokter  Tersebut Belum Tersedia, Silahkan Reschedule Tanggal dan Jam Praktek Lainnya", "code" => 201));
                        return $result;
                    }
                }
            
              
                $norm = '';
                if($request->tipepasien == 'LAMA' ){
                    $norm = \DB::table('pasienmt')->where('id',$request->normidfk)->first()->norm;
                }
                $estimasidilayani = strtotime($request->tanggalreservasi) * 1000;
                $nomor = str_pad($request->noantrian, 3, '0', STR_PAD_LEFT);
                $json = array(
                    "kodebooking" => $request->noreservasi,
                    "jenispasien" => $request->kelompokpasienidfk == '2' ? "JKN": "NON JKN",
                    "nomorkartu" =>isset($request->nobpjs)? $request->nobpjs:'',
                    "nik" => $request->noidentitas?$request->noidentitas:"",
                    "nohp" => $request->notelepon?$request->notelepon:"",
                    "kodepoli" => $ruangan,
                    "namapoli" => $request->namaruangan,
                    "pasienbaru" => $request->tipepasien == 'LAMA' ? 0:1,
                    "norm" => $norm,
                    "tanggalperiksa" => date('Y-m-d',strtotime($request->tanggalreservasi)),
                    "kodedokter" => $dokter['kodedokter'],
                    "namadokter" => $dokter['namadokter'],
                    "jampraktek" => $dokter['jadwal'],
                    "jeniskunjungan" => 1,
                    "nomorreferensi" =>  $request->kelompokpasienidfk == '2' ? ($request->norujukan?$request->norujukan:""):"",
                    "nomorantrean" => $request->jenis . '-' . $nomor,
                    "angkaantrean" => 0,
                    "estimasidilayani" => $estimasidilayani,
                    "sisakuotajkn" => 0,
                    "kuotajkn" => 0,
                    "sisakuotanonjkn" => 0,
                    "kuotanonjkn" => 0,
                    "keterangan" => 'Peserta harap 30 menit lebih awal guna pencatatan administrasi.'
                );
              
          
                $objetoRequest = new \Illuminate\Http\Request();
                $objetoRequest ['url']= "antrean/add";
                $objetoRequest ['jenis']= "antrean";
                $objetoRequest ['method']= "POST";
                $objetoRequest ['data']= $json;
      
    
                $post = app('App\Http\Controllers\Bridging\BridgingBPJSV2Controller')->bpjsTools($objetoRequest);
                if(is_array($post)){
                    $code = $post['metaData']->code;
                    $message = $post['metaData']->message;
                }else{
                    $cek = json_decode($post->content(), true);
                    $code = $cek['metaData']['code'];
                    $message = $cek['metaData']['message'];
                }
    
                if($code != '200'){
                    $result = array("metadata"=>array("message" => "Add antrol gagal : ".$message, "code" => 201));
                    return $result;
                }else{
                    $result = array("metadata"=>array("message" =>$message, "code" => 200));
                    return $result;
                }
            
            } catch (\Exception $e) {
                $result = array("metadata"=>array("message" =>$e->getMessage().' ' .$e->getLine(), "code" => 201));
                return $result;
            }
    
        }
        function isJson($string)
        {
            json_decode($string);
            return json_last_error() === JSON_ERROR_NONE;
        }
}
