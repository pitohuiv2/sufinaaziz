import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { KonsultasiDokterRoutingModule } from './konsultasi-dokter-routing.module';
import { KonsultasiDokterComponent } from './konsultasi-dokter.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [KonsultasiDokterComponent],
  imports: [
    CommonModule,
    KonsultasiDokterRoutingModule,
    primeNgModule
  ]
})
export class KonsultasiDokterModule { }
