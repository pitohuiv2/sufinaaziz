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
  selector: 'app-daftar-kartu-stok',
  templateUrl: './daftar-kartu-stok.component.html',
  styleUrls: ['./daftar-kartu-stok.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarKartuStokComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {}
  dataLogin: any;
  kelUser: any;
  KelUserid: any;
  dateNow: any;
  listRuangan: any[];
  listDetailJenisProduk: any[];
  listJenisBarang: any[];
  listKelompokBarang: any[];
  listProduk: any[];
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit(): void {    
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.KelUserid = this.dataLogin.kelompokUser.id;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglkejadian', header: 'Tgl Transaksi', width: "140px" },
      { field: 'produkidfk', header: 'ID', width: "80px" },
      { field: 'namaproduk', header: 'Produk', width: "280px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'keterangan', header: 'Keterangan', width: "300px" },
      { field: 'saldoawal', header: 'Qty Awal', width: "140px" },
      { field: 'qtyin', header: 'Qty Masuk', width: "140px" },
      { field: 'qtyout', header: 'Qty Keluar', width: "140px" },
      { field: 'saldoakhir', header: 'Qty Akhir', width: "140px" },
      { field: 'transaksi', header: 'Transaksi', width: "140px" },      
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
    });
  }

  filterProduk(event) {
    let query = event.query;
    this.apiService.get("general/get-combo-produk-part?namaproduk=" + query + "&KelUserid=" + this.KelUserid)
      .subscribe(re => {
        this.listProduk = re;
      })
  }

  loadData() {   
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var ruanganId = "";
    if (this.item.dataRuangan !== undefined) {
      ruanganId = "&ruanganfk=" + this.item.dataRuangan.id
    }
    var kdproduk = "";
    if (this.item.produk !== undefined) {
      kdproduk = "&produkfk=" + this.item.produk.id;
    }

    this.apiService.get("general/get-data-kartu-stok?"
      + "tglAwal=" + tglAwal + "&tglAkhir=" + tglAkhir
      + ruanganId + kdproduk).subscribe(data => {        
        for (let i = 0; i < data.length; i++) {
          const element = data[i];          
          element.no = i + 1;
        }
        this.dataTable = data;
      });
  }

  cari() {
    this.loadData();
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.selected = event.data;
    }
  }

}
