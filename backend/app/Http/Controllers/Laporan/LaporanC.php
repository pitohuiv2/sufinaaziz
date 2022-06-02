<?php


namespace App\Http\Controllers\Laporan;
use App\Http\Controllers\ApiController;
use App\Traits\PelayananPasienTrait;
use App\Traits\Valet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanC  extends ApiController{
    use Valet, PelayananPasienTrait;

    public function __construct(){
        parent::__construct($skip_authentication = false);
    }

    public function getDataComboLaporan(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
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

        $dataInstalasiIgd = \DB::table('instalasimt as dp')
            ->where('dp.aktif', true)
            ->where('dp.id', 3)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namadepartemen')
            ->get();

        $dataRuanganIgd = \DB::table('ruanganmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.instalasiidfk', 3)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        foreach ($dataInstalasiIgd as $item) {
            $detail = [];
            foreach ($dataRuanganIgd as $item2) {
                if ($item->id == $item2->instalasiidfk) {
                    $detail[] = array(
                        'id' => $item2->id,
                        'ruangan' => $item2->namaruangan,
                    );
                }
            }

            $dataDepartemenigd[] = array(
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

        $dataJenisDiagnosa = \DB::table('jenisdiagnosamt as kl')
            ->select('kl.id', 'kl.jenisdiagnosa')
            ->where('kl.aktif', true)
            ->orderBy('kl.id')
            ->get();

        $dataInstalasiRajal = \DB::table('instalasimt as dp')
            ->where('dp.aktif', true)
            ->where('dp.id', 1)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namadepartemen')
            ->get();

        $dataRuanganRajal = \DB::table('ruanganmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.instalasiidfk', 1)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        foreach ($dataInstalasiRajal as $item) {
            $detail = [];
            foreach ($dataRuanganRajal as $item2) {
                if ($item->id == $item2->instalasiidfk) {
                    $detail[] = array(
                        'id' => $item2->id,
                        'ruangan' => $item2->namaruangan,
                    );
                }
            }

            $dataDepartemenRajal[] = array(
                'id' => $item->id,
                'departemen' => $item->namadepartemen,
                'ruangan' => $detail,
            );
        }

        $result = array(
            'departemen' => $dataDepartemen,
            'departemenigd' => $dataDepartemenigd,
            'departemenrajal' => $dataDepartemenRajal,
            'kelompokpasien' => $dataKelompok,
            'dokter' => $dataDokter,
            'jenisdiagnosa' => $dataJenisDiagnosa,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDataLaporanKunjungan(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);
        $tglAwal = $request['tglAwal'];
        $tglAkhir = $request['tglAkhir'];
        $bulan = $request['bulan'];
        $paramDept = "";
        if (isset($request['idDept']) && $request['idDept'] != "" && $request['idDept'] != "undefined") {
            $paramDept = " AND ru.instalasiidfk = " . $request['idDept'];
        }

        $paramRuangan = "";
        if (isset($request['idRuangan']) && $request['idRuangan'] != "" && $request['idRuangan'] != "undefined") {
            $paramRuangan = " AND dpr.ruanganidfk = " . $request['idRuangan'];
        }

        $paramDokter = "";
        if (isset($request['idDokter']) && $request['idDokter'] != "" && $request['idDokter'] != "undefined") {
            $paramDokter = " AND dpr.pegawaiidfk = " . $request['idDokter'];
        }

        $data = collect(DB::select("
             SELECT x.kddiagnosa,x.namadiagnosa,COUNT(x.kddiagnosa) AS jmldg,SUM(x.laki) AS laki,SUM(x.cwe) AS cwe,SUM(x.baru) AS baru,SUM(x.lama) AS lama,
                         SUM(x.umum) AS umum,SUM(x.bpjs) AS bpjs,SUM(x.nolsatu) AS nolsatu,SUM(x.duasepuluh) AS duasepuluh,
                         SUM(x.tujuhbelas) AS tujuhbelas,SUM(x.delapanbelas) AS delapanbelas,SUM(x.enampuluh) AS enampuluh,
                         SUM(x.rujuk) AS rujuk,SUM(x.dirawat) AS dirawat,SUM(x.pulang) AS pulang,			 
                         SUM(x.laki)+SUM(x.cwe)+SUM(x.baru)+SUM(x.lama)+SUM(x.umum)+SUM(x.bpjs)+SUM(x.nolsatu)+SUM(x.duasepuluh)+
                         SUM(x.tujuhbelas)+SUM(x.delapanbelas)+SUM(x.enampuluh)+SUM(x.rujuk)+SUM(x.dirawat)+SUM(x.pulang) AS jumlah
            FROM
            (SELECT icd.kddiagnosa,icd.namadiagnosa,
                         CASE WHEN pm.jeniskelaminidfk = 1 THEN 1 ELSE 0 END AS laki,
                         CASE WHEN pm.jeniskelaminidfk = 2 THEN 1 ELSE 0 END AS cwe,
                         CASE WHEN pd.statuspasien = 'BARU' THEN 1 ELSE 0 END AS baru,
                         CASE WHEN pd.statuspasien = 'LAMA' THEN 1 ELSE 0 END AS lama,
                         CASE WHEN pd.kelompokpasienlastidfk <> 2 THEN 1 ELSE 0 END AS umum,
                         CASE WHEN pd.kelompokpasienlastidfk = 2 THEN 1 ELSE 0 END AS bpjs,
                         CASE WHEN EXTRACT(YEAR FROM age(pd.tglregistrasi,pm.tgllahir)) BETWEEN 0 AND 1 THEN 1 ELSE 0 END AS nolsatu,
                         CASE WHEN EXTRACT(YEAR FROM age(pd.tglregistrasi,pm.tgllahir)) BETWEEN 2 AND 10 THEN 1 ELSE 0 END AS duasepuluh,
                         CASE WHEN EXTRACT(YEAR FROM age(pd.tglregistrasi,pm.tgllahir)) BETWEEN 11 AND 17 THEN 1 ELSE 0 END AS tujuhbelas,
                         CASE WHEN EXTRACT(YEAR FROM age(pd.tglregistrasi,pm.tgllahir)) BETWEEN 18 AND 60 THEN 1 ELSE 0 END AS delapanbelas,
                         CASE WHEN EXTRACT(YEAR FROM age(pd.tglregistrasi,pm.tgllahir)) > 60 THEN 1 ELSE 0 END AS enampuluh,
                         CASE WHEN dpr.statuskeluaridfk = 4 THEN 1 ELSE 0 END AS rujuk,
                         CASE WHEN dpr.statuskeluaridfk in (2,6) THEN 1 ELSE 0 END AS dirawat,
                         CASE WHEN dpr.statuskeluaridfk in (1,3) OR dpr.statuskeluaridfk IS NULL THEN 1 ELSE 0 END AS pulang
            FROM registrasipasientr AS pd
            INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = pd.norec
            LEFT JOIN rujukanasalmt AS ra ON ra. ID = pd.asalrujukanidfk
            LEFT JOIN pasienmt AS pm ON pm. ID = pd.normidfk
            LEFT JOIN jeniskelaminmt AS jk ON jk. ID = pm.jeniskelaminidfk
            LEFT JOIN diagnosapasienicdxtr AS dgp ON dgp.daftarpasienruanganfk = dpr.norec
            AND dgp.jenisdiagnosaidfk = 1
            LEFT JOIN icdxmt AS icd ON icd. ID = dgp.icdxidfk
            LEFT JOIN ruanganmt AS ru1 ON ru1. ID = pd.ruanganlastidfk
            LEFT JOIN ruanganmt AS ru ON ru. ID = dpr.ruanganidfk
            LEFT JOIN pegawaimt AS pg ON pg. ID = dpr.pegawaiidfk
            LEFT JOIN statuskeluarmt AS sk ON sk.id = dpr.statuskeluaridfk
            WHERE pd.koders = 1 AND pd.aktif = TRUE
            AND to_char(dpr.tglmasuk,'MM-YYYY') = '$bulan'
              --BETWEEN '$tglAwal' AND '$tglAkhir'
            $paramDept
            $paramRuangan
            $paramDokter
            ) AS x
            GROUP BY x.kddiagnosa,x.namadiagnosa
            ORDER BY x.kddiagnosa,x.namadiagnosa
        "));

        return $this->respond($data);
    }

    public function getDataLaporanSensusBulananIGD(Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);
        $tglAwal = $request['tglAwal'];
        $tglAkhir = $request['tglAkhir'];
        $bulan = $request['bulan'];
        $paramsidDept = "";
        if (isset($request['idDept']) && $request['idDept'] != "" && $request['idDept'] != "undefined") {
            $paramsidDept = " AND ru.instalasiidfk = " . $request['idDept'];
        }
        $paramsidTipePasien = "";
        if (isset($request['idTipePasien']) && $request['idTipePasien'] != "" && $request['idTipePasien'] != "undefined") {
            $paramsidTipePasien = " AND rg.kelompokpasienlastidfk = " . $request['idTipePasien'];
        }
        if (isset($request['nosbm']) && $request['nosbm'] != "" && $request['nosbm'] != "undefined") {
            $data = $data->where('sbm.nosbm', 'ilike', '%' . $request['nosbm'] . '%');
        }

        $paramRuangan = '';
        if(isset($request['listRuang']) && $request['listRuang']!="" && $request['listRuang']!="undefined") {
            $arrRuang = explode(',', $request['listRuang']);
            $kodeRuang = [];
            $str = '';
            $d = 0;
            foreach ($arrRuang as $item) {
                if ($str == '') {
                    $str = $item;
                } else {
                    $str = $str . ',' . $item;
                }
                $d = $d + 1;
            }
            $paramRuangan = " AND rg.ruanganlastidfk IN ($str)";
        }

        $data = collect(DB::select("
               SELECT rg.norec,rg.noregistrasi,rg.tglregistrasi,pm.norm,pm.namapasien,to_char(dpr.tglmasuk,'DD-MM-YYYY') AS tglmasuk,
                      CASE WHEN pm.jeniskelaminidfk = 1 THEN 1 ELSE 0 END AS jk_l,
                      CASE WHEN pm.jeniskelaminidfk = 2 THEN 1 ELSE 0 END AS jk_p,
                      alm.alamatlengkap || ' Kel. ' || CASE WHEN alm.namadesakelurahan IS NULL THEN '' ELSE alm.namadesakelurahan END || ' Kec. ' 
                      || CASE WHEN alm.namakecamatan IS NULL THEN '' ELSE alm.namakecamatan END || ' Kab. '
                      || CASE WHEN alm.namakotakabupaten IS NULL THEN '' ELSE alm.namakotakabupaten END 
                      || ' Prov. ' || CASE WHEN alm.provinsiidfk IS NULL THEN ' ' ELSE prov.namapropinsi END AS alamatlengkap,
                      EXTRACT(YEAR FROM AGE(rg.tglregistrasi, pm.tgllahir)) AS umur,ru.namaruangan,kp.kelompokpasien,
                      CASE WHEN dpr.asalrujukanidfk = 6 THEN 1 ELSE 0 END AS poli,
                      CASE WHEN dpr.asalrujukanidfk IN (1,2,3,4) THEN 1 ELSE 0 END AS rujukan,
                      CASE WHEN dpr.asalrujukanidfk = 5 THEN 1 ELSE 0 END AS sendiri,
                      CASE WHEN rg.kelompokpasienlastidfk = 2 THEN 1 ELSE 0 END AS bpjs,
                      CASE WHEN rg.kelompokpasienlastidfk <> 2 THEN 1 ELSE 0 END AS umum,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (39) AND pm.jeniskelaminidfk = 1 THEN 1 ELSE 0 END AS dl,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (39) AND pm.jeniskelaminidfk = 2 THEN 1 ELSE 0 END AS dp,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (51) THEN 1 ELSE 0 END AS ok,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (50) THEN 1 ELSE 0 END AS bedah,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (40) THEN 1 ELSE 0 END AS neuro,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (42,44,45,46,48) THEN 1 ELSE 0 END AS anak,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (54,41,49) THEN 1 ELSE 0 END AS iso,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (1,2,6) THEN 1 ELSE 0 END AS diizinkan,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (4,5,10,11) THEN 1 ELSE 0 END AS dirujuk,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (3) THEN 1 ELSE 0 END AS lari,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (12) THEN 1 ELSE 0 END AS pulpak,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (10) THEN 1 ELSE 0 END AS doa,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (7) THEN 1 ELSE 0 END AS enamjam,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (5,6,8) THEN 1 ELSE 0 END AS duaempatjam,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (9) THEN 1 ELSE 0 END AS empatdelapanjam
               FROM registrasipasientr AS rg
               INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec
               INNER JOIN pasienmt AS pm ON pm.id = rg.normidfk 
               LEFT JOIN kelompokpasienmt AS kp ON kp.id = rg.kelompokpasienlastidfk 
               LEFT JOIN ruanganmt AS ru ON ru.id = dpr.ruanganidfk 
               LEFT JOIN ruanganmt AS ru1 ON ru1.id = dpr.ruanganasalidfk 
               LEFT JOIN alamatmt AS alm ON alm.normidfk = pm.id 
               LEFT JOIN provinsimt AS prov ON prov.id = alm.provinsiidfk 
               WHERE rg.koders = $kdProfile
               AND rg.aktif = TRUE
               -- AND rg.tglregistrasi BETWEEN '$tglAwal' AND '$tglAkhir'
               AND to_char(dpr.tglmasuk,'MM-YYYY') = '$bulan'
               $paramsidDept
               $paramsidTipePasien
               $paramRuangan
               ORDER BY rg.noregistrasi ASC
        "));

        $norecPd = '';
        $noregistrasi = '';
        foreach ($data as $ob){
            $norecPd = $norecPd.",'".$ob->norec . "'";
            $noregistrasi = $noregistrasi . ",'".$ob->noregistrasi. "'";
            $ob->kddiagnosa = [];
        }
        $norecPd = substr($norecPd, 1, strlen($norecPd)-1);
        $noregistrasi = substr($noregistrasi, 1,strlen($noregistrasi)-1);
        $diagnosa = [];
        if($norecPd != ''){
            $diagnosa = collect(DB::select("
                SELECT dg.kddiagnosa,ddp.registrasipasienfk as norec_rg,dg.namadiagnosa
                FROM diagnosapasienicdxtr as ddp
                LEFT JOIN icdxmt as dg on ddp.icdxidfk = dg.id
                where ddp.registrasipasienfk in ($norecPd)
            "));
            $i = 0;
            foreach ($data as $h){
                $data[$i]->kddiagnosa = [];
                $data[$i]->namadiagnosa = [];
                foreach ($diagnosa as $d){
                    if($data[$i]->norec == $d->norec_rg){
                        $data[$i]->kddiagnosa [] = $d->kddiagnosa . "-" .$d->namadiagnosa;
                        $data[$i]->namadiagnosa [] = $d->namadiagnosa;
                    }
                }
                $i++;
            }
        }
        return $this->respond($data);
    }

    public function getDataLaporanSensusPasien(Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);
        $tglAwal = $request['tglAwal'];
        $tglAkhir = $request['tglAkhir'];
        $tglAyeuna = date('d-m-Y');

        $paramsidDept = "";
        if (isset($request['idDept']) && $request['idDept'] != "" && $request['idDept'] != "undefined") {
            $paramsidDept = " AND ru1.instalasiidfk = " . $request['idDept'];
        }
        $paramsidTipePasien = "";
        if (isset($request['idTipePasien']) && $request['idTipePasien'] != "" && $request['idTipePasien'] != "undefined") {
            $paramsidTipePasien = " AND rg.kelompokpasienlastidfk = " . $request['idTipePasien'];
        }
        $paramRuangan = '';
        if(isset($request['listRuang']) && $request['listRuang']!="" && $request['listRuang']!="undefined") {
            $arrRuang = explode(',', $request['listRuang']);
            $kodeRuang = [];
            $str = '';
            $d = 0;
            foreach ($arrRuang as $item) {
                if ($str == '') {
                    $str = $item;
                } else {
                    $str = $str . ',' . $item;
                }
                $d = $d + 1;
            }
            $paramRuangan = " AND dpr.ruanganidfk IN ($str)";
        }

        $data = collect(DB::select("
               SELECT dpr.norec AS norec_dpr,rg.norec,rg.noregistrasi,rg.tglregistrasi,pm.norm,pm.namapasien,dpr.tglmasuk,
                      CASE WHEN pm.jeniskelaminidfk = 1 THEN 1 ELSE 0 END AS jk_l,
                      CASE WHEN pm.jeniskelaminidfk = 2 THEN 1 ELSE 0 END AS jk_p,
                      CASE WHEN rg.statuspasien = 'BARU' THEN 1 ELSE 0 END AS baru,
                      CASE WHEN rg.statuspasien = 'LAMA' THEN 1 ELSE 0 END AS lama,
                      alm.alamatlengkap || ' Kel. ' || CASE WHEN alm.namadesakelurahan IS NULL THEN '' ELSE alm.namadesakelurahan END || ' Kec. ' 
                      || CASE WHEN alm.namakecamatan IS NULL THEN '' ELSE alm.namakecamatan END || ' Kab. '
                      || CASE WHEN alm.namakotakabupaten IS NULL THEN '' ELSE alm.namakotakabupaten END 
                      || ' Prov. ' || CASE WHEN alm.provinsiidfk IS NULL THEN ' ' ELSE prov.namapropinsi END AS alamatlengkap,
                      EXTRACT(YEAR FROM AGE(rg.tglregistrasi, pm.tgllahir)) || ' Thn '
                      || EXTRACT(MONTH FROM AGE(rg.tglregistrasi, pm.tgllahir)) || ' Bln '
                      || EXTRACT(DAY FROM AGE(rg.tglregistrasi, pm.tgllahir)) || ' Hr' AS umur,
                      ru.namaruangan,kp.kelompokpasien,'' AS tekanandarah,'' AS suhu,'' AS nadi,'' AS pernafasan,
                      '' AS beratbadan,'' AS tinggibadan,
                      CASE WHEN rg.tglmeninggal IS NULL THEN '' ELSE
                      CASE WHEN EXTRACT(MINUTE from (AGE(rg.tglmeninggal,rg.tglregistrasi))) <= 360 THEN 
                      'Meninggal < 6 - ' || to_char(rg.tglmeninggal, 'DD-MM-YYY HH24:MI:SS') Else '' END END AS tglmeninggal,
                      '' AS keluhan,'' AS terapi,sk.statuskeluar
               FROM registrasipasientr AS rg
               INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec
               INNER JOIN pasienmt AS pm ON pm.id = rg.normidfk
               LEFT JOIN kelompokpasienmt AS kp ON kp.id = rg.kelompokpasienlastidfk
               LEFT JOIN ruanganmt AS ru ON ru.id = rg.ruanganlastidfk
               LEFT JOIN ruanganmt AS ru1 ON ru1.id = dpr.ruanganidfk 
               LEFT JOIN alamatmt AS alm ON alm.normidfk = pm.id
               LEFT JOIN provinsimt AS prov ON prov.id = alm.provinsiidfk
               LEFT JOIN statuskeluarmt AS sk ON sk.id = dpr.statuskeluaridfk
               WHERE rg.koders = $kdProfile
               AND rg.aktif = TRUE
               AND dpr.tglmasuk BETWEEN '$tglAwal' AND '$tglAkhir'               
               $paramsidDept
               $paramsidTipePasien
               $paramRuangan
               ORDER BY rg.noregistrasi ASC
        "));

        $norecPd = '';
        $noregistrasi = '';
        $norecDpr = '';
        foreach ($data as $ob){
            $norecPd = $norecPd.",'".$ob->norec . "'";
            $noregistrasi = $noregistrasi . ",'".$ob->noregistrasi. "'";
            $norecDpr = $norecDpr . ",'".$ob->norec_dpr. "'";
            $ob->kddiagnosa = [];
        }
        $norecPd = substr($norecPd, 1, strlen($norecPd)-1);
        $noregistrasi = substr($noregistrasi, 1,strlen($noregistrasi)-1);
        $norecDpr = substr($norecDpr, 1,strlen($norecDpr)-1);
        $diagnosa = [];
        if($norecPd != ''){
            $diagnosa = collect(DB::select("
                SELECT dg.kddiagnosa,ddp.registrasipasienfk as norec_rg,dg.namadiagnosa
                FROM diagnosapasienicdxtr as ddp
                LEFT JOIN icdxmt as dg on ddp.icdxidfk = dg.id
                where ddp.registrasipasienfk in ($norecPd)
            "));
            $i = 0;
            foreach ($data as $h){
                $data[$i]->kddiagnosa = [];
                $data[$i]->namadiagnosa = [];
                foreach ($diagnosa as $d){
                    if($data[$i]->norec == $d->norec_rg){
                        $data[$i]->kddiagnosa [] = $d->kddiagnosa . "-" .$d->namadiagnosa;
                        $data[$i]->namadiagnosa [] = $d->namadiagnosa;
                    }
                }
                $i++;
            }
        }
        $vitalsign = [];
        $soap = [];
        if ($norecDpr != ''){
            $vitalsign = collect(DB::select("
                SELECT emrp.norec_apd,emrp.tglemr,emrpd.emrdfk,emrpd.value			 
                FROM emrpasientr AS emrp 
                LEFT JOIN emrpasiendtr AS emrpd ON emrpd.emrpasienfk = emrp.noemr
                INNER JOIN emrdmt AS emrd ON emrd.id = emrpd.emrdfk
                WHERE emrp.koders = 1 AND emrp.aktif = true AND emrpd.aktif = true
                AND emrp.emrfk = 210080
                AND emrp.norec_apd in ($norecDpr)
                ORDER BY emrp.noregistrasifk ASC
            "));
            $j = 0;
            foreach ($data as $h){
                foreach ($vitalsign as $d){
                    if($data[$j]->norec_dpr == $d->norec_apd){
                        if ($d->emrdfk == 21040345){
                            $data[$j]->tekanandarah = $d->value;
                        }
                        if ($d->emrdfk == 21040348){
                            $data[$j]->suhu = $d->value;
                        }
                        if ($d->emrdfk == 21040349){
                            $data[$j]->nadi = $d->value;
                        }
                        if ($d->emrdfk == 21040350){
                            $data[$j]->pernafasan = $d->value;
                        }
                        if ($d->emrdfk == 21040347){
                            $data[$j]->beratbadan = $d->value;
                        }
                        if ($d->emrdfk == 21040346){
                            $data[$j]->tinggibadan = $d->value;
                        }
                    }
                }
                $j++;                 
            }

            $k = 0;
            $soap = collect(DB::select("
                SELECT str.daftarpasienruanganfk,str.s,str.o,str.a,str.p
                FROM soaptr AS str
                INNER JOIN daftarpasienruangantr AS dpr ON dpr.norec = str.daftarpasienruanganfk
                WHERE str.daftarpasienruanganfk IN ($norecDpr)
            "));
            foreach ($data as $e){
                $data[$k]->keluhan = '';
                $data[$k]->terapi = '';
                foreach ($soap as $a){
                    if($data[$k]->norec_dpr == $a->daftarpasienruanganfk){                        
                        $data[$k]->keluhan = $a->s;                                                    
                        $data[$k]->terapi = $a->p;
                    }
                }                
                $k++;
            }              

            $dataResep = [];
            $result = [];
            foreach ( $data as $item){
                $result[] = array(
                    'norec' => $item->norec,
                    'noregistrasi' => $item->noregistrasi,
                    'tglregistrasi' => $item->tglregistrasi,
                    'tglmasuk' => $item->tglmasuk,
                    'norm' => $item->norm,
                    'namapasien' => $item->namapasien,
                    'jk_l' => $item->jk_l,
                    'jk_p' => $item->jk_p,
                    'baru' => $item->baru,
                    'lama' => $item->lama,
                    'alamatlengkap' => $item->alamatlengkap,
                    'umur' => $item->umur,
                    'namaruangan' => $item->namaruangan,
                    'kelompokpasien' => $item->kelompokpasien,
                    'tekanandarah' => $item->tekanandarah,
                    'suhu' => $item->suhu,
                    'nadi' => $item->nadi,
                    'pernafasan' => $item->pernafasan,
                    'beratbadan' => $item->beratbadan,
                    'tinggibadan' => $item->tinggibadan,
                    'kddiagnosa' => $item->kddiagnosa,
                    'namadiagnosa' => $item->namadiagnosa,
                    'keluhan' => $item->keluhan,
                    'terapi' => $item->terapi,
                    'bb_tb' => $item->beratbadan . ' / ' . $item->tinggibadan,
                    'statuspulang' => $item->statuskeluar,
                );
            }
            return $this->respond($result);
        }
    }

    public function getDataLaporanSensusHarianIGD(Request $request){
        $kdProfile = (int)$this->getDataKdProfile($request);
        $tglAwal = $request['tglAwal'];
        $tglAkhir = $request['tglAkhir'];
        $tglAyeuna = date('d-m-Y');

        $paramsidDept = "";
        if (isset($request['idDept']) && $request['idDept'] != "" && $request['idDept'] != "undefined") {
            $paramsidDept = " AND ru.instalasiidfk = " . $request['idDept'];
        }
        $paramsidTipePasien = "";
        if (isset($request['idTipePasien']) && $request['idTipePasien'] != "" && $request['idTipePasien'] != "undefined") {
            $paramsidTipePasien = " AND rg.kelompokpasienlastidfk = " . $request['idTipePasien'];
        }
        if (isset($request['nosbm']) && $request['nosbm'] != "" && $request['nosbm'] != "undefined") {
            $data = $data->where('sbm.nosbm', 'ilike', '%' . $request['nosbm'] . '%');
        }

        $paramRuangan = '';
        if(isset($request['listRuang']) && $request['listRuang']!="" && $request['listRuang']!="undefined") {
            $arrRuang = explode(',', $request['listRuang']);
            $kodeRuang = [];
            $str = '';
            $d = 0;
            foreach ($arrRuang as $item) {
                if ($str == '') {
                    $str = $item;
                } else {
                    $str = $str . ',' . $item;
                }
                $d = $d + 1;
            }
            $paramRuangan = " AND rg.ruanganlastidfk IN ($str)";
        }

        $data = collect(DB::select("
               SELECT rg.norec,rg.noregistrasi,rg.tglregistrasi,pm.norm,pm.namapasien,
                      CASE WHEN pm.jeniskelaminidfk = 1 THEN 'L' WHEN pm.jeniskelaminidfk = 2 THEN 'P' ELSE '-' END AS jk,
                      alm.alamatlengkap || ' Kel. ' || CASE WHEN alm.namadesakelurahan IS NULL THEN '' ELSE alm.namadesakelurahan END || ' Kec. ' 
                      || CASE WHEN alm.namakecamatan IS NULL THEN '' ELSE alm.namakecamatan END || ' Kab. '
                      || CASE WHEN alm.namakotakabupaten IS NULL THEN '' ELSE alm.namakotakabupaten END 
                      || ' Prov. ' || CASE WHEN alm.provinsiidfk IS NULL THEN ' ' ELSE prov.namapropinsi END AS alamatlengkap,
                      EXTRACT(YEAR FROM AGE(rg.tglregistrasi, pm.tgllahir)) || ' Thn '
                      || EXTRACT(MONTH FROM AGE(rg.tglregistrasi, pm.tgllahir)) || ' Bln '
                      || EXTRACT(DAY FROM AGE(rg.tglregistrasi, pm.tgllahir)) || ' Hr' AS umur,
                      ru.namaruangan,kp.kelompokpasien,
                      CASE WHEN dpr.asalrujukanidfk = 6 THEN 1 ELSE 0 END AS poli,
                      CASE WHEN dpr.asalrujukanidfk IN (1,2,3,4) THEN 1 ELSE 0 END AS rujukan,
                      CASE WHEN dpr.asalrujukanidfk = 5 THEN 1 ELSE 0 END AS sendiri,
                      CASE WHEN rg.kelompokpasienlastidfk = 2 THEN 1 ELSE 0 END AS bpjs,
                      CASE WHEN rg.kelompokpasienlastidfk <> 2 THEN 1 ELSE 0 END AS umum,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (39) AND pm.jeniskelaminidfk = 1 THEN 1 ELSE 0 END AS dl,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (39) AND pm.jeniskelaminidfk = 2 THEN 1 ELSE 0 END AS dp,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (51) THEN 1 ELSE 0 END AS ok,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (50) THEN 1 ELSE 0 END AS bedah,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (40) THEN 1 ELSE 0 END AS neuro,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (42,44,45,46,48) THEN 1 ELSE 0 END AS anak,
                      CASE WHEN ru1.instalasiidfk = 3 AND ru.id in (54,41,49) THEN 1 ELSE 0 END AS iso,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (1,2,6) THEN 1 ELSE 0 END AS diizinkan,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (4,5,10,11) THEN 1 ELSE 0 END AS dirujuk,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (3) THEN 1 ELSE 0 END AS lari,
                      CASE WHEN dpr.statuskeluaridfk = 1 AND dpr.statuspulangidfk IN (12) THEN 1 ELSE 0 END AS pulpak,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (10) THEN 1 ELSE 0 END AS doa,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (7) THEN 1 ELSE 0 END AS enamjam,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (5,6,8) THEN 1 ELSE 0 END AS duaempatjam,
                      CASE WHEN dpr.statuskeluaridfk = 5 AND dpr.statuspulangidfk IN (9) AND dpr.kondisipasienidfk IN (9) THEN 1 ELSE 0 END AS empatdelapanjam
               FROM registrasipasientr AS rg
               INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec
               INNER JOIN pasienmt AS pm ON pm.id = rg.normidfk 
               LEFT JOIN kelompokpasienmt AS kp ON kp.id = rg.kelompokpasienlastidfk 
               LEFT JOIN ruanganmt AS ru ON ru.id = dpr.ruanganidfk 
               LEFT JOIN ruanganmt AS ru1 ON ru1.id = dpr.ruanganasalidfk 
               LEFT JOIN alamatmt AS alm ON alm.normidfk = pm.id 
               LEFT JOIN provinsimt AS prov ON prov.id = alm.provinsiidfk 
               WHERE rg.koders = $kdProfile
               AND rg.aktif = TRUE
               AND dpr.tglmasuk BETWEEN '$tglAwal' AND '$tglAkhir'
               $paramsidDept
               $paramsidTipePasien
               $paramRuangan
               ORDER BY rg.noregistrasi ASC
        "));

        $norecPd = '';
        $noregistrasi = '';
        foreach ($data as $ob){
            $norecPd = $norecPd.",'".$ob->norec . "'";
            $noregistrasi = $noregistrasi . ",'".$ob->noregistrasi. "'";
            $ob->kddiagnosa = [];
        }
        $norecPd = substr($norecPd, 1, strlen($norecPd)-1);
        $noregistrasi = substr($noregistrasi, 1,strlen($noregistrasi)-1);
        $diagnosa = [];
        if($norecPd != ''){
            $diagnosa = collect(DB::select("
                SELECT dg.kddiagnosa,ddp.registrasipasienfk as norec_rg,dg.namadiagnosa
                FROM diagnosapasienicdxtr as ddp
                LEFT JOIN icdxmt as dg on ddp.icdxidfk = dg.id
                where ddp.registrasipasienfk in ($norecPd)
            "));
            $i = 0;
            foreach ($data as $h){
                $data[$i]->kddiagnosa = [];
                $data[$i]->namadiagnosa = [];
                foreach ($diagnosa as $d){
                    if($data[$i]->norec == $d->norec_rg){
                        $data[$i]->kddiagnosa [] = $d->kddiagnosa . "-" .$d->namadiagnosa;
                        $data[$i]->namadiagnosa [] = $d->namadiagnosa;
                    }
                }
                $i++;
            }
        }
        return $this->respond($data);
    }
}
