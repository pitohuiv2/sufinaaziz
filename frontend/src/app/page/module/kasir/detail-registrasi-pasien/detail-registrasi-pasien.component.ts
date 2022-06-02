import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-detail-registrasi-pasien',
  templateUrl: './detail-registrasi-pasien.component.html',
  styleUrls: ['./detail-registrasi-pasien.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class DetailRegistrasiPasienComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  pencarian: any = '';
  listData: any[];
  totalRecords: number;
  item: any = { pasien: {} };
  loading: boolean=false;
  sortField: any;
  sortOrder: any;
  params: any = {};
  norec_pd: any;
  column: any[];
  listRuanganApd: any[];
  listDokter: any[]
  pop_Konsultasi: boolean;
  pop_DokterPJawab: boolean;
  pop_TipePasien: boolean;
  listTipePasien: any[];
  ListPenjaminPasien: any[];
  norecPD: any;
  pop_UbahTanggal: boolean;
  dateNow: any;
  idRuangTerakhir: any;
  idKdRanap: any;
  idInstalasiRTerkahir: any;
  numberss= Array(15).map((x,i)=>i);
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      this.dateNow = new Date();
      this.params.Noregistrasi = params['noregistrasi'];
      this.item.pasien.noregistrasi = this.params.Noregistrasi;
      this.item.tglRegistrasi = this.dateNow;
      this.item.tglMasuk = this.dateNow;
      this.item.tglKeluar = this.dateNow;
      this.item.tglPulang = this.dateNow;
      this.column = [
        { field: 'no', header: 'No', width: "65px" },
        { field: 'namaruangan', header: 'Ruangan', width: "180px" },
        { field: 'namadokter', header: 'Dokter', width: "180px" },
        { field: 'namakelas', header: 'Kelas', width: "140px" },
        { field: 'namakamar', header: 'Kamar', width: "120px" },
        { field: 'nobed', header: 'No Bed', width: "180px" },
        { field: 'tglmasuk', header: 'Tgl Masuk', width: "140px" },
        { field: 'tglkeluar', header: 'Tgl Keluar', width: "140px" }
      ];
      this.getCombo();
      this.cariRegistrasi();
    });
  }

  getCombo() {
    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listRuanganApd = dataCombo.ruangannonianp;
      this.listTipePasien = dataCombo.kelompokpasien;
      this.idKdRanap = dataCombo.kdRanap;
    })
  }

  cariRegistrasi() {
    this.loading =true
    if (this.item.pasien.noregistrasi != undefined) {
      this.apiService.get("kasir/get-detail-registrasi-pasien?noregistrasi=" + this.item.pasien.noregistrasi).subscribe(data => {
        var datadetail = data.datadetail;
        var dataHead = data.datahead[0];
        var now = moment(new Date(dataHead.tglregistrasi)).format('YYYY-MM-DD');
        var tgllahir = moment(new Date(dataHead.tgllahir)).format('YYYY-MM-DD');
        var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
        this.norecPD = dataHead.norec_pd;
        this.idRuangTerakhir = dataHead.ruanganlastidfk;
        this.idInstalasiRTerkahir = dataHead.instalasiidfk;
        this.item.pasien.tglregistrasi = moment(new Date(dataHead.tglregistrasi)).format('DD-MM-YYYY');
        this.item.pasien.norm = dataHead.norm;
        this.item.pasien.namapasien = dataHead.namapasien;
        this.item.pasien.jeniskelamin = dataHead.jeniskelamin;
        this.item.pasien.tgllahir = moment(new Date(dataHead.tgllahir)).format('DD-MM-YYYY');
        this.item.pasien.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari';
        this.item.pasien.kelas = dataHead.namakelas;
        this.item.pasien.ruangan = dataHead.namaruangan;
        this.item.pasien.tipepasien = dataHead.kelompokpasien;
        this.item.pasien.norec_apd = dataHead.norec_apd;
        if (dataHead.tglpulang != null)
          this.item.pasien.tglpulang = moment(new Date(dataHead.tglpulang)).format('DD-MM-YYYY');
        for (let i = 0; i < datadetail.length; i++) {
          const element = datadetail[i];
          element.no = i + 1;
        }
        this.dataTable = datadetail;
        this.totalRecords = datadetail.totalRow;
        this.loading =false
      })
    } else {
      this.loading =false
      this.alertService.info("Info", "Noregistrasi Tidak Boleh Kosong!");
      return;
    }
  }

  onRowSelect(event: any) {
    this.selected = event.data
  }

  filterDokter(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listDokter = re;
    })
  }

  filterPenjaminPasien(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-rekanan-part?namarekanan=" + query
    ).subscribe(re => {
      this.ListPenjaminPasien = re;
    })
  }

  popUpKonsultasi() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.pop_Konsultasi = true;
      }
    })
  }

  batalKonsultasi() {
    this.item.dataRuanganApd = undefined;
    this.item.dokter = undefined;
    this.pop_Konsultasi = false;
  }

  simpanKonsultasi() {
    var length = this.dataTable.length + 1;
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.item.dataRuanganApd == undefined) {
      this.alertService.warn("Info,", "Data Ruangan Belum Dipilih!");
      return;
    }

    var dokter = null;
    if (this.item.dokter != undefined) {
      dokter = this.item.dokter.id;
    }

    var objSave = {
      norec_pd: this.selected.norec_pd,
      norec_apd: this.selected.norec,
      ruanganasal: this.selected.ruanganidfk,
      ruangan: this.item.dataRuanganApd.id,
      dokter: dokter,
      noantrian: length
    }

    this.apiService.post('kasir/save-antrian-konsultasi', objSave).subscribe(e => {
      var data = e;
      if (data.data.norec != '') {
        this.apiService.postLog('Simpan Antrian Konsultasi', 'norec Antrian Pasien', data.data.norec, 'Simpan Antrian Konsultasi Ke Ruangan '
          + this.item.dataRuanganApd.namaruangan + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
      }
      this.item.dataRuanganApd = undefined;
      this.item.dokter = undefined;
      this.pop_Konsultasi = false;
      this.cariRegistrasi();
    })
  }

  popUpUbahDokter() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.pop_DokterPJawab = true;
      }
    })
  }

  batalDokter() {
    this.item.dataRuanganApd = undefined;
    this.item.dokter = undefined;
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
      norec_pd: this.selected.norec_pd,
      norec_apd: this.selected.norec,
      pegawaiidfk: this.item.dokterPJawab.id
    }

    this.apiService.post('kasir/save-dokter-antrian', objSave).subscribe(e => {
      if (this.selected.norec != '') {
        this.apiService.postLog('Simpan Ubah Dokter', 'norec Antrian Pasien', this.selected.norec, 'Ubah Ke Dokter  '
          + this.item.dokterPJawab.namalengkap + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
      }
      this.item.dokterPJawab = undefined;
      this.pop_DokterPJawab = false;
      this.cariRegistrasi();
    })

  }

  popUpUbahPenjamin() {
    if (this.norecPD == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.pop_TipePasien = true;
      }
    })
  }

  batalTipePasien() {
    this.item.tipePasien = undefined;
    this.item.PenjaminPasien = undefined;
    this.pop_TipePasien = false;
  }

  simpanTipePasien() {
    if (this.norecPD == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.item.tipePasien == undefined) {
      this.alertService.warn("Info,", "Data Tipe Pasien Belum Dipilih!");
      return;
    }

    var penjamin = null;
    if (this.item.PenjaminPasien != undefined) {
      penjamin = this.item.PenjaminPasien.id;
    }

    var objSave = {
      norec_pd: this.norecPD,
      rekananidfk: penjamin,
      kelompokpasienlastidfk: this.item.tipePasien.id
    }

    this.apiService.post('kasir/save-ubah-rekanan', objSave).subscribe(e => {
      if (this.norecPD != '') {
        this.apiService.postLog('Simpan Ubah Tipe Pasien', 'norec Pasien Daftar', this.norecPD, 'Ubah Ke Tipe Penjamin  '
          + this.item.tipePasien.kelompokpasien + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
      }
      this.item.tipePasien = undefined;
      this.item.PenjaminPasien = undefined;
      this.pop_TipePasien = false;
      this.cariRegistrasi();
    })
  }

  popUpUbahTanggal() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.item.tglRegistrasi = new Date(moment(this.selected.tglregistrasi).format('YYYY-MM-DD HH:mm'));
        this.item.tglMasuk = new Date(moment(this.selected.tglmasuk).format('YYYY-MM-DD HH:mm'));
        this.item.tglKeluar = new Date(moment(this.selected.tglkeluar).format('YYYY-MM-DD HH:mm'));
        this.item.tglPulang = new Date(moment(this.selected.tglpulang).format('YYYY-MM-DD HH:mm'));
        this.pop_UbahTanggal = true;
      }
    })
  }

  batalUbahTgl() {
    var date = new Date();
    this.item.tglRegistrasi = date;
    this.item.tglMasuk = date;
    this.item.tglKeluar = date;
    this.item.tglPulang = date;
    this.pop_UbahTanggal = false;
  }

  simpanUbahTgl() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    var objSave = {
      norec_pd: this.selected.norec_pd,
      norec_apd: this.selected.norec,
      istglregis: this.item.SelectedtglRegis != undefined ? this.item.SelectedtglRegis : null,
      istglmasuk: this.item.SelectedtglMasuk != undefined ? this.item.SelectedtglMasuk : null,
      istglkeluar: this.item.SelectedtglKeluar != undefined ? this.item.SelectedtglKeluar : null,
      istglpulang: this.item.SelectedtglPulang != undefined ? this.item.SelectedtglPulang : null,
      tglregistrasi: this.item.tglRegistrasi != undefined ? moment(this.item.tglRegistrasi).format('YYYY-MM-DD HH:mm') : null,
      tglmasuk: this.item.tglMasuk != undefined ? moment(this.item.tglMasuk).format('YYYY-MM-DD HH:mm') : null,
      tglkeluar: this.item.tglKeluar != undefined ? moment(this.item.tglKeluar).format('YYYY-MM-DD HH:mm') : null,
      tglpulang: this.item.tglPulang != undefined ? moment(this.item.tglPulang).format('YYYY-MM-DD HH:mm') : null,
    }

    this.apiService.post('kasir/save-ubah-tanggal', objSave).subscribe(e => {
      if (this.item.pasien.noregistrasi != '') {
        this.apiService.postLog('Simpan Ubah Tanggal', 'Noregistrasi', this.item.pasien.noregistrasi, 'Ubah Tanggal Pada NoRegistrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
      }
      var date = new Date();
      this.item.tglRegistrasi = date;
      this.item.tglMasuk = date;
      this.item.tglKeluar = date;
      this.item.tglPulang = date;
      this.pop_UbahTanggal = false;
      this.cariRegistrasi();
    })
  }

  hapusAntrian() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else if (parseInt(this.selected.ruanganidfk) == parseInt(this.idRuangTerakhir)) {
        this.alertService.error("Peringatan!", "Ruangan Terakhir Tidak Bisa Dihapus");
        return;
      } else if (parseInt(this.selected.ruanganidfk) == parseInt(this.idRuangTerakhir)) {
        this.apiService.get('general/get-data-pelayanan-antrian/' + this.selected.norec).subscribe(datas => {
          if (datas.length > 0) {
            this.alertService.error("Peringatan!", "Registrasi Ini Telah Memiliki Layanan");
            return;
          }
        })
      } else {
        var objSave = {
          norec_apd: this.selected.norec,
        }

        this.apiService.post('kasir/save-ubah-tanggal', objSave).subscribe(e => {
          if (this.item.pasien.noregistrasi != '') {
            this.apiService.postLog('Hapus Antrian Pasien', 'norec APD', this.selected.norec, 'Hapus Antrian Pasien NoRegistrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
          }
          this.cariRegistrasi();
        })
      }
    })
  }

  pindahPulang() {
    if (this.norecPD == undefined) {
      this.alertService.warn("Info,", "Data Tidak Ditemukan!");
      return;
    }

    if (this.item.pasien.tglpulang != undefined) {
      this.alertService.warn("Info,", "Pasien Sudah Pulang!");
      // this.router.navigate(['daftar-pasien-pulang'])
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else if (parseInt(this.idInstalasiRTerkahir) != this.idKdRanap) {
        this.alertService.error("Peringatan!", "Pindah Pulang Hanya untuk Pasien Rawat Inap");
        return;
      } else {
        this.router.navigate(['pindah-pulang', this.norecPD, this.item.pasien.norec_apd])

      }
    })
  }

  inputTindakan(){    
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;      
      } else {
        this.item.norec_dpr = this.selected.norec;
        this.router.navigate(['input-tindakan', this.selected.norec_pd, this.item.norec_dpr])
      }
    })
  }
  detailTagihan(){
    this.router.navigate(['detail-tagihan', this.item.pasien.noregistrasi])
  }
  orderPenunjang(){
    
  }
  rekapTagihan(){
    
  }
}
