import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarStokRuanganComponent } from './daftar-stok-ruangan.component'

const routes: Routes = [ { path: '', component: DaftarStokRuanganComponent } ];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarStokRuanganRoutingModule { }
