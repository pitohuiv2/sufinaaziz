import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-mapping-modul',
  templateUrl: './mapping-modul.component.html',
  styleUrls: ['./mapping-modul.component.scss'],
  providers: [ConfirmationService]
})
export class MappingModulComponent implements OnInit {
  item: any = {}
  columnModulApp: any[]
  dsModulApp: any[]
  selectedFile:any
  d_modulAplikasiHead: any[]
  columnSubSistem: any[]
  dsSubSistem: any[]
  d_jenis: any[] = [{ name: 'Menu' }, { name: 'Modul' }]
  treeSourceMenu: TreeNode[] = []
  isLoadingNav: boolean = false
  isSimpan: boolean = false
  idModul: any
  checked2: boolean = true
  pop_Tambah: boolean = false
  pop: any = {}
  isModul: boolean
  selectedSub: any
  selectedMod: any
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
    this.loadColumn()
    this.loadModul()
  }
  loadColumn() {
    this.columnSubSistem = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'modulaplikasi', header: 'Sub Sistem', width: "200px" },
    ];
    this.columnModulApp = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'modulaplikasi', header: 'Modul Aplikasi', width: "200px" },
    ];
  }
  loadModul() {
    this.apiService.get('modul/get-modul-aplikasi?jenis=subsistem').subscribe(e => {
      for (let i = 0; i < e.length; i++) {
        const element = e[i];
        element.no = i + 1
      }
      this.dsSubSistem = e
      this.d_modulAplikasiHead = e
    })
  }
  onSelectSub(e) {
    this.apiService.get('modul/get-modul-aplikasi?jenis=modulaplikasi&id=' + e.data.id).subscribe(e => {
      for (let i = 0; i < e.length; i++) {
        const element = e[i];
        element.no = i + 1
      }
      this.dsModulApp = e
    })
  }
  onSelectModul(e) {
    this.treeSourceMenu = [];
    this.isLoadingNav = true
    this.item = {}
    this.idModul = e.data.id
    this.apiService.get('modul/get-modul-aplikasi?jenis=objekMenuRecursive&id=' + e.data.id).subscribe(e => {
      this.isLoadingNav = false
      this.treeSourceMenu = e
    })

  }

  nodeSelect(event) {
    let selected = event.node
    this.item.idMenu = selected.id;
    if (selected.parent_id != 0) {
      this.item.idHeadMenu = selected.parent_id;
    }
    this.item.nmMenu = selected.label;
    this.item.fungsi = selected.fungsi;
    this.item.keterangan = selected.keterangan;
    this.item.noUrut2 = selected.nourut;
    this.item.url = selected.alamaturlform;
  }
  cancel() {
    this.item = {}
  }
  save() {

    if (this.item.nmMenu == undefined) {
      this.alertService.warn('Info', "Nama Menu belum di isi!");
      return;
    }
    if (this.idModul == undefined) {
      this.alertService.warn('Info', "Pilih Modul Aplikasi!");
      return;
    }
    // if (this.item.url == undefined) {
    //   this.alertService.warn('Info', "Url belum di isi!");
    //   return;
    // }

    this.isSimpan = true
    var objSave = {
      "id": this.item.idMenu == undefined ? 0 : this.item.idMenu,
      "fungsi": this.item.fungsi,
      "keterangan": this.item.keterangan,
      "objekmodulaplikasi": this.item.nmMenu,
      "nourut": this.item.noUrut2,
      "alamaturlform": this.item.url != undefined ? this.item.url : null,
      "kdobjekmodulaplikasihead": this.item.idHeadMenu != undefined ? this.item.idHeadMenu : null,
      "modulaplikasiid": this.idModul
    };
    this.apiService.post("modul/simpan-objek-modul-aplikasi", objSave).subscribe(e => {
      this.onSelectModul({ data: { id: this.idModul } })
      this.isSimpan = false
      this.expandAll()
      this.cancel();
    })
  }
  hapus() {
    if (this.item.idMenu == undefined) {
      this.alertService.warn('Info', " Menu belum di pilih!");
      return;
    }

    var objSave = {
      "id": this.item.idMenu
    };
    this.apiService.post("modul/hapus-objek-modul-aplikasi", objSave).subscribe(e => {
      this.onSelectModul({ data: { id: this.idModul } })
    })
  }
  expandAll() {
    this.treeSourceMenu.forEach(node => {
      this.expandRecursive(node, true);
    });
  }

  collapseAll() {
    this.treeSourceMenu.forEach(node => {
      this.expandRecursive(node, false);
    });
  }
  handleChange(e) {
    var isChecked = e.checked;
    if (isChecked == false) {
      this.expandAll()
    } else {
      this.collapseAll()
    }
  }
  private expandRecursive(node: TreeNode, isExpand: boolean) {
    node.expanded = isExpand;
    if (node.children) {
      node.children.forEach(childNode => {
        this.expandRecursive(childNode, isExpand);
      });
    }
  }
  tambah(jenis) {
    if (jenis == 'Sub Sistem') {
      this.isModul = false
    } else {
      this.isModul = true
    }
    this.pop.judul = jenis
    this.pop_Tambah = true
  }
  simpanModul() {
    if (this.pop.modulaplikasi == undefined) {
      this.alertService.warn('Info', "Nama Modul/Sub Sistem harus di isi")
      return
    }
    if (this.isModul == true) {
      if (this.pop.subSistemHead == undefined) {
        this.alertService.warn('Info', "Sub Sistem harus di pilih")
        return
      }

    }
    let json = {
      'id': this.pop.id != undefined ? this.pop.id : '',
      'modulaplikasi': this.pop.modulaplikasi,
      'nourut': this.pop.noUrut != undefined ? this.pop.noUrut : null,
      'reportdisplay': this.isModul == true ? 'Menu' : 'Modul',
      'kdmodulaplikasihead': this.pop.subSistemHead != undefined ? this.pop.subSistemHead.id : null
    }
    this.apiService.post('modul/save-modul-aplikasi', json).subscribe(e => {
      if (this.isModul == true) {
        this.onSelectSub({ data: { id: this.pop.subSistemHead.id } })
      } else {
        this.loadModul()
      }

    })
  }
  hapusSub() {
    if (this.selectedSub == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    let json = {
      'id': this.selectedSub.id,

    }
    this.apiService.post('modul/hapus-modul-aplikasi', json).subscribe(e => {
      this.loadModul()
    })
  }
  hapusModul() {
    if (this.selectedMod == undefined) {
      this.alertService.warn('Info', 'Pilih data dulu')
      return
    }
    let json = {
      'id': this.selectedMod.id,

    }
    this.apiService.post('modul/hapus-modul-aplikasi', json).subscribe(e => {
      if (this.selectedSub != undefined) {
        this.onSelectSub({ data: { id: this.selectedSub.id } })
      }else{
        this.loadModul()
      }

    })
  }
}
