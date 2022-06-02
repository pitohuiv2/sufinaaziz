import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarTagihanNonlayananRoutingModule } from './daftar-tagihan-nonlayanan-routing.module';
import { DaftarTagihanNonlayananComponent } from './daftar-tagihan-nonlayanan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarTagihanNonlayananComponent
  ],
  imports: [
    CommonModule,
    DaftarTagihanNonlayananRoutingModule,
    primeNgModule
  ]
})
export class DaftarTagihanNonlayananModule { }
