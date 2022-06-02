import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute } from '@angular/router';
@Component({
  selector: 'app-pasien-baru',
  templateUrl: './pasien-baru.component.html',
  styleUrls: ['./pasien-baru.component.scss'],
  providers: [ConfirmationService]
})
export class PasienBaruComponent implements OnInit {
  item: any = {}
  listDataJenisKelamin: SelectItem[];
  listDataAgama: SelectItem[];
  listDataStatusPerkawinan: SelectItem[];
  listGolonganDarah: SelectItem[];
  listDataPendidikan: SelectItem[];
  listDataPekerjaan: SelectItem[];
  listSuku: SelectItem[];
  listKebangsaan: SelectItem[];
  listNegara: SelectItem[];
  // listDataKelurahan: SelectItem[];
  listDataKelurahan: any[]
  listDataKecamatan: any[]
  // listDataKecamatan: SelectItem[];
  listDataKotaKabupaten: SelectItem[];
  listDataPropinsi: SelectItem[];
  isBayi: boolean
  isTriage: boolean
  isPenunjang: boolean
  idPasien: any = ''
  Triage: any
  title: any = 'Pendaftaran Pasien'
  now: any = new Date()
  noCmIbu: any
  nocmIgd: any
  params: any = {}
  reservasi: any = {}
  idIbu: any
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
    this.loadCombo()
    this.init()
  }
  init() {
    this.route.params.subscribe(params => {
      this.params.noRec = params['noRec'];
      this.params.idPasien = params['idPasien'];
      this.params.departemen = params['departemen'];
    });
    this.cacheHelper.set('cacheStatusPasien', undefined);
    this.noCmIbu = this.cacheHelper.get('CacheRegisBayi');
    this.nocmIgd = this.cacheHelper.get('CacheRegisTriage')
    if (this.params.noRec != undefined && this.params.noRec != '-') {
      this.getPasienOnline();
    } else if (this.noCmIbu != undefined) {
      this.isBayi = true;
      this.title = 'Pendaftaran Pasien Bayi';
      this.item.tglLahir = this.now
      this.getPasienBayi();
      this.cacheHelper.set('CacheRegisBayi', undefined)
    } else if (this.params.idPasien != undefined && this.params.idPasien != '-') {
      this.idPasien = parseInt(this.params.idPasien)
      this.title = 'Edit Data Pasien';
      this.editPasien();
    } else if (this.params.departemen != undefined && this.params.departemen != '-') {
      this.isPenunjang = true
    }

    if (this.nocmIgd != undefined) {
      var chacePeriode = this.cacheHelper.get('CacheRegisTriage');
      if (chacePeriode != undefined) {
        this.item.namaPasien = chacePeriode.namaPasien.toUpperCase();
        this.item.alamatLengkap = chacePeriode.alamatlengkap.toUpperCase();
        this.item.tglLahir = moment(chacePeriode.tgllahir).format('YYYY-MM-DD HH:mm');//moment(this.item.tglLahir).format('YYYY-MM-DD HH:mm');//;
        this.item.jenisKelamin = { id: chacePeriode.jkid, jeniskelamin: chacePeriode.jk.toUpperCase() };
        this.item.noHp = chacePeriode.notelepon;
        this.item.noemr = chacePeriode.noemr;
        this.Triage = 'CacheRegisTriage';
      }
    }
  }
  getPasienOnline() {
    this.apiService.get('registrasi/get-pasienonline-bynorec/' + this.params.noRec)
      .subscribe(e => {
        var result = e.data
        if (result.namaibu !=null)
          this.item.namaIbu = result.namaibu.toUpperCase();
        if (result.namaayah!=null)
          this.item.namaAyah = result.namaayah.toUpperCase();
        this.item.namaPasien = result.namapasien.toUpperCase();
        // this.item.pasien.namaPasien = result.namapasien
        if (result.tgllahir != null) {
          this.item.tglLahir = new Date(result.tgllahir)
          this.item.jamLahir = new Date(result.tgllahir)
        }
        if (result.tempatlahir!=null)
          this.item.tempatLahir = result.tempatlahir.toUpperCase();
        if (result.objectjeniskelaminfk!=null)
          this.item.jenisKelamin = { id: result.objectjeniskelaminfk, jeniskelamin: result.jeniskelamin.toUpperCase() };
        if (result.objectagamafk!=null)
          this.item.agama = { id: result.objectagamafk, agama: result.agama.toUpperCase(), namadukcapil: result.agama.toUpperCase() };
        if (result.objectstatusperkawinanfk!=null)
          this.item.statusPerkawinan = { id: result.objectstatusperkawinanfk, statusperkawinan: result.statusperkawinan.toUpperCase(), namadukcapil: result.statusperkawinan.toUpperCase() };
        if (result.objectpendidikanfk!=null)
          this.item.pendidikan = { id: result.objectpendidikanfk, pendidikan: result.pendidikan.toUpperCase(), namadukcapil: result.pendidikan.toUpperCase() };
        if (result.objectpekerjaanfk!=null)
          this.item.pekerjaan = { id: result.objectpekerjaanfk, pekerjaan: result.pekerjaan.toUpperCase(), namadukcapil: result.pekerjaan.toUpperCase() };
        if (result.noidentitas!=null)
          this.item.noIdentitas = result.noidentitas;
        if (result.nobpjs!=null)
          this.item.noBpjs = result.nobpjs;
        if (result.namasuamiistri!=null)
          this.item.namaSuamiIstri = result.namasuamiistri;
        if (result.alamatlengkap!=null)
          this.item.alamatLengkap = result.alamatlengkap;
        if (result.notelepon!=null)
          this.item.noTelepon = result.notelepon;
        if (result.nohp!=null)
          this.item.noHp = result.noaditional;
        this.reservasi = result
        this.item.noRecReservasi = result.norec
        this.item.noReservasi = result.noreservasi
        if (this.item.kodePos != undefined) {
          this.findKodePos(this.item.kodePos);

        }
      }, function (error) {
      })
  }
  getPasienBayi() {

    this.apiService.get("registrasi/get-bynocm?noCm=" + this.noCmIbu).subscribe(e => {

      var result = e.data
      if (result.foto!=null)
        this.item.image = result.foto
      this.item.noCmIbu = result.nocm;
      this.item.namaIbu = result.namapasien.toUpperCase()
      this.item.namaPasien = result.namapasien.toUpperCase() + " By Ny";
      this.item.alamatLengkap = result.alamatlengkap.toUpperCase()
      this.item.tempatLahir = result.tempatlahir.toUpperCase();
      this.item.jenisKelamin = { id: result.objectjeniskelaminfk, jeniskelamin: result.jeniskelamin.toUpperCase() }
      if (result.objectagamafk!=null)
        this.item.agama = { id: result.objectagamafk, agama: result.agama.toUpperCase() }
      // this.item.kebangsaan = { id: this.listKebangsaan._data[1].id, name: this.listKebangsaan._data[1].name }
      if (result.kodepos!=null)
        this.item.kodePos = result.kodepos;
      this.idIbu = result.nocmfk;
      if (result.namasuamiistri!=null)
        this.item.namaAyah = result.namasuamiistri;
      if (result.namasuamiistri!=null)
        this.item.namaKeluarga = result.namasuamiistri;
      if (result.notelepon!=null)
        this.item.noTelepon = result.notelepon;
      if (result.nohp!=null)
        this.item.noHp = result.nohp;

      if (result.objectdesakelurahanfk!=null) {
        this.listDataKelurahan = []
        this.apiService.get("registrasi/get-desa-kelurahan-paging?iddesakelurahan=" + result.objectdesakelurahanfk).subscribe(re => {
          if (re[0] != undefined) {
            var data = {
              id: re[0].id,
              namadesakelurahan: re[0].namadesakelurahan,
              kodepos: re[0].kodepos,
              namakecamatan: re[0].namakecamatan,
              namakotakabupaten: re[0].namakotakabupaten,
              namapropinsi: re[0].namapropinsi,
              objectkecamatanfk: re[0].objectkecamatanfk,
              objectkotakabupatenfk: re[0].objectkotakabupatenfk,
              objectpropinsifk: re[0].objectpropinsifk,
              desa: re[0].namadesakelurahan,
            }
            this.listDataKelurahan.push(data)
            this.item.desaKelurahan = data
          }
        });
      }
      if (result.objectkecamatanfk!=null)
        this.item.kecamatan = { id: result.objectkecamatanfk, namakecamatan: result.namakecamatan }
      if (result.objectkotakabupatenfk!=null)
        this.item.kotaKabupaten = { id: result.objectkotakabupatenfk, namakotakabupaten: result.namakotakabupaten, }
      if (result.objectpropinsifk!=null)
        this.item.propinsi = { id: result.objectpropinsifk, namapropinsi: result.namapropinsi }

      if (this.item.kodePos != undefined && this.item.kodePos != null) {
        // this.findKodePos(this.item.kodePos);
      } else {

      }

    }, function (error) {
    })
  }
  editPasien() {
    this.apiService.get("registrasi/get-bynocm?idPasien=" + this.params.idPasien).subscribe(e => {

      var result = e.data
      if (result.foto!=null)// && result.foto != 'data:image/jpeg;base64,')
        this.item.image = result.foto
      this.item.noCmIbu = result.nocm;
      this.item.namaPasien = result.namapasien.toUpperCase();
      this.item.alamatLengkap = result.alamatlengkap.toUpperCase()
      this.item.tempatLahir = result.tempatlahir.toUpperCase()
      this.item.tglLahir = new Date(result.tgllahir)
      this.item.jenisKelamin = { id: result.objectjeniskelaminfk, jeniskelamin: result.jeniskelamin.toUpperCase() }
      this.item.agama = { id: result.objectagamafk, agama: result.agama.toUpperCase() }
      this.item.PenanggungJawab = result.penanggungjawab;
      if (result.penanggungjawab!=null)
        this.item.cekPenanggungJawab = true
      else
        this.item.cekPenanggungJawab = false
      this.item.Hubungan = result.hubungankeluargapj;
      this.item.Ktp = result.ktppenanggungjawab;
      this.item.alamatRumah = result.alamatrmh;
      this.item.alamatKantor = result.alamatktr;

      this.item.Bahasa = result.bahasa;
      this.item.TeleponP = result.teleponpenanggungjawab;
      this.item.UmurP = result.umurpenanggungjawab;
      this.item.DokterPengirim = result.dokterpengirim;
      this.item.alamatDokterPengirim = result.alamatdokterpengirim;

      if (result.pekerjaanpenangggungjawab!=null) {
        this.item.pekerjaanP = { id: result.idpek, pekerjaan: result.pekerjaanpenangggungjawab.toUpperCase() };
      }
      if (result.jeniskelaminpenanggungjawab!=null) {
        this.item.jenisKelaminP = { id: result.jkidpenanggungjawab, jeniskelamin: result.jeniskelaminpenanggungjawab.toUpperCase() };
      }
      // this.item.kebangsaan = { id: this.listKebangsaan._data[1].id, name: this.listKebangsaan._data[1].name }
      if (result.kodepos!=null)
        this.item.kodePos = result.kodepos;
      // this.idIbu = result.nocmfk;
      if (result.namaibu!=null)
        this.item.namaIbu = result.namaibu.toUpperCase()
      if (result.namasuamiistri!=null)
        this.item.namaSuamiIstri = result.namasuamiistri.toUpperCase()
      if (result.noidentitas!=null)
        this.item.noIdentitas = result.noidentitas
      if (result.nobpjs!=null)
        this.item.noBpjs = result.nobpjs
      if (result.noasuransilain!=null)
        this.item.noAsuransiLain = result.noasuransilain
      if (result.namaayah!=null)
        this.item.namaAyah = result.namaayah.toUpperCase()
      if (result.namakeluarga!=null)
        this.item.namaKeluarga = result.namakeluarga.toUpperCase()
      if (result.notelepon!=null)
        this.item.noTelepon = result.notelepon;
      if (result.nohp!=null)
        this.item.noHp = result.nohp;
      if (result.objectstatusperkawinanfk!=null)
        this.item.statusPerkawinan = { id: result.objectstatusperkawinanfk, statusperkawinan: result.statusperkawinan.toUpperCase(), namadukcapil: result.statusperkawinan.toUpperCase() }
      if (result.objectpendidikanfk!=null)
        this.item.pendidikan = { id: result.objectpendidikanfk, pendidikan: result.pendidikan.toUpperCase(), namadukcapil: result.pendidikan.toUpperCase() }
      if (result.objectpekerjaanfk!=null)
        this.item.pekerjaan = { id: result.objectpekerjaanfk, pekerjaan: result.pekerjaan.toUpperCase(), namadukcapil: result.pekerjaan.toUpperCase() }
      
        if (result.objectsukufk!=null)
        this.item.suku = { id: result.objectsukufk, suku: result.suku.toUpperCase() }
      if (result.objectgolongandarahfk!=null)
        this.item.golonganDarah = { id: result.objectgolongandarahfk, golongandarah: result.golongandarah.toUpperCase(), namadukcapil: result.golongandarah.toUpperCase() }
      if (result.objectdesakelurahanfk!=null) {
        this.listDataKelurahan = []
        this.apiService.get("registrasi/get-desa-kelurahan-paging?iddesakelurahan=" + result.objectdesakelurahanfk).subscribe(re => {
          if (re[0] != undefined) {
            var data = {
              id: re[0].id,
              namadesakelurahan: re[0].namadesakelurahan,
              kodepos: re[0].kodepos,
              namakecamatan: re[0].namakecamatan,
              namakotakabupaten: re[0].namakotakabupaten,
              namapropinsi: re[0].namapropinsi,
              objectkecamatanfk: re[0].objectkecamatanfk,
              objectkotakabupatenfk: re[0].objectkotakabupatenfk,
              objectpropinsifk: re[0].objectpropinsifk,
              desa: re[0].namadesakelurahan,
            }
            this.listDataKelurahan.push(data)
            this.item.desaKelurahan = data
          }
        });
      }
      if (result.objectkecamatanfk!=null)
        this.item.kecamatan = { id: result.objectkecamatanfk, namakecamatan: result.namakecamatan }
      if (result.objectkotakabupatenfk!=null)
        this.item.kotaKabupaten = { id: result.objectkotakabupatenfk, namakotakabupaten: result.namakotakabupaten, }
      if (result.objectpropinsifk!=null)
        this.item.propinsi = { id: result.objectpropinsifk, namapropinsi: result.namapropinsi }

      // if (this.item.kodePos != undefined && this.item.kodePos != null) {
      //   this.findKodePos(this.item.kodePos);
      // }

    }, function (error) {

    })
  }
  changeKebangsaan(e) {
    if (e == undefined) return;
    if (e.value.name == 'WNI')
      this.item.negara = { id: 0, namanegara: 'INDONESIA' };
    if (e.value.name == 'WNA')
      this.item.negara = {}
  }
  loadCombo() {
    this.listDataJenisKelamin = []
    this.listDataPekerjaan = []
    this.listDataAgama = []
    this.listDataPendidikan = []
    this.listDataStatusPerkawinan = []
    this.listDataPekerjaan = []
    this.listGolonganDarah = []
    this.listSuku = []

    this.listDataKecamatan = []
    this.listDataKotaKabupaten = []
    this.listDataPropinsi = []
    this.listKebangsaan = []
    this.listNegara = []
    this.apiService.get("registrasi/get-combo-registrasi").subscribe(result => {
      this.listDataJenisKelamin = result.jeniskelamin
      this.listDataPekerjaan = result.pekerjaan
      this.listDataAgama = result.agama
      this.listDataPendidikan = result.pendidikan
      this.listDataStatusPerkawinan = result.statusperkawinan
      this.listGolonganDarah = result.golongandarah
      this.listSuku = result.suku
    })

    this.apiService.get("registrasi/get-combo-address").subscribe(result => {
      // this.listDataKecamatan = result.kecamatan
      this.listDataKotaKabupaten = result.kotakabupaten
      this.listDataPropinsi = result.propinsi
      this.listKebangsaan = result.kebangsaan
      this.listNegara = result.negara
      this.item.kebangsaan = result.kebangsaan[0]
      let e = {
        value: this.item.kebangsaan
      }
      this.changeKebangsaan(e)
    })
  }
  filterDesa(event) {
    let query = event.query;
    this.apiService.get("registrasi/get-desa-kelurahan-paging?namadesakelurahan=" + query
      // + "&namakecamatan=" + result.NAMA_KEC
    ).subscribe(re => {
      this.listDataKelurahan = re;
    })
  }
  filterKec(event) {
    let query = event.query;
    this.apiService.get("registrasi/get-kecamatan-part?namakecamatan=" + query
    ).subscribe(re => {
      this.listDataKecamatan = re;
    })
  }

  selectDesa(event) {
    if (event.objectkecamatanfk)
      this.item.kecamatan = { id: event.objectkecamatanfk, namakecamatan: event.namakecamatan }
    if (event.objectkotakabupatenfk)
      this.item.kotaKabupaten = { id: event.objectkotakabupatenfk, namakotakabupaten: event.namakotakabupaten }
    if (event.objectpropinsifk)
      this.item.propinsi = { id: event.objectpropinsifk, namapropinsi: event.namapropinsi }
    if (event.kodepos)
      this.item.kodePos = event.kodepos
  }
  findKodePos(kdPos) {
    if (!kdPos) return;
    this.listDataKelurahan = []
    this.apiService.get('registrasi/get-alamat-bykodepos?kodePos=' + kdPos).subscribe(res => {
      if (res.data.length > 0) {
        var data = {
          id: res.data[0].objectdesakelurahanfk,
          namadesakelurahan: res.data[0].namadesakelurahan,
          kodepos: res.data[0].kodepos,
          namakecamatan: res.data[0].namakecamatan,
          namakotakabupaten: res.data[0].namakotakabupaten,
          namapropinsi: res.data[0].namapropinsi,
          objectkecamatanfk: res.data[0].objectkecamatanfk,
          objectkotakabupatenfk: res.data[0].objectkotakabupatenfk,
          objectpropinsifk: res.data[0].objectpropinsifk,
          desa: res.data[0].namadesakelurahan,
        }
        this.listDataKelurahan.push(data)
        this.item.desaKelurahan = data
        this.item.kecamatan = { id: data.objectkecamatanfk, namakecamatan: data.namakecamatan }
        this.item.kotaKabupaten = { id: data.objectkotakabupatenfk, namakotakabupaten: data.namakotakabupaten }
        this.item.propinsi = { id: data.objectpropinsifk, namapropinsi: data.namapropinsi }
      }
    })
  }
  getNik() {

  }
  cancel() {
    this.item = {}
    window.history.back()
  }
  save() {
    if (this.item.noIdentitas == undefined) {
      this.alertService.error('Info', 'NIK harus di isis')
      return
    }
    if (this.item.namaPasien == undefined) {
      this.alertService.error('Info', 'Nama Pasien harus di isis')
      return
    }
    if (this.item.tempatLahir == undefined) {
      this.alertService.error('Info', 'Tempat Lahir harus di isis')
      return
    }
    if (this.item.tglLahir == undefined) {
      this.alertService.error('Info', 'Tgl Lahir harus di isis')
      return
    }
    if (this.item.jenisKelamin == undefined) {
      this.alertService.error('Info', 'Jenis Kelamin harus di isis')
      return
    }
    if (this.item.alamatLengkap == undefined) {
      this.alertService.error('Info', 'Alamat harus di isis')
      return
    }
    if (this.item.noHp == undefined) {
      this.alertService.error('Info', 'No HP harus di isis')
      return
    }
    var pekerjaanPj = null
    if (this.item.pekerjaanP != undefined) {
      pekerjaanPj = this.item.pekerjaanP.pekerjaan;
    }
    var jeniskelaminP = null
    if (this.item.jenisKelaminP != undefined) {
      jeniskelaminP = this.item.jenisKelaminP.jeniskelamin;
    }


    var postJson = {
      'isbayi': this.isBayi,
      'istriageigd': this.isTriage,
      'isPenunjang': this.isPenunjang,
      'idpasien': this.idPasien != undefined ? this.idPasien : '',
      'pasien': {
        'namaPasien': this.item.namaPasien,
        'noIdentitas': this.item.noIdentitas != undefined ? this.item.noIdentitas : null,
        'namaSuamiIstri': this.item.namaSuamiIstri != undefined ? this.item.namaSuamiIstri : null,
        'noAsuransiLain': this.item.noAsuransiLain != undefined ? this.item.noAsuransiLain : null,
        'noBpjs': this.item.noBpjs != undefined ? this.item.noBpjs : null,
        'noHp': this.item.noHp != undefined ? this.item.noHp : null,
        'tempatLahir': this.item.tempatLahir,
        'namaKeluarga': this.item.namaKeluarga != undefined ? this.item.namaKeluarga : null,
        'tglLahir': moment(this.item.tglLahir).format('YYYY-MM-DD HH:mm'),
        'image': this.item.image != undefined && this.item.image != "../app/images/avatar.jpg" ? this.item.image : null
      },
      'agama': {
        'id': this.item.agama != undefined ? this.item.agama.id : null,
      },
      'jenisKelamin': {
        'id': this.item.jenisKelamin != undefined ? this.item.jenisKelamin.id : null,
      },
      'pekerjaan': {
        'id': this.item.pekerjaan != undefined ? this.item.pekerjaan.id : null,
      },
      'pendidikan': {
        'id': this.item.pendidikan != undefined ? this.item.pendidikan.id : null,
      },
      'statusPerkawinan': {
        'id': this.item.statusPerkawinan != undefined ? this.item.statusPerkawinan.id : 0,
      },
      'golonganDarah': {
        'id': this.item.golonganDarah != undefined ? this.item.golonganDarah.id : null,
      },
      'suku': {
        'id': this.item.suku != undefined ? this.item.suku.id : null,
      },

      'namaIbu': this.item.namaIbu != undefined ? this.item.namaIbu : null,
      'noTelepon': this.item.noTelepon != undefined ? this.item.noTelepon : null,
      'noAditional': this.item.noAditional != undefined ? this.item.noAditional : null,
      'kebangsaan': {
        'id': this.item.kebangsaan != undefined ? this.item.kebangsaan.id : null,
      },
      'negara': {
        'id': this.item.negara != undefined ? this.item.negara.id : null,
      },
      'namaAyah': this.item.namaAyah != undefined ? this.item.namaAyah : null,
      'alamatLengkap': this.item.alamatLengkap,
      'desaKelurahan': {
        'id': this.item.desaKelurahan != undefined ? this.item.desaKelurahan.id : null,
        'namaDesaKelurahan': this.item.desaKelurahan != undefined ? this.item.desaKelurahan.namadesakelurahan : null,
      },
      'kecamatan': {
        'id': this.item.kecamatan != undefined ? this.item.kecamatan.id : null,
        'namaKecamatan': this.item.kecamatan != undefined ? this.item.kecamatan.namakecamatan : null,
      },
      'kotaKabupaten': {
        'id': this.item.kotaKabupaten != undefined ? this.item.kotaKabupaten.id : null,
        'namaKotaKabupaten': this.item.kotaKabupaten != undefined ? this.item.kotaKabupaten.namakotakabupaten : null,
      },
      'propinsi': {
        'id': this.item.propinsi != undefined ? this.item.propinsi.id : null,
      },
      'kodePos': this.item.kodePos != undefined ? this.item.kodePos : null,
      'penanggungjawab': this.item.PenanggungJawab != undefined ? this.item.PenanggungJawab : null,
      'hubungankeluargapj': this.item.Hubungan != undefined ? this.item.Hubungan : null,
      'pekerjaanpenangggungjawab': pekerjaanPj,
      'ktppenanggungjawab': this.item.Ktp != undefined ? this.item.Ktp : null,
      'alamatrmh': this.item.alamatRumah != undefined ? this.item.alamatRumah : null,
      'alamatktr': this.item.alamatKantor != undefined ? this.item.alamatKantor : null,
      'teleponpenanggungjawab': this.item.TeleponP != undefined ? this.item.TeleponP : null,
      'bahasa': this.item.Bahasa != undefined ? this.item.Bahasa : null,
      'jeniskelaminpenanggungjawab': this.item.jenisKelaminP != undefined ? this.item.jenisKelaminP.jeniskelamin : null,
      'umurpenanggungjawab': this.item.UmurP != undefined ? this.item.UmurP : null,
      'dokterpengirim': this.item.DokterPengirim != undefined ? this.item.DokterPengirim : null,
      'alamatdokter': this.item.alamatDokterPengirim != undefined ? this.item.alamatDokterPengirim : null
    }
    this.apiService.post('registrasi/save-pasien-fix', postJson)
      .subscribe(
        res => {

          if (this.item.noRecReservasi != undefined) {
            var jsonss = {
              "norec": this.item.noRecReservasi,
              "nocmfk": res.data.data.kodeexternal
            }

            this.apiService.post('reservasi/update-nocmfk-antrian-registrasi', jsonss).subscribe(e => {
            })
          }
          var umur: any = this.dateHelper.CountAge(new Date(res.data.tgllahir), this.now);
          var bln = umur.month,
            thn = umur.year,
            day = umur.day
          var umur: any = thn + ' thn ' + bln + ' bln ' + day + ' hr '
          if (this.Triage != "" || this.Triage != undefined) {
            this.InputTriage();
          }
          this.cacheHelper.set('CacheRegisBayi', undefined);

          if (this.title == 'Pendaftaran Pasien') {
            this.Next()
          }

        },
        function (err) { })
  }
  InputTriage() {

  }
  Next() {

  }
}
