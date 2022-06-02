import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { jsPDF } from "jspdf";
@Component({
  selector: 'app-v-rencana-kontrol',
  templateUrl: './v-rencana-kontrol.component.html',
  styleUrls: ['./v-rencana-kontrol.component.scss']
})
export class VRencanaKontrolComponent implements OnInit {
  itemV2: any = {
    tipe: "1"
  }
  myVar: boolean = false
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD'),
    jenisPelayanan: "1",
    tglAwal: new Date(),
    tglAkhir: new Date(),
    tglRencanaKontrol2: new Date(),
    peserta: {
      statusPeserta: {},
      hakKelas: {},
      provUmum: {}
    },
  }
  daftar: any = {
    periodeAwal: new Date(),
    periodeAkhir: new Date(),

  }
  listDPJP: any[]
  dataSource2: any
  data2: any = []
  data3: any = []
  listDiagnosa: any = []
  listPoli: any = []
  listFaskes: any = []
  jsonResult2: any
  jsonResult: any
  jsonResult3: any
  jsonResult4: any
  jsonResult5: any
  jsonResult6: any
  jsonResult7: any
  jsonResult8: any
  jsonResult9: any
  jsonResult10: any
  dataSource: any
  column: any[];
  column2: any[];
  dataSelected: any
  column3: any = []
  dataSource3: any
  dataSourceRen: any = []
  enabledDetail: boolean = false
  enabledDetail2: boolean = false
  listFilter = [{ kode: 2, nama: 'Tgl Rencana Kontrol' }, { kode: 1, nama: 'Tgl Entri' }]
  namaPPk: any
  listBulan = [
    { id: 1, mm: '01', name: 'Januari' },
    { id: 2, mm: '02', name: 'Februari' },
    { id: 3, mm: '03', name: 'Maret' },
    { id: 4, mm: '04', name: 'April' },
    { id: 5, mm: '05', name: 'Mei' },
    { id: 6, mm: '06', name: 'Juni' },
    { id: 7, mm: '07', name: 'Juli' },
    { id: 8, mm: '08', name: 'Agustus' },
    { id: 9, mm: '09', name: 'September' },
    { id: 10, mm: '10', name: 'Oktober' },
    { id: 11, mm: '11', name: 'November' },
    { id: 12, mm: '12', name: 'Desember' }
  ]
  listStatusPlg = [
    { "id": "1", name: "Atas Persetujuan Dokter" },
    { "id": "3", name: "Atas Permintaan Sendiri" },
    { "id": "4", name: "Meninggal" },
    { "id": "5", name: "Lain-lain" }
  ]

  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.loadCombo()
    this.namaPPk = ''
    this.apiService.get('registrasi/get-setting-asuransi').subscribe(dat => {
      this.namaPPk = dat.namaPPKRujukan
    })
    this.item.tipe = '1'
    this.item.filter = this.listFilter[0]
    this.column = [
      { field: 'noSuratKontrol', header: 'No Surat', width: "150px" },
      { field: 'namaJnsKontrol', header: 'Jenis', width: "150px", filter: true },
      { field: 'tglRencanaKontrol', header: 'Tgl Rencana Kontrol', width: "150px", filter: true },
      { field: 'tglTerbitKontrol', header: 'Tgl Entri', width: "150px", filter: true },
      { field: 'noSepAsalKontrol', header: 'No SEP Asal', width: "150px" },
      { field: 'namaPoliAsal', header: 'Poli Asal', width: "150px" },
      { field: 'namaDokter', header: 'DPJP', width: "150px" },

    ];
    this.column2 = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'kddiagnosa', header: 'Kode', width: "100px" },
      { field: 'namadiagnosa', header: 'Diagnosa', width: "150px" },
      { field: 'jenis', header: 'Jenis', width: "80px" },
    ];
    this.column3 = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'kddiagnosatindakan', header: 'Kode', width: "100px" },
      { field: 'namadiagnosatindakan', header: 'Procedure/tindakan', width: "150px" },

    ];
  }
  loadCombo() {
    this.item.kodePPK = ''
    this.apiService.get('registrasi/get-setting-asuransi').subscribe(dat => {

      this.item.kodePPK = dat.kodePPKRujukan
    })


  }
  cariNoka() {
    this.enabledDetail = false
    this.enabledDetail2 = false
    if (this.item.noKartu == undefined) return

    let json = {

      "url": "Peserta/nokartu/" + this.item.noKartu + "/tglSEP/" + moment(new Date()).format('YYYY-MM-DD'),
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {

      if (e.metaData.code === "200") {
        this.enabledDetail2 = true;
        this.item.peserta = e.response.peserta

      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }

  cariSep() {
    this.enabledDetail = false
    this.enabledDetail2 = false
    if (this.item.sep == undefined) return

    let json = {
      "url": "RencanaKontrol/nosep/" + this.item.sep,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code === "200") {
        this.enabledDetail = true;
        this.item.noSep = e.response.noSep
        this.item.jnsPelayanan = e.response.jnsPelayanan
        this.item.tglSep = e.response.tglSep
        this.item.poli = e.response.poli
        this.item.diagnosa = e.response.diagnosa
        this.item.noKartu = e.response.peserta.noKartu
        this.item.nama = e.response.peserta.nama
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  selectPoli(eve) {


    let json = {
      "url": "RencanaKontrol/JadwalPraktekDokter/JnsKontrol/" + this.item.jenisPelayanan
        + "/KdPoli/" + eve.kode + "/TglRencanaKontrol/" + moment(this.item.tglRencanaKontrol).format('YYYY-MM-DD'),
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == 200) {
        for (let x = 0; x < e.response.list.length; x++) {
          const element = e.response.list[x];
          element.kode = element.kodeDokter
          element.nama = element.namaDokter
        }
        this.listDPJP = e.response.list;
      }
      else {
        this.alertService.error('Info', e.metaData.message);
      }

    })

  }
  filter(event) {
    if (event.query == '') return
    this.listPoli = []
    let json = {
      "url": "referensi/poli/" + event.query,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.response != null) {
        this.listPoli = e.response.poli
      }

    })
  }
  post(url, method, jsonView) {
    let jenis = method
    let data = {}
    if (method == 'DELETE') {
      data = {
        "request": {
          "t_sep": {
            "noSep": this.item.delete,
            "user": "Xoxo"
          }
        }
      }
    }
    if (method == 'pengajuan') {
      data = {
        "request": {
          "t_sep": {
            "noKartu": this.item.kartu,
            "tglSep": moment(this.item.tglSep).format('YYYY-MM-DD'),
            "jnsPelayanan": this.item.tipe,
            "jnsPengajuan": this.item.jenis,
            "keterangan": this.item.ket,
            "user": "Xoxo"
          }
        }
      }
    }
    if (method == 'approv') {
      data = {
        "request": {
          "t_sep": {
            "noKartu": this.item.kartu,
            "tglSep": moment(this.item.tglSep).format('YYYY-MM-DD'),
            "jnsPelayanan": this.item.tipe,
            "keterangan": this.item.ket,
            "user": "Xoxo"
          }
        }
      }
    }
    if (method == 'updatePlg') {
      data = {
        "request": {
          "t_sep": {
            "noSep": this.item.sep,
            "statusPulang": this.item.status.id,
            "noSuratMeninggal": this.item.status.id == 4 ? this.item.noSurat : "",
            "tglMeninggal": this.item.status.id == 4 ? moment(this.item.tglMeningal).format('YYYY-MM-DD') : "",
            "tglPulang": moment(this.item.tglPulang).format('YYYY-MM-DD'),
            "noLPManual": this.item.noLP ? this.item.noLP : "",
            "user": "Xoxo"
          }
        }
      }
    }
    if (method == 'hapusInternal') {
      data = {
        "request": {
          "t_sep": {
            "noSep": this.item.noSEP2,
            "noSurat": this.item.noSurat2,
            "tglRujukanInternal": moment(this.item.tglRujukan).format('YYYY-MM-DD'),
            "kdPoliTuj": this.item.poli.kode,
            "user": "Xoxo"
          }
        }
      }

    }
    if (method == 'rujukanInsert') {
      method = 'POST'
      data = {
        "request": {
          "t_rujukan": {
            "noSep": this.itemV2.noSep,
            "tglRujukan": moment(this.itemV2.tglRujukan).format("YYYY-MM-DD"),
            "tglRencanaKunjungan": moment(this.itemV2.tglRencanaKunjungan).format("YYYY-MM-DD"),
            "ppkDirujuk": this.itemV2.ppkDirujuk.kode,
            "jnsPelayanan": this.itemV2.jnsPelayanan,
            "catatan": this.itemV2.catatan,
            "diagRujukan": this.itemV2.diagRujukan.kode,
            "tipeRujukan": this.itemV2.tipeRujukan,
            "poliRujukan": this.itemV2.poliRujukan.kode,
            "user": "Xoxo"
          }
        }
      }
    }

    if (method == 'rujukanUpdate') {
      {
        method = 'PUT'
        data = {
          "request": {
            "t_rujukan": {
              "noRujukan": this.itemV2.noRujukan,
              "tglRujukan": moment(this.itemV2.tglRujukan).format("YYYY-MM-DD"),
              "tglRencanaKunjungan": moment(this.itemV2.tglRencanaKunjungan).format("YYYY-MM-DD"),
              "ppkDirujuk": this.itemV2.ppkDirujuk.kode,
              "jnsPelayanan": this.itemV2.jnsPelayanan,
              "catatan": this.itemV2.catatan,
              "diagRujukan": this.itemV2.diagRujukan.kode,
              "tipeRujukan": this.itemV2.tipeRujukan,
              "poliRujukan": this.itemV2.poliRujukan.kode,
              "user": "Xoxo"
            }
          }
        }
      }
    }
    if (method == 'hapusKhusus') {
      method = 'DELETE'
      data = {
        "request": {
          "t_rujukan": {
            "idRujukan": this.item.idRujukan,
            "noRujukan": this.item.noRujukan,
            "user": "Xoxo"
          }
        }
      }
    }

    if (method == 'insertRencana') {
      if (this.item.noSuratKontrol) {
        url = 'RencanaKontrol/Update'
        method = 'PUT'
        data = {
          "request": {
            "noSuratKontrol": this.item.noSuratKontrol,
            "noSEP": this.item.noSep,
            "kodeDokter": this.item.kodeDokter ? this.item.kodeDokter.kode : "",
            "poliKontrol": this.item.poliKontrol ? this.item.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.item.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      } else {
        method = 'POST'
        data = {
          "request":
          {
            "noSEP": this.item.noSep,
            "kodeDokter": this.item.kodeDokter ? this.item.kodeDokter.kode : "",
            "poliKontrol": this.item.poliKontrol ? this.item.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.item.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      }
    }
    if (method == 'insertSPRI') {
      if (this.item.noSuratKontrol) {
        url = 'RencanaKontrol/UpdateSPRI'
        method = 'PUT'
        data = {
          "request": {
            "noSPRI": this.item.noSuratKontrol,
            "kodeDokter": this.item.kodeDokter ? this.item.kodeDokter.kode : "",
            "poliKontrol": this.item.poliKontrol ? this.item.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.item.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      } else {
        method = 'POST'
        data = {
          "request":
          {
            "noKartu": this.item.noKartu,
            "kodeDokter": this.item.kodeDokter ? this.item.kodeDokter.kode : "",
            "poliKontrol": this.item.poliKontrol ? this.item.poliKontrol.kode : "",
            "tglRencanaKontrol": moment(this.item.tglRencanaKontrol).format('YYYY-MM-DD'),
            "user": "Xoxo"
          }
        }
      }
    }
    let json = {
      "url": url,
      "method": method,
      "data": data
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
        if (jenis == 'rujukanInsert' || jenis == 'rujukanUpdate') {
          let response2 = e.response.rujukan
          if (response2 != undefined) {
            var data: any = {
              tipe: method == 'rujukanInsert' ? 'save' : 'update',
              nosep: this.itemV2.noSep ? this.itemV2.noSep : null,
              tglrujukan: response2.tglRujukan,
              jenispelayanan: this.itemV2.jnsPelayanan,
              ppkdirujuk: response2.tujuanRujukan.nama,
              kdppkdirujuk: this.item.ppkDirujuk.kode,
              catatan: this.itemV2.catatan,
              diagnosarujukan: response2.diagnosa.nama,
              polirujukan: response2.poliTujuan.nama,
              tiperujukan: this.itemV2.tipeRujukan,
              nama: response2.peserta.nama,
              nokartu: response2.peserta.noKartu,
              tglsep: null,
              sex: response2.peserta.kelamin,
              norujukan: response2.noRujukan,
              nocm: response2.peserta.noMr,
              tglBerlakuKunjungan: response2.tglBerlakuKunjungan,
              tglRencanaKunjungan: response2.tglRencanaKunjungan,

            };
          } else {
            var data: any = {
              tipe: method == 'rujukanInsert' ? 'save' : 'update',
              nosep: this.itemV2.noSep ? this.itemV2.noSep : null,
              tglrujukan: moment(this.itemV2.tglRujukan).format('YYYY-MM-DD'),
              jenispelayanan: this.itemV2.jnsPelayanan,
              ppkdirujuk: this.itemV2.ppkDirujuk,
              kdppkdirujuk: this.itemV2.ppkDirujuk.kode,
              catatan: this.itemV2.catatan,
              diagnosarujukan: this.itemV2.diagRujukan.kode,
              polirujukan: this.itemV2.poliRujukan.nama,
              tiperujukan: this.itemV2.tipeRujukan,
              norujukan: this.itemV2.noRujukan,
              tglRencanaKunjungan: moment(this.itemV2.tglRencanaKunjungan).format('YYYY-MM-DD'),

            };
          }

          this.apiService.post("bridging/bpjs/save-rujukan", data).subscribe(z => { })
        }


      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this[jsonView] = JSON.stringify(e, undefined, 4);
    })
  }
  cari(url, jsonView) {
    if (url.indexOf('date') > -1) {
      let tgl = url.split('date~')
      url = ''
      for (let x = 0; x < tgl.length; x++) {
        if (new Date(tgl[x]) instanceof Date && !isNaN(new Date(tgl[x]).getTime())) {
          tgl[x] = moment(new Date(tgl[x])).format('YYYY-MM-DD');
        }
        url = url + tgl[x]
      }
    }
    let json = {
      "url": url,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this[jsonView] = JSON.stringify(e, undefined, 4);
    })
  }
  filterAutoCom(event, dataSource, url, balikan) {
    if (event.query == '') return
    if (event.query.length < 3) return
    let json = {
      "url": url,
      "method": "GET",
      "data": null
    }
    // this[dataSource] = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this[dataSource] = e.response[balikan]
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }

    })
  }

  clear() {
    this.itemV2 = {
      tipe: "1"
    }
  }
  cariRencana() {

    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD')
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD')

    let json = {
      "url": "RencanaKontrol/ListRencanaKontrol/tglAwal/" + tglAwal + "/tglAkhir/" + tglAkhir + "/filter/" + this.item.filter.kode,
      "method": "GET",
      "data": null
    }
    this.dataSourceRen = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {

      if (e.metaData.code == "200") {
        this.dataSourceRen = e.response.list
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }


    })
  }
  getGridData() {


  }
  hapusRuj(datas) {

    let json = {
      "url": "RencanaKontrol/Delete",
      "method": "DELETE",
      "data":
      {
        "request": {
          "t_suratkontrol": {
            "noSuratKontrol": datas.noSuratKontrol,
            "user": "Xoxo"
          }
        }
      }
    }
    this.apiService.postNonMessage('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code === "200") {
        // var data = {
        //   tipe: 'delete',
        //   norujukan: datas.norujukan,
        // };
        // this.apiService.post("bridging/bpjs/save-rujukan", data).subscribe(z => {
        // this.cariRencana()
        // })

        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this.cariRencana()
    });
  }

  edit(e) {
    this.dataSelected = e

    this.item.sep = this.dataSelected.noSepAsalKontrol
    this.item.noKartu = this.dataSelected.noKartu
    this.cariSep()
    this.item.tglRencanaKontrol = new Date(this.dataSelected.tglRencanaKontrol)
    if (this.dataSelected.jnsKontrol == '2') {
      this.cariSep()
      this.item.jenisPelayanan = this.dataSelected.jnsKontrol
    } else {
      this.cariNoka()
      this.item.jenisPelayanan = this.dataSelected.jnsKontrol
    }

    this.item.noSuratKontrol = this.dataSelected.noSuratKontrol
    this.item.poliKontrol = { kode: this.dataSelected.poliAsal, nama: this.dataSelected.namaPoliAsal }
    this.item.kodeDokter = { kode: this.dataSelected.kodeDokter, nama: this.dataSelected.namaDokter }
    this.myVar = true

  }
  editRujukan(datas) {
    this.dataSelected = datas
    this.itemV2.tipe = "2"
    this.itemV2.noSep = this.dataSelected.nosep
    this.itemV2.noRujukan = this.dataSelected.norujukan
    this.itemV2.tglRencanaKunjungan = new Date(this.dataSelected.tglrencanakunjungan)
    this.itemV2.tglRujukan = new Date(this.dataSelected.tglrujukan)
    this.itemV2.ppkDirujuk = this.dataSelected.ppkdirujuk
    // ppk = this.dataSelected.kdppkdirujuk

    // for (let index = 0; index < this.jenisPelayanan.length; index++) {
    //   const element = this.jenisPelayanan[index];
    //   if (element.idjenispelayanan == this.dataSelected.jenispelayanan) {
    //     this.itemV2.jnsPelayanan = element
    //     break
    //   }
    // }

    // this.itemV2.catatan = this.dataSelected.catatan
    // let diagnosarujukan = this.dataSelected.diagnosarujukan.split(" - ")
    // if (diagnosarujukan.length > 0) {
    //   this.listDiagnosa.add({ kddiagnosa: diagnosarujukan[0] })
    //   this.itemV2.diagRujukan = { kddiagnosa: diagnosarujukan[0] }
    // }
    // for (let index = 0; index < this.listTipeRujukan.length; index++) {
    //   const element = this.listTipeRujukan[index];
    //   if (element.value == this.dataSelected.tiperujukan) {
    //     this.itemV2.tipeRujukan = element
    //     break
    //   }
    // }
    this.itemV2.poliRujukan = this.dataSelected.polirujukan


  }
  tambah() {

    var nomor = 0
    if (this.dataSource2 == undefined) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }
    var kdDiagnosa = "";
    var namaDiagnosa = "";
    if (this.item.diagnosa == undefined) {
      this.alertService.error('Info', "Diagnosa Harus Di isi");
      return
    }

    kdDiagnosa = this.item.diagnosa.kode
    namaDiagnosa = this.item.diagnosa.nama
    let kode = this.item.diagnosaUtama === true ? 'P' : 'S';
    kode = kode + ';' + kdDiagnosa
    var dataDiagnosa = {
      no: nomor,
      kode: kode,
      kddiagnosa: kdDiagnosa,
      namadiagnosa: namaDiagnosa,
      utama: this.item.diagnosaUtama === true ? 'P' : 'S',
      jenis: this.item.diagnosaUtama === true ? "Primer" : "Sekunder",
    }
    this.data2.push(dataDiagnosa)
    this.dataSource2 = this.data2

  }
  removeData(dataItem) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataItem.no) {
        this.data2.splice(i, 1);
        for (var i = this.data2.length - 1; i >= 0; i--) {

          this.data2[i].no = i + 1
        }
        this.dataSource2 = this.data2
      }
    }
  }
  removeData2(dataItem) {
    for (var i = this.data3.length - 1; i >= 0; i--) {
      if (this.data3[i].no == dataItem.no) {
        this.data3.splice(i, 1);
        for (var i = this.data3.length - 1; i >= 0; i--) {

          this.data3[i].no = i + 1
        }
        this.dataSource3 = this.data3
      }
    }
  }
  tambah2() {

    var nomor = 0
    if (this.dataSource3 == undefined) {
      nomor = 1
    } else {
      nomor = this.data3.length + 1
    }
    var kdDiagnosa = "";
    var namaDiagnosa = ";"
    if (this.item.diagnosaTindakan == undefined) {

      this.alertService.error('Info', "Procedure Harus Di isi");
      return
    }

    kdDiagnosa = this.item.diagnosaTindakan.kode;
    namaDiagnosa = this.item.diagnosaTindakan.nama;

    let kode = kdDiagnosa

    var dataDiagnosaTin = {
      no: nomor,
      kode: kode,
      kddiagnosatindakan: kdDiagnosa,
      namadiagnosatindakan: namaDiagnosa,
      // utama: this.model.diagnosaUtama === true ? 1 : 0,
    }
    this.data3.push(dataDiagnosaTin)
    this.dataSource3 = this.data3


  }
  saveRujuk(jsonView) {

    let diagnosa = []
    for (let x = 0; x < this.data2.length; x++) {
      const element = this.data2[x];
      diagnosa.push({ kode: element.kode })
    }

    let diagnosa2 = []
    for (let x = 0; x < this.data3.length; x++) {
      const element = this.data3[x];
      diagnosa2.push({ kode: element.kode })
    }
    let json = {
      "url": "Rujukan/Khusus/insert",
      "method": "POST",
      "data": {
        "noRujukan": this.item.noRujukan,
        "diagnosa": diagnosa,
        "procedure": diagnosa2,
        "user": "Ramdanegie"
      }
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this[jsonView] = e
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }

    })
  }
  cetak(e) {
    var nosuratkontrol = e.noSuratKontrol
    var tglrencanakontrol = e.tglRencanaKontrol
    var txttglentrirencanakontrol = e.tglTerbitKontrol
    var noka = e.noKartu
    var diag = '-'
    let ket = ''
    let namadiag = ''

    let dpjpSEPasal = ''
    let dari = new Date(new Date().setDate(new Date().getDate() - 31))

    let json = { "url": "Peserta/nokartu/" + e.noKartu + "/tglSEP/" + moment(new Date()).format('YYYY-MM-DD'), "method": "GET", "data": null }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(res => {
      if (res.metaData.code == "200") {
        let json = {
          "url": "monitoring/HistoriPelayanan/NoKartu/" + e.noKartu + "/tglMulai/" + moment(dari).format('YYYY-MM-DD') + "/tglAkhir/" + moment(new Date()).format('YYYY-MM-DD'),
          "method": "GET",
          "data": null
        }
        this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(res2 => {
          if (res2.metaData.code == "200") {
            if (res2.response.histori.length > 0) {
              diag = res2.response.histori[0].diagnosa
              namadiag = res2.response.histori[0].diagnosa
              dpjpSEPasal = e.namaDokter;// res2.response.histori[0].namaDokter
              this.jspdfctk(nosuratkontrol, tglrencanakontrol, txttglentrirencanakontrol, noka, e.nama, res.response.peserta.tglLahir,
                this.namaPPk, e.namaPoliTujuan, res.response.peserta.sex, namadiag, ket,
                e.jnsKontrol, diag, tglrencanakontrol, e.namaDokter, dpjpSEPasal, e.jnsPelayanan);
            }

          }
        })

      } else {

      }
    })


  }

  jspdfctk(norujukan, tglrencanakontrol, tglterbitrencanakontrol, nokartu, nmpst, tgllahir, ppkperujuk, polirencanarujuk,
    jnskelamin, dxawal, catatan, jnspelayanan, kddx, tglrcnrujukan, nmdpjprencanarujuk, nmdpjpsepasal, jnskon) {

    jnspelayanan = 2;//Math.abs($('#cbpelayanan').val());

    var imgData = 'data:image/png;base64,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';
    var doc = new jsPDF('l', 'mm', [95, 210]);
    doc.addImage(imgData, 'PNG', 10, 6, 45, 10);

    doc.setProperties({
      title: 'Cetak Rencana Kontrol/Inap',
      subject: 'Rencana Kunjungan Kontrol/Inap'
    });

    doc.setFontSize(11);
    jnspelayanan == 2 ? doc.text('SURAT RENCANA KONTROL', 58, 10,) : doc.text('SURAT RENCANA INAP', 58, 10,);
    doc.text(ppkperujuk, 58, 15,);

    doc.setFontSize(12);
    doc.text('No.  ' + norujukan, 140, 10,);
    doc.setFontSize(10);
    var _tglberlakurjk = new Date(tglrencanakontrol);
    _tglberlakurjk.setDate(_tglberlakurjk.getDate());

    var _ddrujuk = _tglberlakurjk.getDate();
    var _mmrujuk = _tglberlakurjk.getMonth() + 1;
    var _mmmrujuk = this.strbulan((('' + _mmrujuk).length < 2 ? '0' : '') + _mmrujuk);
    var _yrujuk = _tglberlakurjk.getFullYear();
    var _tglrencanakontrol = [_ddrujuk, _mmmrujuk, _yrujuk].join(' ');
    //doc.text(140, 15, 'Rencana Kontrol ' + _tglrencanakontrol);


    //doc.text(140, 30, tipe == '0' ? '== Rujukan Penuh ==' : tipe == '1' ? '== Rujukan Partial ==' : '== Rujuk Balik (Non PRB) ==');
    //doc.text(140, 35, jnspelayanan == '1' ? 'Rawat Inap' : 'Rawat Jalan');


    doc.setFontSize(10);
    jnspelayanan == 2 ? doc.text('Kepada Yth', 10, 25) : doc.text('', 10, 25);

    doc.text('Mohon Pemeriksaan dan Penanganan Lebih Lanjut :', 10, 35);
    doc.text('No.Kartu', 10, 40);
    doc.text('Nama Peserta', 10, 45);
    doc.text('Tgl.Lahir', 10, 50);
    doc.text('Diagnosa', 10, 55);
    jnspelayanan == 2 ? doc.text('Rencana Kontrol', 10, 60) : doc.text('Rencana Inap', 10, 60);

    if (jnspelayanan == 2) {
      doc.text(nmdpjprencanarujuk, 40, 25);
      doc.text('Sp./Sub. ' + this.titleCase(polirencanarujuk), 40, 30);
    }
    doc.text(': ' + nokartu, 40, 40);
    doc.text(': ' + nmpst + ' (' + jnskelamin + ')', 40, 45);

    var _tgllahirs = new Date(tgllahir);
    _tgllahirs.setDate(_tgllahirs.getDate());

    var _ddlahir = _tgllahirs.getDate();
    var _mmlahir = _tgllahirs.getMonth() + 1;
    var _mmmlahir = this.strbulan((('' + _mmlahir).length < 2 ? '0' : '') + _mmlahir);
    var _ylahir = _tgllahirs.getFullYear();
    var _tgllahir = [_ddlahir, _mmmlahir, _ylahir].join(' ');

    var _tglentrirencanakontrol = new Date();
    var _dd2 = _tglentrirencanakontrol.getDate();
    var _mm2 = _tglentrirencanakontrol.getMonth() + 1;
    var _mmm2 = this.strbulan((('' + _mm2).length < 2 ? '0' : '') + _mm2);
    var _y2 = _tglentrirencanakontrol.getFullYear();
    var tglentrirencanakontrol = [_dd2, _mmm2, _y2].join(' ');


    doc.text(': ' + _tgllahir, 40, 50);
    //diagnosa
    var dx = dxawal;//this.dxHIV(kddx) == true ? kddx : dxawal;
    doc.text(': ' + dx, 40, 55);
    doc.text(': ' + _tglrencanakontrol, 40, 60);

    doc.text('Demikian atas bantuannya,diucapkan banyak terima kasih.', 10, 67);

    doc.setFontSize(8);

    //tanggal+time
    var d = new Date();
    var strDateTime = [[this.AddZero(d.getDate()),
    this.AddZero(d.getMonth() + 1),
    d.getFullYear()].join("-"),
    [this.AddZero(d.getHours()),
    this.AddZero(d.getMinutes())].join(":"),
    d.getHours() >= 12 ? "PM" : "AM"].join(" ");

    doc.setFontSize(6);
    doc.text('Tgl.Entri: ' + tglterbitrencanakontrol + ' | Tgl.Cetak: ' + strDateTime, 10, 87);

    //tanggal        
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var tgl = (('' + day).length < 2 ? '0' : '') + day + ' ' +
      this.strbulan((('' + month).length < 2 ? '0' : '') + month) + ' ' +
      d.getFullYear();

    doc.setFontSize(10);
    //doc.text(135, 70, tgl);
    doc.text('Mengetahui DPJP,', 150, 72);
    doc.text(jnspelayanan == 2 ? nmdpjpsepasal : nmdpjprencanarujuk, 150, 87);
    // doc.save("a4.pdf"); // will save the file in the current working directory

    var string = doc.output('datauristring');
    var iframe = "<iframe width='100%' height='100%' src='" + string + "'></iframe>"
    var x = window.open('', '_blank', 'width=1024,height=600,directories=0,status=0,titlebar=0,scrollbars=0,menubar=0,toolbar=0,location=0,resizable=1');
    x.focus();
    x.document.write(iframe);
    x.document.close();
  }
  dxHIV(kode) {
    var str = "B20,B20.0,B20.1,B20.2,B20.3,B20.4,B20.5,B20.6,B20.7,B20.8,B20.9,B21,B21.0,B21.1,B21.2,B21.3,B21.7,B21.8,B21.9,B22,B22.0,B22.1,B22.2,B22.7,B23,B23.0,B23.1,B23.2,B23.8,B24";
    var ret = str.includes(kode);
    return ret;
  }
  AddZero(num) {
    return (num >= 0 && num < 10) ? "0" + num : num + "";
  }
  titleCase(str) {
    str = str.toLowerCase().split(' ');
    for (var i = 0; i < str.length; i++) {
      str[i] = str[i].charAt(0).toUpperCase() + str[i].slice(1);
    }
    return str.join(' ');
  }

  strbulan(id) {
    var nama;
    switch (id) {
      case '01':
        nama = 'Januari';
        break
      case '02':
        nama = 'Februari';
        break
      case '03':
        nama = 'Maret';
        break
      case '04':
        nama = 'April';
        break
      case '05':
        nama = 'Mei';
        break
      case '06':
        nama = 'Juni';
        break
      case '07':
        nama = 'Juli';
        break
      case '08':
        nama = 'Agustus';
        break
      case '09':
        nama = 'September';
        break
      case '10':
        nama = 'Oktober';
        break
      case '11':
        nama = 'Nopember';
        break
      case '12':
        nama = 'Desember';
        break
    }
    return nama;
  }
}
