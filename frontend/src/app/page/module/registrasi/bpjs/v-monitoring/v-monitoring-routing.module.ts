import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VMonitoringComponent } from './v-monitoring.component';

const routes: Routes = [{path:'',component:VMonitoringComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VMonitoringRoutingModule { }
