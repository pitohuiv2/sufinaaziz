import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputResepApotikRoutingModule } from './input-resep-apotik-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { InputResepApotikComponent } from './input-resep-apotik.component'

@NgModule({
  declarations: [
    InputResepApotikComponent
  ],
  imports: [
    CommonModule,
    InputResepApotikRoutingModule,
    primeNgModule
  ]
})
export class InputResepApotikModule { }
