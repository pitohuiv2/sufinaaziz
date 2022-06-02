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
import { RekamMedisComponent } from '../rekam-medis/rekam-medis.component';

@Component({
  selector: 'app-input-diagnosa',
  templateUrl: './input-diagnosa.component.html',
  styleUrls: ['./input-diagnosa.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class InputDiagnosaComponent implements OnInit, AfterViewInit {
  params: any = {};
  currentNorecPD: any;
  currentNorecAPD: any;
  isClosing: boolean = false;
  page: number;
  rows: number;
  item: any = { pasien: {} };
  dataTableIcdX: any[];
  dataTableIcdIX: any[];
  columnIcdX: any[];
  columnIcdIX: any[];
  listDiagnosaX: any[];
  listDiagnosaIX: any[];
  listJenisDiagnosa: any[];
  selectedIcdX: any;
  selectedIcdIX: any;
  norecDiagnosaPasienICDX: any;
  norecDiagnosaPasienICDIX: any;
  hideEMR:boolean
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
    public rekamMedis: RekamMedisComponent,
  ) {
    
  }

  ngAfterViewInit(): void {
    this.route.params.subscribe(params => {
      if(params['norec_rp'] == undefined){
        var cache = this.cacheHelper.get('cacheEMR_qwertyuiop')
        if (cache != undefined) {
          cache = JSON.parse(cache)
          this.currentNorecPD = cache.norec_pd
          this.currentNorecAPD = cache.norec
          this.hideEMR =true
        } else {
          window.history.back()
        }
      }else{
        this.currentNorecPD = params['norec_rp'];
        this.currentNorecAPD = params['norec_dpr'];
        this.loadHead()
      }
    
    })

  }

  loadHead() {
    this.isClosing = false
    this.apiService.get("general/get-pasien-bynorec-general?norec_pd="
      + this.currentNorecPD
      + "&norec_apd="
      + this.currentNorecAPD)
      .subscribe(e => {
        e.tgllahir = moment(new Date(e.tgllahir)).format('YYYY-MM-DD')
        e.umur = this.dateHelper.getUmur(new Date(e.tgllahir), new Date());
        this.h.item.pasien = e;
        this.item.pasien = e;
        this.apiService.get("sysadmin/general/get-status-close/" + this.item.pasien.noregistrasi).subscribe(rese => {
          if (rese.status == true) {
            this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(rese.tglclosing)).format('DD-MMM-YYYY HH:mm'))
            this.isClosing = true
          }
        })
      })
  }

  ngOnInit(): void {
    this.norecDiagnosaPasienICDX = "";
    this.norecDiagnosaPasienICDIX = "";
    this.columnIcdX = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglinputdiagnosa', header: 'Tgl Diagnosa', width: "140px" },
      { field: 'jenisdiagnosa', header: 'Jenis Diagnosa', width: "140px" },
      { field: 'diagnosa', header: 'Diagnosa', width: "100px" },
      { field: 'ketdiagnosis', header: 'Keterangan', width: "180px" },
      { field: 'namaruangan', header: 'Ruangan', width: "180px" },
      { field: 'namalengkap', header: 'Petugas', width: "120px" },
    ];
    this.columnIcdIX = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglinputdiagnosa', header: 'Tgl Diagnosa', width: "140px" },
      { field: 'diagnosa', header: 'Diagnosa', width: "100px" },
      { field: 'keterangantindakan', header: 'Keterangan', width: "180px" },
      { field: 'namaruangan', header: 'Ruangan', width: "120px" },
      { field: 'namalengkap', header: 'Petugas', width: "120px" },
    ];
    this.getCombo();
  }

  getCombo() {
    this.apiService.get("registrasi/get-data-combo-operator").subscribe(table => {
      var dataCombo = table;
      this.listJenisDiagnosa = dataCombo.jenisdiagnosa;
      this.getDataDiagnosaPasien();
    })
  }

  filterDiagnosaX(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-icdx-part?kodediagnosa=" + query
    ).subscribe(re => {
      this.listDiagnosaX = re;
    })
  }

  filterDiagnosaIX(event) {
    let query = event.query;
    this.apiService.get("general/get-data-combo-icdix-part?kodediagnosa=" + query
    ).subscribe(re => {
      this.listDiagnosaIX = re;
    })
  }

  getDataDiagnosaPasien() {
    this.BatalDiagnosaX();
    this.BatalDiagnosaIX();
    this.apiService.get("registrasi/get-data-diagnosaicdx-pasien?norec_pd=" + this.currentNorecPD + "&norec_apd=" + this.currentNorecAPD
    ).subscribe(data => {
      var dataicdx = data.dataicdxpasien;
      var dataicdix = data.dataicdixpasien;
      for (let i = 0; i < dataicdx.length; i++) {
        const element = dataicdx[i];
        element.no = i + 1;
      }
      for (let e = 0; e < dataicdix.length; e++) {
        const element = dataicdix[e];
        element.no = e + 1;
      }
      this.dataTableIcdX = dataicdx;
      this.dataTableIcdIX = dataicdix;
    })
  }

  BatalDiagnosaX() {
    this.item.dataJenisDiagnosa = undefined;
    this.item.DiagnosaX = undefined;
    this.item.KeteranganDiagnosaX = undefined;
  }

  BatalDiagnosaIX() {
    this.item.DiagnosaIX = undefined;
    this.item.KeteranganDiagnosaIX = undefined;
  }

  onRowSelectIcdX(event: any) {
    // debugger;
    this.norecDiagnosaPasienICDX = undefined;
    this.selectedIcdX = event.data;
    this.norecDiagnosaPasienICDX = this.selectedIcdX.norec;
    this.item.KeteranganDiagnosaX = this.selectedIcdX.ketdiagnosis;
    this.item.dataJenisDiagnosa = {id : this.selectedIcdX.jenisdiagnosaidfk, jenisdiagnosa: this.selectedIcdX.jenisdiagnosa };
    this.item.DiagnosaX = {id : this.selectedIcdX.icdxidfk, diagnosa: this.selectedIcdX.diagnosa };
  }

  onRowSelectIcdIX(event: any) {
    // debugger;
    this.norecDiagnosaPasienICDIX = undefined;
    this.selectedIcdIX = event.data;
    this.norecDiagnosaPasienICDIX = this.selectedIcdIX.norec;
    this.item.DiagnosaIX = {id : this.selectedIcdIX.icdixidfk, diagnosa: this.selectedIcdIX.diagnosa };
    this.item.KeteranganDiagnosaIX = this.selectedIcdIX.keterangantindakan
  }

  hapusDiagnosaX() {
    if (this.selectedIcdX == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.confirmationService.confirm({
          message: 'Apakah Anda Yakin Akan Menghapus Data?',
          header: 'Konfirmasi Hapus Data',
          icon: 'pi pi-info-circle',
          accept: () => {
            var objSave = {
              norec_dp: this.selectedIcdX.norec,
              tipe: "icdx"
            }

            this.apiService.post('registrasi/hapus-diagnosa-pasien', objSave).subscribe(e => {
              if (this.selectedIcdX.norec != '') {
                this.apiService.postLog('Hapus Diagnosa Pasien ICD X', 'norec Diagnosa Pasien ICD X', this.selectedIcdX.norec, 'Hapus Diagnosa ICD X  '
                  + this.selectedIcdX.diagnosa + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
              }
              this.getDataDiagnosaPasien();     
              this.confirmationService.close();         
            })
          },
          reject: (type) => {
            this.alertService.warn('Info, Konfirmasi', 'Hapus Dibatalkan!');
            this.confirmationService.close();
            return;
          }
        });
      }
    })
  }

  hapusDiagnosaIX() {
    if (this.selectedIcdIX == undefined) {
      this.alertService.warn("Info,", "Data Belum Dipilih!");
      return;
    }

    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        this.confirmationService.confirm({
          message: 'Apakah Anda Yakin Akan Menghapus Data?',
          header: 'Konfirmasi Hapus Data',
          icon: 'pi pi-info-circle',
          accept: () => {
            var objSave = {
              norec_dp: this.selectedIcdIX.norec,
              tipe: "icdix"
            }

            this.apiService.post('registrasi/hapus-diagnosa-pasien', objSave).subscribe(e => {
              if (this.selectedIcdX.norec != '') {
                this.apiService.postLog('Hapus Diagnosa Pasien ICD IX', 'norec Diagnosa Pasien ICD IX', this.selectedIcdIX.norec, 'Hapus Diagnosa ICD X  '
                  + this.selectedIcdIX.diagnosa + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
              }
              this.getDataDiagnosaPasien();
            })
          },
          reject: (type) => {
            this.alertService.warn('Info, Konfirmasi', 'Hapus Dibatalkan!');
            return;
          }
        });
      }
    })
  }

  simpanDiagnosaX() {
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {
        if (this.item.dataJenisDiagnosa == undefined) {
          this.alertService.warn("Info", "Jenis Diagnosa Tidak Boleh Kosong!");
          return;
        }

        if (this.item.DiagnosaX == undefined) {
          this.alertService.warn("Info", "Diagnosa Tidak Boleh Kosong!");
          return;
        }

        var objSave = {
          norec: this.norecDiagnosaPasienICDX,
          norec_pd: this.currentNorecPD,
          norec_apd: this.currentNorecAPD,
          jenisdiagnosaidfk: this.item.dataJenisDiagnosa.id,
          icdxidfk: this.item.DiagnosaX.id,
          keterangan: this.item.KeteranganDiagnosaX != undefined ? this.item.KeteranganDiagnosaX : null,
        }
        this.apiService.post('registrasi/save-diagnosa-pasien-icdx', objSave).subscribe(e => {
          if (e.data != '') {
            this.apiService.postLog('Input Diagnosa Pasien ICD X', 'norec Diagnosa Pasien ICD X', e.data, 'Input Diagnosa ICD X  '
              + this.item.DiagnosaX.diagnosa + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
          }
          this.getDataDiagnosaPasien();
        })
      }
    })
  }

  simpanDiagnosaIX() {
    this.apiService.get("general/get-data-closing-pasien/" + this.item.pasien.noregistrasi).subscribe(data => {
      if (data.length > 0) {
        this.alertService.error("Peringatan!", "Registrasi Ini Telah Diclosing");
        return;
      } else {       

        if (this.item.DiagnosaIX == undefined) {
          this.alertService.warn("Info", "Diagnosa Tidak Boleh Kosong!");
          return;
        }

        var objSave = {
          norec: this.norecDiagnosaPasienICDIX,
          norec_pd: this.currentNorecPD,
          norec_apd: this.currentNorecAPD,          
          icdixidfk: this.item.DiagnosaIX.id,
          keterangan: this.item.KeteranganDiagnosaIX != undefined ? this.item.KeteranganDiagnosaIX : null,
        }
        this.apiService.post('registrasi/save-diagnosa-pasien-icdix', objSave).subscribe(e => {
          if (e.data != '') {
            this.apiService.postLog('Input Diagnosa Pasien ICD IX', 'norec Diagnosa Pasien ICD IX', e.data, 'Input Diagnosa ICD IX  '
              + this.item.DiagnosaIX.diagnosa + ' pada No Registrasi ' + this.item.pasien.noregistrasi).subscribe(z => { })
          }
          this.getDataDiagnosaPasien();
        })
      }
    })
  }

}
