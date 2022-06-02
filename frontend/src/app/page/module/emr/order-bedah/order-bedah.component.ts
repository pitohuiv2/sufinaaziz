
import { RekamMedisComponent } from '../rekam-medis/rekam-medis.component';
import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MenuItem, MessageService, TreeNode } from 'primeng/api';
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
  selector: 'app-order-bedah',
  templateUrl: './order-bedah.component.html',
  styleUrls: ['./order-bedah.component.scss']
})
export class OrderBedahComponent implements OnInit {
  indexTab: number
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
  columnPaket: any[]
  pop_paket: boolean
  dataSourcePaket: any[]
  columnRiwayatPengkajian: any[]
  dataSourcePengkajian: any[]
  isLoadingNav: boolean
  treeSourceMenu: MenuItem[] = []
  headData: any = {}
  nomorERM = '-'
  selectedFile:any[]
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
    if (this.rekamMedis.header.norec == undefined) {
      var cache = this.cacheHelper.get('cacheEMR_qwertyuiop')
      if (cache != undefined) {
        cache = JSON.parse(cache)
        this.headData = cache
      }
    } else {
      this.headData = this.rekamMedis.header
    }
    if (this.headData.norec == undefined) {
      window.history.back()
    }
    this.skeleton = this.loadSkeleton()
    this.item.idPegawaiLogin = this.authService.getDataLoginUser().pegawai.id
    this.apiService.get("emr/get-combo-penunjang?departemenfk=kdDepartemenOK").subscribe(dat => {
      this.item.ruanganAsal = this.headData.namaruangan
      this.item.namaRuangan = this.headData.namaruangan
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
      { field: 'tglorder', header: 'Tgl Order', width: "120px" },
      { field: 'tglrencana', header: 'Tgl Rencana', width: "120px" },
      { field: 'noorder', header: 'No Order', width: "100px" },
      { field: 'dokter', header: 'Dokter', width: "150px" },
      { field: 'namaruanganasal', header: 'Ruangan Asal', width: "150px" },
      { field: 'namaruangantujuan', header: 'Ruangan', width: "150px" },
      { field: 'keteranganlainnya', header: 'Keterangan', width: "150px" },
      { field: 'statusorder', header: 'Status', width: "70px" },
      // { field: 'cito', header: 'Cito', width: "70px" },
    ];
    this.columnPaket = [
      { field: 'namapaket', header: 'Nama Paket', width: "120px" },
      { field: 'jml', header: 'Jumlah', width: "80px" },
    ];
    this.columnRiwayatPengkajian = [
      { field: 'tglemr', header: 'Tgl EMR', width: "100px" },
      { field: 'noemr', header: 'No EMR', width: "100px" },
      { field: 'noregistrasi', header: 'No Registrasi', width: "100px" },
      { field: 'namaruangan', header: 'Ruangan', width: "150px" },
    ]
  }

