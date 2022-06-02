import { Component, OnInit, Inject, forwardRef } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { AppComponent } from './../../../app.component';
import * as $ from "jquery";
import { FormGroup, FormBuilder, FormControl } from '@angular/forms';

import * as moment from 'moment'
import { ApiService } from 'src/app/service';
import { CacheService } from 'src/app/service/cache.service';
import { AlertService } from 'src/app/service/component/alert/alert.service';

import { HelperService } from 'src/app/service/helperService';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { SelectItem } from 'primeng/api';
import { Config } from '../../../guard';
@Component({
  selector: 'app-verifikasi-pasien-bpjs',
  templateUrl: './verifikasi-pasien-bpjs.component.html',
  styleUrls: ['./verifikasi-pasien-bpjs.component.scss']
})
export class VerifikasiPasienBpjsComponent implements OnInit {
  url: any
  sub: any;
  formGroup: FormGroup;
  isInfoPasien: boolean = false
  pasien: any = {}
  dataCache: any
  item: any = {
    peserta: {
      jenisPeserta: {},
      umur: {},
      mr: {}
    },
    provPerujuk: {},
    diagnosa: {},
    poliRujukan: {}
  }
  pasienDaftar: any = {}
  pemAsuransi: any = {}
  dataSEP: any = {}
  diagnosa: any = {}
  listDokter: SelectItem[]
  listDokterMelayani: SelectItem[]
  listRujukan: SelectItem[]
  listTujuanKun: SelectItem[]
  listFlag: SelectItem[]
  listPenunjang: SelectItem[]
  listAsses: SelectItem[]
  listHistori: any[]
  isTemporaryBrigding: string
  showCetak: boolean = false
  isSuksesSEP: boolean = false
  ppkPelayananRS: any = "0240R008"
  listPoli: SelectItem[]
  listPoliTemp: any = []
  listDokTemp: any = []
  noSKDP: any
  noReservasi: any
  isAdminOtomatisKiosk: any
  isStatusPasien: any
  showPasca: boolean = true
  kodeDokter: any = null
  eksekutif: any = 1
  test: any = "PILIH DPJP Layan"
  tests: any = "PILIH POLI"

  noPascaRanap: any
  item1: any = {}

  showPenjagaan: boolean = false
  showGantiDokter: boolean
  showGantiPoli: boolean
  CurrentPage: string;
  CurrentPages: string;
  buttons: SelectItem[]
  buttonss: SelectItem[]
  listRadio: any[] = [{ name: 'PCare', id: 'pcare' }, { name: 'Rumah Sakit', id: 'rs' }, { name: 'Pasca Ranap', id: 'pasca' }, { name: 'Kontrol RJ', id: 'kontrol' }];
  constructor(@Inject(forwardRef(() => AppComponent))
  public app: AppComponent,
    private router: Router,
    private route: ActivatedRoute,
    private httpService: ApiService,
    private fb: FormBuilder,
    private cacheHelper: CacheService,
    private alertService: AlertService,
    private helper: HelperService,
    private service: HttpClient,) {

  }

  ngOnInit() {
    this.listRujukan = []
    // this.listRujukan.push({ label: "--Histori No Rujukan--", value: null });
    this.httpService.get('kiosk/get-combo-setting').subscribe(resps => {
      this.ppkPelayananRS = resps.ppkpelayanan
      this.isTemporaryBrigding = resps.isTemporaryBrigding
      this.isAdminOtomatisKiosk = resps.isAdminOtomatisKiosk

    }, error => {
      this.isTemporaryBrigding = 'false'
      this.isAdminOtomatisKiosk = 'false'
      this.ppkPelayananRS = '0240R008'
    })
    // this.httpService.get('bridging/bpjs/generateskdp').subscribe(resp => {
    //   this.noSKDP = resp.noskdp

    // })



    this.cacheHelper.set('cacheSelfRegis', undefined);
    this.formGroup = this.fb.group({
      'noRujukan': new FormControl(''),
      'dokterDPJP': new FormControl(''),
      'multiRujukan': new FormControl(''),
      'pCare': new FormControl(''),
      'rumahSakit': new FormControl(''),
      'poliTujuan': new FormControl(''),
      'allDPJP': new FormControl(null),
      'pascaRanap': new FormControl(''),
      'dokterDPJPMelayani': new FormControl(''),
      'tujuanKunj': new FormControl(''),
      'flagProcedure': new FormControl(''),
      'kdPenunjang': new FormControl(''),
      'assesmentPel': new FormControl(''),
      'noSKDP': new FormControl(''),

    })
    this.sub = this.route
      .queryParams
      .subscribe(params => {
        // Defaults to 0 if no query param provided.
        this.url = params['page'];
      });

    var listTujuan = [
      { id: "0", name: "Normal" },
      { id: "1", name: "Prosedur" },
      { id: "2", name: "Konsul Dokter" },
    ]
    this.listTujuanKun = [];
    this.listTujuanKun.push({ label: '-- Tujuan Kunjungan --', value: null });

    listTujuan.forEach(response => {
      this.listTujuanKun.push({
        label: response.name, value: {
          'kode': response.id,
          'nama': response.name,
        }
      });
    });
    // var listFlag = [
    //   { id: "0", name: "Prosedur Tidak Berkelanjutan" },
    //   { id: "1", name: "Prosedur dan Terapi Berkelanjutan" },
    //   // { id: "2", name: "Konsul Dokter" },
    // ]

    var listFlag = [
      {
        id: "0", name: "Prosedur Tidak Berkelanjutan",
        details: [
          { id: "7", name: "Laboratorium" },
          { id: "8", name: "USG" },
          { id: "11", name: "MRI" },
          { id: "9", name: "Farmasi" },
          { id: "10", name: "Lain-Lain" },
        ]
      },
      {
        id: "1", name: "Prosedur dan Terapi Berkelanjutan",
        details: [
          { id: "1", name: "Radioterapi" },
          { id: "2", name: "Kemoterapi" },
          { id: "3", name: "Rehabilitasi Medik" },
          { id: "4", name: "Rehabilitasi Psikososial" },
          { id: "5", name: "Transfusi Darah" },
          { id: "6", name: "Pelayanan Gigi" },
          { id: "12", name: "HEMODIALISA" },
        ]
      },
      // { id: "2", name: "Konsul Dokter" },
    ]
    this.listFlag = [];
    this.listFlag.push({ label: '-- Flag Procedure --', value: null });

    listFlag.forEach(response => {
      this.listFlag.push({
        label: response.name, value: {
          'kode': response.id,
          'nama': response.name,
          'details': response.details,
        }
      });
    });
    // var listPenunjang = [
    //   { id: "1", name: "Radioterapi" },
    //   { id: "2", name: "Kemoterapi" },
    //   { id: "3", name: "Rehabilitasi Medik" },
    //   { id: "4", name: "Rehabilitasi Psikososial" },
    //   { id: "5", name: "Transfusi Darah" },
    //   { id: "6", name: "Pelayanan Gigi" },
    //   { id: "7", name: "Laboratorium" },
    //   { id: "8", name: "USG" },
    //   { id: "9", name: "Farmasi" },
    //   { id: "10", name: "Lain-Lain" },
    //   { id: "11", name: "MRI" },
    //   { id: "12", name: "HEMODIALISA" },
    // ]
    // this.listPenunjang = [];
    // this.listPenunjang.push({ label: '-- Penunjang --', value: null });

    // listPenunjang.forEach(response => {
    //   this.listPenunjang.push({
    //     label: response.name, value: {
    //       'kode': response.id,
    //       'nama': response.name,
    //     }
    //   });
    // });
    var listAsesmen = [
      { id: "1", name: "Poli spesialis tidak tersedia pada hari sebelumnya" },
      { id: "2", name: "Jam Poli telah berakhir pada hari sebelumnya" },
      { id: "3", name: "Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya" },
      { id: "4", name: "Atas Instruksi RS" },
      { id: "5", name: "Tujuan Kontrol" },
    ]
    this.listAsses=[]
    this.listAsses.push({ label: '-- Assesment --', value: null });

    listAsesmen.forEach(response => {
      this.listAsses.push({
        label: response.name, value: {
          'kode': response.id,
          'nama': response.name,
        }
      });
    });

    this.httpService.get('bridging/bpjs/get-daftar-poli-internal').subscribe(response => {
      this.listPoli = [];
      this.buttonss = [];
      this.listPoli.push({ label: '-- Poli --', value: null });
      response.forEach(response => {
        this.listPoli.push({
          label: response.namaruangan, value: {
            'kode': response.kdinternal,
            'nama': response.namaruangan,
            'kodeinternal': response.id,
            'objectdepartemenfk': response.objectdepartemenfk,
            'iseksekutif': response.iseksekutif,
          }
        });


        this.buttonss.push({
          label: response.namaruangan, value: {
            'kode': response.kdinternal,
            'nama': response.namaruangan,
            'kodeinternal': response.id,
            'objectdepartemenfk': response.objectdepartemenfk,
            'iseksekutif': response.iseksekutif,
          }
        });
      });
      this.listPoliTemp = response




    }, error => {
      this.buttonss = [];
      this.listPoli = [];
      this.listPoliTemp = []
    })

    let cacheOnlineBPJS = this.cacheHelper.get('cacheOnlineBPJS')
    if (cacheOnlineBPJS != undefined) {
      this.formGroup.get('noRujukan').setValue(cacheOnlineBPJS.nobpjs)
      this.noReservasi = cacheOnlineBPJS.noreservasi
      this.cacheHelper.set('cacheOnlineBPJS', undefined)
    }
    this.formGroup.get('tujuanKunj').setValue(this.listTujuanKun[1].value)
  }
  changeFlag(event) {
    this.listPenunjang = [];
    this.listPenunjang.push({ label: '-- Penunjang --', value: null });
    if (event.value == null) return
    event.value.details.forEach(response => {
      this.listPenunjang.push({
        label: response.name, value: {
          'kode': response.id,
          'nama': response.name,
        }
      });
    });
  }
  ChangeScreen(button: string) {
    this.CurrentPage = button;
    this.test = this.CurrentPage['label'];
    this.formGroup.get('dokterDPJPMelayani').setValue(this.CurrentPage['value'])
    this.showGantiDokter = false;
  }
  ChangeScreens(button: string) {
    this.CurrentPages = button;
    this.tests = this.CurrentPages['label'];
    this.formGroup.get('poliTujuan').setValue(this.CurrentPages['value'])
    if (this.formGroup.get('poliTujuan').value.kode != this.item.poliRujukan.kode) {
      this.formGroup.get('assesmentPel').setValue(this.listAsses[1].value)
    }
    this.showGantiPoli = false;
    this.changePoli(button);
  }
  changeNoRujukan(event) {
    let noKunjungan = this.listRujukan.find(data => data.value == event.value)
    console.log(noKunjungan)
    // this.item.noKunjungan = noKunjungan.label
    // this.item.diagnosa.kode = noKunjungan.value
    this.cekPesertaByNoRujukan(noKunjungan.label)
  }


  goBack() {
    window.history.back()
  }
  ngOnDestroy() {
    this.sub.unsubscribe();
  }
  //  getPegawaiByName(event) {
  //   this.httpService.get('master/pegawai/get-pegawai-by-nama/' + event.query).subscribe(data => {
  //     this.listPegawai = data.data;
  //   });
  // }

  // getNoRujukan() {
  //   if (this.formGroup.get('noRujukan').value == '') return;
  //   if (this.formGroup.get('pCare').value == '' && this.formGroup.get('rumahSakit').value == '') {
  //     this.alertService.warn('Peringatan', 'Jenis Faskes Belum di pilih')
  //     return;
  //   }

  //   let nomor = this.formGroup.get('noRujukan').value
  //   if (this.isTemporaryBrigding == 'true') {
  //     this.getNoKaTemporaryBrigding()
  //   } else {
  //     if (nomor.toString().length <= 14) {
  //       this.cekPesertaByNoKartu(this.formGroup.get('noRujukan').value)
  //     } else {
  //       this.cekPesertaByNoRujukan(this.formGroup.get('noRujukan').value)
  //     }
  //   }

  // }
  getNoRujukan() {
    if (this.formGroup.get('noRujukan').value == '') {
      this.alertService.error('INFO', 'DATA BELUM DI ISI');
      return
    };

    if (this.formGroup.get('pCare').value == '') {
      this.alertService.error('INFO', 'SILAHKAN PILIH ASAL RUJUKAN ANDA');
      return
    }

    // if (this.formGroup.get('pCare').value == '' && this.formGroup.get('rumahSakit').value == '' && this.formGroup.get('pascaRanap').value == '') {
    //   this.alertService.warn('Peringatan', 'Jenis Faskes Belum di pilih')
    //   return;
    // }


    let nomor = this.formGroup.get('noRujukan').value
    if (this.isTemporaryBrigding == 'true') {
      this.getNoKaTemporaryBrigding()
    } else {
      if (nomor.toString().length == 6) {
        this.getPasienByNoCM1(nomor)
      }
      else if (nomor.toString().length <= 14) {
        this.cekPesertaByNoKartu(this.formGroup.get('noRujukan').value)
      } else {
        this.cekPesertaByNoRujukan(this.formGroup.get('noRujukan').value)
      }
    }

  }

  getNoKaTemporaryBrigding() {
    this.listDokter = []
    this.httpService.get('kiosk/get-combo-dokter-temp').subscribe(sa => {
      this.listDokter.push(
        {
          label: '-- Dokter -- ',
          value: null
        })
      sa.forEach(element => {
        this.listDokter.push({
          label: element.nama,
          value: {
            'kode': element.kode,
            'nama': element.nama
          }
        })
      });
    }, error => {
      this.listDokter = [
        {
          label: '-- Dokter -- ',
          value: null
        }, {
          label: 'DR.I NYOMAN DWIJA PUTRA, SP.B',
          value: {
            'kode': '1',
            'nama': 'DR.I NYOMAN DWIJA PUTRA, SP.B'
          }
        },
        {
          label: 'dr. Devi Rina M Tarigan',
          value: {
            'kode': '2',
            'nama': 'dr. Devi Rina M Tarigan'
          }
        },
        {
          label: 'VERRA',
          value: {
            'kode': '3',
            'nama': 'VERRA'
          }
        }
      ];
    })
    this.getJSON('rujukan').subscribe(e => {
      let res = e.response
      for (let i = 0; i < res.length; i++) {
        const element = res[i];
        let nomorPembanding = ''
        if (this.formGroup.get('noRujukan').value.toString().length <= 14 && this.formGroup.get('noRujukan').value.toString().length > 6)
          nomorPembanding = element.rujukan.peserta.noKartu
        if (this.formGroup.get('noRujukan').value.toString().length == 6)
          nomorPembanding = element.rujukan.peserta.mr.noMR
        else
          nomorPembanding = element.rujukan.noKunjungan

        if (nomorPembanding == this.formGroup.get('noRujukan').value) {

          element.rujukan.tglKunjungan = moment(new Date()).format('YYYY-MM-DD')
          this.item = element.rujukan;

          if (element.rujukan.peserta.mr.noMR != null)
            this.getPasienByNoCM(element.rujukan.peserta.mr.noMR)
          else
            element.rujukan.peserta.mr.noMR = '-'
          this.httpService.get('kiosk/get-ruanganbykode/' + element.rujukan.poliRujukan.kode).subscribe(res => {
            if (res.data != null) {
              this.formGroup.get('poliTujuan').setValue({
                'kode': res.data.kdinternal,
                'nama': res.data.namaruangan,
                'kodeinternal': res.data.id,
                'objectdepartemenfk': res.data.objectdepartemenfk,
                'iseksekutif': res.data.iseksekutif,
              })
              this.pasienDaftar.idruangan = res.data.id
              this.pasienDaftar.objectdepartemenfk = res.data.objectdepartemenfk
            }

          })
          this.getDiagnosaByKode(element.rujukan.diagnosa.kode)


          this.isInfoPasien = true
          this.alertService.info('Status Peserta', element.rujukan.peserta.statusPeserta.keterangan);
          break
        }
      }
    });
  }
  getPasienByNoCM1(nocm) {
    this.httpService.get('reservasionline/get-pasien/' + nocm + '/null').subscribe(e => {
      if (e.data.length > 0) {
        this.formGroup.get('noRujukan').setValue(e.data[0].nobpjs)
        this.cekPesertaByNoKartu(this.formGroup.get('noRujukan').value)
      } else {
        this.alertService.error('SILAHKAN AMBIL ANTRIAN', 'PASIEN TIDAK DITEMUKAN');
        return
      }
    })
  }

