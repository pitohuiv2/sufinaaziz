<?php

namespace App\Http\Controllers\Bridging;

use App\Http\Controllers\ApiController;
use App\Master\DiagnosaKeperawatan;
use App\Master\KelompokTransaksi;
// use App\Transaksi\AntrianPasienDiperiksa;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\HasilGrouping;
use App\Transaksi\InformasiTanggungan;
// use App\Transaksi\PelayananPasien;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\PemakaianAsuransi;
use DB;
use Illuminate\Http\Request;
use App\Traits\Valet;
use phpDocumentor\Reflection\Types\Null_;
use Webpatser\Uuid\Uuid;


class InacbgC extends ApiController
{

    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }
    public function getDaftarPasienRev(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $data  = \DB::table('settingdatafixedmt')
            ->select('namafield','nilaifield')
           ->where('kelompok',"INACBG's")
            ->where('koders', $kdProfile)
            ->get();
        foreach ($data as $item){
            if ($item->namafield == 'codernik'){
                $codernik = $item->nilaifield;
            }
            if ($item->namafield == 'key'){
                $key = $item->nilaifield;
            }
            if ($item->namafield == 'url'){
                $url = $item->nilaifield;
            }
            if ($item->namafield == 'kodetarif'){
                $kodetarif = $item->nilaifield;
            }
        }

        $data = \DB::table('registrasipasientr as pd')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'pd.pegawaiidfk')
            ->leftJoin('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
//            ->leftJoin('departemen_m as dept', 'dept.id', '=', 'ru.objectdepartemenfk')
//            ->join('antrianpasiendiperiksa_t as apd', 'pd.norec', '=', 'apd.noregistrasifk')
            ->leftJoin('strukpelayanantr as sp', 'sp.norec', '=', 'pd.nostruklastidfk')
            ->leftJoin('strukbuktipenerimaantr as sbm', 'sbm.norec', '=', 'pd.nosbmlastidfk')
//            ->leftjoin('loginuser_s as lu', 'lu.id', '=', 'sbm.objectpegawaipenerimafk')
//            ->leftjoin('pegawai_m as pgs', 'pgs.id', '=', 'lu.objectpegawaifk')
            ->leftjoin('pemakaianasuransitr as pas', 'pas.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('asuransimt as asu', 'asu.id', '=', 'pas.asuransiidfk')
            ->leftjoin('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
            ->leftjoin('kelasmt as kls2', 'kls2.id', '=', 'asu.kelasdijaminidfk')
//            ->leftjoin('batalregistrasi_t as br', 'br.pasiendaftarfk', '=', 'pd.norec')
            ->leftjoin('hasilgroupingtr as hg', 'hg.registrasipasienfk', '=', 'pd.norec')
//            ->leftjoin('diagnosaberatbadanbayi_t as dbb', 'dbb.noregistrasifk', '=', 'pd.norec')
            ->leftjoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->select('pd.norec', 'pd.tglregistrasi', 'ps.norm as nocm', 'pd.noregistrasi', 'ru.namaruangan', 'ps.namapasien', 'kp.kelompokpasien',
                'pd.tglpulang', 'pd.statuspasien', 'pg.id as pgid', 'pg.namalengkap as namadokter',
                'pd.ruanganlastidfk as ruanganid','pas.nosep','pas.norec as norec_pa',
                'pas.nokepesertaan','ps.tgllahir','ps.jeniskelaminidfk as objectjeniskelaminfk','ru.instalasiidfk as deptid','kls.nourut as nokelasdaftar','kls2.nourut as nokelasdijamin',
                'kls.reportdisplay as namakelasdaftar','kls2.namakelas as namakelas','pd.statuspulangidfk as objectstatuspulangfk','pd.asalrujukanidfk',
                'hg.biayanaikkelas','ps.beratbadan','rk.id as idrekanan','hg.status as statusgrouping','ru.jenis as statuscovid','ps.noidentitas',
                DB::raw(" 'verifikasi'  as status, null as loscovid"),
                DB::raw("case when hg.totalpiutangpenjamin is null then 1 else hg.totalpiutangpenjamin end as totalpiutangpenjamin,
                case when pd.tglregistrasi <> pd.tglpulang then 1 else 2 end jenis_rawat"))
            ->where('pd.aktif',true)
            ->where('pd.koders',$kdProfile)
             ->where('pas.nosep','<>','')
            ->whereNotNull('pas.nosep')
            ->whereNotNull('pd.tglpulang');


        $filter = $request->all();
        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
            $data = $data->where('pd.tglregistrasi', '>=', $filter['tglAwal']);
        }
        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
            $tgl = $filter['tglAkhir'];//." 23:59:59";
            $data = $data->where('pd.tglregistrasi', '<=', $tgl);
        }
        if (isset($filter['deptId']) && $filter['deptId'] != "" && $filter['deptId'] != "undefined") {
            $data = $data->where('dept.id', '=', $filter['deptId']);
        }
        if (isset($filter['ruangId']) && $filter['ruangId'] != "" && $filter['ruangId'] != "undefined") {
            $data = $data->where('ru.id', '=', $filter['ruangId']);
        }
        if (isset($filter['kelId']) && $filter['kelId'] != "" && $filter['kelId'] != "undefined") {
            $data = $data->where('kp.id', '=', $filter['kelId']);
        }
        if (isset($filter['dokId']) && $filter['dokId'] != "" && $filter['dokId'] != "undefined") {
            $data = $data->where('pg.id', '=', $filter['dokId']);
        }
        if (isset($filter['sttts']) && $filter['sttts'] != "" && $filter['sttts'] != "undefined") {
            $data = $data->where('pd.statuspasien', '=', $filter['sttts']);
        }
        if (isset($filter['noreg']) && $filter['noreg'] != "" && $filter['noreg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'like', '%' . $filter['noreg'] . '%');
        }
        if (isset($filter['norm']) && $filter['norm'] != "" && $filter['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'like', '%' . $filter['norm'] . '%');
        }
        if (isset($filter['nama']) && $filter['nama'] != "" && $filter['nama'] != "undefined") {
            $data = $data->where('ps.namapasien', 'like', '%' . $filter['nama'] . '%');
        }
        if (isset($filter['nosep']) && $filter['nosep'] != "" && $filter['nosep'] != "undefined") {
            $data = $data->where('pas.nosep', '=', $filter['nosep']);
        }
//        if (isset($filter['jmlRows']) && $filter['jmlRows'] != "" && $filter['jmlRows'] != "undefined") {
//            $data = $data->take($filter['jmlRows']);
//        }
        $data = $data->orderBy('pd.noregistrasi');

        $data = $data->get();

        $i = 0 ;
        $dtdt = '';
        // $norecaPd = '';
        // foreach ($data as $ob) {
        //     $norecaPd = $norecaPd . ",'" . $ob->norec . "'";
        //     $ob->icd10 = '';
        // }
        // $norecaPd = substr($norecaPd, 1, strlen($norecaPd) - 1);
        $diagnosa = [];
        $dataDiagnosa =[];
        $dataICD9=[];
        // if ($norecaPd != '') {
            // $dataDiagnosa = DB::select(DB::raw("
            //               select dg.kddiagnosa,ddp.daftarpasienruanganfk  as norec_apd,apd.registrasipasienfk as norec
            //                from diagnosapasienicdxtr as ddp
            //                join  daftarpasienruangantr as apd on apd.norec= ddp.daftarpasienruanganfk
                         
            //                left join icdxmt as dg on ddp.icdxidfk=dg.id
            //                where apd.registrasipasienfk in ($norecaPd) "));
        $dataDiagnosa = \DB::table('diagnosapasienicdxtr as ddp')
            ->join('daftarpasienruangantr as apd','apd.norec','=','ddp.daftarpasienruanganfk')
            ->join('registrasipasientr as pd','pd.norec','=','apd.registrasipasienfk')
            ->join('icdxmt as dg','ddp.icdxidfk','=','dg.id')
            ->select(DB::raw("dg.kddiagnosa,ddp.daftarpasienruanganfk  as norec_apd,apd.registrasipasienfk as norec"))
            ->where('pd.tglregistrasi', '>=', $filter['tglAwal'])
            ->where('pd.tglregistrasi', '<=', $filter['tglAkhir'])
            ->where('pd.koders',$kdProfile)
             ->where('pd.aktif',true);
        if (isset($filter['noreg']) && $filter['noreg'] != "" && $filter['noreg'] != "undefined") {
            $dataDiagnosa = $dataDiagnosa->where('pd.noregistrasi', 'ilike', '%' . $filter['noreg'] . '%');
        }
        $dataDiagnosa=$dataDiagnosa->get();

        $dataICD9 = \DB::table('diagnosapasienicdixtr as ddp')
        ->join('daftarpasienruangantr as apd','apd.norec','=','ddp.daftarpasienruanganfk')
        ->join('registrasipasientr as pd','pd.norec','=','apd.registrasipasienfk')
        ->join('icdxmt as dg','ddp.icdixidfk','=','dg.id')
        ->select(DB::raw("dg.kddiagnosa,ddp.daftarpasienruanganfk  as norec_apd,apd.registrasipasienfk as norec"))
        ->where('pd.tglregistrasi', '>=', $filter['tglAwal'])
        ->where('pd.tglregistrasi', '<=', $filter['tglAkhir'])
        ->where('pd.koders',$kdProfile)
        ->where('pd.aktif',true);
        if (isset($filter['noreg']) && $filter['noreg'] != "" && $filter['noreg'] != "undefined") {
            $dataICD9 = $dataICD9->where('pd.noregistrasi', 'ilike', '%' . $filter['noreg'] . '%');
        }
        $dataICD9=$dataICD9->get();
            
            // $dataICD9 = DB::select(DB::raw("
            //               select dg.kddiagnosa,ddp.daftarpasienruanganfk  as norec_apd,apd.registrasipasienfk as norec
            //                from diagnosapasienicdixtr as ddp
            //                join daftarpasienruangantr as apd on apd.norec= ddp.daftarpasienruanganfk
            //                left join icdixmt as dg on ddp.icdixidfk=dg.id
            //                where apd.registrasipasienfk in ($norecaPd) "));


        // }
        $set1 = explode(',', $this->settingDataFixed('discharge_status_1', $kdProfile));
        $ds1 = [];
        foreach ($set1 as $set11) {
            $ds1 [] = (int)$set11;
        }
        $set2 = explode(',', $this->settingDataFixed('discharge_status_2', $kdProfile));
        $ds2 = [];
        foreach ($set2 as $set22) {
            $ds2 [] = (int)$set22;
        }
        $set3 = explode(',', $this->settingDataFixed('discharge_status_3', $kdProfile));
        $ds3 = [];
        foreach ($set3 as $set33) {
            $ds3 [] = (int)$set33;
        }
        $set4 = explode(',', $this->settingDataFixed('discharge_status_4', $kdProfile));
        $ds4 = [];
        foreach ($set4 as $set44) {
            $ds4 [] = (int)$set44;
        }
        foreach ($data as $item){
            $dtdt = '';
            $dtdt2 = '';
            $asalRujukan = '';
            $covid19_status_cd = '';

            foreach ($dataDiagnosa as $item2){
                if ($item2->norec == $data[$i]->norec){
                    $dtdt = $dtdt . '#' .  $item2->kddiagnosa;
                }
            }
            foreach ($dataICD9 as $item3){
                if ($item3->norec == $data[$i]->norec){
                    $dtdt2  = $dtdt2 . '#' .  $item3->kddiagnosa;
                }
            }
            $data[$i]->icd9 = substr($dtdt2,1,strlen($dtdt2)-1);
            $data[$i]->icd10 = substr($dtdt,1,strlen($dtdt)-1);
            $data[$i]->codernik = $codernik;
            $data[$i]->objectasalrujukanfk =      $data[$i]->asalrujukanidfk;
            $data[$i]->kodetarif = $kodetarif;
            if(in_array($data[$i]->objectstatuspulangfk,$ds1) ){
                $data[$i]->discharge_status = 1;
            }else  if(in_array($data[$i]->objectstatuspulangfk,$ds2) ){
                $data[$i]->discharge_status = 2;
            }else  if(in_array($data[$i]->objectstatuspulangfk,$ds3) ){
                $data[$i]->discharge_status = 3;
            }else  if(in_array($data[$i]->objectstatuspulangfk,$ds4) ){
                $data[$i]->discharge_status = 4;
            }else{
                $data[$i]->discharge_status = 5;
            }

            $i= $i + 1 ;
        }
//        dd($data);
        $i = 0 ;
        $dtdt = '';
//        $dataICD9 = \DB::table('diagnosatindakanpasien_t as dpa')
//            ->join('detaildiagnosatindakanpasien_t as dp', 'dpa.norec', '=', 'dp.objectdiagnosatindakanpasienfk')
//            ->join('diagnosatindakan_m as dg', 'dg.id', '=', 'dp.objectdiagnosatindakanfk')
//            ->join('antrianpasiendiperiksa_t as apd', 'apd.norec', '=', 'dpa.objectpasienfk')
//            ->join('pasiendaftar_t as pd', 'pd.norec', '=', 'apd.noregistrasifk')
//            ->select('dg.kddiagnosatindakan','pd.norec')
////            ->where('apd.noregistrasifk',$data[$i]->norec)
//            ->where('pd.tglregistrasi', '>=', $filter['tglAwal'])
//            ->where('pd.tglregistrasi', '<=', $filter['tglAkhir'])
//            ->where('pd.kdprofile',$kdProfile)
//            ->get();
//        foreach ($data as $item){
//            $dtdt = '';
//
//            foreach ($dataICD9 as $item2){
//                if ($item2->norec == $data[$i]->norec) {
//                    $dtdt = $dtdt . '#' . $item2->kddiagnosatindakan;
//                }
//            }
//            $data[$i]->icd9 = substr($dtdt,1,strlen($dtdt)-1);
//            $i= $i + 1 ;
//        }
//dd($data);
        $tglawalawal = $filter['tglAwal'];
        $tglakhirakhir = $filter['tglAkhir'];
        $kelompokPasien=$filter['kelId'];
        $noregs ='' ;
        $norms ='' ;
        $namas ='' ;
        if (isset($filter['noreg']) && $filter['noreg'] != "" && $filter['noreg'] != "undefined") {
            $noregs = " and pd.noregistrasi='$filter[noreg]'";
        }
        if (isset($filter['norm']) && $filter['norm'] != "" && $filter['norm'] != "undefined") {
            $norms = " and ps.norm='$filter[norm]'";
        }
        if (isset($filter['nama']) && $filter['nama'] != "" && $filter['nama'] != "undefined") {
            $namas = " and ps.namapasien ilike '%".$filter['nama']."%'";
        }
        $dataTarif16 = DB::select(DB::raw("select pd.norec, sum(((pp.hargajual - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah)+ case when pp.jasa is null then 0 else pp.jasa end) as ttl,kpb.namaexternal
            from registrasipasientr as pd
            inner join pasienmt as ps on ps.id = pd.normidfk
            INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
            INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
            INNER JOIN pelayananmt as pr on pr.id=pp.produkidfk
            INNER JOIN kelompokprodukbpjsmt as kpb on kpb.id=pr.kelompokprodukbpjsidfk
            left join kelompokpasienmt as kp on kp.id = pd.kelompokpasienlastidfk
            where pd.aktif=true
            and pd.koders=$kdProfile
            and pd.tglregistrasi >= '$tglawalawal' and pd.tglregistrasi <= '$tglakhirakhir'  and kp.id in ($kelompokPasien)
            $noregs 
            $norms 
            $namas 
            group  by pd.norec,kpb.namaexternal order by pd.norec")
        );
        $i = 0 ;
        $prosedur_non_bedah ='';
        $prosedur_bedah ='';
        $konsultasi ='';
        $tenaga_ahli ='';
        $keperawatan ='';
        $penunjang ='';
        $radiologi ='';
        $laboratorium ='';
        $pelayanan_darah ='';
        $rehabilitasi ='';
        $kamar ='';
        $rawat_intensif ='';
        $obat ='';
        $obat_kronis ='';
        $obat_kemoterapi ='';
        $alkes ='';
        $bmhp ='';
        $sewa_alat ='';
        foreach ($data as $item){
            $norecpd= $data[$i]->norec;
            foreach ($dataTarif16 as $itm){
                if ($itm->norec == $norecpd){
//                    $data[$i]->tarif_rs->($dataTarif16[0]->namaexternal) => (float)$dataTarif16[0]->ttl,
                    if ($itm->namaexternal == 'prosedur_non_bedah'){
                        $prosedur_non_bedah = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'prosedur_bedah'){
                        $prosedur_bedah = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'konsultasi'){
                        $konsultasi = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'tenaga_ahli'){
                        $tenaga_ahli = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'keperawatan'){
                        $keperawatan = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'penunjang'){
                        $penunjang = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'radiologi'){
                        $radiologi = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'laboratorium'){
                        $laboratorium = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'pelayanan_darah'){
                        $pelayanan_darah = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'rehabilitasi'){
                        $rehabilitasi = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'kamar'){
                        $kamar = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'rawat_intensif'){
                        $rawat_intensif = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'obat'){
                        $obat = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'obat_kronis'){
                        $obat_kronis = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'obat_kemoterapi'){
                        $obat_kemoterapi = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'alkes'){
                        $alkes = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'bmhp'){
                        $bmhp = (float)$itm->ttl;
                    }
                    if ($itm->namaexternal == 'sewa_alat'){
                        $sewa_alat = (float)$itm->ttl;
                    }
                }
            }

            $datatatat = array(
                'prosedur_non_bedah' => (float)$prosedur_non_bedah,
                'prosedur_bedah' => (float)$prosedur_bedah,
                'konsultasi' => (float)$konsultasi,
                'tenaga_ahli' => (float)$tenaga_ahli,
                'keperawatan' => (float)$keperawatan,
                'penunjang' => (float)$penunjang,
                'radiologi' => (float)$radiologi,
                'laboratorium' => (float)$laboratorium,
                'pelayanan_darah' => (float)$pelayanan_darah,
                'rehabilitasi' => (float)$rehabilitasi,
                'kamar' => (float)$kamar,
                'rawat_intensif' => (float)$rawat_intensif,
                'obat' => (float)$obat,
                'obat_kronis' => (float)$obat_kronis,
                'obat_kemoterapi' => (float)$obat_kemoterapi,
                'alkes' => (float)$alkes,
                'bmhp' => (float)$bmhp,
                'sewa_alat' => (float)$sewa_alat,
            );
            $prosedur_non_bedah =0;
            $prosedur_bedah =0;
            $konsultasi =0;
            $tenaga_ahli =0;
            $keperawatan =0;
            $penunjang =0;
            $radiologi =0;
            $laboratorium =0;
            $pelayanan_darah =0;
            $rehabilitasi =0;
            $kamar =0;
            $rawat_intensif =0;
            $obat =0;
            $obat_kronis =0;
            $obat_kemoterapi =0;
            $alkes =0;
            $bmhp =0;
            $sewa_alat =0;
            $data[$i]->tarif_rs = $datatatat;

            $i= $i + 1 ;
//            $dataTarif16 = DB::select(DB::raw("select kpb.id,kpb.namaexternal,sum(x.total) as ttl
//                from kelompokprodukbpjs_m kpb
//                left JOIN
//                (select (pp.hargajual - pp.hargadiscount) * pp.jumlah as total ,pr.objectkelompokprodukbpjsfk
//                from produk_m as pr
//                INNER JOIN pelayananpasien_t as pp on pr.id=pp.produkfk
//                INNER JOIN antrianpasiendiperiksa_t as apd on pp.noregistrasifk=apd.norec
//                INNER JOIN pasiendaftar_t as pd on apd.noregistrasifk=pd.norec
//                where pd.norec='$norecpd' ) as x
//                on x.objectkelompokprodukbpjsfk=kpb.id
//                where kpb.id <> 0
//                group by kpb.id,kpb.namaexternal
//                order by kpb.id")
//            );



        }

//        return $this->respond(array(
//            'data1' => $dataTarif16,
//            'data2' =>$data,
//        ));
        return $this->respond($data);
    }
    public function saveBridgingINACBG(Request $request)
    {
        $data  = \DB::table('settingdatafixedmt')
            ->select('namafield','nilaifield')
            ->where('kelompok',"INACBG's")
            ->get();
//        return $data;
        foreach ($data as $item){
            if ($item->namafield == 'codernik'){
                $codernik = $item->nilaifield;
            }
            if ($item->namafield == 'key'){
                $key = $item->nilaifield;
            }
            if ($item->namafield == 'url'){
                $url = $item->nilaifield;
            }
        }

//        $key = "1fa5106f46eedd6d536bb816f4dc23728e0ca1dee3db20904e634134b205b28d";//        $url = "localhost/E-Klaim/ws.php";

        $dataReq = $request['data'];
        $responseArr = [];
        foreach ($dataReq as $dataLoop){
            $json_request = json_encode($dataLoop);
            $payload = $this->inacbg_encrypt($json_request,$key);
            $header = array("Content-Type: application/x-www-form-urlencoded");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            if ($err) {
                return $this->setStatusCode(400)->respond($err, $err);
            }
            $first  = strpos($response, "\n")+1;
            $last   = strrpos($response, "\n")-1;
            $response  = substr($response,
                $first,
                strlen($response) - $first - $last);
            $response = $this->inacbg_decrypt($response,$key);
            $responseArr[] =array(
                'datarequest' => $dataLoop,
                'dataresponse' =>   $response
            );
        }
        $result = array(
            "status" => 201,
            "dataresponse" => $responseArr,
            "as" => 'slvR',
        );
//        return $this->respond($responseArr);
        return $this->setStatusCode($result['status'])->respond($result, "Bridging InaCBG");
    }
    // Encryption Function
    function inacbg_encrypt($data, $key) {
        /// make binary representasion of $key
        $key = hex2bin($key);
        /// check key length, must be 256 bit or 32 bytes
        if (mb_strlen($key, "8bit") !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        /// create initialization vector
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($iv_size);
        // dengan catatan dibawah
        /// encrypt
        $encrypted = openssl_encrypt($data,
            "aes-256-cbc",
            $key,
            OPENSSL_RAW_DATA,
            $iv );
        /// create signature, against padding oracle attacks
        $signature = mb_substr(hash_hmac("sha256",
            $encrypted,
            $key,
            true),0,10,"8bit");
        /// combine all, encode, and format
        $encoded = chunk_split(base64_encode($signature.$iv.$encrypted));
        return $encoded;
    }
    // Decryption Function
    function inacbg_decrypt($str, $strkey){
        /// make binary representation of $key
        $key = hex2bin($strkey);
        /// check key length, must be 256 bit or 32 bytes
        if (mb_strlen($key, "8bit") !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        /// calculate iv size
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        /// breakdown parts
        $decoded = base64_decode($str);
        $signature = mb_substr($decoded,0,10,"8bit");
        $iv = mb_substr($decoded,10,$iv_size,"8bit");
        $encrypted = mb_substr($decoded,$iv_size+10,NULL,"8bit");
        /// check signature, against padding oracle attack
        $calc_signature = mb_substr(hash_hmac("sha256",
            $encrypted,
            $key,
            true),0,10,"8bit");
        if($this->inacbg_compare($signature,$calc_signature)) {
//            return "SIGNATURE_NOT_MATCH"; /// signature doesn't match
        }
        $decrypted = openssl_decrypt($encrypted,
            "aes-256-cbc",
            $key,
            OPENSSL_RAW_DATA,
            $iv);
        $dtdtd = json_decode($decrypted);
        return $dtdtd;
    }
    /// Compare Function
    function inacbg_compare($a, $b) {
        /// compare individually to prevent timing attacks
        /// compare length
        if (strlen($a) !== strlen($b)) return false;
        /// compare individual
        $result = 0;
        for($i = 0; $i < strlen($a); $i ++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $result == 0;
    }
    public function saveProposiBridgingINACBG(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $noregistrasifk=$request['noregistrasifk'];
            $totaldijamin=$request['totalDijamin'];
            $biayanaikkelas=$request['biayaNaikkelas'];
            $b="";
            $c="";
            $d=[];
            if($request['totalDijamin']!=null){
                $delete = DB::table('hasilgroupingtr')
                    ->where("registrasipasienfk",$request['noregistrasifk'])
                    ->delete();

                $noregistrasifk_apd = DaftarPasienRuangan::where('registrasipasienfk', $request['noregistrasifk'])->first();
                $noregistrasifk_apd=$noregistrasifk_apd['norec'];
                $kdPodukDeposit =$this->settingDataFixed('idProdukDeposit',$kdProfile);
                $dataTotalBill = DB::select(DB::raw("select sum(((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah) + case when pp.jasa is null then 0 else pp.jasa end) as total
                        from registrasipasientr as pd
                        INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                        INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                        where pd.koders = $kdProfile and pd.norec=:noregistrasi and pp.produkidfk not in ($kdPodukDeposit) ;
                    "),
                    array(
                        'noregistrasi' => $noregistrasifk ,
                    )
                );
                $TotalBiayaRS = $dataTotalBill[0]->total;


                $TotalBiayaPerTindakan =DB::select(DB::raw("select pp.norec,
                     (((case when pp.hargajual is null then 0 else pp.hargajual  end - case when pp.hargadiscount is null then 0 else pp.hargadiscount end) * pp.jumlah)
                     + case when pp.jasa is null then 0 else pp.jasa end) as total
                        from registrasipasientr as pd
                        INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                        INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                        where pd.koders = $kdProfile and pd.norec=:noregistrasi and pp.produkidfk not in ($kdPodukDeposit) ;
                    "),
                    array(
                        'noregistrasi' => $noregistrasifk ,
                    )
                );

                foreach ($TotalBiayaPerTindakan as $item){
                    if ($item->total != 0){

                        $piutangpenjamin = ($item->total / $TotalBiayaRS)*$totaldijamin;

                        $a=ceil($piutangpenjamin);

                        $dt=TransaksiPasien::where('norec', $item->norec)
                            ->update([
                                    'piutangpenjamin' => $a]
                            );
                    }
                    $TotalPiutangPenjamin = DB::select(DB::raw("SELECT
                            SUM (pp.piutangpenjamin) AS totalpiutangpenjamin
                        FROM
                            transaksipasientr AS pp
                        INNER JOIN daftarpasienruangantr AS apd ON pp.daftarpasienruanganfk = apd.norec
                        INNER JOIN registrasipasientr AS pd ON apd.registrasipasienfk = pd.norec
                        WHERE
                            pd.norec  = '$noregistrasifk'")
                    );
                    foreach ($TotalPiutangPenjamin as $item){
                        $TotalPiutangPenjamin = $item->totalpiutangpenjamin;
                    }
                    $norecupdate = DB::select(DB::raw("select transaksipasientr.piutangpenjamin,
                     transaksipasientr.norec
                    FROM
                        transaksipasientr
                    INNER JOIN daftarpasienruangantr ON transaksipasientr.daftarpasienruanganfk = daftarpasienruangantr.norec
                    INNER JOIN registrasipasientr ON daftarpasienruangantr.registrasipasienfk = registrasipasientr.norec
                    WHERE
                        registrasipasientr.norec = '$noregistrasifk'
                    ORDER BY
                        transaksipasientr.piutangpenjamin DESC
                    limit 1"));
                    foreach ($norecupdate as $item){
                        $norecupdate = $item->norec;
                        $piutangpenjamintindakan = ceil($item->piutangpenjamin);
                    }

                    if($TotalPiutangPenjamin>$totaldijamin){
                        $d=$TotalPiutangPenjamin-$totaldijamin;
                        $piutangpenjamintindakan=$piutangpenjamintindakan-$d;

                        $dt=TransaksiPasien::where('norec',$norecupdate)
                            ->update([
                                'piutangpenjamin' => $piutangpenjamintindakan
                            ]);
                    }
                    if($TotalPiutangPenjamin<$totaldijamin){
                        $d=$totaldijamin-$TotalPiutangPenjamin;
                        $piutangpenjamintindakan=$piutangpenjamintindakan+$d;

                        $dt=TransaksiPasien::where('norec',$norecupdate)
                            ->update([
                                'piutangpenjamin' => $piutangpenjamintindakan
                            ]);
                    }


                }
                $new = new HasilGrouping();
                $new->norec =  $new->generateNewId();
                $new->registrasipasienfk = $noregistrasifk;
                $new->totalbiayars = $TotalBiayaRS;
                $new->totalpiutangpenjamin = $totaldijamin;
                $new->biayanaikkelas = $biayanaikkelas;
                $new->save();
            }
            $transStatus = 'true';
            $transMessage = "Simpan Proposi Berhasil...! ";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Gagal Simpan Data ...! ";
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
                "e" => $e->getMessage(),
            );
        }


        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function savePengajuanKlaim(Request $request){
        DB::beginTransaction();
        try {

            $dt=PemakaianAsuransi::where('norec', $request['norec_pa'])
                // ->where('statusenabled')
                ->update([
                        'nosep' => $request['claim_number'],
                        'statuscovid' => 1,
                        'loscovid' => $request['loscovid']]
                );

            $transStatus = 'true';
            $transMessage = "Simpan Nomor Pengajuan Klaim Berhasil...! ";

        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Gagal Simpan Pengajuan Klaim ...! ";
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

        // return 'sukses';
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
}
