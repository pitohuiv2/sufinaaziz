import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DashboardPersediaanRoutingModule } from './dashboard-persediaan-routing.module';
import { DashboardPersediaanComponent } from './dashboard-persediaan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DashboardPersediaanComponent
  ],
  imports: [
    CommonModule,
    DashboardPersediaanRoutingModule,
    primeNgModule
  ]
})
export class DashboardPersediaanModule { }
