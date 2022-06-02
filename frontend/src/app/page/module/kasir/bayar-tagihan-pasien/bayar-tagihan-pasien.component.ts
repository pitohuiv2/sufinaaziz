import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { ActivatedRoute, Router } from '@angular/router';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';

@Component({
  selector: 'app-bayar-tagihan-pasien',
  templateUrl: './bayar-tagihan-pasien.component.html',
  styleUrls: ['./bayar-tagihan-pasien.component.scss'],
  providers: [ConfirmationService]
})
export class BayarTagihanPasienComponent implements OnInit {
  page: number;
  rows: number;
  params: any = {};
  currentNorecSP: any;
  currentJenisPembayaran: any;
  norec_sp: any;
  JenisPembayaran: any;
  column: any[];
  dataTable: any[] = [];
  selected: any;
  item: any = { pasien: {} };
  dateNow: any;
  dataPegawai: any;
  idKasir: any;
  namaKasir: any;
  listCaraBayar: any[];
  urlGetDataDetail: any;
  tipePembayaran: any;
  dataSave: any[];
  Tagihan: any;
  dataPrevPage: any = {};
  isSimpan: boolean;
  nominalBayar: any;
  maxDateValue = new Date()
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
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit(): void {
    this.dateNow = new Date();
    this.item.tglBayar = this.dateNow;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'caraBayar', header: 'Cara Bayar', width: "140px" },
      { field: 'nominal', header: 'Nominal', width: "100px", isCurrency: true },
    ];    
    this.route.params.subscribe(params => {
      this.currentNorecSP = params['norec_sp'];
      this.currentJenisPembayaran = params['jenispembayaran'];
      this.JenisPembayaran = params['jenispembayaran'];
      this.norec_sp = this.currentNorecSP;
      this.nominalBayar = parseFloat(params['nominalbayar']);
      this.dataPrevPage = {
        noRecStrukPelayanan: this.norec_sp,
        tipePembayaran: this.JenisPembayaran,
      }
      this.LoadCombo();
    });
  }

  LoadCombo() {
    this.apiService.get('kasir/get-combo-kasir').subscribe(dat => {
      var data = dat;
      this.listCaraBayar = data.carabayar;
      this.item.caraBayar = data.carabayar[0];
      this.dataPegawai = data.dataLogin;
      if (this.dataPegawai) {
        this.idKasir = this.dataPegawai.objectpegawaifk;
        this.namaKasir = this.dataPegawai.namalengkap;
      }
      this.LoadData();
    });
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  LoadData() {
    this.apiService.get('kasir/get-data-pembayaran?noRecStrukPelayanan=' + this.norec_sp
      + "&tipePembayaran=" + this.JenisPembayaran + "&jumlahBayar=" + this.nominalBayar).subscribe(dat => {
        var data = dat;
        this.item.Diterima = data.pasien;
        this.Tagihan = parseFloat(data.jumlahBayar);
        this.item.Nominal = parseFloat(data.jumlahBayar);
        this.changeNominal(data.jumlahBayar);
        this.item.totalTagihan = this.formatRupiah(this.Tagihan, "Rp.")
        this.item.TotalDibayar = this.formatRupiah(this.Tagihan, "Rp.")
      });
  }

  Batal() {
    this.item.Nominal = undefined;
    this.item.Terbilang = undefined;
  }

  changeNominal(event) {
    var data = event;
    if (data != undefined && data != "") {
      this.apiService.get('sysadmin/general/get-terbilang/' + parseFloat(data)).subscribe(dat => {
        this.item.Terbilang = dat.terbilang;
      });
    }
  }

  tambahBayar() {
    if (this.item.caraBayar == undefined) {
      this.alertService.warn("Info", "Cara Bayar Belum Dipilih!")
      return;
    }
    if (this.item.Nominal == undefined || this.item.Nominal == "") {
      this.alertService.warn("Info", "Nominal Tidak Boleh Kosong!")
      return;
    }

    var nomor = 1;
    if (this.dataTable.length == 0) {
      nomor = 1;
    } else {
      nomor = this.dataTable.length + 1;
    }
    this.dataTable.push(
      {
        'no': nomor,
        'caraBayar': this.item.caraBayar.carabayar,
        'idcaraBayar': this.item.caraBayar.id,
        'nominal': parseFloat(this.item.Nominal),
      }
    )
    var total = 0;
    for (let i = 0; i < this.dataTable.length; i++) {
      const element = this.dataTable[i];
      total = element.nominal + total;
    }
    this.item.Total = total;
    this.item.Terbilang = undefined;
    this.item.Nominal = undefined;
  }

  hapusItem(e) {
    for (var i = this.dataTable.length - 1; i >= 0; i--) {
      if (this.dataTable[i].no == e.no) {
        this.dataTable.splice(i, 1);
      }
    }
    var total = 0;
    for (let i = 0; i < this.dataTable.length; i++) {
      const element = this.dataTable[i];
      total = element.nominal + total;
    }
    this.item.Total = total;
  }

  bayarTagihan() {

    if (this.item.Total == undefined || this.item.Total == 0) {
      this.alertService.warn("Info", "Total Bayar Tidak Boleh Kosong!")
      return;
    }

    if (this.item.Total != this.Tagihan) {
      this.alertService.warn("Info", "Total Bayar Tidak Sama Dengan Tagihan!")
      return;
    }

    var dataObjPost = {
      "parameterTambahan": this.dataPrevPage,
      "jumlahBayar": this.item.Tota,
      "biayaAdministrasi": 0,
      "diskon": 0,
      "tglsbm": moment(this.item.TglBayar).format('YYYY-MM-DD HH:mm'),
      "pembayaran":
        []
    }
    for (let i = 0; i < this.dataTable.length; i++) {
      const element = this.dataTable[i];
      dataObjPost.pembayaran.push({ "nominal": element.nominal, "caraBayar": { "id": element.idcaraBayar, "caraBayar": element.caraBayar } })
    }
    this.isSimpan = true;
    this.apiService.post('kasir/simpan-data-pembayaran', dataObjPost)
      .subscribe(e => {
        this.apiService.postLog('Pembayaran Tagihan ' + this.JenisPembayaran, 'nomor sbm', e.noSBM[0], 'Pembayaran Tagihan No SBM - ' + e.noSBM[0] + ' pada No Registrasi ' + e.noReg).subscribe(z => { })
        var stt = 'false'
        if (confirm('View Kwitansi? ')) {
          // Save it!
          stt = 'true';
        } else {
          // Do nothing!
          stt = 'false'
        }
        this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-kwitansi=1&nosbm=" +
        e.norec + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
          + "&view=" + stt, function (e) { });
        window.history.back();
        this.isSimpan = false;
      }, error => {
        this.isSimpan = false;
      });
  }

  Kembali() {
    window.history.back();
  }
  onRowSelect(e) {

  }
  changeKlaim(e) {

  }
}
