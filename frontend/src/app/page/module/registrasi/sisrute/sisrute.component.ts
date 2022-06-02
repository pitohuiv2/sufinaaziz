import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-sisrute',
  templateUrl: './sisrute.component.html',
  styleUrls: ['./sisrute.component.scss']
})
export class SisruteComponent implements OnInit {
  dataSource: any = []
  dataSource2: any = []
  listNum: any = [{ id: 'N', name: 'Numerator' }, { id: 'D', name: 'Denumerator' }]
  item: any = {
    tglAwal: new Date(),
    tglAkhir: new Date(),
    tglLap: new Date()
  }
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private helper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,

  ) { }


  ngOnInit(): void {
    this.loadData()
  }
  exportExcel() {

  }
  loadData() {
    var tglRujukan = ""
    if (this.item.tglRujukan != undefined) {
      tglRujukan = moment(this.item.tglRujukan).format('YYYY-MM-DD')
    }

    var noRujukan = "";
    if (this.item.noRujukan != undefined) {
      noRujukan = this.item.noRujukan
    }
    var create = "";
    if (this.item.create == true) {
      create = this.item.create
    }
    this.apiService.get("bridging/sisrute/rujukan/get?"
      // + "create=" + create
      + "nomor=" + noRujukan
      + "&tanggal=" + tglRujukan
    ).subscribe(dat => {
      if (dat.total != undefined && dat.total > 0) {
        // this.totalRespon = 0
        // this.totalUnRespon = 0
      this.alertService.info('Success',dat.total + ' ' + dat.detail)
        var datas = dat.data;
        for (let i = 0; i < datas.length; i++) {
          datas[i].no = i + 1
          datas[i].nocm = datas[i].PASIEN.NORM
          datas[i].namapasien = datas[i].PASIEN.NAMA
          datas[i].norujukan = datas[i].RUJUKAN.NOMOR
          datas[i].tglrujukan = datas[i].RUJUKAN.TANGGAL
          datas[i].faskesasal = datas[i].RUJUKAN.FASKES_ASAL.NAMA
          datas[i].faskestujuan = datas[i].RUJUKAN.FASKES_TUJUAN.NAMA
          datas[i].status = datas[i].RUJUKAN.STATUS.NAMA

          if (datas[i].RUJUKAN.STATUS.NAMA == 'Sudah direspon')
            // this.totalRespon = this.totalRespon + 1
            if (datas[i].RUJUKAN.STATUS.NAMA == 'Belum direspon')
              // this.totalUnRespon = this.totalUnRespon + 1
              var kontak = ''
          if (datas[i].PASIEN.KONTAK != null && datas[i].PASIEN.KONTAK != ' ')
            kontak = ' / ' + datas[i].PASIEN.KONTAK
          if (datas[i].PASIEN.JENIS_KELAMIN != null) {
            datas[i].namakontak = datas[i].PASIEN.NAMA
              + kontak
              + ' / ' + datas[i].PASIEN.JENIS_KELAMIN.NAMA
              + ' / ' + datas[i].PASIEN.TANGGAL_LAHIR
          } else {
            datas[i].namakontak = datas[i].PASIEN.NAMA
              + kontak
              + ' / ' + datas[i].PASIEN.TANGGAL_LAHIR
          }

          datas[i].tglasal = datas[i].RUJUKAN.TANGGAL + ' / ' + datas[i].RUJUKAN.FASKES_ASAL.NAMA
          if (datas[i].RUJUKAN.DIAGNOSA != null)
            datas[i].diagnosa = datas[i].RUJUKAN.DIAGNOSA.NAMA
          else
            datas[i].diagnosa = '-'
          datas[i].alasan = datas[i].RUJUKAN.ALASAN.NAMA + ' / ' + datas[i].RUJUKAN.ALASAN_LAINNYA
        }
      }
      else if (dat.detail != undefined) {
        this.alertService.error( 'Info',dat.detail)
      } else {
        this.alertService.error('Info',dat.metaData.message)
      }
      this.dataSource = datas
    })
  }
  cari2() {
    this.loadData2()
  }
  loadData2() {
    var tglRujukan = ""
    if (this.item.tglRujukan != undefined) {
      tglRujukan = moment(this.item.tglRujukan).format('YYYY-MM-DD')
    }

    var noRujukan = "";
    if (this.item.noRujukan != undefined) {
      noRujukan = this.item.noRujukan
    }
    var create = "";
    if (this.item.create == true) {
      create = this.item.create
    }
    this.apiService.get("bridging/sisrute/rujukan/get?"
      + "create=" + create
      + "nomor=" + noRujukan
      + "&tanggal=" + tglRujukan
    ).subscribe(dat => {
      if (dat.total != undefined && dat.total > 0) {
        this.alertService.info('Info', dat.total + ' ' + dat.detail)
        var datas = dat.data;
        for (let i = 0; i < datas.length; i++) {
          datas[i].no = i + 1
          datas[i].nocm = datas[i].PASIEN.NORM
          datas[i].namapasien = datas[i].PASIEN.NAMA
          datas[i].norujukan = datas[i].RUJUKAN.NOMOR
          datas[i].tglrujukan = datas[i].RUJUKAN.TANGGAL
          datas[i].faskesasal = datas[i].RUJUKAN.FASKES_ASAL.NAMA
          datas[i].faskestujuan = datas[i].RUJUKAN.FASKES_TUJUAN.NAMA
          datas[i].status = datas[i].RUJUKAN.STATUS.NAMA
          var kontak = ''
          if (datas[i].PASIEN.KONTAK != null && datas[i].PASIEN.KONTAK != ' ')
            kontak = ' / ' + datas[i].PASIEN.KONTAK
          if (datas[i].PASIEN.JENIS_KELAMIN != null) {
            datas[i].namakontak = datas[i].PASIEN.NAMA
              + kontak
              + ' / ' + datas[i].PASIEN.JENIS_KELAMIN.NAMA
              + ' / ' + datas[i].PASIEN.TANGGAL_LAHIR
          } else {
            datas[i].namakontak = datas[i].PASIEN.NAMA
              + kontak
              + ' / ' + datas[i].PASIEN.TANGGAL_LAHIR
          }

          datas[i].tglasal = datas[i].RUJUKAN.TANGGAL + ' / ' + datas[i].RUJUKAN.FASKES_TUJUAN.NAMA
          if (datas[i].RUJUKAN.DIAGNOSA != null)
            datas[i].diagnosa = datas[i].RUJUKAN.DIAGNOSA.NAMA
          else
            datas[i].diagnosa = '-'
          datas[i].alasan = datas[i].RUJUKAN.ALASAN.NAMA + ' / ' + datas[i].RUJUKAN.ALASAN_LAINNYA
          datas[i].infobalik = ''
        }
      }
      else if (dat.detail != undefined) {
        this.alertService.error('Info', dat.detail)
      } else {
        this.alertService.error('Info', dat.metaData.message)
      }


      this.dataSource2 = datas
    })
  }
  cari() {
    this.loadData()
  }
  exportExcel2() {

  }
}
