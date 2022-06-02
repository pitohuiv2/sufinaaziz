import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-rs-online',
  templateUrl: './rs-online.component.html',
  styleUrls: ['./rs-online.component.scss'],
  providers: [ConfirmationService]
})
export class RsOnlineComponent implements OnInit {
  item: any = {
    tgl1:new Date(),
    tgl2:new Date(),
    tgl3:new Date(),
    tgl4:new Date(),
    tgl5:new Date(),
  }
 
  dataSource: any[]
  dataSource2: any[]
  dataSource3: any[]
  dataSource4: any[]
  dataSource5: any[]
  dataSource6: any[]
  isSimpan: boolean = false
  indexTab = 0

  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,

  ) { }
  ngOnInit(): void {
    this.loadColumn()
  }
  handleChangeTab(e) {
    this.indexTab = e.index
  }
  loadColumn() {

  }
  cari() {
    this.dataSource = []
    this.apiService.postNonMessage('LapV2/PasienMasuk/get', {}).subscribe(e => {
      this.dataSource = e.RekapPasienMasuk
    })
  }
  hapusPasienMasuk(e){

  }
 
  udpatePasienMasuk(){

  }
  cari2() {
    this.dataSource2 = []
    this.apiService.postNonMessage('LapV2/PasienDirawatKomorbid/get', {}).subscribe(e => {
      this.dataSource2 = e.RekapPasienDirawatKomorbid
    })
  }
  hapusKomorbid(e){

  }
  updateKomorbid(){

  }
  cari3() {
    this.dataSource3 = []
    this.apiService.postNonMessage('LapV2/PasienDirawatTanpaKomorbid/get', {}).subscribe(e => {
      this.dataSource3 = e.RekapPasienDirawatTanpaKomorbid
    })
  }
  hapusKomorbid2(e){

  }
  updateKomorbid2(){

  }
  updateKeluar(){

  }
  hapusKeluar(e){

  }
  cariKeluar(){
    this.dataSource4 = []
    this.apiService.postNonMessage('LapV2/PasienKeluar/get', {}).subscribe(e => {
      this.dataSource4 = e.RekapPasienKeluar
    })
  }
  cariTT(){
    this.dataSource5 = []
    this.apiService.postNonMessage('Fasyankes/get', {}).subscribe(e => {
      this.dataSource5 = e.fasyankes
    })
  }
  cariSDM(){
    this.dataSource6 = []
    this.apiService.postNonMessage('Fasyankes/sdm/get', {}).subscribe(e => {
      this.dataSource6 = e.sdm
    })
  }
  updateTT(){

  }
  updaetSDM(){

  }
  hapusTT(e){

  }
  hapsuSDN(e){
    
  }
}

