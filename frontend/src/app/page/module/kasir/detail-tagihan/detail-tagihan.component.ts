import { AfterViewInit, Component, OnInit, ViewChild, DoCheck, KeyValueDiffers, KeyValueDiffer } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';

@Component({
  selector: 'app-detail-tagihan',
  templateUrl: './detail-tagihan.component.html',
  styleUrls: ['./detail-tagihan.component.scss'],
  providers: [ConfirmationService]
})
export class DetailTagihanComponent implements OnInit {
  selectedArr: any[]
  noregistrasi: any
  isClosing: boolean
  item: any = {}
  listRuangAPD: any[] = []
  columnLayanan: any[]
  columnResep: any[]
  dataLayanan: any[]
  dataResep: any[]
  dibayar = 0
  verifTotal = 0
  norec_pd: any
  ruangFilter: any
  indexTab = 0
  listBtn: MenuItem[];
  listBtn2: MenuItem[]
  dataSelected: any
  selectedData: any[] = []
  pop_detailDokter: boolean
  dataSourceDokter: any[]
  selectedDokter: any
  listPegawai: any
  listJenisPelaksana: any[] = []
  pop_kompoen: boolean
  dataSourceKomponen: any[]
  itemD: any = {}
  pop_Tgl: boolean
  numberss = Array(15).map((x, i) => i);
  loading: boolean = false;
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,) { }


  ngOnInit(): void {
    this.route.params.subscribe(params => {
      this.item.noRegistrasi = params['noregistrasi'];
      this.loadData()
    })
    this.loadBtn()
    this.loadColumn()
  }
  cariRegistrasi() {
    this.loadData()
  } 
  loadColumn() {
    this.columnLayanan = [
      { field: 'tglPelayanan', header: 'Tgl Layanan', width: "100px" },
      { field: 'namaPelayanan', header: 'Layanan', width: "200px" },
      { field: 'kelasTindakan', header: 'Kelas', width: "70px" },
      { field: 'dokter', header: 'Dokter', width: "170px" },
      { field: 'paramedis', header: 'P', width: "30px" },
      { field: 'ruanganTindakan', header: 'Ruangan', width: "200px" },
      { field: 'jumlah', header: 'Qty', width: "50px" },
      { field: 'harga', header: 'Harga', width: "120px", isCurrency: true },
      { field: 'diskon', header: 'Diskon', width: "120px", isCurrency: true },
      { field: 'jasa', header: 'Jasa', width: "100px", isCurrency: true },
      { field: 'total', header: 'Total', width: "120px", isCurrency: true },
      { field: 'statuscito', header: 'Cito', width: "70px" },
      { field: 'strukfk', header: 'No Verif/No Bayar', width: "150px" },
    ];
    this.columnResep = [
      { field: 'tglPelayanan', header: 'Tgl Layanan', width: "100px" },
      { field: 'namaPelayanan', header: 'Layanan', width: "200px" },
      { field: 'kelasTindakan', header: 'Kelas', width: "70px" },
      { field: 'dokter', header: 'Dokter', width: "170px" },
      { field: 'paramedis', header: 'P', width: "30px" },
      { field: 'ruanganTindakan', header: 'Ruangan', width: "200px" },
      { field: 'jumlah', header: 'Qty', width: "50px" },
      { field: 'harga', header: 'Harga', width: "120px", isCurrency: true },
      { field: 'diskon', header: 'Diskon', width: "120px", isCurrency: true },
      { field: 'jasa', header: 'Jasa', width: "100px", isCurrency: true },
      { field: 'total', header: 'Total', width: "120px", isCurrency: true },
      { field: 'statuscito', header: 'Cito', width: "70px" },
      { field: 'strukfk', header: 'No Verif/No Bayar', width: "150px" },
    ];
  }
  loadBtn() {
    this.listBtn = [
      { label: 'Billing', icon: 'fa fa-print', command: () => { this.billing(); } },
      { label: 'Rekap Billing', icon: 'fa fa-print', command: () => { this.rekapBilling(); } },
      // { label: 'Billing Total', icon: 'fa fa-print', command: () => { this.billingTotal(); } },
      // { label: 'Billing Selisih Kelas', icon: 'fa fa-print', command: () => { this.billingSelisihKelas(); } },
      { separator: true },
      { label: 'Bukti Layanan', icon: 'fa fa-print', command: () => { this.buktiLayanan(); } },
      // { label: 'Bukti Layanan Jasa', icon: 'pi pi-print', command: () => { this.buktiLayananJasa(); } },
      // { label: 'Bukti Layanan Pertindakan', icon: 'pi pi-pritn', command: () => { this.buktiLayananPertindakan(); } },
      { separator: true },
      { label: 'Rincian Obat', icon: 'pi pi-print', command: () => { this.rincianObat(); } },
      // { label: 'Rekap Billing Ruangan', icon: 'pi pi-print', command: () => { this.rekapBillingRuangan(); } },
      // { label: 'Kwitansi Total', icon: 'pi pi-print', command: () => { this.kwitansiTotal(); } },
      // { label: 'Surat Total Biaya Perawatan', icon: 'pi pi-print', command: () => { this.suratTotal(); } },
    ];
    this.listBtn2 = [
      { label: 'Tambah Iindakan Tidak Terklaim', icon: 'fa fa-plus', command: () => { this.tambahTindakanTerklaim(); } },
      { label: 'Hapus Iindakan Tidak Terklaim', icon: 'fa fa-trash', command: () => { this.hapusTindakanTerklaim(); } },

    ];
  }

  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadGrid(this.item.noRegistrasi, this.ruangFilter != undefined ? this.ruangFilter.id : '', 'layanan')
    } else {
      this.loadGrid(this.item.noRegistrasi, this.ruangFilter != undefined ? this.ruangFilter.id : '', 'resep')
    }

  }
  changeRuangan(e) {
    if (this.indexTab == 0) {
      this.loadGrid(this.item.noRegistrasi, this.ruangFilter != undefined ? this.ruangFilter.id : '', 'layanan')
    } else {
      this.loadGrid(this.item.noRegistrasi, this.ruangFilter != undefined ? this.ruangFilter.id : '', 'resep')
    }

  }
  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }
  loadData() {
    this.loading = true
    this.dataSelected = undefined
    this.isClosing = false
    this.selectedData = []
    this.apiService.get("kasir/get-ruangan-registrasi?noRegistrasi=" + this.item.noRegistrasi).subscribe(e => {
      if (e.listRuangan.length == 0) {
        this.item = { noRegistrasi: this.item.noRegistrasi }
        this.dataLayanan = []
        this.dataResep = []
        this.loading = false
        this.alertService.info('Info', "Data Tidak Ditemukan")
        return
      }
      this.listRuangAPD = e.listRuangan
      for (let i = 0; i < this.listRuangAPD.length; i++) {
        const element = this.listRuangAPD[i];
        if (element.id == e.lastRuangan.id) {
          this.ruangFilter = { id: element.id, namaruangan: element.namaruangan }
          break
        }
      }

      this.apiService.post('rawatinap/save-akomodasi-tea', { noregistrasi: this.item.noRegistrasi }).subscribe(data => {
        this.loadGrid(this.item.noRegistrasi, this.ruangFilter.id, 'layanan')
      })
      this.apiService.get("sysadmin/general/get-status-close/" + this.item.noRegistrasi).subscribe(rese => {
        if (rese.status == true) {
          this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(rese.tglclosing)).format('DD-MMM-YYYY HH:mm'))
          this.isClosing = true
        }
      })


    })
  }

  formatTanggal = function (tanggal) {
    return moment(tanggal).format('DD-MMM-YYYY HH:mm');
  }
  loadGrid(noreg: any, idRuangan: any, jenisdata: any) {
    this.loading = true
    if (jenisdata == undefined)
      jenisdata = 'layanan'
    this.item.bayar = 0
    this.item.billing = 0
    this.item.deposit = 0
    this.item.sisa = 0
    this.apiService.get("kasir/detail-tagihan/" + noreg + '?jenisdata=' + jenisdata + '&idruangan=' + idRuangan).subscribe(data => {
      this.loading = false
      this.dataLayanan = [];
      this.dataResep = [];
      this.dibayar = 0
      this.verifTotal = 0
      if (data.details.length > 0) {
        for (var i = data.details.length - 1; i >= 0; i--) {
          if (data.details[i].strukfk != null) {
            if (data.details[i].strukfk.length > 20) {
              this.dibayar = this.dibayar + data.details[i].total
            }
            if (data.details[i].strukfk.length < 20 && data.details[i].strukfk.length > 5) {
              this.verifTotal = this.verifTotal + data.details[i].total
            }
          }
          if (data.details[i].strukresepidfk == null) {
            this.dataLayanan.push(data.details[i])
          } else {
            this.dataResep.push(data.details[i])
          }
          if (data.details[i].namaPelayanan) {
            if (data.details[i].namaPelayanan.indexOf('Konsul') > -1 && data.details[i].dokter == '-' || data.details[i].dokter == null) {
              this.alertService.warn("Info", 'Mohon isi Dokter Pemeriksa pada layanan '
                + data.details[i].namaPelayanan)
            }
          }
          if (data.details[i].iscito == "1") {
            data.details[i].statuscito = "✔"
          } else {
            data.details[i].statuscito = ""
          }

          if (data.details[i].isparamedis == "1") {
            data.details[i].paramedis = "✔"
          } else {
            data.details[i].paramedis = ""
          }
        }
        if (data.dibayar == null || data.dibayar == undefined) {
          data.bayar = 0
        } else {
          data.bayar = data.dibayar
        }
        if (data.deposit == null || data.deposit == undefined) {
          data.deposit = 0
        } else {
          data.deposit = data.deposit
        }
        data.verifTotal = data.diverif//verifTotal      
        data.sisa = parseFloat(data.billing) - data.dibayar - parseFloat(data.deposit)
        this.item = data;

        this.item.bayarC = this.formatRupiah(this.item.bayar != null ? this.item.bayar : 0, 'Rp. ')
        this.item.billingC = this.formatRupiah(this.item.billing != null ? this.item.billing : 0, 'Rp. ')
        this.item.depositC = this.formatRupiah(this.item.deposit != null ? this.item.deposit : 0, 'Rp. ')
        this.item.sisaC = this.formatRupiah(this.item.sisa != null ? this.item.sisa : 0, 'Rp. ')

        this.norec_pd = data.norec_pd
        this.item.tglPulang = this.formatTanggal(this.item.tglPulang);
        this.item.tglMasuk = this.formatTanggal(this.item.tglMasuk);
        this.item.tgllahir = this.formatTanggal(this.item.tgllahir);


      } else {
        this.item.bayar = 0
        this.item.billing = 0
        this.item.deposit = 0
        this.item.sisa = 0

        this.item.bayarC = this.formatRupiah(this.item.bayar, 'Rp. ')
        this.item.billingC = this.formatRupiah(this.item.billing, 'Rp. ')
        this.item.depositC = this.formatRupiah(this.item.deposit, 'Rp. ')
        this.item.sisa = this.formatRupiah(this.item.sisa, 'Rp. ')

        this.dataLayanan = []
        this.dataResep = []
      }

    });

  }
  cariNoreg() {
    this.loadData()
  }
  ceklisLayanan(e) {
    this.dataSelected = e.data
  }
  ceklisLayananAll(e) {

  }
  unCeklisLayanan(e) {

  }
  detailDokter() {
    if (this.selectedData.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }
    if (this.dataSelected.norec == null) return
    this.pop_detailDokter = true
    this.item.tglPelayanans = this.dataSelected.tglPelayanan
    this.item.namaPelayanans = this.dataSelected.namaPelayanan
    this.loadPetugas()
    this.item.norec_ppp = ''
  }
  loadPetugas() {
    this.apiService.get("tindakan/get-combo")
      .subscribe(da => {
        this.listJenisPelaksana = da.jenispelaksana;
      })
    this.apiService.get('kasir/get-petugasbypelayananpasien?norec_pp=' + this.dataSelected.norec).subscribe(e => {
      this.dataSourceDokter = e.data
    })
  }
  getPegawaiByJenis(id) {
    this.apiService.get("tindakan/get-pegawaibyjenispetugas?idJenisPetugas=" + id.id).subscribe(dat => {
      this.listPegawai = dat.jenispelaksana
    });
  }
  filterDokter(event) {
    if (this.item.jenisPelaksana == undefined) return
    let query = event.query;
    this.apiService.get("tindakan/get-pegawaibyjenispetugas?idJenisPetugas=" + this.item.jenisPelaksana.id + "&namalengkap=" + query
    ).subscribe(re => {
      this.listPegawai = re.jenispelaksana;
    })
  }

  hapusDokter(e) {
    var objSave = {
      pelayananpasienpetugas: {
        norec_ppp: e.norec_ppp
      },
    }
    this.apiService.post('kasir/hapus-ppasienpetugas', objSave).subscribe(e => {
      this.loadPetugas()
    })
  }
  editDokter(e) {
    this.item.norec_ppp = e.norec_ppp
    this.item.jenisPelaksana = { id: e.jpp_id, jenispetugaspe: e.jenispetugaspe }
    this.item.petugasPelaksana = { id: e.pg_id, namalengkap: e.namalengkap }
    if (this.dataSelected.isparamedis == true)
      this.item.paramedis = true
    else
      this.item.paramedis = false
  }
  simpanDokter() {
    if (this.item.jenisPelaksana == undefined && this.item.paramedis == undefined) {
      if (this.item.jenisPelaksana == undefined) {
        this.alertService.error("Info", "Jenis Pelaksana Tidak Boleh Kosong")
        return
      }
      if (this.item.petugasPelaksana == undefined) {
        this.alertService.error("Info", "Pegawai Tidak Boleh Kosong")
        return
      }
    }
    if (this.item.norec_ppp == "") {
      if (this.dataSourceDokter.length > 0) {
        for (let i = 0; i < this.dataSourceDokter.length; i++) {
          if (this.dataSourceDokter[i].jenispetugaspe == this.item.jenisPelaksana.jenispetugaspe) {
            this.alertService.error("Info", "Jenis Pelaksana yg sama sudah ada !")
            return
          }
        }
      }
    }


    var pelayananpasienpetugas = {
      norec_ppp: this.item.norec_ppp,
      norec_pp: this.dataSelected.norec,
      norec_apd: this.dataSelected.norec_apd,
      objectjenispetugaspefk: this.item.jenisPelaksana != undefined ? this.item.jenisPelaksana.id : undefined,
      objectpegawaifk: this.item.petugasPelaksana != undefined ? this.item.petugasPelaksana.id : undefined,
      isparamedis: this.item.paramedis,
    }

    var objSave = {
      pelayananpasienpetugas: pelayananpasienpetugas,
    }
    this.apiService.post('kasir/save-ppasienpetugas', objSave).subscribe(e => {
      this.item.norec_ppp = ''
      this.item.jenisPelaksana = undefined
      this.item.petugasPelaksana = undefined
      this.loadPetugas()
    })
  }
  komponenHarga() {
    if (this.selectedData.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }
    if (this.dataSelected.norec == null) return
    this.pop_kompoen = true
    this.item.tglPelayanans = this.dataSelected.tglPelayanan
    this.item.namaPelayanans = this.dataSelected.namaPelayanan
    this.loadKomponen()
  }
  loadKomponen() {
    this.apiService.get('kasir/get-komponenharga-pelayanan?norec_pp=' + this.dataSelected.norec).subscribe(e => {
      this.dataSourceKomponen = e.data
    })
  }
  changeDiskon(e) {
    if (e > 100) {
      e.persenDiscount = "";
    }
    this.itemD.diskonKomponen = ((parseFloat(this.itemD.komponenDis)) * this.itemD.persenDiscount) / 100
  }
  simpanDiskon() {
    if (this.dataSelected.strukfk != " / ") {
      this.alertService.error("Info", 'Sudah di Verifikasi Tatarekening tidak bisa diskon!')
      return
    }

    var objSave = {
      norec_ppd: this.itemD.norec,
      norec_pp: this.itemD.norec_pp,
      hargadiskon: this.itemD.diskonKomponen,
      hargakomponen: this.itemD.komponenDis,
      hargajasa: this.itemD.JasaKomponen,
    }
    this.apiService.post('kasir/save-update-harga-diskon-komponen', objSave).subscribe(data => {
      this.itemD.norec_pp = undefined
      this.itemD.norec = undefined
      this.loadData()
      this.loadKomponen();
    });

  }
  editKomponen(e) {
    this.itemD.norec_pp = e.norec_pp
    this.itemD.norec = e.norec
    this.itemD.label = e.komponenharga;
    this.itemD.komponenDis = e.hargasatuan;
    this.itemD.persenDiscount = undefined;
    this.itemD.diskonKomponen = undefined;
    this.itemD.JasaKomponen = e.jasa;

  }
  ubahTanggal() {

    if (this.selectedData.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }
    if (this.dataSelected.norec == null) return
    this.pop_Tgl = true
    this.itemD.tglPelayanans = new Date(this.dataSelected.tglPelayanan)
    this.item.namaPelayanans = this.dataSelected.namaPelayanan
  }
  simpanTgl() {

    var objSave = {
      norec_pp: this.dataSelected.norec,
      tanggalPelayanan: moment(this.itemD.tglPelayanans).format('YYYY-MM-DD HH:mm:ss')
    }
    this.apiService.post('kasir/save-update-tanggal_pelayanan', objSave).subscribe(data => {
      // $scope.saveLogging('Ubah Tgl Pelayanan', 'norec Pelayanan Pasien', $scope.dataSelected.norec, 'menu Detail Tagihan')
      this.loadData()
      this.pop_kompoen = false
    });

  }
  hapusTindakan() {
    if (this.isClosing == true) {
      this.alertService.error("Info", "Data Sudah Diclosing!");
      return;
    }

    if (this.indexTab == 1) {
      this.alertService.error("Info", "Data Resep Tidak Bisa Dihapus, Harap Hubungi Farmasi!");
      return;
    }
    if (this.selectedData.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }
    let dataDel = []
    let logData = []
    for (let i = 0; i < this.selectedData.length; i++) {
      const items = this.selectedData[i];
      if (items.strukfk != " / ") {
        this.alertService.error("Info", "Pelayanan yang sudah di Verif tidak bisa di ubah!");
        return;
      }
      logData.push(items);
      var objDel = {
        "norec_pp": items.norec,
      }
      dataDel.push(objDel)
    }
    this.nextHapus(dataDel, logData)
  }
  nextHapus(deletes: any, log: any) {
    var objDelete = {
      "dataDel": deletes,
    };
    this.apiService.post('kasir/delete-pelayanan-pasien', objDelete).subscribe(e => {
      var namaPROD = this.multiSelectArrayToString2(log)
      this.apiService.postLog('Hapus Tindakan', 'norec PP', '', 'Hapus Tindakan : ('
        + namaPROD + ') pada No Registrasi ' + this.item.noRegistrasi + ' di ' + log[0].ruanganTindakan).subscribe(z => { })

      this.loadData();
    })
  }
  multiSelectArrayToString2(item) {
    if (item.length > 0) {
      return item.map(function (elem) {
        return elem.namaPelayanan
      }).join(", ");
    }
  }
  inputTindakan() {
    if (this.isClosing == true) {
      this.alertService.error("Info", "Data Sudah Diclosing!");
      return;
    }

    if (this.selectedData.length == 0) {
      this.alertService.error("Info", "Ceklis Salah Satu pelayanan dulu!");
      return;
    }
    this.router.navigate(['input-tindakan', this.dataSelected.norec_pd, this.dataSelected.norec_apd])

  }
  hapusTindakanTerklaim() {
    throw new Error('Method not implemented.');
  }
  tambahTindakanTerklaim() {
    throw new Error('Method not implemented.');
  }
  suratTotal() {
    throw new Error('Method not implemented.');
  }
  kwitansiTotal() {
    throw new Error('Method not implemented.');
  }
  rekapBillingRuangan() {
    throw new Error('Method not implemented.');
  }
  rincianObat() {
    if (this.item.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }

    this.apiService.get("kasir/detail-tagihan/" + this.item.noRegistrasi + '?jenisdata=bill').subscribe(dat => {
      var stt = 'false'
      if (confirm('View Rincian Obat? ')) {
        stt = 'true';
      } else {
        stt = 'false'
      }
      this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-RincianBiayaObatAlkes=1&noregistrasi=" +
        this.item.noRegistrasi + "&user=" + this.authService.getDataLoginUser().pegawai.namaLengkap
        + "&view=" + stt, function (e) { });
    });
  }
  buktiLayananPertindakan() {
    throw new Error('Method not implemented.');
  }
  buktiLayananJasa() {
    throw new Error('Method not implemented.');
  }
  buktiLayanan() {
    if (this.item.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }

    if (this.selectedData == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }

    var stt = 'false'
    if (confirm('View Bukti Layanan? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-buktilayanan=1&noregistrasi=" +
      this.item.noRegistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&idRuangan=" + this.selectedData[0].ruid + "&view=" + stt, function (e) { });
  }
  billingSelisihKelas() {
    throw new Error('Method not implemented.');
  }
  billingTotal() {
    throw new Error('Method not implemented.');
  }
  rekapBilling() {
    if (this.item.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }

    this.apiService.get("kasir/detail-tagihan/" + this.item.noRegistrasi + '?jenisdata=bill').subscribe(dat => {
      var stt = 'false'
      if (confirm('View Rekap Rincian Biaya? ')) {
        stt = 'true';
      } else {
        stt = 'false'
      }
      this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-RekapRincianBiaya=1&noregistrasi=" +
        this.item.noRegistrasi + "&user=" + this.authService.getDataLoginUser().pegawai.namaLengkap
        + "&view=" + stt, function (e) { });
    });
  }
  billing() {
    if (this.item.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }

    this.apiService.get("kasir/detail-tagihan/" + this.item.noRegistrasi + '?jenisdata=bill').subscribe(dat => {
      var stt = 'false'
      if (confirm('View Rincian Biaya? ')) {
        stt = 'true';
      } else {
        stt = 'false'
      }
      this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-RincianBiaya=1&noregistrasi=" +
        this.item.noRegistrasi + "&user=" + this.authService.getDataLoginUser().pegawai.namaLengkap
        + "&view=" + stt, function (e) { });
    });
  }
}
