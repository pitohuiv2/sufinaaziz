import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AntrolDashboardRoutingModule } from './antrol-dashboard-routing.module';
import { AntrolDashboardComponent } from './antrol-dashboard.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [AntrolDashboardComponent],
  imports: [
    CommonModule,
    AntrolDashboardRoutingModule,
    primeNgModule
  ]
})
export class AntrolDashboardModule { }
