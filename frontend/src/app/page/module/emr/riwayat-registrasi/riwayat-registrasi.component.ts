import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { ActivatedRoute, Router } from '@angular/router';
import { HelperService } from 'src/app/service/helperService';
import { CacheService } from 'src/app/service/cache.service';
import { RekamMedisComponent } from '../rekam-medis/rekam-medis.component';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-riwayat-registrasi',
  templateUrl: './riwayat-registrasi.component.html',
  styleUrls: ['./riwayat-registrasi.component.scss']
})
export class RiwayatRegistrasiComponent implements OnInit {
  itemD: any = {}

  dataitemDegistrasi: any[]
  constructor(public rekamMedis: RekamMedisComponent,
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,) { }

  ngOnInit(): void {
    var cache = this.cacheHelper.get('cacheEMR_qwertyuiop')
    // debugger
    if (cache != undefined) {
      cache = JSON.parse(cache)
      this.itemD.noRM = cache.nocm
      this.itemD.namaPasien = cache.namapasien
      this.itemD.noRegistrasi = cache.noregistrasi
      this.itemD.tglLahir = moment(cache.tgllahir).format('DD-MM-YYYY');
      this.loadDataRiwayat()
    } else {
      window.history.back()
    }

  }
  loadDataRiwayat() {
    var rm = ""
    if (this.itemD.noRM != undefined) {
      rm = "&norm=" + this.itemD.noRM
    }

    var pasien = ""
    if (this.itemD.namaPasien != undefined) {
      pasien = "&namaPasien=" + this.itemD.namaPasien
    }

    var tglLahirs = ""
    if (this.itemD.tglLahir != undefined) {
      tglLahirs = "tglLahir=" + moment(this.itemD.tglLahir).format('YYYY-MM-DD HH:mm:ss');
    }

    var noReg = ""
    if (this.itemD.noRegistrasi != undefined) {
      noReg = "&noReg=" + this.itemD.noRegistrasi;
    }

    this.apiService.get("registrasi/daftar-riwayat-registrasi?" +
      tglLahirs + rm + noReg + pasien)
      .subscribe(data => {

        var jumlahRawat = 0;
        var ditemDeg = data.daftar;
        for (var i = 0; i < ditemDeg.length; i++) {
          ditemDeg[i].no = i + 1
          if (ditemDeg[i].statusinap == 1) {
            jumlahRawat = jumlahRawat + 1;
            if (ditemDeg[i].tglpulang != undefined) {
              var umur = this.dateHelper.CountAge(new Date(ditemDeg[i].tglregistrasi), new Date(ditemDeg[i].tglpulang));
              var bln = umur.month,
                thn = umur.year,
                day = umur.day
              ditemDeg[i].lamarawat = day + " Hari";
            } else {
              var umur = this.dateHelper.CountAge(new Date(ditemDeg[i].tglregistrasi), new Date());
              var bln = umur.month,
                thn = umur.year,
                day = umur.day
              ditemDeg[i].lamarawat = day + " Hari";
            }
          }
        }
        this.itemD.JumlahRawat = jumlahRawat;
        this.dataitemDegistrasi = ditemDeg
      });
  }
}
