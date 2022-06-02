import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-setting-data',
  templateUrl: './setting-data.component.html',
  styleUrls: ['./setting-data.component.scss'],
  providers: [ConfirmationService]
})
export class SettingDataComponent implements OnInit {

  item: any = {
    mapModul: [],
    mapRuangan: [],
    mapModul2: []
  }
  isList: boolean = false
  column: any[]
  dataSource: any[]
  pop_User: boolean = false
  d_Profile: any[]
  d_KelompokUser: any[]
  d_Pegawai: any[]
  d_LoginUser: any[]
  isSimpan: boolean = false
  indexTab = 0
  searchText = '';
  searchText2 = '';
  listModul: any[] = [];
  listRuangan: any[] = []
  ruanganDef: any[] = [];
  modulDef: any[] = [];
  listitems: MenuItem[];
  listData: any = {}
  selectedGrid: any
  add: any = {}
  listTable: any = []
  listIdField: any = []
  listType = [{ id: 'combobox', name: 'List / Combobox' }, { id: 'textbox', name: 'String' },
  { id: 'datetime', name: 'Datetime' }, { id: 'time', name: 'Time' }, { id: 'date', name: 'Date' },
  { id: 'textbox', name: 'Varchar' }, { id: 'textbox', name: 'Integer' }, { id: 'textbox', name: 'Float' }]
  listFieldName: any[] = []
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

