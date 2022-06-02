import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CpptRoutingModule } from './cppt-routing.module';
import { CpptComponent } from './cppt.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    CpptComponent
  ],
  imports: [
    CommonModule,
    CpptRoutingModule,
    primeNgModule
  ]
})
export class CpptModule { }
