import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-registrasi-ruangan',
  templateUrl: './registrasi-ruangan.component.html',
  styleUrls: ['./registrasi-ruangan.component.scss'],
  providers: [ConfirmationService]
})
export class RegistrasiRuanganComponent implements OnInit {
  params: any = {}
  currentNoCm: any
  model: any = {}
  item: any = {
    tglRegistrasi: new Date(),
    pasien: {}
  }
  dataAntrol:any={}
  listKamar: any[]
  listNoBed: any[]
  listDokter: any[]
  listJenisPelayanan: any[]//SelectItem[]
  listAsalRujukan: SelectItem[]
  listKelompokPasien: SelectItem[]
  listRuangan: SelectItem[] = []
  listRuanganRajal: SelectItem[]
  listRuanganRanap: SelectItem[]
  listRekanan: any[]
  isPenunjang: boolean
  norecPD: any = ''
  nonUmum: boolean
  isRegisOnline: any = ''
  listKelas: any[]
  isSimpan: boolean
  resultAPD: any
  resultPD: any
  isNext: boolean
  nocmIgd: any
  listBtn: MenuItem[];
  norecAPD: any = ''
  pasienBayi: boolean
  // listRekanan: SelectItem[]
  disableTgl: boolean
  history: any = {}
  disabledMutasi: boolean
  pop_cetakLabelPasien: boolean;
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,

  ) { }

  ngOnInit(): void {
    this.listBtn = [
      { label: 'Input Tindakan', icon: 'pi pi-calendar-plus', command: () => { this.inputTindakan(); } },
      { label: 'Input Asuransi', icon: 'pi pi-briefcase', command: () => { this.inputPemakaianAsuransi(); } },
      { label: 'Kembali', icon: 'fa fa-arrow-left', command: () => { this.cancel(); } },
      { separator: true },
      { label: 'Bukti Layanan', icon: 'pi pi-print', command: () => { this.cetakBuktiLayanan(); } },
      { label: 'Label Pasien', icon: 'pi pi-print', command: () => { this.cetakLabelPasien(); } },
      { label: 'Kartu Pasien', icon: 'pi pi-print', command: () => { this.cetakKartu(); } },
      { label: 'Gelang Pasien', icon: 'pi pi-print', command: () => { this.cetakGelang(); } },
      { label: 'Tracer', icon: 'pi pi-print', command: () => { this.cetakTracer(); } },
      // { label: 'Blanko BPJS', icon: 'pi pi-print', command: () => { this.cetakBlanko(); } },
      { label: 'Cetak SEP', icon: 'pi pi-print', command: () => { this.cetakSep(); } },
      { label: 'Nomor Antrian', icon: 'pi pi-print', command: () => { this.cetakNomorAntrian(); } },
      { label: 'Pernyataan Jampersal', icon: 'pi pi-print', command: () => { this.cetakJampersal(); } },
      { label: 'Persetujuan Rawat Inap', icon: 'pi pi-print', command: () => { this.cetakPersetujuanRanap(); } },
      // { label: 'Identitas Pasien', icon: 'pi pi-print', command: () => { this.cetakIdentitas(); } },
      // { label: 'Lembar Rawat Inap', icon: 'pi pi-print', command: () => { this.cetakLembar(); } },
      // { label: 'Summary List', icon: 'pi pi-print', command: () => { this.cetakSummary(); } },
      // { label: 'Status Triage', icon: 'pi pi-print', command: () => { this.cetakTriage(); } },
    ];
    this.route.params.subscribe(params => {
      this.params.idPasien = params['id'];
      this.currentNoCm = this.params.idPasien
      this.apiService.get("registrasi/get-pasienbynocm?noCm=" + this.currentNoCm)
        .subscribe(e => {
          this.item.pasien = e.data[0];
          this.item.nocmfk = e.data[0].nocmfk;
          if (e.data[0].foto == null)
            this.item.pasien.foto = "../app/images/avatar.jpg"

          var now = new Date();
          var umur = this.dateHelper.CountAge(new Date(this.item.pasien.tgllahir), now);
          this.item.pasien.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari'
          this.item.pasien.tgllahir = moment(new Date(e.data[0].tgllahir)).format('DD-MM-YYYY');
          var parameterBayi = this.item.pasien.namapasien;
          if (parameterBayi.indexOf('By Ny') >= 0) {
            this.item.tglRegistrasi = new Date(this.item.pasien.tgllahir);
            this.model.rawatInap = true;
          }
          this.loadCombo()
        });
    });
  }

  loadCombo() {
    this.apiService.get("registrasi/get-data-combo-new").subscribe(dat => {
      this.listJenisPelayanan = dat.jenispelayanan;
      this.listAsalRujukan = dat.asalrujukan;
      this.listKelompokPasien = dat.kelompokpasien;
      if (!this.isPenunjang)
        this.listRuangan = dat.ruanganrajal;
      this.listRuanganRajal = dat.ruanganrajal;
      this.listRuanganRanap = dat.ruanganranap;
      this.model.namaPenjamin = { id: dat.kelompokpasien[1].id, kelompokpasien: dat.kelompokpasien[1].kelompokpasien }
      // if (dat.data.hubunganpeserta.length > 0)
      //   this.model.hubunganPeserta = { id: dat.hubunganpeserta[2].id, hubunganpeserta: dat.hubunganpeserta[2].hubunganpeserta }
      if (this.model.rawatInap == true)
        this.cekRawatInap(this.model.rawatInap);
      var cacheRegisOnline = this.cacheHelper.get('CacheRegisOnline');
      if (cacheRegisOnline != undefined) {
        var arrOnline = cacheRegisOnline[0];
        this.item.tglRegistrasi = new Date();//arrOnline.tanggalreservasi,
        if (arrOnline.objectruanganfk != null) {

          this.item.ruangan = {
            id: arrOnline.objectruanganfk,
            namaruangan: arrOnline.namaruangan
          }
        }
        if (arrOnline.objectkelompokpasienfk != null) {
          this.item.kelompokPasien = {
            id: arrOnline.objectkelompokpasienfk,
            kelompokpasien: arrOnline.kelompokpasien,
          }
        }
        if (arrOnline.objectpegawaifk != null) {
          this.item.dokter = { id: arrOnline.objectpegawaifk, namalengkap: arrOnline.dokter }
        }
        this.isRegisOnline = arrOnline.noreservasi
      }
      this.cacheHelper.set('CacheRegisOnline', undefined);

      var cacheOnlineBaru = this.cacheHelper.get('CacheRegisOnlineBaru');
      if (cacheOnlineBaru != undefined) {
        this.isRegisOnline = cacheOnlineBaru[0].noreservasi
        this.item.ruangan = {
          id: cacheOnlineBaru[0].objectruanganfk,
          namaruangan: cacheOnlineBaru[0].namaruangan,
        }
        this.item.kelompokPasien = {
          id: cacheOnlineBaru[0].objectkelompokpasienfk,
          kelompokpasien: cacheOnlineBaru[0].kelompokpasien,
        }
      }
      this.cacheHelper.set('CacheRegisOnlineBaru', undefined);

      this.norecPD = ''
      var cachePasienDaftar = this.cacheHelper.get('CacheRegistrasiPasien');
      if (cachePasienDaftar != undefined) {
        var arrPasienDaftar = cachePasienDaftar.split('~');
        this.item.IsEdit = true;

        this.model.noRegistrasi = arrPasienDaftar[1];
        this.model.norec_pd = arrPasienDaftar[0];
        this.norecPD = arrPasienDaftar[0];
        this.norecAPD = arrPasienDaftar[2];
        this.model.norec_apd = arrPasienDaftar[2];
        this.getPasienByNorecPD();

      }
      var CacheMutasiRanap = this.cacheHelper.get('CacheMutasiRanap');

      if (CacheMutasiRanap != undefined) {
        var arrPasienDaftar = CacheMutasiRanap.split('~');
        // this.item.IsEdit = true;
        this.model.noRegistrasi = arrPasienDaftar[1];
        this.model.norec_pd = arrPasienDaftar[0];
        this.norecPD = arrPasienDaftar[0];
        this.norecAPD = arrPasienDaftar[2];
        this.model.norec_apd = arrPasienDaftar[2];
        this.getHistory();
        this.model.rawatInap = true
        this.cekRawatInap(this.model.rawatInap);
        this.disabledMutasi = true
        this.cacheHelper.set('CacheMutasiRanap', undefined);
      }
    });
  }
  getHistory() {
    this.apiService.get("registrasi/get-pasienbynorec-pd?norecPD=" + this.norecPD + "&norecAPD=" + this.norecAPD)
      .subscribe(his => {
        this.history = his.data[0];

      })
  }
  getPasienByNorecPD() {
    this.apiService.get("registrasi/get-pasienbynorec-pd?norecPD=" + this.norecPD + "&norecAPD=" + this.norecAPD)
      .subscribe(his => {

        if (his.data[0].israwatinap == 'true') {
          this.model.rawatInap = true;
          this.cekRawatInap(this.model.rawatInap);
          this.pasienBayi = true;
        }
        this.item.tglRegistrasi = new Date(his.data[0].tglregistrasi)
        this.item.ruangan = {
          id: his.data[0].objectruanganlastfk,
          namaruangan: his.data[0].namaruangan,
          objectdepartemenfk: his.data[0].objectdepartemenfk
        }
        if (his.data[0].objectdepartemenfk == 2) {
          this.model.rawatInap = true;
        }
        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var dateNow = new Date();
        var dateRegis = new Date(this.item.tglRegistrasi);
        var diffDays = Math.round(Math.abs((dateNow.getTime() - dateRegis.getTime()) / (oneDay)))
        if (diffDays >= 1) {
          this.disableTgl = true
        }
        // this.resultEdit = his.data.data[0]
        // ruanganLog = his.data.data[0].namaruangan
        this.item.kelas = { id: his.data[0].objectkelasfk, namakelas: his.data[0].namakelas }
        this.listKamar = ([{ id: his.data[0].objectkamarfk, namakamar: his.data[0].namakamar }])
        this.item.kamar = { id: his.data[0].objectkamarfk, namakamar: his.data[0].namakamar }
        this.listNoBed = ([{ id: his.data[0].objecttempattidurfk, reportdisplay: his.data[0].reportdisplay }])
        this.item.nomorTempatTidur = { id: his.data[0].objecttempattidurfk, reportdisplay: his.data[0].reportdisplay }
        this.item.asalRujukan = { id: his.data[0].objectasalrujukanfk, asalrujukan: his.data[0].asalrujukan }
        this.item.kelompokPasien = { id: his.data[0].objectkelompokpasienlastfk, kelompokpasien: his.data[0].kelompokpasien }
        this.changeJenis({
          value: this.item.kelompokPasien
        })
        this.item.rekanan = { id: his.data[0].objectrekananfk, namarekanan: his.data[0].namarekanan }
        this.item.dokter = { id: his.data[0].objectpegawaifk, namalengkap: his.data[0].dokter }
        if (!this.isPenunjang) {
          this.isNext = true;
        } else {
          this.isNext = false;
        }
      });
  }
  filterDokter(event) {
    let query = event.query;
    this.apiService.get("registrasi/get-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listDokter = re;
    })
  }
  cekRawatInap(data) {
    if (data === true) {
      if (this.norecPD == '') {
        delete this.item.ruangan;
      }
      this.listRuangan = []
      this.listRuangan = this.listRuanganRanap
    } else if (data === false || data === undefined) {
      if (this.norecPD == '') {
        delete this.item.ruangan;
      }
      this.listRuangan = []
      this.listRuangan = this.listRuanganRajal;
      this.item.kelas = undefined;
      this.item.nomorTempatTidur = undefined;
      this.item.kamar = undefined;
    } else {
      return;
    }

  }
  changeJenis(data) {
    let e = data.value
    if (e === undefined) return;
    this.apiService.get("registrasi/get-penjaminbykelompokpasien?kdKelompokPasien=" + e.id)
      .subscribe(z => {
        this.listRekanan = z.rekanan;
        if (e.kelompokpasien.indexOf('Umum') > -1) {
          delete this.item.rekanan;
          this.nonUmum = false;
          this.item.jenisPasien = { id: this.listJenisPelayanan[1].id, jenispelayanan: this.listJenisPelayanan[1].jenispelayanan }
        } else if (e.kelompokpasien.indexOf('BPJS') > -1) {
          this.nonUmum = true;
          for (let i = 0; i < this.listRekanan.length; i++) {
            const element = this.listRekanan[i];
            if (element.namarekanan.toLowerCase().indexOf('bpjs') > -1) {
              this.item.rekanan = { id: element.id, namarekanan: element.namarekanan };
              break
            }
          }

          this.item.jenisPasien = { id: this.listJenisPelayanan[1].id, jenispelayanan: this.listJenisPelayanan[1].jenispelayanan }
        } else {
          this.nonUmum = true;
          this.item.jenisPasien = {
            id: this.listJenisPelayanan[1].id,
            jenispelayanan: this.listJenisPelayanan[1].jenispelayanan
          }
        }
      })
  }
  changeRuangan(event) {
    if (this.model.rawatInap === true) {
      this.apiService.get("registrasi/get-kelasbyruangan?idRuangan=" + event.value.id)
        .subscribe(dat => {
          this.listKelas = dat.kelas;
        });
    }
  }
  changeKelas(e) {
    if (e === undefined) return;
    if (!this.item.kelas && !this.item.ruangan) return;
    var kelasId = "idKelas=" + this.item.kelas.id;
    var ruanganId = "&idRuangan=" + this.item.ruangan.id;
    var israwatgabung = "&israwatgabung=" + this.model.rawatGabung

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
        if (this.model.rawatGabung) {
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
    if (this.disabledMutasi == true) {
      this.saveMutasi()
      return
    }
    // 
    if (this.item.ruangan == undefined) {
      this.alertService.warn('Info', 'Ruangan harus di isi')
      return
    }
    var isRawatInap = "false"

    if (this.model.rawatInap != undefined && this.model.rawatInap == true) {
      isRawatInap = "true"
      if (this.item.kelas == undefined) {
        this.alertService.warn('Info', 'Kelas Harus di isi')
        return
      }
    }
    if (this.item.asalRujukan == undefined) {
      this.alertService.warn('Info', 'Asal Rujukan harus di isi')
      return
    }
    if (this.item.kelompokPasien == undefined) {
      this.alertService.warn('Info', 'Jenis Pembiayaan harus di isi')
      return
    }
    if (this.item.jenisPasien == undefined) {
      this.alertService.warn('Info', 'Jenis Pelayanan harus di isi')
      return
    }
    var rekananId = null;
    if (this.item.rekanan != undefined) {
      rekananId = this.item.rekanan.id;
    }
    var dokterId = null
    if (this.item.dokter != undefined) {
      dokterId = this.item.dokter.id;
    }

    var kamarIds = null
    if (this.item.kamar != undefined) {
      kamarIds = this.item.kamar.id;
    }

    var nomorTempatTidurs = null
    if (this.item.nomorTempatTidur != undefined) {
      nomorTempatTidurs = this.item.nomorTempatTidur.id;
    }


    var norec_PasienDaftar = "";
    if (this.model.norec_pd != undefined) {
      norec_PasienDaftar = this.model.norec_pd;
    }

    var norec_Antrian = "";
    if (this.model.norec_apd != undefined) {
      norec_Antrian = this.model.norec_apd;
    }

    var noRegistrasizz = ""
    if (this.model.noRegistrasi != undefined) {
      noRegistrasizz = this.model.noRegistrasi
    }

    var statusPasien = ''
    var cacheBaruLama = this.cacheHelper.get('cacheStatusPasien')
    if (cacheBaruLama != undefined) {
      statusPasien = cacheBaruLama
    }
    var pasiendaftar = {
      tglregistrasi: moment(this.item.tglRegistrasi).format('YYYY-MM-DD HH:mm:ss'),
      tglregistrasidate: moment(this.item.tglRegistrasi).format('YYYY-MM-DD'),
      nocmfk: this.item.nocmfk,
      objectruanganfk: this.item.ruangan.id,
      objectdepartemenfk: this.item.ruangan.objectdepartemenfk,
      objectkelasfk: this.model.rawatInap == true ? this.item.kelas.id : 6,
      objectkelompokpasienlastfk: this.item.kelompokPasien.id,
      objectrekananfk: rekananId,
      tipelayanan: this.item.jenisPasien.id,
      objectpegawaifk: dokterId,
      noregistrasi: noRegistrasizz,
      norec_pd: norec_PasienDaftar,
      israwatinap: isRawatInap,
      statusschedule: this.isRegisOnline,
      statuspasien: statusPasien

    }
    var antrianpasiendiperiksa = {
      norec_apd: norec_Antrian,
      tglregistrasi: moment(this.item.tglRegistrasi).format('YYYY-MM-DD HH:mm:ss'),
      objectruanganfk: this.item.ruangan.id,
      objectkelasfk: this.model.rawatInap == true ? this.item.kelas.id : 6,
      objectpegawaifk: dokterId,
      objectkamarfk: kamarIds,
      nobed: nomorTempatTidurs,
      objectdepartemenfk: this.item.ruangan.objectdepartemenfk,
      objectasalrujukanfk: this.item.asalRujukan.id,
      israwatgabung: this.model.rawatGabung === true ? 1 : 0,
    }
    var objSave = {
      pasiendaftar: pasiendaftar,
      antrianpasiendiperiksa: antrianpasiendiperiksa
    }
    this.isSimpan = true;

    this.apiService.post('registrasi/save-registrasipasien', objSave).subscribe(e => {
      this.isSimpan = false;
      this.resultAPD = e.dataAPD;

      // responData = e.data;
      this.resultPD = e.dataPD;

      this.model.noRegistrasi = e.dataPD.noregistrasi;
      this.model.norec_pd = e.dataPD.norec;

      this.model.norec_apd = e.dataAPD.norec;
      var cachePasienDaftar = this.model.norec_pd
        + "~" + this.model.noRegistrasi
        + "~" + e.dataAPD.norec;

      this.cacheHelper.set('CacheRegistrasiPasien', cachePasienDaftar);

      if (e.status == 201) {
        if (this.item.asalRujukan != undefined) {
          var asalRujukan = this.item.asalRujukan.asalrujukan
          this.cacheHelper.set('cacheAsalRujukan', asalRujukan);
        }
        if (this.norecPD != '') {
          this.apiService.postLog('Edit Registrasi', 'norec Pasien Daftar', this.norecPD, 'Edit Registrasi ke ruangan '
            + this.item.ruangan.namaruangan + ' pada No Registrasi ' + this.model.noRegistrasi).subscribe(z => { })
        } else {
          this.apiService.postLog('Registrasi', 'norec Pasien Daftar', this.norecPD, 'Registrasi ke ruangan '
            + this.item.ruangan.namaruangan + ' pada No Registrasi ' + this.model.noRegistrasi).subscribe(z => {
              // save admin
              var json = {
                norec: this.model.norec_pd,
                norec_apd: this.model.norec_apd
              }
              this.apiService.post("registrasi/save-adminsitrasi", json).subscribe(e => { })
            });
          if (this.isRegisOnline != '') {
            this.apiService.post('registrasi/confirm-pasien-online', { "noreservasi": this.isRegisOnline, }).subscribe(z => { })
          } else {
            // save Antrol
            if (this.norecPD == '' && this.isRegisOnline == '') {
              this.saveAntrol(this.model.noRegistrasi, this.resultAPD)
            }
          }
        }
        if (!this.isPenunjang) {
          this.isNext = true;
        }

        if ((this.item.kelompokPasien.kelompokpasien.toLowerCase().indexOf('umum') > -1) == false) {
          if (this.norecPD == '') {
            this.inputPemakaianAsuransi();
          }
        }
        if (this.norecPD == '' && this.item.ruangan.namaruangan != 'IGD' && this.model.rawatInap != true
          && this.item.ruangan.namaruangan != 'Hemodialisa' && this.item.ruangan.namaruangan != 'RADIOLOGI'
          && this.item.ruangan.namaruangan != 'LABORATORIUM' && this.item.ruangan.namaruangan != 'ELEKTROMEDIK') {
          this.nomorAntrian();
        }
        this.norecPD = e.dataPD.norec;
        this.norecAPD = e.dataAPD.norec;
      }

      if (this.nocmIgd != undefined) {
        this.updateTriage();
      }

    }, function (error) {
      // throw error;
      this.isSimpan = false;
      this.isBatal = false;
    })
  }
  inputPemakaianAsuransi() {
    if (this.item.asalRujukan != undefined) {
      var asalRujukan = this.item.asalRujukan.asalrujukan
      this.cacheHelper.set('cacheAsalRujukan', asalRujukan);
    }
    var norec, norecapd
    if (this.norecPD == '') {
      norec = this.model.norec_pd
      norecapd = this.model.norec_apd
    } else {
      norec = this.norecPD
      norecapd = this.norecAPD
    }

    this.router.navigate(['pemakaian-asuransi', norec, norecapd])
  }
  updateTriage() {

  }
  nomorAntrian() {
  }
  cancel() {
    this.cacheHelper.set('CacheRegistrasiPasien', undefined)
    window.history.back()
  }
  cetakTriage() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    var stt = 'false'
    if (confirm('View Triage IGD? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-triage=1&noregistrasi=" + this.model.noRegistrasi
      + "&view=" + stt, function (e) { });
  }
  cetakSummary() {
    if (this.item.pasien.nocm == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    var stt = 'false'
    if (confirm('View Summary List? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-summarylist=1&nocm=" + this.item.pasien.nocm
      + "&view=" + stt, function (e) { });
  }
  cetakLembar() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    var stt = 'false'
    if (confirm('View Persejutuan Rawat Inap? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-surat-pernyataan-ranap=1&noregistrasi=" + this.model.noRegistrasi
      + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap + "&view=" + stt, function (e) { });
  }
  cetakIdentitas() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    var stt = 'false'
    if (confirm('View Indentitas Pasien? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-lembar-identitas=1&nocm=" + this.item.pasien.nocm
      + "&noregistrasi=" + this.model.noRegistrasi + "&tipePasien=" + this.item.kelompokPasien.kelompokpasien + "&idpegawai="
      + this.authService.getDataLoginUser().pegawai.namaLengkap + "&view=" + stt, function (e) { });
  }
  cetakNomorAntrian() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    var stt = 'false'
    if (confirm('View Bukti Pendaftaran? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-antrian-pendaftaran=1&noregistrasi=" +
      this.model.noRegistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }
  cetakSep() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    if (this.item.kelompokPasien.kelompokpasien != "BPJS") {
      this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
      return
    }
    var stt = 'false'
    if (confirm('View SEP? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-sep=1&noregistrasi=" +
      this.model.noRegistrasi + "&qty=1&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&ket=&view=" + stt, function (e) {
      });
  }
  cetakBlanko() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Pilih data dulu")
      return
    }
    // if (this.item.kelompokPasien.kelompokpasien === "Umum/Tunai") {
    //   this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
    //   return
    // }
    var stt = 'false'
    if (confirm('View Blangko BPJS? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-blangko-bpjs=1&noregistrasi=" +
      this.model.noRegistrasi + "&qty=1&idpegawai=" + this.authService.getDataLoginUser().pegawai.id
      + "&ket=&view=" + stt, function (e) {
      });
  }
  cetakTracer() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan")
      return
    }
    var stt = 'false'
    if (confirm('View Tracer? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-tracer=1&noregistrasi=" +
      this.model.noRegistrasi + "&view=" + stt, function (e) { });
  }
  cetakGelang() {
    var stt = 'false'
    if (confirm('View Gelang Pasien? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-gelangpasien=1&noregistrasi=" +
      this.model.noRegistrasi + "&qty=1" + "&view=" + stt, function (e) { });
  }
  cetakKartu() {
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-kartu-pasien=1&norm=" +
      this.item.pasien.nocm, function (e) { });
  }
  cetakLabelPasien() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }
    this.item.qtyPrint = 1;
    this.pop_cetakLabelPasien = true;
  }
  cetakLabelPrint() {
    var stt = 'false'
    if (confirm('View Label Pasien? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-labelpasien=1&noregistrasi=" +
      this.model.noRegistrasi + "&qty=" + this.item.qtyPrint + "&view=" + stt, function (e) { });
    this.pop_cetakLabelPasien = false;
  }
  cetakBuktiLayanan() {
    if (this.item.ruangan == undefined) {
      this.alertService.error('Info', "Ruangan Masih Belum Diisi!");
      return;
    }
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }
    var stt = 'false'
    if (confirm('View Bukti Layanan? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-buktilayanan=1&noregistrasi=" +
      this.model.noRegistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&idRuangan=" + this.item.ruangan.id + "&view=" + stt, function (e) { });
  }
  inputTindakan() {
    var norec, norecapd
    if (this.norecPD == '') {
      norec = this.model.norec_pd
      norecapd = this.model.norec_apd
    } else {
      norec = this.norecPD
      norecapd = this.norecAPD
    }

    this.router.navigate(['input-tindakan', norec, norecapd])
  }
  saveMutasi() {
    if (this.item.kelas == undefined) {
      this.alertService.warn('Info', 'Kelas Harus di isi')
      return
    }
    if (this.item.jenisPasien == undefined || this.item.jenisPasien == null) {
      this.alertService.warn('Info', 'Jenis Pelayanan Harus di isi')
      return
    }


    var kelasId = this.item.kelas.id;

    var rekananId = null;
    if (this.item.rekanan != undefined)
      rekananId = this.item.rekanan.id;

    var dokterId = null;
    if (this.item.dokter != undefined)
      dokterId = this.item.dokter.id;

    var kamarIds = null;
    if (this.item.kamar == undefined) {
      kamarIds = null;
    } else
      kamarIds = this.item.kamar.id;


    var nomorTempatTidurs = null;
    if (this.item.nomorTempatTidur == undefined) {
      nomorTempatTidurs = null;
    } else
      nomorTempatTidurs = this.item.nomorTempatTidur.id;

    var jenisPel = this.item.jenisPasien.id

    var tmpData = {
      "pegawai": {
        "id": dokterId
      },
      "tglRegistrasi": moment(this.item.tglRegistrasi).format('YYYY-MM-DD HH:mm:ss'),
      "jenisPelayanan": jenisPel,
      "kelompokPasien": {
        "id": this.item.kelompokPasien.id
      },
      "ruangan": {
        "id": this.item.ruangan.id
      },
      "pasien": {
        "id": this.item.nocmfk,
        "pasienDaftar": {
          "noRec": this.norecPD
        },
        "noCm": this.item.pasien.nocm
      },
      "noRecAntrianPasien": this.norecAPD,
      "tglRegisDateOnly": moment(this.item.tglRegistrasi).format('YYYY-MM-DD'),
      "objectruanganasalfk": this.history.objectruanganlastfk,
      "objectrekananfk": rekananId,
      "asalRujukan": {
        "id": this.history.objectasalrujukanfk
      },
      "kelas": {
        "id": kelasId
      },
      "kamar": {
        "id": kamarIds
      },
      "nomorTempatTidur": {
        "id": nomorTempatTidurs
      },
      "status": "pindah Kamar",
      "noRecPasienDaftar": this.norecPD,
      "statusPasien": this.history.statuspasien,
      "isRawatGabung": this.model.rawatGabung != undefined ? this.model.rawatGabung : false,
    }
    this.isSimpan = true;
    this.apiService.post('registrasi/simpan-mutasi-pasien', tmpData).subscribe(e => {
      // this.resultAPD = e.data;
      this.resultAPD = e.data;
      this.model.noRegistrasi = this.history.noregistrasi;
      this.model.norec_apd = e.data.norec;

      this.apiService.postLog('Mutasi Rawat Inap', 'norec Registrasi Ruangan', this.norecPD,
        'Mutasi Rawat Inap ' + ' dengan No Registrasi - ' + this.model.noRegistrasi + ' ke Ruangan '
        + this.item.ruangan.namaruangan).subscribe(z => { })
      this.apiService.post('registrasi/update-sep-igd', { norec: this.norecPD }).subscribe(z => { })
      //## end log    

      var cachePasienDaftar = this.norecPD
        + "~" + this.model.noRegistrasi
        + "~" + this.resultAPD.norec;
      this.cacheHelper.set('CacheRegistrasiPasien', cachePasienDaftar);
      if (this.item.asalRujukan != undefined) {
        this.cacheHelper.set('cacheAsalRujukan', this.item.asalRujukan.asalrujukan);
      }
      this.isSimpan = true;
      this.isNext = true;

      if ((this.item.kelompokPasien.kelompokpasien.toLowerCase().indexOf('umum') > -1) == false) {
        this.inputPemakaianAsuransi();

      } else {
        window.history.back()
      }


    }, function (error) {
      // throw error;
      this.isSimpan = false;
      this.isNext = false;
    })
  }

  cetakJampersal() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }

    if (this.item.kelompokPasien.kelompokpasien = ! "Jampersal") {
      this.alertService.error('Info', "Hanya Untuk Pasien BPJS")
      return
    }

    var stt = 'false'
    if (confirm('View Surat Pernyataan Jampersal? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-surat-jampersal=1&noregistrasi=" +
      this.model.noRegistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }

  cetakPersetujuanRanap() {
    if (this.model.noRegistrasi == undefined) {
      this.alertService.error('Info', "Data Tidak Ditemukan!");
      return;
    }

    if (this.model.rawatInap != true) {
      this.alertService.error('Info', "Hanya Untuk Pasien Rawat Inap!");
      return;
    }

    var stt = 'false'
    if (confirm('View Surat Persetujuan Rawat Inap? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }
    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-surat-pernyataan-ranap=1&noregistrasi=" +
      this.model.noRegistrasi + "&idpegawai=" + this.authService.getDataLoginUser().pegawai.namaLengkap
      + "&view=" + stt, function (e) { });
  }
  saveAntrol(noregistrasi, apd) {
    var isBPJS = false
    if (this.item.kelompokPasien.kelompokpasien.indexOf('BPJS') > -1)
      isBPJS = true

    if (this.item.ruangan.kodebpjs == undefined || this.item.ruangan.kodebpjs == null) {
      return
    }
    var json = {
      "url": "jadwaldokter/kodepoli/" + this.item.ruangan.kodebpjs + "/tanggal/" + moment(this.item.tglRegistrasi).format('YYYY-MM-DD'),
      "jenis": "antrean",
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage('bridging/bpjs/tools', json).subscribe(z => {
      if (z.metaData.code == 201) return
      if (z.response == null) return
      if (z.response.length == 0) return
      if (!this.item.dokter && !this.item.dokter.kodebpjs) return
      var kodeDokterBPJS: any = ''
      for (var i = z.response.length - 1; i >= 0; i--) {
        const element = z.response[i]
        if (element.kodedokter == this.item.dokter.kodebpjs) {
          kodeDokterBPJS = {
            "jadwal": element.jadwal,
            "namadokter": element.namadokter,
            "kodedokter": element.kodedokter,
          }
          break;
        }
      }
      if (kodeDokterBPJS == '') return
      var status = '0'
      if (this.cacheHelper.get('cacheStatusPasien') && this.cacheHelper.get('cacheStatusPasien') == 'BARU') {
        status = '1'
      }
      this.dataAntrol ={}
      var noref = this.item.pasien.nobpjs + this.item.pasien.nocm
      var data = {
        "url": "antrean/add",
        "jenis": "antrean",
        "method": "POST",
        "data": {
          "kodebooking": noregistrasi,
          "jenispasien": isBPJS ? 'JKN' : 'NON JKN',
          "nomorkartu": isBPJS ? (this.item.pasien.nobpjs ? this.item.pasien.nobpjs : "") : "",
          "nik": this.item.pasien.noidentitas ? this.item.pasien.noidentitas : "",
          "nohp": this.item.pasien.notelepon ? this.item.pasien.notelepon : "000000000000",
          "kodepoli": this.item.ruangan.kodebpjs,
          "namapoli": this.item.ruangan.namaruangan,
          "pasienbaru": status,
          "norm": this.item.pasien.nocm,
          "tanggalperiksa": moment(this.item.tglRegistrasi).format('YYYY-MM-DD'),
          "kodedokter": kodeDokterBPJS.kodedokter,
          "namadokter": kodeDokterBPJS.namadokter,
          "jampraktek": kodeDokterBPJS.jadwal,
          "jeniskunjungan": 1,
          "nomorreferensi": noref ,
          "nomorantrean": apd.noantrian,
          "angkaantrean": apd.noantrian,
          "estimasidilayani": new Date().getTime(),
          "sisakuotajkn": 0,
          "kuotajkn": 0,
          "sisakuotanonjkn": 0,
          "kuotanonjkn": 0,
          "keterangan": ""
        }
      }
      this.dataAntrol = data
      
      this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe(e => {
        this.apiService.postLog('Antrol Task ID', 'norec Pasien Daftar',
        noregistrasi, 'Tambah Antrean Kode ' + noregistrasi +' | '+
        JSON.stringify(this.dataAntrol) + ' | '+ JSON.stringify(e)).subscribe(z => { })
     
        if (e.metaData.code == 201) return
        var data = {
          "url": "antrean/updatewaktu",
          "jenis": "antrean",
          "method": "POST",
          "data":
          {
            "kodebooking": noregistrasi,
            "taskid": 1,//waktu admisi
            "waktu": new Date().getTime() - 300000//kurangi 8 menit
          }
        }
        this.apiService.postLog('Antrol Task ID', 'norec Pasien Daftar',
        noregistrasi, 'TASK ID 1 : ' + noregistrasi +' | '+
        JSON.stringify(data)).subscribe(z => { })

        this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe(e => {
          var data = {
            "url": "antrean/updatewaktu",
            "jenis": "antrean",
            "method": "POST",
            "data":
            {
              "kodebooking": noregistrasi,
              "taskid": 2,//akhir waktu tunggu admisi/mulai waktu layan admisi
              "waktu": new Date().getTime() - 180000 //kurangi 5 menit
            }
          }
          this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe(e => {
            var data = {
              "url": "antrean/updatewaktu",
              "jenis": "antrean",
              "method": "POST",
              "data":
              {
                "kodebooking": noregistrasi,
                "taskid": 3,//(akhir waktu layan admisi/mulai waktu tunggu poli), 
                "waktu": new Date().getTime()
              }
            }
            this.apiService.postNonMessage('bridging/bpjs/tools', data).subscribe(e => {

            })
          })
        })



      })
    })
  }
}

