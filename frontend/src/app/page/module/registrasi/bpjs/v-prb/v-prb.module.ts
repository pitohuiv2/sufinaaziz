import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VPrbRoutingModule } from './v-prb-routing.module';
import { VPrbComponent } from './v-prb.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [VPrbComponent],
  imports: [
    CommonModule,
    VPrbRoutingModule,
    primeNgModule
  ]
})
export class VPrbModule { }
