import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RencanaKontrolRoutingModule } from './rencana-kontrol-routing.module';
import { RencanaKontrolComponent } from './rencana-kontrol.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    RencanaKontrolComponent
  ],
  imports: [
    CommonModule,
    RencanaKontrolRoutingModule,
    primeNgModule
  ]
})
export class RencanaKontrolModule { }
