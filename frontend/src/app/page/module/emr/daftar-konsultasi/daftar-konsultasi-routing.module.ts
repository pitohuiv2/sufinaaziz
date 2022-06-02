import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarKonsultasiComponent } from './daftar-konsultasi.component';

const routes: Routes = [
  { path: '', component: DaftarKonsultasiComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarKonsultasiRoutingModule { }
