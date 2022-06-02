import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PemakaianAsuransiRoutingModule } from './pemakaian-asuransi-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { PemakaianAsuransiComponent } from './pemakaian-asuransi.component';


@NgModule({
  declarations: [
    PemakaianAsuransiComponent
  ],
  imports: [
    CommonModule,
    PemakaianAsuransiRoutingModule,
    primeNgModule
  ]
})
export class PemakaianAsuransiModule { }
