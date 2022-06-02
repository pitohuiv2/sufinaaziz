import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { BayarTagihanPasienComponent } from './bayar-tagihan-pasien.component';

const routes: Routes = [  { path: '', component: BayarTagihanPasienComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BayarTagihanPasienRoutingModule { }
