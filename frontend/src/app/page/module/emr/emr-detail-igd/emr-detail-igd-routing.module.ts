import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { EmrDetailIgdComponent } from './emr-detail-igd.component';

const routes: Routes = [
  {
    path: '', component: EmrDetailIgdComponent,
    children: [
      { path: 'emr-detail-form-igd/:namaEMR/:nomorEMR', loadChildren: () => import('../emr-detail-form-igd/emr-detail-form-igd.module').then(m => m.EmrDetailFormIgdModule) }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EmrDetailIgdRoutingModule { }
