import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SisruteComponent } from './sisrute.component';

const routes: Routes = [{ path:'',component:SisruteComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SisruteRoutingModule { }
