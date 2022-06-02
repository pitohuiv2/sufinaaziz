import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EmrDetailFormRoutingModule } from './emr-detail-form-routing.module';
import { EmrDetailFormComponent } from './emr-detail-form.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    EmrDetailFormComponent
  ],
  imports: [
    CommonModule,
    EmrDetailFormRoutingModule,
    primeNgModule
  ]
})
export class EmrDetailFormModule { }
