import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-v-lpk',
  templateUrl: './v-lpk.component.html',
  styleUrls: ['./v-lpk.component.scss']
})
export class VLpkComponent implements OnInit {
  jsonResult2: any
  jsonResult: any
  jsonResult1: any
  jsonResult3: any
  jsonResult4: any
  jsonResult5: any
  jsonResult6: any
  jsonResult7: any
  jsonResult8: any
  jsonResult9: any
  jsonResult10: any
  listProcedur = []
  listKelas = []
  listDJP = []
  listSpesialis = []
  listRuangn = []
  listCaraKeluar = []
  listPasca = []
  data2: any = []
  data3: any = []
  dataSource2: any = []
  listDiagnosa2:any=[]
  listDiagnosa:any=[]
  listPoli:any=[]
  listFaskes:any=[]
  dataSource3: any = []
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD'),
    tipe: "1"
  }
  t_lpk: any = {
    jaminan: "1",
    poli: {},
    perawatan: {},
    diagnosa: [],
    procedure: [],
    rencanaTL: {
      dirujukKe: {},
      kontrolKembali: {}
    }
  }
  listDJ2P: any[]
  column2:any = []
  column3:any = []
  listRencana: any[] = [
    { kode: 1, nama: 'Diperbolehkan Pulang' },
    { kode: 2, nama: 'Pemeriksaan Penunjang' },
    { kode: 3, nama: 'Dirujuk Ke' },
    { kode: 4, nama: 'Kontrol Kembali' }
  ];
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.loadCombo()
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
  filter2(event) {
    if (event.query == '') return
    // this.listDJP = []
    let json = {
      "url": "referensi/dokter/" + event.query,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      this.listDJ2P = e.response.list
    })
  }
  loadCombo() {


    let json2 = {
      "url": "referensi/kelasrawat",
      "method": "GET",
      "data": null
    }
    this.listKelas = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json2).subscribe(e => {
      this.listKelas = e.response.list
    })
    let json3 = {
      "url": "referensi/pascapulang",
      "method": "GET",
      "data": null
    }
    this.listPasca = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json3).subscribe(e => {
      this.listPasca = e.response.list
    })

    let json4 = {
      "url": "referensi/carakeluar",
      "method": "GET",
      "data": null
    }
    this.listCaraKeluar = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json4).subscribe(e => {
      this.listCaraKeluar = e.response.list
    })



    let json5 = {
      "url": "referensi/ruangrawat",
      "method": "GET",
      "data": null
    }
    this.listRuangn = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json5).subscribe(e => {
      this.listRuangn = e.response.list
    })

    let json6 = {
      "url": "referensi/spesialistik",
      "method": "GET",
      "data": null
    }
    this.listSpesialis = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json6).subscribe(e => {
      this.listSpesialis = e.response.list
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
  post(url, method, jsonView) {
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
    if (method == 'lpkDelete') {
      method = 'DELETE'
      data = {
        "request": {
          "t_sep": {
            "noSep": this.item.sep,
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

  saveLPK(jsonView) {

    let diagnosa = []
    for (let x = 0; x < this.data2.length; x++) {
      const element = this.data2[x];
      element.level = ""
      if (element.utama == 'P') {
        element.level = "1"
      } else {
        element.level = "2"
      }
      diagnosa.push({ kode: element.kode, level: element.level })
    }

    let diagnosa2 = []
    for (let x = 0; x < this.data3.length; x++) {
      const element = this.data3[x];
      diagnosa2.push({ kode: element.kode })
    }
    let jsons: any = {}
    let dataSave = {
      "request": {
        "t_lpk": {
          "noSep": this.t_lpk.noSep,
          "tglMasuk": moment(this.t_lpk.tglMasuk).format('YYYY-MM-DD'),
          "tglKeluar": moment(this.t_lpk.tglKeluar).format('YYYY-MM-DD'),
          "jaminan": this.t_lpk.jaminan,
          "poli": {
            "poli": this.t_lpk.poli ? this.t_lpk.poli.kode : "",
          },
          "perawatan": {
            "ruangRawat": this.t_lpk.ruangRawat ? this.t_lpk.ruangRawat.kode : "",
            "kelasRawat": this.t_lpk.kelasRawat ? this.t_lpk.kelasRawat.kode : "",
            "spesialistik": this.t_lpk.spesialistik ? this.t_lpk.spesialistik.kode : "",
            "caraKeluar": this.t_lpk.caraKeluar ? this.t_lpk.caraKeluar.kode : "",
            "kondisiPulang": this.t_lpk.kondisiPulang ? this.t_lpk.kondisiPulang.kode : "",
          },
          "diagnosa": diagnosa,
          "procedure": diagnosa2,
          "rencanaTL": {
            "tindakLanjut": this.t_lpk.tindakLanjut ? this.t_lpk.tindakLanjut.kode : "",
            "dirujukKe": {
              "kodePPK": this.t_lpk.dirujukKe ? this.t_lpk.dirujukKe.kode : "",
            },
            "kontrolKembali": {
              "tglKontrol": moment(this.t_lpk.tglKontrol).format('YYYY-MM-DD'),
              "poli": this.t_lpk.poli ? this.t_lpk.poli.kode : "",
            }
          },
          "DPJP": this.t_lpk.DPJP ? this.t_lpk.DPJP.kode : "",
          "user": "Xoxo"
        }
      }
    }
    if (this.item.tipe == "1") {
      jsons = {
        "url": "LPK/insert",
        "method": "POST",
        "data": dataSave
      }
    } else {
      jsons = {
        "url": "LPK/update",
        "method": "POST",
        "data": dataSave

      }
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", jsons).subscribe(e => {
      if (e.metaData.code == "200") {
        this[jsonView] = e
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }

    })
  }
  ClearForm(){
    
  }
}