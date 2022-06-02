import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EmrDetailFormIgdRoutingModule } from './emr-detail-form-igd-routing.module';
import { EmrDetailFormIgdComponent } from './emr-detail-form-igd.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    EmrDetailFormIgdComponent
  ],
  imports: [
    CommonModule,
    EmrDetailFormIgdRoutingModule,
    primeNgModule
  ]
})
export class EmrDetailFormIgdModule { }
