<?php


namespace App\Http\Controllers\BendaharaPenerimaan;


use App\Http\Controllers\ApiController;
use App\Master\JenisKelamin;
use App\Traits\InternalList;
use App\Traits\PelayananPasienTrait;
use App\Traits\SettingDataFixedTrait;
use App\Traits\Valet;
use App\Transaksi\StrukBuktiPenerimaan;
use App\Transaksi\StrukBuktiPenerimaanCaraBayar;
// use App\Transaksi\StrukClosing;
// use App\Transaksi\StrukClosingKasir;
use App\Transaksi\TransaksiClosing;
use App\Transaksi\TransaksiClosingKasir;
// use App\Transaksi\StrukHistori;
use App\Transaksi\TransaksiHistori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BendaharaPenerimaanC extends ApiController
{
    use Valet, PelayananPasienTrait, SettingDataFixedTrait,InternalList;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }

    public function getDataComboBP(Request $request)
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

        $kdKelUserKasir =  (int) $this->settingDataFixed('kdKelUserKasir', $kdProfile);
        $dataCaraBayar = \DB::table('carabayarmt as ru')
            ->select('ru.id','ru.carabayar')
            ->where('ru.aktif', true)
            ->orderBy('ru.id')
            ->get();

        $jenisTransKasir = explode(',', $this->settingDataFixed('KdJenisTransaksiKasir', $kdProfile));
        $listjenisTransKasir = [];
        foreach ($jenisTransKasir as $itemTransKasir) {
            $listjenisTransKasir [] = (int)$itemTransKasir;
        }
        $dataJenisTransaksi = \DB::table('jenistransaksimt as ru')
            ->select('ru.id','ru.kelompoktransaksi')
            ->where('ru.aktif', true)
            ->whereIn('ru.id', $listjenisTransKasir)
            ->orderBy('ru.id')
            ->get();

        $dataPetugasKasir = \DB::select(DB::raw("
                SELECT pg.id,pg.namalengkap
                FROM loginuser_s lu
                INNER JOIN pegawaimt pg ON lu.objectpegawaifk=pg.id
                WHERE lu.kdprofile = $kdProfile AND objectkelompokuserfk=:id AND pg.aktif=true"),
            array(
                'id' => $kdKelUserKasir,
            )
        );

        $dataStatusSetor = \DB::table('statussetormt as ru')
            ->select('ru.id','ru.statussetor')
            ->where('ru.aktif', true)
            ->orderBy('ru.id')
            ->get();

        $dataCaraSetor =  \DB::table('carasetormt as ru')
            ->select('ru.id','ru.carasetor')
            ->where('ru.aktif', true)
            ->orderBy('ru.id')
            ->get();

        $result = array(
            'departemen' => $dataDepartemen,
            'kelompokpasien' => $dataKelompok,
            'carabayar' => $dataCaraBayar,
            'jenistransaksi' => $dataJenisTransaksi,
            'petugaskasir' => $dataPetugasKasir,
            'statussetor' => $dataStatusSetor,
            'carasetor' => $dataCaraSetor,
            'message' => 'godU',
        );

        return $this->respond($result);
    }

    public function getDaftarSBM(Request $request){
        $idProfile = (int) $this->getDataKdProfile($request);
        $data = \DB::table('strukbuktipenerimaantr as sbm')
            ->join('strukpelayanantr as sp', 'sbm.nostrukidfk', '=', 'sp.norec')
            ->leftjoin('registrasipasientr as pd', 'sp.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('ruanganmt as ru', 'ru.id', '=', 'pd.ruanganlastidfk')
            ->leftjoin('pegawaimt as p', 'p.id', '=', 'sbm.pegawaipenerimaidfk')
            ->leftjoin('pasienmt as ps', 'ps.id', '=', 'sp.normidfk')
            ->leftjoin('strukbuktipenerimaancarabayartr as sbmcr', 'sbmcr.nosbmidfk', '=', 'sbm.norec')
            ->leftjoin('carabayarmt as cb', 'cb.id', '=', 'sbmcr.carabayaridfk')
            ->leftjoin('jenistransaksimt as kt', 'kt.id', '=', 'sbm.kelompokpasienidfk')
            ->leftjoin('transaksiclosingtr as sc', 'sc.norec', '=', 'sbm.noclosingidfk')
            ->leftjoin('transaksiverifikasitr as sv', 'sv.norec', '=', 'sbm.noverifikasiidfk')
            ->leftjoin('statussetormt as ss', 'ss.id', '=', 'sbm.statussetoridfk')
            ->select(DB::raw("
                sbm.norec,cb.carabayar,sbmcr.carabayaridfk,sbm.kelompokpasienidfk,kt.kelompoktransaksi,
			    sbm.keteranganlainnya AS keterangan,p.id,p.namalengkap,sc.noclosing,sbm.nosbm,sbm.pegawaipenerimaidfk,
			    p.namalengkap AS kasir,sv.noverifikasi,sc.tglclosing,sbm.tglsbm,sv.tglverifikasi,pd.noregistrasi,
			    ps.namapasien,sp.norec AS norec_sp,ru.id AS ruid,ru.namaruangan,sp.namapasien_klien,ps.norm AS nocm,
			    sbm.noclosingidfk,sbm.totaldibayar AS totalpenerimaan,sp.registrasipasienfk,sbm.statussetoridfk,ss.statussetor
            "))
            ->where('sbm.aktif',true)
            ->where('sbm.koders', $idProfile);

        $filter = $request->all();
        if(isset($filter['dateStartTglSbm']) && $filter['dateStartTglSbm'] != "" && $filter['dateStartTglSbm'] != "undefined") {
            $tgl2 = $filter['dateStartTglSbm'] ;//. " 00:00:00";
            $data = $data->where('sbm.tglsbm', '>=', $tgl2);
        }

        if(isset($filter['dateEndTglSbm']) && $filter['dateEndTglSbm'] != "" && $filter['dateEndTglSbm'] != "undefined") {
            $tgl = $filter['dateEndTglSbm'] ;//. " 23:59:59";
            $data = $data->where('sbm.tglsbm', '<=', $tgl);
        }

        if(isset($filter['idCaraBayar']) && $filter['idCaraBayar'] != "" && $filter['idCaraBayar'] != "undefined") {
            $data = $data->where('cb.id', '=', $filter['idCaraBayar']);
        }

        if(isset($filter['KetSetor']) && $filter['KetSetor'] != "" && $filter['KetSetor'] != "undefined") {
            $data = $data->where('sbm.statussetoridfk', '=', $filter['KetSetor']);
        }

        if(isset($request['KasirArr']) && $request['KasirArr']!="" && $request['KasirArr']!="undefined"){
            $arrRuang = explode(',',$request['KasirArr']) ;
            $kodeRuang = [];
            foreach ( $arrRuang as $item){
                $kodeRuang[] = (int) $item;
            }
            $data = $data->whereIn('p.id',$kodeRuang);
        }

        $data = $data->get();

        $result=[];
        foreach ($data as $item) {
            $noclosingfk = $item->noclosingidfk;
            $caraBayar = $item->carabayar;
            $details= \DB::select(DB::raw("
                    select distinct sck.noclosingidfk,cb.id,cb.carabayar as caraBayar,sh.noclosing,sck.totaldibayar as jumlah,
                    sc.totaldibayar as total,sh.pegawaiterimaidfk, pg.namalengkap as pegawaipenerima
                    from transaksiclosingkasirtr as sck
                    left join transaksiclosingtr as sc on sc.norec = sck.noclosingidfk
                    left join carabayarmt as cb on cb.id = sck.carabayaridfk
                    left join transaksihistoritr as sh on sh.noclosing = sc.noclosing
                    LEFT JOIN carasetormt as cs on cs.id = sck.carasetoridfk
                    left join pegawaimt as pg on pg.id = sh.pegawaiterimaidfk
                    where sck.koders = 1 
                    AND sck.noclosingidfk ='$noclosingfk'                  
                    GROUP BY cs.carasetor,sck.carasetoridfk,cb.id,
                    sck.totaldibayar, sc.totaldibayar,sck.noclosingidfk, sh.noclosing,
                    sh.pegawaiterimaidfk, pg.namalengkap,sck.totaldibayar,cb.carabayar
            "));

            $result[] = array(
                'noRec' => $item->norec,
                'caraBayar' => $item->carabayar,
                'idCaraBayar' => $item->carabayaridfk,
                'idKelTransaksi' => $item->kelompokpasienidfk,
                'kelTransaksi' => $item->kelompoktransaksi,
                'keterangan' => $item->keterangan,
                'idPegawai' => $item->pegawaipenerimaidfk,
                'namaPenerima' => $item->kasir,
                'noClosing' => $item->noclosing,
                'noSbm' => $item->nosbm,
                'tglSbm' =>$item->tglsbm,
                'noVerifikasi' => $item->noverifikasi,
                'tglClosing' => $item->tglclosing,
                'norec_sp' => $item->norec_sp,
                'totalPenerimaan' => $item->totalpenerimaan,
                'namapasien' => $item->namapasien,
                'ruid' => $item->ruid,
                'namaruangan' => $item->namaruangan,
                'namapasien_klien' => $item->namapasien_klien,
                'nocm' => $item->nocm,
                'status' => $item->statussetor,
                'details' => $details,
            );
        }

        $result = array(
            'data' => $result,
            'message' => 'godU',
        );
        return $this->respond($result);
    }

    public function simpanSetoranKasir(Request $request){
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdKelTrans = (int) $this->settingDataFixed('KdTransSetoranKasir',$idProfile);
        $idRuangan = (int) $this->settingDataFixed('KdRuanganBendaharaPenerimaan',$idProfile);
        $KdStatusSudahSetor = (int) $this->settingDataFixed('KdStatusSudahSetor',$idProfile);
        $transMsg = null;
        \DB::beginTransaction();
        try{
            $input  = $request->all();
            $dataPegawaiUser = \DB::select(DB::raw("select pg.id,pg.namalengkap 
                    from loginuser_s as lu
                    INNER JOIN pegawaimt as pg on lu.objectpegawaifk=pg.id
                    where lu.id=:idLoginUser limit 1"),
                array(
                    'idLoginUser' => $input['kdPegawai'],
                )
            );
            $UserId = 0;
            foreach ($dataPegawaiUser as $items){
                $UserId = $items->id;
            }
            $pegawai = $input['kdPegawai'];//Pegawai::find($input['kdPegawai']);
            $tglAwal=$input['tglAwal'];
            $tglAkhir=$input['tglAkhir'];

            $SC = new TransaksiClosing();
            $SC->norec  = $SC->generateNewId();
            $SC->koders = $idProfile;
            $SC->aktif = true;
            $SC->noclosing = $this->generateCode(new TransaksiClosing, 'noclosing', 10, 'C-'.$this->getDateTime()->format('ym'),$idProfile);
            $SC->pegawaidicloseidfk = $input['kdPegawai'];
            $SC->tglclosing = $input['tglsetor'];
            $SC->totaldibayar = $input['totalSetoran'];
            $SC->kelompoktransaksiidfk = $kdKelTrans;
            $SC->keteranganlainnya = "Setoran Kasir";
            $SC->tglawal = $tglAwal;
            $SC->tglakhir = $tglAkhir;
            $SC->tglclosing = $this->getDateTime()->format('Y-m-d H:i:s');
            $SC->save();
            $NorecSc = $SC->norec;

            $SH = new TransaksiHistori();
            $SH->norec  = $SH->generateNewId();
            $SH->nonhistori = $this->generateCode(new TransaksiHistori(), 'nonhistori', 14, 'SK-'.$this->getDateTime()->format('ym'),$idProfile);
            $SH->koders = $idProfile;
            $SH->aktif= true;
            $SH->totalsetortarikdeposit =$input['totalSetoran'];
            $SH->tglsetortarikdeposit =  $input['tglsetor'];;//$this->getDateTime();
            $SH->pegawaitarikdepositidfk = $input['kdPegawai'];
            $SH->pegawaiterimaidfk = $this->getCurrentUserID();
            $SH->kelompoktransaksiidfk = $kdKelTrans;
            $SH->noclosing = $NorecSc;
            // $SH->objectcarasetorfk = $item['idCaraSetor'];
            $SH->ketlainya ='Setoran Kasir';
            if(isset($idRuangan)){
                $SH->ruanganterimaidfk = $idRuangan;
                $SH->ruanganidfk = $idRuangan;
            }

            $SH->nobukti = '-';
            $SH->kdperkiraan = '-';//$input['kdperkiraan'];
            $SH->namaperkiraan = 'Penerimaan Kasir ';//$input['keterangan'];
            $SH->kettransaksi = '-';// $input['keterangantransaksi'];
            $SH->save();

            /** @Save_StrukBuktiPenerimaan */
            $strukBuktiPenerimanan = new StrukBuktiPenerimaan();
            $strukBuktiPenerimanan->norec = $strukBuktiPenerimanan->generateNewId();
            $strukBuktiPenerimanan->koders = $idProfile;
            $strukBuktiPenerimanan->aktif = true;
            $strukBuktiPenerimanan->keteranganlainnya = 'Setoran Kasir';//nama perkiraan
            $strukBuktiPenerimanan->pegawaipenerimaidfk = $input['kdPegawai'];
            $strukBuktiPenerimanan->tglsbm =  $input['tglsetor'];;//date('Y-m-d H:i:s');
            $strukBuktiPenerimanan->totaldibayar =  $input['totalSetoran'];//$input['totalSetoran'];
            $strukBuktiPenerimanan->kelompoktransaksiidfk = $kdKelTrans;
            $strukBuktiPenerimanan->nosbm = $this->generateCode(new StrukBuktiPenerimaan, 'nosbm', 14, 'INV-' . $this->getDateTime()->format('ym'),$idProfile);
            $strukBuktiPenerimanan->noclosingidfk = $NorecSc;
            $strukBuktiPenerimanan->save();
            /** @End_Save_StrukBuktiPenerimaan */

            foreach ($input['detailSetoran'] as $item){
                $SCK = new TransaksiClosingKasir();
                $SCK->koders = $idProfile;
                $SCK->aktif= true;
                $SCK->norec  = $SCK->generateNewId();
                $SCK->noclosingidfk = $NorecSc; //$SC->norec;
                $SCK->totaldibayar = $item['totalPenerimaan'];
                $SCK->totaldibayarcashin = 0;
                $SCK->totaldibayarcashout = 0;
                $SCK->totaldibayarclose = 0;
                $SCK->carabayaridfk = $item['kdCaraBayar'];
                $SCK->qtystrukbuktiextclose = 0;
                $SCK->qtystrukbuktiintclose = 0;
                $SCK->carasetoridfk = $item['idCaraSetor'];
                $SCK->save();

                /** @Save_StrukBuktiPenerimaanCaraBayar */
                $SBPCB = new StrukBuktiPenerimaanCaraBayar();
                $SBPCB->norec = $SBPCB->generateNewId();
                $SBPCB->koders = $idProfile;
                $SBPCB->aktif = 1;
                $SBPCB->nosbmidfk = $strukBuktiPenerimanan->norec;
                $SBPCB->carabayaridfk = $item['kdCaraBayar'];
                if (isset($input['detailBank'])) {
                    $SBPCB->bankaccountidfk = $input['detailBank']['id'];
                    $SBPCB->namabankprovider = $input['detailBank']['namaBank'];
                    $SBPCB->namapemilik = $input['detailBank']['namaKartu'];
                }
                $SBPCB->save();
                /** @End_Save_SStrukBuktiPenerimaanCaraBayar */
            }

            foreach ($input['detailSBM'] as $item2){
                $updateSBM = StrukBuktiPenerimaan::where('norec', $item2['norec_sbm'])
                ->where('koders',$idProfile)
                ->whereNull('noclosingidfk')
                ->update([
                        'noclosingidfk' => $NorecSc,
                        'statussetoridfk' => $KdStatusSudahSetor,
                ]);
            }

            $transStatus = true;
        } catch(\Exception $e){
            $transStatus= false;
        }

        $transMsg = "";
        if ($transStatus == true) {
            $transMsg = 'Simpan Setoran Kasir Berhasil';
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMsg,
                "data" => $SC,
                "as" => 'slvR',
            );
        } else {
            $transMsg = 'Simpan Setoran Kasir Gagal!!';
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMsg,
                "data" => $SC,
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMsg);
    }

    public function getDaftarSetoranKasir(Request $request){
        $idProfile = (int) $this->getDataKdProfile($request);
        $kdKelTrans = (int) $this->settingDataFixed('KdTransSetoranKasir',$idProfile);
        $data = \DB::table('transaksiclosingtr as sc')
            ->JOIN('transaksiclosingkasirtr AS sck','sck.noclosingidfk','=','sc.norec')
            ->LEFTJOIN('carasetormt AS cs','cs.id','=','sck.carasetoridfk')
            ->LEFTJOIN('carabayarmt AS cb','cb.id','=','sck.carabayaridfk')
            ->LEFTJOIN('pegawaimt AS pg','pg.id','=','sc.pegawaidicloseidfk')
            ->select(DB::raw("
                 sc.norec,sc.noclosing,sc.tglclosing,sc.totaldibayar AS totalsetor,
                 sck.carabayaridfk,cb.carabayar,sck.carasetoridfk,cs.carasetor,sc.pegawaidicloseidfk,
                 pg.namalengkap
            "))
            ->where('sc.koders', $idProfile)
            ->where('sc.aktif',true)
            ->where('sc.kelompoktransaksiidfk',$kdKelTrans);

        if(isset($request['tglAwal']) && $request['tglAwal']!="" && $request['tglAwal']!="undefined"){
            $data = $data->where('sc.tglclosing','>=', $request['tglAwal']);
        }

        if(isset($request['tglAkhir']) && $request['tglAkhir']!="" && $request['tglAkhir']!="undefined"){
            $data = $data->where('sc.tglclosing','<=', $request['tglAkhir']);
        }

        if(isset($request['idCaraBayar']) && $request['idCaraBayar'] != "" && $request['idCaraBayar'] != "undefined") {
            $data = $data->where('sck.carabayaridfk', '=', $request['idCaraBayar']);
        }

        if(isset($request['idCaraSetor']) && $request['idCaraSetor'] != "" && $request['idCaraSetor'] != "undefined") {
            $data = $data->where('sck.carasetoridfk', '=', $request['idCaraSetor']);
        }

        if(isset($request['KasirArr']) && $request['KasirArr']!="" && $request['KasirArr']!="undefined"){
            $arrRuang = explode(',',$request['KasirArr']) ;
            $kodeRuang = [];
            foreach ( $arrRuang as $item){
                $kodeRuang[] = (int) $item;
            }
            $data = $data->whereIn('p.id',$kodeRuang);
        }
        $data = $data->get();

        $results =array();
        foreach ($data as $item){
            $details = DB::select(DB::raw("
                    SELECT sbm.norec,cb.carabayar,sbmcr.carabayaridfk,sbm.kelompokpasienidfk,kt.kelompoktransaksi,
                           sbm.keteranganlainnya AS keterangan,pg.id,pg.namalengkap,sc.noclosing,sbm.nosbm,
                           sbm.pegawaipenerimaidfk,pg.namalengkap AS kasir,sv.noverifikasi,sc.tglclosing,
                           sbm.tglsbm,sv.tglverifikasi,pd.noregistrasi,ps.namapasien,sp.norec AS norec_sp,
                           ru.id AS ruid,ru.namaruangan,sp.namapasien_klien,ps.norm AS nocm,sbm.noclosingidfk,
                           sbm.totaldibayar AS totalpenerimaan,sp.registrasipasienfk,sbm.statussetoridfk,
                           ss.statussetor
                    FROM strukbuktipenerimaantr AS sbm
                    INNER JOIN strukpelayanantr AS sp ON sbm.nostrukidfk = sp.norec
                    LEFT JOIN registrasipasientr AS pd ON sp.registrasipasienfk = pd.norec
                    LEFT JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk
                    LEFT JOIN pegawaimt AS pg ON pg.id = sbm.pegawaipenerimaidfk
                    LEFT JOIN pasienmt AS ps ON ps.id = sp.normidfk
                    LEFT JOIN strukbuktipenerimaancarabayartr AS sbmcr ON sbmcr.nosbmidfk = sbm.norec
                    LEFT JOIN carabayarmt AS cb ON cb.id = sbmcr.carabayaridfk
                    LEFT JOIN jenistransaksimt AS kt ON kt.id = sbm.kelompokpasienidfk
                    LEFT JOIN transaksiclosingtr AS sc ON sc.norec = sbm.noclosingidfk
                    LEFT JOIN transaksiverifikasitr AS sv ON sv.norec = sbm.noverifikasiidfk
                    LEFT JOIN statussetormt AS ss ON ss.id = sbm.statussetoridfk
                    WHERE sbm.aktif = true AND sbm.koders = 1 AND 
                    sbm.noclosingidfk = :norec;
            "),
                array(
                    'norec' => $item->norec,
                )
            );

            $results[] = array(
                'norec' => $item->norec,
                'noclosing' => $item->noclosing,
                'tglclosing' => $item->tglclosing,
                'totalsetor' => $item->totalsetor,
                'carabayaridfk' => $item->carabayaridfk,
                'carabayar' => $item->carabayar,
                'carasetoridfk' => $item->carasetoridfk,
                'carasetor' => $item->carasetor,
                'pegawaidicloseidfk' => $item->pegawaidicloseidfk,
                'petugas' => $item->namalengkap,
                'details' => $details,
            );
        }
        $result = array(
            'data' => $results,
            'message' => 'godU',
        );
        return $this->respond($result);
    }

    public function batalSetoranKasir(Request $request){
        $idProfile = (int) $this->getDataKdProfile($request);
        $transStatus = true;
        \DB::beginTransaction();
        $input  = $request->all();
        try {
            foreach ($input['details'] as $item){
                $sc = TransaksiClosing::where('noclosing',$item['noclosing'])->where('koders', $idProfile)->first();
                $sh = TransaksiClosing::where('noclosing', $sc->norec)
                    ->where('koders', $idProfile)
                    ->update([
                        'aktif' => false
                    ]);
                $sh = TransaksiHistori::where('noclosing', $sc->norec)
                    ->where('koders', $idProfile)
                    ->update([
                        'aktif' => false
                    ]);
                $sbm = StrukBuktiPenerimaan::where('norec', $item['norec_sbm'])
                    ->where('koders', $idProfile)
                    ->whereNotNull('noclosingidfk')
                    ->update(
                        [
                            'noclosingidfk' => null
                        ]);

            }
        } catch (\Exception $e) {
            $transStatus = false;
        }
        if ($transStatus == true) {
            $transMsg = 'Batal Setor Kasir Berhasil!';
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMsg,
                "data" => $sh,
                "as" => 'slvR',
            );
        } else {
            $transMsg = 'Batal Setor Kasir Gagal!';
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message"  => $transMsg,
                "as" => 'slvR',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMsg);
    }

}
