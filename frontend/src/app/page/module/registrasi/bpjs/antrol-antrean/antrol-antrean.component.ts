import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
@Component({
  selector: 'app-antrol-antrean',
  templateUrl: './antrol-antrean.component.html',
  styleUrls: ['./antrol-antrean.component.scss']
})
export class AntrolAntreanComponent implements OnInit {
  minDateValue = new Date()
  item: any = {
    now: moment(new Date()).format('YYYY-MM-DD'),
    waktu: new Date(),
    tanggalperiksa: new Date(new Date().setDate(new Date().getDate() + 1)),
    tahun: new Date().getFullYear(),
    keterangan: 'Peserta harap 30 menit lebih awal guna pencatatan administrasi.',
    sisakuotajkn: 0,
    kuotajkn: 0,
    sisakuotanonjkn: 0,
    kuotanonjkn: 0,
  }
  listTask = [
    {
      id: 1,
      waktu: "Waktu tunggu admisi"
    },
    {
      id: 2,
      waktu: "Waktu layan admisi"
    },
    {
      id: 3,
      waktu: "Waktu tunggu poli"
    },
    {
      id: 4,
      waktu: "Waktu layan poli"
    },
    {
      id: 5,
      waktu: "Waktu tunggu farmasi"
    },
    {
      id: 6,
      waktu: "Waktu layan farmasi"
    },
    {
      id: 7,
      waktu: "Waktu Selesai Obat Dibuat"
    },
    {
      id: 99,
      waktu: "Tidak hadir/batal"
    }
  ]
  dataSource: any = []
  column: any = []
  listDokter = []
  listPoli = []
  listJenis: any = [
    { kode: 1, nama: 'Rujukan FKTP' },
    { kode: 2, nama: 'Rujukan Internal' },
    { kode: 3, nama: 'Kontrol' },
    { kode: 4, nama: 'Rujukan Antar RS' }
  ]
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.column = [
      { field: 'wakturs', header: 'Waktu RS', width: "200px" },
      { field: 'waktu', header: 'Waktu', width: "200px" },
      { field: 'taskname', header: 'Nama Task', width: "200px" },
      { field: 'taskid', header: 'Task ID', width: "150px" },
      { field: 'kodebooking', header: 'Kode Booking', width: "150px" },
    ];
    this.apiService.get('bridging/antrean/get-combo').subscribe(dat => {
      // this.listDokter = dat.dokter
      this.listPoli = dat.ruangan
    })
  }
  cariJadwal(e) {
    let json = {
      "url": "jadwaldokter/kodepoli/" + e.value.kode + "/tanggal/" + moment(this.item.tanggalperiksa).format('YYYY-MM-DD'),
      "jenis": "antrean",
      "method": "GET",
      "data": null
    }
    this.listDokter = []
    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.listDokter = e.response
        this.alertService.success('Jadwal Dokter Poli', e.metaData.message);
      } else {
        this.alertService.error('Jadwal Dokter Poli', e.metaData.message);
      }
    })
  }
  postWaktu() {
    let json = {
      "url": "antrean/updatewaktu",
      "jenis": "antrean",
      "method": "POST",
      "data": {
        "kodebooking": this.item.kodebooking ? this.item.kodebooking : "",
        "taskid": this.item.taskid ? this.item.taskid.id : "",
        "waktu": this.item.waktu ? this.item.waktu.getTime() : "",
      }
    }
    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  batalAntrena() {
    let json = {
      "url": "antrean/batal",
      "jenis": "antrean",
      "method": "POST",
      "data": {
        "kodebooking": this.item.kodebooking ? this.item.kodebooking : "",
        "keterangan": this.item.keterangan ? this.item.keterangan : "",
      }
    }
    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  listTaskC() {
    let json = {
      "url": "antrean/getlisttask",
      "jenis": "antrean",
      "method": "POST",
      "data": {
        "kodebooking": this.item.kodebooking ? this.item.kodebooking : "",

      }
    }
    this.dataSource = []
    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.dataSource = e.response
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  save() {
    let json = {
      "url": "antrean/add",
      "jenis": "antrean",
      "method": "POST",
      "data": {
        "kodebooking": this.item.kodebooking ? this.item.kodebooking : "",
        "jenispasien": this.item.jenispasien != undefined ? (this.item.jenispasien == 1 ? 'JKN' : 'NON JKN') : "",
        "nomorkartu": this.item.nomorkartu ? this.item.nomorkartu : "",
        "nik": this.item.nik ? this.item.nik : "",
        "nohp": this.item.nohp ? this.item.nohp : "",
        "kodepoli": this.item.poli ? this.item.poli.kode : "",
        "namapoli": this.item.poli ? this.item.poli.nama : "",
        "pasienbaru": this.item.pasienbaru != undefined ? (this.item.pasienbaru == true ? 1 : 0) : 0,
        "norm": this.item.norm ? this.item.norm : "",
        "tanggalperiksa": this.item.tanggalperiksa ? moment(this.item.tanggalperiksa).format('YYYY-MM-DD') : "",
        "kodedokter": this.item.dokter ? this.item.dokter.kodedokter : "",
        "namadokter": this.item.dokter ? this.item.dokter.namadokter : "",
        "jampraktek": this.item.dokter ? this.item.dokter.jadwal : "",
        "jeniskunjungan": this.item.jeniskunjungan ? this.item.jeniskunjungan.kode : "",
        "nomorreferensi": this.item.nomorreferensi ? this.item.nomorreferensi : "",
        "nomorantrean": this.item.nomorantrean != undefined ? this.item.nomorantrean : "",
        "angkaantrean": this.item.angkaantrean != undefined ? this.item.angkaantrean : "",
        "estimasidilayani": this.item.estimasidilayani ? this.item.estimasidilayani.getTime() : "",
        "sisakuotajkn": this.item.sisakuotajkn != undefined ? this.item.sisakuotajkn : "",
        "kuotajkn": this.item.kuotajkn != undefined ? this.item.kuotajkn : "",
        "sisakuotanonjkn": this.item.sisakuotanonjkn != undefined ? this.item.sisakuotanonjkn : "",
        "kuotanonjkn": this.item.kuotanonjkn != undefined ? this.item.kuotanonjkn : "",
        "keterangan": this.item.keterangan ? this.item.keterangan : "",
      }
    }

    this.apiService.post('bridging/bpjs/tools', json).subscribe(e => {
      if (e.metaData.code == "200") {

        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
    })
  }
  clear() {

  }

}
