import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-form-rl-dua',
  templateUrl: './form-rl-dua.component.html',
  styleUrls: ['./form-rl-dua.component.scss']
})
export class FormRlDuaComponent implements OnInit {

  item: any = {

  }
  listTahun: any = []
  dataSource: any[]
  dataSource2: any[]
  isSimpan: boolean = false
  indexTab = 0
  column: any = []
  column2: any = []
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private helper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,

  ) { }
  ngOnInit(): void {
    this.loadColumn()
   
  }
  handleChangeTab(e) {
    this.indexTab = e.index
  }
  loadColumn() {
   
  }
 
  cari2(){
   
  }

  exportExcel2() {
    this.helper.exportExcel(this.dataSource2, 'RL 2 Ketenagaan')
  }

}

