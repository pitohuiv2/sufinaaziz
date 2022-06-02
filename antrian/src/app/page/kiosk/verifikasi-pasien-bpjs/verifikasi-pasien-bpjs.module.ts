import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VerifikasiPasienBpjsRoutingModule } from './verifikasi-pasien-bpjs-routing.module';

import { primeNgModule } from 'src/app/shared/shared.module';
import { VerifikasiPasienBpjsComponent } from './verifikasi-pasien-bpjs.component';
import { MatKeyboardModule } from 'angular-onscreen-material-keyboard';
import { MatButtonModule } from '@angular/material/button';

@NgModule({
  declarations: [
    VerifikasiPasienBpjsComponent
  ],
  imports: [
    CommonModule,
    VerifikasiPasienBpjsRoutingModule,
    primeNgModule,
    MatButtonModule,
    MatKeyboardModule,
  ]
})
export class VerifikasiPasienBpjsModule { }
