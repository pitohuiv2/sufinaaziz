
import { RekamMedisComponent } from '../rekam-medis/rekam-medis.component';
import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService, TreeNode } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-cppt',
  templateUrl: './cppt.component.html',
  styleUrls: ['./cppt.component.scss']
})
export class CpptComponent implements OnInit {

  columnKonsul: any[] = []
  dataSourceKonsul: any[]
  item: any = {}
  pop_Konsul: boolean
  columnRiwayat: any[]
  product = {}
  selectedData:any[]
  listDokter: any[]
  listRuangan: any[] = []
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
    if (cache != undefined) {
      cache = JSON.parse(cache)
      this.item = cache

      this.item.ruanganAsal = this.item.namaruangan
      this.loadGrid()
      this.loadCombo()
    } else {
      window.history.back()
    }
    this.columnKonsul = [
      { field: 'no', header: 'No', width: "50px" },
      { field: 'tglinput', header: 'Tanggal', width: "100px" },
      { field: 'namalengkap', header: 'Pegawai', width: "200px" },
      { field: 'namaruangan', header: 'Ruangan', width: "200px" },
      { field: 'noregistrasi', header: 'No Registrasi', width: "100px" },
      // { field: 'soap', header: 'SOAP', width: "200px" },
    ];

  }
  loadCombo() {
    // this.apiService.get("emr/get-ruangan-konsul"
    // ).subscribe(re => {
    //   this.listRuangan = re;

    // })
  }
  openNew() {
    this.clear()
    this.pop_Konsul = true
  }
  hideDialog() {
    this.clear()
    this.pop_Konsul = false
  }
  clear() {
    delete this.item.s
    delete this.item.o
    delete this.item.a
    delete this.item.p
    delete this.item.norecKonsul
  }
  save() {
    // if (this.item.ruanganTujuan == undefined) {
    //   this.alertService.error("Info", "Pilih Ruangan Tujuan terlebih dahulu!")
    //   return
    // }
    // if (this.item.dokter == undefined) {
    //   this.alertService.error("Info", "Pilih Dokter terlebih dahulu!")
    //   return
    // }
    var objSave = {
      norec: this.item.norecKonsul != undefined ? this.item.norecKonsul : '',
      daftarpasienruanganfk: this.item.norec,
      s: this.item.s,
      o: this.item.o,
      a: this.item.a,
      p: this.item.p,
      method: 'save'
    }
    this.apiService.post('emr/save-soap', objSave).subscribe(e => {
      this.clear()
      this.loadGrid();

      this.pop_Konsul = false
    });
  }
  loadGrid() {
    var paramSearch = 'noregistrasi=' + this.item.noregistrasi
    this.apiService.get("emr/get-soap?" + paramSearch).subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        // element.details = []
        // if (element.statusorder == 1) {
        //   element.status = '✔'
        // } else {
        //   element.status = '✘'
        // }
      }
      this.dataSourceKonsul = e.data
    })
  }
  filterDokter(event) {
    let query = event.query;
    this.apiService.get("registrasi/get-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listDokter = re;
    })
  }
  edit(e) {
    if (e.isverifikasi == 1) {
      this.alertService.warn('Info', 'data sudah di verif tidak bisa di edit')
      return
    }
    var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
    var dateNow = new Date();
    var dateOrder = new Date(e.tglorder);
    var diffDays = Math.round(Math.abs((dateNow.getTime() - dateOrder.getTime()) / (oneDay)))
    if (diffDays >= 1) {
      this.alertService.warn('Info', 'data tidak bisa di edit')
      return
    }
    this.item.norecKonsul = e.norec
    this.item.s = e.s
    this.item.a = e.a
    this.item.o = e.o
    this.item.p = e.p
    this.pop_Konsul = true
  }
  hapusD(e) {
    if (e.statusorder == 1) {
      this.alertService.warn('Info', 'data sudah di verif tidak bisa di edit')
      return
    }
    var objSave = {
      norec: e.norec,
      method: 'delete'
    }
    this.apiService.post('emr/save-soap', objSave).subscribe(e => {
      this.loadGrid();

    })
  }
}
