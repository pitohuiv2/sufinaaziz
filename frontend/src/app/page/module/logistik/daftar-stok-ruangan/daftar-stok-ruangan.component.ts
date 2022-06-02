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
  selector: 'app-daftar-stok-ruangan',
  templateUrl: './daftar-stok-ruangan.component.html',
  styleUrls: ['./daftar-stok-ruangan.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarStokRuanganComponent implements OnInit {
  page: number;
  rows: number;
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
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
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
      // { field: 'qtyOnHand', header: 'Stok Onhand', width: "140px" },
      // { field: 'qtyorder', header: 'Stok Order', width: "140px" },
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

    this.apiService.get('logistik/get-stok-ruangan-detail?'
      + 'kelompokprodukid=' + kelBarang
      + '&detailJenisProdukId=' + detailJenisBarang
      + '&jeniskprodukid=' + jenBarang
      + '&namaproduk=' + namaBarang
      + '&ruanganfk=' + ruanganId
      + '&jmlRows=' + jmlRows).subscribe(table => {
        var data = table.detail;
        var datasOrder = table.detailorder;
        var subTotal = 0;
        var stok: any = 0;
        var total: any = 0;
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          subTotal = subTotal + parseFloat(element.qtyProduk);
          stok = stok + parseFloat(element.qtyProduk);
          element.total = parseFloat(element.qtyProduk) * parseFloat(element.harga);
          total = total + parseFloat(element.total);
          element.qtyorder = 0
          for (let e = 0; e < datasOrder.length; e++) {
            const elements = datasOrder[e];
            if (elements.objectprodukfk == element.kodeProduk) {
              if (element.qtyProduk > elements.qty) {
                element.qtyorder = elements.qty
                elements.qty = 0
              } else {
                element.qtyorder = element.qtyProduk
                elements.qty = elements.qty - element.qtyProduk
              }

            }
          }
          element.qtyOnHand = element.qtyProduk
          if (element.qtyorder != 0) {
            element.qtyOnHand = element.qtyProduk - element.qtyorder
          }
        }
        this.item.jumlahStok = parseFloat(stok).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,")
        this.item.totalHarga = parseFloat(total).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,")
        this.dataTable = data;
      })
  }

  cari() {
    this.loadData();
  }

  onRowSelect(event: any) {
    this.selected = event.data
  }

  popUpTglKadaluarsa() {
    if (this.selected != undefined) {
      this.item.tglKadaluarsa = moment(this.dateNow).format("YYYY-MM-DD HH:mm");
      this.pop_UbahTanggal = true;
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
  }
  popUpTglKadaluarsa2(e){
    this.selected =e
    this.popUpTglKadaluarsa()
  }
  popUpAdjusmentStok2(e){
    this.selected =e
    this.popUpAdjusmentStok()
  }
  batalUbahTgl() {
    this.item.tglKadaluarsa = moment(this.dateNow).format("YYYY-MM-DD HH:mm");
    this.pop_UbahTanggal = false;
  }

  simpanUbahTgl() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    if (this.item.tglKadaluarsa == undefined) {
      this.alertService.warn("Info", "Tgl Kadaluarsa Tidak Boleh Kosong!");
      return;
    }
    var objSave = {
      'norec_spd': this.selected.norec_spd,
      'produkfk': this.selected.kodeProduk,
      'nostruterimafk': this.selected.nostrukterimafk,
      'tanggal': moment(this.item.tglKadaluarsa).format('YYYY-MM-DD HH:mm')
    }

    this.apiService.post('logistik/update-tglkadaluarsa', objSave).subscribe(e => {
      this.apiService.postLog('Input / Ubah Tanggal Kadaluarsa', 'norec transaksi stok', this.selected.norec_spd,
        'Input / Ubah  Tanggal Kadaluarsa Pada Produk '
        + this.selected.namaProduk).subscribe(z => { })
      this.item.tglKadaluarsa = moment(this.dateNow).format("YYYY-MM-DD HH:mm");
      this.pop_UbahTanggal = false;
      this.loadData();
    })
  }

  popUpAdjusmentStok() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    this.item.kodeProduk = this.selected.kodeProduk;
    this.item.namaProduk = this.selected.namaProduk;
    this.item.qtyReal = this.selected.qtyProduk;
    this.pop_AdjusmentStok = true;
  }

  batalAdjusmentStok() {
    this.item.kodeProduk = undefined;
    this.item.namaProduk = undefined;
    this.item.qtyReal = undefined;
    this.item.QtyAjustment = undefined;
    this.pop_AdjusmentStok = false;
  }

  simpanAdjusmentStok() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
    if (this.item.QtyAjustment == undefined) {
      this.alertService.warn("Info", "Qty Adjusment Tidak Boleh Kosong!");
      return;
    }
    this.confirmationService.confirm({
      message: 'Apakah Anda Yakin Akan Melakukan Adjusment Stok?',
      header: 'Konfirmasi Adjusment Stok',
      icon: 'pi pi-info-circle',
      accept: () => {        
        this.confirmationService.close();
        this.saveAdjusment();
      },
      reject: (type) => {
        this.item.QtyAjustment = undefined;
        this.alertService.warn('Info, Konfirmasi', 'Adjusment Stok Dibatalkan!');
        this.confirmationService.close();
        return;
      }
    });
  }

  saveAdjusment() {

    var objSave = {
      'namaRuangan': this.item.dataRuangan.namaruangan,
      'ruanganfk': this.item.dataRuangan.id,
      'norec_spd': this.selected.norec_spd,
      'produkfk': this.selected.kodeProduk,
      'nostruterimafk': this.selected.nostrukterimafk,
      'qtyreal': this.item.qtyReal,
      'qtyad': parseFloat(this.item.QtyAjustment),
      'harga': parseFloat(this.selected.harga)
    }

    this.apiService.post('logistik/save-adjusment-stok', objSave).subscribe(e => {
      this.apiService.postLog('Adjusment Stok', 'norec transaksistok', this.selected.norec_spd,
        'Adjusment Stok Pada Produk '
        + this.selected.namaProduk).subscribe(z => { })
        this.item.kodeProduk = undefined;
        this.item.namaProduk = undefined;
        this.item.qtyReal = undefined;
        this.item.QtyAjustment = undefined;
        this.pop_AdjusmentStok = false;
        this.loadData();
    })
  }

}
