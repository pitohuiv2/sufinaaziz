import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';
@Component({
  selector: 'app-pindah-pulang',
  templateUrl: './pindah-pulang.component.html',
  styleUrls: ['./pindah-pulang.component.scss'],
  providers: [ConfirmationService]
})
export class PindahPulangComponent implements OnInit, AfterViewInit {

  selected: any;
  dataTable: any[];
  item: any = {
    pasien: {},
    jumlah: 1,
    tglPelayanan: new Date(),
    tglMeninggal: new Date(),
    tglKeluar: new Date(),
    tglRencanaKeluar: new Date()
  };
  currentNorecPD: any
  currentNorecAPD: any
  maxDateValue = new Date()
  isSimpan: boolean
  listStatusKeluar: any[] = []
  listKondisiPasien: any[] = []
  listStatusPulang: any[] = []
  listRuangan: any[] = []
  listHubunganKel: any[] = []
  listPenyebabKematian: any[] = []
  listKamar: any[] = []
  listNoBed: any[] = []
  listKelas: any[] = []
  PenyebabKematianManual: boolean
  showPindah: boolean
  showMeninggal: boolean
  now = new Date()
  resultAPD: any
  idPindah: any
  idMeninggal: any
  idKelompokPasienBPJS: any
  idStatusKeluarPulang: any
  @ViewChild(HeadPasienComponent, { static: false }) h: HeadPasienComponent;
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) { }
  ngAfterViewInit(): void {
    this.route.params.subscribe(params => {

      this.currentNorecPD = params['norec_rp'];
      this.currentNorecAPD = params['norec_dpr']
      this.loadCombo()
      this.loadHead()
    })

  }
  ngOnInit(): void {
  }
  loadHead() {
    this.apiService.get("rawatinap/get-pasien-bynorec?norec_pd="
      + this.currentNorecPD
      + "&norec_apd="
      + this.currentNorecAPD)
      .subscribe(e => {
        e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
        e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
        this.h.item.pasien = e;
        this.item.pasien = e;
      })
  }
  loadCombo() {
    this.apiService.get("rawatinap/get-combo-pindahpasien")
      .subscribe(e => {
        for (var i = 0; i < e.statuskeluar.length; i++) {
          e.statuskeluar[i].id = parseInt(e.statuskeluar[i].id)
        }

        this.listStatusKeluar = e.statuskeluar;
        this.listKondisiPasien = e.kondisipasien;
        this.listStatusPulang = e.statuspulang;
        this.listRuangan = e.ruanganinap;
        this.listHubunganKel = e.hubungankeluarga;
        this.listPenyebabKematian = e.penyebabkematian;
        this.idPindah = parseInt(e.idPindah)
        this.idMeninggal = parseInt(e.idStatusKeluarMeninggal)
        this.idKelompokPasienBPJS = parseInt(e.idKelompokPasienBPJS)
        this.idStatusKeluarPulang = parseInt(e.idStatusKeluarPulang)

      })
  }
  IsiPenyebab(e) {
    if (e === undefined) return;
    if (e.value.penyebabkematian == "LAINNYA") {
      this.PenyebabKematianManual = true;
    } else {
      this.PenyebabKematianManual = false;
    }
  }





  changeStatus(event) {
    if (event.value.id == this.idPindah) {
      this.showPindah = true
      this.showMeninggal = false
    } else if (event.value.id == this.idMeninggal) {
      this.showMeninggal = true
      this.showPindah = false
    } else {
      this.showMeninggal = false
      this.showPindah = false
    }
  }
  changeRuangan(event) {
    this.apiService.get("registrasi/get-kelasbyruangan?idRuangan=" + event.value.id)
      .subscribe(dat => {
        this.listKelas = dat.kelas;
      });

  }
  changeKelas(e) {
    if (e === undefined) return;
    if (!this.item.kelas && !this.item.ruangan) return;
    var kelasId = "idKelas=" + this.item.kelas.id;
    var ruanganId = "&idRuangan=" + this.item.ruangan.id;
    var israwatgabung = "&israwatgabung=" + this.item.rawatGabung

    this.apiService.get("registrasi/get-kamarbyruangankelas?" + kelasId + ruanganId + israwatgabung)
      .subscribe(b => {
        this.listKamar = b.kamar
      });
  }
  changeKamar(e) {
    if (e.value.id === undefined || e.value.id == null) return;
    this.apiService.get("registrasi/get-nobedbykamar?idKamar=" + e.value.id)
      .subscribe(a => {
        this.listNoBed = []
        if (this.item.rawatGabung) {
          this.listNoBed = a.bed;
        } else {
          for (let i = 0; i < a.bed.length; i++) {
            const element = a.bed[i];
            if (element.statusbed == "KOSONG") {
              this.listNoBed.push(element)
            }
          }
        }
      })
  }
  save() {
    if (this.item.statusKeluar == undefined) {
      this.alertService.error('Info', 'Status keluar belum di pilih');
      return;
    } else if (this.item.statusKeluar.id === this.idPindah) {

      if (this.item.ruangan == undefined) {
        this.alertService.error('Info', 'Ruangan  belum di pilih');
        return;
      }
      if (this.item.kelas == undefined) {
        this.alertService.error('Info', 'Kelas belum di pilih');
        return;
      }
      this.SavePindah()
    } else {
      // debugger
      // if (this.item.pasien.objectkelompokpasienlastfk == 11) {
      //   this.alertService.error('Info', 'Status masih rencana rawat');
      //   return;
      // }
      this.confirmationService.confirm({
        message: 'Pastikan PELAYANAN sudah di Input semua ! Lanjut Simpan?',
        accept: () => {
          var objAkomodasi = {
            noregistrasi: this.item.pasien.noregistrasi
          }
          this.apiService.post('rawatinap/save-akomodasi-tea', objAkomodasi).subscribe(data => {
          })
          this.SavePulang();
        },
        reject: () => {

        }
      });


    }
  }
  SavePindah() {
    var statusKeluarId = null;
    if (this.item.statusKeluar != undefined) {
      statusKeluarId = this.item.statusKeluar.id;
    }

    var ruanganId = null;
    if (this.item.ruangan != undefined) {
      ruanganId = this.item.ruangan.id;
    }
    var kelasId = null;
    if (this.item.kelas != undefined) {
      kelasId = this.item.kelas.id;
    }
    var kamarIds = null;
    if (this.item.kamar != undefined) {
      kamarIds = this.item.kamar.id;
    }

    var nomorTempatTidurs = null;
    if (this.item.nomorTempatTidur != undefined) {
      nomorTempatTidurs = this.item.nomorTempatTidur.id;
    }

    var dokterId = null;
    if (this.item.dokter != undefined) {
      dokterId = this.item.dokter.id;
    }

    var keterangans = "";
    if (this.item.keteranganLainnya != undefined) {
      keterangans = this.item.keteranganLainnya;
    }
    var hubungankeluargaId = null;
    if (this.item.hubunganKeluarga != undefined) {
      hubungankeluargaId = this.item.hubunganKeluarga.id;
    }
    var kondisiKeluarId = null;
    if (this.item.kondisipasien != undefined) {
      kondisiKeluarId = this.item.kondisipasien.id;
    }
    var penyebabkematianId = null;
    if (this.item.penyebabKematian != undefined) {
      penyebabkematianId = this.item.penyebabKematian.id;
    }

    var statusPulangId = null;
    if (this.item.statusPulang != undefined) {
      statusPulangId = this.item.statusPulang.id;
    }


    var strukorder = {
      norecorder: '',
      norecrpp: '',
      tglorder: moment(this.now).format('YYYY-MM-DD HH:mm:ss'),
    }

    var pasiendaftar = {
      tglregistrasi: moment(this.item.pasien.tglregistrasi).format('YYYY-MM-DD HH:mm:ss'),
      tglregistrasidate: moment(this.item.pasien.tglregistrasi).format('YYYY-MM-DD'),
      noregistrasi: this.item.pasien.noregistrasi,
      objectruanganasalfk: this.item.pasien.objectruanganlastfk,
      objectruanganlastfk: ruanganId,
      objectkelasfk: kelasId,
      objecthubungankeluargaambilpasienfk: hubungankeluargaId,
      objectkondisipasienfk: kondisiKeluarId,
      objectpenyebabkematianfk: penyebabkematianId,
      objectstatuskeluarfk: statusKeluarId,
      objectstatuspulangfk: statusPulangId,
      norec_pd: this.currentNorecPD,
      objectkelompokpasienlastfk: this.item.pasien.objectkelompokpasienlastfk,
      nocmfk: this.item.pasien.nocmfk,
      objectstatuskeluarrencanafk: statusKeluarId,
      statuspasien: this.item.pasien.statuspasien,
    }
    var antrianpasiendiperiksa = {
      tglregistrasi: moment(this.item.pasien.tglregistrasi).format('YYYY-MM-DD HH:mm:ss'),
      objectruanganasalfk: this.item.pasien.objectruanganlastfk,
      objectruanganlastfk: ruanganId,
      objectkelasfk: kelasId,
      objectkamarfk: kamarIds,
      nobed: nomorTempatTidurs,
      tglmasuk: moment(this.item.tglRencanaKeluar).format('YYYY-MM-DD HH:mm:ss'),
      // israwatgabung:  this.model.rawatGabung === true ? 1 : 0,
      objectasalrujukanfk: this.item.pasien.objectasalrujukanfk,
      norec_apd: this.item.pasien.norec_apd,
      keteranganpindah: keterangans,
      israwatgabung: this.item.rawatGabung === true ? 1 : 0,
    }

    var objSave = {
      strukorder: strukorder,
      pasiendaftar: pasiendaftar,
      antrianpasiendiperiksa: antrianpasiendiperiksa
    }

    // manageServicePhp.saveOrderPindahPasien(objSave).then(function (e) {
    //     this.resultAPD=e.data.dataAPD;
    //     responData=e.data;
    //     this.isSimpan = true;
    //     this.isBatal = true;
    //     this.isNext=false;                                             
    // })
    this.isSimpan = true;
    this.apiService.post('rawatinap/save-pindah-pasien', objSave).subscribe(e => {
      this.resultAPD = e.dataAPD;
      this.isSimpan = false;
      this.apiService.postLog('Pindah Ruangan', 'norec Daftar Pasien Ruangan', e.dataAPD.norec, 'Pindah Ruangan dari '
        + this.item.pasien.namaruangan + ' ke Ruangan ' + this.item.ruangan.namaruangan + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
      window.history.back();
    }, error => {
      this.isSimpan = false;
    })
  }

  SavePulang() {
    var statusKeluarId = null;
    if (this.item.statusKeluar != undefined) {
      statusKeluarId = this.item.statusKeluar.id;
    }

    var ruanganId = null;
    if (this.item.ruangan != undefined) {
      ruanganId = this.item.ruangan.id;
    }

    var hubungankeluargaId = null;
    var hubunganKeluarga = null;
    if (this.item.hubunganKeluarga != undefined) {
      hubungankeluargaId = this.item.hubunganKeluarga.id;
      hubunganKeluarga = this.item.hubunganKeluarga.hubungankeluarga;
    }
    var kondisiKeluarId = null;
    var kondisiKeluar = null;
    if (this.item.kondisiKeluar != undefined) {
      kondisiKeluarId = this.item.kondisiKeluar.id;
      kondisiKeluar = this.item.kondisiKeluar.kondisipasien
    }
    var PenyebabKematianText = ""
    if (this.item.PenyebabKematianText != undefined) {
      PenyebabKematianText = this.item.PenyebabKematianText
    }
    var penyebabkematianId = null;
    if (this.item.penyebabKematian != undefined) {
      penyebabkematianId = this.item.penyebabKematian.id;
    }
    var statusPulangId = null;
    if (this.item.statusPulang != undefined) {
      statusPulangId = this.item.statusPulang.id;
    }
    var namaPembawaPulang = null;
    if (this.item.namaPembawaPulang != undefined) {
      namaPembawaPulang = this.item.namaPembawaPulang;
    }

    var nosuratketerangan = null;
    if(this.item.nosuratketerangan != undefined){
      nosuratketerangan = this.item.nosuratketerangan;
    }

    var strukorder = {
      norecorder: '',
      norecrpp: '',
      tglorder: moment(this.now).format('YYYY-MM-DD HH:mm:ss'),
    }

    var pasiendaftar = {
      namalengkapambilpasien: namaPembawaPulang,
      noregistrasi: this.item.pasien.noregistrasi,
      objectruanganlastfk: ruanganId,
      objecthubungankeluargaambilpasienfk: hubungankeluargaId,
      objectkondisipasienfk: kondisiKeluarId,
      objectpenyebabkematianfk: penyebabkematianId,
      objectstatuskeluarfk: statusKeluarId,
      objectstatuspulangfk: statusPulangId,
      tglmeninggal: moment(this.item.tglMeninggal).format('YYYY-MM-DD HH:mm:ss'),
      // tglpulang: moment(this.item.tglRencanaKeluar).format('YYYY-MM-DD hh:mm:ss'), #yg egi
      tglpulang: moment(this.item.tglKeluar).format('YYYY-MM-DD HH:mm:ss'),
      norec_pd: this.currentNorecPD,
      objectstatuskeluarrencanafk: statusKeluarId,
      nocmfk: this.item.pasien.nocmfk,
      keteranganpulang: "Pulang dengan kondisi : " + kondisiKeluar,
      keterangankematian: PenyebabKematianText,
      nosuratketerangan

    }
    var antrianpasiendiperiksa = {
      objectruanganlastfk: this.item.pasien.objectruanganlastfk,
      norec_apd: this.item.pasien.norec_apd,

    }
    var objSave = {
      strukorder: strukorder,
      pasiendaftar: pasiendaftar,
      antrianpasiendiperiksa: antrianpasiendiperiksa
    }

    this.isSimpan = true;
    this.apiService.post('rawatinap/save-pulang-pasien', objSave).subscribe(e => {
      if (this.item.pasien.israwatinap == 'true' && this.item.pasien.objectkelompokpasienlastfk == this.idKelompokPasienBPJS
        && this.item.statusKeluar.id == this.idStatusKeluarPulang) {
        this.setTglPulangBridging()
      }
      this.isSimpan = false;

      this.apiService.postLog('Pasien Pulang', 'norec Registrasi Pasien', this.currentNorecPD, 'Pasien Pulang dari '
        + this.item.pasien.namaruangan + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })

      window.history.back();

    }, error => {
      this.isSimpan = false;
    })

  }
  setTglPulangBridging() {
    let statusPulang = ""
    if (this.item.statusPulang != undefined) {
      if (this.item.statusPulang.kodeexternal)
        statusPulang = this.item.statusPulang.kodeexternal
      else
        statusPulang = this.item.statusPulang.id
    }
    let noSuratMeninggal = ""
    if (statusPulang == '4') {
      // noSuratMeninggal = this.item.pasien.noregistrasi
        noSuratMeninggal = this.item.nosuratketerangan
    }
    var dateGenerate = {
      'data': {
        "request": {
          "t_sep": {
            "noSep": this.item.noSep,
            "statusPulang": statusPulang,
            "noSuratMeninggal": noSuratMeninggal,
            "tglMeninggal": statusPulang == '4' ? moment(this.item.tglMeninggal).format('YYYY-MM-DD') : "",
            "tglPulang": moment(this.item.tglKeluar).format('YYYY-MM-DD'),
            "noLPManual": this.item.suratKetPol ? this.item.suratKetPol : "",
            "user": "Xoxo"
          }
        }
      }
    }

    this.apiService.putNonMessage('bridging/bpjs/update-tglpulang', dateGenerate).subscribe(e => {
      this.alertService.info('Set Tgl Pulang BPJS', e.metaData.message,)
    })
  }
  cekRawatGabung(bool) {
    if (bool === true) {
      if (this.item.pasien.id_ibu != undefined) {
        this.apiService.get("rawatinap/get-kamar-ruangan-ibu?id_ibu=" + this.item.pasien.id_ibu
          + "&nocm=" + this.item.pasien.nocm)
          .subscribe(dat => {
            if (dat.length > 0) {
              this.listKamar = []
              this.listNoBed = []
              this.listKamar.push({ id: dat[0].objectkamarfk, namakamar: dat[0].namakamar })
              this.listNoBed.push({ id: dat[0].nobed, reportdisplay: dat[0].tempattidur })
              this.item.ruangan = { id: dat[0].objectruanganlastfk, namaruangan: dat[0].namaruangan }
              this.item.kelas = { id: dat[0].kelasfk, namakelas: dat[0].namakelas }
              this.item.kamar = { id: dat[0].objectkamarfk, namakamar: dat[0].namakamar }
              this.item.nomorTempatTidur = { id: dat[0].nobed, reportdisplay: dat[0].tempattidur }

            }

          })

      }
    }
  }

  cancel() {
    window.history.back()
  }

}
