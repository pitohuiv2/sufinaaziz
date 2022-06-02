import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-input-provinsi',
  templateUrl: './input-provinsi.component.html',
  styleUrls: ['./input-provinsi.component.scss'],
  providers: [ConfirmationService]
})
export class InputProvinsiComponent implements OnInit {

    item: any = {
    // mapModul: [],
    // mapRuangan: [],
    // mapModul2: []
  }
  column: any[]
  dataSource: any[]
  pop_Provinsi: boolean = false
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
    this.apiService.get('sysadmin/general/get-daftar-provinsi').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataSource = e.data
      // this.d_LoginUser = e.data
    }) 
  }
  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'aktif', header: 'Aktif', width: "100px" },
      { field: 'id', header: 'Kode Provinsi', width: "150px" },
      { field: 'namapropinsi', header: 'Provinsi', width: "250px" },
      { field: 'kdpropinsi', header: 'Kode External', width: "150px" },
      { field: 'kdmap', header: 'Maps', width: "250px" },
      
    ];
  }
  editProv(e) {
    var dataItem = e
      for (var i = 0; i < this.listStatus.length; i++) {
      const element = this.listStatus[i]
      if(element.stt == dataItem.statusna){ //status nu digrid
          this.item.status =  element
      }
    }
    this.item.idprovinsi = dataItem.id
    this.item.namaprovinsi = dataItem.namapropinsi
    this.item.kdpropinsi = dataItem.kdpropinsi
    this.item.kdmap = dataItem.kdmap 
    this.pop_Provinsi = true
  }
  hapusProv(e) {
    var objSave = {
      'id': e.id
    }
    this.apiService.post('sysadmin/general/delete-provinsi', objSave).subscribe(e => {
      this.loadData()
    })
  }

  tambahProv() {
    this.pop_Provinsi = true
    this.clearProv()
  }
  simpanProvinsi() {
    if (this.item.namaprovinsi == undefined) {
      this.alertService.error('Info', 'Provinsi Harus di isi')
      return
    }
      var objSave = {
      //** Data Pelayanan */
        "id": this.item.id != undefined ? this.item.id : '-',
        "aktif": this.item.status != undefined ? this.item.status.stt : 't',
        "namaprovinsi": this.item.namaprovinsi != undefined ? this.item.namaprovinsi : null,
        "kdmap": this.item.kdmap != undefined ? this.item.kdmap : null,
        "kdpropinsi":this.item.kdpropinsi != undefined ? this.item.kdpropinsi : null,        
                                            
    }
    
    this.isSimpan = true    
    this.apiService.post('sysadmin/general/save-new-propinsi', objSave).subscribe(e => {
    this.isSimpan = false
    this.pop_Provinsi = false
    this.loadData()
    this.clearProv()

    })
  }
  clearProv() {
    delete this.item.idprovinsi
    delete this.item.namaprovinsi
    delete this.item.kdmap
    delete this.item.kdpropinsi
    delete this.item.status
    // delete this.item.kataKunciConfirm
  }
  filterPegawai(event) {
    let query = event.query;
    this.apiService.get("sysadmin/general/get-pegawai-part?namalengkap=" + query
    ).subscribe(re => {
      this.d_Pegawai = re;
    })

  // handleChangeTab(e) {
  //   this.indexTab = e.index
  //   if (e.index == 0) {
  //     this.loadData()
  //   } else {
  //     this.loadData()
  //   }

  // }
  // clearFilter() {
  //   this.searchText = "";
  // }
  // clearFilter2() {
  //   this.searchText2 = "";
  }

}
