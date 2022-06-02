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
  selector: 'app-daftar-pasien-deposit-lebih',
  templateUrl: './daftar-pasien-deposit-lebih.component.html',
  styleUrls: ['./daftar-pasien-deposit-lebih.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPasienDepositLebihComponent implements OnInit {
  item: any = {};
  listJenisTransaksi: any[];
  dateNow: any;
  column: any[];
  selected: any;
  dataTable: any[];
  dataLogin: any;
  kelUser: any;
  popFilter: boolean = false;
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  idKdRanap: any;
  totalRecords: number;
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
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglMasuk', header: 'Tgl Masuk', width: "140px" },
      { field: 'tglPulang', header: 'Tgl Pulang', width: "140px" },
      { field: 'tglStruk', header: 'Tgl Deposit', width: "140px" },
      { field: 'noCm', header: 'No RM', width: "150px" },
      { field: 'noRegistrasi', header: 'Noregistrasi', width: "150px" },
      { field: 'jenisPasien', header: 'Tipe Pasien', width: "150px" },      
      { field: 'namaPasien', header: 'Nama Pasien', width: "250px" },
      { field: 'lastRuangan', header: 'Ruang Rawat', width: "200px" },
      { field: 'sisdeposit', header: 'Total Deposit Sisa', width: "180px", isCurrency: true },      
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.idKdRanap = dataCombo.kdRanap;
      this.LoadCache();
    })
  }
  onRowSelect(e){

  }
  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
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
        this.item.jmlRows = chacePeriode[8]
      }
      this.loadData();
    }
    else {
      this.loadData();
    }
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.dataDepartemen.ruangan;
    }
  }

  loadData(){    
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');

    var tempRuanganId = "";
    var tempRuanganIdArr = undefined;
    if (this.item.dataRuangan != undefined) {
      tempRuanganId = this.item.dataRuangan.id;
      tempRuanganIdArr = { id: this.item.dataRuangan.id, ruangan: this.item.dataRuangan.ruangan }
    }

    var tempStatus = "";
    var tempStatusArr = undefined;
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
    }
    this.cacheHelper.set('DaftarPasienPulangCtrl', chacePeriode);

    this.apiService.get("kasir/get-data-tagihan-pasien?"
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
        var data = data
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          element.sisdeposit = Math.abs(element.totalBayar)
        }
        this.dataTable = data;
        this.totalRecords = data.totalRow;
      })

  }

  filter() {
    this.popFilter = true
  }

  cariFilter() {
    this.popFilter = false
    this.loadData();
  }
  cari(){
    
  }
  clearFilter() {
    this.popFilter = false
    this.item.dataRuangan = undefined;
    this.item.dataDepartemen = undefined;
    this.item.dataKelPasien = undefined;
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.loadData();
  }

  kembalianDeposit(selected) {    
    var jumlahDeposit = 0
    if (parseFloat(selected.sisdeposit) < 0) {
      this.alertService.warn("Info", "Nilai tidak boleh negatif!")
      return
    }    
    jumlahDeposit = parseFloat(selected.totalBayar) * (-1)
    this.router.navigate(['bayar-tagihan-pasien', selected.norec_pd, "PenyetoranDepositKasirKembali", selected.sisdeposit]);
  }

}
