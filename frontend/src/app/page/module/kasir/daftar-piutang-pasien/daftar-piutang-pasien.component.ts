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
  selector: 'app-daftar-piutang-pasien',
  templateUrl: './daftar-piutang-pasien.component.html',
  styleUrls: ['./daftar-piutang-pasien.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPiutangPasienComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  item: any = {};
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  dateNow: any;
  column: any[];
  dataCheck: any[] = [];
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

  ngOnInit() {
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'noRegistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namaPasien', header: 'Nama Pasien', width: "250px" },
      { field: 'jenisPasisen', header: 'Tipe Pasien', width: "120px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "140px" },
      { field: 'totalBilling', header: 'Total Billing', width: "140px", isCurrency: true  },
      { field: 'totalKlaim', header: 'Total Klaim', width: "140px", isCurrency: true  },
      { field: 'totalBayar', header: 'Total Bayar', width: "140px", isCurrency: true  },
      // { field: 'noposting', header: 'No Collecting', width: "140px" },
      { field: 'multipenjamin', header: 'Multi Penjamin', width: "140px" },
      { field: 'statusVerifikasi', header: 'Stat Piutang', width: "180px" },
    ];
    this.getDataCombo();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  getDataCombo() {
    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.LoadCache();
    })
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.dataDepartemen.ruangan;
    }
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('PiutangkasirCtrl');
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
        this.item.jmlRows = chacePeriode[8]
      }
      this.getData();
    }
    else {
      this.getData();
    }
  }

  getData() {
    this.selected = [];
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

    var tempInstalasiId = "";
    var tempInstalasiIdArr = undefined;
    if (this.item.instalasi != undefined) {
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
      // 8: jmlRow
    }
    this.cacheHelper.set('PiutangkasirCtrl', chacePeriode);
    this.apiService.get("kasir/daftar-piutang-pasien?"
      + "namaPasien=" + tempNamaOrReg
      + "&ruanganId=" + tempRuanganId
      + "&status=" + tempStatus
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noReg=" + tempNoReg
      + "&instalasiId=" + tempInstalasiId
      + "&noRm=" + tempNoRm
      + "&jmlRows=" + jmlRow
      + "&kelompokPasienId=" + kelompokPasienId).subscribe(data => {
        var datas = data.data
        for (let i = 0; i < datas.length; i++) {
          const element = datas[i];
          element.no = i + 1;
          if (element.details.length > 0){
            element.multipenjamin = "✔"
          }else{
            element.multipenjamin = "-"
          }															
        }
        this.dataTable = datas;
      })
  }

  cari() {
    this.getData();
  }

  detailTagihan() {
    if(this.selected == undefined){
      this.alertService.warn("Info", "Untuk Masuk Detail Tagihan Pilih Hanya Satu Data!");
      return;
    }
    if (this.selected.length > 1) {
      this.alertService.warn("Info", "Untuk Masuk Detail Tagihan Pilih Hanya Satu Data!");
      return;
    }
    this.router.navigate(['detail-tagihan', this.selected[0].noRegistrasi])
  }

  verifikasiPiutang() {
    var dataPost = [];
    if (this.selected.length > 0) {
      for (let i = 0; i < this.selected.length; i++) {
        const element = this.selected[i];
        dataPost.push(element.norec);
      }
    }

    if (dataPost.length > 0) {
      var objSave = {
        "dataPiutang": dataPost
      }
      this.apiService.post('kasir/verify-piutang-pasien', objSave).subscribe(data => {
        this.getData();
      });
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!")
    }
  }

  unverifikasiPiutang(){
    var dataPost = [];
    if (this.selected.length > 0) {
      for (let i = 0; i < this.selected.length; i++) {
        const element = this.selected[i];
        dataPost.push(element.norec);
      }
    }

    if (dataPost.length > 0) {
      var objSave = {
        "dataPiutang": dataPost
      }
      this.apiService.post('kasir/cancel-verify-piutang-pasien', objSave).subscribe(data => {
        this.getData();
      });
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!")
    }
  }

}
