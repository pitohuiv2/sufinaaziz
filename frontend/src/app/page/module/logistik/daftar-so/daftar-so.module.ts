import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarSoRoutingModule } from './daftar-so-routing.module';
import { DaftarSoComponent } from './daftar-so.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarSoComponent
  ],
  imports: [
    CommonModule,
    DaftarSoRoutingModule,
    primeNgModule
  ]
})
export class DaftarSoModule { }
