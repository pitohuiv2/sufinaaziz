import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienRiRoutingModule } from './daftar-pasien-ri-routing.module';
import { DaftarPasienRiComponent } from './daftar-pasien-ri.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarPasienRiComponent
  ],
  imports: [
    CommonModule,
    DaftarPasienRiRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienRiModule { }
