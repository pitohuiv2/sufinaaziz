<?php

/**
 * Created by PhpStorm.
 * User: Egie Ramdan
 * Date: 7/31/2019
 * Time: 4:33 PM
 */

namespace App\Http\Controllers\Auth;

use App\Datatrans\LoggingUser;
use App\Datatrans\StrukOrder;
use App\Http\Controllers\ApiController;

use App\User;
use App\Web\Profile;
use App\Traits\CrudMaster;
use App\Traits\Valet;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Web\LoginUser;
use App\Web\Token;

use DB;
use Illuminate\Support\Facades\Hash;
use Namshi\JOSE\Base64\Base64UrlSafeEncoder;
use Namshi\JOSE\JWT;
use Namshi\JOSE\JWS;
use Namshi\JOSE\Base64\Encoder;
use Webpatser\Uuid\Uuid;

use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Hmac\Sha384;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use App\Datatrans\PasienDaftar;
use App\Datatrans\AntrianPasienDiperiksa;
use App\Datatrans\TempatTidur;
use App\Datatrans\RegistrasiPelayananPasien;

class AuthController extends ApiController
{
    use CrudMaster, Valet;
    protected $kdProfile = 18;
    protected $formAwal = "dashboard-pelayanan";
    public function __construct()
    {
        parent::__construct($skip_authentication = true);
    }


   
    public function createToken2($namaUser)
    {
        $class = new Builder();
        $signer = new Sha512();
        $token = $class->setHeader('alg', 'HS512')
            ->set('sub', $namaUser)
            ->sign($signer, "RAMDANEGIE")
            ->getToken();
        return $token;
    }
    public function getSignature(Request $request)
    {
        /*
         * composer update --no-plugins --no-scripts
         * composer require lcobucci/jwt
         * sumber -> https://github.com/lcobucci/jwt
         */

        $login = DB::table('user_m')
            ->where('password', '=', $this->encryptSHA1($request->input('password')))
            ->where('username', '=', $request->input('username'));
        $LoginUser = $login->first();
        if (!empty($LoginUser)) {

            $token['X-ID'] = $LoginUser->id;
            $token['X-USERNAME'] = $LoginUser->username;
            $token['X-AUTH-TOKEN'] = $this->createToken($LoginUser->username) . '';

            return $this->setStatusCode(200)->respond($token);

            //endregion
        } else {

            return $this->setStatusCode(500)->respond('Login gagal, Username atau Password salah');
        }
    }
    public function signOuts(Request $request)
    {
        $result['code'] = 401;
        $result['message'] = 'You have not logged';
        $QueryLogin = DB::table('loginuser_s')
            //            ->where('katasandi', '=', $this->encryptSHA1($request->input('kataSandi')))
            ->where('id', '=', $request->input('kdUser'));
        $LoginUser = $QueryLogin->get();

        if (count($LoginUser) > 0) {
            $result['message'] = 'Logout Success';
            $result['id'] = $LoginUser[0]->id;
            $result['kdUser'] = $LoginUser[0]->namauser;
            $result['code'] = 200;
            $result['as'] = '#Inhuman';
        }
        $resData = array(
            'data' => $result
        );
        return response()->json($result);
    }

    protected function encryptSHA1($pass)
    {
        return sha1($pass);
    }
    protected function encryptHash($pass)
    {
        return  Hash::make($pass);
    }


