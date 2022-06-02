import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PembayaranTagihanLayananComponent } from './pembayaran-tagihan-layanan.component';

const routes: Routes = [  { path: '', component: PembayaranTagihanLayananComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PembayaranTagihanLayananRoutingModule { }
