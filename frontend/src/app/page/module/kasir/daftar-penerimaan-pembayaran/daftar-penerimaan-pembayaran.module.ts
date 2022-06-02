import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPenerimaanPembayaranRoutingModule } from './daftar-penerimaan-pembayaran-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarPenerimaanPembayaranComponent} from './daftar-penerimaan-pembayaran.component';


@NgModule({
  declarations: [DaftarPenerimaanPembayaranComponent],
  imports: [
    CommonModule,
    DaftarPenerimaanPembayaranRoutingModule,
    primeNgModule
  ]
})
export class DaftarPenerimaanPembayaranModule { }
