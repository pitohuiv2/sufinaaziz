import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PembayaranTagihanLayananRoutingModule } from './pembayaran-tagihan-layanan-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { PembayaranTagihanLayananComponent} from './pembayaran-tagihan-layanan.component';

@NgModule({
  declarations: [
    PembayaranTagihanLayananComponent
  ],
  imports: [
    CommonModule,
    PembayaranTagihanLayananRoutingModule,
    primeNgModule
  ]
})
export class PembayaranTagihanLayananModule { }
