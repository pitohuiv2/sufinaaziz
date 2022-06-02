import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MapRuanganToProdukRoutingModule } from './map-ruangan-to-produk-routing.module';
import { MapRuanganToProdukComponent } from './map-ruangan-to-produk.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    MapRuanganToProdukComponent
  ],
  imports: [
    CommonModule,
    MapRuanganToProdukRoutingModule,
    primeNgModule
  ]
})
export class MapRuanganToProdukModule { }
