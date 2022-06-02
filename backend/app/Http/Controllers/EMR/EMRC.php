<?php

namespace App\Http\Controllers\EMR;

use App\Http\Controllers\ApiController;
use App\Master\KelompokTransaksi;
use App\Master\Pasien;
use App\Master\Pegawai;
use App\Master\Ruangan;
use App\Transaksi\EMRPasienD_Temp;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\PerjanjianPasien;
use App\Transaksi\TransaksiOrderDetail;
use App\Transaksi\RegistrasiPasien;
use App\Transaksi\PasienPerjanjian;
use App\Transaksi\TransaksiPasien;
use App\Transaksi\RisOrder;
use App\Transaksi\SOAP;
use App\Transaksi\TransaksiOrder;
use App\Transaksi\EMRPasien;
use App\Transaksi\EMRPasienD;
use App\Transaksi\EmrFoto;
use Illuminate\Http\Request;
use App\Transaksi\SeqNumberEMR;

use DB;
use App\Traits\Valet;
use App\Transaksi\PetugasPelaksana;
use MongoDB\Driver\ReadConcern;
use phpDocumentor\Reflection\Types\Null_;
use Webpatser\Uuid\Uuid;

use App\Transaksi\SeqNumber;
use App\Transaksi\TransaksiAlkes;
use App\Transaksi\TransaksiKartuStok;
use App\Transaksi\TransaksiPasienDetail;
use App\Transaksi\TransaksiResep;
use App\Transaksi\TransaksiStok;

class EMRC extends ApiController
{
    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getAntrianPasienDiperiksa($norecAPD, Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $data = \DB::table('daftarpasienruangantr as apd')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->Join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->Join('kelompokpasienmt as kps', 'kps.id', '=', 'pd.kelompokpasienlastidfk')
            ->Join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'apd.pegawaiidfk')
            ->leftJoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->leftJoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
            ->leftJoin('pendidikanmt as pdd', 'pdd.id', '=', 'ps.pendidikanidfk')
            ->leftjoin('pekerjaanmt as pk', 'pk.id', '=', 'ps.pekerjaanidfk')
            ->leftjoin('golongandarahmt as gd', 'gd.id', '=', 'ps.golongandarahidfk')
            ->leftjoin('rekananmt as rk', 'rk.id', '=', 'pd.rekananidfk')
            ->leftjoin('kelasmt as kls', 'kls.id', '=', 'apd.kelasidfk')
            ->leftJoin('pegawaimt as pg1', 'pg1.id', '=', 'pd.pegawaiidfk')
            ->select('apd.norec', 'pd.noregistrasi', 'pd.tglregistrasi', 'ps.norm as nocm', 'ps.namapasien', 'ps.tgllahir', 'ru.instalasiidfk as objectdepartemenfk',
                'alm.alamatlengkap', 'kps.kelompokpasien', 'ru.namaruangan', 'pg.namalengkap', 'jk.jeniskelamin', 'pd.norec as norec_pd',
                'pdd.pendidikan', 'pk.pekerjaan', 'pd.jenispelayananidfk as jenispelayanan', 'pd.jenispelayananidfk as objectjenispelayananfk',
                'rk.namarekanan', 'kls.namakelas', 'pd.normidfk as nocmfk', 'pd.kelompokpasienlastidfk as objectkelompokpasienlastfk',
                'apd.ruanganidfk as objectruanganfk', 'apd.pegawaiidfk as objectpegawaifk',
                'ps.jeniskelaminidfk as objectjeniskelaminfk', 'apd.kelasidfk as objectkelasfk',
                'ps.golongandarahidfk as objectgolongandarahfk', 'gd.golongandarah', 'ps.tgllahir',
                DB::raw('null AS foto'), 'pg1.namalengkap as dokterdpjp', 'pg1.id as iddpjp', 'pd.isclosing', 'pd.tglclosing', 'pd.statusschedule as noreservasi')
            ->where('apd.koders', $idProfile)
            ->where('apd.aktif', true)
            ->where('pd.aktif', true)
            ->where('apd.norec', $norecAPD);

