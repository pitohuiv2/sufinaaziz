import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ReturResepNonlayananComponent } from './retur-resep-nonlayanan.component'

const routes: Routes = [
  { path: '', component: ReturResepNonlayananComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ReturResepNonlayananRoutingModule { }