    public function signIn(Request $request)
    {
        /*
         * composer update --no-plugins --no-scripts
         * composer require lcobucci/jwt
         * sumber -> https://github.com/lcobucci/jwt
         */

        $makeHash = Hash::make($request->input('kataSandi'));
        // if (Hash::check('admin',  $makeHash ))
        // return $makeHash;
        $login = DB::table('sys_users')
            // ->where('password', '=',  $makeHash  ) //$this->encryptSHA1($request->input('kataSandi'))
            ->where('username', '=', $request->input('namaUser'))
            ->where('active', '=', 1);
        $LoginUser = $login->first();
        if (!empty($LoginUser) && Hash::check($request->input('kataSandi'), $LoginUser->password)) {


            $dataLogin = array(
                'username' => $LoginUser->username,
                'fullname' => $LoginUser->fullname,
                'level' =>  $LoginUser->level,
                'foto' =>  $LoginUser->foto,
                'jabatan' =>  $LoginUser->jabatan,

            );
            $token['X-AUTH-TOKEN'] = $this->createToken($LoginUser->username) . '';

            $result = array(
                'data' => $dataLogin,
                'messages' => $token,
                'status' => 200,
                'as' => 'ramdanegie'
            );

            //endregion
        } else {
            //region Login Gagal send 400 code
            $result = array(
                'data' => [],
                'messages' => 'Login gagal, Username atau Password salah',
                'status' => 500,
                'as' => 'ramdanegie'
            );
            //endregion
        }

        return $this->setStatusCode($result['status'])->respond($result);
    }

