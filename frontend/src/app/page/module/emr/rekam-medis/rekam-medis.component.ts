import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService, TreeNode } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Menubar } from 'primeng/menubar';
@Component({
  selector: 'app-rekam-medis',
  templateUrl: './rekam-medis.component.html',
  styleUrls: ['./rekam-medis.component.scss'],
  providers: [ConfirmationService]
})
export class RekamMedisComponent implements OnInit, AfterViewInit {
  norecRP: any
  norecDPR: any
  isClosing: boolean
  header: any = {}
  isLoadingNav: boolean
  selectedFile: any
  searchText: any
  numberss = Array(13).map((x, i) => i);
  treeSourceMenu: MenuItem[] = []
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,
  ) {

  }
  ngAfterViewInit(): void {
    this.activeRoute.params.subscribe(params => {
      this.norecRP = params['norec_rp'];
      this.norecDPR = params['norec_dpr']
      this.loadHead()
    })

  }
  ngOnInit(): void {
    this.loadTreeView()
  }
  loadHead() {
    this.isClosing = false
    this.apiService.get("emr/get-antrian-pasien-norec/" + this.norecDPR).subscribe(res => {
      let e = res.result
      e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
      e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
      this.header = e;
      if (e.isclosing == true) {
        this.isClosing = true
      }

      // this.apiService.get("sysadmin/general/get-status-close/" + this.header.noregistrasi).subscribe(rese => {
      //   if (rese.status == true) {
      //     this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(rese.tglclosing)).format('DD-MMM-YYYY HH:mm'))
      //     this.isClosing = true
      //   }
      // })
    })
  }
  loadTreeView() {
    this.treeSourceMenu = [];
    this.isLoadingNav = true
    this.apiService.get("emr/get-menu-rekam-medis-dynamic?namaemr=navigasi").subscribe(e => {
      this.isLoadingNav = false

      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.name = element.label
        element.command = (event) => {
          event.node = event.item
          this.nodeSelect(event);
        };
        element.icon = 'pi pi-fw pi-sliders-h'
      }
      this.treeSourceMenu = e.data
    })
  }
  nodeSelect(event) {
    this.cacheHelper.set('cacheEMR_qwertyuiop', undefined)
    let noemr = '-'
    let idTree = event.node.id
    let urlTrue = event.node.reportdisplay
    if (urlTrue == null) {
      this.cacheHelper.set('cacheEMR_qwertyuiop', JSON.stringify(this.header))
      this.router.navigate(["order-lab", idTree, noemr]);
    } else {
      this.cacheHelper.set('cacheEMR_qwertyuiop', JSON.stringify(this.header))
      this.router.navigate([urlTrue], { relativeTo: this.activeRoute });
      // this.router.navigate([urlTrue, this.norecRP, this.norecDPR]);
    }
  }
}
