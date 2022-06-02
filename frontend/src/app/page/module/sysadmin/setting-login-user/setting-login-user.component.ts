import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-setting-login-user',
  templateUrl: './setting-login-user.component.html',
  styleUrls: ['./setting-login-user.component.scss'],
  providers: [ConfirmationService]
})
export class SettingLoginUserComponent implements OnInit {
  item: any = {
    mapModul: [],
    mapRuangan: [],
    mapModul2: []
  }
  column: any[]
  dataSource: any[]
  pop_User: boolean = false
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
    this.apiService.get('sysadmin/general/get-combo-user').subscribe(e => {
      this.d_KelompokUser = e.kelompokuser
      this.d_Profile = e.profile
      for (let i = 0; i < e.modulaplikasi.length; i++) {
        const element = e.modulaplikasi[i];
        element.name = element.modulaplikasi
        element.idCheck = parseFloat(element.id + '.10')

        element.namemodul = 'Modul - ' + element.modulaplikasi
      }
      for (let x = 0; x < e.ruangan.length; x++) {
        const element2 = e.ruangan[x];
        element2.name = element2.namaruangan
      }
      this.listRuangan = e.ruangan
      this.listModul = e.modulaplikasi
      this.item.profile = e.profile[0]
    })
  }
  loadData() {
    this.apiService.get('sysadmin/general/get-daftar-user').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataSource = e.data
      this.d_LoginUser = e.data
    })
  }
  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namauser', header: 'Nama User', width: "200px" },
      { field: 'namalengkap', header: 'Pegawai', width: "250px" },
      { field: 'kelompokuser', header: 'Kelompok User', width: "150px" },
    ];
  }
  editUser(e) {
    var dataItem = e
    this.item.idlogin = dataItem.id
    this.item.namaUser = dataItem.namauser
    this.item.kelompokUser = { id: dataItem.objectkelompokuserfk, kelompokuser: dataItem.kelompokuser }
    this.item.pegawai = { id: dataItem.objectpegawaifk, namalengkap: dataItem.namalengkap }
    this.pop_User = true
  }
  hapusUser(e) {
    var objSave = {
      'id': e.id
    }
    this.apiService.post('sysadmin/general/delete-new-user', objSave).subscribe(e => {
      this.loadData()
    })
  }
  tambah() {
    this.pop_User = true
    this.clearUser()
  }
  simpanUser() {
    if (this.item.namaUser == undefined) {
      this.alertService.error('Info', 'Nama User Harus di isi')
      return
    }
    if (this.item.kataKunciPass == undefined) {
      this.alertService.error('Info', 'Kata Sandi Harus di isi')
      return
    }
    if (this.item.kataKunciPass != this.item.kataKunciConfirm) {
      this.alertService.error('Info', 'Kata Sandi tidak sama')
      return
    }
    if (this.item.kelompokUser == undefined) {
      this.alertService.error('Info', 'Kelompok User Harus di isi')
      return
    }
    if (this.item.pegawai == undefined) {
      this.alertService.error('Info', 'Pegawai Harus di isi')
      return
    }
    if (this.item.profile == undefined) {
      this.alertService.error('Info', 'Profile User Harus di isi')
      return
    }
    var objSave = {
      'id': this.item.idlogin != undefined ? this.item.idlogin : '',
      'katasandi': this.item.kataKunciPass,
      'namauser': this.item.namaUser,
      'objectkelompokuserfk': this.item.kelompokUser.id,
      'objectpegawaifk': this.item.pegawai.id,
      'waktuberakhir': null,
      'kdprofile': this.item.profile.id
    }
    // this.isSimpan = true
    this.apiService.post('sysadmin/general/save-new-user', objSave).subscribe(e => {
      // this.isSimpan = false
      this.pop_User = false
      this.loadData()
      this.clearUser()

    })
  }
  clearUser() {
    delete this.item.idlogin
    delete this.item.namaUser
    delete this.item.kelompokUser
    delete this.item.pegawai
    delete this.item.kataKunciPass
    delete this.item.kataKunciConfirm
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
  saveMapRuangan() {
    if (this.item.loginUser == undefined) {
      this.alertService.warn('Info', 'Login User belum dipilih')
      return
    }
    if (this.item.mapRuangan.length == 0) {
      this.alertService.warn('Info', 'Belum ada ruangan yang dipilih')
      return
    }
    var arrobj = Object.keys(this.item.mapRuangan)
    var arraySave = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      if (this.item.mapRuangan[arrobj[i]] == true) {
        arraySave.push({
          "ruanganid": parseInt(arrobj[i]),
        })
      }
    }
    let json = {
      'loginid': this.item.loginUser.id,
      'ruangan': arraySave
    }
    this.apiService.post('sysadmin/general/save-map-login-to-ruangan', json).subscribe(e => {

    })
  }
  changeUser(e) {
    this.item.mapModul = []
    this.item.mapRuangan = []
    if (e.value == null) return
    this.apiService.get('sysadmin/general/get-map-loginruangan?loginuseridfk=' + e.value.id).subscribe(res => {
      this.item.mapRuangan = []
      this.alertService.info('Info',res.length +' Ruangan Terpilih')
      for (let i = 0; i < res.length; i++) {
        const element = res[i];
        this.item.mapRuangan[element.ruanganidfk] = true
      }
    })
    this.apiService.get('sysadmin/general/get-map-loginmodul?loginuseridfk=' + e.value.id).subscribe(es => {
      this.item.mapModul = []
      this.alertService.info('Info',es.length +' Modul Terpilih')
      for (let i = 0; i < es.length; i++) {
        const element = es[i];
        element.idCheck = parseFloat(element.objectmodulaplikasifk + '.10')
        this.item.mapModul[element.idCheck] = true
      }
    })
  }
  saveModul() {
    if (this.item.loginUser == undefined) {
      this.alertService.warn('Info', 'Login User belum dipilih')
      return
    }
  
    var arrobj = Object.keys(this.item.mapModul)
    var arraySave = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      if (this.item.mapModul[arrobj[i]] == true) {
        let idna = arrobj[i].split('.')
        arraySave.push({
          "objectmodulaplikasifk": parseInt(idna[0]),
        })
      }
    }
    if (arraySave.length == 0) {
      this.alertService.warn('Info', 'Belum ada Modul yang dipilih')
      return
    }
    let json = {
      'objectloginuserfk': this.item.loginUser.id,
      'details': arraySave
    }
    this.apiService.post('sysadmin/general/save-map-login-to-modul', json).subscribe(e => {

    })
  }
}
