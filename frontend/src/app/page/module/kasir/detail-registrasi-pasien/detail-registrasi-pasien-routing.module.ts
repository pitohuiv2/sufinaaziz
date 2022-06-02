import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DetailRegistrasiPasienComponent } from './detail-registrasi-pasien.component';

const routes: Routes = [  { path: '', component: DetailRegistrasiPasienComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DetailRegistrasiPasienRoutingModule { }
