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
  selector: 'app-laporan-sensus-bulanan-igd',
  templateUrl: './laporan-sensus-bulanan-igd.component.html',
  styleUrls: ['./laporan-sensus-bulanan-igd.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class LaporanSensusBulananIgdComponent implements OnInit {
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
      this.listDepartemen = dataCombo.departemenigd;
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
    var chacePeriode = this.cacheHelper.get('SensusCache');
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
    var kodeprofile = 1;
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

    var listRuang = ""
    if (this.item.selectedRuangan != undefined) {
      var a = ""
      var b = ""
      for (var i = this.item.selectedRuangan.length - 1; i >= 0; i--) {

        var c = this.item.selectedRuangan[i].id
        b = "," + c
        a = a + b
      }
      listRuang = a.slice(1, a.length)
    }

    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
    }
    this.cacheHelper.set('SensusCache', chacePeriode);
    this.apiService.get("laporan/get-data-lap-sensus-bulanan-igd?"
      + "&bulan=" + bulan
      + "&tglAwal=" + tglAwal + "&tglAkhir=" + tglAkhir +
      idDept + idRuangan + idTipePasien + "&listRuang=" + listRuang + "&kodeprofile=" + kodeprofile
    ).subscribe(data => {
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
      }
      var dataGrid = data;
      this.dataTable = dataGrid;
    })
  }

  cari() {
    this.getData();
  }

  exportExcel() {
    var profile = this.authService.dataLoginUser.profile.id;
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

    var listRuang = ""
    var paramRuang = ""
    if (this.item.selectedRuangan != undefined) {
      var a = ""
      var b = ""
      for (var i = this.item.selectedRuangan.length - 1; i >= 0; i--) {

        var c = this.item.selectedRuangan[i].id
        b = "," + c
        a = a + b
      }
      listRuang = a.slice(1, a.length)
    }
    if (listRuang != "") {
      paramRuang = "&listRuang=" + listRuang
    }

    window.open(Config.get().apiBackend + "print/cetak-laporan-sensus-bulanan-igd?"
      + "&bulan=" + bulan
      + "&tglAwal=" + tglAwal + "&tglAkhir=" + tglAkhir +
      idDept + idRuangan + idTipePasien + paramRuang + "&kodeprofile=" + profile);
  }

}
