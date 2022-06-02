import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LaporanSensusPasienComponent } from './laporan-sensus-pasien.component';
const routes: Routes = [
  { path: '', component: LaporanSensusPasienComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LaporanSensusPasienRoutingModule { }
