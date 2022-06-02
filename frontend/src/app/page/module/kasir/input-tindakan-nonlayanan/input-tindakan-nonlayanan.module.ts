import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputTindakanNonlayananRoutingModule } from './input-tindakan-nonlayanan-routing.module';
import { InputTindakanNonlayananComponent } from './input-tindakan-nonlayanan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    InputTindakanNonlayananComponent
  ],
  imports: [
    CommonModule,
    InputTindakanNonlayananRoutingModule,
    primeNgModule
  ]
})
export class InputTindakanNonlayananModule { }
