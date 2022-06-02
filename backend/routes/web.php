<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth, App\Http\Controllers\Modul, App\Http\Controllers\Reservasi,
    App\Http\Controllers\SysAdmin,App\Http\Controllers\Registrasi,App\Http\Controllers\Humas,
    App\Http\Controllers\Kasir,App\Http\Controllers\Bridging,App\Http\Controllers\General,
    App\Http\Controllers\RawatInap, App\Http\Controllers\EMR,App\Http\Controllers\Penunjang,
    App\Http\Controllers\KiosK,App\Http\Controllers\Farmasi,App\Http\Controllers\Logistik,
    App\Http\Controllers\RawatJalan,App\Http\Controllers\EIS,App\Http\Controllers\BendaharaPenerimaan
    ,App\Http\Controllers\Laporan,App\Http\Controllers\Cetak;
use App\Traits\Valet;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    $profile = \App\Web\Profile::where('statusenabled', true)->first();
    return view('welcome', compact('profile'));
});
Route::post('/notification', function () {
    $arr['endpoint'] ='https://fcm.googleapis.com/fcm/send/dUbChzYXFSQ:APA91bEcM0UXzo4GPmKtP6jTXtKeHFrJ-_qhMgNxE_YXfD4jbefA567Wi5Sb183G3pYWQSTzvV40z5k3mpsdheLKBWhBz184cLMe6uzQ51Qoy3PwBShQSRQonrni8bylHNZrqACEUvUY';
    $arr['expirationTime'] = null;
    $arr['key']['p256dh'] ='BDEglRj5umJZ8RjSoYc_pgFAYLwMkQye6rhvATWfFyCVjerVmUjMvuHNu2FakpKKo_fQpk4o9BATOTKlbxb2rfg';
    $arr['key']['auth'] ='itBJQ5Vep2mU5-whxt43og';
    return response()->json($arr);
});


