import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-form-rl-tiga',
  templateUrl: './form-rl-tiga.component.html',
  styleUrls: ['./form-rl-tiga.component.scss']
})
export class FormRlTigaComponent implements OnInit {

  selected: any = '3.1 Kegiatan Pelayanan Rawat Inap'
  item: any = {
    tglAwal: new Date(),
    tglAkhir: new Date(),
  }
  listTahun: any = []
  dataSource: any[]
  dataSource2: any[]
  isSimpan: boolean = false
  indexTab = 0
  column: any = []
  listitems: MenuItem[]
  column2: any = []
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
    this.listitems = [
      {
        label: '3.1 Kegiatan Pelayanan Rawat Inap', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.2 Kegiatan Pelayanan Rawat Darurat', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.3 Kegiatan Kesehatan Gigi dan Mulut', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.4 Kegiatan Kebidanan', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.5 Kegiatan Perinatologi', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.6 Kegiatan Pembedahan', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.7 Kegiatan Radiologi', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.8 Pemeriksaan Laboratorium', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.9 Pelayanan Rehabilitasi Medik', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.10 Kegiatan Pelayanan Khusus', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.11 Kegiatan Kesehatan Jiwa', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.12 Kegiatan Keluarga Berencana', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.13 Pengadaaan Obat, Penulisan & Pelayanan Resep', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.14 Kegiatan Rujukan', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },
      {
        label: '3.15 Cara Bayar', icon: 'pi pi-fw pi-chevron-circle-right', command: (event) => {
          event.node = event.item
          this.nodeSelect(event);
        }
      },  
    ]
    this.loadColumn()

  }
  nodeSelect(event) {
    this.selected = event.node.label
  }
  handleChangeTab(e) {
    this.indexTab = e.index
  }
  loadColumn() {

  }

  cari2() {

  }

  exportExcel() {
    this.helper.exportExcel(this.dataSource, 'RL 3.1')
  }

}


