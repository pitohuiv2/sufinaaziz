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
  selector: 'app-daftar-pasien-batal',
  templateUrl: './daftar-pasien-batal.component.html',
  styleUrls: ['./daftar-pasien-batal.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPasienBatalComponent implements OnInit {

  selected: any;
  dataTable: any[];
  listData: any[];
  item: any = {};
  loading: boolean;
  dataLogin: any;
  kelUser: any;
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  listRuanganApd: any[];
  dateNow: any;
  column: any[];
  pop_inputTindakan: boolean;
  pop_inputDiagnosa: boolean;
  pop_DokterPJawab: boolean;
  listDokter: any[];
  listKriteria: any[] = [
    { name: 'Semua', id: '1' }, { name: 'Masih Dirawat', id: '2' }
  ]
  disableTgl: boolean
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) {
  }

  ngOnInit() {
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tanggalpembatalan', header: 'Tgl Batal', width: "140px" },
      { field: 'norm', header: 'No RM', width: "80px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'name', header: 'Pembatal', width: "180px" },
      { field: 'namalengkap', header: 'Pegawai', width: "250px" },
      { field: 'alasanpembatalan', header: 'Alasan', width: "200px" },
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    
      this.LoadCache();
  
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarBatal');
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


  cari() {
    this.getData()
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
    var isCanBalik = false
    if (this.item.isCanBalik != undefined) {
      isCanBalik = this.item.isCanBalik
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
    this.cacheHelper.set('DaftarBatal', chacePeriode);

    this.apiService.get("registrasi/get-daftar-pasienbatal?"
      + "namaPasien=" + tempNamaOrReg
      + "&ruanganId=" + tempRuanganId
      + "&status=" + tempStatus
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noReg=" + tempNoReg
      + "&instalasiId=" + tempInstalasiId
      + "&noRm=" + tempNoRm
      + "&jmlRows=" + jmlRow
      + "&kelompokPasienId=" + kelompokPasienId
      + "&isCanBalik=" + isCanBalik).subscribe(data => {
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
        }
        this.dataTable = data;
      })
  }

  onRowSelect(event: any) {
    this.selected = event.data
  }
}