Route::group(['middleware' => 'cors', 'prefix' => 'service'], function () {

    Route::get('get-profile-login', function () {
        $profile = \App\Web\Profile::where('statusenabled', true)->first();
        return array('as' => 'Xoxo', 'profile' => $profile->login);
    });

    Route::post('auth/sign-in', [Auth\LoginC::class, 'signIn']);
    Route::post('auth/sign-out', [Auth\LoginC::class, 'signOut']);
    Route::post('auth/ubah-password', [Auth\LoginC::class, 'ubahPassword']);
    Route::get('auth/token', [Auth\LoginC::class, 'getTokenExt']);
    Route::get('bed/get-kelas', [Auth\LoginC::class, 'getBedMonitorKelas']);
    Route::get('bed/get-ruangan', [Auth\LoginC::class, 'getBedMonitor']);
    //** START BENDAHARAPENERIMAAN */
    //** GET */
    Route::get('bendaharapenerimaan/get-combo-bp', [BendaharaPenerimaan\BendaharaPenerimaanC::class, 'getDataComboBP']);
    Route::get('bendaharapenerimaan/get-penerimaan-kasir', [BendaharaPenerimaan\BendaharaPenerimaanC::class, 'getDaftarSBM']);
    Route::get('bendaharapenerimaan/get-daftar-setoran-kasir', [BendaharaPenerimaan\BendaharaPenerimaanC::class, 'getDaftarSetoranKasir']);
    //** END GET */
    //** POST */
    Route::post('bendaharapenerimaan/save-setoran-kasir', [BendaharaPenerimaan\BendaharaPenerimaanC::class, 'simpanSetoranKasir']);
    Route::post('bendaharapenerimaan/save-batal-setoran-kasir', [BendaharaPenerimaan\BendaharaPenerimaanC::class, 'batalSetoranKasir']);
    //** END POST */
    //** END BENDAHARAPENERIMAAN */

    Route::get('bridging/bpjs/get-signature', [Bridging\BPJSC::class, 'getSignature']);
    Route::get('bridging/bpjs/get-has-code', [Bridging\BPJSC::class, 'getHasCode']);
    Route::get('bridging/bpjs/get-combo-bpjs-txt', [Bridging\BPJSC::class, 'getComboBPJS']);
    Route::get('bridging/bpjs/get-poli', [Bridging\BPJSC::class, 'getPoli']);
    Route::get('bridging/bpjs/get-ref-diagnosa', [Bridging\BPJSC::class, 'getDiagnosa']);
    Route::get('bridging/bpjs/get-ref-diagnosa-part', [Bridging\BPJSC::class, 'getDiagnosaPart']);
    Route::get('bridging/bpjs/get-ref-faskes', [Bridging\BPJSC::class, 'getFaskes']);
    Route::get('bridging/bpjs/get-ref-faskes-part', [Bridging\BPJSC::class, 'getFaskesSaeutik']);
    Route::get('bridging/bpjs/get-ref-diagnosatindakan', [Bridging\BPJSC::class, 'getProcedureDiagnosaTindakan']);
    Route::get('bridging/bpjs/get-ref-diagnosatindakan-part', [Bridging\BPJSC::class, 'getProcedureDiagnosaTindakanPart']);
    Route::get('bridging/bpjs/get-ref-kelasrawat', [Bridging\BPJSC::class, 'getKelasRawat']);
    Route::get('bridging/bpjs/get-ref-dokter', [Bridging\BPJSC::class, 'getDokter']);
    Route::get('bridging/bpjs/get-ref-spesialistik', [Bridging\BPJSC::class, 'getSpesialistik']);
    Route::get('bridging/bpjs/get-ref-ruangrawat', [Bridging\BPJSC::class, 'getRuangRawat']);
    Route::get('bridging/bpjs/get-ref-carakeluar', [Bridging\BPJSC::class, 'getCaraKeluar']);
    Route::get('bridging/bpjs/get-ref-pascapulang', [Bridging\BPJSC::class, 'getPascaPulang']);
    Route::get('bridging/bpjs/get-ref-diagnosa-contoh', [Bridging\BPJSC::class, 'getDiagnosaReferen']);
    Route::get('bridging/bpjs/get-ref-dokter-part', [Bridging\BPJSC::class, 'getDokterSaeutik']);
    Route::get('bridging/bpjs/get-poli-part', [Bridging\BPJSC::class, 'getPoliSaeutik']);
    Route::get('bridging/bpjs/get-ref-dokter-dpjp', [Bridging\BPJSC::class, 'getDokterDPJP']);
    Route::get('bridging/bpjs/get-ref-propinsi', [Bridging\BPJSC::class, 'getPropinsi']);
    Route::get('bridging/bpjs/get-ref-kabupaten', [Bridging\BPJSC::class, 'getKabupaten']);
    Route::get('bridging/bpjs/get-ref-kecamatan', [Bridging\BPJSC::class, 'getKecamatan']);
    Route::get('bridging/bpjs/cek-sep', [Bridging\BPJSC::class, 'cekSep']);
    Route::get('bridging/bpjs/generateskdp', [Bridging\BPJSC::class, 'generateNoSKDP']);
    Route::get('bridging/bpjs/generate-sep-dummy', [Bridging\BPJSC::class, 'generateSEPDummy']);
    Route::get('bridging/bpjs/get-no-peserta', [Bridging\BPJSC::class, 'getNoPeserta']);
    Route::get('bridging/bpjs/get-nik', [Bridging\BPJSC::class, 'getNIK']);
    Route::get('bridging/bpjs/get-no-peserta-v1', [Bridging\BPJSC::class, 'getNoPesertaV1']);
    Route::get('bridging/bpjs/get-integrasi-inacbg', [Bridging\BPJSC::class, 'getIntegrasiSepInaCbg']);
    Route::get('bridging/bpjs/get-suplesi-jasaraharja', [Bridging\BPJSC::class, 'getSuplesiJasaRaharja']);
    Route::get('bridging/bpjs/get-rujukan-rs', [Bridging\BPJSC::class, 'getNoRujukanRs']);
    Route::get('bridging/bpjs/get-rujukan-pcare', [Bridging\BPJSC::class, 'getNoRujukanPcare']);
    Route::get('bridging/bpjs/get-rujukan-rs-nokartu', [Bridging\BPJSC::class, 'getNoRujukanRsNoKartu']);
    Route::get('bridging/bpjs/get-rujukan-pcare-nokartu', [Bridging\BPJSC::class, 'getNoRujukanPcareNoKartu']);
    Route::get('bridging/bpjs/get-rujukan-pcare-nokartu-multi', [Bridging\BPJSC::class, 'getRujukanNoKartuMulti']);
    Route::get('bridging/bpjs/get-rujukan-rs-nokartu-multi', [Bridging\BPJSC::class, 'getRujukanNoKartuMultiRS']);
    Route::get('bridging/bpjs/get-rujukanbytglrujukan', [Bridging\BPJSC::class, 'getRujukanByTglRujukan']);
    Route::get('bridging/bpjs/get-rujukanbytglrujukan-rs', [Bridging\BPJSC::class, 'getRujukanByTglRujukanRS']);
    Route::get('bridging/bpjs/data-lpk', [Bridging\BPJSC::class, 'dataLPK']);
    Route::get('bridging/bpjs/get-monitoring-kunjungan', [Bridging\BPJSC::class, 'getMonitoringKunjungan']);
    Route::get('bridging/bpjs/get-monitoring-klaim', [Bridging\BPJSC::class, 'getMonitoringKlaim']);
    Route::get('bridging/bpjs/monitoring/HistoriPelayanan/NoKartu/{noKartu}', [Bridging\BPJSC::class, 'getMonitoringHistori']);
    Route::get('bridging/bpjs/get-monitoring-klaim-jasaraharja', [Bridging\BPJSC::class, 'getMonitoringJasaRaharja']);
    Route::get('bridging/bpjs/get-monitoring-historipelayanan-peserta', [Bridging\BPJSC::class, 'getHistoryPelayananPeserta']);
    Route::get('bridging/bpjs/get-diagnosa-saeutik', [Bridging\BPJSC::class, 'getDiagnosaSaeutik']);
    Route::get('bridging/bpjs/get-diagnosa-tindakan-saeutik', [Bridging\BPJSC::class, 'getDiagnosaTindakanSaeutik']);
    Route::get('bridging/bpjs/get-ruangan-ri', [Bridging\BPJSC::class, 'getRuanganRI']);
    Route::get('bridging/bpjs/get-ruangan-rj', [Bridging\BPJSC::class, 'getRuanganRJ']);
    Route::get('bridging/bpjs/get-sep-bynoregistrasi', [Bridging\BPJSC::class, 'getSepByNoregistrasi']);
    Route::get('bridging/bpjs/get-checklist-klaim', [Bridging\BPJSC::class, 'getChecklistKlaim']);
    Route::get('bridging/bpjs/get-monitoring-klaim-status', [Bridging\BPJSC::class, 'getMonitoringKlaimStts']);
    Route::get('bridging/bpjs/get-daftar-rujukan', [Bridging\BPJSC::class, 'getLokalRujukan']);
    Route::get('bridging/bpjs/get-daftar-poli-internal', [Bridging\BPJSC::class, 'getRuanganBPJSInternal']);
    Route::get('bridging/bpjs/get-nosep-by-norec-pd', [Bridging\BPJSC::class, 'getNoSEPByNorecPd']);
    Route::get('bridging/bpjs/aplicaresws/get-tt', [Bridging\BPJSC::class, 'getKetersediaanTTNew']);
    Route::get('bridging/bpjs/aplicaresws/rest/ref/kelas', [Bridging\BPJSC::class, 'getReferensiKamar']);
    Route::get('bridging/bpjs/aplicaresws/rest/bed/read/{kodeppk}/{start}/{limit}', [Bridging\BPJSC::class, 'getKetersedianKamarRS']);

    Route::post('bridging/bpjs/insert-sep', [Bridging\BPJSC::class, 'insertSEP']);
    Route::post('bridging/bpjs/insert-sep-v1.1', [Bridging\BPJSC::class, 'insertSepV11']);
    Route::post('bridging/bpjs/post-pengajuan', [Bridging\BPJSC::class, 'postPengajuan']);
    Route::post('bridging/bpjs/post-aprovalSEP', [Bridging\BPJSC::class, 'postApprovalPengajuanSep']);
    Route::post('bridging/bpjs/insert-rujukan', [Bridging\BPJSC::class, 'insertRujukan']);
    Route::post('bridging/bpjs/insert-lpk', [Bridging\BPJSC::class, 'insertLPK']);
    Route::post('bridging/bpjs/save-bpjs-klaim', [Bridging\BPJSC::class, 'simpanBpjsKlaim']);
    Route::post('bridging/bpjs/save-bpjs-klaim-gagal-hitung', [Bridging\BPJSC::class, 'simpanGagalHitungBpjsKlaim']);
    Route::post('bridging/bpjs/save-rujukan', [Bridging\BPJSC::class, 'simpanLokalRujukan']);
    Route::post('bridging/bpjs/save-monitoring-klaim', [Bridging\BPJSC::class, 'saveMonitoringKlaim']);
    Route::post('bridging/bpjs/aplicaresws/rest/bed/update/{kodeppk}', [Bridging\BPJSC::class, 'updateKetersediaanTT']);
    Route::post('bridging/bpjs/aplicaresws/rest/bed/create/{kodeppk}', [Bridging\BPJSC::class, 'postRuanganBaru']);
    Route::post('bridging/bpjs/aplicaresws/rest/bed/delete/{kodeppk}', [Bridging\BPJSC::class, 'hapusRuangan']);

    Route::put('bridging/bpjs/update-tglpulang', [Bridging\BPJSC::class, 'updateTglPulang']);
    Route::put('bridging/bpjs/update-sep-v1.1', [Bridging\BPJSC::class, 'updateSepV11']);
    Route::put('bridging/bpjs/update-sep', [Bridging\BPJSC::class, 'updateSEP']);
    Route::put('bridging/bpjs/update-rujukan', [Bridging\BPJSC::class, 'updateRujukan']);
    Route::put('bridging/bpjs/update-lpk', [Bridging\BPJSC::class, 'updateLPK']);

    Route::post('bridging/bpjs/save-data-mappingdkoterbpjs', [Bridging\BPJSC::class, 'saveMappingDokterBpjsDokterRs']);
    Route::get('bridging/bpjs/get-data-mappingdkoterbpjs', [Bridging\BPJSC::class,'getDaftarMappingDokterBpjsToDokterRs']);
    Route::post('bridging/bpjs/hapus-data-mappingdkoterbpjs',[Bridging\BPJSC::class,'saveHapusMappingDokterBpjsDokterRs']);
   
    Route::post('bridging/bpjs/tools', [Bridging\BPJSC::class, 'bpjsTools']);

    Route::get('bridging/bpjs/aplicare', [Bridging\BPJSC::class, 'getTT']);
    Route::get('bridging/antrean/get-combo', [Bridging\BPJSC::class, 'getComboAntrean']);

    Route::delete('delete-rujukan', [Bridging\BPJSC::class, 'deleteRujukan']);
    Route::delete('bridging/bpjs/delete-sep', [Bridging\BPJSC::class, 'deleteSEP']);
    Route::delete('delete-lpk', [Bridging\BPJSC::class, 'deleteLPK']);

    Route::get('bridging/inacbg/get-daftar-pasien-inacbg-rev-2', [Bridging\InacbgC::class, 'getDaftarPasienRev']);

    Route::post('bridging/inacbg/save-proposi-bridging-inacbg', [Bridging\InacbgC::class, 'saveProposiBridgingINACBG']);
    Route::post('bridging/inacbg/get-daftar-pasien-statusnaikkelas', [Bridging\InacbgC::class, 'getStatusNaikKelas']);
    Route::post('bridging/inacbg/save-bridging-inacbg',[Bridging\InacbgC::class,'saveBridgingINACBG']);
    Route::post('bridging/inacbg/save-pengajuan-klaim',[Bridging\InacbgC::class,'savePengajuanKlaim']);

    Route::get('bridging/sisrute/rujukan/get',[Bridging\SisruteC::class,'getRujukan']);

    Route::get('eis/get-data-dashboard', [EIS\EISC::class, 'getDashboard']);

    Route::get('emr/get-antrian-pasien-norec/{norec}', [EMR\EMRC::class, 'getAntrianPasienDiperiksa']);
    Route::get('emr/get-menu-rekam-medis-dynamic', [EMR\EMRC::class, 'getMenuRekamMedisAtuh']);
    Route::get('emr/get-combo-penunjang', [EMR\EMRC::class, 'getComboPenunjangOrder']);
    Route::get('emr/get-riwayat-order-penunjang', [EMR\EMRC::class, 'getRiwayatOrderPenunjang']);
    Route::get('emr/get-expertise', [EMR\EMRC::class, 'getExpertise']);
    Route::get('emr/get-ruangan-konsul', [EMR\EMRC::class, 'getRuangKonsul']);
    Route::get('emr/get-order-konsul', [EMR\EMRC::class, 'getOrderKonsul']);
    Route::get('emr/count-order-konsul', [EMR\EMRC::class, 'countOrderKonsul']);
    Route::get('emr/get-daftar-konsul-from-order', [EMR\EMRC::class, 'getDaftarKonsulFromOrder']);
    Route::get('emr/get-emr-transaksi-detail-form', [EMR\EMRC::class, 'getEMRTransaksiDetailForm']);
    Route::get('emr/get-rekam-medis-dynamic', [EMR\EMRC::class, 'getRekamMedisAtuh']);
    Route::get('emr/get-emr-transaksi-detail', [EMR\EMRC::class, 'getEMRTransaksiDetail']);
    Route::get('emr/get-datacombo-part-pegawai', [EMR\EMRC::class, 'getDataComboPegawaiPart']);
    Route::get('emr/get-datacombo-part-ruangan', [EMR\EMRC::class, 'getDataComboRuanganPart']);
    Route::get('emr/get-datacombo-part-diagnosa', [EMR\EMRC::class, 'getDataComboDiagnosaPart']);
    Route::get('emr/get-datacombo-part-tindakan', [EMR\EMRC::class, 'getDataComboTindakanPart']);
    Route::get('emr/get-datacombo-part-dokter', [EMR\EMRC::class, 'getDataComboDokterPart']);
    Route::get('emr/get-datacombo-part-ruangan-pelayanan', [EMR\EMRC::class, 'getDataComboRuanganPelayananPart']);
    Route::get('emr/get-datacombo-part-jk', [EMR\EMRC::class, 'getDataComboJKPart']);
    Route::get('emr/get-datacombo-part-jenisdiagnosa', [EMR\EMRC::class, 'getComboJensiDiagnosaPart']);
    Route::get('emr/get-datacombo-part-diagnosa-tindakan', [EMR\EMRC::class, 'getDataComboDiagnosa9Part']);
    Route::get('emr/get-pasien-triase', [EMR\EMRC::class, 'getPasienTriase']);
    Route::get('emr/get-datacombo-part-kelompokpasien', [EMR\EMRC::class, 'getDataComboKelompokpPasien']);
    Route::get('emr/get-datacombo-part-asalrujukan', [EMR\EMRC::class, 'getDataComboAsalRujukan']);
    Route::get('emr/get-data-riwayat-emr', [EMR\EMRC::class, 'getDataRiwayatEMR']);
    Route::get('emr/get-soap', [EMR\EMRC::class, 'getSOAP']);
    Route::get('emr/get-combo-resep-emr', [EMR\EMRC::class, 'getDataComboResepEMR']);
    Route::get('emr/get-produk-resep', [EMR\EMRC::class, 'getProdukResep']);
    Route::get('emr/get-produkdetail', [Farmasi\ResepC::class, 'getProdukDetail']);
    Route::get('emr/get-jenis-obat', [EMR\EMRC::class, 'getJenisObat']);
    Route::get('emr/get-daftar-detail-order', [EMR\EMRC::class, 'getDaftarDetailOrder']);
    Route::get('emr/get-transaksi-pelayanan', [Farmasi\FarmasiC::class, 'getTransaksiPelayananApotik']);
    Route::get('emr/get-daftar-detail-alkes', [EMR\EMRC::class, 'getDaftarDetailInputAlkes']);

    Route::post('emr/save-soap', [EMR\EMRC::class, 'saveSOAP']);
    Route::post('emr/save-order-pelayanan', [EMR\EMRC::class, 'saveOrderPelayananLabRad']);
    Route::post('emr/delete-order-pelayanan', [EMR\EMRC::class, 'hapusOrderPelayananLabRad']);
    Route::post('emr/post-konsultasi', [EMR\EMRC::class, 'saveOrderKonsul']);
    Route::post('emr/save-konsul-from-order', [EMR\EMRC::class, 'saveKonsulFromOrder']);
    Route::post('emr/ubah-dokter', [EMR\EMRC::class, 'ubahDokter']);
    Route::post('emr/save-emr-dinamis', [EMR\EMRC::class, 'SaveTransaksiEMRBackup']);
    Route::post('emr/hapus-emr-transaksi-norec', [EMR\EMRC::class, 'hapusEMRtransaksiNorec']);
    Route::post('emr/disable-emr-details', [EMR\EMRC::class, 'disableEMRdetail']);
    Route::post('emr/simpan-order-pelayananobatfarmasi', [EMR\EMRC::class, 'SimpanOrderPelayananObat']);
    Route::post('emr/hapus-order-pelayananobatfarmasi', [EMR\EMRC::class, 'hapusOrderResep']);
    Route::post('emr/simpan-input-obat-alkes', [EMR\EMRC::class, 'SimpanPelayananAlkesBhp']);
    Route::post('emr/hapus-input-obat-alkes', [EMR\EMRC::class, 'DeletePelayananAlkesBhp']);

    Route::get('general/get-data-combo-dokter-part', [General\GeneralC::class, 'getComboDokterPart']);
    Route::get('general/get-data-combo-rekanan-part', [General\GeneralC::class, 'getComboRekananPart']);
    Route::get('general/get-data-closing-pasien/{noRegister}', [General\GeneralC::class, 'getDataClosing']);
    Route::get('general/get-data-pelayanan-antrian/{noRec}', [General\GeneralC::class, 'getDataLayananPerAntrian']);
    Route::get('general/get-pasien-bynorec-general', [General\GeneralC::class, 'getPasienByNoregGeneral']);
    Route::get('general/get-data-combo-icdx-part', [General\GeneralC::class, 'getIcd10']);
    Route::get('general/get-data-combo-icdix-part', [General\GeneralC::class, 'getIcd9']);
    Route::get('general/get-pasien-byregistrasiruangan-general', [General\GeneralC::class, 'getPasienByRegistrasiRuanganGeneral']);
    Route::get('general/get-pasien-bystrukpelayan-general', [General\GeneralC::class, 'getPasienByStrukPelayananGeneral']);
    Route::get('general/get-pasien-nonlayanan-general', [General\GeneralC::class, 'getPasienNonLayanan']);
    Route::get('general/get-paket-tindakan', [General\GeneralC::class, 'getPaketTindakan']);
    Route::get('general/get-jenis-pelayanan', [General\GeneralC::class, 'getJenisPelayananByNorecPd']);
    Route::get('general/get-combo-produk-part', [General\GeneralC::class, 'getProdukPart']);
    Route::get('general/get-data-kartu-stok', [General\GeneralC::class, 'GetDataKartuStok']);
    Route::get('general/get-detail-pasien', [General\GeneralC::class, 'getDetailPasien']);
    Route::get('general/get-produkdetail-general', [General\GeneralC::class, 'getProdukDetail']);
    Route::get('general/get-data-ruang-by-keluser', [General\GeneralC::class, 'getDataRuangByKelUser']);
    Route::get('general/get-data-combo-ruangan-part', [General\GeneralC::class, 'getDataComboRuanganGeneral']);
    Route::get('general/get-data-combo-pegawai-part', [General\GeneralC::class, 'getComboPegawaiGeneral']);
    Route::get('general/get-data-combo-jabatan-part', [General\GeneralC::class, 'getComboJabatanGeneral']);
    Route::get('general/get-data-pemakaian-asuransi-pasien', [General\GeneralC::class, 'getDataPemakaianAsuransi']);
    Route::get('general/get-data-combo-sensus', [General\GeneralC::class, 'getComboSensus']);
    Route::post('general/save-date-sensus-pasien', [General\GeneralC::class, 'saveDataStatusPulangPasien']);
    Route::get('general/get-data-combo-dokter', [General\GeneralC::class, 'getComboDokterPart2']);

    Route::get('humas/get-daftar-jadwal-dokter', [Humas\HumasC::class, 'getJadwalDokter']);
    Route::get('humas/get-survey', [Humas\HumasC::class, 'getSurvey']);

    Route::get('modul/get-menu', [Modul\ModulAplikasiC::class, 'getMenu']);
    Route::get('modul/get-modul-aplikasi', [Modul\ModulAplikasiC::class, 'getModulAplikasi']);

    Route::post('modul/simpan-objek-modul-aplikasi',  [Modul\ModulAplikasiC::class, 'objekModulAplikasi']);
    Route::post('modul/hapus-objek-modul-aplikasi',  [Modul\ModulAplikasiC::class, 'HapusObjekModulAplikasi']);
    Route::post('modul/save-modul-aplikasi',  [Modul\ModulAplikasiC::class, 'saveModulApp']);
    Route::post('modul/hapus-modul-aplikasi',  [Modul\ModulAplikasiC::class, 'hapusModulApp']);

    //** FARMASI */
    Route::get('farmasi/get-combo-farmasi', [Farmasi\FarmasiC::class, 'getDataComboFarmasi']);
    Route::get('farmasi/get-daftar-order', [Farmasi\FarmasiC::class, 'getDaftarOrderResep']);
    Route::get('farmasi/get-detail-order', [Farmasi\FarmasiC::class, 'getDetailOrderResep']);
    Route::get('farmasi/get-daftar-resep', [Farmasi\FarmasiC::class, 'getDaftarResep']);
    Route::get('farmasi/get-combo-detailjenisproduk', [Farmasi\FarmasiC::class, 'getDataComboJenisProduk']);
    Route::get('farmasi/get-combo-resep', [Farmasi\FarmasiC::class, 'getDataComboResep']);
    Route::get('farmasi/get-produk-resep', [Farmasi\FarmasiC::class, 'getProdukResep']);
    Route::get('farmasi/get-jenis-obat', [Farmasi\FarmasiC::class, 'getJenisObat']);
    Route::get('farmasi/get-detail-resep', [Farmasi\FarmasiC::class, 'getDetailResep']);
    Route::get('farmasi/get-nostruk-kasir', [Farmasi\FarmasiC::class, 'getNoStrukKasir']);
    Route::get('farmasi/get-transaksi-pelayanan', [Farmasi\FarmasiC::class, 'getTransaksiPelayananApotik']);
    Route::get('farmasi/get-daftar-retur-obat', [Farmasi\FarmasiC::class, 'getDaftarReturObat']);
    Route::get('farmasi/get-daftar-resep-nonlayanan', [Farmasi\FarmasiC::class, 'getDaftarPenjualanBebas']);
    Route::get('farmasi/get-detail-obat-bebas', [Farmasi\FarmasiC::class, 'getDetailResepBebas']);
    Route::get('farmasi/get-combo-resep-rev', [Farmasi\FarmasiC::class, 'getDataComboResepRev']);
    Route::get('farmasi/get-noreservasi', [Farmasi\FarmasiC::class, 'getNoreservasi']);
    

    Route::post('farmasi/save-status-resepelektonik', [Farmasi\FarmasiC::class, 'saveStatusResepElektronik']);
    Route::post('farmasi/save-pelayananobat', [Farmasi\FarmasiC::class, 'SimpanPelayananResep']);
    Route::post('farmasi/save-hapus-pelayananobat', [Farmasi\FarmasiC::class, 'DeletePelayananObat']);
    Route::post('farmasi/save-hapus-resep-nonlayanan', [Farmasi\FarmasiC::class, 'DeleteResepNonLayanan']);
    Route::post('farmasi/save-resep-nonlayanan', [Farmasi\FarmasiC::class, 'saveInputResepNonLayanan']);
    Route::post('farmasi/save-retur-obat-non-layanan', [Farmasi\FarmasiC::class, 'saveReturResepNonLayanan']);
    //** END FARMASI */

    //** START KASIR */
    //** GET */
    Route::get('kasir/get-combo-kasir', [Kasir\KasirC::class, 'getDataComboKasir']);
    Route::get('kasir/daftar-pasien-pulang', [Kasir\KasirC::class, 'daftarPasienPulang']);
    Route::get('kasir/get-struk-pelayanan/{noRegister}', [Kasir\KasirC::class, 'getStrukPelayanan']);
    Route::get('kasir/get-detail-registrasi-pasien', [Kasir\KasirC::class, 'getDetailRegisrtasiPasien']);
    Route::get('kasir/get-status-verif-piutang', [Kasir\KasirC::class, 'getStatusVerifPiutang']);
    Route::get('kasir/get-data-login', [Kasir\KasirC::class, 'getLogin']);
    Route::get('kasir/get-data-verifikasi-tagihan', [Kasir\KasirC::class, 'getDataVerifikasiTagihan']);
    Route::get('kasir/get-detail-verifikasi-tagihan', [Kasir\KasirC::class, 'detailTagihanVerifikasi']);
    Route::get('kasir/get-data-detail-verifikasi-tagihan', [Kasir\KasirC::class, 'getDataDetailVerifikasi']);
    Route::get('kasir/detail-tagihan-pasien-layanan', [Kasir\KasirC::class, 'detailTagihanPasienLayanan']);
    Route::get('kasir/get-data-pembayaran', [Kasir\KasirC::class, 'getDataPembayaran']);
    Route::get('kasir/data-penerimaan-pembayaran', [Kasir\KasirC::class, 'getDataPenerimaanPembayaran']);
    Route::get('kasir/get-ruangan-registrasi', [Kasir\KasirC::class, 'getListRuangan']);
    Route::get('kasir/detail-tagihan/{noRegister}', [Kasir\KasirC::class, 'detailTagihan']);
    Route::get('kasir/get-petugasbypelayananpasien', [Kasir\KasirC::class, 'getPelPetugasByPelPasien']);
    Route::get('kasir/get-komponenharga-pelayanan', [Kasir\KasirC::class, 'getKomponenHargaPelayanan']);
    Route::get('kasir/detail-pasien-deposit/{norec_pd}', [Kasir\KasirC::class, 'detailPasienDeposit']);
    Route::get('kasir/daftar-piutang-pasien', [Kasir\KasirC::class, 'daftarPiutangPasien']);
    Route::get('kasir/daftar-piutang-layanan', [Kasir\KasirC::class, 'getDaftarTagihanPiutang']);
    Route::get('kasir/detail-piutang-pasien/{norec}', [Kasir\KasirC::class, 'detailPiutangPasien']);
    Route::get('kasir/daftar-tagihan-non-layanan', [Kasir\KasirC::class, 'daftarTagihanNonLayanan']);
    Route::get('kasir/detail-tagihan-non-layanan', [Kasir\KasirC::class, 'detailTagihanNonLayanan']);
    Route::get('kasir/get-detail-transaksi-nonlayanan', [Kasir\KasirC::class, 'getDetailTransaksiNonLayanan']);
    Route::get('kasir/get-data-tagihan-pasien', [Kasir\KasirC::class, 'daftarTagihanPasien']);
    Route::get('kasir/get-data-laporan-penerimaan-kasir', [Kasir\KasirC::class, 'getDataLaporanKasir']);
    Route::get('kasir/get-data-combo-lapkasir', [Kasir\KasirC::class, 'getDataComboLapKasir']);
    //** END GET */

    //** POST */
    Route::post('kasir/save-antrian-konsultasi', [Kasir\KasirC::class, 'simpanAntrianKonsultasi']);
    Route::post('kasir/save-dokter-antrian', [Kasir\KasirC::class, 'simpanUpdateDokterAntrian']);
    Route::post('kasir/save-ubah-rekanan', [Kasir\KasirC::class, 'simpanUpdateRekananPD']);
    Route::post('kasir/save-ubah-tanggal', [Kasir\KasirC::class, 'ubahTanggalDetailRegis']);
    Route::post('kasir/save-ubah-tanggal', [Kasir\KasirC::class, 'hapusAntrianPasien']);
    Route::post('kasir/save-batal-pulang', [Kasir\KasirC::class, 'HapusTglPulang']);  //do
    Route::post('kasir/simpan-verifikasi-tagihan-tatarekening', [Kasir\KasirC::class, 'simpanVerifikasiTagihanTatarekening']); // do
    Route::post('kasir/batal-verifikasi-tagihan-tatarekening', [Kasir\KasirC::class, 'batalVerifikasiTagihan']); // do // do
    Route::post('kasir/closing-pemeriksaan-pasien', [Kasir\KasirC::class, 'closePemeriksaanPD']); // do
    Route::post('kasir/simpan-data-pembayaran', [Kasir\KasirC::class, 'simpanPembayaran']); // do
    Route::post('kasir/save-batal-bayar', [Kasir\KasirC::class, 'simpanPembatalanPembayaran']); // do
    Route::post('kasir/save-ubah-carabayar', [Kasir\KasirC::class, 'UbahCaraBayar']); // do
    Route::post('kasir/delete-pelayanan-pasien', [Kasir\KasirC::class, 'deletePelayananPasien']);
    Route::post('kasir/hapus-ppasienpetugas', [Kasir\KasirC::class, 'hapusPPP']);
    Route::post('kasir/save-ppasienpetugas', [Kasir\KasirC::class, 'simpanDokterPPP']);
    Route::post('kasir/save-update-harga-diskon-komponen', [Kasir\KasirC::class, 'simpanUpdateDiskonKomponen']);
    Route::post('kasir/save-update-tanggal_pelayanan', [Kasir\KasirC::class, 'simpanUpdateTglPelayanan']);
    Route::post('kasir/verify-piutang-pasien', [Kasir\KasirC::class, 'verifyPiutangPasien']);
    Route::post('kasir/cancel-verify-piutang-pasien', [Kasir\KasirC::class, 'cancelVerifyPiutangPasien']);
    Route::post('kasir/save-input-non-layanan', [Kasir\KasirC::class, 'SaveInputTagihan']);
    Route::post('kasir/hapus-transaksi-non-layanan', [Kasir\KasirC::class, 'BatalInputTagihanNonLayanan']);
    //** END POST */
    //** END KASIR */

    Route::get('kiosk/get-ruanganbykode/{kode}', [KiosK\KiosKC::class, 'getRuanganByKodeInternal']);
    Route::get('kiosk/get-diagnosabykode/{kode}', [KiosK\KiosKC::class, 'getDiagnosaByKode']);
    Route::get('kiosk/get-view-bed-tea', [KiosK\KiosKC::class, 'getKetersediaanTempatTidurView']);
    Route::get('kiosk/get-view-bed', [KiosK\KiosKC::class, 'viewBed']);
    Route::get('kiosk/get-combo', [KiosK\KiosKC::class, 'getDataCombo']);
    Route::get('kiosk/get-tarif', [KiosK\KiosKC::class, 'getDaftarTarif']);
    Route::get('kiosk/get-combo-dokter-temp', [KiosK\KiosKC::class, 'getComboDokterKios']);
    Route::get('kiosk/get-combo-setting', [KiosK\KiosKC::class, 'getComboSettingKios']);
    Route::get('kiosk/get-ruangan', [KiosK\KiosKC::class, 'getComboRuanganKios']);
    Route::get('kiosk/get-slotting-kiosk', [KiosK\KiosKC::class, 'getSlottingKios']);
    Route::get('kiosk/get-slotting-kosong', [KiosK\KiosKC::class, 'getSlottingKosong']);
    Route::get('kiosk/get-list-loket', [KiosK\KiosKC::class, 'getListLoket']);
    Route::get('kiosk/get-dokter-internal', [KiosK\KiosKC::class, 'getDokterInternal']);
    Route::get('kiosk/get-combo-kiosk2', [KiosK\KiosKC::class, 'getComboKios2']);
    Route::get('kiosk/get-daftar-jadwal-dokter', [KiosK\KiosKC::class, 'getJadwalDokter']);
    Route::get('kiosk/get-data-ruangan',  [KiosK\KiosKC::class, 'getDataRuangan']);

    Route::post('kiosk/save-antrian', [KiosK\KiosKC::class, 'saveAntrianTouchscreen']);
    Route::post('kiosk/save-survey', [KiosK\KiosKC::class, 'saveSurvey']);
    Route::post('kiosk/save-slotting-kiosk', [KiosK\KiosKC::class, 'saveSlottingKios']);
    Route::post('kiosk/delete-slotting-kiosk', [KiosK\KiosKC::class, 'deleteSlotting']);
    Route::post('kiosk/save-keluhan-pelanggan',[KiosK\KiosKC::class, 'SaveKeluhanPelanggan']);

    //** LAPORAN */
    Route::get('laporan/get-data-lap-kunjungan', [Laporan\LaporanC::class, 'getDataLaporanKunjungan']);
    Route::get('laporan/get-data-combo', [Laporan\LaporanC::class, 'getDataComboLaporan']);
    Route::get('laporan/get-data-lap-sensus-pasien', [Laporan\LaporanC::class, 'getDataLaporanSensusPasien']);
    Route::get('laporan/get-data-lap-sensus-harian-igd', [Laporan\LaporanC::class, 'getDataLaporanSensusHarianIGD']);
    Route::get('laporan/get-data-lap-sensus-bulanan-igd', [Laporan\LaporanC::class, 'getDataLaporanSensusBulananIGD']);
    //** END LAPORAN */

    //** LOGISTIK */
    Route::get('logistik/get-combo-detailjenisproduk', [Logistik\LogistikC::class, 'getDataComboJenisProduk']);
    Route::get('logistik/get-combo-logistik', [Logistik\LogistikC::class, 'getDataComboLogistik']);
    Route::get('logistik/get-stok-ruangan-detail', [Logistik\LogistikC::class, 'getDataStokRuanganDetail']);
    Route::get('logistik/get-data-order-barang-ruangan', [Logistik\LogistikC::class, 'getDaftarOrderBarang']);
    Route::get('logistik/get-daftar-distribusi-barang', [Logistik\LogistikC::class, 'getDaftarDistribusiBarang']);
    Route::get('logistik/get-combo-distribusi', [Logistik\LogistikC::class, 'getDataComboDistribusi']);
    Route::get('logistik/get-produk-distribusi', [Logistik\LogistikC::class, 'getProdukDistribusi']);
    Route::get('logistik/get-detail-order-barang-ruangan', [Logistik\LogistikC::class, 'getDetailOrderBarang']);
    Route::get('logistik/get-stok-ruangan-so', [Logistik\LogistikC::class, 'getStokRuanganSO']);
    Route::get('logistik/get-daftar-so', [Logistik\LogistikC::class, 'getDaftarStokRuanganSO']);
    Route::get('logistik/get-detail-kirim-barang-ruangan', [Logistik\LogistikC::class, 'getDetailKirimBarang']);
    Route::get('logistik/get-combo-penerimaan', [Logistik\LogistikC::class, 'getDataComboPenerimaan']);
    Route::get('logistik/get-combo-produk-penerimaan', [Logistik\LogistikC::class, 'getProdukPenerimaan']);
    Route::get('logistik/get-daftar-penerimaan', [Logistik\LogistikC::class, 'getDaftarPenerimaanSuplier']);
    Route::get('logistik/get-detail-penerimaan', [Logistik\LogistikC::class, 'getDetailPenerimaanBarang']);

    Route::post('logistik/update-tglkadaluarsa', [Logistik\LogistikC::class, 'updateBarangKadaluarsa']);
    Route::post('logistik/save-adjusment-stok', [Logistik\LogistikC::class, 'saveAdjustmentStok']);
    Route::post('logistik/save-order-barang-ruangan', [Logistik\LogistikC::class, 'saveOrderBarang']);
    Route::post('logistik/delete-order-barang-ruangan', [Logistik\LogistikC::class, 'saveBatalOrderBarang']);
    Route::post('logistik/save-data-stock-opname', [Logistik\LogistikC::class, 'saveStockOpname']);
    Route::post('logistik/get-stok-ruangan-so-from-fileexcel', [Logistik\LogistikC::class, 'getStokRuanganSOFromFileExcel']);
    Route::post('logistik/save-kirim-barang-ruangan', [Logistik\LogistikC::class, 'saveKirimBarangRuangan']);
    Route::post('logistik/batal-kirim-barang-ruangan', [Logistik\LogistikC::class, 'BatalKirimBarang']);
    Route::post('logistik/save-penerimaan-barang-supplier', [Logistik\LogistikC::class, 'saveTerimaBarangSuplier']);
    Route::post('logistik/delete-data-penerimaan', [Logistik\LogistikC::class, 'DeletePenerimaanBarangSupplier']);
    //** LOGISTIK */

    //** PENUNJANG */
    Route::get('penunjang/get-data-combo-penunjang', [Penunjang\PenunjangC::class, 'getDataComboPenunjang']);
    Route::get('penunjang/get-data-ruang-tujuan', [Penunjang\PenunjangC::class, 'getDataRuangTujuanPenunjang']);
    Route::get('penunjang/get-daftar-order', [Penunjang\PenunjangC::class, 'getDaftarOrderPenunjang']);
    Route::get('penunjang/get-diagnosapasienbynoreg', [Penunjang\PenunjangC::class, 'getDiagnosaRad']);
    Route::get('penunjang/get-order-pelayanan', [Penunjang\PenunjangC::class, 'getOrderPelayanan']);
    Route::get('penunjang/get-detail-verifikasi', [Penunjang\PenunjangC::class, 'getDetailVerifLabRad']);
    Route::get('penunjang/get-detail-pasien', [Penunjang\PenunjangC::class, 'getDetailPasienPenunjang']);
    Route::get('penunjang/get-daftar-pasien-penunjang', [Penunjang\PenunjangC::class, 'getDaftarPasienPenunjang']);
    Route::get('penunjang/get-rincian-pelayanan', [Penunjang\PenunjangC::class, 'getRincianPelayanan']);
    Route::get('penunjang/get-data-apd', [Penunjang\PenunjangC::class, 'getDataRuanganAntrian']);
    Route::get('penunjang/get-hasil-radiologi', [Penunjang\PenunjangC::class, 'getHasilRadiologi']);
    Route::get('penunjang/get-hasil-lab-pa', [Penunjang\PenunjangC::class, 'getHasilLabPA']);
    Route::get('penunjang/get-hasil-lab-manual',  [Penunjang\PenunjangC::class, 'getHasilLabManual']);
    Route::get('penunjang/get-for-update-nilainormal',  [Penunjang\PenunjangC::class, 'getNilaiNormalMas']);

    Route::get('penunjang/get-hasil-lab-manual-rev',  [Penunjang\PenunjangC::class, 'getHasilLabManualRev']);
    Route::get('penunjang/get-for-update-nilainormal-rev',  [Penunjang\PenunjangC::class, 'getNilaiNormalRev']);

    Route::post('penunjang/save-pelayanan-pasien', [Penunjang\PenunjangC::class, 'savePelayananPasien']);
    Route::post('penunjang/update-gol-darah', [Penunjang\PenunjangC::class, 'updateGolonganDarah']);
    Route::post('penunjang/save-antrian-penunjang', [Penunjang\PenunjangC::class, 'saveAntrianPasienPenunjang']);
    Route::post('penunjang/save-hasil-radiologi', [Penunjang\PenunjangC::class, 'saveHasilRadiologi']);
    Route::post('penunjang/save-hasil-lab-pa', [Penunjang\PenunjangC::class, 'saveHasilLabPA']);
    Route::post('penunjang/save-hasil-lab-manual', [Penunjang\PenunjangC::class, 'saveHasilLabManual']);
    Route::post('penunjang/save-update-nilainormal', [Penunjang\PenunjangC::class, 'saveUpdateNilaiNormal']);
    Route::post('penunjang/save-update-nilainormal-rev', [Penunjang\PenunjangC::class, 'saveUpdateNilaiNormalRev']);
    //** END PENUNJANG */

    Route::get('rawatinap/get-pasien-bynorec', [RawatInap\RawatInapC::class, 'getPindahPasienByNoreg2']);
    Route::get('rawatinap/get-combo-pindahpasien', [RawatInap\RawatInapC::class, 'getComboPindahPulang']);
    Route::get('rawatinap/get-kamar-ruangan-ibu', [RawatInap\RawatInapC::class, 'getKamarIbuLast']);
    Route::get('rawatinap/get-daftar-pasien-masih-dirawat', [RawatInap\RawatInapC::class, 'getPasienMasihDirawat']);
    Route::get('rawatinap/get-kamarbyruangankelas', [RawatInap\RawatInapC::class, 'getKamarByKelasRuangan']);
    Route::get('rawatinap/get-nobedbykamar', [RawatInap\RawatInapC::class, 'getNoBedByKamar']);
    Route::get('rawatinap/get-combo', [RawatInap\RawatInapC::class, 'getComboRI']);
    Route::get('rawatinap/get-daftar-antrian-ranap', [RawatInap\RawatInapC::class, 'getDaftarRegistrasiDokterRanap']);
    Route::get('rawatinap/get-combo-gizi', [RawatInap\RawatInapC::class, 'getDataComboBoxGizi']);
    Route::get('rawatinap/get-daftar-order-detail', [RawatInap\RawatInapC::class, 'getDaftarOrderGiziDetail']);
    Route::get('rawatinap/get-daftar-order-gizi', [RawatInap\RawatInapC::class, 'getDaftarOrderGizi']);
    Route::get('rawatinap/get-siklus-menudiet',  [RawatInap\RawatInapC::class, 'getProdukMenu']);
    Route::get('rawatinap/get-produk-gizi',  [RawatInap\RawatInapC::class, 'getProdukGizi']);
    Route::get('rawatinap/get-daftar-kirim-gizi',  [RawatInap\RawatInapC::class, 'getDaftarKirimGizi']);

    Route::post('rawatinap/save-pulang-pasien', [RawatInap\RawatInapC::class, 'savePulangPasien']);
    Route::post('rawatinap/save-pindah-pasien', [RawatInap\RawatInapC::class, 'savePindahPasien']);
    Route::post('rawatinap/save-akomodasi-tea', [RawatInap\RawatInapC::class, 'saveAkomodasiOtomatis']);
    Route::post('rawatinap/save-batal-pindah-ruangan', [RawatInap\RawatInapC::class, 'saveBatalPindahRuangan']);
    Route::post('rawatinap/save-batal-rawat-inap', [RawatInap\RawatInapC::class, 'saveBatalRanap']);
    Route::post('rawatinap/update-kamar', [RawatInap\RawatInapC::class, 'updateKamar']);
    Route::post('rawatinap/save-order-gizi', [RawatInap\RawatInapC::class, 'saveOrderGizi']);
    Route::post('rawatinap/hapus-order-gizi', [RawatInap\RawatInapC::class, 'hapusOrderGzi']);
    Route::post('rawatinap/hapus-order-gizi-detail', [RawatInap\RawatInapC::class, 'hapusOrderGziD']);
    Route::post('rawatinap/save-kirimmenu-gizi', [RawatInap\RawatInapC::class, 'saveKirimMenuGizi']);
    Route::post('rawatinap/hapus-peritem-order-gizi', [RawatInap\RawatInapC::class, 'hapusOrderGiziItem']);
    Route::post('rawatinap/batal-kirim-gizi', [RawatInap\RawatInapC::class,'deleteKirimMenu']);

    Route::get('rawatjalan/get-daftar-antrian-rajal', [RawatJalan\RawatJalanC::class, 'getDaftarRegistrasiDokterRajal']);
    Route::post('rawatjalan/save-update-status-panggil', [RawatJalan\RawatJalanC::class, 'updateStatusPanggil']);

    Route::get('registrasi/get-pasien', [Registrasi\RegistrasiC::class, 'getDaftarPasien']);
    Route::get('registrasi/get-combo-registrasi', [Registrasi\RegistrasiC::class, 'getComboRegBaru']);
    Route::get('registrasi/get-combo-address', [Registrasi\RegistrasiC::class, 'getComboAddress']);
    Route::get('registrasi/get-desa-kelurahan-paging', [Registrasi\RegistrasiC::class, 'getDesaKelurahanPaging']);
    Route::get('registrasi/get-kecamatan-part', [Registrasi\RegistrasiC::class, 'getKecamatanPart']);
    Route::get('registrasi/get-alamat-bykodepos', [Registrasi\RegistrasiC::class, 'getAlamatByKodePos']);
    Route::get('registrasi/get-pasienbynocm', [Registrasi\RegistrasiC::class, 'getPasienByNoCm']);
    Route::get('registrasi/get-data-combo-new', [Registrasi\RegistrasiC::class, 'getDataComboNEW']);
    Route::get('registrasi/get-dokter-part', [Registrasi\RegistrasiC::class, 'getComboDokterPart']);
    Route::get('registrasi/get-penjaminbykelompokpasien', [Registrasi\RegistrasiC::class, 'getPenjaminByKelompokPasien']);
    Route::get('registrasi/get-kelasbyruangan', [Registrasi\RegistrasiC::class, 'getKelasByRuangan']);
    Route::get('registrasi/get-kamarbyruangankelas', [Registrasi\RegistrasiC::class, 'getKamarByKelasRuangan']);
    Route::get('registrasi/get-nobedbykamar', [Registrasi\RegistrasiC::class, 'getNoBedByKamar']);
    Route::get('registrasi/get-pasienbynorec-pd', [Registrasi\RegistrasiC::class, 'getPasienByNoRecPD']);
    Route::get('registrasi/get-setting-asuransi', [Registrasi\RegistrasiC::class, 'getSettingAsuransi']);
    Route::get('registrasi/get-combo-pemakaian-asuransi', [Registrasi\RegistrasiC::class, 'getDataComboAsuransiPasien']);
    Route::get('registrasi/get-pasien-bynorec', [Registrasi\RegistrasiC::class, 'getPasienByNoreg']);
    Route::get('registrasi/get-history-pemakaianasuransi-new', [Registrasi\RegistrasiC::class, 'getHistoryPemakaianAsuransiNew']);
    Route::get('registrasi/get-data-combo-operator', [Registrasi\RegistrasiC::class, 'getDataComboOperator']);
    Route::get('registrasi/get-daftar-registrasi-pasien', [Registrasi\RegistrasiC::class, 'getDaftarRegistrasiPasienOperator']);
    Route::get('registrasi/get-status-close', [Registrasi\RegistrasiC::class, 'getStatusClosePeriksa']);
    Route::get('registrasi/get-data-antrian-pasien', [Registrasi\RegistrasiC::class, 'getdataAntrianPasien']);
    Route::get('registrasi/cek-nobpjs', [Registrasi\RegistrasiC::class, 'cekNoBPJSpasienBaru']);
    Route::get('registrasi/get-diagnosa-saeutik', [Registrasi\RegistrasiC::class, 'getDiagnosaSaeutik']);
    Route::get('registrasi/get-apd', [Registrasi\RegistrasiC::class, 'getAntrianPasien']);
    Route::get('registrasi/get-bynocm', [Registrasi\RegistrasiC::class, 'getPsnByNoCm']);
    Route::get('registrasi/get-data-pasien-reservasi', [Registrasi\RegistrasiC::class, 'getPasienPerjanjian']);
    Route::get('registrasi/get-combo-perjanjian', [Registrasi\RegistrasiC::class, 'getComboPasienPerjanjian']);
    Route::get('registrasi/get-pasienonline-bynorec/{noreservasi}', [Registrasi\RegistrasiC::class, 'getPasienOnlineByNorec']);
    Route::post('registrasi/confirm-pasien-online', [Registrasi\RegistrasiC::class, 'ConfirmOnline']);
    Route::post('registrasi/save-dokter-registrasi', [Registrasi\RegistrasiC::class, 'simpanUpdateDokterRegistrasi']);
    Route::post('registrasi/save-registrasipasien', [Registrasi\RegistrasiC::class, 'saveRegistrasiPasien']);
    Route::post('registrasi/save-pasien', [Registrasi\RegistrasiC::class, 'savePasien']);
    Route::post('registrasi/save-adminsitrasi', [Registrasi\RegistrasiC::class, 'saveAdministrasi']);
    Route::post('registrasi/save-asuransipasien', [Registrasi\RegistrasiC::class, 'saveAsuransiPasien']);
    Route::get('registrasi/get-data-diagnosaicdx-pasien', [Registrasi\RegistrasiC::class, 'getDiagnosaICDPasien']);
    Route::post('registrasi/hapus-diagnosa-pasien', [Registrasi\RegistrasiC::class, 'deleteDiagnosaPasien']);
    Route::post('registrasi/save-diagnosa-pasien-icdx', [Registrasi\RegistrasiC::class, 'saveDiagnosaPasienICDX']);
    Route::post('registrasi/save-diagnosa-pasien-icdix', [Registrasi\RegistrasiC::class, 'saveDiagnosaPasienICDIX']);
    Route::get('registrasi/get-daftar-mutasi', [Registrasi\RegistrasiC::class, 'getDaftarMutasi']);
    Route::get('registrasi/get-data-pasien-mau-batal', [Registrasi\RegistrasiC::class, 'getDataPasienMauBatal']);
    Route::get('registrasi/get-daftar-pasienbatal', [Registrasi\RegistrasiC::class, 'getDaftarPasienBatal']);
    Route::get('registrasi/daftar-riwayat-registrasi', [Registrasi\RegistrasiC::class, 'getDaftarRiwayatRegistrasi']);
    Route::get('registrasi/get-combo-klaim', [Registrasi\RegistrasiC::class, 'getComboEKlaim']);
    Route::post('registrasi/save-registrasipasien-cek', [Registrasi\RegistrasiC::class, 'saveRegistrasiPasien'])->name("CheckInAntrean");

    Route::get('registrasi/laporan/get-laporan-rl12',  [Registrasi\RegistrasiC::class, 'getDataRL12']);
    Route::get('registrasi/laporan/get-laporan-rl13',  [Registrasi\RegistrasiC::class, 'getDataRL13']);

    Route::post('registrasi/save-batal-registrasi', [Registrasi\RegistrasiC::class, 'SimpanBatalPeriksa']);
    Route::post('registrasi/simpan-mutasi-pasien', [Registrasi\RegistrasiC::class, 'saveMutasiPasien']);
    Route::post('registrasi/update-sep-igd', [Registrasi\RegistrasiC::class, 'updateSEPIGD']);
    Route::post('registrasi/simpan-pasien-triase', [Registrasi\RegistrasiC::class, 'SimpanPasienTriase']);
    Route::post('registrasi/update-false-pasien', [Registrasi\RegistrasiC::class, 'updateStatusEnabledPasien']);

    Route::post('reservasionline/update-nocmfk-antrian-registrasi', [Reservasi\ReservasiC::class, 'updateNoCmInAntrianRegistrasi']);
    Route::get('reservasionline/get-list-data', [Reservasi\ReservasiC::class, 'getComboReservasi']);
    Route::get('reservasionline/get-daftar-slotting', [Reservasi\ReservasiC::class, 'getDaftarSlotting']);
    Route::post('reservasionline/save-slotting', [Reservasi\ReservasiC::class, 'saveSlotting']);
    Route::post('reservasionline/update-data-status-reservasi', [Reservasi\ReservasiC::class, 'UpdateStatConfirm']);
    Route::post('reservasionline/update-nocmfk-antrian-registrasi', [Reservasi\ReservasiC::class, 'updateNoCmInAntrianRegistrasi']);
    Route::get('reservasionline/get-history', [Reservasi\ReservasiC::class, 'getHistoryReservasi']);
    Route::get('reservasionline/get-pasien/{nocm}/{tgllahir}', [Reservasi\ReservasiC::class, 'getPasienByNoCmTglLahir']);
    Route::get('reservasionline/get-libur', [Reservasi\ReservasiC::class, 'getLiburSlotting']);
    Route::get('reservasionline/get-bank-account', [Reservasi\ReservasiC::class, 'getNomorRekening']);
    Route::post('reservasionline/save', [Reservasi\ReservasiC::class, 'saveReservasi']);
    Route::post('reservasionline/delete', [Reservasi\ReservasiC::class, 'deleteReservasi']);
    Route::get('reservasionline/cek-reservasi-satu', [Reservasi\ReservasiC::class, 'cekReservasiDipoliYangSama']);
    Route::get('reservasionline/get-slotting-by-ruangan-new/{kode}/{tgl}', [Reservasi\ReservasiC::class, 'getSlottingByRuanganNew']);
    Route::get('reservasionline/get-slot-available', [Reservasi\ReservasiC::class, 'getDaftarSlottingAktif']);
    Route::get('reservasionline/tagihan/get-pasien/{noregistrasi}', [Reservasi\ReservasiC::class, 'getPasienByNoRegistrasi']); //done
    Route::get('reservasionline/get-tagihan-pasien/{noregistasi}', [Reservasi\ReservasiC::class, 'getTagihanEbilling']);
    Route::get('reservasionline/get-setting', [Reservasi\ReservasiC::class, 'getSetting']);
    Route::get('reservasionline/daftar-riwayat-registrasi', [Reservasi\ReservasiC::class, 'getDaftarRiwayatRegistrasi']);
    Route::get('reservasionline/cek-pasien-baru-by-nik/{nik}', [Reservasi\ReservasiC::class, 'cekPasienByNik']);
    Route::post('reservasionline/save-libur', [Reservasi\ReservasiC::class, 'saveLibur']);
    Route::post('reservasionline/delete-libur', [Reservasi\ReservasiC::class, 'deleteLibur']);

    Route::get('sysadmin/general/get-terbilang/{number}', [SysAdmin\GeneralC::class, 'getTerbilangGeneral']);
    Route::get('sysadmin/general/get-status-close/{noregistrasi}', [SysAdmin\GeneralC::class, 'getStatusClosePeriksa']);
    Route::get('sysadmin/general/get-tindakan-with-details', [SysAdmin\GeneralC::class, 'getTindakanWithDetail']);
    Route::get('sysadmin/general/get-siklus-gizi', [SysAdmin\GeneralC::class, 'getSiklusGizi']);
    Route::get('sysadmin/general/get-combo-gizi', [SysAdmin\GeneralC::class, 'getComboGizi']);
    Route::get('sysadmin/general/get-daftar-user', [SysAdmin\GeneralC::class, 'getDaftarUser']);
    Route::get('sysadmin/general/get-pegawai-part', [SysAdmin\GeneralC::class, 'getPegawaiPart']);
    Route::get('sysadmin/general/get-combo-user', [SysAdmin\GeneralC::class, 'getComboUser']);
    Route::get('sysadmin/general/get-objek-modul-aplikasi', [SysAdmin\GeneralC::class, 'getObjekModulAplikasiStandar']);
    Route::get('sysadmin/general/get-map-loginruangan', [SysAdmin\GeneralC::class, 'getMapUserToRuangan']);
    Route::get('sysadmin/general/get-map-loginmodul', [SysAdmin\GeneralC::class, 'getMapUserToModulAp']);
    Route::get('sysadmin/general/get-map-ruangan-produk', [SysAdmin\GeneralC::class, 'getMapRuanganToProduk']);
    Route::get('sysadmin/general/get-map-ruangan-combo', [SysAdmin\GeneralC::class, 'getMapRuCombo']);
    Route::get('sysadmin/general/get-combo-jadwal-dokter', [SysAdmin\GeneralC::class, 'getComboJadwalDokter']);
    Route::get('sysadmin/general/get-data-informasi-jadwal-dokter', [SysAdmin\GeneralC::class, 'getJadwalDokter']);
    Route::get('sysadmin/general/get-data-combo-informasi', [SysAdmin\GeneralC::class, 'getComboInformasi']);
    Route::get('sysadmin/general/get-data-produk-general', [SysAdmin\GeneralC::class, 'getProdukGeneralPart']);
    Route::get('sysadmin/general/get-data-tarif-layanan', [SysAdmin\GeneralC::class, 'getDaftarTarif']);
    Route::get('sysadmin/general/get-master-data', [SysAdmin\GeneralC::class, 'getMasterData']);
    Route::get('sysadmin/general/get-map-administrasi-combo', [General\GeneralC::class, 'getComboAdmin']);
    Route::get('sysadmin/general/get-combo-administrasi', [General\GeneralC::class, 'getComboProdukAdministrasi']);
    Route::post('sysadmin/general/save-map-administrasi-otomatis', [General\GeneralC::class, 'saveMappingAdminstrasi']);
    Route::get('sysadmin/general/get-data-profil-rs', [General\GeneralC::class, 'getDataProfil']);
    Route::post('sysadmin/general/delete-profil-rs', [General\GeneralC::class, 'deleteProfilRS']);
    Route::post('sysadmin/general/save-profil-rs',  [General\GeneralC::class, 'saveDataProfilRs']);
    Route::get('sysadmin/general/get-data-pegawai-rs', [General\GeneralC::class, 'getDataPegawaiRs']);
    Route::post('sysadmin/general/delete-pegawai-rs', [General\GeneralC::class, 'deletePegawaiRS']);
    Route::get('sysadmin/general/get-data-combo-pegawai-rs', [General\GeneralC::class, 'getComboPegawaiRs']);
    Route::post('sysadmin/general/save-pegawai-rs', [General\GeneralC::class, 'saveDataPegawaiRs']);
    Route::get('sysadmin/general/get-map-paket', [General\GeneralC::class, 'getMapPaket']);
    Route::get('sysadmin/general/get-paket', [General\GeneralC::class, 'getPaket']);
    Route::post('sysadmin/general/delete-map-paket', [General\GeneralC::class, 'deleteMapPaket']);
    Route::post('sysadmin/general/save-map-paket',  [General\GeneralC::class, 'saveMapPaket']);
    Route::post('sysadmin/general/save-paket',  [General\GeneralC::class, 'savePaket']);
    Route::get('sysadmin/general/get-sk', [General\GeneralC::class, 'getSK']);
    Route::post('sysadmin/general/save-sk',  [General\GeneralC::class, 'saveSK']);
    Route::get('sysadmin/general/get-akomodasi', [General\GeneralC::class, 'getDataAkomodasi']);
    Route::get('sysadmin/general/get-combo-akomodasi', [General\GeneralC::class, 'getComboProdukAkomodasi']);
    Route::post('sysadmin/general/save-map-akomodasi-otomatis', [General\GeneralC::class, 'saveMappingAkomodasi']);

    Route::post('sysadmin/general/save-master-data', [SysAdmin\GeneralC::class, 'saveMaster']);
    Route::post('sysadmin/general/save-new-user', [SysAdmin\GeneralC::class, 'saveNewUser']);
    Route::post('sysadmin/general/delete-new-user', [SysAdmin\GeneralC::class, 'deleteNewUser']);
    Route::post('sysadmin/general/save-siklus-gizi',  [SysAdmin\GeneralC::class, 'saveSiklusGizi']);
    Route::post('sysadmin/general/delete-siklus-gizi',  [SysAdmin\GeneralC::class, 'deleteSiklusGizi']);
    Route::post('sysadmin/general/save-map-login-to-ruangan',  [SysAdmin\GeneralC::class, 'saveLoginUserToRuangan']);
    Route::post('sysadmin/general/save-map-login-to-modul',  [SysAdmin\GeneralC::class, 'saveMapUserToModul']);
    Route::post('sysadmin/general/delete-map-ruangan-produk',  [SysAdmin\GeneralC::class, 'deleteMapRuanganProduk']);
    Route::post('sysadmin/general/save-map-ruangan-produk',  [SysAdmin\GeneralC::class, 'saveMapRuanganProduk']);
    Route::get('sysadmin/general/get-master-pelayanan', [SysAdmin\GeneralC::class, 'getListProduk']);
    Route::post('sysadmin/general/delete-master-pelayanan', [SysAdmin\GeneralC::class, 'deleteMasterPelayanan']);
    Route::get('sysadmin/general/get-combo-master', [SysAdmin\GeneralC::class, 'getComboMaster']);
    Route::get('sysadmin/general/get-combo-detailjenisproduk', [SysAdmin\GeneralC::class, 'getDataComboJenisProduk']);
    Route::post('sysadmin/general/save-master-pelayanan', [SysAdmin\GeneralC::class, 'saveMasterPelayanan']);
    Route::post('sysadmin/general/save-informasi-jadwal-dokter', [SysAdmin\GeneralC::class, 'saveInformasiDokter']);
    Route::post('sysadmin/general/hapus-informasi-jadwal-dokter', [SysAdmin\GeneralC::class, 'deleteInformasiDokter']);

    Route::get('sysadmin/general/get-harga-pelayanan', [SysAdmin\GeneralC::class, 'getHargaPelayanan']);
    Route::get('sysadmin/general/get-combo-pelayanan', [SysAdmin\GeneralC::class, 'getComboTarif']);
    Route::get('sysadmin/general/get-produk', [SysAdmin\GeneralC::class, 'getProduks']);

    Route::post('sysadmin/general/save-harga-pelayanan', [SysAdmin\GeneralC::class, 'saveHargaPelayanan']);
    Route::post('sysadmin/general/hapus-harga-pelayanan', [SysAdmin\GeneralC::class, 'hapusHargaPelayanan']);
    Route::get('sysadmin/general/get-kelmpok-produk', [SysAdmin\GeneralC::class, 'getKelompokProd']);
    Route::get('sysadmin/general/get-master-data', [SysAdmin\GeneralC::class, 'getMasterData']);
    Route::get('sysadmin/general/get-comobo-ruru', [SysAdmin\GeneralC::class, 'getComboRuru']);    
    Route::get('sysadmin/general/get-map-ruangan-combo2', [SysAdmin\GeneralC::class, 'getMapRuCombo2']);
    Route::get('sysadmin/general/get-map-ruangan-kelas', [SysAdmin\GeneralC::class, 'getMapRuanganToKelas']);
    Route::post('sysadmin/general/delete-map-ruangan-kelas',  [SysAdmin\GeneralC::class, 'deleteMapRutoKelas']);
    Route::post('sysadmin/general/save-map-ruangan-kelas',  [SysAdmin\GeneralC::class, 'saveMapRutoKelas']);
    Route::post('sysadmin/general/save-map-rekanan-tipepasien',  [SysAdmin\GeneralC::class, 'saveMapRekananToTipePasien']);
    Route::post('sysadmin/general/delete-map-rekanan-tipepasien',  [SysAdmin\GeneralC::class, 'deleteMapRekananToTipePasien']);
    Route::get('sysadmin/general/get-combo-konversi', [SysAdmin\GeneralC::class, 'getComboKonversi']);
    Route::get('sysadmin/general/get-data-produk-konversi', [SysAdmin\GeneralC::class, 'getDataProdukKonversi']);    
    Route::get('sysadmin/general/get-data-satuan-konversi', [SysAdmin\GeneralC::class, 'getDataSatuanKonversi']);
    Route::post('sysadmin/general/save-konversi-satuan', [SysAdmin\GeneralC::class, 'SaveKonversiSatuan']);
    Route::post('sysadmin/general/hapus-konversi-satuan', [SysAdmin\GeneralC::class, 'hapusKonversiSatuan']);
    Route::get('sysadmin/general/get-combo-master-rekanan', [SysAdmin\GeneralC::class, 'getComboMasterRekanan']);
    Route::get('sysadmin/general/get-map-rekanan-combo', [SysAdmin\GeneralC::class, 'getMapRknToTp']);
    Route::get('sysadmin/general/get-map-rekanan-tipepasien', [SysAdmin\GeneralC::class, 'getMapRekananToTipePasien']);


    Route::get('sysadmin/settingdatafixed/get/{namaField}', [SysAdmin\SettingDataFixedC::class, 'getSettingDataFixedGeneric']);
    Route::get('sysadmin/settingdatafixed/get-kelompok-setting',  [SysAdmin\SettingDataFixedC::class,'getKelompokSettingDataFix']);
    Route::get('sysadmin/settingdatafixed/get-setting-detail', [SysAdmin\SettingDataFixedC::class,'getSettingDetail']);
    Route::post('sysadmin/settingdatafixed/update-setting', [SysAdmin\SettingDataFixedC::class,'updateSettingDataFix']);
    Route::get('sysadmin/settingdatafixed/get-setting-combo', [SysAdmin\SettingDataFixedC::class,'getComboPart']);
    Route::get('sysadmin/settingdatafixed/get-field-table',  [SysAdmin\SettingDataFixedC::class,'getFieldTable']);
    Route::get('sysadmin/settingdatafixed/get-table',  [SysAdmin\SettingDataFixedC::class,'getTable']);
    Route::get('sysadmin/settingdatafixed/get-data-from-table', [SysAdmin\SettingDataFixedC::class,'getDataFromTable']);
    Route::post('sysadmin/settingdatafixed/post-settingdatafixe', [SysAdmin\SettingDataFixedC::class,'SaveSettingDataFixed']);

    Route::get('sysadmin/logging/save-log-all', [SysAdmin\LoggingC::class, 'saveLoggingAll']);

    Route::get('tindakan/get-tindakan', [Kasir\TindakanC::class, 'getTindakanPart']);
    Route::get('tindakan/get-combo', [Kasir\TindakanC::class, 'getCombo']);
    Route::get('tindakan/get-komponenharga', [Kasir\TindakanC::class, 'getKomponenHarga']);
    Route::get('tindakan/get-pegawaibyjenispetugas', [Kasir\TindakanC::class, 'getPegawaiByJenisPetugasPe']);

    Route::post('tindakan/save-tindakan', [Kasir\TindakanC::class, 'saveTindakan']);


    Route::post('LapV2/PasienMasuk/{method}', [Bridging\RSOnlineC::class, 'PasienMasuk']);
    Route::post('LapV2/PasienDirawatKomorbid/{method}', [Bridging\RSOnlineC::class, 'Komorbid']);
    Route::post('LapV2/PasienDirawatTanpaKomorbid/{method}', [Bridging\RSOnlineC::class, 'NonKomorbid']);
    Route::post('LapV2/PasienKeluar/{method}', [Bridging\RSOnlineC::class, 'PasienKeluar']);

    Route::post('Referensi/usia_meninggal_probable',[Bridging\RSOnlineC::class, 'getRefUsia']);
    Route::post('Referensi/tempat_tidur',[Bridging\RSOnlineC::class, 'getRefTT']);
    Route::post('Fasyankes/{method}',[Bridging\RSOnlineC::class, 'Fasyankes']);

    Route::post('Referensi/kebutuhan_sdm', [Bridging\RSOnlineC::class, 'getReffSDM']);
    Route::post('Fasyankes/sdm/{method}', [Bridging\RSOnlineC::class, 'FasyankesSDM']);

    Route::post('Referensi/kebutuhan_apd', [Bridging\RSOnlineC::class, 'getReffAPD']);
    Route::post('Fasyankes/apd/{method}', [Bridging\RSOnlineC::class, 'FasyankesAPD']);

    Route::group(['prefix' => 'jkn'], function () {
        Route::get('get-token', [Auth\LoginC::class, 'getTokens']);
        Route::post('get-token', [Auth\LoginC::class, 'getTokens']);
        Route::post('get-no-antrean',[Reservasi\ReservasiC::class, 'GetNoAntrianMobileJKN']);
        Route::post('get-rekap-antrean',[Reservasi\ReservasiC::class, 'GetRekapMobileJKN']);
        Route::post('get-kode-booking-operasi',[Reservasi\ReservasiC::class, 'getKodeBokingOperasi']);
        Route::post('get-jadwal-operasi',[Reservasi\ReservasiC::class, 'getJadwalOperasi']);

        Route::post('get-status-antrean',[Reservasi\ReservasiC::class, 'GetStatusAntrianMobileJKN']);
        Route::post('get-antrean',[Reservasi\ReservasiC::class, 'GetAntrean']);
        Route::post('get-sisa-antrean',[Reservasi\ReservasiC::class, 'GetSisaAntrean']);
        Route::post('save-batal-antrean',[Reservasi\ReservasiC::class, 'saveBatalAntrean']);
        Route::post('save-checkin',[Reservasi\ReservasiC::class, 'saveCheckInAntrean']);
        Route::post('save-pasien-baru',[Reservasi\ReservasiC::class, 'savePasienBaru']);

    });
});


