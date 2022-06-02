<?php


namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\ApiController;
use App\Master\Agama;
use App\Master\AsalProduk;
use App\Master\DetailJenisProduk;
use App\Master\Diagnosa;
use App\Master\DiagnosaTindakan;
use App\Master\Evaluasi;
use App\Master\Implementasi;
use App\Master\Instalasi;
use App\Master\Intervensi;
use App\Master\JadwalDokter;
use App\Master\JenisDiagnosa;
use App\Master\JenisKelamin;
use App\Master\JenisProduk;
use App\Master\JenisRacikan;
use App\Master\JenisRekanan;
use App\Master\Kamar;
use App\Master\KelompokPasien;
use App\Master\KelompokProduk;
use App\Master\MapKelompokPasientoPenjamin;
use App\Master\MapKelompokPasientoRekanan;
use App\Master\MapLoginUserToModulAplikasi;
use App\Master\MapLoginUsertoRuangan;
use App\Master\MapRuanganToKelas;
use App\Master\MapRuanganToProduk;
use App\Master\ModulAplikasi;
// use App\Master\Pendidikan;
use App\Master\Produk;
use App\Master\ProdukDetailLaboratorium;
use App\Master\ProdukDetailLaboratoriumNilaiNormal;
use App\Master\Rekanan;
use App\Master\Ruangan;
use App\Master\SatuanResep;
use App\Master\SatuanStandar;
use App\Master\SiklusGizi;
use App\Master\SlottingKiosk;
use App\Master\TempatTidur;
// use App\Master\StatusPerkawinan;
// use App\Master\DiagnosaKeperawatan;
use App\Master\WaktuLogin;
use App\Transaksi\LoggingUser;
// use App\Transaksi\MapRuanganToAdministrasi;
// use App\Transaksi\MapRuanganToAkomodasi;
// use App\Transaksi\PostingJurnal;
// use App\Transaksi\PostingJurnalTransaksi;
// use App\Transaksi\PostingJurnalTransaksiD;
// use App\Transaksi\StrukPlanning;
use App\Transaksi\RegistrasiPasien;
use App\Web\LoginUser;
use App\Web\Provinsi;
use App\Web\KotaKabupaten;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\Valet;
use DB;



use App\Transaksi\IdentifikasiPasien;
use App\Transaksi\KonversiSatuan;
use App\Transaksi\PasienDaftar;
use App\Transaksi\TarifPelayanan;
use App\Transaksi\TarifPelayananD;

class GeneralC extends ApiController
{
    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getTerbilangGeneral($number)
    {
        $terbilang = $this->makeTerbilang($number);
        return $this->respond(array('terbilang' => $terbilang));
    }

