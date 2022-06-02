import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-sismadak',
  templateUrl: './sismadak.component.html',
  styleUrls: ['./sismadak.component.scss']
})
export class SismadakComponent implements OnInit {
  dataSource: any = []
  dataSource2: any = []
  listDep:any=[]
  listIndikator:any=[]
  listNum: any = [{ id: 'N', name: 'Numerator' }, { id: 'D', name: 'Denumerator' }]
  item: any = {
    tglAwal: new Date(),
    tglAkhir: new Date(),
    tglLap: new Date()
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
  exportExcel() {

  }
  cari() {

  }
  cancel(){

  }
  save(){
    
  }
}
