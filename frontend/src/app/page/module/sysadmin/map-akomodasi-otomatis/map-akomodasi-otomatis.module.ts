import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MapAkomodasiOtomatisRoutingModule } from './map-akomodasi-otomatis-routing.module';
import { MapAkomodasiOtomatisComponent } from './map-akomodasi-otomatis.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    MapAkomodasiOtomatisComponent
  ],
  imports: [
    CommonModule,
    MapAkomodasiOtomatisRoutingModule,
    primeNgModule
  ]
})
export class MapAkomodasiOtomatisModule { }
