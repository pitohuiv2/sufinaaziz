import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarStokRuanganRoutingModule } from './daftar-stok-ruangan-routing.module';
import { DaftarStokRuanganComponent } from './daftar-stok-ruangan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarStokRuanganComponent
  ],
  imports: [
    CommonModule,
    DaftarStokRuanganRoutingModule,
    primeNgModule
  ]
})
export class DaftarStokRuanganModule { }
