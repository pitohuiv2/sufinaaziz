import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarTagihanPiutangPasienRoutingModule } from './daftar-tagihan-piutang-pasien-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarTagihanPiutangPasienComponent} from './daftar-tagihan-piutang-pasien.component';

@NgModule({
  declarations: [
    DaftarTagihanPiutangPasienComponent
  ],
  imports: [
    CommonModule,
    DaftarTagihanPiutangPasienRoutingModule,
    primeNgModule
  ]
})
export class DaftarTagihanPiutangPasienModule { }
