import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FormRlEmpatComponent } from './form-rl-empat.component';

const routes: Routes = [{ path:'',component:FormRlEmpatComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FormRlEmpatRoutingModule { }
