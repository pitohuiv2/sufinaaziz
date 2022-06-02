import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarKirimBarangRoutingModule } from './daftar-kirim-barang-routing.module';
import { DaftarKirimBarangComponent } from './daftar-kirim-barang.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarKirimBarangComponent
  ],
  imports: [
    CommonModule,
    DaftarKirimBarangRoutingModule,
    primeNgModule
  ]
})
export class DaftarKirimBarangModule { }
