import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputOrderBarangComponent } from './input-order-barang.component';
const routes: Routes = [
  { path: '', component: InputOrderBarangComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputOrderBarangRoutingModule { }
