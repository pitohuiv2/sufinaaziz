import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LaporanSensusBulananIgdComponent } from './laporan-sensus-bulanan-igd.component';
import { LaporanSensusBulananIgdRoutingModule } from './laporan-sensus-bulanan-igd-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    LaporanSensusBulananIgdComponent
  ],
  imports: [
    CommonModule,
    LaporanSensusBulananIgdRoutingModule,
    primeNgModule
  ]
})
export class LaporanSensusBulananIgdModule { }
