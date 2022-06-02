import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { EmrDetailFormIgdComponent } from './emr-detail-form-igd.component';

const routes: Routes = [
  { path: '', component: EmrDetailFormIgdComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EmrDetailFormIgdRoutingModule { }
