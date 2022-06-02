import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RekamMedisIgdRoutingModule } from './rekam-medis-igd-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { RekamMedisIgdComponent} from './rekam-medis-igd.component';


@NgModule({
  declarations: [
    RekamMedisIgdComponent
  ],
  imports: [
    CommonModule,
    RekamMedisIgdRoutingModule,
    primeNgModule
  ],
  bootstrap: [RekamMedisIgdComponent]
})
export class RekamMedisIgdModule { }
