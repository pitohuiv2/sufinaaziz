<?php


namespace App\Http\Controllers\Penunjang;


use App\Http\Controllers\ApiController;
use App\Master\NilaiNormal;
use App\Master\Pasien;
use App\Master\ProdukDetailLaboratorium;
use App\Master\ProdukDetailLaboratoriumNilaiNormal;
use App\Traits\InternalList;
use App\Traits\PelayananPasienTrait;
use App\Traits\SettingDataFixedTrait;
use App\Traits\Valet;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\HasilLaboratorium;
use App\Transaksi\HasilPemeriksaanLab;
use App\Transaksi\HasilRadiologi;
use App\Transaksi\RegistrasiPasien;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\TransaksiPasienDetail;
use App\Transaksi\PetugasPelaksana;
use App\Transaksi\TransaksiOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenunjangC  extends ApiController
{
    use Valet, PelayananPasienTrait, SettingDataFixedTrait, InternalList;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getDataComboPenunjang(Request $request)
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

        $kdRanap = (int) $this->settingDataFixed('kdDepartemenRanap', $kdProfile);
        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter', $kdProfile);
        $dataDokter = \DB::table('pegawaimt as ru')
            ->select('ru.id', 'ru.namalengkap')
            ->where('ru.aktif', true)
            ->where('ru.objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namalengkap')
            ->get();

        $dataGolDarah = \DB::table('golongandarahmt as ru')
            ->select('ru.id', 'ru.golongandarah')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.id')
            ->get();

        $result = array(
            'dataLogin' => $dataPegawai,
            'departemen' => $dataDepartemen,
            'kelompokpasien' => $dataKelompok,
            'datalogin' => $dataLogin,
            'kelas' => $dataKelas,
            'ruangannonianp' => $dataRuanganNonInap,
            'kdRanap' => $kdRanap,
            'dokter' => $dataDokter,
            'goldarah' => $dataGolDarah,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDataRuangTujuanPenunjang(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataRuangan = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile);

        if (isset($request['keluseridfk'])) {
            $dataRuangan = $dataRuangan->where('ru.kelompokuseridfk', '=', $request['keluseridfk']);
        }

        $dataRuangan = $dataRuangan->orderBy('ru.namaruangan');
        $dataRuangan = $dataRuangan->get();

        $result = array(
            'ruangan' => $dataRuangan,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDaftarOrderPenunjang(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $result = [];
        if ($request['KelUser'] == "laboratorium") {
            $data = \DB::table('transaksiordertr as so')
                ->join('registrasipasientr as pd', 'pd.norec', '=', 'so.registrasipasienfk')
                ->leftjoin('strukpelayanantr as sps', 'sps.norec', '=', 'pd.nostruklastidfk')
                ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                ->leftJoin('jeniskelaminmt as klm', 'klm.id', '=', 'ps.jeniskelaminidfk')
                ->join('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
                ->join('ruanganmt as ru2', 'ru2.id', '=', 'so.ruangantujuanidfk')
                ->join('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                ->join('instalasimt as dp2', 'dp2.id', '=', 'ru2.instalasiidfk')
                ->leftJoin('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
                ->join('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
                ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
                ->Join('daftarpasienruangantr as apd', function ($join) {
                    $join->on('apd.registrasipasienfk', '=', 'pd.norec');
                    $join->on('apd.ruanganidfk', '=', 'pd.ruanganlastidfk');
                })
                ->select(DB::raw("
                     so.norec AS norec_so,pd.norec AS norec_pd,so.noorder,pd.noregistrasi,pd.tglregistrasi,pd.tglpulang,
                     ps.norm,ps.namapasien,kps.kelompokpasien,klm.jeniskelamin,ps.tgllahir,kps.kelompokpasien,dp.namadepartemen,
                     pd.kelasidfk,kls.namakelas,kls.id AS klsid,so.ruangantujuanidfk,so.ruanganidfk,
                     pd.kelompokpasienlastidfk,ru.instalasiidfk,ru2.instalasiidfk AS iddeptujuan,
                     so.pegawaiorderidfk,pg.namalengkap AS pegawaiorder,ru.namaruangan,ru.id AS ruid,ru2.namaruangan AS ruangantujuan,
                     so.tglorder,sps.nostruk,apd.norec AS norec_apd,so.keteranganlainnya,so.cito,
                     CASE WHEN so.statusorder IS NULL THEN 'MASUK' ELSE 'SELESAI' END AS status
                "))
                ->where('so.koders', $kdProfile)
                ->where('ru2.instalasiidfk', $this->settingDataFixed('kdDepartemenLab', $kdProfile))
                ->where('so.aktif', true);
            if (isset($request['isNotVerif']) && $request['isNotVerif'] != "" && $request['isNotVerif'] != "undefined") {
                if ($request['isNotVerif'] == true) {
                    $data = $data->whereNull('so.statusorder');
                }
            }
        }
        if ($request['KelUser'] == "radiologi") {
            $data = \DB::table('transaksiordertr as so')
                ->join('registrasipasientr as pd', 'pd.norec', '=', 'so.registrasipasienfk')
                ->leftjoin('strukpelayanantr as sps', 'sps.norec', '=', 'pd.nostruklastidfk')
                ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                ->leftJoin('jeniskelaminmt as klm', 'klm.id', '=', 'ps.jeniskelaminidfk')
                ->join('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
                ->join('ruanganmt as ru2', 'ru2.id', '=', 'so.ruangantujuanidfk')
                ->join('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                ->join('instalasimt as dp2', 'dp2.id', '=', 'ru2.instalasiidfk')
                ->leftJoin('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
                ->join('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
                ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
                ->leftJoin('ris_order as pp', 'pp.order_no', '=', 'so.noorder')
                ->Join('daftarpasienruangantr as apd', function ($join) {
                    $join->on('apd.registrasipasienfk', '=', 'pd.norec');
                    $join->on('apd.ruanganidfk', '=', 'pd.ruanganlastidfk');
                })
                ->select(DB::raw("
                         so.norec AS norec_so,pd.norec AS norec_pd,so.noorder,pd.noregistrasi,pd.tglregistrasi,
                         pd.tglpulang,ps.norm,ps.namapasien,kps.kelompokpasien,klm.jeniskelamin,ps.tgllahir,
                         kps.kelompokpasien,dp.namadepartemen,pd.kelasidfk,kls.namakelas,kls.id AS klsid,
                         so.ruangantujuanidfk,so.ruanganidfk,pd.kelompokpasienlastidfk,ru.instalasiidfk,
                         ru2.instalasiidfk AS iddeptujuan,so.pegawaiorderidfk,pg.namalengkap AS pegawaiorder,
                         so.tglorder,sps.nostruk,apd.norec AS norec_apd,ru.namaruangan,ru2.namaruangan AS ruangantujuan,
                         so.keteranganlainnya,so.cito,CASE WHEN so.statusorder IS NULL THEN 'MASUK' ELSE 'SELESAI' END AS status
                    "))
                ->where('so.koders', $kdProfile)
                ->where('ru2.instalasiidfk', $this->settingDataFixed('kdDepartemenRad', $kdProfile))
                ->where('so.aktif', true);
            if (isset($request['isNotVerif']) && $request['isNotVerif'] != "" && $request['isNotVerif'] != "undefined") {
                if ($request['isNotVerif'] == true) {
                    $data = $data->whereNull('so.statusorder');
                }
            }
        }


        if ($request['KelUser'] == "bedah") {
            $data = \DB::table('transaksiordertr as so')
                ->join('registrasipasientr as pd', 'pd.norec', '=', 'so.registrasipasienfk')
                ->leftjoin('strukpelayanantr as sps', 'sps.norec', '=', 'pd.nostruklastidfk')
                ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                ->leftJoin('jeniskelaminmt as klm', 'klm.id', '=', 'ps.jeniskelaminidfk')
                ->join('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
                ->join('ruanganmt as ru2', 'ru2.id', '=', 'so.ruangantujuanidfk')
                ->join('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                ->join('instalasimt as dp2', 'dp2.id', '=', 'ru2.instalasiidfk')
                ->leftJoin('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
                ->join('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
                ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
                ->Join('daftarpasienruangantr as apd', function ($join) {
                    $join->on('apd.registrasipasienfk', '=', 'pd.norec');
                    $join->on('apd.ruanganidfk', '=', 'pd.ruanganlastidfk');
                })
                ->leftJoin('pegawaimt as pg2', 'pg2.id', '=', 'pd.pegawaiidfk')
                ->leftJoin('transaksipasientr as pp', 'pp.transaksiorderfk', '=', 'so.norec')
                ->select(DB::raw("
                    so.norec AS norec_so,pd.norec AS norec_pd,so.noorder,pd.noregistrasi,pd.tglregistrasi,pd.tglpulang,ps.norm,
                    ps.namapasien,kps.kelompokpasien,klm.jeniskelamin,ps.tgllahir,kps.kelompokpasien,dp.namadepartemen,pd.kelasidfk,
                    kls.namakelas,kls.id AS klsid,so.ruangantujuanidfk,so.ruanganidfk,pd.kelompokpasienlastidfk,ru.instalasiidfk,
                    ru2.instalasiidfk AS iddeptujuan,so.pegawaiorderidfk,pg.namalengkap AS pegawaiorder,so.tglorder,
                    ru.namaruangan,ru2.namaruangan AS ruangantujuan,so.tglpelayananakhir,pg2.namalengkap AS dpjp,
                    apd.norec AS norec_apd,so.cito,CASE WHEN pp.transaksiorderfk IS NULL THEN 'MASUK' ELSE 'Sudah Verifikasi' END AS status,
                  '' AS kddiagnosa
                "))
                ->where('so.koders', $kdProfile)
                ->where('ru2.instalasiidfk', $this->settingDataFixed('kdDepartemenOK', $kdProfile))
                ->where('so.aktif', true);

            if (isset($request['isNotVerif']) && $request['isNotVerif'] != "" && $request['isNotVerif'] != "undefined") {
                if ($request['isNotVerif'] == true) {
                    $data = $data->whereNull('so.statusorder');
                }
            }
        }
        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('so.tglorder', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $data = $data->where('so.tglorder', '<=', $request['tglAkhir']);
        }
        if (isset($request['tglAwalOperasi']) && $request['tglAwalOperasi'] != "" && $request['tglAwalOperasi'] != "undefined") {
            $data = $data->where('so.tglpelayananakhir', '>=', $request['tglAwalOperasi']);
        }
        if (isset($request['tglAkhirOperasi']) && $request['tglAkhirOperasi'] != "" && $request['tglAkhirOperasi'] != "undefined") {
            $tgl = $request['tglAkhirOperasi'];
            $data = $data->where('so.tglpelayananakhir', '<=', $tgl);
        }
        if (isset($request['deptId']) && $request['deptId'] != "" && $request['deptId'] != "undefined") {
            $data = $data->where('ru.instalasiidfk', '=', $request['deptId']);
        }
        if (isset($request['pegId']) && $request['pegId'] != "" && $request['pegId'] != "undefined") {
            $data = $data->where('so.pegawaiorderidfk', '=', $request['pegId']);
        }
        if (isset($request['ruangId']) && $request['ruangId'] != "" && $request['ruangId'] != "undefined") {
            $data = $data->where('so.ruanganidfk', '=', $request['ruangId']);
        }
        if (isset($request['ruangTujuanid']) && $request['ruangTujuanid'] != "" && $request['ruangTujuanid'] != "undefined") {
            $data = $data->where('so.ruangantujuanidfk', '=', $request['ruangTujuanid']);
        }
        if (isset($request['kelId']) && $request['kelId'] != "" && $request['kelId'] != "undefined") {
            $data = $data->where('pd.kelompokpasienlastidfk', '=', $request['kelId']);
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != "" && $request['noregistrasi'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'ilike', '%' . $request['noregistrasi'] . '%');
        }
        if (isset($request['nocm']) && $request['nocm'] != "" && $request['nocm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['nocm'] . '%');
        }
        if (isset($request['namapasien']) && $request['namapasien'] != "" && $request['namapasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['namapasien'] . '%');
        }
        if (isset($request['noOrders']) && $request['noOrders'] != "" && $request['noOrders'] != "undefined") {
            $data = $data->where('so.noorder', 'ilike', '%' . $request['noOrders'] . '%');
        }
        if (isset($request['jmlRow']) && $request['jmlRow'] != "" && $request['jmlRow'] != "undefined") {
            $data = $data->take($request['jmlRow']);
        }
        $data = $data->orderBy('so.noorder', 'desc');
        $data = $data->distinct();
        $data = $data->get();

        $norecaPd = '';
        foreach ($data as $ob) {
            $norecaPd = $norecaPd . ",'" . $ob->norec_apd . "'";
            $ob->kddiagnosa = '';
        }
        $norecaPd = substr($norecaPd, 1, strlen($norecaPd) - 1);
        $diagnosa = [];
        if ($norecaPd != '') {
            $diagnosa = DB::select(DB::raw("
               select dg.kddiagnosa,dg.namadiagnosa,
                      dg.kddiagnosa || '~' || dg.namadiagnosa AS diagnosa,
                      ddp.daftarpasienruanganfk as norec_apd
               from diagnosapasienicdxtr as ddp              
               left join icdxmt as dg on ddp.jenisdiagnosaidfk = dg.id
               where ddp.daftarpasienruanganfk in ($norecaPd) and ddp.jenisdiagnosaidfk = 1"));
            $i = 0;
            foreach ($data as $h) {
                foreach ($diagnosa as $d) {
                    if ($data[$i]->norec_apd == $d->norec_apd) {
                        $data[$i]->kddiagnosa = $d->kddiagnosa;
                    }
                }
                $i++;
            }
        }

        $dataResult = array(
            'message' =>  'Xoxo',
            'data' =>  $data,
        );
        return $this->respond($dataResult);
    }

    public function getDiagnosaRad(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->select(DB::raw("
                pd.noregistrasi,pd.tglregistrasi,apd.ruanganidfk,ru.namaruangan,apd.norec AS norec_apd,
			    ddp.icdxidfk,dg.kddiagnosa,dg.namadiagnosa,ddp.tglinputdiagnosa,ddp.jenisdiagnosaidfk,
			    jd.jenisdiagnosa,ddp.norec AS norec_detaildpasien,ddp.tglinputdiagnosa,
			    ddp.ketdiagnosis,dg.*,ddp.iskasusbaru,ddp.iskasuslama
            "))
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('diagnosapasienicdxtr as ddp', 'ddp.daftarpasienruanganfk', '=', 'apd.norec')
            ->join('icdxmt as dg', 'dg.id', '=', 'ddp.icdxidfk')
            ->join('jenisdiagnosamt as jd', 'jd.id', '=', 'ddp.jenisdiagnosaidfk')
            ->where('pd.aktif', true)
            ->where('pd.koders', $kdProfile);

        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $request['noReg']);
        };

        $data = $data->get();

        $result = array(
            'datas' => $data,
            'message' => 'giw',
        );
        return $this->respond($result);
    }

    public function getOrderPelayanan(Request $request)
    {
        $idkelas = $request['objectkelasfk'];
        $norec_so = $request['norec_so'];
        $kdProfile = (int) $this->getDataKdProfile($request);
        $so = TransaksiOrder::where('norec', $norec_so)->where('koders', $kdProfile)->first();
        $pasienDaftar = RegistrasiPasien::where('norec', $so->registrasipasienfk)
            ->where('koders', $kdProfile)
            ->first();
        $jp = (int)$pasienDaftar->jenispelayanan;
        $dataOrderPelayanan = \DB::select(
            DB::raw("
                select DISTINCT op.norec AS norec_op,pr. ID AS prid,pr.namaproduk,op.tglpelayanan,op.qtyproduk,
                     ru.namaruangan AS ruangantujuan,ru.instalasiidfk,op.strukorderfk,so.ruangantujuanidfk,
                     hnp.hargasatuan,kls.namakelas,dpm.namadepartemen,pps.norec AS norec_pp
                from transaksiorderdetailtr AS op
                left join transaksiordertr as so on so.norec = op.transaksiorderfk
                INNER JOIN pelayananmt as pr on pr.id = op.produkidfk
                INNER JOIN tarifpelayananmt as hnp on pr.id = hnp.produkidfk
                        and hnp.kelasidfk = '$idkelas' 
                        and hnp.aktif = true
                        and hnp.jenispelayananidfk = $jp                
                INNER join kelasmt as kls on kls.id = '$idkelas'  
                INNER JOIN suratkeputusanmt AS sk ON sk.ID = hnp.suratkeputusanidfk and sk.aktif=true              
                left join ruanganmt as ru on ru.id = so.ruangantujuanidfk
                left join instalasimt as dpm on dpm.id = ru.instalasiidfk
                left JOIN transaksipasientr as pps on pps.transaksiorderfk = so.norec
                      and op.produkidfk = pps.produkidfk
                where op.aktif = true AND op.koders = $kdProfile AND op.transaksiorderfk = :norec_so
                and kls.id=:objectkelasfk
                ORDER by op.tglpelayanan"),
            array(
                'norec_so' => $norec_so,
                'objectkelasfk' => $idkelas,
            )
        );

        $result = [];
        foreach ($dataOrderPelayanan as $item) {
            $dataz =  DB::select(DB::raw("
                select hnp.komponenhargaidfk,kh.komponentarif,hnp.hargasatuan,
                hnp.produkidfk,hnp.jenispelayananidfk
                from tarifpelayanandmt as hnp   
                inner join pelayananmt as prd on prd.id=hnp.produkidfk
                inner join komponentarifmt as kh on kh.id=hnp.komponenhargaidfk
                inner join kelasmt as kls on kls.id = hnp.kelasidfk
                inner join suratkeputusanmt as sk on sk.id= hnp.suratkeputusanidfk
                where hnp.koders = $kdProfile 
                and hnp.kelasidfk = '$idkelas'
                and hnp.aktif=true
                and hnp.jenispelayananidfk=$jp
                and prd.id='$item->prid'
                and sk.aktif=true"));

            $result[] = array(
                'norec_op' => $item->norec_op,
                'norec_pp' => $item->norec_pp,
                'prid' => $item->prid,
                'namaproduk' => $item->namaproduk,
                'qtyproduk' => $item->qtyproduk,
                'tglpelayanan' => $item->tglpelayanan,
                'idruangan' => $item->ruangantujuanidfk,
                'ruangantujuan' => $item->ruangantujuan,
                'hargasatuan' => $item->hargasatuan,
                'namakelas' => $item->namakelas,
                'objectdepartemenfk' => $item->instalasiidfk,
                'namadepartemen' => $item->namadepartemen,
                'details' => $dataz,
            );
        }

        $result = array(
            'message' =>  'Xoxo',
            'data' =>  $result,
        );
        return $this->respond($result);
    }

    public function savePelayananPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        \DB::beginTransaction();
        try {
            TransaksiOrder::where('norec', $request['norec_so'])
                ->where('koders', $idProfile)
                ->update(
                    [
                        'statusorder' => 1
                    ]
                );
            $dataTarifAdminCito = (float)$this->settingDataFixed('tarifadmincito', $idProfile);
            $pd = RegistrasiPasien::where('norec', $request['norec_pd'])->where('koders', $idProfile)->first();
            $apd = DaftarPasienRuangan::where('registrasipasienfk', $pd->norec)
                ->where('koders', $idProfile)
                ->where('ruanganidfk', $request['ruangantujuanidfk'])
                ->where('aktif', true)
                ->first();
            if (empty($apd)) {
                $dataAPD = new DaftarPasienRuangan;
                $dataAPD->norec = $dataAPD->generateNewId();
                $dataAPD->koders = $idProfile;
                $dataAPD->asalrujukanidfk = 1;
                $dataAPD->aktif = true;
                $dataAPD->kelasidfk = 6; 
                $dataAPD->noantrian = 1;
                $dataAPD->registrasipasienfk = $request['norec_pd'];
                $dataAPD->pegawaiidfk = $request['pegawaiorderidfk'];
                $dataAPD->ruanganidfk = $request['ruangantujuanidfk'];
                $dataAPD->ruanganasalidfk = $request['ruanganasalidfk'];
                $dataAPD->statusantrian = 0;
                $dataAPD->statuspasien = 1;
                $dataAPD->strukorderidfk = $request['norec_so'];
                $dataAPD->tglregistrasi = $pd->tglregistrasi; 
                $dataAPD->tglmasuk = date('Y-m-d H:i:s');
                $dataAPD->tglkeluar = date('Y-m-d H:i:s');
                $dataAPD->save();

                $dataAPDnorec = $dataAPD->norec;
                $dataAPDtglPel = $dataAPD->tglregistrasi;
            } else {
                $dataAPDnorec  = $apd->norec;
                $dataAPDtglPel = $apd->tglregistrasi;
            }

            $antrian = DaftarPasienRuangan::where('norec', $dataAPDnorec)
                ->update([
                    'ispelayananpasien' => true
                ]);
            $totJasa= 0;
            $totalJasa = 0;
            $penjumlahanJasa = 0;
            foreach ($request['bridging'] as $item) {
                $totalJasa = 0;
                $totJasa= 0;
                $penjumlahanJasa = 0;
                $PelPasien = new TransaksiPasien();
                $PelPasien->norec = $PelPasien->generateNewId();
                $PelPasien->koders = $idProfile;
                $PelPasien->aktif = true;
                $PelPasien->daftarpasienruanganfk =  $dataAPDnorec;
                $PelPasien->tglregistrasi = $dataAPDtglPel;
                $PelPasien->hargadiscount = 0;
                $PelPasien->hargajual =  $item['hargasatuan'];
                $PelPasien->hargasatuan =  $item['hargasatuan'];
                $PelPasien->jumlah =  $item['qtyproduk'];
                $PelPasien->kelasidfk =  $request['objectkelasfk'];
                $PelPasien->kdkelompoktransaksi =  1;
                $PelPasien->piutangpenjamin =  0;
                $PelPasien->piutangrumahsakit = 0;
                $PelPasien->produkidfk =  $item['produkid'];
                $PelPasien->stock =  1;
                $PelPasien->transaksiorderfk =  $request['norec_so'];
                $PelPasien->tglpelayanan = date('Y-m-d H:i:s');
                $PelPasien->harganetto =  $item['hargasatuan'];
                $PelPasien->iscito = $request['iscito'];
                $PelPasien->save();
                $PPnorec = $PelPasien->norec;


                $PelPasienPetugas = new PetugasPelaksana();
                $PelPasienPetugas->norec = $PelPasienPetugas->generateNewId();
                $PelPasienPetugas->koders = $idProfile;
                $PelPasienPetugas->aktif = true;
                $PelPasienPetugas->nomasukidfk = $dataAPDnorec;
                $PelPasienPetugas->pegawaiidfk = $request['iddokterverif']; 
                $PelPasienPetugas->jenispetugaspeidfk = 4; 
                $PelPasienPetugas->transaksipasienfk = $PPnorec;
                $PelPasienPetugas->save();
                $PPPnorec = $PelPasienPetugas->norec;


                foreach ($item['komponenharga'] as $itemKomponen) {

                    $PelPasienDetail = new TransaksiPasienDetail();
                    $PelPasienDetail->norec = $PelPasienDetail->generateNewId();
                    $PelPasienDetail->koders = $idProfile;
                    $PelPasienDetail->aktif = true;
                    $PelPasienDetail->daftarpasienruanganfk = $dataAPDnorec;
                    $PelPasienDetail->aturanpakai = '-';
                    $PelPasienDetail->hargadiscount = 0;
                    $PelPasienDetail->hargajual = $itemKomponen['hargasatuan'];
                    $PelPasienDetail->hargasatuan = $itemKomponen['hargasatuan'];
                    $PelPasienDetail->jumlah = 1;
                    $PelPasienDetail->keteranganlain = '-';
                    $PelPasienDetail->keteranganpakai2 = '-';
                    $PelPasienDetail->komponenhargaidfk = $itemKomponen['komponenhargaidfk'];
                    $PelPasienDetail->transaksipasienfk = $PPnorec;
                    $PelPasienDetail->piutangpenjamin = 0;
                    $PelPasienDetail->piutangrumahsakit = 0;
                    $PelPasienDetail->produkidfk =  $item['produkid'];
                    $PelPasienDetail->stock = 1;
                    $PelPasienDetail->strukorderidfk =  $request['norec_so'];
                    $PelPasienDetail->tglpelayanan = $dataAPDtglPel;
                    $PelPasienDetail->harganetto = $itemKomponen['hargasatuan'];
                    if ($request['iscito'] == true) {
                        $penjumlahanJasa = $itemKomponen['hargasatuan']  * $dataTarifAdminCito;
                        $PelPasienDetail->jasa = $penjumlahanJasa;
                        $totalJasa = $totalJasa + $penjumlahanJasa;
                    }
                    $PelPasienDetail->save();
                    $PPDnorec = $PelPasienDetail->norec;
                    $transStatus = 'true';
                }

                if ($request['iscito'] == true) {
                    $dataaa = TransaksiPasienDetail::where('transaksipasienfk', $PPnorec)->get();
                    foreach ($dataaa as $itemss) {
                        $totJasa = $totJasa + $itemss->jasa;
                    }
                   TransaksiPasien::where('norec', $PPnorec)
                    ->update([
                        'jasa' => $totalJasa
                    ]);
                }
            }

            
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'dataPP' => $PelPasien,
                'dataPPD' => $PelPasienDetail,
                'as' => 'Xoxo',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message'  => $ReportTrans,
                'e' => $e->getMessage(). ' '. $e->getLine(),
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDetailVerifLabRad(Request $r)
    {
        $kdProfile = (int) $this->getDataKdProfile($r);
        $data = \DB::select(DB::raw("select 
            pp.tglpelayanan,pp.jumlah,pp.hargasatuan,(pp.jumlah*pp.hargasatuan) as total,
            prd.namaproduk
            from transaksipasientr as pp
            join pelayananmt as prd on prd.id = pp.produkidfk
            where pp.transaksiorderfk = '$r[norec_so]'
            and pp.koders=$kdProfile and pp.aktif = true"));
        $res = array(
            "data" => $data,
            "tb" => 'Xoxo'
        );
        return $this->respond($res);
    }

    public function getDetailPasienPenunjang(Request $r)
    {
        $kdProfile = (int) $this->getDataKdProfile($r);
        $data = \DB::select(DB::raw("
            select apd.registrasipasienfk AS norec_pd,apd.norec AS norec_apd
            from transaksipasientr as pp
            join daftarpasienruangantr as apd on apd.norec = pp.daftarpasienruanganfk
            where pp.transaksiorderfk = '$r[norec_so]'
            and pp.koders=$kdProfile and pp.aktif = true 
            and apd.aktif = true LIMIT 1"));
        $res = array(
            "data" => $data,
            "tb" => 'Xoxo'
        );
        return $this->respond($res);
    }

    public function getDaftarPasienPenunjang(Request $request)
    {
        $dataLogin = $request->all();
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('daftarpasienruangantr as apd')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->Join('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->JOIN('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->JOIN('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->leftJoin('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftJoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->leftJoin('kelasmt as kl', 'kl.id', '=', 'pd.kelasidfk')
            ->leftJoin('strukpelayanantr as sp', 'sp.norec', '=', 'pd.nostruklastidfk')
            ->leftJoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftJoin('golongandarahmt as gol', 'gol.id', '=', 'ps.golongandarahidfk')
            ->leftjoin('transaksiordertr as so', 'so.norec', '=', 'apd.strukorderidfk')
            ->leftJoin('ruanganmt as ru1', 'ru1.id', '=', 'so.ruanganidfk')
            ->select(DB::raw("
                apd.norec AS norec_apd,ru.id AS ruid,ru.namaruangan,pd.noregistrasi,
			    ps.norm,ps.namapasien,jk.jeniskelamin,kp.kelompokpasien,rk.namarekanan,
			    kl.namakelas,kl.id AS klid,pd.tglregistrasi,pd.tglpulang,ps.tgllahir,
			    apd.norec,pd.norec AS norec_pd,sp.tglstruk,pd.nostruklastidfk,
			    alm.alamatlengkap,gol.golongandarah,apd.tglmasuk,ru1.namaruangan AS ruanganasal,
			    '' AS expertise
            "))
            ->where('apd.koders', $kdProfile)
            ->where('apd.aktif', true)
            ->where('ru.kelompokuseridfk', $request['KelUserid'])
            ->orderBy('apd.tglmasuk', 'desc');

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('apd.tglmasuk', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $data = $data->where('apd.tglmasuk', '<=', $request['tglAkhir']);
        }
        if (isset($request['deptId']) && $request['deptId'] != "" && $request['deptId'] != "undefined") {
            $data = $data->where('dept.id', '=', $request['deptId']);
        }
        if (isset($request['ruangId']) && $request['ruangId'] != "" && $request['ruangId'] != "undefined") {
            $data = $data->where('ru.id', '=', $request['ruangId']);
        }
        if (isset($request['kelId']) && $request['kelId'] != "" && $request['kelId'] != "undefined") {
            $data = $data->where('kp.id', '=', $request['kelId']);
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != "" && $request['noregistrasi'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'ilike', '%' . $request['noregistrasi'] . '%');
        }
        if (isset($request['nocm']) && $request['nocm'] != "" && $request['nocm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['nocm'] . '%');
        }
        if (isset($request['namapasien']) && $request['namapasien'] != "" && $request['namapasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['namapasien'] . '%');
        }
        if (isset($request['jmlRow']) && $request['jmlRow'] != "" && $request['jmlRow'] != "undefined") {
            $data = $data->take($request['jmlRow']);
        }
        $data = $data->get();
        $apdnorec = [];
        foreach ($data as $key => $v) {
            $norecapd = $v->norec_apd;
            $apdnorec[] = $v->norec_apd;
        }
        $hasilRad = DB::table('transaksipasientr as pp')
            ->join('hasilradiologitr as hh', 'pp.norec', '=', 'hh.transaksipasienfk')
            ->distinct()
            ->select('hh.tanggal', 'pp.daftarpasienruanganfk as norec_apd')
            ->whereIn('pp.daftarpasienruanganfk', $apdnorec)
            ->orderBy('hh.tanggal', 'desc')
            ->get();
        $hasilLab = DB::table('transaksipasientr as pp')
            ->join('hasillaboratoriumkliniktr as hh', 'pp.norec', '=', 'hh.transaksipasienfk')
            ->distinct()
            ->select('hh.tglhasil as tanggal', 'pp.daftarpasienruanganfk as norec_apd')
            ->whereIn('pp.daftarpasienruanganfk', $apdnorec)
            ->orderBy('hh.tglhasil', 'desc')
            ->get();
        $hasilLabPA = DB::table('transaksipasientr as pp')
            ->join('hasillaboratoriumpatologitr as hh', 'pp.norec', '=', 'hh.transaksipasienfk')
            ->distinct()
            ->select('hh.tanggal', 'pp.daftarpasienruanganfk as norec_apd')
            ->whereIn('pp.daftarpasienruanganfk', $apdnorec)
            ->orderBy('hh.tanggal', 'desc')
            ->get();


        $result = array(
            "data" => $data,
            "tb" => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function updateGolonganDarah(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        try {

            $dataApr = Pasien::where('norm', $request['norm'])
                ->where('koders', $idProfile)
                ->update([
                    'golongandarahidfk' => $request['golongandarahidfk'],
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            \DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getRincianPelayanan(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $pelayanan = [];
        $result = [];

        if ($request['KelUser'] == "laboratorium") {
            $pelayanan = \DB::table('transaksipasientr as pp')
                ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'pp.daftarpasienruanganfk')
                ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                ->JOIN('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                ->leftJOIN('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
                ->JOIN('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
                ->JOIN('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                ->JOIN('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                ->leftJOIN('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
                ->leftjoin('strukbuktipenerimaantr as sbm', 'sp.nosbmlastidfk', '=', 'sbm.norec')
                ->leftJOIN('transaksiordertr as so', 'so.norec', '=', 'pp.transaksiorderfk')
                ->leftjoin('pmimt as pmi', 'pmi.id', '=', 'pp.pmiidfk')
                ->leftJOIN(
                    'order_lab as lis',
                    'lis.no_lab',
                    '=',
                    DB::raw("so.noorder AND 
                        lis.kode_test =cast (pr.id as VARCHAR)   ")
                )
                ->select(DB::raw("
                     ps.norm,ps.namapasien,jk.jeniskelamin,pp.tglpelayanan,pp.produkidfk,pr.namaproduk,
                     pp.jumlah,pp.hargasatuan,pp.hargadiscount,sp.nostruk,pd.noregistrasi,ru.namaruangan,
                     dp.namadepartemen,ps.id AS psid,apd.norec AS norec_apd,sp.norec AS norec_sp,
                     pp.norec AS norec_pp,ru.instalasiidfk,so.noorder,lis.no_lab AS idbridging,
                     so.keteranganlainnya,apd.ruanganidfk,pp.iscito,pp.jasa,ps.jeniskelaminidfk,
                     ps.tgllahir,sbm.nosbm,pmi.pmi,so.keteranganlainnya,
                     CASE WHEN lis.no_lab IS NOT NULL THEN 'Sudah Dikirim' ELSE '-' END AS statusbridging,
                     '' AS hr_norec,NULL AS nourutrad
                "))
                ->where('pp.aktif', true)
                ->where('pp.koders', $idProfile)
                ->where('ru.instalasiidfk', $this->settingDataFixed('kdDepartemenLab', $idProfile))
                ->orderBy('pp.tglpelayanan');
        }
        if ($request['KelUser'] == "radiologi") {
            $pelayanan = \DB::table('transaksipasientr as pp')
                ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'pp.daftarpasienruanganfk')
                ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                ->JOIN('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                ->leftJOIN('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
                ->JOIN('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
                ->JOIN('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                ->JOIN('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                ->leftJOIN('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
                ->leftjoin('strukbuktipenerimaantr as sbm', 'sp.nosbmlastidfk', '=', 'sbm.norec')
                ->leftJOIN('transaksiordertr as so', 'so.norec', '=', 'pp.transaksiorderfk')
                ->leftjoin('pmimt as pmi', 'pmi.id', '=', 'pp.pmiidfk')
                ->leftJOIN(
                    'ris_order as ris',
                    'ris.order_no',
                    '=',
                    \DB::raw('so.noorder AND ris.order_code=cast(pp.produkidfk as text)')
                )
                ->leftJOIN(
                    'hasilradiologitr AS hr',
                    'hr.transaksipasienfk',
                    '=',
                    DB::raw("pp.norec AND hr.aktif = true ")
                )
                ->select(DB::raw("
                     ps.norm,ps.namapasien,jk.jeniskelamin,pp.tglpelayanan,pp.produkidfk,pr.namaproduk,
                     pp.jumlah,pp.hargasatuan,pp.hargadiscount,sp.nostruk,pd.noregistrasi,ru.namaruangan,
                     dp.namadepartemen,ps.id AS psid,apd.norec AS norec_apd,sp.norec AS norec_sp,
                     pp.norec AS norec_pp,ru.instalasiidfk,so.noorder,ris.order_key AS idbridging,
                     apd.ruanganidfk,pp.iscito,pp.jasa,so.keteranganlainnya,ps.jeniskelaminidfk,
                     ps.tgllahir,sbm.nosbm,pmi.pmi,ris.order_cnt AS nourutrad,
                     CASE WHEN ris.order_key IS NOT NULL THEN 'Sudah Dikirim' ELSE '-' END AS statusbridging,
		             hr.norec AS hr_norec
                "))
                ->where('pp.aktif', true)
                ->where('pp.koders', $idProfile)
                ->where('ru.instalasiidfk', $this->settingDataFixed('kdDepartemenRad', $idProfile))
                ->groupBy(DB::raw("
                     ps.norm,ps.namapasien,jk.jeniskelamin,pp.tglpelayanan,pp.produkidfk,pr.namaproduk,
                     pp.jumlah,pp.hargasatuan,pp.hargadiscount,sp.nostruk,pd.noregistrasi,ru.namaruangan,
                     dp.namadepartemen,ps.id,apd.norec,sp.norec,pp.norec,ru.instalasiidfk,so.noorder,
                     ris.order_key,apd.ruanganidfk,pp.iscito,pp.jasa,hr.norec,sbm.nosbm,pmi.pmi,
                     so.keteranganlainnya
                "))
                ->orderBy('pp.tglpelayanan');
        }
        if ($request['KelUser'] == "bedah") {
            $pelayanan = \DB::table('transaksipasientr as pp')
                ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'pp.daftarpasienruanganfk')
                ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                ->JOIN('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                ->leftJOIN('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
                ->JOIN('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
                ->JOIN('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                ->JOIN('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                ->leftJOIN('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
                ->leftjoin('strukbuktipenerimaantr as sbm', 'sp.nosbmlastidfk', '=', 'sbm.norec')
                ->leftJOIN('transaksiordertr as so', 'so.norec', '=', 'pp.transaksiorderfk')
                ->leftjoin('pmimt as pmi', 'pmi.id', '=', 'pp.pmiidfk')
                ->select(DB::raw("
                     ps.norm,ps.namapasien,jk.jeniskelamin,pp.tglpelayanan,pp.produkidfk,pr.namaproduk,
                     pp.jumlah,pp.hargasatuan,pp.hargadiscount,sp.nostruk,pd.noregistrasi,ru.namaruangan,
                     dp.namadepartemen,ps.id AS psid,apd.norec AS norec_apd,sp.norec AS norec_sp,
                     pp.norec AS norec_pp,ru.instalasiidfk,so.noorder,null AS idbridging,
                     so.keteranganlainnya,apd.ruanganidfk,pp.iscito,pp.jasa,ps.jeniskelaminidfk,
                     ps.tgllahir,sbm.nosbm,pmi.pmi,so.keteranganlainnya,
                     '-' AS statusbridging,
                     '' AS hr_norec,NULL AS nourutrad
                "))
                ->where('pp.aktif', true)
                ->where('pp.koders', $idProfile)
                ->where('ru.instalasiidfk', $this->settingDataFixed('kdDepartemenOK', $idProfile))
                ->orderBy('pp.tglpelayanan');
        }

        if (isset($request['departemenfk']) && $request['departemenfk'] != "" && $request['departemenfk'] != "undefined") {
            $pelayanan = $pelayanan->where('ru.instalasiidfk', '=', $request['departemenfk']);
        }
        if (isset($request['nocm']) && $request['nocm'] != "" && $request['nocm'] != "undefined") {
            $pelayanan = $pelayanan->where('ps.norm', '=', $request['nocm']);
        }
        if (isset($request['norec_apd']) && $request['norec_apd'] != "" && $request['norec_apd'] != "undefined") {
            $pelayanan = $pelayanan->where('pp.daftarpasienruanganfk', '=', $request['norec_apd']);
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != "" && $request['noregistrasi'] != "undefined") {
            $pelayanan = $pelayanan->where('pd.noregistrasi', '=', $request['noregistrasi']);
        }
        $pelayanan = $pelayanan->get();
        if (count($pelayanan) > 0) {
            $pelayananpetugas = \DB::table('registrasipasientr as pd')
                ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
                ->join('petugaspelaksanatr as ptu', 'ptu.nomasukidfk', '=', 'apd.norec')
                ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'ptu.pegawaiidfk')
                ->select('ptu.transaksipasienfk', 'pg.namalengkap', 'pg.id')
                ->where('pd.koders', $idProfile)
                ->where('ptu.aktif', true)
                ->where('ptu.jenispetugaspeidfk', 4)
                ->where('pd.noregistrasi', $pelayanan[0]->noregistrasi)
                ->get();

            $result = [];
            foreach ($pelayanan as $item) {
                if (isset($request['noregistrasifk'])) {
                    $diskon = $item->hargadiscount;
                } else {
                    $diskon = 0;
                }
                $NamaDokter = '-';
                $DokterId = '';
                foreach ($pelayananpetugas as $hahaha) {
                    if ($hahaha->transaksipasienfk == $item->norec_pp) {
                        $NamaDokter = $hahaha->namalengkap;
                        $DokterId = $hahaha->id;
                    }
                }
                $total = (((float)$item->hargasatuan - (float)$diskon) * (float)$item->jumlah) + (float)$item->jasa;
                $result[] = array(
                    'norm' => $item->norm,
                    'namapasien' => $item->namapasien,
                    'jeniskelamin' => $item->jeniskelamin,
                    'tglpelayanan' => $item->tglpelayanan,
                    'produkfk' => $item->produkidfk,
                    'namaproduk' => $item->namaproduk,
                    'jumlah' => (float)$item->jumlah,
                    'hargasatuan' => (float)$item->hargasatuan,
                    'hargadiscount' => (float)$diskon,
                    'total' => (float)$total,
                    'nostruk' => $item->nostruk,
                    'noregistrasi' => $item->noregistrasi,
                    'ruangan' => $item->namaruangan,
                    'objectruanganfk' => $item->ruanganidfk,
                    'departemen' => $item->namadepartemen,
                    'objectdepartemenfk' => $item->instalasiidfk,
                    'norec_apd' => $item->norec_apd,
                    'norec_sp' => $item->norec_sp,
                    'norec_pp' => $item->norec_pp,
                    'nourutrad' => $item->nourutrad,
                    'idpatient' => ($item->nourutrad == null) ? '-' : $item->nocm . '-' . $item->nourutrad,
                    'dokter' => $NamaDokter,
                    'dokterid' => $DokterId,
                    'noorder' => $item->noorder,
                    'idbridging' => $item->idbridging,
                    'statusbridging' => $item->statusbridging,
                    'iscito' => $item->iscito,
                    'jasa' => (float)$item->jasa,
                    'objectjeniskelaminfk' => $item->jeniskelaminidfk,
                    'tgllahir' => $item->tgllahir,
                    'nosbm' => $item->nosbm,
                    'hr_norec' => $item->hr_norec,
                    'pmi' => $item->pmi,
                    'keteranganlainnya' => $item->keteranganlainnya
                );
            }
        }
        $dataTXoxo = array(
            'data' => $result,
            'message' => 'Xoxo'
        );
        return $this->respond($dataTXoxo);
    }

    public function getDataRuanganAntrian(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('daftarpasienruangantr as apd')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->select(DB::raw("
                apd.ruanganidfk AS id,ru.namaruangan,apd.norec AS norec_apd
            "))
            ->where('apd.aktif', true)
            ->where('pd.aktif', true)
            ->where('apd.koders', $idProfile)
            ->where('pd.noregistrasi', $request['noregistrasi'])
            ->orderBy('pd.ruanganlastidfk')
            ->get();

        $result = array(
            'ruangan' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function saveAntrianPasienPenunjang(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        \DB::beginTransaction();

        try {
            $countNoAntrian = DaftarPasienRuangan::where('ruanganidfk', $request['objectruanganasalfk'])
                ->where('aktif', true)
                ->where('koders', $idProfile)
                ->where('tglregistrasi', '>=', $request['tglregistrasidate'] . ' 00:00')
                ->where('tglregistrasi', '<=', $request['tglregistrasidate'] . ' 23:59')
                ->count('norec');
            $pd = RegistrasiPasien::where('norec', $request['norec_pd'])->first();
            $noAntrian = $countNoAntrian + 1;
            $dataAPD = new DaftarPasienRuangan;
            $dataAPD->norec = $dataAPD->generateNewId();
            $dataAPD->koders = $idProfile;
            $dataAPD->aktif = true;
            $dataAPD->asalrujukanidfk = $request['asalrujukanfk'];
            $dataAPD->kelasidfk = $request['objectkelasfk'];
            $dataAPD->noantrian = $noAntrian;
            $dataAPD->registrasipasienfk = $request['norec_pd'];
            $dataAPD->pegawaiidfk = $request['dokterfk'];
            $dataAPD->ruanganidfk = $request['objectruangantujuanfk'];
            $dataAPD->statusantrian = 0;
            $dataAPD->statuspasien = 1;
            $dataAPD->statuskunjungan = 'LAMA';
            $dataAPD->statuspenyakit = 'BARU';
            $dataAPD->ruanganasalidfk = $request['objectruanganasalfk'];;
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

    public function getHasilRadiologi(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('hasilradiologitr as ar')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'ar.pegawaiidfk')
            ->select(DB::raw("
                ar.*, pg.namalengkap
            "))
            ->where('ar.koders', $kdProfile)
            ->where('ar.aktif', true)
            ->where('ar.transaksipasienfk', $request['norec_pp'])
            ->get();
        return $this->respond($data);
    }

    public function saveHasilRadiologi(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        \DB::beginTransaction();
        try {

            if ($request['norec'] == "") {
                $dataSO = new HasilRadiologi();
                $dataSO->norec = $dataSO->generateNewId();
                $dataSO->koders = $idProfile;
                $dataSO->aktif = true;
            } else {
                $dataSO =  HasilRadiologi::where('norec', $request['norec'])->first();
            }
            $dataSO->tanggal = $request['tglinput'];
            $dataSO->pegawaiidfk = $request['dokterid'];
            if (isset($request['nofoto'])) {
                $dataSO->nofoto = $request['nofoto'];
            }
            $dataSO->keterangan = $request['keterangan'];
            $dataSO->transaksipasienfk = $request['pelayananpasienfk'];
            $dataSO->registrasipasienfk = $request['norec_pd'];
            $dataSO->save();

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
                "strukorder" => $dataSO,
                "tb" => 'Xoxo',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getHasilLabPA(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('hasillaboratoriumpatologitr as ar')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'ar.pegawaiidfk')
            ->leftjoin('pegawaimt as dokterpengirim', 'dokterpengirim.id', '=', 'ar.dokterpengirimidfk')
            ->select('ar.*', 'pg.namalengkap', 'dokterpengirim.namalengkap as namadokterpengirim')
            ->where('ar.koders', $kdProfile)
            ->where('ar.aktif', true)
            ->where('ar.transaksipasienfk', $request['norec_pp'])
            ->get();
        return $this->respond($data);
    }

    public function saveHasilLabPA(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        \DB::beginTransaction();
        try {
            if ($request['norec'] == "") {
                $dataSO = new HasilPemeriksaanLab();
                $dataSO->norec = $dataSO->generateNewId();
                $dataSO->koders = $idProfile;
                $dataSO->aktif = true;
            } else {
                $dataSO =  HasilPemeriksaanLab::where('norec', $request['norec'])->first();
            }
            if ($request['isDokterLuar'] == true) {
                $dataSO->dokterluar = $request['dokterpengirim2'];
                $dataSO->dokterpengirimidfk = NULL;
            } else {
                $dataSO->dokterluar = NULL;
                $dataSO->dokterpengirimidfk = $request['dokterpengirim1'];
            }
            $dataSO->nomorpa = $request['nomor'];
            $dataSO->tanggal = $request['tglinput'];
            $dataSO->pegawaiidfk = $request['dokterid'];
            $dataSO->jenis = $request['jenis'];
            $dataSO->transaksipasienfk = $request['pelayananpasienfk'];
            $dataSO->registrasipasienfk = $request['norec_pd'];
            $dataSO->diagnosaklinik = $request['diagnosaklinik'];
            $dataSO->keteranganklinik = $request['keteranganklinik'];
            $dataSO->diagnosapb = $request['diagnosapb'];
            $dataSO->keteranganpb = $request['keteranganpb'];
            $dataSO->topografi = $request['topografi'];
            $dataSO->morfologi = $request['morfologi'];
            $dataSO->makroskopik = $request['makroskopik'];
            $dataSO->mikroskopik = $request['mikroskopik'];
            $dataSO->kesimpulan = $request['kesimpulan'];
            $dataSO->anjuran = $request['anjuran'];
            $dataSO->jaringanasal = $request['jaringanasal'];
            $dataSO->save();

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
                "dataSave" => $dataSO,
                "tb" => 'Xoxo',
            );
        } else {
            $ReportTrans =            "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getHasilLabManual(Request $r)
    {
        $kdProfile = $this->getDataKdProfile($r);
        $umur = $r['umur'];
  
        $data = DB::select(DB::raw("SELECT pp.daftarpasienruanganfk as norec_apd,djp.detailjenisproduk,pp.produkidfk,prd.namaproduk ,
                pdl.detailpemeriksaan,
                hh.hasil,pdlm.rangemin as nilaimin,pdlm.rangemax as nilaimax,pdlm.refrange as nilaitext,
                ss.satuanstandar,pdl.id as iddetailproduk,
                hh.metode,pp.norec as norec_pp,
                hh.norec as norec_hasil
                FROM transaksipasientr as pp
                inner join pelayananmt as prd on prd.id = pp.produkidfk
                inner join detailjenisprodukmt as djp on djp.id = prd.detailjenisprodukidfk
                left JOIN produkdetaillaboratoriummt pdl on pdl.produkidfk = prd.id
                left join produkdetaillaboratoriumnilainormalmt as pdlm on pdlm.produkdetaillabidfk = pdl.id
                and pdlm.jeniskelaminidfk=$r[objectjeniskelaminfk]
                left join hasillaboratoriumtr as hh on hh.produkidfk = prd.id
                and pp.daftarpasienruanganfk=hh.daftarpasienruanganfk
                and pp.norec=hh.transaksipasienfk
                and hh.produkdetaillabidfk =pdl.id
                left join satuanstandarmt as ss on ss.id = pdl.satuanstandaridfk
                where pp.daftarpasienruanganfk='$r[norec_apd]'
                and pp.koders=$kdProfile
                order by prd.namaproduk asc"));
        $result = array(
            'data' => $data,
        );
        return $this->respond($result);
    }
    public function saveHasilLabManual(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        DB::beginTransaction();
        try {
            $cek = HasilLaboratorium::where('daftarpasienruanganfk', $request['hasil'][0]['noregistrasifk'])->first();
            if (!empty($cek)) {
                HasilLaboratorium::where('daftarpasienruanganfk', $request['hasil'][0]['noregistrasifk'])->delete();
            }
            foreach ($request['hasil'] as $key => $value) {
                $h = new HasilLaboratorium();
                $h->norec = $h->generateNewId();
                $h->koders = $idProfile;
                $h->aktif = true;
                $h->tglhasil = date('Y-m-d H:i:s');
                $h->pegawaiidfk = $this->getCurrentUserID();
                $h->hasil = $value['hasil'];
                $h->daftarpasienruanganfk = $value['noregistrasifk'];
                $h->transaksipasienfk = $value['pelayananpasienfk'];
                $h->produkidfk = $value['produkfk'];
                // $h->nilaimin = $value['nilaimin'];
                // $h->nilaimax = $value['nilaimax'];
                // $h->satuan = $value['satuan'];
                $h->metode = $value['metode'];
                $h->produkdetaillabidfk = $value['produkdetaillabfk'];

                $h->save();
                # code...
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';

        }

        if ($transStatus == 'true') {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'Xoxo',
            );

        } else {
            $transMessage = "Simpan  gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "e" => $e,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getNilaiNormalMas(Request $r)
    {
        $kdProfile = $this->getDataKdProfile($r);
        $data = DB::select(DB::raw("SELECT
       pdlm.refrange as nilaitext,pdlm.rangemax as nilaimax,pdlm.rangemin as nilaimin,
       pdlm.jeniskelaminidfk as jkid
        FROM  produkdetaillaboratoriummt pdl 
        left join produkdetaillaboratoriumnilainormalmt as pdlm on pdlm.produkdetaillabidfk = pdl.id
        where  pdl.koders=$kdProfile
        and pdl.produkidfk='$r[produkfk]' 
        and pdl.id='$r[mpid]'  "));
        return response()->json($data);
    }
    public function saveUpdateNilaiNormal(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        DB::beginTransaction();
        try {

            $pd = ProdukDetailLaboratoriumNilaiNormal::where('produkdetaillabidfk',$request['l']['id'])
            ->where('jeniskelaminidfk',1)
            ->first();
            $pd->rangemax = $request['l']['nilaimax'];
            $pd->rangemin = $request['l']['nilaimin'];
            $pd->refrange = $request['l']['nilaitext'];
            $pd->save();

            $pd2 = ProdukDetailLaboratoriumNilaiNormal::where('produkdetaillabidfk',$request['p']['id'])
            ->where('jeniskelaminidfk',2)
            ->first();
            $pd2->rangemax = $request['p']['nilaimax'];
            $pd2->rangemin = $request['p']['nilaimin'];
            $pd2->refrange = $request['p']['nilaitext'];
            $pd2->save();


            $transStatus = 'true';
            $transMessage = "Sukses Nilai Normal";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Gagal Simpan Nilai Normal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getHasilLabManualRev(Request $r)
    {
        $kdProfile = $this->getDataKdProfile($r);
        $umur = $r['umur'];
  
        $data = DB::select(DB::raw("
            SELECT
                pp.daftarpasienruanganfk AS norec_apd,
                djp.detailjenisproduk,
                pp.produkidfk,
                prd.namaproduk,  hh.hasil,
                pdlm.rangemin AS nilaimin,
                pdlm.rangemax AS nilaimax,
                pdlm.refrange AS nilaitext, ss.satuanstandar,
                pdlm.ID AS iddetailproduk, hh.metode,
                pp.norec AS norec_pp, hh.norec AS norec_hasil  
            FROM
                transaksipasientr AS pp
                INNER JOIN pelayananmt AS prd ON prd.ID = pp.produkidfk 
                INNER JOIN detailjenisprodukmt AS djp ON djp.ID = prd.detailjenisprodukidfk 
            
                LEFT JOIN produkdetaillaboratoriumnilainormalmt AS pdlm ON pdlm.produkdetaillabidfk = prd.ID  
                AND pdlm.jeniskelaminidfk = 1 
                LEFT JOIN hasillaboratoriumtr AS hh ON hh.produkidfk = prd.ID  
                AND pp.daftarpasienruanganfk = hh.daftarpasienruanganfk  
                AND pp.norec = hh.transaksipasienfk  
                AND hh.produkdetaillabidfk = pdlm.ID 
                LEFT JOIN satuanstandarmt AS ss ON ss.ID = pdlm.satuanstandaridfk  
            WHERE
                 pp.daftarpasienruanganfk='$r[norec_apd]'
                and pp.koders=$kdProfile
            ORDER BY
                prd.namaproduk ASC
        
        "));
        $satuan= DB::table('satuanstandarmt')
        ->where('aktif',true)
        ->where('instalasiidfk',$this->settingDataFixed('kdDepartemenLab', $kdProfile))
        ->select('id','satuanstandar')->get();
        $result = array(
            'data' => $data,
            'satuan' => $satuan,
        );
        return $this->respond($result);
    }
    public function getNilaiNormalRev(Request $r)
    {
        $kdProfile = $this->getDataKdProfile($r);
      
        $data = DB::select(DB::raw("SELECT
        pdlm.id,
        pdlm.refrange as nilaitext,pdlm.rangemax as nilaimax,pdlm.rangemin as nilaimin,
        pdlm.jeniskelaminidfk as jkid
        FROM  produkdetaillaboratoriumnilainormalmt AS pdlm
        where pdlm.koders=$kdProfile
        and pdlm.produkdetaillabidfk='$r[produkfk]' 
       "));
     
        
        return response()->json($data);
    }
    public function saveUpdateNilaiNormalRev(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        DB::beginTransaction();
        try {
            if($request['l']['id'] == ''){
                $pd = new ProdukDetailLaboratoriumNilaiNormal();
                $pd->id =ProdukDetailLaboratoriumNilaiNormal::max('id')+1;
                $pd->koders =$idProfile;
                $pd->aktif =true;
                $pd->jeniskelaminidfk =1;
            }else{
                $pd = ProdukDetailLaboratoriumNilaiNormal::where('id',$request['l']['id'])
                ->where('jeniskelaminidfk',1)
                ->first();
            }
            $pd->produkdetaillabidfk = $request['l']['produkidfk'];
            $pd->satuanstandaridfk= $request['l']['ssid'];
            $pd->rangemax = $request['l']['nilaimax'];
            $pd->rangemin = $request['l']['nilaimin'];
            $pd->refrange = $request['l']['nilaitext'];
            $pd->save();


            if($request['p']['id'] == ''){
                $pd2 = new ProdukDetailLaboratoriumNilaiNormal();
                $pd2->id =ProdukDetailLaboratoriumNilaiNormal::max('id')+1;
                $pd2->koders =$idProfile;
                $pd2->aktif =true;
                $pd2->jeniskelaminidfk =2;
            }else{
                $pd2 = ProdukDetailLaboratoriumNilaiNormal::where('id',$request['p']['id'])
                ->where('jeniskelaminidfk',2)
                ->first();
            }
        
            $pd2->produkdetaillabidfk = $request['p']['produkidfk'];
            $pd2->satuanstandaridfk= $request['p']['ssid'];
            $pd2->rangemax = $request['p']['nilaimax'];
            $pd2->rangemin = $request['p']['nilaimin'];
            $pd2->refrange = $request['p']['nilaitext'];
            $pd2->save();


            $transStatus = 'true';
            $transMessage = "Sukses Nilai Normal";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Gagal Simpan Nilai Normal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "error" => $e->getMessage(). ' '. $e->getLine(),
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function deleteorder(Request $request)
    {
        \DB::beginTransaction();

        try {
            TransaksiOrder::where('norec', $request['norec'])
                ->update(
                    [
                        'aktif' => 0
                    ]
                );
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
                // "data" => $dataAPD,
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
}