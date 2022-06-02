import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { HelperService } from 'src/app/service/helperService';
import { CacheService } from 'src/app/service/cache.service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-daftar-pasien-terdaftar',
  templateUrl: './daftar-pasien-terdaftar.component.html',
  styleUrls: ['./daftar-pasien-terdaftar.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPasienTerdaftarComponent implements OnInit {
  page: number;
  rows: number=50;
  selected: any;
  dataTable: any[];
  column: any[];
  pencarian: any = ''
  listData: any[];
  isTglRegistrasi:boolean
  totalRecords: number;
  item: any = {}
  loading: boolean
  sortField: any
  sortOrder: any
  listBtn: MenuItem[];
  pop_riwayataReg: boolean
  itemD: any = {}
  dataitemDegistrasi: any[]
  now = new Date();
  pop_DataPasien: boolean;
  listJenisKelamin: any[];
  isSimpan: boolean = false;
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private router: Router,
    private DateHelper: HelperService,
    private cacheHelper: CacheService,
    private alertService: AlertService,
    private route: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'nocm', header: 'No RM', width: "80px" },
      { field: 'noidentitas', header: 'NIK', width: "140px" },
      { field: 'nobpjs', header: 'No BPJS', width: "140px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'jeniskelamin', header: 'Jenis Kelamin', width: "100px" },
      { field: 'namaayah', header: 'Nama Ayah Kandung', width: "180px" },
      { field: 'tgllahir', header: 'Tgl Lahir', width: "140px" },
      { field: 'alamatlengkap', header: 'Alamat', width: "220px" },
      { field: 'nohp', header: 'No Hp', width: "100px" },
      { field: 'statuspasien', header: 'Status Pasien', width: "140px" }
    ];
    this.getData(this.page, this.rows, this.sortField, this.sortOrder, '')
    this.combo();
  }

  getData(page: number, rows: number, sortfield: any, sortorder: any, search: any) {
    this.loading = true
    if (sortfield == undefined) sortfield = ''

    this.apiService.get('registrasi/get-pasien?' + this.pencarian + '&sortfield=' + sortfield + '&sortorder=' + sortorder + '&page=' + page + '&rows=' + rows).subscribe(table => {
      this.loading = true
      for (let i = 0; i < table.daftar.length; i++) {
        const element = table.daftar[i];
        element.no = i + 1;
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

  combo() {
    this.apiService.get("registrasi/get-combo-registrasi").subscribe(table => {
      var dataCombo = table;
      this.listJenisKelamin = dataCombo.jeniskelamin
    })
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
    debugger;
    this.selected = event.data
  }

  popUpTriasePasienBaru() {
    this.isSimpan = false;
    this.item.tglLahir = this.now;
    this.pop_DataPasien = true;
  }
  popUpTriasePasienBaru2(e) {
    this.isSimpan = false;
    this.item.tglLahir = this.now;
    this.pop_DataPasien = true;
  }

  tutupPasienTriase() {
    this.item.namaPasien = undefined;
    this.item.jenisKelamin = undefined;
    this.item.tempatLahir = undefined;
    this.item.Notelepon = undefined;
    this.item.Alamat = undefined;
    this.item.tglLahir = this.now;
    this.pop_DataPasien = false
  }

  simpanPasienTriase() {
    this.isSimpan = true
    if (this.item.namaPasien == undefined) {
      this.alertService.warn('Info', "Nama Pasien Belum Diisi")
      return
    }
    if (this.item.jenisKelamin == undefined) {
      this.alertService.warn('Info', "Jenis Kelamin Belum Diisi")
      return
    }
    if (this.item.tglLahir == undefined) {
      this.alertService.warn('Info', "Tanggal Lahir Belum Diisi")
      return
    }

    var objSave = {
      idpasien: "",
      namapasien: this.item.namaPasien,
      jeniskelamin: this.item.jenisKelamin.id,
      tgllahir: moment(this.item.tglLahir).format('YYYY-MM-DD HH:mm'),
      tempatlahir: this.item.tempatLahir != undefined ? this.item.tempatLahir : null,
      nohp: this.item.Notelepon != undefined ? this.item.Notelepon : null,
      alamat: this.item.Alamat != undefined ? this.item.Alamat : null,
    }

    this.apiService.post('registrasi/simpan-pasien-triase', objSave).subscribe(dataSave => {
      debugger;
      this.apiService.postLog('Simpan Pasien Baru Triase', 'id Triase Pasien', dataSave.data.id, 'Simpan Pasien Baru Triase dengan Nama Pasien '
        + dataSave.data.namapasien).subscribe(z => { })
      this.router.navigate(['rekam-medis-igd', dataSave.data.id, "pasienTriase"])
    })

  }

  inputTriase() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    this.router.navigate(['rekam-medis-igd', this.selected.nocm, "pasienTerdaftar"])
  }
  inputTriase2(e) {
   
    this.router.navigate(['rekam-medis-igd',e.nocm, "pasienTerdaftar"])
  }
}
