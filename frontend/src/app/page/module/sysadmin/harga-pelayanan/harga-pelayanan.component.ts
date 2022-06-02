import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-harga-pelayanan',
  templateUrl: './harga-pelayanan.component.html',
  styleUrls: ['./harga-pelayanan.component.scss'],
  providers: [ConfirmationService]
})
export class HargaPelayananComponent implements OnInit {

  item: any = {
    rows: 30
  }
  indexTab: number
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
  listKelas: any
  listJP: any
  popUp: boolean
  add: any = {}
  listProduk: any[]
  data2: any = []
  listAsalProduk: any = []
  dataSource2: any = []
  columnGrid2: any = []
  listKomp: any = []
  selectedData: any
  columnPak: any
  dataSourcePak: any
  popUpPak: boolean
  listSK: any
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
  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }
  loadCombo() {
    this.apiService.get('sysadmin/general/get-combo-pelayanan').subscribe(table => {
      this.listKelas = table.kelas
      this.listJP = table.jenis
      this.listAsalProduk = table.asalproduk
      this.listKomp = table.komponentarif
      this.listSK = table.sk
    })
  }
  cari() {
    this.loadData()
  }

  loadData() {
    let rows = ''
    if (this.item.rows != undefined) {
      rows = this.item.rows
    }
    let namaProduk = ''
    if (this.item.namaProduk != undefined) {
      namaProduk = this.item.namaProduk
    }
    let kelas = ''
    if (this.item.kelas != undefined) {
      kelas = this.item.kelas.id
    }
    let jenispelayanan = ''
    if (this.item.jenispelayanan != undefined) {
      jenispelayanan = this.item.jenispelayanan.id
    }
    let sk = ''
    if (this.item.sk != undefined) {
      sk = this.item.sk.id
    }

    this.apiService.get('sysadmin/general/get-harga-pelayanan?rows=' + rows
      + '&namaproduk=' + namaProduk
      + '&klsid=' + kelas
      + '&jpid=' + jenispelayanan
      + '&skid=' + sk
    ).subscribe(table => {
      var data = table
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = 1 + i;
      }
      this.dataSource = data;
    })
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'produkidfk', header: 'ID', width: "100px" },
      { field: 'namaproduk', header: 'Nama Pelayanan', width: "250px" },
      { field: 'namakelas', header: 'Kelas', width: "180px" },
      { field: 'asalproduk', header: 'Asal Produk', width: "180px" },
      { field: 'jenispelayanan', header: 'Jenis Pelayanan', width: "180px" },
      { field: 'namask', header: 'SK', width: "150px" },
      { field: 'hargasatuan', header: 'Harga', width: "180px", isCurrency: true },
    ];
    this.columnGrid2 = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'komponentarif', header: 'Nama Komponen', width: "150px" },
      { field: 'hargasatuan', header: 'Harga', width: "100px", isCurrency: true },

    ];
    this.columnPak = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'nosk', header: 'Nomor SK', width: "150px" },
      { field: 'namask', header: 'Nama SK', width: "200px" },
      { field: 'tglberlakuawal', header: 'Tgl Berlaku Awal', width: "150px" },
      { field: 'tglberlakuakhir', header: 'Tgl Berlaku Akhir', width: "150px" },
    ];
  }

  clearFilter() {
    this.searchText = "";
  }

  clearFilter2() {
    this.searchText2 = "";
  }

  tambah2() {
    this.add = {};
    this.data2 = []
    this.dataSource2 = []
    this.popUp = true
  }
  tambah() {
    if (this.add.komponen == undefined) {
      this.alertService.warn("Info", "Komponen harus di isi!")
      return;
    }
    if (this.add.hargakom == undefined) {
      this.alertService.warn("Info", "Harga harus di isi!")
      return;
    }




    var nomor = 0
    if (this.dataSource2 == undefined) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }
    var data: any = {};
    if (this.add.no != undefined) {
      for (var i = this.data2.length - 1; i >= 0; i--) {
        if (this.data2[i].no == this.add.no) {
          data.no = this.add.no
          data.komponenhargaidfk = this.add.komponen.id
          data.komponentarif = this.add.komponen.komponentarif
          data.hargasatuan = this.add.hargakom
          this.data2[i] = data;
          this.dataSource2 = this.data2
        }
      }

    } else {
      data = {
        no: nomor,
        komponenhargaidfk: this.add.komponen.id,
        komponentarif: this.add.komponen.komponentarif,
        hargasatuan: this.add.hargakom,

      }
      this.data2.push(data)
      this.dataSource2 = this.data2


    }
    var subTotal = 0;
    for (var i = this.data2.length - 1; i >= 0; i--) {
      subTotal = subTotal + parseFloat(this.data2[i].hargasatuan)
    }
    this.add.totalSubTotal = subTotal
    this.kosongkan();

  }
  kosongkan() {
    delete this.add.komponen
    delete this.add.hargakom
    delete this.add.no
  }
  hapusD(dataSelected) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataSelected.no) {

        this.data2.splice(i, 1);
        var subTotal = 0;
        for (var i = this.data2.length - 1; i >= 0; i--) {
          subTotal = subTotal + parseFloat(this.data2[i].hargasatuan)
          this.data2[i].no = i + 1
        }
        this.dataSource2 = this.data2
        this.add.totalSubTotal = subTotal
      }
    }

    this.kosongkan()
  }
  editD(dataSelected) {
    this.add.komponen = {
      id: dataSelected.komponenhargaidfk,
      komponentarif: dataSelected.komponentarif
    }
    this.add.hargakom = dataSelected.hargasatuan
    this.add.no = dataSelected.no
  }
  save() {
    if (this.add.produk == undefined) {
      this.alertService.warn("Info", " Pelayanan harus di isi!")
      return;
    }
    if (this.add.kelas == undefined) {
      this.alertService.warn("Info", "Kelas harus di isi!")
      return;
    }
    if (this.add.asalproduk == undefined) {
      this.alertService.warn("Info", "Asal Produk harus di isi!")
      return;
    }
    if (this.add.jenispelayanan == undefined) {
      this.alertService.warn("Info", "Jenis Pelayanan harus di isi!")
      return;
    }
    if (this.add.sk == undefined) {
      this.alertService.warn("Info", "SK harus di isi!")
      return;
    }
    if (this.data2.length == 0) {
      this.alertService.warn("Info", "Komponen harus di isi!")
      return;
    }

    let json = {
      'id': this.add.id ? this.add.id : '',
      'produkidfk': this.add.produk.id,
      'kelasidfk': this.add.kelas.id,
      'asalprodukidfk': this.add.asalproduk.id,
      'jenispelayananidfk': this.add.jenispelayanan.id,
      'suratkeputusanidfk': this.add.sk.id,
      'hargasatuan': this.add.totalSubTotal,
      'details': this.data2
    }
    this.apiService.post('sysadmin/general/save-harga-pelayanan', json).subscribe(e => {
      this.clear()
    })

  }
  batal() {
    this.kosongkan()
  }
  clear() {
    this.data2 = []
    this.kosongkan()
    this.popUp = false
    this.loadData()

  }
  hapus(e) {
    let json = {
      'id': e.id,
      'produkidfk': e.produkidfk,
      'kelasidfk': e.kelasidfk,
      'asalprodukidfk': e.asalprodukidfk,
      'jenispelayananidfk': e.jenispelayananidfk,
    }
    this.apiService.post('sysadmin/general/hapus-harga-pelayanan', json).subscribe(z => {
      this.loadData()
    })
  }
  confirm(event: Event, data) {
    this.confirmationService.confirm({
      target: event.target,
      message: 'Yakin mau hapus?',
      icon: 'pi pi-exclamation-triangle',
      acceptLabel: "Ya",
      rejectLabel: "Tidak",
      accept: () => {
        this.hapus(data)
      },
      reject: () => {
        //reject action
      }
    });
  }
  editPelayanan(e) {

    this.add.id = e.id
    this.add.produk = { id: e.produkidfk, namaproduk: e.namaproduk }
    this.add.kelas = { id: e.kelasidfk, namakelas: e.namakelas }
    this.add.asalproduk = { id: e.asalprodukidfk, asalproduk: e.asalproduk }
    this.add.jenispelayanan = { id: e.jenispelayananidfk, jenispelayanan: e.jenispelayanan }

    for (let x = 0; x < this.listSK.length; x++) {
      const element = this.listSK[x];
      if (element.id == e.suratkeputusanidfk) {
        this.add.sk = element
        break;
      }
    }
    for (let x = 0; x < e.details.length; x++) {
      const element = e.details[x];
      element.no = x + 1
    }
    this.data2 = e.details

    var subTotal = 0;
    for (var i = this.data2.length - 1; i >= 0; i--) {
      subTotal = subTotal + parseFloat(this.data2[i].hargasatuan)
      this.data2[i].no = i + 1
    }
    this.dataSource2 = this.data2
    this.add.totalSubTotal = subTotal
    this.popUp = true
  }
  filter(event) {
    let query = event.query;
    this.apiService.get("sysadmin/general/get-produk?namaproduk=" + query
    ).subscribe(re => {
      this.listProduk = re;
    })
  }

  tambahPak() {
    this.clearSK()
    this.popUpPak = true
  }
  clearSK() {
    delete this.item.nosk
    delete this.item.namask
    delete this.item.tglberlakuawal
    delete this.item.tglberlakuakhir
    delete this.item.idP
  }
  savePak() {
    if (!this.item.namask) {
      this.alertService.warn('Info', 'Nama SK harus di isi')
      return
    }
    let json = {
      "id": this.item.idP ? this.item.idP : '',
      "namask": this.item.namask,
      "nosk": this.item.nosk ? this.item.nosk : null,
      "tglberlakuawal": this.item.tglberlakuawal ? moment(this.item.tglberlakuawal).format('YYYY-MM-DD') : null,
      "tglberlakuakhir": this.item.tglberlakuakhir ? moment(this.item.tglberlakuakhir).format('YYYY-MM-DD') : null,
      "method": 'save',
    }
    this.apiService.post('sysadmin/general/save-sk', json).subscribe(e => {
      this.clearSK()
      this.popUpPak = false
      this.loadSK()
      this.loadCombo()
    })

  }
  loadSK() {
    this.apiService.get('sysadmin/general/get-sk').subscribe(e => {
      for (let i = 0; i < e.length; i++) {
        const element = e[i];
        if (element.tglberlakuakhir) {
          element.tglberlakuakhir = moment(new Date(element.tglberlakuakhir)).format('YYYY-MM-DD')
        }
        if (element.tglberlakuawal) {
          element.tglberlakuawal = moment(new Date(element.tglberlakuawal)).format('YYYY-MM-DD')
        }
        element.no = i + 1
      }
      this.listSK = e
      this.dataSourcePak = e
    })
  }
  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadData()
    } else if (e.index == 1) {
      this.loadSK()
    } else {
      // this.loadData()
    }

  }
  editPak(e) {
    this.item.namask = e.namask
    this.item.nosk = e.nosk
    if (e.tglberlakuawal)
      this.item.tglberlakuawal = new Date(e.tglberlakuawal)
    if (e.tglberlakuakhir)
      this.item.tglberlakuakhir = new Date(e.tglberlakuakhir)
    this.item.idP = e.id
    this.popUpPak = true
  }
  setAktif(e, data) {
    let isChecked = e.checked;
    let json = {
      id: data.id,
      aktif: isChecked,

      method: 'aktif',
    }
    this.apiService.post('sysadmin/general/save-sk', json).subscribe(e => {
      this.loadSK()
    })
  }
  delPak(e) {
    let json = {
      id: e.id,
      method: 'delete',
    }
    this.apiService.post('sysadmin/general/save-sk', json).subscribe(e => {
      this.loadSK()
      this.loadCombo()
    })

  }
}
