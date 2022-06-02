import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-input-tindakan-nonlayanan',
  templateUrl: './input-tindakan-nonlayanan.component.html',
  styleUrls: ['./input-tindakan-nonlayanan.component.scss'],
  providers: [ConfirmationService]
})
export class InputTindakanNonlayananComponent implements OnInit {
  norec_sp: any;
  params: any = {}
  item: any = {
    pasien: {},
  };
  dateNow: any;
  dataLogin: any;
  kelUser: any;
  listRuangan: any[];
  listKelas: any[];
  listProduk: any[];
  listKomponen: any[];
  listJenisKelamin: any[];
  listJenisTransaksi: any[];
  isSimpan: any;
  hargaDiskon: any = 0;
  disabledRuangan: boolean;
  disabledJenisTransaksi: boolean;
  statusTambah: boolean = true;
  dataSelected: any;
  columnGrid: any[] = [];
  dataSource: any[] = [];
  data2: any[] = [];
  noStruk: any;
  isNext:boolean
  maxDateValue:any
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
    this.disabledJenisTransaksi = false;
    this.isSimpan = false;
    this.dateNow = new Date();
    this.item.tgltransaksi = this.dateNow;
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.hargaDiskon = 0;
    this.loadColumn();
    this.loadCombo();
    this.firstLoad();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  loadColumn() {
    this.columnGrid = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'namaproduk', header: 'Pelayanan', width: "250px" },
      { field: 'keterangan', header: 'Keterangan', width: "180px" },
      { field: 'jumlah', header: 'Qty', width: "100px" },
      { field: 'qtyoranglast', header: 'Qty Per Org/Per Km', width: "180px" },
      { field: 'harga', header: 'Harga', width: "140px", isCurrency: true },
      { field: 'total', header: 'Total', width: "140px", isCurrency: true },
    ];
  }

  loadCombo() {
    this.apiService.get("general/get-data-ruang-by-keluser?keluseridfk=" + this.dataLogin.kelompokUser.id).subscribe(table => {
      var dataCombo = table;
      this.listRuangan = dataCombo.ruangan;
      this.item.dataRuangan = this.listRuangan[0];
      this.disabledRuangan = true;
    });
    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listKelas = dataCombo.NonKelas;
      this.listJenisKelamin = dataCombo.jeniskelamin;
      this.item.dataKelas = this.listKelas[0];
      this.listJenisTransaksi = dataCombo.jenistransaksitindakan;
      this.item.dataJenisTransaksi = dataCombo.jenistransaksitindakan[0];
    })
  }

  firstLoad() {
    this.route.params.subscribe(params => {
      if (params['norec_sp'] != undefined || params['norec_sp'] != '-') {
        this.params.norec_sp = params['norec_sp'];
        this.norec_sp = params['norec_sp'];
      } else {
        this.norec_sp = "-"
      }
      this.loadData();
    });
  }

  loadData() {
    if (this.norec_sp != "-") {
      this.apiService.get("kasir/get-detail-transaksi-nonlayanan?noRec=" + this.norec_sp).subscribe(table => {        
        var datahead = table.datahead;
        var detail = table.details;
        this.item.dataJenisTransaksi = { id: datahead.kelompoktransaksiidfk, kelompoktransaksi: datahead.kelompoktransaksi }
        this.item.tgltransaksi = moment(datahead.tgltransaksi).format('YYYY-MM-DD HH:mm');
        this.item.pasien = datahead;
        this.item.pasien.tgllahir = moment(datahead.tgllahir).format('YYYY-MM-DD HH:mm');
        this.item.pasien.jeniskelamin = { id: datahead.jkid, jeniskelamin: datahead.jeniskelamin }
        var subTotal: any = 0;
        for (let i = 0; i < detail.length; i++) {
          const element = detail[i];
          element.no = i + 1;
          subTotal = subTotal + parseFloat(element.total)
        }
        this.data2 = detail;
        this.dataSource = this.data2;
        this.item.totalSubTotal = this.formatRupiah(subTotal, "Rp.");
      });
    }
  }

  cariPasien() {
    if (this.item.pasien.norm == "" || this.item.pasien.norm == "-") {
      this.item.pasien.namapasien = undefined
      this.item.pasien.jeniskelamin = undefined
      this.item.pasien.tgllahir = undefined
      this.item.pasien.notelepon = undefined
      this.item.pasien.alamatlengkap = undefined
      return
    }
    this.apiService.get("general/get-detail-pasien?nocm=" + this.item.pasien.norm).subscribe(data => {
      if (data != undefined) {
        this.item.pasien = data;
        this.item.pasien.tgllahir = new Date(moment(data.tgllahir).format('YYYY-MM-DD HH:mm'))
        this.item.pasien.jeniskelamin = { id: data.jkid, jeniskelamin: data.jeniskelamin }
      }
    })
  }

  filterProduk(event) {
    if (this.item.dataRuangan == undefined) return
    let query = event.query;
    this.apiService.get("tindakan/get-tindakan?namaproduk=" + query
      + "&idRuangan="
      + this.item.dataRuangan.id
      + "&idKelas="
      + this.item.dataKelas.id
      + "&idJenisPelayanan=1")
      .subscribe(re => {
        this.listProduk = re;
      })
  }

  getHargaTindakan() {
    this.item.hargaTindakan = 0
    this.item.jumlah = 0
    this.listKomponen = []
    if (this.item.namaProduk != undefined) {
      this.apiService.get("tindakan/get-komponenharga?idRuangan="
        + this.item.dataRuangan.id
        + "&idKelas=" + this.item.dataKelas.id
        + "&idProduk=" + this.item.namaProduk.id
        + "&idJenisPelayanan=1"
      ).subscribe(dat => {
        this.listKomponen = dat.data;
        this.item.hargaTindakan = dat.data2[0].hargasatuan
        this.item.jumlah = 1;
        this.item.jumlahOrg = 1;
        this.item.Total = (parseFloat(this.item.jumlah) * parseFloat(this.item.hargaTindakan)) * parseFloat(this.item.jumlahOrg);
      })
    }
  }

  onChangeJml(e) {
    var qtyOrg: any = 0;
    if (this.item.jumlahOrg != 0) {
      qtyOrg = this.item.jumlahOrg
    }
    if (this.item.jumlah != 0) {
      this.item.Total = (parseFloat(this.item.jumlah) * parseFloat(this.item.hargaTindakan)) * parseFloat(qtyOrg);
    }
  }

  onChangeJmlOrg(e) {
    var qtyOrg: any = 0;
    if (this.item.jumlahOrg != 0) {
      qtyOrg = this.item.jumlahOrg
    }
    if (this.item.jumlah != 0) {
      this.item.Total = (parseFloat(this.item.jumlah) * parseFloat(this.item.hargaTindakan)) * parseFloat(qtyOrg);
    }
  }

  kosongkan() {
    this.item.tgltransaksi = this.dateNow;
    this.item.namaProduk = undefined;
    this.item.hargaTindakan = 0;
    this.item.jumlah = 1;
    this.item.jumlahOrg = 1;
    this.item.Total = 0;
  }

  editD(dataSelected) {
    if (this.statusTambah == false)
      return
    this.dataSelected = dataSelected;
    this.item.no = dataSelected.no;
    this.listProduk = [{
      id: this.dataSelected.id, namaproduk: this.dataSelected.namaproduk
    }];
    this.item.namaProduk = this.listProduk[0];
    this.item.hargaTindakan = this.dataSelected.harga;
    this.item.jumlah = this.dataSelected.jumlah;
    this.item.jumlahOrg = this.dataSelected.qtyoranglast;
    this.item.Total = this.dataSelected.total;
  }

  hapusD(dataSelected) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataSelected.no) {

        this.data2.splice(i, 1);
        var subTotal = 0;
        for (var i = this.data2.length - 1; i >= 0; i--) {
          subTotal = subTotal + parseFloat(this.data2[i].total)
          this.data2[i].no = i + 1
        }
        this.dataSource = this.data2
        this.item.totalSubTotal = subTotal
      }
    }
    this.kosongkan()
  }

  batal() {
    this.kosongkan();
  }

  tambahTindakan() {
    if (this.statusTambah == false) {
      return
    }

    if (this.item.namaProduk == undefined) {
      this.alertService.error("Info", "Data Pelayanan Masih Kosong!");
      return;
    }

    if (this.item.hargaTindakan == undefined) {
      this.alertService.error("Info", "Harga Pelayanan Masih Kosong!");
      return;
    }

    if (this.item.Total == undefined) {
      this.alertService.error("Info", "Total Pelayanan Masih Kosong!");
      return;
    }

    if (this.item.jumlah == 0) {
      this.alertService.error("Info", "Jumlah harus di isi!")
      return;
    }

    var nomor = 0
    if (this.dataSource == undefined) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }

    var keterangan = '-'

    var data: any = {};
    this.disabledJenisTransaksi = true;
    if (this.item.no != undefined) {
      for (var i = this.data2.length - 1; i >= 0; i--) {
        if (this.data2[i].no == this.item.no) {
          data.no = this.item.no
          data.id = this.item.namaProduk.id
          data.namaproduk = this.item.namaProduk.namaproduk
          data.jumlah = parseFloat(this.item.jumlah)
          data.qtyoranglast = parseFloat(this.item.jumlahOrg)
          data.harga = parseFloat(this.item.hargaTindakan)
          data.total = this.item.Total
          data.keterangan = keterangan
          this.data2[i] = data;
          this.dataSource = this.data2;
          var subTotal: any = 0;
          for (let i = 0; i < this.data2.length; i++) {
            const element = this.data2[i];
            subTotal = subTotal + parseFloat(element.total)
          }
          this.item.totalSubTotal = this.formatRupiah(subTotal, "Rp.")
          this.kosongkan();
        }
      }

    } else {
      data = {
        nomor: nomor,
        id: this.item.namaProduk.id,
        namaproduk: this.item.namaProduk.namaproduk,
        jumlah: parseFloat(this.item.jumlah),
        qtyoranglast: parseFloat(this.item.jumlahOrg),
        harga: parseFloat(this.item.hargaTindakan),
        keterangan: keterangan,
        total: this.item.Total
      }
      this.data2.push(data)
      this.dataSource = this.data2;
      var subTotal: any = 0;
      for (let i = 0; i < this.data2.length; i++) {
        const element = this.data2[i];
        subTotal = subTotal + parseFloat(element.total)
      }
      this.item.totalSubTotal = this.formatRupiah(subTotal, "Rp.")
      this.kosongkan();
    }
  }

  batalGrid() {
    this.kosongkan();
    this.data2 = [];
    this.dataSource = undefined;
  }

  Kembali() {
    window.history.back();
  }

  save() {
    if (this.item.dataJenisTransaksi == undefined) {
      this.alertService.error("Info", "Jenis Transaksi Masih Kosong!");
      return;
    }

    if (this.item.tgltransaksi == undefined) {
      this.alertService.error("Info", "Data Tanggal transaksi Masih Kosong!");
      return;
    }

    if (this.item.pasien.namapasien == undefined) {
      this.alertService.error("Info", "Nama Pasien Masih Kosong!");
      return;
    }

    if (this.data2.length == 0) {
      this.alertService.error("Info", "Detail Layanan Masih Kosong!");
      return;
    }

    var total: any = 0;
    for (let i = 0; i < this.data2.length; i++) {
      const element = this.data2[i];
      total = total + parseFloat(element.total);
    }

    if (total == undefined || total == 0) {
      this.alertService.error("Info", "Total tidak Boleh Kosong!");
      return;
    }

    var objSave = {
      norec: this.norec_sp,
      kelompoktransaksifk: this.item.dataJenisTransaksi.id,
      keteranganlainnya: "INPUT PELAYANAN NON LAYANAN",
      namapasien: this.item.pasien.namapasien,
      nocm: this.item.pasien.norm != undefined ? this.item.pasien.norm : "-",
      tgllahir: this.item.pasien.tgllahir != undefined ? moment(this.item.pasien.tgllahir).format('YYYY-MM-DD HH:mm') : null,
      notelepon: this.item.pasien.notelepon != undefined ? this.item.pasien.notelepon : "-",
      alamat: this.item.pasien.alamatlengkap != undefined ? this.item.pasien.alamatlengkap : "-",
      jkid: this.item.pasien.jeniskelamin != undefined ? this.item.pasien.jeniskelamin.id : null,
      tglstruk: moment(this.item.tgltransaksi).format('YYYY-MM-DD hh:mm'),
      totalharusdibayar: parseFloat(total),
      details: this.data2

    }
    this.apiService.post('kasir/save-input-non-layanan', objSave).subscribe(dataSave => {
      if (this.norec_sp == '-') {
        this.apiService.postLog('Simpan Pelayanan Non Layanan', 'Norec strukpelayan', dataSave.data.norec,
          'Simpan Pelayanan Non Layanan Dengan No Transaksi - ' + dataSave.data.nostruk + ' atas Nama Pasien - '
          + this.item.pasien.namapasien).subscribe(res => {
            if (this.kelUser == "kasir") {
              this.confirmationService.confirm({
                message: 'Apakah Anda Akan Melanjutkan Transaksi Ke Pembayaran?',
                header: 'Konfirmasi Melanjutkan Transaksi',
                icon: 'pi pi-info-circle',
                accept: () => {
                  this.router.navigate(['pembayaran-tagihan-layanan', dataSave.data.norec, "nonlayanan"]);
                  this.confirmationService.close();
                },
                reject: (type) => {
                  this.confirmationService.close();
                  window.history.back();
                }
              });
            } else {
              window.history.back();
            }
          })
      } else {
        this.apiService.postLog('Ubah Pelayanan Non Layanan', 'Norec strukpelayan', this.norec_sp,
          'Ubah Pelayanan Non Layanan Dengan No Transaksi - ' + dataSave.data.nostruk + ' atas Nama Pasien - '
          + this.item.pasien.namapasien).subscribe(res => {
            if (this.kelUser == "kasir") {
              this.confirmationService.confirm({
                message: 'Apakah Anda Akan Melanjutkan Transaksi Ke Pembayaran?',
                header: 'Konfirmasi Melanjutkan Transaksi',
                icon: 'pi pi-info-circle',
                accept: () => {
                  this.router.navigate(['pembayaran-tagihan-layanan', this.norec_sp, "nonlayanan"]);
                  this.confirmationService.close();
                },
                reject: (type) => {
                  this.confirmationService.close();
                  window.history.back();
                }
              });
            } else {
              window.history.back();
            }
          })
      }
    })
  }
  cancel(){
    
  }
}
