import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-antrol-dashboard',
  templateUrl: './antrol-dashboard.component.html',
  styleUrls: ['./antrol-dashboard.component.scss']
})
export class AntrolDashboardComponent implements OnInit {
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD'),
    tglAwal: new Date(),
    tglAkhir: new Date(),
    tahun: new Date().getFullYear(),
    tglperiksa: new Date()
  }
  sisa: any = {}
  dataSource: any = []
  column: any = []
  listPoli: any = []
  listDokter: any = []
  isInput: boolean
  listBulan = [
    { kode: '01', nama: "JANUARI" },
    { kode: '02', nama: "FEBRUARI" },
    { kode: '03', nama: "MARET" },
    { kode: '04', nama: "APRIL" },
    { kode: '05', nama: "MEI" },
    { kode: '06', nama: "JUNI" },
    { kode: '08', nama: "JULI" },
    { kode: '09', nama: "AGUSTUS" },
    { kode: '10', nama: "SEPTEMBER" },
    { kode: '11', nama: "NOVEMBER" },
    { kode: '12', nama: "DESEMBER" }
  ]
  add: any = {}
  selected: any = {}
  dataSource2: any = []
  column2: any = []
  data2: any = []
  listWaktu = [
    { nama: "rs" },
    { nama: "server" },
  ]
  listDokterHFIS: any
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.apiService.get('bridging/antrean/get-combo').subscribe(dat => {
      this.listDokter = dat.dokter
      this.listPoli = dat.ruangan
    })
    this.apiService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=1&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=IGD").subscribe(data => {
      if (data.metaData.code == 200) {
        this.listDokterHFIS = data.response.list;
      }
      else
        this.alertService.info('Info', 'Dokter DPJP tidak ada')
    });
    this.column = [
      {
        "field": "kdppk",
        "header": "Kode Faskes",
        "width": "80px"
      },
      {
        "field": "nmppk",
        "header": "Faskes",
        "width": "200px"
      },
      {
        "field": "tanggal",
        "header": "Tanggal",
        "width": "100px"
      },
      {
        "field": "jumlah_antrean",
        "header": "Jumlah Antrean",
        "width": "100px"
      },

      {
        "field": "namapoli",
        "header": "Poli",
        "width": "200px"
      },

      {
        "field": "waktu_task1",
        "header": "Waktu tunggu admisi",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task1",
        "header": "Rata2 Waktu tunggu admisi",
        "width": "100px"
      },
      {
        "field": "waktu_task2",
        "header": "Waktu layan admisi",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task2",
        "header": "Rata2 Waktu layan admisi",
        "width": "100px"
      },

      {
        "field": "waktu_task3",
        "header": "Waktu tunggu poli",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task3",
        "header": "Rata2 Waktu tunggu poli",
        "width": "100px"
      },

      {
        "field": "waktu_task4",
        "header": "Waktu layan poli",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task4",
        "header": "Rata2 Waktu layan Poli",
        "width": "100px"
      },

      {
        "field": "waktu_task5",
        "header": "Waktu layan poli",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task5",
        "header": "Rata2 Waktu tunggu farmasi",
        "width": "100px"
      },
      {
        "field": "waktu_task6",
        "header": "Waktu layan farmasi",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task6",
        "header": "Rata2 Waktu layan farmasi",
        "width": "100px"
      },

      {
        "field": "insertdate",
        "header": "Update",
        "width": "100px"
      },
    ];
    this.column2 = [
      {
        "field": "kdppk",
        "header": "Kode Faskes",
        "width": "80px"
      },
      {
        "field": "nmppk",
        "header": "Faskes",
        "width": "200px"
      },
      {
        "field": "tanggal",
        "header": "Tanggal",
        "width": "100px"
      },
      {
        "field": "jumlah_antrean",
        "header": "Jumlah Antrean",
        "width": "100px"
      },

      {
        "field": "namapoli",
        "header": "Poli",
        "width": "200px"
      },

      {
        "field": "waktu_task1",
        "header": "Waktu tunggu admisi",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task1",
        "header": "Rata2 Waktu tunggu admisi",
        "width": "100px"
      },
      {
        "field": "waktu_task2",
        "header": "Waktu layan admisi",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task2",
        "header": "Rata2 Waktu layan admisi",
        "width": "100px"
      },

      {
        "field": "waktu_task3",
        "header": "Waktu tunggu poli",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task3",
        "header": "Rata2 Waktu tunggu poli",
        "width": "100px"
      },

      {
        "field": "waktu_task4",
        "header": "Waktu layan poli",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task4",
        "header": "Rata2 Waktu layan Poli",
        "width": "100px"
      },

      {
        "field": "waktu_task5",
        "header": "Waktu layan poli",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task5",
        "header": "Rata2 Waktu tunggu farmasi",
        "width": "100px"
      },
      {
        "field": "waktu_task6",
        "header": "Waktu layan farmasi",
        "width": "100px"
      },
      {
        "field": "avg_waktu_task6",
        "header": "Rata2 Waktu layan farmasi",
        "width": "100px"
      },

      {
        "field": "insertdate",
        "header": "Update",
        "width": "100px"
      },
    ];
  }
  cari() {
    if (!this.item.waktu) {
      this.alertService.warn('Info', 'Waktu harus dipilih');
      return
    }
    let json = {
      "url": "dashboard/waktutunggu/tanggal/" + moment(this.item.tglAwal).format('YYYY-MM-DD') + "/waktu/" + this.item.waktu.nama,
      "jenis": "antrean",
      "method": "GET",
      "data": null
    }
    this.dataSource = []
    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.dataSource = e.response.list
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  cari2() {
    if (!this.item.waktu) {
      this.alertService.warn('Info', 'Waktu harus dipilih');
      return
    }
    if (!this.item.bulan) {
      this.alertService.warn('Info', 'Bulan harus dipilih');
      return
    }
    let json = {
      "url": "dashboard/waktutunggu/bulan/" + this.item.bulan.kode + "/tahun/" + this.item.tahun + "/waktu/" + this.item.waktu.nama,
      "jenis": "antrean",
      "method": "GET",
      "data": null
    }
    this.dataSource2 = []
    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.dataSource2 = e.response.list
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  cari3() {
    if (!this.item.tglperiksa) {
      return
    }
    if (!this.item.poli) {
      return
    }
    if (!this.item.dokterHFIS) {
      return
    }
    
    let json = {
      "kodepoli": this.item.poli.kode,
      "kodedokter": this.item.dokterHFIS.kode,
      "tanggalperiksa": moment(this.item.tglperiksa).format('YYYY-MM-DD'),
      "jampraktek": "08:00-16:00"
  }
    this.dataSource2 = []
    this.apiService.post('jkn/get-status-antrean', json).subscribe(e => {
      if (e.metadata.code == "200") {
        this.sisa = e.response
      } else {
        this.alertService.error('Info', e.metadata.message);
      }
    })
  }

}
