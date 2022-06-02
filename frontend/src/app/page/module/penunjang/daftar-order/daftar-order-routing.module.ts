import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DaftarOrderComponent } from './daftar-order.component';

const routes: Routes = [  { path: '', component: DaftarOrderComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarOrderRoutingModule { }
