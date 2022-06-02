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
  selector: 'app-daftar-triase-pasien',
  templateUrl: './daftar-triase-pasien.component.html',
  styleUrls: ['./daftar-triase-pasien.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarTriasePasienComponent implements OnInit {
  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {}
  dataLogin: any;
  kelUser: any;
  dateNow: any;
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
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    // this.item.tglLahir = moment(this.dateNow).format('YYYY-MM-DD');
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'nocm', header: 'No RM', width: "120px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'jeniskelamin', header: 'Jenis Kelamin', width: "140px" },
      { field: 'tgllahir', header: 'Tgl Lahir', width: "140px" },
      { field: 'alamatlengkap', header: 'Alamat', width: "200px" },
      { field: 'notelepon', header: 'Telepon', width: "120px" },
      { field: 'noemr', header: 'No EMR', width: "140px" },
      { field: 'tglemr', header: 'Tgl EMR', width: "140px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "120px" },
    ];
    this.loadData();
  }

  loadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var rm = ""
    if (this.item.noRM != undefined) {
      rm = "&norm=" + this.item.noRM
    }

    var pasien = ""
    if (this.item.NamaPasien != undefined) {
      pasien = "&namaPasien=" + this.item.NamaPasien;
    }

    var idPasien = ""
    if (this.item.idPasienTriase != undefined) {
      idPasien = "&idPasienTriase=" + this.item.idPasienTriase;
    }

    var tglLahirs = ""
    if (this.item.tglLahir != undefined) {
      tglLahirs = "&tglLahir=" + new Date(this.item.tglLahir);
    }

    this.apiService.get("emr/get-data-riwayat-emr?"
      + "tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + tglLahirs + rm + pasien + idPasien + "&jenisEMR=igd").subscribe(table => {
        var datas = table.data;
        for (let i = 0; i < datas.length; i++) {
          const element = datas[i];
          element.no = i + 1;
          var now = new Date();
          var tgllahir = moment(new Date(element.tgllahir)).format('YYYY-MM-DD');
          var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
          element.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari';
        }
        this.dataTable = datas;
      });
  }

  cari() {
    this.loadData();
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.selected = event.data
    }
  }

  RegistrasiPasien2(e){
    if (e.noregistrasi != undefined) {
      this.alertService.warn("Info,", "Pasien Sudah Terdaftar, Tidak Bisa Didaftarkan, Peringatan !");
      return;
    }

    if (e.status == "pasienlama") {
      this.cacheHelper.set('CacheRegistrasiPasien', undefined)
      this.router.navigate(['registrasi-ruangan',e.idpasien])
    } else {
      this.selected  =e
      if (this.selected != undefined) {
        var header = {
          nocm: this.selected.nocm,
          namaPasien: this.selected.namapasien,
          jkid: this.selected.jeniskelaminidfk,
          jk: this.selected.jeniskelamin,
          alamatlengkap: this.selected.alamatlengkap,
          tempatlahir: this.selected.tempatlahir,
          tgllahir: moment(this.selected.tgllahir).format('YYYY-MM-DD HH:mm'),
          notelepon: this.selected.notelepon,
          noemr: this.selected.noemr
        }
        this.cacheHelper.set('CacheRegisTriage', header)
        this.router.navigate(['pasien-baru', '-', '-', '-'])        
      }
    }
  }
  RegistrasiPasien() {        
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.noregistrasi != undefined) {
      this.alertService.warn("Info,", "Pasien Sudah Terdaftar, Tidak Bisa Didaftarkan, Peringatan !");
      return;
    }

    if (this.selected.status == "pasienlama") {
      this.cacheHelper.set('CacheRegistrasiPasien', undefined)
      this.router.navigate(['registrasi-ruangan', this.selected.idpasien])
    } else {
      if (this.selected != undefined) {
        var header = {
          nocm: this.selected.nocm,
          namaPasien: this.selected.namapasien,
          jkid: this.selected.jeniskelaminidfk,
          jk: this.selected.jeniskelamin,
          alamatlengkap: this.selected.alamatlengkap,
          tempatlahir: this.selected.tempatlahir,
          tgllahir: moment(this.selected.tgllahir).format('YYYY-MM-DD HH:mm'),
          notelepon: this.selected.notelepon,
          noemr: this.selected.noemr
        }
        this.cacheHelper.set('CacheRegisTriage', header)
        this.router.navigate(['pasien-baru', '-', '-', '-'])        
      }
    }
  }

}