        $data = $data->first();
        // if ($data->foto != null) {
        //     $data->foto = "data:image/jpeg;base64," . $data->foto;
        // }
        $result = array(
            'result' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function getMenuRekamMedisAtuh(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $dataRaw = \DB::table('emrmt as emr')
            ->where('emr.koders', $idProfile)
            ->where('emr.aktif', true)
            ->where('emr.namaemr', $request['namaemr'])
            ->select('emr.*')
            ->orderBy('emr.nourut');
        $dataRaw = $dataRaw->get();
        $dataraw3 = [];
        foreach ($dataRaw as $dataRaw2) {
            $dataraw3[] = array(
                'id' => $dataRaw2->id,
                'kdprofile' => $dataRaw2->koders,
                'statusenabled' => $dataRaw2->aktif,
                'kodeexternal' => $dataRaw2->kodeexternal,
                'namaexternal' => $dataRaw2->namaexternal,
                'reportdisplay' => $dataRaw2->reportdisplay,
                'namaemr' => $dataRaw2->namaemr,
                'caption' => $dataRaw2->caption,
                'headfk' => $dataRaw2->headfk,
                'nourut' => $dataRaw2->nourut,
                'key' => $dataRaw2->id,
                'label' => $dataRaw2->caption,
                'data' => $dataRaw2->caption,
                'expandedIcon' => "pi pi-folder-open",
                'collapsedIcon' => "pi pi-folder",
            );
        }
        $data = $dataraw3;

        function recursiveElements($data)
        {
            $elements = [];
            $tree = [];
            foreach ($data as &$element) {
                $id = $element['id'];
                $parent_id = $element['headfk'];

                $elements[$id] = &$element;
                if (isset($elements[$parent_id])) {
                    $elements[$parent_id]['children'][] = &$element;
                    $elements[$parent_id]['items'][] = &$element;
                } else {
                    if ($parent_id <= 10) {
                        $tree[] = &$element;
                    }
                }
            }
            return $tree;
        }

        $data = recursiveElements($data);

        $result = array(
            'data' => $data,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function getComboPenunjangOrder(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        $set = $this->settingDataFixed($request['departemenfk'], $kdProfile);

        $dataRuanganTujuan = \DB::table('ruanganmt as ru')
            ->select('ru.id', 'ru.namaruangan', 'ru.instalasiidfk')
            ->where('ru.koders', $kdProfile)
            ->where('ru.instalasiidfk', $set)
            ->where('ru.aktif', true)
            ->orderBy('ru.id')
            ->get();


        $dataTea = array(
            'ruangantujuan' => $dataRuanganTujuan,
            'message' => 'slvR'
        );
        return $this->respond($dataTea);
    }

    public function saveOrderPelayananLabRad(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        if ($request['pegawaiorderfk'] == "") {
            $dokter2 = null;
        } else {
            $dokter2 = $request['pegawaiorderfk'];
        }
        DB::beginTransaction();
        try {
            $ketOrder = '';
            $kelTran = null;
            if ($request['departemenfk'] == $this->settingDataFixed('kdDepartemenLab', $kdProfile)) {
                $noOrder = $this->generateCodeBySeqTable(new TransaksiOrder, 'noorderlab', 11, 'OL' . date('ym'), $idProfile);
                $ketOrder = 'Order Laboratorium';
                $kelTran = $this->settingDataFixed('kdKelompokTransaksiOrderLab', $kdProfile);
            }
            if ($request['departemenfk'] == $this->settingDataFixed('kdDepartemenRad', $kdProfile)) {
                $noOrder = $this->generateCodeBySeqTable(new TransaksiOrder, 'noorderrad', 11, 'OR' . date('ym'), $idProfile);
                $ketOrder = 'Order Radiologi';
                $kelTran = $this->settingDataFixed('kdKelompokTransaksiOrderRad', $kdProfile);
            }
            if ($request['departemenfk'] == $this->settingDataFixed('kdDepartemenOK', $kdProfile)) {
                $noOrder = $this->generateCodeBySeqTable(new TransaksiOrder, 'noorderok', 11, 'OK' . date('ym'), $idProfile);
                $ketOrder = 'Order Bedah';
                $kelTran = $this->settingDataFixed('kdKelompokTransaksiOrderBedah', $kdProfile);
            }
            if ($request['departemenfk'] == $this->settingDataFixed('kdDepartemenPJ', $kdProfile)) {
                $noOrder = $this->generateCodeBySeqTable(new TransaksiOrder, 'noorderpj', 11, 'PJ' . date('ym'), $idProfile);
                $ketOrder = 'Order Pelayanan Jenazah';
                $kelTran = $this->settingDataFixed('kdKelompokTransaksiOrderJenazah', $kdProfile);
            }
            if ($request['departemenfk'] == $this->settingDataFixed('kdDepartemenBM', $kdProfile)) {
                $noOrder = $this->generateCodeBySeqTable(new TransaksiOrder, 'noorderabm', 11, 'ABM' . date('ym'), $idProfile);
                $ketOrder = 'Order Ambulance';
                $kelTran = $this->settingDataFixed('kdKelompokTransaksiOrderAmbulance', $kdProfile);
            }
            if ($noOrder == '') {
                $transMessage = "Gagal mengumpukan data, Coba lagi.!";
                DB::rollBack();
                $result = array(
                    "status" => 400,
                    "message" => $transMessage,
                    "as" => 'slvR',
                );
                return $this->setStatusCode($result['status'])->respond($result, $transMessage);
            }

            $dataPD = RegistrasiPasien::where('norec', $request['norec_pd'])->first();
            $pasien = DB::table('pasienmt')->where('id', $dataPD->normidfk)->first();
            if ($request['norec_so'] == "") {
                $dataSO = new TransaksiOrder;
                $dataSO->norec = $dataSO->generateNewId();
                $dataSO->koders = $idProfile;
                $dataSO->aktif = true;
                $dataSO->normidfk = $dataPD->normidfk;
                $dataSO->isdelivered = 1;
                $dataSO->noorder = $noOrder;
                $dataSO->noorderintern = $noOrder;
                $dataSO->registrasipasienfk = $dataPD->norec;
                $dataSO->pegawaiorderidfk = $dokter2;
                $dataSO->qtyjenisproduk = 1;
                $dataSO->qtyproduk = $request['qtyproduk'];
                $dataSO->ruanganidfk = $request['objectruanganfk'];
                $dataSO->ruangantujuanidfk = $request['objectruangantujuanfk'];
                $dataSO->keteranganorder = $ketOrder;
                $dataSO->kelompoktransaksiidfk = $kelTran;
                if (isset($request['tgloperasi'])) {
                    $dataSO->tglpelayananakhir = $request['tgloperasi'];
                }
                if ($request['departemenfk'] == $this->settingDataFixed('kdDepartemenOK', $kdProfile)) {
                    $dataSO->tglpelayananakhir = $request['tglrencana'];
                    $dataSO->tglpelayananawal = $request['tglrencana'];
                }
                
                if (isset($request['tgloperasi'])) {
                    $dataSO->tglpelayananawal = $request['tgloperasi'];
                }
                if (isset($request['tglrencana'])) {
                    $dataSO->tglrencana = $request['tglrencana'];
                }
                if (isset($request['keterangan'])) {
                    $dataSO->keteranganlainnya = $request['keterangan'];
                }
                if (isset($request['tanggal'])) {
                    $dataSO->tglorder = $request['tanggal'];
                } else {
                    $dataSO->tglorder = date('Y-m-d H:i:s');
                }

                $dataSO->totalbeamaterai = 0;
                $dataSO->totalbiayakirim = 0;
                $dataSO->totalbiayatambahan = 0;
                $dataSO->totaldiscount = 0;
                $dataSO->totalhargasatuan = 0;
                $dataSO->totalharusdibayar = 0;
                $dataSO->totalpph = 0;
                $dataSO->totalppn = 0;
                if (isset($request['iscito'])) {
                    $dataSO->cito = $request['iscito'];
                }
                $dataSO->save();

                $dataSOnorec = $dataSO->norec;

                $listProduk = '';
                $prod = [];

                foreach ($request['details'] as $item) {
                    if ($request['status'] == 'bridinglangsung') {
                        $updatePP = TransaksiPasien::where('norec', $item['norec_pp'])
                            ->where('koders', $idProfile)
                            ->update([
                                    'transaksiorderfk' => $dataSOnorec
                                ]
                            );
                    }
                    $pro = DB::table('pelayananmt')->where('id', $item['produkfk'])->first();
                    $prod[] = $pro->namaproduk;

                    $listProduk = $listProduk . ',' . $pro->namaproduk;
                    $listProduk = substr($listProduk, 1, strlen($listProduk) - 1);
                    $dataOP = new TransaksiOrderDetail;
                    $dataOP->norec = $dataOP->generateNewId();
                    $dataOP->koders = $idProfile;
                    $dataOP->aktif = true;
                    if (isset($item['iscito'])) {
                        $dataOP->iscito = (float)$item['iscito'];
                    } else {
                        $dataOP->iscito = 0;
                    }

                    $dataOP->transaksiorderfk = $dataSOnorec;
                    $dataOP->produkidfk = $item['produkfk'];
                    $dataOP->qtyproduk = $item['qtyproduk'];
                    $dataOP->kelasidfk = $item['objectkelasfk'];
                    $dataOP->qtyprodukretur = 0;
                    $dataOP->ruanganidfk = $request['objectruanganfk'];
                    $dataOP->ruangantujuanidfk = $request['objectruangantujuanfk'];
                    $dataOP->transaksiorderfk = $dataSOnorec;
                    if (isset($item['pemeriksaanluar'])) {
                        if ($item['pemeriksaanluar'] == 1) {
                            $dataOP->keteranganlainnya = 'isPemeriksaanKeluar';
                        }
                    }
                    if (isset($item['tglrencana'])) {
                        $dataOP->tglpelayanan = $item['tglrencana'];
                    } else {
                        $dataOP->tglpelayanan = date('Y-m-d H:i:s');
                    }
                    if (isset($item['dokterid']) && $item['dokterid'] != "") {
                        $dataOP->namapenyerahbarangidfk = $item['dokterid'];
                    }
                    $dataOP->nourut = $item['nourut'];
                    $dataOP->save();
                }

            } else {
                $prod = [];
                $listProduk = '';
                foreach ($request['details'] as $item) {
                    $pro = DB::table('pelayananmt')->where('id', $item['produkfk'])->first();
                    $listProduk = $listProduk . ',' . $pro->namaproduk;
                    $prod[] = $pro->namaproduk;
                    $listProduk = substr($listProduk, 1, strlen($listProduk) - 1);
                    $dataOP = new TransaksiOrderDetail;
                    $dataOP->norec = $dataOP->generateNewId();
                    $dataOP->koders = $idProfile;
                    $dataOP->aktif = true;
                    if (isset($item['iscito'])) {
                        $dataOP->iscito = (float)$item['iscito'];
                    } else {
                        $dataOP->iscito = 0;
                    }

                    $dataOP->transaksiorderfk = $request['norec_so'];
                    $dataOP->produkidfk = $item['produkfk'];
                    $dataOP->qtyproduk = $item['qtyproduk'];
                    $dataOP->kelasidfk = $item['objectkelasfk'];
                    $dataOP->qtyprodukretur = 0;
                    $dataOP->ruanganidfk = $request['objectruanganfk'];
                    $dataOP->ruangantujuanidfk = $request['objectruangantujuanfk'];
                    $dataOP->transaksiorderfk = $request['norec_so'];

                    if (isset($item['tglrencana'])) {
                        $dataOP->tglpelayanan = $item['tglrencana'];
                    } else {
                        $dataOP->tglpelayanan = date('Y-m-d H:i:s');
                    }

                    if (isset($item['dokterid']) && $item['dokterid'] != "") {
                        $dataOP->namapenyerahbarangidfk = $item['dokterid'];
                    }
                    $dataOP->nourut = $item['nourut'];
                    $dataOP->save();
                }
            }

            $statusBot = false;
            $telegram_id = '';
            if ($request['departemenfk'] == 3) {
                $telegram_id = '-1001350253936';
                $statusBot = true;
            }
            if ($request['departemenfk'] == 27) {
                $telegram_id = '-506890893';
                $statusBot = true;
            }
            if ($statusBot) {
                $setting = $this->settingDataFixed('settingOrderTelegramLab', $idProfile);
                if (!empty($setting) && $setting == 'true') {
                    $from = DB::table('ruangan_m')->where('id', $request['objectruanganfk'])->first();
                    $to = DB::table('ruangan_m')->where('id', $request['objectruangantujuanfk'])->first();
                    $peg = DB::table('pegawai_m')->where('id', $dokter2)->first();
                    $cito = '';
                    if (isset($request['iscito']) && $request['iscito'] == 'true') {
                        $cito = ' Cito ';
                    }
                    $secret_token = "1545548931:AAHwGMJrXxGMc609WwO9e2UQTcRquu5ri-M";
                    $produks = '';
                    foreach ($prod as $key => $value) {
                        $produks = $produks . " \n " . $value;
                    }

                    $url = "https://api.telegram.org/bot" . $secret_token . "/sendMessage?parse_mode=html&chat_id=" . $telegram_id;
                    $url = $url . "&text=" . urlencode("✅ Order Baru : <b> " . $to->namaruangan . " </b> \n\n Dari Ruangan : <b>" . $from->namaruangan .
                            "</b>  \n Pengorder : <b>" . $peg->namalengkap . "</b>  \n Pasien : <b>" . $pasien->namapasien . " (" . $pasien->norm . ") " .
                            "</b> \n Status : <b>" . $cito . "</b>  \n Nama Pelayanan : <b>" . $produks . "</b> \n\n");
                    $ch = curl_init();
                    $optArray = array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true
                    );
                    curl_setopt_array($ch, $optArray);
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
            }


            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            if ($request['norec_so'] == "") {
                $transMessage = "Simpan Order Pelayanan";
                DB::commit();
                $result = array(
                    "status" => 201,
                    "message" => $transMessage,
                    "strukorder" => $dataSO,
                    "as" => 'Xoxo',
                );
            } else {
                $transMessage = "Simpan Order Pelayanan";
                DB::commit();
                $result = array(
                    "status" => 201,
                    "message" => $transMessage,
                    "as" => 'Xoxo',
                );
            }
        } else {
            $transMessage = "Simpan Order Pelayanan gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getRiwayatOrderPenunjang(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $results = [];
        $ruanganLab = explode(',', $this->settingDataFixed($request['setting'], $idProfile));
        $kdRuangLab = [];
        foreach ($ruanganLab as $item) {
            $kdRuangLab [] = (int)$item;
        }

        $data = \DB::table('transaksiordertr as so')
            ->LEFTJOIN('registrasipasientr as pd', 'pd.norec', '=', 'so.registrasipasienfk')
            ->JOIN('pasienmt as pas', 'pas.id', '=', 'pd.normidfk')
            ->LEFTJOIN('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
            ->LEFTJOIN('ruanganmt as ru2', 'ru2.id', '=', 'so.ruangantujuanidfk')
            ->LEFTJOIN('pegawaimt as p', 'p.id', '=', 'so.pegawaiorderidfk')
            ->select('so.norec', 'pd.norec as norecpd', 'pd.noregistrasi', 'so.tglorder', 'so.noorder',
                'ru.namaruangan as ruanganasal', 'ru2.namaruangan as ruangantujuan', 'p.namalengkap',
                'so.noorder', 'pd.noregistrasi', 'so.keteranganlainnya', 'so.cito', 'so.ruangantujuanidfk', 'so.tglrencana',
                'so.statusorder'
            )
            ->where('pd.koders', $idProfile);
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != "" && $request['noregistrasi'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $request['noregistrasi']);
        }
        if (isset($request['NoCM']) && $request['NoCM'] != "" && $request['NoCM'] != "undefined") {
            $data = $data->where('pas.norm', 'ilike', '%' . $request['NoCM'] . '%');
        }
        $data = $data->whereIn('ru2.instalasiidfk', $kdRuangLab);
        $data = $data->where('so.aktif', true);
        $data = $data->orderBy('so.tglorder');
        $data = $data->get();

        foreach ($data as $item) {
            if ($request['setting'] == 'kdDepartemenLab') {
                $noorder = $item->noorder;
                $hasil = collect(DB::select("select * from order_lab where no_lab='$noorder'"))->first();
                if (!empty($hasil)) {
                    $item->statusorder = 'SELESAI';
                } else {
                    $item->statusorder = 'PENDING';
                }
                $details = DB::select(DB::raw("
                            select
                               pr.id,pr.namaproduk,op.qtyproduk
                             from transaksiorderdetailtr as op
                             join pelayananmt as pr on pr.id=op.produkidfk
                            where op.koders = $idProfile and op.transaksiorderfk=:noorder"),
                    array(
                        'noorder' => $item->norec,
                    )
                );
                $results[] = array(
                    'noregistrasi' => $item->noregistrasi,
                    'tglorder' => $item->tglorder,
                    'noorder' => $item->noorder,
                    'norec' => $item->norec,
                    'norecpd' => $item->norecpd,
                    'namaruanganasal' => $item->ruanganasal,
                    'namaruangantujuan' => $item->ruangantujuan,
                    'dokter' => $item->namalengkap,
                    'statusorder' => $item->statusorder,
                    'keteranganlainnya' => $item->keteranganlainnya,
                    'cito' => $item->cito,
                    'details' => $details,
                );
            } else if ($request['setting'] == 'kdDepartemenRad') {


                $risorder = array();
                if ($item->ruangantujuanidfk != $this->settingDataFixed('kdRuanganElektromedik', $idProfile)) {

                    $risorder = RisOrder::where('order_no', $item->noorder)->where('study_remark', '!=', '-')->get(); // syamsu
                    if (count($risorder) > 0) {
                        $status = 'Sudah diproses';
                    } else {
                        $status = 'Belum diproses';
                    }
                } else {
                    if ($item->statusorder != null) {
                        $status = 'Verifikasi';
                    } else {
                        $status = 'Belum Verifikasi';
                    }
                }

                $details = DB::select(DB::raw("
                            select op.norec as norecopfk,
                            pr.id, pr.namaproduk, op.qtyproduk,
                             pp.norec as norec_pp, hr.norec as norec_hr
                            from transaksiorderdetailtr as op
                            left join transaksipasientr as pp on pp.transaksiorderfk = op.transaksiorderfk
                            left join hasilradiologitr as hr on hr.transaksipasienfk = pp.norec
                            join pelayananmt as pr on pr.id=op.produkidfk
                             where op.koders = $idProfile and op.transaksiorderfk=:noorder"),
                    array(
                        'noorder' => $item->norec,
                    )
                );


                $results[] = array(
                    'noregistrasi' => $item->noregistrasi,
                    'tglorder' => $item->tglorder,
                    'noorder' => $item->noorder,
                    'norec' => $item->norec,
                    'namaruanganasal' => $item->ruanganasal,
                    'namaruangantujuan' => $item->ruangantujuan,
                    'dokter' => $item->namalengkap,
                    'statusorder' => $status,
                    'keteranganlainnya' => $item->keteranganlainnya,
                    'cito' => $item->cito,
                    'details' => $details,
                    'risorder' => $risorder
                );
            } else {
                if ($item->statusorder != null) {
                    $status = 'Verifikasi';
                } else {
                    $status = 'Belum Verifikasi';
                }
                $details = DB::select(DB::raw("
                            select
                               pr.id,pr.namaproduk,op.qtyproduk
                             from transaksiorderdetailtr as op
                             join pelayananmt as pr on pr.id=op.produkidfk
                            where op.koders = $idProfile and op.transaksiorderfk=:noorder"),
                    array(
                        'noorder' => $item->norec,
                    )
                );
                $results[] = array(
                    'noregistrasi' => $item->noregistrasi,
                    'tglorder' => $item->tglorder,
                    'noorder' => $item->noorder,
                    'norec' => $item->norec,
                    'norecpd' => $item->norecpd,
                    'namaruanganasal' => $item->ruanganasal,
                    'namaruangantujuan' => $item->ruangantujuan,
                    'dokter' => $item->namalengkap,
                    'statusorder' => $status,
                    'keteranganlainnya' => $item->keteranganlainnya,
                    'cito' => $item->cito,
                    'tglrencana' => $item->tglrencana,
                    'details' => $details,
                );
            }
        }

        $result = array(
            'daftar' => $results,
            'message' => 'Xoxo',
        );

        return $this->respond($result);
    }

    public function hapusOrderPelayananLabRad(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        DB::beginTransaction();
        try {
            TransaksiOrder::where('norec', $request['norec_order'])->where('koders', $idProfile)->update
            (['aktif' => false]);

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Data Terhapus";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'Xoxo',
            );

        } else {
            $transMessage = "Hapus Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getExpertise(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $data2 = DB::table('hasilradiologitr as hr')
                ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'hr.pegawaiidfk')
                ->select('hr.*', 'pg.namalengkap')
                ->where('hr.aktif', true)
            > where('hr.koders', $kdProfile)
                ->where('hr.transaksipasienfk', '=', $request['norec'])
                ->first();


        $res = array(
            "data" => $data2,
            "as" => 'Xoxo'
        );
        return $this->respond($res);
    }

    public function getRuangKonsul(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenRawatJalanFix', $kdProfile));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item) {
            $kdDepartemenRawatJalan [] = (int)$item;
        }
        $ruangan = DB::table('ruanganmt')->where('koders', $kdProfile)
            ->wherein('instalasiidfk', $kdDepartemenRawatJalan)
            ->get();
        return $this->respond($ruangan);
    }

    public function saveOrderKonsul(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        DB::beginTransaction();
        try {
            if ($request['method'] == 'delete') {
                $dataSO = TransaksiOrder::where('norec', $request['norec_so'])->where('koders', $kdProfile)->first();
                $dataSO->aktif = false;
                $dataSO->save();
                $msg = 'Hapus ';
            } else {
                $msg = 'Simpan ';
                $dataPD = RegistrasiPasien::where('norec', $request['norec_pd'])->where('koders', $kdProfile)->first();
                if ($request['norec_so'] == "") {
                    $dataSO = new TransaksiOrder();
                    $dataSO->norec = $dataSO->generateNewId();
                    $dataSO->koders = $kdProfile;
                    $dataSO->aktif = true;
                    $noOrder = $this->generateCodeBySeqTable(new TransaksiOrder, 'noorder', 11, 'KS' . $this->getDateTime()->format('ym'), $kdProfile);
                } else {
                    $dataSO = TransaksiOrder::where('norec', $request['norec_so'])->where('koders', $kdProfile)->first();
                    $noOrder = $dataSO->noorder;
                }
                $dataSO->normidfk = $dataPD->normidfk;
                $dataSO->isdelivered = 1;
                $dataSO->noorder = $noOrder;
                $dataSO->noorderintern = $noOrder;
                $dataSO->registrasipasienfk = $dataPD->norec;
                $dataSO->pegawaiorderidfk = $request['pegawaifk'];
                $dataSO->petugasidfk = $this->getCurrentUserID();
                $dataSO->qtyjenisproduk = 0;
                $dataSO->qtyproduk = 0;
                $dataSO->ruanganidfk = $request['objectruanganasalfk'];
                $dataSO->ruangantujuanidfk = $request['objectruangantujuanfk'];
                $dataSO->keteranganorder = $request['keterangan'];
                $kelompokTransaksi = KelompokTransaksi::where('kelompoktransaksi', 'KONSULTASI DOKTER')->where('koders', $kdProfile)->first();
                $dataSO->kelompoktransaksiidfk = $kelompokTransaksi->id;
                $dataSO->tglorder = date('Y-m-d H:i:s');
                $dataSO->totalbeamaterai = 0;
                $dataSO->totalbiayakirim = 0;
                $dataSO->totalbiayatambahan = 0;
                $dataSO->totaldiscount = 0;
                $dataSO->totalhargasatuan = 0;
                $dataSO->totalharusdibayar = 0;
                $dataSO->totalpph = 0;
                $dataSO->totalppn = 0;
                $dataSO->save();

            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = $msg . ' Konsul';
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "strukorder" => $dataSO,
                "as" => 'Xoxo',
            );
        } else {
            $transMessage = $msg . " Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "e" => $e->getMessage() . ' ' . $e->getLine(),
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getOrderKonsul(Request $request)
    {
        $idProfile = $this->getDataKdProfile($request);
        $kelTrans = KelompokTransaksi::where('kelompoktransaksi', 'KONSULTASI DOKTER')->first();
        $data = \DB::table('transaksiordertr as so')
            ->Join('registrasipasientr as pd', 'pd.norec', '=', 'so.registrasipasienfk')
            ->Join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
            ->leftJoin('ruanganmt as rutuju', 'rutuju.id', '=', 'so.ruangantujuanidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
            ->leftJoin('pegawaimt as pet', 'pet.id', '=', 'so.petugasidfk')
            ->leftJoin('daftarpasienruangantr as apd', 'apd.strukorderidfk', '=', 'so.norec')
            ->select('so.norec', 'so.noorder', 'so.tglorder', 'ru.namaruangan as ruanganasal', 'pg.namalengkap',
                'rutuju.namaruangan as ruangantujuan', 'pet.namalengkap as pengonsul',
                'pd.noregistrasi', 'pd.tglregistrasi', 'ps.norm as nocm', 'so.keteranganorder', 'pd.norec as norec_pd',
                'ps.namapasien', 'pg.id as pegawaifk', 'so.ruangantujuanidfk as objectruangantujuanfk', 'so.ruanganidfk as objectruanganfk', 'apd.norec as norec_apd',
                'so.keteranganlainnya', 'so.statusorder')
            ->where('so.koders', $idProfile)
            ->where('so.aktif', true)
            ->where('so.kelompoktransaksiidfk', $kelTrans->id)
            ->orderBy('so.tglorder', 'desc');
        if (isset($request['norecpd']) && $request['norecpd'] != '') {
            $data = $data->where('pd.norec', $request['norecpd']);
        }
        if (isset($request['dokterid']) && $request['dokterid'] != '') {
            $data = $data->where('pg.id', $request['dokterid']);
        }
        if (isset($request['nocm']) && $request['nocm'] != '') {
            $data = $data->where('ps.norm', $request['nocm']);
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != '') {
            $data = $data->where('pd.noregistrasi', $request['noregistrasi']);
        }
        $data = $data->get();
        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function saveKonsulFromOrder(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        DB::beginTransaction();
        try {
            $pd = RegistrasiPasien::where('norec', $request['norec_pd'])->first();
            $apd = DaftarPasienRuangan::where('registrasipasienfk', $request['norec_pd'])->first();
            $dataAPD = new DaftarPasienRuangan;
            $dataAPD->norec = $dataAPD->generateNewId();
            $dataAPD->koders = $idProfile;
            $dataAPD->aktif = true;
            $dataAPD->asalrujukanidfk = $pd->asalrujukanidfk;
            $dataAPD->kelasidfk = $request['kelasfk'];
            $dataAPD->noantrian = $request['noantrian'];
            $dataAPD->registrasipasienfk = $request['norec_pd'];
            $dataAPD->pegawaiidfk = $request['dokterfk'];
            $dataAPD->ruanganidfk = $request['objectruangantujuanfk'];
            $dataAPD->statusantrian = 0;
            $dataAPD->statuspasienidfk = 1;
            $dataAPD->statuskunjungan = 'LAMA';
            $dataAPD->statuspenyakit = 'BARU';
            $dataAPD->ruanganasalidfk = $request['objectruanganasalfk'];;
            $dataAPD->tglregistrasi = $pd->tglregistrasi;
            $dataAPD->tglkeluar = date('Y-m-d H:i:s');
            $dataAPD->tglmasuk = date('Y-m-d H:i:s');
            $dataAPD->strukorderidfk = $request['norec_so'];
            $dataAPD->save();
            TransaksiOrder::where('norec', $request['norec_so'])->where('koders', $kdProfile)->update([
                'statusorder' => 1
            ]);
            $dataAPDnorec = $dataAPD->norec;
            $transStatus = 'true';
            $transMessage = "Simpan ";
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = "simpan Gagal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $dataAPD,
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDaftarKonsulFromOrder(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $filter = $request->all();
        $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenRawatJalanFix', $kdProfile));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item) {
            $kdDepartemenRawatJalan [] = (int)$item;
        }
        $data = \DB::table('daftarpasienruangantr as apd')
            ->Join('transaksiordertr as so', 'so.norec', '=', 'apd.strukorderidfk')
            ->join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->join('pasienmt as ps', 'ps.id', '=', 'pd.normidfk')
            ->leftjoin('alamatmt as alm', 'ps.id', '=', 'alm.normidfk')
            ->leftjoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
            ->join('kelasmt as kls', 'kls.id', '=', 'pd.kelasidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'apd.pegawaiidfk')
            ->Join('kelompokpasienmt as kp', 'kp.id', '=', 'pd.kelompokpasienlastidfk')
            ->leftjoin('perjanjianpasientr as apr', function ($join) {
                $join->on('apr.noreservasi', '=', 'pd.statusschedule');
                $join->on('apr.normidfk', '=', 'pd.normidfk');
            })
            ->distinct()
            ->select('apd.tglmasuk as tglregistrasi', 'ps.norm as nocm', 'pd.noregistrasi', 'ps.namapasien',
                'ps.tgllahir', 'jk.jeniskelamin', 'apd.ruanganidfk as objectruanganfk', 'ru.namaruangan', 'kls.id as idkelas', 'kls.namakelas',
                'kp.kelompokpasien', 'apd.pegawaiidfk as objectpegawaifk', 'pg.namalengkap as namadokter', 'pd.norec as norec_pd',
                'apd.norec as norec_apd', 'apd.asalrujukanidfk as objectasalrujukanfk',
                'apd.tgldipanggildokter', 'apd.statuspasien as statuspanggil', 'pd.statuspasien', 'apd.tgldipanggildokter', 'apd.tgldipanggilsuster', 'apr.noreservasi', 'apd.noantrian',
                'apr.tanggalreservasi', 'alm.alamatlengkap', DB::raw('case when apd.ispelayananpasien is null then \'false\' else \'true\' end as statuslayanan'))
            ->where('apd.koders', $idProfile)
            ->where('pd.aktif', true)
            ->where('ps.aktif', true)
            ->where('apd.aktif', true);

        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
            $data = $data->where('apd.tglmasuk', '>=', $filter['tglAwal']);
        }
        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
            $data = $data->where('apd.tglmasuk', '<=', $filter['tglAkhir']);
        }
        if (isset($filter['ruangId']) && $filter['ruangId'] != "" && $filter['ruangId'] != "undefined") {
            $data = $data->where('ru.id', '=', $filter['ruangId']);
        }
        if (isset($filter['dokId']) && $filter['dokId'] != "" && $filter['dokId'] != "undefined") {
            $data = $data->where('pg.id', '=', $filter['dokId']);
        }
        if (isset($filter['noreg']) && $filter['noreg'] != "" && $filter['noreg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', '=', $filter['noreg']);
        }
        if (isset($filter['norm']) && $filter['norm'] != "" && $filter['norm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $filter['norm'] . '%');
        }
        if (isset($filter['nama']) && $filter['nama'] != "" && $filter['nama'] != "undefined") {
            $data = $data->where('ps.namapasien', 'ilike', '%' . $filter['nama'] . '%');
        }
        if (isset($filter['norecApd']) && $filter['norecApd'] != "" && $filter['norecApd'] != "undefined") {
            $data = $data->where('apd.norec', $filter['norecApd']);
        }
        if (isset($request['ruanganArr']) && $request['ruanganArr'] != "" && $request['ruanganArr'] != "undefined") {
            $arrRuang = explode(',', $request['ruanganArr']);
            $kodeRuang = [];
            foreach ($arrRuang as $item) {
                $kodeRuang[] = (int)$item;
            }
            $data = $data->whereIn('ru.id', $kodeRuang);
        }
        if (isset($filter['jmlRow']) && $filter['jmlRow'] != "" && $filter['jmlRow'] != "undefined") {
            $data = $data->take($filter['jmlRow']);
        }
        $data = $data->whereIn('ru.instalasiidfk', $kdDepartemenRawatJalan);
        $data = $data->orderBy('apd.noantrian');
        $data = $data->get();

        return $this->respond($data);
    }

    public function countOrderKonsul(Request $request)
    {
        $idProfile = $this->getDataKdProfile($request);
        $kelTrans = KelompokTransaksi::where('kelompoktransaksi', 'KONSULTASI DOKTER')->first();
        $data = \DB::table('transaksiordertr as so')
            ->where('so.koders', $idProfile)
            ->where('so.aktif', true)
            ->where('so.kelompoktransaksiidfk', $kelTrans->id)
            ->whereNull('so.statusorder');
        if (isset($request['norecpd']) && $request['norecpd'] != '') {
            $data = $data->where('pd.norec', $request['norecpd']);
        }
        if (isset($request['dokterid']) && $request['dokterid'] != '') {
            $data = $data->where('so.pegawaiorderidfk', $request['dokterid']);
        }
        if (isset($request['nocm']) && $request['nocm'] != '') {
            $data = $data->where('ps.norm', $request['nocm']);
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != '') {
            $data = $data->where('pd.noregistrasi', $request['noregistrasi']);
        }
        $data = $data->count();
        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function ubahDokter(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        \Illuminate\Support\Facades\DB::beginTransaction();
        $transStatus = 'true';
        try {
            $data = DaftarPasienRuangan::where('norec', $request['norec_apd'])
                ->where('koders', $kdProfile)
                ->update([
                    'pegawaiidfk' => $request['pegawaiidfk'],
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

    public function getEMRTransaksiDetailForm(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        //todo : detail
        $paramNocm = '';
        $paramNoreg = '';
        $data = \DB::table('emrpasientr as emrp')
            ->leftJoin('pegawaimt as pg', 'pg.id', '=', 'emrp.pegawaifk')
            ->select('emrp.*', 'emrp.noregistrasifk as noregistrasi', 'pg.namalengkap')
            ->where('emrp.aktif', true)
            ->where('emrp.koders', $idProfile)
            ->orderBy('emrp.tglemr', 'desc');

        if (isset($request['noemr']) && $request['noemr'] != '') {
            $data = $data->where('emrp.noemr', $request['noemr']);
        }
        if (isset($request['emrfk']) && $request['emrfk'] != '') {
            $data = $data->where('emrdp.emrfk', $request['emrfk']);
        }
        if (isset($request['norec']) && $request['norec'] != '') {
            $data = $data->where('emrp.norec', $request['norec']);
        }
        if (isset($request['nocm']) && $request['nocm'] != '') {
            $data = $data->where('emrp.nocm', $request['nocm']);
            $paramNocm = "AND emrp.nocm='" . $request['nocm'] . "'";
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != '') {
            $data = $data->where('emrp.noregistrasifk', $request['noregistrasi']);
            $paramNoreg = "AND emrp.noregistrasifk='" . $request['noregistrasi'] . "'";
        }
        if (isset($request['tgllahir']) && $request['tgllahir'] != "" && $request['tgllahir'] != "undefined" && $request['tgllahir'] != "null") {
            $tgllahir = $request['tgllahir'];
            $data = $data->whereRaw("format( emrp.tgllahir, 'yyyy-MM-dd')  ='$tgllahir' ");
        }
        if (isset($request['namapasien']) && $request['namapasien'] != '') {
            $data = $data->where('emrp.namapasien', $request['namapasien']);
        }
        if (isset($request['jenisEmr']) && $request['jenisEmr'] != '') {
            $data = $data->where('emrp.jenisemr', 'ilike', '%' . $request['jenisEmr'] . '%');
        } else {
            $data = $data->whereNull('emrp.jenisemr');
        }
        $data = $data->get();
        $jenisEMr = $request['jenisEmr'];
        $result = [];
        foreach ($data as $item) {
            $noemr = $item->noemr;

            $details = DB::select(DB::raw("
             SELECT
                    emrdp.emrpasienfk,
                    emrdp.emrfk,
                    emr.reportdisplay,
                    emrp.norec,emr.caption as namaform
                FROM
                    emrpasiendtr AS emrdp
                INNER JOIN emrpasientr AS emrp ON emrp.noemr = emrdp.emrpasienfk
                --INNER JOIN emrd_t AS emrd ON emrd.id = emrdp.emrdfk
               INNER JOIN emrmt AS emr ON emr.id = emrdp.emrfk
                WHERE emrdp.koders = $idProfile and
                    emrdp.aktif = true
                   $paramNocm
                   $paramNoreg
                    AND emrp.jenisemr ILIKE '%$jenisEMr%'
                    and emrdp.emrpasienfk='$noemr'
                    GROUP BY emrdp.emrpasienfk,
                    emrdp.emrfk,emrp.norec,emr.caption,
                    emr.reportdisplay

            "));

            $result [] = array(
                'norec' => $item->norec,
                'kdprofile' => $item->koders,
                'statusenabled' => $item->aktif,
                'kodeexternal' => $item->kodeexternal,
                'emrfk' => $item->emrfk,
                'noregistrasifk' => $item->noregistrasifk,
                'noemr' => $item->noemr,
                'nocm' => $item->nocm,
                'namapasien' => $item->namapasien,
                'jeniskelamin' => $item->jeniskelamin,
                'noregistrasi' => $item->noregistrasi,
                'umur' => $item->umur,
                'kelompokpasien' => $item->kelompokpasien,
                'tglregistrasi' => $item->tglregistrasi,
                'norec_apd' => $item->norec_apd,
                'namakelas' => $item->namakelas,
                'namaruangan' => $item->namaruangan,
                'tglemr' => $item->tglemr,
                'tgllahir' => $item->tgllahir,
                'notelepon' => $item->notelepon,
                'alamat' => $item->alamat,
                'jenisemr' => $item->jenisemr,
                'pegawaifk' => $item->pegawaifk,
                'namalengkap' => $item->namalengkap,
                'details' => array([
                    'details' => $details
                ])
            );
        }
        $result = array(
            'data' => $result,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function getRekamMedisAtuh(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $dataRaw = \DB::table('emrdmt as emrd')
            ->where('emrd.emrfk', $request['emrid'])
            ->where('emrd.aktif', '=', true)
            ->where('emrd.koders', $idProfile)
            ->select('emrd.*')
            ->orderBy('emrd.nourut');
        $dataRaw = $dataRaw->get();
        $dataTitle = \DB::table('emrmt as emr')
            ->where('emr.id', $request['emrid'])
            ->where('emr.koders', $idProfile)
            ->select('emr.caption as captionemr', 'emr.classgrid', 'emr.namaemr')
            ->get();
        $title = $dataTitle[0]->captionemr;
        $classgrid = $dataTitle[0]->classgrid;
        $namaemr = $dataTitle[0]->namaemr;

        $dataraw3A = [];
        $dataraw3B = [];
        $dataraw3C = [];
        $dataraw3D = [];
        foreach ($dataRaw as $dataRaw2) {
            $head = '';
            $captioRadio = [];
            if ($dataRaw2->type == 'radio') {
                $cap = explode(',', $dataRaw2->caption);
                foreach ($cap as $key => $values) {
                    $captioRadio[] = $values;
                }
            }

            if ($dataRaw2->kolom == 1) {
                $dataraw3A[] = array(
                    'kdprofile' => $dataRaw2->koders,
                    'statusenabled' => $dataRaw2->aktif,
                    'kodeexternal' => $dataRaw2->kodeexternal,
                    'namaexternal' => $dataRaw2->namaexternal,
                    'reportdisplay' => $dataRaw2->reportdisplay,
                    'emrfk' => $dataRaw2->emrfk,
                    'caption' => $head . $dataRaw2->caption,
                    'type' => $dataRaw2->type,
                    'nourut' => $dataRaw2->nourut,
                    'satuan' => $dataRaw2->satuan,
                    'id' => $dataRaw2->id,
                    'headfk' => $dataRaw2->headfk,
                    'namaemr' => $namaemr,
                    'style' => $dataRaw2->style,
                    'cbotable' => $dataRaw2->cbotable,
                    'child' => [],
                    'captionradio' => $captioRadio,
                );
            } elseif ($dataRaw2->kolom == 2) {
                $dataraw3B[] = array(
                    'kdprofile' => $dataRaw2->koders,
                    'statusenabled' => $dataRaw2->aktif,
                    'kodeexternal' => $dataRaw2->kodeexternal,
                    'namaexternal' => $dataRaw2->namaexternal,
                    'reportdisplay' => $dataRaw2->reportdisplay,
                    'emrfk' => $dataRaw2->emrfk,
                    'caption' => $head . $dataRaw2->caption,
                    'type' => $dataRaw2->type,
                    'nourut' => $dataRaw2->nourut,
                    'satuan' => $dataRaw2->satuan,
                    'id' => $dataRaw2->id,
                    'headfk' => $dataRaw2->headfk,
                    'namaemr' => $namaemr,
                    'style' => $dataRaw2->style,
                    'cbotable' => $dataRaw2->cbotable,
                    'child' => [],
                    'captionradio' => $captioRadio,
                );
            } elseif ($dataRaw2->kolom == 3) {
                $dataraw3C[] = array(
                    'kdprofile' => $dataRaw2->koders,
                    'statusenabled' => $dataRaw2->aktif,
                    'kodeexternal' => $dataRaw2->kodeexternal,
                    'namaexternal' => $dataRaw2->namaexternal,
                    'reportdisplay' => $dataRaw2->reportdisplay,
                    'emrfk' => $dataRaw2->emrfk,
                    'caption' => $head . $dataRaw2->caption,
                    'type' => $dataRaw2->type,
                    'nourut' => $dataRaw2->nourut,
                    'satuan' => $dataRaw2->satuan,
                    'id' => $dataRaw2->id,
                    'headfk' => $dataRaw2->headfk,
                    'namaemr' => $namaemr,
                    'style' => $dataRaw2->style,
                    'cbotable' => $dataRaw2->cbotable,
                    'child' => [],
                    'captionradio' => $captioRadio,
                );
            } else {
                $dataraw3D[] = array(
                    'kdprofile' => $dataRaw2->koders,
                    'statusenabled' => $dataRaw2->aktif,
                    'kodeexternal' => $dataRaw2->kodeexternal,
                    'namaexternal' => $dataRaw2->namaexternal,
                    'reportdisplay' => $dataRaw2->reportdisplay,
                    'emrfk' => $dataRaw2->emrfk,
                    'caption' => $head . $dataRaw2->caption,
                    'type' => $dataRaw2->type,
                    'nourut' => $dataRaw2->nourut,
                    'satuan' => $dataRaw2->satuan,
                    'id' => $dataRaw2->id,
                    'headfk' => $dataRaw2->headfk,
                    'namaemr' => $namaemr,
                    'style' => $dataRaw2->style,
                    'cbotable' => $dataRaw2->cbotable,
                    'child' => [],
                    'captionradio' => $captioRadio,
                );
            }

        }

        function recursiveElements($data)
        {
            $elements = [];
            $tree = [];
            foreach ($data as &$element) {
                $id = $element['id'];
                $parent_id = $element['headfk'];

                $elements[$id] = &$element;
                if (isset($elements[$parent_id])) {
                    $elements[$parent_id]['child'][] = &$element;
                } else {
                    if ($parent_id <= 10) {
                        $tree[] = &$element;
                    }
                }
            }
            return $tree;
        }


        $dataA = [];
        $dataB = [];
        $dataC = [];
        $dataD = [];
        if (count($dataraw3A) > 0) {
            $dataA = recursiveElements($dataraw3A);
        }
        if (count($dataraw3B) > 0) {
            $dataB = recursiveElements($dataraw3B);
        }
        if (count($dataraw3C) > 0) {
            $dataC = recursiveElements($dataraw3C);
        }
        if (count($dataraw3D) > 0) {
            $dataD = recursiveElements($dataraw3D);
        }


        $result = array(
            'kolom1' => $dataA,
            'kolom2' => $dataB,
            'kolom3' => $dataC,
            'kolom4' => $dataD,
            'title' => $title,
            'classgrid' => str_replace('grid_', 'p-md-', $classgrid),
            'namaemr' => $namaemr,
            'message' => 'slvR',
        );

        return $this->respond($result);
    }

    public function getEMRTransaksiDetail(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);

        $data = \DB::table('emrpasiendtr as emrdp')
            ->leftjoin('emrdmt as emrd', 'emrd.id', '=', 'emrdp.emrdfk')
            ->leftjoin('pegawaimt as pg', 'pg.id', '=', 'emrdp.pegawaifk')
            ->select('emrdp.*', 'emrd.caption', 'emrd.type', 'emrd.nourut', 'emrdp.emrfk', 'emrd.reportdisplay', 'emrd.kodeexternal as kodeex', 'emrd.satuan', 'pg.namalengkap')
            ->where('emrdp.aktif', true)
            ->where('emrdp.koders', $kdProfile)
            ->whereNotNull('emrdp.value')
            ->where('emrdp.value', '!=', 'Invalid date')
            ->orderBy('emrd.nourut');
        if (isset($request['noemr']) && $request['noemr'] != '') {
            $data = $data->where('emrdp.emrpasienfk', $request['noemr']);
        }
        if (isset($request['emrfk']) && $request['emrfk'] != '') {
            $data = $data->where('emrdp.emrfk', $request['emrfk']);
        }
        if (isset($request['objectid']) && $request['objectid'] != '') {
            $data = $data->where('emrdp.emrdfk', $request['objectid']);
        }
        $data = $data->get();
        $result = array(
            'data' => $data,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function getDataComboPegawaiPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $req = $request->all();
        $dataProduk = \DB::table('pegawaimt')
            ->select('id as value', 'namalengkap as text')
            ->where('aktif', true)
            ->where('koders', $idProfile)
            ->orderBy('namalengkap');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $dataProduk = $dataProduk->where('namalengkap', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();

        return $this->respond($dataProduk);
    }

    public function getDataComboDokterPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $req = $request->all();
        $dataProduk = \DB::table('pegawaimt')
            ->select('id as value', 'namalengkap as text')
            ->where('koders', $idProfile)
            ->where('aktif', true)
            ->where('objectjenispegawaifk', 1)
            ->orderBy('namalengkap');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $dataProduk = $dataProduk->where('namalengkap', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();

        return $this->respond($dataProduk);
    }

    public function getDataComboRuanganPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $req = $request->all();
        $dataProduk = \DB::table('ruanganmt')
            ->select('id as value', 'namaruangan as text')
            ->where('koders', $idProfile)
            ->where('aktif', true)
            ->orderBy('namaruangan');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $dataProduk = $dataProduk->where('namaruangan', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();

        return $this->respond($dataProduk);
    }

    public function getDataComboRuanganPelayananPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $req = $request->all();
        $dataProduk = \DB::table('ruangan_m')
            ->select('id as value', 'namaruangan as text')
            ->where('kdprofile', $idProfile)
            ->where('statusenabled', true)
            ->wherein('objectdepartemenfk', array(25, 27, 24, 3, 17, 35, 26, 28, 16, 18))
            ->orderBy('objectdepartemenfk');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $dataProduk = $dataProduk->where('namaruangan', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();

        return $this->respond($dataProduk);
    }

    public function getDataComboDiagnosaPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $req = $request->all();
        $data = \DB::table('icdxmt')
            ->select('id as value', 'kddiagnosa', 'namadiagnosa as text')
            ->where('koders', $idProfile)
            ->where('aktif', true)
            ->orderBy('kddiagnosa');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $data = $data->where('kddiagnosa', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
            $data = $data->orwhere('namadiagnosa', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $data = $data->take(10);
        $data = $data->get();

        $dt = [];
        foreach ($data as $item) {
            $dt[] = array(
                'value' => $item->value,
                'text' => $item->kddiagnosa . ' ' . $item->text,
            );
        }

        return $this->respond($dt);
    }

    public function getDataComboTindakanPart(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $req = $request->all();
        $data = \DB::table('pelayananmt as pr')
            ->join('mapruangantopelayananmt as map', 'pr.id', '=', 'map.produkidfk')
            ->join('ruanganmt as ru', 'ru.id', '=', 'map.ruanganidfk')
            ->select('pr.id as value', 'pr.namaproduk as text')
            ->where('pr.koders', $idProfile)
            ->where('pr.aktif', true)
//            ->where('ru.instalasiidfk', 25)
            ->orderBy('pr.namaproduk');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $data = $data->where('pr.namaproduk', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $data = $data->take(10);
        $data = $data->get();

        return $this->respond($data);
    }

    public function getDataComboJKPart(Request $request)
    {

        $data[] = array(
            'values' => 1,
            'text' => 'Laki-laki'
        );
        $data[] = array(
            'values' => 2,
            'text' => 'Perempuan'
        );


        return $this->respond($data);
    }

    public function SaveTransaksiEMRBackup(Request $request)
    {
        $kdProfile = (int)$this->getDataKdProfile($request);
        $dataReq = $request->all();
        $head = $dataReq['head'];
        $data = $dataReq['data'];
        DB::beginTransaction();
        try {
            if ($head['norec_emr'] == '-') {
                $noemr = $this->generateCodeBySeqTable(new EMRPasien, 'noemr', 15, 'MR' . date('ym') . '/', $kdProfile);

                if ($noemr == '') {
                    $transMessage = "Gagal mengumpukan data, Coba lagi.!";
                    DB::rollBack();
                    $result = array(
                        "status" => 400,
                        "message" => $transMessage,
                        "as" => 'slvR',
                    );
                    return $this->setStatusCode($result['status'])->respond($result, $transMessage);
                }

                $EMR = new EMRPasien();
                $norecHead = $EMR->generateNewId();
                $EMR->norec = $norecHead;
                $norecTehMenikitunyaeuy = $norecHead;
                $EMR->norec = $norecTehMenikitunyaeuy;
                $EMR->koders = $kdProfile;
                $EMR->aktif = 1;

                if (isset($head['noregistrasi'])) {
                    $EMR->noregistrasifk = $head['noregistrasi'];
                }
                $EMRPASIENDETAIL = [];
                $EMRPASIENDETAILIMG = [];

            } else {
                $EMR = EMRPasien::where('noemr', $head['norec_emr'])
                    ->where('noregistrasifk', $head['noregistrasi'])
                    ->where('koders', $kdProfile)
                    ->first();
                $noemr = $EMR->noemr;

                $EMRPASIENDETAIL = EMRPasienD::where('emrpasienfk', $noemr)
                    ->select('emrdfk', 'value')
                    ->where('emrfk', $head['emrfk'])
                    ->where('koders', $kdProfile)
                    ->where('aktif', 1)
                    ->orderBy('emrdfk')
                    ->get();
                if (isset($dataReq['dataimg'])) {
                    $EMRPASIENDETAILIMG = EmrFoto::where('noemrpasienfk', $noemr)
                        ->select('emrdfk', 'image')
                        ->where('emrfk', $head['emrfk'])
                        ->where('koders', $kdProfile)
                        ->where('aktif', 1)
                        ->orderBy('emrdfk')
                        ->get();
                }
            }

            if (isset($head['noregistrasi'])) {
                if (trim($EMR->noregistrasifk) != $head['noregistrasi']) {
                    $transMessage = "Kesalahan loading data..!";
                    DB::rollBack();
                    $result = array(
                        "status" => 400,
                        "message" => $transMessage,
                        "as" => 'slvR',
                    );
                    return $this->setStatusCode($result['status'])->respond($result, $transMessage);
                }
            }

            $EMR->noemr = $noemr;
            $EMR->emrfk = $head['emrfk'];
            $EMR->nocm = $head['nocm'];
            $EMR->namapasien = $head['namapasien'];
            $EMR->jeniskelamin = $head['jeniskelamin'];
            $EMR->umur = $head['umur'];
            if (isset($head['kelompokpasien'])) {
                $EMR->kelompokpasien = $head['kelompokpasien'];
            }
            if (isset($head['tglregistrasi'])) {
                $EMR->tglregistrasi = $head['tglregistrasi'];
            }
            if (isset($head['norec'])) {
                $EMR->norec_apd = $head['norec'];
            }
            if (isset($head['namakelas'])) {
                $EMR->namakelas = $head['namakelas'];
            }
            if (isset($head['namaruangan'])) {
                $EMR->namaruangan = $head['namaruangan'];
            } else {
                $EMR->namaruangan = "Triage Gawat Darurat";
            }
            if (isset($head['tgllahir'])) {
                $EMR->tgllahir = $head['tgllahir'];
            }
            if (isset($head['notelepon'])) {
                $EMR->notelepon = $head['notelepon'];
            }
            if (isset($head['alamatlengkap'])) {
                $EMR->alamat = $head['alamatlengkap'];
            }
            if (isset($head['jenisemr'])) {
                $EMR->jenisemr = $head['jenisemr'];
            }
            $EMR->pegawaifk = $this->getCurrentUserID();
            $EMR->tglemr = $this->getDateTime()->format('Y-m-d H:i:s');
            $EMR->save();
            $norec_EMR = $EMR->noemr;

            $i = 0;
            $sama = 0;
            $j = 0;
            $h = 0;
            foreach ($data as $item) {
                $emrdfk = $item['id'];
                if (is_array($item['values'])) {
                    $valueemr = $item['values']['value'] . '~' . $item['values']['text'];
                } else {
                    $valueemr = $item['values'];
                }
                $sama = 0;
                foreach ($EMRPASIENDETAIL as $emrupdate) {
                    $sama = 0;
                    if ($emrupdate->emrdfk == $emrdfk) {
                        $sama = 1;
                        if ($emrupdate->value != $valueemr) {
                            $sama = 2;
                            break;
                        }
                        break;
                    }
                }

                if ($sama == 2) {
                    $EMRPasienDUpdatekeun = EMRPasienD::where('emrpasienfk', $norec_EMR)
                        ->where('emrfk', $head['emrfk'])
                        ->where('emrdfk', $emrdfk)
                        ->where('koders', $kdProfile)
                        ->where('aktif', 1)
                        ->update([
                            'value' => $valueemr
                        ]);
                    $j++;
                }
                $EMRD = [];
                if ($sama == 0) {
                    $EMRD = new EMRPasienD();
                    $norecD = $EMRD->generateNewId();
                    $EMRD->norec = $norecD;
                    $EMRD->koders = $kdProfile;
                    $EMRD->aktif = 1;
                    $EMRD->emrpasienfk = $norec_EMR;
                    $EMRD->value = $valueemr;
                    $EMRD->emrdfk = $emrdfk;
                    $EMRD->emrfk = $head['emrfk'];
                    $EMRD->pegawaifk = $this->getCurrentUserID();
                    $EMRD->tgl = $this->getDateTime()->format('Y-m-d H:i:s');
                    $EMRD->save();
                    $h++;
                }
                $i = $i + 1;
            }

            if (isset($dataReq['dataimg'])) {
                $i2 = 0;
                $sama2 = 0;
                $j2 = 0;
                $h2 = 0;
                $dataImg = $dataReq['dataimg'];
                foreach ($dataImg as $item2) {
                    if ($item2['values'] != '../app/images/svg/no-image.svg') {

                        $emrdfk2 = $item2['id'];
                        $valueemr2 = $item2['values'];

                        $sama2 = 0;
                        foreach ($EMRPASIENDETAILIMG as $emrupdate) {
                            $sama2 = 0;
                            if ($emrupdate->emrdfk == $emrdfk2) {
                                $sama2 = 1;
                                if ($emrupdate->image != $valueemr2) {
                                    $sama2 = 2;
                                    break;
                                }
                                break;
                            }
                        }

                        if ($sama2 == 2) {
                            $EMRPasienDUpdatekeun2 = EmrFoto::where('noemrpasienfk', $norec_EMR)
                                ->where('emrfk', $head['emrfk'])
                                ->where('emrdfk', $emrdfk2)
                                ->where('koders', $kdProfile)
                                ->where('aktif', 1)
                                ->update([
                                    'image' => $valueemr2
                                ]);
                            $j2++;
                        }
                        $EMRD2 = [];
                        if ($sama2 == 0) {
                            $EMRD2 = new EmrFoto();
                            $norecD2 = $EMRD2->generateNewId();
                            $EMRD2->norec = $norecD2;
                            $EMRD2->koders = $kdProfile;
                            $EMRD2->aktif = 1;
                            $EMRD2->noemrpasienfk = $norec_EMR;
                            $EMRD2->image = $valueemr2;
                            $EMRD2->emrdfk = $emrdfk2;
                            $EMRD2->emrfk = $head['emrfk'];
                            $EMRD2->save();
                            $h2++;
                        }

                        $i2 = $i2 + 1;
                    }
                }
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
        $transMessage = 'Saving EMR Pasien ';

        if ($transStatus == 'true') {
            $transMessage = $transMessage . "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $EMR,
                "jumlah" => count($EMRPASIENDETAIL),
                "update" => $j,
                "new" => $h,
                "as" => 'slvR',
            );
            $this->saveEMRBackup($data, $head, $norec_EMR, $kdProfile);

        } else {
            $transMessage = $transMessage . " Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "e" => $e->getLine() . ' ' . $e->getMessage(),
                "as" => 'slvR',
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function saveEMRBackup($data, $head, $norec_EMR, $kdProfile)
    {
        DB::beginTransaction();

        foreach ($data as $item) {
            $emrdfk = $item['id'];
            if (is_array($item['values'])) {
                $valueemr = $item['values']['value'] . '~' . $item['values']['text'];
            } else {
                $valueemr = $item['values'];
            }
            $EMRD_temp = new EMRPasienD_Temp();
            $norecDs = $EMRD_temp->generateNewId();
            $EMRD_temp->norec = $norecDs;
            $EMRD_temp->koders = $kdProfile;
            $EMRD_temp->aktif = 1;
            $EMRD_temp->emrpasienfk = $norec_EMR;
            $EMRD_temp->value = $valueemr;
            $EMRD_temp->emrdfk = $emrdfk;
            $EMRD_temp->emrfk = $head['emrfk'];
            $EMRD_temp->pegawaifk = $this->getCurrentUserID();
            $EMRD_temp->tgl = $this->getDateTime()->format('Y-m-d H:i:s');
            $EMRD_temp->save();
        }
        $transStatus = 'true';

        $transMessage = 'Saving Backup EMR Pasien ';

        if ($transStatus == 'true') {
            $transMessage = $transMessage . "Sukses";
            DB::commit();

        } else {
            $transMessage = $transMessage . " Gagal!!";
            DB::rollBack();

        }

    }

    public function hapusEMRtransaksiNorec(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        DB::beginTransaction();
        try {
            EMRPasien::where('norec', $request['norec'])->where('koders', $idProfile)->update(
                ['aktif' => false]
            );
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus EMR";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $transMessage,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = " Hapus Gagal";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $transMessage,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function disableEMRdetail(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        DB::beginTransaction();
        try {
            $emr = EMRPasienD::where('emrfk', $request['idemr'])
                ->where('koders', $idProfile)
                ->where('emrpasienfk', $request['noemr'])
                ->first();
            $pegawai = Pegawai::where('id', $request['idpegawai'])->first();

            if ($emr->pegawaifk != null && $emr->pegawaifk != $request['idpegawai']) {
                $pegawaiInput = Pegawai::where('id', $emr->pegawaifk)->where('koders', $idProfile)->first();
                $transMessage = "Hanya User yang mengisi yang bisa menghapus. ( " . $pegawaiInput->namalengkap . " )";
                DB::rollBack();
                $result = array(
                    "status" => 400,
                    "message" => $transMessage,
                    "as" => 'Xoxo',
                );
                return $this->setStatusCode($result['status'])->respond($result, $transMessage);
            }

            EMRPasienD::where('emrfk', $request['idemr'])
                ->where('koders', $idProfile)
                ->where('emrpasienfk', $request['noemr'])
                ->update([
                    'aktif' => false,
                ]);
            $emrdetail = EMRPasienD::where('emrpasienfk', $request['noemr'])
                ->where('koders', $idProfile)
                ->where('aktif', true)
                ->get();
            if (count($emrdetail) == 0) {
                EMRPasien::where('norec', $request['norec'])
                    ->update([
                        'aktif' => false,
                    ]);
            }
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus Berhasil";
            DB::commit();
            $result = array(
                'status' => 201,
                'message' => $transMessage,
                'as' => 'Xoxo',
            );
        } else {
            $transMessage = "Hapus Gagal";
            DB::rollBack();
            $result = array(
                'status' => 400,
                'message' => $transStatus,
                'as' => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getPasienTriase(Request $request)
    {
        $idProfile = (int)$this->getDataKdProfile($request);
        $data = [];
        $idPasien = $request['idPasien'];
        if ($request['Keterangan'] == "pasienTerdaftar") {
            $data = \DB::table('pasienmt as ps')
                ->leftJoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
                ->leftJoin('alamatmt as alm', 'alm.normidfk', '=', 'ps.id')
                ->select(DB::raw("
                    ps.norm AS nocm,ps.namapasien,ps.tgllahir,alm.alamatlengkap,jk.jeniskelamin,ps.nohp	as notelepon
                "))
                ->where('ps.koders', $idProfile)
                ->where('ps.aktif', true)
                ->where('ps.norm', $idPasien);
        }
        if ($request['Keterangan'] == "pasienTriase") {
            $data = \DB::table('pasientriasemt as ps')
                ->leftJoin('jeniskelaminmt as jk', 'jk.id', '=', 'ps.jeniskelaminidfk')
                ->select(DB::raw("
                    ps.id AS nocm,ps.namapasien,ps.tgllahir,ps.alamat AS alamatlengkap,jk.jeniskelamin,ps.nohp as notelepon
                "))
                ->where('ps.koders', $idProfile)
                ->where('ps.aktif', true)
                ->where('ps.id', $idPasien);
        }


        $data = $data->first();
        $result = array(
            'result' => $data,
            'message' => 'godU',
        );
        return $this->respond($result);
    }

    public function getDataComboKelompokpPasien(Request $request)
    {
        $req = $request->all();
        $dataProduk = \DB::table('kelompokpasienmt')
            ->select('id as value', 'kelompokpasien as text')
            ->where('aktif', true)
            ->orderBy('id');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $dataProduk = $dataProduk->where('kelompokpasien', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();
        return $this->respond($dataProduk);
    }

    public function getDataComboAsalRujukan(Request $request)
    {
        $req = $request->all();
        $dataProduk = \DB::table('rujukanasalmt')
            ->select('id as value', 'asalrujukan as text')
            ->where('aktif', true)
            ->orderBy('id');
        if (isset($req['filter']['filters'][0]['value']) &&
            $req['filter']['filters'][0]['value'] != "" &&
            $req['filter']['filters'][0]['value'] != "undefined") {
            $dataProduk = $dataProduk->where('asalrujukan', 'ilike', '%' . $req['filter']['filters'][0]['value'] . '%');
        };
        $dataProduk = $dataProduk->take(10);
        $dataProduk = $dataProduk->get();

        return $this->respond($dataProduk);
    }

    public function getDataRiwayatEMR(Request $request)
    {
        $idProfile = (int)$this->getDataKdProfile($request);
        $tglAwal = $request['tglAwal'];
        $tglAkhir = $request['tglAkhir'];
        $paramNoRM = '';
        $paramtglLahir = '';
        $paramnamaPasien = '';
        $paramnamaPasien2 = '';
        $paramID = '';
        if (isset($request['norm']) && $request['norm'] != '') {
            $paramNoRM = " AND pm.norm  ILIKE '%" . $request['norm'] . "%'";
        }
        if (isset($request['namaPasien']) && $request['namaPasien'] != '') {
            $paramnamaPasien = " AND pm.namapasien  ILIKE '%" . $request['namaPasien'] . "%'";
            $paramnamaPasien2 = " AND ep.namapasien  ILIKE '%" . $request['namaPasien'] . "%'";
        }
        if (isset($request['tglLahir']) && $request['tglLahir'] != '') {
            $paramtglLahir = " AND to_char(pm.tgllahir,'yyyy-MM-dd')  =" . $request['tglLahir'] . "%'";
        }
        if (isset($request['idPasienTriase']) && $request['idPasienTriase'] != '') {
            $paramID = " AND ps.id = " . $request['idPasienTriase'];
        }

        $data2 = DB::select(DB::raw("
                SELECT ep.nocm,pm.namapasien,pm.jeniskelaminidfk,jk.jeniskelamin,pm.tempatlahir,pm.tgllahir,
                       pm.notelepon,alm.alamatlengkap,ep.noemr,ep.tglemr,ep.noregistrasi,'pasienlama' AS status,
                       pm.id AS idpasien
                FROM emrpasientr AS ep
                INNER JOIN pasienmt AS pm ON pm.norm = ep.nocm
                INNER JOIN alamatmt AS alm ON alm.normidfk = pm.id
                INNER JOIN jeniskelaminmt AS jk ON jk.id = pm.jeniskelaminidfk
                WHERE ep.koders = $idProfile AND ep.aktif = true
                AND ep.namaruangan = 'Triage Gawat Darurat'
                AND ep.nocm <> '-'
                AND ep.tglemr >= '$tglAwal'
                AND ep.tglemr <= '$tglAkhir'
               $paramNoRM
               $paramtglLahir
               $paramnamaPasien

                UNION ALL

                 SELECT ep.nocm,ep.namapasien,jk.id AS objectjeniskelaminfk,jk.jeniskelamin,
                        ps.tempatlahir,ps.tgllahir,ps.nohp as notelepon,ps.alamat AS alamatlengkap,ep.noemr,ep.tglemr,
                        ep.noregistrasi,'pasienbaru' AS status,ps.id AS idpasien
                FROM emrpasientr AS ep
                INNER JOIN pasientriasemt AS ps ON ps.id = CAST(ep.nocm AS INTEGER)
                LEFT JOIN jeniskelaminmt AS jk ON jk.id = ps.jeniskelaminidfk
                WHERE ep.koders = $idProfile
                AND ep.aktif = true
                AND ep.namaruangan = 'Triage Gawat Darurat'
               $paramtglLahir
               $paramnamaPasien2
               $paramID
        "));

        if (count($data2) > 0) {
            foreach ($data2 as $key => $row) {
                $count[$key] = $row->tglemr;
            }
            array_multisort($count, SORT_DESC, $data2);
        }

        $result = array(
            'data' => $data2,
            'message' => 'slvR',
        );
        return $this->respond($result);
    }

    public function getSOAP(Request $request)
    {
        $idProfile = $this->getDataKdProfile($request);
        $data = \DB::table('soaptr as so')
            ->Join('daftarpasienruangantr as apd', 'apd.norec', '=', 'so.daftarpasienruanganfk')
            ->Join('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
            ->leftJoin('pegawaimt as pet', 'pet.id', '=', 'so.pegawaiidfk')
            ->leftJoin('ruanganmt as ru', 'ru.id', '=', 'apd.ruanganidfk')
            ->select('so.*', 'pd.noregistrasi', 'pet.namalengkap', 'ru.namaruangan')
            ->where('so.koders', $idProfile)
            ->where('so.aktif', true)
            ->orderBy('so.tglinput', 'desc');
        if (isset($request['norecpd']) && $request['norecpd'] != '') {
            $data = $data->where('pd.norec', $request['norecpd']);
        }
        if (isset($request['dokterid']) && $request['dokterid'] != '') {
            $data = $data->where('pg.id', $request['dokterid']);
        }
        if (isset($request['nocm']) && $request['nocm'] != '') {
            $data = $data->where('ps.norm', $request['nocm']);
        }
        if (isset($request['noregistrasi']) && $request['noregistrasi'] != '') {
            $data = $data->where('pd.noregistrasi', $request['noregistrasi']);
        }
        $data = $data->get();
        foreach ($data as $d) {
            $d->details = array([
                'details' => array(
                    ['isi' => $d->s, 'jenis' => 's', 'norec' => $d->norec],
                    ['isi' => $d->o, 'jenis' => 'o', 'norec' => $d->norec],
                    ['isi' => $d->a, 'jenis' => 'a', 'norec' => $d->norec],
                    ['isi' => $d->p, 'jenis' => 'p', 'norec' => $d->norec],
                )
            ]);
        }
        $result = array(
            'data' => $data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }

    public function saveSOAP(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        DB::beginTransaction();
        try {
            if ($request['method'] == 'delete') {
                $transMessage = "Hapus ";

                $dataAPD = SOAP::where('norec', $request['norec'])->update(['aktif' => false]);
            } else {

                if ($request['norec'] == '') {
                    $dataAPD = new SOAP();
                    $dataAPD->norec = $dataAPD->generateNewId();
                    $dataAPD->koders = $idProfile;
                    $dataAPD->aktif = true;
                } else {
                    $dataAPD = SOAP::where('norec', $request['norec'])->first();
                }
                $dataAPD->tglinput = date('Y-m-d H:i:s');
                $dataAPD->s = $request['s'];
                $dataAPD->o = $request['o'];
                $dataAPD->a = $request['a'];
                $dataAPD->p = $request['p'];
                $dataAPD->daftarpasienruanganfk = $request['daftarpasienruanganfk'];
                $dataAPD->pegawaiidfk = $this->getCurrentUserID();
                $dataAPD->save();
                $transMessage = "Simpan ";
            }
            $transStatus = 'true';

        } catch (\Exception $e) {
            $transStatus = 'false';
            $transMessage = " Gagal";
        }

        if ($transStatus != 'false') {
            DB::commit();
            $result = array(
                "status" => 201,
                "data" => $dataAPD,
            );
        } else {
            DB::rollBack();
            $result = array(
                "status" => 400,
                "e" => $e->getMessage(),
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDataComboResepEMR(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int)$kdProfile;
        $dataLogin = $request->all();

        $dept = $this->settingDataFixed('kdDepartemenFarmasi', $kdProfile);

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
            ->where('kp.koders', $kdProfile)
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
        $kdProfile = $this->getDataKdProfile($request);
        $set = explode(',', $this->settingDataFixed('kdJenisProdukObat', $kdProfile));
        $arrPo = [];
        foreach ($set as $ie) {
            $arrPo [] = (int)$ie;
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
            ->select('ks.produkidfk', 'ks.satuanstandar_asal', 'ss.satuanstandar', 'ks.satuanstandar_tujuan', 'ss2.satuanstandar as satuanstandar2',
                'ks.nilaikonversi')
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
    public function SimpanOrderPelayananObat(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;

        if ($request['data'][0]['strukorder']['penulisresepfk'] == "") {
            $dokter2 = null;
        } else {
            $dokter2 = $request['data'][0]['strukorder']['penulisresepfk'];
        }
        DB::beginTransaction();
        try {
            foreach($request['data'] as $data)
            {
                $r_SR = $data['strukorder'];

                $dataDetail = \DB::table('daftarpasienruangantr as apd')
                    ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                    ->select('pd.norec', 'pd.normidfk as  nocmfk', 'apd.ruanganidfk as objectruanganfk', 'pd.kelasidfk as objectkelasfk', 'apd.norec as apdnorec')
                    ->where('apd.norec', $r_SR['noregistrasifk'])
                    ->where('apd.aktif',true)
                    ->first();

                $pasien = DB::table('pasienmt')->where('id',$dataDetail->nocmfk)->first();
                if ($r_SR['norec'] == '') {
                    $noOrder = $this->generateCodeBySeqTable(new TransaksiOrder, 'noorderresep', 10, date('ym'),$idProfile);
                    if ($noOrder == ''){
                        $transMessage = "Gagal mengumpukan data, Coba lagi.!";
                        DB::rollBack();
                        $result = array(
                            "status" => 400,
                            "message"  => $transMessage,
                            "as" => 'slvR',
                        );
                        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
                    }

                    $newSO = new TransaksiOrder();
                    $norecSO = $newSO->generateNewId();
                    $newSO->norec = $norecSO;
                    $newSO->koders = $idProfile;
                    $newSO->aktif = true;
                } else {
                    $newSO = TransaksiOrder::where('norec', $r_SR['norec'])->first();
                    $noOrder = $newSO->noorder;
                    TransaksiOrderDetail::where('transaksiorderfk', $newSO->norec)
                        ->where('koders',$kdProfile)
                        ->delete();
                }

                $newSO->normidfk = $dataDetail->nocmfk;
                $newSO->kddokter = $r_SR['penulisresepfk'];
                $newSO->jenisorderidfk = 5;
                $newSO->isdelivered = 1;
                $newSO->kelompoktransaksiidfk = $this->settingDataFixed('kdKelompokTransaksiOrderResep',$kdProfile);
                $newSO->keteranganorder = 'Order Farmasi';
                $newSO->noorder = $noOrder;
                $newSO->registrasipasienfk = $dataDetail->norec;
                $newSO->pegawaiorderidfk = $r_SR['penulisresepfk'];
                $newSO->qtyproduk = $r_SR['qtyproduk'];
                $newSO->qtyjenisproduk = $r_SR['qtyproduk'];
                $newSO->ruanganidfk = $dataDetail->objectruanganfk;
                $newSO->ruangantujuanidfk = $r_SR['ruanganfk'];
                $newSO->statusorder = 1;
                $newSO->tglorder = $r_SR['tglresep'];
                $newSO->totalbeamaterai = 0;
                $newSO->totalbiayakirim = 0;
                $newSO->totalbiayatambahan = 0;
                $newSO->totaldiscount = 0;
                $newSO->totalhargasatuan = 0;
                $newSO->totalharusdibayar = 0;
                $newSO->totalpph = 0;
                $newSO->totalppn = 0;
                if(isset($r_SR['isreseppulang']) && $r_SR['isreseppulang'] == 1){
                    $newSO->isreseppulang = true;
                }
                if (isset($r_SR['noruangan'])) {
                    $newSO->nourutruangan = $r_SR['noruangan'];
                }
                $newSO->save();

                $norec_SR = $newSO->norec;

                $r_PP = $data['orderfarmasi'];
                $prod = [];
                foreach ($r_PP as $r_PPL) {
                    $qtyJumlah = (float)$r_PPL['jumlah'] * (float)$r_PPL['nilaikonversi'];
                    $pro = DB::table('pelayananmt')->where('id',$r_PPL['produkfk'])->first();
                    $prod[] =$pro->namaproduk;

                    $newPP = new TransaksiOrderDetail();
                    $norecPP = $newPP->generateNewId();
                    $newPP->norec = $norecPP;
                    $newPP->koders = $idProfile;
                    $newPP->aktif = true;
                    $newPP->aturanpakai = $r_PPL['aturanpakai'];
                    $newPP->isreadystok = 1;
                    $newPP->kddokter = $r_SR['penulisresepfk'];;
                    $newPP->kelasidfk = $dataDetail->objectkelasfk;;
                    $newPP->normidfk = $dataDetail->nocmfk;
                    $newPP->transaksiorderfk = $norec_SR;
                    $newPP->registrasipasienfk = $dataDetail->norec;
                    $newPP->produkidfk = $r_PPL['produkfk'];
                    $newPP->qtyproduk = $r_PPL['jumlah'];
                    $newPP->qtystokcurrent = $r_PPL['jmlstok'];
                    $newPP->racikanke = $r_PPL['rke'];
                    $newPP->ruanganidfk = $dataDetail->objectruanganfk;
                    $newPP->ruangantujuanidfk = $r_PPL['ruanganfk'];
                    $newPP->satuanstandaridfk = $r_PPL['satuanstandarfk'];
                    $newPP->transaksiorderfk = $norec_SR;
                    $newPP->tglpelayanan = $r_SR['tglresep'];
                    if (isset($r_PPL['jenisobatfk'])) {
                        $newPP->jenisobatidfk = $r_PPL['jenisobatfk'];//5;
                    }
                    $newPP->jumlah = $qtyJumlah;//$r_PPL['jumlah'];
                    $newPP->iscito = 0;
                    $newPP->hargasatuan = $r_PPL['hargasatuan'];
                    $newPP->hargadiscount = $r_PPL['hargadiscount'];
                    $newPP->qtyprodukretur = 0;
                    $newPP->hasilkonversi = $r_PPL['nilaikonversi'];;
                    $newPP->jeniskemasanidfk = $r_PPL['jeniskemasanfk'];
                    $newPP->dosis = $r_PPL['dosis'];
                    $newPP->rke = $r_PPL['rke'];
                    $newPP->satuanviewidfk = $r_PPL['satuanviewfk'];
                    $newPP->ispagi = isset( $r_PPL['ispagi']) ? $r_PPL['ispagi'] : null;
                    $newPP->issiang = isset( $r_PPL['issiang'] )? $r_PPL['issiang'] : null;
                    $newPP->ismalam =isset( $r_PPL['ismalam']) ? $r_PPL['ismalam'] : null;
                    $newPP->issore = isset($r_PPL['issore']) ? $r_PPL['issore'] : null;
                    $newPP->keteranganpakai = $r_PPL['keterangan'];
                    if (isset($r_PPL['satuanresepfk'])){
                        $newPP->satuanresepidfk = $r_PPL['satuanresepfk'];
                    }
                    if (isset($r_PPL['tglkadaluarsa']) && $r_PPL['tglkadaluarsa'] != 'Invalid date' && $r_PPL['tglkadaluarsa'] != ''){
                        $newPP->tglkadaluarsa = $r_PPL['tglkadaluarsa'];
                    }
                    if (isset($r_PPL['isoutofstok'])  && $r_PPL['isoutofstok'] != ''){
                        $newPP->isoutofstok = $r_PPL['isoutofstok'];
                    }
                    $newPP->save();

                }

            }
            $statusBot = false;
            $telegram_id = '' ;
            foreach ($request['data'] as $data){
                if ($data['strukorder']['ruanganfk'] == 94) {
                    $telegram_id = '-437464365';
                    $statusBot = true;
                }
                if ($data['strukorder']['ruanganfk'] == 116) {
                    $telegram_id = '-573225350';
                    $statusBot = true;
                }
                if ($data['strukorder']['ruanganfk'] == 125) {
                    $telegram_id = '-598519318';
                    $statusBot = true;
                }
                if ($data['strukorder']['ruanganfk'] == 556) {
                    $telegram_id = '-443840485';
                    $statusBot = true;
                }
                if ($data['strukorder']['ruanganfk'] == 744) {
                    $telegram_id = '-1001433844424';
                    $statusBot = true;
                }
                $tglresep = $data['strukorder']['tglresep'];
                if($statusBot){
                    $setting = $this->settingDataFixed('settingOrderTelegramLab', $idProfile);
                    if(!empty( $setting) &&  $setting == 'true'){
                        $to =  DB::table('ruanganmt')->where('id',$data['strukorder']['ruanganfk'])->first();
                        $peg=  DB::table('pegawaimt')->where('id',  $dokter2)->first();
                        $cito = '';
                        if(isset($request['iscito']) &&$request['iscito']=='true'){
                            $cito =' Cito ';
                        }
                        if($data['strukorder']['isreseppulang'] == 1){
                            $cito =' RESEP PULANG ';
                        }
                        $secret_token = "1545548931:AAHwGMJrXxGMc609WwO9e2UQTcRquu5ri-M";
                        $produks ='';
                        foreach ($prod as $key => $value) {
                            $produks = $produks ." \n ". $value;
                        }

                        $url = "https://api.telegram.org/bot" . $secret_token . "/sendMessage?parse_mode=html&chat_id=" . $telegram_id;
                        $url = $url . "&text=" . urlencode("✅ Order Baru : <b> ".$to->namaruangan.
                                "</b>  \n Pengorder : <b>".$peg->namalengkap."</b>  \n Pasien : <b>".$pasien->namapasien. " (".$pasien->norm.") ".
                                "</b> \n Tgl Order: <b>".$tglresep.
                                "</b> \n Status : <b>".$cito."</b>  \n Nama Obat : <b>".$produks."</b> \n\n");

                        $ch = curl_init();
                        $optArray = array(
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true
                        );
                        curl_setopt_array($ch, $optArray);
                        $result = curl_exec($ch);
                        curl_close($ch);
                    }
                }
            }



            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }
//
        if ($transStatus == 'true') {
            $transMessage = "Selesai";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "noresep" => $newSO,
                "as" => 'slvR',
            );
        } else {
            $transMessage = "Gagal coba lagi";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                 "e" => $e->getMessage().' '.$e->getLine(),
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function getDaftarDetailOrder(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $data = \DB::table('transaksiordertr as so')
            ->JOIN('pasienmt as ps', 'ps.id', '=', 'so.normidfk')
            ->JOIN('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
            ->JOIN('ruanganmt as ru2', 'ru2.id', '=', 'so.ruangantujuanidfk')
            ->leftJOIN('pegawaimt as pg', 'pg.id', '=', 'so.pegawaiorderidfk')
            ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'so.registrasipasienfk')
            ->JOIN('kelasmt as kl', 'kl.id', '=', 'pd.kelasidfk')
            ->leftJOIN('statuspengerjaanmt as stt', 'stt.id', '=', 'so.statusorder')
            ->leftJOIN('daftarpasienruangantr as apd', function ($join) {
                $join->on('apd.registrasipasienfk', '=', 'pd.norec')
                    ->on('apd.ruanganidfk', '=', 'so.ruanganidfk');
            })
            ->select('so.noorder', 'ps.norm as nocm', 'ps.namapasien', 'ru.namaruangan as namaruanganrawat',
                'so.tglorder', 'pg.namalengkap', 'ru2.namaruangan',
                'stt.statuspengerjaan as statusorder', 'so.namapengambilorder', 'so.registrasipasienfk as  noregistrasifk',
                'pd.noregistrasi',
                'apd.norec as norec_apd',
                'pd.tglregistrasi', 'ps.tgllahir', 'kl.namakelas', 'kl.id as klid', 'so.tglambilorder', 'so.norec as norec_order','so.isreseppulang')
            ->where('so.koders', $idProfile);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('so.tglorder', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'] . " 23:59:59";
            $data = $data->where('so.tglorder', '<=', $tgl);
        }
        if (isset($request['nocm']) && $request['nocm'] != "" && $request['nocm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['nocm'] . '%');
        }
        if (isset($request['norec_apd']) && $request['norec_apd'] != "" && $request['norec_apd'] != "undefined") {
            $data = $data->where('apd.norec', $request['norec_apd']);
        }
        if (isset($request['noreg']) && $request['noreg'] != "" && $request['noreg'] != "undefined") {
            $data = $data->where('pd.noregistrasi', $request['noreg']);
        }
        $data = $data->where('so.kelompoktransaksiidfk',  $this->settingDataFixed('kdKelompokTransaksiOrderResep',$idProfile));
        $data = $data->where('so.aktif', true);
        $data = $data->get();
        $status = '';

        $result = [];
        foreach ($data as $item) {
            $details = DB::select(DB::raw("
                    SELECT op.rke, jk.jeniskemasan, pr.namaproduk, ss.satuanstandar, op.aturanpakai,
                            op.jumlah, op.hargasatuan,op.keteranganpakai as keterangan,
                           op.satuanresepidfk as satuanresepfk,sn.satuanresep,op.tglkadaluarsa
                    from transaksiorderdetailtr as op
                     join pelayananmt as pr on pr.id=op.produkidfk
                    left join jeniskemasanmt as jk on jk.id=op.jeniskemasanidfk
                    left join satuanstandarmt as ss on ss.id=op.satuanstandaridfk
                    left join satuanresepmt as sn on sn.id=op.satuanresepidfk
                    where op.koders = $idProfile and op.aktif = true and transaksiorderfk=:noorder"),
                array(
                    'noorder' => $item->norec_order,
                )
            );
            $result[] = array(
                'noregistrasi' => $item->noregistrasi,
                'norec_order' => $item->norec_order,
                'norec' => $item->noregistrasifk,
                'tglregistrasi' => $item->tglregistrasi,
                'norec_apd' => $item->norec_apd,
                'noorder' => $item->noorder,
                'nocm' => $item->nocm,
                'namapasien' => $item->namapasien,
                'namaruanganrawat' => $item->namaruanganrawat,
                'tglorder' => $item->tglorder,
                'namalengkap' => $item->namalengkap,

                'namaruangan' => $item->namaruangan,
                'statusorder' => $item->statusorder ,
                'namapengambilorder' => $item->namapengambilorder,
                'tgllahir' => $item->tgllahir,
                'klid' => $item->klid,
                'namakelas' => $item->namakelas,
                'reseppulang' => $item->isreseppulang,
                'details' => $details
            );
        }

        return $this->respond($result);

    }
    public function hapusOrderResep(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        DB::beginTransaction();
        try {
            $so = TransaksiOrder::where('norec', $request['norec'])->where('koders', $idProfile)->first();
            if ($so->statusorder == 5) {
                $transMessage = "Tidak Bisa dihapus sudah Di Verifikasi";
                $result = array(
                    "status" => 400,
                    "message" => $transMessage,
                    "as" => 'Xoxo',
                );
                return $this->setStatusCode($result['status'])->respond($result, $transMessage);
            }
            TransaksiOrder::where('norec', $request['norec'])->update
            (['aktif' => false]
            );

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            $transMessage = "Data Terhapus";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'Xoxo',
            );

        } else {
            $transMessage = "Hapus Gagal";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "as" => 'Xoxo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }

    public function SimpanPelayananAlkesBhp(Request $request){
        $idProfile = (int) $this->getDataKdProfile($request);        
        $kdJenisTransaksiResep = (int) $this->settingDataFixed('KdTransaksiAlkesRuangan', $idProfile);
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

        $noResep = $this->generateCodeBySeqTable(new TransaksiAlkes, 'noalkes', 12, 'TA/' . $this->getDateTime()->format('ym') . '/', $idProfile);        
        if ($noResep == '') {
            $ReportTrans = "Gagal mengumpukan data, Coba lagi.!";
            \DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "tb" => 'EREA',
            );
            return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
        }
        DB::beginTransaction();
        try {   
            $dataLogin = $request->all();
            $r_SR = $request['data']['strukorder'];
            $tglTrans =  date('Y-m-d H:i:s');
            $dataAntrian = DaftarPasienRuangan::where('norec', $r_SR['noregistrasifk'])
                ->where('koders', $idProfile)
                ->where('aktif', true)
                ->first();            
            $resepOld = TransaksiAlkes::where('norec', $r_SR['norec'])
                        ->where('koders', $idProfile)->first();

            if ($r_SR['norec'] == "") {
                $newSR = new TransaksiAlkes();
                $norecSR = $newSR->generateNewId();
                $newSR->norec = $norecSR;
            } else {
                $newSR = TransaksiAlkes::where('norec', $r_SR['norec'])->where('koders', $idProfile)->first();
                $noResep = $newSR->noalkes;
            }
                $newSR->koders = $idProfile;
                $newSR->aktif = true;
                $newSR->noalkes = $noResep;
                $newSR->kelompoktransaksiidfk = $kdJenisTransaksiResep;
                $newSR->daftarpasienruanganfk = $r_SR['noregistrasifk'];
                $newSR->petugasidfk = $r_SR['penulisresepfk'];
                $newSR->ruanganidfk = $r_SR['ruanganfk'];
                $newSR->status = 0;
                $newSR->tglinput =  $r_SR['tglresep'];
                $newSR->petugasinputidfk =  $dataPegawaiUser[0]->id;
                $newSR->save();
                $norec_SR = $newSR->norec;
                $dokterPenulis =  $newSR->petugasidfk;            

            if ($r_SR['norec'] != '') {
                TransaksiKartuStok::where('keterangan', 'Input Alkse & BHP ' . ' ' . $noResep)
                    ->where('koders', $idProfile)
                    ->update([
                        'flagfk' => null
                    ]);

                $tglnow = date('Y-m-d H:i:s');
                $tglUbah = date('Y-m-d H:i:s', strtotime('-5 seconds', strtotime($tglTrans)));

                //##PENAMBAHAN KEMBALI STOKPRODUKDETAIL
                $dataKembaliStok = collect(DB::select("
                            select pp.norec,pp.strukterimaidfk AS strukterimafk,pp.jumlah,pp.nilaikonversi,sr.ruanganidfk AS ruanganfk,
                                   pp.produkidfk AS produkfk
                            from transaksipasientr as pp
                            INNER JOIN transaksialkestr sr on sr.norec = pp.transaksialkesfk
                            where pp.koders = $idProfile 
                            and sr.koders = $idProfile 
                            and sr.norec='$norec_SR'                            
                    "));                    
               
                if ($r_SR['ruanganfk'] == $resepOld->ruanganidfk) {
                    foreach ($dataKembaliStok as $item5) {
                        $saldoAwal = 0;
                        $saldoAkhir = 0;
                        $TambahStok = (float)$item5->jumlah;
                        $dataSaldoAwal = collect(DB::select("
                                SELECT sum(qtyproduk) as qty FROM transaksistoktr 
                                WHERE koders = $idProfile AND ruanganidfk=$resepOld->ruanganidfk 
                                AND produkidfk=$item5->produkfk"))->first();
                        $saldoAwal = (float)$dataSaldoAwal->qty;
                        $saldoAkhir = (float)$dataSaldoAwal->qty + $TambahStok;                       

                        $datStok = TransaksiStok::where('nostrukterimafk', $item5->strukterimafk)
                                ->where('aktif', true)
                                ->where('koders', $idProfile)
                                ->where('ruanganidfk', $item5->ruanganfk)
                                ->where('produkidfk', $item5->produkfk)
                                ->orderby('tglkadaluarsa', 'asc')
                                ->first();

                        DB::table('transaksistoktr')
                            ->where('koders', $idProfile)
                            ->where('norec', $datStok->norec)
                            ->sharedLock()
                            ->increment('qtyproduk', (float)$TambahStok);

                        $TRANS_KS = array(
                            "saldoawal" => $saldoAwal,
                            "qtyin" => (float)$TambahStok,
                            "qtyout" => 0,
                            "saldoakhir" => $saldoAkhir,
                            "keterangan" => 'Ubah Alkse & BHP ' . $noResep,
                            "produkidfk" => $item5->produkfk,
                            "ruanganidfk" => $r_SR['ruanganfk'],
                            "tglinput" => $tglUbah,
                            "tglkejadian" => $tglUbah,
                            "nostrukterimaidfk" => $item5->strukterimafk,
                            "norectransaksi" => $item5->norec,
                            "tabletransaksi" => 'transaksipasientr',                            
                            "flagfk" => null,
                        );                        
                        $this->KartuStok($idProfile, $TRANS_KS);                        
                    }
                } else {
                    foreach ($dataKembaliStok as $item5) {
                        $TambahStok = (float)$item5->jumlah;
                        $saldoAwal = 0;
                        $saldoAkhir = 0;
                        $dataSaldoAwal = collect(DB::select("
                                SELECT sum(qtyproduk) as qty FROM transaksistoktr 
                                WHERE koders = $idProfile AND ruanganidfk=$resepOld->ruanganfk AND produkidfk=$item5->produkfk
                        "))->first();

                        $saldoAwal = (float)$dataSaldoAwal->qty;
                        $saldoAkhir = (float)$dataSaldoAwal->qty + $TambahStok;

                        $datStokR = TransaksiStok::where('nostrukterimafk', $item5->strukterimaidfk)
                                ->where('aktif', true)
                                ->where('koders', $idProfile)
                                ->where('ruanganidfk', $item5->ruanganfk)
                                ->where('produkidfk', $item5->produkfk)
                                ->orderby('tglkadaluarsa', 'asc')
                                ->first();

                        DB::table('transaksistoktr')
                            ->where('koders', $idProfile)
                            ->where('norec', $datStokR->norec)
                            ->sharedLock()
                            ->increment('qtyproduk', (float)$TambahStok);

                        $TRANS_KS = array(
                            "saldoawal" => $saldoAwal,
                            "qtyin" => (float)$TambahStok,
                            "qtyout" => 0,
                            "saldoakhir" => $saldoAkhir,
                            "keterangan" => 'Ubah Alkse & BHP '. $noResep,
                            "produkidfk" => $item5->produkfk,
                            "ruanganidfk" => $r_SR['ruanganfk'],
                            "tglinput" => $tglUbah,
                            "tglkejadian" => $tglUbah,
                            "nostrukterimaidfk" => $item5->nostrukterimafk,
                            "norectransaksi" => $item5->norec,
                            "tabletransaksi" => 'transaksipasientr',                            
                            "flagfk" => null,
                        );
                        $this->KartuStok($idProfile, $TRANS_KS);
                    }
                }

                //### LOGACC untuk penjurnalan blm ada
                $HapusPP = TransaksiPasien::where('transaksialkesfk', $norec_SR)->where('koders', $idProfile)->get();
                // foreach ($HapusPP as $pp){
                //     $HapusPPD = TransaksiPasienDetail::where('transaksipasienfk', $pp['norec'])->where('koders', $idProfile)->delete();
                //     $HapusPPP = PetugasPelaksana::where('transaksipasienfk', $pp['norec'])->where('koders', $idProfile)->delete();
                // }
                $HpsPP = TransaksiPasien::where('transaksialkesfk', $norec_SR)->where('koders', $idProfile)->delete();
            }

            $r_PP = $request['data']['orderfarmasi'];
            foreach ($r_PP as $r_PPL) {
                $qtyJumlah = (float)$r_PPL['jumlah'] * (float)$r_PPL['nilaikonversi'];
                $newPP = new TransaksiPasien();
                $norecPP = $newPP->generateNewId();
                $newPP->norec = $norecPP;
                $newPP->koders = $idProfile;
                $newPP->aktif = true;
                $newPP->daftarpasienruanganfk = $r_SR['noregistrasifk'];
                $newPP->tglregistrasi = $dataAntrian->tglregistrasi;                                
                if (isset($r_PPL['hargadiscount']) && $r_PPL['hargadiscount'] != "null" || $r_PPL['hargadiscount'] != null){
                    $newPP->hargadiscount = $r_PPL['hargadiscount'];
                }else{
                    $newPP->hargadiscount = 0;
                }
                if (isset($r_PPL['persendiscount'])) {
                    $newPP->persendiscount = $r_PPL['persendiscount'];
                } else {
                    $newPP->persendiscount = 0;
                }
                $newPP->hargajual = $r_PPL['hargajual'];
                $newPP->hargasatuan = 0;              
                $newPP->jumlah = $qtyJumlah;
                $newPP->kelasidfk = $dataAntrian->kelasidfk;                
                $newPP->produkidfk = $r_PPL['produkfk'];               
                $newPP->stock = $r_PPL['stock'];
                $newPP->tglpelayanan = $r_SR['tglresep'];
                $newPP->harganetto = 0;                
                $newPP->transaksialkesfk = $norec_SR;
                $newPP->satuanviewidfk = $r_PPL['satuanviewfk'];
                $newPP->nilaikonversi = $r_PPL['nilaikonversi'];
                $newPP->strukterimaidfk = $r_PPL['nostrukterimafk'];                               
                if (isset($r_PPL['jasa'])) {
                    $newPP->jasa = $r_PPL['jasa'];
                } else {
                    $newPP->jasa = 0;
                }
                $newPP->qtydetailresep = $r_PPL['jumlah'];
                $newPP->isalkes = true;                
                $newPP->keteranganpakai = $r_PPL['keterangan'];               
                if (isset($r_PPL['tglkadaluarsa']) && $r_PPL['tglkadaluarsa'] != 'Invalid date' && $r_PPL['tglkadaluarsa'] != '') {
                    $newPP->tglkadaluarsa = $r_PPL['tglkadaluarsa'];
                }                
                $newPP->save();               

                $dataPP[] = $newPP;
                $norec_PP = $newPP->norec;                                

                $GetNorec = TransaksiStok::where('nostrukterimafk',$r_PPL['nostrukterimafk'])                 
                    ->where('koders', $idProfile)
                    ->where('ruanganidfk',$r_PPL['ruanganfk'])
                    ->where('produkidfk',$r_PPL['produkfk'])
                    ->select('norec','qtyproduk')
                    ->get();

                $jmlPengurang =(float)$qtyJumlah;
                $kurangStok = (float)0;
                $dataSaldoAwal = collect(DB::select("
                    SELECT sum(qtyproduk) as qty FROM transaksistoktr 
                    WHERE koders = $idProfile AND ruanganidfk=$r_PPL[ruanganfk] 
                    AND produkidfk=$r_PPL[produkfk]
                "))->first();
                $namaProduk = $r_PPL['namaproduk'];
                $saldoAwalIn = (float)$dataSaldoAwal->qty;
                $saldoAkhirIn = (float)$dataSaldoAwal->qty - $jmlPengurang;
                $sqty = 0;

                $newSPD = TransaksiStok::where('nostrukterimafk', $r_PPL['nostrukterimafk'])
                    ->where('koders', $idProfile)
                    ->where('ruanganidfk', $r_PPL['ruanganfk'])
                    ->where('produkidfk', $r_PPL['produkfk'])
                    ->where('qtyproduk','>', $jmlPengurang)
                    ->first();

                if(empty($newSPD) ){
                    $transMessage = "Simpan Resep Gagal, cek stok barang " . $namaProduk . ' ' .$r_PPL['produkfk'];
                    DB::rollBack();
                    $result = array(
                        "status" => 400,
                        "message"  => $transMessage,
                        "data" => $r_PPL,
                        "as" => 'epic',
                    );
                    return $this->setStatusCode($result['status'])->respond($result, $transMessage);
                }
                DB::table('transaksistoktr')
                    ->where('norec', $newSPD->norec)
                    ->where('koders', $idProfile)
                    ->sharedLock()
                    ->decrement('qtyproduk', (float)$jmlPengurang);

                $dataKS[] =[];
                if((float)$dataSaldoAwal->qty == 0 || $jmlPengurang > (float)$dataSaldoAwal->qty){
                    $transMessage = "Simpan Resep Gagal, Stok Produk ". $namaProduk .", ada " . (float)$dataSaldoAwal->qty . " Data Stok Kurang Dari Qty Resep !";
                    DB::rollBack();
                    $result = array(
                        "status" => 400,
                        "message"  => $transMessage,
                        "as" => 'epic',
                    );
                    return $this->setStatusCode($result['status'])->respond($result, $transMessage);
                }

                $TRANS_KS = array(
                    "saldoawal" => (float)$saldoAwalIn,
                    "qtyin" => 0,
                    "qtyout" => (float)$qtyJumlah,
                    "saldoakhir" => (float)$saldoAkhirIn,
                    "keterangan" => 'Input Alkse & BHP ' . $noResep,
                    "produkidfk" =>  $r_PPL['produkfk'],
                    "ruanganidfk" => $r_PPL['ruanganfk'],
                    "tglinput" => date('Y-m-d H:i:s'),
                    "tglkejadian" => $tglTrans,
                    "nostrukterimaidfk" => $r_PPL['nostrukterimafk'],
                    "norectransaksi" => $norec_PP,
                    "tabletransaksi" => 'transaksipasientr',
                    // "transstokfk" => $r_PPL['norec_stok'],
                    "flagfk" => 13,
                );
                $this->KartuStok($idProfile, $TRANS_KS);

                $dataKS[] = $TRANS_KS;                
            }
            

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transError = $e->getMessage();
        }

        if ($transStatus == 'true') {
            $ReportTrans = "Simpan Pelayanan Alkes Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $ReportTrans,
                "resep" => $newSR,
                "dataPP" => $dataPP,
                "dataKS" => $TRANS_KS,
                "R_PPL" => $r_PPL,
                "tb" => 'EREA',
            );
        } else {
            $ReportTrans = "Simpan Pelayanan Alkes Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $ReportTrans,
                "e"  => $transError,
                "tb" => 'EREA',
            );
        }

        return $this->setStatusCode($result['status'])->respond($result, $ReportTrans);
    }

    public function getDaftarDetailInputAlkes(Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $data = \DB::table('transaksialkestr as so')
            ->join('daftarpasienruangantr AS dpr','dpr.norec','=','so.daftarpasienruanganfk')
            ->join('registrasipasientr AS rg','rg.norec','=','dpr.registrasipasienfk')
            ->leftJOIN('ruanganmt as ru', 'ru.id', '=', 'so.ruanganidfk')
            ->leftJOIN('pegawaimt as pg', 'pg.id', '=', 'so.petugasidfk')           
            ->select(DB::raw("
                so.norec,so.tglinput,so.noalkes,so.ruanganidfk,ru.namaruangan,so.petugasidfk,pg.namalengkap                
            "))
            ->where('so.koders', $idProfile);

        if (isset($request['tglAwal']) && $request['tglAwal'] != "" && $request['tglAwal'] != "undefined") {
            $data = $data->where('so.tglorder', '>=', $request['tglAwal']);
        }
        if (isset($request['tglAkhir']) && $request['tglAkhir'] != "" && $request['tglAkhir'] != "undefined") {
            $tgl = $request['tglAkhir'] . " 23:59:59";
            $data = $data->where('so.tglorder', '<=', $tgl);
        }
        if (isset($request['nocm']) && $request['nocm'] != "" && $request['nocm'] != "undefined") {
            $data = $data->where('ps.norm', 'ilike', '%' . $request['nocm'] . '%');
        }
        if (isset($request['norec_apd']) && $request['norec_apd'] != "" && $request['norec_apd'] != "undefined") {
            $data = $data->where('apd.norec', $request['norec_apd']);
        }
        if (isset($request['noreg']) && $request['noreg'] != "" && $request['noreg'] != "undefined") {
            $data = $data->where('rg.noregistrasi', $request['noreg']);
        }
        $data = $data->where('so.aktif', true);
        $data = $data->get();
        $status = '';

        $result = [];
        foreach ($data as $item) {
            $details = DB::select(DB::raw("
                    SELECT tp.norec,tp.produkidfk,tp.produkidfk AS produkfk,pr.namaproduk,tp.satuanviewidfk,ss.satuanstandar AS satuanview,
                           tp.jumlah,tp.hargasatuan,tp.keteranganpakai AS keterangan,ss.satuanstandar
                    FROM transaksipasientr AS tp 
                    INNER JOIN pelayananmt AS pr ON pr.id = tp.produkidfk
                    LEFT JOIN satuanstandarmt AS ss ON ss.id = tp.satuanviewidfk
                    WHERE tp.aktif = true AND tp.isalkes = true AND tp.transaksialkesfk = :noorder"),
                array(
                    'noorder' => $item->norec,
                )
            );
            $result[] = array(
                'tglinput' => $item->tglinput,
                'norec' => $item->norec,
                'noalkes' => $item->noalkes,
                'ruanganidfk' => $item->ruanganidfk,  
                'namaruangan' => $item->namaruangan,  
                'petugasidfk' => $item->petugasidfk,                            
                'namalengkap' => $item->namalengkap,                                              
                'details' => $details
            );
        }

        return $this->respond($result);

    }

    public function DeletePelayananAlkesBhp(Request $request) {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        //## StrukResep
        DB::beginTransaction();
        $r_SR=$request->all();
        try {
            $dataPasien = \DB::table('transaksialkestr as sr')
                ->JOIN('daftarpasienruangantr as apd', 'apd.norec', '=', 'sr.daftarpasienruanganfk')
                ->JOIN('registrasipasientr as pd', 'pd.norec', '=', 'apd.registrasipasienfk')
                ->JOIN('pasienmt as pm', 'pm.id', '=', 'pd.normidfk')
                ->select('pm.namapasien', 'sr.noalkes', 'pm.norm', 'pd.noregistrasi','sr.ruanganidfk','sr.tglinput')
                ->where('sr.koders', $idProfile)
                ->where('sr.norec', $r_SR['norec'])
                ->first();

            $dataKs = TransaksiKartuStok::where('keterangan',  'Input Alkse & BHP ' . ' ' .  $dataPasien->noalkes)
                ->where('koders', $idProfile)
                ->update([
                    'flagfk' => null
                ]);

            $newSR = TransaksiAlkes::where('norec',$r_SR['norec'])->where('koders', $idProfile)
                     ->update([
                         "aktif" => false,
                     ]);            

            $norec_SR = $r_SR['norec'];
            $tgl_SR = $dataPasien->tglinput;
            $idRuangan_SR = $dataPasien->ruanganidfk;

            //## PelayananPasien

            $newPP = TransaksiPasien::where('transaksialkesfk', $norec_SR)->where('koders', $idProfile)->get();
            $newPP2 = $newPP;
            foreach ($newPP as $r_PPL){
                $norec_PP = $r_PPL->norec;
                $newPPD = TransaksiPasienDetail::where('transaksipasienfk', $norec_PP)->where('koders', $idProfile)->delete();
                $qtyJumlah = (float)$r_PPL['jumlah'] * (float)$r_PPL['nilaikonversi'];
                //## StokProdukDetail
                $produk = $r_PPL['produkidfk'];
                $dataSaldoAwal = collect(DB::select("
                    select sum(qtyproduk) as qty from transaksistoktr 
                    where koders = $idProfile and ruanganidfk='$idRuangan_SR' and produkidfk=$produk")
                )->first();

                    $saldoAwal = (float)$dataSaldoAwal->qty;
                    $saldoAkhir = (float)$dataSaldoAwal->qty + $qtyJumlah;
                    $GetNorec = TransaksiStok::where('nostrukterimafk', $r_PPL['strukterimaidfk'])
                    ->where('koders', $idProfile)
                    ->where('ruanganidfk', $idRuangan_SR)
                    ->where('produkidfk', $produk)
                    ->orderBy('tglpelayanan', 'desc')
                    ->first();

                    $jmlPengurang =(float)$qtyJumlah;
                    DB::table('transaksistoktr')
                        ->where('koders', $idProfile)
                        ->where('norec', $GetNorec->norec)
                        ->sharedLock()
                        ->increment('qtyproduk', (float)$jmlPengurang);

                    //## KartuStok
                    $TRANS_KS = array(
                        "saldoawal" => $saldoAwal,
                        "qtyin" => (float)$jmlPengurang,
                        "qtyout" => 0,
                        "saldoakhir" => $saldoAkhir,
                        "keterangan" => 'Hapus Alkse & BHP ' . $dataPasien->noalkes,
                        "produkidfk" =>  $produk,
                        "ruanganidfk" => $idRuangan_SR,
                        "tglinput" => date('Y-m-d H:i:s'),
                        "tglkejadian" => date('Y-m-d H:i:s'),
                        "nostrukterimaidfk" => $r_PPL['strukterimaidfk'],
                        "norectransaksi" => $norec_PP,
                        "tabletransaksi" => 'transaksipasien',
                        // "transstokfk" => $r_PPL['transstokfk'],
                        "flagfk" => null,
                    );
                    $this->KartuStok($idProfile, $TRANS_KS);
            }            

            $newPP = TransaksiPasien::where('transaksialkesfk', $norec_SR)->where('koders', $idProfile)->delete();

            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
            $transError = $e->getMessage();
        }

        if ($transStatus == 'true') {
            $transMessage = "Hapus Input Alkes/Bhp Berhasil";
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "as" => 'erea',
            );
        } else {
            $transMessage = "Hapus Input Alkes/Bhp Gagal!!";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMessage,
                "e"  => $transError,
                "as" => 'erea',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
}
