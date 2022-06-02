import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienPenunjangRoutingModule } from './daftar-pasien-penunjang-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPasienPenunjangComponent} from './daftar-pasien-penunjang.component';

@NgModule({
  declarations: [
    DaftarPasienPenunjangComponent
  ],
  imports: [
    CommonModule,
    DaftarPasienPenunjangRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienPenunjangModule { }
