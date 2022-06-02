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
  selector: 'app-daftar-retur-resep',
  templateUrl: './daftar-retur-resep.component.html',
  styleUrls: ['./daftar-retur-resep.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarReturResepComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {};
  listDepartemen: any[];
  listRuangan: any[];
  listRuanganDepo: any[];
  listKelompokPasien: any[];
  listRuanganApd: any[];
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  listBtnCetak: MenuItem[];
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
    this.dateNow = new Date();
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.LoadColumnButton();
    this.getDataCombo();
  }

  LoadColumnButton() {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglretur', header: 'Tgl Retur', width: "140px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'noretur', header: 'No Retur', width: "110px" },
      { field: 'noresep', header: 'No Resep', width: "110px" },
      { field: 'nocm', header: 'No RM', width: "100px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "220px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'alasanretur', header: 'Ket Retur', width: "220px" },
    ];
    this.listBtnCetak = [
      { label: 'Cetak Bukti Retur', icon: 'fa fa-print', command: () => { this.ctkBuktiRetur(); } },
    ];
  }

  getDataCombo() {
    this.apiService.get("farmasi/get-combo-farmasi").subscribe(table => {
      var dataCombo = table;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuangan = dataCombo.ruangfarmasi;
      }
      this.item.dataRuangan = this.listRuangan[0];
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarReturResepCache');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.item.status = chacePeriode[2]
      this.item.namaOrReg = chacePeriode[3]
      if (chacePeriode[6] != undefined) {
        this.listDepartemen = [chacePeriode[6]]
        this.item.dataDepartemen = chacePeriode[6]
      }
      if (chacePeriode[5] != undefined) {
        this.listRuangan = [chacePeriode[5]]
        this.item.dataRuangan = chacePeriode[5]
      }

      if (chacePeriode[4] != undefined && chacePeriode[4] != "") {
        this.item.noReg = chacePeriode[4]
      }
      if (chacePeriode[7] != undefined && chacePeriode[7] != "") {
        this.item.noRm = chacePeriode[7]
      }
      this.loadData();
    }
    else {
      this.loadData();
    }
  }

  loadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');

    var tempRuanganId = "";
    var tempRuanganIdArr = undefined;
    if (this.item.ruangan != undefined) {
      tempRuanganId = this.item.dataRuangan.id;
      tempRuanganIdArr = { id: this.item.dataRuangan.id, ruangan: this.item.dataRuangan.ruangan }
    }

    var tempStatus = "";
    var tempStatusArr = undefined;
    // if (this.item.status != undefined) {
    //   tempStatus = this.item.status.namaExternal;
    //   tempStatusArr = { id: this.item.status.id, namaExternal: this.item.status.namaExternal }
    // }

    var tempRuanganIdDepo = "";
    var tempRuanganIdDepoArr = undefined;
    if (this.item.instalasi != undefined) {
      tempRuanganIdDepo = this.item.dataRuanganDepo.id;
      tempRuanganIdDepoArr = { id: this.item.dataRuanganDepo.id, namaruangan: this.item.dataRuanganDepo.namaruangan }
    }

    var tempInstalasiId = "";
    var tempInstalasiIdArr = undefined;
    if (this.item.instalasi != undefined) {
      tempInstalasiId = this.item.dataDepartemen.id;
      tempInstalasiIdArr = { id: this.item.dataDepartemen.id, departemen: this.item.dataDepartemen.departemen }
    }

    var tempNoRm = "";
    if (this.item.noRM != undefined) {
      tempNoRm = this.item.noRM;
    }

    var tempNoReg = "";
    if (this.item.Noregistrasi != undefined) {
      tempNoReg = this.item.Noregistrasi;
    }

    var tempNamaOrReg = "";
    if (this.item.namaPasien != undefined) {
      tempNamaOrReg = this.item.namaPasien;
    }

    var tempnoResep = "";
    if (this.item.noResep != undefined) {
      tempnoResep = this.item.noResep;
    }

    var tempnoRetur = "";
    if (this.item.noRetur != undefined) {
      tempnoRetur = this.item.noRetur;
    }

    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = this.item.jmlRows
    }

    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempStatusArr,
      3: tempNamaOrReg,
      4: tempNoReg,
      5: tempRuanganIdArr,
      6: tempInstalasiIdArr,
      7: tempNoRm,
    }
    this.cacheHelper.set('DaftarReturResepCache', chacePeriode);

    this.apiService.get("farmasi/get-daftar-retur-obat?"
      + "namaPasien=" + tempNamaOrReg
      + "&ruanganId=" + tempRuanganId
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noReg=" + tempNoReg
      + "&noRm=" + tempNoRm
      + "&noResep=" + tempnoResep
      + "&noRetur=" + tempnoRetur
      + "&jmlRows=" + jmlRow).subscribe(datas => {
        var data = datas.daftar
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
        }
        this.dataTable = data;
      })
  }

  cari() {
    this.loadData();
  }

  ctkBuktiRetur() {
    throw new Error('Method not implemented.');
  }
  onRowSelect(e){
    
  }

}
