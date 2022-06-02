<?php


namespace App\Http\Controllers\RawatInap;

use App\Http\Controllers\ApiController;
use App\Master\Pasien;
use App\Master\TempatTidur;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\TransaksiKirimDetail;
use App\Transaksi\TransaksiOrderDetail;
use App\Transaksi\TransaksiKirim;
use App\Transaksi\TransaksiOrder;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Transaksi\RegistrasiPasien;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\TransaksiPasienDetail;
use App\Master\Ruangan;
use App\Master\Instalasi;
use App\Traits\Valet;
use App\Traits\PelayananPasienTrait;
use DB;
use Carbon\Carbon;

class RawatInapC extends ApiController
{
    use Valet, PelayananPasienTrait;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getPindahPasienByNoreg2(Request $r)
    {

        $norec_pd = $r['norec_pd'];
        $norec_apd = $r['norec_apd'];
        $kdProfile = $this->getDataKdProfile($r);
        $data = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->leftJoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftjoin('pendidikanmt as pdd', 'pdd.id', '=', 'ps.pendidikanidfk')
            ->leftjoin('pekerjaanmt as pk', 'pk.id', '=', 'ps.pekerjaanidfk')
            ->join('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('instalasimt as dpm', 'dpm.id', '=', 'ru.instalasiidfk')
            ->leftjoin('pegawaimt as peg', 'peg.id', '=', 'pd.pegawaiidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->LEFTjoin('jenispelayananmt as jpl', 'jpl.id', '=', 'pd.jenispelayananidfk')
            ->select(
                'ps.norm as nocm',
                'ps.id as nocmfk',
                'ps.noidentitas',
                'ps.namapasien',
                'pd.noregistrasi',
                'pd.tglregistrasi',
                'jk.jeniskelamin',
                'ps.tgllahir',
                'alm.alamatlengkap',
                'pdd.pendidikan',
                'pk.pekerjaan',
                'ps.nohp as notelepon',
                'ps.jeniskelaminidfk as objectjeniskelaminfk',
                'apd.ruanganidfk as objectruanganfk',
                'ru.namaruangan',
                'apd.norec as norec_apd',
                'pd.norec as norec_pd',
                'kps.kelompokpasien',
                'kls.namakelas',
                'apd.kelasidfk as objectkelasfk',
                'pd.kelompokpasienlastidfk as objectkelompokpasienlastfk',
                'pd.rekananidfk as objectrekananfk',
                'rk.namarekanan',
                'pd.ruanganlastidfk as objectruanganlastfk',
                'jpl.jenispelayanan',
                'pd.asalrujukanidfk as objectasalrujukanfk',
                'ru.kdinternal',
                'jpl.id as objectjenispelayananfk',
                'pd.pegawaiidfk as objectpegawaifk',
                'pd.statuspasien',
                'ps.nobpjs',
                'pd.statuspasien',
                'ps.idpasien2 as id_ibu',
                DB::raw("CASE WHEN pd.tglpulang is null THEN
                'true'
         WHEN pd.tglpulang != pd.tglregistrasi THEN
                'true' ELSE'false' 
            END AS israwatinap")
            )
            ->where('pd.norec', '=', $norec_pd)
            ->where('apd.norec', '=', $norec_apd)
            ->where('pd.koders', (int)$kdProfile)
            ->where('pd.aktif', true)
            ->whereNull('pd.tglpulang')
            ->first();

        return $this->respond($data);
    }

    public function getComboPindahPulang(Request $request)
    {
        $dataLogin = $request->all();
        $kdProfile = $this->getDataKdProfile($request);
        $statusKeluar = \DB::table('statuskeluarmt as st')
            ->select('st.id', 'st.statuskeluar')
            ->where('st.aktif', true)
            ->where('st.koders', $kdProfile)
            ->orderBy('st.statuskeluar')
            ->get();
        $kondisiKeluar = \DB::table('kondisipasienmt as kp')
            ->select('kp.id', 'kp.kondisipasien')
            ->where('kp.aktif', true)
            ->where('kp.koders', $kdProfile)
            ->orderBy('kp.kondisipasien')
            ->get();
        $kelas = \DB::table('kelasmt as kls')
            ->select('kls.id', 'kls.namakelas')
            ->where('kls.aktif', true)
            ->where('kls.koders', $kdProfile)
            ->orderBy('kls.namakelas')
            ->get();
        $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile));
        $kdDepartemenRawatInap = [];
        foreach ($deptRanap as $itemRanap) {
            $kdDepartemenRawatInap[] = (int)$itemRanap;
        }
        $ruanganInap = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk as objectdepartemenfk')
            ->where('ru.aktif', true)
            ->wherein('ru.instalasiidfk', $kdDepartemenRawatInap)
            ->where('ru.koders', $kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();
        $statusPulang = \DB::table('statuspulangmt as sp')
            ->select('sp.id', 'sp.statuspulang')
            ->where('sp.aktif', true)
            ->where('sp.koders', $kdProfile)
            ->orderBy('sp.statuspulang')
            ->get();
        $hubunganKeluarga = \DB::table('hubungankeluargamt as sp')
            ->select('sp.id', 'sp.hubungankeluarga')
            ->where('sp.aktif', true)
            ->where('sp.koders', $kdProfile)
            ->orderBy('sp.hubungankeluarga')
            ->get();
        $penyebabKematian = \DB::table('penyebabkematianmt as sp')
            ->select('sp.id', 'sp.penyebabkematian')
            ->where('sp.koders', $kdProfile)
            ->where('sp.aktif', true)
            ->orderBy('sp.penyebabkematian')
            ->get();
        $idPindah = $this->settingDataFixed('idStatusKeluarPindah', $kdProfile);
        $idStatusKeluarPulang = $this->settingDataFixed('idStatusKeluarPulang', $kdProfile);
        $idStatusKeluarMeninggal = $this->settingDataFixed('idStatusKeluarMeninggal', $kdProfile);
        $idKelompokPasienBPJS = $this->settingDataFixed('idKelompokPasienBPJS', $kdProfile);

        $result = array(
            'statuskeluar' => $statusKeluar,
            'kondisipasien' => $kondisiKeluar,
            'kelas' => $kelas,
            'ruanganinap' => $ruanganInap,
            'statuspulang' => $statusPulang,
            'hubungankeluarga' => $hubunganKeluarga,
            'penyebabkematian' => $penyebabKematian,
            'idPindah' => $idPindah,
            'idStatusKeluarMeninggal' => $idStatusKeluarMeninggal,
            'idStatusKeluarPulang' => $idStatusKeluarPulang,
            'idKelompokPasienBPJS' => $idKelompokPasienBPJS,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getKamarIbuLast(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $idIbu = $request['id_ibu'];
        $noCm = $request['nocm'];
        $data = DB::select(DB::raw("
                select * from
                        (select pd.norec as norec_pd,  apd.norec as norec_apd,  ps.norm as nocm,
                        pd.noregistrasi,  apd.kamaridfk as objectkamarfk,  tt.id as nobed,  kmr.namakamar,
                        tt.reportdisplay as tempattidur,ps.namapasien,
                        kls.id as kelasfk, kls.namakelas,ru.namaruangan,pd.ruanganlastidfk as objectruanganlastfk,
                        row_number() over (partition by apd.ruanganidfk order by apd.tglmasuk desc) as rownum
                        from registrasipasientr as pd
                        inner join daftarpasienruangantr as apd on apd.registrasipasienfk = pd.norec and pd.ruanganlastidfk=apd.ruanganidfk
                        inner join pasienmt as ps on ps.id = pd.normidfk
                        inner join ruanganmt as ru on ru.id = pd.ruanganlastidfk
                        inner join kelasmt as kls on kls.id = pd.kelasidfk
                        left join kamarmt as kmr on kmr.id = apd.kamaridfk
                        left join tempattidurmt as tt on tt.id = apd.nobedidfk
                        where ps.idpasien2 ='$idIbu'
                        and ps.norm <> '$noCm'
                     and ps.namapasien not  ilike '%By Ny%'
                        and pd.tglpulang is null
             and pd.koders = $idProfile
        ) as x
        where x.rownum =1
               "));

        return $this->respond($data);
    }

    public function savePulangPasien(Request $request)
    {
        $detLogin = $request->all();
        $tglAyeuna = date('Y-m-d H:i:s');
        $r_NewPD = $request['pasiendaftar'];
        $r_NewAPD = $request['antrianpasiendiperiksa'];
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $idStatusKeluarMeninggal = $this->settingDataFixed('idStatusKeluarMeninggal', $kdProfile);
            $idPenyebabKematianLainnya = $this->settingDataFixed('idPenyebabKematianLainnya', $kdProfile);
            if ($r_NewPD['norec_pd'] != 'undefined' && $r_NewPD['noregistrasi'] != 'undefined' && $r_NewPD['objectstatuskeluarfk'] == $idStatusKeluarMeninggal) {
                $updatePD = RegistrasiPasien::where('noregistrasi', $r_NewPD['noregistrasi'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'hubungankeluargaambilpasienidfk' => $r_NewPD['objecthubungankeluargaambilpasienfk'],
                            'kondisipasienidfk' => $r_NewPD['objectkondisipasienfk'],
                            'namalengkapambilpasien' => $r_NewPD['namalengkapambilpasien'],
                            'penyebabkematianidfk' => $r_NewPD['objectpenyebabkematianfk'],
                            'statuskeluaridfk' => $r_NewPD['objectstatuskeluarfk'],
                            'statuspulangidfk' => $r_NewPD['objectstatuspulangfk'],
                            'tglmeninggal' => $r_NewPD['tglmeninggal'],
                            'tglpulang' => $r_NewPD['tglpulang'],
                        ]
                    );
            }
            if (
                $r_NewPD['norec_pd'] != 'undefined' && $r_NewPD['noregistrasi'] != 'undefined' && $r_NewPD['objectstatuskeluarfk'] == $idStatusKeluarMeninggal
                && $r_NewPD['objectpenyebabkematianfk'] == $idPenyebabKematianLainnya
            ) {
                $updatePD = RegistrasiPasien::where('noregistrasi', $r_NewPD['noregistrasi'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'hubungankeluargaambilpasienidfk' => $r_NewPD['objecthubungankeluargaambilpasienfk'],
                            'kondisipasienidfk' => $r_NewPD['objectkondisipasienfk'],
                            'namalengkapambilpasien' => $r_NewPD['namalengkapambilpasien'],
                            'penyebabkematianidfk' => $r_NewPD['objectpenyebabkematianfk'],
                            'statuskeluaridfk' => $r_NewPD['objectstatuskeluarfk'],
                            'statuspulangidfk' => $r_NewPD['objectstatuspulangfk'],
                            'tglmeninggal' => $r_NewPD['tglmeninggal'],
                            'tglpulang' => $r_NewPD['tglpulang'],
                            'keteranganpenyebabkematian' => $r_NewPD['keterangankematian'],
                        ]
                    );
            }
            if ($r_NewPD['norec_pd'] != 'undefined' && $r_NewPD['noregistrasi'] != 'undefined' && $r_NewPD['objectstatuskeluarfk'] != $idStatusKeluarMeninggal) {
                $updatePD = RegistrasiPasien::where('noregistrasi', $r_NewPD['noregistrasi'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'hubungankeluargaambilpasienidfk' => $r_NewPD['objecthubungankeluargaambilpasienfk'],
                            'kondisipasienidfk' => $r_NewPD['objectkondisipasienfk'],
                            'namalengkapambilpasien' => $r_NewPD['namalengkapambilpasien'],
                            'statuskeluaridfk' => $r_NewPD['objectstatuskeluarfk'],
                            'statuspulangidfk' => $r_NewPD['objectstatuspulangfk'],
                            'tglpulang' => $r_NewPD['tglpulang'],
                        ]
                    );
            }
            if ($r_NewPD['nocmfk'] != 'undefined' && $r_NewPD['objectstatuskeluarfk'] == $idStatusKeluarMeninggal) {
                $updatePS = Pasien::where('id', $r_NewPD['nocmfk'])
                    ->update(
                        [
                            'tglmeninggal' => $r_NewPD['tglmeninggal'],
                        ]
                    );
            }
            if ($r_NewAPD['norec_apd'] != 'undefined') {
                $updateAPD = DaftarPasienRuangan::where('norec', $r_NewAPD['norec_apd'])
                    ->update(
                        [
                            'tglkeluar' => $r_NewPD['tglpulang'],
                            "statuskeluaridfk" => $r_NewPD['objectstatuskeluarfk'],
                            "statuspulangidfk" => $r_NewPD['objectstatuspulangfk'],
                            "kondisipasienidfk" => $r_NewPD['objectkondisipasienfk'],
                            "tglmeninggal" => $r_NewPD['tglmeninggal'],
                            "penyebabkematian" => $r_NewPD['objectpenyebabkematianfk'],
                            "ketkematian" => $r_NewPD['keterangankematian'],
                        ]
                    );

                $ruangasal = DB::select(
                    DB::raw("select * from daftarpasienruangantr
                         where norec=:norec and ruanganidfk=:objectruanganasalfk;"),
                    array(
                        'norec' => $r_NewAPD['norec_apd'],
                        'objectruanganasalfk' => $r_NewAPD['objectruanganlastfk'],
                    )
                );

                //update statusbed jadi Kosong
                $set = $this->settingDataFixed('kdStatusBedKosong', $kdProfile);
                foreach ($ruangasal as $Hit) {
                    TempatTidur::where('id', $Hit->nobedidfk)->update(['statusbedidfk' => $set]);
                }
            }

            if ($request['strukorder']['norecorder'] != '') {
                $updateSO = TransaksiOrder::where('norec', $request['strukorder']['norecorder'])
                    ->update(
                        [
                            'statusorder' => 4,
                            'tglvalidasi' => $tglAyeuna
                        ]
                    );
            }
            //
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
                'as' => 'Xoxo',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $ReportTrans,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function savePindahPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $tglAyeuna = date('Y-m-d H:i:s');
        $r_NewPD = $request['pasiendaftar'];
        $r_NewAPD = $request['antrianpasiendiperiksa'];
        DB::beginTransaction();
        try {
            if ($r_NewPD['norec_pd'] != 'null' && $r_NewPD['norec_pd'] != 'undefined') {
                $updatePD = RegistrasiPasien::where('noregistrasi', $r_NewPD['noregistrasi'])
                    ->where('koders', $kdProfile)
                    ->update(
                        [
                            'ruanganlastidfk' => $r_NewPD['objectruanganlastfk'],
                            'kelasidfk' => $r_NewPD['objectkelasfk'],
                        ]
                    );
            }
            if ($r_NewAPD['norec_apd'] != 'null' && $r_NewAPD['norec_apd'] != 'undefined') {
                $updateAPD = DaftarPasienRuangan::where('norec', $r_NewAPD['norec_apd'])
                    ->update(
                        [
                            'tglkeluar' => $r_NewAPD['tglmasuk'],
                            "statuskeluaridfk" => $r_NewPD['objectstatuskeluarfk'],
                        ]
                    );


                $ruangasal = DB::select(DB::raw("select * from daftarpasienruangantr
                         where registrasipasienfk= '$r_NewPD[norec_pd]' and ruanganidfk=$r_NewPD[objectruanganasalfk]
                         and koders=$kdProfile ;"));

                $set = $this->settingDataFixed('kdStatusBedKosong', $kdProfile);
                foreach ($ruangasal as $Hit) {
                    TempatTidur::where('id', $Hit->nobedidfk)->update(['statusbedidfk' => $set]);
                }
            }

            if ($request['strukorder']['norecorder'] != '') {
                $updateSO = TransaksiOrder::where('norec', $request['strukorder']['norecorder'])
                    ->update(
                        [
                            'statusorder' => 1,
                            'tglvalidasi' => $tglAyeuna
                        ]
                    );
            }

            $countNoAntrian = DaftarPasienRuangan::where('ruanganidfk', $r_NewPD['objectruanganlastfk'])
                ->where('koders', $kdProfile)
                ->where('aktif', true)
                ->where('tglregistrasi', '>=', $r_NewPD['tglregistrasidate'] . ' 00:00')
                ->where('tglregistrasi', '<=', $r_NewPD['tglregistrasidate'] . ' 23:59')
                ->max('noantrian');
            $noAntrian = $countNoAntrian + 1;
            $pd = RegistrasiPasien::where('norec', $r_NewPD['norec_pd'])->first();
            $dataAPD = new DaftarPasienRuangan;
            $dataAPD->norec = $dataAPD->generateNewId();
            $dataAPD->koders = $kdProfile;
            $dataAPD->aktif = true;
            $dataAPD->ruanganidfk = $r_NewAPD['objectruanganlastfk'];
            $dataAPD->asalrujukanidfk = $r_NewAPD['objectasalrujukanfk'];
            $dataAPD->kamaridfk = $r_NewAPD['objectkamarfk'];
            $dataAPD->kasuspenyakitidfk = null;
            $dataAPD->kelasidfk = $r_NewAPD['objectkelasfk'];
            $dataAPD->noantrian = $noAntrian;
            $dataAPD->nobedidfk = $r_NewAPD['nobed'];
            $dataAPD->registrasipasienfk = $r_NewPD['norec_pd'];
            $dataAPD->statusantrian = 0;
            $dataAPD->statuskunjungan = $r_NewPD['statuspasien'];
            $dataAPD->statuspasienidfk = 1;
            $dataAPD->tglregistrasi = $pd->tglregistrasi;
            $dataAPD->ruanganasalidfk = $r_NewPD['objectruanganasalfk'];
            $dataAPD->tglkeluar = null;
            $dataAPD->tglmasuk = $r_NewAPD['tglmasuk'];
            $dataAPD->israwatgabung = $r_NewAPD['israwatgabung'];
            $dataAPD->save();

            $set = $this->settingDataFixed('kdStatusBedIsi', $kdProfile);
            TempatTidur::where('id', $r_NewAPD['nobed'])->update(['statusbedidfk' => $set]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            DB::commit();
            $ReportTrans = 'Save Pindah Ruangan';
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'dataAPD' => $dataAPD,
                'as' => 'Xoxo',
            );
        } else {
            $ReportTrans = "Save Pindah Ruangan Gagal";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $ReportTrans,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function saveAkomodasiOtomatis(Request $request)
    {
        //        ini_set('max_execution_time', 3000); //6 minutes
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        DB::beginTransaction();
        try {
            // $kdDetailJenisProdukAkomodasi = (int)$this->settingDataFixed('kdDetailJenisProdukAkomodasi', $kdProfile);
            $kdranap = $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile);
            $data2 = DB::select(
                DB::raw("select apd.tglmasuk,apd.tglkeluar,apd.norec as norec_apd,pd.tglregistrasi,pd.jenispelayananidfk
                    from registrasipasientr as pd
                    INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                    INNER JOIN ruanganmt as ru_pd on ru_pd.id=apd.ruanganidfk
                    where ru_pd.instalasiidfk in ($kdranap) and apd.koders = $idProfile
                    and apd.aktif =true
                    and pd.noregistrasi='$request[noregistrasi]' and pd.tglpulang is null order by apd.tglmasuk;")

            );
            foreach ($data2 as $dateAPD) {
                $jenispelayanan =$dateAPD->jenispelayananidfk;
                $tglMasuk = $dateAPD->tglmasuk;
                if (is_null($dateAPD->tglkeluar) == true) {
                    $tglKeluar = date('Y-m-d 23:59:59');
                } else {
                    $tglKeluar = $dateAPD->tglkeluar;
                }
                $arrDate = $this->dateRange($tglMasuk, $tglKeluar);
                foreach ($arrDate as $itemDate) {
                    $tglAwal = $itemDate . ' 00:00';
                    $tglAkhir = $itemDate . ' 23:59';

                    $data = DB::select(
                        DB::raw("select pp.tglpelayanan,apd.kelasidfk as objectkelasfk,
                    apd.ruanganidfk as objectruanganfk,apd.israwatgabung
                    from registrasipasientr as pd
                    INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                    INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                    INNER JOIN pelayananmt as pr on pr.id=pp.produkidfk
                    INNER JOIN ruanganmt as ru_pd on ru_pd.id=pd.ruanganlastidfk
                    where pd.tglpulang is null  and ru_pd.instalasiidfk in ($kdranap)
                    and pd.koders=$kdProfile
                    and apd.aktif=true
                    and pp.tglpelayanan between :tglAwal and :tglAkhir
                    and pp.status='akomodasi'
                    and pd.noregistrasi=:noregistrasi ;"),
                        array(
                            'tglAwal' => $tglAwal,
                            'tglAkhir' => $tglAkhir,
                            'noregistrasi' => $request['noregistrasi'],
                        )
                    );

                    if (count($data) == 0) {
                        $dataDong = DB::select(
                            DB::raw("select apd.kelasidfk as objectkelasfk,
                            apd.ruanganidfk as objectruanganfk,apd.israwatgabung ,apd.norec as norec_apd,pd.tglregistrasi
                            from registrasipasientr as pd
                            INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                            INNER JOIN ruanganmt as ru_pd on ru_pd.id=apd.ruanganidfk
                            where pd.tglpulang is null and  ru_pd.instalasiidfk in ($kdranap)
                            and pd.koders=$kdProfile
                            and apd.aktif=true
                            and pd.noregistrasi=:noregistrasi and apd.norec=:norec_apd;"),
                            array(
                                'noregistrasi' => $request['noregistrasi'],
                                'norec_apd' => $dateAPD->norec_apd,
                            )
                        );
                        $rawatGabung = ' and map.israwatgabung='.$dataDong[0]->israwatgabung;
                        if($dataDong[0]->israwatgabung== false){
                            $rawatGabung = ' and map.israwatgabung is null';
                        }
                        $ruanganfk =$dataDong[0]->objectruanganfk;
                        $kelasid =$dataDong[0]->objectkelasfk;
                     
                  
                        $sirahMacan = DB::select(DB::raw("select hett.*,map.ruanganidfk as objectruanganfk,
                            map.produkidfk as objectprodukfk,hett.kelasidfk as objectkelasfk
                            from mapruangantoakomodasitr as map
                            INNER JOIN tarifpelayananmt as hett on hett.produkidfk=map.produkidfk
                            INNER JOIN suratkeputusanmt as sk on sk.id=hett.suratkeputusanidfk
                            where map.ruanganidfk=$ruanganfk
                            $rawatGabung
                            and map.koders=$kdProfile
                            and hett.kelasidfk=$kelasid 
                            and map.kelasidfk = $kelasid
                            and hett.jenispelayananidfk=$jenispelayanan
                            and hett.aktif=true
                            and sk.aktif=true;
                            ")
                        );
                        
                        $diskon = 0;
                        $tglAwalDiskon = $itemDate . ' 23:59';
                        $start = new Carbon($dateAPD->tglregistrasi);
                        $end = new Carbon($tglAwalDiskon);
                        $tglRegis = date('Y-m-d', strtotime($dateAPD->tglregistrasi));
                        $selisihjam = $start->diff($end)->format('%H');
                        if ($tglRegis == $itemDate) {
                            if ((int)$selisihjam <= 6 && count($sirahMacan) > 0) {
                                $diskon = ((float)$sirahMacan[0]->hargasatuan * 50) / 100;
                            }
                        }


                        $PelPasien = new TransaksiPasien();
                        $PelPasien->norec = $PelPasien->generateNewId();
                        $PelPasien->koders = $kdProfile;
                        $PelPasien->aktif = true;
                        $PelPasien->daftarpasienruanganfk = $dateAPD->norec_apd;
                        $PelPasien->tglregistrasi = $dataDong[0]->tglregistrasi;
                        $PelPasien->hargadiscount = $diskon; //0;
                        $PelPasien->hargajual = $sirahMacan[0]->hargasatuan;
                        $PelPasien->hargasatuan = $sirahMacan[0]->hargasatuan;
                        $PelPasien->jumlah = 1;
                        $PelPasien->kelasidfk = $dataDong[0]->objectkelasfk;
                        $PelPasien->kdkelompoktransaksi = 1;
                        $PelPasien->piutangpenjamin = 0;
                        $PelPasien->piutangrumahsakit = 0;
                        $PelPasien->produkidfk = $sirahMacan[0]->objectprodukfk;
                        $PelPasien->stock = 1;
                        $PelPasien->tglpelayanan = $tglAwal;
                        $PelPasien->harganetto = $sirahMacan[0]->harganetto1;
                        $PelPasien->status = 'akomodasi';
                        $PelPasien->save();
                        $PPnorec = $PelPasien->norec;


                        $buntutMacan = DB::select(DB::raw("select hett.*,map.ruanganidfk as objectruanganfk,
                            map.produkidfk as objectprodukfk,hett.kelasidfk as objectkelasfk,
                            hett.komponenhargaidfk as objectkomponenhargafk
                            from mapruangantoakomodasitr as map
                            INNER JOIN tarifpelayanandmt as hett on hett.produkidfk=map.produkidfk
                            INNER JOIN suratkeputusanmt as sk on sk.id=hett.suratkeputusanidfk
                            where map.ruanganidfk=$ruanganfk
                            $rawatGabung
                            and map.koders=$kdProfile
                            and hett.kelasidfk=$kelasid 
                            and hett.jenispelayananidfk=$jenispelayanan
                            and hett.aktif=true
                            and sk.aktif=true;
                            ")
                        );
                        

                        foreach ($buntutMacan as $itemKomponen) {
                            $PelPasienDetail = new TransaksiPasienDetail();
                            $PelPasienDetail->norec = $PelPasienDetail->generateNewId();
                            $PelPasienDetail->koders = $kdProfile;
                            $PelPasienDetail->aktif = true;
                            $PelPasienDetail->daftarpasienruanganfk = $dateAPD->norec_apd;
                            $PelPasienDetail->aturanpakai = '-';
                            $PelPasienDetail->hargadiscount = $diskon;
                            $PelPasienDetail->hargajual = $itemKomponen->hargasatuan;
                            $PelPasienDetail->hargasatuan = $itemKomponen->hargasatuan;
                            $PelPasienDetail->jumlah = 1;
                            $PelPasienDetail->keteranganlain = 'akomodasi';
                            $PelPasienDetail->keteranganpakai2 = '-';
                            $PelPasienDetail->komponenhargaidfk = $itemKomponen->objectkomponenhargafk;
                            $PelPasienDetail->transaksipasienfk = $PPnorec;
                            $PelPasienDetail->piutangpenjamin = 0;
                            $PelPasienDetail->piutangrumahsakit = 0;
                            $PelPasienDetail->produkidfk = $itemKomponen->objectprodukfk;
                            $PelPasienDetail->stock = 1;
                            $PelPasienDetail->tglpelayanan = $tglAwal;
                            $PelPasienDetail->harganetto = $itemKomponen->harganetto1;
                            $PelPasienDetail->save();

                            $diskon = 0;
                        }
                    }
                }
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        $ReportTrans = "Akomodasi Kamar Otomatis";

        if ($transStatus == 'true') {
            $ReportTrans = $ReportTrans . "";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'slvR',
            );
        } else {
            $ReportTrans = $ReportTrans . " Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 201,
                "data" => $data2,
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getPasienMasihDirawat(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $filter = $request->all();
        $noreg = '';
        if (isset($filter['noReg']) && $filter['noReg'] != "" && $filter['noReg'] != "undefined") {
            $noreg = " AND pd.noregistrasi ilike '%" . $filter['noReg'] . "%'";
        }

        $norm = '';
        if (isset($filter['noRm']) && $filter['noRm'] != "" && $filter['noRm'] != "undefined") {
            $norm = " AND ps.norm ilike '%" . $filter['noRm'] . "%'";
        }
        $namaPasien = '';
        if (isset($filter['namaPasien']) && $filter['namaPasien'] != "" && $filter['namaPasien'] != "undefined") {
            $namaPasien = " AND ps.namapasien ilike '%" . $filter['namaPasien'] . "%'";
        }
        $ruId = '';
        if (isset($filter['ruanganId']) && $filter['ruanganId'] != "" && $filter['ruanganId'] != "undefined") {
            $ruId = ' AND ru.id = ' . $filter['ruanganId'];
        }
        $jmlRow = '';
        if (isset($filter['jmlRow']) && $filter['jmlRow'] != "" && $filter['jmlRow'] != "undefined") {
            $jmlRow = ' limit ' . $filter['jmlRow'];
        }
        $data = \Illuminate\Support\Facades\DB::select(DB::raw("
            SELECT * FROM (
            SELECT pd.tglregistrasi,apd.norec AS norec_apd,apd.ruanganidfk,ps.id,ps.norm,
                 pd.noregistrasi,ru.namaruangan,ps.namapasien,kp.kelompokpasien,kls.namakelas,
                 alm.alamatlengkap,jk.jeniskelamin,pg.namalengkap AS namadokter,pd.norec AS norec_pd,
                 pd.tglpulang,pd.statuspasien,ps.tgllahir,pd.ruanganlastidfk,pd.kelasidfk,
                 apd.kamaridfk,kmr.namakamar,apd.nobedidfk,tt.reportdisplay AS namabed,
                 pd.rekananidfk,rk.namarekanan,
                 CASE WHEN pd.kelompokpasienlastidfk = 2 THEN ps.nobpjs ELSE ps.noasuransilain END AS noasuransi,
                 pss.nosep
            FROM registrasipasientr AS pd
            INNER JOIN daftarpasienruangantr AS apd ON pd.norec = apd.registrasipasienfk AND apd.tglkeluar IS NULL and pd.ruanganlastidfk = apd.ruanganidfk
            INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk
            INNER JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk
            INNER JOIN kelasmt AS kls ON kls.id = pd.kelasidfk
            INNER JOIN jeniskelaminmt AS jk ON jk.id = ps.jeniskelaminidfk
            LEFT JOIN pegawaimt AS pg ON pg.id = apd.pegawaiidfk
            LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk
            LEFT JOIN instalasimt AS dept ON dept.id = ru.instalasiidfk
            LEFT JOIN kamarmt AS kmr ON kmr.id = apd.kamaridfk
            LEFT JOIN tempattidurmt AS tt ON tt.id = apd.nobedidfk
            LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id
            LEFT JOIN rekananmt AS rk ON rk.id = pd.rekananidfk
            LEFT JOIN pemakaianasuransitr AS pss ON pss.registrasipasienfk = pd.norec
			WHERE pd.tglpulang IS NULL
			AND pd.koders = $idProfile
			AND pd.aktif = true
			AND apd.aktif = true
			$noreg $norm $namaPasien $ruId
            $jmlRow
			) as x
			--where x.rownum=1
			order by x.tglregistrasi desc
		"));
        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function saveBatalPindahRuangan(Request $request)
    {
        DB::beginTransaction();
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();
        $kdProfile = $this->getDataKdProfile($request);
        try {
            $norecPd = $request['data']['norec_pd'];
            $apd = \DB::table('daftarpasienruangantr as apd')
                ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                ->select('ru.instalasiidfk', 'apd.norec', 'apd.ruanganidfk', 'apd.ruanganasalidfk', 'apd.kelasidfk', 'apd.nobedidfk', 'apd.kamaridfk')
                ->where('apd.registrasipasienfk', $norecPd)
                ->where('apd.koders', (int)$kdProfile)
                ->where('apd.aktif', true)
                ->get();

            foreach ($apd as $item) {

                $ruanganAsal = \DB::table('daftarpasienruangantr as apd')
                    ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                    ->select('ru.instalasiidfk', 'apd.norec as norec_apd', 'apd.ruanganidfk', 'apd.ruanganasalidfk', 'rpp.kelasidfk', 'apd.nobedidfk', 'apd.kamaridfk')
                    ->where('apd.registrasipasienfk', $norecPd)
                    ->where('apd.objectruanganfk', $item->objectruanganasalfk)
                    ->whereNotIn('ru.instalasiidfk', [27, 3])
                    ->where('apd.koders', (int)$kdProfile)
                    ->first();

                if ($item->instalasiidfk == 16 && $request['data']['ruanganlastidfk'] == $item->objectruanganfk) {
                    $pelayanan = PelayananPasien::where('daftarpasienruanganfk', $item->norec)
                        ->where('koders', (int)$kdProfile)
                        ->where('aktif', true)
                        ->first();
                    if (!empty($pelayanan)) {
                        $ReportTrans = 'Pasien sudah mendapatkan pelayanan, hapus pelayanan dulu !';
                        $pel = array('norec_pp' => $pelayanan->norec);
                        return $this->setStatusCode(400)->respond($pel, $ReportTrans);
                    } else {
                        $updatePD = RegistrasiPasien::where('norec', $norecPd)->where('koders', (int)$kdProfile)
                            ->update(
                                [
                                    'ruanganlastidfk' => $ruanganAsal->ruanganidfk,
                                    'kelasidfk' => $ruanganAsal->objectkelasfk,
                                ]
                            );

                        $delAPD = DaftarPasienRuangan::where('registrasipasienfk', $norecPd)
                            ->where('ruanganidfk', $request['data']['ruanganlastidfk'])
                            ->whereNull('tglkeluar')
                            ->where('koders', (int)$kdProfile)
                            ->delete();
                        $updateAPDs = DaftarPasienRuangan::where('registrasipasienfk', $norecPd)
                            ->where('ruanganidfk', $ruanganAsal->objectruanganfk)
                            ->wherenotnull('tglkeluar')
                            ->where('koders', (int)$kdProfile)
                            ->update(
                                ['tglkeluar' => null]
                            );

                        if (isset($request['data']['nobedidfk']) && $request['data']['nobedidfk'] != 'null') {
                            //update statusbed jadi Kosong
                            TempatTidur::where('id', $request['nobedidfk'])->where('koders', (int)$kdProfile)->update(['statusbedidfk' => 2]);
                        }
                        if ($ruanganAsal->kamaridfk != 'null') {
                            //update statusbed jadi Kosong
                            TempatTidur::where('id', $ruanganAsal->kamaridfk)->where('koders', (int)$kdProfile)->update(['statusbedidfk' => 1]);
                        }
                        break;
                    }
                }
            }

            $transStatus = 'true';
            $ReportTrans = "Batal Pindah Ruangan Sukses";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Tidak bisa Batal Pindah Ruangan";
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

    public function saveBatalRanap(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();
        $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile));
        $kdDepartemenRawatInap = [];
        foreach ($deptRanap as $itemRanap) {
            $kdDepartemenRawatInap[] = (int)$itemRanap;
        }
        $norecPd = $request['data']['norec_pd'];
        $apd = \DB::table('daftarpasienruangantr as apd')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->select('ru.instalasiidfk', 'apd.norec', 'apd.ruanganidfk', 'apd.ruanganasalidfk', 'apd.kelasidfk', 'apd.nobedidfk', 'apd.kamaridfk')
            ->where('apd.registrasipasienfk', $norecPd)
            ->whereIn('ru.instalasiidfk', $kdDepartemenRawatInap)
            ->where('apd.koders', (int)$kdProfile)
            ->where('apd.aktif', true)
            ->get();

        if (count($apd) > 1) {
            $ReportTrans = 'Tidak Bisa Batal Rawat Inap, pasien sudah mendapatkan lebih dari 1 ruangan Rawat Inap !';
            return $this->setStatusCode(400)->respond([], $ReportTrans);
        }

        foreach ($apd as $item) {
            $ruanganAsal = \DB::table('daftarpasienruangantr as apd')
                ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                ->leftjoin('diagnosapasienicdxtr as dg', 'dg.daftarpasienruanganfk', '=', 'apd.norec')
                ->select(
                    'ru.instalasiidfk',
                    'apd.norec as norec_apd',
                    'apd.ruanganidfk',
                    'apd.ruanganasalidfk',
                    'apd.kelasidfk',
                    'apd.nobedidfk',
                    'apd.kamaridfk',
                    'dg.norec AS norec_dg'
                )
                ->where('apd.registrasipasienfk', $norecPd)
                ->where('apd.objectruanganfk', $item->objectruanganasalfk)
                ->whereNotIn('ru.instalasiidfk', [27, 3])
                ->where('apd.koders', (int)$kdProfile)
                ->first();

            if ($item->instalasiidfk == 16) {
                $pelayanan = PelayananPasien::where('daftarpasienruanganfk', $item->norec)->first();
                if (!empty($pelayanan)) {
                    $ReportTrans = 'Pasien sudah mendapatkan pelayanan, hapus pelayanan dulu !';
                    $pel = array('norec_pp' => $pelayanan->norec);
                    return $this->setStatusCode(400)->respond($pel, $ReportTrans);
                }
                if ($item->norec_dg != null) {
                    $ReportTrans = 'Pasien sudah mendapatkan Diagnosis, hapus Diagnosis dulu !';
                    return $this->setStatusCode(400)->respond([], $ReportTrans);
                }
                DB::beginTransaction();
                try {
                    if (!empty($ruanganAsal)) {
                        $updatePD = RegistrasiPasien::where('norec', $norecPd)
                            ->update(
                                [
                                    'ruanganlastidfk' => $ruanganAsal->ruanganasalidfk,
                                    'tglpulang' => $request['data']['tglregistrasi'],
                                    'kelasidfk' => 6,
                                ]
                            );
                    } else {
                        $updatePD = RegistrasiPasien::where('norec', $norecPd)
                            ->update(
                                [
                                    'aktif' => false,
                                    'tglpulang' => $request['data']['tglregistrasi']
                                ]
                            );
                    }

                    $delAPD = DaftarPasienRuangan::where('norec', $item->norec)
                        ->delete();


                    if (isset($request['data']['nobedidfk']) && $request['data']['nobedidfk'] != 'null') {
                        TempatTidur::where('id', $request['data']['nobedidfk'])->update(['statusbedidfk' => 2]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                }
                DB::commit();
            }
        }

        $transStatus = 'true';
        $ReportTrans = "Batal Rawat Inap Sukses";

        if ($transStatus != 'false') {
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getKamarByKelasRuangan(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('kamarmt as kmr')
            ->join('ruanganmt as ru', 'ru.id', '=', 'kmr.ruanganidfk')
            ->join('kelasmt as kl', 'kl.id', '=', 'kmr.kelasidfk')
            ->JOIN('tempattidurmt as tt', function ($join) {
                $join->on('tt.kamaridfk', '=', 'kmr.id');
                $join->where('tt.statusbedidfk', '=', 2);
            })
            ->distinct()
            ->select(DB::raw("
                kmr.id,kmr.namakamar,kl.id AS id_kelas,kl.namakelas,ru.id AS id_ruangan,
			    ru.namaruangan,kmr.jumlakamarisi,kmr.qtybed
            "))
            ->where('kmr.ruanganidfk', $request['idRuangan'])
            ->where('kmr.kelasidfk', $request['idKelas'])
            ->where('kmr.aktif', true)
            ->where('kmr.koders', (int)$kdProfile)
            ->get();

        $result = array(
            'kamar' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getNoBedByKamar(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('tempattidurmt as tt')
            ->join('statusbedmt as sb', 'sb.id', '=', 'tt.statusbedidfk')
            ->join('kamarmt as km', 'km.id', '=', 'tt.kamaridfk')
            ->select('tt.id', 'sb.statusbed', 'tt.reportdisplay')
            ->where('tt.kamaridfk', $request['idKamar'])
            ->where('km.aktif', true)
            ->where('tt.aktif', true)
            ->where('tt.koders', (int)$kdProfile)
            ->get();

        $result = array(
            'bed' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function updateKamar(Request $request)
    {
        DB::beginTransaction();
        $kdProfile = $this->getDataKdProfile($request);
        try {
            $antrians = DaftarPasienRuangan::where('registrasipasienfk', $request['norec_pd'])
                ->where('ruanganidfk', $request['ruanganlastfk'])
                ->whereNull('tglkeluar')
                ->where('koders', (int)$kdProfile)
                ->first();

            DaftarPasienRuangan::where('norec', $antrians->norec)->where('koders', (int)$kdProfile)
                ->update(
                    [
                        'kamaridfk' => $request['objectkamarfk'],
                        'nobedidfk' => $request['nobed'],
                    ]
                );

            TempatTidur::where('id', $request['nobed'])->where('koders', (int)$kdProfile)->update(['statusbedidfk' => 1]);

            if (isset($request['nobedasal']) && $request['nobedasal'] != 'null' && $request['nobedasal'] != $request['nobed']) {
                TempatTidur::where('id', $request['nobedasal'])->where('koders', (int)$kdProfile)->update(['statusbedidfk' => 2]);
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Update Kamar Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            $ReportTrans = "Update Kamar Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getComboRI(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile));
        $kdDepartemenRawatInap = [];
        foreach ($deptRanap as $itemRanap) {
            $kdDepartemenRawatInap[] = (int)$itemRanap;
        }
        $ruanganInap = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk as objectdepartemenfk')
            ->where('ru.aktif', true)
            ->wherein('ru.instalasiidfk', $kdDepartemenRawatInap)
            ->where('ru.koders', $kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        $kelPasien = \DB::table('kelompokpasienmt as ru')
            ->select('ru.id', 'ru.kelompokpasien')
            ->where('ru.aktif', true)
            ->where('ru.koders', $kdProfile)
            ->orderBy('ru.kelompokpasien')
            ->get();

        $result = array(
            'ruanganinap' => $ruanganInap,
            'kelompokpasien' => $kelPasien,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getDaftarRegistrasiDokterRanap(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $filter = $request->all();
        $ruangId = '';
        if (isset($filter['ruangId']) && $filter['ruangId'] != "" && $filter['ruangId'] != "undefined") {
            $ruangId = ' AND ru.id = ' . $filter['ruangId'];
        }
        $ruangArr = '';
        if (isset($filter['ruanganArr']) && $filter['ruanganArr'] != "" && $filter['ruanganArr'] != "undefined") {
            $ruangArr = ' AND ru.id in (' . $filter['ruanganArr'] . ')';
        }
        $noreg = '';
        if (isset($filter['noreg']) && $filter['noreg'] != "" && $filter['noreg'] != "undefined") {
            $noreg = " AND pd.noregistrasi = '" . $filter['noreg'] . "'";
        }
        $norm = '';
        if (isset($filter['norm']) && $filter['norm'] != "" && $filter['norm'] != "undefined") {
            $norm = " AND ps.norm ilike '%" . $filter['norm'] . "%'";
            //            $data = $data->where('ps.nocm', 'ilike', '%' . $filter['norm'] . '%');
        }
        $nama = '';
        if (isset($filter['nama']) && $filter['nama'] != "" && $filter['nama'] != "undefined") {
            $nama = " AND ps.namapasien ilike '%" . $filter['nama'] . "%'";
            //            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['nama'] . '%');
        }

        $data = DB::select(
            DB::raw("select * from
                (select pd.tglregistrasi,  ps.id as nocmfk,  ps.norm,  pd.noregistrasi,  ps.namapasien,  ps.tgllahir,
                 jk.jeniskelamin,  apd.ruanganidfk, ru.namaruangan,  kls.id as idkelas,kls.namakelas,  kp.kelompokpasien,
                 apd.pegawaiidfk,  pg.namalengkap as namadokter,
                  age(current_date, to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'))as lamarawat,
                 pd.norec as norec_pd, apd.tglmasuk, apd.norec as norec_apd, row_number() over (partition by pd.noregistrasi order by apd.tglmasuk desc) as rownum
                 from daftarpasienruangantr as apd
                 inner join registrasipasientr as pd on pd.norec = apd.registrasipasienfk and pd.ruanganlastidfk = apd.ruanganidfk
                 inner join pasienmt as ps on ps.id = pd.normidfk
                 left join jeniskelaminmt as jk on jk.id = ps.jeniskelaminidfk
                 inner join kelasmt as kls on kls.id = pd.kelasidfk
                 inner join ruanganmt as ru on ru.id = apd.ruanganidfk
                 inner join instalasimt as dept on dept.id = ru.instalasiidfk
                 left join pegawaimt as pg on pg.id = pd.pegawaiidfk
                 left join kelompokpasienmt as kp on kp.id = pd.kelompokpasienlastidfk
                 where pd.aktif = true and pd.koders = $idProfile

                and pd.tglpulang is null
                $ruangId $noreg $norm $nama
                    $ruangArr

                 ) as x where x.rownum=1")
        );

        return $this->respond($data);
    }

    public function getDataComboBoxGizi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataJenisDiet = \DB::table('jenisdietmt as jd')
            ->select('jd.id', 'jd.jenisdiet')
            ->where('jd.aktif', true)
            ->get();

        $dataJenisWaktu = \DB::table('jeniswaktumt as jw')
            ->select('jw.id', 'jw.jeniswaktu')
            ->where('jw.aktif', true)
            ->get();

        $dataKategoryDiet = \DB::table('kategorydietmt as kd')
            ->select('kd.id', 'kd.kategorydiet')
            ->where('kd.aktif', true)
            ->get();
        $result = array(
            'jenisdiet' => $dataJenisDiet,
            'jeniswaktu' => $dataJenisWaktu,
            'kategorydiet' => $dataKategoryDiet,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveOrderGizi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $item = $request['strukorder'];
            if ($item['norec_so'] == '') {
                $noOrder = $this->generateCode(new TransaksiOrder, 'noorder', 11, 'G' . $this->getDateTime()->format('ym'), $kdProfile);
                $dataSO = new TransaksiOrder();
                $dataSO->norec = $dataSO->generateNewId();
                $dataSO->koders = $kdProfile;
                $dataSO->aktif = true;
                $dataSO->isdelivered = 1;
                $dataSO->noorder = $noOrder;
                $dataSO->noorderintern = $noOrder;
            } else {
                $dataSO = TransaksiOrder::where('norec', $item['norec_so'])->where('koders', $kdProfile)->first();
                $del = TransaksiOrderDetail::where('transaksiorderfk', $item['norec_so'])->where('koders', $kdProfile)->delete();
            }
            $dataSO->pegawaiorderidfk = $this->getCurrentUserID();
            $dataSO->qtyjenisproduk = 1;
            $dataSO->qtyproduk = $item['qtyproduk'];
            $dataSO->ruangantujuanidfk = $this->settingDataFixed('kdRuanganGizi', $kdProfile);
            $dataSO->keteranganorder = 'Order Gizi';
            $dataSO->kelompoktransaksiidfk = $this->settingDataFixed('kdJenisTransaksiGizi', $kdProfile);
            $dataSO->tglorder = date('Y-m-d H:i:s');
            $dataSO->tglpelayananawal = $item['tglmenu'];
            $dataSO->totalbeamaterai = 0;
            $dataSO->totalbiayakirim = 0;
            $dataSO->totalbiayatambahan = 0;
            $dataSO->totaldiscount = 0;
            $dataSO->totalhargasatuan = 0;
            $dataSO->totalharusdibayar = 0;
            $dataSO->totalpph = 0;
            $dataSO->totalppn = 0;
            $dataSO->save();
            $dataSOnorec = $dataSO->norec;

            foreach ($item['details'] as $itemDetails) {
                $dataOP = new TransaksiOrderDetail;
                $dataOP->norec = $dataOP->generateNewId();
                $dataOP->koders = $kdProfile;
                $dataOP->aktif = true;
                $dataOP->iscito = 0;
                $dataOP->arrjenisdiet = $item['jenisdietfk'];
                if (isset($item['jeniswaktufk'])) {
                    $dataOP->jeniswaktuidfk = $item['jeniswaktufk'];
                }
                if (isset($itemDetails['objectjenisdietfk'])) {
                    $dataOP->jenisdietidfk = $itemDetails['objectjenisdietfk'];
                }

                $dataOP->kategorydietidfk = $itemDetails['objectkategorydietfk'];
                $dataOP->keteranganlainnya = $itemDetails['keterangan'];
                $dataOP->keteranganlainnya_quo = 'Order Gizi';
                $dataOP->statusgizi = $item['jenisorder'];
                $dataOP->normidfk = $itemDetails['nocmfk'];
                $dataOP->registrasipasienfk = $itemDetails['norec_pd'];
                $dataOP->transaksiorderfk = $dataSOnorec;
                $dataOP->qtyproduk = 1;
                $dataOP->qtyprodukinuse = $itemDetails['cc'];
                $dataOP->jumlah = $itemDetails['volume'];
                $dataOP->kelasidfk = $itemDetails['idkelas'];
                $dataOP->qtyprodukretur = 0;
                $dataOP->ruanganidfk = $itemDetails['ruanganidfk'];
                $dataOP->ruangantujuanidfk = $this->settingDataFixed('kdRuanganGizi', $kdProfile);;
                $dataOP->tglpelayanan = $item['tglorder'];
                $dataOP->save();
            }
            //            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Simpan Order Pelayanan";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "strukorder" => $dataSO,

            );
        } else {
            $ReportTrans = "Simpan Order Pelayanan gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
                "e" => $e->getMessage() . ' ' . $e->getLine(),

            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDaftarOrderGiziDetail(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $data = DB::table('transaksiordertr as so')
            ->join('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'so.ruangantujuanidfk')
            ->select(
                'so.norec',
                'so.noorder',
                'so.qtyproduk',
                'so.ruangantujuanidfk',
                'ru.namaruangan as ruangantujuan',
                'so.keteranganorder',
                'so.tglorder',
                'so.tglpelayananawal',
                'so.pegawaiorderidfk',
                'pg.namalengkap'
            )
            ->where('so.kelompoktransaksiidfk', $this->settingDataFixed('kdJenisTransaksiGizi', $kdProfile))
            ->where('so.koders', $kdProfile)
            ->where('so.aktif', true);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('so.tglpelayananawal', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('so.tglpelayananawal', '<=', $tgl);
        }

        if (isset($request['pegId']) && $request['pegId'] != "" && $request['pegId'] != "undefined") {
            $data = $data->where('so.pegawaiorderidfk', '=', $request['pegId']);
        }

        if (isset($request['jenisDietId']) && $request['jenisDietId'] != "" && $request['jenisDietId'] != "undefined") {
            $arr = explode(',', $request['jenisDietId']);
            $arrIn = [];
            foreach ($arr as $a) {
                $arrIn[] = $a;
            }
            $data = $data->whereIn('op.jenisdietidfk', $arrIn);
        }
        if (isset($request['jenisWaktuId']) && $request['jenisWaktuId'] != "" && $request['jenisWaktuId'] != "undefined") {
            $data = $data->where('op.jeniswaktuidfk', '=', $request['jenisWaktuId']);
        }

        if (isset($request['noorder']) && $request['noorder'] != "" && $request['noorder'] != "undefined") {
            $data = $data->where('so.noorder', 'ilike', '%' . $request['noorder'] . '%');
        }

        if (isset($request['noreg']) && $request['noreg'] != "" && $request['noreg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'ilike', '%' . $request['noreg'] . '%');
        }
        if (isset($request['norm']) && $request['norm'] != "" && $request['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['norm'] . '%');
        }
        if (isset($request['pengorderId']) && $request['pengorderId'] != "" && $request['pengorderId'] != "undefined") {
            $data = $data->where('pg.id', '=', $request['pengorderId']);
        }

        if (isset($request['nama']) && $request['nama'] != "" && $request['nama'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['nama'] . '%');
        }
        if (isset($request['jmlRows']) && $request['jmlRows'] != "" && $request['jmlRows'] != "undefined") {
            $data = $data->take($request['jmlRows']);
        }
        $data = $data->orderBy('so.noorder');
        $data = $data->get();
        $datas = [];
        foreach ($data as $item) {
            $norec = $item->norec;
            $deptId = '';
            $ruangId = '';
            if (isset($request['deptId']) && $request['deptId'] != "" && $request['deptId'] != "undefined") {
                $deptId = ' and ru.objectdepartemenfk =' . $request['deptId'];
            }

            if (isset($request['ruangId']) && $request['ruangId'] != "" && $request['ruangId'] != "undefined") {
                $ruangId = ' and ru.id=' . $request['ruangId'];
            }
            $detail = DB::select(DB::raw("select op.transaksiorderfk, op.jenisdietidfk,
                --jd.jenisdiet,
                    op.jeniswaktuidfk,jw.jeniswaktu,op.arrjenisdiet,
                    op.kategorydietidfk,kt.kategorydiet,op.keteranganlainnya,
                    op.statusgizi as jenisorder,op.qtyprodukinuse as cc,op.jumlah as volume,op.kelasidfk,kls.namakelas,
                    ru.namaruangan as ruangorder,op.ruanganidfk,ps.norm,pd.noregistrasi,ps.namapasien,sk.nokirim,pd.norec as norec_pd,ps.id as nocmfk,op.norec as norec_op
                    from transaksiorderdetailtr as op
                    join jeniswaktumt as jw on jw.id=op.jeniswaktuidfk
                    left join kategorydietmt as kt on kt.id=op.kategorydietidfk
                    join registrasipasientr as pd on pd.norec=op.registrasipasienfk
                    join pasienmt as ps on ps.id=pd.normidfk
                    join kelasmt as kls on kls.id=op.kelasidfk
                    join ruanganmt as ru on ru.id=op.ruanganidfk
                    left join transaksikirimtr as sk on sk.norec=op.transaksikirimfk
                    where op.transaksiorderfk='$norec' AND op.koders = $kdProfile
                    $deptId
                    $ruangId
                    "));
            if (count($detail) > 0) {
                $datas[] = array(
                    'norec' => $item->norec,
                    'noorder' => $item->noorder,
                    'tglorder' => $item->tglorder,
                    'tglpelayananawal' => $item->tglpelayananawal,
                    'ruangantujuanidfk' => $item->ruangantujuanidfk,
                    'ruangantujuan' => $item->ruangantujuan,
                    'keteranganorder' => $item->keteranganorder,
                    'tglmenu' => $item->tglpelayananawal,
                    'pegawaiorderidfk' => $item->pegawaiorderidfk,
                    'pengorder' => $item->namalengkap,
                    'details' => $detail
                );
            }
        }

        $dataResult = array(
            'message' => 'Xoxo',
            'data' => $datas,

        );
        return $this->respond($dataResult);
    }

    public function hapusOrderGzi(Request $request)
    {

        DB::beginTransaction();
        try {
            TransaksiOrder::where('norec', $request['norec'])->update(
                [
                    'aktif' => false
                ]
            );
            $transStatus = 'true';
        } catch (Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Hapus Order ";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Hapus Order Pelayana";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function hapusOrderGziD(Request $request)
    {

        DB::beginTransaction();
        try {
            TransaksiOrderDetail::where('norec', $request['norec_op'])->delete();
            //            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Hapus Order ";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Hapus Order Pelayanan";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDaftarOrderGizi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $data = \DB::table('transaksiorderdetailtr as op')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'op.registrasipasienfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'op.ruanganidfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'op.normidfk')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->join('transaksiordertr as so', 'so.norec', '=', 'op.transaksiorderfk')
            ->join('ruanganmt as ru2', 'ru2.id', '=', 'so.ruangantujuanidfk')
            ->leftjoin('transaksikirimtr as sk', 'sk.norec', '=', 'op.transaksikirimfk')
            ->leftjoin('jeniswaktumt as jw', 'jw.id', '=', 'op.jeniswaktuidfk')
            ->join('kategorydietmt as kd', 'kd.id', '=', 'op.kategorydietidfk')
            ->leftjoin('kelasmt as kls', 'kls.id', '=', 'op.kelasidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
            ->select(
                'so.norec as norec_so',
                'op.norec as norec_op',
                'so.noorder',
                'so.tglorder',
                'so.tglpelayananawal as tglmenu',
                'pd.tglregistrasi',
                'ps.tgllahir',
                'ps.namapasien',
                'ps.norm',
                'ps.id as nocmfk',
                'ru.namaruangan as ruanganasal',
                'jk.jeniskelamin',
                'op.ruanganidfk',
                'jw.jeniswaktu',
                'op.transaksiorderfk',
                'op.kategorydietidfk',
                'kd.kategorydiet',
                'op.qtyproduk',
                'op.jeniswaktuidfk',
                'op.jenisdietidfk',
                'op.keteranganlainnya',
                'op.statusgizi as jenisorder',
                'op.qtyprodukinuse as cc',
                'op.jumlah as volume',
                'op.kelasidfk',
                'kls.namakelas',
                'pd.noregistrasi',
                'so.pegawaiorderidfk',
                'pg.namalengkap as pegawaiorder',
                'sk.nokirim',
                'sk.qtyproduk',
                'pd.norec as norec_pd',
                'so.ruangantujuanidfk',
                'op.transaksikirimfk',
                'sk.nokirim',
                'ru2.namaruangan as ruangantujuan',
                'jw.jeniswaktu',
                'op.jeniswaktuidfk',
                'op.arrjenisdiet',
                DB::raw("case when op.transaksikirimfk is not null then 'Sudah Dikirim'  else '-' end as statuskirim")
            );
        $data = $data->where('so.kelompoktransaksiidfk', $this->settingDataFixed('kdJenisTransaksiGizi', $kdProfile));
        $data = $data->where('so.koders', $kdProfile);
        $data = $data->where('so.aktif', true);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('so.tglpelayananawal', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('so.tglpelayananawal', '<=', $tgl);
        }

        if (isset($request['pegId']) && $request['pegId'] != "" && $request['pegId'] != "undefined") {
            $data = $data->where('so.objectpegawaiorderfk', '=', $request['pegId']);
        }
        if (isset($request['ruangId']) && $request['ruangId'] != "" && $request['ruangId'] != "undefined") {
            $arr = explode(',', $request['ruangId']);
            $arrIn = [];
            foreach ($arr as $a) {
                $arrIn[] = $a;
            }
            $data = $data->whereIn('ru.id', '=', $arrIn);
        }
        if (isset($request['jenisDietId']) && $request['jenisDietId'] != "" && $request['jenisDietId'] != "undefined") {
            $arr = explode(',', $request['jenisDietId']);
            $arrIn = [];
            foreach ($arr as $a) {
                $arrIn[] = $a;
            }
            $data = $data->whereIn('op.jenisdietidfk', $arrIn);
        }
        if (isset($request['jenisWaktuId']) && $request['jenisWaktuId'] != "" && $request['jenisWaktuId'] != "undefined") {
            $data = $data->where('op.jeniswaktuidfk', '=', $request['jenisWaktuId']);
        }

        if (isset($request['noorder']) && $request['noorder'] != "" && $request['noorder'] != "undefined") {
            $data = $data->where('so.noorder', 'ilike', '%' . $request['noorder'] . '%');
        }

        if (isset($request['noreg']) && $request['noreg'] != "" && $request['noreg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'ilike', '%' . $request['noreg'] . '%');
        }
        if (isset($request['norm']) && $request['norm'] != "" && $request['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['norm'] . '%');
        }
        if (isset($request['nama']) && $request['nama'] != "" && $request['nama'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['nama'] . '%');
        }
        if (isset($request['iskirim']) && $request['iskirim'] != "" && $request['iskirim'] != "undefined" && $request['iskirim'] == "true") {
            $data = $data->whereNotNull('op.transaksikirimfk');
        }


        $data = $data->orderBy('so.noorder');
        $data = $data->get();
        $datas = [];
        foreach ($data as $item) {
            $item->umur = $this->getAge($item->tgllahir, $item->tglregistrasi);
        }

        $dataResult = array(
            'message' => 'er',
            'data' => $data,

        );
        return $this->respond($dataResult);
    }

    public function saveKirimMenuGizi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['strukkirim']['norec_sk'] == '') {
                $noKirim = $this->generateCode(new TransaksiKirim, 'nokirim', 14, 'KM-' . $this->getDateTime()->format('ym'), $kdProfile);
                $dataSK = new TransaksiKirim();
                $dataSK->norec = $dataSK->generateNewId();
                $dataSK->koders = $kdProfile;
                $dataSK->aktif = true;
            } else {
                $dataSK = TransaksiKirim::where('norec', $request['strukkirim']['norec_sk'])->first();
                $noKirim = $dataSK->nokirim;
                $delKP = TransaksiKirimDetail::where('transaksikirimfk', $request['strukkirim']['norec_sk'])->delete();
            }
            $dataSK->tglkirim = date('Y-m-d H:i:s');
            $dataSK->nokirim = $noKirim;
            $dataSK->pegawaipengirimidfk = $this->getCurrentUserID();
            $dataSK->ruanganasalidfk = $this->settingDataFixed('kdRuanganGizi', $kdProfile);
            $dataSK->ruanganidfk = $request['strukkirim']['objectruanganasalfk'];
            $dataSK->jenispermintaanidfk = 1; /*transfer */
            $dataSK->kelompoktransaksiidfk = $this->settingDataFixed('kdJenisTransaksiGizi', $kdProfile);;
            $dataSK->keteranganlainnyakirim = $request['strukkirim']['keterangan'];
            $dataSK->qtydetailjenisproduk = 0;
            $dataSK->qtyproduk = $request['strukkirim']['qtyproduk'];
            $dataSK->totalbeamaterai = 0;
            $dataSK->totalbiayakirim = 0;
            $dataSK->totalbiayatambahan = 0;
            $dataSK->totaldiscount = 0;
            $dataSK->totalhargasatuan = 0;
            $dataSK->totalharusdibayar = 0;
            $dataSK->totalpph = 0;
            $dataSK->totalppn = 0;
            if (isset($request['strukkirim']['norec_pd'])) {
                $dataSK->noregistrasifk = $request['strukkirim']['norec_pd'];
            }
            $dataSK->save();

            $norecSK = $dataSK->norec;
            foreach ($request['strukkirim']['details'] as $items) {
                $dataKP = new TransaksiKirimDetail;
                $dataKP->norec = $dataKP->generateNewId();
                $dataKP->koders = $kdProfile;
                $dataKP->aktif = true;
                $dataKP->hargadiscount = 0;
                $dataKP->harganetto = 0;
                $dataKP->hargapph = 0;
                $dataKP->hargappn = 0;
                $dataKP->hargasatuan = 0;
                $dataKP->hargatambahan = 0;
                $dataKP->produkidfk = $items['produkfk'];
                $dataKP->produkkirimidfk = $items['produkfk'];
                $dataKP->transaksikirimfk = $norecSK;
                $dataKP->persendiscount = 0;
                $dataKP->qtyproduk = $items['qtyproduk'];
                $dataKP->qtyprodukkonfirmasi = $items['qtyproduk'];
                $dataKP->qtyprodukretur = 0;
                $dataKP->qtyprodukterima = $items['qtyproduk'];
                $dataKP->ruanganidfk = $request['strukkirim']['objectruanganasalfk'];
                $dataKP->ruanganpengirimidfk = $this->settingDataFixed('kdRuanganGizi', $kdProfile);
                $dataKP->satuan = '-';
                $dataKP->tglpelayanan = date('Y-m-d H:i:s');
                $dataKP->qtyprodukterimakonversi = $items['qtyproduk'];
                $dataKP->save();
            }
            if (isset($request['strukkirim']['datapasien'])) {
                foreach ($request['strukkirim']['datapasien'] as $itempasien) {
                    if (isset($itempasien['norec_op'])) {
                        $updateOp = TransaksiOrderDetail::where('registrasipasienfk', $itempasien['norec_pd'])
                            ->where('norec', $itempasien['norec_op'])
                            ->update(
                                [
                                    'transaksikirimfk' => $norecSK,
                                ]
                            );
                    }
                }
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Simpan Kirim Menu Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "nokirim" => $dataSK,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Simpan Kirim Menu Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "e" => $e->getLine() . ' ' . $e->getMessage(),
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function hapusOrderGiziItem(Request $request)
    {

        DB::beginTransaction();
        try {
            foreach ($request['orderpelayanan'] as $item) {
                TransaksiOrderDetail::where('norec', $item['norec_op'])->delete();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Hapus Order ";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Hapus Order Pelayanan";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getProdukMenu(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $siklusKe = $request['siklusKe'];
        $kelasId = $request['kelasId'];
        $jenisDietId = $request['jenisDietId'];
        $jenisWaktuId = $request['jenisWaktuId'];
        $kat = $request['kategoryDiet'];
        $norec_pd = $request['norec_pd'];
        $data = DB::select(DB::raw("
            select distinct sm.produkidfk as produkfk, prd.namaproduk,
                    --sm.sikluske,
                    sm.jeniswaktuidfk,jw.jeniswaktu,sm.jenisdietidfk,jd.jenisdiet ,
                    sm.kelasidfk,kls.namakelas
                    from siklusgizimt as sm
                    inner join pelayananmt as prd on prd.id=sm.produkidfk
                    inner join jeniswaktumt as jw on jw.id=sm.jeniswaktuidfk
                    inner join jenisdietmt as jd on jd.id=sm.jenisdietidfk
                    inner join kelasmt as kls on kls.id=sm.kelasidfk
                        INNER JOIN kategorydietmt kat on kat.id=sm.kategoryprodukidfk
                    where sm.koders = $kdProfile
                    and sm.aktif =true
                    --and sm.sikluske = '$siklusKe' 
                    and kat.id='$kat'
                    and sm.jeniswaktuidfk in ($jenisWaktuId) and --pagi siang sore
                    sm.jenisdietidfk in ( $jenisDietId) and
                    sm.kelasidfk = '$kelasId';
            
                    "));
        $results = [];
        if (count($data) > 0) {
            foreach ($data as $item) {
                $results[] = array(
                    'id' => $item->produkfk,
                    'produkfk' => $item->produkfk,
                    'namaproduk' => $item->namaproduk,
                    'objectjeniswaktufk' => $item->jeniswaktuidfk,
                    'jeniswaktu' => $item->jeniswaktu,
                    'objectjenisdietfk' => $item->jenisdietidfk,
                    'jenisdiet' => $item->jenisdiet,
                    'objectkelasfk' => $item->kelasidfk,
                    'namakelas' => $item->namakelas,
                    'qtyproduk' => 1,
                    'norec_op' => $request['norec_op'],
                );
            }
        }

        $result = array(
            'data' => $results,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getProdukGizi(Request $r)
    {
        $kdProfile = (int)$this->getDataKdProfile($r);
        $dataProduk = DB::table('pelayananmt as pr')
            ->JOIN('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->leftJOIN('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->select('pr.id', 'pr.namaproduk', 'ss.id as ssid', 'ss.satuanstandar')
            ->where('pr.aktif', true)
            ->where('pr.koders', $kdProfile)
            ->where('djp.jenisprodukidfk', (int)$this->settingDataFixed('kdProdukGizi', $kdProfile));
        if (isset($r['namaproduk']) && $r['namaproduk'] != '') {
            $dataProduk = $dataProduk->where('pr.namaproduk','ilike', '%' . $r['namaproduk'] . '%');
        }
        $dataProduk = $dataProduk->take(15);
        $dataProduk = $dataProduk->groupBy('pr.id', 'pr.namaproduk', 'ss.id', 'ss.satuanstandar');
        $dataProduk = $dataProduk->orderBy('pr.namaproduk');
        $dataProduk = $dataProduk->get();
        return $dataProduk;
    }
    public function getDaftarKirimGizi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $data = \DB::table('transaksikirimtr as sk')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'sk.ruanganasalidfk')
            ->leftjoin('ruanganmt as ru2', 'ru2.id', '=', 'sk.ruangantujuanidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'sk.pegawaipengirimidfk')
            ->select(
                'sk.norec as norec_sk',
                'sk.nokirim',
                'pg.namalengkap as pegawaikirim',
                'ru2.namaruangan as ruangantujuan',
                'sk.tglkirim',
                'ru.namaruangan as ruanganasal',
                'sk.ruangantujuanidfk',
                'sk.ruanganasalidfk',
                'sk.pegawaipengirimidfk',
                'sk.keteranganlainnyakirim',
                'sk.qtyproduk'
            )
            ->where('sk.aktif', true)
            ->where('sk.koders', $kdProfile)
            ->where('sk.kelompoktransaksiidfk', $this->settingDataFixed('kdJenisTransaksiGizi', $kdProfile));
        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('sk.tglkirim', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('sk.tglkirim', '<=', $tgl);
        }

        if (isset($request['pegId']) && $request['pegId'] != "" && $request['pegId'] != "undefined") {
            $data = $data->where('sk.objectpegawaipengirimfk', '=', $request['pegId']);
        }
        if (isset($request['ruangId']) && $request['ruangId'] != "" && $request['ruangId'] != "undefined") {
            $data = $data->where('sk.objectruangantujuanfk', '=', $request['ruangId']);
        }
        if (isset($request['noreg']) && $request['noreg'] != "" && $request['noreg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'ilike', '%' . $request['noreg'] . '%');
        }
        if (isset($request['norm']) && $request['norm'] != "" && $request['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['norm'] . '%');
        }
        if (isset($request['nama']) && $request['nama'] != "" && $request['nama'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['nama'] . '%');
        }

        if (isset($request['nokirim']) && $request['nokirim'] != "" && $request['nokirim'] != "undefined") {
            $data = $data->where('sk.nokirim', 'ilike', '%' . $request['nokirim'] . '%');
        }

        $data = $data->orderBy('sk.nokirim', 'desc');
        $data = $data->get();
        $result = [];
        foreach ($data as $item) {

            $details = DB::select(
                DB::raw("
                    select  pd.normidfk,pd.noregistrasi,ps.namapasien,ps.norm,
                    kd.kategorydiet,ru.namaruangan,jw.jeniswaktu,op.arrjenisdiet
                    from transaksiorderdetailtr as op 
                     JOIN registrasipasientr as pd on pd.norec=op.registrasipasienfk
                    left JOIN pasienmt as ps on ps.id=pd.normidfk
                    left JOIN kategorydietmt as kd on kd.id=op.kategorydietidfk
                    --left JOIN jenisdiet_m as jd on jd.id=op.objectjenisdietfk
                    left JOIN jeniswaktumt as jw on jw.id=op.jeniswaktuidfk
                     left JOIN ruanganmt as ru on ru.id=op.ruanganidfk
                    where op.transaksikirimfk=:norec_sk "),
                array(
                    'norec_sk' => $item->norec_sk,
                )
            );

            $result[] = array(
                'norec_sk' => $item->norec_sk,
                'nokirim' => $item->nokirim,
                'pegawaikirim' => $item->pegawaikirim,
                'ruangantujuan' => $item->ruangantujuan,
                'tglkirim' => $item->tglkirim,
                'ruanganasal' => $item->ruanganasal,
                'ruangantujuanidfk' => $item->ruangantujuanidfk,
                'ruanganasalidfk' =>  $item->ruanganasalidfk,
                'pegawaipengirimidfk' => $item->pegawaipengirimidfk,
                'keterangan' => $item->keteranganlainnyakirim,
                'qtyproduk' => $item->qtyproduk,
                'details' => $details,
            );
        }

        $dataResult = array(
            'message' =>  'Xoxo',
            'data' =>  $result,

        );
        return $this->respond($dataResult);
    }
    public function deleteKirimMenu(Request $request)
    {
        DB::beginTransaction();
        try {

            $hapus = TransaksiKirim::where('norec', $request['norec'])->update(
                ['aktif' => false]
            );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'er',
            );
        } else {
            $ReportTrans = "Gagal Batal Kirim";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
}
