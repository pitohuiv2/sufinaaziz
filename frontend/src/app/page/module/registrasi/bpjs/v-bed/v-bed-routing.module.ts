import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VBedComponent } from './v-bed.component';

const routes: Routes = [{path: '',component:VBedComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VBedRoutingModule { }
