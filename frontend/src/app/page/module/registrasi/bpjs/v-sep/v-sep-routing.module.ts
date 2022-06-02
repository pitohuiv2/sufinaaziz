import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VSepComponent } from './v-sep.component';

const routes: Routes = [{path:'',component:VSepComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VSepRoutingModule { }
