import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VLpkRoutingModule } from './v-lpk-routing.module';
import { VLpkComponent } from './v-lpk.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [VLpkComponent],
  imports: [
    CommonModule,
    VLpkRoutingModule,
    primeNgModule
  ]
})
export class VLpkModule { }
