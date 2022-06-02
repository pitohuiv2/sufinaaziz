import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute } from '@angular/router';
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
  listKamar: any[]
  listNoBed: any[]
  listDokter: any[]
  listJenisPelayanan: any[]//SelectItem[]
  listAsalRujukan: SelectItem[]
  listKelompokPasien: SelectItem[]
  listRuangan: SelectItem[]
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
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,

  ) { }

  ngOnInit(): void {
    this.listBtn = [
      { label: 'Input Tindakan', icon: 'pi pi-calendar-plus', command: () => { this.inputTindakan(); } },
      { label: 'Input Asuransi', icon: 'pi pi-briefcase', command: () => { this.inputTindakan(); } },
      { separator: true },
      { label: 'Bukti Layanan', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Label Pasien', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Kartu Pasien', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Gelang Pasien', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Tracer', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Blanko BPJS', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Cetak SEP', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Nomor Antrian', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Identitas Pasien', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Lembar Rawat Inap', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Summary List', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
      { label: 'Status Triage', icon: 'pi pi-print', command: () => { this.inputTindakan(); } },
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
  inputTindakan() {

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
    });
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
    debugger
    let e = data.value
    if (e === undefined) return;
    this.apiService.get("registrasi/get-penjaminbykelompokpasien?kdKelompokPasien=" + e.id)
      .subscribe(z => {
        this.listRekanan = z.rekanan;
        if (e.kelompokpasien == 'Umum/Pribadi') {
          delete this.item.rekanan;
          this.nonUmum = false;
          this.item.jenisPasien = { id: this.listJenisPelayanan[1].id, jenispelayanan: this.listJenisPelayanan[1].jenispelayanan }
        } else if (e.kelompokpasien.indexOf('BPJS') > -1) {
          this.nonUmum = true;
          this.item.rekanan = { id: this.listRekanan[0].id, namarekanan: this.listRekanan[0].namarekanan };
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
    debugger
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
          var asalRujukan = this.item.asalRujukan
          this.cacheHelper.set('cacheAsalRujukan', asalRujukan);
        }
        if (this.norecPD != '') {
          this.apiService.postLog('Edit Registrasi', 'norec Pasien Daftar', this.norecPD, 'Edit Registrasi ke ruangan ' + this.item.ruangan.namaruangan + ' pada No Registrasi ' + this.model.noRegistrasi)
        } else {
          this.apiService.postLog('Registrasi', 'norec Pasien Daftar', this.norecPD, 'Registrasi ke ruangan ' + this.item.ruangan.namaruangan + ' pada No Registrasi ' + this.model.noRegistrasi)
          this.apiService.post("registrasi/save-adminsitrasi", { norec: this.model.norec_pd, norec_apd: this.resultAPD.norec }).subscribe(z => { })
          if (this.isRegisOnline != '') {
            this.apiService.post('registrasi/confirm-pasien-online', { "noreservasi": this.isRegisOnline, }).subscribe(z => { })
          }
        }
        if (!this.isPenunjang) {
          this.isNext = true;
        }

        if (this.item.kelompokPasien.kelompokpasien != 'Umum/Pribadi') {
          if (this.norecPD == '') {
            this.inputPemakaianAsuransi();
          }
        }
        if (this.norecPD == '' && this.item.ruangan.namaruangan != 'IGD' && this.model.rawatInap != true
          && this.item.ruangan.namaruangan != 'Hemodialisa' && this.item.ruangan.namaruangan != 'RADIOLOGI'
          && this.item.ruangan.namaruangan != 'LABORATORIUM' && this.item.ruangan.namaruangan != 'ELEKTROMEDIK') {
          this.nomorAntrian();
        }

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

  }
  updateTriage() {

  }
  nomorAntrian() {

  }
}
