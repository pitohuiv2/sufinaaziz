<?php


namespace App\Http\Controllers\Logistik;


use App\Http\Controllers\ApiController;
use App\Master\Ruangan;
use App\Traits\InternalList;
use App\Traits\PelayananPasienTrait;
use App\Traits\SettingDataFixedTrait;
use App\Traits\Valet;
use App\Transaksi\TransaksiKartuStok;
use App\Transaksi\TransaksiKirimDetail;
use App\Transaksi\TransaksiOrderDetail;
use App\Transaksi\TransaksiRealisasiDetail;
use App\Transaksi\TransaksiStok;
use App\Transaksi\TransaksiAdjustmentStok;
use App\Transaksi\TransaksiOpname;
use App\Transaksi\TransaksiClosing;
use App\Transaksi\TransaksiKirim;
use App\Transaksi\TransaksiOrder;
use App\Transaksi\StrukPelayanan;
use App\Transaksi\StrukPelayananDetail;
use App\Transaksi\TransaksiRealisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogistikC  extends ApiController
{
    use Valet, PelayananPasienTrait, SettingDataFixedTrait, InternalList;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getDataComboLogistik(Request $request)
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

        $deptLayanan = explode(',', $this->settingDataFixed('kdDepartemenLayanan', $kdProfile));
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
            ->where('ru.koders', (int)$kdProfile)
            ->whereIn('ru.instalasiidfk', $kdDepartemenLayanan)
            ->orderBy('ru.namaruangan')
            ->get();

        $kdFarmasi = (int) $this->settingDataFixed('kdDepartemenFarmasi', $kdProfile);
        $dataRuanganFarmasi = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->where('ru.instalasiidfk', $kdFarmasi)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataStatusPengerjaan = \DB::table('statuspengerjaanmt as ru')
            ->select('ru.id', 'ru.statuspengerjaan as status')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.id')
            ->get();

        $JenisKirim = \DB::table('jeniskirimmt')
            ->select('id', 'jeniskirim')
            ->where('aktif', true)
            ->where('koders', (int)$kdProfile)
            ->orderBy('id')
            ->get();

        $result = array(
            'departemen' => $dataDepartemen,
            'kelompokpasien' => $dataKelompok,
            'kelas' => $dataKelas,
            'ruanglayanan' => $dataRuanganLayanan,
            'ruangfarmasi' => $dataRuanganFarmasi,
            'kelompokpasien' => $dataKelompok,
            'statuspengerjaan' => $dataStatusPengerjaan,
            'jeniskirim' => $JenisKirim,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDataComboJenisProduk(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataDetailJenisProduk = \DB::table('detailjenisprodukmt as dp')
            ->select('dp.id', 'dp.detailjenisproduk', 'dp.jenisprodukidfk')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.detailjenisproduk')
            ->get();

        $dataJenisProduk = \DB::table('jenisprodukmt as ru')
            ->select('ru.id', 'ru.jenisproduk', 'ru.kelompokprodukidfk')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.jenisproduk')
            ->get();

        $dataKelompokProduk = \DB::table('kelompokprodukmt as ru')
            ->select('ru.id', 'ru.kelompokproduk')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
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

    public function getDataStokRuanganDetail(Request $request)
    {
        $idProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $data = \DB::table('transaksistoktr as spd')
            ->JOIN('strukpelayanantr as sp', 'sp.norec', '=', 'spd.nostrukterimafk')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'spd.produkidfk')
            ->JOIN('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->leftJOIN('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->leftJOIN('kelompokprodukmt as kp', 'kp.id', '=', 'jp.kelompokprodukidfk')
            ->JOIN('asalprodukmt as ap', 'ap.id', '=', 'spd.asalprodukidfk')
            ->JOIN('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->select(DB::raw("sp.nostruk AS noterima,spd.produkidfk AS objectprodukfk,pr.kdproduk AS kdsirs,
                                    pr.namaproduk,ap.asalproduk,CAST (spd.qtyproduk AS FLOAT),
                                    ss.satuanstandar,spd.tglkadaluarsa,spd.nobatch,CAST (spd.harganetto1 AS FLOAT),
                                    spd.norec AS norec_spd,spd.nostrukterimafk"))
            ->where('pr.aktif', true)
            ->where('spd.aktif', true)
            ->where('pr.koders', $idProfile)
            ->where('spd.koders', $idProfile);
        if (isset($request['kelompokprodukid']) && $request['kelompokprodukid'] != "" && $request['kelompokprodukid'] != "undefined") {
            $data = $data->where('jp.kelompokprodukidfk', '=', $request['kelompokprodukid']);
        }
        if (isset($request['detailJenisProdukId']) && $request['detailJenisProdukId'] != "" && $request['detailJenisProdukId'] != "undefined") {
            $data = $data->where('djp.detailjenisprodukidfk', '=', $request['detailJenisProdukId']);
        }
        if (isset($request['jeniskprodukid']) && $request['jeniskprodukid'] != "" && $request['jeniskprodukid'] != "undefined") {
            $data = $data->where('djp.jenisprodukidfk', '=', $request['jeniskprodukid']);
        }
        if (isset($request['produkfk']) && $request['produkfk'] != "" && $request['produkfk'] != "undefined") {
            $data = $data->where('spd.produkidfk', '=', $request['produkfk']);
        }
        if (isset($request['namaproduk']) && $request['namaproduk'] != "" && $request['namaproduk'] != "undefined") {
            $data = $data->where('pr.namaproduk', 'ilike', '%' . $request['namaproduk'] . '%');
        }
        if (isset($request['ruanganfk']) && $request['ruanganfk'] != "" && $request['ruanganfk'] != "undefined") {
            $data = $data->where('spd.ruanganidfk', '=', $request['ruanganfk']);
        }
        if (isset($request['asalprodukfk']) && $request['asalprodukfk'] != "" && $request['asalprodukfk'] != "undefined") {
            $data = $data->where('spd.asalprodukidfk', '=', $request['asalprodukfk']);
        }
        $data = $data->where('spd.qtyproduk', '>', 0);
        if (isset($request['jmlRows']) && $request['jmlRows'] != "" && $request['jmlRows'] != "undefined") {
            $data = $data->take($request['jmlRows']);
        }
        $data = $data->get();
        $data2 = [];

        $dataOrder = \DB::table('transaksiordertr as so')
            ->JOIN('transaksiorderdetailtr as op', 'op.transaksiorderfk', '=', 'so.norec')
            ->leftJOIN('transaksikirimtr as sk', 'sk.transaksiorderfk', '=', 'so.norec')
            ->select(DB::raw("
                so.ruanganidfk AS objectruanganfk,op.produkidfk AS objectprodukfk,
			    CAST (SUM(op.qtyproduk) AS FLOAT) AS qty
            "))
            ->where('so.koders', $idProfile);
        $dataOrder = $dataOrder->where('so.ruanganidfk', '=', $request['ruanganfk']);
        $dataOrder = $dataOrder->whereNull('sk.transaksiorderfk')
            ->groupBy(DB::raw("
                so.ruanganidfk,op.produkidfk
            "));
        $dataOrder = $dataOrder->get();

        foreach ($data as $item) {
            $data2[] = array(
                'noTerima' => $item->noterima,
                'kodeProduk' => $item->objectprodukfk,
                'kdsirs' => $item->kdsirs,
                'namaProduk' => $item->namaproduk,
                'asalProduk' => $item->asalproduk,
                'qtyProduk' => $item->qtyproduk,
                'satuanStandar' => $item->satuanstandar,
                'tglKadaluarsa' => $item->tglkadaluarsa,
                'noBatch' => $item->nobatch,
                'harga' => $item->harganetto1,
                'norec_spd' => $item->norec_spd,
                'nostrukterimafk' => $item->nostrukterimafk,
            );
        }
        $result = array(
            'detail' => $data2,
            'detailorder' => $dataOrder,
            'datalogin' => $dataLogin,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function updateBarangKadaluarsa(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        \DB::beginTransaction();

        try {
            $TransaksiStok = TransaksiStok::where('norec', $request['norec_spd'])
                ->where('koders', $idProfile)
                ->where('produkidfk', $request['produkfk'])
                ->where('aktif', true)
                ->update([
                    'tglkadaluarsa' => $request['tanggal']
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal";
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $TransaksiStok,
                "tb" => 'godU',
            );
        } else {
            $ReportTrans = "Gagal, coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'godU',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function saveAdjustmentStok(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->select('lu.objectpegawaifk')
            ->where('lu.id', $request['userData']['id'])
            ->first();
        $KelTransAddjusment = (int) $this->settingDataFixed('KelJenisTransaksiAdjusmentStok', $idProfile);
        \DB::beginTransaction();
        try {
            $newSPD = TransaksiStok::where('norec', $request['norec_spd'])
                ->where('koders', $idProfile)
                ->where('nostrukterimafk', $request['nostruterimafk'])
                ->where('ruanganidfk', $request['ruanganfk'])
                ->where('produkidfk', $request['produkfk'])
                ->first();
            $noClosing = $this->generateCode(new TransaksiClosing, 'noclosing', 10, 'ADJ/' . $this->getDateTime()->format('ym'), $idProfile);
            $dataSC = new TransaksiClosing();
            $dataSC->norec = $dataSC->generateNewId();
            $dataSC->koders = $idProfile;
            $dataSC->aktif = true;
            $dataSC->pegawaidicloseidfk = $dataPegawai->objectpegawaifk;
            $dataSC->kelompoktransaksiidfk = $KelTransAddjusment;
            $dataSC->keteranganlainnya = 'Adjusment Stok Plus' . $request['namaRuangan'];
            $dataSC->noclosing = $noClosing;
            $dataSC->ruangandicloseidfk = $request['ruanganfk'];
            $dataSC->ruanganidfk = $request['ruanganfk'];
            $dataSC->tglclosing = date('Y-m-d H:i:s');
            $dataSC->save();
            $norecSC = $dataSC->norec;

            $dataSPD = new TransaksiAdjustmentStok();
            $dataSPD->norec = $dataSPD->generateNewId();
            $dataSPD->koders = $idProfile;
            $dataSPD->aktif = true;
            $dataSPD->asalprodukidfk = $newSPD->asalprodukidfk;
            $dataSPD->hargadiscount = 0;
            $dataSPD->harganetto1 = $newSPD->harganetto1;
            $dataSPD->harganetto2 = $newSPD->harganetto2;
            $dataSPD->persendiscount = 0;
            $dataSPD->produkidfk = $request['produkfk'];
            $dataSPD->qtyprodukreal = $request['qtyreal'];
            $dataSPD->qtyproduksystem = $request['qtyad'];
            $dataSPD->qtyprodukadjusment = $request['qtyad'];
            $dataSPD->ruanganidfk = $request['ruanganfk'];
            $dataSPD->noclosingidfk = $norecSC;
            $dataSPD->nostrukterimaidfk = $newSPD->nostrukterimafk;
            $dataSPD->norec_spd = $request['norec_spd'];
            $dataSPD->save();

            $dataSpdAD = $dataSPD->norec;
            $dataSaldoAwal = DB::select(
                DB::raw("select sum(qtyproduk) as qty from transaksistoktr
                            where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                array(
                    'ruanganfk' => $request['ruanganfk'],
                    'produkfk' => $request['produkfk']
                )
            );

            $saldoAwal = 0;
            foreach ($dataSaldoAwal as $itemss) {
                $saldoAwal = (float)$itemss->qty;
            }
            if ($saldoAwal == 0) {
                $qtyAwal = (float)$saldoAwal - (float) $request['qtyad'];
                $tglnow =  date('Y-m-d H:i:s');
                $dataKS = array(
                    "saldoawal" => (float) $request['qtyreal'],
                    "qtyin" => (float) $request['qtyad'],
                    "qtyout" => 0,
                    "saldoakhir" => (float) $request['qtyreal'] + (float) $request['qtyad'],
                    "keterangan" => 'Adjusment Stok Plus' . $request['namaRuangan'],
                    "produkidfk" =>  $request['produkfk'],
                    "ruanganidfk" => $request['ruanganfk'],
                    "tglinput" => $tglnow,
                    "tglkejadian" => $tglnow,
                    "nostrukterimaidfk" => $newSPD->nostrukterimafk,
                    "norectransaksi" => $newSPD->norec,
                    "tabletransaksi" => 'transaksistoktr',
                    "flagfk" => null,
                );
                return $this->respond($dataKS);
            } else {
                $Selisih = (float) $request['qtyad'] - (float) $request['qtyreal'];
                $statusssss = 0;
                $hasilSelisih = 0;
                $saldoAwalR = 0;
                $jumlahR = 0;
                if ($Selisih < 0) {
                    $statusssss = 0;
                    $selisih = (float)$Selisih * (-1);
                } else {
                    $statusssss = 1;
                    $selisih = (float)$Selisih;
                }
                if ($statusssss == 0) {
                    $jumlahR = $selisih;
                    $saldoAwalR = (float)$saldoAwal - (float)$selisih;
                } else {
                    $jumlahR = $selisih;
                    $saldoAwalR = (float)$selisih + (float)$saldoAwal;
                }
                if ($statusssss == 1) {
                    $qty = 0;
                    if ((float) $request['qtyreal'] > (float) $request['qtyad']) {
                        $qty = (float) $request['qtyreal'] - (float) $request['qtyad'];
                    } else {
                        $qty = (float) $request['qtyad'] - (float) $request['qtyreal'];
                    }
                    $dataKS = array(
                        "saldoawal" => (float) $request['qtyreal'],
                        "qtyin" => $qty,
                        "qtyout" => 0,
                        "saldoakhir" => (float) $request['qtyad'],
                        "keterangan" => 'Adjusment Stok Plus ' . $request['namaRuangan'],
                        "produkidfk" =>  $request['produkfk'],
                        "ruanganidfk" => $request['ruanganfk'],
                        "nostrukterimaidfk" => $newSPD->nostrukterimafk,
                        "norectransaksi" => $newSPD->norec,
                        "tabletransaksi" => 'transaksistoktr',
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $dataKS);
                } else {
                    $qty = 0;
                    if ((float) $request['qtyreal'] > (float) $request['qtyad']) {
                        $qty = (float) $request['qtyreal'] - (float) $request['qtyad'];
                    } else {
                        $qty = (float) $request['qtyad'] - (float) $request['qtyreal'];
                    }
                    $dataKS = array(
                        "saldoawal" => (float) $request['qtyreal'],
                        "qtyin" => 0,
                        "qtyout" => $qty,
                        "saldoakhir" => (float) $request['qtyad'],
                        "keterangan" => 'Adjusment Stok Minus ' . $request['namaRuangan'],
                        "produkidfk" =>  $request['produkfk'],
                        "ruanganidfk" => $request['ruanganfk'],
                        "nostrukterimaidfk" => $newSPD->nostrukterimafk,
                        "norectransaksi" => $newSPD->norec,
                        "tabletransaksi" => 'transaksistoktr',
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $dataKS);
                }
            }

            TransaksiStok::where('norec', $newSPD->norec)
                ->where('koders', $idProfile)
                ->update([
                    'qtyproduk' => $request['qtyad']
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "Gagal coba lagi";
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "tb" => 'godU',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'godU',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }
    public function getStokRuanganSO(Request $request)
    {
        $dataLogin = $request->all();
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $data = \DB::table('pelayananmt as pr')
            ->leftJoin('transaksistoktr as spd', 'pr.id', '=', 'spd.produkidfk')
            ->leftJoin('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->leftJoin('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->leftJoin('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->select(DB::raw('sum(spd.qtyproduk) as qty, pr.id as prid,pr.namaproduk,ss.satuanstandar'))
            ->where('pr.koders', $idProfile)
            ->where('spd.koders', $idProfile)
            ->groupBy(
                'pr.id',
                'pr.namaproduk',
                'ss.satuanstandar'
            )

            ->orderBy('pr.namaproduk');

        if (isset($request['kelompokprodukid']) && $request['kelompokprodukid'] != "" && $request['kelompokprodukid'] != "undefined") {
            $data = $data->where('jp.kelompokprodukidfk', '=', $request['kelompokprodukid']);
        }
        if (isset($request['detailjenisprodukfk']) && $request['detailjenisprodukfk'] != "" && $request['detailjenisprodukfk'] != "undefined") {
            $arrDetJenis = explode(',', $request['detailjenisprodukfk']);
            $kodeDet = [];
            foreach ($arrDetJenis as $item) {
                $kodeDet[] = (int) $item;
            }
            $data = $data->whereIn('djp.id', $kodeDet);
        }
        if (isset($request['jeniskprodukid']) && $request['jeniskprodukid'] != "" && $request['jeniskprodukid'] != "undefined") {
            $arrJenis = explode(',', $request['jeniskprodukid']);
            $kode = [];
            foreach ($arrJenis as $item) {
                $kode[] = (int) $item;
            }
            $data = $data->whereIn('djp.jenisprodukidfk', $kode);
        }
        if (isset($request['namaproduk']) && $request['namaproduk'] != "" && $request['namaproduk'] != "undefined") {
            $data = $data->where('pr.namaproduk', 'ilike', '%' . $request['namaproduk'] . '%');
        }
        if (isset($request['ruanganfk']) && $request['ruanganfk'] != "" && $request['ruanganfk'] != "undefined") {
            $data = $data->where('spd.ruanganidfk', '=', $request['ruanganfk']);
        }
        $data = $data->where('pr.aktif', true);
        $data = $data->where('spd.aktif', true);
        $data = $data->get();
        $data2 = [];
        foreach ($data as $item) {
            $data2[] = array(
                'kodeProduk' => $item->prid,
                'namaProduk' => strtoupper($item->namaproduk),
                'qtyProduk' => $item->qty,
                'satuanStandar' => $item->satuanstandar,
            );
        }
        $result = array(
            'detail' => $data2,
            'datalogin' => $dataLogin,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }
    public function saveStockOpname(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        ini_set('max_execution_time', 300); 

        $datas = array();
        \DB::beginTransaction();
        try {
            $noClosing = $this->generateCode(new TransaksiClosing, 'noclosing', 10, 'PN/' . $this->getDateTime()->format('ym'), $idProfile);

            $dataSC = new TransaksiClosing();
            $dataSC->norec = $dataSC->generateNewId();
            $dataSC->koders = $idProfile;
            $dataSC->aktif = true;
            $dataSC->pegawaidicloseidfk = $this->getCurrentUserID();
            $dataSC->kelompoktransaksiidfk = $this->kelompokTransaksi('STOK OPNAME', $kdProfile);
            $dataSC->keteranganlainnya = 'Stock Opname ' . $request['namaRuangan'];
            $dataSC->noclosing = $noClosing;
            $dataSC->ruangandicloseidfk = $request['ruanganId'];
            $dataSC->ruanganidfk = $request['ruanganId'];
            $dataSC->tglclosing = $request['tglClosing'];
            $dataSC->save();

            $norecSC = $dataSC->norec;


            foreach ($request['stokProduk'] as $item) {

                $dataSPDK = TransaksiStok::where('produkidfk', $item['produkfk'])
                    ->where('koders', $idProfile)
                    ->where('ruanganidfk', $request['ruanganId'])
                    ->orderby('tglkadaluarsa')
                    ->first();

                if (empty($dataSPDK)) {
                    $dataSPDK2 = TransaksiStok::where('produkidfk', $item['produkfk'])
                        ->where('koders', $idProfile)
                        ->orderby('tglkadaluarsa')
                        ->first();

                    if (!empty($dataSPDK2)) {

                        $dataSPD = new TransaksiOpname();
                        $dataSPD->norec = $dataSPD->generateNewId();
                        $dataSPD->koders = $idProfile;
                        $dataSPD->aktif = true;
                        $dataSPD->asalprodukidfk = $dataSPDK2->asalprodukidfk;
                        $dataSPD->hargadiscount = 0;
                        $dataSPD->harganetto1 = $dataSPDK2->harganetto1;
                        $dataSPD->harganetto2 = $dataSPDK2->harganetto2;
                        $dataSPD->persendiscount = 0;
                        $dataSPD->produkidfk = $item['produkfk'];
                        $dataSPD->qtyprodukreal = $item['stokReal'];
                        $dataSPD->qtyproduksystem = $item['stokSistem'];
                        $dataSPD->qtyprodukinext = $item['selisih'];
                        $dataSPD->ruanganidfk = $request['ruanganId'];
                        $dataSPD->transaksiclosingfk = $norecSC;
                        $dataSPD->nostrukterimafk = $dataSPDK2->nostrukterimafk;
                        $dataSPD->save();

                        $dataNewSPD = new TransaksiStok;
                        $dataNewSPD->norec = $dataNewSPD->generateNewId();
                        $dataNewSPD->koders = $idProfile;
                        $dataNewSPD->aktif = true;
                        $dataNewSPD->asalprodukidfk = $dataSPDK2->asalprodukidfk;
                        $dataNewSPD->hargadiscount = $dataSPDK2->hargadiscount;
                        $dataNewSPD->harganetto1 = $dataSPDK2->harganetto1;
                        $dataNewSPD->harganetto2 = $dataSPDK2->harganetto2;
                        $dataNewSPD->persendiscount = 0;
                        $dataNewSPD->produkidfk = $dataSPDK2->produkidfk;
                        $dataNewSPD->qtyproduk = (float)$item['selisih'];
                        $dataNewSPD->qtyprodukonhand = 0;
                        $dataNewSPD->qtyprodukoutext = 0;
                        $dataNewSPD->qtyprodukoutint = 0;
                        $dataNewSPD->ruanganidfk = $request['ruanganId'];
                        $dataNewSPD->nostrukterimafk = $dataSPDK2->nostrukterimafk;
                        $dataNewSPD->noverifikasifk = $dataSPDK2->noverifikasifk;
                        $dataNewSPD->nobatch = $dataSPDK2->nobatch;
                        $dataNewSPD->tglkadaluarsa = $dataSPDK2->tglkadaluarsa;
                        $dataNewSPD->tglpelayanan = $dataSPDK2->tglpelayanan;
                        $dataNewSPD->tglproduksi = $dataSPDK2->tglproduksi;
                        $dataNewSPD->save();

                        $dataSTOKDETAIL[] = DB::select(
                            DB::raw("select qtyproduk as qty,norec from transaksistoktr
                            where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                            array(
                                'ruanganfk' => $request['ruanganId'],
                                'produkfk' => $item['produkfk'],
                            )
                        );
                        $asup = 0;
                        $stt = '';
                        if ((float)$item['selisih'] > 0) {
                            $asup = (float)$item['selisih'];
                            $stt = '(+)';
                        }
                        $kaluar = 0;
                        if ((float)$item['selisih'] < 0) {
                            $stt = '(-)';
                            $kaluar = (float)$item['selisih'] * (-1);
                        }
                        $dataKS = array(
                            "saldoawal" => (float)$item['stokSistem'],
                            "qtyin" => $asup,
                            "qtyout" => $kaluar,
                            "saldoakhir" => (float)$item['stokReal'],
                            "keterangan" =>  'Stock Opname ' . $stt . ' Ruangan ' . $request['namaRuangan'],
                            "produkidfk" =>  $item['produkfk'],
                            "ruanganidfk" => $request['ruanganId'],
                            "nostrukterimaidfk" => $dataNewSPD->nostrukterimafk,
                            "norectransaksi" => $dataNewSPD->norec,
                            "tabletransaksi" => 'transaksistoktr',
                            "flagfk" => null,
                        );
                        $this->KartuStok($idProfile, $dataKS);
                    } else {
                        $dataBarang = DB::select(
                            DB::raw("select * from pelayananmt where koders = $idProfile and id=:produkfk"),
                            array(
                                'produkfk' => $item['produkfk'],
                            )
                        );
                        foreach ($dataBarang as $poek) {
                            $datas[] = array(
                                "kdproduk" => $item['produkfk'],
                                "namaproduk" => $poek->namaproduk,
                                "stokSistem" => $item['stokSistem'],
                                "stokReal" => $item['stokReal'],
                                "selisih" => $item['selisih'],
                            );
                        }
                    }
                } else {
                    $dataSPD = new TransaksiOpname();
                    $dataSPD->norec = $dataSPD->generateNewId();
                    $dataSPD->koders = $idProfile;
                    $dataSPD->aktif = true;
                    $dataSPD->asalprodukidfk = $dataSPDK->asalprodukidfk;
                    $dataSPD->hargadiscount = 0;
                    $dataSPD->harganetto1 = $dataSPDK->harganetto1;
                    $dataSPD->harganetto2 = $dataSPDK->harganetto2;
                    $dataSPD->persendiscount = 0;
                    $dataSPD->produkidfk = $item['produkfk'];
                    $dataSPD->qtyprodukreal = $item['stokReal'];
                    $dataSPD->qtyproduksystem = $item['stokSistem'];
                    $dataSPD->qtyprodukinext = $item['selisih'];
                    $dataSPD->ruanganidfk = $request['ruanganId'];
                    $dataSPD->transaksiclosingfk = $norecSC;
                    $dataSPD->nostrukterimafk = $dataSPDK->nostrukterimafk;
                    $dataSPD->save();

                    $dataStokMinus = DB::select(
                        DB::raw("select sum(qtyproduk) as qty from transaksistoktr
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk and qtyproduk < 0 "),
                        array(
                            'ruanganfk' => $request['ruanganId'],
                            'produkfk' => $item['produkfk'],
                        )
                    );
                    TransaksiStok::where('ruanganidfk', $request['ruanganId'])
                        ->where('koders', $idProfile)
                        ->where('produkidfk', $item['produkfk'])
                        ->where('qtyproduk', '<', 0)
                        ->update([
                            'qtyproduk' => 0
                        ]);
                    if (count($dataStokMinus) != 0) {
                        foreach ($dataStokMinus as $items) {
                            $stokMinus = (float)$items->qty;
                        }
                    }

                    $saldoAwal = 0;
                    $jumlah = (float)$item['selisih'] + (float)$stokMinus;

                    if ($jumlah > 0) {
                        $dataStok = DB::select(
                            DB::raw("select qtyproduk as qty,norec,nostrukterimafk from transaksistoktr
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk and qtyproduk>0 limit 1"),
                            array(
                                'ruanganfk' => $request['ruanganId'],
                                'produkfk' => $item['produkfk'],
                            )
                        );
                        if (count($dataStok) == 0) {
                            $dataStok = DB::select(
                                DB::raw("select qtyproduk as qty,norec,nostrukterimafk from transaksistoktr
                                  where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk limit 1"),
                                array(
                                    'ruanganfk' => $request['ruanganId'],
                                    'produkfk' => $item['produkfk'],
                                )
                            );
                        }
                        foreach ($dataStok as $items) {
                            TransaksiStok::where('norec', $items->norec)
                                ->where('koders', $idProfile)
                                ->update(
                                    [
                                        'qtyproduk' => (float)$items->qty + (float)$jumlah
                                    ]
                                );
                        }

                        $dataSTOKDETAIL[] = DB::select(
                            DB::raw("select qtyproduk as qty,norec from transaksistoktr
                            where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk "),
                            array(
                                'ruanganfk' => $request['ruanganId'],
                                'produkfk' => $item['produkfk'],
                            )
                        );
                    } else {
                        $jumlah = $jumlah * (-1);
                        $dataStok = DB::select(
                            DB::raw("select qtyproduk as qty,norec,nostrukterimafk from transaksistoktr
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                            array(
                                'ruanganfk' => $request['ruanganId'],
                                'produkfk' => $item['produkfk'],
                            )
                        );
                        foreach ($dataStok as $items) {
                            if ((float)$items->qty < $jumlah) {
                                $jumlah = $jumlah - (float)$items->qty;
                                TransaksiStok::where('norec', $items->norec)
                                    ->where('koders', $idProfile)
                                    ->update(
                                        [
                                            'qtyproduk' => 0
                                        ]
                                    );
                            } else {
                                $saldoakhir = (float)$items->qty - $jumlah;
                                $jumlah = 0;
                                TransaksiStok::where('norec', $items->norec)
                                    ->where('koders', $idProfile)
                                    ->update(
                                        [
                                            'qtyproduk' => (float)$saldoakhir
                                        ]
                                    );
                            }
                        }

                        $dataSTOKDETAIL[] = DB::select(
                            DB::raw("select qtyproduk as qty,norec,nostrukterimafk from transaksistoktr
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                            array(
                                'ruanganfk' => $request['ruanganId'],
                                'produkfk' => $item['produkfk'],
                            )
                        );
                    }

                    $dataSaldoAwalK = DB::select(
                        DB::raw("select sum(qtyproduk) as qty from transaksistoktr
                  where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                        array(
                            'ruanganfk' => $request['ruanganId'],
                            'produkfk' => $item['produkfk'],
                        )
                    );
                    $saldoAwal = 0;
                    foreach ($dataSaldoAwalK as $items) {
                        $saldoAwal = (float)$items->qty;
                    }
                    $statusssss = 0;
                    $flagfk = 0;
                    if ($item['selisih'] < 0) {
                        $statusssss = 0;
                        $selisih = (float)$item['selisih'] * (-1);
                        $flagfk = 5;
                    } else {
                        $statusssss = 1;
                        $selisih = (float)$item['selisih'];
                        $flagfk = 4;
                    }

                    $asup = 0;
                    $stt = '';
                    if ((float)$item['selisih'] > 0) {
                        $asup = (float)$item['selisih'];
                        $stt = '(+)';
                    }
                    $kaluar = 0;
                    if ((float)$item['selisih'] < 0) {
                        $kaluar = (float)$item['selisih'] * (-1);
                        $stt = '(-)';
                    }
                    $dataKS = array(
                        "saldoawal" => (float)$item['stokSistem'],
                        "qtyin" => $asup,
                        "qtyout" => $kaluar,
                        "saldoakhir" => (float)$item['stokReal'],
                        "keterangan" =>  'Stock Opname ' . $stt . ' Ruangan ' . $request['namaRuangan'],
                        "produkidfk" =>  $item['produkfk'],
                        "ruanganidfk" => $request['ruanganId'],
                        "nostrukterimaidfk" => $dataSPDK->nostrukterimafk,
                        "norectransaksi" => $dataSPDK->norec,
                        "tabletransaksi" => 'transaksistoktr',
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $dataKS);
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
                "status" => 201,
                "databarangtaktersave" => $datas,

                "noSO" => $dataSC,
                "detailstok" => $dataSTOKDETAIL,
                "dataSPDK" => $dataSPDK,
                "by" => 'slvR',
            );
        } else {
            $ReportTrans = " Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                // "databarangtaktersave" => $datas,
                // "noSO" => $dataSC,
                // "detailstok" => $dataSTOKDETAIL,
                // "dataSPDK" => $dataSPDK,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "by" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getStokRuanganSOFromFileExcel(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $dataLogin = $request->all();
        $arr_fix = $dataLogin['data'];
        $kode = [];
        foreach ($dataLogin['data'] as $item) {
            $kode[] = (float) $item['kodeProduk'];
        }

        $data = \DB::table('pelayananmt as pr')
            ->Join('transaksistoktr as spd', 'pr.id', '=', 'spd.produkidfk')
            ->leftJoin('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->leftJoin('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->leftJoin('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->select(DB::raw('sum(spd.qtyproduk) as qty, pr.id as prid,pr.namaproduk,ss.satuanstandar'))
            ->where('pr.koders', $idProfile)
            ->groupBy(
                'pr.id',
                'pr.namaproduk',
                'ss.satuanstandar'
            )
            ->orderBy('pr.namaproduk');


        $data = $data->whereIn('pr.id', $kode);
        if (isset($request['ruanganfk']) && $request['ruanganfk'] != "" && $request['ruanganfk'] != "undefined") {
            $data = $data->where('spd.ruanganidfk', '=', $request['ruanganfk']);
        }
        $data = $data->get();

        $data3 = \DB::table('pelayananmt as pr')
            ->Join('transaksistoktr as spd', 'pr.id', '=', 'spd.produkidfk')
            ->leftJoin('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->select(DB::raw('pr.id as prid,pr.namaproduk,ss.satuanstandar'))
            ->where('pr.koders', $idProfile)
            ->orderBy('pr.namaproduk');
        $data3 = $data3->whereIn('pr.id', $kode);
        $data3 = $data3->get();

        $sama = false;
        $sami = false;
        $gaBisaSave = true;
        foreach ($arr_fix as $ketanItem) {
            $sama = false;
            foreach ($data as $item) {
                if ($ketanItem['kodeProduk'] == $item->prid) {
                    $sama = true;
                    $data2[] = array(
                        'kodeProduk' => $item->prid,
                        'namaProduk' => strtoupper($item->namaproduk),
                        'qtyProduk' => $item->qty,
                        'satuanStandar' => $item->satuanstandar,
                        'stokReal' => $ketanItem['stokReal'],
                        'selisih' => (float)$ketanItem['stokReal'] - (float)$item->qty,
                    );
                }
            }
            if ($sama == false) {
                foreach ($data3 as $berasMerah) {
                    $sami = false;
                    if ($ketanItem['kodeProduk'] == $berasMerah->prid) {
                        $sami = true;
                        $data2[] = array(
                            'kodeProduk' => $ketanItem['kodeProduk'],
                            'namaProduk' => strtoupper($berasMerah->namaproduk),
                            'qtyProduk' => 0,
                            'satuanStandar' => $item->satuanstandar,
                            'stokReal' => $ketanItem['stokReal'],
                            'selisih' => (float)$ketanItem['stokReal'],
                        );
                        break;
                    }
                }
                if ($sami == false) {
                    $gaBisaSave = false;
                    $data4 = \DB::table('pelayananmt as pr')
                        ->leftJoin('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
                        ->leftJoin('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
                        ->leftJoin('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
                        ->select(DB::raw('pr.id as prid,pr.namaproduk,ss.satuanstandar'))
                        ->where('pr.koders', $idProfile)
                        ->orderBy('pr.namaproduk');
                    $data4 = $data4->where('pr.id', $ketanItem['kodeProduk']);
                    $data4 = $data4->get();
                    if (count($data4) == 0) {
                        $data2[] = array(
                            'kodeProduk' => 'xxx' . $ketanItem['kodeProduk'] . 'xxx',
                            'namaProduk' => 'xxx !Kode Produk Tidak ada! xxx',
                            'qtyProduk' => 0,
                            'satuanStandar' => 'xxxxxx',
                            'stokReal' => (float) $ketanItem['stokReal'],
                            'selisih' => (float)$ketanItem['stokReal'],
                        );
                    } else {
                        foreach ($data4 as $berasItem) {
                            $data2[] = array(
                                'kodeProduk' => 'xxx' . $ketanItem['kodeProduk'] . 'xxx',
                                'namaProduk' => 'xxx' . strtoupper($berasItem->namaproduk) . 'xxx!belum ada penerimaan!',
                                'qtyProduk' => 0,
                                'satuanStandar' => $berasItem->satuanstandar,
                                'stokReal' => $ketanItem['stokReal'],
                                'selisih' => (float)$ketanItem['stokReal'],
                            );
                        }
                    }
                }
            }
        }


        $result = array(
            'detail' => $data2,
            'data_stok_kosong' => $data3,
            'datalogin' => $dataLogin,
            'arr_req' => $arr_fix,
            'save_cmd' => $gaBisaSave,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }
    public function getDaftarStokRuanganSO(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $detailjenisprodukfk = '';
        $jenisprodukfk = '';
        $namaproduk = '';
        $tglAwal = $request['tglAwal'];
        $tglAkhir = $request['tglAkhir'];
        $ruanganId = '';

        if (isset($request['jeniskprodukid']) &&  $request['jeniskprodukid'] != '') {
            $jenisprodukfk = "and djp.jenisprodukidfk in (" . $request['jeniskprodukid'] . ")";
        }
        if (isset($request['namaproduk']) &&  $request['namaproduk'] != '') {
            $namaproduk = "and pr.namaproduk  ILIKE '%" . $request['namaproduk'] . "%'";
        }
        if (isset($request['detailjenisprodukfk']) &&  $request['detailjenisprodukfk'] != '') {
            $detailjenisprodukfk = "and djp.id in (" . $request['detailjenisprodukfk'] . ")";
        }
        if (isset($request['ruanganfk']) &&  $request['ruanganfk'] != '') {
            $ruanganId = "and ru.id =" . $request['ruanganfk'];
        }

        $data = DB::select(DB::raw("
				SELECT
					x.kdproduk,
					x.tglclosing,
					x.namaproduk,
					x.satuanstandar,
					x.namaruangan,
					x.tglkadaluarsa,
					SUM (x.qtyprodukreal) AS qtyprodukreal,
					SUM (x.harganetto1) AS harganetto1,
					SUM (x.total) AS total
				FROM
					(
						SELECT DISTINCT
							pr.id AS kdproduk,
							sp.tglstruk,
							sc.tglclosing,
							pr.namaproduk,
							ss.satuanstandar,
							spd.qtyprodukreal,
							spd.harganetto1,
							spd.qtyprodukreal * spd.harganetto1 AS total,
							ru.namaruangan,
							spdt.tglkadaluarsa
						FROM
							transaksiclosingtr sc
						LEFT JOIN transaksiopnametr spd ON spd.transaksiclosingfk = sc.norec
						LEFT JOIN strukpelayanantr sp ON sp.norec = spd.nostrukterimafk
						LEFT JOIN strukpelayanandetailtr spdt ON spdt.noclosingidfk = sc.norec
						LEFT JOIN pelayananmt pr ON pr.id = spd.produkidfk
						LEFT JOIN detailjenisprodukmt djp ON djp.id = pr.detailjenisprodukidfk
						LEFT JOIN satuanstandarmt ss ON ss.id = pr.satuanstandaridfk
						LEFT JOIN ruanganmt ru ON ru.id = spd.ruanganidfk
						WHERE sc.koders =$idProfile and
							sc.tglclosing BETWEEN '$tglAwal'
						AND '$tglAkhir'
						$ruanganId
					$namaproduk
					$detailjenisprodukfk
					$jenisprodukfk


					) AS x
				GROUP BY
					x.kdproduk,
					x.tglclosing,
					x.namaproduk,
					x.satuanstandar,
					x.namaruangan,
					x.tglkadaluarsa
			"));
        $samateu = false;
        $arrayFix = [];
        foreach ($data as $item) {
            $samateu = false;
            foreach ($arrayFix as $itemsss) {
                if ($item->kdproduk == $itemsss['kdproduk']) {
                    $samateu = true;
                    if ($item->tglclosing > date($itemsss['tglclosing'])) {
                        $itemsss['qtyprodukreal'] = (float) $item->qtyprodukreal;
                        $itemsss['tglclosing'] = $item->tglclosing;
                        break;
                    }
                }
            }
            if ($samateu == false) {
                $arrayFix[] = array(
                    'kdproduk' => $item->kdproduk,
                    'tglclosing' => $item->tglclosing,
                    'namaproduk' => $item->namaproduk,
                    'satuanstandar' => $item->satuanstandar,
                    'namaruangan' => $item->namaruangan,
                    'tglkadaluarsa' => $item->tglkadaluarsa,
                    'qtyprodukreal' => (float)$item->qtyprodukreal,
                    'harga' => (float)$item->harganetto1,
                    'total' => (float) $item->total,

                );
            }
        }
        $result = array(
            'data' => $arrayFix,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getDaftarOrderBarang(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiOrderBarang = (int) $this->settingDataFixed('KdJenisTransaksiOrderBarang', $idProfile);
        $data = \DB::table('transaksiordertr as sp')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaiorderidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->LEFTJOIN('ruanganmt as ru2', 'ru2.id', '=', 'sp.ruangantujuanidfk')
            ->LEFTJOIN('jeniskirimmt as jk', 'jk.id', '=', 'sp.jenispermintaanidfk')
            ->LEFTJOIN('statusordermt as so', 'so.id', '=', 'sp.statusorder')
            ->select(DB::raw("
                sp.norec,sp.tglorder,sp.noorder,sp.jenispermintaanidfk,jk.jeniskirim,
                 pg.namalengkap,ru.namaruangan AS ruanganasal,
                 ru2.namaruangan AS ruangantujuan,sp.keteranganorder,
                 sp.statusorder as statusorderid,so.statusorder,sp.qtyjenisproduk
            "))
            ->where('sp.koders', $idProfile)
            ->where('sp.aktif', true)
            ->where('sp.kelompoktransaksiidfk', $kdJenisTransaksiOrderBarang);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('sp.tglorder', '>=', $request['tglAwal']);
        }

        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $data = $data->where('sp.tglorder', '<=', $request['tglAkhir']);
        }

        if (isset($request['ruanganasalfk']) && $request['ruanganasalfk'] != "" && $request['ruanganasalfk'] != "undefined") {
            $data = $data->where('sp.ruanganidfk', '=', $request['ruanganasalfk']);
        } else {
            if (isset($request['ruanganArr']) && $request['ruanganArr'] != "" && $request['ruanganArr'] != "undefined") {
                $arrRuang = explode(',', $request['ruanganArr']);
                $kodeRuang = [];
                foreach ($arrRuang as $item) {
                    $kodeRuang[] = (int) $item;
                }
                $data = $data->whereIn('sp.ruanganidfk', $kodeRuang);
            }
        }

        if (isset($request['noorder']) && $request['noorder'] != "" && $request['noorder'] != "undefined") {
            $data = $data->where('sp.noorder', 'ILIKE', '%' . $request['noorder']);
        }

        if (isset($request['ruangantujuanfk']) && $request['ruangantujuanfk'] != "" && $request['ruangantujuanfk'] != "undefined") {
            $data = $data->where('sp.ruangantujuanidfk', '=', $request['ruangantujuanfk']);
        }

        if (isset($request['norecOrder']) && $request['norecOrder'] != "" && $request['norecOrder'] != "undefined") {
            $data = $data->where('sp.norec', '=', $request['norecOrder']);
        }

        $data = $data->orderBy('sp.noorder');
        $data = $data->get();

        $results = array();
        foreach ($data as $item) {
            $details = DB::select(
                DB::raw("
                    select  pr.id as kdproduk,pr.namaproduk,
                    ss.satuanstandar,spd.qtyproduk,spd.hargasatuan
                    from transaksiorderdetailtr as spd
                    left JOIN pelayananmt as pr on pr.id=spd.produkidfk
                    left JOIN satuanstandarmt as ss on ss.id=spd.satuanstandaridfk
                    where spd.koders = $idProfile and transaksiorderfk=:norec"),
                array(
                    'norec' => $item->norec,
                )
            );

            $results[] = array(
                'status' => 'Kirim Order Barang',
                'tglorder' => $item->tglorder,
                'noorder' => $item->noorder,
                'jeniskirim' => $item->jeniskirim,
                'norec' => $item->norec,
                'namaruanganasal' => $item->ruanganasal,
                'namaruangantujuan' => $item->ruangantujuan,
                'petugas' => $item->namalengkap,
                'keterangan' => $item->keteranganorder,
                'statusorder' => $item->statusorder,
                'jmlitem' => $item->qtyjenisproduk,
                'details' => $details,
            );
        }

        $data2 = \DB::table('transaksiordertr as sp')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaiorderidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->LEFTJOIN('ruanganmt as ru2', 'ru2.id', '=', 'sp.ruangantujuanidfk')
            ->LEFTJOIN('jeniskirimmt as jk', 'jk.id', '=', 'sp.jenispermintaanidfk')
            ->LEFTJOIN('statusordermt as so', 'so.id', '=', 'sp.statusorder')
            ->select(DB::raw("
                sp.norec,sp.tglorder,sp.noorder,sp.jenispermintaanidfk,jk.jeniskirim,
                 pg.namalengkap,ru.namaruangan AS ruanganasal,
                 ru2.namaruangan AS ruangantujuan,sp.keteranganorder,
                 sp.statusorder as statusorderid,so.statusorder,sp.qtyjenisproduk
            "))
            ->where('sp.koders', $idProfile)
            ->where('sp.aktif', true)
            ->where('sp.kelompoktransaksiidfk', $kdJenisTransaksiOrderBarang);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data2 = $data2->where('sp.tglorder', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $data2 = $data2->where('sp.tglorder', '<=', $request['tglAkhir']);
        }
        if (isset($request['noorder']) && $request['noorder'] != "" && $request['noorder'] != "undefined") {
            $data2 = $data2->where('sp.noorder', 'ILIKE', '%' . $request['noorder']);
        }
        if (isset($request['ruanganasalfk']) && $request['ruanganasalfk'] != "" && $request['ruanganasalfk'] != "undefined") {
            $data2 = $data2->where('sp.ruanganidfk', '=', $request['ruanganasalfk']);
        } else {
            if (isset($request['ruanganArr']) && $request['ruanganArr'] != "" && $request['ruanganArr'] != "undefined") {
                $arrRuang = explode(',', $request['ruanganArr']);
                $kodeRuang = [];
                foreach ($arrRuang as $item) {
                    $kodeRuang[] = (int) $item;
                }
                $data2 = $data2->whereIn('sp.ruanganidfk', $kodeRuang);
            }
        }
        if (isset($request['ruangantujuanfk']) && $request['ruangantujuanfk'] != "" && $request['ruangantujuanfk'] != "undefined") {
            $data2 = $data2->where('sp.ruangantujuanidfk', '=', $request['ruangantujuanfk']);
        }

        $data2 = $data2->orderBy('sp.noorder');
        $data2 = $data2->get();

        foreach ($data2 as $item) {
            $details = DB::select(
                DB::raw("
                select  pr.id as kdproduk,pr.namaproduk,
                ss.satuanstandar,spd.qtyproduk,spd.hargasatuan
                from transaksiorderdetailtr as spd
                left JOIN pelayananmt as pr on pr.id=spd.produkidfk
                left JOIN satuanstandarmt as ss on ss.id=spd.satuanstandaridfk
                where spd.koders = $idProfile and transaksiorderfk=:norec"),
                array(
                    'norec' => $item->norec,
                )
            );

            $results[] = array(
                'status' => 'Terima Order Barang',
                'tglorder' => $item->tglorder,
                'noorder' => $item->noorder,
                'jeniskirim' => $item->jeniskirim,
                'norec' => $item->norec,
                'namaruanganasal' => $item->ruanganasal,
                'namaruangantujuan' => $item->ruangantujuan,
                'petugas' => $item->namalengkap,
                'keterangan' => $item->keteranganorder,
                'statusorder' => $item->statusorder,
                'jmlitem' => $item->qtyjenisproduk,
                'details' => $details,
            );
        }

        $result = array(
            'data' => $results,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function getDaftarDistribusiBarang(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiOrderBarang = (int) $this->settingDataFixed('KdJenisTransaksiKirimBarang', $idProfile);
        $data = \DB::table('transaksikirimtr as sp')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaipengirimidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganasalidfk')
            ->LEFTJOIN('ruanganmt as ru2', 'ru2.id', '=', 'sp.ruangantujuanidfk')
            ->LEFTJOIN('jeniskirimmt as jk', 'jk.id', '=', 'sp.jenispermintaanidfk')
            ->select(DB::raw("
                sp.norec,sp.tglkirim,sp.nokirim,sp.jenispermintaanidfk,jk.jeniskirim,pg.namalengkap,
			    sp.transaksiorderfk,ru.id AS ruasalid,ru.namaruangan AS ruanganasal,sp.qtyproduk,
			    ru2.id AS rutujuanid,ru2.namaruangan AS ruangantujuan,sp.keteranganlainnyakirim
            "))
            ->where('sp.koders', $idProfile)
            ->where('sp.aktif', true)
            ->where('sp.kelompoktransaksiidfk', $kdJenisTransaksiOrderBarang);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('sp.tglkirim', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $data = $data->where('sp.tglkirim', '<=', $request['tglAkhir']);
        }
        if (isset($request['nokirim']) && $request['nokirim'] != "" && $request['nokirim'] != "undefined") {
            $data = $data->where('sp.nokirim', 'ILIKE', '%' . $request['nokirim'] . '%');
        }
        if (isset($request['ruangantujuanfk']) && $request['ruangantujuanfk'] != "" && $request['ruangantujuanfk'] != "undefined") {
            $data = $data->where('sp.ruangantujuanidfk', '=', $request['ruangantujuanfk']);
        }
        if (isset($request['ruanganasalfk']) && $request['ruanganasalfk'] != "" && $request['ruanganasalfk'] != "undefined") {
            $data = $data->where('sp.ruanganasalidfk', '=', $request['ruanganasalfk']);
        } else {
            if (isset($request['ruanganArr']) && $request['ruanganArr'] != "" && $request['ruanganArr'] != "undefined") {
                $arrRuang = explode(',', $request['ruanganArr']);
                $kodeRuang = [];
                foreach ($arrRuang as $item) {
                    $kodeRuang[] = (int) $item;
                }
                $data = $data->whereIn('sp.ruanganasalidfk', $kodeRuang);
            }
        }

        $data = $data->orderBy('sp.nokirim');
        $data = $data->get();

        $results = array();
        foreach ($data as $item) {
            $details = DB::select(
                DB::raw("
                    select pr.id as kdproduk,pr.namaproduk,spd.satuanstandaridfk,ss.satuanstandar,
                           spd.qtyproduk,spd.qtyprodukretur,spd.qtyorder,spd.hargasatuan
                    from transaksikirimdetailtr as spd
                    left JOIN pelayananmt as pr on pr.id=spd.produkidfk
                    left JOIN satuanstandarmt as ss on ss.id=spd.satuanstandaridfk
                    where spd.koders = $idProfile and transaksikirimfk=:norec and spd.qtyproduk <> 0"),
                array(
                    'norec' => $item->norec,
                )
            );

            $results[] = array(
                'status' => 'Kirim Barang',
                'tglstruk' => $item->tglkirim,
                'nostruk' => $item->nokirim,
                'noorderfk' => $item->transaksiorderfk,
                'jenispermintaanfk' => $item->jenispermintaanidfk,
                'jeniskirim' => $item->jeniskirim,
                'norec' => $item->norec,
                'ruasalid' => $item->ruasalid,
                'namaruanganasal' => $item->ruanganasal,
                'rutujuanid' => $item->rutujuanid,
                'namaruangantujuan' => $item->ruangantujuan,
                'petugas' => $item->namalengkap,
                'keterangan' => $item->keteranganlainnyakirim,
                'jmlitem' => $item->qtyproduk,
                'details' => $details,
            );
        }

        $data2 = \DB::table('transaksikirimtr as sp')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaipengirimidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganasalidfk')
            ->LEFTJOIN('ruanganmt as ru2', 'ru2.id', '=', 'sp.ruangantujuanidfk')
            ->LEFTJOIN('jeniskirimmt as jk', 'jk.id', '=', 'sp.jenispermintaanidfk')
            ->select(DB::raw("
                sp.norec,sp.tglkirim,sp.nokirim,sp.jenispermintaanidfk,jk.jeniskirim,pg.namalengkap,
			    sp.transaksiorderfk,ru.id AS ruasalid,ru.namaruangan AS ruanganasal,sp.qtyproduk,
			    ru2.id AS rutujuanid,ru2.namaruangan AS ruangantujuan,sp.keteranganlainnyakirim
            "))
            ->where('sp.koders', $idProfile)
            ->where('sp.aktif', true)
            ->where('sp.kelompoktransaksiidfk', $kdJenisTransaksiOrderBarang);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data2 = $data2->where('sp.tglkirim', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data2 = $data2->where('sp.tglkirim', '<=', $tgl);
        }
        if (isset($request['nokirim']) && $request['nokirim'] != "" && $request['nokirim'] != "undefined") {
            $data2 = $data2->where('sp.nokirim', 'ILIKE', '%' . $request['nokirim']);
        }
        if (isset($request['ruangantujuanfk']) && $request['ruangantujuanfk'] != "" && $request['ruangantujuanfk'] != "undefined") {
            $data2 = $data2->where('sp.ruangantujuanidfk', '=', $request['ruangantujuanfk']);
        }
        if (isset($request['ruanganasalfk']) && $request['ruanganasalfk'] != "" && $request['ruanganasalfk'] != "undefined") {
            $data2 = $data2->where('sp.ruanganasalidfk', '=', $request['ruanganasalfk']);
        } else {
            if (isset($request['ruanganArr']) && $request['ruanganArr'] != "" && $request['ruanganArr'] != "undefined") {
                $arrRuang = explode(',', $request['ruanganArr']);
                $kodeRuang = [];
                foreach ($arrRuang as $item) {
                    $kodeRuang[] = (int) $item;
                }
                $data2 = $data2->whereIn('sp.ruangantujuanidfk', $kodeRuang);
            }
        }

        $data2 = $data2->orderBy('sp.nokirim');
        $data2 = $data2->get();

        foreach ($data2 as $item) {
            $details = DB::select(
                DB::raw("
                    select pr.id,pr.namaproduk,spd.satuanstandaridfk,ss.satuanstandar,
                           spd.qtyproduk,spd.qtyprodukretur
                    from transaksikirimdetailtr as spd
                    left JOIN pelayananmt as pr on pr.id=spd.produkidfk
                    left JOIN satuanstandarmt as ss on ss.id=spd.satuanstandaridfk
                    where spd.koders = $idProfile and transaksikirimfk=:norec and spd.qtyproduk <> 0"),
                array(
                    'norec' => $item->norec,
                )
            );

            $results[] = array(
                'status' => 'Terima Barang',
                'tglstruk' => $item->tglkirim,
                'nostruk' => $item->nokirim,
                'jeniskirim' => $item->jeniskirim,
                'norec' => $item->norec,
                'jenispermintaanfk' => $item->jenispermintaanidfk,
                'ruasalid' => $item->ruasalid,
                'namaruanganasal' => $item->ruanganasal,
                'rutujuanid' => $item->rutujuanid,
                'namaruangantujuan' => $item->ruangantujuan,
                'petugas' => $item->namalengkap,
                'keterangan' => $item->keteranganlainnyakirim,
                'jmlitem' => $item->qtyproduk,
                'details' => $details,
            );
        }

        $result = array(
            'data' => $results,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function getDataComboDistribusi(Request $request)
    {
        $idProfile = (int)$this->getDataKdProfile($request);
        $dataAsalProduk = \DB::table('asalprodukmt as ap')
            ->JOIN('transaksistoktr as spd', 'spd.asalprodukidfk', '=', 'ap.id')
            ->select('ap.id', 'ap.asalproduk')
            ->where('ap.koders', $idProfile)
            ->where('ap.aktif', true)
            ->orderBy('ap.id')
            ->groupBy('ap.id', 'ap.asalproduk')
            ->get();

        $result = array(
            'asalproduk' => $dataAsalProduk,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getProdukDistribusi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $set = explode(',', $this->settingDataFixed('kdJenisProdukDistribusi', $kdProfile));
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
            ->where('pr.koders', $kdProfile)
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
            ->where('ks.koders', $kdProfile)
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

    public function getDetailOrderBarang(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiOrderBarang = (int) $this->settingDataFixed('KdJenisTransaksiOrderBarang', $idProfile);
        $dataLogin = $request->all();
        $dataAsalProduk = \DB::table('asalprodukmt as ap')
            ->select('ap.id', 'ap.asalproduk')
            ->get();

        $dataStruk = \DB::table('transaksiordertr as sp')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaiorderidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->LEFTJOIN('ruanganmt as ru2', 'ru2.id', '=', 'sp.ruangantujuanidfk')
            ->LEFTJOIN('jeniskirimmt as jk', 'jk.id', '=', 'sp.jenispermintaanidfk')
            ->LEFTJOIN('statusordermt as so', 'so.id', '=', 'sp.statusorder')
            ->select(DB::raw("
                sp.norec,sp.tglorder,sp.noorder,sp.jenispermintaanidfk AS jeniskirimfk,jk.jeniskirim,
			    pg.namalengkap,ru.namaruangan AS namaruanganasal,ru2.namaruangan AS namaruangantujuan,
			    sp.keteranganorder,sp.ruangantujuanidfk as objectruangantujuanfk,sp.ruanganidfk AS objectruanganasalfk,
			    sp.statusorder AS statusorderid,so.statusorder
            "))
            ->where('sp.koders', $idProfile)
            ->where('sp.aktif', true)
            ->where('sp.kelompoktransaksiidfk', $kdJenisTransaksiOrderBarang);

        if (isset($request['norecOrder']) && $request['norecOrder'] != "" && $request['norecOrder'] != "undefined") {
            $dataStruk = $dataStruk->where('sp.norec', '=', $request['norecOrder']);
        }

        $dataStruk = $dataStruk->orderBy('sp.noorder');
        $dataStruk = $dataStruk->first();

        $data = \DB::table('transaksiordertr as so')
            ->JOIN('transaksiorderdetailtr as op', 'op.transaksiorderfk', '=', 'so.norec')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
            ->leftjoin('ruanganmt as ru2', 'ru2.id', '=', 'so.ruangantujuanidfk')
            ->join('pelayananmt as pr', 'pr.id', '=', 'op.produkidfk')
            ->join('satuanstandarmt as ss', 'ss.id', '=', 'op.satuanstandaridfk')
            ->select(DB::raw("
                so.noorder,op.qtyproduk,op.produkidfk AS produkfk,pr.id AS kdproduk,
			    pr.namaproduk,op.hasilkonversi AS nilaikonversi,op.satuanstandaridfk,
			    ss.satuanstandar,op.satuanviewidfk AS satuanviewfk,ss.satuanstandar AS satuanview,
			    op.qtyproduk AS jumlah,ru.namaruangan AS ruanganasal,ru2.namaruangan AS ruangantujuan,
			    so.ruanganidfk,so.ruangantujuanidfk,op.hargasatuan
            "))
            ->where('so.koders', $idProfile)
            ->where('so.aktif', true)
            ->where('so.kelompoktransaksiidfk', $kdJenisTransaksiOrderBarang);

        if (isset($request['norecOrder']) && $request['norecOrder'] != "" && $request['norecOrder'] != "undefined") {
            $data = $data->where('so.norec', '=', $request['norecOrder']);
        }

        $data = $data->get();
        $details = [];
        $i = 0;
        $dataStok = DB::select(
            DB::raw("
                    select sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,ap.asalproduk,
                    spd.harganetto2 as hargajual,spd.harganetto2 as harganetto,spd.hargadiscount,
                    sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk
                    from transaksistoktr as spd
                    inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                    inner JOIN asalprodukmt as ap on ap.id=spd.asalprodukidfk
                    where spd.koders = $idProfile and spd.ruanganidfk =:ruanganid
                    group by sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,
				             spd.harganetto2,spd.hargadiscount,ap.asalproduk,spd.ruanganidfk
                    order By sk.tglstruk"),
            array(
                'ruanganid' => $dataStruk->objectruangantujuanfk
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
            if ($item->jumlah > 0) {
                $i = $i + 1;
                foreach ($dataStok as $item2) {
                    if ($item2->produkidfk == $item->produkfk) {
                        if ($item2->qtyproduk >= $item->jumlah * $item->nilaikonversi) {
                            $nostrukterimafk = $item2->norec;
                            $asalprodukfk = $item2->asalprodukidfk;
                            $jmlstok = $item2->qtyproduk;
                            break;
                        }
                    }
                }
                foreach ($dataAsalProduk as $item3) {
                    if ($asalprodukfk == $item3->id) {
                        $asalproduk = $item3->asalproduk;
                    }
                }
                $details[] = array(
                    'no' => $i,
                    'noregistrasifk' => '',
                    'tglregistrasi' => '',
                    'generik' => null,
                    'hargajual' => $item->hargasatuan,
                    'jenisobatfk' => '',
                    'kelasfk' => '',
                    'stock' => $jmlstok,
                    'harganetto' => $item->hargasatuan,
                    'nostrukterimafk' => $nostrukterimafk,
                    'ruanganfk' => $item->ruanganidfk,
                    'rke' => 0,
                    'jeniskemasanfk' => 0,
                    'jeniskemasan' => '',
                    'aturanpakaifk' => 0,
                    'aturanpakai' => '',
                    'routefk' => 0,
                    'route' => '',
                    'asalprodukfk' => $asalprodukfk,
                    'asalproduk' => $asalproduk,
                    'produkfk' => $item->produkfk,
                    'kdproduk' => $item->kdproduk,
                    'namaproduk' => $item->namaproduk,
                    'nilaikonversi' => $item->nilaikonversi,
                    'satuanstandarfk' => $item->satuanviewfk,
                    'satuanstandar' => $item->satuanview,
                    'satuanviewfk' => $item->satuanviewfk,
                    'satuanview' => $item->satuanview,
                    'jmlstok' => $jmlstok,
                    'jumlah' => $item->jumlah,
                    'total' => (float)$item->jumlah * (float)$item->hargasatuan,
                    'qtyorder' => $item->jumlah,
                );
            }
        }

        $result = array(
            'head' => $dataStruk,
            'detail' => $details,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function saveOrderBarang(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiOrderBarang = (int) $this->settingDataFixed('KdJenisTransaksiOrderBarang', $idProfile);
        \DB::beginTransaction();
        try {
            if ($request['strukorder']['norecorder'] == '-') {
                if ($request['strukorder']['jenispermintaanfk'] != 1) {
                    $noOrder = $this->generateCode(new TransaksiOrder, 'noorder', 14, 'TRO-' . $this->getDateTime()->format('ym'), $idProfile);
                } else {
                    $noOrder = $this->generateCode(new TransaksiOrder, 'noorder', 14, 'AMO-' . $this->getDateTime()->format('ym'), $idProfile);
                }
                $dataSO = new TransaksiOrder();
                $dataSO->norec = $dataSO->generateNewId();
                $dataSO->koders = $idProfile;
                $dataSO->aktif = true;
                $dataSO->isdelivered = 0;
                $dataSO->noorder = $noOrder;
            } else {
                $dataSO = TransaksiOrder::where('norec', $request['strukorder']['norecorder'])->first();
                TransaksiOrderDetail::where('transaksiorderfk', $request['strukorder']['norecorder'])->delete();
            }
            $dataSO->jenispermintaanidfk = $request['strukorder']['jenispermintaanfk'];
            $dataSO->kelompoktransaksiidfk = $kdJenisTransaksiOrderBarang;
            $dataSO->keteranganorder = $request['strukorder']['keteranganorder'];
            $dataSO->pegawaiorderidfk = $request['strukorder']['pegawaiorderfk'];
            $dataSO->qtyjenisproduk = $request['strukorder']['qtyjenisproduk'];
            $dataSO->qtyproduk = $request['strukorder']['qtyjenisproduk'];
            $dataSO->ruanganidfk = $request['strukorder']['ruanganfk'];
            $dataSO->ruangantujuanidfk = $request['strukorder']['ruangantujuanfk'];
            $dataSO->tglorder = $request['strukorder']['tglorder'];
            $dataSO->statusorder = 0;
            $dataSO->totalbeamaterai = 0;
            $dataSO->totalbiayakirim = 0;
            $dataSO->totalbiayatambahan = 0;
            $dataSO->totaldiscount = 0;
            $dataSO->totalhargasatuan = 0;
            $dataSO->totalharusdibayar = 0;
            $dataSO->totalpph = 0;
            $dataSO->totalppn = 0;
            $dataSO->save();
            $norecSO = $dataSO->norec;

            foreach ($request['details'] as $item) {
                $dataOP = new TransaksiOrderDetail();
                $dataOP->norec = $dataOP->generateNewId();
                $dataOP->koders = $idProfile;
                $dataOP->aktif = true;
                $dataOP->hasilkonversi = $item['nilaikonversi'];
                $dataOP->iscito = 0;
                $dataOP->produkidfk = $item['produkfk'];
                $dataOP->qtyproduk = $item['jumlah'];
                $dataOP->hargasatuan = $item['hargasatuan'];
                $dataOP->qtyprodukretur = 0;
                $dataOP->ruanganidfk = $request['strukorder']['ruanganfk'];
                $dataOP->ruangantujuanidfk =  $request['strukorder']['ruangantujuanfk'];
                $dataOP->satuanstandaridfk = $item['satuanviewfk'];
                $dataOP->transaksiorderfk = $norecSO;
                $dataOP->tglpelayanan = $request['strukorder']['tglorder'];
                $dataOP->save();
            }

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $ReportTrans = "simpan Order";
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $dataSO,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function saveBatalOrderBarang(Request $request)
    {
        \DB::beginTransaction();
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        try {
            $dataAing = TransaksiOrder::where('norec', $request['norecorder'])
                ->where('koders', $idProfile)
                ->update(
                    [
                        'aktif' => false
                    ]
                );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }


        $ReportTrans = "Selesai";

        if ($transStatus == 'true') {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDetailKirimBarang(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiKirimBarang = (int) $this->settingDataFixed('KdJenisTransaksiKirimBarang', $idProfile);
        $dataAsalProduk = \DB::table('asalprodukmt as ap')
            ->select('ap.id', 'ap.asalproduk')
            ->get();
        $dataStruk = \DB::table('transaksikirimtr as sr')
            ->JOIN('pegawaimt as pg', 'pg.id', '=', 'sr.pegawaipengirimidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'sr.ruanganidfk')
            ->JOIN('ruanganmt as ru2', 'ru2.id', '=', 'sr.ruangantujuanidfk')
            ->LEFTJOIN('jeniskirimmt as jk', 'jk.id', '=', 'sr.jenispermintaanidfk')
            ->select(DB::raw("
                sr.nokirim,pg.id AS pgid,pg.namalengkap,sr.ruanganidfk,ru.namaruangan AS namaruanganasal,
			    sr.ruangantujuanidfk,ru2.namaruangan AS namaruangantujuan,sr.jenispermintaanidfk,
			    sr.tglkirim,sr.keteranganlainnyakirim AS keterangan,sr.jenispermintaanidfk AS jeniskirimfk,
			    jk.jeniskirim
            "))
            ->where('sr.koders', $idProfile);

        if (isset($request['norec']) && $request['norec'] != "" && $request['norec'] != "undefined") {
            $dataStruk = $dataStruk->where('sr.norec', '=', $request['norec']);
        }
        $dataStruk = $dataStruk->first();

        $data = \DB::table('transaksikirimtr as sp')
            ->JOIN('transaksikirimdetailtr as spd', 'spd.transaksikirimfk', '=', 'sp.norec')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'spd.produkidfk')
            ->JOIN('satuanstandarmt as ss', 'ss.id', '=', 'spd.satuanstandaridfk')
            ->select(DB::raw("
                sp.nokirim,spd.hargasatuan,spd.qtyprodukoutext,sp.ruanganidfk,ru.namaruangan,
			    spd.nostrukterimaidfk,spd.produkidfk AS produkfk,pr.id AS kdproduk,pr.namaproduk,
			    spd.hasilkonversi AS nilaikonversi,spd.satuanstandaridfk,ss.satuanstandar,
			    spd.satuanstandaridfk AS satuanviewfk,ss.satuanstandar AS ssview,
			    spd.qtyproduk AS jumlah,spd.hargadiscount,spd.hargatambahan AS jasa,
			    spd.hargasatuan AS hargajual,spd.harganetto,spd.qtyprodukretur,spd.qtyorder
            "))
            ->where('sp.koders', $idProfile);

        if (isset($request['norec']) && $request['norec'] != "" && $request['norec'] != "undefined") {
            $data = $data->where('sp.norec', '=', $request['norec']);
        }
        $data = $data->get();

        $pelayananPasien = [];
        $i = 0;
        $dataStok = \DB::select(
            DB::raw("
                    SELECT sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,
                           spd.harganetto2 as hargajual,spd.harganetto2 as harganetto,spd.hargadiscount,
                           sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk
                    FROM transaksistoktr as spd
                    INNER JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                    WHERE spd.koders = $idProfile and spd.ruanganidfk =:ruanganid
                    GROUP BY sk.norec,spd.produkidfk,sk.tglstruk,spd.asalprodukidfk,
				             spd.harganetto2,spd.hargadiscount,spd.ruanganidfk
                    ORDER BY sk.tglstruk
                "),
            array(
                'ruanganid' => $dataStruk->ruanganidfk
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
            if ($item->jumlah > 0) {
                $i = $i + 1;

                foreach ($dataStok as $item2) {
                    if ($item2->produkidfk == $item->produkfk) {
                        if ($item2->qtyproduk > $item->jumlah * $item->nilaikonversi) {
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
                foreach ($dataAsalProduk as $item3) {
                    if ($asalprodukfk == $item3->id) {
                        $asalproduk = $item3->asalproduk;
                    }
                }
                $pelayananPasien[] = array(
                    'no' => $i,
                    'noregistrasifk' => '',
                    'tglregistrasi' => '',
                    'generik' => null,
                    'hargajual' => $item->hargasatuan,
                    'jenisobatfk' => '',
                    'kelasfk' => '',
                    'stock' => $jmlstok,
                    'harganetto' => $item->hargasatuan,
                    'nostrukterimafk' => $nostrukterimafk,
                    'ruanganfk' => $item->ruanganidfk,
                    'rke' => 0,
                    'jeniskemasanfk' => 0,
                    'jeniskemasan' => '',
                    'aturanpakaifk' => 0,
                    'aturanpakai' => '',
                    'routefk' => 0,
                    'route' => '',
                    'asalprodukfk' => $asalprodukfk,
                    'asalproduk' => $asalproduk,
                    'produkfk' => $item->produkfk,
                    'kdproduk' => $item->kdproduk,
                    'namaproduk' => $item->namaproduk,
                    'nilaikonversi' => $item->nilaikonversi,
                    'satuanstandarfk' => $item->satuanviewfk,
                    'satuanstandar' => $item->ssview,
                    'satuanviewfk' => $item->satuanviewfk,
                    'satuanview' => $item->ssview,
                    'jmlstok' => $jmlstok,
                    'jumlah' => $item->jumlah,
                    'total' => (float)$item->jumlah * (float)$item->hargasatuan,
                    'qtyorder' => $item->qtyorder,
                );
            }
        }

        $result = array(
            'head' => $dataStruk,
            'detail' => $pelayananPasien,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function saveKirimBarangRuangan(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdJenisTransaksiKirimBarang = (int) $this->settingDataFixed('KdJenisTransaksiKirimBarang', $idProfile);
        if ($request['strukkirim']['jenispermintaanfk'] == 2) {
            $noKirim = $this->generateCodeBySeqTable(new TransaksiKirim, 'nokirim', 14, 'TRF-' . $this->getDateTime()->format('ym'), $idProfile);
        } else {
            $noKirim = $this->generateCodeBySeqTable(new TransaksiKirim, 'nokirim', 14, 'AMP-' . $this->getDateTime()->format('ym'), $idProfile);
        }
        if ($noKirim == '') {
            $ReportTrans = "Gagal mengumpukan data, Coba lagi.!";
            \DB::rollBack();
            $result = array(
                "status" => 400,
                "NOKIRIM" => $noKirim,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
            return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
        }
        DB::beginTransaction();
        $ruanganAsal = DB::select(
            DB::raw("
                    select  ru.namaruangan
                    from ruanganmt as ru 
                    where ru.koders = $idProfile and ru.id=:id"),
            array(
                'id' => $request['strukkirim']['objectruanganfk'],
            )
        );
        $strRuanganAsal = '';
        $strRuanganAsal = $ruanganAsal[0]->namaruangan;
        $ruanganTujuan = DB::select(
            DB::raw("
                    select ru.namaruangan from ruanganmt as ru 
                    where ru.koders = $idProfile and ru.id=:id"),
            array(
                'id' => $request['strukkirim']['objectruangantujuanfk'],
            )
        );
        $strRuanganTujuan = '';
        $strRuanganTujuan = $ruanganTujuan[0]->namaruangan;
        try {
            if ($request['strukkirim']['noreckirim'] == '') {
                if ($request['strukkirim']['norecOrder'] != '') {
                    $dataAing = TransaksiOrder::where('norec', $request['strukkirim']['norecOrder'])
                        ->where('koders', $idProfile)
                        ->update([
                            'statusorder' => 1
                        ]);
                }
                $dataSK = new TransaksiKirim;
                $dataSK->norec = $dataSK->generateNewId();
                $dataSK->nokirim = $noKirim;
            } else {
                $ruanganStrukKirimSebelumnya = DB::select(
                    DB::raw("
                         select ru.id,ru.namaruangan
                         from ruanganmt as ru 
                         where ru.koders = $idProfile and ru.id=(select ruangantujuanidfk from transaksikirimtr where norec = :norec)"),
                    array(
                        'norec' => $request['strukkirim']['noreckirim'],
                    )
                );

                $strNmRuanganStrukKirimSebelumnya = '';
                $strIdRuanganStrukKirimSebelumnya = '';
                $strNmRuanganStrukKirimSebelumnya = $ruanganStrukKirimSebelumnya[0]->namaruangan;
                $strIdRuanganStrukKirimSebelumnya = $ruanganStrukKirimSebelumnya[0]->id;

                //** Get Data Struk Kirim Sebelumnya */
                $dataSK = TransaksiKirim::where('norec', $request['strukkirim']['noreckirim'])->where('koders', $idProfile)->first();
                $strukKirimOld = TransaksiKirim::where('norec', $request['strukkirim']['noreckirim'])->where('koders', $idProfile)->first();
                TransaksiKartuStok::where('keterangan',  'Kirim Amprahan, dari Ruangan ' . $strRuanganAsal . ' ke Ruangan ' . $strRuanganTujuan . ' No Kirim: ' .  $dataSK->nokirim)
                    ->update([
                        'flagfk' => null
                    ]);
                if ($request['strukkirim']['objectruanganfk'] == $strukKirimOld->ruanganidfk) {

                    $getDetails = TransaksiKirimDetail::where('transaksikirimfk', $request['strukkirim']['noreckirim'])
                        ->where('qtyproduk', '>', 0)
                        ->get();

                    foreach ($getDetails as $item) {
                        $dataSaldoAwalK = DB::select(
                            DB::raw("
                        select sum(qtyproduk) as qty from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                            array(
                                'ruanganfk' => $request['strukkirim']['objectruanganfk'],
                                'produkfk' => $item->produkidfk,
                            )
                        );
                        $saldoAwalPengirim = 0;
                        foreach ($dataSaldoAwalK as $items) {
                            $saldoAwalPengirim = (float)$items->qty;
                        }
                        $tambah = TransaksiStok::where('nostrukterimafk', $item->nostrukterimaidfk)
                            ->where('koders', $idProfile)
                            ->where('ruanganidfk', $request['strukkirim']['objectruanganfk'])
                            ->where('produkidfk', $item->produkidfk)
                            ->first();

                        TransaksiStok::where('norec', $tambah->norec)
                            ->where('koders', $idProfile)
                            ->update([
                                'qtyproduk' => (float)$tambah->qtyproduk + (float)$item->qtyproduk
                            ]);

                        $tglnow =  date('Y-m-d H:i:s');
                        $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                        $dataKS = array(
                            "saldoawal" => (float)$saldoAwalPengirim,
                            "qtyin" => (float)$item->qtyproduk,
                            "qtyout" => 0,
                            "saldoakhir" => (float)$saldoAwalPengirim + (float)$item->qtyproduk,
                            "keterangan" => 'Ubah Kirim Barang, dari Ruangan ' . $strRuanganAsal . ' ke Ruangan ' . $strNmRuanganStrukKirimSebelumnya . ' No Kirim: ' .  $dataSK->nokirim,
                            "produkidfk" => $item->produkidfk,
                            "ruanganidfk" => $request['strukkirim']['objectruanganfk'],
                            "tglinput" => $tglUbah,
                            "tglkejadian" => $tglUbah,
                            "nostrukterimaidfk" => $item->nostrukterimafk,
                            "norectransaksi" => $request['strukkirim']['noreckirim'],
                            "tabletransaksi" => 'transaksikirimtr',
                            "flagfk" => null,
                        );
                        $this->KartuStok($idProfile, $dataKS);

                        if ($request['strukkirim']['jenispermintaanfk'] == 2) {
                            $dataSaldoAwalT = DB::select(
                                DB::raw("
                                select sum(qtyproduk) as qty from transaksistoktr 
                                where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                                array(
                                    'ruanganfk' => $strIdRuanganStrukKirimSebelumnya,
                                    'produkfk' => $item->produkidfk,
                                )
                            );
                            $saldoAwalPenerima = 0;
                            foreach ($dataSaldoAwalT as $items) {
                                $saldoAwalPenerima = (float)$items->qty;
                            }
                            if ($dataSK->jenispermintaanidfk == 2) {
                                $kurang = TransaksiStok::where('nostrukterimafk', $item->nostrukterimaidfk)
                                    ->where('koders', $idProfile)
                                    ->where('ruanganidfk', $strIdRuanganStrukKirimSebelumnya)
                                    ->where('produkidfk', $item->produkidfk)
                                    ->first();
                                TransaksiStok::where('norec', $kurang->norec)
                                    ->where('koders', $idProfile)
                                    ->update([
                                        'qtyproduk' => (float)$kurang->qtyproduk - (float)$item->qtyproduk
                                    ]);

                                $tglnow1 =  date('Y-m-d H:i:s');
                                $tglUbah1 = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow1)));
                                $dataKSs = array(
                                    "saldoawal" => (float)$saldoAwalPenerima,
                                    "qtyin" => 0,
                                    "qtyout" => (float)$item->qtyproduk,
                                    "saldoakhir" => (float)$saldoAwalPenerima - (float)$item->qtyproduk,
                                    "keterangan" => 'Ubah Terima Barang, dari Ruangan ' . $strRuanganAsal . ' ke Ruangan ' . $strNmRuanganStrukKirimSebelumnya . ' No Kirim: ' .  $dataSK->nokirim,
                                    "produkidfk" => $item->produkidfk,
                                    "ruanganidfk" => $strIdRuanganStrukKirimSebelumnya,
                                    "tglinput" => $tglUbah1,
                                    "tglkejadian" => $tglUbah1,
                                    "nostrukterimaidfk" => $item->nostrukterimafk,
                                    "norectransaksi" => $request['strukkirim']['noreckirim'],
                                    "tabletransaksi" => 'transaksikirimtr',
                                    "flagfk" => null,
                                );
                                $this->KartuStok($idProfile, $dataKSs);
                            } else {
                            }
                        } elseif ($strukKirimOld->jenispermintaanidfk == 2 && $request['strukkirim']['jenispermintaanfk'] == 1) {
                            $dataSaldoAwalT = DB::select(
                                DB::raw("
                                select sum(qtyproduk) as qty from transaksistoktr 
                                where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                                array(
                                    'ruanganfk' => $strIdRuanganStrukKirimSebelumnya, 
                                    'produkfk' => $item->produkidfk,
                                )
                            );
                            $saldoAwalPenerima = 0;
                            foreach ($dataSaldoAwalT as $items) {
                                $saldoAwalPenerima = (float)$items->qty;
                            }

                            $kurang = TransaksiStok::where('nostrukterimafk', $item->nostrukterimaidfk)
                                ->where('koders', $idProfile)
                                ->where('ruanganidfk', $strIdRuanganStrukKirimSebelumnya)
                                ->where('produkidfk', $item->produkidfk)
                                ->first();

                            if ((float)$kurang->qtyproduk == 0) {
                                TransaksiStok::where('norec', $kurang->norec)
                                    ->where('koders', $idProfile)
                                    ->update([
                                        'qtyproduk' => 0
                                    ]);
                            } else {
                                TransaksiStok::where('norec', $kurang->norec)
                                    ->where('koders', $idProfile)
                                    ->update([
                                        'qtyproduk' => (float)$kurang->qtyproduk - (float)$item->qtyproduk
                                    ]);
                            }

                            $tglnow1 =  date('Y-m-d H:i:s');
                            $tglUbah1 = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow1)));
                            $dataKS = array(
                                "saldoawal" => (float)$saldoAwalPenerima,
                                "qtyin" => 0,
                                "qtyout" => (float)$item->qtyproduk,
                                "saldoakhir" => (float)$saldoAwalPenerima - (float)$item->qtyproduk,
                                "keterangan" => 'Ubah Terima Barang, dari Ruangan ' . $strRuanganAsal . ' ke Ruangan ' . $strNmRuanganStrukKirimSebelumnya . ' No Kirim: ' .  $dataSK->nokirim,
                                "produkidfk" => $item->produkidfk,
                                "ruanganidfk" => $strIdRuanganStrukKirimSebelumnya,
                                "tglinput" => $tglUbah1,
                                "tglkejadian" => $tglUbah1,
                                "nostrukterimaidfk" => $item->nostrukterimafk,
                                "norectransaksi" => $request['strukkirim']['noreckirim'],
                                "tabletransaksi" => 'transaksikirimtr',
                                "flagfk" => null,
                            );
                            $this->KartuStok($idProfile, $dataKS);
                        } else {
                        }
                    }
                    TransaksiKirimDetail::where('transaksikirimfk', $request['strukkirim']['noreckirim'])->where('koders', $idProfile)->delete();
                } else {

                    $ruanganAsal = Ruangan::where('id', $strukKirimOld->ruanganidfk)->where('koders', $idProfile)->first();
                    $ruanganTujuan = Ruangan::where('id', $strukKirimOld->ruangantujuanidfk)->where('koders', $idProfile)->first();
                    $getDetails = TransaksiKirimDetail::where('transaksikirimfk', $request['strukkirim']['noreckirim'])
                        ->where('koders', $idProfile)
                        ->where('qtyproduk', '>', 0)
                        ->get();

                    foreach ($getDetails as $item) {
                        $dataSaldoAwalK = DB::select(
                            DB::raw("
                        select sum(qtyproduk) as qty from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                            array(
                                'ruanganfk' => $strukKirimOld->ruanganidfk,
                                'produkfk' => $item->produkidfk,
                            )
                        );
                        $saldoAwalPengirim = 0;
                        foreach ($dataSaldoAwalK as $items) {
                            $saldoAwalPengirim = (float)$items->qty;
                        }
                        $tambah = TransaksiStok::where('nostrukterimafk', $item->nostrukterimaidfk)
                            ->where('koders', $idProfile)
                            ->where('ruanganidfk', $strukKirimOld->ruanganidfk)
                            ->where('produkidfk', $item->produkidfk)
                            ->first();
                        TransaksiStok::where('norec', $tambah->norec)
                            ->where('koders', $idProfile)
                            ->update([
                                'qtyproduk' => (float)$tambah->qtyproduk + (float)$item->qtyproduk
                            ]);

                        $tglnow =  date('Y-m-d H:i:s');
                        $tglUbah = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow)));
                        $dataKS = array(
                            "saldoawal" => (float)$saldoAwalPengirim - (float)$item->qtyproduk,
                            "qtyin" => (float)$item->qtyproduk,
                            "qtyout" => 0,
                            "saldoakhir" => (float)$saldoAwalPengirim + (float)$item->qtyproduk,
                            "keterangan" => 'Ubah Kirim Barang, dari Ruangan ' . $ruanganAsal->namaruangan . ' ke Ruangan ' . $ruanganTujuan->namaruangan . ' No Kirim: ' .  $dataSK->nokirim,
                            "produkidfk" => $item->produkidfk,
                            "ruanganidfk" => $strukKirimOld->ruanganidfk,
                            "tglinput" => $tglUbah,
                            "tglkejadian" => $tglUbah,
                            "nostrukterimaidfk" => $item->nostrukterimafk,
                            "norectransaksi" => $request['strukkirim']['noreckirim'],
                            "tabletransaksi" => 'transaksikirimtr',
                            "flagfk" => null,
                        );
                        $this->KartuStok($idProfile, $dataKS);

                        if ($request['strukkirim']['jenispermintaanfk'] == 2) {
                            $dataSaldoAwalT = DB::select(
                                DB::raw("select sum(qtyproduk) as qty from stokprodukdetail_t 
                                where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                                array(
                                    'ruanganfk' => $strIdRuanganStrukKirimSebelumnya,
                                    'produkfk' => $item->produkidfk,
                                )
                            );
                            $saldoAwalPenerima = 0;
                            foreach ($dataSaldoAwalT as $items) {
                                $saldoAwalPenerima = (float)$items->qty;
                            }
                            if ($dataSK->jenispermintaanidfk == 2) {
                                $kurang = TransaksiStok::where('nostrukterimafk', $item->nostrukterimaidfk)
                                    ->where('koders', $idProfile)
                                    ->where('ruanganidfk', $strIdRuanganStrukKirimSebelumnya)
                                    ->where('produkidfk', $item->produkidfk)
                                    ->first();
                                TransaksiStok::where('norec', $kurang->norec)
                                    ->where('koders', $idProfile)
                                    ->update(
                                        [
                                            'qtyproduk' => (float)$kurang->qtyproduk - (float)$item->qtyproduk
                                        ]
                                    );

                                $tglnow1 =  date('Y-m-d H:i:s');
                                $tglUbah1 = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($tglnow1)));
                                $dataKS = array(
                                    "saldoawal" => (float)$saldoAwalPenerima,
                                    "qtyin" => 0,
                                    "qtyout" => (float)$item->qtyproduk,
                                    "saldoakhir" => (float)$saldoAwalPenerima - (float)$item->qtyproduk,
                                    "keterangan" => 'Ubah Terima Barang, dari Ruangan ' . $ruanganAsal->namaruangan . ' ke Ruangan ' . $ruanganTujuan->namaruangan . ' No Kirim: ' .  $dataSK->nokirim,
                                    "produkidfk" => $item->produkidfk,
                                    "ruanganidfk" => $strukKirimOld->ruangantujuanidfk,
                                    "tglinput" => $tglUbah1,
                                    "tglkejadian" => $tglUbah1,
                                    "nostrukterimaidfk" => $item->nostrukterimafk,
                                    "norectransaksi" => $request['strukkirim']['noreckirim'],
                                    "tabletransaksi" => 'transaksikirimtr',
                                    "flagfk" => null,
                                );
                                $this->KartuStok($idProfile, $dataKS);
                            } else {
                            }
                        } else {
                        }
                    }
                    TransaksiKirimDetail::where('nokirimfk', $request['strukkirim']['noreckirim'])->where('koders', $idProfile)->delete();
                }
            }

            $dataSK->koders = $idProfile;
            $dataSK->aktif = true;
            $dataSK->pegawaipengirimidfk = $request['strukkirim']['objectpegawaipengirimfk'];
            $dataSK->ruanganasalidfk = $request['strukkirim']['objectruanganfk'];
            $dataSK->ruanganidfk = $request['strukkirim']['objectruanganfk'];
            $dataSK->ruangantujuanidfk = $request['strukkirim']['objectruangantujuanfk'];
            $dataSK->jenispermintaanidfk = $request['strukkirim']['jenispermintaanfk'];
            $dataSK->kelompoktransaksiidfk = $kdJenisTransaksiKirimBarang;
            $dataSK->keteranganlainnyakirim = $request['strukkirim']['keteranganlainnyakirim'];
            $dataSK->qtydetailjenisproduk = 0;
            $dataSK->qtyproduk = $request['strukkirim']['qtyproduk'];
            $dataSK->tglkirim = date($request['strukkirim']['tglkirim']);
            $dataSK->totalbeamaterai = 0;
            $dataSK->totalbiayakirim = 0;
            $dataSK->totalbiayatambahan = 0;
            $dataSK->totaldiscount = 0;
            $dataSK->totalhargasatuan = $request['strukkirim']['totalhargasatuan'];
            $dataSK->totalharusdibayar = 0;
            $dataSK->totalpph = 0;
            $dataSK->totalppn = 0;
            $dataSK->registrasipasienfk = $request['strukkirim']['norec_apd'];
            $dataSK->transaksiorderfk = $request['strukkirim']['norecOrder'];
            if (isset($request['strukkirim']['statuskirim'])) {
                $dataSK->statuskirim = $request['strukkirim']['statuskirim'];
            }
            $dataSK->save();
            $norecSK = $dataSK->norec;

            foreach ($request['details'] as $item) {
                $satuanstandar = DB::select(
                    DB::raw("
                    select ru.satuanstandaridfk from pelayananmt as ru 
                    where ru.id=:id"),
                    array(
                        'id' => $item['produkfk'],
                    )
                );
                $satuanstandarfk = $satuanstandar[0]->satuanstandaridfk;
                if ($request['strukkirim']['jenispermintaanfk'] == 2) {
                    $dataSaldoAwalK = DB::select(
                        DB::raw("
                        select qtyproduk as qty,nostrukterimafk,norec,asalprodukidfk as asalprodukfk,
                               hargadiscount,harganetto1 as harganetto,harganetto1 as hargasatuan
                        from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk                         
                        "),
                        array(
                            'ruanganfk' => $request['strukkirim']['objectruanganfk'],
                            'produkfk' => $item['produkfk'],
                        )
                    );
                    $dataSaldoAwalT = DB::select(
                        "
                        select sum(qtyproduk) as qty from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk",
                        array(
                            'ruanganfk' => $request['strukkirim']['objectruangantujuanfk'],
                            'produkfk' => $item['produkfk'],
                        )
                    );
                    $saldoAwalPenerima = 0;
                    foreach ($dataSaldoAwalT as $items) {
                        $saldoAwalPenerima = (float)$items->qty;
                    }

                    $saldoAwalPengirim = 0;
                    $jumlah = (float)$item['jumlah'] * (float)$item['nilaikonversi'];
                    foreach ($dataSaldoAwalK as $items) {
                        $saldoAwalPengirim = $saldoAwalPengirim + (float)$items->qty;
                        if ((float)$items->qty <= $jumlah) {
                            if ((float)$items->qty > 0) {
                                $qtyqtyqty = (float)$items->qty;
                                $dataKP = new TransaksiKirimDetail;
                                $dataKP->norec = $dataKP->generateNewId();
                                $dataKP->koders = $idProfile;
                                $dataKP->aktif = true;
                                $dataKP->asalprodukidfk = $items->asalprodukfk;
                                $dataKP->hargadiscount = $items->hargadiscount;
                                $dataKP->harganetto = $items->harganetto;
                                $dataKP->hargapph = 0;
                                $dataKP->hargappn = 0;
                                $dataKP->hargasatuan = $items->hargasatuan;
                                $dataKP->hargatambahan = 0;
                                $dataKP->hasilkonversi = (float)$item['nilaikonversi'];
                                $dataKP->produkidfk = $item['produkfk'];
                                $dataKP->produkkirimidfk = $item['produkfk'];
                                $dataKP->transaksikirimfk = $norecSK;
                                $dataKP->persendiscount = 0;
                                $dataKP->qtyproduk = $qtyqtyqty;
                                $dataKP->qtyprodukkonfirmasi = $qtyqtyqty;
                                $dataKP->qtyprodukretur = 0;
                                $dataKP->qtyorder = $item['qtyorder'];
                                $dataKP->qtyprodukterima = $qtyqtyqty;
                                $dataKP->nostrukterimaidfk = $items->nostrukterimafk;
                                $dataKP->ruanganidfk = $request['strukkirim']['objectruangantujuanfk'];
                                $dataKP->ruanganpengirimidfk = $request['strukkirim']['objectruanganfk'];
                                $dataKP->satuan = '-';
                                $dataKP->satuanstandaridfk = $satuanstandarfk;
                                $dataKP->satuanviewidfk = $item['satuanviewfk'];
                                $dataKP->tglpelayanan = date($request['strukkirim']['tglkirim']);
                                $dataKP->qtyprodukterimakonversi = $qtyqtyqty;
                                $dataKP->save();

                                $jumlah = $jumlah - (float)$items->qty;
                                TransaksiStok::where('norec', $items->norec)
                                    ->where('koders', $idProfile)
                                    ->update([
                                        'qtyproduk' => 0
                                    ]);
                                $dataStok = TransaksiStok::where('norec', $items->norec)
                                    ->first();

                                $dataNewSPD = new TransaksiStok;
                                $dataNewSPD->norec = $dataNewSPD->generateNewId();
                                $dataNewSPD->koders = $idProfile;
                                $dataNewSPD->aktif = true;
                                $dataNewSPD->asalprodukidfk = $dataStok->asalprodukidfk;
                                $dataNewSPD->hargadiscount = $dataStok->hargadiscount;
                                $dataNewSPD->harganetto1 = $dataStok->harganetto1;
                                $dataNewSPD->harganetto2 = $dataStok->harganetto2;
                                $dataNewSPD->persendiscount = 0;
                                $dataNewSPD->produkidfk = $dataStok->produkidfk;
                                $dataNewSPD->qtyproduk = $qtyqtyqty;
                                $dataNewSPD->qtyprodukonhand = 0;
                                $dataNewSPD->qtyprodukoutext = 0;
                                $dataNewSPD->qtyprodukoutint = 0;
                                $dataNewSPD->ruanganidfk = $request['strukkirim']['objectruangantujuanfk'];
                                $dataNewSPD->nostrukterimafk = $dataStok->nostrukterimafk;
                                $dataNewSPD->noverifikasifk = $dataStok->noverifikasifk;
                                $dataNewSPD->nobatch = $dataStok->nobatch;
                                $dataNewSPD->tglkadaluarsa = $dataStok->tglkadaluarsa;
                                $dataNewSPD->tglpelayanan = $dataStok->tglpelayanan;
                                $dataNewSPD->tglproduksi = $dataStok->tglproduksi;
                                $dataNewSPD->save();
                            }
                        } else {
                            if ((float)$items->qty > 0) {
                                $dataKP = new TransaksiKirimDetail;
                                $dataKP->norec = $dataKP->generateNewId();
                                $dataKP->koders = $idProfile;
                                $dataKP->aktif = true;
                                $dataKP->asalprodukidfk = $items->asalprodukfk;
                                $dataKP->hargadiscount = $items->hargadiscount;
                                $dataKP->harganetto = $items->harganetto;
                                $dataKP->hargapph = 0;
                                $dataKP->hargappn = 0;
                                $dataKP->hargasatuan = $items->hargasatuan;
                                $dataKP->hargatambahan = 0;
                                $dataKP->hasilkonversi = (float)$item['nilaikonversi'];
                                $dataKP->produkidfk = $item['produkfk'];
                                $dataKP->produkkirimidfk = $item['produkfk'];
                                $dataKP->transaksikirimfk = $norecSK;
                                $dataKP->persendiscount = 0;
                                $dataKP->qtyproduk = $jumlah;
                                $dataKP->qtyprodukkonfirmasi = $jumlah;
                                $dataKP->qtyprodukretur = 0;
                                $dataKP->qtyorder = $item['qtyorder'];
                                $dataKP->qtyprodukterima = $jumlah;
                                $dataKP->nostrukterimaidfk = $items->nostrukterimafk;
                                $dataKP->ruanganidfk = $request['strukkirim']['objectruangantujuanfk'];
                                $dataKP->ruanganpengirimidfk = $request['strukkirim']['objectruanganfk'];
                                $dataKP->satuan = '-';
                                $dataKP->satuanstandaridfk = $satuanstandarfk; //$item['satuanstandarfk'];
                                $dataKP->satuanviewidfk = $item['satuanviewfk'];
                                $dataKP->tglpelayanan = date($request['strukkirim']['tglkirim']);
                                $dataKP->qtyprodukterimakonversi = $jumlah;
                                $dataKP->save();

                                $saldoakhir = (float)$items->qty - $jumlah;
                                TransaksiStok::where('norec', $items->norec)
                                    ->where('koders', $idProfile)
                                    ->sharedLock()
                                    ->update([
                                        'qtyproduk' => (float)$saldoakhir
                                    ]);

                                $dataStok = TransaksiStok::where('norec', $items->norec)
                                    ->where('koders', $idProfile)
                                    ->first();

                                $dataNewSPD = new TransaksiStok;
                                $dataNewSPD->norec = $dataNewSPD->generateNewId();
                                $dataNewSPD->koders = $idProfile;
                                $dataNewSPD->aktif = true;
                                $dataNewSPD->asalprodukidfk = $dataStok->asalprodukidfk;
                                $dataNewSPD->hargadiscount = $dataStok->hargadiscount;
                                $dataNewSPD->harganetto1 = $dataStok->harganetto1;
                                $dataNewSPD->harganetto2 = $dataStok->harganetto2;
                                $dataNewSPD->persendiscount = 0;
                                $dataNewSPD->produkidfk = $dataStok->produkidfk;
                                $dataNewSPD->qtyproduk = ((float)$jumlah);
                                $dataNewSPD->qtyprodukonhand = 0;
                                $dataNewSPD->qtyprodukoutext = 0;
                                $dataNewSPD->qtyprodukoutint = 0;
                                $dataNewSPD->ruanganidfk = $request['strukkirim']['objectruangantujuanfk'];
                                $dataNewSPD->nostrukterimafk = $dataStok->nostrukterimafk;
                                $dataNewSPD->noverifikasifk = $dataStok->noverifikasifk;
                                $dataNewSPD->nobatch = $dataStok->nobatch;
                                $dataNewSPD->tglkadaluarsa = $dataStok->tglkadaluarsa;
                                $dataNewSPD->tglpelayanan = $dataStok->tglpelayanan;
                                $dataNewSPD->tglproduksi = $dataStok->tglproduksi;
                                $dataNewSPD->save();
                                $jumlah = 0;
                            }
                        }
                    }

                    $tglnow1 =  date('Y-m-d H:i:s');
                    $qtyAkhir = (float)$saldoAwalPengirim - ((float)$item['jumlah'] * (float)$item['nilaikonversi']);
                    $dataKSr = array(
                        "saldoawal" => (float)$saldoAwalPengirim, 
                        "qtyin" => 0,
                        "qtyout" => ((float)$item['jumlah'] * (float)$item['nilaikonversi']),
                        "saldoakhir" => $qtyAkhir,
                        "keterangan" => 'Kirim Barang, dari Ruangan ' . $strRuanganAsal . ' ke Ruangan ' . $strRuanganTujuan . ' No Kirim: ' .  $dataSK->nokirim,
                        "produkidfk" => $item['produkfk'],
                        "ruanganidfk" => $request['strukkirim']['objectruanganfk'],
                        "tglinput" => $tglnow1,
                        "tglkejadian" => $tglnow1,
                        "nostrukterimaidfk" => $dataStok->nostrukterimafk,
                        "norectransaksi" => $norecSK,
                        "tabletransaksi" => 'transaksikirimtr',
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $dataKSr);
                    $dataKSrs = array(
                        "saldoawal" => (float)$saldoAwalPenerima,
                        "qtyin" => ((float)$item['jumlah'] * (float)$item['nilaikonversi']),
                        "qtyout" => 0,
                        "saldoakhir" => (float)$saldoAwalPenerima + ((float)$item['jumlah'] * (float)$item['nilaikonversi']),
                        "keterangan" => 'Terima Barang, dari Ruangan ' . $strRuanganAsal . ' ke Ruangan ' . $strRuanganTujuan . ' No Kirim: ' .  $dataSK->nokirim,
                        "produkidfk" => $item['produkfk'],
                        "ruanganidfk" => $request['strukkirim']['objectruangantujuanfk'],
                        "tglinput" => $tglnow1,
                        "tglkejadian" => $tglnow1,
                        "nostrukterimaidfk" => $dataStok->nostrukterimafk,
                        "norectransaksi" => $norecSK,
                        "tabletransaksi" => 'transaksikirimtr',
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $dataKSrs);
                }
                if ($request['strukkirim']['jenispermintaanfk'] == 1) {
                    $dataSaldoAwalK = DB::select(
                        DB::raw("
                        select qtyproduk as qty,nostrukterimafk,norec,asalprodukidfk as asalprodukfk,
                               hargadiscount,harganetto1 as harganetto,harganetto1 as hargasatuan
                        from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk and qtyproduk > 0 "),
                        array(
                            'ruanganfk' => $request['strukkirim']['objectruanganfk'],
                            'produkfk' => $item['produkfk'],
                        )
                    );
                    $saldoAwalPengirim = 0;
                    $jumlah = (float)$item['jumlah'] * (float)$item['nilaikonversi'];
                    foreach ($dataSaldoAwalK as $items) {
                        $saldoAwalPengirim = $saldoAwalPengirim + (float)$items->qty;
                        if ((float)$items->qty <= $jumlah) {
                            $dataKP = new TransaksiKirimDetail;
                            $dataKP->norec = $dataKP->generateNewId();
                            $dataKP->koders = $idProfile;
                            $dataKP->aktif = true;
                            $dataKP->asalprodukidfk = $items->asalprodukfk;
                            $dataKP->hargadiscount = $items->hargadiscount;
                            $dataKP->harganetto = $items->harganetto;
                            $dataKP->hargapph = 0;
                            $dataKP->hargappn = 0;
                            $dataKP->hargasatuan = $items->hargasatuan;
                            $dataKP->hargatambahan = 0;
                            $dataKP->hasilkonversi = (float)$item['nilaikonversi'];
                            $dataKP->produkidfk = $item['produkfk'];
                            $dataKP->produkkirimidfk = $item['produkfk'];
                            $dataKP->transaksikirimfk = $norecSK;
                            $dataKP->persendiscount = 0;
                            $dataKP->qtyproduk = (float)$items->qty;
                            $dataKP->qtyprodukkonfirmasi = (float)$items->qty;
                            $dataKP->qtyprodukretur = 0;
                            $dataKP->qtyorder = $item['qtyorder'];
                            $dataKP->qtyprodukterima = (float)$items->qty;
                            $dataKP->nostrukterimaidfk = $items->nostrukterimafk;
                            $dataKP->ruanganidfk = $request['strukkirim']['objectruangantujuanfk'];
                            $dataKP->ruanganpengirimidfk =  $request['strukkirim']['objectruanganfk'];
                            $dataKP->satuan = '-';
                            $dataKP->satuanstandaridfk = $satuanstandarfk; 
                            $dataKP->satuanviewidfk = $item['satuanviewfk'];
                            $dataKP->tglpelayanan = date($request['strukkirim']['tglkirim']);
                            $dataKP->qtyprodukterimakonversi = (float)$items->qty;
                            $dataKP->save();

                            $jumlah = $jumlah - (float)$items->qty;
                            TransaksiStok::where('norec', $items->norec)
                                ->where('koders', $idProfile)
                                ->sharedLock()
                                ->update([
                                    'qtyproduk' => 0
                                ]);
                        } else {

                            $dataKP = new TransaksiKirimDetail;
                            $dataKP->norec = $dataKP->generateNewId();
                            $dataKP->koders = $idProfile;
                            $dataKP->aktif = true;
                            $dataKP->asalprodukidfk = $items->asalprodukfk;
                            $dataKP->hargadiscount = $items->hargadiscount;
                            $dataKP->harganetto = $items->harganetto;
                            $dataKP->hargapph = 0;
                            $dataKP->hargappn = 0;
                            $dataKP->hargasatuan = $items->hargasatuan;
                            $dataKP->hargatambahan = 0;
                            $dataKP->hasilkonversi = $jumlah;
                            $dataKP->produkidfk = $item['produkfk'];
                            $dataKP->produkkirimidfk = $item['produkfk'];
                            $dataKP->transaksikirimfk = $norecSK;
                            $dataKP->persendiscount = 0;
                            $dataKP->qtyproduk = $jumlah;
                            $dataKP->qtyprodukkonfirmasi = $jumlah;
                            $dataKP->qtyprodukretur = 0;
                            $dataKP->qtyorder = $item['qtyorder'];
                            $dataKP->qtyprodukterima = $jumlah;
                            $dataKP->nostrukterimaidfk = $items->nostrukterimafk;
                            $dataKP->ruanganidfk = $request['strukkirim']['objectruangantujuanfk'];
                            $dataKP->ruanganpengirimidfk =  $request['strukkirim']['objectruanganfk'];
                            $dataKP->satuan = '-';
                            $dataKP->satuanstandaridfk = $satuanstandarfk; 
                            $dataKP->satuanviewidfk = $item['satuanviewfk'];
                            $dataKP->tglpelayanan = date($request['strukkirim']['tglkirim']);
                            $dataKP->qtyprodukterimakonversi = $jumlah;
                            $dataKP->save();

                            $saldoakhir = (float)$items->qty - $jumlah;
                            $jumlah = 0;
                            TransaksiStok::where('norec', $items->norec)
                                ->where('koders', $idProfile)
                                ->sharedLock()
                                ->update([
                                    'qtyproduk' => (float)$saldoakhir
                                ]);
                        }
                    }

                    $tglnow1 =  date('Y-m-d H:i:s');
                    $dataKS = array(
                        "saldoawal" => (float)$saldoAwalPengirim,
                        "qtyin" => 0,
                        "qtyout" => ((float)$item['jumlah'] * (float)$item['nilaikonversi']),
                        "saldoakhir" => (float)$saldoAwalPengirim - ((float)$item['jumlah'] * (float)$item['nilaikonversi']),
                        "keterangan" => 'Kirim Amprahan, dari Ruangan ' . $strRuanganAsal . ' ke Ruangan ' . $strRuanganTujuan . ' No Kirim: ' .  $dataSK->nokirim,
                        "produkidfk" => $item['produkfk'],
                        "ruanganidfk" => $request['strukkirim']['objectruanganfk'],
                        "tglinput" => $tglnow1,
                        "tglkejadian" => $tglnow1,
                        "nostrukterimaidfk" => $items->norec,
                        "norectransaksi" => $norecSK,
                        "tabletransaksi" => 'transaksikirimtr',
                        "flagfk" => 2,
                    );
                    $this->KartuStok($idProfile, $dataKS);
                }
                $dataSTOKDETAIL2[] = DB::select(
                    DB::raw("
                    select qtyproduk as qty,nostrukterimafk,norec from transaksistoktr 
                    where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk   "),
                    array(
                        'ruanganfk' => $request['strukkirim']['objectruangantujuanfk'],
                        'produkfk' => $item['produkfk'],
                    )
                );
                $dataSTOKDETAIL[] = DB::select(
                    DB::raw("select qtyproduk as qty,nostrukterimafk,norec from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                    array(
                        'ruanganfk' => $request['strukkirim']['objectruanganfk'],
                        'produkfk' => $item['produkfk'],
                    )
                );
                $kirim = TransaksiKartuStok::where('ruanganidfk', $request['strukkirim']['objectruanganfk'])
                    ->where('koders', $idProfile)
                    ->where('produkidfk', $item['produkfk'])
                    ->get();
                $terima = TransaksiKartuStok::where('ruanganidfk', $request['strukkirim']['objectruangantujuanfk'])
                    ->where('koders', $idProfile)
                    ->where('produkidfk', $item['produkfk'])
                    ->get();
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
                "data" => $dataSK,
                // "kirim" => $kirim,
                // "terima" => $terima,
                "tb" => 'slvR',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function BatalKirimBarang(Request $request)
    {
        $idProfile = (int) $this->getDataKdProfile($request);
        \DB::beginTransaction();
        try {
            if (isset($request['strukkirim']['noorderfk']) && $request['strukkirim']['noorderfk'] != '') {
                $dataAing = TransaksiOrder::where('norec', $request['strukkirim']['noorderfk'])
                    ->update([
                        'statusorder' => 3
                    ]);
            }
            $dataSK = TransaksiKirim::where('norec', $request['strukkirim']['noreckirim'])->where('koders', $idProfile)->first();
            $ruanganAsal = Ruangan::where('id', $dataSK->ruanganasalidfk)->where('koders', $idProfile)->first();
            $ruanganTujuan = Ruangan::where('id', $dataSK->ruangantujuanidfk)->where('koders', $idProfile)->first();
            $getDetails = TransaksiKirimDetail::where('transaksikirimfk', $request['strukkirim']['noreckirim'])
                ->where('koders', $idProfile)
                ->where('qtyproduk', '>', 0)
                ->get();
            foreach ($getDetails as $item) {
                $dataSaldoAwalK = DB::select(
                    DB::raw("
                        select sum(qtyproduk) as qty from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                    array(
                        'ruanganfk' => $dataSK->ruanganasalidfk,
                        'produkfk' => $item->produkidfk,
                    )
                );
                $saldoAwalPengirim = 0;
                foreach ($dataSaldoAwalK as $items) {
                    $saldoAwalPengirim = (float)$items->qty;
                }
                $tambah = TransaksiStok::where('nostrukterimafk', $item->nostrukterimaidfk)
                    ->where('koders', $idProfile)
                    ->where('ruanganidfk', $dataSK->ruanganasalidfk)
                    ->where('produkidfk', $item->produkidfk)
                    ->first();
                TransaksiStok::where('norec', $tambah->norec)
                    ->where('koders', $idProfile)
                    ->sharedLock()
                    ->update([
                        'qtyproduk' => (float)$tambah->qtyproduk + (float)$item->qtyproduk
                    ]);

                $dataKs = TransaksiKartuStok::where('keterangan',  'Kirim Amprahan, dari Ruangan ' . $ruanganAsal->namaruangan . ' ke Ruangan ' . $ruanganTujuan->namaruangan . ' No Kirim: ' . $dataSK->nokirim)
                    ->where('koders', $idProfile)
                    ->update([
                        'flagfk' => null
                    ]);

                $dataKS = array(
                    "saldoawal" => (float)$saldoAwalPengirim,
                    "qtyin" => (float)$item->qtyproduk,
                    "qtyout" => 0,
                    "saldoakhir" => (float)$saldoAwalPengirim + (float)$item->qtyproduk,
                    "keterangan" => 'Batal Kirim Barang ke Ruangan ' . $ruanganTujuan->namaruangan . ' No Kirim: ' . $dataSK->nokirim,
                    "produkidfk" => $item->produkidfk,
                    "ruanganidfk" => $dataSK->ruanganasalidfk,
                    "tglinput" => date('Y-m-d H:i:s'),
                    "tglkejadian" => date('Y-m-d H:i:s'),
                    "nostrukterimaidfk" => $item->nostrukterimaidfk,
                    "norectransaksi" => $item->transaksikirimfk,
                    "tabletransaksi" => 'transaksikirimtr',
                );
                $this->KartuStok($idProfile, $dataKS);
                if ((int)$dataSK->jenispermintaanidfk == 2) {
                    $dataSaldoAwalT = DB::select(
                        DB::raw("
                        select sum(qtyproduk) as qty from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                        array(
                            'ruanganfk' => $dataSK->ruangantujuanidfk,
                            'produkfk' => $item->produkidfk,
                        )
                    );

                    $saldoAwalPenerima = 0;
                    foreach ($dataSaldoAwalT as $items) {
                        $saldoAwalPenerima = (float)$items->qty;
                    }
                    $kurang = TransaksiStok::where('nostrukterimafk', $item->nostrukterimaidfk)
                        ->where('ruanganidfk', $dataSK->ruangantujuanidfk)
                        ->where('produkidfk', $item->produkidfk)
                        ->first();
                    if ((float)$kurang->qtyproduk == 0) {
                        TransaksiStok::where('norec', $kurang->norec)
                            ->where('koders', $idProfile)
                            ->sharedLock()
                            ->update([
                                'qtyproduk' => (float)$kurang->qtyproduk
                            ]);
                    } else {
                        TransaksiStok::where('norec', $kurang->norec)
                            ->where('koders', $idProfile)
                            ->sharedLock()
                            ->update([
                                'qtyproduk' => (float)$kurang->qtyproduk - (float)$item->qtyproduk
                            ]);
                    }

                    $dataKS = array(
                        "saldoawal" => (float)$saldoAwalPenerima,
                        "qtyin" => 0,
                        "qtyout" => (float)$item->qtyproduk,
                        "saldoakhir" => (float)$saldoAwalPenerima - (float)$item->qtyproduk,
                        "keterangan" => 'Batal Terima Barang dari Ruangan ' . $ruanganAsal->namaruangan . ' No Kirim: ' . $dataSK->nokirim,
                        "produkidfk" => $item->produkidfk,
                        "ruanganidfk" => $dataSK->ruangantujuanidfk,
                        "tglinput" => date('Y-m-d H:i:s'),
                        "tglkejadian" => date('Y-m-d H:i:s'),
                        "nostrukterimaidfk" => $item->nostrukterimaidfk,
                        "norectransaksi" => $item->transaksikirimfk,
                        "tabletransaksi" => 'transaksikirimtr',
                    );
                    $this->KartuStok($idProfile, $dataKS);
                }
            }
            TransaksiKirim::where('norec', $request['strukkirim']['noreckirim'])
                ->where('koders', $idProfile)
                ->update([
                    'aktif' => false
                ]);
            TransaksiKirimDetail::where('transaksikirimfk', $request['strukkirim']['noreckirim'])
                ->where('koders', $idProfile)
                ->update([
                    'aktif' => false
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $dataSK,
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

    public function getDataComboPenerimaan(Request $request)
    {
        $idProfile = (int)$this->getDataKdProfile($request);
        $dataAsalProduk = \DB::table('asalprodukmt as ap')
            // ->JOIN('transaksistoktr as spd', 'spd.asalprodukidfk', '=', 'ap.id')
            ->select('ap.id', 'ap.asalproduk')
            ->where('ap.koders', $idProfile)
            ->where('ap.aktif', true)
            ->orderBy('ap.id')
            ->groupBy('ap.id', 'ap.asalproduk')
            ->get();

        $detailJenisProdukTindakan = explode(',', $this->settingDataFixed('kdDetailJenisProdukTindakan', $idProfile));
        $listNotIn = [];
        foreach ($detailJenisProdukTindakan as $items) {
            $listNotIn[] = (int)$items;
        }
        $dataKelompokProduk = \DB::table('pelayananmt as pr')
            ->select('kp.id', 'kp.kelompokproduk')
            ->JOIN('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->JOIN('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->JOIN('kelompokprodukmt as kp', 'kp.id', '=', 'jp.kelompokprodukidfk')
            ->where('kp.aktif', true)
            ->whereNotIn('pr.detailjenisprodukidfk', $listNotIn)
            ->groupBy(DB::raw("
                kp.id,kp.kelompokproduk
            "))
            ->get();

        $result = array(
            'asalproduk' => $dataAsalProduk,
            'kelompokproduk' => $dataKelompokProduk,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getProdukPenerimaan(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataProduk = \DB::table('pelayananmt as pr')
            ->leftjoin('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            ->leftjoin('jenisprodukmt as jp', 'jp.id', '=', 'djp.jenisprodukidfk')
            ->leftJOIN('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->select('pr.id', 'pr.namaproduk', 'ss.id as ssid', 'ss.satuanstandar', 'pr.spesifikasi')
            ->where('pr.koders', $kdProfile)
            ->where('pr.aktif', true);

        if (isset($request['idKelProduk']) && $request['idKelProduk'] != '') {
            $dataProduk = $dataProduk->where('jp.kelompokprodukidfk', '=', $request['idKelProduk']);
        }
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
            ->where('ks.koders', $kdProfile)
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
                'spesifikasi' => $item->spesifikasi,
            );
        }
        return $this->respond($dataProdukResult);
    }

    public function saveTerimaBarangSuplier(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $kdJenisTransaksiPenerimaan = (int) $this->settingDataFixed('kdJenisTransaksiPenerimaanBarang', $idProfile);
        \DB::beginTransaction();
        try {
            $req = $request;
            $status = "";
            $noStruk = "";
            $tglTrans =  date('Y-m-d H:i:s');
            if ($request['struk']['nostruk'] == '' ) {
                $SP = new StrukPelayanan();
                $norecSP = $SP->generateNewId();
                if (isset($req['struk']['noterima'])) {
                    $noStruk = $req['struk']['noterima'];
                } else {
                    $noStruk = $this->generateCodeBySeqTable(new StrukPelayanan(), 'nostruk', 13, 'SR/' . $this->getDateTime()->format('ym') . '/', $idProfile);
                    if ($noStruk == '') {
                        $ReportTrans = "Ulang lagi";
                        \DB::rollBack();
                        $result = array(
                            "status" => 400,
                            "message"  => $ReportTrans,
                            "tb" => 'slvR',
                        );
                        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
                    }
                }
                $SP->norec = $norecSP;
                $SP->koders = $idProfile;
                $SP->aktif = true;
                $SP->kelompoktransaksiidfk = $kdJenisTransaksiPenerimaan;
                $SP->noorderidfk = $req['struk']['norecOrder'];
                $SP->nostruk = $noStruk;
                $SP->nokontrak = $req['struk']['nokontrak'];;
                $SP->nofaktur = $req['struk']['nofaktur'];
                $SP->pegawaipenerimaidfk = $req['struk']['pegawaimenerimafk'];
                $SP->pegawaimenerimaidfk = $req['struk']['pegawaimenerimafk'];
                $SP->pegawaipenanggungjawabidfk = $req['struk']['objectpegawaipenanggungjawabfk'];
                $SP->rekananidfk = $req['struk']['rekananfk'];
                $SP->namarekanan = $req['struk']['namarekanan'];
                $SP->namapegawaipenerima = $req['struk']['namapegawaipenerima'];
                if ($request['struk']['norecOrder'] != '') {
                    $data = TransaksiOrder::where('norec', $request['struk']['norecOrder'])->first();
                    $SP->nosppb = $req['struk']['noorder'];
                    foreach ($req['details'] as $item) {
                        $dataOP = TransaksiOrderDetail::where('transaksiorderfk', $request['struk']['norecOrder'])
                            ->where('koders', $idProfile)
                            ->where('produkidfk', $item['produkfk'])
                            ->update(
                                [
                                    'qtyterimalast' => (float)$item['jumlah'] + (float)$item['jumlahusulan']
                                ]
                            );
                    }
                }
                $SP->noterima = $noStruk;
            } else {
                $dataKS =  TransaksiKartuStok::where('keterangan',  'Penerimaan Barang Suplier. No Terima. ' . $req['struk']['noterima'] . ' Faktur No.' . $req['struk']['nofaktur'] . ' ' . $req['struk']['namarekanan'])
                    ->where('koders', $idProfile)
                    ->update([
                        'flagfk' => null
                    ]);

                $dataKembaliStok = DB::select(
                    DB::raw("
                        select sp.norec,spd.qtyproduk,spd.hasilkonversi,sp.ruanganidfk as objectruanganfk,spd.produkidfk as objectprodukfk,
                               sp.nostruk
                        from strukpelayanandetailtr as spd
                        INNER JOIN strukpelayanantr sp on sp.norec=spd.nostrukidfk
                        where sp.koders = $idProfile and sp.norec=:norec  order by spd.produkidfk"),
                    array(
                        'norec' => $request['struk']['nostruk'],
                    )
                );
                $TambahStok = 0;
                $ruanganEdit = $request['struk']['ruanganfk'];
                $dataSaldoAwalUbah= [];
                $dataSaldoAwalUbah = DB::select("
                select sum(qtyproduk) as qty ,produkidfk from transaksistoktr
                where koders = $idProfile and ruanganidfk=$ruanganEdit group by produkidfk
                ");
                foreach ($dataKembaliStok as $item5) {
                    $TambahStok = (float)$item5->qtyproduk * (float)$item5->hasilkonversi;
                    $saldoAwal = 0;
                    foreach ($dataSaldoAwalUbah as $iite) {
                        if($item5->objectprodukfk == $iite->produkidfk){
                            $saldoAwal = (float)$iite->qty;
                            break;
                        }
                    }
                    if ($request['struk']['norecOrder'] != '') {
                        foreach ($req['details'] as $item) {
                            $dataOP = TransaksiOrderDetail::where('transaksiorderfk', $request['struk']['norecOrder'])
                                ->where('koders', $idProfile)
                                ->where('produkidfk', $item['produkfk'])
                                ->update(
                                    [
                                        'qtyterimalast' => (float)$item['jumlah']
                                    ]
                                );
                        }
                    }
                    if ((float)$saldoAwal < (float)$TambahStok) {
                        $transStatus = 'false';
                        $status = ", Stok Sudah Hasil Penerimaan Telah Digunakan Data Tidak Bisa Diubah!";
                    }

                    $tglUbah1 = date('Y-m-d H:i:s',strtotime('-5 seconds',strtotime($tglTrans)));
                    $dataKS = array(
                        "saldoawal" => (float)$saldoAwal,
                        "qtyin" => 0,
                        "qtyout" => (float)$TambahStok,
                        "saldoakhir" => (float)$saldoAwal - (float)$TambahStok,
                        "keterangan" => 'Ubah Penerimaan No. ' . $item5->nostruk,
                        "produkidfk" =>  $item5->objectprodukfk,
                        "ruanganidfk" => $item5->objectruanganfk,
                        "tglinput" => $tglUbah1,
                        "tglkejadian" => $tglUbah1,
                        "nostrukterimaidfk" => $request['struk']['nostruk'],
                        "norectransaksi" => $request['struk']['nostruk'],
                        "tabletransaksi" => 'strukpelayanan',
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $dataKS);
                    $SP = StrukPelayanan::where('norec', $request['struk']['nostruk'])->first();
                    $noStruk = $SP->nostruk;
                    $delSPD = TransaksiStok::where('nostrukterimafk', $request['struk']['nostruk'])
                        ->where('koders', $idProfile)
                        ->delete();
                    $delSPD = StrukPelayananDetail::where('nostrukidfk', $request['struk']['nostruk'])
                        ->where('koders', $idProfile)
                        ->delete();
                }
            }
            $SP->asalprodukidfk = $req['struk']['asalproduk'];
            $SP->tglfaktur = date('Y-m-d H:i:s', strtotime($req['struk']['tglfaktur']));
            $SP->tglstruk = date('Y-m-d H:i:s', strtotime($req['struk']['tglstruk']));
            $SP->ruanganidfk = $req['struk']['ruanganfk'];
            $SP->keteranganlainnya = 'Penerimaan Barang Dari Supplier';
            $SP->qtyproduk = $req['struk']['qtyproduk'];
            $SP->totalharusdibayar = $req['struk']['totalharusdibayar'];
            $SP->totalppn = $req['struk']['totalppn'];
            $SP->totaldiscount = $req['struk']['totaldiscount'];
            $SP->totalhargasatuan = $req['struk']['totalhargasatuan'];
            $SP->keteranganambil = $req['struk']['ketterima'];
            $SP->tgldokumen = $req['struk']['tglorder'];
            $SP->tglkontrak = $req['struk']['tglkontrak'];
            $SP->namapengadaan = $req['struk']['namapengadaan'];
            $SP->kelompokprodukidfk = $req['struk']['kelompokproduk'];
            $SP->tgljatuhtempo = $req['struk']['tgljatuhtempo'];
            $SP->save();

            foreach ($req['details'] as $item) {
                $qtyJumlah = (float)$item['jumlah'] * (float)$item['nilaikonversi'];
                $SPD = new StrukPelayananDetail();
                $norecKS = $SPD->generateNewId();
                $SPD->norec = $norecKS;
                $SPD->koders = $idProfile;
                $SPD->aktif = true;
                $SPD->nostrukidfk = $SP->norec;
                $SPD->asalprodukidfk = $item['asalprodukfk'];
                $SPD->produkidfk = $item['produkfk'];
                $SPD->ruanganidfk = $item['ruanganfk'];
                $SPD->ruanganstokidfk = $item['ruanganfk'];
                $SPD->satuanstandaridfk = $item['satuanstandarfk'];
                $SPD->hargadiscount = (float)$item['hargadiscount'];
                $SPD->hargadiscountgive = 0;
                $SPD->hargadiscountsave = 0;
                $SPD->harganetto = (float)$item['hargasatuan'];
                $SPD->hargapph = 0;
                $SPD->hargappn = (float)$item['ppn'];
                $SPD->hargasatuan = (float)$item['hargasatuan'];
                $SPD->hasilkonversi = $item['nilaikonversi'];
                $SPD->namaproduk = $item['namaproduk'];
                $SPD->keteranganlainnya = $item['keterangan'];
                $SPD->hargasatuandijamin = 0;
                $SPD->hargasatuanppenjamin = 0;
                $SPD->hargatambahan = 0;
                $SPD->hargasatuanpprofile = 0;
                $SPD->isonsiteservice = 0;
                $SPD->persendiscount = (float)$item['persendiscount'];
                $SPD->persenppn = (float)$item['persenppn'];
                $SPD->qtyproduk = (float)$item['jumlah'];
                $SPD->qtyprodukoutext = 0;
                $SPD->qtyprodukoutint = (float)$item['jumlahusulan'];
                $SPD->qtyprodukretur = 0;
                $SPD->satuan = '-';
                $SPD->satuanstandar = $item['satuanviewfk'];
                $SPD->tglpelayanan = date('Y-m-d H:i:s', strtotime($req['struk']['tglstruk']));
                $SPD->is_terbayar = 0;
                $SPD->linetotal = 0;
                $SPD->tglkadaluarsa = $item['tglkadaluarsa'];
                $SPD->nobatch = $item['nobatch'];
                $SPD->totaldiskon = (float)$item['totaldiskon'];
                $SPD->totalppn = (float)$item['ppn'];
                $SPD->totalharga = (float)$item['subtotal'];
                $SPD->save();

                $StokPD = new TransaksiStok();
                $norecStokPD = $StokPD->generateNewId();
                $StokPD->norec = $norecKS;
                $StokPD->koders = $idProfile;
                $StokPD->aktif = true;
                $StokPD->asalprodukidfk = $item['asalprodukfk'];
                $StokPD->hargadiscount = 0;
                $diskon = (((float)$item['persendiscount']) * (float)$item['hargasatuan']) / 100;
                $hargaStlhDiskon = (float)$item['hargasatuan'] - $diskon;
                $ppn = ((float) $item['persenppn'] * $hargaStlhDiskon) / 100;
                $StokPD->harganetto1 = ($hargaStlhDiskon + $ppn) / (float)$item['nilaikonversi'];
                $StokPD->harganetto2 = ((float)$item['hargasatuan']) / (float)$item['nilaikonversi'];
                $StokPD->persendiscount = 0;
                $StokPD->produkidfk = $item['produkfk'];
                $StokPD->qtyproduk = $qtyJumlah;
                $StokPD->qtyprodukonhand = 0;
                $StokPD->qtyprodukoutext = 0;
                $StokPD->qtyprodukoutint = 0;
                $StokPD->ruanganidfk = $req['struk']['ruanganfk'];
                $StokPD->nostrukterimafk = $SP->norec;
                $StokPD->nobatch = $item['nobatch'];
                $StokPD->objectstrukpelayanandetail = $SPD->norec;
                $StokPD->tglkadaluarsa = $item['tglkadaluarsa'];
                $StokPD->tglpelayanan = date('Y-m-d H:i:s', strtotime($req['struk']['tglstruk']));
                $StokPD->save();

                $dataSaldoAwal = DB::select(
                    DB::raw("
                                select sum(qtyproduk) as qty from transaksistoktr
                                where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                    array(
                        'ruanganfk' => $item['ruanganfk'],
                        'produkfk' => $item['produkfk'],
                    )
                );

                foreach ($dataSaldoAwal as $items) {
                    $saldoAwal = (float)$items->qty;
                }
                if ($saldoAwal == 0) {
                    $saldoAwal = $qtyJumlah;
                }

                $dataKS = array(
                    "saldoawal" => (float)$saldoAwal - $qtyJumlah,
                    "qtyin" => $qtyJumlah,
                    "qtyout" => 0,
                    "saldoakhir" => (float)$saldoAwal,
                    "keterangan" => 'Penerimaan Barang Suplier. No Terima. ' . $noStruk . ' Faktur No.' . $req['struk']['nofaktur'] . ' ' . $req['struk']['namarekanan'],
                    "produkidfk" => $item['produkfk'],
                    "ruanganidfk" => $item['ruanganfk'],
                    "tglinput" => $tglTrans,//date('Y-m-d H:i:s'),
                    "tglkejadian" => $tglTrans,//date('Y-m-d H:i:s'),
                    "nostrukterimaidfk" => $SP->norec,
                    "norectransaksi" => $SP->norec,
                    "tabletransaksi" => 'strukpelayanan',
                    "flagfk" => null,
                );
                $this->KartuStok($idProfile, $dataKS);
            }
            $datanorecSR = '';
            if ($req['struk']['norecrealisasi'] == '') {
                $dataSR = new TransaksiRealisasi();
                $norealisasi = $this->generateCode(new TransaksiRealisasi(), 'norealisasi', 10, 'RA-' .
                    $this->getDateTime()->format('ym'), $idProfile);
                $dataSR->norec = $dataSR->generateNewId();
                $dataSR->koders = $idProfile;
                $dataSR->aktif = true;
                $dataSR->norealisasi = $norealisasi;
                $dataSR->tglrealisasi = date('Y-m-d H:i:s', strtotime($req['struk']['tglrealisasi']));
                $dataSR->mataanggaranidfk = $req['struk']['objectmataanggaranfk'];
                $dataSR->totalbelanja = $req['struk']['totalharusdibayar'];
                $dataSR->save();
                $datanorecSR = $dataSR->norec;
            } else {
                $dataSR = TransaksiRealisasi::where('norec', $req['struk']['norecrealisasi'])->first();
                $dataSR->tglrealisasi = date('Y-m-d H:i:s', strtotime($req['struk']['tglstruk']));
                $dataSR->mataanggaranidfk = $req['struk']['objectmataanggaranfk'];
                $dataSR->totalbelanja = $req['struk']['totalharusdibayar'];
                $dataSR->save();
                $datanorecSR = $req['struk']['norecrealisasi'];
            }

            if ($req['struk']['norecrealisasi'] == '') {
                $dataRR = new TransaksiRealisasiDetail();
                $dataRR->norec = $dataRR->generateNewId();
                $dataRR->koders = $idProfile;
                $dataRR->aktif = true;
                $dataRR->kelompoktransaksiidfk = $kdJenisTransaksiPenerimaan;
                $dataRR->strukrealisasifk = $datanorecSR;
                $dataRR->strukfk = $req['struk']['norecOrder'];
                $dataRR->penerimaanfk = $SP->norec;;
                $dataRR->tglrealisasi = date('Y-m-d H:i:s', strtotime($req['struk']['tglrealisasi'])); 
                $dataRR->petugasfk = $req['struk']['pegawaimenerimafk'];
                $dataRR->noorderintern = $req['struk']['noorder'];
                $dataRR->keteranganlainnya = 'Penerimaan Barang Dari Supplier';
                $dataRR->save();
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
                // "dataSaldoAwalUbah" => $dataSaldoAwalUbah,
                "tb" => 'godU',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
                "tb" => 'godU',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDaftarPenerimaanSuplier(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $kdJenisTransaksiPenerimaan = (int) $this->settingDataFixed('kdJenisTransaksiPenerimaanBarang', $idProfile);
        $dataLogin = $request->all();
        $data = \DB::table('strukpelayanantr as sp')
            ->leftJOIN('rekananmt as rkn', 'rkn.id', '=', 'sp.rekananidfk')
            ->LEFTJOIN('pegawaimt as pg', 'pg.id', '=', 'sp.pegawaimenerimaidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'sp.ruanganidfk')
            ->select(DB::raw("
                sp.tglstruk,sp.nostruk,rkn.namarekanan,pg.namalengkap,sp.nokontrak,ru.namaruangan,
			    sp.norec,sp.nofaktur,sp.tglfaktur,CAST (sp.totalharusdibayar AS FLOAT),sp.nosbklastidfk,
			    sp.nosppb,sp.noorderidfk,sp.qtyproduk
            "))
            ->where('sp.koders', $idProfile)
            ->groupBy(DB::raw("
                 sp.tglstruk,sp.nostruk,rkn.namarekanan,pg.namalengkap,sp.nokontrak,ru.namaruangan,
				 sp.norec,sp.nofaktur,sp.tglfaktur,sp.totalharusdibayar,sp.nosbklastidfk,sp.nosppb,
				 sp.noorderidfk,sp.qtyproduk
            "));

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('sp.tglstruk', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('sp.tglstruk', '<=', $tgl);
        }
        if (isset($request['nostruk']) && $request['nostruk'] != "" && $request['nostruk'] != "undefined") {
            $data = $data->where('sp.nostruk', 'ILIKE', '%' . $request['nostruk']);
        }
        if (isset($request['namarekanan']) && $request['namarekanan'] != "" && $request['namarekanan'] != "undefined") {
            $data = $data->where('rkn.namarekanan', 'ILIKE', '%' . $request['namarekanan'] . '%');
        }
        if (isset($request['nofaktur']) && $request['nofaktur'] != "" && $request['nofaktur'] != "undefined") {
            $data = $data->where('sp.nofaktur', 'ILIKE', '%' . $request['nofaktur'] . '%');
        }
        if (isset($request['noSppb']) && $request['noSppb'] != "" && $request['noSppb'] != "undefined") {
            $data = $data->where('sp.nosppb', 'ILIKE', '%' . $request['noSppb'] . '%');
        }
        $data = $data->where('sp.aktif', true);
        $data = $data->where('sp.kelompoktransaksiidfk', $kdJenisTransaksiPenerimaan);
        $data = $data->orderBy('sp.nostruk');
        $data = $data->get();

        foreach ($data as $item) {
            $details = \DB::select(
                DB::raw("select pr.id as kdproduk,pr.namaproduk,ss.satuanstandar,spd.qtyproduk,spd.qtyprodukretur,spd.hargasatuan,spd.hargadiscount,
                    spd.persendiscount,spd.persenppn,spd.hargappn,CAST(((spd.qtyproduk*spd.hargasatuan)-(((spd.persendiscount*spd.hargasatuan)/100)*spd.qtyproduk))+
                    (spd.persenppn*((spd.qtyproduk*spd.hargasatuan)-(((spd.persendiscount*spd.hargasatuan)/100)*spd.qtyproduk))/100) AS FLOAT) AS total,
                    spd.tglkadaluarsa,spd.nobatch
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
                'nosbk' => $item->nosbklastidfk,
                'nosppb' => $item->nosppb,
                'nokontrak' => $item->nokontrak,
                'noorderfk' => $item->noorderidfk,
                'jmlitem' => $item->qtyproduk,
                'details' => $details,
            );
        }
        if (count($data) == 0) {
            $result = [];
        }

        $result = array(
            'data' => $result,
            'datalogin' => $dataLogin,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function DeletePenerimaanBarangSupplier(Request $request)
    {
        \DB::beginTransaction();
        $ReportTrans = '';
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        try {
            $dataKembaliStok = DB::select(
                DB::raw("
                    select sp.norec,spd.qtyproduk,spd.hasilkonversi,sp.ruanganidfk as objectruanganfk,spd.produkidfk as objectprodukfk,
                           sp.nostruk
                    from strukpelayanandetailtr as spd
                    INNER JOIN strukpelayanantr sp on sp.norec=spd.nostrukidfk
                    where spd.koders = $idProfile and sp.norec=:norec"),
                array(
                    'norec' => $request['nostruk'],
                )
            );

            $dataStokSudahKirim = TransaksiStok::where('nostrukterimafk', $request['nostruk'])
                ->where('koders', $idProfile)
                ->whereNotIn('ruanganidfk', [$dataKembaliStok[0]->objectruanganfk])
                ->where('qtyproduk', '>', 0)
                ->get();
            if (count($dataStokSudahKirim) == 0) {
                foreach ($dataKembaliStok as $item5) {
                    $TambahStok = (float)$item5->qtyproduk * (float)$item5->hasilkonversi;

                    $dataSaldoAwal = DB::select(
                        DB::raw("select sum(qtyproduk) as qty from transaksistoktr
                            where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                        array(
                            'ruanganfk' => $item5->objectruanganfk,
                            'produkfk' => $item5->objectprodukfk,
                        )
                    );
                    $saldoAwal = 0;
                    foreach ($dataSaldoAwal as $itemss) {
                        $saldoAwal = (float)$itemss->qty;
                    }

                    $dataPenerimaan = \DB::table('strukpelayanantr as sr')
                        ->leftJoin('rekananmt as rkn', 'rkn.id', '=', 'sr.rekananidfk')
                        ->select(DB::raw("sr.nostruk,sr.nofaktur,rkn.namarekanan"))
                        ->where('sr.koders', $idProfile)
                        ->where('sr.norec', $request['nostruk'])
                        ->first();

                    $dataKS = TransaksiKartuStok::where('keterangan',  'Penerimaan Barang Suplier. No Terima. ' . $dataPenerimaan->nostruk . ' Faktur No.' . $dataPenerimaan->nofaktur . ' ' . $dataPenerimaan->namarekanan)
                        ->where('koders', $idProfile)
                        ->update([
                            'flagfk' => null
                        ]);

                    $dataKS = array(
                        "saldoawal" => (float)$saldoAwal,
                        "qtyin" => 0,
                        "qtyout" => (float)$TambahStok,
                        "saldoakhir" => (float)$saldoAwal - (float)$TambahStok,
                        "keterangan" => 'Batal Penerimaan No. ' . $item5->nostruk,
                        "produkidfk" =>  $item5->objectprodukfk,
                        "ruanganidfk" => $item5->objectruanganfk,
                        "tglinput" =>  date('Y-m-d H:i:s'),
                        "tglkejadian" =>  date('Y-m-d H:i:s'),
                        "nostrukterimaidfk" => $request['nostruk'],
                        "norectransaksi" => $request['nostruk'],
                        "tabletransaksi" => 'transaksistok',
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $dataKS);

                    TransaksiOrderDetail::where('transaksiorderfk', $request['noorderfk'])
                        ->where('koders', $idProfile)
                        ->where('produkidfk', $item5->objectprodukfk)
                        ->update([
                            'qtyterimalast' => 0
                        ]);
                }
                $SP = StrukPelayanan::where('norec', $request['nostruk'])->where('koders', $idProfile)->first();
                $SP->aktif = false;
                $SP->save();

                $delSPD = TransaksiStok::where('nostrukterimafk', $request['nostruk'])
                    ->where('koders', $idProfile)
                    ->delete();

                $kirim = TransaksiKartuStok::where('ruanganidfk', $item5->objectruanganfk)
                    ->where('koders', $idProfile)
                    ->where('produkidfk', $item5->objectprodukfk)
                    ->get();

                $kartuStok[] = $kirim;

                $dataSTOKDETAIL[] = DB::select(
                    DB::raw("
                        select qtyproduk as qty,nostrukterimafk,norec from transaksistoktr 
                        where koders = $idProfile and ruanganidfk=:ruanganfk and produkidfk=:produkfk"),
                    array(
                        'ruanganfk' => $item5->objectruanganfk,
                        'produkfk' => $item5->objectprodukfk,
                    )
                );

                $stokdetail[] = $dataSTOKDETAIL;

                $transStatus = 'true';
                $ReportTrans = "Selesai";
            } else {
                $transStatus = 'false';
                $ReportTrans = "Sudah ada distribusi, tidak dapat di batalkan!!";
            }
        } catch (\Exception $e) {
            $transStatus = 'false';
        };

        if ($transStatus == 'true') {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "data" => $SP,
                "tb" => 'slvR',
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $ReportTrans,
                "tb" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDaftarPenerimaanSuplierPerUnit(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $kdSirs1 = $request['KdSirs1'];
        $kdSirs2 = $request['KdSirs2'];
        $dataLogin = $request->all();
        $dataRuangan = \DB::table('maploginusertoruangan_s as mlu')
            ->JOIN('ruangan_m as ru', 'ru.id', '=', 'mlu.objectruanganfk')
            ->select('ru.id')
            ->where('mlu.objectloginuserfk', $dataLogin['userData']['id'])
            ->get();

        $data = \DB::table('strukpelayanan_t as sp')
            ->JOIN('strukpelayanandetail_t as spd', 'spd.nostrukfk', '=', 'sp.norec')
            ->LEFTJOIN('rekanan_m as rkn', 'rkn.id', '=', 'sp.objectrekananfk')
            ->LEFTJOIN('pegawai_m as pg', 'pg.id', '=', 'sp.objectpegawaipenerimafk')
            ->LEFTJOIN('ruangan_m as ru', 'ru.id', '=', 'sp.objectruanganfk')
            ->LEFTJOIN('produk_m as pr', 'pr.id', '=', 'spd.objectprodukfk')
            ->LEFTJOIN('satuanstandar_m as ss', 'ss.id', '=', 'spd.objectsatuanstandarfk')
            ->LEFTJOIN('asalproduk_m as asp', 'asp.id', '=', 'spd.objectasalprodukfk')
            ->LEFTJOIN('strukbuktipengeluaran_t as sbk', 'sbk.norec', '=', 'sp.nosbklastfk')
            ->select(
                'sp.tglstruk',
                'sp.nostruk',
                'sp.nofaktur',
                'sp.tglfaktur',
                'pr.id as kdproduk',
                'pr.kdproduk as kdsirs',
                'pr.namaproduk',
                'ss.satuanstandar',
                'rkn.namarekanan',
                'pg.namalengkap as namapenerima',
                'ru.namaruangan',
                'spd.hargasatuan',
                'sbk.nosbk',
                'sp.nosppb',
                'asp.asalproduk',
                \DB::raw("CAST(spd.qtyproduk AS FLOAT) AS qtyproduk,CAST(spd.qtyprodukretur AS FLOAT) AS qtyprodukretur,CAST (spd.hargasatuan AS FLOAT) AS hargasatuan,
                                CAST (spd.qtyproduk*spd.hargasatuan AS FLOAT) AS subtotal,CAST (spd.hargadiscount AS FLOAT) AS hargadiscount,CAST (spd.hargappn AS FLOAT) AS hargappn,
                                CAST (((spd.hargasatuan*spd.qtyproduk) - spd.hargadiscount) + spd.hargappn AS FLOAT) AS total,CAST(spd.tglkadaluarsa as varchar) as  tglkadaluarsa")
            )
            ->where('sp.kdprofile', $idProfile);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('sp.tglfaktur', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'];
            $data = $data->where('sp.tglfaktur', '<=', $tgl);
        }
        if (isset($request['nostruk']) && $request['nostruk'] != "" && $request['nostruk'] != "undefined") {
            $data = $data->where('sp.nostruk', 'ILIKE', '%' . $request['nostruk']);
        }
        if (isset($request['namarekanan']) && $request['namarekanan'] != "" && $request['namarekanan'] != "undefined") {
            $data = $data->where('rkn.namarekanan', 'ILIKE', '%' . $request['namarekanan'] . '%');
        }
        if (isset($request['nofaktur']) && $request['nofaktur'] != "" && $request['nofaktur'] != "undefined") {
            $data = $data->where('sp.nofaktur', 'ILIKE', '%' . $request['nofaktur'] . '%');
        }
        if (isset($request['produkfk']) && $request['produkfk'] != "" && $request['produkfk'] != "undefined") {
            $data = $data->where('spd.objectprodukfk', '=', $request['produkfk']);
        }
        if (isset($request['KdSirs1']) &&  $request['KdSirs1'] != '') {
            if ($request['KdSirs2'] != null &&  $request['KdSirs2'] != '' && $request['KdSirs1'] != null &&  $request['KdSirs1'] != '') {
                $data = $data->whereRaw(" (pr.kdproduk BETWEEN '" . $request['KdSirs1'] . "' and '" . $request['KdSirs2'] . "') ");
            } elseif ($request['KdSirs2'] &&  $request['KdSirs2'] != '' && $request['KdSirs1'] == '' ||  $request['KdSirs1'] == null) {
                $data = $data->whereRaw = (" pr.kdproduk ILIKE '" . $request['KdSirs2'] . "%'");
            } elseif ($request['KdSirs1'] &&  $request['KdSirs1'] != '' && $request['KdSirs2'] == '' ||  $request['KdSirs2'] == null) {
                $data = $data->whereRaw = (" pr.kdproduk ILIKE '" . $request['KdSirs1'] . "%'");
            }
        }

        $data = $data->where('sp.statusenabled', true);
        $data = $data->where('sp.objectkelompoktransaksifk', 35);
        $data = $data->orderBy('pr.kdproduk', 'asc');
        $data = $data->get();
        $result = array(
            'datalogin' => $dataLogin,
            'data' => $data,
            'message' => 'Cepot'
        );
        return $this->respond($result);
    }

    public function getDetailPenerimaanBarang(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $dataStruk = \DB::table('strukpelayanantr as sr')
            ->LEFTJOIN('transaksirealisasidetailtr as rr', 'rr.penerimaanfk', '=', 'sr.norec')
            ->LEFTJOIN('transaksirealisasitr as srr', 'srr.norec', '=', 'rr.strukrealisasifk')
            ->leftJOIN('pegawaimt as pg', 'pg.id', '=', 'sr.pegawaipenerimaidfk')
            ->leftJOIN('pegawaimt as pg1', 'pg1.id', '=', 'sr.pegawaipenanggungjawabidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'sr.ruanganidfk')
            ->leftJOIN('asalprodukmt as ap', 'ap.id', '=', 'sr.asalprodukidfk')
            ->leftJOIN('kelompokprodukmt as kp', 'kp.id', '=', 'sr.kelompokprodukidfk')
            ->select(DB::raw("
                 sr.tglstruk,sr.nostruk,rr.noorderintern AS nousulan,sr.nokontrak,pg.id AS pgid,pg.namalengkap,
                 ru.id,ru.namaruangan,sr.nofaktur,sr.tglfaktur,sr.namarekanan,sr.rekananidfk,sr.nosppb,
                 srr.norealisasi,srr.norec AS norecrealisasi,sr.tglkontrak,sr.tgldokumen,rr.strukfk,
                 rr.tglrealisasi,sr.keteranganlainnya,sr.keteranganambil,pg.id AS pgid,pg.namalengkap,
                 sr.pegawaipenanggungjawabidfk,pg1.namalengkap AS penanggungjawab,
                 sr.namapengadaan,sr.noorderidfk,sr.tgljatuhtempo,sr.asalprodukidfk,ap.asalproduk,
			     sr.kelompokprodukidfk,kp.kelompokproduk,sr.namapengadaan,sr.tgljatuhtempo
            "))
            ->where('sr.koders', $idProfile);

        if (isset($request['norec']) && $request['norec'] != "" && $request['norec'] != "undefined") {
            $dataStruk = $dataStruk->where('sr.norec', '=', $request['norec']);
        }

        $dataStruk = $dataStruk->first();

        $data = \DB::table('strukpelayanandetailtr as spd')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'spd.ruanganidfk')
            ->JOIN('pelayananmt as pr', 'pr.id', '=', 'spd.produkidfk')
            ->leftJOIN('satuanstandarmt as ss', 'ss.id', '=', 'spd.satuanstandaridfk')
            ->leftJOIN('asalprodukmt as ap', 'ap.id', '=', 'spd.asalprodukidfk')
            ->select(DB::raw("
                spd.hargasatuan,spd.qtyproduk,spd.ruanganidfk AS ruanganfk,ru.namaruangan,
 			    spd.produkidfk AS produkfk,pr.namaproduk,spd.hasilkonversi AS nilaikonversi,
 	            spd.satuanstandaridfk,ss.satuanstandar,spd.satuanstandar AS satuanviewfk,
                ss.satuanstandar AS ssview,spd.qtyproduk AS jumlah,spd.hargadiscount,
                spd.hargappn,spd.hargasatuan,spd.asalprodukidfk,ap.asalproduk,spd.persendiscount,
                spd.persenppn,spd.keteranganlainnya,spd.nobatch,spd.tglkadaluarsa,spd.totaldiskon,
                spd.totalppn,spd.totalharga,spd.qtyprodukoutint as jumlahusulan
            "))
            ->where('spd.koders', $idProfile);

        if (isset($request['norec']) && $request['norec'] != "" && $request['norec'] != "undefined") {
            $data = $data->where('spd.nostrukidfk', '=', $request['norec']);
        }
        $data = $data->get();

        $pelayananPasien = [];
        $i = 0;
        foreach ($data as $item) {
            $i = $i + 1;
            $pelayananPasien[] = array(
                'no' => $i,
                'hargasatuan' => $item->hargasatuan,
                'ruanganfk' => $item->ruanganfk,
                'asalprodukfk' => $item->asalprodukidfk,
                'asalproduk' => $item->asalproduk,
                'produkfk' => $item->produkfk,
                'namaproduk' => $item->namaproduk,
                'nilaikonversi' => $item->nilaikonversi,
                'satuanstandarfk' => $item->satuanviewfk,
                'satuanstandar' => $item->ssview,
                'satuanviewfk' => $item->satuanviewfk,
                'satuanview' => $item->ssview,
                'jumlah' => $item->jumlah,
                'jumlahusulan' => $item->jumlahusulan,
                'hargadiscount' => $item->hargadiscount,
                'persendiscount' => $item->persendiscount,
                'ppn' => $item->hargappn,
                'persenppn' => $item->persenppn,
                'subtotal' => $item->totalharga,
                'totaldiskon' => $item->totaldiskon,
                'totalppn' => $item->totalppn,
                'total' =>  $item->totalharga - $item->totaldiskon + (float)$item->totalppn,
                'keterangan' => $item->keteranganlainnya,
                'nobatch' => $item->nobatch,
                'tglkadaluarsa' => $item->tglkadaluarsa,
            );
        }

        $result = array(
            'detailterima' => $dataStruk,
            'details' => $pelayananPasien,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }
}
