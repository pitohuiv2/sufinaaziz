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
  selector: 'app-daftar-resep-pasien',
  templateUrl: './daftar-resep-pasien.component.html',
  styleUrls: ['./daftar-resep-pasien.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarResepPasienComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  item: any = {};
  listDepartemen: any[];
  listRuangan: any[];
  listRuanganDepo: any[];
  listKelompokPasien: any[];
  listRuanganApd: any[];
  dateNow: any;
  column: any[];
  pop_inputTindakan: boolean;
  listBtnCetak: MenuItem[];
  popFilter:boolean;
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
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglresep', header: 'Tgl Resep', width: "140px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'noresep', header: 'No Resep', width: "110px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'namaruanganapotik', header: 'Depo', width: "180px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "120px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "140px" },
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("farmasi/get-combo-farmasi").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.listRuanganDepo = dataCombo.ruangfarmasi;
      this.LoadCache();
    })
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.dataDepartemen.ruangan;
    }
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarPasienPulangCtrl');
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
      if (chacePeriode[8] != undefined && chacePeriode[8] != "") {
        this.listRuanganDepo = [chacePeriode[8]]
        this.item.dataRuanganDepo = chacePeriode[8]
      }
      this.getData();
    }
    else {
      this.getData();
    }
  }

  getData() {

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
    if (this.item.dataRuanganDepo != undefined) {
      tempRuanganIdDepo = this.item.dataRuanganDepo.id;
      tempRuanganIdDepoArr = { id: this.item.dataRuanganDepo.id, namaruangan: this.item.dataRuanganDepo.namaruangan }
    }

    var tempInstalasiId = "";
    var tempInstalasiIdArr = undefined;
    if (this.item.dataDepartemen != undefined) {
      tempInstalasiId = this.item.dataDepartemen.id;
      tempInstalasiIdArr = { id: this.item.dataDepartemen.id, departemen: this.item.dataDepartemen.departemen }
    }

    var kelompokPasienId = ""
    if (this.item.dataKelPasien != undefined) {
      kelompokPasienId = this.item.dataKelPasien.id
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
      8: tempRuanganIdDepoArr
    }
    this.cacheHelper.set('DaftarPasienPulangCtrl', chacePeriode);

    this.apiService.get("farmasi/get-daftar-resep?"
      + "namaPasien=" + tempNamaOrReg
      + "&ruanganId=" + tempRuanganId
      + "&depoId=" + tempRuanganIdDepo
      + "&status=" + tempStatus
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noReg=" + tempNoReg
      + "&instalasiId=" + tempInstalasiId
      + "&noRm=" + tempNoRm
      + "&noResep=" + tempnoResep
      + "&jmlRows=" + jmlRow
      + "&kelompokPasienId=" + kelompokPasienId).subscribe(datas => {
        var data = datas.daftar
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          var now = moment(new Date(element.tglregistrasi)).format('YYYY-MM-DD');
          var tgllahir = moment(new Date(element.tgllahir)).format('YYYY-MM-DD');
          var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
          element.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari';
        }
        this.dataTable = data;
      })
  }

  cari() {
    this.getData();
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
        if (data.length > 0) {
          this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
          return;
        } else {
          this.selected = event.data
        }
      })
    }
  }

  transaksiLayanan() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    var jenisdata = "resep";
    var arrStr = {
      0: this.selected.norec,
      1: "",
    }
    this.cacheHelper.set('rincianPelayananFarmasiCache', arrStr);
    this.router.navigate(['transaksi-pelayanan-apotik', this.selected.norec_pd, jenisdata])
  }
  popUpInputObat2(e) {
    this.selected = e
    this.popUpInputObat()
  }
  transaksiLayanan2(e) {
    this.selected = e
    this.transaksiLayanan()
  }
  popUpInputObat() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    this.item.norec_dpr = ''
    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.apiService.get("registrasi/get-data-antrian-pasien?noregistrasi=" + this.selected.noregistrasi).subscribe(data => {
          var datas = data;
          this.listRuanganApd = datas.ruangan;
          if (datas.ruangan != undefined) {
            this.item.dataRuanganApd = datas.ruangan[0]
            this.item.norec_dpr = datas.ruangan[0].norec_apd
            this.pop_inputTindakan = true
          }
        })
      }
    })
  }

  inputObat() {
    if (this.item.dataRuanganApd == undefined) {
      this.alertService.warn("Info", "Ruang Antrian Belum Dipilih!");
      return;
    }

    var arrStr = {
      0: this.selected.norm,
      1: this.selected.namapasien,
      2: this.selected.jeniskelamin,
      3: this.selected.noregistrasi,
      4: this.selected.umur,
      5: this.selected.klid,
      6: this.selected.namakelas,
      7: this.selected.tglregistrasi,
      8: this.item.dataRuanganApd.norec_apd,
      9: "",
      10: this.selected.kelompokpasien,
      11: this.selected.namaruangan,
      12: this.selected.alamatlengkap,
      13: '',
      14: '',
      15: '',
      16: ''
    }
    this.cacheHelper.set('InputResepPasienCtrl', arrStr);
    this.router.navigate(['input-resep-apotik', this.selected.norec_pd, this.item.dataRuanganApd.norec_apd])
  }
  filter() {
    this.popFilter = true
  }
  cariFilter() {
    this.popFilter = false
    this.getData();
  }
  clearFilter() {
    this.popFilter = false
    this.item = {}
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.getData()
  }
}
