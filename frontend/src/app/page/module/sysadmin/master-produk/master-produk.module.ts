import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MasterProdukRoutingModule } from './master-produk-routing.module';
import { MasterProdukComponent } from './master-produk.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    MasterProdukComponent
  ],
  imports: [
    CommonModule,
    MasterProdukRoutingModule,
    primeNgModule
  ]
})
export class MasterProdukModule { }
