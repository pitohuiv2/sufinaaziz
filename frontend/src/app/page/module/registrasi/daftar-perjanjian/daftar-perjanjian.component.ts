import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-daftar-perjanjian',
  templateUrl: './daftar-perjanjian.component.html',
  styleUrls: ['./daftar-perjanjian.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPerjanjianComponent implements OnInit {
  column: any[]
  item: any = {
    tglAwal: new Date(),
    tglAkhir: new Date()
  }
  listRuangan: any[]
  listStatus: any[]
  listPasien: any[]
  selected:any
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,) { }

  ngOnInit(): void {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'noreservasi', header: 'Kode Reservasi', width: "140px" },
      { field: 'nocm', header: 'No RM', width: "80px" },
      { field: 'tanggalreservasi', header: 'Tgl Reservasi', width: "150px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namaruangan', header: 'Poli', width: "200px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "180px" },
      { field: 'dokter', header: 'Dokter', width: "200px" },
      { field: 'status', header: 'Status', width: "120px", 'tag':'<p-tag severity="color" value="status"></p-tag>' },
      { field: 'notelepon', header: 'Telpon', width: "120px" },
      { field: 'tglinput', header: 'Tgl Input', width: "150px" },
    ];
    this.listStatus = [{ nama: 'Confirm' }, { nama: 'Reservasi' }]
    this.apiService.get("registrasi/get-combo-perjanjian").subscribe(e => {
      this.listRuangan = e.ruanganrajal
    })
    this.load()
  }
  cari() {
    this.load()
  }
  load() {

    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD');
    var status = "";
    if (this.item.status != undefined) {
      status = this.item.status.nama;
    }
    var ruanganId = "";
    if (this.item.ruangan != undefined) {
      ruanganId = this.item.ruangan.id;
    }
    var namapasienpm = ''
    if (this.item.namaPasien != undefined) {
      namapasienpm = this.item.namaPasien
    }
    var noRM = ""
    if (this.item.noRM != undefined) {
      noRM = this.item.noRM
    }
    var kodeReservasi = ""
    if (this.item.kodeReservasi != undefined) {
      kodeReservasi = this.item.kodeReservasi
    }
    this.apiService.get("registrasi/get-data-pasien-reservasi?"
      + "tglAwal=" + tglAwal
      + "&tglAkhir=" + tglAkhir
      + "&kdReservasi=" + kodeReservasi
      + "&statusRev=" + status
      + "&namapasienpm=" + namapasienpm
      + "&ruanganId=" + ruanganId
      + "&noRM=" + noRM
    ).subscribe(data => {
      for (let i = 0; i < data.data.length; i++) {
        const element = data.data[i];
        element.no = i + 1
        if(element.status=='Confirm'){
          element.color = 'info'
        }else{
          element.color = 'danger'
        }
      }
      this.listPasien = data.data
    })
  }
  confirm(event: Event, data) {
    if(data.status =='Confirm'){
      this.alertService.info('Info','Pasien Sudah di Confirm, tidak bisa dihapus')
      return
    }
    this.confirmationService.confirm({
      target: event.target,
      message: 'Yakin mau hapus?',
      icon: 'pi pi-exclamation-triangle',
      acceptLabel: "Ya",
      rejectLabel: "Tidak",
      accept: () => {
        this.hapus(data)
      },
      reject: () => {
        //reject action
      }
    });
  }
  hapus(e) {
    var item = {
      norec: e.norec
    }
    this.apiService.post('reservasionline/delete', item).subscribe(e => {
      this.load()
      this.saveAntrol(e.noreservasi,99)
    })
  }
  reconfirm(e) {
    if(e.status =='Confirm'){
      this.alertService.info('Info','Pasien Sudah di Confirm')
      return
    }
    if (e.nocm == null) {
      var cahce = {
        0: e,
        1: 'Online',
        2: '',
        3: '',
        4: '',
        5: '',
        6: ''
      };
      this.cacheHelper.set('CacheRegisOnline', cahce);
      this.router.navigate(['pasien-baru', e.norec,'-','-'])

    }
    else {
      this.cacheHelper.set('cacheStatusPasien', 'LAMA');
      var cacheSet = undefined;
      this.cacheHelper.set('CacheRegistrasiPasien', cacheSet);
      var cahce = {
        0: e,
        1: 'Online',
        2: '',
        3: '',
        4: '',
        5: '',
        6: ''
      };
      this.cacheHelper.set('CacheRegisOnline', cahce);
      this.router.navigate(['registrasi-ruangan', e.nocmfk])

    }
  }
  saveAntrol(param,waktu){
    var data = {
       "url": "antrean/updatewaktu",
       "jenis": "antrean",
       "method": "POST",
       "data":                                                 
       {
          "kodebooking": param,
          "taskid": waktu,//Waktu akhir farmasi/mulai buat obat
          "waktu": new Date().getTime()  
       }
   }
   this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe( e=> {})
  }
}
