import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { TransaksiPelayananApotikComponent } from './transaksi-pelayanan-apotik.component'

const routes: Routes = [{ path: '', component: TransaksiPelayananApotikComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class TransaksiPelayananApotikRoutingModule { }
