import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputOrderBarangRoutingModule } from './input-order-barang-routing.module';
import { InputOrderBarangComponent } from './input-order-barang.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    InputOrderBarangComponent
  ],
  imports: [
    CommonModule,
    InputOrderBarangRoutingModule,
    primeNgModule
  ]
})
export class InputOrderBarangModule { }
