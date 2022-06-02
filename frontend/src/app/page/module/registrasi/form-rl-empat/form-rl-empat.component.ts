import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-form-rl-empat',
  templateUrl: './form-rl-empat.component.html',
  styleUrls: ['./form-rl-empat.component.scss']
})
export class FormRlEmpatComponent implements OnInit {
  dataSource:any=[]
  dataSource2:any=[]
  item:any={
    tglAwal: new Date(),
    tglAkhir: new Date(),
  }
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
  }
  exportExcel(){

  }
  cari(){

  }
}
