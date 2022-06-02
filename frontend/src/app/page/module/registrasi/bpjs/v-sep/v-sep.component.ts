import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-v-sep',
  templateUrl: './v-sep.component.html',
  styleUrls: ['./v-sep.component.scss']
})
export class VSepComponent implements OnInit {

  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD')
  }
  jsonResult: any
  listStatusPlg = [
    { "id": "1", name: "Atas Persetujuan Dokter" },
    { "id": "3", name: "Atas Permintaan Sendiri" },
    { "id": "4", name: "Meninggal" },
    { "id": "5", name: "Lain-lain" }
  ]
  jsonResult2:any
  jsonResult3:any
  jsonResult4:any
  jsonResult5:any
  jsonResult6:any
  jsonResult7:any
  jsonResult8:any
  jsonResult9:any
  jsonResult10:any
  jsonResult11:any
  jsonResult12:any
  listPoli: any = []
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.loadCombo()

  }
  loadCombo() {

  }
  filter(event) {
    if (event.query == '') return
    // this.listPoli = []
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
    let data = {}
    if (method == 'DELETE') {
      data = {
        "request": {
          "t_sep": {
            "noSep": this.item.delete,
            "user": "XoxoXoxo"
          }
        }
      }
    }
    if (method == 'pengajuan') {
      method = 'POST'
      data = {
        "request": {
          "t_sep": {
            "noKartu": this.item.kartu,
            "tglSep": moment(this.item.tglSep).format('YYYY-MM-DD'),
            "jnsPelayanan": this.item.tipe,
            "jnsPengajuan": this.item.jenis,
            "keterangan": this.item.ket,
            "user": "XoxoXoxo"
          }
        }
      }
    }
    if (method == 'approv') {
      method = 'POST'
      data = {
        "request": {
          "t_sep": {
            "noKartu": this.item.kartu,
            "tglSep": moment(this.item.tglSep).format('YYYY-MM-DD'),
            "jnsPelayanan": this.item.tipe,
            "jnsPengajuan": this.item.jenis,
            "keterangan": this.item.ket,
            "user": "XoxoXoxo"
          }
        }
      }
    }
    if (method == 'updatePlg') {
      method = 'PUT'
      data = {
        "request": {
          "t_sep": {
            "noSep": this.item.sep,
            "statusPulang": this.item.status.id,
            "noSuratMeninggal": this.item.status.id == 4 ? this.item.noSurat : "",
            "tglMeninggal": this.item.status.id == 4 ? moment(this.item.tglMeningal).format('YYYY-MM-DD') : "",
            "tglPulang": moment(this.item.tglPulang).format('YYYY-MM-DD'),
            "noLPManual": this.item.noLP ? this.item.noLP : "",
            "user": "XoxoXoxo"
          }
        }
      }
    }
    if (method == 'hapusInternal') {
      method = 'DELETE'
      data = {
        "request": {
          "t_sep": {
            "noSep": this.item.noSEP2,
            "noSurat": this.item.noSurat2,
            "tglRujukanInternal": moment(this.item.tglRujukan).format('YYYY-MM-DD'),
            "kdPoliTuj": this.item.poli.kode,
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


}
