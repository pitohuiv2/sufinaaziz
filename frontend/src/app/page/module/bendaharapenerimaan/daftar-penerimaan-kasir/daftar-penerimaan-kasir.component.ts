import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-daftar-penerimaan-kasir',
  templateUrl: './daftar-penerimaan-kasir.component.html',
  styleUrls: ['./daftar-penerimaan-kasir.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class DaftarPenerimaanKasirComponent implements OnInit {
  page: number;
  rows: number;
  column: any[];
  selected: any;
  dataTable: any[];
  item: any = {};
  listCaraBayar: any;
  listKelompokTransaksi: any;
  listPetugasPenerima: any = [];
  dateNow: any;
  loginUser: any;
  isDeposit: boolean = false;
  isCicilanPasien: boolean = false;
  pop_ubahCaraBayar: boolean;
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
    this.loginUser = this.authService.getDataLoginUser();
    this.dateNow = new Date();
    this.item.tglAwal = new Date(moment(this.dateNow).format('YYYY-MM-DD 00:00'));
    this.item.tglAkhir = new Date(moment(this.dateNow).format('YYYY-MM-DD 23:59'));
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglsbm', header: 'Tgl Bayar', width: "140px" },
      { field: 'nosbm', header: 'No Bayar', width: "140px" },
      { field: 'nocm', header: 'No RM', width: "125px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namapasien_klien', header: 'Deskripsi', width: "250px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'keterangan', header: 'Jenis Pembayaran', width: "180px" },
      { field: 'carabayar', header: 'Cara Bayar', width: "140px" },
      { field: 'totalpenerimaan', header: 'Total Penerimaan', width: "160px", isCurrency: true },
      { field: 'namalengkap', header: 'Petugas', width: "180px" },
      { field: 'status', header: 'Stat Setor', width: "140px" },
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("bendaharapenerimaan/get-combo-bp").subscribe(table => {
      this.listCaraBayar = table.carabayar;
      this.listKelompokTransaksi = table.jenistransaksi;
      this.listPetugasPenerima = table.petugaskasir;
      // if (this.loginUser.pegawai.id != undefined) {
      //   var data = [];
      //   for (let i = 0; i < table.petugaskasir.length; i++) {
      //     const element = table.petugaskasir[i];
      //     if (element.id == this.loginUser.pegawai.id) {
      //       data.push(element);
      //       break;
      //     }
      //   }
      //   this.item.selectedKasir = data;
      // }
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('cacheDaftarPenerimaanPembayaran');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.LoadData();
    } else {
      this.LoadData();
    }
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  LoadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
    }
    this.cacheHelper.set('cacheDaftarPenerimaanPembayaran', chacePeriode);

    var ScaraBayar = "";
    if (this.item.dataCaraBayar != undefined) {
      ScaraBayar = this.item.dataCaraBayar.id;
    }

    var SkelompokTransaksi = "";
    if (this.item.dataKelTransaksi != undefined) {
      SkelompokTransaksi = this.item.dataKelTransaksi.id;
    }
    var SnoSbm = this.item.noBuktiBayar;

    var listKasir = ""
    if (this.item.selectedKasir != undefined) {
      var a = ""
      var b = ""
      for (var i = this.item.selectedKasir.length - 1; i >= 0; i--) {

        var c = this.item.selectedKasir[i].id
        b = "," + c
        a = a + b
      }
      listKasir = a.slice(1, a.length)
    }

    this.apiService.get("kasir/data-penerimaan-pembayaran?"
      + "dateStartTglSbm=" + tglAwal
      + "&dateEndTglSbm=" + tglAkhir
      + "&idCaraBayar=" + ScaraBayar
      + "&idKelTransaksi=" + SkelompokTransaksi
      + "&nosbm=" + SnoSbm
      + "&nocm=" + this.item.noRM
      + "&noregistrasi=" + this.item.Noregistrasi
      + "&nama=" + this.item.namaPasien
      + "&desk=" + this.item.Desk
      + "&KasirArr=" + listKasir
    ).subscribe(table => {
      var data = table;
      var cash = 0;
      var debit = 0;
      var kredit = 0;
      var donasi = 0;
      var trf = 0;
      var mix = 0;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
        if (element.nocm == null) {
          element.nocm = '-'
        }
        if (element.namapasien == null) {
          element.namapasien = '-'
        }
        if (element.noregistrasi == null) {
          element.noregistrasi = '-'
        }
        if (element.namaruangan == null) {
          element.namaruangan = '-'
        }
        if (element.namapasien_klien == null) {
          element.namapasien_klien = '-'
        }
        if (element.namapasien == null) {
          element.namapasien = "-"
        }
        if (element.noclosing != null) {
          element.status = "Setor";
        } else {
          element.status = "Belum Setor";
        }
        if (element.carabayar == "TUNAI") {
          cash = parseFloat(element.totalpenerimaan) + cash;
        };
        if (element.carabayar == "KARTU DEBIT") {
          debit = parseFloat(element.totalpenerimaan) + debit;
        }
        if (element.carabayar == "KARTU KREDIT") {
          kredit = parseFloat(element.totalpenerimaan) + kredit;
        };
        if (element.carabayar == "TRANSFER BANK") {
          trf = parseFloat(element.totalpenerimaan) + trf;
        }
        if (element.carabayar == "DONASI") {
          donasi = parseFloat(element.totalpenerimaan) + donasi;
        };
        if (element.carabayar == "MIX") {
          mix = parseFloat(element.totalpenerimaan) + mix;
        }
      }
      this.item.totalCash = this.formatRupiah(cash, "RP.");
      this.item.totalDebit = this.formatRupiah(debit, "RP.");
      this.item.totalKredit = this.formatRupiah(kredit, "RP.");
      this.item.totalDonasi = this.formatRupiah(donasi, "RP.");
      this.item.totalMix = this.formatRupiah(mix, "RP.");
      this.item.totalTrf = this.formatRupiah(trf, "RP.");
      this.dataTable = data;
    })
  }

  cari() {
    this.LoadData();
  }

  onRowSelect(event: any) {
    var datas = event.data
    this.apiService.get("general/get-data-closing-pasien/" + datas.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing Tidak Bisa Diubah!");
        return;
      } else {
        this.selected = event.data
      }
    })
  }

}
