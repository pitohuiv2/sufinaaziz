<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\ApiController;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\TransaksiPasienDetail;
use App\Transaksi\PetugasPelaksana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\Valet;
use App\Transaksi\DaftarPasienRuangan;
use phpDocumentor\Reflection\Types\Null_;
use Webpatser\Uuid\Uuid;

class TindakanC extends ApiController
{

    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getTindakanPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        //TODO : GET LIST TINDAKAN
        $req = $request->all();
        $data = DB::table('tarifpelayananmt as hnp')
        ->join('mappelayananruanganmt as mpr', 'mpr.produkidfk', '=', 'hnp.produkidfk')
        ->join('suratkeputusanmt as sk', 'sk.id', '=', 'hnp.suratkeputusanidfk')
        ->join('pelayananmt as prd', 'prd.id', '=', 'mpr.produkidfk')
        ->select(
                    'mpr.produkidfk as id',
                    'prd.namaproduk',
                    'mpr.ruanganidfk',
                    'prd.namaproduk',
                    'hnp.hargasatuan',
            'hnp.id as hnpid'
        )
        ->where('mpr.ruanganidfk', $request['idRuangan'])
        ->where('hnp.kelasidfk', $request['idKelas'])
        ->where('hnp.jenispelayananidfk', $request['idJenisPelayanan'])
        ->where('hnp.aktif', true)
        ->where('mpr.aktif', true)
        ->where('sk.aktif', true)
        ->where('hnp.koders', $idProfile)
        ->where('prd.aktif', true);
       
