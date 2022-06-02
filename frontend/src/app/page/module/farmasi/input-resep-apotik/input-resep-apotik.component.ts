import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-input-resep-apotik',
  templateUrl: './input-resep-apotik.component.html',
  styleUrls: ['./input-resep-apotik.component.scss'],
  providers: [ConfirmationService]
})
export class InputResepApotikComponent implements OnInit {
  norec_pd: any;
  norec_apd: any;
  params: any = {}
  item: any = {
    pasien: {},
    tglresep: new Date(),
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
  listRuangan: any[];
  listPenulisResep: any[];
  listJenisKemasan: any[];
  listAsalProduk: any[];
  listsatuanresep: any[];
  listJenisRacikan: any[];
  listProduk: any[];
  dataProdukDetail: any[] = [];
  listSatuan: any[] = [];
  statusTambah: boolean = true
  pop_inputTindakan: boolean;
  
  dataSelected: any
  listDataSigna = [
    { "id": 1, "nama": "P", 'isChecked': false },
    { "id": 2, "nama": "S", 'isChecked': false },
    { "id": 3, "nama": "Sr", 'isChecked': false },
    { "id": 4, "nama": "M", 'isChecked': false }
  ];
  hrg1: any = 0;
  columnGrid: any[] = [];
  showRacikanDose: boolean;
  noTerima: any;
  dataSource: any[] = [];
  data2: any[] = [];
  diffDays: any;
  norecOrder: any = '';
  noOrder: any = '';
  dataSourceRiwayat: any[];
  dataSourceResep: any[];
  columnResep: any[];
  dateNow: any;
  NorecData: any;
  JenisData: any;
  dataLogin: any;
  kelUser: any;
  hargaDiskon: any;
  norecResep: any;
  strStatus: any = 0;
  isPemakaianObatAlkes: boolean = false;
  tglkadaluarsa: any;
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
    this.isSimpan = false;
    this.dateNow = new Date();
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.item.SelectedResepPulang = false;
    this.item.SelectedObatKronis = false;
    this.hargaDiskon = 0;
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
  }

  loadCombo() {
    // this.apiService.get("farmasi/get-combo-farmasi").subscribe(table => {
    //   var dataCombo = table;
    //   if (this.dataLogin.mapLoginUserToRuangan != undefined) {
    //     this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
    //   } else {
    //     this.listRuangan = dataCombo.ruangfarmasi;
    //   }
    //   this.listPenulisResep = dataCombo.dokter;
    // });
    this.apiService.get("farmasi/get-combo-resep-rev").subscribe(e => {
      this.listJenisKemasan = e.jeniskemasan;
      this.listAsalProduk = e.asalproduk;
      this.listsatuanresep = e.satuanresep
      this.listJenisRacikan = e.jenisracikan;
      this.listPenulisResep = e.dokter;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuangan = e.ruanganfarmasi;
      }
      this.item.jenisKemasan = e.jeniskemasan[1];
      this.item.tglresep = new Date(e.tglserver)
    });
  }

  firstLoad() {
    this.route.params.subscribe(params => {
      this.params.norec_pd = params['norec_rp'];
      this.params.norec_apd = params['norec_dpr'];
      this.norec_pd = params['norec_rp'];
      this.norec_apd = params['norec_dpr'];
    });
    var Cache = this.cacheHelper.get("InputResepPasienCtrl");
    if (Cache != undefined) {
      this.item.RuangRawat = Cache[11];
      this.item.pasien.norm = Cache[0];
      this.item.pasien.noregistrasi = Cache[3];
      this.item.pasien.namapasien = Cache[1];
      this.item.pasien.jeniskelamin = Cache[2];
      this.item.pasien.umur = Cache[4];
      this.item.pasien.kelompokpasien = Cache[10];
      this.item.pasien.alamat = Cache[12];
      this.JenisData = Cache[16];
      this.NorecData = Cache[9];
      this.loadData();
      this.loadReser()
    }
  }
  loadReser(){
    this.apiService.get("farmasi/get-noreservasi?norec_pd=" +  this.norec_pd ).subscribe(e => {      
      this.item.pasien.noreservasi = e.noreservasi;   
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

  loadData() {
    if (this.JenisData != undefined || this.JenisData != "") {
      if (this.JenisData == "OrderResep") {
        this.apiService.get("farmasi/get-detail-order?norecOrder=" + this.NorecData).subscribe(dataOrder => {          
          var strukorder = dataOrder.strukorder;
          var detail = dataOrder.orderpelayanan;
          var total: any = 0;
          this.noOrder = strukorder.noorder;
          this.norecResep = "";
          this.disabledRuangan = true;
          this.item.ruangan = undefined;
          this.item.penulisResep = undefined;
          this.item.ruangan = { id: strukorder.id, namaruangan: strukorder.namaruangan }
          this.item.penulisResep = { id: strukorder.pgid, namalengkap: strukorder.namalengkap }          
          if (strukorder.isreseppulang == '1') {
            this.item.SelectedResepPulang = true;
          } else {
            this.item.SelectedResepPulang = false;
          }
          this.data2 = detail
          for (let e = 0; e < this.data2.length; e++) {
            const elements = this.data2[e];
            if (elements.obatkronis == "1") {
              this.item.SelectedObatKronis = true;
              break;
            } else {
              this.item.SelectedObatKronis = false;
              break;
            }
          }
          for (let i = 0; i < this.data2.length; i++) {
            const element = this.data2[i];
            element.no = i + 1;
            if (element.nilaikonversi == 0 || element.nilaikonversi == undefined) {
              element.nilaikonversi = 1
            }
            if (element.hargadiscount == undefined) {
              element.hargadiscount = 0;
              element.persendiscount = 0;
            } else {
              element.hargadiscount = parseFloat(element.hargadiscount);
              element.persendiscount = parseFloat(element.hargajual) / parseFloat(element.hargadiscount);
            }

            element.total = parseFloat(element.jumlah) * parseFloat(element.hargasatuan)
            total = total + parseFloat(element.total)
          }
          this.item.totalSubTotal = this.formatRupiah(total, "Rp.");
          this.dataSource = this.data2;
        })
      } else if (this.JenisData == "EditResep") {
        this.apiService.get("farmasi/get-detail-resep?norecResep=" + this.NorecData).subscribe(table => {
          var strukresep = table.detailresep;
          var pelayananpasien = table.pelayananPasien;
          var total: any = 0;
          this.item.penulisResep = [];
          this.disabledRuangan = true;
          this.item.nomorResep = strukresep.noresep
          this.item.ruangan = { id: strukresep.id, namaruangan: strukresep.namaruangan }
          this.item.penulisResep = { id: strukresep.pgid, namalengkap: strukresep.namalengkap }
          this.item.tglresep = moment(strukresep.tglresep).format("YYYY-MM-DD HH:mm");
          this.norecResep = strukresep.norec
          if (strukresep.isreseppulang == '1') {
            this.item.SelectedResepPulang = true;
          } else {
            this.item.SelectedResepPulang = false;
          }
          this.data2 = pelayananpasien;
          for (let e = 0; e < this.data2.length; e++) {
            const elements = this.data2[e];
            if (elements.obatkronis == "1") {
              this.item.SelectedObatKronis = true;
              break;
            } else {
              this.item.SelectedObatKronis = false;
              break;
            }
          }
          for (let i = 0; i < this.data2.length; i++) {
            const element = this.data2[i];
            element.no = i + 1;
            if (element.nilaikonversi == 0 || element.nilaikonversi == undefined) {
              element.nilaikonversi = 1
            }
            if (element.hargadiscount == undefined) {
              element.hargadiscount = 0;
              element.persendiscount = 0;
            } else {
              element.hargadiscount = parseFloat(element.hargadiscount);
              element.persendiscount = parseFloat(element.hargajual) / parseFloat(element.hargadiscount);
            }

            element.total = parseFloat(element.jumlah) * parseFloat(element.hargasatuan)
            total = total + parseFloat(element.total)
          }
          this.item.totalSubTotal = this.formatRupiah(total, "Rp.");
          this.dataSource = this.data2;
        })
      }
    }
  }

  ResepPulang(event: any) {
    var data = event;
    if (data.checked == true) {
      this.item.SelectedResepPulang = true;
    } else {
      this.item.SelectedResepPulang = false;
    }
  }

  ObatKronis(event: any) {
    var data = event;
    if (data.checked == true) {
      this.item.SelectedObatKronis = true;
    } else {
      this.item.SelectedObatKronis = false;
    }
  }

  filterProduk(event) {
    let query = event.query;
    this.apiService.get("farmasi/get-produk-resep?namaproduk=" + query)
      .subscribe(re => {
        this.listProduk = re;
      })
  }

  getSatuan() {
    if (this.item.produk.id == undefined) return
    if(this.item.produk.id ==  '406007'){
     this.pop_inputTindakan = true
    }
    this.GETKONVERSI()
  }
  hitungoksigen(){
       this.item.jumlah = this.item.menit * this.item.lpm;
       this.gettotal()
       this.pop_inputTindakan = false 
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
    this.apiService.get("general/get-produkdetail-general?" +
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
        this.tglkadaluarsa = moment(this.dataProdukDetail[0].tglkadaluarsa).format("YYYY-MM-DD HH:mm");
        if (this.dataProdukDetail.length > 0)
          if (this.dataSelected != undefined) {
            this.item.nilaiKonversi = this.dataSelected.nilaikonversi;
            this.item.stok = this.dataSelected.jmlstok //* this.item.nilaiKonversi 
            this.item.jumlah = this.dataSelected.jumlah
            this.item.hargaSatuan = this.dataSelected.hargasatuan
            if (this.dataSelected.persendiscount != 0) {
              this.hargaDiskon = (parseFloat(this.item.hargaSatuan) * parseFloat(this.dataSelected.persendiscount))/100;
              this.item.hargaDiskon = parseFloat(this.dataSelected.persendiscount);
            }else{
              this.hargaDiskon = 0;
              this.item.hargaDiskon = 0;
            }   
            // this.item.hargadiskon = this.dataSelected.hargadiscount
            this.item.hargaTotal = this.dataSelected.total
            if (this.item.kekuatan != undefined && this.item.kekuatan != 0) {
              this.item.jumlahxmakan = (parseFloat(this.item.jumlah) / parseFloat(this.item.dosis)) * parseFloat(this.item.kekuatan)
            }
          } else {
            this.hargaDiskon = 0;
            this.item.hargaDiskon = 0;
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
    if (this.item.stok > 0) {
      this.item.stok = parseFloat(this.item.stok) * parseFloat(this.item.nilaiKonversi)
      this.item.jumlah = 1//parseFloat(this.item.jumlah) / parseFloat(newValue)
      this.item.hargaSatuan = 0//hrg1 * parseFloat(newValue)
      this.item.hargaDiskon = 0//hrgsdk * parseFloat(newValue)
      this.item.hargaTotal = 0// parseFloat(newValue) * 
      // (hrg1-hrgsdk)
    }
  }

  onChangeDiskon(e) {

    this.item.hargaDiskon = parseFloat(e.value)
    this.hargaDiskon = 0;
    var harga = this.item.hargaSatuan - ((this.item.hargaSatuan * this.item.hargaDiskon) / 100)
    this.hargaDiskon = harga
    this.item.hargaTotal = (this.item.jumlah * (harga))
    // this.gettotal()
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
        this.item.hargaTotal = (this.item.jumlah * (this.hrg1))//+tarifJasa
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
    this.item.hargaSatuan = 0
    delete this.item.satuanresep
    delete this.item.KeteranganPakai
    this.item.hargaDiskon = 0;
    this.item.hargaTotal = 0;
    this.hargaDiskon = 0;
    this.tglkadaluarsa = undefined;
  }

  clear() {
    if (this.item.jenisKemasan.jeniskemasan != 'Racikan') {
      delete this.item.jenisRacikan
    }
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

  tambah = function () {
    if (this.statusTambah == false) {
      return
    }

    if (this.headData.isclosing == true) {
      this.alertService.error("Info", "Data Sudah Diclosing!");
      return;
    }

    if (this.item.ruangan == undefined) {
      this.alertService.error("Info", "Data Ruangan Masih Kosong!");
      return;
    }

    if (this.item.penulisResep == undefined) {
      this.alertService.error("Info", "Data Penulis Resep Masih Kosong!");
      return;
    }

    if (this.item.tglresep == undefined) {
      this.alertService.error("Info", "Data Tanggal Resep Masih Kosong!");
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

    var checkOK = 0;
    if (this.item.SelectedObatKronis == true) {
      checkOK = 1;
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
          data.hargadiscount = this.hargaDiskon != undefined ? this.hargaDiskon : 0
          data.persendiscount = this.item.hargaDiskon != undefined ? this.item.hargaDiskon : 0
          data.total = this.item.hargaTotal
          data.dosis = dosis
          data.jumlahxmakan = this.item.jumlahxmakan
          data.jmldosis = String((this.item.jumlah) / dosis) + '/' + String(dosis)
          data.keterangan = KetPakai
          data.satuanresepfk = this.item.satuanresep != undefined ? this.item.satuanresep.id : null
          data.satuanresep = this.item.satuanresep != undefined ? this.item.satuanresep.satuanresep : null
          data.tglkadaluarsa = this.tglkadaluarsa != undefined ? this.tglkadaluarsa : null
          data.isoutofstok = isOutOfStok
          data.iskronis = checkOK
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
        hargadiscount: this.hargaDiskon != undefined ? this.hargaDiskon : 0,
        persendiscount: this.item.hargaDiskon != undefined ? this.item.hargaDiskon : 0,
        total: this.item.hargaTotal,
        dosis: dosis,
        jumlahxmakan: this.item.jumlahxmakan,
        jmldosis: String((this.item.jumlah) / dosis) + '/' + String(dosis),
        keterangan: KetPakai,
        satuanresepfk: this.item.satuanresep != undefined ? this.item.satuanresep.id : null,
        satuanresep: this.item.satuanresep != undefined ? this.item.satuanresep.satuanresep : null,
        tglkadaluarsa: this.tglkadaluarsa != undefined ? this.tglkadaluarsa : null,
        isoutofstok: isOutOfStok,
        iskronis: checkOK,
        belumlengkap: false,
      }
      this.data2.push(data)
      this.dataSource = this.data2

      var subTotal = 0;
      for (var i = this.data2.length - 1; i >= 0; i--) {
        subTotal = subTotal + parseFloat(this.data2[i].total)
      }
      this.item.totalSubTotal = this.formatRupiah(subTotal, "Rp.");
    }
    // debugger
    if (this.item.jenisKemasan.jeniskemasan != 'Racikan') {
      this.item.rke = parseFloat(this.item.rke) + 1
    }
    this.kosongkan();
    this.clear();
  }

  batal() {
    this.kosongkan();
    this.clear()
  }

  batalGrid() {
    this.kosongkan();
    this.clear()
    if (this.JenisData == "OrderResep") {
      this.alertService.warn("Info", "Data Tidak Bisa Direset");
      return;
    } else if (this.JenisData == "EditResep") {
      this.alertService.warn("Info", "Data Tidak Bisa Direset");
      return;
    } else {
      this.data2 = []
      this.dataSource = undefined
    }

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
      this.apiService.get("farmasi/get-jenis-obat?jrid=" + dataSelected.jenisobatfk).subscribe(JR => {
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

  Kembali() {
    window.history.back();
  }

  save() {
    this.isSimpan = true;
    var checkRP = 0;
    if (this.item.SelectedResepPulang == true) {
      checkRP = 1;
    }

    var checkOK = 0;
    if (this.item.SelectedObatKronis == true) {
      checkOK = 1;
    }

    if (this.item.tglresep == undefined) {
      this.alertService.error("Info", "Tgl Resep Masih Kosong!")
      return
    }

    if (this.item.ruangan == undefined) {
      this.alertService.error("Info", "Ruangan Masih Kosong!")
      return
    }

    if (this.item.penulisResep == undefined) {
      this.alertService.error("Info", "Penulis Resep Masih Kosong!")
      return
    }

    if (this.data2.length == 0) {
      this.alertService.error("Info", "Pilih Produk terlebih dahulu!")
      return
    }

    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (parseFloat(this.data2[i].jmlstok) < parseFloat(this.data2[i].jumlah)) {
        this.alertService.error("Info", "Terdapat obat dengan jumlah melebihi STOK!" + this.data2[i].namaproduk)
        return;
      }
    }

    var strukresep = {
      tglresep: moment(this.item.tglresep).format('YYYY-MM-DD HH:mm:ss'),
      pasienfk: this.norec_apd,
      nocm: this.item.pasien.norm,
      namapasien: this.item.pasien.namapasien,
      penulisresepfk: this.item.penulisResep != undefined ? this.item.penulisResep.id : null,
      ruanganfk: this.item.ruangan.id,
      noorder: this.noOrder,//this.norecOrder,
      status: this.strStatus,
      norecResep: this.norecResep,
      noresep: this.item.nomorResep != undefined ? this.item.nomorResep : "",
      retur: '-',
      isobatalkes: this.isPemakaianObatAlkes,
      isreseppulang: checkRP != undefined ? checkRP : null,
      isobatkronis: checkOK != undefined ? checkOK : null,
      jenisdata: this.JenisData

    }
    var objSave =
    {
      strukresep: strukresep,
      pelayananpasien: this.data2
    }

    this.apiService.post('farmasi/save-pelayananobat', objSave).subscribe(dataSave => {
      this.isSimpan = true;

      let params = this.item.pasien.noregistrasi 
      if(this.item.pasien.noreservasi != undefined && this.item.pasien.noreservasi != '' && this.item.pasien.noreservasi !='Kios-K'){
          params =this.item.pasien.noreservasi 
      } 
      this.saveAntrol(params,5)//waktu mulai farmasi

      this.norecResep = dataSave.resep.norec;
      this.apiService.postLog('Simpan Pelayanan Resep Pasien', 'Norec strukresep_t', this.norecResep,
        'Simpan Pelayanan Resep Pasien Dengan Noresep - ' + dataSave.resep.noresep + ' dan No Registrasi - ' + this.item.pasien.noregistrasi).subscribe(res => { })
      if (this.JenisData == "OrderResep") {
        this.apiService.postLog('Simpan Verifikasi Order Resep', 'Norec transaksiorder', this.NorecData,
          'Simpan Verifikasi Order Resep Dengan Nooder - ' + this.noOrder + ' Noresep - ' + dataSave.resep.noresep + ' dan No Registrasi - ' + this.item.pasien.noregistrasi).subscribe(res => { })
      }
      this.kosongkan();
      this.clear();
      this.data2 = []
      this.dataSource = undefined
      window.history.back();
    })
  }
   saveAntrol(param,task){
    var data = {
       "url": "antrean/updatewaktu",
       "jenis": "antrean",
       "method": "POST",
       "data":                                                 
       {
          "kodebooking": param,
          "taskid": task,//Waktu mulai farmasi
          "waktu": new Date().getTime()  
       }
     }
     this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe(e => {})
  }

}
