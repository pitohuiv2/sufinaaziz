import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { HelperService } from 'src/app/service/helperService';
@Component({
  selector: 'app-rujukan-bpjs',
  templateUrl: './rujukan-bpjs.component.html',
  styleUrls: ['./rujukan-bpjs.component.scss']
})
export class RujukanBpjsComponent implements OnInit {
  daftar: any = {
    periodeAwal: new Date(),
    periodeAkhir: new Date(),
    tglSep: new Date(),
    tipe: "2"
  }
  resCetak: any = {}
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD')
  }
  itemV2: any = {
    tipeRujukan: "0",
    jnsPelayanan: "1",
    tglRujukan: new Date(),
    tglRencanaKunjungan: new Date()
  }
  listFaskes: any = []
  activeState: boolean = false
  dataSelected: any
  dataSource: any
  dataSource2: any
  column: any[];
  listDiagnosa: any
  column2: any[]
  index: number
  showInput: boolean = false
  listPel = [
    { kode: 1, nama: 'Rawat Inap' },
    { kode: 2, nama: 'Rawat Jalan' }
  ]
  rujukan: any = {}
  sep: any = {
    peserta: {
      hakKelas: {},
      provUmum: { kdProvider: '', nmProvider: '' }
    }
  }
  jadwal: any = {
    tglRencanaKunjungan: new Date()
  }
  popJadwal: boolean = false
  dataSourceSarana: any[]
  columnSarana: any = []
  dataSourceSub: any = []
  columnSub: any = []
  disabledCetak: boolean = true
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
    private helper: HelperService
  ) { }

  ngOnInit(): void {
    this.resCetak.namaPPKRujukan = ''
    this.apiService.get('registrasi/get-setting-asuransi').subscribe(dat => {

      this.resCetak.namaPPKRujukan = dat.namaPPKRujukan
    })

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
      // { field: 'noSep', header: 'Tgl Rujukan', width: "150px" },
      { field: 'tglSep', header: 'Tgl SEP', width: "100px" },
      { field: 'tglPlgSep', header: 'Tgl Pulang', width: "100px" },
      { field: 'jnsPelayanan', header: 'Pelayanan', width: "100px" },
      { field: 'kelasRawat', header: 'Kelas', width: "100px" },
      { field: 'noKartu', header: 'No Kartu', width: "100px" },
      { field: 'nama', header: 'Nama', width: "200px" },
      { field: 'poli', header: 'Poli', width: "200px" },
      { field: 'diagnosa', header: 'Diagnosa', width: "200px" },
      { field: 'noRujukan', header: 'No Rujukan', width: "100px" },
    ];
    this.columnSub = [
      { field: 'namaSpesialis', header: 'Nama Spesialis/Sub', width: "200px" },
      { field: 'kapasitas', header: 'Kapasitas', width: "100px" },
      { field: 'jumlahRujukan', header: 'Jml Rujukan', width: "100px" },
      { field: 'persentase', header: 'Presentase', width: "100px" },

    ]
    this.columnSarana = [
      { field: 'namaSarana', header: 'Sarana', width: "200px" },
      { field: 'kodeSarana', header: 'Kode', width: "100px" },
    ]
    this.getGridData()
  }

  handleChangeTab(e) {
    this.index = e.index
    if (e.index == 0) {
      this.getGridData()
    } else if (e.index == 1) {
      this.cariHistory()
    } else {

    }
  }
  cekTipe(e) {

    if (e == 2) {
      this.itemV2.ppkDirujuk = this.sep.peserta.provUmum.kdProvider + "-" + this.sep.peserta.provUmum.nmProvider
    } else {
      this.itemV2.ppkDirujuk = ''
    }
  }
  cancel() {
    this.showInput = false
  }
  setSEP(e) {
    this.itemV2.noSep = e.noSep
    var json = {
      "url": "SEP/" + e.noSep,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      this.showInput = true
      if (e.metaData.code == 200) {
        for (let x = 0; x < this.listPel.length; x++) {
          const element = this.listPel[x];
          if (element.nama == e.response.jnsPelayanan) {
            this.itemV2.jnsPelayanan = element.kode
            break
          }
        }
        e.response.peserta.provUmum = {}
        this.sep = e.response

        // this.itemV2.tglRujukan = new Date(e.response.tglSep)

        var json = {
          "url": "Peserta/nokartu/" + e.response.peserta.noKartu + "/tglSEP/" + moment(new Date()).format('YYYY-MM-DD'),
          "method": "GET",
          "data": null
        }
        this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(z => {
          if (z.metaData.code == 200) {
            this.sep.peserta = z.response.peserta
            this.rujukan.rujukFaskes = z.response.peserta.provUmum.kdProvider + '~' + z.response.peserta.provUmum.nmProvider
          }
        })
      }
      else this.alertService.info('Info', e.metaData.message)
    })
  }
  cariHistory() {
    let json = {
      "url": "Monitoring/Kunjungan/Tanggal/" + moment(this.daftar.tglSep).format('YYYY-MM-DD') + "/JnsPelayanan/" + this.daftar.tipe,
      "method": "GET",
      "data": null
    }
    this.dataSource2 = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this.dataSource2 = e.response.sep
    })
  }
  editRujukan(datas) {
    this.index = 1
    this.showInput = true

    this.dataSelected = datas
    this.itemV2.tipe = this.dataSelected.tiperujukan
    this.setSEP({ noSep: this.dataSelected.nosep })
    this.itemV2.noSep = this.dataSelected.nosep
    this.itemV2.noRujukan = this.dataSelected.norujukan
    this.itemV2.tglRencanaKunjungan = new Date(this.dataSelected.tglrencanakunjungan)
    this.itemV2.tglRujukan = new Date(this.dataSelected.tglrujukan)
    this.itemV2.ppkDirujuk = this.dataSelected.kdppkdirujuk + "~" + this.dataSelected.ppkdirujuk
    this.itemV2.poliRujukan = this.dataSelected.polirujukan
    this.itemV2.catatan = this.dataSelected.catatan
    this.itemV2.diagRujukan = { kode: this.dataSelected.diagnosarujukan.split(" - ")[0], nama: this.dataSelected.diagnosarujukan }

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
  filterAutoComDiag(event) {
    if (event.query == '') return
    if (event.query.length < 3) return
    let json = {
      "url": 'referensi/diagnosa/' + encodeURIComponent(event.query),
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        // for (let x = 0; x < e.response.diagnosa.length; x++) {
        //   const element = e.response.diagnosa[x];
        //   element.nama = element.kode + "~" + element.nama
        // }
        this.listDiagnosa = e.response.diagnosa
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  filterAutoCom(event) {
    if (event.query == '') return
    if (event.query.length < 3) return
    let json = {
      "url": "referensi/faskes/" + encodeURIComponent(event.query) + "/2",
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.listFaskes = e.response.faskes
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  cariFaskes() {
    this.popJadwal = true
    delete this.itemV2.ppkDirujuk
    delete this.itemV2.poliRujukan
  }
  pakeSub(e) {
    this.itemV2.ppkDirujuk = this.jadwal.ppkDirujuk.kode + "~" + this.jadwal.ppkDirujuk.nama
    this.itemV2.poliRujukan = e.kodeSpesialis + "~" + e.namaSpesialis

    this.popJadwal = false
  }
  cariJadwal() {
    if (!this.jadwal.ppkDirujuk) {
      this.alertService.warn('Info', 'Pilih PPK Dirujuk');
      return
    }
    this.dataSourceSarana = []
    var json = { "url": "Rujukan/ListSarana/PPKRujukan/" + this.jadwal.ppkDirujuk.kode, "method": "GET", "data": null }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.dataSourceSarana = e.response.list
      } else {
        this.alertService.error('Sarana', e.metaData.message);
      }
    })

    this.dataSourceSub = []
    var json = { "url": "Rujukan/ListSpesialistik/PPKRujukan/" + this.jadwal.ppkDirujuk.kode + "/TglRujukan/" + moment(this.itemV2.tglRencanaKunjungan).format('YYYY-MM-DD'), "method": "GET", "data": null }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.dataSourceSub = e.response.list
      } else {
        this.alertService.error('Spesialis/Sub', e.metaData.message);
      }
    })
  }
  cetak(e) {
    this.helper.cetakRujukan(e.norujukan,
      e.tglrujukan, e.nokartu,
      e.nama, e.tgllahir,
      e.ppkdirujuk, this.resCetak.namaPPKRujukan,
      e.namaruangan, e.sex,
      e.diagnosarujukan, e.catatan,
      e.tiperujukan, e.jenispelayanan,
      e.diagnosarujukan,
      e.tglrencanakunjungan
    )
  }
  cetakAfter() {
    this.helper.cetakRujukan(this.resCetak.norujukan,
      this.resCetak.tglrujukan, this.resCetak.nokartu,
      this.resCetak.nama, this.resCetak.tgllahir,
      this.resCetak.ppkdirujuk, this.resCetak.namaPPKRujukan,
      this.resCetak.namaruangan, this.resCetak.sex,
      this.resCetak.diagnosarujukan, this.resCetak.catatan,
      this.resCetak.tiperujukan, this.resCetak.jenispelayanan,
      this.resCetak.diagnosarujukan,
      this.resCetak.tglrencanakunjungan
    )
  }
  save() {
    var data = {};
    var url = ''
    var method = ''
    if (this.itemV2.noRujukan == undefined) {
      url = 'Rujukan/2.0/insert'
      method = 'POST'
      data = {
        "request": {
          "t_rujukan": {
            "noSep": this.itemV2.noSep,
            "tglRujukan": moment(this.itemV2.tglRujukan).format("YYYY-MM-DD"),
            "tglRencanaKunjungan": moment(this.itemV2.tglRencanaKunjungan).format("YYYY-MM-DD"),
            "ppkDirujuk": this.itemV2.ppkDirujuk ? this.itemV2.ppkDirujuk.split('~')[0] : "",
            "jnsPelayanan": this.itemV2.jnsPelayanan,
            "catatan": this.itemV2.catatan ? this.itemV2.catatan : "",
            "diagRujukan": this.itemV2.diagRujukan.kode,
            "tipeRujukan": this.itemV2.tipeRujukan,
            "poliRujukan": this.itemV2.poliRujukan ? this.itemV2.poliRujukan.split('~')[0] : "",
            "user": "XoxoXoxo"
          }
        }
      }
    } else {
      url = 'Rujukan/2.0/Update'
      method = 'PUT'
      data = {
        "request": {
          "t_rujukan": {
            "noRujukan": this.itemV2.noRujukan,
            "tglRujukan": moment(this.itemV2.tglRujukan).format("YYYY-MM-DD"),
            "tglRencanaKunjungan": moment(this.itemV2.tglRencanaKunjungan).format("YYYY-MM-DD"),
            "ppkDirujuk": this.itemV2.ppkDirujuk ? this.itemV2.ppkDirujuk.split('~')[0] : "",
            "jnsPelayanan": this.itemV2.jnsPelayanan,
            "catatan": this.itemV2.catatan ? this.itemV2.catatan : "",
            "diagRujukan": this.itemV2.diagRujukan.kode,
            "tipeRujukan": this.itemV2.tipeRujukan,
            "poliRujukan": this.itemV2.poliRujukan ? this.itemV2.poliRujukan.split('~')[0] : "",
            "user": "XoxoXoxo"
          }
        }
      }
    }
    let json = {
      "url": url,
      "method": method,
      "data": data
    }
    this.disabledCetak = true
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.disabledCetak = false
        var dataz: any = {}
        this.alertService.success('Info', e.metaData.message);

        let response2 = e.response.rujukan
        if (response2 != undefined) {

          this.resCetak.norujukan = response2.noRujukan;
          this.resCetak.tglrujukan = response2.tglRujukan
          this.resCetak.nokartu = response2.peserta.noKartu
          this.resCetak.nama = response2.peserta.nama
          this.resCetak.tgllahir = response2.peserta.tglLahir
          this.resCetak.ppkdirujuk = this.itemV2.ppkDirujuk ? this.itemV2.ppkDirujuk.split('~')[1] : ""
          this.resCetak.namaruangan = response2.poliTujuan.nama
          this.resCetak.sex = response2.peserta.kelamin
          this.resCetak.diagnosarujukan = response2.diagnosa.nama
          this.resCetak.catatan = this.itemV2.catatan
          this.resCetak.tiperujukan = this.itemV2.tipeRujukan
          this.resCetak.jenispelayanan = this.itemV2.jnsPelayanan
          this.resCetak.tglrencanakunjungan = response2.tglRencanaKunjungan

          dataz = {
            tipe: this.itemV2.noRujukan ? 'update' : 'save',
            nosep: this.itemV2.noSep ? this.itemV2.noSep : null,
            tglrujukan: response2.tglRujukan,
            jenispelayanan: this.itemV2.jnsPelayanan,
            ppkdirujuk: response2.tujuanRujukan.nama,
            kdppkdirujuk: this.itemV2.ppkDirujuk ? this.itemV2.ppkDirujuk.split('~')[0] : "",
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
            tipe: this.itemV2.noRujukan ? 'update' : 'save',
            nosep: this.itemV2.noSep ? this.itemV2.noSep : null,
            tglrujukan: moment(this.itemV2.tglRujukan).format('YYYY-MM-DD'),
            jenispelayanan: this.itemV2.jnsPelayanan,
            ppkdirujuk: this.itemV2.ppkDirujuk ? this.itemV2.ppkDirujuk.split('~')[1] : "",
            kdppkdirujuk: this.itemV2.ppkDirujuk ? this.itemV2.ppkDirujuk.split('~')[0] : "",
            catatan: this.itemV2.catatan,
            diagnosarujukan: this.itemV2.diagRujukan.kode,
            polirujukan: this.itemV2.poliRujukan ? this.itemV2.poliRujukan.split('~')[0] : "",
            tiperujukan: this.itemV2.tipeRujukan,
            norujukan: this.itemV2.noRujukan,
            tglRencanaKunjungan: moment(this.itemV2.tglRencanaKunjungan).format('YYYY-MM-DD'),

          };
          this.index = 0
        }

        this.apiService.post("bridging/bpjs/save-rujukan", dataz).subscribe(z => { })
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
}
