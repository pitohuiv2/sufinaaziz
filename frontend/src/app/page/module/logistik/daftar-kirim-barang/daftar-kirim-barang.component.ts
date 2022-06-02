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
  selector: 'app-daftar-kirim-barang',
  templateUrl: './daftar-kirim-barang.component.html',
  styleUrls: ['./daftar-kirim-barang.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarKirimBarangComponent implements OnInit {
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
  pop_tandaTangan: boolean;
  listPetugas: any[];
  listjabatan: any[];
  listBtn:MenuItem[];
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
 
      { label: 'Ubah Kirim Barang', icon: 'fa fa-pencil-square-o', command: () => { this.ubahKirim(); } },
      { label: 'Hapus Kirim Barang', icon: 'fa fa-trash', command: () => { this.hapusKirim(); } },
      { label: 'Cetak Bukti Kirim', icon: 'pi pi-print', command: () => { this.cetakBukti(); } },


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
      { field: 'status', header: 'Status', width: "120px" },
      { field: 'tglstruk', header: 'Tgl Kirim', width: "140px" },
      { field: 'nostruk', header: 'No Kirim', width: "120px" },
      { field: 'jeniskirim', header: 'Jenis Kirim', width: "140px" },
      { field: 'jmlitem', header: 'Jml Item', width: "140px" },
      { field: 'namaruanganasal', header: 'Ruang Asal', width: "180px" },
      { field: 'namaruangantujuan', header: 'Ruang Tujuan', width: "180px" },
      { field: 'petugas', header: 'Petugas', width: "180px" },
      { field: 'keterangan', header: 'Keterangan', width: "140px" },
      // { field: 'statusorder', header: 'Status Kirim', width: "180px" },
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
    var chacePeriode = this.cacheHelper.get('DaftarPermintaanKirimBarangCache');
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
    this.cacheHelper.set('DaftarPermintaanKirimBarangCache', chacePeriode);
    this.apiService.get("logistik/get-daftar-distribusi-barang?"
      + "ruanganasalfk=" + tempRuanganId
      + "&ruangantujuanfk=" + tempRuanganTId
      + "&jeniskirimfk=" + tempJenisPengirimanId
      + "&nokirim=" + tempNoOrder
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

  kirimBarang() {
    this.router.navigate(['input-kirim-barang', "-", "-"]);
  }

  ubahKirim() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    if (this.selected.status == 'Terima Barang') {
      this.alertService.error("Info", "Ruang Penerima Tidak Bisa Mengubah!")
      return;
    }

    this.router.navigate(['input-kirim-barang', this.selected.norec, "editkirim"]);
  }

  hapusKirim() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    if (this.selected.status == 'Terima Barang') {
      this.alertService.error("Info", "Ruang Penerima Tidak Bisa Menghapus!")
      return;
    }

    var data = {
      noorderfk: this.selected.transaksiorderfk,
      noreckirim: this.selected.norec
    }
    var objSave = {
      strukkirim: data,
    }

    this.apiService.post('logistik/batal-kirim-barang-ruangan', objSave).subscribe(dataSave => {
      this.apiService.postLog('Hapus Kirim Barang Ruangan', 'Norec transaksikirim', this.selected.norec,
        'Hapus Kirim Barang Ruangan Dengan Nokirim - ' + this.selected.nokirim).subscribe(res => { })
      this.loadData();
    })
    // 
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.selected = event.data
    }
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
    var petugasMenyerahkan: any = ""
    if (this.item.PetugasSatu != undefined) {
      petugasMenyerahkan = this.item.PetugasSatu.id;
    }
    var petugasMengetahui: any = ""
    if (this.item.PetugasDua != undefined) {
      petugasMengetahui = this.item.PetugasDua.id;
    }
    var petugasMeminta: any = ""
    if (this.item.PetugasTiga != undefined) {
      petugasMeminta = this.item.PetugasTiga.id;
    }
    var jabatanMenyerahkan: any = ""
    if (this.item.JabatanSatu != undefined) {
      jabatanMenyerahkan = this.item.JabatanSatu.namajabatan;
    }
    var jabatanMengetahui: any = ""
    if (this.item.JabatanDua != undefined) {
      petugasMengetahui = this.item.JabatanDua.namajabatan;
    }
    var jabatanMeminta: any = ""
    if (this.item.JabatanTiga != undefined) {
      petugasMeminta = this.item.JabatanTiga.namajabatan;
    }
    var stt = 'false'
    if (confirm('View Bukti Kirim Barang? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-bukti-kirim=1&norec=" +
      this.selected.norec + "&petugasMenyerahkan=" + petugasMenyerahkan + "&petugasMengetahui=" + petugasMengetahui +
      "&petugasMeminta=" + petugasMeminta + "&jabatanMenyerahkan=" + jabatanMenyerahkan +
      "&jabatanMengetahui=" + jabatanMengetahui + "&jabatanMeminta=" + jabatanMeminta +
      "&view=" + stt + "&user=" + this.authService.getDataLoginUser().pegawai.namaLengkap, function (e) {
      });
  }

}
