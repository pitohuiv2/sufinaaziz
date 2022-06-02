import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarMutasiRoutingModule } from './daftar-mutasi-routing.module';
import { DaftarMutasiComponent } from './daftar-mutasi.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarMutasiComponent
  ],
  imports: [
    CommonModule,
    DaftarMutasiRoutingModule,
    primeNgModule
  ]
})
export class DaftarMutasiModule { }
