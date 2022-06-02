import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PenerimaanBarangSupplierRoutingModule } from './penerimaan-barang-supplier-routing.module';
import { PenerimaanBarangSupplierComponent } from './penerimaan-barang-supplier.component';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    PenerimaanBarangSupplierComponent
  ],
  imports: [
    CommonModule,
    PenerimaanBarangSupplierRoutingModule,
    primeNgModule  
  ]
})
export class PenerimaanBarangSupplierModule { }
