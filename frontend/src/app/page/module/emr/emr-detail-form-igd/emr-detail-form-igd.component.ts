import { RekamMedisIgdComponent } from '../rekam-medis-igd/rekam-medis-igd.component';
import { AfterViewInit, Component, Input, OnInit, ViewChild } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService, TreeNode } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { EmrDetailIgdComponent } from '../emr-detail-igd/emr-detail-igd.component';

@Component({
  selector: 'app-emr-detail-form-igd',
  templateUrl: './emr-detail-form-igd.component.html',
  styleUrls: ['./emr-detail-form-igd.component.scss']
})
export class EmrDetailFormIgdComponent implements OnInit {
  cc: any = {}
  nomorEMR: any = '-'
  namaEMR: any
  listData: any = {}
  item: any = {
    obj: [],
    obj2: [],
  }
  jenisEMR = 'igd'
  isSimpan: boolean
  @Input() ngSwitchCase: any
  constructor(
    public rekamMedis: RekamMedisIgdComponent,
    public orderBedah: EmrDetailIgdComponent,
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private activeRoute: ActivatedRoute,
    private router: Router,
  ) { }

  ngAfterViewInit(): void {

  }

  ngOnInit(): void {
    this.activeRoute.params.subscribe(params => {
      this.namaEMR = params['namaEMR']
      this.nomorEMR = params['nomorEMR']

      if (this.rekamMedis.header.noregistrasi == undefined) {
        var cache = this.cacheHelper.get('cacheEMR_qwertyuiop')
        if (cache != undefined) {
          cache = JSON.parse(cache)
          this.setHead(cache)
        } else {
          window.history.back()
          // this.router.navigate(['rekam-medis', this.rekamMedis.header.norec_pd, this.rekamMedis.header.norec])
        }
      } else {
        this.setHead(this.rekamMedis.header)
      }
      this.loadEMR()
    })

  }
  setHead(data) {
    this.cc.nocm = data.nocm
    this.cc.namapasien = data.namapasien
    this.cc.jeniskelamin = data.jeniskelamin
    this.cc.noregistrasi = data.noregistrasi
    this.cc.umur = data.umur
    this.cc.kelompokpasien = data.kelompokpasien
    this.cc.tglregistrasi = data.tglregistrasi
    this.cc.norec = data.norec
    this.cc.norec_pd = data.norec_pd
    this.cc.objectkelasfk = data.objectkelasfk
    this.cc.namakelas = data.namakelas
    this.cc.objectruanganfk = data.objectruanganfk
    this.cc.namaruangan = data.namaruangan
    this.cc.DataNoregis = false
    if (this.nomorEMR == '-') {
      this.cc.norec_emr = '-'
    } else {
      this.cc.norec_emr = this.nomorEMR
    }
  }
  loadEMR() {
    // debugger
    if (this.nomorEMR == '-') {
      this.apiService.get("emr/get-rekam-medis-dynamic?emrid=" + this.namaEMR).subscribe(e => {
        this.listData = e
        this.item.title = e.title
        this.item.classgrid = e.classgrid

        this.cc.emrfk = this.namaEMR
        this.item.objcbo = []
        this.item.obj = []
        this.item.obj2 = []
      })
    } else {
      var chekedd = false
      this.apiService.get("emr/get-rekam-medis-dynamic?emrid=" + this.namaEMR).subscribe(e => {
        this.listData = e
        this.item.title = e.title
        this.item.classgrid = e.classgrid

        this.cc.emrfk = this.namaEMR

        this.item.objcbo = []

        this.apiService.get("emr/get-emr-transaksi-detail?noemr=" + this.nomorEMR + "&emrfk=" + this.cc.emrfk,).subscribe(dat => {
          this.item.obj = []
          this.item.obj2 = []
          let dataLoad = dat.data
          for (var i = 0; i <= dataLoad.length - 1; i++) {
            if (parseFloat(this.cc.emrfk) == dataLoad[i].emrfk) {
              if (dataLoad[i].type == "textbox") {
                this.item.obj[dataLoad[i].emrdfk] = dataLoad[i].value
              }
              if (dataLoad[i].type == "checkbox") {
                chekedd = false
                if (dataLoad[i].value == '1') {
                  chekedd = true
                }
                this.item.obj[dataLoad[i].emrdfk] = chekedd
              }
              if (dataLoad[i].type == "radio") {
                this.item.obj[dataLoad[i].emrdfk] = dataLoad[i].value
              }
              if (dataLoad[i].type == "datetime") {
                this.item.obj[dataLoad[i].emrdfk] = new Date(dataLoad[i].value)
              }
              if (dataLoad[i].type == "time") {
                var momenst = moment(new Date()).format('YYYY-MM-DD')
                this.item.obj[dataLoad[i].emrdfk] = new Date(momenst + ' ' +dataLoad[i].value)
              }
              if (dataLoad[i].type == "date") {
              
                this.item.obj[dataLoad[i].emrdfk] = new Date(dataLoad[i].value)
              }

              if (dataLoad[i].type == "checkboxtextbox") {
                this.item.obj[dataLoad[i].emrdfk] = dataLoad[i].value
                this.item.obj2[dataLoad[i].emrdfk] = true
              }
              if (dataLoad[i].type == "textarea") {
                this.item.obj[dataLoad[i].emrdfk] = dataLoad[i].value
              }
              if (dataLoad[i].type == "combobox") {
                var str = dataLoad[i].value
                var res = str.split("~");
                this.item.obj[dataLoad[i].emrdfk] = { value: res[0], text: res[1] }

              }
              // pegawaiInputDetail = dataLoad[i].pegawaifk
            }

          }
          // setTimeout(function(){medifirstService.setDisableAllInputElement()  }, 2000);

        })

      });
    }
  }
  save() {   
    var arrobj = Object.keys(this.item.obj)
    var arrSave = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      if (this.item.obj[parseInt(arrobj[i])] instanceof Date)
        this.item.obj[parseInt(arrobj[i])] = moment(this.item.obj[parseInt(arrobj[i])]).format('YYYY-MM-DD HH:mm')

      if (this.item.obj[parseInt(arrobj[i])] == 'Invalid date') {
        arrobj.splice(i, 1)
      }
      arrSave.push({ id: arrobj[i], values: this.item.obj[parseInt(arrobj[i])] })
    }
    this.cc.jenisemr = this.jenisEMR
    var jsonSave = {
      head: this.cc,
      data: arrSave
    }
    this.isSimpan = true
    this.apiService.post('emr/save-emr-dinamis', jsonSave).subscribe(e => {
      this.isSimpan = false
      this.nomorEMR = e.data.noemr
      this.cc.norec_emr = e.data.noemr
      this.apiService.postLog('EMR', 'norec emrpasien_t', e.data.norec,
        this.item.title + ' dengan No EMR - ' + e.data.noemr + ' pada No Registrasi '
        + this.cc.noregistrasi).subscribe(res => { })

    }, error => {
      this.isSimpan = false
    });
  }
  back() {
    window.history.back()
  }
  batal() {
    this.item.obj = []
    this.item.obj2 = []
  }
  onSelectTime($event, e) {
    let hour = new Date($event).getHours();
    let min = new Date($event).getMinutes();
    if (min < 10) {
      this.item.obj[e] = `${hour}:0${min}`;
    } else {
      this.item.obj[e] = `${hour}:${min}`;
    }
  }
  filterAutoComplete(event, obj) {
    this.apiService.get(obj.cbotable + '?filter%5Bfilters%5D%5B0%5D%5Bvalue%5D=' + event.query).subscribe(data => {
      this.item.objcbo[obj.id] = data
    })

  }

}
