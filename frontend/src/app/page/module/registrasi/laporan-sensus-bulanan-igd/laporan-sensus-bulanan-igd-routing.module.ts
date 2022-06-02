import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LaporanSensusBulananIgdComponent } from './laporan-sensus-bulanan-igd.component';

const routes: Routes = [
  { path: '', component: LaporanSensusBulananIgdComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LaporanSensusBulananIgdRoutingModule { }
