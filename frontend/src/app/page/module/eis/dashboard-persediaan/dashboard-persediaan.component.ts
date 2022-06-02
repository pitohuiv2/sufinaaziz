import { Component, OnInit } from '@angular/core';

import { SelectItem } from 'primeng/api';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { ProductService } from 'src/app/demo/service/productservice';
import { EventService } from 'src/app/demo/service/eventservice';
import { Product } from 'src/app/demo/domain/product';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import * as moment from 'moment';
@Component({
  selector: 'app-dashboard-persediaan',
  templateUrl: './dashboard-persediaan.component.html',
  styleUrls: ['./dashboard-persediaan.component.scss']
})
export class DashboardPersediaanComponent implements OnInit {
  namaLengkap: ''
  constructor(
    private apiService: ApiService,
    private productService: ProductService,
    private eventService: EventService,
    private auth: AuthService,
    private alert: AlertService
  ) {
    this.namaLengkap = this.auth.getDataLoginUser().pegawai.namaLengkap
  }

  ngOnInit(): void {
  }

}
