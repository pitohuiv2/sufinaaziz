import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { Table } from 'primeng/table';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-v-bed',
  templateUrl: './v-bed.component.html',
  styleUrls: ['./v-bed.component.scss']
})
export class VBedComponent implements OnInit {
  jsonResult: any
  dataSource: any = []
  dataSource2: any = []
  dataSource3: any = []
  column3: any = []
  column: any
  column2: any = []
  item: any = {
    start: 0,
    limit: 100
  }
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }


  ngOnInit(): void {
    this.column = [
      { field: 'kodekelas', header: 'Kode Kelas', width: "100px" },
      { field: 'namakelas', header: 'Kelas', width: "150px", filter: true },
      { field: 'koderuang', header: 'Kode Ruang', width: "100px", filter: true },
      { field: 'namaruang', header: 'Nama Ruang', width: "250px", filter: true },
      { field: 'kapasitas', header: 'Kapasitas', width: "150px" },
      { field: 'tersedia', header: 'Tersedia', width: "150px" },
      { field: 'tersediapria', header: 'Tersedia Pria', width: "150px" },
      { field: 'tersediawanita', header: 'Tersedia Wanita', width: "150px" },
      { field: 'tersediapriawanita', header: 'Tersedia Pria Wanita', width: "150px" },
      { field: 'rownumber', header: 'Row', width: "80px" },
      { field: 'lastupdate', header: 'Tgl Update', width: "150px" },
    ];
    this.column2 = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'kodekelas', header: 'Kode', width: "100px" },
      { field: 'namakelas', header: 'Kelas', width: "150px" },

    ];
    this.column3 = [
      { field: 'kodekelas', header: 'Kode Kelas', width: "100px" },
      { field: 'namakelas', header: 'Kelas', width: "150px", filter: true },
      { field: 'koderuang', header: 'Kode Ruang', width: "100px", filter: true },
      { field: 'namaruang', header: 'Nama Ruang', width: "250px", filter: true },
      { field: 'kapasitas', header: 'Kapasitas', width: "150px" },
      { field: 'tersedia', header: 'Tersedia', width: "150px" },
      { field: 'tersediapria', header: 'Tersedia Pria', width: "150px" },
      { field: 'tersediawanita', header: 'Tersedia Wanita', width: "150px" },
      { field: 'tersediapriawanita', header: 'Tersedia Pria Wanita', width: "150px" },

    ];
    this.item.kodePPK = ''
    this.apiService.get('registrasi/get-setting-asuransi').subscribe(dat => {
      this.item.kodePPK = dat.kodePPKRujukan

    })
    let json = {
      "url": "aplicaresws/rest/ref/kelas",
      "method": "GET",
      "data": null,
      "jenis": "aplicares"
    }
    this.dataSource2 = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      for (let x = 0; x < e.response.list.length; x++) {
        const element = e.response.list[x];
        element.no = x + 1
      }
      this.dataSource2 = e.response.list
    })
    this.cariLokal()
  }
  cari() {
    let json = {
      "url": `aplicaresws/rest/bed/read/${this.item.kodePPK}/${this.item.start}/${this.item.limit}`,
      "method": "GET",
      "data": null,
      "jenis": "aplicares"
    }
    this.dataSource = []
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      for (let x = 0; x < e.response.list.length; x++) {
        const element = e.response.list[x];
        element.no = x + 1
      }
      this.dataSource = e.response.list
    })
  }
  cariLokal() {
    let kelas = ''
    let namaruangan = ''
    if (this.item.kelas) {
      kelas = this.item.kelas
    }
    if (this.item.namaruangan) {
      namaruangan = this.item.namaruangan
    }
    this.apiService.get("bridging/bpjs/aplicare?namaruangan=" + namaruangan + "&kelas=" + kelas).subscribe(e => {
      for (let x = 0; x < e.length; x++) {
        const element = e[x];
        element.no = x + 1
      }
      this.dataSource3 = e
    })
  }

  clear(table: Table) {
    table.clear();
  }
  removeData(e) {
    let json = {
      "url": `aplicaresws/rest/bed/delete/${this.item.kodePPK}`,
      "method": "POST",
      "data": {
        "kodekelas": e.kodekelas,
        "koderuang": e.koderuang,
      },
      "jenis": "aplicares"
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metadata.code == 1) {
        this.cari()
        this.alertService.success('Info', e.metadata.message);
      } else {
        this.alertService.error('Info', e.metadata.message);
      }
    })
  }
  create() {
    for (let i = 0; i < this.dataSource3.length; i++) {
      const element = this.dataSource3[i];
      let json = {
        "url": `aplicaresws/rest/bed/create/${this.item.kodePPK}`,
        "method": "POST",
        "data": {
          "kodekelas": element.kodekelas,
          "koderuang": element.koderuang,
          "namaruang": element.namaruang,
          "kapasitas": element.kapasitas,
          "tersedia": element.tersedia,
          "tersediapria": element.tersediapria,
          "tersediawanita": element.tersediawanita,
          "tersediapriawanita": element.tersediapriawanita,
        },
        "jenis": "aplicares"
      }
      this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
        if (e.metadata.code == 1) {
          this.alertService.success('Info', e.metadata.message);
        } else {
          this.alertService.error('Info', e.metadata.message);
        }
      })
    }
  }

  update() {
    for (let i = 0; i < this.dataSource3.length; i++) {
      const element = this.dataSource3[i];
      let json = {
        "url": `aplicaresws/rest/bed/update/${this.item.kodePPK}`,
        "method": "POST",
        "data": {
          "kodekelas": element.kodekelas,
          "koderuang": element.koderuang,
          "namaruang": element.namaruang,
          "kapasitas": element.kapasitas,
          "tersedia": element.tersedia,
          "tersediapria": element.tersediapria,
          "tersediawanita": element.tersediawanita,
          "tersediapriawanita": element.tersediapriawanita,
        },
        "jenis": "aplicares"
      }
      this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
        if (e.metadata.code == 1) {
          this.alertService.success('Info', e.metadata.message);
        } else {
          this.alertService.error('Info', e.metadata.message);
        }
      })
    }

  }

}
