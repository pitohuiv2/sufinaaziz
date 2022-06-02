import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPasienDirawatRoutingModule } from './daftar-pasien-dirawat-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPasienDirawatComponent} from './daftar-pasien-dirawat.component';

@NgModule({
  declarations: [DaftarPasienDirawatComponent],
  imports: [
    CommonModule,
    DaftarPasienDirawatRoutingModule,
    primeNgModule
  ]
})
export class DaftarPasienDirawatModule { }
