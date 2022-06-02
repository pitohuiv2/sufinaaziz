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
  selector: 'app-informasi-tarif-layanan',
  templateUrl: './informasi-tarif-layanan.component.html',
  styleUrls: ['./informasi-tarif-layanan.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class InformasiTarifLayananComponent implements OnInit {
  column: any[];
  selected: any;
  dataTable: any[];
  item: any = {};
  listProduk: any;
  listKelas: any;
  listRuangan: any = [];
  listLayanan: any = [];
  dateNow: any;
  loginUser: any;
  listBtn: MenuItem[]
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
    this.column = [
      { field: 'no', header: 'NO', width: "60px" },
      { field: 'id', header: 'ID', width: "100px" },
      { field: 'namaproduk', header: 'NAMA PELAYANAN', width: "180px" },
      { field: 'jenispelayanan', header: 'JENIS LAYANAN', width: "125px" },
      { field: 'namakelas', header: 'KELAS', width: "125px" },
      { field: 'namaruangan', header: 'RUANGAN', width: "125px" },
      { field: 'hargalayanan', header: 'TARIF', width: "120px" }
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("sysadmin/general/get-data-combo-informasi").subscribe(table => {
      this.listLayanan = table.jenispelayanan;
      this.listKelas = table.kelas;
      this.listRuangan = table.poli;
    })
  }

  loadData() {
    var idProduk = "";
    if (this.item.idProduk != undefined) {
      idProduk = this.item.idProduk
    }
    var produk = "";
    if (this.item.namaProduk != undefined) {
      produk = this.item.namaProduk.id
    }
    var kelas = "";
    if (this.item.dataKelas != undefined) {
      kelas = this.item.dataKelas.id
    }
    var ruangan = "";
    if (this.item.dataRuangan != undefined) {
      ruangan = this.item.dataRuangan.id
    }
    var jenispelayanan = "";
    if (this.item.dataJenisLayanan != undefined) {
      jenispelayanan = this.item.dataJenisLayanan.id
    }
    this.apiService.get('sysadmin/general/get-data-tarif-layanan?'
      + "produkId=" + idProduk
      + "&namaproduk=" + produk
      + "&kelasId=" + kelas
      + "&jenispelayananId=" + jenispelayanan
      + "&ruanganId=" + ruangan
    ).subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataTable = e.data
    })
  }

  cari() {
    this.loadData();
  }

  filterProduk(event) {
    let query = event.query;
    this.apiService.get("sysadmin/general/get-data-produk-general?namaproduk=" + query).subscribe(re => {
      this.listProduk = re;
    })
  }

}
