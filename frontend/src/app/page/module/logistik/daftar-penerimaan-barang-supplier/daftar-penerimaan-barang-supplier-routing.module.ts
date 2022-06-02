import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPenerimaanBarangSupplierComponent } from './daftar-penerimaan-barang-supplier.component';

const routes: Routes = [
  { path: '', component: DaftarPenerimaanBarangSupplierComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPenerimaanBarangSupplierRoutingModule { }
