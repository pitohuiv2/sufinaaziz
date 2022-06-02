import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputKirimBarangComponent } from './input-kirim-barang.component';

const routes: Routes = [
  { path: '', component: InputKirimBarangComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputKirimBarangRoutingModule { }
