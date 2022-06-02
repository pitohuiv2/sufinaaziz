import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-v-monitoring',
  templateUrl: './v-monitoring.component.html',
  styleUrls: ['./v-monitoring.component.scss']
})
export class VMonitoringComponent implements OnInit {

  jsonResult2: any
  jsonResult3: any
  jsonResult4: any
  jsonResult5: any
  jsonResult6: any
  jsonResult7: any
  jsonResult8: any
  jsonResult9: any
  jsonResult10: any
  jsonResult1: any
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD')
  }
  jsonResult: any

  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.loadCombo()

  }
  loadCombo() {

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
  cari1(jsonView) {

    let json = {
      "url": "Monitoring/Kunjungan/Tanggal/" + moment(this.item.tglSep).format('YYYY-MM-DD') + "/JnsPelayanan/" + this.item.tipe,
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