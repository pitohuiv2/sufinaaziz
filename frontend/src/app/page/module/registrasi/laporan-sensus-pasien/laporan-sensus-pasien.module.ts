import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { LaporanSensusPasienRoutingModule } from './laporan-sensus-pasien-routing.module';
import { LaporanSensusPasienComponent } from './laporan-sensus-pasien.component';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    LaporanSensusPasienComponent
  ],
  imports: [
    CommonModule,
    LaporanSensusPasienRoutingModule,
    primeNgModule
  ]
})
export class LaporanSensusPasienModule { }
