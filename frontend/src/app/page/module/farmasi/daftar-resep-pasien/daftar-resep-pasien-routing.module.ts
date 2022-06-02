import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarResepPasienComponent } from './daftar-resep-pasien.component'

const routes: Routes = [ { path: '', component: DaftarResepPasienComponent } ];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarResepPasienRoutingModule { }
