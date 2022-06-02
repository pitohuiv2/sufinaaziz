import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-v-rujukan',
  templateUrl: './v-rujukan.component.html',
  styleUrls: ['./v-rujukan.component.scss']
})
export class VRujukanComponent implements OnInit {
  itemV2: any = {
    tipe: "1"
  }
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD')
  }
  daftar: any = {
    periodeAwal: new Date(),
    periodeAkhir: new Date(),
  }
  activeState: boolean = false
  dataSource2: any
  data2: any = []
  data3: any = []
  listDiagnosa: any = []
  listPoli: any = []
  listFaskes: any = []
  jsonResult: any
  dataSource: any
  column: any[];
  column2: any[];
  dataSelected: any
  column3: any = []
  dataSource3: any
  jsonResult2: any
  jsonResult3: any
  jsonResult4: any
  jsonResult5: any
  jsonResult6: any
  jsonResult7: any
  jsonResult8: any
  jsonResult9: any
  jsonResult10: any
  listDiagnosa2: any
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
    this.item.tipe = '1'
    this.column = [
      { field: 'tglrujukan', header: 'Tgl Rujukan', width: "100px" },
      { field: 'norujukan', header: 'No Rujukan', width: "150px", filter: true },
      { field: 'nocm', header: 'No RM', width: "70px", filter: true },
      { field: 'nama', header: 'Nama Pasien', width: "150px", filter: true },
      { field: 'nosep', header: 'No SEP', width: "150px" },
      { field: 'nokartu', header: 'No Kartu', width: "90px" },
      { field: 'jenispelayanannama', header: 'Pelayanan', width: "80px" },
      { field: 'tiperujukannama', header: 'Tipe', width: "80px" },
      { field: 'namaruangan', header: 'Poli', width: "150px" },
      { field: 'ppkdirujuk', header: 'Dirujuk Ke', width: "150px" },
      { field: 'diagnosarujukan', header: 'Diagnosa', width: "200px" },
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
  ClearForm() {

  }
  loadCombo() {
    this.item.kodePPK = ''
    this.apiService.get('registrasi/get-setting-asuransi').subscribe(dat => {

      this.item.kodePPK = dat.kodePPKRujukan
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
    let json = {
      "url": url,
      "method": method,
      "data": data
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      // var e:any =                                              
      // {
      //    "metaData": {
      //       "code": "200",
      //       "message": "OK"
      //    },
      //    "response": {
      //       "rujukan": {
      //          "AsalRujukan": {
      //             "kode": "0301R001",
      //             "nama": "RSUP DR M JAMIL PADANG"
      //          },
      //          "diagnosa": {
      //             "kode": "A00.1",
      //             "nama": "A00.1 - Cholera due to Vibrio cholerae 01, biovar eltor"
      //          },
      //          "noRujukan": "0301R0011117B001126",
      //          "peserta": {
      //             "asuransi": "-",
      //             "hakKelas": null,
      //             "jnsPeserta": "PNS PUSAT",
      //             "kelamin": "Laki-Laki",
      //             "nama": "ZIYADUL",
      //             "noKartu": "0000000110156",
      //             "noMr": "123456",
      //             "tglLahir": "2008-02-05"
      //          },
      //          "poliTujuan": {
      //             "kode": "INT",
      //             "nama": "Poli Penyakit Dalam"
      //          },
      //          "tglRujukan": "2017-11-08",
      //          "tujuanRujukan": {
      //             "kode": "0301R002",
      //             "nama": "RS JIWA ULU GADUT"
      //          }
      //       }
      //    }
      // }
      if (e.metaData.code == "200") {
        var dataz: any = {}
        this.alertService.success('Info', e.metaData.message);
        if (jenis == 'rujukanInsert' || jenis == 'rujukanUpdate') {
          let response2 = e.response.rujukan
          if (response2 != undefined) {
            dataz = {
              tipe: jenis == 'rujukanInsert' ? 'save' : 'update',
              nosep: this.itemV2.noSep ? this.itemV2.noSep : null,
              tglrujukan: response2.tglRujukan,
              jenispelayanan: this.itemV2.jnsPelayanan,
              ppkdirujuk: response2.tujuanRujukan.nama,
              kdppkdirujuk: this.itemV2.ppkDirujuk.kode,
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
            dataz = {
              tipe: jenis == 'rujukanInsert' ? 'save' : 'update',
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

          this.apiService.post("bridging/bpjs/save-rujukan", dataz).subscribe(z => { })
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
  getGridData() {
    var tglAwal = moment(this.daftar.periodeAwal).format('YYYY-MM-DD');
    var tglAkhir = moment(this.daftar.periodeAkhir).format('YYYY-MM-DD');

    var noRujukan = ""
    if (this.daftar.noRujukan != undefined) {
      noRujukan = "&norujukan=" + this.daftar.noRujukan
    }
    var rm = ""
    if (this.daftar.nocm != undefined) {
      rm = "&nocm=" + this.daftar.nocm
    }

    this.apiService.get("bridging/bpjs/get-daftar-rujukan?" +
      "tglAwal=" + tglAwal +
      "&tglAkhir=" + tglAkhir +
      noRujukan + rm
    )
      .subscribe(data => {
        var result = data.data
        for (var i = 0; i < result.length; i++) {
          if (result[i].jenispelayanan == 1 && result[i].jenispelayanan != null)
            result[i].jenispelayanannama = 'Rawat Inap'
          else
            result[i].jenispelayanannama = 'Rawat Jalan'

          if (result[i].tiperujukan == "0")
            result[i].tiperujukannama = 'Penuh'
          else if (result[i].tiperujukan == "1")
            result[i].tiperujukannama = 'Partial'
          else if (result[i].tiperujukan == "2")
            result[i].tiperujukannama = 'Rujuk Balik'
        }
        this.dataSource = result

      });

  }
  hapusRuj(datas) {

    let json = {
      "url": "Rujukan/delete",
      "method": "DELETE",
      "data":
      {
        "request": {
          "t_rujukan": {
            "noRujukan": datas.norujukan,
            "user": "Xoxo"
          }
        }
      }
    }
    this.apiService.postNonMessage('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code === "200") {
        var data = {
          tipe: 'delete',
          norujukan: datas.norujukan,
        };
        this.apiService.post("bridging/bpjs/save-rujukan", data).subscribe(z => {
          this.getGridData()
        })

        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    });
  }
  toggle(index: number) {
    this.activeState[index] = !this.activeState[index];
  }
  editRujukan(datas) {
    this.activeState = true
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
}
