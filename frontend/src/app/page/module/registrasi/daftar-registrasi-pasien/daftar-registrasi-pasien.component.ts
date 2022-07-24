import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-daftar-registrasi-pasien',
  templateUrl: './daftar-registrasi-pasien.component.html',
  styleUrls: ['./daftar-registrasi-pasien.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarRegistrasiPasienComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  pencarian: any = '';
  listData: any[];
  totalRecords: number;
  item: any = {};
  loading: boolean;
  sortField: any;
  sortOrder: any;
  dataLogin: any;
  kelUser: any;
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  listRuanganApd: any[];
  listRuanganBr: any[];
  dateNow: any;
  column: any[];
  pop_inputTindakan: boolean;
  pop_inputDiagnosa: boolean;
  pop_DokterPJawab: boolean;
  listDokter: any[];
  listKriteria: any[] = [
    { name: 'Semua', id: '1' }, { name: 'Masih Dirawat', id: '2' }
  ]
  disableTgl: boolean
  listPembatalan: any[]
  pop_batalPeriksa: boolean
  pop_transaksiLayanan: boolean
  pop_transaksiBuktiLayanan: boolean
  listRuanganTL: any[];
  listBtn: MenuItem[];
  pop_cetakLabelPasien: boolean;
  popFilter: boolean;
  listBtnAk: MenuItem[];
  norecPD: any;
  listcetakan: MenuItem[];
  ppkRumahSakit = ''
  namappkRumahSakit = ''
  statusBridgingTemporary = 'false'
  popSPRI: boolean = false
  listFilter = [{ kode: 2, nama: 'Tgl Rencana Kontrol' }, { kode: 1, nama: 'Tgl Entri' }]
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
  daftar: any = {
    periodeAwal: new Date(),
    periodeAkhir: new Date(),

  }
  myVar: boolean = false
  namaPPk: any
  columnSPRI: any = []
  dataSourceRen: any = []
  enabledDetail: boolean = false
  enabledDetail2: boolean = false
  listDPJP: any = []
  disabledCetak: boolean = true
  resCetak: any = {}
  index: any = 0
  dataSelected: any
  popCetak: boolean;
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private helper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit() {


    this.listBtn = [

      { label: 'Bukti Layanan', icon: 'pi pi-print', command: () => { this.cetakBuktiLayanan(); } },
      { label: 'Label Pasien', icon: 'pi pi-print', command: () => { this.cetakLabelPasien(); } },
      { label: 'Kartu Pasien', icon: 'pi pi-print', command: () => { this.cetakKartu(); } },
      { label: 'Gelang Pasien', icon: 'pi pi-print', command: () => { this.cetakGelang(); } },
      { label: 'Tracer', icon: 'pi pi-print', command: () => { this.cetakTracer(); } },
      { label: 'Blanko BPJS', icon: 'pi pi-print', command: () => { this.cetakBlanko(); } },
      { label: 'Cetak SEP', icon: 'pi pi-print', command: () => { this.cetakSep(); } },
      { label: 'Nomor Antrian', icon: 'pi pi-print', command: () => { this.cetakNomorAntrian(); } },
      { label: 'Identitas Pasien', icon: 'pi pi-print', command: () => { this.cetakIdentitas(); } },
      { label: 'Lembar Rawat Inap', icon: 'pi pi-print', command: () => { this.cetakLembar(); } },
      { label: 'Summary List', icon: 'pi pi-print', command: () => { this.cetakSummary(); } },
      { label: 'Status Triage', icon: 'pi pi-print', command: () => { this.cetakTriage(); } },
      { separator: true },
      { label: 'Rencana Kontrol/SPRI', icon: 'pi pi-fw pi-arrow-circle-up', command: () => { this.buatSPRI(); } },
    ];

    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    if (this.kelUser == 'laboratorium' || this.kelUser == 'radiologi' || this.kelUser == 'bedah') {
      this.listBtnAk = [
        { label: 'Transaksi Pelayanan', icon: 'fa fa-stethoscope', command: () => { this.popUpTransaksiLayanan(); } },
        { separator: true },
        { label: 'Label Pasien', icon: 'pi pi-print', command: () => { this.cetakLabelPasien(); } },
        { label: 'Cetak SEP', icon: 'pi pi-print', command: () => { this.cetakSep(); } },
        { label: 'Bukti Pelayanan', icon: 'pi pi-print', command: () => { this.cetakBuktiLayanan(); } },
      ];
    } else {
      this.listBtnAk = [
        { label: 'Pemakaian Asuransi', icon: 'fa fa-pencil-square-o', command: () => { this.editAsuransi(); } },
        { label: 'Ubah Dokter', icon: 'fa fa-user-md', command: () => { this.popUpUbahDokter(); } },
        { label: 'Input Diagnosa', icon: 'fa fa-medkit', command: () => { this.popUpinputDiagnosa(); } },
        { label: 'Input Tindakan', icon: 'fa fa-medkit', command: () => { this.popUpInputTindakan(); } },

        { separator: true },

        { label: 'Rencana Kontrol/SPRI', icon: 'pi pi-fw pi-arrow-circle-up', command: () => { this.buatSPRI(); } },
        { separator: true },
        { label: 'Label Pasien', icon: 'pi pi-print', command: () => { this.cetakLabelPasien(); } },
        { label: 'Kartu Pasien', icon: 'pi pi-print', command: () => { this.cetakKartu(); } },
        { label: 'Gelang Pasien', icon: 'pi pi-print', command: () => { this.cetakGelang(); } },
        { label: 'Tracer', icon: 'pi pi-print', command: () => { this.cetakTracer(); } },
        { label: 'Nomor Antrian', icon: 'pi pi-print', command: () => { this.cetakNomorAntrian(); } },
        { label: 'Cetak SEP', icon: 'pi pi-print', command: () => { this.cetakSep(); } },
        { label: 'Bukti Pelayanan', icon: 'pi pi-print', command: () => { this.cetakBuktiLayanan(); } },
        { label: 'Identitas Pasien', icon: 'pi pi-print', command: () => { this.cetakIdentitas(); } },
        { label: 'Pernyataan Jampersal', icon: 'pi pi-print', command: () => { this.cetakJampersal(); } },
        { label: 'Persetujuan Rawat Inap', icon: 'pi pi-print', command: () => { this.cetakPersetujuanRanap(); } },
        { label: 'Formulir Rawat Jalan', icon: 'pi pi-print', command: () => { this.cetakFormulirRajal(); } },
      ];
    }

    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'norm', header: 'No RM', width: "80px", filter: true },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px", filter: true },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px", filter: true },
      { field: 'namakelas', header: 'Kelas', width: "100px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'namadokter', header: 'Dokter', width: "180px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "120px" },
      { field: 'namarekanan', header: 'Penjamin', width: "120px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "140px" },
      { field: 'jenispelayanan', header: 'Jenis Pelayanan', width: "140px" },
      { field: 'kelasditanggung', header: 'Kls Ditanggung', width: "180px" },
      { field: 'keterangan', header: 'Keterangan', width: "180px" },
      { field: 'nosep', header: 'SEP', width: "120px" },
      { field: '', header: 'Diagnosa', width: "140px" },
    ];
    this.columnSPRI = [

      { field: 'noSuratKontrol', header: 'No Surat', width: "200px" },
      { field: 'namaJnsKontrol', header: 'Jenis', width: "125px", filter: true },
      { field: 'tglRencanaKontrol', header: 'Tgl Rencana Kontrol', width: "125px", filter: true },
      { field: 'noSepAsalKontrol', header: ' No SEP Asal', width: "200px", filter: true },
      { field: 'namaPoliAsal', header: 'Poli Asal ', width: "200px" },
      { field: 'namaPoliTujuan', header: 'Poli Tujuan', width: "200px" },
      { field: 'namaDokter', header: 'DPJP', width: "200px" },

    ];

    this.getDataCombo();
  }

  cetakTriage() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    var stt = 'false'
    if (confirm('View Triage IGD? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-triage=1&noregistrasi=" + this.selected.noregistrasi
      + "&view=" + stt, function (e) { });
  }

  cetakSummary() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    var stt = 'false'
    if (confirm('View Summary List? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-summarylist=1&nocm=" + this.selected.norm
      + "&view=" + stt, function (e) { });
  }

  cetakLembar() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    var stt = 'false'
    if (confirm('View Persejutuan Rawat Inap? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-surat-pernyataan-ranap=1&noregistrasi=" + this.selected.noregistrasi
      + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }

  cetakIdentitas() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    var stt = 'false'
    if (confirm('View Indentitas Pasien? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-lembar-identitas=1&nocm=" + this.selected.norm
      + "&noregistrasi=" + this.selected.noregistrasi + "&tipePasien=" + this.selected.kelompokpasien + "&idpegawai="
      + this.authService.getDataLoginUser().pegawai.namaLengkap + "&view=" + stt, function (e) { });
  }

  cetakNomorAntrian() {
    if (this.selected == undefined) {
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
      this.selected.noregistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }

  // cetakSep() {
  //   if (this.selected == undefined) {
  //     this.alertService.error('Info', "Pilih data dulu")
  //     return
  //   }
  //   if (this.selected.kelompokpasien != "BPJS") {
  //     this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
  //     return
  //   }
  //   this.apiService.get("general/get-data-pemakaian-asuransi-pasien?noregistrasi=" + this.selected.noregistrasi).subscribe(data => {        
  //       var datas = data;
  //       if (data[1] == false || data[1] == "false") {
  //         this.alertService.error('Info', "Pasien belum memiliki no sep, harap input pemakaian asuransi dahulu!")
  //         return
  //       }else{
  //          var stt = 'false'
  //           if (confirm('View SEP? ')) {
  //             // Save it!
  //             stt = 'true';
  //           } else {
  //             // Do nothing!
  //             stt = 'false'
  //           }
  //           this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-sep=1&noregistrasi=" +
  //             this.selected.noregistrasi + "&qty=1&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
  //             + "&ket=&view=" + stt, function (e) {
  //          });
  //       }       
  //   }) 
  // }
  cetakSep() {
    var profile = this.authService.dataLoginUser.profile.id;
    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    if (this.selected.kelompokpasien != "BPJS") {
      this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
      return
    }
    this.apiService.get("general/get-data-pemakaian-asuransi-pasien?noregistrasi=" + this.selected.noregistrasi).subscribe(data => {
      var sep = data[0];
      if (data[1] == false || data[1] == "false") {
        this.alertService.error('Info', "Pasien belum memiliki no sep, harap input pemakaian asuransi dahulu!")
        return
      } else {
        // var stt = 'false'
        // if (confirm('View SEP? ')) {
        //   // Save it!
        //   stt = 'true';
        // } else {
        //   // Do nothing!
        //   stt = 'false'
        // }
        // window.open(Config.get().apiBackend + "print/cetak-sep?noregistrasi=" + this.selected.noregistrasi
        // + '&kodeprofile=' + profile);
        //   this.apiService.getUrlCetak("http://127.0.0.1:9495/print/routes?cetak-sep=1&noregistrasi=" +
        //     this.selected.noregistrasi + "&qty=1&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
        //     + "&ket=&view=" + stt, function (e) {
        //  });

        var nosep = sep.nosep
        var nmperujuk = sep.nmprovider

        var tglsep = moment(new Date(sep.tanggalsep)).format('YYYY-MM-DD')
        var nokartu = sep.nokepesertaan + '  ( MR. ' + this.selected.norm + ' )';
        var nmpst = this.selected.namapasien
        var tgllahir = moment(new Date(this.selected.tgllahir)).format('YYYY-MM-DD')
        var jnskelamin = '  Kelamin : ' + this.selected.jeniskelamin;
        var poli = this.selected.isranap == true ? '-' : this.selected.namaruangan;
        var faskesperujuk = this.selected.isranap == true ? this.namappkRumahSakit : nmperujuk;
        var notelp = sep.notelpmobile ? sep.notelpmobile : ''
        var dxawal = sep.kddiagnosa + '-' + sep.namadiagnosa.substring(0, 45);
        var catatan = sep.catatan ? sep.catatan : ''
        var jnspst = sep.jenispeserta ? sep.jenispeserta : ''
        var FLAGCOB = sep.cob
        var cob = '-';
        if (FLAGCOB) {
          cob = null
        }

        //cob non aktif
        var FLAGNAIKKELAS = sep.klsrawatnaik ? 1 : 0
        var klsrawat_naik = sep.klsrawatnaik ? sep.klsrawatnaik : ""

        var jnsrawat = this.selected.isranap == true ? 'R.Inap' : 'R.Jalan';
        var klsrawat = sep.namakelas ? sep.namakelas : '-';
        var prolanis = sep.prolanisprb ? sep.prolanisprb : ""
        var eksekutif = sep.eksekutif ? ' (Poli Eksekutif)' : '';
        //var penjaminJR = $('#c  hkjaminan_JR').is(":checked") == true ? 'Jasa Raharja PT' : '';
        //var penjaminTK = $('#chkjaminan_BPJSTK').is(":checked") == true ? 'BPJS Ketenagakerjaan' : '';
        //var penjaminTP = $('#chkjaminan_TASPEN').is(":checked") == true ? 'PT TASPEN' : '';
        //var penjaminAS = $('#chkjaminan_ASABRI').is(":checked") == true ? 'ASABRI' : '';
        var katarak = sep.katarak ? '1' : '0';
        var potensiprb = sep.prolanisprb ? sep.prolanisprb : ""
        var statuskll = sep.lakalantas ? sep.lakalantas : ""

        var dokter = (this.selected.isranap == true) ? sep.namadpjp ? sep.namadpjp : "" : sep.namadjpjpmelayanni ? sep.namadjpjpmelayanni : "";
        var FLAGPROSEDUR = sep.flagprocedure ? sep.flagprocedure : ""

        var kunjungan = this.selected.isranap == true ? 3 : 1;

        var isrujukanthalasemia_hemofilia = 0

        if (sep.polirujukankode == 'UGD' || sep.polirujukankode == 'IGD' || sep.poliasalkode == 'UGD' || sep.poliasalkode == 'IGD') {
          nmperujuk = '';
          kunjungan = 0;
          FLAGPROSEDUR = null;
        }

        //var sepdate = new Date(tglsep);
        //var currDate = new Date(dataSEP.sep.sep.FDATE);
        //var backdate = sepdate < new Date(currDate.getFullYear(), currDate.getMonth(), currDate.getDate()) ? " (BACKDATE)" : "";

        var backdate = this.helper.cekBackdate(tglsep, sep.tglcreate ? sep.tglcreate : tglsep);
        var ispotensiHEMOFILIA_cetak = 0
        var _kodejaminan = '-';
        this.helper.cetakSEP(nosep + backdate, tglsep, nokartu, nmpst, tgllahir, jnskelamin, notelp, poli, faskesperujuk, dxawal, catatan, jnspst, cob, jnsrawat, klsrawat,
          prolanis, eksekutif, _kodejaminan, statuskll, katarak, potensiprb, dokter, kunjungan, FLAGPROSEDUR, "-", FLAGNAIKKELAS, klsrawat_naik, isrujukanthalasemia_hemofilia, ispotensiHEMOFILIA_cetak,
          this.namappkRumahSakit);


      }
    })
  }
  cetakBlanko() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    if (this.selected.kelompokpasien != "BPJS") {
      this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
      return
    }
    var stt = 'false'
    if (confirm('View Blangko BPJS? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-blangko-bpjs=1&noregistrasi=" +
      this.selected.noregistrasi + "&qty=1&idpegawai=" + this.authService.getDataLoginUser().pegawai.id
      + "&ket=&view=" + stt, function (e) {
      });
  }
  cetakTracer() {
    if (this.selected == undefined) {
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
      this.selected.noregistrasi + "&view=" + stt, function (e) { });
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
      this.selected.noregistrasi + "&qty=1" + "&view=" + stt, function (e) { });
  }
  cetakKartu() {
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-kartu-pasien=1&norm=" +
      this.selected.norm, function (e) { });
  }
  cetakLabelPasien() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
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
      this.selected.noregistrasi + "&qty=" + this.item.qtyPrint + "&view=" + stt, function (e) { });
    this.pop_cetakLabelPasien = false;
  }
  cetakBuktiLayanan() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    this.listRuanganBr = []
    this.apiService.get("registrasi/get-data-antrian-pasien?noregistrasi=" + this.selected.noregistrasi).subscribe(data => {
      var datas = data;
      this.listRuanganBr = datas.ruangan;
      if (datas.ruangan != undefined) {
        this.item.dataRuanganBr = datas.ruangan[0]
        this.pop_transaksiBuktiLayanan = true
      }
    })
  }
  cetakBuktiLayananPrint() {
    if (this.item.dataRuanganBr == undefined) {
      this.alertService.error('Info', "Ruangan Masih Belum Diisi!");
      return;
    }

    if (this.selected.noregistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }
    var stt = 'false'
    if (confirm('View Bukti Pelayanan? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-buktilayanan=1&noregistrasi=" +
      this.selected.noregistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&idRuangan=" + this.item.dataRuanganBr.ruanganidfk + "&view=" + stt, function (e) { });
    this.listRuanganBr = [];
    this.item.dataRuanganBr = undefined;
    this.pop_transaksiBuktiLayanan = false;

  }
  getDataCombo() {
    this.apiService.get("registrasi/get-data-combo-operator").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.listPembatalan = dataCombo.pembatalan;
      this.namappkRumahSakit = dataCombo.namaPPKRujukan
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarRegistrasiPasienCtrl');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.item.status = chacePeriode[2]
      this.item.namaOrReg = chacePeriode[3]
      if (chacePeriode[6] != undefined) {
        this.listDepartemen = [chacePeriode[6]]
        this.item.dataDepartemen = chacePeriode[6]
      }
      if (chacePeriode[5] != undefined) {
        this.listRuangan = [chacePeriode[5]]
        this.item.dataRuangan = chacePeriode[5]
      }

      if (chacePeriode[4] != undefined && chacePeriode[4] != "") {
        this.item.noReg = chacePeriode[4]
      }
      if (chacePeriode[7] != undefined && chacePeriode[7] != "") {
        this.item.noRm = chacePeriode[7]
      }
      if (chacePeriode[8] != undefined && chacePeriode[8] != "") {
        this.item.jmlRows = chacePeriode[8]
      }
      this.getData();
    }
    else {
      this.getData();
    }
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.dataDepartemen.ruangan;
    }
  }
  cari() {
    this.getData()
  }
  getData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');

    var tempRuanganId = "";
    var tempRuanganIdArr = undefined;
    if (this.item.dataRuangan != undefined) {
      tempRuanganId = this.item.dataRuangan.id;
      tempRuanganIdArr = { id: this.item.dataRuangan.id, ruangan: this.item.dataRuangan.ruangan }
    }

    var tempStatus = "";
    var tempStatusArr = undefined;
    // if (this.item.status != undefined) {
    //   tempStatus = this.item.status.namaExternal;
    //   tempStatusArr = { id: this.item.status.id, namaExternal: this.item.status.namaExternal }
    // }

    var tempInstalasiId = "";
    var tempInstalasiIdArr = undefined;
    if (this.item.dataDepartemen != undefined) {
      tempInstalasiId = this.item.dataDepartemen.id;
      tempInstalasiIdArr = { id: this.item.dataDepartemen.id, departemen: this.item.dataDepartemen.departemen }
    }

    var kelompokPasienId = ""
    if (this.item.dataKelPasien != undefined) {
      kelompokPasienId = this.item.dataKelPasien.id
    }

    var tempNoRm = "";
    if (this.item.noRM != undefined) {
      tempNoRm = this.item.noRM;
    }

    var tempNoReg = "";
    if (this.item.Noregistrasi != undefined) {
      tempNoReg = this.item.Noregistrasi;
    }

    var tempNamaOrReg = "";
    if (this.item.namaPasien != undefined) {
      tempNamaOrReg = this.item.namaPasien;
    }

    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = this.item.jmlRows
    }
    var isCanBalik = false
    if (this.item.isCanBalik != undefined) {
      isCanBalik = this.item.isCanBalik
    }


    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempStatusArr,
      3: tempNamaOrReg,
      4: tempNoReg,
      5: tempRuanganIdArr,
      6: tempInstalasiIdArr,
      7: tempNoRm,
      // 8: jmlRow
    }
    this.cacheHelper.set('DaftarRegistrasiPasienCtrl', chacePeriode);

    this.apiService.get("registrasi/get-daftar-registrasi-pasien?"
      + "namaPasien=" + tempNamaOrReg
      + "&ruanganId=" + tempRuanganId
      + "&status=" + tempStatus
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noReg=" + tempNoReg
      + "&instalasiId=" + tempInstalasiId
      + "&noRm=" + tempNoRm
      + "&jmlRows=" + jmlRow
      + "&kelompokPasienId=" + kelompokPasienId
      + "&isCanBalik=" + isCanBalik).subscribe(data => {
        var data = data
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          if (element.tglmeninggal == null) {
            element.statuspasien = 'Hidup'
            element.color = 'info'
          } else {
            element.statuspasien = 'Meninggal'
            element.color = 'danger'
          }
        }
        this.dataTable = data;
        this.totalRecords = data.totalRow;
      })
  }
  editAsuransi() {

    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    } else {
      this.cacheHelper.set('CachePemakaianAsuransi', undefined);
      this.apiService.get("registrasi/get-apd?noregistrasi="
        + this.selected.noregistrasi
        + "&objectruanganlastfk=" + this.selected.ruanganlastidfk
      ).subscribe(data => {
        var dataAntrian = data.data;
        if (dataAntrian != undefined) {
          this.router.navigate(['pemakaian-asuransi', this.selected.norec, dataAntrian.norec_apd])
          if (this.selected.norec_pa != null) {
            var cacheSet = this.selected.asuransiidfk
              + "~" + this.selected.norec_pa
              + "~" + this.selected.noregistrasi;

            this.cacheHelper.set('CachePemakaianAsuransi', cacheSet);
          }

        }
      })
    }
  }
  editRegistrasi() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    } else {
      this.cacheHelper.set('CacheRegistrasiPasien', undefined);
      this.apiService.get("registrasi/get-apd?noregistrasi="
        + this.selected.noregistrasi
        + "&objectruanganlastfk=" + this.selected.ruanganlastidfk
      ).subscribe(data => {
        var dataAntrian = data.data;
        if (dataAntrian != undefined) {
          this.router.navigate(['registrasi-ruangan', this.selected.normidfk])
          var cacheSet = this.selected.norec
            + "~" + this.selected.noregistrasi
            + "~" + dataAntrian.norec_apd
          this.cacheHelper.set('CacheRegistrasiPasien', cacheSet);

        }
      })
    }

  }
  editReg(e) {
    this.cacheHelper.set('CacheRegistrasiPasien', undefined);
    this.apiService.get("registrasi/get-apd?noregistrasi="
      + e.noregistrasi
      + "&objectruanganlastfk=" + e.ruanganlastidfk
    ).subscribe(data => {
      var dataAntrian = data.data;
      if (dataAntrian != undefined) {
        this.router.navigate(['registrasi-ruangan', e.normidfk])
        var cacheSet = e.norec
          + "~" + e.noregistrasi
          + "~" + dataAntrian.norec_apd
        this.cacheHelper.set('CacheRegistrasiPasien', cacheSet);

      }
    })
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
        if (data.length > 0) {
          this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
          return;
        } else {
          this.selected = event.data
        }
      })
    }
  }

  detailRegistrasi() {
    if (this.selected != undefined) {
      this.router.navigate(['detail-registrasi-pasien', this.selected.noregistrasi]);
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
  }
  detailRegis(e) {
    this.router.navigate(['detail-registrasi-pasien', e.noregistrasi]);
  }
  popUpInputTindakan() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    this.item.norec_dpr = ''
    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.apiService.get("registrasi/get-data-antrian-pasien?noregistrasi=" + this.selected.noregistrasi).subscribe(data => {
          var datas = data;
          this.listRuanganApd = datas.ruangan;
          if (datas.ruangan != undefined) {
            this.item.dataRuanganApd = datas.ruangan[0]
            this.item.norec_dpr = datas.ruangan[0].norec_apd
            this.pop_inputTindakan = true
          }
        })
      }
    })
  }

  inputTindakan() {
    if (this.item.dataRuanganApd == undefined) {
      this.alertService.warn("Info", "Ruang Antrian Belum Dipilih!");
      return;
    }

    this.router.navigate(['input-tindakan', this.selected.norec, this.item.norec_dpr])
  }

  popUpinputDiagnosa() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.apiService.get("registrasi/get-data-antrian-pasien?noregistrasi=" + this.selected.noregistrasi).subscribe(data => {
          var datas = data;
          this.listRuanganApd = datas.ruangan;
          if (datas.ruangan != undefined) {
            this.item.dataRuanganApd = datas.ruangan[0]
            this.pop_inputDiagnosa = true
          }
        });
      }
    })
  }

  inputDiagnosa() {
    if (this.item.dataRuanganApd == undefined) {
      this.alertService.warn("Info", "Ruang Antrian Belum Dipilih!");
      return;
    }
    this.router.navigate(['input-diagnosa', this.selected.norec, this.item.dataRuanganApd.norec_apd]);
  }

  filterDokter(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listDokter = re;
    })
  }

  popUpUbahDokter() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.pop_DokterPJawab = true;
      }
    })
  }

  batalDokter() {
    this.item.dokterPJawab = undefined;
    this.pop_DokterPJawab = false;
  }

  simpanDokter() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.item.dokterPJawab == undefined) {
      this.alertService.warn("Info,", "Data Dokter Belum Dipilih!");
      return;
    }

    var objSave = {
      norec_pd: this.selected.norec,
      pegawaiidfk: this.item.dokterPJawab.id
    }

    this.apiService.post('registrasi/save-dokter-registrasi', objSave).subscribe(e => {
      if (this.selected.norec != '') {
        this.apiService.postLog('Simpan Ubah Dokter', 'norec Registrasi Pasien', this.selected.norec, 'Ubah Ke Dokter  '
          + this.item.dokterPJawab.namalengkap + ' pada No Registrasi ' + this.selected.noregistrasi).subscribe(z => { })
      }
      this.item.dokterPJawab = undefined;
      this.pop_DokterPJawab = false;
      this.getData();
    })

  }
  cekCanBalik(e) {
    if (e == true) {
      this.disableTgl = true
      this.getData()
    } else {
      this.disableTgl = false
      this.getData()
    }
  }
  pindahPulang() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Pilih data dulu!");
      return;
    }
    if (this.selected.tglpulang != null) {
      this.router.navigate(['daftar-pasien-pulang', this.selected.noregistrasi]);
      return
    }

    if (this.selected.isclosing == true) {
      this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
      return;
    } else {
      this.apiService.get("registrasi/get-apd?noregistrasi="
        + this.selected.noregistrasi
        + "&objectruanganlastfk=" + this.selected.ruanganlastidfk
      ).subscribe(data => {
        var dataAntrian = data.data;
        if (dataAntrian != undefined) {
          this.router.navigate(['pindah-pulang', this.selected.norec, dataAntrian.norec_apd])
        }
      })
    }
  }
  batalReg(e) {
    this.selected = e;
    this.apiService.get("registrasi/get-data-pasien-mau-batal?noregistrasi="
      + e.noregistrasi
    ).subscribe(data => {

      if (data.length > 0)
        this.alertService.error("info", 'Pasien sudah Mendapatkan Pelayanan');
      else {
        this.item.namaBatal = e.namapasien
        this.item.ruanganBatal = { id: e.ruanganlastidfk, namaruangan: e.namaruangan }
        this.item.tglbatal = new Date();
        this.norecPD = e.norec;
        this.pop_batalPeriksa = true
      }
    });
  }
  batalPeriksa() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Pilih data dulu!");
      return;
    }
    this.apiService.get("registrasi/get-data-pasien-mau-batal?noregistrasi="
      + this.selected.noregistrasi
    ).subscribe(data => {

      if (data.length > 0)
        this.alertService.error("info", 'Pasien sudah Mendapatkan Pelayanan');
      else {
        this.item.namaBatal = this.selected.namapasien
        this.item.ruanganBatal = { id: this.selected.ruanganlastidfk, namaruangan: this.selected.namaruangan }
        this.item.tglbatal = new Date();
        this.norecPD = undefined;
        this.pop_batalPeriksa = true
      }
    });
  }
  simpanBatalPeriksa() {
    if (this.item.pembatalan == undefined) {
      this.alertService.warn("Info,", "Pilih Pembatalan!");
      return;
    }
    var BatalPeriksa = {
      "norec": this.norecPD,//this.selected.norec,
      "tanggalpembatalan": moment(this.item.tglbatal).format('YYYY-MM-DD hh:mm:ss'),
      "pembatalanfk": this.item.pembatalan.id,
      "alasanpembatalan": this.item.alasanBatal != undefined ? this.item.alasanBatal : '',
    }
    this.apiService.post('registrasi/save-batal-registrasi', BatalPeriksa).subscribe(e => {
       let params = this.selected.noregistrasi    
      if(this.selected.noreservasi != undefined && this.selected.noreservasi != '' && this.selected.noreservasi !='Kios-K'){
          params = this.selected.noreservasi
      } 
      // this.saveAntrol(params,6)
      var data = {
              "url": "antrean/updatewaktu",
              "jenis": "antrean",
              "method": "POST",
              "data":
              {
                "kodebooking": params,
                "taskid": 99,//(akhir waktu layan admisi/mulai waktu tunggu poli), 
                "waktu": new Date().getTime()
              }
            }
            this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe(e => {

            })
      this.getData()
      this.pop_batalPeriksa = false
      delete this.item.Pembatalan
      delete this.item.alasanBatal
      delete this.norecPD
    })
  }
  detailTagihan() {
    this.router.navigate(['detail-tagihan', this.selected.noregistrasi])
  }
  pengkajianMedis2(e) {
    this.apiService.get("registrasi/get-apd?noregistrasi="
      + e.noregistrasi
      + "&objectruanganlastfk=" + e.ruanganlastidfk
    ).subscribe(data => {
      var dataAntrian = data.data;
      if (dataAntrian != undefined) {
        this.router.navigate(['rekam-medis', e.norec, dataAntrian.norec_apd])
      }
    })
  }
  pengkajianMedis() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Pilih data dulu!");
      return;
    }
    this.apiService.get("registrasi/get-apd?noregistrasi="
      + this.selected.noregistrasi
      + "&objectruanganlastfk=" + this.selected.ruanganlastidfk
    ).subscribe(data => {
      var dataAntrian = data.data;
      if (dataAntrian != undefined) {
        this.router.navigate(['rekam-medis', this.selected.norec, dataAntrian.norec_apd])
      }
    })
  }
  popUpTransaksiLayanan() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Pilih data dulu!");
      return;
    }
    this.listRuanganTL = []
    this.apiService.get("penunjang/get-data-ruang-tujuan?keluseridfk=" + this.dataLogin.kelompokUser.id).subscribe(table => {
      var dataCombo = table;
      this.listRuanganTL = dataCombo.ruangan;
      this.item.dataRuanganTL = dataCombo.ruangan[0];
      this.pop_transaksiLayanan = true;
    })
  }

  lanjutRincian() {
    this.apiService.get("penunjang/get-data-apd?noregistrasi=" + this.selected.noregistrasi).subscribe(table => {
      var data = table.ruangan;
      var status = false
      var norec_apd = ''
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        status = false
        if (element.id == this.item.dataRuanganTL.id) {
          status = true
          norec_apd = element.norec_apd
          break
        }
      }
      if (status == true) {
        this.router.navigate(['rincian-penunjang', this.selected.norec, norec_apd])
      } else {
        this.saveKonsul();
      }
    })
  }

  saveKonsul() {
    var dataKonsul = {
      "asalrujukanfk": 1,
      "norec_pd": this.selected.norec,
      "dokterfk": this.selected.pgid,
      "objectruangantujuanfk": this.item.dataRuanganTL.id,
      "objectruanganasalfk": this.selected.ruanganlastidfk,
      "objectkelasfk": 6,//this.selected.kelasidfk,
      "tglregistrasidate": moment(this.selected.tglregistrasi).format('YYYY-MM-DD'),
    }
    this.apiService.post('penunjang/save-antrian-penunjang', dataKonsul).subscribe(dataSave => {
      var norec_apd = dataSave.data.norec
      this.router.navigate(['rincian-penunjang', this.selected.norec, norec_apd])
    })
  }
  filter() {
    this.popFilter = true
  }
  cariFilter() {
    this.popFilter = false
    this.getData();
  }
  clearFilter() {
    this.popFilter = false
    this.item = {}
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;

    this.getData();
  }

  selectData(e) {
    this.selected = e
  }

  cetakJampersal() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }

    if (this.item.kelompokPasien.kelompokpasien = ! "Jampersal") {
      this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
      return
    }

    var stt = 'false'
    if (confirm('View Surat Pernyataan Jampersal? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-surat-jampersal=1&noregistrasi=" +
      this.selected.noregistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }

  cetakFormulirRajal() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }

    if (this.selected.isranap != false) {
      this.alertService.error('Info', "Hanya Untuk Pasien Rawat Jalan!");
      return;
    }

    var stt = 'false'
    if (confirm('View Formulir Rawat Jalan? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-formulir-rajal=1&noregistrasi=" +
      this.selected.noregistrasi + "&view=" + stt, function (e) { });
  }

  cetakPersetujuanRanap() {
    if (this.selected == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }

    if (this.selected.isranap != true) {
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
      this.selected.noregistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }
  buatSPRI() {
    this.popSPRI = true
    this.popCetak = false
    this.kon.noKartu = this.selected.nokepesertaan
    this.kon.sep = this.selected.nosep
    this.clickRad(this.kon.jenisPelayanan)

    this.cariRencana()
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
  cariRencana() {

    var tglAwal = moment(this.kon.tglAwal).format('YYYY-MM-DD')
    var tglAkhir = moment(this.kon.tglAkhir).format('YYYY-MM-DD')

    let json = {
      "url": "RencanaKontrol/ListRencanaKontrol/tglAwal/" + tglAwal + "/tglAkhir/" + tglAkhir + "/filter/" + this.kon.filter.kode,
      "method": "GET",
      "data": null
    }
    this.dataSourceRen = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {

      if (e.metaData.code == "200") {
        for (let x = 0; x < e.response.list.length; x++) {
          const element = e.response.list[x];
          if (this.kon.noKartu == element.noKartu) {
            this.dataSourceRen.push(element)
          }
        }

        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }

    })
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
        this.listDPJP = e.response.list;
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
              this.helper.cetakSuratKontrol(nosuratkontrol, tglrencanakontrol, txttglentrirencanakontrol, noka, res.response.peserta.nama, res.response.peserta.tglLahir,
                this.namappkRumahSakit, e.namaPoliTujuan, res.response.peserta.sex, namadiag, ket,
                e.jnsKontrol, diag, tglrencanakontrol, e.namaDokter, dpjpSEPasal, e.jnsPelayanan);
            }

          } else {
            this.helper.cetakSuratKontrol(nosuratkontrol, tglrencanakontrol, txttglentrirencanakontrol, noka, res.response.peserta.nama, res.response.peserta.tglLahir,
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
      this.cariRencana()
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
