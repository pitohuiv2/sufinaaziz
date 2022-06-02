import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DashboardManajemenRoutingModule } from './dashboard-manajemen-routing.module';
import { DashboardManajemenComponent } from './dashboard-manajemen.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [DashboardManajemenComponent],
  imports: [
    CommonModule,
    DashboardManajemenRoutingModule,
    primeNgModule
  ]
})
export class DashboardManajemenModule { }
