import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormRlSatuRoutingModule } from './form-rl-satu-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { FormRlSatuComponent } from './form-rl-satu.component';


@NgModule({
  declarations: [
    FormRlSatuComponent
  ],
  imports: [
    CommonModule,
    FormRlSatuRoutingModule,
    primeNgModule
  ]
})
export class FormRlSatuModule { }
