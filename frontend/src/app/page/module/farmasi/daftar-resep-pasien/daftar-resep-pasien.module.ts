import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarResepPasienRoutingModule } from './daftar-resep-pasien-routing.module';
import { DaftarResepPasienComponent } from './daftar-resep-pasien.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [DaftarResepPasienComponent],
  imports: [
    CommonModule,
    DaftarResepPasienRoutingModule,
    primeNgModule
  ]
})
export class DaftarResepPasienModule { }
