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
  selector: 'app-daftar-pasien-dirawat',
  templateUrl: './daftar-pasien-dirawat.component.html',
  styleUrls: ['./daftar-pasien-dirawat.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarPasienDirawatComponent implements OnInit {
  page: number;
  rows: number;
  selected: any;
  dataTable: any[];
  column: any[];
  item: any = {}
  dataLogin: any;
  kelUser: any;
  dateNow: any;
  listDepartemen: any[];
  listRuangan: any[];
  listKelompokPasien: any[];
  isbatalPindah: boolean;
  isbatalRawat: boolean;
  pop_MasukKamar: boolean;
  listKamar: any[];
  listTempatTidur: any[];
  pop_DokterPJawab: boolean
  listDokter: any[] = []
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
  ) {
    this.page = Config.get().page;
    this.rows = Config.get().rows;
  }

  ngOnInit() {
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser.kelompokUser;
    this.dateNow = new Date();
    this.item.tglAwal = moment(this.dateNow).format('YYYY-MM-DD 00:00');
    this.item.tglAkhir = moment(this.dateNow).format('YYYY-MM-DD 23:59');
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namadokter', header: 'Dokter', width: "200px" },
      { field: 'namakelas', header: 'Kelas', width: "100px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'namakamar', header: 'Kamar', width: "120px" },
      { field: 'namabed', header: 'Bed', width: "120px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "120px" },
      { field: 'namarekanan', header: 'Penjamin', width: "140px" },
      { field: 'noasuransi', header: 'No Asuransi', width: "120px" },
      { field: 'nosep', header: 'SEP', width: "120px" },
      { field: '', header: 'Lama Rawat', width: "120px" },
    ];
    this.getDataCombo();
    this.LoadCache();
  }

  getDataCombo() {
    this.apiService.get("rawatinap/get-combo").subscribe(table => {
      var dataCombo = table;
      // this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;
      this.listRuangan = dataCombo.ruanganinap;

    })
  }

  isiRuangan() {
    if (this.item.dataDepartemen != undefined) {
      this.listRuangan = this.item.dataDepartemen.ruangan;
    }
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('cachePasienDirawatCtrl');
    if (chacePeriode != undefined) {
      // this.item.jmlRows = chacePeriode[0];
      this.item.noRm = chacePeriode[2];
      this.item.noReg = chacePeriode[3];
    }
    this.getData();
  }

  getData() {
    var rg = ""
    var tempRuanganArr = {}
    if (this.item.dataRuangan == undefined) {
      rg = ""
    } else {
      rg = "&ruanganId=" + this.item.dataRuangan.id
      tempRuanganArr = { id: this.item.dataRuangan.id, namaruangan: this.item.dataRuangan.namaruangan }
    }

    var reg = ""
    var tempNoREG = ""
    if (this.item.noReg == undefined) {
      reg = ""
      tempNoREG = ""
    } else {
      reg = "&noReg=" + this.item.noReg
      tempNoREG = this.item.noReg
    }

    var rm = ""
    var tempRM = ""
    if (this.item.noRM == undefined) {
      rm = ""
      tempRM = ""
    } else {
      rm = "&noRm=" + this.item.noRM
      tempRM = this.item.noRM
    }

    var nm = ""
    if (this.item.namaPasien == undefined) {
      nm = ""
    } else {
      nm = "namaPasien=" + this.item.namaPasien
    }

    var noregistrasi = ""
    if (this.item.Noregistrasi != undefined) {
      noregistrasi =  "&noReg=" +  this.item.Noregistrasi
    }

    var jmlRow = ""
    var tempRow = ""
    if (this.item.jmlRows != undefined) {
      jmlRow = this.item.jmlRows
      tempRow = this.item.tempRow
    }

    var chacePeriode = {
      0: tempRow,
      1: tempRM,
      2: tempNoREG,
    }
    this.cacheHelper.set('cachePasienDirawatCtrl', chacePeriode);
    this.apiService.get("rawatinap/get-daftar-pasien-masih-dirawat?"
      + nm + reg + rg + rm + noregistrasi + "&jmlRow=" + jmlRow
    ).subscribe(table => {
      var data = table.data;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
      }
      this.dataTable = data;
    });
  }

  cari() {
    this.getData();
  }


  onRowSelect(event: any) {
    this.selected = event.data
  }

  detailRegistrasi() {
    if (this.selected != undefined) {
      this.router.navigate(['detail-registrasi-pasien', this.selected.noregistrasi]);
    } else {
      this.alertService.warn("Info", "Data Belum Dipilih!");
      return;
    }
  }
  detailRegistrasi2(e) {
    this.router.navigate(['detail-registrasi-pasien', e.noregistrasi]);
  }
  pindahPulang2(e) {
    this.apiService.get("general/get-data-closing-pasien/" + e.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.router.navigate(['pindah-pulang', e.norec_pd, e.norec_apd])
      }
    })
  }
  pindahPulang() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.router.navigate(['pindah-pulang', this.selected.norec_pd, this.selected.norec_apd])
      }
    })
  }

  batalPindah() {
    this.isbatalPindah = true;
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    var objSave = {
      data: this.selected.dataPasienSelected
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {

        this.apiService.post('rawatinap/save-batal-pindah-ruangan', objSave).subscribe(data => {
          this.apiService.postLog('Batal Pindah Ruangan', 'norec Daftar Pasien Ruangan', this.selected.norec_apd, ' pada No Registrasi ' + this.selected.noregistrasi).subscribe(z => { })
          this.isbatalPindah = false;
          this.getData();
        }, function (error) {
          this.isbatalPindah = false;
        });

      }
    })
  }
  batalPindah2(e) {
    this.isbatalPindah = true;
  
    var objSave = {
      data: e.dataPasienSelected
    }

    this.apiService.get("general/get-data-closing-pasien/" + e.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {

        this.apiService.post('rawatinap/save-batal-pindah-ruangan', objSave).subscribe(data => {
          this.apiService.postLog('Batal Pindah Ruangan', 'norec Daftar Pasien Ruangan', e.norec_apd, ' pada No Registrasi ' + e.noregistrasi).subscribe(z => { })
          this.isbatalPindah = false;
          this.getData();
        }, function (error) {
          this.isbatalPindah = false;
        });

      }
    })
  }
  batalRawat() {
    this.isbatalRawat = true;
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    var objSave = {
      data: this.selected.dataPasienSelected
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.apiService.post('rawatinap/save-batal-rawat-inap', objSave).subscribe(data => {
          this.apiService.postLog('Batal Rawat Inap', 'norec Daftar Pasien Ruangan', this.selected.norec_apd, ' pada No Registrasi ' + this.selected.noregistrasi).subscribe(z => { })
          this.isbatalRawat = false;
          this.getData();
        }, function (error) {
          this.isbatalRawat = false;
        });
      }
    })
  }
  batalRawat2(e) {
    this.isbatalRawat = true;
  

    var objSave = {
      data: e.dataPasienSelected
    }

    this.apiService.get("general/get-data-closing-pasien/" +e.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.apiService.post('rawatinap/save-batal-rawat-inap', objSave).subscribe(data => {
          this.apiService.postLog('Batal Rawat Inap', 'norec Daftar Pasien Ruangan', e.norec_apd, ' pada No Registrasi ' + e.noregistrasi).subscribe(z => { })
          this.isbatalRawat = false;
          this.getData();
        }, function (error) {
          this.isbatalRawat = false;
        });
      }
    })
  }

  inputDeposit(selected) {
    if (this.kelUser != 'kasir') {
      this.alertService.warn("Info,", "Hanya Untuk Kasir!");
      return;
    }
    if (selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.router.navigate(['penyetoran-deposit', selected.norec_pd]);
      }
    })
  }

  getKamar() {
    debugger;
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    if (this.selected.ruanganidfk == undefined && this.selected.kelasidfk == undefined) return;
    var kelasIds = "idKelas=" + this.selected.kelasidfk
    var ruanganIds = "&idRuangan=" + this.selected.ruanganidfk
    this.apiService.get("rawatinap/get-kamarbyruangankelas?" + kelasIds + ruanganIds)
      .subscribe(data => {
        var datas = data;
        this.listKamar = datas.kamar
        this.item.dataTempatTidur = datas.kamar[0];
      });
  }

  masukKamar() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }
    this.apiService.get("general/get-data-closing-pasien/" + this.selected.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.getKamar();
        this.pop_MasukKamar = true;
      }
    })
  }
  masukKamar2(e) {
    
    this.apiService.get("general/get-data-closing-pasien/" +e.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.selected = e
        this.getKamar2(e);
        this.pop_MasukKamar = true;
      }
    })
  }
  getKamar2(e) {
   
    if (e.ruanganidfk == undefined &&e.kelasidfk == undefined) return;
    var kelasIds = "idKelas=" +e.kelasidfk
    var ruanganIds = "&idRuangan=" + e.ruanganidfk
    this.apiService.get("rawatinap/get-kamarbyruangankelas?" + kelasIds + ruanganIds)
      .subscribe(data => {
        var datas = data;
        this.listKamar = datas.kamar
        this.item.dataTempatTidur = datas.kamar[0];
      });
  }

  changeKamar(event) {
    var data = event;
    if (data != undefined) {
      var kamarId = data.id;
      this.listTempatTidur=[]
      this.apiService.get("rawatinap/get-nobedbykamar?idKamar=" + kamarId).subscribe(data => {
        var datas = data.bed;
         for (let i = 0; i < datas.length; i++) {
            const element = datas[i];
            if (element.statusbed == "KOSONG") {
              this.listTempatTidur.push(element)
            }
          }
        // this.listTempatTidur = datas;
      })
    }
  }

  batalMasukKamar() {
    this.item.dataKamar = undefined;
    this.item.dataTempatTidur = undefined;
    this.pop_MasukKamar = false;
  }

  simpanMasukKamar() {
    var json = {
      'norec_pd': this.selected.norec_pd,
      'ruanganlastfk': this.selected.ruanganlastidfk,
      'objectkamarfk': this.item.dataKamar.id,
      'nobed': this.item.dataTempatTidur.id,
      'nobedasal': this.selected.nobedidfk,

    }
    this.apiService.post("rawatinap/update-kamar", json).subscribe(data => {
      this.apiService.postLog('Simpan Masuk Kamar', 'norec Registrasi Pasien', this.selected.norec_pd, 'Simpan Masuk Kamar, Ke Kamar '
        + this.item.dataKamar.namakamar + ' Dengan No Bed ' + this.item.dataTempatTidur.reportdisplay + ' pada No Registrasi ' + this.selected.noregistrasi).subscribe(z => { })
      this.item.dataKamar = undefined;
      this.item.dataTempatTidur = undefined;
      this.getData();
      this.pop_MasukKamar = false;
    });
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
      this.getData();
    })

  }
  detailTagihan() {
    this.router.navigate(['detail-tagihan', this.selected.noregistrasi])
  }
  detailTagihan2(e) {
    this.router.navigate(['detail-tagihan', e.noregistrasi])
  }
  pengkajianMedis2(e) {
   
    this.router.navigate(['rekam-medis', e.norec_pd, e.norec_apd])

  }
  pengkajianMedis() {
    if (this.selected == undefined) {
      this.alertService.warn("Info,", "Pilih data dulu!");
      return;
    }
    this.router.navigate(['rekam-medis', this.selected.norec_pd, this.selected.norec_apd])

  }
  filterDokter(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-dokter-part?namalengkap=" + query
    ).subscribe(re => {
      this.listDokter = re;
    })
  }

}
