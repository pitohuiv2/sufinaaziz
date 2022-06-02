import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RincianPenunjangComponent } from './rincian-penunjang.component';

const routes: Routes = [  { path: '', component: RincianPenunjangComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RincianPenunjangRoutingModule { }
