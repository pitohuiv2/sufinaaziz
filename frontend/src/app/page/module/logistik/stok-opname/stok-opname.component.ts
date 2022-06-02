import { Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { FileUpload } from 'primeng/fileupload';
import * as XLSX from 'xlsx';
@Component({
  selector: 'app-stok-opname',
  templateUrl: './stok-opname.component.html',
  styleUrls: ['./stok-opname.component.scss'],
  providers: [ConfirmationService]
})
export class StokOpnameComponent implements OnInit {

  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {}
  dataLogin: any;
  kelUser: any;
  dateNow: any;
  listRuangan: any[];
  listDetailJenisProduk: any[];
  listJenisBarang: any[];
  listKelompokBarang: any[];
  pop_UbahTanggal: boolean;
  pop_AdjusmentStok: boolean;
  isTeuAya: boolean
  dataSourceTeuAya: any[]
  isSimpan: boolean = true
  dataImport: any[]
  dataSourceGagal: any[]
  totalRecords: number = 0
  @ViewChild('fileUpload') fileUpload: any;
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private helper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private state: Router,
  ) {

  }

  ngOnInit(): void {
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.jmlRows = 10;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'noTerima', header: 'No Terima', width: "140px" },
      { field: 'kodeProduk', header: 'Kode Produk', width: "140px" },
      { field: 'namaProduk', header: 'Nama Produk', width: "300px" },
      { field: 'asalProduk', header: 'Asal Produk', width: "180px" },
      { field: 'qtyProduk', header: 'Stok', width: "120px" },
      { field: 'qtyOnHand', header: 'Stok Onhand', width: "140px" },
      { field: 'qtyorder', header: 'Stok Order', width: "140px" },
      { field: 'satuanStandar', header: 'Satuan', width: "120px" },
      { field: 'tglKadaluarsa', header: 'Tgl Exp', width: "140px" },
      { field: 'harga', header: 'Harga', width: "140px", isCurrency: true },
      { field: 'noBatch', header: 'No Batch', width: "140px" },
    ];
    this.loadDataCombo();
  }

  loadDataCombo() {
    this.apiService.get("logistik/get-combo-logistik").subscribe(table => {
      var dataCombo = table;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuangan = dataCombo.ruangfarmasi;
      }
      this.item.dataRuangan = this.listRuangan[0];
      // this.loadData()
    });
    this.apiService.get("logistik/get-combo-detailjenisproduk").subscribe(table => {
      var dataCombo = table;
      this.listDetailJenisProduk = dataCombo.detailjenisproduk;

    });
  }

  isiJenisProduk() {
    if (this.item.dataDjenisProduk != undefined) {
      this.listJenisBarang = this.item.dataDjenisProduk.jenisproduk;
    }
  }

  isiKelompokProduk() {
    if (this.item.dataJenisBarang != undefined) {
      this.listKelompokBarang = this.item.dataJenisBarang.kelompokproduk;
    }
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadData() {
    var detailJenisBarang, kelBarang, jenBarang, ruanganId, namaBarang;
    ruanganId = "";
    if (this.item.dataRuangan != undefined) {
      ruanganId = this.item.dataRuangan.id
    }
    detailJenisBarang = "";
    if (this.item.dataDjenisProduk != undefined) {
      detailJenisBarang = this.item.dataDjenisProduk.id
    }
    kelBarang = "";
    if (this.item.dataKelompokBarang != undefined) {
      kelBarang = this.item.dataKelompokBarang.id
    }
    jenBarang = "";
    if (this.item.dataJenisBarang != undefined) {
      jenBarang = this.item.dataJenisBarang.id
    }
    namaBarang = "";
    if (this.item.namaProduk != undefined) {
      namaBarang = this.item.namaProduk
    }

    var jmlRows = "";
    if (this.item.jmlRows != undefined) {
      jmlRows = this.item.jmlRows
    }
    this.dataTable = []
    this.apiService.get('logistik/get-stok-ruangan-so?'
      + 'kelompokprodukid=' + kelBarang
      + '&detailJenisProdukId=' + detailJenisBarang
      + '&jeniskprodukid=' + jenBarang
      + '&namaproduk=' + namaBarang
      + '&ruanganfk=' + ruanganId
      + '&jmlRows=' + jmlRows).subscribe(table => {
        var data = table.detail;
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          // element.no = i + 1;
          element.stokReal = element.qtyProduk
        }
        this.totalRecords = data.length
        this.dataTable = data;
      })
  }

  cari() {
    this.loadData();
  }

  onChange(item, e) {
    item.selisih = e - item.qtyProduk;
  }
  save() {
    if (this.item.dataRuangan == undefined) {
      this.alertService.warn('Info', "Pilih ruangan dulu")
      return
    }
    var dataArray = [];
    for (var i = this.dataTable.length - 1; i >= 0; i--) {
      if (this.dataTable[i].selisih != undefined)
        dataArray.push({
          "produkfk": this.dataTable[i].kodeProduk,
          "stokSistem": this.dataTable[i].qtyProduk,
          "stokReal": this.dataTable[i].stokReal,
          "selisih": this.dataTable[i].selisih
        });
    }

    if (dataArray.length !== 0) {
      var objSave = {
        "ruanganId": this.item.dataRuangan.id,
        "namaRuangan": this.item.dataRuangan.namaruangan,
        "tglClosing": moment(new Date()).format('YYYY-MM-DD HH:mm:ss'), //DateHelper.getPeriodeFormatted(this.item.tanggal),
        "stokProduk": dataArray
      }
      this.isSimpan = false
      this.apiService.post('logistik/save-data-stock-opname', objSave).subscribe(e => {
        this.isSimpan = true
        var norecSC = e.noSO
        if (norecSC != undefined)

          this.apiService.postLog('Stok Opname', 'norec Struk Closing', norecSC.norec, 'Stok Opname dengan No Closing '
            + norecSC.noclosing + ' di ruangan ' + this.item.dataRuangan.namaruangan).subscribe(res => { })

        var datas = e.databarangtaktersave;
        for (var i = datas.length - 1; i >= 0; i--) {
          datas[i].no = i + 1
        }
        if (datas.length > 0) {
          this.isTeuAya = true;
        } else {
          this.isTeuAya = false;
        }
        this.dataSourceTeuAya = datas
        this.cancel()
        this.cari()

      }, error => {
        this.isSimpan = true
      })
    } else {
      this.alertService.error("Info", 'Saldo Real barang belum di isi');
    }
  }
  exportExcel() {
    this.helper.exportExcel(this.dataTable, 'StokOpname')
    
  }
  exportExcel2() {
    this.helper.exportExcel(this.dataTable, 'StokOpnameGagal')
  }
 
  onSelect(ev, uploader: FileUpload) {
    let workBook = null;
    let jsonData = null;
    const reader = new FileReader();
    const file = ev.files[0];
    this.item.namaFile = file
    reader.onload = (event) => {
      const data = reader.result;
      workBook = XLSX.read(data, { type: 'binary' });
      jsonData = workBook.SheetNames.reduce((initial, name) => {
        const sheet = workBook.Sheets[name];
        initial[name] = XLSX.utils.sheet_to_json(sheet);
        return initial;
      }, {});
      const dataString = JSON.stringify(jsonData);

      this.dataImport = jsonData
      let objSave = {
        data: jsonData.data,
        ruanganfk: this.item.dataRuangan.id
      }
      this.dataTable = []
      this.apiService.post('logistik/get-stok-ruangan-so-from-fileexcel', objSave).subscribe(data => {
        this.isSimpan = data.save_cmd
        this.dataTable = data.detail
        this.totalRecords = this.dataTable.length
      })
    }
    reader.readAsBinaryString(file);
  }
  reset() {
    this.totalRecords = 0
    this.fileUpload.clear()
  }
  cancel() {
    this.totalRecords = 0
    this.fileUpload.clear()
    this.item.namaFile = undefined
  }
  daftarSo() {
    this.state.navigate(['daftar-so'])
  }
}
