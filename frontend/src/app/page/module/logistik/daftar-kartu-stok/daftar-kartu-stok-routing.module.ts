import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarKartuStokComponent } from './daftar-kartu-stok.component';

const routes: Routes = [
  { path: '', component: DaftarKartuStokComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarKartuStokRoutingModule { }
