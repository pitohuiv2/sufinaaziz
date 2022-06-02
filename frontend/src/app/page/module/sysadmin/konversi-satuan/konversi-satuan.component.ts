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
  selector: 'app-konversi-satuan',
  templateUrl: './konversi-satuan.component.html',
  styleUrls: ['./konversi-satuan.component.scss'],
  providers: [ConfirmationService]
})
export class KonversiSatuanComponent implements OnInit {
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  indexTab = 0
  selected: any;
  dataSource: any[];
  dataSourceSs: any[];
  dataSourceSr: any[];
  dataSourceJr: any[];
  column: any[];
  columnSs: any[];
  columnSr: any[];
  columnJr: any[];
  item: any = { layanan: [] }
  listProduk: any[] = [];
  listKelompokProduk: any[] = [];
  listSatuanAsal: any[] = [];
  listSatuanTujuan: any[] = [];
  listProdukInput: any[] = [];
  popUpTambah: boolean = false;
  popUpSatuan: boolean = false;
  popUpSatuanResep: boolean = false;
  popUpJr: boolean = false;
  isSimpan: boolean = false;
  disabledSA: boolean = true;
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
    this.dateNow = new Date();
    this.loadColumn();
    this.loadCombo();
    this.LoadSs();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadCombo() {
    this.apiService.get('sysadmin/general/get-combo-konversi').subscribe(data => {
      this.listKelompokProduk = data.kelompokproduk;
      this.listSatuanAsal = data.satuanstandar;
      this.listSatuanTujuan = data.satuanstandar;
    });
  }

