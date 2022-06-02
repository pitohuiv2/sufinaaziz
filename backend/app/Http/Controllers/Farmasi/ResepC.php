<?php


namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Traits\Valet;
use DB;



class ResepC extends ApiController
{
    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getProdukDetail(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;

        $metodeambilharganetto = $this->settingDataFixed('metodeambilharganetto', $kdProfile);

        if (empty($metodeambilharganetto)) {
            return $this->respond(array(
                'Error' => 'Setting Data Fixed Farmasi dulu',
                'message' => 'slvR',
            ));
        }
        $strMetodeAmbilHargaNetto = $metodeambilharganetto;
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
        if (count($persenHargaJualProduk) == 0) {
            return $this->respond(array(
                'Error' => 'Setting persenhargajualproduk_m dulu',
                'message' => 'slvR',
            ));
        }

        $strHN = '';
        $strMSHT = '';
        $SistemHargaNetto = '';
        $MetodeAmbilHargaNetto = '';
        $MetodeStokHargaNetto = '';

        
        if ($strSistemHargaNetto == 7) {
            $SistemHargaNetto = 'Harga Terakhir';
            if ($strMetodeAmbilHargaNetto == 1) {
                $strHN = 'spd.harganetto1';
                $MetodeAmbilHargaNetto = 'HN1';
            }
            if ($strMetodeAmbilHargaNetto == 2) {
                $strHN = 'spd.harganetto2';
                $MetodeAmbilHargaNetto = 'HN2';
            }

            if ($strMetodeStokHargaNetto == 1) {
                $strMSHT = 'sk.tglstruk';
                $MetodeStokHargaNetto = 'FIFO';
            }
            if ($strMetodeStokHargaNetto == 2) {
                $strMSHT = 'sk.tglstruk desc';
                $MetodeStokHargaNetto = 'LIFO';
            }
            if ($strMetodeStokHargaNetto == 3) {
                $strMSHT = 'spd.tglkadaluarsa';
                $MetodeStokHargaNetto = 'FEFO';
            }
            if ($strMetodeStokHargaNetto == 4) {
                $strMSHT = 'spd.tglkadaluarsa desc';
                $MetodeStokHargaNetto = 'LEFO';
            }
            if ($strMetodeStokHargaNetto == 5) {
                $strMSHT = '';
                $MetodeStokHargaNetto = 'Summary';
            }
            $maxHarga = DB::select(DB::raw("select spd.tglpelayanan, $strHN as harga
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                where spd.koders = $idProfile and spd.produkidfk =:produkId "),
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
                sum(spd.qtyproduk) as qtyproduk,spd.ruanganidfk as objectruanganfk,ap.asalproduk,spd.tglkadaluarsa
                from transaksistoktr as spd
                inner JOIN strukpelayanantr as sk on sk.norec=spd.nostrukterimafk
                inner JOIN asalprodukmt as ap on ap.id=spd.asalprodukidfk
                where spd.koders = $idProfile and spd.produkidfk =:produkId and spd.ruanganidfk =:ruanganid and spd.qtyproduk > 0
                group by sk.norec,spd.produkidfk, $strMSHT, spd.asalprodukidfk,
                        spd.hargadiscount,
                spd.ruanganidfk,ap.asalproduk,spd.nostrukterimafk
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
                    'harganetto' => (float)$item->harganetto,
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

        $jmlstok = 0;
        foreach ($result as $item) {
            $jmlstok = $jmlstok + $item->qtyproduk;
        }


        $cekKekuatanSupranatural = DB::select(DB::raw("

            select pr.kekuatan,sdn.name as sediaan from pelayananmt as pr
            inner join sediaanmt as sdn on sdn.id=pr.sediaanidfk
            where pr.koders = $idProfile and pr.id=:produkfk;

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
            'sistemharganetto' => $SistemHargaNetto,
            'metodeambilharganetto' => $MetodeAmbilHargaNetto,
            'metodestokharganetto' => $MetodeStokHargaNetto,
            'message' => 'slvR',
        );
        return $this->respond($result);
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
}
