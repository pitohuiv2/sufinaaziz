import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SisruteRoutingModule } from './sisrute-routing.module';
import { SisruteComponent } from './sisrute.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    SisruteComponent,
  ],
  imports: [
    CommonModule,
    SisruteRoutingModule,
    primeNgModule,
  ]
})
export class SisruteModule { }
