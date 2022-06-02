import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarOrderBarangComponent } from './daftar-order-barang.component';

const routes: Routes = [
  { path: '', component: DaftarOrderBarangComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarOrderBarangRoutingModule { }
