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
  selector: 'app-daftar-konsultasi',
  templateUrl: './daftar-konsultasi.component.html',
  styleUrls: ['./daftar-konsultasi.component.scss']
})
export class DaftarKonsultasiComponent implements OnInit {
  item: any = {
  }
  disableTgl: boolean
  selectedData: any[]
  listRuangan: any = []
  column: any[]
  dataSource: any[]
  selected: any
  pop_daftarOrder: boolean
  pop_DokterPJawab: boolean
  listDokter: any[]
  kelompokUser: any
  lengthKonsul: number = 0
  columnKonsul: any[]
  dataSourceKonsul: any[] = []
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.kelompokUser = this.authService.getKelompokUser()
    this.loadCombo()
    this.apiService.get("emr/get-ruangan-konsul"
    ).subscribe(re => {
      this.listRuangan = re;

    })
    this.column = [
      { field: 'statlayanan', header: '✔', width: "60px" },
      { field: 'no', header: 'No', width: "60px" },
      { field: 'tglregistrasi', header: 'Tgl Konsul', width: "150px" },
      { field: 'noregistrasi', header: 'No Registrasi', width: "150px" },
      { field: 'nocm', header: 'No RM', width: "100px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'umurzz', header: 'Umur', width: "100px" },
      { field: 'jeniskelamin', header: 'JK', width: "80px" },
      { field: 'statuspasien', header: 'Status', width: "100px" },
      { field: 'namadokter', header: 'Dokter', width: "180px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "120px" },
      { field: 'namakelas', header: 'Kelas', width: "120px" },
      { field: 'alamatlengkap', header: 'Alamat', width: "200px" },
      { field: 'stts', header: 'Panggil', width: "100px" },
      { field: 'isonline', header: 'Online', width: "100px" },
    ];
    this.columnKonsul = [
      { field: 'no', header: 'No', width: "60px" },
      { field: 'noregistrasi', header: 'No Registrasi', width: "100px" },
      { field: 'nocm', header: 'No RM', width: "100px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "200px" },
      { field: 'tglorder', header: 'Tgl Order', width: "100px" },
      { field: 'ruanganasal', header: 'Ruang Asal', width: "150px" },
      { field: 'ruangantujuan', header: 'Ruang Tujuan', width: "150px" },
      { field: 'namalengkap', header: 'Dokter', width: "150px" },
      { field: 'pengonsul', header: 'Pengonsul', width: "150px" },
      { field: 'keteranganorder', header: 'Ket', width: "180px" },
      { field: 'status', header: 'Status', width: "100px" },

    ];
  }
  loadCombo() {
    var chacePeriode = this.cacheHelper.get('cache_DaftarKONSUL');
    if (chacePeriode != undefined) {
      this.item.periodeAwal = new Date(chacePeriode[0]);
      this.item.periodeAkhir = new Date(chacePeriode[1]);
      if (chacePeriode[4] != undefined && chacePeriode[4].length > 0) {
        this.item.ruanganMulti = chacePeriode[4]
      }
    } else {
      this.item.periodeAwal = moment(new Date()).format('YYYY-MM-DD 00:00');
      this.item.periodeAkhir = moment(new Date()).format('YYYY-MM-DD 23:59');
    }
    this.loadData()
    this.countKonsul()
  }
  countKonsul() {
    let idDokter = ''
    if (this.kelompokUser == 'dokter') {
      idDokter = this.authService.getPegawaiId()
    }
    this.apiService.get("emr/count-order-konsul?dokterid=" + idDokter).subscribe(e => {
      this.lengthKonsul = parseFloat(e.data)
    })
  }
  verif(dataItem) {
    if (dataItem.status == 'Selesai') {
      this.alertService.error('Info', 'Sudah di verifkasi')
      return
    }
    var length = this.dataSourceKonsul.length + 1;
    var dataKonsul = {
      "kelasfk": 6,
      "noantrian": length,
      "norec_so": dataItem.norec,
      "norec_pd": dataItem.norec_pd,
      "dokterfk": dataItem.pegawaifk,
      "objectruangantujuanfk": dataItem.objectruangantujuanfk,
      "objectruanganasalfk": dataItem.objectruanganfk,
      // "jawaban" :jawabanKonsul
    }
    this.apiService.post('emr/save-konsul-from-order', dataKonsul).subscribe(e => {
      this.loadData();
      this.countKonsul()
      this.pop_daftarOrder = false

    })
  }
  
  konsultasi() {
    this.pop_daftarOrder = true
    let idDokter = ''
    if (this.kelompokUser == 'dokter') {
      idDokter = this.authService.getPegawaiId()
    }
    this.apiService.get("emr/get-order-konsul?dokterid=" + idDokter).subscribe(e => {
      var result = e.data
      if (result.length > 0) {
        for (var i = 0; i < result.length; i++) {
          result[i].no = i + 1
          if (result[i].norec_apd != null)
            result[i].status = 'Selesai'
          else
            result[i].status = '-'
        }
      }
      this.dataSourceKonsul = result
    })

  }
  cari() {
    this.loadData()
  }
  loadData() {
    var tglAwal = moment(this.item.periodeAwal).format('YYYY-MM-DD HH:mm:ss');
    var tglAkhir = moment(this.item.periodeAkhir).format('YYYY-MM-DD HH:mm:ss');

    var nocm = ""
    if (this.item.noCm != undefined) {
      var nocm = "&norm=" + this.item.noCm
    }
    var nama = ""
    if (this.item.namaPasien != undefined) {
      var nama = "&nama=" + this.item.namaPasien
    }
    var noRegistrasi = ""
    if (this.item.noRegistrasi != undefined) {
      var noRegistrasi = "&noreg=" + this.item.noRegistrasi
    }

    var ruangId = ""
    if (this.item.ruangan != undefined) {
      var ruangId = "&ruangId=" + this.item.ruangan.id
    }
    var listRuangan = ""

    if (this.item.ruanganMulti != undefined && this.item.ruanganMulti.length != 0) {
      var a = ""
      var b = ""
      for (var i = this.item.ruanganMulti.length - 1; i >= 0; i--) {

        var c = this.item.ruanganMulti[i].id
        b = "," + c
        a = a + b
      }
      listRuangan = a.slice(1, a.length)
    }
    var jmlRow = ""
    if (this.item.rows != undefined) {
      jmlRow = "&jmlRow=" + this.item.rows
    }

    this.apiService.get("emr/get-daftar-konsul-from-order?" +
      "&tglAwal=" + tglAwal +
      "&tglAkhir=" + tglAkhir +
      "&norm=" + nocm +
      "&noreg=" + noRegistrasi +
      "&nama=" + nama +
      "&ruanganArr=" + listRuangan
      + ruangId
      + jmlRow).subscribe(datas => {
        for (var i = 0; i < datas.length; i++) {
          datas[i].no = i + 1
          var tanggal = new Date()
          var tanggalLahir = new Date(datas[i].tgllahir);
          datas[i].umurzz = this.dateHelper.getUmur(tanggalLahir, tanggal);
          datas[i].stts = '-'
          datas[i].isonline = '-'
          if (datas[i].noreservasi != undefined) {
            datas[i].isonline = 'Online'
            datas[i].tglregistrasi = datas[i].tanggalreservasi
          }
          if (datas[i].tgldipanggilsuster != undefined) {
            datas[i].stts = 'Di Panggil Perawat'
          }
          if (datas[i].tgldipanggildokter != undefined) {
            datas[i].stts = 'Di Panggil Dokter'
          }
          if (datas[i].statuslayanan == 'true' || datas[i].statuslayanan == true) {
            datas[i].statlayanan = "✔"
          } else {
            datas[i].statlayanan = ""
          }
        }
        this.dataSource = datas
        var chacePeriode = {
          0: tglAwal,
          1: tglAkhir,
          2: this.item.ruangan != undefined ? this.item.ruangan.id : null,
          3: this.item.ruangan != undefined ? this.item.ruangan.namaruangan : null,
          4: this.item.ruanganMulti,
        }
        // var chacePeriode = tglAwal + "~" + tglAkhir + "~" + this.item.ruangan.id + "~" + this.item.ruangan.namaruangan;
        this.cacheHelper.set('cache_DaftarKONSUL', chacePeriode);
      });
  }

  filterDokter(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listDokter = re;
    })
  }

