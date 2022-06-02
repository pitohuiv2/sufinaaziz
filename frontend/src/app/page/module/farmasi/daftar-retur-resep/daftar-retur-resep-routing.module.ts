import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarReturResepComponent } from './daftar-retur-resep.component';

const routes: Routes = [
  { path: '', component: DaftarReturResepComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarReturResepRoutingModule { }
