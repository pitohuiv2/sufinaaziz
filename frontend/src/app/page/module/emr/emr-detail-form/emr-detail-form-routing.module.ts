import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { EmrDetailFormComponent } from './emr-detail-form.component';

const routes: Routes = [
  { path: '', component: EmrDetailFormComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EmrDetailFormRoutingModule { }
