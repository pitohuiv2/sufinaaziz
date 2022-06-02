import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PenerimaanBarangSupplierComponent } from './penerimaan-barang-supplier.component'

const routes: Routes = [
  { path: '', component: PenerimaanBarangSupplierComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PenerimaanBarangSupplierRoutingModule { }
