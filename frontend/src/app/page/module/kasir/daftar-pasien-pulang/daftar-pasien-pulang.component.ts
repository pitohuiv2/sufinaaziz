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
  selector: 'app-daftar-pasien-pulang',
  templateUrl: './daftar-pasien-pulang.component.html',
  styleUrls: ['./daftar-pasien-pulang.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPasienPulangComponent implements OnInit {
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
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  dateNow: any;
  column: any[];
  norecSpLast: any;
  listBelumBayar: any[];
  idKdRanap: any
  pop_detailVerifikasi: boolean;
  dataTableDV: any[];
  columnDV: any[];
  selectedDV: any;
  popFilter: boolean
  listBtn: MenuItem[];
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

  ngOnInit() {
    this.listBtn = [
      { label: 'Detail Registrasi', icon: 'pi pi-users', command: () => { this.detailRegistrasi(); } },
      { label: 'Detail Tagihan', icon: 'pi pi-money-bill', command: () => { this.detailTagihan(); } },
      { separator: true },
      { label: 'Closing Pemeriksaan', icon: 'pi pi-lock', command: () => { this.closingPemeriksaan(); } },
      { label: 'Batal Closing', icon: 'pi pi-lock-open', command: () => { this.batalClosing(); } },
      { separator: true },
      { label: 'Bayar Tagihan', icon: 'fa fa-money', command: () => { this.bayarTagihan(); } },
      { label: 'Batal Pulang', icon: 'pi pi-undo', command: () => { this.batalPulang(); } },
      // { label: 'Rekap Billing Ruangan', icon: 'pi pi-print', command: () => { this.rekapBillingRuangan(); } },
      // { label: 'Kwitansi Total', icon: 'pi pi-print', command: () => { this.kwitansiTotal(); } },
      // { label: 'Surat Total Biaya Perawatan', icon: 'pi pi-print', command: () => { this.suratTotal(); } },
    ];
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.closing = false;
    this.item.bayar = false;
    this.item.jmlRows = 10;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'statclosing', header: 'Closing', width: "120px", isTag: true },  
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'norm', header: 'No RM', width: "120px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "120px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "140px" },
      { field: 'statuspasien', header: 'Stat Pasien', width: "120px" },      
      { field: 'status', header: 'Stat Pembayaran', width: "180px", isTag: true },
    ];
    this.columnDV = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglstruk', header: 'Tgl Struk', width: "140px" },
      { field: 'nostruk', header: 'No Verifikasi', width: "140px" },
      { field: 'petugasverif', header: 'Petugas', width: "180px" },
      { field: 'totalharusdibayar', header: 'Tot Harus Bayar', width: "150px", isCurrency: true },
      { field: 'status', header: 'Stat Bayar', width: "120px" },
    ];
    this.getDataCombo();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  getDataCombo() {
    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.idKdRanap = dataCombo.kdRanap;
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarPasienPulangCtrl');
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
    if (this.item.instalasi != undefined) {
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
    this.cacheHelper.set('DaftarPasienPulangCtrl', chacePeriode);

    this.apiService.get("kasir/daftar-pasien-pulang?"
      + "namaPasien=" + tempNamaOrReg
      + "&ruanganId=" + tempRuanganId
      + "&status=" + tempStatus
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noReg=" + tempNoReg
      + "&instalasiId=" + tempInstalasiId
      + "&noRm=" + tempNoRm
      + "&jmlRows=" + jmlRow
      + "&kelompokPasienId=" + kelompokPasienId).subscribe(data => {
        var data = data
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;

          if (element.status == "Verifikasi") {
            element.class = 'p-tag p-tag-info';
          } else if (element.status == "Belum Verifikasi") {
            element.class = 'p-tag p-tag-warning';
          } else {
            element.class = 'p-tag p-tag-success';
          }          
          if (element.tglmeninggal == null) {
            element.statuspasien = 'Hidup'
            element.color = 'info'
          } else {
            element.statuspasien = 'Meninggal'
            element.color = 'danger'
          }
          if (element.statclosing == "Belum Closing") {
            element.class = 'p-tag p-tag-warning';
          } else if (element.statclosing == "Closing") {
            element.class = 'p-tag p-tag-success';
          }
        }
        this.dataTable = data;
        this.totalRecords = data.totalRow;
      })
  }

  cari() {
    this.getData();
  }

  onRowSelect(event: any) {
    this.selected = event.data
    if (event.data != undefined) {
      if (this.selected.statclosing == "Belum Closing") {
        this.item.closing = false;
      } else {
        this.item.closing = true;
      }
      this.apiService.get("kasir/get-struk-pelayanan/" + this.selected.noregistrasi).subscribe(table => {
        var data = table.data;
        if (data.length > 0) {
          this.item.bayar = true
          this.listBelumBayar = data
          this.norecSpLast = data[0].norec_sp
        } else {
          this.item.bayar = false
          this.listBelumBayar = undefined
          this.norecSpLast = ''
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

  batalPulang() {
    this.apiService.get("kasir/get-status-verif-piutang?noReg=" + this.selected.noregistrasi).subscribe(res => {
      if (this.selected.deptid != this.idKdRanap) {
        this.alertService.error('Info', "Fitur Ini Khusus Pasien Rawat Inap!!!");
        return;
      }
      if (this.selected.status == 'Verifikasi'
        || res.noverif != undefined
        || this.selected.status == '-') {
        this.alertService.error('Info', "Data Pasien Sudah di Verifikasi!!!");
        return;
      }
      var objsave = {
        noregistrasi: this.selected.noregistrasi,
        tglpulang: null
      }
      this.apiService.post('kasir/save-batal-pulang', objsave).subscribe(data => {
        this.getData();
      })
    })
  }

  verifikasiTagihan(e) {
    // if (this.selected == undefined) {
    //   this.alertService.warn("Info", "Data Belum Dipilih!");
    //   return;
    // }
    this.apiService.get("general/get-data-closing-pasien/" + e.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.router.navigate(['verifikasi-tagihan', e.norec_pd])
      }
    })
  }

  loadDetailVerifikasi(e) {
    this.apiService.get("kasir/get-data-detail-verifikasi-tagihan?noRegistrasi=" + e.noregistrasi).subscribe(data => {
      var dataGrid = data.data;
      for (let i = 0; i < dataGrid.length; i++) {
        const element = dataGrid[i];
        element.no = i + 1;
      }
      this.dataTableDV = dataGrid;
    });
  }

  detailVerifikasi() {
    if (this.selected != undefined) {
      this.loadDetailVerifikasi(this.selected);
      this.pop_detailVerifikasi = true;
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
  }

  onRowSelectDV(event: any) {
    this.selectedDV = event.data
  }

  UnverifikasiTagihan() {
    if (this.selectedDV != undefined) {
      if (this.selectedDV.noverifikasi != null) {
        this.alertService.warn("Info", "Data Sudah Diverifikasi Piutang!");
        return;
      }

      var objSave = {
        'noregistrasi': this.selectedDV.noregistrasi,
        'norec_sp': this.selectedDV.norec
      }

      this.apiService.post('kasir/batal-verifikasi-tagihan-tatarekening', objSave).subscribe(data => {
        this.apiService.postLog('Batal Verifikasi Tagihan', 'norec Struk Pelayanan', this.selectedDV.norec, 'Nomor Verifikasi  '
          + this.selectedDV.nostruk + ' pada No Registrasi ' + this.selectedDV.noregistrasi).subscribe(z => {
            this.getData();
            this.loadDetailVerifikasi(this.selected);
          })
      })
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
  }
  selectData(e) {
    this.selected = e
    this.apiService.get("kasir/get-struk-pelayanan/" + this.selected.noregistrasi).subscribe(table => {
      var data = table.data;
      if (data.length > 0) {
        this.item.bayar = true
        this.listBelumBayar = data
        this.norecSpLast = data[0].norec_sp
      } else {
        this.item.bayar = false
        this.listBelumBayar = undefined
        this.norecSpLast = ''
      }
    })
  }
  closingPemeriksaan() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.status == 'Belum Verifikasi') {
      this.alertService.warn("Info", "Pasien Belum Diverifikasi!");
      return;
    }
    if (this.selected.statclosing != 'Belum Closing') {
      this.alertService.warn("Info", "Pasien Sudah Diclosing!");
      return;
    }
    if (this.selected.kelompokpasien == "Umum/Tunai" && this.listBelumBayar != undefined) {
      this.alertService.error("Info", "Pasien Harus Melunasi Tagihan Terlebih Dahulu!")
      return
    }

    this.confirmationService.confirm({
      message: 'Apakah Anda Yakin Akan Mengclosing Data?',
      header: 'Konfirmasi Closing Data',
      icon: 'pi pi-info-circle',
      accept: () => {
        var objSave = {
          'noregistrasi': this.selected.noregistrasi,
          'close': true
        }
        this.apiService.post('kasir/closing-pemeriksaan-pasien', objSave).subscribe(e => {
          this.apiService.postLog('Closing Tagihan Pasien', 'norec_pd', this.selected.norec_pd,
            ' Closing Tagihan Pada No Registrasi ' + this.selected.noregistrasi).subscribe(z => { })
          this.getData();
          this.confirmationService.close();
        })
      },
      reject: (type) => {
        this.alertService.warn('Info, Konfirmasi', 'Hapus Dibatalkan!');
        this.confirmationService.close();
        return;
      }
    });
  }

  batalClosing() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.status == 'Belum Verifikasi') {
      this.alertService.warn("Info", "Pasien Belum Diverifikasi!");
      return;
    }
    if (this.selected.statclosing == 'Belum Closing') {
      this.alertService.warn("Info", "Pasien Belum Diclosing!");
      return;
    }
    if (this.selected.kelompokpasien == "Umum/Tunai" && this.listBelumBayar != undefined) {
      this.alertService.error("Info", "Pasien Harus Melunasi Tagihan Terlebih Dahulu!")
      return
    }

    this.confirmationService.confirm({
      message: 'Apakah Anda Yakin Akan Membatalkan Closing?',
      header: 'Konfirmasi Batal Closing',
      icon: 'pi pi-info-circle',
      accept: () => {
        var objSave = {
          'noregistrasi': this.selected.noregistrasi,
          'close': false
        }
        this.apiService.post('kasir/closing-pemeriksaan-pasien', objSave).subscribe(e => {
          this.apiService.postLog('Batal Closing Tagihan Pasien', 'norec_pd', this.selected.norec_pd,
            ' Batal Closing Tagihan Pada No Registrasi ' + this.selected.noregistrasi).subscribe(z => { })
          this.getData();
          this.confirmationService.close();
        })
      },
      reject: (type) => {
        this.alertService.warn('Info, Konfirmasi', 'Hapus Dibatalkan!');
        this.confirmationService.close();
        return;
      }
    });
  }
  detailTagihan() {
    this.router.navigate(['detail-tagihan', this.selected.noregistrasi])
  }

  rekapTagihan() {

  }

  bayarTagihan() {    
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.status == 'Belum Verifikasi') {
      this.alertService.warn("Info", "Pasien Belum Diverifikasi!");
      return;
    }
    if (this.selected.statclosing != 'Belum Closing') {
      this.alertService.warn("Info", "Pasien Sudah Diclosing!");
      return;
    }
    // this.getNorecSP(this.selected)
    if (this.norecSpLast == '') {
      this.alertService.warn("Info", "Data Pembayaran Tidak Ditemukan!");
      return;
    }

    this.router.navigate(['pembayaran-tagihan-layanan', this.norecSpLast, "layanan"]);
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
}