  loadColumn() {
    this.columnSs = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
      { field: 'satuanstandar', header: 'Satuan Standar', width: "150px" },
    ];
    this.columnSr = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
      { field: 'satuanresep', header: 'Satuan Rsep', width: "150px" },
    ];
    this.columnJr = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'status', header: 'Status', width: "100px", isTag: true },
      { field: 'jenisracikan', header: 'Jenis Racikan', width: "150px" },
      // { field: 'jasaracikan', header: 'Tarif Jasa', width: "150px", isCurrency: true  },
    ];
    this.column = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'produkfk', header: 'Kode Produk', width: "140px" },
      { field: 'namaproduk', header: 'Nama Produk', width: "250px" },
      { field: 'satuanasal', header: 'Satuan Asal', width: "150px" },
      { field: 'satuantujuan', header: 'Satuan Tujuan', width: "150px" },
      { field: 'nilaikonversi', header: 'Nilai Konversi', width: "150px" },
    ];
  }

  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.LoadSs()
    } else if (e.index == 1) {
      this.LoadSr();
    }else if (e.index == 2) {
      
    }else if (e.index == 3) {
      this.LoadJr();
    }
  }

  //** SATUAN STANDAR */
  LoadSs() {
    this.apiService.get('sysadmin/general/get-master-data?table=satuanstandarmt').subscribe(e => {
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
      this.dataSourceSs = e.data;
    })
  }

  tambahTp() {
    this.item.IdSatuan = undefined;
    this.item.Satuan = undefined;
    this.popUpSatuan = true;
  }

  editTp(e) {
    this.item.IdSatuan = e.id
    this.item.Satuan = e.satuanstandar
    this.popUpSatuan = true
  }

  hapusTp(e) {
    var objSave = {
      'id': e.id,
      'satuanstandar': e.satuanstandar,
      'aktif': false,
      'table': 'satuanstandarmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {      
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveTp() {
    if (this.item.Satuan == undefined) {
      this.alertService.warn('Info', 'Tipe Pasien Belum Diisi')
      return
    }

    var objSave = {
      'id': this.item.IdSatuan != undefined ? this.item.IdSatuan : '',
      'satuanstandar': this.item.Satuan,
      'aktif': true,
      'table': 'satuanstandarmt'
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpSatuan = false
      this.handleChangeTab({ index: this.indexTab })
      this.item.IdSatuan = undefined;
      this.item.Satuan = undefined;
    }, error => {
      this.isSimpan = false
    })
  }
  //** END SATUAN STANDAR */

  //** SATUAN RESEP */
  LoadSr() {
    this.apiService.get('sysadmin/general/get-master-data?table=satuanresepmt').subscribe(e => {
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
      this.dataSourceSr = e.data;
    })
  }

  tambahSr() {
    this.item.IdSatuanResep = undefined;
    this.item.SatuanResep = undefined;
    this.popUpSatuanResep = true;
  }

  editSr(e) {
    this.item.IdSatuanResep = e.id
    this.item.SatuanResep = e.satuanresep
    this.popUpSatuanResep = true
  }

  hapusSr(e) {
    var objSave = {
      'id': e.id,
      'satuanresep': e.satuanresep,
      'aktif': false,
      'table': 'satuanresepmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.LoadSr();
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveSr() {
    if (this.item.SatuanResep == undefined) {
      this.alertService.warn('Info', 'Tipe Pasien Belum Diisi')
      return
    }

    var objSave = {
      'id': this.item.IdSatuanResep != undefined ? this.item.IdSatuanResep : '',
      'satuanresep': this.item.SatuanResep,
      'aktif': true,
      'table': 'satuanresepmt'
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpSatuanResep = false
      this.LoadSr();
      this.handleChangeTab({ index: this.indexTab })
      this.item.IdSatuanResep = undefined;
      this.item.SatuanResep = undefined;
    }, error => {
      this.isSimpan = false
    })
  }
  //** END SATUAN RESEP */

   //** JENIS RACIKAN */
   LoadJr() {
    this.apiService.get('sysadmin/general/get-master-data?table=jenisracikanmt').subscribe(e => {
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
      this.dataSourceJr = e.data;
    })
  }

  tambahJr() {
    this.item.IdJenisR = undefined;
    this.item.JenisR = undefined;
    this.item.tarifR = undefined;
    this.popUpJr = true;
  }

  editJr(e) {
    this.item.IdJenisR = e.id
    this.item.JenisR = e.jenisracikan
    this.item.tarifR = e.jasaracikan
    this.popUpJr = true
  }

  hapusJr(e) {
    var objSave = {
      'id': e.id,
      'jenisracikan': e.jenisracikan,      
      'jasaracikan': e.jasaracikan,
      'aktif': false,
      'table': 'jenisracikanmt',
    }

    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {      
      this.handleChangeTab({ index: this.indexTab })
    })
  }

  saveJr() {
    if (this.item.JenisR == undefined) {
      this.alertService.warn('Info', 'Tipe Pasien Belum Diisi')
      return
    }

    var objSave = {
      'id': this.item.IdJenisR != undefined ? this.item.IdJenisR : '',
      'jenisracikan': this.item.JenisR,
      'jasaracikan': this.item.tarifR,
      'aktif': true,
      'table': 'jenisracikanmt'
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-master-data', objSave).subscribe(e => {
      this.isSimpan = false
      this.popUpJr = false
      this.handleChangeTab({ index: this.indexTab })
      this.item.IdJenisR = undefined;
      this.item.JenisR = undefined;
      this.item.tarifR = undefined;
    }, error => {
      this.isSimpan = false
    })
  }
  //** END JENIS RACIKAN */

  loadData() {
    var kelProduk = "";
    if (this.item.dataKelompokProduk != undefined) {
      kelProduk = "&kelProduk=" + this.item.dataKelompokProduk.id;
    }
    var idProduk = "";
    if (this.item.produk != undefined) {
      idProduk = "&idProduk=" + this.item.produk.id;
    }
    var namaProduk = "";
    if (this.item.namaProduk != undefined) {
      namaProduk = "&namaProduk=" + this.item.namaProduk;
    }

    this.apiService.get('sysadmin/general/get-data-produk-konversi?status=true' + kelProduk + idProduk + namaProduk).subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataSource = e.data
    })
  }

  cari() {
    this.loadData()
  }

  filterProduk(event) {
    if (this.item.dataKelompokProduk == undefined) {
      this.alertService.error("Info", "Kelompok Produk Belum Diisi!");
      return;
    }
    let query = event.query;
    this.apiService.get("logistik/get-combo-produk-penerimaan?idKelProduk=" + this.item.dataKelompokProduk.id + "&namaproduk=" + query)
      .subscribe(re => {
        this.listProduk = re;
      })
  }

  filterProdukInput(event) {
    if (this.item.InputKelompokProduk == undefined) {
      this.alertService.error("Info", "Kelompok Produk Belum Diisi!");
      return;
    }
    let query = event.query;
    this.apiService.get("logistik/get-combo-produk-penerimaan?idKelProduk=" + this.item.InputKelompokProduk.id + "&namaproduk=" + query)
      .subscribe(re => {
        this.listProdukInput = re;
      })
  }

  getSatuan() {
    if (this.item.Inputproduk.id == undefined) return
    this.apiService.get("sysadmin/general/get-data-satuan-konversi?idProduk=" + this.item.Inputproduk.id).subscribe(data => {
      var satuanAsal = [];
      var satuanTujuan = [];
      var nilai = 0;
      var konversi = data;
      for (let i = 0; i < konversi.length; i++) {
        const element = konversi[i];
        if (element.norec == null) {
          this.alertService.warn("Info", "Produk Belum Memiliki Konversi, Buatlah Konversi 1 ke 1 terbilih dahulu!");
          for (let j = 0; j < this.listSatuanAsal.length; j++) {
            const elementss = this.listSatuanAsal[j];
            if (element.satuanstandaridfk == elementss.id) {
              satuanAsal = elementss;
              satuanTujuan = elementss;
              nilai = 1;
              break;
            }
          }
        } else {
          for (let x = 0; x < this.listSatuanAsal.length; x++) {
            const elementss = this.listSatuanAsal[x];
            if (element.satuanstandaridfk == elementss.id) {
              satuanAsal = elementss;              
              break;
            }
          }          
        }
      }
      this.item.InputSatuanAsal = satuanAsal;
      this.item.InputSatuanTujuan = satuanTujuan;
      this.item.nilaikonversi = nilai;
    });
  }

  tambahAdd() {
    this.kosongkan();
    this.popUpTambah = true
  }

  kosongkan() {
    this.item.norecKs = undefined;
    this.item.InputKelompokProduk = undefined;
    this.item.Inputproduk = undefined;
    this.item.InputSatuanAsal = undefined;
    this.item.InputSatuanTujuan = undefined;
    this.item.nilaikonversi = undefined;
  }

  saveKonversi() {
    if (this.item.InputKelompokProduk == undefined) {
      this.alertService.error("Info", "Produk Belum Dipilih!");
      return;
    }

    if (this.item.Inputproduk == undefined) {
      this.alertService.error("Info", "Produk Belum Dipilih!");
      return;
    }
    if (this.item.InputSatuanAsal == undefined) {
      this.alertService.error("Info", "Satuan Asal Belum Dipilih!");
      return;
    }
    if (this.item.InputSatuanTujuan == undefined) {
      this.alertService.error("Info", "Satuan Tujuan Belum Dipilih!");
      return;
    }
    if (this.item.nilaikonversi == 0 || this.item.nilaikonversi < 0) {
      this.alertService.error("Info", "Nilai Konversi Tidak Boleh Nol!");
      return;
    }

    var objSave = {
      'norec': this.item.norecKs != undefined ? this.item.norecKs : '',
      'produkfk': this.item.Inputproduk.id,
      'satuanstandar_asal': this.item.InputSatuanAsal.id,
      'satuanstandar_tujuan': this.item.InputSatuanTujuan.id,
      'nilaikonversi': this.item.nilaikonversi,
      'kelompokprodukidfk': this.item.InputKelompokProduk.id,
    }

    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-konversi-satuan', objSave).subscribe(e => {
      this.isSimpan = false
      this.kosongkan();
      this.popUpTambah = false
      this.loadData();
    }, error => {
      this.isSimpan = false
    })
  }

  editKonversi(selected) {
    if (selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!");
      return;
    }
    var dataProduk = [];
    this.item.norecKs = selected.norec;
    this.item.InputKelompokProduk = { id: selected.kelompokprodukidfk, kelompokproduk: selected.kelompokproduk };
    this.item.InputSatuanAsal = { id: selected.satuanstandar_asal, satuanstandar: selected.satuanasal };
    this.item.InputSatuanTujuan = { id: selected.satuanstandar_tujuan, satuanstandar: selected.satuantujuan };
    this.item.nilaikonversi = selected.nilaikonversi;
    this.apiService.get("logistik/get-combo-produk-penerimaan?idproduk=" + selected.produkfk)
      .subscribe(re => {
        this.listProdukInput = re;
        for (var i = this.listProdukInput.length - 1; i >= 0; i--) {
          if (this.listProdukInput[i].id == selected.produkfk) {
            dataProduk = this.listProdukInput[i]
            break;
          }
        }
        this.item.Inputproduk = dataProduk
      })
    this.popUpTambah = true;
  }

  hapusKonversi(selected) {
    if (selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!");
      return;
    }

    var objSave = {
      'norec': selected.norec,
    }
    this.apiService.post('sysadmin/general/hapus-konversi-satuan', objSave).subscribe(e => {
      this.loadData();
    }, error => { })
  }

}
