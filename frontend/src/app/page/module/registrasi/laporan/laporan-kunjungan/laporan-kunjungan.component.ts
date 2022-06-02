import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from 'src/app/guard';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-laporan-kunjungan',
  templateUrl: './laporan-kunjungan.component.html',
  styleUrls: ['./laporan-kunjungan.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class LaporanKunjunganComponent implements OnInit {
  page: number;
  rows: number;
  column: any[];
  selected: any;
  dataTable: any[] = [];
  item: any = {};
  dateNow: any;
  listDepartemen: any[];
  listRuangan: any[];
  listTipePasien: any[];
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
    this.dateNow = new Date();
    this.item.bulan = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("laporan/get-data-combo").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemenrajal;
      this.listTipePasien = dataCombo.kelompokpasien;
      this.item.dataDepartemen = this.listDepartemen[0];
      this.isiRuangan();
      this.LoadCache();
    })
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.dataDepartemen.ruangan;
    }
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('kunjunganCache');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.getData();
    }
    else {
      this.getData();
    }
  }

  getData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var bulan = moment(this.item.bulan).format('MM-YYYY');
    var idDept = "";

    if (this.item.dataDepartemen != undefined) {
      idDept = "&idDept=" + this.item.dataDepartemen.id;
    }

    var idTipePasien = "";
    if (this.item.dataTipePasien != undefined) {
      idTipePasien = "&idTipePasien=" + this.item.dataTipePasien.id;
    }

    var idRuangan = "";
    if (this.item.dataRuangan != undefined) {
      idRuangan = "&idRuangan=" + this.item.dataRuangan.id;
    }

    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
    }
    this.cacheHelper.set('kunjunganCache', chacePeriode);
    this.apiService.get("laporan/get-data-lap-kunjungan?"
      + "&bulan=" + bulan
      + "&tglAwal=" + tglAwal + "&tglAkhir=" + tglAkhir +
      idDept + idRuangan + idTipePasien
    ).subscribe(data => {
      var dataGrid = data;
      for (let i = 0; i < dataGrid.length; i++) {
        const element = dataGrid[i];
        element.no = i + 1;
      }
      this.dataTable = dataGrid;
    })
  }

  cari() {
    this.getData();
  }
  exportExcel() {
    var profile = this.authService.dataLoginUser.profile.id;
    var namauser = this.authService.dataLoginUser.pegawai.namaLengkap;
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var bulan = moment(this.item.bulan).format('MM-YYYY');

    var idDept = "";
    if (this.item.dataDepartemen != undefined) {
      idDept = "&idDept=" + this.item.dataDepartemen.id;
    }

    var idRuangan = "";
    if (this.item.dataRuangan != undefined) {
      idRuangan = "&idRuangan=" + this.item.dataRuangan.id;
    }

    var idTipePasien = "";
    if (this.item.dataTipePasien != undefined) {
      idTipePasien = "&idTipePasien=" + this.item.dataTipePasien.id;
    }

    var idRuangan = "";
    if (this.item.dataRuangan != undefined) {
      idRuangan = "&idRuangan=" + this.item.dataRuangan.id;
    }

    window.open(Config.get().apiBackend + "print/cetak-laporan-kunjungan-poliklinik?" +
      "&tglAwal=" + tglAwal + "&tglAkhir=" + tglAkhir + "&bulan=" + bulan +
      idDept + idRuangan + idTipePasien +  "&kodeprofile=" + profile + "&namauser=" + namauser);

  }
}