Route::group(['middleware' => 'cors', 'prefix' => 'api'], function () {

    Route::group(['prefix' => 'm-jkn'], function () {
        Route::get('get-token', [Auth\LoginC::class, 'getTokens']);
        Route::post('get-token', [Auth\LoginC::class, 'getTokens']);
        Route::post('get-no-antrean',[Reservasi\ReservasiC::class, 'GetNoAntrianMobileJKN']);
        Route::post('get-rekap-antrean',[Reservasi\ReservasiC::class, 'GetRekapMobileJKN']);
        Route::post('get-kode-booking-operasi',[Reservasi\ReservasiC::class, 'getKodeBokingOperasi']);
        Route::post('get-jadwal-operasi',[Reservasi\ReservasiC::class, 'getJadwalOperasi']);

        Route::post('get-status-antrean',[Reservasi\ReservasiC::class, 'GetStatusAntrianMobileJKN']);
        Route::post('get-antrean',[Reservasi\ReservasiC::class, 'GetAntrean']);
        Route::post('get-sisa-antrean',[Reservasi\ReservasiC::class, 'GetSisaAntrean']);
        Route::post('save-batal-antrean',[Reservasi\ReservasiC::class, 'saveBatalAntrean']);
        Route::post('save-checkin',[Reservasi\ReservasiC::class, 'saveCheckInAntrean']);
        Route::post('save-pasien-baru',[Reservasi\ReservasiC::class, 'savePasienBaru']);

    });
});

Route::get('print/cetak-laporan-penerimaan-kasir', [Cetak\CetakC::class, 'getDataLaporanKasir']);
Route::get('print/cetak-laporan-sensus-pasien', [Cetak\CetakC::class, 'cetakLaporanSensusPasien']);
Route::get('print/cetak-laporan-sensus-igd', [Cetak\CetakC::class, 'cetakLaporanSensusIGD']);
Route::get('print/cetak-laporan-sensus-bulanan-igd', [Cetak\CetakC::class, 'cetakLaporanSensusBulananIGD']);
Route::get('print/cetak-laporan-kunjungan-poliklinik', [Cetak\CetakC::class, 'cetakLaporanKunjungan']);

