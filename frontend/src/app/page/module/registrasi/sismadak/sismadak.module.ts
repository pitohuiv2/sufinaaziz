import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SismadakRoutingModule } from './sismadak-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { SismadakComponent } from './sismadak.component';


@NgModule({
  declarations: [
    SismadakComponent
  ],
  imports: [
    CommonModule,
    SismadakRoutingModule,
    primeNgModule
  ]
})
export class SismadakModule { }
