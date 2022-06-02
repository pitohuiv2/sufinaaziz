import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { EmrDetailComponent } from './emr-detail.component';

const routes: Routes = [
  {
    path: '', component: EmrDetailComponent,
    children: [
      { path: 'emr-detail-form/:namaEMR/:nomorEMR', loadChildren: () => import('../emr-detail-form/emr-detail-form.module').then(m => m.EmrDetailFormModule) }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EmrDetailRoutingModule { }
