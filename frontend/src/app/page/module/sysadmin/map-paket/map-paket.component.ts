import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-map-paket',
  templateUrl: './map-paket.component.html',
  styleUrls: ['./map-paket.component.scss']
})
export class MapPaketComponent implements OnInit {
  item: any = {
    layanan: [],

    limit: 200
  }
  column: any[]
  dataSource: any[]
  pop_User: boolean = false
  d_Profile: any[]
  d_KelompokUser: any[]
  d_Pegawai: any[]
  d_LoginUser: any[]
  d_Departemen: any[]
  d_Ruangan: any[]
  d_Paket: any[]
  isSimpan: boolean = false
  indexTab = 0
  searchText = '';
  searchText2 = '';
  listModul: any[] = [];
  listRuangan: any[] = []
  ruanganDef: any[] = [];
  produkDef: any[] = [];
  d_Ruangan2: any[] = []
  listProdukCek: any[] = []
  listChecked = []
  countSelected = 0
  selectedGrid: any
  columnPak: any
  popUpPak: boolean
  dataSourcePak: any
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    // private confirmationService: ConfirmationService,
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
    this.apiService.get('sysadmin/general/get-map-ruangan-combo').subscribe(e => {
      this.d_Departemen = e.departemen
      this.listProdukCek = e.produk;
      this.produkDef = e.produk;
      this.d_Paket = e.paket;
    })
  }
  cari() {
    this.loadData()
  }
  tambahPak() {
    delete this.item.namapaket
    delete this.item.idP
    this.popUpPak = true
  }
  savePak() {
    let json = {
      id: this.item.idP ? this.item.idP : '',
      namapaket: this.item.namapaket,
      method: 'save',
    }
    this.apiService.post('sysadmin/general/save-paket', json).subscribe(e => {
      delete this.item.namapaket
      delete this.item.idP
      this.popUpPak = false
      this.loadPaket()
      this.loadCombo()
    })

  }
  editPak(e) {
    this.item.namapaket = e.namapaket
    this.item.idP = e.id
    this.popUpPak = true
  }
  delPak(e) {
    let json = {
      id: e.id,
      method: 'delete',
    }
    this.apiService.post('sysadmin/general/save-paket', json).subscribe(e => {
      this.loadPaket()
      this.loadCombo()
    })

  }
  loadPaket() {
    this.apiService.get('sysadmin/general/get-paket').subscribe(e => {
      for (let i = 0; i < e.length; i++) {
        const element = e[i];
        element.no = i + 1
      }
      this.dataSourcePak = e
    })

  }
  loadData() {
    let instId = ''
    let ruangId = ''
    let produk = ''
    let limit = ''
    if (this.item.qDept != undefined) {
      instId = this.item.qDept.id
    }
    if (this.item.qRuangn != undefined) {
      ruangId = this.item.qRuangn.id
    }
    if (this.item.qProduk != undefined) {
      produk = this.item.qProduk
    }
    if (this.item.limit != undefined) {
      limit = this.item.limit
    }
    this.apiService.get('sysadmin/general/get-map-paket?instId=' + instId + '&paketId=' + ruangId
      + '&produk=' + produk + '&limit=' + limit).subscribe(e => {
        for (let i = 0; i < e.length; i++) {
          const element = e[i];
          element.no = i + 1
        }
        this.dataSource = e
      })
  }
  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namapaket', header: 'Nama Paket', width: "150px" },
      { field: 'jml', header: 'Jml', width: "100px" },
    ];
    this.columnPak = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namapaket', header: 'Nama Paket', width: "150px" },
    ];
  }
  isiRuangan() {

    if (this.item.qDept != undefined) {
      this.d_Ruangan = this.item.qDept.ruangan;
    }
  }
  isiRuangan2() {

    if (this.item.instalasi != undefined) {
      this.d_Ruangan2 = this.item.instalasi.ruangan;
    }
  }
  hapusRow(e) {
    var objSave = {
      'id': e.paketidfk
    }
    this.apiService.post('sysadmin/general/delete-map-paket', objSave).subscribe(e => {
      this.loadData()
    })
  }
  hapusRowD(e) {
    var objSave = {
      'paketidfk': e.paketidfk,
      'produkidfk': e.produkidfk
    }
    this.apiService.post('sysadmin/general/delete-map-paket', objSave).subscribe(e => {
      this.loadData()
    })
  }

  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadData()

    } else if (e.index == 2) {
      this.loadPaket()
    } else {
      // this.loadData()
    }

  }
  clearFilter() {
    this.searchText = "";
  }

  saveMap() {
    if (this.item.paket == undefined) {
      this.alertService.warn('Info', 'Paket belum dipilih')
      return
    }
    if (this.item.layanan.length == 0) {
      this.alertService.warn('Info', 'Belum ada Pelayanan yang dipilih')
      return
    }
    var arrobj = Object.keys(this.item.layanan)
    var arraySave = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      if (this.item.layanan[arrobj[i]] == true) {
        arraySave.push({
          "produkidfk": parseInt(arrobj[i]),
        })
      }
    }
    let json = {
      'paketidfk': this.item.paket.id,
      'details': arraySave
    }
    this.apiService.post('sysadmin/general/save-map-paket', json).subscribe(e => {

    })
  }
  changeMap(e) {
    this.item.layanan = []
    this.getSelected()
    if (e.value == null) return
    this.apiService.get('sysadmin/general/get-map-paket?paketId=' + e.value.id).subscribe(res => {
      this.item.layanan = []
      if (res.length > 0) {
        for (let i = 0; i < res[0].details.length; i++) {
          const element = res[0].details[i];
          this.item.layanan[element.produkidfk] = true
        }
        this.getSelected()
      }


    })

  }
  cekAll() {
    for (let x = 0; x < this.listProdukCek.length; x++) {
      const element2 = this.listProdukCek[x];
      this.item.layanan[element2.id] = true
    }
    this.getSelected()
  }
  clearSelection() {
    var arrobj = Object.keys(this.item.layanan)
    for (let x = 0; x < arrobj.length; x++) {
      const element2 = arrobj[x];
      this.item.layanan[element2] = false
    }
    this.getSelected()
  }
  getSelected() {
    if (this.item.layanan.length > 0) {
      var arrobj = Object.keys(this.item.layanan)
      for (var x = 0; x < arrobj.length; x++) {
        const element = arrobj[x];
        if (this.item.layanan[parseInt(element)] == true) {
          for (var i = 0; i < this.produkDef.length; i++) {
            const element2 = this.produkDef[i];
            if (element2.id == element) {
              for (var z = 0; z < this.listChecked.length; z++) {
                const element3 = this.listChecked[z];
                if (element3.namaproduk == element2.namaproduk) {
                  this.listChecked.splice(z, 1)
                }
              }
              this.listChecked.push({ namaproduk: element2.namaproduk })
            }
          }
        } else {
          for (var i = 0; i < this.produkDef.length; i++) {
            const element2 = this.produkDef[i];
            if (element2.id == element) {
              for (var z = 0; z < this.listChecked.length; z++) {
                const element3 = this.listChecked[z];
                if (element3.namaproduk == element2.namaproduk) {
                  this.listChecked.splice(z, 1)
                }
              }
            }
          }
        }
      }
      this.countSelected = this.listChecked.length
    } else {
      this.listChecked = []
    }
  }
}

