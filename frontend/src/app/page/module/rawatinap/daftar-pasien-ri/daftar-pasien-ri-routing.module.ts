import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPasienRiComponent } from './daftar-pasien-ri.component';

const routes: Routes = [
  {
    path: '', component: DaftarPasienRiComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienRiRoutingModule { }
