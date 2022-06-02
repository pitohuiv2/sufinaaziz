import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InformasiTarifLayananComponent } from './informasi-tarif-layanan.component';
const routes: Routes = [ { path: '', component: InformasiTarifLayananComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InformasiTarifLayananRoutingModule { }
