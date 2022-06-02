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
  selector: 'app-laporan-penerimaan-kasir',
  templateUrl: './laporan-penerimaan-kasir.component.html',
  styleUrls: ['./laporan-penerimaan-kasir.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class LaporanPenerimaanKasirComponent implements OnInit {
  column: any[];
  selected: any;
  dataTable: any[];
  item: any = {};
  listCaraBayar: any;
  listKelompokTransaksi: any;
  listPetugasPenerima: any = [];
  dateNow: any;
  loginUser: any;
  rowGroupMetadata: any;
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
  ) { }

  ngOnInit(): void {
    this.item.tglAwal = new Date(moment(this.dateNow).format('YYYY-MM-DD 00:00'));
    this.item.tglAkhir = new Date(moment(this.dateNow).format('YYYY-MM-DD 23:59'));
    this.item.banyak = 0;
    this.item.totalprekanan = 0;
    this.item.tanggunganrs = 0;
    this.item.totalharusdibayar = 0;
    this.item.pembebasan = 0;
    this.item.administrasi = 0;
    this.item.totaldibayar = 0;
    this.item.sisa = 0;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglsbm', header: 'Tgl Bayar', width: "140px" },
      { field: 'nosbm', header: 'No Bayar', width: "140px" },
      { field: 'nocm', header: 'No RM', width: "125px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namapasien_klien', header: 'Deskripsi', width: "250px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'keterangan', header: 'Jenis Pembayaran', width: "180px" },
      { field: 'carabayar', header: 'Cara Bayar', width: "140px" },
      { field: 'totalpenerimaan', header: 'Total Penerimaan', width: "160px", isCurrency: true },
      { field: 'namalengkap', header: 'Petugas', width: "180px" },
      { field: 'status', header: 'Stat Setor', width: "140px" },
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("kasir/get-data-combo-lapkasir").subscribe(table => {
      this.listCaraBayar = table.carabayar;
      this.listKelompokTransaksi = table.jenistransaksi;
      this.listPetugasPenerima = table.petugaskasir;
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('cacheDaftarPenerimaanPembayaran');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.LoadData();
    } else {
      this.LoadData();
    }
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  LoadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
    }
    this.cacheHelper.set('cacheDaftarPenerimaanPembayaran', chacePeriode);

    var ScaraBayar = "";
    if (this.item.dataCaraBayar != undefined) {
      ScaraBayar = this.item.dataCaraBayar.id;
    }

    var SkelompokTransaksi = "";
    if (this.item.dataKelTransaksi != undefined) {
      SkelompokTransaksi = this.item.dataKelTransaksi.id;
    }
    var listKasir = ""
    if (this.item.selectedKasir != undefined) {
      var a = ""
      var b = ""
      for (var i = this.item.selectedKasir.length - 1; i >= 0; i--) {

        var c = this.item.selectedKasir[i].id
        b = "," + c
        a = a + b
      }
      listKasir = a.slice(1, a.length)
    }

    this.apiService.get("kasir/get-data-laporan-penerimaan-kasir?"
      + "dateStartTglSbm=" + tglAwal
      + "&dateEndTglSbm=" + tglAkhir
      + "&idCaraBayar=" + ScaraBayar
      + "&idKelTransaksi=" + SkelompokTransaksi
      + "&KasirArr=" + listKasir
    ).subscribe(table => {
      var data = table;
      var banyak = 0;
      var totalprekanan = 0;
      var tanggunganrs = 0;
      var totalharusdibayar = 0;
      var pembebasan = 0;
      var administrasi = 0;
      var totaldibayar = 0;
      var sisa = 0;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
        banyak = banyak + parseFloat(element.banyak);
        totalprekanan = totalprekanan + parseFloat(element.totalprekanan);
        tanggunganrs = tanggunganrs + parseFloat(element.tanggunganrs);
        totalharusdibayar = totalharusdibayar + parseFloat(element.totalharusdibayar);
        pembebasan = pembebasan + parseFloat(element.pembebasan);
        administrasi = administrasi + parseFloat(element.administrasi);
        totaldibayar = totaldibayar + parseFloat(element.totaldibayar);
        sisa = sisa + parseFloat(element.sisa);
      }
      this.dataTable = data;
      this.item.banyak = banyak;
      this.item.totalprekanan = totalprekanan;
      this.item.tanggunganrs = tanggunganrs;
      this.item.totalharusdibayar = totalharusdibayar;
      this.item.pembebasan = pembebasan;
      this.item.administrasi = administrasi;
      this.item.totaldibayar = totaldibayar;
      this.item.sisa = sisa;
      this.updateRowGroupMetaData();
    })
  }

  onSort() {
    this.updateRowGroupMetaData();
  }

  updateRowGroupMetaData() {
    this.rowGroupMetadata = {};
    if (this.dataTable) {
      for (let i = 0; i < this.dataTable.length; i++) {
        let rowData = this.dataTable[i];
        let ruangan = rowData.ruangan;
        if (i == 0) {
          this.rowGroupMetadata[ruangan] = { index: 0, size: 1 };
        }
        else {
          let previousRowData = this.dataTable[i - 1];
          let previousRowGroup = previousRowData.brand;
          if (ruangan === previousRowGroup)
            this.rowGroupMetadata[ruangan].size++;
          else
            this.rowGroupMetadata[ruangan] = { index: i, size: 1 };
        }
      }
    }
  }

  cari() {
    this.LoadData();
  }

  cetakLaporan() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var idcarabayar = "";
    var carabayar = "";
    if (this.item.dataCaraBayar != undefined) {
      idcarabayar = this.item.dataCaraBayar.id;
      carabayar = this.item.dataCaraBayar.carabayar
    }

    var SkelompokTransaksi = "";
    if (this.item.dataKelTransaksi != undefined) {
      SkelompokTransaksi = this.item.dataKelTransaksi.id;
    }
    var listKasir = ""
    if (this.item.selectedKasir != undefined) {
      var a = ""
      var b = ""
      for (var i = this.item.selectedKasir.length - 1; i >= 0; i--) {

        var c = this.item.selectedKasir[i].id
        b = "," + c
        a = a + b
      }
      listKasir = a.slice(1, a.length)
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-laporan-penerimaan-kasir=1&idkasir=" + listKasir
      + "&tglawal=" + tglAwal + "&tglakhir=" + tglAkhir + "&idcarabayar=" + idcarabayar + "&idruangan=" + "&carabayar=" + carabayar
      + "&ruangan=&view=true", function (e) {
      });
  }

}
