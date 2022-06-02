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
  selector: 'app-daftar-setoran-kasir',
  templateUrl: './daftar-setoran-kasir.component.html',
  styleUrls: ['./daftar-setoran-kasir.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class DaftarSetoranKasirComponent implements OnInit {
  page: number;
  rows: number;
  column: any[];
  selected: any;
  dataTable: any[];
  item: any = {};
  listCaraBayar: any;
  listCaraSetor: any;
  listKelompokTransaksi: any;
  listPetugasPenerima: any = [];
  dateNow: any;
  loginUser: any;
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
    this.loginUser = this.authService.getDataLoginUser();
    this.dateNow = new Date();
    this.item.tglAwal = new Date(moment(this.dateNow).format('YYYY-MM-DD 00:00'));
    this.item.tglAkhir = new Date(moment(this.dateNow).format('YYYY-MM-DD 23:59'));
    this.item.jmlRows = 50;
    this.loadColumn();
    this.getDataCombo();
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'noclosing', header: 'Noclosing', width: "180px" },
      { field: 'tglclosing', header: 'Tgl Setor', width: "140px" },
      { field: 'carabayar', header: 'Cara Bayar', width: "120px" },
      { field: 'carasetor', header: 'Cara Setor', width: "140px" },
      { field: 'petugas', header: 'Petugas Penerima', width: "200px" },
      { field: 'totalsetor', header: 'Total Setor', width: "180px", isCurrency: true }
    ];
  }

  getDataCombo() {
    this.apiService.get("bendaharapenerimaan/get-combo-bp").subscribe(table => {
      this.listCaraBayar = table.carabayar;
      this.listKelompokTransaksi = table.jenistransaksi;
      this.listPetugasPenerima = table.petugaskasir;
      this.listCaraSetor = table.carasetor;
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('cacheDaftarSetoranKasir');
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
    this.cacheHelper.set('cacheDaftarSetoranKasir', chacePeriode);
    var ScaraBayar = "";
    if (this.item.CaraBayar != undefined) {
      ScaraBayar = this.item.CaraBayar.id;
    }
    var ScaraSetor = "";
    if (this.item.CaraSetor != undefined) {
      ScaraSetor = this.item.CaraSetor.id;
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
    this.apiService.get("bendaharapenerimaan/get-daftar-setoran-kasir?"
      + "tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&idCaraBayar=" + ScaraBayar
      + "&idCaraSetor=" + ScaraSetor
      + "&KasirArr=" + listKasir
    ).subscribe(table => {
      var data = table.data;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
      }
      this.dataTable = data;
    })
  }

  cari() {
    this.LoadData();
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.selected = event.data
    }
  }
  batalSetor2(e) {
    this.selected = e
    this.batalSetor()
  }
  batalSetor() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }

    var tgl = moment(this.item.tglAwal).format('YYYY-MM-DD')
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm')
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm')
    var total: any = 0
    var objSbm: any = []
    if (this.dataTable == undefined) {
      this.alertService.error("Info", "Belum ada data yang di setor!")
      return;
    }
    for (let i = 0; i < this.selected.details.length; i++) {
      const element = this.selected.details[i];
      if (element.norec != undefined) {
        total = total + parseFloat(element.totalpenerimaan)
        objSbm.push({
          'norec_sbm': element.norec,
          'noclosing': this.selected.noclosing
        })
      }
    }
    if (objSbm.length == 0) {
      this.alertService.error("Info", "'Belum ada data yang di setor!")
      return
    }
    total = this.formatRupiah(total, "Rp.");
    var objSaveNew = {
      "details": objSbm
    }
    this.confirmationService.confirm({
      message: 'Apakah Anda Yakin Akan Melakukan Pembatalan Setoran Kasir?',
      header: 'Konfirmasi Pembatalan Setoran Kasir',
      icon: 'pi pi-info-circle',
      accept: () => {
        this.confirmationService.close();
        this.apiService.post('bendaharapenerimaan/save-batal-setoran-kasir', objSaveNew).subscribe(dataSave => {
          this.LoadData();
        })
      },
      reject: (type) => {
        this.alertService.warn('Info, Konfirmasi', 'Pembatalan Setoran Kasir Dibatalkan!');
        this.confirmationService.close();
        return;
      }
    });
  }

}
