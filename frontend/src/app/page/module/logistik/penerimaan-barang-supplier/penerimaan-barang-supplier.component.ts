import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-penerimaan-barang-supplier',
  templateUrl: './penerimaan-barang-supplier.component.html',
  styleUrls: ['./penerimaan-barang-supplier.component.scss'],
  providers: [ConfirmationService]
})
export class PenerimaanBarangSupplierComponent implements OnInit {
  norec_data: any;
  norecOrder: any = '';
  norec_Realisasi: any = '';
  params: any = {};
  jenisData: any;
  listRuangan: any[];
  listRuanganAll: any[];
  listAsalProduk: any[];
  listJenisKirim: any[];
  listSatuan: any[] = [];
  listProduk: any[] = [];
  listRekanan: any[] = [];
  listKelompokProduk: any[] = [];
  listPegawaiPj: any[] = [];
  listPegawai: any[] = [];
  dataProdukDetail: any[] = [];
  columnGrid: any[] = [];
  dataSource: any[] = [];
  dataSelected: any
  data2: any[] = [];
  item: any = {};
  dataLogin: any;
  kelUser: any;
  dateNow: any;
  noTerima: any;
  hrg1: any = 0;
  tglkadaluarsa: any;
  statusTambah: boolean = true;
  disabledRuangan: boolean;
  isSimpan: any;
  maxDateValue:any
  disabledEdit:boolean
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
    this.item.tahun = moment(this.dateNow).format("YYYY");
    this.item.tglPermintaan = this.dateNow;
    this.item.tglTerima = this.dateNow;
    this.item.tglFaktur = this.dateNow;
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
      { field: 'asalproduk', header: 'Asal Produk', width: "180px" },
      { field: 'satuanstandar', header: 'Satuan', width: "120px" },
      { field: 'jumlahusulan', header: 'Qty Usulan', width: "120px" },
      { field: 'jumlah', header: 'Qty Terima', width: "120px" },
      { field: 'hargasatuan', header: 'Harga Satuan', width: "140px", isCurrency: true },
      { field: 'subtotal', header: 'Total Harga', width: "140px", isCurrency: true },
      { field: 'persendiscount', header: 'Diskon (%)', width: "120px" },
      { field: 'persenppn', header: 'PPN (%)', width: "120px" },
      { field: 'total', header: 'Total', width: "140px", isCurrency: true },
      { field: 'nobatch', header: 'Nobatch', width: "100px" },
      { field: 'keterangan', header: 'Keterangan', width: "180px" },
      { field: 'tglkadaluarsa', header: 'Tgl Exp', width: "140px" }
    ]
  }

  loadCombo() {
    if (this.dataLogin.mapLoginUserToRuangan != undefined) {
      this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      this.item.ruangan = this.listRuangan[0];
    } else {
      this.alertService.error("Info", "Ruangan Login Belum Disetting!");
      return;
    }
    this.apiService.get("logistik/get-combo-penerimaan").subscribe(e => {
      this.listAsalProduk = e.asalproduk;
      this.listKelompokProduk = e.kelompokproduk;
      this.item.dataKelompokProduk = e.kelompokproduk[0];
    });
  }

  filterRekanan(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-rekanan-part?namarekanan=" + query)
      .subscribe(re => {
        this.listRekanan = re;
      })
  }

  filterPegawaiPj(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-pegawai-part?namalengkap=" + query)
      .subscribe(re => {
        this.listPegawaiPj = re;
      })
  }

  filterPegawai(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-pegawai-part?namalengkap=" + query)
      .subscribe(re => {
        this.listPegawai = re;
      })
  }

  firstLoad() {
    this.route.params.subscribe(params => {
      this.params.norec_data = params['norec'];
      this.params.jenisdata = params['jenisdata'];
      this.jenisData = params['jenisdata'];
      if (this.jenisData == "editterima") {
        this.norec_data = params['norec'];
      } else {
        this.norec_data = '';
      }
      this.loadData();
    });
  }

  loadData() {
    if (this.norec_data != "-") {
      if (this.jenisData == "editterima") {
        this.apiService.get("logistik/get-detail-penerimaan?norec=" + this.norec_data).subscribe(dataLoad => {          
          var datahead = dataLoad.detailterima;
          var detail = dataLoad.details;
          //** DATA USULAN */
          this.item.nomorPermintaan = datahead.nousulan;
          this.item.tglPermintaan = moment(datahead.tglkontrak).format('YYYY-MM-DD HH:mm');
          this.item.nomorKontrak = datahead.nokontrak;
          this.item.tahun = moment(datahead.tglkontrak).format('YYYY');
          this.item.namaPengadaan = datahead.namapengadaan;
          this.norecOrder = datahead.noorderidfk;
          //** END DATA USULAN */
          //** DATA FAKTUR */
          this.item.tglFaktur = moment(datahead.tglfaktur).format('YYYY-MM-DD HH:mm');
          this.item.tglTerima = moment(datahead.tglstruk).format('YYYY-MM-DD HH:mm');
          this.item.nomorTerima = datahead.nostruk;
          this.item.nomorFaktur = datahead.nofaktur;
          this.item.dataAsalProduk = { id: datahead.asalprodukidfk, asalproduk: datahead.asalproduk };
          //** END DATA FAKTUR */
          //** DATA PETUGAS */
          this.listPegawaiPj = [{
            id: datahead.pegawaipenanggungjawabidfk, namalengkap: datahead.penanggungjawab
          }];
          this.item.dataPenanggungJawab = this.listPegawaiPj[0];
          this.listPegawai = [{
            id: datahead.pgid, namalengkap: datahead.namalengkap
          }];
          this.item.dataPetugasPenerima = this.listPegawai[0];
          this.item.ruangan = { id: datahead.id, namaruangan: datahead.namaruangan };
          //** END DATA PETUGAS */
          //** DATA SUPPLIER */
          this.listRekanan = [{
            id: datahead.rekananidfk, namarekanan: datahead.namarekanan
          }];
          this.item.dataRekanan = this.listRekanan[0];
          this.item.dataKelompokProduk = { id: datahead.kelompokprodukidfk, kelompokproduk: datahead.kelompokproduk };
          this.item.tglJatuhTempo = moment(datahead.tgljatuhtempo).format('YYYY-MM-DD HH:mm');
          //** END DATA SUPPLIER */    
          this.item.Keterangan = datahead.keteranganambil;
          this.norec_Realisasi = datahead.norecrealisasi;
          this.data2 = detail;
          var totalharga: any = 0;
          var totaldiskon: any = 0;
          var totalppn: any = 0;
          var totalall: any = 0;
          for (let i = 0; i < this.data2.length; i++) {
            const element = this.data2[i];
            element.no = i + 1;
            totalharga = totalharga + parseFloat(element.subtotal);
            totaldiskon = totaldiskon + parseFloat(element.totaldiskon);
            totalppn = totalppn + parseFloat(element.ppn);
          }
          this.dataSource = this.data2;
          totalall = (totalharga - totaldiskon) + totalppn;
          this.item.totalHarga = 0;
          this.item.totalDiskon = 0;
          this.item.totalPpn = 0;
          this.item.TotalAll = 0;
          this.item.totalHarga = this.formatRupiah(totalharga, "Rp.");
          this.item.totalDiskon = this.formatRupiah(totaldiskon, "Rp.");
          this.item.totalPpn = this.formatRupiah(totalppn, "Rp.");
          this.item.TotalAll = this.formatRupiah(totalall, "Rp.");

        });
      }
    }
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

  getSatuan() {
    if (this.item.produk.id == undefined) return
    this.GETKONVERSI(0)
  }

  GETKONVERSI(jml) {
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
    if (this.item.produk == undefined) {
      return
    }

    if (this.item.produk == "") {
      return
    }

    this.listSatuan = this.item.produk.konversisatuan
    if (this.listSatuan.length == 0) {
      this.listSatuan = ([{ ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar }])
    }

    this.item.satuan = { ssid: this.item.produk.ssid, satuanstandar: this.item.produk.satuanstandar }
    this.item.keterangan = this.item.produk.spesifikasi;

    if (this.item.satuan.nilaikonversi == undefined || this.item.satuan.nilaikonversi == 0) {
      this.item.nilaiKonversi = 1
    } else {
      this.item.nilaiKonversi = this.item.satuan.nilaikonversi
    }

    if (jml == 0) {
      this.item.qtyUsulan = 0;
      this.item.jumlah = 0;
      this.item.hargaSatuan = 0;
      this.item.persenDiskon = 0;
      this.item.hargaDiskon = 0;
      this.item.persenPpn = 0;
      this.item.hargaPpn = 0;
      this.item.total = 0
      this.item.nobatch = '-';
      // this.item.tglExp = this.dateNow;
      this.apiService.get("general/get-produkdetail-general?" +
        "produkfk=" + this.item.produk.id +
        "&ruanganfk=" + this.item.ruangan.id).subscribe(dat => {
          var dataProdukDetail = dat.detail;
          if (dataProdukDetail.length > 0) {
            this.item.hargaSatuan = dataProdukDetail[0].harganetto
          }
        });
    } else {
      this.item.qtyUsulan = parseFloat(this.dataSelected.jumlahusulan);
      this.item.jumlah = parseFloat(this.dataSelected.jumlah);
      this.item.hargaSatuan = parseFloat(this.dataSelected.hargasatuan);
      this.item.persenDiskon = parseFloat(this.dataSelected.persendiscount);
      this.item.hargaDiskon = 0;
      this.item.persenPpn = parseFloat(this.dataSelected.persenppn);
      this.item.hargaPpn = 0;
      this.item.total = this.dataSelected.total;
      this.item.nobatch = this.dataSelected.nobatch;
      this.item.keterangan = this.dataSelected.keterangan;
      if (this.dataSelected.tglkadaluarsa != undefined) {
        this.item.tglExp = new Date(moment(this.dataSelected.tglkadaluarsa).format('YYYY-MM-DD HH:mm'));
      }
    }
    this.statusTambah = true
    this.gettotal();
  }

  getNilaiKonversi() {
    this.item.nilaiKonversi = this.item.satuan.nilaikonversi
  }

  onChangeKonversi() {
    if (this.item.jumlah > 0) {
      this.item.jumlah = 1 * parseFloat(this.item.nilaiKonversi);
    }
  }

  onChangeQty(e) {
    this.item.jumlah = e.value;
    this.gettotal();
  }

  onChangeHarga(e) {
    this.item.hargaSatuan = e;
    this.gettotal();
  }

  onChangeDiskon(e) {
    this.item.persenDiskon = e.value;
    this.gettotal();
  }

  onChangePpn(e) {
    this.item.persenPpn = e.value;
    this.gettotal();
  }

  gettotal() {
    var ada = false;
    if (this.item.jumlah * parseFloat(this.item.nilaiKonversi) > 0) {
      ada = false
      var qty: any = parseFloat(this.item.jumlah) * parseFloat(this.item.nilaiKonversi)
      var hargasatuan: any = 0
      if (this.item.hargaSatuan != undefined) {
        hargasatuan = this.item.hargaSatuan;
      }
      var persendiskon: any = 0
      var diskon: any = 0;
      if (this.item.persenDiskon != undefined) {
        persendiskon = this.item.persenDiskon;
        diskon = (parseFloat(hargasatuan) * parseFloat(persendiskon)) / 100;
      }
      var persenppn: any = 0;
      var ppn: any = 0;
      if (this.item.persenPpn != undefined) {
        persenppn = this.item.persenPpn;
        // ppn = (parseFloat(hargasatuan)*parseFloat(persenppn))/100;
      }
      var totalharga: any = 0;
      var totaldiskon: any = 0;
      var totalppn: any = 0;
      totalharga = parseFloat(hargasatuan) * qty;
      totaldiskon = diskon * qty;
      totalppn = ((totalharga - totaldiskon) * persenppn) / 100;
      this.item.hargaDiskon = diskon;
      this.item.hargaPpn = totalppn;
      this.item.total = (totalharga - totaldiskon) + totalppn;
      ada = true;
    }
    if (ada == false) {
      this.item.hargaSatuan = 0
      this.item.persenDiskon = 0
      this.item.persenPpn = 0
      this.item.hargaPpn = 0
      this.item.hargaDiskon = 0
      this.item.total = 0
    }
    if (this.item.jumlah == 0) {
      this.item.hargaSatuan = 0
      this.item.hargaSatuan = 0
      this.item.persenDiskon = 0
      this.item.persenPpn = 0
      this.item.hargaPpn = 0
      this.item.hargaDiskon = 0
      this.item.total = 0
    }
  }

  Kosongkan() {
    this.dataSelected = undefined;
    delete this.item.produk;
    delete this.item.satuan;
    delete this.item.nilaiKonversi;
    this.item.no = undefined;
    this.item.qtyUsulan = 0;
    this.item.jumlah = 0;
    this.item.hargaSatuan = 0
    this.item.persenDiskon = 0
    this.item.persenPpn = 0
    this.item.hargaPpn = 0
    this.item.hargaDiskon = 0
    this.item.total = 0
    this.item.Nobatch = undefined;
    this.item.keterangan = undefined;
    this.item.tglExp = undefined;
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
    this.apiService.get("logistik/get-combo-produk-penerimaan?idproduk=" + dataSelected.produkfk)
      .subscribe(re => {
        this.listProduk = re;
        for (var i = this.listProduk.length - 1; i >= 0; i--) {
          if (this.listProduk[i].id == dataSelected.produkfk) {
            dataProduk = this.listProduk[i]
            break;
          }
        }
        this.item.produk = dataProduk
        this.GETKONVERSI(dataSelected.jumlah)
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

    if (this.item.ruangan == undefined) {
      this.alertService.error("Info", "Data Ruangan Penerima Masih Kosong!");
      return;
    }

    if (this.item.dataAsalProduk == undefined) {
      this.alertService.error("Info", "Data Sumber Dana / Asal Produk Masih Kosong!");
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
          data.hargasatuan = String(this.item.hargaSatuan)
          data.ruanganfk = this.item.ruangan.id
          data.asalprodukfk = this.item.dataAsalProduk.id
          data.asalproduk = this.item.dataAsalProduk.asalproduk
          data.produkfk = this.item.produk.id
          data.namaproduk = this.item.produk.namaproduk
          data.nilaikonversi = this.item.nilaiKonversi
          data.satuanstandarfk = this.item.satuan.ssid
          data.satuanstandar = this.item.satuan.satuanstandar
          data.satuanviewfk = this.item.satuan.ssid
          data.satuanview = this.item.satuan.satuanstandar
          data.jumlahusulan = this.item.qtyUsulan
          data.jumlah = this.item.jumlah
          data.subtotal = parseFloat(this.item.jumlah) * parseFloat(this.item.hargaSatuan)
          data.hargadiscount = String(this.item.hargaDiskon)
          data.persendiscount = String(this.item.persenDiskon)
          data.totaldiskon = parseFloat(this.item.jumlah) * parseFloat(this.item.hargaDiskon)
          data.ppn = String(this.item.hargaPpn)
          data.persenppn = String(this.item.persenPpn)
          data.total = this.item.total
          data.keterangan = this.item.keterangan != undefined ? this.item.keterangan : null,
            data.nobatch = this.item.nobatch != undefined ? this.item.nobatch : null
          data.tglkadaluarsa = this.item.tglExp != undefined ? moment(this.item.tglExp).format('YYYY-MM-DD HH:mm') : null

          this.data2[i] = data;
          this.dataSource = this.data2;
          var totalharga: any = 0;
          var totaldiskon: any = 0;
          var totalppn: any = 0;
          var totalall: any = 0;
          for (let i = 0; i < this.data2.length; i++) {
            const element = this.data2[i];
            totalharga = totalharga + parseFloat(element.subtotal);
            totaldiskon = totaldiskon + parseFloat(element.totaldiskon);
            totalppn = totalppn + parseFloat(element.ppn);
          }
          totalall = (totalharga - totaldiskon) + totalppn;
          this.item.totalHarga = 0;
          this.item.totalDiskon = 0;
          this.item.totalPpn = 0;
          this.item.TotalAll = 0;
          this.item.totalHarga = this.formatRupiah(totalharga, "Rp.");
          this.item.totalDiskon = this.formatRupiah(totaldiskon, "Rp.");
          this.item.totalPpn = this.formatRupiah(totalppn, "Rp.");
          this.item.TotalAll = this.formatRupiah(totalall, "Rp.");
        }
      }
    } else {
      data = {
        no: nomor,
        hargasatuan: String(this.item.hargaSatuan),
        ruanganfk: this.item.ruangan.id,
        asalprodukfk: this.item.dataAsalProduk.id,
        asalproduk: this.item.dataAsalProduk.asalproduk,
        produkfk: this.item.produk.id,
        namaproduk: this.item.produk.namaproduk,
        nilaikonversi: this.item.nilaiKonversi,
        satuanstandarfk: this.item.satuan.ssid,
        satuanstandar: this.item.satuan.satuanstandar,
        satuanviewfk: this.item.satuan.ssid,
        satuanview: this.item.satuan.satuanstandar,
        jumlahusulan: this.item.qtyUsulan,
        jumlah: this.item.jumlah,
        subtotal: parseFloat(this.item.jumlah) * parseFloat(this.item.hargaSatuan),
        hargadiscount: String(this.item.hargaDiskon),
        persendiscount: String(this.item.persenDiskon),
        totaldiskon: parseFloat(this.item.jumlah) * parseFloat(this.item.hargaDiskon),
        persenppn: String(this.item.persenPpn),
        ppn: String(this.item.hargaPpn),
        total: this.item.total,
        keterangan: this.item.keterangan != undefined ? this.item.keterangan : null,
        nobatch: this.item.nobatch != undefined ? this.item.nobatch : null,
        tglkadaluarsa: this.item.tglExp != undefined ? moment(this.item.tglExp).format('YYYY-MM-DD HH:mm') : null
      }
      this.data2.push(data)
      this.dataSource = this.data2;
      var totalharga: any = 0;
      var totaldiskon: any = 0;
      var totalppn: any = 0;
      var totalall: any = 0;
      for (let i = 0; i < this.data2.length; i++) {
        const element = this.data2[i];
        totalharga = totalharga + parseFloat(element.subtotal);
        totaldiskon = totaldiskon + parseFloat(element.totaldiskon);
        totalppn = totalppn + parseFloat(element.ppn);
      }
      totalall = (totalharga - totaldiskon) + totalppn;
      this.item.totalHarga = 0;
      this.item.totalDiskon = 0;
      this.item.totalPpn = 0;
      this.item.TotalAll = 0;
      this.item.totalHarga = this.formatRupiah(totalharga, "Rp.");
      this.item.totalDiskon = this.formatRupiah(totaldiskon, "Rp.");
      this.item.totalPpn = this.formatRupiah(totalppn, "Rp.");
      this.item.TotalAll = this.formatRupiah(totalall, "Rp.");
    }
    this.Kosongkan();
  }

  save() {
    if (this.item.tglTerima == undefined) {
      this.alertService.error("Info", "Tanggal Terima Tidak Boleh Kosong!")
      return
    }
    if (this.item.tglFaktur == undefined) {
      this.alertService.error("Info", "Tanggal Faktur Tidak Boleh Kosong!")
      return
    }
    if (this.item.dataPenanggungJawab == undefined) {
      this.alertService.error("Info", "Penanggung Jawab Tidak Boleh Kosong!")
      return
    }
    if (this.item.dataPetugasPenerima == undefined) {
      this.alertService.error("Info", "Petugas Penerima Tidak Boleh Kosong!")
      return
    }
    if (this.item.dataRekanan == undefined) {
      this.alertService.error("Info", "Supplier Tidak Boleh Kosong!")
      return
    }
    if (this.data2.length == 0) {
      this.alertService.error("Infor", "Data Produk Masih Kosong!")
      return
    }
    this.isSimpan = true;
    var totalharga: any = 0;
    var totaldiskon: any = 0;
    var totalppn: any = 0;
    var totalall: any = 0;
    for (let i = 0; i < this.data2.length; i++) {
      const element = this.data2[i];
      totalharga = totalharga + parseFloat(element.subtotal);
      totaldiskon = totaldiskon + parseFloat(element.totaldiskon);
      totalppn = totalppn + parseFloat(element.ppn);
    }
    totalall = (totalharga - totaldiskon) + totalppn;
    var mataanggaran = null;
    var struk = {
      nostruk: this.norec_data,
      //** DATA USULAN */
      noorder: this.item.nomorPermintaan != undefined ? this.item.nomorPermintaan : null,
      tglorder: this.item.tglPermintaan != undefined ? moment(this.item.tglPermintaan).format('YYYY-MM-DD HH:mm') : null,
      tglkontrak: this.item.tglPermintaan != undefined ? moment(this.item.tglPermintaan).format('YYYY-MM-DD HH:mm') : null,
      nokontrak: this.item.nomorKontrak != undefined ? this.item.nomorKontrak : null,
      tahun: this.item.tahun != undefined ? this.item.tahun : null,
      namapengadaan: this.item.namaPengadaan != undefined ? this.item.namaPengadaan : null,
      norecOrder: this.norecOrder,
      //** END DATA USULAN */
      //** DATA FAKTUR */
      tglfaktur: moment(this.item.tglFaktur).format('YYYY-MM-DD HH:mm'),
      tglstruk: moment(this.item.tglTerima).format('YYYY-MM-DD HH:mm'),
      tglrealisasi: moment(this.item.tglTerima).format('YYYY-MM-DD HH:mm'),
      noterima: this.item.nomorTerima != undefined ? this.item.nomorTerima : null,
      nofaktur: this.item.nomorFaktur != undefined ? this.item.nomorFaktur : null,
      asalproduk: this.item.dataAsalProduk.id,
      //** END DATA FAKTUR */
      //** DATA PETUGAS */
      objectpegawaipenanggungjawabfk: this.item.dataPenanggungJawab.id,
      pegawaimenerimafk: this.item.dataPetugasPenerima.id,
      namapegawaipenerima: this.item.dataPetugasPenerima.namalengkap,
      ruanganfk: this.item.ruangan.id,
      //** END DATA PETUGAS */
      //** DATA SUPPLIER */
      rekananfk: this.item.dataRekanan.id,
      namarekanan: this.item.dataRekanan.namarekanan,
      kelompokproduk: this.item.dataKelompokProduk.id,
      tgljatuhtempo: this.item.tglJatuhTempo != undefined ? moment(this.item.tglJatuhTempo).format('YYYY-MM-DD HH:mm') : null,
      //** END DATA SUPPLIER */    
      ketterima: this.item.Keterangan != undefined ? this.item.Keterangan : null,
      objectmataanggaranfk: mataanggaran,
      qtyproduk: this.data2.length,
      totalharusdibayar: totalall,
      totalppn: totalppn,
      totaldiscount: totaldiskon,
      totalhargasatuan: totalharga,
      norecrealisasi: this.norec_Realisasi,
    }
    var objSave = {
      struk: struk,
      details: this.data2
    }
    this.apiService.post('logistik/save-penerimaan-barang-supplier', objSave).subscribe(dataSave => {
      var faktur = "-"
      if (dataSave.data.nofaktur == undefined) {
        faktur = dataSave.data.nofaktur
      }
      this.apiService.postLog('Simpan Kirim Barang Ruangan', 'Norec strukpelayanan', dataSave.data.norec,
        'Simpan Penerimaan Barang Supplier Dengan Noterima - ' + dataSave.data.struk + ' dan Nofaktur - ' + faktur).subscribe(res => { })
      this.Kosongkan();
      this.data2 = []
      this.dataSource = undefined
      window.history.back();
    });
  }

}
