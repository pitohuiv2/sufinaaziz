import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarRegistrasiPasienRoutingModule } from './daftar-registrasi-pasien-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarRegistrasiPasienComponent } from './daftar-registrasi-pasien.component';


@NgModule({
  declarations: [DaftarRegistrasiPasienComponent],
  imports: [
    CommonModule,
    DaftarRegistrasiPasienRoutingModule,
    primeNgModule
    
  ],
})
export class DaftarRegistrasiPasienModule { }
