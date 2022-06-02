import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LaporanSensusIgdComponent } from './laporan-sensus-igd.component';
const routes: Routes = [{ path: '', component: LaporanSensusIgdComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LaporanSensusIgdRoutingModule { }
