import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPasienPulangComponent } from './daftar-pasien-pulang.component';

const routes: Routes = [  { path: '', component: DaftarPasienPulangComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienPulangRoutingModule { }
