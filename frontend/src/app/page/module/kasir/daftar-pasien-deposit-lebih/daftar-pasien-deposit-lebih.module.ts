import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DaftarPasienDepositLebihComponent } from './daftar-pasien-deposit-lebih.component';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPasienDepositLebihRoutingModule } from './daftar-pasien-deposit-lebih-routing.module';


@NgModule({
  declarations: [
    DaftarPasienDepositLebihComponent
  ],
  imports: [
    CommonModule,
    DaftarPasienDepositLebihRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienDepositLebihModule { }
