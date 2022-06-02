<?php

namespace App\Http\Controllers\EIS;

use App\Http\Controllers\ApiController;
// use App\Master\Departemen;
use App\Master\Instalasi;
// use App\Master\JenisIndikator;
// use App\Master\KelompokTransaksi;
// use App\Master\Pasien;
// use App\Master\SettingDataFixed;
// use App\Master\TargetIndikator;
// use App\Transaksi\IndikatorPasienJatuh;
// use App\Transaksi\IndikatorRensar;
// use App\Transaksi\IndikatorRensarDetail;
// use App\Transaksi\PasienDaftar;
// use App\Transaksi\RegistrasiPasien;
// use App\Transaksi\PelayananPasien;
// use App\Transaksi\TransaksiPasien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Transaksi\BPJSKlaimTxt;
use App\Traits\Valet;
use Jimmyjs\ReportGenerator\ReportMedia\PdfReport;

class EISC extends ApiController
{
    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getDashboard(Request $r)
    {
        $kdProfile = $this->getDataKdProfile($r);
        $result['toprajal'] = $this->getTopRajal($r['dari'], $r['sampai'], $kdProfile);
        $result['ranapjk'] = $this->getRanapJK($r['dari'], $r['sampai'], $kdProfile);
        $result['borlostoi'] = $this->getBorLosToi($r['dari'], $r['sampai'], $kdProfile);
        $result['tt_usia'] = $this->getTempatTidurTerpakai($r['dari'], $r['sampai'], $kdProfile);
        $result['kunjungan_perjenispasien'] = $this->getKunjunganRSPerJenisPasien($r['dari'], $r['sampai'], $kdProfile);
        $result['topdiagnosa'] = $this->getTopDiagnosa($r['dari'], $r['sampai'], $kdProfile);
        $result['trendkunjungan'] = $this->trendKunjungan($kdProfile);
        return $this->respond($result);
    }

    public function trendKunjungan($kdProfile)
    {
        $minggu = Carbon::now()->subDay(14)->format('Y-m-d');
        $tanggal = Carbon::now()->addDay(1)->format('Y-m-d');

        $begin = new \DateTime($minggu);
        $end = new \DateTime($tanggal);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $set = explode(',', $this->settingDataFixed('kdDeptEis', $kdProfile));
        $kdDept = [];
        foreach ($set as $s) {
            $kdDept [] = (int)$s;
        }
        $deptDa = Instalasi::whereIN('id', $kdDept)->where('aktif', true)->get();
//        dd($deptDa);
        $i = 0;
        $color = ['#FF6384', '#4BC0C0', '#FFCE56', '#36A2EB'];
        $chart = [];
        foreach ($deptDa as $d) {
            $array2 = [];
            foreach ($period as $dt) {
                $array2[] = array(
                    'tgl' => $dt->format("Y-m-d"),
                );
            }
            $chart [] = array(
                'label' => $d->namadepartemen,
                'backgroundColor' => $color[$i],
                'borderColor' => '#1E88E5',
                'data' => [],
                'detailtgl' => $array2
            );
            $i++;
        }
//        dd($chart);
//
        $cat = [];
        foreach ($period as $dt) {
            $cat[] = $dt->format("d F Y");
        }
//
//        foreach ($chart as $ch){
//
//        }
//        $data = DB::table('registrasipasientr as rps')
//            ->join('ruanganmt as ru','ru.id','=','rp.ruanganlastidfk')
//            ->join('instalasimt as ins','ins.id','=','ru.instalasiidfk')
//            ->select(DB::raw("to_char(rp.tglregistrasi,'yyyy-mm-dd') as tgl,ins.namadepartemen"))
//            ->whereBetween('rp.tglregistrasi', [$minggu, $tanggal])
//            ->where('rp.aktif',true)
//            ->groupby('ins.namadepartemen')
//            ->get();
        $data = DB::select(DB::raw("
            select x.tgl,count(x.namadepartemen) as jml,x.namadepartemen from (SELECT
                to_char(
                    rp.tglregistrasi,
                    'yyyy-mm-dd'
                ) AS tgl,
                ins.namadepartemen
            FROM
                registrasipasientr AS rp
            INNER JOIN ruanganmt AS ru ON ru.id = rp.ruanganlastidfk
            INNER JOIN instalasimt AS ins ON ins.id = ru.instalasiidfk
            WHERE
                rp.tglregistrasi BETWEEN '$minggu'
            AND '$tanggal'
            AND rp.aktif = true
            ) as x
            GROUP BY x.tgl,x.namadepartemen"));
        $i = 0;
        foreach ($chart as $c) {
            foreach ($data as $d) {
                if ($c['label'] == $d->namadepartemen) {
                    foreach ($c['detailtgl'] as $t) {
                        $chart[$i]['data'][] = 0;
                        if ($t['tgl'] == $d->tgl) {
                            $chart[$i]['data'][] = (float)$d->jml;
                        }
                    }
                    break;
                }
            }
            $i++;

        }
        $data2 = [];
        $sama = false;

//            foreach ($array as $value) {
//                $sama = false;
//                foreach ($data as $value2) {
//
//                            if ($value['tgl'] == $value2->tgl) {
//                                $data2[] = [
//                                    'tgl' => $value['tgl'],
//                                    'tglstring' => $value['tglstring'],
//                                    'jml' => (float)$value2->jml,
//                                ];
//                                $sama = true;
//
//                            }
//
//                            if ($sama == false) {
//                                $data2[] = [
//                                    'tgl' => $value['tgl'],
//                                    'tglstring' => $value['tglstring'],
//                                    'jml' => $value['jml'],
//                                ];
//                            }
//
//                }
//            }

//        {
//            label: 'Tingkat 1',
//            backgroundColor: '#FF6384',
//            borderColor: '#1E88E5',
//            data: seriesTKT1
//          }
        $res['labels'] = $cat;
        $res['datasets'] = $chart;
        return $res;
    }
    public function getTempatTidurTerpakai($tglAwal, $tglAkhir, $kdProfile)
    {
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $paramDep = '';
        $data = DB::select(DB::raw("SELECT
                pd.noregistrasi,
                pd.tglregistrasi,
                ru.namaruangan,
                jk.jeniskelamin,
                ps.jeniskelaminidfk as objectjeniskelaminfk,
              date_part('year',age(ps.tgllahir))as umur,
                date_part('day',now()-ps.tgllahir)as hari
            FROM
                registrasipasientr AS pd
            LEFT JOIN pasienmt AS ps ON ps.id = pd.normidfk
            LEFT JOIN jeniskelaminmt AS jk ON jk.id = ps.jeniskelaminidfk
            LEFT JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk 
            WHERE pd.koders = $kdProfile 
                $paramDep 
                 and pd.aktif = true
                and (pd.tglregistrasi < '$tglAwal' AND pd.tglpulang >= '$tglAkhir' 
                )
                or pd.tglpulang is null and pd.koders=$kdProfile
                and pd.aktif = true
                $paramDep "));
        $L_balita = 0;
        $P_balita = 0;
        $L_anak = 0;
        $P_anak = 0;
        $L_remajaAwal = 0;
        $P_remajaAwal = 0;
        $L_remajaAkhir = 0;
        $P_remajaAkhir = 0;
        $L_dewasaAwal = 0;
        $P_dewasaAwal = 0;
        $L_dewasaAkhir = 0;
        $P_dewasaAkhir = 0;
        $L_lansiaAwal = 0;
        $P_lansiaAwal = 0;
        $L_lansiaakhir = 0;
        $P_lansiaakhir = 0;
        $L_manula = 0;
        $P_manula = 0;
        $jmlAll = 0;


        foreach ($data as $item) {
            $jmlAll = $jmlAll + 1;
            //DATA KEMENKES
            //1.Balita= 0 –5
            //2.Anak = 6 –11
            //3. Remaja Awal= 12 -16
            //4.Remaja Akhir= 17 –25
            //5.Dewasa Awal= 26 –35 .
            //6.Dewasa Akhir= 36 –45
            //7.Lansia Awal= 46 –55.
            //8.Lansia Akhir= 56 –65.
            //9.Manula= 65 –atas
            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 0 && (float)$item->umur <= 5) {
                $L_balita = (float)$L_balita + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 0 && (float)$item->umur <= 5) {
                $P_balita = (float)$P_balita + 1;
            }
            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 6 && (float)$item->umur <= 11) {
                $L_anak = (float)$L_anak + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 6 && (float)$item->umur <= 11) {
                $P_anak = (float)$P_anak + 1;
            }
            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 12 && (float)$item->umur <= 16) {
                $L_remajaAwal = (float)$L_remajaAwal + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 12 && (float)$item->umur <= 16) {
                $P_remajaAwal = (float)$P_remajaAwal + 1;
            }
            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 17 && (float)$item->umur <= 25) {
                $L_remajaAkhir = (float)$L_remajaAkhir + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 17 && (float)$item->umur <= 25) {
                $P_remajaAkhir = (float)$P_remajaAkhir + 1;
            }

            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 26 && (float)$item->umur <= 35) {
                $L_dewasaAwal = (float)$L_dewasaAwal + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 26 && (float)$item->umur <= 35) {
                $P_dewasaAwal = (float)$P_dewasaAwal + 1;
            }
            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 36 && (float)$item->umur <= 45) {
                $L_dewasaAkhir = (float)$L_dewasaAkhir + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 36 && (float)$item->umur <= 45) {
                $P_dewasaAkhir = (float)$P_dewasaAkhir + 1;
            }

            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 46 && (float)$item->umur <= 55) {
                $L_lansiaAwal = (float)$L_lansiaAwal + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 46 && (float)$item->umur <= 55) {
                $P_lansiaAwal = (float)$P_lansiaAwal + 1;
            }
            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur >= 56 && (float)$item->umur <= 65) {
                $L_lansiaakhir = (float)$L_lansiaakhir + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur >= 56 && (float)$item->umur <= 65) {
                $P_lansiaakhir = (float)$P_lansiaakhir + 1;
            }

            if ($item->objectjeniskelaminfk == 1 && (float)$item->umur > 65) {
                $L_manula = (float)$L_manula + 1;
            }
            if ($item->objectjeniskelaminfk == 2 && (float)$item->umur > 65) {
                $P_manula = (float)$P_manula + 1;
            }

        }

        $resultData = array(
            'jumlah' => count($data),
            'L_balita' => $L_balita,
            'P_balita' => $P_balita,
            'L_anak' => $L_anak,
            'P_anak' => $P_anak,
            'L_remajaAwal' => $L_remajaAwal,
            'P_remajaAwal' => $P_remajaAwal,
            'L_remajaAkhir' => $L_remajaAkhir,
            'P_remajaAkhir' => $P_remajaAkhir,
            'L_dewasaAwal' => $L_dewasaAwal,
            'P_dewasaAwal' => $P_dewasaAwal,
            'L_dewasaAkhir' => $L_dewasaAkhir,
            'P_dewasaAkhir' => $P_dewasaAkhir,
            'L_lansiaAwal' => $L_lansiaAwal,
            'P_lansiaAwal' => $P_lansiaAwal,
            'L_lansiaakhir' => $L_lansiaakhir,
            'P_lansiaakhir' => $P_lansiaakhir,
            'L_manula' => $L_manula,
            'P_manula' => $P_manula,
            'all' => $jmlAll,

        );
        //DATA KEMENKES
        //1.Balita= 0 –5
        //2.Anak = 6 –11
        //3. Remaja Awal= 12 -16
        //4.Remaja Akhir= 17 –25
        //5.Dewasa Awal= 26 –35 .
        //6.Dewasa Akhir= 36 –45
        //7.Lansia Awal= 46 –55.
        //8.Lansia Akhir= 56 –65.
        //9.Manula= 65 –atas
        $result [] = ['name' => 'Balita Perempuan', 'umur' => '0 - 5 Tahun', 'jml' => $P_balita, 'img' => 'assets/layout/images/usia/BayiPerempuan.png'];
        $result [] = ['name' => 'Balita Laki-laki', 'umur' => '0 - 5 Tahun', 'jml' => $L_balita, 'img' => 'assets/layout/images/usia/BayiLaki-Laki.png'];
        $result [] = ['name' => 'Anak Perempuan', 'umur' => '6 - 11 Tahun', 'jml' => $P_anak, 'img' => 'assets/layout/images/usia/AnakPerempuan.png'];
        $result [] = ['name' => 'Anak Laki-laki', 'umur' => '6 - 11 Tahun', 'jml' => $L_anak, 'img' => 'assets/layout/images/usia/AnakLaki-Laki.png'];
        $result [] = ['name' => 'Remaja Perempuan', 'umur' => '12 - 25 Tahun', 'jml' => $P_remajaAwal + $P_remajaAkhir, 'img' => 'assets/layout/images/usia/RemajaPerempuan.png'];
        $result [] = ['name' => 'Remaja Laki-laki', 'umur' => '12 - 25 Tahun', 'jml' => $L_remajaAwal + $L_remajaAkhir, 'img' => 'assets/layout/images/usia/RemajaLaki-Laki.png'];
        $result [] = ['name' => 'Dewasa Perempuan', 'umur' => '26 - 45 Tahun', 'jml' => $P_dewasaAwal + $P_dewasaAkhir, 'img' => 'assets/layout/images/usia/DewasaPerempuan.png'];
        $result [] = ['name' => 'Dewasa Laki-laki', 'umur' => '26 - 45 Tahun', 'jml' => $L_dewasaAwal + $L_dewasaAkhir, 'img' => 'assets/layout/images/usia/DewasaLaki-Laki.png'];
        $result [] = ['name' => 'Lansia Perempuan', 'umur' => '46 - 65 Tahun', 'jml' => $P_lansiaAwal + $P_lansiaakhir, 'img' => 'assets/layout/images/usia/Nenek2.png'];
        $result [] = ['name' => 'Lansia Laki-laki', 'umur' => '46 - 65 Tahun', 'jml' => $L_lansiaAwal + $L_lansiaakhir, 'img' => 'assets/layout/images/usia/Kakek2.png'];
        $result [] = ['name' => 'Manula Perempuan', 'umur' => '> 65 Tahun', 'jml' => $P_manula, 'img' => 'assets/layout/images/usia/Nenek.png'];
        $result [] = ['name' => 'Manula Laki-laki', 'umur' => '> 65 Tahun', 'jml' => $L_manula, 'img' => 'assets/layout/images/usia/Kakek.png'];

//        dd($result);
        return $result;
    }
    public function getKunjunganRSPerJenisPasien($tglAwal, $tglAkhir, $kdProfile)
    {
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $dataALL = DB::select(DB::raw("SELECT dp.id ,dp.kelompokpasien as name,count(pd.norec) as count
                FROM kelompokpasienmt dp
                LEFT JOIN (SELECT rp.norec,rp.kelompokpasienlastidfk 
                FROM registrasipasientr as rp
                where tglregistrasi BETWEEN '$tglAwal' and '$tglAkhir' and rp.aktif=true
                and rp.koders=$kdProfile) pd ON (dp.id= pd.kelompokpasienlastidfk)
                WHERE
                 dp.koders =$kdProfile
                and dp.aktif =true
                group by dp.kelompokpasien,dp.id
                order by dp.kelompokpasien asc"));

        return $dataALL;
    }
    public function getTopDiagnosa($tglAwal, $tglAkhir, $kdProfile)
    {
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $data = DB::select(DB::raw("SELECT
                        *
                    FROM
                        (
                            SELECT
                                COUNT (x.kddiagnosa) AS count,
                                x.kddiagnosa,
                                x.namadiagnosa,
                                 x.kddiagnosa as name
                            FROM
                                (
                                    SELECT
                                        dm.kddiagnosa,
                                        dm.namadiagnosa
                                    FROM
                                        daftarpasienruangantr AS app
                                    join registrasipasientr  as pd on pd.norec = app.registrasipasienfk
                                    LEFT JOIN diagnosapasienicdxtr AS dp ON dp.daftarpasienruanganfk = app.norec
                                    LEFT JOIN icdxmt AS dm ON dm.id = dp.icdxidfk
                                    WHERE
                                        app.koders = $kdProfile
                                    AND dm.kddiagnosa <> '-'
                                    AND pd.aktif = TRUE
                                    AND pd.tglregistrasi BETWEEN '$tglAwal'
                                    AND '$tglAkhir'
                                ) AS x
                            GROUP BY
                                x.namadiagnosa,
                                x.kddiagnosa
                        ) AS z
                    ORDER BY
                        z.count DESC
                    LIMIT 10"));
        return $data;
    }
    public function getTopRajal($tglAwal, $tglAkhir, $kdProfile)
    {
        $deptJalan = $this->settingDataFixed('kdDepartemenRawatJalanFix', $kdProfile);

        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $data = DB::select(DB::raw("SELECT
                        *
                    FROM
                        (
                            SELECT
                                COUNT (x.namaruangan) AS count,
                                x.namaruangan as name
                            FROM
                                (
                                    SELECT
                                        dp.namaruangan
                                    FROM
                                     registrasipasientr  as pd
                                     JOIN ruanganmt AS dp ON dp.id = pd.ruanganlastidfk
                                    WHERE
                                        pd.koders = $kdProfile
                                    AND pd.aktif = TRUE  and dp.instalasiidfk in ($deptJalan)
                                    AND pd.tglregistrasi BETWEEN '$tglAwal'
                                    AND '$tglAkhir'
                                ) AS x
                            GROUP BY
                                x.namaruangan
                        ) AS z
                    ORDER BY
                        z.count DESC
                    LIMIT 10"));
        return $data;
    }
    public function getRanapJK($tglAwal, $tglAkhir, $kdProfile)
    {
        $dept = $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile);
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $data = DB::select(DB::raw("SELECT dp.id ,dp.jeniskelamin as name,count(pd.norec) as count
                FROM jeniskelaminmt dp
                LEFT JOIN (SELECT rp.norec, ps.jeniskelaminidfk
                FROM registrasipasientr as rp
                JOIN pasienmt AS ps ON ps.id = rp.normidfk
                JOIN ruanganmt AS ru ON ru.id = rp.ruanganlastidfk 
               WHERE rp.koders = $kdProfile
              and ru.instalasiidfk in ($dept) 
             and rp.aktif = true
            and (rp.tglregistrasi < '$tglAwal' AND rp.tglpulang >= '$tglAkhir' )
            or rp.tglpulang is null and rp.koders=$kdProfile
             and rp.aktif = true
               ) pd ON (dp.id= pd.jeniskelaminidfk)
                WHERE
                 dp.koders =$kdProfile
                and dp.aktif =true
                group by dp.jeniskelamin,dp.id
                order by dp.jeniskelamin asc"));


        return $data;
    }
    public function getBorLosToi($tglAwal,$tglAkhir,$kdProfile){

        $idProfile = (int) $kdProfile;
        $idDepRanap = $this->settingDataFixed('kdDepartemenRanapFix', $kdProfile);
        $idStatKelMeninggal = $this->settingDataFixed('KdStatKeluarMeninggal', $kdProfile);
        $idKondisiPasienMeninggal = $this->settingDataFixed('KdKondisiPasienMeninggal', $kdProfile);

        $tglAwal =$tglAwal.' 00:00';
        $tglAkhir = $tglAkhir.' 23:59';
        $tahun = new \DateTime($tglAkhir);
        $tahun = date ('Y');
        $datetime1 = new \DateTime($tglAwal);
        $datetime2 = new \DateTime($tglAkhir);
        $interval = $datetime1->diff($datetime2);
        $sehari = 1;//$interval->format('%d');
        $data10=[];
        $jumlahTT = collect(DB::select("SELECT
                    tt.id,
                    tt.statusbedidfk as objectstatusbedfk
            FROM
                    tempattidurmt AS tt
            INNER JOIN kamarmt AS kmr ON kmr.id = tt.kamaridfk
            INNER JOIN ruanganmt AS ru ON ru.id = kmr.ruanganidfk
            WHERE
                    tt.koders = $idProfile
            AND tt.aktif = true
            AND kmr.aktif = true
            "))->count();
        if($jumlahTT == 0){
            $data10[] = array(
                'lamarawat'=> 0 ,
                'hariperawatan' =>0,
                'pasienpulang' =>0,
                'meninggal' => 0,
                'matilebih48' =>  0,
                'tahun' => 0,
                'bulan' => date('d-M-Y') ,//(float)$item->bulanregis ,
                'bor' => 0,
                'alos' => 0,
                'bto' => 0,
                'toi' =>  0,
                'gdr' => 0,
                'ndr' =>  0,
            );

            return $data10;
        }

        $hariPerawatan = DB::select(DB::raw("
           SELECT   COUNT (x.noregistrasi) AS jumlahhariperawatan
            FROM
            (
                SELECT
                    pd.noregistrasi,
                    pd.tglpulang,
                    to_char ( pd.tglregistrasi,'mm') AS bulanregis
                FROM
                    registrasipasientr AS pd
                INNER JOIN ruanganmt AS ru ON ru.ID = pd.ruanganlastidfk
                WHERE
                    ru.instalasiidfk = $idDepRanap 
                        and pd.koders = $idProfile
            and (  (pd.tglregistrasi < '$tglAwal' AND pd.tglpulang >= '$tglAkhir' )
               or pd.tglpulang is null
            )
         
            and pd.aktif = true
            ) AS x"
        ));

        $lamaRawat = DB::select(DB::raw("
                        select sum(x.hari) as lamarawat, count(x.noregistrasi)as jumlahpasienpulang from (
                        SELECT 
                            date_part('DAY', pd.tglpulang- pd.tglregistrasi) as hari ,pd.noregistrasi
                            FROM registrasipasientr AS pd
                            INNER JOIN ruanganmt AS ru ON ru. ID = pd.ruanganlastidfk
                            WHERE pd.koders = $idProfile and
                            pd.tglpulang BETWEEN '$tglAwal'
                              AND '$tglAkhir' 
                            and pd.tglpulang is not null
                            and pd.aktif=true
                            and  ru.instalasiidfk = $idDepRanap 
                            GROUP BY pd.noregistrasi,pd.tglpulang,pd.tglregistrasi
                         -- order by pd.noregistrasi 
                      ) as x       
                "));


        $dataMeninggal = DB::select(DB::raw("select count(x.noregistrasi) as jumlahmeninggal, x.bulanregis,  
                count(case when x.kondisipasienidfk = $idKondisiPasienMeninggal then 1 end ) AS jumlahlebih48 FROM
                (
                select noregistrasi,to_char(tglregistrasi , 'mm')  as bulanregis ,statuskeluar,kondisipasien,kondisipasienidfk
                from registrasipasientr 
                join statuskeluarmt on statuskeluarmt.id =registrasipasientr.statuskeluaridfk
                left join kondisipasienmt on kondisipasienmt.id =registrasipasientr.kondisipasienidfk
                where registrasipasientr.koders = $idProfile and statuskeluaridfk = $idStatKelMeninggal
                and  tglregistrasi BETWEEN '$tglAwal' and '$tglAkhir'
                and registrasipasientr.aktif=true
                ) as x
                GROUP BY x.bulanregis;"));

//        return $this->respond($dataMeninggal);
        $year = Carbon::now()->year;
        $num_of_days = [];
        if($year == date('Y'))
            $total_month = date('m');
        else
            $total_month = 12;

        for($m=1; $m<=$total_month; $m++){
            $num_of_days[] = array(
                'bulan' =>  $m,
                'jumlahhari' =>  cal_days_in_month(CAL_GREGORIAN, $m, $year),
            );
        }
        $bor = 0;
        $alos = 0;
        $toi = 0;
        $bto = 0;
        $ndr = 0;
        $gdr = 0;
        $hariPerawatanJml = 0;
        $jmlPasienPlg = 0;
        $jmlLamaRawat = 0;
        $jmlMeninggal = 0;
        $jmlMatilebih48=0;
        foreach ($hariPerawatan as $item){
            foreach ($lamaRawat as $itemLamaRawat){
                foreach ($dataMeninggal as $itemDead) {
//                         if ($item->bulanregis == $itemLamaRawat->bulanpulang &&
//                             $itemLamaRawat->bulanpulang == $itemDead->bulanregis ) {
                    /** @var  $gdr = (Jumlah Mati dibagi Jumlah pasien Keluar (Hidup dan Mati) */
                    $gdr = (int) $itemDead->jumlahmeninggal * 1000 /(int)$itemLamaRawat->jumlahpasienpulang ;
                    /** @var  $NDR = (Jumlah Mati > 48 Jam dibagi Jumlah pasien Keluar (Hidup dan Mati) */
                    $ndr = (int) $itemDead->jumlahlebih48 * 1000 /(int)$itemLamaRawat->jumlahpasienpulang ;

                    $jmlMeninggal = (int) $itemDead->jumlahmeninggal ;
                    $jmlMatilebih48= (int) $itemDead->jumlahlebih48;
//                         }
                }
//                if ($item->bulanregis == $itemLamaRawat->bulanpulang ) {
                /** @var  $alos = (Jumlah Lama Dirawat dibagi Jumlah pasien Keluar (Hidup dan Mati) */
//                return $this->respond($itemLamaRawat->jumlahpasienpulang );
                if ( (int)$itemLamaRawat->jumlahpasienpulang > 0){
                    $alos = (int)$itemLamaRawat->lamarawat / (int)$itemLamaRawat->jumlahpasienpulang;
                }

                /** @var  $bto = Jumlah pasien Keluar (Hidup dan Mati) DIBAGI Jumlah tempat tidur */
                $bto = (int)$itemLamaRawat->jumlahpasienpulang / $jumlahTT;

//                }
//                foreach ($num_of_days as $numday){
//                    if ($numday['bulan'] == $item->bulanregis){
                /** @var  $bor = (Jumlah hari perawatn RS dibagi ( jumlah TT x Jumlah hari dalam satu periode ) ) x 100 % */
                $bor = ( (int)$item->jumlahhariperawatan * 100 / ($jumlahTT *  (float)$sehari ));//$numday['jumlahhari']));
                /** @var  $toi = (Jumlah TT X Periode) - Hari Perawatn DIBAGI Jumlah pasien Keluar (Hidup dan Mati)*/
//                        $toi = ( ( $jumlahTT * $numday['jumlahhari'] )- (int)$item->jumlahhariperawatan ) /(int)$itemLamaRawat->jumlahpasienpulang ;
                if ( (int)$itemLamaRawat->jumlahpasienpulang > 0){
                    $toi = ( ( $jumlahTT * (float)$sehari)- (int)$item->jumlahhariperawatan ) /(int)$itemLamaRawat->jumlahpasienpulang ;
                }
                $hariPerawatanJml = (int)$item->jumlahhariperawatan;
                $jmlPasienPlg = (int)$itemLamaRawat->jumlahpasienpulang;
//                    }
//                }
            }

            $data10[] = array(
                'lamarawat'=>(int)$itemLamaRawat->lamarawat,
                'hariperawatan' => $hariPerawatanJml,
                'pasienpulang' => $jmlPasienPlg,
                'meninggal' => $jmlMeninggal,
                'matilebih48' =>  $jmlMatilebih48,
                'tahun' => $tahun,
                'bulan' => date('d-M-Y') ,//(float)$item->bulanregis ,
                'bor' =>(float) number_format($bor,2),
                'alos' =>(float) number_format($alos,2),
                'bto' =>(float) number_format($bto,2),
                'toi' => (float)number_format($toi,2),
                'gdr' => (float)number_format($gdr,2),
                'ndr' => (float) number_format($ndr,2),
            );
        }

        return $data10;

    }
}
