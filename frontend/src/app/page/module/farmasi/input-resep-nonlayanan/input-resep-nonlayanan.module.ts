import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputResepNonlayananRoutingModule } from './input-resep-nonlayanan-routing.module';
import { InputResepNonlayananComponent } from './input-resep-nonlayanan.component';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    InputResepNonlayananComponent
  ],
  imports: [
    CommonModule,
    InputResepNonlayananRoutingModule,
    primeNgModule
  ]
})
export class InputResepNonlayananModule { }
