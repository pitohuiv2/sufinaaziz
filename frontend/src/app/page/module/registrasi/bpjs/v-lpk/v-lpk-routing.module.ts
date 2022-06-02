import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VLpkComponent } from './v-lpk.component';

const routes: Routes = [{path:'',component:VLpkComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VLpkRoutingModule { }
