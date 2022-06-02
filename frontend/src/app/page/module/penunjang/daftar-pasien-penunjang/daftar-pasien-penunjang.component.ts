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
  selector: 'app-daftar-pasien-penunjang',
  templateUrl: './daftar-pasien-penunjang.component.html',
  styleUrls: ['./daftar-pasien-penunjang.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPasienPenunjangComponent implements OnInit {
  selected: any;
  dataTable: any[];
  dateNow: any;
  disableTgl: boolean
  column: any[];
  disableVer: boolean
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  listRuanganApd: any[];
  listRuanganTujuan: any[];
  listLayanan: any[] = [];
  listKomponen: any[];
  dataLogin: any;
  kelUser: any;
  item: any = {
    pasien: {}
  };
  popFilter: boolean
  pop_GolonganDarah: boolean;
  listGolonganDarah: any[];
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
    this.dataLogin = this.authService.dataLoginUser;
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'expertise', header: 'Exp', width: "85px" },
      { field: 'tglmasuk', header: 'Tgl Masuk', width: "140px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "140px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "200px" },
      { field: 'jeniskelamin', header: 'Jenis Kelamin', width: "140px" },
      { field: 'golongandarah', header: 'Gol Darah', width: "120px" },
      { field: 'umur', header: 'Umur', width: "120px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "140px" },
      { field: 'namakelas', header: 'Kelas', width: "120px" },
      { field: 'pegawaiorder', header: 'Petugas', width: "180px" },
      { field: 'alamatlengkap', header: 'Alamat', width: "200px" },
    ];
    this.getDataCombo();
  }

  getDataCombo() {
    this.apiService.get("penunjang/get-data-combo-penunjang").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.listGolonganDarah = dataCombo.goldarah;
      // this.listDokter = dataCombo.dokter;
    });
    this.apiService.get("penunjang/get-data-ruang-tujuan?keluseridfk=" + this.dataLogin.kelompokUser.id).subscribe(table => {
      var dataCombo = table;
      this.listRuanganTujuan = dataCombo.ruangan;
      // this.item.dataRuanganTujuan = dataCombo.ruangan[0];
      this.LoadCache();
    })
  }

  LoadCache() {
    this.item.jmlRows = 50;
    var chacePeriode = this.cacheHelper.get('daftarPasienPenunjangCache');
    if (chacePeriode != undefined) {
      // this.item.tglAwal = new Date(chacePeriode[0]);
      // this.item.tglAkhir = new Date(chacePeriode[1]);      
      if (chacePeriode[2] != undefined) {
        this.listKelompokPasien = [chacePeriode[2]]
        this.item.dataKelPasien = chacePeriode[2]
      }
      if (chacePeriode[5] != undefined) {
        this.listDepartemen = [chacePeriode[5]]
        this.item.dataDepartemen = chacePeriode[5]
      }
      if (chacePeriode[4] != undefined) {
        this.listRuangan = [chacePeriode[4]]
        this.item.dataRuangan = chacePeriode[4]
      }
      if (chacePeriode[3] != undefined) {
        this.listRuanganTujuan = [chacePeriode[3]]
        this.item.dataRuanganTujuan = chacePeriode[3]
      }
      this.getData();
    }
    else {
      this.getData();
    }
  }

  cari() {
    this.getData()
  }

  getData() {
    var ins = ""
    var tempInstalasiIdArr = undefined;
    if (this.item.dataDepartemen != undefined) {
      var ins = "&deptId=" + this.item.dataDepartemen.id
      tempInstalasiIdArr = { id: this.item.dataDepartemen.id, departemen: this.item.dataDepartemen.departemen }
    }
    var rg = ""
    var tempRuanganIdArr = undefined
    if (this.item.dataRuangan != undefined) {
      var rg = "&ruangId=" + this.item.dataRuangan.id
      tempRuanganIdArr = { id: this.item.dataRuangan.id, ruangan: this.item.dataRuangan.ruangan }
    }

    var kp = ""
    var tempKelompokPasienArr = undefined
    if (this.item.dataKelPasien != undefined) {
      var kp = "&kelId=" + this.item.dataKelPasien.id;
      tempKelompokPasienArr = { id: this.item.dataKelPasien.id, kelompokpasien: this.item.dataKelPasien.kelompokpasien }
    }

    var rt = ""
    var tempRuangTujuanArr = undefined
    if (this.item.dataRuanganTujuan != undefined) {
      var rt = "&ruangTujuan=" + this.item.dataRuanganTujuan.id;
      tempRuangTujuanArr = { id: this.item.dataRuanganTujuan.id, namaruangan: this.item.dataRuanganTujuan.namaruangan }
    }

    var reg = ""
    if (this.item.Noregistrasi != undefined) {
      var reg = "&noregistrasi=" + this.item.Noregistrasi
    }
    var rm = ""
    if (this.item.noRM != undefined) {
      var rm = "&nocm=" + this.item.noRM
    }
    var nm = ""
    if (this.item.namaPasien != undefined) {
      var nm = "&namapasien=" + this.item.namaPasien
    }
    var jmlRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = "&jmlRow=" + this.item.jmlRow
    }

    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm:ss');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm:ss');
    var KelUserid = "&KelUserid=" + this.dataLogin.kelompokUser.id;
    var KelUser = "&KelUser=" + this.dataLogin.kelompokUser.kelompokUser;
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
      2: tempKelompokPasienArr,
      3: tempRuangTujuanArr,
      4: tempRuanganIdArr,
      5: tempInstalasiIdArr,
    }

    this.cacheHelper.set('daftarPasienPenunjangCache', chacePeriode);
    this.apiService.get("penunjang/get-daftar-pasien-penunjang?" +
      "tglAwal=" + tglAwal +
      "&tglAkhir=" + tglAkhir +
      reg + rm + nm + ins + rg + rt + kp + KelUserid + KelUser + jmlRow).subscribe(table => {
        var data = table.data;
        for (let i = 0; i < data.length; i++) {
          const element = data[i];
          element.no = i + 1;
          var now = moment(new Date(element.tglregistrasi)).format('YYYY-MM-DD');
          var tgllahir = moment(new Date(element.tgllahir)).format('YYYY-MM-DD');
          var umur = this.dateHelper.CountAge(new Date(tgllahir), new Date(now));
          element.umur = element.umur = umur.year + ' thn ' + umur.month + ' bln ' + umur.day + ' hari';;
          if (element.expertise == true) {
            element.expertise = "✔";
          } else {
            element.expertise = "✘";
          }
          element.durasi = '-'
          if (element.tglexpertise != null) {
            var awal = moment(new Date(element.tglmasuk)).format('YYYY-MM-DD HH:mm');
            var akhir = moment(new Date(element.tglexpertise)).format('YYYY-MM-DD HH:mm');
            var durasi = this.dateHelper.CountDifferenceDayHourMinute(new Date(akhir), new Date(awal));
            if (new Date(element.tglexpertise) < new Date(element.tglmasuk)) {
              element.durasi = durasi;
            } else
              element.durasi = durasi;
          }
        }
        this.dataTable = data;
      });
  }

  onRowSelect(event: any) {
    if (event.data != undefined) {
      this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
        if (data.length > 0) {
          this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
          return;
        } else {
          this.selected = event.data
        }
      })
    }
  }

  ubahGolDarah() {
    if (this.selected == undefined) {
      this.alertService.warn("Peringatan!", "Data Belum Dipilih!");
      return;
    }
    this.pop_GolonganDarah = true;
  }
  ubahGolDarah2(e) {
    this.selected = e
    this.pop_GolonganDarah = true;
  }


  batalGolDarah() {
    this.item.golDarah = undefined;
    this.pop_GolonganDarah = false;
  }

  simpanGolDarah() {
    if (this.selected == undefined) {
      this.alertService.warn("Peringatan!", "Data Belum Dipilih!");
      return;
    }

    if (this.item.golDarah == undefined) {
      this.alertService.warn("Peringatan!", "Golongan Darah Belum Dipilih!");
      return;
    }

    var objSave = {
      norm: this.selected.norm,
      golongandarahidfk: this.item.golDarah.id
    }

    this.apiService.post('penunjang/update-gol-darah', objSave).subscribe(data => {
      this.item.golDarah = undefined;
      this.pop_GolonganDarah = false;
      this.getData();
    });
  }
  pengkajianMedis2(e) {

    this.apiService.get("registrasi/get-apd?noregistrasi="
      + e.noregistrasi
      + "&objectruanganlastfk=" + e.ruid
    ).subscribe(data => {
      var dataAntrian = data.data;
      if (dataAntrian != undefined) {
        this.router.navigate(['rekam-medis', e.norec, dataAntrian.norec_apd])
      }
    })
  }
  pengkajianMedis() {
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }
    this.apiService.get("registrasi/get-apd?noregistrasi="
      + this.selected.noregistrasi
      + "&objectruanganlastfk=" + this.selected.ruid
    ).subscribe(data => {
      var dataAntrian = data.data;
      if (dataAntrian != undefined) {
        this.router.navigate(['rekam-medis', this.selected.norec, dataAntrian.norec_apd])
      }
    })
  }
  RincianPenunjang2(e) {

    this.router.navigate(['rincian-penunjang', e.norec_pd, e.norec_apd])
  }
  RincianPenunjang() {
    if (this.selected == undefined) {
      this.alertService.warn("Info!", "Data Belum Dipilih");
      return;
    }
    this.router.navigate(['rincian-penunjang', this.selected.norec_pd, this.selected.norec_apd])
  }
  isiRuangan(e) {
    this.listRuangan = e.value.ruangan
  }

  filter() {
    this.popFilter = true
  }
  cariFilter() {
    this.popFilter = false
    this.getData();
  }
  clearFilter() {
    this.popFilter = false
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.getData();
  }


}
