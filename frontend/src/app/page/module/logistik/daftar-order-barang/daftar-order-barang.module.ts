import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarOrderBarangRoutingModule } from './daftar-order-barang-routing.module';
import { DaftarOrderBarangComponent } from './daftar-order-barang.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarOrderBarangComponent
  ],
  imports: [
    CommonModule,
    DaftarOrderBarangRoutingModule,
    primeNgModule
  ]
})
export class DaftarOrderBarangModule { }
