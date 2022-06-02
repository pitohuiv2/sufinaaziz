import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { jsPDF } from "jspdf";
@Component({
  selector: 'app-antrol-jadwal-dokter',
  templateUrl: './antrol-jadwal-dokter.component.html',
  styleUrls: ['./antrol-jadwal-dokter.component.scss']
})
export class AntrolJadwalDokterComponent implements OnInit {
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD'),
    tglAwal: new Date(),
    tglAkhir: new Date(),
  }
  dataSource: any = []
  column: any = []
  listPoli: any = []
  listDokter: any = []
  isInput: boolean
  listHari = [
    { id: 1, namahari: "SENIN" },
    { id: 2, namahari: "SELASA" },
    { id: 3, namahari: "RABU" },
    { id: 4, namahari: "KAMIS" },
    { id: 5, namahari: "JUM'AT" },
    { id: 6, namahari: "SABTU" },
    { id: 7, namahari: "MINGGU" }
  ]
  add: any = {}
  selected: any = {}
  dataSourceAdd: any = []
  columnAdd: any = []
  dataSource2: any
  column2: any
  data2: any = []
  listDokterRS: any
  listDokterHFIS: any
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {

    // g

    this.column = [
      { field: 'namasubspesialis', header: 'Nama Subspesialis', width: "200px", filter: true },
      { field: 'namapoli', header: 'Poli', width: "200px", filter: true },
      { field: 'namadokter', header: 'Dokter', width: "200px", filter: true },
      { field: 'namahari', header: 'Hari', width: "150px" },
      { field: 'jadwal', header: 'Jadwal', width: "150px" },
      { field: 'libur', header: 'Libur', width: "150px" },
      { field: 'kapasitaspasien', header: 'Kapasitas', width: "100px" },
    ];
    this.columnAdd = [
      { field: 'namahari', header: 'Hari', width: "100px", filter: true },
      { field: 'buka', header: 'Jam Buka', width: "100px", filter: true },
      { field: 'tutup', header: 'Jam Tutup', width: "100px", filter: true },
    ];
    this.column2 = [
      { field: 'namalengkap', header: 'Dokter', width: "200px", filter: true },
      { field: 'id', header: 'Kode Dokter RS', width: "100px", filter: true },
      { field: 'kddokterbpjs', header: 'Kode Dokter BPJS', width: "100px", filter: true },
    ];
    this.apiService.get('bridging/antrean/get-combo').subscribe(dat => {
      this.listDokter = dat.dokter
      this.listPoli = dat.ruangan
    })
    this.apiService.get('general/get-data-combo-dokter').subscribe(dat => {
      this.listDokterRS = dat
    })
    this.apiService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=1&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=IGD").subscribe(data => {
      if (data.metaData.code == 200) {
        this.listDokterHFIS = data.response.list;
      }
      else
        this.alertService.info('Info', 'Dokter DPJP tidak ada')
    });
  }
  cari() {
    if (!this.item.poli) {
      this.alertService.warn('Info', 'Poli harus dipilih');
      return
    }

    let json = {
      "url": "jadwaldokter/kodepoli/" + this.item.poli.kode + "/tanggal/" + moment(this.item.tglAwal).format('YYYY-MM-DD'),
      "jenis": "antrean",
      "method": "GET",
      "data": null
    }
    this.dataSource = []
    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.dataSource = e.response
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  cariD() {
    this.dataSource2 = []
    this.apiService.get('bridging/bpjs/get-data-mappingdkoterbpjs').subscribe(e => {
      this.dataSource2 = e
    })

  }
  edit(e) {
    this.isInput = true
    this.selected = e
    this.data2.push({
      'no': 1,
      'hari': e.hari,
      'namahari': e.namahari,
      'buka': e.jadwal.split('-')[0],
      'tutup': e.jadwal.split('-')[1],
    })
    this.dataSourceAdd = this.data2
  }
  hapusD(dataSelected) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == dataSelected.no) {
        this.data2.splice(i, 1);
        this.dataSourceAdd = this.data2
      }
    }
  }
  tambah() {
    if (!this.add.hari) {
      this.alertService.warn('Info', 'Hari harus dipilih');
      return
    }
    if (!this.add.jambuka) {
      this.alertService.warn('Info', 'Jam Buka harus dipilih');
      return
    }
    if (!this.add.jamtutup) {
      this.alertService.warn('Info', 'Jam Tutup harus dipilih');
      return
    }
    var nomor = 0
    if (this.data2.length == 0) {
      nomor = 1
    } else {
      nomor = this.data2.length + 1
    }
    var data: any = {};

    if (this.add.no != undefined) {
      for (var i = this.data2.length - 1; i >= 0; i--) {
        if (this.data2[i].no == this.add.no) {
          data.no = this.add.no
          data.hari = this.add.hari.id
          data.namahari = this.add.hari.namahari
          data.buka = this.add.jambuka
          data.tutup = this.add.jamtutup
          this.data2[i] = data;
          this.dataSourceAdd = this.data2
        }
      }
    } else {
      data = {
        'no': nomor,
        'hari': this.add.hari.id,
        'buka': this.add.jambuka,
        'tutup': this.add.jamtutup,
        'namahari': this.add.hari.namahari,
      }
      this.data2.push(data)
      this.dataSourceAdd = this.data2
    }


    this.add = {}
  }
  editRow(e) {
    this.add.no = e.no
    for (let x = 0; x < this.listHari.length; x++) {
      const element = this.listHari[x];
      if (element.id == e.hari) {
        this.add.hari = element
      }
    }
    this.add.jambuka = e.buka
    this.add.jamtutup = e.tutup
  }
  clear() {
    this.data2 = []
    this.dataSourceAdd = []
    this.isInput = false
    this.selected = {}
  }
  save() {
    let json = {
      "url": "jadwaldokter/updatejadwaldokter",
      "jenis": "antrean",
      "method": "POST",
      "data":
      {
        "kodepoli": this.selected.kodepoli,
        "kodesubspesialis": this.selected.kodesubspesialis,
        "kodedokter": this.selected.kodedokter,
        "jadwal": this.data2
      }
    }
    this.dataSource = []
    this.apiService.postNonMessage('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
        this.clear()
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  editD(e) {
    this.item.dokterRS = { id: e.id, namalengkap: e.namalengkap }
    for (let x = 0; x < this.listDokterHFIS.length; x++) {
      const element = this.listDokterHFIS[x];
      if (element.kode == e.kddokterbpjs) {
        this.item.dokterHFIS = element
        break
      }
    }

  }
  hapusDok(e) {
    let json = {
      'id': e.id,
    }
    this.apiService.post('bridging/bpjs/hapus-data-mappingdkoterbpjs', json).subscribe(e => {
      this.cariD()
    })

  }
  addDokter() {
    if (!this.item.dokterRS) {
      this.alertService.warn('Info', 'Dokter RS harus dipilih');
      return
    }
    if (!this.item.dokterHFIS) {
      this.alertService.warn('Info', 'Dokter BPJS harus dipilih');
      return
    }
    let json = {
      'idpegawai': this.item.dokterRS.id,
      'kodedokterbpjs': this.item.dokterHFIS.kode,
    }
    this.apiService.post('bridging/bpjs/save-data-mappingdkoterbpjs', json).subscribe(e => {
      this.cariD()
      delete this.item.dokterRS
      delete this.item.dokterHFIS
    })
  }

  onTabOpen(event) {
    if (event.index == 1) {
      this.cariD()
    }
  }
}
