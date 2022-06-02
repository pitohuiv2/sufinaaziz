<?php
/**
 * Created by PhpStorm.
 * HumasController
 * User: GodUsop
 * Date: 07/08/2019
 * Time: 11:44 PM
 */
namespace App\Http\Controllers\Humas;
// use App\Http\Controllers\Transaksi\Pegawai\Pegawai;
// use App\Master\JadwalDokter;
// use App\Master\JadwalPraktek;
// use App\Master\JadwalPraktekBulanan;
// use App\Master\KeluhanPelanggan;
use App\Traits\Valet;
// use App\Transaksi\LoggingUser;
// use App\Transaksi\PenangananKeluhanPelanggan;
// use App\Transaksi\PenangananKeluhanPelangganD;
// use App\Transaksi\PenungguPasien;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Response;
use App\Http\Controllers\ApiController;

class HumasC extends ApiController
{
    use Valet;

    public function __construct()
    {
        parent::__construct($skip_authentication = false);
    }
    public function getJadwalDokter (Request $request){
        $kdProfile = $this->getDataKdProfile($request);
        $idProfile = (int) $kdProfile;

        $data = \DB::table('jadwaldoktermt as jd')
            ->join('ruanganmt AS ru','ru.id','=','jd.ruanganidfk')
            ->join('pegawaimt as pg','pg.id','=','jd.pegawaiidfk')
            // ->leftJoin('hari_m AS hr','hr.id','=','jd.objecthariawal')
            // ->leftJoin('hari_m AS hr1','hr1.id','=','jd.objecthariakhir')
            ->select(DB::raw("jd.id as idjadwalpegawai,jd.ruanganidfk as objectruanganfk,ru.namaruangan,
                              jd.pegawaiidfk as objectpegawaifk,pg.namalengkap,pg.nosip,pg.nostr,pg.noidentitas as nik,
                              jd.jammulai,jd.jamakhir,jd.keterangan, jd.hari"))
            ->where('jd.koders', $idProfile)
            ->where('jd.aktif', true)
            ->where('pg.aktif', true)
            ->where('ru.aktif', true);

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
            'data'=>$data,
            'message' => 'godU',
        );
        return $this->respond($result);
    }
    public function getSurvey (Request $request){
        $kdProfile = $this->getDataKdProfile($request);
   

        $data = \DB::table('keluhanpelanggantr as jd')
            ->join('ruanganmt AS ru','ru.id','=','jd.ruanganidfk')
            ->select(DB::raw("jd.*,ru.namaruangan"))
            ->where('jd.koders', $kdProfile)
            ->where('jd.aktif', true);

        if (isset($request['dari']) && $request['dari'] != "" && $request['dari'] != "undefined") {
            $data = $data->where('jd.tglkeluhan', '>=', $request['dari'].' 00:00');
        }
        if (isset($request['sampai']) && $request['sampai'] != "" && $request['sampai'] != "undefined") {
            $data = $data->where('jd.tglkeluhan', '<=', $request['sampai'].' 23:59');
        }
        if (isset($request['nama']) && $request['nama'] != "" && $request['nama'] != "undefined") {
            $data = $data->where('jd.namapasien', 'ilike', '%'.$request['nama'].'%');
        }
        $data = $data->orderBy('jd.tglkeluhan', 'desc');
        $data = $data->get();
        $result = array(
            'data'=>$data,
            'message' => 'Xoxo',
        );
        return $this->respond($result);
    }
    
}
