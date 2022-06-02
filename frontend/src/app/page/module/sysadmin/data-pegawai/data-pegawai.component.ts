import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-data-pegawai',
  templateUrl: './data-pegawai.component.html',
  styleUrls: ['./data-pegawai.component.scss'],
  providers: [ConfirmationService]
})
export class DataPegawaiComponent implements OnInit {
  selected: any;
  dataSource: any[];
  item: any = {};
  dateNow: any;
  column: any[];
  dataLogin: any;
  kelUser: any;
  selectedGrid:any;
  pop_Pegawai:boolean = false;
  idPegawai: any = "";
  listDataJenisKelamin: any[];
  listDataJenisPegawai: any[];
  listDataStatusPegawai: any[];
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
    this.loadColumn();
    this.loadCombo();
    this.loadData();    
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "80px" },
      { field: 'namalengkap', header: 'Nama Pegawai', width: "250px" },
      { field: 'tempatlahir', header: 'Tempat Lahir', width: "180px" },
      { field: 'tgllahir', header: 'Tgl Lahir', width: "160px" },
      { field: 'jeniskelamin', header: 'Jenis Kelamin', width: "180px" },
      { field: 'noidentitas', header: 'No Identitas', width: "120px" },
      { field: 'nip_pns', header: 'Nip', width: "120px" },
      { field: 'tglmasuk', header: 'Tgl Masuk', width: "160px" },
      { field: 'jenispegawai', header: 'Jenis Pegawai', width: "250px" },      
      { field: 'statuspegawai', header: 'Status', width: "280px" },             
      { field: 'alamat', header: 'Alamat', width: "250px" },      
    ];
  }

  loadCombo(){    
    this.apiService.get('sysadmin/general/get-data-combo-pegawai-rs').subscribe(datas => {        
        var combo = datas
        this.listDataJenisKelamin = combo.jeniskelamin;
        this.listDataJenisPegawai = combo.jenispegawai;
        this.listDataStatusPegawai = combo.statuspegawai;
    })
  }

  loadData(){
    this.apiService.get('sysadmin/general/get-data-pegawai-rs').subscribe(e => {
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.no = i + 1
      }
      this.dataSource = e.data      
    })    
  }

  clearData(){
    this.idPegawai = "";
    this.item.NamaLengkap = undefined;
    this.item.TempatLahir = undefined;
    this.item.tglLahir = undefined;
    this.item.jenisKelamin = undefined;
    this.item.NoIdentitas = undefined;
    this.item.Nip = undefined;
    this.item.tglMasuk = undefined;
    this.item.jenisPegawai = undefined;
    this.item.statusPegawai = undefined;
    this.item.Alamat = undefined;
  }

  editPegawai(e) {
    var dataItem = e
    this.idPegawai = dataItem.id;    
    this.item.NamaLengkap = dataItem.namalengkap;
    this.item.TempatLahir = dataItem.tempatlahir;
    if(dataItem.tgllahir)
    this.item.tglLahir = new Date(dataItem.tgllahir)
    this.item.jenisKelamin = { id: dataItem.objectjeniskelaminfk, jeniskelamin: dataItem.jeniskelamin };
    this.item.NoIdentitas = dataItem.noidentitas;
    this.item.Nip = dataItem.nip_pns;
    if(dataItem.tglmasuk)
    this.item.tglMasuk = new Date( dataItem.tglmasuk);
    this.item.jenisPegawai = { id: dataItem.objectjenispegawaifk, jenispegawai: dataItem.jenispegawai };
    this.item.statusPegawai = { id: dataItem.objectstatuspegawaifk, statuspegawai: dataItem.statuspegawai };
    this.item.Alamat = dataItem.alamat;
    this.pop_Pegawai = true;
  }

  hapusPegawai(e) {
    var objSave = {
      'id': e.id
    }
    this.apiService.post('sysadmin/general/delete-pegawai-rs', objSave).subscribe(e => {
      this.loadData()
    })
  }

  tambah() {
    this.pop_Pegawai = true
    this.clearData()
  }

  simpanPegawai(){
    if (this.item.NamaLengkap == undefined) {
      this.alertService.warn('Info', 'Nama Lengkap Belum Diisi')
      return
    }

    if (this.item.jenisKelamin == undefined) {
      this.alertService.warn('Info', 'Jenis Pegawai Belum Diisi')
      return
    }

    if (this.item.jenisPegawai == undefined) {
      this.alertService.warn('Info', 'Jenis Pegawai Belum Diisi')
      return
    }
        
    let json = {
      'id': this.idPegawai,
      'namalengkap': this.item.NamaLengkap != undefined ? this.item.NamaLengkap : null,
      'tempatlahir': this.item.TempatLahir != undefined ? this.item.TempatLahir : null,
      'tgllahir': this.item.tglLahir != undefined ? moment(this.item.tglLahir).format('YYYY-MM-DD HH:mm') : null,
      'objectjeniskelaminfk': this.item.jenisKelamin != undefined ? this.item.jenisKelamin.id : null,
      'noidentitas': this.item.NoIdentitas != undefined ? this.item.NoIdentitas : null,
      'nip_pns': this.item.Nip != undefined ? this.item.Nip : null,
      'tglmasuk': this.item.tglMasuk != undefined ? moment(this.item.tglMasuk).format('YYYY-MM-DD HH:mm') : null,
      'jenispegawai': this.item.jenisPegawai != undefined ? this.item.jenisPegawai.id : null,
      'statuspegawai': this.item.statusPegawai != undefined ? this.item.statusPegawai.id : null,
      'alamat': this.item.Alamat != undefined ? this.item.Alamat : null,
    }

    this.apiService.post('sysadmin/general/save-pegawai-rs', json).subscribe(e => {
      this.loadData();
      this.clearData();
      this.pop_Pegawai = false;
    })

  }

}
