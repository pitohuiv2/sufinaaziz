import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPenerimaanKasirComponent } from './daftar-penerimaan-kasir.component';

const routes: Routes = [
  { path: '', component: DaftarPenerimaanKasirComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPenerimaanKasirRoutingModule { }
