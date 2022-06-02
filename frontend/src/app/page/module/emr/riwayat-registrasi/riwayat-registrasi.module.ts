import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RiwayatRegistrasiRoutingModule } from './riwayat-registrasi-routing.module';
import { RiwayatRegistrasiComponent } from './riwayat-registrasi.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    RiwayatRegistrasiComponent
  ],
  imports: [
    CommonModule,
    RiwayatRegistrasiRoutingModule,
    primeNgModule
  ]
})
export class RiwayatRegistrasiModule { }
