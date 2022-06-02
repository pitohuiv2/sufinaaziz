import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';

@Component({
  selector: 'app-daftar-registrasi-pasien',
  templateUrl: './daftar-registrasi-pasien.component.html',
  styleUrls: ['./daftar-registrasi-pasien.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarRegistrasiPasienComponent implements OnInit {
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
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit() {
    this.getData(this.page, this.rows, this.sortField, this.sortOrder, '')
  }

  getData(page: number, rows: number, sortfield: any, sortorder: any, search: any) {
    this.loading = true
    if (sortfield == undefined) sortfield = ''

    this.apiService.get('registrasi/get-pasien?' + this.pencarian + '&sortfield=' + sortfield + '&sortorder=' + sortorder + '&page=' + page + '&rows=' + rows).subscribe(table => {
      this.loading = true
      for (let i = 0; i < table.daftar.length; i++) {
        const element = table.daftar[i];
        element.tgllahir =  moment(new Date(element.tgllahir)).format('DD-MM-YYYY')
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
}
