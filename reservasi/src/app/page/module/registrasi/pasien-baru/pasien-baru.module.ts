import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PasienBaruRoutingModule } from './pasien-baru-routing.module';
import { PasienBaruComponent } from './pasien-baru.component';
import { primeNgModule } from '../../../../shared/shared.module';

@NgModule({
  declarations: [PasienBaruComponent],
  imports: [
    CommonModule,
    PasienBaruRoutingModule,
    primeNgModule,
  ]
})
export class PasienBaruModule { }
