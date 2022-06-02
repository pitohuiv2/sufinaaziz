import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarSetoranKasirRoutingModule } from './daftar-setoran-kasir-routing.module';
import { DaftarSetoranKasirComponent } from "./daftar-setoran-kasir.component";
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarSetoranKasirComponent
  ],
  imports: [
    CommonModule,
    DaftarSetoranKasirRoutingModule,
    primeNgModule
  ]
})
export class DaftarSetoranKasirModule { }
