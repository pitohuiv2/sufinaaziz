
// import { SwPush } from '@angular/service-worker';
import { WebNotificationService } from 'src/app/service/notif.service';

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
  selector: 'app-order-resep',
  templateUrl: './order-resep.component.html',
  styleUrls: ['./order-resep.component.scss']
})
export class OrderResepComponent implements OnInit {
  // isEnabled = this.swPush.isEnabled;
  isGranted = Notification.permission === 'granted';
  indexTab: number
  item: any = {
    tglresep: new Date(),
    tglresepAkhir: new Date(),
    aturanCheck: [],
    rke: 1,
  }
  skeleton: any = []
  maxDateValue = new Date()
  disabledRuangan: boolean
  isSimpan: any
  columnRiwayat: any[]
  selectedData: any
  headData: any = {}
  listJenisKemasan: any[]
  listRuangan: any[]
  listAsalProduk: any[]
  listsatuanresep: any[]
  listJenisRacikan: any[]
  listProduk: any[]
  dataProdukDetail: any[] = []
  listSatuan: any[] = []
  statusTambah: boolean = true
  dataSelected: any
  listDataSigna = [
    { "id": 1, "nama": "P", 'isChecked': false },
    { "id": 2, "nama": "S", 'isChecked': false },
    { "id": 3, "nama": "Sr", 'isChecked': false },
    { "id": 4, "nama": "M", 'isChecked': false }

  ];
  hrg1: any = 0
  columnGrid: any[] = []
  showRacikanDose: boolean
  listPenulisResep: any[]
  noTerima: any
  dataSource: any[] = []
  data2: any[] = []
  diffDays: any
  norecOrder: any = ''
  dataSourceRiwayat: any[]
  dataSourceResep: any[]
  columnResep: any[]
  pop_paket:boolean
  columnPaket:any
  dataSourcePaket:any[]
  constructor(public rekamMedis: RekamMedisComponent,
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,
    // private swPush: SwPush,
    // private webNotificationService: WebNotificationService
    ) { }
  ngAfterViewInit() {

  }
  ngOnInit(): void {
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
    // this.skeleton = this.loadSkeleton()
    this.item.idPegawaiLogin = this.authService.getDataLoginUser().pegawai.id
    this.listPenulisResep = [{ id: this.authService.getDataLoginUser().pegawai.id, namalengkap: this.authService.getDataLoginUser().pegawai.namaLengkap }]
    this.item.penulisResep = this.listPenulisResep[0]

    this.loadDropdown()
    this.loadColumn()
    this.diffDays = 1
    var date1 = new Date(this.item.tglresep);
    var date2 = new Date(this.item.tglresepAkhir);
    this.diffDays = date2.getDate() - date1.getDate();
    this.diffDays = this.diffDays + 1
  }

  loadColumn() {
    this.columnGrid = [
      { field: 'rke', header: 'R/Ke', width: "100px" },
      { field: 'jeniskemasan', header: 'Kemasan', width: "120px" },
      { field: 'jmldosis', header: 'Jml/Dosis', width: "120px" },
      { field: 'aturanpakai', header: 'Aturan Pakai', width: "120px" },
      { field: 'satuanresep', header: 'Satuan Resep', width: "120px" },
      { field: 'namaproduk', header: 'Produk', width: "120px" },
      { field: 'satuanstandar', header: 'Satuan', width: "100px" },
      { field: 'jumlah', header: 'Jumlah', width: "150px" },
      { field: 'hargasatuan', header: 'Harga', width: "150px", isCurrency: true },
      { field: 'total', header: 'Total', width: "150px", isCurrency: true },
      { field: 'keterangan', header: 'Keterangan', width: "150px" },
    ];
    this.columnRiwayat = [
      { field: 'tglorder', header: 'Tgl Order', width: "100px" },
      { field: 'noorder', header: 'No Order', width: "120px" },
      { field: 'namalengkap', header: 'Pegawai', width: "120px" },
      { field: 'namaruangan', header: 'Apotik', width: "200px" },
      { field: 'statusorder', header: 'Status', width: "100px" },
    ];
    this.columnResep = [
      { field: 'noresep', header: 'No Resep', width: "100px" },
      { field: 'tglpelayanan', header: 'Tgl Resep', width: "120px" },
      { field: 'tglorder', header: 'Tgl Order', width: "120px" },
      { field: 'noregistrasi', header: 'No Registrasi', width: "100px" },
      { field: 'dokter', header: 'Penulis Resep', width: "200px" },
      { field: 'namaruangan', header: 'Ruangan', width: "150px" },
      { field: 'namaruangandepo', header: 'Apotik', width: "150px" },
    ];

  }


  getSelected() {
    let jml = 0
    if (this.item.aturanCheck.length > 0) {
      var arrobj = Object.keys(this.item.aturanCheck)
      for (var x = 0; x < arrobj.length; x++) {
        const element = arrobj[x];
        if (this.item.aturanCheck[parseInt(element)] == true) {
          if (element == '1') {
            this.item.chkp = 1
          }
          if (element == '2') {
            this.item.chks = 1
          }
          if (element == '3') {
            this.item.chksr = 1
          }
          if (element == '4') {
            this.item.chkm = 1
          }
        } else {
          if (element == '1') {
            this.item.chkp = 0
          }
          if (element == '2') {
            this.item.chks = 0
          }
          if (element == '3') {
            this.item.chksr = 0
          }
          if (element == '4') {
            this.item.chkm = 0
          }
        }
      }

    }
    if (this.item.chkp == 1) {
      jml = jml + 1
    }
    if (this.item.chks == 1) {
      jml = jml + 1
    }
    if (this.item.chksr == 1) {
      jml = jml + 1
    }
    if (this.item.chkm == 1) {
      jml = jml + 1
    }
    this.item.aturanpakaitxt = jml + 'x1'
    if (jml == 0) {
      this.item.aturanpakaitxt = ''
    }
  }
  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }
  loadDropdown() {
    this.apiService.get('emr/get-combo-resep-emr').subscribe(e => {
      this.listRuangan = e.ruanganfarmasi;
      this.listJenisKemasan = e.jeniskemasan;
      this.listAsalProduk = e.asalproduk;
      this.listsatuanresep = e.satuanresep
      this.listJenisRacikan = e.jenisracikan;
      this.item.jenisKemasan = e.jeniskemasan[1];
      this.item.ruangan = e.ruanganfarmasi[1];
    })
  }
  clickRadio(e) {
    if (e.jeniskemasan == 'Racikan') {
      this.showRacikanDose = true
      delete this.item.jumlahxmakan
      delete this.item.dosis
      delete this.item.jenisRacikan
      delete this.item.sediaan
      delete this.item.kekuatan
    } else {
      this.showRacikanDose = false
    }
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
            this.item.nilaiKonversi = this.dataSelected.nilaikonversi

            this.item.stok = this.dataSelected.jmlstok //* this.item.nilaiKonversi 
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
    // debugger
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

    // if (this.item.stok > 0) {
    this.item.stok = parseFloat(this.item.stok) * parseFloat(this.item.nilaiKonversi)
    this.item.jumlah = 1//parseFloat(this.item.jumlah) / parseFloat(newValue)
    this.item.hargaSatuan = 0//hrg1 * parseFloat(newValue)
    // this.item.hargadiskon =0//hrgsdk * parseFloat(newValue)
    this.item.total = 0// parseFloat(newValue) * 
    // (hrg1-hrgsdk)
    // }

  }
  getNilaiKonversi() {
    this.item.nilaiKonversi = this.item.satuan.nilaikonversi
  }
  tambah = function () {
    if (this.statusTambah == false) {
      return
    }

    if (this.headData.isclosing == true) {
      this.alertService.error("Info", "Data Sudah Diclosing!");
      return;
    }

    if (this.item.jumlah == 0) {
      this.alertService.error("Info", "Jumlah harus di isi!")
      return;
    }

    if (this.item.jenisKemasan == undefined) {
      this.alertService.error("Info", "Pilih Jenis Kemasan terlebih dahulu!!")
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
    if (this.item.aturanpakaitxt == undefined) {
      this.alertService.error("Info", "Aturan Pakai Belum diisi!!")
      return;
    }
    let isOutOfStok = false

    var KetPakai = "";
    if (this.item.KeteranganPakai) {
      KetPakai = this.item.KeteranganPakai;
    }

    var dosis = 1;
    if (this.item.jenisKemasan.jeniskemasan == 'Racikan') {
      dosis = this.item.dosis
    }
    var jRacikan = null
    if (this.item.jenisRacikan != undefined) {
      jRacikan = this.item.jenisRacikan.id
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

          data.noregistrasifk = this.headData.norec//this.item.noRegistrasi
          //data.tglregistrasi = this.item.tglregistrasi
          data.generik = null
          data.hargajual = this.item.hargaSatuan
          data.jenisobatfk = jRacikan
          //data.kelasfk = this.item.kelas.id
          data.stock = this.item.stok
          data.harganetto = this.item.hargaSatuan
          data.nostrukterimafk = this.noTerima
          data.ruanganfk = this.item.ruangan.id

          data.rke = this.item.rke
          data.jeniskemasanfk = this.item.jenisKemasan.id
          data.jeniskemasan = this.item.jenisKemasan.jeniskemasan
          data.aturanpakaifk = 0//this.item.aturanPakai.id
          data.aturanpakai = this.item.aturanpakaitxt//aturanPakai.name
          data.ispagi = this.item.chkp
          data.issiang = this.item.chks
          data.issore = this.item.chksr
          data.ismalam = this.item.chkm
          data.asalprodukfk = 0//this.item.asal.id
          data.asalproduk = ''//this.item.asal.asalproduk
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
          data.dosis = dosis
          data.jumlahxmakan = this.item.jumlahxmakan
          data.jmldosis = String((this.item.jumlah) / dosis) + '/' + String(dosis)
          data.keterangan = KetPakai
          data.satuanresepfk = this.item.satuanresep != undefined ? this.item.satuanresep.id : null
          data.satuanresep = this.item.satuanresep != undefined ? this.item.satuanresep.satuanresep : null
          data.tglkadaluarsa = this.tglkadaluarsa != undefined ? this.tglkadaluarsa : null
          data.isoutofstok = isOutOfStok
          data.belumlengkap = false
          this.data2[i] = data;
          this.dataSource = this.data2


        }
      }

    } else {
      data = {
        no: nomor,
        generik: null,
        hargajual: this.item.hargaSatuan,
        jenisobatfk: jRacikan,
        //kelasfk:this.item.kelas.id,
        stock: this.item.stok,
        harganetto: this.item.hargaSatuan,
        nostrukterimafk: this.noTerima,
        ruanganfk: this.item.ruangan.id,//£££
        rke: this.item.rke,
        jeniskemasanfk: this.item.jenisKemasan.id,
        jeniskemasan: this.item.jenisKemasan.jeniskemasan,
        aturanpakaifk: 0,//this.item.aturanPakai.id,
        aturanpakai: this.item.aturanpakaitxt,//aturanPakai.name,
        ispagi: this.item.chkp,
        issiang: this.item.chks,
        issore: this.item.chksr,
        ismalam: this.item.chkm,
        asalprodukfk: 0,//this.item.asal.id,
        asalproduk: '',//this.item.asal.asalproduk,
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
        dosis: dosis,
        jumlahxmakan: this.item.jumlahxmakan,
        jmldosis: String((this.item.jumlah) / dosis) + '/' + String(dosis),
        keterangan: KetPakai,
        satuanresepfk: this.item.satuanresep != undefined ? this.item.satuanresep.id : null,
        satuanresep: this.item.satuanresep != undefined ? this.item.satuanresep.satuanresep : null,
        tglkadaluarsa: this.tglkadaluarsa != undefined ? this.tglkadaluarsa : null,
        isoutofstok: isOutOfStok,
        belumlengkap: false,
      }
      this.data2.push(data)
      this.dataSource = this.data2

      var subTotal = 0;
      for (var i = this.data2.length - 1; i >= 0; i--) {
        subTotal = subTotal + parseFloat(this.data2[i].total)
      }
      this.item.totalSubTotal = subTotal
    }
    // debugger
    if (this.item.jenisKemasan.jeniskemasan != 'Racikan') {
      this.item.rke = parseFloat(this.item.rke) + 1
    }
    this.kosongkan();
    this.clear();
  }

  hapusD(dataSelected) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataSelected.no) {

        this.data2.splice(i, 1);
        var subTotal = 0;
        for (var i = this.data2.length - 1; i >= 0; i--) {
          subTotal = subTotal + parseFloat(this.data2[i].total)
          this.data2[i].no = i + 1
        }
        this.dataSource = this.data2
        this.item.totalSubTotal = subTotal
      }
    }
    if (this.item.jenisKemasan.jeniskemasan != 'Racikan') {
      this.item.rke = parseFloat(this.item.rke) - 1
    }
    this.kosongkan()
  }
  editD(dataSelected) {
    if (this.statusTambah == false)
      return
    var dataProduk = [];
    this.dataSelected = dataSelected
    this.item.no = dataSelected.no
    this.item.rke = dataSelected.rke
    if (dataSelected.jenisobatfk != null) {
      this.apiService.get("emr/get-jenis-obat?jrid=" + dataSelected.jenisobatfk).subscribe(JR => {
        if (JR.data.length == 0) return
        this.item.jenisRacikan = { id: JR.data[0].id, jenisracikan: JR.data[0].jenisracikan }
      });
    }
    if (this.item.jenisKemasan != undefined && this.item.jenisKemasan.id == dataSelected.jeniskemasanfk) {
    } else {
      this.item.jenisKemasan = { id: dataSelected.jeniskemasanfk, jeniskemasan: dataSelected.jeniskemasan }
    }
    this.item.satuanresep = { id: dataSelected.satuanresepfk, satuanresep: dataSelected.satuanresep }
    this.item.jumlahxmakan = dataSelected.jumlahxmakan
    this.item.dosis = dataSelected.dosis
    this.item.aturanPakai = { id: dataSelected.aturanpakaifk, name: dataSelected.aturanpakai }
    this.item.aturanpakaitxt = dataSelected.aturanpakai
    this.item.KeteranganPakai = dataSelected.keterangan

    this.item.aturanCheck = []
    if (dataSelected.ispagi != undefined && dataSelected.ispagi != "0") {
      this.item.aturanCheck[1] = true
    }
    if (dataSelected.issiang != undefined && dataSelected.issiang != "0") {
      this.item.aturanCheck[2] = true
    }
    if (dataSelected.issore != undefined && dataSelected.issore != "0") {
      this.item.aturanCheck[3] = true
    }
    if (dataSelected.ismalam != undefined && dataSelected.ismalam != "0") {
      this.item.aturanCheck[4] = true
    }
    this.getSelected()

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
  callFunt() {

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
    delete this.item.satuanresep
    delete this.item.KeteranganPakai

  }
  clear() {
    if (this.item.jenisKemasan.jeniskemasan != 'Racikan') {
      delete this.item.jenisRacikan
    }
  }
  save() {
    if (this.data2.length == 0) {
      this.alertService.error("Info", "Pilih Produk terlebih dahulu!!")
      return
    }
    var date1 = this.item.tglresep;
    var date2 = this.item.tglresepAkhir;
    var Difference_In_Time = date2.getTime() - date1.getTime();
    this.diffDays = date2.getDate() - date1.getDate();

    var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
    this.diffDays = Difference_In_Days + 1
    if (this.diffDays < 1) {
      this.alertService.error("Info", "Tanggal Akhir tidak boleh lebih kecil!!")
      return
    }

    var checkRP = 0;
    // if (this.checkResepPulang == true) {
    //   checkRP = 1;
    // }

    var tglResepHari = ''
    var objSaves = [];
    for (var i = this.diffDays - 1; i >= 0; i--) {
      var someDate = moment(this.item.tglresep).toDate();//new Date(moment(this.item.tglresep).format('YYYY-MM-DD hh:mm:ss'));
      var numberOfDaysToAdd = i;
      tglResepHari = moment(someDate.setDate(someDate.getDate() + numberOfDaysToAdd)).format('YYYY-MM-DD HH:mm:ss');

      var strukorder = {
        norec: this.norecOrder,
        tglresep: tglResepHari,
        penulisresepfk: this.item.penulisResep.id,
        ruanganfk: this.item.ruangan.id,
        noregistrasifk: this.headData.norec,
        qtyproduk: this.dataSource.length,
        noruangan: this.item.noRuang != undefined ? this.item.noRuang : null,
        isreseppulang: checkRP,
      }
      var objSave = {
        strukorder: strukorder,
        orderfarmasi: this.data2
      }
      objSaves.push(objSave);
    }


    this.isSimpan = true

    this.apiService.post('emr/simpan-order-pelayananobatfarmasi', { data: objSaves }).subscribe(e => {
      this.item.resep == undefined
      this.norecOrder = ''
      this.apiService.postLog('Order Resep', 'Norec strukresep_t', e.noresep.norec,
        'Order Resep No Order - ' + e.noresep.noorder + ' dengan No Registrasi ' + this.headData.noregistrasi).subscribe(res => { })
      this.isSimpan = false
      this.batalGrid();
      this.disabledRuangan = false
      if (this.item.noRuang != undefined)
        this.cacheHelper.set('noRuangInputResep', this.item.noRuang)


      let params = this.headData.noregistrasi    
      if( this.headData.noreservasi != undefined &&  this.headData.noreservasi != '' &&  this.headData.noreservasi !='Kios-K'){
          params =  this.headData.noreservasi
      } 
      this.saveAntrol(params)


    }, error => {
      this.isSimpan = false
    })
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
    // if (this.item.stok > 0) {
    //     this.item.stok =parseFloat(this.item.stok) * (parseFloat(oldValue)/ parseFloat(newValue))
    // }
  }
  batal() {
    this.kosongkan();
    this.clear()
  }
  batalGrid() {
    this.kosongkan();
    this.clear()
    this.data2 = []
    this.dataSource = undefined
  }

  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
    } else if (e.index == 1) {
      this.loadOrder()
    } else {
      this.loadResep()
    }
  }

  loadResep() {
    let params = 'noReg=' + this.headData.noregistrasi
    this.apiService.get("emr/get-transaksi-pelayanan?" + params).subscribe(e => {
      let group = [];
      var dat = {
        data: e
      }
      for (var i = 0; i < dat.data.length; i++) {
        dat.data[i].no = i + 1
        dat.data[i].total = parseFloat(dat.data[i].jumlah) * (parseFloat(dat.data[i].hargasatuan) - parseFloat(dat.data[i].hargadiscount));
        dat.data[i].total = parseFloat(dat.data[i].total) + parseFloat(dat.data[i].jasa);
        if (dat.data[i].reseppulang == '1') {
          dat.data[i].cekreseppulang = "✔"
        } else {
          dat.data[i].cekreseppulang = "-"
        }
      }
      var array = dat.data;
      let sama = false

      for (let i in array) {
        array[i].count = 1
        sama = false
        for (let x in group) {
          if (group[x].noresep == array[i].noresep) {
            sama = true;
            group[x].count = parseFloat(group[x].count) + parseFloat(array[i].count)

          }
        }
        if (sama == false) {
          var dataDetail0 = [];
          for (var f = 0; f < array.length; f++) {
            if (array[i].noresep == array[f].noresep) {
              dataDetail0.push(array[f]);
            };
          }
          let result = {
            noregistrasi: array[i].noregistrasi,
            tglpelayanan: array[i].tglpelayanan,
            noresep: array[i].noresep,
            aturanpakai: array[i].aturanpakai,
            namaruangandepo: array[i].namaruangandepo,
            namaruangan: array[i].namaruangan,
            dokter: array[i].dokter,
            count: array[i].count,
            cekreseppulang: array[i].cekreseppulang,
            details: dataDetail0
          }
          group.push(result)
        }
      }

      this.dataSourceResep = group

    });

  }
  editOrder(e) {

    if (e.statusorder != "Menunggu") {
      this.alertService.error("Info", 'Tidak bisa di edit sudah di verifikasi')
      return
    }

    this.apiService.get("farmasi/get-detail-order?noorder=" + e.noorder).subscribe(dat => {

      this.norecOrder = e.norec_order
      this.item.resep = e.noorder
      this.item.ruangan = { id: dat.strukorder.id, namaruangan: dat.strukorder.namaruangan }
      this.item.penulisResep = { id: dat.strukorder.pgid, namalengkap: dat.strukorder.namalengkap }
      this.item.tglresep = new Date(dat.strukorder.tglorder)
      this.item.tglresepAkhir = new Date(dat.strukorder.tglorder)


      this.data2 = dat.orderpelayanan
      for (var i = this.data2.length - 1; i >= 0; i--) {



        this.data2[i].noregistrasifk = this.headData.norec//this.item.noRegistrasi
        this.data2[i].tglregistrasi = this.headData.tglregistrasi

        this.data2[i].kelasfk = this.headData.objectkelasfk

      }

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

  loadOrder() {
    let params = 'noreg=' + this.headData.noregistrasi
    this.apiService.get("emr/get-daftar-detail-order?" + params).subscribe(e => {
      for (var i = e.length - 1; i >= 0; i--) {
        e[i].no = i + 1
        if (e[i].reseppulang == 1) {
          e[i].cekreseppulang = "✔"
        } else {
          e[i].cekreseppulang = "-"
        }
      }
      this.dataSourceRiwayat = e
    });
  }

  hapusOrder(e) {
    if (e.statusorder != "Menunggu") {
      this.alertService.error("Info", 'Tidak bisa di Hapus sudah di verifikasi')
      return
    }

    this.apiService.post('emr/hapus-order-pelayananobatfarmasi', { norec: e.norec_order }).subscribe(e => {
      this.loadOrder();
    })
  }

  loadSkeleton() {
    return [
      { 'id': 1, details: [{ 'class': 'p-col-2', details: [{ 'class': 'p-mb-0' }] }] },
      {
        'id': 2, details: [
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
        ]
      },
      { 'id': 3, details: [{ 'class': 'p-col-2', details: [{ 'class': 'p-mb-0' }] }] },
      {
        'id': 4, details: [
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
        ]
      }
    ]
  }
  saveAntrol(param){
    var data = {
       "url": "antrean/updatewaktu",
       "jenis": "antrean",
       "method": "POST",
       "data":                                                 
       {
          "kodebooking": param,
          "taskid": 5,//Waktu mulai farmasi
          "waktu": new Date().getTime()  
       }
   }
   this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe( e=> {})
}
}
