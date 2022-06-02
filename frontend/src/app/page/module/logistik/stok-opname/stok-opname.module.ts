import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { StokOpnameRoutingModule } from './stok-opname-routing.module';
import { StokOpnameComponent } from './stok-opname.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    StokOpnameComponent
  ],
  imports: [
    CommonModule,
    StokOpnameRoutingModule,
    primeNgModule
  ]
})
export class StokOpnameModule { }
