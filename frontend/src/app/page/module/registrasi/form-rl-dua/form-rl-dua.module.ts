import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormRlDuaRoutingModule } from './form-rl-dua-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { FormRlDuaComponent } from './form-rl-dua.component';


@NgModule({
  declarations: [FormRlDuaComponent],
  imports: [
    CommonModule,
    FormRlDuaRoutingModule,
    primeNgModule
  ]
})
export class FormRlDuaModule { }
