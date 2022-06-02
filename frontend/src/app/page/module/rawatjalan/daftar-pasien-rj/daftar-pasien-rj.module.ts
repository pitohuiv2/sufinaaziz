import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienRjRoutingModule } from './daftar-pasien-rj-routing.module';
import { DaftarPasienRjComponent } from './daftar-pasien-rj.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarPasienRjComponent
  ],
  imports: [
    CommonModule,
    DaftarPasienRjRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienRjModule { }
