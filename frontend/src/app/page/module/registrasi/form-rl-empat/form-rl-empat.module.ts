import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormRlEmpatRoutingModule } from './form-rl-empat-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { FormRlEmpatComponent } from './form-rl-empat.component';


@NgModule({
  declarations: [
    FormRlEmpatComponent
  ],
  imports: [
    CommonModule,
    FormRlEmpatRoutingModule,
    primeNgModule
  ]
})
export class FormRlEmpatModule { }
