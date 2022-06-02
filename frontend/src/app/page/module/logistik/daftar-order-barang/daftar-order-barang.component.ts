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
  selector: 'app-daftar-order-barang',
  templateUrl: './daftar-order-barang.component.html',
  styleUrls: ['./daftar-order-barang.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarOrderBarangComponent implements OnInit {
  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {};
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  listRuangan: any[] = [];
  listRuanganAll: any[];
  listJenisPengiriman: any[];
  listPetugas: any[];
  listjabatan: any[];
  listBtn: MenuItem[];
  pop_tandaTangan: boolean;
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
    this.listBtn = [
      { label: 'Kirim Barang', icon: 'fa fa-paper-plane', command: () => { this.kirimBarang(); } },
      { label: 'Ubah Order', icon: 'fa fa-pencil-square-o', command: () => { this.ubahOrder(); } },
      { label: 'Hapus Order', icon: 'fa fa-trash', command: () => { this.hapusOrder(); } },
      { label: 'Cetak', icon: 'pi pi-print', command: () => { this.cetakBukti(); } },


    ];
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.loadColumn();
    this.getDataCombo();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'status', header: 'Status', width: "180px" },
      { field: 'tglorder', header: 'Tgl Order', width: "140px" },
      { field: 'noorder', header: 'No Order', width: "120px" },
      { field: 'jeniskirim', header: 'Jenis Kirim', width: "140px" },
      { field: 'jmlitem', header: 'Jml Item', width: "140px" },
      { field: 'namaruanganasal', header: 'Ruang Asal', width: "180px" },
      { field: 'namaruangantujuan', header: 'Ruang Tujuan', width: "180px" },
      { field: 'petugas', header: 'Petugas', width: "180px" },
      { field: 'keterangan', header: 'Keterangan', width: "140px" },
      { field: 'jmlitem', header: 'Jml Item', width: "140px" },
      { field: 'statusorder', header: 'Status Order', width: "180px" },
    ];
  }

  getDataCombo() {
    this.apiService.get("logistik/get-combo-logistik").subscribe(table => {
      var dataCombo = table;
      if (this.dataLogin.mapLoginUserToRuangan != undefined) {
        this.listRuangan = this.dataLogin.mapLoginUserToRuangan;
      } else {
        this.listRuangan = dataCombo.ruangfarmasi;
      }
      this.listJenisPengiriman = dataCombo.jeniskirim;
      this.LoadCache();
    });
  }

  filterRuangan(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-ruangan-part?namaruangan=" + query)
      .subscribe(re => {
        this.listRuanganAll = re;
      })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarPermintaanOrderBarangCache');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.loadData();
    } else {
      this.loadData();
    }
  }

  loadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var tempRuanganId = "";
    var tempRuanganIdArr = undefined;
    if (this.item.dataRuangan != undefined) {
      tempRuanganId = this.item.dataRuangan.id;
      tempRuanganIdArr = { id: this.item.dataRuangan.id, namaruangan: this.item.dataRuangan.namaruangan }
    }
    var tempRuanganTId = "";
    var tempRuanganTIdArr = undefined;
    if (this.item.dataRuanganAll != undefined) {
      tempRuanganId = this.item.dataRuanganAll.id;
      tempRuanganIdArr = { id: this.item.dataRuanganAll.id, namaruangan: this.item.dataRuanganAll.namaruangan }
    }
    var tempJenisPengirimanId = "";
    var tempJenisPengirimanArr = undefined;
    if (this.item.dataJenisPengiriman != undefined) {
      tempJenisPengirimanId = this.item.dataJenisPengiriman.id;
      tempJenisPengirimanArr = { id: this.item.dataJenisPengiriman.id, jeniskirim: this.item.dataJenisPengiriman.jeniskirim }
    }
    var tempNoOrder = "";
    if (this.item.noOrder != undefined) {
      tempNoOrder = this.item.noOrder;
    }
    var listRuang = ""
    if (this.listRuangan != undefined) {
      var a = ""
      var b = ""
      for (var i = this.listRuangan.length - 1; i >= 0; i--) {

        var c = this.listRuangan[i].id
        b = "," + c
        a = a + b
      }
      listRuang = a.slice(1, a.length)
    }
    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = this.item.jmlRows
    }
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempRuanganIdArr,
      3: tempRuanganTIdArr,
      4: tempJenisPengirimanArr,
      5: tempNoOrder,
    }
    this.cacheHelper.set('DaftarPermintaanOrderBarangCache', chacePeriode);
    this.apiService.get("logistik/get-data-order-barang-ruangan?"
      + "ruanganasalfk=" + tempRuanganId
      + "&ruangantujuanfk=" + tempRuanganTId
      + "&jeniskirimfk=" + tempJenisPengirimanId
      + "&noorder=" + tempNoOrder
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&ruanganArr=" + listRuang
      + "&jmlRows=" + jmlRow).subscribe(table => {
        var data = table.data
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
        }
        this.dataTable = data;
      })
  }

  cari() {
    this.loadData();
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.selected = event.data
    }
  }

  orderBaru() {
    this.router.navigate(['input-order-barang', "-"]);
  }

  ubahOrder() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    if (this.selected.status == 'Terima Order Barang') {
      this.alertService.error("Info", "Tidak Bisa Mengubah Order Ini!")
      return;
    }
    if (this.selected.statusorder == 'Sudah Kirim') {
      this.alertService.error("Info", "Tidak Bisa Mengubah Data, Barang Sudah Dikirim!")
      return;
    }
    this.router.navigate(['input-order-barang', this.selected.norec]);
  }

  hapusOrder() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    if (this.selected.status == 'Terima Order Barang') {
      this.alertService.error("Info", "Tidak Bisa Menghapus Order Ini!")
      return;
    }
    if (this.selected.statusorder == 'Sudah Kirim') {
      this.alertService.error("Info", "Tidak Bisa Menghapus Data, Barang Sudah Dikirim!")
      return;
    }

    var objSave = {
      norecorder: this.selected.norec
    }

    this.apiService.post('logistik/delete-order-barang-ruangan', objSave).subscribe(dataSave => {
      this.apiService.postLog('Hapus Order Barang Ruangan', 'Norec transaksiorder', this.selected.norec,
        'Hapus Order Barang Ruangan Dengan Noorder - ' + this.selected.noorder).subscribe(res => { })
      this.loadData();
    })
  }

  kirimBarang() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    if (this.selected.status == 'Kirim Order Barang') {
      this.alertService.error("Info", "Tidak Bisa Mengirim Ke Ruangan Sendiri!")
      return;
    }
    if (this.selected.statusorder == 'Sudah Kirim') {
      this.alertService.error("Info", "Tidak Bisa Mengubah Data, Barang Sudah Dikirim!")
      return;
    }
    this.router.navigate(['input-kirim-barang', this.selected.norec, "kirimbarang"]);
  }

  filterPetugas(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-pegawai-part?namalengkap=" + query
    ).subscribe(re => {
      this.listPetugas = re;
    })
  }

  filterJabatan(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-jabatan-part?namajabatan=" + query
    ).subscribe(re => {
      this.listjabatan = re;
    })
  }
  selectData(e) {
    this.selected = e
  }
  cetakBukti() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    this.pop_tandaTangan = true
  }

  batalCetak() {
    this.pop_tandaTangan = false
  }

  lanjutCetak() {
    this.pop_tandaTangan = false
    var petugasMengetahui: any = ""
    if (this.item.PetugasSatu != undefined) {
      petugasMengetahui = this.item.PetugasSatu.id;
    }
    var petugasMeminta: any = ""
    if (this.item.PetugasDua != undefined) {
      petugasMeminta = this.item.PetugasDua.id;
    }

    var jabatanMengetahui: any = ""
    if (this.item.JabatanSatu != undefined) {
      petugasMengetahui = this.item.JabatanSatu.namajabatan;
    }
    var jabatanMeminta: any = ""
    if (this.item.JabatanDua != undefined) {
      petugasMeminta = this.item.JabatanDua.namajabatan;
    }
    var stt = 'false'
    if (confirm('View Bukti Order Barang? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-bukti-order=1&norec=" +
      this.selected.norec + "&petugasMengetahui=" + petugasMengetahui + "&petugasMeminta=" + petugasMeminta +
      "&jabatanMengetahui=" + jabatanMengetahui + "&jabatanMeminta=" + jabatanMeminta +
      "&view=" + stt + "&user=" + this.authService.getDataLoginUser().pegawai.namaLengkap, function (e) {
      });
  }

}
