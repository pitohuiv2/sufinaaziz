import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardSdmComponent } from './dashboard-sdm.component';

const routes: Routes = [{
  path: '', component: DashboardSdmComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DashboardSdmRoutingModule { }
