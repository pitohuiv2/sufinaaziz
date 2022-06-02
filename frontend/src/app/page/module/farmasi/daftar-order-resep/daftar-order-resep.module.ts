import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarOrderResepRoutingModule } from './daftar-order-resep-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarOrderResepComponent} from './daftar-order-resep.component';

@NgModule({
  declarations: [
    DaftarOrderResepComponent
  ],
  imports: [
    CommonModule,
    DaftarOrderResepRoutingModule,
    primeNgModule
  ]
})
export class DaftarOrderResepModule { }
