
import { RekamMedisComponent } from '../rekam-medis/rekam-medis.component';
import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService, TreeNode } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

import { FilterPipe } from '../../../../pipe/filter.pipe';
import { Pipe, PipeTransform } from '@angular/core';
@Component({
  selector: 'app-order-rad',
  templateUrl: './order-rad.component.html',
  styleUrls: ['./order-rad.component.scss']
})
export class OrderRadComponent implements OnInit {
  item: any = {
    tglPelayanan: new Date(),
    layanan: []
  }
  listProdukCek: any[] = []
  produkDef: any[] = []
  listRuanganTujuan: any[] = []
  skeleton: any = []
  searchText: string = "";
  selected_count: number = 0;
  selected_games: any[] = []
  listChecked = []
  isSimpan: any
  isRiwayat: boolean
  dataSourceRiwayat: any = []
  columnRiwayat: any[]
  selectedData: any
  pop_expertise: boolean
  columnPaket: any[]
  pop_paket: boolean
  dataSourcePaket: any[]
  constructor(public rekamMedis: RekamMedisComponent,
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,) { }
  ngAfterViewInit() {

  }
  ngOnInit(): void {
    this.skeleton = this.loadSkeleton()
    this.item.idPegawaiLogin = this.authService.getDataLoginUser().pegawai.id
    this.apiService.get("emr/get-combo-penunjang?departemenfk=kdDepartemenRad").subscribe(dat => {
      this.item.ruanganAsal = this.rekamMedis.header.namaruangan
      this.item.namaRuangan = this.rekamMedis.header.namaruangan
      this.listRuanganTujuan = dat.ruangantujuan;
      this.item.ruangantujuan = {
        id: dat.ruangantujuan[0].id,
        namaruangan: dat.ruangantujuan[0].namaruangan,
        instalasiidfk: dat.ruangantujuan[0].instalasiidfk
      }
      this.getProduk(this.item.ruangantujuan)
    });
    this.columnRiwayat = [
      { field: 'noregistrasi', header: 'No Registrasi', width: "100px" },
      { field: 'tglorder', header: 'Tgl Order', width: "100px" },
      { field: 'noorder', header: 'No Order', width: "100px" },
      { field: 'dokter', header: 'Dokter', width: "150px" },
      { field: 'namaruanganasal', header: 'Ruangan Asal', width: "150px" },
      { field: 'namaruangantujuan', header: 'Ruangan', width: "150px" },
      { field: 'keteranganlainnya', header: 'Keterangan', width: "150px" },
      { field: 'statusorder', header: 'Status', width: "70px" },
      { field: 'cito', header: 'Cito', width: "70px" },
    ];
    this.columnPaket = [
      { field: 'namapaket', header: 'Nama Paket', width: "120px" },
      { field: 'jml', header: 'Jumlah', width: "80px" },
    ];
  }

  getProduk(ruangan) {

    this.listProdukCek = []
    this.item.layanan = []
    this.produkDef = []
    if (ruangan == null) return
    this.apiService.get("sysadmin/general/get-tindakan-with-details?idRuangan="
      + ruangan.id
      + "&idKelas="
      + this.rekamMedis.header.objectkelasfk
      + "&idJenisPelayanan="
      + this.rekamMedis.header.jenispelayanan
      + "&isLabRad=true")
      .subscribe(x => {
        this.listProdukCek = x.details
        this.produkDef = x.data
      })
  }
  getSelected() {
    if (this.item.layanan.length > 0) {
      var arrobj = Object.keys(this.item.layanan)
      for (var x = 0; x < arrobj.length; x++) {
        const element = arrobj[x];
        if (this.item.layanan[parseInt(element)] == true) {
          for (var i = 0; i < this.produkDef.length; i++) {
            const element2 = this.produkDef[i];
            if (element2.id == element) {
              for (var z = 0; z < this.listChecked.length; z++) {
                const element3 = this.listChecked[z];
                if (element3.namaproduk == element2.namaproduk) {
                  this.listChecked.splice(z, 1)
                }
              }
              this.listChecked.push({ namaproduk: element2.namaproduk })
            }
          }
        } else {
          for (var i = 0; i < this.produkDef.length; i++) {
            const element2 = this.produkDef[i];
            if (element2.id == element) {
              for (var z = 0; z < this.listChecked.length; z++) {
                const element3 = this.listChecked[z];
                if (element3.namaproduk == element2.namaproduk) {
                  this.listChecked.splice(z, 1)
                }
              }
            }
          }
        }
      }

    }
  }
  clearFilter() {
    this.searchText = "";
  }
  save() {
    if (this.item.ruangantujuan == undefined) {
      this.alertService.warn("Info","Pilih Ruangan Tujuan terlebih dahulu!!")
      return
    }
    if (this.item.tglPelayanan == undefined) {
      this.alertService.warn("Info","Pilih Tgl Order  terlebih dahulu!!")
      return
    }
    if (this.item.layanan == undefined || this.item.layanan.length == 0) {
      this.alertService.warn("Info","Pilih layanan terlebih dahulu!!")
      return
    }
    var arrobj = Object.keys(this.item.layanan)
    var data2 = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      if (this.item.layanan[parseInt(arrobj[i])] == true) {
        var data = {
          no: i + 1,
          produkfk: arrobj[i],
          namaproduk: arrobj[i],
          qtyproduk: 1,
          objectruanganfk: this.rekamMedis.header.objectruanganfk,
          objectruangantujuanfk: this.item.ruangantujuan.id,
          pemeriksaanluar: this.item.pemeriksaanKeluar == true ? 1 : 0,
          iscito: this.item.iscito != undefined && this.item.iscito == true ? this.item.iscito : false,
          objectkelasfk: this.rekamMedis.header.objectkelasfk,
          nourut: null,
        }
        data2.push(data)
      }
    }
    if(data2.length == 0){
      this.alertService.warn("Info","Pilih Pelayanan dulu")
      return
    }
    var objSave = {
      tanggal: moment(this.item.tglPelayanan).format('YYYY-MM-DD HH:mm:ss'),
      norec_so: '',
      norec_apd: this.rekamMedis.header.norec_apd,
      norec_pd: this.rekamMedis.header.norec_pd,
      qtyproduk: data2.length,
      objectruanganfk: this.rekamMedis.header.objectruanganfk,
      objectruangantujuanfk: this.item.ruangantujuan.id,
      departemenfk: this.item.ruangantujuan.instalasiidfk,
      pegawaiorderfk: this.item.idPegawaiLogin,
      keterangan: this.item.keterangan != undefined ? this.item.keterangan : null,
      iscito: this.item.iscito != undefined && this.item.iscito == true ? this.item.iscito : false,
      details: data2,
    }

