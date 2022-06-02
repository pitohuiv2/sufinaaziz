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
  selector: 'app-daftar-penerimaan-barang-supplier',
  templateUrl: './daftar-penerimaan-barang-supplier.component.html',
  styleUrls: ['./daftar-penerimaan-barang-supplier.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPenerimaanBarangSupplierComponent implements OnInit {
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
  listBtn: MenuItem[];
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
      { label: 'Ubah', icon: 'fa fa-pencil-square-o', command: () => { this.ubahPenerimaan(); } },
      { label: 'Hapus', icon: 'fa fa-trash', command: () => { this.hapusPenerimaan(); } },
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
      { field: 'nofaktur', header: 'No. Faktur', width: "120px" },
      { field: 'nostruk', header: 'No. Struk', width: "120px" },
      { field: 'tglstruk', header: 'Tgl Terima', width: "140px" },
      { field: 'namarekanan', header: 'Supplier', width: "140px" },
      { field: 'jmlitem', header: 'Jml Item', width: "140px" },
      { field: 'namaruangan', header: 'Ruang Terima', width: "180px" },
      { field: 'namapenerima', header: 'Petugas', width: "180px" },
      { field: 'keterangan', header: 'Keterangan', width: "140px" },
      { field: 'totalharusdibayar', header: 'Total Tagihan', width: "180px", isCurrency: true },
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

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('DaftarPenerimaanBarangSupplierCache');
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
    var tempNoTerima = "";
    if (this.item.noTerima != undefined) {
      tempNoTerima = this.item.noTerima;
    }
    var tempNoFakturr = "";
    if (this.item.noFaktur != undefined) {
      tempNoFakturr = this.item.noFaktur;
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
    }
    this.cacheHelper.set('DaftarPenerimaanBarangSupplierCache', chacePeriode);
    this.apiService.get("logistik/get-daftar-penerimaan?"
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&ruanganfk=" + tempRuanganId
      + "&nostruk=" + tempNoTerima
      + "&noFaktur=" + tempNoFakturr
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

  TambahPenerimaan() {
    this.router.navigate(['penerimaan-barang-supplier', "-", "-"]);
  }

  ubahPenerimaan() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    if (this.selected.nosbk != undefined) {
      this.alertService.error("Info", "Data Tidak Bisa Diubah!")
      return;
    }

    this.router.navigate(['penerimaan-barang-supplier', this.selected.norec, "editterima"]);
  }

  hapusPenerimaan() {
    if (this.selected == undefined) {
      this.alertService.error("Info", "Data Belum Dipilih!")
      return;
    }
    if (this.selected.nosbk != undefined) {
      this.alertService.error("Info", "Data Tidak Bisa Diubah!")
      return;
    }
    var objSave = {
      nostruk: this.selected.norec,
      noorderfk: this.selected.noorderfk
    }
    this.confirmationService.confirm({
      message: 'Lanjutkan Hapus Penerimaan?',
      header: 'Konfirmasi Hapus Penerimaan',
      icon: 'pi pi-info-circle',
      accept: () => {
        this.apiService.post('logistik/delete-data-penerimaan', objSave).subscribe(data => {
          this.apiService.postLog('Hapus Penerimaan Barang Supplier', 'norec StrukPelayanan', this.selected.norec, 'Hapus Penerimaan Dengan No Terima : '
            + this.selected.nostruk).subscribe(z => { });
          this.loadData();
        })
      },
      reject: (type) => {
        this.alertService.warn('Info, Konfirmasi', 'Hapus Penerimaan Dibatalkan!');
        this.confirmationService.close();
        return;
      }
    });
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
    var petugasMenerima: any = ""
    if (this.item.PetugasDua != undefined) {
      petugasMenerima = this.item.PetugasDua.id;
    }
    var petugasMengetahui: any = ""
    if (this.item.PetugasTiga != undefined) {
      petugasMengetahui = this.item.PetugasTiga.id;
    }
    var jabatanMenyerahkan: any = ""
    if (this.item.JabatanSatu != undefined) {
      jabatanMenyerahkan = this.item.JabatanSatu.namajabatan;
    }
    var jabatanMenerima: any = ""
    if (this.item.JabatanDua != undefined) {
      jabatanMenerima = this.item.JabatanDua.namajabatan;
    }
    var jabatanMengetahui: any = ""
    if (this.item.JabatanTiga != undefined) {
      jabatanMengetahui = this.item.JabatanTiga.namajabatan;
    }
    var stt = 'false'
    if (confirm('View Bukti Penerimaan Barang? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-bukti-penerimaan=1&norec=" +
      this.selected.norec + "&petugasMenyerahkan=" + petugasMenyerahkan + "&petugasMenerima=" + petugasMenerima +
      "&petugasMengetahui=" + petugasMengetahui + "&jabatanMenyerahkan=" + jabatanMenyerahkan +
      "&jabatanMenerima=" + jabatanMenerima + "&jabatanMengetahui=" + jabatanMengetahui +
      "&view=" + stt + "&user=" + this.authService.getDataLoginUser().pegawai.namaLengkap, function (e) {
      });
  }

}
