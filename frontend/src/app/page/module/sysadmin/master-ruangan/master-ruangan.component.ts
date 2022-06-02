import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AppBreadcrumbService } from 'src/app/app.breadcrumb.service';
@Component({
  selector: 'app-master-ruangan',
  templateUrl: './master-ruangan.component.html',
  styleUrls: ['./master-ruangan.component.scss']
})
export class MasterRuanganComponent implements OnInit {
  item: any = { layanan: [] }
  columnRu: any = [];
  dataSourceRu: any = []
  columnIns: any = [];
  dataSourceIns: any = []
  indexTab = 0
  popUpIns: boolean = false
  isSimpan: boolean = false
  popUpRu: boolean = false
  listDept: any = []
  searchText = '';
  d_Departemen: any[]
  d_Ruangan: any[]
  d_Ruangan2: any[]
  ruanganDef: any[] = [];
  produkDef: any[] = [];
  listProdukCek: any[] = []
  listChecked = []
  countSelected = 0
  selectedGrid: any
  d_Kelas: any = []
  dataSourceKamar: any = []
  dataSourceTT: any = []
  columnKamar: any = []
  columnTT: any = []
  popUpKamar: boolean
  popUpTT: boolean
  dataSourceKls: any = []
  listRuangan: any = []
  listKamar: any = []
  listKelas: any
  listStatusBed: any
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
    private breadcrumbService: AppBreadcrumbService

  ) {

    this.breadcrumbService.setItems([
      { label: 'Master', routerLink: ['/'] },
      { label: 'Ruangan Pelayanan' },
    ]);
  }

  ngOnInit(): void {
    this.loadColumn()
    this.loadIns()
    this.loadComboNa()
  }
  loadColumn() {
    this.columnIns = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namadepartemen', header: 'Instalasi', width: "250px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
    this.columnRu = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namaruangan', header: 'Ruangan', width: "250px" },
      { field: 'namadepartemen', header: 'Instalasi', width: "250px" },
      { field: 'kodesiranap', header: 'Kode SIRANAP', width: "100px" },
      { field: 'kdinternal', header: 'Kode BPJS', width: "100px" },
      { field: 'noruangan', header: 'Kode Antrian Poli', width: "100px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
    this.columnTT = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namaruangan', header: 'Ruangan', width: "250px" },
      { field: 'namakamar', header: 'Kamar', width: "250px" },
      { field: 'nomorbed', header: 'No Bed', width: "100px" },
      { field: 'statusbed', header: 'Status Bed', width: "100px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
    this.columnKamar = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namaruangan', header: 'Ruangan', width: "250px" },
      { field: 'namakelas', header: 'Kelas', width: "150px" },
      { field: 'namakamar', header: 'Nama Kamar', width: "250px" },
      { field: 'qtybed', header: 'Jumlah Bed', width: "100px" },
      { field: 'keterangan', header: 'Keterangan', width: "100px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
  }
  editIns(e) {
    this.item.idDept = e.id
    this.item.namadepartemen = e.namadepartemen
    this.popUpIns = true
  }
  hapusIns(e) {
    var objSave = {
      'id': e.id,
      'namadepartemen': e.namadepartemen,
      'aktif': false,
      'table': 'instalasimt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })
  }
  editRu(e) {
    this.item.idRu = e.id
    this.item.dept = { id: e.instalasiidfk, namadepartemen: e.namadepartemen }
    this.item.kdinternal = e.kdinternal
    this.item.kodesiranap = e.kodesiranap
    this.item.namaruangan = e.namaruangan
    this.item.noruangan = e.noruangan
    this.popUpRu = true
  }
  hapusRu(e) {
    var objSave = {
      'id': e.id,
      'instalasiidfk': e.instalasiidfk,
      'namaruangan': e.namaruangan,
      'kdinternal': e.kdinternal,
      'kodesiranap': e.kodesiranap,
      'noruangan': e.noruangan,
      'aktif': false,
      'table': 'ruanganmt'
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    }, error => {

    })
  }
  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadIns()
    } else if (e.index == 1) {
      this.loadRu()
      this.loadComboNa()
    } else if (e.index == 2) {
      this.apiService.get('sysadmin/general/get-map-ruangan-combo2').subscribe(z => {
        this.d_Kelas = z.kelas
        this.listProdukCek = z.ruangan;
        this.produkDef = z.ruangan;
      })
    } else if (e.index == 3) {
      this.loadKamar()
    } else if (e.index == 4) {
      this.loadTT()
    }

  }
  loadComboNa() {
    this.apiService.get('sysadmin/general/get-comobo-ruru').subscribe(x => {
      this.listKelas = x.kelas
      this.listStatusBed = x.statusbed
      this.listRuangan = x.ruangan

    })
  }
  setKamar(event) {
    this.listKamar = []
    this.listKamar = event.value.kamar
  }
  loadRu() {
    this.loadComboNa()
    // this.listRuangan = []
    this.apiService.get('sysadmin/general/get-master-data?table=ruanganmt').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.aktif == true) {
          // this.listRuangan.push(element)
          element.status = 'Aktif';
          element.class = 'p-tag p-tag-success';
        } else {
          element.status = 'Non Aktif';
          element.class = 'p-tag p-tag-help';
        }
      }

      this.dataSourceRu = e.data
    })
  }
  loadIns() {
    this.apiService.get('sysadmin/general/get-master-data?table=instalasimt').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.aktif == true) {
          element.status = 'Aktif';
          element.class = 'p-tag p-tag-success';
        } else {
          element.status = 'Non Aktif';
          element.class = 'p-tag p-tag-help';
        }
      }
      this.dataSourceIns = e.data
      this.listDept = e.data
    })
  }
  tambah() {
    this.popUpIns = true
    this.clearAll()
  }
  saveInst() {
    var objSave = {
      'id': this.item.idDept != undefined ? this.item.idDept : '',
      'namadepartemen': this.item.namadepartemen,
      'aktif': true,
      'table': 'instalasimt'
    }
    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpIns = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()

    }, error => {
      this.isSimpan = false
    })
  }
  tambahRu() {
    this.popUpRu = true
    this.clearAll()
  }
  saveRu() {
    var objSave = {
      'id': this.item.idRu != undefined ? this.item.idRu : '',
      'instalasiidfk': this.item.dept.id,
      'namaruangan': this.item.namaruangan,
      'kdinternal': this.item.kdinternal ? this.item.kdinternal : null,
      'kodesiranap': this.item.kodesiranap ? this.item.kodesiranap : null,
      'noruangan': this.item.noruangan ? this.item.noruangan : null,
      'aktif': true,
      'table': 'ruanganmt'
    }
    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpRu = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()

    }, error => {
      this.isSimpan = false
    })
  }
  clearAll() {
    this.item = {}

  }
  isiRuangan2() {

    if (this.item.instalasi != undefined) {
      this.d_Ruangan2 = this.item.instalasi.ruangan;
    }
  }
  clearFilter() {
    this.searchText = "";
  }


  saveMap() {
    if (this.item.kelas == undefined) {
      this.alertService.warn('Info', 'Kelas belum dipilih')
      return
    }
    if (this.item.layanan.length == 0) {
      this.alertService.warn('Info', 'Belum ada Ruangan yang dipilih')
      return
    }
    var arrobj = Object.keys(this.item.layanan)
    var arraySave = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      if (this.item.layanan[arrobj[i]] == true) {
        arraySave.push({
          "ruanganidfk": parseInt(arrobj[i]),
        })
      }
    }
    let json = {
      'kelasidfk': this.item.kelas.id,
      'details': arraySave
    }
    this.apiService.post('sysadmin/general/save-map-ruangan-kelas', json).subscribe(e => {

    })
  }
  changeMap(e) {
    this.item.layanan = []
    this.getSelected()
    if (e.value == null) return
    this.apiService.get('sysadmin/general/get-map-ruangan-kelas?kelasId=' + e.value.id).subscribe(e => {
      this.item.layanan = []
      for (let i = 0; i < e.length; i++) {
        const element = e[i];
        this.item.layanan[element.ruanganidfk] = true
      }
      this.getSelected()
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
                if (element3.namaruangan == element2.namaruangan) {
                  this.listChecked.splice(z, 1)
                }
              }
              this.listChecked.push({ namaruangan: element2.namaruangan })
            }
          }
        } else {
          for (var i = 0; i < this.produkDef.length; i++) {
            const element2 = this.produkDef[i];
            if (element2.id == element) {
              for (var z = 0; z < this.listChecked.length; z++) {
                const element3 = this.listChecked[z];
                if (element3.namaruangan == element2.namaruangan) {
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
  tambahKamar() {
    this.popUpKamar = true
    this.clearAll()
  }
  editKamar(e) {
    this.item.idKamar = e.id
    for (let x = 0; x < this.listRuangan.length; x++) {
      const element = this.listRuangan[x];
      if (element.id == e.ruanganidfk) {
        this.item.ruangankm = element
        break
      }
    }

    this.item.kelaskm = { id: e.kelasidfk, namakelas: e.namakelas }
    this.item.namakamarkm = e.namakamar
    this.item.qtybed = e.qtybed
    this.item.keterangan = e.keterangan
    this.item.aktif = e.aktif
    this.popUpKamar = true
  }
  hapusKamar(e) {
    var objSave = {
      'id': e.id,
      'ruanganidfk': e.ruanganidfk,
      'kelasidfk': e.kelasidfk,
      'namakamar': e.namakamar,
      'qtybed': e.qtybed,
      'keterangan': e.keterangan,
      'aktif': false,
      'table': 'kamarmt'
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    }, error => {

    })
  }
  tambahTT() {
    this.popUpTT = true
    this.clearAll()
  }
  editTT(e) {
    this.item.idTT = e.id
    for (let x = 0; x < this.listRuangan.length; x++) {
      const element = this.listRuangan[x];
      if (element.id == e.ruanganidfk) {
        this.item.ruangantt = element
        this.setKamar({ value: { kamar: element.kamar } })
        break
      }
    }

    for (let x = 0; x < this.listKamar.length; x++) {
      const element = this.listKamar[x];
      if (element.id == e.kamaridfk) {
        this.item.kamartt = element
        break
      }
    }

    this.item.statusbed = { id: e.statusbedidfk, statusbed: e.statusbed }
    this.item.nomorbed = e.nomorbed
    this.item.aktif = e.aktif
    this.popUpTT = true
  }
  hapusTT(e) {
    var objSave = {
      'id': e.id,
      'kamaridfk': e.kamaridfk,
      'statusbedidfk': e.statusbedidfk,
      'nomorbed': e.nomorbed,
      'aktif': false,
      'table': 'kamarmt'
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    }, error => {

    })
  }
  loadKamar() {

    this.dataSourceKamar = []
    this.apiService.get('sysadmin/general/get-master-data?table=kamarmt').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.aktif == true) {

          element.status = 'Aktif';
          element.class = 'p-tag p-tag-success';
        } else {
          element.status = 'Non Aktif';
          element.class = 'p-tag p-tag-help';
        }
      }
      this.dataSourceKamar = e.data
    })
  }
  loadTT() {
    this.dataSourceTT = []
    this.apiService.get('sysadmin/general/get-master-data?table=tempattidurmt').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.aktif == true) {
          element.status = 'Aktif';
          element.class = 'p-tag p-tag-success';
        } else {
          element.status = 'Non Aktif';
          element.class = 'p-tag p-tag-help';
        }
      }
      this.dataSourceTT = e.data
    })
  }
  saveKamar() {
    if (!this.item.ruangankm) return
    if (!this.item.kelaskm) return
    if (!this.item.namakamarkm) return
    var objSave = {
      'id': this.item.idKamar != undefined ? this.item.idKamar : '',
      'ruanganidfk': this.item.ruangankm.id,
      'kelasidfk': this.item.kelaskm.id,
      'namakamar': this.item.namakamarkm,
      'qtybed': this.item.qtybed ? this.item.qtybed : null,
      'keterangan': this.item.keterangan ? this.item.keterangan : null,
      'aktif': this.item.aktif ? true : false,
      'table': 'kamarmt'
    }
    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpKamar = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()
    }, error => {
      this.isSimpan = false
    })
  }
  saveTT() {
    if (!this.item.kamartt) return
    if (!this.item.statusbed) return
    if (!this.item.nomorbed) return
    var objSave = {
      'id': this.item.idTT != undefined ? this.item.idTT : '',
      'kamaridfk': this.item.kamartt.id,
      'statusbedidfk': this.item.statusbed.id,
      'nomorbed': this.item.nomorbed,
      'aktif': this.item.aktif ? true : false,
      'table': 'tempattidurmt'
    }
    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpTT = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()
    }, error => {
      this.isSimpan = false
    })
  }
}
