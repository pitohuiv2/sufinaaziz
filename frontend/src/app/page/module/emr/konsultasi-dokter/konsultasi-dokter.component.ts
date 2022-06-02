
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
  selector: 'app-konsultasi-dokter',
  templateUrl: './konsultasi-dokter.component.html',
  styleUrls: ['./konsultasi-dokter.component.scss'],

})
export class KonsultasiDokterComponent implements OnInit {
  columnKonsul: any[] = []
  dataSourceKonsul: any[]
  item: any = {}
  pop_Konsul: boolean
  columnRiwayat: any[]
  product = {}
  listDokter: any[]
  listRuangan: any[] = []
  selectedData:any[]
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
      { field: 'tglorder', header: 'Tgl Order', width: "100px" },
      { field: 'ruanganasal', header: 'Ruangan Asal', width: "150px" },
      { field: 'ruangantujuan', header: 'Ruangan Tujuan', width: "150px" },
      { field: 'namalengkap', header: 'Dokter', width: "200px" },
      { field: 'keteranganorder', header: 'Keterangan', width: "150px" },
      { field: 'status', header: 'Verifikasi', width: "90px" },
    ];

  }
  loadCombo() {
    this.apiService.get("emr/get-ruangan-konsul"
    ).subscribe(re => {
      this.listRuangan = re;

    })
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
    delete this.item.norec
    delete this.item.ruanganTujuan
    delete this.item.keterangan
    delete this.item.dokter
    delete this.item.jawaban
    delete this.item.norecKonsul

  }
  save() {
    if (this.item.ruanganTujuan == undefined) {
      this.alertService.error("Info", "Pilih Ruangan Tujuan terlebih dahulu!")
      return
    }
    if (this.item.dokter == undefined) {
      this.alertService.error("Info", "Pilih Dokter terlebih dahulu!")
      return
    }
    var objSave = {
      norec_so: this.item.norecKonsul != undefined ? this.item.norecKonsul : '',
      norec_pd: this.item.norec_pd,
      pegawaifk: this.item.dokter.id,
      objectruanganasalfk: this.item.objectruanganfk,
      objectruangantujuanfk: this.item.ruanganTujuan.id,
      keterangan: this.item.keterangan != undefined ? this.item.keterangan : '',
      method: 'save'
    }
    this.apiService.post('emr/post-konsultasi', objSave).subscribe(e => {
      this.clear()
      this.loadGrid();
      this.apiService.postLog('Konsultasi', 'Norec strukorder_t', e.strukorder.norec, 'Order Konsul ke Ruangan ' + this.item.namaruangan
        + ' pada noregistrasi ' + this.item.noregistrasi).subscribe(e => { })
      this.pop_Konsul = false
    });
  }
  loadGrid() {
    var paramSearch = 'noregistrasi=' + this.item.noregistrasi
    this.apiService.get("emr/get-order-konsul?" + paramSearch).subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.statusorder == 1) {
          element.status = '✔'
        } else {
          element.status = '✘'
        }
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
    if (e.statusorder == 1) {
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
    this.item.ruanganAsal = e.ruanganasal
    this.item.ruanganTujuan = { id: e.objectruangantujuanfk, namaruangan: e.ruangantujuan }
    this.item.dokter = { id: e.pegawaifk, namalengkap: e.namalengkap }
    this.item.keterangan = e.keteranganorder
    this.pop_Konsul = true
  }
  hapusD(e) {
    if (e.statusorder == 1) {
      this.alertService.warn('Info', 'data sudah di verif tidak bisa di edit')
      return
    }
    var objSave = {
      norec_so: e.norec,
      method: 'delete'
    }
    this.apiService.post('emr/post-konsultasi', objSave).subscribe(e => {
      this.loadGrid();
      this.apiService.postLog('Konsultasi', 'Norec strukorder_t', this.item.norec, 'Hapus Konsul ke Ruangan ' + this.item.namaruangan
        + ' pada noregistrasi ' + this.item.noregistrasi).subscribe(z => { })
    })
  }
}
