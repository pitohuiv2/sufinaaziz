import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarReturResepRoutingModule } from './daftar-retur-resep-routing.module';
import { DaftarReturResepComponent } from './daftar-retur-resep.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarReturResepComponent
  ],
  imports: [
    CommonModule,
    DaftarReturResepRoutingModule,
    primeNgModule
  ]
})
export class DaftarReturResepModule { }
