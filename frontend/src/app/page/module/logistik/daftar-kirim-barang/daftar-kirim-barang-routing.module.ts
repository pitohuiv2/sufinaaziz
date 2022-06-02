import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarKirimBarangComponent } from './daftar-kirim-barang.component';

const routes: Routes = [
  { path: '', component: DaftarKirimBarangComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarKirimBarangRoutingModule { }
