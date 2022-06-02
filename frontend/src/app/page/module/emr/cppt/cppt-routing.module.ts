import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CpptComponent } from './cppt.component';

const routes: Routes = [
  {path:'',component:CpptComponent}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CpptRoutingModule { }
