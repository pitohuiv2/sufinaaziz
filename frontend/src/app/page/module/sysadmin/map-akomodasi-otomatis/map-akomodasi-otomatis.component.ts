import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-map-akomodasi-otomatis',
  templateUrl: './map-akomodasi-otomatis.component.html',
  styleUrls: ['./map-akomodasi-otomatis.component.scss']
})
export class MapAkomodasiOtomatisComponent implements OnInit {

  item: any = {}
  column: any[]
  dataSource: any[]
  listRuangan: []
  listJenisPelayanan: []
  listPelayanan: []
  listrg: []
  listKomponen: []
  norecMap: any
  selectedGrid: any
  isClosing: boolean
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    // private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.norecMap = undefined;
    this.loadCombo();
    this.loadColumn();
    this.load()
  }

  loadCombo() {
    this.apiService.get('sysadmin/general/get-map-administrasi-combo').subscribe(e => {
      this.listRuangan = e.ruangan;
      this.listJenisPelayanan = e.jenispelayanan;
    })
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namaruangan', header: 'Ruang', width: "200px" },
      { field: 'namaproduk', header: 'Pelayanan', width: "150px" },
      { field: 'jenispelayanan', header: 'Jenis Pelayanan', width: "150px" },
    ];
  }

  getProduk() {
    this.apiService.get("sysadmin/general/get-combo-akomodasi?produk=1&objectruanganfk=" + this.item.ruangan.id).subscribe(e => {
      // for (var i = e.listakomodasi.length - 1; i >= 0; i--) {
      //   e.listakomodasi[i].no = i + 1
      //   if (e.listakomodasi[i].israwatgabung == 1) {
      //     e.listakomodasi[i].israwatgabungSS = 'Yes'
      //   } else {
      //     e.listakomodasi[i].israwatgabungSS = 'No'
      //   }
      // }
      this.listPelayanan = e.produk;
      // this.listrg = [
      //   { id: 1, status: 'Yes' },
      //   { id: 2, status: 'No' }
      // ]
      // this.dataSource = e.listakomodasi;
    })
    this.load()
  }

  getKomponenHarga() {
    this.item.Harga = 0
    this.listKomponen = []
    if (this.item.pelayanan != undefined) {
      this.apiService.get("tindakan/get-komponenharga?idRuangan="
        + this.item.ruangan.id
        + "&idKelas=" + 6
        + "&idProduk=" + this.item.pelayanan.id
        + "&idJenisPelayanan=" + this.item.jenispelayanan.id,
      ).subscribe(e => {
        this.listKomponen = e.data;
        this.item.Harga = e.data2[0].hargasatuan
        this.item.jumlah = 1;
      })
    }
  }

  batalInput() {
    this.item.Harga = 0;
    this.listKomponen = [];
    this.item.pelayanan = undefined;
    this.item.jenispelayanan = undefined;
    this.item.ruangan = undefined;
  }

  hapusAll() {
    if (this.item.pelayanan == undefined) {
      this.alertService.error('Info', "Pilih Pelayanan dulu")
      return
    }
    if (this.item.ruangan == undefined) {
      this.alertService.error('Info', "Pilih Ruangan dulu")
      return
    }
    var maid = '';
    if (this.norecMap != undefined) {
      maid = this.norecMap
    }
    var rg = null;
    if (this.item.rg != undefined) {
      if (this.item.rg.status = 'Yes') {
        rg = 1
      } else {
        rg = null
      }
    }
    var objSave = {
      maid: maid,
      pelayanan: this.item.pelayanan.id,
      rg: rg,
      ruangan: this.item.ruangan.id,
      jenispelayanan: this.item.jenispelayanan.id,
      status: 'HAPUS'
    }
    this.apiService.post('sysadmin/general/save-map-akomodasi-otomatis', objSave).subscribe(e => {
      this.batalInput();
      this.load()
    })
  }

  simpanMapping() {
    if (this.item.pelayanan == undefined) {
      this.alertService.error('Info', "Pilih Pelayanan dulu")
      return
    }
    if (this.item.ruangan == undefined) {
      this.alertService.error('Info', "Pilih Ruangan dulu")
      return
    }
    var maid = '';
    if (this.norecMap != undefined) {
      maid = this.norecMap
    }
    var rgg = 'NO';
    if (this.item.rg != undefined) {
      if (this.item.rg.status == 'Yes') {
        rgg = 'YES'
      } else {
        rgg = "NO"
      }
    }
    var objSave = {
      maid: maid,
      pelayanan: this.item.pelayanan.id,
      rg: rgg,
      ruangan: this.item.ruangan.id,
      jenispelayanan: this.item.jenispelayanan.id,
      status: 'SIMPAN_JANG'
    }
    this.apiService.post('sysadmin/general/save-map-akomodasi-otomatis', objSave).subscribe(e => {
      this.batalInput();
      this.load()
    })
  }
  load() {
    var ru = ''
    if (this.item.ruangan) {
      ru = this.item.ruangan.id
    }
    this.dataSource =[]
    this.apiService.get("sysadmin/general/get-akomodasi?objectruanganfk=" +ru ).subscribe(dat => {
      for (var i = dat.listakomodasi.length - 1; i >= 0; i--) {
        dat.listakomodasi[i].no = i + 1
        if (dat.listakomodasi[i].israwatgabung == 1) {
          dat.listakomodasi[i].israwatgabungSS = 'Yes'
        } else {
          dat.listakomodasi[i].israwatgabungSS = 'No'
        }
      }
      this.dataSource = dat.listakomodasi;
    });
  }
  hapusRow(e) {
    var objSave = {
      maid: e.maid,
      status: 'HAPUS'
    }
    this.apiService.post('sysadmin/general/save-map-akomodasi-otomatis', objSave).subscribe(e => {
      // this.batalInput();
      this.dataSource = []
      this.load()
    })
  }
}
