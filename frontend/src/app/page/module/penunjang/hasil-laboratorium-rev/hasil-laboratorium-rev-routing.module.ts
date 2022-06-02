import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HasilLaboratoriumRevComponent } from './hasil-laboratorium-rev.component';

const routes: Routes = [{path: '',component:HasilLaboratoriumRevComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class HasilLaboratoriumRevRoutingModule { }
