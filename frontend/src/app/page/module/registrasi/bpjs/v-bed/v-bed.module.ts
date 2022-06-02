import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VBedRoutingModule } from './v-bed-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { VBedComponent } from './v-bed.component';


@NgModule({
  declarations: [VBedComponent],
  imports: [
    CommonModule,
    VBedRoutingModule,
    primeNgModule,
  ]
})
export class VBedModule { }
