import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPenerimaanPembayaranComponent } from './daftar-penerimaan-pembayaran.component';

const routes: Routes = [  { path: '', component: DaftarPenerimaanPembayaranComponent }];


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPenerimaanPembayaranRoutingModule { }
