<?php

namespace App\Http\Controllers;

use App\Datatrans\PasienDaftar;
use App\Datatrans\Pegawai;
use App\User;
use App\Traits\Valet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;

//use Storage;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Webpatser\Uuid\Uuid;

// use Illuminate\Contracts\Encryption\DecryptException;
// use Picqer;

date_default_timezone_set('Asia/Jakarta');

class MainController extends ApiController
{
    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = true);
    }

    public function show_page(Request $r)
    {
        $request = array('role' => $r->role, 'pages' => $r->pages, "id" => $r->id);
        $request = $this->validate_input($request);
        $compact = [];
        $kdProfile = $_SESSION['kdProfile'];
        if ($request['role'] == 'admin') {
            switch ($request["pages"]) {
                case 'dashboard':
//                    array_push($compact,"listdiagnosa","map","kddiagnosa","umur","tglawal","tglakhir");
                    break;

                case 'dashboard-pelayanan':
                    $tglawal = date('Y-m-d');
                    $tglakhir = date('Y-m-d');

                    if (!isset($r->tglawal) && !isset($r->tglakhir)) {

                        return redirect()->route("show_page",
                            ["role" => $_SESSION['role'],
                                "pages" => $r->pages,
                                "tglawal" => $tglawal,
                                "tglakhir" => $tglakhir]);
                    } else {
                        $tglawal = $r->tglawal;
                        $tglakhir = $r->tglakhir;
                    }

                    $res['pengunjung'] = $this->getPengunjung($tglawal, $tglakhir, $kdProfile);
                    $res['kunjungan'] = $this->getKunjungan($tglawal, $tglakhir, $kdProfile);
                    $res['trend_kunjungan'] = $this->getTrendKunjunganPasienRajal($tglawal, $tglakhir, $kdProfile);
                    // $res['jenis_penjadwalan'] = $this->getPasienPerjenisPenjadwalan($tglawal,$tglakhir,$kdProfile);
                    $res['info_kedatangan'] = $this->getInfoKunjunganRawatJalanPerhari($tglawal, $tglakhir, $kdProfile);
                    $res['kunjungan_perjenispasien'] = $this->getKunjunganRSPerJenisPasien($tglawal, $tglakhir, $kdProfile);
                    $res['tt_usia'] = $this->getTempatTidurTerpakai($tglawal, $tglakhir, $kdProfile);
                    $res['alamat'] = $this->getKotaMap();
                    $res['alamat'] = array_slice($res['alamat'], 0, 10);
                    $res['z'] = $this->getTempatTidurTerpakai($tglawal, $tglakhir, $kdProfile);

                    $i = 0;
                    foreach ($res['pengunjung'] as $k) {
                        $k->warna = $this->listWarna()[$i];
                        $k->gambar = $this->listGambar()[$i];
                        $k->namadepartemen = str_replace('Instalasi', 'Pengunjung ', $k->namadepartemen);
                        $i++;
                    }
                    $z = 0;
                    foreach ($res['kunjungan'] as $k) {
                        $res['kunjungan'][$z]['warna'] = $this->listWarna()[$z];
                        $res['kunjungan'][$z]['gambar'] = $this->listGambar()[$z];
                        $res['kunjungan'][$z]['namadepartemen'] = str_replace('Instalasi', 'Kunjungan ', $k['namadepartemen']);
                        $z++;
                    }

//                    dd($res);
                    array_push($compact, 'res', 'r');
                    break;
                case 'dashboard-pendapatan':
                    $tglawal = date('Y-m-d');
                    $tglakhir = date('Y-m-d');

                    if (!isset($r->tglawal) && !isset($r->tglakhir)) {
                        return redirect()->route("show_page",
                            ["role" => $_SESSION['role'],
                                "pages" => $r->pages,
                                "tglawal" => $tglawal,
                                "tglakhir" => $tglakhir]);
                    } else {
                        $tglawal = $r->tglawal;
                        $tglakhir = $r->tglakhir;
                    }
                    $res['pendapatan'] = $this->getPendapatanRumahSakit($tglawal, $tglakhir, $kdProfile, 'sehari');
//                    dd($res);
                    array_push($compact, 'res', 'r');
                    break;
                case 'dashboard-persediaan':
                    $tglawal = date('Y-m-d');
                    $tglakhir = date('Y-m-d');

                    if (!isset($r->tglawal) && !isset($r->tglakhir)) {
                        return redirect()->route("show_page",
                            ["role" => $_SESSION['role'],
                                "pages" => $r->pages,
                                "tglawal" => $tglawal,
                                "tglakhir" => $tglakhir]);
                    } else {
                        $tglawal = $r->tglawal;
                        $tglakhir = $r->tglakhir;
                    }
                    $res['obat'] = $this->getLaporanPemakaianObat($r);
                    $res['stok'] = $this->getInfoStok($r);

                    $res['trend'] = $this->getTrendPemakaianObat($r);
//                    dd(  $res);
                    array_push($compact, 'res', 'r');
                    break;
                case 'dashboard-sdm':
                    $tglawal = date('Y-m-d');
                    $tglakhir = date('Y-m-d');

                    if (!isset($r->tglawal) && !isset($r->tglakhir)) {
                        return redirect()->route("show_page",
                            ["role" => $_SESSION['role'],
                                "pages" => $r->pages,
                                "tglawal" => $tglawal,
                                "tglakhir" => $tglakhir]);
                    } else {
                        $tglawal = $r->tglawal;
                        $tglakhir = $r->tglakhir;
                    }

                    $res['pegawai'] = $this->getCountPegawai($r);
                    $aktif = 0;
                    $nonaktif = 0;
                    foreach ($res['pegawai']['statuspegawai'] as $s) {
                        if ($s->statuspegawai == 'Aktif') {
                            $aktif = $aktif + (float)$s->total;
                        } else {
                            $nonaktif = $nonaktif + (float)$s->total;
                        }
                    }
                    $res['pegawai']['aktif'] = $aktif;
                    $res['pegawai']['nonaktif'] = $nonaktif;
//                    dd($res['pegawai']);
                    array_push($compact, 'res', 'r');
                    break;
                case 'dashboard-backoffice':
                    $tglawal = date('Y-m-d');
                    $tglakhir = date('Y-m-d');

                    if (!isset($r->tglawal) && !isset($r->tglakhir)) {
                        return redirect()->route("show_page",
                            ["role" => $_SESSION['role'],
                                "pages" => $r->pages,
                                "tglawal" => $tglawal,
                                "tglakhir" => $tglakhir]);
                    } else {
                        $tglawal = $r->tglawal;
                        $tglakhir = $r->tglakhir;
                    }

                    $res = [];
                    array_push($compact, 'res', 'r');
                    break;
                case 'dashboard-tt':
                    $tglawal = date('Y-m-d');
                    $tglakhir = date('Y-m-d');

                    if (!isset($r->tglawal) && !isset($r->tglakhir)) {
                        return redirect()->route("show_page",
                            ["role" => $_SESSION['role'],
                                "pages" => $r->pages,
                                "tglawal" => $tglawal,
                                "tglakhir" => $tglakhir]);
                    } else {
                        $tglawal = $r->tglawal;
                        $tglakhir = $r->tglakhir;
                    }
                    $kdProfile = $_SESSION["kdProfile"];

                    $data = collect(DB::select("
                          SELECT
                            x.namaruangan,
                            SUM (x.isi) AS isi,
                            SUM (x.kosong) AS kosong,
                        count(x.tt_id) as total
                        FROM
                            (
                                SELECT
                                    CAST (tt.nomorbed AS INT) AS nomor,
                                    tt. ID AS tt_id,
                                    tt.nomorbed AS namabed,
                                    kmr. ID AS kmr_id,
                                    kmr.namakamar,
                                    ru. ID AS id_ruangan,
                                    ru.namaruangan,
                                    kls.namakelas,
                                    sb.statusbed,
                                    CASE 	WHEN sb. ID = 1 THEN	1	ELSE 0 END AS isi,
                                CASE WHEN sb. ID = 2 THEN	1	ELSE 0	END AS kosong
                            FROM
                                tempattidurmt AS tt
                            INNER JOIN statusbedmt AS sb ON sb. ID = tt.statusbedidfk
                            INNER JOIN kamarmt AS kmr ON kmr. ID = tt.kamaridfk
                            INNER JOIN kelasmt AS kls ON kls. ID = kmr.kelasidfk
                            INNER JOIN ruanganmt AS ru ON ru. ID = kmr.ruanganidfk
                            WHERE
                                tt.koders = $kdProfile
                            AND tt.aktif = TRUE
                            AND kmr.aktif = TRUE
                            ) AS x
                        GROUP BY
                            x.namaruangan
                        
                        "));
                    $totalKamar = $data->count();
                    $totalBed = 0;
                    $totalIsi = 0;
                    $totalKosong = 0;
                    foreach ($data as $item) {
                        $totalBed = $totalBed + (float)$item->total;
                        $totalIsi = $totalIsi + (float)$item->isi;
                        $totalKosong = $totalKosong + (float)$item->kosong;
                    }

                    $tt = collect(DB::select("SELECT
                        ru.id AS idruangan,
                        ru.namaruangan,
                        km.id AS idkamar,
                        km.namakamar,
                        tt.id AS idtempattidur,
                        tt.reportdisplay,
                        tt.nomorbed,
                        sb.id AS idstatusbed,
                        sb.statusbed,
                        kl.id AS idkelas,
                        kl.namakelas
                    FROM
                        tempattidurmt AS tt
                    INNER JOIN statusbedmt AS sb ON sb. ID = tt.statusbedidfk
                    INNER JOIN kamarmt AS km ON km. ID = tt.kamaridfk
                    INNER JOIN kelasmt AS kl ON kl. ID = km.kelasidfk
                    INNER JOIN ruanganmt AS ru ON ru. ID = km.ruanganidfk
                    WHERE
                         ru.aktif = true
                    AND km.aktif = true
                    AND tt.aktif = true
                    AND tt.koders = $kdProfile"));

                    $data10 = [];
                    $sama = false;
                    $bed = 0;
                    $isi = 0;
                    $kosong = 0;
                    foreach ($tt as $item) {
                        $sama = false;
                        $i = 0;
                        foreach ($data10 as $hideung) {
                            if ($item->namaruangan == $data10[$i]['namaruangan']) {
                                $sama = 1;
                                $jml = (float)$hideung['bed'] + 1;
                                $data10[$i]['bed'] = $jml;
                                if ($item->idstatusbed == 1) {
                                    $data10[$i]['isi'] = (float)$hideung['isi'] + 1;
                                }
                                if ($item->idstatusbed == 2) {
                                    $data10[$i]['kosong'] = (float)$hideung['kosong'] + 1;
                                }
                            }
                            $i = $i + 1;
                        }
                        if ($sama == false) {
                            if ($item->idstatusbed == 1) {
                                $isi = 1;
                                $kosong = 0;
                            }
                            if ($item->idstatusbed == 2) {
                                $isi = 0;
                                $kosong = 1;
                            }

                            $data10[] = array(
                                'idruangan' => $item->idruangan,
                                'namaruangan' => $item->namaruangan,
                                'idstatusbed' => $item->idstatusbed,
                                'bed' => 1,
                                'kosong' => $kosong,
                                'isi' => $isi,
                            );
                        }
                    }

                    $res['totalKamar'] = $totalKamar;
                    $res['totalBed'] = $totalBed;
                    $res['totalIsi'] = $totalIsi;
                    $res['totalKosong'] = $totalKosong;

                    $res['tt'] = $data10;
                    array_push($compact, 'res', 'r');
                    break;
                case 'leaflet':
                    break;
                default:
                    return abort(404);
                    break;
            }
        } else {
            return abort(404);
        }

        $pages = $request["pages"];
        $role = $request["role"];
        array_push($compact, "pages");
//        if($pages == 'dashboard'){
//            return view("module.".$role.".".$pages,compact($compact));
//        }else  if($pages == 'dashboard-v2') {
//            return view("module.".$role.".".$pages, compact($compact));
//        }else{
        return view("module." . $role . "." . $pages . "." . $pages, compact($compact));
//        }

    }

    public static function getDaftarPenerimaanSuplier($tglAwal, $tglAkhir, Request $request)
    {
        $kdProfile = $_SESSION['kdProfile'];
        $idProfile = (int)$kdProfile;
        $data = \DB::table('strukpelayanantr as sp')
            ->JOIN('strukpelayanandetailtr as spd', 'spd.nostrukidfk', '=', 'sp.norec')
            ->leftJOIN('rekananmt as rkn', 'rkn.id', '=', 'sp.rekananidfk')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaipenerimaidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->LEFTJOIN('strukbuktipengeluarantr as sbk', 'sbk.norec', '=', 'sp.nosbklastidfk')
            ->select('sp.tglstruk', 'sp.nostruk', 'rkn.namarekanan', 'pg.namalengkap', 'sp.nokontrak',
                'ru.namaruangan', 'sp.norec', 'sp.nofaktur', 'sp.tglfaktur', 'sp.totalharusdibayar', 'sbk.nosbk',
                'sp.nosppb', 'sp.noorderidfk as noorderfk', 'sp.qtyproduk'
            )
            ->where('sp.koders', $idProfile)
            ->groupBy('sp.tglstruk', 'sp.nostruk', 'rkn.namarekanan', 'pg.namalengkap', 'sp.nokontrak', 'ru.namaruangan', 'sp.norec', 'sp.nofaktur',
                'sp.tglfaktur', 'sp.totalharusdibayar', 'sbk.nosbk', 'sp.nosppb', 'sp.noorderidfk', 'sp.qtyproduk');

        $data = $data->where('sp.tglstruk', '>=', $tglAwal . ' 00:00');
        $data = $data->where('sp.tglstruk', '<=', $tglAkhir . ' 23:59');

        if (isset($request['nostruk']) && $request['nostruk'] != "" && $request['nostruk'] != "undefined") {
            $data = $data->where('sp.nostruk', 'ilike', '%' . $request['nostruk']);
        }
        if (isset($request['namarekanan']) && $request['namarekanan'] != "" && $request['namarekanan'] != "undefined") {
            $data = $data->where('rkn.namarekanan', 'ilike', '%' . $request['namarekanan'] . '%');
        }
        if (isset($request['nofaktur']) && $request['nofaktur'] != "" && $request['nofaktur'] != "undefined") {
            $data = $data->where('sp.nofaktur', 'ilike', '%' . $request['nofaktur'] . '%');
        }
        if (isset($request['produkfk']) && $request['produkfk'] != "" && $request['produkfk'] != "undefined") {
            $data = $data->where('spd.objectprodukfk', '=', $request['produkfk']);
        }
        if (isset($request['noSppb']) && $request['noSppb'] != "" && $request['noSppb'] != "undefined") {
            $data = $data->where('sp.nosppb', 'ilike', '%' . $request['noSppb'] . '%');
        }
//        $data = $data->distinct();
        $data = $data->where('sp.aktif', true);
        $data = $data->where('sp.kelompoktransaksiidfk', 35);
        $data = $data->orderBy('sp.nostruk');
        $data = $data->get();

        foreach ($data as $item) {
            $details = \DB::select(DB::raw("select  pr.namaproduk,ss.satuanstandar,spd.qtyproduk,spd.qtyprodukretur,spd.hargasatuan,spd.hargadiscount,
                    --spd.hargappn,((spd.hargasatuan-spd.hargadiscount+spd.hargappn)*spd.qtyproduk) as total,spd.tglkadaluarsa,spd.nobatch
                    spd.hargappn,((spd.hargasatuan * spd.qtyproduk)-spd.hargadiscount+spd.hargappn) as total,spd.tglkadaluarsa,spd.nobatch
                    from strukpelayanandetailtr as spd 
                    left JOIN pelayananmt as pr on pr.id=spd.produkidfk
                    left JOIN satuanstandarmt as ss on ss.id=spd.satuanstandaridfk
                    where spd.koders = $idProfile and nostrukidfk=:norec"),
                array(
                    'norec' => $item->norec,
                )
            );
            $result[] = array(
                'tglstruk' => $item->tglstruk,
                'nostruk' => $item->nostruk,
                'nofaktur' => $item->nofaktur,
                'tglfaktur' => $item->tglfaktur,
                'namarekanan' => $item->namarekanan,
                'norec' => $item->norec,
                'namaruangan' => $item->namaruangan,
                'namapenerima' => $item->namalengkap,
                'totalharusdibayar' => $item->totalharusdibayar,
                'nosbk' => $item->nosbk,
                'nosppb' => $item->nosppb,
                'nokontrak' => $item->nokontrak,
                'noorderfk' => $item->noorderfk,
                'jmlitem' => $item->qtyproduk,
                'details' => $details,
            );
        }
        if (count($data) == 0) {
            $result = [];
        }

//        $result = array(
//            'daftar' => $result,
//            'message' => 'as@epic',
//        );

        return $result;
    }

    public static function getDaftarDistribusiBarangPerUnit($tglAwal, $tglAkhir, Request $request)
    {
        $kdProfile = $_SESSION['kdProfile'];
        $idProfile = (int)$kdProfile;
        $kdSirs1 = $request['KdSirs1'];
        $kdSirs2 = $request['KdSirs2'];


        $data = \DB::table('transaksikirimtr as sp')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaipengirimidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganasalidfk')
            ->LEFTJOIN('ruanganmt as ru2', 'ru2.id', '=', 'sp.ruangantujuanidfk')
            ->LEFTJOIN('transaksikirimdetailtr as kp', 'kp.transaksikirimfk', '=', 'sp.norec')
            ->LEFTJOIN('pelayananmt as pr', 'pr.id', '=', 'kp.produkidfk')
            ->LEFTJOIN('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->LEFTJOIN('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->LEFTJOIN('kelompokprodukmt as kps', 'kps.id', '=', 'jp.kelompokprodukidfk')
            ->LEFTJOIN('asalprodukmt as ap', 'ap.id', '=', 'kp.asalprodukidfk')
            ->LEFTJOIN('satuanstandarmt as ss', 'ss.id', '=', 'kp.satuanstandaridfk')
            ->select(
                DB::raw('sp.norec,pr.id as kodebarang,pr.kdproduk as kdsirs,pr.namaproduk,sp.nokirim,sp.jenispermintaanidfk,sp.tglkirim,ss.satuanstandar,
                         kp.qtyproduk,kp.hargasatuan,ru.namaruangan as ruanganasal,ru2.namaruangan as ruangantujuan,(kp.qtyproduk*kp.hargasatuan) as total,
                         pr.detailjenisprodukidfk as objectdetailjenisprodukfk,djp.detailjenisproduk,djp.jenisprodukidfk as objectjenisprodukfk,jp.jenisproduk,jp.jenisproduk,
                         jp.kelompokprodukidfk as objectkelompokprodukfk,
                         kps.kelompokproduk,kp.asalprodukidfk as objectasalprodukfk,ap.asalproduk')
            )
            ->where('sp.koders', $idProfile);
        $data = $data->where('sp.tglkirim', '>=', $tglAwal . ' 00:00');
        $data = $data->where('sp.tglkirim', '<=', $tglAkhir . ' 23:59');

        if (isset($request['nokirim']) && $request['nokirim'] != "" && $request['nokirim'] != "undefined") {
            $data = $data->where('sp.nokirim', 'ilike', '%' . $request['nokirim']);
        }
        if (isset($request['ruanganasalfk']) && $request['ruanganasalfk'] != "" && $request['ruanganasalfk'] != "undefined") {
            $data = $data->where('ru.id', '=', $request['ruanganasalfk']);
        }
        if (isset($request['ruangantujuanfk']) && $request['ruangantujuanfk'] != "" && $request['ruangantujuanfk'] != "undefined") {
            $data = $data->where('ru2.id', '=', $request['ruangantujuanfk']);
        }
        if (isset($request['namaproduk']) && $request['namaproduk'] != "" && $request['namaproduk'] != "undefined") {
            $data = $data->where('pr.namaproduk', 'ilike', '%' . $request['namaproduk']);
        }

        if (isset($request['jenisProduk']) && $request['jenisProduk'] != "" && $request['jenisProduk'] != "undefined") {
            $data = $data->where('djp.objectjenisprodukfk', '=', $request['jenisProduk']);
        }
        if (isset($request['AsalProduk']) && $request['AsalProduk'] != "" && $request['AsalProduk'] != "undefined") {
            $data = $data->where('kp.objectasalprodukfk', '=', $request['AsalProduk']);
        }
        if (isset($request['kelompokProduk']) && $request['kelompokProduk'] != "" && $request['kelompokProduk'] != "undefined") {
            $data = $data->where('jp.objectkelompokprodukfk', '=', $request['kelompokProduk']);
        }
        if (isset($request['KdSirs1']) && $request['KdSirs1'] != '') {
            if ($request['KdSirs2'] != null && $request['KdSirs2'] != '' && $request['KdSirs1'] != null && $request['KdSirs1'] != '') {
                $data = $data->whereRaw(" (pr.kdproduk BETWEEN '" . $request['KdSirs1'] . "' and '" . $request['KdSirs2'] . "') ");
            } elseif ($request['KdSirs2'] && $request['KdSirs2'] != '' && $request['KdSirs1'] == '' || $request['KdSirs1'] == null) {
                $data = $data->whereRaw = (" pr.kdproduk like '" . $request['KdSirs2'] . "%'");
            } elseif ($request['KdSirs1'] && $request['KdSirs1'] != '' && $request['KdSirs2'] == '' || $request['KdSirs2'] == null) {
                $data = $data->whereRaw = (" pr.kdproduk like '" . $request['KdSirs1'] . "%'");
            }
        }

        $data = $data->where('sp.aktif', true);
        $data = $data->where('sp.kelompoktransaksiidfk', 34);
        $data = $data->where('kp.qtyproduk', '>', 0);
        $data = $data->orderBy('sp.nokirim');
        $data = $data->get();
        $result = array(
//            'datalogin' => $dataLogin,
            'data' => $data,
            'message' => 'Cepot'
        );
        return $data;
    }

    public function getInfoStok(Request $request)
    {
        $idProfile = $_SESSION['kdProfile'];
        $data = DB::select(DB::raw("select sum( cast (spd.qtyproduk as float))  as qtyproduk,prd.namaproduk,
                ru.namaruangan,ss.satuanstandar
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                inner JOIN ruanganmt as ru on ru.id=spd.ruanganidfk
                inner JOIN pelayananmt as prd on prd.id=spd.produkidfk
                left JOIN satuanstandarmt as ss on ss.id=prd.satuanstandaridfk
                inner JOIN asalprodukmt as ap on ap.id=spd.asalprodukidfk
                where spd.koders = $idProfile and spd.qtyproduk > 0 
                and prd.aktif=true 
                and ru.aktif=true  
                group by prd.namaproduk,ru.namaruangan,ss.satuanstandar
                order by prd.namaproduk"));
        if (count($data) > 0) {
            foreach ($data as $key => $row) {
                $count[$key] = $row->qtyproduk;
            }
            array_multisort($count, SORT_DESC, $data);
        }


        $result = array(
            'data' => $data,
            'message' => 'inhuman',
        );
        return $data;
    }

    public function getLaporanPemakaianObat(Request $request)
    {

        $idProfile = (int)$_SESSION['kdProfile'];
        $tglAwal = Carbon::now()->format('Y-m-d 00:00');
        $tglAkhir = Carbon::now()->format('Y-m-d 23:59');
        $data = DB::select(DB::raw("
            select * from (
                select x.namaproduk ,sum (x.jumlah) as jumlah ,sum(x.total ) as total from (

                select
                prd.namaproduk  , pp.jumlah , (
                ((  CASE WHEN   pp.hargasatuan IS NULL THEN 0 ELSE pp.hargasatuan END 
                - CASE WHEN pp.hargadiscount IS NULL THEN   0 ELSE pp.hargadiscount END     ) * pp.jumlah
                ) + CASE    WHEN    pp.jasa IS NULL THEN 0  ELSE        pp.jasa END) AS total
                from  transaksireseptr as sr
                join transaksipasientr  as pp  on pp.strukresepidfk =sr.norec
                join pelayananmt as prd on pp.produkidfk= prd.id
                where sr.koders = $idProfile and pp.tglpelayanan BETWEEN  '$tglAwal' and '$tglAkhir'
                and pp.strukresepidfk is not null

                Union all

                SELECT pr.namaproduk,  (spd.qtyproduk) as jumlah,
                  (((   CASE WHEN   spd.hargasatuan IS NULL THEN 0 ELSE spd.hargasatuan END 
                - CASE WHEN spd.hargadiscount IS NULL THEN  0 ELSE spd.hargadiscount END    ) * spd.qtyproduk
                ) + CASE    WHEN    spd.hargatambahan IS NULL THEN 0    ELSE        spd.hargatambahan END) AS total
                FROM strukpelayanantr as sp  
                JOIN strukpelayanandetailtr as spd on spd.nostrukidfk = sp.norec  
                join pelayananmt as pr  on pr.id =spd.produkidfk
                WHERE sp.koders = $idProfile and sp.tglstruk BETWEEN   '$tglAwal' and '$tglAkhir'
                AND sp.nostruk_intern='-' AND substring(sp.nostruk,1,2)='OB'  
                and sp.aktif != false
                Union ALL

                SELECT pr.namaproduk, (spd.qtyproduk) as jumlah,
                  (((   CASE WHEN   spd.hargasatuan IS NULL THEN 0 ELSE spd.hargasatuan END 
                - CASE WHEN spd.hargadiscount IS NULL THEN  0 ELSE spd.hargadiscount END    ) * spd.qtyproduk
                ) + CASE    WHEN    spd.hargatambahan IS NULL THEN 0    ELSE        spd.hargatambahan END) AS total
                FROM strukpelayanantr as sp  
                JOIN strukpelayanandetailtr as spd on spd.nostrukidfk = sp.norec  
                join pelayananmt as pr  on pr.id =spd.produkidfk
                WHERE sp.koders = $idProfile and sp.tglstruk BETWEEN   '$tglAwal' and '$tglAkhir'
                AND sp.nostruk_intern not in ('-') AND substring(sp.nostruk,1,2)='OB'  
                and sp.aktif != false
                ) as x
                group by x.namaproduk
            )  as z order by z.total desc")
        );

        $result = array(
            'data' => $data,
            'message' => 'er@epic',
        );
        return $result;
    }

    public function getDetailPegawai(Request $r)
    {
        $idProfile = (int)$_SESSION['kdProfile'];
        if ($r['jenis'] == 'Aktif') {
            $data = DB::select(DB::raw("select pg.namalengkap ,pg.id,jp.statuspegawai,
                  date_part('year', age( pg.tgllahir))::int as umur,pdd.pendidikan,jk.jeniskelamin,pg.tgllahir
                from pegawaimt  as pg
                left JOIN statuspegawaimt as jp on jp.id =pg.objectstatuspegawaifk
                left JOIN jeniskelaminmt as jk on jk.id =pg.objectjeniskelaminfk
                left JOIN pendidikanmt as pdd on pg.objectpendidikanterakhirfk = pdd.id
                where pg.koders = $idProfile and pg.aktif=true
                and jp.statuspegawai='$r[jenis]'
                order by pg.namalengkap"));
        } else {
            $data = DB::select(DB::raw("select pg.namalengkap ,pg.id,jp.statuspegawai,
              date_part('year', age( pg.tgllahir))::int  as umur,pdd.pendidikan,jk.jeniskelamin,pg.tgllahir
                from pegawaimt  as pg
                left JOIN statuspegawaimt as jp on jp.id =pg.objectstatuspegawaifk  
                left JOIN jeniskelaminmt as jk on jk.id =pg.objectjeniskelaminfk
                left JOIN pendidikan_m as pdd on pg.objectpendidikanterakhirfk = pdd.id
                where pg.koders = $idProfile and pg.alktif=true
            and (jp.statuspegawai in ('Non Aktif') or jp.statuspegawai is null )
                order by pg.namalengkap 
                "));
        }
        return view('module.shared.detail-pegawai', compact('data'));


    }

    public static function getLaporanLayanan()
    {

        $idProfile = (int)$_SESSION['kdProfile'];

        $tglAwal = date('Y-m-d 00:00');
        $tglAkhir = date('Y-m-d 23:59');

        $results = DB::select(DB::raw("
                select pg.id as iddokter,pp.norec,pg.namalengkap as dokter,pp.jumlah as count, pp.hargasatuan as tariff ,
                pr.namaproduk as layanan, pp.hargasatuan * pp.jumlah as totall ,
                ps.norm as nocm,ps.namapasien,pp.tglpelayanan
                from transaksipasientr as pp 
                join daftarpasienruangantr as apd on apd.norec = pp.daftarpasienruanganfk
                join registrasipasientr as pd on pd.norec = apd.registrasipasienfk
                join pasienmt as ps on ps.id = pd.normidfk
                join petugaspelaksanatr as ppp on ppp.transaksipasienfk = pp.norec
                join pegawaimt as pg on pg.id =ppp.pegawaiidfk
                join pelayananmt as pr on pr.id = pp.produkidfk
                where pp.koders = $idProfile and pp.tglpelayanan BETWEEN '$tglAwal'  and '$tglAkhir'
                and pp.strukresepidfk is null
                and ppp.jenispetugaspeidfk=4
                "));
        return $results;
    }

    public function getCountPegawai(Request $request)
    {

        $idProfile = (int)$_SESSION['kdProfile'];
        $kateg = $this->settingDataFixed('statusDataPegawaiException', $idProfile);
        $keduduk = $this->settingDataFixed('listDataKedudukanException', $idProfile);
        $jenisKelamin = DB::select(DB::raw("select count ( x.namalengkap) as total, x.jeniskelamin from (
                select jp.jeniskelamin,pg.namalengkap 
                from pegawaimt  as pg
                left JOIN jeniskelaminmt as jp on jp.id =pg.objectjeniskelaminfk
                where pg.aktif=true
                 )as x GROUP BY x.jeniskelamin"));
        $kategoryPegawai = DB::select(DB::raw("select count ( x.namalengkap) as total, x.kategorypegawai from (
                select jp.kategorypegawai,pg.namalengkap 
                from pegawaimt  as pg
                left JOIN kategorypegawaimt as jp on jp.id =pg.kategorypegawai
                where pg.koders = $idProfile and pg.aktif=true
                )as x GROUP BY x.kategorypegawai"));
        $kelompokJabatan = DB::select(DB::raw("select count ( x.id) as total, x.namakelompokjabatan from (
                select jp.detailkelompokjabatan as namakelompokjabatan,pg.namalengkap ,pg.id
                from pegawaimt  as pg
                inner JOIN nilaikelompokjabatanmt as jp on jp.id =pg.objectkelompokjabatanfk
                where pg.koders = $idProfile and pg.aktif=true
                 )as x GROUP BY x.namakelompokjabatan"));
        $unitKerja = DB::select(DB::raw("
            
            select count ( x.id) as total, x.unitkerja from (
            select uk.name as unitkerja,pg.namalengkap ,pg.id
            from pegawaimt as pg
            left JOIN unitkerjapegawaimt as uk on uk.id=pg.objectunitkerjapegawaifk
             where pg.aktif=true
             and pg.koders = $idProfile
              )
            as x GROUP BY x.unitkerja
                "));
        $unitKerja2 = DB::select(DB::raw("
            
            select count ( x.id) as total, x.unitkerja from (
            select uk.namaruangan as unitkerja,pg.namalengkap ,pg.id
            from pegawaimt as pg
            left JOIN ruanganmt as uk on uk.id=pg.objectruangankerjafk
             where pg.aktif=true
             and pg.koders = $idProfile
              )
            as x GROUP BY x.unitkerja
                "));
        $statusPegawai = DB::select(DB::raw("select count ( x.id) as total,x.statuspegawai as statuspegawai from (
                select pg.namalengkap ,pg.id,jp.statuspegawai
                from pegawaimt  as pg
                left JOIN statuspegawaimt as jp on jp.id =pg.objectstatuspegawaifk
                where pg.koders = $idProfile and pg.aktif=true
                )as x
                GROUP BY x.statuspegawai
                "));
//
//        $kedudukanPeg = DB::select(DB::raw("select count ( x.namalengkap) as total, x.kedudukan  from (
//                select jp.name as kedudukan,pg.namalengkap from pegawaimt  as pg
//                left JOIN sdm_kedudukan_m as jp on jp.id =pg.kedudukanfk
//                where pg.kdprofile = $idProfile and pg.statusenabled=true
//
//
//                )as x
//                GROUP BY x.kedudukan
//                "));

        $pendidikan = DB::select(DB::raw("select x.total, x.pendidikan  from (
                select jp.pendidikan, count(pg.namalengkap) as total from pegawaimt  as pg
                left JOIN pendidikanmt as jp on pg.objectpendidikanterakhirfk = jp.id
                where pg.aktif=true
                   and pg.koders = $idProfile
                 GROUP by jp.pendidikan
                )as x
                order by x.total  
                "));
        $jenispegawai = DB::select(DB::raw("select x.total, x.jenis  from (
                select jp.jenispegawai as jenis, count(pg.namalengkap) as total from pegawaimt  as pg
                left JOIN jenispegawaimt as jp on pg.objectjenispegawaifk = jp.id
                where pg.aktif=true
                   and pg.koders = $idProfile
                 GROUP by jp.jenispegawai
                )as x
                order by x.total  
                "));
        $usia = DB::select(DB::raw("
                select pg.namalengkap,pg.tgllahir ,
                --CONVERT(int,ROUND(DATEDIFF(hour,pg.tgllahir,GETDATE())/8766.0,0)) AS umur
                    date_part('year', age( pg.tgllahir))::int as umur
                 from pegawaimt  as pg
                where pg.koders = $idProfile and pg.aktif=true
                "));
        $under20 = 0;
        $under30 = 0;
        $under40 = 0;
        $under50 = 0;
        $up51 = 0;
        $usiaa = [];
        foreach ($usia as $itemu) {
            if ($itemu->umur <= 20) {
                $under20 = $under20 + 1;
            }
            if ($itemu->umur > 20 && $itemu->umur <= 30) {
                $under30 = $under30 + 1;
            }
            if ($itemu->umur > 30 && $itemu->umur <= 40) {
                $under40 = $under40 + 1;
            }
            if ($itemu->umur > 40 && $itemu->umur <= 50) {
                $under50 = $under50 + 1;
            }
            if ($itemu->umur > 50) {
                $up51 = $up51 + 1;
            }
        }
        $usiaa [] = array(
            'total' => $under20,
            'usia' => 'dibawah 20 Tahun',
        );
        $usiaa [] = array(
            'total' => $under30,
            'usia' => '21 s/d 30 Tahun',
        );
        $usiaa [] = array(
            'total' => $under40,
            'usia' => '31 s/d 40 Tahun',
        );
        $usiaa [] = array(
            'total' => $under50,
            'usia' => '41 s/d 50 Tahun',
        );
        $usiaa [] = array(
            'total' => $up51,
            'usia' => 'diatas 51 Tahun',
        );
        $tglAwal = Carbon::now()->startOfMonth();
        $tglAkhir = Carbon::now()->endOfMonth();
        $dataPensiun = DB::select(DB::raw("
            select pg.id,pg.namalengkap,to_char(pg.tglpensiun,'YYYY-MM-DD') as tglpensiun,to_char (pg.tgllahir,'YYYY-MM-DD') as tgllahir,
            pg.nippns,gp.golonganpegawai,
            pdd.pendidikan,sm.name as subunitkerja,uk.name as unitkerja
            from mappegawaijabatantounitkerjamt as mappe
            left join pegawaimt as pg on mappe.pegawaiidfk =pg.id
            left join golonganpegawaimt as gp on pg.objectgolonganpegawaifk = gp.id
            left join pendidikanmt as pdd on pg.objectpendidikanterakhirfk = pdd.id
            left join subunitkerjamt sm on mappe.subunitkerjapegawaiidfk = sm.id
            left join unitkerjapegawaimt  as uk on mappe.unitkerjapegawaiidfk = uk.id
            where mappe.koders = $idProfile 
            and pg.tglpensiun between '$tglAwal' and '$tglAkhir'
            order by pg.namalengkap"));

        $pensiun['tglAwal'] = $tglAwal;
        $pensiun['tglAkhir'] = $tglAkhir;
        $pensiun['bulan'] = Carbon::now()->format('F Y');
        $pensiun['data'] = $dataPensiun;

        $result = array(
            'jeniskelamin' => $jenisKelamin,
            'countjk' => count($jenisKelamin),
            'kategoripegawai' => $kategoryPegawai,
            'jenispegawai' => $jenispegawai,
            'kelompokjabatan' => $kelompokJabatan,
            'unitkerjapegawai' => $unitKerja,
            'statuspegawai' => $statusPegawai,
//            'kedudukan' => $kedudukanPeg,
            'unitkerja2' => $unitKerja2,
            'pendidikan' => $pendidikan,
            'usia' => $usiaa,
            'datapensiun' => $pensiun,
            'message' => 'ramdanegie',
        );
//        dd($result);
        return $result;

    }

    public function getTrendPemakaianObat(Request $request)
    {
        $idProfile = $_SESSION['kdProfile'];
        $tglAwal = Carbon::now()->format('Y-m-d 00:00');
        $tglAkhir = Carbon::now()->format('Y-m-d 23:59');
        $data = DB::select(DB::raw("select * from
                (
                select sum(pp.jumlah) as jumlah,prd.namaproduk  
                from transaksipasientr  as pp 
                join pelayananmt as prd on pp.produkidfk= prd.id
                where pp.tglpelayanan BETWEEN '$tglAwal' and  '$tglAkhir'
                and pp.strukresepidfk is not null
                GROUP BY prd.namaproduk

                UNION ALL
                SELECT  sum(spd.qtyproduk) as jumlah,pr.namaproduk
               FROM strukpelayanantr as sp  
                JOIN strukpelayanandetailtr as spd on spd.nostrukidfk = sp.norec  
                join pelayananmt as pr  on pr.id =spd.produkidfk
                WHERE sp.koders = $idProfile and sp.tglstruk BETWEEN '$tglAwal' and  '$tglAkhir'
                AND sp.nostruk_intern='-' AND substring(sp.nostruk,1,2)='OB'  
                and sp.aktif != false
                GROUP BY pr.namaproduk
                UNION ALL    
                 SELECT sum  (spd.qtyproduk) as jumlah,pr.namaproduk
               FROM strukpelayanantr as sp  
                JOIN strukpelayanandetailtr as spd on spd.nostrukidfk = sp.norec  
                join pelayananmt as pr  on pr.id =spd.produkidfk
                WHERE sp.koders = $idProfile and sp.tglstruk BETWEEN '$tglAwal' and  '$tglAkhir'
              AND sp.nostruk_intern not in ('-') AND substring(sp.nostruk,1,2)='OB'  
                and sp.aktif != false
                        GROUP BY pr.namaproduk
                ) as x
                order by x.jumlah desc")
        );

        $result = array(
            'chart' => $data,
            'message' => 'er@epic',
        );
        return $result;
    }

    public static function getPendapatanRumahSakit($tgl1, $tgl2, $kdProfile, $tipe)
    {

        $idProfile = (int)$kdProfile;
        $data = [];
        if ($tipe == 'sehari') {
            $tglAwal = $tgl1 . ' 00:00';
            $tglAkhir = $tgl2 . ' 23:59';
        }
        if ($tipe == 'seminggu') {
            $tglAwal = Carbon::now()->subWeek(1)->toDateString();//  Carbon::now()->subMonth(1);
            $tglAwal = date($tglAwal . ' 00:00');
            $tglAkhir = date('Y-m-d 23:59');
        }

        $data = DB::select(DB::raw("
        
      SELECT
    x.tglpencarian,
    x.namaruangan,
    x.namadepartemen,
    x.kelompokpasien,
    SUM (x.total) AS total
FROM
    (
        SELECT
            to_char (
                pp.tglpelayanan,
                'yyyy-MM-dd HH:mm'
            ) AS tglpencarian,
            ru.namaruangan,
            dpm.namadepartemen,
            kps.kelompokpasien,
            --SUM
             (
                (
                    (
                        CASE
                        WHEN pp.hargajual IS NULL THEN
                            0
                        ELSE
                            pp.hargajual
                        END - CASE
                        WHEN pp.hargadiscount IS NULL THEN
                            0
                        ELSE
                            pp.hargadiscount
                        END
                    ) * pp.jumlah
                ) + CASE
                WHEN pp.jasa IS NULL THEN
                    0
                ELSE
                    pp.jasa
                END
            ) AS total
        FROM
            transaksipasientr AS pp
        JOIN daftarpasienruangantr AS apd ON apd.norec = pp.daftarpasienruanganfk
        JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk
        JOIN ruanganmt AS ru ON ru.id = apd.ruanganidfk
        LEFT JOIN kelompokpasienmt AS kps ON kps.id = pd.kelompokpasienlastidfk
        LEFT JOIN instalasimt AS dpm ON dpm.id = ru.instalasiidfk
        WHERE pp.koders = $idProfile and
            pp.tglpelayanan BETWEEN '$tglAwal'
        AND '$tglAkhir'
        AND pp.strukresepidfk IS NULL
        AND pd.aktif = true
 
        UNION ALL
            SELECT
                to_char (
                    sp.tglstruk,
                    'yyyy-MM-dd HH:mm'
                ) AS tglpencarian,
                ru.namaruangan,
                dp.namadepartemen,
                'Umum/Pribadi' AS kelompokpasien,
                -- SUM 
                (
                    spd.qtyproduk * (
                        spd.hargasatuan - CASE
                        WHEN spd.hargadiscount IS NULL THEN
                            0
                        ELSE
                            spd.hargadiscount
                        END
                    ) + CASE
                    WHEN spd.hargatambahan IS NULL THEN
                        0
                    ELSE
                        spd.hargatambahan
                    END
                ) AS total
            FROM
                strukpelayanantr AS sp
            JOIN strukpelayanandetailtr AS spd ON spd.nostrukidfk = sp.norec
            LEFT JOIN ruanganmt AS ru ON ru.id = sp.ruanganidfk
            LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk
            WHERE sp.koders = $idProfile and
                sp.tglstruk BETWEEN '$tglAwal'
            AND '$tglAkhir'
            AND sp.nostruk LIKE 'OB%'
            AND sp.aktif <> false
            --GROUP BY
            --    sp.tglstruk,
            --  ru.namaruangan,
            --   dp.namadepartemen
            UNION ALL
                SELECT
                    to_char (
                        pp.tglpelayanan,
                        'yyyy-MM-dd HH:mm'
                    ) AS tglpencarian,
                    ru.namaruangan,
                    dpm.namadepartemen,
                    kps.kelompokpasien,
                    -- SUM 
                    (
                        (
                            (
                                CASE
                                WHEN pp.hargajual IS NULL THEN
                                    0
                                ELSE
                                    pp.hargajual
                                END - CASE
                                WHEN pp.hargadiscount IS NULL THEN
                                    0
                                ELSE
                                    pp.hargadiscount
                                END
                            ) * pp.jumlah
                        ) + CASE
                        WHEN pp.jasa IS NULL THEN
                            0
                        ELSE
                            pp.jasa
                        END
                    ) AS total
                FROM
                        transaksipasientr AS pp
                JOIN daftarpasienruangantr AS apd ON apd.norec = pp.daftarpasienruanganfk
                JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk
                JOIN ruanganmt AS ru ON ru.id = apd.ruanganidfk
                LEFT JOIN kelompokpasienmt AS kps ON kps.id = pd.kelompokpasienlastidfk
                LEFT JOIN instalasimt AS dpm ON dpm.id = ru.instalasiidfk
                WHERE pp.koders = $idProfile and
                    pp.tglpelayanan BETWEEN '$tglAwal'
                AND '$tglAkhir'
                AND pp.strukresepidfk IS NOT NULL
                AND pd.aktif = true
                -- GROUP BY
                --  ru.namaruangan,
                -- dpm.namadepartemen,
                --  kps.kelompokpasien,
                    --  pp.tglpelayanan
    ) AS x
GROUP BY
    x.tglpencarian,
    x.kelompokpasien,
    x.namaruangan,
    x.namadepartemen
   

           "));
        if (count($data) > 0) {
            foreach ($data as $key => $row) {
                $count[$key] = $row->tglpencarian;
            }


            array_multisort($count, SORT_ASC, $data);
        }


        $result = array(
            'data' => $data,
//            'count' => count($data),
            'message' => 'ramdanegie',
        );
        return $result;

    }

    public static function formatRp($number)
    {
        return 'Rp.' . number_format((float)$number, 2, ".", ",");
    }

    public static function getPenerimaanKasir($tgl, $tgl2, $kdProfile)
    {
        $request['tglAwal'] = $tgl . ' 00:00';
        $request['tglAkhir'] = $tgl2 . ' 23:59';
        $idProfile = (int)$kdProfile;
//        $data = \DB::table('strukbuktipenerimaantr as sbm')
//            ->join('strukpelayanantr as sp', 'sbm.nostrukidfk', '=', 'sp.norec')
//            ->leftjoin('registrasipasientr as pd', 'sp.registrasipasienfk', '=', 'pd.norec')
//            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
//              ->leftjoin('instalasimt as dpr', 'dpr.id', '=', 'ru.instalasiidfk')
//            ->leftjoin('loginuser_s as lu', 'lu.id', '=', 'sbm.pegawaipenerimaidfk')
//            ->leftjoin('pegawaimt as p', 'p.id', '=', 'lu.objectpegawaifk')
//            ->leftjoin('pasienmt as ps', 'ps.id', '=', 'sp.normidfk')
//            ->leftjoin('strukbuktipenerimaancarabayartr as sbmcr', 'sbmcr.nosbmidfk', '=', 'sbm.norec')
//            ->leftjoin('carabayarmt as cb', 'cb.id', '=', 'sbmcr.carabayaridfk')
//            ->leftjoin('jenistransaksimt as kt', 'kt.id', '=', 'sbm.')
//            ->leftjoin('transaksiclosingtr as sc', 'sc.norec', '=', 'sbm.noclosingidfk')
//            ->leftjoin('transaksiverifikasitr as sv', 'sv.norec', '=', 'sbm.noverifikasiidfk')
//            ->select('sbm.norec as noRec','cb.carabayar as caraBayar','sbmcr.carabayaridfk as idCaraBayar','sbm.objectkelompoktransaksifk as idKelTransaksi','dpr.namadepartemen',
//                              'kt.kelompoktransaksi as kelTransaksi','sbm.keteranganlainnya as keterangan','p.id as idPegawai','p.namalengkap as namaPenerima',
//                              'sc.noclosing as noClosing','sbm.nosbm as noSbm','sv.noverifikasi as noVerifikasi','sc.tglclosing as tglClosing',
//                              'sbm.tglsbm as tglSbm','sv.tglverifikasi as tglVerif','pd.noregistrasi','ps.namapasien',
//                              'sp.norec as norec_sp','ru.id as ruid','ru.namaruangan','sp.namapasien_klien','ps.nocm','sbm.totaldibayar AS totaldibayar'
////                    ,\DB::raw("")
//            )
//            ->where('sbm.statusenabled',true)
//            ->where('sbm.kdprofile', $kdProfile);
        $data = \DB::table('strukbuktipenerimaantr as sbm')
            ->join('strukpelayanantr as sp', 'sbm.nostrukidfk', '=', 'sp.norec')
            ->leftjoin('registrasipasientr as pd', 'sp.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('instalasimt as dpr', 'dpr.id', '=', 'ru.instalasiidfk')
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
			    sbm.totaldibayar AS totalpenerimaan,sp.registrasipasienfk,dpr.namadepartemen,  sbm.totaldibayar 
            "))
            ->where('sbm.aktif', true)
            ->where('sbm.koders', $kdProfile);

        $filter = $request;
        $filter['tglAwal'] = $tgl . ' 00:00';
        $filter['tglAkhir'] = $tgl2 . ' 23:59';
        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
            $tgl2 = $filter['tglAwal'];//. " 00:00:00";
            $data = $data->where('sbm.tglsbm', '>=', $tgl2);
        }
        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
            $tgl = $filter['tglAkhir'];//. " 23:59:59";
            $data = $data->where('sbm.tglsbm', '<=', $tgl);
        }
        if (isset($filter['idPegawai']) && $filter['idPegawai'] != "" && $filter['idPegawai'] != "undefined") {
            $data = $data->where('p.id', '=', $filter['idPegawai']);
        }
        if (isset($filter['idCaraBayar']) && $filter['idCaraBayar'] != "" && $filter['idCaraBayar'] != "undefined") {
            $data = $data->where('cb.id', '=', $filter['idCaraBayar']);
        }
        if (isset($filter['idKelTransaksi']) && $filter['idKelTransaksi'] != "" && $filter['idKelTransaksi'] != "undefined") {
            $data = $data->where('kt.id', $filter['idKelTransaksi']);
        }
        if (isset($filter['ins']) && $filter['ins'] != "" && $filter['ins'] != "undefined") {
            $data = $data->where('ru.objectdepartemenfk', $filter['ins']);
        }
        if (isset($filter['nosbm']) && $filter['nosbm'] != "" && $filter['nosbm'] != "undefined") {
            $data = $data->where('sbm.nosbm', 'ilike', '%' . $filter['nosbm'] . '%');
        }
        if (isset($filter['nocm']) && $filter['nocm'] != "" && $filter['nocm'] != "undefined") {
            $data = $data->where('ps.nocm', 'ilike', '%' . $filter['nocm'] . '%');
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
                $kodeRuang[] = (int)$item;
            }
            $data = $data->whereIn('p.id', $kodeRuang);
        }


        $data = $data->get();
//         dd($data);
        $result = array(
            'data' => $data,
            'message' => 'inhuman'
        );
        return $result;
    }

    public function getDetailPendapatan(Request $r)
    {

        $data = DB::select(DB::raw("
        

    SELECT
                x.namapasien,
                x.nocm,
                x.noregistrasi,
                x.tglpencarian,
                x.namaruangan,
                x.namadepartemen,
                x.kelompokpasien,
                SUM (x.total) AS total,
                x.layanan
            FROM
            (
                SELECT
                        pm.namapasien,
                        pm.norm as nocm,
                        pd.noregistrasi,
                        to_char (
                            pp.tglpelayanan,
                            'yyyy-MM-dd'
                        ) AS tglpencarian,
                        ru.namaruangan,
                        dpm.namadepartemen,
                        kps.kelompokpasien,
                        SUM (
                            (
                            (
                                    CASE
                                    WHEN pp.hargajual IS NULL THEN
                                        0
                                    ELSE
                                        pp.hargajual
                                    END - CASE
                                    WHEN pp.hargadiscount IS NULL THEN
                                        0
                                    ELSE
                                        pp.hargadiscount
                                    END
                                ) * pp.jumlah
                            ) + CASE
                            WHEN pp.jasa IS NULL THEN
                                0
                            ELSE
                                pp.jasa
                            END
                        ) AS total,
                        'Layanan' AS layanan
                    FROM
                        transaksipasientr AS pp
                    JOIN daftarpasienruangantr AS apd ON apd.norec = pp.daftarpasienruanganfk
                    JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk
                    JOIN pasienmt AS pm ON pm.id = pd.normidfk
                    JOIN ruanganmt AS ru ON ru.id = apd.ruanganidfk
                    LEFT JOIN kelompokpasienmt AS kps ON kps.id = pd.kelompokpasienlastidfk
                    LEFT JOIN instalasimt AS dpm ON dpm.id = ru.instalasiidfk
                    WHERE
                      pp.tglpelayanan BETWEEN '$r[tglawal] 00:00'
                     AND '$r[tglakhir] 23:59'
                    and ru.namaruangan='$r[ruangan]'
                  AND 
pp.strukresepidfk IS NULL
                    AND pd.aktif = true
                    GROUP BY
                        pm.namapasien,
                        pm.norm,
                        pd.noregistrasi,
                        ru.namaruangan,
                        dpm.namadepartemen,
                        kps.kelompokpasien,
                        pp.tglpelayanan
                    UNION ALL
                        SELECT
                            sp.namapasien_klien AS namapasien,
                            sp.nostruk_intern AS nocm,
                            '-' AS noregistrasi,
                            to_char (
                                sp.tglstruk,
                                'yyyy-MM-dd'
                            ) AS tglpencarian,
                            ru.namaruangan,
                            dp.namadepartemen,
                            'Umum/Pribadi' AS kelompokpasien,
                            SUM (
                                spd.qtyproduk * (
                                    spd.hargasatuan - CASE
                                    WHEN spd.hargadiscount IS NULL THEN
                                        0
                                    ELSE
                                        spd.hargadiscount
                                    END
                                ) + CASE
                                WHEN spd.hargatambahan IS NULL THEN
                                    0
                                ELSE
                                    spd.hargatambahan
                                END
                            ) AS total,
                            'Non Layanan' AS layanan
                        FROM
                            strukpelayanantr AS sp
                        JOIN strukpelayanandetailtr AS spd ON spd.nostrukidfk = sp.norec
                        LEFT JOIN ruanganmt AS ru ON ru.id = sp.ruanganidfk
                        LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk
                        WHERE
                           sp.tglstruk BETWEEN '$r[tglawal] 00:00'
 AND '$r[tglakhir] 23:59'
 and ru.namaruangan='$r[ruangan]'
 AND 
SUBSTRING (sp.nostruk, 1, 2) = 'OB'
AND sp.aktif <> false
                        GROUP BY
                            sp.namapasien_klien,
                            sp.nostruk_intern,
                            sp.tglstruk,
                            ru.namaruangan,
                            dp.namadepartemen
                        UNION ALL
                            SELECT
                                pm.namapasien,
                                pm.norm as nocm,
                                pd.noregistrasi,
                                to_char (
                                    pp.tglpelayanan,
                                    'yyyy-MM-dd'
                                ) AS tglpencarian,
                                ru.namaruangan,
                                dpm.namadepartemen,
                                kps.kelompokpasien,
                                SUM (
                                    (
                                    (
                                            CASE
                                            WHEN pp.hargajual IS NULL THEN
                                                0
                                            ELSE
                                                pp.hargajual
                                            END - CASE
                                            WHEN pp.hargadiscount IS NULL THEN
                                                0
                                            ELSE
                                                pp.hargadiscount
                                            END
                                        ) * pp.jumlah
                                    ) + CASE
                                    WHEN pp.jasa IS NULL THEN
                                        0
                                    ELSE
                                        pp.jasa
                                    END
                                ) AS total,
                                'Layanan' AS layanan
                            FROM
                                transaksipasientr AS pp
                            JOIN daftarpasienruangantr AS apd ON apd.norec = pp.daftarpasienruanganfk
                            JOIN transaksireseptr AS sr ON sr.norec = pp.strukresepidfk
                            JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk
                            JOIN pasienmt AS pm ON pm.id = pd.normidfk
                            JOIN ruanganmt AS ru ON ru.id = sr.ruanganidfk
                            LEFT JOIN kelompokpasienmt AS kps ON kps.id = pd.kelompokpasienlastidfk
                            LEFT JOIN instalasimt AS dpm ON dpm.id = ru.instalasiidfk
                            WHERE
                                 pp.tglpelayanan BETWEEN'$r[tglawal] 00:00'
                          AND '$r[tglakhir] 23:59'
                          and ru.namaruangan='$r[ruangan]'
                           AND 
pp.strukresepidfk IS NOT NULL
                            AND pd.aktif = true
                            GROUP BY
                                pm.namapasien,
                                pm.norm,
                                pd.noregistrasi,
                                ru.namaruangan,
                                dpm.namadepartemen,
                                kps.kelompokpasien,
                                pp.tglpelayanan
                ) AS x
            GROUP BY
                x.namapasien,
                x.nocm,
                x.noregistrasi,
                x.tglpencarian,
                x.kelompokpasien,
                x.namaruangan,
                x.namadepartemen,
                x.layanan
            
                            
            
                       "));


        return view('module.shared.detail-pendapatan', compact('data'));


    }

    public function getDetailKun(Request $r)
    {
        $idProfile = $_SESSION['kdProfile'];
        $tglAwal = $r['tglawal'] . ' 00:00';
        $tglAkhir = $r['tglakhir'] . ' 23:59';
        $idDepRanap = (int)$this->settingDataFixed('kdDepartemenRanapFix', $idProfile);
        $idDepFarmasi = (int)$this->settingDataFixed('instalasiFarmasi', $idProfile);
        $isfaramsi = false;
        // dd($r->input());
        if ($r['jenis'] == 'pengunjung') {
            if ($r['title'] == 'Pengunjung  Hemodialisa') {
                $data = DB::table('pasiendaftar_t as pd')
                    ->join('pasien_m as ps', 'ps.id', '=', 'pd.nocmfk')
                    ->join('ruangan_m as ru', 'ru.id', '=', 'pd.objectruanganlastfk')
                    ->join('departemen_m as dp', 'dp.id', '=', 'ru.objectdepartemenfk')
                    ->select('pd.noregistrasi', 'pd.tglregistrasi', 'ps.nocm', 'ps.namapasien', 'ru.namaruangan',
                        'pd.tglpulang')
                    ->where('pd.kdprofile', $idProfile)
                    ->whereBetween('pd.tglregistrasi', [$tglAwal, $tglAkhir])
                    ->where('ru.objectdepartemenfk', $r['id'])
                    ->where('ru.id', 12)
                    ->where('pd.statusenabled', 1)
                    ->get();
            } else {
                $data = DB::table('registrasipasientr as pd')
                    ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                    ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
                    ->join('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                    ->select('pd.noregistrasi', 'pd.tglregistrasi', 'ps.norm as nocm', 'ps.namapasien', 'ru.namaruangan',
                        'pd.tglpulang')
                    ->where('pd.koders', $idProfile)
                    ->whereBetween('pd.tglregistrasi', [$tglAwal, $tglAkhir])
                    ->where('ru.instalasiidfk', $r['id'])
                    ->where('pd.aktif', 1)
//                    ->where('ru.id','<>',12)
                    ->get();
            }
        } else {
            if ($r['id'] == $idDepRanap) {
                $data = DB::select(DB::raw("SELECT
                    pd.noregistrasi,
                    pd.tglregistrasi,
                    ps.norm as nocm,
                    ps.namapasien,
                    ru.namaruangan,
                    pd.tglpulang
                FROM
                    registrasipasientr AS pd
                INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk
                INNER JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk
                INNER JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk
                WHERE pd.koders = $idProfile 
                 and ru.instalasiidfk = $idDepRanap 
                and pd.aktif = true
                AND ((
                    pd.tglregistrasi < '$tglAwal'
                    AND pd.tglpulang >= '$tglAkhir')
                  OR pd.tglpulang IS NULL 
                )
               
             "));
            } else if ($r['id'] == $idDepFarmasi) {
                $isfaramsi = true;
                $data = DB::select(DB::raw("
                SELECT
                    apd.norec,  pd.noregistrasi,pd.tglregistrasi,
                   ps.norm as nocm,ps.namapasien,
                    ru.namaruangan, ru2.namaruangan AS ruanganfarmasi,sr.noresep,
                    sr.tglresep,    pg.namalengkap,pd.tglpulang
                FROM
                    registrasipasientr AS pd
                INNER JOIN ruanganmt AS ru ON ru. ID = pd.ruanganlastidfk
                INNER JOIN pasienmt AS ps ON ps. ID = pd.normidfk
                INNER JOIN daftarpasienruangantr AS apd ON apd.registrasipasienfk = pd.norec
                INNER JOIN transaksireseptr AS sr ON sr.daftarpasienruanganfk = apd.norec
                LEFT JOIN pegawaimt AS pg ON sr.penulisresepidfk = pg. ID
                LEFT JOIN ruanganmt AS ru2 ON ru2. ID = sr.ruanganidfk
                WHERE pd.koders = $idProfile and 
                    sr.tglresep BETWEEN '$tglAwal'
                AND '$tglAkhir'
                 and sr.aktif = true
               "));
            } else {
                if ($r['title'] == 'Kunjungan  Hemodialisa') {
                    $data = DB::table('antrianpasiendiperiksa_t as apd')
                        ->join('pasiendaftar_t as pd', 'pd.norec', '=', 'apd.noregistrasifk')
                        ->join('pasien_m as ps', 'ps.id', '=', 'pd.nocmfk')
                        ->join('ruangan_m as ru', 'ru.id', '=', 'apd.objectruanganfk')
                        ->join('departemen_m as dp', 'dp.id', '=', 'ru.objectdepartemenfk')
                        ->select('pd.noregistrasi', 'pd.tglregistrasi', 'ps.norm as ps.nocm', 'ps.namapasien', 'ru.namaruangan',
                            'pd.tglpulang')
                        ->where('apd.kdprofile', $idProfile)
                        ->whereBetween('apd.tglregistrasi', [$tglAwal, $tglAkhir])
                        ->where('pd.statusenabled', 1)
                        ->where('ru.objectdepartemenfk', $r['id'])
                        ->where('ru.id', '=', 12)
                        ->get();
                } else {
                    $data = DB::table('daftarpasienruangantr as apd')
                        ->join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                        ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                        ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                        ->join('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
                        ->select('pd.noregistrasi', 'pd.tglregistrasi', 'ps.norm as nocm', 'ps.namapasien', 'ru.namaruangan',
                            'pd.tglpulang')
                        ->where('apd.koders', $idProfile)
                        ->whereBetween('apd.tglregistrasi', [$tglAwal, $tglAkhir])
                        ->where('pd.aktif', 1)
                        ->where('ru.instalasiidfk', $r['id'])
//                        ->where('ru.id','<>',12)
                        ->get();
                }
            }
        }

        $title = DB::table('instalasimt')->where('id', $r['id'])->first()->namadepartemen;
        return view('module.shared.detail-kunjungan', compact('data', 'title', 'isfaramsi'));

    }

    function getComboDiagnosaTop()
    {
        $data = DB::select(DB::raw("select kddiagnosa,namadiagnosa from pelayananmedis_t
                where kddiagnosa is not null
                and kddiagnosa !='-'
                GROUP BY kddiagnosa,namadiagnosa
                order by kddiagnosa asc"));
        echo "<option value=''>-- Filter Diagnosa --</option>";
        foreach ($data as $k) {
            echo "<option value='$k->kddiagnosa' >" . $k->kddiagnosa . ' - ' . $k->namadiagnosa . "</option>";
        }
    }

    public static function getAllMonitoringKlaim()
    {
        $kdProfile = $_SESSION['kdProfile'];
        $y = date('Y') - 1;
        $start = $month = strtotime($y . '-01-01');
        $end = strtotime(date('Y-m-d'));
        $arrM = [];
        while ($month < $end) {
            $arrM [] = array(
                'blntahun' => date('Y-m', $month),
                'tahun' => date('Y', $month),
                'bulan' => date('F', $month),
                'jmlkasus_ri' => 0,
                'jmlkasuspending_ri' => 0,
                'pengajuan_ri' => static::formatRp(0),
                'pending_ri' => static::formatRp(0),
                'klaim_ri' => static::formatRp(0),
                'jmlkasus_rj' => 0,
                'jmlkasuspending_rj' => 0,
                'pengajuan_rj' => static::formatRp(0),
                'pending_rj' => static::formatRp(0),
                'klaim_rj' => static::formatRp(0),
            );
            $month = strtotime("+1 month", $month);
        }
        foreach ($arrM as $key => $row) {
            $count[$key] = $row['blntahun'];
        }
        array_multisort($count, SORT_DESC, $arrM);
        // return $arrM ;
        $data = DB::select(DB::raw("select x.tahun,x.bulan,
            sum(x.jmlkasus_ri) as jmlkasus_ri ,sum(x.jmlkasuspending_ri) as jmlkasuspending_ri ,
            sum(x.pengajuan_ri) as pengajuan_ri ,
            sum(x.pending_ri) as pending_ri ,
            sum(x.klaim_ri) as klaim_ri ,

            sum(x.jmlkasus_rj) as jmlkasus_rj ,sum(x.jmlkasuspending_rj) as jmlkasuspending_rj,
            sum(x.pengajuan_rj) as pengajuan_rj,
            sum(x.pending_rj) as pending_rj ,
            sum(x.klaim_rj) as klaim_rj,
            x.blntahun
             from (
            SELECT to_char(tglpulang,'yyyy') as tahun,to_char(tglpulang,'MM')as  bulan,
             to_char(tglpulang,'yyyy') || '-' ||to_char(tglpulang,'MM')as blntahun,
            case when jenispelayanan ='1' then count(norec) else 0 end as jmlkasus_ri,
            case when jenispelayanan ='2' then count(norec) else 0  end as jmlkasus_rj,
            case when jenispelayanan ='1'  and status='Proses Pending' then 1 else 0 end as jmlkasuspending_ri,
            case when jenispelayanan ='2'  and status='Proses Pending' then 1 else 0 end as jmlkasuspending_rj,
            case when jenispelayanan ='1' then   sum(totalpengajuan) else 0 end as pengajuan_ri,
            case when jenispelayanan ='2' then   sum(totalpengajuan) else 0 end as pengajuan_rj,
            case when jenispelayanan ='1' and  status='Proses Pending' then sum(totalpengajuan) else 0 end as pending_ri,
            case when jenispelayanan ='2' and  status='Proses Pending' then sum(totalpengajuan) else 0 end as pending_rj,
            case when jenispelayanan ='1' and status='Klaim' then sum(totalpengajuan) else 0 end as klaim_ri,
            case when jenispelayanan ='2' and status='Klaim' then sum(totalpengajuan) else 0 end as klaim_rj

            FROM monitoringklaimtr
            where koders=$kdProfile
            group by tglpulang,status,jenispelayanan
            ) as x GROUP BY x.tahun,x.bulan,x.blntahun
            order by x.blntahun desc;


                "));
        $i = 0;
        foreach ($arrM as $key => $v) {
            foreach ($data as $key2 => $k) {
                if ($arrM[$i]['blntahun'] == $k->blntahun) {
                    $arrM[$i]['blntahun'] = $k->blntahun;
                    $arrM[$i]['tahun'] = $k->tahun;
                    // $arrM[$i]['bulan']  =  $k->bulan;
                    $arrM[$i]['jmlkasus_ri'] = $k->jmlkasus_ri;
                    $arrM[$i]['jmlkasuspending_ri'] = $k->jmlkasuspending_ri;
                    $arrM[$i]['pengajuan_ri'] = static::formatRp($k->pengajuan_ri);
                    $arrM[$i]['pending_ri'] = static::formatRp($k->pending_ri);
                    $arrM[$i]['klaim_ri'] = static::formatRp($k->klaim_ri);
                    $arrM[$i]['jmlkasus_rj'] = $k->jmlkasus_rj;
                    $arrM[$i]['jmlkasuspending_rj'] = $k->jmlkasuspending_rj;
                    $arrM[$i]['pengajuan_rj'] = static::formatRp($k->pengajuan_rj);
                    $arrM[$i]['pending_rj'] = static::formatRp($k->pending_rj);
                    $arrM[$i]['klaim_rj'] = static::formatRp($k->klaim_rj);

                }
            }
            $i++;
        }


        $result = array(
            'data' => $arrM,
            'as' => 'er@epic',
        );

        return $arrM;
    }

    public static function getKetersediaanTempatTidurPerkelas($tglAwal, $tglAkhir, $kdProfile)
    {

        $idProfile = (int)$kdProfile;

        $data = DB::select(DB::raw("select sum(x.isi) as terpakai, sum(x.kosong) as kosong,x.namakelas,
        sum(x.isi)+ sum(x.kosong) as jml from (
            SELECT
                kmr.id,
                kmr.namakamar,
                kl.id AS id_kelas,
                kl.namakelas,
                ru.id AS id_ruangan,
                ru.namaruangan,
                kmr.jumlakamarisi,
                kmr.qtybed,
                case when sb.id=1 then 1 else 0 end as isi,
                case when sb.id=2 then 1 else 0 end as kosong
            FROM
                tempattidurmt AS tt
            LEFT JOIN statusbedmt AS sb ON sb.id = tt.statusbedidfk
            LEFT JOIN kamarmt AS kmr ON kmr.id = tt.kamaridfk
            LEFT JOIN ruanganmt AS ru ON ru.id = kmr.ruanganidfk
            LEFT JOIN kelasmt AS kl ON kl.id = kmr.kelasidfk
            WHERE tt.koders = $idProfile and
                tt.aktif = true
                and kmr.aktif=TRUE) as x GROUP BY x.namakelas
            order by  x.namakelas

            "));

        return $data;
    }

    public static function getBorLosToi($tglAwal, $tglAkhir, $kdProfile)
    {


        $idProfile = (int)$kdProfile;
        $idDepRanap = static::settingDataFixed2('kdDepartemenRanapFix', $idProfile); //$this->settingDataFixed('kdDepartemenRanapFix', $kdProfile);
        $idStatKelMeninggal = static::settingDataFixed2('KdStatKeluarMeninggal', $idProfile);//$this->settingDataFixed('KdStatKeluarMeninggal', $kdProfile);
        $idKondisiPasienMeninggal = static::settingDataFixed2('KdKondisiPasienMeninggal', $idProfile);//$this->settingDataFixed('KdKondisiPasienMeninggal', $kdProfile);

        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $tahun = new \DateTime($tglAkhir);
        $tahun = date('Y');
        $datetime1 = new \DateTime($tglAwal);
        $datetime2 = new \DateTime($tglAkhir);
        $interval = $datetime1->diff($datetime2);
        $sehari = 1;//$interval->format('%d');
        $data10 = [];
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
        if ($jumlahTT == 0) {
            $data10[] = array(
                'lamarawat' => 0,
                'hariperawatan' => 0,
                'pasienpulang' => 0,
                'meninggal' => 0,
                'matilebih48' => 0,
                'tahun' => 0,
                'bulan' => date('d-M-Y'),//(float)$item->bulanregis ,
                'bor' => 0,
                'alos' => 0,
                'bto' => 0,
                'toi' => 0,
                'gdr' => 0,
                'ndr' => 0,
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
        if ($year == date('Y'))
            $total_month = date('m');
        else
            $total_month = 12;

        for ($m = 1; $m <= $total_month; $m++) {
            $num_of_days[] = array(
                'bulan' => $m,
                'jumlahhari' => cal_days_in_month(CAL_GREGORIAN, $m, $year),
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
        $jmlMatilebih48 = 0;
        foreach ($hariPerawatan as $item) {
            foreach ($lamaRawat as $itemLamaRawat) {
                foreach ($dataMeninggal as $itemDead) {
//                         if ($item->bulanregis == $itemLamaRawat->bulanpulang &&
//                             $itemLamaRawat->bulanpulang == $itemDead->bulanregis ) {
                    /** @var  $gdr = (Jumlah Mati dibagi Jumlah pasien Keluar (Hidup dan Mati) */
                    $gdr = (int)$itemDead->jumlahmeninggal * 1000 / (int)$itemLamaRawat->jumlahpasienpulang;
                    /** @var  $NDR = (Jumlah Mati > 48 Jam dibagi Jumlah pasien Keluar (Hidup dan Mati) */
                    $ndr = (int)$itemDead->jumlahlebih48 * 1000 / (int)$itemLamaRawat->jumlahpasienpulang;

                    $jmlMeninggal = (int)$itemDead->jumlahmeninggal;
                    $jmlMatilebih48 = (int)$itemDead->jumlahlebih48;
//                         }
                }
//                if ($item->bulanregis == $itemLamaRawat->bulanpulang ) {
                /** @var  $alos = (Jumlah Lama Dirawat dibagi Jumlah pasien Keluar (Hidup dan Mati) */
//                return $this->respond($itemLamaRawat->jumlahpasienpulang );
                if ((int)$itemLamaRawat->jumlahpasienpulang > 0) {
                    $alos = (int)$itemLamaRawat->lamarawat / (int)$itemLamaRawat->jumlahpasienpulang;
                }

                /** @var  $bto = Jumlah pasien Keluar (Hidup dan Mati) DIBAGI Jumlah tempat tidur */
                $bto = (int)$itemLamaRawat->jumlahpasienpulang / $jumlahTT;

//                }
//                foreach ($num_of_days as $numday){
//                    if ($numday['bulan'] == $item->bulanregis){
                /** @var  $bor = (Jumlah hari perawatn RS dibagi ( jumlah TT x Jumlah hari dalam satu periode ) ) x 100 % */
                $bor = ((int)$item->jumlahhariperawatan * 100 / ($jumlahTT * (float)$sehari));//$numday['jumlahhari']));
                /** @var  $toi = (Jumlah TT X Periode) - Hari Perawatn DIBAGI Jumlah pasien Keluar (Hidup dan Mati) */
//                        $toi = ( ( $jumlahTT * $numday['jumlahhari'] )- (int)$item->jumlahhariperawatan ) /(int)$itemLamaRawat->jumlahpasienpulang ;
                if ((int)$itemLamaRawat->jumlahpasienpulang > 0) {
                    $toi = (($jumlahTT * (float)$sehari) - (int)$item->jumlahhariperawatan) / (int)$itemLamaRawat->jumlahpasienpulang;
                }
                $hariPerawatanJml = (int)$item->jumlahhariperawatan;
                $jmlPasienPlg = (int)$itemLamaRawat->jumlahpasienpulang;
//                    }
//                }
            }

            $data10[] = array(
                'lamarawat' => (int)$itemLamaRawat->lamarawat,
                'hariperawatan' => $hariPerawatanJml,
                'pasienpulang' => $jmlPasienPlg,
                'meninggal' => $jmlMeninggal,
                'matilebih48' => $jmlMatilebih48,
                'tahun' => $tahun,
                'bulan' => date('d-M-Y'),//(float)$item->bulanregis ,
                'bor' => (float)number_format($bor > 0 ? $bor : 0, 2),
                'alos' => (float)number_format( $alos > 0 ? $alos : 0, 2),
                'bto' => (float)number_format( $bto > 0 ? $bto : 0, 2),
                'toi' => (float)number_format( $toi > 0 ? $toi : 0, 2),
                'gdr' => (float)number_format( $gdr > 0 ? $gdr : 0, 2),
                'ndr' => (float)number_format( $ndr > 0 ? $ndr : 0, 2),
            );
        }

        return $data10;

    }

    public function getTopDiagByKec(Request $r)
    {
        $idProfile = $_SESSION['kdProfile'];
        // $dataLogin = $request->all();
        $tglAwal = $r['tglawal'] . ' 00:00';
        $tglAkhir = $r['tglakhir'] . ' 23:59';
        $bulan = Carbon::now()->format('F');
        $paramProp = '';
        $paramKota = '';
        $paramKec = '';
//        if(isset($request['propinsiId']) && $request['propinsiId']!=''&& $request['propinsiId']!='undefined'){
//            $paramProp = ' and pro.id='.$request['propinsiId'];
//        }
//        if(isset($request['kotaId']) && $request['kotaId']!=''&& $request['kotaId']!='undefined'){
//            $paramKota = ' and kot.id='.$request['kotaId'];
//        }
        if (isset($r['id']) && $r['id'] != '' && $r['id'] != 'undefined') {
            $paramKec = ' and kec.id=' . $r['id'];
        }
        $data = DB::select(DB::raw("select * from (
                select count(x.kddiagnosa)as jumlah,x.kddiagnosa,x.namadiagnosa
                from (select dm.kddiagnosa, 
                dm.namadiagnosa
                from daftarpasienruangantr as app
                left join diagnosapasienicdxtr as dp on dp.daftarpasienruanganfk = app.norec
                
                inner join icdxmt as dm on dp.icdxidfk = dm.id
                inner join registrasipasientr as pd on pd.norec = app.registrasipasienfk
                inner join pasienmt as ps on ps.id = pd.normidfk
                left join alamatmt as alm on alm.normidfk = ps.id
                 left join kecamatanmt as kec on kec.id = alm.kecamatanidfk
                left join kotakabupatenmt as kot on kot.id = alm.kotakabupatenidfk
                left join propinsimt as pro on pro.id = alm.provinsiidfk
                left join ruanganmt as ru on ru.id = pd.ruanganlastidfk
                where app.koders = $idProfile and dm.kddiagnosa <> '-'  and   pd.aktif=true and               
                pd.tglregistrasi BETWEEN '$tglAwal' AND '$tglAkhir'
                $paramProp
                $paramKota 
                 $paramKec

                )as x GROUP BY x.namadiagnosa ,x.kddiagnosa 
                ) as z
                ORDER BY z.jumlah desc  limit 10

            "));
        if (count($data) > 0) {
            foreach ($data as $item) {
                $result[] = array(
                    'jumlah' => $item->jumlah,
                    'kd' => $item->kddiagnosa,
                    'kddiagnosa' => $item->kddiagnosa . '-' . $item->namadiagnosa,
                    'namadiagnosa' => $item->namadiagnosa,
                );
            }

        } else {
            $result[] = array(
                'jumlah' => 0,
                'kd' => null,
                'kddiagnosa' => null,
                'namadiagnosa' => null
            );
        }

        $results = array(
            'result' => $result,
            'month' => $bulan,
            'message' => 'ramdanegie',
        );
        return $result;
    }

    public static function getTopTenDiagnosa($tglAwal, $tglAkhir, $kdProfile, $prov = null)
    {
        $idProfile = (int)$kdProfile;
        // $dataLogin = $request->all();
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $bulan = Carbon::now()->format('F');
        $paramProp = '';
        $paramKota = '';
        $paramKec = '';
        if ($prov != null) {
            $paramProp = ' and pro.id=' . $prov;
        }
//        if(isset($request['kotaId']) && $request['kotaId']!=''&& $request['kotaId']!='undefined'){
//            $paramKota = ' and kot.id='.$request['kotaId'];
//        }
//        if(isset($request['kecamatanId']) && $request['kecamatanId']!='' && $request['kecamatanId']!='undefined'){
//            $paramKec = ' and kec.id='.$request['kecamatanId'];
//        }
        $data = DB::select(DB::raw("select * from (
                select count(x.kddiagnosa)as jumlah,x.kddiagnosa,x.namadiagnosa
                from (select dm.kddiagnosa, 
                dm.namadiagnosa
                from daftarpasienruangantr as app
                left join diagnosapasienicdxtr as dp on dp.daftarpasienruanganfk = app.norec
             
              inner join icdxmt as dm on dp.icdxidfk = dm.id
                inner join registrasipasientr as pd on pd.norec = app.registrasipasienfk
                inner join pasienmt as ps on ps.id = pd.normidfk
                left join alamatmt as alm on alm.normidfk = ps.id
                 left join kecamatanmt as kec on kec.id = alm.kecamatanidfk
                left join kotakabupatenmt as kot on kot.id = alm.kotakabupatenidfk
                left join provinsimt as pro on pro.id = alm.provinsiidfk
                left join ruanganmt as ru on ru.id = pd.ruanganlastidfk
                where app.koders = $idProfile and dm.kddiagnosa <> '-'  and   pd.aktif=true and               
                pd.tglregistrasi BETWEEN '$tglAwal' AND '$tglAkhir'
                $paramProp
                $paramKota 
                 $paramKec

                )as x GROUP BY x.namadiagnosa ,x.kddiagnosa 
                ) as z
                ORDER BY z.jumlah desc  limit 10

            "));
        if (count($data) > 0) {
            foreach ($data as $item) {
                $result[] = array(
                    'jumlah' => $item->jumlah,
                    'kd' => $item->kddiagnosa,
                    'kddiagnosa' => $item->kddiagnosa . '-' . $item->namadiagnosa,
                    'namadiagnosa' => $item->namadiagnosa,
                );
            }

        } else {
            $result[] = array(
                'jumlah' => 0,
                'kd' => null,
                'kddiagnosa' => null,
                'namadiagnosa' => null
            );
        }

        $results = array(
            'result' => $result,
            'month' => $bulan,
            'message' => 'ramdanegie',
        );
        return $result;
    }

    public static function getKunjunganRuanganRawatInap($tglAwal, $tglAkhir, $kdProfile)
    {

        $idProfile = (int)$kdProfile;
        $depInap = 16;
        $tglAwal = $tglAwal . ' 00:00'; //Carbon::now()->startOfMonth()->subMonth(1);
        $tglAkhir = $tglAkhir . ' 23:59';
        $bulan = Carbon::now()->format('F');
        $idDepRanap = (int)static::settingDataFixed2('kdDepartemenRanapFix', $idProfile);
        $data = DB::select(DB::raw("SELECT
                        COUNT (z.kdruangan) AS jumlah,
                        z.namaruangan
                       FROM
                        (
                            select pd.noregistrasi, pd.tglregistrasi, ru.namaruangan, ru.id as kdruangan
                            from registrasipasientr as pd 
                            left join ruanganmt as ru on ru.id = pd.ruanganlastidfk 
                            where pd.koders = $idProfile and ru.instalasiidfk = $idDepRanap 
                            and pd.aktif = true 
                            and (  pd.tglregistrasi < '$tglAwal' AND pd.tglpulang >= '$tglAkhir' 
                           )
                            or pd.tglpulang is null
                            and pd.koders = $idProfile and ru.instalasiidfk = $idDepRanap 
                            and pd.aktif = true 
                            group by pd.tglregistrasi, pd.noregistrasi, ru.namaruangan, ru.id
                           
                        ) AS z
                    GROUP BY
                        z.namaruangan
            "));

        $result = array(
            'result' => $data,
            'month' => $bulan,
            'jml' => count($data),
            'message' => 'ramdanegie',
        );
        return $data;
    }

    public function getKecByProv(Request $r)
    {
        return DB::select(DB::raw("select kb.id as kbid,kb.namakotakabupaten,
            kk.id as kecid, kk.namakecamatan
             from kotakabupatenmt kb
            join kecamatanmt as kk on kk.kotakabupatenidfk =kb.id
            where kb.provinsiidfk in (select id from provinsimt where kdmap='$r[kdmap]')
            order by kk.namakecamatan"));
    }

    public static function getKunjunganPerJenisPelayanan($tglAwal, $tglAkhir, $kdProfile)
    {

        $idProfile = (int)$kdProfile;
        $JenisPelayananReg = 'REGULER';//static::settingDataFixed2('KdJenisPelayananReg', $idProfile);
        $JenisPelayananEks = 'EKSEKUTIF';//static::settingDataFixed2('KdJenisPelayananEks', $idProfile);
        $tglAwal = $tglAwal . ' 00:00'; //Carbon::now()->startOfMonth()->subMonth(1);
        $tglAkhir = $tglAkhir . ' 23:59';
        $data = DB::select(DB::raw("
            SELECT dp.id,
            dp.namadepartemen,
            pd.norec as norec_pd,
            CASE WHEN jp.jenispelayanan IS NULL THEN 'REGULER' else jp.jenispelayanan end as jenispelayanan
            FROM
            registrasipasientr AS pd
            left JOIN jenispelayananmt AS jp ON CAST (jp.id AS CHAR) = pd.jenispelayanan
            JOIN ruanganmt AS ru ON ru. ID = pd.ruanganlastidfk
            JOIN instalasimt AS dp ON dp. ID = ru.instalasiidfk
            WHERE pd.koders = $idProfile and
            pd.tglregistrasi BETWEEN '$tglAwal'
        
            AND '$tglAkhir'
            and pd.aktif=true
        "));
        $data10 = [];
        $data20 = [];
        $reguler = 0;
        $eksekutif = 0;
        if (count($data) > 0) {
            foreach ($data as $item) {
                $sama = false;
                $i = 0;
                foreach ($data10 as $hideung) {
                    if ($item->id == $data10[$i]['id']) {
                        $sama = 1;
                        $jml = (float)$hideung['total'] + 1;
                        $data10[$i]['total'] = $jml;
                        if ($item->jenispelayanan == $JenisPelayananReg) {
                            $data10[$i]['reguler'] = (float)$hideung['reguler'] + 1;
                        }
                        if ($item->jenispelayanan == $JenisPelayananEks) {
                            $data10[$i]['eksekutif'] = (float)$hideung['eksekutif'] + 1;
                        }
                    }
                    $i = $i + 1;
                }
                if ($sama == false) {
                    if ($item->jenispelayanan == $JenisPelayananReg) {
                        $reguler = 1;
                        $eksekutif = 0;

                    }
                    if ($item->jenispelayanan == $JenisPelayananEks) {
                        $reguler = 0;
                        $eksekutif = 1;
                    }

                    $data10[] = array(
                        'id' => $item->id,
                        'namadepartemen' => $item->namadepartemen,
                        'total' => 1,
                        'reguler' => $reguler,
                        'eksekutif' => $eksekutif


                    );

                }
                foreach ($data10 as $key => $row) {
                    $count[$key] = $row['id'];
                }
                array_multisort($count, SORT_DESC, $data10);
            }
        }
        $result = array(
            'data' => $data10,
            'jml' => count($data),
        );
//        dd($result);
        return $result;
    }

    public static function getTopTenAsalPerujukBPJS($tglAwal, $tglAkhir, $kdProfile)
    {

        $idProfile = (int)$kdProfile;

        $tglAwal = $tglAwal . ' 00:00'; //Carbon::now()->startOfMonth()->subMonth(1);
        $tglAkhir = $tglAkhir . ' 23:59';
        $bulan = Carbon::now()->format('F');
        $kdBPJS = DB::table('settingdatafixedmt')->where('namafield', 'idKelompokPasienBPJS')->where('koders', $idProfile)->first()->nilaifield;

        $data = DB::select(DB::raw("SELECT * FROM
            (
                SELECT COUNT (x.ppkrujukan) AS jumlah, x.ppkrujukan, x.kodeperujuk AS kodeppkrujukan
                FROM (SELECT pd.noregistrasi, CASE WHEN ap.kdprovider IS NULL THEN '-' ELSE ap.kdprovider
                END AS kodeperujuk,CASE WHEN ap.nmprovider IS NULL THEN '-' ELSE ap.nmprovider END AS ppkrujukan,
                pa.ppkrujukan AS kodepa
                FROM registrasipasientr AS pd
                LEFT JOIN pemakaianasuransitr AS pa ON pa.registrasipasienfk = pd.norec
                LEFT JOIN asuransimt AS ap ON ap.ID = pa.asuransiidfk
                WHERE pd.koders = $idProfile and pd.tglregistrasi BETWEEN '$tglAwal' AND '$tglAkhir'
                AND pd.kelompokpasienlastidfk in ($kdBPJS) 
                and   pd.aktif=true
                ) AS x GROUP BY x.ppkrujukan, x.kodeperujuk
            ) AS z
          ORDER BY
          z.jumlah DESC
         "));
        if (count($data) > 0) {
            $result = $data;
        } else {
            $result [] = array(
                'jumlah' => 0,
                'kodeppkrujukan' => null,
                'ppkrujukan' => null,
            );
        }

        $results = array(
            'result' => $result,
            'month' => $bulan,
            'message' => 'ramdanegie',
        );
        // dd($results);
        return $result;
    }

    public function getTempatTidurTerpakai($tglAwal, $tglAkhir, $kdProfile)
    {
        $idProfile = (int)$kdProfile;
        $idDepRanap = (int)$this->settingDataFixed('kdDepartemenRanapFix', $idProfile);
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
            
            WHERE pd.koders = $idProfile 
                $paramDep 
                 and pd.aktif = true
                and (pd.tglregistrasi < '$tglAwal' AND pd.tglpulang >= '$tglAkhir' 
                )
                or pd.tglpulang is null and pd.koders=$idProfile
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
        $result [] = ['name' => 'Balita Perempuan', 'umur' => '0 - 5 Tahun', 'jml' => $P_balita, 'img' => 'images/BayiPerempuan.png'];
        $result [] = ['name' => 'Balita Laki-laki', 'umur' => '0 - 5 Tahun', 'jml' => $L_balita, 'img' => 'images/BayiLaki-Laki.png'];
        $result [] = ['name' => 'Anak Perempuan', 'umur' => '6 - 11 Tahun', 'jml' => $P_anak, 'img' => 'images/AnakPerempuan.png'];
        $result [] = ['name' => 'Anak Laki-laki', 'umur' => '6 - 11 Tahun', 'jml' => $L_anak, 'img' => 'images/AnakLaki-Laki.png'];
        $result [] = ['name' => 'Remaja Perempuan', 'umur' => '12 - 25 Tahun', 'jml' => $P_remajaAwal + $P_remajaAkhir, 'img' => 'images/RemajaPerempuan.png'];
        $result [] = ['name' => 'Remaja Laki-laki', 'umur' => '12 - 25 Tahun', 'jml' => $L_remajaAwal + $L_remajaAkhir, 'img' => 'images/RemajaLaki-Laki.png'];
        $result [] = ['name' => 'Dewasa Perempuan', 'umur' => '26 - 45 Tahun', 'jml' => $P_dewasaAwal + $P_dewasaAkhir, 'img' => 'images/DewasaPerempuan.png'];
        $result [] = ['name' => 'Dewasa Laki-laki', 'umur' => '26 - 45 Tahun', 'jml' => $L_dewasaAwal + $L_dewasaAkhir, 'img' => 'images/DewasaLaki-Laki.png'];
        $result [] = ['name' => 'Lansia Perempuan', 'umur' => '46 - 65 Tahun', 'jml' => $P_lansiaAwal + $P_lansiaakhir, 'img' => 'images/Nenek2.png'];
        $result [] = ['name' => 'Lansia Laki-laki', 'umur' => '46 - 65 Tahun', 'jml' => $L_lansiaAwal + $L_lansiaakhir, 'img' => 'images/Kakek2.png'];
        $result [] = ['name' => 'Manula Perempuan', 'umur' => '> 65 Tahun', 'jml' => $P_manula, 'img' => 'images/Nenek.png'];
        $result [] = ['name' => 'Manula Laki-laki', 'umur' => '> 65 Tahun', 'jml' => $L_manula, 'img' => 'images/Kakek.png'];

        // dd($result);
        return $result;
    }

    public function getKunjunganRSPerJenisPasien($tglAwal, $tglAkhir, $kdProfile)
    {

        $idProfile = (int)$kdProfile;
        $kdKelompokPasienUmum = (int)$this->settingDataFixed('idKelompokPasienUmum', $idProfile);
        $KelompokPasienBpjs = (int)$this->settingDataFixed('idKelompokPasienBPJS', $idProfile);
        $KelompokPasienAsuransi = (int)$this->settingDataFixed('idKelompokPasienAsuransi', $idProfile);
        $KdKelPasienPerusahaan = (int)$this->settingDataFixed('idKelompokPasienPerusahaan', $idProfile);
        // $KdKelPasienPerjanjian = (int) $this->settingDataFixed('KdKelPasienPerjanjian', $idProfile);
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $dataALL = DB::select(DB::raw("select x.kelompokpasien ,count(x.kelompokpasien) as jumlah from (
                select pd.noregistrasi, 
                 kps.kelompokpasien, 
                 pd.kelompokpasienlastidfk as objectkelompokpasienlastfk, 
                 to_char (pd.tglregistrasi,'YYYY') as tahunregis
                 from registrasipasientr as pd
                 inner join kelompokpasienmt as kps on kps.id = pd.kelompokpasienlastidfk
          
                 WHERE pd.koders = $idProfile and pd.tglregistrasi BETWEEN '$tglAwal'AND '$tglAkhir'
                and pd.aktif =true
                )as  x
                GROUP BY x.kelompokpasien"));


        $data = \DB::table('registrasipasientr as pd')
            ->join('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->join('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
            ->select('pd.noregistrasi', 'kps.kelompokpasien',
                'pd.kelompokpasienlastidfk as objectkelompokpasienlastfk', 'dp.id',
                'dp.namadepartemen',
                'pd.norec as norec_pd',
                DB::raw("to_char (pd.tglregistrasi,'YYYY') as tahunregis"))
            ->where('pd.koders', $idProfile)
            ->where('pd.aktif', true)
            ->whereBetween('pd.tglregistrasi', [$tglAwal, $tglAkhir]);

        $data = $data->get();

        $data10 = [];
        $jmlBPJS = 0;
        $jmlAsuransiLain = 0;
        $jmlPerusahaan = 0;
        $jmlUmum = 0;
        $jmlPerjanjian = 0;
//         if (count($data) > 0) {
        foreach ($data as $item) {
            $sama = false;
            $i = 0;
            foreach ($data10 as $hideung) {
                if ($item->id == $data10[$i]['id']) {
                    $sama = 1;
                    $jml = (float)$hideung['total'] + 1;
                    $data10[$i]['total'] = $jml;
                    if ($item->objectkelompokpasienlastfk == $kdKelompokPasienUmum) {
                        $data10[$i]['jmlUmum'] = (float)$hideung['jmlUmum'] + 1;
                    }
                    if ($item->objectkelompokpasienlastfk == $KelompokPasienBpjs) {
                        $data10[$i]['jmlBPJS'] = (float)$hideung['jmlBPJS'] + 1;
                    }
                    if ($item->objectkelompokpasienlastfk == $KelompokPasienAsuransi) {
                        $data10[$i]['jmlAsuransiLain'] = (float)$hideung['jmlAsuransiLain'] + 1;
                    }
                    if ($item->objectkelompokpasienlastfk == $KdKelPasienPerusahaan) {
                        $data10[$i]['jmlPerusahaan'] = (float)$hideung['jmlPerusahaan'] + 1;
                    }
                    // if ($item->objectkelompokpasienlastfk == $KdKelPasienPerjanjian) {
                    //     $data10[$i]['jmlPerjanjian'] = (float)$hideung['jmlPerjanjian'] + 1;
                    // }
                }
                $i = $i + 1;
            }
            if ($sama == false) {
                if ($item->objectkelompokpasienlastfk == $kdKelompokPasienUmum) {
                    $jmlBPJS = 0;
                    $jmlAsuransiLain = 0;
                    $jmlPerusahaan = 0;
                    $jmlUmum = 1;
                    $jmlPerjanjian = 0;
                }
                if ($item->objectkelompokpasienlastfk == $KelompokPasienBpjs) {
                    $jmlBPJS = 1;
                    $jmlAsuransiLain = 0;
                    $jmlPerusahaan = 0;
                    $jmlUmum = 0;
                    $jmlPerjanjian = 0;
                }
                if ($item->objectkelompokpasienlastfk == $KelompokPasienAsuransi) {
                    $jmlBPJS = 0;
                    $jmlAsuransiLain = 1;
                    $jmlPerusahaan = 0;
                    $jmlUmum = 0;
                    $jmlPerjanjian = 0;
                }
                if ($item->objectkelompokpasienlastfk == $KdKelPasienPerusahaan) {
                    $jmlBPJS = 0;
                    $jmlAsuransiLain = 0;
                    $jmlPerusahaan = 1;
                    $jmlUmum = 0;
                    $jmlPerjanjian = 0;
                }
                // if ($item->objectkelompokpasienlastfk == $KdKelPasienPerjanjian) {
                //     $jmlBPJS  = 0;
                //     $jmlAsuransiLain  = 0;
                //     $jmlPerusahaan  = 0;
                //     $jmlUmum  = 0;
                //     $jmlPerjanjian  = 1;
                // }
                $data10[] = array(
                    'id' => $item->id,
                    'namadepartemen' => $item->namadepartemen,
                    'total' => 1,
                    'jmlBPJS' => $jmlBPJS,
                    'jmlAsuransiLain' => $jmlAsuransiLain,
                    'jmlPerusahaan' => $jmlPerusahaan,
                    'jmlUmum' => $jmlUmum,
                    // 'jmlPerjanjian' => $jmlPerjanjian,
                );
            }
            foreach ($data10 as $key => $row) {
                $count[$key] = $row['total'];
            }
            array_multisort($count, SORT_DESC, $data10);
        }

        $result = array(
            'dataAll' => $dataALL,
            'data' => $data10,
            'message' => 'ramdanegie',
        );
        // dd($result);
        return $result;
    }

    public function getPasienPerjenisPenjadwalan($tglAwal, $tglAkhir, $kdProfile)
    {
        $idProfile = (int)$kdProfile;
//         $idDepRaJal = (int)$this->settingDataFixed('KdDepartemenRawatJalan', $idProfile);
//         $idDepRanap = (int) $this->settingDataFixed('idDepRawatInap', $idProfile);
//         $idDepRehab = (int) $this->settingDataFixed('KdDepartemenInstalasiRehabilitasiMedik', $idProfile);
//         $idDepBedahSentral = (int) $this->settingDataFixed('KdDeptBedahSentral', $idProfile);
//         $idDepLaboratorium = (int) $this->settingDataFixed('KdDepartemenInstalasiLaboratorium',$idProfile);
//         $idDepRadiologi = (int) $this->settingDataFixed('KdDepartemenInstalasiRadiologi',$idProfile);
//         $idDepIGD = (int) $this->settingDataFixed('KdDepartemenInstalasiGawatDarurat',$idProfile);
//         $tglAwal = $tglAwal .' 00:00' ;
//         $tglAkhir = $tglAkhir.' 23:59' ;
//         $data = DB::select(DB::raw("
//                                    select count(x.noregistrasi) as jumlah  ,x.keterangan,
//     count (case when x.departemen = 'rawat_jalan' then 1 end) AS rawatjalan,
// count(case when x.departemen = 'igd' then 1 end) AS igd,
// count(case when x.departemen = 'rawat_inap' then 1 end) AS rawat_inap,
// count(case when x.departemen = 'radiologi' then 1 end) AS radiologi,
// count(case when x.departemen = 'laboratorium' then 1 end) AS laboratorium,
// count(case when x.departemen = 'operasi' then 1 end) AS operasi,
// count(case when x.departemen = 'rahab_medik' then 1 end) AS rehab_medik
//     from (
//     SELECT
//     case when apr.noreservasi is not null then 'Registrasi Online' else 'Loket Pendaftaran' end as keterangan,
//             pd.noregistrasi,
//     ru.namaruangan,pd.statusschedule,
//     case when ru.objectdepartemenfk = $idDepRaJal  then 'rawat_jalan'
//     when ru.objectdepartemenfk = $idDepIGD then 'igd'
//     when ru.objectdepartemenfk = $idDepRanap   then 'rawat_inap'
//     when ru.objectdepartemenfk = $idDepRadiologi  then 'radiologi'
//     when ru.objectdepartemenfk = $idDepLaboratorium  then 'laboratorium'
//     when ru.objectdepartemenfk = $idDepBedahSentral  then 'operasi'
//     when ru.objectdepartemenfk = $idDepRehab  then 'rahab_medik'
//     end as departemen
//     FROM
//     pasiendaftar_t AS pd
//     left join antrianpasienregistrasi_t as apr on apr.noreservasi=pd.statusschedule
//     inner JOIN ruangan_m AS ru ON ru.id = pd.objectruanganlastfk
//     WHERE pd.kdprofile = $idProfile and pd.tglregistrasi BETWEEN '$tglAwal'   AND '$tglAkhir'
//     and pd.statusenabled=true
//     ) as x  group BY x.keterangan

//             "));

//         $res = array(
//             'data' => $data
//         );
        $res = [];
        return $res;
    }

    public function getInfoKunjunganRawatJalanPerhari($tglAwal, $tglAkhir, $kdProfile)
    {
        $idProfile = (int)$kdProfile;
        $idDepRaJal = (int)$this->settingDataFixed('kdDepartemenRajal', $idProfile);
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $data = DB::select(DB::raw("
        
                    select * from (SELECT
                    pd.noregistrasi,
                    ps.namapasien,
                    pd.aktif,
                    apd.ispelayananpasien,
                    ru.namaruangan,
                  
                    pd.ruanganlastidfk AS kdruangan,row_number() over (partition by pd.noregistrasi order by apd.tglmasuk desc) as rownum 
                    FROM
                    registrasipasientr AS pd
                    INNER JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk
                    INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk
                    inner join daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                    WHERE pd.koders = $idProfile and
                    pd.tglregistrasi BETWEEN '$tglAwal'
                    AND '$tglAkhir'
                    AND ru.instalasiidfk = $idDepRaJal) as x where x.rownum=1
                    ORDER BY
                    x.noregistrasi"));

        $data10 = [];
        $sudahPeriksa = 0;
        $belumPeriksa = 0;
        $batalRegis = 0;
        $totalAll = 0;
        if (count($data) > 0) {
            foreach ($data as $item) {
                $sama = false;
                $i = 0;
                foreach ($data10 as $hideung) {
                    if ($item->kdruangan == $data10[$i]['kdruangan']) {
                        $sama = 1;
                        $jml = (float)$hideung['total'] + 1;
                        $data10[$i]['total'] = $jml;
                        if ($item->ispelayananpasien != null && $item->aktif == true) {
//                         if ($item->statusantrian != '0' && $item->norec_batal == null) {
                            $data10[$i]['diperiksa'] = (float)$hideung['diperiksa'] + 1;

                        }
                        if ($item->ispelayananpasien == null && $item->aktif == true) {
//                        if ($item->statusantrian == '0' && $item->norec_batal == null) {
                            $data10[$i]['belumperiksa'] = (float)$hideung['belumperiksa'] + 1;
                        }
                        if ($item->aktif == false) {
                            $data10[$i]['batalregistrasi'] = (float)$hideung['batalregistrasi'] + 1;
                        }
                        //                    $data10[$i]['totalAll'] = $data10[$i]['diperiksa'] + $data10[$i]['belumperiksa'] + $data10[$i]['batalregistrasi'];
                    }
                    $i = $i + 1;
                }
                if ($sama == false) {
                    if ($item->ispelayananpasien != null && $item->aktif == true) {
                        $sudahPeriksa = 1;
                        $belumPeriksa = 0;
                        $batalRegis = 0;
                    }
                    if ($item->ispelayananpasien == null && $item->aktif == true) {
                        $sudahPeriksa = 0;
                        $belumPeriksa = 1;
                        $batalRegis = 0;
                    }
                    if ($item->aktif == false) {
                        $sudahPeriksa = 0;
                        $belumPeriksa = 0;
                        $batalRegis = 1;
                    }
                    $data10[] = array(
                        'kdruangan' => $item->kdruangan,
                        'namaruangan' => $item->namaruangan,
                        'total' => 1,
                        'diperiksa' => $sudahPeriksa,
                        'belumperiksa' => $belumPeriksa,
                        'batalregistrasi' => $batalRegis,
//                        'count' => $totalAll,
                    );
                }
                foreach ($data10 as $key => $row) {
                    $count[$key] = $row['total'];
                }
                array_multisort($count, SORT_DESC, $data10);
            }
        } else {
            $data10[] = array(
                'kdruangan' => '-',
                'namaruangan' => 'Tidak Ada Data',
                'total' => 0,
                'diperiksa' => 0,
                'belumperiksa' => 0,
                'batalregistrasi' => 0,
            );
        }
        // dd($data10);

        return $data10;
    }

    public function listGambar()
    {
        return ['images/icon-pasien.png', 'images/icon-pasien-emergency.png',
            'images/icon-pasien-rawat-inap.png', 'images/icon-radiologi.png', 'images/icon-laboratorium.png',
            'images/icon-pasien.png', 'images/icon-pasien-emergency.png',
            'images/icon-pasien-rawat-inap.png', 'images/icon-radiologi.png', 'images/icon-laboratorium.png',
            'images/icon-pasien.png', 'images/icon-pasien-emergency.png',
            'images/icon-pasien-rawat-inap.png', 'images/icon-radiologi.png', 'images/icon-laboratorium.png'];
    }

    public function listWarna()
    {
        return [
            'bg-aqua-gradient', 'bg-green-gradient', 'bg-blue-active', 'bg-yellow-gradient', 'bg-red-gradient',
            'bg-light-blue-gradient', 'bg-maroon-gradient', 'bg-purple-gradient', 'bg-teal-gradient',
            'bg-aqua-gradient', 'bg-green-gradient', 'bg-blue-active', 'bg-yellow-gradient', 'bg-red-gradient',
            'bg-light-blue-gradient', 'bg-maroon-gradient', 'bg-purple-gradient', 'bg-teal-gradient'];
    }

    public function getPengunjung($tglAwal, $tglAkhir, $kdProfile)
    {
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $dept = $this->settingDataFixed('kdDeptEis', $kdProfile);
        $data = DB::select(DB::raw("SELECT dp.id ,dp.namadepartemen,count(pd.norec) as jumlah,dp.namaexternal as icon
                FROM instalasimt dp
                join ruanganmt as ru on ru.instalasiidfk=dp.id
                LEFT JOIN (SELECT registrasipasientr.norec,registrasipasientr.ruanganlastidfk 
                FROM registrasipasientr
                where tglregistrasi BETWEEN '$tglAwal' and '$tglAkhir' and registrasipasientr.aktif=true
                and registrasipasientr.koders=$kdProfile) pd ON (ru.id= pd.ruanganlastidfk)
                WHERE dp.id in ($dept)
                and dp.koders =$kdProfile
                and dp.aktif =true
                group by dp.namadepartemen,dp.id,dp.namaexternal
                order by dp.id asc
        "));


        return $data;
    }

    public function getKunjungan($tglAwal, $tglAkhir, $kdProfile)
    {
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $idDepRanap = (int)$this->settingDataFixed('kdDepartemenRanapFix', $kdProfile);
        $dept = $this->settingDataFixed('kdDeptEisKunjungan', $kdProfile);
        $data = DB::select(DB::raw("SELECT dp.id ,dp.namadepartemen,count(pd.norec) as jumlah,dp.namaexternal as icon
                FROM instalasimt dp
                join ruanganmt as ru on ru.instalasiidfk=dp.id
                LEFT JOIN (SELECT daftarpasienruangantr.norec,ruanganidfk FROM daftarpasienruangantr
                where tglregistrasi BETWEEN '$tglAwal' and '$tglAkhir' 
                and daftarpasienruangantr.aktif=true
                and daftarpasienruangantr.koders=$kdProfile)
                 pd ON (ru.id= pd.ruanganidfk)
                WHERE dp.id in ($dept)
                 and dp.aktif =true
                and dp.koders =$kdProfile
                group by dp.namadepartemen,dp.id,dp.namaexternal
                order by dp.id asc
        "));

        // $data = array_merge($data,$data2);
        $dataRanap = DB::select(DB::raw("select count(x.noregistrasi) as jumlah  
            from ( select  pd.noregistrasi,pd.tglregistrasi
            from registrasipasientr as pd
            inner join ruanganmt as ru on ru.id = pd.ruanganlastidfk
            where ru.instalasiidfk = $idDepRanap 
            and pd.aktif = true
            and pd.koders=$kdProfile
            and ( (  pd.tglregistrasi < '$tglAwal' 
                AND pd.tglpulang >= '$tglAkhir' 
             ) or pd.tglpulang is null)
          
         ) as x
            "));
        $dataFarmasi = DB::select(DB::raw("
             SELECT
                COUNT (x.noresep) AS jumlah
              FROM
                (
                    SELECT *
                    FROM
                        transaksireseptr AS sr
                    WHERE
                        sr.tglresep BETWEEN '$tglAwal'
                    AND '$tglAkhir'
                     and sr.aktif = true
                     and sr.koders=$kdProfile
                ) AS x"));
        $farmasi = 0;
        $masihDirawat = 0;
        if (count($dataFarmasi) > 0) {
            $farmasi = $dataFarmasi[0]->jumlah;
        }
        if (count($dataRanap) > 0) {
            $masihDirawat = $dataRanap[0]->jumlah;
        }
        $data10 = [];
        foreach ($data as $key => $value) {
            $data10 [] = array('id' => $value->id,
                'namadepartemen' => $value->namadepartemen,
                'jumlah' => $value->jumlah,
                'icon' => $value->icon,
            );

            # code...
        }
        $data10 [] = array(
            'id' => $idDepRanap,
            'namadepartemen' => 'Instalasi Rawat Inap',
            'jumlah' => $masihDirawat,
            'icon' => 'icofont icofont-icu',
        );
        $data10 [] = array(
            'id' => $this->settingDataFixed('instalasiFarmasi', $kdProfile),
            'namadepartemen' => 'Instalasi Farmasi',
            'jumlah' => $farmasi,
            'icon' => 'icofont icofont-pills',
        );
        if (count($data10) > 0) {
            foreach ($data10 as $key => $row) {
                $count[$key] = $row['id'];
            }
            array_multisort($count, SORT_ASC, $data10);
        }

        return $data10;
    }

    public function getTrendKunjunganPasienRajal($tglAwal, $tglAkhir, $kdProfile)
    {


        $minggu = Carbon::now()->subWeek(3)->format('Y-m-d');
        $tanggal = Carbon::now()->addDay(7)->format('Y-m-d');

        $begin = new \DateTime($minggu);
        $end = new \DateTime($tanggal);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $tgl = [];
        foreach ($period as $dt) {
            $tgl[] = array(
                'tglregistrasi' =>$dt->format("d, M Y"),
                'tanggal' => $dt->format("d. M"),
                'totalterdaftar' => 0,
                'diperiksa' => 0,
                'belumperiksa' => 0,
                'batalregistrasi' => 0,
                );
        }
//        dd($tgl);
        $idProfile = (int)$kdProfile;
        $now = Carbon::now()->addDay(1)->format('Y-m-d');
//        dd($now);
        $idDepRaJal = (int)$this->settingDataFixed('kdDepartemenRajal', $idProfile);
        $tglAwal2 = Carbon::now()->subWeek(3);//  Carbon::now()->subMonth(1);
        $tglAwal = date('Y-m-d', strtotime($tglAwal2)) . ' 00:00';
        $tglAkhir = Carbon::now()->format('Y-m-d 23:59');
        $currentDate = Carbon::now();
        $last2week = $currentDate->subWeek();
        $reser = DB::select(DB::raw("
                SELECT
                    pd.norec,
                    pd.noreservasi AS noregistrasi,
                    to_char( pd.tanggalreservasi, 'dd, Mon YYYY' ) AS tglregistrasi,
                    to_char( pd.tanggalreservasi, 'dd. Mon' ) AS tanggal,
                    pd.tanggalreservasi AS tgl,
                    'belumdiperiksa' AS keterangan,
                    ( CASE WHEN ps.namapasien IS NULL THEN pd.namapasien ELSE ps.namapasien END ) AS namapasien,
                    1 AS rownum 
                FROM
                    perjanjianpasientr AS pd
                    LEFT JOIN pasienmt AS ps ON ps.ID = pd.normidfk 
                WHERE
                    pd.noreservasi <> '-' 
                    AND pd.aktif = TRUE 
                    AND pd.koders = 1 
                    AND pd.noreservasi IS NOT NULL 
                    AND pd.isconfirm IS NULL 
                    and pd.tanggalreservasi between '$now' and '$tanggal'
                    order by pd.tanggalreservasi"));
        $data = DB::select(DB::raw("
                 select * from (
                 SELECT
                     pd.norec,
                     pd.noregistrasi,
                     to_char (
                         pd.tglregistrasi,
                        'dd, Mon YYYY'
                     ) AS tglregistrasi,
                     to_char (pd.tglregistrasi, 'dd. Mon') AS tanggal,
                     pd.tglregistrasi AS tgl,
                     CASE
                 WHEN  apd.ispelayananpasien IS NOT NULL  and pd.aktif  =true THEN
                     'sudahdiperiksa'
                 WHEN  apd.ispelayananpasien IS NULL  and  pd.aktif  =true THEN
                     'belumdiperiksa'
                 WHEN pd.aktif  =false THEN
                     'batalregis'
                 END AS keterangan,
                  ps.namapasien
                 ,
                 row_number() over (partition by pd.noregistrasi order by apd.tglmasuk desc) as rownum
                 FROM
                     registrasipasientr AS pd
                 inner join daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                 INNER JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk
                 INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk
                 WHERE pd.koders = $idProfile and
                     pd.tglregistrasi BETWEEN  '$tglAwal'
                 AND '$tglAkhir'
                 AND ru.instalasiidfk = $idDepRaJal
                 ) as x where x.rownum=1
                 ORDER BY
                 x.noregistrasi
         "));
        $data = array_merge($data,$reser);
//        dd($data);
        $data10 = [];
        $sudahPeriksa = 0;
        $belumPeriksa = 0;
        $batalRegis = 0;
        $totalAll = 0;
        if (count($data) > 0) {
            foreach ($data as $item) {
                $sama = false;
                $i = 0;
                foreach ($data10 as $hideung) {
                    if ($item->tglregistrasi == $data10[$i]['tglregistrasi']) {
                        $sama = 1;
                        $jml = (float)$hideung['totalterdaftar'] + 1;
                        $data10[$i]['totalterdaftar'] = $jml;
                        if ($item->keterangan == 'sudahdiperiksa') {
                            $data10[$i]['diperiksa'] = (float)$hideung['diperiksa'] + 1;
                        }
                        if ($item->keterangan == 'belumdiperiksa') {
                            $data10[$i]['belumperiksa'] = (float)$hideung['belumperiksa'] + 1;
                        }
                        if ($item->keterangan == 'batalregis') {
                            $data10[$i]['batalregistrasi'] = (float)$hideung['batalregistrasi'] + 1;
                        }
                    }
                    $i = $i + 1;
                }
                if ($sama == false) {
                    if ($item->keterangan == 'sudahdiperiksa') {
                        $sudahPeriksa = 1;
                        $belumPeriksa = 0;
                        $batalRegis = 0;
                    }
                    if ($item->keterangan == 'belumdiperiksa') {
                        $sudahPeriksa = 0;
                        $belumPeriksa = 1;
                        $batalRegis = 0;
                    }
                    if ($item->keterangan == 'batalregis') {
                        $sudahPeriksa = 0;
                        $belumPeriksa = 0;
                        $batalRegis = 1;
                    }
                    $data10[] = array(
                        'tglregistrasi' => $item->tglregistrasi,
                        'tanggal' => $item->tanggal,
                        'totalterdaftar' => 1,
                        'diperiksa' => $sudahPeriksa,
                        'belumperiksa' => $belumPeriksa,
                        'batalregistrasi' => $batalRegis,

                    );
                }
                // foreach ($data10 as $key => $row) {
                //     $count[$key] = $row['totalterdaftar'];
                // }
                // array_multisort($count, SORT_DESC, $data10);
            }
        }
        $x=0;
        foreach ($tgl as $tgls){
            foreach ($data10 as $d){
                if($tgls['tglregistrasi'] == $d['tglregistrasi'] ){
                    $tgl[$x]['tanggal']  =$d['tanggal'];
                    $tgl[$x]['totalterdaftar']  =$d['totalterdaftar'];
                    $tgl[$x]['diperiksa']  =$d['diperiksa'];
                    $tgl[$x]['belumperiksa']  =$d['belumperiksa'];
                    $tgl[$x]['batalregistrasi']  =$d['batalregistrasi'];
                }
            }
            $x++;
        }
//        dd($tgl);
        return $tgl;
//         $minggu = Carbon::now()->subDay(14)->format('Y-m-d');
//        $tanggal = Carbon::now()->addDay(1)->format('Y-m-d');
//
//        $begin = new \DateTime($minggu);
//        $end = new \DateTime($tanggal);
//        $interval = \DateInterval::createFromDateString('1 day');
//        $period = new \DatePeriod($begin, $interval, $end);
//
//        $set = explode(',', $this->settingDataFixed('kdDeptEis', $kdProfile));
//        $kdDept = [];
//        foreach ($set as $s) {
//            $kdDept [] = (int)$s;
//        }
//        $deptDa = DB::table('instalasimt')->whereIN('id', $kdDept)->where('aktif', true)->get();
////        dd($deptDa);
//        $i = 0;
//        $color = ['#FF6384', '#4BC0C0', '#FFCE56', '#36A2EB'];
//        $chart = [];
//        foreach ($deptDa as $d) {
//            $array2 = [];
//            foreach ($period as $dt) {
//                $array2[] = array(
//                    'tgl' => $dt->format("Y-m-d"),
//                );
//            }
//            $chart [] = array(
//                'label' => $d->namadepartemen,
//                'backgroundColor' => $color[$i],
//                'borderColor' => '#1E88E5',
//                'data' => [],
//                'detailtgl' => $array2
//            );
//            $i++;
//        }
////        dd($chart);
////
//        $cat = [];
//        foreach ($period as $dt) {
//            $cat[] = $dt->format("d F Y");
//        }
//
//        $data = DB::select(DB::raw("
//            select x.tgl,count(x.namadepartemen) as jml,x.namadepartemen from (SELECT
//                to_char(
//                    rp.tglregistrasi,
//                    'yyyy-mm-dd'
//                ) AS tgl,
//                ins.namadepartemen
//            FROM
//                registrasipasientr AS rp
//            INNER JOIN ruanganmt AS ru ON ru.id = rp.ruanganlastidfk
//            INNER JOIN instalasimt AS ins ON ins.id = ru.instalasiidfk
//            WHERE
//                rp.tglregistrasi BETWEEN '$minggu'
//            AND '$tanggal'
//            AND rp.aktif = true
//            ) as x
//            GROUP BY x.tgl,x.namadepartemen"));
//        // dd($data);
//        $i = 0;
//        foreach ($chart as $c) {
//            foreach ($data as $d) {
//                if ($c['label'] == $d->namadepartemen) {
//                    foreach ($c['detailtgl'] as $t) {
//                        $chart[$i]['data'][] = 0;
//                        if ($t['tgl'] == $d->tgl) {
//                            $chart[$i]['data'][] = (float)$d->jml;
//                        }
//                    }
//                    break;
//                }
//            }
//            $i++;
//
//        }
//        $data2 = [];
//        $sama = false;
//
//        $res['labels'] = $cat;
//        $res['datasets'] = $chart;
////         dd($res);
//        return $res;

    }

    public function getTotalKlaim($noregistrasi, $kdProfile)
    {
        $pelayanan = collect(\DB::select("select sum(x.totalppenjamin) as totalklaim
         from (select spp.norec,spp.totalppenjamin
         from pasiendaftar_t as pd
            join antrianpasiendiperiksa_t as apd on apd.noregistrasifk=pd.norec
            join pelayananpasien_t as pp on pp.noregistrasifk =apd.norec
            join strukpelayanan_t as sp on sp.norec= pp.strukfk
            join strukpelayananpenjamin_t as spp on spp.nostrukfk=sp.norec
            where pd.noregistrasi ='$noregistrasi'
        and spp.statusenabled is null 
        and pd.kdprofile=$kdProfile
        GROUP BY spp.norec,spp.totalppenjamin

        ) as x"))->first();
        if (!empty($pelayanan) && $pelayanan->totalklaim != null) {
            return (float)$pelayanan->totalklaim;
        } else {
            return 0;
        }


    }

    public function getTotolBayar($noregistrasi, $kdProfile)
    {
        $pelayanan = collect(\DB::select("select sum(x.totaldibayar) as totaldibayar
         from (select sbm.norec,sbm.totaldibayar
         from pasiendaftar_t as pd
        join antrianpasiendiperiksa_t as apd on apd.noregistrasifk=pd.norec
        join pelayananpasien_t as pp on pp.noregistrasifk =apd.norec
        join strukpelayanan_t as sp on sp.norec= pp.strukfk
        join strukbuktipenerimaan_t as sbm on sbm.nostrukfk = sp.norec
        where pd.noregistrasi ='$noregistrasi'
        and sp.statusenabled is null 
        and sbm.statusenabled =true
        and pd.kdprofile=$kdProfile
        GROUP BY sbm.norec,sbm.totaldibayar

        ) as x"))->first();
        if (!empty($pelayanan) && $pelayanan->totaldibayar != null) {
            return (float)$pelayanan->totaldibayar;
        } else {
            return 0;
        }


    }

    protected function getDepositPasien($noregistrasi)
    {
        $produkIdDeposit = $this->getProdukIdDeposit();
        $deposit = 0;
        $pasienDaftar = PasienDaftar::has('pelayanan_pasien')->where('noregistrasi', $noregistrasi)->first();
        if ($pasienDaftar) {
            $depositList = $pasienDaftar->pelayanan_pasien()->where('nilainormal', '-1')->whereNull('strukfk')->get();
            foreach ($depositList as $item) {
                if ($item->produkfk == $produkIdDeposit) {
                    $deposit = $deposit + $item->hargasatuan;
                }
            }
        }
        return $deposit;
    }

    public function getDataDashboard($r)
    {
        $colors = $this->getColor();
        $tglakhir = date('Y-m-d');
        $tglawal = Carbon::now()->subWeek(1)->format('Y-m-d');
        if (isset($r['tglawal'])) {
            $tglawal = $r['tglawal'];
        }
        if (isset($r['tglakhir'])) {
            $tglakhir = $r['tglakhir'];
        }


        $data = \DB::select(DB::raw("select * from (select count(x.kddiagnosa) as jumlah,x.kddiagnosa,x.namadiagnosa
                from (
                select pm.kddiagnosa,pm.namadiagnosa,ps.namapasien,al.alamatlengkap,
                prov.provinsi,kot.kotakabupaten
                from pelayananmedis_t as pm
                inner join pasien_m as ps on pm.pasienfk= ps.id
                inner join alamat_m as al on ps.id = al.pasienfk
                inner join provinsi_m as prov on prov.id = al.provinsifk
                inner join kotakabupaten_m as kot on kot.id = al.kotakabupatenfk
                where pm.kddiagnosa is not null
                and pm.tglregistrasi between '$tglawal 00:00' and '$tglakhir 23:59'

                ) as x GROUP BY x.namadiagnosa) as z
                order by z.jumlah desc limit 10"));
        $map = [];
        foreach ($data as $key => $value) {
            $data[$key]->color = $colors[$key];
            # code...
        }
        // dd($data);
//        $umur = \DB::select(\DB::raw("select count(x.rangeumur) as jumlah,x.rangeumur from (
//            select pm.kddiagnosa,pm.namadiagnosa,ps.namapasien,al.alamatlengkap,
//            prov.provinsi,kot.kotakabupaten,ps.tgllahir,
//            case when TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) <= 1 then 'Bayi : < 1 tahun'
//            when  TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) >= 2 and TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) <=5 then 'Balita : >=2 & <=5 Tahun '
//            when  TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) > 5 and TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) <=12 then 'Anak : > 5 & <=12 Tahun'
//            when  TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) > 12 and TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) <=50 then 'Dewasa : >12 & <=50 Tahun'
//            when  TIMESTAMPDIFF(YEAR, ps.tgllahir, CURDATE()) > 50  then 'Geriatri : >50 Tahun'  end as rangeumur
//
//            from pelayananmedis_t as pm
//            inner join pasien_m as ps on pm.pasienfk= ps.id
//            inner join alamat_m as al on ps.id = al.pasienfk
//            inner join provinsi_m as prov on prov.id = al.provinsifk
//            inner join kotakabupaten_m as kot on kot.id = al.kotakabupatenfk
//            where pm.kddiagnosa is not null
//           /*
//            and DATE_FORMAT(pm.tglregistrasi, '%Y-%m')='$now'
//            */
//            ) as x
//            group by x.rangeumur"));
        $kddiagnosa = '';
        if (count($data) > 0) {
            $kddiagnosa = $data[0]->kddiagnosa;
            $map = \DB::select(DB::raw("
					select * from (select count(x.provinsi) as jumlah,x.provinsi,x.kdmap
					from (
					select pm.kddiagnosa,pm.namadiagnosa,ps.namapasien,al.alamatlengkap,
					prov.provinsi,kot.kotakabupaten,prov.kdmap
					from pelayananmedis_t as pm
					inner join pasien_m as ps on pm.pasienfk= ps.id
					inner join alamat_m as al on ps.id = al.pasienfk
					inner join provinsi_m as prov on prov.id = al.provinsifk
					inner join kotakabupaten_m as kot on kot.id = al.kotakabupatenfk
					where pm.kddiagnosa is not null
					and pm.kddiagnosa ='$kddiagnosa'

					) as x GROUP BY x.provinsi,x.kdmap) as z
					order by z.jumlah desc "));
        }

        $result['listdiagnosa'] = $data;
        $result['map'] = $map;
        $result['kddiagnosa'] = $kddiagnosa;
        $result['umur'] = [];//$umur;
        $result['tglawal'] = $tglawal;
        $result['tglakhir'] = $tglakhir;
        return $result;
//        return view('dashboard.pelayanan', compact('data', 'map', 'kddiagnosa', 'umur'));

    }

    public static function getColor()
    {
        $colors = [
            "#FF6384", "#4BC0C0", "#FFCE56",
            "#ffff9c", "#36A2EB", '#7cb5ec', '#75b2a3', '#9ebfcc', '#acdda8', '#d7f4d2', '#ccf2e8',
            '#468499', '#088da5', '#00ced1', '#3399ff', '#00ff7f',
            '#b4eeb4', '#a0db8e', '#999999', '#6897bb', '#0099cc', '#3b5998',
            '#000080', '#191970', '#8a2be2', '#31698a', '#87ff8a', '#49e334',
            '#13ec30', '#7faf7a', '#408055', '#09790e'
        ];
        return $colors;
    }

    public function showDataPegawai(Request $r)
    {
        $listJk = DB::table('jeniskelamnimt')
            ->select('*')
            ->where('statusenabled', true)
            ->get();
        $listJP = DB::table('jenispegawaimt')
            ->select('*')
            ->where('statusenabled', true)
            ->orderBy('jenispegawai')
            ->get();
        $listPangkat = DB::table('pangkat_m')
            ->select('*')
            ->where('statusenabled', true)
            ->orderBy('pangkat')
            ->get();
        $listPdd = DB::table('pendidikan_m')
            ->select('*')
            ->where('statusenabled', true)
            ->orderBy('pendidikan')
            ->get();
        $listJB = DB::table('jabatan_m')
            ->select('*')
            ->where('statusenabled', true)
            ->orderBy('jabatan')
            ->get();

        if (isset($r->id) && $r->id != null) {
            $valueEdit = DB::table('pegawaimt as pg')
                ->leftJoin('jeniskelamnimt as jk', 'jk.id', 'pg.objectjeniskelaminfk')
                ->leftJoin('jabatan_m as jb', 'jb.id', 'pg.objectjabatanfk')
                ->leftJoin('pendidikan_m as pdd', 'pdd.id', 'pg.objectpendidikanfk')
                ->leftJoin('pangkat_m as pn', 'pn.id', 'pg.objectpangkatfk')
                ->leftJoin('jenispegawaimt as jp', 'jp.id', 'pg.objectjenispegawaifk')
                ->select('pg.*', 'jk.jeniskelamin', 'jb.jabatan', 'pdd.pendidikan', 'pn.pangkat', 'jp.jenispegawai')
                ->where('pg.id', $r->id)
                ->first();
//            dd($valueEdit);
            return view('module.user.pegawai.assets.add-pegawai', compact('valueEdit',
                'listJk', 'listJB', 'listPdd', 'listPangkat', 'listJP'));
        } else {
            return view('module.user.pegawai.assets.add-pegawai', compact('r',
                'listJk', 'listJB', 'listPdd', 'listPangkat', 'listJP'));
        }
    }

    public function hapusPegawai(Request $r)
    {
        DB::beginTransaction();
        try {

            Pegawai::where('id', $r['id'])->delete();
            DB::commit();
            session()->flash('type', "success");
            session()->flash('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', "error");
            session()->flash('message', 'Data gagal dihapus');
        }

        return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => "pegawai"]);

    }

    public function savePegawai(Request $r)
    {
        DB::beginTransaction();
        try {
//            dd($r->input());
            $profile = DB::table('user_m')->where('id', $_SESSION['id'])->first();
//            dd($profile);
            $data = $r->input();
            if ($data['id'] == null) {
                $saveData = new Pegawai();
                $saveData->id = Pegawai::max('id') + 1;
                $saveData->statusenabled = true;
            } else {
                $saveData = Pegawai::where('id', $data['id'])->first();
            }
            $saveData->profilefk = $profile->profilefk;
            $saveData->namalengkap = $data['namalengkap'];
            $saveData->objectjeniskelaminfk = $data['jk'];
            $saveData->tgllahir = $data['tgllahir'];
            $saveData->tempatlahir = $data['tempatlahir'];
            $saveData->nip = $data['nip'];
            $saveData->objectpendidikanfk = $data['pendidikan'];
            $saveData->objectjabatanfk = $data['jabatan'];
            $saveData->objectpangkatfk = $data['pangkat'];
            $saveData->tglmasuk = $data['tglmasuk'];
            $saveData->tglkeluar = $data['tglkeluar'];
            $saveData->objectjenispegawaifk = $data['jenispegawai'];
            $saveData->tglupdate = date('Y-m-d H:i:s');
            $saveData->save();

            DB::commit();
            session()->flash('type', "success");
            session()->flash('message', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', "error");
            session()->flash('message', 'Data gagal disimpan');
        }

        return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => "pegawai"]);

    }

    public function showDataBed(Request $r)
    {

        $listKelas = DB::table('kelas_m')
            ->select('*')
            ->where('statusenabled', true)
            ->orderBy('namakelas')
            ->get();

        if (isset($r['id']) && $r['id'] != null) {
            $valueEdit = DB::table('ketersediaantempattidur_t as pg')
                ->join('kelas_m as jk', 'jk.id', 'pg.objectkelasfk')
                ->Join('profile_m as pr', 'pr.id', 'pg.profilefk')
                ->select('pg.*', 'jk.namakelas', 'pr.namaprofile')
                ->where('pg.norec', $r['id'])
                ->first();

//            dd($valueEdit);
            return view('module.user.bed.assets.add-bed', compact('valueEdit',
                'listKelas'));
        } else {
            return view('module.user.bed.assets.add-bed', compact('listKelas'));
        }
    }

    public function hapusBed(Request $r)
    {
        DB::beginTransaction();
        try {

            DB::table('ketersediaantempattidur_t')->where('norec', $r['id'])->delete();
//            session()->flash('message',"Incorrect username or password");
            toastr()->success('Data Delete Succesfully !', 'Info');
            DB::commit();
            session()->flash('type', "success");
            session()->flash('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', "error");
            session()->flash('message', 'Data gagal dihapus');
        }

        return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => "bed"]);

    }

    public function saveBed(Request $r)
    {
        DB::beginTransaction();
        try {
//            dd($r->input());
            $profile = DB::table('user_m')->where('id', $_SESSION['id'])->first();
//            dd($profile);
            $data = $r->input();

            if ($data['id'] == null) {
                $cekData = DB::table('ketersediaantempattidur_t')
                    ->where('objectkelasfk', $data['kelas'])
                    ->where('profilefk', $profile->profilefk)
                    ->first();
                if (empty($cekData)) {
//                    $max =  DB::table('ketersediaantempattidur_t')->max('id');
                    DB::table('ketersediaantempattidur_t')->insert([
                        "norec" => substr(Uuid::generate(), 0, 32),
                        "statusenabled" => true,
                        "objectkelasfk" => $data['kelas'],
                        "profilefk" => $profile->profilefk,
                        "kapasitas" => $data['kapasitas'],
                        "tersedia" => $data['tersedia'],
                        "tglupdate" => date('Y-m-d H:i:s')
                    ]);
                } else {
                    DB::table('ketersediaantempattidur_t')->where('norec', $cekData->norec)
                        ->update([
                            "objectkelasfk" => $data['kelas'],
                            "profilefk" => $profile->profilefk,
                            "kapasitas" => $data['kapasitas'],
                            "tersedia" => $data['tersedia'],
                            "tglupdate" => date('Y-m-d H:i:s')
                        ]);
                }
            } else {
                DB::table('ketersediaantempattidur_t')
                    ->where('norec', $data['id'])
                    ->update([
                        "objectkelasfk" => $data['kelas'],
                        "profilefk" => $profile->profilefk,
                        "kapasitas" => $data['kapasitas'],
                        "tersedia" => $data['tersedia'],
                        "tglupdate" => date('Y-m-d H:i:s')
                    ]);
            }
            DB::commit();
            session()->flash('type', "success");
            session()->flash('message', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', "error");
            session()->flash('message', 'Data gagal disimpan');
        }

        return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => "bed"]);

    }

    public function showDataStok(Request $r)
    {

        $listProduk = DB::table('produk_m')
            ->select('*')
            ->where('statusenabled', true)
            ->orderBy('namaproduk')
            ->get();
        $listSatuan = DB::table('satuanstandar_m')
            ->select('*')
            ->where('statusenabled', true)
            ->orderBy('satuanstandar')
            ->get();

        if (isset($r['norec']) && $r['norec'] != null) {
            $valueEdit = DB::table('transaksistok_t as pg')
                ->join('satuanstandar_m as jk', 'jk.id', 'pg.satuanstandarfk')
                ->Join('produk_m as prd', 'prd.id', 'pg.produkfk')
                ->Join('profile_m as pr', 'pr.id', 'pg.profilefk')
                ->select('pg.*', 'jk.satuanstandar', 'prd.namaproduk', 'pr.namaprofile')
                ->where('pg.norec', $r['norec'])
                ->first();

//            dd($valueEdit);
            return view('module.user.stok.assets.add-stok', compact('valueEdit',
                'listSatuan', 'listProduk'));
        } else {
            return view('module.user.stok.assets.add-stok', compact('listSatuan', 'listProduk'));
        }
    }

    public function hapusStok(Request $r)
    {
        DB::beginTransaction();
        try {

            DB::table('transaksistok_t')->where('norec', $r['norec'])->delete();
//            session()->flash('message',"Incorrect username or password");
            toastr()->success('Data Delete Succesfully !', 'Info');
            DB::commit();
            session()->flash('type', "success");
            session()->flash('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', "error");
            session()->flash('message', 'Data gagal dihapus');
        }

        return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => "stok"]);

    }

    public function saveStok(Request $r)
    {
        DB::beginTransaction();
        try {
//            dd($r->input());
            $profile = DB::table('user_m')->where('id', $_SESSION['id'])->first();
//            dd($profile);
            $data = $r->input();

            if ($data['norec'] == null) {
                $cekData = DB::table('transaksistok_t')
                    ->where('satuanstandarfk', $data['satuanstandar'])
                    ->where('produkfk', $data['produk'])
                    ->where('profilefk', $profile->profilefk)
                    ->first();
                if (empty($cekData)) {
                    DB::table('transaksistok_t')->insert([
                        "norec" => substr(Uuid::generate(), 0, 32),
                        "statusenabled" => true,
                        "produkfk" => $data['produk'],
                        "profilefk" => $profile->profilefk,
                        "satuanstandarfk" => $data['satuanstandar'],
                        "total" => $data['total'],
                        "tglupdate" => date('Y-m-d H:i:s')
                    ]);
                } else {
                    DB::table('transaksistok_t')->where('norec', $cekData->norec)
                        ->update([
                            "produkfk" => $data['produk'],
                            "profilefk" => $profile->profilefk,
                            "satuanstandarfk" => $data['satuanstandar'],
                            "total" => $data['total'],
                            "tglupdate" => date('Y-m-d H:i:s')
                        ]);
                }
            } else {
                DB::table('transaksistok_t')
                    ->where('norec', $data['norec'])
                    ->update([
                        "produkfk" => $data['produk'],
                        "profilefk" => $profile->profilefk,
                        "satuanstandarfk" => $data['satuanstandar'],
                        "total" => $data['total'],
                        "tglupdate" => date('Y-m-d H:i:s')
                    ]);
            }
            DB::commit();
            session()->flash('type', "success");
            session()->flash('message', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', "error");
            session()->flash('message', 'Data gagal disimpan');
        }

        return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => "stok"]);

    }

    public function getPasienMasihDirawat(Request $request)
    {

        $idProfile = (int)$_SESSION['kdProfile'];
        $filter = $request->all();
        $noreg = '';
        if (isset($filter['noReg']) && $filter['noReg'] != "" && $filter['noReg'] != "undefined") {
            $noreg = " AND pd.noregistrasi ilike '%" . $filter['noReg'] . "%'";
        }

        $norm = '';
        if (isset($filter['noRm']) && $filter['noRm'] != "" && $filter['noRm'] != "undefined") {
            $norm = " AND p.nocm ilike '%" . $filter['noRm'] . "%'";
        }
        $namaPasien = '';
        if (isset($filter['namaPasien']) && $filter['namaPasien'] != "" && $filter['namaPasien'] != "undefined") {
            $namaPasien = " AND p.namapasien ilike '%" . $filter['namaPasien'] . "%'";
        }
        $ruId = '';
        if (isset($filter['ruanganId']) && $filter['ruanganId'] != "" && $filter['ruanganId'] != "undefined") {
            $ruId = ' AND ru.id = ' . $filter['ruanganId'];
        }
        $jmlRow = '';
        if (isset($filter['jmlRow']) && $filter['jmlRow'] != "" && $filter['jmlRow'] != "undefined") {
            $jmlRow = ' limit ' . $filter['jmlRow'];
        }
        $data = DB::select(DB::raw("
        select  * from 
			                (
			select pd.tglregistrasi,
				p.nocm,
				pd.noregistrasi,
				ru.namaruangan,
				p.namapasien,
                kp.kelompokpasien,
                case when p.nobpjs is null then '-' else p.nobpjs end as nobpjs,
				kls.namakelas,
				alm.alamatlengkap,
				jk.jeniskelamin,
				pg.namalengkap AS namadokter,
				pd.norec AS norec_pd,
				pd.tglpulang,
				pd.statuspasien,
				p.tgllahir,
				pd.objectruanganlastfk,
				pd.objectkelasfk, apd.objectkamarfk,
				kmr.namakamar,apd.nobed,
				tt.reportdisplay as namabed,
				EXTRACT(day from age(current_date, to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Hari' as lamarawat,
				--,
			row_number() over (partition by pd.noregistrasi order by apd.tglmasuk desc) as rownum 
			FROM
				pasiendaftar_t AS pd
			INNER JOIN antrianpasiendiperiksa_t AS apd ON pd.norec = apd.noregistrasifk and apd.tglkeluar is null
			--inner join registrasipelayananpasien_t as rpp on rpp.noregistrasifk=pd.norec
			INNER JOIN pasien_m AS p ON p.id = pd.nocmfk
			INNER JOIN ruangan_m AS ru ON ru.id = pd.objectruanganlastfk
			INNER JOIN kelas_m AS kls ON kls.id = pd.objectkelasfk
			INNER JOIN jeniskelamnimt AS jk ON jk.id = p.objectjeniskelaminfk
			LEFT JOIN pegawaimt AS pg ON pg.id = pd.objectpegawaifk
			LEFT JOIN kelompokpasien_m AS kp ON kp.id = pd.objectkelompokpasienlastfk
			LEFT JOIN departemen_m AS dept ON dept.id = ru.objectdepartemenfk
			LEFT JOIN batalregistrasi_t AS br ON pd.norec = br.pasiendaftarfk
			left JOIN kamar_m as kmr on kmr.id =apd.objectkamarfk
			left JOIN tempattidur_m as tt on tt.id =apd.nobed
			left join alamat_m as alm on alm.nocmfk = p.id
			where br.norec is null 
			$noreg $norm $namaPasien $ruId
			and pd.tglpulang is null
			and pd.kdprofile = $idProfile	
			and pd.statusenabled=true
            $jmlRow
			) as x 
			where x.rownum=1
			order by x.tglregistrasi desc
		"));
        $result = array(
            'data' => $data,
            'message' => 'ramdanegie',
        );
        return view('module.shared.detail-kamar', compact('data'));
    }

    public function getSebaranPasienLama($kddiagnosa, Request $r)
    {
        $now = date('Y-m');
        $kdProfile = $_SESSION['kdProfile'];
        $map = \DB::select(DB::raw("
            select * from (
                select count(x.provinsi)as jumlah,x.provinsi,x.kdmap
                from (select dm.kddiagnosa,
					case when pro.namapropinsi is null then 'Banten' else pro.namapropinsi end as provinsi,
                dm.namadiagnosa,case when pro.namapropinsi is null then '4' else pro.kdmap end as kdmap
                from daftarpasienruangantr as app
                left join diagnosapasienicdxtr as dp on dp.daftarpasienruanganfk = app.norec
                inner join icdxmt as dm on dp.icdxidfk = dm.id
                inner join registrasipasientr as pd on pd.norec = app.registrasipasienfk
                inner join pasienmt as ps on ps.id = pd.normidfk
                left join alamatmt as alm on alm.normidfk = ps.id
                 left join kecamatanmt as kec on kec.id = alm.kecamatanidfk
                left join kotakabupatenmt as kot on kot.id = alm.kotakabupatenidfk
                left join provinsimt as pro on pro.id = alm.provinsiidfk
                left join ruanganmt as ru on ru.id = pd.ruanganlastidfk
                where app.koders = $kdProfile and dm.kddiagnosa <> '-'  and   pd.aktif=true and               
                pd.tglregistrasi BETWEEN '$r[tglawal] 00:00' and '$r[tglakhir] 23:59'
                and dm.kddiagnosa='$kddiagnosa'
                )as x GROUP BY x.provinsi ,x.kdmap 
                ) as z
                ORDER BY z.jumlah desc  limit 10
                 "));
        $data['map'] = $map;
        $data['kddiagnosa'] = $kddiagnosa;
        $dataMap = [];
        foreach ($map as $key => $m) {
            $dataMap [$m->kdmap] = (float)$m->jumlah;

        }
        return $dataMap;


    }

    public function saveMapKabupaten(Request $r)
    {
        // ini_set('max_execution_time', 300);
        $kota2 = DB::table('kotakabupatenmt')->where('aktif', true)->whereNull('lat')->whereNull('lng')->get();
        // return $kota2;
        $result['status'] = 200;
        $result['message'] = 'Sukses';
        foreach ($kota2 as $k) {

            $response = \Http::get('https://maps.googleapis.com/maps/api/geocode/json?address=' . $k->namakotakabupaten . '&key=AIzaSyDYhPXYjgCmT7ZO8jZigFm8iPXY_e16C8M');
            // data.results[0].geometry.location.lat, data.results[0].geometry.location.lng
            if ($response['status'] == 'OK') {
                // return $k->namakotakabupaten;
                $lat = $response['results'][0]['geometry']['location']['lat'];
                $lng = $response['results'][0]['geometry']['location']['lng'];
                DB::table('kotakabupatenmt')->where('aktif', true)->where('id', $k->id)
                    ->update(
                        [
                            'lat' => $lat,
                            'lng' => $lng,
                        ]
                    );
                $result['status'] = 200;
                $result['message'] = 'Sukses';
            } else {
                $result['status'] = 400;
                $result['message'] = $response['status'];

            }
        }

        // DB::table('kotakabupatenmt')->where('aktif',true)->where('id',$k->id)
        //             ->update(
        //                 [
        //                 'lat'=>$lat,
        //                 'lng'=>$lng,
        //                 ]
        //             );
        return $this->setStatusCode($result['status'])->respond($result, $result['message']);


    }

    public function getKotaMap()
    {
        $data = DB::select(DB::raw("select * from (select x.namakotakabupaten ,x.lat,x.lng,count(x.namakotakabupaten) as jumlah 
            from (
            SELECT
            case when kot.namakotakabupaten is null then 'Kota Bandung' else kot.namakotakabupaten end as namakotakabupaten,
            case when kot.lat is null then '-6.9174639' else kot.lat end as lat,
            case when kot.lng is null then '107.6191228' else kot.lng end as lng
            FROM
                pasienmt AS ps
            INNER JOIN alamatmt AS alm ON alm.normidfk = ps. ID
            LEFT JOIN kotakabupatenmt AS kot ON kot. ID = alm.kotakabupatenidfk
            LEFT JOIN provinsimt AS pro ON pro. ID = alm.provinsiidfk
            WHERE
                ps.koders = 1
            AND ps.aktif = TRUE
            ) as x
            group by x.namakotakabupaten ,x.lat,x.lng)  as z order by z.jumlah desc"));
        return $data;
    }

    public static function getRanapByJK($tglAwal, $tglAkhir, $kdProfile)
    {

        $idProfile = (int)$kdProfile;
        $depInap = 16;
        $tglAwal = $tglAwal . ' 00:00'; //Carbon::now()->startOfMonth()->subMonth(1);
        $tglAkhir = $tglAkhir . ' 23:59';
        $bulan = Carbon::now()->format('F');
        $idDepRanap = (int)static::settingDataFixed2('kdDepartemenRanapFix', $idProfile);
        $data = DB::select(DB::raw("SELECT
                        COUNT (z.jeniskelamin) AS jumlah,
                        z.jeniskelamin
                       FROM
                        (
                            select pd.noregistrasi, pd.tglregistrasi, ru.namaruangan, ru.id as kdruangan,jk.jeniskelamin
                            from registrasipasientr as pd 
                            left join ruanganmt as ru on ru.id = pd.ruanganlastidfk 
                            left join pasienmt as ps on ps.id = pd.normidfk 
                             left join jeniskelaminmt as jk on jk.id = ps.jeniskelaminidfk 
                            where pd.koders = $idProfile and ru.instalasiidfk = $idDepRanap 
                            and pd.aktif = true 
                            and (  pd.tglregistrasi < '$tglAwal' AND pd.tglpulang >= '$tglAkhir' 
                           )
                            or pd.tglpulang is null
                            and pd.koders = $idProfile and ru.instalasiidfk = $idDepRanap 
                            and pd.aktif = true 
                       
                           
                        ) AS z
                    GROUP BY
                        z.jeniskelamin
            "));

        $result = array(
            'result' => $data,
            'month' => $bulan,
            'jml' => count($data),
            'message' => 'ramdanegie',
        );
        return $data;
    }

    public static function getTopTenDPJP($tglAwal, $tglAkhir, $kdProfile)
    {
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $result = DB::select(DB::raw("SELECT
                *
            FROM
                (
                    SELECT
                        COUNT (case when dp.namalengkap is null then '-' else dp.namalengkap end) AS jml,
                        case when dp.namalengkap is null then '-' else dp.namalengkap end AS nama
                    FROM
                        registrasipasientr AS pd
                    LEFT JOIN pegawaimt AS dp ON dp. ID = pd.pegawaiidfk
                    WHERE
                        pd.koders = $kdProfile
                    AND pd.aktif = TRUE
                    AND pd.tglregistrasi BETWEEN '$tglAwal'
                    AND '$tglAkhir'
                    GROUP BY
                        dp.namalengkap
                ) AS x
            ORDER BY
                x.jml DESC
            LIMIT 10"));
        return $result;
    }

    public static function getTopTenPoli($tglAwal, $tglAkhir, $kdProfile)
    {
        $tglAwal = $tglAwal . ' 00:00';
        $tglAkhir = $tglAkhir . ' 23:59';
        $idDepRaJal = static::settingDataFixed2('kdDepartemenRajal', $kdProfile);
        $result = DB::select(DB::raw("SELECT
                *
            FROM
                (
                    SELECT
                        COUNT (case when dp.namaruangan is null then '-' else dp.namaruangan end) AS jml,
                        case when dp.namaruangan is null then '-' else dp.namaruangan end AS nama
                    FROM
                        registrasipasientr AS pd
                    LEFT JOIN ruanganmt AS dp ON dp. ID = pd.ruanganlastidfk
                    WHERE
                        pd.koders = $kdProfile
                    and dp.instalasiidfk in ($idDepRaJal)
                    AND pd.aktif = TRUE
                    AND pd.tglregistrasi BETWEEN '$tglAwal'
                    AND '$tglAkhir'
                    GROUP BY
                        dp.namaruangan
                ) AS x
            ORDER BY
                x.jml DESC
            LIMIT 10"));

        return $result;
    }
}
