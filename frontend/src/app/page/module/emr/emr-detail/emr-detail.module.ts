import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EmrDetailRoutingModule } from './emr-detail-routing.module';
import { EmrDetailComponent } from './emr-detail.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    EmrDetailComponent
  ],
  imports: [
    CommonModule,
    EmrDetailRoutingModule,
    primeNgModule
  ]
})
export class EmrDetailModule { }
