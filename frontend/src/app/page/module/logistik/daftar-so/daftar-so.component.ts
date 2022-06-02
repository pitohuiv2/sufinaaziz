import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-daftar-so',
  templateUrl: './daftar-so.component.html',
  styleUrls: ['./daftar-so.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarSoComponent implements OnInit {

  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {
    tglAwal: new Date(moment(new Date()).format('YYYY-MM-DD 00:00')),
    tglAkhir: new Date(moment(new Date()).format('YYYY-MM-DD 23:59')),
  }
  dataLogin: any;
  kelUser: any;
  dateNow: any;
  listRuangan: any[];
  listDetailJenisProduk: any[];
  listJenisBarang: any[];
  listKelompokBarang: any[];
  pop_UbahTanggal: boolean;
  pop_AdjusmentStok: boolean;
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private helper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit(): void {
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.jmlRows = 10;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglclosing', header: 'Tgl Closing', width: "140px" },
      { field: 'kdproduk', header: 'Kode Produk', width: "140px" },
      { field: 'namaproduk', header: 'Nama Produk', width: "300px" },
      { field: 'satuanstandar', header: 'Satuan', width: "180px" },
      { field: 'qtyprodukreal', header: 'Jumlah', width: "120px" },
      { field: 'harga', header: 'Harga Satuan', width: "140px", isCurrency: true },
      { field: 'total', header: 'Total', width: "140px", isCurrency: true },
      { field: 'namaruangan', header: 'Ruangan', width: "250px" },

    ];
    this.loadDataCombo();
  }

  loadDataCombo() {
    this.apiService.get("logistik/get-combo-logistik").subscribe(table => {
      var dataCombo = table;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuangan = dataCombo.ruangfarmasi;
      }
      this.item.dataRuangan = this.listRuangan[0];
      this.loadData()
    });
    this.apiService.get("logistik/get-combo-detailjenisproduk").subscribe(table => {
      var dataCombo = table;
      this.listDetailJenisProduk = dataCombo.detailjenisproduk;

    });
  }

  isiJenisProduk() {
    if (this.item.dataDjenisProduk != undefined) {
      this.listJenisBarang = this.item.dataDjenisProduk.jenisproduk;
    }
  }

  isiKelompokProduk() {
    if (this.item.dataJenisBarang != undefined) {
      this.listKelompokBarang = this.item.dataJenisBarang.kelompokproduk;
    }
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');

    var detailJenisBarang, kelBarang, jenBarang, ruanganId, namaBarang;
    ruanganId = "";
    if (this.item.dataRuangan != undefined) {
      ruanganId = this.item.dataRuangan.id
    }
    detailJenisBarang = "";
    if (this.item.dataDjenisProduk != undefined) {
      detailJenisBarang = this.item.dataDjenisProduk.id
    }
    kelBarang = "";
    if (this.item.dataKelompokBarang != undefined) {
      kelBarang = this.item.dataKelompokBarang.id
    }
    jenBarang = "";
    if (this.item.dataJenisBarang != undefined) {
      jenBarang = this.item.dataJenisBarang.id
    }
    namaBarang = "";
    if (this.item.namaProduk != undefined) {
      namaBarang = this.item.namaProduk
    }

    var jmlRows = "";
    if (this.item.jmlRows != undefined) {
      jmlRows = this.item.jmlRows
    }
    this.apiService.get('logistik/get-daftar-so?' +
      'tglAwal=' + tglAwal +
      '&tglAkhir=' + tglAkhir +
      '&jeniskprodukid=' + jenBarang +
      '&ruanganfk=' + ruanganId +
      "&detailjenisprodukfk=" + detailJenisBarang +
      '&jmlRows=' + jmlRows)

      .subscribe(table => {
        var data = table.data;

        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1
        }
        this.dataTable = data
      })
  }

  cari() {
    this.loadData();
  }


  exportExcel() {
    this.helper.exportExcel(this.dataTable, 'DaftarStokOpname')
  }

}
