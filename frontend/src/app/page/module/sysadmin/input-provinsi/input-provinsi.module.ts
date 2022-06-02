import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputProvinsiRoutingModule } from './input-provinsi-routing.module';
import { InputProvinsiComponent } from './input-provinsi.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
  InputProvinsiComponent
  ],
  imports: [
    CommonModule,
    InputProvinsiRoutingModule,
    primeNgModule
  ]
})
export class InputProvinsiModule { }
