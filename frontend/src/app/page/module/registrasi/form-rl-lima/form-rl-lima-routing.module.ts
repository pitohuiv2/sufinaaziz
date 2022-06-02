import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FormRlLimaComponent } from './form-rl-lima.component';

const routes: Routes = [{path: '',component:FormRlLimaComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FormRlLimaRoutingModule { }
