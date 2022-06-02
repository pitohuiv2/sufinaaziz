import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { DaftarPasienTerdaftarComponent } from './daftar-pasien-terdaftar.component';

const routes: Routes = [  { path: '', component: DaftarPasienTerdaftarComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienTerdaftarRoutingModule { }
