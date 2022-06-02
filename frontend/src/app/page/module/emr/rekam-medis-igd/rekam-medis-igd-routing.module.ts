import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RekamMedisIgdComponent } from './rekam-medis-igd.component';

const routes: Routes = [
  {
    path: '', component: RekamMedisIgdComponent,
    children: [

      { path: 'emr-detail-igd', loadChildren: () => import('../emr-detail-igd/emr-detail-igd.module').then(m => m.EmrDetailIgdModule) },

    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RekamMedisIgdRoutingModule { }
