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
  selector: 'app-daftar-order',
  templateUrl: './daftar-order.component.html',
  styleUrls: ['./daftar-order.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarOrderComponent implements OnInit {
  selected: any;
  selectedDV: any;
  dataTable: any[];
  dateNow: any;
  column: any[];
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  listRuanganApd: any[];
  listRuanganTujuan: any[];
  listLayanan: any[] = [];
  listKomponen: any[];
  dataLogin: any;
  kelUser: any;
  item: any = {
    pasien: {}
  };
  disableVer: boolean;
  pop_VerifikasiOrder: boolean;
  idJenisPelayanan: any;
  idKelas: any;
  data2: any[] = [];
  columnDV: any[];
  dataTableDV: any[];
  ruanganTujuan: any;
  namaruanganTujuan: any;
  instalasifk: any;
  listDokter: any[];
  listDokterOrder: any[] = [];
  disableDokterOrder: boolean = true;
  isSimpan: boolean;
  pop_DetailVerifikasi: boolean;
  selectedDVs: any;
  columnDVs: any[];
  dataTableDVs: any[];
  idRuanganAsal: any[];
  listBtn: MenuItem[];
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
    this.listBtn = [
      { label: 'Verifikasi Order', icon: 'fa fa-check-square-o', command: () => { this.verifikasiOrder(); } },
      { label: 'Detail Verifikasi', icon: 'fa fa-info-circle', command: () => { this.detailVerifikasi(); } },
      { label: 'Lihat Hasil', icon: 'pi pi-file', command: () => { this.lihatHasil(); } },
      { label: 'Hapus Order', icon: 'pi pi-user-edit', command: () => { this.hapusOrder(); } },
      { label: 'Bukti Layanan', icon: 'pi pi-print', command: () => { this.cetakBuktiLayanan(); } },
      { label: 'Pengkajian Medis', icon: 'fa fa-stethoscope', command: () => { this.pengkajianMedis(); } },];
    this.disableDokterOrder = true;
    this.item.SelectedVerifikasi = true;
    if (this.item.SelectedVerifikasi = true) {
      this.disableVer = true;
    } else {
      this.disableVer = false;
    }
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'noorder', header: 'No Order', width: "140px" },
      { field: 'tglorder', header: 'Tgl Order', width: "140px" },
      { field: 'status', header: 'Status', width: "120px", isTag: true },
      { field: 'cito', header: 'Cito', width: "100px", isTag2: true },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "200px" },
      { field: 'namaruangan', header: 'Ruang Order', width: "180px" },
      { field: 'ruangantujuan', header: 'Ruang Tujuan', width: "180px" },
      { field: 'umur', header: 'Umur', width: "120px" },
      { field: 'pegawaiorder', header: 'Petugas', width: "180px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "140px" },
      { field: 'keteranganlainnya', header: 'Keterangan', width: "140px" },


    ];
    this.columnDV = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglpelayanan', header: 'Tgl Pelayanan', width: "140px" },
      { field: 'namaproduk', header: 'Layanan', width: "180px" },
      { field: 'qtyproduk', header: 'Jumlah', width: "100px" },
    ];
    this.columnDVs = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglpelayanan', header: 'Tgl Pelayanan', width: "140px" },
      { field: 'namaproduk', header: 'Layanan', width: "180px" },
      { field: 'jumlah', header: 'Jumlah', width: "100px" },
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("penunjang/get-data-combo-penunjang").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.listDokter = dataCombo.dokter;
    });
    this.apiService.get("penunjang/get-data-ruang-tujuan?keluseridfk=" + this.dataLogin.kelompokUser.id).subscribe(table => {
      var dataCombo = table;
      this.listRuanganTujuan = dataCombo.ruangan;
      // this.item.dataRuanganTujuan = dataCombo.ruangan[0];
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('OrderDaftarPenunjangCtrl');
    if (chacePeriode != undefined) {
      // this.item.tglAwal = new Date(chacePeriode[0]);
      // this.item.tglAkhir = new Date(chacePeriode[1]);
      this.item.namaOrReg = chacePeriode[3]
      if (chacePeriode[2] != undefined) {
        this.listKelompokPasien = [chacePeriode[2]]
        this.item.dataKelPasien = chacePeriode[2]
      }
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
        this.item.NoOrder = chacePeriode[8]
      }
      this.getData();
    }
    else {
      this.getData();
    }
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.ruangan;
    }
  }

  cari() {
    this.getData()
  }

  getData() {
    var tempRuanganId = "";
    var tempRuanganIdArr = undefined;
    if (this.item.dataRuangan != undefined) {
      tempRuanganId = this.item.dataRuangan.id;
      tempRuanganIdArr = { id: this.item.dataRuangan.id, ruangan: this.item.dataRuangan.ruangan }
    }

    var tempInstalasiId = "";
    var tempInstalasiIdArr = undefined;
    if (this.item.dataDepartemen != undefined) {
      tempInstalasiId = this.item.dataDepartemen.id;
      tempInstalasiIdArr = { id: this.item.dataDepartemen.id, departemen: this.item.dataDepartemen.departemen }
    }

    var kelompokPasienId = "";
    var tempkelompokPasienArr = undefined;
    if (this.item.dataKelPasien != undefined) {
      kelompokPasienId = this.item.dataKelPasien.id;
      tempkelompokPasienArr = { id: this.item.dataKelPasien.id, kelompokpasien: this.item.dataKelPasien.kelompokpasien }
    }

    var tempRuanganTujuanId = "";
    if (this.item.dataRuanganTujuan != undefined) {
      tempRuanganTujuanId = "&ruangTujuanid=" + this.item.dataRuanganTujuan.id;
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

    var tempNoOrder = "";
    if (this.item.NoOrder != undefined) {
      tempNoOrder = this.item.NoOrder;
    }

    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = "&jmlRow=" + this.item.jmlRows
    }

    var isNotVerif = ""
    if (this.item.SelectedVerifikasi) {
      var isNotVerif = "isNotVerif=" + true;
    }

    var tglAwal = ""
    var tglAkhir = ""
    if (this.item.SelectedVerifikasi == false) {
      tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
      tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    }

    var KelUserid = "&KelUserid=" + this.dataLogin.kelompokUser.id;
    var KelUser = "&KelUser=" + this.dataLogin.kelompokUser.kelompokUser;
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempkelompokPasienArr,
      3: tempNamaOrReg,
      4: tempNoReg,
      5: tempRuanganIdArr,
      6: tempInstalasiIdArr,
      7: tempNoRm,
      8: tempNoOrder
    }
    this.cacheHelper.set('OrderDaftarPenunjangCtrl', chacePeriode);
    this.apiService.get("penunjang/get-daftar-order?"
      + isNotVerif
      + "&tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&namapasien=" + tempNamaOrReg
      + "&ruangId=" + tempRuanganId
      + "&noregistrasi=" + tempNoReg
      + "&deptId=" + tempInstalasiId
      + "&nocm=" + tempNoRm
      + "&noOrders=" + tempNoOrder
      + "&kelId=" + kelompokPasienId
      + tempRuanganTujuanId + KelUserid + KelUser
      + jmlRow).subscribe(table => {
        var data = table.data;
        for (var i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          var now = moment(new Date(element.tglregistrasi)).format('YYYY-MM-DD');
          var tgllahir = moment(new Date(element.tgllahir)).format('YYYY-MM-DD');
          var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
          element.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari';
          element.isCito = element.cito
          if (element.cito == true) {
            element.cito = '✔'
            element.classs = 'p-tag p-tag-danger';
          } else {
            element.cito = '✘'
            element.classs = 'p-tag p-tag-info';
          }
          if (element.status == "MASUK") {
            element.class = 'p-tag p-tag-warning';
          } else if (element.status == "Verifikasi") {
            element.class = 'p-tag p-tag-info';
          } else {
            element.class = 'p-tag p-tag-success';
          }
          // switch (element[i].status) {
          //   case "MASUK":
          //     element.myStyle = { 'background-color': '#606572', 'color': '#F0FFFF' };
          //     break;
          //   case "SELESAI":
          //     element.myStyle = { 'background-color': '#3CB27A', 'color': '#F0FFFF' };
          //     break;
          //   case "PROSES":
          //     element.myStyle = { 'background-color': '#FF0000', 'color': '#F0FFFF' };
          //     break;
          // }
        }

        this.dataTable = data;
      })
  }

  Verifikasi(event: any) {
    if (event.checked == false) {
      this.disableVer = false;
    } else {
      this.disableVer = true;
    }
  }
  selectData(event) {
    this.selected = event

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

  tutupPop() {
    this.isSimpan = false;
    this.item.pasien = {};
    this.idJenisPelayanan = undefined;
    this.idKelas = undefined;
    this.item.No = undefined;
    this.item.Layanan = undefined;
    this.item.Jumlah = undefined;
    this.item.Harga = undefined;
    this.item.DokterOrder = undefined;
    this.item.DokterVerifikasi = undefined;
    this.item.catatanLab = undefined;
    this.dataTableDV = [];
    this.ruanganTujuan = undefined;
    this.pop_VerifikasiOrder = false;
    this.instalasifk = undefined;
    this.namaruanganTujuan = undefined;
    this.selectedDV = undefined;
    this.selected = undefined;
    this.idRuanganAsal = undefined;
  }

  verifikasiOrder() {
    this.isSimpan = false;
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }
    if (this.selected.status == 'SELESAI') {
      this.alertService.warn("Info!", "Data Sudah Diverifikasi");
      return
    }

    this.item.pasien = this.selected;
    this.listDokterOrder.push({ id: this.selected.pegawaiorderidfk, namalengkap: this.selected.pegawaiorder })
    this.item.DokterOrder = { id: this.selected.pegawaiorderidfk, namalengkap: this.selected.pegawaiorder };
    this.loadDiagnosa(this.selected.noregistrasi)
    this.idJenisPelayanan = undefined;
    this.idKelas = undefined;
    this.idKelas = this.selected.klsid;
    this.ruanganTujuan = this.selected.ruangantujuanidfk;
    this.namaruanganTujuan = this.selected.ruangantujuan;
    this.idRuanganAsal = this.selected.ruanganidfk;
    this.loadjenisPelayanan(this.selected.norec_pd, this.ruanganTujuan)
    this.loadDataVerif();
  }

  loadjenisPelayanan(norec_pd, ruId) {
    this.apiService.get('general/get-jenis-pelayanan?norec_pd=' + norec_pd).subscribe(e => {
      this.idJenisPelayanan = e.jenispelayanan
    })
  }

  filterLayanan(event) {
    if (this.selected == undefined) return
    let query = event.query;
    this.apiService.get("tindakan/get-tindakan?namaproduk=" + query
      + "&idRuangan="
      + this.selected.ruangantujuanidfk
      + "&idKelas="
      + this.selected.klsid
      + "&idJenisPelayanan="
      + this.idJenisPelayanan)
      .subscribe(re => {
        this.listLayanan = re;
      })
  }

  changeLayanan(event) {
    var data = event;
    if (event != undefined && event != "") {
      this.listKomponen = [];
      if (this.item.Jumlah == 0 || this.item.Jumlah == undefined || this.item.Jumlah == "" || this.item.Jumlah == null) {
        this.item.Jumlah = 0;
      }

      this.apiService.get('tindakan/get-komponenharga?idRuangan=' + this.ruanganTujuan
        + "&idKelas=" + this.idKelas
        + "&idProduk=" + event.id
        + "&idJenisPelayanan=" + this.idJenisPelayanan
      ).subscribe(e => {
        var data = e.data;
        if (data.length > 0) {
          this.listKomponen = data;
          this.item.Harga = parseFloat(data[0].hargasatuan)
          if (this.item.Jumlah == 0 || this.item.Jumlah == undefined || this.item.Jumlah == "" || this.item.Jumlah == null) {
            this.item.Jumlah = 1;
          }
        }
      })
    }
  }

  loadDiagnosa(noreg) {
    this.apiService.get("penunjang/get-diagnosapasienbynoreg?noReg=" + noreg
    ).subscribe(data => {
      var dataICD10 = data.datas;
      if (dataICD10.length > 0) {
        var diagnosa = ''
        var a = ""
        var b = ""
        for (let i = 0; i < dataICD10.length; i++) {
          const element = dataICD10[i];
          var c = element.kddiagnosa + '-' + element.namadiagnosa
          b = ", " + c
          a = a + b
        }
        diagnosa = a.slice(1, a.length)
        this.item.pasien.diagnosa = diagnosa
      }
    });
  }

  loadDataVerif() {
    this.data2 = [];
    this.apiService.get("penunjang/get-order-pelayanan?norec_so=" + this.selected.norec_so
      + "&objectkelasfk=" + this.idKelas).subscribe(table => {
        var data = table.data
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          element.qtyproduk = parseFloat(element.qtyproduk);
          this.instalasifk = element.instalasiidfk
          this.data2.push(element);
        }
        this.dataTableDV = this.data2;
        this.pop_VerifikasiOrder = true;
      });
  }

  onRowSelectDV(event: any) {
    this.selectedDV = event.data
    var dataProduk = [];
    this.apiService.get("tindakan/get-tindakan?idRuangan=" + this.selectedDV.idruangan
      + "&objectkelasfk=" + this.selectedDV.klsid
      + "&idJenisPelayanan=" + this.idJenisPelayanan
      + "&idProduk=" + this.selectedDV.prid
    ).subscribe(table => {
      var data = table;
      this.listLayanan.push(data[0])
      for (var i = this.listLayanan.length - 1; i >= 0; i--) {
        if (this.listLayanan[i].id == this.selectedDV.prid) {
          dataProduk = this.listLayanan[i]
          break;
        }
      }
      this.item.Layanan = dataProduk;
      this.changeLayanan(dataProduk);
    })
    this.item.No = this.selectedDV.no;
    this.item.Jumlah = this.selectedDV.qtyproduk;
  }

  tambah() {
    if (this.item.Layanan == undefined) {
      this.alertService.warn("Info,", "Pilih Layanan terlebih dahulu!")
      return;
    }
    if (this.item.Jumlah == 0) {
      this.alertService.warn("Info,", "Qty Harus Diisi!")
      return;
    }

    var nomor = 0
    if (this.data2 == undefined) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }
    var data: any = {};
    if (this.item.No != undefined) {
      for (var i = this.data2.length - 1; i >= 0; i--) {
        if (this.data2[i].no == this.item.No) {
          data.no = this.item.No
          data.prid = this.item.Layanan.id
          data.namaproduk = this.item.Layanan.namaproduk
          data.qtyproduk = parseFloat(this.item.Jumlah)
          data.hargasatuan = parseFloat(this.item.Harga)
          data.details = this.listKomponen
          data.ruangantujuan = this.namaruanganTujuan
          data.idruangan = this.ruanganTujuan
          data.objectdepartemenfk = this.instalasifk
          data.tglpelayanan = moment(new Date()).format('YYYY-MM-DD HH:mm')
          this.data2[i] = data;
          this.dataTableDV = this.data2;
        }
      }
    } else {
      data = {
        no: nomor,
        prid: this.item.Layanan.id,
        namaproduk: this.item.Layanan.namaproduk,
        qtyproduk: parseFloat(this.item.Jumlah),
        hargasatuan: parseFloat(this.item.Harga),
        details: this.listKomponen,
        ruangantujuan: this.namaruanganTujuan,
        tglpelayanan: moment(new Date()).format('YYYY-MM-DD HH:mm'),
        idruangan: this.ruanganTujuan,
        objectdepartemenfk: this.instalasifk
      }
      this.data2.push(data)
      this.dataTableDV = this.data2;
    }
    this.batalInput();
  }

  batalInput() {
    this.item.No = undefined;
    this.item.Layanan = undefined;
    this.item.Jumlah = undefined;
    this.item.Harga = undefined;
  }

  hapusItem(e) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == e.no) {
        this.data2.splice(i, 1);
      }
    }
    for (var i = this.dataTableDV.length - 1; i >= 0; i--) {
      if (this.dataTableDV[i].no == e.no) {
        this.dataTableDV.splice(i, 1);
      }
    }
  }

  simpanVerifikasi() {
    if (this.item.DokterVerifikasi == undefined) {
      this.alertService.warn("Info,", "Dokter Verifikasi harus di isi!")
      return;
    }
    var dataPost = [];
    for (let i = 0; i < this.dataTableDV.length; i++) {
      const element = this.dataTableDV[i];
      var datasys = {
        "produkid": element.prid,
        "hargasatuan": element.hargasatuan,
        "qtyproduk": element.qtyproduk,
        "komponenharga": element.details,
        "tglpelayanan": element.tglpelayanan,
        "nourut": element.no
      }
      dataPost.push(datasys)
    }
    var norec_pp = ''
    if (this.dataTableDV[0].norec_pp != null) {
      norec_pp = this.dataTableDV[0].norec_pp
    }

    var itemsave = {
      "bridging": dataPost,
      "norec_pp": norec_pp,
      "noorder": this.item.pasien.noorder,
      "norec_so": this.selected.norec_so,
      "kelasidfk": this.selected.kelasidfk,
      "norec_pd": this.selected.norec_pd,
      "ruangantujuanidfk": this.ruanganTujuan,
      "ruanganasalidfk": this.idRuanganAsal,
      "pegawaiorderidfk": this.selected.objectpegawaiorderfk,
      "iddokterverif": this.item.DokterVerifikasi.id,
      "namadokterverif": this.item.DokterVerifikasi.namalengkap,
      "iddokterorder": this.item.DokterOrder.id,
      "namadokterorder": this.item.DokterOrder.namalengkap,
      "iscito": this.selected.isCito ,
      "details": dataPost,
    }

    var jsonVansLab = {
      "noorder": this.item.pasien.noOrder,
      "iddokterverif": this.item.DokterVerifikasi.id,
      "namadokterverif": this.item.DokterVerifikasi.namalengkap,
      "details": dataPost,
      "catatan": this.item.catatanLab != undefined ? this.item.catatanLab : null,

    }

    if (dataPost.length > 0) {
      for (let i = 0; i < dataPost.length; i++) {
        const element = dataPost[i];
        if (element.komponenharga.length == 0) {
          this.alertService.error("Info,", "Tindakan Belum ada Komponen Harganya!");
          return;
        }
      }

      this.isSimpan = true;
      var departemen = this.instalasifk;
      var ruangan = this.ruanganTujuan;
      if (this.dataLogin.kelompokUser.kelompokUser == "laboratorium") {
        this.apiService.post('penunjang/save-pelayanan-pasien', itemsave)
          .subscribe(table => {
            this.apiService.postLog('Verifikasi Order Lab', 'norec Transaksi Order', this.selected.norec_so,
              'Verif Laboratorium No Order - ' + this.item.pasien.noOrder + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
            this.isSimpan = false;
            this.tutupPop();
            this.getData();
          },error =>{
            this.isSimpan = false;
          });
      } else if (this.dataLogin.kelompokUser.kelompokUser == "radiologi") {
        this.apiService.post('penunjang/save-pelayanan-pasien', itemsave)
          .subscribe(table => {
            this.apiService.postLog('Verifikasi Order Radiologi', 'norec Transaksi Order', this.selected.norec_so,
              'Verif Radiologi No Order - ' + this.item.pasien.noOrder + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
            this.isSimpan = false;
            this.tutupPop();
            this.getData();
          },error =>{
            this.isSimpan = false;
          });
      } else if (this.dataLogin.kelompokUser.kelompokUser == "bedah") {
        this.apiService.post('penunjang/save-pelayanan-pasien', itemsave)
          .subscribe(table => {
            this.apiService.postLog('Verifikasi Order Bedah', 'norec Transaksi Order', this.selected.norec_so,
              'Verif Bedah No Order - ' + this.item.pasien.noOrder + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
            this.isSimpan = false;
            this.tutupPop();
            this.getData();
          },error =>{
            this.isSimpan = false;
          });
      } else {
        this.alertService.error("Info,", "Data Belum Dipilih!");
        return;
      }
    }
  }

  pengkajianMedis() {
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }
    this.apiService.get("registrasi/get-apd?noregistrasi="
      + this.selected.noregistrasi
      + "&objectruanganlastfk=" + this.selected.ruanganidfk
    ).subscribe(data => {
      var dataAntrian = data.data;
      if (dataAntrian != undefined) {
        this.router.navigate(['rekam-medis', this.selected.norec, dataAntrian.norec_apd])
      }
    })
  }

  detailVerifikasi = function () {
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }
    if (this.selected.status != 'SELESAI' && this.selected.status != 'Sudah Verifikasi') {
      this.alertService.warn("Info!", "Data Belum di verifikasi");
      return;
    }

    this.item.pasien = this.selected;
    this.dataTableDVs = [];
    this.apiService.get('penunjang/get-detail-verifikasi?norec_so=' + this.selected.norec_so).subscribe(table => {
      var data = table.data;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
      }
      this.dataTableDVs = data;
      this.pop_DetailVerifikasi = true;
    })
  }

  lihatHasil() {
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }

    if (this.selected.status != 'SELESAI') {
      this.alertService.warn("Info!", "Data Belum di verifikasi");
      return;
    }

    this.apiService.get('penunjang/get-detail-pasien?norec_so=' + this.selected.norec_so).subscribe(table => {
      var data = table.data[0];
      if (this.dataLogin.kelompokUser.kelompokUser == "laboratorium") {
        this.router.navigate(['hasil-laboratorium', data.norec_pd, data.norec_apd])
      }
    })
  }
  hapusOrder= function () {
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }
    if (this.selected.status == 'SELESAI' && this.selected.status == 'Sudah Verifikasi') {
      this.alertService.warn("Info!", "Data Sudah di verifikasi");
      return;
    }

    this.item.pasien = this.selected;
    var itemsave = {
      "norec" : this.selected.norec_so
    }
    this.apiService.post('penunjang/delete-order', itemsave).subscribe(table => {      
            this.getData();
          },error =>{
            this.isSimpan = false;
          }
    );        
  }
  cetakBuktiLayanan() {

  }
  onRowSelectDVs(e) {

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
    this.item = { pasien: {} }
    this.item.SelectedVerifikasi = true;
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.getData();
  }
}