  cekPesertaByNoKartu(noKa) {
    this.showPasca = true
    if (this.formGroup.get('pCare') == null) {
      this.alertService.error('INFO', 'SILAHKAN PILIH ASAL FASKES');
      return
    }
    let jenis = this.formGroup.get('pCare').value
    this.formGroup.get('tujuanKunj').setValue(this.listTujuanKun[1].value)
    this.formGroup.get('noSKDP').setValue('')
    this.formGroup.get('flagProcedure').setValue('')
    this.formGroup.get('kdPenunjang').setValue('')
    this.formGroup.get('assesmentPel').setValue('')
    if (this.formGroup.get('pCare').value == 'pasca') {
      jenis = 'pcare'
      this.showPasca = false;
      this.httpService.get("bridging/bpjs/get-no-peserta?nokartu=" + noKa + "&tglsep=" + moment(new Date()).format('YYYY-MM-DD')).subscribe(e => {
        if (e.metaData.code === "200") {
          this.item1 = e.response.peserta;

          if (e.response.peserta.mr.noMR != null) {

            this.getPasienByNoCM(e.response.peserta.mr.noMR)
            // if (e.response.peserta.mr.noMR.length == 1) {
            //   e.response.peserta.mr.noMR = "0000000" + e.response.peserta.mr.noMR
            // } else if (e.response.peserta.mr.noMR.length == 2) {
            //   e.response.peserta.mr.noMR = "000000" + e.response.peserta.mr.noMR
            // } else if (e.response.peserta.mr.noMR.length == 3) {
            //   e.response.peserta.mr.noMR = "00000" + e.response.peserta.mr.noMR
            // } else if (e.response.peserta.mr.noMR.length == 4) {
            //   e.response.peserta.mr.noMR = "0000" + e.response.peserta.mr.noMR
            // } else if (e.response.peserta.mr.noMR.length == 5) {
            //   e.response.peserta.mr.noMR = "000" + e.response.peserta.mr.noMR
            // } else if (e.response.peserta.mr.noMR.length == 6) {
            //   e.response.peserta.mr.noMR = "00" + e.response.peserta.mr.noMR
            // } else if (e.response.peserta.mr.noMR.length == 7) {
            //   e.response.peserta.mr.noMR = "0" + e.response.peserta.mr.noMR
            // }
          } else {
            this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
            this.isInfoPasien = false
            return
          }
          this.getHistoriPelayananPesesta1(e.response.peserta.noKartu)

          this.isInfoPasien = true
          // this.alertService.info('Status Peserta', e.response.rujukan.peserta.statusPeserta.keterangan);
          if (e.response.peserta.statusPeserta.kode == 6) {
            this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
            this.isInfoPasien = false
            return
          }
        } else {
          this.isInfoPasien = false
          // this.alertService.error('Error', e.metaData.message);
          this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
          return

        }
      }, error => {
        this.isInfoPasien = false
        this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', error);
        return
      })
    } else if (this.formGroup.get('pCare').value == 'kontrol') {
      jenis = 'pcare'
      this.showPasca = false;
      this.httpService.get("bridging/bpjs/get-no-peserta?nokartu=" + noKa + "&tglsep=" + moment(new Date()).format('YYYY-MM-DD')).subscribe(e => {
        if (e.metaData.code === "200") {
          this.item1 = e.response.peserta;
          if (e.response.peserta.mr.noMR != null) {
            this.getPasienByNoCM(e.response.peserta.mr.noMR)

          } else {
            this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
            this.isInfoPasien = false
            return
          }
          this.getHistoriPelayananPesesta2(e.response.peserta.noKartu)

          this.isInfoPasien = true
          // this.alertService.info('Status Peserta', e.response.rujukan.peserta.statusPeserta.keterangan);
          if (e.response.peserta.statusPeserta.kode == 6) {
            this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
            this.isInfoPasien = false
            return
          }
        } else {
          this.isInfoPasien = false
          // this.alertService.error('Error', e.metaData.message);
          this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
          return

        }
      }, error => {
        this.isInfoPasien = false
        this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', error);
        return
      })
    } else {
     
      this.httpService.get("bridging/bpjs/get-rujukan-" + jenis + "-nokartu?nokartu=" + noKa).subscribe(e => {
        if (e.metaData.code === "200") {
          this.item = e.response.rujukan;
          if (e.response.rujukan.peserta.mr.noMR != null) {
            this.getPasienByNoCM(e.response.rujukan.peserta.mr.noMR)
            for (var i = this.listPoliTemp.length - 1; i >= 0; i--) {
              if (this.listPoliTemp[i].kditernal == this.item.poliRujukan.kode) {
                this.getRuanganInternal(this.item.poliRujukan.kode)
                break
              } else {
                this.getRuanganInternal(this.item.poliRujukan.kode)
                break
              }
            }

            this.getDiagnosaByKode(this.item.diagnosa.kode)
            // get Dokter DPJP By Histori
            this.getHistoriPelayananPesesta(e.response.rujukan.peserta.noKartu)
            this.getMultiRujukan(e.response.rujukan.peserta.noKartu)
            // end Histori

            if (e.response.rujukan.peserta.statusPeserta.kode == 6) {
              this.isInfoPasien = false
              this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.response.rujukan.peserta.statusPeserta.keterangan);
              return
            } else {
              this.isInfoPasien = true
              this.alertService.info('Status Peserta', e.response.rujukan.peserta.statusPeserta.keterangan);

            }

          }
        } else {
          this.isInfoPasien = false
          this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
        }
      }, error => {
        this.isInfoPasien = false
        this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', JSON.stringify(error));
      })
    }


  }

  cekPesertaByNoRujukan(noKa) {
    this.showPasca = true
    this.formGroup.get('tujuanKunj').setValue(this.listTujuanKun[1].value)
    this.formGroup.get('noSKDP').setValue('')
    this.formGroup.get('flagProcedure').setValue('')
    this.formGroup.get('kdPenunjang').setValue('')
    this.formGroup.get('assesmentPel').setValue('')
    let jenis = "rs"
    if (this.formGroup.get('pCare').value == 'pcare')
      jenis = this.formGroup.get('pCare').value
    this.httpService.get('bridging/bpjs/get-rujukan-' + jenis + '?norujukan=' + noKa).subscribe(e => {
      if (e.metaData.code === "200") {
        this.item = e.response.rujukan;
        if (e.response.rujukan.peserta.mr.noMR != null)
          this.getPasienByNoCM(e.response.rujukan.peserta.mr.noMR)
        for (var i = this.listPoliTemp.length - 1; i >= 0; i--) {
          if (this.listPoliTemp[i].kditernal == this.item.poliRujukan.kode) {
            this.getRuanganInternal(this.item.poliRujukan.kode)
            break
          } else {
            this.getRuanganInternal(this.item.poliRujukan.kode)
            break
          }
        }
        // this.getRuanganInternal(this.item.poliRujukan.kode)
        this.getDiagnosaByKode(this.item.diagnosa.kode)
        this.isInfoPasien = true
        // var tglLahir = new Date(e.response.rujukan.peserta.tglLahir);
        // $scope.model.tglRujukan = new Date(e.response.rujukan.tglKunjungan)
        // $scope.model.noKepesertaan = $scope.noKartu = e.response.rujukan.peserta.noKartu;
        // $scope.model.namaPeserta = e.response.rujukan.peserta.nama;
        // $scope.model.tglLahir = tglLahir;
        // $scope.model.noIdentitas = e.response.rujukan.peserta.nik;
        // $scope.model.kelasBridg = {
        //   id: parseInt(e.response.rujukan.peserta.hakKelas.kode),
        //   kdKelas: e.response.rujukan.peserta.hakKelas.kode,
        //   nmKelas: e.response.rujukan.peserta.hakKelas.keterangan,
        //   namakelas: e.response.rujukan.peserta.hakKelas.keterangan,
        // };
        // if ($scope.model.kelasBridg.id == 1) {
        //   $scope.model.kelasDitanggung = { id: 3, namakelas: 'Kelas I' }
        // } else if ($scope.model.kelasBridg.id == 2) {
        //   $scope.model.kelasDitanggung = { id: 2, namakelas: 'Kelas II' }
        // } else {
        //   $scope.model.kelasDitanggung = { id: 1, namakelas: 'Kelas III' }
        // }

        // $scope.kodeProvider = e.response.rujukan.provPerujuk.kode;
        // $scope.namaProvider = e.response.rujukan.provPerujuk.nama;
        // $scope.model.namaAsalRujukan = $scope.kodeProvider + " - " + $scope.namaProvider;
        // $scope.model.jenisPeserta = e.response.rujukan.peserta.jenisPeserta.keterangan;
        // this.httpService.get("registrasipasien/get-diagnosa-saeutik?kddiagnosa=" + e.response.rujukan.diagnosa.kode)
        //   .subscribe(res => {
        //     $scope.sourceDiagnosa.add(res[0])
        //     $scope.model.diagnosa = res[0]
        //   })

        // get Dokter DPJP By Histori
        this.getHistoriPelayananPesesta(e.response.rujukan.peserta.noKartu)
        // end Histori
        this.alertService.info('Status Peserta', e.response.rujukan.peserta.statusPeserta.keterangan);
        if (e.response.rujukan.peserta.statusPeserta.kode == 6) {
          this.isInfoPasien = false
          this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.response.rujukan.peserta.statusPeserta.keterangan);
          return
        }
        this.alertService.info('Status Peserta', e.response.rujukan.peserta.statusPeserta.keterangan);
      } else {
        this.isInfoPasien = false
        this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', e.metaData.message);
      }

    }, error => {
      this.isInfoPasien = false
      this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', error);
    })
  }
  getDiagnosaByKode(kode) {
    this.httpService.get('kiosk/get-diagnosabykode/' + kode).subscribe(res => {
      this.item.diagnosa.id = res.data.id
      // this.diagnosa = res.data
    })
  }
  getRuanganInternal(kode: any) {
    this.httpService.get('kiosk/get-ruanganbykode/' + kode).subscribe(res => {
      if (res.data != null) {
        this.listPoli = [];
        this.buttonss = [];
        this.listPoli.push({ label: '-- Poli Tujuan --', value: null });

        this.listPoli.push({
          label: res.data.namaruangan, value: {
            'kode': res.data.kdinternal,
            'nama': res.data.namaruangan,
            'kodeinternal': res.data.id,
            'objectdepartemenfk': res.data.objectdepartemenfk,
            'iseksekutif': res.data.iseksekutif,
          }
        });
        this.buttonss.push({
          label: res.data.namaruangan, value: {
            'kode': res.data.kdinternal,
            'nama': res.data.namaruangan,
            'kodeinternal': res.data.id,
            'objectdepartemenfk': res.data.objectdepartemenfk,
            'iseksekutif': res.data.iseksekutif,
          }
        });
        this.listPoliTemp = this.listPoli

        this.formGroup.get('poliTujuan').setValue({
          'kode': res.data.kdinternal,
          'nama': res.data.namaruangan,
          'kodeinternal': res.data.id,
          'objectdepartemenfk': res.data.objectdepartemenfk,
          'iseksekutif': res.data.iseksekutif,
        })

        this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 2
          + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + kode).subscribe(data => {
            if (data.metaData.code == 200) {
              this.listDokter = [];
              this.listDokter.push({ label: '-- Dokter --', value: null });
              data.response.list.forEach(response => {
                this.listDokter.push({
                  label: response.nama, value: {
                    'kode': response.kode,
                    'nama': response.nama
                  }
                });
              });
              this.listDokterMelayani = [];
              this.buttons = [];
              this.listDokterMelayani.push({ label: '-- Dokter Melayani --', value: null });
              data.response.list.forEach(response => {
                this.listDokterMelayani.push({
                  label: response.nama, value: {
                    'kode': response.kode,
                    'nama': response.nama
                  }
                });
                this.buttons.push({
                  label: response.nama, value: {
                    'kode': response.kode,
                    'nama': response.nama
                  }
                });
              });
            } else {
              this.listDokter = []
              this.listDokterMelayani = [];
              this.buttons = [];
              this.listDokter = []
              this.alertService.info('Dokter DPJP tidak ada', 'Info')
            }

          });
        this.pasienDaftar.idruangan = res.data.id
        this.pasienDaftar.objectdepartemenfk = res.data.objectdepartemenfk
      }

    })
  }
  getPasienByNoCM(nocm) {
    this.httpService.get('reservasionline/get-pasien/' + nocm + '/null').subscribe(e => {
      if (e.data.length > 0) {
        this.pasien = e.data[0]
      } else {
        this.alertService.error('SILAHKAN MENGAMBIL NO ANTRIAN', '');
        this.isInfoPasien = false
        return
      }
    })
  }
  getHistoriPelayananPesesta(noKartu) {
    // debugger
    let jenisPel = ''
    let kdSpesialis = ''
    this.httpService.get("bridging/bpjs/monitoring/HistoriPelayanan/NoKartu/" + noKartu).subscribe(data => {
      if (data.metaData.code == 200) {
        this.listHistori = data.response.histori;
        if (this.listHistori.length > 0) {
          jenisPel = this.listHistori[0].jnsPelayanan
          let kodeNamaPoli = this.listHistori[0].poli.split(' ');
          if (kodeNamaPoli.length > 0)
            this.httpService.get("bridging/bpjs/get-poli?kodeNamaPoli=" + kodeNamaPoli[0]).subscribe(e => {
              if (e.metaData.code == 200) {
                let resPoli = e.response.poli;
                if (resPoli.length > 0) {
                  for (let i in resPoli) {
                    if (this.listHistori[0].poli == resPoli[i].nama) {
                      kdSpesialis = resPoli[i].kode
                      break
                    }
                  }

                  this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + jenisPel
                    + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + kdSpesialis).subscribe(data => {
                      if (data.metaData.code == 200) {
                        this.listDokter = [];
                        this.listDokter.push({ label: '-- Dokter --', value: null });
                        data.response.list.forEach(response => {
                          this.listDokter.push({
                            label: response.nama, value: {
                              'kode': response.kode,
                              'nama': response.nama
                            }
                          });
                        });
                        this.formGroup.get('dokterDPJP').setValue({ kode: this.listDokter[1].value.kode, nama: this.listDokter[1].value.nama })
                      } else {
                        if (data.metaData.code == 201 && kdSpesialis == "HDL") {
                          this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + jenisPel
                            + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + "INT").subscribe(data => {
                              if (data.metaData.code == 200) {
                                this.listDokter = [];
                                this.listDokter.push({ label: '-- Dokter Perujuk --', value: null });
                                data.response.list.forEach(response => {
                                  this.listDokter.push({
                                    label: response.nama, value: {
                                      'kode': response.kode,
                                      'nama': response.nama
                                    }
                                  });
                                });
                                this.listDokterMelayani = [];
                                this.buttons = [];
                                this.listDokterMelayani.push({ label: '-- Dokter Melayani --', value: null });
                                data.response.list.forEach(response => {
                                  this.listDokterMelayani.push({
                                    label: response.nama, value: {
                                      'kode': response.kode,
                                      'nama': response.nama
                                    }
                                  });
                                  this.buttons.push({
                                    label: response.nama, value: {
                                      'kode': response.kode,
                                      'nama': response.nama
                                    }
                                  });
                                });

                              }
                            });
                        } else if (data.metaData.code == 201) {
                          this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
                            + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + "INT").subscribe(data => {
                              if (data.metaData.code == 200) {
                                this.listDokter = [];
                                this.listDokter.push({ label: '-- Dokter Perujuk --', value: null });
                                data.response.list.forEach(response => {
                                  this.listDokter.push({
                                    label: response.nama, value: {
                                      'kode': response.kode,
                                      'nama': response.nama
                                    }
                                  });
                                });
                                this.listDokterMelayani = [];
                                this.buttons = [];
                                this.listDokterMelayani.push({ label: '-- Dokter Melayani --', value: null });
                                data.response.list.forEach(response => {
                                  this.listDokterMelayani.push({
                                    label: response.nama, value: {
                                      'kode': response.kode,
                                      'nama': response.nama
                                    }
                                  });
                                  this.buttons.push({
                                    label: response.nama, value: {
                                      'kode': response.kode,
                                      'nama': response.nama
                                    }
                                  });
                                });

                              }
                            });
                        }
                        this.listDokter = []
                        this.listDokterMelayani = [];
                        this.buttons = [];
                        this.alertService.info('Dokter DPJP tidak ada', 'Info')
                      }

                    });
                }
              }

            });
        }
      }
      else {
        this.listDokter = [];
        this.listDokterMelayani = [];
        // this.listDokter.push({ label: '--Pilih Dokter --', value: null });
        this.listHistori = []

      }
    });
  }

  insertSep() {
    if (this.isTemporaryBrigding == 'true') {
      this.insertTempBrigding()
    } else {
      /*
      * cek kuota poli
      */
      if (this.formGroup.get('poliTujuan').value == '') {
        this.alertService.warn('Peringatan', 'Pilih Poli dahulu')
        return
      }
      if (this.item.peserta.mr.noTelepon == null) {
        this.item.peserta.mr.noTelepon = '12345678'
      } else if (this.item.peserta.mr.noTelepon.length > 12) {
        this.item.peserta.mr.noTelepon = '12345678'
      }

      if (this.formGroup.get('poliTujuan').value != '') {
        // if (this.formGroup.get('poliTujuan').value.kode != "HDL") {
        //   this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 2
        //     + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + this.formGroup.get('poliTujuan').value.kode).subscribe(data => {
        //       if (data.metaData.code == 200) {
        //         this.httpService.get('kiosk/get-slotting-kosong?ruanganfk=' + this.formGroup.get('poliTujuan').value.kodeinternal).subscribe(es => {
        //           if (es.status == true) {
        //             this.insertLiveBridging()
        //           } else {
        //             this.alertService.info('Info', es.status)
        //             return
        //           }
        //         })
        //       } else {
        //         this.alertService.info('SIP Dokter DPJP POLI TUJUAN HABIS', 'Info')
        //         return
        //       }
        //     });
        // } else {
        // this.httpService.get('kiosk/get-slotting-kosong?ruanganfk=' + this.formGroup.get('poliTujuan').value.kodeinternal).subscribe(es => {
        //   if (es.status == true) {
            this.insertLiveBridging()
          // } else {
            // this.alertService.info('Info', es.status)
            // return
          // }
        // })
        // }
      }

      // this.getRuanganInternal(this.formGroup.get('poliTujuan').value.kode)
      // this.insertLiveBridging()
    }
  }
  getMultiRujukan(noKartu) {
    if (this.formGroup.get('pCare').value == '') return
    let jenisfaskes = "rs"
    if (this.formGroup.get('pCare').value == 'pcare')
      jenisfaskes = "pcare"
    this.httpService.get("bridging/bpjs/get-rujukan-" + jenisfaskes + "-nokartu-multi?nokartu=" + noKartu).subscribe(data => {
      if (data.metaData.code == 200) {
        this.listRujukan = [];

        this.listRujukan.push({ label: "--Histori No Rujukan--", value: null });
        this.listRujukan.push({ label: data.response.rujukan[0].noKunjungan, value: data.response.rujukan[0].diagnosa.kode });
        this.listRujukan.push({ label: data.response.rujukan[1].noKunjungan, value: data.response.rujukan[1].diagnosa.kode });
        this.listRujukan.push({ label: data.response.rujukan[2].noKunjungan, value: data.response.rujukan[2].diagnosa.kode });

      } else {
        this.listRujukan = [];
        this.listRujukan.push({ label: "--Histori No Rujukan--", value: null });
      }
    })
  }
  getHistoriPelayananPesesta1(noKartu) {
    var status = false

  this.httpService.get("bridging/bpjs/monitoring/HistoriPelayanan/NoKartu/" + noKartu).subscribe(data => {
      if (data.metaData.code == 200) {
        this.listHistori = data.response.histori;
        if (this.listHistori.length > 0) {
          for (let i = 0; i < this.listHistori.length; i++) {
            if (this.listHistori[i].jnsPelayanan == "1") {
              status = true
              this.noPascaRanap = this.listHistori[i].noSep
              var a = {
                'nama': "RSUD PAGELARAN",
                'kode': this.ppkPelayananRS
              }
              var b = {
                'kode': this.listHistori[i].diagnosa.split(' - ')[0],
                'nama': this.listHistori[i].diagnosa.split(' - ')[1]
              }

              this.getDiagnosaByKode(this.listHistori[i].diagnosa.split(' - ')[0])

              var c = {
                'kode': "2"
              }
              var d = {
                'kode': "Pasca",
                'nama': "Pasca Ranap"
              }
              this.item = {
                'noKunjungan': this.noPascaRanap,
                'peserta': this.item1,
                'tglKunjungan': moment(new Date()).format('YYYY-MM-DD'),
                'provPerujuk': a,
                'diagnosa': b,
                'pelayanan': c,
                'poliRujukan': d,
              }
              this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
                + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + "").subscribe(data => {
                  if (data.metaData.code == 200) {
                    this.listDokter = [];
                    // this.listDokter.push({ label: '-- Dokter Perujuk --', value: null });
                    data.response.list.forEach(response => {
                      this.listDokter.push({
                        label: response.nama, value: {
                          'kode': response.kode,
                          'nama': response.nama
                        }
                      });
                    });
                    this.formGroup.get('dokterDPJP').setValue({ kode: this.listDokter[0].value.kode, nama: this.listDokter[0].value.nama })
                    this.listDokterMelayani = [];
                    this.buttons = [];
                    this.listDokterMelayani.push({ label: '-- Dokter Melayani --', value: null });
                    data.response.list.forEach(response => {
                      this.listDokterMelayani.push({
                        label: response.nama, value: {
                          'kode': response.kode,
                          'nama': response.nama
                        }
                      });
                      this.buttons.push({
                        label: response.nama, value: {
                          'kode': response.kode,
                          'nama': response.nama
                        }
                      });
                    });

                  }
                });
                break
            }
         
          }
        }

        if (status == false) {

        }
      }
    })
  }
  getHistoriPelayananPesesta2(noKartu) {


    var status = false
    this.httpService.get("bridging/bpjs/monitoring/HistoriPelayanan/NoKartu/" + noKartu).subscribe(data => {
      if (data.metaData.code == 200) {
        this.listHistori = data.response.histori;
        if (this.listHistori.length > 0) {
          for (let i = 0; i < this.listHistori.length; i++) {

            if (this.listHistori[i].jnsPelayanan == "2") {
              status = true
              this.noPascaRanap = this.listHistori[i].noRujukan
              var a = {
                'asalRujukan': "2",
                'nama': "RSUD PAGELARAN",
                'kode': this.listHistori[i].noRujukan.substring(0, 8)
              }
              var b = {
                'kode': this.listHistori[i].diagnosa.split(' - ')[0],
                'nama': this.listHistori[i].diagnosa.split(' - ')[1]
              }

              this.getDiagnosaByKode(this.listHistori[i].diagnosa.split(' - ')[0])

              var c = {
                'kode': "2"
              }
              var d = {
                'kode': "kontrol",
                'nama': "Kontrol Ulang"
              }
              // "rujukan": {
              //   "asalRujukan": asalRujukan,//RS
              //   "tglRujukan": this.item.tglKunjungan,
              //   "noRujukan": this.item.noKunjungan,
              //   "ppkRujukan": this.item.provPerujuk.kode
              // },
              this.item = {
                'noKunjungan': this.listHistori[i].noRujukan,
                'peserta': this.item1,
                'tglKunjungan': moment(new Date()).format('YYYY-MM-DD'),
                'provPerujuk': a,
                'diagnosa': b,
                'pelayanan': c,
                'poliRujukan': d,
              }
              this.getNoSurat(this.listHistori[i].noSep)
              this.httpService.get("bridging/bpjs/get-rujukan-pcare-nokartu?nokartu=" + noKartu).subscribe(e => {
                if (e.metaData.code === "200") {
                  this.item.tglKunjungan = e.response.rujukan.tglKunjungan
                  this.item.noKunjungan = e.response.rujukan.noKunjungan
                  this.item.provPerujuk = {
                    'asalRujukan': "1",
                    'nama': e.response.rujukan.provPerujuk.nama,
                    'kode': e.response.rujukan.provPerujuk.kode
                  }
                  // "rujukan": {
                  //   "asalRujukan": asalRujukan,//RS
                  //   "tglRujukan": this.item.tglKunjungan,
                  //   "noRujukan": this.item.noKunjungan,
                  //   "ppkRujukan": this.item.provPerujuk.kode
                  // },
                }
              })
              this.formGroup.get('tujuanKunj').setValue(this.listTujuanKun[3].value)
              break
              // this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
              //   + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + "").subscribe(data => {
              //     if (data.metaData.code == 200) {
              //       this.listDokter = [];
              //       // this.listDokter.push({ label: '-- Dokter Perujuk --', value: null });
              //       data.response.list.forEach(response => {
              //         this.listDokter.push({
              //           label: response.nama, value: {
              //             'kode': response.kode,
              //             'nama': response.nama
              //           }
              //         });
              //       });
              //       this.formGroup.get('dokterDPJP').setValue({ kode: this.listDokter[0].value.kode, nama: this.listDokter[0].value.nama })
              //       this.listDokterMelayani = [];
              //       this.buttons = [];
              //       this.listDokterMelayani.push({ label: '-- Dokter Melayani --', value: null });
              //       data.response.list.forEach(response => {
              //         this.listDokterMelayani.push({
              //           label: response.nama, value: {
              //             'kode': response.kode,
              //             'nama': response.nama
              //           }
              //         });
              //         this.buttons.push({
              //           label: response.nama, value: {
              //             'kode': response.kode,
              //             'nama': response.nama
              //           }
              //         });
              //       });

              //     }
              //   });
            }
          }
        }

        if (status == false) {

        }
      }
    })
  }
  getNoSurat(nosep) {
    var now = moment(new Date()).format('YYYY-MM-DD')
    var dataSend = {
      "url": "RencanaKontrol/ListRencanaKontrol/tglAwal/" + now + "/tglAkhir/" + now + "/filter/2",
      "method": "GET",
      "data": null
    }
    var status = false
    this.httpService.postNonMessage("bridging/bpjs/tools", dataSend).subscribe(e => {
      if (e.metaData.code == 200) {
        if (e.response.list != null && e.response.list.length > 0) {

          for (let x = 0; x < e.response.list.length; x++) {
            const element = e.response.list[x];
            if (element.noSepAsalKontrol == nosep) {
              status = true
              this.alertService.success('INFO', 'No Surat Kontrol Terisi Otomatis');
              this.formGroup.get('noSKDP').setValue(e.response.list[x].noSuratKontrol)
              this.formGroup.get('dokterDPJP').setValue({ kode: e.response.list[x].kodeDokter, nama: e.response.list[x].namaDokter })
              this.formGroup.get('assesmentPel').setValue(this.listAsses[1].value)
              this.listDokter = [];
              this.listDokter.push({ label: '-- Dokter --', value: null });

              this.listDokter.push({
                label: e.response.list[x].namaDokter, value: {
                  'kode': e.response.list[x].kodeDokter,
                  'nama': e.response.list[x].namaDokter
                }
              });

              this.formGroup.get('dokterDPJPMelayani').setValue({ kode: e.response.list[x].kodeDokter, nama: e.response.list[x].namaDokter })
              this.tests = e.response.list[x].namaPoliTujuan
              this.test = e.response.list[x].namaDokter
              this.httpService.get('kiosk/get-ruanganbykode/' + e.response.list[x].poliTujuan).subscribe(res => {
                if (res.data != null) {
                  this.formGroup.get('poliTujuan').setValue({
                    'kode': res.data.kdinternal,
                    'nama': res.data.namaruangan,
                    'kodeinternal': res.data.id,
                    'objectdepartemenfk': res.data.objectdepartemenfk,
                    'iseksekutif': res.data.iseksekutif,
                  })
                  this.pasienDaftar.idruangan = res.data.id
                  this.pasienDaftar.objectdepartemenfk = res.data.objectdepartemenfk
                }
              })

              this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 2
                + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + e.response.list[x].poliTujuan).subscribe(data => {
                  if (data.metaData.code == 200) {
                    this.buttons = [];
                    data.response.list.forEach(response => {
                      this.buttons.push({
                        label: response.nama, value: {
                          'kode': response.kode,
                          'nama': response.nama
                        }
                      });
                    });
                  } else {

                    this.buttons = [];
                  }

                });
              break
            }
          }


        } else {
          this.alertService.error('INFO', 'Surat Kontrol Belum Ada');
        }

      } else {
        this.alertService.error('INFO', e.metaData.message);
      }
      if (status == false) {

        this.alertService.error('SURAT KONTROL BELUM DI BUATKAN', 'INFO');
        this.isInfoPasien = false
      }


    })
  }
  insertLiveBridging() {
    let asalRujukan = "2"
    if (this.formGroup.get('pCare').value != null) {
      if (this.formGroup.get('pCare').value == 'pcare') {
        asalRujukan = "1"
      } else if (this.formGroup.get('pCare').value == 'pasca') {
        this.formGroup.get('dokterDPJP').setValue(this.formGroup.get("dokterDPJPMelayani").value)
        asalRujukan = "2"
      } else if (this.formGroup.get('pCare').value == 'kontrol') {

        asalRujukan = this.item.provPerujuk.asalRujukan ? this.item.provPerujuk.asalRujukan : "2"
      } else {
        asalRujukan = "2"
      }
    }

    let poliTujuan = ""
    if (this.formGroup.get('poliTujuan').value != '')
      poliTujuan = this.formGroup.get('poliTujuan').value.kode

    let eksekutif = "0"
    this.eksekutif = 1
    if (this.formGroup.get('poliTujuan').value != ''
      && this.formGroup.get('poliTujuan').value.iseksekutif != undefined
      && this.formGroup.get('poliTujuan').value.iseksekutif == true) {
      eksekutif = "1"
      this.eksekutif = 2
    }
    var dataSend = {
      "url": "SEP/2.0/insert",
      "method": "POST",
      "data": {
        "request": {
          "t_sep": {
            "noKartu": this.item.peserta.noKartu,
            "tglSep": moment(new Date()).format('YYYY-MM-DD'),
            "ppkPelayanan": this.ppkPelayananRS,
            "jnsPelayanan": "2",
            "klsRawat": {
              "klsRawatHak": this.item.peserta.hakKelas.kode.toString(),
              "klsRawatNaik": "",
              "pembiayaan": "",
              "penanggungJawab": ""
            },
            "noMR": this.item.peserta.mr.noMR,
            "rujukan": {
              "asalRujukan": asalRujukan,//RS
              "tglRujukan": this.item.tglKunjungan,
              "noRujukan": this.item.noKunjungan,
              "ppkRujukan": this.item.provPerujuk.kode
            },
            "catatan": "REGISTRASI MANDIRI",
            "diagAwal": this.item.diagnosa.kode,
            "poli": {
              "tujuan": poliTujuan,
              "eksekutif": eksekutif
            },
            "cob": {
              "cob": "0"
            },
            "katarak": {
              "katarak": "0"
            },
            "jaminan": {
              "lakaLantas": "0",
              "penjamin": {
                "tglKejadian": "",
                "keterangan": "",
                "suplesi": {
                  "suplesi": "0",
                  "noSepSuplesi": "",
                  "lokasiLaka": {
                    "kdPropinsi": "",
                    "kdKabupaten": "",
                    "kdKecamatan": ""
                  }
                }
              }
            },
            "tujuanKunj": this.formGroup.get("tujuanKunj").value != '' && this.formGroup.get("tujuanKunj").value != null ? this.formGroup.get("tujuanKunj").value.kode : "",
            "flagProcedure": this.formGroup.get("flagProcedure").value != '' && this.formGroup.get("flagProcedure").value != null ? this.formGroup.get("flagProcedure").value.kode : "",
            "kdPenunjang": this.formGroup.get("kdPenunjang").value != '' && this.formGroup.get("kdPenunjang").value != null ? this.formGroup.get("kdPenunjang").value.kode : "",
            "assesmentPel": this.formGroup.get("assesmentPel").value != '' && this.formGroup.get("assesmentPel").value != null ? this.formGroup.get("assesmentPel").value.kode : "",
            "skdp": {
              "noSurat": this.formGroup.get("noSKDP").value != '' && this.formGroup.get("noSKDP").value != null ? this.formGroup.get("noSKDP").value : "",
              "kodeDPJP": this.formGroup.get("dokterDPJP").value != '' && this.formGroup.get("dokterDPJP").value != null ? this.formGroup.get("dokterDPJP").value.kode : ""
            },
            "dpjpLayan": this.formGroup.get("dokterDPJPMelayani").value != '' && this.formGroup.get("dokterDPJPMelayani").value != null ? this.formGroup.get("dokterDPJPMelayani").value.kode : "",
            "noTelp": this.item.peserta.mr.noTelepon,
            "user": "Xoxo"
          }
        }
      }
    }
    // var dataSend = {
    //   "data": {
    //     "request": {
    //       "t_sep": {
    //         "noKartu": this.item.peserta.noKartu,
    //         "tglSep": moment(new Date()).format('YYYY-MM-DD'),
    //         "ppkPelayanan": this.ppkPelayananRS,
    //         "jnsPelayanan": "2",
    //         "klsRawat": this.item.peserta.hakKelas.kode.toString(),
    //         "noMR": this.item.peserta.mr.noMR,
    //         "rujukan": {
    //           "asalRujukan": asalRujukan,//RS
    //           "tglRujukan": this.item.tglKunjungan,
    //           "noRujukan": this.item.noKunjungan,
    //           "ppkRujukan": this.item.provPerujuk.kode
    //         },
    //         "catatan": "REGISTRASI MANDIRI",
    //         "diagAwal": this.item.diagnosa.kode,
    //         "poli": {
    //           "tujuan": poliTujuan,
    //           "eksekutif": eksekutif
    //         },
    //         "cob": {
    //           "cob": "0"
    //         },
    //         "katarak": {
    //           "katarak": "0"
    //         },
    //         "jaminan": {
    //           "lakaLantas": "0",
    //           "penjamin": {
    //             "penjamin": "",
    //             "tglKejadian": "",
    //             "keterangan": "",
    //             "suplesi": {
    //               "suplesi": "0",
    //               "noSepSuplesi": "",
    //               "lokasiLaka": {
    //                 "kdPropinsi": "",
    //                 "kdKabupaten": "",
    //                 "kdKecamatan": ""
    //               }
    //             }
    //           }
    //         },
    //         "skdp": {
    //           "noSurat": this.noSKDP,
    //           "kodeDPJP": this.formGroup.get("dokterDPJP").value != '' ? this.formGroup.get("dokterDPJP").value.kode : ""
    //         },
    //         "noTelp": this.item.peserta.mr.noTelepon,
    //         "user": "Ramdanegie"
    //       }
    //     }
    //   }
    // };
    this.httpService.postNonMessage("bridging/bpjs/tools", dataSend).subscribe(e => {
      // this.httpService.post("bridging/bpjs/insert-sep-v1.1", dataSend).subscribe(e => {
      if (e.response != null) {
        this.dataSEP.nosep = e.response.sep.noSep
        this.dataSEP.tglSep = e.response.sep.tglSep

        this.alertService.success('Status', 'Generate SEP Success. No SEP : ' + e.response.sep.noSep);
        this.savePasienDatfatar();

      } else {
        this.alertService.error('Gagal Generate SEP', e.metaData.message);
        console.log(dataSend)
      }

    }, err => {
      this.alertService.error('Gagal Generate SEP', JSON.stringify(err));
    });
  }
  public getJSON(jenis): Observable<any> {
    return this.service.get<any>("assets/jsonbridging/bpjs/" + jenis + ".json")
      .pipe(
        tap((res: any) => {
          return res
        }),

      )
  };
  // }
  //   public getJSON(jenis): Observable<any> {
  //     return this.service.get("assets/jsonbridging/bpjs/" + jenis + ".json")
  //       .map(response => response.json())
  //       .catch(e => { console.log(e); return Observable.throw(e); })

  // }

  insertTempBrigding() {
    this.getJSON('sep').subscribe(e => {
      let res = e.response
      for (let i = 0; i < res.length; i++) {
        const element = res[i];
        var nomor = ''
        if (this.formGroup.get('noRujukan').value.length <= 14 && this.formGroup.get('noRujukan').value.length > 6) {
          nomor = this.formGroup.get('noRujukan').value
          if (this.formGroup.get('noRujukan').value.toString().length == 6)
            nomor = this.item.peserta.noKartu
        } else {
          nomor = this.item.peserta.noKartu
        }
        if (element.sep.peserta.noKartu == nomor) {
          this.httpService.get('bridging/bpjs/generate-sep-dummy?kodeppk=' + this.ppkPelayananRS).subscribe(es => {
            this.dataSEP.nosep = es
            // this.dataSEP.nosep = element.sep.noSep
            this.dataSEP.tglSep = moment(new Date()).format('YYYY-MM-DD')
            this.alertService.success('Status', 'Generate SEP Success. No SEP : ' + this.dataSEP.nosep);
            this.showCetak = true
            this.savePasienDatfatar();

          })
          break
        }
      }
    });
  }
  savePasienDatfatar() {
    this.kodeDokter = null
    this.httpService.get('kiosk/get-dokter-internal?kode=' +
      this.formGroup.get("dokterDPJP").value.kode).subscribe(e => {
        if (e != false) {
          this.kodeDokter = e.id
        }
        if (this.pasien.nocmfk == undefined) {
          this.isStatusPasien = 'BARU'
          // this.savePasien()
          this.alertService.error('SILAHKAN AMBIL NO ANTRIAN', 'PASIEN BARU');
          return
        } else {
          this.isStatusPasien = 'LAMA'
          this.savePasienDaftarFix()
        }
      })
  }
  lanjutSave() {

  }
  savePasienDaftarFix() {
    if (this.eksekutif == '0') {

    }
    var pasiendaftar = {
      'tglregistrasi': moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
      'tglregistrasidate': moment(new Date()).format('YYYY-MM-DD'),
      'nocmfk': this.pasien.nocmfk,
      'objectruanganfk': this.formGroup.get('poliTujuan').value.kodeinternal, //this.pasienDaftar.idruangan,
      'objectdepartemenfk': this.formGroup.get('poliTujuan').value.objectdepartemenfk, //this.pasienDaftar.objectdepartemenfk,
      'objectkelasfk': 6,//nonkelas
      'objectkelompokpasienlastfk': 2,//umum
      'objectrekananfk': 1,//bpjs
      'tipelayanan': 1,//this.eksekutif != undefined ? this.eksekutif : 1,//reguler
      'objectpegawaifk': this.kodeDokter,
      'noregistrasi': '',
      'norec_pd': '',
      'israwatinap': 'false',
      'objectruanganasalfk': this.formGroup.get('poliTujuan').value.kodeinternal,
      'statusschedule': this.noReservasi != undefined ? this.noReservasi : 'Kios-K',
      'statuspasien': 'LAMA',
    }
    var antrianpasiendiperiksa = {
      'norec_apd': '',
      'tglregistrasi': moment(new Date()).format('YYYY-MM-DD HH:mm:ss'),
      'objectruanganfk': this.formGroup.get('poliTujuan').value.kodeinternal, //this.pasienDaftar.idruangan,
      'objectkelasfk': 6,//nonkelas
      'objectpegawaifk': this.kodeDokter,
      'objectkamarfk': null,
      'nobed': null,
      'objectdepartemenfk': this.formGroup.get('poliTujuan').value.objectdepartemenfk, //this.pasienDaftar.objectdepartemenfk,
      'objectasalrujukanfk': 2,//Datang Sendiri
      'israwatgabung': 0,
    }
    var objSave = {
      'pasiendaftar': pasiendaftar,
      'antrianpasiendiperiksa': antrianpasiendiperiksa
    }

    this.httpService.postNonMessage('registrasi/save-registrasipasien', objSave).subscribe(response => {
      this.pasienDaftar.noregistrasi = response.dataPD.noregistrasi
      this.pasienDaftar.norec_pd = response.dataPD.norec
      this.pasienDaftar.tglregistrasi = response.dataPD.tglregistrasi
      this.pasienDaftar.norec_apd = response.dataAPD.norec


      this.saveLogging('Pendaftaran Pasien', 'norec Pasien Daftar', response.dataPD.norec,
        'Self Registration No Registrasi (' + response.dataPD.noregistrasi + ') ')
      this.simpanPemakaianAsuransi()
      this.updateStatusConfirm()
      if (this.isAdminOtomatisKiosk == 'true') {
        this.saveAdminAuto(this.pasienDaftar)
      }
    }, error => {

    })
  }
  saveAdminAuto(pd) {
    let json = {
      norec: pd.norec_pd,
      norec_apd: pd.norec_apd
    }
    this.httpService.postNonMessage("registrasi/save-adminsitrasi", json).subscribe(z => {

    })
  }
  updateStatusConfirm() {
    let data = {
      "noreservasi": this.noReservasi,
    }
    this.httpService.postNonMessage('reservasionline/update-data-status-reservasi', data).subscribe(e => {

    })
  }
  savePasien() {
    var postJson = {
      'isbayi': false,
      'isPenunjang': false,
      'idpasien': '',
      'pasien': {
        'namaPasien': this.item.peserta.nama,
        'noIdentitas': this.item.peserta.nik,
        'namaSuamiIstri': null,
        'noAsuransiLain': null,
        'noBpjs': this.item.peserta.noKartu,
        'noHp': this.item.peserta.mr.noTelepon,
        'tempatLahir': '',
        'namaKeluarga': null,
        'tglLahir': this.item.peserta.tglLahir
      },
      'agama': {
        'id': null,
      },
      'jenisKelamin': {
        'id': this.item.peserta.sex == 'L' ? 1 : 2,
      },
      'pekerjaan': {
        'id': null,
      },
      'pendidikan': {
        'id': null,
      },
      'statusPerkawinan': {
        'id': 0,
      },
      'namaIbu': null,
      'noTelepon': this.item.peserta.mr.noTelepon,
      'noAditional': null,
      'kebangsaan': {
        'id': 1,//WNI
      },
      'negara': {
        'id': 0,
      },
      'namaAyah': null,
      'alamatLengkap': '-',
      'desaKelurahan': {
        'id': null,
        'namaDesaKelurahan': null,
      },
      'kecamatan': {
        'id': null,
        'namaKecamatan': null,
      },
      'kotaKabupaten': {
        'id': null,
        'namaKotaKabupaten': null,
      },
      'propinsi': {
        'id': null,
      },
      'kodePos': null,
    }
    this.httpService.post('registrasi/save-pasien-fix', postJson)
      .subscribe(
        res => {
          let tempId = res.data.id;
          this.pasien.nocmfk = res.data.kodeexternal
          this.pasien.nocm = res.data.nocm
          this.pasien.objectjeniskelaminfk = res.data.objectjeniskelaminfk

          this.savePasienDaftarFix()
        }, error => {

        })
  }
  saveLogging(jenis, referensi, noreff, ket) {
    this.httpService.get("sysadmin/logging/save-log-all?jenislog=" + jenis
      + "&referensi=" + referensi
      + "&noreff=" + noreff
      + "&keterangan=" + ket
    ).subscribe(e => {

    })
  }


  simpanPemakaianAsuransi() {

    let kelas: any = ""
    if (this.item.peserta.hakKelas.kode == "1")
      kelas = 3
    else if (this.item.peserta.hakKelas.kode == "2")
      kelas = 2
    else if (this.item.peserta.hakKelas.kode == "3")
      kelas = 1

    let asuransipasien = {
      'id_ap': '',
      'noregistrasi': this.pasienDaftar.noregistrasi,
      'nocm': this.pasien.nocm,
      'alamatlengkap': '',
      'objecthubunganpesertafk': 1,//Peserta
      'objectjeniskelaminfk': this.pasien.objectjeniskelaminfk,
      'kdinstitusiasal': 1,
      'kdpenjaminpasien': 1,
      'objectkelasdijaminfk': kelas,
      'namapeserta': this.item.peserta.nama,
      'nikinstitusiasal': 1,
      'noasuransi': this.item.peserta.noKartu,
      'alamat': '',
      'nocmfkpasien': this.pasien.nocmfk,
      'noidentitas': this.item.peserta.nik,
      'qasuransi': 2,
      'kelompokpasien': 2,
      'tgllahir': moment(new Date(this.item.peserta.tglLahir)).format('YYYY-MM-DD'),
      'jenispeserta': this.item.peserta.jenisPeserta.keterangan,
      'kdprovider': this.item.provPerujuk.kode,
      'nmprovider': this.item.provPerujuk.nama,
      'notelpmobile': this.item.peserta.mr.noTelepon,
    }
    let asalrujukan = "2"
    if (this.formGroup.get('pCare').value != null) {
      if (this.formGroup.get('pCare').value == 'pcare') {
        asalrujukan = '1'
      } else {
        asalrujukan = '2'
      }
    }

    let pemakaianasuransi = {
      'norec_pa': '',
      'noregistrasifk': this.pasienDaftar.norec_pd,
      'tglregistrasi': moment(new Date(this.pasienDaftar.tglregistrasi)).format('YYYY-MM-DD HH:mm'),
      'diagnosisfk': this.item.diagnosa.id != null ? this.item.diagnosa.id : null,
      'lakalantas': 0,
      'nokepesertaan': this.item.peserta.noKartu,
      'norujukan': this.item.noKunjungan,
      'nosep': this.dataSEP.nosep,
      'tglrujukan': this.item.tglKunjungan,
      'objectdiagnosafk': this.item.diagnosa.id != null ? this.item.diagnosa.id : null,
      'tanggalsep': this.dataSEP.tglSep,
      'catatan': '',
      'lokasilaka': null,
      'penjaminlaka': null,
      'cob': false,
      'katarak': false,
      'keteranganlaka': "",
      'tglkejadian': null,
      'suplesi': false,
      'nosepsuplesi': "",
      'kdpropinsi': null,
      'namapropinsi': null,
      'kdkabupaten': null,
      'namakabupaten': null,
      'kdkecamatan': null,
      'namakecamatan': null,
      'nosuratskdp': this.formGroup.get("noSKDP").value != '' ? this.formGroup.get("noSKDP").value.kode : "",
      'kodedpjp': this.formGroup.get('dokterDPJP').value.kode,
      'namadpjp': this.formGroup.get('dokterDPJP').value.nama,
      'prolanisprb': null,
      'asalrujukanfk': asalrujukan,
      'kodedpjpmelayani': this.formGroup.get('dokterDPJPMelayani').value.kode,
      'namadjpjpmelayanni': this.formGroup.get('dokterDPJPMelayani').value.nama,
      'polirujukankode': this.formGroup.get('poliTujuan').value.kode,
      'polirujukannama': this.formGroup.get('poliTujuan').value.nama,
      'klsrawatnaik': null,
      'pembiayaan': null,
      'penanggungjawab': null,
      'tujuankunj': this.formGroup.get("tujuanKunj").value != '' && this.formGroup.get("tujuanKunj").value != null ? this.formGroup.get("tujuanKunj").value.kode : null,
      'flagprocedure': this.formGroup.get("flagProcedure").value != '' && this.formGroup.get("flagProcedure").value != null ? this.formGroup.get("flagProcedure").value.kode : null,
      'kdpenunjang': this.formGroup.get("kdPenunjang").value != '' && this.formGroup.get("kdPenunjang").value != null ? this.formGroup.get("kdPenunjang").value.kode : null,
      'assesmentpel': this.formGroup.get("assesmentPel").value != '' && this.formGroup.get("assesmentPel").value != null ? this.formGroup.get("assesmentPel").value.kode : null,
    }


    var objSave = {
      'asuransipasien': asuransipasien,
      'pemakaianasuransi': pemakaianasuransi
    }
    this.httpService.postNonMessage('registrasi/save-asuransipasien', objSave).subscribe(e => {
      this.showCetak = true
      this.isSuksesSEP = true
    })
  }
  cetakSep() {
    // this.service.get("http://127.0.0.1:3885/desk/routes?cetak-sep=1&noregistrasi=" +
    //   this.pasienDaftar.noregistrasi + "&qty=1&idpegawai=&ket=&view=true").subscribe(e => {
    //   });

    if (this.dataSEP.nosep != '') {
      var nosep = this.dataSEP.nosep
      var nmperujuk = this.item.provPerujuk.nama

      var tglsep = this.dataSEP.tglSep
      var nokartu = this.item.peserta.noKartu + '  ( MR. ' + this.item.peserta.mr.noMR + ' )';
      var nmpst = this.item.peserta.nama
      var tgllahir = this.item.peserta.tglLahir
      var jnskelamin = this.item.peserta.sex == 'L' ? '  Kelamin : Laki-Laki' : '  Kelamin :Perempuan';
      var poli = this.formGroup.get('poliTujuan').value.nama
      var faskesperujuk = nmperujuk;
      var notelp = this.item.peserta.mr.noTelepon
      var dxawal = (this.item.diagnosa.kode + ' - ' + this.item.diagnosa.nama).substring(0, 45);
      var catatan = '-'
      var jnspst = this.item.peserta.jenisPeserta.keterangan
      var FLAGCOB = 0
      var cob = '-';
      if (FLAGCOB) {
        cob = null
      }

      //cob non aktif
      var FLAGNAIKKELAS = 0
      var klsrawat_naik = ""

      var jnsrawat = 'R.Jalan';
      var klsrawat = this.item.peserta.hakKelas.keterangan
      var prolanis = ""
      var eksekutif = '';

      var katarak = '0';
      var potensiprb = ""
      var statuskll = ""

      var dokter = this.formGroup.get('dokterDPJPMelayani').value && this.formGroup.get('dokterDPJPMelayani').value != '' ? this.formGroup.get('dokterDPJPMelayani').value.nama : (this.formGroup.get('dokterDPJP').value && this.formGroup.get('dokterDPJP').value != '' ? this.formGroup.get('dokterDPJPMelayani').value.nama : '')
      var FLAGPROSEDUR = this.formGroup.get("flagProcedure").value != '' && this.formGroup.get("flagProcedure").value != null ? this.formGroup.get("flagProcedure").value.kode : null

      var kunjungan = 1;
      if (this.formGroup.get('pCare').value == 'pasca') {
        kunjungan = 0
      } else if (this.formGroup.get('pCare').value == 'kontrol') {
        kunjungan = 3
      }
      var isrujukanthalasemia_hemofilia = 0

      if (this.formGroup.get('poliTujuan').value.kode == 'UGD' || this.formGroup.get('poliTujuan').value.kode == 'IGD') {
        nmperujuk = '';
        kunjungan = 0;
        FLAGPROSEDUR = null;
      }

      //var sepdate = new Date(tglsep);
      //var currDate = new Date(dataSEP.sep.sep.FDATE);
      //var backdate = sepdate < new Date(currDate.getFullYear(), currDate.getMonth(), currDate.getDate()) ? " (BACKDATE)" : "";

      var backdate = ""
      var ispotensiHEMOFILIA_cetak = 0
      var _kodejaminan = '-';
      this.helper.cetakSEP(nosep + backdate, tglsep, nokartu, nmpst, tgllahir, jnskelamin, notelp, poli, faskesperujuk, dxawal, catatan, jnspst, cob, jnsrawat, klsrawat,
        prolanis, eksekutif, _kodejaminan, statuskll, katarak, potensiprb, dokter, kunjungan, FLAGPROSEDUR, "-", FLAGNAIKKELAS, klsrawat_naik, isrujukanthalasemia_hemofilia, ispotensiHEMOFILIA_cetak, 'RSUD PAGELARAN');


    }

  }
  cetakAntrian() {
    var profile = "1";
    // let petugas = '-'
    // window.open(Config.get().apiBackend + 'service/print/cetak-bukti-pendaftaran?noregistrasi='
    //   + this.pasienDaftar.noregistrasi + '&kdprofile=' + profile);

    let petugas = '-'
    this.service.get("http://127.0.0.1:3885/desk/routes?cetak-buktipendaftaran=1&noregistrasi=" +
      this.pasienDaftar.noregistrasi + "&qty=1&idpegawai=&ket=&view=true").subscribe(e => {
      });

  }

  changePoli(event) {
    var kdinternal = this.listPoli.find(data => data.value == event.value);
    if (this.showPasca == false) {
      kdinternal = event
    }
    console.log(kdinternal);
    this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 2
      + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=" + event.value.kode).subscribe(data => {
        if (data.metaData.code == 200) {
          this.listDokter = [];
          this.listDokter.push({ label: '-- Dokter --', value: null });
          data.response.list.forEach(response => {
            this.listDokter.push({
              label: response.nama, value: {
                'kode': response.kode,
                'nama': response.nama
              }
            });
          });
        }
        else {
          this.listDokter = [];
          this.alertService.info('Info', 'Dokter DPJP tidak ada')
        }

      });
  }
  changeClick() {
    this.listDokTemp = this.listDokter
    if (this.formGroup.get('allDPJP').value == true) {
      this.httpService.get("bridging/bpjs/get-ref-dokter-dpjp?jenisPelayanan=" + 1
        + "&tglPelayanan=" + moment(new Date()).format('YYYY-MM-DD') + "&kodeSpesialis=").subscribe(data => {
          if (data.metaData.code == 200) {
            this.listDokter = [];
            this.listDokter.push({ label: '-- Dokter --', value: null });
            data.response.list.forEach(response => {
              this.listDokter.push({
                label: response.nama, value: {
                  'kode': response.kode,
                  'nama': response.nama
                }
              });
            });
          }
          else
            this.alertService.info('Info', 'Dokter DPJP tidak ada')
        });
    } else {
      this.listDokter = this.listDokTemp
    }
  }


  //jQUERY

}
