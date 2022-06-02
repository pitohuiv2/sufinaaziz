import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PasienLamaRoutingModule } from './pasien-lama-routing.module';
import { PasienLamaComponent } from './pasien-lama.component';
import { primeNgModule } from '../../../../shared/shared.module';

@NgModule({
  declarations: [PasienLamaComponent],
  imports: [
    CommonModule,
    PasienLamaRoutingModule,
    primeNgModule,
  ]
})
export class PasienLamaModule { }
