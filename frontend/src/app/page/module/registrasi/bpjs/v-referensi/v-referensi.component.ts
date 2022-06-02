import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-v-referensi',
  templateUrl: './v-referensi.component.html',
  styleUrls: ['./v-referensi.component.scss']
})
export class VReferensiComponent implements OnInit {
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD')
  }
  jsonResult: any
  listTipe: any[] = [{ name: 'No Kartu', id: '1' }, { name: 'NIK', id: '2' }];
  listProv = [];
  listKab = []
  listKec = []
  listProcedur = []
  listKelas =[]
  listDJP=[]
  listSpesialis =[]
  listRuangn=[]
  listCaraKeluar =[]
  listPasca=[]
  jsonResult2:any
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
  ) { }

  ngOnInit(): void {
    this.loadCombo()
    this.item.tipe = this.listTipe[0]
  }
  loadCombo() {
 
    let json = {
      "url": "referensi/propinsi",
      "method": "GET",
      "data": null
    }
    this.listProv = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      this.listProv = e.response.list
    })

    
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
    this.listPasca   = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json3).subscribe(e => {
      this.listPasca = e.response.list
    })

    let json4 = {
      "url": "referensi/carakeluar",
      "method": "GET",
      "data": null
    }
    this.listCaraKeluar   = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json4).subscribe(e => {
      this.listCaraKeluar = e.response.list
    })


    
    let json5 = {
      "url": "referensi/ruangrawat",
      "method": "GET",
      "data": null
    }
    this.listRuangn   = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json5).subscribe(e => {
      this.listRuangn = e.response.list
    })
    
    let json6 = {
      "url": "referensi/spesialistik",
      "method": "GET",
      "data": null
    }
    this.listSpesialis   = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json6).subscribe(e => {
      this.listSpesialis = e.response.list
    })
  }
  setData(param, data) {
    if (param == 'kab') {
      let json = {
        "url": "referensi/kabupaten/propinsi/" + data.kode,
        "method": "GET",
        "data": null
      }
      this.listKab = []
      this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
        if (e.metaData.code == "200") {
          this.alertService.success('Info', e.metaData.message);
        } else {
          this.alertService.error('Info', e.metaData.message);
        }
        this.listKab = e.response.list
      })

    } else {
      let json = {
        "url": "referensi/kecamatan/kabupaten/" + data.kode,
        "method": "GET",
        "data": null
      }
      this.listKec = []
      this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
        if (e.metaData.code == "200") {
          this.alertService.success('Info', e.metaData.message);
        } else {
          this.alertService.error('Info', e.metaData.message);
        }
        this.listKec = e.response.list
      })
    }
  }
  cari(url, jsonView) {
    let param = '';
    if (this.item.tipe.id == 1) {
      param = 'nokartu'
    } else {
      param = 'nik'
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
  filter(event) {
    if (event.query == '') return
    // this.listProcedur = []
    let json = {
      "url": "referensi/procedure/" + event.query,
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      this.listProcedur = e.response.procedure
    })
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
      this.listDJP = e.response.list
    })
  }

}
