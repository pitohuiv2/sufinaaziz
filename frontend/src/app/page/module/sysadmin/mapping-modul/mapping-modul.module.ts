import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MappingModulRoutingModule } from './mapping-modul-routing.module';
import { MappingModulComponent } from './mapping-modul.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    MappingModulComponent
  ],
  imports: [
    CommonModule,
    MappingModulRoutingModule,
    primeNgModule
  ]
})
export class MappingModulModule { }
