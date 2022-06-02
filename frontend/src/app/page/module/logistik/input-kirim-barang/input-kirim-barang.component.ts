import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-input-kirim-barang',
  templateUrl: './input-kirim-barang.component.html',
  styleUrls: ['./input-kirim-barang.component.scss'],
  providers: [ConfirmationService]
})
export class InputKirimBarangComponent implements OnInit {
  norec_data: any;
  norec_kirim: any;
  jenisData: any;
  params: any = {};
  item: any = {};
  listRuangan: any[];
  listRuanganAll: any[];
  listAsalProduk: any[];
  listJenisKirim: any[];
  listSatuan: any[] = [];
  listProduk: any[] = [];
  dataProdukDetail: any[] = [];
  columnGrid: any[] = [];
  dataSource: any[] = [];
  dataSelected: any
  data2: any[] = [];
  dataLogin: any;
  kelUser: any;
  dateNow: any;
  noTerima: any;
  hrg1: any = 0;
  tglkadaluarsa: any;
  statusTambah: boolean = true;
  disabledRuangan: boolean;
  isSimpan: any;
  maxDateValue:boolean
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
    this.disabledRuangan = false;
    this.isSimpan = false;
    this.dateNow = new Date();
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.item.tglKirim = this.dateNow;
    this.loadColumn();
    this.loadCombo();
    this.firstLoad();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadColumn() {
    this.columnGrid = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'produkfk', header: 'Kode Produk', width: "100px" },
      { field: 'namaproduk', header: 'Nama Produk', width: "180px" },
      { field: 'asalproduk', header: 'Asal Produk', width: "120px" },
      { field: 'satuanstandar', header: 'Satuan', width: "120px" },
      { field: 'jmlstok', header: 'Stok', width: "120px" },
      { field: 'qtyorder', header: 'Qty Order', width: "140px" },
      { field: 'jumlah', header: 'Qty Kirim', width: "140px" }
    ];
  }

  loadCombo() {
    this.apiService.get("logistik/get-combo-logistik").subscribe(table => {
      var dataCombo = table;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuangan = dataCombo.ruangfarmasi;
      }
      this.listJenisKirim = dataCombo.jeniskirim;
    });
    this.apiService.get("logistik/get-combo-distribusi").subscribe(e => {
      this.listAsalProduk = e.asalproduk;
    });
  }

  firstLoad() {
    this.route.params.subscribe(params => {
      this.params.norec_data = params['norec'];
      this.params.jenisdata = params['jenisdata'];
      this.jenisData = params['jenisdata'];
      if (this.jenisData == "editkirim") {
        this.norec_kirim = params['norec'];
        this.norec_data = "";
      } else {
        this.norec_data = params['norec'];
        this.norec_kirim = "";
      }
      this.loadData();
    });
  }

  loadData() {
    if (this.params.norec_data != "-") {
      if (this.jenisData == "kirimbarang") {
        this.apiService.get("logistik/get-detail-order-barang-ruangan?norecOrder=" + this.params.norec_data).subscribe(dataGet => {
          var datahead = dataGet.head;
          var detail = dataGet.detail;
          this.item.tglKirim = moment(this.dateNow).format('YYYY-MM-DD HH:mm');
          this.item.dataJenisKirim = { id: datahead.jeniskirimfk, jeniskirim: datahead.jeniskirim };
          this.item.ruangan = { id: datahead.objectruangantujuanfk, namaruangan: datahead.namaruangantujuan };
          this.listRuanganAll = [
            { id: datahead.objectruanganasalfk, namaruangan: datahead.namaruanganasal }
          ];
          this.item.dataRuanganAll = { id: datahead.objectruanganasalfk, namaruangan: datahead.namaruanganasal }
          this.item.keteranganKirim = datahead.keteranganorder;
          this.data2 = detail;
          var total: any = 0;
          for (let i = 0; i < this.data2.length; i++) {
            const element = this.data2[i];
            element.no = i + 1;
            if (element.nilaikonversi == undefined) {
              element.nilaikonversi = 1;
            }
            total = total + parseFloat(element.total)
          }
          this.dataSource = this.data2;
          this.item.totalSubTotal = this.formatRupiah(total, "Rp.")
          this.disabledRuangan = true;
        });
      } else if (this.jenisData == "editkirim") {
        this.apiService.get("logistik/get-detail-kirim-barang-ruangan?norec=" + this.params.norec_data).subscribe(dataGet => {
          var datahead = dataGet.head;
          var detail = dataGet.detail;
          this.item.nomorKirim = datahead.nokirim;
          this.item.tglKirim = moment(this.dateNow).format('YYYY-MM-DD HH:mm');
          this.item.dataJenisKirim = { id: datahead.jeniskirimfk, jeniskirim: datahead.jeniskirim };
          this.item.ruangan = { id: datahead.ruanganidfk, namaruangan: datahead.namaruanganasal };
          this.listRuanganAll = [
            { id: datahead.ruangantujuanidfk, namaruangan: datahead.namaruangantujuan }
          ];
          this.item.dataRuanganAll = { id: datahead.ruangantujuanidfk, namaruangan: datahead.namaruangantujuan }
          this.item.keteranganKirim = datahead.keterangan;
          this.data2 = detail;
          var total: any = 0;
          for (let i = 0; i < this.data2.length; i++) {
            const element = this.data2[i];
            element.no = i + 1;
            if (element.nilaikonversi == undefined) {
              element.nilaikonversi = 1;
            }
            total = total + parseFloat(element.total)
          }
          this.dataSource = this.data2;
          this.item.totalSubTotal = this.formatRupiah(total, "Rp.")
          this.disabledRuangan = true;
        });
      }
    }
  }

  filterRuangan(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-ruangan-part?namaruangan=" + query)
      .subscribe(re => {
        this.listRuanganAll = re;
      })
  }

  filterProduk(event) {
    let query = event.query;
    this.apiService.get("logistik/get-produk-distribusi?namaproduk=" + query)
      .subscribe(re => {
        this.listProduk = re;
      })
  }

  getSatuan() {
    if (this.item.produk.id == undefined) return

    this.GETKONVERSI()
  }

  GETKONVERSI() {    
    this.listSatuan = this.item.produk.konversisatuan
    if (this.listSatuan.length == 0) {
      this.listSatuan = ([{ ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar }]);
    }
    this.item.satuan = { ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar };
    this.item.nilaiKonversi = 1;
    if (this.item.ruangan == undefined) {
      return;
    }
    this.statusTambah = false
    this.apiService.get("general/get-produkdetail-general?" +
      "produkfk=" + this.item.produk.id +
      "&ruanganfk=" + this.item.ruangan.id).subscribe(dat => {
        this.dataProdukDetail = dat.detail;        
        this.item.stok = dat.jmlstok / this.item.nilaiKonversi
        this.onChangeKonversi()
        if (this.dataProdukDetail.length > 0) {
          this.tglkadaluarsa = moment(this.dataProdukDetail[0].tglkadaluarsa).format("YYYY-MM-DD HH:mm");
          this.listAsalProduk = [{
            id: this.dataProdukDetail[0].objectasalprodukfk, asalproduk: this.dataProdukDetail[0].asalproduk
          }]
          this.item.asal = this.listAsalProduk[0];
          if (this.dataSelected != undefined) {            
            this.item.nilaiKonversi = this.dataSelected.nilaikonversi;
            // this.item.stok = this.dataSelected.jmlstok;
            this.item.jumlah = this.dataSelected.jumlah;
            this.item.jumlahOrder = this.dataSelected.qtyorder
            this.item.hargaSatuan = this.dataSelected.hargasatuan;
            this.item.harganetto = this.dataSelected.harganetto;
            this.item.hargaTotal = this.dataSelected.total;
            this.item.hargadiskon = 0;
          }
        }
        this.statusTambah = true
        this.gettotal();
      });
  }

  onChangeKonversi() {
    if (this.item.stok > 0) {
      this.item.stok = parseFloat(this.item.stok) * parseFloat(this.item.nilaiKonversi);
      this.item.jumlah = 1;
      this.item.jumlahOrder = 0;
      this.item.hargaSatuan = 0;
      this.item.harganetto = 0;
      this.item.hargaTotal = 0;
      this.item.hargadiskon = 0;
    }
  }

  getNilaiKonversi() {    
    this.item.nilaiKonversi = this.item.satuan.nilaikonversi
  }

  onChangeQty(e) {
    this.item.jumlah = e.value
    this.gettotal()
  }

  gettotal() {
    var ada = false;
    for (var i = 0; i < this.dataProdukDetail.length; i++) {
      ada = false
      if (this.item.jumlah * parseFloat(this.item.nilaiKonversi) > 0) {
        this.hrg1 = Math.round(parseFloat(this.dataProdukDetail[i].hargajual) * parseFloat(this.item.nilaiKonversi))
        this.item.hargaSatuan = parseFloat(this.hrg1)
        this.item.harganetto = this.dataProdukDetail[i].harganetto
        this.item.hargaTotal = (this.item.jumlah * (this.hrg1))
        this.item.hargadiskon = 0;
        this.noTerima = this.dataProdukDetail[i].norec
        ada = true;
        break;
      }
    }
    if (ada == false) {
      this.item.hargaSatuan = 0
      this.item.total = 0
      this.noTerima = ''
    }
    if (this.item.jumlah == 0) {
      this.item.hargaSatuan = 0
      this.item.harganetto = 0
    }
  }

  Kosongkan() {
    this.dataSelected = undefined;
    delete this.item.produk;
    delete this.item.satuan;
    delete this.item.nilaiKonversi;
    this.item.no = undefined;
    this.item.stok = 0
    this.hrg1 = 0;
    this.item.jumlah = 0;
    this.item.jumlahOrder = 0;
    this.item.hargaSatuan = 0;
    this.item.harganetto = 0
    this.item.hargadiskon = 0;
    this.item.total = 0;
    this.item.hargaTotal = 0;
    this.noTerima = undefined;
    this.tglkadaluarsa = undefined;
    this.listAsalProduk = undefined;
    this.item.asal = undefined;
  }

  batal() {
    this.Kosongkan();
  }

  editD(dataSelected) {
    if (this.statusTambah == false)
      return
    var dataProduk = [];
    this.dataSelected = dataSelected
    this.item.no = dataSelected.no
    this.item.asal = { id: dataSelected.asalprodukfk, asalproduk: dataSelected.asalproduk }
    this.apiService.get("logistik/get-produk-distribusi?idproduk=" + dataSelected.produkfk)
      .subscribe(re => {
        this.listProduk = re;
        for (var i = this.listProduk.length - 1; i >= 0; i--) {
          if (this.listProduk[i].id == dataSelected.produkfk) {
            dataProduk = this.listProduk[i]
            break;
          }
        }
        this.item.produk = dataProduk
        this.GETKONVERSI()
      })
  }

  hapusD(dataSelected) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataSelected.no) {
        this.data2.splice(i, 1);
        var subTotal = 0;
        var totItem: any = 0;
        for (var i = this.data2.length - 1; i >= 0; i--) {
          subTotal = subTotal + parseFloat(this.data2[i].total)
          totItem = totItem + i;
          this.data2[i].no = i + 1
        }
        this.dataSource = this.data2
        this.item.totalSubTotal = this.formatRupiah(subTotal, "Rp.")
        this.item.TotalItem = parseFloat(totItem);
      }
    }
    this.Kosongkan();
  }

  batalGrid() {
    this.Kosongkan();
    this.data2 = [];
    this.dataSource = undefined;
    this.isSimpan = false;
  }

  Kembali() {
    window.history.back();
  }

  tambah() {

    if (this.statusTambah == false) {
      return
    }

    if (this.item.dataRuanganAll == undefined) {
      this.alertService.error("Info", "Data Ruangan Tujuan Masih Kosong!");
      return;
    }

    if (this.item.produk == undefined) {
      this.alertService.error("Info", "Pilih Produk terlebih dahulu!!")
      return;
    }
    if (this.item.satuan == undefined) {
      this.alertService.error("Info", "Pilih Satuan terlebih dahulu!!")
      return;
    }

    if (this.item.jumlah == 0) {
      this.alertService.error("Info", "Jumlah harus di isi!")
      return;
    }

    if (parseFloat(this.item.stok) == 0) {
      this.alertService.error("Info", "Stok tidak ada!")
      return;
    }

    if (this.item.jumlah > parseFloat(this.item.stok)) {
      this.alertService.error("Info", "Jumlah Order Melebihi Stok!")
      return;
    }

    if (this.noTerima == '') {
      this.item.jumlah = 0
      this.alertService.error("Info", "Jumlah harus di isi!")
      return;
    }

    var nomor = 0
    if (this.dataSource == undefined) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }
    var data: any = {};
    this.disabledRuangan = true;
    if (this.item.no != undefined) {
      for (var i = this.data2.length - 1; i >= 0; i--) {
        if (this.data2[i].no == this.item.no) {
          data.no = this.item.no
          data.hargajual = String(this.item.hargaSatuan)
          data.jenisobatfk = null
          data.stock = String(this.item.stok)
          data.harganetto = String(this.item.harganetto)
          data.nostrukterimafk = this.noTerima
          data.ruanganfk = this.item.ruangan.id
          data.asalprodukfk = this.item.asal.id
          data.asalproduk = this.item.asal.asalproduk
          data.produkfk = this.item.produk.id
          data.kdproduk = this.item.produk.id
          data.namaproduk = this.item.produk.namaproduk
          data.nilaikonversi = this.item.nilaiKonversi
          data.satuanstandarfk = this.item.satuan.ssid
          data.satuanstandar = this.item.satuan.satuanstandar
          data.satuanviewfk = this.item.satuan.ssid
          data.satuanview = this.item.satuan.satuanstandar
          data.jmlstok = String(this.item.stok)
          data.jumlah = this.item.jumlah
          data.qtyorder = this.item.jumlahOrder != undefined ? this.item.jumlahOrder : 0,
            data.hargasatuan = String(this.item.hargaSatuan)
          data.hargadiscount = String(this.item.hargadiskon)
          data.total = this.item.hargaTotal

          this.data2[i] = data;
          this.dataSource = this.data2
          var Total: any = 0;
          var totItem: any = 0;
          for (var i = this.data2.length - 1; i >= 0; i--) {
            Total = Total + parseFloat(this.data2[i].total)
            totItem = totItem + i;
          }
          this.item.totalSubTotal = this.formatRupiah(Total, "Rp.");
          this.item.TotalItem = parseFloat(totItem);
        }
      }

    } else {
      data = {
        no: nomor,
        hargajual: String(this.item.hargaSatuan),
        jenisobatfk: null,
        stock: String(this.item.stok),
        harganetto: String(this.item.harganetto),
        nostrukterimafk: this.noTerima,
        ruanganfk: this.item.ruangan.id,
        asalprodukfk: this.item.asal.id,
        asalproduk: this.item.asal.asalproduk,
        produkfk: this.item.produk.id,
        kdproduk: this.item.produk.id,
        namaproduk: this.item.produk.namaproduk,
        nilaikonversi: this.item.nilaiKonversi,
        satuanstandarfk: this.item.satuan.ssid,
        satuanstandar: this.item.satuan.satuanstandar,
        satuanviewfk: this.item.satuan.ssid,
        satuanview: this.item.satuan.satuanstandar,
        jmlstok: String(this.item.stok),
        jumlah: this.item.jumlah,
        qtyorder: this.item.jumlahOrder != undefined ? this.item.jumlahOrder : 0,
        hargasatuan: String(this.item.hargaSatuan),
        hargadiscount: String(this.item.hargadiskon),
        total: this.item.hargaTotal
      }
      this.data2.push(data)
      this.dataSource = this.data2;
      var total: any = 0;
      var totItem: any = 0;
      for (var i = this.data2.length - 1; i >= 0; i--) {
        total = total + parseFloat(this.data2[i].total);
        totItem = totItem + i;
      }
      this.item.totalSubTotal = this.formatRupiah(total, "Rp.")
      this.item.TotalItem = parseFloat(totItem);
    }
    this.Kosongkan()
  }

  save() {
    if (this.item.ruangan == undefined) {
      this.alertService.error("Info", "Pilih Ruanganan Pengirim!")
      return
    }
    if (this.item.dataRuanganAll == undefined) {
      this.alertService.error("Info", "Pilih Ruanganan Pengorder!")
      return
    }
    if (this.item.dataJenisKirim == undefined) {
      alert("Pilih Jenis Kiriman!!")
      return
    }
    if (this.item.keteranganKirim == undefined) {
      alert("Keterangan Tidak Boleh Kosong!!")
      return
    }
    if (this.data2.length == 0) {
      alert("Pilih Produk terlebih dahulu!!")
      return
    }
    this.isSimpan = true;
    var keterangan = 'Kirim Barang';
    if (this.item.keteranganKirim != undefined || this.item.keteranganKirim != '') {
      keterangan = this.item.keteranganKirim
    }

    var strukkirim = {
      objectpegawaipengirimfk: this.dataLogin.pegawai.id,
      objectruanganfk: this.item.ruangan.id,
      objectruangantujuanfk: this.item.dataRuanganAll.id,
      jenispermintaanfk: this.item.dataJenisKirim.id,
      keteranganlainnyakirim: keterangan,
      qtydetailjenisproduk: 0,
      qtyproduk: this.data2.length,
      tglkirim: moment(this.item.tglKirim).format('YYYY-MM-DD HH:mm:ss'),
      totalhargasatuan: 0,
      norecOrder: this.norec_data,
      noreckirim: this.norec_kirim,
      norec_apd: 0
    }
    var objSave =
    {
      strukkirim: strukkirim,
      details: this.data2
    }

    this.apiService.post('logistik/save-kirim-barang-ruangan', objSave).subscribe(dataSave => {
      this.apiService.postLog('Simpan Kirim Barang Ruangan', 'Norec transaksikirim', dataSave.data.norec,
        'Simpan Kirim Barang Ruangan Dengan Noorder - ' + dataSave.data.nokirim).subscribe(res => { })
      this.Kosongkan();
      this.data2 = []
      this.dataSource = undefined
      window.history.back();
    });

  }

}
