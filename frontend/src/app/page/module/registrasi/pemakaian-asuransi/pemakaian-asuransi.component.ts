import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { jsPDF } from "jspdf";
@Component({
  selector: 'app-pemakaian-asuransi',
  templateUrl: './pemakaian-asuransi.component.html',
  styleUrls: ['./pemakaian-asuransi.component.scss'],
  providers: [ConfirmationService]
})
export class PemakaianAsuransiComponent implements OnInit {
  currentNorecPD: any
  currentNorecAPD: any
  now = new Date()
  item: any = {
    pasien: {}
  }
  isLoadingSEP: boolean
  model: any = {
    tglSEP: new Date(),
    tglRujukan: new Date(moment(new Date()).format("YYYY-MM-DD 00:00")),
    tglPelayanan: new Date(),
    tglLakalantas: new Date(),
    cekNikPeserta: true,
    cekNomorRujukan: true
  }
  listDPJP2: any = []
  ppkRumahSakit: any
  namappkRumahSakit: any
  statusBridgingTemporary: any
  captionRujukan: string = 'No Rujukan'
  listAsalRujukan: any[] = []
  listKelompokPasien: any[] = []
  listHubunganPasien: any[] = []
  listKelasDitanggung: any[] = []
  cacheAsalRujuk: any = ''
  listRekanan: any
  jenisPel: any
  kdSpesialis: any = ''
  cacheIdAP: any
  cacheNorecPA: any
  listDPJP: any[] = []
  dataKabupaten: any
  dataKecamatan: any
  listPropinsi: any[] = []
  disableSEP: boolean
  ceknobpjsdouble: boolean = false
  isLoadingNoKartu: boolean
  kodeProvider: any
  namaProvider: any
  poliRujukanKode: any
  poliRujukanNama: any
  listHistori: any = []
  listDiagnosa: any[] = []
  listNoRujukanMulti: any[]
  popupRujukan: boolean
  showPilihNomor: boolean
  listPpkRujukan: any[] = []
  isSimpan: boolean
  isLoadingRujukan: boolean
  isLoadingNIK: boolean
  currentListPenjaminLaka: any = []
  isHistory: boolean
  isRujukan: boolean
  dataSourceHistoriPeserta: any[]
  dataSourceRujukan: any[]
  listBtn: MenuItem[];
  classsimpan = 'p-md-offset-10'
  isNext: boolean
  numberss = Array(15).map((x, i) => i);
  listProv: any[]
  listKab: any[]
  listKec: any[]
  listKelasNaik = []
  namaPPK = ''
  listLakaLantas = [
    { id: "0", name: "Bukan Kecelakaan lalu lintas [BKLL]" },
    { id: "1", name: "KLL dan bukan kecelakaan Kerja [BKK]" },
    { id: "2", name: "KLL dan KK" },
    { id: "3", name: "KK" },
  ]
  listPenjaminLaka = [
    { "id": 12, "name": "Jasa Raharja PT", "value": 1 },
    { "id": 13, "name": "BPJS Ketenagakerjaan", "value": 2 },
    { "id": 14, "name": "TASPEN PT", "value": 3 },
    { "id": 15, "name": "ASABRI PT", "value": 4 }
  ];
  listTujuan = [
    { id: "0", name: "Normal" },
    { id: "1", name: "Prosedur" },
    { id: "2", name: "Konsul Dokter" },
  ]
  listFlag = [
    { id: "0", name: "Prosedur Tidak Berkelanjutan" },
    { id: "1", name: "Prosedur dan Terapi Berkelanjutan" },
    { id: "2", name: "Konsul Dokter" },
  ]

  listPenunjang = [
    { id: "1", name: "Radioterapi" },
    { id: "2", name: "Kemoterapi" },
    { id: "3", name: "Rehabilitasi Medik" },
    { id: "4", name: "Rehabilitasi Psikososial" },
    { id: "5", name: "Transfusi Darah" },
    { id: "6", name: "Pelayanan Gigi" },
    { id: "7", name: "Laboratorium" },
    { id: "8", name: "USG" },
    { id: "9", name: "Farmasi" },
    { id: "10", name: "Lain-Lain" },
    { id: "11", name: "MRI" },
    { id: "12", name: "HEMODIALISA" },
  ]
  listAsesmen = [
    { id: "1", name: "Poli spesialis tidak tersedia pada hari sebelumnya" },
    { id: "2", name: "Jam Poli telah berakhir pada hari sebelumnya" },
    { id: "3", name: "Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya" },
    { id: "4", name: "Atas Instruksi RS" },
    { id: "5", name: "Tujuan Kontrol" },
  ]

  listBiaya = [
    { "id": "1", "name": "Pribadi", "value": 1 },
    { "id": "2", "name": "Pemberi Kerja", "value": 2 },
    { "id": "3", "name": "Asuransi Kesehatan Tambahan", "value": 3 },
  ]
  cachePasienDaftar: any
  columnSPRI: any = []
  popUpRSPRI: boolean

  dataSourceSPRI: any = []
  listFilter = [{ kode: 2, nama: 'Tgl Rencana Kontrol' }, { kode: 1, nama: 'Tgl Entri' }]
  // this.kontrol.tglRencanaKontrol = new Date()
  kontrol: any = {
    tglAwal: new Date(),
    tglAkhir: new Date(),
    filter: this.listFilter[0]
  }
  pop_cetakLabelPasien:boolean
  enabledDetail: boolean = false
  enabledDetail2: boolean = false

  disabledCetak: boolean = true
  resCetak: any = {}
  index: any = 0
  dataSelected: any
  popCetak: boolean;
  kon: any = {
    now: moment(new Date()).format('YYYY-MM-DD'),
    jenisPelayanan: "1",
    tglAwal: new Date(),
    tglAkhir: new Date(),
    tglRencanaKontrol2: new Date(),
    peserta: {
      statusPeserta: {},
      hakKelas: {},
      provUmum: {}
    },
    filter: this.listFilter[0]
  }
  listDPJPKontrol:any =[]
  myVar: boolean = false
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router) { }

  ngOnInit(): void {

    this.listBtn = [
      { label: 'Cetak SEP', icon: 'pi pi-print', command: () => { this.cetakSEP(); } },
      { label: 'Hapus SEP', icon: 'pi pi-trash', command: () => { this.hapusSEP(); } },

      { label: 'Kembali', icon: 'fa fa-arrow-left', command: () => { this.cancel(); } },
      { separator: true },
      { label: 'Bukti Layanan', icon: 'pi pi-print', command: () => { this.cetakBuktiLayanan(); } },
      { label: 'Label Pasien', icon: 'pi pi-print', command: () => { this.cetakLabelPasien(); } },
      { label: 'Kartu Pasien', icon: 'pi pi-print', command: () => { this.cetakKartu(); } },
      { label: 'Gelang Pasien', icon: 'pi pi-print', command: () => { this.cetakGelang(); } },
      { label: 'Tracer', icon: 'pi pi-print', command: () => { this.cetakTracer(); } },
      { label: 'Nomor Antrian', icon: 'pi pi-print', command: () => { this.cetakNomorAntrian(); } },
      { label: 'Persetujuan Rawat Inap', icon: 'pi pi-print', command: () => { this.cetakPersetujuanRanap(); } },
    ];
    this.model.tujuanKunj = this.listTujuan[0]
    this.route.params.subscribe(params => {
      this.currentNorecPD = params['norec_rp'];
      this.currentNorecAPD = params['norec_dpr']
      this.loadCombo()
      this.loadFirst()
    })
    this.listAsalRujukan = [{ name: 'P-Care', id: '1' }, { name: 'RS', id: '2' }];
    this.model.cekNomorPeserta = true
  }


 // hapusSEP() {

 //    var stt = 'false'
 //    if (confirm('Yakin akan menghapus SEP? ')) {
 //      // Save it!
 //      stt = 'true';
 //    } else {
 //      // Do nothing!
 //      stt = 'false'
 //    }
 //    this.hapusSEP();
 //  }











  cetakPersetujuanRanap() {
    if (this.item.pasien.noregistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }

    if ( this.item.pasien.israwatinap != true) {
      this.alertService.error('Info', "Hanya Untuk Pasien Rawat Inap!");
      return;
    }

    var stt = 'false'
    if (confirm('View Surat Persetujuan Rawat Inap? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-surat-pernyataan-ranap=1&noregistrasi=" +
    this.item.pasien.noregistrasi  + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }
  cetakNomorAntrian() {
    if ( this.item.pasien.noregistrasi  == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    var stt = 'false'
    if (confirm('View Bukti Pendaftaran? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-antrian-pendaftaran=1&noregistrasi=" +
       this.item.pasien.noregistrasi  + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }
  cetakTracer() {
    if ( this.item.pasien.noregistrasi  == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    var stt = 'false'
    if (confirm('View Tracer? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-tracer=1&noregistrasi=" +
       this.item.pasien.noregistrasi  + "&view=" + stt, function (e) { });
  }
  cetakGelang() {
    var stt = 'false'
    if (confirm('View Gelang Pasien? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-gelangpasien=1&noregistrasi=" +
       this.item.pasien.noregistrasi  + "&qty=1" + "&view=" + stt, function (e) { });
  }
  cetakKartu() {
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-kartu-pasien=1&norm=" +
    this.item.pasien.nocm, function (e) { });
  }
  cetakLabelPasien() {
    if ( this.item.pasien.noregistrasi  == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }
    this.item.qtyPrint = 1;
    this.pop_cetakLabelPasien = true;
  }
  cetakLabelPrint() {
    var stt = 'false'
    if (confirm('View Label Pasien? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-labelpasien=1&noregistrasi=" +
       this.item.pasien.noregistrasi  + "&qty=" + this.item.qtyPrint + "&view=" + stt, function (e) { });
    this.pop_cetakLabelPasien = false;
  }
  cetakBuktiLayanan() {
    if (this.item.ruangan == undefined) {
      this.alertService.error('Info', "Ruangan Masih Belum Diisi!");
      return;
    }
    if ( this.item.pasien.noregistrasi  == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }
    var stt = 'false'
    if (confirm('View Bukti Layanan? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-buktilayanan=1&noregistrasi=" +
       this.item.pasien.noregistrasi  + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&idRuangan=" + this.item.ruangan.id + "&view=" + stt, function (e) { });
  }
  hapusSEP() {
    var stt = 'false'
    if (confirm('Yakin akan menghapus SEP? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    let json = {
      "url": "SEP/2.0/delete",
      "method": "DELETE",
      "data": {
        "request": {
          "t_sep": {
            "noSep": this.model.noSep,
            "user": "Xoxo"
          }
        }
      }
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code === "200") {
        // harusnya save riwayat delete di db
        this.alertService.success("Info", e.metaData.message);
        this.model.generateNoSEP = false;
        this.disableSEP = false;
        this.model.noSep = '';
        this.thisGenerate();
      }
      else {
        this.alertService.success("Info", e.metaData.message);
      }
    })

  }
  cetakSEP() {
    if (this.model.noSep != '') {
      var nosep = this.model.noSep
      var nmperujuk = this.model.namaAsalRujukan.split(" - ")[1]

      var tglsep = moment(this.model.tglSEP).format('YYYY-MM-DD')
      var nokartu = this.model.noKepesertaan + '  ( MR. ' + this.item.pasien.nocm + ' )';
      var nmpst = this.item.pasien.namapasien
      var tgllahir = this.item.pasien.tgllahir
      var jnskelamin = this.item.pasien.objectjeniskelaminfk == '1' ? '  Kelamin : Laki-Laki' : '  Kelamin :Perempuan';
      var poli = this.item.pasien.israwatinap == 'true' ? '-' : this.item.pasien.namaruangan;
      var faskesperujuk = this.item.pasien.israwatinap == 'true' ? this.namappkRumahSakit : nmperujuk;
      var notelp = this.model.noTelpons ? this.model.noTelpons : ''
      var dxawal = this.model.diagnosa.nama.substring(0, 45);
      var catatan = this.model.catatan ? this.model.catatan : ''
      var jnspst = this.model.jenisPeserta ? this.model.jenisPeserta : ''
      var FLAGCOB = this.model.cob
      var cob = '-';
      if (FLAGCOB) {
        cob = this.model.cobNama ? this.model.cobNama : null
      }

      //cob non aktif
      var FLAGNAIKKELAS = this.model.naikKelas == true ? 1 : 0
      var klsrawat_naik = this.model.klsRawatNaik ? this.model.klsRawatNaik.namakelas : ""

      var jnsrawat = this.item.pasien.israwatinap == 'true' ? 'R.Inap' : 'R.Jalan';
      var klsrawat = this.model.kelasDitanggung ? this.model.kelasDitanggung.namakelas : '-';
      var prolanis = this.model.prolanis ? this.model.prolanis : ""
      var eksekutif = this.item.pasien.israwatinap == 'true' ? '' : this.model.poliEksekutif == true ? ' (Poli Eksekutif)' : '';
      //var penjaminJR = $('#chkjaminan_JR').is(":checked") == true ? 'Jasa Raharja PT' : '';
      //var penjaminTK = $('#chkjaminan_BPJSTK').is(":checked") == true ? 'BPJS Ketenagakerjaan' : '';
      //var penjaminTP = $('#chkjaminan_TASPEN').is(":checked") == true ? 'PT TASPEN' : '';
      //var penjaminAS = $('#chkjaminan_ASABRI').is(":checked") == true ? 'ASABRI' : '';
      var katarak = this.model.katarak == true ? '1' : '0';
      var potensiprb = this.model.prolanis ? this.model.prolanis : ""
      var statuskll = this.model.lakaLantas ? this.model.lakaLantas.name : ""

      var dokter = (this.item.pasien.israwatinap == 'true') ? this.model.dokterDPJP ? this.model.dokterDPJP.nama : "" : this.model.DPJPMelayani ? this.model.DPJPMelayani.nama : "";
      var FLAGPROSEDUR = this.model.flagProcedure ? this.model.flagProcedure.name : ""

      var kunjungan = this.item.pasien.israwatinap == 'true' ? 3 : 1;

      var isrujukanthalasemia_hemofilia = 0

      if (this.item.pasien.kdinternal == 'UGD' || this.item.pasien.kdinternal == 'IGD') {
        nmperujuk = '';
        kunjungan = 0;
        FLAGPROSEDUR = null;
      }

      //var sepdate = new Date(tglsep);
      //var currDate = new Date(dataSEP.sep.sep.FDATE);
      //var backdate = sepdate < new Date(currDate.getFullYear(), currDate.getMonth(), currDate.getDate()) ? " (BACKDATE)" : "";

      var backdate = this.dateHelper.cekBackdate(tglsep, this.model.tglcreate ? this.model.tglcreate : tglsep);
      var ispotensiHEMOFILIA_cetak = 0
      var _kodejaminan = '-';
      this.dateHelper.cetakSEP(nosep + backdate, tglsep, nokartu, nmpst, tgllahir, jnskelamin, notelp, poli, faskesperujuk, dxawal, catatan, jnspst, cob, jnsrawat, klsrawat,
        prolanis, eksekutif, _kodejaminan, statuskll, katarak, potensiprb, dokter, kunjungan, FLAGPROSEDUR, "-", FLAGNAIKKELAS, klsrawat_naik, isrujukanthalasemia_hemofilia, ispotensiHEMOFILIA_cetak, this.namappkRumahSakit);


    }
  }

  ceklisNoKartu(e) {

  }
  loadCombo() {
    this.apiService.get('registrasi/get-setting-asuransi').subscribe(dat => {
      this.ppkRumahSakit = dat.kodePPKRujukan
      this.namappkRumahSakit = dat.namaPPKRujukan
      this.statusBridgingTemporary = dat.statusBridgingTemporary
    })
    this.apiService.get("registrasi/get-combo-pemakaian-asuransi")
      .subscribe(dat => {
        // this.listAsalRujukan = dat.asalrujukan;
        this.listKelompokPasien = dat.kelompokpasien;
        this.listHubunganPasien = dat.hubunganpeserta;
        this.listKelasDitanggung = dat.kelas;

        for (let x = 0; x < dat.kelas.length; x++) {
          const element = dat.kelas[x];
          if (element.kelasbpjs != null) {
            this.listKelasNaik.push({ id: element.kelasbpjs, namakelas: element.namakelas })
          }
        }

        this.model.hubunganPeserta = { id: dat.hubunganpeserta[2].id, hubunganpeserta: dat.hubunganpeserta[2].hubunganpeserta }
        this.cacheAsalRujuk = this.cacheHelper.get('cacheAsalRujukan')
        if (this.cacheAsalRujuk != '' && this.cacheAsalRujuk != undefined) {
          // debugger
          if (this.cacheAsalRujuk.toLowerCase().indexOf('puskesmas') > -1)
            this.model.asalRujukan = this.listAsalRujukan[0]
          else if (this.cacheAsalRujuk.toLowerCase().indexOf('sakit') > -1)
            this.model.asalRujukan = this.listAsalRujukan[1]
          else
            this.model.asalRujukan = this.listAsalRujukan[0]
        } else {
          this.model.asalRujukan = this.listAsalRujukan[0]
        }

      });
    // this.generateSKDP(true)

  }
  loadFirst() {
    this.apiService.get("registrasi/get-pasien-bynorec?norec_pd="
      + this.currentNorecPD
      + "&norec_apd="
      + this.currentNorecAPD)
      .subscribe(e => {
        let res = e
        this.item.pasien = res;
        this.item.pasien.tglregistrasi = moment(new Date(this.item.pasien.tglregistrasi)).format('YYYY-MM-DD HH:mm')
        this.item.pasien.tgllahir = moment(new Date(this.item.pasien.tgllahir)).format('YYYY-MM-DD')
        var now = new Date();
        var umur = this.dateHelper.CountAge(new Date(this.item.pasien.tgllahir), now);
        this.item.pasien.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari'
        this.model.noTelpons = res.notelepon;
        this.model.noKepesertaan = res.nobpjs;
        this.model.noIdentitas = res.noidentitas;
        this.listKelompokPasien = ([{ id: res.objectkelompokpasienlastfk, kelompokpasien: res.kelompokpasien }]);
        this.item.kelompokPasien = { id: res.objectkelompokpasienlastfk, kelompokpasien: res.kelompokpasien };
        this.listRekanan = ([{ id: res.objectrekananfk, namarekanan: res.namarekanan }])
        this.model.institusiAsalPasien = { id: res.objectrekananfk, namarekanan: res.namarekanan }
        this.item.jenispelayanan = res.jenispelayanan;
        if (this.item.jenispelayanan == "EKSEKUTIF") {
          this.model.poliEksekutif = true;
        } else {
          this.model.poliEksekutif = false;
        }
        if (res.israwatinap == "true") {
          this.model.rawatInap = true;
          this.captionRujukan = 'No SPRI'
          this.jenisPel = "1"
        } else {
          this.model.rawatInap = false;
          this.captionRujukan = 'No Rujukan'
          this.jenisPel = "2"
        }
        this.kdSpesialis = this.item.pasien.kdinternal
        this.cachePasienDaftar = this.cacheHelper.get('CachePemakaianAsuransi');
        if (this.cachePasienDaftar != undefined) {
          var arrPasienDaftar = this.cachePasienDaftar.split('~');
          if (arrPasienDaftar[0] == 'null' && arrPasienDaftar[1] == 'null')
            return
          this.cacheIdAP = arrPasienDaftar[0];
          this.cacheNorecPA = arrPasienDaftar[1];
          var cacheNoreg = arrPasienDaftar[2];
          if (this.item.pasien.noregistrasi != cacheNoreg) {
            this.cacheNorecPA = undefined;
            return
          }

          // this.generateSKDP(true)
          this.getPemakaianAsuransiByNoReg(this.cacheNorecPA)

        } else {
          // this.generateSKDP(true)
          this.getPemakaianAsuransiByNoReg(this.item.pasien.noregistrasi)

        }
      });
  }

  getPemakaianAsuransiByNoReg(noreg: any) {

    this.apiService.get("registrasi/get-history-pemakaianasuransi-new?noregistrasi=" + noreg).subscribe(x => {
      if (x.data != null) {
        // debugger
        this.isNext = true
        var result = x.data
        this.classsimpan = 'p-md-offset-9'
        this.cacheIdAP = result.norec_ap;
        this.cacheNorecPA = result.norec;
        this.model.noKepesertaan = result.nokepesertaan
        this.model.namaPeserta = result.namapeserta
        this.model.noIdentitas = result.noidentitas
        this.model.jenisPeserta = result.jenisPeserta
        // this.model.noTelpons ={id:result.objectkelompokpasienlastfk,kelompokpasien:result.kelompokpasien}
        this.model.hubunganPeserta = { id: result.objecthubunganpesertafk, hubunganpeserta: result.hubunganpeserta }
        this.model.noSep = result.nosep
        this.model.tglSEP = new Date(result.tanggalsep)
        // this.listKelasDitanggung = [{ id: result.objectkelasdijaminfk, namakelas: result.namakelas }]
        if (result.objectkelasdijaminfk != null)
          this.model.kelasDitanggung = { id: result.objectkelasdijaminfk, namakelas: result.namakelas }
        this.model.catatan = result.catatan
        this.model.noRujukan = result.norujukan
        if (this.cacheAsalRujuk != '' && this.cacheAsalRujuk != undefined) {
          if (this.cacheAsalRujuk.toLowerCase().indexOf('puskesmas') > -1)
            this.model.asalRujukan = this.listAsalRujukan[0]
          else if (this.cacheAsalRujuk.toLowerCase().indexOf('sakit') > -1)
            this.model.asalRujukan = this.listAsalRujukan[1]
          else
            this.model.asalRujukan = this.listAsalRujukan[0]
        } else {

          this.model.asalRujukan = this.listAsalRujukan[0]
        }
        if (result.asalrujukanfk != null) {
          for (let i = 0; i < this.listAsalRujukan.length; i++) {
            const element = this.listAsalRujukan[i];
            if (element.id == result.asalrujukanfk) {
              this.model.asalRujukan = element
              break
            }
          }
        }

        // this.model.asalRujukan ={id:result.objectkelompokpasienlastfk,kelompokpasien:result.kelompokpasien}
        if (this.model.rawatInap == true) {
          if (result.norujukan != null && result.norujukan != '') {
            this.model.noRujukan = result.norujukan
          } else {
            this.model.noRujukan = this.model.skdp
          }
          this.model.namaAsalRujukan = this.ppkRumahSakit + " - " + this.namappkRumahSakit
        } else {
          this.model.namaAsalRujukan = result.kdprovider + " - " + result.nmprovider
        }
        this.model.tglRujukan = new Date(result.tglrujukan)
        if (result.objectdiagnosafk)
          this.model.diagnosa = { id: result.objectdiagnosafk, kddiagnosa: result.kddiagnosa, nama: result.kddiagnosa + ' - ' + result.namadiagnosa }
        if (result.tgllahir != null)
          this.model.tglLahir = new Date(result.tgllahir)
        this.item.kelompokPasien = { id: result.objectkelompokpasienlastfk, kelompokpasien: result.kelompokpasien }
        this.model.institusiAsalPasien = { id: result.objectrekananfk, namarekanan: result.namarekanan }
        this.model.lokasiLakaLantas = result.lokasilakalantas
        this.model.jenisPeserta = result.jenispeserta
        if (result.kodedpjp != null) {
          let resDpjp = { kode: result.kodedpjp, nama: result.namadpjp }
          this.listDPJP.push(resDpjp);
          this.model.dokterDPJP = { kode: result.kodedpjp, nama: result.namadpjp }
        }
        if (result.kodedpjpmelayani != null) {
          let resDpjp = { kode: result.kodedpjpmelayani, nama: result.namadjpjpmelayanni }
          this.listDPJP2.push(resDpjp);
          this.model.DPJPMelayani = { kode: result.kodedpjpmelayani, nama: result.namadjpjpmelayanni }
        }

        if (result.lakalantas != 0) {
          for (let z = 0; z < this.listLakaLantas.length; z++) {
            const element = this.listLakaLantas[z];
            if (element.id == result.lakalantas) {
              this.model.lakaLantas = element
              break
            }
          }

          this.model.tglLakalantas = new Date(result.tglkejadian)
          if (result.kdpropinsi != null) {
            this.listProv.push({ kode: result.kdpropinsi, nama: result.namapropinsi });
            this.model.prov = { kode: result.kdpropinsi, nama: result.namapropinsi }
          }
          if (result.kdkabupaten != null) {
            this.listKab.push({ kode: result.kdkabupaten, nama: result.namakabupaten });
            this.model.kab = { kode: result.kdkabupaten, nama: result.namakabupaten }
          }
          if (result.kdkecamatan) {
            this.listKec.push({ kode: result.kdkecamatan, nama: result.namakecamatan });
            this.model.kec = { kode: result.kdkecamatan, nama: result.namakecamatan }
          }

          this.model.keteranganLaka = result.keteranganlaka
          if (result.suplesi == true) {
            this.model.suplesi = true
            this.model.nomorSepSuplesi = result.nosepsuplesi
          }
        }


        if (result.klsrawatnaik) {
          this.model.naikKelas = true
          for (let z = 0; z < this.listKelasNaik.length; z++) {
            const element = this.listKelasNaik[z];
            if (element.id == result.klsrawatnaik) {
              this.model.klsRawatNaik = element
              break
            }
          }
        }
        if (result.objectkelasdijaminfk) {
          for (let z = 0; z < this.listKelasDitanggung.length; z++) {
            const element = this.listKelasDitanggung[z];
            if (element.id == result.objectkelasdijaminfk) {
              this.model.kelasDitanggung = element
              break
            }
          }
        }
        if (result.pembiayaan) {
          for (let z = 0; z < this.listBiaya.length; z++) {
            const element = this.listBiaya[z];
            if (element.id == result.pembiayaan) {
              this.model.pembiayaan = element
              break
            }
          }
        }
        if (result.penanggungjawab) {
          this.model.penanggungJawab = result.penanggungjawab
        }
        if (result.tujuankunj) {
          for (let z = 0; z < this.listTujuan.length; z++) {
            const element = this.listTujuan[z];
            if (element.id == result.tujuankunj) {
              this.model.tujuanKunj = element
              break
            }
          }
        }
        if (result.flagprocedure) {
          for (let z = 0; z < this.listFlag.length; z++) {
            const element = this.listFlag[z];
            if (element.id == result.flagprocedure) {
              this.model.flagProcedure = element
              break
            }
          }
        }
        if (result.kdpenunjang) {
          for (let z = 0; z < this.listPenunjang.length; z++) {
            const element = this.listPenunjang[z];
            if (element.id == result.kdpenunjang) {
              this.model.kdPenunjang = element
              break
            }
          }
        }
        if (result.assesmentpel) {
          for (let z = 0; z < this.listAsesmen.length; z++) {
            const element = this.listAsesmen[z];
            if (element.id == result.assesmentpel) {
              this.model.assesmentPel = element
              break
            }
          }
        }
        // if (result.penjaminlaka != '' && result.penjaminlaka != null) {
        //   var penjaminsLaka = result.penjaminlaka.split(',')
        //   penjaminsLaka.forEach(function (data) {
        //     this.listPenjaminLaka.forEach(function (e) {
        //       if (e.value == data) {
        //         e.isChecked = true
        //         var dataid = {
        //           "id": e.id,
        //           "name": e.name,
        //           "value": data
        //         }
        //         this.currentListPenjaminLaka.push(dataid)
        //       }
        //     })
        //   })
        // }
        this.model.skdp = result.nosuratskdp
        // this.model.dokterDPJP = result.cob
        this.model.cob = result.cob
        this.model.katarak = result.katarak
        this.model.tglcreate = result.tglcreate

        if (result.statuskunjungan)
          this.model.statuskunjungan = result.statuskunjungan
        if (result.poliasalkode)
          this.model.poliasalkode = result.poliasalkode
        if (result.poliasalkode)
          this.model.poliasalkode = result.poliasalkode
      }
    });
  }
  Sendiri(data) {
    if (data === true) {
      this.model.namaPeserta = this.item.pasien.namapasien;
      this.model.tglLahir = new Date(this.item.pasien.tgllahir)
      this.model.noIdentitas = this.item.pasien.noidentitas;
      this.model.alamatPeserta = this.item.pasien.alamatlengkap;
      this.model.noKepesertaan = this.item.pasien.nobpjs;
      this.model.noTelpons = this.item.pasien.notelepon;
      this.model.noIdentitas = this.item.pasien.noidentitas;

    } else {
      this.model.noKepesertaan = "";
      this.model.namaPeserta = "";
      this.model.tglLahir = "";
      this.model.noIdentitas = "";
      this.model.alamatPeserta = "";
      this.model.noTelpons = "";
      this.model.noKepesertaan = "";
      this.model.noIdentitas = "";

    }
    this.disableSEP = data;
  }
  checkKepesertaanByNoBpjs = function () {
    // debugger
    if (this.model.sendiri === true) return;
    if (this.model.noKepesertaan === '' || this.model.noKepesertaan === undefined) return;
    if (this.model.cekNomorPeserta == true || this.model.cekNomorRujukanMulti == true) {

      this.ceknobpjsdouble = false
      this.isLoadingNoKartu = true
      this.apiService.get("registrasi/cek-nobpjs?nobpjs=" + this.model.noKepesertaan + "&idnocm=" + this.item.pasien.nocmfk)
        .subscribe(data => {
          this.isLoadingNoKartu = false
          this.ceks = data;
          if (this.ceks.data.length >= 1) {
            var nocm = this.ceks.data[0].nocm;
            var nama = this.ceks.data[0].namapasien;
            this.alertService.error('Info', "NO BPJS ini sudah di pakai oleh pasien RM : " + nocm + " (" + nama + ") !")
            this.ceknobpjsdouble = true
            return;
          } else {
            this.checkKepesertan()
          }
        })
    }
  }
  checkKepesertan() {
    if (this.model.cekNomorPeserta == true) {
      if (this.model.noKepesertaan === '' || this.model.noKepesertaan === undefined) return;
      this.model.generateNoSEP = true
      if (this.model.rawatInap === true || this.item.pasien.kdinternal == "IGD") {
        this.isLoadingNoKartu = true;
        this.apiService.get("bridging/bpjs/get-no-peserta?nokartu=" + this.model.noKepesertaan
          + "&tglsep=" + moment(new Date).format('YYYY-MM-DD')).subscribe(e => {
            if (e.metaData.code === "200") {
              var tglLahir = new Date(e.response.peserta.tglLahir);
              this.model.noKepesertaan = e.response.peserta.noKartu;
              this.model.namaPeserta = e.response.peserta.nama;
              this.model.tglLahir = tglLahir;
              this.model.noIdentitas = e.response.peserta.nik;
              this.model.kelasBridg = {
                id: parseInt(e.response.peserta.hakKelas.kode),
                kdKelas: e.response.peserta.hakKelas.kode,
                nmKelas: e.response.peserta.hakKelas.keterangan,
                namakelas: e.response.peserta.hakKelas.keterangan,
              };
              for (let x = 0; x < this.listKelasDitanggung.length; x++) {
                const element = this.listKelasDitanggung[x];
                if (element.id == e.response.peserta.hakKelas.kode) {
                  this.model.kelasDitanggung = element
                  break
                }
              }
              this.kodeProvider = e.response.peserta.provUmum.kdProvider;
              this.namaProvider = e.response.peserta.provUmum.nmProvider;
              this.model.faskesRujukan = false;
              this.model.namaAsalRujukan = this.kodeProvider + " - " + this.namaProvider;
              this.model.jenisPeserta = e.response.peserta.jenisPeserta.keterangan;
              this.model.prolanis = e.response.peserta.informasi.prolanisPRB;
              this.alertService.info('Status Peserta', e.response.peserta.statusPeserta.keterangan);
              this.model.cobNama == e.response.peserta.cob.nmAsuransi;
              var jenisFaskes = "rs"
              if (this.model.asalRujukan.id != undefined) {
                if (this.model.asalRujukan.id == 1)
                  jenisFaskes = "pcare"
                if (this.model.asalRujukan.id == 2)
                  jenisFaskes = "rs"
              }

              this.apiService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
                + "&tglPelayanan=" + moment(this.now).format('YYYY-MM-DD') + "&kodeSpesialis=" + this.kdSpesialis).subscribe(data => {
                  if (data.metaData.code == 200) {
                    this.listDPJP = data.response.list;
                    this.listDPJP2 = data.response.list;
                  }
                  else
                    this.alertService.info('Info', 'Dokter DPJP tidak ada')
                });
              // this.getHistoriPelayananPesesta(this.model.noKepesertaan)
            } else {
              this.alertService.error('Info', e.metaData.message)
            }
            this.isLoadingNoKartu = false;
          }, function (err) {
            this.isLoadingNoKartu = false;
          });
      } else {
        // debugger
        var jenisFaskes = "rs"
        if (this.model.asalRujukan.id != undefined) {
          if (this.model.asalRujukan.id == 1)
            jenisFaskes = "pcare"
          if (this.model.asalRujukan.id == 2)
            jenisFaskes = "rs"
        }
        this.apiService.get("bridging/bpjs/get-rujukan-" + jenisFaskes + "-nokartu?nokartu=" + this.model.noKepesertaan).subscribe(e => {
          if (e.metaData.code === "200") {
            // polirujukanbpjs = e.response.rujukan.poliRujukan.kode;
            this.model.noRujukan = e.response.rujukan.noKunjungan;
            this.model.tglRujukan = new Date(e.response.rujukan.tglKunjungan);
            var tglLahir = new Date(e.response.rujukan.peserta.tglLahir);
            this.model.namaPeserta = e.response.rujukan.peserta.nama;
            this.model.tglLahir = tglLahir;
            this.model.noIdentitas = e.response.rujukan.peserta.nik;
            this.model.kelasBridg = {
              id: parseInt(e.response.rujukan.peserta.hakKelas.kode),
              kdKelas: e.response.rujukan.peserta.hakKelas.kode,
              nmKelas: e.response.rujukan.peserta.hakKelas.keterangan,
              namakelas: e.response.rujukan.peserta.hakKelas.keterangan,
            };
            for (let x = 0; x < this.listKelasDitanggung.length; x++) {
              const element = this.listKelasDitanggung[x];
              if (element.id == e.response.rujukan.peserta.hakKelas.kode) {
                this.model.kelasDitanggung = element
                break
              }
            }
            this.poliRujukanKode = e.response.rujukan.poliRujukan.kode
            this.poliRujukanNama = e.response.rujukan.poliRujukan.nama
            this.kodeProvider = e.response.rujukan.provPerujuk.kode;
            this.namaProvider = e.response.rujukan.provPerujuk.nama;
            this.model.faskesRujukan = false;
            this.model.namaAsalRujukan = this.kodeProvider + " - " + this.namaProvider;
            this.model.jenisPeserta = e.response.rujukan.peserta.jenisPeserta.keterangan;
            this.model.prolanis = e.response.rujukan.peserta.informasi.prolanisPRB;
            this.model.noTelpons = e.response.rujukan.peserta.mr.noTelepon;
            this.model.tglRujukan = new Date(e.response.rujukan.tglKunjungan);

            this.apiService.get("registrasi/get-diagnosa-saeutik?kddiagnosa=" + e.response.rujukan.diagnosa.kode)
              .subscribe(data => {
                this.listDiagnosa.push(data[0])
                this.model.diagnosa = data[0]
              })

            // get Dokter DPJP By Histori
            this.getHistoriPelayananPesesta(this.model.noKepesertaan)

            // medifirstService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
            //     + "&tglPelayanan=" + new moment(this.now).format('YYYY-MM-DD') + "&kodeSpesialis=" 
            //     + kdSpesialis ).then(function (z) {
            //         if (z.data.metaData.code == 200)
            //             this.listDPJP = z.data.response.list;
            //         else
            //             toastr.info('Dokter DPJP tidak ada', 'Info')
            // });

            this.alertService.info('No Rujukan', e.response.rujukan.noKunjungan);
          } else {
            this.alertService.error('Info', e.metaData.message);
          }
          this.isLoadingNoKartu = false;
        }, function (err) {
          this.isLoadingNoKartu = false;
        });

      }
    } else if (this.model.cekNomorRujukanMulti == true) {
      if (this.model.noKepesertaan === '' || this.model.noKepesertaan === undefined) return;
      this.isLoadingNoKartu = true;
      var jenisFaskes = "rs"
      if (this.model.asalRujukan.id != undefined) {
        if (this.model.asalRujukan.id == 1)
          jenisFaskes = "pcare"
        if (this.model.asalRujukan.id == 2)
          jenisFaskes = "rs"
      }
      if (this.model.asalRujukan.id == 7) {
        // this.apiService.get("bridging/bpjs/monitoring/HistoriPelayanan/NoKartu/" + this.model.noKepesertaan).subscribe( e=> {
        //   if (e.metaData.code === "200") {
        //     this.listHistori = e.response.histori;

        //     for (let i = 0; i < e.response.histori.length; i++) {
        //       const element = e.response.histori[i];
        //       element.no = i + 1
        //     }
        //     this.popUpHistoriPelayananPeserta.center().open()
        //     this.datalistHistoriPeserta = new kendo.data.Datalist({
        //       data: e.response.histori,
        //       pageSize: 10,
        //       total: e.response.histori.length,
        //       serverPaging: false,
        //       schema: {
        //         model: {
        //           fields: {
        //           }
        //         }
        //       }
        //     });


        //   } else {
        //     toastr.error(e.metaData.message, 'Info');
        //   }
        //   this.isLoadingNoKartu = false;
        // }, function (err) {
        //   this.isLoadingNoKartu = false;
        // });
      }
      else {

        this.ceklisNomorRujukanMulti(true)
      }
    }
  }
  ceklisNomorRujukanMulti(data) {
    if (data == false) return
    this.listNoRujukanMulti = []
    var jenisFaskes = "rs"
    if (this.model.asalRujukan.id != undefined) {
      if (this.model.asalRujukan.id == 1)
        jenisFaskes = "pcare"
      if (this.model.asalRujukan.id == 2)
        jenisFaskes = "rs"
    }
    if (this.model.noKepesertaan == undefined) return
    this.isRujukan = true
    this.listNoRujukanMulti = []
    this.apiService.get("bridging/bpjs/get-rujukan-" + jenisFaskes + "-nokartu-multi?nokartu=" + this.model.noKepesertaan).subscribe(e => {
      if (e.metaData.code === "200") {
        for (var i = 0; i < e.response.rujukan.length; i++) {
          e.response.rujukan[i].no = i + 1;
        };
        this.listNoRujukanMulti = e.response.rujukan
        // this.listNoRujukanMulti =  e.response.rujukan;

        this.showPilihNomor = true;

      } else {
        this.alertService.error('Info', e.metaData.message);
        // this.alertService.error('Info',e.metaData.message)
      }
      this.isLoadingNoKartu = false;
    }, function (err) {
      this.isLoadingNoKartu = false;
    });
  }
  getHistoriPelayananPesesta(noKartu: any) {
    this.apiService.get("bridging/bpjs/monitoring/HistoriPelayanan/NoKartu/" + noKartu).subscribe(data => {
      if (data.metaData.code == 200) {
        this.listHistori = data.response.histori;

        if (this.listHistori.length > 0) {
          var countKunjungan = 0

          for (let i = 0; i < this.listHistori.length; i++) {
            if (this.model.noRujukan != undefined && this.model.noRujukan.length > 8
              && this.listHistori[i].noRujukan == this.model.noRujukan) {
              countKunjungan = countKunjungan + 1
            }
          }
          var jml = 0
          jml = countKunjungan + 1

          this.alertService.info('Info', 'Kunjungan ke- ' + jml + ' Dengan Rujukan yang sama.')

          this.jenisPel = this.listHistori[0].jnsPelayanan
          var kodeNamaPoli = this.listHistori[0].poli.split(' ');
          if (kodeNamaPoli.length > 0)
            this.apiService.get("bridging/bpjs/get-poli?kodeNamaPoli=" + kodeNamaPoli[0]).subscribe(e => {
              if (e.metaData.code == 200) {
                var resPoli = e.response.poli;
                if (resPoli.length > 0) {
                  for (let i in resPoli) {
                    if (this.listHistori[0].poli == resPoli[i].nama) {
                      this.kdSpesialis = resPoli[i].kode
                      break
                    }
                  }
                  // var kodespe = e.data.response.rujukan.poliRujukan.kode
                  if (this.item.pasien.kdinternal == "IGD") {
                    this.jenisPel = "1"
                  }
                  this.apiService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + this.jenisPel
                    + "&tglPelayanan=" + moment(this.now).format('YYYY-MM-DD') + "&kodeSpesialis=" + this.kdSpesialis).subscribe(data => {
                      if (data.metaData.code == 200) {
                        this.listDPJP2 = data.response.list;
                        this.listDPJP = data.response.list;
                      }
                      else
                        this.alertService.info('Info', 'Dokter DPJP tidak ada')
                    });
                }
              }
            });
        }
      }
      else
        this.listHistori = []
    });

  }
  filterDiagnosa(event) {
    let query = event.query;
    this.apiService.get("registrasi/get-diagnosa-saeutik?kddiagnosa=" + query)
      .subscribe(re => {
        this.listDiagnosa = re;
      })
  }
  filterFaskes(event) {
    let query = event.query;
    if (this.model.asalRujukan == undefined) return
    let json = {
      "url": 'referensi/faskes/' + encodeURI(query) + '/' + this.model.asalRujukan.id,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json)
      .subscribe(x => {
        this.listPpkRujukan = x.response.faskes;
      })
  }
  Sendiriaa(data) {
    this.apiService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
      + "&tglPelayanan=" + moment(this.now).format('YYYY-MM-DD') + "&kodeSpesialis=" + this.kdSpesialis).subscribe(data => {
        if (data.metaData.code == 200) {
          this.listDPJP = data.response.list;
        }
        else
          this.alertService.info('Info', 'Dokter DPJP tidak ada')
      });
  }
  Sendiriaaa(data) {
    this.apiService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
      + "&tglPelayanan=" + moment(this.now).format('YYYY-MM-DD') + "&kodeSpesialis=" + this.kdSpesialis).subscribe(data => {
        if (data.metaData.code == 200) {
          this.listDPJP2 = data.response.list;
        }
        else
          this.alertService.info('Info', 'Dokter DPJP tidak ada')
      });
  }
  checkKepesertaanByNik() {

    if (!this.model.cekNikPeserta) return;
    if (!this.model.noIdentitas) return;
    if (this.model.sendiri) return;
    if (this.model.noIdentitas.length > 16) {
      this.alertService.error('Info', "NIK Lebih Dari 16 Digit");
      return;
    }
    if (this.model.noIdentitas.length < 16) {
      this.alertService.error('Info', "NIK Kurang Dari 16 Digit");
      return;
    }

    this.isLoadingNIK = true;

    this.apiService.get("bridging/bpjs/get-nik?nik=" + this.model.noIdentitas + "&tglsep=" + moment(new Date).format('YYYY-MM-DD')).subscribe(e => {
      if (e.metaData.code === "200") {
        var tglLahir = new Date(e.response.peserta.tglLahir);
        this.model.noKepesertaan = e.response.peserta.noKartu;
        this.model.namaPeserta = e.response.peserta.nama;
        this.model.tglLahir = tglLahir;
        this.model.noIdentitas = e.response.peserta.nik;
        this.model.kelasBridg = {
          id: parseInt(e.response.peserta.hakKelas.kode),
          kdKelas: e.response.peserta.hakKelas.kode,
          nmKelas: e.response.peserta.hakKelas.keterangan,
          namakelas: e.response.peserta.hakKelas.keterangan,
        };

        for (let x = 0; x < this.listKelasDitanggung.length; x++) {
          const element = this.listKelasDitanggung[x];
          if (element.id == e.response.peserta.hakKelas.kode) {
            this.model.kelasDitanggung = element
            break
          }
        }

        this.kodeProvider = e.response.peserta.provUmum.kdProvider;
        this.namaProvider = e.response.peserta.provUmum.nmProvider;
        this.model.namaAsalRujukan = this.kodeProvider + " - " + this.namaProvider;
        this.model.jenisPeserta = e.response.peserta.jenisPeserta.nmJenisPeserta;
        this.alertService.info('Status Peserta', e.response.peserta.statusPeserta.keterangan);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this.isLoadingNIK = false;
    }, function (err) {
      this.isLoadingNIK = false;
    });
  };

  checkDataRujukan() {
    if (!this.model.cekNomorRujukan) return;
    if (!this.model.noRujukan) return;
    if (this.model.sendiri) return;


    this.isLoadingRujukan = true;
    var jenisFaskes = "rs"
    if (this.model.asalRujukan != undefined) {
      if (this.model.asalRujukan.id == 1)
        jenisFaskes = "pcare"
      if (this.model.asalRujukan.id == 2)
        jenisFaskes = "rs"
    }
    this.apiService.get("bridging/bpjs/get-rujukan-" + jenisFaskes + "?norujukan=" + this.model.noRujukan).subscribe(e => {
      // debugger;
      if (e.metaData.code === "200") {
        var tglLahir = new Date(e.response.rujukan.peserta.tglLahir);
        this.model.tglRujukan = new Date(e.response.rujukan.tglKunjungan)
        this.model.noKepesertaan = e.response.rujukan.peserta.noKartu;
        this.model.namaPeserta = e.response.rujukan.peserta.nama;
        this.model.tglLahir = tglLahir;
        this.model.noIdentitas = e.response.rujukan.peserta.nik;
        this.model.kelasBridg = {
          id: parseInt(e.response.rujukan.peserta.hakKelas.kode),
          kdKelas: e.response.rujukan.peserta.hakKelas.kode,
          nmKelas: e.response.rujukan.peserta.hakKelas.keterangan,
          namakelas: e.response.rujukan.peserta.hakKelas.keterangan,
        };
        for (let x = 0; x < this.listKelasDitanggung.length; x++) {
          const element = this.listKelasDitanggung[x];
          if (element.id == e.response.rujukan.peserta.hakKelas.kode) {
            this.model.kelasDitanggung = element
            break
          }
        }

        this.poliRujukanKode = e.response.rujukan.poliRujukan.kode
        this.poliRujukanNama = e.response.rujukan.poliRujukan.nama
        this.kodeProvider = e.response.rujukan.provPerujuk.kode;
        this.namaProvider = e.response.rujukan.provPerujuk.nama;
        this.model.namaAsalRujukan = this.kodeProvider + " - " + this.namaProvider;
        this.model.jenisPeserta = e.response.rujukan.peserta.jenisPeserta.keterangan;
        this.apiService.get("registrasi/get-diagnosa-saeutik?kddiagnosa=" + e.response.rujukan.diagnosa.kode)
          .subscribe(data => {
            this.listDiagnosa.push(data[0])
            this.model.diagnosa = data[0]
          })

        // get Dokter DPJP By Histori
        this.getHistoriPelayananPesesta(e.response.rujukan.peserta.noKartu)
        // end Histori

        // var kodespe = e.response.rujukan.poliRujukan.kode
        // medifirstService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + jenisPel
        //     + "&tglPelayanan=" + new moment(this.now).format('YYYY-MM-DD') + "&kodeSpesialis=" + kdSpesialis).then(function (data) {
        //         if (data.data.metaData.code == 200)
        //             this.listDPJP = data.data.response.list;
        //         else
        //             toastr.info('Dokter DPJP tidak ada', 'Info')
        //     });

        // this.sourceDiagnosa.add({ kddiagnosa: e.response.rujukan.diagnosa.kode });
        // this.model.diagnosa = { kddiagnosa: e.response.rujukan.diagnosa.kode };
        this.alertService.info('Status Peserta', e.response.rujukan.peserta.statusPeserta.keterangan);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this.isLoadingRujukan = false;
    }, function (err) {
      this.isLoadingRujukan = false;
    });
  };

  save() {
    if (this.ceknobpjsdouble == true) {
      this.alertService.error('Peringatan', 'No BPJS Terdeteksi Double, Sesuaikan No BPJS dengan pasiennya')
      return;
    }
    if (this.poliRujukanKode != undefined && this.item.pasien.kdinternal != this.poliRujukanKode) {
      this.model.politujuankode = this.item.pasien.namaruangan
      this.model.poliasalkode = this.poliRujukanNama
      this.model.statuskunjungan = 2
    }
    if (this.model.generateNoSEP) {
      if (this.statusBridgingTemporary == 'false') {
        if (this.poliRujukanKode != undefined && this.item.pasien.kdinternal != this.poliRujukanKode) {

          this.confirmationService.confirm({
            message: 'Pasien Kontrol ? Jika ya maka poli yg dikirim ke BPJS = ' + this.item.pasien.namaruangan
              + ', jika tidak maka ' + this.poliRujukanNama,
            accept: () => {
              this.generateSEP();
            },
            reject: () => {
              this.item.pasien.kdinternal = this.poliRujukanKode
              this.item.pasien.namaruangan = this.poliRujukanNama
              this.generateSEP();
            }
          });

        } else {
          this.generateSEP();
        }

      } else {
        /*
        * dummy SEP
        */
        this.createDummySEP()

      }
    } else {
      this.thisGenerate();
    }
  }

  createDummySEP() {
    if (this.model.noSep == '' || this.model.noSep == undefined) {
      this.apiService.get('bridging/bpjs/generate-sep-dummy?kodeppk=' + this.ppkRumahSakit).subscribe(e => {
        this.model.noSep = e;
        this.model.generateNoSEP = false;
        this.disableSEP = true;
        this.alertService.success('Status', 'Generate SEP Success. No SEP : ' + this.model.noSep);
        this.thisGenerate();
      })
    } else {
      this.model.generateNoSEP = false;
      this.disableSEP = true;
      this.alertService.success('Status', 'Update SEP Success. No SEP : ' + this.model.noSep);
      this.thisGenerate();
    }

  }
  thisGenerate() {
    this.isSimpan = true
    var noasuransi = "";
    if (this.model.noKepesertaan == undefined) {
      noasuransi = '';
    } else
      noasuransi = this.model.noKepesertaan;

    var noidentitas = "";
    if (this.model.noIdentitas == undefined) {
      noidentitas = '';
    } else
      noidentitas = this.model.noIdentitas;

    var diagnosisfk = null;
    if (this.model.diagnosa == undefined) {
      diagnosisfk = null;
    } else
      diagnosisfk = this.model.diagnosa.id;

    var norujukan = "";
    if (this.model.noRujukan == undefined) {
      norujukan = '';
    } else
      norujukan = this.model.noRujukan;

    var noKepesertaans = "";
    if (this.model.noKepesertaan == undefined) {
      noKepesertaans = '';
    } else
      noKepesertaans = this.model.noKepesertaan;


    var tanggalsep = "";
    if (this.model.tglSEP == undefined) {
      tanggalsep = null;
    } else
      tanggalsep = moment(this.model.tglSEP).format('YYYY-MM-DD HH:mm:ss');;

    var tglRujukan = "";
    if (this.model.tglRujukan == undefined) {
      tglRujukan = '';
    } else
      tglRujukan = moment(this.model.tglRujukan).format('YYYY-MM-DD HH:mm:ss');


    var noCM = "";
    if (this.item.pasien.nocm == undefined)
      noCM = ''
    else
      noCM = this.item.pasien.nocm

    var catatans = "";
    if (this.model.catatan == undefined) {
      catatans = '';
    } else
      catatans = this.model.catatan;

    var kdProviders = "-";
    var namaProviders = "-";
    if (this.model.faskesRujukan == true) {
      if (this.model.namaAsalRujukanBrid != undefined) {
        kdProviders = this.model.namaAsalRujukanBrid.kode
        namaProviders = this.model.namaAsalRujukanBrid.nama
      }
    } else {
      if (this.model.namaAsalRujukan != undefined) {
        var arrKdPpkRUjukan = this.model.namaAsalRujukan.split(' - ');
        kdProviders = arrKdPpkRUjukan[0];
        namaProviders = arrKdPpkRUjukan[1];
      }
    }

    if (namaProviders == undefined) {
      namaProviders = "-";
    }

    var id_AsPasien = "";
    if (this.cacheIdAP != undefined) {
      id_AsPasien = this.cacheIdAP;
    }
    if (id_AsPasien == "null") {
      id_AsPasien = ""
    }

    var norec_PA = "";

    if (this.cacheNorecPA != undefined) {
      norec_PA = this.cacheNorecPA;
    }
    if (norec_PA == "null") {
      norec_PA = ""
    }


    var alamatPesertas = "";
    if (this.model.alamatPeserta != undefined)
      alamatPesertas = this.model.alamatPeserta;

    var kelasDitanggungs = null;
    if (this.model.kelasDitanggung != undefined)
      kelasDitanggungs = this.model.kelasDitanggung.id;

    var namaPesertas = "";
    if (this.model.namaPeserta != undefined)
      namaPesertas = this.model.namaPeserta;

    var jenisPesertas = "";
    if (this.model.jenisPeserta != undefined)
      jenisPesertas = this.model.jenisPeserta;
    var lokasiLakaLantas = "";
    if (this.model.lokasiLakaLantas != undefined)
      lokasiLakaLantas = this.model.lokasiLakaLantas;
    else
      lokasiLakaLantas = "";

    var listPenjaminLakas = ""
    if (this.model.lakaLantas) {
      var a = ""
      var b = ""
      for (var i = this.currentListPenjaminLaka.length - 1; i >= 0; i--) {
        var c = this.currentListPenjaminLaka[i].value
        b = "," + c
        a = a + b
      }
      listPenjaminLakas = a.slice(1, a.length)
    }

    var noTelp = "";
    if (this.model.noTelpons != undefined)
      noTelp = this.model.noTelpons;
    var jenisPeserta = ""
    if (this.model.jenisPeserta != undefined)
      jenisPeserta = this.model.jenisPeserta

    var asuransipasien = {
      id_ap: id_AsPasien,
      noregistrasi: this.item.pasien.noregistrasi,
      nocm: noCM,
      alamatlengkap: alamatPesertas,
      objecthubunganpesertafk: this.model.hubunganPeserta.id,
      objectjeniskelaminfk: this.item.pasien.objectjeniskelaminfk,
      kdinstitusiasal: this.model.institusiAsalPasien.id,
      kdpenjaminpasien: this.model.institusiAsalPasien.id,
      objectkelasdijaminfk: kelasDitanggungs,
      namapeserta: namaPesertas,
      nikinstitusiasal: this.model.institusiAsalPasien.id,
      noasuransi: noasuransi,
      alamat: this.item.pasien.alamatlengkap,
      nocmfkpasien: this.item.pasien.nocmfk,
      noidentitas: noidentitas,
      qasuransi: this.item.kelompokPasien.id,
      kelompokpasien: this.item.kelompokPasien.id,
      tgllahir: this.model.tglLahir != undefined ? moment(this.model.tglLahir).format('YYYY-MM-DD') : null,

      kdprovider: kdProviders,
      nmprovider: namaProviders,
      notelpmobile: noTelp,
      jenispeserta: jenisPeserta,
    }

    var pemakaianasuransi = {
      norec_pa: norec_PA,
      noregistrasifk: this.currentNorecPD,
      tglregistrasi: this.item.pasien.tglregistrasi,
      diagnosisfk: diagnosisfk,
      lakalantas: this.model.lakaLantas ? this.model.lakaLantas.id : 0,
      nokepesertaan: noKepesertaans,
      norujukan: norujukan,
      nosep: this.model.noSep != undefined ? this.model.noSep : null,
      tglrujukan: tglRujukan,
      objectdiagnosafk: diagnosisfk,
      tanggalsep: tanggalsep,
      catatan: catatans,
      lokasilaka: lokasiLakaLantas,
      penjaminlaka: listPenjaminLakas,
      cob: this.model.cob != undefined ? this.model.cob : false,
      katarak: this.model.katarak != undefined ? this.model.katarak : false,
      keteranganlaka: this.model.keteranganLaka != undefined ? this.model.keteranganLaka : "",
      tglkejadian: this.model.lakaLantas && this.model.tglLakalantas ? moment(this.model.tglLakalantas).format("YYYY-MM-DD") : null,
      suplesi: this.model.suplesi != undefined ? this.model.suplesi : false,
      nosepsuplesi: this.model.nomorSepSuplesi ? this.model.nomorSepSuplesi : "",
      kdpropinsi: this.model.prov ? this.model.prov.kode : null,
      namapropinsi: this.model.prov ? this.model.prov.nama : null,
      kdkabupaten: this.model.kab ? this.model.kab.kode : null,
      namakabupaten: this.model.kab ? this.model.kab.nama : null,
      kdkecamatan: this.model.kec ? this.model.kec.kode : null,
      namakecamatan: this.model.kec ? this.model.kec.nama : null,
      nosuratskdp: this.model.skdp ? this.model.skdp : "",
      kodedpjp: this.model.dokterDPJP ? this.model.dokterDPJP.kode : null,
      namadpjp: this.model.dokterDPJP ? this.model.dokterDPJP.nama : null,
      prolanisprb: this.model.prolanis !== undefined && this.model.prolanis !== null ? this.model.prolanis : null,
      asalrujukanfk: this.model.asalRujukan != undefined ? this.model.asalRujukan.id : null,
      polirujukankode: this.item.pasien.kdinternal,
      polirujukannama: this.item.pasien.namaruangan,/// this.item.pasien.namaexternal
      kodedpjpmelayani: this.model.DPJPMelayani ? this.model.DPJPMelayani.kode : null,
      namadjpjpmelayanni: this.model.DPJPMelayani ? this.model.DPJPMelayani.nama : null,


      klsrawatnaik: this.model.klsRawatNaik ? this.model.klsRawatNaik.id : null,
      pembiayaan: this.model.pembiayaan ? this.model.pembiayaan.id : null,
      penanggungjawab: this.model.penanggungJawab ? this.model.penanggungJawab : null,
      tujuankunj: this.model.tujuanKunj ? this.model.tujuanKunj.id : null,
      flagprocedure: this.model.flagProcedure ? this.model.flagProcedure.id : null,
      kdpenunjang: this.model.kdPenunjang ? this.model.kdPenunjang.id : null,
      assesmentpel: this.model.assesmentPel ? this.model.assesmentPel.id : null,
      statuskunjungan: this.model.statuskunjungan ? this.model.statuskunjungan : null,
      poliasalkode: this.model.poliasalkode ? this.model.poliasalkode : null,
      politujuankode: this.model.politujuankode ? this.model.politujuankode : null,
    }
    var objSave = {
      asuransipasien: asuransipasien,
      pemakaianasuransi: pemakaianasuransi
    }
    this.apiService.post("registrasi/save-asuransipasien", objSave).subscribe(e => {
      this.isNext = true
      this.isSimpan = false
      this.classsimpan = 'p-md-offset-9'
      var cachePasienDaftars = e.PA.objectasuransipasienfk
        + "~" + e.PA.norec
        + "~" + this.item.pasien.noregistrasi;
      this.cacheHelper.set('CachePemakaianAsuransi', cachePasienDaftars);
      if (this.cachePasienDaftar == undefined) {
        this.cancel();
      }
    }, error => {
      this.isNext = false
    })
  }

  cancel() {
    window.history.back();
  }
  generateSEP() {

    if (!this.model.generateNoSEP) return;

    if (this.model.noKepesertaan == undefined) {
      this.alertService.warn('Info', 'No Peserta harus di isi')
      return
    }
    if (this.model.noTelpons == undefined) {
      this.alertService.warn('Info', 'No Telpon harus di isi')
      return
    }
    if (this.model.kelasDitanggung == undefined) {
      this.alertService.warn('Info', 'Kelas harus di isi')
      return
    }
    // if (this.model.skdp == undefined) {
    //   this.alertService.warn('Info', 'No SKDP harus di isi')
    //   return
    // }
    // if (this.model.dokterDPJP == undefined) {
    //   this.alertService.warn('Info', 'DPJP harus di isi')
    //   return
    // }
    // if (this.model.diagnosa == undefined) {
    //   this.alertService.warn('Info', 'Diagnosa harus di isi')
    //   return
    // }

    // debugger
    var kdJenisPelayanan = "";
    if (this.model.rawatInap === true)
      kdJenisPelayanan = "1";
    else
      kdJenisPelayanan = "2";

    var kddiagnosaawal = "";
    if (this.model.diagnosa != undefined)
      kddiagnosaawal = this.model.diagnosa.kddiagnosa;
    else
      kddiagnosaawal = "";

    var catatan = "";
    if (this.model.catatan != undefined)
      catatan = this.model.catatan;
    else
      catatan = "";

    var polisEksekutif = "";
    if (this.model.poliEksekutif)
      polisEksekutif = "1"
    else
      polisEksekutif = "0"

    var lokasiLakaLantas = "";
    if (this.model.lokasiLakaLantas != undefined)
      lokasiLakaLantas = this.model.lokasiLakaLantas;
    else
      lokasiLakaLantas = "";


    var noTelp = "";
    if (this.model.noTelpons != undefined)
      noTelp = this.model.noTelpons;
    else
      noTelp = "";

    var kdPpkRujukan = "";
    if (this.model.faskesRujukan == true) {
      if (this.model.namaAsalRujukanBrid != undefined) {
        kdPpkRujukan = this.model.namaAsalRujukanBrid.kode;
      }
    } else {
      if (this.model.namaAsalRujukan != undefined) {
        var arrKdPpkRUjukan = this.model.namaAsalRujukan.split(' - ');
        kdPpkRujukan = arrKdPpkRUjukan[0];
      }
    }

    var poliTujuans = "";
    if (this.item.pasien.kdinternal != null)
      poliTujuans = this.item.pasien.kdinternal;
    else
      poliTujuans = "";

    var listPenjaminLakas = ""
    if (this.model.lakaLantas) {
      var a = ""
      var b = ""
      for (var i = this.currentListPenjaminLaka.length - 1; i >= 0; i--) {
        var c = this.currentListPenjaminLaka[i].value
        b = "," + c
        a = a + b
      }
      listPenjaminLakas = a.slice(1, a.length)
    }
    var kdPropinsi = ""
    if (this.model.prov != undefined)
      kdPropinsi = this.model.prov.kode

    var kdKabupaten = ""
    if (this.model.kab != undefined)
      kdKabupaten = this.model.kab.kode

    var kdKecamatan = ""
    if (this.model.kec != undefined)
      kdKecamatan = this.model.kec.kode





    this.isSimpan = true;
    //##Generate SEP
    if (this.model.noSep == '' || this.model.noSep == undefined) {
      var dataSend = {
        "url": "SEP/2.0/insert",
        "method": "POST",
        "data": {
          "request": {
            "t_sep": {
              "noKartu": this.model.noKepesertaan,
              "tglSep": moment(this.model.tglSEP).format('YYYY-MM-DD'),
              "ppkPelayanan": this.ppkRumahSakit.trim(),
              "jnsPelayanan": kdJenisPelayanan,
              "klsRawat": {
                "klsRawatHak": this.model.kelasDitanggung.id,
                "klsRawatNaik": this.model.klsRawatNaik ? this.model.klsRawatNaik.id : "",
                "pembiayaan": this.model.pembiayaan ? this.model.pembiayaan.id : "",
                "penanggungJawab": this.model.penanggungJawab ? this.model.penanggungJawab : ""
              },

              "noMR": this.item.pasien.nocm,
              "rujukan": {
                "asalRujukan": this.model.asalRujukan.id,
                "tglRujukan": this.model.tglRujukan ? moment(this.model.tglRujukan).format('YYYY-MM-DD') : "",
                "noRujukan": this.model.noRujukan ? this.model.noRujukan : "",
                "ppkRujukan": kdPpkRujukan
              },
              "catatan": catatan,
              "diagAwal": kddiagnosaawal,
              "poli": {
                "tujuan": kdJenisPelayanan == '2' ? poliTujuans : "",
                "eksekutif": polisEksekutif
              },
              "cob": {
                "cob": this.model.cob === true ? "1" : "0"
              },
              "katarak": {
                "katarak": this.model.katarak === true ? "1" : "0"
              },
              "jaminan": {
                "lakaLantas": this.model.lakaLantas ? this.model.lakaLantas.id : "0",
                "penjamin": {

                  "tglKejadian": this.model.lakaLantas ? moment(this.model.tglKejadian).format('YYYY-MM-DD') : "",
                  "keterangan": this.model.keteranganLaka ? this.model.keteranganLaka : "",
                  "suplesi": {
                    "suplesi": this.model.suplesi === true ? "1" : "0",
                    "noSepSuplesi": this.model.nomorSepSuplesi ? this.model.nomorSepSuplesi : "",
                    "lokasiLaka": {
                      "kdPropinsi": kdPropinsi,
                      "kdKabupaten": kdKabupaten,
                      "kdKecamatan": kdKecamatan
                    }
                  }
                }
              },
              "tujuanKunj": this.model.tujuanKunj ? this.model.tujuanKunj.id : "",
              "flagProcedure": this.model.flagProcedure ? this.model.flagProcedure.id : "",
              "kdPenunjang": this.model.kdPenunjang ? this.model.kdPenunjang.id : "",
              "assesmentPel": this.model.assesmentPel ? this.model.assesmentPel.id : "",
              "skdp": {
                "noSurat": this.model.skdp ? this.model.skdp : "",
                "kodeDPJP": this.model.dokterDPJP ? this.model.dokterDPJP.kode : ""
              },
              "dpjpLayan": this.model.DPJPMelayani !=null &&  this.model.DPJPMelayani!=undefined ? this.model.DPJPMelayani.kode : "",
              "noTelp": noTelp,
              "user": "Xoxo"
            }
          }
        }
      }
      this.apiService.postNonMessage("bridging/bpjs/tools", dataSend).subscribe(e => {
        if (e.metaData.code == 200) {
          this.model.noSep = e.response.sep.noSep;
          this.model.generateNoSEP = false;
          this.disableSEP = true;
          this.alertService.success('Status', 'Generate SEP Success. No SEP : ' + this.model.noSep);
          this.thisGenerate();
          this.isSimpan = false;
        } else {
          this.isSimpan = false;
          this.alertService.error('Status', e.metaData.message);
        }
      }, function (err) {
      });
    }
    //## Update SEP
    else if (this.model.noSep != undefined) {
      if (this.model.noSep.length > 10) {
        var dataUpdate = {
          "url": "SEP/2.0/update",
          "method": "PUT",
          "data": {
            "request": {
              "t_sep": {
                "noSep": this.model.noSep ? "" : this.model.noSep,
                "klsRawat": {
                  "klsRawatHak": this.model.kelasDitanggung.id,
                  "klsRawatNaik": this.model.klsRawatNaik ? this.model.klsRawatNaik.id : "",
                  "pembiayaan": this.model.pembiayaan ? this.model.pembiayaan.id : "",
                  "penanggungJawab": this.model.penanggungJawab ? this.model.penanggungJawab : ""
                },

                "noMR": this.item.pasien.nocm,

                "catatan": catatan,
                "diagAwal": kddiagnosaawal,
                "poli": {
                  "tujuan": kdJenisPelayanan == '2' ? poliTujuans : "",
                  "eksekutif": polisEksekutif
                },
                "cob": {
                  "cob": this.model.cob == true ? "1" : "0",
                },
                "katarak": {
                  "katarak": this.model.katarak == true ? "1" : "0",
                },

                "jaminan": {
                  "lakaLantas": this.model.lakaLantas ? this.model.lakaLantas.id : "0",
                  "penjamin":
                  {
                    "tglKejadian": this.model.lakaLantas ? moment(this.model.tglKejadian).format('YYYY-MM-DD') : "",
                    "keterangan": this.model.keteranganLaka ? this.model.keteranganLaka : "",
                    "suplesi":
                    {
                      "suplesi": this.model.suplesi == true ? "1" : "0",
                      "noSepSuplesi": this.model.nomorSepSuplesi ? this.model.nomorSepSuplesi : "",
                      "lokasiLaka":
                      {
                        "kdPropinsi": kdPropinsi,
                        "kdKabupaten": kdKabupaten,
                        "kdKecamatan": kdKecamatan
                      }
                    }
                  }
                },
                "dpjpLayan": this.model.DPJPMelayani !=null &&  this.model.DPJPMelayani!=undefined ? this.model.DPJPMelayani.kode : "",
                "noTelp": noTelp,
                "user": "Xoxo"
              }
            }
          }
        }
        this.apiService.postNonMessage("bridging/bpjs/tools", dataUpdate).subscribe(e => {
          if (e.metaData.code == 200) {
            this.model.noSep = e.response.sep.noSep;
            this.model.generateNoSEP = false;
            this.disableSEP = true;
            this.alertService.success('Status', 'Update SEP Success. No SEP : ' + this.model.noSep);
            this.thisGenerate();
            this.isSimpan = false;
          } else {
            this.alertService.error('Status', e.metaData.message);
            this.isSimpan = false;
          }
        }, function (err) {
        });
      }
      //## End Update SEP
    }
  }
  setSep(dataHistorySelect) {
    this.model.noRujukan = dataHistorySelect.noSep
    this.kodeProvider = this.ppkRumahSakit
    this.namaProvider = this.namappkRumahSakit
    this.model.faskesRujukan = false;
    this.model.namaAsalRujukan = this.kodeProvider + " - " + this.namaProvider;
    this.isHistory = false
  }
  historiPelayanan(data) {

    this.dataSourceHistoriPeserta = []
    if (data === true) {

      if (this.model.noKepesertaan == undefined) return
      this.isHistory = true
      this.apiService.get("bridging/bpjs/monitoring/HistoriPelayanan/NoKartu/" + this.model.noKepesertaan).subscribe(data => {
        if (data.metaData.code == 200) {
          for (let i = 0; i < data.response.histori.length; i++) {
            const element = data.response.histori[i];
            element.no = i + 1
          }

          this.dataSourceHistoriPeserta = data.response.histori
        } else {
          this.alertService.info('Info', data.metaData.message);
        }
      })
    }
  }
  setRujukan(data) {
    if (data != undefined) {
      this.model.noRujukan = data.noKunjungan;
      this.model.tglRujukan = new Date(data.tglKunjungan);
      var tglLahir = new Date(data.peserta.tglLahir);
      this.model.namaPeserta = data.peserta.nama;
      this.model.tglLahir = tglLahir;
      this.model.noIdentitas = data.peserta.nik;
      this.model.kelasBridg = {
        id: parseInt(data.peserta.hakKelas.kode),
        kdKelas: data.peserta.hakKelas.kode,
        nmKelas: data.peserta.hakKelas.keterangan,
        namakelas: data.peserta.hakKelas.keterangan,
      };
      for (let x = 0; x < this.listKelasDitanggung.length; x++) {
        const element = this.listKelasDitanggung[x];
        if (element.id == data.peserta.hakKelas.kode) {
          this.model.kelasDitanggung = element
          break
        }
      }

      this.poliRujukanKode = data.poliRujukan.kode
      this.poliRujukanNama = data.poliRujukan.nama
      this.kodeProvider = data.provPerujuk.kode;
      this.namaProvider = data.provPerujuk.nama;
      this.model.faskesRujukan = false;
      this.model.namaAsalRujukan = this.kodeProvider + " - " + this.namaProvider;
      this.model.jenisPeserta = data.peserta.jenisPeserta.keterangan;
      this.model.prolanis = data.peserta.informasi.prolanisPRB;
      this.model.noTelpons = data.peserta.mr.noTelepon;
      this.model.tglRujukan = new Date(data.tglKunjungan);

      this.alertService.info('Status Peserta', data.peserta.statusPeserta.keterangan);

      this.apiService.get("registrasi/get-diagnosa-saeutik?kddiagnosa=" + data.diagnosa.kode)
        .subscribe(xx => {
          this.listDiagnosa.push(xx[0])
          this.model.diagnosa = xx[0]
        })
      this.apiService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
        + "&tglPelayanan=" + moment(this.now).format('YYYY-MM-DD') + "&kodeSpesialis="
        + this.poliRujukanKode).subscribe(z => {
          if (z.metaData.code == 200) {
            this.listDPJP2 = z.response.list;
            this.listDPJP = z.response.list;
          }
          else
            this.alertService.info('Info', 'Dokter DPJP tidak ada')
        });

      this.isRujukan = false
    }
  }
  generateSKDP(data) {
    if (data === true) {
      this.model.cekNoSkdp = true
      // this.apiService.get("bridging/bpjs/generateskdp").subscribe(dat => {
      //   var noSKDP = dat.noskdp
      //   if (noSKDP != undefined)
      //     this.model.skdp = noSKDP
      // })
      this.kontrol = {
        tglAwal: new Date(),
        tglAkhir: new Date()
      }

      this.kontrol.filter = this.listFilter[0]
      // this.loadGridKontrol()
      this.cariRencana()
    } else {
      delete this.model.skdp
    }
  }
  cekLaka(e) {
    if (e == true) {
      let json = {
        "url": "referensi/propinsi",
        "method": "GET",
        "data": null
      }
      this.listProv = []
      this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
        this.listProv = e.response.list
      })

    }
  }
  setData(param, data) {
    if (param == 'kab') {
      let json = {
        "url": "referensi/kabupaten/propinsi/" + data.kode,
        "method": "GET",
        "data": null
      }
      this.listKab = []
      this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {

        this.listKab = e.response.list
      })

    } else {
      let json = {
        "url": "referensi/kecamatan/kabupaten/" + data.kode,
        "method": "GET",
        "data": null
      }
      this.listKec = []
      this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {

        this.listKec = e.response.list
      })
    }
  }
  cariRencana() {
    this.popUpRSPRI = true
    this.popCetak = false
    this.kon.noKartu = this.model.noKepesertaan ?this.model.noKepesertaan :''
    this.kon.sep = this.model.noSep ?this.model.noSep :''
    this.clickRad(this.kon.jenisPelayanan)

    this.loadGridKontrol()
  }


  clickRad(e) {
    if (e == '2') {
      this.cariSep()
    } else {
      this.cariNoka()
    }

  }
  cariNoka() {
    this.enabledDetail = false
    this.enabledDetail2 = false
    if (this.kon.noKartu == undefined) return
    if (this.kon.noKartu == '') return
    let json = {

      "url": "Peserta/nokartu/" + this.kon.noKartu + "/tglSEP/" + moment(new Date()).format('YYYY-MM-DD'),
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {

      if (e.metaData.code === "200") {
        this.enabledDetail2 = true;
        this.kon.peserta = e.response.peserta
        this.alertService.info('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  filterAutoCom(event, dataSource, url, balikan) {
    if (event.query == '') return
    if (event.query.length < 3) return
    let json = {
      "url": url,
      "method": "GET",
      "data": null
    }
    // this[dataSource] = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this[dataSource] = e.response[balikan]
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }

    })
  }
  cariSep() {
    this.enabledDetail = false
    this.enabledDetail2 = false
    if (this.kon.sep == undefined) return
    if (this.kon.sep == '') return
    let json = {
      "url": "RencanaKontrol/nosep/" + this.kon.sep,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code === "200") {
        this.enabledDetail = true;
        this.kon.noSep = e.response.noSep
        this.kon.jnsPelayanan = e.response.jnsPelayanan
        this.kon.tglSep = e.response.tglSep
        this.kon.poli = e.response.poli
        this.kon.diagnosa = e.response.diagnosa
        this.kon.noKartu = e.response.peserta.noKartu
        this.kon.nama = e.response.peserta.nama
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  loadGridKontrol() {

    var tglAwal = moment(this.kontrol.tglAwal).format('YYYY-MM-DD')
    var tglAkhir = moment(this.kontrol.tglAkhir).format('YYYY-MM-DD')

    let json = {
      "url": "RencanaKontrol/ListRencanaKontrol/tglAwal/" + tglAwal + "/tglAkhir/" + tglAkhir + "/filter/" + this.kontrol.filter.kode,
      "method": "GET",
      "data": null
    }

    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      var dataKon = []
      if (e.metaData.code == 200) {
        for (var i = e.response.list.length - 1; i >= 0; i--) {
          const element = e.response.list[i]
          if (this.model.noKepesertaan == element.noKartu) {
            dataKon.push(element)
          }
        }
      } else this.alertService.success('Rencana Kontrol/SPRI', e.metaData.message);
      if (dataKon.length > 0) {
        for (let x = 0; x < dataKon.length; x++) {
          const element = dataKon[x];
          element.no = x + 1
        }
      } else {
        this.alertService.success('Rencana Kontrol/SPRI', "Data tidak ditemukan");
      }
     
      this.dataSourceSPRI = dataKon
    })
  }

  setKontrol(data) {
    if (data != undefined) {
      this.model.skdp = data.noSuratKontrol
      if (this.listDPJP == undefined || this.listDPJP.length == 0) {
        this.listDPJP = [{ kode: data.kodeDokter, nama: data.namaDokter }]

      } else {
        if (this.listDPJP.length > 0) {
          var status = false
          for (let x = 0; x < this.listDPJP.length; x++) {
            const element = this.listDPJP[x];
            if (element.kode == data.kodeDokter) {
              status = true
            }
          }
          if (status == false) {
            this.listDPJP = [{ kode: data.kodeDokter, nama: data.namaDokter }]
          }
        }
      }
      this.model.dokterDPJP = { kode: data.kodeDokter, nama: data.namaDokter }
      this.popUpRSPRI = false
    }
  }
  selectPoli(eve) {

    let json = {
      "url": "RencanaKontrol/JadwalPraktekDokter/JnsKontrol/" + this.kon.jenisPelayanan
        + "/KdPoli/" + eve.kode + "/TglRencanaKontrol/" + moment(this.kon.tglRencanaKontrol).format('YYYY-MM-DD'),
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == 200) {
        for (let x = 0; x < e.response.list.length; x++) {
          const element = e.response.list[x];
          element.kode = element.kodeDokter
          element.nama = element.namaDokter
        }
        this.listDPJPKontrol = e.response.list;
      }
      else {
        this.alertService.error('Info', e.metaData.message);
      }

    })
  }
  post(url, method, jsonView) {
    let jenis = method
    let data = {}
    if (method == 'DELETE') {
      data = {
        "request": {
          "t_sep": {
            "noSep": this.kon.delete,
            "user": "Xoxo"
          }
        }
      }
    }

    if (method == 'insertRencana') {
      if (this.kon.noSuratKontrol) {
        url = 'RencanaKontrol/Update'
        method = 'PUT'
        data = {
          "request": {
            "noSuratKontrol": this.kon.noSuratKontrol,
            "noSEP": this.kon.noSep,
            "kodeDokter": this.kon.kodeDokter ? this.kon.kodeDokter.kode : "",
            "poliKontrol": this.kon.poliKontrol ? this.kon.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.kon.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      } else {
        method = 'POST'
        data = {
          "request":
          {
            "noSEP": this.kon.noSep,
            "kodeDokter": this.kon.kodeDokter ? this.kon.kodeDokter.kode : "",
            "poliKontrol": this.kon.poliKontrol ? this.kon.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.kon.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      }
    }
    if (method == 'insertSPRI') {
      if (this.kon.noSuratKontrol) {
        url = 'RencanaKontrol/UpdateSPRI'
        method = 'PUT'
        data = {
          "request": {
            "noSPRI": this.kon.noSuratKontrol,
            "kodeDokter": this.kon.kodeDokter ? this.kon.kodeDokter.kode : "",
            "poliKontrol": this.kon.poliKontrol ? this.kon.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.kon.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      } else {
        method = 'POST'
        data = {
          "request":
          {
            "noKartu": this.kon.noKartu,
            "kodeDokter": this.kon.kodeDokter ? this.kon.kodeDokter.kode : "",
            "poliKontrol": this.kon.poliKontrol ? this.kon.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.kon.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      }
    }
    let json = {
      "url": url,
      "method": method,
      "data": data
    }
    this.disabledCetak = true
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
        this.disabledCetak = false
        if (this.kon.noSuratKontrol) {
          this.resCetak = this.kon.noSuratKontrol
        } else {
          if (url == 'RencanaKontrol/UpdateSPRI' || url == 'RencanaKontrol/InsertSPRI') {
            this.resCetak = e.response.noSPRI
          } else {
            this.resCetak = e.response.noSuratKontrol
          }

        }


      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this[jsonView] = JSON.stringify(e, undefined, 4);
    })
  }
  cetakAfter() {

    let json = { "url": 'RencanaKontrol/noSuratKontrol/' + this.resCetak, "method": "GET", "data": null }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(res2 => {
      if (res2.metaData.code == "200") {
        let datas = res2.response
        let e = {
          'noSuratKontrol': datas.noSuratKontrol,
          'tglRencanaKontrol': datas.tglRencanaKontrol,
          'noKartu': this.kon.noKartu,
          'tglTerbitKontrol': datas.tglTerbit,
          'namaPoliTujuan': datas.namaPoliTujuan,
          'namaDokter': datas.namaDokter,
          'jnsPelayanan': datas.jnsKontrol,
          'jnsKontrol': datas.jnsKontrol,
        }
        this.cetak(e)
      }
    })

  }
  cetak(e) {
    this.resCetak = e.noSuratKontrol
    var nosuratkontrol = e.noSuratKontrol
    var tglrencanakontrol = e.tglRencanaKontrol
    var txttglentrirencanakontrol = e.tglTerbitKontrol
    var noka = e.noKartu
    var diag = '-'
    let ket = ''
    let namadiag = ''

    let dpjpSEPasal = ''
    let dari = new Date(new Date().setDate(new Date().getDate() - 31))

    let json = { "url": "Peserta/nokartu/" + e.noKartu + "/tglSEP/" + moment(new Date()).format('YYYY-MM-DD'), "method": "GET", "data": null }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(res => {
      if (res.metaData.code == "200") {
        let json = {
          "url": "monitoring/HistoriPelayanan/NoKartu/" + e.noKartu + "/tglMulai/" + moment(dari).format('YYYY-MM-DD') + "/tglAkhir/" + moment(new Date()).format('YYYY-MM-DD'),
          "method": "GET",
          "data": null
        }
        this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(res2 => {
          if (res2.metaData.code == "200") {
            if (res2.response.histori.length > 0) {
              diag = res2.response.histori[0].diagnosa
              namadiag = res2.response.histori[0].diagnosa
              dpjpSEPasal = e.namaDokter;// res2.response.histori[0].namaDokter
              this.dateHelper.cetakSuratKontrol(nosuratkontrol, tglrencanakontrol, txttglentrirencanakontrol, noka, res.response.peserta.nama, res.response.peserta.tglLahir,
                this.namappkRumahSakit, e.namaPoliTujuan, res.response.peserta.sex, namadiag, ket,
                e.jnsKontrol, diag, tglrencanakontrol, e.namaDokter, dpjpSEPasal, e.jnsPelayanan);
            }

          } else {
            this.dateHelper.cetakSuratKontrol(nosuratkontrol, tglrencanakontrol, txttglentrirencanakontrol, noka, res.response.peserta.nama, res.response.peserta.tglLahir,
              this.namappkRumahSakit, e.namaPoliTujuan, res.response.peserta.sex, namadiag, ket,
              e.jnsKontrol, diag, tglrencanakontrol, e.namaDokter, dpjpSEPasal, e.jnsPelayanan);
          }
        })

      } else {

      }
    })
  }
  hapusRuj(datas) {
    let json = {
      "url": "RencanaKontrol/Delete",
      "method": "DELETE",
      "data":
      {
        "request": {
          "t_suratkontrol": {
            "noSuratKontrol": datas.noSuratKontrol,
            "user": "Xoxo"
          }
        }
      }
    }
    this.apiService.postNonMessage('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code === "200") {
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this.loadGridKontrol()
    });
  }
  edit(e) {
    this.dataSelected = e

    this.kon.sep = this.dataSelected.noSepAsalKontrol
    this.kon.noKartu = this.dataSelected.noKartu
    this.cariSep()
    this.kon.tglRencanaKontrol = new Date(this.dataSelected.tglRencanaKontrol)
    if (this.dataSelected.jnsKontrol == '2') {
      this.cariSep()
      this.kon.jenisPelayanan = this.dataSelected.jnsKontrol
    } else {
      this.cariNoka()
      this.kon.jenisPelayanan = this.dataSelected.jnsKontrol
    }

    this.kon.noSuratKontrol = this.dataSelected.noSuratKontrol
    this.kon.poliKontrol = { kode: this.dataSelected.poliAsal, nama: this.dataSelected.namaPoliAsal }
    this.listDPJP = [{ kode: this.dataSelected.kodeDokter, nama: this.dataSelected.namaDokter }]
    this.kon.kodeDokter = { kode: this.dataSelected.kodeDokter, nama: this.dataSelected.namaDokter }
    this.myVar = true
    this.index = 1

  }
  clear() {
    delete this.kon.poliKontrol
    delete this.kon.tglRencanaKontrol
    delete this.kon.kodeDokter
  }
}