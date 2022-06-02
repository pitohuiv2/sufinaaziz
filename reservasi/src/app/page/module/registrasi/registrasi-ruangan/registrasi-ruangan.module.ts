import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RegistrasiRuanganRoutingModule } from './registrasi-ruangan-routing.module';
import { RegistrasiRuanganComponent } from './registrasi-ruangan.component';
import { primeNgModule } from 'src/app/shared/shared.module';
import { HeaderPasienComponent } from 'src/app/page/template/header-pasien/header-pasien.component';



@NgModule({
  declarations: [
    RegistrasiRuanganComponent,
    // HeaderPasienComponent
  ],
  imports: [
    CommonModule,
    RegistrasiRuanganRoutingModule,
    primeNgModule
  ]
})
export class RegistrasiRuanganModule { }
