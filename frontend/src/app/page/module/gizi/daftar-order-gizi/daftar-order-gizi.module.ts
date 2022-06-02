import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarOrderGiziRoutingModule } from './daftar-order-gizi-routing.module';
import { DaftarOrderGiziComponent } from './daftar-order-gizi.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarOrderGiziComponent
  ],
  imports: [
    CommonModule,
    DaftarOrderGiziRoutingModule,
    primeNgModule
  ]
})
export class DaftarOrderGiziModule { }
