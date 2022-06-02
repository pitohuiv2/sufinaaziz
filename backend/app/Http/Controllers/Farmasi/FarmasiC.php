<?php


namespace App\Http\Controllers\Farmasi;


use App\Http\Controllers\ApiController;
use App\Master\JenisKelamin;
use App\Traits\InternalList;
use App\Traits\PelayananPasienTrait;
use App\Traits\SettingDataFixedTrait;
use App\Traits\Valet;
use App\Transaksi\AntrianApotik;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\TransaksiKartuStok;
use App\Transaksi\LoggingUser;
use App\Transaksi\TransaksiOrder;
use App\Transaksi\RegistrasiPasien;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\TransaksiPasienDetail;
use App\Transaksi\PelayananPasienObatKronis;
use App\Transaksi\PetugasPelaksana;
use App\Transaksi\PelayananPasienRetur;
use App\Transaksi\TransaksiStok;
use App\Transaksi\StrukPelayanan;
use App\Transaksi\StrukPelayananDetail;
use App\Transaksi\TransaksiResep;
use App\Transaksi\TransaksiRetur;
use App\Transaksi\TransaksiReturDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmasiC  extends ApiController
{
    use Valet, PelayananPasienTrait, SettingDataFixedTrait, InternalList;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getDataComboFarmasi(Request $request)
    {
        $JENISPROFILE = $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->join('pegawaimt as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select('lu.objectpegawaifk', 'pg.namalengkap')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', (int)$JENISPROFILE)
            ->first();

        $dataInstalasi = \DB::table('instalasimt as dp')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$JENISPROFILE)
            ->orderBy('dp.namadepartemen')
            ->get();

        $dataRuangan = \DB::table('ruanganmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$JENISPROFILE)
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

        $dataKelas = \DB::table('kelasmt as kl')
            ->select('kl.id', 'kl.namakelas')
            ->where('kl.aktif', true)
            ->orderBy('kl.id')
            ->get();

        $deptLayanan = explode(',', $this->settingDataFixed('kdDepartemenLayanan', $JENISPROFILE));
        $kdDepartemenLayanan = [];
        foreach ($deptLayanan as $item) {
            $kdDepartemenLayanan[] = (int)$item;
        }

        $dataKelompok = \DB::table('kelompokpasienmt as kp')
            ->select('kp.id', 'kp.kelompokpasien')
            ->where('kp.aktif', true)
            ->orderBy('kp.kelompokpasien')
            ->get();

        $dataRuanganLayanan = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$JENISPROFILE)
            ->whereIn('ru.instalasiidfk', $kdDepartemenLayanan)
            ->orderBy('ru.namaruangan')
            ->get();

        $kdFarmasi = (int) $this->settingDataFixed('kdDepartemenFarmasi', $JENISPROFILE);
        $dataRuanganFarmasi = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$JENISPROFILE)
            ->where('ru.instalasiidfk', $kdFarmasi)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataStatusPengerjaan = \DB::table('statuspengerjaanmt as ru')
            ->select('ru.id', 'ru.statuspengerjaan as status')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$JENISPROFILE)
            ->orderBy('ru.id')
            ->get();
         $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter', $JENISPROFILE);
        $dataDokter = \DB::table('pegawaimt as ru')
            ->select('ru.id', 'ru.namalengkap')
            ->where('ru.aktif', true)
            ->where('ru.objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->where('ru.koders', (int)$JENISPROFILE)
            ->orderBy('ru.namalengkap')
            ->get();

        $jk = JenisKelamin::where('aktif', true)
            ->select(DB::raw("id, UPPER(jeniskelamin) as jeniskelamin"))
            ->where('koders', $JENISPROFILE)
            ->get();

        $result = array(
            'dataLogin' => $dataPegawai,
            'departemen' => $dataDepartemen,
            'kelompokpasien' => $dataKelompok,
            'kelas' => $dataKelas,
            'ruanglayanan' => $dataRuanganLayanan,
            'ruangfarmasi' => $dataRuanganFarmasi,
            'kelompokpasien' => $dataKelompok,
            'statuspengerjaan' => $dataStatusPengerjaan,
            'dokter' => $dataDokter,
            'datalogin' => $dataLogin,
            'jeniskelamin' => $jk,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDaftarOrderResep(Request $request)
    {
        $JENISPROFILE = (int) $this->getDataKdProfile($request);
        $kdJenisTrasnsaksiResep = (int) $this->settingDataFixed('kdKelompokTransaksiOrderResep', $JENISPROFILE);
        $tglAwal = $request['tglAwal'];
        $tglAkhir = $request['tglAkhir'];
        $nocmOrder = '';
        $Noregis = '';
        $namaPasienOrder = '';
        $noPesananOrder = '';
        $norec_apdOrder = '';
        $ruanganIdOrder = '';
        $ruanganIdResep = '';
        $depoIdIdOrder = '';
        $depoIdIdResep = '';
        $departemenIdOrder = '';
        $statusIdOrder = '';
        $statusIdResep = '';
        if (isset($request['nocm']) && $request['nocm'] != "" && $request['nocm'] != "undefined") {
            $nocmOrder =  "and ps.norm ilike '%" . $request['nocm'] . "%'";
        }
        if (isset($request['norec_apd']) && $request['norec_apd'] != "" && $request['norec_apd'] != "undefined") {
            $norec_apdOrder = " and apd.norec = " . $request['norec_apd'];
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != "" && $request['noregistrasi'] != "undefined") {
            $Noregis = " and pd.noregistrasi ilike '%" . $request['noregistrasi'] . "%'";
        }
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $namaPasienOrder = " and ps.namapasien ilike '%" . $request['namaPasien'] . "%'";
        }
        if (isset($request['noPesanan']) && $request['noPesanan'] != "" && $request['noPesanan'] != "undefined") {
            $noPesananOrder =  "and so.noorder ilike '%" . $request['noPesanan'] . "%'";
        }
        if (isset($request['ruanganId']) && $request['ruanganId'] != "" && $request['ruanganId'] != "undefined") {
            $ruanganIdOrder = " and so.ruanganidfk = " . $request['ruanganId'];
            $ruanganIdResep = " and apd.ruanganidfk = " . $request['ruanganId'];
        }
        if (isset($request['depoId']) && $request['depoId'] != "" && $request['depoId'] != "undefined") {
            $depoIdIdOrder = " and so.ruangantujuanidfk = " . $request['depoId'];
            $depoIdIdResep = " and sr.ruanganidfk = " . $request['depoId'];
        }
        if (isset($request['statusId']) && $request['statusId'] != "" && $request['statusId'] != "undefined") {
            $arrStatus = explode(',', $request['statusId']);
            $kode = [];
            foreach ($arrStatus as $item) {
                $kode[] = (int) $item;
            }
            $statusIdOrder = " and so.statusorder in (" .$request['statusId'].")";
            $statusIdResep = " and sr.status in (" .$request['statusId'].")";
        }

        $data = \DB::select(DB::raw("
              
                 SELECT so.norec AS norec_order,so.noorder,ps.norm,ps.namapasien,jk.jeniskelamin,ru.namaruangan AS namaruanganrawat,
                        so.tglorder,pg.namalengkap,ru2.namaruangan,so.statusorder,so.namapengambilorder,so.registrasipasienfk,pd.noregistrasi,
                        kp.kelompokpasien,apd.norec AS norec_apd,pd.tglregistrasi,ps.tgllahir,kl.namakelas,kl. ID AS klid,so.tglambilorder,
                        sr.noresep,aa.noantri AS aanoantri,aa.jenis AS aajenis,sr.norec AS norecresep,sr.noresep,so.nourutruangan AS noruangan,
                        so.isreseppulang as checkreseppulang,pd.tglpulang,sps.statuspengerjaan AS status,alm.alamatlengkap as alamat,sr.tglresep,
                        pd.statusschedule as noreservasi
                 FROM transaksiordertr AS so
                 INNER JOIN pasienmt AS ps ON ps.id = so.normidfk
                 INNER JOIN jeniskelaminmt AS jk ON jk.id = ps.jeniskelaminidfk
                 INNER JOIN ruanganmt AS ru ON ru.id = so.ruanganidfk
                 INNER JOIN ruanganmt AS ru2 ON ru2.id = so.ruangantujuanidfk
                 LEFT JOIN pegawaimt AS pg ON pg.id = so.pegawaiorderidfk
                 LEFT JOIN transaksireseptr AS sr ON sr.transaksiorderfk = so.norec and sr.aktif=true
                 LEFT JOIN antrianapotiktr AS aa ON aa.noresep = sr.noresep
                 INNER JOIN registrasipasientr AS pd ON pd.norec = so.registrasipasienfk
                 INNER JOIN kelasmt AS kl ON kl.id = pd.kelasidfk
                 LEFT JOIN daftarpasienruangantr AS apd ON apd.registrasipasienfk = pd.norec AND apd.ruanganidfk = so.ruanganidfk
                 LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk
                 LEFT JOIN statuspengerjaanmt AS sps ON sps.id = so.statusorder
                 LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id
                 WHERE so.koders = $JENISPROFILE AND so.tglorder >= '$tglAwal' AND so.tglorder <= '$tglAkhir'
                 AND so.keteranganorder = 'Order Farmasi' AND so.kelompoktransaksiidfk = $kdJenisTrasnsaksiResep AND so.aktif = 't'
                 $nocmOrder
                 $Noregis
                 $namaPasienOrder
                 $noPesananOrder
                 $norec_apdOrder
                 $ruanganIdOrder
                 $depoIdIdOrder
                 $departemenIdOrder
                 $statusIdOrder

                 UNION ALL

                 SELECT  sr.norec AS norec_order,sr.noresep AS noorder,ps.norm,ps.namapasien,jk.jeniskelamin,ru.namaruangan AS namaruanganrawat,
                        sr.tglresep AS tglorder,pg.namalengkap,ru2.namaruangan,sr.status  AS statusorder,
                        sr.namalengkapambilresep AS namapengambilorder,pd.norec AS registrasipasienfk,pd.noregistrasi,kp.kelompokpasien,
                        apd.norec AS norec_apd,pd.tglregistrasi,ps.tgllahir,kl.namakelas,kl. ID AS klid,sr.tglambilresep AS tglambilorder,
                        sr.noresep,aa.noantri AS aanoantri,aa.jenis AS aajenis,sr.norec AS norecresep,sr.noresep,NULL AS noruangan,
                        sr.isreseppulang as checkreseppulang,pd.tglpulang,case when sps.statuspengerjaan  is null then 'Verifikasi' else sps.statuspengerjaan end AS status,alm.alamatlengkap as alamat,sr.tglresep, '' as noreservasi
                FROM transaksireseptr AS sr
                INNER JOIN daftarpasienruangantr AS apd ON apd.norec = sr.daftarpasienruanganfk
                INNER JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk
                INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk
                INNER JOIN jeniskelaminmt AS jk ON jk.id = ps.jeniskelaminidfk
                INNER JOIN ruanganmt AS ru ON ru.id = apd.ruanganidfk
                INNER JOIN ruanganmt AS ru2 ON ru2.id = sr.ruanganidfk
                INNER JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk
                LEFT JOIN pegawaimt AS pg ON pg.id = sr.penulisresepidfk
                LEFT JOIN antrianapotiktr AS aa ON aa.noresep = sr.noresep
                INNER JOIN kelasmt AS kl ON kl.id = pd.kelasidfk
                LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk
                LEFT JOIN statuspengerjaanmt AS sps ON sps.id = sr.status
                LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id
                WHERE sr.koders = $JENISPROFILE AND sr.tglresep  >= '$tglAwal' AND sr.tglresep <= '$tglAkhir'
                AND sr.aktif = 't' AND sr.transaksiorderfk IS NULL
                $nocmOrder
                $Noregis
                $namaPasienOrder
                $norec_apdOrder
                $ruanganIdResep
                $depoIdIdResep
                $departemenIdOrder
                $statusIdResep
             
        "));

        $status = '';
        $jenis = '';
        $result = [];
        foreach ($data as $item) {

            if ($item->aajenis == 'R') {
                $jenis = 'Racikan';
            } else if ($item->aajenis == 'N') {
                $jenis = 'Non Racikan';
            } else {
                $jenis = '-';
            };
            $item->class = 'p-tag p-tag-secondary';
            if ($item->status == 'Menunggu') {
                $item->class = 'p-tag p-tag-danger';
            }else if ($item->status == 'Verifikasi') {
                $item->class = 'p-tag p-tag-warning';
            }else if ($item->status == 'Produksi') {
                $item->class = 'p-tag p-tag-info';
            }else if ($item->status == 'Packaging') {
                $item->class = 'p-tag p-tag-help';
            }else if ($item->status == 'Selesai') {
                $item->class = 'p-tag p-tag-primary';
            }else if ($item->status == 'Sudah Di Ambil') {
                $item->class = 'p-tag p-tag-success';
            }else if ($item->status == 'Penyerahan Obat') {
                $item->class = 'p-tag p-tag-secondary';
            }
            $result[] = array(
                'norec_order' => $item->norec_order,
                'noregistrasi' => $item->noregistrasi,
                'norec_pd' => $item->registrasipasienfk,
                'tglregistrasi' => $item->tglregistrasi,
                'norec_apd' => $item->norec_apd,
                'noorder' => $item->noorder,
                'norm' => $item->norm,
                'namapasien' => $item->namapasien,
                'jeniskelamin' => $item->jeniskelamin,
                'namaruanganrawat' => $item->namaruanganrawat,
                'tglorder' => $item->tglorder,
                'namalengkap' => $item->namalengkap,
                'kelompokpasien' => $item->kelompokpasien,
                'namaruangan' => $item->namaruangan,
                'statusorder' => $item->status,
                'namapengambilorder' => $item->namapengambilorder,
                'tgllahir' => $item->tgllahir,
                'klid' => $item->klid,
                'namakelas' => $item->namakelas,
                'noresep' => $item->noresep,
                'norecresep' => $item->norecresep,
                'noantri' =>  $item->aajenis . '-' . $item->aanoantri,
                'noresep' => $item->noresep,
                'jenis' => $jenis,
                'noruangan' => $item->noruangan,
                'checkreseppulang' => $item->checkreseppulang,
                'tglpulang' => $item->tglpulang,
                'alamat' => $item->alamat,
                'tglresep' => $item->tglresep,
                'noreservasi' => $item->noreservasi,
                'class' => $item->class,
            );
        }

        return $this->respond($result);
    }

    public function getDetailOrderResep(Request $request)
    {
        $JENISPROFILE = $this->getDataKdProfile($request);
        $idProfile = (int) $JENISPROFILE;
        $dataAsalProduk = \DB::table('asalprodukmt as ap')
            ->select('ap.id', 'ap.asalproduk')
            ->get();
        $dataStruk = \DB::table('transaksiordertr as so')
            ->JOIN('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'so.ruangantujuanidfk')
            ->select('so.noorder', 'pg.id as pgid', 'pg.namalengkap', 'ru.id', 'ru.namaruangan', 'so.tglorder')
            ->where('so.koders', $idProfile)
            ->where('so.aktif', true);

        if (isset($request['noorder']) && $request['noorder'] != "" && $request['noorder'] != "undefined") {
            $dataStruk = $dataStruk->where('so.noorder', '=', $request['noorder']);
        }

        if (isset($request['norecOrder']) && $request['norecOrder'] != "" && $request['norecOrder'] != "undefined") {
            $dataStruk = $dataStruk->where('so.norec', '=', $request['norecOrder']);
        }

        $dataStruk = $dataStruk->first();
        $data = \DB::table('transaksiordertr as so')
            ->JOIN('transaksiorderdetailtr as op', 'op.transaksiorderfk', '=', 'so.norec')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'so.ruangantujuanidfk')
            ->leftJOIN('jeniskemasanmt as jk', 'jk.id', '=', 'op.jeniskemasanidfk')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'op.produkidfk')
            ->leftJOIN('sediaanmt as sdn', 'sdn.id', '=', 'pr.sediaanidfk')
            ->leftJOIN('satuanstandarmt as ss', 'ss.id', '=', 'op.satuanstandaridfk')
            ->leftJOIN('satuanstandarmt as ss2', 'ss2.id', '=', 'op.satuanviewidfk')
            ->leftJOIN('satuanresepmt as sn', 'sn.id', '=', 'op.satuanresepidfk')
            ->select(
                'so.noorder','op.hargasatuan','op.qtystokcurrent','so.ruangantujuanidfk as objectruangantujuanfk','ru.namaruangan',
                'op.rke','op.jeniskemasanidfk as jeniskemasanfk','jk.id as jkid','jk.jeniskemasan','op.aturanpakai',
                'op.produkidfk as objectprodukfk','pr.namaproduk','op.hasilkonversi','op.satuanstandaridfk as objectsatuanstandarfk','ss.satuanstandar',
                'op.satuanviewidfk as satuanviewfk','ss2.satuanstandar as ssview','op.qtyproduk','op.hargadiscount','op.hasilkonversi',
                'op.qtystokcurrent','op.dosis','op.jenisobatidfk as jenisobatfk','op.hargasatuan','op.hargadiscount',
                'pr.kekuatan','sdn.name as sediaan','op.ispagi','op.issiang','op.ismalam','op.issore',
                'op.keteranganpakai','op.satuanresepidfk as satuanresepfk','sn.satuanresep','op.tglkadaluarsa','so.isreseppulang'
            )
            ->where('so.koders', $idProfile);

        if (isset($request['noorder']) && $request['noorder'] != "" && $request['noorder'] != "undefined") {
            $data = $data->where('so.noorder', '=', $request['noorder']);
        }

        if (isset($request['norecOrder']) && $request['norecOrder'] != "" && $request['norecOrder'] != "undefined") {
            $data = $data->where('so.norec', '=', $request['norecOrder']);
        }

        $data = $data->get();

        $orderPelayanan = [];
        $i = 0;
        $dataStok = \DB::select(
            DB::raw("select sk.norec,spd.produkidfk as objectprodukfk, sk.tglstruk,spd.asalprodukidfk as objectasalprodukfk,
                            spd.harganetto2 as hargajual,spd.harganetto2 as harganetto,spd.hargadiscount,
                    sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk as objectruanganfk
                    from transaksistoktr as spd
                    inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                    where spd.koders = $idProfile and spd.ruanganidfk =:ruanganid
                    group by sk.norec,spd.produkidfk, sk.tglstruk,spd.asalprodukidfk,
                            spd.harganetto2,spd.hargadiscount,
                    spd.ruanganidfk
                    order By sk.tglstruk"),
            array(
                'ruanganid' => $dataStruk->id
            )
        );

        $hargajual = 0;
        $harganetto = 0;
        $nostrukterimafk = '';
        $asalprodukfk = 0;
        $asalproduk = '';
        $jmlstok = 0;
        $hargasatuan = 0;
        $hargadiscount = 0;
        $total = 0;
        $aturanpakaifk = 0;
        $rke = '0';
        $dataTarifAdminResep = $this->settingDataFixed('tarifadminresep', $JENISPROFILE);
        foreach ($data as $item) {
            $i = $i + 1;
            $hargajual = 0;
            $harganetto = 0;
            $jmlstok = 0;
            $hargasatuan = 0;
            $hargadiscount = 0;
            $total = 0;
            $tarifadminjasa = $dataTarifAdminResep;
            $tarifjasa = 0;
            $qty20 = 0;
            if ($item->jkid == 2) {
                $tarifjasa = $tarifadminjasa;
            }
            if ($item->jkid == 1) {
                if ($rke != $item->rke) {
                    if ($item->qtyproduk > 20) {
                        $qty20 = number_format($item->qtyproduk / 20, 0);
                        if ($item->qtyproduk % 20 == 0) {
                            $qty20 = $qty20;
                        } else {
                            $qty20 = $qty20 + 1;
                        }
                        $tarifjasa = $tarifadminjasa * $qty20;
                    } else {
                        $tarifjasa = $tarifadminjasa;
                    }
                    $rke = $item->rke;
                }
            }
            $hargajual = round($item->hargasatuan, 0);
            $hargasatuan = round($item->hargasatuan, 0);
            $harganetto = round($item->hargadiscount, 0);

            foreach ($dataStok as $item2) {
                if ($item2->objectprodukfk == $item->objectprodukfk) {
                    if ($item2->qtyproduk > $item->qtyproduk * $item->hasilkonversi) {
                        $nostrukterimafk = $item2->norec;
                        $asalprodukfk = $item2->objectasalprodukfk;
                        $jmlstok = $item2->qtyproduk;
                        $hargadiscount = $item2->hargadiscount;
                        $total = (((float)ceil($item->qtyproduk) * ((float)$hargasatuan - (float)$hargadiscount)) * $item->hasilkonversi) + $tarifjasa;
                        break;
                    }
                }
            }
            foreach ($dataAsalProduk as $item3) {
                if ($asalprodukfk == $item3->id) {
                    $asalproduk = $item3->asalproduk;
                }
            }
            $aturanpakaifk =  null;
            if ((float)$item->dosis == 0) {
                $item->dosis = 1;
            }

            $orderPelayanan[] = array(
                'no' => $i,
                'noregistrasifk' => '',
                'tglregistrasi' => '',
                'generik' => null,
                'hargajual' => $hargajual,
                'jenisobatfk' => $item->jenisobatfk,
                'kelasfk' => '',
                'stock' => $jmlstok,
                'harganetto' => $harganetto,
                'nostrukterimafk' => $nostrukterimafk,
                'ruanganfk' => $item->objectruangantujuanfk,
                'rke' => $item->rke,
                'jeniskemasanfk' => $item->jeniskemasanfk,
                'jeniskemasan' => $item->jeniskemasan,
                'aturanpakaifk' => $aturanpakaifk,
                'aturanpakai' => $item->aturanpakai,
                'asalprodukfk' => $asalprodukfk,
                'asalproduk' => $asalproduk,
                'produkfk' => $item->objectprodukfk,
                'namaproduk' => $item->namaproduk,
                'nilaikonversi' => $item->hasilkonversi,
                'satuanstandarfk' => $item->satuanviewfk,
                'satuanstandar' => $item->ssview,
                'satuanviewfk' => $item->satuanviewfk,
                'satuanview' => $item->ssview,
                'jmlstok' => $item->qtystokcurrent,
                'jumlah' => ceil($item->qtyproduk),
                'jumlahobat' => $item->qtyproduk,
                'dosis' => $item->dosis,
                'kekuatan' => $item->kekuatan,
                'hargasatuan' => $hargasatuan,
                'hargadiscount' => $hargadiscount,
                'total' => $total,
                'sediaan' => $item->sediaan,
                'jmldosis' => (string)$item->qtyproduk / $item->dosis . '/' . (string)$item->dosis,
                'jasa' => $tarifjasa,
                'ispagi' => $item->ispagi,
                'issiang' =>  $item->issiang,
                'ismalam' =>  $item->ismalam,
                'issore' =>  $item->issore,
                "keterangan" => $item->keteranganpakai,
                'satuanresepfk' =>  $item->satuanresepfk,
                "satuanresep" => $item->satuanresep,
                "tglkadaluarsa" => $item->tglkadaluarsa,
                "isreseppulang" => $item->isreseppulang,
            );
        }

        $result = array(
            'strukorder' => $dataStruk,
            'orderpelayanan' => $orderPelayanan,
        );
        return $this->respond($result);
    }

    public function saveStatusResepElektronik(Request $request)
    {
        $JENISPROFILE = $this->getDataKdProfile($request);
        $idProfile = (int) $JENISPROFILE;
        \DB::beginTransaction();
        $dataReq = $request->all();
        try {
            if (isset($request['strukresep']) && $request['strukresep'] == '') {
                TransaksiOrder::where('noorder', $request['noorder'])
                    ->where('koders', $idProfile)
                    ->update(
                        [
                            'statusorder' => $request['statusorder'],
                            'namapengambilorder' => $request['namapengambil'],
                            'tglambilorder' => $request['tglambil']
                        ]
                    );
                $data = TransaksiOrder::where('noorder', $request['noorder'])->where('koders', $idProfile)->first();

                $dt = TransaksiResep::where('transaksiorderfk', $data->norec)->where('koders', $idProfile)->first();
                AntrianApotik::where('noresep', $dt->noresep)
                    ->where('koders', $idProfile)
                    ->update(
                        [
                            'status' => $request['statusorder']
                        ]
                    );
            }
            if (isset($request['strukresep']) && $request['strukresep'] == true) {
                TransaksiResep::where('noresep', $request['noorder'])
                    ->where('koders', $idProfile)
                    ->update(
                        [
                            'status' => $request['statusorder'],
                            'namalengkapambilresep' => $request['namapengambil'],
                            'tglambilresep' => $request['tglambil']
                        ]
                    );

                AntrianApotik::where('noresep', $request['noorder'])
                    ->where('koders', $idProfile)
                    ->update(
                        [
                            'status' => $request['statusorder']
                        ]
                    );
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
                "by" => 'slvR',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "by" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDaftarResep(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('transaksireseptr as sr')
            ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'sr.daftarpasienruanganfk')
            ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->JOIN('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->JOIN('pegawaimt as pg', 'pg.id', '=', 'sr.penulisresepidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->JOIN('instalasimt as dp', 'dp.id', '=', 'ru.instalasiidfk')
            ->JOIN('ruanganmt as ru2', 'ru2.id', '=', 'sr.ruanganidfk')
            ->JOIN('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->JOIN('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftJoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->JOIN('kelasmt as kl', 'kl.id', '=', 'pd.kelasidfk')
            ->leftJoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->select(DB::raw("
                pd.norec as norec_pd,sr.norec,sr.aktif,sr.noresep,sr.tglresep,ps.id AS psid,pd.noregistrasi,
			    ps.norm,ps.namapasien,ru.id AS ruid,ru.namaruangan,pg.id AS pgid,pg.namalengkap AS dokter,
			    ru2.id AS ruapotikid,ru2.namaruangan AS namaruanganapotik,jk.jeniskelamin,ps.tgllahir,
			    kl.id AS klid,kl.namakelas,pd.tglregistrasi,apd.norec AS norec_apd,kp.kelompokpasien,alm.alamatlengkap
            "))
            ->where('sr.koders', $idProfile)
            ->where('pd.aktif', true)
            ->where('apd.aktif', true)
            ->where('sr.aktif', true);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('sr.tglresep', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $data = $data->where('sr.tglresep', '<=', $request['tglAkhir']);
        }
        if (isset($request['noResep']) && $request['noResep'] != "" && $request['noResep'] != "undefined") {
            $data = $data->where('sr.noresep', 'ilike', '%' . $request['noResep']);
        }
        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $request['noReg']);
        }
        if (isset($request['ruanganId']) && $request['ruanganId'] != "" && $request['ruanganId'] != "undefined") {
            $data = $data->where('ru.id', $request['ruanganId']);
        }
        if (isset($request['instalasiId']) && $request['instalasiId'] != "" && $request['instalasiId'] != "undefined") {
            $data = $data->where('dp.id', $request['instalasiId']);
        }
        if (isset($request['noRm']) && $request['noRm'] != "" && $request['noRm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['noRm'] . '%');
        }
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['namaPasien'] . '%');
        }
        if (isset($request['status']) && $request['status'] != "" && $request['status'] != "undefined") {
            $data = $data->where('sr.status', $request['status']);
        }
        if (isset($request['depoId']) && $request['depoId'] != "" && $request['noResep'] != "undefined") {
            $data = $data->where('sr.ruanganidfk', $request['depoId']);
        }
        if (isset($request['jmlRows']) && $request['jmlRows'] != "" && $request['jmlRows'] != "undefined") {
            $data = $data->take($request['jmlRows']);
        }
        $data = $data->orderby('sr.noresep');
        $data = $data->get();

        $result = array(
            'daftar' => $data,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function getDataComboJenisProduk(Request $request)
    {
        $JENISPROFILE = $this->getDataKdProfile($request);
        $dataDetailJenisProduk = \DB::table('detailjenisprodukmt as dp')
            ->select('dp.id', 'dp.detailjenisproduk', 'dp.jenisprodukidfk')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$JENISPROFILE)
            ->orderBy('dp.detailjenisproduk')
            ->get();

        $dataJenisProduk = \DB::table('jenisprodukmt as ru')
            ->select('ru.id', 'ru.jenisproduk', 'ru.kelompokprodukidfk')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$JENISPROFILE)
            ->orderBy('ru.jenisproduk')
            ->get();

        $dataKelompokProduk = \DB::table('kelompokprodukmt as ru')
            ->select('ru.id', 'ru.kelompokproduk')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$JENISPROFILE)
            ->orderBy('ru.kelompokproduk')
            ->get();

        foreach ($dataDetailJenisProduk as $item) {
            $jenisProduk = [];
            $kelompokProduk = [];
            foreach ($dataJenisProduk as $item2) {
                if ($item->jenisprodukidfk == $item2->id) {
                    foreach ($dataKelompokProduk as $item3) {
                        if ($item2->kelompokprodukidfk == $item3->id) {
                            $kelompokProduk[] = array(
                                'id' => $item3->id,
                                'kelompokproduk' => $item3->kelompokproduk
                            );
                        }
                    }
                    $jenisProduk[] = array(
                        'id' => $item2->id,
                        'jenisproduk' => $item2->jenisproduk,
                        'kelompokproduk' => $kelompokProduk,
                    );
                }
            }

            $detailJenisProduk[] = array(
                'id' => $item->id,
                'detailjenisproduk' => $item->detailjenisproduk,
                'jenisproduk' => $jenisProduk,
            );
        }

        $result = array(
            'detailjenisproduk' => $detailJenisProduk,
            'message' => 'godU',
        );

        return $this->respond($result);
    }
    public function getTransaksiPelayananApotik(Request $request)
    {
        $JENISPROFILE = $this->getDataKdProfile($request);
        $idProfile = (int) $JENISPROFILE;
        $detail = $request->all();
        $result = [];
        $set = explode(',', $this->settingDataFixed('kdJenisProdukObat', $JENISPROFILE));
        $arrPo = [];
        foreach ($set as $ie) {
            $arrPo[] = (int)$ie;
        }
        $data = \DB::table('transaksipasientr as pp')
            ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'pp.daftarpasienruanganfk')
            ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->JOIN('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->JOIN('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->JOIN('jeniskemasanmt as jkm', 'jkm.id', '=', 'pp.jeniskemasanidfk')
            ->leftJoin('jenisracikanmt as jra', 'jra.id', '=', 'pp.jenisobatidfk')
            ->leftJoin('satuanstandarmt as ss', 'ss.id', '=', 'pp.satuanviewidfk')
            ->leftJoin('satuanstandarmt as ss2', 'ss2.id', '=', 'pr.satuanstandaridfk')
            ->JOIN('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->JOIN('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->leftJOIN('strukpelayanantr as sp', 'sp.norec', '=', 'pp.strukfk')
            ->leftJOIN('konversisatuantr as ks', function ($join) {
                $join->on('ks.produkidfk', '=', 'pr.id')
                    ->on('ks.satuanstandar_tujuan', '=', 'pp.satuanviewidfk');
            })
            ->leftJOIN('transaksireseptr as sr', 'sr.norec', '=', 'pp.strukresepidfk')
            ->leftJOIN('ruanganmt as ru2', 'ru2.id', '=', 'sr.ruanganidfk')
            ->leftJOIN('pegawaimt as dok', 'dok.id', '=', 'sr.penulisresepidfk')
            ->leftJOIN('sediaanmt AS rs', 'rs.id', '=', 'pr.sediaanidfk')
            ->leftJoin('satuanresepmt as sn', 'sn.id', '=', 'pp.satuanresepidfk')
            ->select(
                'ps.norm as nocm',
                'ps.namapasien',
                'jk.jeniskelamin',
                'pp.tglpelayanan',
                'pp.produkidfk as produkfk',
                'pr.namaproduk',
                'ss.satuanstandar',
                'pp.jumlah',
                'pp.hargasatuan',
                'pp.hargadiscount',
                'sp.nostruk',
                'pd.noregistrasi',
                'ks.nilaikonversi',
                'ss2.satuanstandar as satuanstandar2',
                'sr.noresep',
                'sr.norec as norec_resep',
                'pp.rke',
                'jkm.jeniskemasan',
                'jk.id as jkid',
                'pp.jenisobatidfk as jenisobatfk',
                'jra.jenisracikan',
                'pp.jasa',
                'ru2.id as ruangandepoid',
                'ru2.namaruangan as ruangandepo',
                'pp.aturanpakai',
                'ru.namaruangan',
                'dok.namalengkap as dokter',
                'pp.ispagi',
                'pp.issiang',
                'pp.ismalam',
                'pp.issore',
                'pp.iskronis',
                'sr.isreseppulang as reseppulang',
                DB::raw("CASE WHEN pr.kekuatan IS NOT NULL AND rs.name IS NOT NULL THEN pr.kekuatan || ' ' || rs.name ELSE '' END AS kekuatan,
                               sn.satuanresep,pp.satuanresepidfk as satuanresepfk,pp.tglkadaluarsa,pp.dosis,djp.detailjenisproduk,
                               jp.jenisproduk,apd.norec AS norec_apd,pd.norec AS norec_pd")
            )
            ->where('pp.koders', $idProfile)
            ->where('jp.id', $arrPo)
            ->orderByRaw(" pp.tglpelayanan asc ,pp.rke asc");

        if (isset($request['nocm']) && $request['nocm'] != "" && $request['nocm'] != "undefined") {
            $data = $data->where('ps.norm', '=', $request['nocm']);
        }
        if (isset($request['norec_pd']) && $request['norec_pd'] != "" && $request['norec_pd'] != "undefined") {
            $data = $data->where('pd.norec', '=', $request['norec_pd']);
        }
        if (isset($request['norec_apd']) && $request['norec_apd'] != "" && $request['norec_apd'] != "undefined") {
            $data = $data->where('apd.norec', '=', $request['norec_apd']);
        }
        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $request['noReg']);
        }
        if (isset($request['norec_resep']) && $request['norec_resep'] != "" && $request['norec_resep'] != "undefined") {
            $data = $data->where('sr.norec', '=', $request['norec_resep']);
        }
        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('pp.tglpelayanan', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('pp.tglpelayanan', '<=', $tgl);
        }
        if (isset($request['dokter']) && $request['dokter'] != "" && $request['dokter'] != "undefined") {
            $data = $data->where('dok.id', '=', $request['dokter']);
        }
        if (isset($request['ruangan']) && $request['ruangan'] != "" && $request['ruangan'] != "undefined") {
            $data = $data->where('ru.id', '=', $request['ruangan']);
        }
        if (isset($request['nik']) && $request['nik'] != '') {
            $data = $data->where('ps.noidentitas', $request['nik']);
        }
        $data = $data->get();

        $rke = 0;
        foreach ($data as $item) {
            if (isset($item->nilaikonversi)) {
                $nKonversi = $item->nilaikonversi;
            } else {
                $nKonversi = 1;
            }
            if (isset($item->satuanstandar)) {
                $ss = $item->satuanstandar;
            } else {
                $ss = $item->satuanstandar2;
            }
            $JenisKemasan = $item->jeniskemasan;
            if (isset($item->jenisracikan)) {
                $JenisKemasan = $item->jeniskemasan . '/' . $item->jenisracikan;
            }
            $jasa = 0;
            if (isset($item->jasa) && $item->jasa != "" && $item->jasa != "undefined") {
                $jasa = $item->jasa;
            }

            $result[] = array(
                'nocm' => $item->nocm,
                'namapasien' => $item->namapasien,
                'jeniskelamin' => $item->jeniskelamin,
                'tglpelayanan' => $item->tglpelayanan,
                'produkfk' => $item->produkfk,
                'namaproduk' => $item->namaproduk,
                'satuanstandar' => $ss,
                'jumlah' => (float)$item->jumlah / (float)$nKonversi,
                'hargasatuan' => $item->hargasatuan,
                'hargadiscount' => $item->hargadiscount,
                'nostruk' => $item->nostruk,
                'noregistrasi' => $item->noregistrasi,
                'noresep' => $item->noresep,
                'rke' => $item->rke,
                'jeniskemasan' => $JenisKemasan,
                'norec_resep' =>  $item->norec_resep,
                'detail' => $detail,
                'jasa' => $jasa,
                'depoid' => $item->ruangandepoid,
                'namaruangandepo' => $item->ruangandepo,
                'aturanpakai' => $item->aturanpakai,
                'dokter' => $item->dokter,
                'pemberiresep' => $item->dokter,
                'namaruangan' => $item->namaruangan,
                'kekuatan' => $item->kekuatan,
                'ispagi' => $item->ispagi,
                'issiang' => $item->issiang,
                'ismalam' => $item->ismalam,
                'issore' => $item->issore,
                'iskronis' => $item->iskronis,
                'satuanresepfk' => $item->satuanresepfk,
                'satuanresep' => $item->satuanresep,
                'tglkadaluarsa' => $item->tglkadaluarsa,
                'dosis' => $item->dosis,
                'detailjenisproduk' => $item->detailjenisproduk,
                'jenisproduk' => $item->jenisproduk,
                'reseppulang' => $item->reseppulang,
                'norec_apd' => $item->norec_apd,
                'norec_pd' => $item->norec_pd,
            );
        }
        return $this->respond($result);
    }

    public function getDataComboResep(Request $request)
    {
        $idProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dept = $this->settingDataFixed('kdDepartemenFarmasi', $idProfile);
        $dataRuanganFamasi = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->where('ru.koders', $idProfile)
            ->where('ru.instalasiidfk', $dept)
            ->where('ru.aktif', true)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataJenisKemasan = \DB::table('jeniskemasanmt as jk')
            ->select('jk.id', 'jk.jeniskemasan')
            ->where('jk.koders', $idProfile)
            ->where('jk.aktif', true)
            ->get();
        $dataJenisRacikan = \DB::table('jenisracikanmt as jk')
            ->select('jk.id', 'jk.jenisracikan')
            ->where('jk.koders', $idProfile)
            ->where('jk.aktif', true)
            ->get();

        $dataAsalProduk = \DB::table('asalprodukmt as ap')
            ->JOIN('transaksistoktr as spd', 'spd.asalprodukidfk', '=', 'ap.id')
            ->select('ap.id', 'ap.asalproduk')
            ->where('ap.koders', $idProfile)
            ->where('ap.aktif', true)
            ->orderBy('ap.id')
            ->groupBy('ap.id', 'ap.asalproduk')
            ->get();

        $dataSatuanResep = \DB::table('satuanresepmt as kp')
            ->select('kp.id', 'kp.satuanresep')
            ->where('kp.aktif', true)
            ->where('kp.koders', $idProfile)
            ->orderBy('kp.satuanresep')
            ->get();

        $result = array(
            'ruanganfarmasi' => $dataRuanganFamasi,
            'jeniskemasan' => $dataJenisKemasan,
            'asalproduk' => $dataAsalProduk,
            'jenisracikan' => $dataJenisRacikan,
            'satuanresep' => $dataSatuanResep,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function getProdukResep(Request $request)
    {
        $JENISPROFILE = $this->getDataKdProfile($request);
        $set = explode(',', $this->settingDataFixed('kdJenisProdukObat', $JENISPROFILE));
        $arrPo = [];
        foreach ($set as $ie) {
            $arrPo[] = (int)$ie;
        }
        $dataProduk = \DB::table('pelayananmt as pr')
            ->leftjoin('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->leftjoin('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->leftJOIN('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->JOIN('transaksistoktr as spd', 'spd.produkidfk', '=', 'pr.id')
            ->select('pr.id', 'pr.namaproduk', 'ss.id as ssid', 'ss.satuanstandar')
            ->where('pr.koders', $JENISPROFILE)
            ->where('pr.aktif', true)
            ->whereIn('jp.id', $arrPo)
            ->where('spd.qtyproduk', '>', 0);
        if (isset($request['namaproduk']) && $request['namaproduk'] != '') {
            $dataProduk = $dataProduk->where('pr.namaproduk', 'ilike', '%' . $request['namaproduk'] . '%');
        }
        if (isset($request['idproduk']) && $request['idproduk'] != '') {
            $dataProduk = $dataProduk->where('pr.id', '=', $request['idproduk']);
        }
        $dataProduk = $dataProduk->groupBy('pr.id', 'pr.namaproduk', 'ss.id', 'ss.satuanstandar');
        $dataProduk = $dataProduk->orderBy('pr.namaproduk');
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();

        $dataKonversiProduk = \DB::table('konversisatuantr as ks')
            ->JOIN('satuanstandarmt as ss', 'ss.id', '=', 'ks.satuanstandar_asal')
            ->JOIN('satuanstandarmt as ss2', 'ss2.id', '=', 'ks.satuanstandar_tujuan')
            ->select(
                'ks.produkidfk',
                'ks.satuanstandar_asal',
                'ss.satuanstandar',
                'ks.satuanstandar_tujuan',
                'ss2.satuanstandar as satuanstandar2',
                'ks.nilaikonversi'
            )
            ->where('ks.koders', $JENISPROFILE)
            ->where('ks.aktif', true)
            ->get();
        $dataProdukResult = [];
        foreach ($dataProduk as $item) {
            $satuanKonversi = [];
            foreach ($dataKonversiProduk as $item2) {
                if ($item->id == $item2->produkidfk) {
                    $satuanKonversi[] = array(
                        'ssid' => $item2->satuanstandar_tujuan,
                        'satuanstandar' => $item2->satuanstandar2,
                        'nilaikonversi' => $item2->nilaikonversi,
                    );
                }
            }

            $dataProdukResult[] = array(
                'id' => $item->id,
                'namaproduk' => $item->namaproduk,
                'ssid' => $item->ssid,
                'satuanstandar' => $item->satuanstandar,
                'konversisatuan' => $satuanKonversi,
            );
        }
        return $this->respond($dataProdukResult);
    }

    public function getJenisObat(Request $request)
    {

        $data = [];
        if (isset($request['jrid']) && $request['jrid'] != "" && $request['jrid'] != "undefined" && $request['jrid'] != 'null') {
            $data = \DB::table('jenisracikanmt as jr')
                ->select('jr.id', 'jr.jenisracikan');
            $data = $data->where('jr.id', $request['jrid']);
            $data = $data->get();
        }

        $result = array(
            'data' => $data,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function SimpanPelayananResep(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdVerifikasi = (int) $this->settingDataFixed('KdStatusPengerjaanVerifikasi', $idProfile);
        $kdJenisTransaksiReturResep = (int) $this->settingDataFixed('KdJenisTransaksiReturResep', $idProfile);
        $kdJenisTransaksiResep = (int) $this->settingDataFixed('KdJenisTransaksiResep', $idProfile);
        $dataLogin = $request->all();
        $dataPegawaiUser = \DB::select(
            DB::raw("
                select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.kdprofile = $idProfile
                and lu.id=:idLoginUser"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        if (isset($request['strukresep']['isobatalkes']) && $request['strukresep']['isobatalkes'] == true) {
            $noResep = $this->generateCodeBySeqTable(new TransaksiResep, 'noresep', 12, 'TA/' . $this->getDateTime()->format('ym') . '/', $idProfile);
        } else {
            $noResep = $this->generateCodeBySeqTable(new TransaksiResep, 'noresep', 12, 'TR/' . $this->getDateTime()->format('ym') . '/', $idProfile);
        }
        if ($noResep == '') {
            $ReportTrans = "Gagal mengumpukan data, Coba lagi.!";
            \DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
            return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
        }
        DB::beginTransaction();

        try {

            $racikanORnonracikan = 'F';
            $dataLogin = $request->all();
            $r_SR = $request['strukresep'];
            $dataAntrian = DaftarPasienRuangan::where('norec', $r_SR['pasienfk'])
                ->where('koders', $idProfile)
                ->where('aktif', true)
                ->first();
            if (isset($r_SR['noorder']) && $r_SR['jenisdata'] == 'OrderResep') {
                $dataOrder = TransaksiOrder::where('noorder', $r_SR['noorder'])->where('koders', $idProfile)->first();
                $dataOrder->statusorder = $kdVerifikasi;
                $dataOrder->save();
            }
            $resepOld = TransaksiResep::where('noresep', $r_SR['noresep'])
                ->where('koders', $idProfile)->first();
            $namaPasien = $r_SR['nocm'] . ' ' . $r_SR['namapasien'];
            if ($r_SR['noresep'] == "") {
                $newSR = new TransaksiResep();
                $norecSR = $newSR->generateNewId();
                $newSR->norec = $norecSR;
            } else {
                $newSR = TransaksiResep::where('norec', $r_SR['norecResep'])->where('koders', $idProfile)->first();
                $noResep = $newSR->noresep;
            }
            $newSR->koders = $idProfile;
            $newSR->aktif = true;
            $newSR->noresep = $noResep;
            $newSR->kelompoktransaksiidfk = $kdJenisTransaksiResep;
            $newSR->daftarpasienruanganfk = $r_SR['pasienfk'];
            $newSR->penulisresepidfk = $r_SR['penulisresepfk'];
            $newSR->ruanganidfk = $r_SR['ruanganfk'];
            $newSR->status = $kdVerifikasi;
            $newSR->tglresep =  $r_SR['tglresep'];
            $newSR->petugasinputidfk =  $dataPegawaiUser[0]->id;
            $newSR->save();
            $norec_SR = $newSR->norec;
            $dokterPenulis =  $newSR->penulisresepfk;
            $isRetur = false;

            if (isset($r_SR['noorder']) && $r_SR['jenisdata'] == 'OrderResep') {
                $DataOrder = TransaksiOrder::where('noorder', $r_SR['noorder'])
                    ->where('koders', $idProfile)
                    ->first();
                $norecOrder = $DataOrder->norec;
                $dataResep = TransaksiResep::where('norec', $norec_SR)
                    ->update(['transaksiorderfk' => $norecOrder]);
            }

            $isRetur = false;
            if ($r_SR['noresep'] != '-') {
                if ($r_SR['retur'] != '-') {
                    $isRetur = true;
                    $TRANSRETUR = new TransaksiRetur();
                    $norecSRetur = $TRANSRETUR->generateNewId();
                    $noRetur = $this->generateCode(new TransaksiRetur, 'noretur', 12, 'RETUR/' . $this->getDateTime()->format('ym') . '/', $idProfile);
                    $TRANSRETUR->norec = $norecSRetur;
                    $TRANSRETUR->koders = $idProfile;
                    $TRANSRETUR->aktif = true;
                    $TRANSRETUR->kelompoktransaksiidfk = $kdJenisTransaksiReturResep;
                    $TRANSRETUR->keteranganalasan = $r_SR['alasan'];
                    $TRANSRETUR->keteranganlainnya = 'RETUR OBAT ALKES';
                    $TRANSRETUR->noretur = $noRetur;
                    $TRANSRETUR->ruanganidfk = $r_SR['ruanganfk'];
                    $TRANSRETUR->pegawaiidfk = $dataPegawaiUser[0]->id;
                    $TRANSRETUR->tglretur = $this->getDateTime()->format('Y-m-d H:i:s');
                    $TRANSRETUR->transaksiresepfk = $norec_SR;
                    $TRANSRETUR->save();
                    $norec_retur = $TRANSRETUR->norec;

                    $r_PP = $request['pelayananpasien'];
                    foreach ($r_PP as $r_PPLXXXX) {
                        if ((int)$r_PPLXXXX['jmlretur'] != 0) {
                            $TRANSRETURDETAIL = new TransaksiReturDetail();
                            $norecPPR = $TRANSRETURDETAIL->generateNewId();
                            $TRANSRETURDETAIL->norec = $norecPPR;
                            $TRANSRETURDETAIL->koders = $idProfile;
                            $TRANSRETURDETAIL->aktif = true;
                            $TRANSRETURDETAIL->transaksireturfk = $norec_retur;
                            $TRANSRETURDETAIL->produkidfk = $r_PPLXXXX['produkfk'];
                            $TRANSRETURDETAIL->hargadiscount = $r_PPLXXXX['hargadiscount'];
                            $TRANSRETURDETAIL->harganetto1 = $r_PPLXXXX['hargajual'];
                            $TRANSRETURDETAIL->harganetto2 = $r_PPLXXXX['hargajual'];
                            if (isset($r_PPLXXXX['persendiscount'])) {
                                $TRANSRETURDETAIL->persendiscount = $r_PPLXXXX['persendiscount'];
                            } else {
                                $TRANSRETURDETAIL->persendiscount = 0;
                            }
                            $TRANSRETURDETAIL->qtyproduk = $r_PPLXXXX['jmlretur'];
                            $TRANSRETURDETAIL->qtyprodukonhand = 0;
                            $TRANSRETURDETAIL->qtyprodukoutext = 0;
                            $TRANSRETURDETAIL->qtyprodukoutint = 0;
                            $TRANSRETURDETAIL->nostrukterimaidfk = $r_PPLXXXX['nostrukterimafk'];
                            $TRANSRETURDETAIL->save();
                            $norec_PPR = $TRANSRETURDETAIL->norec;

                            $TambahStok = (float)$r_PPLXXXX['jmlretur'] * (float)$r_PPLXXXX['nilaikonversi']; //$r_PPLXXXX['jmlretur'];
                            $TRANSSTOKARR = TransaksiStok::where('nostrukterimafk', $r_PPLXXXX['nostrukterimafk'])
                                ->where('koders', $idProfile)
                                ->where('ruanganidfk', $r_SR['ruanganfk'])
                                ->where('produkidfk', $r_PPLXXXX['produkfk'])
                                ->orderby('tglkadaluarsa', 'desc')
                                ->first();
                            $TRANSSTOKARR->qtyproduk = (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok;
                            $TRANSSTOKARR->save();

                            $TRANSSALDOAWAL = DB::select(
                                DB::raw("select sum(qtyproduk) as qty from transaksistoktr 
                            where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                                array(
                                    'ruanganfk' => $r_SR['ruanganfk'],
                                    'produkfk' =>  $r_PPLXXXX['produkfk'],
                                )
                            );
                            $saldoAwal = 0;
                            foreach ($TRANSSALDOAWAL as $ITEMSs) {
                                $saldoAwal = (float)$ITEMSs->qty;
                            }
                            $tglnow =  date('Y-m-d H:i:s');
                            $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                            $qtyAwal = (float)$saldoAwal - (float)$TambahStok;
                            $TRANS_KS = array(
                                "saldoawal" => (float)$saldoAwal,
                                "qtyin" => (float)$TambahStok,
                                "qtyout" => 0,
                                "saldoakhir" => $qtyAwal + (float)$TambahStok,
                                "keterangan" => 'Retur Resep No. ' . $noResep,
                                "produkidfk" =>  $r_PPLXXXX['produkfk'],
                                "ruanganidfk" => $r_SR['ruanganfk'],
                                "tglinput" => $tglUbah,
                                "tglkejadian" => $tglUbah,
                                "nostrukterimaidfk" => $r_PPLXXXX['nostrukterimafk'],
                                "norectransaksi" => $norec_PPR,
                                "tabletransaksi" => 'strukreturdetail',
                                "flagfk" => null,
                            );
                            $this->KartuStok($idProfile, $TRANS_KS);

                            $TambahStok = 0;
                            $TambahStok = (float)$r_PPLXXXX['jumlah'] * (float)$r_PPLXXXX['nilaikonversi'];
                            $TRANSSTOKARR = TransaksiStok::where('nostrukterimafk', $r_PPLXXXX['nostrukterimafk'])
                                ->where('koders', $idProfile)
                                ->where('ruanganidfk', $r_SR['ruanganfk'])
                                ->where('produkidfk', $r_PPLXXXX['produkfk'])
                                ->orderby('tglkadaluarsa', 'desc')
                                ->first();
                            $TRANSSTOKARR->qtyproduk = (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok;
                            $TRANSSTOKARR->save();

                            $TRANSSALDOAWAL = DB::select(
                                DB::raw("
                                select sum(qtyproduk) as qty from transaksistoktr 
                                where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk
                            "),
                                array(
                                    'ruanganfk' => $r_SR['ruanganfk'],
                                    'produkfk' =>  $r_PPLXXXX['produkfk'],
                                )
                            );
                            $saldoAwal = 0;
                            foreach ($TRANSSALDOAWAL as $ITEMSs) {
                                $saldoAwal = (float)$ITEMSs->qty;
                            }
                        }
                    }

                    $HapusPP = TransaksiPasien::where('strukresepidfk', $norec_SR)->where('koders', $idProfile)->get();
                    foreach ($HapusPP as $pp) {
                        $HapusPPD = TransaksiPasienDetail::where('transaksipasienfk', $pp['norec'])->where('koders', $idProfile)->delete();
                        $HapusPPP = PetugasPelaksana::where('transaksipasienfk', $pp['norec'])->where('koders', $idProfile)->delete();
                    }
                    $Edit = TransaksiPasien::where('strukresepidfk', $norec_SR)->delete();
                } else {
                    TransaksiKartuStok::where('keterangan',  'Resep Farmasi ' . ' ' . $noResep . ' ' . $namaPasien)
                        ->where('koders', $idProfile)
                        ->update([
                            'flagfk' => null
                        ]);

                    $tglnow =  date('Y-m-d H:i:s');
                    $tglUbah = date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($tglnow)));
                    $dataKembaliStok = DB::select(
                        DB::raw("
                            select pp.strukterimaidfk,pp.jumlah,pp.nilaikonversi,sr.ruanganidfk,pp.produkidfk
                            from transaksipasientr as pp
                            INNER JOIN transaksireseptr sr on sr.norec=pp.strukresepidfk
                            where sr.aktif = true and pp.koders = $idProfile 
                            and sr.koders = $idProfile and sr.norec=:strukresepfk"),
                        array(
                            'strukresepfk' => $norec_SR,
                        )
                    );

                    if ($resepOld != null && $r_SR['ruanganfk'] == $resepOld->ruanganidfk) {
                        foreach ($dataKembaliStok as $item5) {
                            $TambahStok = (float)$item5->jumlah;
                            $TRANSSTOKARR = TransaksiStok::where('nostrukterimafk', $item5->strukterimaidfk)
                                ->where('aktif', true)
                                ->where('koders', $idProfile)
                                ->where('ruanganidfk', $item5->ruanganidfk)
                                ->where('produkidfk', $item5->produkidfk)
                                ->orderby('tglkadaluarsa', 'desc')
                                ->first();

                            TransaksiStok::where('norec', $TRANSSTOKARR->norec)
                                ->where('koders', $idProfile)
                                ->update([
                                    'qtyproduk' => (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok
                                ]);

                            $TRANSSALDOAWAL = DB::select(
                                DB::raw("
                                select sum(qtyproduk) as qty from transaksistoktr 
                                where aktif = true and koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk
                            "),
                                array(
                                    'ruanganfk' => $item5->ruanganidfk,
                                    'produkfk' => $item5->produkidfk,
                                )
                            );
                            $saldoAwal = 0;
                            foreach ($TRANSSALDOAWAL as $ITEMSs) {
                                $saldoAwal = (float)$ITEMSs->qty;
                            }

                            $qtyAwal = (float)$saldoAwal - (float)$TambahStok;
                            $TRANS_KS = array(
                                "saldoawal" => $qtyAwal,
                                "qtyin" => (float)$TambahStok,
                                "qtyout" => 0,
                                "saldoakhir" => $qtyAwal + (float)$TambahStok,
                                "keterangan" => 'Ubah Resep No. ' . $noResep,
                                "produkidfk" =>  $item5->produkidfk,
                                "ruanganidfk" => $r_SR['ruanganfk'],
                                "tglinput" => $tglUbah,
                                "tglkejadian" => $tglUbah,
                                "nostrukterimaidfk" => $TRANSSTOKARR->nostrukterimafk,
                                "norectransaksi" => $TRANSSTOKARR->norec,
                                "tabletransaksi" => 'transaksistoktr',
                                "flagfk" => null,
                            );
                            $this->KartuStok($idProfile, $TRANS_KS);
                        }
                    } else {

                        foreach ($dataKembaliStok as $item5) {
                            $TambahStok = (float)$item5->jumlah;
                            $TRANSSTOKARR = TransaksiStok::where('nostrukterimafk', $item5->strukterimaidfk)
                                ->where('aktif', true)
                                ->where('koders', $idProfile)
                                ->where('ruanganidfk', $resepOld->ruanganidfk)
                                ->where('produkidfk', $item5->produkidfk)
                                ->orderby('tglkadaluarsa', 'desc')
                                //->where('qtyproduk','>',0)
                                ->first();

                            TransaksiStok::where('norec', $TRANSSTOKARR->norec)
                                ->where('koders', $idProfile)
                                ->update(
                                    [
                                        'qtyproduk' => (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok
                                    ]
                                );

                            $TRANSSALDOAWAL = DB::select(
                                DB::raw("
                                select sum(qtyproduk) as qty from transaksistoktr 
                                where aktif = true and koders = $idProfile 
                                and ruanganidfk=:ruanganfk and produkidfk=:produkfk
                            "),
                                array(
                                    'ruanganfk' => $resepOld->ruanganidfk,
                                    'produkfk' => $item5->produkidfk,
                                )
                            );

                            $saldoAwal = 0;
                            foreach ($TRANSSALDOAWAL as $ITEMSs) {
                                $saldoAwal = (float)$ITEMSs->qty;
                            }

                            $TRANS_KS = array(
                                "saldoawal" => (float)$saldoAwal,
                                "qtyin" => (float)$TambahStok,
                                "qtyout" => 0,
                                "saldoakhir" => (float)$saldoAwal + (float)$TambahStok,
                                "keterangan" => 'Ubah Resep No. ' . $noResep,
                                "produkidfk" =>  $item5->produkidfk,
                                "ruanganidfk" => $resepOld->ruanganidfk,
                                "tglinput" => $tglUbah,
                                "tglkejadian" => $tglUbah,
                                "nostrukterimaidfk" => $TRANSSTOKARR->nostrukterimafk,
                                "norectransaksi" => $TRANSSTOKARR->norec,
                                "tabletransaksi" => 'transaksistoktr',
                                "flagfk" => null,
                            );
                            $this->KartuStok($idProfile, $TRANS_KS);
                        }
                    }

                    $HapusPP = TransaksiPasien::where('strukresepidfk', $norec_SR)->where('koders', $idProfile)->get();
                    foreach ($HapusPP as $pp) {
                        $HapusPPD = TransaksiPasienDetail::where('transaksipasienfk', $pp['norec'])->where('koders', $idProfile)->delete();
                        $HapusPPP = PetugasPelaksana::where('transaksipasienfk', $pp['norec'])->where('koders', $idProfile)->delete();
                    }
                    $Edit = TransaksiPasien::where('strukresepidfk', $norec_SR)->where('koders', $idProfile)->delete();
                }
            }

            $r_PP = $request['pelayananpasien'];
            foreach ($r_PP as $r_PPL) {
                $qtyJumlah = (float)$r_PPL['jumlah'] * (float)$r_PPL['nilaikonversi'];
                $newPP = new TransaksiPasien();
                $norecPP = $newPP->generateNewId();
                $newPP->norec = $norecPP;
                $newPP->koders = $idProfile;
                $newPP->aktif = true;
                $newPP->daftarpasienruanganfk = $r_SR['pasienfk'];
                $newPP->tglregistrasi = $dataAntrian->tglregistrasi;
                $newPP->aturanpakai = $r_PPL['aturanpakai'];
                $newPP->generik = $r_PPL['generik'];
                $newPP->hargadiscount = $r_PPL['hargadiscount'];
                if (isset($r_PPL['persendiscount'])) {
                    $newPP->persendiscount = $r_PPL['persendiscount'];
                } else {
                    $newPP->persendiscount = 0;
                }
                $newPP->hargajual = $r_PPL['hargajual'];
                $newPP->hargasatuan = $r_PPL['hargasatuan'];
                $newPP->jenisobatidfk = $r_PPL['jenisobatfk'];
                $newPP->jumlah = $qtyJumlah;
                $newPP->kelasidfk = $dataAntrian->kelasidfk;
                $newPP->kdkelompoktransaksi = 1;
                $newPP->produkidfk = $r_PPL['produkfk'];
                if (isset($r_PPL['routefk'])) {
                    $newPP->routeidfk = $r_PPL['routefk'];
                }
                $newPP->stock = $r_PPL['stock'];
                $newPP->tglpelayanan = $r_SR['tglresep'];
                $newPP->harganetto = $r_PPL['harganetto'];
                $newPP->jeniskemasanidfk = $r_PPL['jeniskemasanfk'];
                $newPP->rke = $r_PPL['rke'];
                $newPP->strukresepidfk = $norec_SR;
                $newPP->satuanviewidfk = $r_PPL['satuanviewfk'];
                $newPP->nilaikonversi = $r_PPL['nilaikonversi'];
                $newPP->strukterimaidfk = $r_PPL['nostrukterimafk'];
                $newPP->dosis = $r_PPL['dosis'];
                if (isset($r_PPL['jasa'])) {
                    $newPP->jasa = $r_PPL['jasa'];
                } else {
                    $newPP->jasa = 0;
                }
                $newPP->qtydetailresep = $r_PPL['jumlah'];
                $newPP->isobat = 1;
                if (isset($r_PPL['ispagi'])) {
                    $newPP->ispagi = $r_PPL['ispagi'];
                }
                if (isset($r_PPL['issiang'])) {
                    $newPP->issiang = $r_PPL['issiang'];
                }
                if (isset($r_PPL['ismalam'])) {
                    $newPP->ismalam = $r_PPL['ismalam'];
                }
                if (isset($r_PPL['issore'])) {
                    $newPP->issore = $r_PPL['issore'];
                }
                $newPP->keteranganpakai = $r_PPL['keterangan'];
                if (isset($r_PPL['iskronis'])) {
                    $newPP->iskronis = $r_PPL['iskronis'];
                }
                if (isset($r_PPL['satuanresepfk'])) {
                    $newPP->satuanresepidfk = $r_PPL['satuanresepfk'];
                }
                if (isset($r_PPL['tglkadaluarsa']) && $r_PPL['tglkadaluarsa'] != 'Invalid date' && $r_PPL['tglkadaluarsa'] != '') {
                    $newPP->tglkadaluarsa = $r_PPL['tglkadaluarsa'];
                }
                $newPP->save();

                if ((int)$r_PPL['jeniskemasanfk'] == 1) {
                    $racikanORnonracikan = 'F';
                }

                $dataPP[] = $newPP;
                $norec_PP = $newPP->norec;
                $dataKomponen = [];
                $dataKomponen[] = array(
                    'komponenfk' => '9',
                    'komponen' => 'Harga Netto',
                    'harga' => (float)$r_PPL['harganetto']
                );
                $dataKomponen[] = array(
                    'komponenfk' => '12',
                    'komponen' => 'Profit',
                    'harga' => (float)$r_PPL['hargasatuan'] - (float)$r_PPL['harganetto']
                );
                foreach ($dataKomponen as $itemKomponen) {
                    $newPPD = new TransaksiPasienDetail();
                    $norecPPD = $newPPD->generateNewId();
                    $newPPD->norec = $norecPPD;
                    $newPPD->koders = $idProfile;
                    $newPPD->aktif = true;
                    $newPPD->daftarpasienruanganfk = $r_SR['pasienfk'];
                    $newPPD->tglregistrasi = $dataAntrian->tglregistrasi;
                    $newPPD->aturanpakai = $r_PPL['aturanpakai'];
                    $newPPD->generik = $r_PPL['generik'];
                    $newPPD->hargadiscount = 0;
                    $newPPD->hargajual = $itemKomponen['harga'];
                    $newPPD->hargasatuan = $itemKomponen['harga'];
                    $newPPD->jenisobatidfk = $r_PPL['jenisobatfk'];
                    $newPPD->jumlah = $qtyJumlah;
                    $newPPD->komponenhargaidfk = $itemKomponen['komponenfk'];
                    $newPPD->transaksipasienfk = $norec_PP;
                    $newPPD->produkidfk = $r_PPL['produkfk'];
                    if (isset($r_PPL['routefk'])) {
                        $newPPD->routeidfk = $r_PPL['routefk'];
                    }
                    $newPPD->stock = 0;
                    $newPPD->tglpelayanan =  $r_SR['tglresep'];
                    $newPPD->harganetto = $itemKomponen['harga'];
                    if (isset($r_PPL['jasa'])) {
                        $newPPD->jasa = $r_PPL['jasa'];
                    } else {
                        $newPPD->jasa = 0;
                    }
                    $newPPD->save();
                }

                $GetNorec = TransaksiStok::where('nostrukterimafk', $r_PPL['nostrukterimafk'])
                    ->where('aktif', true)
                    ->where('koders', $idProfile)
                    ->where('ruanganidfk', $r_PPL['ruanganfk'])
                    ->where('produkidfk', $r_PPL['produkfk'])
                    ->select('norec')
                    ->get();

                $jmlPengurang = (float)$qtyJumlah;
                $kurangStok = (float)0;
                foreach ($GetNorec as $item) {
                    $TRANSSTOKARR = TransaksiStok::where('nostrukterimafk', $r_PPL['nostrukterimafk'])
                        ->where('aktif', true)
                        ->where('koders', $idProfile)
                        ->where('ruanganidfk', $r_PPL['ruanganfk'])
                        ->where('produkidfk', $r_PPL['produkfk'])
                        ->where('norec', $item->norec)
                        ->first();
                    if ((float)$TRANSSTOKARR->qtyproduk <= (float)$jmlPengurang) {
                        $kurangStok = (float)$TRANSSTOKARR->qtyproduk;
                        $jmlPengurang = (float)$jmlPengurang - (float)$kurangStok;
                    } else {
                        $kurangStok = (float)$jmlPengurang;
                        $jmlPengurang = (float)$jmlPengurang - (float)$kurangStok;
                    }
                    $TRANSSTOKARR->qtyproduk = (float)$TRANSSTOKARR->qtyproduk - (float)$kurangStok;
                    $TRANSSTOKARR->save();
                }

                $TRANSSALDOAWAL = DB::select(
                    DB::raw("
                      select sum(qtyproduk) as qty from transaksistoktr 
                      where aktif = true and koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk
                "),
                    array(
                        'ruanganfk' => $r_PPL['ruanganfk'],
                        'produkfk' => $r_PPL['produkfk'],
                    )
                );

                foreach ($TRANSSALDOAWAL as $item) {
                    $saldoAwal = (float)$item->qty;
                }

                $TRANS_KS[] = [];
                if ($isRetur == true) {
                } else {

                    $tglnow =  date('Y-m-d H:i:s');
                    $qtyAwal = (float)$saldoAwal + (float)$qtyJumlah;
                    $TRANS_KS = array(
                        "saldoawal" => $qtyAwal,
                        "qtyin" => 0,
                        "qtyout" => (float)$qtyJumlah,
                        "saldoakhir" => $qtyAwal - (float)$qtyJumlah,
                        "keterangan" => 'Resep Farmasi ' . ' ' . $noResep . ' ' . $namaPasien,
                        "produkidfk" =>  $r_PPL['produkfk'],
                        "ruanganidfk" => $r_PPL['ruanganfk'],
                        "tglinput" => $tglnow,
                        "tglkejadian" => $tglnow,
                        "nostrukterimaidfk" => $r_PPL['nostrukterimafk'],
                        "norectransaksi" => $norec_PP,
                        "tabletransaksi" => 'pelayananpasien_t',
                        "flagfk" => 7,
                    );
                    $this->KartuStok($idProfile, $TRANS_KS);
                }

                $newP3 = new PetugasPelaksana();
                $norecKS = $newP3->generateNewId();
                $newP3->norec = $norecKS;
                $newP3->koders = $idProfile;
                $newP3->aktif = true;
                $newP3->nomasukidfk = $dataAntrian->registrasipasienfk;
                $newP3->asalprodukidfk = $r_PPL['asalprodukfk'];
                $newP3->jenispetugaspeidfk = 3;
                $newP3->produkidfk = $r_PPL['produkfk'];
                $newP3->ruanganidfk = $r_PPL['ruanganfk'];
                $newP3->transaksipasienfk = $norec_PP;
                $newP3->tglpelayanan = $r_SR['tglresep'];
                $newP3->pegawaiidfk = $dokterPenulis;
                $newP3->save();
            }
            $kdRuanganDepoRajal = (int) $this->settingDataFixed('kdRuanganDepoRajal', $idProfile);
            if ($r_SR['jenisdata'] != 'EditResep') {
                if ($r_SR['noorder'] == '' || $r_SR['noorder'] != '') {
                    $historyOrder = TransaksiOrder::where('noorder', $r_SR['noorder'])->where('koders', $idProfile)->first();
                    $noAntri = "";
                    if ($historyOrder != null) {
                        $noAntri = $historyOrder->noantri;
                    }
                    if ($noAntri != "" || $noAntri != null) {
                        $newAA = new AntrianApotik();
                        $norecAA = $newAA->generateNewId();
                        $newAA->norec = $norecAA;
                        $newAA->koders = $idProfile;
                        $newAA->aktif = true;
                        $newAA->noantri = $historyOrder->noantri;
                        $newAA->keterangan = $namaPasien;
                        $newAA->jenis = $racikanORnonracikan;
                        $newAA->tglresep = date('Y-m-d H:i:s',strtotime($r_SR['tglresep']));//date('Y-m-d H:i:s');
                        $newAA->noresep = $noResep;
                        $newAA->save();
                    } elseif ($r_SR['noresep'] == '' && $r_SR['ruanganfk'] == $kdRuanganDepoRajal) {
                        $dari = date('Y-m-d 00:00',strtotime($r_SR['tglresep']));
                        $sampai = date('Y-m-d 23:59',strtotime($r_SR['tglresep']));
                        $countAntrian = AntrianApotik::where('jenis', $racikanORnonracikan)
                            ->where('koders', $idProfile)
                            ->whereBetween('tglresep', [$dari, $sampai])
                            ->max('noantri');

                        $noAntriApotik = (str_pad((int)$countAntrian + 1, 4, "0", STR_PAD_LEFT));
                        $newAA = new AntrianApotik();
                        $norecAA = $newAA->generateNewId();
                        $newAA->norec = $norecAA;
                        $newAA->koders = $idProfile;
                        $newAA->aktif = true;
                        $newAA->noantri = $noAntriApotik;
                        $newAA->keterangan = $namaPasien;
                        $newAA->jenis = $racikanORnonracikan;
                        $newAA->tglresep = date('Y-m-d H:i:s',strtotime($r_SR['tglresep']));//date('Y-m-d H:i:s');
                        $newAA->noresep = $noResep;
                        $newAA->save();

                        if ($r_SR['jenisdata'] != 'EditResep' && $r_SR['noorder'] != '') {
                            $dataOrder = TransaksiOrder::where('noorder', $r_SR['noorder'])
                                ->where('koders', $idProfile)
                                ->update([
                                    'noantri' => $noAntriApotik,
                                    'jenis' => $racikanORnonracikan,
                                    'keterangaantrian' => $namaPasien,
                            ]);
                        }
                    }
                }
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Simpan Pelayanan Resep Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "resep" => $newSR,
                "dataPP" => $dataPP,
                "dataKS" => $TRANS_KS,
                "R_PPL" => $r_PPL,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Simpan Pelayanan Resep Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDetailResep(Request $request)
    {
        $JENISPROFILE = (int) $this->getDataKdProfile($request);

        $dataStruk = \DB::table('transaksireseptr as sr')
            ->JOIN('pegawaimt as pg', 'pg.id', '=', 'sr.penulisresepidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'sr.ruanganidfk')
            ->select(DB::raw("sr.norec,sr.noresep,pg.id AS pgid,pg.namalengkap,
                                    ru.id,ru.namaruangan,sr.tglresep,sr.daftarpasienruanganfk AS norec_apd"))
            ->where('sr.aktif', true)
            ->where('sr.koders', $JENISPROFILE);

        if (isset($request['norecResep']) && $request['norecResep'] != "" && $request['norecResep'] != "undefined") {
            $dataStruk = $dataStruk->where('sr.norec', '=', $request['norecResep']);
        }

        $dataStruk = $dataStruk->first();
        $ruanganResep = $dataStruk->id;

        $data = \DB::table('transaksireseptr as sr')
            ->JOIN('transaksipasientr as pp', 'pp.strukresepidfk', '=', 'sr.norec')
            ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'pp.daftarpasienruanganfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->JOIN('jeniskemasanmt as jk', 'jk.id', '=', 'pp.jeniskemasanidfk')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'pp.produkidfk')
            ->JOIN('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->JOIN('satuanstandarmt as ss2', 'ss2.id', '=', 'pp.satuanviewidfk')
            ->LeftJOIN('satuanresepmt as sn', 'sn.id', '=', 'pp.satuanresepidfk')
            ->select(DB::raw("
                sr.noresep,pp.hargasatuan,pp.stock,apd.ruanganidfk,ru.namaruangan,
			    pp.rke,pp.jeniskemasanidfk,jk.id AS jkid,jk.jeniskemasan,pp.aturanpakai,
			    pp.produkidfk,pr.namaproduk,pp.nilaikonversi,
			    pr.satuanstandaridfk,ss.satuanstandar,pp.satuanviewidfk,ss2.satuanstandar AS ssview,
			    pp.jumlah,pp.hargadiscount,pp.dosis,pp.jenisobatidfk,pp.jasa,pp.hargajual,pp.hargasatuan,
			    pp.strukterimaidfk,pp.qtydetailresep,pp.ispagi,pp.issiang,pp.ismalam,pp.issore,pr.kekuatan,
			    pp.keteranganpakai,pp.iskronis,pp.satuanresepidfk,sn.satuanresep,pp.tglkadaluarsa,
			    CASE WHEN pp.persendiscount IS NULL THEN 0 ELSE pp.persendiscount END AS persendiscount			   
            "))
            ->where('sr.aktif', true)
            ->where('sr.koders', $JENISPROFILE);

        if (isset($request['norecResep']) && $request['norecResep'] != "" && $request['norecResep'] != "undefined") {
            $data = $data->where('sr.norec', '=', $request['norecResep']);
        }
        $data = $data->get();

        $pelayananPasien = [];
        $i = 0;
        $dataStok = \DB::select(
            DB::raw("
                    select sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,
                    spd.harganetto2 as hargajual,spd.harganetto2 as harganetto,spd.hargadiscount,
                    sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk
                    from transaksistoktr as spd
                    inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                    where spd.koders = $JENISPROFILE and spd.ruanganidfk =:ruanganid
                    group by sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,
                             spd.harganetto2,spd.hargadiscount,spd.ruanganidfk
                    order By sk.tglstruk"),
            array(
                'ruanganid' => $dataStruk->id
            )
        );
        $hargajual = 0;
        $harganetto = 0;
        $nostrukterimafk = '';
        $asalprodukfk = 0;
        $asalproduk = '';
        $jmlstok = 0;
        $hargasatuan = 0;
        $hargadiscount = 0;
        $total = 0;
        $aturanpakaifk = 0;
        foreach ($data as $item) {
            $i = $i + 1;

            foreach ($dataStok as $item2) {
                if ($item2->produkidfk == $item->produkidfk) {
                    if ($item2->norec == $item->strukterimaidfk) {
                        $hargajual = $item->hargajual;
                        $harganetto = $item->hargasatuan;
                        $nostrukterimafk = $item2->norec;
                        $asalprodukfk = $item2->asalprodukidfk;
                        $jmlstok = $item2->qtyproduk;
                        $hargasatuan = $harganetto;
                        $hargadiscount = $item->hargadiscount;
                        $total = (((float)$item->jumlah * ((float)$hargasatuan - (float)$hargadiscount)));
                        break;
                    }
                }
            }
            $jmlxMakan = (((float)$item->jumlah / (float)$item->nilaikonversi) / (float)$item->dosis) * (float)$item->kekuatan;
            $pelayananPasien[] = array(
                'no' => $i,
                'noregistrasifk' => '',
                'tglregistrasi' => '',
                'generik' => null,
                'hargajual' => $hargajual,
                'jenisobatfk' => $item->jenisobatidfk,
                'kelasfk' => '',
                'stock' => $jmlstok,
                'harganetto' => $harganetto,
                'nostrukterimafk' => $nostrukterimafk,
                'ruanganfk' => $ruanganResep,
                'rke' => $item->rke,
                'jeniskemasanfk' => $item->jeniskemasanidfk,
                'jeniskemasan' => $item->jeniskemasan,
                'aturanpakaifk' => $aturanpakaifk,
                'aturanpakai' => $item->aturanpakai,
                'asalprodukfk' => $asalprodukfk,
                'asalproduk' => $asalproduk,
                'produkfk' => $item->produkidfk,
                'namaproduk' => $item->namaproduk,
                'nilaikonversi' => $item->nilaikonversi,
                'satuanstandarfk' => $item->satuanviewidfk,
                'satuanstandar' => $item->ssview,
                'satuanviewfk' => $item->satuanviewidfk,
                'satuanview' => $item->ssview,
                'jmlstok' => $item->stock,
                'jumlah' => $item->jumlah / $item->nilaikonversi,
                'jumlahobat' =>  $item->qtydetailresep,
                'dosis' => $item->dosis,
                'hargasatuan' => $hargasatuan,
                'hargadiscount' => $hargadiscount,
                'total' => $total + $item->jasa,
                'jmldosis' => (string)$jmlxMakan . '/' . (string)$item->dosis . '/' . $item->kekuatan,
                'jasa' => $item->jasa,
                'ispagi'  => $item->ispagi,
                'issiang' => $item->issiang,
                'ismalam' => $item->ismalam,
                'issore' => $item->issore,
                'keterangan' => $item->keteranganpakai,
                'iskronis' => $item->iskronis,
                'satuanresepfk' => $item->satuanresepidfk,
                'satuanresep' => $item->satuanresep,
                'tglkadaluarsa' => $item->tglkadaluarsa,
                'persendiscount' => $item->persendiscount,
            );
        }

        $result = array(
            'detailresep' => $dataStruk,
            'pelayananPasien' => $pelayananPasien,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function DeletePelayananObat(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        \DB::beginTransaction();
        $r_SR = $request->all();

        try {

            $dataPasien = \DB::table('transaksireseptr as sr')
                ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'sr.daftarpasienruanganfk')
                ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                ->JOIN('pasienmt as pm', 'pm.id', '=', 'pd.normidfk')
                ->select('pm.namapasien', 'sr.noresep', 'pm.norm', 'pd.noregistrasi')
                ->where('sr.koders', $idProfile)
                ->where('sr.norec', $r_SR['norec'])
                ->first();

            $TRANS_KS = TransaksiKartuStok::where('keterangan',  'Resep Farmasi ' . ' ' . $dataPasien->noresep . ' ' . $dataPasien->norm . ' ' . $dataPasien->namapasien)
                ->where('koders', $idProfile)
                ->update([
                    'flagfk' => null
                ]);

            $newSR = TransaksiResep::where('norec', $r_SR['norec'])->where('koders', $idProfile)->first();
            if (isset($request['norec_order'])) {
                $newSR->transaksiorderfk = null;
            }
            $newSR->aktif = false;
            $newSR->save();

            $norec_SR = $newSR->norec;
            $tgl_SR = $newSR->tglresep;
            $noresep = $newSR->noresep;
            $idRuangan_SR = $newSR->ruanganidfk;

            $newPP = TransaksiPasien::where('strukresepidfk', $norec_SR)->where('koders', $idProfile)->get();
            $newPP2 = $newPP;
            foreach ($newPP as $r_PPL) {
                $norec_PP = $r_PPL->norec;

                $newPPD = TransaksiPasienDetail::where('transaksipasienfk', $norec_PP)->where('koders', $idProfile)->delete();
                $newP3 = PetugasPelaksana::where('transaksipasienfk', $norec_PP)->where('koders', $idProfile)->delete();

                $qtyJumlah = (float)$r_PPL['jumlah'] * (float)$r_PPL['nilaikonversi'];

                $produk = $r_PPL['produkidfk'];
                $GetNorec = TransaksiStok::where('nostrukterimafk', $r_PPL['strukterimaidfk'])
                    ->where('koders', $idProfile)
                    ->where('ruanganidfk', $idRuangan_SR)
                    ->where('produkidfk', $produk)
                    ->orderBy('tglpelayanan', 'desc')
                    ->first();
                $dataSPD = $GetNorec;
                $saldo = (float)$GetNorec->qtyproduk + (float)$qtyJumlah;
                $GetNorec->qtyproduk = $saldo;
                $GetNorec->save();

                $TRANSSALDOAWAL = DB::select(
                    DB::raw("
                  select sum(qtyproduk) as qty 
                  from transaksistoktr 
                  where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk
                "),
                    array(
                        'ruanganfk' => $idRuangan_SR,
                        'produkfk' => $produk,
                    )
                );

                foreach ($TRANSSALDOAWAL as $item) {
                    $saldoAwal = (float)$item->qty;
                }

                $tglUbah =  date('Y-m-d H:i:s');
                $qtyAwal = (float)$saldoAwal - (float)$qtyJumlah;
                $TRANS_KS = array(
                    "saldoawal" => $qtyAwal,
                    "qtyin" => (float)$qtyJumlah,
                    "qtyout" => 0,
                    "saldoakhir" => $qtyAwal + (float)$qtyJumlah,
                    "keterangan" => 'Hapus Resep Farmasi ' . $dataPasien->noresep . ' Pada Pasien :' . $dataPasien->norm . '/' . $dataPasien->namapasien,
                    "produkidfk" =>  $produk,
                    "ruanganidfk" => $idRuangan_SR,
                    "tglinput" => $tglUbah,
                    "tglkejadian" => $tglUbah,
                    "nostrukterimaidfk" => $r_PPL['strukterimaidfk'],
                    "norectransaksi" => $norec_PP,
                    "tabletransaksi" => 'pelayananpasien_t',
                );
                $this->KartuStok($idProfile, $TRANS_KS);
            }

            if (isset($request['norec_order'])) {
                $sOrder = TransaksiOrder::where('norec', $request['norec_order'])
                    ->where('koders', $idProfile)
                    ->update([
                        'statusorder' => 1,
                        'namapengambilorder' => null,
                        'tglambilorder' => null
                    ]);
                $antrianApotik = AntrianApotik::where('noresep', $newSR->noresep)->where('koders', $idProfile)->delete();
            }

            $newPP = TransaksiPasien::where('strukresepidfk', $norec_SR)->where('koders', $idProfile)->delete();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Simpan Pelayanan Pasien";
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Hapus Pelayanan Resep Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "stokprodukdetail" => $GetNorec,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Hapus Pelayanan Resep Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getNoStrukKasir(Request $request)
    {
        $JENISPROFILE = (int)$this->getDataKdProfile($request);
        $norecresep = "";
        if (isset($request['norecresep']) && $request['norecresep'] != "" && $request['norecresep'] != "undefined") {
            $norecresep = " AND sr.norec = '" . $request['norecresep'] . "' ";
        }
        $data = \DB::select(DB::raw("
            SELECT sp.nostruk
            FROM transaksipasientr AS pp
            INNER JOIN daftarpasienruangantr AS apd ON apd.norec = pp.daftarpasienruanganfk
            INNER JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk
            LEFT JOIN transaksireseptr AS sr ON sr.norec = pp.strukresepidfk
            LEFT JOIN strukpelayanantr AS sp ON sp.norec = pp.strukfk
            WHERE pp.koders = $JENISPROFILE
            $norecresep
            LIMIT 1"));
        return $this->respond($data);
    }

    public function getDaftarReturObat(Request $request)
    {
        $JENISPROFILE = $this->getDataKdProfile($request);
        $idProfile = (int) $JENISPROFILE;
        $dataLogin = $request->all();
        $data = \DB::table('transaksireturtr as srt')
            ->LEFTJOIN('transaksireseptr as sr', 'sr.norec', '=', 'srt.transaksiresepfk')
            ->LEFTJOIN('strukpelayanantr as sp', 'sp.norec', '=', 'srt.nostrukidfk')
            ->LEFTJOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'sr.daftarpasienruanganfk')
            ->LEFTJOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->LEFTJOIN('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'srt.pegawaiidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'srt.ruanganidfk')
            ->select(DB::raw("
                srt.norec,srt.tglretur,srt.noretur,pd.noregistrasi,
                CASE WHEN srt.transaksiresepfk IS NOT NULL THEN pd.noregistrasi ELSE '-' END AS noregistrasi,
	            CASE WHEN srt.transaksiresepfk IS NOT NULL THEN ps.norm ELSE sp.nostruk_intern END AS norm,
	            CASE WHEN srt.transaksiresepfk IS NOT NULL THEN ps.namapasien ELSE sp.namapasien_klien END AS namapasien,
                pg.namalengkap,ru.namaruangan,srt.norec,srt.keteranganalasan AS alasanretur,
			    srt.keteranganlainnya,sr.noresep,pd.tglregistrasi
            "))
            ->where('srt.aktif', true)
            ->where('srt.koders', $idProfile);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('srt.tglretur', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('srt.tglretur', '<=', $tgl);
        }
        if (isset($request['noRetur']) && $request['noRetur'] != "" && $request['noRetur'] != "undefined") {
            $data = $data->where('srt.noretur', 'ilike', '%' . $request['noRetur']);
        }
        if (isset($request['noRm']) && $request['noRm'] != "" && $request['noRm'] != "undefined") {
            $data = $data->where('ps.norm', 'like', '%' . $request['noRm'] . '%');
        }
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $data = $data->where('sp.namapasien_klien', 'like', '%' . $request['namaPasien'] . '%');
        }
        if (isset($request['noResep']) && $request['noResep'] != "" && $request['noResep'] != "undefined") {
            $data = $data->where('sp.nostruk', 'like', '%' . $request['noResep'] . '%');
        }
        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasin', $request['noReg']);
        }
        if (isset($request['ruanganId']) && $request['ruanganId'] != "" && $request['ruanganId'] != "undefined") {
            $data = $data->where('srt.ruanganidfk', $request['ruanganId']);
        }
        if (isset($request['jmlRows']) && $request['jmlRows'] != "" && $request['jmlRows'] != "undefined") {
            $data = $data->take($request['jmlRows']);
        }
        $data = $data->orderBy('srt.noretur');
        $data = $data->get();
        $results = [];
        foreach ($data as $item) {
            $details = \DB::select(
                DB::raw("
                SELECT sp.tglretur,spd.qtyproduk as jumlah,
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN CAST(pp.rke AS varchar) ELSE CAST(spp.resepke AS varchar) END AS rke,
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN jkm.jeniskemasan ELSE jkm1.jeniskemasan END AS jeniskemasan,
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN pr.namaproduk ELSE prs.namaproduk END AS namaproduk,
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN ss.satuanstandar ELSE ss1.satuanstandar END AS satuanstandar,                
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN pp.hargasatuan ELSE spp.hargasatuan END AS hargasatuan,
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN pp.hargadiscount ELSE spp.hargadiscount END AS hargadiscount,
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN pp.jasa ELSE spp.hargatambahan END AS jasa,
                ((CASE WHEN sp.transaksiresepfk IS NOT NULL THEN pp.hargasatuan ELSE spp.hargasatuan END
                -CASE WHEN sp.transaksiresepfk IS NOT NULL THEN pp.hargadiscount ELSE spp.hargadiscount END)*spd.qtyproduk)+
                CASE WHEN sp.transaksiresepfk IS NOT NULL THEN pp.jasa ELSE spp.hargatambahan END AS total  
                FROM transaksireturdetailtr as spd
                INNER JOIN transaksireturtr AS sp ON sp.norec = spd.transaksireturfk
                LEFT JOIN transaksipasientr AS pp ON pp.strukresepidfk = sp.transaksiresepfk
                LEFT JOIN strukpelayanandetailtr AS spp ON spp.nostrukidfk = sp.nostrukidfk
                INNER JOIN pelayananmt AS pr ON pr.id=spd.produkidfk
                LEFT JOIN satuanstandarmt AS ss ON ss.id=pp.satuanviewidfk
                LEFT JOIN jeniskemasanmt AS jkm ON jkm.id = pp.jeniskemasanidfk
                LEFT JOIN jeniskemasanmt AS jkm1 ON jkm1.id = spp.jeniskemasanidfk
                INNER JOIN pelayananmt AS prs ON prs.id=spp.produkidfk
                LEFT JOIN satuanstandarmt AS ss1 ON ss1.id=spp.satuanstandaridfk
                WHERE spd.koders = $idProfile AND spd.transaksireturfk=:norec
            "),
                array(
                    'norec' => $item->norec,
                )
            );
            $namapasien = $item->namapasien;
            $nocm = $item->norm;
            $noregistrasi = $item->noregistrasi;

            $results[] = array(
                'tglretur' => $item->tglretur,
                'noretur' => $item->noretur,
                'noresep' => $item->noresep,
                'noregistrasi' => $noregistrasi,
                'tglregistrasi' => $item->tglregistrasi,
                'nocm' => $nocm,
                'namapasien' => $namapasien,
                'namalengkap' => $item->namalengkap,
                'namaruangan' => $item->namaruangan,
                'norec' => $item->norec,
                'keteranganlainnya' => $item->keteranganlainnya,
                'alasanretur' => $item->alasanretur,
                'details' => $details,
            );
        }

        $result = array(
            'daftar' => $results,
            'datalogin' => $dataLogin,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function getDaftarPenjualanBebas(Request $request)
    {
        $JENISPROFILE = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiResep = (int) $this->settingDataFixed('KelJenisTransaksiResepNonLayanan', $JENISPROFILE);
        $kdInstalasiFarmasi = (int) $this->settingDataFixed('kdDepartemenFarmasi', $JENISPROFILE);
        $dataLogin = $request->all();
        $data = \DB::table('strukpelayanantr as sp')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaipenanggungjawabidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->LEFTJOIN('strukbuktipenerimaantr as sbm', 'sbm.norec', '=', 'sp.nosbmlastidfk')
            ->select(DB::raw("
                sp.norec,sp.tglstruk,sp.nostruk AS noresep,sp.nostruk_intern AS norm,sp.namapasien_klien AS namapasien,
			    pg.namalengkap AS dokter,ru.namaruangan,sp.noteleponfaks AS notelp,sp.namatempattujuan AS alamat,
			    sbm.nosbm,sp.namakurirpengirim
            "))
            ->where('sp.koders', $JENISPROFILE)
            ->where('sp.aktif', true)
            ->where('ru.instalasiidfk', $kdInstalasiFarmasi)
            ->where('sp.kelompoktransaksiidfk', $kdJenisTransaksiResep);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('sp.tglstruk', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('sp.tglstruk', '<=', $tgl);
        }
        if (isset($request['noResep']) && $request['noResep'] != "" && $request['noResep'] != "undefined") {
            $data = $data->where('sp.nostruk', 'ilike', '%' . $request['noResep']);
        }
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $data = $data->where('sp.namapasien_klien', 'ilike', '%' . $request['namaPasien'] . '%');
        }
        if (isset($request['ruid']) && $request['ruid'] != "" && $request['ruid'] != "undefined") {
            $data = $data->where('ru.id', '=', $request['ruid']);
        }
        if (isset($request['jmlRows']) && $request['jmlRows'] != "" && $request['jmlRows'] != "undefined") {
            $data = $data->take($request['jmlRows']);
        }
        $data = $data->orderBy('sp.nostruk');
        $data = $data->get();
        $result = [];
        foreach ($data as $item) {
            $details = \DB::select(
                DB::raw("
                    select spd.tglpelayanan,spd.resepke,jkm.jeniskemasan,pr.namaproduk,spd.jeniskemasanidfk,
                    ss.satuanstandar,spd.qtyproduk,spd.hargasatuan,spd.hargadiscount,
                    spd.hargatambahan,((spd.hargasatuan-spd.hargadiscount)*spd.qtyproduk)+spd.hargatambahan as total,
                    spd.tglkadaluarsa
                    from strukpelayanandetailtr as spd 
                    left JOIN pelayananmt as pr on pr.id=spd.produkidfk
                    left JOIN jeniskemasanmt as jkm on jkm.id=spd.jeniskemasanidfk
                    left JOIN satuanstandarmt as ss on ss.id=spd.satuanstandaridfk
                    where spd.aktif = true AND spd.koders = $JENISPROFILE and nostrukidfk=:norec"),
                array(
                    'norec' => $item->norec,
                )
            );
            $namapasienasdsa = $item->namapasien;
            if ($item->namakurirpengirim != '') {
                $namapasienasdsa = $item->namakurirpengirim . ' / ' . $item->namapasien;
            }
            $result[] = array(
                'norec_resep' => $item->norec,
                'tglstruk' => $item->tglstruk,
                'noresep' => $item->noresep,
                'norm' => $item->norm,
                'namapasien' => $namapasienasdsa,
                'dokter' => $item->dokter,
                'norec' => $item->norec,
                'namaruangan' => $item->namaruangan,
                'notelp' => $item->notelp,
                'alamat' => $item->alamat,
                'nosbm' => $item->nosbm,
                'details' => $details,
            );
        }

        $result = array(
            'daftar' => $result,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function saveInputResepNonLayanan(Request $request)
    {
        $JENISPROFILE = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiResep = (int) $this->settingDataFixed('KelJenisTransaksiResepNonLayanan', $JENISPROFILE);
        $kdInstalasiFarmasi = (int) $this->settingDataFixed('kdDepartemenFarmasi', $JENISPROFILE);
        $noResep = $this->generateCodeBySeqTable(new StrukPelayanan, 'nostruk', 13, 'TNL/' . $this->getDateTime()->format('ym/'), $JENISPROFILE);
        if ($noResep == '') {
            $ReportTrans = "Gagal mengumpukan data, Coba lagi.!";
            \DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
            return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
        }
        DB::beginTransaction();
        $ReportTrans = '';
        $req = $request->all();
        $namaPasien = $request['strukresep']['nocm'] . ' ' . $request['strukresep']['namapasien'];
        $resepOld = StrukPelayanan::where('norec', $request['strukresep']['noresep'])->where('koders', $JENISPROFILE);
        try {
            if ($request['strukresep']['noresep'] == '-') {
                $TRANSSTRUKPELAYANAN = new StrukPelayanan();
                $norecSP = $TRANSSTRUKPELAYANAN->generateNewId();
                $noStruk = $noResep;
                $TRANSSTRUKPELAYANAN->norec = $norecSP;
                $TRANSSTRUKPELAYANAN->koders = $JENISPROFILE;
                $TRANSSTRUKPELAYANAN->aktif = true;
                $TRANSSTRUKPELAYANAN->nostruk = $noStruk;
            } else {
                $TRANSSTRUKPELAYANAN = StrukPelayanan::where('norec', $request['strukresep']['noresep'])->where('koders', $JENISPROFILE)->first();
                $TRANSSTRUKPELAYANANOld = StrukPelayanan::where('norec', $request['strukresep']['noresep'])->where('koders', $JENISPROFILE)->first();
                $noStruk = $TRANSSTRUKPELAYANAN->nostruk;
                $norecSP = $TRANSSTRUKPELAYANAN->norec;
                TransaksiKartuStok::where('keterangan',  'Resep Farmasi Non Layanan ' . $noStruk . ' ' . $namaPasien)
                    ->where('koders', $JENISPROFILE)
                    ->update([
                        'flagfk' => null
                    ]);

                if ($request['strukresep']['ruanganfk'] == $TRANSSTRUKPELAYANANOld->ruanganidfk) {
                    $dataKembaliStok = DB::select(
                        DB::raw("
                    select spd.qtyproduk,spd.hasilkonversi,spd.ruanganidfk,spd.produkidfk,
                           spd.harganetto
                    from strukpelayanandetailtr as spd
                    where spd.koders = $JENISPROFILE and spd.nostrukidfk=:strukresepfk"),
                        array(
                            'strukresepfk' => $norecSP,
                        )
                    );
                    foreach ($dataKembaliStok as $item5) {
                        $TambahStok = (float)$item5->qtyproduk * (float)$item5->hasilkonversi;
                        $TRANSSTOKARR = TransaksiStok::where('ruanganidfk', $item5->ruanganidfk)
                            ->where('koders', $JENISPROFILE)
                            ->where('produkidfk', $item5->produkidfk)
                            ->orderby('tglkadaluarsa', 'desc')
                            ->first();
                        TransaksiStok::where('norec', $TRANSSTOKARR->norec)
                            ->where('koders', $JENISPROFILE)
                            ->update(
                                [
                                    'qtyproduk' => (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok
                                ]
                            );
                        $TRANSSALDOAWAL = DB::select(
                            DB::raw("
                        select sum(qtyproduk) as qty 
                        from transaksistoktr
                        where koders = $JENISPROFILE and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                            array(
                                'ruanganfk' => $item5->ruanganidfk,
                                'produkfk' => $item5->produkidfk,
                            )
                        );
                        $saldoAwal = 0;
                        foreach ($TRANSSALDOAWAL as $ITEMSs) {
                            $saldoAwal = (float)$ITEMSs->qty;
                        }

                        $tglnow =  date('Y-m-d H:i:s');
                        $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                        $qtyAwal = (float)$saldoAwal - $TambahStok;
                        $TRANS_KS = array(
                            "saldoawal" => $qtyAwal,
                            "qtyin" => $TambahStok,
                            "qtyout" => 0,
                            "saldoakhir" => (float)$saldoAwal,
                            "keterangan" => 'Ubah Resep Farmasi Non Layanan No. ' . $noStruk,
                            "produkidfk" =>  $item5->produkidfk,
                            "ruanganidfk" => $item5->ruanganidfk,
                            "tglinput" => $tglUbah,
                            "tglkejadian" => $tglUbah,
                            "nostrukterimaidfk" => $TRANSSTOKARR->nostrukterimafk,
                            "norectransaksi" => $TRANSSTOKARR->norec,
                            "tabletransaksi" => 'transaksistoktr',
                            "flagfk" => null,
                        );
                        $this->KartuStok($JENISPROFILE, $TRANS_KS);
                    }

                } else {
                    $dataKembaliStok = DB::select(
                        DB::raw("
                    select spd.qtyproduk,spd.hasilkonversi,spd.ruanganidfk ,
                    spd.produkidfk,spd.harganetto
                    from strukpelayanandetailtr as spd
                    where spd.koders = $JENISPROFILE and spd.nostrukidfk=:strukresepfk"),
                        array(
                            'strukresepfk' => $norecSP,
                        )
                    );
                    foreach ($dataKembaliStok as $item5) {
                        $TambahStok = (float)$item5->qtyproduk * (float)$item5->hasilkonversi;
                        $TRANSSTOKARR = TransaksiStok::where('ruanganidfk', $TRANSSTRUKPELAYANANOld->ruanganidfk)
                            ->where('koders', $JENISPROFILE)
                            ->where('produkidfk', $item5->produkidfk)
                            ->orderby('tglkadaluarsa', 'desc')
                            ->first();
                        TransaksiStok::where('norec', $TRANSSTOKARR->norec)
                            ->where('koders', $JENISPROFILE)
                            ->update(
                                [
                                    'qtyproduk' => (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok
                                ]
                            );
                        $TRANSSALDOAWAL = DB::select(
                            DB::raw("
                        select sum(qtyproduk) as qty 
                        from transaksistoktr
                        where koders = $JENISPROFILE and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                            array(
                                'ruanganfk' => $TRANSSTRUKPELAYANANOld->ruanganidfk,
                                'produkfk' => $item5->produkidfk,
                            )
                        );
                        $saldoAwal = 0;
                        foreach ($TRANSSALDOAWAL as $ITEMSs) {
                            $saldoAwal = (float)$ITEMSs->qty;
                        }

                        $tglnow =  date('Y-m-d H:i:s');
                        $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                        $qtyAwal = (float)$saldoAwal - $TambahStok;
                        $TRANS_KS = array(
                            "saldoawal" => $qtyAwal,
                            "qtyin" => $TambahStok,
                            "qtyout" => 0,
                            "saldoakhir" => (float)$saldoAwal,
                            "keterangan" => 'Ubah Resep Farmasi Non Layanan No. ' . $noStruk,
                            "produkidfk" =>  $item5->produkidfk,
                            "ruanganidfk" => $item5->ruanganidfk,
                            "tglinput" => $tglUbah,
                            "tglkejadian" => $tglUbah,
                            "nostrukterimaidfk" => $TRANSSTOKARR->nostrukterimafk,
                            "norectransaksi" => $TRANSSTOKARR->norec,
                            "tabletransaksi" => 'transaksistoktr',
                            "flagfk" => null,
                        );
                        $this->KartuStok($JENISPROFILE, $TRANS_KS);
                    }
                }
                $delSPD = StrukPelayananDetail::where('nostrukidfk', $request['strukresep']['noresep'])
                    ->where('koders', $JENISPROFILE)
                    ->delete();
            }

            $TRANSSTRUKPELAYANAN->kelompoktransaksiidfk = $kdJenisTransaksiResep;
            $TRANSSTRUKPELAYANAN->keteranganlainnya = $request['strukresep']['keteranganlainnya'];
            $TRANSSTRUKPELAYANAN->namapasien_klien = $request['strukresep']['namapasien'];
            $TRANSSTRUKPELAYANAN->nostruk_intern = $request['strukresep']['nocm'];
            $TRANSSTRUKPELAYANAN->namarekanan = 'Umum/Tunai';
            $TRANSSTRUKPELAYANAN->tglfaktur =  $request['strukresep']['tglLahir'];
            $TRANSSTRUKPELAYANAN->noteleponfaks =  $request['strukresep']['noTelepon'];
            $TRANSSTRUKPELAYANAN->namatempattujuan =  $request['strukresep']['alamat'];
            $TRANSSTRUKPELAYANAN->pegawaipenanggungjawabidfk = $request['strukresep']['penulisresepfk'];
            $TRANSSTRUKPELAYANAN->tglstruk = $request['strukresep']['tglresep'];
            $TRANSSTRUKPELAYANAN->totalharusdibayar = $request['strukresep']['totalharusdibayar'];
            $TRANSSTRUKPELAYANAN->ruanganidfk = $request['strukresep']['ruanganfk'];
            if (isset($request['strukresep']['karyawan'])) {
                $TRANSSTRUKPELAYANAN->namakurirpengirim = $request['strukresep']['karyawan'];
            }
            $TRANSSTRUKPELAYANAN->jeniskelaminidfk = $request['strukresep']['jkid'];
            $TRANSSTRUKPELAYANAN->save();
            foreach ($request['details'] as $item) {
                $qtyJumlah = (float)$item['jumlah'];
                $TRANSSTRUKPDETAIL = new StrukPelayananDetail();
                $norecKS = $TRANSSTRUKPDETAIL->generateNewId();
                $TRANSSTRUKPDETAIL->norec = $norecKS;
                $TRANSSTRUKPDETAIL->koders = $JENISPROFILE;
                $TRANSSTRUKPDETAIL->aktif = true;
                $TRANSSTRUKPDETAIL->nostrukidfk = $TRANSSTRUKPELAYANAN->norec;
                $TRANSSTRUKPDETAIL->asalprodukidfk = $item['asalprodukfk'];
                $TRANSSTRUKPDETAIL->jeniskemasanidfk = $item['jeniskemasanfk'];
                $TRANSSTRUKPDETAIL->produkidfk = $item['produkfk'];
                $TRANSSTRUKPDETAIL->ruanganidfk = $request['strukresep']['ruanganfk'];
                $TRANSSTRUKPDETAIL->ruanganstokidfk = $request['strukresep']['ruanganfk'];
                $TRANSSTRUKPDETAIL->satuanstandaridfk = $item['satuanstandarfk'];
                $TRANSSTRUKPDETAIL->aturanpakai = $item['aturanpakai'];
                $TRANSSTRUKPDETAIL->hargadiscount = $item['hargadiscount'];
                $TRANSSTRUKPDETAIL->hargadiscountgive = 0;
                $TRANSSTRUKPDETAIL->hargadiscountsave = 0;
                $TRANSSTRUKPDETAIL->harganetto = $item['hargasatuan'];
                $TRANSSTRUKPDETAIL->hargapph = 0;
                $TRANSSTRUKPDETAIL->hargappn = 0;
                $TRANSSTRUKPDETAIL->hargasatuan = $item['hargasatuan'];
                $TRANSSTRUKPDETAIL->hasilkonversi = $item['nilaikonversi'];
                $TRANSSTRUKPDETAIL->namaproduk = $item['namaproduk'];
                $TRANSSTRUKPDETAIL->resepke = $item['rke'];
                $TRANSSTRUKPDETAIL->hargasatuandijamin = 0;
                $TRANSSTRUKPDETAIL->hargasatuanppenjamin = 0;
                $TRANSSTRUKPDETAIL->hargasatuanpprofile = 0;
                if (isset($item['jasa'])) {
                    $TRANSSTRUKPDETAIL->hargatambahan = $item['jasa'];
                } else {
                    $TRANSSTRUKPDETAIL->hargatambahan = 0;
                }
                $TRANSSTRUKPDETAIL->isonsiteservice = 0;
                $TRANSSTRUKPDETAIL->persendiscount = 0;
                $TRANSSTRUKPDETAIL->qtyproduk = $item['jumlah'];
                $TRANSSTRUKPDETAIL->qtyprodukoutext = 0;
                $TRANSSTRUKPDETAIL->qtyprodukoutint = 0;
                $TRANSSTRUKPDETAIL->qtyprodukretur = 0;
                $TRANSSTRUKPDETAIL->satuan = '-';
                $TRANSSTRUKPDETAIL->satuanstandar = $item['satuanviewfk'];
                if (isset($item['satuanresep'])) {
                    $TRANSSTRUKPDETAIL->satuanresepidfk = $item['satuanresep'];
                }
                $TRANSSTRUKPDETAIL->tglpelayanan = $request['strukresep']['tglresep'];
                $TRANSSTRUKPDETAIL->is_terbayar = 0;
                $TRANSSTRUKPDETAIL->linetotal = 0;
                $TRANSSTRUKPDETAIL->qtydetailresep = $item['jumlah'];
                if (isset($item['ispagi'])) {
                    $TRANSSTRUKPDETAIL->ispagi = $item['ispagi'];
                }
                if (isset($item['issiang'])) {
                    $TRANSSTRUKPDETAIL->issiang = $item['issiang'];
                }
                if (isset($item['ismalam'])) {
                    $TRANSSTRUKPDETAIL->ismalam = $item['ismalam'];
                }
                if (isset($item['issore'])) {
                    $TRANSSTRUKPDETAIL->issore = $item['issore'];
                }
                $TRANSSTRUKPDETAIL->dosis = $item['dosis'];
                if (isset($item['tglkadaluarsa']) && $item['tglkadaluarsa'] != 'Invalid date' && $item['tglkadaluarsa'] != '') {
                    $TRANSSTRUKPDETAIL->tglkadaluarsa = $item['tglkadaluarsa'];
                }
                $TRANSSTRUKPDETAIL->save();

                $jmlPengurang = (float)$qtyJumlah;
                $kurangStok = 0;
                $TRANSSTOKARR = TransaksiStok::where('ruanganidfk', $request['strukresep']['ruanganfk'])
                    ->where('koders', $JENISPROFILE)
                    ->where('produkidfk', $item['produkfk'])
                    ->orderby('tglkadaluarsa', 'desc')
                    ->first();
                if ((float)$TRANSSTOKARR->qtyproduk <= (float)$jmlPengurang) {
                    $kurangStok = (float)$TRANSSTOKARR->qtyproduk;
                    $jmlPengurang = (float)$jmlPengurang - (float)$kurangStok;
                } else {
                    $kurangStok = (float)$jmlPengurang;
                    $jmlPengurang = (float)$jmlPengurang - (float)$kurangStok;
                }
                $TRANSSTOKARR->qtyproduk = (float)$TRANSSTOKARR->qtyproduk - (float)$kurangStok;
                $dadada[] = array('kurangStok' => (float)$kurangStok, 'jmlPengurang' => (float)$jmlPengurang, 'stok' => (float)$TRANSSTOKARR->qtyproduk);
                $TRANSSTOKARR->save();

                $TRANSSALDOAWAL = DB::select(
                    DB::raw("
                  select sum(qtyproduk) as qty from transaksistoktr
                  where koders = $JENISPROFILE and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                    array(
                        'ruanganfk' => $request['strukresep']['ruanganfk'],
                        'produkfk' => $item['produkfk'],
                    )
                );

                foreach ($TRANSSALDOAWAL as $ITEMS) {
                    $saldoAwal = (float)$ITEMS->qty;
                }

                $tglnow =  date('Y-m-d H:i:s');
                $qtyAwal = (float)$saldoAwal + $qtyJumlah;
                $TRANS_KS = array(
                    "saldoawal" => $qtyAwal,
                    "qtyin" => 0,
                    "qtyout" => $qtyJumlah,
                    "saldoakhir" => (float)$saldoAwal,
                    "keterangan" => 'Resep Farmasi Non Layanan ' . $noStruk . ' ' . $namaPasien,
                    "produkidfk" => $item['produkfk'],
                    "ruanganidfk" => $request['strukresep']['ruanganfk'],
                    "tglinput" => $tglnow,
                    "tglkejadian" => $tglnow,
                    "nostrukterimaidfk" => $item['nostrukterimafk'],
                    "norectransaksi" => $TRANSSTRUKPELAYANAN->norec,
                    "tabletransaksi" => 'strukpelayanantr',
                    "flagfk" => 7,
                );
                $this->KartuStok($JENISPROFILE, $TRANS_KS);
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Simpan Resep Non Layanan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $TRANSSTRUKPELAYANAN,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Simpan Resep Non Layanan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function DeleteResepNonLayanan(Request $request)
    {
        \DB::beginTransaction();
        $JENISPROFILE = (int) $this->getDataKdProfile($request);
        $dataKembaliStok = DB::select(
            DB::raw("
                select spd.qtyproduk,spd.hasilkonversi,spd.ruanganidfk,
                spd.produkidfk,spd.harganetto,sp.nostruk
                from strukpelayanandetailtr as spd 
                inner join strukpelayanantr as sp on sp.norec=spd.nostrukidfk
                where spd.koders = $JENISPROFILE and spd.nostrukidfk=:strukfk"),
            array(
                'strukfk' => $request['norec_sp'],
            )
        );
        try {
            foreach ($dataKembaliStok as $item5) {
                $TambahStok = (float)$item5->qtyproduk * (float)$item5->hasilkonversi;
                $TRANSSTOKARR = TransaksiStok::where('ruanganidfk', $item5->ruanganidfk)
                    ->where('koders', $JENISPROFILE)
                    ->where('produkidfk', $item5->produkidfk)
                    ->orderby('tglkadaluarsa', 'desc')
                    ->first();
                $TRANSSTOKARR->qtyproduk = (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok;
                $TRANSSTOKARR->save();

                $TRANSSALDOAWAL = DB::select(
                    DB::raw("
                    select sum(qtyproduk) as qty from transaksistoktr 
                    where koders = $JENISPROFILE and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                    array(
                        'ruanganfk' => $item5->ruanganidfk,
                        'produkfk' => $item5->produkidfk,
                    )
                );
                $saldoAwal = 0;
                foreach ($TRANSSALDOAWAL as $ITEMSs) {
                    $saldoAwal = (float)$ITEMSs->qty;
                }

                $dataPasien = \DB::table('strukpelayanantr as sr')
                    ->select(DB::raw("sr.namapasien_klien,sr.nostruk,sr.nostruk_intern"))
                    ->where('sr.koders', $JENISPROFILE)
                    ->where('sr.norec', $request['norec_sp'])
                    ->first();

                $TRANS_KS = TransaksiKartuStok::where('keterangan',  'Pelayanan Resep Non Layanan' . ' ' . $dataPasien->nostruk . ' ' . $dataPasien->nostruk_intern . ' ' . $dataPasien->namapasien_klien)
                    ->where('koders', $JENISPROFILE)
                    ->update([
                        'flagfk' => null
                    ]);

                $tglnow =  date('Y-m-d H:i:s');
                $qtyAwal = (float)$saldoAwal - $TambahStok;
                $TRANS_KS = array(
                    "saldoawal" => $qtyAwal,
                    "qtyin" => $TambahStok,
                    "qtyout" => 0,
                    "saldoakhir" => (float)$saldoAwal,
                    "keterangan" => 'Hapus Resep Farmasi Non Layanan No. ' . $item5->nostruk,
                    "produkidfk" =>  $item5->produkidfk,
                    "ruanganidfk" => $item5->ruanganidfk,
                    "tglinput" => $tglnow,
                    "tglkejadian" => $tglnow,
                    "nostrukterimaidfk" => $TRANSSTOKARR['nostrukterimafk'],
                    "norectransaksi" => $TRANSSTOKARR->norec,
                    "tabletransaksi" => 'transaksistoktr',
                    "flagfk" => null,
                );
                $this->KartuStok($JENISPROFILE, $TRANS_KS);
            }

            $datadel = StrukPelayananDetail::where('nostrukidfk', $request['norec_sp'])
                ->update([
                    'aktif' => false
                ]);
            $datadel2 = StrukPelayanan::where('norec', $request['norec_sp'])
                ->update([
                    'aktif' => false
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "gagal Pelayanan Pasien";
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Hapus Resep Non Layanan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Hapus Resep Non Layanan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDetailResepBebas(Request $request)
    {
        $JENISPROFILE = (int) $this->getDataKdProfile($request);
        $dataStruk = \DB::table('strukpelayanantr as sr')
            ->JOIN('pegawaimt as pg', 'pg.id', '=', 'sr.pegawaipenanggungjawabidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'sr.ruanganidfk')
            ->leftJoin('jeniskelaminmt AS jk', 'jk.id', '=', 'sr.jeniskelaminidfk')
            ->select(DB::raw("
                sr.nostruk,pg.id AS pgid,pg.namalengkap,ru.id,ru.namaruangan,
			    sr.nostruk_intern AS norm,sr.namapasien_klien AS namapasien,sr.tglfaktur AS tgllahir,
			    sr.noteleponfaks AS notelepon,sr.namatempattujuan AS alamatlengkap,sr.tglstruk AS tglresep,
			    sr.jeniskelaminidfk AS jkid,jk.jeniskelamin
            "))
            ->where('sr.koders', $JENISPROFILE);

        if (isset($request['norecResep']) && $request['norecResep'] != "" && $request['norecResep'] != "undefined") {
            $dataStruk = $dataStruk->where('sr.norec', '=', $request['norecResep']);
        }

        $dataStruk = $dataStruk->first();

        $data = \DB::table('strukpelayanantr as sp')
            ->JOIN('strukpelayanandetailtr as spd', 'spd.nostrukidfk', '=', 'sp.norec')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->JOIN('jeniskemasanmt as jk', 'jk.id', '=', 'spd.jeniskemasanidfk')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'spd.produkidfk')
            ->JOIN('satuanstandarmt as ss', 'ss.id', '=', 'spd.satuanstandaridfk')
            ->leftJoin('satuanresepmt as sn', 'sn.id', '=', 'spd.satuanresepidfk')
            ->leftJoin('asalprodukmt as ap', 'ap.id', '=', 'spd.asalprodukidfk')
            ->select(DB::raw("
                sp.nostruk,spd.hargasatuan,spd.qtyprodukoutext,sp.ruanganidfk,ru.namaruangan,spd.resepke,jk.id AS jkid,
			    jk.jeniskemasan,spd.aturanpakai,spd.produkidfk AS produkfk,pr.namaproduk,spd.hasilkonversi AS nilaikonversi,
			    spd.satuanstandaridfk,ss.satuanstandar,spd.satuanstandar AS satuanviewfk,ss.satuanstandar AS ssview,
			    spd.qtyproduk AS jumlah,spd.hargadiscount,spd.hargatambahan AS jasa,spd.hargasatuan AS hargajual,
			    spd.harganetto,spd.qtydetailresep,spd.ispagi,spd.issiang,spd.ismalam,spd.issore,pr.kekuatan,spd.dosis,
			    spd.satuanresepidfk,sn.satuanresep,spd.tglkadaluarsa,spd.asalprodukidfk,ap.asalproduk
            "))
            ->where('sp.koders', $JENISPROFILE);

        if (isset($request['norecResep']) && $request['norecResep'] != "" && $request['norecResep'] != "undefined") {
            $data = $data->where('sp.norec', '=', $request['norecResep']);
        }
        $data = $data->get();

        $pelayananPasien = [];
        $i = 0;
        $dataStok = \DB::select(
            DB::raw("
                        select sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,
                               spd.harganetto2 as hargajual,spd.harganetto2 as harganetto,spd.hargadiscount,
                               sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk
                        from transaksistoktr as spd
                        inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk                    
                        where spd.koders = $JENISPROFILE and spd.ruanganidfk =:ruanganid
                        group by sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,
                                spd.harganetto2,spd.hargadiscount,spd.ruanganidfk
                        order By sk.tglstruk"),
            array(
                'ruanganid' => $dataStruk->id
            )
        );
        $hargajual = 0;
        $harganetto = 0;
        $nostrukterimafk = '';
        $asalprodukfk = 0;
        $asalproduk = '';
        $jmlstok = 0;
        $hargasatuan = 0;
        $hargadiscount = 0;
        $total = 0;
        $aturanpakaifk = 0;
        foreach ($data as $item) {
            $i = $i + 1;
            foreach ($dataStok as $item2) {
                $hargajual = 0;
                $harganetto = 0;
                $hargadiscount = 0;
                if ($item2->produkidfk == $item->produkfk) {
                    if ($item2->qtyproduk >= 0) {
                        $hargajual = $item->hargajual;
                        $harganetto = $item->harganetto;
                        $nostrukterimafk = $item2->norec;
                        $asalprodukfk = $item2->asalprodukidfk;
                        $jmlstok = $item2->qtyproduk;
                        $hargasatuan = $harganetto;
                        $hargadiscount = $item->hargadiscount;
                        $total = (((float)$item->jumlah * ((float)$hargasatuan - (float)$hargadiscount)));
                        break;
                    }
                }
            }
            $jmlxMakan = (((float)$item->jumlah / (float)$item->nilaikonversi) / (float)$item->dosis) * (float)$item->kekuatan;
            $pelayananPasien[] = array(
                'no' => $i,
                'noregistrasifk' => '',
                'tglregistrasi' => '',
                'generik' => null,
                'hargajual' => $hargajual,
                'jenisobatfk' => '',
                'kelasfk' => '',
                'stock' => $jmlstok,
                'harganetto' => $harganetto,
                'nostrukterimafk' => $nostrukterimafk,
                'ruanganfk' => $item->ruanganidfk,
                'rke' => $item->resepke,
                'jeniskemasanfk' => $item->jkid,
                'jeniskemasan' => $item->jeniskemasan,
                'aturanpakaifk' => $aturanpakaifk,
                'aturanpakai' => $item->aturanpakai,
                'routefk' => 0,
                'route' => '',
                'asalprodukfk' => $item->asalprodukidfk,
                'asalproduk' => $item->asalproduk,
                'produkfk' => $item->produkfk,
                'namaproduk' => $item->namaproduk,
                'nilaikonversi' => $item->nilaikonversi,
                'satuanstandarfk' => $item->satuanviewfk,
                'satuanstandar' => $item->ssview,
                'satuanviewfk' => $item->satuanviewfk,
                'satuanview' => $item->ssview,
                'jmlstok' => $jmlstok,
                'jumlah' => $item->jumlah / $item->nilaikonversi,
                'dosis' => $item->dosis,
                'hargasatuan' => $hargasatuan,
                'hargadiscount' => $hargadiscount,
                'total' => $total + $item->jasa,
                'jasa' => $item->jasa,
                'jmldosis' => (string)$jmlxMakan . '/' . (string)$item->dosis . '/' . $item->kekuatan,
                'ispagi'  => $item->ispagi,
                'issiang' => $item->issiang,
                'ismalam' => $item->ismalam,
                'issore' => $item->issore,
                'jumlahobat' => $item->qtydetailresep,
                'satuanresep' => $item->satuanresepidfk,
                'satuanresepview' => $item->satuanresep,
                'tglkadaluarsa' => $item->tglkadaluarsa,
            );
        }

        $result = array(
            'detailresep' => $dataStruk,
            'pelayananPasien' => $pelayananPasien,
            'data' => $data,
            'datastok' => $dataStok,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function saveReturResepNonLayanan(Request $request)
    {
        \DB::beginTransaction();
        $JENISPROFILE = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiResep = (int) $this->settingDataFixed('KelJenisTransaksiResepNonLayanan', $JENISPROFILE);
        $kdInstalasiFarmasi = (int) $this->settingDataFixed('kdDepartemenFarmasi', $JENISPROFILE);
        $kdJenisTransaksiReturResep = (int) $this->settingDataFixed('KdJenisTransaksiReturResep', $JENISPROFILE);
        $req = $request->all();
        $qtySebelumRetur = 0;
        try {
            if ($request['strukresep']['noresep'] == '') {
                $TRANSSTRUKPELAYANAN = new StrukPelayanan();
                $norecSP = $TRANSSTRUKPELAYANAN->generateNewId();
                $noStruk = $this->generateCode(new StrukPelayanan, 'nostruk', 13, 'ONL/' . $this->getDateTime()->format('ym/'), $JENISPROFILE);
                $TRANSSTRUKPELAYANAN->norec = $norecSP;
                $TRANSSTRUKPELAYANAN->koders = $JENISPROFILE;
                $TRANSSTRUKPELAYANAN->aktif = true;
                $TRANSSTRUKPELAYANAN->nostruk = $noStruk;
            } else {
                $TRANSSTRUKPELAYANAN = StrukPelayanan::where('norec', $request['strukresep']['noresep'])->where('koders', $JENISPROFILE)->first();
                $noStruk = $TRANSSTRUKPELAYANAN->nostruk;
                $norecSP = $TRANSSTRUKPELAYANAN->norec;
            }
            $TRANSSTRUKPELAYANAN->kelompoktransaksiidfk = $kdJenisTransaksiResep;
            $TRANSSTRUKPELAYANAN->keteranganlainnya = $req['strukresep']['keteranganlainnya'];
            $TRANSSTRUKPELAYANAN->namapasien_klien = $req['strukresep']['namapasien'];
            $TRANSSTRUKPELAYANAN->nostruk_intern = $req['strukresep']['nocm'];
            $TRANSSTRUKPELAYANAN->namarekanan = 'Umum/Tunai';
            $TRANSSTRUKPELAYANAN->tglfaktur =  $req['strukresep']['tglLahir'];
            $TRANSSTRUKPELAYANAN->noteleponfaks =  $req['strukresep']['noTelepon'];
            $TRANSSTRUKPELAYANAN->namatempattujuan =  $req['strukresep']['alamat'];
            $TRANSSTRUKPELAYANAN->pegawaipenanggungjawabidfk = $req['strukresep']['penulisresepfk'];
            $TRANSSTRUKPELAYANAN->tglstruk = $req['strukresep']['tglresep'];
            $TRANSSTRUKPELAYANAN->totalharusdibayar = $req['strukresep']['totalharusdibayar'];
            $TRANSSTRUKPELAYANAN->ruanganidfk = $req['strukresep']['ruanganfk'];
            $TRANSSTRUKPELAYANAN->jeniskelaminidfk = $request['strukresep']['jkid'];
            $TRANSSTRUKPELAYANAN->save();

            $namaPasien = $req['strukresep']['nocm'] . ' ' . $req['strukresep']['namapasien'];
            $nocmNama = $req['strukresep']['nocm'] . '-' . $req['strukresep']['namapasien'];

            if ($request['strukresep']['noresep'] != '-') {
                if ($request['strukresep']['retur'] != '') {
                    $TRANSRETUR = new TransaksiRetur();
                    $norecSRetur = $TRANSRETUR->generateNewId();
                    $noRetur = $this->generateCode(new TransaksiRetur, 'noretur', 12, 'Ret/' . $this->getDateTime()->format('ym') . '/', $JENISPROFILE);
                    $TRANSRETUR->norec = $norecSRetur;
                    $TRANSRETUR->koders = $JENISPROFILE;
                    $TRANSRETUR->aktif = true;
                    $TRANSRETUR->kelompoktransaksiidfk = $kdJenisTransaksiReturResep;
                    $TRANSRETUR->keteranganalasan = $request['strukresep']['alasan'];
                    $TRANSRETUR->keteranganlainnya = 'RETUR RESEP NON LAYANAN';
                    $TRANSRETUR->noretur = $noRetur;
                    $TRANSRETUR->ruanganidfk = $request['strukresep']['ruanganfk'];
                    $TRANSRETUR->pegawaiidfk = $request['strukresep']['pegawairetur'];
                    $TRANSRETUR->tglretur = $this->getDateTime()->format('Y-m-d H:i:s');
                    $TRANSRETUR->nostrukidfk = $norecSP;
                    $TRANSRETUR->save();
                    $transStatus = 'false';
                    $norec_retur = $TRANSRETUR->norec;
                }
                $r_PP = $request['details'];
                foreach ($r_PP as $r_PPLXXXX) {
                    $TambahStok = 0;
                    $TambahStok = (float)$r_PPLXXXX['jumlah'] * (float)$r_PPLXXXX['nilaikonversi'];
                    $TRANSSTOKARR = TransaksiStok::where('nostrukterimafk', $r_PPLXXXX['nostrukterimafk'])
                        ->where('koders', $JENISPROFILE)
                        ->where('ruanganidfk', $req['strukresep']['ruanganfk'])
                        ->where('produkidfk', $r_PPLXXXX['produkfk'])
                        ->orderby('tglkadaluarsa', 'desc')
                        ->first();
                    $qtySebelumRetur = $TRANSSTOKARR->qtyproduk;
                    $TRANSSTOKARR->qtyproduk = (float)$TRANSSTOKARR->qtyproduk + (float)$TambahStok;
                    $TRANSSTOKARR->save();
                    $TRANSSALDOAWAL = DB::select(
                        DB::raw("select sum(qtyproduk) as qty from transaksistoktr 
                        where koders = $JENISPROFILE and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                        array(
                            'ruanganfk' => $req['strukresep']['ruanganfk'],
                            'produkfk' =>  $r_PPLXXXX['produkfk'],
                        )
                    );
                    $saldoAwal = 0;
                    foreach ($TRANSSALDOAWAL as $ITEMSs) {
                        $saldoAwal = (float)$ITEMSs->qty;
                    }

                    TransaksiKartuStok::where('keterangan',  'Resep Farmasi Non Layanan ' . $noStruk . ' ' . $namaPasien)
                        ->where('koders', $JENISPROFILE)
                        ->update([
                            'flagfk' => null
                        ]);

                    $tglnow =  date('Y-m-d H:i:s');
                    $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                    $qtyAwal = (float)$saldoAwal - $TambahStok;
                    $TRANS_KS = array(
                        "saldoawal" => $qtyAwal,
                        "qtyin" => $TambahStok,
                        "qtyout" => 0,
                        "saldoakhir" => (float)$saldoAwal,
                        "keterangan" => 'Ubah Resep Non Layanan No. ' . $noStruk,
                        "produkidfk" =>  $r_PPLXXXX['produkfk'],
                        "ruanganidfk" => $req['strukresep']['ruanganfk'],
                        "tglinput" => $tglUbah,
                        "tglkejadian" => $tglUbah,
                        "nostrukterimaidfk" => $TRANSSTOKARR->nostrukterimafk,
                        "norectransaksi" => $TRANSSTOKARR->norec,
                        "tabletransaksi" => 'transaksistoktr',
                        "flagfk" => null,
                    );
                    $this->KartuStok($JENISPROFILE, $TRANS_KS);
                }
                $delSPD = StrukPelayananDetail::where('nostrukidfk', $request['strukresep']['noresep'])
                    ->where('koders', $JENISPROFILE)
                    ->delete();
            }

            foreach ($req['details'] as $item) {
                $qtyJumlah = 0;
                if ((int)$item['jmlretur'] != 0) {
                    $qtyJumlah = $item['jmlretur'];
                } else {
                    $qtyJumlah = $item['jumlah'];
                }
                $TRANSSTRUKPDETAIL = new StrukPelayananDetail();
                $norecKS = $TRANSSTRUKPDETAIL->generateNewId();
                $TRANSSTRUKPDETAIL->norec = $norecKS;
                $TRANSSTRUKPDETAIL->koders = $JENISPROFILE;
                $TRANSSTRUKPDETAIL->aktif = true;
                $TRANSSTRUKPDETAIL->nostrukidfk = $request['strukresep']['noresep'];
                $TRANSSTRUKPDETAIL->asalprodukidfk = $item['asalprodukfk'];
                $TRANSSTRUKPDETAIL->jeniskemasanidfk = $item['jeniskemasanfk'];
                $TRANSSTRUKPDETAIL->produkidfk = $item['produkfk'];
                $TRANSSTRUKPDETAIL->ruanganidfk = $req['strukresep']['ruanganfk'];
                $TRANSSTRUKPDETAIL->ruanganstokidfk = $req['strukresep']['ruanganfk'];
                $TRANSSTRUKPDETAIL->satuanstandaridfk = $item['satuanstandarfk'];
                $TRANSSTRUKPDETAIL->aturanpakai = $item['aturanpakai'];
                $TRANSSTRUKPDETAIL->hargadiscount = $item['hargadiscount'];
                $TRANSSTRUKPDETAIL->hargadiscountgive = 0;
                $TRANSSTRUKPDETAIL->hargadiscountsave = 0;
                $TRANSSTRUKPDETAIL->harganetto = $item['hargasatuan'];
                $TRANSSTRUKPDETAIL->hargapph = 0;
                $TRANSSTRUKPDETAIL->hargappn = 0;
                $TRANSSTRUKPDETAIL->hargasatuan = $item['hargasatuan'];
                $TRANSSTRUKPDETAIL->hasilkonversi = (float)$item['nilaikonversi'];
                $TRANSSTRUKPDETAIL->namaproduk = $item['namaproduk'];
                $TRANSSTRUKPDETAIL->resepke = $item['rke'];
                $TRANSSTRUKPDETAIL->hargasatuandijamin = 0;
                $TRANSSTRUKPDETAIL->hargasatuanppenjamin = 0;
                $TRANSSTRUKPDETAIL->hargasatuanpprofile = 0;
                if (isset($item['jasa'])) {
                    $TRANSSTRUKPDETAIL->hargatambahan = $item['jasa'];
                } else {
                    $TRANSSTRUKPDETAIL->hargatambahan = 0;
                }
                $TRANSSTRUKPDETAIL->isonsiteservice = 0;
                $TRANSSTRUKPDETAIL->persendiscount = 0;
                if ((int)$item['jmlretur'] != 0) {
                    $TRANSSTRUKPDETAIL->qtyproduk = $item['jmlretur'];
                } else {
                    $TRANSSTRUKPDETAIL->qtyproduk = $item['jumlah'];
                }
                $TRANSSTRUKPDETAIL->qtyprodukoutext = 0;
                $TRANSSTRUKPDETAIL->qtyprodukoutint = 0;
                $TRANSSTRUKPDETAIL->qtyprodukretur = 0;
                $TRANSSTRUKPDETAIL->satuan = '-';
                $TRANSSTRUKPDETAIL->satuanstandar = $item['satuanviewfk'];
                $TRANSSTRUKPDETAIL->tglpelayanan = $req['strukresep']['tglresep'];
                $TRANSSTRUKPDETAIL->is_terbayar = 0;
                $TRANSSTRUKPDETAIL->linetotal = 0;
                $TRANSSTRUKPDETAIL->save();

                if ((int)$item['jmlretur'] != 0) {
                    $TRANSRETURDETAIL = new TransaksiReturDetail();
                    $norecPPR = $TRANSRETURDETAIL->generateNewId();
                    $TRANSRETURDETAIL->norec = $norecPPR;
                    $TRANSRETURDETAIL->koders = $JENISPROFILE;
                    $TRANSRETURDETAIL->aktif = true;
                    $TRANSRETURDETAIL->transaksireturfk = $norec_retur;
                    $TRANSRETURDETAIL->produkidfk = $item['produkfk'];
                    $TRANSRETURDETAIL->hargadiscount = $item['hargadiscount'];
                    $TRANSRETURDETAIL->harganetto1 = $item['hargajual'];
                    $TRANSRETURDETAIL->harganetto2 = $item['hargajual'];
                    if (isset($item['persendiscount'])) {
                        $TRANSRETURDETAIL->persendiscount = $item['persendiscount'];
                    } else {
                        $TRANSRETURDETAIL->persendiscount = 0;
                    }
                    $TRANSRETURDETAIL->qtyproduk = $item['jmlretur'];
                    $TRANSRETURDETAIL->qtyprodukonhand = 0;
                    $TRANSRETURDETAIL->qtyprodukoutext = 0;
                    $TRANSRETURDETAIL->qtyprodukoutint = 0;
                    $TRANSRETURDETAIL->nostrukterimaidfk = $item['nostrukterimafk'];
                    $TRANSRETURDETAIL->save();
                    $norec_PPR = $TRANSRETURDETAIL->norec;

                    $TRANSSALDOAWAL = DB::select(
                        DB::raw("select sum(qtyproduk) as qty from transaksistoktr 
                            where koders = $JENISPROFILE and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                        array(
                            'ruanganfk' => $req['strukresep']['ruanganfk'],
                            'produkfk' =>  $item['produkfk'],
                        )
                    );
                    $saldoAwal = 0;
                    foreach ($TRANSSALDOAWAL as $ITEMSs) {
                        $saldoAwal = (float)$ITEMSs->qty;
                    }

                    $tglnow =  date('Y-m-d H:i:s');
                    $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                    $tglretur = date('Y-m-d H:i:s', strtotime('+1 minutes', strtotime($tglnow)));
                    $qtyAwal = (float)$saldoAwal;
                    $TRANS_KS = array(
                        "saldoawal" => $qtyAwal,
                        "qtyin" => 0,
                        "qtyout" => (float)$item['jumlah'],
                        "saldoakhir" => (float)$saldoAwal - (float)$item['jumlah'],
                        "keterangan" => 'Resep Farmasi Non Layanan ' . $noStruk . ' ' . $namaPasien,
                        "produkidfk" => $item['produkfk'],
                        "ruanganidfk" => $request['strukresep']['ruanganfk'],
                        "tglinput" => $tglUbah,
                        "tglkejadian" => $tglUbah,
                        "nostrukterimaidfk" => $item['nostrukterimafk'],
                        "norectransaksi" => $TRANSSTRUKPELAYANAN->norec,
                        "tabletransaksi" => 'strukpelayanantr',
                        "flagfk" => 7,
                    );
                    $this->KartuStok($JENISPROFILE, $TRANS_KS);

                    $qtyAwalRetur = (float)$saldoAwal - (float)$item['jumlah'];
                    $TRANS_KSs = array(
                        "saldoawal" => $qtyAwalRetur,
                        "qtyin" => (float)$item['jmlretur'],
                        "qtyout" => 0,
                        "saldoakhir" => $qtyAwalRetur + (float)$item['jmlretur'],
                        "keterangan" => 'Retur Resep Non Layanan No. ' . $noStruk,
                        "produkidfk" =>  $r_PPLXXXX['produkfk'],
                        "ruanganidfk" => $req['strukresep']['ruanganfk'],
                        "tglinput" => $tglretur,
                        "tglkejadian" => $tglretur,
                        "nostrukterimaidfk" => $TRANSSTOKARR->nostrukterimafk,
                        "norectransaksi" => $TRANSSTOKARR->norec,
                        "tabletransaksi" => 'transaksistoktr',
                        "flagfk" => 3,
                    );
                    $this->KartuStok($JENISPROFILE, $TRANS_KSs);

                    $TambahStok = (float)$item['jmlretur'] * (float)$item['nilaikonversi'];
                    $TRANSSTOKARR = TransaksiStok::where('nostrukterimafk', $item['nostrukterimafk'])
                        ->where('koders', $JENISPROFILE)
                        ->where('ruanganidfk', $req['strukresep']['ruanganfk'])
                        ->where('produkidfk', $item['produkfk'])
                        ->orderby('tglkadaluarsa', 'desc')
                        ->first();
                    $TRANSSTOKARR->qtyproduk = ((float)$TRANSSTOKARR->qtyproduk - (float)$item['jumlah']) + (float)$TambahStok;
                    $TRANSSTOKARR->save();
                } else {
                    $jmlPengurang = (float)$item['jumlah'];
                    $kurangStok = 0;
                    $TRANSSTOKARR = TransaksiStok::where('ruanganidfk', $request['strukresep']['ruanganfk'])
                        ->where('koders', $JENISPROFILE)
                        ->where('produkidfk', $item['produkfk'])
                        ->first();
                    if ((float)$TRANSSTOKARR->qtyproduk <= (float)$jmlPengurang) {
                        $kurangStok = (float)$TRANSSTOKARR->qtyproduk;
                        $jmlPengurang = (float)$jmlPengurang - (float)$kurangStok;
                    } else {
                        $kurangStok = (float)$jmlPengurang;
                        $jmlPengurang = (float)$jmlPengurang - (float)$kurangStok;
                    }
                    $TRANSSTOKARR->qtyproduk = (float)$TRANSSTOKARR->qtyproduk - (float)$kurangStok;
                    $dadada[] = array('kurangStok' => (float)$kurangStok, 'jmlPengurang' => (float)$jmlPengurang, 'stok' => (float)$TRANSSTOKARR->qtyproduk);
                    $TRANSSTOKARR->save();

                    $TRANSSALDOAWAL = DB::select(
                        DB::raw("
                    select sum(qtyproduk) as qty from transaksistoktr
                    where koders = $JENISPROFILE and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                        array(
                            'ruanganfk' => $request['strukresep']['ruanganfk'],
                            'produkfk' => $item['produkfk'],
                        )
                    );

                    foreach ($TRANSSALDOAWAL as $ITEMS) {
                        $saldoAwal = (float)$ITEMS->qty;
                    }

                    $tglnow =  date('Y-m-d H:i:s');
                    $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                    $qtyAwal = (float)$saldoAwal + $qtyJumlah;
                    $TRANS_KS = array(
                        "saldoawal" => $qtyAwal,
                        "qtyin" => 0,
                        "qtyout" => $qtyJumlah,
                        "saldoakhir" => (float)$saldoAwal,
                        "keterangan" => 'Resep Farmasi Non Layanan ' . $noStruk . ' ' . $namaPasien,
                        "produkidfk" => $item['produkfk'],
                        "ruanganidfk" => $request['strukresep']['ruanganfk'],
                        "tglinput" => $tglUbah,
                        "tglkejadian" => $tglUbah,
                        "nostrukterimaidfk" => $item['nostrukterimafk'],
                        "norectransaksi" => $TRANSSTRUKPELAYANAN->norec,
                        "tabletransaksi" => 'strukpelayanantr',
                        "flagfk" => 7,
                    );
                    $this->KartuStok($JENISPROFILE, $TRANS_KS);
                }
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Simpan Struk Pelayanan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $TRANSSTRUKPELAYANAN,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Simpan Struk Pelayanan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "data" => $TRANSSTRUKPELAYANAN,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getDataComboResepRev(Request $request)
    {
        $idProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dept = $this->settingDataFixed('kdDepartemenFarmasi', $idProfile);
        $dataRuanganFamasi = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->where('ru.koders', $idProfile)
            ->where('ru.instalasiidfk', $dept)
            ->where('ru.aktif', true)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataJenisKemasan = \DB::table('jeniskemasanmt as jk')
            ->select('jk.id', 'jk.jeniskemasan')
            ->where('jk.koders', $idProfile)
            ->where('jk.aktif', true)
            ->get();
        $dataJenisRacikan = \DB::table('jenisracikanmt as jk')
            ->select('jk.id', 'jk.jenisracikan')
            ->where('jk.koders', $idProfile)
            ->where('jk.aktif', true)
            ->get();

        $dataAsalProduk = \DB::table('asalprodukmt as ap')
            ->JOIN('transaksistoktr as spd', 'spd.asalprodukidfk', '=', 'ap.id')
            ->select('ap.id', 'ap.asalproduk')
            ->where('ap.koders', $idProfile)
            ->where('ap.aktif', true)
            ->orderBy('ap.id')
            ->groupBy('ap.id', 'ap.asalproduk')
            ->get();

        $dataSatuanResep = \DB::table('satuanresepmt as kp')
            ->select('kp.id', 'kp.satuanresep')
            ->where('kp.aktif', true)
            ->where('kp.koders', $idProfile)
            ->orderBy('kp.satuanresep')
            ->get();

        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter', $idProfile);
        $dataDokter = \DB::table('pegawaimt as ru')
            ->select('ru.id', 'ru.namalengkap')
            ->where('ru.aktif', true)
            ->where('ru.objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->where('ru.koders', (int)$idProfile)
            ->orderBy('ru.namalengkap')
            ->get();

        $result = array(
            'ruanganfarmasi' => $dataRuanganFamasi,
            'jeniskemasan' => $dataJenisKemasan,
            'asalproduk' => $dataAsalProduk,
            'jenisracikan' => $dataJenisRacikan,
            'satuanresep' => $dataSatuanResep,
            'dokter' => $dataDokter,
            'tglserver'=>date('Y-m-d H:i:s'),
            'message' => 'slvR',
        );

        return $this->respond($result);
    }
    public function getNoreservasi(Request $request)
    {
        $result = DB::table('registrasipasientr')
        ->select('statusschedule as noreservasi')
        ->where('norec',$request['norec_pd'])
        ->first();
       return $this->respond($result);
    }
}
