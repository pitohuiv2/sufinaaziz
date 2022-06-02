import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-data-profil-rs',
  templateUrl: './data-profil-rs.component.html',
  styleUrls: ['./data-profil-rs.component.scss'],
  providers: [ConfirmationService]
})
export class DataProfilRsComponent implements OnInit {
  selected: any;
  dataSource: any[];
  item: any = {};
  dateNow: any;
  column: any[];
  dataLogin: any;
  kelUser: any;
  selectedGrid:any;
  pop_Profil:boolean = false;
  idProfil: any = "";
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
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.loadColumn();
    this.loadData();
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'namaexternal', header: 'Nama Eksternal', width: "250px" },
      { field: 'namalengkap', header: 'Nama Lengkap', width: "250px" },
      { field: 'alamatemail', header: 'Email', width: "180px" },
      { field: 'alamatlengkap', header: 'Alamat', width: "250px" },
      { field: 'kodepos', header: 'Kode Pos', width: "120px" },
      { field: 'faksimile', header: 'Faks', width: "100px" },
      { field: 'fixedphone', header: 'Telepon', width: "120px" },
      { field: 'website', header: 'Website', width: "120px" },
      { field: 'namapemerintahan', header: 'Pemerintahan', width: "150px" },
      { field: 'namakota', header: 'Kota', width: "120px" },        
    ];
  }

  loadData(){
    this.apiService.get('sysadmin/general/get-data-profil-rs').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataSource = e.data      
    })    
  }
  
  clearData(){
    this.idProfil = "";
    this.item.NamaExternal = undefined;
    this.item.NamaProfil = undefined;
    this.item.NamaEmail = undefined;
    this.item.Alamat = undefined;
    this.item.KodePos = undefined;
    this.item.Faks = undefined;
    this.item.Telepon = undefined;
    this.item.Website = undefined;
    this.item.Pemerintahan = undefined;
    this.item.Kota = undefined;
  }

  editProfil(e) {
    var dataItem = e
    this.idProfil = dataItem.id;
    this.item.NamaExternal = dataItem.namaexternal;
    this.item.NamaProfil = dataItem.namalengkap;
    this.item.NamaEmail = dataItem.alamatemail;
    this.item.Alamat = dataItem.alamatlengkap;
    this.item.KodePos = dataItem.kodepos;
    this.item.Faks = dataItem.faksimile;
    this.item.Telepon = dataItem.fixedphone;
    this.item.Website = dataItem.website;
    this.item.Pemerintahan = dataItem.namapemerintahan;
    this.item.Kota = dataItem.namakota;
    this.pop_Profil = true;
  }

  hapusProfil(e) {
    var objSave = {
      'id': e.id
    }
    this.apiService.post('sysadmin/general/delete-profil-rs', objSave).subscribe(e => {
      this.loadData()
    })
  }

  tambah() {
    this.pop_Profil = true
    this.clearData()
  }

  simpanProfil(){
    if (this.item.NamaProfil == undefined) {
      this.alertService.warn('Info', 'Nama Profil Belum Diisi')
      return
    }

    if (this.item.Alamat == undefined) {
      this.alertService.warn('Info', 'Alamat Profil Belum Diisi')
      return
    }

    let json = {
      'id': this.idProfil,
      'namaexternal': this.item.NamaExternal != undefined ? this.item.NamaExternal : null,
      'namalengkap': this.item.NamaProfil != undefined ? this.item.NamaProfil : null,
      'alamatemail': this.item.NamaEmail != undefined ? this.item.NamaEmail : null,
      'kodepos': this.item.KodePos != undefined ? this.item.KodePos : null,
      'alamatlengkap': this.item.Alamat != undefined ? this.item.Alamat : null,
      'faksimile': this.item.Faks != undefined ? this.item.Faks : null,
      'fixedphone': this.item.Telepon != undefined ? this.item.Telepon : null,
      'website': this.item.Website != undefined ? this.item.Website : null,
      'namapemerintahan': this.item.Pemerintahan != undefined ? this.item.Pemerintahan : null,
      'namakota': this.item.Kota != undefined ? this.item.Kota : null,
    }

    this.apiService.post('sysadmin/general/save-profil-rs', json).subscribe(e => {
      this.loadData();
      this.clearData();
      this.pop_Profil = false;
    })
  }

}
