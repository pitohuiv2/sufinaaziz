import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LaporanSensusIgdComponent } from './laporan-sensus-igd.component';
import { LaporanSensusIgdRoutingModule } from './laporan-sensus-igd-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';



@NgModule({
  declarations: [
    LaporanSensusIgdComponent
  ],
  imports: [
    CommonModule,
    LaporanSensusIgdRoutingModule,
    primeNgModule
  ]
})
export class LaporanSensusIgdModule { }
