import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VerifikasiTagihanRoutingModule } from './verifikasi-tagihan-routing.module';
import { VerifikasiTagihanComponent } from './verifikasi-tagihan.component';
import { primeNgModule } from 'src/app/shared/shared.module';
// import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';



@NgModule({
  declarations: [
    // HeadPasienComponent,
    VerifikasiTagihanComponent
  ],
  imports: [
    CommonModule,
    VerifikasiTagihanRoutingModule,
    primeNgModule
  ],
  providers:[]
})
export class VerifikasiTagihanModule { }
