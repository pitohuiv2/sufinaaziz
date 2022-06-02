import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarMutasiComponent } from './daftar-mutasi.component';

const routes: Routes = [{ path: '', component: DaftarMutasiComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarMutasiRoutingModule { }
