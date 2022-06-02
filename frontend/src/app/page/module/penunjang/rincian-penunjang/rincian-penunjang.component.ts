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
import { ThisReceiver } from '@angular/compiler';

@Component({
  selector: 'app-rincian-penunjang',
  templateUrl: './rincian-penunjang.component.html',
  styleUrls: ['./rincian-penunjang.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class RincianPenunjangComponent implements OnInit, AfterViewInit {
  params: any = {};
  currentNorecPD: any;
  currentNorecAPD: any;
  isClosing: boolean = false;
  item: any = { pasien: {} };
  column: any[];
  selected: any[];
  dataTable: any[];
  norec_apd: any;
  norec_pd: any;
  dataLogin: any;
  kelUser: any;
  listBtn: MenuItem[];
  indexTab = 0;
  pop_detailDokter: boolean
  dataSourceDokter: any[]
  selectedDokter: any
  listPegawai: any
  listJenisPelaksana: any[] = []
  pop_kompoen: boolean
  dataSourceKomponen: any[]
  itemD: any = {}
  pop_Tgl: boolean
  pop_ExpRadiologi: boolean;
  dateNow: any;
  listPegawaiR: any;
  listPegawaiPA: any;
  listPegawaiKirim: any;
  norecHasilRadiologi: any;
  isSimpan: boolean = false;
  pop_ExpLabPA: boolean;
  norecPA: any;
  @ViewChild(HeadPasienComponent, { static: false }) h: HeadPasienComponent;
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

  ngAfterViewInit(): void {
    this.route.params.subscribe(params => {
      this.currentNorecPD = params['norec_rp'];
      this.currentNorecAPD = params['norec_dpr'];
      this.norec_pd = params['norec_rp'];
      this.norec_apd = params['norec_dpr'];
      this.loadHead()
    })
  }

  loadHead() {
    this.isClosing = false
    this.apiService.get("general/get-pasien-bynorec-general?norec_pd="
      + this.currentNorecPD
      + "&norec_apd="
      + this.currentNorecAPD)
      .subscribe(e => {
        e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
        e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
        this.h.item.pasien = e;
        this.item.pasien = e;
        this.apiService.get("sysadmin/general/get-status-close/" + this.item.pasien.noregistrasi).subscribe(rese => {
          if (rese.status == true) {
            this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(rese.tglclosing)).format('DD-MMM-YYYY HH:mm'))
            this.isClosing = true
          }
        })
      })
    this.LoadData();
  }

  ngOnInit(): void {
    this.selected = [];
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.item.SelectedDokterLuar = false;
    this.item.SelectedHistopatologi = true;
    this.item.SelectedSitologi = false;
    this.dateNow = new Date();
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglpelayanan', header: 'Tgl Pelayanan', width: "180px" },
      { field: 'idpatient', header: 'ID Patient di Alat', width: "180px" },
      { field: 'ruangan', header: 'Ruangan', width: "150px" },
      { field: 'namaproduk', header: 'Layanan', width: "180px" },
      { field: 'dokter', header: 'Nama Dokter', width: "250px" },
      { field: 'jumlah', header: 'Jumlah', width: "100px" },
      { field: 'hargasatuan', header: 'Harga', width: "120px", isCurrency: true },
      { field: 'hargadiscount', header: 'Diskon', width: "120px", isCurrency: true },
      { field: 'jasa', header: 'Jasa Cito', width: "120px" },
      { field: 'total', header: 'Total', width: "140px", isCurrency: true },
      { field: 'noorder', header: 'No Order', width: "140px" },
      { field: 'expertise', header: 'Expertise', width: "140px" },
      { field: 'noverifbayar', header: 'No Verif/No Bayar', width: "180px" },
      { field: 'statusbridging', header: 'Bridging', width: "140px" },
      { field: 'pmi', header: 'PMI', width: "140px" },
    ];
    this.loadBtn();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadBtn() {
    this.listBtn = [
      { label: 'Bukti Layanan', icon: 'fa fa-print', command: () => { this.buktiLayanan(); } },
    ];
  }

  LoadData() {
    var KelUserid = "&KelUserid=" + this.dataLogin.kelompokUser.id;
    var KelUser = "&KelUser=" + this.dataLogin.kelompokUser.kelompokUser;
    this.apiService.get("penunjang/get-rincian-pelayanan?norec_apd=" + this.norec_apd + KelUserid + KelUser).subscribe(table => {
      var data = table.data;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
        element.noverifbayar = (element.nostruk != null ? element.nostruk : '') + ' / ' + (element.nosbm != null ? element.nosbm : '');
        if (element.statusbridging == "Sudah Dikirim") {
          element.statusbridging = "✔";
        } else {
          element.statusbridging = "✘";
        }
        if (element.iscito == "1") {
          element.statuscito = "✔";
        } else {
          element.statuscito = "✘";
        }
        if (element.hr_norec != undefined && element.hr_norec != '') {
          element.expertise = "✔";
        } else {
          element.expertise = "✘";
        }
      }
      this.dataTable = data;
    });
  }

  buktiLayanan() {
    throw new Error('Method not implemented.');
  }

  inputTindakan() {
    if (this.isClosing == true) {
      this.alertService.error("Info", "Data Sudah Diclosing!");
      return;
    }
    this.router.navigate(['input-tindakan', this.norec_pd, this.norec_apd])
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
    if (this.selected.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }
    let dataDel = []
    let logData = []
    for (let i = 0; i < this.selected.length; i++) {
      const items = this.selected[i];
      if (items.noverifbayar != " / ") {
        this.alertService.error("Info", "Pelayanan yang sudah di Verif tidak bisa di ubah!");
        return;
      }
      logData.push(items);
      var objDel = {
        "norec_pp": items.norec_pp,
      }
      dataDel.push(objDel)
    }
    this.nextHapus(dataDel, logData)
  }

  multiSelectArrayToString2(item) {
    if (item.length > 0) {
      return item.map(function (elem) {
        return elem.namaproduk
      }).join(", ");
    }
  }

  nextHapus(deletes: any, log: any) {
    var objDelete = {
      "dataDel": deletes,
    };
    this.apiService.post('kasir/delete-pelayanan-pasien', objDelete).subscribe(e => {
      var namaPROD = this.multiSelectArrayToString2(log)
      this.apiService.postLog('Hapus Tindakan', 'norec PP', '', 'Hapus Tindakan : ('
        + namaPROD + ') pada No Registrasi ' + this.item.pasien.noregistrasi + ' di ' + log[0].ruanganTindakan).subscribe(z => { })
      this.LoadData();
    })
  }

  detailDokter() {
    debugger
    if (this.selected == undefined || this.selected.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }

    if (this.selected.length > 1) {
      this.alertService.error("Info", "Ceklis Hanya Satu Data!");
      return;
    }
    if (this.selected[0].norec_pp == null) return
    this.pop_detailDokter = true
    this.item.tglPelayanans = this.selected[0].tglpelayanan
    this.item.namaPelayanans = this.selected[0].namaproduk
    this.loadPetugas()
    this.item.norec_ppp = ''
  }
  loadPetugas() {
    this.apiService.get("tindakan/get-combo")
      .subscribe(da => {
        this.listJenisPelaksana = da.jenispelaksana;
      })
    this.apiService.get('kasir/get-petugasbypelayananpasien?norec_pp=' + this.selected[0].norec_pp).subscribe(e => {
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

  filterDokterR(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listPegawaiR = re;
    })
  }

  filterDokterPA(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listPegawaiPA = re;
    })
  }

  filterDokterKirim(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listPegawaiKirim = re;
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
    if (this.selected[0].isparamedis == true)
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
      norec_pp: this.selected[0].norec_pp,
      norec_apd: this.selected[0].norec_apd,
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
    if (this.selected == undefined || this.selected.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }

    if (this.selected.length > 1) {
      this.alertService.error("Info", "Ceklis Hanya Satu Data!");
      return;
    }
    if (this.selected[0].norec_pp == null) return
    this.pop_kompoen = true
    this.item.tglPelayanans = this.selected[0].tglpelayanan
    this.item.namaPelayanans = this.selected[0].namaproduk
    this.loadKomponen()
  }
  loadKomponen() {
    this.apiService.get('kasir/get-komponenharga-pelayanan?norec_pp=' + this.selected[0].norec_pp).subscribe(e => {
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
    if (this.selected[0].nostruk != " / ") {
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
      this.LoadData();
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

    if (this.selected == undefined || this.selected.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }

    if (this.selected.length > 1) {
      this.alertService.error("Info", "Ceklis Hanya Satu Data!");
      return;
    }
    if (this.selected[0].norec_pp == null) return
    this.pop_Tgl = true
    this.itemD.tglPelayanans = new Date(this.selected[0].tglpelayanan)
    this.item.namaPelayanans = this.selected[0].namaproduk
  }
  simpanTgl() {

    var objSave = {
      norec_pp: this.selected[0].norec_pp,
      tanggalPelayanan: moment(this.itemD.tglPelayanans).format('YYYY-MM-DD HH:mm:ss')
    }
    this.apiService.post('kasir/save-update-tanggal_pelayanan', objSave).subscribe(data => {
      // this.saveLogging('Ubah Tgl Pelayanan', 'norec Pelayanan Pasien', $scope.dataSelected.norec, 'menu Detail Tagihan')
      this.LoadData()
      this.pop_kompoen = false
    });

  }

  ExpertiseR() {
    this.isSimpan = false;
    if (this.selected == undefined || this.selected.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }

    if (this.selected.length > 1) {
      this.alertService.error("Info", "Ceklis Hanya Satu Data!");
      return;
    }
    this.norecHasilRadiologi = "";
    this.item.NamaPelayanan = this.selected[0].namaproduk;
    this.item.tglExpertiseR = moment(this.dateNow).format('YYYY-MM-DD HH:mm');
    this.apiService.get('penunjang/get-hasil-radiologi?norec_pp=' + this.selected[0].norec_pp + '&idproduk=' + this.selected[0].produkfk)
      .subscribe(table => {
        var data = table;
        if (data.length > 0) {
          this.norecHasilRadiologi = data[0].norec
          this.item.tglExpertiseR = data[0].tanggal == null ? new Date() : new Date(data[0].tanggal)
          this.listPegawaiR = { id: data[0].pegawaiidfk, namalengkap: data[0].namalengkap }
          this.item.DokterExpR = { id: data[0].pegawaiidfk, namalengkap: data[0].namalengkap }
          this.item.expertiseRadiologi = (data[0].keterangan == null) ? '' : data[0].keterangan.replace(/~/g, "\n")
        }
        this.pop_ExpRadiologi = true;
      })
  }

  tutupExpR() {
    this.item.NamaPelayanan = undefined;
    this.item.DokterExpR = undefined;
    this.item.expertiseRadiologi = undefined;
    this.item.tglExpertiseR = this.dateNow
    this.pop_ExpRadiologi = false;
  }

  simpanExpR() {

    this.item.SelectedDokterLuar = false;
    if (this.item.tglExpertiseR == undefined) {
      this.alertService.warn("Info", "Tanggal Tidak Boleh Kosong!");
      return;
    }
    if (this.item.DokterExpR == undefined) {
      this.alertService.warn("Info", "Dokter Tidak Boleh Kosong!");
      return;
    }
    if (this.item.expertiseRadiologi == undefined) {
      this.alertService.warn("Info", "Keterangan Tidak Boleh Kosong!");
      return;
    }

    var objSave = {
      noregistrasi: this.item.pasien.noregistrasi,
      tglinput: moment(this.item.tglExpertiseR).format('YYYY-MM-DD HH:mm'),
      dokterid: this.item.DokterExpR.id,
      keterangan: (this.item.expertiseRadiologi == null) ? '' : this.item.expertiseRadiologi.replace(/~/g, "\n"),
      pelayananpasienfk: this.selected[0].norec_pp,
      norec_pd: this.norec_pd,
      norec: this.norecHasilRadiologi

    }
    this.isSimpan = true;
    this.apiService.post('penunjang/save-hasil-radiologi', objSave).subscribe(data => {
      this.apiService.postLog('Simpan Expertise Radiologi', 'norec hasilradiologi', data.strukorder.norec,
        'Hasil Radiologi / Expertise ' + ' pada No Registrasi ' + this.item.pasien.noregistrasi
        + ' Dengan Tindakan ' + this.selected[0].namaproduk).subscribe(z => { })
      this.LoadData();
      this.tutupExpR();
    })
  }

  DokterLuar(event: any) {
    if (event.checked == false) {
      this.item.SelectedDokterLuar = false;
    } else {
      this.item.SelectedDokterLuar = true;
    }
  }

  SlcHis(event: any) {
    if (event.checked == false) {
      this.item.SelectedHistopatologi = false;
    } else {
      this.item.SelectedHistopatologi = true;
      this.item.SelectedSitologi = false;
    }
  }

  SlcSit(event: any) {
    if (event.checked == false) {
      this.item.SelectedSitologi = false;
    } else {
      this.item.SelectedSitologi = true;
      this.item.SelectedHistopatologi = false;
    }
  }

  lihatHasilPa() {
    this.isSimpan = false;
    if (this.selected == undefined || this.selected.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }

    if (this.selected.length > 1) {
      this.alertService.error("Info", "Ceklis Hanya Satu Data!");
      return;
    }
    this.norecPA = "";
    this.item.NamaPelayananPA = this.selected[0].namaproduk;
    this.item.tglExpertisePA = moment(this.dateNow).format('YYYY-MM-DD HH:mm');
    this.apiService.get('penunjang/get-hasil-lab-pa?norec_pp=' + this.selected[0].norec_pp
      + '&idproduk=' + this.selected[0].produkfk).subscribe(table => {
        var data = table;
        if (data.length > 0) {
          var res = data[0]
          this.norecPA = res.norec
          this.item.tglExpertisePA = new Date(res.tanggal)
          this.listPegawaiPA = { id: res.pegawaifk, namalengkap: res.namalengkap }
          this.item.DokterExpPA = { id: res.pegawaifk, namalengkap: res.namalengkap }
          if (res.jenis != null && res.jenis == 'sitologi') {
            this.item.SelectedSitologi = true;
            this.item.SelectedHistopatologi = false;
          } else {
            this.item.SelectedHistopatologi = true;
            this.item.SelectedSitologi = false;
          }
          if (res.dokterpengirimfk) {
            this.item.SelectedDokterLuar = false;
            this.listPegawaiKirim = { id: res.dokterpengirimfk, namalengkap: res.namadokterpengirim };
            this.item.DokterPengirim = { id: res.dokterpengirimfk, namalengkap: res.namadokterpengirim };
          }
          if (res.dokterluar) {
            this.item.SelectedDokterLuar = true;
            this.item.NamaDokterLuar = res.dokterluar
          }
          this.item.DiagnosaKlinik = res.diagnosaklinik
          this.item.KeteranganKlinik = res.keteranganklinik
          this.item.JarianganAsal = res.jaringanasal
          this.item.DiagnosisPB = res.diagnosapb
          this.item.KeteranganPB = res.keteranganpb
          this.item.Topografi = res.topografi
          this.item.Morfologi = res.morfologi
          if (res.makroskopik)
            this.item.Makroskopik = res.makroskopik.replace(/~/g, "\n")
          if (res.mikroskopik)
            this.item.Mikroskopik = res.mikroskopik.replace(/~/g, "\n")
          if (res.kesimpulan)
            this.item.Kesimpulan = res.kesimpulan.replace(/~/g, "\n")
          if (res.anjuran)
            this.item.Anjuran = res.anjuran.replace(/~/g, "\n")
        }
        this.pop_ExpLabPA = true;
      })

  }

  tutupHasilPA() {
    this.item.tglExpertisePA = this.dateNow
    this.item.NamaPelayananPA = undefined;
    this.item.DokterExpPA = undefined;
    this.item.SelectedDokterLuar = false;
    this.item.DokterPengirim = undefined;
    this.item.NamaDokterLuar = undefined;
    this.item.DiagnosaKlinik = undefined;
    this.item.KeteranganKlinik = undefined;
    this.item.JarianganAsal = undefined;
    this.item.DiagnosisPB = undefined;
    this.item.KeteranganPB = undefined;
    this.item.Topografi = undefined;
    this.item.Morfologi = undefined;
    this.item.Makroskopik = undefined;
    this.item.Mikroskopik = undefined;
    this.item.Kesimpulan = undefined;
    this.item.Anjuran = undefined;
    this.pop_ExpLabPA = false;
  }

  simpanExpPA() {
    this.isSimpan = true;
    if (this.item.tglExpertisePA == undefined) {
      this.alertService.warn("Info", "Tanggal Tidak Boleh Kosong!");
      return;
    }
    if (this.item.DokterExpPA == undefined) {
      this.alertService.warn("Info", "Dokter Tidak Boleh Kosong!");
      return;
    }

    var objSave = {
      noregistrasi: this.item.pasien.noregistrasi,
      tglinput: moment(this.item.tglExpertisePA).format('YYYY-MM-DD HH:mm'),
      dokterid: this.item.DokterExpPA.id,
      nomor: null,
      // keterangan: this.itemPA.keterangan.replace(/\n/ig,'~'),
      pelayananpasienfk: this.selected[0].norec_pp,
      jenis: this.item.SelectedSitologi == true ? 'sitologi' : 'histopatologi',
      isDokterLuar: this.item.SelectedDokterLuar != undefined ? this.item.SelectedDokterLuar : null,
      dokterpengirim1: this.item.DokterPengirim != undefined ? this.item.DokterPengirim.id : null,
      dokterpengirim2: this.item.NamaDokterLuar != undefined ? this.item.NamaDokterLuar : null,
      diagnosaklinik: this.item.DiagnosaKlinik != undefined ? this.item.DiagnosaKlinik : null,
      keteranganklinik: this.item.KeteranganKlinik != undefined ? this.item.KeteranganKlinik : null,
      diagnosapb: this.item.DiagnosisPB != undefined ? this.item.DiagnosisPB : null,
      keteranganpb: this.item.KeteranganPB != undefined ? this.item.KeteranganPB : null,
      topografi: this.item.Topografi != undefined ? this.item.Topografi : null,
      morfologi: this.item.Morfologi != undefined ? this.item.Morfologi : null,
      makroskopik: this.item.Makroskopik != undefined ? this.item.Makroskopik.replace(/\n/ig, '~') : null,
      mikroskopik: this.item.Mikroskopik != undefined ? this.item.Mikroskopik.replace(/\n/ig, '~') : null,
      kesimpulan: this.item.Kesimpulan != undefined ? this.item.Kesimpulan.replace(/\n/ig, '~') : null,
      anjuran: this.item.Anjuran != undefined ? this.item.Anjuran.replace(/\n/ig, '~') : null,
      jaringanasal: this.item.JarianganAsal != undefined ? this.item.JarianganAsal : null,
      norec_pd: this.norec_pd,
      norec: this.norecPA
    }
    this.apiService.post('penunjang/save-hasil-lab-pa', objSave).subscribe(data => {
      this.apiService.postLog('Simpan Expertise Lab PA', 'norec hasillaboratoriumpa', data.dataSave.norec,
        'Hasil Laboratorium PA / Expertise ' + ' pada No Registrasi ' + this.item.pasien.noregistrasi
        + ' Dengan Tindakan ' + this.selected[0].namaproduk).subscribe(z => { })
      this.LoadData();
      this.tutupHasilPA();
    })
  }
  hasilLabManual() {
    if (this.selected == undefined || this.selected.length == 0) {
      this.alertService.error("Info", "Ceklis pelayanan dulu!");
      return;
    }

    if (this.selected[0].ruangan.toLowerCase().indexOf('lab') > -1) {
      // this.router.navigate(['hasil-laboratorium', this.norec_pd, this.norec_apd])
      this.router.navigate(['hasil-laboratorium-rev', this.norec_pd, this.norec_apd])
    }

  }


}
