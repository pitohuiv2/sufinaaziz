import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPasienDepositLebihComponent } from './daftar-pasien-deposit-lebih.component';
const routes: Routes = [
  { path: '', component: DaftarPasienDepositLebihComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienDepositLebihRoutingModule { }
