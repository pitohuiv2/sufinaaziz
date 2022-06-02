import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-transaksi-pelayanan-apotik',
  templateUrl: './transaksi-pelayanan-apotik.component.html',
  styleUrls: ['./transaksi-pelayanan-apotik.component.scss'],
  providers: [ConfirmationService]
})
export class TransaksiPelayananApotikComponent implements OnInit {
  norec_pd: any;
  jenisData: any;
  norecResep: any;
  norec_apd: any;
  norecData: any;
  params: any = {}
  item: any = {
    pasien: {},
  }
  numberss = Array(13).map((x, i) => i);
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  columnGrid: any[];
  dataSource: any[];
  selected: any;
  listBtnCetak: MenuItem[];
  listBtnBridging: MenuItem[];
  pop_riwayatResep: boolean;
  dataSourceRr: any[];
  columnGridRr: any[];
  pop_inputResep: boolean;
  listRuanganApd: any[];
  israwatInap: any;
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
    this.israwatInap = false;
    this.loadColumnButton();
    this.firstLoad();
  }

  loadColumnButton() {
    this.listBtnCetak = [
      { label: 'Cetak SEP', icon: 'fa fa-print', command: () => { this.ctkSep(); } },
      // { label: 'Cetak Label Identitas', icon: 'fa fa-print', command: () => { this.ctkLabelIdentitas(); } },
      // { label: 'Cetak Rekap Label', icon: 'fa fa-print', command: () => { this.ctkRekapLabel(); } },
      { label: 'Cetak Label Resep', icon: 'fa fa-print', command: () => { this.ctkLabelResep(); } },
      { label: 'Cetak Resep Obat', icon: 'fa fa-print', command: () => { this.ctkResepObat(); } },
      { label: 'Cetak Rincian Obat', icon: 'fa fa-print', command: () => { this.ctkRincianObat(); } },
      // { label: 'Cetak Label Biru', icon: 'fa fa-print', command: () => { this.ctkLabelBiru(); } },
    ];
    this.columnGrid = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglpelayanan', header: 'Tgl Pelayanan', width: "145px" },
      { field: 'noresep', header: 'No Resep', width: "140px" },
      { field: 'namaruangan', header: 'Ruang Rawat', width: "200px" },
      { field: 'namaruangandepo', header: 'Depo', width: "140px" },
      { field: 'rke', header: 'R/ke', width: "100px" },
      { field: 'jeniskemasan', header: 'Kemasan', width: "130px" },
      { field: 'namaproduk', header: 'Nama Obat', width: "200px" },
      { field: 'satuanstandar', header: 'Satuan', width: "120px" },
      { field: 'jumlah', header: 'Qty', width: "100px" },
      { field: 'hargasatuan', header: 'Harga', width: "120px", isCurrency: true },
      { field: 'hargadiscount', header: 'Diskon', width: "120px", isCurrency: true },
      { field: 'jasa', header: 'Jasa', width: "120px", isCurrency: true },
      { field: 'total', header: 'Total', width: "120px", isCurrency: true },
      { field: 'kronis', header: 'Kronis', width: "100px" },
      { field: 'nostruk', header: 'No Struk', width: "140px" },
      { field: 'tglkadaluarsa', header: 'Tgl Exp', width: "140px" },
      { field: 'cekreseppulang', header: 'Resep Pulang', width: "140px" },
    ];
    this.columnGridRr = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglpelayanan', header: 'Tgl Pelayanan', width: "145px" },
      { field: 'noresep', header: 'No Resep', width: "140px" },
      { field: 'namaruangan', header: 'Ruang Rawat', width: "200px" },
      { field: 'namaruangandepo', header: 'Depo', width: "140px" },
      { field: 'rke', header: 'R/ke', width: "100px" },
      { field: 'jeniskemasan', header: 'Kemasan', width: "130px" },
      { field: 'namaproduk', header: 'Nama Obat', width: "200px" },
      { field: 'satuanstandar', header: 'Satuan', width: "120px" },
      { field: 'jumlah', header: 'Qty', width: "100px" },
      { field: 'hargasatuan', header: 'Harga', width: "120px", isCurrency: true },
      { field: 'hargadiscount', header: 'Diskon', width: "120px", isCurrency: true },
      { field: 'jasa', header: 'Jasa', width: "120px", isCurrency: true },
      { field: 'total', header: 'Total', width: "120px", isCurrency: true },
      { field: 'kronis', header: 'Kronis', width: "100px" },
      { field: 'cekreseppulang', header: 'Resep Pulang', width: "140px" },
    ];
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  firstLoad() {
    this.route.params.subscribe(params => {
      this.params.norec_pd = params['norec_pd'];
      this.norec_pd = params['norec_pd'];
      this.jenisData = params['jenisdata'];
    });
    var Cache = this.cacheHelper.get("rincianPelayananFarmasiCache");
    if (Cache != undefined) {
      if (Cache[0] != "") {
        this.norecResep = Cache[0];
      }
      if (Cache[1] != "") {
        this.norec_apd = Cache[1];
      }
    }
    if (this.params.norec_pd != undefined) {
      this.apiService.get("general/get-pasien-byregistrasiruangan-general?norec_pd=" + this.params.norec_pd).subscribe(e => {
        e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
        e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date(e.tglregistrasi));
        if (e.instalasiidfk == 2) {
          this.israwatInap = true;
        }
        this.item.pasien = e;
        this.loadDataResep();
      })
    } else {
      this.alertService.warn("Info", "Data Tidak Ditemukan !");
      return;
    }
  }

  loadDataResep() {
    if (this.jenisData == "pasien") {
      this.apiService.get("farmasi/get-transaksi-pelayanan?norec_pd=" + this.norec_pd).subscribe(data => {
        var datas = data;
        var total = 0
        for (let i = 0; i < datas.length; i++) {
          const element = datas[i];
          element.no = i + 1;
          if (element.iskronis == true || element.iskronis == 't') {
            element.kronis = "✔"
          } else {
            element.kronis = "-"
          }
          if (element.reseppulang == '1') {
            element.cekreseppulang = '✔'
          } else {
            element.cekreseppulang = '-'
          }
          element.total = parseFloat(element.jumlah) * (parseFloat(element.hargasatuan) - parseFloat(element.hargadiscount));
          element.total = parseFloat(element.total) + parseFloat(element.jasa)
          total = total + element.total;
        }
        this.item.totalSubTotal = this.formatRupiah(total, "Rp.")
        this.dataSource = datas;
      });
    } else if (this.jenisData == "resep") {
      this.apiService.get("farmasi/get-transaksi-pelayanan?norec_resep=" + this.norecResep).subscribe(data => {
        var datas = data;
        for (let i = 0; i < datas.length; i++) {
          const element = datas[i];
          element.no = i + 1;
          if (element.iskronis == true || element.iskronis == 't') {
            element.kronis = "✔"
          } else {
            element.kronis = "-"
          }
          if (element.reseppulang == '1') {
            element.cekreseppulang = '✔'
          } else {
            element.cekreseppulang = '-'
          }
          element.total = parseFloat(element.jumlah) * (parseFloat(element.hargasatuan) - parseFloat(element.hargadiscount));
          element.total = parseFloat(element.total) + parseFloat(element.jasa)
        }
        this.dataSource = datas;
      });
    } else if (this.jenisData == "antrian") {
      this.apiService.get("farmasi/get-transaksi-pelayanan?norec_resep=" + this.norecData).subscribe(data => {
        var datas = data;
        for (let i = 0; i < datas.length; i++) {
          const element = datas[i];
          element.no = i + 1;
          if (element.iskronis == true || element.iskronis == 't') {
            element.kronis = "✔"
          } else {
            element.kronis = ""
          }
          if (element.reseppulang == '1') {
            element.cekreseppulang = '✔'
          } else {
            element.cekreseppulang = '-'
          }
          element.total = parseFloat(element.jumlah) * (parseFloat(element.hargasatuan) - parseFloat(element.hargadiscount));
          element.total = parseFloat(element.total) + parseFloat(element.jasa)
        }
        this.dataSource = datas;
      });
    }
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
        if (data.length > 0) {
          this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
          return;
        } else {
          this.selected = event.data
        }
      })
    }
  }

  tambahResep() {
    if (this.item.pasien == undefined) {
      this.alertService.warn("Info", "Data Pasien Tidak Ditemukan!");
      return;
    }
    this.item.norec_dpr = ''
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.apiService.get("registrasi/get-data-antrian-pasien?noregistrasi=" + this.item.pasien.noregistrasi).subscribe(data => {
          var datas = data;
          this.listRuanganApd = datas.ruangan;
          if (datas.ruangan != undefined) {
            this.item.dataRuanganApd = datas.ruangan[0]
            this.item.norec_dpr = datas.ruangan[0].norec_apd
            this.pop_inputResep = true
          }
        })
      }
    })
  }

  inputObat() {
    if (this.item.dataRuanganApd == undefined) {
      this.alertService.warn("Info", "Ruang Antrian Belum Dipilih!");
      return;
    }
    var arrStr = {
      0: this.item.pasien.nocm,
      1: this.item.pasien.namapasien,
      2: this.item.pasien.jeniskelamin,
      3: this.item.pasien.noregistrasi,
      4: this.item.pasien.umur,
      5: this.item.pasien.objectkelasfk,
      6: this.item.pasien.namakelas,
      7: this.item.pasien.tglregistrasi,
      8: this.item.dataRuanganApd.norec_apd,
      9: '',
      10: this.item.pasien.kelompokpasien,
      11: this.item.dataRuanganApd.ruangan,
      12: this.item.pasien.alamatlengkap,
      13: '',
      14: '',
      15: '',
      16: ''
    }
    this.cacheHelper.set('InputResepPasienCtrl', arrStr);
    this.router.navigate(['input-resep-apotik', this.item.pasien.norec_pd, this.item.dataRuanganApd.norec_apd]);
  }

  ubahResep() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    if (this.item.pasien == undefined) {
      this.alertService.warn("Info", "Data Pasien Tidak Ditemukan!");
      return;
    }
    var arrStr = {
      0: this.selected.nocm,
      1: this.selected.namapasien,
      2: this.selected.jeniskelamin,
      3: this.selected.noregistrasi,
      4: this.item.pasien.umur,
      5: this.item.pasien.objectkelasfk,
      6: this.item.pasien.namakelas,
      7: this.item.pasien.tglregistrasi,
      8: this.selected.norec_apd,
      9: this.selected.norec_resep,
      10: this.item.pasien.kelompokpasien,
      11: this.selected.namaruangan,
      12: this.item.pasien.alamatlengkap,
      13: '',
      14: '',
      15: '',
      16: 'EditResep'
    }
    this.cacheHelper.set('InputResepPasienCtrl', arrStr);
    this.router.navigate(['input-resep-apotik', this.selected.norec_pd, this.selected.norec_apd]);
  }

  hapusResep() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    var objDelete = {
      norec: this.selected.norec_resep,
    }

    this.confirmationService.confirm({
      message: 'Apakah Anda Yakin Akan Menghapus Resep Ini?',
      header: 'Konfirmasi Hapus Resep',
      icon: 'pi pi-info-circle',
      accept: () => {
        this.confirmationService.close();
        this.apiService.post('farmasi/save-hapus-pelayananobat', objDelete).subscribe(d => {
          this.apiService.postLog('Hapus Resep', 'Norec Transaksi Resep',
            this.selected.norec_resep, 'Hapus Resep No resep: ' + this.selected.noresep
            + '/ Noregistrasi : ' + this.item.pasien.noregistrasi).subscribe(res => { })
          this.loadDataResep();
        })
      },
      reject: (type) => {
        this.alertService.warn('Info, Konfirmasi', 'Hapus Resep Dibatalkan!');
        this.confirmationService.close();
        return;
      }
    });
  }

  riwayatResep() {
    if (this.item.pasien == undefined) {
      this.alertService.warn("Info", "Data Tidak Ditemukan!");
      return;
    }

    this.apiService.get("farmasi/get-transaksi-pelayanan?&noReg=" + this.item.pasien.noregistrasi
      + "&nocm=" + this.item.pasien.nocm).subscribe(table => {
        var dataRr = table
        let group = [];
        if (dataRr != undefined) {
          for (let i = 0; i < dataRr.length; i++) {
            const element = dataRr[i];
            element.no = i + 1;
            element.total = parseFloat(element.jumlah) * (parseFloat(element.hargasatuan) - parseFloat(element.hargadiscount))
            element.total = parseFloat(element.total) + parseFloat(element.jasa)
            if (element.reseppulang == '1') {
              element.cekreseppulang = '✔'
            } else {
              element.cekreseppulang = '-'
            }
          }
        }
        this.dataSourceRr = dataRr;
        this.pop_riwayatResep = true;
      });
  }

  returResep() {
    if (this.selected == undefined) {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }

    var arrStr = {
      0: this.selected.nocm,
      1: this.selected.namapasien,
      2: this.selected.jeniskelamin,
      3: this.selected.noregistrasi,
      4: this.item.pasien.umur,
      5: this.item.pasien.objectkelasfk,
      6: this.item.pasien.namakelas,
      7: this.item.pasien.tglregistrasi,
      8: this.selected.norec_apd,
      9: this.selected.norec_resep,
      10: this.item.pasien.kelompokpasien,
      11: this.selected.namaruangan,
      12: this.item.pasien.alamatlengkap,
      13: 'ReturResep',
    }
    this.cacheHelper.set('ReturResepPasienCache', arrStr);
    this.router.navigate(['input-retur-resep', this.selected.norec_resep]);
  }

  ctkSep() {
    if (this.item.pasien == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    if (this.item.pasien.kelompokpasien === "Umum/Tunai") {
      this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
      return
    }
    var stt = 'false'
    if (confirm('View SEP? ')) {
      stt = 'true';
    } else {
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-sep=1&noregistrasi=" +
      this.item.pasien.noregistrasi + "&qty=1&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&ket=&view=" + stt, function (e) {
      });
  }

  ctkLabelIdentitas() {
    throw new Error('Method not implemented.');
  }

  ctkRekapLabel() {
    throw new Error('Method not implemented.');
  }

  ctkLabelResep() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    var userLogin = this.authService.getDataLoginUser().pegawai.namaLengkap;
    var Norec = this.selected.norec_resep;
    var stt = 'false'
    if (confirm('View Label Farmasi ? ')) {
      stt = 'true';
    } else {
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-LabelFarmasi=1&norecresep=" + Norec
      + "&user=" + userLogin + "&jenisdata=LAYANAN" + "&view=" + stt, function (e) { });
  }

  ctkResepObat() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    var userLogin = this.authService.getDataLoginUser().pegawai.namaLengkap;
    var Norec = this.selected.norec_resep;
    var stt = 'false'
    if (confirm('View Resep Farmasi ? ')) {
      stt = 'true';
    } else {
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-resep=1&norecresep=" + Norec
      + "&user=" + userLogin + "&jenisdata=LAYANAN" + "&view=" + stt, function (e) { });
  }

  ctkLabelBiru() {
    throw new Error('Method not implemented.');
  }

  ctkRincianObat() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    var userLogin = this.authService.getDataLoginUser().pegawai.namaLengkap;
    var Norec = this.selected.norec_resep;
    var stt = 'false'
    if (confirm('View Rincian Obat ? ')) {
      stt = 'true';
    } else {
      stt = 'false'
    }

    if (this.israwatInap == false) {
      this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-rincian-resep-rajal=1&norecresep=" + Norec
        + "&user=" + userLogin + "&view=" + stt, function (e) { });
    } else {
      this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-rincian-resep-ranap=1&norecresep=" + Norec
        + "&user=" + userLogin + "&view=" + stt, function (e) { });
    }
  }
}
