import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarResepNonlayananComponent } from './daftar-resep-nonlayanan.component'

const routes: Routes = [
  { path: '', component: DaftarResepNonlayananComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarResepNonlayananRoutingModule { }
