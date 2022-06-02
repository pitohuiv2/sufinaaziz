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
  selector: 'app-master-rekanan',
  templateUrl: './master-rekanan.component.html',
  styleUrls: ['./master-rekanan.component.scss'],
  providers: [ConfirmationService]
})
export class MasterRekananComponent implements OnInit {
  indexTab = 0
  selected: any;
  dataSourceTp: any[];
  columnTp: any[];
  dataSourceJRkn: any[];
  columnJRkn: any[];
  dataSourceRkn: any[];
  columnRkn: any[];
  item: any = { layanan: [] }
  itemtp: any = {};
  itemrkn: any = {};
  itemjrkn: any = {};
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  selectedGrid: any;
  popUpTp: boolean = false;
  popUpJRkn: boolean = false;
  popUpRkn: boolean = false;
  isSimpan: boolean;
  idPegawai: any = "";
  listKelompokPasien: any[];
  listJenisRekanan: any[];
  listRekanan: any[];
  listRekananCek: any[] = [];
  listProdukCek: any[] = [];
  listChecked = [];
  produkDef: any[] = [];
  countSelected = 0;
  searchText = '';
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
  ) { 
    
  }

  ngOnInit(): void {
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.loadColumn();
    this.loadCombo();
    this.loadTp();
  }

  loadColumn() {
    this.columnTp = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "150px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
    this.columnJRkn = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'jenisrekanan', header: 'Jenis Rekanan', width: "250px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
    this.columnRkn = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'jenisrekanan', header: 'Jenis Rekanan', width: "150px" },
      { field: 'namarekanan', header: 'Nama Rekanan/Supplier', width: "350px" },
      { field: 'alamatlengkap', header: 'Alamat', width: "250px" },
      { field: 'telepon', header: 'No. Telp', width: "100px" },
      { field: 'email', header: 'Email', width: "100px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
  }

  loadCombo() {
    this.apiService.get('sysadmin/general/get-combo-master-rekanan').subscribe(data => {
      this.listJenisRekanan = data.jenisrekanan;
      this.listRekanan = data.rekanan;
    });
  }

  clearAll() {
    this.item = {};
    this.itemtp = {};
    this.itemrkn = {};
    this.itemjrkn = {};
  }

  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadTp()
    } else if (e.index == 1) {
      this.LoadJRkn();
    } else if (e.index == 2) {
      this.LoadRkn();
    } else if (e.index == 3) {
      this.apiService.get('sysadmin/general/get-map-rekanan-combo').subscribe(z => {        
        this.listProdukCek = z.rekanan;
        this.produkDef = z.rekanan;
      })
    } else if (e.index == 4) {
      // this.loadTT()
    }

  }

  //** TIPE PASIEN */
  loadTp() {
    this.apiService.get('sysadmin/general/get-master-data?table=kelompokpasienmt').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.aktif == true || element.aktif == "true") {
          element.status = 'Aktif';
          element.class = 'p-tag p-tag-success';
        } else {
          element.status = 'Non Aktif';
          element.class = 'p-tag p-tag-help';
        }
      }
      this.dataSourceTp = e.data
      this.listKelompokPasien = e.data
    })
  }

  tambahTp() {
    this.itemtp.IdtipePasien = undefined;
    this.itemtp.tipePasien = undefined;
    this.popUpTp = true;
  }

  editTp(e) {
    this.itemtp.IdtipePasien = e.id
    this.itemtp.tipePasien = e.kelompokpasien
    this.popUpTp = true
  }

  hapusTp(e) {
    var objSave = {
      'id': e.id,
      'tipepasien': e.kelompokpasien,
      'aktif': false,
      'table': 'kelompokpasienmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveTp() {
    if (this.itemtp.tipePasien == undefined) {
      this.alertService.warn('Info', 'Tipe Pasien Belum Diisi')
      return
    }

    var objSave = {
      'id': this.itemtp.IdtipePasien != undefined ? this.itemtp.IdtipePasien : '',
      'tipepasien': this.itemtp.tipePasien,
      'aktif': true,
      'table': 'kelompokpasienmt'
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpTp = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()

    }, error => {
      this.isSimpan = false
    })
  }

  //** END TIPE PASIEN */

  //** JENIS REKANAN / SUPPLIER */   
  LoadJRkn() {
    this.apiService.get('sysadmin/general/get-master-data?table=jenisrekananmt').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.aktif == true || element.aktif == "true") {
          element.status = 'Aktif';
          element.class = 'p-tag p-tag-success';
        } else {
          element.status = 'Non Aktif';
          element.class = 'p-tag p-tag-help';
        }
      }
      this.dataSourceJRkn = e.data
    })
  }

  tambahJRkn() {
    this.itemjrkn.idJenisRekanan = undefined;
    this.itemjrkn.jenisRekanan = undefined;
    this.popUpJRkn = true;
  }

  editJRkn(e) {
    this.itemjrkn.idJenisRekanan = e.id
    this.itemjrkn.jenisRekanan = e.jenisrekanan
    this.popUpJRkn = true
  }

  hapusJRkn(e) {
    var objSave = {
      'id': e.id,
      'jenisrekanan': e.jenisrekanan,
      'aktif': false,
      'table': 'jenisrekananmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveJRkn() {
    if (this.itemjrkn.jenisRekanan == undefined) {
      this.alertService.warn('Info', 'Jenis Rekanan Belum Diisi')
      return
    }

    var objSave = {
      'id': this.itemjrkn.idJenisRekanan != undefined ? this.itemjrkn.idJenisRekanan : '',
      'jenisrekanan': this.itemjrkn.jenisRekanan,
      'aktif': true,
      'table': 'jenisrekananmt'
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpJRkn = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()

    }, error => {
      this.isSimpan = false
    })

  }
  //** END JENIS REKANAN / SUPPLIER */

  //** REKANAN / SUPPLIER */
  LoadRkn() {
    this.apiService.get('sysadmin/general/get-master-data?table=rekananmt').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
        if (element.aktif == true || element.aktif == "true") {
          element.status = 'Aktif';
          element.class = 'p-tag p-tag-success';
        } else {
          element.status = 'Non Aktif';
          element.class = 'p-tag p-tag-help';
        }
      }
      this.dataSourceRkn = e.data
    })
  }

  tambahRkn() {
    this.itemrkn.idRekanan = undefined;
    this.itemrkn.namaRekanan = undefined;
    this.itemrkn.dataJenisRekanan = undefined;
    this.itemrkn.email = undefined;
    this.itemrkn.telepon = undefined;
    this.itemrkn.alamat = undefined;
    this.popUpRkn = true;
  }

  editRkn(e) {
    this.itemrkn.idRekanan = e.id;
    this.itemrkn.namaRekanan = e.namarekanan;
    this.itemrkn.dataJenisRekanan = { id: e.jenisrekananidfk, jenisrekanan: e.jenisrekanan };
    this.itemrkn.email = e.email;
    this.itemrkn.telepon = e.telepon;
    this.itemrkn.alamat = e.alamatlengkap;
    this.popUpRkn = true
  }

  hapusRkn(e) {
    var objSave = {
      'id': e.id,
      'namarekanan': e.jenisrekanan,
      'jenisrekananidfk': e.jenisrekananidfk,
      'alamatlengkap': e.alamatlengkap,
      'email': e.email,
      'telepon': e.telepon,
      'aktif': false,
      'table': 'rekananmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveRkn() {
    if (this.itemrkn.namaRekanan == undefined) {
      this.alertService.warn('Info', 'Nama Rekanan Belum Diisi')
      return
    }

    var objSave = {
      'id': this.itemrkn.idRekanan != undefined ? this.itemrkn.idRekanan : '',
      'namarekanan': this.itemrkn.namaRekanan,
      'jenisrekananidfk': this.itemrkn.dataJenisRekanan != undefined ? this.itemrkn.dataJenisRekanan.id : null,
      'alamatlengkap': this.itemrkn.alamat != undefined ? this.itemrkn.alamat : null,
      'email': this.itemrkn.email != undefined ? this.itemrkn.email : null,
      'telepon': this.itemrkn.telepon != undefined ? this.itemrkn.telepon : null,
      'aktif': true,
      'table': 'rekananmt'
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpRkn = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()

    }, error => {
      this.isSimpan = false
    })

  }
  //** REKANAN / SUPPLIER */


  //** MAP REKANAN TO TIPE PASIEN */
  saveMap() {
    if (this.item.dataTipePasien == undefined) {
      this.alertService.warn('Info', 'Tipe Pasien belum dipilih')
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
          "rekananidfk": parseInt(arrobj[i]),
        })
      }
    }
    let json = {
      'kelompokpasienidfk': this.item.dataTipePasien.id,
      'details': arraySave
    }
    this.apiService.post('sysadmin/general/save-map-rekanan-tipepasien', json).subscribe(e => {

    })
  }

  changeMap(e) {
    this.item.layanan = []
    this.getSelected()
    if (e.value == null) return
    this.apiService.get('sysadmin/general/get-map-rekanan-tipepasien?KelompokPasienId=' + e.value.id).subscribe(e => {
      this.item.layanan = []
      for (let i = 0; i < e.length; i++) {
        const element = e[i];
        this.item.layanan[element.rekananidfk] = true
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
                if (element3.namarekanan == element2.namarekanan) {
                  this.listChecked.splice(z, 1)
                }
              }
              this.listChecked.push({ namarekanan: element2.namarekanan })
            }
          }
        } else {
          for (var i = 0; i < this.produkDef.length; i++) {
            const element2 = this.produkDef[i];
            if (element2.id == element) {
              for (var z = 0; z < this.listChecked.length; z++) {
                const element3 = this.listChecked[z];
                if (element3.namarekanan == element2.namarekanan) {
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

  clearFilter() {
    this.searchText = "";
  }  
  //** END MAP REKANAN TO TIPE PASIEN */
}
