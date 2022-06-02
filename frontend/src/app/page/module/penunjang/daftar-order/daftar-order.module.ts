import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarOrderRoutingModule } from './daftar-order-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarOrderComponent} from './daftar-order.component';

@NgModule({
  declarations: [
    DaftarOrderComponent
  ],
  imports: [
    CommonModule,
    DaftarOrderRoutingModule,
    primeNgModule
  ]
})
export class DaftarOrderModule { }
