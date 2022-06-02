import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputKabKotaRoutingModule } from './input-kab-kota-routing.module';
import { InputKabKotaComponent } from './input-kab-kota.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
	InputKabKotaComponent
  ],
  imports: [
    CommonModule,
    InputKabKotaRoutingModule,
    primeNgModule
  ]
})
export class InputKabKotaModule { }