    this.isSimpan = true
    this.apiService.post('emr/save-order-pelayanan', objSave).subscribe(e => {
      this.item.layanan = []

      this.isSimpan = false
      this.apiService.postLog('Order Radiologi', 'Norec strukorder_t', e.strukorder.norec, 'Order Radiologi No Order - '
        + e.strukorder.noorder + ' dengan No Registrasi ' + this.item.noregistrasi).subscribe(res => {
        })
      this.batal();
    }, error => {
      this.isSimpan = false
    })
  }
  batal() {
    var arrobj = Object.keys(this.item.layanan)
    for (var i = arrobj.length - 1; i >= 0; i--) {
      this.item.layanan[parseInt(arrobj[i])] = false
    }
    this.listChecked = []
    // this.item.layanan = []
    delete this.item.keterangan
  }
  onRowSelect(E) {
  }
  hapusD(e) {
    if (e.status == 'SELESAI') {
      this.alertService.warn('Info', 'Status Sudah Selesai tidak bisa di hapus')
      return
    }
    var data = {
      norec_order: e.norec
    }
    this.apiService.post('emr/delete-order-pelayanan', data)
      .subscribe(e => {
        this.loadRiwayat('noregistrasi=' + this.rekamMedis.header.noregistrasi)
      })
  }
  order() {
    this.isRiwayat = false
    this.batal()
    this.getProduk(this.item.ruangantujuan)
  }
  riwayat() {
    this.isRiwayat = true
    this.loadRiwayat('noregistrasi=' + this.rekamMedis.header.noregistrasi)
  }
  loadRiwayat(params) {
    this.apiService.get('emr/get-riwayat-order-penunjang?' + params + '&setting=kdDepartemenRad').subscribe(e => {
      for (var i = e.daftar.length - 1; i >= 0; i--) {
        e.daftar[i].no = i + 1
        if (e.daftar[i].cito == true) {
          e.daftar[i].cito = '???'
        } else {
          e.daftar[i].cito = '???'
        }
        for (var x = e.daftar[i].details.length - 1; x >= 0; x--) {
          const element = e.daftar[i].details[x];
          if (element.norec_hr != undefined && element.norec_hr != '') {
            element.expertise = "???";
          } else {
            element.expertise = "???";
          }
        }
      }
      this.dataSourceRiwayat = e.daftar
    });
  }
  hapus() {
    if (this.selectedData == undefined) {
      this.alertService.info('Info', 'Pilih data dulu')
      return
    }
    if (this.selectedData.status == 'Verifikasi' || this.selectedData.status == 'Sudah diproses') {
      this.alertService.warn('Info', 'Status Sudah Selesai tidak bisa di hapus')
      return
    }
    var data = {
      norec_order: this.selectedData.norec
    }
    this.apiService.post('emr/delete-order-pelayanan', data)
      .subscribe(e => {
        this.loadRiwayat('noregistrasi=' + this.rekamMedis.header.noregistrasi)
      })
  }
  lihatHasil(e) {

  }
  expertise(e) {
    if(e.norec_hr == null){
      this.alertService.info('Info', 'Belum ada Expertise')
      return
    }
    this.pop_expertise = true
    this.apiService.get('emr/get-expertise?norec=' + e.norec_pp + '&produkfk=' + e.id).subscribe( re=> {
      this.item.nofoto = re.data.nofoto;
      this.item.dokterExp = re.data.namalengkap;
      this.item.tglExp = re.data.tanggal;
      this.item.expertise = re.data.keterangan;
    })
  }
  cekPaket(e) {
    if (e == true) {
      if (this.item.ruangantujuan == undefined) {
        this.alertService.warn('Info', 'Pilih ruangan dulu')
        return
      }
      this.pop_paket = true
      this.apiService.get('general/get-paket-tindakan').subscribe(e => {
        this.dataSourcePaket = e
      })
    }
  }
  addPaket(e) {
    var arr = e.details
    var arrobj = Object.keys(this.item.layanan)
    for (var i = 0; i < arr.length; i++) {
      const element = arr[i];
      if (arrobj.length > 0) {
        for (let x = 0; x < arrobj.length; x++) {
          const element2 = arrobj[x];
          if (element.objectprodukfk != element2)
            this.item.layanan[element.objectprodukfk] = true
        }
      } else {
        this.item.layanan[element.objectprodukfk] = true
      }
    }
    this.getSelected()
    this.pop_paket = false
  }
 
  closePaket() {
    this.pop_paket = false
    this.item.paket = false
  }
  clearSelection(){
    var arrobj = Object.keys(this.item.layanan)
    for (let x = 0; x < arrobj.length; x++) {
      const element2 = arrobj[x];
        this.item.layanan[element2] = false
    }
    this.getSelected()
  }
  loadSkeleton() {
    return [
      { 'id': 1, details: [{ 'class': 'p-col-2', details: [{ 'class': 'p-mb-0' }] }] },
      {
        'id': 2, details: [
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
        ]
      },
      { 'id': 3, details: [{ 'class': 'p-col-2', details: [{ 'class': 'p-mb-0' }] }] },
      {
        'id': 4, details: [
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
          { 'class': 'p-col-4', details: [{ 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }, { 'class': 'p-mb-2' }] },
        ]
      }
    ]
  }
}
