import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPenerimaanKasirRoutingModule } from './daftar-penerimaan-kasir-routing.module';
import { DaftarPenerimaanKasirComponent } from './daftar-penerimaan-kasir.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarPenerimaanKasirComponent
  ],
  imports: [
    CommonModule,
    DaftarPenerimaanKasirRoutingModule,
    primeNgModule
  ]
})
export class DaftarPenerimaanKasirModule { }
