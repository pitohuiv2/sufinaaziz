import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VPrbComponent } from './v-prb.component';

const routes: Routes = [{path:'',component: VPrbComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VPrbRoutingModule { }
