import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienFarmasiRoutingModule } from './daftar-pasien-farmasi-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPasienFarmasiComponent } from './daftar-pasien-farmasi.component';


@NgModule({
  declarations: [
    DaftarPasienFarmasiComponent
  ],
  imports: [
    CommonModule,
    DaftarPasienFarmasiRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienFarmasiModule { }
