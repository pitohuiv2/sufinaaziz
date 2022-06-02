import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VRencanaKontrolRoutingModule } from './v-rencana-kontrol-routing.module';
import { VRencanaKontrolComponent } from './v-rencana-kontrol.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [VRencanaKontrolComponent],
  imports: [
    CommonModule,
    VRencanaKontrolRoutingModule,
    primeNgModule
  ]
})
export class VRencanaKontrolModule { }
