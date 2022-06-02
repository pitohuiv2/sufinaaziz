import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarOrderResepComponent } from './daftar-order-resep.component';

const routes: Routes = [  { path: '', component: DaftarOrderResepComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarOrderResepRoutingModule { }
