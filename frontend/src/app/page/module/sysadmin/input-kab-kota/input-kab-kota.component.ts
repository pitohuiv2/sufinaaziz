import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-input-kab-kota',
  templateUrl: './input-kab-kota.component.html',
  styleUrls: ['./input-kab-kota.component.scss'],
  providers: [ConfirmationService]
})
export class InputKabKotaComponent implements OnInit {

    item: any = {
    // mapModul: [],
    // mapRuangan: [],
    // mapModul2: []
  }
  column: any[]
  dataSource: any[]
  pop_KotaKab: boolean = false
  listStatus: any[] = [];
  listProvinsi: any[] = [];
  // item: any = {};
  d_Profile: any[]
  d_KelompokUser: any[]
  d_Pegawai: any[]
  d_LoginUser: any[]
  isSimpan: boolean = false
  indexTab = 0
  searchText = '';
  searchText2 = '';
  
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
    this.apiService.get("sysadmin/general/get-combo-master-wilayah").subscribe(table => {
      var dataCombo = table;
      this.listProvinsi = dataCombo.namapropinsi;
      // this.listSediaan = dataCombo.sediaan;
      // this.listGolonganDarah = dataCombo.goloangandarah;
      // this.listRhesus = dataCombo.rhesus;
      // this.listKelompokUser = dataCombo.kelompokuser;
    });
  }
  loadData() {
    this.apiService.get('sysadmin/general/get-daftar-kab-kota').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataSource = e.data
      // this.data = table[0];
       
      // this.d_LoginUser = e.data
	// this.item.namapropinsi = {id: this.data.provinsiidfk ,namapropinsi: this.data.namapropinsi};
    }) 
    // this.apiService.get("sysadmin/general/get-daftar-kab-kota").subscribe(table => {
      // var dataCombo = table;
      // this.listDetailJenisProduk = dataCombo.detailjenisproduk;
    // });
  }
  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'aktif', header: 'Aktif', width: "100px" },
      { field: 'namapropinsi', header: 'Provinsi', width: "150px" },
      { field: 'kdkotakabupaten', header: 'kd.Kota/Kab', width: "75px" },
      { field: 'namakotakabupaten', header: 'Kota/Kab', width: "150px" },
      { field: 'lat', header: 'Lat', width: "100px" },
      { field: 'lng', header: 'Lng', width: "100px" },
      
    ];
  }
  editKabKota(e) {
    var dataItem = e
      for (var i = 0; i < this.listStatus.length; i++) {
      const element = this.listStatus[i]
      if(element.stt == dataItem.statusna){ //status nu digrid
          this.item.status =  element
      }
    }
    this.item.idkotakab = dataItem.id
    this.item.namapropinsi = { id: dataItem.provinsiidfk, 	namaprovinsi: dataItem.namapropinsi }    
    this.item.kotakab = dataItem.namakotakabupaten
    this.item.external = dataItem.kdkotakabupaten
    this.item.longitude = dataItem.lng 
    this.item.latitude = dataItem.lat 
    this.pop_KotaKab = true

  }
  hapuskabkota(e) {
    var objSave = {
      'id': e.id
    }
    this.apiService.post('sysadmin/general/delete-kab-kota', objSave).subscribe(e => {
      this.loadData()
    })
  }

  tambahKotaKab() {
    this.pop_KotaKab = true
    this.clearKotaKab()
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
    this.apiService.post('sysadmin/general/save-new-kota-kabupaten', objSave).subscribe(e => {
    this.isSimpan = false
    this.pop_KotaKab = false
    this.loadData()
    this.clearKotaKab()

    })
  }
  clearKotaKab() {
    delete this.item.instalasi
    delete this.item.kotakab
    delete this.item.external
    delete this.item.longitude
    delete this.item.latitude
    delete this.item.status
    delete this.item.idkotakab
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