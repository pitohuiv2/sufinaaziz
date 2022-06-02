import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-retur-resep-nonlayanan',
  templateUrl: './retur-resep-nonlayanan.component.html',
  styleUrls: ['./retur-resep-nonlayanan.component.scss'],
  providers: [ConfirmationService]
})
export class ReturResepNonlayananComponent implements OnInit {
  norec_resep: any;
  norec_apd: any;
  params: any = {}
  item: any = {
    pasien: {},
  }
  isSimpan: any;
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  columnGrid: any[] = [];
  dataSource: any[] = [];
  data2: any[] = [];
  listRuangan: any[];
  listPenulisResep: any[];
  disabledRuangan: boolean;
  listJenisKemasan: any[];
  listAsalProduk: any[];
  listsatuanresep: any[];
  listJenisRacikan: any[];
  listProduk: any[];
  dataProdukDetail: any[] = [];
  listSatuan: any[] = [];
  statusTambah: boolean = true
  dataSelected: any;
  JenisData: any;
  hrg1: any = 0;
  noTerima: any;
  noOrder: any = "";
  strStatus: any = 0;
  isPemakaianObatAlkes: boolean = false;
  hargaDiskon: any = 0;
  tglkadaluarsa: any;
  showRacikanDose: boolean;
  maxDateValue:any
  listJenisKelamin:any[]
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
      { field: 'jmlretur', header: 'Jumlah Retur', width: "150px" },
    ];
  }

  loadCombo() {
    this.apiService.get("farmasi/get-combo-farmasi").subscribe(table => {
      var dataCombo = table;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuangan = dataCombo.ruangfarmasi;
      }
      this.listPenulisResep = dataCombo.dokter;
    });
    this.apiService.get("farmasi/get-combo-resep").subscribe(e => {
      this.listJenisKemasan = e.jeniskemasan;
      this.listAsalProduk = e.asalproduk;
      this.listsatuanresep = e.satuanresep
      this.listJenisRacikan = e.jenisracikan;
      this.item.jenisKemasan = e.jeniskemasan[1];
    });
  }

  firstLoad() {
    this.route.params.subscribe(params => {
      this.params.norec_resep = params['norec_resep'];
      this.norec_resep = params['norec_resep'];
    });
    this.loadData();
  }

  loadData() {
    if (this.norec_resep != "-") {
      this.apiService.get("farmasi/get-detail-obat-bebas?norecResep=" + this.norec_resep).subscribe(data => {
        var strukresep = data.detailresep;
        var detail = data.pelayananPasien;
        var subTotal = 0;
        this.item.pasien = strukresep;
        this.item.pasien.tgllahir = moment(new Date(strukresep.tgllahir)).format('YYYY-MM-DD HH:mm')
        this.item.pasien.jeniskelamin = { id: strukresep.jkid, jeniskelamin: strukresep.jeniskelamin }
        this.item.nomorResep = strukresep.nostruk
        this.item.ruangan = { id: strukresep.id, namaruangan: strukresep.namaruangan }
        this.disabledRuangan = true;
        this.item.penulisResep = { id: strukresep.pgid, namalengkap: strukresep.namalengkap }
        this.item.tglresep = moment(strukresep.tglresep).format('YYYY-MM-DD HH:mm');
        for (let i = 0; i < detail.length; i++) {
          const element = detail[i];
          element.no = i + 1;
          element.jmlretur = 0
          subTotal = subTotal + parseFloat(element.total);
        }
        this.data2 = detail
        this.item.totalSubTotal = this.formatRupiah(subTotal, "Rp.");
        this.dataSource = this.data2;
      });
    }
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

  cariPasien() {
    if (this.item.pasien.norm == "" || this.item.pasien.norm == "-") {
      this.item.pasien.namapasien = undefined
      this.item.pasien.jeniskelamin = undefined
      this.item.pasien.tgllahir = undefined
      this.item.pasien.notelepon = undefined
      this.item.pasien.alamatlengkap = undefined
      return
    }
    this.apiService.get("general/get-detail-pasien?nocm=" + this.item.pasien.norm).subscribe(data => {
      if (data != undefined) {
        this.item.pasien = data;
        this.item.pasien.tgllahir = new Date(moment(data.tgllahir).format('YYYY-MM-DD HH:mm'))
        this.item.pasien.jeniskelamin = { id: data.jkid, jeniskelamin: data.jeniskelamin }
      }
    })
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

    this.GETKONVERSI()
  }

  GETKONVERSI() {
    this.listSatuan = this.item.produk.konversisatuan
    if (this.listSatuan.length == 0) {
      this.listSatuan = ([{ ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar }])
    }
    this.item.satuan = { ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar }
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
        if (dat.kekuatan == 0) {
          dat.kekuatan = 1
        }
        this.item.kekuatan = dat.kekuatan
        this.item.sediaan = dat.sediaan
        this.tglkadaluarsa = moment(this.dataProdukDetail[0].tglkadaluarsa).format("YYYY-MM-DD HH:mm");
        if (this.dataProdukDetail.length > 0)
          if (this.dataSelected != undefined) {
            this.item.nilaiKonversi = this.dataSelected.nilaikonversi;
            this.item.stok = this.dataSelected.jmlstok
            this.item.jumlah = this.dataSelected.jumlah
            this.item.hargaSatuan = this.dataSelected.hargasatuan
            if (this.dataSelected.hargadiscount != 0) {
              this.hargaDiskon = parseFloat(this.item.hargaSatuan) - parseFloat(this.dataSelected.hargadiscount);
              this.item.hargaDiskon = parseFloat(this.item.hargaSatuan) / parseFloat(this.dataSelected.hargadiscount);
            } else {
              this.hargaDiskon = 0;
              this.item.hargaDiskon = 0;
            }
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
      this.item.jumlah = 1;
      this.item.hargaSatuan = 0;
      this.item.hargaDiskon = 0;
      this.item.hargaTotal = 0;
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
    this.item.jumlahRetur = 0;
  }

  clear() {
    if (this.item.jenisKemasan.jeniskemasan != 'Racikan') {
      delete this.item.jenisRacikan
    }
  }

  batal() {
    this.kosongkan();
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

  tambah = function () {
    if (this.statusTambah == false) {
      return
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

    if (this.item.jumlahRetur == 0) {
      this.alertService.error("Info", "Jumlah retur harus di isi!")
      return;
    }

    if (this.item.jenisKemasan == undefined) {
      this.alertService.error("Info", "Pilih Jenis Kemasan terlebih dahulu!!")
      return;
    }
    if (this.item.produk == undefined) {
      this.alertService.error("Info", "Data Yang Dapat Diretur Hanya Bisa Produk Didalam Resep!")
      return;
    }
    if (this.item.satuan == undefined) {
      this.alertService.error("Info", "Data Yang Dapat Diretur Hanya Bisa Produk Didalam Resep!")
      return;
    }

    var checkOK = 0;
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
          data.generik = null
          data.hargajual = this.item.hargaSatuan
          data.jenisobatfk = jRacikan
          data.stock = this.item.stok
          data.harganetto = this.item.hargaSatuan
          data.nostrukterimafk = this.noTerima
          data.rke = this.item.rke
          data.jeniskemasanfk = this.item.jenisKemasan.id
          data.jeniskemasan = this.item.jenisKemasan.jeniskemasan
          data.aturanpakaifk = 0
          data.aturanpakai = this.item.aturanpakaitxt
          data.ispagi = this.item.chkp
          data.issiang = this.item.chks
          data.issore = this.item.chksr
          data.ismalam = this.item.chkm
          data.asalprodukfk = 0
          data.asalproduk = ''
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
          data.jmlretur = parseFloat(this.item.jumlahRetur)

          this.data2[i] = data;
          this.dataSource = this.data2
        }
      }
    } else {
      this.alertService.error("Info", "Data Yang Dapat Diretur Hanya Bisa Produk Didalam Resep!")
      return;
    }
    this.kosongkan();
  }

  batalGrid() {
    this.kosongkan();
    this.data2 = []
    this.dataSource = undefined
  }

  save() {
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

    if (this.item.alasan == undefined) {
      this.alertService.error("Info", "Alasan Retur Masih Kosong!")
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
    
    var subTotal = 0;
    for (var i = this.data2.length - 1; i >= 0; i--) {
      subTotal = subTotal + parseFloat(this.data2[i].total)
    }

    this.isSimpan = true;
    var strukresep = {
      noresep: this.norec_resep,
      tglresep: moment(this.item.tglresep).format('YYYY-MM-DD HH:mm'),
      nocm: this.item.pasien.norm,
      namapasien: this.item.pasien.namapasien,
      penulisresepfk: this.item.penulisResep.id,
      ruanganfk: this.item.ruangan.id,
      keteranganlainnya: 'Resep Non Layanan',
      totalharusdibayar: subTotal,
      tglLahir: this.item.pasien.tgllahir,
      noTelepon: this.item.pasien.notelepon,
      alamat: this.item.pasien.alamatlengkap,
      jkid: this.item.pasien.jeniskelamin.id,
      karyawan: null,
      retur: 'RETUR',
      alasan: this.item.alasan,
      pegawairetur: this.dataLogin.pegawai.id,
    }
    var objSave =
    {
      strukresep: strukresep,
      details: this.data2
    }

    this.apiService.post('farmasi/save-retur-obat-non-layanan', objSave).subscribe(dataSave => {
      this.isSimpan = false;
      this.apiService.postLog('Simpan Retur Pelayanan Resep Non Layanan', 'Norec strukpelayanan', this.norec_resep,
        'Simpan Retur Pelayanan Resep Non Layanan Dengan Noresep - ' + this.item.nomorResep + ', Atas Nama - ' + this.item.pasien.namapasien).subscribe(res => { })
      this.kosongkan();
      this.data2 = []
      this.dataSource = undefined
      window.history.back();
    })
  }

}