    // this.apiService.get("sysadmin/settingdatafixed/get-table").then(function (data) {
    //   this.listTable = data
    // })
    // this.loadColumn()
    // this.loadCombo()
    this.loadData()

  }
  filterTable(event) {
    let query = event.query;
    this.apiService.get("sysadmin/settingdatafixed/get-table?name=" + query
    ).subscribe(re => {
      this.listTable = re;
    })
  }
  nodeSelect(event) {

    this.apiService.get("sysadmin/settingdatafixed/get-setting-detail?kelompok=" + event.node.label).subscribe(res => {
      let e = {
        data: res
      }
      this.listData = e.data
      this.item.objcbo = []
      // for (var i = e.data.kolom1.length - 1; i >= 0; i--) {
      //   this.apiService.get( 'sysadmin/settingdatafixed/get-setting-combo?id='+e.data.kolom1[i].id).subscribe(data=> {
      //     if(data.length>0){
      //       this.item.objcbo[e.data.kolom1[i].id] = data
      //     }

      //   })
      // }


      for (let z = 0; z < e.data.kolom1.length; z++) {
        const element = e.data.kolom1[z];
        this.item.statusenabled = false
        if (element.statusenabled == true) {
          this.item.statusenabled = true
          break
        }
      }
      this.item.obj = []
      this.item.obj2 = []
      var dataLoad = e.data.kolom1
      let chekedd = false
      for (var i = 0; i <= dataLoad.length - 1; i++) {

        if (dataLoad[i].type == "textbox") {
          this.item.obj[dataLoad[i].id] = dataLoad[i].value
        }
        if (dataLoad[i].type == "checkbox") {
          chekedd = false
          if (dataLoad[i].value == '1') {
            chekedd = true
          }
          this.item.obj[dataLoad[i].id] = chekedd
        }

        if (dataLoad[i].type == "datetime") {
          this.item.obj[dataLoad[i].id] = new Date(dataLoad[i].value)
        }
        if (dataLoad[i].type == "time") {
          this.item.obj[dataLoad[i].id] = new Date(dataLoad[i].value)
        }
        if (dataLoad[i].type == "date") {
          this.item.obj[dataLoad[i].id] = new Date(dataLoad[i].value)
        }

        if (dataLoad[i].type == "checkboxtextbox") {
          this.item.obj[dataLoad[i].id] = dataLoad[i].value
          this.item.obj2[dataLoad[i].id] = true
        }
        if (dataLoad[i].type == "textarea") {
          this.item.obj[dataLoad[i].id] = dataLoad[i].value
        }
        if (dataLoad[i].type == "combobox") {
          this.item.obj[dataLoad[i].id] = { value: dataLoad[i].value, text: dataLoad[i].text }

        }

      }

    })
  }
  filterAutoComplete(event, obj) {
    this.apiService.get('sysadmin/settingdatafixed/get-setting-combo?id=' + obj.id + '&filter%5Bfilters%5D%5B0%5D%5Bvalue%5D=' + event.query).subscribe(data => {
      this.item.objcbo[obj.id] = data
    })

  }
  loadData() {
    this.listitems = []
    this.apiService.get('sysadmin/settingdatafixed/get-kelompok-setting').subscribe(e => {
      for (let x = 0; x < e.data.length; x++) {
        const element = e.data[x];
        this.listitems.push({ label: element.kelompok, icon: 'pi pi-fw pi-chevron-circle-right' })
      }

    })
  }
  saveSetting() {
    var arrobj = Object.keys(this.item.obj)
    var arrSave = []
    for (var i = arrobj.length - 1; i >= 0; i--) {
      arrSave.push({ id: arrobj[i], values: this.item.obj[parseInt(arrobj[i])] })
    }
    var jsonSave = {
      data: arrSave,
      head: true
    }
    this.apiService.post('sysadmin/settingdatafixed/update-setting', jsonSave).subscribe(e => {

    });
  }
  setTextCombo(e) {
    if (e.id == 'combobox') {
      this.isList = true
      delete this.add.nilaiField
      delete this.add.reportDisplay
    }
    else {
      this.isList = false
      delete this.add.nilaiField
      delete this.add.reportDisplay
    }

  }
  getField(e) {
    this.apiService.get("sysadmin/settingdatafixed/get-field-table?tablename=" + e.table_name
    ).subscribe(e => {
      this.listFieldName = e.data
    })
  }
  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {
      this.loadData()
    } else {
      this.loadData()
    }

  }
  setSourceNilaiFiled(e) {
    this.listIdField = []
    if (this.add.tabelRelasi && this.add.fieldKeyTable) {
      this.apiService.get("sysadmin/settingdatafixed/get-data-from-table?table_name=" + this.add.tabelRelasi.table_name
        + "&column_name=" + this.add.fieldKeyTable.column_name
      ).subscribe(e => {
        this.listIdField = e.data
      })
    }
  }
  simpanNew() {
    if (this.add.namaField == undefined) {
      this.alertService.error("Nama Field harus di isi!","info")
      return
    }
    if (this.add.typeField == undefined) {
      this.alertService.error("Type Field harus di isi!","info")
      return
    }
    if (this.add.nilaiField == undefined) {
      this.alertService.error("Nilai Field harus di isi!","info")
      return
    }
    if (this.add.keteranganFungsi == undefined) {
      this.alertService.error("Keterangan Fungsi harus di isi!","info")
      return
    }

    var id = "";
    if (this.add.id != undefined) {
      id = this.add.id
    }
    var tabelRelasi = null;
    if (this.add.tabelRelasi != undefined)
      tabelRelasi = this.add.tabelRelasi.table_name

    var fieldKeyTable = null
    if (this.add.fieldKeyTable != undefined)
      fieldKeyTable = this.add.fieldKeyTable.COLUMN_NAME

    var fieldReportDisplay = null
    if (this.add.fieldReportDisplay != undefined)
      fieldReportDisplay = this.add.fieldReportDisplay.COLUMN_NAME

    var reportDisplay = null
    if (this.add.reportDisplay != undefined && this.isList == true)
      reportDisplay = this.add.reportDisplay.name
    else if (this.add.reportDisplay != undefined && this.isList == false)
      reportDisplay = this.add.reportDisplay

    var nilaiField = null
    if (this.isList == true)
      nilaiField = this.add.nilaiField.name
    else if (this.isList == false)
      nilaiField = this.add.nilaiField

    var data = {
      "iddatafixed": id,
      "namafield": this.add.namaField,
      "nilai": nilaiField,
      "tabelrelasi": tabelRelasi,
      "kodeexternal": null,
      "namaexternal": null,
      "reportdisplay": reportDisplay,
      "fieldkeytabelrelasi": fieldKeyTable,
      "fieldreportdisplaytabelrelasi": fieldReportDisplay,
      "keteranganfungsi": this.add.keteranganFungsi,
      "typefield": this.add.typeField.id,
      "kelompok": this.add.kelompok != undefined ? this.add.kelompok : null,
    }

    var objSave =
    {
      datafixed: data
    }

    this.apiService.post('sysadmin/settingdatafixed/post-settingdatafixe', objSave).subscribe(e => {
      this.loadData()
      this.add = {};

    });
  }
  batal() {
    this.add = {};
    this.loadData()
  }
 
}