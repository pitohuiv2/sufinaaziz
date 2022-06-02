import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarTagihanPiutangPasienComponent } from './daftar-tagihan-piutang-pasien.component';

const routes: Routes = [  { path: '', component: DaftarTagihanPiutangPasienComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarTagihanPiutangPasienRoutingModule { }
