import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LaporanPenerimaanKasirComponent } from './laporan-penerimaan-kasir.component';
import { LaporanPenerimaanKasirRoutingModule } from './laporan-penerimaan-kasir-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    LaporanPenerimaanKasirComponent
  ],
  imports: [
    CommonModule,
    LaporanPenerimaanKasirRoutingModule,
    primeNgModule
  ]
})
export class LaporanPenerimaanKasirModule { }
