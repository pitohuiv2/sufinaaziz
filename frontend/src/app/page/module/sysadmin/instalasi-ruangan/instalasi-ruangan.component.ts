import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-instalasi-ruangan',
  templateUrl: './instalasi-ruangan.component.html',
  styleUrls: ['./instalasi-ruangan.component.scss'],
  providers: [ConfirmationService]
})
export class InstalasiRuanganComponent implements OnInit {

   item: any = {
    mapModul: [],
    mapRuangan: [],
    mapModul2: []
  }
  column: any[]
  dataSource: any[]
  pop_Instalasi: boolean = false
  listStatus: any[] = [];
  // item: any = {};
  d_Profile: any[]
  d_KelompokUser: any[]
  d_Pegawai: any[]
  d_LoginUser: any[]
  isSimpan: boolean = false
  indexTab = 0
  searchText = '';
  searchText2 = '';
  listModul: any[] = [];
  listRuangan: any[] = []
  ruanganDef: any[] = [];
  modulDef: any[] = [];
  selectedGrid:any
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
    this.loadCombo()
    this.loadData()

  }
  loadCombo() {
    this.listStatus = [{ id: 1, stt: 't', status: 'Aktif' }, { id: 2, stt: 'f', status: 'Tidak Aktif' }]
    this.item.status = this.listStatus[0];
    // this.apiService.get('sysadmin/general/get-combo-user').subscribe(e => {
      // this.d_KelompokUser = e.kelompokuser
         // })
  }
  loadData() {
    this.apiService.get('sysadmin/general/get-daftar-instalasi').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataSource = e.data
      // this.d_LoginUser = e.data
    })

    this.apiService.get("sysadmin/general/get-daftar-ruangan").subscribe(table => {
      // var dataCombo = table;
      // this.listDetailJenisProduk = dataCombo.detailjenisproduk;
    });
  }
  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'aktif', header: 'Aktif', width: "100px" },
      { field: 'id', header: 'Kode Instalasi', width: "150px" },
      { field: 'namadepartemen', header: 'Instalasi', width: "250px" },
      { field: 'kodeexternal', header: 'Kode External', width: "150px" },
      { field: 'namaexternal', header: 'Nama External', width: "250px" },
      // { field: 'kelompokuser', header: 'Keterangan', width: "350px" },
    ];
  }
  editInstalasi(e) {
    var dataItem = e
      for (var i = 0; i < this.listStatus.length; i++) {
      const element = this.listStatus[i]
      if(element.stt == dataItem.statusna){ //status nu digrid
          this.item.status =  element
      }
    }
    this.item.idinstalasi = dataItem.id
    this.item.namainstalasi = dataItem.namadepartemen
    this.item.namaexternal = dataItem.namaexternal
    this.item.kodeexternal = dataItem.kodeexternal
    // this.item.status = [{ id: 1, stt: 't', status: 'Aktif' }, { id: 2, stt: 'f', status: 'Tidak Aktif' }]
    // this.item.pegawai = { id: dataItem.pegawaifk, namalengkap: dataItem.namalengkap }
    this.pop_Instalasi = true
  }
  hapusInstalasi(e) {
    var objSave = {
      'id': e.id
    }
    this.apiService.post('sysadmin/general/delete-new-instalasi', objSave).subscribe(e => {
      this.loadData()
    })
  }

  tambahIns() {
    this.pop_Instalasi = true
    this.clearInstalasi()
  }
  simpanInstalasi() {
    if (this.item.namainstalasi == undefined) {
      this.alertService.error('Info', 'Instalsi Harus di isi')
      return
    }
      var objSave = {
      //** Data Pelayanan */
        "id": this.item.id != undefined ? this.item.id : '-',
        "aktif": this.item.status != undefined ? this.item.status.stt : 't',
        "namadepartemen": this.item.namainstalasi != undefined ? this.item.namainstalasi : null,
        "namaexternal": this.item.namaexternal != undefined ? this.item.namaexternal : null,
        "kodeexternal":this.item.kodeexternal != undefined ? this.item.kodeexternal : null,        
                                            
    }
    
    this.isSimpan = true    
    this.apiService.post('sysadmin/general/save-new-instalasi', objSave).subscribe(e => {
    this.isSimpan = false
    this.pop_Instalasi = false
    this.loadData()
    this.clearInstalasi()

    })
  }
  clearInstalasi() {
    delete this.item.idinstalasi
    delete this.item.namainstalasi
    delete this.item.namaexternal
    delete this.item.kodeexternal
    delete this.item.status
    // delete this.item.kataKunciConfirm
  }
  filterPegawai(event) {
    let query = event.query;
    this.apiService.get("sysadmin/general/get-pegawai-part?namalengkap=" + query
    ).subscribe(re => {
      this.d_Pegawai = re;
    })
  }

  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadData()
    } else {
      this.loadData()
    }

  }
  clearFilter() {
    this.searchText = "";
  }
  clearFilter2() {
    this.searchText2 = "";
  }
  // saveMapRuangan() {
  //   if (this.item.loginUser == undefined) {
  //     this.alertService.warn('Info', 'Login User belum dipilih')
  //     return
  //   }
  //   if (this.item.mapRuangan.length == 0) {
  //     this.alertService.warn('Info', 'Belum ada ruangan yang dipilih')
  //     return
  //   }
  //   var arrobj = Object.keys(this.item.mapRuangan)
  //   var arraySave = []
  //   for (var i = arrobj.length - 1; i >= 0; i--) {
  //     if (this.item.mapRuangan[arrobj[i]] == true) {
  //       arraySave.push({
  //         "ruanganid": parseInt(arrobj[i]),
  //       })
  //     }
  //   }
  //   let json = {
  //     'loginid': this.item.loginUser.id,
  //     'ruangan': arraySave
  //   }
  //   this.apiService.post('sysadmin/general/save-map-login-to-ruangan', json).subscribe(e => {

  //   })
  // }
  // changeUser(e) {
  //   this.item.mapModul = []
  //   this.item.mapRuangan = []
  //   if (e.value == null) return
  //   this.apiService.get('sysadmin/general/get-map-loginruangan?loginuseridfk=' + e.value.id).subscribe(res => {
  //     this.item.mapRuangan = []
  //     this.alertService.info('Info',res.length +' Ruangan Terpilih')
  //     for (let i = 0; i < res.length; i++) {
  //       const element = res[i];
  //       this.item.mapRuangan[element.ruanganidfk] = true
  //     }
  //   })
  //   this.apiService.get('sysadmin/general/get-map-loginmodul?loginuseridfk=' + e.value.id).subscribe(es => {
  //     this.item.mapModul = []
  //     this.alertService.info('Info',es.length +' Modul Terpilih')
  //     for (let i = 0; i < es.length; i++) {
  //       const element = es[i];
  //       element.idCheck = parseFloat(element.objectmodulaplikasifk + '.10')
  //       this.item.mapModul[element.idCheck] = true
  //     }
  //   })
  // }
  // saveModul() {
  //   if (this.item.loginUser == undefined) {
  //     this.alertService.warn('Info', 'Login User belum dipilih')
  //     return
  //   }
  
  //   var arrobj = Object.keys(this.item.mapModul)
  //   var arraySave = []
  //   for (var i = arrobj.length - 1; i >= 0; i--) {
  //     if (this.item.mapModul[arrobj[i]] == true) {
  //       let idna = arrobj[i].split('.')
  //       arraySave.push({
  //         "objectmodulaplikasifk": parseInt(idna[0]),
  //       })
  //     }
  //   }
  //   if (arraySave.length == 0) {
  //     this.alertService.warn('Info', 'Belum ada Modul yang dipilih')
  //     return
  //   }
  //   let json = {
  //     'objectloginuserfk': this.item.loginUser.id,
  //     'details': arraySave
  //   }
  //   this.apiService.post('sysadmin/general/save-map-login-to-modul', json).subscribe(e => {

  //   })
  // }

  // simpanRuangan() {
  //   if (this.item.namainstalasi == undefined) {
  //     this.alertService.error('Info', 'Instalsi Harus di isi')
  //     return
  //   }
  //     var objSave = {
  //     //** Data Pelayanan */
  //       "id": this.item.id != undefined ? this.item.id : '-',
  //       "aktif": this.item.status != undefined ? this.item.status.stt : 't',
  //       "namadepartemen": this.item.namainstalasi != undefined ? this.item.namainstalasi : null,
  //       "namaexternal": this.item.namaexternal != undefined ? this.item.namaexternal : null,
  //       "kodeexternal":this.item.kodeexternal != undefined ? this.item.kodeexternal : null,        
                                            
  //   }
    
  //   this.isSimpan = true    
  //   this.apiService.post('sysadmin/general/save-new-ruangan', objSave).subscribe(e => {
  //   this.isSimpan = false
  //   this.pop_Instalasi = false
  //   this.loadData()
  //   this.clearInstalasi()

  //   })
  // }

  // clearRuangan() {
  //   delete this.item.idinstalasi
  //   delete this.item.namainstalasi
  //   delete this.item.namaexternal
  //   delete this.item.kodeexternal
  //   delete this.item.status
  //   // delete this.item.kataKunciConfirm
  // }

  // hapusRuangan(e) {
  //   var objSave = {
  //     'id': e.id
  //   }
  //   this.apiService.post('sysadmin/general/delete-new-ruangan', objSave).subscribe(e => {
  //     this.loadData()
  //   })
  // }

}
