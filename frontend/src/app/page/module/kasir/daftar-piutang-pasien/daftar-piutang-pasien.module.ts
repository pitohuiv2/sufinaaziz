import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPiutangPasienRoutingModule } from './daftar-piutang-pasien-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPiutangPasienComponent} from './daftar-piutang-pasien.component';


@NgModule({
  declarations: [
    DaftarPiutangPasienComponent
  ],
  imports: [
    CommonModule,
    DaftarPiutangPasienRoutingModule,
    primeNgModule
  ]
})
export class DaftarPiutangPasienModule { }
