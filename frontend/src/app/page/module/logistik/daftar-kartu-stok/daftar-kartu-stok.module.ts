import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarKartuStokRoutingModule } from './daftar-kartu-stok-routing.module';
import { DaftarKartuStokComponent } from './daftar-kartu-stok.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarKartuStokComponent
  ],
  imports: [
    CommonModule,
    DaftarKartuStokRoutingModule,
    primeNgModule
  ]
})
export class DaftarKartuStokModule { }
