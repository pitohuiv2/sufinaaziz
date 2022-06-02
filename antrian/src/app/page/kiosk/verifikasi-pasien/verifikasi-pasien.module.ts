import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { VerifikasiPasienRoutingModule } from './verifikasi-pasien-routing.module';
import { VerifikasiPasienComponent } from './verifikasi-pasien.component';
import { primeNgModule } from 'src/app/shared/shared.module';
import { MatKeyboardModule } from 'angular-onscreen-material-keyboard';
import { MatButtonModule } from '@angular/material/button';


@NgModule({
  declarations: [
    VerifikasiPasienComponent
  ],
  imports: [
    CommonModule,
    VerifikasiPasienRoutingModule,
    primeNgModule,
    MatButtonModule,
    MatKeyboardModule,
  ]
})
export class VerifikasiPasienModule { }
