import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BayarTagihanPasienRoutingModule } from './bayar-tagihan-pasien-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { BayarTagihanPasienComponent} from './bayar-tagihan-pasien.component';

@NgModule({
  declarations: [BayarTagihanPasienComponent],
  imports: [
    CommonModule,
    BayarTagihanPasienRoutingModule,
    primeNgModule
  ]
})
export class BayarTagihanPasienModule { }
