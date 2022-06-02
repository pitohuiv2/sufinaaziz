import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AntrolDashboardComponent } from './antrol-dashboard.component';

const routes: Routes = [{ path: '', component: AntrolDashboardComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AntrolDashboardRoutingModule { }
