import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienTerdaftarRoutingModule } from './daftar-pasien-terdaftar-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPasienTerdaftarComponent } from './daftar-pasien-terdaftar.component';

@NgModule({
  declarations: [
    DaftarPasienTerdaftarComponent
  ],
  imports: [
    CommonModule,
    DaftarPasienTerdaftarRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienTerdaftarModule { }
