import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { TransaksiPelayananApotikRoutingModule } from './transaksi-pelayanan-apotik-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { TransaksiPelayananApotikComponent } from './transaksi-pelayanan-apotik.component';


@NgModule({
  declarations: [
    TransaksiPelayananApotikComponent
  ],
  imports: [
    CommonModule,
    TransaksiPelayananApotikRoutingModule,
    primeNgModule
  ]
})
export class TransaksiPelayananApotikModule { }
