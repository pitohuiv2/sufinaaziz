import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienBatalRoutingModule } from './daftar-pasien-batal-routing.module';
import { DaftarPasienBatalComponent } from './daftar-pasien-batal.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [DaftarPasienBatalComponent],
  imports: [
    CommonModule,
    DaftarPasienBatalRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienBatalModule { }
