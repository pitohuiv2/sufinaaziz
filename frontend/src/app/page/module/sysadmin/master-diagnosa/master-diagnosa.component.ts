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
  selector: 'app-master-diagnosa',
  templateUrl: './master-diagnosa.component.html',
  styleUrls: ['./master-diagnosa.component.scss'],
  providers: [ConfirmationService]
})
export class MasterDiagnosaComponent implements OnInit {
  indexTab = 0
  selected: any; 
  columnJd: any[];
  columnIcdX: any[];
  columnIcdIX: any[];
  dataSourceJd: any[];  
  dataSourceIcdX: any [];
  dataSourceIcdIX: any [];
  item: any = { layanan: [] }
  itemtp: any = {};
  itemrkn: any = {};
  itemjrkn: any = {};
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  selectedGrid: any;
  popUpJd: boolean = false;
  popUpJRkn: boolean = false;
  popUpRkn: boolean = false;
  isSimpan: boolean;
  idPegawai: any = "";
  listKelompokPasien: any[];
  listJenisDiagnosa: any[];
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
  ) { }

  ngOnInit(): void {
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.loadColumn();
    this.loadCombo();
    this.loadJd();
  }

  loadColumn() {
    this.columnJd = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'jenisdiagnosa', header: 'Jenis Diagnosa', width: "150px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
    this.columnIcdX = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'kddiagnosa', header: 'Kode Diagnosa', width: "120px" },
      { field: 'namadiagnosa', header: 'Nama Diagnosa', width: "250px"},
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
    this.columnIcdIX = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'kddiagnosa', header: 'Kode Diagnosa', width: "120px" },
      { field: 'namadiagnosa', header: 'Nama Diagnosa', width: "250px"},
      { field: 'status', header: 'Status', width: "100px", isTag: true },
    ];
  }

  loadCombo() {
    this.apiService.get('sysadmin/general/get-combo-master-rekanan').subscribe(data => {
      this.listJenisDiagnosa = data.jenisdiagnosa;
      // this.listRekanan = data.rekanan;
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
      this.loadJd()
    } else if (e.index == 1) {
      this.loadIcdX();
    } else if (e.index == 2) {
      this.loadIcdIX();
    } else if (e.index == 3) {
      // this.apiService.get('sysadmin/general/get-map-rekanan-combo').subscribe(z => {        
      //   this.listProdukCek = z.rekanan;
      //   this.produkDef = z.rekanan;
      // })
    } else if (e.index == 4) {
      // this.loadTT()
    }

  }
  
  //** JENIS DIAGNOSA */
  loadJd(){
    this.apiService.get('sysadmin/general/get-master-data?table=jenisdiagnosamt').subscribe(e => {
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
      this.dataSourceJd = e.data     
    })
  }

  tambahJd() {
    this.itemtp.IdJenisDiagnosa = undefined;
    this.itemtp.jenisDiagnosa = undefined;
    this.popUpJd = true;
  }

  editTp(e) {
    this.itemtp.IdJenisDiagnosa = e.id
    this.itemtp.jenisDiagnosa = e.jenisdiagnosa
    this.popUpJd = true
  }

  hapusTp(e) {
    var objSave = {
      'id': e.id,
      'jenisdiagnosa': e.jenisdiagnosa,
      'aktif': false,
      'table': 'jenisdiagnosamt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveTp() {
    if (this.itemtp.jenisDiagnosa == undefined) {
      this.alertService.warn('Info', 'Jenis Diagnosa Belum Diisi')
      return
    }

    var objSave = {
      'id': this.itemtp.IdJenisDiagnosa != undefined ? this.itemtp.IdJenisDiagnosa : '',
      'jenisdiagnosa': this.itemtp.jenisDiagnosa,
      'aktif': true,
      'table': 'jenisdiagnosamt'
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpJd = false
      this.handleChangeTab({ index: this.indexTab })
      this.clearAll()

    }, error => {
      this.isSimpan = false
    })
  }
  //** END JENIS DIAGNOSA */

  //** ICD X */
  loadIcdX(){
    this.apiService.get('sysadmin/general/get-master-data?table=icdixmt').subscribe(e => {
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
      this.dataSourceIcdX = e.data     
    })
  }

  tambahIcdX() {
    this.itemrkn.IdDiagnosaX = undefined;
    this.itemrkn.kodeDiagnosa = undefined;
    this.itemrkn.namaDiagnosa = undefined;
    this.popUpRkn = true;
  }

  editIcdX(e) {
    this.itemrkn.IdDiagnosaX = e.id
    this.itemrkn.kodeDiagnosa = e.kddiagnosa;
    this.itemrkn.namaDiagnosa = e.namadiagnosa;
    this.popUpRkn = true
  }

  hapusIcdX(e) {
    var objSave = {
      'id': e.id,
      'kddiagnosa': e.kddiagnosa,
      'namadiagnosa': e.namadiagnosa,
      'aktif': false,
      'table': 'icdxmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveRkn() {
    if (this.itemrkn.kodeDiagnosa == undefined) {
      this.alertService.warn('Info', 'Kode Diagnosa Belum Diisi')
      return
    }

    if (this.itemrkn.namaDiagnosa == undefined) {
      this.alertService.warn('Info', 'Nama Diagnosa Belum Diisi')
      return
    }

    var objSave = {
      'id': this.itemrkn.IdDiagnosaX != undefined ? this.itemrkn.IdDiagnosaX : '',
      'jenisdiagnosa': this.itemrkn.kodeDiagnosa,
      'namadiagnosa': this.itemrkn.namaDiagnosa,
      'aktif': true,
      'table': 'icdxmt'
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
  //** END ICD X */

  //** ICD IX */
  loadIcdIX(){
    this.apiService.get('sysadmin/general/get-master-data?table=icdixmt').subscribe(e => {
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
      this.dataSourceIcdIX = e.data     
    })
  }

  tambahIcdIX() {
    this.itemjrkn.IdDiagnosaIX = undefined;
    this.itemjrkn.kodeDiagnosa = undefined;
    this.itemjrkn.namaDiagnosa = undefined;
    this.popUpJRkn = true;
  }

  editIcdIX(e) {
    this.itemjrkn.IdDiagnosaIX = e.id
    this.itemjrkn.kodeDiagnosa = e.kddiagnosa;
    this.itemjrkn.namaDiagnosa = e.namadiagnosa;
    this.popUpJRkn = true
  }

  hapusIcdIX(e) {
    var objSave = {
      'id': e.id,
      'kddiagnosa': e.kddiagnosa,
      'namadiagnosa': e.namadiagnosa,
      'aktif': false,
      'table': 'icdixmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveJRkn() {
    if (this.itemjrkn.kodeDiagnosa == undefined) {
      this.alertService.warn('Info', 'Kode Diagnosa Belum Diisi')
      return
    }

    if (this.itemjrkn.namaDiagnosa == undefined) {
      this.alertService.warn('Info', 'Nama Diagnosa Belum Diisi')
      return
    }

    var objSave = {
      'id': this.itemjrkn.IdDiagnosaIX != undefined ? this.itemjrkn.IdDiagnosaIX : '',
      'jenisdiagnosa': this.itemjrkn.kodeDiagnosa,
      'namadiagnosa': this.itemjrkn.namaDiagnosa,
      'aktif': true,
      'table': 'icdixmt'
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
  //** END ICD IX */

}
