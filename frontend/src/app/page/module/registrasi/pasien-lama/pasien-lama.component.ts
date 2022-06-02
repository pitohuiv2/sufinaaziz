import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { Router } from '@angular/router';
import { HelperService } from 'src/app/service/helperService';
import { CacheService } from 'src/app/service/cache.service';
@Component({
  selector: 'app-pasien-lama',
  templateUrl: './pasien-lama.component.html',
  styleUrls: ['./pasien-lama.component.css'],
  providers: [ConfirmationService]
})
export class PasienLamaComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[]
  pencarian: any = ''
  listData: any[];
  totalRecords: number;
  item: any = {}
  loading: boolean
  sortField: any
  sortOrder: any
  listBtn: MenuItem[];
  pop_riwayataReg: boolean
  itemD: any = {}
  dataitemDegistrasi: any[]
  now = new Date()
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private router: Router,
    private DateHelper: HelperService,
    private cacheHelper: CacheService,
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit() {
    this.listBtn = [
      { label: 'Pasien Baru', icon: 'pi pi-user-plus', routerLink: ['/pasien-baru/-/-/-'] },
    ];
    this.getData(this.page, this.rows, this.sortField, this.sortOrder, '')
  }

  getData(page: number, rows: number, sortfield: any, sortorder: any, search: any) {
    this.loading = true
    if (sortfield == undefined) sortfield = ''

    this.apiService.get('registrasi/get-pasien?' + this.pencarian + '&sortfield=' + sortfield + '&sortorder=' + sortorder + '&page=' + page + '&rows=' + rows).subscribe(table => {
      this.loading = true
      for (let i = 0; i < table.daftar.length; i++) {
        const element = table.daftar[i];
        element.tgllahir = moment(new Date(element.tgllahir)).format('DD-MM-YYYY')
        if (element.tglmeninggal == null) {
          element.statuspasien = 'Hidup'
          element.color = 'info'
        } else {
          element.statuspasien = 'Meninggal'
          element.color = 'danger'
        }
      }

      this.dataTable = table.daftar;
      this.totalRecords = table.totalRow
    });
  }
  cari() {
    var Rows = ''
    if (this.item.Rows != undefined) {
      Rows = "&Rows=" + this.item.Rows
    }
    var rm = ""
    if (this.item.noRM != undefined) {
      rm = "&norm=" + this.item.noRM
    }

    var pasien = ""
    if (this.item.namaPasien != undefined) {
      pasien = "&namaPasien=" + this.item.namaPasien
    }
    var ayah = ""
    if (this.item.namaAyah != undefined) {
      ayah = "&namaAyah=" + this.item.namaAyah
    }
    var almat = ""
    if (this.item.alamat != undefined) {
      almat = "&alamat=" + this.item.alamat
    }
    var nik = ""
    if (this.item.nik != undefined) {
      nik = "nik=" + this.item.nik
    }
    var bpjs = ""
    if (this.item.noBPJS != undefined) {
      bpjs = "&bpjs=" + this.item.noBPJS
    }

    if (this.item.namaAyah != undefined) {
      ayah = "&namaAyah=" + this.item.namaAyah
    }
    var tglLahirs = ""
    if (this.item.tglLahir != undefined) {
      tglLahirs = "tglLahir=" + moment(new Date(this.item.tglLahir)).format('YYYY-MM-DD')
    }
    this.pencarian = nik +
      tglLahirs +
      rm +
      pasien +
      ayah +
      almat + bpjs

    this.getData(this.page, this.rows, this.sortField, this.sortOrder, this.pencarian)
  }

  findSelectedIndex(): number {
    return this.dataTable.indexOf(this.selected);
  }

  loadLazy(event: LazyLoadEvent) {

    this.getData(event.first, event.rows, event.sortField, event.sortOrder, this.pencarian);
    this.page = event.first;
    this.rows = event.rows;
    this.sortField = event.sortField
    this.sortOrder = event.sortOrder
  }
  onRowSelect(event: any) {
    this.selected = event.data
  }
  edit(e) {
    this.cacheHelper.set('cacheStatusPasien', 'LAMA');
    this.router.navigate(['pasien-baru', '-', e.nocmfk, '-'])
  }
  hapus(e) {

    var item = {
      idpasien: e.nocmfk
    }

    this.apiService.post('registrasi/update-false-pasien', item).subscribe(e => {
      this.getData(this.page, this.rows, this.sortField, this.sortOrder, '')
    })

  }
  confirm(event: Event, data) {
    this.confirmationService.confirm({
      target: event.target,
      message: 'Yakin mau hapus?',
      icon: 'pi pi-exclamation-triangle',
      acceptLabel: "Ya",
      rejectLabel: "Tidak",
      accept: () => {
        this.hapus(data)
      },
      reject: () => {
        //reject action
      }
    });
  }
  riwayat(e) {
    this.itemD.noRM = e.nocm;
    this.itemD.namaPasien = e.namapasien;
    this.itemD.tglLahir = moment(e.tgllahir).format('DD-MM-YYYY');
    this.loadDataRiwayat()
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
    this.pop_riwayataReg = true

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
              var umur = this.DateHelper.CountAge(new Date(ditemDeg[i].tglregistrasi), new Date(ditemDeg[i].tglpulang));
              var bln = umur.month,
                thn = umur.year,
                day = umur.day
              ditemDeg[i].lamarawat = day + " Hari";
            } else {
              var umur = this.DateHelper.CountAge(new Date(ditemDeg[i].tglregistrasi), new Date(this.now));
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

  registrasi(e) {
    this.cacheHelper.set('CacheRegistrasiPasien', undefined)
    this.router.navigate(['registrasi-ruangan', e.nocmfk])
  }
}
