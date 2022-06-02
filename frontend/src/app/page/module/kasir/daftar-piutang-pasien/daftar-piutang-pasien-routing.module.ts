import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPiutangPasienComponent } from './daftar-piutang-pasien.component';

const routes: Routes = [  { path: '', component: DaftarPiutangPasienComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPiutangPasienRoutingModule { }
