import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { InformasiTarifLayananComponent } from './informasi-tarif-layanan.component';
import { InformasiTarifLayananRoutingModule } from './informasi-tarif-layanan-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    InformasiTarifLayananComponent
  ],
  imports: [
    CommonModule,
    InformasiTarifLayananRoutingModule,
    primeNgModule
  ]
})
export class InformasiTarifLayananModule { }
