import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { primeNgModule } from 'src/app/shared/shared.module';
import { LaporanKunjunganRoutingModule } from './laporan-kunjungan-routing.module';
import { LaporanKunjunganComponent } from './laporan-kunjungan.component';

@NgModule({
  declarations: [
    LaporanKunjunganComponent
  ],
  imports: [
    CommonModule,
    LaporanKunjunganRoutingModule,
    primeNgModule
  ]
})
export class LaporanKunjunganModule { }
