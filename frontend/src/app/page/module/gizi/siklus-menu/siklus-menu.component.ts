import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-siklus-menu',
  templateUrl: './siklus-menu.component.html',
  styleUrls: ['./siklus-menu.component.scss'],
  providers: [ConfirmationService]
})
export class SiklusMenuComponent implements OnInit {
  item: any = {
    jmlRow: 100
  }
  rowGroupMetadata: any;
  column: any[]
  dataSource: any[]
  listJenisDiet: any[]
  listJenisWaktu: any[]
  listProduk: any[]
  listProdukCek: any[] = []
  produkDef: any[] = []
  listKategoriDiet: any[]
  pop_Tambah: boolean
  popUp: any = {
    layanan: [],
    kelas: []
  }
  listKelas: any = []
  listChecked: any[] = []
  searchText: string = "";
  selectedData:any
  isSimpan:boolean = false
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
    private confirmationService: ConfirmationService,
  ) { }
  ngOnInit(): void {
    this.column = [
      { field: 'no', header: 'No', width: "60px" },
      { field: 'sikluske', header: 'Siklus Ke', width: "80px" },
      { field: 'jeniswaktu', header: 'Jenis Waktu', width: "150px" },
      { field: 'namaproduk', header: 'Menu', width: "200px" },
      { field: 'namakelas', header: 'Kelas', width: "150px" },
      { field: 'jenisdiet', header: 'Jenis Diet', width: "150px" },
      { field: 'kategorydiet', header: 'Kategory Diet', width: "150px" },
    ]
    this.loadCombo()
    this.load()
  }
  loadCombo() {
    this.apiService.get('sysadmin/general/get-combo-gizi').subscribe(e => {
      this.listJenisDiet = e.jenisdiet
      this.listJenisWaktu = e.jeniswaktu
      this.produkDef = e.produk
      this.listProdukCek = e.produk
      this.listKelas = e.kelas
      this.listKategoriDiet = e.kategorydiet
    })
  }
  cari() {
    this.load()
  }
  load() {
    var kelasId = ""
    if (this.item.kelas != undefined) {
      kelasId = "&kelasId=" + this.item.kelas.id
    }
    var jenisDietId = ""
    if (this.item.jenisDiet != undefined) {
      jenisDietId = "&jenisDietId=" + this.item.jenisDiet.id
    }
    var jenisWaktuId = ""
    if (this.item.jenisWaktu != undefined) {
      jenisWaktuId = "&jenisWaktuId=" + this.item.jenisWaktu.id
    }
    var namaProduk = ""
    if (this.item.namaProduk != undefined) {
      namaProduk = "&namaProduk=" + this.item.namaProduk
    }
    var jmlRow = ""
    if (this.item.jmlRow != undefined) {
      jmlRow = "&jmlRow=" + this.item.jmlRow
    }
    var siklusKe = ""
    if (this.item.siklusKe != undefined) {
      siklusKe = "&siklusKe=" + this.item.siklusKe
    }
    this.apiService.get("sysadmin/general/get-siklus-gizi?"
      + siklusKe
      + kelasId
      + jenisDietId
      + jenisWaktuId
      + namaProduk
      + jmlRow
      + siklusKe
    ).subscribe(data => {
      for (var i = 0; i < data.data.length; i++) {
        data.data[i].no = i + 1
      }

      this.dataSource = data.data
      // this.updateRowGroupMetaData();
    })
  }
  onSort() {
    this.updateRowGroupMetaData();
}
  updateRowGroupMetaData() {
    this.rowGroupMetadata = {};

    if (this.dataSource) {
        for (let i = 0; i < this.dataSource.length; i++) {
            let rowData = this.dataSource[i];
            let sikluske = rowData.sikluske;
            
            if (i == 0) {
                this.rowGroupMetadata[sikluske] = { index: 0, size: 1 };
            }
            else {
                let previousRowData = this.dataSource[i - 1];
                let previousRowGroup = previousRowData.sikluske;
                if (sikluske === previousRowGroup)
                    this.rowGroupMetadata[sikluske].size++;
                else
                    this.rowGroupMetadata[sikluske] = { index: i, size: 1 };
            }
        }
    }
}
  tambah() {
    this.pop_Tambah = true
    this.popUp = {
      layanan: [],
      kelas: []
    }
  }
  clearFilter() {
    this.searchText = "";
  }
  clearSelection() {
    var arrobj = Object.keys(this.popUp.layanan)
    for (let x = 0; x < arrobj.length; x++) {
      const element2 = arrobj[x];
      this.popUp.layanan[element2] = false
    }
    this.getSelected()
  }
  getSelected() {
    if (this.popUp.layanan.length > 0) {
      var arrobj = Object.keys(this.popUp.layanan)
      for (var x = 0; x < arrobj.length; x++) {
        const element = arrobj[x];
        if (this.popUp.layanan[parseInt(element)] == true) {
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

    }
  }
  save() {

    if (this.popUp.siklusKe == undefined) {
      this.alertService.error('Info', 'Siklus Ke harus di isi')
      return
    }
    if (this.popUp.jenisDiet == undefined) {
      this.alertService.error('Info', 'Jenis Diet belum di pilih')
      return
    }
    if (this.popUp.jenisWaktu == undefined) {
      this.alertService.error('Info', 'Jenis Waktu belum di pilih')
      return
    }
    if (this.popUp.kelas.length == 0) {
      this.alertService.error('Info', 'Kelas belum di pilih')
      return
    }
    if (this.popUp.layanan.length == 0) {
      this.alertService.error('Info', 'Produk belum di pilih')
      return
    }
    var arrobj = Object.keys(this.popUp.layanan)

    var arrKls = Object.keys(this.popUp.kelas)

    var arraySave = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      if (this.popUp.layanan[parseInt(arrobj[i])] == true) {
        for (var ii = arrKls.length - 1; ii >= 0; ii--) {
          if (this.popUp.kelas[parseInt(arrKls[ii])] == true) {
            arraySave.push({
              "produkfk": arrobj[i],
              "kelasfk": arrKls[ii]
            })
          }
        }
      }
    }

    var objSave = {
      "sikluske": this.popUp.siklusKe,
      "objectjenisdietfk": this.popUp.jenisDiet.id,
      // "objectkelasfk": $scope.popUp.kelas.id,
      "objectjeniswaktufk": this.popUp.jenisWaktu.id,
      "objectkategoryprodukfk": this.popUp.kategory != undefined ? this.popUp.kategory.id : null,
      "objectbentukprodukfk": this.popUp.bentukProduk != undefined ? this.popUp.bentukProduk.id : null,
      "details": arraySave
    }
    this.isSimpan =true
    this.apiService.post('sysadmin/general/save-siklus-gizi', objSave).subscribe(e => {
      this.load();
      this.popUp = {
        layanan: [],
        kelas: []
      }
      this.isSimpan =false
      this.pop_Tambah = false
    })

  }
 
  hapusData(event: Event, data) {
    this.confirmationService.confirm({
      target: event.target,
      message: 'Yakin mau hapus?',
      icon: 'pi pi-exclamation-triangle',
      acceptLabel: "Ya",
      rejectLabel: "Tidak",
      accept: () => {
       
          this.apiService.post('sysadmin/general/delete-siklus-gizi', {
            'id': data.id
          }).subscribe(e => {
            this.load()
          })
      },
      reject: () => {
        //reject action
      }
    });
  }
}
