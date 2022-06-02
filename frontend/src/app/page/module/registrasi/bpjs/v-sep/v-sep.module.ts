import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VSepRoutingModule } from './v-sep-routing.module';
import { VSepComponent } from './v-sep.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [VSepComponent],
  imports: [
    CommonModule,
    VSepRoutingModule,
    primeNgModule
  ]
})
export class VSepModule { }
