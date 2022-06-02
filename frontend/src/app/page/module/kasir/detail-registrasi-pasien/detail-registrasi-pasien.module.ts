import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DetailRegistrasiPasienRoutingModule } from './detail-registrasi-pasien-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DetailRegistrasiPasienComponent } from './detail-registrasi-pasien.component';


@NgModule({
  declarations: [ DetailRegistrasiPasienComponent],
  imports: [
    CommonModule,
    DetailRegistrasiPasienRoutingModule,
    primeNgModule
  ],
  providers:[]
})
export class DetailRegistrasiPasienModule { }
