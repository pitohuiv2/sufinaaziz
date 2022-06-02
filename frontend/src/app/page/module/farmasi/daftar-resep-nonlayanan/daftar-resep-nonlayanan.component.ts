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
  selector: 'app-daftar-resep-nonlayanan',
  templateUrl: './daftar-resep-nonlayanan.component.html',
  styleUrls: ['./daftar-resep-nonlayanan.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarResepNonlayananComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  item: any = {};
  listRuanganDepo: any[];
  dateNow: any;
  column: any[];
  listBtnCetak: MenuItem[];
  dataLogin: any;
  kelUser: any;
  listBtn:MenuItem[]
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
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.getDataList();
    this.getDataCombo();
  }

  getDataList() {
    this.listBtnCetak = [
      { label: 'Cetak Label Identitas', icon: 'fa fa-print', command: () => { this.ctkLabelIdentitas(); } },
      { label: 'Cetak Label Resep', icon: 'fa fa-print', command: () => { this.ctkLabelResep(); } },
      { label: 'Cetak Resep Obat', icon: 'fa fa-print', command: () => { this.ctkResepObat(); } },
    ];
    this.listBtn = [
 
      { label: 'Ubah Resep', icon: 'fa fa-medkit', command: () => { this.ubahResep(); } },
      { label: 'Hapus Resep', icon: 'fa fa-trash', command: () => { this.hapusResep(); } },
      { label: 'Retur Resep ', icon: 'fa fa-retweet', command: () => { this.returResep(); } },
    ];
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglstruk', header: 'Tgl Resep', width: "140px" },
      { field: 'noresep', header: 'No Resep', width: "140px" },
      { field: 'norm', header: 'No RM', width: "120px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "180px" },
      { field: 'notelp', header: 'No Telp', width: "140px" },
      { field: 'namaruangan', header: 'Depo', width: "180px" },
      { field: 'dokter', header: 'Dokter', width: "180px" },
      { field: 'nosbm', header: 'No Bukti Bayar', width: "180px" },
    ];
  }

  getDataCombo() {
    this.apiService.get("farmasi/get-combo-farmasi").subscribe(table => {
      var dataCombo = table;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuanganDepo = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuanganDepo = dataCombo.ruangfarmasi;
      }
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarResepNonLayananCache');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      if (chacePeriode[2] != undefined && chacePeriode[2] != "") {
        this.item.namaPasien = chacePeriode[2];
      }
      if (chacePeriode[3] != undefined && chacePeriode[3] != "") {
        this.item.noResep = chacePeriode[3]
      }
      if (chacePeriode[4] != undefined && chacePeriode[4] != "") {
        this.listRuanganDepo = [chacePeriode[4]]
        this.item.dataRuanganDepo = chacePeriode[4]
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
    var tempRuanganIdDepo = "";
    var tempRuanganIdDepoArr = undefined;
    if (this.item.instalasi != undefined) {
      tempRuanganIdDepo = this.item.dataRuanganDepo.id;
      tempRuanganIdDepoArr = { id: this.item.dataRuanganDepo.id, namaruangan: this.item.dataRuanganDepo.namaruangan }
    }

    var tempNamaPasien = "";
    if (this.item.namaPasien != undefined) {
      tempNamaPasien = this.item.namaPasien;
    }

    var tempnoResep = "";
    if (this.item.noResep != undefined) {
      tempnoResep = this.item.noResep;
    }

    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = this.item.jmlRows
    }

    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempNamaPasien,
      3: tempnoResep,
      4: tempRuanganIdDepoArr
    }
    this.cacheHelper.set('DaftarResepNonLayananCache', chacePeriode);

    this.apiService.get("farmasi/get-daftar-resep-nonlayanan?"
      + "namaPasien=" + tempNamaPasien
      + "&depoId=" + tempRuanganIdDepo
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noResep=" + tempnoResep
      + "&jmlRows=" + jmlRow).subscribe(datas => {
        var data = datas.daftar
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          var now = moment(this.dateNow).format('YYYY-MM-DD');
          var tgllahir = moment(new Date(element.tgllahir)).format('YYYY-MM-DD');
          var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
          element.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari';
        }
        this.dataTable = data;
      })
  }

  cari() {
    this.loadData();
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.selected = event.data
    }
  }

  inputResepBaru() {
    this.router.navigate(['input-resep-nonlayanan', '-']);
  }
  selectData(e) {
    this.selected = e
  }
  ubahResep() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.nosbm != undefined) {
      this.alertService.warn("Info", "Data Sudah Dibayar!");
      return;
    }

    this.router.navigate(['input-resep-nonlayanan', this.selected.norec_resep]);
  }

  hapusResep() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.nosbm != undefined) {
      this.alertService.warn("Info", "Data Sudah Dibayar!");
      return;
    }

    var objSave = {
      norec_sp: this.selected.norec,
    }

    this.apiService.post('farmasi/save-hapus-resep-nonlayanan', objSave).subscribe(e => {
      this.apiService.postLog('Hapus Pelayanan Resep Non Layanan', 'Norec strukpelayanan', this.selected.norec,
        'Hapus Pelayanan Resep Non Layanan Dengan Noresep - ' + this.selected.noresep + ' atas Nama Pasien - ' + this.selected.namapasien
      ).subscribe(res => { })
      this.loadData();
    })
  }

  returResep() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.nosbm != undefined) {
      this.alertService.warn("Info", "Data Sudah Dibayar!");
      return;
    }

    this.router.navigate(['retur-resep-nonlayanan', this.selected.norec_resep]);
  }

  ctkLabelIdentitas() {
    throw new Error('Method not implemented.');
  }

  ctkLabelResep() {
    throw new Error('Method not implemented.');
  }

  ctkResepObat() {
    throw new Error('Method not implemented.');
  }


}