  popUpUbahDokter() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.pop_DokterPJawab = true;
      }
    })
  }
  popUpUbahDokter2(e) {

    this.apiService.get("general/get-data-closing-pasien/" + e.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.pop_DokterPJawab = true;
      }
    })
  }

  batalDokter() {
    this.item.dokterPJawab = undefined;
    this.pop_DokterPJawab = false;
  }

  simpanDokter() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.item.dokterPJawab == undefined) {
      this.alertService.warn("Info,", "Data Dokter Belum Dipilih!");
      return;
    }

    var objSave = {
      norec_apd: this.selected.norec_apd,
      pegawaiidfk: this.item.dokterPJawab.id
    }

    this.apiService.post('emr/ubah-dokter', objSave).subscribe(e => {
      if (this.selected.norec != '') {
        this.apiService.postLog('Simpan Ubah Dokter', 'norec Registrasi Pasien', this.selected.norec, 'Ubah Ke Dokter  '
          + this.item.dokterPJawab.namalengkap + ' pada No Registrasi ' + this.selected.noregistrasi).subscribe(z => { })
      }
      this.item.dokterPJawab = undefined;
      this.pop_DokterPJawab = false;
      this.loadData();
    })

  }
  detailTagihan() {
    this.router.navigate(['detail-tagihan', this.selected.noregistrasi])
  }
  pengkajianMedis2(e) {
  
    this.router.navigate(['rekam-medis',e.norec_pd, e.norec_apd])

  }
  pengkajianMedis() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Pilih data dulu!");
      return;
    }
    this.router.navigate(['rekam-medis', this.selected.norec_pd, this.selected.norec_apd])

  }
}
