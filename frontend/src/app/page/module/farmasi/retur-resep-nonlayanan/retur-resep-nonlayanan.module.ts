import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ReturResepNonlayananRoutingModule } from './retur-resep-nonlayanan-routing.module';
import { ReturResepNonlayananComponent } from './retur-resep-nonlayanan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    ReturResepNonlayananComponent
  ],
  imports: [
    CommonModule,
    ReturResepNonlayananRoutingModule,
    primeNgModule
  ]
})
export class ReturResepNonlayananModule { }
