
import { RekamMedisIgdComponent } from '../rekam-medis-igd/rekam-medis-igd.component';
import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService, TreeNode } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

import { FilterPipe } from '../../../../pipe/filter.pipe';
import { Pipe, PipeTransform } from '@angular/core';
@Component({
  selector: 'app-emr-detail-igd',
  templateUrl: './emr-detail-igd.component.html',
  styleUrls: ['./emr-detail-igd.component.scss']
})
export class EmrDetailIgdComponent implements OnInit {
  indexTab: number
  item: any = {
    tglPelayanan: new Date(),
    layanan: []
  }
  
  selectedFile:any[]
  listProdukCek: any[] = []
  produkDef: any[] = []
  listRuanganTujuan: any[] = []
  skeleton: any = []
  searchText: string = "";
  selected_count: number = 0;
  selected_games: any[] = []
  listChecked = []
  isSimpan: any
  isRiwayat: boolean
  dataSourceRiwayat: any = []
  columnRiwayat: any[]
  selectedData: any
  columnPaket: any[]
  pop_paket: boolean
  dataSourcePaket: any[]
  columnRiwayatPengkajian: any[]
  dataSourcePengkajian: any[]
  isLoadingNav: boolean
  treeSourceMenu: TreeNode[] = []
  headData: any = {}
  nomorERM = '-'
  navDetailForm = 'emr-detail-form-igd'
  paramEMR: any = 'igd'
  defaultFormTujuan = 210079
  constructor(
    public rekamMedis: RekamMedisIgdComponent,
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,
  ) { }

  ngAfterViewInit() {

  }
  ngOnInit(): void {    
    if (this.rekamMedis.header.norec == undefined) {
      var cache = this.cacheHelper.get('cacheEMR_qwertyuiop')
      if (cache != undefined) {
        cache = JSON.parse(cache)        
        this.headData = cache
        if (cache.norec == undefined) {
          this.headData.norec = "";
        }
      }
    } else {
      this.headData = this.rekamMedis.header
    }
    if (this.headData.norec == undefined) {
      window.history.back()
    }
    this.item.idPegawaiLogin = this.authService.getDataLoginUser().pegawai.id

    this.columnRiwayatPengkajian = [
      { field: 'tglemr', header: 'Tgl EMR', width: "100px" },
      { field: 'noemr', header: 'No EMR', width: "100px" },
      { field: 'norm', header: 'No RM/Id Pasien Triase', width: "180px" },
      // { field: 'namaruangan', header: 'Ruangan', width: "150px" },
    ]
    this.loadRiwayatEMR()
  }


  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadRiwayatEMR()
      console.log(this.activeRoute)
      // this.router.navigate(['order-bedah'], { relativeTo: this.activeRoute });
    } else if (e.index == 1) {
      this.loadTreeView()
    } else {

    }
  }
  buatBaru() {

    this.indexTab = 1
    this.handleChangeTab({ index: this.indexTab })
    this.router.navigate([this.navDetailForm, this.defaultFormTujuan, '-'], { relativeTo: this.activeRoute });
    this.nomorERM = '-'
  }
  editD(e) {
    this.nomorERM = e.noemr
    this.indexTab = 1
    this.handleChangeTab({ index: this.indexTab })
    this.router.navigate([this.navDetailForm, this.defaultFormTujuan, e.noemr], { relativeTo: this.activeRoute });
  }
  hapusPengkajian(e) {
    this.nomorERM = '-'
    this.apiService.post('emr/hapus-emr-transaksi-norec', { norec: e.norec }).subscribe(e => {
      this.loadRiwayatEMR()
      this.apiService.postLog('Hapus EMR', 'norec emrpasien_t', e.norec,
        'Hapus No EMR - ' + e.emrpasienfk + ' pada No Registrasi  '
        + this.headData.noregistrasi + ' - Pasien : ' + this.headData.namapasien).subscribe(res => {
        })
    })
  }
  asupKaForm(e) {
    this.nomorERM = e.emrpasienfk
    this.indexTab = 1
    this.handleChangeTab({ index: this.indexTab })
    this.router.navigate([this.navDetailForm, e.emrfk, e.emrpasienfk], { relativeTo: this.activeRoute });
  }
  hapusForm(e) {
    var json = {
      'noemr': e.noemr,
      'reportdisplay': e.reportdisplay,
      'idemr': e.emrfk,
      'norec': e.norec,
      'idpegawai': this.authService.getPegawaiId(),
    }
    this.apiService.post('emr/disable-emr-details', json).subscribe(z => {
      this.apiService.postLog('Hapus EMR', 'norec emrpasien_t', e.norec,
        'Hapus Satu Form EMR ( ' + e.namaform + ' ) No EMR - ' + e.noemr + ' pada No Registrasi  '
        + this.headData.noregistrasi + ' - Pasien : ' + this.headData.namapasien).subscribe(res => {
        })
    })
  }
  loadTreeView() {
    this.treeSourceMenu = [];
    this.isLoadingNav = true
    this.apiService.get("emr/get-menu-rekam-medis-dynamic?namaemr=" + this.paramEMR).subscribe(e => {
      this.isLoadingNav = false
      this.treeSourceMenu = e.data
    })
  }
  nodeSelect(event) {
    // debugger
    // this.cacheHelper.set('cacheEMR_qwertyuiop', undefined)

    let idTree = event.node.id
    let urlTrue = event.node.reportdisplay
    if (urlTrue == null) {
      this.router.navigate([this.navDetailForm, idTree, this.nomorERM], { relativeTo: this.activeRoute });
    } else {
      this.router.navigate([urlTrue], { relativeTo: this.activeRoute });
    }
  }
  loadRiwayatEMR() {    
    var paramSearch = 'nocm=' + this.headData.nocm
    this.apiService.get("emr/get-emr-transaksi-detail-form?" + paramSearch + "&jenisEmr=" + this.paramEMR).subscribe(dat => {
      this.dataSourcePengkajian = dat.data
    });
  }

}
