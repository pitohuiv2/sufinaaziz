import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { ActivatedRoute, Router } from '@angular/router';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';

@Component({
  selector: 'app-pembayaran-tagihan-layanan',
  templateUrl: './pembayaran-tagihan-layanan.component.html',
  styleUrls: ['./pembayaran-tagihan-layanan.component.scss'],
  providers: [ConfirmationService]
})
export class PembayaranTagihanLayananComponent implements OnInit {
  page: number;
  rows: number;
  params: any = {};
  currentNorecSP: any;
  currentJenisLayanan: any;
  norec_sp: any;
  jenisPelayan: any;
  item: any = { pasien: {} };
  isClosing: boolean = false;
  column: any[];
  selected: any;
  dataTable: any[];
  totalBilling: any;
  totalBayar: any;
  totalKlaim: any;
  totalDeposit: any;
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
    this.route.params.subscribe(params => {
      this.currentNorecSP = params['norec_sp'];
      this.currentJenisLayanan = params['jenislayanan'];
      this.jenisPelayan = params['jenislayanan'];
      this.norec_sp = this.currentNorecSP;
      this.loadHead();
    });
  }

  loadHead() {
    if (this.currentNorecSP != undefined && this.currentJenisLayanan != undefined) {
      if (this.currentJenisLayanan == 'layanan') {
        this.column = [
          { field: 'no', header: 'No', width: "65px" },
          { field: 'namaLayanan', header: 'Layanan', width: "250px" },
          { field: 'jumlah', header: 'Jumlah', width: "100px" },
          { field: 'harga', header: 'Harga', width: "180px", isCurrency: true },
          { field: 'diskon', header: 'Diskon', width: "180px", isCurrency: true },
          { field: 'total', header: 'Total', width: "120px", isCurrency: true },
        ];
        this.apiService.get("general/get-pasien-bystrukpelayan-general?norec_sp=" + this.currentNorecSP).subscribe(e => {
          e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
          e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
          // this.h.item.pasien = e;
          this.item.pasien = e;
          this.item.pasien.jenispelayanan = this.currentJenisLayanan.toUpperCase();
          this.loadDataDetail();
        })
      } else {
        this.column = [
          { field: 'no', header: 'No', width: "65px" },
          { field: 'namaLayanan', header: 'Layanan', width: "250px" },
          { field: 'keterangan', header: 'Keterangan', width: "250px" },
          { field: 'jumlah', header: 'Jumlah', width: "100px" },
          { field: 'qtyoranglast', header: 'Jumlah Per Org/ Per Km', width: "200px" },
          { field: 'harga', header: 'Harga', width: "140px", isCurrency: true },
          { field: 'jasa', header: 'Jasa', width: "140px", isCurrency: true },
          { field: 'total', header: 'Total', width: "120px", isCurrency: true },
        ];
        this.apiService.get("general/get-pasien-nonlayanan-general?norec_sp=" + this.currentNorecSP).subscribe(e => {
          e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
          e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
          // this.h.item.pasien = e;
          this.item.pasien = e;
          this.item.pasien.jenispelayanan = this.currentJenisLayanan.toUpperCase();
          this.loadDataDetail();
        })
      }
    } else {
      this.alertService.error("Info", "Data Tidak Ditemukan!");
      return;
    }
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadDataDetail() {
    if (this.currentJenisLayanan == 'layanan') {
      this.apiService.get("kasir/detail-tagihan-pasien-layanan?norec_sp=" + this.norec_sp).subscribe(e => {
        var data = e.detailTagihan;
        var totalPRekanan = 0;
        var totalbayar = 0;
        var total = 0;
        this.item.deposit = this.formatRupiah(e.totalDeposit, "Rp.");
        this.totalDeposit = parseFloat(e.totalDeposit);
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          total = total + element.total;
        }
        if (e.totalPenjamin != undefined) {
          totalPRekanan = parseFloat(e.totalPenjamin)
        }
        totalbayar = total - this.totalDeposit - totalPRekanan;
        this.item.billing = this.formatRupiah(total, "Rp.");
        this.item.jumlahBayar = this.formatRupiah(totalbayar, "Rp.");
        this.item.totalKlaim = this.formatRupiah(totalPRekanan, "Rp.");
        this.totalBilling = total;
        this.totalBayar = totalbayar;
        this.totalKlaim = totalPRekanan;
        this.dataTable = data;
      })
    } else {
      this.apiService.get("kasir/detail-tagihan-non-layanan?norec_sp=" + this.norec_sp).subscribe(e => {        
        var data = e.detailTagihan;
        var totalPRekanan = 0;
        var totalbayar = 0;
        var total = 0;  
        var deposit:any = 0;      
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          total = total + element.total;
        }       
        totalbayar = total;
        this.item.billing = this.formatRupiah(total, "Rp.");
        this.item.jumlahBayar = this.formatRupiah(totalbayar, "Rp.");
        this.item.totalKlaim = this.formatRupiah(totalPRekanan, "Rp.");
        this.totalBilling = total;
        this.totalBayar = totalbayar;
        this.totalKlaim = totalPRekanan;
        this.item.deposit = this.formatRupiah(deposit, "Rp.");
        this.totalDeposit = parseFloat(deposit);
        this.dataTable = data;
      })
    }
  }

  Batal() {
    this.router.navigate(['daftar-pasien-pulang']);
  }

  bayarTagihan() {
    if (this.norec_sp == undefined) {
      this.alertService.warn("Info", "Data Tidak Ditemukan!");
      return;
    }
    if (this.currentJenisLayanan == 'layanan') {
      this.router.navigate(['bayar-tagihan-pasien', this.norec_sp, "tagihanPasien", this.totalBayar]);
    }else{
      this.router.navigate(['bayar-tagihan-pasien', this.norec_sp, "pembayaranNonLayanan", this.totalBayar]);      
    }
    
  }
  
  changeKlaim(e) {

  }
}
