import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPasienRjComponent } from './daftar-pasien-rj.component';

const routes: Routes = [{
  path: '', component: DaftarPasienRjComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienRjRoutingModule { }
