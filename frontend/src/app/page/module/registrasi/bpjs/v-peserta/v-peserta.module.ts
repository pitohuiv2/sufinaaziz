import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VPesertaRoutingModule } from './v-peserta-routing.module';
import { VPesertaComponent } from './v-peserta.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [VPesertaComponent],
  imports: [
    CommonModule,
    VPesertaRoutingModule,
    primeNgModule
  ]
})
export class VPesertaModule { }
