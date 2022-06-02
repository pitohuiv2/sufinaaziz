import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FormRlSatuComponent } from './form-rl-satu.component';

const routes: Routes = [{
  path: '', component: FormRlSatuComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FormRlSatuRoutingModule { }
