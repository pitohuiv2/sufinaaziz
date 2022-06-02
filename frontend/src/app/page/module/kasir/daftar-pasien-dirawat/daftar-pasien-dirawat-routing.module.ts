import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPasienDirawatComponent } from './daftar-pasien-dirawat.component';

const routes: Routes = [{ path: '', component: DaftarPasienDirawatComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienDirawatRoutingModule { }
