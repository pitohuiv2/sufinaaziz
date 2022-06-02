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

@Component({
  selector: 'app-hasil-laboratorium',
  templateUrl: './hasil-laboratorium.component.html',
  styleUrls: ['./hasil-laboratorium.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class HasilLaboratoriumComponent implements OnInit {
  params: any = {};
  currentNorecPD: any;
  currentNorecAPD: any;
  isClosing: boolean = false;
  item: any = { pasien: {} };
  column: any[];
  isSimpan: boolean = false;
  selected: any;
  dataTable: any[];
  isSelected: boolean
  rowGroupMetadata: any
  popupEntry: boolean = false;
  @ViewChild(HeadPasienComponent, { static: false }) h: HeadPasienComponent;
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

  ngAfterViewInit(): void {

    this.route.params.subscribe(params => {
      this.currentNorecPD = params['norec_rp'];
      this.currentNorecAPD = params['norec_dpr'];
      this.loadHead()
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
        const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        const firstDate: any = new Date(this.item.pasien.tgllahir);
        const secondDate: any = new Date(this.item.pasien.tglregistrasi);

        this.item.umurDay = Math.round(Math.abs((firstDate - secondDate) / oneDay));
        this.cari()
        // this.apiService.get("sysadmin/general/get-status-close/" + this.item.pasien.noregistrasi).subscribe(rese => {
        //   if (rese.status == true) {
        //     this.alertService.warn('Peringatan!', 'Pemeriksaan sudah ditutup tanggal ' + moment(new Date(rese.tglclosing)).format('DD-MMM-YYYY HH:mm'))
        //     this.isClosing = true
        //   }
        // })
      })
  }

  ngOnInit(): void {
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'namaproduk', header: 'Nama Pemeriksaan', width: "180px" },
      { field: 'detailpemeriksaan', header: 'Detail Pemeriksaan', width: "180px" },
      { field: 'hasil', header: 'Hasil Pemeriksaan', width: "180px" },
      { field: 'nilaitext', header: 'Nilai Normal', width: "180px" },
      { field: 'satuanstandar', header: 'Satuan Hasil', width: "180px" },
      { field: 'Metode', header: 'Metode', width: "140px" },
    ];
  }
  cari() {
    this.apiService.get("penunjang/get-hasil-lab-manual?norec_apd=" + this.currentNorecAPD +
      '&objectjeniskelaminfk=' + this.item.pasien.objectjeniskelaminfk + '&umur=' + this.item.umurDay).subscribe(res => {
        for (let i = 0; i < res.data.length; i++) {
          const element = res.data[i];
          element.no = i + 1

          if (element.hasil != null && element.nilaimax != null && element.nilaimin != null) {
            if (parseFloat(element.hasil) <= parseFloat(element.nilaimax)
              && parseFloat(element.hasil) >= parseFloat(element.nilaimin)) {
              element.flag = 'N'
            }
            if (parseFloat(element.hasil) > parseFloat(element.nilaimax)
              || parseFloat(element.hasil) < parseFloat(element.nilaimin)) {
              element.flag = 'Y'
            }
            if (isNaN(parseFloat(element.nilaimin)) || isNaN(parseFloat(element.nilaimax))) {
              if (element.nilaimin == element.hasil || element.nilaimax == element.hasil) {
                element.flag = 'N'
              } else {
                element.flag = 'Y'
              }
            }
          } else if (element.hasil != null && element.nilaimax == null && element.nilaimin == null) {
            if (element.hasil == element.nilaitext) {
              element.flag = 'N'
            } else {
              element.flag = 'Y'
            }
          } else {
            element.flag = 'Y'
          }


        }
        this.dataTable = res.data
        this.updateRowGroupMetaData()
      })
  }
  exportExcel() {
    this.dateHelper.exportExcel(this.dataTable, 'Hasil Laboratorium')
  }
  onSort() {
    this.updateRowGroupMetaData();
  }

  updateRowGroupMetaData() {
    this.rowGroupMetadata = {};

    if (this.dataTable) {
      for (let i = 0; i < this.dataTable.length; i++) {
        let rowData = this.dataTable[i];
        let representativeName = rowData.detailjenisproduk;

        if (i == 0) {
          this.rowGroupMetadata[representativeName] = { index: 0, size: 1 };
        }
        else {
          let previousRowData = this.dataTable[i - 1];
          let previousRowGroup = previousRowData.detailjenisproduk;
          if (representativeName === previousRowGroup)
            this.rowGroupMetadata[representativeName].size++;
          else
            this.rowGroupMetadata[representativeName] = { index: i, size: 1 };
        }
      }
    }
  }
  calculateCustomerTotal(name) {
    let total = 0;

    if (this.dataTable) {
      for (let customer of this.dataTable) {
        if (customer.detailjenisproduk === name) {
          total++;
        }
      }
    }

    return total;
  }

  onChange(element, items) {
    if (items != null && element.nilaimax != null && element.nilaimin != null) {
      if (parseFloat(items) <= parseFloat(element.nilaimax)
        && parseFloat(items) >= parseFloat(element.nilaimin)) {
        element.flag = 'N'
      }
      if (parseFloat(items) > parseFloat(element.nilaimax)
        || parseFloat(items) < parseFloat(element.nilaimin)) {
        element.flag = 'Y'
      }
      if (isNaN(parseFloat(element.nilaimin)) || isNaN(parseFloat(element.nilaimax))) {
        if (element.nilaimin == items || element.nilaimax == items) {
          element.flag = 'N'
        } else {
          element.flag = 'Y'
        }
      }
    } else if (items != null && element.nilaimax == null && element.nilaimin == null) {
      if (items == element.nilaitext) {
        element.flag = 'N'
      } else {
        element.flag = 'Y'
      }
    } else {
      element.flag = 'Y'
    }

    // items.flag = element.flag
  }

  cancel() {
    window.history.back()
  }
  onRowSelect(e) {
    this.item.min = e.nilaimin
    this.item.max = e.nilaimax
    this.item.objectprodukfk = e.produkidfk
    this.item.namaproduk = e.namaproduk
    this.item.detailpemeriksaan = e.detailpemeriksaan
    this.item.mpid = e.iddetailproduk
    this.isSelected = !this.isSelected
    this.selected = e
  }
  saveNilai() {
    // this.isSelected = false
    if (this.item.objectprodukfk == undefined) {
      return
    }
    this.popupEntry = true;

    this.apiService.get("penunjang/get-for-update-nilainormal?produkfk=" + this.item.objectprodukfk +
      '&mpid=' + this.item.mpid).subscribe(data => {

        for (var i = data.length - 1; i >= 0; i--) {
          if (data[i].jkid == 1) {
            this.item.nilaiminL = data[i].nilaimin
            this.item.nilaimaxL = data[i].nilaimax
            this.item.nilaiTextL = data[i].nilaitext
          } else if (data[i].jkid == 2) {
            this.item.nilaiminP = data[i].nilaimin
            this.item.nilaimaxP = data[i].nilaimax
            this.item.nilaiTextP = data[i].nilaitext
          }
        }
      })
  }
  simpanUpdateNilaiNormal() {

    var objSave = {
      "l": {
        id: this.item.mpid,
        nilaimax: this.item.nilaimaxL,
        nilaimin: this.item.nilaiminL,
        nilaitext: this.item.nilaiTextL,

      },
      "p": {
        id: this.item.mpid,
        nilaimax: this.item.nilaimaxP,
        nilaimin: this.item.nilaiminP,
        nilaitext: this.item.nilaiTextP,
      }
    }
    this.apiService.post('penunjang/save-update-nilainormal', objSave).subscribe(e => {
      this.popupEntry = false
      this.isSelected = false;
      if (this.item.pasien.objectjeniskelaminfk == 1) {
        this.selected.nilaimin = this.item.nilaiminL
        this.selected.nilaimax = this.item.nilaimaxL
        this.selected.nilaitext = this.item.nilaiTextL

        this.item.nilaimin = this.item.nilaiminL
        this.item.nilaimax = this.item.nilaimaxL
      }
      if (this.item.pasien.objectjeniskelaminfk == 2) {
        this.selected.nilaimin = this.item.nilaiminP
        this.selected.nilaimax = this.item.nilaimaxP
        this.selected.nilaitext = this.item.nilaiTextP

        this.item.nilaimin = this.item.nilaiminP
        this.item.nilaimax = this.item.nilaimaxP
      }
      let HasilInput = this.selected.hasil
      if (HasilInput != "") {
        if (HasilInput.indexOf("+/") >= 0) {
          let hssl = "positif"
          let nilaistring = this.selected.nilaitext
          nilaistring = nilaistring.toUpperCase();
          hssl = hssl.toUpperCase();
          if (hssl == nilaistring) {
            this.selected.flag = "N"
          } else {
            this.selected.flag = "Y"
          }
        } else if (HasilInput.indexOf("-/") >= 0) {
          let hssl = "negatif"
          let nilaistring = this.selected.nilaitext
          nilaistring = nilaistring.toUpperCase();
          hssl = hssl.toUpperCase();
          if (hssl == nilaistring) {
            this.selected.flag = "N"
          } else {
            this.selected.flag = "Y"
          }
        } else if (HasilInput.indexOf("<") >= 0) {
          let a = parseFloat(this.selected.nilaimin)
          let b = parseFloat(this.selected.nilaimax)
          let hsslARr = HasilInput.split("<");
          let hssl = parseFloat(hsslARr[1])

          if (hssl >= a && hssl <= b) {
            this.selected.flag = "N"
          } else {
            this.selected.flag = "Y"
          }
        } else if (HasilInput.indexOf(">") >= 0) {
          let a = parseFloat(this.selected.nilaimin)
          let b = parseFloat(this.selected.nilaimax)
          let hsslARr = HasilInput.split(">");
          let hssl = parseFloat(hsslARr[1])

          if (hssl >= a && hssl <= b) {
            this.selected.flag = "N"
          } else {
            this.selected.flag = "Y"
          }
        } else if (HasilInput.indexOf("-") >= 0) {
          let a = parseFloat(this.selected.nilaimin)
          let b = parseFloat(this.selected.nilaimax)
          let hsslARr = HasilInput.split("-");
          let hssl0 = parseFloat(hsslARr[0])
          let hssl = parseFloat(hsslARr[1])

          if (hssl0 >= a && hssl <= b) {
            this.selected.flag = "N"
          } else {
            this.selected.flag = "Y"
          }
        } else {
          if (this.selected.nilaimin != null) {
            let a = parseFloat(this.selected.nilaimin)
            let b = parseFloat(this.selected.nilaimax)
            let hssl = parseFloat(this.selected.hasil)

            if (hssl >= a && hssl <= b) {
              this.selected.flag = "N"
            } else {
              this.selected.flag = "Y"
            }
          } else {
            let hssl = this.selected.hasil
            let nilaistring = this.selected.nilaitext
            nilaistring = nilaistring.toUpperCase();
            hssl = hssl.toUpperCase();
            if (hssl == nilaistring) {
              this.selected.flag = "N"
            } else {
              this.selected.flag = "Y"
            }
          }
        }
      }
    })
  }

  save() {
    var dataArray = [];

    for (var i = this.dataTable.length - 1; i >= 0; i--) {
      if (this.dataTable[i].hasil != null) {
        dataArray.push({
          "produkfk": this.dataTable[i].produkidfk,
          "hasil": this.dataTable[i].hasil,
          "noregistrasifk": this.currentNorecAPD,
          // "nilaimin": this.dataTable[i].nilaimin,
          // "nilaimax": this.dataTable[i].nilaimax,   
          // "satuan": this.dataTable[i].satuan, 
          "metode": this.dataTable[i].metode,
          "produkdetaillabfk": this.dataTable[i].iddetailproduk,
          "pelayananpasienfk": this.dataTable[i].norec_pp,

        });
      } else {

      }

    }

    if (dataArray.length !== 0) {
      var objSave = {
        "hasil": dataArray
      }
      this.isSimpan = true
      this.apiService.post('penunjang/save-hasil-lab-manual', objSave).subscribe(e => {
        this.cari();
        this.isSimpan = false
      }, error => {
        this.isSimpan = false
      })
    } else {

    }
  }

}
