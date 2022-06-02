import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardManajemenComponent } from './dashboard-manajemen.component';

const routes: Routes = [{
  path: '', component: DashboardManajemenComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DashboardManajemenRoutingModule { }
