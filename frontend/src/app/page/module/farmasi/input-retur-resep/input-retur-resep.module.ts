import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputReturResepRoutingModule } from './input-retur-resep-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { InputReturResepComponent } from './input-retur-resep.component'


@NgModule({
  declarations: [
    InputReturResepComponent
  ],
  imports: [
    CommonModule,
    InputReturResepRoutingModule,
    primeNgModule
  ]
})
export class InputReturResepModule { }