  getProduk(ruangan) {

    this.listProdukCek = []
    this.item.layanan = []
    this.produkDef = []
    if (ruangan == null) return
    // debugger
    this.apiService.get("sysadmin/general/get-tindakan-with-details?idRuangan="
      + ruangan.id
      + "&idKelas="
      + this.headData.objectkelasfk
      + "&idJenisPelayanan="
      + this.headData.jenispelayanan
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
      this.alertService.warn("Info", "Pilih Ruangan Tujuan terlebih dahulu!!")
      return
    }
    if (this.item.tglPelayanan == undefined) {
      this.alertService.warn("Info", "Pilih Tgl Order  terlebih dahulu!!")
      return
    }
    if (this.item.layanan == undefined || this.item.layanan.length == 0) {
      this.alertService.warn("Info", "Pilih layanan terlebih dahulu!!")
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
          objectruanganfk: this.headData.objectruanganfk,
          objectruangantujuanfk: this.item.ruangantujuan.id,
          pemeriksaanluar: this.item.pemeriksaanKeluar == true ? 1 : 0,
          iscito: this.item.iscito != undefined && this.item.iscito == true ? this.item.iscito : false,
          objectkelasfk: this.headData.objectkelasfk,
          nourut: null,
        }
        data2.push(data)
      }
    }

    if (data2.length == 0) {
      this.alertService.warn("Info", "Pilih Pelayanan dulu")
      return
    }
    var objSave = {
      // tanggal: moment(this.item.tglPelayanan).format('YYYY-MM-DD HH:mm:ss'),
      tglrencana: moment(this.item.tglPelayanan).format('YYYY-MM-DD HH:mm:ss'),
      norec_so: '',
      norec_apd: this.headData.norec_apd,
      norec_pd: this.headData.norec_pd,
      qtyproduk: data2.length,
      objectruanganfk: this.headData.objectruanganfk,
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
      this.apiService.postLog('Order Bedah', 'Norec strukorder_t', e.strukorder.norec, 'Order Laboratorium No Order - '
        + e.strukorder.noorder + ' dengan No Registrasi ' + this.headData.noregistrasi).subscribe(res => {
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
        this.loadRiwayat('noregistrasi=' + this.headData.noregistrasi)
      })
  }
  order() {
    this.isRiwayat = false
    this.batal()
    this.getProduk(this.item.ruangantujuan)
  }
  riwayat() {
    this.isRiwayat = true
    this.loadRiwayat('noregistrasi=' + this.headData.noregistrasi)
  }
  loadRiwayat(params) {
    this.apiService.get('emr/get-riwayat-order-penunjang?' + params + '&setting=kdDepartemenOK').subscribe(e => {
      for (var i = e.daftar.length - 1; i >= 0; i--) {
        e.daftar[i].no = i + 1
        if (e.daftar[i].cito == true) {
          e.daftar[i].cito = '✔'
        } else {
          e.daftar[i].cito = '✘'
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
    if (this.selectedData.status == 'SELESAI') {
      this.alertService.warn('Info', 'Status Sudah Selesai tidak bisa di hapus')
      return
    }
    var data = {
      norec_order: this.selectedData.norec
    }
    this.apiService.post('emr/delete-order-pelayanan', data)
      .subscribe(e => {
        this.loadRiwayat('noregistrasi=' + this.headData.noregistrasi)
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
  }
  clearSelection() {
    var arrobj = Object.keys(this.item.layanan)
    for (let x = 0; x < arrobj.length; x++) {
      const element2 = arrobj[x];
      this.item.layanan[element2] = false
    }
    this.getSelected()
  }
  closePaket() {
    this.pop_paket = false
    this.item.paket = false
  }


  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
    } else if (e.index == 1) {
      this.loadRiwayatEMR()
      console.log(this.activeRoute)
      // this.router.navigate(['order-bedah'], { relativeTo: this.activeRoute });
    } else {
      this.loadTreeView()
    }
  }
  buatBaru() {
    this.indexTab = 2
    this.handleChangeTab({ index: this.indexTab })
    this.router.navigate(['pengkajian-bedah-detail', 2, '-'], { relativeTo: this.activeRoute });
    this.nomorERM = '-'
  }
  editD(e) {
    this.nomorERM = e.noemr
    this.indexTab = 2
    this.handleChangeTab({ index: this.indexTab })
    this.router.navigate(['pengkajian-bedah-detail', 2, e.noemr], { relativeTo: this.activeRoute });
  }
  hapusPengkajian(e) {
    this.nomorERM = '-'
    this.apiService.post('emr/hapus-emr-transaksi-norec', { norec: e.norec }).subscribe(e => {
      this.loadRiwayatEMR()
      this.apiService.postLog('Hapus EMR', 'norec emrpasien_t', e.norec,
        'Hapus No EMR - ' + e.emrpasienfk + ' pada No Registrasi  '
        + this.headData.noregistrasi + ' - Pasien : ' + this.headData.namapasien).subscribe(res => {
        })
    })
  }
  asupKaForm(e) {
    this.nomorERM =  e.emrpasienfk
    this.indexTab = 2
    this.handleChangeTab({ index: this.indexTab })
    this.router.navigate(['pengkajian-bedah-detail', e.emrfk, e.emrpasienfk], { relativeTo: this.activeRoute });
  }
  hapusForm(e) {
    var json = {
      'noemr': e.noemr,
      'reportdisplay': e.reportdisplay,
      'idemr': e.emrfk,
      'norec': e.norec,
      'idpegawai': this.authService.getPegawaiId(),
    }
    this.apiService.post('emr/disable-emr-details', json).subscribe(z => {
      this.apiService.postLog('Hapus EMR', 'norec emrpasien_t', e.norec,
        'Hapus Satu Form EMR ( ' + e.namaform + ' ) No EMR - ' + e.noemr + ' pada No Registrasi  '
        + this.headData.noregistrasi + ' - Pasien : ' + this.headData.namapasien).subscribe(res => {
        })
    })
  }
  loadTreeView() {
    this.treeSourceMenu = [];
    this.isLoadingNav = true
    this.apiService.get("emr/get-menu-rekam-medis-dynamic?namaemr=bedah").subscribe(e => {
      this.isLoadingNav = false
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        element.name = element.label
        if(element.items == undefined) {
          element.command = (event) => {
            event.node = event.item
            this.nodeSelect(event);
          };
        }else{
          for (let ii = 0; ii < element.items.length; ii++) {
            const element2 = element.items[ii];
            element2.name = element2.label
            if(element2.items == undefined) {
              element2.command = (event) => {
                event.node = event.item
                this.nodeSelect(event);
              };
            }else{
              for (let iii = 0; iii < element2.items.length; iii++) {
                const element3 = element2.items[iii];
                element3.name = element3.label
                if(element3.items == undefined) {
                  element3.command = (event) => {
                    event.node = event.item
                    this.nodeSelect(event);
                  };
                }else{
                  for (let iv= 0; iv < element3.items.length; iv++) {
                    const element4 = element3.items[iv];
                    element4.name = element4.label
                    if(element4.items == undefined) {
                      element4.command = (event) => {
                        event.node = event.item
                        this.nodeSelect(event);
                      };
                    }else{
                      for (let v= 0; v < element4.items.length; v++) {
                        const element5 = element4.items[v];
                        element5.name = element5.label
                        if(element5.items == undefined) {
                          element5.command = (event) => {
                            event.node = event.item
                            this.nodeSelect(event);
                          };
                        }else{
                          // for (let v= 0; v < element4.items.length; v++) {
                          //   const element5 = element4.items[v];
                          //   element5.name = element5.label
                          // }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      
        element.icon = 'pi pi-fw pi-bars'
      }
      this.treeSourceMenu = e.data
    })
  }
  nodeSelect(event) {
    // debugger
    // this.cacheHelper.set('cacheEMR_qwertyuiop', undefined)
  
    let idTree = event.node.id
    let urlTrue = event.node.reportdisplay
    if (urlTrue == null) {
      this.router.navigate(['pengkajian-bedah-detail', idTree,this.nomorERM ], { relativeTo: this.activeRoute });
    } else {
      this.router.navigate([urlTrue], { relativeTo: this.activeRoute });
    }
  }
  loadRiwayatEMR() {
    var paramSearch = 'noregistrasi=' + this.headData.noregistrasi
    this.apiService.get("emr/get-emr-transaksi-detail-form?" + paramSearch + "&jenisEmr=bedah").subscribe(dat => {
      this.dataSourcePengkajian = dat.data
    });
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
