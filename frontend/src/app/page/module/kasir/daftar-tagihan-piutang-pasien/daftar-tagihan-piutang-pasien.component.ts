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
  selector: 'app-daftar-tagihan-piutang-pasien',
  templateUrl: './daftar-tagihan-piutang-pasien.component.html',
  styleUrls: ['./daftar-tagihan-piutang-pasien.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarTagihanPiutangPasienComponent implements OnInit {
  column: any[];
  selected: any;
  dataTable: any[];
  item: any = { pasien: {} };
  dateNow: any;
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  pop_pembayaranPiutangPasien: boolean;
  dataTableDV: any[];
  columnDV: any[];
  selectedDV: any;
  TotalTagihan: any;
  TotalSudahDibayar: any;
  TotalSisaTagihan: any;
  norec_sp: any;
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
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglverifikasi', header: 'Tgl Verifikasi', width: "140px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'ruangan', header: 'Ruangan', width: "180px" },
      { field: 'jenisPasien', header: 'Tipe Pasien', width: "120px" },
      { field: 'tgltransaksi', header: 'Tgl Pulang', width: "140px" },
      { field: 'totalKlaim', header: 'Total Tagihan', width: "140px", isCurrency: true },
      { field: 'totalBayar', header: 'Total Bayar', width: "140px", isCurrency: true },
      { field: 'sisaBayar', header: 'Sisa Bayar', width: "180px", isCurrency: true },
      { field: 'status', header: 'Status', width: "120px" }
    ];
    this.columnDV = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglPembayaran', header: 'Tgl Bayar', width: "140px" },
      { field: 'noSbm', header: 'No Bayar', width: "140px" },
      { field: 'jlhPembayaran', header: 'Jumlah Bayar', width: "180px", isCurrency: true },
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('TagihanPiutangKasirCtrl');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.item.status = chacePeriode[2]
      this.item.namaOrReg = chacePeriode[3]
      if (chacePeriode[6] != undefined) {
        this.listDepartemen = [chacePeriode[6]]
        this.item.dataDepartemen = chacePeriode[6]
      }
      if (chacePeriode[5] != undefined) {
        this.listRuangan = [chacePeriode[5]]
        this.item.dataRuangan = chacePeriode[5]
      }

      if (chacePeriode[4] != undefined && chacePeriode[4] != "") {
        this.item.noReg = chacePeriode[4]
      }
      if (chacePeriode[7] != undefined && chacePeriode[7] != "") {
        this.item.noRm = chacePeriode[7]
      }
      if (chacePeriode[8] != undefined && chacePeriode[8] != "") {
        this.item.jmlRows = chacePeriode[8]
      }
      this.getData();
    }
    else {
      this.getData();
    }
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.dataDepartemen.ruangan;
    }
  }

  getData() {

    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');

    var tempRuanganId = "";
    var tempRuanganIdArr = undefined;
    if (this.item.ruangan != undefined) {
      tempRuanganId = this.item.dataRuangan.id;
      tempRuanganIdArr = { id: this.item.dataRuangan.id, ruangan: this.item.dataRuangan.ruangan }
    }

    var tempStatus = "";
    var tempStatusArr = undefined;
    // if (this.item.status != undefined) {
    //   tempStatus = this.item.status.namaExternal;
    //   tempStatusArr = { id: this.item.status.id, namaExternal: this.item.status.namaExternal }
    // }

    var tempInstalasiId = "";
    var tempInstalasiIdArr = undefined;
    if (this.item.instalasi != undefined) {
      tempInstalasiId = this.item.dataDepartemen.id;
      tempInstalasiIdArr = { id: this.item.dataDepartemen.id, departemen: this.item.dataDepartemen.departemen }
    }

    var kelompokPasienId = ""
    if (this.item.dataKelPasien != undefined) {
      kelompokPasienId = this.item.dataKelPasien.id
    }

    var tempNoRm = "";
    if (this.item.noRM != undefined) {
      tempNoRm = this.item.noRM;
    }

    var tempNoReg = "";
    if (this.item.Noregistrasi != undefined) {
      tempNoReg = this.item.Noregistrasi;
    }

    var tempNamaOrReg = "";
    if (this.item.namaPasien != undefined) {
      tempNamaOrReg = this.item.namaPasien;
    }

    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = this.item.jmlRows
    }

    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempStatusArr,
      3: tempNamaOrReg,
      4: tempNoReg,
      5: tempRuanganIdArr,
      6: tempInstalasiIdArr,
      7: tempNoRm,
      // 8: jmlRow
    }
    this.cacheHelper.set('TagihanPiutangKasirCtrl', chacePeriode);

    this.apiService.get("kasir/daftar-piutang-layanan?"
      + "namaPasien=" + tempNamaOrReg
      + "&ruanganId=" + tempRuanganId
      + "&status=" + tempStatus
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&noReg=" + tempNoReg
      + "&instalasiId=" + tempInstalasiId
      + "&noRm=" + tempNoRm
      + "&jmlRows=" + jmlRow
      + "&kelompokPasienId=" + kelompokPasienId).subscribe(data => {
        var data = data
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          element.sisaBayar = element.totalKlaim - element.totalBayar
        }
        this.dataTable = data;
      })
  }

  cari() {
    this.getData();
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

  bayarTagihan2(e) {
    this.selected = e
    this.bayarTagihan()
  }
  bayarTagihan() {
    if (this.selected != undefined) {
      this.item.pasien = this.selected;
      var now = new Date();
      var tgllahir = moment(new Date(this.selected.tgltransaksi)).format('YYYY-MM-DD');
      var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
      this.item.pasien.lamapiutang = umur.day + ' hari';
      this.norec_sp = this.selected.norec_sp;
      this.getDataHistoryBayar();
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  getDataHistoryBayar() {
    this.apiService.get("kasir/detail-piutang-pasien/" + this.selected.noRec).subscribe(table => {
      var data = table;
      var dataGrid = data.detailPembayaran;
      for (let i = 0; i < dataGrid.length; i++) {
        const element = dataGrid[i];
        element.no = i + 1
      }

      this.TotalTagihan = parseFloat(data.totalTagihan);
      this.TotalSudahDibayar = parseFloat(data.sudahDibayar);
      this.TotalSisaTagihan = parseFloat(data.sisaPiutang);
      this.item.TotalTagihan = this.formatRupiah(parseFloat(data.totalTagihan), "Rp.");
      this.item.TotalSudahDibayar = this.formatRupiah(parseFloat(data.sudahDibayar), "Rp.");
      this.item.TotalSisaTagihan = this.formatRupiah(parseFloat(data.sisaPiutang), "Rp.");
      this.dataTableDV = dataGrid;
      this.pop_pembayaranPiutangPasien = true;
    })
  }

  changeNominal(event) {
    var data = event;
    if (data != undefined && data != "") {
      this.apiService.get('sysadmin/general/get-terbilang/' + parseFloat(data)).subscribe(dat => {
        this.item.Terbilang = dat.terbilang;
      });
    }
  }

  Bayar() {
    if (parseFloat(this.item.TotalDibayar) > this.TotalTagihan) {
      this.alertService.warn('Info', 'Total Dibayar Tidak Bisa Lebih Dari Total Tagihan!')
      return
    }
    this.router.navigate(['bayar-tagihan-pasien', this.norec_sp, "cicilanPasien", parseFloat(this.item.TotalDibayar)]);
  }

  batalBayar() {
    this.item.TotalDibayar = undefined;
    this.item.Terbilang = undefined;
  }

  Kembali() {
    this.item.pasien = {};
    this.norec_sp = undefined;
    this.TotalTagihan = undefined;
    this.TotalSudahDibayar = undefined;
    this.TotalSisaTagihan = undefined;
    this.item.TotalTagihan = undefined;
    this.item.TotalSudahDibayar = undefined;
    this.item.TotalSisaTagihan = undefined;
    this.dataTableDV = [];
    this.pop_pembayaranPiutangPasien = false;
  }
  onRowSelectDV(e) {

  }
}
