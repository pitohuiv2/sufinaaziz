<?php

namespace App\Http\Controllers\Registrasi;

use App\Http\Controllers\ApiController;
use App\Master\Agama;
use App\Master\Alamat;
use App\Master\AsuransiPasien;
use App\Master\JenisKelamin;
use App\Master\Pasien;
use App\Master\PasienTriase;
use App\Master\Pendidikan;
use App\Master\RunningNumber;
use App\Master\StatusPerkawinan;
use App\Master\TempatTidur;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\PerjanjianPasien;
use App\Transaksi\BatalRegistrasi;
use App\Transaksi\DiagnosaPasien;
use App\Transaksi\DiagnosaTindakanPasien;
use App\Transaksi\LoggingUser;
use App\Transaksi\RegistrasiPasien;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\TransaksiPasienDetail;
use App\Transaksi\PemakaianAsuransi;
use Illuminate\Http\Request;
use App\Traits\PelayananPasienTrait;
use DB;
use App\Traits\Valet;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\This;

class RegistrasiC extends ApiController
{
    use Valet, PelayananPasienTrait;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getDaftarPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('pasienmt as ps')
            ->leftjoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->select(
                'ps.norm as nocm',
                'ps.namapasien',
                'ps.tgllahir',
                'jk.jeniskelamin',
                'ps.noidentitas',
                'alm.alamatlengkap',
                'ps.id as nocmfk',
                'ps.namaayah',
                'ps.notelepon',
                'ps.nohp',
                'ps.tglmeninggal',
                'ps.iskompleks',
                'ps.nobpjs' //,
                //                'ps.foto'
                ,
                DB::raw('case when ps.foto is null then null else \'ada\' end as photo')
            );
        if (isset($request['tglLahir']) && $request['tglLahir'] != "" && $request['tglLahir'] != "undefined") {
            $data = $data->where('ps.tgllahir', '>=', $request['tglLahir'] . ' 00:00');
        };
        if (isset($request['tglLahir']) && $request['tglLahir'] != "" && $request['tglLahir'] != "undefined") {
            $data = $data->where('ps.tgllahir', '<=', $request['tglLahir'] . ' 23:59');
        };
        if (isset($request['norm']) && $request['norm'] != "" && $request['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['norm'] . '%');
        };
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['namaPasien'] . '%');
        };
        if (isset($request['alamat']) && $request['alamat'] != "" && $request['alamat'] != "undefined") {
            $data = $data->where('alm.alamatlengkap', 'ilike', '%' . $request['alamat'] . '%');
        };

        if (isset($request['namaAyah']) && $request['namaAyah'] != "" && $request['namaAyah'] != "undefined") {
            $data = $data->where('ps.namaayah', 'ilike', '%' . $request['namaAyah'] . '%');
        };
        if (isset($request['nik']) && $request['nik'] != "" && $request['nik'] != "undefined") {
            $data = $data->where('ps.noidentitas', '=', $request['nik']);
        };
        if (isset($request['bpjs']) && $request['bpjs'] != "" && $request['bpjs'] != "undefined") {
            $data = $data->where('ps.nobpjs', '=', $request['bpjs']);
        };

        $totalRow = $data->count();
        if (isset($request['rows']) && $request['rows'] != "" && $request['rows'] != "undefined") {
            $data = $data->limit(($request['rows']));
        };
        if (isset($request['page']) && $request['page'] != "" && $request['page'] != "undefined") {
            $data = $data->offset(($request['page']));
        };
        if ((isset($request['sortfield']) && $request['sortfield'] != '' && $request['sortfield'] != 'undefined')
            && (isset($request['sortorder']) && $request['sortorder'] != '' && $request['sortorder'] != 'undefined')
        ) {
            $or = 'asc';
            if ($request['sortorder'] == -1) {
                $or = 'desc';
            }
            $data = $data->orderby($request['sortfield'], $or);
        } else {
            $data = $data->orderby('ps.id', 'desc');
        }

        $data = $data->where('ps.koders', (int)$kdProfile);
        $data = $data->where('ps.aktif', true);
        $data = $data->get();
        $data2 = [];
        foreach ($data as $item) {
            $data2[] = array(
                'nocm' => $item->nocm,
                'namapasien' => $item->namapasien,
                'jeniskelamin' => $item->jeniskelamin,
                'noidentitas' => $item->noidentitas,
                'alamatlengkap' => $item->alamatlengkap,
                'nocmfk' => $item->nocmfk,
                'namaayah' => $item->namaayah,
                'notelepon' => $item->notelepon,
                'nohp' => $item->nohp,
                'tglmeninggal' => $item->tglmeninggal,
                'iskompleks' => $item->iskompleks,
            );
        };
        $result = array(
            'daftar' => $data,
            'totalRow' => $totalRow,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getComboRegBaru(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->join('pegawaimt as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select('lu.objectpegawaifk', 'pg.namalengkap')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', $kdProfile)
            ->first();

        $jk = JenisKelamin::where('aktif', true)
            ->select(DB::raw("id, UPPER(jeniskelamin) as jeniskelamin"))
            ->where('koders', $kdProfile)
            ->get();

        $agama = Agama::where('aktif', true)
            ->select(DB::raw("id, UPPER(agama) as agama"))
            ->where('koders', $kdProfile)
            ->get();

        $statusPerkawinan = StatusPerkawinan::where('aktif', true)
            ->select(DB::raw("id, UPPER(statusperkawinan) as statusperkawinan,namaexternal as namadukcapil"))
            ->where('koders', $kdProfile)
            ->get();

        $pendidikan = Pendidikan::where('aktif', true)
            ->select(DB::raw("id, UPPER(pendidikan) as pendidikan,namaexternal as namadukcapil"))
            ->where('koders', $kdProfile)
            ->get();

        $pekerjaan = DB::table('pekerjaanmt')
            ->select(DB::raw("id, UPPER(pekerjaan) as pekerjaan,namaexternal as namadukcapil"))
            ->where('aktif', true)
            ->where('koders', $kdProfile)
            ->get();

        $gd = DB::table('golongandarahmt')
            ->select(DB::raw("id, UPPER(golongandarah) as golongandarah,namaexternal as namadukcapil"))
            ->where('aktif', true)
            ->get();
        $suku = DB::table('sukumt')
            ->select(DB::raw("id, UPPER(suku) as suku"))
            ->where('aktif', true)
            ->where('koders', $kdProfile)
            ->get();
        $result = array(
            'jeniskelamin' => $jk,
            'agama' => $agama,
            'statusperkawinan' => $statusPerkawinan,
            'pendidikan' => $pendidikan,
            'pekerjaan' => $pekerjaan,
            'pegawaiLogin' => $dataPegawai->namalengkap,
            'golongandarah' => $gd,
            'suku' => $suku,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getComboAddress(Request $request)
    {

        $kebangsaan = DB::table('kebangsaanmt')
            ->select(DB::raw("id, UPPER(name) as name"))
            ->where('aktif', true)
            ->get();

        $negara = DB::table('negaramt')
            ->select(DB::raw("id, UPPER(namanegara) as namanegara"))
            ->where('aktif', true)
            ->orderBy('namanegara')
            ->get();

        $kotakabupaten = DB::table('kotakabupatenmt')
            ->select(DB::raw("id, UPPER(namakotakabupaten) as namakotakabupaten"))
            ->where('aktif', true)
            ->orderBy('namakotakabupaten')
            ->get();

        $propinsi = DB::table('provinsimt')
            ->select(DB::raw("id, UPPER(namapropinsi) as namapropinsi"))
            ->where('aktif', true)
            ->orderBy('namapropinsi')
            ->get();

        $kecamatan = [];
        $result = array(
            'kebangsaan' => $kebangsaan,
            'negara' => $negara,
            'kotakabupaten' => $kotakabupaten,
            'propinsi' => $propinsi,
            'kecamatan' => $kecamatan,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getDesaKelurahanPaging(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $req = $request->all();
        if (isset($req['namadesakelurahan'])) {
            $explode = explode(',',$req['namadesakelurahan']);
            if (count($explode) > 1) {
                $namaDesa = $explode[0];
                $namaKec = $explode[1];
            }
        }
        $Desa = \DB::table('desakelurahanmt as ds')
            ->join('kecamatanmt as kc', 'ds.kecamatanidfk', '=', 'kc.id')
            ->join('kotakabupatenmt as kk', 'ds.kotakabupatenidfk', '=', 'kk.id')
            ->join('provinsimt as pp', 'ds.provinsiidfk', '=', 'pp.id')
            ->select(DB::raw("ds.id,UPPER(ds.namadesakelurahan) as namadesakelurahan,ds.kodepos,
			                 ds.kecamatanidfk as objectkecamatanfk,ds.kotakabupatenidfk as objectkotakabupatenfk,ds.provinsiidfk as objectpropinsifk,
				             UPPER(kc.namakecamatan) as namakecamatan,UPPER(kk.namakotakabupaten) as namakotakabupaten,UPPER( pp.namapropinsi) as namapropinsi"))
            ->where('ds.aktif', true)
            ->orderBy('ds.namadesakelurahan');

        // if (
        //     isset($req['namadesakelurahan']) &&
        //     $req['namadesakelurahan'] != "" &&
        //     $req['namadesakelurahan'] != "undefined"
        // ) {
        //     $Desa = $Desa->where('ds.namadesakelurahan', 'ilike', '%' . $req['namadesakelurahan'] . '%');
        // };
        if (
            isset($req['namakecamatan']) &&
            $req['namakecamatan'] != "" &&
            $req['namakecamatan'] != "undefined"
        ) {
            $Desa = $Desa->where('kc.namakecamatan', 'ilike', '%' . $req['namakecamatan'] . '%');
        };
        if (
            isset($req['iddesakelurahan']) &&
            $req['iddesakelurahan'] != "" &&
            $req['iddesakelurahan'] != "undefined"
        ) {
            $Desa = $Desa->where('ds.id', $req['iddesakelurahan']);
        };
        if (
            isset($req['namadesakelurahan']) &&
            $req['namadesakelurahan'] != "" &&
            $req['namadesakelurahan'] != "undefined"
        ) {
            if (isset($namaDesa) && isset($namaKec) && $namaDesa!='' && $namaKec!='') {
                $Desa = $Desa
                    ->where('ds.namadesakelurahan', 'ilike', '%' . $namaDesa . '%')
                    ->where('kc.namakecamatan', 'ilike', '%' . $namaKec . '%');
            }
            if (isset($namaDesa) && !isset($namaKec) ) {
                $Desa = $Desa
                    ->where('ds.namadesakelurahan', 'ilike', '%' . $namaDesa . '%');
            }
            if (!isset($namaDesa) && !isset($namaKec) ) {
                $Desa = $Desa
                    ->where('ds.namadesakelurahan', 'ilike', '%' . $req['namadesakelurahan'] . '%');
            }


        }

        $Desa = $Desa->take(20);
        $Desa = $Desa->get();
        $tempDesa = [];
        if (count($Desa) != 0) {
            foreach($Desa as $item) {
                $tempDesa[] = array(
                    'id' => $item->id,
                    'namadesakelurahan' => $item->namadesakelurahan,
                    'kodepos' => $item->kodepos,
                    'namakecamatan' => $item->namakecamatan,
                    'namakotakabupaten' => $item->namakotakabupaten,
                    'namapropinsi' => $item->namapropinsi,
                    'desa' => $item->namadesakelurahan . ', ' . $item->namakecamatan . ',  ' . $item->namakotakabupaten . ', ' .
                        $item->namapropinsi,
                    'objectkecamatanfk' => $item->objectkecamatanfk,
                    'objectkotakabupatenfk' => $item->objectkotakabupatenfk,
                    'objectpropinsifk' => $item->objectpropinsifk,
                );
            }
        }
        return $this->respond($tempDesa);
    }

    public function getKecamatanPart(Request $request)
    {
        $req = $request->all();
        $kecamatan = \DB::table('kecamatanmt as ru')
            ->select(DB::raw("ru.id, UPPER(ru.namakecamatan) as namakecamatan"))
            ->where('ru.aktif', true)
            ->orderBy('ru.namakecamatan');

        if (
            isset($req['namakecamatan']) &&
            $req['namakecamatan'] != "" &&
            $req['namakecamatan'] != "undefined"
        ) {
            $kecamatan = $kecamatan->where('ru.namakecamatan ', $req['namakecamatan']);
        };
        if (
            isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined"
        ) {
            $kecamatan = $kecamatan
                ->where('ru.namakecamatan', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        }

        $kecamatan = $kecamatan->take(10);
        $kecamatan = $kecamatan->get();
        return $this->respond($kecamatan);
    }

    public function getAlamatByKodePos(Request $request)
    {
        $data = \DB::table('desakelurahanmt as dk')
            ->Join('kecamatanmt as kcm', 'kcm.id', '=', 'dk.kecamatanidfk')
            ->Join('kotakabupatenmt as kk', 'kk.id', '=', 'dk.kotakabupatenidfk')
            ->Join('provinsimt as pp', 'pp.id', '=', 'dk.provinsiidfk')
            ->select(DB::raw("dk.id,dk.id as objectdesakelurahanfk,UPPER(dk.namadesakelurahan) as namadesakelurahan,dk.kodepos,
			                 dk.kecamatanidfk as objectkecamatanfk,dk.kotakabupatenidfk as objectkotakabupatenfk,dk.provinsiidfk as objectpropinsifk,
				             UPPER(kcm.namakecamatan) as namakecamatan,UPPER(kk.namakotakabupaten) as namakotakabupaten,
				             UPPER(pp.namapropinsi) as namapropinsi"))
            ->where('dk.aktif', true)
            ->where('dk.kodepos', $request['kodePos'])
            ->get();

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function savePasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $detLogin = $request->all();
        DB::beginTransaction();
        try {
            if ($request['idpasien'] == '') {
                $newId = Pasien::max('id') + 1;
                if (isset($request['isPenunjang']) && $request['isPenunjang'] == true) {
                    $noCm = $this->generateCodeBySeqTable(new Pasien, 'nocmpenunjang', 9, 'P', $idProfile);
                } //endregion
                else {
                    $idRunningNumber = $this->settingDataFixed('idRunningNumberPasien', $idProfile);
                    $runningNumber = RunningNumber::where('id', $idRunningNumber)->where('koders', $idProfile)->first();
                    $noCm = $this->generateCodeBySeqTable(new Pasien, 'nocm', (int)$runningNumber->length, $runningNumber->prefix, $idProfile);

                    RunningNumber::where('id', $idRunningNumber)
                        ->where('koders', $idProfile)
                        ->update(
                            [
                                'nomer_terbaru' => $noCm
                            ]
                        );
                }

                $dataPS = new Pasien();
                $dataPS->id = $newId;
                $dataPS->koders = (int)$kdProfile;
                $dataPS->aktif = true;
                $dataPS->kodeexternal = $newId;
                $dataPS->norec = $dataPS->generateNewId();
            } else {
                $dataPS = Pasien::where('id', $request['idpasien'])->first();
                $noCm = $dataPS->norm;
                if ($noCm == null) {
                    if (isset($request['isPenunjang']) && $request['isPenunjang'] == true) {
                        $noCm = $this->generateCodeBySeqTable(new Pasien, 'nocmpenunjang', 9, 'P', $idProfile);
                    }
                    else {
                        $idRunningNumber = $this->settingDataFixed('idRunningNumberPasien', $idProfile);
                        $runningNumber = RunningNumber::where('id', $idRunningNumber)->where('koders', $idProfile)->first();
                        $noCm = $this->generateCodeBySeqTable(new Pasien, 'nocm', (int)$runningNumber->length, $runningNumber->prefix, $idProfile);

                        RunningNumber::where('id', $idRunningNumber)
                            ->where('koders', $idProfile)
                            ->update(
                                [
                                    'nomer_terbaru' => $noCm
                                ]
                            );
                    }
                }
                $newId = $dataPS->id;
            }
            $dataPS->aktif = true;
            $dataPS->namaexternal = $request['pasien']['namaPasien'];
            $dataPS->agamaidfk = $request['agama']['id'];
            $dataPS->jeniskelaminidfk = $request['jenisKelamin']['id'];
            $dataPS->namapasien = $request['pasien']['namaPasien'];
            $dataPS->pekerjaanidfk = $request['pekerjaan']['id'];
            $dataPS->pendidikanidfk = $request['pendidikan']['id'];
            $dataPS->idpasien2 = 1;
            $dataPS->statusperkawinanidfk = $request['statusPerkawinan']['id'];
            $dataPS->tglpendaftaran = date('Y-m-d H:i:s');
            $dataPS->tgllahir = $request['pasien']['tglLahir'];
            $dataPS->namaibu = $request['namaIbu'];
            $dataPS->notelepon = $request['noTelepon'];
            $dataPS->noidentitas = $request['pasien']['noIdentitas'];
            $dataPS->noaditional = $request['noAditional'];
            $dataPS->kebangsaanidfk = $request['kebangsaan']['id'];
            $dataPS->negaraidfk = $request['negara']['id'];
            $dataPS->namaayah = $request['namaAyah'];
            $dataPS->namasuamiistri = $request['pasien']['namaSuamiIstri'];
            $dataPS->noasuransilain = $request['pasien']['noAsuransiLain'];
            $dataPS->nobpjs = $request['pasien']['noBpjs'];
            $dataPS->nohp = $request['pasien']['noHp'];
            $dataPS->tempatlahir = $request['pasien']['tempatLahir'];
            $dataPS->namakeluarga = $request['pasien']['namaKeluarga'];
            $dataPS->jamlahir = $request['pasien']['tglLahir'];
            if (isset($request['golonganDarah'])) {
                $dataPS->golongandarahidfk = $request['golonganDarah']['id'];
            }
            if (isset($request['suku'])) {
                $dataPS->sukuidfk = $request['suku']['id'];
            }

            $dataPS->norm = $noCm;
            if (isset($request['penanggungjawab'])) {
                $dataPS->penanggungjawab = $request['penanggungjawab'];
            }
            if (isset($request['hubungankeluargapj'])) {
                $dataPS->hubungankeluargapj = $request['hubungankeluargapj'];
            }
            if (isset($request['pekerjaanpenangggungjawab'])) {
                $dataPS->pekerjaanpenangggungjawab = $request['pekerjaanpenangggungjawab'];
            }
            if (isset($request['ktppenanggungjawab'])) {
                $dataPS->ktppenanggungjawab = $request['ktppenanggungjawab'];
            }
            if (isset($request['alamatrmh'])) {
                $dataPS->alamatrmh = $request['alamatrmh'];
            }
            if (isset($request['alamatktr'])) {
                $dataPS->alamatktr = $request['alamatktr'];
            }
            if (isset($request['bahasa'])) {
                $dataPS->bahasa = $request['bahasa'];
            }
            if (isset($request['teleponpenanggungjawab'])) {
                $dataPS->teleponpenanggungjawab = $request['teleponpenanggungjawab'];
            }
            if (isset($request['dokterpengirim'])) {
                $dataPS->dokterpengirim = $request['dokterpengirim'];
            }
            if (isset($request['alamatdokter'])) {
                $dataPS->alamatdokterpengirim = $request['alamatdokter'];
            }
            if (isset($request['jeniskelaminpenanggungjawab'])) {
                $dataPS->jeniskelaminpenanggungjawab = $request['jeniskelaminpenanggungjawab'];
            }
            if (isset($request['umurpenanggungjawab'])) {
                $dataPS->umurpenanggungjawab = $request['umurpenanggungjawab'];
            }

            $dataPS->save();
            $dataNoCMFk = $newId;
            $nocmfk = $dataPS->id;
            $nocmss = $dataPS->norm;
            if (isset($request['pasien']['image'])) {
            }
            if ($request['idpasien'] == '') {

                $idAlamat = Alamat::max('id') + 1;
                $dataAL = new Alamat();
                $dataAL->id = $idAlamat;
                $dataAL->koders = (int)$kdProfile;;
                $dataAL->aktif = true;
                $dataAL->kodeexternal = $idAlamat;
                $dataAL->norec = $dataAL->generateNewId();
            } else {
                $dataAL = Alamat::where('normidfk', $dataNoCMFk)->first();
                if (empty($dataAL)) {
                    $idAlamat = Alamat::max('id') + 1;
                    $dataAL = new Alamat();
                    $dataAL->id = $idAlamat;
                    $dataAL->koders = (int)$kdProfile;
                    $dataAL->aktif = true;
                    $dataAL->kodeexternal = $idAlamat;
                    $dataAL->norec = $dataAL->generateNewId();
                    $idAlamat = $dataAL->id;
                } else {
                    $idAlamat = $dataAL->id;
                }
            }
            $dataAL->aktif = true;
            $dataAL->alamatlengkap = $request['alamatLengkap'];
            $dataAL->hubungankeluargaidfk = 1;
            $dataAL->desakelurahanidfk = $request['desaKelurahan']['id'];
            $dataAL->jenisalamatidfk = 1;
            $dataAL->kdalamat = $idAlamat;
            $dataAL->kecamatanidfk = $request['kecamatan']['id'];
            $dataAL->kodepos = $request['kodePos'];
            $dataAL->kotakabupatenidfk = $request['kotaKabupaten']['id'];
            $dataAL->namadesakelurahan = $request['desaKelurahan']['namaDesaKelurahan'];
            $dataAL->namakecamatan = $request['kecamatan']['namaKecamatan'];
            $dataAL->namakotakabupaten = $request['kotaKabupaten']['namaKotaKabupaten'];
            $dataAL->negaraidfk = $request['negara']['id'];
            $dataAL->normidfk = $dataNoCMFk;
            $dataAL->pegawaiidfk = $detLogin['userData']['id'];
            $dataAL->provinsiidfk = $request['propinsi']['id'];
            $dataAL->kecamatan = $request['kecamatan']['namaKecamatan'];
            $dataAL->kotakabupaten = $request['kotaKabupaten']['namaKotaKabupaten'];

            $dataAL->save();
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Selesai";
            DB::commit();
            $dataPS->foto = $dataPS->foto != null ? "data:image/jpeg;base64," . base64_encode($dataPS->foto) : null;
            $result = array(
                'status' => 201,
                'data' => $dataPS,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'e' => $e->getMessage() . ' ' . $e->getLine(),
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getPasienByNoCm(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('pasienmt as ps')
            ->leftJOIN('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftjoin('pendidikanmt as pdd', 'ps.pendidikanidfk', '=', 'pdd.id')
            ->leftjoin('pekerjaanmt as pk', 'ps.pekerjaanidfk', '=', 'pk.id')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->select(
                'ps.norm as nocm',
                'ps.id as nocmfk',
                'ps.namapasien',
                'ps.jeniskelaminidfk as objectjeniskelaminfk',
                'jk.jeniskelamin',
                'ps.tgllahir',
                'alm.alamatlengkap',
                'pdd.pendidikan',
                'pk.pekerjaan',
                'ps.noidentitas',
                'ps.nohp as notelepon',
                'ps.nobpjs',
                DB::raw('encode(foto, \'base64\') AS foto')
            )
            ->where('ps.aktif', true)
            ->where('ps.koders', (int)$kdProfile)
            ->where('ps.id', $request['noCm'])
            ->get();

        if (count($data) > 0) {
            foreach ($data as $item) {
                if ($item->foto != null) {
                    $item->foto = "data:image/jpeg;base64," . $item->foto;
                }
            }
        }

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getDataComboNEW(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenRanapFix', $idProfile));
        $kdDepartemenRawatInap = [];
        foreach ($deptRanap as $itemRanap) {
            $kdDepartemenRawatInap[] = (int)$itemRanap;
        }
        $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenRawatJalanFix', $idProfile));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item) {
            $kdDepartemenRawatJalan[] = (int)$item;
        }

        $dataRuanganInap = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk as departemenidfk')
            ->where('ru.aktif', true)
            ->wherein('ru.instalasiidfk', $kdDepartemenRawatInap)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();
        $dataRuanganJalan = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk as objectdepartemenfk','ru.kdinternal as kodebpjs')
            ->where('ru.aktif', true)
            ->wherein('ru.instalasiidfk', $kdDepartemenRawatJalan)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        $dataAsalRujukan = \DB::table('rujukanasalmt as as')
            ->select('as.id', 'as.asalrujukan')
            ->where('as.aktif', true)
            ->orderBy('as.asalrujukan')
            ->get();


        $dataKelompok = \DB::table('kelompokpasienmt as kp')
            ->select('kp.id', 'kp.kelompokpasien')
            ->where('kp.aktif', true)
            ->orderBy('kp.kelompokpasien')
            ->get();

        $dataKelas = \DB::table('kelasmt as kl')
            ->select('kl.id', 'kl.namakelas')
            ->where('kl.aktif', true)
            ->orderBy('kl.namakelas')
            ->get();

        $dataKamar = \DB::table('kamarmt as kmr')
            ->select('kmr.id', 'kmr.namakamar')
            ->where('kmr.aktif', true)
            ->where('kmr.koders', (int)$kdProfile)
            ->orderBy('kmr.namakamar')
            ->get();

        $jenisPelayanan = \DB::table('jenispelayananmt as jp')
            ->select('jp.id', 'jp.jenispelayanan')
            ->where('jp.aktif', true)
            ->orderBy('jp.jenispelayanan')
            ->get();

        $result = array(
            'ruanganranap' => $dataRuanganInap,
            'ruanganrajal' => $dataRuanganJalan,
            'kelompokpasien' => $dataKelompok,
            'kelas' => $dataKelas,
            'asalrujukan' => $dataAsalRujukan,
            'jenispelayanan' => $jenisPelayanan,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getComboDokterPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $kdJenisPegawaiDokter = $this->settingDataFixed('kdJenisPegawaiDokter', $kdProfile);
        $req = $request->all();
        $data = \DB::table('pegawaimt')
            ->select('*','kddokterbpjs as kodebpjs')
            ->where('aktif', true)
            ->where('objectjenispegawaifk', $kdJenisPegawaiDokter)
            ->where('koders', (int)$kdProfile)
            ->orderBy('namalengkap');

        if (
            isset($req['namalengkap']) &&
            $req['namalengkap'] != "" &&
            $req['namalengkap'] != "undefined"
        ) {
            $data = $data->where('namalengkap', 'ilike', '%' . $req['namalengkap'] . '%');
        };
        if (
            isset($req['idpegawai']) &&
            $req['idpegawai'] != "" &&
            $req['idpegawai'] != "undefined"
        ) {
            $data = $data->where('id', $req['idpegawai']);
        };
        if (
            isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined"
        ) {
            $data = $data
                ->where('namalengkap', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        }

        $data = $data->take(10);
        $data = $data->get();

        return $this->respond($data);
    }

    public function getPenjaminByKelompokPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('mapkelompokpasientorekananmt as mkp')
            ->join('kelompokpasienmt as kp', 'kp.id', '=', 'mkp.kelompokpasienidfk')
            ->join('rekananmt as rk', 'rk.id', '=', 'mkp.rekananidfk')
            ->select('rk.id', 'rk.namarekanan', 'kp.id as id_kelompokpasien', 'kp.kelompokpasien')
            ->where('mkp.aktif', true)
            ->where('mkp.koders', (int)$kdProfile);

        if (isset($request['kdKelompokPasien']) && $request['kdKelompokPasien'] != "" && $request['kdKelompokPasien'] != "undefined") {
            $data = $data->where('mkp.kelompokpasienidfk', '=', $request['kdKelompokPasien']);
        };
        $data = $data->get();

        $result = array(
            'rekanan' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getKelasByRuangan(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('mapruangantokelasmt as mrk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'mrk.ruanganidfk')
            ->join('kelasmt as kl', 'kl.id', '=', 'mrk.kelasidfk')
            ->select('kl.id', 'kl.namakelas', 'ru.id as id_ruangan', 'ru.namaruangan')
            ->where('mrk.ruanganidfk', $request['idRuangan'])
            ->where('mrk.koders', (int)$kdProfile)
            ->get();

        $result = array(
            'kelas' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getKamarByKelasRuangan(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);


        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('kamarmt as kmr')
            ->join('ruanganmt as ru', 'ru.id', '=', 'kmr.ruanganidfk')
            ->join('kelasmt as kl', 'kl.id', '=', 'kmr.kelasidfk')
            ->select(
                'kmr.id',
                'kmr.namakamar',
                'kl.id as id_kelas',
                'kl.namakelas',
                'ru.id as id_ruangan',
                'ru.namaruangan',
                'kmr.jumlakamarisi',
                'kmr.qtybed'
            )
            ->where('kmr.ruanganidfk', $request['idRuangan'])
            ->where('kmr.kelasidfk', $request['idKelas'])
            ->where('kmr.aktif', true)
            ->where('kmr.koders', (int)$kdProfile)
            ->get();
        $set = $this->settingDataFixed('kdStatusBedKosong', $kdProfile);
        if (isset($request['israwatgabung']) && $request['israwatgabung'] != 'undefined' && $request['israwatgabung'] == 'false') {
            for ($i = count($data) - 1; $i >= 0; $i--) {
                $id = $data[$i]->id;
                $des = DB::select(DB::raw("select * from tempattidurmt
                    where kamaridfk ='$id' and statusbedidfk='$set' and koders=$kdProfile and aktif=true"));
                if (count($des) == 0) {
                    array_splice($data, $i, 1);
                }
            }
        }

        $result = array(
            'kamar' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getNoBedByKamar(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('tempattidurmt as tt')
            ->join('statusbedmt as sb', 'sb.id', '=', 'tt.statusbedidfk')
            ->join('kamarmt as km', 'km.id', '=', 'tt.kamaridfk')
            ->select('tt.id', 'sb.statusbed', 'tt.reportdisplay')
            ->where('tt.kamaridfk', $request['idKamar'])
            ->where('km.aktif', true)
            ->where('tt.aktif', true)
            ->where('tt.koders', (int)$kdProfile)
            ->orderBy('tt.reportdisplay')
            ->get();

        $result = array(
            'bed' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function saveRegistrasiPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProdile = (int)$kdProfile;
        $r_NewPD = $request['pasiendaftar'];
        $r_NewAPD = $request['antrianpasiendiperiksa'];

        if ($r_NewPD['israwatinap'] == 'true') {
            $isRawatInap = 'true';
        } else {
            $isRawatInap = 'false';
        }

        $cekUdahDaftar = RegistrasiPasien::where('normidfk', $r_NewPD['nocmfk'])
            ->wherenull('tglpulang')
            ->where('aktif',true)
            ->count();
        if ($cekUdahDaftar > 0 && $r_NewPD['norec_pd'] == '') {
            $transStatus = 'belumdipulangkan';
        } else {
            DB::beginTransaction();
            try {
                if ($r_NewPD['norec_pd'] == '') {
                    $noRegistrasiSeq = $this->generateCodeBySeqTable(new RegistrasiPasien, 'noregistrasi', 10, date('ym'), $idProdile);
                    if ($noRegistrasiSeq == '') {
                        $transMessage = "Gagal mengumpukan data, Coba lagi.!";
                        DB::rollBack();
                        $result = array(
                            "status" => 400,
                            "message" => $transMessage,
                            "as" => 'slvR',
                        );
                        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
                    }
                    $dataPD = new RegistrasiPasien();
                    $dataPD->norec = $dataPD->generateNewId();
                    $dataPD->koders = $idProdile;
                    $dataPD->aktif = true;
                    $noRegistrasi = $noRegistrasiSeq;
                    $dataPD->ruanganasalidfk = $r_NewPD['objectruanganfk'];
                } else {
                    $dataPD = RegistrasiPasien::where('norec', $r_NewPD['norec_pd'])->where('koders', $idProdile)->first();
                    $noRegistrasi = $dataPD->noregistrasi;
                }
                $dataPD->ruanganlastidfk = $r_NewPD['objectruanganfk'];
                $dataPD->dokterpemeriksaidfk = isset($r_NewAPD['objectpegawaifk']) ? $r_NewAPD['objectpegawaifk'] : null;

                $dataPD->pegawaiidfk = isset($r_NewAPD['objectpegawaifk']) ? $r_NewAPD['objectpegawaifk'] : null;
                $dataPD->iskajianawal = false;
                $dataPD->isonsiteservice = 0;
                $dataPD->isregistrasilengkap = 0;
                $dataPD->jenispelayanan = $r_NewPD['tipelayanan'];
                $dataPD->jenispelayananidfk = $r_NewPD['tipelayanan'];
                $dataPD->pegawaiidfk = 1;
                $dataPD->asalrujukanidfk = $r_NewAPD['objectasalrujukanfk'];
                if ($isRawatInap == 'true') {
                    $dataPD->kelasidfk = $r_NewPD['objectkelasfk'];
                    $dataPD->tglpulang = null;
                } else {
                    $dataPD->kelasidfk = (int)$this->settingDataFixed('kdKelasNonKelasRegistrasi', $idProdile);
                    $dataPD->tglpulang = $r_NewPD['tglregistrasi'];
                }
                $dataPD->kelompokpasienlastidfk = $r_NewPD['objectkelompokpasienlastfk'];
                $dataPD->normidfk = $r_NewPD['nocmfk'];
                $dataPD->rekananidfk = $r_NewPD['objectrekananfk'];
                $dataPD->statuskasuspenyakit = false;
                if (isset($r_NewPD['statuspasien']) && $r_NewPD['statuspasien']!='') {
                    $dataPD->statuspasien = $r_NewPD['statuspasien'];
                } else {
                    $dataPD->statuspasien = 'LAMA';
                }
                if (isset($r_NewPD['statusschedule'])) {
                    $dataPD->statusschedule = $r_NewPD['statusschedule'];
                }
                $dataPD->tglregistrasi = $r_NewPD['tglregistrasi'];
                $dataPD->noregistrasi = $noRegistrasi;
                $dataPD->save();
                $dataPDnorec = $dataPD->norec;
                $dataStatusPasien = $dataPD->statuspasien;
                if ($r_NewAPD['norec_apd'] == '') {
                    $countNoAntrian = DaftarPasienRuangan::where('ruanganidfk', $r_NewPD['objectruanganfk'])
                        ->where('koders', $idProdile)
                        ->where('tglregistrasi', '>=', $r_NewPD['tglregistrasidate'] . ' 00:00')
                        ->where('tglregistrasi', '<=', $r_NewPD['tglregistrasidate'] . ' 23:59')
                        ->where('aktif', true)
                        ->max('noantrian');
                    $noAntrian = $countNoAntrian + 1;

                    $dataAPD = new DaftarPasienRuangan;
                    $dataAPD->norec = $dataAPD->generateNewId();
                    $dataAPD->koders = (int)$kdProfile;
                    $dataAPD->aktif = true;
                    $dataAPD->noantrian = $noAntrian;
                } else {
                    $dataAPD = DaftarPasienRuangan::where('norec', $r_NewAPD['norec_apd'])->where('koders', $idProdile)->first();
                    if ($r_NewPD['objectruanganfk'] != $dataAPD->ruanganidfk) {
                        $countNoAntrian = DaftarPasienRuangan::where('ruanganidfk', $r_NewPD['objectruanganfk'])
                            ->where('koders', $idProdile)
                            ->where('tglregistrasi', '>=', $r_NewPD['tglregistrasidate'] . ' 00:00')
                            ->where('tglregistrasi', '<=', $r_NewPD['tglregistrasidate'] . ' 23:59')
                            ->where('aktif', true)
                            ->max('noantrian');
                        $noAntrian = $countNoAntrian + 1;
                        $dataAPD->noantrian = $noAntrian;
                    }
                    if ($isRawatInap == 'true') {
                        $set = $this->settingDataFixed('kdStatusBedKosong', $kdProfile);
                        TempatTidur::where('id', $dataAPD->nobed)->update(['statusbedidfk' => $set]);
                    }
                }

                $dataAPD->asalrujukanidfk = $r_NewAPD['objectasalrujukanfk'];
                if (isset($r_NewAPD['objectkamarfk'])){
                    $dataAPD->kamaridfk =$r_NewAPD['objectkamarfk'];
                }
                $dataAPD->kasuspenyakitidfk = null;
                $dataAPD->ruanganidfk = $r_NewPD['objectruanganfk'];
                if ($isRawatInap == 'true') {
                    $dataAPD->kelasidfk = $r_NewAPD['objectkelasfk'];
                    $dataAPD->tglkeluar = null;
                } else {
                    $dataAPD->kelasidfk = (int)$this->settingDataFixed('kdKelasNonKelasRegistrasi', (int)$kdProfile);
                    $dataAPD->tglkeluar = $r_NewPD['tglregistrasi'];
                }
                $dataAPD->nobedidfk = isset($r_NewAPD['nobed'])?$r_NewAPD['nobed']:null;
                $dataAPD->registrasipasienfk = $dataPDnorec;
                $dataAPD->pegawaiidfk = isset($r_NewAPD['objectpegawaifk']) ? ($r_NewAPD['objectpegawaifk'] == null ? $r_NewPD['objectpegawaifk'] : $r_NewAPD['objectpegawaifk']):null;    $dataAPD->statusantrian = 0;
                $dataAPD->statuskunjungan = $dataStatusPasien;
                $dataAPD->statuspasienidfk = 1;
                $dataAPD->tglregistrasi = $r_NewAPD['tglregistrasi'];
                $dataAPD->tglmasuk = $r_NewAPD['tglregistrasi'];
                $dataAPD->israwatgabung = null;
                $dataAPD->save();
                if ($isRawatInap == 'true') {
                    $set = $this->settingDataFixed('kdStatusBedIsi', $kdProfile);
                    TempatTidur::where('id', $r_NewAPD['nobed'])->update(['statusbedidfk' => $set]);
                }

                $transStatus = 'true';
            } catch (\Exception $e) {
                $transStatus = 'false';
            }
        }

        if ($transStatus == 'belumdipulangkan') {
            $transMessage = 'Pasien Belum Dipulangkan';
            $result = array(
                'status' => 400,
                'message' => $transMessage,
                'as' => 'Xoxo',
            );
        } else if ($transStatus == 'true') {
            if (isset($r_NewPD['isjkn']) && $r_NewPD['isjkn'] == true){
                $data = DB::table('perjanjianpasientr')
                    ->where('koders', $kdProfile)
                    ->where('aktif', true)
                    ->where('noreservasi', $r_NewPD['statusschedule'])
                    ->update([
                        'isconfirm' => true,
                    ]);

                $json = array(
                    "kodebooking" => $r_NewPD['statusschedule'],
                    "taskid" => 3, //pasien lama langsung task 3 //(akhir waktu layan admisi/mulai waktu tunggu poli)
                    "waktu" => strtotime(date('Y-m-d H:i:s')) * 1000,
                );
                $objetoRequest = new \Illuminate\Http\Request();
                $objetoRequest ['url']= "antrean/updatewaktu";
                $objetoRequest ['jenis']= "antrean";
                $objetoRequest ['method']= "POST";
                $objetoRequest ['data']= $json;
                
                $post = app('App\Http\Controllers\Bridging\BridgingBPJSV2Controller')->bpjsTools($objetoRequest);

                DB::commit();
                $transMessage = 'OK';
                $result = array(
                    "metadata" => array(
                        "code" => 200,
                        "message" => $transMessage)
                );
                return response()->json($result);
            }else{
                DB::commit();
                $antrol = '';
                if($r_NewPD['norec_pd'] == '' && $r_NewPD['statusschedule'] != '' && $r_NewPD['statusschedule'] != 'Kios-K'){
                    $antrol = $this->saveAntrolV2($r_NewPD['statusschedule']);
                }
                $transMessage = 'Simpan Registrasi';
                $result = array(
                    'status' => 201,
                    'message'=>$transMessage,
                    'dataPD' => $dataPD,
                    'dataAPD' => $dataAPD,
                    'antrol'=>$antrol,
                    'as' => 'ramdanegie',
                );
            }


        } else {
            $transMessage = 'Simpan Registrasi Gagal';
            DB::rollBack();
            if (isset($r_NewPD['isjkn']) && $r_NewPD['isjkn'] == true){
                $transMessage = 'Gagal Check In';
                $result = array(
                    "metadata" => array(
                        "code" => 201,
                        "message" => $transMessage,
                        'e'=>$e->getMessage().' '.$e->getLine(),
                        )
                );
                return response()->json($result);
            }else{
                $transMessage = 'Simpan Registrasi Gagal';
                $result = array(
                    'status' => 400,
                    'message'=>$transMessage,
                    'dataPD' => $dataPD,
                    'e' => $e->getMessage().' '.$e->getLine(),
                    'as' => 'ramdanegie',
                );
            }

        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function saveAdministrasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {

            $pasiendaftar = RegistrasiPasien::where('norec', $request['norec'])->first();
            $apd = DaftarPasienRuangan::where('registrasipasienfk', $request['norec'])->first();
            $antrian = DaftarPasienRuangan::where('norec',$apd->norec)
            ->update([
                'ispelayananpasien' => true
            ]);
            $data = DB::select(DB::raw("select pp.tglpelayanan,pd.kelasidfk as objectkelasfk,
                    pd.ruanganlastidfk as objectruanganlastfk
                    from registrasipasientr as pd
                    INNER JOIN daftarpasienruangantr as apd on apd.registrasipasienfk=pd.norec
                    INNER JOIN transaksipasientr as pp on pp.daftarpasienruanganfk=apd.norec
                    INNER JOIN pelayananmt as pr on pr.id=pp.produkidfk
                    INNER JOIN ruanganmt as ru_pd on ru_pd.id=pd.ruanganlastidfk
                    where  pd.norec='$request[norec]'
                    and pd.koders=$kdProfile
                    and pp.produkidfk in (
                    select
                    produkidfk
                    from mapruangantoadministrasitr
                    where ruanganidfk=pd.ruanganlastidfk
                    and aktif=true
                    )
                "));

            if (count($data) == 0) {
                $sirahMacan = DB::select(
                    DB::raw("select hett.* from mapruangantoadministrasitr as map
                                INNER JOIN tarifpelayananmt as hett on hett.produkidfk=map.produkidfk
                                and hett.jenispelayananidfk =map.jenispelayananidfk
                                inner join suratkeputusanmt as sk on sk.id= hett.suratkeputusanidfk
                                where map.ruanganidfk=:ruanganid and hett.kelasidfk=:kelasid
                                and map.jenispelayananidfk=:jenispelayanan
                                and map.koders=:kdprofile
                                and hett.aktif=true
                                and sk.aktif=true"),
                    array(
                        'ruanganid' => $pasiendaftar->ruanganlastidfk,
                        'kelasid' => $pasiendaftar->kelasidfk,
                        'jenispelayanan' => $pasiendaftar->jenispelayananidfk,
                        'kdprofile' => $kdProfile
                    )
                );
                $buntutMacan = DB::select(
                    DB::raw("select hett.* from mapruangantoadministrasitr as map
                                        INNER JOIN tarifpelayanandmt as hett on hett.produkidfk=map.produkidfk
                                        and hett.jenispelayananidfk =map.jenispelayananidfk
                                        inner join suratkeputusanmt as sk on sk.id= hett.suratkeputusanidfk
                                        where map.ruanganidfk=:ruanganid and hett.kelasidfk=:kelasid
                                        and map.jenispelayananidfk=:jenispelayanan
                                        and map.koders=:kdprofile
                                        and hett.aktif=true
                                        and sk.aktif=true"),
                    array(
                        'ruanganid' => $pasiendaftar->ruanganlastidfk,
                        'kelasid' => $pasiendaftar->kelasidfk,
                        'jenispelayanan' => $pasiendaftar->jenispelayananidfk,
                        'kdprofile' => $kdProfile
                    )
                );

                foreach ($sirahMacan as $k) {
                    $PelPasien = new TransaksiPasien();
                    $PelPasien->norec = $PelPasien->generateNewId();
                    $PelPasien->koders = $kdProfile;
                    $PelPasien->aktif = true;
                    $PelPasien->daftarpasienruanganfk = $request['norec_apd'];
                    $PelPasien->tglregistrasi = $pasiendaftar->tglregistrasi;
                    $PelPasien->hargadiscount = 0; //0;
                    $PelPasien->hargajual = $k->hargasatuan;
                    $PelPasien->hargasatuan = $k->hargasatuan;
                    $PelPasien->jumlah = 1;
                    $PelPasien->kelasidfk = $pasiendaftar->kelasidfk;
                    $PelPasien->kdkelompoktransaksi = 1;
                    $PelPasien->piutangpenjamin = 0;
                    $PelPasien->piutangrumahsakit = 0;
                    $PelPasien->produkidfk = $k->produkidfk;
                    $PelPasien->stock = 1;
                    $PelPasien->tglpelayanan = date('Y-m-d H:i:s');
                    $PelPasien->harganetto = $k->harganetto1;

                    $PelPasien->save();
                    $PPnorec = $PelPasien->norec;
                    foreach ($buntutMacan as $itemKomponen) {
                        if ($itemKomponen->produkidfk == $k->produkidfk) {
                            $PelPasienDetail = new TransaksiPasienDetail();
                            $PelPasienDetail->norec = $PelPasienDetail->generateNewId();
                            $PelPasienDetail->koders = $kdProfile;
                            $PelPasienDetail->aktif = true;
                            $PelPasienDetail->daftarpasienruanganfk = $request['norec_apd'];
                            $PelPasienDetail->aturanpakai = '-';
                            $PelPasienDetail->hargadiscount = 0;
                            $PelPasienDetail->hargajual = $itemKomponen->hargasatuan;
                            $PelPasienDetail->hargasatuan = $itemKomponen->hargasatuan;
                            $PelPasienDetail->jumlah = 1;
                            $PelPasienDetail->keteranganlain = 'admin otomatis';
                            $PelPasienDetail->keteranganpakai2 = '-';
                            $PelPasienDetail->komponenhargaidfk = $itemKomponen->komponenhargaidfk;
                            $PelPasienDetail->transaksipasienfk = $PPnorec;
                            $PelPasienDetail->piutangpenjamin = 0;
                            $PelPasienDetail->piutangrumahsakit = 0;
                            $PelPasienDetail->produkidfk = $itemKomponen->produkidfk;
                            $PelPasienDetail->stock = 1;
                            $PelPasienDetail->tglpelayanan = date('Y-m-d H:i:s');
                            $PelPasienDetail->harganetto = $itemKomponen->harganetto1;
                            $PelPasienDetail->save();
                        }
                    }
                }
            }


            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        $transMessage = "Simpan Administrasi Otomatis";

        if ($transStatus == 'true') {
            $transMessage = $transMessage . "";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => 'Xoxo',
            );
        } else {
            $transMessage = $transMessage . " Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 201,
                "e" => $e->getMessage().' '.$e->getLine(),
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getPasienByNoRecPD(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('instalasimt as dpm', 'dpm.id', '=', 'ru.instalasiidfk')
            ->LEFTJOIN('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->LEFTJOIN('kamarmt as kmr', 'kmr.id', '=', 'apd.kamaridfk')
            ->leftjoin('tempattidurmt as tt', 'tt.id', '=', 'apd.nobedidfk')
            ->join('rujukanasalmt as ar', 'ar.id', '=', 'pd.asalrujukanidfk')
            ->join('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'apd.pegawaiidfk')
            ->leftjoin('jenispelayananmt as jpl', 'jpl.id', '=', 'pd.jenispelayananidfk')
            ->select(
                'pd.norec as norec_pd',
                'pd.noregistrasi',
                'pd.tglregistrasi',
                'pd.ruanganlastidfk as objectruanganlastfk',
                'ru.namaruangan',
                'pd.kelasidfk as objectkelasfk',
                'kls.namakelas',
                'apd.kamaridfk as objectkamarfk',
                'kmr.namakamar',
                'apd.nobedidfk as objecttempattidurfk',
                'pd.asalrujukanidfk as objectasalrujukanfk',
                'ar.asalrujukan',
                'pd.kelompokpasienlastidfk as  objectkelompokpasienlastfk',
                'kps.kelompokpasien',
                'pd.rekananidfk as objectrekananfk',
                'rk.namarekanan',
                'jpl.id as objectjenispelayananfk',
                'jpl.jenispelayanan',
                'pd.pegawaiidfk as objectpegawaifk',
                'pg.namalengkap as dokter',
                'ru.instalasiidfk as objectdepartemenfk',
                'tt.reportdisplay',
                DB::raw("CASE WHEN pd.tglpulang is null THEN
                'true'
              WHEN pd.tglpulang != pd.tglregistrasi THEN
                'true' ELSE'false' 
            END AS israwatinap")
            )
            ->where('pd.norec', $request['norecPD'])
            ->where('apd.norec', $request['norecAPD'])
            ->where('pd.koders', (int)$kdProfile)
            ->whereRaw(" (apd.ruanganasalidfk IS NULL or apd.tglkeluar is null)")
            ->get();

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getSettingAsuransi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $res['kodePPKRujukan'] = $this->settingDataFixed('kodePPKRujukan', $kdProfile);
        $res['namaPPKRujukan'] = $this->settingDataFixed('namaPPKRujukan', $kdProfile);
        $res['statusBridgingTemporary'] = $this->settingDataFixed('statusBridgingTemporary', $kdProfile);
        return $this->respond($res);
    }

    public function getDataComboAsuransiPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataAsalRujukan = \DB::table('rujukanasalmt as as')
            ->select('as.id', 'as.asalrujukan')
            ->where('as.aktif', true)
            ->where('as.koders', $kdProfile)
            ->orderBy('as.id')
            ->get();


        $dataKelompok = \DB::table('kelompokpasienmt as kp')
            ->select('kp.id', 'kp.kelompokpasien')
            ->where('kp.aktif', true)
            ->where('kp.koders', $kdProfile)
            ->orderBy('kp.kelompokpasien')
            ->get();

        $dataKelas = \DB::table('kelasmt as kl')
            ->select('kl.id', 'kl.namakelas','kl.idkelas2 as kelasbpjs')
            ->where('kl.aktif', true)
            ->where('kl.koders', $kdProfile)
            ->orderBy('kl.namakelas')
            ->get();


        $dataHubunganPeserta = \DB::table('hubunganpesertaasuransimt as hp')
            ->select('hp.id', 'hp.hubunganpeserta')
            ->where('hp.aktif', true)
            ->where('hp.koders', $kdProfile)
            ->orderBy('hp.hubunganpeserta')
            ->get();


        $result = array(
            'kelompokpasien' => $dataKelompok,
            'kelas' => $dataKelas,
            'asalrujukan' => $dataAsalRujukan,
            'hubunganpeserta' => $dataHubunganPeserta,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getPasienByNoreg(Request $request)
    {
        $norec_pd = $request['norec_pd'];
        $norec_apd = $request['norec_apd'];
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->leftjoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftjoin('pendidikanmt as pdd', 'pdd.id', '=', 'ps.pendidikanidfk')
            ->leftjoin('pekerjaanmt as pk', 'pk.id', '=', 'ps.pekerjaanidfk')
            ->join('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('instalasimt as dpm', 'dpm.id', '=', 'ru.instalasiidfk')
            ->leftjoin('pegawaimt as peg', 'peg.id', '=', 'pd.pegawaiidfk')
            ->leftjoin('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->LEFTjoin('jenispelayananmt as jpl', 'jpl.id', '=', 'pd.jenispelayananidfk')
            ->select(
                'ps.norm as nocm',
                'ps.id as nocmfk',
                'ps.noidentitas',
                'ps.namapasien',
                'pd.noregistrasi',
                'pd.tglregistrasi',
                'jk.jeniskelamin',
                'ps.tgllahir',
                'alm.alamatlengkap',
                'pdd.pendidikan',
                'pk.pekerjaan',
                'ps.nohp as notelepon',
                'ps.jeniskelaminidfk as objectjeniskelaminfk',
                'apd.ruanganidfk as objectruanganfk',
                'ru.namaruangan',
                'apd.norec as norec_apd',
                'pd.norec as norec_pd',
                'kps.kelompokpasien',
                'kls.namakelas',
                'apd.kelasidfk as objectkelasfk',
                'pd.kelompokpasienlastidfk as objectkelompokpasienlastfk',
                'pd.rekananidfk as objectrekananfk',
                'rk.namarekanan',
                'pd.ruanganlastidfk as objectruanganlastfk',
                'jpl.jenispelayanan',
                'pd.asalrujukanidfk as objectasalrujukanfk',
                'ru.kdinternal',
                'jpl.id as objectjenispelayananfk',
                'pd.pegawaiidfk as objectpegawaifk',
                'pd.statuspasien',
                'ps.nobpjs',
                'pd.statuspasien',
                DB::raw("CASE WHEN pd.tglpulang is null THEN
                'true'
                WHEN pd.tglpulang != pd.tglregistrasi THEN
                'true' ELSE'false' 
                 END AS israwatinap")
            )
            ->where('pd.norec', '=', $norec_pd)
            ->where('apd.norec', '=', $norec_apd)
            ->where('pd.koders', (int)$kdProfile)
            ->where('pd.aktif', true)
            ->first();

        return $this->respond($data);
    }

    public function getHistoryPemakaianAsuransiNew(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('pemakaianasuransitr as pa')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'pa.registrasipasienfk')
            ->leftjoin('asuransimt as apn', 'apn.id', '=', 'pa.asuransiidfk')
            ->leftjoin('rekananmt as rek', 'rek.id', '=', 'pd.rekananidfk')
            ->leftjoin('hubunganpesertaasuransimt as hpa', 'hpa.id', '=', 'apn.hubunganpesertaidfk')
            ->leftjoin('kelasmt as kls', 'kls.id', '=', 'apn.kelasdijaminidfk')
            ->leftjoin('icdxmt as dg', 'dg.id', '=', 'pa.icdxidfk')
            ->leftjoin('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('rujukanasalmt as asl', 'asl.id', '=', 'pa.asalrujukanfk')
            ->select(
                'pa.norec',
                'apn.id as norec_ap',
                'rek.namarekanan',
                'pd.noregistrasi',
                'pa.nokepesertaan',
                'apn.namapeserta',
                'apn.noasuransi',
                'apn.tgllahir',
                'apn.noidentitas',
                'apn.alamatlengkap',
                'apn.hubunganpesertaidfk as objecthubunganpesertafk',
                'pa.nosep',
                'pa.tanggalsep',
                'apn.kelasdijaminidfk as objectkelasdijaminfk',
                'kls.namakelas',
                'pa.catatan',
                'pa.norujukan',
                'apn.kdprovider',
                'apn.nmprovider',
                'pa.tglrujukan',
                'pa.icdxidfk as objectdiagnosafk',
                'dg.kddiagnosa',
                'dg.namadiagnosa',
                'pa.lakalantas',
                'pa.ppkrujukan',
                'pa.lokasilakalantas',
                'pa.penjaminlaka',
                'pd.kelompokpasienlastidfk as objectkelompokpasienlastfk',
                'pd.rekananidfk as objectrekananfk',
                'kps.kelompokpasien',
                'apn.kdpenjaminpasien',
                'apn.jenispeserta',
                'hpa.hubunganpeserta',
                'apn.tgllahir',
                'pa.cob',
                'pa.katarak',
                'pa.keteranganlaka',
                'pa.tglkejadian',
                'pa.suplesi',
                'pa.nosepsuplesi',
                'pa.kdpropinsi',
                'pa.namapropinsi',
                'pa.kdkabupaten',
                'pa.namakabupaten',
                'pa.kdkecamatan',
                'pa.namakecamatan',
                'pa.nosuratskdp',
                'pa.kodedpjp',
                'pa.namadpjp',
                'pa.asalrujukanfk',
                'asl.asalrujukan',
                'pa.kodedpjpmelayani',
                'pa.namadjpjpmelayanni',
                'pa.klsrawatnaik',
                'pa.pembiayaan',
                'pa.penanggungjawab',
                'pa.tujuankunj',
                'pa.flagprocedure',
                'pa.kdpenunjang',
                'pa.assesmentpel',
                'pa.tglcreate',
                'pa.statuskunjungan',
                'pa.poliasalkode',
                'pa.poliasalkode'
            )
            ->whereRaw(" ( pa.norec='$request[noregistrasi]' or pd.noregistrasi='$request[noregistrasi]')")
            ->where('pa.koders', (int)$kdProfile)
            ->where('pa.aktif', (int)$kdProfile)
            ->first();

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getDataComboOperator(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $dataPegawai = \DB::table('loginuser_s as lu')
            ->join('pegawaimt as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select('lu.objectpegawaifk', 'pg.namalengkap')
            ->where('lu.id', $dataLogin['userData']['id'])
            ->where('lu.kdprofile', (int)$kdProfile)
            ->first();

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

        $dataKelompok = \DB::table('kelompokpasienmt as kp')
            ->select('kp.id', 'kp.kelompokpasien')
            ->where('kp.aktif', true)
            ->orderBy('kp.kelompokpasien')
            ->get();

        $dataKelas = \DB::table('kelasmt as kl')
            ->select('kl.id', 'kl.namakelas')
            ->where('kl.aktif', true)
            ->orderBy('kl.id')
            ->get();

        $dataJenisDiagnosa = \DB::table('jenisdiagnosamt as kl')
            ->select('kl.id', 'kl.jenisdiagnosa')
            ->where('kl.aktif', true)
            ->orderBy('kl.id')
            ->get();
        $pembatalan = DB::select(DB::raw("select id,name from pembatalmt where aktif=true and koders = $kdProfile"));

        $result = array(
            'departemen' => $dataDepartemen,
            'kelompokpasien' => $dataKelompok,
            'dokter' => $dataDokter,
            'datalogin' => $dataLogin,
            'kelas' => $dataKelas,
            'jenisdiagnosa' => $dataJenisDiagnosa,
            'namaPPKRujukan' => $this->settingDataFixed('namaPPKRujukan', $kdProfile),
            'pembatalan' => $pembatalan,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDaftarRegistrasiPasienOperator(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $filter = $request->all();
        $data = \DB::table('registrasipasientr as pd')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'pd.pegawaiidfk')
            ->join('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
            ->join('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->leftjoin('pemakaianasuransitr as pa', 'pa.registrasipasienfk', '=', 'pd.norec')
            ->leftJoin('strukpelayanantr as sp', 'sp.norec', '=', 'pd.nostruklastidfk')
            ->leftJoin('strukbuktipenerimaantr as sbm', 'sbm.norec', '=', 'pd.nosbmlastidfk')
            ->leftjoin('rekananmt as rek', 'rek.id', '=', 'pd.rekananidfk')
            ->leftjoin('asuransimt as asu', 'pa.asuransiidfk', '=', 'asu.id')
            ->leftjoin('kelasmt as klstg', 'klstg.id', '=', 'asu.kelasdijaminidfk')
            ->leftjoin('jenispelayananmt as jpl', 'pd.jenispelayananidfk', '=', 'jpl.id')
            ->leftjoin('jeniskelaminmt AS jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->leftjoin('alamatmt AS alm', 'alm.normidfk', '=', 'ps.id')
            ->select(DB::raw("
                pd.norec,pd.tglregistrasi,ps.norm,pd.normidfk,pd.noregistrasi,ru.namaruangan,ps.namapasien,
			    kp.kelompokpasien,rek.namarekanan,pg.namalengkap AS namadokter,pd.tglpulang,pd.statuspasien,
			    pa.norec AS norec_pa,pa.asuransiidfk,pd.pegawaiidfk AS pgid,pd.ruanganlastidfk,pa.nosep AS nosep,
			    pd.nostruklastidfk,klstg.namakelas AS kelasditanggung,pd.kelasidfk,kls.namakelas,ps.tgllahir,ru.instalasiidfk,
			    pd.kelasidfk,pa.ppkrujukan,jpl.jenispelayanan,pa.icdxidfk AS iddiagnosabpjs,pd.isclosing,jk.jeniskelamin,
			    alm.alamatlengkap,CASE WHEN ru.instalasiidfk = 2 THEN true ELSE false END AS isranap,pa.nokepesertaan,pd.statusschedule as noreservasi
            "))
            ->where('pd.aktif', true)
            ->where('pd.koders', (int)$kdProfile);
        if (isset($filter['isCanBalik']) && $filter['isCanBalik'] != "" && $filter['isCanBalik'] != "undefined" && $filter['isCanBalik'] == "true") {
            $data = $data->whereNull('pd.tglpulang');
        } else {
            if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
                $data = $data->where('pd.tglregistrasi', '>=', $filter['tglAwal']);
            }
            if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
                $data = $data->where('pd.tglregistrasi', '<=', $filter['tglAkhir']);
            }
        }

        if (isset($filter['instalasiId']) && $filter['instalasiId'] != "" && $filter['instalasiId'] != "undefined") {
            $data = $data->where('dept.id', '=', $filter['instalasiId']);
        }
        if (isset($filter['ruangId']) && $filter['ruangId'] != "" && $filter['ruangId'] != "undefined") {
            $data = $data->where('ru.id', '=', $filter['ruangId']);
        }
        if (isset($filter['ruanganId']) && $filter['ruanganId'] != "" && $filter['ruanganId'] != "undefined") {
            $data = $data->where('ru.id', '=', $filter['ruanganId']);
        }

        if (isset($filter['kelompokPasienId']) && $filter['kelompokPasienId'] != "" && $filter['kelompokPasienId'] != "undefined") {
            $data = $data->where('kp.id', '=', $filter['kelompokPasienId']);
        }
        if (isset($filter['dokId']) && $filter['dokId'] != "" && $filter['dokId'] != "undefined") {
            $data = $data->where('pg.id', '=', $filter['dokId']);
        }
        if (isset($filter['sttts']) && $filter['sttts'] != "" && $filter['sttts'] != "undefined") {
            $data = $data->where('pd.statuspasien', '=', $filter['sttts']);
        }
        if (isset($filter['noReg']) && $filter['noReg'] != "" && $filter['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $filter['noReg']);
        }
        if (isset($filter['noRm']) && $filter['noRm'] != "" && $filter['noRm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $filter['noRm'] . '%');
        }
        if (isset($filter['namaPasien']) && $filter['namaPasien'] != "" && $filter['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['namaPasien'] . '%');
        }

        if (isset($filter['jmlRows']) && $filter['jmlRows'] != "" && $filter['jmlRows'] != "undefined") {
            $data = $data->take($filter['jmlRows']);
        }
        if ((isset($filter['isBlmInputSep']) && $filter['isBlmInputSep'] != "" && $filter['isBlmInputSep'] != "undefined" && $filter['isBlmInputSep'] != 'false')
            && (isset($filter['isSepTdkSesuai']) && $filter['isSepTdkSesuai'] != "" && $filter['isSepTdkSesuai'] != "undefined" && $filter['isSepTdkSesuai'] != 'false')
        ) {
            $data = $data->whereIn('kp.id', [10, 2, 4]);
            $data = $data->where(function ($query) {
                $query->where('pa.nosep', null)
                    ->Orwhere('pa.nosep', "")
                    ->Orwhere(function ($query2) {
                        $query2->where('dept.id', 16)
                            ->where('pa.ppkrujukan', '<>', "1124R004");
                    });
            });
        } elseif (isset($filter['isBlmInputSep']) && $filter['isBlmInputSep'] != "" && $filter['isBlmInputSep'] != "undefined" && $filter['isBlmInputSep'] != 'false') {
            $data = $data->whereIn('kp.id', [10, 2, 4]);
            $data = $data->where(function ($query) {
                $query->where('pa.nosep', null)
                    ->Orwhere('pa.nosep', "");
            });
        } elseif (isset($filter['isSepTdkSesuai']) && $filter['isSepTdkSesuai'] != "" && $filter['isSepTdkSesuai'] != "undefined" && $filter['isSepTdkSesuai'] != 'false') {
            $data = $data->whereIn('kp.id', [10, 2, 4]);
            $data = $data->where('dept.id', 16);
            $data = $data->where(function ($query) {
                $query->where('pa.nosep', '<>', null)
                    ->where('pa.nosep', '<>', "");
            });

            $data = $data->where('pa.ppkrujukan', '<>', "1124R004");
        }
        if (isset($filter['jenisPel']) && $filter['jenisPel'] != "" && $filter['jenisPel'] != "undefined") {
            $data = $data->where('pd.jenispelayanan', '=', $filter['jenisPel']);
        }

        $data = $data->orderBy('pd.noregistrasi');
        $data = $data->get();

        $data2 = [];
        foreach ($data as $key => $value) {
            if ($value->iddiagnosabpjs != null) {
                $value->isdiagnosis = true;
            } else {
                $value->isdiagnosis = false;
            }
            $data2[] = $value;
        }
        return $this->respond($data2);
    }

    public function getStatusClosePeriksa(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $noregistrasi = $request['noregistrasi'];
        $data = RegistrasiPasien::where('noregistrasi', $noregistrasi)->where('koders', (int)$kdProfile)->first();
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

    public function getdataAntrianPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('daftarpasienruangantr as apd')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->select(DB::raw("
                apd.registrasipasienfk,apd.norec AS norec_apd,apd.ruanganidfk,ru.namaruangan AS ruangan
            "))
            ->where('apd.aktif', true)
            ->where('pd.noregistrasi', $request['noregistrasi'])
            ->where('pd.koders', (int)$kdProfile)
            ->orderBy('apd.ruanganidfk')
            ->get();

        $result = array(
            'ruangan' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function cekNoBPJSpasienBaru(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $data = \DB::table('pasienmt as ps')
            ->select('ps.id', 'ps.norm as nocm', 'ps.namapasien', 'ps.nobpjs')
            ->where('ps.nobpjs', '=', $request['nobpjs'])
            ->where('ps.aktif', '=', true)
            ->where('ps.id', '<>', $request['idnocm'])
            ->where('ps.nobpjs', '=', $request['nobpjs'])
            ->take(1)
            ->where('ps.koders', $kdProfile)
            ->get();

        $result = array(
            'data' => $data,
            'message' => 'snaps',
        );

        return $this->respond($result);
    }

    public function getDiagnosaSaeutik(Request $request)
    {
        $req = $request->all();
        $datRek = \DB::table('icdxmt as dg')
            ->select(
                'dg.id',
                'dg.kddiagnosa',
                'dg.namadiagnosa',
                DB::raw("dg.kddiagnosa || '-' || dg.namadiagnosa as nama")
            )
            ->where('dg.aktif', true)
            ->orderBy('dg.kddiagnosa');

        if (
            isset($req['kddiagnosa']) &&
            $req['kddiagnosa'] != "" &&
            $req['kddiagnosa'] != "undefined"
        ) {
            $datRek = $datRek->where('dg.kddiagnosa', 'ilike', '%' . $req['kddiagnosa'] . '%');
        };
        if (
            isset($req['id']) &&
            $req['id'] != "" &&
            $req['id'] != "undefined"
        ) {
            $datRek = $datRek->where('dg.id', '=', $req['id']);
        };

        if (
            isset($req['namadiagnosa']) &&
            $req['namadiagnosa'] != "" &&
            $req['namadiagnosa'] != "undefined"
        ) {
            $datRek = $datRek->where('dg.namadiagnosa', 'ilike', '%' . $req['namadiagnosa'] . '%');
        };
        if (
            isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined"
        ) {
            $datRek = $datRek
                ->where('dg.kddiagnosa', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        }

        $datRek = $datRek->take(10);
        $datRek = $datRek->get();

        return $this->respond($datRek);
    }

    public function saveAsuransiPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            // $cekSEP = PemakaianAsuransi::where('registrasipasienfk', '!=', $request['pemakaianasuransi']['noregistrasifk'])
            //     ->where('nosep', $request['pemakaianasuransi']['nosep'])
            //     ->where('koders', $kdProfile)
            //     ->first();
            // if ($request['pemakaianasuransi']['nosep'] != '' && !empty($cekSEP)) {
            //     $transMessage = "No SEP sudah ada di pasien lain dengan no Kartu " . $cekSEP->nokepesertaan;
            //     DB::rollBack();
            //     $result = array(
            //         "status" => 400,
            //         "message" => $transMessage,
            //         "as" => 'slvR',
            //     );
            //     return $this->setStatusCode($result['status'])->respond($result, $transMessage);
            // }
            if ($request['asuransipasien']['kelompokpasien'] != 'null' || $request['asuransipasien']['kelompokpasien'] != 'undefined') {
                $updateKPS = RegistrasiPasien::where('noregistrasi', $request['asuransipasien']['noregistrasi'])
                    ->update(
                        [
                            'kelompokpasienlastidfk' => $request['asuransipasien']['kelompokpasien'],
                            'rekananidfk' => $request['asuransipasien']['kdpenjaminpasien'],
                        ]
                    );
            }
            if ($request['asuransipasien']['notelpmobile'] != '') {
                $updatepas = Pasien::where('norm', $request['asuransipasien']['nocm'])
                    ->update(
                        [
                            'notelepon' => $request['asuransipasien']['notelpmobile'],
                        ]
                    );
            }

            if ($request['asuransipasien']['id_ap'] == '' || $request['asuransipasien']['id_ap'] =='undefined') {
                $cekAsuransi = AsuransiPasien::where('normidfk', $request['asuransipasien']['nocmfkpasien'])
                    ->where('kdpenjaminpasien', $request['asuransipasien']['kdpenjaminpasien'])
                    ->where('koders', $kdProfile)
                    ->get();
                if (count($cekAsuransi) > 0) {
                    $dataUpdate = array(
                        'alamatlengkap' => $request['asuransipasien']['alamat'],
                        'hubunganpesertaidfk' => $request['asuransipasien']['objecthubunganpesertafk'],
                        'jeniskelaminidfk' => $request['asuransipasien']['objectjeniskelaminfk'],
                        'kdinstitusiasal' => $request['asuransipasien']['kdinstitusiasal'],
                        'notelpmobile' => $request['asuransipasien']['notelpmobile'],
                        'jenispeserta' => $request['asuransipasien']['jenispeserta'],
                        'kdprovider' => $request['asuransipasien']['kdprovider'],
                        'nmprovider' => $request['asuransipasien']['nmprovider'],
                        'kdpenjaminpasien' => $request['asuransipasien']['kdpenjaminpasien'],
                        'kelasdijaminidfk' => $request['asuransipasien']['objectkelasdijaminfk'],
                        'namapeserta' => $request['asuransipasien']['namapeserta'],
                        'nikinstitusiasal' => $request['asuransipasien']['nikinstitusiasal'],
                        'noasuransi' => $request['asuransipasien']['noasuransi'],
                        'noidentitas' => $request['asuransipasien']['noidentitas'],
                        'tgllahir' => $request['asuransipasien']['tgllahir'],
                    );
                    AsuransiPasien::where('normidfk', $request['asuransipasien']['nocmfkpasien'])
                        ->where('kdpenjaminpasien', $request['asuransipasien']['kdpenjaminpasien'])
                        ->where('koders', $kdProfile)
                        ->update($dataUpdate);
                    $newId = $cekAsuransi[0]->id;
                    $dataAP = AsuransiPasien::where('id', $newId)->first();
                } else {
                    $newId = AsuransiPasien::max('id');
                    $newId = $newId + 1;
                    $dataAP = new AsuransiPasien();
                    $dataAP->id = $newId;
                    $dataAP->koders = (int)$kdProfile;
                    $dataAP->aktif = true;
                    $dataAP->norec = $dataAP->generateNewId();
                    $dataAP->alamatlengkap = $request['asuransipasien']['alamat'];
                    $dataAP->hubunganpesertaidfk = $request['asuransipasien']['objecthubunganpesertafk'];
                    $dataAP->jeniskelaminidfk = $request['asuransipasien']['objectjeniskelaminfk'];
                    $dataAP->kdinstitusiasal = $request['asuransipasien']['kdinstitusiasal'];
                    $dataAP->notelpmobile = $request['asuransipasien']['notelpmobile'];
                    $dataAP->jenispeserta = $request['asuransipasien']['jenispeserta'];
                    $dataAP->kdprovider = $request['asuransipasien']['kdprovider'];
                    $dataAP->nmprovider = $request['asuransipasien']['nmprovider'];
                    $dataAP->kdpenjaminpasien = $request['asuransipasien']['kdpenjaminpasien'];
                    $dataAP->kelasdijaminidfk = $request['asuransipasien']['objectkelasdijaminfk'];
                    $dataAP->namapeserta = $request['asuransipasien']['namapeserta'];
                    $dataAP->nikinstitusiasal = $request['asuransipasien']['nikinstitusiasal'];
                    $dataAP->noasuransi = $request['asuransipasien']['noasuransi'];
                    $dataAP->normidfk = $request['asuransipasien']['nocmfkpasien'];
                    $dataAP->noidentitas = $request['asuransipasien']['noidentitas'];
                    $dataAP->tgllahir = $request['asuransipasien']['tgllahir'];
                    $dataAP->save();
                    $newId = $dataAP->id;
                }
            } else {
                $dataAP = AsuransiPasien::where('id', $request['asuransipasien']['id_ap'])->first();
                $newId = $dataAP->id;
                $dataAP->alamatlengkap = $request['asuransipasien']['alamat'];
                $dataAP->hubunganpesertaidfk = $request['asuransipasien']['objecthubunganpesertafk'];
                $dataAP->jeniskelaminidfk = $request['asuransipasien']['objectjeniskelaminfk'];
                $dataAP->kdinstitusiasal = $request['asuransipasien']['kdinstitusiasal'];
                $dataAP->notelpmobile = $request['asuransipasien']['notelpmobile'];
                $dataAP->jenispeserta = $request['asuransipasien']['jenispeserta'];
                $dataAP->kdprovider = $request['asuransipasien']['kdprovider'];
                $dataAP->nmprovider = $request['asuransipasien']['nmprovider'];
                $dataAP->kdpenjaminpasien = $request['asuransipasien']['kdpenjaminpasien'];
                $dataAP->kelasdijaminidfk = $request['asuransipasien']['objectkelasdijaminfk'];
                $dataAP->namapeserta = $request['asuransipasien']['namapeserta'];
                $dataAP->nikinstitusiasal = $request['asuransipasien']['nikinstitusiasal'];
                $dataAP->noasuransi = $request['asuransipasien']['noasuransi'];
                $dataAP->normidfk = $request['asuransipasien']['nocmfkpasien'];
                $dataAP->noidentitas = $request['asuransipasien']['noidentitas'];
                $dataAP->tgllahir = $request['asuransipasien']['tgllahir'];
                $dataAP->save();
            }

            $idAP = $newId;
            $cekPemakaian = PemakaianAsuransi::where('registrasipasienfk', $request['pemakaianasuransi']['noregistrasifk'])
                ->where('koders', $kdProfile)
                ->first();
            if (empty($cekPemakaian)) {
                $dataPA = new PemakaianAsuransi();
                $dataPA->norec = $dataPA->generateNewId();;
                $dataPA->koders = (int)$kdProfile;
                $dataPA->aktif = true;
                $dataPA->tglcreate = date('Y-m-d');
            } else {
                $dataPA = $cekPemakaian;
            }
            $dataPA->registrasipasienfk = $request['pemakaianasuransi']['noregistrasifk'];
            $dataPA->tglregistrasi = $request['pemakaianasuransi']['tglregistrasi'];
            $dataPA->icdxidfk = $request['pemakaianasuransi']['diagnosisfk'];
            $dataPA->lakalantas = $request['pemakaianasuransi']['lakalantas'];
            $dataPA->nokepesertaan = $request['pemakaianasuransi']['nokepesertaan'];
            $dataPA->norujukan = $request['pemakaianasuransi']['norujukan'];
            $dataPA->nosep = $request['pemakaianasuransi']['nosep'];
            $dataPA->ppkrujukan = $request['asuransipasien']['kdprovider'];
            $dataPA->tglrujukan = $request['pemakaianasuransi']['tglrujukan'];
            $dataPA->asuransiidfk = $idAP;
            $dataPA->tanggalsep = $request['pemakaianasuransi']['tanggalsep'];
            $dataPA->catatan = $request['pemakaianasuransi']['catatan'];
            $dataPA->lokasilakalantas = $request['pemakaianasuransi']['lokasilaka'];
            $dataPA->penjaminlaka = $request['pemakaianasuransi']['penjaminlaka'];
            $dataPA->asalrujukanfk = $request['pemakaianasuransi']['asalrujukanfk'];

            if (isset($request['pemakaianasuransi']['cob'])) {
                $dataPA->cob = $request['pemakaianasuransi']['cob'];
            }
            if (isset($request['pemakaianasuransi']['katarak'])) {
                $dataPA->katarak = $request['pemakaianasuransi']['katarak'];
            }
            if (isset($request['pemakaianasuransi']['keteranganlaka'])) {
                $dataPA->keteranganlaka = $request['pemakaianasuransi']['keteranganlaka'];
            }
            if (isset($request['pemakaianasuransi']['tglkejadian'])) {
                $dataPA->tglkejadian = $request['pemakaianasuransi']['tglkejadian'];
            }
            if (isset($request['pemakaianasuransi']['suplesi'])) {
                $dataPA->suplesi = $request['pemakaianasuransi']['suplesi'];
            }
            if (isset($request['pemakaianasuransi']['nosepsuplesi'])) {
                $dataPA->nosepsuplesi = $request['pemakaianasuransi']['nosepsuplesi'];
            }
            if (isset($request['pemakaianasuransi']['kdpropinsi'])) {
                $dataPA->kdpropinsi = $request['pemakaianasuransi']['kdpropinsi'];
            }
            if (isset($request['pemakaianasuransi']['namapropinsi'])) {
                $dataPA->namapropinsi = $request['pemakaianasuransi']['namapropinsi'];
            }
            if (isset($request['pemakaianasuransi']['kdkabupaten'])) {
                $dataPA->kdkabupaten = $request['pemakaianasuransi']['kdkabupaten'];
            }
            if (isset($request['pemakaianasuransi']['namakabupaten'])) {
                $dataPA->namakabupaten = $request['pemakaianasuransi']['namakabupaten'];
            }
            if (isset($request['pemakaianasuransi']['kdkecamatan'])) {
                $dataPA->kdkecamatan = $request['pemakaianasuransi']['kdkecamatan'];
            }
            if (isset($request['pemakaianasuransi']['namakecamatan'])) {
                $dataPA->namakecamatan = $request['pemakaianasuransi']['namakecamatan'];
            }
            if (isset($request['pemakaianasuransi']['nosuratskdp'])) {
                $dataPA->nosuratskdp = $request['pemakaianasuransi']['nosuratskdp'];
            }
            if (isset($request['pemakaianasuransi']['kodedpjp'])) {
                $dataPA->kodedpjp = $request['pemakaianasuransi']['kodedpjp'];
                if (!isset($request['pemakaianasuransi']['kodedpjpmelayani'])) {
                    $dataPA->kodedpjpmelayani = $request['pemakaianasuransi']['kodedpjp'];
                }
            }
            if (isset($request['pemakaianasuransi']['namadpjp'])) {
                $dataPA->namadpjp = $request['pemakaianasuransi']['namadpjp'];
                if (!isset($request['pemakaianasuransi']['namadjpjpmelayanni'])) {
                    $dataPA->namadjpjpmelayanni = $request['pemakaianasuransi']['namadpjp'];
                }
            }
            if (isset($request['pemakaianasuransi']['prolanisprb'])) {
                $dataPA->prolanisprb = $request['pemakaianasuransi']['prolanisprb'];
            }
            if (isset($request['pemakaianasuransi']['polirujukankode'])) {
                $dataPA->polirujukankode = $request['pemakaianasuransi']['polirujukankode'];
            }
            if (isset($request['pemakaianasuransi']['polirujukannama'])) {
                $dataPA->polirujukannama = $request['pemakaianasuransi']['polirujukannama'];
            }
            if (isset($request['pemakaianasuransi']['kodedpjpmelayani'])) {
                $dataPA->kodedpjpmelayani = $request['pemakaianasuransi']['kodedpjpmelayani'];
            }
            if (isset($request['pemakaianasuransi']['namadjpjpmelayanni'])) {
                $dataPA->namadjpjpmelayanni = $request['pemakaianasuransi']['namadjpjpmelayanni'];
            }
            $pemA = $request['pemakaianasuransi'];
            if(isset($pemA['klsrawatnaik'])) {
                $dataPA->klsrawatnaik = $pemA['klsrawatnaik'];
            }
            if(isset($pemA['pembiayaan'])) {
                $dataPA->pembiayaan = $pemA['pembiayaan'];
            }
            if(isset($pemA['penanggungjawab'])) {
                $dataPA->penanggungjawab = $pemA['penanggungjawab'];
            }
            if(isset($pemA['tujuankunj'])) {
                $dataPA->tujuankunj = $pemA['tujuankunj'];
            }
            if(isset($pemA['flagprocedure'])) {
                $dataPA->flagprocedure = $pemA['flagprocedure'];
            }
            if(isset($pemA['kdpenunjang'])) {
                $dataPA->kdpenunjang = $pemA['kdpenunjang'];
            }
            if(isset($pemA['assesmentpel'])) {
                $dataPA->assesmentpel = $pemA['assesmentpel'];
            }
            if(isset($pemA['statuskunjungan'])) {
                $dataPA->statuskunjungan = $pemA['statuskunjungan'];
            }
            if(isset($pemA['poliasalkode'])) {
                $dataPA->poliasalkode = $pemA['poliasalkode'];
            }
            if(isset($pemA['politujuankode'])) {
                $dataPA->politujuankode = $pemA['politujuankode'];
            }

            $dataPA->save();


            if (
                isset($request['asuransipasien']['tgllahir']) && $request['asuransipasien']['tgllahir'] != null
                && isset($request['asuransipasien']['noasuransi']) && $request['asuransipasien']['noasuransi'] != null
            ) {
                $pasien = Pasien::where('id', $request['asuransipasien']['nocmfkpasien'])->update(
                    [
                        'tgllahir' => $request['asuransipasien']['tgllahir'],
                        'nobpjs' => $request['asuransipasien']['noasuransi'],
                        'noidentitas' => $request['asuransipasien']['noidentitas']

                    ]
                );
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }


        if ($transStatus == 'true') {
            $transMessage = "Simpan Asuransi Pasien";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $transMessage,
                'PA' => $dataPA,
                'AP' => $dataAP,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = "Gagal Simpan Asuransi Pasien";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $transMessage,
                'e' => $e->getMessage(),
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getAntrianPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        if ($request['objectruanganlastfk'] != "") {
            $noreg = $request['noregistrasi'];
            $ruanganLast = $request['objectruanganlastfk'];
            $data = collect(DB::select("
            select x.* from (
             select apd.norec as norec_apd,
             row_number() over (partition by pd.noregistrasi order by apd.tglmasuk desc) as rownum
             from daftarpasienruangantr as apd
             inner join registrasipasientr as pd on pd.norec = apd.registrasipasienfk
             where apd.aktif = true and apd.koders = $idProfile  and pd.noregistrasi = '$noreg' and apd.ruanganidfk = '$ruanganLast'
             ) as x "))->first();
        } else {
            $data = \DB::table('daftarpasienruangantr as apd')
                ->join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
                ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
                ->join('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
                ->select(
                    'apd.norec as norec_apd',
                    'ps.nocm',
                    'ps.id as nocmfk',
                    'ps.namapasien',
                    'pd.noregistrasi',
                    'apd.ruanganidfk as objectruanganfk',
                    'ru.namaruangan',
                    'apd.tglregistrasi',
                    'kls.namakelas',
                    'apd.ruanganasalidfk as objectruanganasalfk'
                )
                ->where('pd.noregistrasi', $request['noregistrasi'])
                ->where('apd.koders', (int)$kdProfile)
                ->whereNull('apd.ruanganasalidfk')
                ->first();
        }


        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getPsnByNoCm(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('pasienmt as ps')
            ->leftjoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftjoin('pendidikanmt as pdd', 'ps.pendidikanidfk', '=', 'pdd.id')
            ->leftjoin('pekerjaanmt as pk', 'ps.pekerjaanidfk', '=', 'pk.id')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->leftjoin('agamamt as agm', 'agm.id', '=', 'ps.agamaidfk')
            ->leftjoin('statusperkawinanmt as sp', 'sp.id', '=', 'ps.statusperkawinanidfk')
            ->leftjoin('kebangsaanmt as kb', 'kb.id', '=', 'ps.kebangsaanidfk')
            ->leftjoin('negaramt as ng', 'ng.id', '=', 'alm.negaraidfk')
            ->leftjoin('desakelurahanmt as dsk', 'dsk.id', '=', 'alm.desakelurahanidfk')
            ->leftjoin('kecamatanmt as kcm', 'kcm.id', '=', 'alm.kecamatanidfk')
            ->leftjoin('kotakabupatenmt as kkb', 'kkb.id', '=', 'alm.kotakabupatenidfk')
            ->leftjoin('provinsimt as prp', 'prp.id', '=', 'alm.provinsiidfk')
            ->leftjoin('pekerjaanmt as pk1', 'ps.pekerjaanpenangggungjawab', '=', 'pk1.pekerjaan')
            ->leftjoin('sukumt as sk', 'sk.id', '=', 'ps.sukuidfk')
            ->leftjoin('golongandarahmt as gol', 'gol.id', '=', 'ps.golongandarahidfk')
            ->leftjoin('jeniskelaminmt as jk1', 'jk1.jeniskelamin', '=', 'ps.jeniskelaminpenanggungjawab')
            ->select(
                'ps.norm as nocm',
                'ps.id as nocmfk',
                'ps.namapasien',
                'ps.tgllahir',
                'ps.tempatlahir',
                'ps.jeniskelaminidfk as objectjeniskelaminfk',
                'jk.jeniskelamin',
                'ps.agamaidfk as objectagamafk',
                'agm.agama',
                'ps.statusperkawinanidfk as objectstatusperkawinanfk',
                'sp.statusperkawinan',
                'ps.pendidikanidfk as objectpendidikanfk',
                'pdd.pendidikan',
                'ps.pekerjaanidfk as objectpekerjaanfk',
                'pk.pekerjaan',
                'ps.kebangsaanidfk as objectkebangsaanfk',
                'kb.name as kebangsaan',
                'alm.negaraidfk as objectnegarafk',
                'ng.namanegara',
                'ps.noidentitas',
                'ps.nobpjs',
                'ps.noasuransilain',
                'alm.alamatlengkap',
                'alm.kodepos',
                'alm.desakelurahanidfk as objectdesakelurahanfk',
                'dsk.namadesakelurahan',
                'alm.kecamatanidfk as objectkecamatanfk',
                'kcm.namakecamatan',
                'alm.kotakabupatenidfk as objectkotakabupatenfk',
                'kkb.namakotakabupaten',
                'alm.provinsiidfk as objectpropinsifk',
                'prp.namapropinsi',
                'ps.notelepon',
                'ps.nohp',
                'ps.namaayah',
                'ps.namaibu',
                'ps.namakeluarga',
                'ps.namasuamiistri',
                'ps.penanggungjawab',
                'ps.hubungankeluargapj',
                'ps.pekerjaanpenangggungjawab',
                'ps.ktppenanggungjawab',
                'ps.alamatrmh',
                'ps.alamatktr',
                'pk1.id as idpek',
                'ps.photo',
                'ps.foto',
                'ps.golongandarahidfk as objectgolongandarahfk',
                'gol.golongandarah',
                'ps.sukuidfk as objectsukufk',
                'sk.suku',
                'ps.bahasa',
                'ps.teleponpenanggungjawab',
                'ps.dokterpengirim',
                'ps.alamatdokterpengirim',
                'jk1.id as jkidpenanggungjawab',
                'ps.jeniskelaminpenanggungjawab',
                'ps.umurpenanggungjawab'
            )
            ->where('ps.koders', (int)$kdProfile);

        if (isset($request['noCm']) && $request['noCm'] != '' && $request['noCm'] != 'undefined') {
            $data = $data->where('ps.nocm', $request['noCm']);
        }
        if (isset($request['idPasien']) && $request['idPasien'] != '' && $request['idPasien'] != 'undefined') {
            $data = $data->where('ps.id', $request['idPasien']);
        }

        $data = $data->first();
        $foto = null;
        $dt = array(
            'nocm' => $data->nocm,
            'nocmfk' => $data->nocmfk,
            'namapasien' => $data->namapasien,
            'tgllahir' => $data->tgllahir,
            'tempatlahir' => $data->tempatlahir,
            'objectjeniskelaminfk' => $data->objectjeniskelaminfk,
            'jeniskelamin' => $data->jeniskelamin,
            'objectagamafk' => $data->objectagamafk,
            'agama' => $data->agama,
            'objectstatusperkawinanfk' => $data->objectstatusperkawinanfk,
            'statusperkawinan' => $data->statusperkawinan,
            'objectpendidikanfk' => $data->objectpendidikanfk,
            'pendidikan' => $data->pendidikan,
            'objectpekerjaanfk' => $data->objectpekerjaanfk,
            'pekerjaan' => $data->pekerjaan,
            'objectkebangsaanfk' => $data->objectkebangsaanfk,
            'kebangsaan' => $data->kebangsaan,
            'objectnegarafk' => $data->objectnegarafk,
            'namanegara' => $data->namanegara,
            'noidentitas' => $data->noidentitas,
            'nobpjs' => $data->nobpjs,
            'noasuransilain' => $data->noasuransilain,
            'alamatlengkap' => $data->alamatlengkap,
            'kodepos' => $data->kodepos,
            'objectdesakelurahanfk' => $data->objectdesakelurahanfk,
            'namadesakelurahan' => $data->namadesakelurahan,
            'objectkecamatanfk' => $data->objectkecamatanfk,
            'namakecamatan' => $data->namakecamatan,
            'objectkotakabupatenfk' => $data->objectkotakabupatenfk,
            'namakotakabupaten' => $data->namakotakabupaten,
            'objectpropinsifk' => $data->objectpropinsifk,
            'namapropinsi' => $data->namapropinsi,
            'notelepon' => $data->notelepon, 'nohp' => $data->nohp,
            'namaayah' => $data->namaayah, 'namaibu' => $data->namaibu,
            'namakeluarga' => $data->namakeluarga, 'namasuamiistri' => $data->namasuamiistri,
            'penanggungjawab' => $data->penanggungjawab,
            'hubungankeluargapj' => $data->hubungankeluargapj,
            'pekerjaanpenangggungjawab' => $data->pekerjaanpenangggungjawab,
            'ktppenanggungjawab' => $data->ktppenanggungjawab,
            'alamatrmh' => $data->alamatrmh,
            'alamatktr' => $data->alamatktr, 'idpek' => $data->idpek,
            'foto' => $foto,
            'photo' => $data->photo,
            'suku' => $data->suku,
            'objectsukufk' => $data->objectsukufk,
            'golongandarah' => $data->golongandarah,
            'objectgolongandarahfk' => $data->objectgolongandarahfk,
            'bahasa' => $data->bahasa,
            'teleponpenanggungjawab' => $data->teleponpenanggungjawab,
            'dokterpengirim' => $data->dokterpengirim,
            'alamatdokterpengirim' => $data->alamatdokterpengirim,
            'jkidpenanggungjawab' => $data->jkidpenanggungjawab,
            'jeniskelaminpenanggungjawab' => $data->jeniskelaminpenanggungjawab,
            'umurpenanggungjawab' => $data->umurpenanggungjawab,
        );
        $result = array(
            'data' => $dt,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getPasienPerjanjian(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('perjanjianpasientr as apr')
            ->leftJoin('pasienmt as pm', 'pm.id', '=', 'apr.normidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'apr.ruanganidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'apr.pegawaiidfk')
            ->leftJoin('kelompokpasienmt as kps', 'kps.id', '=', 'apr.kelompokpasienidfk')
            ->select(
                'apr.norec',
                'pm.norm as nocm',
                'apr.noreservasi',
                'apr.tanggalreservasi',
                'apr.ruanganidfk as objectruanganfk',
                'apr.pegawaiidfk as objectpegawaifk',
                'ru.namaruangan',
                'apr.isconfirm',
                'pg.namalengkap as dokter',
                'apr.notelepon',
                'pm.namapasien',
                'apr.namapasien',
                'apr.kelompokpasienidfk as objectkelompokpasienfk',
                'kps.kelompokpasien',
                'apr.tglinput',
                'apr.normidfk as nocmfk',
                DB::raw('(case when pm.namapasien is null then apr.namapasien else pm.namapasien end) as namapasien,
                (case when apr.isconfirm=\'true\' then \'Confirm\' else \'Reservasi\' end) as status')
            )
            ->where('apr.noreservasi', '<>', '-')
            ->where('apr.aktif', true)
            ->where('apr.koders', (int)$kdProfile)
            ->whereNotNull('apr.noreservasi');

        $filter = $request;
        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
            $data = $data->where('apr.tanggalreservasi', '>=', $filter['tglAwal'] . " 00:00:00");
        }
        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
            $tgl = $filter['tglAkhir'] . " 23:59:59"; //. " 23:59:59";
            $data = $data->where('apr.tanggalreservasi', '<=', $tgl);
        }
        if (isset($filter['ruanganId']) && $filter['ruanganId'] != "" && $filter['ruanganId'] != "undefined") {
            $data = $data->where('ru.id', '=', $filter['ruanganId']);
        }
        if (isset($filter['kdReservasi']) && $filter['kdReservasi'] != "" && $filter['kdReservasi'] != "undefined") {
            $data = $data->where('apr.noreservasi', '=', $filter['kdReservasi']);
        }
        if (isset($filter['statusRev']) && $filter['statusRev'] == "Confirm" && $filter['statusRev'] == "Confirm" && $filter['statusRev'] == "Confirm") {
            $data = $data->where('apr.isconfirm', '=', true);
        }
        if (isset($filter['statusRev']) && $filter['statusRev'] == "Reservasi" && $filter['statusRev'] == "Reservasi" && $filter['statusRev'] == "Reservasi") {
            $data = $data->whereNull('apr.isconfirm');
        }
        if (isset($filter['namapasienpm']) && $filter['namapasienpm'] != "" && $filter['namapasienpm'] != "undefined") {
            $data = $data->where('pm.namapasien', 'ilike', '%' . $filter['namapasienpm'] . '%');
            // ->orWhere('apr.namapasien','ilike','%'. $filter['namapasienapr'] .'%');
        }
        if (isset($filter['noRM']) && $filter['noRM'] != "" && $filter['noRM'] != "undefined") {
            $data = $data->where('pm.norm', 'ilike', '%' . $filter['noRM'] . '%');
        }
        $data = $data->orderBy('apr.tanggalreservasi', 'asc');
        $data = $data->get();

        $result = array(
            'data' => $data,
            'message' => 'er',
        );
        return $this->respond($result);
    }

    public function getComboPasienPerjanjian(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenRawatJalanFix', $kdProfile));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item) {
            $kdDepartemenRawatJalan[] = (int)$item;
        }

        $dataRuanganJalan = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk as objectdepartemenfk')
            ->where('ru.aktif', true)
            ->wherein('ru.instalasiidfk', $kdDepartemenRawatJalan)
            ->where('ru.koders', (int)$kdProfile)
            ->orderBy('ru.namaruangan')
            ->get();

        $result = array(
            'ruanganrajal' => $dataRuanganJalan,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function getPasienOnlineByNorec($noreservasi, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('perjanjianpasientr as apr')
            ->leftjoin('pasienmt as ps', 'ps.id', '=', 'apr.normidfk')
            ->leftjoin('agamamt as agm', 'agm.id', '=', 'apr.agamaidfk')
            ->leftjoin('rujukanasalmt as aru', 'aru.id', '=', 'apr.asalrujukanidfk')
            ->leftjoin('desakelurahanmt as ds', 'ds.id', '=', 'apr.desakelurahanidfk')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'apr.jeniskelaminidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'apr.pegawaiidfk')
            ->leftjoin('pekerjaanmt as pkj', 'pkj.id', '=', 'apr.pekerjaanidfk')
            ->leftjoin('pendidikanmt as pdd', 'pdd.id', '=', 'apr.pendidikanidfk')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'apr.ruanganidfk')
            ->leftjoin('statusperkawinanmt as st', 'st.id', '=', 'apr.statusperkawinanidfk')
            ->leftjoin('kelompokpasienmt as kps', 'kps.id', '=', 'apr.kelompokpasienidfk')
            ->select(
                'apr.norec',
                'apr.noreservasi',
                'apr.normidfk as nocmfk',
                'ps.norm as nocm',
                'apr.namapasien',
                'apr.alamatlengkap',
                'apr.namaayah',
                'apr.namaibu',
                'apr.namasuamiistri',
                'apr.noaditional',
                'apr.noasuransilain',
                'apr.noidentitas',
                'apr.notelepon',
                'apr.nobpjs',
                'apr.tempatlahir',
                'apr.tgllahir',
                'apr.tanggalreservasi',
                'apr.tipepasien',
                'apr.agamaidfk as objectagamafk',
                'agm.agama',
                'apr.asalrujukanidfk as objectasalrujukanfk',
                'aru.asalrujukan',
                'apr.desakelurahanidfk as objectdesakelurahanfk',
                'ds.namadesakelurahan',
                'apr.isconfirm',
                'apr.jeniskelaminidfk as objectjeniskelaminfk',
                'jk.jeniskelamin',
                'apr.pegawaiidfk as objectpegawaifk',
                'pg.namalengkap as dokter',
                'apr.pekerjaanidfk as objectpekerjaanfk',
                'pkj.pekerjaan',
                'apr.pendidikanidfk as objectpendidikanfk',
                'pdd.pendidikan',
                'apr.ruanganidfk as objectruanganfk',
                'ru.namaruangan as ruangantujuan',
                'apr.statusperkawinanidfk as objectstatusperkawinanfk',
                'st.statusperkawinan',
                'apr.kelompokpasienidfk as objectkelompokpasienfk',
                'kps.kelompokpasien'
            )
            ->where('apr.norec', $noreservasi)
            ->where('apr.koders', (int)$kdProfile)
            ->where('apr.aktif', true)
            ->first();

        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function ConfirmOnline(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        try {
            $dataApr = PerjanjianPasien::where('noreservasi', $request['noreservasi'])
                ->where('koders', (int)$kdProfile)
                ->update([
                    'isconfirm' => true,
                ]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }


        if ($transStatus == 'true') {
            $transMessage = "Confirm Pasien Online";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
            );
        } else {
            $transMessage = "Confirm Pasien Online Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function simpanUpdateDokterRegistrasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        \Illuminate\Support\Facades\DB::beginTransaction();
        $transStatus = 'true';
        try {
            $data = RegistrasiPasien::where('norec', $request['norec_pd'])
                ->where('koders', $kdProfile)
                ->update([
                    'pegawaiidfk' => $request['pegawaiidfk'],
                    'dokterpemeriksaidfk' => $request['pegawaiidfk'],
                ]);
            $transMessage = "Simpan Dokter Berhasil!";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Simpan Dokter Gagal";
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

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDiagnosaICDPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $dataICDXPasien = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->join('diagnosapasienicdxtr  as dp', 'dp.daftarpasienruanganfk', '=', 'apd.norec')
            ->leftJoin('icdxmt as dg', 'dg.id', '=', 'dp.icdxidfk')
            ->leftJoin('jenisdiagnosamt as jd', 'jd.id', '=', 'dp.jenisdiagnosaidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'dp.pegawaiidfk')
            ->select(DB::raw("
                dp.norec,dp.tglinputdiagnosa,
			    dp.jenisdiagnosaidfk,jd.jenisdiagnosa,dp.icdxidfk,dg.kddiagnosa,dg.namadiagnosa,
			    dg.kddiagnosa|| '~' || dg.namadiagnosa AS diagnosa,dp.ketdiagnosis,apd.ruanganidfk,ru.namaruangan,
			    dp.pegawaiidfk,pg.namalengkap
            "))
            ->where('pd.koders', (int)$kdProfile)
            ->where('pd.aktif', true)
            ->where('apd.aktif', true)
            ->where('dp.aktif', true);

        if (isset($request['norec_pd']) && $request['norec_pd'] != "" && $request['norec_pd'] != "undefined") {
            $dataICDXPasien = $dataICDXPasien->where('dp.registrasipasienfk', '=', $request['norec_pd']);
        };
        if (isset($request['norec_apd']) && $request['norec_apd'] != "" && $request['norec_apd'] != "undefined") {
            $dataICDXPasien = $dataICDXPasien->where('dp.daftarpasienruanganfk', '=', $request['norec_apd']);
        };
        $dataICDXPasien = $dataICDXPasien->get();

        $dataICDIXPasien = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->join('diagnosapasienicdixtr  as dp', 'dp.daftarpasienruanganfk', '=', 'apd.norec')
            ->leftJoin('icdixmt as dg', 'dg.id', '=', 'dp.icdixidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'dp.pegawaiidfk')
            ->select(DB::raw("
                dp.norec,dp.tglinputdiagnosa,dp.icdixidfk,dg.kddiagnosa,dg.namadiagnosa,
			    dg.kddiagnosa|| '~' || dg.namadiagnosa AS diagnosa,dp.keterangantindakan,
			    apd.ruanganidfk,ru.namaruangan,dp.pegawaiidfk,pg.namalengkap
            "))
            ->where('pd.koders', (int)$kdProfile)
            ->where('pd.aktif', true)
            ->where('apd.aktif', true)
            ->where('dp.aktif', true);

        if (isset($request['norec_pd']) && $request['norec_pd'] != "" && $request['norec_pd'] != "undefined") {
            $dataICDIXPasien = $dataICDIXPasien->where('dp.registrasipasienfk', '=', $request['norec_pd']);
        };
        if (isset($request['norec_apd']) && $request['norec_apd'] != "" && $request['norec_apd'] != "undefined") {
            $dataICDIXPasien = $dataICDIXPasien->where('dp.daftarpasienruanganfk', '=', $request['norec_apd']);
        };
        $dataICDIXPasien = $dataICDIXPasien->get();

        $result = array(
            'dataicdxpasien' => $dataICDXPasien,
            'dataicdixpasien' => $dataICDIXPasien,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function deleteDiagnosaPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['tipe'] == "icdx") {
                $data1 = DiagnosaPasien::where('norec', $request['norec_dp'])
                    ->where('koders', (int)$kdProfile)
                    ->update([
                        'aktif' => false,
                    ]);
            } elseif ($request['tipe'] == "icdix") {
                $data2 = DiagnosaTindakanPasien::where('norec', $request['norec_dp'])
                    ->where('koders', (int)$kdProfile)
                    ->update([
                        'aktif' => false,
                    ]);
            }

            $transStatus = 'true';
            $transMessage = "Hapus Data Berhasil!";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Hapus Data Gagal!";
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

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function saveDiagnosaPasienICDX(Request $request)
    {
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataPegawaiUser = DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.id=:idLoginUser and lu.kdprofile = $kdProfile limit 1"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        DB::beginTransaction();
        try {
            if ($request['norec'] == "") {
                $dataDiagnosa = new DiagnosaPasien();
                $dataDiagnosa->norec = $dataDiagnosa->generateNewId();
                $dataDiagnosa->koders = $kdProfile;
                $dataDiagnosa->aktif = true;
                $dataDiagnosa->daftarpasienruanganfk = $request['norec_apd'];
                $dataDiagnosa->registrasipasienfk = $request['norec_pd'];
                $dataDiagnosa->tglinputdiagnosa = $tglAyeuna;
            } else {
                $dataDiagnosa = DiagnosaPasien::where('norec', $request['norec'])->first();
            }
            $dataDiagnosa->jenisdiagnosaidfk = $request['jenisdiagnosaidfk'];
            $dataDiagnosa->icdxidfk = $request['icdxidfk'];
            $dataDiagnosa->ketdiagnosis = $request['keterangan'];
            $dataDiagnosa->pegawaiidfk = $dataPegawaiUser[0]->id;
            $dataDiagnosa->save();
            $norecData = $dataDiagnosa->norec;

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Selesai";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $transMessage,
                'data' => $norecData,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = "Gagal Coba Lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $transMessage,
                'data' => $norecData,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function saveDiagnosaPasienICDIX(Request $request)
    {
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataPegawaiUser = DB::select(
            DB::raw("select pg.id,pg.namalengkap from loginuser_s as lu
                INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                where lu.id=:idLoginUser and lu.kdprofile = $kdProfile limit 1"),
            array(
                'idLoginUser' => $dataLogin['userData']['id'],
            )
        );
        DB::beginTransaction();
        try {
            if ($request['norec'] == "") {
                $dataDiagnosa = new DiagnosaTindakanPasien();
                $dataDiagnosa->norec = $dataDiagnosa->generateNewId();
                $dataDiagnosa->koders = $kdProfile;
                $dataDiagnosa->aktif = true;
                $dataDiagnosa->daftarpasienruanganfk = $request['norec_apd'];
                $dataDiagnosa->registrasipasienfk = $request['norec_pd'];
                $dataDiagnosa->tglinputdiagnosa = $tglAyeuna;
            } else {
                $dataDiagnosa = DiagnosaTindakanPasien::where('norec', $request['norec'])->first();
            }
            $dataDiagnosa->icdixidfk = $request['icdixidfk'];
            $dataDiagnosa->keterangantindakan = $request['keterangan'];
            $dataDiagnosa->pegawaiidfk = $dataPegawaiUser[0]->id;
            $dataDiagnosa->save();
            $norecData = $dataDiagnosa->norec;

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Simpan Gagal";
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Berhasil";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $transMessage,
                'data' => $norecData,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = "Simpan Gagal";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $transMessage,
                'data' => $norecData,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDaftarMutasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $filter = $request->all();
        $deptRanap = explode(',', $this->settingDataFixed('kdDepartemenMutasi', $kdProfile));
        $kd = [];
        foreach ($deptRanap as $itemRanap) {
            $kd[] = (int)$itemRanap;
        }
        $data = \DB::table('registrasipasientr as pd')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'pd.pegawaiidfk')
            ->join('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
            ->join('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->leftJoin('strukpelayanantr as sp', 'sp.norec', '=', 'pd.nostruklastidfk')
            ->leftJOIN('jenispelayananmt as jpl', 'pd.jenispelayananidfk', '=', 'jpl.id')
            ->select(DB::raw("
                pd.norec,pd.tglregistrasi,ps.norm,pd.normidfk,pd.noregistrasi,ru.namaruangan,ps.namapasien,
			    kp.kelompokpasien,pg.namalengkap AS namadokter,pd.tglpulang,pd.statuspasien,
			   pd.pegawaiidfk AS pgid,pd.ruanganlastidfk,
			    pd.nostruklastidfk,kls.namakelas,ps.tgllahir,ru.instalasiidfk,
			    pd.kelasidfk,jpl.jenispelayanan,pd.isclosing
            "))
            ->where('pd.aktif', true)
            ->whereNotNull('pd.statuspulangidfk')
            ->whereIn('ru.instalasiidfk', $kd)
            ->where('pd.koders', (int)$kdProfile);
        if (isset($filter['isCanBalik']) && $filter['isCanBalik'] != "" && $filter['isCanBalik'] != "undefined" && $filter['isCanBalik'] == "true") {
            $data = $data->whereNull('pd.tglpulang');
        } else {
            if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
                $data = $data->where('pd.tglregistrasi', '>=', $filter['tglAwal']);
            }
            if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
                $data = $data->where('pd.tglregistrasi', '<=', $filter['tglAkhir']);
            }
        }

        if (isset($filter['instalasiId']) && $filter['instalasiId'] != "" && $filter['instalasiId'] != "undefined") {
            $data = $data->where('dept.id', '=', $filter['instalasiId']);
        }
        if (isset($filter['ruanganId']) && $filter['ruanganId'] != "" && $filter['ruanganId'] != "undefined") {
            $data = $data->where('ru.id', '=', $filter['ruanganId']);
        }
        if (isset($filter['kelompokPasienId']) && $filter['kelompokPasienId'] != "" && $filter['kelompokPasienId'] != "undefined") {
            $data = $data->where('kp.id', '=', $filter['kelompokPasienId']);
        }
        if (isset($filter['dokId']) && $filter['dokId'] != "" && $filter['dokId'] != "undefined") {
            $data = $data->where('pg.id', '=', $filter['dokId']);
        }
        if (isset($filter['sttts']) && $filter['sttts'] != "" && $filter['sttts'] != "undefined") {
            $data = $data->where('pd.statuspasien', '=', $filter['sttts']);
        }
        if (isset($filter['noreg']) && $filter['noreg'] != "" && $filter['noreg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $filter['noreg']);
        }
        if (isset($filter['noReg']) && $filter['noReg'] != "" && $filter['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $filter['noReg']);
        }

        if (isset($filter['norm']) && $filter['norm'] != "" && $filter['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $filter['norm'] . '%');
        }
        if (isset($filter['noRm']) && $filter['noRm'] != "" && $filter['noRm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $filter['noRm'] . '%');
        }

        if (isset($filter['nama']) && $filter['nama'] != "" && $filter['nama'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['nama'] . '%');
        }
        if (isset($filter['namaPasien']) && $filter['namaPasien'] != "" && $filter['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['namaPasien'] . '%');
        }


        if (isset($filter['jmlRows']) && $filter['jmlRows'] != "" && $filter['jmlRows'] != "undefined") {
            $data = $data->take($filter['jmlRows']);
        }
        if (isset($filter['jenisPel']) && $filter['jenisPel'] != "" && $filter['jenisPel'] != "undefined") {
            $data = $data->where('pd.jenispelayanan', '=', $filter['jenisPel']);
        }

        $data = $data->orderBy('pd.noregistrasi');
        $data = $data->get();

        $data2 = [];
        foreach ($data as $key => $value) {
            $value->isdiagnosis = false;
            $data2[] = $value;
        }
        return $this->respond($data2);
    }

    public function updateSEPIGD(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();

        try {
            PemakaianAsuransi::where('registrasipasienfk', $request['norec'])
                ->where('koders', (int)$kdProfile)
                ->update([
                    'nosep' => ''
                ]);
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus != 'false') {
            $transMessage = "Update SEP";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
            );
        } else {
            $transMessage = "Update SEP Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function saveMutasiPasien(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            $dokterId = null;
            if (isset($request['pegawai']['id'])) {
                $dokterId = $request['pegawai']['id'];
            }
            if ($request['tglpulang'] != 'null') {
                $ddddd = RegistrasiPasien::where('norec', $request['noRecPasienDaftar'])
                    ->update(
                        [
                            'tglpulang' => null,
                            'ruanganlastidfk' => $request['ruangan']['id'],
                            'kelompokpasienlastidfk' => $request['kelompokPasien']['id'],
                            'pegawaiidfk' => $dokterId, //$request['pegawai']['id'],
                            'kelasidfk' => $request['kelas']['id'],
                            'nostruklastidfk' => null,
                            'nosbmlastidfk' => null
                        ]
                    );
            }

            $countNoAntrian = DaftarPasienRuangan::where('ruanganidfk', $request['ruangan']['id'])
                ->where('koders', $kdProfile)
                ->where('tglregistrasi', '>=', $request['tglRegisDateOnly'] . ' 00:00')
                ->where('tglregistrasi', '<=', $request['tglRegisDateOnly'] . ' 23:59')
                ->where('aktif', true)
                ->max('noantrian');
            $noAntrian = $countNoAntrian + 1;

            $dataAPD = new DaftarPasienRuangan();
            $dataAPD->koders = $kdProfile;
            $dataAPD->aktif = true;
            $dataAPD->norec = $dataAPD->generateNewId();
            $dataAPD->asalrujukanidfk = $request['asalRujukan']['id'];
            $dataAPD->kamaridfk = $request['kamar']['id'];
            $dataAPD->kelasidfk = $request['kelas']['id'];
            $dataAPD->noantrian = $noAntrian;
            $dataAPD->nobedidfk = $request['nomorTempatTidur']['id'];
            $dataAPD->registrasipasienfk = $request['noRecPasienDaftar'];
            $dataAPD->pegawaiidfk = $dokterId;
            $dataAPD->ruanganidfk = $request['ruangan']['id'];
            $dataAPD->statusantrian = 0;
            $dataAPD->statuskunjungan = $request['statusPasien'];
            $dataAPD->statuspasien = 'Mutasi Gawat Darurat';
            $dataAPD->tglregistrasi = $request['tglRegistrasi'];
            $dataAPD->ruanganasalidfk = $request['objectruanganasalfk'];
            $dataAPD->tglkeluar = null;
            $dataAPD->tglmasuk = $request['tglRegistrasi'];
            $dataAPD->israwatgabung = $request['rawatGabung'];;
            $dataAPD->save();

            $set = $this->settingDataFixed('kdStatusBedIsi', $kdProfile);
            TempatTidur::where('id', $request['nomorTempatTidur']['id'])->update(['statusbedidfk' => $set]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Simpan Mutasi";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $transMessage,
                'data' => $dataAPD,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = "Simpan Mutasi Gagal";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $transStatus,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDataPasienMauBatal(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $filter = $request->all();
        $data = \DB::table('registrasipasientr as pd')
            ->leftJoin('daftarpasienruangantr as apd', 'apd.registrasipasienfk', '=', 'pd.norec')
            ->Join('transaksipasientr as pp', 'pp.daftarpasienruanganfk', '=', 'apd.norec')
            ->leftJoin('batalregistrasitr as br', 'br.registrasipasienfk', '=', 'pd.norec')
            ->select('pd.norec', 'pp.norec as norec_pp');
        if (isset($filter['noregistrasi']) && $filter['noregistrasi'] != "" && $filter['noregistrasi'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $filter['noregistrasi']);
        }
        $data = $data->where('pd.koders', (int)$kdProfile);
        $data = $data->where('apd.aktif', true);
        $data = $data->orderBy('pd.noregistrasi');
        $data = $data->get();

        return $this->respond($data);
    }

    public function SimpanBatalPeriksa(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $dataLogin = $request->all();
        DB::beginTransaction();
        $TglPulang = "";

        $transStatus = 'true';
        try {
            if ($request['norec_br'] == '') {
                $newBR = new BatalRegistrasi();
                $norec = $newBR->generateNewId();
                $newBR->norec = $norec;
                $newBR->koders = $idProfile;
                $newBR->aktif = true;
            } else {
                $newBR = BatalRegistrasi::where('registrasipasienfk', $request['norec'])->first();
            }
            $newBR->alasanpembatalan = $request['alasanpembatalan'];
            $newBR->registrasipasienfk = $request['norec'];
            $newBR->pegawaiidfk = $this->getCurrentUserID();
            $newBR->pembatalanidfk = $request['pembatalanfk'];
            $newBR->tanggalpembatalan = $request['tanggalpembatalan'];
            $newBR->save();
            $dataaa = RegistrasiPasien::where('norec', $request['norec'])->get();
            foreach ($dataaa as $item) {
                $TglPulang = $item->tglpulang;
            }
            if ($TglPulang != null) {
                $data2 = RegistrasiPasien::where('norec', $request['norec'])
                    ->where('koders', $idProfile)
                    ->update([
                        'aktif' => false,
                    ]);
            } else {
                $data2 = RegistrasiPasien::where('norec', $request['norec'])
                    ->update([
                        'aktif' => false,
                        'tglpulang' => $request['tanggalpembatalan']
                    ]);
            }
            $data2 = RegistrasiPasien::where('norec', $request['norec'])
                ->where('koders', $idProfile)
                ->update([
                    'aktif' => false
                ]);
            DaftarPasienRuangan::where('registrasipasienfk', $request['norec'])
                ->whereNull('tglkeluar')
                ->where('koders', $idProfile)
                ->update([
                    'aktif' => false,
                    'tglkeluar' => $request['tanggalpembatalan'],
                ]);


            $transMessage = "Batal Periksa ";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Gagal Batal Periksa";
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

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDaftarPasienBatal(Request $request)
    {
        $filter = $request->all();
        $data = \DB::table('batalregistrasitr as br')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'br.registrasipasienfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'br.pegawaiidfk')
            ->leftjoin('pembatalmt as pmb', 'pmb.id', '=', 'br.pembatalanidfk')
            ->join('ruanganmt as r', 'r.id', '=', 'pd.ruanganlastidfk')
            ->select(
                'pd.norec as norec_pd',
                'br.tanggalpembatalan',
                'br.alasanpembatalan',
                'ps.norm',
                'ps.namapasien',
                'pd.noregistrasi',
                'pg.namalengkap',
                'pmb.name',
                'r.namaruangan'
            );

        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
            $data = $data->where('br.tanggalpembatalan', '>=', $filter['tglAwal']);
        }
        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
            $data = $data->where('br.tanggalpembatalan', '<=', $filter['tglAkhir']);
        }
        if (isset($filter['noReg']) && $filter['noReg'] != "" && $filter['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', 'ilike', '%' . $filter['noReg'] . '%');
        }
        if (isset($filter['noRm']) && $filter['noRm'] != "" && $filter['noRm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $filter['noRm'] . '%');
        }
        if (isset($filter['namaPasien']) && $filter['namaPasien'] != "" && $filter['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['namaPasien'] . '%');
        }
        $data = $data->get();
        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($data);
    }

    public function getDaftarRiwayatRegistrasi(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data = \DB::table('pasienmt as ps')
            ->join('registrasipasientr as pd', 'pd.normidfk', '=', 'ps.id')
            ->join('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->join('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'pd.pegawaiidfk')
            ->select(DB::raw("pd.norec,pd.tglregistrasi,ps.norm as nocm,pd.noregistrasi,ps.namapasien,pd.ruanganlastidfk as objectruanganlastfk,kp.kelompokpasien,ru.namaruangan,
                              pd.pegawaiidfk as objectpegawaifk,pg.namalengkap as namadokter,pd.tglpulang,ru.instalasiidfk as objectdepartemenfk,
			                  CASE when pd.tglregistrasi <> pd.tglpulang then 1 else 0 end as statusinap"))
            ->where('pd.aktif', true)
            ->where('ps.koders', (int)$kdProfile);


        if (isset($request['norm']) && $request['norm'] != "" && $request['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['norm'] . '%');
        };
        if (isset($request['namaPasien']) && $request['namaPasien'] != "" && $request['namaPasien'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $request['namaPasien'] . '%');
        };
        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $request['noReg']);
        };
        if (isset($request['idRuangan']) && $request['idRuangan'] != "" && $request['idRuangan'] != "undefined") {
            $data = $data->where('pd.ruanganlastidfk', '=', $request['idRuangan']);
        };

        $data = $data->where('ps.aktif', true);
        $data = $data->orderBy('pd.tglregistrasi');
        $data = $data->get();
        $data2 = \DB::table('registrasipasientr as pd')
            ->join('daftarpasienruangantr as apd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'apd.pegawaiidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->join('instalasimt as dept', 'dept.id', '=', 'ru.instalasiidfk')
            ->leftJoin('kamarmt as km', 'km.id', '=', 'apd.kamaridfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->leftJoin('tempattidurmt as tt', 'tt.id', '=', 'apd.nobedidfk')
            ->leftJoin('statuspulangmt as sp', 'sp.id', '=', 'pd.statuspulangidfk')
            ->select(
                'apd.norec',
                'pd.norec as norec_pd',
                'apd.tglregistrasi',
                'ru.id as ruid_asal',
                'ru.namaruangan',
                'kls.id as kelasid',
                'kls.namakelas',
                'km.namakamar',
                'tt.reportdisplay as nobed',
                'apd.statusantrian',
                'apd.statuskunjungan',
                'apd.tglregistrasi',
                'apd.tgldipanggildokter',
                'apd.tgldipanggilsuster',
                'pg.id as pgid',
                'pg.namalengkap as namadokter',
                'pd.asalrujukanidfk as objectasalrujukanfk',
                'pd.nostruklastidfk as nostruklastfk',
                'pd.nosbmlastidfk as nosbmlastfk',
                'apd.tglmasuk',
                'apd.tglkeluar',
                'ru.instalasiidfk as objectdepartemenfk',
                'dept.namadepartemen',
                'ps.tglmeninggal',
                'sp.statuspulang'
            )
            ->where('pd.aktif', true)
            ->where('pd.koders', (int)$kdProfile);

        if (isset($request['norm']) && $request['norm'] != "" && $request['norm'] != "undefined") {
            $data2 = $data2->where('ps.norm', '=', $request['norm']);
        };
        if (isset($request['noReg']) && $request['noReg'] != "" && $request['noReg'] != "undefined") {
            $data2 = $data2->where('pd.noregistrasi', '=', $request['noReg']);
        };
        if (isset($request['idRuangan']) && $request['idRuangan'] != "" && $request['idRuangan'] != "undefined") {
            $data2 = $data2->where('pd.ruanganlastidfk', '=', $request['idRuangan']);
        };

        $data2 = $data2->get();


        $norecaPd = '';
        foreach ($data2 as $ob) {
            $norecaPd = $norecaPd . ",'" . $ob->norec . "'";
            $ob->kddiagnosa = [];
        }
        $norecaPd = substr($norecaPd, 1, strlen($norecaPd) - 1);
        $diagnosa = [];
        if ($norecaPd != '') {
            $diagnosa = DB::select(DB::raw("
                          select dg.kddiagnosa,ddp.daftarpasienruanganfk  as norec_apd
                           from diagnosapasienicdxtr as ddp
                           left join icdxmt as dg on ddp.icdxidfk=dg.id
                           where ddp.daftarpasienruanganfk in ($norecaPd) "));
            $i = 0;
            foreach ($data2 as $h) {
                $data2[$i]->kddiagnosa = [];
                foreach ($diagnosa as $d) {
                    if ($data2[$i]->norec == $d->norec_apd) {
                        $data2[$i]->kddiagnosa[] = $d->kddiagnosa;
                    }
                }
                $i++;
            }
        }

        foreach ($data as $d) {
            $d->details = [];
            foreach ($data2 as $d2) {
                if ($d->norec == $d2->norec_pd) {
                    $d->details[] = $d2;
                }
            }
        }

        $result = array(
            'daftar' => $data,
            'message' => 'godU',
        );
        return $this->respond($result);
    }

    public function SimpanPasienTriase(Request $request)
    {
        $kdProfile = (int) $this->getDataKdProfile($request);
        $dataLogin = $request->all();
        $tglAyeuna = date('Y-m-d H:i:s');
        DB::beginTransaction();

        try {
            $idPasien = PasienTriase::max('id');
            $idPasien = $idPasien + 1;

            if ($request['idpasien'] == '') {
                $newBR = new PasienTriase();
                $norec = $newBR->generateNewId();
                $newBR->id = $idPasien;
                $newBR->norec = $norec;
                $newBR->koders = $kdProfile;
                $newBR->aktif = true;
            } else {
                $newBR = PasienTriase::where('id', $request['idpasien'])->first();
            }
            $newBR->jeniskelaminidfk = $request['jeniskelamin'];
            $newBR->namapasien = $request['namapasien'];
            $newBR->tglpendaftaran = $tglAyeuna;
            $newBR->tempatlahir = $request['tempatlahir'];
            $newBR->tgllahir = $request['tgllahir'];
            $newBR->nohp = $request['nohp'];
            $newBR->alamat = $request['alamat'];
            $newBR->save();

            $transStatus = 'true';
            $transMessage = "Selesai";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "Gagal coba lagi";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $newBR,
                "message" => $transMessage,
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function updateStatusEnabledPasien(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        $tglAyeuna = date('Y-m-d H:i:s');
        $dataLogin = $request->all();

        $pasien = \DB::table('pasien_m')
            ->where('id', $request['idpasien'])
            ->where('aktif', true)
            ->where('koders', $kdProfile)
            ->first();

        try {
            if ($request['idpasien'] != '') {
                $dataPS = Pasien::where('id', $request['idpasien'])
                    ->where('koders', (int)$kdProfile)
                    ->update(
                        [
                            'aktif' => false,
                        ]
                    );

                $newId = LoggingUser::max('id');
                $newId = $newId + 1;
                $logUser = new LoggingUser();
                $logUser->id = $newId;
                $logUser->norec = $logUser->generateNewId();
                $logUser->koders = $kdProfile;
                $logUser->aktif = true;
                $logUser->jenislog = 'Hapus Pasien';
                $logUser->noreff = $request['idpasien'];
                $logUser->referensi = 'id pasien';
                $logUser->loginuseridfk = $dataLogin['userData']['id'];
                $logUser->tanggal = $tglAyeuna;
                $logUser->keterangan = 'Hapus Pasien No Rekam Medis : ' . $pasien->nocm . ', Nama Pasien : ' . $pasien->namapasien;
                $logUser->save();
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "update status enabled ";
        }

        if ($transStatus == 'true') {
            $transMessage = "Selesai";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $transMessage,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $transStatus,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getComboEKlaim(Request $request)
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

        $dataKelompok = \DB::table('kelompokpasienmt as kp')
            ->select('kp.id', 'kp.kelompokpasien')
            ->where('kp.aktif', true)
            ->orderBy('kp.kelompokpasien')
            ->get();


        $result = array(
            'departemen' => $dataDepartemen,
            'kelompokpasien' => $dataKelompok,

            'message' => 'godU',
        );

        return $this->respond($result);
    }
    public function getDataRL12(Request $request)
    {
        $idProfile = $this->getDataKdProfile($request);

        $tahun =  '';
        $tahun2 = '';
        $tahun3 = '';
        if (isset($request['tahun']) && $request['tahun'] != '') {
            $tahun = " and to_char(pd.tglregistrasi,'yyyy')='$request[tahun]'";
            $tahun2 = " and to_char(pd.tglpulang,'yyyy')='$request[tahun]'";
            $tahun3 = " and to_char(registrasipasientr.tglregistrasi,'yyyy')='$request[tahun]'";
        }
        $idDepRanap = $this->settingDataFixed('kdDepartemenRanapFix', $idProfile);
        $idStatKelMeninggal = $this->settingDataFixed('KdStatKeluarMeninggal', $idProfile);
        $idKondisiPasienMeninggal = $this->settingDataFixed('KdKondisiPasienMeninggal', $idProfile);

        $sehari = 360;
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
                'matilebih48' =>  0,
                'tahun' => 0,
                'bulan' => date('d-M-Y'),
                'bor' => 0,
                'alos' => 0,
                'bto' => 0,
                'toi' =>  0,
                'gdr' => 0,
                'ndr' =>  0,
            );
            $result = array(
                'data' => $data10,
                'message' => 'Xoxo',
            );
            return $this->respond($result);
        }

        $hariPerawatan = DB::select(DB::raw(
            "
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
              $tahun
              $tahun2
              and pd.aktif = true 
            ) AS x"
        ));

        $lamaRawat = DB::select(DB::raw("
                        select sum(x.hari) as lamarawat, count(x.noregistrasi)as jumlahpasienpulang from (
                        SELECT 
                            date_part('DAY', pd.tglpulang- pd.tglregistrasi) as hari ,pd.noregistrasi
                            FROM registrasipasientr AS pd
                            INNER JOIN ruanganmt AS ru ON ru. ID = pd.ruanganlastidfk
                            WHERE pd.koders = $idProfile 
                            $tahun2
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
                $tahun3
                and registrasipasientr.aktif=true
                ) as x
                GROUP BY x.bulanregis;"));

        $yXoxor = Carbon::now()->yXoxor;
        $num_of_days = [];
        if ($yXoxor == date('Y'))
            $total_month = date('m');
        else
            $total_month = 12;

        for ($m = 1; $m <= $total_month; $m++) {
            $num_of_days[] = array(
                'bulan' =>  $m,
                'jumlahhari' =>  cal_days_in_month(CAL_GREGORIAN, $m, $yXoxor),
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
                foreach ($dataMeninggal as $itemDXoxod) {
                    /** @var  $gdr = (Jumlah Mati dibagi Jumlah pasien Keluar (Hidup dan Mati) */
                    $gdr = (float) $itemDXoxod->jumlahmeninggal * 1000 / (float)$itemLamaRawat->jumlahpasienpulang;
                    /** @var  $NDR = (Jumlah Mati > 48 Jam dibagi Jumlah pasien Keluar (Hidup dan Mati) */
                    $ndr = (float) $itemDXoxod->jumlahlebih48 * 1000 / (float)$itemLamaRawat->jumlahpasienpulang;

                    $jmlMeninggal = (float) $itemDXoxod->jumlahmeninggal;
                    $jmlMatilebih48 = (float) $itemDXoxod->jumlahlebih48;
                }
                /** @var  $alos = (Jumlah Lama Dirawat dibagi Jumlah pasien Keluar (Hidup dan Mati) */
                if ((float)$itemLamaRawat->jumlahpasienpulang > 0) {
                    $alos = (float)$itemLamaRawat->lamarawat / (float)$itemLamaRawat->jumlahpasienpulang;
                }

                /** @var  $bto = Jumlah pasien Keluar (Hidup dan Mati) DIBAGI Jumlah tempat tidur */
                $bto = (float)$itemLamaRawat->jumlahpasienpulang / $jumlahTT;

                /** @var  $bor = (Jumlah hari perawatn RS dibagi ( jumlah TT x Jumlah hari dalam satu periode ) ) x 100 % */
                $bor = ((float)$item->jumlahhariperawatan * 100 / ($jumlahTT *  (float)$sehari)); //$numday['jumlahhari']));
                /** @var  $toi = (Jumlah TT X Periode) - Hari Perawatn DIBAGI Jumlah pasien Keluar (Hidup dan Mati)*/
                if ((float)$itemLamaRawat->jumlahpasienpulang > 0) {
                    $toi = (($jumlahTT * (float)$sehari) - (float)$item->jumlahhariperawatan) / (float)$itemLamaRawat->jumlahpasienpulang;
                }
                $hariPerawatanJml = (float)$item->jumlahhariperawatan;
                $jmlPasienPlg = (float)$itemLamaRawat->jumlahpasienpulang;
            }

            $data10[] = array(
                'lamarawat' => (float)$itemLamaRawat->lamarawat,
                'hariperawatan' => $hariPerawatanJml,
                'pasienpulang' => $jmlPasienPlg,
                'meninggal' => $jmlMeninggal,
                'matilebih48' =>  $jmlMatilebih48,
                'tahun' => $tahun,
                'bulan' => date('d-M-Y'),
                'bor' => (float) number_format($bor, 2),
                'alos' =>  (float)number_format($alos, 2),
                'bto' => (float)number_format($bto, 2),
                'toi' => (float)number_format($toi, 2),
                'gdr' => (float)number_format($gdr, 2),
                'ndr' => (float) number_format($ndr, 2),
            );
        }

        $result = array(
            'data' => $data10,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function getDataRL13(Request $request)
    {
        $idProfile = $this->getDataKdProfile($request);
        $jumlahTT = collect(DB::select("
        select x.jenis,count(x.id) as jumlah , sum(x.jumlahvvip) as jumlahvvip,
        
        sum(x.jumlahvip) as jumlahvip,sum(x.jumlah1) as jumlah1,sum(x.jumlah2) as jumlah2,
        sum(x.jumlah3) as jumlah3,sum(x.jumlahkhusus) as jumlahkhusus
        from(
        select  ru.namaruangan as jenis,tt.id,case when kls.id = 5 then 1 else 0 end as jumlahvvip
        ,case when kls.id = 4 then 1 else 0 end as jumlahvip,case when kls.id = 1 then 1 else 0 end as jumlah1
        ,case when kls.id in( 2,6) then 1 else 0 end as jumlah2,case when kls.id = 3 then 1 else 0 end as jumlah3,
        case when kls.id not in(5,4,3,2,1,6) then 1 else 0 end as jumlahkhusus
                FROM
                        tempattidurmt AS tt
                INNER JOIN kamarmt AS kmr ON kmr.id = tt.kamaridfk
                INNER JOIN ruanganmt AS ru ON ru.id = kmr.ruanganidfk
                INNER JOIN kelasmt AS kls ON kls.id =  kmr.kelasidfk
                WHERE
                        tt.koders = $idProfile
                AND tt.aktif = true
                AND kmr.aktif = true
                ) as x GROUP BY x.jenis
        "));
        $result = array(
            'data' => $jumlahTT,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }
    public function savePasienJKN(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $detLogin = $request->all();
        \DB::beginTransaction();
        try {
            if ($request['idpasien'] == '') {
                $newId = Pasien::max('id') + 1;
                if ( $request['isPenunjang'] == true) {
                    $noCm = $this->generateCodeBySeqTable(new Pasien, 'nocmpenunjang', 9, 'P', $idProfile);
                } //endregion
                else {
                    $idRunningNumber = $this->settingDataFixed('idRunningNumberPasien', $idProfile);
                    $runningNumber = RunningNumber::where('id', $idRunningNumber)->where('koders', $idProfile)->first();
                    $noCm = $this->generateCodeBySeqTable(new Pasien, 'nocm', (int)$runningNumber->length, $runningNumber->prefix, $idProfile);

                    RunningNumber::where('id', $idRunningNumber)
                        ->where('koders', $idProfile)
                        ->update(
                            [
                                'nomer_terbaru' => $noCm
                            ]
                        );
                }

           
                $dataPS = new Pasien();
                $dataPS->id = $newId;
                $dataPS->koders = (int)$kdProfile;
                $dataPS->aktif = true;
                $dataPS->kodeexternal = 'myjkn';
                $dataPS->norec = $dataPS->generateNewId();
            } else {
                $dataPS = Pasien::where('id', $request['idpasien'])->first();
                $noCm = $dataPS->norm;
                
            }
            $dataPS->aktif = true;
            $dataPS->namaexternal = $request['pasien']['namaPasien'];
            $dataPS->agamaidfk = $request['agama']['id'];
            $dataPS->jeniskelaminidfk = $request['jenisKelamin']['id'];
            $dataPS->namapasien = $request['pasien']['namaPasien'];
            $dataPS->pekerjaanidfk = $request['pekerjaan']['id'];
            $dataPS->pendidikanidfk = $request['pendidikan']['id'];
            $dataPS->idpasien2 = 1;
            $dataPS->statusperkawinanidfk = $request['statusPerkawinan']['id'];
            $dataPS->tglpendaftaran = date('Y-m-d H:i:s');
            $dataPS->tgllahir = $request['pasien']['tglLahir'];
            $dataPS->namaibu = $request['namaIbu'];
            $dataPS->notelepon = $request['noTelepon'];
            $dataPS->noidentitas = $request['pasien']['noIdentitas'];
            $dataPS->noaditional = $request['noAditional'];
            $dataPS->kebangsaanidfk = $request['kebangsaan']['id'];
            $dataPS->negaraidfk = $request['negara']['id'];
            $dataPS->namaayah = $request['namaAyah'];
            $dataPS->namasuamiistri = $request['pasien']['namaSuamiIstri'];
            $dataPS->noasuransilain = $request['pasien']['noAsuransiLain'];
            $dataPS->nobpjs = $request['pasien']['noBpjs'];
            $dataPS->nohp = $request['pasien']['noHp'];
            $dataPS->tempatlahir = $request['pasien']['tempatLahir'];
            $dataPS->namakeluarga = $request['pasien']['namaKeluarga'];
            $dataPS->jamlahir = $request['pasien']['tglLahir'];
           
            $dataPS->norm = $noCm;
         
            $dataPS->save();
            $dataNoCMFk = $newId;
          
           
            if ($request['idpasien'] == '') {

                $idAlamat = Alamat::max('id') + 1;
                $dataAL = new Alamat();
                $dataAL->id = $idAlamat;
                $dataAL->koders = (int)$kdProfile;;
                $dataAL->aktif = true;
                $dataAL->kodeexternal = $idAlamat;
                $dataAL->norec = $dataAL->generateNewId();
            } else {
                $dataAL = Alamat::where('normidfk', $dataNoCMFk)->first();
                if (empty($dataAL)) {
                    $idAlamat = Alamat::max('id') + 1;
                    $dataAL = new Alamat();
                    $dataAL->id = $idAlamat;
                    $dataAL->koders = (int)$kdProfile;
                    $dataAL->aktif = true;
                    $dataAL->kodeexternal = $idAlamat;
                    $dataAL->norec = $dataAL->generateNewId();
                    $idAlamat = $dataAL->id;
                } else {
                    $idAlamat = $dataAL->id;
                }
            }
            $dataAL->aktif = true;
            $dataAL->alamatlengkap = $request['alamatLengkap'];
            $dataAL->hubungankeluargaidfk = 1;
            $dataAL->desakelurahanidfk = $request['desaKelurahan']['id'];
            $dataAL->jenisalamatidfk = 1;
            $dataAL->kdalamat = $idAlamat;
            $dataAL->kecamatanidfk = $request['kecamatan']['id'];
            $dataAL->kodepos = $request['kodePos'];
            $dataAL->kotakabupatenidfk = $request['kotaKabupaten']['id'];
            $dataAL->namadesakelurahan = $request['desaKelurahan']['namaDesaKelurahan'];
            $dataAL->namakecamatan = $request['kecamatan']['namaKecamatan'];
            $dataAL->namakotakabupaten = $request['kotaKabupaten']['namaKotaKabupaten'];
            $dataAL->negaraidfk = $request['negara']['id'];
            $dataAL->normidfk = $dataNoCMFk;
            $dataAL->pegawaiidfk = $detLogin['userData']['id'];
            $dataAL->provinsiidfk = $request['propinsi']['id'];
            $dataAL->kecamatan = $request['kecamatan']['namaKecamatan'];
            $dataAL->kotakabupaten = $request['kotaKabupaten']['namaKotaKabupaten'];

            $dataAL->save();
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus != 'false') {
            \DB::commit();
            $result = array(
                "response" => array(
                    "norm" =>  $dataPS->norm
                ),
                "metadata" => array(
                    "message" => 'Harap datang ke adminsi untuk melengkapi data rekam medis',
                    "code" => 200
                ),
                
            );
        }else{
            DB::rollBack();
            $result = array(
                "metadata" => array(
                    "message" => $e->getMessage(),
                    "code" => 201)
            );

        }
        return $this->setStatusCode($result['metadata']['code'])->respond($result);
    }
    public function saveAntrolV2($noreservasi){
        try{
            $json = array(
                "kodebooking" => $noreservasi,
                "taskid" => 3, //pasien lama langsung task 3 //(akhir waktu layan admisi/mulai waktu tunggu poli)
                "waktu" => strtotime(date('Y-m-d H:i:s')) * 1000,
            );
            $objetoRequest = new \Illuminate\Http\Request();
            $objetoRequest ['url']= "antrean/updatewaktu";
            $objetoRequest ['jenis']= "antrean";
            $objetoRequest ['method']= "POST";
            $objetoRequest ['data']= $json;
         
            $post = app('App\Http\Controllers\Bridging\BridgingBPJSV2Controller')->bpjsTools($objetoRequest);
       
            if(is_array($post)){
                $code = $post['metaData']->code;
                $message = $post['metaData']->message;
            }else{
                $cek = json_decode($post->content(), true);
                $code = $cek['metaData']['code'];
                $message = isset($cek['metaData']['message']) ? $cek['metaData']['message']: "";
            }
            if($code != '200'){
                $json2 = array(
                    "kodebooking" => $noreservasi,
                    "taskid" => 1, 
                    "waktu" => strtotime(date('Y-m-d H:i:s')) * 1000,
                );
                $objetoRequest2 = new \Illuminate\Http\Request();
                $objetoRequest2 ['url']= "antrean/updatewaktu";
                $objetoRequest2 ['jenis']= "antrean";
                $objetoRequest2 ['method']= "POST";
                $objetoRequest2 ['data']= $json2;
             
                $post2 = app('App\Http\Controllers\Bridging\BridgingBPJSV2Controller')->bpjsTools($objetoRequest2);

                $json3 = array(
                    "kodebooking" => $noreservasi,
                    "taskid" => 2, 
                    "waktu" => (strtotime(date('Y-m-d H:i:s')) * 1000 )-240000 ,
                );
                $objetoRequest3 = new \Illuminate\Http\Request();
                $objetoRequest3 ['url']= "antrean/updatewaktu";
                $objetoRequest3 ['jenis']= "antrean";
                $objetoRequest3 ['method']= "POST";
                $objetoRequest3 ['data']= $json3;
             
                $post3 = app('App\Http\Controllers\Bridging\BridgingBPJSV2Controller')->bpjsTools($objetoRequest3);

                $json4 = array(
                    "kodebooking" => $noreservasi,
                    "taskid" => 3, 
                    "waktu" =>  (strtotime(date('Y-m-d H:i:s')) * 1000 )-120000 ,
                );
                $objetoRequest4 = new \Illuminate\Http\Request();
                $objetoRequest4 ['url']= "antrean/updatewaktu";
                $objetoRequest4 ['jenis']= "antrean";
                $objetoRequest4 ['method']= "POST";
                $objetoRequest4 ['data']= $json4;
             
                $post5= app('App\Http\Controllers\Bridging\BridgingBPJSV2Controller')->bpjsTools($objetoRequest4);
                if(is_array($post5)){
                    $code = $post5['metaData']->code;
                    $message = $post5['metaData']->message;
                }else{
                    $cek = json_decode($post5->content(), true);
                    $code = $cek['metaData']['code'];
                    $message = isset($cek['metaData']['message']) ? $cek['metaData']['message']: "";
                }
                if($code != '200'){
                    $result = array("metadata"=>array("message" => "Add antrol gagal : ".$message, "code" => 201));
                    return $result;
                }else{
                    $result = array("metadata"=>array("message" =>$message, "code" => 200));
                    return $result;
                }
                $result = array("metadata"=>array("message" => "Add antrol gagal : ".$message, "code" => 201));
                return $result;
            }else{
                $result = array("metadata"=>array("message" =>$message, "code" => 200));
                return $result;
            }
        } catch (\Exception $e) {
            $result = array("metadata"=>array("message" =>$e->getMessage().' ' .$e->getLine(), "code" => 201));
            return $result;
        }

    }
}
