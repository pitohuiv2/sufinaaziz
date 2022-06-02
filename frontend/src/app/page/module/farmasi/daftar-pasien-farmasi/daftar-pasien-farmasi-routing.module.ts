import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {DaftarPasienFarmasiComponent} from './daftar-pasien-farmasi.component'

const routes: Routes = [{ path: '', component: DaftarPasienFarmasiComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPasienFarmasiRoutingModule { }
