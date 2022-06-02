import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarKonsultasiRoutingModule } from './daftar-konsultasi-routing.module';
import { DaftarKonsultasiComponent } from './daftar-konsultasi.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarKonsultasiComponent
  ],
  imports: [
    CommonModule,
    DaftarKonsultasiRoutingModule,
    primeNgModule
  ]
})
export class DaftarKonsultasiModule { }
