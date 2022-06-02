<?php
namespace App\Traits;

use App\Master\Pasien;
use App\Master\SettingDataFixed;
use App\Transaksi\RegistrasiPasien;
use App\User;

Trait PelayananPasienTrait
{
    protected function getProdukIdDeposit(){
        $set = SettingDataFixed::where('namafield', 'idProdukDeposit')->first();
        $this->id= ($set) ? (int)$set->nilaifield: null;

        return $this->id;
    }



    protected function getDepositPasien($noregistrasi){
        $noRegistrasi = $noregistrasi;
        $produkIdDeposit = $this->getProdukIdDeposit();
        $deposit = 0;
        $pasienDaftar  = RegistrasiPasien::has('pelayanan_pasien')->where('noregistrasi', "'".$noRegistrasi."'")->first();
        if($pasienDaftar){
            $depositList =$pasienDaftar->pelayanan_pasien()->where('nilainormal', '-1')->whereNull('strukfk')->get();
            foreach ($depositList as $item){
                if($item->produkfk==$produkIdDeposit){
                    $deposit = $deposit + $item->hargasatuan;
                }
            }
        }
        return $deposit;
    }

   

   

    protected function  getPercentageBiayaAdmin(){
        return 0.05;
    }

    protected function getUrlBrigdingBPJS(){
        $statusBridgingProduction = SettingDataFixed::where('namafield', 'isBridgingProduction')->first();

        if(!empty($statusBridgingProduction)){
            if($statusBridgingProduction->nilaifield == 'false') {
                $set = SettingDataFixed::where('namafield', 'linkBPJS')->first();
            } else{
                $set = SettingDataFixed::where('namafield', 'linkBPJSV1.1')->first();
            }
            return $set->nilaifield;
        }else{
            $set = SettingDataFixed::where('namafield', 'linkBPJSV1.1')->first();
        }
        return $set->nilaifield;
    }
    protected function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = [];
        $current = strtotime( $first );
        $last = strtotime( $last );

        while( $current <= $last ) {

            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }

        return $dates;
    }

    protected function getPortBrigdingBPJS(){
        $set = SettingDataFixed::where('namafield', 'portBrigdingBPJS')
            ->where('koders',$this->getKdProfile())->first();
        return '';
    }
    protected function getIdConsumerBPJS(){
        $set = SettingDataFixed::where('namafield', 'idConsumerBPJS')
            ->where('koders',$this->getKdProfile())->first();
        return $set->nilaifield;
    }
    protected function getPasswordConsumerBPJS(){
        $set = SettingDataFixed::where('namafield', 'PasswordConsumerBPJS')
            ->where('koders',$this->getKdProfile())->first();
        return $set->nilaifield;
    }
    protected function userKeyBPJS(){
        $set = SettingDataFixed::where('namafield', 'userKeyBPJS')
            ->where('koders',$this->getKdProfile())->first();
        return $set->nilaifield;
    }
    protected function getLinkAplicare(){
        $set = SettingDataFixed::where('namafield', 'urlBridgingAplicare')
            ->where('koders',$this->getKdProfile())->first();
        return $set->nilaifield;
    }
    protected  function  getKdProfile (){
        $session = \Session::get('userData');
        return  User::where('id',$session['id'])->first()->kdprofile;
    }

    protected function getUrlAplicare(){
        $statusBridgingProduction = SettingDataFixed::where('namafield', 'isBridgingProduction')->first();

        if(!empty($statusBridgingProduction)){
            if($statusBridgingProduction->nilaifield == 'false') {
                $set = SettingDataFixed::where('namafield', 'urlBridgingAplicareDev')->first();
            } else{
                $set = SettingDataFixed::where('namafield', 'urlBridgingAplicare')->first();
            }
            return $set->nilaifield;
        }else{
            $set = SettingDataFixed::where('namafield', 'urlBridgingAplicare')->first();
        }
        return $set->nilaifield;
    }

    protected function getUrlAntreanBPJSv2(){
        $statusBridgingProduction = SettingDataFixed::where('namafield', 'isBridgingProduction')->first();

        if(!empty($statusBridgingProduction)){
            if($statusBridgingProduction->nilaifield == 'false') {
                $set = SettingDataFixed::where('namafield', 'UrlAntreanBPJS_dev')->first();
            } else{
                $set = SettingDataFixed::where('namafield', 'UrlAntreanBPJS_prod')->first();
            }
            return $set->nilaifield;
        }else{
            $set = SettingDataFixed::where('namafield', 'UrlAntreanBPJS_prod')->first();
        }
        return $set->nilaifield;
    }
    protected function getUserKeyAntreanBPJSv2(){
     
         $set = SettingDataFixed::where('namafield', 'UserKeyAntreanBPJS')->first();
        
        return $set->nilaifield;
    }


}
