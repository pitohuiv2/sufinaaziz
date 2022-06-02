import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPasienPenunjangComponent } from './daftar-pasien-penunjang.component';

const routes: Routes = [  { path: '', component: DaftarPasienPenunjangComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienPenunjangRoutingModule { }
