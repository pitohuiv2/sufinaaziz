import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardKeuanganComponent } from './dashboard-keuangan.component';

const routes: Routes = [{
  path: '', component: DashboardKeuanganComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DashboardKeuanganRoutingModule { }
