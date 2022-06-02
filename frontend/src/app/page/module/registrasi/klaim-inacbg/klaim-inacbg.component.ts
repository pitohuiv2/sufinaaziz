import { Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { FileUpload } from 'primeng/fileupload';
@Component({
  selector: 'app-klaim-inacbg',
  templateUrl: './klaim-inacbg.component.html',
  styleUrls: ['./klaim-inacbg.component.scss'],
  providers: [ConfirmationService]
})
export class KlaimInacbgComponent implements OnInit {
  item: any = {
    periodeAwal: new Date(),
    periodeAkhir: new Date(),
  }
  column: any[]
  listDepartemen: any[] = []
  listKelompokPasien: any[] = []
  listRuangan: any[] = []
  data2: any[] = []
  dataRow: any = {}
  dataSave: any[] = []
  dataLogin: any
  dataTable: any[]
  selected: any
  listspecialdrug: any = []
  listspecialprocedure: any = []
  listspecialprosthesis: any = []
  coderNIK: any
  listspecialinvestigation: any = []
  dataSEPCMG: any
  popupCMG: boolean
  popupPengajuanKlaim: boolean
  fileBASe: any
  popupUploadFile: boolean
  popFilter:boolean
  @ViewChild('fileUpload') fileUpload: any;
  listFaskes = [{ id: 1, name: 'resumse_medis' }
    , { id: 2, name: 'ruang_rawat' }
    , { id: 3, name: 'laboratorium' }
    , { id: 4, name: 'radiologi' }
    , { id: 5, name: 'penunjang_lain' }
    , { id: 6, name: 'resep_obat' }
    , { id: 7, name: 'tagihan' }
    , { id: 8, name: 'kartu_identitas' }
    , { id: 9, name: 'lain_lain' }]
  disableTgl: boolean
  listBtn: MenuItem[];
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) {
    this.dataLogin = this.authService.dataLoginUser
  }


  ngOnInit(): void {
    this.listBtn = [
      { label: 'Grouping', icon: 'pi pi-clone', command: () => { this.grouping(); } },
      { label: 'Final Klaim', icon: 'pi pi-check-circle', command: () => { this.grouping(); } },
      { label: 'Detail Tagihan', icon: 'pi pi-money-bill', command: () => { this.claim_final(); } },
      { label: 'Kirim Klaim Online', icon: 'pi pi-user', command: () => { this.send_claim_individual(); } },
      { label: 'Cetak Klaim', icon: 'pi pi-print', command: () => { this.claim_print(); } },
      { label: 'Top UP', icon: 'pi pi-cloud-upload', command: () => { this.grouper_2(); } },
      { label: 'Edit Klaim', icon: 'pi pi-user-edit', command: () => { this.edit_claim(); } },
      { label: 'Hapus Klaim', icon: 'pi pi-trash', command: () => { this.delete_claim(); } },
      { label: 'Pengajuan Covid-19', icon: 'pi pi-file', command: () => { this.genPengajuan(); } },
      { label: 'Upload File Covid-19', icon: 'pi pi-upload', command: () => { this.uploadFile(); } },
      { label: 'Detail Tagihan', icon: 'pi pi-th-large', command: () => { this.detailTagihan(); } },

    ];
    var chacePeriode = this.cacheHelper.get('cache_EKLAIMqwerty');
    if (chacePeriode != undefined) {

      var arrPeriode = chacePeriode.split('~');
      this.item.periodeAwal = new Date(arrPeriode[0]);
      this.item.periodeAkhir = new Date(arrPeriode[1]);
    } else {
      this.item.periodeAwal = moment(new Date()).format('YYYY-MM-DD 00:00');
      this.item.periodeAkhir = moment(new Date()).format('YYYY-MM-DD 23:59');
    }
    this.getColumn()
    this.getDataCombo()

  }
  selectData(e) {
    this.selected = e
  }
  getColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "140px" },
      { field: 'nocm', header: 'No RM', width: "80px" },
      { field: 'noregistrasi', header: 'Noregistrasi', width: "125px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'namadokter', header: 'Dokter', width: "180px" },
      { field: 'tglpulang', header: 'Tgl Pulang', width: "100px" },
      { field: 'nosep', header: 'No SEP', width: "200px" },
      { field: 'namakelas', header: 'Kelas Dijamin', width: "100px" },
      { field: 'namakelasdaftar', header: 'Kelas Terakhir', width: "100px" },
      { field: 'totalpiutangpenjamin', header: 'Total Grouping', width: "180px", color: 'danger' },
      { field: 'biayanaikkelas', header: 'Biaya Naik Kelas', width: "180px" },
      { field: 'icd10', header: 'Diagnosa Utama dan Sekunder', width: "180px", color: 'danger' },
      { field: 'statusgrouping', header: 'Status Grouping', width: "180px" },

    ];
  }
  cari() {
    this.loadData()
  }
  isiRuangan() {
    if (this.item.instalasi != undefined) {
      this.listRuangan = this.item.instalasi.ruangan;
    }
  }
  getDataCombo() {
    this.apiService.get("registrasi/get-combo-klaim").subscribe(table => {
      var dataCombo = table;
      this.listDepartemen = dataCombo.departemen;
      this.listKelompokPasien = dataCombo.kelompokpasien;

      for (let i = 0; i < dataCombo.kelompokpasien.length; i++) {
        const element = dataCombo.kelompokpasien[i];
        if (element.kelompokpasien.indexOf("BPJS") > -1) {
          this.item.kelompokpasien = element
        }
      }
      this.loadData()
    })
  }
  loadData() {
    var tglAwal = moment(this.item.periodeAwal).format('YYYY-MM-DD HH:mm:ss');
    var tglAkhir = moment(this.item.periodeAkhir).format('YYYY-MM-DD HH:mm:ss');

    var reg = ""
    if (this.item.noReg != undefined) {
      var reg = "&noreg=" + this.item.noReg
    }
    var rm = ""
    if (this.item.noRm != undefined) {
      var rm = "&norm=" + this.item.noRm
    }
    var nm = ""
    if (this.item.nama != undefined) {
      var nm = "&nama=" + this.item.nama
    }
    var ins = ""
    if (this.item.instalasi != undefined) {
      var ins = "&deptId=" + this.item.instalasi.id
    }
    var rg = ""
    if (this.item.ruangan != undefined) {
      var rg = "&ruangId=" + this.item.ruangan.id
    }
    var kp = ""
    if (this.item.kelompokpasien != undefined) {
      var kp = "&kelId=" + this.item.kelompokpasien.id
    }
    var dk = ""
    if (this.item.dokter != undefined) {
      var dk = "&dokId=" + this.item.dokter.id
    }

    var jmlRows = "";
    if (this.item.jmlRows != undefined) {
      jmlRows = this.item.jmlRows
    }

    this.apiService.get("bridging/inacbg/get-daftar-pasien-inacbg-rev-2?" +
      "tglAwal=" + tglAwal +
      "&tglAkhir=" + tglAkhir +
      reg + rm + nm + ins + rg + kp + dk
      + '&jmlRows=' + jmlRows).subscribe(data => {

        var chacePeriode = tglAwal + "~" + tglAkhir;
        this.cacheHelper.set('cache_EKLAIMqwerty', chacePeriode);

        this.data2 = data;

        this.dataSave = []
        for (var i = this.data2.length - 1; i >= 0; i--) {
          this.data2[i].no = i + 1

          if (this.data2[i].icd10 == false) {
            this.data2[i].statusicd = 'Belum Di Coder'
          }
          if (this.data2[i].totalpiutangpenjamin == '1') {
            this.data2[i].totalpiutangpenjamin = 'Belum Di Grouping'
          }
          var jenis_rawat = this.data2[i].jenis_rawat
          // if (this.data2[i].deptid != 16) {
          //   jenis_rawat = 2
          // }
          var upgrade_class_ind = 0
          var upgrade_class_class = ''
          var add_payment_pct = 0
          if (this.data2[i].nokelasdijamin > this.data2[i].nokelasdaftar && this.data2[i].deptid == 2) {
            upgrade_class_ind = this.data2[i].namakelasdaftar!=null ?1: 0
            upgrade_class_class = this.data2[i].namakelasdaftar !=null?this.data2[i].namakelasdaftar : ""
            add_payment_pct = 0
          }
          if (this.data2[i].namaruangan == 'NHCU' || this.data2[i].namaruangan == 'ICU' || this.data2[i].namaruangan == 'ICCU') {
            upgrade_class_ind = 0
          }
          if (this.data2[i].statustitipan == 1) {
            upgrade_class_ind = 0
            upgrade_class_class = ''
            add_payment_pct = 0
          }

          var discharge_status = 0
          var pemulasaraan_covid = 0
          // if (this.data2[i].discharge_status == 1 || this.data2[i].objectstatuspulangfk == 6) {
          discharge_status = this.data2[i].discharge_status

          if (jenis_rawat == 2) {
            this.data2[i].nokelasdijamin = ''
          }
          var payor_id = '3'
          var payor_cd = 'JKN'
          if (this.data2[i].statuscovid == '1') {
            payor_id = '71'
            payor_cd = 'COVID-19'
          } else if (this.data2[i].idrekanan == '2552') {
            payor_id = '3'
            payor_cd = 'JKN'
          } else if (this.data2[i].idrekanan == '581164') {
            payor_id = '5'
            payor_cd = 'JAMKESDA'
            this.data2[i].nosep = this.data2[i].nokepesertaan
          }
          this.dataRow = {
            "nomor_sep": this.data2[i].nosep,    //"0901R001TEST0001",    
            "nomor_kartu": this.data2[i].nokepesertaan,    //"233333",    
            "tgl_masuk": this.data2[i].tglregistrasi,    //"2017-11-20 12:55:00",    
            "tgl_pulang": this.data2[i].tglpulang,    //"2017-12-01 09:55:00",    
            "jenis_rawat": jenis_rawat,    //"1",    
            "kelas_rawat": this.data2[i].nokelasdijamin,    //"1",    
            "adl_sub_acute": '',    //"15",    
            "adl_chronic": '',    //"12",    
            "icu_indikator": '',    //"1",    
            "icu_los": '',    //"2",    
            "ventilator_hour": '',    //"5",    
            "upgrade_class_ind": upgrade_class_ind,    //"1",    
            "upgrade_class_class": upgrade_class_class,    //"vip",    
            "upgrade_class_los": '',    //"5",    
            "add_payment_pct": '',    //"35",    
            "birth_weight": '',    //"0",    
            "discharge_status": discharge_status,    //"1",    
            "diagnosa": this.data2[i].icd10,    //"S71.0#A00.1",    
            "procedure": this.data2[i].icd9,    //"81.52#88.38",    
            "tarif_rs": {
              "prosedur_non_bedah": this.data2[i].tarif_rs.prosedur_non_bedah,    //"300000",      
              "prosedur_bedah": this.data2[i].tarif_rs.prosedur_bedah,    //"20000000",      
              "konsultasi": this.data2[i].tarif_rs.konsultasi,    //"300000",      
              "tenaga_ahli": this.data2[i].tarif_rs.tenaga_ahli,    //"200000",      
              "keperawatan": this.data2[i].tarif_rs.keperawatan,    // "80000",      
              "penunjang": this.data2[i].tarif_rs.penunjang,    //"1000000",      
              "radiologi": this.data2[i].tarif_rs.radiologi,    //"500000",      
              "laboratorium": this.data2[i].tarif_rs.laboratorium,    //"600000",      
              "pelayanan_darah": this.data2[i].tarif_rs.pelayanan_darah,    //"150000",      
              "rehabilitasi": this.data2[i].tarif_rs.rehabilitasi,    //"100000",      
              "kamar": this.data2[i].tarif_rs.kamar,    //"6000000",      
              "rawat_intensif": this.data2[i].tarif_rs.rawat_intensif,    //"2500000",      
              "obat": this.data2[i].tarif_rs.obat,    //"2000000",      
              "obat_kronis": this.data2[i].tarif_rs.obat_kronis,    //"2000000",      
              "obat_kemoterapi": this.data2[i].tarif_rs.obat_kemoterapi,    //"2000000",      
              "alkes": this.data2[i].tarif_rs.alkes,    //"500000",      
              "bmhp": this.data2[i].tarif_rs.bmhp,    //"400000",      
              "sewa_alat": this.data2[i].tarif_rs.sewa_alat,    //"210000"    
            },
            "pemulasaraan_jenazah": pemulasaraan_covid,
            "kantong_jenazah": pemulasaraan_covid,
            "peti_jenazah": pemulasaraan_covid,
            "plastik_erat": pemulasaraan_covid,
            "desinfektan_jenazah": pemulasaraan_covid,
            "mobil_jenazah": pemulasaraan_covid,
            "desinfektan_mobil_jenazah": pemulasaraan_covid,
            "covid19_status_cd": this.data2[i].covid19_status_cd,
            "nomor_kartu_t": this.data2[i].noidentitas,
            "episodes": this.data2[i].loscovid,//"1;12#2;3#6;5",
            "covid19_cc_ind": this.data2[i].covid19_cc_ind,
            "tarif_poli_eks": 0,    //"100000",    
            "nama_dokter": this.data2[i].namadokter,    //"RUDY, DR",    
            "kode_tarif": this.data2[i].kodetarif,    //'RSAB',    //"AP",    
            "payor_id": payor_id,//'3',    //"3",    
            "payor_cd": payor_cd,//'JKN',    //"JKN",    
            "cob_cd": '#',    //"0001",    
            "coder_nik": this.data2[i].codernik,    //"123123123123"  
            "nomor_rm": this.data2[i].nocm,    //"123-45-28",
            "nama_pasien": this.data2[i].namapasien,    //"Efan Andrian",
            "tgl_lahir": this.data2[i].tgllahir,    //"1985-01-01 02:00:00",
            "gender": this.data2[i].objectjeniskelaminfk    //"2"
          }
          this.coderNIK = this.data2[i].codernik
          this.dataSave.push(this.dataRow)
        }
        // this.show_btn = true
        for (var i = this.dataSave.length - 1; i >= 0; i--) {
          if (this.dataSave[i].nosep == '') {
            // this.show_btn = false
            break;
          }
        }

        this.dataTable = data

      });

  };
  new_claim() {

    var dt1 = {}
    var dt2 = []
    for (var i = this.dataSave.length - 1; i >= 0; i--) {
      // if (dataSave[i].nomor_sep == this.dataPasienSelected.nosep) {
      dt1 = {
        "metadata": {
          "method": "new_claim"
        },
        "data": {
          "nomor_kartu": this.dataSave[i].nomor_kartu,
          "nomor_sep": this.dataSave[i].nomor_sep,
          "nomor_rm": this.dataSave[i].nomor_rm,
          "nama_pasien": this.dataSave[i].nama_pasien,
          "tgl_lahir": this.dataSave[i].tgl_lahir,
          "gender": this.dataSave[i].gender
        }
      }
      dt2.push(dt1)
      // }
    }

    var objData = {
      "data": dt2
    }
    // manageTataRekening.savebridginginacbg(objData).then(function(e){
    for (var i = this.dataSave.length - 1; i >= 0; i--) {
      // if (dataSave[i].nomor_sep == this.dataPasienSelected.nosep) {
      dt1 = {
        "metadata": {
          "method": "set_claim_data",
          "nomor_sep": this.dataSave[i].nomor_sep
        },
        "data": {
          "nomor_sep": this.dataSave[i].nomor_sep,    //"0901R001TEST0001",    
          "nomor_kartu": this.dataSave[i].nomor_kartu,    //"233333",    
          "tgl_masuk": this.dataSave[i].tgl_masuk,    //"2017-11-20 12:55:00",    
          "tgl_pulang": this.dataSave[i].tgl_pulang,    //"2017-12-01 09:55:00",    
          "jenis_rawat": this.dataSave[i].jenis_rawat,    //"1",    
          "kelas_rawat": this.dataSave[i].kelas_rawat,    //"1",    
          "adl_sub_acute": this.dataSave[i].adl_sub_acute,    //"15",    
          "adl_chronic": this.dataSave[i].adl_chronic,    //"12",    
          "icu_indikator": this.dataSave[i].icu_indikator,    //"1",    
          "icu_los": this.dataSave[i].icu_los,    //"2",    
          "ventilator_hour": this.dataSave[i].ventilator_hour,    //"5",    
          "upgrade_class_ind": this.dataSave[i].upgrade_class_ind,    //"1",    
          "upgrade_class_class": this.dataSave[i].upgrade_class_class,    //"vip",    
          "upgrade_class_los": this.dataSave[i].upgrade_class_los,    //"5",    
          "add_payment_pct": "75",//dataSave[i].add_payment_pct ,    //"35",    
          "birth_weight": this.dataSave[i].beratbadan == null ? "0" : this.dataSave[i].beratbadan,//this.dataPasienSelected.beratbadan,//dataSave[i].birth_weight ,    //"0",    
          "discharge_status": this.dataSave[i].discharge_status,    //"1",    
          "diagnosa": this.dataSave[i].diagnosa,    //"S71.0#A00.1",    
          "procedure": this.dataSave[i].procedure,    //"81.52#88.38",    
          "tarif_rs": {
            "prosedur_non_bedah": this.dataSave[i].tarif_rs.prosedur_non_bedah,    //"300000",      
            "prosedur_bedah": this.dataSave[i].tarif_rs.prosedur_bedah,    //"20000000",      
            "konsultasi": this.dataSave[i].tarif_rs.konsultasi,    //"300000",      
            "tenaga_ahli": this.dataSave[i].tarif_rs.tenaga_ahli,    //"200000",      
            "keperawatan": this.dataSave[i].tarif_rs.keperawatan,    // "80000",      
            "penunjang": this.dataSave[i].tarif_rs.penunjang,    //"1000000",      
            "radiologi": this.dataSave[i].tarif_rs.radiologi,    //"500000",      
            "laboratorium": this.dataSave[i].tarif_rs.laboratorium,    //"600000",      
            "pelayanan_darah": this.dataSave[i].tarif_rs.pelayanan_darah,    //"150000",      
            "rehabilitasi": this.dataSave[i].tarif_rs.rehabilitasi,    //"100000",      
            "kamar": this.dataSave[i].tarif_rs.kamar,    //"6000000",      
            "rawat_intensif": this.dataSave[i].tarif_rs.rawat_intensif,    //"2500000",      
            "obat": this.dataSave[i].tarif_rs.obat,    //"2000000",  
            "obat_kronis": this.dataSave[i].tarif_rs.obat_kronis,
            "obat_kemoterapi": this.dataSave[i].tarif_rs.obat_kemoterapi,
            "alkes": this.dataSave[i].tarif_rs.alkes,    //"500000",      
            "bmhp": this.dataSave[i].tarif_rs.bmhp,    //"400000",      
            "sewa_alat": this.dataSave[i].tarif_rs.sewa_alat,    //"210000"    
          },
          "tarif_poli_eks": this.dataSave[i].tarif_poli_eks,    //"100000",    
          "nama_dokter": this.dataSave[i].nama_dokter,    //"RUDY, DR",    
          "kode_tarif": this.dataSave[i].kode_tarif,    //"AP",    
          "payor_id": this.dataSave[i].payor_id,    //"3",    
          "payor_cd": this.dataSave[i].payor_cd,    //"JKN",    
          "cob_cd": this.dataSave[i].cob_cd,    //"0001",    
          "coder_nik": this.dataSave[i].coder_nik    //"123123123123"  
        }
      }
      dt2.push(dt1)
      // }
    }

    var objData = {
      "data": dt2
    }
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {


    })
    // })
  }
  grouping() {
    if (this.selected == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    var stt = 'false'
    var covid19_status_cd: any = ''
    var covid19_cc_ind: any = '0'
    if (this.selected.statuscovid == 'covid') {
      if (this.selected.noidentitas == "") {
        this.alertService.warn('Info', 'NO IDENTITAS KOSONG!!!')
        return;
      }

      if (confirm('Positif Covid-19 ? ')) {
        // Save it!
        stt = 'true';
        covid19_status_cd = 3
      } else {
        // Do nothing!
        stt = 'false'
        if (confirm('PDP Covid-19 ? ')) {
          // Save it!
          stt = 'true';
          covid19_status_cd = 2
        } else {
          // Do nothing!
          stt = 'false'
          if (confirm('ODP Covid-19 ? ')) {
            // Save it!
            stt = 'true';
            covid19_status_cd = 1
          } else {
            // Do nothing!
            stt = 'false'
          }
        }
      }
      if (covid19_status_cd == 0) {
        this.alertService.warn('Info', 'JENIS PASIEN COVID BELUM DITENTUKAN');
        return;
      }

      if (confirm('comorbidity/complexity ? ')) {
        stt = 'true';
        covid19_cc_ind = '1'
      } else {
        stt = 'false';
      }

    }


    if (this.selected.jenis_rawat == 2) {
      var dt1 = {}
      var dt2 = []
      for (var i = this.dataSave.length - 1; i >= 0; i--) {
        if (this.dataSave[i].nomor_sep == this.selected.nosep) {
          dt1 = {
            "metadata": {
              "method": "new_claim"
            },
            "data": {
              "nomor_kartu": this.dataSave[i].nomor_kartu,
              "nomor_sep": this.dataSave[i].nomor_sep,
              "nomor_rm": this.dataSave[i].nomor_rm,
              "nama_pasien": this.dataSave[i].nama_pasien,
              "tgl_lahir": this.dataSave[i].tgl_lahir,
              "gender": this.dataSave[i].gender
            }
          }
          dt2.push(dt1)
        }
      }

      var objData = {
        "data": dt2
      }
      for (var i = this.dataSave.length - 1; i >= 0; i--) {
        if (this.dataSave[i].nomor_sep == this.selected.nosep) {
          dt1 = {
            "metadata": {
              "method": "set_claim_data",
              "nomor_sep": this.dataSave[i].nomor_sep
            },
            "data": {
              "nomor_sep": this.dataSave[i].nomor_sep,    //"0901R001TEST0001",    
              "nomor_kartu": this.dataSave[i].nomor_kartu,    //"233333",    
              "tgl_masuk": this.dataSave[i].tgl_masuk,    //"2017-11-20 12:55:00",    
              "tgl_pulang": this.dataSave[i].tgl_pulang,    //"2017-12-01 09:55:00",    
              "jenis_rawat": this.dataSave[i].jenis_rawat,    //"1",    
              "kelas_rawat": this.dataSave[i].kelas_rawat,    //"1",    
              "adl_sub_acute": this.dataSave[i].adl_sub_acute,    //"15",    
              "adl_chronic": this.dataSave[i].adl_chronic,    //"12",    
              "icu_indikator": this.dataSave[i].icu_indikator,    //"1",    
              "icu_los": this.dataSave[i].icu_los,    //"2",    
              "ventilator_hour": this.dataSave[i].ventilator_hour,    //"5",    
              "upgrade_class_ind": this.dataSave[i].upgrade_class_ind,    //"1",    
              "upgrade_class_class": this.dataSave[i].upgrade_class_class,    //"vip",    
              "upgrade_class_los": this.dataSave[i].upgrade_class_los,    //"5",    
              "add_payment_pct": "75",//dataSave[i].add_payment_pct ,    //"35",    
              "birth_weight": this.selected.beratbadan,//dataSave[i].birth_weight ,    //"0",    
              "discharge_status": this.dataSave[i].discharge_status,    //"1",    
              "diagnosa": this.dataSave[i].diagnosa,    //"S71.0#A00.1",    
              "procedure": this.dataSave[i].procedure,    //"81.52#88.38",    
              "tarif_rs": {
                "prosedur_non_bedah": this.dataSave[i].tarif_rs.prosedur_non_bedah,    //"300000",      
                "prosedur_bedah": this.dataSave[i].tarif_rs.prosedur_bedah,    //"20000000",      
                "konsultasi": this.dataSave[i].tarif_rs.konsultasi,    //"300000",      
                "tenaga_ahli": this.dataSave[i].tarif_rs.tenaga_ahli,    //"200000",      
                "keperawatan": this.dataSave[i].tarif_rs.keperawatan,    // "80000",      
                "penunjang": this.dataSave[i].tarif_rs.penunjang,    //"1000000",      
                "radiologi": this.dataSave[i].tarif_rs.radiologi,    //"500000",      
                "laboratorium": this.dataSave[i].tarif_rs.laboratorium,    //"600000",      
                "pelayanan_darah": this.dataSave[i].tarif_rs.pelayanan_darah,    //"150000",      
                "rehabilitasi": this.dataSave[i].tarif_rs.rehabilitasi,    //"100000",      
                "kamar": this.dataSave[i].tarif_rs.kamar,    //"6000000",      
                "rawat_intensif": this.dataSave[i].tarif_rs.rawat_intensif,    //"2500000",      
                "obat": this.dataSave[i].tarif_rs.obat,    //"2000000",  
                "obat_kronis": this.dataSave[i].tarif_rs.obat_kronis,
                "obat_kemoterapi": this.dataSave[i].tarif_rs.obat_kemoterapi,
                "alkes": this.dataSave[i].tarif_rs.alkes,    //"500000",      
                "bmhp": this.dataSave[i].tarif_rs.bmhp,    //"400000",      
                "sewa_alat": this.dataSave[i].tarif_rs.sewa_alat,    //"210000"    
              },
              "pemulasaraan_jenazah": this.dataSave[i].pemulasaraan_jenazah,//dataSave[i].pemulasaraan_jenazah,
              "kantong_jenazah": this.dataSave[i].kantong_jenazah,//dataSave[i].kantong_jenazah,
              "peti_jenazah": this.dataSave[i].peti_jenazah,//dataSave[i].peti_jenazah,
              "plastik_erat": this.dataSave[i].plastik_erat,//dataSave[i].plastik_erat,
              "desinfektan_jenazah": this.dataSave[i].desinfektan_jenazah,//dataSave[i].desinfektan_jenazah,
              "mobil_jenazah": this.dataSave[i].mobil_jenazah,//dataSave[i].mobil_jenazah,
              "desinfektan_mobil_jenazah": this.dataSave[i].desinfektan_mobil_jenazah,//dataSave[i].desinfektan_mobil_jenazah,
              "covid19_status_cd": covid19_status_cd,//dataSave[i].covid19_status_cd,
              "nomor_kartu_t": this.dataSave[i].nomor_kartu_t,//dataSave[i].nomor_kartu_t,
              "episodes": this.dataSave[i].episodes,//dataSave[i].episodes,//"1;12#2;3#6;5",
              "covid19_cc_ind": covid19_cc_ind,//dataSave[i].covid19_cc_ind,
              "tarif_poli_eks": this.dataSave[i].tarif_poli_eks,    //"100000",    
              "nama_dokter": this.dataSave[i].nama_dokter,    //"RUDY, DR",    
              "kode_tarif": this.dataSave[i].kode_tarif,    //"AP",    
              "payor_id": this.dataSave[i].payor_id,    //"3",    
              "payor_cd": this.dataSave[i].payor_cd,    //"JKN",    
              "cob_cd": this.dataSave[i].cob_cd,    //"0001",    
              "coder_nik": this.dataSave[i].coder_nik    //"123123123123"  
            }
          }
          dt2.push(dt1)
        }
      }

      var objData = {
        "data": dt2
      }
      this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
        var dt1 = {}
        var dt2 = []

        // for (var i = dataSave.length - 1; i >= 0; i--) {
        dt1 = {
          "metadata": {
            "method": "grouper",
            "stage": "1"
          },
          "data": {
            // "nomor_sep": this.dataSave[i].nomor_sep 
            "nomor_sep": this.selected.nosep
          }
        }
        dt2.push(dt1)
        // }


        var objData = {
          "data": dt2
        }
        var totaldijamin = "";
        var hakkelas = "";
        var biayanaikkelas: any = "0";
        this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
          // simpan response ke database
          let responData = e.dataresponse;
          this.alertService.info('Info', responData[0].dataresponse.metadata.message);
          this.alertService.info('Info', responData[0].dataresponse.response.cbg.description);
          if (responData[0].dataresponse.response.cbg.description == "ERROR: MALE WITH GROUPING CRITERIA NOT MET") {
            this.alertService.info('Info', 'JENIS KELAMIN SALAH ATAU DIAGNOSA TIDAK SESUAI JENIS KELAMIN');
          }
          // if(dataSave[0].jenis_rawat==2){
          if (this.selected.jenis_rawat == 2) {
            totaldijamin = responData[0].dataresponse.tarif_alt[2].tarif_inacbg
          } else {
            hakkelas = responData[0].dataresponse.response.kelas
            if (hakkelas == "kelas_1") {
              totaldijamin = responData[0].dataresponse.tarif_alt[0].tarif_inacbg
            } else if (hakkelas == "kelas_2") {
              totaldijamin = responData[0].dataresponse.tarif_alt[1].tarif_inacbg
            } else if (hakkelas == "kelas_3") {
              totaldijamin = responData[0].dataresponse.tarif_alt[2].tarif_inacbg
            }
            if (this.selected.namakelas != this.selected.namakelasdaftar) {
              biayanaikkelas = responData[0].dataresponse.response.add_payment_amt
              if (biayanaikkelas < 0) {
                biayanaikkelas = 0
              }
            }
          }
          var dataproposi = {
            "noregistrasifk": this.selected.norec,
            "totalDijamin": totaldijamin,
            "biayaNaikkelas": biayanaikkelas
          }
          this.apiService.post('bridging/inacbg/save-proposi-bridging-inacbg', dataproposi).subscribe(e => {
            //ini untuk proposional kan utang per tindakan
          })
          this.loadData()
          if (responData[0].dataresponse.special_cmg_option.length > 1) {
            this.alertService.info('Info', 'Terdeteksi Top-up CMG Options')
            this.dataSEPCMG = responData[0].datarequest.data.nomor_sep
            var responOptions = responData[0].dataresponse.special_cmg_option
            var spesialDrug = []
            var specialProcedure = []
            var specialProsthesis = []
            var specialInvestigation = []
            for (let i = 0; i < responOptions.length; i++) {
              const element = responOptions[i];
              if (element.type == 'Special Drug') {
                spesialDrug.push(element)
              }
              if (element.type == 'Special Procedure') {
                specialProcedure.push(element)
              }
              if (element.type == 'Special Prosthesis') {
                specialProsthesis.push(element)
              }
              if (element.type == 'Special Investigation') {
                specialInvestigation.push(element)
              }
            }
            this.listspecialdrug = spesialDrug
            this.listspecialprocedure = specialProcedure
            this.listspecialprosthesis = specialProsthesis
            this.listspecialinvestigation = specialInvestigation
          }
        })

      })
    } else {
      this.apiService.get('bridging/inacbg/get-daftar-pasien-statusnaikkelas?noreg=' + this.selected.norec
        + '&namakelas=' + this.selected.namakelas).subscribe(e => {
          var resp = e.data
          var dt1 = {}
          var dt2 = []
          for (var i = this.dataSave.length - 1; i >= 0; i--) {
            if (this.dataSave[i].nomor_sep == this.selected.nosep) {
              dt1 = {
                "metadata": {
                  "method": "new_claim"
                },
                "data": {
                  "nomor_kartu": this.dataSave[i].nomor_kartu,
                  "nomor_sep": this.dataSave[i].nomor_sep,
                  "nomor_rm": this.dataSave[i].nomor_rm,
                  "nama_pasien": this.dataSave[i].nama_pasien,
                  "tgl_lahir": this.dataSave[i].tgl_lahir,
                  "gender": this.dataSave[i].gender
                }
              }
              dt2.push(dt1)
            }
          }

          var objData = {
            "data": dt2
          }
          // manageTataRekening.savebridginginacbg(objData).then(function(e){
          for (var i = this.dataSave.length - 1; i >= 0; i--) {
            if (this.dataSave[i].nomor_sep == this.selected.nosep) {
              dt1 = {
                "metadata": {
                  "method": "set_claim_data",
                  "nomor_sep": this.dataSave[i].nomor_sep
                },
                "data": {
                  "nomor_sep": this.dataSave[i].nomor_sep,    //"0901R001TEST0001",    
                  "nomor_kartu": this.dataSave[i].nomor_kartu,    //"233333",    
                  "tgl_masuk": this.dataSave[i].tgl_masuk,    //"2017-11-20 12:55:00",    
                  "tgl_pulang": this.dataSave[i].tgl_pulang,    //"2017-12-01 09:55:00",    
                  "jenis_rawat": this.dataSave[i].jenis_rawat,    //"1",    
                  "kelas_rawat": this.dataSave[i].kelas_rawat,    //"1",    
                  "adl_sub_acute": this.dataSave[i].adl_sub_acute,    //"15",    
                  "adl_chronic": this.dataSave[i].adl_chronic,    //"12",    
                  "icu_indikator": resp.statusrawatintensiv,//dataSave[i].icu_indikator ,    //"1",    
                  "icu_los": resp.lamarawatintensiv,//dataSave[i].icu_los ,    //"2",    
                  "ventilator_hour": this.dataSave[i].ventilator_hour,    //"5",    
                  "upgrade_class_ind": resp.statusnaikkelas,    //"1",    dataSave[i].upgrade_class_ind ,
                  "upgrade_class_class": resp.kelastertinggi,//dataSave[i].upgrade_class_class ,    //"vip",    
                  "upgrade_class_los": resp.lamarawatnaikkelas,//dataSave[i].upgrade_class_los ,    //"5",    
                  "add_payment_pct": "75",//dataSave[i].add_payment_pct ,    //"35",    
                  "birth_weight": this.selected.beratbadan,//dataSave[i].birth_weight ,    //"0",    
                  "discharge_status": this.dataSave[i].discharge_status,    //"1",    
                  "diagnosa": this.dataSave[i].diagnosa,    //"S71.0#A00.1",    
                  "procedure": this.dataSave[i].procedure,    //"81.52#88.38",    
                  "tarif_rs": {
                    "prosedur_non_bedah": this.dataSave[i].tarif_rs.prosedur_non_bedah,    //"300000",      
                    "prosedur_bedah": this.dataSave[i].tarif_rs.prosedur_bedah,    //"20000000",      
                    "konsultasi": this.dataSave[i].tarif_rs.konsultasi,    //"300000",      
                    "tenaga_ahli": this.dataSave[i].tarif_rs.tenaga_ahli,    //"200000",      
                    "keperawatan": this.dataSave[i].tarif_rs.keperawatan,    // "80000",      
                    "penunjang": this.dataSave[i].tarif_rs.penunjang,    //"1000000",      
                    "radiologi": this.dataSave[i].tarif_rs.radiologi,    //"500000",      
                    "laboratorium": this.dataSave[i].tarif_rs.laboratorium,    //"600000",      
                    "pelayanan_darah": this.dataSave[i].tarif_rs.pelayanan_darah,    //"150000",      
                    "rehabilitasi": this.dataSave[i].tarif_rs.rehabilitasi,    //"100000",      
                    "kamar": this.dataSave[i].tarif_rs.kamar,    //"6000000",      
                    "rawat_intensif": this.dataSave[i].tarif_rs.rawat_intensif,    //"2500000",      
                    "obat": this.dataSave[i].tarif_rs.obat,    //"2000000",  
                    "obat_kronis": this.dataSave[i].tarif_rs.obat_kronis,
                    "obat_kemoterapi": this.dataSave[i].tarif_rs.obat_kemoterapi,
                    "alkes": this.dataSave[i].tarif_rs.alkes,    //"500000",      
                    "bmhp": this.dataSave[i].tarif_rs.bmhp,    //"400000",      
                    "sewa_alat": this.dataSave[i].tarif_rs.sewa_alat,    //"210000"    
                  },
                  "pemulasaraan_jenazah": this.dataSave[i].pemulasaraan_jenazah,//dataSave[i].pemulasaraan_jenazah,
                  "kantong_jenazah": this.dataSave[i].kantong_jenazah,//dataSave[i].kantong_jenazah,
                  "peti_jenazah": this.dataSave[i].peti_jenazah,//dataSave[i].peti_jenazah,
                  "plastik_erat": this.dataSave[i].plastik_erat,//dataSave[i].plastik_erat,
                  "desinfektan_jenazah": this.dataSave[i].desinfektan_jenazah,//dataSave[i].desinfektan_jenazah,
                  "mobil_jenazah": this.dataSave[i].mobil_jenazah,//dataSave[i].mobil_jenazah,
                  "desinfektan_mobil_jenazah": this.dataSave[i].desinfektan_mobil_jenazah,//dataSave[i].desinfektan_mobil_jenazah,
                  "covid19_status_cd": covid19_status_cd,//dataSave[i].covid19_status_cd,
                  "nomor_kartu_t": this.dataSave[i].nomor_kartu_t,//dataSave[i].nomor_kartu_t,
                  "episodes": this.dataSave[i].episodes,//dataSave[i].episodes,//"1;12#2;3#6;5",
                  "covid19_cc_ind": covid19_cc_ind,//dataSave[i].covid19_cc_ind,
                  "tarif_poli_eks": this.dataSave[i].tarif_poli_eks,    //"100000",    
                  "nama_dokter": this.dataSave[i].nama_dokter,    //"RUDY, DR",    
                  "kode_tarif": this.dataSave[i].kode_tarif,    //"AP",    
                  "payor_id": this.dataSave[i].payor_id,    //"3",    
                  "payor_cd": this.dataSave[i].payor_cd,    //"JKN",    
                  "cob_cd": this.dataSave[i].cob_cd,    //"0001",    
                  "coder_nik": this.dataSave[i].coder_nik    //"123123123123"  
                }
              }
              dt2.push(dt1)
            }
          }

          var objData = {
            "data": dt2
          }
          this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
            var dt1 = {}
            var dt2 = []

            // for (var i = dataSave.length - 1; i >= 0; i--) {
            dt1 = {
              "metadata": {
                "method": "grouper",
                "stage": "1"
              },
              "data": {
                // "nomor_sep": this.dataSave[i].nomor_sep 
                "nomor_sep": this.selected.nosep
              }
            }
            dt2.push(dt1)
            // }


            var objData = {
              "data": dt2
            }
            var totaldijamin = "";
            var hakkelas = "";
            var biayanaikkelas: any = "0";
            var top_up_jenazah = "";
            this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
              // simpan response ke database
              let responData = e.dataresponse;
              this.alertService.info('Info', responData[0].dataresponse.metadata.message);
              this.alertService.info('Info', responData[0].dataresponse.response.cbg.description);
              if (responData[0].dataresponse.response.cbg.description == "ERROR: MALE WITH GROUPING CRITERIA NOT MET") {
                this.alertService.info('Info', 'JENIS KELAMIN SALAH ATAU DIAGNOSA TIDAK SESUAI JENIS KELAMIN');
              }
              // if(dataSave[0].jenis_rawat==2){
              if (this.selected.jenis_rawat == 2) {
                totaldijamin = responData[0].dataresponse.tarif_alt[2].tarif_inacbg
              } else if (this.selected.statuscovid == 1) {
                if (responData[0].dataresponse.response.covid19_data.top_up_jenazah != 0) {
                  top_up_jenazah = "";
                }
                totaldijamin = top_up_jenazah + responData[0].dataresponse.response.covid19_data.top_up_rawat + responData[0].dataresponse.response.covid19_data.top_up_rawat_factor + responData[0].dataresponse.response.covid19_data.top_up_rawat_gross
              } else {
                hakkelas = responData[0].dataresponse.response.kelas
                if (hakkelas == "kelas_1") {
                  totaldijamin = responData[0].dataresponse.tarif_alt[0].tarif_inacbg
                } else if (hakkelas == "kelas_2") {
                  totaldijamin = responData[0].dataresponse.tarif_alt[1].tarif_inacbg
                } else if (hakkelas == "kelas_3") {
                  totaldijamin = responData[0].dataresponse.tarif_alt[2].tarif_inacbg
                }
                // if(this.selected.namakelas!=this.selected.namakelasdaftar){
                if (resp.statusnaikkelas != '0') {
                  biayanaikkelas = responData[0].dataresponse.response.add_payment_amt
                  if (biayanaikkelas < 0) {
                    biayanaikkelas = 0
                  }
                }
              }
              var dataproposi = {
                "noregistrasifk": this.selected.norec,
                "totalDijamin": totaldijamin,
                "biayaNaikkelas": biayanaikkelas
              }
              this.apiService.post('bridging/inacbg/save-proposi-bridging-inacbg', dataproposi).subscribe(e => {
                //ini untuk proposional kan utang per tindakan
              })
              this.loadData()
              if (responData[0].dataresponse.special_cmg_option.length > 1) {
                this.alertService.info('Info', 'Terdeteksi Top-up CMG Options')
                this.dataSEPCMG = responData[0].datarequest.data.nomor_sep
                var responOptions = responData[0].dataresponse.special_cmg_option
                var spesialDrug = []
                var specialProcedure = []
                var specialProsthesis = []
                var specialInvestigation = []
                for (let i = 0; i < responOptions.length; i++) {
                  const element = responOptions[i];
                  if (element.type == 'Special Drug') {
                    spesialDrug.push(element)
                  }
                  if (element.type == 'Special Procedure') {
                    specialProcedure.push(element)
                  }
                  if (element.type == 'Special Prosthesis') {
                    specialProsthesis.push(element)
                  }
                  if (element.type == 'Special Investigation') {
                    specialInvestigation.push(element)
                  }
                }
                this.listspecialdrug = spesialDrug
                this.listspecialprocedure = specialProcedure
                this.listspecialprosthesis = specialProsthesis
                this.listspecialinvestigation = specialInvestigation
              }
            })

          })

        })
    }


  }
  claim_final() {
    if (this.selected == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    var dt1 = {}
    var dt2 = []
    // for (var i = dataSave.length - 1; i >= 0; i--) {
    dt1 = {
      "metadata": {
        "method": "claim_final"
      },
      "data": {
        "nomor_sep": this.selected.nosep,//dataSave[i].nomor_sep,      
        "coder_nik": this.coderNIK,
      }
    }
    dt2.push(dt1)
    // }

    var objData = {
      "data": dt2
    }
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
      // response oke saja
      let responData = e.dataresponse;
      // this.apiService.post("tatarekening/simpan-verifikasi-tagihan-inacbg/"+this.selected.noregistrasi ,this.selected)
      //   .subscribe( e=> {
      //     this.loadData();

      //   });
      this.alertService.info('Info', responData[0].dataresponse.metadata.message);
    })
  }
  send_claim_individual() {
    if (this.selected == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    var dt1 = {}
    var dt2 = []
    // for (var i = dataSave.length - 1; i >= 0; i--) {
    dt1 = {
      "metadata": {
        "method": "send_claim_individual"
      },
      "data": {
        "nomor_sep": this.selected.nosep
      }
    }
    dt2.push(dt1)
    // }

    var objData = {
      "data": dt2
    }
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
      // response simpan ke database	
      let responData = e.dataresponse;
      var datasend = {
        "noregistrasifk": this.selected.norec
      }
      if (responData[0].dataresponse.metadata.code == "200") {
        // this.apiService.updatestatusbridginginacbg(datasend).then(function (e) {
        //   this.loadData();
        // })
        this.loadData();
      }
      this.alertService.info('Info', responData[0].dataresponse.metadata.message);
    })
  }

  claim_print() {
    if (this.selected == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    var dt1 = {}
    var dt2 = []
    // for (var i = dataSave.length - 1; i >= 0; i--) {
    dt1 = {
      "metadata": {
        "method": "claim_print"
      },
      "data": {
        "nomor_sep": this.selected.nosep
      }
    }
    dt2.push(dt1)
    // }

    var objData = {
      "data": dt2
    }
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
      // response simpan ke database	
      let responData = e.dataresponse;
      if (responData[0].dataresponse.metadata.code == 200) {
        const linkSource = 'data:application/pdf;base64,' + responData[0].dataresponse.data;
        const downloadLink = document.createElement("a");
        var tglprint = moment(new Date()).format('YYYY-MM-DD');
        // const fileName = "claim_print_" + responData[0].datarequest.data.nomor_sep + "_" + tglprint + ".pdf";
        var a = responData[0].datarequest.data.nomor_sep
        var nama = a.substr(15);
        const fileName = nama + ".pdf";

        downloadLink.href = linkSource;
        downloadLink.download = fileName;
        downloadLink.click();
      }
      // window.open('data:application/pdf;base64,' + responData[0].dataresponse.data);
      this.alertService.info("Info", responData[0].dataresponse.metadata.message);
    })
  }

  delete_claim() {
    if (this.selected == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    var dt1 = {}
    var dt2 = []
    // for (var i = dataSave.length - 1; i >= 0; i--) {
    dt1 = {
      "metadata": {
        "method": "delete_claim"
      },
      "data": {
        "nomor_sep": this.selected.nosep,
        "coder_nik": this.coderNIK
      }
    }
    dt2.push(dt1)
    // }

    var objData = {
      "data": dt2
    }
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
      //
      let responData = e.dataresponse;
      this.alertService.info("info", responData[0].dataresponse.metadata.message);
    })
    // })

  }
  grouper_2() {
    if (this.selected == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    if (this.dataSEPCMG == this.selected.nosep) {
      this.item.specialprocedure = ""
      this.item.specialprosthesis = ""
      this.item.specialinvestigation = ""
      this.item.specialdrug = ""
      this.popupCMG = true
    }
    else {
      this.alertService.error("info", 'Pasien ini Bukan Top-up CMG Options!')
    }

  }
  simpangrouper2() {
    var cmg = "";
    if (this.item.specialprocedure != undefined) {
      if (cmg != "") {
        cmg = cmg + '#' + this.item.specialprocedure.code
      } else {
        cmg = this.item.specialprocedure.code
      }
    }
    if (this.item.specialprosthesis != undefined) {
      if (cmg != "") {
        cmg = cmg + '#' + this.item.specialprosthesis.code
      } else {
        cmg = this.item.specialprosthesis.code
      }
    }
    if (this.item.specialinvestigation != undefined) {
      if (cmg != "") {
        cmg = cmg + '#' + this.item.specialinvestigation.code
      } else {
        cmg = this.item.specialinvestigation.code
      }
    }
    if (this.item.specialdrug != undefined) {
      if (cmg != "") {
        cmg = cmg + '#' + this.item.specialdrug.code
      } else {
        cmg = this.item.specialdrug.code
      }
    }
    var dt1 = {}
    var dt2 = []

    dt1 = {
      "metadata": {
        "method": "grouper",
        "stage": "2"
      },
      "data": {
        "nomor_sep": this.selected.nosep,
        "special_cmg": cmg//"ambil dari table hasil grouper 1"   
      }
    }
    dt2.push(dt1)




    var objData = {
      "data": dt2
    }
    var totaldijamin: any = "";
    var totaldijamina: any = "";
    var biayanaikkelas: any = "0";
    var hakkelas: any = "";
    var cmglength: any = "";
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
      // simpan response ke database
      let responData = e.dataresponse;
      this.alertService.info('INACBG', responData[0].dataresponse.metadata.message);
      this.alertService.info('INACBG', responData[0].dataresponse.response.cbg.description);
      cmglength = responData[0].dataresponse.response.special_cmg
      // if(dataSave[0].jenis_rawat==2){
      if (this.selected.jenis_rawat == 2) {
        totaldijamin = parseFloat(responData[0].dataresponse.tarif_alt[2].tarif_inacbg)
        for (let i = 0; i < cmglength.length; i++) {
          const element = cmglength[i];
          if (element.type == 'Special Drug') {
            if (totaldijamina != "") {
              totaldijamina = totaldijamina + element.tariff
            } else {
              totaldijamina = element.tariff
            }
          }
          if (element.type == 'Special Procedure') {
            if (totaldijamina != "") {
              totaldijamina = totaldijamina + element.tariff
            } else {
              totaldijamina = element.tariff
            }
          }
          if (element.type == 'Special Prosthesis') {
            if (totaldijamina != "") {
              totaldijamina = totaldijamina + element.tariff
            } else {
              totaldijamina = element.tariff
            }
          }
          if (element.type == 'Special Investigation') {
            if (totaldijamina != "") {
              totaldijamina = totaldijamina + element.tariff
            } else {
              totaldijamina = element.tariff
            }
          }
        }
        totaldijamina = responData[0].dataresponse.tarif_alt[2].tarif_sd
        totaldijamin = totaldijamin + totaldijamina
      } else {
        hakkelas = responData[0].dataresponse.response.kelas
        // if(this.dataPasienSelected.namakelas!=this.dataPasienSelected.namakelasdaftar){
        // 	biayanaikkelas=responData[0].dataresponse.response.add_payment_amt
        // }

        biayanaikkelas = Number(this.selected.biayanaikkelas)
        // biayanaikkelas=biayanaikkelas.toFixed(0);
        if (hakkelas == "kelas_1") {
          for (let i = 0; i < cmglength.length; i++) {
            const element = cmglength[i];
            if (element.type == 'Special Drug') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Procedure') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Prosthesis') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Investigation') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
          }
          totaldijamin = parseFloat(responData[0].dataresponse.tarif_alt[0].tarif_inacbg)
          totaldijamin = totaldijamin + totaldijamina
        } else if (hakkelas == "kelas_2") {
          for (let i = 0; i < cmglength.length; i++) {
            const element = cmglength[i];
            if (element.type == 'Special Drug') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Procedure') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Prosthesis') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Investigation') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
          }
          totaldijamin = parseFloat(responData[0].dataresponse.tarif_alt[1].tarif_inacbg)
          totaldijamin = totaldijamin + totaldijamina
        } else if (hakkelas == "kelas_3") {
          for (let i = 0; i < cmglength.length; i++) {
            const element = cmglength[i];
            if (element.type == 'Special Drug') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Procedure') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Prosthesis') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
            if (element.type == 'Special Investigation') {
              if (totaldijamina != "") {
                totaldijamina = totaldijamina + element.tariff
              } else {
                totaldijamina = element.tariff
              }
            }
          }
          totaldijamin = parseFloat(responData[0].dataresponse.tarif_alt[2].tarif_inacbg)
          totaldijamin = totaldijamin + totaldijamina
        }
      }
      var dataproposi = {
        "noregistrasifk": this.selected.norec,
        "totalDijamin": totaldijamin,
        "biayaNaikkelas": biayanaikkelas
      }
      this.apiService.post('bridging/inacbg/save-proposi-bridging-inacbg', dataproposi).subscribe(e => {

      })
      this.loadData()
    })
  }
  edit_claim() {
    if (this.selected == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    var dt1 = {}
    var dt2 = []
    // for (var i = dataSave.length - 1; i >= 0; i--) {
    dt1 = {
      "metadata": {
        "method": "reedit_claim"
      },
      "data": {
        "nomor_sep": this.selected.nosep,//dataSave[i].nomor_sep,      
      }
    }
    dt2.push(dt1)
    // }

    var objData = {
      "data": dt2
    }
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
      // response oke saja
      let responData = e.dataresponse;
      this.alertService.info('INACBG', responData[0].dataresponse.metadata.message);
    })
    // })
  }
  detailTagihan() {
    if (this.selected == undefined) {
      this.alertService.error("Info", 'Pilih data dulu')
      return
    }
    this.router.navigate(['detail-tagihan', this.selected.noregistrasi])
  }
  genPengajuan() {
    if (this.selected.nosep == undefined) {
      this.alertService.error("info", 'Pilih data dulu')
      return
    }
    this.item.icusatu = undefined
    this.item.icudua = undefined
    this.item.isosatu = undefined
    this.item.isodua = undefined
    this.item.isotiga = undefined
    this.item.isoempat = undefined
    this.popupPengajuanKlaim = true
  }
  push_pengajuan() {
    var stt = 'false'
    if (confirm('Generete Pengajuan Klaim Pasien Covid-19? ')) {
      // Save it!
      stt = 'true';
      var dt1 = {}
      var dt2 = []
      // for (var i = dataSave.length - 1; i >= 0; i--) {

      dt1 = {
        "metadata": {
          "method": "generate_claim_number"
        },
        "data": {
          "payor_id": "71"
        }
      }
      dt2.push(dt1)
      // }

      var objData = {
        "data": dt2
      }
      var Los = "";
      this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
        let responData = e.dataresponse[0].dataresponse.response.claim_number;
        this.alertService.info('Claim Number', responData);
        if (this.item.icusatu != undefined) {
          Los = '1;' + this.item.icusatu
        }
        if (this.item.icudua != undefined) {
          if (Los != "") {
            Los = Los + '#2;' + this.item.icudua
          } else {
            Los = '2;' + this.item.icudua
          }
        }
        if (this.item.isosatu != undefined) {
          if (Los != "") {
            Los = Los + '#3;' + this.item.isosatu
          } else {
            Los = '3;' + this.item.isosatu
          }
        }
        if (this.item.isodua != undefined) {
          if (Los != "") {
            Los = Los + '#4;' + this.item.isodua
          } else {
            Los = '4;' + this.item.isodua
          }
        }
        if (this.item.isotiga != undefined) {
          if (Los != "") {
            Los = Los + '#5;' + this.item.isotiga
          } else {
            Los = '5;' + this.item.isotiga
          }
        }
        if (this.item.isoempat != undefined) {
          if (Los != "") {
            Los = Los + '#6;' + this.item.isoempat
          } else {
            Los = '6;' + this.item.isoempat
          }
        }
        var postData = {
          "norec_pa": this.selected.norec_pa,
          "claim_number": responData,
          "loscovid": Los
        }
        this.apiService.post('bridging/inacbg/save-pengajuan-klaim', postData).subscribe(e => {
          this.popupPengajuanKlaim = false
          this.loadData();
        })
      })
    } else {
      // Do nothing!
      stt = 'false'
    }
  }
  uploadFile() {
    if (this.selected == undefined) {
      this.alertService.error("Info", 'Pilih data dulu')
      return
    }
    this.fileUpload.clear();
    this.popupUploadFile = true
    this.item.jenisfaskes = this.listFaskes[0]
    delete this.item.base64textarea
    delete this.item.fileName
  }
  // onSelect(e) {
  //   var files = e.files;
  //   var file = files[0];
  //   var a = e.files[0].name;
  //   this.item.filename = a
  //   if (files && file) {
  //     var reader = new FileReader();

  //     reader.onload = function (readerEvt) {
  //       var binaryString: any = readerEvt.target.result;
  //       // this.fileBASe=  btoa(binaryString);
  //       this.text=btoa(binaryString);
  //     };

  //     reader.readAsBinaryString(file);
  //   }
  // }
  onSelect(event) {
    let me = this;
    let file = event.files[0];
    this.item.fileName = event.files[0].name
    let reader = new FileReader();

    reader.onload = function (readerEvt) {
      var binaryString: any = readerEvt.target.result;
      me.item.base64textarea = btoa(binaryString);
    };
    reader.readAsDataURL(file);
    reader.onerror = function (error) {
      console.log('Error: ', error);
    };
  }

  upload() {
    var a = this.item.base64textarea
    var b = this.item.fileName
    var dt1 = {}
    var dt2 = []
    // for (var i = dataSave.length - 1; i >= 0; i--) {

    dt1 = {
      "metadata": {
        "method": "file_upload",
        "nomor_sep": this.selected.nosep,
        "file_class": this.item.jenisfaskes.name,
        "file_name": b,
      },
      "data": a
    }
    dt2.push(dt1)
    // }

    var objData = {
      "data": dt2
    }
    this.apiService.post('bridging/inacbg/save-bridging-inacbg', objData).subscribe(e => {
      let responData = e.dataresponse[0].dataresponse.metadata.message;
      this.alertService.info('file_upload', responData);
      this.item.base64textarea = responData
    })
  }
  filter() {
    this.popFilter = true
  }
  cariFilter() {
    this.popFilter = false
    this.loadData();
  }
  clearFilter() {
    this.popFilter = false
    this.item = {}
    this.item.periodeAwal = moment(new Date()).format('YYYY-MM-DD 00:00');
    this.item.periodeAkhir = moment(new Date()).format('YYYY-MM-DD 23:59');

    for (let i = 0; i < this.listKelompokPasien.length; i++) {
      const element = this.listKelompokPasien[i];
      if (element.kelompokpasien.indexOf("BPJS") > -1) {
        this.item.kelompokpasien = element
      }
    }
    this.loadData();
  }
}
