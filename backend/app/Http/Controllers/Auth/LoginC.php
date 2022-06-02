<?php
/**
 * Created by PhpStorm.
 * User: GIW
 * Date: 7/31/2019
 * Time: 4:33 PM
 */

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\ApiController;
use App\Master\Agama;
use App\Master\Pegawai;
use App\Master\SettingDataFixed;
use App\Traits\CrudMaster;
use App\Traits\Valet;
use App\Transaksi\LoggingUser;
use Illuminate\Http\Request;

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

class LoginC extends ApiController {
    use CrudMaster;


    public function __construct() {
        parent::__construct($skip_authentication=true);
    }
    public function createToken($namaUser){
        $class = new Builder();
        $signer = new Sha512();
        $token = $class->setHeader('alg','HS512')
            ->set('sub', $namaUser)
            ->sign($signer, "PCURE")
            ->getToken();
        return $token;
    }
    public function signIn(Request $request)
    {
        $login = DB::table('loginuser_s')
            ->where('katasandi', '=', $this->encryptSHA1($request->input('kataSandi')))
            ->where('namauser', '=', $request->input('namaUser'))
            ->where('statusenabled', '=', true);
        $LoginUser = $login->get();
        if (count($LoginUser) > 0){
            $kelompokUser = DB::table('kelompokusermt')
                ->select('id','kelompokuser as kelompokUser','kodeexternal')
                ->where('id', '=',$LoginUser[0]->objectkelompokuserfk)
                ->first();
            $pegawai = Pegawai::where('id',$LoginUser[0]->objectpegawaifk)
                ->first();
            $mapLoginUserToRuangan = DB::table('maploginusertoruanganmt as mlur')
                ->join('loginuser_s as lu','lu.id','=','mlur.loginuseridfk')
                ->join('ruanganmt as ru','ru.id','=','mlur.ruanganidfk')
                ->join('instalasimt as dept','dept.id','=','ru.instalasiidfk')
                ->select('ru.id','ru.namaruangan','ru.instalasiidfk','dept.namadepartemen as departemen')
                ->where('lu.id', '=',$LoginUser[0]->id)
                ->get();
            $jenisPegawai =  DB::table('jenispegawaimt')
                ->where('id', '=',$pegawai->objectjenispegawaifk)
                ->first();

            $profile =  DB::table('profile_m')
                ->where('id', '=', $LoginUser[0]->kdprofile)
                ->first();
            $resPegawai= array(
                'id' =>$pegawai->id,
                'namaLengkap' =>$pegawai->namalengkap,
                'tempatLahir'=> $pegawai->tempatlahir,
                'tglLahir'=> $pegawai->tgllahir,
                'noIdentitas' => $pegawai->noidentitas,
                'statusEnabled' => $pegawai->aktif ,
                'jenisPegawai' => $jenisPegawai,
                'kdProfile' => $pegawai->kdprofile,

            );

            $dataLogin = array(
                'id' => $LoginUser[0]->id,
                'kdProfile' => $LoginUser[0]->kdprofile,
                'namaUser' => $LoginUser[0]->namauser,
                'kataSandi'=> $LoginUser[0]->katasandi,
                'kelompokUser'=> $kelompokUser,
                'mapLoginUserToRuangan' => $mapLoginUserToRuangan,
                'jenisPegawai' => $jenisPegawai,
                'pegawai' => $resPegawai,
                'profile' => $profile
            ) ;

            $date = strtotime('+10 minutes',strtotime( date('Y-m-d H:i:s')));
            $expired = date('Y-m-d H:i:s',$date);
            $en = base64_encode($expired);

            $expFE = date('Y-m-d H:i:s',strtotime('+1 minutes',strtotime( date('Y-m-d H:i:s'))));
            $dataLogin['exp'] = $expFE;
            $token['X-AUTH-TOKEN'] = $this->createToken($LoginUser[0]->namauser).'';
            $token['X-AUTH-TOKEN'] =  $token['X-AUTH-TOKEN'].'.'.$en;
            $result = array(
                'data' => $dataLogin,
                'messages' =>$token,
                'status'=> 201,
                'as'=> '#Xoxo'
            );

        }else{
            $result = array(
                'data' => [],
                'messages' => 'Login gagal, Username atau Password salah',
                'status'=> 400,
                'as'=> '#Xoxo'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result);
    }
    public function signOut (Request $request)
    {
        $result['code'] = 401;
        $result['message'] = 'You have not logged';
        $QueryLogin = DB::table('loginuser_s')
            ->where('namauser', '=', $request->input('kdUser'));
        $LoginUser = $QueryLogin->get();
        if(count($LoginUser) > 0){
            $result['message'] = 'Logout Success';
            $result['id'] =$LoginUser[0]->id;
            $result['kdUser'] = $LoginUser[0]->namauser;
            $result['code'] = 200;
            $result['as'] = '#Xoxo';
        }
        $resData = array(
            'data' => $result
        );
        return response()->json($resData);
    }

    protected function encryptSHA1($pass)
    {
        return sha1($pass);
    }
    protected function encryptHash($pass)
    {
        return  Hash::make($pass);
    }
    public function ubahPassword (Request $request)
    {
        try{
            $cekUser = LoginUser::where('namauser',$request['namaUser'])
                ->first();
            $sama = false ;
            if(!empty($cekUser)){
                if($cekUser->id != $request['id'] && $request['namaUser'] == $cekUser->namauser ){
                    $sama = true;
                }
            }
            if($sama ==  true){
                $result = array(
                    "status" => 400,
                    "as" => '#Xoxo'
                );
                return $this->setStatusCode($result['status'])->respond($result, 'Nama User sudah ada');
            }

           $d= LoginUser::where('id',$request['id'])->update(
                [
                    'passcode' => $this->encryptSHA1($request->input('kataSandi')),
                    'katasandi' => $this->encryptSHA1($request->input('kataSandi')),
                    'objectkelompokuserfk' => $request['kelompokUser']['id'],
                    'namauser' => $request['namaUser']
                ]
            );

            $transStatus = true;
        } catch (\Exception $e) {
            $transStatus = false;
        }

        if ($transStatus) {
            $transMessage = "Sukses";
            DB::commit();
            $result = array(
                "status" => 201,
                "as" => '#Xoxo',
                "data" => $d,
            );
        } else {
            $transMessage = "Failed";
            DB::rollBack();
            $result = array(
                "status" => 400,
                "as" => '#Xoxo'
            );
        }
        return $this->setStatusCode($result['status'])->respond($result, $transMessage);
    }


    
    public function saveECG(Request $r)
    {
        $req =  $r->getContent();
        echo "##$EPI";
        $req = str_replace('[ECG SERVER V1.0]','',$req);
        $req = str_replace('slvR=##$ECG','',$req);
        $req = str_replace('~','',$req);

        $xml = simplexml_load_string($req, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array =json_decode($json,TRUE);
        $uid=  substr(Uuid::generate(), 0, 6);
        $save =[];
        $fdata = array_values($array['Data']) ;
        $i = 1;
        for ($x = 0; $x < count($fdata); $x++) {
            $ket = str_replace('xmlECG','', array_keys($array['Data'])[$x]) ;
            if($ket !='xmlHeader' && $ket  != 'Data'){
                if($ket == 'date'){
                    $ket ='ecgDate';
                }
                if($ket == 'Time'){
                    $ket ='ecgTime';
                }
                $save[] =array(
                    'norec'=> date('YmdHis').$uid.$array['Data']['xmlECGCustomerID'],
                    'kunci' => $ket,
                    'nilai' =>$fdata[$x],
                    'urut' => $i,
                    'customerid' => $array['Data']['xmlECGCustomerID'],
                    'datesend' => $array['Data']['xmlECGdate'].' '.$array['Data']['xmlECGTime']
                );
                $i++;
            }
        }
        $save[] =array(
            'norec'=> date('YmdHis').$uid.$array['Data']['xmlECGCustomerID'],
            'kunci' => 'expertise',
            'nilai' =>'',
            'urut' => 10,
            'customerid' => $array['Data']['xmlECGCustomerID'],
            'datesend' => $array['Data']['xmlECGdate'].' '.$array['Data']['xmlECGTime']
        );

        $frame = array_values($array['Data']['xmlECGData']) ;
        for ($x = 0; $x < count($frame); $x++) {
            $save[] =array(
                'norec'=> date('YmdHis').$uid.$array['Data']['xmlECGCustomerID'],
                'kunci' => 'xmlframe',
                'nilai' => $frame[$x],
                'urut' => 20 + $x,
                'customerid' => $array['Data']['xmlECGCustomerID'],
                'datesend' => $array['Data']['xmlECGdate'].' '.$array['Data']['xmlECGTime']
            );
        }
            $dataInsert =[];
            foreach ($save as $s){
                $dataInsert[] = [
                    'norec' => $s['norec'],
                    'reportdisplay' => 'SLVR',
                    'kunci' => $s['kunci'],
                    'nilai' => $s['nilai'],
                    'urut' => $s['urut'],
                    'customerid' => $s['customerid'],
                    'datesend' => $s['datesend'],
                ];

                if (count($dataInsert) > 100){
                    DB::table('eecg_t')->insert($dataInsert);
                    $dataInsert = [];
                }
             }
             DB::table('eecg_t')->insert($dataInsert);
             $result = array("response"=>'ECG',
                 "metadata"=>
                     array(
                         "code" => "200",
                         "message" => "Sukses")
             );
            return response()->json($result);

    }
    public function getTokenExt(){
        $set = SettingDataFixed::where('namafield', 'tokenNonLogin')->first();
        $res['xtkn'] =$set->nilaifield;
        return $res;
    }
    public function getBedMonitor(Request $r){
        $data =  DB::table('tempattidurmt AS tt')
        ->JOIN ('statusbedmt AS sb','sb.id','=','tt.statusbedidfk')
        ->JOIN ('kamarmt AS kmr','kmr.id','=','tt.kamaridfk')
        ->JOIN ('kelasmt AS kl','kl.id','=','kmr.kelasidfk')
        ->JOIN ('ruanganmt AS ru','ru.id','=','kmr.ruanganidfk')
        ->select(DB::raw("kl.namakelas,
        ru.namaruangan ,
        COUNT ( tt.id ) AS kapasitas,
        COUNT ( tt.id )  - SUM ( CASE WHEN sb.id= 2 THEN 1 ELSE 0 END ) as terisi,
        SUM ( CASE WHEN sb.id = 2 THEN 1 ELSE 0 END ) AS tersedia "))
        ->where('tt.aktif',true)
        ->where('kmr.aktif',true)
        ->where('ru.aktif',true)
        ->groupBy('kl.namakelas','ru.namaruangan')
        ->limit($r['limit'])
        ->offset($r['offset'])
        ->get();
        return $this->respond($data);
    }
    public function getBedMonitorKelas(){
        $data = DB::select(DB::raw("SELECT

        kl.namakelas,
        COUNT ( tt.ID ) AS kapasitas,
        
        SUM ( CASE WHEN sb.ID = 2 THEN 1 ELSE 0 END ) AS tersedia 
        FROM tempattidurmt AS tt
        INNER JOIN statusbedmt AS sb ON sb. ID = tt.statusbedidfk
        INNER JOIN kamarmt AS kmr ON kmr. ID = tt.kamaridfk
        INNER JOIN kelasmt AS kl ON kl. ID = kmr.kelasidfk
        WHERE tt.aktif = TRUE
        AND kmr.aktif = TRUE
        
        GROUP BY 
        kl.namakelas
        
        "));
        return $this->respond($data);
    }

      public function getTokens(Request $request)
    {
        if ($request->method() == 'POST') {
            $req =  $request->json()->all();
            $login = DB::table('loginuser_s')
                ->where('passcode', '=', $this->encryptSHA1($req['password']))
                ->where('namauser', '=', $req['username']);
        } elseif ($request->method() == 'GET') {
            $login = DB::table('loginuser_s')
                ->where('passcode', '=', $this->encryptSHA1($request->header('x-password')))
                ->where('namauser', '=', $request->header('x-username'));
        }

        $LoginUser = $login->get();
        if (count($LoginUser) > 0){
            $profile =  DB::table('profile_m')
                ->where('id', '=', $LoginUser[0]->kdprofile)
                ->first();
            $dataLogin = array(
                'id' => $LoginUser[0]->id,
                'kdProfile' => $LoginUser[0]->kdprofile,
                'namaProfile' => $profile->namaexternal,
                'namaUser' => $LoginUser[0]->namauser,

            ) ;

            $date = strtotime('+20 minutes',strtotime( date('Y-m-d H:i:s')));
            $expired = date('Y-m-d H:i:s',$date);

            $en = base64_encode($expired);
            $token['X-AUTH-TOKEN'] = $this->createToken($LoginUser[0]->namauser).'';
            $result = array(
                "response"=>array(
                    "token"=> $token['X-AUTH-TOKEN'].'.'.$en
                ),
                "metadata"=>array(
                    "message" => 'Ok',
                    "code" => 200,
                )
            );
            //endregion
        }else{
            $result = array(
                // "response"=> null,
                "metadata"=> array(
                    "message" => 'Username atau password Tidak sesuai',
                    "code" => 201,
                )
            );
            //endregion
        }


        return $this->setStatusCode($result['metadata']['code'])->respond($result);
    }

}