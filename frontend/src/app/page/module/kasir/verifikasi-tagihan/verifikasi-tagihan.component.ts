import { AfterViewInit, Component, OnInit, ViewChild, DoCheck, KeyValueDiffers, KeyValueDiffer } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';

@Component({
  selector: 'app-verifikasi-tagihan',
  templateUrl: './verifikasi-tagihan.component.html',
  styleUrls: ['./verifikasi-tagihan.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class VerifikasiTagihanComponent implements OnInit, AfterViewInit {
  params: any = {};
  currentNorecPD: any;
  norec_pd: any;
  page: number;
  rows: number;
  item: any = { pasien: {}, mp: {} };
  isClosing: boolean = false;
  dataTableLayanan: any[];
  columnLayanan: any;
  dataTableResep: any[];
  columnResep: any;
  listRuanganAPD: any[];
  dataLogin: any;
  KelompokUser: any;
  showKelengkapanDokumen: boolean = false;
  showTtlKlaim: boolean;
  showTtlKlaim2: boolean;
  dataTableMultiPenjamin: any[];
  columnMultiPenjamin: any[];
  totalBilling: any;
  totalBayar: any;
  totalKlaim: any;
  totalDeposit: any;
  dataLayanan: any[];
  dataResep: any[];
  iskronis: boolean;
  dataChecklist: any[];
  jenisPasien: any;
  isSimpan: boolean;
  selectedArr: any[] = []
  selectedArrPel: any[] = []
  pop_multiPenjamin: boolean;
  columnGrid: any[];
  dataSource: any[];
  listJenisPasien: any[];
  listPenjamin: any[];
  data2: any[] = [];
  dataSelected: any;
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
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngAfterViewInit(): void {
    this.route.params.subscribe(params => {
      this.currentNorecPD = params['norec_rp'];
      this.loadHead();      
    })

  }

  loadHead() {
    this.isClosing = false
    this.apiService.get("general/get-pasien-byregistrasiruangan-general?norec_pd=" + this.currentNorecPD).subscribe(e => {
      this.norec_pd = this.currentNorecPD;
      e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
      e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
      this.h.item.pasien = e;
      this.item.pasien = e;      
      this.apiService.get("sysadmin/general/get-status-close/" + this.item.pasien.noregistrasi).subscribe(rese => {
        if (rese.status == true) {
          this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(rese.tglclosing)).format('DD-MMM-YYYY HH:mm'))
          this.isClosing = true
        }
        this.LoadCombo();
      })
    })
  }

  ngOnInit(): void {
    this.showKelengkapanDokumen = false;
    this.columnLayanan = [
      // { field: 'no', header: 'No', width: "65px" }, 
      { field: 'tglPelayanan', header: 'Tgl Layanan', width: "140px" },
      { field: 'namaPelayanan', header: 'Layanan', width: "180px" },
      { field: 'kelasTindakan', header: 'Kelas', width: "100px" },
      { field: 'namadokter', header: 'Dokter', width: "180px" },
      { field: 'ruanganTindakan', header: 'Dokter', width: "180px" },
      { field: 'jumlah', header: 'Qty', width: "100px", isCurrency: true },
      { field: 'harga', header: 'Harga', width: "120px", isCurrency: true },
      { field: 'diskon', header: 'Diskon', width: "120px", isCurrency: true },
      { field: 'jasa', header: 'Jasa', width: "120px", isCurrency: true },
      { field: 'total', header: 'Total', width: "120px", isCurrency: true },
    ];
    this.columnResep = [
      // { field: 'no', header: 'No', width: "65px" },
      { field: 'tglPelayanan', header: 'Tgl Layanan', width: "140px" },
      { field: 'namaPelayanan', header: 'Layanan', width: "180px" },
      { field: 'kelasTindakan', header: 'Kelas', width: "100px" },
      { field: 'namadokter', header: 'Dokter', width: "180px" },
      { field: 'ruanganTindakan', header: 'Ruangan', width: "180px" },
      { field: 'jumlah', header: 'Qty', width: "100px", isCurrency: true },
      { field: 'harga', header: 'Harga', width: "120px", isCurrency: true },
      { field: 'diskon', header: 'Diskon', width: "120px", isCurrency: true },
      { field: 'jasa', header: 'Jasa', width: "120px", isCurrency: true },
      { field: 'total', header: 'Total', width: "120px", isCurrency: true },
    ];
    this.columnGrid = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'kelompokpasien', header: 'Jenis Pasien', width: "180px" },
      { field: 'namarekanan', header: 'Penjamin', width: "180px" },
      { field: 'klaim', header: 'Total Klaim', width: "180px", isCurrency: true },
    ];
  }

  LoadCombo() {
    this.apiService.get("kasir/get-data-login?norec_pd=" + this.norec_pd).subscribe(data => {      
      var datas = data;
      this.dataLogin = datas.datalogin[0];
      this.KelompokUser = datas.kelompokuser[0];
      this.listRuanganAPD = datas.listRuangan;
      this.jenisPasien = datas.jenispasien[0].kelompokpasien;
      // this.listJenisPasien = datas.kelompokpasien;      
    });

    this.apiService.get("kasir/get-combo-kasir").subscribe(table => {
      var dataCombo = table;
      this.listJenisPasien = dataCombo.kelompokpasien;
      this.LoadData();
    })
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  LoadData() {
    this.apiService.get("kasir/get-data-verifikasi-tagihan?norec_pd=" + this.norec_pd).subscribe(data => {
      debugger;
      var datas = data;
      if (datas != undefined) {
        this.item.SelectedMultiPenjamin = false;
        this.dataTableMultiPenjamin = [];
        this.item.billing = this.formatRupiah(0, 'Rp.');
        this.item.jumlahBayar = this.formatRupiah(0, 'Rp.');
        if (datas.deposit != undefined) {
          this.item.deposit = this.formatRupiah(datas.deposit, 'Rp.');
          this.totalDeposit = datas.deposit;
        } else {
          this.item.deposit = 0;
          this.totalDeposit = 0;
        }
        // if (datas.needDokument.length > 0) {
        //   this.showKelengkapanDokumen = true;
        // $scope.dataKelengkapanDokumen = new kendo.data.DataSource({
        //       data: data[0].data.dokuments
        // }       
        //   $scope.showTtlKlaim = true
        //   $scope.showTtlKlaim2 = true
      }
    });

    this.apiService.get("kasir/get-detail-verifikasi-tagihan?norec_pd=" + this.norec_pd).subscribe(data => {
      var datas = data.details;
      if (datas.length > 0) {
        var dataL = [];
        var dataR = [];
        var dibayar = 0
        var verifTotal = 0
        var nourutlayanan = 0
        var nourutresep = 0
        for (let i = 0; i < datas.length; i++) {
          const element = datas[i];
          if (element.strukfk != null) {
            if (element.strukfk == " / ") {
              element.statCheckbox = false;
            } else if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
              element.statCheckbox = true;
            } else {
              element.statCheckbox = true;
            }

            if (element.strukfk.length > 20) {
              dibayar = dibayar + element.total
            }
            if (element.strukfk.length < 20 && element.strukfk.length > 5) {
              verifTotal = verifTotal + element.total
            }
          }
          if (element.strukfk == " / ") {
            if (element.aturanpakai == null) {
              nourutlayanan = nourutlayanan + 1
              element.no = nourutlayanan
              dataL.push(element)
            } else {
              nourutresep = nourutresep + nourutresep
              element.no = nourutresep
              dataR.push(element)
            }
          }
        }
        this.dataLayanan = dataL;
        this.dataResep = dataR;
        this.dataTableLayanan = this.dataLayanan;
        this.dataTableResep = this.dataResep;
        //$scope.item.ruang2 = ruangaan2        
      } else {
        this.alertService.error("Info", "Data Tidak Ditemukan!");
        return;
      }
    });
  }

  ceklisLayanan(e) {
    var total = 0;
    var bayar = 0;
    for (let i = 0; i < this.selectedArrPel.length; i++) {
      const element = this.selectedArrPel[i];
      total = element.total + total
    }
    for (let e = 0; e < this.selectedArr.length; e++) {
      const elements = this.selectedArrPel[e];
      total = elements.total + total
    }
    this.item.billing = this.formatRupiah(total, "Rp.");
    bayar = total - parseFloat(this.totalDeposit);
    this.item.jumlahBayar = this.formatRupiah(bayar, 'Rp.');
    this.totalBayar = bayar;
    this.item.billing = this.formatRupiah(total, "Rp.");
    this.totalBilling = total;
    if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
      this.item.jumlahBayar = bayar - total;
      this.totalBayar = bayar - total;;
      this.item.totalKlaim = total;//this.formatRupiah(total, "Rp.");//- jml723;
      this.totalKlaim = total
    } else {
      this.item.totalKlaim = 0;
      this.totalKlaim = 0;
    }
  }

  unCeklisLayanan(e) {
    var total = 0;
    var bayar = 0;
    for (let i = 0; i < this.selectedArrPel.length; i++) {
      const element = this.selectedArrPel[i];
      total = element.total + total
    }
    for (let e = 0; e < this.selectedArr.length; e++) {
      const elements = this.selectedArr[e];
      total = elements.total + total
    }
    this.item.billing = this.formatRupiah(total, "Rp.");
    bayar = total - parseFloat(this.totalDeposit);
    this.item.jumlahBayar = this.formatRupiah(bayar, 'Rp.');
    this.totalBayar = bayar;
    this.item.billing = this.formatRupiah(total, "Rp.");
    this.totalBilling = total;
    if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
      this.item.jumlahBayar = bayar - total;
      this.totalBayar = bayar - total;;
      this.item.totalKlaim = total;//this.formatRupiah(total, "Rp.");//- jml723;
      this.totalKlaim = total
    } else {
      this.item.totalKlaim = 0;
      this.totalKlaim = 0;
    }
  }

  ceklisLayananAll(e) {
    var total = 0;
    var bayar = 0;
    for (let i = 0; i < this.selectedArrPel.length; i++) {
      const element = this.selectedArrPel[i];
      total = element.total + total
    }
    for (let e = 0; e < this.selectedArr.length; e++) {
      const elements = this.selectedArr[e];
      total = elements.total + total
    }
    this.item.billing = this.formatRupiah(total, "Rp.");
    bayar = total - parseFloat(this.totalDeposit);
    this.item.jumlahBayar = this.formatRupiah(bayar, 'Rp.');
    this.totalBayar = bayar;
    this.item.billing = this.formatRupiah(total, "Rp.");
    this.totalBilling = total;
    if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
      this.item.jumlahBayar = bayar - total;
      this.totalBayar = bayar - total;;
      this.item.totalKlaim = total;//this.formatRupiah(total, "Rp.");//- jml723;
      this.totalKlaim = total
    } else {
      this.item.totalKlaim = 0;
      this.totalKlaim = 0;
    }
  }

  ceklisResep(e) {
    var total = 0;
    var bayar = 0;
    for (let i = 0; i < this.selectedArr.length; i++) {
      const element = this.selectedArr[i];
      total = element.total + total
    }
    for (let i = 0; i < this.selectedArrPel.length; i++) {
      const element = this.selectedArrPel[i];
      total = element.total + total
    }
    this.item.billing = this.formatRupiah(total, "Rp.");
    bayar = total - parseFloat(this.totalDeposit);
    this.item.jumlahBayar = this.formatRupiah(bayar, 'Rp.');
    this.totalBayar = bayar;
    this.item.billing = this.formatRupiah(total, "Rp.");
    this.totalBilling = total;
    if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
      this.item.jumlahBayar = bayar - total;
      this.totalBayar = bayar - total;;
      this.item.totalKlaim = total;//this.formatRupiah(total, "Rp.");//- jml723;
      this.totalKlaim = total
    } else {
      this.item.totalKlaim = 0;
      this.totalKlaim = 0;
    }
  }

  unCeklisResep(e) {
    var total = 0;
    var bayar = 0;
    for (let i = 0; i < this.selectedArr.length; i++) {
      const element = this.selectedArr[i];
      total = element.total - total
    }
    for (let i = 0; i < this.selectedArrPel.length; i++) {
      const element = this.selectedArrPel[i];
      total = element.total + total
    }
    this.item.billing = this.formatRupiah(total, "Rp.");
    bayar = total - parseFloat(this.totalDeposit);
    this.item.jumlahBayar = this.formatRupiah(bayar, 'Rp.');
    this.totalBayar = bayar;
    this.item.billing = this.formatRupiah(total, "Rp.");
    this.totalBilling = total;
    if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
      this.item.jumlahBayar = bayar - total;
      this.totalBayar = bayar - total;;
      this.item.totalKlaim = total;//this.formatRupiah(total, "Rp.");//- jml723;
      this.totalKlaim = total
    } else {
      this.item.totalKlaim = 0;
      this.totalKlaim = 0;
    }
  }

  ceklisLayananAllResep(e) {
    var total = 0;
    var bayar = 0;
    for (let i = 0; i < this.selectedArr.length; i++) {
      const element = this.selectedArr[i];
      total = element.total + total
    }
    for (let i = 0; i < this.selectedArrPel.length; i++) {
      const element = this.selectedArrPel[i];
      total = element.total + total
    }
    this.item.billing = this.formatRupiah(total, "Rp.");
    bayar = total - parseFloat(this.totalDeposit);
    this.item.jumlahBayar = this.formatRupiah(bayar, 'Rp.');
    this.totalBayar = bayar;
    this.item.billing = this.formatRupiah(total, "Rp.");
    this.totalBilling = total;
    if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
      this.item.jumlahBayar = bayar - total;
      this.totalBayar = bayar - total;;
      this.item.totalKlaim = total;//this.formatRupiah(total, "Rp.");//- jml723;
      this.totalKlaim = total
    } else {
      this.item.totalKlaim = 0;
      this.totalKlaim = 0;
    }
  }

  verifSemua(event: any) {
    var data = event;
    var jml723 = 0;
    var dataChecked = [];
    if (data.checked == true) {
      this.selectedArrPel = this.dataLayanan
      this.selectedArr = this.dataResep
      this.dataChecklist = [];
      for (let i = 0; i < this.dataLayanan.length; i++) {
        const element = this.dataLayanan[i];
        dataChecked.push(element)
      }

      for (let e = 0; e < this.dataResep.length; e++) {
        const elements = this.dataResep[e];
        dataChecked.push(elements)
      }
      this.dataChecklist = dataChecked;
      var total = 0;
      for (var i = 0; i < this.dataChecklist.length; i++) {
        if (this.iskronis == true) {
          jml723 = (parseFloat(this.dataChecklist[i].total) / 30) * 7 + jml723
        }
        total = parseFloat(this.dataChecklist[i].total) + total

      }
      var bayar = 0;
      bayar = total - parseFloat(this.totalDeposit);
      this.item.jumlahBayar = this.formatRupiah(bayar, 'Rp.');
      this.totalBayar = bayar;
      this.item.billing = this.formatRupiah(total, "Rp.");
      this.totalBilling = total;
      if (this.jenisPasien == 'BPJS' || this.jenisPasien == 'BPJS Non PBI ' || this.jenisPasien == 'BPJS PBI') {
        this.item.jumlahBayar = bayar - total;
        this.totalBayar = bayar - total;;
        this.item.totalKlaim = total;//this.formatRupiah(total, "Rp.");//- jml723;
        this.totalKlaim = total
      } else {
        this.item.totalKlaim = 0;
        this.totalKlaim = 0;
      }
    } else {
      this.selectedArrPel = [];
      this.selectedArr = [];
      dataChecked = [];
      this.dataChecklist = [];
      this.item.jumlahBayar = 0;
      this.item.billing = 0;
      this.item.totalKlaim = 0;
      this.totalBilling = 0;
      this.totalBayar = 0;
      this.totalKlaim = 0;
    }
    // checklisteuy = true
  }

  changeKlaim(event) {
    var data = event;
    var tagihan = this.totalBilling;
    if (data != undefined && data != "") {
      var totalklaim = 0
      var nilai = parseFloat(data);
      totalklaim = parseFloat(this.totalBilling) - parseFloat(data);
      this.totalKlaim = nilai;
      this.item.jumlahBayar = totalklaim;
      this.item.totalKlaim = nilai; //this.formatRupiah(nilai, "Rp.");
      this.item.jumlahBayar = this.formatRupiah(totalklaim, "Rp.");
    } else if (data == "") {
      this.totalKlaim = 0;
      this.item.totalKlaim = 0;
      this.totalBayar =  parseFloat(tagihan)
      this.item.jumlahBayar = this.formatRupiah(tagihan, "Rp.");
    } else {
      this.totalKlaim = 0;
      this.item.totalKlaim = 0;
      this.totalBayar =  parseFloat(tagihan) - parseFloat(data);
    }
  }

  BatalVerifikasi() {
    this.dataChecklist = [];
    this.item.jumlahBayar = 0;
    this.item.billing = 0;
    this.item.totalKlaim = 0;
    this.totalBilling = 0;
    this.totalBayar = 0;
    this.totalKlaim = 0;
    this.item.SelectedVerifikasiSemua.value = false;
  }

  KembaliKeFormAwal() {
    this.router.navigate(['daftar-pasien-pulang']);
  }

  simpanVerifikasi() {
 
    var cekMultiPenjamin: any = null
    if (this.item.SelectedMultiPenjamin != undefined) {
      cekMultiPenjamin = this.item.SelectedMultiPenjamin;
    }
    if (this.dataChecklist != undefined) {
      var objSave = {
        norec_pd: this.norec_pd,
        datapasien: this.item.pasien,
        data: this.item,
        details: this.dataChecklist,
        totalKlaim: this.totalKlaim,
        totalBayar: this.totalBayar,
        totalBilling: this.totalBilling,
        totalDeposit: this.totalDeposit,
        cekMultiPenjamin: cekMultiPenjamin,
        multipenjamin: this.data2
      }
      this.isSimpan = true;
      this.apiService.post('kasir/simpan-verifikasi-tagihan-tatarekening', objSave).subscribe(data => {
        if (data.result.norec != '') {
          this.apiService.postLog('Simpan Verifikasi Tagihan', 'norec Registrasi Pasien', data.result.norec, 'Nomor Verifikasi  '
            + data.result.nostruk + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => {
              if (parseFloat(this.totalBayar) > 0 && this.KelompokUser.kelompokuser == 'kasir') {
                this.confirmationService.confirm({
                  message: 'Lanjutkan Pembayaran Tagihan?',
                  header: 'Konfirmasi Pembayaran',
                  icon: 'pi pi-info-circle',
                  accept: () => {
                    this.router.navigate(['pembayaran-tagihan-layanan', data.result.norec, "layanan"]);
                    this.confirmationService.close();
                  },
                  reject: (type) => {
                    this.alertService.warn('Info, Konfirmasi', 'Hapus Dibatalkan!');
                    this.confirmationService.close();
                    return;
                  }
                });
              } else {
                this.router.navigate(['daftar-pasien-pulang'])
              }
            })
        }
      },error=>{
        this.isSimpan = false;
      })
    } else if (this.selectedArr != undefined || this.selectedArrPel != undefined) {
      var cekMultiPenjamin: any = null
      if (this.item.SelectedMultiPenjamin != undefined) {
        cekMultiPenjamin = this.item.SelectedMultiPenjamin;
      }

      var dataCheck = [];
      for (let i = 0; i < this.selectedArr.length; i++) {
        const element = this.selectedArr[i];
        dataCheck.push(element)
      }

      for (let e = 0; e < this.selectedArrPel.length; e++) {
        const elements = this.selectedArrPel[e];
        dataCheck.push(elements)
      }
      this.dataChecklist = dataCheck
      var objSave = {
        norec_pd: this.norec_pd,
        datapasien: this.item.pasien,
        data: this.item,
        details: this.dataChecklist,
        totalKlaim: this.totalKlaim,
        totalBayar: this.totalBayar,
        totalBilling: this.totalBilling,
        totalDeposit: this.totalDeposit,
        cekMultiPenjamin: cekMultiPenjamin,
        multipenjamin: this.data2
        
      }
      this.apiService.post('kasir/simpan-verifikasi-tagihan-tatarekening', objSave).subscribe(data => {
        if (data.result.norec != '') {
          this.apiService.postLog('Simpan Verifikasi Tagihan', 'norec StrukPelayanan', data.result.norec, 'Nomor Verifikasi  '
            + data.result.nostruk + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => {
              if (parseFloat(this.totalBayar) > 0 && this.KelompokUser.kelompokuser == 'kasir') {
                this.confirmationService.confirm({
                  message: 'Lanjutkan Pembayaran Tagihan?',
                  header: 'Konfirmasi Pembayaran',
                  icon: 'pi pi-info-circle',
                  accept: () => {
                    this.router.navigate(['pembayaran-tagihan-layanan', data.result.norec, "layanan"]);
                    this.confirmationService.close();
                  },
                  reject: (type) => {
                    this.alertService.warn('Info, Konfirmasi', 'Pembayaran Dibatalkan!');
                    this.confirmationService.close();
                    return;
                  }
                });
              } else {
                this.router.navigate(['daftar-pasien-pulang'])
              }
            })
        }
      },error=>{
        this.isSimpan = false;
      })

    } else {
      this.isSimpan = false;
      this.alertService.warn("Info", "Data Belum Dipilih");
      return;
    }
  }

  filterPenjamin(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-rekanan-part?namarekanan=" + query
    ).subscribe(re => {
      this.listPenjamin = re;
    })
  }

  MultiPenjamin(event: any) {
    var data = event;    
    if (this.item.billing == "Rp. 0.00" || this.item.billing == undefined || this.item.billing == 0) {
      this.alertService.warn("Info", "Ceklis Data Verifikasi Terlebih Dahulu!"); 
      data == undefined;
      this.item.SelectedMultiPenjamin = undefined;
      return;
    }      
    if (data.checked == true) {
      this.item.SelectedMultiPenjamin = true;        
      // if (this.data2.length > 0) {
      //   this.item.mp.no = this.data2.length  + 1;
      // }else{
      //   this.item.mp.no = 1;
      // }
      this.item.mp.totalTagihan = parseFloat(this.totalBilling)
      this.item.mp.multiPenjamin = undefined
      this.pop_multiPenjamin = true;
    } else {
      this.item.SelectedMultiPenjamin = false;
      this.pop_multiPenjamin = false;
    }
  }

  kosongkan() {
    this.item.mp.no = undefined;
    this.item.mp.dataJenisPasien = undefined;
    this.item.mp.dataPenjamin = undefined;
    this.item.mp.totalTagihan = undefined;
  }

  batalGrid() {
    this.kosongkan();
    this.dataSource = undefined;
    this.item.SelectedMultiPenjamin = false;
    this.pop_multiPenjamin = false;    
  }

  batalMp(){
    this.kosongkan();
  }

  hapusD(dataSelected) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataSelected.no) {
        this.data2.splice(i, 1);
        var subTotal = 0;
        for (var i = this.data2.length - 1; i >= 0; i--) {
          subTotal = subTotal + parseFloat(this.data2[i].klaim)
          this.data2[i].no = i + 1
        }
        this.dataSource = this.data2
        this.item.totalFixClaimRp = this.formatRupiah(subTotal, "Rp.");
        this.item.mp.totalKlaim = this.totalBilling - subTotal;
      }
    }    
    this.kosongkan()
  }

  editD(dataSelected){    
    this.dataSelected = dataSelected;
    this.item.mp.no = this.dataSelected.no;
    this.item.mp.dataJenisPasien = {id:this.dataSelected.kelompokpasienfk, kelompokpasien:this.dataSelected.kelompokpasien};
    this.listPenjamin = [{
      id:this.dataSelected.rekananfk, namarekanan:this.dataSelected.namarekanan
    }];
    this.item.mp.dataPenjamin = {id:this.dataSelected.rekananfk, namarekanan:this.dataSelected.namarekanan};
    this.item.mp.totalTagihan = parseFloat(this.dataSelected.klaim);
  }

  tambahMp(){
    if (this.item.mp.dataJenisPasien == undefined) {
      this.alertService.warn("Info", "jenis Pasien Belum Dipilih!");      
      return;
    }
    if (this.item.mp.dataPenjamin == undefined) {
      this.alertService.warn("Info", "Penjamin Belum Dipilih!");            
      return;
    }

    var nomor = 0
    if (this.item.mp.no == undefined) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }
    if (this.data2.length > 0) {
      var total = 0
      for (var i = 0; i < this.data2.length; i++) {
        total = parseFloat(this.data2[i].klaim) + total
      }
      if (total > parseFloat(this.totalBilling)) {
        this.alertService.warn("Info", "Total klaim Lebih Dari Total Tagihan!");        
        return
      }
    }
    var data: any = {};
    if (this.item.mp.no != undefined) {
      for (var i = this.data2.length - 1; i >= 0; i--) {
        if (this.data2[i].no == this.item.mp.no) {
          data.no = this.item.mp.no
          data.kelompokpasienfk = this.item.mp.dataJenisPasien.id
          data.kelompokpasien = this.item.mp.dataJenisPasien.kelompokpasien
          data.klaim = parseFloat(this.item.mp.totalTagihan)
          data.rekananfk = this.item.mp.dataPenjamin.id
          data.namarekanan = this.item.mp.dataPenjamin.namarekanan
          if (this.data2.length > 0) {
            this.item.mp.no = this.data2.length  + 1;
          }else{
            this.item.mp.no = 1;
          }
          this.data2[i] = data;
          this.dataSource = this.data2
          
        }
      }

    } else {
      data = {
        no: nomor,
        kelompokpasienfk: this.item.mp.dataJenisPasien.id,
        kelompokpasien: this.item.mp.dataJenisPasien.kelompokpasien,
        klaim: parseFloat(this.item.mp.totalTagihan),
        rekananfk: this.item.mp.dataPenjamin.id,
        namarekanan: this.item.mp.dataPenjamin.namarekanan,
      }
      this.data2.push(data)     
      this.dataSource = this.data2
    }

    this.item.mp.totalFixClaim = 0;
    this.item.totalFixClaimRp = 0;
    var totals = 0;
    for (var i = 0; i < this.data2.length; i++) {
      totals = parseFloat(this.data2[i].klaim) + totals;      
    }
    this.item.mp.totalFixClaim = totals;
    this.item.totalFixClaimRp = this.formatRupiah(totals, "Rp.")
    this.item.mp.totalKlaim = this.totalBilling - totals;
    this.kosongkan();
  }

  save(){
    if (this.data2.length == 0) {
      this.alertService.warn("Info", "Data Penjamin Masih Kosong!");        
      return
    }
    this.totalKlaim = this.item.mp.totalFixClaim;    
    this.item.totalKlaim = this.formatRupiah(this.item.mp.totalFixClaim, "Rp.");    
    this.pop_multiPenjamin = false;
  }

}
