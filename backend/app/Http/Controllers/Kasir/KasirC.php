<?php


namespace App\Http\Controllers\Kasir;


use App\Http\Controllers\ApiController;
use App\Master\JenisKelamin;
use App\Master\KelompokTransaksi;
use App\Traits\InternalList;
use App\Traits\PelayananPasienTrait;
use App\Traits\SettingDataFixedTrait;
use App\Traits\Valet;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\LoggingUser;
use App\Transaksi\RegistrasiPasien;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\TransaksiPasienDetail;
use App\Transaksi\PetugasPelaksana;
use App\Transaksi\StrukBuktiPenerimaan;
use App\Transaksi\StrukBuktiPenerimaanCaraBayar;
use App\Transaksi\StrukBuktiPengeluaran;
use App\Transaksi\StrukPelayanan;
use App\Transaksi\StrukPelayananDetail;
use App\Transaksi\StrukPelayananPenjamin;
use App\Transaksi\StrukPelayananPenjaminDetail;
use App\Transaksi\StrukVerifikasi;
use App\Transaksi\TempBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class KasirC  extends ApiController
{
    use Valet, PelayananPasienTrait, SettingDataFixedTrait, InternalList;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getDataComboKasir(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->join('pegawaimt as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select('lu.objectpegawaifk', 'pg.namalengkap')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', (int)$kdProfile)
            ->first();

        $dataInstalasi = \DB::table('instalasimt as dp')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namadepartemen')
            ->get();

        $dataRuangan = \DB::table('ruanganmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataDokter = \DB::table('pegawaimt as ru')
            ->where('ru.aktif', true)
            ->where('ru.objectjenispegawaifk', 1)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namalengkap')
            ->get();

        foreach ($dataInstalasi as $item) {
            $detail = [];
            foreach ($dataRuangan as $item2) {
                if ($item->id == $item2->instalasiidfk) {
                    $detail[] = array(
                        'id' => $item2->id,
                        'ruangan' => $item2->namaruangan,
                    );
                }
            }

            $dataDepartemen[] = array(
                'id' => $item->id,
                'departemen' => $item->namadepartemen,
                'ruangan' => $detail,
            );
        }

        $dataKelompok = \DB::table('kelompokpasienmt as kp')
            ->select('kp.id', 'kp.kelompokpasien')
            ->where('kp.aktif', true)
            ->orderBy('kp.kelompokpasien')
            ->get();

        $dataKelas = \DB::table('kelasmt as kl')
            ->select('kl.id', 'kl.namakelas')
            ->where('kl.aktif', true)
            ->orderBy('kl.id')
            ->get();

        $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenNonRanap', $kdProfile));
        $kdDepartemenRawatInap = [];
        foreach ($deptRanap as $itemRanap) {
            $kdDepartemenRawatInap[] = (int)$itemRanap;
        }

        $dataRuanganNonInap = \DB::table('ruanganmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->whereIn('ru.instalasiidfk', $kdDepartemenRawatInap)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataDokter = \DB::table('ruanganmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->whereIn('ru.instalasiidfk', $kdDepartemenRawatInap)
            ->orderBy('ru.namaruangan')
            ->get();

        $kdRanap = (int) $this->settingDataFixed('kdDepartemenRanap', $kdProfile);
        $kdKelUserKasir =  (int) $this->settingDataFixed('kdKelUserKasir', $kdProfile);
        $dataCaraBayar = \DB::table('carabayarmt as ru')
            ->select('ru.id', 'ru.carabayar')
            ->where('ru.aktif', true)
            ->orderBy('ru.id')
            ->get();

        $jenisTransKasir = explode(',', $this->settingDataFixed('KdJenisTransaksiKasir', $kdProfile));
        $listjenisTransKasir = [];
        foreach ($jenisTransKasir as $itemTransKasir) {
            $listjenisTransKasir[] = (int)$itemTransKasir;
        }
        $dataJenisTransaksi = \DB::table('jenistransaksimt as ru')
            ->select('ru.id', 'ru.kelompoktransaksi')
            ->where('ru.aktif', true)
            ->whereIn('ru.id', $listjenisTransKasir)
            ->orderBy('ru.id')
            ->get();

        $dataPetugasKasir = \DB::select(
            DB::raw("
                SELECT pg.id,pg.namalengkap
                FROM loginuser_s lu
                INNER JOIN pegawaimt pg ON lu.objectpegawaifk=pg.id
                WHERE lu.kdprofile = $kdProfile AND objectkelompokuserfk=:id AND pg.aktif=true"),
            array(
                'id' => $kdKelUserKasir,
            )
        );

        $kdTransaksiNonLayanan = explode(',', $this->settingDataFixed('KdJenisTransaksiNonLayanan', $kdProfile));
        $listkdTransaksiNonLayanan = [];
        foreach ($kdTransaksiNonLayanan as $itemTrans) {
            $listkdTransaksiNonLayanan[] = (int)$itemTrans;
        }
        $JenisTransaksiNonLayanan = \DB::table('jenistransaksimt as ru')
            ->select('ru.id', 'ru.kelompoktransaksi')
            ->where('ru.aktif', true)
            ->whereIn('ru.id', $listkdTransaksiNonLayanan)
            ->orderBy('ru.id')
            ->get();

        $kdKelasNonKelas = (int) $this->settingDataFixed('kdKelasNonKelasRegistrasi', $kdProfile);
        $dataKelasNonKelas = \DB::table('kelasmt as kl')
            ->select('kl.id', 'kl.namakelas')
            ->where('kl.id', $kdKelasNonKelas)
            ->where('kl.aktif', true)
            ->orderBy('kl.namakelas')
            ->get();

        $jk = JenisKelamin::where('aktif', true)
            ->select(DB::raw("id, UPPER(jeniskelamin) as jeniskelamin"))
            ->where('koders', $kdProfile)
            ->get();

        $kdTransaksiNonLayananNonResep = explode(',', $this->settingDataFixed('KdJenisTransaksiNonLayananTindakan', $kdProfile));
        $listkdTransaksi = [];
        foreach ($kdTransaksiNonLayananNonResep as $itemT) {
            $listkdTransaksi[] = (int)$itemT;
        }
        $JenisTransaksiNonLayananNonResep = \DB::table('jenistransaksimt as ru')
            ->select('ru.id', 'ru.kelompoktransaksi')
            ->where('ru.aktif', true)
            ->whereIn('ru.id', $listkdTransaksi)
            ->orderBy('ru.id')
            ->get();

        $result = array(
            'dataLogin' => $dataPegawai,
            'departemen' => $dataDepartemen,
            'kelompokpasien' => $dataKelompok,
            'dokter' => $dataDokter,
            'datalogin' => $dataLogin,
            'kelas' => $dataKelas,
            'ruangannonianp' => $dataRuanganNonInap,
            'kdRanap' => $kdRanap,
            'carabayar' => $dataCaraBayar,
            'jenistransaksi' => $dataJenisTransaksi,
            'petugaskasir' => $dataPetugasKasir,
            'jenistransaksinonlayanan' => $JenisTransaksiNonLayanan,
            'NonKelas' => $dataKelasNonKelas,
            'jeniskelamin' => $jk,
            'jenistransaksitindakan' => $JenisTransaksiNonLayananNonResep,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function daftarPasienPulang(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftJoin('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftJoin('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->select(DB::raw("
                pd.norec AS norec_pd,pd.tglregistrasi,ps.norm,pd.noregistrasi,ru.namaruangan,
			    ps.namapasien,kp.kelompokpasien,pd.tglpulang,pd.statuspasien,pd.nostruklastidfk,
			    pd.nosbmlastidfk,pd.tglmeninggal,ps.nosuratkematian,dept.id AS deptid,pd.tglclosing
            "))
            ->where('pd.aktif', true)
            ->where('pd.koders', $kdProfile)
            ->whereNotNull('pd.tglpulang');

        $filter = $request->all();
        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
            $data = $data->where('pd.tglpulang', '>=', $filter['tglAwal']);
        }

        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
            $tgl = $filter['tglAkhir'];
            $data = $data->where('pd.tglpulang', '<=', $tgl);
        }

        if (isset($filter['instalasiId']) && $filter['instalasiId'] != "" && $filter['instalasiId'] != "undefined") {
            $data = $data->where('dept.id', '=', $filter['instalasiId']);
        }

        if (isset($filter['ruanganId']) && $filter['ruanganId'] != "" && $filter['ruanganId'] != "undefined") {
            $data = $data->where('ru.id', '=', $filter['ruanganId']);
        }

        if (isset($filter['namaPasien']) && $filter['namaPasien'] != "" && $filter['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['namaPasien'] . '%');
        }

        if (isset($filter['noReg']) && $filter['noReg'] != "" && $filter['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'ilike', '%' . $filter['noReg'] . '%');
        }

        if (isset($filter['noRm']) && $filter['noRm'] != "" && $filter['noRm'] != "undefined") {
            $data = $data->where('ps.nocm', 'ilike', '%' . $filter['noRm'] . '%');
        }
        if (isset($filter['kelompokPasienId']) && $filter['kelompokPasienId'] != "" && $filter['kelompokPasienId'] != "undefined") {
            $data = $data->where('kp.id', '=', $filter['kelompokPasienId']);
        }

        if (isset($filter['status']) && $filter['status'] != "" && $filter['status'] != "undefined") {
            if ($filter['status'] == 'Belum Verifikasi') {
                $data = $data->whereNull('pd.nostruklastfk')->whereNull('pd.nosbmlastidfk');
            } elseif ($filter['status'] == 'Verifikasi') {
                $data = $data->whereNotNull('pd.nostruklastfk')->whereNull('pd.nosbmlastidfk');
            } elseif ($filter['status'] == 'Lunas') {
                $data = $data->whereNotNull('pd.nostruklastfk')->whereNotNull('pd.nosbmlastidfk');;
            }
        }
        if (isset($filter['jmlRows']) && $filter['jmlRows'] != "" && $filter['jmlRows'] != "undefined") {
            $data = $data->take($filter['jmlRows']);
        }
        $data = $data->get();

        $result = array();
        foreach ($data as $pasienD) {
            $status = "-";
            $statusClosing = "-";
            if ($pasienD->nostruklastidfk == null && $pasienD->nosbmlastidfk == null) {
                $status = "Belum Verifikasi";
            } elseif ($pasienD->nostruklastidfk != null && $pasienD->nosbmlastidfk == null) {
                $status = "Verifikasi";
            } elseif ($pasienD->nostruklastidfk != null && $pasienD->nosbmlastidfk != null) {
                $status = '-';
            }
            if ($pasienD->tglclosing == null) {
                $statusClosing = "Belum Closing";
            } elseif ($pasienD->tglclosing != null) {
                $statusClosing = "Closing";
            }
            $result[] = array(
                'norec_pd'  => $pasienD->norec_pd,
                'tglregistrasi'  => $pasienD->tglregistrasi,
                'norm'  => $pasienD->norm,
                'noregistrasi'  => $pasienD->noregistrasi,
                'namaruangan'  => $pasienD->namaruangan,
                'namapasien'  => $pasienD->namapasien,
                'kelompokpasien'  => $pasienD->kelompokpasien,
                'tglpulang' => $pasienD->tglpulang,
                'tglmeninggal' =>  $pasienD->tglmeninggal,
                'status'    =>  $status,
                'deptid' => $pasienD->deptid,
                'tglclosing' => $pasienD->tglclosing,
                'nosuratkematian' => $pasienD->nosuratkematian,
                'statclosing' => $statusClosing
            );
        }
        return $this->respond($result);
    }

    public function getStrukPelayanan($noRegister, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->join('strukpelayanantr as sp', 'pd.nostruklastidfk', '=', 'sp.norec')
            ->select(DB::raw("
                pd.norec AS norec_pd,pd.noregistrasi,sp.norec AS norec_sp,sp.nosbmlastidfk,
			    sp.nostruk,sp.totalharusdibayar,sp.tglstruk
            "))
            ->whereNull('sp.nosbmlastidfk')
            ->where('pd.koders', $kdProfile)
            ->where('pd.aktif', true)
            ->whereNotNull('pd.tglpulang')
            ->where('pd.noregistrasi', $noRegister)
            ->orderBy('sp.tglstruk', 'desc')
            ->get();
        $result  = array(
            'data' => $data,
        );

        return $this->respond($result);
    }

    public function getDetailRegisrtasiPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $filter = $request->all();
        $Noregistrasi = " ";
        if (isset($filter['noregistrasi']) && $filter['noregistrasi'] != "" && $filter['noregistrasi'] != "undefined") {
            $dtNoregistrasi = $filter['noregistrasi'];
            $Noregistrasi = " AND pd.noregistrasi = '$dtNoregistrasi' ";
        }
        $datahead = DB::select(DB::raw("
                    SELECT apd.norec AS norec_apd,pd.norec AS norec_pd,pd.normidfk,pd.nostruklastidfk,ag.id AS agid,
                           ag.agama,pas.tgllahir,kp.id AS kpid,kp.kelompokpasien,
                           -- pas.ststusperkawinanidfk,
                           pas.namaayah,pas.namasuamiistri,pas.id AS pasid,
                           pas.norm,jkel.id AS jkelid,jkel.jeniskelamin,
                           pd.noregistrasi,pas.namapasien,pd.tglregistrasi,
                           pd.norec AS norec_pd,pd.tglpulang,pas.notelepon,pd.rekananidfk,
                           kls2.id AS klsid2,kls2.namakelas,rk.namarekanan,ru2.namaruangan,
                           sp.nostruk,sp.norec AS strukfk,pd.statuspasien,sp.nosbmlastidfk,pd.tglmeninggal,
                           pd.ruanganlastidfk,ru2.instalasiidfk
                    FROM registrasipasientr AS pd
                    INNER JOIN daftarpasienruangantr AS apd ON apd.registrasipasienfk = pd.norec
                    LEFT JOIN transaksipasientr AS pp ON pp.daftarpasienruanganfk = apd.norec
                    LEFT JOIN strukpelayanantr AS sp ON sp.norec = pp.strukfk
                    LEFT JOIN strukbuktipenerimaantr AS sbm ON sbm.norec = sp.nosbmlastidfk AND sbm.aktif = true
                    INNER JOIN pasienmt AS pas ON pas.id = pd.normidfk
                    LEFT JOIN agamamt AS ag ON ag.id = pas.agamaidfk
                    LEFT JOIN jeniskelaminmt AS jkel ON jkel.id = pas.jeniskelaminidfk
                    LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk
                    LEFT JOIN kelasmt AS kls2 ON kls2.id = pd.kelasidfk
                    INNER JOIN ruanganmt AS ru2 ON ru2.id = pd.ruanganlastidfk
                    LEFT JOIN rekananmt AS rk ON rk.id = pd.rekananidfk
                    LEFT JOIN kamarmt AS kamar ON kamar.id = apd.kamaridfk
                    WHERE pd.aktif = true AND apd.aktif = true
                    AND pd.koders = $kdProfile
                    $Noregistrasi
                    LIMIT 1"));

        $data = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'apd.pegawaiidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->leftJoin('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->leftJoin('kamarmt as km', 'km.id', '=', 'apd.kamaridfk')
            ->leftJoin('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->leftJoin('pasienmt as pm', 'pm.id', '=', 'pd.normidfk')
            ->leftJoin('tempattidurmt as tt', 'tt.id', '=', 'apd.nobedidfk')
            ->select(DB::raw("
                apd.norec,apd.registrasipasienfk as norec_pd,pd.tglregistrasi as tglregistrasi_pd,ru.id AS ruid_asal,ru.namaruangan,kls.id AS kelasid,kls.namakelas,
			    km.namakamar,tt.reportdisplay AS nobed,apd.statusantrian,apd.statuskunjungan,apd.tglregistrasi,
			    apd.tgldipanggildokter,apd.tgldipanggilsuster,pg.id AS pgid,pg.namalengkap AS namadokter,
			    apd.asalrujukanidfk,pd.nostruklastidfk,pd.nosbmlastidfk,apd.tglmasuk,apd.tglkeluar,pd.tglpulang,
			    apd.ruanganidfk,ru.instalasiidfk,dept.namadepartemen,pm.tglmeninggal
            "))
            ->where('pd.aktif', true)
            ->where('apd.aktif', true)
            ->where('pd.koders', $kdProfile);

        if (isset($filter['noregistrasi']) && $filter['noregistrasi'] != "" && $filter['noregistrasi'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $filter['noregistrasi']);
        }
        $data = $data->groupBy(DB::raw("
            apd.norec,apd.registrasipasienfk,pd.tglregistrasi,ru.id,ru.namaruangan,kls.id,kls.namakelas,km.namakamar,
            tt.reportdisplay,apd.statusantrian,apd.statuskunjungan,apd.tglregistrasi,apd.tgldipanggildokter,
            apd.tgldipanggilsuster,pg.id,pg.namalengkap,apd.asalrujukanidfk,pd.nostruklastidfk,pd.nosbmlastidfk,
            pd.noregistrasi,apd.tglmasuk,apd.tglkeluar,apd.ruanganidfk,ru.instalasiidfk,dept.namadepartemen,pm.tglmeninggal,pd.tglpulang
        "));
        $data = $data->orderBy('apd.tglmasuk', 'asc');
        $data = $data->get();

        $result = array(
            'datahead' => $datahead,
            'datadetail' => $data,
            'message' => 'godU',
        );
        return $this->respond($result);
    }

    public function simpanAntrianKonsultasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $kdKelasDefault = $this->settingDataFixed('kdDefaultKelasKonsultasi', $kdProfile);
        DB::beginTransaction();
        try {
            $pd = RegistrasiPasien::where('norec', $request['norec_pd'])->first();
            $dataAPD = new DaftarPasienRuangan();
            $dataAPD->norec = $dataAPD->generateNewId();
            $dataAPD->koders = $kdProfile;
            $dataAPD->aktif = true;
            $dataAPD->asalrujukanidfk = $pd->asalrujukanidfk;
            $dataAPD->kelasidfk = $kdKelasDefault;
            $dataAPD->noantrian = $request['noantrian'];
            $dataAPD->registrasipasienfk = $request['norec_pd'];
            $dataAPD->pegawaiidfk = $request['dokter'];
            $dataAPD->ruanganidfk = $request['ruangan'];
            $dataAPD->statusantrian = 0;
            $dataAPD->statuspasien = 1;
            $dataAPD->statuskunjungan = 'LAMA';
            $dataAPD->statuspenyakit = 'BARU';
            $dataAPD->ruanganasalidfk = $request['ruanganasal'];
            $dataAPD->tglregistrasi = $pd->tglregistrasi;
            $dataAPD->tglkeluar = date('Y-m-d H:i:s');
            $dataAPD->tglmasuk = date('Y-m-d H:i:s');
            $dataAPD->save();
            $dataAPDnorec = $dataAPD->norec;

            $transStatus = 'true';
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $dataAPD,
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

    public function simpanUpdateDokterAntrian(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        $transStatus = 'true';
        try {
            $data = DaftarPasienRuangan::where('norec', $request['norec_apd'])
                ->where('koders', $kdProfile)
                ->update(
                    [
                        'pegawaiidfk' => $request['pegawaiidfk']
                    ]
                );
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
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

    public function simpanUpdateRekananPD(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        $transStatus = 'true';
        try {

            $data = RegistrasiPasien::where('norec', $request['norec_pd'])
                ->where('koders', $kdProfile)
                ->update(
                    [
                        'rekananidfk' => $request['rekananidfk'],
                        'kelompokpasienlastidfk' => $request['kelompokpasienlastidfk'],
                    ]
                );

            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
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

    public function ubahTanggalDetailRegis(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {

            if ($request['istglregis'] == true ||  $request['istglregis'] == "true") {
                $updatePD = RegistrasiPasien::where('norec', $request['norec_pd'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'tglregistrasi' => $request['tglregistrasi']

                        ]
                    );
                $updatXoxoPD = DaftarPasienRuangan::where('norec', $request['norec_apd'])
                    ->update(
                        [
                            'tglregistrasi' => $request['tglregistrasi'],
                        ]
                    );
            }

            if ($request['istglmasuk'] == true || $request['istglmasuk'] == "true") {
                $updatXoxoPDs = DaftarPasienRuangan::where('norec', $request['norec_apd'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'tglmasuk' => $request['tglmasuk']
                        ]
                    );
            }
            if ($request['istglkeluar'] == true || $request['istglkeluar'] == "true") {
                $updatXoxossPD = DaftarPasienRuangan::where('norec', $request['norec_apd'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'tglkeluar' => $request['tglkeluar']

                        ]
                    );
            }
            if ($request['istglpulang'] == true || $request['istglpulang'] == "true") {
                $updatePDss = RegistrasiPasien::where('norec', $request['norec_pd'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'tglpulang' => $request['tglpulang']

                        ]
                    );
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            DB::commit();
            $ReportTrans = 'Selesai';
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'tb' => 'Xoxo',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message'  => $ReportTrans,
                'tb' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function hapusAntrianPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        $transStatus = 'true';
        if ($request['norec_apd'] != '') {
            try {
                $delApd = DaftarPasienRuangan::where('norec', $request['norec_apd'])
                    ->where('koders', $kdProfile)
                    ->update([
                        'aktif' => false,
                    ]);
                $ReportTrans = "Selesai";
            } catch (\Exception $e) {
                $transStatus = 'false';
                $ReportTrans = "Gagal, sudah ada tindakannya";
            }
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
    public function getStatusVerifPiutang(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->join('transaksipasientr as pp', 'apd.norec', '=', 'pp.daftarpasienruanganfk')
            ->leftjoin('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
            ->leftjoin('strukpelayananpenjamintr as spp', 'spp.nostrukidfk', '=', 'sp.norec')
            ->select(
                'pd.norec',
                'pd.noregistrasi',
                'pd.tglpulang',
                'pd.nostruklastidfk as nostruklastfk',
                'pd.nosbmlastidfk as nosbmlastfk',
                'spp.noverifikasi as noverif'

            )
            ->whereNotNull('pd.tglpulang')
            ->where('pd.noregistrasi', $request['noReg'])
            ->where('pd.koders', $kdProfile)
            ->first();
        return $this->respond($data);
    }

    public function HapusTglPulang(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();

        $pasienDaftar = RegistrasiPasien::where('noregistrasi', $request['noregistrasi'])->where('koders', $kdProfile)->first();
        try {
            $data =  \DB::table('registrasipasientr as pd')
                ->leftjoin('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
                ->leftjoin('transaksipasientr as pp', 'pp.daftarpasienruanganfk', '=', 'apd.norec')
                ->leftjoin('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
                ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
                ->select('ru.instalasiidfk as objectdepartemenfk')
                ->where('pd.koders', $kdProfile)
                ->where('pd.noregistrasi', $request['noregistrasi'])
                ->first();

            $stt = false;
            $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile));

            foreach ($deptRanap as $itemRanap) {
                if ($data->objectdepartemenfk == $itemRanap) {
                    $stt = true;
                }
                if ($data->objectdepartemenfk == $itemRanap) {
                    $stt = true;
                }
                if ($data->objectdepartemenfk == $itemRanap) {
                    $stt = true;
                }
                if ($data->objectdepartemenfk == $itemRanap) {
                    $stt = true;
                }
            }

            if ($stt == false) {
                $ddddd = [];
                $ReportTrans = "Bukan Pasien Rawat Inap!";
            } else {
                $antrian = DaftarPasienRuangan::where('registrasipasienfk', $pasienDaftar->norec)
                    ->where('koders', $kdProfile)
                    ->where('ruanganidfk', $pasienDaftar->ruanganlastidfk)
                    ->where('aktif', true)
                    ->orderBy('tglmasuk', 'desc')
                    ->first();

                if ($request['tglpulang'] == 'null') {
                    $ddddd = RegistrasiPasien::where('noregistrasi', $request['noregistrasi'])
                        ->where('koders', $kdProfile)
                        ->update(
                            [
                                'tglpulang' => null
                            ]
                        );
                    $apd = DaftarPasienRuangan::where('norec', $antrian->norec)
                        ->where('koders', $kdProfile)
                        ->update(['tglkeluar' => null]);
                } else {
                    $ddddd = RegistrasiPasien::where('noregistrasi', $request['noregistrasi'])
                        ->where('koders', $kdProfile)
                        ->update(
                            [
                                'tglpulang' => $request['tglpulang']
                            ]
                        );

                    $apd = DaftarPasienRuangan::where('norec', $antrian->norec)
                        ->where('koders', $kdProfile)
                        ->update(['tglkeluar' => null]);

                    $newId = LoggingUser::max('id');
                    $newId = $newId + 1;
                    $logUser = new LoggingUser();
                    $logUser->id = $newId;
                    $logUser->norec = $logUser->generateNewId();
                    $logUser->koders = $kdProfile;
                    $logUser->aktif = true;
                    $logUser->jenislog = 'Batal Pulang Pasien';
                    $logUser->noreff = $request['noregistrasi'];
                    $logUser->referensi = 'noregistrasi Pasien';
                    $logUser->loginuserfk =  $dataLogin['userData']['id'];
                    $logUser->tanggal = $tglAyeuna;
                    $logUser->save();
                }
                $ReportTrans = "Selesai";
            }




            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {

            DB::commit();
            $result = array(
                "status" => 201,
                "message" => "selesai",
                "struk" => $ddddd,
                "as" => 'slvR',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getLogin(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->select('lu.objectpegawaifk')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->first();
        $KelompokUser = DB::select(
            DB::raw("select ku.id,ku.kelompokuser
                    from loginuser_s as lu
                    INNER JOIN kelompokusermt as ku on lu.objectkelompokuserfk=ku.id
                    where lu.id =:IDIDID;"),
            array(
                'IDIDID' => $dataLogin['userData']['id'],
            )
        );

        $dataAPD = DB::select(
            DB::raw("
                select x.id,x.namaruangan,x.tglmasuk from (select  ru.id,ru.namaruangan ,apd.tglmasuk
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                INNER JOIN ruanganmt as ru on ru.id=apd.ruanganidfk
                where pd.norec=:noregistrasi

                 ) as x group by x.id,x.namaruangan,x.tglmasuk order by x.tglmasuk asc
            "),
            array(
                'noregistrasi' => $request['norec_pd'],
            )
        );

        $dataJenisPasien = DB::select(
            DB::raw("
                SELECT pd.kelompokpasienlastidfk,kp.kelompokpasien,pd.rekananidfk,rk.namarekanan
                FROM registrasipasientr AS pd
                LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk
                LEFT JOIN rekananmt AS rk ON rk.id = pd.rekananidfk
                WHERE pd.koders = $kdProfile AND pd.aktif = true AND pd.norec = :noregistrasi
            "),
            array(
                'noregistrasi' => $request['norec_pd'],
            )
        );

        $result = array(
            'datalogin' => $dataLogin,
            'kelompokuser' => $KelompokUser,
            'listRuangan' => $dataAPD,
            'pegawailoginfk' => $dataPegawai,
            'jenispasien' => $dataJenisPasien,
            'by' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getDataVerifikasiTagihan(Request $request)
    {
        $norec_pd = $request['norec_pd'];
        $kdProfile = (int) $this->getDataKdProfile($request);
        $iddeposit = (int) $this->settingDataFixed('kdPelayananDeposit', $kdProfile);
        $pelayanan = DB::select(
            DB::raw("
                    select pd.ruanganlastidfk,pd.nostruklastidfk,ps.id as psid,ps.norm,
				           ps.namapasien,pd.tglpulang,kps.kelompokpasien,kl.namakelas,
				           ru.instalasiidfk,pd.noregistrasi,pp.*
                    from registrasipasientr pd
                    left JOIN daftarpasienruangantr apd on apd.registrasipasienfk = pd.norec
                    left JOIN transaksipasientr pp on pp.daftarpasienruanganfk = apd.norec
                    left JOIN pasienmt ps on ps.id = pd.normidfk
                    left JOIN kelasmt kl on kl.id = pd.kelasidfk
                    left JOIN kelompokpasienmt kps on kps.id = pd.kelompokpasienlastidfk
                    left JOIN ruanganmt ru on ru.id = pd.ruanganlastidfk
                    where pd.aktif = TRUE AND apd.aktif = TRUE AND pp.aktif = TRUE
                    AND pd.koders = $kdProfile AND pd.norec=:norec_pd AND pp.strukfk is null
                    AND pp.produkidfk NOT IN ($iddeposit) ;"),
            array(
                'norec_pd' => $norec_pd,
            )
        );

        $pelayanantidakterklaim = DB::select(
            DB::raw("
            select pd.ruanganlastidfk,pd.nostruklastidfk,ps.id as psid,ps.norm,
                   ps.namapasien,pd.tglpulang,kps.kelompokpasien,kl.namakelas,
                   ru.instalasiidfk,pd.noregistrasi,pp.*
            from registrasipasientr pd
            left JOIN daftarpasienruangantr apd on apd.registrasipasienfk = pd.norec
            left JOIN transaksipasientidakterklaimtr pp on pp.daftarpasienruanganfk = apd.norec
            left JOIN pasienmt ps on ps.id = pd.normidfk
            left JOIN kelasmt kl on kl.id = pd.kelasidfk
            left JOIN kelompokpasienmt kps on kps.id = pd.kelompokpasienlastidfk
            left JOIN ruanganmt ru on ru.id = pd.ruanganlastidfk
            where pd.aktif = TRUE AND apd.aktif = TRUE AND pp.aktif = TRUE
            AND pd.koders = $kdProfile AND pd.norec=:norec_pd AND pp.strukfk is null
            ;"),
            array(
                'norec_pd' => $norec_pd,
            )
        );

        $deposit = collect(DB::select("
            SELECT tp.tglpelayanan,pr.namaproduk,tp.hargasatuan,tp.jumlah
            FROM registrasipasientr AS rg
            INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec
            INNER JOIN transaksipasientr AS tp ON tp.daftarpasienruanganfk = dpr.norec
            INNER JOIN pelayananmt AS pr ON pr.id = tp.produkidfk
            WHERE tp.strukfk IS NOT NULL AND tp.produkidfk = $iddeposit
            AND rg.norec = '$norec_pd'
        "));

        $totalBilling = 0;
        $totalKlaim = 0;
        $totalDeposit = 0;
        $totaltakterklaim = 0;

        if (count($deposit) > 0){
            foreach ($deposit as $value) {
                $totalDeposit = $totalDeposit + ((float) $value->jumlah *(float)$value->hargasatuan);
            }
        }

        foreach ($pelayanantidakterklaim as $values) {
            $totaltakterklaim = $totaltakterklaim + (($values->hargajual - $values->hargadiscount) * $values->jumlah) + $values->jasa;
        }

        foreach ($pelayanan as $value) {
//            if ($value->produkidfk == $iddeposit) {
//                $totalDeposit = $totalDeposit + $value->hargajual;
//            } else {
                $totalBilling = $totalBilling + (($value->hargajual - $value->hargadiscount) * $value->jumlah) + $value->jasa;
//            }
        }

        $totalBilling = $totalBilling;
        $isRawatInap = false;
        $result = [];
        if ($pelayanan != null) {
            $pelayanan = $pelayanan[0];
            if ($pelayanan->ruanganlastidfk != null) {
                if ((int)$pelayanan->instalasiidfk == 16) {
                    $isRawatInap = true;
                }
            }
            $totalDeposit = $totalDeposit;
            $totalKlaim = 0;
            $result = array(
                'pasienID' => $pelayanan->psid,
                'noCm' => $pelayanan->norm,
                'noRegistrasi' => $pelayanan->noregistrasi,
                'namaPasien' => $pelayanan->namapasien,
                'tglPulang' => $pelayanan->tglpulang,
                'jenisPasien' => $pelayanan->kelompokpasien,
                'kelasRawat' => $pelayanan->namakelas,
                'noAsuransi' => '-',
                'kelasPenjamin' => '-',
                'billing' => $totalBilling,
                'penjamin' => '',
                'deposit' => $totalDeposit,
                'totalKlaim' => $totalKlaim,
                'jumlahBayar' => $totalBilling - $totalDeposit - $totalKlaim,
                'jumlahBayarNew' =>  $totalBilling - $totalDeposit - $totalKlaim - $totaltakterklaim,
                'jumlahPiutang' => 0,
                'needDokument' => true,
                'dokuments' => [],
                'totaltakterklaim' => $totaltakterklaim,
                'isRawatInap' => $isRawatInap,
            );
        }

        return $this->respond($result);
    }

    public function detailTagihanVerifikasi(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $norec_pd = $request['norec_pd'];
        $deposit = (int) $this->settingDataFixed('kdPelayananDeposit', $kdProfile);
        $dataRuangan = \DB::table('registrasipasientr as pd')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->select('ru.namaruangan as namaruangan')
            ->where('pd.koders', $kdProfile)
            ->where('pd.norec', $norec_pd)
            ->first();

        $pelayanan = [];
        $pelayanan = \DB::table('registrasipasientr as pd')
            ->leftjoin('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('transaksipasientr as pp', 'pp.daftarpasienruanganfk', '=', 'apd.norec')
            ->leftjoin('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
            ->leftjoin('kelasmt as kl', 'kl.id', '=', 'apd.kelasidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->leftjoin('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
            ->leftjoin('strukbuktipenerimaantr as sbm', 'sp.nosbmlastidfk', '=', 'sbm.norec')
            ->select(DB::raw("
                pp.norec,pp.tglpelayanan,pp.rke,pr.id AS prid,pr.namaproduk,pp.jumlah,
			    kl.id AS klid,kl.namakelas,ru.id AS ruid,ru.namaruangan,pp.produkidfk,
			    pp.hargajual,pp.hargadiscount,sp.nostruk,sp.tglstruk,apd.norec AS norec_apd,
			    sbm.nosbm,sp.norec AS norec_sp,pp.jasa,pd.normidfk,pd.nostruklastidfk,pd.noregistrasi,
			    pd.tglregistrasi,pd.norec AS norec_pd,pd.tglpulang,pd.rekananidfk AS rekananid,
			    pp.jasa,sp.totalharusdibayar,sp.totalprekanan,sp.totalbiayatambahan,pd.koders,
			    pp.aturanpakai,pp.iscito,pd.statuspasien,pp.isparamedis,pp.iskronis
            "))
            ->where('pd.koders', $kdProfile)
            ->where('pd.aktif', true)
            ->where('apd.aktif', true)
            ->where('pp.aktif', true)
            ->where('pd.norec', $norec_pd);

        if (isset($request['idruangan']) && $request['idruangan'] != "" && $request['idruangan'] != "undefined") {
            $pelayanan = $pelayanan->where('apd.ruanganidfk', '=', $request['idruangan']);
        }

        $pelayanan = $pelayanan->get();
        $dataTotaldibayar = DB::select(
            DB::raw("
                select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk = pd.norec
                INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk = apd.norec
                INNER JOIN strukpelayanantr as sp on sp.norec = pp.strukfk
                where pd.norec=:norec_pd and sp.nosbmlastidfk is not null and pp.produkidfk not in ($deposit);
            "),
            array(
                'norec_pd' => $norec_pd,
            )
        );

        $deposit = collect(DB::select("
            SELECT tp.tglpelayanan,pr.namaproduk,tp.hargasatuan,tp.jumlah
            FROM registrasipasientr AS rg
            INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec
            INNER JOIN transaksipasientr AS tp ON tp.daftarpasienruanganfk = dpr.norec
            INNER JOIN pelayananmt AS pr ON pr.id = tp.produkidfk
            WHERE tp.strukfk IS NOT NULL AND tp.produkidfk = $deposit
            AND rg.norec = '$norec_pd'
        "));

        $totalDeposit = 0;
        if (count($deposit) > 0){
            foreach ($deposit as $value) {
                $totalDeposit = $totalDeposit + ((float) $value->jumlah *(float)$value->hargasatuan);
            }
        }

        $dibayar = 0;
        $dibayar = $dataTotaldibayar[0]->total;
        $details = [];
        if (count($pelayanan) > 0) {
            $totalBilling = 0;
            $norecAPD = '';
            $norecSP = '';
            $details = array();
            $diverif = 0;
            foreach ($pelayanan as $value) {
                if ($value->produkidfk == $this->getProdukIdDeposit()) {
                    continue;
                }
                if ($value->namaproduk == null) {
                    continue;
                }
                $jasa = 0;
                if (isset($value->jasa) && $value->jasa != "" && $value->jasa != "undefined") {
                    $jasa = $value->jasa;
                }
                $kmpn = [];

                $harga = (float)$value->hargajual;
                $diskon = (float)$value->hargadiscount;
                $detail = array(
                    'norec' => $value->norec,
                    'tglPelayanan' => $value->tglpelayanan,
                    'namaPelayanan' => $value->namaproduk,
                    'jumlah' => $value->jumlah,
                    'kelasTindakan' => @$value->namakelas,
                    'ruanganTindakan' => @$value->namaruangan,
                    'harga' => $harga,
                    'diskon' => $diskon,
                    'total' => (($harga - $diskon) * $value->jumlah) + $jasa,
                    'jppid' => '',
                    'jenispetugaspe' => '',
                    'strukfk' => $value->nostruk . ' / ' . $value->nosbm,
                    'sbmfk' => $value->nosbm,
                    'pgid' => '',
                    'ruid' => $value->ruid,
                    'prid' => $value->prid,
                    'klid' => $value->klid,
                    'norec_apd' => $value->norec_apd,
                    'norec_pd' => $value->norec_pd,
                    'norec_sp' => $value->norec_sp,
                    'komponen' => $kmpn,
                    'jasa' => $jasa,
                    'aturanpakai' => $value->aturanpakai,
                    'iscito' => $value->iscito,
                    'isparamedis' => $value->isparamedis,
                    'iskronis' => $value->iskronis,
                    'totaldibayar' => $dibayar,
                    'deposit' => $totalDeposit,
                );
                $details[] = $detail;
            }
        }

        $arrHsil = array(
            'details' => $details
        );
        return $this->respond($arrHsil);
    }

    public function simpanVerifikasiTagihanTatarekening(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $norec_pd = $request['norec_pd'];
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.kdprofile = $kdProfile
                and lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        $transMsg = null;
        $totalBilling = 0;
        $totalDeposit = 0;
        $totalKlaim = 0;
        DB::beginTransaction();
        try {
            $pasienDaftar = RegistrasiPasien::where('norec', $norec_pd)
                ->where('koders', $kdProfile)
                ->first();
            $pelayanan =  DB::select(DB::raw("
            select pp.*
            from registrasipasientr as pd
            inner join daftarpasienruangantr as apd on apd.registrasipasienfk = pd.norec
            INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk = apd.norec
            where pd.norec='$norec_pd' and pp.strukfk is null
            and pd.koders = $kdProfile
         "));
            $SPPenjamin = new StrukPelayananPenjamin();
            $pelayananDetail = $pasienDaftar->transaksi_pasien_detail()->whereNull('strukidfk')
                ->where('transaksipasiendetailtr.koders', $kdProfile)
                ->get();
            if (count($pelayanan) == 0) {
                $transMsg = "Pelayanan yang dilakukan pasien tidak ada.";
            }
            if (count($dataLogin['details']) == 0) {
                $transMsg = "Pelayanan blm ada yg di pilih";
            }
            $noStruk = $this->generateCode(new StrukPelayanan, 'nostruk', 10, 'VRF', $kdProfile);
            $strukPelayanan = new StrukPelayanan();
            $strukPelayanan->norec = $strukPelayanan->generateNewId();
            $lastPelayanan = null;

            $sama = false;
            foreach ($pelayanan as $pel) {
                $sama = false;
                foreach ($dataLogin['details'] as $chklist) {
                    if ($chklist['norec'] == $pel->norec) {
                        $sama = true;
                        break;
                    }
                }
                if ($sama == true) {
                    $harga = ($pel->hargajual == null) ? 0 : $pel->hargajual;
                    $diskon = ($pel->hargadiscount == null) ? 0 : $pel->hargadiscount;
                    if ($pel->nilainormal == -1) {
                        $totalDeposit += ($harga * $pel->jumlah);
                    } else {
                        $totalBilling += (($harga - $diskon) * $pel->jumlah) + $pel->jasa;
                    }
                }
            }
            $totalBilling = (float)$request['totalBayar'];
            $strukPelayanan->koders = $kdProfile;
            $strukPelayanan->aktif = true;
            $strukPelayanan->normidfk = $pasienDaftar->normidfk;
            $strukPelayanan->registrasipasienfk = $pasienDaftar->norec;
            $strukPelayanan->kelaslastidfk = $pasienDaftar->kelasidfk;
            $strukPelayanan->kelompoktransaksiidfk = 1;
            $strukPelayanan->pegawaipenerimaidfk = $dataPegawaiUser[0]->id;
            $strukPelayanan->nostruk = $noStruk;
            $strukPelayanan->totalharusdibayar = $totalBilling;
            $strukPelayanan->tglstruk = $this->getDateTime();
            $strukPelayanan->ruanganidfk = $pasienDaftar->ruanganlastidfk;

            $strukPelayanan->save();
            foreach ($dataLogin['details'] as $chklist) {
                TransaksiPasien::where('norec', $chklist['norec'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'strukfk' => $strukPelayanan->norec
                        ]
                    );
            }

            foreach ($pelayananDetail as $pelDel) {
                $pelDel->strukidfk = $strukPelayanan->norec;
                $pelDel->save();
            }

            $totalKlaim = (float)$request['totalKlaim'];
            if ($totalKlaim > 0) {
                $strukPelayanan->totalprekanan = $totalKlaim;
                if ($pasienDaftar->kelompokpasienlastidfk == $this->getKelompokPasienPerjanjian()) {
                    $rekananpenjamin_id = 0;
                } elseif ($pasienDaftar->kelompokpasienlastidfk == 2 || $pasienDaftar->kelompokpasienlastidfk == 4) {
                    $rekananpenjamin_id = 2552;
                } else {
                    $rekananpenjamin_id = 0;
                }
                $SPPenjamin->norec = $SPPenjamin->generateNewId();
                $SPPenjamin->aktif = true;
                $SPPenjamin->koders = $kdProfile;
                $SPPenjamin->kdkelompokpasien = $pasienDaftar->kelompokpasienlastidfk;
                $SPPenjamin->kdrekananpenjamin = $rekananpenjamin_id;
                $SPPenjamin->totalbiaya = $totalBilling + $totalKlaim + $totalDeposit;
                $SPPenjamin->totalsudahppenjamin = $totalKlaim; //? apa in ?
                $SPPenjamin->totalsisaharusdibayar = $totalKlaim;
                $SPPenjamin->totalppenjamin = $totalKlaim;
                $SPPenjamin->totalharusdibayar = $totalKlaim;
                $SPPenjamin->totalsudahdibayar = 0;
                $SPPenjamin->totalsudahdibebaskan = 0;
                $SPPenjamin->totalsisapiutang = $totalKlaim;
                $SPPenjamin->totaldibayarlebih = 0;
                $SPPenjamin->nostrukidfk = $strukPelayanan->norec;
                $pasienDaftar->nostruklastidfk = $strukPelayanan->norec;
                $SPPenjamin->save();

                if ($request['multipenjamin'] != null) {
                    $reqDetail = $request['multipenjamin'];
                    foreach ($reqDetail as $values) {
                        $SPPenjaminDet = new StrukPelayananPenjaminDetail();
                        $SPPenjaminDet->norec = $SPPenjaminDet->generateNewId();
                        $SPPenjaminDet->aktif = true;
                        $SPPenjaminDet->koders = $kdProfile;
                        $SPPenjaminDet->nostrukidfk = $strukPelayanan->norec;
                        $SPPenjaminDet->totalppenjamin = $values['klaim'];
                        $SPPenjaminDet->totalharusdibayar = $values['klaim'];
                        $SPPenjaminDet->keteranganlainnya = 'Multi Penjamin';
                        $SPPenjaminDet->kdrekananpenjaminidfk = $values['rekananfk'];
                        $SPPenjaminDet->strukpelayananpenjaminidfk = $SPPenjamin->norec;
                        $SPPenjaminDet->kelompokpasienidfk = $values['kelompokpasienfk'];
                        $SPPenjaminDet->save();
                    }
                }
            }
            $pasienDaftar->nostruklastidfk = $strukPelayanan->norec;
            $pasienDaftar->save();
            $strukPelayanan->save();

            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'result' => $strukPelayanan,
                'tb' => 'godU',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message'  => $ReportTrans,
                'e'  => $e->getMessage() . ' ' . $e->getLine(),
                'tb' => 'godU',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDataDetailVerifikasi(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $noRegistrasi = $request['noRegistrasi'];
        $result = [];
        $data = \DB::table('registrasipasientr as pd')
            ->join('strukpelayanantr as sp', 'sp.registrasipasienfk', '=', 'pd.norec')
            ->leftJOIN(
                'strukbuktipenerimaantr as sbm',
                'sbm.nostrukidfk',
                '=',
                \DB::raw('sp.norec AND sbm.aktif = true')
            )
            ->join('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaipenerimaidfk')
            ->leftJoin('pegawaimt as pg1', 'pg1.id', '=', 'sbm.pegawaipenerimaidfk')
            ->leftJoin('strukpelayananpenjamintr as spp', 'spp.nostrukidfk', '=', 'sp.norec')
            ->select(\DB::raw("
                pd.noregistrasi,sp.norec,sp.nostruk,sp.tglstruk,sp.totalharusdibayar,
			    pg.namalengkap AS petugasverif,sbm.tglsbm,
			    CASE WHEN sp.nosbmlastidfk IS NULL THEN 'Belum Bayar' ELSE 'Lunas' END AS status,
			    CASE WHEN sp.nosbmlastidfk IS NULL THEN NULL ELSE pg1.namalengkap END AS kasir,
			    spp.norec AS norec_piutang,pd.kelompokpasienlastidfk,sp.nosbmlastidfk,spp.noverifikasi
            "))
            ->where('pd.koders', $kdProfile)
            ->where('pd.aktif', true)
            ->where('sp.aktif', true)
            ->where('pd.noregistrasi', '=', $noRegistrasi)
            ->groupBy(DB::raw("
                 pd.noregistrasi,sp.norec,sp.nostruk,sp.tglstruk,sp.totalharusdibayar,
				 pg.namalengkap,sbm.tglsbm,sp.nosbmlastidfk,pg1.namalengkap,spp.norec,
				 pd.kelompokpasienlastidfk,sp.nosbmlastidfk,spp.noverifikasi
            "))
            ->get();

        foreach ($data as $item) {
            $details = \DB::select(
                DB::raw("
                   
                        SELECT sbm.norec,sbm.tglsbm,sbm.nosbm,cb.carabayar,sbm.totaldibayar,pg.namalengkap as kasir
                        FROM strukbuktipenerimaantr as sbm
                        INNER JOIN strukbuktipenerimaancarabayartr as sbmc on sbmc.nosbmidfk = sbm.norec
                        INNER JOIN carabayarmt as cb on cb.id = sbmc.carabayaridfk
                        INNER JOIN loginuser_s as lu on lu.id = sbm.pegawaipenerimaidfk
                        INNER JOIN pegawaimt as pg on pg.id = lu.objectpegawaifk
                        WHERE sbm.koders = $kdProfile and sbm.aktif = true AND sbm.nostrukidfk=:norec

                "),
                array(
                    'norec' => $item->norec,
                )
            );

            $result[] = array(
                'norec' => $item->norec,
                'nostruk' => $item->nostruk,
                'tglstruk' => $item->tglstruk,
                'totalharusdibayar' => $item->totalharusdibayar,
                'petugasverif' => $item->petugasverif,
                'tglsbm' => $item->tglsbm,
                'status' => $item->status,
                'kasir' => $item->kasir,
                'norec_piutang' => $item->norec_piutang,
                'objectkelompokpasienlastfk' => $item->kelompokpasienlastidfk,
                'nosbmlastfk' => $item->nosbmlastidfk,
                'noregistrasi' => $item->noregistrasi,
                'noverifikasi' => $item->noverifikasi,
                'details' => $details,
            );
        }

        if (count($data) == 0) {
            $result = [];
        }

        $result = array(
            'data' => $result,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function batalVerifikasiTagihan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $transMsg = null;
        $totalBilling = 0;
        $totalDeposit = 0;
        $noRegister = $request['noregistrasi'];
        $NorecSP = $request['norec_sp'];
        \DB::beginTransaction();
        try {
            $dataSP = \DB::table('registrasipasientr as pd')
                ->leftJoin('strukpelayanantr as sp', 'sp.registrasipasienfk', '=', 'pd.norec')
                ->select(DB::raw("
                    sp.norec,sp.nostruk,pd.norec AS norec_pd,pd.noregistrasi
                "))
                ->where('pd.koders', $kdProfile)
                ->where('pd.aktif', true)
                ->where('sp.aktif', true)
                ->where('pd.noregistrasi', '=', $noRegister)
                ->where('sp.norec', '=', $NorecSP)
                ->where('sp.nosbmlastidfk', '=', null)
                ->get();
            foreach ($dataSP as $item) {
                $nostrukPelayananVerifikasi = $NorecSP;
                $norec_pd = $item->norec_pd;
                $sbm = StrukBuktiPenerimaan::where('nostrukidfk', $nostrukPelayananVerifikasi)->first();
                $sbk = StrukBuktiPengeluaran::where('nostrukidfk', $nostrukPelayananVerifikasi)->first();
                if ($sbm  || $sbk) {
                    $transStatus = false;
                    $transMsg = "Tagihan ini sudah ada pembayarannya";
                }
                $data2 = TransaksiPasien::where('strukfk', $nostrukPelayananVerifikasi)
                    ->where('produkidfk', 10011572)
                    ->delete();
                $data1 = TransaksiPasien::where('strukfk', $nostrukPelayananVerifikasi)
                    ->update([
                        'strukfk' => null,
                    ]);
                $data4 = TransaksiPasienDetail::where('strukidfk', $nostrukPelayananVerifikasi)
                    ->update([
                        'strukidfk' => null,
                    ]);
                $data5 = StrukPelayananPenjamin::where('nostrukidfk', $nostrukPelayananVerifikasi)
                    ->update([
                        'aktif' => false
                    ]);

                $data6 = RegistrasiPasien::where('norec', $norec_pd)
                    ->where('nostruklastidfk', $NorecSP)
                    ->update([
                        'nostruklastidfk' => null,
                    ]);

                $data7 = StrukPelayanan::where('norec', $nostrukPelayananVerifikasi)
                    ->update([
                        'aktif' => false,
                    ]);
            }

            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus == 'true') {
            DB::commit();
            $ReportTrans = 'Selesai';
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $dataSP,
                "as" => 'godU',
                "edited" => 'slvR'
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => 'Gagal coba lagi',
                "data" => $dataSP,
                "as" => 'slvR',
                "edited" => 'slvR'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function closePemeriksaanPD(Request $request)
    {
        \DB::beginTransaction();
        $kdProfile = $this->getDataKdProfile($request);
        $transStatus = 'true';
        try {
            $status = true;
            $tglClose = date('Y-m-d H:i:s');
            if ($request['close'] == false) {
                $status = null;
                $tglClose = null;
            }
            $data = RegistrasiPasien::where('noregistrasi', $request['noregistrasi'])
                ->where('koders', $kdProfile)
                ->update([
                    'isclosing' => $status,
                    'tglclosing' => $tglClose
                ]);
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
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

    public function detailTagihanPasienLayanan(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $strukPelayanan = StrukPelayanan::where('norec', $request->norec_sp)
            ->where('koders', $kdProfile)
            ->where('aktif', true)
            ->first();
        $pasienDaftar = RegistrasiPasien::where('norec', $strukPelayanan->registrasipasienfk)
            ->where('koders', $kdProfile)
            ->where('aktif', true)
            ->first();
        if ($strukPelayanan) {
        }
        $pelayanan_pasien = $strukPelayanan->transaksi_pasien;
        $deposit = 0;
        $detailTagihan = array();
        foreach ($pelayanan_pasien as $value) {
            $harga =  ($value->hargajual == null) ? 0 : $value->hargajual;
            $diskon = ($value->hargadiscount == null) ? 0 : $value->hargadiscount;
            if ($value->nilainormal == -1) {
                $deposit += $harga;
            } else {
                $detailTagihan[] = array(
                    'namaLayanan'  => $value->produk->namaproduk,
                    "ruangan" => @$value->daftar_pasien_ruangan->ruangan->namaruangan,
                    'jumlah'  => $value->jumlah,
                    'harga'  => (float) $harga,
                    'diskon'  => $diskon,
                    'total'  => ($harga - $diskon) * $value->jumlah,
                );
            }
        }
        $noregistasi = $pasienDaftar->noregistrasi;
        $result = array(
            "totalDeposit"  => $this->getDepositPasien($noregistasi),
            "jumlahBayar"  => $strukPelayanan->totalharusdibayar,
            "totalPenjamin" => ($strukPelayanan->totalprekanan == null) ? 0 : $strukPelayanan->totalprekanan,
            "detailTagihan"  => $detailTagihan,
        );
        return $this->respond($result, "Detail Tagihan Pasien");
    }

    public function getDataPembayaran(Request $request)
    {
        switch ($request['tipePembayaran']) {
            case 'depositPasien':
                return $this->getDetailPembayaranDeposit($request);
                break;
            case 'tagihanPasien':
                return $this->getDetailTagihanPasien($request);
                break;
            case 'cicilanPasien':
                return $this->getDetailCicilanPasien($request);
                break;
            case 'pembayaranNonLayanan':
                return $this->getDetailPembayaranNonLayanan($request);
            case 'PenyetoranDepositKasirKembali':
                return $this->getDetailPenyerahanKembaliDeposit($request);
            default:
                return 0;
        }
    }

    protected function getDetailPembayaranDeposit(Request $request)
    {
        $result = array(
            'jumlahBayar' => $request['jumlahBayar'],
            'tipePembayaran' => $request['tipePembayaran'],
            'norec_pd' => $request['noRecStrukPelayanan'],
        );
        return $this->respond($result, "Data Pembayaran");
    }

    protected function getDetailTagihanPasien(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $norec_sp = $request['noRecStrukPelayanan'];
        $strukPelayanan = StrukPelayanan::where('norec', $request['noRecStrukPelayanan'])->where('koders', $kdProfile)->first();
        $dataPasien = DB::select(DB::raw("
            SELECT 'Pembayaran Tagihan Pasien A/N ' || ps.namapasien AS pasien
            FROM strukpelayanantr AS sp
            INNER JOIN registrasipasientr AS pd ON pd.norec = sp.registrasipasienfk
            INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk
            WHERE sp.norec = '$norec_sp' LIMIT 1
        "));
        $totalBilling = $strukPelayanan->totalharusdibayar;
        $totalBayar = $totalBilling;
        $result = array(
            "pasien" => $dataPasien[0]->pasien,
            "noRecStrukPelayanan"  => $strukPelayanan->norec,
            "jumlahBayar"  => $totalBayar,
        );
        return $this->respond($result, "Data Pembayaran");
    }

    protected  function getDetailCicilanPasien(Request $request)
    {
        $result = array(
            "noRecStrukPelayanan"  => $request['noRecStrukPelayanan'],
            "jumlahBayar"  => $request['jumlahBayar'],
        );
        return $this->respond($result, "Data Pembayaran");
    }

    protected function getDetailPenyerahanKembaliDeposit(Request $request)
    {
        $result = array(
            'jumlahBayar' => $request['jumlahBayar'],
            'tipePembayaran' => $request['tipePembayaran'],
            'norec_pd' => $request['noRecStrukPelayanan'],
        );
        return $this->respond($result, "Data Pembayaran");
    }

    public function simpanPembayaran(Request $request)
    {
        switch ($request['parameterTambahan']['tipePembayaran']) {
            case 'depositPasien':
                return $this->simpanPembayaranDeposit($request);
                break;
            case 'PenyetoranDepositKasirKembali':
                return $this->simpanPengembalianDeposit($request);
                break;
            case 'tagihanPasien':
                return $this->simpanPembayaranTagihanPasien($request);
                break;
            case 'cicilanPasien':
                return $this->simpanCicilanPasien($request);
                break;
            case 'pembayaranNonLayanan':
                return $this->simpanPembayaranTagihanNonLayanan($request);
                break;
            case 'cicilanPasienCollect':
                return $this->simpanCicilanPasienCollect($request);
                break;
            default:
                return 0;
        }
    }

    protected function simpanPembayaranTagihanPasien(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.kdprofile = $kdProfile
                and lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        $noRegistrasi = '';
        \DB::beginTransaction();
        try {
            $strukPelayanan = StrukPelayanan::where('norec', $request['parameterTambahan']['noRecStrukPelayanan'])->first();
            $sisa = 0;
            if ($strukPelayanan->nosbmlastfk == null || $strukPelayanan->nosbmlastfk == '') {
                $sisa = $sisa + $this->getDepositPasien($strukPelayanan->pasien_daftar->noregistrasi);
            }

            $deposit = $sisa;
            $sisa = $sisa + $request['jumlahBayar'];

            $NOSBM = array();
            foreach ($request['pembayaran'] as $pembayaran) {
                $strukBuktiPenerimanan = new StrukBuktiPenerimaan();
                $strukBuktiPenerimanan->norec = $strukBuktiPenerimanan->generateNewId();
                $strukBuktiPenerimanan->koders = $kdProfile;
                $strukBuktiPenerimanan->aktif = true;
                $strukBuktiPenerimanan->keteranganlainnya = "Pembayaran Tagihan Pasien";
                $strukBuktiPenerimanan->nostrukidfk = $strukPelayanan->norec;
                $strukBuktiPenerimanan->kelompokpasienidfk = $strukPelayanan->pasien_daftar->kelompokpasienlastidfk;
                $strukBuktiPenerimanan->kelompoktransaksiidfk = 1;
                $strukBuktiPenerimanan->pegawaipenerimaidfk = $dataPegawaiUser[0]->id;
                $strukBuktiPenerimanan->tglsbm = $request['tglsbm']; //$this->getDateTime();
                $strukBuktiPenerimanan->totaldibayar = $pembayaran['nominal'];
                $strukBuktiPenerimanan->nosbm = $this->generateCode(new StrukBuktiPenerimaan, 'nosbm', 14, 'IVN-' . $this->getDateTime()->format('ym'), $kdProfile);
                $strukBuktiPenerimanan->save();
                $NOSBM[] = $strukBuktiPenerimanan->nosbm;
                $norecSBM = $strukBuktiPenerimanan->norec;
                $nostrukfkSTR = $strukPelayanan->norec;

                $this->transMessage = "Simpan Pembayaran Gagal {SBP}";

                if ($this->transStatus) {
                    $SBPCB = new StrukBuktiPenerimaanCaraBayar();
                    $SBPCB->norec = $SBPCB->generateNewId();
                    $SBPCB->koders = $kdProfile;
                    $SBPCB->aktif = 1;
                    $SBPCB->nosbmidfk = $strukBuktiPenerimanan->norec;
                    $SBPCB->carabayaridfk = $pembayaran['caraBayar']['id'];
                    $SBPCB->totaldibayar = $pembayaran['nominal'];
                    $SBPCB->save();
                }
            }

            $strukPelayanan->nosbmlastidfk = $strukBuktiPenerimanan->norec;
            $strukPelayanan->save();
            $pd = $strukPelayanan->pasien_daftar;
            $pd->nosbmlastidfk = $strukBuktiPenerimanan->norec;
            $noRegistrasi = $pd['noregistrasi'];
            $pd->save();

            $this->transStatus = true;
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $this->transStatus = false;
            $ReportTrans = "Gagal coba lagi";
        }

        if ($this->transStatus) {
            DB::commit();
            $result = array(
                "status" => 201,
                'noSBM' => $NOSBM,
                'norec' => $norecSBM,
                'noReg' => $noRegistrasi,
                "message" => $ReportTrans,
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
                "exp" => $e->getMessage() . ' ' . $e->getLine()
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getListRuangan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataAPD = DB::select(
            DB::raw("
            select x.id,x.namaruangan,x.tglmasuk from (select  ru.id,ru.namaruangan ,apd.tglmasuk
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                INNER JOIN ruanganmt as ru on ru.id=apd.ruanganidfk
                where pd.noregistrasi=:noregistrasi
                and pd.aktif =true
                and apd.aktif=true
                and pd.koders=$kdProfile
                  ) as x group by x.id,x.namaruangan,x.tglmasuk order by x.tglmasuk asc
            "),
            array(
                'noregistrasi' => $request['noRegistrasi'],
            )
        );
        $lastRuangan = collect(DB::select("
                 select  ru.id,ru.namaruangan
                from registrasipasientr as pd
                INNER JOIN ruanganmt as ru on ru.id=pd.ruanganlastidfk
                where pd.noregistrasi='$request[noRegistrasi]'
                and pd.aktif =true
                and pd.koders=$kdProfile
            "))->first();

        $result = array(
            'listRuangan' => $dataAPD,
            'lastRuangan' => $lastRuangan,
            'tb' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function detailTagihan($noRegister, Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $delete = TempBilling::where('noregistrasi', $noRegister)->where('koders', $kdProfile)->delete();
        $dataRuangan = \DB::table('registrasipasientr as pd')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->select('ru.namaruangan')
            ->where('pd.koders', $kdProfile)
            ->where('pd.aktif', true)
            ->where('pd.noregistrasi', $noRegister)
            ->first();
        //        dd($dataRuangan);
        $pelayanan = [];
        $pelayanan = \DB::table('registrasipasientr as pd')
            ->leftjoin('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('transaksipasientr as pp', 'pp.daftarpasienruanganfk', '=', 'apd.norec')
            ->leftjoin('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
            ->leftjoin('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->leftjoin('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->leftjoin('kelompokprodukbpjsmt as kpBpjs', 'kpBpjs.id', '=', 'pr.kelompokprodukbpjsidfk')
            ->leftjoin('kelasmt as kl', 'kl.id', '=', 'apd.kelasidfk')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('ruanganmt as ru2', 'ru2.id', '=', 'pd.ruanganlastidfk')
            ->join('pasienmt as pas', 'pas.id', '=', 'pd.normidfk')
            ->leftjoin('agamamt as ag', 'ag.id', '=', 'pas.agamaidfk')
            ->leftjoin('jeniskelaminmt as jkel', 'jkel.id', '=', 'pas.jeniskelaminidfk')
            ->leftjoin('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->leftjoin('kelasmt as kls2', 'kls2.id', '=', 'pd.kelasidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'apd.pegawaiidfk')
            ->leftjoin('pegawaimt as pgpj', 'pgpj.id', '=', 'pd.pegawaiidfk')
            ->leftjoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->leftjoin('transaksireseptr as sr', 'sr.norec', '=', 'pp.strukresepidfk')
            ->leftjoin('ruanganmt as rusr', 'rusr.id', '=', 'sr.ruanganidfk')
            ->leftjoin('kamarmt as kamar', 'kamar.id', '=', 'apd.kamaridfk')
            ->leftjoin('pegawaimt as pgsr', 'pgsr.id', '=', 'sr.penulisresepidfk')
            ->leftjoin('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
            ->leftjoin('strukpelayananpenjamintr as sppj', 'sp.norec', '=', 'sppj.nostrukidfk')
            ->leftjoin('strukbuktipenerimaantr as sbm', 'sp.nosbmlastidfk', '=', 'sbm.norec')
            ->leftjoin('pegawaimt as pgsbm', 'pgsbm.id', '=', 'sbm.pegawaipenerimaidfk')
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
                'pg.id as pgid',
                'pg.namalengkap',
                'sbm.nosbm',
                'sp.norec as norec_sp',
                'pp.jasa',
                'pd.normidfk as nocmfk',
                'ru2.instalasiidfk as deptid',
                'pd.nostruklastidfk as nostruklastfk',
                'ag.id as agid',
                'ag.agama',
                'pas.tgllahir',
                'kp.id as kpid',
                'kp.kelompokpasien',
                'pas.statusperkawinanidfk as objectstatusperkawinanfk',
                'pas.namaayah',
                'pas.namasuamiistri',
                'pas.id as pasid',
                'pas.norm as nocm',
                'jkel.id as jkelid',
                'jkel.jeniskelamin',
                'jkel.jeniskelamin as jk',
                'pd.noregistrasi',
                'pas.namapasien',
                'pd.tglregistrasi',
                'pd.norec as norec_pd',
                'pd.tglpulang',
                'pas.notelepon',
                'kls.id as klsid',
                'kls.namakelas',
                'pd.rekananidfk as rekananid',
                'ru2.namaruangan as ruanganlast',
                'kls2.id as klsid2',
                'kls2.namakelas as namakelas2',
                'sr.noresep',
                'rk.namarekanan',
                'rusr.namaruangan as ruanganfarmasi',
                'pgsr.namalengkap as penulisresep',
                'jp.jenisproduk',
                'kpBpjs.kelompokprodukbpjs as kelompokprodukbpjs',
                'pgpj.namalengkap as dokterpj',
                'pp.jasa',
                'kamar.namakamar',
                'sp.totalharusdibayar',
                'sp.totalprekanan',
                'sppj.totalppenjamin',
                'sp.totalbiayatambahan',
                'pgsbm.namalengkap as namalengkapsbm',
                'pd.koders as kdprofile',
                'pp.aturanpakai',
                'pp.iscito',
                'pd.statuspasien',
                'pp.isparamedis',
                'ru2.id as ruanganlastid',
                'pp.strukresepidfk'
            )
            ->where('pd.koders', $kdProfile)
            ->where('pd.noregistrasi', $noRegister)
            ->orderByRaw(" pp.tglpelayanan asc ,pp.rke asc");


        if ($request['jenisdata'] == 'resep') {
            // $pelayanan = $pelayanan->whereNotNull('pp.strukresepidfk');
            // $pelayanan = $pelayanan->whereNotNull('pp.transaksialkesfk');        
            $pelayanan = $pelayanan->whereRaw("(pp.strukresepidfk is not null or pp.transaksialkesfk is not null )");
        }
        if ($request['jenisdata'] == 'layanan') {
            if (isset($request['idruangan']) && $request['idruangan'] != "" && $request['idruangan'] != "undefined") {
                $pelayanan = $pelayanan->where('apd.ruanganidfk', $request['idruangan']);
            };
            $pelayanan = $pelayanan->whereNull('pp.strukresepidfk');
            $pelayanan = $pelayanan->whereNull('pp.transaksialkesfk');
        }
        $pelayanan = $pelayanan->get();
        $jpDokPemeriksa = $this->settingDataFixed('kdJenisPetugasDokterPemeriksa', $kdProfile);
        $pelayananpetugas = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->join('petugaspelaksanatr as ptu', 'ptu.nomasukidfk', '=', 'apd.norec')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'ptu.pegawaiidfk')
            ->select('ptu.transaksipasienfk as pelayananpasien', 'pg.namalengkap')
            ->where('pd.koders', $kdProfile)
            ->where('ptu.jenispetugaspeidfk', $jpDokPemeriksa)
            ->where('pd.noregistrasi', $noRegister)
            ->get();

        if ($request['jenisdata'] == 'bill') {
            $pelayanankelasdijamin = DB::select(
                DB::raw("
                select pp.norec, pd.noregistrasi,pd.kelasidfk as objectkelasfk,kls_pd.namakelas,asp.kelasdijaminidfk as objectkelasdijaminfk,
                kls_dijamin.namakelas as namakelas_dijamin,pp.hargajual,pp.jumlah,
                case when hnpk.harganetto2 is null then pp.hargajual else hnpk.harganetto2 end as harga_kelasdijamin
                from registrasipasientr as pd
                LEFT JOIN pemakaianasuransitr as pas on pas.registrasipasienfk=pd.norec
                left join asuransimt as asp on asp.id=pas.asuransiidfk
                left join daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                left join transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                left join tarifpelayananmt as hnpk on hnpk.produkidfk=pp.produkidfk and hnpk.kelasidfk=asp.kelasdijaminidfk
                left join kelasmt as kls_pd on kls_pd.id=pd.kelasidfk
                left join kelasmt as kls_dijamin on kls_dijamin.id=asp.kelasdijaminidfk
                where pd.koders = $kdProfile and noregistrasi=:noregistrasi;
            "),
                array(
                    'noregistrasi' => $noRegister,
                )
            );
        }

        $totototol = 0;
        $kdPodukDeposit = $this->settingDataFixed('idProdukDeposit', $kdProfile);
        $dataTotaldibayar = DB::select(
            DB::raw("
                select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk = pd.norec
                INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk = apd.norec
                INNER JOIN strukpelayanantr as sp on sp.norec = pp.strukfk
                where  pd.noregistrasi=:noregistrasi and sp.nosbmlastidfk is not null
                and pp.produkidfk not in ($kdPodukDeposit);
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );

        $dataTotalDeposit = DB::select(
            DB::raw("
                select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk = pd.norec
                INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk = apd.norec
                INNER JOIN strukpelayanantr as sp on sp.norec = pp.strukfk
                where  pd.noregistrasi=:noregistrasi and sp.nosbmlastidfk is not null
                and pp.produkidfk in ($kdPodukDeposit);
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );

        $Totdibayar = 0;
        if (count($pelayanan) > 0) {
            $alamat = [];
            $alamat = \DB::table('alamatmt as al')
                ->select('al.alamatlengkap')
                ->where('al.koders', $kdProfile)
                ->where('al.normidfk', $pelayanan[0]->nocmfk)
                ->first();

            if (empty($alamat)) {
                $alamatTXoxo = '0';
            } else {
                $alamatTXoxo = $alamat->alamatlengkap;
            }

            if (empty($dataTotaldibayar)) {
                $Totdibayar = 0;
            } else {
                $Totdibayar = $dataTotaldibayar[0]->total;
            }

            $totalBilling = 0;
            $norecAPD = '';
            $norecSP = '';
            $details = array();
            $dibayar = 0;
            $diverif = 0;
            $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile));
            $arrDeptRanap = [];
            foreach ($deptRanap as $ie) {
                $arrDeptRanap[] = (int)$ie;
            }

            foreach ($pelayanan as $value) {
                $isRawatInap = false;
                if (in_array($value->deptid, $arrDeptRanap)) {
                    $isRawatInap = true;
                }
                if ($value->produkfk == $this->getProdukIdDeposit()) {
                    continue;
                }
                $komponen = [];

                $dokter = [];
                $NamaDokter = '-';
                foreach ($pelayananpetugas as $hahaha) {
                    if ($hahaha->pelayananpasien == $value->norec) {
                        $NamaDokter = $hahaha->namalengkap;
                    }
                }

                $jasa = 0;
                if (isset($value->jasa) && $value->jasa != "" && $value->jasa != "undefined") {
                    $jasa = $value->jasa;
                }
                $kmpn = [];


                $harga = (float)$value->hargajual;
                $diskon = (float)$value->hargadiscount;
                $detail = array(
                    'norec' => $value->norec,
                    'tglPelayanan' => $value->tglpelayanan,
                    'namaPelayanan' => $value->namaproduk,
                    'dokter' => $NamaDokter,
                    'jumlah' => $value->jumlah,
                    'kelasTindakan' => @$value->namakelas,
                    'ruanganTindakan' => @$value->namaruangan,
                    'harga' => $harga,
                    'diskon' => $diskon,
                    'total' => (($harga - $diskon) * $value->jumlah) + $jasa,
                    'jppid' => '',
                    'jenispetugaspe' => '',
                    'strukfk' => $value->nostruk . ' / ' . $value->nosbm,
                    'sbmfk' => $value->nosbm,
                    'pgid' => '',
                    'ruid' => $value->ruid,
                    'prid' => $value->prid,
                    'klid' => $value->klid,
                    'norec_apd' => $value->norec_apd,
                    'norec_pd' => $value->norec_pd,
                    'norec_sp' => $value->norec_sp,
                    'komponen' => $kmpn,
                    'jasa' => $jasa,
                    'aturanpakai' => $value->aturanpakai,
                    'iscito' => $value->iscito,
                    'isparamedis' => $value->isparamedis,
                    'strukresepidfk' => $value->strukresepidfk
                );

                if (is_null($value->nosbm) == true) {
                    $dibayar = $dibayar + (($harga - $diskon) * $value->jumlah) + $jasa;
                }
                if (is_null($value->nostruk) == true) {
                    $diverif = $diverif + (($harga - $diskon) * $value->jumlah) + $jasa;
                }
                $norecAPD = $value->norec_apd;
                $norecSP = $value->norec_sp;
                $totalBilling = $totalBilling + (($harga - $diskon) * $value->jumlah) + $value->jasa;
                $total =  (($harga - $diskon) * $value->jumlah) + $value->jasa;
                $details[] = $detail;

                if ($request['jenisdata'] == 'bill') {
                    $kelas_dijamin = 0;
                    $harga_dijamin = 0;
                    $total_dijamin = 0;
                    foreach ($pelayanankelasdijamin as $kupret) {
                        if ($value->norec == $kupret->norec) {
                            $kelas_dijamin = $kupret->namakelas_dijamin;
                            $harga_dijamin = $kupret->harga_kelasdijamin;
                            $total_dijamin = ((float)$kupret->harga_kelasdijamin * (float)$value->jumlah) + $value->jasa;
                        }
                    }
                    $namakelas = $value->namakelas;
                    $ruanganTindakan = $value->namaruangan;
                    $jenisprodukMaster = $value->jenisproduk;
                    if ($value->kpid == $this->settingDataFixed('idKelompokPasienBPJS', $kdProfile)) {
                        $jenisproduk = $value->kelompokprodukbpjs;
                    } else {
                        $jenisproduk = $value->jenisproduk;
                    }
                    if ($value->noresep != null) {
                        $namakelas = '';
                        $ruanganTindakan = $value->ruanganfarmasi . '     Farmasi No: ' . $value->noresep;
                    }
                    if ($value->namaproduk == 'Biaya Administrasi') {
                        $ruanganTindakan = $dataRuangan->namaruangan;
                    }
                    if ($value->namaproduk == 'Biaya Materai') {
                        $ruanganTindakan = $dataRuangan->namaruangan;
                    }
                    $namarekanan = '-';
                    if ($value->namarekanan != null) {
                        $namarekanan = $value->namarekanan;
                    }
                    $namaproduk = $value->namaproduk;
                    if ($value->rke != null) {
                        $namaproduk = 'R/' . $value->rke . ' ' . $value->namaproduk;
                    }

                    $hargadiscount = 0;
                    if ($value->hargadiscount != null) {
                        $hargadiscount = $value->hargadiscount;
                    }


                    $hargakurangdiskon = (float)$value->hargajual - $hargadiscount;
                    $hargakalijml = $hargakurangdiskon * (float)$value->jumlah;
                    $jasa = 0;
                    if ($value->jasa != null) {
                        $jasa = $value->jasa;
                    }
                    $total = ((float)$value->jumlah * ((float)$value->hargajual - $hargadiscount)) + $jasa;


                    $namakamar = '-';
                    if ($value->namakamar != null) {
                        $namakamar = $value->namakamar;
                    }
                    $totalprekanan = 0;
                    if ($value->totalprekanan != null) {
                        $totalprekanan = $value->totalprekanan;
                    }
                    $totalppenjamin = 0;
                    if ($value->totalppenjamin != null) {
                        $totalppenjamin = $value->totalppenjamin;
                    }
                    $totalbiayatambahan = 0;
                    if ($value->totalbiayatambahan != null) {
                        $totalbiayatambahan = $value->totalbiayatambahan;
                    }
                    $totalharusdibayar = $value->totalharusdibayar;
                    if ($value->totalharusdibayar < 0) {
                        $totalharusdibayar = 0;
                    }
                    $temp = new TempBilling(); #
                    $tempNorec = $temp->generateNewId(); #
                    $temp->norec = $tempNorec; #
                    $temp->koders = $kdProfile;
                    $temp->norec_pp = $value->norec; #
                    $temp->tglstruk = $value->tglstruk; #
                    $temp->nobilling = $noRegister; #
                    $temp->nokwitansi = $value->nosbm; #
                    $temp->noregistrasi = $noRegister; #
                    $temp->nocm = $value->nocm; #
                    $temp->namapasienjk = $value->namapasien . ' ( ' . $value->jk . ' )'; #
                    $temp->unit = $value->ruanganlast; #
                    $temp->objectdepartemenfk = $value->deptid; #
                    $temp->namakelas = $namakelas; #
                    $temp->dokterpj = $value->dokterpj; #
                    $temp->tglregistrasi = $value->tglregistrasi; #
                    $temp->tglpulang = $value->tglpulang; #
                    $temp->namarekanan = $namarekanan; #
                    $temp->tglpelayanan = $value->tglpelayanan; #
                    $temp->ruangantindakan = $ruanganTindakan; #
                    if ($value->iscito == 1) {
                        $temp->namaproduk = $namaproduk . ' -> CITO'; #
                    } else {
                        $temp->namaproduk = $namaproduk;
                    }
                    $temp->penulisresep = $value->penulisresep; #
                    $temp->jenisproduk = $jenisproduk; #
                    $temp->dokter = $NamaDokter; #
                    $temp->jumlah = $value->jumlah; #
                    $temp->hargajual = $value->hargajual; #
                    $temp->diskon = $hargadiscount; #
                    $temp->total = $total; #
                    $temp->namakamar = $namakamar; #
                    $temp->tipepasien = $value->kelompokpasien; #
                    $temp->totalharusdibayar = $totalharusdibayar; #
                    $temp->totalprekanan = $totalprekanan; #
                    $temp->totalppenjamin = $totalppenjamin; #
                    $temp->totalbiayatambahan = $totalbiayatambahan; #
                    $temp->user = $value->namalengkapsbm; #
                    $temp->namakelaspd = $value->namakelas2; #
                    $temp->nama_kelasasal = $kelas_dijamin; #
                    $temp->hargajual_kelasasal = $harga_dijamin; #
                    $temp->total_kelasasal = $total_dijamin; #
                    $temp->jenisprodukmaster = $jenisprodukMaster;
                    $temp->totaldibayar = $Totdibayar;
                    $temp->save();
                }
            }
        }

        $dataTotalBill = DB::select(
            DB::raw("select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                where pd.koders = $kdProfile and pd.noregistrasi=:noregistrasi and pp.produkidfk not in ($kdPodukDeposit) ;
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );

        $totalBilling = 0;
        $totalBilling = $dataTotalBill[0]->total;

        $dataTotaldibayar = DB::select(
            DB::raw("select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                INNER JOIN strukpelayanantr as sp on sp.norec=pp.strukfk
                where pd.koders = $kdProfile and pd.noregistrasi=:noregistrasi and sp.nosbmlastidfk is not null and pp.produkidfk not in ($kdPodukDeposit)
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );

        $dibayar = 0;
        $dibayar = $dataTotaldibayar[0]->total;

        $dataTotalverif = DB::select(
            DB::raw("select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                from registrasipasientr as pd
                INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                where pd.koders = $kdProfile and pd.noregistrasi=:noregistrasi and pp.strukfk is not null and pp.produkidfk not in ($kdPodukDeposit)
            "),
            array(
                'noregistrasi' => $noRegister,
            )
        );

        $diverif = 0;
        $diverif = $dataTotalverif[0]->total;

        if (count($pelayanan) > 0) {
            $komponen = [];
            if ($pelayanan[0]->nostruklastfk == null && $isRawatInap) {
                $biayaAdministrasi = number_format($totalBilling * $this->getPercentageBiayaAdmin(), 0, '', '');


                $biayaMaterai = $this->getBiayaMaterai($totalBilling);
            }
            if (count($pelayanan) == 0) {
                empty($details);
            }


            try {
                $rekanan = [];
                $rekanan = \DB::table('rekananmt as al')
                    ->select('al.namarekanan')
                    ->where('al.koders', $kdProfile)
                    ->where('al.id', $pelayanan[0]->rekananid)
                    ->first();
                $penjamin = $rekanan->namarekanan;
            } catch (\Exception $e) {
                $penjamin = '-';
            }
            try {
                $kelompokPasien = $pelayanan[0]->kelompokpasien;
            } catch (\Exception $e) {
                $kelompokPasien = '-';
            }

            try {
                $agama = $pelayanan[0]->agama;
            } catch (\Exception $e) {
                $agama = '-';
            }
            try {
                $umur = $this->hitungUmur($pelayanan[0]->tgllahir);
            } catch (\Exception $e) {
                $umur = '-';
            }

            $keluarga = '-';
            try {
                if ($pelayanan[0]->objectstatusperkawinanfk == '1') {
                    $keluarga = $pelayanan[0]->namaayah;
                } else {
                    $keluarga = $pelayanan[0]->namasuamiistri;
                }
            } catch (\Exception $e) {
                $keluarga = '-';
            }

            $result = array(
                'pasienID' => $pelayanan[0]->pasid,
                'noCm' => $pelayanan[0]->nocm,
                'jenisKelamin' => $pelayanan[0]->jeniskelamin,
                'noRegistrasi' => $pelayanan[0]->noregistrasi,
                'namaPasien' => $pelayanan[0]->namapasien,
                'lastRuangan' => $pelayanan[0]->ruanganlast,
                'tglMasuk' => $pelayanan[0]->tglregistrasi,
                'tglPulang' => $pelayanan[0]->tglpulang,
                'jenisPasien' => $kelompokPasien,
                'kelasRawat' => $pelayanan[0]->namakelas2,
                'kelasId' => $pelayanan[0]->klsid2,
                'noAsuransi' => '-',
                'kelasPenjamin' => '-',
                'namaPenjamin' => $penjamin,
                'penjamin' => $penjamin,
                'deposit' => $dataTotalDeposit[0]->total,
                'totalKlaim' => 0,
                'jumlahBayar' => 0,
                'jumlahPiutang' => 0,
                'billing' => (float) $totalBilling,
                'norec_pd' => $pelayanan[0]->norec_pd,
                'strukfk' => $value->nostruk,
                'telepon' => $pelayanan[0]->notelepon,
                'alamat' => $alamatTXoxo,
                'tgllahir' => $pelayanan[0]->tgllahir,
                'agama' => $agama,
                'umur' => $umur,
                'keluarga' => $keluarga,
                'details' => $details,
                'lalal' => $pelayanan[0]->nostruklastfk,
                '$isRawatInap' => $isRawatInap,
                'diverif' => $diverif,
                'dibayar' => $dibayar,
                'statuspasien' => $pelayanan[0]->statuspasien,
                'ruanganlastid' => $pelayanan[0]->ruanganlastid,
            );
        } else {
            $pelayanan = \DB::table('registrasipasientr as pd')
                ->leftjoin('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
                ->leftjoin('transaksipasientr as pp', 'pp.daftarpasienruanganfk', '=', 'apd.norec')
                ->leftjoin('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
                ->leftjoin('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
                ->leftjoin('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
                ->leftjoin('kelompokprodukbpjsmt as kpBpjs', 'kpBpjs.id', '=', 'pr.kelompokprodukbpjsidfk')
                ->leftjoin('kelasmt as kl', 'kl.id', '=', 'apd.kelasidfk')
                ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                ->join('ruanganmt as ru2', 'ru2.id', '=', 'pd.ruanganlastidfk')
                ->join('pasienmt as pas', 'pas.id', '=', 'pd.normidfk')
                ->leftjoin('agamamt as ag', 'ag.id', '=', 'pas.agamaidfk')
                ->leftjoin('jeniskelaminmt as jkel', 'jkel.id', '=', 'pas.jeniskelaminidfk')
                ->leftjoin('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
                ->leftjoin('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
                ->leftjoin('kelasmt as kls2', 'kls2.id', '=', 'pd.kelasidfk')
                ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'apd.pegawaiidfk')
                ->leftjoin('pegawaimt as pgpj', 'pgpj.id', '=', 'pd.pegawaiidfk')
                ->leftjoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
                ->leftjoin('transaksireseptr as sr', 'sr.norec', '=', 'pp.strukresepidfk')
                ->leftjoin('ruanganmt as rusr', 'rusr.id', '=', 'sr.ruanganidfk')
                ->leftjoin('kamarmt as kamar', 'kamar.id', '=', 'apd.kamaridfk')
                ->leftjoin('pegawaimt as pgsr', 'pgsr.id', '=', 'sr.penulisresepidfk')
                ->leftjoin('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
                ->leftjoin('strukpelayananpenjamintr as sppj', 'sp.norec', '=', 'sppj.nostrukidfk')
                ->leftjoin('strukbuktipenerimaantr as sbm', 'sp.nosbmlastidfk', '=', 'sbm.norec')
                ->leftjoin('pegawaimt as pgsbm', 'pgsbm.id', '=', 'sbm.pegawaipenerimaidfk')
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
                    'pg.id as pgid',
                    'pg.namalengkap',
                    'sbm.nosbm',
                    'sp.norec as norec_sp',
                    'pp.jasa',
                    'pd.normidfk as nocmfk',
                    'ru2.instalasiidfk as deptid',
                    'pd.nostruklastidfk as nostruklastfk',
                    'ag.id as agid',
                    'ag.agama',
                    'pas.tgllahir',
                    'kp.id as kpid',
                    'kp.kelompokpasien',
                    'pas.statusperkawinanidfk as objectstatusperkawinanfk',
                    'pas.namaayah',
                    'pas.namasuamiistri',
                    'pas.id as pasid',
                    'pas.norm as nocm',
                    'jkel.id as jkelid',
                    'jkel.jeniskelamin',
                    'jkel.jeniskelamin as jk',
                    'pd.noregistrasi',
                    'pas.namapasien',
                    'pd.tglregistrasi',
                    'pd.norec as norec_pd',
                    'pd.tglpulang',
                    'pas.notelepon',
                    'kls.id as klsid',
                    'kls.namakelas',
                    'pd.rekananidfk as rekananid',
                    'ru2.namaruangan as ruanganlast',
                    'kls2.id as klsid2',
                    'kls2.namakelas as namakelas2',
                    'sr.noresep',
                    'rk.namarekanan',
                    'rusr.namaruangan as ruanganfarmasi',
                    'pgsr.namalengkap as penulisresep',
                    'jp.jenisproduk',
                    'kpBpjs.kelompokprodukbpjs as kelompokprodukbpjs',
                    'pgpj.namalengkap as dokterpj',
                    'pp.jasa',
                    'kamar.namakamar',
                    'sp.totalharusdibayar',
                    'sp.totalprekanan',
                    'sppj.totalppenjamin',
                    'sp.totalbiayatambahan',
                    'pgsbm.namalengkap as namalengkapsbm',
                    'pd.koders as kdprofile',
                    'pp.aturanpakai',
                    'pp.iscito',
                    'pd.statuspasien',
                    'pp.isparamedis',
                    'ru2.id as ruanganlastid',
                    'pp.strukresepidfk'
                )
                ->where('pd.koders', $kdProfile)
                ->where('pd.noregistrasi', $noRegister)
                ->orderByRaw(" pp.tglpelayanan asc ,pp.rke asc");


            $pelayanan = $pelayanan->first();
            $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile));
            $arrDeptRanap = [];
            foreach ($deptRanap as $ie) {
                $arrDeptRanap[] = (int)$ie;
            }

            $isRawatInap = false;
            if (in_array($pelayanan->deptid, $arrDeptRanap)) {
                $isRawatInap = true;
            }

            $alamat = [];
            $alamat = \DB::table('alamatmt as al')
                ->select('al.alamatlengkap')
                ->where('al.normidfk', $pelayanan->nocmfk)
                ->get();

            if (count($alamat) == 0) {
                $alamatTXoxo = '0';
            } else {
                $alamatTXoxo = $alamat[0]->alamatlengkap;
            }
            try {
                $rekanan = [];
                $penjamin = $pelayanan->namarekanan;
            } catch (\Exception $e) {
                $penjamin = '-';
            }
            try {
                $kelompokPasien = $pelayanan->kelompokpasien;
            } catch (\Exception $e) {
                $kelompokPasien = '-';
            }

            try {
                $agama = $pelayanan->agama;
            } catch (\Exception $e) {
                $agama = '-';
            }
            try {
                $umur = $this->hitungUmur($pelayanan->tgllahir);
            } catch (\Exception $e) {
                $umur = '-';
            }

            $keluarga = '-';
            try {
                if ($pelayanan->objectstatusperkawinanfk == '1') {
                    $keluarga = $pelayanan->namaayah;
                } else {
                    $keluarga = $pelayanan->namasuamiistri;
                }
            } catch (\Exception $e) {
                $keluarga = '-';
            }

            $result = array(
                'pasienID' => $pelayanan->pasid,
                'noCm' => $pelayanan->nocm,
                'jenisKelamin' => $pelayanan->jeniskelamin,
                'noRegistrasi' => $pelayanan->noregistrasi,
                'namaPasien' => $pelayanan->namapasien,
                'lastRuangan' => $pelayanan->ruanganlast,
                'tglMasuk' => $pelayanan->tglregistrasi,
                'tglPulang' => $pelayanan->tglpulang,
                'jenisPasien' => $kelompokPasien,
                'kelasRawat' => $pelayanan->namakelas2,
                'kelasId' => $pelayanan->klsid2,
                'noAsuransi' => '-',
                'kelasPenjamin' => '-',
                'namaPenjamin' => $penjamin,
                'penjamin' => $penjamin,
                'deposit' => $this->getDepositPasien($pelayanan->noregistrasi),
                'totalKlaim' => 0,
                'jumlahBayar' => 0,
                'jumlahPiutang' => 0,
                'billing' => $totalBilling,
                'norec_pd' => $pelayanan->norec_pd,
                'strukfk' => $pelayanan->nostruk,
                'telepon' => $pelayanan->notelepon,
                'alamat' => $alamatTXoxo,
                'tgllahir' => $pelayanan->tgllahir,
                'agama' => $agama,
                'umur' => $umur,
                'keluarga' => $keluarga,
                'details' => [],
                'lalal' => $pelayanan->nostruklastfk,
                '$isRawatInap' => $isRawatInap,
                'diverif' => $diverif,
                'dibayar' => $dibayar,
                'statuspasien' => $pelayanan->ruanganlastid,
                'ruanganlastid' => $pelayanan->ruanganlastid,
            );
        }
        return $this->respond($result);
    }

    public function getDataPenerimaanPembayaran(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $data = \DB::table('strukbuktipenerimaantr as sbm')
            ->join('strukpelayanantr as sp', 'sbm.nostrukidfk', '=', 'sp.norec')
            ->leftjoin('registrasipasientr as pd', 'sp.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('pegawaimt as p', 'p.id', '=', 'sbm.pegawaipenerimaidfk')
            ->leftjoin('pasienmt as ps', 'ps.id', '=', 'sp.normidfk')
            ->leftjoin('strukbuktipenerimaancarabayartr as sbmcr', 'sbmcr.nosbmidfk', '=', 'sbm.norec')
            ->leftjoin('carabayarmt as cb', 'cb.id', '=', 'sbmcr.carabayaridfk')
            ->leftjoin('jenistransaksimt as kt', 'kt.id', '=', 'sbm.kelompokpasienidfk')
            ->leftjoin('transaksiclosingtr as sc', 'sc.norec', '=', 'sbm.noclosingidfk')
            ->leftjoin('transaksiverifikasitr as sv', 'sv.norec', '=', 'sbm.noverifikasiidfk')
            ->select(DB::raw("
                sbm.norec,cb.carabayar,sbmcr.carabayaridfk,sbm.kelompokpasienidfk,kt.kelompoktransaksi,
			    sbm.keteranganlainnya AS keterangan,p.id,p.namalengkap,sc.noclosing,sbm.nosbm,
			    sv.noverifikasi,sc.tglclosing,sbm.tglsbm,sv.tglverifikasi,pd.noregistrasi,ps.namapasien,sp.norec AS norec_sp,
			    ru.id AS ruid,ru.namaruangan,sp.namapasien_klien,ps.norm AS nocm,
			    sbm.totaldibayar AS totalpenerimaan,sp.registrasipasienfk
            "))
            ->where('sbm.aktif', true)
            ->where('sbm.koders', $kdProfile);

        $filter = $request->all();
        if (isset($filter['dateStartTglSbm']) && $filter['dateStartTglSbm'] != "" && $filter['dateStartTglSbm'] != "undefined") {
            $tgl2 = $filter['dateStartTglSbm'];
            $data = $data->where('sbm.tglsbm', '>=', $tgl2);
        }
        if (isset($filter['dateEndTglSbm']) && $filter['dateEndTglSbm'] != "" && $filter['dateEndTglSbm'] != "undefined") {
            $tgl = $filter['dateEndTglSbm'];
            $data = $data->where('sbm.tglsbm', '<=', $tgl);
        }
        if (isset($filter['idCaraBayar']) && $filter['idCaraBayar'] != "" && $filter['idCaraBayar'] != "undefined") {
            $data = $data->where('cb.id', '=', $filter['idCaraBayar']);
        }
        if (isset($filter['idKelTransaksi']) && $filter['idKelTransaksi'] != "" && $filter['idKelTransaksi'] != "undefined") {
            $data = $data->where('kt.id', $filter['idKelTransaksi']);
        }
        if (isset($filter['nosbm']) && $filter['nosbm'] != "" && $filter['nosbm'] != "undefined") {
            $data = $data->where('sbm.nosbm', 'ilike', '%' . $filter['nosbm'] . '%');
        }
        if (isset($filter['noregistrasi']) && $filter['noregistrasi'] != "" && $filter['noregistrasi'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $filter['noregistrasi']);
        }
        if (isset($filter['nocm']) && $filter['nocm'] != "" && $filter['nocm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $filter['nocm'] . '%');
        }
        if (isset($filter['nama']) && $filter['nama'] != "" && $filter['nama'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['nama'] . '%');
        }
        if (isset($filter['desk']) && $filter['desk'] != "" && $filter['desk'] != "undefined") {
            $data = $data->where('sp.namapasien_klien', 'ilike', '%' . $filter['desk'] . '%');
        }
        if (isset($filter['JenisPelayanan']) && $filter['JenisPelayanan'] != "" && $filter['JenisPelayanan'] != "undefined") {
            $data = $data->where('pd.jenispelayanan', '=', $filter['JenisPelayanan']);
            if ($filter['JenisPelayanan'] == 1) {
                $data = $data->where('ru.id', '=', 663);
            }
        }

        if (isset($request['KasirArr']) && $request['KasirArr'] != "" && $request['KasirArr'] != "undefined") {
            $arrRuang = explode(',', $request['KasirArr']);
            $kodeRuang = [];
            foreach ($arrRuang as $item) {
                $kodeRuang[] = (int) $item;
            }
            $data = $data->whereIn('p.id', $kodeRuang);
        }

        $data = $data->get();
        return $this->respond($data);
    }

    protected function simpanPembatalanPembayaran(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        \DB::beginTransaction();
        try {
            $produkDeposit = $this->getProdukIdDeposit();
            $noreg = $request['noregistrasi'];
            $sbm = StrukBuktiPenerimaan::where('nostrukidfk', $request['norec_sp'])->first();
            if ($request['isdeposit'] == true) {
                $getPP = DB::select(DB::raw("select pp.norec,pp.produkidfk,prd.namaproduk,pp.hargasatuan
                        from registrasipasientr as pd
                        inner join daftarpasienruangantr as apd on pd.norec = apd.registrasipasienfk
                        inner join transaksipasientr as pp on pp.daftarpasienruanganfk = apd.norec
                        inner join pelayananmt as prd on prd.id = pp.produkidfk
                        where pd.koders = $kdProfile and pd.noregistrasi ='$noreg' and pp.produkidfk='$produkDeposit'
                        and pd.aktif = true and apd.aktif = true and pp.aktif = true"));
                if (count($getPP) > 0) {
                    foreach ($getPP as $item) {
                        if ($item->hargasatuan == $sbm['totaldibayar']) {
                            $pelayananPasien = TransaksiPasien::where('norec', $item->norec)
                                ->where('aktif', true)
                                ->where('koders', $kdProfile)
                                ->delete();
                        }
                    }
                }
            }

            if ($request['iscicilanpasien'] == true) {
                $strukPelayananPenjamin = StrukPelayananPenjamin::where('nostrukidfk', $request['norec_sp'])
                    ->where('koders', $kdProfile)
                    ->first();

                $dataSPP = StrukPelayananPenjamin::where('nostrukidfk', $request['norec_sp'])
                    ->update([
                        'totalsudahdibayar' => $strukPelayananPenjamin->totalsudahdibayar - $sbm['totaldibayar']
                    ]);
            }

            $strukPelayanan = StrukPelayanan::where('norec', $request['norec_sp'])
                ->where('nosbmlastidfk', $request['norec_sbm'])
                ->where('koders', $kdProfile)
                ->update([
                    'nosbmlastidfk'    => null,
                ]);

            $strukBuktiPenerimanan = StrukBuktiPenerimaan::where('norec', $request['norec_sbm'])
                ->where('koders', $kdProfile)
                ->update(
                    [
                        'aktif' => false,
                    ]
                );
            $pasienDaftar = RegistrasiPasien::where('norec', $request['norec_pd'])
                ->where('nosbmlastidfk', $request['norec_sbm'])
                ->where('koders', $kdProfile)
                ->update(
                    [
                        'nosbmlastidfk'    => null,
                    ]
                );

            $this->transStatus = true;
        } catch (\Exception $e) {
            $this->transStatus = false;
        }

        if ($this->transStatus) {
            $transMsg = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMsg,
                "as" => 'slvR',
            );
        } else {
            $transMsg = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMsg,
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMsg);
    }

    protected function UbahCaraBayar(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        \DB::beginTransaction();

        try {
            $data = StrukBuktiPenerimaanCaraBayar::where('nosbmidfk', $request['norec_sbm'])
                ->where('koders', $kdProfile)
                ->update(
                    [
                        'carabayaridfk' => $request['carabayaridfk']
                    ]
                );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMsg =   "Batal Pembayaran Gagal {SP}";
        }

        if ($transStatus == 'true') {
            $transMsg = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMsg,
                "as" => 'slvR',
            );
        } else {
            $transMsg = "Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMsg,
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMsg);
    }
    public function deletePelayananPasien(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['dataDel'] as $item) {
                $HapusPPD = TransaksiPasienDetail::where('transaksipasienfk', $item['norec_pp'])->delete();
                $HapusPPP = PetugasPelaksana::where('transaksipasienfk', $item['norec_pp'])->delete();
                $Edit = TransaksiPasien::where('norec', $item['norec_pp'])->delete();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getPelPetugasByPelPasien(Request $request)
    {
        $data4 = \DB::table('transaksipasientr as pp')
            ->join('petugaspelaksanatr as ppp', 'pp.norec', '=', 'ppp.transaksipasienfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'ppp.pegawaiidfk')
            ->leftjoin('jenispetugaspelaksanamt as jpp', 'jpp.id', '=', 'ppp.jenispetugaspeidfk')
            ->select(
                'pp.norec as norec_pp',
                'ppp.norec as norec_ppp',
                'pg.id as pg_id',
                'pg.namalengkap',
                'jpp.jenispetugaspe',
                'jpp.id as jpp_id'
            )
            ->where('pp.norec', $request['norec_pp'])
            ->distinct()
            ->get();

        $result = array(
            'data' => $data4,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function hapusPPP(Request $request)
    {
        $dataLogin = $request->all();
        DB::beginTransaction();
        $r_PPP = $request['pelayananpasienpetugas'];
        try {
            $data1 = PetugasPelaksana::where('norec', $r_PPP['norec_ppp'])->delete();
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function simpanDokterPPP(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        $new_PPP = $request['pelayananpasienpetugas'];
        try {
            if (isset($new_PPP['norec_ppp']) && $new_PPP['norec_ppp'] == '' && isset($new_PPP['objectjenispetugaspefk'])) {
                $data1 = new PetugasPelaksana();
                $data1->norec = $data1->generateNewId();
                $data1->koders = $kdProfile;
                $data1->aktif = true;
            } else {
                $data1 =  PetugasPelaksana::where('norec', $new_PPP['norec_ppp'])->first();
            }
            if (!empty($data1)) {
                $data1->nomasukidfk = $new_PPP['norec_apd'];
                $data1->jenispetugaspeidfk = $new_PPP['objectjenispetugaspefk'];
                $data1->transaksipasienfk = $new_PPP['norec_pp'];
                $data1->pegawaiidfk = $new_PPP['objectpegawaifk'];
                $data1->save();
            }
            if (isset($new_PPP['isparamedis'])) {
                $data2 =  TransaksiPasien::where('norec', $new_PPP['norec_pp'])->first();
                if (!empty($data2)) {
                    $data2->isparamedis =  $new_PPP['isparamedis'];
                    $data2->save();
                }
            }
            $transStatus = 'true';
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $data1 != null ? $data1 : $data2,
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
    public function getKomponenHargaPelayanan(Request $request)
    {
        $data4 = \DB::table('transaksipasiendetailtr as ppd')
            ->join('komponentarifmt as kh', 'kh.id', '=', 'ppd.komponenhargaidfk')
            ->select(
                'ppd.transaksipasienfk as norec_pp',
                'ppd.norec',
                'kh.komponentarif as komponenharga',
                'ppd.jumlah',
                'ppd.hargasatuan',
                'ppd.hargadiscount',
                'ppd.jasa'
            )
            ->where('ppd.transaksipasienfk', $request['norec_pp'])
            ->where('ppd.aktif', true)
            ->get();
        foreach ($data4 as $d) {
            if ($d->jasa == null) {
                $d->jasa = 0;
            }
        }
        $result = array(
            'data' => $data4,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function simpanUpdateDiskonKomponen(Request $request)
    {
        $dataLog = $request->all();
        DB::beginTransaction();
        $transStatus = 'true';
        $nilaiCito = 0;
        $totalJasa = 0;
        if ($request['hargajasa'] != 0) {
            $nilaiCito = 0.25;
        }
        try {
            $data = TransaksiPasienDetail::where('norec', $request['norec_ppd'])
                ->update(
                    [
                        'hargadiscount' => $request['hargadiskon'],
                        'jasa' => ($request['hargakomponen'] - $request['hargadiskon']) * $nilaiCito,
                    ]
                );
            $totalDiskon = 0.0;
            $dataaa = TransaksiPasienDetail::where('transaksipasienfk', $request['norec_pp'])->get();
            foreach ($dataaa as $item) {
                $totalDiskon = $totalDiskon + $item->hargadiscount;
                $totalJasa = $totalJasa + $item->jasa;
            }
            $data2 = TransaksiPasien::where('norec', $request['norec_pp'])
                ->update([
                    'hargadiscount' => $totalDiskon,
                    'jasa' => $totalJasa
                ]);
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $dataaa,
                "message" => $ReportTrans,
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "data" => $dataLog,
                "message" => $ReportTrans,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function simpanUpdateTglPelayanan(Request $request)
    {
        DB::beginTransaction();
        $transStatus = 'true';
        try {
            $data = TransaksiPasien::where('norec', $request['norec_pp'])
                ->update(
                    [
                        'tglpelayanan' => $request['tanggalPelayanan']
                    ]
                );
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
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
    public function detailPasienDeposit($norec_pd, Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $DepositId = $this->getProdukIdDeposit();
        $pasienDaftar = collect(DB::select("
            SELECT pd.noregistrasi,pm.norm,pm.namapasien,jk.jeniskelamin,td.strukfk,sp.nosbmlastidfk,
                   to_char(pm.tgllahir, 'DD-MM-YYYY') as tgllahir,EXTRACT(YEAR FROM AGE(pd.tglregistrasi, pm.tgllahir)) || ' Thn '
			       || EXTRACT(MONTH FROM AGE(pd.tglregistrasi, pm.tgllahir)) || ' Bln '
			       || EXTRACT(DAY FROM AGE(pd.tglregistrasi, pm.tgllahir)) || ' Hr' AS umur
            FROM registrasipasientr AS pd
            INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = pd.norec
            LEFT JOIN transaksipasientr AS td ON td.daftarpasienruanganfk = dpr.norec AND td.produkidfk <> $DepositId
            LEFT JOIN strukpelayanantr AS sp ON sp.norec = td.strukfk
            LEFT JOIN strukbuktipenerimaantr AS sbm ON sbm.norec = sp.nosbmlastidfk
            INNER JOIN pasienmt AS pm ON pm.id = pd.normidfk
            LEFT JOIN jeniskelaminmt AS jk ON jk.id = pm.jeniskelaminidfk
            WHERE pd.koders = $kdProfile AND pd.norec = '$norec_pd'
        "));
        $statuspasien = false;
        foreach ($pasienDaftar as $pp){
            if($pp->strukfk) {
                if($pp->nosbmlastidfk != null){
                    $statuspasien = true;
                }
            }
        }

        $isBayar = "";
        if($statuspasien == true){
            $isBayar = 'Sudah Bayar';
        }else{
            $isBayar = 'Belum Bayar';
        }

        $listDeposit = array();
        $deposit = collect(DB::select("
            SELECT tp.tglpelayanan,pr.namaproduk,tp.hargasatuan,tp.jumlah
            FROM registrasipasientr AS rg
            INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec
            INNER JOIN transaksipasientr AS tp ON tp.daftarpasienruanganfk = dpr.norec
            INNER JOIN pelayananmt AS pr ON pr.id = tp.produkidfk
            WHERE tp.strukfk IS NOT NULL AND tp.produkidfk = $DepositId
            AND rg.norec = '$norec_pd'
        "));

        if (count($deposit) > 0){
            foreach ($deposit as $item) {
                $listDeposit[] = array(
                    "tglTransaksi" => $item->tglpelayanan,
                    "jumlahDeposit" => (float)$item->hargasatuan *(float)$item->jumlah
                );
            }
        }

        $result = array(
            'noCm'  => $pasienDaftar[0]->norm,
            'noRegistrasi' => $pasienDaftar[0]->noregistrasi,
            'jenisKelamin' => $pasienDaftar[0]->jeniskelamin,
            'namaPasien'  => $pasienDaftar[0]->namapasien,
            'umur'  => $pasienDaftar[0]->umur,
            'status' => $isBayar,
            'detailDeposit' => $listDeposit
        );
        return $this->respond($result, "Detail Deposit Pasien");
    }

    protected function simpanPembayaranDeposit(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $pasienDaftar = RegistrasiPasien::where('norec', $request['parameterTambahan']['noRecStrukPelayanan'])->where('koders', $kdProfile)->first();
        $noRegistrasi = $pasienDaftar->noregistrasi;
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        DB::beginTransaction();
        try {
            foreach ($request['pembayaran'] as $pembayaran) {
                $NOSBM = array();
                $SP = new StrukPelayanan();
                $SP->norec = $SP->generateNewId();
                $AntrianPasienDiperiksa = $pasienDaftar->antrian_pasien_diperiksa->first();
                $PP = new TransaksiPasien();
                $PP->norec  = $PP->generateNewId();
                $PP->koders = $kdProfile;
                $PP->aktif = true;
                $PP->daftarpasienruanganfk = $AntrianPasienDiperiksa->norec; //salah
                $PP->aturanpakai = "-";
                $PP->hargasatuan = $pembayaran['nominal'];
                $PP->hargajual = $pembayaran['nominal'];
                $PP->jumlah = 1;
                $PP->nilainormal = -1;
                $PP->keteranganlain = "-";
                $PP->keteranganpakai2 = "-";
                $PP->produkidfk = $this->getProdukIdDeposit();
                $PP->stock = 1;
                $PP->tglpelayanan = $request['tglsbm']; //$this->getDateTime();
                $PP->save();
                $norecPP = $PP->norec;

                $SP->koders =  $kdProfile;
                $SP->aktif =  false;
                $SP->normidfk =  $pasienDaftar->normidfk;
                $SP->registrasipasienfk =  $pasienDaftar->norec;
                $SP->kelaslastidfk =  $pasienDaftar->kelasidfk;
                $SP->kelompoktransaksiidfk =  1;
                $SP->pegawaipenerimaidfk =  $dataPegawaiUser[0]->id;
                $SP->nostruk =  $this->generateCode(new StrukPelayanan, 'nostruk', 10, 'VRF', $kdProfile);
                $SP->tglstruk =  $request['tglsbm'];
                $SP->ruanganidfk =  $pasienDaftar->ruanganlastidfk;
                $SP->save();
                $norecSP =  $SP->norec;

                $strukPelayananDetail = new StrukPelayananDetail();
                $strukPelayananDetail->norec = $strukPelayananDetail->generateNewId();
                $strukPelayananDetail->nostrukidfk = $SP->norec;
                $strukPelayananDetail->koders = $kdProfile;
                $strukPelayananDetail->aktif = true;
                $strukPelayananDetail->hargadiscount = 0;
                $strukPelayananDetail->hargadiscountgive = 0;
                $strukPelayananDetail->hargadiscountsave = 0;
                $strukPelayananDetail->harganetto = $PP->hargajual;
                $strukPelayananDetail->hargapph = $PP->hargajual;
                $strukPelayananDetail->hargappn = $PP->hargajual;
                $strukPelayananDetail->hargasatuan = $PP->hargasatuan;
                $strukPelayananDetail->hargasatuandijamin = 0;
                $strukPelayananDetail->hargasatuanppenjamin = 0;
                $strukPelayananDetail->hargasatuanpprofile = 0;
                $strukPelayananDetail->hargatambahan = 0;
                $strukPelayananDetail->isonsiteservice = 0;
                $strukPelayananDetail->persendiscount = 0;
                $strukPelayananDetail->qtyproduk = $PP->jumlah;
                $strukPelayananDetail->qtyprodukoutext = 0;
                $strukPelayananDetail->qtyprodukoutint = 0;
                $strukPelayananDetail->qtyprodukretur = 0;
                $strukPelayananDetail->satuan = 0;
                $strukPelayananDetail->satuanstandar = 0;
                $strukPelayananDetail->is_terbayar = 0;
                $strukPelayananDetail->tglpelayanan = $SP->tglstruk;
                $strukPelayananDetail->produkidfk = $PP->produkidfk;
                $strukPelayananDetail->kelasidfk = $PP->kelasidfk;
                $strukPelayananDetail->save();

                $strukBuktiPenerimanan = new StrukBuktiPenerimaan();
                $strukBuktiPenerimanan->norec = $strukBuktiPenerimanan->generateNewId();
                $strukBuktiPenerimanan->koders = $kdProfile;
                $strukBuktiPenerimanan->aktif = true;
                $strukBuktiPenerimanan->keteranganlainnya = "Pembayaran Deposit Pasien";
                $strukBuktiPenerimanan->nostrukidfk = $SP->norec;
                $strukBuktiPenerimanan->kelompokpasienidfk = $pasienDaftar->KelompokPasien->first();
                $strukBuktiPenerimanan->kelompoktransaksiidfk = 1;
                $strukBuktiPenerimanan->pegawaipenerimaidfk = $dataPegawaiUser[0]->id;
                $strukBuktiPenerimanan->tglsbm = $SP->tglstruk;
                $strukBuktiPenerimanan->totaldibayar = $pembayaran['nominal'];
                $sbmKode = $this->generateCode(new StrukBuktiPenerimaan, 'nosbm', 14, 'IVN-' . $this->getDateTime()->format('ym'), $kdProfile);
                $strukBuktiPenerimanan->nosbm = $sbmKode; //$this->generateCode(new StrukBuktiPenerimaan, 'nosbm', 14, 'RV-'.$this->getDateTime()->format('ym'));
                $strukBuktiPenerimanan->save();
                $norecSBM = $strukBuktiPenerimanan->norec;
                $NOSBM[] = $strukBuktiPenerimanan->nosbm;

                $SBPCB = new StrukBuktiPenerimaanCaraBayar();
                $SBPCB->norec = $SBPCB->generateNewId();
                $SBPCB->koders = $kdProfile;
                $SBPCB->aktif = true;
                $SBPCB->nosbmidfk = $strukBuktiPenerimanan->norec;
                $SBPCB->carabayaridfk = $pembayaran['caraBayar']['id'];
                $SBPCB->totaldibayar = $pembayaran['nominal'];
                if (isset($pembayaran['detailBank'])) {
                    $SBPCB->jeniskartuidfk = $pembayaran['detailBank']['jenisKartu']['id'];
                    $SBPCB->nokartuaccount = $pembayaran['detailBank']['noKartu'];
                    $SBPCB->namabankprovider = $pembayaran['detailBank']['namaKartu'];
                    $SBPCB->namapemilik = $pembayaran['detailBank']['namaKartu'];
                }
                $SBPCB->save();

                $updatePP = TransaksiPasien::where('norec', $norecPP)
                    ->update([
                        'strukfk' => $norecSP,
                    ]);

                $updateSP = StrukPelayanan::where('norec', $norecSP)
                    ->update([
                        'nosbmlastidfk' => $norecSBM
                    ]);
            }

            $this->transStatus = true;
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $this->transStatus = false;
            $ReportTrans = "Gagal coba lagi";
        }

        if ($this->transStatus) {
            DB::commit();
            $result = array(
                "status" => 201,
                'noSBM' => $NOSBM,
                'norec' => $norecSBM,
                'noReg' => $noRegistrasi,
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

    protected function simpanPengembalianDeposit(Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        \DB::beginTransaction();
        $pasienDaftar = RegistrasiPasien::where('norec', $request['parameterTambahan']['noRecStrukPelayanan'])->where('koders', $kdProfile)->first();
        $dataSP = StrukPelayanan::where('registrasipasienfk',$pasienDaftar->norec)
            ->where('koders', $kdProfile)
            ->where('aktif', true)
            ->first();

        $norec_sp = $dataSP['norec'];
        $NOSBM = array();
        try{
            foreach($request['pembayaran'] as $pembayaran){
                    $strukBuktiPenerimanan = new StrukBuktiPenerimaan();
                    $strukBuktiPenerimanan->norec = $strukBuktiPenerimanan->generateNewId();
                    $strukBuktiPenerimanan->koders = $kdProfile;
                    $strukBuktiPenerimanan->aktif = true;
                    $strukBuktiPenerimanan->keteranganlainnya = "Pengembalian Deposit Pasien";
                    $strukBuktiPenerimanan->nostrukidfk = $norec_sp;
                    $strukBuktiPenerimanan->kelompokpasienidfk = $pasienDaftar->KelompokPasien->first();
                    $strukBuktiPenerimanan->kelompoktransaksiidfk = 1;
                    $strukBuktiPenerimanan->pegawaipenerimaidfk = $dataPegawaiUser[0]->id;
                    $strukBuktiPenerimanan->tglsbm  = $dataSP['tglstruk'];
                    $strukBuktiPenerimanan->totaldibayar  = $pembayaran['nominal'];
                    $sbmKode = $this->generateCode(new StrukBuktiPenerimaan, 'nosbm', 14, 'INV-'.$this->getDateTime()->format('ym'), $kdProfile);
                    $strukBuktiPenerimanan->nosbm = $sbmKode;
                    $NOSBM[] = $strukBuktiPenerimanan->nosbm;
                    $NOSBM2 = $strukBuktiPenerimanan->nosbm;
                    $norecSBM = $strukBuktiPenerimanan->norec;
                    $strukBuktiPenerimanan->save();
                    $norec_sbm=$strukBuktiPenerimanan->norec;

                    $SBPCB = new StrukBuktiPenerimaanCaraBayar();
                    $SBPCB->norec = $SBPCB->generateNewId();
                    $SBPCB->koders= $kdProfile;
                    $SBPCB->aktif = 1;
                    $SBPCB->nosbmidfk = $strukBuktiPenerimanan->norec;
                    $SBPCB->carabayaridfk = $pembayaran['caraBayar']['id'];
                    $SBPCB->totaldibayar = $pembayaran['nominal'];
                    if(isset($pembayaran['detailBank'])){
                        $SBPCB->jeniskartuidfk = $pembayaran['detailBank']['jenisKartu']['id'];
                        $SBPCB->nokartuaccount = $pembayaran['detailBank']['noKartu'];
                        $SBPCB->namabankprovider = $pembayaran['detailBank']['namaKartu'];
                        $SBPCB->namapemilik = $pembayaran['detailBank']['namaKartu'];
                    }
                    $SBPCB->save();

                    $dt = StrukPelayanan::where('norec', $norec_sp)
                        ->where('koders', $kdProfile)
                        ->where('aktif', true)
                        ->update([
                                'nosbmlastidfk' => $norec_sbm,
                                'iskembalideposit' => true
                        ]);

                    $pd = RegistrasiPasien::where('norec', $pasienDaftar->norec)
                            ->where('koders', $kdProfile)
                            ->where('aktif', true)
                            ->update([
                                'nosbmlastidfk' => $norec_sbm,
                            ]);
            }

          $transStatus = 'true';
          $ReportTrans = "Simpan Pengembalian Deposi Berhasil";
        }catch(\Exception $e){
            $transStatus = 'false';
            $ReportTrans = "Simpan Pengembalian Deposit Gagal";
        }
        if ($transStatus == true) {
            DB::commit();
            $result = array(
                "status" => 201,
                'noSBM' => $NOSBM,
                'norec' => $norecSBM,
                'noReg' => $pasienDaftar->noregistrasi,
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

    public function daftarPiutangPasien(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataPiutang = \DB::table('strukpelayanantr as sp')
            ->leftjoin('strukpelayananpenjamintr as spp', 'sp.norec', '=', 'spp.nostrukidfk')
            ->join('transaksipasientr as pp', 'pp.strukfk', '=', 'sp.norec')
            ->join('daftarpasienruangantr as ap', 'ap.norec', '=', 'pp.daftarpasienruanganfk')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'ap.registrasipasienfk')
            ->leftjoin('pemakaianasuransitr as pa', 'pa.registrasipasienfk', '=', 'sp.registrasipasienfk')
            ->leftjoin('klaimbpjstxttr as bpjs', 'bpjs.sep', '=', 'pa.nosep')
            ->leftjoin('rekananmt as rkn', 'rkn.id', '=', 'pd.rekananidfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftJoin('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->leftJoin('rekananmt as rk', 'rk.id', '=', 'spp.kdrekananpenjamin')
            ->leftJoin('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftJoin('postinghutangpiutangtr as php', 'php.nostrukidfk', '=', 'spp.norec')
            ->leftJoin('transaksipostingtr as spt', 'spt.noposting', '=', 'php.noposting')
            ->select(DB::raw("
                 pd.tglregistrasi,kp.kelompokpasien,spp.norec,sp.tglstruk,pd.noregistrasi,pd.tglregistrasi,ps.norm,
                 ps.namapasien,spp.totalppenjamin,spp.totalharusdibayar,bpjs.tarif_inacbg AS tarifklaim,
                 spp.totalsudahdibayar,rk.namarekanan,spp.totalbiaya,spp.noverifikasi,rkn.namarekanan,
                 pd.norec AS norec_pd,pd.tglpulang,php.noposting,spt.aktif,ru.namaruangan,ps.norm
            "))
            ->whereNotNull('spp.norec')
            ->where('sp.aktif', true)
            ->where('sp.koders', $kdProfile);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "") {
            $dataPiutang = $dataPiutang->where('pd.tglpulang', '>=', $request['tglAwal']);
        }

        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "") {
            $dataPiutang = $dataPiutang->where('pd.tglpulang', '<=', $request['tglAkhir']);
        }

        if (isset($request['instalasiId']) && $request['instalasiId'] != "") {
            $dataPiutang = $dataPiutang->where('dept.id', '=', $request['instalasiId']);
        }

        if (isset($request['ruanganId']) && $request['ruanganId'] != "") {
            $dataPiutang = $dataPiutang->where('ru.id', '=', $request['ruanganId']);
        }

        if (isset($request['kelompokPasienId']) && $request['kelompokPasienId'] != "") {
            $dataPiutang = $dataPiutang->where('pd.kelompokpasienlastidfk', '=', $request['kelompokPasienId']);
        }

        if (isset($request['namaPasien']) && $request['namaPasien'] != "") {
            $dataPiutang = $dataPiutang->where('ps.namapasien', 'ilike', '%' . $request['namaPasien'] . '%');
        }

        if (isset($request['noReg']) && $request['noReg'] != "") {
            $dataPiutang = $dataPiutang->where('pd.noregistrasi', '=', $request['noReg']);
        }

        if (isset($request['noRm']) && $request['noRm'] != "") {
            $dataPiutang = $dataPiutang->where('ps.norm', '=', $request['noRm']);
        }

        if (isset($request['status']) && $request['status'] != "") {
            if ($request['status'] == 'Verifikasi') {
                $dataPiutang = $dataPiutang->whereNotNull('spp.noverifikasi');
            }
            if ($request['status'] == 'Belum Verifikasi') {
                $dataPiutang = $dataPiutang->whereNull('spp.noverifikasi');
            }
        }

        $dataPiutang = $dataPiutang->groupBy(DB::raw("
            pd.tglregistrasi,kp.kelompokpasien,spp.norec,sp.tglstruk,pd.noregistrasi,pd.tglregistrasi,
			ps.norm,ps.namapasien,spp.totalppenjamin,spp.totalharusdibayar,spp.totalsudahdibayar,
			rk.namarekanan,spp.totalbiaya,spp.noverifikasi,rkn.namarekanan,pd.norec,pd.tglpulang,
			php.noposting,spt.aktif,bpjs.tarif_inacbg,ru.namaruangan,ps.norm
        "));
        $dataPiutang = $dataPiutang->orderBy('ps.namapasien');
        $dataPiutang = $dataPiutang->get();
        $result = [];
        foreach ($dataPiutang as $item) {
            if ($item->aktif == 1 || is_null($item->aktif)) {
                $statusVerifikasi = "Belum Diverifikasi";
                $isVerified = false;
                if ($item->noverifikasi != null) {
                    $statusVerifikasi = "Verifikasi";
                    $isVerified = true;
                }
                if ($item->tarifklaim == null) {
                    $tarifklaim = 0;
                    $selisihKlaim = 0;
                } else {
                    $tarifklaim = (float)$item->tarifklaim;
                    $selisihKlaim = (float)$item->tarifklaim - (float)$item->totalppenjamin;
                }
                $detailss = \DB::select(
                    DB::raw("
                    SELECT spd.norec,sp.nostrukidfk,spd.totalppenjamin,kl.kelompokpasien,rek.namarekanan
					FROM strukpelayananpenjamindetailtr as spd
					inner join strukpelayananpenjamintr as sp on sp.norec= spd.strukpelayananpenjaminidfk
					inner join kelompokpasienmt as kl on kl.id= spd.kelompokpasienidfk
					inner join rekananmt as rek on rek.id=spd.kdrekananpenjaminidfk
					where spd.koders = $kdProfile and spd.strukpelayananpenjaminidfk=:norecStruk
	                "),
                    array(
                        'norecStruk' => $item->norec,
                    )
                );
                $result[] = array(
                    'norec' => $item->norec,
                    'norm' => $item->norm,
                    'tglregistrasi' => $item->tglregistrasi,
                    'tglTransaksi' => $item->tglstruk,
                    'noRegistrasi' => $item->noregistrasi,
                    'namaPasien' => $item->namapasien,
                    'kelasRawat' => '-',
                    'jenisPasisen' => $item->kelompokpasien,
                    'kelasPenjamin' => "-",
                    'totalBilling' => $item->totalbiaya,
                    'totalKlaim' => $item->totalppenjamin,
                    'totalBayar' => $item->totalsudahdibayar,
                    'statusVerifikasi' => $statusVerifikasi,
                    'rekanan' => $item->namarekanan,
                    'norec_pd' => $item->norec_pd,
                    'tglpulang' => $item->tglpulang,
                    'isVerified' => $isVerified,
                    'noposting' => $item->noposting,
                    'tarifselisihklaim' => $selisihKlaim,
                    'tarifinacbgs' => $tarifklaim,
                    'verifikasi' => $item->noverifikasi,
                    'namaruangan' => $item->namaruangan,
                    'details' => $detailss
                );
            }
        }
        $datadata = array(
            'data' =>   $result,
        );
        return $this->respond($datadata, 'Data Daftar Pasien');
    }

    public function verifyPiutangPasien(Request $request)
    {
        $transMsg = null;
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        \DB::beginTransaction();
        try {
            $verifikasi = new StrukVerifikasi();
            $noVerif = $this->generateCode(new StrukVerifikasi, 'noverifikasi', 12, 'TVP' . $this->getDateTime()->format('dmy'), $kdProfile);
            $verifikasi->norec = $verifikasi->generateNewId();
            $verifikasi->koders = $kdProfile;
            $verifikasi->aktif = true;
            $verifikasi->kelompoktransaksiidfk = 1;
            $verifikasi->pegawaipjawabidfk = $dataPegawaiUser[0]->id;
            $verifikasi->ruanganidfk = 5;
            $verifikasi->namaverifikasi = "Verifikasi Piutang Penjamin";
            $verifikasi->noverifikasi = $noVerif;
            $verifikasi->tglverifikasi = $this->getDateTime();
            $verifikasi->save();
            $noVerifikasi = $verifikasi->noverifikasi;

            foreach ($request['dataPiutang'] as $norec) {
                $strukPelayanan = StrukPelayananPenjamin::where('norec', $norec)->first();
                if ($strukPelayanan) {
                    $strukPelayanan->noverifikasi = $noVerifikasi;
                    $strukPelayanan->save();
                }
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = false;
            // $transMsg = "Verifikasi Piutang Gagal";
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'result' => $verifikasi,
                'tb' => 'epc',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message'  => $ReportTrans,
                'result' => $verifikasi,
                'tb' => 'epc',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function cancelVerifyPiutangPasien(Request $request)
    {
        $transMsg = null;
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if (count($request['dataPiutang']) > 0) {
                foreach ($request['dataPiutang'] as $norec) {
                    $strukPelayanan = StrukPelayananPenjamin::where('norec', $norec)->where('koders', $kdProfile)->first();
                    if ($strukPelayanan) {
                        $strukPelayanan->noverifikasi = null;
                        $strukPelayanan->save();
                    }
                }
            } else {
                $transStatus = false;
                // $transMsg = "Transaksi Gagal (0)";
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = false;
            // $transMsg = "Unverifikasi Piutang Gagal";
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'result' => $strukPelayanan,
                'tb' => 'epc',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message'  => $ReportTrans,
                'result' => $strukPelayanan,
                'tb' => 'epc',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDaftarTagihanPiutang(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $filter = $request->all();
        $dataPiutang = \DB::table('strukpelayananpenjamintr as spp')
            ->join('strukpelayanantr as sp', 'sp.norec', '=', 'spp.nostrukidfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'sp.normidfk')
            ->join('registrasipasientr as pd', 'sp.registrasipasienfk', '=', 'pd.norec')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('rekananmt as rkn', 'rkn.id', '=', 'pd.rekananidfk')
            ->join('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('postinghutangpiutangtr as php', 'php.nostrukidfk', '=', 'spp.norec')
            ->leftjoin('transaksipostingtr as spt', 'spt.noposting', '=', 'php.noposting')
            ->join('transaksiverifikasitr as tv', 'tv.noverifikasi', '=', 'spp.noverifikasi')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->select(DB::raw("
                kp.kelompokpasien,spp.norec,pd.tglpulang AS tglstruk,pd.noregistrasi,pd.tglregistrasi,
			    ps.norm,ps.namapasien,ru.namaruangan,spp.totalppenjamin,spp.totalharusdibayar,spp.totalsudahdibayar,
			    spp.totalbiaya,spp.noverifikasi,rkn.namarekanan,php.noposting,spt.aktif,pd.norec AS norec_pd,
			    php.aktif AS sttts,tv.tglverifikasi,jk.jeniskelamin,sp.norec AS norec_sp
            "))
            ->where('spp.koders', $kdProfile)
            ->whereNotNull('spp.noverifikasi')
            ->where('sp.aktif', true)
            ->where('pd.aktif', true);

        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "") {
            $dataPiutang = $dataPiutang->where('tv.tglverifikasi', '>=', $filter['tglAwal']);
        }
        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "") {
            $dataPiutang = $dataPiutang->where('tv.tglverifikasi', '<=', $filter['tglAkhir']);
        }
        if (isset($filter['kelompokpasienfk']) && $filter['kelompokpasienfk'] != "") {
            $dataPiutang = $dataPiutang->where('pd.kelompokpasienlastidfk', '=', $filter['kelompokpasienfk']);
        }
        if (isset($filter['penjaminID']) && $filter['penjaminID'] != "") {
            $dataPiutang = $dataPiutang->where('pd.kelompokpasienlastidfk', '=', $filter['penjaminID']);
        }
        if (isset($filter['rekananfk']) && $filter['rekananfk'] != "") {
            $dataPiutang = $dataPiutang->where('pd.rekananidfk', '=', $filter['rekananfk']);
        }
        if (isset($filter['ruanganId']) && $filter['ruanganId'] != "") {
            $dataPiutang = $dataPiutang->where('ru.id', '=', $filter['ruanganId']);
        }
        if (isset($filter['namaPasien']) && $filter['namaPasien'] != "") {
            $dataPiutang = $dataPiutang->where('ps.namapasien', 'ilike', '%' . $filter['namaPasien'] . '%');
        }
        if (isset($filter['noregistrasi']) && $filter['noregistrasi'] != "") {
            $dataPiutang = $dataPiutang->where('pd.noregistrasi', 'ilike', '%' . $filter['noregistrasi'] . '');
        }
        if (isset($filter['nocm']) && $filter['nocm'] != "") {
            $dataPiutang = $dataPiutang->where('ps.norm', '=', $filter['nocm']);
        }
        if (isset($filter['jmlRows']) && $filter['jmlRows'] != "" && $filter['jmlRows'] != "undefined") {
            $dataPiutang = $dataPiutang->take($filter['jmlRows']);
        }
        $dataPiutang = $dataPiutang->orderBy('tv.tglverifikasi');
        $dataPiutang = $dataPiutang->get();
        $result = array();
        $no = 1;
        foreach ($dataPiutang as $item) {
            if ($item->aktif ==  1 || is_null($item->aktif)) {
                if ($item->sttts == 1 || is_null($item->sttts)) {
                    if ($item->totalppenjamin > $item->totalsudahdibayar) {
                        if (!isset($item->noposting)) {
                            $status = 'Piutang';
                        } else {
                            $status = 'Collecting';
                        }
                    } else {
                        $status = 'Lunas';
                    }

                    $result[] = array(
                        'no' => $no++,
                        'noRec' => $item->norec,
                        'tgltransaksi' => $item->tglstruk,
                        'tglverifikasi' => $item->tglverifikasi,
                        'tglregistrasi' => $item->tglregistrasi,
                        'noregistrasi' => $item->noregistrasi,
                        'norm' => $item->norm,
                        'namapasien' => $item->namapasien,
                        'jeniskelamin' =>  $item->jeniskelamin,
                        'kelompokpasien' => $item->kelompokpasien,
                        'ruangan' => $item->namaruangan,
                        'kelasRawat' => $item->kelompokpasien,
                        'jenisPasien' => $item->kelompokpasien,
                        'umur' => $this->hitungUmur($item->tglstruk),
                        'kelasPenjamin' => "-",
                        'totalBilling' => $item->totalbiaya,
                        'totalKlaim' => $item->totalppenjamin,
                        'totalBayar' => $item->totalsudahdibayar,
                        'rekanan' => $item->namarekanan,
                        'status' => $status,
                        'norec_pd' => $item->norec_pd,
                        'noposting' => $item->noposting,
                        'norec_sp' => $item->norec_sp,
                        'stts' => $item->aktif,
                    );
                }
            }
        }
        return $this->respond($result, 'Data Piutang Layanan');
    }

    public function detailPiutangPasien($norec, Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        // $spp = StrukPelayananPenjamin::where('norec', $norec)
        //     ->where('koders', $kdProfile)
        //     ->where('aktif', true)
        //     ->first();
        $spp = collect(DB::select("
            SELECT spp.totalbiaya,spp.totalsudahdibayar,pm.norm AS nocm,jk.jeniskelamin,
                   kp.kelompokpasien,pm.namapasien,pd.noregistrasi,spp.nostrukidfk,
                   EXTRACT(YEAR FROM AGE(now(), sp.tglstruk)) || ' Th ' || EXTRACT(MONTH FROM AGE(now(), sp.tglstruk)) || ' Bln ' ||
                   EXTRACT(DAY FROM AGE(now(), sp.tglstruk)) || ' Hr ' umurpiutang
            FROM strukpelayananpenjamintr AS spp
            INNER JOIN strukpelayanantr AS sp ON sp.norec = spp.nostrukidfk
            INNER JOIN registrasipasientr AS pd ON pd.norec = sp.registrasipasienfk
            INNER JOIN pasienmt AS pm ON pm.id = pd.normidfk
            LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk
            LEFT JOIN jeniskelaminmt AS jk ON jk.id = pm.jeniskelaminidfk
            WHERE spp.norec = '$norec';            
        "))->first();
        $sbp = StrukBuktiPenerimaan::where('nostrukidfk', $spp->nostrukidfk)->orderBy('nosbm')
            ->where('koders', $kdProfile)
            ->where('aktif', true)
            ->get();
        $detailPembayaran = array();
        foreach ($sbp as $item) {
            $detailPembayaran[] = array(
                'noSbm' => $item->nosbm,
                'tglPembayaran' => $item->tglsbm,
                'jlhPembayaran' => $item->totaldibayar
            );
        }

        $data = array(
            "noRegistrasi" => $spp->noregistrasi,
            "namaPasien" => $spp->namapasien,
            "jenisPenjamin" => $spp->kelompokpasien,
            "jenisKelamin" => $spp->jeniskelamin,
            "umurPiutang" => $spp->umurpiutang,
            "noCM" => $spp->nocm,
            "totalTagihan" => $spp->totalbiaya,
            "sudahDibayar" => $spp->totalsudahdibayar,
            "sisaPiutang" => $spp->totalbiaya - $spp->totalsudahdibayar,
            "detailPembayaran" => $detailPembayaran
        );

        return $this->respond($data);
    }

    protected function simpanCicilanPasien($request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.kdprofile = $kdProfile
                and lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        $noRegistrasi = '';
        \DB::beginTransaction();
        try {
            $strukPelayananPenjamin = StrukPelayananPenjamin::where('nostrukidfk', $request['parameterTambahan']['noRecStrukPelayanan'])
                ->where('koders', $kdProfile)
                ->first();
            // $strukPelayanan = StrukPelayanan::where('norec', $request['parameterTambahan']['noRecStrukPelayanan'])
            //     ->where('koders', $kdProfile)
            //     ->first();
            $spNorec =  $request['parameterTambahan']['noRecStrukPelayanan'];
            $strukPelayanan = collect(DB::select("
                SELECT sp.*,pd.kelompokpasienlastidfk,pd.noregistrasi
                FROM strukpelayanantr AS sp
                INNER JOIN registrasipasientr AS pd ON pd.norec = sp.registrasipasienfk
                INNER JOIN pasienmt AS pm ON pm.id = pd.normidfk
                LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk
                WHERE sp.koders = $kdProfile AND sp.norec = '$spNorec'
            "))->first();

            $NOSBM = array();
            foreach ($request['pembayaran'] as $pembayaran) {
                $strukBuktiPenerimanan = new StrukBuktiPenerimaan();
                $strukBuktiPenerimanan->norec = $strukBuktiPenerimanan->generateNewId();
                $strukBuktiPenerimanan->koders = $kdProfile;
                $strukBuktiPenerimanan->aktif = true;
                $strukBuktiPenerimanan->keteranganlainnya = "Pembayaran Cicilan Tagihan Pasien";
                $strukBuktiPenerimanan->nostrukidfk = $strukPelayanan->norec;
                $strukBuktiPenerimanan->kelompokpasienidfk = $strukPelayanan->kelompokpasienlastidfk;
                $strukBuktiPenerimanan->kelompoktransaksiidfk = 1;
                $strukBuktiPenerimanan->pegawaipenerimaidfk  = $dataPegawaiUser[0]->id;
                $strukBuktiPenerimanan->tglsbm  = $this->getDateTime();
                $strukBuktiPenerimanan->totaldibayar  = $pembayaran['nominal'];
                $strukBuktiPenerimanan->nosbm = $this->generateCode(new StrukBuktiPenerimaan, 'nosbm', 14, 'IVN-' . $this->getDateTime()->format('ym'), $kdProfile);
                $NOSBM[] = $strukBuktiPenerimanan->nosbm;
                $nostrukfkSTR = $strukPelayanan->norec;
                $strukBuktiPenerimanan->save();
                $sbmNorec = $strukBuktiPenerimanan->norec;
                $strukPelayananPenjamin->totalsudahdibayar = $strukPelayananPenjamin->totalsudahdibayar + $pembayaran['nominal'];

                $SBPCB = new StrukBuktiPenerimaanCaraBayar();
                $SBPCB->norec = $SBPCB->generateNewId();
                $SBPCB->koders = $kdProfile;
                $SBPCB->aktif = 1;
                $SBPCB->nosbmidfk = $strukBuktiPenerimanan->norec;
                $SBPCB->carabayaridfk = $pembayaran['caraBayar']['id'];
                if (isset($pembayaran['detailBank'])) {
                    $SBPCB->jeniskartuidfk = $pembayaran['detailBank']['jenisKartu']['id'];
                    $SBPCB->nokartuaccount = $pembayaran['detailBank']['noKartu'];
                    $SBPCB->namabankprovider = $pembayaran['detailBank']['namaKartu'];
                    $SBPCB->namapemilik = $pembayaran['detailBank']['namaKartu'];
                }
                $SBPCB->save();
                $strukPelayananPenjamin->save();
            }
            // $pd = $strukPelayanan->pasien_daftar;
            $noRegistrasi = $strukPelayanan->noregistrasi;

            $this->transStatus = true;
            $ReportTrans = "Simpan Pembayaran Berhasil";
        } catch (\Exception $e) {
            $this->transStatus = false;
            $ReportTrans = "Simpan Pembayaran Gagal";
        }

        if ($this->transStatus) {
            DB::commit();
            $result = array(
                "status" => 201,
                'noSBM' => $NOSBM,
                'norec' => $sbmNorec,
                'noReg' => $noRegistrasi,
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

    public function daftarTagihanNonLayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $list = explode(',', $this->settingDataFixed('KdJenisTransaksiNonLayanan', $kdProfile));
        $KdList = [];
        foreach ($list as $item) {
            $KdList[] =  (int)$item;
        }

        $dataNonLayanan = \DB::table('strukpelayanantr as sp')
            ->join('strukpelayanandetailtr as spd', 'spd.nostrukidfk', '=', 'sp.norec')
            ->join('jenistransaksimt as kt', 'kt.id', '=', 'sp.kelompoktransaksiidfk')
            ->select(DB::raw("
                 sp.nostruk,sp.norec,sp.tglstruk,sp.namapasien_klien AS namapasien,kt.kelompoktransaksi AS jenistagihan,sp.keteranganlainnya,
                 sp.nosbklastidfk,sp.nosbmlastidfk,kt.id AS jenistagihanid,
                 CAST(SUM(((CAST (spd.qtyproduk AS FLOAT)*(CAST (spd.hargasatuan AS FLOAT)-CAST (spd.hargadiscount AS FLOAT)))
                 + CASE WHEN spd.hargatambahan IS NULL THEN 0 ELSE CAST (spd.hargatambahan AS FLOAT) END)* 
                 CASE WHEN spd.qtyoranglast IS NULL THEN 1 ELSE CAST (spd.qtyoranglast AS FLOAT) END) AS FLOAT)AS totalharusdibayar,
                 CASE WHEN sp.nosbmlastidfk IS NULL THEN 'Belum Bayar' ELSE 'Lunas' END AS status
            "))
            ->where('sp.koders', $kdProfile)
            ->whereNotNull('sp.totalharusdibayar')
            ->where('sp.aktif', '=', true)
            ->groupBy(DB::raw("
                 sp.norec,sp.tglstruk,sp.namapasien_klien,kt.kelompoktransaksi,sp.keteranganlainnya,
				 sp.nosbklastidfk,sp.nosbmlastidfk,kt.id
            "));

        $filter = $request->all();
        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "") {
            $dataNonLayanan = $dataNonLayanan->where('sp.tglstruk', '>=', $filter['tglAwal']);
        }

        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "") {
            $dataNonLayanan = $dataNonLayanan->where('sp.tglstruk', '<=', $filter['tglAkhir']);
        }

        if (isset($filter['jenisTagihanId']) && $filter['jenisTagihanId'] != "") {
            $dataNonLayanan = $dataNonLayanan->where('kt.id', '=', $filter['jenisTagihanId']);
        } else {
            $dataNonLayanan = $dataNonLayanan->whereIn('sp.kelompoktransaksiidfk', $KdList);
        }

        if (isset($filter['namaPelanggan']) && $filter['namaPelanggan'] != "") {
            $dataNonLayanan = $dataNonLayanan->where('sp.namapasien_klien', 'ilike', '%' . $filter['namaPelanggan'] . '%');
        }

        if (isset($filter['status']) && $filter['status'] != "") {
            if ($filter['status'] == 'Lunas') {
                $dataNonLayanan = $dataNonLayanan->whereNotNull('sp.nosbmlastfk');
            } else {
                $dataNonLayanan = $dataNonLayanan->whereNull('sp.nosbmlastfk');
            }
        }
        $dataNonLayanan = $dataNonLayanan->get();
        return $this->respond($dataNonLayanan);
    }

    public function detailTagihanNonLayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $noRec = $request['norec_sp'];
        $strukPelayanan = StrukPelayanan::where('norec', $noRec)->where('koders', $kdProfile)->first();
        $kelTrans = collect(DB::select("select * from jenistransaksimt where id = $strukPelayanan->kelompoktransaksiidfk"))->first();
        $strukPelayananDetail = collect(DB::select("
            SELECT spd.*,pr.namaproduk
            FROM strukpelayanandetailtr AS spd 
            INNER JOIN pelayananmt AS pr ON pr.id = spd.produkidfk
            WHERE spd.koders = $kdProfile AND spd.nostrukidfk = '$noRec'
        "));
        $totalBilling = 0;
        $totalKlaim = 0;
        $detailTagihan = array();
        foreach ($strukPelayananDetail as $value) {
            $totalBilling = $totalBilling + ($value->harganetto * $value->qtyproduk);
            $totalKlaim = $totalKlaim + ($value->hargasatuanppenjamin * $value->qtyproduk);
            $qtyOrang = 1;
            if ($value->qtyoranglast != null) {
                $qtyOrang = $value->qtyoranglast;
            }
            $detailTagihan[] = array(
                'namaLayanan'  => $value->namaproduk,
                'namaproduk' => $value->namaproduk,
                'id' => $value->produkidfk,
                'jumlah'  => $value->qtyproduk,
                'qtyoranglast' => $qtyOrang,
                'harga'  => $value->hargasatuan,
                'jasa' => $value->hargatambahan,
                'total'  => $value->hargasatuan * $value->qtyproduk,
                'totalK'  => (($value->hargasatuan * $value->qtyoranglast) * $value->qtyproduk) + $value->hargatambahan,
                'keterangan' => $value->keteranganlainnya,
            );
        }
        $totalBayar = $totalBilling - $totalKlaim;
        $result = array(
            "tglTransaksi" => $strukPelayanan->tglstruk,
            "namaPasien_klien"  => $strukPelayanan->namapasien_klien,
            "jumlahBayar" => 0,
            "kdkelompokTransaksi" => $strukPelayanan->kelompoktransaksiidfk,
            "kelompokTransaksi" => $kelTrans->reportdisplay,
            "notelepon" => $strukPelayanan->noteleponfaks,
            "keterangan" => $strukPelayanan->keteranganlainnya,
            "noRecStruk"  => $strukPelayanan->norec,
            "totalBilling" => $strukPelayanan->totalharusdibayar,
            "detailTagihan"  => $detailTagihan,

        );

        return $this->respond($result, "Detail Tagihan Pasien");
    }

    protected  function getDetailPembayaranNonLayanan(Request $request)
    {
        $result = array(
            "noRecStrukPelayanan"  => $request['noRecStrukPelayanan'],
            "jumlahBayar"  => $request['jumlahBayar'],
        );
        return $this->respond($result, "Data Pembayaran");
    }

    protected function simpanPembayaranTagihanNonLayanan($request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.kdprofile = $kdProfile
                and lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        $noRegistrasi = '';
        \DB::beginTransaction();
        try {
            $strukPelayanan = StrukPelayanan::where('norec', $request['parameterTambahan']['noRecStrukPelayanan'])
                ->where('koders', $kdProfile)
                ->first();
            $sisa = 0;
            $deposit = 0;
            $NOSBM = array();
            foreach ($request['pembayaran'] as $pembayaran) {
                $strukBuktiPenerimanan = new StrukBuktiPenerimaan();
                $strukBuktiPenerimanan->norec = $strukBuktiPenerimanan->generateNewId();
                $strukBuktiPenerimanan->koders = $kdProfile;
                $strukBuktiPenerimanan->aktif = true;
                $strukBuktiPenerimanan->nosbm = $this->generateCode(new StrukBuktiPenerimaan, 'nosbm', 14, 'IVN-' . $this->getDateTime()->format('ym'), $kdProfile);
                $strukBuktiPenerimanan->keteranganlainnya = "Pembayaran Tagihan Non Layanan";
                $strukBuktiPenerimanan->nostrukidfk = $strukPelayanan->norec;
                $strukBuktiPenerimanan->kelompoktransaksiidfk = $strukPelayanan->kelompoktransaksiidfk;
                $strukBuktiPenerimanan->pegawaipenerimaidfk = $dataPegawaiUser[0]->id;
                $strukBuktiPenerimanan->tglsbm = $request['tglsbm'];
                $strukBuktiPenerimanan->totaldibayar = $pembayaran['nominal'];
                $strukBuktiPenerimanan->save();
                $NOSBM[] = $strukBuktiPenerimanan->nosbm;
                $nostrukfkSTR = $strukPelayanan->norec;
                $norecSBM = $strukBuktiPenerimanan->norec;

                $SBPCB = new StrukBuktiPenerimaanCaraBayar();
                $SBPCB->norec = $SBPCB->generateNewId();
                $SBPCB->koders = $kdProfile;
                $SBPCB->aktif = true;
                $SBPCB->nosbmidfk = $strukBuktiPenerimanan->norec;
                $SBPCB->carabayaridfk = $pembayaran['caraBayar']['id'];
                $SBPCB->save();

                $strukPelayanan->nosbmlastidfk = $norecSBM;
                $strukPelayanan->save();
            }

            $this->transStatus = true;
            $ReportTrans = "Selesai";
        } catch (\Exception $e) {
            $this->transStatus = false;
            $ReportTrans = "Gagal coba lagi";
        }

        if ($this->transStatus) {
            DB::commit();
            $result = array(
                "status" => 201,
                'noSBM' => $NOSBM,
                'noReg' => $noRegistrasi,
                'norec' => $norecSBM,
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

    public function SaveInputTagihan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.kdprofile = $kdProfile
                and lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        $noStruk = $this->generateCode(new StrukPelayanan, 'nostruk', 10, 'NSR' . $this->getDateTime()->format('ym'), $kdProfile);
        if ($noStruk == '') {
            $ReportTrans = "Gagal mengumpukan data, Coba lagi.!";
            \DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "as" => 'slvR',
            );
            return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
        }
        \DB::beginTransaction();
        try {
            if ($request['norec'] == '-') {
                $SP = new StrukPelayanan();
                $norecSP = $SP->generateNewId();
                $noStruk = $noStruk;
                $SP->norec = $norecSP;
                $SP->koders = $kdProfile;
                $SP->aktif = true;
                $SP->nostruk = $noStruk;
            } else {
                $SP = StrukPelayanan::where('norec', $request['norec'])->where('koders', $kdProfile)->first();
                $DelSPD = StrukPelayananDetail::where('nostrukidfk', $request['norec'])->where('koders', $kdProfile)->delete();
            }
            $SP->kelompoktransaksiidfk = $request['kelompoktransaksifk'];
            $SP->namapasien_klien = $request['namapasien']; //namapasien
            $SP->nostruk_intern = $request['nocm']; //nocm
            $SP->namarekanan = 'Umum/Tunai';
            if (isset($request['tgllahir']) && $request['tgllahir'] != "Invalid date") {
                $SP->tglfaktur =  $request['tgllahir']; //tgllahir
            }
            $SP->noteleponfaks =  $request['notelepon']; //notlp
            $SP->namatempattujuan =  $request['alamat']; //alamat
            $SP->jeniskelaminidfk = $request['strukresep']['jkid'];
            $SP->pegawaipenanggungjawabidfk = $dataPegawaiUser[0]->id;
            $SP->keteranganlainnya = $request['keteranganlainnya'];
            $SP->tglstruk = $request['tglstruk'];
            $SP->totalharusdibayar = $request['totalharusdibayar'];
            $SP->save();
            $norecSP = $SP->norec;

            foreach ($request['details'] as $item) {
                $SPD = new StrukPelayananDetail();
                $norecKS = $SPD->generateNewId();
                $SPD->norec = $norecKS;
                $SPD->koders = $kdProfile;
                $SPD->aktif = true;
                $SPD->nostrukidfk = $norecSP;
                $SPD->produkidfk = $item['id'];
                $SPD->hargadiscount = 0;
                $SPD->hargadiscountgive = 0;
                $SPD->hargadiscountsave = 0;
                $SPD->harganetto = $item['harga'];
                $SPD->hargapph = 0;
                $SPD->hargappn = 0;
                $SPD->hargasatuan = $item['harga'];
                $SPD->hargasatuandijamin = 0;
                $SPD->hargasatuanppenjamin = 0;
                $SPD->hargasatuanpprofile = 0;
                $SPD->hargatambahan = 0;
                $SPD->isonsiteservice = 0;
                $SPD->persendiscount = 0;
                $SPD->qtyproduk = $item['jumlah'];
                $SPD->qtyoranglast = $item['qtyoranglast'];
                $SPD->qtyprodukoutext = 0;
                $SPD->qtyprodukoutint = 0;
                $SPD->qtyprodukretur = 0;
                $SPD->satuan = 0;
                $SPD->tglpelayanan = $request['tglstruk'];
                $SPD->is_terbayar = 0;
                $SPD->linetotal = 0;
                $SPD->keteranganlainnya = $item['keterangan'];
                $SPD->save();
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $SP,
                "as" => 'slvR',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDetailTransaksiNonLayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataStruk = \DB::table('strukpelayanantr as sr')
            ->JOIN('pegawaimt as pg', 'pg.id', '=', 'sr.pegawaipenanggungjawabidfk')
            ->leftJoin('jeniskelaminmt AS jk', 'jk.id', '=', 'sr.jeniskelaminidfk')
            ->leftJoin('jenistransaksimt AS jt', 'jt.id', '=', 'sr.kelompoktransaksiidfk')
            ->select(DB::raw("
                sr.nostruk,pg.id AS pgid,pg.namalengkap,sr.kelompoktransaksiidfk,jt.kelompoktransaksi,
			    sr.nostruk_intern AS norm,sr.namapasien_klien AS namapasien,sr.tglfaktur AS tgllahir,
			    sr.noteleponfaks AS notelepon,sr.namatempattujuan AS alamatlengkap,sr.tglstruk AS tgltransaksi,
			    sr.jeniskelaminidfk AS jkid,jk.jeniskelamin
            "))
            ->where('sr.koders', $kdProfile);

        if (isset($request['noRec']) && $request['noRec'] != "" && $request['noRec'] != "undefined") {
            $dataStruk = $dataStruk->where('sr.norec', '=', $request['noRec']);
        }

        $dataStruk = $dataStruk->first();

        $data = \DB::table('strukpelayanandetailtr as spd')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'spd.produkidfk')
            ->select(DB::raw("
                pr.id,pr.namaproduk,spd.keteranganlainnya,spd.qtyproduk,spd.qtyoranglast,spd.hargasatuan
            "))
            ->where('spd.koders', $kdProfile);

        if (isset($request['noRec']) && $request['noRec'] != "" && $request['noRec'] != "undefined") {
            $data = $data->where('spd.nostrukidfk', '=', $request['noRec']);
        }
        $data = $data->get();

        $pelayananPasien = [];
        $i = 0;
        foreach ($data as $item) {
            $i = $i + 1;
            $pelayananPasien[] = array(
                'no' => $i,
                'id' => $item->id,
                'namaproduk' => $item->namaproduk,
                'jumlah' => (float)$item->qtyproduk,
                'qtyoranglast' => (float)$item->qtyoranglast,
                'harga' => (float)$item->hargasatuan,
                'total' => ((float)$item->qtyproduk * (float)$item->hargasatuan) * (float)$item->qtyoranglast,
                'keteranganlainnya' => $item->keteranganlainnya,
            );
        }

        $result = array(
            'datahead' => $dataStruk,
            'details' => $pelayananPasien,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function BatalInputTagihanNonLayanan(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        \DB::beginTransaction();
        try {
            $Kel = StrukPelayanan::where('norec', $request['norec'])
                ->where('koders', $kdProfile)
                ->update([
                    'aktif' => false,
                ]);

            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus) {
            $ReportTrans = 'Selesai';
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'tb' => 'godU',
            );
        } else {
            $ReportTrans = 'Gagal coba lagi';
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message'  => $ReportTrans,
                'tb' => 'godU',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function daftarTagihanPasien(Request $request){
        $result = array();
        $filter = $request->all();
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataStrukPelayanan= \DB::table('strukpelayanantr as sp')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'sp.registrasipasienfk')
            ->join('pasienmt as pm', 'pm.id', '=', 'pd.normidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftJoin('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->leftJoin('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftJoin('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
            ->select(DB::raw("
                pd.noregistrasi,pm.norm AS nocm,pm.namapasien,pd.tglregistrasi,pd.tglpulang,ru.namaruangan,kp.kelompokpasien,
			    sp.totalharusdibayar,sp.norec,sp.nostruk,kls.namakelas,sp.tglstruk,sp.totalprekanan,ru.id AS ruanganid,
			    dept.id AS departmentid,pd.norec AS norec_pd
            "))
            ->where('sp.koders', $kdProfile)
            ->where('sp.aktif', true)
            ->whereNotNull('sp.totalharusdibayar')
            ->whereNotNull('sp.registrasipasienfk')
            ->whereRaw("(sp.iskembalideposit IS NULL OR sp.iskembalideposit = false)")
            ->where('sp.totalharusdibayar','<',0);

        if(isset($filter['noReg']) && $filter['noReg']!="" && $filter['noReg']!="undefined"){
            $dataStrukPelayanan  = $dataStrukPelayanan->where('pd.noregistrasi', $filter['noReg']);
        }

        if(isset($filter['noRm']) && $filter['noRm']!="" && $filter['noRm']!="undefined"){
            $dataStrukPelayanan  = $dataStrukPelayanan->where('pm.norm','ilike','%'. $filter['noRm'].'%');
        }

        if(isset($filter['tglAwal']) && $filter['tglAwal']!="" && $filter['tglAwal']!="undefined"){
            $dataStrukPelayanan = $dataStrukPelayanan->where('sp.tglstruk','>=', $filter['tglAwal']);
        }

        if(isset($filter['tglAkhir']) && $filter['tglAkhir']!="" && $filter['tglAkhir']!="undefined"){
            $tgl= $filter['tglAkhir'];//." 23:59:59";
            $dataStrukPelayanan = $dataStrukPelayanan->where('sp.tglstruk','<=', $tgl);
        }

        if(isset($filter['instalasiId']) && $filter['instalasiId']!="" && $filter['instalasiId']!="undefined"){
            $dataStrukPelayanan = $dataStrukPelayanan->where('dept.id','=', $filter['instalasiId']);
        }

        if(isset($filter['ruanganId']) && $filter['ruanganId']!="" && $filter['ruanganId']!="undefined"){
            $dataStrukPelayanan = $dataStrukPelayanan->where('ru.id','=', $filter['ruanganId']);
        }

        if(isset($filter['namaPasien']) && $filter['namaPasien']!="" && $filter['namaPasien']!="undefined"){
            $dataStrukPelayanan = $dataStrukPelayanan->where('pm.namapasien','ilike', '%'.$filter['namaPasien'].'%');
        }

        if(isset($filter['kelompokPasienId']) && $filter['kelompokPasienId']!="" && $filter['kelompokPasienId']!="undefined"){
            $dataStrukPelayanan = $dataStrukPelayanan->where('pd.kelompokpasienlastidfk','=', $filter['kelompokPasienId']);
        }

        if(isset($filter['status']) && $filter['status']!=""){
            if($filter['status']=='Lunas'){
                $dataStrukPelayanan  = $dataStrukPelayanan->whereNotNull('sp.nosbmlastfk');
            }else{
                $dataStrukPelayanan  = $dataStrukPelayanan->whereNull('sp.nosbmlastfk');
            }
        }
        if (isset($filter['jmlRows']) && $filter['jmlRows'] != "" && $filter['jmlRows'] != "undefined") {
            $dataStrukPelayanan = $dataStrukPelayanan->take($filter['jmlRows']);
        }
        $dataStrukPelayanan = $dataStrukPelayanan->get();

        foreach ($dataStrukPelayanan as $key => $item){
            $sp=StrukPelayanan::find($item->norec);
            $result[] = array(
                'noRec' => $item->norec,
                'tglStruk' => $item->tglstruk,
                'tglMasuk' => $item->tglregistrasi,
                'tglPulang' => $item->tglpulang,
                'noRegistrasi' => $item->noregistrasi,
                'namaPasien' => $item->namapasien,
                'noCm' => $item->nocm,
                'kelasRawat' => $item->namakelas,
                'lastRuangan' => $item->namaruangan,
                'jenisPasien' => $item->kelompokpasien,
                'totalBilling' => $item->totalharusdibayar,
                'totalKlaim' => $item->totalprekanan ,
                'totalBayar' => $item->totalharusdibayar,
                'statusBayar' => $sp->statusBayar,
                'ruanganId' => $item->ruanganid,
                'departmentId' => $item->departmentid,
                'norec_pd' => $item->norec_pd,
            );
        }
        return $this->respond($result, 'Data Daftar Pasien');
    }

    public function getDataLaporanKasir(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $filter = $request->all();
        $tglAwal = $filter['dateStartTglSbm'];
        $tglAkhir = $filter['dateEndTglSbm'];
        $paramCaraBayar = "";
        if (isset($filter['idCaraBayar']) && $filter['idCaraBayar'] != "" && $filter['idCaraBayar'] != "undefined") {
            $paramCaraBayar = " AND cb.id = " . $filter['idCaraBayar'];
        }
        $paramKelTrans = "";
        if (isset($filter['idKelTransaksi']) && $filter['idKelTransaksi'] != "" && $filter['idKelTransaksi'] != "undefined") {
            $paramKelTrans = " AND kt.id = " .$filter['idKelTransaksi'];
        }
        $paramKasir = "";
        if (isset($request['KasirArr']) && $request['KasirArr'] != "" && $request['KasirArr'] != "undefined") {
            $arrRuang = explode(',',$request['KasirArr']) ;
            $kodeRuang = [];
            $str = '';
            $d=0;
            foreach ( $arrRuang as $item){
                if ($str == ''){
                    $str = $item;
                }else{
                    $str = $str . ',' . $item;
                }
                $d = $d + 1;
            }
            $paramKasir = " AND sbm.pegawaipenerimaidfk IN ($str)";
        }

        $data = collect(DB::select("
            SELECT x.tipepasien,x.ruangan,SUM(x.totalprekanan)+SUM(x.totalharusdibayar)+SUM(x.tanggunganrs)+SUM(x.pembebasan)+SUM(x.administrasi) AS banyak,
			       SUM(x.totalprekanan) AS totalprekanan,SUM(x.tanggunganrs) AS tanggunganrs,SUM(x.pembebasan) AS pembebasan,SUM(x.totalharusdibayar) AS totalharusdibayar,
			       SUM(x.administrasi) AS administrasi,SUM(x.totaldibayar) AS totaldibayar,(
				   SUM(x.totalharusdibayar)+SUM(x.tanggunganrs)+SUM(x.pembebasan)+SUM(x.administrasi))-SUM(x.totaldibayar) AS sisa
            FROM(SELECT CASE WHEN sp.registrasipasienfk IS NULL THEN 'Umum/Tunai' ELSE kp.kelompokpasien END AS tipepasien,
                   CASE WHEN sp.registrasipasienfk IS NULL THEN ru1.namaruangan ELSE ru.namaruangan END AS ruangan,
                   sum(sbm.totaldibayar),CASE WHEN sp.totalprekanan IS NULL THEN 0 ELSE sp.totalprekanan END AS totalprekanan,0 AS tanggunganrs,
                   sp.totalharusdibayar,0 AS pembebasan,0 AS administrasi,sbm.totaldibayar,CASE WHEN sbm.totalsisapiutang IS NULL THEN 0 
                   ELSE sbm.totalsisapiutang END AS totalsisapiutang 
            FROM strukpelayanantr AS sp 
            INNER JOIN strukbuktipenerimaantr AS sbm ON sbm.norec = sp.nosbmlastidfk
            LEFT JOIN registrasipasientr AS rg ON rg.norec = sp.registrasipasienfk
            LEFT JOIN kelompokpasienmt AS kp ON kp.id = rg.kelompokpasienlastidfk
            LEFT JOIN ruanganmt AS ru ON ru.id = rg.ruanganlastidfk
            LEFT JOIN ruanganmt AS ru1 ON ru1.id = sp.ruanganidfk
            LEFT JOIN pegawaimt AS pg ON pg.id = sbm.pegawaipenerimaidfk
            WHERE sbm.aktif = true AND sp.aktif = TRUE
            AND sbm.koders = $kdProfile AND sp.koders = $kdProfile
            AND sbm.tglsbm BETWEEN '$tglAwal' AND '$tglAkhir'
            AND (sp.iskembalideposit IS NULL OR sp.iskembalideposit = false)
            $paramCaraBayar
            $paramKelTrans
            $paramKasir
            GROUP BY sp.registrasipasienfk,kp.kelompokpasien,ru.namaruangan,ru1.namaruangan,
				     sbm.totaldibayar,sp.totalprekanan,sp.totalharusdibayar,sbm.totaldibayar,
 				     sbm.totalsisapiutang ) AS x
 		    GROUP BY x.tipepasien,x.ruangan
        "));

        return $this->respond($data);
    }

    public function getDataComboLapKasir(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->join('pegawaimt as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select('lu.objectpegawaifk', 'pg.namalengkap')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', (int)$kdProfile)
            ->first();

        $kdKelUserKasir =  (int) $this->settingDataFixed('kdKelUserKasir', $kdProfile);
        $dataCaraBayar = \DB::table('carabayarmt as ru')
            ->select('ru.id', 'ru.carabayar')
            ->where('ru.aktif', true)
            ->orderBy('ru.id')
            ->get();

        $jenisTransKasir = explode(',', $this->settingDataFixed('KdJenisTransaksiKasir', $kdProfile));
        $listjenisTransKasir = [];
        foreach ($jenisTransKasir as $itemTransKasir) {
            $listjenisTransKasir[] = (int)$itemTransKasir;
        }
        $dataJenisTransaksi = \DB::table('jenistransaksimt as ru')
            ->select('ru.id', 'ru.kelompoktransaksi')
            ->where('ru.aktif', true)
            ->whereIn('ru.id', $listjenisTransKasir)
            ->orderBy('ru.id')
            ->get();

        $dataPetugasKasir = \DB::select(
            DB::raw("
                SELECT pg.id,pg.namalengkap
                FROM loginuser_s lu
                INNER JOIN pegawaimt pg ON lu.objectpegawaifk=pg.id
                WHERE lu.kdprofile = $kdProfile AND objectkelompokuserfk=:id AND pg.aktif=true"),
            array(
                'id' => $kdKelUserKasir,
            )
        );

        $kdTransaksiNonLayanan = explode(',', $this->settingDataFixed('KdJenisTransaksiNonLayanan', $kdProfile));
        $listkdTransaksiNonLayanan = [];
        foreach ($kdTransaksiNonLayanan as $itemTrans) {
            $listkdTransaksiNonLayanan[] = (int)$itemTrans;
        }
        $JenisTransaksiNonLayanan = \DB::table('jenistransaksimt as ru')
            ->select('ru.id', 'ru.kelompoktransaksi')
            ->where('ru.aktif', true)
            ->whereIn('ru.id', $listkdTransaksiNonLayanan)
            ->orderBy('ru.id')
            ->get();

        $result = array(
            'dataLogin' => $dataPegawai,
            'carabayar' => $dataCaraBayar,
            'jenistransaksi' => $dataJenisTransaksi,
            'petugaskasir' => $dataPetugasKasir,
            'jenistransaksinonlayanan' => $JenisTransaksiNonLayanan,
            'message' => 'godU',
        );

        return $this->respond($result);
    }
}
