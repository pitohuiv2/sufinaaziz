import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-daftar-order-resep',
  templateUrl: './daftar-order-resep.component.html',
  styleUrls: ['./daftar-order-resep.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarOrderResepComponent implements OnInit {
  selected: any;
  dataTable: any[];
  dateNow: any;
  column: any[];
  item: any = {
    pasien: {},
    order: {}
  };
  listBtnObat: MenuItem[];
  listBtn: MenuItem[];
  listBtnCetak: MenuItem[]
  listRuangan: any[];
  listRuanganFarmasi: any[];
  listStatusPengerjaan: any = [];
  dataLogin: any;
  kelUser: any;
  strukresep: any;
  pop_detailOrder: boolean;
  pop_detailVerifikasi: boolean;
  columnDo: any[];
  dataTableDo: any[];
  pop_penyerahanObat: boolean;
  popFilter: boolean
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
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      // { field: 'no', header: 'No', width: "65px" },
      { field: 'noantri', header: 'No Antri', width: "125px" },
      { field: 'noorder', header: 'No Order', width: "140px" },
      { field: 'statusorder', header: 'Status', width: "120px", isTag: true },
      { field: 'tglorder', header: 'Tgl Order', width: "140px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "140px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "200px" },
      { field: 'namaruanganrawat', header: 'Ruang Order', width: "180px" },
      { field: 'namaruangan', header: 'Depo', width: "180px" },
      { field: 'umur', header: 'Umur', width: "120px" },
      { field: 'namalengkap', header: 'Petugas', width: "180px" },

    ];
    this.columnDo = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'rke', header: 'Rke', width: "80px" },
      { field: 'jeniskemasan', header: 'Jenis Kemasan', width: "140px" },
      { field: 'namaproduk', header: 'Produk', width: "180px" },
      { field: 'satuanstandar', header: 'Satuan', width: "140px" },
      { field: 'aturanpakai', header: 'Aturan Pakai', width: "140px" },
      { field: 'jumlah', header: 'Qty', width: "125px" },
      { field: 'chekreseppulang', header: 'Resep Pulang', width: "140px" },
    ]
    this.loadBtn();
    this.getDataCombo();
    this.listBtn = [
      { label: 'Verifikasi Order', icon: 'fa fa-check-square-o', command: () => { this.verifikasiOrder(); } },
      { label: 'Detail Order', icon: 'fa fa-info-circle', command: () => { this.detailOrder(); } },
      { label: 'Detail Verifikasi', icon: 'fa fa-info-circle', command: () => { this.detailVerifikasi(); } },
      { label: 'Batal Verifikasi', icon: 'fa fa-ban', command: () => { this.batalVerifikasi(); } },
      { label: 'Hapus Order', icon: 'pi pi-user-edit', command: () => { this.hapusOrder(); } },


    ];
  }

  loadBtn() {
    this.listBtnObat = [
      { label: 'Produksi', icon: 'fa fa-pencil-square-o', command: () => { this.produksi(); } },
      { label: 'Packaging', icon: 'fa fa-pencil-square-o', command: () => { this.packaging(); } },
      { label: 'Selesai', icon: 'fa fa-pencil-square-o', command: () => { this.selesai(); } },
      { label: 'Penyerahan Obat', icon: 'fa fa-pencil-square-o', command: () => { this.penyerahanObat(); } },
    ];
    this.listBtnCetak = [
      { label: 'Cetak Antrian', icon: 'fa fa-print', command: () => { this.ctkNoAntrian(); } },
      { label: 'Cetak Label', icon: 'fa fa-print', command: () => { this.ctkLabel(); } },
      // { label: 'Cetak Rekap Label', icon: 'fa fa-print', command: () => { this.ctkRekapLabel(); } },
      // { label: 'Cetak Label Resep', icon: 'fa fa-print', command: () => { this.ctkLabelResep(); } },
      { label: 'Cetak Resep Obat', icon: 'fa fa-print', command: () => { this.ctkResepObat(); } },
    ];
  }

  getDataCombo() {
    this.apiService.get("farmasi/get-combo-farmasi").subscribe(table => {
      var dataCombo = table;
      this.listRuangan = dataCombo.ruanglayanan;
      this.listRuanganFarmasi = dataCombo.ruangfarmasi;
      this.listStatusPengerjaan = dataCombo.statuspengerjaan;
      this.LoadCache();
    });
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('daftarOrderResepElektronikCtrl');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.item.noRM = chacePeriode[2];
      this.item.Noregistrasi = chacePeriode[3];
      this.item.namaPasien = chacePeriode[4];
      this.item.NoOrder = chacePeriode[5];
      this.getData();
    }
    else {
      this.getData();
    }
  }

  getData() {
    var nocm = ''
    if (this.item.noRM != undefined) {
      nocm = '&nocm=' + this.item.noRM
    }

    var noregistrasi = ''
    if (this.item.Noregistrasi != undefined) {
      noregistrasi = '&noregistrasi=' + this.item.Noregistrasi
    }

    var namaPasien = ''
    if (this.item.namaPasien != undefined) {
      namaPasien = '&namaPasien=' + this.item.namaPasien
    }

    var noPesanan = ''
    if (this.item.NoOrder != undefined) {
      noPesanan = '&noPesanan=' + this.item.NoOrder
    }

    var ruanganId = ''
    if (this.item.dataRuangan != undefined) {
      ruanganId = '&ruanganId=' + this.item.dataRuangan
    }

    var depoId = ''
    if (this.item.dataDepo != undefined) {
      depoId = '&depoId=' + this.item.dataDepo.id
    }

    var status = ""
    if (this.item.selectedStatus != undefined) {
      if (this.item.selectedStatus.length != 0) {
        var a = ""
        var b = ""
        for (var i = this.item.selectedStatus.length - 1; i >= 0; i--) {
          var c = this.item.selectedStatus[i].id
          b = "," + c
          a = a + b
        }
        status = a.slice(1, a.length)
      }
    }

    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: this.item.noRM != undefined ? this.item.noRM : null,
      3: this.item.Noregistrasi != undefined ? this.item.Noregistrasi : null,
      4: this.item.namaPasien != undefined ? this.item.namaPasien : null,
      5: this.item.NoOrder != undefined ? this.item.NoOrder : null
    }
    this.cacheHelper.set('daftarOrderResepElektronikCtrl', chacePeriode);
    this.apiService.get("farmasi/get-daftar-order?tglAwal=" + tglAwal + '&tglAkhir=' + tglAkhir
      + nocm + noregistrasi + namaPasien + noPesanan  + '&statusId=' + status + depoId).subscribe(table => {
        var data = table
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          var now = moment(new Date(element.tglregistrasi)).format('YYYY-MM-DD');
          var tgllahir = moment(new Date(element.tgllahir)).format('YYYY-MM-DD');
          var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
          element.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari';

          // if(element.noresep!=null){
          //     element.statusorder = 'Verifikasi'
          // }
          // if (element.noorder == element.noresep) {
          //   if (element.statusorder == 'Menunggu'){
          //     element.statusorder = 'Verifikasi'
          //   }
          // }

          // if (element.statusorder == "Menunggu") {
          //   element.class = 'p-tag p-tag-danger';
          // } else if (element.statusorder == "Verifikasi") {
          //   element.class = 'p-tag p-tag-warning';
          // }else if (element.statusorder == "Produksi") {
          //   element.class = 'p-tag p-tag-info';
          // } else if (element.statusorder == "Produksi") {
          //   element.class = 'p-tag p-tag-success';
          // }
          if (element.checkreseppulang == '1') {
            element.cekreseppulang = '✔'
          } else {
            element.cekreseppulang = '-'
          }
        }
        data.sort(function (a, b) {
          if (a.noantri < b.noantri) { return -1; }
          if (a.noantri > b.noantri) { return 1; }
          return 0;
        })
        this.dataTable = data;
      })
  }

  cari() {
    this.getData();
  }

  onRowSelect(event: any) {
    this.selected = event.data
  }
  selectData(e) {
    this.selected = e
  }
  verifikasiOrder() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.statusorder == 'Menunggu') {
      var arrStr = {
        0: this.selected.norm,
        1: this.selected.namapasien,
        2: this.selected.jeniskelamin,
        3: this.selected.noregistrasi,
        4: this.selected.umur,
        5: this.selected.klid,
        6: this.selected.namakelas,
        7: this.selected.tglregistrasi,
        8: this.selected.norec_apd,
        9: this.selected.norec_order,
        10: this.selected.kelompokpasien,
        11: this.selected.namaruanganrawat,
        12: this.selected.alamat,
        13: '',//this.selected.beratBadan,
        14: '',//this.selected.AlergiYa,
        15: '',
        16: 'OrderResep'
      }
      this.cacheHelper.set('InputResepPasienCtrl', arrStr);
      this.router.navigate(['input-resep-apotik', this.selected.norec_pd, this.selected.norec_apd])

      let params = this.selected.noregistrasi    
      if(this.selected.noreservasi != undefined && this.selected.noreservasi != '' && this.selected.noreservasi !='Kios-K'){
          params = this.selected.noreservasi
      } 
      this.saveAntrol(params,6)
    } else {
      this.alertService.warn("Info,", "Sudah di verifikasi!");
      return;
    }
  }

  detailVerifikasi() {
    var dataSet = []
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.norecresep == undefined) {
      this.alertService.warn("Info,", "Data Belum Diverifikasi!");
      return
    }
    this.item.order = this.selected;
    this.apiService.get("farmasi/get-detail-resep?norecResep=" + this.selected.norecresep).subscribe(table => {
      var data = table.pelayananPasien
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
        if (element.isreseppulang == 1 || element.isreseppulang == '1' || element.isreseppulang == true) {
          element.chekreseppulang = '✔'
        } else {
          element.chekreseppulang = '-'
        }
      }
      this.dataTableDo = data;
      this.pop_detailVerifikasi = true;
    });
  }

  detailOrder() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.noorder.indexOf('TR') > -1) {
      this.alertService.warn("Info,", "Bukan Merupakan order Resep, pilih detail verifikasi!");
      return;
    }

    

    this.item.order = this.selected;
    this.apiService.get("farmasi/get-detail-order?norecOrder=" + this.selected.norec_order).subscribe(table => {
      var data = table.orderpelayanan
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
        if (element.isreseppulang == 1 || element.isreseppulang == '1' || element.isreseppulang == true) {
          element.chekreseppulang = '✔'
        } else {
          element.chekreseppulang = '-'
        }
      }
      this.dataTableDo = data;
      this.pop_detailOrder = true;
    });
  }

  batalVerifikasi() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    if (this.selected.norecresep == undefined) {
      this.alertService.warn("Info,", "Data Belum Diverifikasi!");
      return
    }

    if (this.selected.noorder == this.selected.noresep) {
      this.alertService.warn("Info,", "Data Tidak bisa Unverifikasi!");
      return
    }

    this.apiService.get('farmasi/get-nostruk-kasir?norecresep=' + this.selected.norecresep).subscribe(x => {
      var nostruk = x[0].nostruk
      if (nostruk != null) {
        this.alertService.error("Info,", "Tidak bisa batal verif karena sudah diverif kasir, harap hubungi kasir terlebih dahulu!");
        return
      } else {
        this.confirmationService.confirm({
          message: 'Lanjutkan Batal Verifikasi?',
          header: 'Konfirmasi Batal Verifikasi',
          icon: 'pi pi-info-circle',
          accept: () => {
            this.confirmationService.close();
            var objDelete = {
              norec: this.selected.norecresep,
              norec_order: this.selected.norec_order,
            }

            this.apiService.post('farmasi/save-hapus-pelayananobat', objDelete).subscribe(d => {
              this.apiService.postLog('Batal Verifikasi Resep Elektronik', 'No Order Struk Order',
                this.selected.noorder, 'Batal Verifikasi Resep Elektronik No Order: ' + this.selected.noorder
                + '/ Noregistrasi : ' + this.selected.noregistrasi).subscribe(res => { })
              this.apiService.postLog('Hapus Resep', 'Norec Transaksi Resep',
                this.selected.norecresep, 'Hapus Resep No resep: ' + this.selected.noresep
                + '/ Noregistrasi : ' + this.selected.noregistrasi).subscribe(res => { })
              this.getData();
            })
          },
          reject: (type) => {
            this.alertService.warn('Info, Konfirmasi', 'Batal Verifikasi Dibatalkan!');
            this.confirmationService.close();
            return;
          }
        });
      }
    })
  }

  produksi() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.statusorder == 'Verifikasi') {
      this.strukresep = ""
      if (this.selected.noorder == this.selected.noresep) {
        this.strukresep = true
      }

      var objSave = {
        noorder: this.selected.noorder,
        statusorder: 3,
        tglambil: null,
        namapengambil: null,
        strukresep: this.strukresep
      }
      this.apiService.post('farmasi/save-status-resepelektonik', objSave).subscribe(dataSave => {
        this.apiService.postLog('Rubah Status Produksi Order Resep Elektronik', 'No Order Struk Order',
          this.selected.noorder, 'Rubah Status Produksi Order Resep Elektronik No Order: ' + this.selected.noorder
          + '/ Noregistrasi : ' + this.selected.noregistrasi).subscribe(res => { })
        this.getData()
      });
    } else {
      this.alertService.warn("Info", "Status Data Belum Diverifikasi!")
      return;
    }
  }

  packaging() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.statusorder == 'Verifikasi' || this.selected.statusorder == 'Produksi') {
      this.strukresep = ""
      if (this.selected.noorder == this.selected.noresep) { //resep yang input langsung tanpa order
        this.strukresep = true
      }

      var objSave =
      {
        noorder: this.selected.noorder,
        statusorder: 4,
        tglambil: null,
        namapengambil: null,
        strukresep: this.strukresep
      }
      this.apiService.post('farmasi/save-status-resepelektonik', objSave).subscribe(dataSave => {
        this.apiService.postLog('Rubah Status Packaging Order Resep Elektronik', 'No Order Struk Order',
          this.selected.noorder, 'Rubah Status Packaging Order Resep Elektronik No Order: ' + this.selected.noorder
          + '/ Noregistrasi : ' + this.selected.noregistrasi).subscribe(res => { })
        this.getData();
      });
    } else {
      if (this.selected.statusorder == 'Menunggu') {
        this.alertService.warn("Info,", "Status Data Belum Diverifikasi !");
        return;
      } else {
        this.alertService.warn("Info,", "Status Data Belum Produksi !");
        return;
      }
    }
  }

  selesai() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.statusorder == 'Verifikasi' || this.selected.statusorder == 'Packaging') {
      this.strukresep = ""
      if (this.selected.noorder == this.selected.noresep) {
        this.strukresep = true
      }
      var objSave =
      {
        noorder: this.selected.noorder,
        statusorder: 5,
        tglambil: null,
        namapengambil: null,
        strukresep: this.strukresep
      }
      this.apiService.post('farmasi/save-status-resepelektonik', objSave).subscribe(dataSave => {
        this.apiService.postLog('Rubah Status Selasai Order Resep Elektronik', 'No Order Struk Order',
          this.selected.noorder, 'Rubah Status Selasai Order Resep Elektronik No Order: ' + this.selected.noorder
          + '/ Noregistrasi : ' + this.selected.noregistrasi).subscribe(res => { })
        this.getData();
      });
    } else {
      if (this.selected.statusorder == 'Menunggu') {
        this.alertService.warn("Info,", "Status Data Belum Diverifikasi!");
        return;
      } else {
        this.alertService.warn("Info,", "Status Data Belum Packaging!")
        return;
      }
    }
  }

  penyerahanObat() {    
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.getUrlCetak("http://127.0.0.1:2905/printvb/panggil?displaykeun=1&noantri=" + this.selected.noantri
      + '&namapasien=' + this.selected.norm + ', ' + this.selected.namapasien + '&view=true', function (e) {
      });

    let params = this.selected.noregistrasi    
    if(this.selected.noreservasi != undefined && this.selected.noreservasi != '' && this.selected.noreservasi !='Kios-K'){
        params = this.selected.noreservasi
    } 
    this.saveAntrol(params,7)

    this.alertService.info(this.selected.noantri + ' - ' + this.selected.namapasien, 'Panggil')
    if (this.selected.statusorder == 'Selesai') {
      this.item.tglAmbil = this.dateNow;
      this.pop_penyerahanObat = true;
    } else {
      this.alertService.warn("Info", "Status Data Harus Selesai !!")
      return;
    }
  }

  ctkNoAntrian() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.noantri == undefined) {
      this.alertService.warn("Info,", "Data Belum Diverifikasi!");
      return;
    }
    var userLogin = this.authService.getDataLoginUser().pegawai.namaLengkap;
    var Norec = this.selected.noresep;
    var Noreg = this.selected.noregistrasi;
    var stt = 'false'
    if (confirm('View Antrian Farmasi? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-buktiantrianfarmasi=1&noresep=" + Norec
      + "&noregistrasi=" + Noreg + "&user=" + userLogin + "&view=" + stt, function (e) { });
  }

  ctkLabel() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.norecresep == undefined) {
      this.alertService.warn("Info,", "Data Belum Diverifikasi!");
      return;
    }
    var userLogin = this.authService.getDataLoginUser().pegawai.namaLengkap;
    var Norec = this.selected.norecresep;
    var stt = 'false'
    if (confirm('View Label Farmasi ? ')) {
      stt = 'true';
    } else {
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-LabelFarmasi=1&norecresep=" + Norec
      + "&user=" + userLogin + "&jenisdata=LAYANAN" + "&view=" + stt, function (e) { });
  }

  ctkRekapLabel() {
    throw new Error('Method not implemented.');
  }

  ctkLabelResep() {
    throw new Error('Method not implemented.');
  }

  ctkResepObat() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.norecresep == undefined) {
      this.alertService.warn("Info,", "Data Belum Diverifikasi!");
      return;
    }
    var userLogin = this.authService.getDataLoginUser().pegawai.namaLengkap;
    var Norec = this.selected.norecresep;
    var stt = 'false'
    if (confirm('View Resep Farmasi ? ')) {
      stt = 'true';
    } else {
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-resep=1&norecresep=" + Norec
      + "&user=" + userLogin + "&jenisdata=LAYANAN" + "&view=" + stt, function (e) { });
  }

  batalAmbilObat() {
    this.item.tglAmbil = this.dateNow;
    this.item.namapengambil = undefined;
    this.pop_penyerahanObat = false;
  }

  simpanAmbilObat() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    var nm = ''
    if (this.item.namapengambil != undefined) {
      nm = this.item.namapengambil
    }
    var strukresep: any = ""
    if (this.selected.noorder == this.selected.noresep) {
      strukresep = true
    }
    var objSave = {
      noorder: this.selected.noorder,
      statusorder: 7,
      tglambil: moment(this.item.tglAmbil).format('YYYY-MM-DD HH:mm'),
      namapengambil: nm,
      strukresep: strukresep
    }
    this.apiService.post('farmasi/save-status-resepelektonik', objSave).subscribe(dataSave => {
      this.apiService.postLog('Rubah Status Sudah Di Ambil Order Resep Elektronik', 'No Order Struk Order',
        this.selected.noorder, 'Rubah Sudah Di Ambil Order Resep Elektronik: ' + this.selected.noorder
        + '/ Noregistrasi : ' + this.selected.noregistrasi).subscribe(res => { })
      this.item.tglAmbil = this.dateNow;
      this.item.namapengambil = undefined;
      this.pop_penyerahanObat = false;
      this.getData();
    });
  }
  filter() {
    this.popFilter = true
  }
  cariFilter() {
    this.popFilter = false
    this.getData();
  }
  clearFilter() {
    this.popFilter = false
    this.item = {}
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.getData();
  }
  saveAntrol(param,waktu){
    var data = {
       "url": "antrean/updatewaktu",
       "jenis": "antrean",
       "method": "POST",
       "data":                                                 
       {
          "kodebooking": param,
          "taskid": waktu,//Waktu akhir farmasi/mulai buat obat
          "waktu": new Date().getTime()  
       }
   }
   this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe( e=> {})
  }
  hapusOrder() {
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }
    if (this.selected.statusorder != 'Menunggu') {
      this.alertService.warn("Info!", "Data Sudah di verifikasi");
      return;
    }

    // this.item.pasien = this.selected;
    var itemsave = {
      "norec" : this.selected.norec_order
    }
    this.apiService.post('penunjang/delete-order', itemsave).subscribe(table => {      
            this.getData();
          },error =>{
          }
    );        
  }
}
