import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RekamMedisRoutingModule } from './rekam-medis-routing.module';
import { RekamMedisComponent } from './rekam-medis.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    RekamMedisComponent
  ],
  imports: [
    CommonModule,
    RekamMedisRoutingModule,
    primeNgModule
  ],
     
  bootstrap: [RekamMedisComponent]
})
export class RekamMedisModule { }
