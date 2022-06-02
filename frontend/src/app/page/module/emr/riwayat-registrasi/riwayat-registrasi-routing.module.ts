import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RiwayatRegistrasiComponent } from './riwayat-registrasi.component';

const routes: Routes = [{
  path: '', component: RiwayatRegistrasiComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RiwayatRegistrasiRoutingModule { }
