import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MasterDiagnosaRoutingModule } from './master-diagnosa-routing.module';
import { MasterDiagnosaComponent } from './master-diagnosa.component';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    MasterDiagnosaComponent
  ],
  imports: [
    CommonModule,
    MasterDiagnosaRoutingModule,
    primeNgModule
  ]
})
export class MasterDiagnosaModule { }
