import { Component, OnInit, Inject, forwardRef } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { AppComponent } from './../../../app.component';
import * as $ from "jquery";
import { FormGroup, FormBuilder, FormControl } from '@angular/forms';

import * as moment from 'moment'
import { ApiService } from 'src/app/service';
import { CacheService } from 'src/app/service/cache.service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { SelectItem } from 'primeng/api';
@Component({
  selector: 'app-info-kritik',
  templateUrl: './info-kritik.component.html',
  styleUrls: ['./info-kritik.component.scss']
})
export class InfoKritikComponent implements OnInit {

  nomorEMR: any = '-'
  namaEMR: any = 1
  listData: any = {}
  item: any = {
    obj: [],
    obj2: [],
    deskripsi: '',
    tgl: new Date()
  }

  jenisEMR = 'asesmen'
  displayDialog: boolean
  title: any
  public rows: any;

  isLoad: boolean

  loading: boolean = false
  options: any
  contentHeader: any
  showTarif: boolean

  listRuangan: any[]//SelectItem[]
  listJenisPel: any[]//SelectItem[]
  listKelas: any[]//SelectItem[]
  isKritik: boolean
  isKeluhan: boolean
  isQuis: boolean
  constructor(private router: Router,
    private route: ActivatedRoute,
    private httpService: ApiService,
    private fb: FormBuilder,
    private cacheHelper: CacheService,
    private alertService: AlertService,
    private service: HttpClient,) { }

  ngOnInit(): void {
  }
  changePel() {

    this.httpService.get("kiosk/get-data-ruangan?jenis=" + this.item.pelayanan).subscribe(e => {

      this.listRuangan = e.ruangan
    })
  }
  CountAge(birthday, dataNow) {

    if (birthday === undefined || birthday === '')
      birthday = Date.now();
    else {
      if (birthday instanceof Date) {

      } else {
        var arr = birthday.split('-');
        if (arr[0].length === 4) {
          birthday = new Date(arr[0], arr[1], arr[2]);
        } else {
          birthday = new Date(arr[2], arr[1], arr[0]);
        }
      }

    }
    if (dataNow === undefined)
      dataNow = Date.now();
    var ageDifMs = dataNow - birthday;
    var ageDate = new Date(ageDifMs); // miliseconds from epoch
    var year = ageDate.getFullYear() - 1970;
    if (year <= -1)
      year = 0;
    var day = ageDate.getDate() - 1;
    var date = new Date(year, ageDate.getMonth(), day);
    return {
      year: year,
      month: ageDate.getMonth(),
      day: day,
      date: date
    };
  }
  saveKritik() {
    if (!this.item.namaPengisi) {
      this.alertService.warn('Info', 'Nama Pengisi harus di isi')
      return;
    }
    if (!this.item.namapasien) {
      this.alertService.warn('Info', 'Nama Pasien harus di isi')
      return;

    }
    if (!this.item.noHp) {
      this.alertService.warn('Info', 'No HP harus di isi')
      return;

    }
    if (!this.item.kritik) {
      this.alertService.warn('Info', 'Kritik & Saran harus di isi')
      return;

    }
    if (!this.item.komplain) {
      this.alertService.warn('Info', 'Komplain harus di isi')
      return;

    }
    let umur = ''
    if (this.item.tgllahir) {
      let tgllahir = this.item.tgllahir

      var age: any = this.CountAge(tgllahir, new Date());
      var bln = age.month,
        thn = age.year,
        day = age.day
      umur = thn + 'thn ' + bln + 'bln ' + day + 'hr '
    }

    let data = {
      "id": "",
      "alamat": "",
      "email": "",
      "keluhan": this.item.komplain,
      "namapasien": this.item.namapasien,
      "norm": "",
      "notlp": this.item.noHp,
      "objectruanganfk": this.item.ruangan ? this.item.ruangan.id : null,
      "saran": this.item.kritik,
      "objectpekerjaanfk": null,
      "umur": umur,
      "tglkeluhan": moment(this.item.tgl).format('YYYY-MM-DD'),
      "tglorder": null,
      "notlpkntr": null,
      "objectjeniskelaminfk": null,
      "namapengisi": this.item.namaPengisi
    }
    var json = {
      data: data,
    }
    this.httpService.post("kiosk/save-keluhan-pelanggan", json).subscribe(e => {
      this.closeAll()

    })
  }
  closeAll() {
    this.item = {}
    window.history.back()
  }
}
