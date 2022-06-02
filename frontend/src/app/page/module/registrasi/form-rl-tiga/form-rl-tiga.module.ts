import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormRlTigaRoutingModule } from './form-rl-tiga-routing.module';
import { FormRlTigaComponent } from './form-rl-tiga.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    FormRlTigaComponent
  ],
  imports: [
    CommonModule,
    FormRlTigaRoutingModule,
    primeNgModule
  ]
})
export class FormRlTigaModule { }
