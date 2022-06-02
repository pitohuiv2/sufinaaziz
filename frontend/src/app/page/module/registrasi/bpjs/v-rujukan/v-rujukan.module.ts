import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VRujukanRoutingModule } from './v-rujukan-routing.module';
import { VRujukanComponent } from './v-rujukan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [VRujukanComponent],
  imports: [
    CommonModule,
    VRujukanRoutingModule,
    primeNgModule
  ]
})
export class VRujukanModule { }
