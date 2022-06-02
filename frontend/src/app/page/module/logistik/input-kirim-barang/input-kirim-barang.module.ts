import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputKirimBarangRoutingModule } from './input-kirim-barang-routing.module';
import { InputKirimBarangComponent } from './input-kirim-barang.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    InputKirimBarangComponent
  ],
  imports: [
    CommonModule,
    InputKirimBarangRoutingModule,
    primeNgModule
  ]
})
export class InputKirimBarangModule { }