        // $data = DB::table('mappelayananruanganmt as mpr')
        //     ->join('pelayananmt as prd', 'prd.id', '=', 'mpr.produkidfk')
        //     ->select(
        //         'mpr.produkidfk as id',
        //         'prd.namaproduk',
        //         'mpr.ruanganidfk',
        //         'prd.namaproduk'
        //     )
        //     ->where('mpr.koders', $idProfile)
        //     ->where('mpr.ruanganidfk', $request['idRuangan'])
        //     ->where('mpr.aktif', true)
        //     ->where('prd.aktif', true);
        // if (
        //     isset($req['filter']['filters'][0]['value']) &&
        //     $req['filter']['filters'][0]['value'] != "" &&
        //     $req['filter']['filters'][0]['value'] != "undefined"
        // ) {
        //     $data = $data
        //         ->where('prd.namaproduk', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        // }
        // if (
        //     isset($req['idProduk']) &&
        //     $req['idProduk'] != "" &&
        //     $req['idProduk'] != "undefined"
        // ) {
        //     $data = $data
        //         ->where('prd.id', '=', $req['idProduk']);
        // }
        if (
            isset($req['namaproduk']) &&
            $req['namaproduk'] != "" &&
            $req['namaproduk'] != "undefined"
        ) {
            $data = $data
                ->where('prd.namaproduk', 'ilike', '%' . $req['namaproduk'] . '%');
        }
        $data = $data->distinct();
        $data = $data->orderBy('prd.namaproduk', 'ASC');
        $data = $data->take(15);
        $data = $data->get();
        return $this->respond($data);
    }

    public function getCombo(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $jenisPelaksana = DB::table('jenispetugaspelaksanamt as jpp')
            ->where('jpp.aktif', true)
            ->orderBy('jpp.jenispetugaspe')
            ->get();
        $dataTarifAdminCito = $this->settingDataFixed('tarifadmincito', $idProfile);
        $result = array(
            'jenispelaksana' => $jenisPelaksana,
            'tarifcito' => $dataTarifAdminCito,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getPegawaiByJenisPetugasPe(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $data = DB::table('mapjenispetugasptojenispegawaimt as mpp')
            ->join('jenispegawaimt as jp', 'jp.id', '=', 'mpp.jenispegawaiidfk')
            ->join('pegawaimt as pg', 'pg.objectjenispegawaifk', '=', 'jp.id')
            ->join('jenispetugaspelaksanamt as jpp', 'jpp.id', '=', 'mpp.jenispetugaspeidfk')
            ->select(
                'mpp.jenispegawaiidfk',
                'jp.jenispegawai',
                'mpp.jenispetugaspeidfk',
                'jpp.jenispetugaspe',
                'pg.namalengkap',
                'pg.id'
            )->groupBy(
                'mpp.jenispegawaiidfk',
                'jp.jenispegawai',
                'mpp.jenispetugaspeidfk',
                'jpp.jenispetugaspe',
                'pg.namalengkap',
                'pg.id'
            )
            ->where('mpp.koders', $idProfile)
            ->where('mpp.jenispetugaspeidfk', $request['idJenisPetugas'])
            ->where('mpp.aktif', true)
            ->where('pg.aktif', true)
            ->where('jpp.aktif', true);
        if (isset($request['namalengkap']) && $request['namalengkap'] != '') {
            $data = $data->where('pg.namalengkap', 'ilike', '%' . $request['namalengkap'] . '%');
        }
        $data = $data->orderBy('pg.namalengkap', 'ASC');
        $data = $data->get();

        $result = array(
            'jenispelaksana' => $data,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveTindakan(Request $request)
    {
        //TODO : SAVE TINDAKAN
        DB::beginTransaction();
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        try {

            $antrian = DaftarPasienRuangan::where('norec', $request['pelayananpasien'][0]['noregistrasifk'])
                ->update([
                    'ispelayananpasien' => true
                ]);
            $totalJasa = 0;
            $totJasa = 0;
            $penjumlahanJasa = 0;
            $penjumlahanJasaTuslah = 0;
            foreach ($request['pelayananpasien'] as $item) {
                $totJasa = 0;
                $totalJasa = 0;
                $penjumlahanJasa = 0;
                $penjumlahanJasaTuslah = 0;
                $PelPasien = new TransaksiPasien();
                $PelPasien->norec = $PelPasien->generateNewId();
                $PelPasien->koders = $idProfile;
                $PelPasien->aktif = true;
                $PelPasien->daftarpasienruanganfk = $item['noregistrasifk'];
                $PelPasien->tglregistrasi = $item['tglregistrasi'];
                $PelPasien->hargadiscount = $item['diskon']; //0;
                $PelPasien->hargajual = $item['hargajual'];
                $PelPasien->hargasatuan = $item['hargasatuan'];
                $PelPasien->jumlah = $item['jumlah'];
                $PelPasien->kelasidfk = $item['kelasfk'];
                $PelPasien->kdkelompoktransaksi = 1;
                if (isset($item['keterangan'])) {
                    $PelPasien->keteranganlain = $item['keterangan'];
                }
                $PelPasien->piutangpenjamin = 0;
                $PelPasien->piutangrumahsakit = 0;
                $PelPasien->produkidfk = $item['produkfk'];
                $PelPasien->stock = 1;
                $PelPasien->tglpelayanan = $item['tglpelayanan'];
                $PelPasien->harganetto = $item['harganetto'];
                $PelPasien->iscito = $item['iscito'];
                if (isset($item['isparamedis'])) {
                    $PelPasien->isparamedis = $item['isparamedis'];
                }
                if (isset($item['jenispelayananfk'])) {
                    $PelPasien->jenispelayananidfk = $item['jenispelayananfk'];
                }
                $PelPasien->save();
                $PPnorec = $PelPasien->norec;

                $new_PPP = $item['pelayananpetugas'];
                foreach ($new_PPP as $itemPPP) {
                    $detailItemPPP = $itemPPP['listpegawai'];
                    foreach ($detailItemPPP as $detailItemPPPz) {
                        $PelPasienPetugas = new PetugasPelaksana();
                        $PelPasienPetugas->norec = $PelPasienPetugas->generateNewId();
                        $PelPasienPetugas->koders = $idProfile;
                        $PelPasienPetugas->aktif = true;
                        $PelPasienPetugas->nomasukidfk = $item['noregistrasifk'];
                        $PelPasienPetugas->jenispetugaspeidfk = $itemPPP['objectjenispetugaspefk'];
                        if ($detailItemPPPz == 'undefined') {
                            $PelPasienPetugas->pegawaiidfk = null;
                        } else {
                            $PelPasienPetugas->pegawaiidfk = $detailItemPPPz['id'];
                        }
                        $PelPasienPetugas->transaksipasienfk = $PPnorec;
                        $PelPasienPetugas->save();
                        $PPPnorec = $PelPasienPetugas->norec;
                    }
                }
                //TODO : TARIFF UP TUSLAH
                $dataTuslah = $this->settingDataFixed('PersenUpTuslah', $idProfile);
                //END TARIFF UP TUSLAH
                foreach ($item['komponenharga'] as $itemKomponen) {
                    $PelPasienDetail = new TransaksiPasienDetail();
                    $PelPasienDetail->norec = $PelPasienDetail->generateNewId();
                    $PelPasienDetail->koders = $idProfile;
                    $PelPasienDetail->aktif = true;
                    $PelPasienDetail->daftarpasienruanganfk = $item['noregistrasifk'];
                    $PelPasienDetail->aturanpakai = '-';
                    $PelPasienDetail->hargadiscount = $item['diskon']; //0;
                    $PelPasienDetail->hargajual = $itemKomponen['hargasatuan'];
                    $PelPasienDetail->hargasatuan = $itemKomponen['hargasatuan'];
                    $PelPasienDetail->jumlah = 1;
                    $PelPasienDetail->keteranganlain = '-';
                    $PelPasienDetail->keteranganpakai2 = '-';
                    $PelPasienDetail->komponenhargaidfk = $itemKomponen['komponenhargaidfk'];
                    $PelPasienDetail->transaksipasienfk = $PPnorec;
                    $PelPasienDetail->piutangpenjamin = 0;
                    $PelPasienDetail->piutangrumahsakit = 0;
                    $PelPasienDetail->produkidfk = $item['produkfk'];
                    $PelPasienDetail->stock = 1;
                    $PelPasienDetail->tglpelayanan = $item['tglpelayanan'];
                    $PelPasienDetail->harganetto = $itemKomponen['hargasatuan'];
                    if ($item['iscito'] == "1") {
                        if (!empty($dataTuslah)  && $dataTuslah > 0) {
                            $penjumlahanJasa = ($itemKomponen['hargasatuan'] - $item['diskon']) * $item['nilaicito'];
                            $penjumlahanJasaTuslah = (((float)$itemKomponen['hargasatuan'] * (int)$dataTuslah) / 100);
                            $totalJasa = $totalJasa + $penjumlahanJasa + $penjumlahanJasaTuslah;

                            $PelPasienDetail->jasa = $penjumlahanJasa + $penjumlahanJasaTuslah;
                        } else {
                            $penjumlahanJasa = ($itemKomponen['hargasatuan'] - $item['diskon']) * $item['nilaicito'];
                            $PelPasienDetail->jasa = $penjumlahanJasa;
                            $totalJasa = $totalJasa + $penjumlahanJasa;
                        }
                    } else {
                        if (!empty($dataTuslah) && $dataTuslah > 0) {
                            $penjumlahanJasaTuslah = ((float)$itemKomponen['hargasatuan'] * (int)$dataTuslah) / 100;
                            $PelPasienDetail->jasa = $penjumlahanJasaTuslah;
                            $totalJasa = $totalJasa + $penjumlahanJasaTuslah;
                        } else {
                            $penjumlahanJasa = 0;
                            $PelPasienDetail->jasa = $penjumlahanJasa;
                            $totalJasa = $totalJasa + $penjumlahanJasa;
                        }
                    }

                    $PelPasienDetail->save();
                    $PPDnorec = $PelPasienDetail->norec;
                    $transStatus = 'true';
                }


                if ($item['iscito'] == 1) {
                    $dataaa = TransaksiPasienDetail::where('transaksipasienfk', $PPnorec)->get();
                    foreach ($dataaa as $itemss) {
                        $totJasa = $totJasa + $itemss->jasa;
                    }
                    $dataJasa = TransaksiPasien::where('norec', $PPnorec)
                        ->update([
                            'jasa' => $totalJasa
                        ]);
                }


                if (!empty($dataTuslah)  && $dataTuslah[0]->nilaifield > 0) {
                    $dataJasa = TransaksiPasien::where('norec', $PPnorec)
                        ->update([
                            'istuslah' => 1,
                            'jasa' => $totalJasa
                        ]);
                }

                if (isset($item['diskon'])) {
                    if ($item['diskon'] != 0) {

                        $jasaPelayanan = $this->settingDataFixed('idKomponenJasaPelayanan', $idProfile);
                        if (!empty($jasaPelayanan)) {
                            $data = TransaksiPasienDetail::where('transaksipasienfk', $PPnorec)
                                ->where('komponenhargaidfk', (int)$jasaPelayanan)
                                ->update(
                                    [
                                        'hargadiscount' => $item['diskon']
                                    ]
                                );
                        }
                        $data2 = TransaksiPasien::where('norec', $PPnorec)
                            ->update(
                                [
                                    'hargadiscount' => $item['diskon']
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
            $ReportTrans = "Selesai";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $ReportTrans,
                'dataPP' => $PelPasien,
                'dataPPD' => $PelPasienDetail,
                'dataTuslah' => $dataTuslah,
                'as' => 'Xoxo',
                'edited' => 'godU',
                'as2' => 'slvR',
            );
        } else {
            $ReportTrans = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $ReportTrans,
                'e' => $e->getMessage() . ' ' . $e->getLine(),
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
        //        return $this->respond($requestAll);
    }

    public function getKomponenHarga(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        //TODO :GET HARGA & HARGA KOMPONEN
        $data = DB::table('tarifpelayanandmt as hnp')
            ->join('mappelayananruanganmt as mpr', 'mpr.produkidfk', '=', 'hnp.produkidfk')
            ->join('pelayananmt as prd', 'prd.id', '=', 'mpr.produkidfk')
            ->join('komponentarifmt as kh', 'kh.id', '=', 'hnp.komponenhargaidfk')
            ->join('suratkeputusanmt as sk', 'sk.id', '=', 'hnp.suratkeputusanidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'hnp.kelasidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'mpr.ruanganidfk')
            ->select(DB::raw("
                hnp.komponenhargaidfk,kh.komponentarif as komponenharga,CAST(hnp.hargasatuan AS float8) AS hargasatuan,mpr.produkidfk,kh.iscito
            "))
            ->where('mpr.ruanganidfk', $request['idRuangan'])
            ->where('hnp.kelasidfk', $request['idKelas'])
            ->where('mpr.produkidfk', $request['idProduk'])
            ->where('hnp.jenispelayananidfk', $request['idJenisPelayanan'])
            ->where('mpr.aktif', true)
            ->where('hnp.aktif', true)
            ->where('sk.aktif',true)
            ->where('prd.aktif', true)
            ->where('hnp.koders', $idProfile);
        $data = $data->distinct();
        $data = $data->get();


        $data2 = DB::table('tarifpelayananmt as hnp')
            ->join('mappelayananruanganmt as mpr', 'mpr.produkidfk', '=', 'hnp.produkidfk')
            ->join('pelayananmt as prd', 'prd.id', '=', 'mpr.produkidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'hnp.kelasidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'mpr.ruanganidfk')
            ->join('suratkeputusanmt as sk', 'hnp.suratkeputusanidfk', '=', 'sk.id')
            ->select(
                'mpr.produkidfk',
                'prd.id',
                'prd.namaproduk',
                'hnp.hargasatuan',
                'hnp.kelasidfk',
                'kls.namakelas',
                'mpr.ruanganidfk',
                'ru.namaruangan',
                'prd.namaproduk'
            )
            ->where('mpr.ruanganidfk', $request['idRuangan'])
            ->where('hnp.kelasidfk', $request['idKelas'])
            ->where('mpr.produkidfk', $request['idProduk'])
            ->where('hnp.jenispelayananidfk', $request['idJenisPelayanan'])
            ->where('hnp.aktif', true)
            ->where('sk.aktif', true)
            ->where('mpr.aktif', true)
           
           
            ->where('hnp.koders', $idProfile)
            ->where('prd.aktif', true);
            if (isset($request['hnpid']) && $request['hnpid'] != "" && $request['hnpid'] != "undefined") {
                $data2 = $data2 ->where('hnp.id', $request['hnpid']);
            };
        $data2 = $data2->distinct();
        $data2 = $data2->get();

        $result = array(
            'data' => $data,
            'data2' => $data2,
            'message' => 'Xoxo',
            'edited' => 'slvR',
        );

        return $this->respond($result);
    }
}
