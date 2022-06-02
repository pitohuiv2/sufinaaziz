import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FormRlDuaComponent } from './form-rl-dua.component';

const routes: Routes = [{ path:'',component:FormRlDuaComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FormRlDuaRoutingModule { }