    public function show()
    {
        if (isset($_SESSION["tokenLogin"])) {
            return redirect("admin/".$this->formAwal);
        }
        return view("auth.login");
    }
    public function showIndex(Request $r)
    {
        $kdProfile = $_SESSION["kdProfile"];
        $set = collect(DB::select("select nilaifield from settingdatafixed_m where kdprofile=$kdProfile and namafield='kdDepartemenRanapFix'"))->first();
        $deptRanap = explode(',', $set->nilaifield);
        $kdDepartemenRawatInap = [];
        foreach ($deptRanap as $itemRanap) {
            $kdDepartemenRawatInap[] =  (int)$itemRanap;
        }

        $departemen = \DB::table('departemen_m as dp')
            ->select('dp.id', 'dp.kdprofile', 'dp.namadepartemen')
            ->where('dp.statusenabled', true)
            ->whereIn('dp.id', $kdDepartemenRawatInap)
            ->whereIn('dp.kdprofile', [$kdProfile, 0])
            ->orderBy('dp.namadepartemen')
            ->get();
        // dd($departemen);

        $objectruanganfk =  null;
        $objectdepartemenfk =  82;

        $ruangan = DB::table('ruangan_m')
            ->select('id', 'namaruangan')
            ->where('kdprofile', $kdProfile)
            ->where('statusenabled', true)
            ->where('objectdepartemenfk', $objectdepartemenfk)
            ->orderBy('namaruangan')
            ->get();
        if (isset($r->objectdepartemenfk)) {
            $ruangan = DB::table('ruangan_m')
                ->select('id', 'namaruangan')
                ->where('kdprofile', $kdProfile)
                ->where('statusenabled', true)
                ->where('objectdepartemenfk', $r->objectdepartemenfk)
                ->orderBy('namaruangan')
                ->get();
        }
        $stts = DB::table('statusbed_m')
            ->select('id', 'statusbed')
            //            ->where('kdprofile',$kdProfile)
            ->where('statusenabled', true)
            //            ->where('objectdepartemenfk',16)
            ->orderBy('statusbed')
            ->get();

        $namaruang = '-';
        $namakamar = '';
        $namapasien = '';
        $statusbedfk = '';
        $objectruanganfk = $ruangan[0]->id;

        if (
            !isset($r->objectruanganfk) && !isset($r->namakamar) && !isset($r->namapasien) && !isset($r->statusbedfk)
            &&  !isset($r->objectdepartemenfk)
        ) {
            $ruangans = DB::table('ruangan_m')->where('id', $objectruanganfk)
                ->where('objectdepartemenfk', $objectdepartemenfk)->first();
            $namaruang  = $ruangans->namaruangan;
            return redirect()->route("home", [
                "objectruanganfk" => $objectruanganfk,
                'namaruang' => $namaruang,
                'namakamar' => $namakamar,
                'namapasien' => $namapasien,
                'statusbedfk' => $statusbedfk,
                'objectdepartemenfk' => $objectdepartemenfk
            ]);
        } else {
            $objectruanganfk = $r->objectruanganfk;
            if ($objectruanganfk != '') {
                $ruangans = DB::table('ruangan_m')->where('id', $objectruanganfk)
                    ->where('objectdepartemenfk', $r->objectdepartemenfk)
                    ->first();
                $namaruang  = $ruangans->namaruangan;
            } else {
                $namaruang = '';
            }

            $namakamar =  $r->namakamar;
            $namapasien =  $r->namapasien;
            $statusbedfk =  $r->statusbedfk;
            $objectdepartemenfk =  $r->objectdepartemenfk;
        }
        //        dd($namakamar);
        $param = '';
        if ($namakamar != '') {
            $param = " and kmr.namakamar ilike '%" . $namakamar . "%'";
        }
        $param2 = '';
        if ($namapasien != '') {
            $param2 = " and ps.namapasien ilike '%" . $namapasien . "%'";
        }
        $paramStatus = '';
        if ($statusbedfk != '') {
            $paramStatus = " and sb.id = " . $statusbedfk;
        }
        //        return $this->getAllBed();
        $paramruangs = '';
        if ($objectruanganfk != '') {
            $paramruangs = " and ru.id = $objectruanganfk";
        }
        $data = collect(DB::select("

                SELECT distinct
                tt.id as tt_id,
                tt.nomorbed as namabed,
                kmr.id as kmr_id,
                kmr.namakamar,
                ru.id AS id_ruangan,
                ru.namaruangan,
                sb.statusbed,ps.nocm,ps.objectjeniskelaminfk as jkid,
                ps.tgllahir,ps.namapasien,pd.tglregistrasi,
                 EXTRACT(year FROM age(current_date,ps.tgllahir)) :: int as umur,
                 sb.color,sb.txtcolor,
                 	DATE_PART('day',now()-  pd.tglregistrasi ) as lamarawat,
                 	pd.noregistrasi,ps.nohp
                FROM
                tempattidur_m AS tt
                inner JOIN statusbed_m AS sb ON sb.id = tt.objectstatusbedfk
                inner JOIN kamar_m AS kmr ON kmr.id = tt.objectkamarfk
                inner JOIN ruangan_m AS ru ON ru.id = kmr.objectruanganfk
                LEFT JOIN antrianpasiendiperiksa_t as rpp on rpp.nobed=tt.id
                    and kmr.id= rpp.objectkamarfk
                    and rpp.statusenabled =true
                    and rpp.tglkeluar is null
                LEFT JOIN pasiendaftar_t AS pd ON pd.norec=rpp.noregistrasifk
                    and pd.tglpulang is  null
                    and rpp.objectruanganfk=pd.objectruanganlastfk
                    and pd.statusenabled=true
                LEFT JOIN pasien_m AS ps ON ps.id = pd.nocmfk

                WHERE tt.kdprofile = $kdProfile and
                tt.statusenabled = true and
                kmr.statusenabled = true
                $param
                $param2
                $paramStatus
               $paramruangs
                order by tt.nomorbed"));
        // $data = collect(DB::select("

        //      SELECT
        //         tt. ID AS tt_id,
        //         tt.nomorbed AS namabed,
        //         kmr. ID AS kmr_id,
        //         kmr.namakamar,
        //         ru. ID AS id_ruangan,
        //         ru.namaruangan,
        //         sb.statusbed,
        //         pd.nocm,
        //         pd.jkid,
        //         pd.tgllahir,
        //         pd.namapasien,
        //         pd.tglregistrasi,
        //         pd.umur,
        //         sb.color,
        //         sb.txtcolor,
        //     pd.lamarawat,
        //         pd.noregistrasi,
        //         pd.nohp
        //     FROM
        //         tempattidur_m AS tt
        //     INNER JOIN statusbed_m AS sb ON sb. ID = tt.objectstatusbedfk
        //     INNER JOIN kamar_m AS kmr ON kmr. ID = tt.objectkamarfk
        //     INNER JOIN ruangan_m AS ru ON ru. ID = kmr.objectruanganfk
        //     left join (select * from (
        //     select   row_number() over (partition by pd.noregistrasi order by apd.tglmasuk desc) as rownum ,ps.nocm,
        //         ps.objectjeniskelaminfk AS jkid,
        //         ps.tgllahir,
        //         ps.namapasien,
        //         pd.tglregistrasi,
        //         EXTRACT (
        //             YEAR
        //             FROM
        //                 age(CURRENT_DATE, ps.tgllahir)
        //         ) :: INT AS umur,   DATE_PART(
        //             'day',
        //             now() - pd.tglregistrasi
        //         ) AS lamarawat,
        //         pd.noregistrasi,
        //         ps.nohp,apd.objectruanganfk,apd.objectkamarfk,apd.nobed
        //      from pasiendaftar_t as pd
        //     join antrianpasiendiperiksa_t as apd on pd.norec =apd.noregistrasifk
        //     and apd.objectruanganfk=pd.objectruanganlastfk
        //     join pasien_m as ps on ps.id=pd.nocmfk
        //     where pd.tglpulang is null
        //     and pd.statusenabled=TRUE
        //     and pd.kdprofile=$kdProfile
        //     $param2
        //     )as x where x.rownum=1
        //     ) as pd on pd.objectruanganfk=ru.id
        //     and pd.objectkamarfk = kmr.id
        //     and pd.nobed=tt.id
        //     WHERE
        //         tt.kdprofile = $kdProfile
        //     AND tt.statusenabled = TRUE
        //     AND kmr.statusenabled = TRUE

        //       $param

        //          $paramStatus
        //         $paramruangs

        //     ORDER BY
        //         tt.nomorbed;


        //     "));

        //         foreach ($data as $key => $row) {
        //             $count[$key] = $row->namakamar;
        //         }
        //         array_multisort($count, SORT_ASC, $data);
        //        $dataNa =$data->sortBy('namakamar')->groupBy('namakamar');
        //        dd($data);

        $sbStatus2 = DB::select(DB::raw("
         SELECT
                COUNT (tts.objectstatusbedfk) AS jml,
                sb.statusbed,
                sb.color,
                sb.txtcolor
            FROM
                statusbed_m AS sb
            LEFT JOIN (
                SELECT
                    tt.id,
                    tt.objectstatusbedfk
                FROM
                    tempattidur_m AS tt
                INNER JOIN kamar_m AS kmr ON kmr.id = tt.objectkamarfk
                INNER JOIN ruangan_m AS ru ON ru.id = kmr.objectruanganfk
                WHERE
                    tt.kdprofile = $kdProfile
                AND tt.statusenabled = TRUE
                AND kmr.statusenabled = TRUE
               
                --and ru.id = $objectruanganfk
                $paramruangs
            
            ) AS tts ON (
                sb.id = tts.objectstatusbedfk
            )
            where sb.statusenabled =true
            GROUP BY
                sb.statusbed,
                sb.color,
                sb.txtcolor,
                sb.nourut
            ORDER BY
                sb.nourut;
                        "));
        //        dd($statusb);

        foreach ($data as $d) {
            $d->umur_string  = null;
            if ($d->tgllahir != null) {
                $d->umur_string = $this->getAge($d->tgllahir, date('Y-m-d'));
            }
        }
        $totalz = 0;
        $sbStatus = [];
        foreach ($sbStatus2 as $k) {
            $totalz = $totalz + (float) $k->jml;
            $sbStatus[] = array(
                "jml" =>  (float) $k->jml,
                "statusbed" =>   $k->statusbed,
                "color" =>   $k->color,
                "txtcolor" =>   $k->txtcolor,
            );
        }
        $sbStatus[] = array(
            "jml" =>  $totalz,
            "statusbed" => 'TOTAL',
            "color" =>  'bg-c-maroon',
            "txtcolor" =>   'text-white',
        );


        //        dd($stts);

        return view("module.view-bed.index", compact('departemen', 'ruangan', 'data', 'namaruang', 'statusbed', 'sbStatus', 'stts'));
    }
    public static function getAllBed(Request $r)
    {
        $parDep = '';
        if (isset($r['objectdepartemenfk'])) {
            $parDep = " and ru.objectdepartemenfk = $r[objectdepartemenfk]";
        }

        $kdProfile = $_SESSION['kdProfile'];
        $data = DB::select(DB::raw("  SELECT
sb.id,
                COUNT (tts.objectstatusbedfk) AS jml,
                sb.statusbed,
                sb.color,
                sb.txtcolor
            FROM
                statusbed_m AS sb
            LEFT JOIN (
                SELECT
                    tt.id,
                    tt.objectstatusbedfk
                FROM
                    tempattidur_m AS tt
                INNER JOIN kamar_m AS kmr ON kmr.id = tt.objectkamarfk
                INNER JOIN ruangan_m AS ru ON ru.id = kmr.objectruanganfk
                WHERE
                    tt.kdprofile = $kdProfile
                AND tt.statusenabled = TRUE
                AND kmr.statusenabled = TRUE
                $parDep

            ) AS tts ON (
                sb.id = tts.objectstatusbedfk
            )
            where sb.statusenabled =true
            GROUP BY
                sb.statusbed,
                sb.color,
                sb.txtcolor,
                sb.id,
                sb.nourut
        "));
        $kamar = collect(DB::select("select *  from kamar_m where kdprofile=$kdProfile and statusenabled =true"))->count();
        $kamarRusak = collect(DB::select("  SELECT kmr.namakamar
                FROM
                    tempattidur_m AS tt
                INNER JOIN kamar_m AS kmr ON kmr.id = tt.objectkamarfk
                INNER JOIN ruangan_m AS ru ON ru.id = kmr.objectruanganfk
                WHERE
                    tt.kdprofile = $kdProfile
                AND tt.statusenabled = TRUE
                AND kmr.statusenabled = TRUE
                and tt.objectstatusbedfk=5
                $parDep
                GROUP BY kmr.namakamar"))->count();
        $kamarPerawat = collect(DB::select("  SELECT kmr.namakamar
                FROM
                    tempattidur_m AS tt
                INNER JOIN kamar_m AS kmr ON kmr.id = tt.objectkamarfk
                INNER JOIN ruangan_m AS ru ON ru.id = kmr.objectruanganfk
                WHERE
                    tt.kdprofile = $kdProfile
                AND tt.statusenabled = TRUE
                AND kmr.statusenabled = TRUE
                and tt.objectstatusbedfk=10
                $parDep
                GROUP BY kmr.namakamar"))->count();
        $kamarIsi = collect(DB::select("  SELECT kmr.namakamar
                FROM
                    tempattidur_m AS tt
                INNER JOIN kamar_m AS kmr ON kmr.id = tt.objectkamarfk
                INNER JOIN ruangan_m AS ru ON ru.id = kmr.objectruanganfk
                WHERE
                    tt.kdprofile = $kdProfile
                AND tt.statusenabled = TRUE
                AND kmr.statusenabled = TRUE
                and tt.objectstatusbedfk=1
                $parDep
                GROUP BY kmr.namakamar"))->count();
        $totalBed = 0;
        $rusak  = 0;
        $dipakaiperawat = 0;
        $terisipasien = 0;
        foreach ($data as $k) {
            $totalBed = $totalBed +  (float) $k->jml;
            if ($k->id == 5) {
                $rusak = $rusak + (float) $k->jml;
            }
            if ($k->id == 10) {
                $dipakaiperawat = $dipakaiperawat + (float) $k->jml;
            }
            if ($k->id == 1) {
                $terisipasien = $terisipasien + (float) $k->jml;
            }
        }

        $result['totalBed'] = $totalBed;
        $result['totalKamar'] = $kamar;
        $result['totalBedRusak'] = $rusak;
        $result['totalKamarRusak'] = $kamarRusak;
        $result['totalBedPerawat'] = $dipakaiperawat;
        $result['totalKamarPerawat'] = $kamarPerawat;
        $result['totalBedUtkPasien'] =  $result['totalBed'] - ($result['totalBedRusak'] +  $result['totalBedPerawat']);
        $result['totalKamarUtkPasien'] = $result['totalKamar'] - ($result['totalKamarRusak'] +  $result['totalKamarPerawat']);
        $result['totalBedIsi'] = $terisipasien;
        $result['totalKamarIsi'] = $kamarIsi;
        $result['totalBedKapasitas'] =  $result['totalBedUtkPasien'] - $result['totalBedIsi'];
        $result['totalKamarKapasitas'] = $result['totalKamarUtkPasien'] - $result['totalKamarIsi'];
        $result['totalPresentase'] = number_format(($result['totalBedIsi'] /  $result['totalBedUtkPasien']) * 100, 2, ',', '.');
        return $result;
        //        return $data;
    }
    public function getDataBed(Request $r)
    {
        $kdProfile = $_SESSION["kdProfile"];
        $valueEdit = collect(DB::select("
            select
            tt.id as tt_id,
            tt.nomorbed as namabed,
            kmr.id as kmr_id,
            kmr.namakamar,
            ru.id AS id_ruangan,
            ru.namaruangan,
            ps.nocm,ps.namapasien,
            ps.objectjeniskelaminfk as jkid,
            jk.jeniskelamin,ps.tgllahir,
            sb.id as sbid,sb.statusbed,
            EXTRACT(year FROM age(current_date,ps.tgllahir)) :: int as umur,
            pd.tglregistrasi,pd.noregistrasi,ps.nohp
            FROM
            tempattidur_m AS tt
            inner JOIN statusbed_m AS sb ON sb.id = tt.objectstatusbedfk
            inner JOIN kamar_m AS kmr ON kmr.id = tt.objectkamarfk
            inner JOIN ruangan_m AS ru ON ru.id = kmr.objectruanganfk
             LEFT JOIN antrianpasiendiperiksa_t as rpp on rpp.nobed=tt.id
                    and kmr.id= rpp.objectkamarfk
                    and rpp.statusenabled =true
                    and rpp.tglkeluar is null
                LEFT JOIN pasiendaftar_t AS pd ON pd.norec=rpp.noregistrasifk
                    and pd.tglpulang is  null
                    and pd.statusenabled=true
                LEFT JOIN pasien_m AS ps ON ps.id = pd.nocmfk
            --LEFT JOIN pasien_m AS ps ON ps.id = tt.nocmfk
            LEFT JOIN jeniskelamin_m AS jk ON jk.id = ps.objectjeniskelaminfk
             -- LEFT JOIN pasiendaftar_t AS pd ON pd.nocmfk = ps.id and pd.tglpulang is  null and pd.statusenabled=true
            WHERE tt.kdprofile = $kdProfile and
            tt.statusenabled = true and
            kmr.statusenabled = true
            and tt.id = $r[id]
           "))->first();
        $listStatus  = DB::table('statusbed_m')
            ->select('id', 'statusbed')
            //            ->where('kdprofile',$kdProfile)
            ->where('statusenabled', true)
            ->get();
        $valueEdit->umur_string = $this->getAge($valueEdit->tgllahir, date('Y-m-d'));
        //            dd($valueEdit);

        return view('module.view-bed.input-bed', compact(
            'valueEdit',
            'listStatus'
        ));
    }
    public function getAge($tgllahir, $now)
    {
        // dd($tgllahir);
        $datetime = new \DateTime(date($tgllahir));
        $y = $datetime->diff(new \DateTime($now))->y;
        $m = $datetime->diff(new \DateTime($now))->m;

        if ($y == 0 && $m == 0) {
            return $datetime->diff(new \DateTime($now))
                ->format('%dhr');
        }
        if ($y == 0 && $m > 0) {
            return $datetime->diff(new \DateTime($now))
                ->format('%mbln %dhr');
        }
        if ($y > 0 && $m > 0) {
            return $datetime->diff(new \DateTime($now))
                ->format('%ythn %mbln %dhr');
        }
        if ($y > 0 && $m == 0) {
            return $datetime->diff(new \DateTime($now))
                ->format('%ythn %mbln %dhr');
        }
    }
    public function getLamaRawat($tgllahir, $now)
    {
        $now = strtotime($now);
        $your_date = strtotime($tgllahir);
        $datediff = $now - $your_date;

        return round($datediff / (60 * 60 * 24));
    }
    public function saveDataBeds(Request $r)
    {
        DB::beginTransaction();
        try {
            if ($r->input('statusbeds') != 1) {
                DB::table('tempattidur_m')->where('id', $r['tt_id'])
                    ->where('kdprofile', $_SESSION["kdProfile"])->update(
                        [
                            'objectstatusbedfk' => $r->input('statusbeds'),
                            'nocmfk' =>  null,
                            'jeniskelaminfk' => null,
                        ]
                    );
            } else {
                DB::table('tempattidur_m')->where('id', $r['tt_id'])
                    ->where('kdprofile', $_SESSION["kdProfile"])->update(
                        [
                            'objectstatusbedfk' => $r->input('statusbeds'),
                            //                            'nocmfk' =>  null,
                            //                            'jeniskelaminfk' => null,
                        ]
                    );
            }
            session()->flash('type', "success");
            session()->flash('message', 'Data berhasil diupdate');
            toastr()->error('Incorrect username or password.', 'Error !');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', "error");
            session()->flash('message', 'Data gagal diupdate');
        }
        $dept = DB::table('ruangan_m')->where('id', $r['id_ruangan'])->first();
        //        return view("module.view-bed.index",compact('ruangan','data','namaruang','statusbed'));
        return redirect()->route("home", ['objectdepartemenfk' =>  !empty($dept) ? $dept->objectdepartemenfk : '', 'objectruanganfk' => $r['id_ruangan']]);
    }
    public function loginKeun(Request $r)
    {
        try {

            $data = array('username' => $r->username, 'password' => $r->password);
            $data = $this->validate_input($data);

            if ($this->validate_login($data)) {
//                dd($_SESSION["role"]);
                if (isset($_SESSION["role"]) && $_SESSION["role"] == 'user') {
                    return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => "bed"]);
                }
                return redirect()->route("show_page", ["role" => $_SESSION["role"], "pages" => $this->formAwal]);
            } else {
                toastr()->error('Incorrect username or password.', 'Error !');
                return redirect()->route("login",['username' =>   $r->username]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function validate_login($data)
    {
        $user = \DB::table('loginuser_s')
            ->where('namauser', $data["username"])
            ->where('passcode', $this->encryptSHA1($data['password']))
            ->first();
//            dd($user);
        if (!empty($user)) {
            $_SESSION["role"] = 'admin';
            $pegawai = DB::table('pegawaimt')->where('id', $user->objectpegawaifk)
                ->first();
            $profile =  DB::table('profile_m')
                ->where('id', '=', $user->kdprofile)
                ->first();
            $_SESSION["namaLengkap"] = $pegawai->namalengkap;
            $_SESSION["username"] = $user->namauser;
            $_SESSION["kdProfile"] = $profile->id;
            $_SESSION["namaProfile"] = $profile->namalengkap;
            $_SESSION["id"] = $user->id;
            $_SESSION['pegawai'] = $pegawai;
            $_SESSION["tokenLogin"] = $this->createToken2($user->namauser) . ''; //
            $sts = true;
        } else {
            $sts = false;
        }
        return $sts;
    }

    public function logoutKeun()
    {
        //        session_start();
        //        session_unset();
        if(isset($_SESSION) ){
            session_destroy();
        }
        return redirect()->route("login");
    }
  
}
