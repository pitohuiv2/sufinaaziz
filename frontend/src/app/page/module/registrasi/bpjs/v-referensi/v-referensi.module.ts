import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VReferensiRoutingModule } from './v-referensi-routing.module';
import { VReferensiComponent } from './v-referensi.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [VReferensiComponent],
  imports: [
    CommonModule,
    VReferensiRoutingModule,
    primeNgModule
  ]
})
export class VReferensiModule { }
