import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MapAdministrasiOtomatisComponent } from './map-administrasi-otomatis.component';
import { MapAdministrasiOtomatisRoutingModule } from './map-administrasi-otomatis-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [MapAdministrasiOtomatisComponent],
  imports: [
    CommonModule,
    MapAdministrasiOtomatisRoutingModule,
    primeNgModule
  ]
})
export class MapAdministrasiOtomatisModule { }
