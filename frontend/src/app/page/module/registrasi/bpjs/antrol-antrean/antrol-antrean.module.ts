import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AntrolAntreanRoutingModule } from './antrol-antrean-routing.module';
import { AntrolAntreanComponent } from './antrol-antrean.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [AntrolAntreanComponent],
  imports: [
    CommonModule,
    AntrolAntreanRoutingModule,
    primeNgModule
  ]
})
export class AntrolAntreanModule { }
