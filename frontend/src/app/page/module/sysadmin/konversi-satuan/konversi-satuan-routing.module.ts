import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { KonversiSatuanComponent } from './konversi-satuan.component';
const routes: Routes = [
  { path: '', component: KonversiSatuanComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class KonversiSatuanRoutingModule { }
