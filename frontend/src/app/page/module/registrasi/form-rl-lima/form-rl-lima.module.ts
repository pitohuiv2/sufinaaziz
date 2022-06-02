import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormRlLimaRoutingModule } from './form-rl-lima-routing.module';
import { FormRlLimaComponent } from './form-rl-lima.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [FormRlLimaComponent],
  imports: [
    CommonModule,
    FormRlLimaRoutingModule,
    primeNgModule
  ]
})
export class FormRlLimaModule { }
