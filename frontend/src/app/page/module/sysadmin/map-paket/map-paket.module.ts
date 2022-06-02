import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MapPaketRoutingModule } from './map-paket-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { MapPaketComponent } from './map-paket.component';


@NgModule({
  declarations: [MapPaketComponent],
  imports: [
    CommonModule,
    MapPaketRoutingModule,
    primeNgModule
  ]
})
export class MapPaketModule { }
