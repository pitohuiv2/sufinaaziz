import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-v-prb',
  templateUrl: './v-prb.component.html',
  styleUrls: ['./v-prb.component.scss']
})
export class VPrbComponent implements OnInit {
  search: any = {}
  item: any = {}
  listDPJP: any
  column: any[];
  dataTable: any[];
  showNomor: boolean = true
  isInsert: boolean = true
  listProgramPRB: any
  dataobatgen: any[] = []
  listObat: any
  obat: any = {}
  delete: any = {}
  listTipe: any[] = [{ name: 'Nomor SRB', id: '1' }, { name: 'Tanggal SRB', id: '2' }];
  listTipe2: any[] = [{ name: 'Insert', id: '1' }, { name: 'Update', id: '2' }];
  popUp: boolean = false
  jsonResult2:any
  jsonResult:any
  jsonResult3:any
  jsonResult4:any
  jsonResult5:any
  jsonResult6:any
  jsonResult7:any
  jsonResult8:any
  jsonResult9:any
  jsonResult10:any
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,

  ) {
    this.column = [
      { field: 'kdObat', header: 'Kode Obat', width: "150px" },
      { field: 'signa1', header: 'Signa 1', width: "150px" },
      { field: 'signa2', header: 'Signa 2', width: "150px" },
      { field: 'jmlObat', header: 'Jumlah Obat', width: "150px" },

    ];
  }

  ngOnInit(): void {
    this.search.tipe = this.listTipe[0]
    this.item.tipe = this.listTipe2[0].id
    let now = moment(new Date()).format('YYYY-MM-DD')
    let json = {
      "url": "referensi/dokter/pelayanan/1/tglPelayanan/" + now + "/Spesialis/IGD",
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.listDPJP = e.response.list;
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })



    let json2 = {
      "url": "/referensi/diagnosaprb",
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json2).subscribe(e => {
      if (e.metaData.code == "200") {
        this.listProgramPRB = e.response.list;
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  klikRadio(e) {
    this.showNomor = !this.showNomor
  }
  klikRadio2(e) {
    this.isInsert = !this.isInsert
  }
  cari(jsonView) {
    var data;
    if (this.search.tipe.id == 1) {
      if (this.search.nosrb == undefined) {
        this.alertService.error("info", 'Nomor Surat Rujukan Balik harap diisi !');
        return
      }
      const nosrb = this.search.nosrb
      const nosep = this.search.nosep
      data = {
        "url": `prb/${nosrb}/nosep/${nosep}`,
        "method": "GET",
        "data": null
      }
    } else {
      if (this.search.dari == undefined) {
        this.alertService.error("info", 'Tanggal mulai harap diisi !');
        return
      }
      if (this.search.sampai == undefined) {
        this.alertService.error("info", 'Tanggal Akhir harap diisi !');
        return
      }
      const tglawal = moment(this.search.dari).format('YYYY-MM-DD')
      const tglakhir = moment(this.search.sampai).format('YYYY-MM-DD')
      data = {
        "url": `prb/tglMulai/${tglawal}/tglAkhir/${tglakhir}`,
        "method": "GET",
        "data": null
      }
    }

    this.apiService.postNonMessage("bridging/bpjs/tools", data).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this[jsonView] = JSON.stringify(e, undefined, 4);
    })

  }
  hapus(e) {
    for (var i = this.dataobatgen.length - 1; i >= 0; i--) {
      if (this.dataobatgen[i].kdObat == e.kdObat) {
        this.dataobatgen.splice(i, 1);
        this.dataTable = this.dataobatgen
      }
    }
  }
  addObat() {
    this.obat = {};
    this.listObat = null;
    this.popUp = true
  }
  tutupObat() {
    this.obat = {};
    this.listObat = null;
    this.popUp = false
  }
  filter(event) {
    if (event.query == '') return
    // this.listObat = []
    let json = {
      "url": "referensi/obatprb/" + event.query,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      this.listObat = e.response.list
    })
  }
  addGrid() {
    if (this.obat.obat == undefined) {
      this.alertService.error("info", "Obat Harus Di isi")
      return
    }
    if (this.obat.signa1 == undefined) {
      this.alertService.error("info", "Signa 1 Harus Di isi")
      return
    }
    if (this.obat.signa2 == undefined) {
      this.alertService.error("info", "Signa 2 Harus Di isi")
      return
    }
    if (this.obat.jmlobat == undefined) {
      this.alertService.error("info", "Jumlah Obat Harus Di isi")
      return
    }

    var dataObat = {};
    dataObat = {
      kdObat: this.obat.obat.kode,
      signa1: this.obat.signa1,
      signa2: this.obat.signa2,
      jmlObat: this.obat.jmlobat
    }
    this.dataobatgen.push(dataObat)
    this.dataTable = this.dataobatgen

    this.obat = {};
    this.listObat = null;
  }
  Save(jsonView) {
    if (!this.item.tipe) {
      this.alertService.error("info", 'Pilih Tipe Post');
      return;
    } else {
      if (this.item.tipe === 1) {

        this.SaveData(jsonView);
      } else {
        this.SaveData(jsonView);
      }
    }
  }

  SaveData(jsonView) {
    var url = "";
    var method = "";
    var dataSend = {};
    if (this.item.noSep == undefined) {
      this.alertService.error("info", "No SEP Harus Di isi")
      return
    }

    if (this.item.alamat == undefined) {
      this.alertService.error("info", "Alamat Harus Di isi")
      return
    }
    if (this.item.email == undefined) {
      this.alertService.error("info", "Email Harus Di isi")
      return
    }
    if (this.item.kodeDPJP == undefined) {
      this.alertService.error("info", "DPJP Harus Di isi")
      return
    }
    if (this.item.keterangan == undefined) {
      this.alertService.error("info", "Keterangan Harus Di isi")
      return
    }
    if (this.item.saran == undefined) {
      this.alertService.error("info", "Saran Harus Di isi")
      return
    }

    if (this.dataTable == undefined) {
      this.alertService.error("info", "Obat Harus Di isi")
      return
    }

    if (this.item.tipe == 1) {
      if (this.item.noKartu == undefined) {
        this.alertService.error("info", "No Kartu Harus Di isi")
        return
      }
      if (this.item.programprb == undefined) {
        this.alertService.error("info", "Program PRB Harus Di isi")
        return
      }

      url = "PRB/insert";
      method = "POST";
      dataSend = {
        "noSep": this.item.noSep,
        "noKartu": this.item.noKartu,
        "alamat": this.item.alamat,
        "email": this.item.email,
        "programPRB": this.item.programprb.kode,
        "kodeDPJP": this.item.kodeDPJP.kode,
        "keterangan": this.item.keterangan,
        "saran": this.item.saran,
        "user": "Xoxo",
        "obat": this.dataobatgen
      }
    } else {


      if (this.item.noSrb == undefined) {
        this.alertService.error("info", "No SRB Harus Di isi")
        return
      }
      url = "PRB/Update";
      method = "PUT";
      dataSend = {
        "noSrb": this.item.noSrb,
        "noSep": this.item.noSep,
        "alamat": this.item.alamat,
        "email": this.item.email,
        "kodeDPJP": this.item.kodeDPJP.kode,
        "keterangan": this.item.keterangan,
        "saran": this.item.saran,
        "user": "Xoxo",
        "obat": this.dataobatgen
      }
    }

    var data = {
      "url": url,
      "method": method,
      "data": {
        "request": {
          "t_prb": dataSend
        }
      }
    }

    this.apiService.postNonMessage("bridging/bpjs/tools", data).subscribe(e => {
      this[jsonView] = JSON.stringify(e, undefined, 4);

      if (e.metaData.code == "200") {
        this.ClearForm();
      }

      this.item.tipe = 1;
    })

  }

  ClearForm() {
    this.item = {};
    this.item.tipe = 1;
    this.dataobatgen = []
    this.dataTable = this.dataobatgen
  }

  deleteDataPRB(jsonView) {
    if (this.delete.nosrb == undefined) {
      this.alertService.error("info", "No SRB Harus Di isi")
      return
    }
    if (this.delete.nosep == undefined) {
      this.alertService.error("info", "No SEP Harus Di isi")
      return
    }
    var data = {
      "url": "PRB/Delete",
      "method": "DELETE",
      "data": {
        "request": {
          "t_prb": {
            "noSrb": this.delete.nosrb,
            "noSep": this.delete.nosep,
            "user": "Xoxo"
          }
        }
      }
    }

    this.apiService.postNonMessage("bridging/bpjs/tools", data).subscribe(e => {
      this[jsonView] = JSON.stringify(e, undefined, 4);

    })
  }

  clearDelete = function () {
    this.delete = {}
  }
}
