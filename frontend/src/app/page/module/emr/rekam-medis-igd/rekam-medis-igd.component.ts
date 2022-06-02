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
  selector: 'app-rekam-medis-igd',
  templateUrl: './rekam-medis-igd.component.html',
  styleUrls: ['./rekam-medis-igd.component.scss'],
  providers: [ConfirmationService]
})
export class RekamMedisIgdComponent implements OnInit, AfterViewInit {
  idPasien: any
  ket: any
  numberss = Array(10).map((x, i) => i);
  isClosing: boolean
  header: any = {}
  isLoadingNav: boolean
  selectedFile:any[]
  searchText:string
  treeSourceMenu: TreeNode[] = []
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,
  ) { }

  ngAfterViewInit(): void {
    this.activeRoute.params.subscribe(params => {
      this.idPasien = params['norm'];
      this.ket = params['ket']
      this.loadHead()
    })

  }

  ngOnInit(): void {
    this.loadTreeView()
  }

  loadHead() {
    this.isClosing = false
    this.apiService.get("emr/get-pasien-triase?idPasien=" + this.idPasien + "&Keterangan=" + this.ket).subscribe(res => {      
      let e = res.result
      e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
      e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
      this.header = e;
      if (e.isclosing == true) {
        this.isClosing = true
      }
    })
  }
  loadTreeView() {
    this.treeSourceMenu = [];
    this.isLoadingNav = true
    this.apiService.get("emr/get-menu-rekam-medis-dynamic?namaemr=navigasiigd").subscribe(e => {
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
