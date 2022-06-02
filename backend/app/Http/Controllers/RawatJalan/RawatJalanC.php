<?php


namespace App\Http\Controllers\RawatJalan;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\Valet;
use App\Traits\PelayananPasienTrait;
use App\Transaksi\DaftarPasienRuangan;
use App\Transaksi\RegistrasiPasien;
use DB;
use Carbon\Carbon;

class RawatJalanC extends ApiController
{
    use Valet, PelayananPasienTrait;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }
    public function getDaftarRegistrasiDokterRajal(Request $request)
    {
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;
        $filter = $request->all();
        $deptJalan = explode(',', $this->settingDataFixed('kdDepartemenRawatJalanFix', $idProfile));
        $kdDepartemenRawatJalan = [];
        foreach ($deptJalan as $item) {
            $kdDepartemenRawatJalan[] = (int)$item;
        }
        $data = \DB::table('daftarpasienruangantr as apd')
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
            ->leftjoin('pemakaianasuransitr as pa', 'pa.registrasipasienfk', '=', 'pd.norec')
            ->leftjoin('asuransimt as asu', 'pa.asuransiidfk', '=', 'asu.id')
            ->leftjoin('kelasmt as klstg', 'klstg.id', '=', 'asu.kelasdijaminidfk')
            ->select(
                'pd.tglregistrasi',
                'ps.norm',
                'pd.normidfk',
                'pd.noregistrasi',
                'ps.namapasien',
                'ps.tgllahir',
                'jk.jeniskelamin',
                'apd.ruanganidfk',
                'ru.namaruangan',
                'kls.id as idkelas',
                'kls.namakelas',
                'pd.kelompokpasienlastidfk',
                'kp.kelompokpasien',
                'apd.pegawaiidfk',
                'pg.namalengkap as namadokter',
                'pd.norec as norec_pd',
                'apd.norec as norec_apd',
                'pd.asalrujukanidfk',                
                'apd.statuspasien as statuspanggil',
                'pd.statuspasien',
                'apd.tgldipanggildokter',
                'apd.tgldipanggilsuster',
                'apr.noreservasi',
                'apd.noantrian',
                'apr.tanggalreservasi',
                'alm.alamatlengkap',
                'klstg.namakelas as kelasdijamin',
                'apd.tglselesaiperiksa',
                'pd.ruanganlastidfk',
                'apd.norec as norec_apd',                
                DB::raw('case when apd.ispelayananpasien is null then \'false\' else \'true\' end as statuslayanan')
            )
            ->where('apd.aktif', true)
            ->where('apd.koders', $idProfile)
            ->where('pd.aktif', true)
            ->where('ps.aktif', true);

        if (isset($filter['tglAwal']) && $filter['tglAwal'] != "" && $filter['tglAwal'] != "undefined") {
            $data = $data->where('pd.tglregistrasi', '>=', $filter['tglAwal']);
        }
        if (isset($filter['tglAkhir']) && $filter['tglAkhir'] != "" && $filter['tglAkhir'] != "undefined") {
            $data = $data->where('pd.tglregistrasi', '<=', $filter['tglAkhir']);
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
            $data = $data->where('apd.norec',  $filter['norecApd']);
        }
        if (isset($request['ruanganArr']) && $request['ruanganArr'] != "" && $request['ruanganArr'] != "undefined") {
            $arrRuang = explode(',', $request['ruanganArr']);
            $kodeRuang = [];
            foreach ($arrRuang as $item) {
                $kodeRuang[] = (int) $item;
            }
            $data = $data->whereIn('ru.id', $kodeRuang);
        }
        if (isset($filter['jmlRow']) && $filter['jmlRow'] != "" && $filter['jmlRow'] != "undefined") {
            $data = $data->take($filter['jmlRow']);
        }
        $data = $data->whereIn('ru.instalasiidfk', $kdDepartemenRawatJalan);
        $data = $data->orderBy('apd.noantrian');
        $data = $data->get();
        $norecaPd = '';
        foreach ($data as $ob) {
            $norecaPd = $norecaPd . ",'" . $ob->norec_apd . "'";
            $ob->kddiagnosa = [];
        }
        $norecaPd = substr($norecaPd, 1, strlen($norecaPd) - 1);
        $diagnosa = [];
        if ($norecaPd != '') {
            $diagnosa = DB::select(DB::raw("
               select dg.kddiagnosa,ddp.daftarpasienruanganfk as norec_apd
               from diagnosapasienicdxtr as ddp
               left join icdxmt as dg on ddp.icdxidfk=dg.id
               where ddp.daftarpasienruanganfk in ($norecaPd) "));
            $i = 0;

            foreach ($data as $h) {
                $data[$i]->kddiagnosa = [];
                foreach ($diagnosa as $d) {
                    if ($data[$i]->norec_apd == $d->norec_apd) {
                        $data[$i]->kddiagnosa[] = $d->kddiagnosa;
                    }
                }
                $i++;
            }
        }

        return $this->respond($data);
    }

    public function updateStatusPanggil(Request $request){
        $kdProfile = (int) $this->getDataKdProfile($request);        
        $tglnow =  date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {

            if ($request['norec_apd']!=null) {
                $apd =  DaftarPasienRuangan::where('norec', $request['norec_apd'])->where('koders', $kdProfile)->first();
                $ddddd = DaftarPasienRuangan::where('norec', $request['norec_apd'])
                    ->where('koders', $kdProfile)
                    ->update([
                            // 'objectpegawaifk' => $request['iddokter']
                            'tgldipanggildokter' => $tglnow,
                            'tgldipanggilsuster' => $tglnow
                        ]

                    );

                // $pasienDaftar = RegistrasiPasien::where('norec',$apd->noregistrasifk)
                //     ->where('koders', $kdProfile)
                //     ->update([
                //     'objectpegawaifk' => $request['iddokter']
                // ]);
            }

            $transMessage = "";
            $transStatus = 'true';
        } catch (\Exception $e) {
            $transStatus = 'false';
        }

        if ($transStatus == 'true') {
            DB::commit();
            $result = array(
                "status" => 201,
                "message" => $transMessage,
                "struk" => $ddddd,
                "as" => 'XoXo',
            );
        } else {
            $transMessage = "";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "message" => $transMessage,
                "struk" => $ddddd,
                "as" => 'XoXo',
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }
}
