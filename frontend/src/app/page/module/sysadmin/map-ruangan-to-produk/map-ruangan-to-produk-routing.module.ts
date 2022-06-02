import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MapRuanganToProdukComponent } from './map-ruangan-to-produk.component';

const routes: Routes = [{ path: '', component: MapRuanganToProdukComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MapRuanganToProdukRoutingModule { }
