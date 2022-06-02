import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputDiagnosaComponent } from './input-diagnosa.component';

const routes: Routes = [  { path: '', component: InputDiagnosaComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputDiagnosaRoutingModule { }
