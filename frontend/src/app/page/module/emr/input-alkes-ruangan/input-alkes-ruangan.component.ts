import { RekamMedisComponent } from '../rekam-medis/rekam-medis.component';
import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService, TreeNode } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-input-alkes-ruangan',
  templateUrl: './input-alkes-ruangan.component.html',
  styleUrls: ['./input-alkes-ruangan.component.scss'],
  providers: [ConfirmationService]
})
export class InputAlkesRuanganComponent implements OnInit {
  indexTab: number;
  item: any = {
    tglresep: new Date(),
    tglresepAkhir: new Date(),
    aturanCheck: [],
    rke: 1,
  }
  skeleton: any = [];
  maxDateValue = new Date()
  disabledRuangan: boolean;
  isSimpan: any;
  columnRiwayat: any[];
  selectedData: any;
  headData: any = {};
  listRuangan: any[];
  listAsalProduk: any[];
  listProduk: any[];
  dataProdukDetail: any[] = [];
  listSatuan: any[] = [];
  statusTambah: boolean = true;
  dataSelected: any;
  listDataSigna = [
    { "id": 1, "nama": "P", 'isChecked': false },
    { "id": 2, "nama": "S", 'isChecked': false },
    { "id": 3, "nama": "Sr", 'isChecked': false },
    { "id": 4, "nama": "M", 'isChecked': false }

  ];
  hrg1: any = 0;
  columnGrid: any[] = [];
  showRacikanDose: boolean;
  listPenulisResep: any[];
  noTerima: any;
  dataSource: any[] = [];
  data2: any[] = [];
  diffDays: any;
  norecOrder: any = '';
  dataSourceRiwayat: any[];
  dataSourceResep: any[];
  columnResep: any[];
  dataLogin: any;
  constructor(
    public rekamMedis: RekamMedisComponent,
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.dataLogin = this.authService.getDataLoginUser();
    if (this.rekamMedis.header.norec == undefined) {
      var cache = this.cacheHelper.get('cacheEMR_qwertyuiop')
      if (cache != undefined) {
        cache = JSON.parse(cache)
        this.headData = cache
      }
    } else {
      this.headData = this.rekamMedis.header
    }
    if (this.headData.norec == undefined) {
      window.history.back()
    }
    this.dataLogin = this.authService.getDataLoginUser();
    this.item.idPegawaiLogin = this.authService.getDataLoginUser().pegawai.id;
    this.listPenulisResep = [{ id: this.authService.getDataLoginUser().pegawai.id, namalengkap: this.authService.getDataLoginUser().pegawai.namaLengkap }];
    this.item.penulisResep = this.listPenulisResep[0];
    this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
    this.loadDropdown()
    this.loadColumn()
    this.diffDays = 1
    var date1 = new Date(this.item.tglresep);
    var date2 = new Date(this.item.tglresepAkhir);
    this.diffDays = date2.getDate() - date1.getDate();
    this.diffDays = this.diffDays + 1
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadDropdown() {
    this.apiService.get('emr/get-combo-resep-emr').subscribe(e => {
      this.listAsalProduk = e.asalproduk;
      this.item.ruangan = e.ruanganfarmasi[1];
    })
  }

  loadColumn() {
    this.columnGrid = [
      { field: 'namaproduk', header: 'Produk', width: "120px" },
      { field: 'satuanstandar', header: 'Satuan', width: "100px" },
      { field: 'jumlah', header: 'Jumlah', width: "150px" },
      { field: 'keterangan', header: 'Keterangan', width: "150px" },
    ];
    this.columnRiwayat = [
      { field: 'tglinput', header: 'Tgl Input', width: "100px" },
      { field: 'noalkes', header: 'No Transaksi', width: "150px" },
      { field: 'namalengkap', header: 'Pegawai', width: "120px" },
      { field: 'namaruangan', header: 'Ruangan', width: "200px" }
    ];
  }

  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
    } else if (e.index == 1) {
      this.loadRiwayat()
    } else {
      // this.loadResep()
    }
  }

  loadRiwayat() {
    let params = 'noreg=' + this.headData.noregistrasi
    this.apiService.get("emr/get-daftar-detail-alkes?" + params).subscribe(e => {
      for (var i = e.length - 1; i >= 0; i--) {
        e[i].no = i + 1
      }
      this.dataSourceRiwayat = e
    });
  }

  filterProduk(event) {
    let query = event.query;
    this.apiService.get("emr/get-produk-resep?namaproduk=" + query)
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
      this.listSatuan = ([{ ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar }])
    }
    this.item.satuan = { ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar }
    this.item.nilaiKonversi = 1// this.item.satuan.nilaikonversi
    if (this.item.ruangan == undefined) {
      return;
    }

    this.statusTambah = false
    this.apiService.get("emr/get-produkdetail?" +
      "produkfk=" + this.item.produk.id +
      "&ruanganfk=" + this.item.ruangan.id).subscribe(dat => {

        this.dataProdukDetail = dat.detail;
        this.item.stok = dat.jmlstok / this.item.nilaiKonversi
        this.onChangeKonversi()
        if (dat.kekuatan == 0) {
          dat.kekuatan = 1
        }
        this.item.kekuatan = dat.kekuatan
        this.item.sediaan = dat.sediaan
        if (this.dataProdukDetail.length > 0)
          if (this.dataSelected != undefined) {
            // this.item.nilaiKonversi = this.dataSelected.nilaikonversi

            // this.item.stok = this.dataSelected.jmlstok //* this.item.nilaiKonversi 
            this.item.jumlah = this.dataSelected.jumlah
            this.item.hargaSatuan = this.dataSelected.hargasatuan
            // this.item.hargadiskon = this.dataSelected.hargadiscount
            this.item.total = this.dataSelected.total
            if (this.item.kekuatan != undefined && this.item.kekuatan != 0) {
              this.item.jumlahxmakan = (parseFloat(this.item.jumlah) / parseFloat(this.item.dosis)) * parseFloat(this.item.kekuatan)
            }
          }
        this.statusTambah = true
        this.gettotal();
      });
  }

  onChangeQty(e) {
    this.item.jumlah = e.value
    this.gettotal()
  }

  onChangeJmlXMakan(e) {
    if (this.item.kekuatan == undefined || this.item.kekuatan == 0) {
      this.item.kekuatan = 1
    }
    this.item.jumlah = (parseFloat(this.item.jumlahxmakan) * parseFloat(this.item.dosis)) / parseFloat(this.item.kekuatan)
  }

  onChangeKonversi() {
    this.item.stok = parseFloat(this.item.stok) * parseFloat(this.item.nilaiKonversi)
    this.item.jumlah = 1;
    this.item.hargaSatuan = 0;
    this.item.total = 0;
  }

  getNilaiKonversi() {
    this.item.nilaiKonversi = this.item.satuan.nilaikonversi
  }

  gettotal() {
    var ada = false;
    for (var i = 0; i < this.dataProdukDetail.length; i++) {
      ada = false
      if (this.item.jumlah * parseFloat(this.item.nilaiKonversi) > 0) {// <= parseFloat(dataProdukDetail[i].qtyproduk)) {
        this.hrg1 = Math.round(parseFloat(this.dataProdukDetail[i].hargajual) * parseFloat(this.item.nilaiKonversi))
        this.item.hargaSatuan = parseFloat(this.hrg1)
        this.item.total = (this.item.jumlah * (this.hrg1))//+tarifJasa
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
    }
  }

  kosongkan() {
    this.dataSelected = undefined
    delete this.item.produk
    delete this.item.satuan
    delete this.item.nilaiKonversi
    delete this.item.sediaan
    delete this.item.kekuatan
    this.item.stok = 0
    this.item.jumlah = 1
    this.item.no = undefined
    this.item.total = 0
    this.item.hargaSatuan = 0
    delete this.item.KeteranganPakai
  }

  batal() {
    this.kosongkan();
  }

  batalGrid() {
    this.kosongkan();
    this.data2 = [];
    this.dataSource = undefined;
  }

  tambah = function () {
    if (this.statusTambah == false) {
      return
    }

    if (this.item.jumlah == 0) {
      this.alertService.error("Info", "Jumlah harus di isi!")
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

    var KetPakai = "";
    if (this.item.KeteranganPakai) {
      KetPakai = this.item.KeteranganPakai;
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
          data.generik = null
          data.hargajual = this.item.hargaSatuan
          data.stock = this.item.stok
          data.harganetto = this.item.hargaSatuan
          data.nostrukterimafk = this.noTerima
          data.ruanganfk = this.item.ruangan.id
          data.produkfk = this.item.produk.id
          data.namaproduk = this.item.produk.namaproduk
          data.nilaikonversi = this.item.nilaiKonversi
          data.satuanstandarfk = this.item.satuan.ssid
          data.satuanstandar = this.item.satuan.satuanstandar
          data.satuanviewfk = this.item.satuan.ssid
          data.satuanview = this.item.satuan.satuanstandar
          data.jmlstok = this.item.stok
          data.jumlah = this.item.jumlah
          data.hargasatuan = this.item.hargaSatuan
          data.hargadiscount = 0
          data.total = this.item.total
          data.jumlahxmakan = this.item.jumlahxmakan
          data.keterangan = KetPakai

          this.data2[i] = data;
          this.dataSource = this.data2;
        }
      }

    } else {
      data = {
        no: nomor,
        generik: null,
        hargajual: this.item.hargaSatuan,
        stock: this.item.stok,
        harganetto: this.item.hargaSatuan,
        nostrukterimafk: this.noTerima,
        ruanganfk: this.item.ruangan.id,
        produkfk: this.item.produk.id,
        namaproduk: this.item.produk.namaproduk,
        nilaikonversi: this.item.nilaiKonversi,
        satuanstandarfk: this.item.satuan.ssid,
        satuanstandar: this.item.satuan.satuanstandar,
        satuanviewfk: this.item.satuan.ssid,
        satuanview: this.item.satuan.satuanstandar,
        jmlstok: this.item.stok,
        jumlah: this.item.jumlah,
        hargasatuan: this.item.hargaSatuan,
        hargadiscount: 0,
        total: this.item.total,
        jumlahxmakan: this.item.jumlahxmakan,
        keterangan: KetPakai,
      }
      this.data2.push(data)
      this.dataSource = this.data2
    }
    this.kosongkan();
  }

  hapusD(dataSelected) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataSelected.no) {
        this.data2.splice(i, 1);
        for (var i = this.data2.length - 1; i >= 0; i--) {
          this.data2[i].no = i + 1
        }
        this.dataSource = this.data2
      }
    }
    this.kosongkan()
  }

  editD(dataSelected) {
    if (this.statusTambah == false)
      return
    var dataProduk = [];
    this.dataSelected = dataSelected
    this.item.no = dataSelected.no
    this.item.jumlahxmakan = dataSelected.jumlahxmakan
    this.item.KeteranganPakai = dataSelected.keterangan
    this.item.asal = { id: dataSelected.asalprodukfk, asalproduk: dataSelected.asalproduk }

    this.apiService.get("emr/get-produk-resep?idproduk=" + dataSelected.produkfk)
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

  save() {
    if (this.data2.length == 0) {
      this.alertService.error("Info", "Pilih Produk terlebih dahulu!!")
      return
    }

    if (this.item.tglresep == undefined) {
      this.alertService.error("Info", "Tanggal Harus diisi!!")
      return
    }

    if (this.item.penulisResep == undefined) {
      this.alertService.error("Info", "Penginput Harus diisi!!")
      return
    }

    var strukorder = {
      norec: this.norecOrder,
      tglresep: moment(this.item.tglresep).format('YYYY-MM-DD HH:mm'),
      penulisresepfk: this.item.penulisResep.id,
      ruanganfk: this.item.ruangan.id,
      noregistrasifk: this.headData.norec,
      qtyproduk: this.data2.length,
      noruangan: this.item.noRuang != undefined ? this.item.noRuang : null,
    }

    var objSave = {
      strukorder: strukorder,
      orderfarmasi: this.data2
    }

    this.isSimpan = true
    this.apiService.post('emr/simpan-input-obat-alkes', { data: objSave }).subscribe(e => {
      this.item.resep == undefined
      this.norecOrder = ''
      this.apiService.postLog('Input Alkes Ruangan', 'Norec transaksialkestr', e.noresep.norec,
        'Simpan Input Alkes Ruangan No. Transaksi : ' + e.noresep.noalkes + ' dengan No Registrasi ' + this.headData.noregistrasi).subscribe(res => { })
      this.isSimpan = false
      this.batalGrid();
      this.disabledRuangan = false
      if (this.item.noRuang != undefined)
        this.cacheHelper.set('noRuangInputAlkes', this.item.noRuang)
      let params = this.headData.noregistrasi
    }, error => {
      this.isSimpan = false
    })
  }

  hapusAlkes(e) {
    if (e == undefined) {
      this.alertService.error("Info", 'Tidak Belum Dipilih')
      return
    }

    this.apiService.post('emr/hapus-input-obat-alkes', { norec: e.norec }).subscribe(e => {
      this.loadRiwayat();
    })
  }

  editAlkes(e) {
    this.apiService.get("emr/get-daftar-detail-alkes?noalkes=" + e.noalkes).subscribe(dat => {

      this.norecOrder = e.norec
      this.item.resep = e.noalkes
      this.item.ruangan = { id: e.ruanganidfk, namaruangan: e.namaruangan }
      this.item.penulisResep = { id: e.petugasidfk, namalengkap: e.namalengkap }
      this.item.tglresep = new Date(e.tglinput)
      for (let i = 0; i < e.details.length; i++) {
        const element = e.details[i];
        element.no = 1 + i;
      }

      this.data2 = e.details      
      this.dataSource = this.data2

      var subTotal = 0;
      for (var i = this.data2.length - 1; i >= 0; i--) {
        subTotal = subTotal + parseFloat(this.data2[i].total)
      }
      this.item.totalSubTotal = subTotal

      this.indexTab = 0
      this.handleChangeTab({ index: this.indexTab })
    });
  }

}
