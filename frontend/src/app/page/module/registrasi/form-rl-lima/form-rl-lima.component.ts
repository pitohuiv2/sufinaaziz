import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment'
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-form-rl-lima',
  templateUrl: './form-rl-lima.component.html',
  styleUrls: ['./form-rl-lima.component.scss']
})
export class FormRlLimaComponent implements OnInit {
  item: any = {
    tglAwal: new Date(),
    tglAkhir: new Date(),
  }
  dataSource: any[]
  indexTab = 0
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
  handleChangeTab(e) {
    this.indexTab = e.index
  }
  exportExcel() {
    this.helper.exportExcel(this.dataSource, 'RL 3.1')
  }
  cari() {
    
  }
}
