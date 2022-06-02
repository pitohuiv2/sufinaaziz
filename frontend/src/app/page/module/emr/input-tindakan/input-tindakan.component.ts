import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
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
  selector: 'app-input-tindakan',
  templateUrl: './input-tindakan.component.html',
  styleUrls: ['./input-tindakan.component.scss'],
  providers: [ConfirmationService]
})
export class InputTindakanComponent implements OnInit, AfterViewInit {
  params: any = {};
  currentNorecPd: any;
  currentNorecApd: any;
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  totalRecords: number;
  item: any = {
    pasien: {},
    jumlah: 1,
    tglPelayanan: new Date(),
  };
  dataLogin: any;
  kelUser: any;
  currentNorecPD: any
  currentNorecAPD: any
  maxDateValue = new Date()
  isClosing: boolean = false
  listProduk: any[] = []
  listJenisPelaksana: any[] = []
  listPegawai: any[] = []
  dataTindakan: any[] = []
  listKomponen: any[] = []
  data2: any[] = []
  show1: boolean
  show2: boolean
  show3: boolean
  show4: boolean
  show5: boolean
  isSimpan: boolean
  hideEMR: boolean = true
  isNext: boolean
  @ViewChild(HeadPasienComponent, { static: false }) h: HeadPasienComponent;
  constructor(private apiService: ApiService,
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
      if (params['norec_rp'] == undefined) {
        var cache = this.cacheHelper.get('cacheEMR_qwertyuiop')
        if (cache != undefined) {
          cache = JSON.parse(cache)
          this.currentNorecPD = cache.norec_pd
          this.currentNorecAPD = cache.norec
          this.item.pasien = cache
          this.isClosing = false
          this.hideEMR = false
          if (cache.isclosing == true) {
            this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(cache.tglclosing)).format('DD-MMM-YYYY HH:mm'))
            this.isClosing = true
          }
        } else {
          window.history.back()
        }
      } else {
        this.currentNorecPD = params['norec_rp'];
        this.currentNorecAPD = params['norec_dpr']
        this.loadHead()
      }
    })

  }
  loadHead() {
    this.isClosing = false
    this.apiService.get("registrasi/get-pasien-bynorec?norec_pd="
      + this.currentNorecPD
      + "&norec_apd="
      + this.currentNorecAPD)
      .subscribe(e => {
        e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
        e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
        this.h.item.pasien = e;
        this.item.pasien = e;
        this.apiService.get("sysadmin/general/get-status-close/" + this.item.pasien.noregistrasi).subscribe(rese => {
          if (rese.status == true) {
            this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(rese.tglclosing)).format('DD-MMM-YYYY HH:mm'))
            this.isClosing = true
          }
        })
      })
  }

  ngOnInit(): void {
    this.isClosing = false
    this.loadCombo()
  }
  loadCombo() {
    this.apiService.get("tindakan/get-combo")
      .subscribe(da => {
        this.listJenisPelaksana = da.jenispelaksana;
        this.item.jenisPelaksana = this.listJenisPelaksana[6]
        this.getPegawaiByJenis(this.item.jenisPelaksana)
        this.item.nilaiStatusCito = parseFloat(da.tarifcito);
      })
  }

  filterProduk(event) {
    if (this.item.pasien.objectruanganfk == undefined) return
    let query = event.query;
    this.apiService.get("tindakan/get-tindakan?namaproduk=" + query
      + "&idRuangan="
      + this.item.pasien.objectruanganfk
      + "&idKelas="
      + this.item.pasien.objectkelasfk
      + "&idJenisPelayanan="
      + this.item.pasien.objectjenispelayananfk)
      .subscribe(re => {
        for (let x = 0; x < re.length; x++) {
          const element = re[x];
          element.hargasatuan =  this.formatRupiah(parseFloat(element.hargasatuan),'')
        }
        this.listProduk = re;
      })
  }
  getPegawaiByJenis(id) {
    this.apiService.get("tindakan/get-pegawaibyjenispetugas?idJenisPetugas=" + id.id).subscribe(dat => {
      this.listPegawai = dat.jenispelaksana
    });
  }

  getHargaTindakan() {
    this.item.hargaTindakan = 0
    this.item.jumlah = 0
    this.listKomponen = []
    if (this.item.namaProduk != undefined) {
      this.apiService.get("tindakan/get-komponenharga?idRuangan="
        + this.item.pasien.objectruanganfk
        + "&idKelas=" + this.item.pasien.objectkelasfk
        + "&idProduk=" + this.item.namaProduk.id
        + "&idJenisPelayanan=" + this.item.pasien.objectjenispelayananfk
        + "&hnpid=" + this.item.namaProduk.hnpid
      ).subscribe(dat => {
        this.listKomponen = dat.data;
        this.item.hargaTindakan = dat.data2[0].hargasatuan //this.item.namaProduk.hargasatuan;
        this.item.jumlah = 1;
      })
    }
  }
  multiSelectArrayToString(item) {
    if (item.length > 0) {
      return item.map(function (elem) {
        return elem.namalengkap
      }).join(", ");
    }
  }
  multiSelectArrayToString2(item) {
    if (item.length > 0) {
      return item.map(function (elem) {
        return elem.produk.namaproduk
      }).join(", ");
    }
  }
  hapusAll() {
    this.data2 = [];
    this.dataTindakan = this.data2
    this.item.StatusCito = undefined;
    this.item.JasaCito = 0;
    this.item.NilaiCito = 0;
    this.item.totalAlls = this.formatRupiah(0, 'Rp.')
  }

  hapusItem(dataItem) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].rowNumber == dataItem.rowNumber) {
        this.data2.splice(i, 1);
      }
    }
    this.dataTindakan = this.data2
    var subTotal = 0;
    for (var i = this.data2.length - 1; i >= 0; i--) {
      subTotal = subTotal + parseFloat(this.data2[i].subTotal)
    }
    this.item.totalAlls = this.formatRupiah(subTotal, 'Rp.')
  }
  tambahTindakan() {
    if (this.isClosing == true) {
      this.alertService.error("Info", "Data sudah di closing, tidak bisa input tindakan ")
      return
    }
    if (this.item.namaProduk == undefined) {
      this.alertService.error("Info", "Tindakan harus di isi")
      return
    }
    if (this.item.jumlah == 0) {
      this.alertService.error("Info", "Jumlah tidak boleh nol")
      return
    }

    // if(this.item.paramedis != true){
    if (this.item.jenisPelaksana == undefined) {
      this.alertService.error("Info", "Jenis Pelaksana harus di isi")
      return
    }
    if (this.item.petugasPelaksana == undefined || this.item.petugasPelaksana.length == 0) {
      this.alertService.error("Info", 'Petugas Pelaksana harus di isi')
      return
    }
    // }

    // if (this.item.jenisPelaksana2) {
    //   for (let i = 0; i < this.selectedPegawai.length; i++) {
    //     for (let j = 0; j < this.selectedPegawai2.length; j++) {
    //       if (this.item.jenisPelaksana.jenispetugaspe == this.item.jenisPelaksana2.jenispetugaspe
    //         && this.selectedPegawai[i].namalengkap == this.selectedPegawai2[j].namalengkap) {
    //         toastr.error('Dokter Pemeriksa yg sama tidak boleh lebih dari 2 kali')
    //         return
    //       }
    //     }
    //   }
    // }

    var statuscito = "";
    if (this.item.iscito == true) {
      statuscito = "✔";
      this.item.JasaCito = parseFloat(this.item.hargaTindakan) * this.item.nilaiStatusCito;
      this.item.NilaiCito = this.item.nilaiStatusCito;
    } else {
      statuscito = "";
      this.item.JasaCito = 0;
      this.item.NilaiCito = 0;
    }

    var nomor = 0
    if (this.dataTindakan.length == 0) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }




    var data: any = {};
    data = {
      "rowNumber": nomor,
      "tglPelayanan": moment(this.item.tglPelayanan).format('YYYY-MM-DD HH:mm:ss'),
      "produk": this.item.namaProduk,//this.item.noRegistrasi,
      "qty": this.item.jumlah,
      "hargaSatuan": this.item.hargaTindakan,
      "hargadiskon": 0,
      "subTotal": (this.item.hargaTindakan) * (this.item.jumlah),
      "listKomponen": this.listKomponen,
      "statuscito": statuscito,
      "cito": this.item.iscito,
      "jasacito": this.item.JasaCito,
      "isparamedis": this.item.paramedis,
      "jenispelayananfk": this.item.pasien.objectjenispelayananfk,
      "nilaicito": this.item.NilaiCito,
      "pelayananpetugas": []
    }

    var pushData = {
      "idParent": data.rowNumber,
      "jenisPetugas": {
        "id": this.item.jenisPelaksana.id,
        "jenisPelaksana": this.item.jenisPelaksana.jenispetugaspe
      },
      "objectjenispetugaspefk": this.item.jenisPelaksana.id,
      "listpegawai": this.item.petugasPelaksana,
      "namalengkap": this.multiSelectArrayToString(this.item.petugasPelaksana)
    }
    data.pelayananpetugas.push(pushData)
    if (this.item.jenisPelaksana1 != undefined && (this.item.petugasPelaksana1 != undefined && this.item.petugasPelaksana1.length > 0)) {
      var pushData = {
        "idParent": data.rowNumber,
        "jenisPetugas": {
          "id": this.item.jenisPelaksana1.id,
          "jenisPelaksana": this.item.jenisPelaksana1.jenispetugaspe
        },
        "objectjenispetugaspefk": this.item.jenisPelaksana1.id,
        "listpegawai": this.item.petugasPelaksana1,
        "namalengkap": this.multiSelectArrayToString(this.item.petugasPelaksana1)
      }
      data.pelayananpetugas.push(pushData)
      this.show1 = false;
      delete this.item.jenisPelaksana1;
      delete this.item.petugasPelaksana1
    }
    if (this.item.jenisPelaksana2 != undefined && (this.item.petugasPelaksana2 != undefined && this.item.petugasPelaksana2.length > 0)) {
      var pushData = {
        "idParent": data.rowNumber,
        "jenisPetugas": {
          "id": this.item.jenisPelaksana2.id,
          "jenisPelaksana": this.item.jenisPelaksana2.jenispetugaspe
        },
        "objectjenispetugaspefk": this.item.jenisPelaksana2.id,
        "listpegawai": this.item.petugasPelaksana2,
        "namalengkap": this.multiSelectArrayToString(this.item.petugasPelaksana2)
      }
      data.pelayananpetugas.push(pushData)
      this.show2 = false;
      delete this.item.jenisPelaksana2;
      delete this.item.petugasPelaksana2
    }
    if (this.item.jenisPelaksana3 != undefined && (this.item.petugasPelaksana3 != undefined && this.item.petugasPelaksana3.length > 0)) {
      var pushData = {
        "idParent": data.rowNumber,
        "jenisPetugas": {
          "id": this.item.jenisPelaksana3.id,
          "jenisPelaksana": this.item.jenisPelaksana3.jenispetugaspe
        },
        "objectjenispetugaspefk": this.item.jenisPelaksana3.id,
        "listpegawai": this.item.petugasPelaksana3,
        "namalengkap": this.multiSelectArrayToString(this.item.petugasPelaksana3)
      }
      data.pelayananpetugas.push(pushData)
      this.show3 = false;
      delete this.item.jenisPelaksana3;
      delete this.item.petugasPelaksana3
    }
    if (this.item.jenisPelaksana4 != undefined && (this.item.petugasPelaksana4 != undefined && this.item.petugasPelaksana4.length > 0)) {
      var pushData = {
        "idParent": data.rowNumber,
        "jenisPetugas": {
          "id": this.item.jenisPelaksana4.id,
          "jenisPelaksana": this.item.jenisPelaksana4.jenispetugaspe
        },
        "objectjenispetugaspefk": this.item.jenisPelaksana4.id,
        "listpegawai": this.item.petugasPelaksana4,
        "namalengkap": this.multiSelectArrayToString(this.item.petugasPelaksana4)
      }
      data.pelayananpetugas.push(pushData)
      this.show4 = false;
      delete this.item.jenisPelaksana4;
      delete this.item.petugasPelaksana4
    }
    if (this.item.jenisPelaksana5 != undefined && (this.item.petugasPelaksana5 != undefined && this.item.petugasPelaksana5.length > 0)) {
      var pushData = {
        "idParent": data.rowNumber,
        "jenisPetugas": {
          "id": this.item.jenisPelaksana5.id,
          "jenisPelaksana": this.item.jenisPelaksana5.jenispetugaspe
        },
        "objectjenispetugaspefk": this.item.jenisPelaksana5.id,
        "listpegawai": this.item.petugasPelaksana5,
        "namalengkap": this.multiSelectArrayToString(this.item.petugasPelaksana5)
      }
      data.pelayananpetugas.push(pushData)
      this.show5 = false;
      delete this.item.jenisPelaksana5;
      delete this.item.petugasPelaksana5
    }
    this.data2.push(data)
    this.dataTindakan = this.data2
    var subTotal = 0;
    for (var i = this.data2.length - 1; i >= 0; i--) {
      subTotal = subTotal + parseFloat(this.data2[i].subTotal)
    }
    this.item.totalAlls = this.formatRupiah(subTotal, 'Rp.')

    this.kosongkan();
    this.item.Cito = undefined;

  }
  kosongkan() {
    this.item.namaProduk = undefined;
    this.item.hargaTindakan = undefined
    this.item.jumlah = undefined;
  }
  save() {
    if (this.dataTindakan.length == 0) {
      this.alertService.error("Info", 'Tindakan belum di isi')
      return
    }
    var dataTindakanFix = [];
    for (let i = 0; i < this.dataTindakan.length; i++) {
      const e = this.dataTindakan[i];
      if (e.listKomponen.length <= 0) {
        this.alertService.error("Info", "Tidak bisa Simpan, Komponen Tindakan tidak ada");
        return
      }
      else {
        var statusCitto = 0
        if (e.cito == true) {
          statusCitto = 1;
        }
        var nilaiCito = 0
        var nilaijasaCito = 0
        if (e.jasacito != 0) {
          nilaiCito = e.jasacito;
          nilaijasaCito = e.nilaicito;
        }

        dataTindakanFix.push({
          "noregistrasifk": this.currentNorecAPD,
          "tglregistrasi": this.item.pasien.tglregistrasi,
          "tglpelayanan": moment(e.tglPelayanan).format('YYYY-MM-DD HH:mm:ss'),
          "ruangan": e.ruangan,
          "produkfk": e.produk.id,
          "hargasatuan": e.hargaSatuan,
          "diskon": e.hargadiskon,
          "hargajual": e.hargaSatuan,
          "harganetto": e.hargaSatuan,
          "jumlah": e.qty,
          "kelasfk": this.item.pasien.objectkelasfk,
          "pelayananpetugas": e.pelayananpetugas,
          "komponenharga": e.listKomponen,
          "keterangan": this.item.pemeriksaanKeluar === true ? 'Pemeriksaan Keluar' : '-',
          "iscito": statusCitto,
          "jasacito": nilaiCito,
          "isparamedis": e.isparamedis != undefined ? e.isparamedis : false,
          "jenispelayananfk": e.jenispelayananfk,
          "nilaicito": nilaijasaCito,
        });
      }
    }

    var namaPROD = this.multiSelectArrayToString2(this.dataTindakan)

    var objSave = {
      "pelayananpasien": dataTindakanFix
    }
    this.isSimpan = true
    this.apiService.post('tindakan/save-tindakan', objSave).subscribe(e => {

      this.apiService.postLog('Input Tindakan', 'norec PP', e.dataPP.norec, 'Input Tindakan : ('
        + namaPROD + ') pada No Registrasi ' + this.item.pasien.noregistrasi + ' di ' + this.item.pasien.namaruangan).subscribe(z => { })

      this.hapusAll();
      this.isSimpan = false
      if (this.hideEMR == false) {
        window.history.back()
      }
    }, function (error) {
      this.isSimpan = false
    })
  }
  cancel() {
    this.hapusAll()
    window.history.back()
  }
  showKeun(id) {
    if (id == 1) {
      this.show1 = true
    }
    if (id == 2) {
      this.show2 = true
    }
    if (id == 3) {
      this.show3 = true
    }
    if (id == 4) {
      this.show4 = true
    }
    if (id == 5) {
      this.show5 = true
    }

  }
  hide(id) {
    if (id == 1) {
      this.show1 = false
    }
    if (id == 2) {
      this.show2 = false
    }
    if (id == 3) {
      this.show3 = false
    }
    if (id == 4) {
      this.show4 = false
    }
    if (id == 5) {
      this.show5 = false
    }

  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }
  cekPaket(e) {

  }
}
