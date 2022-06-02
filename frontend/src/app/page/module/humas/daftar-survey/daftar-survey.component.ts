import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-daftar-survey',
  templateUrl: './daftar-survey.component.html',
  styleUrls: ['./daftar-survey.component.scss']
})
export class DaftarSurveyComponent implements OnInit {
  item: any = { tglAwal: new Date(), tglAkhir: new Date() }
  columns: any = []
  dataSource: any = []
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private cacheHelper: CacheService,
    private helper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,

  ) { }

  ngOnInit(): void {
    this.columns = [
      { field: 'tglkeluhan', header: 'Tgl Keluhan', width: "100px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'umur', header: 'Umur', width: "100px" },
      { field: 'notlp', header: 'No HP', width: "100px" },
      { field: 'namapengisi', header: 'Nama Pengisi', width: "250px" },
      { field: 'namaruangan', header: 'Ruangan', width: "200px" },
      { field: 'keluhan', header: 'Keluhan', width: "300px" },
      { field: 'saran', header: 'Saran', width: "300px" },

    ];
    this.cari()
  }
  cari() {
    this.dataSource = []
    let nama = ''
    if (this.item.nama) {
      nama = this.item.nama
    }
    this.apiService.get('humas/get-survey?dari=' + moment(this.item.tglAwal).format('YYYY-MM-DD')
      + '&sampai=' + moment(this.item.tglAkhir).format('YYYY-MM-DD') + '&nama=' + nama).subscribe(e => {
        for (let x = 0; x < e.data.length; x++) {
          const element = e.data[x];
          element.tglkeluhan = moment(new Date(element.tglkeluhan)).format('YYYY-MM-DD')
        }
        this.dataSource = e.data
      })
  }
  exportExcel() {
    this.helper.exportExcel(this.dataSource, 'Daftar Survey Kepuasan')
  }

}
