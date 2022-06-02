import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DashboardPelayananRoutingModule } from './dashboard-pelayanan-routing.module';
import { DashboardPelayananComponent } from './dashboard-pelayanan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DashboardPelayananComponent
  ],
  imports: [
    CommonModule,
    DashboardPelayananRoutingModule,
    primeNgModule
  ]
})
export class DashboardPelayananModule { }
