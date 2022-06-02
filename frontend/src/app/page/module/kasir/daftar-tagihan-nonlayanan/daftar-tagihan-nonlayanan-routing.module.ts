import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarTagihanNonlayananComponent } from './daftar-tagihan-nonlayanan.component'

const routes: Routes = [
  { path: '', component: DaftarTagihanNonlayananComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarTagihanNonlayananRoutingModule { }
