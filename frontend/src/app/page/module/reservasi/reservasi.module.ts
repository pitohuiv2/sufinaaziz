import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ReservasiRoutingModule } from './reservasi-routing.module';
import { primeNgModule } from '../../../shared/shared.module';
import { ReservasiComponent } from './reservasi.component';


@NgModule({
  declarations: [
    ReservasiComponent
  ],
  imports: [
    CommonModule,
    ReservasiRoutingModule,
    primeNgModule
  ]
})
export class ReservasiModule { }