    public function getStatusClosePeriksa($noregistrasi, Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $data = RegistrasiPasien::where('noregistrasi', $noregistrasi)->where('koders', $kdProfile)->first();
        $status = false;
        $tgl = null;
        if (!empty($data) && $data->isclosing != null) {
            $status = $data->isclosing;
            $tgl = $data->tglclosing;
        }
        $result = array(
            'status' => $status,
            'tglclosing' => $tgl,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getTindakanWithDetail(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $detail = \DB::table('detailjenisprodukmt')
            ->select('id', 'detailjenisproduk')
            ->where('koders', $kdProfile)
            ->where('aktif', true)
            ->get();
        // $data = \DB::table('mappelayananruanganmt as mprs')
        //     ->join('pelayananmt as prd', 'prd.id', '=', 'mpr.produkidfk')

        //     ->select('mpr.produkidfk as id', 'prd.namaproduk', 'prd.detailjenisprodukidfk as objectdetailjenisprodukfk',
        //         'mpr.ruanganidfk as objectruanganfk',
        //         'prd.namaproduk'
        //     )
        //     ->where('mpr.koders', $kdProfile)
        //     ->where('mpr.ruanganidfk', $request['idRuangan'])
        //     ->where('mpr.aktif', true)
        //     ->where('prd.aktif', true)
        //     ->orderBy('prd.namaproduk', 'ASC')
        //     ->get();
        $data = DB::select(DB::raw("SELECT
      mpr.produkidfk AS id, prd.namaproduk as produk, prd.detailjenisprodukidfk AS objectdetailjenisprodukfk, mpr.ruanganidfk AS objectruanganfk, prd.namaproduk,hnp.hargasatuan 
  FROM
      mappelayananruanganmt AS mpr
      INNER JOIN pelayananmt AS prd ON prd.id = mpr.produkidfk
      INNER JOIN tarifpelayananmt AS hnp ON hnp.produkidfk = prd.id 
      INNER JOIN suratkeputusanmt AS sk ON hnp.suratkeputusanidfk = sk.id 
  WHERE
      mpr.koders = $kdProfile 
      AND mpr.ruanganidfk =  $request[idRuangan] 
      AND mpr.aktif = true
      AND prd.aktif = true 
      AND hnp.kelasidfk = $request[idKelas] 
      AND hnp.jenispelayananidfk = $request[idJenisPelayanan] 
      AND hnp.aktif = true 
      AND sk.aktif=true
  ORDER BY
      prd.namaproduk ASC"));
        $detail = $detail->toArray();
        // foreach ($detail as $key => $value) {

        //     $value->details = [];
        // }
        $i = 0;
        foreach ($detail as $value) {

            $value->details = [];
            foreach ($data as $value2) {

                $value2->selected = false;
                $harga_format = number_format((float)$value2->hargasatuan, 0, ",", ".");
                $value2->name = $value2->produk . ' | Rp.' . $harga_format;
                $value2->namaproduk = $value2->produk . ' | Rp.' . $harga_format;

                if ($detail[$i]->id == $value2->objectdetailjenisprodukfk) {
                    $detail[$i]->details[] = $value2; //array('namaproduk' =>  $value2->namaproduk, );
                }
            }
            $i++;
        }
        for ($i = count($detail) - 1; $i >= 0; $i--) {
            if (count($detail[$i]->details) == 0) {
                array_splice($detail, $i, 1);
            }
        }
        $result = array(
            'data' => $data,
            'details' => $detail,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getSiklusGizi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $siklusKe = $request['siklusKe'];
        $kelasId = $request['kelasId'];
        $jenisDietId = $request['jenisDietId'];
        $kategoryDietId = $request['kategoryDiet'];
        $jenisWaktuId = $request['jenisWaktuId'];
        $namaProduk = $request['namaProduk'];
        $row = $request['jmlRow'];
        $arrJenisWaktu = explode(',', $jenisWaktuId);

        //        return $this->respond($arrJenisWaktu);
        $data = DB::table('siklusgizimt as sm')
            ->join('pelayananmt as prd', 'prd.id', '=', 'sm.produkidfk')
            ->join('jeniswaktumt as jw', 'jw.id', '=', 'sm.jeniswaktuidfk')
            ->join('jenisdietmt as jd', 'jd.id', '=', 'sm.jenisdietidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'sm.kelasidfk')
            ->leftjoin('kategorydietmt as kd', 'kd.id', '=', 'sm.kategoryprodukidfk')
            //            ->leftjoin ('bentukproduk_m as bp','bp.id','=','sm.bentukprodukidfk')
            ->select(
                'sm.*',
                'prd.id as objectprodukfk',
                'prd.namaproduk',
                'jw.jeniswaktu',
                'jd.jenisdiet',
                'kls.namakelas',
                'kd.kategorydiet'
            )
            ->where('sm.koders', $kdProfile)
            ->where('sm.aktif', true);

        if (isset($siklusKe) && $siklusKe != '') {
            $data = $data->where('sm.sikluske', $siklusKe);
        }
        if (isset($kelasId) && $kelasId != '') {
            $data = $data->where('kls.id', $kelasId);
        }
        if (isset($jenisDietId) && $jenisDietId != '') {
            $data = $data->where('jd.id', $jenisDietId);
        }
        if (isset($kategoryDietId) && $kategoryDietId != '') {
            $data = $data->where('kd.id', $kategoryDietId);
        }
        //        if(isset($jenisWaktuId) && $jenisWaktuId!=''){
        //            $data = $data->where('jw.id',$jenisWaktuId);
        //        }
        if (isset($jenisWaktuId) && $jenisWaktuId != '') {
            $data = $data->whereIn('jw.id', $arrJenisWaktu);
        }
        if (isset($namaProduk) && $namaProduk != '') {
            $data = $data->where('prd.namaproduk', 'ilike', '%' . $namaProduk . '%');
        }

        if (isset($row) && $row != '') {
            $data = $data->limit($row);
        }
        $data = $data->get();
        $result = array(
            'data' => $data
        );
        return $this->respond($result);
    }
    public function getComboGizi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataJenisDiet = \DB::table('jenisdietmt as jd')
            ->select('jd.id', 'jd.jenisdiet')
            ->where('jd.aktif', true)
            ->get();

        $dataJenisWaktu = \DB::table('jeniswaktumt as jw')
            ->select('jw.id', 'jw.jeniswaktu')
            ->where('jw.aktif', true)
            ->get();

        $dataKategoryDiet = \DB::table('kategorydietmt as kd')
            ->select('kd.id', 'kd.kategorydiet')
            ->where('kd.aktif', true)
            ->get();
        $dataProduk = DB::table('pelayananmt as pr')
            ->JOIN('detailjenisprodukmt as djp', 'djp.id', '=', 'pr.detailjenisprodukidfk')
            //            ->leftJOIN('satuanstandarmt as ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->select('pr.id', 'pr.namaproduk', 'pr.namaproduk as name')
            ->where('pr.aktif', true)
            ->where('pr.koders', $kdProfile)
            ->where('djp.jenisprodukidfk', (int)$this->settingDataFixed('kdProdukGizi', $kdProfile));
        if (isset($request['namaproduk']) && $request['namaproduk'] != '') {
            $dataProduk = $dataProduk->where('pr.namaproduk', '%' . $request['namaproduk'] . '%');
        }
        $dataProduk = $dataProduk->orderBy('pr.namaproduk');
        $dataProduk = $dataProduk->get();

        $kelas = \DB::table('kelasmt as kd')
            ->select('kd.id', 'kd.namakelas')
            ->where('kd.aktif', true)
            ->get();
        $result = array(
            'jenisdiet' => $dataJenisDiet,
            'jeniswaktu' => $dataJenisWaktu,
            'kategorydiet' => $dataKategoryDiet,
            'produk' => $dataProduk,
            'kelas' => $kelas,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveSiklusGizi(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['details'] as $item) {
                $kode[] = (float) $item['produkfk'];
                $kelasfk[] = (float) $item['kelasfk'];
            }

            $hapus = SiklusGizi::where('aktif', true)
                ->where('koders', $kdProfile)
                ->where('sikluske', $request['sikluske'])
                ->where('jenisdietidfk', $request['objectjenisdietfk'])
                ->whereIn('kelasidfk', $kelasfk)
                ->where('jeniswaktuidfk', $request['objectjeniswaktufk'])
                ->whereIn('produkidfk', $kode)
                ->where('kategoryprodukidfk', $request['objectkategoryprodukfk'])
                ->where('bentukprodukidfk', $request['objectbentukprodukfk'])
                ->delete();

            foreach ($request['details'] as $item) {
                $SG = new SiklusGizi();
                $SG->id =  SiklusGizi::max('id') + 1;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->kodeexternal = 'GIZI';
                $SG->norec =  $SG->generateNewId();
                $SG->sikluske = $request['sikluske'];
                $SG->jeniswaktuidfk = $request['objectjeniswaktufk'];
                $SG->jenisdietidfk =  $request['objectjenisdietfk'];
                $SG->kelasidfk = $item['kelasfk'];
                $SG->produkidfk =  $item['produkfk'];
                $SG->bentukprodukidfk = $request['objectbentukprodukfk'];
                $SG->kategoryprodukidfk = $request['objectkategoryprodukfk'];
                $SG->save();
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
                "siklus" => $SG,
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
    public function deleteSiklusGizi(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $hapus = SiklusGizi::where('id', $request['id'])
                ->where('koders', $kdProfile)
                ->update(
                    ['aktif' => false]
                );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'er',
            );
        } else {
            $transMessage = "Hapus gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    protected function encryptSHA1($pass)
    {
        return sha1($pass);
    }
    public function saveNewUser(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['id'] == '') {
                $user = LoginUser::where('namauser', $request['namauser'])->where('kdprofile', $kdProfile)->get();
                if (count($user) > 0) {
                    $transMessage = 'User sudah ada';
                    $result = array(
                        "status" => 400,
                        "message" => 'Xoxo@PCURE'
                    );
                    return $this->setStatusCode($result['status'])->respond($result, $transMessage);
                }
                $new = new LoginUser();
                $new->id = LoginUser::max('id') + 1;
                $new->kdprofile = $kdProfile; //0;
                $new->statusenabled = true;
                $new->norec = $new->generateNewId();
            } else {
                $new = LoginUser::where('id', $request['id'])->where('kdprofile', $kdProfile)->first();
                $cekUser = LoginUser::where('namauser', $request['namauser'])
                    ->where('kdprofile', $kdProfile)
                    ->first();
                $sama = false;
                if (!empty($cekUser)) {
                    if ($cekUser->id != $request['id'] && $request['namauser'] == $cekUser->namauser) {
                        $sama = true;
                    }
                }
                if ($sama ==  true) {
                    $result = array(
                        "status" => 400,
                        "as" => '#Xoxo'
                    );
                    return $this->setStatusCode($result['status'])->respond($result, 'Nama User sudah ada');
                }
            }
            $new->namaexternal = $request['namauser'];
            $new->reportdisplay = $request['namauser'];
            $new->katasandi = $this->encryptSHA1($request['katasandi']);
            $new->objectkelompokuserfk = $request['objectkelompokuserfk'];
            $new->namauser = $request['namauser'];
            $new->objectpegawaifk = $request['objectpegawaifk'];
            $new->statuslogin = 0;
            $new->passcode = $this->encryptSHA1($request['katasandi']);
            $new->save();
            if (isset($request['waktuberakhir']) && $request['waktuberakhir'] != '') {
                $news = new WaktuLogin();
                $news->id = WaktuLogin::max('id') + 1;
                $news->kdprofile = $kdProfile;
                $news->statusenabled = true;
                $news->norec = $news->generateNewId();
                $news->loginuserfk = $new->id;
                $news->expired =  $request['waktuberakhir'];
                $news->status = '';
                $news->save();
            }
            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus == true) {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => 'Xoxo@PCURE'
            );
        } else {
            $transMessage = "Failed";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => 'Xoxo@PCURE'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getPegawaiPart(Request $request)
    {
        $req = $request->all();
        $kdProfile = (int) $this->getDataKdProfile($request);
        $datas = [];
        $data  = \DB::table('pegawaimt as st')
            ->select('st.id', 'st.namalengkap')
            ->where('st.aktif', true)
            ->where('st.koders', $kdProfile)
            ->orderBy('st.namalengkap');
        if (
            isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined"
        ) {
            $data = $data->where('st.namalengkap', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        if (isset($req['namalengkap']) && $req['namalengkap'] != "" && $req['namalengkap'] != "undefined") {
            $data = $data
                ->where('st.namalengkap', 'ilike', '%' . $req['namalengkap'] . '%');
        }
        if (isset($req['idPeg']) && $req['idPeg'] != "" && $req['idPeg'] != "undefined") {
            $data = $data
                ->where('st.id', '=', $req['idPeg']);
        }
        $data = $data->take(10);
        $data = $data->get();

        foreach ($data as $item) {
            $datas[]  = array(
                'id' => $item->id,
                'namalengkap' => $item->namalengkap,
            );
        }

        return $this->respond($datas);
    }
    public function getDaftarUser(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data  = \DB::table('loginuser_s as lo')
            ->join('pegawaimt as pg', 'lo.objectpegawaifk', '=', 'pg.id')
            ->join('kelompokusermt as kl', 'lo.objectkelompokuserfk', '=', 'kl.id')
            ->select(
                'lo.id',
                'lo.namauser',
                'lo.passcode as katakunci',
                'pg.namalengkap',
                'kl.kelompokuser',
                'lo.objectkelompokuserfk',
                'lo.objectpegawaifk'
            )
            ->where('lo.statusenabled', true)
            ->where('lo.kdprofile', $kdProfile)
            ->orderBy('lo.namauser');

        $data = $data->get();

        $result = array(
            "data" => $data,
            "message" => 'Xoxo@PCURE'
        );

        return $this->respond($result);
    }
    public function deleteNewUser(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        try {
            if ($request['id'] != '') {
                //                WaktuLogin::where('loginuserfk',$request['id'])
                //                    ->where('kdprofile', $kdProfile)
                //                    ->delete();
                LoginUser::where('id', $request['id'])
                    ->where('kdprofile', $kdProfile)
                    ->delete();
                //                    ->update([
                //                        'statusenabled' =>false
                //                    ]);
            }

            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus) {
            $transMessage = "Data Terhapus";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => 'Xoxo'
            );
        } else {
            $transMessage = "Failed";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => 'Xoxo'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getComboUser(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $kelUser = \DB::table('kelompokusermt as jd')
            ->select('jd.id', 'jd.kelompokuser')
            ->where('jd.aktif', true)
            ->where('jd.koders', $kdProfile)
            ->get();
        $ruangan = \DB::table('ruanganmt as jd')
            ->select('jd.id', 'jd.namaruangan')
            ->where('jd.aktif', true)
            ->where('jd.koders', $kdProfile)
            ->get();
        $profile = \DB::table('profile_m as jd')
            ->select('jd.id', 'jd.namalengkap')
            //            ->where('jd.aktif', true)
            //            ->where('jd.koders', $kdProfile)
            ->get();
        $modulAplikasi = ModulAplikasi::where('statusenabled', true)
            //            ->where('kdprofile', $kdProfile)
            ->select('id', 'modulaplikasi')
            ->where('reportdisplay', '=', 'Menu')
            ->get();
        $result = array(
            'kelompokuser' => $kelUser,
            'profile' => $profile,
            'modulaplikasi' => $modulAplikasi,
            'ruangan' => $ruangan,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }
    public function getObjekModulAplikasiStandar(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);

        //        $dataraw3 = [];
        //        $dataRaw = \DB::table('objekmodulaplikasi_s')
        ////            ->where('kdprofile', $kdProfile)
        //            ->where('statusenabled', true)
        //            ->select('id', 'kdobjekmodulaplikasihead', 'objekmodulaplikasi')
        ////			->whereBetween('id',[1450,1790])
        //            ->orderBy('id');
        //        $dataRaw = $dataRaw->get();
        //        foreach ($dataRaw as $dataRaw2) {
        //            if ($dataRaw2->kdobjekmodulaplikasihead == null) {
        //                $dataraw3[] = array(
        //                    'id' => $dataRaw2->id,
        //                    'parent_id' => 0,
        //                    'name' => $dataRaw2->objekmodulaplikasi,
        //                );
        //            } else if ($dataRaw2->kdobjekmodulaplikasihead != null ) {
        //                $dataraw3[] = array(
        //                    'id' => $dataRaw2->id,
        //                    'parent_id' => $dataRaw2->kdobjekmodulaplikasihead,
        //                    'name' => $dataRaw2->objekmodulaplikasi,
        //                );
        //            }
        //
        //        }
        //        $data = $dataraw3;
        //
        //        function recursiveElements($data)
        //        {
        //            $elements = [];
        //            $tree = [];
        //            foreach ($data as &$element) {
        //                $id = $element['id'];
        //                $parent_id = $element['parent_id'];
        //
        //                $elements[$id] = &$element;
        //                if (isset($elements[$parent_id])) {
        //                    $elements[$parent_id]['child'][] = &$element;
        //                } else {
        ////					if ($parent_id <= 10) {
        //                    $tree[] = &$element;
        ////					}
        //                }
        //            }
        //            return $tree;
        //        }
        //
        //        $data = recursiveElements($data);

        $modulAplikasi = ModulAplikasi::where('statusenabled', true)
            //            ->where('kdprofile', $kdProfile)
            ->where('reportdisplay', '=', 'Menu')->get();
        $result = array(
            //            'objekmodulaplikasi' => $data,
            'modulaplikasi' => $modulAplikasi
        );
        return $this->respond($result);
    }
    public function saveLoginUserToRuangan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['ruangan'] as $item) {
                $kode[] =  $item['ruanganid'];
            }

            $hapus = MapLoginUsertoRuangan::where('aktif', true)
                ->where('koders', $kdProfile)
                ->where('loginuseridfk', $request['loginid'])
                //                ->where('ruanganidfk',$kode)
                ->delete();

            foreach ($request['ruangan'] as $item) {
                $SG = new MapLoginUsertoRuangan();
                $SG->id =  MapLoginUsertoRuangan::max('id') + 1;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->kodeexternal = 'App';
                $SG->norec =  $SG->generateNewId();
                $SG->ruanganidfk = $item['ruanganid'];
                $SG->loginuseridfk = $request['loginid'];
                $SG->save();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Map Login User To Ruangan";
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
    public function saveMapUserToModul(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['details'] as $item) {
                $kode[] =  $item['objectmodulaplikasifk'];
            }

            $hapus = MapLoginUserToModulAplikasi::where('statusenabled', true)
                ->where('kdprofile', $kdProfile)
                ->where('objectloginuserfk', $request['objectloginuserfk'])
                //                ->where('objectmodulaplikasifk',$kode)
                ->delete();

            foreach ($request['details'] as $item) {
                $SG = new MapLoginUserToModulAplikasi();
                $SG->id =  MapLoginUserToModulAplikasi::max('id') + 1;
                $SG->kdprofile = $kdProfile;
                $SG->statusenabled = true;
                $SG->kodeexternal = 'App';
                $SG->norec =  $SG->generateNewId();
                $SG->objectmodulaplikasifk = $item['objectmodulaplikasifk'];
                $SG->objectloginuserfk = $request['objectloginuserfk'];
                $SG->save();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Map Login User To Modul Aplikasi";
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
    public function getMapUserToRuangan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = DB::table('maploginusertoruanganmt as mm')
            ->join('loginuser_s as lo', 'lo.id', '=', 'mm.loginuseridfk')
            ->select('mm.*', 'lo.namauser')
            ->where('mm.koders', $kdProfile)
            ->where('mm.loginuseridfk', $request['loginuseridfk'])
            ->where('mm.aktif', true)
            ->get();
        return $data;
    }
    public function getMapUserToModulAp(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = DB::table('maploginusertomodulaplikasi_s as mm')
            ->join('modulaplikasi_s as mo', 'mo.id', '=', 'mm.objectmodulaplikasifk')
            ->join('loginuser_s as lo', 'lo.id', '=', 'mm.objectloginuserfk')
            ->select('mm.*', 'lo.namauser', 'mo.modulaplikasi')
            ->where('mm.kdprofile', $kdProfile)
            ->where('mm.objectloginuserfk', $request['loginuseridfk'])
            ->where('mm.statusenabled', true)
            ->get();
        return $data;
    }
    public function getMapRuanganToProduk(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = DB::table('mappelayananruanganmt as mm')
            ->join('ruanganmt as mo', 'mo.id', '=', 'mm.ruanganidfk')
            ->join('instalasimt as in', 'in.id', '=', 'mo.instalasiidfk')
            ->join('pelayananmt as lo', 'lo.id', '=', 'mm.produkidfk')
            ->select('mm.*', 'lo.namaproduk', 'mo.namaruangan', 'in.namadepartemen')
            ->where('mm.koders', $kdProfile)
            ->where('mm.aktif', true);
        if (isset($request['instId']) && $request['instId'] != '') {
            $data = $data->where('mo.instalasiidfk', $request['instId']);
        }
        if (isset($request['ruangId']) && $request['ruangId'] != '') {
            $data = $data->where('mo.id', $request['ruangId']);
        }
        if (isset($request['produk']) && $request['produk'] != '') {
            $data = $data->where('lo.namaproduk', 'ilike', '%' . $request['produk'] . '%');
        }
        if (isset($request['limit']) && $request['limit'] != '') {
            $data = $data->take($request['limit']);
        }
        $data = $data->get();
        return $data;
    }
    public function getMapRuCombo(Request $request)
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
        $set = explode(',', $this->settingDataFixed('kdJenisProdukBukanPelayanan', $kdProfile));
        $kode = [];
        foreach ($set as $item) {
            $kode[] = (int)$item;
        }
        $produk = \DB::table('pelayananmt as ru')
            ->join('detailjenisprodukmt  as djp', 'djp.id', '=', 'ru.detailjenisprodukidfk')
            ->select('ru.id', 'ru.namaproduk', 'ru.namaproduk as name')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->whereNotIn('djp.jenisprodukidfk', $kode)
            ->orderBy('ru.namaproduk')
            ->get();

        $paket= DB::table('paketmt')
        ->select('id','namapaket')
        ->where('aktif',true)    
        ->where('koders', (int)$kdProfile)
        ->orderBy('namapaket')
        ->get();
        $result = array(
            'departemen' => $dataDepartemen,
            'produk' => $produk,
            'paket' => $paket,
            'message' => 'er@epc',
        );

        return $this->respond($result);
    }
    public function deleteMapRuanganProduk(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $hapus = MapRuanganToProduk::where('id', $request['id'])
                ->where('koders', $kdProfile)
                ->update(
                    ['aktif' => false]
                );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus Map Ruangan To Pelayanan";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'er',
            );
        } else {
            $transMessage = "Hapus gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'er',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function saveMapRuanganProduk(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['details'] as $item) {
                $kode[] =  $item['produkidfk'];
            }

            $hapus = MapRuanganToProduk::where('aktif', true)
                ->where('koders', $kdProfile)
                ->where('ruanganidfk', $request['ruanganidfk'])
                //                ->where('produkidfk',$kode)
                ->delete();

            foreach ($request['details'] as $item) {
                $SG = new MapRuanganToProduk();
                $SG->id =  MapRuanganToProduk::max('id') + 1;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->kodeexternal = 'App';
                $SG->norec =  $SG->generateNewId();
                $SG->produkidfk = $item['produkidfk'];
                $SG->ruanganidfk = $request['ruanganidfk'];
                $SG->save();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Map Ruangan To Pelayanan";
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

    public function getListProduk(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $filter = $request->all();
        //get data
        $data = \DB::table('pelayananmt as pr')
            ->leftJoin('rekananmt AS rk', 'rk.id', '=', 'pr.rekananidfk')
            ->leftJoin('instalasimt AS dp', 'dp.id', '=', 'pr.departemenidfk')
            ->leftJoin('detailjenisprodukmt AS dj', 'dj.id', '=', 'pr.detailjenisprodukidfk')
            ->leftJoin('jenisprodukmt AS jp', 'jp.id', '=', 'dj.jenisprodukidfk')
            ->leftJoin('kelompokprodukmt AS kp', 'kp.id', '=', 'jp.kelompokprodukidfk')
            ->leftJoin('kelompokusermt AS keluser', 'keluser.id', '=', 'pr.kelompokuseridfk')
            ->leftJoin('satuanstandarmt AS ss', 'ss.id', '=', 'pr.satuanstandaridfk')
            ->leftJoin('sediaanmt AS sd', 'sd.id', '=', 'pr.sediaanidfk')
            ->leftJoin('golongandarahmt AS gd', 'gd.id', '=', 'pr.golongandarahidfk')
            ->leftJoin('rhesusmt AS rh', 'rh.id', '=', 'pr.rhesusidfk')
            ->select(\DB::raw("
                   pr.id,CASE WHEN pr.aktif = 't' THEN 'Aktif' ELSE 'Tidak Aktif' END AS aktif,pr.namaproduk,pr.kdproduk_intern,pr.kodebmn,pr.keterangan,
                   pr.kelompokuseridfk,keluser.kelompokuser,pr.detailjenisprodukidfk,dj.detailjenisproduk,dj.jenisprodukidfk,jp.jenisproduk,jp.kelompokprodukidfk,kp.kelompokproduk,
                   pr.satuanstandaridfk,ss.satuanstandar,pr.sediaanidfk,sd.sediaan,CASE WHEN pr.kekuatan IS NULL THEN '1' ELSE pr.kekuatan END AS kekuatan,pr.deskripsiproduk,pr.spesifikasi,
                   CASE WHEN pr.isfornas = 't' THEN 'Aktif' ELSE 'Tidak Aktif' END AS isfornas,CASE WHEN pr.isantibiotik = 't' THEN 'Aktif' ELSE 'Tidak Aktif' END AS isantibiotik,
                   CASE WHEN pr.ispsikotropika = 't' THEN 'Aktif' ELSE 'Tidak Aktif' END AS ispsikotropika,CASE WHEN pr.isgeneric = 't' THEN 'Aktif' ELSE 'Tidak Aktif' END AS isgeneric,
                   pr.qtykalori,pr.qtykarbohidrat,pr.qtylemak,pr.qtyporsi,pr.qtyprotein,
                   pr.nilainormal,pr.golongandarahidfk,gd.golongandarah,pr.rhesusidfk,rh.rhesus
                 
            "))
            ->where('pr.koders', $kdProfile);

        if (isset($filter['kdProduk']) && !empty($filter['kdProduk'])) {
            $data = $data->where('pr.id', '=', $filter['kdProduk']);
        } elseif (isset($filter['nmProduk']) && !empty($filter['nmProduk'])) {
            $data = $data->where('pr.namaproduk', 'ilike', '%' . $filter['nmProduk'] . '%');
        }
        if (isset($filter['kelUser']) && !empty($filter['kelUser'])) {
            if ($filter['kelUser'] != '-' && $filter['kelUser'] != 'admin' && $filter['kelUser'] != 'it') {
                $data = $data->where('keluser.kelompokuser', '=', $filter['kelUser']);
            }
        }
        if (isset($filter['status']) && !empty($filter['status'])) {
            $data = $data->where('pr.aktif', '=', $filter['status']);
        }
        if (isset($filter['StatusProduk']) && !empty($filter['StatusProduk'])) {
            $data = $data->where('pr.objectstatusprodukfk', '=', $filter['StatusProduk']);
        }
        if (isset($filter['Generik']) && !empty($filter['Generik'])) {
            $data = $data->where('pr.isgeneric', '=', $filter['Generik']);
        }
        if (isset($filter['Fornas']) && !empty($filter['Fornas'])) {
            $data = $data->where('pr.isfornas', '=', $filter['Fornas']);
        }
        if (isset($filter['Retriksi']) && !empty($filter['Retriksi'])) {
            $data = $data->where('pr.isretriksi', '=', $filter['Retriksi']);
        }
        if (isset($filter['jmlRetriksi']) && !empty($filter['jmlRetriksi'])) {
            $data = $data->where('pr.jmlretriksi', '=', $filter['jmlRetriksi']);
        }
        if (isset($filter['antiBiotik']) && !empty($filter['antiBiotik'])) {
            $data = $data->where('pr.isantibiotik', '=', $filter['antiBiotik']);
        }
        if (isset($filter['detailJenisProduk']) && !empty($filter['detailJenisProduk'])) {
            $data = $data->where('pr.objectdetailjenisprodukfk', '=', $filter['detailJenisProduk']);
        }
        if (isset($filter['jenisProduk']) && !empty($filter['jenisProduk'])) {
            $data = $data->where('dj.objectjenisprodukfk', '=', $filter['jenisProduk']);
        }
        if (isset($filter['KelompokProduk']) && !empty($filter['KelompokProduk'])) {
            $data = $data->where('jp.objectkelompokprodukfk', '=', $filter['KelompokProduk']);
        }
        $data = $data->orderBy('pr.namaproduk');
        //        $data = $data->take($filter['jmlRows']);
        //        $data = $data->take(50);
        $data = $data->get();
        return $this->respond($data);
    }

    public function deleteMasterPelayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $Pesan = "";
        try {
            if ($request['id'] != '') {
                Produk::where('id', $request['id'])
                    ->where('koders', $kdProfile)
                    ->update([
                        'aktif' => 'false'
                    ]);
            } else {
                $Pesan = "Data Tidak Ditemukan";
            }

            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus) {
            $transMessage = "Data Terhapus";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => 'Xoxo'
            );
        } else {
            $transMessage = "Hapus Gagal, " . $Pesan;
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => 'Xoxo'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboMaster(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        //        $kelompokproduk = \DB::table('kelompokprodukmt')
        //            ->select('id','kelompokproduk')
        //            ->where('aktif', true)
        //            ->get();
        //
        //        $jenisproduk = \DB::table('jenisprodukmt')
        //            ->select('id','kelompokprodukidfk','jenisproduk')
        //            ->where('aktif', true)
        //            ->get();
        //
        //        $detailjenisProduk = DB::table('detailjenisprodukmt')
        //            ->select('id','jenisprodukidfk','detailjenisproduk')
        //            ->where('aktif', true)
        //            ->get();

        $kelompokUser = \DB::table('kelompokusermt as kl')
            ->select('kl.id', 'kl.kelompokuser')
            ->where('kl.aktif', true)
            ->orderBy('kl.kelompokuser')
            ->get();

        $Sediaan = \DB::table('sediaanmt AS kl')
            ->select('kl.id', 'kl.sediaan')
            ->where('kl.aktif', true)
            ->orderBy('kl.sediaan')
            ->get();

        $Satuan = \DB::table('satuanstandarmt AS kl')
            ->select('kl.id', 'kl.satuanstandar')
            ->where('kl.aktif', true)
            ->orderBy('kl.satuanstandar')
            ->get();

        $GolDarah = \DB::table('golongandarahmt AS kl')
            ->select('kl.id', 'kl.golongandarah')
            ->where('kl.aktif', true)
            ->orderBy('kl.golongandarah')
            ->get();

        $Rhesus = \DB::table('rhesusmt AS kl')
            ->select('kl.id', 'kl.rhesus')
            ->where('kl.aktif', true)
            ->orderBy('kl.rhesus')
            ->get();

        $data = array(
            //            "kelompokproduk" => $kelompokproduk,
            //            "jenisproduk" => $jenisproduk,
            //            "detailjenisproduk" => $detailjenisProduk,
            "kelompokuser" => $kelompokUser,
            "sediaan" => $Sediaan,
            "satuanstandar" => $Satuan,
            "goloangandarah" => $GolDarah,
            "rhesus" => $Rhesus
        );
        return $this->respond($data);
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

    public function saveMasterPelayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        try {
            if ($request['id'] == '-') {
                $Prod = new Produk();
                $Prod->id =  Produk::max('id') + 1;
                $Prod->norec = $Prod->generateNewId();
                $Prod->koders = $kdProfile;
             
            } else {
                $Prod = Produk::where('id', $request['id'])->first();
            }
            $Prod->aktif =  $request['aktif'];
            $Prod->namaproduk = $request['namaproduk'];
            $Prod->kdproduk_intern = $request['kdproduk_intern'];
            $Prod->kodebmn = $request['kodebmn'];
            $Prod->keterangan = $request['keterangan'];
            $Prod->kelompokuseridfk = $request['kelompokuser'];
            $Prod->detailjenisprodukidfk = $request['objectdetailjenisprodukfk'];
            $Prod->satuanstandaridfk = $request['objectsatuanstandarfk'];
            $Prod->sediaanidfk = $request['objectsediaanfk'];
            $Prod->kekuatan = $request['kekuatan'];
            $Prod->deskripsiproduk = $request['deskripsiproduk'];
            $Prod->spesifikasi = $request['spesifikasi'];
            $Prod->isfornas = $request['isfornas'];
            $Prod->isantibiotik = $request['isantibiotik'];
            $Prod->ispsikotropika = $request['ispsikotropika'];
            $Prod->isgeneric = $request['isgeneric'];
            $Prod->qtykalori = $request['qtykalori'];
            $Prod->qtykarbohidrat = $request['qtykarbohidrat'];
            $Prod->qtylemak = $request['qtylemak'];
            $Prod->qtyporsi = $request['qtyporsi'];
            $Prod->qtyprotein = $request['qtyprotein'];
            $Prod->jenisperiksaidfk = $request['objectjenisperiksafk'];
            $Prod->jenisperiksapenunjangidfk = $request['objectjenisperiksapenunjangfk'];
            $Prod->nilainormal = $request['nilainormal'];
            $Prod->bahansampleidfk = $request['bahansamplefk'];
            $Prod->golongandarahidfk = $request['golongandarahfk'];
            $Prod->rhesusidfk = $request['rhesusfk'];
            $Prod->save();

            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus) {
            $transMessage = "Data Master Tersimpan";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $Prod,
                "message" => 'Xoxo'
            );
        } else {
            $transMessage = "Simpan Gagal!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => 'Xoxo'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboJadwalDokter(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $KdinstalasiPoli = (int) $this->settingDataFixed('kdInstalasiPoli', $kdProfile);
        $KdjenisPegawaiDokter = (int) $this->settingDataFixed('kdJenisPegawaiDokter', $kdProfile);
        $RuanganPoli = \DB::table('ruanganmt as jd')
            ->select('jd.id', 'jd.namaruangan')
            ->where('jd.aktif', true)
            ->where('jd.instalasiidfk', $KdinstalasiPoli)
            ->get();

        $dataDokter = \DB::table('pegawaimt as jw')
            ->select('jw.id', 'jw.namalengkap')
            ->where('jw.aktif', true)
            ->where('jw.objectjenispegawaifk', $KdjenisPegawaiDokter)
            ->get();

        $dataHari = \DB::table('harimt as kd')
            ->select('kd.id', 'kd.hari')
            ->where('kd.aktif', true)
            ->get();

        $result = array(
            'poli' => $RuanganPoli,
            'dokter' => $dataDokter,
            'hari' => $dataHari,
            'message' => 'kh@PCURE',
        );
        return $this->respond($result);
    }

    public function saveInformasiDokter(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();
        $jeniLog = "";
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->join('pegawaimt as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select('lu.objectpegawaifk', 'pg.namalengkap')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', $kdProfile)
            ->first();

        try {

            if ($request['idjadwal'] == '') {
                $jeniLog = "Simpan Informasi jadwal Dokter ";
                $dataJadwalDokter = new JadwalDokter();
                $idDK = JadwalDokter::max('id');
                $dataJadwalDokter->koders = $kdProfile;
                $dataJadwalDokter->aktif = true;
                $dataJadwalDokter->id = $idDK + 1;
                $dataJadwalDokter->pegawaiidfk = $request['objectpegawaifk'];
                $dataJadwalDokter->tglinput = $tglAyeuna;
            } else {
                $dataJadwalDokter =  JadwalDokter::where('id', $request['idjadwal'])->where('koders', $kdProfile)->first();

                $jeniLog = "Ubah Informasi jadwal Dokter ";
            }
            $dataJadwalDokter->pegawaiidfk = $request['objectpegawaifk'];
            $dataJadwalDokter->ruanganidfk = $request['objectruanganfk'];
            $dataJadwalDokter->hari = $request['hari'];
            $dataJadwalDokter->jammulai = $request['jammulai'];
            $dataJadwalDokter->jamakhir = $request['jamakhir'];
            $dataJadwalDokter->keterangan = $request['keterangan'];
            $dataJadwalDokter->save();
            $idPegawai = $dataJadwalDokter->id;

            //## Logging User
            $newId = LoggingUser::max('id');
            $newId = $newId + 1;
            $logUser = new LoggingUser();
            $logUser->id = $newId;
            $logUser->norec = $logUser->generateNewId();
            $logUser->koders = $kdProfile;
            $logUser->aktif = true;
            $logUser->jenislog = $jeniLog;
            $logUser->noreff = $idPegawai;
            $logUser->referensi = 'id Informasi Jadwal Dokter';
            $logUser->loginuserfk = $dataLogin['userData']['id'];
            $logUser->tanggal = $tglAyeuna;
            $logUser->keterangan = $jeniLog . $dataPegawai->namalengkap;
            $logUser->save();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Simpan Gagal";
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'kh@PCURE',
            );
        } else {
            $transMessage = "Simpan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "as" => 'kh@PCURE',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getJadwalDokter(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $filter = $request->all();

        $data = \DB::table('jadwaldoktermt as jd')
            ->join('ruanganmt AS ru', 'ru.id', '=', 'jd.ruanganidfk')
            ->join('pegawaimt as pg', 'pg.id', '=', 'jd.pegawaiidfk')
            ->select(DB::raw("jd.id as idjadwalpegawai,jd.ruanganidfk,ru.namaruangan,
                              jd.pegawaiidfk,pg.namalengkap,pg.nosip,pg.nostr,pg.noidentitas as nik,
                              jd.jammulai,jd.jamakhir,jd.keterangan, jd.hari"))
            ->where('jd.koders', $kdProfile)
            ->where('ru.aktif', true)
            ->where('pg.aktif', true)
            ->where('jd.aktif', true);

        if (isset($request['dokterId']) && $request['dokterId'] != "" && $request['dokterId'] != "undefined") {
            $data = $data->where('pg.id', '=', $request['dokterId']);
        }
        if (isset($request['ruanganId']) && $request['ruanganId'] != "" && $request['ruanganId'] != "undefined") {
            $data = $data->where('ru.id', '=', $request['ruanganId']);
        }
        if (isset($request['nik']) && $request['nik'] != "" && $request['nik'] != "undefined") {
            $data = $data->where('pg.noidentitas', '=', $request['nik']);
        }
        if (isset($request['nostr']) && $request['nostr'] != "" && $request['nostr'] != "undefined") {
            $data = $data->where('pg.nostr', '=', $request['nostr']);
        }

        $data = $data->orderBy('pg.namalengkap', 'asc');
        $data = $data->get();
        $result = array(
            'data' => $data,
            'message' => 'kh@PCURE',
        );
        return $this->respond($result);
    }

    public function deleteInformasiDokter(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->join('pegawaimt as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select('lu.objectpegawaifk', 'pg.namalengkap')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', $kdProfile)
            ->first();

        try {

            $dataJadwalDokter =  JadwalDokter::where('id', $request['idJadwal'])
                ->where('koders', $kdProfile)
                ->where('pegawaiidfk', $request['objectpegawaifk'])
                ->update([
                    "aktif" => 'f',
                ]);

            //## Logging User
            $newId = LoggingUser::max('id');
            $newId = $newId + 1;
            $logUser = new LoggingUser();
            $logUser->id = $newId;
            $logUser->norec = $logUser->generateNewId();
            $logUser->koders = $kdProfile;
            $logUser->aktif = true;
            $logUser->jenislog = 'Delete Informasi jadwal Dokter ';
            $logUser->noreff = $request['idJadwal'];
            $logUser->referensi = 'id Informasi Jadwal Dokter';
            $logUser->loginuserfk =  $dataLogin['userData']['id'];
            $logUser->tanggal = $tglAyeuna;
            $logUser->keterangan = 'Hapus Informasi jadwal Dokter' . $dataPegawai->namalengkap;
            $logUser->save();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Simpan Gagal";
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'kh@PCURE',
            );
        } else {
            $transMessage = "Simpan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "as" => 'kh@PCURE',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboInformasi(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenLayanan', $kdProfile));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item) {
            $kdDepartemenRawatJalan[] = (int)$item;
        }
        $RuanganPoli = \DB::table('ruanganmt as jd')
            ->select('jd.id', 'jd.namaruangan')
            ->where('jd.aktif', true)
            ->wherein('jd.instalasiidfk', $kdDepartemenRawatJalan)
            ->get();

        $dataJenisPelayanan = \DB::table('jenispelayananmt as jw')
            ->select('jw.id', 'jw.jenispelayanan')
            ->where('jw.aktif', true)
            ->get();

        $dataKelas = \DB::table('kelasmt as kd')
            ->select('kd.id', 'kd.namakelas')
            ->where('kd.aktif', true)
            ->get();

        $result = array(
            'poli' => $RuanganPoli,
            'jenispelayanan' => $dataJenisPelayanan,
            'kelas' => $dataKelas,
            'message' => 'kh@PCURE',
        );
        return $this->respond($result);
    }

    public function  getProdukGeneralPart(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $req = $request->all();
        $dataProd = \DB::table('pelayananmt as dg')
            ->select('dg.id', 'dg.namaproduk')
            ->where('dg.koders', $kdProfile)
            ->where('dg.aktif', true)
            ->orderBy('dg.namaproduk');

        if (
            isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined"
        ) {
            $dataProd = $dataProd
                ->where('dg.namaproduk', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        }
        if (isset($req['namaproduk']) && $req['namaproduk'] != "" && $req['namaproduk'] != "undefined") {
            $dataProd = $dataProd
                ->where('dg.namaproduk', 'ilike', '%' . $req['namaproduk'] . '%');
        }

        $dataProd = $dataProd->take(10);
        $dataProd = $dataProd->get();

        return $this->respond($dataProd);
    }

    public function getDaftarTarif(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $idKelas = $this->settingDataFixed('KdListKelas', $kdProfile);
        $filter = $request->all();
        $produkid = '';
        if ($filter['produkId'] != '') {
            $produkid = 'AND pr.id = ' . $filter['produkId'];
        }
        $ruanganid = '';
        if ($filter['ruanganId'] != '') {
            $ruanganid = 'AND mprtp.ruanganidfk =' . $filter['ruanganId'];
        }
        $kelasid = '';
        if ($filter['kelasId'] != '') {
            $kelasid = 'AND kls.id =' . $filter['kelasId'];
        }
        $jenispelid = '';
        if ($filter['jenispelayananId'] != '') {
            $jenispelid = 'AND jnsp.id =' . $filter['jenispelayananId'];
        }
        $namaproduk = '';
        if ($filter['namaproduk'] != '') {
            $namaproduk = "AND pr.namaproduk ilike '%" . $filter['namaproduk'] . "%'";
        }

        $data = DB::select(
            DB::raw("
                SELECT distinct pr.id,pr.namaproduk,hrpk.harganetto1 AS hargalayanan,kls.id as idkelas,kls.namakelas,
                jnsp.id as jenispelayananid,jnsp.jenispelayanan,mprtp.ruanganidfk as ruid,ru.id as ruid,
                ru.namaruangan,sk.namask
                FROM pelayananmt AS pr
                INNER JOIN mappelayananruanganmt AS mprtp ON mprtp.produkidfk = pr.id            
                LEFT JOIN tarifpelayananmt AS hrpk ON hrpk.produkidfk = pr.id
                INNER JOIN kelasmt as kls on kls.id=hrpk.kelasidfk
                INNER JOIN jenispelayananmt as jnsp on jnsp.id=hrpk.jenispelayananidfk
                INNER JOIN ruanganmt as ru on ru.id=mprtp.ruanganidfk
                INNER JOIN suratkeputusanmt as sk on sk.id=hrpk.suratkeputusanidfk
                WHERE pr.koders = $kdProfile and              
                hrpk.aktif = true
                AND pr.aktif = true      
                AND sk.aktif = true              
                AND hrpk.kelasidfk IN ($idKelas)
                $produkid
                $ruanganid 
                $kelasid 
                $jenispelid 
                $namaproduk
                limit 50
             ")
        );

        $result = array(
            'data' => $data,
            'message' => 'ea@PCURE',
        );
        return $this->respond($result);
    }

    public function getHargaPelayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('tarifpelayananmt as tr')
            ->join('pelayananmt as pr', 'pr.id', '=', 'tr.produkidfk')
            ->join('kelasmt as kl', 'kl.id', '=', 'tr.kelasidfk')
            ->join('suratkeputusanmt as sk', 'sk.id', '=', 'tr.suratkeputusanidfk')
            ->leftjoin('asalprodukmt as asl', 'asl.id', '=', 'tr.asalprodukidfk')

            ->join('jenispelayananmt as jp', 'jp.id', '=', 'tr.jenispelayananidfk')
            ->select(\DB::raw("tr.id,tr.aktif, pr.namaproduk,kl.namakelas,jp.jenispelayanan,tr.hargasatuan,tr.asalprodukidfk,asl.asalproduk,
            tr.produkidfk,tr.kelasidfk,tr.jenispelayananidfk,sk.namask,tr.suratkeputusanidfk"))
            ->where('tr.koders', $kdProfile)
            ->where('pr.aktif', true)
            // ->where('sk.aktif', true)
            ->where('tr.aktif', true);

        if (isset($request['namaproduk']) && !empty($request['namaproduk'])) {
            $data = $data->where('pr.namaproduk', 'ilike', '%' . $request['namaproduk'] . '%');
        }
        if (isset($request['klsid']) && !empty($request['klsid'])) {
            $data = $data->where('kl.id', '=', $request['klsid']);
        }
        if (isset($request['jpid']) && !empty($request['jpid'])) {
            $data = $data->where('jp.id', '=', $request['jpid']);
        }
        if (isset($request['skid']) && !empty($request['skid'])) {
            $data = $data->where('tr.suratkeputusanidfk', '=', $request['skid']);
        }
        if (isset($request['rows']) && !empty($request['rows'])) {
            $data = $data->limit($request['rows']);
        }

        $data = $data->orderBy('pr.namaproduk');
        $data = $data->get();


        $data2 = \DB::table('tarifpelayanandmt as tr')
            ->join('pelayananmt as pr', 'pr.id', '=', 'tr.produkidfk')
            ->join('kelasmt as kl', 'kl.id', '=', 'tr.kelasidfk')
            ->join('komponentarifmt as kom', 'kom.id', '=', 'tr.komponenhargaidfk')
            ->join('suratkeputusanmt as sk', 'sk.id', '=', 'tr.suratkeputusanidfk')
            ->join('jenispelayananmt as jp', 'jp.id', '=', 'tr.jenispelayananidfk')
            ->select(\DB::raw("tr.id,tr.aktif, pr.namaproduk,kl.namakelas,jp.jenispelayanan,tr.hargasatuan,kom.komponentarif,
            tr.produkidfk,tr.kelasidfk,tr.jenispelayananidfk,tr.komponenhargaidfk,tr.suratkeputusanidfk"))
            ->where('tr.koders', $kdProfile)
            ->where('pr.aktif', true)
            // ->where('sk.aktif', true)
            ->where('tr.aktif', true);

        if (isset($request['namaproduk']) && !empty($request['namaproduk'])) {
            $data2 = $data2->where('pr.namaproduk', 'ilike', '%' . $request['namaproduk'] . '%');
        }
        if (isset($request['klsid']) && !empty($request['klsid'])) {
            $data2 = $data2->where('kl.id', '=', $request['klsid']);
        }
        if (isset($request['jpid']) && !empty($request['jpid'])) {
            $data2 = $data2->where('jp.id', '=', $request['jpid']);
        }

        $data2 = $data2->get();

        foreach ($data as $d) {
            $d->details = [];
            $d->norecs = $d->id;
            foreach ($data2 as $d2) {
                if ($d->produkidfk == $d2->produkidfk &&  $d->kelasidfk == $d2->kelasidfk &&  $d->jenispelayananidfk == $d2->jenispelayananidfk 
                && $d->suratkeputusanidfk == $d2->suratkeputusanidfk) {
                    $d2->norecs = $d->norecs;
                    $d->details[] = $d2;
                }
            }
        }

        return $this->respond($data);
    }
    public function getComboTarif(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $kelas = \DB::table('kelasmt as jd')
            ->select('jd.id', 'jd.namakelas')
            ->where('jd.aktif', true)
            ->get();

        $jenis = \DB::table('jenispelayananmt as jw')
            ->select('jw.id', 'jw.jenispelayanan')
            ->where('jw.aktif', true)
            ->get();

        $asal = \DB::table('asalprodukmt as jw')
            ->select('jw.id', 'jw.asalproduk')
            ->where('jw.aktif', true)
            ->get();

        $komponentarif = \DB::table('komponentarifmt as jw')
            ->select('jw.id', 'jw.komponentarif')
            ->where('jw.aktif', true)
            ->get();
        $sk = \DB::table('suratkeputusanmt as jw')
            ->select('*')
            // ->where('jw.aktif', true)
            ->get();


        $result = array(
            'komponentarif' => $komponentarif,
            'asalproduk' => $asal,
            'jenis' => $jenis,
            'kelas' => $kelas,
            'sk' => $sk,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function saveHargaPelayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['id'] == '') {
                $new = new TarifPelayanan();
                $new->id = TarifPelayanan::max('id') + 1;
                $new->aktif = true;
                $new->koders = $kdProfile;
            } else {
                $new = TarifPelayanan::where('id', $request['id'])->first();
                TarifPelayananD::where('produkidfk', $request['produkidfk'])
                    ->where('asalprodukidfk', $request['asalprodukidfk'])
                    ->where('kelasidfk', $request['kelasidfk'])
                    ->where('jenispelayananidfk', $request['jenispelayananidfk'])
                    ->where('suratkeputusanidfk', $request['suratkeputusanidfk'])
                    ->delete();
            }
            $new->kodeexternal = 'APP';
            $new->asalprodukidfk = $request['asalprodukidfk'];
            $new->kelasidfk = $request['kelasidfk'];
            $new->produkidfk = $request['produkidfk'];
            $new->jenispelayananidfk = $request['jenispelayananidfk'];
            $new->suratkeputusanidfk = $request['suratkeputusanidfk'];
            $new->hargasatuan = $request['hargasatuan'];
            $new->save();

            foreach ($request['details'] as $item) {
                $SG = new TarifPelayananD();
                $SG->id =  TarifPelayananD::max('id') + 1;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->kodeexternal = 'APP';
                $SG->asalprodukidfk = $request['asalprodukidfk'];
                $SG->hargasatuan = $item['hargasatuan'];
                $SG->kelasidfk = $request['kelasidfk'];
                $SG->komponenhargaidfk = $item['komponenhargaidfk'];
                $SG->produkidfk = $request['produkidfk'];
                $SG->jenispelayananidfk = $request['jenispelayananidfk'];
                $SG->tarifpelayananidfk = $new->id;
                $SG->suratkeputusanidfk = $request['suratkeputusanidfk'];
                $SG->save();
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
                "data" => $SG,
                "as" => 'Xoxo',

            );
        } else {
            $transMessage = "Simpan gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'Xoxo',
                "e" => $e->getMessage() . ' Line :' . $e->getLine(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function hapusHargaPelayanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {

            $dar =  TarifPelayanan::where('id', $request['id'])->first();
            TarifPelayananD::where('produkidfk', $request['produkidfk'])
                ->where('asalprodukidfk', $request['asalprodukidfk'])
                ->where('kelasidfk', $request['kelasidfk'])
                ->where('jenispelayananidfk', $request['jenispelayananidfk'])
                ->where('hargasatuan', $dar->hargasatuan)
                ->where('koders', $kdProfile)
                ->delete();
            TarifPelayanan::where('id', $request['id'])->delete();
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'Xoxo',

            );
        } else {
            $transMessage = "Hapus gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'Xoxo',
                "e" => $e->getMessage() . ' Line :' . $e->getLine(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getProduks(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $produk = \DB::table('pelayananmt as jd')
            ->select('jd.id', 'jd.namaproduk')
            ->where('jd.aktif', true)
            ->where('jd.koders', $kdProfile);
        if (isset($request['namaproduk']) && $request['namaproduk'] != '') {
            $produk = $produk->where('jd.namaproduk', 'ilike', '%' . $request['namaproduk'] . '%');
        }

        $produk = $produk->orderBy('jd.namaproduk');
        $produk = $produk->limit(20);
        $produk = $produk->get();

        return $this->respond($produk);
    }

    public function getKelompokProd(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);

        $kel = \DB::table('kelompokprodukmt as dp')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.kelompokproduk')
            ->get();

        $jen = \DB::table('jenisprodukmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.jenisproduk')
            ->get();
        $dataz = [];
        foreach ($kel as $item) {
            $detail = [];
            foreach ($jen as $item2) {
                if ($item->id == $item2->kelompokprodukidfk) {
                    $detail[] = array(
                        'id' => $item2->id,
                        'jenisproduk' => $item2->jenisproduk,
                    );
                }
            }

            $dataz[] = array(
                'id' => $item->id,
                'kelompokproduk' => $item->kelompokproduk,
                'jenisproduk' => $detail,
            );
        }
        $res['jenisproduk'] = $jen;
        $res['kelompok'] = $dataz;
        return response()->json($res);
    }

    public function getMasterData(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        if ($request['table'] == 'ruanganmt') {
            $data =  DB::table('ruanganmt as ru')
                ->join('instalasimt as ins', 'ru.instalasiidfk', '=', 'ins.id')
                ->select('ru.*', 'ins.namadepartemen')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.namaruangan');
        }
        if ($request['table'] == 'instalasimt') {
            $data =  DB::table('instalasimt as ru')
                ->select('ru.*')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.namadepartemen');
        }
        if ($request['table'] == 'slottingkioskmt') {
            $data =  DB::table('slottingkioskmt as ru')
                ->join('ruanganmt as ss', 'ss.id', '=', 'ru.ruanganidfk')
                ->select('ru.*', 'ss.namaruangan')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ss.namaruangan');
        }
        if ($request['table'] == 'detailjenisprodukmt') {
            $data =  DB::table('detailjenisprodukmt as ru')
                ->join('jenisprodukmt as ss', 'ss.id', '=', 'ru.jenisprodukidfk')
                ->join('kelompokprodukmt as kp', 'kp.id', '=', 'ss.kelompokprodukidfk')
                ->select('ru.*', 'ss.jenisproduk', 'ss.kelompokprodukidfk', 'kp.kelompokproduk')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.detailjenisproduk');
        }
        if ($request['table'] == 'jenisprodukmt') {
            $data =  DB::table('jenisprodukmt as ss')
                ->join('kelompokprodukmt as kp', 'kp.id', '=', 'ss.kelompokprodukidfk')
                ->select('ss.*', 'kp.kelompokproduk')
                ->where('ss.koders', $kdProfile)
                ->orderBy('ss.jenisproduk');
        }
        if ($request['table'] == 'kelompokprodukmt') {
            $data =  DB::table('kelompokprodukmt as ru')
                ->select('ru.*')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.kelompokproduk');
        }
        if ($request['table'] == 'kamarmt') {
            $data =  DB::table('kamarmt as kmr')
                ->join('ruanganmt as ru', 'ru.id', '=', 'kmr.ruanganidfk')
                ->join('kelasmt as kls', 'kls.id', '=', 'kmr.kelasidfk')
                ->select('kmr.*', 'ru.namaruangan', 'kls.namakelas')
                ->where('kmr.koders', $kdProfile)
                ->where('ru.aktif', true)
                ->where('kls.aktif', true)
                ->orderBy('kmr.namakamar');
        }
        if ($request['table'] == 'tempattidurmt') {
            $data =  DB::table('tempattidurmt as tt')
                ->join('kamarmt as kmr', 'kmr.id', '=', 'tt.kamaridfk')
                ->join('ruanganmt as ru', 'ru.id', '=', 'kmr.ruanganidfk')
                ->join('statusbedmt as st', 'st.id', '=', 'tt.statusbedidfk')
                ->select('tt.*', 'ru.namaruangan', 'kmr.namakamar', 'st.statusbed', 'kmr.ruanganidfk')
                ->where('tt.koders', $kdProfile)
                ->where('ru.aktif', true)
                ->where('kmr.aktif', true)
                ->where('st.aktif', true)
                ->orderByRaw('ru.namaruangan asc,tt.nomorbed asc');
        }
        if ($request['table'] == 'kelasmt') {
            $data =  DB::table('kelasmt as tt')

                ->select('tt.id', 'tt.namakelas')
                ->where('tt.koders', $kdProfile)
                ->where('tt.aktif', true)
                ->orderBy('tt.namakelas');
        }
        if ($request['table'] == 'kelompokpasienmt') {
            $data =  DB::table('kelompokpasienmt as tt')
                ->select('tt.id', 'tt.kelompokpasien', 'tt.aktif', 'tt.kelompokpasien AS name')
                ->where('tt.koders', $kdProfile)
                ->where('tt.aktif', true)
                ->orderBy('tt.kelompokpasien');
        }
        if ($request['table'] == 'jenisrekananmt') {
            $data =  DB::table('jenisrekananmt as tt')
                ->select('tt.id', 'tt.jenisrekanan', 'tt.aktif', 'tt.jenisrekanan AS name')
                ->where('tt.koders', $kdProfile)
                ->where('tt.aktif', true)
                ->orderBy('tt.jenisrekanan');
        }
        if ($request['table'] == 'rekananmt') {
            $data =  DB::table('rekananmt as tt')
                ->leftjoin('jenisrekananmt as jr', 'jr.id', '=', 'tt.jenisrekananidfk')
                ->select('tt.id', 'tt.namarekanan', 'tt.aktif', 'tt.alamatlengkap', 'tt.email', 'tt.telepon', 'tt.jenisrekananidfk', 'jr.jenisrekanan', 'tt.namarekanan AS name')
                ->where('tt.koders', $kdProfile)
                ->where('tt.aktif', true)
                ->orderBy('tt.namarekanan');
        }
        if ($request['table'] == 'asalprodukmt') {
            $data =  DB::table('asalprodukmt as ru')
                ->select('ru.id', 'ru.aktif', 'ru.asalproduk', 'ru.asalproduk AS name')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.asalproduk');
        }
        if ($request['table'] == 'satuanstandarmt') {
            $data =  DB::table('satuanstandarmt as ru')
                ->select('ru.id', 'ru.aktif', 'ru.satuanstandar', 'ru.satuanstandar AS name')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.satuanstandar');
        }
        if ($request['table'] == 'satuanresepmt') {
            $data =  DB::table('satuanresepmt as ru')
                ->select('ru.id', 'ru.aktif', 'ru.satuanresep', 'ru.satuanresep AS name')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.satuanresep');
        }
        if ($request['table'] == 'jenisracikanmt') {
            $data =  DB::table('jenisracikanmt as ru')
                ->select('ru.id', 'ru.aktif', 'ru.jenisracikan', 'ru.jasaracikan', 'ru.jenisracikan AS name')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.jenisracikan');
        }
        if ($request['table'] == 'jenisdiagnosamt') {
            $data =  DB::table('jenisdiagnosamt as ru')
                ->select('ru.id', 'ru.aktif', 'ru.jenisdiagnosa', 'ru.jenisdiagnosa AS name')
                ->where('ru.koders', $kdProfile)
                ->orderBy('ru.jenisdiagnosa');
        }

        if ($request['table'] == 'icdxmt') {
            $data =  DB::table('icdxmt as ru')
                ->select('ru.id', 'ru.aktif', 'ru.kddiagnosa', 'ru.namadiagnosa', 'ru.namadiagnosa AS name')
                ->where('ru.koders', $kdProfile)
                ->where('ru.aktif', true)
                ->orderBy('ru.namadiagnosa');
        }

        if ($request['table'] == 'icdixmt') {
            $data =  DB::table('icdixmt as ru')
                ->select('ru.id', 'ru.aktif', 'ru.kddiagnosa', 'ru.namadiagnosa', 'ru.diagnosatindakan', 'ru.namadiagnosa AS name')
                ->where('ru.koders', $kdProfile)
                ->where('ru.aktif', true)
                ->orderBy('ru.namadiagnosa');
        }

        $data = $data->get();

        $result = array(
            "data" => $data,
            "message" => 'Xoxo'
        );

        return $this->respond($result);
    }
    public function saveMaster(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['table'] == 'ruanganmt') {
                if ($request['id'] == '') {
                    $id = Ruangan::max('id') + 1;
                    $new = new Ruangan();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->qruangan = $id;
                } else {
                    $new = Ruangan::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->namaexternal = 'APP';
                $new->kodesiranap = $request['kodesiranap'];
                $new->kdinternal = $request['kdinternal'];
                $new->namaruangan = $request['namaruangan'];
                $new->instalasiidfk = $request['instalasiidfk'];
                $new->noruangan = $request['noruangan'];
                $new->save();
            }
            if ($request['table'] == 'instalasimt') {
                if ($request['id'] == '') {
                    $id = Instalasi::max('id') + 1;
                    $new = new Instalasi();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                } else {
                    $new = Instalasi::where('id', $request['id'])->first();
                }
                $new->aktif =  $request['aktif'];;
                $new->namaexternal = 'APP';
                $new->namadepartemen = $request['namadepartemen'];


                $new->save();
            }
            if ($request['table'] == 'slottingkioskmt') {
                if ($request['id'] == '') {
                    $id = SlottingKiosk::max('id') + 1;
                    $new = new SlottingKiosk();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                } else {
                    $new = SlottingKiosk::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->ruanganidfk = $request['ruanganidfk'];
                $new->jambuka = $request['jambuka'];
                $new->jamtutup = $request['jamtutup'];
                $new->quota = $request['quota'];
                $new->hari = $request['hari'];
                $new->save();
            }

            if ($request['table'] == 'detailjenisprodukmt') {
                if ($request['id'] == '') {
                    $id = DetailJenisProduk::max('id') + 1;
                    $new = new DetailJenisProduk();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->qdetailjenisproduk = $id;
                    $new->norec = $new->generateNewId();
                } else {
                    $new = DetailJenisProduk::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                // $new->kelompokprodukidfk = $request['kelompokprodukidfk'];
                $new->jenisprodukidfk = $request['jenisprodukidfk'];
                $new->detailjenisproduk = $request['detailjenisproduk'];

                $new->save();
            }

            if ($request['table'] == 'jenisprodukmt') {
                if ($request['id'] == '') {
                    $id = JenisProduk::max('id') + 1;
                    $new = new JenisProduk();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->qjenisproduk = $id;
                    $new->norec = $new->generateNewId();
                } else {
                    $new = JenisProduk::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->kelompokprodukidfk = $request['kelompokprodukidfk'];
                $new->jenisproduk = $request['jenisproduk'];
                $new->save();
            }
            if ($request['table'] == 'kelompokprodukmt') {
                if ($request['id'] == '') {
                    $id = KelompokProduk::max('id') + 1;
                    $new = new KelompokProduk();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    // $new->qkelompokproduk =$id; 
                } else {
                    $new = KelompokProduk::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->kelompokproduk = $request['kelompokproduk'];
                $new->save();
            }
            if ($request['table'] == 'kamarmt') {
                if ($request['id'] == '') {
                    $id = Kamar::max('id') + 1;
                    $new = new Kamar();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                } else {
                    $new = Kamar::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['namakamar'];
                $new->kelasidfk = $request['kelasidfk'];
                $new->ruanganidfk = $request['ruanganidfk'];
                $new->namakamar = $request['namakamar'];
                $new->qtybed = $request['qtybed'];
                $new->keterangan = $request['keterangan'];
                $new->save();
            }
            if ($request['table'] == 'tempattidurmt') {
                if ($request['id'] == '') {
                    $id = TempatTidur::max('id') + 1;
                    $new = new TempatTidur();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                } else {
                    $new = TempatTidur::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['nomorbed'];
                $new->kamaridfk = $request['kamaridfk'];
                $new->statusbedidfk = $request['statusbedidfk'];
                $new->nomorbed = $request['nomorbed'];
                $new->save();
            }
            if ($request['table'] == 'kelompokpasienmt') {
                if ($request['id'] == '') {
                    $id = KelompokPasien::max('id') + 1;
                    $new = new KelompokPasien();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->kdkelompokpasien = $id;
                } else {
                    $new = KelompokPasien::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['tipepasien'];
                $new->kelompokpasien = $request['tipepasien'];
                $new->save();
            }
            if ($request['table'] == 'kelompokpasienmt') {
                if ($request['id'] == '') {
                    $id = KelompokPasien::max('id') + 1;
                    $new = new KelompokPasien();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->kdkelompokpasien = $id;
                } else {
                    $new = KelompokPasien::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['tipepasien'];
                $new->kelompokpasien = $request['tipepasien'];
                $new->save();
            }
            if ($request['table'] == 'jenisrekananmt') {
                if ($request['id'] == '') {
                    $id = JenisRekanan::max('id') + 1;
                    $new = new JenisRekanan();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->kdkelompokpasien = $id;
                } else {
                    $new = JenisRekanan::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['jenisrekanan'];
                $new->jenisrekanan = $request['jenisrekanan'];
                $new->save();
            }
            if ($request['table'] == 'rekananmt') {
                if ($request['id'] == '') {
                    $id = Rekanan::max('id') + 1;
                    $new = new Rekanan();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                } else {
                    $new = Rekanan::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['namarekanan'];
                $new->namarekanan = $request['namarekanan'];
                $new->jenisrekananidfk = $request['jenisrekananidfk'];
                $new->alamatlengkap = $request['alamatlengkap'];
                $new->email = $request['email'];
                $new->telepon = $request['telepon'];
                $new->save();
            }

            if ($request['table'] == 'asalprodukmt') {
                if ($request['id'] == '') {
                    $id = AsalProduk::max('id') + 1;
                    $new = new AsalProduk();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->kdkelompokpasien = $id;
                } else {
                    $new = AsalProduk::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['asalproduk'];
                $new->asalproduk = $request['asalproduk'];
                $new->save();
            }

            if ($request['table'] == 'satuanstandarmt') {
                if ($request['id'] == '') {
                    $id = SatuanStandar::max('id') + 1;
                    $new = new SatuanStandar();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->qsatuanstandar = 0;
                } else {
                    $new = SatuanStandar::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['satuanstandar'];
                $new->satuanstandar = $request['satuanstandar'];
                $new->save();
            }

            if ($request['table'] == 'satuanresepmt') {
                if ($request['id'] == '') {
                    $id = SatuanResep::max('id') + 1;
                    $new = new SatuanResep();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->qsatuanresep = 0;
                    $new->kdsatuanresep = $id;
                } else {
                    $new = SatuanResep::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['satuanresep'];
                $new->satuanresep = $request['satuanresep'];
                $new->save();
            }

            if ($request['table'] == 'jenisracikanmt') {
                if ($request['id'] == '') {
                    $id = JenisRacikan::max('id') + 1;
                    $new = new JenisRacikan();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->kdjenisracikan = $id;
                } else {
                    $new = JenisRacikan::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['jenisracikan'];
                $new->jenisracikan = $request['jenisracikan'];
                $new->jasaracikan = $request['jasaracikan'];
                $new->save();
            }

            if ($request['table'] == 'jenisdiagnosamt') {
                if ($request['id'] == '') {
                    $id = JenisDiagnosa::max('id') + 1;
                    $new = new JenisDiagnosa();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();
                    $new->kdjenisdiagnosa = $id;
                } else {
                    $new = JenisDiagnosa::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->reportdisplay = $request['jenisdiagnosa'];
                $new->jenisdiagnosa = $request['jenisdiagnosa'];
                $new->save();
            }

            if ($request['table'] == 'icdxmt') {
                if ($request['id'] == '') {
                    $id = Diagnosa::max('id') + 1;
                    $new = new Diagnosa();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();                    
                } else {
                    $new = Diagnosa::where('id', $request['id'])->first();
                }
                $new->aktif =  $request['aktif'];
                $new->kddiagnosa = $request['kddiagnosa'];
                $new->namadiagnosa = $request['namadiagnosa'];
                $new->save();
            }

            if ($request['table'] == 'icdixmt') {
                if ($request['id'] == '') {
                    $id = DiagnosaTindakan::max('id') + 1;
                    $new = new DiagnosaTindakan();
                    $new->id = $id;
                    $new->koders = $kdProfile;
                    $new->norec = $new->generateNewId();                  
                } else {
                    $new = DiagnosaTindakan::where('id', $request['id'])->first();
                }

                $new->aktif =  $request['aktif'];
                $new->kddiagnosa = $request['kddiagnosa'];
                $new->diagnosatindakan = $request['namadiagnosa'];
                $new->namadiagnosa = $request['namadiagnosa']; 
                $new->save();
            }


            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus == true) {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => 'Xoxo@QWERTYUIOP'
            );
        } else {
            $transMessage = "Gagal Simpan";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "message" => 'Xoxo@QWERTYUIOP'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboRuru(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);

        $kelas = \DB::table('kelasmt as dp')
            ->select('dp.id', 'dp.namakelas')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namakelas')
            ->get();

        $statsu = \DB::table('statusbedmt as dp')
            ->select('dp.id', 'dp.statusbed')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.statusbed')
            ->get();

        $ruanganmt = \DB::table('ruanganmt as dp')
            ->select('dp.id', 'dp.namaruangan')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namaruangan')
            ->get();


        $kmr = \DB::table('kamarmt as dp')
            ->select('dp.id', 'dp.namakamar', 'ruanganidfk')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namakamar')
            ->get();

        $dataz = [];
        foreach ($ruanganmt as $item) {
            $detail = [];
            foreach ($kmr as $item2) {
                if ($item->id == $item2->ruanganidfk) {
                    $detail[] = array(
                        'id' => $item2->id,
                        'namakamar' => $item2->namakamar,
                    );
                }
            }

            $dataz[] = array(
                'id' => $item->id,
                'namaruangan' => $item->namaruangan,
                'kamar' => $detail,
            );
        }
        $res['kelas'] = $kelas;
        $res['statusbed'] = $statsu;
        $res['ruangan'] = $dataz;
        return response()->json($res);
    }

    public function getComboMasterRekanan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);

        $dataJRKN = JenisRekanan::where('aktif', true)
            ->select('id', 'jenisrekanan')
            ->get();
        $dataRKN = Rekanan::where('aktif', true)
            ->select('id', 'namarekanan')
            ->get();

        $result = array(
            'jenisrekanan' => $dataJRKN,
            'rekanan' => $dataRKN,
            'message' => 'erea',
        );

        return $this->respond($result);
    }

    public function getMapRuCombo2(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        $dataInstalasi = \DB::table('instalasimt as dp')
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namadepartemen')
            ->get();

        $dataRuangan = \DB::table('ruanganmt as ru')
            ->select('id', 'namaruangan', 'instalasiidfk', 'namaruangan as name')
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
        $kelas = \DB::table('kelasmt as ru')
            ->where('ru.aktif', true)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namakelas')
            ->get();
        $result = array(
            'departemen' => $dataDepartemen,
            'ruangan' => $dataRuangan,
            'kelas' => $kelas,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getMapRuanganToKelas(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = DB::table('mapruangantokelasmt as mm')
            ->join('ruanganmt as mo', 'mo.id', '=', 'mm.ruanganidfk')
            ->join('instalasimt as in', 'in.id', '=', 'mo.instalasiidfk')
            ->join('kelasmt as lo', 'lo.id', '=', 'mm.kelasidfk')
            ->select('mm.*', 'lo.namakelas', 'mo.namaruangan', 'in.namadepartemen')
            ->where('mm.koders', $kdProfile)
            ->where('mm.aktif', true);
        if (isset($request['instId']) && $request['instId'] != '') {
            $data = $data->where('mo.instalasiidfk', $request['instId']);
        }
        if (isset($request['ruangId']) && $request['ruangId'] != '') {
            $data = $data->where('mo.id', $request['ruangId']);
        }
        if (isset($request['kelasId']) && $request['kelasId'] != '') {
            $data = $data->where('mm.kelasidfk', $request['kelasId']);
        }
        if (isset($request['produk']) && $request['produk'] != '') {
            $data = $data->where('lo.namakelas', 'ilike', '%' . $request['produk'] . '%');
        }
        if (isset($request['limit']) && $request['limit'] != '') {
            $data = $data->take($request['limit']);
        }
        $data = $data->get();
        return $data;
    }

    public function deleteMapRutoKelas(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $hapus = MapRuanganToKelas::where('id', $request['id'])
                ->where('koders', $kdProfile)
                ->update(
                    ['aktif' => false]
                );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus Map Ruangan To Kelas";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'Xoxo',
            );
        } else {
            $transMessage = "Hapus gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function saveMapRutoKelas(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['details'] as $item) {
                $kode[] =  $item['ruanganidfk'];
            }

            $hapus = MapRuanganToKelas::where('aktif', true)
                ->where('koders', $kdProfile)
                ->where('kelasidfk', $request['kelasidfk'])
                // ->where('ruanganidfk',$kode)
                ->delete();

            foreach ($request['details'] as $item) {
                $SG = new MapRuanganToKelas();
                $SG->id =  MapRuanganToKelas::max('id') + 1;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->kodeexternal = 'App';
                $SG->norec =  $SG->generateNewId();
                $SG->ruanganidfk = $item['ruanganidfk'];
                $SG->kelasidfk = $request['kelasidfk'];
                $SG->save();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Map Ruangan To Kelas";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $SG,
                "as" => 'Xoxo',

            );
        } else {
            $transMessage = "Simpan gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'Xoxo',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function getMapRekananToTipePasien(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $data = DB::table('mapkelompokpasientorekananmt as mm')
            ->join('kelompokpasienmt as mo', 'mo.id', '=', 'mm.kelompokpasienidfk')
            ->join('rekananmt as rkn', 'rkn.id', '=', 'mm.rekananidfk')
            ->select('mm.*', 'mo.kelompokpasien', 'rkn.namarekanan')
            ->where('mm.koders', $kdProfile)
            ->where('mm.aktif', true);

        if (isset($request['rekananId']) && $request['rekananId'] != '') {
            $data = $data->where('mm.rekananidfk', $request['rekananId']);
        }
        if (isset($request['KelompokPasienId']) && $request['KelompokPasienId'] != '') {
            $data = $data->where('mm.kelompokpasienidfk', $request['KelompokPasienId']);
        }
        if (isset($request['rekanan']) && $request['rekanan'] != '') {
            $data = $data->where('rkn.namarekanan', 'ilike', '%' . $request['rekanan'] . '%');
        }
        if (isset($request['limit']) && $request['limit'] != '') {
            $data = $data->take($request['limit']);
        }
        $data = $data->get();
        return $data;
    }

    public function deleteMapRekananToTipePasien(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $hapus = MapKelompokPasientoRekanan::where('id', $request['id'])
                ->where('koders', $kdProfile)
                ->update(
                    ['aktif' => false]
                );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus Map Ruangan To Kelas";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'Xoxo',
            );
        } else {
            $transMessage = "Hapus gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
    public function saveMapRekananToTipePasien(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            foreach ($request['details'] as $item) {
                $kode[] =  $item['rekananidfk'];
            }

            $hapus = MapKelompokPasientoRekanan::where('aktif', true)
                ->where('koders', $kdProfile)
                ->where('rekananidfk', $request['rekananidfk'])
                // ->where('ruanganidfk',$kode)
                ->delete();

            foreach ($request['details'] as $item) {
                $SG = new MapKelompokPasientoRekanan();
                $SG->id =  MapKelompokPasientoRekanan::max('id') + 1;
                $SG->koders = $kdProfile;
                $SG->aktif = true;
                $SG->kodeexternal = 'App';
                $SG->norec =  $SG->generateNewId();
                $SG->rekananidfk = $item['rekananidfk'];
                $SG->kelompokpasienidfk = $request['kelompokpasienidfk'];
                $SG->save();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Map Tipe Pasien To Rekanan";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $SG,
                "as" => 'Xoxo',

            );
        } else {
            $transMessage = "Simpan gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => 'Xoxo',
                "e" => $e->getMessage(),
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboKonversi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $detailJenisProdukTindakan = explode(',', $this->settingDataFixed('kdDetailJenisProdukTindakan', $kdProfile));
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

        $dataSatuan = \DB::table('satuanstandarmt')
            ->select('id', 'satuanstandar')
            ->where('aktif', true)
            ->get();


        $result = array(
            'kelompokproduk' => $dataKelompokProduk,
            'satuanstandar' => $dataSatuan,
            'message' => 'erea',
        );

        return $this->respond($result);
    }

    public function getDataProdukKonversi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        $paramNamaProduk = "";
        if (isset($request['namaProduk']) && $request['namaProduk'] != "" && $request['namaProduk'] != "undefined") {
            $paramNamaProduk = " AND pr.namaproduk ILIKE " . "'%" . $request['namaProduk'] . "%'";
        }
        $paramsKelProduk = "";
        if (isset($request['kelProduk']) && $request['kelProduk'] != "" && $request['kelProduk'] != "undefined") {
            $paramsKelProduk = " AND jp.kelompokprodukidfk = " . $request['kelProduk'];
        }
        $paramKdProduk = "";
        if (isset($request['idProduk']) && $request['idProduk'] != "" && $request['idProduk'] != "undefined") {
            $paramKdProduk = " AND pr.id = " . $request['idProduk'];
        }

        $details = collect(\DB::select("
            SELECT ks.norec,pr.id,pr.id AS produkfk,ks.produkidfk,pr.namaproduk,ks.satuanstandar_asal,
                    ssa.satuanstandar AS satuanasal,ks.satuanstandar_tujuan,sst.satuanstandar AS satuantujuan,
                    ks.nilaikonversi,ks.kelompokprodukidfk,kp.kelompokproduk
            FROM konversisatuantr AS ks
            INNER JOIN pelayananmt AS pr ON pr.id = ks.produkidfk
            LEFT JOIN satuanstandarmt AS ssa ON ssa.id = ks.satuanstandar_asal                
            LEFT JOIN satuanstandarmt AS sst ON sst.id = ks.satuanstandar_tujuan
            LEFT JOIN kelompokprodukmt AS kp ON kp.id = ks.kelompokprodukidfk
            WHERE ks.koders = $kdProfile AND ks.aktif = true
            $paramNamaProduk            
            $paramKdProduk
        "));

        $result = array(
            'data' => $details,
            'message' => 'HK',
        );
        return $this->respond($result);
    }

    public function getDataSatuanKonversi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProduk = $request['idProduk'];
        $details = collect(\DB::select("
            SELECT ks.norec,pr.id AS produkidfk,pr.namaproduk,ks.satuanstandar_asal,ssa.satuanstandar AS satuanasal,
                   ks.satuanstandar_tujuan,sst.satuanstandar AS satuantujuan,ks.nilaikonversi,pr.satuanstandaridfk,ss.satuanstandar
            FROM pelayananmt AS pr
            LEFT JOIN konversisatuantr AS ks ON ks.produkidfk = pr.id            
            LEFT JOIN satuanstandarmt AS ssa ON ssa.id = ks.satuanstandar_asal                
            LEFT JOIN satuanstandarmt AS sst ON sst.id = ks.satuanstandar_tujuan                                
            LEFT JOIN satuanstandarmt AS ss ON ss.id = pr.satuanstandaridfk
            WHERE pr.koders = $kdProfile AND pr.aktif = true
            AND pr.id = '$idProduk'
        "));

        return $this->respond($details);
    }

    public function SaveKonversiSatuan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        //## KartuStok
        \DB::beginTransaction();
        try {

            if ($request['norec'] == '') {
                $newKS = new KonversiSatuan();
                $norecKS = $newKS->generateNewId();
                $newKS->koders = $kdProfile;
                $newKS->aktif = true;
                $newKS->norec = $norecKS;
            } else {
                $newKS = KonversiSatuan::where('norec', $request['norec'])->where('koders', $kdProfile)->first();
                $norecKS = $request['norec'];
            }
            $newKS->nilaikonversi = $request['nilaikonversi'];
            $newKS->produkidfk = $request['produkfk'];
            $newKS->satuanstandar_asal = $request['satuanstandar_asal'];
            $newKS->satuanstandar_tujuan = $request['satuanstandar_tujuan'];
            $newKS->kelompokprodukidfk = $request['kelompokprodukidfk'];
            $newKS->save();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Simpan Konversi Satuan";
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Konversi Satuan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "noresep" => $newKS,
                "as" => 'QWERTYUIOP',
            );
        } else {
            $transMessage = "Simpan Konversi Satuan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "noresep" => $newKS,
                "as" => 'QWERTYUIOP',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function hapusKonversiSatuan(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        \DB::beginTransaction();
        try {

            $newKS = KonversiSatuan::where('norec', $request['norec'])->where('koders', $kdProfile)->delete();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Hapus Konversi Satuan";
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus Konversi Satuan Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "noresep" => $newKS,
                "as" => 'QWERTYUIOP',
            );
        } else {
            $transMessage = "Hapus Konversi Satuan Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "noresep" => $newKS,
                "as" => 'QWERTYUIOP',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getMapRknToTp(Request $request)
{
        $kdProfile = $this->getDataKdProfile($request);

        $dataRekanan = \DB::table('rekananmt as dp')
            ->select('dp.id','dp.namarekanan','dp.namarekanan as name')      
            ->where('dp.aktif', true)
            ->where('dp.koders', (int)$kdProfile)
            ->orderBy('dp.namarekanan')
            ->get();

        $dataKelompokPasien = \DB::table('kelompokpasienmt as ru')
        ->select('ru.id','ru.kelompokpasien')
        ->where('ru.aktif', true)
        ->where('ru.koders', (int)$kdProfile)
        ->orderBy('ru.kelompokpasien')
        ->get();

        $result = array(
            'rekanan' => $dataRekanan,            
            'kelas' => $dataKelompokPasien,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getComboMasterDiagnosa(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);

        $dataJRKN = JenisDiagnosa::where('aktif', true)
            ->select('id', 'jenisdiagnosa')
            ->get();
        // $dataRKN = Rekanan::where('aktif', true)
        //     ->select('id', 'namarekanan')
        //     ->get();

        $result = array(
            'jenisdiagnosa' => $dataJRKN,
            // 'rekanan' => $dataRKN,
            'message' => 'erea',
        );

        return $this->respond($result);
    }
}
