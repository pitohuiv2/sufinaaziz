import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-form-rl-satu',
  templateUrl: './form-rl-satu.component.html',
  styleUrls: ['./form-rl-satu.component.scss'],

  providers: [ConfirmationService]
})
export class FormRlSatuComponent implements OnInit {
  item: any = {

  }
  listTahun: any = []
  dataSource: any[]
  dataSource2: any[]
  isSimpan: boolean = false
  indexTab = 0
  column: any = []
  column2: any = []
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private helper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,

  ) { }
  ngOnInit(): void {
    this.loadColumn()
    let tahun = new Date().getFullYear()
    for (let x = tahun; x < 10; x++) {
      this.listTahun.push({ name: x })

    }
  }
  handleChangeTab(e) {
    this.indexTab = e.index
  }
  loadColumn() {
    this.column = [
      { field: 'tahun', header: 'Tahun', width: "150px" },
      { field: 'BOR', header: 'BOR', width: "150px" },
      { field: 'LOS', header: 'LOS', width: "150px" },
      { field: 'BTO', header: 'BTO', width: "150px" },
      { field: 'TOI', header: 'TOI', width: "150px" },
      { field: 'NDR', header: 'NDR', width: "150px" },
      { field: 'GDR', header: 'GDR', width: "150px" },
      { field: 'ratarataperhari', header: 'Rata-rata Kunjungan/Hari', width: "180px" },
    ];
   
  }
  cari() {
    this.dataSource = []
    let tahun = ''
    if (this.item.tahun) {
      tahun = this.item.tahun
    }
    this.apiService.get('registrasi/laporan/get-laporan-rl12?tahun=' + tahun).subscribe(e => {
      this.dataSource = e.data
    })
  }
  cari2(){
    this.dataSource2 = []
    this.apiService.get('registrasi/laporan/get-laporan-rl13').subscribe(e => {
      this.dataSource2 = e.data
    })
  }
  exportExcel() {
    this.helper.exportExcel(this.dataSource, 'RL 1.2 Indikator Pelayanan RS')
  }
  exportExcel2() {
    this.helper.exportExcel(this.dataSource2, 'RL 1.3 Fasilitas Pelayanan')
  }

}

