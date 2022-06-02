import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-master-produk',
  templateUrl: './master-produk.component.html',
  styleUrls: ['./master-produk.component.scss'],
  providers: [ConfirmationService]
})
export class MasterProdukComponent implements OnInit {
  item: any = {}
  column: any[]
  dataSource: any[]
  pop_User: boolean = false
  d_Profile: any[]
  d_KelompokUser: any[]
  d_Pegawai: any[]
  d_LoginUser: any[]
  isSimpan: boolean = false
  searchText = '';
  searchText2 = '';
  selectedGrid: any;
  listModul: any[] = [];
  listRuangan: any[] = [];
  indexTab: number
  dataSourceJenis: any
  dataSourceDetail: any
  dataSourceKelompok: any
  columnDetail: any
  columnJenis: any
  columnKelompok: any
  popUpTambah: boolean
  tableKon: string
  listKelompokProd: any[]
  listJenisProd: any
  listJneis: any
  columnAp: any
  dataSourceAp: any[]
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
    this.loadColumn()
    this.loadCombo()
    this.loadData()
  }
  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadData()
    } else if (e.index == 1) {
      this.loadDataDetail()
    } else if (e.index == 2) {
      this.loadDataJenis()
    } else if (e.index == 3) {
      this.loadDataKelompok()
    }else if (e.index == 4) {
      this.loadDataAsalProduk()
    }
  }
  loadDataDetail() {
    this.dataSourceDetail = []
    this.apiService.get('sysadmin/general/get-master-data?table=detailjenisprodukmt').subscribe(e => {
      let data = e.data
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = 1 + i;
        if (element.aktif == true) {
          element.aktif = 'Aktif'
          element.class = 'p-tag p-tag-success';
        } else {
          element.aktif = 'Tidak Aktif'
          element.class = 'p-tag p-tag-help';
        }

      }
      this.dataSourceDetail = data;
    })
  }
  loadDataJenis() {
    this.dataSourceJenis = []
    this.apiService.get('sysadmin/general/get-master-data?table=jenisprodukmt').subscribe(e => {
      let data = e.data
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = 1 + i;
        if (element.aktif == true) {
          element.aktif = 'Aktif'
          element.class = 'p-tag p-tag-success';
        } else {
          element.aktif = 'Tidak Aktif'
          element.class = 'p-tag p-tag-help';
        }

      }
      this.dataSourceJenis = data;
    })
  }
  loadDataKelompok() {
    this.dataSourceKelompok = []
    this.apiService.get('sysadmin/general/get-master-data?table=kelompokprodukmt').subscribe(e => {
      let data = e.data
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = 1 + i;
        if (element.aktif == true) {
          element.aktif = 'Aktif'
          element.class = 'p-tag p-tag-success';
        } else {
          element.aktif = 'Tidak Aktif'
          element.class = 'p-tag p-tag-help';
        }

      }
      this.dataSourceKelompok = data;
    })
  }
  
  loadCombo() {
    this.apiService.get('sysadmin/general/get-kelmpok-produk').subscribe(table => {
      this.listKelompokProd = table.kelompok
      this.listJneis = table.jenisproduk

    })
  }
  setJenis(event) {
    this.listJenisProd = event.value.jenisproduk
  }
  loadData() {
    this.apiService.get('sysadmin/general/get-master-pelayanan').subscribe(table => {
      var data = table
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = 1 + i;
        if (element.aktif == "Tidak Aktif") {
          element.class = 'p-tag p-tag-help';
        } else if (element.aktif == "Aktif") {
          element.class = 'p-tag p-tag-success';
        }
      }
      this.dataSource = data;
      // this.loadCache();
    })
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'aktif', header: 'Aktif', width: "100px", isTag: true },
      { field: 'id', header: 'idpelayanan', width: "120px" },
      { field: 'namaproduk', header: 'Nama Pelayanan', width: "250px" },
      { field: 'detailjenisproduk', header: 'Detail Jenis Pelayanan', width: "180px" },
      { field: 'jenisproduk', header: 'Jenis Pelayanan', width: "180px" },
      { field: 'Kelompokproduk', header: 'Kelompok Pelayanan', width: "180px" },
      { field: 'kelompokuser', header: 'Kelompok User', width: "150px" },
    ];
    this.columnDetail = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'aktif', header: 'Aktif', width: "100px", isTag: true },
      { field: 'id', header: 'Kode', width: "120px" },
      { field: 'detailjenisproduk', header: 'Detail Jenis Pelayanan', width: "250px" },
      { field: 'jenisproduk', header: 'Jenis Pelayanan', width: "180px" },
      { field: 'kelompokproduk', header: 'Kelompok Pelayanan', width: "180px" },
    ];
    this.columnJenis = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'aktif', header: 'Aktif', width: "100px", isTag: true },
      { field: 'id', header: 'Kode', width: "120px" },
      { field: 'jenisproduk', header: 'Jenis Pelayanan', width: "250px" },
      { field: 'kelompokproduk', header: 'Kelompok Pelayanan', width: "180px" },
    ];
    this.columnKelompok = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'aktif', header: 'Aktif', width: "100px", isTag: true },
      { field: 'id', header: 'Kode', width: "120px" },
      { field: 'kelompokproduk', header: 'Kelompok Pelayanan', width: "250px" },
    ];
    this.columnAp = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'aktif', header: 'Aktif', width: "100px", isTag: true },
      { field: 'id', header: 'Kode', width: "120px" },
      { field: 'asalproduk', header: 'Asal Produk', width: "250px" },
    ]
  }

  clearFilter() {
    this.searchText = "";
  }

  clearFilter2() {
    this.searchText2 = "";
  }

  loadCache() {
    var chaceMaster = this.cacheHelper.get('cacheMasterProdukLayanan');
    if (chaceMaster != undefined) {
      this.item.search = chaceMaster[0];
    }
  }

  tambah() {
    this.router.navigate(['input-master-produk', '-'])
  }

  hapusPelayanan(e) {
    var objSave = {
      'id': e.id
    }
    this.apiService.post('sysadmin/general/delete-master-pelayanan', objSave).subscribe(e => {
      this.loadData()
    })
  }

  editPelayanan(e) {
    if (e != undefined) {
      this.router.navigate(['input-master-produk', e.id])
    } else {
      this.alertService.error("Info", "Data Tidak Ditemukan !");
      return;
    }
  }
  tambahAdd(table) {
    this.loadCombo()
    this.item = {
      aktif: true
    }
    this.tableKon = table
    this.popUpTambah = true
  }
  saveTable() {
    var objSave = {}
    if (this.tableKon == 'detailjenisprodukmt') {
      if (!this.item.kelompokprodukd) return
      if (!this.item.jenispelayanand) return
      if (!this.item.detailjenisproduk) return
      objSave = {
        'id': this.item.idDet != undefined ? this.item.idDet : '',
        'kelompokprodukidfk': this.item.kelompokprodukd.id,
        'jenisprodukidfk': this.item.jenispelayanand.id,
        'detailjenisproduk': this.item.detailjenisproduk,
        'aktif': this.item.aktif ? this.item.aktif : false,
        'table': this.tableKon
      }
    }
    if (this.tableKon == 'jenisprodukmt') {
      if (!this.item.kelompokprodukd) return
      if (!this.item.jenispelayanan) return
      objSave = {
        'id': this.item.idJenis != undefined ? this.item.idJenis : '',
        'kelompokprodukidfk': this.item.kelompokprodukd.id,
        'jenisproduk': this.item.jenispelayanan,
        'aktif': this.item.aktif ? this.item.aktif : false,
        'table': this.tableKon
      }
    }
    if (this.tableKon == 'kelompokprodukmt') {
      if (!this.item.kelompokproduk) return
      objSave = {
        'id': this.item.idKel != undefined ? this.item.idKel : '',
        'kelompokproduk': this.item.kelompokproduk,
        'aktif': this.item.aktif ? this.item.aktif : false,
        'table': this.tableKon
      }
    }
    if (this.tableKon == 'asalprodukmt') {
      if (!this.item.asalproduk) return
      objSave = {
        'id': this.item.idAslProd != undefined ? this.item.idAslProd : '',
        'asalproduk': this.item.asalproduk,
        'aktif': this.item.aktif ? this.item.aktif : false,
        'table': this.tableKon
      }
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpTambah = false
      this.handleChangeTab({ index: this.indexTab })
    }, error => {
      this.isSimpan = false
    })

  }
  edit(row, table) {
    this.tableKon = table
    if (table == 'detailjenisprodukmt') {
      this.item.idDet = row.id
      for (let x = 0; x < this.listKelompokProd.length; x++) {
        const element = this.listKelompokProd[x];
        if (element.id == row.kelompokprodukidfk) {
          this.item.kelompokprodukd = element
          this.setJenis({ value: { jenisproduk: element.jenisproduk } })
          break
        }
      }
      this.item.jenispelayanand = { id: row.jenisprodukidfk, jenisproduk: row.jenisproduk }
      this.item.detailjenisproduk = row.detailjenisproduk
      this.item.aktif = row.aktif == 'Aktif' ? true : false
    }
    if (table == 'jenisprodukmt') {
      this.item.idJenis = row.id
      for (let x = 0; x < this.listKelompokProd.length; x++) {
        const element = this.listKelompokProd[x];
        if (element.id == row.kelompokprodukidfk) {
          this.item.kelompokprodukd = element
          break
        }
      }

      this.item.jenispelayanan = row.jenisproduk
      this.item.aktif = row.aktif == 'Aktif' ? true : false
    }
    if (table == 'kelompokprodukmt') {
      this.item.idKel = row.id
      this.item.kelompokproduk = row.kelompokproduk
      this.item.aktif = row.aktif == 'Aktif' ? true : false
    }
    if (table == 'asalprodukmt') {
      this.item.idAslProd = row.id
      this.item.asalproduk = row.asalproduk
      this.item.aktif = row.aktif == 'Aktif' ? true : false
    }
    this.popUpTambah = true

  }
  hapus(row, table) {
    var objSave = {}
    if (table == 'detailjenisprodukmt') {
      objSave = {
        'id': row.id,
        'kelompokprodukidfk': row.kelompokprodukidfk,
        'jenisprodukidfk': row.jenisprodukidfk,
        'detailjenisproduk': row.detailjenisproduk,
        'aktif': false,
        'table': table
      }
    }
    if (table == 'jenisprodukmt') {
      objSave = {
        'id': row.id,
        'kelompokprodukidfk': row.kelompokprodukidfk,
        'jenisproduk': row.jenisproduk,
        'aktif': false,
        'table': table
      }
    }
    if (table == 'kelompokprodukmt') {
      objSave = {
        'id': row.id,
        'kelompokproduk': row.kelompokproduk,
        'aktif': false,
        'table': table
      }
    }
    if (table == 'asalprodukmt') {
      objSave = {
        'id': row.id,
        'asalproduk': row.asalproduk,
        'aktif': false,
        'table': table
      }
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.handleChangeTab({ index: this.indexTab })
    })

  }

  loadDataAsalProduk() {
    this.dataSourceAp = []
    this.apiService.get('sysadmin/general/get-master-data?table=asalprodukmt').subscribe(e => {
      let data = e.data
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = 1 + i;
        if (element.aktif == true) {
          element.aktif = 'Aktif'
          element.class = 'p-tag p-tag-success';
        } else {
          element.aktif = 'Tidak Aktif'
          element.class = 'p-tag p-tag-help';
        }

      }
      this.dataSourceAp = data;
    })
  }

}
