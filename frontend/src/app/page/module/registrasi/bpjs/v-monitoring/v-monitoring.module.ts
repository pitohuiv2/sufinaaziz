import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VMonitoringRoutingModule } from './v-monitoring-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { VMonitoringComponent } from './v-monitoring.component';


@NgModule({
  declarations: [VMonitoringComponent],
  imports: [
    CommonModule,
    VMonitoringRoutingModule,
    primeNgModule
  ]
})
export class VMonitoringModule { }
