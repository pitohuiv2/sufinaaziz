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
  selector: 'app-daftar-tagihan-nonlayanan',
  templateUrl: './daftar-tagihan-nonlayanan.component.html',
  styleUrls: ['./daftar-tagihan-nonlayanan.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarTagihanNonlayananComponent implements OnInit {
  page: number;
  rows: number;
  pencarian: any = '';
  item: any = {};
  listJenisTransaksi: any[];
  dateNow: any;
  column: any[];
  selected: any;
  dataTable: any[];
  dataLogin: any;
  kelUser: any;
  listBtn:MenuItem[]
  listStatus = [
    { "id": 1, "status": "Lunas" },
    { "id": 2, "status": "Belum Bayar" },
  ];
  constructor(
    private apiService: ApiService,
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
    this.listBtn = [
      { label: 'Bayar Tagihan', icon: 'fa fa-money', command: () => { this.bayarTagihan(); } },
      { label: 'Ubah Tagihan', icon: 'fa fa-pencil-square-o', command: () => { this.ubahTagihan(); } },
      { label: 'Hapus OrTagihander', icon: 'fa fa-trash', command: () => { this.hapusTagihan(); } },
    
    ];
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglstruk', header: 'Tgl Transaksi', width: "140px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'jenistagihan', header: 'Jenis Tagihan', width: "180px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'keteranganlainnya', header: 'Keterangan', width: "200px" },
      { field: 'totalharusdibayar', header: 'Total Tagihan', width: "140px", isCurrency: true },
      { field: 'status', header: 'Status', width: "140px" },
    ];
    this.getDataCombo();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  getDataCombo() {
    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listJenisTransaksi = dataCombo.jenistransaksinonlayanan;
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarTagihanPasienNonLayananCache');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      if (chacePeriode[2] != undefined) {
        this.listJenisTransaksi = [chacePeriode[2]];
        this.item.dataTransaksi = chacePeriode[2];
      }
      if (chacePeriode[4] != undefined) {
        this.listStatus = [chacePeriode[4]];
        this.item.dataStatus = chacePeriode[4];
      }
      this.item.namaPasien = chacePeriode[3];
      this.loadData();
    } else {
      this.loadData();
    }
  }

  loadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var tempKelTransaksi = "";
    var tempKelTransaksiIdArr = undefined;
    if (this.item.dataTransaksi != undefined) {
      tempKelTransaksi = this.item.dataTransaksi.id;
      tempKelTransaksiIdArr = { id: this.item.dataTransaksi.id, kelompoktransaksi: this.item.dataTransaksi.kelompoktransaksi }
    }
    var tempStatus = "";
    var tempStatusArr = undefined;
    if (this.item.dataStatus != undefined) {
      tempStatus = this.item.dataStatus.status;
      tempStatusArr = { id: this.item.dataStatus.id, status: this.item.dataStatus.status }
    }
    var tempNamaPasien = "";
    if (this.item.namaPasien != undefined) {
      tempNamaPasien = this.item.namaPasien;
    }
    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = this.item.jmlRows
    }
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempKelTransaksiIdArr,
      3: tempNamaPasien,
      4: tempStatusArr,
    }
    this.cacheHelper.set('DaftarTagihanPasienNonLayananCache', chacePeriode);
    this.apiService.get("kasir/daftar-tagihan-non-layanan?"
      + "namaPelanggan=" + tempNamaPasien
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&jenisTagihanId=" + tempKelTransaksi
      + "&status=" + tempStatus
      + "&jmlRows=" + jmlRow).subscribe(table => {
        var data = table;
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
      })
  }

  cari() {
    this.loadData();
  }
  selectData(e){
    this.selected =e
  }
  onRowSelect(event: any) {
    this.selected = event.data;
  }

  ubahTagihan() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.status == "Lunas") {
      this.alertService.warn("Info", "Data Yang Dipilih Sudah Dibayar!");
      return;
    }

    if (this.selected.jenistagihanid == 2) {
      this.alertService.error("Info", "Data Yang Dipilih Hanya Bisa Dirubah Farmasi!");
      return;
    } else {
      this.router.navigate(['input-tindakan-nonlayanan', this.selected.norec]);
    }
  }

  hapusTagihan() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.status == "Lunas") {
      this.alertService.warn("Info", "Data Yang Dipilih Sudah Dibayar!");
      return;
    }

    if (this.selected.jenistagihanid == 2) {
      this.alertService.error("Info", "Data Yang Dipilih Hanya Bisa Hapus Farmasi!");
      return;
    } else {

      var objSave = {
        norec: this.selected.norec,
        nostruk: this.selected.nostruk,
      }

      this.confirmationService.confirm({
        message: 'Apakah Anda Akan Menghapus Transaksi Tersebut?',
        header: 'Konfirmasi Hapus Transaksi',
        icon: 'pi pi-info-circle',
        accept: () => {
          this.confirmationService.close();
          this.apiService.post('kasir/hapus-transaksi-non-layanan', objSave).subscribe(data => {
            this.apiService.postLog('Hapus Pelayanan Non Layanan', 'Norec strukpelayan', this.selected.norec,
              'Hapus Pelayanan Non Layanan Dengan No Transaksi - ' + this.selected.nostruk + ' atas Nama Pasien - '
              + this.selected.namapasien).subscribe(res => { })
              this.loadData();
          })
        },
        reject: (type) => {
          this.confirmationService.close();
          window.history.back();
        }
      });


    }

  }

  inputTagihan() {
    this.router.navigate(['input-tindakan-nonlayanan', "-"]);
  }

  bayarTagihan() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.status == "Lunas") {
      this.alertService.warn("Info", "Data Yang Dipilih Sudah Dibayar!");
      return;
    }

    this.router.navigate(['pembayaran-tagihan-layanan', this.selected.norec, "nonlayanan"]);
  }

}
