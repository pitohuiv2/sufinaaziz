import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MessageService, SelectItem } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-input-master-produk',
  templateUrl: './input-master-produk.component.html',
  styleUrls: ['./input-master-produk.component.scss'],
  providers: [ConfirmationService]
})
export class InputMasterProdukComponent implements OnInit {
  idProduk: any;
  params: any = {};
  item: any = {};
  isSimpan: any;
  kelUser: any;
  dateNow: any;
  dataLogin: any;
  indexTab = 0;
  listStatus: any[] = [];
  listKelompokUser: any[] = [];
  listDetailJenisProduk: any[] = [];
  listJenisBarang: any[] = [];
  listKelompokBarang: any[] = [];
  listSatuan: any[] = [];
  listSediaan: any[] = [];
  listJenisPeriksaPenunjang: any[] = [];
  listJenisPeriksa: any[] = [];
  listBahanSample: any[] = [];
  listGolonganDarah: any[] = [];
  listRhesus: any[] = [];
  data: any;
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
    this.isSimpan = false;
    this.dateNow = new Date();
    this.dataLogin = this.authService.getDataLoginUser();
    this.kelUser = this.dataLogin.kelompokUser;
    this.item.tglKirim = this.dateNow;
    this.loadCombo();
    this.firstLoad();
  }
  handleChangeTab(e){
    
  }
  firstLoad() {
    this.route.params.subscribe(params => {
      this.params.idproduk = params['idproduk'];
      this.idProduk = this.params.idproduk;
      this.loadData();
    });
  }

  loadCombo() {
    this.listStatus = [{ id: 1, stt: 't', status: 'Aktif' }, { id: 2, stt: 'f', status: 'Tidak Aktif' }]
    this.item.status = this.listStatus[0];
    this.apiService.get("sysadmin/general/get-combo-master").subscribe(table => {
      var dataCombo = table;
      this.listSatuan = dataCombo.satuanstandar;
      this.listSediaan = dataCombo.sediaan;
      this.listGolonganDarah = dataCombo.goloangandarah;
      this.listRhesus = dataCombo.rhesus;
      this.listKelompokUser = dataCombo.kelompokuser;
    });
    this.apiService.get("sysadmin/general/get-combo-detailjenisproduk").subscribe(table => {
      var dataCombo = table;
      this.listDetailJenisProduk = dataCombo.detailjenisproduk;
    });
  }

  loadData() {
    if (this.idProduk != "-") {
      this.apiService.get('sysadmin/general/get-master-pelayanan?kdProduk=' + this.idProduk).subscribe(table => {        
        this.data = table[0];
        this.item = this.data;
        if (this.data.aktif != undefined) {
          for (let i = 0; i < this.listStatus.length; i++) {
            const element = this.listStatus[i];
            if (this.data.aktif == element.status) {
              this.item.status = element;
              break;
            }
          }
        }
        this.item.kelompokuser = {id: this.data.kelompokuseridfk ,kelompokuser: this.data.kelompokuser};        
        this.item.satuan = {id: this.data.satuanstandaridfk ,satuanstandar: this.data.satuanstandar};
        this.item.sediaan = {id: this.data.sediaanidfk ,sediaan: this.data.sediaan};
        this.item.golongandarah = {id: this.data.golongandarahidfk ,golongandarah: this.data.golongandarah};
        this.item.rhesus = {id: this.data.rhesusidfk ,rhesus: this.data.rhesus};
        if (this.data.isfornas != undefined) {
          for (let i = 0; i < this.listStatus.length; i++) {
            const element = this.listStatus[i];
            if (this.data.aktif == element.status) {
              this.item.isfornas = element;
              break;
            }
          }
        }
        if (this.data.isgeneric != undefined) {
          for (let i = 0; i < this.listStatus.length; i++) {
            const element = this.listStatus[i];
            if (this.data.isgeneric == element.status) {
              this.item.generik = element;
              break;
            }
          }
        }
        if (this.data.isantibiotik != undefined) {
          for (let i = 0; i < this.listStatus.length; i++) {
            const element = this.listStatus[i];
            if (this.data.isantibiotik == element.status) {
              this.item.antibiotik = element;
              break;
            }
          }
        }
        if (this.data.ispsikotropika != undefined) {
          for (let i = 0; i < this.listStatus.length; i++) {
            const element = this.listStatus[i];
            if (this.data.ispsikotropika == element.status) {
              this.item.psikotropika = element;
              break;
            }
          }
        }
        this.getDetailJenis();
      })
    }
  }

  getDetailJenis(){
    if (this.data != undefined) {
      if (this.data.detailjenisproduk != undefined) {
        for (let i = 0; i < this.listDetailJenisProduk.length; i++) {
          const element = this.listDetailJenisProduk[i];
          if (element.id == this.data.detailjenisprodukidfk) {
            this.item.detailjenisproduk = element;
            break;
          }
        }
        this.isiJenisProduk();
      }
    }
  }

  isiJenisProduk() {
    if (this.item.detailjenisproduk != undefined) {
      this.listJenisBarang = this.item.detailjenisproduk.jenisproduk;
      
      if (this.data != undefined && this.data.jenisprodukidfk != undefined) {
        this.item.jenisproduk = {id: this.data.jenisprodukidfk ,jenisproduk: this.data.jenisproduk};
        this.isiKelompokProduk()
      }
    }
  }

  isiKelompokProduk() {
    if (this.item.jenisproduk != undefined) {
      this.listKelompokBarang = this.item.jenisproduk.kelompokproduk;
      if (this.data != undefined && this.data.kelompokprodukidfk != undefined) {
        this.item.kelompokroduk = {id: this.data.kelompokprodukidfk ,kelompokroduk: this.data.kelompokroduk};        
      }
    }
  }

  kosongkan() {
    this.item = {};
    this.item.status = this.listStatus[0];
    this.isSimpan = false;
  }

  batal() {
    this.kosongkan();
  }

  Kembali() {
    this.router.navigate(['master-produk'])
  }

  simpan() {
    if (this.item.namaproduk == undefined) {
      this.alertService.error("Info", "Nama Pelayanan Masih Kosong!")
      return
    }
    if (this.item.detailjenisproduk == undefined) {
      this.alertService.error("Info", "Detail Jenis Pelayanan Masih Kosong!")
      return
    }
    if (this.item.jenisproduk == undefined) {
      this.alertService.error("Info", "Jenis Pelayanan Masih Kosong!")
      return
    }
    // if (this.item.kelompokroduk == undefined) {
    //   this.alertService.error("Info", "Kelompok Pelayanan Masih Kosong!")
    //   return
    // }
    if (this.item.satuan == undefined) {
      this.alertService.error("Info", "Satuan Pelayanan Masih Kosong!")
      return
    }
    this.isSimpan = true;

    var objSave = {
      //** Data Pelayanan */
        "id": this.item.id != undefined ? this.item.id : '-',
        "aktif": this.item.status != undefined ? this.item.status.stt : 't',
        "namaproduk": this.item.namaproduk != undefined ? this.item.namaproduk : null,
        "kdproduk_intern": this.item.kdproduk_intern != undefined ? this.item.kdproduk_intern : null,
        "kodebmn":this.item.kodebmn != undefined ? this.item.kodebmn : null,        
        "keterangan": this.item.keterangan != undefined ? this.item.keterangan : null,
        "kelompokuser": this.item.kelompokuser != undefined ? this.item.kelompokuser.id : null,
      //** END Data Pelayanan */
      //** Detail Pelayanan */
        "objectdetailjenisprodukfk": this.item.detailjenisproduk != undefined ? this.item.detailjenisproduk.id : null,
      //** END Detail Pelayanan */
      //** Spesifikasi Pelayanan */
        "objectsatuanstandarfk": this.item.satuan != undefined ? this.item.satuan.id : null,
        "objectsediaanfk": this.item.sediaan != undefined ? this.item.sediaan.id : null,
        "kekuatan": this.item.kekuatan != undefined ? this.item.kekuatan : 1, 
        "deskripsiproduk": this.item.deskripsi != undefined ? this.item.deskripsi : null,      
        "spesifikasi": this.item.spesifikasi != undefined ? this.item.spesifikasi : null,
      //** END Spesifikasi Pelayanan */
      //** Farmasi */
        "isfornas": this.item.fornas != undefined ? this.item.fornas.stt : 'f',
        "isantibiotik": this.item.antibiotik != undefined ? this.item.antibiotik.stt : 'f',
        "ispsikotropika": this.item.psikotropika != undefined ? this.item.psikotropika.stt : 'f',
        "isgeneric": this.item.generik != undefined ? this.item.generik.stt : 'f',
      //** END Farmasi */
      //** Gizi */
        "qtykalori": this.item.qtykalori != undefined ? this.item.qtykalori : 0, 
        "qtykarbohidrat": this.item.qtykarbohidrat != undefined ? this.item.qtykarbohidrat : 0,
        "qtylemak": this.item.qtylemak != undefined ? this.item.qtylemak : 0,
        "qtyporsi": this.item.qtyprotein != undefined ? this.item.qtyprotein : 0,
        "qtyprotein": this.item.qtyporsi != undefined ? this.item.qtyporsi : 0,
      //** END Gizi */
      //** Penunjang */
        "objectjenisperiksafk": this.item.jenisperiksa != undefined ? this.item.jenisperiksa.id : null,
        "objectjenisperiksapenunjangfk": this.item.jenisperiksapenunjang != undefined ? this.item.jenisperiksapenunjang.id : null,
        "nilainormal":  this.item.nilainormal != undefined ? this.item.nilainormal : 0,
        "bahansamplefk": this.item.bahansample != undefined ? this.item.bahansample.id : null,
        "golongandarahfk": this.item.golongandarah != undefined ? this.item.golongandarah.id : null,
        "rhesusfk": this.item.rhesus != undefined ? this.item.rhesus.id : null,
      //** END Penunjang */                                    
    }

    this.apiService.post("sysadmin/general/save-master-pelayanan", objSave).subscribe(table => {
      var cacheMasterProdukLayanan = {
        0: table.data.namaproduk,       
      }
      this.cacheHelper.set('cacheMasterProdukLayanan', cacheMasterProdukLayanan);
      this.router.navigate(['master-produk'])
    });

  }

}
