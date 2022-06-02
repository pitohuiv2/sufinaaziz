import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputAlkesRuanganRoutingModule } from './input-alkes-ruangan-routing.module';
import { InputAlkesRuanganComponent } from './input-alkes-ruangan.component';
import { primeNgModule } from 'src/app/shared/shared.module';



@NgModule({
  declarations: [
    InputAlkesRuanganComponent
  ],
  imports: [
    CommonModule,
    InputAlkesRuanganRoutingModule,
    primeNgModule
  ]
})
export class InputAlkesRuanganModule { }
