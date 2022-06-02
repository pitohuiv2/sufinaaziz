import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPenerimaanBarangSupplierRoutingModule } from './daftar-penerimaan-barang-supplier-routing.module';
import { DaftarPenerimaanBarangSupplierComponent } from './daftar-penerimaan-barang-supplier.component';
import { primeNgModule } from 'src/app/shared/shared.module';
@NgModule({
  declarations: [
    DaftarPenerimaanBarangSupplierComponent
  ],
  imports: [
    CommonModule,
    DaftarPenerimaanBarangSupplierRoutingModule,
    primeNgModule
  ]
})
export class DaftarPenerimaanBarangSupplierModule { }
