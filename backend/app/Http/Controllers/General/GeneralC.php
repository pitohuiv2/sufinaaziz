<?php


namespace App\Http\Controllers\General;


use App\Http\Controllers\ApiController;
use App\Master\MapPaketToProduk;
use App\Master\Paket;
use App\Master\Pegawai;
use App\Master\ProfileM;
use App\Master\SuratKeputusan;
use App\Traits\PelayananPasienTrait;
use App\Traits\Valet;
// use App\Transaksi\PasienDaftar;
// use App\Transaksi\StrukOrder;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\MapRuanganToAdministrasi;
use App\Transaksi\MapRuanganToAkomodasi;
use App\Transaksi\RegistrasiPasien;
use App\Web\LoginUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralC extends ApiController
{
    use Valet, PelayananPasienTrait;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getComboDokterPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter', $kdProfile);
        $req = $request->all();
        $data = \DB::table('pegawaimt')
            ->select('*')
            ->where('aktif', true)
            ->where('objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->where('koders', (int)$kdProfile)
            ->orderBy('namalengkap');

        if (isset($req['namalengkap']) &&
            $req['namalengkap'] != "" &&
            $req['namalengkap'] != "undefined") {
            $data = $data->where('namalengkap', 'ilike', '%' . $req['namalengkap'] . '%');
        };
        if (isset($req['idpegawai']) &&
            $req['idpegawai'] != "" &&
            $req['idpegawai'] != "undefined") {
            $data = $data->where('id', $req['idpegawai']);
        };
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $data = $data
                ->where('namalengkap', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        }

        $data = $data->take(10);
        $data = $data->get();

        return $this->respond($data);
    }

    public function getComboRekananPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $req = $request->all();
        $data = \DB::table('rekananmt')
            ->select('id','namarekanan')
            ->where('aktif', true)
            ->where('koders', (int)$kdProfile)
            ->orderBy('namarekanan');

        if (isset($req['namarekanan']) &&
            $req['namarekanan'] != "" &&
            $req['namarekanan'] != "undefined") {
            $data = $data->where('namarekanan', 'ilike', '%' . $req['namarekanan'] . '%');
        };
        if (isset($req['idrekanan']) &&
            $req['idrekanan'] != "" &&
            $req['idrekanan'] != "undefined") {
            $data = $data->where('id', $req['idrekanan']);
        };
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $data = $data
                ->where('namarekanan', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        }

        $data = $data->take(10);
        $data = $data->get();

        return $this->respond($data);
    }

    public function getDataClosing($noRegister, Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $data= \DB::table('registrasipasientr as pd')
            ->select(DB::raw("
                pd.isclosing,pd.tglclosing
            "))
            ->where('pd.koders', $kdProfile)
            ->where('pd.aktif',true)
            ->whereNotNull('pd.tglclosing')
            ->where('pd.noregistrasi',$noRegister)
            ->get();
        $result  = array(
            'data' =>$data,
        );

        return $this->respond($result);
    }

    public function getDataLayananPerAntrian($noRec, Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $data= \DB::table('daftarpasienruangantr as apd')
            ->JOIN('transaksipasientr AS pp','pp.daftarpasienruanganfk','=','apd.norec')
            ->select(DB::raw("
                pp.norec
            "))
            ->where('apd.koders', $kdProfile)
            ->where('apd.aktif',true)
            ->where('apd.norec',$noRec)
            ->get();
        $result  = array(
            'data' =>$data,
        );

        return $this->respond($result);
    }

    public function getPasienByNoregGeneral(Request $request){
        $norec_pd = $request['norec_pd'];
        $norec_apd = $request['norec_apd'];
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd','apd.registrasipasienfk','=','pd.norec')
            ->join('pasienmt as ps','ps.id','=','pd.normidfk')
            ->join('jeniskelaminmt as jk','jk.id','=','ps.jeniskelaminidfk')
            ->leftJoin('alamatmt as alm','alm.normidfk','=','ps.id')
            ->leftjoin('pendidikanmt as pdd','pdd.id','=','ps.pendidikanidfk')
            ->leftjoin('pekerjaanmt as pk','pk.id','=','ps.pekerjaanidfk')
            ->join('kelompokpasienmt as kps','kps.id','=','pd.kelompokpasienlastidfk')
            ->leftjoin('rekananmt as rk','rk.id','=','pd.rekananidfk')
            ->join('ruanganmt as ru','ru.id','=','apd.ruanganidfk')
            ->join('instalasimt as dpm','dpm.id','=','ru.instalasiidfk')
            ->leftjoin('pegawaimt as peg','peg.id','=','pd.pegawaiidfk')
            ->join('kelasmt as kls','kls.id','=','apd.kelasidfk')
            ->LEFTjoin('jenispelayananmt as jpl','jpl.id','=','pd.jenispelayananidfk')
            ->select('ps.norm as nocm','ps.id as nocmfk','ps.noidentitas','ps.namapasien','pd.noregistrasi', 'pd.tglregistrasi','jk.jeniskelamin',
                'ps.tgllahir','alm.alamatlengkap','pdd.pendidikan','pk.pekerjaan','ps.nohp as notelepon','ps.jeniskelaminidfk as objectjeniskelaminfk',
                'apd.ruanganidfk as objectruanganfk', 'ru.namaruangan', 'apd.norec as norec_apd','pd.norec as norec_pd',
                'kps.kelompokpasien','kls.namakelas','apd.kelasidfk as objectkelasfk','pd.kelompokpasienlastidfk as objectkelompokpasienlastfk','pd.rekananidfk as objectrekananfk',
                'rk.namarekanan','pd.ruanganlastidfk as objectruanganlastfk','jpl.jenispelayanan','pd.asalrujukanidfk as objectasalrujukanfk',
                'ru.kdinternal','jpl.id as objectjenispelayananfk','pd.pegawaiidfk as objectpegawaifk','pd.statuspasien',
                'ps.nobpjs','pd.statuspasien',
                DB::raw('case when pd.tglpulang <> pd.tglregistrasi  then \'true\' else \'false\' end as israwatinap')
            )
            ->where('pd.norec','=',$norec_pd)
            ->where('apd.norec','=',$norec_apd)
            ->where('pd.koders', (int)$kdProfile)
            ->where('pd.aktif',true)
            ->first();

        return $this->respond($data);
    }

    public function getIcd10(Request $request){
        $req = $request->all();
        $icdIX = \DB::table('icdxmt as dg')
            ->select(DB::raw("dg.id,dg.kddiagnosa,dg.namadiagnosa,
                                    dg.kddiagnosa || '-' || dg.namadiagnosa AS diagnosa"))
            ->where('dg.aktif', true)
            ->orderBy('dg.kddiagnosa');

        if(isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value']!="" &&
            $req['filter']['filters'][0]['value']!="undefined"){
            $icdIX = $icdIX
                ->where('dg.namadiagnosa','ilike','%'.$req['filter']['filters'][0]['value'].'%' )
                ->orWhere('dg.kddiagnosa','ilike',$req['filter']['filters'][0]['value'].'%' )  ;
        }
        if(isset($req['kodediagnosa']) &&
            $req['kodediagnosa']!="" &&
            $req['kodediagnosa']!="undefined"){
            $icdIX = $icdIX
                ->where('dg.namadiagnosa','ilike','%'.$req['kodediagnosa'].'%' )
                ->orWhere('dg.kddiagnosa','ilike',$req['kodediagnosa'].'%' )  ;
        }


        $icdIX=$icdIX->take(10);
        $icdIX=$icdIX->get();

        return $this->respond($icdIX);
    }

    public function getIcd9(Request $request){
        $req = $request->all();
        $icdIX = \DB::table('icdixmt as dg')
            ->select(DB::raw("dg.id,dg.kddiagnosa,dg.namadiagnosa,
                                    dg.kddiagnosa || '-' || dg.namadiagnosa AS diagnosa"))
            ->where('dg.aktif', true)
            ->orderBy('dg.kddiagnosa');

        if(isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value']!="" &&
            $req['filter']['filters'][0]['value']!="undefined"){
            $icdIX = $icdIX
                ->where('dg.namadiagnosa','ilike','%'.$req['filter']['filters'][0]['value'].'%' )
                ->orWhere('dg.kddiagnosa','ilike',$req['filter']['filters'][0]['value'].'%' )  ;
        }
        if(isset($req['kodediagnosa']) &&
            $req['kodediagnosa']!="" &&
            $req['kodediagnosa']!="undefined"){
            $icdIX = $icdIX
                ->where('dg.namadiagnosa','ilike','%'.$req['kodediagnosa'].'%' )
                ->orWhere('dg.kddiagnosa','ilike',$req['kodediagnosa'].'%' )  ;
        }

        $icdIX=$icdIX->take(10);
        $icdIX=$icdIX->get();

        return $this->respond($icdIX);
    }

    public function getPasienByRegistrasiRuanganGeneral(Request $request){
        $norec_pd = $request['norec_pd'];
        $norec_apd = $request['norec_apd'];
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd','apd.registrasipasienfk','=','pd.norec')
            ->join('pasienmt as ps','ps.id','=','pd.normidfk')
            ->join('jeniskelaminmt as jk','jk.id','=','ps.jeniskelaminidfk')
            ->leftJoin('alamatmt as alm','alm.normidfk','=','ps.id')
            ->leftjoin('pendidikanmt as pdd','pdd.id','=','ps.pendidikanidfk')
            ->leftjoin('pekerjaanmt as pk','pk.id','=','ps.pekerjaanidfk')
            ->join('kelompokpasienmt as kps','kps.id','=','pd.kelompokpasienlastidfk')
            ->leftjoin('rekananmt as rk','rk.id','=','pd.rekananidfk')
            ->join('ruanganmt as ru','ru.id','=','apd.ruanganidfk')
            ->join('instalasimt as dpm','dpm.id','=','ru.instalasiidfk')
            ->leftjoin('pegawaimt as peg','peg.id','=','pd.pegawaiidfk')
            ->join('kelasmt as kls','kls.id','=','apd.kelasidfk')
            ->LEFTjoin('jenispelayananmt as jpl','jpl.id','=','pd.jenispelayananidfk')
            ->select('ps.norm as nocm','ps.id as nocmfk','ps.noidentitas','ps.namapasien','pd.noregistrasi', 'pd.tglregistrasi','jk.jeniskelamin',
                'ps.tgllahir','alm.alamatlengkap','pdd.pendidikan','pk.pekerjaan','ps.nohp as notelepon','ps.jeniskelaminidfk as objectjeniskelaminfk',
                'apd.ruanganidfk as objectruanganfk', 'ru.namaruangan', 'apd.norec as norec_apd','pd.norec as norec_pd',
                'kps.kelompokpasien','kls.namakelas','apd.kelasidfk as objectkelasfk','pd.kelompokpasienlastidfk as objectkelompokpasienlastfk',
                'pd.rekananidfk as objectrekananfk','rk.namarekanan','pd.ruanganlastidfk as objectruanganlastfk','jpl.jenispelayanan',
                'pd.asalrujukanidfk as objectasalrujukanfk','ru.kdinternal','jpl.id as objectjenispelayananfk','pd.pegawaiidfk as objectpegawaifk',
                'pd.statuspasien','ps.nobpjs','pd.statuspasien','ru.instalasiidfk',
                DB::raw('case when pd.tglpulang <> pd.tglregistrasi  then \'true\' else \'false\' end as israwatinap')
            );
        if(isset($norec_pd)){
            $data=$data->where('pd.norec','=',$norec_pd);
        }
        if(isset($request['noregistrasi'])){
            $data=$data->where('pd.noregistrasi','=', $request['noregistrasi']);
        }

//      ->where('apd.norec','=',$norec_apd)
        $data = $data->where('pd.koders', (int)$kdProfile);
        $data = $data->where('pd.aktif',true);
        $data = $data->first();

        return $this->respond($data);
    }

    public function getPasienByStrukPelayananGeneral(Request $request){
        $norec_sp = $request['norec_sp'];
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('strukpelayanantr as pd')
            ->join('registrasipasientr as apd','apd.norec','=','pd.registrasipasienfk')
            ->join('pasienmt as ps','ps.id','=','apd.normidfk')
            ->join('jeniskelaminmt as jk','jk.id','=','ps.jeniskelaminidfk')
            ->leftJoin('alamatmt as alm','alm.normidfk','=','ps.id')
            ->leftjoin('pendidikanmt as pdd','pdd.id','=','ps.pendidikanidfk')
            ->leftjoin('pekerjaanmt as pk','pk.id','=','ps.pekerjaanidfk')
            ->join('kelompokpasienmt as kps','kps.id','=','apd.kelompokpasienlastidfk')
            ->leftjoin('rekananmt as rk','rk.id','=','apd.rekananidfk')
            ->join('ruanganmt as ru','ru.id','=','apd.ruanganlastidfk')
            ->join('instalasimt as dpm','dpm.id','=','ru.instalasiidfk')
            ->join('kelasmt as kls','kls.id','=','apd.kelasidfk')
            ->select(DB::raw("
                ps.norm AS nocm,ps.id AS nocmfk,ps.noidentitas,ps.namapasien,apd.noregistrasi,
			    apd.tglregistrasi,jk.jeniskelamin,ps.tgllahir,alm.alamatlengkap,ps.nohp AS notelepon,
			    ps.jeniskelaminidfk AS objectjeniskelaminfk,
			    ru.namaruangan,apd.norec AS norec_pd,pd.norec AS norec_sp,
			    kps.kelompokpasien,kls.namakelas,apd.kelasidfk AS objectkelasfk,
			    apd.kelompokpasienlastidfk AS objectkelompokpasienlastfk,apd.rekananidfk AS objectrekananfk,
			    rk.namarekanan,apd.ruanganlastidfk AS objectruanganlastfk,
			    apd.asalrujukanidfk AS objectasalrujukanfk,ru.kdinternal,
			    apd.pegawaiidfk AS objectpegawaifk,apd.statuspasien,ps.nobpjs,apd.statuspasien,
			    CASE WHEN apd.tglpulang <> apd.tglregistrasi THEN 'true' ELSE 'false' END AS israwatinap,
			    pd.nostruk
            "))
            ->where('pd.norec','=',$norec_sp)
//            ->where('apd.norec','=',$norec_apd)
            ->where('pd.koders', (int)$kdProfile)
            ->where('pd.aktif',true)
            ->first();

        return $this->respond($data);
    }

    public function getPasienNonLayanan(Request $request){
        $norec_sp = $request['norec_sp'];
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('strukpelayanantr as sp')
            ->select(DB::raw("
               sp.namapasien_klien as namapasien,sp.tglstruk as tglregistrasi,sp.nostruk
            "))
            ->where('sp.norec','=',$norec_sp)
//            ->where('apd.norec','=',$norec_apd)
            ->where('sp.koders', (int)$kdProfile)
            ->where('sp.aktif',true)
            ->first();

        return $this->respond($data);
    }
    public function getPaketTindakan(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('paketmt as mpr')
            ->select('mpr.id', 'mpr.namapaket','mpr.harga' )
            ->where('mpr.koders', $kdProfile)
            ->where('mpr.aktif',true)
            ->orderBy('mpr.namapaket', 'ASC')
            ->get();

        $data2 =[];
        foreach ($data as $item){
            $idPaket = $item->id;
            $details = DB::select(DB::raw("select
                    maps.id,prd.namaproduk, maps.produkidfk as objectprodukfk
                    from mappakettopelayananmt as maps
                    join pelayananmt as prd on prd.id =maps.produkidfk
                   -- join mapruangantoproduk_m as mpr on mpr.objectprodukfk = prd.id
                    where maps.koders = $kdProfile and maps.paketidfk='$idPaket'
                    and maps.aktif = true"));
            if(count($details) > 0){
                $data2 [] = array(
                    'id'=>   $item->id,
                    'namapaket'=>   $item->namapaket,
                    'jml' => count($details),
                    'hargapaket' => $item->harga == null ? 0 : (float) $item->harga,
                    'details' => $details
                ) ;
            }

        }

        return $this->respond($data2);
    }

    public function getJenisPelayananByNorecPd(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $norec_pd = $request['norec_pd'];
        $data = \DB::table('registrasipasientr as pd')
            ->select('pd.*')
            ->where('pd.norec',$norec_pd)
            ->where('pd.koders', (int)$kdProfile)
            ->first();
        return $this->respond($data);
    }

    public function getProdukDetail(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        //        namafield	nilaifield	keteranganfungsi
//        MetodeAmbilHargaNetto	0	KOSONG .: pengambilan hn1 atau hn2

//        MetodeHargaNetto	2	AVG
//        MetodeHargaNetto	3	Harga Tertinggi
//        MetodeHargaNetto	1	Harga Netto #

//        MetodeStokHargaNetto	2	LIFO
//        MetodeStokHargaNetto	3	FEFO #
//        MetodeStokHargaNetto	4	LEFO
//        MetodeStokHargaNetto	1	FIFO
//        MetodeStokHargaNetto	5	Summary

//        SistemHargaNetto	7	Harga Terakhir #
//        SistemHargaNetto	6	LEFO
//        SistemHargaNetto	2	LIFO
//        SistemHargaNetto	3	Harga Tertinggi
//        SistemHargaNetto	4	AVG
//        SistemHargaNetto	5	FEFO
//        SistemHargaNetto	1	FIFO

        //bucat : # setting yg dipakai
        //        select * from settingdatafixed_m where kelompok  in ('Harga Farmasi') order by namafield;
//        select * from persenhargajualproduk_m where objectjenistransaksifk =5; .:25%
        $metodeambilharganetto = $this->settingDataFixed('metodeambilharganetto', $kdProfile);

        if (empty($metodeambilharganetto)) {
            return $this->respond(array(
                'Error' => 'Setting Data Fixed Farmasi dulu',
                'message' => 'slvR',
            ));
        }
        $strMetodeAmbilHargaNetto = $metodeambilharganetto;
//        $strMetodeHargaNetto = $jenisTransaksi->metodeharganetto; //ketika penerimaan saja
        $strMetodeStokHargaNetto = $this->settingDataFixed('metodestokharganetto', $kdProfile);
        $strSistemHargaNetto = $this->settingDataFixed('sistemharganetto', $kdProfile);

        $persenHargaJualProduk = array(
            [
                'rangemin' => (float) $this->settingDataFixed('rangeMin', $kdProfile),
                'rangemax' => (float) $this->settingDataFixed('rangeMax', $kdProfile),
                'persenuphargasatuan' =>(float)  $this->settingDataFixed('persenMin', $kdProfile)
            ],
            [
                'rangemin' =>(float) $this->settingDataFixed('rangeMax', $kdProfile),
                'rangemax' => 9999999999,
                'persenuphargasatuan' => (float) $this->settingDataFixed('persenMax', $kdProfile)
            ]
        );
//        return $persenHargaJualProduk;
//        $persenHargaJualProduk = \DB::table('persenhargajualproduk_m as phjp')
//            ->JOIN('range_m as rg', 'rg.id', '=', 'phjp.objectrangefk')
//            ->select('rg.rangemin', 'rg.rangemax', 'phjp.persenuphargasatuan')
//            ->where('phjp.objectjenistransaksifk', 5)
//            ->where('phjp.statusenabled', true)
//            ->get();
        if (count($persenHargaJualProduk) == 0) {
            return $this->respond(array(
                'Error' => 'Setting persenhargajualproduk_m dulu',
                'message' => 'slvR',
            ));
        }

//        $persenUpHargaSatuan = $persenHargaJualProduk->persenuphargasatuan;
        $strHN = '';
        $strMSHT = '';
        $SistemHargaNetto = '';
        $MetodeAmbilHargaNetto = '';
        $MetodeStokHargaNetto = '';

        // ### FIFO ### //
        if ($strSistemHargaNetto == 1) {
            $SistemHargaNetto = 'FIFO';

            if ($strMetodeAmbilHargaNetto == 1) {//HN1
                $strHN = 'spd.harganetto1';
                $MetodeAmbilHargaNetto = 'HN1';
            }
            if ($strMetodeAmbilHargaNetto == 2) {//HN2
                $strHN = 'spd.harganetto2';
                $MetodeAmbilHargaNetto = 'HN2';
            }

            if ($strMetodeStokHargaNetto == 1) {//FIFO
                $strMSHT = 'sk.tglstruk';
                $MetodeStokHargaNetto = 'FIFO';
            }
            if ($strMetodeStokHargaNetto == 2) {//LIFO
                $strMSHT = '';
                $MetodeStokHargaNetto = 'LIFO';
            }
            if ($strMetodeStokHargaNetto == 3) {//FEFO
                $strMSHT = 'spd.tglkadaluarsa';
                $MetodeStokHargaNetto = 'FEFO';
            }
            if ($strMetodeStokHargaNetto == 4) {//LEFO
                $strMSHT = '';
                $MetodeStokHargaNetto = 'LEFO';
            }
            if ($strMetodeStokHargaNetto == 5) {//Summary
                $strMSHT = '';
                $MetodeStokHargaNetto = 'Summary';
            }
            $result = DB::select(DB::raw("select sk.norec,spd.produkidfk as objectprodukfk, $strMSHT as tgl,spd.asalprodukidfk as objectasalprodukfk,$strHN as harganetto ,
                      spd.hargadiscount,sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk as objectruanganfk,ap.asalproduk,spd.nostrukterimafk,spd.tglkadaluarsa
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                inner JOIN asalprodukmt as ap on ap.id=spd.asalprodukidfk
                where spd.koders = $kdProfile and spd.produkidfk =:produkId and spd.ruanganidfk =:ruanganid
                group by sk.norec,spd.produkidfk, $strMSHT,spd.asalprodukidfk,
                        $strHN,spd.hargadiscount,
                spd.ruanganidfk,ap.asalproduk,spd.nostrukterimafk
                order By $strMSHT"),
                array(
                    'produkId' => $request['produkfk'],
                    'ruanganid' => $request['ruanganfk'],
                )
            );

            $persenUpHargaSatuan = 0;
            foreach ($result as $item) {
                foreach ($persenHargaJualProduk as $hitem) {
                    if ((float)$hitem['rangemin'] < (float)$item->harganetto && (float)$hitem['rangemax'] > (float)$item->harganetto) {
                        $persenUpHargaSatuan = (float)$hitem['persenuphargasatuan'];
                    }
                }
                $results[] = array(
                    'norec' => $item->norec,
                    'objectprodukfk' => $item->objectprodukfk,
                    'tgl' => $item->tgl,
                    'objectasalprodukfk' => $item->objectasalprodukfk,
                    'asalproduk' => $item->asalproduk,
                    'harganetto' => $item->harganetto,
                    'hargadiscount' => $item->hargadiscount,
                    'hargajual' => (float)$item->harganetto + (((float)$item->harganetto * (float)$persenUpHargaSatuan) / 100),
                    'persenhargajualproduk' => $persenUpHargaSatuan,
                    'qtyproduk' => (float)$item->qtyproduk,
                    'objectruanganfk' => $item->objectruanganfk,
                    'nostrukterimafk' => $item->nostrukterimafk,
                    'tglkadaluarsa' => $item->tglkadaluarsa,
                );
            }
        }
        // ### END-FIFO ### //

        // ### Harga Tertinggi ### //
        if ($strSistemHargaNetto == 3) {
            $SistemHargaNetto = 'Harga Tertinggi';
            if ($strMetodeAmbilHargaNetto == 1) {//HN1
                $strHN = 'spd.harganetto1';
                $MetodeAmbilHargaNetto = 'HN1';
            }
            if ($strMetodeAmbilHargaNetto == 2) {//HN2
                $strHN = 'spd.harganetto2';
                $MetodeAmbilHargaNetto = 'HN2';
            }

            if ($strMetodeStokHargaNetto == 1) {//FIFO
                $strMSHT = 'sk.tglstruk';
                $MetodeStokHargaNetto = 'FIFO';
            }
            if ($strMetodeStokHargaNetto == 2) {//LIFO
                $strMSHT = '';
                $MetodeStokHargaNetto = 'LIFO';
            }
            if ($strMetodeStokHargaNetto == 3) {//FEFO
                $strMSHT = 'spd.tglkadaluarsa';
                $MetodeStokHargaNetto = 'FEFO';
            }
            if ($strMetodeStokHargaNetto == 4) {//LEFO
                $strMSHT = '';
                $MetodeStokHargaNetto = 'LEFO';
            }
            if ($strMetodeStokHargaNetto == 5) {//Summary
                $strMSHT = '';
                $MetodeStokHargaNetto = 'Summary';
            }
            $maxHarga = DB::select(DB::raw("select $strHN as harga
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                where spd.koders = $kdProfile and spd.produkidfk =:produkId and spd.ruanganidfk =:ruanganid"),
                array(
                    'produkId' => $request['produkfk'],
                    'ruanganid' => $request['ruanganfk'],
                )
            );
            $hargaTertinggi = 0;
            foreach ($maxHarga as $item) {
                if ($hargaTertinggi < (float)$item->harga) {
                    $hargaTertinggi = (float)$item->harga;
                }
            }

            $result = DB::select(DB::raw("select sk.norec,spd.produkidfk as objectprodukfk, $strMSHT as tgl,spd.asalprodukidfk as objectasalprodukfk,$hargaTertinggi as harganetto ,
                        $hargaTertinggi  as hargajual,spd.hargadiscount,sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk as objectruanganfk,ap.asalproduk,spd.nostrukterimafk,
                        spd.tglkadaluarsa
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                inner JOIN asalprodukmt as ap on ap.id=spd.asalprodukidfk
                where spd.koders = $kdProfile and spd.produkidfk =:produkId and spd.ruanganidfk =:ruanganid
                group by sk.norec,spd.produkidfk, $strMSHT,spd.asalprodukidfk,
                        spd.hargadiscount,
                spd.ruanganidfk,ap.asalproduk,spd.nostrukterimafk
                order By $strMSHT"),
                array(
                    'produkId' => $request['produkfk'],
                    'ruanganid' => $request['ruanganfk'],
                )
            );

            $persenUpHargaSatuan = 0;
            foreach ($result as $item) {
                foreach ($persenHargaJualProduk as $hitem) {
                    if ((float)$hitem['rangemin'] < (float)$item->harganetto && (float)$hitem['rangemax'] > (float)$item->harganetto) {
                        $persenUpHargaSatuan = (float)$hitem['persenuphargasatuan'];
                    }
                }
                $results[] = array(
                    'norec' => $item->norec,
                    'objectprodukfk' => $item->objectprodukfk,
                    'tgl' => $item->tgl,
                    'objectasalprodukfk' => $item->objectasalprodukfk,
                    'asalproduk' => $item->asalproduk,
                    'harganetto' => $item->harganetto,
                    'hargadiscount' => $item->hargadiscount,
                    'hargajual' => (float)$item->harganetto + (((float)$item->harganetto * (float)$persenUpHargaSatuan) / 100),
                    'persenhargajualproduk' => $persenUpHargaSatuan,
                    'qtyproduk' => (float)$item->qtyproduk,
                    'objectruanganfk' => $item->objectruanganfk,
                    'nostrukterimafk' => $item->nostrukterimafk,
                    'tglkadaluarsa' => $item->tglkadaluarsa,
                );
            }
        }
        // ### END-Harga Tertinggi ### //

        // ### Harga Terakhir ### //
        if ($strSistemHargaNetto == 7) {
            $SistemHargaNetto = 'Harga Terakhir';
            if ($strMetodeAmbilHargaNetto == 1) {//HN1
                $strHN = 'spd.harganetto1';
                $MetodeAmbilHargaNetto = 'HN1';
            }
            if ($strMetodeAmbilHargaNetto == 2) {//HN2
                $strHN = 'spd.harganetto2';
                $MetodeAmbilHargaNetto = 'HN2';
            }

            if ($strMetodeStokHargaNetto == 1) {//FIFO
                $strMSHT = 'sk.tglstruk';
                $MetodeStokHargaNetto = 'FIFO';
            }
            if ($strMetodeStokHargaNetto == 2) {//LIFO
                $strMSHT = 'sk.tglstruk desc';
                $MetodeStokHargaNetto = 'LIFO';
            }
            if ($strMetodeStokHargaNetto == 3) {//FEFO
                $strMSHT = 'spd.tglkadaluarsa';
                $MetodeStokHargaNetto = 'FEFO';
            }
            if ($strMetodeStokHargaNetto == 4) {//LEFO
                $strMSHT = 'spd.tglkadaluarsa desc';
                $MetodeStokHargaNetto = 'LEFO';
            }
            if ($strMetodeStokHargaNetto == 5) {//Summary
                $strMSHT = '';
                $MetodeStokHargaNetto = 'Summary';
            }
            $maxHarga = DB::select(DB::raw("select spd.tglpelayanan, $strHN as harga
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                where spd.koders = $kdProfile and spd.produkidfk =:produkId "),
                array(
                    'produkId' => $request['produkfk'],
                )
            );
            $hargaTerakhir = 0;
            $tgl = date('2000-01-01 00:00');
            foreach ($maxHarga as $item) {
                if ($tgl < $item->tglpelayanan) {
                    $tgl = $item->tglpelayanan;
                    $hargaTerakhir = (float)$item->harga;
                }
            }
            $result = [];
            $result = DB::select(DB::raw("select sk.norec,spd.produkidfk as objectprodukfk, $strMSHT as tgl,spd.asalprodukidfk as objectasalprodukfk,$hargaTerakhir as harganetto ,
                        $hargaTerakhir  as hargajual,spd.hargadiscount,spd.nostrukterimafk,
                (spd.qtyproduk) as qtyproduk,spd.ruanganidfk as objectruanganfk,ap.asalproduk,spd.tglkadaluarsa
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                inner JOIN asalprodukmt as ap on ap.id=spd.asalprodukidfk
                where spd.koders = $kdProfile and spd.produkidfk =:produkId and spd.ruanganidfk =:ruanganid and spd.qtyproduk > 0

                order By $strMSHT"),
                array(
                    'produkId' => $request['produkfk'],
                    'ruanganid' => $request['ruanganfk'],
                )
            );
            $results = [];
            $persenUpHargaSatuan = 0;
            foreach ($result as $item) {
                foreach ($persenHargaJualProduk as $hitem) {
                    if ((float)$hitem['rangemin'] < (float)$item->harganetto && (float)$hitem['rangemax'] > (float)$item->harganetto) {
                        $persenUpHargaSatuan = (float)$hitem['persenuphargasatuan'];
                    }
                }
                $results[] = array(
                    'norec' => $item->norec,
                    'objectprodukfk' => $item->objectprodukfk,
                    'tgl' => $item->tgl,
                    'objectasalprodukfk' => $item->objectasalprodukfk,
                    'asalproduk' => $item->asalproduk,
                    'harganetto' => (float)$item->harganetto,//$item->harganetto,
                    'hargadiscount' => $item->hargadiscount,
                    'hargajual' => (float)$item->harganetto + (((float)$item->harganetto * (float)$persenUpHargaSatuan) / 100),
                    'persenhargajualproduk' => $persenUpHargaSatuan,
                    'qtyproduk' => (float)$item->qtyproduk,
                    'objectruanganfk' => $item->objectruanganfk,
                    'nostrukterimafk' => $item->nostrukterimafk,
                    'tglkadaluarsa' => $item->tglkadaluarsa,
                );
            }
        }
        // ### END-Harga Terakhir ### //

        $jmlstok = 0;
        foreach ($result as $item) {
            $jmlstok = $jmlstok + $item->qtyproduk;
        }

//        $cekConsis = DB::select(DB::raw("select * from his_obat_ms_t where hobatid=:produkfk;"),
//            array(
//                'produkfk' => $request['produkfk'],
//            )
//        );
        $cekConsis =[];
        $cekKekuatanSupranatural = DB::select(DB::raw("

            select pr.kekuatan,sdn.name as sediaan from pelayananmt as pr
            inner join sediaanmt as sdn on sdn.id=pr.sediaanidfk
            where pr.koders = $kdProfile and pr.id=:produkfk;

            "),
            array(
                'produkfk' => $request['produkfk'],
            )
        );
        $kekuatan = 0;
        $sediaan = 0;
        if (count($cekKekuatanSupranatural) > 0) {
            $kekuatan = (float)$cekKekuatanSupranatural[0]->kekuatan;
            $sediaan = $cekKekuatanSupranatural[0]->sediaan;
            if ($kekuatan == null) {
                $kekuatan = 0;
            }
        }


        $result = array(
            'detail' => $results,
            'jmlstok' => $jmlstok,
            'kekuatan' => $kekuatan,
            'sediaan' => $sediaan,
            // 'sistemharganetto' => $SistemHargaNetto,
            // 'metodeambilharganetto' => $MetodeAmbilHargaNetto,
            // 'metodestokharganetto' => $MetodeStokHargaNetto,
            // 'consis' => count($cekConsis),
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function  getProdukPart(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);
        $kdKelUserLogistik =  (int) $this->settingDataFixed('KdKelUserLogistik', $kdProfile);
        $listKelUser = explode(',', $this->settingDataFixed('KdListKelUserProduk', $kdProfile));
        $listData = [];
        foreach ($listKelUser as $item) {
            $listData [] = (int)$item;
        }
        $req = $request->all();
        $dataProd = \DB::table('pelayananmt as pr')
            ->select('pr.id','pr.namaproduk' )
            ->where('pr.koders', $kdProfile)
            ->where('pr.aktif', true)
            ->orderBy('pr.namaproduk');

        if (isset($request['namaproduk']) && $request['namaproduk'] != '') {
            $dataProd = $dataProd->where('pr.namaproduk', 'ilike', '%' . $request['namaproduk'] . '%');
        }
        if (isset($request['idproduk']) && $request['idproduk'] != '') {
            $dataProd = $dataProd->where('pr.id', '=', $request['idproduk']);
        }
        if (isset($request['KelUserid']) && $request['KelUserid'] != '') {
            if ($request['KelUserid'] == $kdKelUserLogistik){
                $dataProd = $dataProd->whereIn('pr.kelompokuseridfk', $listData);
            }else{
                $dataProd = $dataProd->where('pr.kelompokuseridfk', '=', $request['KelUserid']);
            }
        }
        if(isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value']!="" &&
            $req['filter']['filters'][0]['value']!="undefined"){
            $dataProd = $dataProd
                ->where('pr.namaproduk','ilike','%'.$req['filter']['filters'][0]['value'].'%' );
        }
        if(isset($req['namaproduk']) && $req['namaproduk']!="" && $req['namaproduk']!="undefined"){
            $dataProd = $dataProd
                ->where('pr.namaproduk','ilike','%'.$req['namaproduk'].'%' );
        }

        $dataProd=$dataProd->take(10);
        $dataProd=$dataProd->get();

        return $this->respond($dataProd);
    }

    public function GetDataKartuStok(Request $request) {
        $idProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('transaksikartustoktr as ks')
            ->JOIN('pelayananmt as pro','pro.id','=','ks.produkidfk')
            ->JOIN('ruanganmt as ru','ru.id','=','ks.ruanganidfk')
            ->leftJoin('flagtransaksistokmt as fg','fg.id','=','ks.flagfk')
            ->select(DB::raw("
                ks.keterangan,ks.tglinput,ks.tglkejadian,ks.produkidfk,pro.namaproduk,
			    ks.ruanganidfk,ru.namaruangan,fg.flag as transaksi,
			    COALESCE (ks.saldoawal, 0.0) AS saldoawal,
			    COALESCE (ks.qtyin, 0.0) AS qtyin,
			    COALESCE (ks.qtyout, 0.0) AS qtyout,
			    COALESCE (ks.saldoakhir, 0.0) AS saldoakhir
            "))
            ->where('ks.koders',$idProfile);

        if(isset($request['tglAwal']) && $request['tglAwal']!="" && $request['tglAwal']!="undefined"){
            $data = $data->where('ks.tglkejadian','>=', $request['tglAwal']);
        }
        if(isset($request['tglAkhir']) && $request['tglAkhir']!="" && $request['tglAkhir']!="undefined"){
            $tgl= $request['tglAkhir'];
            $data = $data->where('ks.tglkejadian','<=', $tgl);
        }
        if(isset($request['ruanganfk']) && $request['ruanganfk']!="" && $request['ruanganfk']!="undefined"){
            $data = $data->where('ks.ruanganidfk','=', $request['ruanganfk']);
        }
        if(isset($request['produkfk']) && $request['produkfk']!="" && $request['produkfk']!="undefined"){
            $data = $data->where('ks.produkidfk','=', $request['produkfk']);
        }
        $data = $data->where('ks.aktif',true);
        $data = $data->orderBy('ks.tglkejadian');
        $data = $data->get();

        return $this->respond($data);
    }

    public function getDetailPasien(Request $request) {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('pasienmt as ps')
            ->leftJOIN('alamatmt as al','al.normidfk','=','ps.id')
            ->leftJOIN('jeniskelaminmt as jk','jk.id','=','ps.jeniskelaminidfk')
            ->select('ps.norm','ps.namapasien','ps.notelepon','ps.tgllahir','al.alamatlengkap','jk.id as jkid',
                     'jk.jeniskelamin','al.alamatemail')
            ->where('ps.koders', $kdProfile)
            ->where('ps.aktif',true);
        if(isset($request['nocm']) && $request['nocm']!="" && $request['nocm']!="undefined"){
            $data = $data->where('ps.norm', $request['nocm'] );
        };
        $data = $data->first();

        return $this->respond($data);
    }

    public function getDataRuangByKelUser(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataRuangan = \DB::table('ruanganmt as ru')
            ->select('ru.id','ru.namaruangan')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile);

        if (isset($request['keluseridfk'])){
            $dataRuangan = $dataRuangan->where('ru.kelompokuseridfk','=',$request['keluseridfk']);
        }

        $dataRuangan = $dataRuangan->orderBy('ru.namaruangan');
        $dataRuangan = $dataRuangan->get();

        $result = array(
            'ruangan' => $dataRuangan,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDataComboRuanganGeneral(Request $request) {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $req=$request->all();
        $dataProduk  = \DB::table('ruanganmt')
            ->select('id','namaruangan')
            ->where('koders', $kdProfile)
            ->where('aktif',true)
            ->orderBy('namaruangan');
        if(isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value']!="" &&
            $req['filter']['filters'][0]['value']!="undefined"){
            $dataProduk = $dataProduk->where('namaruangan','ilike','%'. $req['filter']['filters'][0]['value'].'%' );
        };
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();

        return $this->respond($dataProduk);
    }

    public function getComboPegawaiGeneral(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);
        $req = $request->all();
        $data = \DB::table('pegawaimt')
            ->select('id','namalengkap')
            ->where('koders', $kdProfile)
            ->where('aktif', true)
            ->orderBy('namalengkap');

        if(isset($req['namalengkap']) &&
            $req['namalengkap']!="" &&
            $req['namalengkap']!="undefined"){
            $data = $data->where('namalengkap','ilike','%'. $req['namalengkap'] .'%' );
        };
        if(isset($req['idpegawai']) &&
            $req['idpegawai']!="" &&
            $req['idpegawai']!="undefined"){
            $data = $data->where('id', $req['idpegawai'] );
        };
        if(isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value']!="" &&
            $req['filter']['filters'][0]['value']!="undefined"){
            $data = $data
                ->where('namalengkap','ilike','%'.$req['filter']['filters'][0]['value'].'%' );
        }

        $data = $data->get();
        return $this->respond($data);
    }

    public function getComboJabatanGeneral(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);
        $req = $request->all();
        $data = \DB::table('jabatanmt')
            ->select('id','namajabatan')
            ->where('aktif', true)
            ->orderBy('namajabatan');

        if(isset($req['namajabatan']) &&
            $req['namajabatan']!="" &&
            $req['namajabatan']!="undefined"){
            $data = $data->where('namajabatan','ilike','%'. $req['namajabatan'] .'%' );
        };
        if(isset($req['idjabatan']) &&
            $req['idjabatan']!="" &&
            $req['idjabatan']!="undefined"){
            $data = $data->where('id', $req['idjabatan'] );
        };
        if(isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value']!="" &&
            $req['filter']['filters'][0]['value']!="undefined"){
            $data = $data
                ->where('namajabatan','ilike','%'.$req['filter']['filters'][0]['value'].'%' );
        }

        $data = $data->get();
        return $this->respond($data);
    }

    public function getDataPemakaianAsuransi(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $Noregistrasi = $request['noregistrasi'];
        $data = collect(DB::select("
            SELECT pa.*,ap.nmprovider,ap.kdprovider,ap.notelpmobile,dd.kddiagnosa,dd.namadiagnosa,ap.jenispeserta,
            kl.namakelas
            FROM registrasipasientr AS pd
            INNER JOIN pemakaianasuransitr AS pa ON pa.registrasipasienfk = pd.norec
            LEFT JOIN asuransimt ap ON pa.asuransiidfk = ap.id
            left join icdxmt AS dd ON dd.id = pa.icdxidfk 
            left join kelasmt AS kl ON kl.id = ap.kelasdijaminidfk 
            WHERE pd.aktif = true AND pd.koders = $kdProfile 
            AND pd.noregistrasi = '$Noregistrasi'
        "))->first();

        $status = false;
        if (!empty($data) && !empty($data->nosep)){
            $status = true;
        }
        $res[0] = $data;
        $res[1] = $status;
        return $this->respond($res);
    }

    public function getComboAdmin(Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);
        $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenPelayanan', $kdProfile));
        $kd = [];
        foreach ($deptRanap as $itemRanap) {
            $kd[] = (int)$itemRanap;
        }

        $dataRuangan = \DB::table('ruanganmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->whereIn('ru.instalasiidfk', $kd)
            ->orderBy('ru.namaruangan')
            ->get();

        $jenisPelayan = collect(DB::select("
            select * from jenispelayananmt where aktif = true
        "));

        $result = array(
            'ruangan' => $dataRuangan,
            'jenispelayanan' => $jenisPelayan,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getComboProdukAdministrasi(Request $request) {
        $dataLogin = $request->all();
        $kdProfile = (int) $this->getDataKdProfile($request);
        $deptJalan = explode (',',$this->settingDataFixed('kdDepartemenRawatJalanFix',  $kdProfile ));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item){
            $kdDepartemenRawatJalan []=  (int)$item;
        }
        $dpGD = explode (',',$this->settingDataFixed('KdDepartemenInstalasiGawatDarurat',  $kdProfile ));
        foreach ($dpGD as $item){
            $kdDepartemenRawatJalan []=  (int)$item;
        }
        $dataRuanganInap = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->whereIn('ru.instalasiidfk', $kdDepartemenRawatJalan)
            ->where('ru.aktif', true)
            ->where('ru.koders', $kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataProduk=[];
        $datalistAkomodasi=[];
        if ($request['produk']==1){
            $dataProduk = \DB::table('pelayananmt as pr')
                ->JOIN('mappelayananruanganmt as ma','ma.produkidfk','=','pr.id')
                ->select('pr.id','pr.namaproduk')
                ->where('pr.aktif',true)
                ->where('pr.koders', $kdProfile)
                ->orderBy('pr.namaproduk');
            if(isset($request['objectruanganfk']) && $request['objectruanganfk']!="" && $request['objectruanganfk']!="undefined"){
                $dataProduk = $dataProduk->where('ma.ruanganidfk','=', $request['objectruanganfk']);
            }
            $dataProduk = $dataProduk->get();

            $datalistAkomodasi = \DB::table('pelayananmt as pr')
                ->JOIN('mapruangantoadministrasitr as ma','ma.produkidfk','=','pr.id')
                ->JOIN('jenispelayananmt as jp','jp.id','=','ma.jenispelayananidfk')
                ->select('pr.id','pr.namaproduk','ma.israwatgabung','ma.id as maid','ma.aktif AS statusenabled',
                    'ma.jenispelayananidfk AS jenispelayananfk','jp.jenispelayanan')
                ->where('pr.koders', $kdProfile)
                ->where('pr.aktif',true)
                ->where('ma.aktif',true)
                ->orderBy('pr.namaproduk');
            if(isset($request['objectruanganfk']) && $request['objectruanganfk']!="" && $request['objectruanganfk']!="undefined"){
                $datalistAkomodasi = $datalistAkomodasi->where('ma.ruanganidfk','=', $request['objectruanganfk']);
            }
            $datalistAkomodasi = $datalistAkomodasi->get();
        }

        $result = array(
            'produk' => $dataProduk,
            'ruangan' => $dataRuanganInap,
            'listakomodasi' => $datalistAkomodasi,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveMappingAdminstrasi(Request $request) {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = $request->all();
        try {
            if ($data['status'] == 'HAPUS') {
                $newKS = MapRuanganToAdministrasi::where('id', $request['maid'])->where('koders', $kdProfile)->delete();
            }else {
                if ($data['maid'] == '') {
                    if ($data['rg'] == 'YES'){
                        $RG = 1;
                    }else{
                        $RG =null;
                    }
                    $newKS = new MapRuanganToAdministrasi();
                    $norecKS = MapRuanganToAdministrasi::max('id');
                    $norecKS = $norecKS + 1;
                    $newKS->id = $norecKS;
                    $newKS->norec = $norecKS;
                    $newKS->koders = $kdProfile;
                    $newKS->aktif = true;
                    $newKS->produkidfk = $data['pelayanan'];
                    $newKS->ruanganidfk = $data['ruangan'];
                    $newKS->jenispelayananidfk = $data['jenispelayanan'];
                    $newKS->israwatgabung = $RG;
                    $newKS->save();
                } else {
                    $newKS = MapRuanganToAdministrasi::where('id', $request['maid'])
                        ->where('koders', $kdProfile)
                        ->update([
                                'ruanganidfk' => $data['ruangan'],
                                'produkidfk' => $data['pelayanan'],
                                'jenispelayananidfk' => $data['jenispelayanan'],
                                'israwatgabung' => null,
                            ]
                        );
                }
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        $transMessage = $data['status'] ." Administrasi Otomatis";

        if ($transStatus == 'true') {
            $transMessage = $transMessage . " Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "data" => $newKS,
                "as" => 'Xoxo',
            );
        } else {
            $transMessage =  $transMessage . " Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "error" => $e,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDataProfil (Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);

        $data = collect(DB::select("
            SELECT ps.id,ps.namaexternal,ps.alamatemail,ps.alamatlengkap,ps.faksimile,
                   ps.fixedphone,ps.kodepos,ps.namalengkap,ps.website,ps.namapemerintahan,
                   ps.namakota
            FROM profile_m AS ps WHERE ps.statusenabled = TRUE AND ps.id = $kdProfile
        "));

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function deleteProfilRS(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);
        try {
            if ($request['id'] != '') {
                ProfileM::where('id', $request['id'])
                    ->update([
                        'aktif' => false
                    ]);
            }
            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus) {
            $transMessage = "Hapus Data Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => 'Xoxo'
            );
        } else {
            $transMessage = "Hapus Data Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => 'Xoxo'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function saveDataProfilRs(Request $request){
        DB::beginTransaction();
        try {
             if ($request['id'] == ""){
                 $SG = new ProfileM();
                 $idNew = ProfileM::max('id') + 1;
                 $SG->id = $idNew;
                 $SG->kdprofile = $idNew;
                 $SG->aktif = true;
                 $SG->norec =  $SG->generateNewId();
             }else{
                 $SG = ProfileM::where('id',$request['id'])->first();
             }
                $SG->namaexternal = $request['namaexternal'];
                $SG->namalengkap = $request['namalengkap'];
                $SG->alamatemail = $request['alamatemail'];
                $SG->kodepos = $request['kodepos'];
                $SG->alamatlengkap = $request['alamatlengkap'];
                $SG->faksimile = $request['faksimile'];
                $SG->fixedphone = $request['fixedphone'];
                $SG->website = $request['website'];
                $SG->namapemerintahan = $request['namapemerintahan'];
                $SG->namakota = $request['namakota'];
                $SG->save();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $SG,
                "as" => 'er',

            );
        } else {
            $transMessage = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDataPegawaiRs (Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);

        $data = collect(DB::select("
            SELECT ps.id,ps.namalengkap,ps.tempatlahir,ps.tgllahir,ps.alamat,
                   ps.objectjeniskelaminfk,jk.jeniskelamin,ps.noidentitas,ps.nip_pns,
                   ps.tglmasuk,ps.objectjenispegawaifk,jp.jenispegawai,ps.objectstatuspegawaifk,
                   sp.statuspegawai                   
            FROM pegawaimt AS ps 
            LEFT JOIN jenispegawaimt AS jp ON jp.id = ps.objectjenispegawaifk
            LEFT JOIN jeniskelaminmt AS jk ON jk.id = ps.objectjeniskelaminfk
            LEFT JOIN statuspegawaimt AS sp ON sp.id = ps.objectstatuspegawaifk
            WHERE ps.aktif = TRUE
        "));

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function deletePegawaiRS(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);
        try {
            if ($request['id'] != '') {
                Pegawai::where('id', $request['id'])
                    ->update([
                        'aktif' => false
                    ]);
            }
            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus) {
            $transMessage = "Hapus Data Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => 'Xoxo'
            );
        } else {
            $transMessage = "Hapus Data Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => 'Xoxo'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboPegawaiRs(Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);
        $jenisPegawai = collect(DB::select("
            SELECT id,jenispegawai 
            FROM jenispegawaimt WHERE aktif = TRUE AND koders = $kdProfile
            ORDER BY jenispegawai ASC
        "));

        $statusPegawai = collect(DB::select("
            SELECT id,statuspegawai 
            FROM statuspegawaimt
            WHERE aktif = TRUE AND koders = $kdProfile
            ORDER BY statuspegawai ASC
        "));

        $jenisKelamin = collect(DB::select("
            SELECT id,jeniskelamin FROM jeniskelaminmt 
            WHERE aktif = TRUE AND koders = $kdProfile
            ORDER BY jeniskelamin ASC
        "));

        $result = array(
            'jenispegawai' => $jenisPegawai,
            'statuspegawai' => $statusPegawai,
            'jeniskelamin' => $jenisKelamin,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveDataPegawaiRs(Request $request){
        DB::beginTransaction();
        try {
            $kdProfile = (int)$this->getDataKdProfile($request);
            if ($request['id'] == ""){
                $SG = new Pegawai;
                $idNew = Pegawai::max('id') + 1;
                $SG->id = $idNew;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->norec =  $SG->generateNewId();
            }else{
                $SG = Pegawai::where('id',$request['id'])->first();
            }
                $SG->namalengkap = $request['namalengkap'];
                $SG->tempatlahir = $request['tempatlahir'];
                $SG->tgllahir = $request['tgllahir'];
                $SG->objectjeniskelaminfk = $request['objectjeniskelaminfk'];
                $SG->noidentitas = $request['noidentitas'];
                $SG->nip_pns = $request['nip_pns'];
                $SG->tglmasuk = $request['tglmasuk'];
                $SG->objectjenispegawaifk = $request['jenispegawai'];
                $SG->objectstatuspegawaifk = $request['statuspegawai'];
                $SG->alamat = $request['alamat'];
                $SG->save();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $SG,
                "as" => 'er',

            );
        } else {
            $transMessage = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboSensus(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);

        $StatusPulang = DB::table('statuspulangmt')
            ->select(DB::raw("id, UPPER(statuspulang) as statuspulang"))
            ->where('aktif', true)
            ->get();

        $StatusKeluar = DB::table('statuskeluarmt')
            ->select(DB::raw("id, UPPER(statuskeluar) as statuskeluar"))
            ->where('aktif', true)
            ->get();

        $KondisiPasien = DB::table('kondisipasienmt')
            ->select(DB::raw("id, UPPER(kondisipasien) as kondisipasien"))
            ->where('aktif', true)
            ->get();

        $penyebabKematian = \DB::table('penyebabkematianmt as sp')
            ->select('sp.id', 'sp.penyebabkematian')
            ->where('sp.aktif', true)
            ->orderBy('sp.penyebabkematian')
            ->get();

        $idStatusKeluarMeninggal = $this->settingDataFixed('idStatusKeluarMeninggal', $kdProfile);

        $result = array(
            'statuspulang' => $StatusPulang,
            'statuskeluar' => $StatusKeluar,
            'kondisipasien' => $KondisiPasien,
            'penyebabkematian' => $penyebabKematian,
            'idmeninggal' => $idStatusKeluarMeninggal,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveDataStatusPulangPasien(Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);
        if ($request['penyebabkematian'] == null) {
            $request['tglmeninggal'] = null;
            $request['ketpenyebabkematian'] = null;
        }
        DB::beginTransaction();
        try {

            $upDpr = DaftarPasienRuangan::where('norec', $request['norec_dpr'])
                     ->where('koders', $kdProfile)
                     ->update([
                        "tglkeluar" => $request['tglkeluar'],
                        "statuskeluaridfk" => $request['statuskeluar'],
                        "statuspulangidfk" => $request['statuspulang'],
                        "kondisipasienidfk" => $request['kondisikeluar'],
                        "tglmeninggal" => $request['tglmeninggal'],
                        "penyebabkematian" => $request['penyebabkematian'],
                        "ketkematian" => $request['ketpenyebabkematian'],
                     ]);

            $upDprd = RegistrasiPasien::where('norec', $request['norec_rg'])
                ->where('koders', $kdProfile)
                ->update([            
                    "statuskeluaridfk" => $request['statuskeluar'],
                    "statuspulangidfk" => $request['statuspulang'],
                    "kondisipasienidfk" => $request['kondisikeluar'],
                    "tglmeninggal" => $request['tglmeninggal'],
                    "penyebabkematianidfk" => $request['penyebabkematian'],
                    "keteranganpenyebabkematian" => $request['ketpenyebabkematian'],
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'ea',
            );
        } else {
            $transMessage = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'ea',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getComboDokterPart2(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter', $kdProfile);
        $req = $request->all();
        $data = \DB::table('pegawaimt')
            ->select('*')
            ->where('aktif', true)
            ->where('objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->where('koders', (int)$kdProfile)
            ->orderBy('namalengkap');

        if (isset($req['namalengkap']) &&
            $req['namalengkap'] != "" &&
            $req['namalengkap'] != "undefined") {
            $data = $data->where('namalengkap', 'ilike', '%' . $req['namalengkap'] . '%');
        };
        if (isset($req['idpegawai']) &&
            $req['idpegawai'] != "" &&
            $req['idpegawai'] != "undefined") {
            $data = $data->where('id', $req['idpegawai']);
        };
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $data = $data
                ->where('namalengkap', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        }

        $data = $data->get();

        return $this->respond($data);
    }
    public function getMapPaket(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = DB::table('mappakettopelayananmt as mm')
            ->join('paketmt as pk', 'pk.id', '=', 'mm.paketidfk')
            ->select('mm.paketidfk','pk.namapaket')
            ->where('mm.koders', $kdProfile)
            ->where('mm.aktif', true)
            ->where('pk.aktif', true)
            ->distinct();
        if (isset($request['paketId']) && $request['paketId'] != '') {
            $data = $data->where('mm.paketidfk', $request['paketId']);
        }
        // if (isset($request['produk']) && $request['produk'] != '') {
        //     $data = $data->where('lo.namaproduk', 'ilike', '%' . $request['produk'] . '%');
        // }
        // if (isset($request['limit']) && $request['limit'] != '') {
        //     $data = $data->take($request['limit']);
        // }
        $data = $data->get();


        $data2 = DB::table('mappakettopelayananmt as mm')
        ->join('paketmt as pk', 'pk.id', '=', 'mm.paketidfk')
        ->join('pelayananmt as pl', 'pl.id', '=', 'mm.produkidfk')
        ->select('mm.*','pk.namapaket','pl.namaproduk')
        ->where('mm.koders', $kdProfile)
        ->where('pk.aktif', true)
        ->where('pl.aktif', true)
        ->where('mm.aktif', true);
       
        if (isset($request['paketId']) && $request['paketId'] != '') {
            $data2 = $data2->where('mm.paketidfk', $request['paketId']);
        }
        if (isset($request['produk']) && $request['produk'] != '') {
            $data2 = $data2->where('pl.namaproduk', 'ilike', '%' . $request['produk'] . '%');
        }
        // if (isset($request['limit']) && $request['limit'] != '') {
        //     $data = $data->take($request['limit']);
        // }
        $data2 = $data2->get();

        foreach($data as $dd){
            $dd->details = [];
            foreach($data2 as $d2){
                if($dd->paketidfk == $d2->paketidfk){
                    $dd->details[] = array(
                        'namaproduk'=> $d2->namaproduk,
                        'paketidfk'=> $d2->paketidfk,
                        'produkidfk'=> $d2->produkidfk,
                    );
                }
            }
            $dd->jml = count($dd->details);
        }
     
        return $data;
    }
    public function deleteMapPaket(Request $request){
        DB::beginTransaction();
        try {
            $kdProfile = (int)$this->getDataKdProfile($request);
            if (isset($request['id']) ){
                MapPaketToProduk::where('paketidfk',$request['id'])->delete();
            }else{
                MapPaketToProduk::where('paketidfk',$request['paketidfk'])
                ->where('produkidfk',$request['produkidfk'])
                ->delete();
            }
             

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                // "data" => $SG,
                "as" => 'er',

            );
        } else {
            $transMessage = "Hapus Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function saveMapPaket(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['details'] as $item) {
                $kode[] =  $item['produkidfk'];
            }

            $hapus = MapPaketToProduk::where('aktif', true)
                ->where('koders', $kdProfile)
                ->where('paketidfk', $request['paketidfk'])
                //                ->where('produkidfk',$kode)
                ->delete();

            foreach ($request['details'] as $item) {
                $SG = new MapPaketToProduk();
                $SG->id =  MapPaketToProduk::max('id') + 1;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->kodeexternal = 'App';
                $SG->norec =  $SG->generateNewId();
                $SG->produkidfk = $item['produkidfk'];
                $SG->paketidfk = $request['paketidfk'];
                $SG->save();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Map Paket To Pelayanan";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $SG,
                "as" => 'er',

            );
        } else {
            $transMessage = "Simpan gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getPaket(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $db = DB::table('paketmt')
        ->where('koders',$kdProfile)
        ->where('aktif',true)
        ->orderBy('namapaket')
        ->get();
        return $db;
    }
    public function savePaket(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $SG = '';
            if($request['method'] == 'save'){
                if($request['id'] == ''){
                    $SG = new Paket();
                    $SG->id =  Paket::max('id') + 1;
                    $SG->koders = $kdProfile;
                    $SG->aktif = true;
                    $SG->kodeexternal = 'App';
                  
                }else{
                    $SG =  Paket::where('id',$request['id'] )->first();
                }
                $SG->norec =  $SG->generateNewId();
                $SG->namapaket = $request['namapaket'];
                $SG->save();
            }else{
                $SG =  Paket::where('id',$request['id'] )->delete();
            }

        
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $SG,
                "as" => 'er',

            );
        } else {
            $transMessage = "Simpan gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function saveSK(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $SG = '';
            if($request['method'] == 'save'){
                if($request['id'] == ''){
                    $SG = new SuratKeputusan();
                    $SG->id =  SuratKeputusan::max('id') + 1;
                    $SG->koders = $kdProfile;
                    $SG->aktif = false;
                    // $SG->kodeexternal = 'App';
                    // $SG->norec =  $SG->generateNewId(); 
                }else{
                    $SG =  SuratKeputusan::where('id',$request['id'] )->first();
                }
              
                $SG->namask = $request['namask'];
                $SG->nosk = $request['nosk'];
                $SG->tglberlakuawal = $request['tglberlakuawal'];
                $SG->tglberlakuakhir = $request['tglberlakuakhir'];
                $SG->save();
            } 
             if($request['method'] == 'delete'){
                $SG =  SuratKeputusan::where('id',$request['id'] )->delete();
            }
            if($request['method'] == 'aktif'){
               
                $SG =  SuratKeputusan::where('id',$request['id'] )->update([
                    'aktif' => $request['aktif'] ,
                ]);
                if($request['aktif'] == true){
                    $SG =  SuratKeputusan::where('id','<>',$request['id'] )->update([
                        'aktif' => false,
                    ]);
                }
            }
        
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $SG,
                "as" => 'er',

            );
        } else {
            $transMessage = "Simpan gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getSK(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $db = DB::table('suratkeputusanmt')
        ->where('koders',$kdProfile)
        // ->where('aktif',true)
        ->orderBy('namask')
        ->get();
        return $db;
    }
    public function getDataAkomodasi(Request $request) {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $datalistAkomodasi = \DB::table('pelayananmt as pr')
        ->JOIN('mapruangantoakomodasitr as ma','ma.produkidfk','=','pr.id')
        ->JOIN('jenispelayananmt as jp','jp.id','=','ma.jenispelayananidfk')
        ->JOIN('ruanganmt as ru','ru.id','=','ma.ruanganidfk')
        ->select('pr.id','pr.namaproduk','ma.israwatgabung','ma.id as maid','ma.aktif AS statusenabled',
            'ma.jenispelayananidfk AS jenispelayananfk','jp.jenispelayanan','ru.namaruangan')
         ->distinct()
        ->where('pr.koders', $kdProfile)
        ->where('pr.aktif',true)
        ->where('ma.aktif',true)
        ->orderBy('pr.namaproduk');
        if(isset($request['objectruanganfk']) && $request['objectruanganfk']!="" && $request['objectruanganfk']!="undefined"){
            $datalistAkomodasi = $datalistAkomodasi->where('ma.ruanganidfk','=', $request['objectruanganfk']);
        }
        $datalistAkomodasi = $datalistAkomodasi->get();
        $result = array(
          
            'listakomodasi' => $datalistAkomodasi,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }
    public function getComboProdukAkomodasi(Request $request) {
        // $dataLogin = $request->all();
        $kdProfile = (int) $this->getDataKdProfile($request);
        // $deptJalan = explode (',',$this->settingDataFixed('kdDepartemenRawatJalanFix',  $kdProfile ));
        // $kdDepartemenRawatJalan = [];
        // foreach ($deptJalan as $item){
        //     $kdDepartemenRawatJalan []=  (int)$item;
        // }
        // $dpGD = explode (',',$this->settingDataFixed('KdDepartemenInstalasiGawatDarurat',  $kdProfile ));
        // foreach ($dpGD as $item){
        //     $kdDepartemenRawatJalan []=  (int)$item;
        // }
        // $dataRuanganInap = \DB::table('ruanganmt as ru')
        //     ->select('ru.id', 'ru.namaruangan')
        //     ->whereIn('ru.instalasiidfk', $kdDepartemenRawatJalan)
        //     ->where('ru.aktif', true)
        //     ->where('ru.koders', $kdProfile)
        //     ->orderBy('ru.namaruangan')
        //     ->get();

        $dataProduk=[];
        $datalistAkomodasi=[];
        if ($request['produk']==1){
            $dataProduk = \DB::table('pelayananmt as pr')
                ->JOIN('mappelayananruanganmt as ma','ma.produkidfk','=','pr.id')
                ->select('pr.id','pr.namaproduk')
                ->distinct()
                ->where('pr.aktif',true)
                ->where('ma.aktif',true)
                ->where('pr.koders', $kdProfile)
                ->orderBy('pr.namaproduk');
            if(isset($request['objectruanganfk']) && $request['objectruanganfk']!="" && $request['objectruanganfk']!="undefined"){
                $dataProduk = $dataProduk->where('ma.ruanganidfk','=', $request['objectruanganfk']);
            }
            $dataProduk = $dataProduk->get();

            // $datalistAkomodasi = \DB::table('pelayananmt as pr')
            //     ->JOIN('mapruangantoakomodasitr as ma','ma.produkidfk','=','pr.id')
            //     ->JOIN('jenispelayananmt as jp','jp.id','=','ma.jenispelayananidfk')
            //     ->select('pr.id','pr.namaproduk','ma.israwatgabung','ma.id as maid','ma.aktif AS statusenabled',
            //         'ma.jenispelayananidfk AS jenispelayananfk','jp.jenispelayanan')
            //      ->distinct()
            //     ->where('pr.koders', $kdProfile)
            //     ->where('pr.aktif',true)
            //     ->where('ma.aktif',true)
            //     ->orderBy('pr.namaproduk');
            // if(isset($request['objectruanganfk']) && $request['objectruanganfk']!="" && $request['objectruanganfk']!="undefined"){
            //     $datalistAkomodasi = $datalistAkomodasi->where('ma.ruanganidfk','=', $request['objectruanganfk']);
            // }
            // $datalistAkomodasi = $datalistAkomodasi->get();
        }

        $result = array(
            'produk' => $dataProduk,
            // 'ruangan' => $dataRuanganInap,
            'listakomodasi' => $datalistAkomodasi,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveMappingAkomodasi(Request $request) {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = $request->all();
        try {
            if ($data['status'] == 'HAPUS') {
                $newKS = MapRuanganToAkomodasi::where('id', $request['maid'])->where('koders', $kdProfile)->delete();
            }else {
                if ($data['maid'] == '') {
                    if ($data['rg'] == 'YES'){
                        $RG = 1;
                    }else{
                        $RG =null;
                    }
                    $newKS = new MapRuanganToAkomodasi();
                    $norecKS = MapRuanganToAkomodasi::max('id');
                    $norecKS = $norecKS + 1;
                    $newKS->id = $norecKS;
                    $newKS->norec = $norecKS;
                    $newKS->koders = $kdProfile;
                    $newKS->aktif = true;
                    $newKS->produkidfk = $data['pelayanan'];
                    $newKS->ruanganidfk = $data['ruangan'];
                    $newKS->jenispelayananidfk = $data['jenispelayanan'];
                    $newKS->israwatgabung = $RG;
                    $newKS->save();
                } else {
                    $newKS = MapRuanganToAkomodasi::where('id', $request['maid'])
                        ->where('koders', $kdProfile)
                        ->update([
                                'ruanganidfk' => $data['ruangan'],
                                'produkidfk' => $data['pelayanan'],
                                'jenispelayananidfk' => $data['jenispelayanan'],
                                'israwatgabung' => null,
                            ]
                        );
                }
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        $transMessage = $data['status'] ." Akomodasi Otomatis";

        if ($transStatus == 'true') {
            $transMessage = $transMessage . " Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "data" => $newKS,
                "as" => 'Xoxo',
            );
        } else {
            $transMessage =  $transMessage . " Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "error" => $e,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

}
