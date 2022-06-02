import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarResepNonlayananRoutingModule } from './daftar-resep-nonlayanan-routing.module';
import { DaftarResepNonlayananComponent } from './daftar-resep-nonlayanan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarResepNonlayananComponent
  ],
  imports: [
    CommonModule,
    DaftarResepNonlayananRoutingModule,
    primeNgModule
  ]
})
export class DaftarResepNonlayananModule { }
