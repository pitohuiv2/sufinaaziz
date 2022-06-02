import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { Table } from 'primeng/table';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-bed-online',
  templateUrl: './bed-online.component.html',
  styleUrls: ['./bed-online.component.scss']
})
export class BedOnlineComponent implements OnInit {
  item: any = {}
  apiTimer: any;
  dataSource: any[] = []
  color = ['white', 'blue', 'gray', 'darkgray', 'orange']
  listKelas = []

  value = 10
  count_defaultTable: any = 15
  countTable: any = this.count_defaultTable
  totalPage: any = 3
  page_otomatis: any = 1
  page_default: any = 1
  infoPage: any
  timerTables: any
  start_otomatis: any = 0
  limit_otomatis: any = 10
  count_defaultPanel: any = 60
  countPanel = this.count_defaultPanel
  timerPanels:any
  numberss = Array(5).map((x, i) => i);
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.loadTable(this.start_otomatis, this.limit_otomatis)
    this.loadKelas()
    this.apiTimer = setInterval(() => {
      this.timerTable()
      this.timerPanel()
    }, (1000)); //1 second


  }
  timerPanel() {
    this.countPanel = this.countPanel - 1;
    if (this.countPanel <= 0) {
      this.countPanel = this.count_defaultPanel;
      this.loadKelas();

    }
    this.timerPanels = this.countPanel + " Detik"; // watch
  }

  timerTable() {
    this.countTable = this.countTable - 1;
    if (this.countTable <= 0) {
      this.countTable = this.count_defaultTable;

      // event Load Table

      // page selanjutnya
      this.page_otomatis = this.page_otomatis + 1;
      if (this.page_otomatis > this.totalPage) {
        this.page_otomatis = this.page_default;
      }

      this.start_otomatis = 0;
      this.limit_otomatis = 10;
      for (var j = 1; j <= this.totalPage; j++) {
        if (j == this.page_otomatis) {
          this.loadTable(this.start_otomatis, this.limit_otomatis);
        }

        this.start_otomatis = this.start_otomatis + 10;

      }



    }
    this.infoPage = this.page_otomatis + ' dari ' + this.totalPage + ' Halaman'; // info page
    this.timerTables = this.countTable + " Detik"; // watch
  }
  loadTable(start, limit_otomatis) {
    this.dataSource = []
    this.apiService.get('bed/get-ruangan?offset=' + start + '&limit=' + limit_otomatis).subscribe(e => {

      for (let x = 0; x < e.length; x++) {
        const element = e[x];
        element.no = start + x + 1
      }
      this.dataSource = e
    })
  }
  loadKelas() {
    this.listKelas = []
    this.apiService.get('bed/get-kelas').subscribe(e => {
      let z = 0
      for (let x = 0; x < e.length; x++) {
        const element = e[x];
        if (this.color[z] == undefined) z = 0
        element.sisa = parseFloat(element.kapasitas) - parseFloat(element.tersedia)
        element.persen = parseFloat(element.sisa) / parseFloat(element.kapasitas) * 100
        element.color = this.color[z]
        z = z + 1
      }
      this.listKelas = e
    })
  }

}
