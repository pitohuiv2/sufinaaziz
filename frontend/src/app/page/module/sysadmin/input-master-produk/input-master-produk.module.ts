import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputMasterProdukRoutingModule } from './input-master-produk-routing.module';
import { InputMasterProdukComponent } from "./input-master-produk.component";
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    InputMasterProdukComponent
  ],
  imports: [
    CommonModule,
    InputMasterProdukRoutingModule,
    primeNgModule
  ]
})
export class InputMasterProdukModule { }
