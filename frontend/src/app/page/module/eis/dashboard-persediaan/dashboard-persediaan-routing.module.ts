import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardPersediaanComponent } from './dashboard-persediaan.component';

const routes: Routes = [
  { path: '',component:DashboardPersediaanComponent}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DashboardPersediaanRoutingModule { }
