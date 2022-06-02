import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';

@Component({
  selector: 'app-penyetoran-deposit',
  templateUrl: './penyetoran-deposit.component.html',
  styleUrls: ['./penyetoran-deposit.component.scss'],
  providers: [ConfirmationService]
})
export class PenyetoranDepositComponent implements OnInit, AfterViewInit {
  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {
    pasien: {},
  };
  currentNorecPD: any;
  norec_pd: any;
  jumlahDepositMasuk: any;
  @ViewChild(HeadPasienComponent, { static: false }) h: HeadPasienComponent;
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

  ngAfterViewInit(): void {
    this.route.params.subscribe(params => {
      this.currentNorecPD = params['norec_rp'];
      this.norec_pd = params['norec_rp'];
      this.loadHead();
      this.loadDetail();
    })
  }

  ngOnInit(): void {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglTransaksi', header: 'Tgl Bayar', width: "140px" },
      { field: 'jumlahDeposit', header: 'Jumlah', width: "180px", isCurrency: true },
    ];
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadHead() {
    this.apiService.get("general/get-pasien-byregistrasiruangan-general?norec_pd=" + this.currentNorecPD).subscribe(e => {
      e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
      e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
      this.h.item.pasien = e;
      this.item.pasien = e;
    })
  }

  loadDetail() {
    this.apiService.get("kasir/detail-pasien-deposit/" + this.norec_pd).subscribe(table => {
      var data = table.detailDeposit
      var total = 0;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
        total = parseFloat(element.jumlahDeposit) + total;
      }
      this.dataTable = data;
      this.item.totalDeposit = this.formatRupiah(total, "Rp.")
      this.jumlahDepositMasuk = total;
    })
  }

  Batal() {
    this.item.jumlahDeposit = undefined;
  }

  Kembali() {
    window.history.back();
  }

  changeNominal(event) {
    var data = event;
    if (data != undefined && data != "") {
      this.apiService.get('sysadmin/general/get-terbilang/' + parseFloat(data)).subscribe(dat => {
        this.item.terbilang = dat.terbilang;
      });
    }
  }

  kembalianDeposit() {
    if (parseFloat(this.item.jumlahDeposit) < 0) {
      this.alertService.warn("Info", "Nilai tidak boleh negatif!")
      return
    }
    if (this.item.jumlahDeposit > this.jumlahDepositMasuk) {
      this.alertService.warn("Info", "Jumlah kembali tidak boleh lebih dari total deposit!")
      return
    }
    this.item.jumlahDeposit = parseFloat(this.item.jumlahDeposit) * (-1)
    this.router.navigate(['bayar-tagihan-pasien', this.norec_pd, "depositPasien", this.item.jumlahDeposit]);
  }

  bayarDeposit() {
    if (parseFloat(this.item.jumlahDeposit) < 0) {
      alert('Nilai tidak boleh negatif!')
      return
    }
    this.router.navigate(['bayar-tagihan-pasien', this.norec_pd, "PenyetoranDepositKasirKembali", this.item.jumlahDeposit]);
  }
  onRowSelect(e){
    
  }
}
