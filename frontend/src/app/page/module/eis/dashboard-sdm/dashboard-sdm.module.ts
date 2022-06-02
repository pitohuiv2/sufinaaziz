import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DashboardSdmRoutingModule } from './dashboard-sdm-routing.module';
import { DashboardSdmComponent } from './dashboard-sdm.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DashboardSdmComponent
  ],
  imports: [
    CommonModule,
    DashboardSdmRoutingModule,
    primeNgModule
  ]
})
export class DashboardSdmModule { }
