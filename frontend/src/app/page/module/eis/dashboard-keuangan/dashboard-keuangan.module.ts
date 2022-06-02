import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DashboardKeuanganRoutingModule } from './dashboard-keuangan-routing.module';
import { DashboardKeuanganComponent } from './dashboard-keuangan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DashboardKeuanganComponent
  ],
  imports: [
    CommonModule,
    DashboardKeuanganRoutingModule,
    primeNgModule
  ]
})
export class DashboardKeuanganModule { }
