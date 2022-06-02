import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarRegistrasiPasienComponent } from './daftar-registrasi-pasien.component';

const routes: Routes = [  { path: '', component: DaftarRegistrasiPasienComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarRegistrasiPasienRoutingModule { }
