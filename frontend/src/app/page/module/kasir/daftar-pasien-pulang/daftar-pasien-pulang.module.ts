import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienPulangRoutingModule } from './daftar-pasien-pulang-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPasienPulangComponent} from './daftar-pasien-pulang.component';


@NgModule({
  declarations: [DaftarPasienPulangComponent],
  imports: [
    CommonModule,
    DaftarPasienPulangRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienPulangModule { }
