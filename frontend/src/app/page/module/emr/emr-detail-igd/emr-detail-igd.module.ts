import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EmrDetailIgdRoutingModule } from './emr-detail-igd-routing.module';
import { EmrDetailIgdComponent } from './emr-detail-igd.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    EmrDetailIgdComponent
  ],
  imports: [
    CommonModule,
    EmrDetailIgdRoutingModule,
    primeNgModule
  ]
})
export class EmrDetailIgdModule { }
